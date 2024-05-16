<?php
if ( has_post_thumbnail() ) {
	echo '<div class="post-format-media">';
	$posthead_size = 'atollmatrix-gridblock-full';

	atollmatrix_display_post_image( $post->ID, $have_image_url = false, $gen_link = false, $imagesize_type = $posthead_size, $post->post_title, $class = '', $lazyload = true );
	echo '</div>';
}
