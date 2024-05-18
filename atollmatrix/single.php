<?php
/*
*  Single Page
*/
if (!current_user_can('manage_options')) {
	// Display the content for admin users
	echo '<p>You do not have permission to view this content.</p>';

	return;
}
get_header();

if ( post_password_required() ) {
	echo '<div class="entry-content" id="password-protected">';
		atollmatrix_display_password_form_action();
	echo '</div>';
} else {
	$sidebar_present  = false;
	$floatside        = '';
	$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_id() );
	if ( ! isset( $mtheme_pagestyle ) || '' === $mtheme_pagestyle ) {
		$mtheme_pagestyle = 'nosidebar';
	}
	if ( 'nosidebar' !== $mtheme_pagestyle ) {
		$floatside = 'float-left';
		if ( 'rightsidebar' === $mtheme_pagestyle ) {
			$sidebar_present = true;
			$floatside       = 'float-left two-column';
		}
		if ( 'leftsidebar' === $mtheme_pagestyle ) {
			$sidebar_present = true;
			$floatside       = 'float-right two-column';
		}
	} else {
		$floatside = 'fullwidth-column';
	}
	if ( post_password_required() ) {
		$floatside = 'fullwidth-column';
	}
	if ( 'edge-to-edge' === $mtheme_pagestyle ) {
		$floatside = '';
	}
	?>
	<div class="contents-wrap <?php echo esc_attr( $floatside ); ?>">
	<?php
	if (current_user_can('manage_options')) {
		// Display the content for admin users
		get_template_part( 'loop', 'single' );
	} else {
		// Display a message for non-admin users
		echo '<p>You do not have permission to view this content.</p>';
	}
	?>
	</div>
	<?php
	if ( $sidebar_present ) {
		get_sidebar();
	}
}
get_footer();
?>
