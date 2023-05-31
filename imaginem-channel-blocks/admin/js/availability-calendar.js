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

		// ---- Boostrap Tooltip
		$('.reserved-tab-with-info').each(function () {
			var guest = $(this).data('guest');
			var room = $(this).data('room');
			var reservationid = $(this).data('reservationid');
			var checkin = $(this).data('checkin');
			var checkout = $(this).data('checkout');
			var tooltipContent = 'Guest: ' + guest + '<br>Room: ' + room + '<br>Reservation ID: ' + reservationid + '<br>Check-in: ' + checkin + '<br>Check-out: ' + checkout;
	
			$(this).attr('data-bs-toggle', 'tooltip');
			$(this).attr('data-bs-html', 'true'); // Allow HTML content in the tooltip
			$(this).attr('title', tooltipContent);
	
		});
	
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		});
		// ---- Bootstrap Tooltip end

		if ($.fn.flatpickr) {
			// Initialize Flatpickr
			$('#quantity-popup .modaldatepicker').flatpickr(
				{
					mode: "range",
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
					action: 'cognitive_update_room_rate',
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
					action: 'cognitive_update_room_availability',
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
	
			flatpickr(".reservation", {
				mode: "range",
				dateFormat: "Y-m-d",
				enableTime: false,
				onChange: function(selectedDates, dateStr, instance) {
				  const checkin = selectedDates[0];
				  let checkout; // Declare the checkout variable
			  
				  if (selectedDates.length > 1) {
					checkout = selectedDates[1];
				  }
			  
				  const roomNights = checkout ? Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24)) : 0;
			  
				  const reservationDetails = document.getElementById("reservation-details");
				  reservationDetails.innerHTML = `
					<p>Checkin: ${checkin.toLocaleDateString()}</p>
					${checkout ? `<p>Checkout: ${checkout.toLocaleDateString()}</p>` : ''}
					<p>Room nights: ${roomNights}</p>
				  `;
			  
				  // Set the values of the hidden input fields
				  if (checkin && checkout) {
					const checkinOffset = checkin.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
					const checkoutOffset = checkout.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
			  
					document.getElementById("pagemeta_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
					document.getElementById("pagemeta_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];
				  }
				},
				onReady: function(selectedDates, dateStr, instance) {
				  // calculate room nights and display reservation details for existing reservation
				  var dateRangeInput = instance.input;
				  var dateRangeValue = dateRangeInput.value;
				  var dateRangeParts = dateRangeValue.split(" to ");
				  var checkin = new Date(dateRangeParts[0]);
				  var checkout = new Date(dateRangeParts[1]);
				  var roomNights = (checkout - checkin) / (1000 * 60 * 60 * 24);
			  
				  // Set the values of the hidden input fields
				  if (checkin && checkout) {
					const checkinOffset = checkin.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
					const checkoutOffset = checkout.getTimezoneOffset() * 60000; // Time zone offset in milliseconds
			  
					document.getElementById("pagemeta_checkin_date").value = new Date(checkin - checkinOffset).toISOString().split('T')[0];
					document.getElementById("pagemeta_checkout_date").value = new Date(checkout - checkoutOffset).toISOString().split('T')[0];
				  }
			  
				  var reservationDetails = "<p>Check-in: " + checkin.toLocaleDateString() + "</p>" +
										   "<p>Checkout: " + checkout.toLocaleDateString() + "</p>" +
										   "<p>Room nights: " + roomNights + "</p>";
				  document.getElementById("reservation-details").innerHTML = reservationDetails;
				}
			});
			  
	
			flatpickr(".availabilitycalendar", {
				mode: "range",
				dateFormat: "Y-m-d",
				enableTime: false,
				onChange: function(selectedDates, dateStr, instance) {
					
					if (selectedDates.length == 2) {
						var start_date = selectedDates[0].toLocaleDateString('en-US').substr(0, 10);
						var end_date = selectedDates[1].toLocaleDateString('en-US').substr(0, 10);					
						console.log( start_date,end_date );
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {
								'action': 'cognitive_ajax_get_availability_calendar',
								'start_date': start_date,
								'end_date': end_date
							},
							success: function(data){
								$('#calendar').html(data);
								//updateCalendarCells();
							},
							error: function(){
								alert('Error: Unable to retrieve calendar data.');
							}
						});
					}
				}
			});
		}

	});
})(jQuery);
