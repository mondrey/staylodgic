<?php
/**
 * Archive
 *
 */
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
