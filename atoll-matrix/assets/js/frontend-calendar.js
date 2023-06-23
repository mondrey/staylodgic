(function ($) {
	$(document).ready(function () {


		flatpickr("#reservation-date", {
			mode: "range",
			dateFormat: "Y-m-d",
			showMonths: 2,
			enableTime: false
		});

		$("#number-of-children").change(function() {
			// Remove old selectors if exists
			$(".children-selector-wrap").remove();
	
			var numberOfChildren = $(this).val();
			var maxAge = $(this).parent().data('agelimitofchild');
			
			for (var j = 0; j < numberOfChildren; j++) {
				var select = $('<select class="children-age-selector" name="children_age_' + (j+1) + '"></select>');
	
				for(var i = 0; i <= maxAge; i++) {
					select.append('<option value="' + i + '">' + i + '</option>');
				}
				
				var wrapper = $('<div class="children-selector-wrap"></div>');
				wrapper.append(select);
				$(this).parent().append(wrapper);
			}
		});

		// Frontend codes
		$('#bookingSearch').on('click', function(e) { // Changed here
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
				success: function(response) {
					// handle response
					//console.log(response);
					$('#available-list-ajax').html(response);
				},
				error: function(err) {
					// Handle error here
					console.log(err);
				}
			});
		});

		$(document).on('click', '#bookingRegister', function(e) {
			e.preventDefault();
	
			let booking_number = $('#reservation-data').data('bookingnumber');
			console.log( 'booking-number:' + booking_number );
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
	
			$('#available-list-ajax div').each(function() {
				let roomId = $(this).data('room-id');
				let roomQuantity = $(this).find('select[name="room_quantity"]').val();
				
				if(roomId && roomQuantity > 0) {
					rooms.push({id: roomId, quantity: roomQuantity});
				}
			});
			console.log(checkin,checkout,rooms);
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type : 'POST',
				data : {
					action : 'bookRooms',
					booking_number : booking_number,
					checkin : checkin,
					checkout : checkout,
					rooms : rooms,
					full_name : full_name,
					email_address : email_address,
					phone_number : phone_number,
					street_address : street_address,
					city : city,
					state : state,
					zip_code : zip_code,
					country : country,
					nonce: frontendAjax.nonce // Our defined nonce
				},
				success : function(response) {
					// handle success
					if(response.success) {
						// handle success
						$('#bookingResponse').removeClass('error').addClass('success').text('Booking successfully registered.');
					} else {
						// handle error
						$('#bookingResponse').removeClass('success').addClass('error').text(response.data);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// handle network errors, bad URLs, etc.
					$('#bookingResponse').removeClass('success').addClass('error').text(errorMessage);
				}
			});
		});

	});
})(jQuery);
