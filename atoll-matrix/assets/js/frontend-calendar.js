(function ($) {
	$(document).ready(function () {


		function roomOccupants() {
			document.addEventListener('click', function (event) {
				if (event.target.matches('.occupant-minus-btn')) {
					const minusBtn = event.target;
					const inputField = minusBtn.nextElementSibling;
					const currentValue = parseInt(inputField.value);
					const minValue = parseInt(inputField.getAttribute('min'));
					const roomParentData = $(event.target).closest('.room-occupied-group');
					const roomID = roomParentData.data('room-id');

					var occupantRoomNumber = $(event.target).closest('.occupant-input-group').find('.room-occupants').data('roomnumber');
					var occupantType = $(event.target).closest('.occupant-input-group').find('.room-occupants').data('type');

					if (currentValue > minValue) {
						inputField.value = currentValue - 1;

						var max_child_inputs = currentValue - 1;
						if ( occupantType == 'children') {
							activateChildInputField( occupantRoomNumber,roomID, max_child_inputs );
						}
					}
					event.preventDefault();
					updateButtonStates(inputField);
					occupantsSummary();
				} else if (event.target.matches('.occupant-plus-btn')) {
					const plusBtn = event.target;
					const inputField = plusBtn.previousElementSibling;
					const currentValue = parseInt(inputField.value);
					const maxValue = parseInt(inputField.getAttribute('max'));

					const roomParentData = $(event.target).closest('.room-occupied-group');
					const roomInputData = $(event.target).closest('.room-occupants-wrap');
					const roomID = roomParentData.data('room-id');

					const maxAdults = roomParentData.data('adults');
					const maxChildren = roomParentData.data('children');
					const maxGuests = roomParentData.data('guests');
					
					var maxType = maxGuests;

					var occupantRoomNumber = $(event.target).closest('.occupant-input-group').find('.room-occupants').data('roomnumber');
					console.log( 'Room Number:' , occupantRoomNumber );

					var adultsUserInput = parseInt(roomInputData.find('[data-occupant="adults-input-' + roomID + '-' + occupantRoomNumber + '"]').val());
					var childrenUserInput = parseInt(roomInputData.find('[data-occupant="children-input-' + roomID + '-' + occupantRoomNumber + '"]').val());
					var occupantType = $(event.target).closest('.occupant-input-group').find('.room-occupants').data('type');
					
					if ( occupantType == 'children') {
						maxType = maxChildren;
					} else {
						maxType = maxAdults;
					}
					if (isNaN(adultsUserInput)) {
						adultsUserInput = 0;
					}
					if (isNaN(childrenUserInput)) {
						childrenUserInput = 0;
					}


					const totalUserGuests = parseInt( adultsUserInput + childrenUserInput );
					console.log( 'Adults:', adultsUserInput, 'Children:', childrenUserInput, 'Total:', totalUserGuests, 'Max:', maxGuests );
					
					if ( maxGuests > totalUserGuests ) {
						if ( maxGuests > currentValue ) {
							inputField.value = currentValue + 1;

							var max_child_inputs = currentValue + 1;
							
							if ( occupantType == 'children') {
								activateChildInputField( occupantRoomNumber,roomID, max_child_inputs );
							}
						}
					}
					event.preventDefault();
					updateButtonStates(inputField);
					occupantsSummary();
					
				}

			});

			function updateButtonStates(inputField) {
				const minusBtn = inputField.previousElementSibling;
				const plusBtn = inputField.nextElementSibling;
				const currentValue = parseInt(inputField.value);
				const minValue = parseInt(inputField.getAttribute('min'));
				const maxValue = parseInt(inputField.getAttribute('max'));

				minusBtn.disabled = (currentValue <= minValue);
				plusBtn.disabled = (currentValue >= maxValue);
			}
		}

		roomOccupants();

		function activateChildInputField( occupantRoomNumber,roomID, max_child_inputs ) {
			console.log( 'child inputfield' , occupantRoomNumber );
			$( '.occupant-children-age-set-' + roomID + '-' + occupantRoomNumber ).prop("disabled", true).hide();
			for ( var i = 0; i < max_child_inputs; i++ ) {
				var child_age_id = '#children-age-input-' + roomID + '-' + occupantRoomNumber + '-' + i;
				//add a line with input field with child_age_id from disabled state to active state
				$( child_age_id ).prop("disabled", false).show();
				console.log( 'child inputs:' , child_age_id );
			}
		}


		function roomSelection() {
			const RoomBookingNumber = $('#booking-number').val();
			const minusBtns = $('.room-minus-btn');
			const plusBtns = $('.room-plus-btn');
			const numberInputs = $('input[data-type="room-number"]');

			const storedBookingData = sessionStorage.getItem(RoomBookingNumber);
			const BookingparsedData = JSON.parse(storedBookingData);
			console.log( BookingparsedData );
		
			numberInputs.each(function(index) {
				const inputField = $(this);
				const minusBtn = minusBtns.eq(index);
				const plusBtn = plusBtns.eq(index);
				const currentValue = parseInt(inputField.val());
				const minValue = parseInt(inputField.attr('min'));
				const maxValue = parseInt(inputField.attr('max'));
				console.log(inputField, minusBtn, plusBtn, currentValue, minValue, maxValue);
				updateButtonStates(inputField, minusBtn, plusBtn, currentValue, minValue, maxValue);
			});
		
			$(document).on('click', function(event) {
				if ($(event.target).is('.room-minus-btn')) {
					const minusBtn = $(event.target);
					const inputField = minusBtn.next();
					const currentValue = parseInt(inputField.val());
					const minValue = parseInt(inputField.attr('min'));
					if (currentValue > minValue) {
						inputField.val(currentValue - 1);
						roomSummary('minus');
					}
					event.preventDefault();
					showOccupants(inputField);
					updateButtonStates(inputField);
					return false;
				} else if ($(event.target).is('.room-plus-btn')) {
					const plusBtn = $(event.target);
					const inputField = plusBtn.prev();
					const currentValue = parseInt(inputField.val());
					const maxValue = parseInt(inputField.attr('max'));
					console.log(currentValue);
					if (currentValue < maxValue) {
						inputField.val(currentValue + 1);
						roomSummary('plus');
						const roomgroupID = inputField.data('roominputid');
						const roomOccupantData = inputField.closest('.room-occupied-group');
						roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + parseInt(currentValue)).find('.occupant-adults').val('1');
					}

					event.preventDefault();
					showOccupants(inputField);
					updateButtonStates(inputField);
					return false;
				}
			});
		
			function showOccupants(inputField) {
				const roomgroupID = inputField.data('roominputid');
				const roomgroupQTY = inputField.data('roomqty');
				const roomOccupantData = inputField.closest('.room-occupied-group');
				console.log( roomOccupantData, roomgroupID, roomgroupQTY);
				const currentValue = parseInt(inputField.val());
		
				for (let index = 0; index < currentValue; index++) {
					roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + index).show();
				}
		
				for (let index = currentValue; index < roomgroupQTY; index++) {
					roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + index).find('.occupant-adults').val('0');
					roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + index).find('.occupant-children').val('0');
					roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + index).hide();
				}
				occupantsSummary();
			}
		
			function updateButtonStates(inputField) {
				const minusBtn = inputField.prev();
				const plusBtn = inputField.next();
				const currentValue = parseInt(inputField.val());
				const minValue = parseInt(inputField.attr('min'));
				const maxValue = parseInt(inputField.attr('max'));
		
				minusBtn.prop('disabled', (currentValue <= minValue));
				plusBtn.prop('disabled', (currentValue >= maxValue));
			}
		}
		roomSelection();

		function roomSummary( action ) {
			var current_rooms = parseInt( $( '.summary-room-number' ).text() );
			console.log('current room val', current_rooms );
			if ( action == 'plus' ) {
				var new_rooms = parseInt(current_rooms) + 1;
				$( '.summary-room-number' ).text( new_rooms );
			}
			if ( action == 'minus' ) {
				var new_rooms = parseInt(current_rooms) - 1;
				$( '.summary-room-number' ).text( new_rooms );
			}
		}
		function occupantsSummary() {
			var current_adult_number = 0;
			var current_children_number = 0;
		
			$('#reservation-data').find('.occupant-adults').each(function() {
				var get_adult_number = parseInt($(this).val());
				current_adult_number += get_adult_number;
			});
		
			$('.summary-adults-number').text(current_adult_number);

			$('#reservation-data').find('.occupant-children').each(function() {
				var get_child_number = parseInt($(this).val());
				current_children_number += get_child_number;
			});
		
			$('.summary-children-number').text(current_children_number);
		}

		// Function to update the selected dates and nights
		function updateSelectedDates(checkIn, checkOut) {
			if (!(checkIn instanceof Date) || !(checkOut instanceof Date)) {
				// Handle the case when checkIn or checkOut is not a valid Date object
				console.error('Invalid Date object');
				return;
			}

			var checkInDate = checkIn.toDateString();
			var stayLast = new Date(checkOut.getTime() - 86400000).toDateString();
			var checkOutDate = checkOut.toDateString();

			$('.pre-book-check-in').text(checkInDate);
			$('.pre-book-stay-night').text(stayLast);
			$('.pre-book-check-out').text(checkOutDate);

			var nights = calculateDaysBetweenDates(checkInDate, checkOutDate);
			$('.pre-book-nights').text(nights);
		}

		// Get the flatpickr input element
		var flatpickrInput = $("#reservation-date");
		// Attach click event to each span element
		$(document).on('click', '.recommended-dates-wrap span', function (e) {
			// Get the check-in and check-out dates from the data attributes
			var checkInDateStr = $(this).data("check-in");
			var stayLastDateStr = $(this).data("check-staylast");
			var checkOutDateStr = $(this).data("check-out");

			// Convert the date strings to Date objects
			var checkInDate = new Date(checkInDateStr);
			var stayLastDate = new Date(stayLastDateStr);
			var checkOutDate = new Date(checkOutDateStr);
			console.log(' the new checkout ' + checkOutDate);
			// Update the flatpickr input value with the selected date range
			flatpickrInput.val(checkInDateStr + " to " + checkOutDateStr);

			updateSelectedDates(checkInDate, checkOutDate);
			// Trigger click on the bookingSearch button
			$("#bookingSearch").trigger("click");
		});


		function calculateDaysBetweenDates(startDate, endDate) {
			var start = new Date(startDate);
			var end = new Date(endDate);
			var timeDiff = Math.abs(end.getTime() - start.getTime());
			var days = Math.ceil(timeDiff / (1000 * 3600 * 24));
			return days;
		}

		function ReservationDatePicker() {

			flatpickr("#reservation-date", {
				mode: "range",
				dateFormat: "Y-m-d",
				showMonths: 2,
				enableTime: false,
				locale: {
					firstDayOfWeek: 1 // Start week on Monday
				},
				minDate: "today", // Disable navigation to months previous to the current month
				onChange: function (selectedDates, dateStr, instance) {
					if (selectedDates.length === 2) {
						updateSelectedDates(selectedDates[0], selectedDates[1]);
					}
				}
			});
		}

		ReservationDatePicker();



		$("#number-of-children").change(function () {
			// Remove old selectors if exists
			$(".children-selector-wrap").remove();

			var numberOfChildren = $(this).val();
			var maxAge = $(this).parent().data('agelimitofchild');

			for (var j = 0; j < numberOfChildren; j++) {
				var select = $('<select class="children-age-selector" name="children_age_' + (j + 1) + '"></select>');

				for (var i = 0; i <= maxAge; i++) {
					select.append('<option value="' + i + '">' + i + '</option>');
				}

				var wrapper = $('<div class="children-selector-wrap"></div>');
				wrapper.append(select);
				$(this).parent().append(wrapper);
			}
		});

		// Frontend codes
		$('#bookingSearch').on('click', function (e) { // Changed here
			e.preventDefault();
			console.log('Here');
			var bookingNumber = $('#booking-number').val();
			var reservationDate = $('#reservation-date').val();
			var numberOfAdults = $('#number-of-adults').val();
			var numberOfChildren = $('#number-of-children').val();

			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'frontend_BookingSearch', // the PHP function to trigger
					booking_number: bookingNumber,
					reservation_date: reservationDate,
					number_of_adults: numberOfAdults,
					number_of_children: numberOfChildren
				},
				success: function (response) {
					var parsedResponse = JSON.parse(response);
					console.log(parsedResponse); // Output: The parsed JavaScript object or array
					// You can now work with the parsed data

					// Check if the array is null or empty
					if (parsedResponse.alt_recommends === false || parsedResponse.alt_recommends.length === 0) {
						// The array is empty
						var storeData = []; // Create a new empty array
						storeData = parsedResponse.booking_data;
						sessionStorage.setItem(bookingNumber, JSON.stringify(storeData));
						$('#available-list-ajax').html(parsedResponse.roomlist);
					} else {
						// The array is not empty
						$('.recommended-alt-wrap').show();
						$('#recommended-alt-dates').html(parsedResponse.alt_recommends);
					}
				},
				error: function (err) {
					// Handle error here
					console.log(err);
				}
			});
		});

		$(document).on('click', '#bookingRegister', function (e) {
			e.preventDefault();

			let booking_number = $('#reservation-data').data('bookingnumber');
			console.log('booking-number:' + booking_number);
			let adults = $('#reservation-data').data('adults');
			let children = $('#reservation-data').data('children');
			console.log(adults, children);
			let checkin = $('#reservation-data').data('checkin');
			let checkout = $('#reservation-data').data('checkout');
			let rooms = [];

			let full_name = $('#full_name').val();
			let email_address = $('#email_address').val();
			let phone_number = $('#phone_number').val();
			let street_address = $('#street_address').val();
			let city = $('#city').val();
			let state = $('#state').val();
			let zip_code = $('#zip_code').val();
			let country = $('#country').val();

			$('#available-list-ajax div').each(function () {
				let roomId = $(this).data('room-id');
				let roomQuantity = $(this).find('select[name="room_quantity"]').val();

				if (roomId && roomQuantity > 0) {
					rooms.push({ id: roomId, quantity: roomQuantity });
				}
			});

			$('#hotel-booking').find('input[type="text"]').each(function() {
				var inputValue = $(this).val();
				
				// Check if input value is zero and disable the input
				if (inputValue === '0') {
					$(this).prop('disabled', true);
				}
			});

			// Serialize form data
			const booking_data = $('#hotel-booking').serialize();
			
			console.log(checkin, checkout, rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'bookRooms',
					bookingdata: booking_data,
					adults: adults,
					children: children,
					booking_number: booking_number,
					checkin: checkin,
					checkout: checkout,
					rooms: rooms,
					full_name: full_name,
					email_address: email_address,
					phone_number: phone_number,
					street_address: street_address,
					city: city,
					state: state,
					zip_code: zip_code,
					country: country,
					nonce: frontendAjax.nonce // Our defined nonce
				},
				success: function (response) {
					// handle success
					if (response.success) {
						// handle success
						$('#bookingResponse').removeClass('error').addClass('success').text('Booking successfully registered.');
					} else {
						// handle error
						$('#bookingResponse').removeClass('success').addClass('error').text(response.data);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					// handle network errors, bad URLs, etc.
					$('#bookingResponse').removeClass('success').addClass('error').text(errorMessage);
				}
			});
		});

	});
})(jQuery);
