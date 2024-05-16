<?php
$postformat        = atollmatrix_get_postformat();
$postformat_icon   = atollmatrix_get_postformat_icon( $postformat );
$the_content_class = 'post-display-excerpt';
$show_readmore     = false;
$pagestyle         = atollmatrix_get_pagestyle( get_the_id() );

if ( atollmatrix_get_option_data( 'postformat_fullcontent' ) || is_singular() ) {
	$the_content_class = 'post-display-content';
}
?>
<div class="entry-content postformat_contents <?php echo esc_attr( $the_content_class ); ?> clearfix">
<?php
if ( is_singular( 'post' ) ) {
	if ( 'edge-to-edge' !== $pagestyle ) {
		get_template_part( 'template-parts/postformats/postformat-media' );
	}
} else {
	get_template_part( 'template-parts/postformats/postformat-media' );
}
?>
<div class="entry-blog-contents-wrap clearfix">
<?php
if ( ! is_single() ) {
	switch ( $postformat ) {
		case 'aside':
			break;
		case 'link':
			$linked_to   = get_post_meta( $post->ID, 'pagemeta_meta_link', true );
			$fullcontent = true;
			?>
			<div class="entry-post-title entry-post-title-only">
			<h2>
			<a class="postformat_<?php echo esc_attr( $postformat ); ?>" href="<?php echo esc_url( $linked_to ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
			</div>
			<?php
			break;
		case 'quote':
			break;
		default:
			?>
			<div class="entry-post-title">
			<h2>
			<a class="postformat_<?php echo esc_attr( $postformat ); ?>" href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
			</div>
			<?php
			break;
	}
}
?>
<?php
if ( 'quote' === $postformat ) {
	$quote        = get_post_meta( $post->ID, 'pagemeta_meta_quote', true );
	$quote_author = get_post_meta( $post->ID, 'pagemeta_meta_quote_author', true );
	$fullcontent  = true;
	if ( '' !== $quote ) {
		?>
		<span class="quote_say"><div class="quote-symbol"><i class="fa fa-quote-left"></i></div><?php echo esc_html( $quote ); ?></span>
		<?php
		if ( '' !== $quote_author ) {
			?>
			<span class="quote_author"><?php echo '&#8212;&nbsp;' . esc_html( $quote_author ); ?></span>
			<?php
		}
	}
}
?>
<?php
if ( ! is_search() && ! is_singular( 'post' ) ) {
	get_template_part( 'template-parts/postformats/post', 'data' );
}
if ( is_single() ) {
	$header_display_status = atollmatrix_get_page_header_status();
	echo '<div class="fullcontent-spacing">';
	echo '<article>';
	the_content();
	wp_link_pages(
		array(
			'before' => '<div class="page-link">' . esc_html__( 'Pages:', 'atollmatrix' ),
			'after'  => '</div>',
		)
	);
	echo '</article>';
	echo '</div>';
} else {
	if ( atollmatrix_get_option_data( 'postformat_fullcontent' ) ) {
		echo '<div class="postsummary-spacing blog-archive-full-content">';
		the_content();
		echo '</div>';
	} else {
		if ( 'link' !== $postformat && 'aside' !== $postformat && 'quote' !== $postformat ) {
			echo '<div class="postsummary-spacing">';
			the_excerpt();
			echo '</div>';
			$show_readmore = true;
		} else {
			echo '<div class="postsummary-spacing">';
			the_content();
			echo '</div>';
			$show_readmore = false;
		}
	}
}
?>
<?php
if ( true === $show_readmore ) {
	$haslink       = get_the_permalink();
	$continue_text = atollmatrix_get_option_data( 'read_more', 'Continue Reading' );
	$continue_tag  = atollmatrix_show_continue_reading( $continue_text, $haslink );
	echo wp_kses( $continue_tag, atollmatrix_get_allowed_tags() );
}
?>
</div>
<?php
if ( is_singular( 'post' ) ) {
	get_template_part( 'template-parts/postformats/post', 'data' );
}
?>
</div>
