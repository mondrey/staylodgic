<?php
/**
 *  loop that displays a page.
 */
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
			<?php
			if ( comments_open() ) {
				echo '<div class="commentform-wrap-page">';
				comments_template();
				echo '</div>';
			}
			?>
		</div><!-- .entry-content -->
		<?php
	endwhile;
endif;
?>
