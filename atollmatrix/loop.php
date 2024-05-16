<?php
/**
 * Loop
 *
 */
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<div class="entry-content-wrapper">
			<?php
			get_template_part( 'template-parts/post', 'summary' );
			?>
		</div>
		<?php
	endwhile;
	get_template_part( '/template-parts/paginate', 'navigation' );
endif;
?>
