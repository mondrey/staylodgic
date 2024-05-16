<?php
if ( has_post_thumbnail() ) {
	echo '<div class="post-format-media post-format-media-audio-image">';

	$single_height = '';
	$posthead_size = 'atollmatrix-gridblock-full';

	echo '<a class="postsummaryimage" href="' . esc_url( get_permalink() ) . '">';

	atollmatrix_display_post_image( $post->ID, $have_image_url = false, $gen_link = false, $imagesize_type = $posthead_size, $post->post_title, $class = '', $lazyload = true );
	echo '</a>';
}
$audio_embed_code = get_post_meta( $post->ID, 'pagemeta_audio_embed', true );
if ( '' !== $audio_embed_code ) {
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
	echo wp_kses( $audio_embed_code, $allowed_tags );
} else {

	$mp3_file = get_post_meta( $post->ID, 'pagemeta_meta_audio_mp3', true );
	$m4a_file = get_post_meta( $post->ID, 'pagemeta_meta_audio_m4a', true );
	$oga_file = get_post_meta( $post->ID, 'pagemeta_meta_audio_ogg', true );
	$mp3_ext  = '';
	$mp3_sep  = '';
	$m4a_ext  = '';
	$oga_ext  = '';
	$m4a_sep  = '';

	if ( $mp3_file ) {
		$mp3_ext = 'mp3';
		if ( $m4a_file || $oga_file ) {
			$mp3_sep = ',';
		}
	}
	if ( $m4a_file ) {
		$m4a_ext = 'm4a';
		if ( $oga_file ) {
			$m4a_sep = ',';
		}
	}
	if ( $oga_file ) {
		$oga_ext = 'oga';
	}

	$files_used = $mp3_ext . $mp3_sep . $m4a_ext . $m4a_sep . $oga_ext;

	if ( $files_used ) {
		?>
	<div id="jquery_jplayer_<?php the_ID(); ?>" class="single-jplay-audio-postformat jp-jplayer" data-autoplay="false" data-id="<?php the_ID(); ?>" data-audiofiles="<?php echo esc_attr( $files_used ); ?>" data-oga="<?php echo esc_url( $oga_file ); ?>" data-m4a="<?php echo esc_url( $m4a_file ); ?>" data-mp3="<?php echo esc_url( $mp3_file ); ?>" data-swfpath="<?php echo esc_url( get_stylesheet_directory_uri() . '/js/html5player/' ); ?>"></div>
	<div class="jp-audio">
		<div class="jp-type-single">
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
}
if ( has_post_thumbnail() ) {
	echo '</div>';
}
