<?php
$postformat = get_post_format();
if ( '' === $postformat ) {
	$postformat = 'standard';
}
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<div class="post-<?php echo esc_attr( $postformat ); ?>-wrapper post-contents-wrap">
			<?php
			get_template_part( 'template-parts/postformats/default' );
			if ( atollmatrix_get_option_data( 'postsingle_navigation' ) ) {
				get_template_part( 'template-parts/post-navigate' );
			}
			comments_template();
			?>
		</div>
		<?php
	endwhile;
endif;
?>
