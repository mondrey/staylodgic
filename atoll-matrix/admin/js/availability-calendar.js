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

		function runCalendarAnimation() {
			$('#calendarTable .calendarRow').each(function(rowIndex) {
				var row = $(this);
				row.find('.reserved-tab-inner').each(function() {
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
				var tooltipContent = 'Guest: ' + guest + '<br><br/>Room: ' + room + '<br><br/>Booking Number:<br/>' + bookingnumber + '<br><br/>Check-in: ' + checkin + '<br>Check-out: ' + checkout;
		
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


		if ($.fn.flatpickr) {
			// Initialize Flatpickr
			$('#quantity-popup .modaldatepicker').flatpickr(
				{
					mode: "range",
					showMonths: 2,
					dateFormat: "Y-m-d",
					enableTime: false,
					onClose: function(selectedDates, dateStr, instance) {
						console.log( selectedDates );
						if(selectedDates.length == 1){
							instance.setDate([selectedDates[0],selectedDates[0]], true);
						}
					}
				}
			);

			// Initialize Flatpickr
			$('#rates-popup .modaldatepicker').flatpickr(
				{
					mode: "range",
					showMonths: 2,
					dateFormat: "Y-m-d",
					enableTime: false,
					onClose: function(selectedDates, dateStr, instance) {
						console.log( selectedDates );
						if(selectedDates.length == 1){
							instance.setDate([selectedDates[0],selectedDates[0]], true);
						}
					}
				}
			);
		}

		// Handle click event on the "Save changes" button
		$('#rates-popup .save-changes').click(function() {
			var dateRange = $('#rates-popup input[name="modaldatepicker"]').val();
			var rate = $('#rates-popup input[name="rate"]').val();
			var postID = $('#rates-popup select[name="room"]').val();
	
			console.log( ajaxurl,dateRange,rate,postID);
	
			// Perform AJAX request
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'update_RoomRate',
					postID: postID,
					dateRange: dateRange,
					rate: rate
				},
				success: function(response) {
					// Handle the AJAX response here
					if (response.success) {
						// Metadata stored successfully
						console.log(response.data.message);
					} else {
						// Error storing metadata
						console.error(response.data.message);
					}
	
					// Close the modal
					$('#rates-popup').modal('hide');
					location.reload();
				},
				error: function(xhr, status, error) {
					// Handle AJAX error here
					console.error(error);
				}
			});
		});

		// Handle click event on quantity link
		$(document).on('click', '.roomrate-link', function(e) {
			e.preventDefault();
			var date = $(this).data('date');
			var room = $(this).data('room');
			
			// Open the modal with the selected date and room
			openRoomRateModal(date, room);
		});

		// Function to open the modal with the selected date and room
		function openRoomRateModal(date, room) {
			// Set the date and room values in the modal inputs
			$('#rates-popup input[name="modaldatepicker"]').val(date);
			$('#rates-popup select[name="room"]').val(room);
			
			// Open the modal
			$('#rates-popup').modal('show');
		}
	
		// Handle click event on quantity link
		$(document).on('click', '.quantity-link', function(e) {
			e.preventDefault();
			var date = $(this).data('date');
			var room = $(this).data('room');
			
			// Open the modal with the selected date and room
			openQuantityModal(date, room);
		});
	
		// Function to open the modal with the selected date and room
		function openQuantityModal(date, room) {
			// Set the date and room values in the modal inputs
			$('#quantity-popup input[name="modaldatepicker"]').val(date);
			$('#quantity-popup select[name="room"]').val(room);
			
			// Open the modal
			$('#quantity-popup').modal('show');
		}
	
		// Handle click event on the link that triggers the popup
		$('#quantity-popup-link').click(function(e) {
			e.preventDefault();
	
			// Show the popup
			$('#quantity-popup').modal('show');
	
			// Focus on the input field inside the popup
			$('#quantity-popup input').focus();
		});
	
		// Handle click event on the "Save changes" button
		$('#quantity-popup .save-changes').click(function() {
			var dateRange = $('#quantity-popup input[name="modaldatepicker"]').val();
			var quantity = $('#quantity-popup input[name="quantity"]').val();
			var postID = $('#quantity-popup select[name="room"]').val();
	
			console.log( ajaxurl,dateRange,quantity,postID);
	
			// Perform AJAX request
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'update_RoomAvailability',
					postID: postID,
					dateRange: dateRange,
					quantity: quantity
				},
				success: function(response) {
					// Handle the AJAX response here
					if (response.success) {
						// Metadata stored successfully
						console.log(response.data.message);
					} else {
						// Error storing metadata
						console.error(response.data.message);
					}
	
					// Close the modal
					$('#quantity-popup').modal('hide');
					location.reload();
				},
				error: function(xhr, status, error) {
					// Handle AJAX error here
					console.error(error);
				}
			});
		});
	
		if ($.fn.flatpickr) {
	
			$('.datepicker').flatpickr();

		function initialize_reservation_guest_amount() {
			
			$('#atollmatrix_room_id').change(function() {

				$('.notify-number-over-max').hide();

				var selectedValue = $(this).val(); // Get the selected room value
				var occupantsData = $('.occupants-range').attr('data-occupants'); // Get the data-occupants attribute
				var occupantsObject = JSON.parse(occupantsData); // Parse the JSON string into an object
			
				// Check if the selected room ID exists in the occupants object
				if (occupantsObject.hasOwnProperty(selectedValue)) {

					var $adultInput = $('#atollmatrix_reservation_room_adults');

					var adult_number = $('#atollmatrix_reservation_room_adults').val();

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
					console.log( maxValueAdults );
					if ( 'disabled' == maxValueAdults ) {
						$adultInput.attr('data-min', '1');
						$adultInput.attr('data-max', maxGuests);
						$adultInput.attr('data-guestmax', maxGuests);
						$('.adult-number-max-notice').hide();
						$('.combined-adult-number-max-notice').show();
						$('.combined-adult-number-max').show().text(maxGuests);

						if ( adult_number > maxGuests ) {
							$('.occupant-adult-notify.notify-number-over-max').show();
						}
					} else {
						$adultInput.attr('data-min', '1');
						$adultInput.attr('data-max', maxValueAdults);
						$adultInput.attr('data-guestmax', maxGuests);
						$('.combined-adult-number-max-notice').hide();
						$('.adult-number-max-notice').show();
						$('.adult-number-max').show().text(maxValueAdults);

						if ( adult_number > maxValueAdults ) {
							$('.occupant-adult-notify.notify-number-over-max').show();
						}
					}
			
					// Update the max attribute and the jQuery UI slider for adults
					
					

					//$adultInput.val('1');
			


					var $childInput = $('#atollmatrix_reservation_room_children');

					var children_number = $childInput.val();

					$childInput.attr("data-guestmax", maxGuests);
					$childInput.attr("data-adultmax", maxValueAdults);
					$childInput.attr("data-childmax", maxValueChildren);
					// If max_children value is 0, use maxGuests value
					//maxValueChildren = (maxValueChildren == '0') ? maxGuests : maxValueChildren;

					if ( 'disabled' == maxValueChildren ) {
						$childInput.attr('data-min', '0');
						$childInput.attr('data-max', maxGuests);
						$childInput.attr('data-guestmax', maxGuests);
						$('.child-number-max-notice').hide();
						$('.combined-child-number-max-notice').show();
						$('.combined-child-number-max').show().text(maxGuests);

						if ( children_number > maxGuests ) {
							$('.occupant-child-notify.notify-number-over-max').show();
						}
					} else {
						$childInput.attr('data-min', '0');
						$childInput.attr('data-max', maxValueChildren);
						$childInput.attr('data-guestmax', maxGuests);
						$('.combined-child-number-max-notice').hide();
						$('.child-number-max-notice').show();
						$('.child-number-max').text(maxValueChildren);
						
						if ( children_number > maxValueChildren ) {
							$('.occupant-child-notify.notify-number-over-max').show();
						}
					}
					
					// Update the max attribute and the jQuery UI slider for children

					//$childInput.val('0');
			
					// Update the text in the ranger-max-value span
					
				}
			});

			$('.number-input').each(function() {
				var input = $(this).find('.number-value');
			
				$(this).on('click', '.minus-btn', function() {
					var guest = input.attr('data-guest');
					var minValue = parseInt(input.attr('data-min'));
					var value = parseInt(input.val());
					if (value > minValue) {
						input.val(value - 1);
						calculateSum();
						if (guest === "child") {
							$("#guest-age input[data-counter='" + (value - 1) + "']").remove();  // remove the corresponding extra input field
						}
					}
					$('#atollmatrix_room_id').trigger('change');
				});
			
				$(this).on('click', '.plus-btn', function() {
					var guest = input.attr('data-guest');
					var minValue = parseInt(input.attr('data-min'));
					var maxValue = parseInt(input.attr('data-max'));
					var guestmaxValue = parseInt(input.attr('data-guestmax'));
					var value = parseInt(input.val());
					if (value < maxValue && calculateSum() + 1 <= guestmaxValue) {
						$('.child-number-notice').hide();
						input.val(value + 1);
						calculateSum();
						if (guest === "child") {
							var extraInput = $("<input name='atollmatrix_reservation_room_children[age][]' type='text' data-counter='" + value + "' placeholder='Enter age'>");
							$("#guest-age").append(extraInput);  // append the extra input to the "guest-age" div
						}
					}
					$('#atollmatrix_room_id').trigger('change');
				});
			
				// Calculate the initial sum
				calculateSum();
			});
			
			
				function calculateSum() {
					var sum = 0;
					$('.number-input .number-value').each(function() {
					sum += parseInt($(this).val());
					});
					console.log('Total sum:', sum);
					return sum;
				}

				function calculateSum() {
					var sum = 0;
					$('.number-input .number-value').each(function() {
					sum += parseInt($(this).val());
					});
					console.log('Total sum:', sum);
					return sum;
				}
			  
			  

		}

			initialize_reservation_guest_amount();
			
			function getExistingDates() {
				let checkinValue = document.getElementById('atollmatrix_checkin_date') ? document.getElementById('atollmatrix_checkin_date').value : null;
				let checkoutValue = document.getElementById('atollmatrix_checkout_date') ? document.getElementById('atollmatrix_checkout_date').value : null;
			
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
			  
				if (selectedDates.length > 1) {
				  checkout = selectedDates[1];
				}
				let reservationID = atollmatrix_admin_vars.post_id;
				console.log(reservationID);
				const roomNights = checkout ? Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)) : 0;
			  
				const reservationDetails = document.getElementById("reservation-details");
				if (checkin && checkout) {
					reservationDetails.innerHTML = `
					<p>Checkin: ${checkin.toLocaleDateString()}</p>
					${checkout ? `<p>Checkout: ${checkout.toLocaleDateString()}</p>` : ''}
					<p>Room nights: ${roomNights}</p>
					`;
				}
			  
				// Set the values of the hidden input fields
				if (checkin && checkout) {
					const checkinOffset = checkin.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
					const checkoutOffset = checkout.getTimezoneOffset() * 60000; // Time zone offset in milliseconds

					document.getElementById("atollmatrix_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
					document.getElementById("atollmatrix_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];
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
			  
				  jQuery.post(ajaxurl, data, function(response) {
					let selectElement = $('#atollmatrix_room_id');
					let selectedValue = selectElement.val(); // Save the currently selected value

					// store the value to guest number selectors
					$('.occupants-range').attr('data-room', selectedValue);

					selectElement.empty();
				  
					var available_rooms = JSON.parse(response);
					$.each(available_rooms, function(key, value) {
						if (value) {
							let optionElement = `<option value="${key}" ${(key === selectedValue) ? 'selected' : ''}>${value}</option>`;
							selectElement.append(optionElement);
						}
					});
				  
					// Trigger update
					selectElement.select2('open');
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
				onReady: function(selectedDates, dateStr, instance) {
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
			  
					document.getElementById("atollmatrix_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
					document.getElementById("atollmatrix_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];
				  }
			  
				  var reservationDetails = "<p>Check-in: " + checkin.toLocaleDateString() + "</p>" +
										   "<p>Checkout: " + checkout.toLocaleDateString() + "</p>" +
										   "<p>Room nights: " + roomNights + "</p>";
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
				  fp = flatpickr(".availabilitycalendar", {
					  mode: "range",
					  dateFormat: "Y-m-d",
					  showMonths: 2,
					  enableTime: false,
					  defaultDate: [newStartDate, newEndDate], // Set the defaultDate to the start and end dates
					  onChange: function(selectedDates, dateStr, instance) {
						  if (selectedDates.length == 2) {
							  updateCalendarData(selectedDates);
						  }
					  }
				  });
			  }
			  
			  // Call the initialize function with the initial dates
			  initializeFlatpickr(startDate, endDate);
			  
			function updateCalendarData(selectedDates) {
				var start_date = selectedDates[0].toLocaleDateString('en-US').substr(0, 10);
				var end_date = selectedDates[1].toLocaleDateString('en-US').substr(0, 10);
				console.log( start_date,end_date,ajaxurl );
	
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						'action': 'get_Selected_Range_AvailabilityCalendar',
						'start_date': start_date,
						'end_date': end_date
					},
					success: function(data){
						$('#calendar').html(data);
						runCalendarAnimation();
						// After updating the content, initialize the tooltips again
						initializeTooltips();
					},
					error: function(data){
						alert('Error: Unable to retrieve calendar data.');
					}
				});
			}
	
			function shiftDates(buttonId, days) {
				$(buttonId).click(function() {
					var newStartDate = fp.selectedDates[0].fp_incr(days);
					var newEndDate = fp.selectedDates[1].fp_incr(days);
					
					// Destroy the current instance
					fp.destroy();
			
					// Initialize a new flatpickr instance with the new dates
					initializeFlatpickr(newStartDate, newEndDate);
			
					debouncedCalendarUpdate(fp.selectedDates);
				});
			}
			
			// Now call this function with the parameters you want:
			shiftDates('#prev-week', -7);
			shiftDates('#next-week', 7);
			shiftDates('#prev-half', -15);
			shiftDates('#next-half', 15);
			shiftDates('#prev', -30);
			shiftDates('#next', 30);
			  
						
		}

	});
})(jQuery);
