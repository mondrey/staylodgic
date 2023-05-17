(function ($) {
	$(document).ready(function () {
		$('#menu-to-edit')
			.on('click', '.menu-item .set-post-thumbnail', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var item_id = $(this).data('imageid'),
					uploader = wp.media({
						title: menuImage.l10n.uploaderTitle, // todo: translate
						button: { text: menuImage.l10n.uploaderButtonText },
						multiple: false
					}).on('select', function () {
						var attachment = uploader.state().get('selection').first().toJSON();
						var thumbnail = attachment.sizes.thumbnail.url;
						//menuImageUpdate( item_id, attachment.id );
						$('#menu-item-image-id-'+item_id).val( attachment.id );
						$('.menu-item-image-wrap-'+item_id).addClass('image-present').removeClass('image-absent');
						$('.menu-item-image-wrap-'+item_id+' .menu-item-image').attr('src', thumbnail);
					}).open();
			})
			.on('click', '.menu-item .remove-post-thumbnail', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var item_id = $(this).data('imageid');
				$('.menu-item-image-wrap-'+item_id).removeClass('image-present').addClass('image-absent');
				$('.menu-item-image-wrap-'+item_id+' .menu-item-image').attr('src', '');
				$('#menu-item-image-id-'+item_id).val( '' );
			});
	});
})(jQuery);
