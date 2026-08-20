const TaxCalc = require('../secretary.taxcalc.js');

describe('lineTax', () => {
	test('inclusive: extracts the tax already contained in a gross amount', () => {
		// 119 gross at 19% VAT contains exactly 19 of tax (100 net + 19 tax).
		expect(TaxCalc.lineTax(TaxCalc.TAX_INCLUSIVE, 119, 19, 1)).toBeCloseTo(19, 10);
	});

	test('exclusive: adds tax on top of a net amount', () => {
		expect(TaxCalc.lineTax(TaxCalc.TAX_EXCLUSIVE, 100, 19, 1)).toBeCloseTo(19, 10);
	});

	test('applies the discount factor', () => {
		expect(TaxCalc.lineTax(TaxCalc.TAX_EXCLUSIVE, 100, 19, 0.9)).toBeCloseTo(17.1, 10);
	});

	test('a zero or missing tax rate yields no tax', () => {
		expect(TaxCalc.lineTax(TaxCalc.TAX_EXCLUSIVE, 100, 0, 1)).toBe(0);
		expect(TaxCalc.lineTax(TaxCalc.TAX_EXCLUSIVE, 100, undefined, 1)).toBe(0);
	});

	test('a zero line total yields no tax even with a rate set', () => {
		expect(TaxCalc.lineTax(TaxCalc.TAX_EXCLUSIVE, 0, 19, 1)).toBe(0);
	});

	test('taxtype 0 (no tax) always yields 0', () => {
		expect(TaxCalc.lineTax(TaxCalc.TAX_NONE, 100, 19, 1)).toBe(0);
	});
});

describe('aggregateTaxes', () => {
	test('no tax: returns 0 total and no breakdown regardless of items', () => {
		const result = TaxCalc.aggregateTaxes(TaxCalc.TAX_NONE, [{ total: 100, taxRate: 19 }], 0);
		expect(result.taxTotal).toBe(0);
		expect(result.byRate).toEqual({});
	});

	test('groups items by tax rate and sums each group', () => {
		const items = [
			{ total: 100, taxRate: 19 },
			{ total: 50, taxRate: 19 },
			{ total: 100, taxRate: 7 }
		];
		const result = TaxCalc.aggregateTaxes(TaxCalc.TAX_EXCLUSIVE, items, 0);
		expect(result.byRate['19']).toBeCloseTo(28.5, 10); // (100+50) * 0.19
		expect(result.byRate['7']).toBeCloseTo(7, 10);
		expect(result.taxTotal).toBeCloseTo(35.5, 10);
	});

	test('items without a positive tax rate (e.g. linked sub-documents) are skipped', () => {
		const items = [
			{ total: 100, taxRate: 19 },
			{ total: 250, taxRate: undefined }, // sub-document row: no taxrate input
			{ total: 80, taxRate: 0 }
		];
		const result = TaxCalc.aggregateTaxes(TaxCalc.TAX_EXCLUSIVE, items, 0);
		expect(result.byRate).toEqual({ 19: 19 });
		expect(result.taxTotal).toBeCloseTo(19, 10);
	});

	test('non-array input is treated as no items', () => {
		expect(TaxCalc.aggregateTaxes(TaxCalc.TAX_EXCLUSIVE, null, 0)).toEqual({ taxTotal: 0, byRate: {} });
	});
});

describe('computeDocumentTotals - no tax (taxtype 0)', () => {
	test('subtotal and total equal the (discounted) sum, tax is exactly 0', () => {
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_NONE, [{ total: 100, taxRate: 19 }], {});
		expect(totals.taxTotal).toBe(0);
		expect(totals.subtotal).toBe(100);
		expect(totals.total).toBe(100);
		expect(totals.byRate).toEqual({});
	});
});

