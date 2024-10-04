(function ($) {
    $(document).ready(function () {
        "use strict";

        $('.flatpickr-date-time').flatpickr({
            enableTime: true, // Enable time picker
            dateFormat: "Y-m-d H:i", // Set the format to include date and time
            // Additional options can be added here as needed
        });

        $('#submitregistration').click(function(e){
            e.preventDefault();


			const $form = $('#guestregistration');
			
            $('#submitregistration').addClass('booking-disabled');

			// Check if form is valid
			if ($form[0].checkValidity() === false) {
				// $form.find(':input').each(function() {
				// 	console.log(this.id + ' is valid: ' + this.checkValidity());
				// });
				e.stopPropagation(); // Stop further handling of the click event
				$form.addClass('was-validated'); // Optional: for Bootstrap validation styling
				$('#submitregistration').removeClass('booking-disabled');
				return; // Do not proceed to AJAX if validation fails
			}
    
            var form_data = {};
            var guestId = $(this).data('guest'); // Fetch the guest ID from the button

            $('form#guestregistration input, form#guestregistration select').each(function() {
                var label = $(this).data('label');
                var id = $(this).data('id');
                // Use element's nodeName to determine if it's an input or select and set type accordingly
                var type = $(this).attr('type') || this.nodeName.toLowerCase();
                var value;
            
                if (type === 'checkbox') {
                    // For checkbox, use 'true' if checked, 'false' otherwise
                    value = $(this).prop('checked'); // true or false
                } else {
                    // For other inputs and selects, just use the value
                    value = $(this).val();
                }
            
                form_data[id] = {
                    value: value, // Will be true or false for checkboxes, and the actual value for other inputs/selects
                    type: type, // Now 'select' for <select> elements, correct type for inputs, or 'undefined' for other cases
                    label: label
                };
            });
                      
    
            // Get signature data
            var signaturePadCanvas = document.getElementById('signature-pad');
            var signatureData = signaturePadCanvas.toDataURL('image/png');

            // Prepare the data to be sent in the AJAX request
            var ajaxData = {
                nonce: frontendAjax.nonce,
                action: 'save_guestregistration_data',
                booking_data: form_data,
                signature_data: signatureData,
                post_id: frontendAjax.post_id,
            };

            // Include guestId only if it is set
            if (guestId) {
                ajaxData.guest_id = guestId;
            }

            $.ajax({
                type: 'POST',
                url: frontendAjax.ajaxurl,
                data: ajaxData,
                success: function(response){
                    console.log('Data saved successfully:', response);
                    
                    $('.registration-column-one').html( response );
                },
                error: function(error){
                    console.log('Error saving data:', error);
                }
            });
        });      
    });
})(jQuery);
