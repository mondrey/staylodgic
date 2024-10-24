(function ($) {
	$(document).ready(function () {

		$('#invoiceActivityDetails').on('click', function (e) {
			e.preventDefault();
			var stay_booking_number = $('#booking_number').val();
			var staylodgic_bookingdetails_nonce = $('input[name="staylodgic_bookingdetails_nonce"]').val();

			if (!stay_booking_number) {
				alert('Please enter a booking number.');
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'html', // Change dataType to html
				data: {
					action: 'get_invoice_activity_details',
					booking_number: stay_booking_number,
					staylodgic_bookingdetails_nonce: staylodgic_bookingdetails_nonce
				},
				success: function (response) {
					// Directly use the HTML response
					$('#booking-details-ajax').html(response);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('Error fetching booking details:', textStatus, errorThrown);
					$('#booking-details-ajax').html('Error fetching booking details. Please try again.');
				}
			});


		});

		$('#invoiceDetails').on('click', function (e) {
			e.preventDefault();

			var stay_booking_number = $('#booking_number').val();
			var staylodgic_bookingdetails_nonce = $('input[name="staylodgic_bookingdetails_nonce"]').val();

			if (!stay_booking_number) {
				alert('Please enter a booking number.');
				return;
			}

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'html', // Change dataType to html
				data: {
					action: 'get_invoice_booking_details',
					booking_number: stay_booking_number,
					staylodgic_bookingdetails_nonce: staylodgic_bookingdetails_nonce
				},
				success: function (response) {
					// Directly use the HTML response
					$('#booking-details-ajax').html(response);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('Error fetching booking details:', textStatus, errorThrown);
					$('#booking-details-ajax').html('Error fetching booking details. Please try again.');
				}
			});


		});

		$(document).on('click', '#save-pdf-ticket-button', function (e) {
			e.preventDefault();
			// Convert HTML to Canvas
			var postid = $(this).data('postid'); // Get the booking number from the button's data-id attribute
			var stay_booking_number = $(this).data('bookingnumber'); // Get the booking number from the button's data-id attribute
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

		$(document).on('click', '#save-pdf-invoice-button', function (e) {
			e.preventDefault();
			// Convert HTML to Canvas
			var stay_booking_number = $(this).data('id'); // Get the booking number from the button's data-id attribute
			var bookingFile = $(this).data('file'); // Get the booking number from the button's data-id attribute
			// Target the specific invoice container matching the booking number
			html2canvas(document.querySelector('.invoice-container[data-bookingnumber="' + stay_booking_number + '"]'), { scale: 2 }).then(canvas => {
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


		$(document).on('click', '.print-invoice-button', function (e) {
			e.preventDefault();
			var stay_booking_number = $(this).data('id'); // Get the booking number from the button's data-id attribute
			var bookingTitle = $(this).data('title'); // Get the booking title from the button's data-title attribute

			// Find the invoice container that matches the booking number
			var invoiceContent = $('.invoice-container[data-bookingnumber="' + stay_booking_number + '"]').html();

			// Create a new window or tab for printing
			var printWindow = window.open('', '_blank', 'width=800,height=600');
			printWindow.document.open();
			printWindow.document.write('<html><head><title>' + bookingTitle + '</title>');

			// Include the external CSS file for styling the print document
			printWindow.document.write('<link rel="stylesheet" type="text/css" href="' + staylodgicData.pluginUrl + 'admin/css/invoice.css">');

			printWindow.document.write('</head><body>');
			printWindow.document.write(invoiceContent);
			printWindow.document.write('</body></html>');
			printWindow.document.close();

			// Use a slight delay to ensure the document is fully loaded before printing
			setTimeout(function () {
				printWindow.print();
				printWindow.close();
			}, 500);
		});


	});
})(jQuery);
