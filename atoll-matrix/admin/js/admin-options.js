jQuery(document).ready(function ($) {
	"use strict";

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

			if (idMappings.hasOwnProperty(id)) {
				$element.attr('id', idMappings[id]);
			}

			// Remove the 'disabled' attribute if present
			$element.prop('disabled', false);
		});

		// Apply sortable if needed
		if (applySortableFlag) {
			applySortable();
		}
	}


	// Add event listener to the "Add New Section" button
	$('#add-repeatable').click(function () {
		var idMappings = {
			'.option-label': 'option_' + getNextUniqueId() + '_label',
			'.option-number': 'option_' + getNextUniqueId() + '_number',
			'.option-type': 'option_' + getNextUniqueId() + '_type',
			'.option-duration': 'option_' + getNextUniqueId() + '_duration',
		};
		addRepeatableSection('.repeatable-template', '#repeatable-container', idMappings, true);

		return false;
	});

	$('#addperperson-repeatable').click(function () {
		var idMappings = {
			'.option-people': 'option_' + getNextUniqueId() + '_people',
			'.option-number': 'option_' + getNextUniqueId() + '_number',
			'.option-type': 'option_' + getNextUniqueId() + '_type',
			'.option-total': 'option_' + getNextUniqueId() + '_total',
		};
		addRepeatableSection('.repeatable-perperson-template', '#repeatable-perperson-container', idMappings, false);

		return false;
	});

	$('#addmealplan-repeatable').click(function () {
		var idMappings = {
			'.option-mealtype': 'option_' + getNextUniqueId() + '_mealtype',
			'.option-choice': 'option_' + getNextUniqueId() + '_choice',
			'.option-price': 'option_' + getNextUniqueId() + '_price',
		};
		addRepeatableSection('.repeatable-mealplan-template', '#repeatable-mealplan-container', idMappings, false);

		return false;
	});

	$(document).on('click', '.remove-set-button', function () {
		$(this).parent('.repeatable').remove(); // Remove the current repeatable section
	});


	// Function to get the next unique ID (similar to previous code)
	var uniqueIdCounter = 1;
	function getNextUniqueId() {
		return uniqueIdCounter++;
	}

});
