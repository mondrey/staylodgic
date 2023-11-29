jQuery(document).ready(function ($) {
	"use strict";

	$('.atollmatrix-options-form select').each(function () {
		// Fetch the data-width attribute value
		var widthAttribute = $(this).data('width');
		$(this).select2({
			width: widthAttribute || '300px' // Use the fetched value or set a default width
		});
	});

	$(document).on('click', function (event) {
		const target = event.target;
		if (!$(target).closest('.atollmatrix-tabs a').length) {
			return;
		}
		event.preventDefault();
		$('.atollmatrix-tabs a').removeClass('nav-tab-active');
		$(target).addClass('nav-tab-active');
		const targetTab = $(target).attr('data-tab');
		$('.atollmatrix-options-form .atollmatrix-tab-item').each(function () {
			if ($(this).hasClass(`atollmatrix-tab-item--${targetTab}`)) {
				$(this).css('display', 'block');
			} else {
				$(this).css('display', 'none');
			}
		});
	});

	$(document).ready(function () {
		$('.atollmatrix-tabs .nav-tab:first').click();
	});


	// Apply Sortable to the repeatable container
	function applySortable() {
		$('#repeatable-container').sortable();
	}
	applySortable();

	// Common function to add a new repeatable section
	function addRepeatableSection(templateSelector, containerSelector, idMappings, applySortableFlag) {
		// Clone the repeatable template and append it to the container
		var $newRepeatable = $(templateSelector + ' .repeatable').clone();
		$(containerSelector).append($newRepeatable);

		// Update unique IDs for the new elements (if required) and remove disabled attribute
		$newRepeatable.find('[id]').each(function () {
			var $element = $(this);
			var id = $element.attr('id');

			console.log(idMappings);
			if (idMappings.hasOwnProperty(id)) {
				$element.attr('id', idMappings[id]);
			}
			var template_name = $element.attr('name');
			$element.attr('name', idMappings['name'] + '[' + template_name + ']');

			// Remove the 'disabled' attribute if present
			$element.prop('disabled', false);
		});

		// Apply sortable if needed
		if (applySortableFlag) {
			applySortable();
		}
	}


	// Add event listener to the "Add New Section" button
	$('#addtax-repeatable').click(function () {
		var new_count = getNextUniqueId();
		var new_id = generateUniqueId();
		var idMappings = {
			'name': 'atollmatrix_settings[taxes][' + new_id + ']',
			'taxes_label': 'option_' + new_count + '_label',
			'taxes_number': 'option_' + new_count + '_number',
			'taxes_type': 'option_' + new_count + '_type',
			'taxes_duration': 'option_' + new_count + '_duration',
		};
		addRepeatableSection('.repeatable-tax-template', '#repeatable-tax-container', idMappings, true);

		return false;
	});

	$('#addperperson-repeatable').click(function () {
		var new_count = getNextUniqueId();
		var new_id = generateUniqueId();
		var idMappings = {
			'name': 'atollmatrix_settings[perpersonpricing][' + new_id + ']',
			'perpersonpricing_people': 'perpersonpricing_' + new_count + '_people',
			'perpersonpricing_number': 'perpersonpricing_' + new_count + '_number',
			'perpersonpricing_type': 'perpersonpricing_' + new_count + '_type',
			'perpersonpricing_total': 'perpersonpricing_' + new_count + '_total',
		};
		addRepeatableSection('.repeatable-perperson-template', '#repeatable-perperson-container', idMappings, false);

		return false;
	});

	$('#addmealplan-repeatable').click(function () {
		var new_count = getNextUniqueId();
		var new_id = generateUniqueId();
		var idMappings = {
			'name': 'atollmatrix_settings[mealplan][' + new_id + ']',
			'mealplan_mealtype': 'mealplan_' + new_count + '_mealtype',
			'mealplan_choice': 'mealplan_' + new_count + '_choice',
			'mealplan_price': 'mealplan_' + new_count + '_price',
		};
		addRepeatableSection('.repeatable-mealplan-template', '#repeatable-mealplan-container', idMappings, false);

		return false;
	});

	$(document).on('click', '.remove-set-button', function () {
		$(this).parent('.repeatable').remove(); // Remove the current repeatable section
	});

	function generateUniqueId() {
		const timestamp = Date.now().toString(); // Get current timestamp
		const randomNum = Math.floor(Math.random() * 10000); // Generate random number
		const uniqueId = timestamp + randomNum;
		return uniqueId;
	}

	// Function to get the next unique ID (similar to previous code)
	var uniqueIdCounter = 1;
	function getNextUniqueId() {
		return uniqueIdCounter++;
	}

});
