<?php

namespace Secretary\Helpers;

defined('_JEXEC') or die;

/**
 * Pure tax/total calculation for documents (invoices, offers, ...).
 *
 * This is a deliberate 1:1 port of media/secretary/js/secretary.taxcalc.js -
 * same inputs, same rounding, same edge-case handling - so the totals a
 * document is actually saved with can never drift from what the live
 * client-side preview showed while editing. Both sides are unit tested
 * against the same numeric scenarios (see tests/TaxCalcTest.php and
 * media/secretary/js/__tests__/secretary.taxcalc.test.js). If you change
 * one, change the other and re-run both suites.
 *
 * No Joomla/DB dependency, so it can be unit tested directly under PHPUnit.
 *
 * Tax types (mirrors COM_SECRETARY_NOTAX / _INKLUSIV / _EXKLUSIV):
 *   0 - no tax
 *   1 - inclusive: line totals already include tax, tax is extracted from them
 *   2 - exclusive: line totals are net, tax is added on top
 */
class TaxCalc
{
    const TAX_NONE = 0;
    const TAX_INCLUSIVE = 1;
    const TAX_EXCLUSIVE = 2;

    public static function round2($value)
    {
        return round((float) $value, 2);
    }

    /**
     * Converts a discount percentage (e.g. 10 for 10%) into the multiplier
     * that, applied to a gross/net amount, yields the discounted amount.
     * Values <= 0 (or non-numeric) mean "no discount".
     */
    public static function discountFactor($discountPercent)
    {
        $pct = self::toNumber($discountPercent);
        
        if (!($pct > 0))
		{
            return 1.0;
        }
        
        return 1 - ($pct / 100);
    }

    /**
     * Tax amount contained in (taxtype 1) or owed on top of (taxtype 2)
     * a single line's total, after discount.
     */
    public static function lineTax($taxtype, $lineTotal, $taxRate, $discountFactorValue)
    {
        $total = self::toNumber($lineTotal);
        $rate = self::toNumber($taxRate);

        if (!($rate > 0) || $total == 0.0 || is_nan($total))
		{
            return 0.0;
        }

        if ($taxtype === self::TAX_INCLUSIVE)
		{
            return ($total - ($total / (1 + ($rate / 100)))) * $discountFactorValue;
        }
        
        if ($taxtype === self::TAX_EXCLUSIVE)
		{
            return ($total * ($rate / 100)) * $discountFactorValue;
        }
        
        return 0.0;
    }

    /**
     * items: array of ['total' => number|string, 'taxRate' => number|string]
     * discountPercent: overall discount in percent (0 = none)
     *
     * Returns ['taxTotal' => float, 'byRate' => ["<rate>" => float]]
     * byRate keys are the distinct tax rates found among the items, values
     * are the summed (unrounded) tax amount for that rate.
     */
    public static function aggregateTaxes($taxtype, $items, $discountPercent)
    {
        $byRate = array();
        $taxTotal = 0.0;

        if ($taxtype === self::TAX_NONE || !is_array($items))
		{
            return array('taxTotal' => 0.0, 'byRate' => $byRate);
        }

        $factor = self::discountFactor($discountPercent);

        foreach ($items as $item)
		{
            $rate = self::toNumber(isset($item['taxRate']) ? $item['taxRate'] : null);
            $total = self::toNumber(isset($item['total']) ? $item['total'] : null);

            if (!($rate > 0) || $total == 0.0 || is_nan($total))
			{
                continue;
            }

            $amount = self::lineTax($taxtype, $total, $rate, $factor);
            
            if (is_nan($amount))
			{
                continue;
            }

            $rateKey = self::rateKey($rate);
            
            if (!isset($byRate[$rateKey]))
			{
                $byRate[$rateKey] = 0.0;
            }
            $byRate[$rateKey] += $amount;
            $taxTotal += $amount;
        }

        return array('taxTotal' => $taxTotal, 'byRate' => $byRate);
    }

