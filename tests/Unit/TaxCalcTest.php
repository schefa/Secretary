<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

use PHPUnit\Framework\TestCase;
use Secretary\Helpers\TaxCalc;

/**
 * Mirrors media/secretary/js/__tests__/secretary.taxcalc.test.js case for
 * case (same inputs, same expected numbers) so the PHP and JS engines are
 * verified against one shared spec. If a case is added/changed on one side,
 * add/change it on the other too.
 */
final class TaxCalcTest extends TestCase
{
    public function testLineTaxInclusiveExtractsTaxAlreadyContainedInGrossAmount()
    {
        // 119 gross at 19% VAT contains exactly 19 of tax (100 net + 19 tax).
        $this->assertEqualsWithDelta(19, TaxCalc::lineTax(TaxCalc::TAX_INCLUSIVE, 119, 19, 1), 1e-10);
    }

    public function testLineTaxExclusiveAddsTaxOnTopOfNetAmount()
    {
        $this->assertEqualsWithDelta(19, TaxCalc::lineTax(TaxCalc::TAX_EXCLUSIVE, 100, 19, 1), 1e-10);
    }

    public function testLineTaxAppliesTheDiscountFactor()
    {
        $this->assertEqualsWithDelta(17.1, TaxCalc::lineTax(TaxCalc::TAX_EXCLUSIVE, 100, 19, 0.9), 1e-10);
    }

    public function testLineTaxZeroOrMissingRateYieldsNoTax()
    {
        $this->assertSame(0.0, TaxCalc::lineTax(TaxCalc::TAX_EXCLUSIVE, 100, 0, 1));
        $this->assertSame(0.0, TaxCalc::lineTax(TaxCalc::TAX_EXCLUSIVE, 100, null, 1));
    }

    public function testLineTaxZeroLineTotalYieldsNoTaxEvenWithRateSet()
    {
        $this->assertSame(0.0, TaxCalc::lineTax(TaxCalc::TAX_EXCLUSIVE, 0, 19, 1));
    }

    public function testLineTaxTypeNoneAlwaysYieldsZero()
    {
        $this->assertSame(0.0, TaxCalc::lineTax(TaxCalc::TAX_NONE, 100, 19, 1));
    }

    public function testAggregateTaxesNoTaxReturnsZeroTotalAndNoBreakdown()
    {
        $result = TaxCalc::aggregateTaxes(TaxCalc::TAX_NONE, array(array('total' => 100, 'taxRate' => 19)), 0);
        $this->assertSame(0.0, $result['taxTotal']);
        $this->assertSame(array(), $result['byRate']);
    }

    public function testAggregateTaxesGroupsItemsByTaxRateAndSumsEachGroup()
    {
        $items = array(
            array('total' => 100, 'taxRate' => 19),
            array('total' => 50, 'taxRate' => 19),
            array('total' => 100, 'taxRate' => 7),
        );
        $result = TaxCalc::aggregateTaxes(TaxCalc::TAX_EXCLUSIVE, $items, 0);
        $this->assertEqualsWithDelta(28.5, $result['byRate']['19'], 1e-10); // (100+50) * 0.19
        $this->assertEqualsWithDelta(7, $result['byRate']['7'], 1e-10);
        $this->assertEqualsWithDelta(35.5, $result['taxTotal'], 1e-10);
    }

    public function testAggregateTaxesSkipsItemsWithoutPositiveTaxRate()
    {
        $items = array(
            array('total' => 100, 'taxRate' => 19),
            array('total' => 250), // sub-document row: no taxRate key
            array('total' => 80, 'taxRate' => 0),
        );
        $result = TaxCalc::aggregateTaxes(TaxCalc::TAX_EXCLUSIVE, $items, 0);
        $this->assertEquals(array('19' => 19.0), $result['byRate']);
        $this->assertEqualsWithDelta(19, $result['taxTotal'], 1e-10);
    }

    public function testAggregateTaxesNonArrayInputIsTreatedAsNoItems()
    {
        $result = TaxCalc::aggregateTaxes(TaxCalc::TAX_EXCLUSIVE, null, 0);
        $this->assertSame(0.0, $result['taxTotal']);
        $this->assertSame(array(), $result['byRate']);
    }

