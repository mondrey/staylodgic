<?php
if ( ! atollmatrix_is_fullscreen_home() && ! atollmatrix_is_fullscreen_post() && is_singular() && ! post_password_required() ) {
	$bg_choice = get_post_meta( get_the_id(), 'pagemeta_meta_background_choice', true );
	atollmatrix_featured_image_link( get_the_id() );

	if ( 'image_attachments' === $bg_choice ) {
		$bgchoice_page_id = get_the_id();
	}
	if ( 'options_slideshow' === $bg_choice ) {
		$bgchoice_page_id = atollmatrix_get_option_data( 'general_bgslideshow' );
	}
	if ( isset( $bgchoice_page_id ) ) {
		atollmatrix_populate_slide_ui_colors( $bgchoice_page_id );
	}
}
