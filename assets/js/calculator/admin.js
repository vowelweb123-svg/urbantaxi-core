/**
 * Cost Calculator Admin JS
 */

(function ($) {
	'use strict';

	let fields = typeof calculatorFields !== 'undefined' ? calculatorFields : [];

	$(document).ready(function () {
		renderFieldsList();
		initEventHandlers();
		updateFormulaHints();
	});

	/**
	 * Initialize event handlers
	 */
	function initEventHandlers() {
		$(document).on('click', '#add-field-btn', openFieldModal);
		$(document).on('click', '#save-field-btn', saveField);
		$(document).on('change', '#field_type', onFieldTypeChange);
		$(document).on('click', '#add-option', addOptionInput);
		$(document).on('click', '.remove-option-btn', removeOptionInput);
		$(document).on('click', '.edit-field-btn', editField);
		$(document).on('click', '.delete-field-btn', deleteField);
		$(document).on('click', '.move-up-btn', moveFieldUp);
		$(document).on('click', '.move-down-btn', moveFieldDown);
		$(document).on('submit', '#calculator-form', saveCalculator);
		$(document).on('click', '#field-modal', closeModalOnBackdrop);
		$(document).on('keydown', closeModalOnEscape);
	}

	/**
	 * Open field modal
	 */
	function openFieldModal() {
		$('#field-form')[0].reset();
		$('#field_show_frontend').prop('checked', true);
		$('#save-field-btn').removeData('editing-index');
		$('#field_type').val('').trigger('change');
		$('#field-modal').show();
	}

	/**
	 * Handle field type change
	 */
	function onFieldTypeChange() {
		const type = $('#field_type').val();

		// Hide all special sections
		$('#field-options, #field-formula, #field-html, #field-min-max').hide();

		// Show relevant sections based on type
		if (['dropdown', 'radio', 'checkbox'].includes(type)) {
			$('#field-options').show();
			if ($('#options-list .option-item').length === 0) {
				addOptionInput();
			}
		}

		if (type === 'formula') {
			$('#field-formula').show();
		}

		if (type === 'html') {
			$('#field-html').show();
		}

		if (['number', 'quantity', 'slider'].includes(type)) {
			$('#field-min-max').show();
		}

		if (type === 'formula' && $('#save-field-btn').data('editing-index') === undefined) {
			$('#field_show_frontend').prop('checked', false);
		}
	}

	/**
	 * Add option input
	 */
	function addOptionInput() {
		const html = `
			<div class="option-item">
				<input type="text" placeholder="Label" class="option-label" />
				<input type="text" placeholder="Value" class="option-value" />
				<button type="button" class="button button-small remove-option-btn">Remove</button>
			</div>
		`;
		$('#options-list').append(html);
	}

	/**
	 * Remove option input
	 */
	function removeOptionInput(e) {
		e.preventDefault();
		$(e.target).closest('.option-item').remove();
	}

	/**
	 * Save field
	 */
	function saveField() {
		const type = $('#field_type').val();
		const label = ($('#field_label').val() || '').trim();
		const key = normalizeKey($('#field_key').val());

		$('#field_key').val(key);

		if (!type || !label || !key) {
			alert('Please fill in all required fields');
			return;
		}

		const field = {
			type: type,
			label: label,
			key: key,
			show_frontend: $('#field_show_frontend').is(':checked'),
			default_value: ($('#field_default_value').val() || '0').trim()
		};

		// Add type-specific fields
		if (['dropdown', 'radio', 'checkbox'].includes(type)) {
			field.options = [];
			$('.option-item').each(function () {
				const label = ($(this).find('.option-label').val() || '').trim();
				const value = ($(this).find('.option-value').val() || '').trim();
				if (label && value) {
					field.options.push({ label: label, value: value });
				}
			});

			if (field.options.length === 0) {
				alert('Please add at least one option');
				return;
			}
		}

		if (type === 'formula') {
			field.formula = ($('#field_formula').val() || '').trim();
			if (!field.formula) {
				alert('Please enter a formula');
				return;
			}
			field.summary_only = $('#field_summary_only').is(':checked');
		}

		if (type === 'html') {
			field.html = $('#field_html').val();
		}

		if (['number', 'quantity', 'slider'].includes(type)) {
			field.min = $('#field_min').val() || '0';
			field.max = $('#field_max').val() || '100';
		}

		if (type === 'text') {
			field.placeholder = $('#field_placeholder').val() || '';
		}

		// Check if editing existing field
		const editingIndex = $(this).data('editing-index');
		if (editingIndex !== undefined) {
			fields[editingIndex] = field;
			$(this).removeData('editing-index');
		} else {
			fields.push(field);
		}

		$('#field-modal').hide();
		renderFieldsList();
		updateFieldsInput();
		updateFormulaHints();
	}

	/**
	 * Edit field
	 */
	function editField(e) {
		e.preventDefault();
		const index = $(e.target).data('field-index');
		const field = fields[index];

		$('#field-form')[0].reset();
		$('#field_type').val(field.type).trigger('change');

		$('#field_label').val(field.label);
		$('#field_key').val(field.key);
		$('#field_show_frontend').prop('checked', field.show_frontend !== false);
		$('#field_default_value').val(field.default_value !== undefined ? field.default_value : '0');

		if (field.formula) {
			$('#field_formula').val(field.formula);
			$('#field_summary_only').prop('checked', field.summary_only === true);
		}

		if (field.html) {
			$('#field_html').val(field.html);
		}

		if (field.min !== undefined) {
			$('#field_min').val(field.min);
			$('#field_max').val(field.max);
		}

		if (field.options) {
			$('#options-list').html('');
			field.options.forEach(function (option) {
				$('#options-list').append(`
					<div class="option-item">
						<input type="text" placeholder="Label" class="option-label" value="${escapeHtml(option.label)}" />
						<input type="text" placeholder="Value" class="option-value" value="${escapeHtml(option.value)}" />
						<button type="button" class="button button-small remove-option-btn">Remove</button>
					</div>
				`);
			});
		}

		$('#save-field-btn').data('editing-index', index);
		$('#field-modal').show();
	}

	/**
	 * Delete field
	 */
	function deleteField(e) {
		e.preventDefault();
		if (confirm('Are you sure?')) {
			const index = $(e.target).data('field-index');
			fields.splice(index, 1);
			renderFieldsList();
			updateFieldsInput();
			updateFormulaHints();
		}
	}

	/**
	 * Move field up
	 */
	function moveFieldUp(e) {
		e.preventDefault();
		const index = $(e.target).data('field-index');
		if (index > 0) {
			[fields[index - 1], fields[index]] = [fields[index], fields[index - 1]];
			renderFieldsList();
			updateFieldsInput();
			updateFormulaHints();
		}
	}

	/**
	 * Move field down
	 */
	function moveFieldDown(e) {
		e.preventDefault();
		const index = $(e.target).data('field-index');
		if (index < fields.length - 1) {
			[fields[index], fields[index + 1]] = [fields[index + 1], fields[index]];
			renderFieldsList();
			updateFieldsInput();
			updateFormulaHints();
		}
	}

	/**
	 * Render fields list
	 */
	function renderFieldsList() {
		const list = $('#fields-list');
		list.html('');

		if (fields.length === 0) {
			list.html('<p style="color: #999;">No fields added yet. Click "Add Field" to start.</p>');
			return;
		}

		fields.forEach(function (field, index) {
			const visibilityBadge = field.show_frontend === false ? '<span class="field-item-visibility">Hidden on frontend</span>' : '';
			const summaryOnlyBadge = field.summary_only === true ? '<span class="field-item-visibility" style="background:#0d6efd;">Summary only</span>' : '';
			const html = `
				<div class="field-item">
					<div class="field-item-info">
						<span class="field-item-type">${escapeHtml(field.type)}</span>
						${visibilityBadge}
						${summaryOnlyBadge}
						<div class="field-item-label">${escapeHtml(field.label)}</div>
						<div class="field-item-key">{${escapeHtml(field.key)}}</div>
					</div>
					<div class="field-item-actions">
						<button type="button" class="button button-small edit-field-btn" data-field-index="${index}">Edit</button>
						<button type="button" class="button button-small delete-field-btn" data-field-index="${index}">Delete</button>
						${index > 0 ? `<button type="button" class="button button-small move-up-btn" data-field-index="${index}">↑</button>` : ''}
						${index < fields.length - 1 ? `<button type="button" class="button button-small move-down-btn" data-field-index="${index}">↓</button>` : ''}
					</div>
				</div>
			`;
			list.append(html);
		});
	}

	/**
	 * Update hidden fields input
	 */
	function updateFieldsInput() {
		$('#calculator_fields').val(JSON.stringify(fields));
	}

	/**
	 * Save calculator
	 */
	function saveCalculator(e) {
		updateFieldsInput();
		// Form will submit normally
	}

	function updateFormulaHints() {
		const hintBox = $('#formula-key-hints');
		if (!hintBox.length) {
			return;
		}

		const keys = fields
			.filter((field) => field && field.key)
			.map((field) => '{' + escapeHtml(field.key) + '}');

		if (keys.length === 0) {
			hintBox.html('<small>Add fields first. Their keys will be available for formulas here.</small>');
			return;
		}

		hintBox.html('<small>Available keys: ' + keys.join(', ') + '</small>');
	}

	function normalizeKey(value) {
		return (value || '')
			.toString()
			.trim()
			.toLowerCase()
			.replace(/[^a-z0-9_]/g, '_')
			.replace(/_+/g, '_')
			.replace(/^_+|_+$/g, '');
	}

	function closeModalOnBackdrop(e) {
		if (e.target && e.target.id === 'field-modal') {
			$('#field-modal').hide();
		}
	}

	function closeModalOnEscape(e) {
		if (e.key === 'Escape') {
			$('#field-modal').hide();
		}
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
