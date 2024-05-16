<?php
if ( ! isset( $count ) ) {
	$count = 0;
}
$postformat = atollmatrix_get_postformat();
$top_class  = '';
$count++;
if ( 1 === $count ) {
	$top_class = 'topseperator';
}
?>
<div class="<?php echo esc_attr( $top_class ); ?> entry-wrapper post-<?php echo esc_attr( $postformat ); ?>-wrapper clearfix">
<div class="blog-content-section">
<?php
get_template_part( 'template-parts/postformats/default' );
?>
</div>
</div>
