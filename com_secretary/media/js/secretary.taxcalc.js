/**
 *
 * Pure tax/total calculation for documents (invoices, offers, ...).
 * No DOM/jQuery dependency, so it can run both in the browser (attached to
 * the global Secretary namespace) and under Node for unit tests.
 *
 * Tax types (mirrors COM_SECRETARY_NOTAX / _INKLUSIV / _EXKLUSIV):
 *   0 - no tax
 *   1 - inclusive: line totals already include tax, tax is extracted from them
 *   2 - exclusive: line totals are net, tax is added on top
 */
(function (root) {

	var TAX_NONE = 0;
	var TAX_INCLUSIVE = 1;
	var TAX_EXCLUSIVE = 2;

	function round2(value) {
		return Math.round((value + Number.EPSILON) * 100) / 100;
	}

	/**
	 * Converts a discount percentage (e.g. 10 for 10%) into the multiplier
	 * that, applied to a gross/net amount, yields the discounted amount.
	 * Values <= 0 (or non-numeric) mean "no discount".
	 */
	function discountFactor(discountPercent) {
		var pct = Number(discountPercent);
		if (!(pct > 0)) {
			return 1;
		}
		return 1 - (pct / 100);
	}

	/**
	 * Tax amount contained in (taxtype 1) or owed on top of (taxtype 2)
	 * a single line's total, after discount.
	 */
	function lineTax(taxtype, lineTotal, taxRate, discountFactorValue) {
		var total = Number(lineTotal);
		var rate = Number(taxRate);

		if (!(rate > 0) || !total) {
			return 0;
		}

		if (taxtype === TAX_INCLUSIVE) {
			return (total - (total / (1 + (rate / 100)))) * discountFactorValue;
		}
		if (taxtype === TAX_EXCLUSIVE) {
			return (total * (rate / 100)) * discountFactorValue;
		}
		return 0;
	}

	/**
	 * items: array of { total: number|string, taxRate: number|string }
	 * discountPercent: overall discount in percent (0 = none)
	 *
	 * Returns { taxTotal: number, byRate: { "<rate>": number } }
	 * byRate keys are the distinct tax rates found among the items, values
	 * are the summed (unrounded) tax amount for that rate.
	 */
	function aggregateTaxes(taxtype, items, discountPercent) {
		var byRate = {};
		var taxTotal = 0;

		if (taxtype === TAX_NONE || !Array.isArray(items)) {
			return { taxTotal: 0, byRate: byRate };
		}

		var factor = discountFactor(discountPercent);

		items.forEach(function (item) {
			var rate = Number(item && item.taxRate);
			var total = Number(item && item.total);

			if (!(rate > 0) || !total) {
				return;
			}

			var amount = lineTax(taxtype, total, rate, factor);
			if (isNaN(amount)) {
				return;
			}

			if (!byRate.hasOwnProperty(rate)) {
				byRate[rate] = 0;
			}
			byRate[rate] += amount;
			taxTotal += amount;
		});

		return { taxTotal: taxTotal, byRate: byRate };
	}

	/**
	 * Full totals for a document.
	 *
	 * items: array of { total, taxRate }
	 * taxtype: 0 | 1 | 2
	 * discount: { amount: number|string, percent: number|string }
	 *   Only one of amount/percent is expected to be set (mirrors the
	 *   "Rabatt in currency" vs "Rabatt in %" pair of inputs); if amount is
	 *   given it takes precedence and percent is derived from it.
	 *
	 * Returns:
	 *   {
	 *     sum,            // gross total (item totals, pre-discount)
	 *     discountAmount, // resolved discount, in currency
	 *     discountPercent,// resolved discount, in percent
	 *     total,          // final gross total after discount (+ tax if exclusive)
	 *     subtotal,       // net total
	 *     taxTotal,       // total tax
	 *     byRate          // tax amount per rate
	 *   }
	 * All money fields are rounded to 2 decimals.
	 */
	function computeDocumentTotals(taxtype, items, discount) {
		discount = discount || {};

		var sum = (Array.isArray(items) ? items : []).reduce(function (acc, item) {
			var total = Number(item && item.total);
			return acc + (isNaN(total) ? 0 : total);
		}, 0);

		var discountAmount = Number(discount.amount);
		var discountPercent = Number(discount.percent);

		if (discountAmount > 0) {
			discountPercent = sum > 0 ? (discountAmount * 100 / sum) : 0;
		} else if (discountPercent > 0) {
			discountAmount = sum * (discountPercent / 100);
		} else {
			discountAmount = 0;
			discountPercent = 0;
		}

		var discountedSum = sum - discountAmount;

		var taxes = aggregateTaxes(taxtype, items, discountPercent);
		var taxTotal = taxes.taxTotal;

		var subtotal;
		var total;

		if (taxtype === TAX_INCLUSIVE) {
			subtotal = discountedSum - taxTotal;
			total = discountedSum;
		} else if (taxtype === TAX_EXCLUSIVE) {
			subtotal = discountedSum;
			total = discountedSum + taxTotal;
		} else {
			subtotal = discountedSum;
			total = discountedSum;
			taxTotal = 0;
		}

		var byRateRounded = {};
		Object.keys(taxes.byRate).forEach(function (rate) {
			byRateRounded[rate] = round2(taxes.byRate[rate]);
		});

		return {
			sum: round2(sum),
			discountAmount: round2(discountAmount),
			discountPercent: discountPercent,
			total: round2(total),
			subtotal: round2(subtotal),
			taxTotal: round2(taxTotal),
			byRate: byRateRounded,
			// Unrounded per-rate amounts, for callers (e.g. a hidden form field)
			// that want to preserve sub-cent precision instead of double-rounding.
			byRateRaw: taxes.byRate
		};
	}

	var TaxCalc = {
		TAX_NONE: TAX_NONE,
		TAX_INCLUSIVE: TAX_INCLUSIVE,
		TAX_EXCLUSIVE: TAX_EXCLUSIVE,
		round2: round2,
		discountFactor: discountFactor,
		lineTax: lineTax,
		aggregateTaxes: aggregateTaxes,
		computeDocumentTotals: computeDocumentTotals
	};

	if (typeof module !== 'undefined' && module.exports) {
		module.exports = TaxCalc;
	}
	if (root) {
		root.Secretary = root.Secretary || {};
		root.Secretary.TaxCalc = TaxCalc;
	}

}(typeof window !== 'undefined' ? window : (typeof global !== 'undefined' ? global : this)));
