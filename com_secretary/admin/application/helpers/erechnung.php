<?php

namespace Secretary\Helpers;

defined('_JEXEC') or die;

/**
 * Builds an EN16931 / XRechnung 3.0 compliant UBL 2.1 Invoice XML for a
 * com_secretary document, using plain DOMDocument (no external dependency).
 */
class ERechnung
{
    const CUSTOMIZATION_ID  = 'urn:cen.eu:en16931:2017#compliant#urn:xoev-de:kosit:standard:xrechnung_3.0';
    const PROFILE_ID        = 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0';
    const INVOICE_TYPE_CODE = '380';
    const TAX_EXEMPT_REASON = 'Gemäß § 19 UStG wird keine Umsatzsteuer berechnet.';

    const CBC = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';
    const CAC = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
    const UBL = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';

    /**
     * A document can only become an e-invoice once it has a number, a buyer and at least one line item.
     */
    public static function isAvailable($item)
    {
        return !empty($item->id) && !empty($item->nr) && !empty($item->subject[1])
            && !empty($item->items) && $item->items !== '[]';
    }

    /**
     * @param object $item document item as returned by SecretaryModelDocument::getItem()
     * @return string XML
     */
    public static function generate($item)
    {
        $company = \Secretary\Application::company();
        $currency = $item->currency ?: 'EUR';

        $office = null;
        
        if (!empty($item->office))
		{
            $office = \Secretary\Database::getQuery('locations', (int) $item->office);
        }

        $buyerCountry = 'DE';
        
        if (!empty($item->subjectid))
		{
            $subjectCountry = \Secretary\Database::getQuery('subjects', (int) $item->subjectid, 'id', 'country', 'loadResult');
            
            if (!empty($subjectCountry))
			{
                $buyerCountry = self::countryToCode($subjectCountry);
            }
        }

        $lineItems = json_decode($item->items ?? '[]', true) ?: array();

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $invoice = $dom->createElementNS(self::UBL, 'Invoice');
        $invoice->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cac', self::CAC);
        $invoice->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cbc', self::CBC);
        $dom->appendChild($invoice);

        self::addChild($dom, $invoice, 'cbc:UBLVersionID', '2.1');
        self::addChild($dom, $invoice, 'cbc:CustomizationID', self::CUSTOMIZATION_ID);
        self::addChild($dom, $invoice, 'cbc:ProfileID', self::PROFILE_ID);
        self::addChild($dom, $invoice, 'cbc:ID', $item->nr);
        self::addChild($dom, $invoice, 'cbc:IssueDate', self::formatDate($item->created));
        
        if (!empty($item->deadline))
		{
            self::addChild($dom, $invoice, 'cbc:DueDate', self::formatDate($item->deadline));
        }
        self::addChild($dom, $invoice, 'cbc:InvoiceTypeCode', self::INVOICE_TYPE_CODE);
        
        if (!empty($item->title))
		{
            self::addChild($dom, $invoice, 'cbc:Note', strip_tags($item->title));
        }
        self::addChild($dom, $invoice, 'cbc:DocumentCurrencyCode', $currency);
        self::addChild($dom, $invoice, 'cbc:BuyerReference', $item->nr);

        // Seller
        $sellerAddress = self::sellerAddress($office, $company);
        $sellerParty = self::buildParty(
            $dom,
            $company['title'] ?? '',
            $sellerAddress,
            $company['vatid'] ?? '',
            \Secretary\Joomla::getApplication()->getCfg('mailfrom')
        );
        $supplier = $dom->createElement('cac:AccountingSupplierParty');
        $supplier->appendChild($sellerParty);
        $invoice->appendChild($supplier);

        // Buyer
        $buyerAddress = array(
            'street'  => $item->subject[2] ?? '',
            'zip'     => $item->subject[3] ?? '',
            'city'    => $item->subject[4] ?? '',
            'country' => $buyerCountry,
        );
        $buyerParty = self::buildParty($dom, $item->subject[1] ?? '', $buyerAddress, '', $item->subject[6] ?? '');
        $customer = $dom->createElement('cac:AccountingCustomerParty');
        $customer->appendChild($buyerParty);
        $invoice->appendChild($customer);

        // Line items + tax groups
        $taxGroups = array();
        $lines = array();
        $lineExtensionTotal = 0.0;
        $position = 1;

        foreach ($lineItems as $lineItem)
		{
            $quantity = (float) ($lineItem['quantity'] ?? 0);
            $rate = isset($lineItem['taxRate']) ? (float) $lineItem['taxRate'] : 0.0;
            $totalField = (float) ($lineItem['total'] ?? 0);

            // 'total' is the gross amount when taxtype is inclusive, otherwise it is already net.
            $net = ((int) $item->taxtype === 1 && $rate > 0)
                ? round($totalField / (1 + ($rate / 100)), 2)
                : round($totalField, 2);

            $category = 'S';
            $categoryRate = $rate;
            
            if ((int) $item->taxtype === 0)
			{
                $category = 'E';
                $categoryRate = 0.0;
            }
            elseif ($rate <= 0)
			{
                $category = 'Z';
                $categoryRate = 0.0;
            }

            $lineExtensionTotal += $net;

            $groupKey = $category . '|' . $categoryRate;
            
            if (!isset($taxGroups[$groupKey]))
			{
                $taxGroups[$groupKey] = array('category' => $category, 'rate' => $categoryRate, 'taxable' => 0.0, 'tax' => 0.0);
            }
            $taxGroups[$groupKey]['taxable'] += $net;
            $taxGroups[$groupKey]['tax'] += round($net * ($categoryRate / 100), 2);

            $unitPrice = ($quantity != 0) ? round($net / $quantity, 4) : $net;

            $lines[] = array(
                'position'  => $position,
                'quantity'  => $quantity,
                'unitCode'  => self::unitCode($lineItem['entity'] ?? ''),
                'net'       => $net,
                'name'      => $lineItem['title'] ?? '',
                'desc'      => $lineItem['description'] ?? '',
                'pno'       => $lineItem['pno'] ?? '',
                'category'  => $category,
                'rate'      => $categoryRate,
                'unitPrice' => $unitPrice,
            );
            $position++;
        }

        // AllowanceCharge (document level discount) - prorated across tax groups by taxable
        // share, and subtracted from each group's taxable/tax amounts below, so TaxTotal is
        // computed on the discounted base rather than overstating VAT on the full line total.
        $rabatt = (float) ($item->rabatt ?? 0);
        
        if ($rabatt > 0 && !empty($taxGroups))
		{
            $keys = array_keys($taxGroups);
            $lastKey = end($keys);
            $allocated = 0.0;
            
            foreach ($keys as $key)
			{
                if ($key === $lastKey)
				{
                    $share = round($rabatt - $allocated, 2);
                }
                else
				{
                    $share = round($rabatt * ($taxGroups[$key]['taxable'] / $lineExtensionTotal), 2);
                    $allocated += $share;
                }
                
                if ($share <= 0)
				{
                    continue;
                }

                $taxGroups[$key]['taxable'] = round($taxGroups[$key]['taxable'] - $share, 2);
                $taxGroups[$key]['tax'] = round($taxGroups[$key]['taxable'] * ($taxGroups[$key]['rate'] / 100), 2);

                $allowance = $dom->createElement('cac:AllowanceCharge');
                self::addChild($dom, $allowance, 'cbc:ChargeIndicator', 'false');
                self::addChild($dom, $allowance, 'cbc:AllowanceChargeReason', 'Rabatt');
                self::addChild($dom, $allowance, 'cbc:Amount', self::formatAmount($share))->setAttribute('currencyID', $currency);
                $allowanceTaxCategory = $dom->createElement('cac:TaxCategory');
                self::addChild($dom, $allowanceTaxCategory, 'cbc:ID', $taxGroups[$key]['category']);
                self::addChild($dom, $allowanceTaxCategory, 'cbc:Percent', self::formatAmount($taxGroups[$key]['rate']));
                $allowanceTaxScheme = $dom->createElement('cac:TaxScheme');
                self::addChild($dom, $allowanceTaxScheme, 'cbc:ID', 'VAT');
                $allowanceTaxCategory->appendChild($allowanceTaxScheme);
                $allowance->appendChild($allowanceTaxCategory);
                $invoice->appendChild($allowance);
            }
        }

        // TaxTotal
        $taxTotalAmount = 0.0;
        $taxTotal = $dom->createElement('cac:TaxTotal');
        
        foreach ($taxGroups as $group)
		{
            $taxTotalAmount += $group['tax'];
            $subtotal = $dom->createElement('cac:TaxSubtotal');
            self::addChild($dom, $subtotal, 'cbc:TaxableAmount', self::formatAmount($group['taxable']))->setAttribute('currencyID', $currency);
            self::addChild($dom, $subtotal, 'cbc:TaxAmount', self::formatAmount($group['tax']))->setAttribute('currencyID', $currency);
            $category = $dom->createElement('cac:TaxCategory');
            self::addChild($dom, $category, 'cbc:ID', $group['category']);
            self::addChild($dom, $category, 'cbc:Percent', self::formatAmount($group['rate']));
            
            if ($group['category'] === 'E')
			{
                self::addChild($dom, $category, 'cbc:TaxExemptionReason', self::TAX_EXEMPT_REASON);
            }
            $taxScheme = $dom->createElement('cac:TaxScheme');
            self::addChild($dom, $taxScheme, 'cbc:ID', 'VAT');
            $category->appendChild($taxScheme);
            $subtotal->appendChild($category);
            $taxTotal->appendChild($subtotal);
        }
        self::insertFirst($dom, $taxTotal, 'cbc:TaxAmount', self::formatAmount($taxTotalAmount))->setAttribute('currencyID', $currency);
        $invoice->appendChild($taxTotal);

        // LegalMonetaryTotal
        $taxExclusive = round($lineExtensionTotal - $rabatt, 2);
        $taxInclusive = round($taxExclusive + $taxTotalAmount, 2);
        $paid = (float) ($item->paid ?? 0);
        $payable = round($taxInclusive - $paid, 2);

        $legalTotal = $dom->createElement('cac:LegalMonetaryTotal');
        self::addChild($dom, $legalTotal, 'cbc:LineExtensionAmount', self::formatAmount($lineExtensionTotal))->setAttribute('currencyID', $currency);
        self::addChild($dom, $legalTotal, 'cbc:TaxExclusiveAmount', self::formatAmount($taxExclusive))->setAttribute('currencyID', $currency);
        self::addChild($dom, $legalTotal, 'cbc:TaxInclusiveAmount', self::formatAmount($taxInclusive))->setAttribute('currencyID', $currency);
        
        if ($rabatt > 0)
		{
            self::addChild($dom, $legalTotal, 'cbc:AllowanceTotalAmount', self::formatAmount($rabatt))->setAttribute('currencyID', $currency);
        }
        
        if ($paid > 0)
		{
            self::addChild($dom, $legalTotal, 'cbc:PrepaidAmount', self::formatAmount($paid))->setAttribute('currencyID', $currency);
        }
        self::addChild($dom, $legalTotal, 'cbc:PayableAmount', self::formatAmount($payable))->setAttribute('currencyID', $currency);
        $invoice->appendChild($legalTotal);

        // InvoiceLines
        foreach ($lines as $lineItem)
		{
            $line = $dom->createElement('cac:InvoiceLine');
            self::addChild($dom, $line, 'cbc:ID', (string) $lineItem['position']);
            self::addChild($dom, $line, 'cbc:InvoicedQuantity', self::formatAmount($lineItem['quantity'], 4))->setAttribute('unitCode', $lineItem['unitCode']);
            self::addChild($dom, $line, 'cbc:LineExtensionAmount', self::formatAmount($lineItem['net']))->setAttribute('currencyID', $currency);

            $itemEl = $dom->createElement('cac:Item');
            
            if (!empty($lineItem['desc']))
			{
                self::addChild($dom, $itemEl, 'cbc:Description', strip_tags($lineItem['desc']));
            }
            self::addChild($dom, $itemEl, 'cbc:Name', strip_tags($lineItem['name']));
            
            if (!empty($lineItem['pno']))
			{
                $sellersItemId = $dom->createElement('cac:SellersItemIdentification');
                self::addChild($dom, $sellersItemId, 'cbc:ID', $lineItem['pno']);
                $itemEl->appendChild($sellersItemId);
            }
            $classifiedTax = $dom->createElement('cac:ClassifiedTaxCategory');
            self::addChild($dom, $classifiedTax, 'cbc:ID', $lineItem['category']);
            self::addChild($dom, $classifiedTax, 'cbc:Percent', self::formatAmount($lineItem['rate']));
            $lineTaxScheme = $dom->createElement('cac:TaxScheme');
            self::addChild($dom, $lineTaxScheme, 'cbc:ID', 'VAT');
            $classifiedTax->appendChild($lineTaxScheme);
            $itemEl->appendChild($classifiedTax);
            $line->appendChild($itemEl);

            $price = $dom->createElement('cac:Price');
            self::addChild($dom, $price, 'cbc:PriceAmount', self::formatAmount($lineItem['unitPrice'], 4))->setAttribute('currencyID', $currency);
            $line->appendChild($price);

            $invoice->appendChild($line);
        }

        return $dom->saveXML();
    }

