<?php
if ( has_post_thumbnail() ) {

	echo '<div class="post-format-media">';

	$posthead_size = 'atollmatrix-gridblock-full';
	$link_added    = false;

	if ( is_singular( 'post' ) ) {

		if ( atollmatrix_get_option_data( 'postformat_imagelightbox' ) ) {

			$image_link = atollmatrix_featured_image_link( $post->ID );
			$imageid    = get_post_thumbnail_id();
			$imagedata  = get_post( $imageid );
			$imagetitle = $imagedata->post_title;

			echo '<a class="lightbox-active lightbox-image postformat-image-lightbox" data-elementor-open-lightbox="no" data-sub-html="' . esc_attr( '<h4 class="lightbox-text">' . $imagetitle . '</h4>' ) . '" data-src="' . esc_url( $image_link ) . '" href="' . esc_url( $image_link ) . '">';
			echo '<span class="lightbox-indicate"><i class="feather-icon-maximize"></i></span>';
			$link_added = true;

		}

	} else {
		echo '<a href="' . esc_url( get_permalink() ) . '">';
		$link_added = true;
	}
	atollmatrix_display_post_image_srcset( $post->ID, $have_image_url = false, $gen_link = false, $imagesize_type = $posthead_size, $post->post_title, $class = '', $lazyload = true );
	if ( $link_added ) {
		echo '</a>';
	}
	echo '</div>';
}
