<?php
/*
Template Name: Room Search Page
*/
?>
<?php get_header(); ?>
<?php
if ( post_password_required() ) {
	echo '<div class="entry-content" id="password-protected">';
		atollmatrix_display_password_form_action();
	echo '</div>';
} else {
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-page-wrapper entry-content clearfix">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-link">' . esc_html__( 'Pages:', 'atollmatrix' ),
						'after'  => '</div>',
					)
				);
				?>
				</div>			
			</div><!-- .entry-content -->
			<?php
		endwhile;
	endif;
}
get_footer();
?>
