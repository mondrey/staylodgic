(function ($) {
    $(document).ready(function () {
        "use strict";

        function updateSignatureData( signaturePad, signatureDataField ) {
            console.log( 'Updating signature ')
            if (signaturePad && !signaturePad.isEmpty()) {
                signatureDataField.value = signaturePad.toDataURL();
            }
        }
        function initializeSignaturePad() {
            var signaturePadCanvas = document.getElementById('signature-pad');
            if (signaturePadCanvas) {
                var signaturePad = new SignaturePad(signaturePadCanvas);
                var signatureDataField = document.getElementById('signature-data');
                var clearButton = document.getElementById('clear-signature');

                // Clear signature pad
                clearButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    signaturePad.clear();
                    signatureDataField.value = '';
                });

                signaturePad.addEventListener("endStroke", () => {
                    updateSignatureData( signaturePad, signatureDataField);
                });
        
                // $('.wpcf7-form').off('submit.signaturePad').on('submit.signaturePad', function () {
                //     if (signaturePad && !signaturePad.isEmpty()) {
                //         signatureDataField.value = signaturePad.toDataURL();
                //     }
                // });
            }
        }

        // Initialize the signature pad
        initializeSignaturePad();

        // Reinitialize if the form is loaded via AJAX
        // $(document).ajaxComplete(function () {
        //     initializeSignaturePad();
        // });
    });
})(jQuery);
