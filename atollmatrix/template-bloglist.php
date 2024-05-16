<?php
/*
Template Name: Blog list
*/
?>
<?php get_header(); ?>
<?php
$twocolumn_class  = '';
$floatside        = '';
$mtheme_pagestyle = 'nosidebar';

$mtheme_pagestyle = atollmatrix_get_pagestyle( get_the_id() );
$sidebar_present  = false;
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
if ( 'nosidebar' === $mtheme_pagestyle ) {
	?>
	<div class="fullpage-contents-wrap">
	<?php
} else {
	?>
	<div class="contents-wrap <?php echo esc_attr( $floatside ); ?> two-column">
	<?php
}
if ( get_query_var( 'paged' ) ) {
	$blog_list_paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
	$blog_list_paged = get_query_var( 'page' );
} else {
	$blog_list_paged = 1;
}
$default_posts_per_page = get_option( 'posts_per_page' );
$args                   = array(
	'paged'          => $blog_list_paged,
	'posts_per_page' => $default_posts_per_page,
);

$postslist = new WP_Query( $args );
if ( $postslist->have_posts() ) :
	while ( $postslist->have_posts() ) :
		$postslist->the_post();
		?>
		<div class="entry-content-wrapper">
		<?php
		get_template_part( '/template-parts/post', 'summary' );
		?>
		</div>
		<?php
	endwhile;
		atollmatrix_pagination( $postslist->max_num_pages );
		wp_reset_postdata();
endif;
?>
</div>
<?php
if ( $sidebar_present ) {
	get_sidebar();
}
get_footer();
?>
