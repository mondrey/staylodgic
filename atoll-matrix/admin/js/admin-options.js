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

	// Add event listener to the "Add New Section" button
	$('#add-repeatable').click(function () {
		// Clone the repeatable template and append it to the container
		var $newRepeatable = $('.repeatable-template .repeatable').clone();
		$('#repeatable-container').append($newRepeatable);

		// Update unique IDs for the new elements (if required)
		$newRepeatable.find('.option-label').attr('id', 'option_' + getNextUniqueId() + '_label');
		$newRepeatable.find('.option-number').attr('id', 'option_' + getNextUniqueId() + '_number');
		$newRepeatable.find('.option-type').attr('id', 'option_' + getNextUniqueId() + '_type');
		$newRepeatable.find('.option-duration').attr('id', 'option_' + getNextUniqueId() + '_duration');
		$newRepeatable.find('.option-national').attr('id', 'option_' + getNextUniqueId() + '_national');

		// Create and add a "Remove" button to the new repeatable section
		var $removeButton = $('<span class="remove-repeatable"><i class="dashicons dashicons-remove"></i></span>');
		$newRepeatable.append($removeButton);

		// Add event listener to the "Remove" button
		$removeButton.click(function () {
			$newRepeatable.remove(); // Remove the current repeatable section when the "Remove" button is clicked
		});
		applySortable();
		return false;
	});

	// Function to get the next unique ID (similar to previous code)
	var uniqueIdCounter = 1;
	function getNextUniqueId() {
		return uniqueIdCounter++;
	}

	// Function to update the URL with the clicked tab's ID
	function updateUrlWithTabId(tabId) {
		const newUrl = window.location.origin + window.location.pathname + '?tab=' + tabId;
		window.history.pushState({ path: newUrl }, '', newUrl);
	}

	// Add click event listeners to each tab
	$('.atollmatrix-tabs .nav-tab').on('click', function () {
		const tabId = $(this).data('tab');
		updateUrlWithTabId(tabId);
	});

	// Check if the URL has a tab parameter on page load and activate the corresponding tab
	const urlParams = new URLSearchParams(window.location.search);
	const tabParam = urlParams.get('tab');
	if (tabParam) {
		const activeTab = $('.atollmatrix-tabs .nav-tab[data-tab="' + tabParam + '"]');
		if (activeTab.length > 0) {
			// Add a class to indicate the active tab (you can apply your own styles)
			activeTab.addClass('active');
		}
	}
});
