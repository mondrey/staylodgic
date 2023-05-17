jQuery(document).ready(function($){
    $('.aboutme_media_upload').on('click',function (e) {
        var button_clicked = $(this);
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_id = uploaded_image.toJSON().id;
            var image_url = uploaded_image.toJSON().sizes.thumbnail.url;
            // Let's assign the url value to the input field
            console.log(button_clicked.parent());
            button_clicked.parent().find('.aboutme_media_id').val(image_id).trigger('change');
            button_clicked.parent().find('.aboutme_media_image').attr("src", image_url);
        });
    });
});