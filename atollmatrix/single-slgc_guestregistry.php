<?php
/*
*  Portfolio Page
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
	?>
	<div class="page-contents-wrap <?php echo esc_attr( $floatside ); ?> <?php echo esc_attr( $twocolumn_class ); ?>">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-page-wrapper entry-content clearfix">
					<?php
					the_content();
					?>
				</div>
			</div><!-- .entry-content -->
			<?php
		endwhile;
	endif;
	?>
	</div>
	<?php
}
get_footer();
?>
