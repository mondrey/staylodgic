<div id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
<?php
$postformat = atollmatrix_get_postformat();
$top_class  = '';
echo '<div class="blog-small-left">';
get_template_part( 'template-parts/postformats/postformat-media' );
echo '</div>';
echo '<div class="blog-small-right">';
if ( ! isset( $count ) ) {
	$count = 0;
}
$count++;
if ( 1 === $count ) {
	$top_class = 'topseperator';
}
?>
<div class="<?php echo esc_attr( $top_class ); ?> entry-wrapper post-<?php echo esc_attr( $postformat ); ?>-wrapper clearfix">
	<div class="blog-content-section">
<?php
$the_content_class = 'post-display-excerpt';
if ( atollmatrix_get_option_data( 'postformat_fullcontent' ) || is_singular() ) {
	$the_content_class = 'post-display-content';
}
?>
<div class ="entry-content postformat_contents <?php echo esc_attr( $the_content_class ); ?> clearfix">
<?php
$show_readmore   = false;
$blogpost_style  = atollmatrix_get_pagestyle( get_the_id() );
$postformat_icon = atollmatrix_get_postformat_icon( $postformat );

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
if ( ! is_search() ) {
	get_template_part( 'template-parts/postformats/post', 'data' );
}
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
?>
<?php
if ( true === $show_readmore ) {
	echo '<div class="button-blog-continue">
	<a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( atollmatrix_get_option_data( 'read_more' ) ) . '</a>
	</div>';
}
?>
</div>
</div>
	</div>
</div>
</div>
