(function ($) {
	$(document).ready(function () {

		// function updateCalendarCells() {
		// 	// For each td.calendarCell element...
		// 	$('td.calendarCell').each(function() {
		// 		// Find the .quantity-link element within this td.
		// 		var $quantityLink = $(this).find('.quantity-link');

		// 		// If the data-remaining attribute is 0...
		// 		if ($quantityLink.data('remaining') == 0) {
		// 			// Add your specific class to the td.
		// 			$(this).addClass('fully-booked');
		// 		}
		// 	});
		// }

		// updateCalendarCells();

	function showToast(toastId) {
		var toastEl = document.getElementById(toastId);
		var timeElement = toastEl.querySelector('.toast-time');
	
		// Update the time to "just now"
		timeElement.textContent = 'just now';
	
		var toast = new bootstrap.Toast(toastEl);
		toast.show();
	}

	function markToday() {
		// Find the index of the .is-today cell in the second row
		var todayIndex = $('#calendarTable .calendarRow:eq(1) .calendarCell.monthHeader.is-today').index();
	
		// Check if .is-today was found
		if (todayIndex !== -1) {
			// Add the .is-today class to all cells in the same column
			$('#calendarTable .calendarRow').each(function() {
				$(this).find('.calendarCell:eq(' + todayIndex + ')').addClass('is-today');
			});
		}
	}
	markToday();
	
	function generateOpacityforRemainingRooms() {
		// Find the maximum number of rooms
		var maxRooms = 0;
		$('#calendarTable .monthHeader.occupancy-stats').each(function() {
			var roomsRemaining = parseInt($(this).data('roomsremaining'));
			if (roomsRemaining > maxRooms) {
				maxRooms = roomsRemaining;
			}
		});
	
		// Apply opacity based on the remaining rooms
		$('#calendarTable .monthHeader.occupancy-stats').each(function() {
			var roomsRemaining = parseInt($(this).data('roomsremaining'));
			var opacity;
			if (roomsRemaining === 0) {
				opacity = 0.6; // Set opacity to 0.6 for 0 remaining rooms
			} else {
				// Scale opacity from 0.4 (for 1 room remaining) to 1 (for maxRooms remaining)
				opacity = 0.3 * (roomsRemaining - 1) / (maxRooms - 1) + 0.7;
			}
			if ( maxRooms == 1 ) {
				opacity = 1;
			}
			if ( maxRooms == 0 ) {
				opacity = 1;
			}
			// Generate a random duration between 500 and 1500 milliseconds
			var randomDuration = Math.floor(Math.random() * (1500 - 500 + 1)) + 500;
			
			// Animate the opacity change using Velocity.js with a random duration
			$(this).velocity({ opacity: opacity }, { duration: randomDuration });
		});
	}
	generateOpacityforRemainingRooms();

		function runCalendarAnimation() {
			$('#calendarTable .calendarRow').each(function (rowIndex) {
				var row = $(this);
				row.find('.reserved-tab-inner').each(function () {
					var tabWidth = $(this).data('tabwidth');
					$(this).css('opacity', 0)
						.velocity({
							width: tabWidth,
							opacity: 1
						}, {
							duration: 100,
							easing: "swing",
							delay: (Math.random() * 170) * rowIndex // Random delay between 0 and 100
						});
				});
			});
		}
		runCalendarAnimation();

		function initializeTooltips() {
			$('.reserved-tab-with-info').each(function () {
				var guest = $(this).data('guest');
				var room = $(this).data('room');
				var reservationid = $(this).data('reservationid');
				var bookingnumber = $(this).data('bookingnumber');
				var checkin = $(this).data('checkin');
				var checkout = $(this).data('checkout');
				var tooltipContent = guest + '<br/>' + bookingnumber + '<br/>Check-in: ' + checkin + '<br/>Check-out: ' + checkout;

				$(this).attr('data-bs-toggle', 'tooltip');
				$(this).attr('data-bs-html', 'true'); // Allow HTML content in the tooltip
				$(this).attr('title', tooltipContent);
			});

			var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl, {
					animation: true,
					delay: { "show": 1000, "hide": 100 }
				});
			});
		}
		// Initially initialize tooltips
		initializeTooltips();

		var quantityModalDatepicker; // Declare a variable to hold the instance
		var ratesModalDatepicker; // Declare a variable to hold the instance

		if ($.fn.flatpickr) {
			// Initialize Flatpickr
			quantityModalDatepicker = $('#quantity-modal .modaldatepicker').flatpickr(
				{
					mode: "range",
					showMonths: 2,
					dateFormat: "Y-m-d",
					enableTime: false,
					onClose: function (selectedDates, dateStr, instance) {
						console.log(selectedDates);
						if (selectedDates.length == 1) {
							instance.setDate([selectedDates[0], selectedDates[0]], true);
						}
					}
				}
			);

			// Initialize Flatpickr
			ratesModalDatepicker = $('#rates-modal .modaldatepicker').flatpickr(
				{
					mode: "range",
					showMonths: 2,
					dateFormat: "Y-m-d",
					enableTime: false,
					onClose: function (selectedDates, dateStr, instance) {
						console.log(selectedDates);
						if (selectedDates.length == 1) {
							instance.setDate([selectedDates[0], selectedDates[0]], true);
						}
					}
				}
			);
		}

		$('#calendar-booking-status').change(function() {
			// Get the switch value (true if checked, false otherwise)
			var confirmedOnly = $(this).is(':checked') ? 1 : 0;

			var staylodgic_availabilitycalendar_nonce = $('input[name="staylodgic_availabilitycalendar_nonce"]').val();
	
			// Send an AJAX request to update the option
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'update_availDisplayConfirmedStatus',
					confirmed_only: confirmedOnly,
					staylodgic_availabilitycalendar_nonce: staylodgic_availabilitycalendar_nonce
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						// Option updated successfully
						// Update the calendar without reloading the page
						var currentDate = fp.selectedDates[0]; // Assuming fp is your flatpickr instance
						var startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
						var endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 5);
						debouncedCalendarUpdate([startDate, endDate]);
					} else {
						// Handle failure
						console.error('Failed to update option');
					}

				},
				error: function(xhr, status, error) {
					// Handle AJAX errors
					console.error('AJAX error: ' + error);
				}
			});
		});

		// Handle click event on the "Save changes" button
		$('#rates-modal .save-changes').click(function () {
			var dateRange = $('#rates-modal input[name="modaldatepicker"]').val();
			var rate = $('#rates-modal input[name="rate"]').val();
			var postID = $('#rates-modal select[name="room"]').val();
			
			var save_button = $('#rates-modal .save-changes');
			save_button.find('.spinner-border').css('opacity', '1');
			save_button.prop('disabled', true);

			var staylodgic_availabilitycalendar_nonce = $('input[name="staylodgic_availabilitycalendar_nonce"]').val();

			console.log(ajaxurl, dateRange, rate, postID);

			// Perform AJAX request
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'update_RoomRate',
					postID: postID,
					dateRange: dateRange,
					rate: rate,
					staylodgic_availabilitycalendar_nonce: staylodgic_availabilitycalendar_nonce
				},
				success: function (response) {
					// Handle the AJAX response here
					if (response.success) {
						// Metadata stored successfully
						console.log(response.data.message);

						// Close the modal
						$('#rates-modal').modal('hide');
						
						save_button.find('.spinner-border').css('opacity', '0');
						save_button.prop('disabled', false);
						// Update the calendar without reloading the page
						var currentDate = fp.selectedDates[0]; // Assuming fp is your flatpickr instance
						var startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
						var endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 5);
						debouncedCalendarUpdate([startDate, endDate]);

						showToast('rateToast');

					} else {
						
						$('#rates-modal').modal('hide');
						
						save_button.find('.spinner-border').css('opacity', '0');
						save_button.prop('disabled', false);

						showToast('rateToastFail');
					}
				},
				error: function (xhr, status, error) {
					// Handle AJAX error here
					console.error(error);
				}
			});
		});

		// Handle click event on quantity link
		$(document).on('click', '.roomrate-link', function (e) {
			e.preventDefault();
			var date = $(this).data('date');
			var room = $(this).data('room');
			var rate = $(this).data('rate');

			// Open the modal with the selected date and room
			openRoomRateModal(date, room, rate);
		});

		// Function to open the modal with the selected date and room
		function openRoomRateModal(date, room, rate) {
			// Set the date and room values in the modal inputs
			$('#rates-modal input[name="modaldatepicker"]').val(date);
			$('#rates-modal input[name="rate"]').val(rate);
			$('#rates-modal select[name="room"]').val(room);
			
			// Use the stored Flatpickr instance to set the date
			if (date && ratesModalDatepicker) {
				ratesModalDatepicker.setDate(date, true); // The 'true' ensures the calendar and input display the date
			}
			
			// Open the modal
			$('#rates-modal').modal('show');
		}

		// Handle click event on quantity link
		$(document).on('click', '.quantity-link', function (e) {
			e.preventDefault();
			var date = $(this).data('date');
			var room = $(this).data('room');
			var remaining = $(this).data('remaining');

			// Open the modal with the selected date and room
			openQuantityModal(date, room, remaining);
		});

		// Function to open the modal with the selected date and room
		function openQuantityModal(date, room, remaining) {
			// Set the date and room values in the modal inputs
			$('#quantity-modal input[name="modaldatepicker"]').val(date);
			$('#quantity-modal input[name="quantity"]').val(remaining);
			$('#quantity-modal select[name="room"]').val(room);

			// Use the stored Flatpickr instance to set the date
			if (date && quantityModalDatepicker) {
				quantityModalDatepicker.setDate(date, true); // The 'true' ensures the calendar and input display the date
			}
			
			// Open the modal
			$('#quantity-modal').modal('show');
		}

		// Handle click event on the link that triggers the popup
		$('#quantity-modal-link').click(function (e) {
			e.preventDefault();

			// Show the popup
			$('#quantity-modal').modal('show');

			// Focus on the input field inside the popup
			$('#quantity-modal input').focus();
		});

		// Handle click event on the "Save changes" button
		$('#quantity-modal .save-changes').click(function () {
			var dateRange = $('#quantity-modal input[name="modaldatepicker"]').val();
			var quantity = $('#quantity-modal input[name="quantity"]').val();
			var postID = $('#quantity-modal select[name="room"]').val();

			var save_button = $('#quantity-modal .save-changes');
			save_button.find('.spinner-border').css('opacity', '1');
			save_button.prop('disabled', true);

			var staylodgic_availabilitycalendar_nonce = $('input[name="staylodgic_availabilitycalendar_nonce"]').val();

			console.log(ajaxurl, dateRange, quantity, postID);

			// Perform AJAX request
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'update_RoomAvailability',
					postID: postID,
					dateRange: dateRange,
					quantity: quantity,
					staylodgic_availabilitycalendar_nonce: staylodgic_availabilitycalendar_nonce
				},
				success: function (response) {
					// Handle the AJAX response here
					if (response.success) {
						// Metadata stored successfully
					// Close the modal
						$('#quantity-modal').modal('hide');

						save_button.find('.spinner-border').css('opacity', '0');
						save_button.prop('disabled', false);
						// Update the calendar without reloading the page
						var currentDate = fp.selectedDates[0]; // Assuming fp is your flatpickr instance
						var startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
						var endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 5);
						debouncedCalendarUpdate([startDate, endDate]);

						showToast('quantityToast');
					} else {
						// Error storing metadata
						// Close the modal
						$('#quantity-modal').modal('hide');

						save_button.find('.spinner-border').css('opacity', '0');
						save_button.prop('disabled', false);

						showToast('quantityToastFail');
					}

				},
				error: function (xhr, status, error) {
					// Handle AJAX error here
					console.error(error);
				}
			});
		});

		if ($.fn.flatpickr) {

			var room_related_input_fields = $('#reservation-details,.metabox-fields.room_choice,.metabox-fields.reservation_status,.metabox-fields.metaboxtype_bedlayout,.metabox-fields.metaboxtype_mealplan_included,.metabox-fields.reservation_meals,.metabox-fields.metaboxtype_currency,.metabox-fields.metaboxtype_taxgenerate,.metabox-fields.metaboxtype_currencyarray');

			$('.datepicker').flatpickr();

			function initialize_reservation_guest_amount() {

				$('#staylodgic_room_id').change(function () {


					$('.notify-number-over-max').hide();

					var selectedValue = $(this).val(); // Get the selected room value
					var occupantsData = $('.occupants-range').attr('data-occupants'); // Get the data-occupants attribute
					var occupantsObject = JSON.parse(occupantsData); // Parse the JSON string into an object


					var bedLayoutField = $('#metabox-bedlayout').attr('data-field');
					var bedMetaValue = $('#metabox-bedlayout').attr('data-metavalue');
					// Change Bedlayout for chosen room

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'generate_BedMetabox',
							roomID: selectedValue,
							fieldID: bedLayoutField,
							metaValue: bedMetaValue,
							nonce: staylodgic_admin_vars.nonce
						},
						success: function (response) {
							// Handle the AJAX response here
							if (response.success) {
								// Metadata stored successfully
								console.log(response.data.message);
							} else {
								// Error storing metadata
								$('#metabox-bedlayout').html(response);
							}
						},
						error: function (xhr, status, error) {
							// Handle AJAX error here
							console.error(error);
						}
					});

					// Adjust Guest max count for occupants
					// Check if the selected room ID exists in the occupants object
					if (occupantsObject.hasOwnProperty(selectedValue)) {

						var $adultInput = $('#staylodgic_reservation_room_adults');

						var adult_number = $('#staylodgic_reservation_room_adults').val();

						// Get the max guests for the selected room
						var maxGuests = occupantsObject[selectedValue]["max_guests"];

						// Determine whether to use max_adults or max_children for adults
						var maxValueAdults = occupantsObject[selectedValue]['max_adults'];

						// Determine whether to use max_adults or max_children for children
						var maxValueChildren = occupantsObject[selectedValue]['max_children'];

						$adultInput.attr("data-guestmax", maxGuests);
						$adultInput.attr("data-adultmax", maxValueAdults);
						$adultInput.attr("data-childmax", maxValueChildren);
						// If max_adults value is 0, use maxGuests value
						//maxValueAdults = (maxValueAdults == '0') ? maxGuests : maxValueAdults;
						console.log(maxValueAdults);
						if ('disabled' == maxValueAdults) {
							$adultInput.attr('data-min', '1');
							$adultInput.attr('data-max', maxGuests);
							$adultInput.attr('data-guestmax', maxGuests);
							$('.adult-number-max-notice').hide();
							$('.combined-adult-number-max-notice').show();
							$('.combined-adult-number-max').show().text(maxGuests);

							if (adult_number > maxGuests) {
								$('.occupant-adult-notify.notify-number-over-max').show();
							}
						} else {
							$adultInput.attr('data-min', '1');
							$adultInput.attr('data-max', maxValueAdults);
							$adultInput.attr('data-guestmax', maxGuests);
							$('.combined-adult-number-max-notice').hide();
							$('.adult-number-max-notice').show();
							$('.adult-number-max').show().text(maxValueAdults);

							if (adult_number > maxValueAdults) {
								$('.occupant-adult-notify.notify-number-over-max').show();
							}
						}

						// Update the max attribute and the jQuery UI slider for adults



						//$adultInput.val('1');



						var $childInput = $('#staylodgic_reservation_room_children');

						var children_number = $childInput.val();

						$childInput.attr("data-guestmax", maxGuests);
						$childInput.attr("data-adultmax", maxValueAdults);
						$childInput.attr("data-childmax", maxValueChildren);
						// If max_children value is 0, use maxGuests value
						//maxValueChildren = (maxValueChildren == '0') ? maxGuests : maxValueChildren;

						if ('disabled' == maxValueChildren) {
							$childInput.attr('data-min', '0');
							$childInput.attr('data-max', maxGuests);
							$childInput.attr('data-guestmax', maxGuests);
							$('.child-number-max-notice').hide();
							$('.combined-child-number-max-notice').show();
							$('.combined-child-number-max').show().text(maxGuests);

							if (children_number > maxGuests) {
								$('.occupant-child-notify.notify-number-over-max').show();
							}
						} else {
							$childInput.attr('data-min', '0');
							$childInput.attr('data-max', maxValueChildren);
							$childInput.attr('data-guestmax', maxGuests);
							$('.combined-child-number-max-notice').hide();
							$('.child-number-max-notice').show();
							$('.child-number-max').text(maxValueChildren);

							if (children_number > maxValueChildren) {
								$('.occupant-child-notify.notify-number-over-max').show();
							}
						}

						// Update the max attribute and the jQuery UI slider for children

						//$childInput.val('0');

						// Update the text in the ranger-max-value span

					}
				});

			}

			initialize_reservation_guest_amount();

			function getExistingDates() {
				let checkinValue = document.getElementById('staylodgic_checkin_date') ? document.getElementById('staylodgic_checkin_date').value : null;
				let checkoutValue = document.getElementById('staylodgic_checkout_date') ? document.getElementById('staylodgic_checkout_date').value : null;

				// Only set the default dates if checkinValue and checkoutValue exist
				let defaultDates = [];
				if (checkinValue && checkoutValue) {
					defaultDates = [checkinValue, checkoutValue];
				}
				return defaultDates;
			}

			function handleDateChange(selectedDates, instance) {
				const checkin = selectedDates[0];
				let checkout;

				let selectElement = $('#staylodgic_room_id');
				selectElement.prop('disabled', true);

				if (selectedDates.length > 1) {
					checkout = selectedDates[1];
				}
				let reservationID = staylodgic_admin_vars.post_id;
				console.log(reservationID);
				const roomNights = checkout ? Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)) : 0;

				const reservationDetails = document.getElementById("reservation-details");
				if (checkin && checkout) {
					reservationDetails.innerHTML = `
					<p class="reservation-post-checkin-date">Checkin: ${checkin.toLocaleDateString()}</p>
					${checkout ? `<p class="reservation-post-checkout-date">Checkout: ${checkout.toLocaleDateString()}</p>` : ''}
					<p data-numberofnights="${roomNights}" class="reservation-post-numberof-nights">Room nights: ${roomNights}</p>
					`;
				}

				// Set the values of the hidden input fields
				if (checkin && checkout) {
					const checkinOffset = checkin.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
					const checkoutOffset = checkout.getTimezoneOffset() * 60000; // Time zone offset in milliseconds

					document.getElementById("staylodgic_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
					document.getElementById("staylodgic_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];
				}

				// Availability checking to see if the chosen range has rooms available for the dates
				const checkinOffset = checkin ? checkin.getTimezoneOffset() * 60000 : 0;
				const checkoutOffset = checkout ? checkout.getTimezoneOffset() * 60000 : 0;

				if (checkin && checkout) {
					var data = {
						'action': 'get_AvailableRooms',
						'reservationid': reservationID,
						'checkin': new Date(checkin - checkinOffset).toISOString().split('T')[0],
						'checkout': new Date(checkout - checkoutOffset).toISOString().split('T')[0]
					};

					jQuery.post(ajaxurl, data, function (response) {
						let selectedValue = selectElement.val(); // Save the currently selected value

						// store the value to guest number selectors
						$('.occupants-range').attr('data-room', selectedValue);

						selectElement.empty();

						var available_rooms = JSON.parse(response);

						if (available_rooms.length === 0) {
							// Handle the case when there are no available rooms
							console.log("No available rooms."); // You can replace this with your desired action
							room_related_input_fields.hide();
						} else {
							room_related_input_fields.show();
							$.each(available_rooms, function (key, value) {
								if (value) {
									let optionElement = `<option value="${key}" ${(key === selectedValue) ? 'selected' : ''}>${value}</option>`;
									selectElement.append(optionElement);
								}
							});

							// Enable selectElement after the response is processed
							selectElement.prop('disabled', false);

							// Trigger update
							selectElement.trigger('change');

							room_related_input_fields.show();
						}
					});

				}
			}

			let defaultDates = getExistingDates();
			const flatpickrInstance = flatpickr(".reservation", {
				mode: "range",
				showMonths: 2,
				dateFormat: "Y-m-d",
				defaultDate: defaultDates,
				enableTime: false,
				onChange: handleDateChange,
				onReady: function (selectedDates, dateStr, instance) {
					handleDateChange(selectedDates, instance); // Call the handleDateChange function manually
					// calculate room nights and display reservation details for existing reservation
					var dateRangeInput = instance.input;
					var dateRangeValue = dateRangeInput.value;
					var dateRangeParts = dateRangeValue.split(" to ");
					var checkin = new Date(dateRangeParts[0]);
					var checkout = new Date(dateRangeParts[1]);
					var roomNights = (checkout - checkin) / (1000 * 60 * 60 * 24);

					// Set the values of the hidden input fields
					if (checkin && !isNaN(Date.parse(checkin)) && checkout && !isNaN(Date.parse(checkout))) {
						const checkinOffset = checkin.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
						const checkoutOffset = checkout.getTimezoneOffset() * 60000; // Time zone offset in milliseconds

						document.getElementById("staylodgic_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
						document.getElementById("staylodgic_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];

					}

					var reservationDetails = '<p class="reservation-post-checkin-date">Check-in: ' + checkin.toLocaleDateString() + '</p>' +
						'<p class="reservation-post-checkout-date">Checkout: ' + checkout.toLocaleDateString() + '</p>' +
						'<p data-numberofnights="' + roomNights + '" class="reservation-post-numberof-nights">Room nights: ' + roomNights + "</p>";
					document.getElementById("reservation-details").innerHTML = reservationDetails;
				}
			});

			// Availablity Calendar
			var calendarTable = $('#calendarTable');

			// Extract the start and end date from the data attributes
			var startDate = calendarTable.data('calstart');
			var endDate = calendarTable.data('calend');

			var debouncedCalendarUpdate = _.debounce(updateCalendarData, 500);  // Wait for 300ms of inactivity

			var fp;

			function initializeFlatpickr(newStartDate, newEndDate) {
				if (typeof monthSelectPlugin !== 'undefined') {
					fp = flatpickr(".availabilitycalendar", {
						mode: "single", // Change to single mode for month selection
						plugins: [
							new monthSelectPlugin({
								shorthand: true,
								dateFormat: "Y-m",
								altFormat: "F Y",
								theme: "light"
							})
						],
						defaultDate: newStartDate, // Set the defaultDate to the start date only
						onChange: function (selectedDates, dateStr, instance) {
							if (selectedDates.length > 0) {
								var selectedMonth = selectedDates[0].getMonth();
								var selectedYear = selectedDates[0].getFullYear();
				
								// Create the start date (1st of the selected month)
								var startDate = new Date(selectedYear, selectedMonth, 1);
				
								// Create the end date (5th of the next month)
								var endDate = new Date(selectedYear, selectedMonth + 1, 5);
				
								// Update the calendar data with the constructed range
								updateCalendarData([startDate, endDate]);
							}
						}
					});
				}
			}
			
			function shiftDates(buttonId, months) {
				$(buttonId).click(function () {

					var currentDate = fp.selectedDates[0];
					var newMonth = currentDate.getMonth() + months;
					var newYear = currentDate.getFullYear();
			
					if (newMonth < 0) {
						newMonth = 11;
						newYear -= 1;
					} else if (newMonth > 11) {
						newMonth = 0;
						newYear += 1;
					}
			
					var newStartDate = new Date(newYear, newMonth, 1);
					var newEndDate = new Date(newYear, newMonth + 1, 5);
			
					// Destroy the current instance
					fp.destroy();
			
					// Initialize a new flatpickr instance with the new dates
					initializeFlatpickr(newStartDate, newEndDate);
			
					debouncedCalendarUpdate([newStartDate, newEndDate]);
				});
			}
			shiftDates('#prevmonth:not(".disabled")', -1);
			shiftDates('#nextmonth:not(".disabled")', 1);

			// Call the initialize function with the initial dates
			initializeFlatpickr(startDate, endDate);

			function updateCalendarData(selectedDates) {
				var start_date = selectedDates[0].toLocaleDateString('en-US').substr(0, 10);
				var end_date = selectedDates[1].toLocaleDateString('en-US').substr(0, 10);
				console.log(start_date, end_date, ajaxurl);

				$('.preloader-element').velocity('fadeIn');
				$('.calendar-nav-buttons').addClass('disabled');
				$('.availabilitycalendar').addClass('disabled');

				var staylodgic_availabilitycalendar_nonce = $('input[name="staylodgic_availabilitycalendar_nonce"]').val();

				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						'action': 'get_Selected_Range_AvailabilityCalendar',
						'start_date': start_date,
						'end_date': end_date,
						staylodgic_availabilitycalendar_nonce: staylodgic_availabilitycalendar_nonce
					},
					success: function (data) {
						$('#calendar').html(data);
						runCalendarAnimation();
						// After updating the content, initialize the tooltips again
						initializeTooltips();
						$('.preloader-element').velocity('fadeOut');
						$('.calendar-nav-buttons').removeClass('disabled');
						$('.availabilitycalendar').removeClass('disabled');

						generateOpacityforRemainingRooms();
						markToday();
						showToast('calendarToast');
					},
					error: function (data) {
						alert('Error: Unable to retrieve calendar data.');
					}
				});
			}

			function get_parameter_settings_on_new_reservation_post() {
				var urlParams = new URLSearchParams(window.location.search);
				var createFromDate = urlParams.get('createfromdate');
				var createToDate = urlParams.get('createtodate');
				var roomID = urlParams.get('roomID');
			
				if (createFromDate && createToDate) {
					// Parse the dates
					var startDate = flatpickr.parseDate(createFromDate, "Y-m-d");
					var endDate = flatpickr.parseDate(createToDate, "Y-m-d");
			
					// Set the dates in the flatpickr instance
					flatpickrInstance.setDate([startDate, endDate]);
			
					// Manually trigger the handleDateChange function
					handleDateChange([startDate, endDate], flatpickrInstance);
			
					// Show the room-related input fields
					room_related_input_fields.show();
			
					// Enable the select input and set the selected option
					$('#staylodgic_room_id').prop('disabled', false).val(roomID).trigger('change.select2');
				} else if ('' == $('.reservation').val()) {
					room_related_input_fields.hide();
				}
			}
			get_parameter_settings_on_new_reservation_post();

			// Check if it's a new post page for a specific post type
			if ($('body').hasClass('post-new-php') && $('input[name="post_type"]').val() === 'slgc_reservations') {
				// Listen for the publish button click event
				$('#publish').click(function() {
					// Check if the title is empty
					if ($('#title').val().trim() === '') {
						// Set the title dynamically
						var dynamicTitle = $('#staylodgic_booking_number').val();
						$('#title').val(dynamicTitle);
					}
				});
			}

		}

	});
})(jQuery);
