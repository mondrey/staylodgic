<?php
$fullscreen_check = atollmatrix_get_option_data( 'fullcscreen_henable' );
if ( true === $fullscreen_check ) {
	get_template_part( '/template-parts/home', 'fullscreen' );
} else {
	get_header();
	$mtheme_pagelayout_type = 'nosidebar';
	$floatside              = '';
	if ( is_active_sidebar( 'default_sidebar' ) ) {
		$mtheme_pagelayout_type = 'two-column';
		$floatside              = 'float-left';
	}
	?>
	<div class="contents-wrap <?php echo esc_attr( $floatside ); ?> <?php echo esc_attr( $mtheme_pagelayout_type ); ?>">
		<?php
		$sticky_posts = get_option( 'sticky_posts' );
		if ( $sticky_posts ) {
			$args_sticky  = array( 'post__in' => $sticky_posts );
			$sticky_query = new WP_Query( $args_sticky );
			?>
			<div class="entry-content-wrapper post-is-sticky">
			<?php
			if ( $sticky_query->have_posts() ) :
				while ( $sticky_query->have_posts() ) :
					$sticky_query->the_post();
					get_template_part( '/template-parts/post', 'summary' );
				endwhile;
			endif;
			wp_reset_postdata();
			?>
			</div>
			<?php
		}
		if ( get_query_var( 'paged' ) ) {
			$curr_paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$curr_paged = get_query_var( 'page' );
		} else {
			$curr_paged = 1;
		}
		$default_posts_per_page = get_option( 'posts_per_page' );
		$args                   = array(
			'paged'               => $curr_paged,
			'posts_per_page'      => $default_posts_per_page,
			'ignore_sticky_posts' => 1,
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
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '<i class="ion-ios-arrow-thin-left"></i>',
					'next_text' => '<i class="ion-ios-arrow-thin-right"></i>',
				)
			);
			wp_reset_postdata();
		endif;
	?>
	</div>
	<?php
	get_sidebar();
	get_footer();
}
?>
