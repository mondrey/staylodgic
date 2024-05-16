<?php
/*
* Category List
*/
get_header();
$blog_category_style   = atollmatrix_get_option_data( 'blog_category_style' );
$blog_grid_achivestyle = atollmatrix_get_option_data( 'blog_grid_achivestyle' );
$posts_per_page        = get_option( 'posts_per_page' );
$pagestyle             = '';
if ( '' === $blog_grid_achivestyle ) {
	$blog_grid_achivestyle = '3';
}
if ( is_active_sidebar( 'default_sidebar' ) ) {
	if ( 'grid' !== $blog_category_style ) {
		$pagestyle = ' float-left two-column';
	}
}
?>
<div class="contents-wrap<?php echo esc_attr( $pagestyle ); ?>">
	<div class="entry-content-wrapper entry-content">
<?php
$cat_term = get_queried_object();
if ( ! isset( $cat_term->term_id ) ) {
	$cat_slug = '';
} else {
	$cat_slug = $cat_term->slug;
}
if ( ! isset( $cat_term->category_description ) ) {
	$cat_desc = '';
} else {
	$cat_desc = $cat_term->category_description;
}
$read_more = atollmatrix_get_option_data( 'read_more' );

if ( 'grid' === $blog_category_style ) {
	if ( '' !== $cat_desc ) {
		echo '<div class="blog-category-desc">';
		echo esc_html( $cat_desc );
		echo '</div>';
	}
}
if ( 'grid' !== $blog_category_style ) {
	if ( have_posts() ) :
		get_template_part( 'loop', 'category' );
	endif;
}
if ( 'grid' === $blog_category_style ) {
	echo '<div class="detect-isotope">';
	echo do_shortcode( '[gridcreate grid_post_type="blog" category_display="no" effect="default" style="classic" columns="' . esc_attr( $blog_grid_achivestyle ) . '" format="landscape" worktype_slugs="' . esc_attr( $cat_slug ) . '" title="true" desc="true" pagination="true" limit="' . esc_attr( $posts_per_page ) . '"]' );
	echo '</div>';
}
?>
</div>
</div>
<?php
if ( 'grid' !== $blog_category_style ) {
	get_sidebar();
}
get_footer();
