(function ($) {
	$(document).ready(function () {

		$('#invoiceActivityDetails').on('click', function(e) {
			e.preventDefault();
			console.log('sdkfjhsdkjfhksdjfh');
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
					action: 'getInvoiceActivityDetails',
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

		$(document).on('click', '#save-pdf-ticket-button', function(e) {
			e.preventDefault();
			// Convert HTML to Canvas
			var postid = $(this).data('postid'); // Get the booking number from the button's data-id attribute
			var bookingNumber = $(this).data('bookingnumber'); // Get the booking number from the button's data-id attribute
			var bookingFile = $(this).data('file'); // Get the booking number from the button's data-id attribute
			// Target the specific invoice container matching the booking number
			html2canvas(document.querySelector('.ticket-container-outer'), { scale: 2 }).then(canvas => {
				// Canvas dimensions
				const canvasWidth = canvas.width;
				const canvasHeight = canvas.height;
			
				// Convert dimensions to millimeters (1 mm = 3.78 pixels)
				const pdfWidth = canvasWidth / 3.78;
				const pdfHeight = canvasHeight / 3.78;
			
				// Initialize jsPDF with custom dimensions
				var doc = new window.jspdf.jsPDF({
					orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
					unit: 'mm',
					format: [pdfWidth, pdfHeight]
				});
			
				// Add canvas as image
				var imgData = canvas.toDataURL('image/png');
				doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
			
				// Save the PDF
				doc.save(bookingFile + '.pdf');
			});
			
		});

		$(document).on('click', '#save-pdf-invoice-button', function(e) {
			e.preventDefault();
			// Convert HTML to Canvas
			var bookingNumber = $(this).data('id'); // Get the booking number from the button's data-id attribute
			var bookingFile = $(this).data('file'); // Get the booking number from the button's data-id attribute
			// Target the specific invoice container matching the booking number
			html2canvas(document.querySelector('.invoice-container[data-bookingnumber="' + bookingNumber + '"]'), { scale: 2 }).then(canvas => {
				// Canvas dimensions
				const canvasWidth = canvas.width;
				const canvasHeight = canvas.height;
			
				// Convert dimensions to millimeters (1 mm = 3.78 pixels)
				const pdfWidth = canvasWidth / 3.78;
				const pdfHeight = canvasHeight / 3.78;
			
				// Initialize jsPDF with custom dimensions
				var doc = new window.jspdf.jsPDF({
					orientation: pdfWidth > pdfHeight ? 'landscape' : 'portrait',
					unit: 'mm',
					format: [pdfWidth, pdfHeight]
				});
			
				// Add canvas as image
				var imgData = canvas.toDataURL('image/png');
				doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
			
				// Save the PDF
				doc.save(bookingFile + '.pdf');
			});
			
		});
				

		$(document).on('click', '.print-invoice-button', function(e) {
			e.preventDefault();
			var bookingNumber = $(this).data('id'); // Get the booking number from the button's data-id attribute
			var bookingTitle = $(this).data('title'); // Get the booking number from the button's data-id attribute
			// Find the invoice container that matches the booking number
			var invoiceContent = $('.invoice-container[data-bookingnumber="' + bookingNumber + '"]').html();
			
			var printFrame = $('<iframe id="print-frame" style="display:none;"></iframe>').appendTo('body');
			
			var frameDoc = printFrame[0].contentWindow ? printFrame[0].contentWindow : printFrame[0].contentDocument.document ? printFrame[0].contentDocument.document : printFrame[0].contentDocument;
			frameDoc.document.open();
			frameDoc.document.write('<html><head><title>' + bookingTitle + '</title>');
			
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
