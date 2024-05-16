<?php
/*
*  Tag page
*/
get_header();
?>
<?php
$pagestyle = '';
if ( is_active_sidebar( 'default_sidebar' ) ) {
	$pagestyle = 'float-left two-column';
}
?>
<div class="contents-wrap <?php echo esc_attr( $pagestyle ); ?>">
	<?php
	rewind_posts();
	get_template_part( 'loop', 'tag' );
	?>
</div>
<?php
get_sidebar();
get_footer();
?>
