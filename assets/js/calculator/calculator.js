/**
 * Cost Calculator Frontend JS
 */

(function ($) {
	'use strict';

	$(document).ready(function () {
		// Initialize quantity controls
		initQuantityControls();

		// Initialize sliders
		initSliders();

		// Initialize field calculations
		initFieldCalculations();
	});

	/**
	 * Initialize quantity button controls
	 */
	function initQuantityControls() {
		$(document).on('click', '.qty-plus', function (e) {
			e.preventDefault();
			const input = $(this).siblings('.qty-input');
			const max = input.attr('max') || 100;
			let value = parseInt(input.val()) || 0;
			value = Math.min(value + 1, parseInt(max));
			input.val(value).trigger('change');
		});

		$(document).on('click', '.qty-minus', function (e) {
			e.preventDefault();
			const input = $(this).siblings('.qty-input');
			const min = input.attr('min') || 0;
			let value = parseInt(input.val()) || 0;
			value = Math.max(value - 1, parseInt(min));
			input.val(value).trigger('change');
		});
	}

	/**
	 * Initialize slider value display
	 */
	function initSliders() {
		$(document).on('input change', '.slider', function () {
			$(this).siblings('.slider-value').text($(this).val());
			$(this).trigger('change');
		});

		// Initialize slider display on load
		$('.slider').each(function () {
			$(this).siblings('.slider-value').text($(this).val());
		});
	}

	/**
	 * Initialize field change listeners
	 */
	function initFieldCalculations() {
		$(document).on('change input', '.field-calculate, .qty-input, .toggle-input', function () {
			triggerCalculation($(this).closest('.cost-calculator-wrapper'));
		});

		$('.cost-calculator-wrapper').each(function () {
			triggerCalculation($(this));
		});
	}

	/**
	 * Trigger calculator calculation
	 */
	function triggerCalculation(wrapper) {
		const calculatorId = wrapper.data('calculator-id');
		const form = wrapper.find('form');
		const values = getFormValues(form);

		$.ajax({
			url: costCalculatorCore.ajaxUrl,
			type: 'POST',
			data: {
				action: 'calculate',
				calculator_id: calculatorId,
				values: values,
				nonce: costCalculatorCore.nonce
			},
			success: function (response) {
				if (response.success) {
					updateSummary(wrapper, response.data);
				}
			},
			error: function (error) {
				console.error('Calculation error:', error);
			}
		});
	}

	/**
	 * Get form values
	 */
	function getFormValues(form) {
		const values = {};

		form.find('.field-calculate').each(function () {
			const name = $(this).attr('name');
			const type = $(this).attr('type');

			if (!name) {
				return;
			}

			if (type === 'checkbox' && $(this).hasClass('toggle-input')) {
				const onValue = parseFloat($(this).data('on-value')) || 1;
				const offValue = parseFloat($(this).data('off-value')) || 0;
				values[name] = $(this).is(':checked') ? onValue : offValue;
				return;
			}

			if (type === 'checkbox') {
				// Handle checkbox arrays
				const key = name.replace(/\[\]$/, '');
				const checkboxes = form.find('input[name="' + name + '"]');
				const checkedValues = [];
				checkboxes.each(function () {
					if ($(this).is(':checked')) {
						checkedValues.push(parseFloat($(this).val()) || 0);
					}
				});
				values[key] = checkedValues.length > 0 ? checkedValues.reduce((a, b) => a + b, 0) : 0;
			} else if (type === 'radio') {
				// Handle radio buttons
				const selected = form.find('input[name="' + name + '"]:checked');
				values[name] = parseFloat(selected.val()) || 0;
			} else {
				// Handle text, number, select, etc.
				const value = $(this).val();
				values[name] = $.isNumeric(value) ? parseFloat(value) : 0;
			}
		});

		return values;
	}

	/**
	 * Update summary display
	 */
	function updateSummary(wrapper, data) {
		const summaryTable = wrapper.find('.summary-table tbody');
		const summary = data.summary || {};
		const total = data.total || 0;

		// Clear existing summary rows (except total)
		summaryTable.find('tr:not(:last-child)').remove();

		// Add summary rows
		$.each(summary, function (label, value) {
			const row = $('<tr>')
				.html('<td>' + escapeHtml(label) + '</td><td class="summary-amount">' + formatAmount(value) + '</td>')
				.insertBefore(summaryTable.find('tr:last-child'));
		});

		// Update total
		summaryTable.find('.total-value span').text(formatCurrency(total));
	}

	/**
	 * Format currency
	 */
	function formatCurrency(value) {
		return parseFloat(value).toFixed(2);
	}

	/**
	 * Format summary amounts without currency symbol
	 */
	function formatAmount(value) {
		const number = parseFloat(value);

		if (!isFinite(number)) {
			return '0';
		}

		if (Number.isInteger(number)) {
			return String(number);
		}

		return number.toFixed(2).replace(/\.?0+$/, '');
	}

	/**
	 * Escape HTML
	 */
	function escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

})(jQuery);
