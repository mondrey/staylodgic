(function ($) {
	$(document).ready(function () {


		function calculateDaysBetweenDates(startDate, endDate) {
			var start = new Date(startDate);
			var end = new Date(endDate);
			var timeDiff = Math.abs(end.getTime() - start.getTime());
			var days = Math.ceil(timeDiff / (1000 * 3600 * 24));
			return days;
		}

		function processOccupancyData(occupancyData, instance) {
			// Process the occupancy data here
			// ...
			// You can use the occupancyData to disable specific dates or perform other operations
			// Set the disabled dates
			var disabledDates = Object.entries(occupancyData)
				.filter(function ([date, occupancyPercentage]) {
					return occupancyPercentage === 100;
				})
				.map(function ([date, occupancyPercentage]) {
					return date;
				});

			instance.set("disable", disabledDates);
		}

		function ReservationDatePicker() {
			var occupancyCache = {}; // Object to store cached occupancy data
			var cacheDuration = 5 * 60 * 1000; // Cache duration in milliseconds (5 minutes)

			function fetchOccupancyData(startDate, endDate, instance) {
				var data = {
					action: "fetchOccupancy_Percentage_For_Calendar_Range",
					start: startDate,
					end: endDate
				};

				var cacheKey = startDate + "-" + endDate;

				// Check if the occupancy data is already cached
				if (occupancyCache.hasOwnProperty(cacheKey)) {
					var cachedData = occupancyCache[cacheKey];

					// Check if the cache has expired
					if (Date.now() - cachedData.timestamp <= cacheDuration) {
						// Cache is still valid, use the cached data
						processOccupancyData(cachedData.data, instance);
						return; // Return early, no need for AJAX request
					} else {
						// Cache has expired, remove it
						delete occupancyCache[cacheKey];
					}
				}

				$.ajax({
					url: frontendAjax.ajaxurl,
					method: "POST",
					data: data,
					success: function (response) {
						console.log("Response data:", response);

						try {
							var occupancyData = response;

							// Cache the occupancy data with timestamp
							occupancyCache[cacheKey] = {
								data: occupancyData,
								timestamp: Date.now()
							};

							processOccupancyData(occupancyData, instance);
						} catch (error) {
							console.log("JSON parse error:", error);
						}
					},
					error: function (xhr, status, error) {
						console.log("AJAX error:", error);
					}
				});
			}

			// Rest of the code remains the same

			var debouncedFetchOccupancyData = _.debounce(fetchOccupancyData, 500); // Adjust the delay (in milliseconds) as needed

			function handleReservationCalendarMonthChange(selectedDates, dateStr, instance) {
				var currentYear = instance.currentYear;
				var currentMonth = instance.currentMonth;

				// Calculate the current and next months
				var currentMonthDate = new Date(currentYear, currentMonth, 1);
				var nextMonthDate = new Date(currentYear, currentMonth + 1, 1);

				var currentMonthName = currentMonthDate.toLocaleString("default", {
					month: "long"
				});
				var nextMonthName = nextMonthDate.toLocaleString("default", {
					month: "long"
				});

				console.log("Current month:", currentMonthName);
				console.log("Next month:", nextMonthName);

				// Calculate the first day of the current month
				var currentMonthFirstDay = new Date(currentYear, currentMonth, 1);
				currentMonthFirstDay.setDate(currentMonthFirstDay.getDate() + 1);
				currentMonthFirstDay = currentMonthFirstDay.toISOString().split("T")[0];
				console.log("First day of current month:", currentMonthFirstDay);

				// Calculate the last day of the next month
				var nextMonthLastDay = new Date(currentYear, currentMonth + 2, 0);
				nextMonthLastDay.setDate(nextMonthLastDay.getDate() + 1);
				nextMonthLastDay = nextMonthLastDay.toISOString().split("T")[0];
				console.log("Last day of next month:", nextMonthLastDay);

				debouncedFetchOccupancyData(currentMonthFirstDay, nextMonthLastDay, instance);
			}

			flatpickr("#reservation-date", {
				mode: "range",
				dateFormat: "Y-m-d",
				showMonths: 2,
				enableTime: false,
				onReady: handleReservationCalendarMonthChange,
				onMonthChange: handleReservationCalendarMonthChange,
				disable: [
					function (date) {
						// Example disable function
						// Return true to disable specific dates
						// Modify this function according to your requirements
						var dayOfWeek = date.getDay();
						return dayOfWeek === 0 || dayOfWeek === 6; // Disable weekends
					}
				],
				locale: {
					firstDayOfWeek: 1 // Start week on Monday
				},
				minDate: "today", // Disable navigation to months previous to the current month
				onChange: function (selectedDates, dateStr, instance) {
					if (selectedDates.length === 2) {
						var checkInDate = selectedDates[0].toDateString();
						var stayLast = selectedDates[1].toDateString();
						var checkOutDate = new Date(selectedDates[1].getTime() + 86400000).toDateString();
						updateSelectedDates(checkInDate, stayLast, checkOutDate);
					}
				}
			});
			// Function to update the selected dates and nights
			function updateSelectedDates(checkInDate, stayLast, checkOutDate) {
				$('.pre-book-check-in').text(checkInDate);
				$('.pre-book-stay-night').text(stayLast);
				$('.pre-book-check-out').text(checkOutDate);

				var nights = calculateDaysBetweenDates(checkInDate, checkOutDate);
				$('.pre-book-nights').text(nights);
			}
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
			var numberOfAdults = $('#number-of-guests').val();
			var numberOfChildren = $('#number-of-children').val();

			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'frontend_BookingSearch', // the PHP function to trigger
					booking_number: bookingNumber,
					reservation_date: reservationDate,
					number_of_guests: numberOfAdults,
					number_of_children: numberOfChildren
				},
				success: function (response) {
					var parsedResponse = JSON.parse(response);
					console.log(parsedResponse); // Output: The parsed JavaScript object or array
					// You can now work with the parsed data
					$('#recommended-alternative-dates').html(parsedResponse.alt_recommends);
					$('#available-list-ajax').html(parsedResponse.roomlist);
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
