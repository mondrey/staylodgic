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

		$(document).on('click', '#print-invoice-button', function() {
			var invoiceContent = $('.invoice-container').html();
			var printFrame = $('<iframe id="print-frame" style="display:none;"></iframe>').appendTo('body');
			
			var frameDoc = printFrame[0].contentWindow ? printFrame[0].contentWindow : printFrame[0].contentDocument.document ? printFrame[0].contentDocument.document : printFrame[0].contentDocument;
			frameDoc.document.open();
			frameDoc.document.write('<html><head><title>Invoice</title>');

			// Include the external CSS file
			frameDoc.document.write('<link rel="stylesheet" type="text/css" href="' + atollmatrixData.pluginUrl + 'admin/css/invoice.css">');

			frameDoc.document.write('</head><body>');
			frameDoc.document.write(invoiceContent);
			frameDoc.document.write('</body></html>');
			frameDoc.document.close();

			setTimeout(function () {
				frameDoc.window.print();
				printFrame.remove();
			}, 500);
		});

		
	});
})(jQuery);
