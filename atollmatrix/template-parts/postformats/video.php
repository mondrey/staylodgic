<div class="post-format-media">
<?php
$video_width          = '1400';
$m4v_file             = get_post_meta( $post->ID, 'pagemeta_video_m4v_file', true );
$ogv_file             = get_post_meta( $post->ID, 'pagemeta_video_ogv_file', true );
$poster_file          = get_post_meta( $post->ID, 'pagemeta_video_poster_file', true );
$video_height         = get_post_meta( $post->ID, 'pagemeta_video_height', true );
$video_title          = get_post_meta( $post->ID, 'pagemeta_video_title', true );
$youtube_video_id     = get_post_meta( $post->ID, 'pagemeta_video_youtube_id', true );
$vimeo_video_id       = get_post_meta( $post->ID, 'pagemeta_video_vimeo_id', true );
$dailymotion_video_id = get_post_meta( $post->ID, 'pagemeta_video_dailymotion_id', true );
$flash_video_url      = get_post_meta( $post->ID, 'pagemeta_video_flash_url', true );
$embed_video_code     = get_post_meta( $post->ID, 'pagemeta_video_embed_code', true );
$google_video_id      = get_post_meta( $post->ID, 'pagemeta_video_google_id', true );

if ( '' !== $ogv_file ) {
	$files_used = 'ogv, m4v';
} else {
	$files_used = 'm4v';
};

if ( $m4v_file ) {
	?>
<div id="jp_container_<?php the_ID(); ?>" class="single-jplay-video-postformat jp-video jp-video-360p" data-m4v="<?php echo esc_url( $m4v_file ); ?>" data-ogv="<?php echo esc_url( $ogv_file ); ?>" data-poster="<?php echo esc_url( $poster_file ); ?>" data-id="<?php the_ID(); ?>" data-autoplay="false" data-videofiles="<?php echo esc_js( $files_used ); ?>" data-swfpath="<?php echo esc_url( get_stylesheet_directory_uri() . '/js/html5player/' ); ?>">
	<div class="jp-type-single">
		<div id="jquery_jplayer_<?php the_ID(); ?>" class="jp-jplayer"></div>
			<div id="jp_interface_<?php the_ID(); ?>" class="jp-gui jp-interface">
				<ul class="jp-controls">
					<li><a href="#" class="jp-play" tabindex="1"><i class="feather-icon-play"></i></a></li>
					<li><a href="#" class="jp-pause" tabindex="1"><i class="feather-icon-pause"></i></a></li>
					<li><a href="#" class="jp-mute" tabindex="1" title="mute"><i class="feather-icon-volume"></i></a></li>
					<li><a href="#" class="jp-unmute" tabindex="1" title="unmute"><i class="feather-icon-mute"></i></a></li>
				</ul>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-time-holder">
					<div class="jp-current-time"></div>
				</div>
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
			</div>
	</div>
</div>
	<?php
}

if ( $youtube_video_id || $vimeo_video_id || $dailymotion_video_id || $google_video_id || $flash_video_url || $embed_video_code ) {
	echo '<div class="ajax-video-wrapper">';
	echo '<div class="ajax-video-container">';
}

if ( $youtube_video_id ) {
		echo do_shortcode( '[youtube_video id="' . $youtube_video_id . '" height="' . $video_height . '" width="' . $video_width . '"]' );
}

if ( $vimeo_video_id ) {
		echo do_shortcode( '[vimeo_video id="' . $vimeo_video_id . '" height="' . $video_height . '" width="' . $video_width . '"]' );
}

if ( $dailymotion_video_id ) {
		echo do_shortcode( '[dailymotion_video id="' . $dailymotion_video_id . '" height="' . $video_height . '" width="' . $video_width . '"]' );
}

if ( $google_video_id ) {
		echo do_shortcode( '[google_video id="' . $google_video_id . '" height="' . $video_height . '" width="' . $video_width . '"]' );
}

if ( $embed_video_code ) {
	$allowed_tags = array(
		'iframe' => array(
			'align'        => true,
			'width'        => true,
			'height'       => true,
			'frameborder'  => true,
			'name'         => true,
			'src'          => true,
			'id'           => true,
			'class'        => true,
			'style'        => true,
			'scrolling'    => true,
			'marginwidth'  => true,
			'marginheight' => true,
		),
	);
	echo wp_kses( $embed_video_code, $allowed_tags );
}
if ( $youtube_video_id || $vimeo_video_id || $dailymotion_video_id || $google_video_id || $flash_video_url || $embed_video_code ) {
	echo '</div>';
	echo '</div>';
}
?>
</div>
