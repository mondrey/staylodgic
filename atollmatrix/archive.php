<?php
/**
 * Archive
 *
 */
if (!current_user_can('manage_options')) {
	// Display the content for admin users
	echo '<p>You do not have permission to view this content.</p>';

	return;
}
get_header(); ?>
<div class="contents-wrap float-left two-column">
<?php
if ( have_posts() ) :
	get_template_part( 'loop', 'archive' );
endif;
?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
