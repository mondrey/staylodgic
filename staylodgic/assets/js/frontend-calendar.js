(function ($) {
	$(document).ready(function () {
		window.isMobile = function () {
			const mobileRegex =
				/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
			return mobileRegex.test(navigator.userAgent);
		};

		var calMonthsToDisplay = 2;
		if (window.isMobile()) {
			calMonthsToDisplay = 1;
		}

		$(document).on("click", Lightbox.defaultSelector, Lightbox.initialize);

		$("#booking_details").on("click", function (e) {
			e.preventDefault();

			var stay_booking_number = $("#booking_number").val();
			if (!stay_booking_number) {
				return;
			}

			$("#booking_details").addClass("booking-disabled");

			// Retrieve the booking number
			var staylodgic_bookingdetails_nonce = $(
				'input[name="staylodgic_bookingdetails_nonce"]'
			).val();

			var requestType = $(this).data("request");

			// console.log(stay_booking_number);
			// console.log(frontendAjax.ajaxurl);
			// Check if the booking number is entered

			if ("guestregistration" == requestType) {
				// AJAX call to get booking details
				$.ajax({
					url: frontendAjax.ajaxurl, // Replace with your AJAX URL
					type: "POST",
					dataType: "html",
					data: {
						action: "requestRegistrationDetails", // Replace with your actual action hook
						booking_number: stay_booking_number,
						staylodgic_bookingdetails_nonce:
							staylodgic_bookingdetails_nonce,
					},
					success: function (response) {
						// Display the booking details
						var details = response; // Parse the JSON string to HTML
						console.log(details);
						$("#guestregistration-details-ajax").html(response);

						$("#booking_details").removeClass("booking-disabled");
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.error(
							"Error fetching booking details:",
							textStatus,
							errorThrown
						);
						$("#booking-details-ajax").html(
							"Error fetching booking details. Please try again."
						);
					},
				});
			}

			if ("bookingdetails" == requestType) {
				// AJAX call to get booking details
				$.ajax({
					url: frontendAjax.ajaxurl, // Replace with your AJAX URL
					type: "POST",
					dataType: "html",
					data: {
						action: "getBookingDetails", // Replace with your actual action hook
						booking_number: stay_booking_number,
						staylodgic_bookingdetails_nonce:
							staylodgic_bookingdetails_nonce,
					},
					success: function (response) {
						// Display the booking details
						$("#booking-details-ajax").html(response);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						console.error(
							"Error fetching booking details:",
							textStatus,
							errorThrown
						);
						$("#booking-details-ajax").html(
							"Error fetching booking details. Please try again."
						);
					},
				});
			}
		});

		function processRoomPrice(roomOccupiedGroup) {
			$(".preloader-element").velocity("fadeIn");
			$(".choose-room-button").addClass("booking-disabled");
			$(".room-occupied-group").removeClass("room-selected");
			roomOccupiedGroup.addClass("room-selected");

			var bookingnumber = $("#reservation-data").data("bookingnumber");
			var stay_room_id = roomOccupiedGroup.data("room-id");
			var roomPriceTotal = roomOccupiedGroup
				.find(".room-price-total")
				.data("roomprice");
			var stay_bed_layout = $(
				"input[name='room[" + stay_room_id + "][bedlayout]']:checked"
			).val();
			var mealPlanInput = $(
				"input[name='room[" +
					stay_room_id +
					"][meal_plan][optional]']:checked"
			);
			var mealPlan = mealPlanInput.val();
			var mealPlanPrice = mealPlanInput.data("mealprice");
			var staylodgic_searchbox_nonce = $(
				'input[name="staylodgic_searchbox_nonce"]'
			).val();
			console.log("Got:" + bookingnumber);

			var dataToSend = {
				action: "process_room_price",
				booking_number: bookingnumber,
				room_id: stay_room_id,
				room_price: roomPriceTotal,
				bed_layout: stay_bed_layout,
				meal_plan: mealPlan,
				meal_plan_price: mealPlanPrice,
				staylodgic_searchbox_nonce: staylodgic_searchbox_nonce,
			};

			$.ajax({
				type: "POST",
				url: frontendAjax.ajaxurl,
				data: dataToSend,
				success: function (response) {
					var price_element =
						roomOccupiedGroup.find(".room-price-total");

					price_element.find(".formatted-price").velocity("fadeOut", {
						duration: 150,
						complete: function () {
							price_element.html(response);
						},
					});
					$(".preloader-element").velocity("fadeOut");
					$(".choose-room-button").removeClass("booking-disabled");
					console.log(response);
					// You can update the page content or perform other actions here
				},
			});
		}

		// Get new price for room input changes
		$(document).on(
			"change",
			'#reservation-data input[type="radio"].mealtype-input',
			function () {
				var roomOccupiedGroup = $(this).closest(".room-occupied-group");
				processRoomPrice(roomOccupiedGroup);
			}
		);

		// Back to Room Selection
		$(document).on("click", ".booking-backto-roomschoice", function () {
			$(".registration_request").velocity("fadeOut", {
				duration: 350,
				complete: function () {
					$("#reservation-data").velocity("fadeIn");
				},
			});
		});
		// Back to Room Selection
		$(document).on("click", ".booking-backto-activitychoice", function () {
			$(".registration_request").velocity("fadeOut", {
				duration: 350,
				complete: function () {
					$("#activity-data").velocity("fadeIn");
				},
			});
		});

		// Process Room Data
		function processRoomData(roomOccupiedGroup) {
			$(".choose-room-button").addClass("booking-disabled");
			$(".room-occupied-group").removeClass("room-selected");
			roomOccupiedGroup.addClass("room-selected");

			var staylodgic_roomlistingbox_nonce = $(
				'input[name="staylodgic_roomlistingbox_nonce"]'
			).val();
			var bookingnumber = $("#reservation-data").data("bookingnumber");
			var stay_room_id = roomOccupiedGroup.data("room-id");
			var roomPriceTotal = roomOccupiedGroup
				.find(".room-price-total")
				.data("roomprice");
			var stay_bed_layout = $(
				"input[name='room[" + stay_room_id + "][bedlayout]']:checked"
			).val();
			var mealPlanInput = $(
				"input[name='room[" +
					stay_room_id +
					"][meal_plan][optional]']:checked"
			);
			var mealPlan = mealPlanInput.val();
			var mealPlanPrice = mealPlanInput.data("mealprice");
			console.log(mealPlanPrice);

			var dataToSend = {
				action: "process_selected_room",
				bookingnumber: bookingnumber,
				room_id: stay_room_id,
				room_price: roomPriceTotal,
				bed_layout: stay_bed_layout,
				meal_plan: mealPlan,
				meal_plan_price: mealPlanPrice,
				staylodgic_roomlistingbox_nonce:
					staylodgic_roomlistingbox_nonce,
			};

			$.ajax({
				type: "POST",
				url: frontendAjax.ajaxurl,
				data: dataToSend,
				success: function (response) {
					// Handle the response from the server
					$("#booking-summary").html(response);

					$("#reservation-data").velocity("fadeOut", {
						duration: 350,
						complete: function () {
							$(".registration_request").velocity("fadeIn");
							$(".choose-room-button").removeClass(
								"booking-disabled"
							);
						},
					});
					console.log(response);
					// You can update the page content or perform other actions here
				},
			});
		}

		// Process room choice and registration
		$(document).on(
			"click",
			'#reservation-data .choose-room-button:not(".booking-disabled")',
			function () {
				var roomOccupiedGroup = $(this).closest(".room-occupied-group");
				processRoomData(roomOccupiedGroup);
			}
		);

		// $(document).on('click', '.room-occupied-group:not(input[type="radio"])', function () {
		// 	var roomOccupiedGroup = $(this);
		// 	processRoomData(roomOccupiedGroup);
		// });

		// Function to format the date as "MMM Dth" (e.g., "Jan 21st")
		function formatDate(date) {
			const options = { month: "short", day: "numeric" };
			return new Intl.DateTimeFormat("en-US", options).format(date);
		}
		function formatDateToYYYYMMDD(date) {
			if (!(date instanceof Date)) {
				console.error("Invalid Date object");
				return "";
			}

			var year = date.getFullYear();
			var month = ("0" + (date.getMonth() + 1)).slice(-2); // months are 0-based
			var day = ("0" + date.getDate()).slice(-2);

			return year + "-" + month + "-" + day;
		}

		// Function to update the selected dates and nights
		function updateSelectedDates(checkIn, checkOut) {
			if (!(checkIn instanceof Date) || !(checkOut instanceof Date)) {
				// Handle the case when checkIn or checkOut is not a valid Date object
				console.error("Invalid Date object");
				return;
			}

			var formattedCheckIn = formatDate(checkIn);
			var formattedStayLast = formatDate(
				new Date(checkOut.getTime() - 86400000)
			);
			var formattedCheckOut = formatDate(checkOut);

			// Update the selected dates in the specified format
			var formattedDateRange =
				"<span class='date-front-calendar-block'>" +
				formattedCheckIn +
				" to </span><span class='date-front-calendar-block'>" +
				formattedCheckOut +
				"</span> ";
			var nights = calculateDaysBetweenDates(checkIn, checkOut);

			nightsSuffix = "Nights";
			if (1 == nights) {
				nightsSuffix = "Night";
			}
			var nightsText =
				"<span class='date-front-calendar-nights-block'>( " +
				nights +
				" " +
				nightsSuffix +
				" )</spa>";

			$(".front-booking-calendar-date").html(
				formattedDateRange + nightsText
			);

			$(".pre-book-check-in").text(formattedCheckIn);
			$(".pre-book-stay-night").text(formattedStayLast);
			$(".pre-book-check-out").text(formattedCheckOut);

			$(".pre-book-nights").text(nights);
		}

		function updateInfoDisplay(selectedDates) {
			const checkInSpan = document.querySelector(
				"#check-in-display span"
			);
			const nightsSpan = document.querySelector("#nights-display span");

			if (selectedDates.length === 1) {
				let checkInDate = selectedDates[0];

				let checkOutDate = new Date(checkInDate);
				checkOutDate.setDate(checkOutDate.getDate() + 1);
				let lastNightDate = new Date(checkOutDate);
				lastNightDate.setDate(lastNightDate.getDate() - 1); // Calculate last night

				checkInSpan.textContent =
					formatDateToLocale(checkInDate) +
					" to " +
					formatDateToLocale(checkOutDate);

				nightsSpan.textContent = "1";
			} else if (selectedDates.length === 2) {
				let checkInDate = selectedDates[0];
				let checkOutDate = new Date(selectedDates[1]);
				let lastNightDate = new Date(checkOutDate);
				checkOutDate.setDate(checkOutDate.getDate()); // Increment checkout date
				lastNightDate.setDate(checkOutDate.getDate() - 1); // Increment checkout date
				const nights =
					(checkOutDate - checkInDate) / (1000 * 60 * 60 * 24);

				checkInSpan.textContent =
					formatDateToLocale(checkInDate) +
					" to " +
					formatDateToLocale(checkOutDate);

				nightsSpan.textContent = nights.toString();
			}
		}

		// Helper function to format date to YYYY-MM-DD
		function formatDateToLocale(date) {
			return (
				("0" + (date.getMonth() + 1)).slice(-2) +
				"-" +
				("0" + date.getDate()).slice(-2) +
				"-" +
				date.getFullYear()
			);
		}

		// Get the flatpickr input element
		var flatpickrInput = $("#reservation-date");
		// Attach click event to each span element
		$(document).on("click", ".recommended-dates-wrap span", function (e) {
			// Get the check-in and check-out dates from the data attributes
			var checkInDateStr = $(this).data("check-in");
			var stayLastDateStr = $(this).data("check-staylast");
			var checkOutDateStr = $(this).data("check-out");

			// Convert the date strings to Date objects
			var checkInDate = new Date(checkInDateStr);
			var stayLastDate = new Date(stayLastDateStr);
			var checkOutDate = new Date(checkOutDateStr);
			console.log(" the new checkout " + checkOutDate);
			// Update the flatpickr input value with the selected date range
			// Update the flatpickr input value with the selected date range
			flatpickrInstance.input.value =
				checkInDateStr + " to " + checkOutDateStr;

			// Set the selected dates using setDate method
			flatpickrInstance.setDate([checkInDate, checkOutDate]);

			updateSelectedDates(checkInDate, checkOutDate);
			var selectedDates = []; // Correct array declaration
			selectedDates[0] = checkInDate;
			selectedDates[1] = checkOutDate;
			// updateInfoDisplay(selectedDates); // Call the function with the array

			// Trigger click on the bookingSearch button
			$("#bookingSearch").trigger("click");
		});

		function calculateDaysBetweenDates(stay_start_date, stay_end_date) {
			var start = new Date(stay_start_date);
			var end = new Date(stay_end_date);
			var timeDiff = Math.abs(end.getTime() - start.getTime());
			var days = Math.ceil(timeDiff / (1000 * 3600 * 24));
			return days;
		}

		function setupGuestsContainer() {
			var guestsContainer = $(".front-booking-guests-container");
			var guestsWrap = $(".staylodgic_reservation_room_guests_wrap");

			// Hide guestsWrap initially
			guestsWrap.addClass("hidden");

			// Function to fade in the guestsWrap
			function fadeInGuestsWrap() {
				guestsWrap.velocity("slideDown", {
					duration: 200,
				});
			}

			// Function to fade out the guestsWrap
			function fadeOutGuestsWrap() {
				guestsWrap.velocity("slideUp", {
					duration: 200,
				});
			}

			// Toggle guestsWrap visibility when clicking .front-booking-guests-container
			guestsContainer.on("click", function (event) {
				// Toggle visibility by adding/removing the 'hidden' class
				guestsWrap.hasClass("hidden")
					? fadeInGuestsWrap()
					: fadeOutGuestsWrap();
				event.stopPropagation(); // Prevent the click event from reaching the document click handler
			});

			// Hide guestsWrap when clicking outside of it
			$(document).on("click", function (event) {
				if (
					!$(event.target).closest(
						".staylodgic_reservation_room_guests_wrap"
					).length
				) {
					// If the click was outside the guestsWrap, fade it out
					fadeOutGuestsWrap();
				}
			});

			// Prevent hiding when clicking inside the container
			guestsWrap.on("click", function (event) {
				event.stopPropagation(); // Prevent the click event from reaching the document click handler
			});
		}

		// Call the function to set up the guests container behavior
		setupGuestsContainer();

		function ReservationDatePicker() {
			var flatpickrInstance;

			// Function to get fully booked dates from the data attribute
			function getFullyBookedDates() {
				var bookedData = document
					.getElementById("reservation-date")
					.getAttribute("data-booked");
				return JSON.parse(bookedData);
			}

			// Function to disable dates based on the fully booked dates
			function disableFullyBookedDates(date) {
				var fullyBookedDates = getFullyBookedDates();
				// Convert date to local YYYY-MM-DD format
				var stay_date_string =
					date.getFullYear() +
					"-" +
					("0" + (date.getMonth() + 1)).slice(-2) +
					"-" +
					("0" + date.getDate()).slice(-2);

				if (Array.isArray(fullyBookedDates)) {
					return fullyBookedDates.includes(stay_date_string);
				} else if (typeof fullyBookedDates === "object") {
					// Check if the date is in the object keys
					return fullyBookedDates.hasOwnProperty(stay_date_string);
				}

				return false;
			}

			if (document.getElementById("reservation-date")) {
				flatpickrInstance = flatpickr("#reservation-date", {
					mode: "range",
					dateFormat: "Y-m-d",
					disableMobile: "true",
					showMonths: calMonthsToDisplay,
					enableTime: false,
					locale: {
						firstDayOfWeek: 1, // Start week on Monday
					},
					minDate: "today", // Disable navigation to months previous to the current month
					onMonthChange: function (selectedDates, dateStr, instance) {
						//updateInfoDisplay(selectedDates);
					},
					onChange: function (selectedDates, dateStr, instance) {
						if (selectedDates.length === 2) {
							// Calculate the difference in days between the start and end dates
							var diffInDays =
								(selectedDates[1] - selectedDates[0]) /
								(1000 * 60 * 60 * 24);
							if (diffInDays > 60) {
								// If the difference exceeds 30 days, show an alert and clear the selection
								$(".front-booking-calendar-date").html(
									"Cannot exceed 60 days"
								);
								instance.clear(); // Clear the selected dates
							} else {
								// Otherwise, update the selected dates
								updateSelectedDates(
									selectedDates[0],
									selectedDates[1]
								);

								//updateInfoDisplay(selectedDates);
							}
						}
					},
					onDayCreate: function (dObj, dStr, fp, dayElem) {
						// Disable the start date in the end date selection
						if (
							fp.selectedDates.length === 1 &&
							dayElem.dateObj.getTime() ===
								fp.selectedDates[0].getTime()
						) {
							dayElem.classList.add("disabled");
							dayElem.onclick = function () {
								return false;
							};
						}
					},
					disable: [
						// Use the disableFullyBookedDates function to disable specific dates
						function (date) {
							return disableFullyBookedDates(date);
						},
					],
				});
			}

			if (document.getElementById("activity-reservation-date")) {
				flatpickrInstance = flatpickr("#activity-reservation-date", {
					mode: "single",
					dateFormat: "Y-m-d",
					showMonths: 1,
					disableMobile: "true",
					enableTime: false,
					locale: {
						firstDayOfWeek: 1, // Start week on Monday
					},
					minDate: "today",
					onChange: function (selectedDates, dateStr, instance) {
						var formattedCheckIn = formatDate(selectedDates[0]);
						$(".front-booking-calendar-date").text(
							formattedCheckIn
						);
					},
				});
			}

			var calendarWrap = document.querySelector(
				".front-booking-calendar-wrap"
			);
			if (calendarWrap) {
				calendarWrap.addEventListener("click", function () {
					flatpickrInstance.open();
				});
			} else {
				console.log(
					"Element with class 'front-booking-calendar-wrap' not found."
				);
			}

			return flatpickrInstance;
		}

		// Initialize flatpickr and get the instance
		var flatpickrInstance = ReservationDatePicker();

		// $("#number-of-children").change(function () {
		// 	// Remove old selectors if exists
		// 	$(".children-selector-wrap").remove();

		// 	var numberOfChildren = $(this).val();
		// 	var maxAge = $(this).parent().data('agelimitofchild');

		// 	for (var j = 0; j < numberOfChildren; j++) {
		// 		var select = $('<select id="children_age_' + (j + 1) + '" class="children-age-selector" name="children_age[]"></select>');

		// 		for (var i = 0; i <= maxAge; i++) {
		// 			select.append('<option value="' + i + '">' + i + '</option>');
		// 		}

		// 		var wrapper = $('<div class="children-selector-wrap"></div>');
		// 		wrapper.append(select);
		// 		$(this).parent().append(wrapper);
		// 	}
		// });

		// Frontend codes
		$("#bookingSearch").on("click", function (e) {
			// Changed here
			e.preventDefault();

			$("#bookingSearch").addClass("booking-disabled");
			$('.available-list').fadeOut();

			// Retrieve the date from the input field
			var inputVal = $("#reservation-date").val();
			var dates = inputVal.split(" to ");

			var checkInDate, checkOutDate;
			var reservationDate;

			console.log(inputVal);
			if (inputVal == "") {
				console.log("One");
				// Only one date in input field, get date from #check-in-display
				// var checkInDateStr = $('#check-in-display span').text();
				// checkInDate = new Date(checkInDateStr);
				// checkOutDate = new Date(checkInDateStr); // Use the same date for check-out
				// updateSelectedDates(checkInDate, checkOutDate);
				// var formattedCheckIn = formatDateToYYYYMMDD(checkInDate);
				// var formattedCheckOut = formatDateToYYYYMMDD(checkOutDate);
				// reservationDate = formattedCheckIn + ' to ' + formattedCheckOut;
				// console.log( reservationDate );
				$("#bookingSearch").removeClass("booking-disabled");
			} else {
				//reservationDate = $('#reservation-date').val();
				console.log("Two");
				//console.log( reservationDate );

				var inputVal = $("#reservation-date").val();
				var dates = inputVal.split(" to ");

				if (dates.length === 2) {
					// Parse the second date and subtract one day
					var stay_end_date = new Date(dates[1]);
					stay_end_date.setDate(stay_end_date.getDate() - 1);

					// Format the date back into a string in the format "YYYY-MM-DD"
					var formattedEndDate =
						stay_end_date.getFullYear() +
						"-" +
						("0" + (stay_end_date.getMonth() + 1)).slice(-2) +
						"-" +
						("0" + stay_end_date.getDate()).slice(-2);

					// Combine the first date and the adjusted second date
					var reservationDate = dates[0] + " to " + formattedEndDate;

					console.log(reservationDate); // Output the combined dates

					var stay_booking_number = $("#booking-number").val();
					var numberOfAdults = $("#number-of-adults").val();
					var numberOfChildren = $("#number-of-children").val();
					var staylodgic_searchbox_nonce = $(
						'input[name="staylodgic_searchbox_nonce"]'
					).val();

					var childrenAge = [];

					// Loop through all select elements with the class 'children-age-selector'
					$('#guest-age input[name="children_age[]"]').each(
						function () {
							childrenAge.push($(this).val());
						}
					);

					$.ajax({
						url: frontendAjax.ajaxurl, // the localized URL
						type: "POST",
						data: {
							action: "booking_booking_search", // the PHP function to trigger
							booking_number: stay_booking_number,
							reservation_date: reservationDate,
							number_of_adults: numberOfAdults,
							number_of_children: numberOfChildren,
							children_age: childrenAge,
							staylodgic_searchbox_nonce:
								staylodgic_searchbox_nonce,
						},
						success: function (response) {
							$(".recommended-alt-wrap").hide();
							var parsedResponse = JSON.parse(response);
							console.log(parsedResponse.booking_data.rooms); // Output: The parsed JavaScript object or array
							// You can now work with the parsed data

							// Check if the array is null or empty
							if (
								parsedResponse.alt_recommends === false ||
								parsedResponse.alt_recommends.length === 0
							) {
								// The array is empty
								var storeData = []; // Create a new empty array
								storeData = parsedResponse.booking_data;
								sessionStorage.setItem(
									stay_booking_number,
									JSON.stringify(storeData)
								);
								$("#available-list-ajax").html(
									parsedResponse.roomlist
								);
								$("#available-list-ajax").show();
							} else {
								// The array is not empty
								$("#available-list-ajax").hide();
								$(".recommended-alt-wrap").show();
								$("#recommended-alt-dates").html(
									parsedResponse.alt_recommends
								);
							}
							$("#bookingSearch").removeClass("booking-disabled");
							$('.available-list').fadeIn();
						},
						error: function (err) {
							// Handle error here
							console.log(err);
						},
					});
				} else {
					console.log("Invalid date range format.");
				}
			}
		});

		$(document).on("click", "#booking-register", function (e) {
			e.preventDefault();

			const $form = $("#hotel-room-listing");
			$("#booking-register").addClass("booking-disabled");

			// Check if form is valid
			if ($form[0].checkValidity() === false) {
				// $form.find(':input').each(function() {
				// 	console.log(this.id + ' is valid: ' + this.checkValidity());
				// });
				e.stopPropagation(); // Stop further handling of the click event
				$form.addClass("was-validated"); // Optional: for Bootstrap validation styling
				$("#booking-register").removeClass("booking-disabled");
				return; // Do not proceed to AJAX if validation fails
			}

			var staylodgic_roomlistingbox_nonce = $(
				'input[name="staylodgic_roomlistingbox_nonce"]'
			).val();
			let data_booking_number =
				$("#reservation-data").data("bookingnumber");
			console.log("booking-number:" + data_booking_number);

			let data_full_name = $(
				".registration_form_inputs #full_name"
			).val();
			let data_passport = $(".registration_form_inputs #passport").val();
			let data_email_address = $(
				".registration_form_inputs #email_address"
			).val();
			let data_phone_number = $(
				".registration_form_inputs #phone_number"
			).val();
			let data_street_address = $(
				".registration_form_inputs #street_address"
			).val();
			let data_city = $(".registration_form_inputs #city").val();
			let data_state = $(".registration_form_inputs #state").val();
			let data_zip_code = $(".registration_form_inputs #zip_code").val();
			let data_country = $(".registration_form_inputs #country").val();
			let data_guest_comment = $(
				".registration_form_inputs #guest_comment"
			).val();
			let data_guest_consent = $(
				".registration_form_inputs #guest_consent"
			).val();

			// Serialize form data
			const registration_data = $form.serialize();

			//console.log(checkin, checkout, rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: "POST",
				data: {
					action: "book_rooms",
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
					staylodgic_roomlistingbox_nonce:
						staylodgic_roomlistingbox_nonce,
				},
				success: function (response) {
					// handle success
					if (response.success) {
						// handle success
						$("#bookingResponse")
							.show()
							.removeClass("error")
							.addClass("success")
							.text("Booking successfully registered.");
						$(".registration_request").remove();
						$(".registration_successful").show();
					} else {
						// handle error
						$("#bookingResponse")
							.removeClass("success")
							.addClass("error")
							.text(response.data);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					// handle network errors, bad URLs, etc.
					$("#bookingResponse")
						.removeClass("success")
						.addClass("error")
						.text(errorMessage);
				},
			});
		});

		// ***********************
		// ****** Activities
		// ***********************

		// Process room choice and registration
		$(document).on(
			"click",
			"#activity-data .time-slot.time-active:not(.time-slot-unavailable)",
			function () {
				$(".time-slot").removeClass("time-choice");
				$(this).addClass("time-choice");
				var chosenActivity = $(this).data("activity");
				var activityChoice = $(this).closest(".activity-schedule");
				processActivityData(activityChoice);
			}
		);

		// Process Room Data
		function processActivityData(activityChoice) {
			var staylodgic_roomlistingbox_nonce = $(
				'input[name="staylodgic_roomlistingbox_nonce"]'
			).val();
			var bookingnumber = $("#activity-data").data("bookingnumber");
			var activityId = activityChoice
				.find(".time-choice")
				.data("activity");
			var activityDate = $("#activity-data").data("checkin");
			var activityTime = activityChoice.find(".time-choice").data("time");
			var activityPriceTotal = activityChoice
				.find(".activity-rate")
				.data("activityprice");

			console.log(
				staylodgic_roomlistingbox_nonce,
				bookingnumber,
				activityId,
				activityDate,
				activityTime,
				activityPriceTotal
			);

			var dataToSend = {
				action: "process_selected_activity",
				bookingnumber: bookingnumber,
				activity_id: activityId,
				activity_date: activityDate,
				activity_time: activityTime,
				activity_price: activityPriceTotal,
				staylodgic_roomlistingbox_nonce:
					staylodgic_roomlistingbox_nonce,
			};

			$.ajax({
				type: "POST",
				url: frontendAjax.ajaxurl,
				data: dataToSend,
				success: function (response) {
					// Handle the response from the server
					$("#booking-summary").html(response);

					$("#activity-data").velocity("fadeOut", {
						duration: 350,
						complete: function () {
							$(".registration_request").velocity("fadeIn");
						},
					});
					console.log(response);
					// You can update the page content or perform other actions here
				},
			});
		}
		// Frontend codes
		$("#activitySearch").on("click", function (e) {
			// Changed here
			e.preventDefault();

			$("#activitySearch").addClass("booking-disabled");
			$('.available-list').fadeOut();

			// Retrieve the date from the input field
			var inputVal = $("#activity-reservation-date").val();
			var dates = inputVal.split(" to ");

			if (inputVal == "") {
				console.log("One");
				// Only one date in input field, get date from #check-in-display
				// var checkInDateStr = $('#check-in-display span').text();
				// checkInDate = new Date(checkInDateStr);
				// checkOutDate = new Date(checkInDateStr); // Use the same date for check-out
				// updateSelectedDates(checkInDate, checkOutDate);
				// var formattedCheckIn = formatDateToYYYYMMDD(checkInDate);
				// var formattedCheckOut = formatDateToYYYYMMDD(checkOutDate);
				// reservationDate = formattedCheckIn + ' to ' + formattedCheckOut;
				// console.log( reservationDate );
				$("#activitySearch").removeClass("booking-disabled");
			} else {
				var checkInDate, checkOutDate;
				var reservationDate;

				reservationDate = $("#activity-reservation-date").val();

				var stay_booking_number = $("#booking-number").val();
				var numberOfAdults = $("#number-of-adults").val();
				var numberOfChildren = $("#number-of-children").val();
				var staylodgic_searchbox_nonce = $(
					'input[name="staylodgic_searchbox_nonce"]'
				).val();

				var childrenAge = [];

				// Loop through all select elements with the class 'children-age-selector'
				$('#guest-age input[name="children_age[]"]').each(function () {
					childrenAge.push($(this).val());
				});

				$.ajax({
					url: frontendAjax.ajaxurl, // the localized URL
					type: "POST",
					data: {
						action: "get_activity_frontend_schedules",
						selected_date: reservationDate,
						number_of_adults: numberOfAdults,
						number_of_children: numberOfChildren,
						children_age: childrenAge,
						staylodgic_searchbox_nonce: staylodgic_searchbox_nonce,
					},
					success: function (response) {
						$("#available-list-ajax").html(response.data);
						$("#available-list-ajax").show();

						$("#activitySearch").removeClass("booking-disabled");
						$('.available-list').fadeIn();
					},
					error: function (err) {
						// Handle error here
						console.log(err);
					},
				});
			}
		});

		$(document).on("click", "#activity-register", function (e) {
			e.preventDefault();

			const $form = $("#hotel-acitivity-listing");

			// Check if form is valid
			if ($form[0].checkValidity() === false) {
				// $form.find(':input').each(function() {
				// 	console.log(this.id + ' is valid: ' + this.checkValidity());
				// });
				e.stopPropagation(); // Stop further handling of the click event
				$form.addClass("was-validated"); // Optional: for Bootstrap validation styling
				return; // Do not proceed to AJAX if validation fails
			}

			var staylodgic_roomlistingbox_nonce = $(
				'input[name="staylodgic_roomlistingbox_nonce"]'
			).val();
			let data_booking_number = $("#activity-data").data("bookingnumber");
			console.log("booking-number:" + data_booking_number);

			let data_full_name = $(
				".registration_form_inputs #full_name"
			).val();
			let data_passport = $(".registration_form_inputs #passport").val();
			let data_email_address = $(
				".registration_form_inputs #email_address"
			).val();
			let data_phone_number = $(
				".registration_form_inputs #phone_number"
			).val();
			let data_street_address = $(
				".registration_form_inputs #street_address"
			).val();
			let data_city = $(".registration_form_inputs #city").val();
			let data_state = $(".registration_form_inputs #state").val();
			let data_zip_code = $(".registration_form_inputs #zip_code").val();
			let data_country = $(".registration_form_inputs #country").val();
			let data_guest_comment = $(
				".registration_form_inputs #guest_comment"
			).val();
			let data_guest_consent = $(
				".registration_form_inputs #guest_consent"
			).val();

			// Serialize form data
			const registration_data = $form.serialize();

			//console.log(checkin, checkout, rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: "POST",
				data: {
					action: "book_activity",
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
					staylodgic_roomlistingbox_nonce:
						staylodgic_roomlistingbox_nonce,
				},
				success: function (response) {
					// handle success
					if (response.success) {
						// handle success
						$("#bookingResponse")
							.show()
							.removeClass("error")
							.addClass("success")
							.text("Booking successfully registered.");
						$(".registration_request").remove();
						$(".registration_successful").show();
					} else {
						// handle error
						$("#bookingResponse")
							.removeClass("success")
							.addClass("error")
							.text(response.data);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					// handle network errors, bad URLs, etc.
					$("#bookingResponse")
						.removeClass("success")
						.addClass("error")
						.text(errorMessage);
				},
			});
		});
	});
})(jQuery);