    /**
     * Full totals for a document.
     *
     * items: array of ['total' => ..., 'taxRate' => ...]. A "linked
     * sub-document" row (a total already computed elsewhere, e.g. a
     * referenced document) is represented by omitting 'taxRate' - it
     * contributes to the sum/subtotal only, exactly like the JS side.
     * taxtype: 0 | 1 | 2
     * discount: ['amount' => number|string, 'percent' => number|string]
     *   Only one of amount/percent is expected to be set (mirrors the
     *   "Rabatt in currency" vs "Rabatt in %" pair of inputs); if amount is
     *   given it takes precedence and percent is derived from it.
     *
     * Returns:
     *   [
     *     'sum'             => gross total (item totals, pre-discount)
     *     'discountAmount'  => resolved discount, in currency
     *     'discountPercent' => resolved discount, in percent
     *     'total'           => final gross total after discount (+ tax if exclusive)
     *     'subtotal'        => net total
     *     'taxTotal'        => total tax
     *     'byRate'          => tax amount per rate, rounded
     *     'byRateRaw'       => tax amount per rate, unrounded
     *   ]
     * All money fields are rounded to 2 decimals.
     */
    public static function computeDocumentTotals($taxtype, $items, $discount = array())
    {
        $items = is_array($items) ? $items : array();
        $discount = is_array($discount) ? $discount : array();

        $sum = 0.0;
        
        foreach ($items as $item)
		{
            $total = self::toNumber(isset($item['total']) ? $item['total'] : null);
            $sum += is_nan($total) ? 0 : $total;
        }

        $discountAmount = self::toNumber(isset($discount['amount']) ? $discount['amount'] : null);
        $discountPercent = self::toNumber(isset($discount['percent']) ? $discount['percent'] : null);
        $discountAmount = is_nan($discountAmount) ? 0.0 : $discountAmount;
        $discountPercent = is_nan($discountPercent) ? 0.0 : $discountPercent;

        if ($discountAmount > 0)
		{
            $discountPercent = $sum > 0 ? ($discountAmount * 100 / $sum) : 0.0;
        }
        elseif ($discountPercent > 0)
		{
            $discountAmount = $sum * ($discountPercent / 100);
        }
        else
		{
            $discountAmount = 0.0;
            $discountPercent = 0.0;
        }

        $discountedSum = $sum - $discountAmount;

        $taxes = self::aggregateTaxes($taxtype, $items, $discountPercent);
        $taxTotal = $taxes['taxTotal'];

        if ($taxtype === self::TAX_INCLUSIVE)
		{
            $subtotal = $discountedSum - $taxTotal;
            $total = $discountedSum;
        }
        elseif ($taxtype === self::TAX_EXCLUSIVE)
		{
            $subtotal = $discountedSum;
            $total = $discountedSum + $taxTotal;
        }
        else
		{
            $subtotal = $discountedSum;
            $total = $discountedSum;
            $taxTotal = 0.0;
        }

        $byRateRounded = array();
        
        foreach ($taxes['byRate'] as $rate => $amount)
		{
            $byRateRounded[$rate] = self::round2($amount);
        }

        return array(
            'sum' => self::round2($sum),
            'discountAmount' => self::round2($discountAmount),
            'discountPercent' => $discountPercent,
            'total' => self::round2($total),
            'subtotal' => self::round2($subtotal),
            'taxTotal' => self::round2($taxTotal),
            'byRate' => $byRateRounded,
            // Unrounded per-rate amounts, for callers that want to preserve
            // sub-cent precision instead of double-rounding.
            'byRateRaw' => $taxes['byRate'],
        );
    }

    /**
     * Mirrors JS's Number() coercion for the values this module actually
     * receives (numbers, numeric strings from form/DB, or a missing key).
     * PHP has no "undefined", so a missing/null value is treated like JS
     * Number(undefined) (NAN, "not a taxable value") rather than like
     * Number(null) (0) - that's the meaning a missing 'taxRate' has
     * everywhere this module is called from.
     */
    private static function toNumber($value)
    {
        if (is_int($value) || is_float($value))
		{
            return (float) $value;
        }
        
        if ($value === '')
		{
            return 0.0;
        }
        
        if (is_string($value) && is_numeric(trim($value)))
		{
            return (float) trim($value);
        }
        
        return NAN;
    }

    /**
     * Array keys in PHP silently normalize numeric-string keys to int
     * ("19" and "19.0" would collide as different keys otherwise stay
     * distinct) - format consistently so e.g. rate 19 and 19.0 group together
     * exactly like JS object keys (which stringify the same way).
     */
    private static function rateKey($rate)
    {
        return rtrim(rtrim(sprintf('%F', $rate), '0'), '.');
    }
}