    /**
     * Structured seller address: prefers the document's office/location (has street/zip/location/country
     * columns); falls back to parsing the business's free-text address textarea.
     */
    private static function sellerAddress($office, $company)
    {
        if (!empty($office) && !empty($office->street) && !empty($office->zip))
		{
            return array(
                'street'  => $office->street,
                'zip'     => $office->zip,
                'city'    => $office->location,
                'country' => self::countryToCode($office->country ?: 'DE'),
            );
        }
        
        return self::parseAddress($company['address'] ?? '');
    }

    /**
     * The company address is a free-text textarea (no structured fields), so this parses the
     * conventional "Street\nZIP City\nCountry" layout on a best-effort basis.
     */
    private static function parseAddress($rawAddress)
    {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', strip_tags($rawAddress ?? '')))));
        $result = array('street' => '', 'zip' => '', 'city' => '', 'country' => 'DE');

        if (!empty($lines[0]))
		{
            $result['street'] = $lines[0];
        }
        
        if (!empty($lines[1]) && preg_match('/^(\d{4,5})\s+(.+)$/', $lines[1], $matches))
		{
            $result['zip'] = $matches[1];
            $result['city'] = $matches[2];
        }
        elseif (!empty($lines[1]))
		{
            $result['city'] = $lines[1];
        }
        
        if (!empty($lines[2]))
		{
            $result['country'] = self::countryToCode($lines[2]);
        }

        return $result;
    }

