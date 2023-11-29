(function ($) {
	$(document).ready(function () {

		$(document).on('click', '#woo-bookingpayment', function(e) {
			e.preventDefault();
			// Get the total value from the form
			var total = $(this).data("paytotal");
			var booking_number = $(this).data("bookingnumber");
			console.log(total);
			// Send an AJAX request to trigger the server-side function
			$.ajax({
				url: frontendAjax.ajaxurl, // the localized URL
				type: 'POST',
				data: {
					action: 'processReservation',
					total: total,
					booking_number: booking_number
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
