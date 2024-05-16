<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php
	get_template_part( 'template-parts/postformats/post', 'contents' );
?>
<?php
if ( is_single() ) {
	$display_authorbio = false;
	if ( atollmatrix_get_option_data( 'author_bio' ) ) {
		$display_authorbio = true;
	}
	$post_authorbio_status = get_post_meta( $post->ID, 'pagemeta_post_authorbio', true );
	if ( 'activate' === $post_authorbio_status ) {
		$display_authorbio = true;
	}
	if ( $display_authorbio ) {
		get_template_part( 'template-parts/author', 'bio' );
	}
}
?>
</div>
