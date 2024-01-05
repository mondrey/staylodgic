(function ($) {
	$(document).ready(function () {

		$('#invoiceDetails').on('click', function(e) {
			e.preventDefault();
	
			var bookingNumber = $('#booking_number').val();
			var atollmatrix_bookingdetails_nonce = $('input[name="atollmatrix_bookingdetails_nonce"]').val();

			if (!bookingNumber) {
				alert('Please enter a booking number.');
				return;
			}
	
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'html', // Change dataType to html
				data: {
					action: 'getInvoiceBookingDetails',
					booking_number: bookingNumber,
					atollmatrix_bookingdetails_nonce: atollmatrix_bookingdetails_nonce
				},
				success: function(response) {
					// Directly use the HTML response
					$('#booking-details-ajax').html(response);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error('Error fetching booking details:', textStatus, errorThrown);
					$('#booking-details-ajax').html('Error fetching booking details. Please try again.');
				}
			});
			
			
		});
		
	});
})(jQuery);
