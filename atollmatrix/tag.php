<?php
/*
*  Tag page
*/
if (!current_user_can('manage_options')) {
	// Display the content for admin users
	echo '<p>You do not have permission to view this content.</p>';

	return;
}
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
