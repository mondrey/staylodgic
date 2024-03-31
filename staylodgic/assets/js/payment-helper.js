(function ($) {
	$(document).ready(function () {

		$(document).on('click', '#woo-bookingpayment', function(e) {
			e.preventDefault();

			const $form = $('#hotel-room-listing');

			// Check if form is valid
			if ($form[0].checkValidity() === false) {
				// $form.find(':input').each(function() {
				// 	console.log(this.id + ' is valid: ' + this.checkValidity());
				// });
				e.stopPropagation(); // Stop further handling of the click event
				$form.addClass('was-validated'); // Optional: for Bootstrap validation styling
				return; // Do not proceed to AJAX if validation fails
			}

			// Get the total value from the form
			var total = $(this).data("paytotal");
			var booking_number = $(this).data("bookingnumber");
			console.log(total);
			// Send an AJAX request to trigger the server-side function
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'processReservationPayment',
					total: total,
					booking_number: booking_number,
					nonce: staylodgic_admin_vars.nonce
				},
				success: function(response) {
					// Handle the success response
					if (response.success) {
						// Redirect to the WooCommerce checkout page
						window.location.href = response.data.redirect_url;
					} else {
						// Display the error message
						alert(response.data.error_message);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					// Handle the error response
					console.log('Error processing reservation.');
					console.log('jqXHR:', jqXHR);
					console.log('textStatus:', textStatus);
					console.log('errorThrown:', errorThrown);
				}
			});
		});
		


	});
})(jQuery);
