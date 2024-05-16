<?php
/*
*  Page
*/
?>
<?php get_header(); ?>
<?php
if ( post_password_required() ) {
	echo '<div class="entry-content" id="password-protected">';
		atollmatrix_display_password_form_action();
	echo '</div>';
} else {
	$twocolumn_class  = '';
	$floatside        = '';
	$mtheme_pagestyle = 'nosidebar';

	$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_id() );
	if ( ! isset( $mtheme_pagestyle ) || '' === $mtheme_pagestyle || empty( $mtheme_pagestyle ) ) {
		$mtheme_pagestyle = 'edge-to-edge';
	}
	$sidebar_present = false;
	if ( 'nosidebar' === $mtheme_pagestyle ) {
		$floatside = '';
	}
	if ( 'rightsidebar' === $mtheme_pagestyle ) {
		$floatside       = 'float-left';
		$sidebar_present = true;
	}
	if ( 'leftsidebar' === $mtheme_pagestyle ) {
		$floatside       = 'float-right';
		$sidebar_present = true;
	}
	if ( $sidebar_present ) {
		$twocolumn_class = 'two-column';
	}
	if ( 'edge-to-edge' === $mtheme_pagestyle ) {
		$floatside        = '';
		$mtheme_pagestyle = 'nosidebar';
	}
	$mtheme_sidebar_choice = atollmatrix_page_has_sidebar( get_the_id() );
	if ( ! $mtheme_sidebar_choice ) {
		$sidebar_present  = false;
		$twocolumn_class  = '';
		$floatside        = '';
		$mtheme_pagestyle = 'nosidebar';
	}
	?>
	<div class="page-contents-wrap <?php echo esc_attr( $floatside ); ?> <?php echo esc_attr( $twocolumn_class ); ?>">
		<?php
		get_template_part( 'loop', 'page' );
		?>
	</div>
	<?php
	if ( $sidebar_present ) {
		get_sidebar();
	}
}
get_footer();
?>