    private static function countryToCode($country)
    {
        $country = trim((string) $country);
        
        if (preg_match('/^[A-Z]{2}$/', $country))
		{
            return $country;
        }
        $map = array(
            'deutschland' => 'DE',
            'germany'     => 'DE',
            'österreich'  => 'AT',
            'oesterreich' => 'AT',
            'austria'     => 'AT',
            'schweiz'     => 'CH',
            'switzerland' => 'CH',
        );
        $key = mb_strtolower($country);
        
        return $map[$key] ?? 'DE';
    }

    private static function unitCode($entity)
    {
        $entity = mb_strtolower(trim((string) $entity));
        $map = array(
            'stk' => 'C62', 'stück' => 'C62', 'stueck' => 'C62', 'st' => 'C62', 'pcs' => 'C62', 'pc' => 'C62', 'piece' => 'C62',
            'h' => 'HUR', 'std' => 'HUR', 'stunde' => 'HUR', 'stunden' => 'HUR', 'hour' => 'HUR', 'hrs' => 'HUR', 'hr' => 'HUR',
            'tag' => 'DAY', 'tage' => 'DAY', 'day' => 'DAY', 'days' => 'DAY',
            'monat' => 'MON', 'monate' => 'MON', 'month' => 'MON',
            'kg' => 'KGM', 'g' => 'GRM',
            'l' => 'LTR', 'liter' => 'LTR',
            'm' => 'MTR', 'meter' => 'MTR',
            'pauschal' => 'LS', 'lump sum' => 'LS', 'flat' => 'LS',
        );
        
        return $map[$entity] ?? 'C62';
    }

