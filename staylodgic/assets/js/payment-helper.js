(function ($) {
	$(document).ready(function () {

		$(document).on('click', '#woo-bookingpayment', function(e) {
			e.preventDefault();

			let $form = $('#hotel-room-listing');
			if ($form.length === 0) {
				$form = $('#hotel-acitivity-listing');
			}

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

			var staylodgic_roomlistingbox_nonce = $(
				'input[name="staylodgic_roomlistingbox_nonce"]'
			).val();
			console.log(total);
			// Send an AJAX request to trigger the server-side function
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'process_reservation_payment',
					total: total,
					booking_number: booking_number,
					staylodgic_roomlistingbox_nonce: staylodgic_roomlistingbox_nonce
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