    public function testComputeDocumentTotalsNoTaxSubtotalAndTotalEqualSumTaxIsZero()
    {
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_NONE, array(array('total' => 100, 'taxRate' => 19)), array());
        $this->assertSame(0.0, $totals['taxTotal']);
        $this->assertSame(100.0, $totals['subtotal']);
        $this->assertSame(100.0, $totals['total']);
        $this->assertSame(array(), $totals['byRate']);
    }

    public function testComputeDocumentTotalsInclusiveSingleRateNoDiscount()
    {
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_INCLUSIVE, array(array('total' => 119, 'taxRate' => 19)), array());
        $this->assertSame(119.0, $totals['total']);
        $this->assertSame(100.0, $totals['subtotal']);
        $this->assertSame(19.0, $totals['taxTotal']);
    }

    public function testComputeDocumentTotalsInclusivePercentageDiscountReducesNetTaxAndGrossConsistently()
    {
        $totals = TaxCalc::computeDocumentTotals(
            TaxCalc::TAX_INCLUSIVE,
            array(array('total' => 119, 'taxRate' => 19)),
            array('percent' => 10)
        );
        // 10% off 119 gross -> 107.10 gross, still 19% VAT inside.
        $this->assertSame(107.1, $totals['total']);
        $this->assertSame(90.0, $totals['subtotal']);
        $this->assertSame(17.1, $totals['taxTotal']);
    }

    public function testComputeDocumentTotalsInclusiveAbsoluteDiscountMatchesEquivalentPercentage()
    {
        $byAmount = TaxCalc::computeDocumentTotals(
            TaxCalc::TAX_INCLUSIVE,
            array(array('total' => 119, 'taxRate' => 19)),
            array('amount' => 11.9)
        );
        $byPercent = TaxCalc::computeDocumentTotals(
            TaxCalc::TAX_INCLUSIVE,
            array(array('total' => 119, 'taxRate' => 19)),
            array('percent' => 10)
        );
        $this->assertSame($byPercent['total'], $byAmount['total']);
        $this->assertSame($byPercent['subtotal'], $byAmount['subtotal']);
        $this->assertSame($byPercent['taxTotal'], $byAmount['taxTotal']);
        $this->assertEqualsWithDelta(10, $byAmount['discountPercent'], 1e-10);
    }

    public function testComputeDocumentTotalsInclusiveMixedTaxRates()
    {
        $items = array(
            array('total' => 119, 'taxRate' => 19), // net 100, tax 19
            array('total' => 107, 'taxRate' => 7),  // net 100, tax 7
        );
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_INCLUSIVE, $items, array('percent' => 10));
        $this->assertEqualsWithDelta($totals['subtotal'] + $totals['taxTotal'], $totals['total'], 0.005);
        $this->assertEqualsWithDelta(17.1, $totals['byRate']['19'], 0.005);
        $this->assertEqualsWithDelta(6.3, $totals['byRate']['7'], 0.005);
    }

    public function testComputeDocumentTotalsExclusiveSingleRateNoDiscount()
    {
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, array(array('total' => 100, 'taxRate' => 19)), array());
        $this->assertSame(100.0, $totals['subtotal']);
        $this->assertSame(19.0, $totals['taxTotal']);
        $this->assertSame(119.0, $totals['total']);
    }

    public function testComputeDocumentTotalsExclusivePercentageDiscountAppliedToNetBeforeTax()
    {
        $totals = TaxCalc::computeDocumentTotals(
            TaxCalc::TAX_EXCLUSIVE,
            array(array('total' => 100, 'taxRate' => 19)),
            array('percent' => 10)
        );
        $this->assertSame(90.0, $totals['subtotal']);
        $this->assertSame(17.1, $totals['taxTotal']);
        $this->assertSame(107.1, $totals['total']);
    }

    public function testComputeDocumentTotalsExclusiveMixedTaxRatesWithAbsoluteDiscountStayConsistent()
    {
        $items = array(
            array('total' => 100, 'taxRate' => 19),
            array('total' => 100, 'taxRate' => 7),
        );
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, $items, array('amount' => 20));
        $this->assertSame(180.0, $totals['subtotal']);
        $this->assertEqualsWithDelta(17.1, $totals['byRate']['19'], 0.005);
        $this->assertEqualsWithDelta(6.3, $totals['byRate']['7'], 0.005);
        $this->assertEqualsWithDelta(203.4, $totals['total'], 0.005);
    }

    public function testComputeDocumentTotalsExclusiveLinkedSubDocumentContributesToSumOnly()
    {
        $items = array(
            array('total' => 100, 'taxRate' => 19), // regular item, net 100
            array('total' => 250),                  // linked document, already-taxed gross total
        );
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, $items, array());
        $this->assertSame(350.0, $totals['sum']);
        $this->assertSame(350.0, $totals['subtotal']);
        $this->assertSame(19.0, $totals['taxTotal']); // only the regular item is taxed
        $this->assertSame(369.0, $totals['total']);
    }

    public function testComputeDocumentTotalsEdgeCaseNoItems()
    {
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, array(), array());
        $this->assertSame(0.0, $totals['sum']);
        $this->assertSame(0.0, $totals['subtotal']);
        $this->assertSame(0.0, $totals['total']);
        $this->assertSame(0.0, $totals['taxTotal']);
    }

    public function testComputeDocumentTotalsEdgeCaseAbsoluteDiscountWithZeroSumDoesNotProduceInfinityOrNan()
    {
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, array(), array('amount' => 5));
        $this->assertIsFloat($totals['discountPercent']);
        $this->assertFalse(is_nan($totals['discountPercent']));
        $this->assertTrue(is_finite($totals['discountPercent']));
        $this->assertSame(0.0, $totals['discountPercent']);
    }

    public function testComputeDocumentTotalsEdgeCaseStringTypedValuesAreHandledLikeNumbers()
    {
        $totals = TaxCalc::computeDocumentTotals(
            TaxCalc::TAX_EXCLUSIVE,
            array(array('total' => '100.00', 'taxRate' => '19')),
            array('percent' => '10')
        );
        $this->assertSame(90.0, $totals['subtotal']);
        $this->assertSame(17.1, $totals['taxTotal']);
    }

    public function testComputeDocumentTotalsEdgeCaseRoundingNeverCarriesMoreThanCentPrecision()
    {
        $items = array(
            array('total' => 10, 'taxRate' => 19),
            array('total' => 10, 'taxRate' => 19),
            array('total' => 10, 'taxRate' => 19),
        );
        $totals = TaxCalc::computeDocumentTotals(TaxCalc::TAX_EXCLUSIVE, $items, array());
        $this->assertSame(round($totals['taxTotal'] * 100) / 100, $totals['taxTotal']);
        $this->assertSame(round($totals['total'] * 100) / 100, $totals['total']);
    }
}
