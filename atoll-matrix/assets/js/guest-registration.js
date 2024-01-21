(function ($) {
    $(document).ready(function () {
        "use strict";

        $('#submitregistration').click(function(e){
            e.preventDefault();
    
            var formData = {};
            $('form input, form select').each(function(){
                var label = $(this).data('label');
                var value = $(this).val();
                formData[label] = value;
            });
    
            // Get signature data
            var signaturePadCanvas = document.getElementById('signature-pad');
            var signatureData = signaturePadCanvas.toDataURL('image/png');

            $.ajax({
                type: 'POST',
                url: frontendAjax.ajaxurl,
                data: {
                    nonce: frontendAjax.nonce,
                    action: 'save_guestregistration_data',
                    booking_data: formData,
                    signature_data: signatureData,
                    post_id: frontendAjax.post_id,
                },
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
