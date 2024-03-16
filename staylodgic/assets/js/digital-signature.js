(function ($) {
    $(document).ready(function () {
        "use strict";

        function updateSignatureData(signaturePad, signatureDataField) {
            console.log('Updating signature');
            if (signaturePad && !signaturePad.isEmpty()) {
                signatureDataField.value = signaturePad.toDataURL();
            }
        }

        function loadImageOnCanvas(imageURL, canvas, signaturePad) {
            var ctx = canvas.getContext('2d');
            var img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                signaturePad.fromDataURL(imageURL); // Update signaturePad with the loaded image
            };
            img.src = imageURL;
        }

        function initializeSignaturePad() {
            var signaturePadCanvas = document.getElementById('signature-pad');
            if (signaturePadCanvas) {
                var signaturePad = new SignaturePad(signaturePadCanvas);
                var signatureDataField = document.getElementById('signature-data');
                var clearButton = document.getElementById('clear-signature');
                var predefinedImageURL = signaturePadCanvas.getAttribute('data-signature-image-url');

                // Load predefined image if available
                if (predefinedImageURL) {
                    loadImageOnCanvas(predefinedImageURL, signaturePadCanvas, signaturePad);
                }

                // Clear signature pad
                clearButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    signaturePad.clear();
                    signatureDataField.value = '';
                });

                signaturePad.addEventListener("endStroke", () => {
                    updateSignatureData(signaturePad, signatureDataField);
                });
            }
        }

        // Initialize the signature pad
        initializeSignaturePad();
    });
})(jQuery);