describe('computeDocumentTotals - inclusive tax (taxtype 1)', () => {
	test('single rate, no discount: splits gross into net + tax', () => {
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_INCLUSIVE, [{ total: 119, taxRate: 19 }], {});
		expect(totals.total).toBe(119);
		expect(totals.subtotal).toBe(100);
		expect(totals.taxTotal).toBe(19);
	});

	test('percentage discount reduces net, tax and gross consistently', () => {
		const totals = TaxCalc.computeDocumentTotals(
			TaxCalc.TAX_INCLUSIVE,
			[{ total: 119, taxRate: 19 }],
			{ percent: 10 }
		);
		// 10% off 119 gross -> 107.10 gross, still 19% VAT inside.
		expect(totals.total).toBe(107.1);
		expect(totals.subtotal).toBe(90);
		expect(totals.taxTotal).toBe(17.1);
	});

	test('absolute discount amount produces the same result as the equivalent percentage', () => {
		const byAmount = TaxCalc.computeDocumentTotals(
			TaxCalc.TAX_INCLUSIVE,
			[{ total: 119, taxRate: 19 }],
			{ amount: 11.9 }
		);
		const byPercent = TaxCalc.computeDocumentTotals(
			TaxCalc.TAX_INCLUSIVE,
			[{ total: 119, taxRate: 19 }],
			{ percent: 10 }
		);
		expect(byAmount.total).toBe(byPercent.total);
		expect(byAmount.subtotal).toBe(byPercent.subtotal);
		expect(byAmount.taxTotal).toBe(byPercent.taxTotal);
		expect(byAmount.discountPercent).toBeCloseTo(10, 10);
	});

	test('mixed tax rates: subtotal + taxTotal reconstructs the discounted gross total', () => {
		const items = [
			{ total: 119, taxRate: 19 }, // net 100, tax 19
			{ total: 107, taxRate: 7 }   // net 100, tax 7
		];
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_INCLUSIVE, items, { percent: 10 });
		expect(totals.total).toBeCloseTo(totals.subtotal + totals.taxTotal, 2);
		expect(totals.byRate['19']).toBeCloseTo(17.1, 2);
		expect(totals.byRate['7']).toBeCloseTo(6.3, 2);
	});
});

describe('computeDocumentTotals - exclusive tax (taxtype 2)', () => {
	test('single rate, no discount: adds tax on top of net', () => {
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, [{ total: 100, taxRate: 19 }], {});
		expect(totals.subtotal).toBe(100);
		expect(totals.taxTotal).toBe(19);
		expect(totals.total).toBe(119);
	});

	test('percentage discount is applied to net before tax is added', () => {
		const totals = TaxCalc.computeDocumentTotals(
			TaxCalc.TAX_EXCLUSIVE,
			[{ total: 100, taxRate: 19 }],
			{ percent: 10 }
		);
		expect(totals.subtotal).toBe(90);
		expect(totals.taxTotal).toBe(17.1);
		expect(totals.total).toBe(107.1);
	});

	test('mixed tax rates with an absolute discount stay internally consistent', () => {
		const items = [
			{ total: 100, taxRate: 19 },
			{ total: 100, taxRate: 7 }
		];
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, items, { amount: 20 });
		expect(totals.subtotal).toBe(180);
		expect(totals.byRate['19']).toBeCloseTo(17.1, 2);
		expect(totals.byRate['7']).toBeCloseTo(6.3, 2);
		expect(totals.total).toBeCloseTo(203.4, 2);
	});

	test('a linked sub-document total (no tax rate) contributes to sum/subtotal only', () => {
		const items = [
			{ total: 100, taxRate: 19 },  // regular item, net 100
			{ total: 250, taxRate: undefined } // linked document, already-taxed gross total
		];
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, items, {});
		expect(totals.sum).toBe(350);
		expect(totals.subtotal).toBe(350);
		expect(totals.taxTotal).toBe(19); // only the regular item is taxed
		expect(totals.total).toBe(369);
	});
});

describe('computeDocumentTotals - edge cases', () => {
	test('no items: everything is zero, no division by zero artifacts', () => {
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, [], {});
		expect(totals.sum).toBe(0);
		expect(totals.subtotal).toBe(0);
		expect(totals.total).toBe(0);
		expect(totals.taxTotal).toBe(0);
	});

	test('an absolute discount with a zero sum does not produce Infinity/NaN', () => {
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, [], { amount: 5 });
		expect(Number.isFinite(totals.discountPercent)).toBe(true);
		expect(Number.isNaN(totals.discountPercent)).toBe(false);
		expect(totals.discountPercent).toBe(0);
	});

	test('string-typed DOM values (as jQuery .val() returns) are handled like numbers', () => {
		const totals = TaxCalc.computeDocumentTotals(
			TaxCalc.TAX_EXCLUSIVE,
			[{ total: '100.00', taxRate: '19' }],
			{ percent: '10' }
		);
		expect(totals.subtotal).toBe(90);
		expect(totals.taxTotal).toBe(17.1);
	});

	test('rounding: totals never carry more than cent precision', () => {
		const items = [
			{ total: 10, taxRate: 19 },
			{ total: 10, taxRate: 19 },
			{ total: 10, taxRate: 19 }
		];
		const totals = TaxCalc.computeDocumentTotals(TaxCalc.TAX_EXCLUSIVE, items, {});
		expect(totals.taxTotal).toBe(Math.round(totals.taxTotal * 100) / 100);
		expect(totals.total).toBe(Math.round(totals.total * 100) / 100);
	});
});
