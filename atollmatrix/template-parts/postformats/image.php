<?php
if ( has_post_thumbnail() ) {
	echo '<div class="post-format-media">';

	$posthead_size   = 'atollmatrix-gridblock-full';
	$lightbox_status = get_post_meta( $post->ID, 'pagemeta_meta_lightbox', true );
	$image_link      = atollmatrix_featured_image_link( $post->ID );
	$open_link       = false;

	if ( '' !== $image_link ) {
		if ( 'enabled_lightbox' === $lightbox_status ) {
			echo '<a class="lightbox-active lightbox-image postformat-image-lightbox" data-elementor-open-lightbox="no" data-sub-html="' . esc_attr( '<h4 class="lightbox-text">' . $post->post_title . '</h4>' ) . '" data-src="' . esc_url( $image_link ) . '" href="' . esc_url( $image_link ) . '">';
			echo '<span class="lightbox-indicate"><i class="feather-icon-maximize"></i></span>';
			$open_link = true;
		} else {
			echo '<a href="' . esc_url( get_permalink() ) . '">';
			$open_link = true;
		}
	}
	atollmatrix_display_post_image_srcset( $post->ID, $have_image_url = false, $gen_link = false, $imagesize_type = $posthead_size, $post->post_title, $class = '', $lazyload = true );
	if ( $open_link ) {
		echo '</a>';
	}
	echo '</div>';
}
