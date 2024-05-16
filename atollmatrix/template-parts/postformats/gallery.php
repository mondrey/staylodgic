<div class="post-format-media">
<?php
$posthead_size = 'atollmatrix-gridblock-full';
if ( shortcode_exists( 'slideshowcarousel' ) ) {
	echo do_shortcode( '[slideshowcarousel thumbnails="false" lazyload="false" lightbox="true" title="true" imagesize=' . $posthead_size . ']' );
}
?>
</div>
