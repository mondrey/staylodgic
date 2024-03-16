jQuery(document).ready(function ($) {
	"use strict";

	var modal = document.getElementById("import-settings-modal");
	var btn = document.getElementById("import-settings-button");
	var span = document.getElementsByClassName("staylodgic-close")[0];

	btn.onclick = function() {
		modal.style.display = "block";
	}

	span.onclick = function() {
		modal.style.display = "none";
	}

	window.onclick = function(event) {
		if (event.target == modal) {
			modal.style.display = "none";
		}
	}

	$('.upload_image_button').click(function(e) {
		e.preventDefault();

		var button = $(this),
			custom_uploader = wp.media({
				title: 'Insert image',
				library : { type : 'image' },
				button: { text: 'Use this image' },
				multiple: false
			}).on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$(button).html('<img src="' + attachment.url + '" style="max-height:100px;display:block;">').next().val(attachment.id).next().show();
			}).open();
	});

	$('.remove_image_button').click(function(e) {
		e.preventDefault();
		$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
	});

	// $('.staylodgic-options-form select').each(function () {
	// 	// Fetch the data-width attribute value
	// 	var widthAttribute = $(this).data('width');
	// 	$(this).select2({
	// 		width: widthAttribute || '300px' // Use the fetched value or set a default width
	// 	});
	// });

	// function select_input_process() {
	// 	$('.staylodgic-options-form select').not('.select2-hidden-accessible').each(function () {
	// 		var widthAttribute = $(this).data('width') || 'style'; // 'style' uses the select's style attribute for width
	// 		$(this).select2({
	// 			width: widthAttribute
	// 		});
	// 	});
	// }
	

	$(document).on('click', function (event) {
		const target = event.target;
		if (!$(target).closest('.staylodgic-tabs a').length) {
			return;
		}
		event.preventDefault();
		$('.staylodgic-tabs a').removeClass('nav-tab-active');
		$(target).addClass('nav-tab-active');
		const targetTab = $(target).attr('data-tab');
		$('.staylodgic-options-form .staylodgic-tab-item').each(function () {
			if ($(this).hasClass(`staylodgic-tab-item--${targetTab}`)) {
				$(this).css('display', 'block');
			} else {
				$(this).css('display', 'none');
			}
		});
	});

	$(document).ready(function () {
		$('.staylodgic-tabs .nav-tab:first').click();
	});


	// Apply Sortable to the repeatable container
	function applySortable() {
		$('#repeatable-tax-container').sortable();
		$('#repeatable-activitytax-container').sortable();
	}
	applySortable();

	// Common function to add a new repeatable section
	function addRepeatableSection(templateSelector, containerSelector, idMappings, applySortableFlag) {
		// Clone the repeatable template and append it to the container
		var $newRepeatable = $(templateSelector + ' .repeatable').clone();
		$(containerSelector).append($newRepeatable);

		// Ensure new select elements are enabled
		$newRepeatable.find('select').prop('disabled', false);

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

		// Directly call select_input_process here to initialize Select2 for new elements
		// select_input_process();

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
			'name': 'staylodgic_settings[taxes][' + new_id + ']',
			'taxes_label': 'option_' + new_count + '_label',
			'taxes_number': 'option_' + new_count + '_number',
			'taxes_type': 'option_' + new_count + '_type',
			'taxes_duration': 'option_' + new_count + '_duration',
		};
		addRepeatableSection('.repeatable-tax-template', '#repeatable-tax-container', idMappings, true);

		return false;
	});
	
	// Add event listener to the "Add New Section" button
	$('#addtax-activity-repeatable').click(function () {
		var new_count = getNextUniqueId();
		var new_id = generateUniqueId();
		var idMappings = {
			'name': 'staylodgic_settings[activity_taxes][' + new_id + ']',
			'taxes_label': 'option_' + new_count + '_label',
			'taxes_number': 'option_' + new_count + '_number',
			'taxes_type': 'option_' + new_count + '_type',
			'taxes_duration': 'option_' + new_count + '_duration',
		};
		addRepeatableSection('.repeatable-activitytax-template', '#repeatable-activitytax-container', idMappings, true);

		return false;
	});

	$('#addperperson-repeatable').click(function () {
		var new_count = getNextUniqueId();
		var new_id = generateUniqueId();
		var idMappings = {
			'name': 'staylodgic_settings[perpersonpricing][' + new_id + ']',
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
			'name': 'staylodgic_settings[mealplan][' + new_id + ']',
			'mealplan_mealtype': 'mealplan_' + new_count + '_mealtype',
			'mealplan_choice': 'mealplan_' + new_count + '_choice',
			'mealplan_price': 'mealplan_' + new_count + '_price',
		};
		addRepeatableSection('.repeatable-mealplan-template', '#repeatable-mealplan-container', idMappings, false);

		// Re-initialize select2 for new select elements
		// select_input_process();

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
