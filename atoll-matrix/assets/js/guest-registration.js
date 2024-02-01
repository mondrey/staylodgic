(function ($) {
    $(document).ready(function () {
        "use strict";

        $('#submitregistration').click(function(e){
            e.preventDefault();
    
            var formData = {};
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
            
                formData[id] = {
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
                booking_data: formData,
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
                },
                error: function(error){
                    console.log('Error saving data:', error);
                }
            });
        });      
    });
})(jQuery);
