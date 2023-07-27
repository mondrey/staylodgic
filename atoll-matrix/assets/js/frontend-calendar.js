(function ($) {
	$(document).ready(function () {


		function roomOccupants() {
			document.addEventListener('click', function (event) {
				if (event.target.matches('.occupant-minus-btn')) {
					const minusBtn = event.target;
					const inputField = minusBtn.nextElementSibling;
					const currentValue = parseInt(inputField.value);
					const minValue = parseInt(inputField.getAttribute('min'));

					if (currentValue > minValue) {
						inputField.value = currentValue - 1;
					}
					event.preventDefault();
					updateButtonStates(inputField);
				} else if (event.target.matches('.occupant-plus-btn')) {
					const plusBtn = event.target;
					const inputField = plusBtn.previousElementSibling;
					const currentValue = parseInt(inputField.value);
					const maxValue = parseInt(inputField.getAttribute('max'));

					const roomParentData = $(event.target).closest('.room-occupied-group');
					const roomInputData = $(event.target).closest('.room-occupants-wrap');
					const roomID = roomParentData.data('room-id');

					const occupantType = roomParentData.data('type');
					const maxAdults = roomParentData.data('adults');
					const maxChildren = roomParentData.data('children');
					const maxGuests = roomParentData.data('guests');

					var maxType = maxGuests;

					if ( occupantType == 'children') {
						maxType = maxChildren;
					} else {
						maxType = maxAdults;
					}

					var adultsUserInput = parseInt(roomInputData.find('[data-occupant="adults-input-' + roomID + '"]').val());
					var childrenUserInput = parseInt(roomInputData.find('[data-occupant="children-input-' + roomID + '"]').val());
					if (isNaN(adultsUserInput)) {
						adultsUserInput = 0;
					}
					if (isNaN(childrenUserInput)) {
						childrenUserInput = 0;
					}

					const totalUserGuests = parseInt( adultsUserInput + childrenUserInput );
					console.log( adultsUserInput, childrenUserInput, totalUserGuests, maxGuests );

					
					if ( maxGuests > totalUserGuests ) {
							inputField.value = currentValue + 1;
					}
					event.preventDefault();
					updateButtonStates(inputField);
					
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


		function roomSelection() {
			const RoomBookingNumber = $('#booking-number').val();
			const minusBtns = $('.room-minus-btn');
			const plusBtns = $('.room-plus-btn');
			const numberInputs = $('input[type="room-number"]');

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
					roomOccupantData.find('.room-occupants-wrap-' + roomgroupID + '-' + index).hide();
				}
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

					roomSelection();
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
			console.log(checkin, checkout, rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'bookRooms',
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