    private static function buildParty($dom, $name, array $address, $vatId, $endpointId)
    {
        $party = $dom->createElement('cac:Party');

        if (!empty($endpointId))
		{
            self::addChild($dom, $party, 'cbc:EndpointID', $endpointId)->setAttribute('schemeID', 'EM');
        }

        $partyName = $dom->createElement('cac:PartyName');
        self::addChild($dom, $partyName, 'cbc:Name', $name);
        $party->appendChild($partyName);

        $postalAddress = $dom->createElement('cac:PostalAddress');
        self::addChild($dom, $postalAddress, 'cbc:StreetName', $address['street'] ?? '');
        self::addChild($dom, $postalAddress, 'cbc:CityName', $address['city'] ?? '');
        self::addChild($dom, $postalAddress, 'cbc:PostalZone', $address['zip'] ?? '');
        $countryEl = $dom->createElement('cac:Country');
        self::addChild($dom, $countryEl, 'cbc:IdentificationCode', $address['country'] ?? 'DE');
        $postalAddress->appendChild($countryEl);
        $party->appendChild($postalAddress);

        if (!empty($vatId))
		{
            $taxScheme = $dom->createElement('cac:PartyTaxScheme');
            self::addChild($dom, $taxScheme, 'cbc:CompanyID', $vatId);
            $scheme = $dom->createElement('cac:TaxScheme');
            self::addChild($dom, $scheme, 'cbc:ID', 'VAT');
            $taxScheme->appendChild($scheme);
            $party->appendChild($taxScheme);
        }

        $legalEntity = $dom->createElement('cac:PartyLegalEntity');
        self::addChild($dom, $legalEntity, 'cbc:RegistrationName', $name);
        $party->appendChild($legalEntity);

        if (!empty($endpointId))
		{
            $contact = $dom->createElement('cac:Contact');
            self::addChild($dom, $contact, 'cbc:ElectronicMail', $endpointId);
            $party->appendChild($contact);
        }

        return $party;
    }

    private static function addChild(\DOMDocument $dom, \DOMElement $parent, $name, $value = null)
    {
        $el = $dom->createElement($name);
        
        if ($value !== null && $value !== '')
		{
            $el->appendChild($dom->createTextNode((string) $value));
        }
        $parent->appendChild($el);
        
        return $el;
    }

    private static function insertFirst(\DOMDocument $dom, \DOMElement $parent, $name, $value)
    {
        $el = $dom->createElement($name);
        $el->appendChild($dom->createTextNode((string) $value));
        
        if ($parent->firstChild)
		{
            $parent->insertBefore($el, $parent->firstChild);
        }
        else
		{
            $parent->appendChild($el);
        }
        
        return $el;
    }

    private static function formatAmount($value, $decimals = 2)
    {
        return number_format((float) $value, $decimals, '.', '');
    }

    private static function formatDate($value)
    {
        if (empty($value) || $value === '0000-00-00')
		{
            return date('Y-m-d');
        }
        $timestamp = is_numeric($value) ? (int) $value : strtotime($value);
        
        return date('Y-m-d', $timestamp ?: time());
    }
}
