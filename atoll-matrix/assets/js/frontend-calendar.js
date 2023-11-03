(function ($) {
	$(document).ready(function () {

		function processRoomPrice(roomOccupiedGroup) {
			$('.room-occupied-group').removeClass('room-selected');
			roomOccupiedGroup.addClass('room-selected');
			
			var bookingnumber = $('#reservation-data').data('bookingnumber');
			var roomId = roomOccupiedGroup.data('room-id');
			var roomPriceTotal = roomOccupiedGroup.find('.room-price-total').data('roomprice');
			var bedLayout = $("input[name='room[" + roomId + "][bedlayout]']:checked").val();
			var mealPlanInput = $("input[name='room[" + roomId + "][meal_plan][optional]']:checked");
			var mealPlan = mealPlanInput.val();
			var mealPlanPrice = mealPlanInput.data('mealprice');
			console.log('Got:' + bookingnumber);
		
			var dataToSend = {
				action: 'process_RoomPrice',
				booking_number: bookingnumber,
				room_id: roomId,
				room_price: roomPriceTotal,
				bed_layout: bedLayout,
				meal_plan: mealPlan,
				meal_plan_price: mealPlanPrice
			};
		
			$.ajax({
				type: 'POST',
				url: frontendAjax.ajaxurl,
				data: dataToSend,
				success: function(response) {
					var price_element = roomOccupiedGroup.find('.room-price-total');
					
					price_element.find('.formatted-price').velocity('fadeOut', {
						duration: 150,
						complete: function() {
							price_element.html(response);
						}
					});		
					console.log(response);
					// You can update the page content or perform other actions here
				}
			});
		}

		// Get new price for room input changes
		$(document).on('change', '#reservation-data input[type="radio"].mealtype-input', function () {
			var roomOccupiedGroup = $(this).closest('.room-occupied-group');
			processRoomPrice(roomOccupiedGroup);
		});

		// Back to Room Selection
		$(document).on('click', '.booking-backto-roomschoice', function () {
			$('.registration_form_outer').velocity('fadeOut', {
				duration: 350,
				complete: function() {
					$('#reservation-data').velocity('fadeIn');
				}
			});
		});

		// Process Room Data
		function processRoomData(roomOccupiedGroup) {
			$('.room-occupied-group').removeClass('room-selected');
			roomOccupiedGroup.addClass('room-selected');
			
			var atollmatrix_roomlistingbox_nonce = $('input[name="atollmatrix_roomlistingbox_nonce"]').val();
			var bookingnumber = $('#reservation-data').data('bookingnumber');
			var roomId = roomOccupiedGroup.data('room-id');
			var roomPriceTotal = roomOccupiedGroup.find('.room-price-total').data('roomprice');
			var bedLayout = $("input[name='room[" + roomId + "][bedlayout]']:checked").val();
			var mealPlanInput = $("input[name='room[" + roomId + "][meal_plan][optional]']:checked");
			var mealPlan = mealPlanInput.val();
			var mealPlanPrice = mealPlanInput.data('mealprice');
			console.log(mealPlanPrice);
		
			var dataToSend = {
				action: 'process_SelectedRoom',
				bookingnumber: bookingnumber,
				room_id: roomId,
				room_price: roomPriceTotal,
				bed_layout: bedLayout,
				meal_plan: mealPlan,
				meal_plan_price: mealPlanPrice,
				atollmatrix_roomlistingbox_nonce: atollmatrix_roomlistingbox_nonce
			};
		
			$.ajax({
				type: 'POST',
				url: frontendAjax.ajaxurl,
				data: dataToSend,
				success: function(response) {
					// Handle the response from the server
					$('#booking-summary').html(response);

					$('#reservation-data').velocity('fadeOut', {
						duration: 350,
						complete: function() {
							$('.registration_form_outer').velocity('fadeIn')
						}
					});
					console.log(response);
					// You can update the page content or perform other actions here
				}
			});
		}

		// Process room choice and registration
		$(document).on('click', '#reservation-data .choose-room-button', function () {

			var roomOccupiedGroup = $(this).closest('.room-occupied-group');
			processRoomData(roomOccupiedGroup);

		});
		
		// $(document).on('click', '.room-occupied-group:not(input[type="radio"])', function () {
		// 	var roomOccupiedGroup = $(this);
		// 	processRoomData(roomOccupiedGroup);
		// });

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
				var select = $('<select id="children_age_' + (j + 1) + '" class="children-age-selector" name="children_age[]"></select>');

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
			var atollmatrix_searchbox_nonce = $('input[name="atollmatrix_searchbox_nonce"]').val();

			var childrenAge = [];

			// Loop through all select elements with the class 'children-age-selector'
			$('.children-age-selector').each(function() {
				childrenAge.push($(this).val());
			});

			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'booking_BookingSearch', // the PHP function to trigger
					booking_number: bookingNumber,
					reservation_date: reservationDate,
					number_of_adults: numberOfAdults,
					number_of_children: numberOfChildren,
					children_age: childrenAge,
					atollmatrix_searchbox_nonce: atollmatrix_searchbox_nonce
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

		$(document).on('click', '#booking-register', function (e) {
			e.preventDefault();

			var atollmatrix_roomlistingbox_nonce = $('input[name="atollmatrix_roomlistingbox_nonce"]').val();
			let data_booking_number = $('#reservation-data').data('bookingnumber');
			console.log('booking-number:' + data_booking_number);

			let data_full_name = $('.registration_form_inputs #full_name').val();
			let data_passport = $('.registration_form_inputs #passport').val();
			let data_email_address = $('.registration_form_inputs #email_address').val();
			let data_phone_number = $('.registration_form_inputs #phone_number').val();
			let data_street_address = $('.registration_form_inputs #street_address').val();
			let data_city = $('.registration_form_inputs #city').val();
			let data_state = $('.registration_form_inputs #state').val();
			let data_zip_code = $('.registration_form_inputs #zip_code').val();
			let data_country = $('.registration_form_inputs #country').val();
			let data_guest_comment = $('.registration_form_inputs #guest_comment').val();
			let data_guest_consent = $('.registration_form_inputs #guest_consent').val();

			// Serialize form data
			const registration_data = $('#guest-registration').serialize();
			
			//console.log(checkin, checkout, rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'bookRooms',
					bookingdata: registration_data,
					booking_number: data_booking_number,
					full_name: data_full_name,
					passport: data_passport,
					email_address: data_email_address,
					phone_number: data_phone_number,
					street_address: data_street_address,
					city: data_city,
					state: data_state,
					zip_code: data_zip_code,
					country: data_country,
					guest_comment: data_guest_comment,
					guest_consent: data_guest_consent,
					atollmatrix_roomlistingbox_nonce: atollmatrix_roomlistingbox_nonce
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
