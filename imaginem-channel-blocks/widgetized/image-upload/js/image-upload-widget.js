jQuery(document).ready(function($){
    console.log('loaded');
    $('body').on('click','.custom_media_upload',function(e){
        console.log('clicked');
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
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            console.log(button_clicked.parent());
            button_clicked.parent().find('.custom_media_url').val(image_url).trigger( 'change' );
            button_clicked.parent().find('.custom_media_image').attr("src", image_url);
        });
    });
});