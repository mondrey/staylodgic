<?php
function mtheme_gen_height($width) {
	$height = intval($width * 9 / 16);
	return $height;
	}
	
function mtheme_gen_width($height) {
	$width = intval($height * 16 / 9);
	return $width;
	}
	
/*Flash Video ShortCode*/
function mtheme_gen_flash_video($atts) {
	extract(shortcode_atts(array(
		'src' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
		'play'			=> 'false',
		'flashvars' => '',
	), $atts));
	
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	$uri = get_template_directory_uri();
	if (!empty($src)){
		return <<<HTML
<div class="video_frame">
<object width="{$width}" height="{$height}" type="application/x-shockwave-flash" data="{$src}">
	<param name="movie" value="{$src}" />
	<param name="allowFullScreen" value="true" />
	<param name="allowscriptaccess" value="always" />
	<param name="play" value="{$play}"/>
	<param name="wmode" value="opaque" />
</object>
</div>
HTML;
	}
}
add_shortcode( 'flash_video', 'mtheme_gen_flash_video' );

/*Youtube Video ShortCode*/
function mtheme_gen_youtube_video( $atts ) {
   extract( shortcode_atts( array(
		'id' => null,
		'width' 	=> false,
		'height' 	=> false,
		'rel' => null,
		'ytlogo' => null,
		'theme' => null,
		'info' => null,
		'hd' => '1',
		'playlist' => null,
		'time' => '0',
		'border' => '0',
      ), $atts ) );
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
 
   return '<div class="fitVids"><iframe class="youtube-player lazyload" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" src="'.$protocol.'://www.youtube.com/embed/' . esc_attr($id) . '/?wmode=transparent&amp;hd='. esc_attr($hd) . '&amp;theme=' . esc_attr($theme) . '&amp;autohide=1&amp;rel=' . esc_attr($rel) . '&amp;showinfo=' . esc_attr($info) . 'playlist=' . esc_attr($playlist) . '&amp;start=' . esc_attr($time) . '" frameborder="0" allow="encrypted-media" allowfullscreen></iframe></div>';
}
add_shortcode( 'youtube_video', 'mtheme_gen_youtube_video' );

/*Youtube Video ShortCode*/
function mtheme_pagebuilder_youtube_video( $atts ) {
   extract( shortcode_atts( array(
		'id' => null,
		'width' 	=> false,
		'height' 	=> false,
		'autoplay' => 1,
		'rel' => 1,
		'ytlogo' => null,
		'theme' => null,
		'info' => null,
		'hd' => '1',
		'playlist' => null,
		'time' => '0',
		'border' => '0',
      ), $atts ) );

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);
 
   return '<div class="fitVids"><iframe class="pb-youtube-player lazyload" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" src="'.$protocol.'://www.youtube.com/embed/' . esc_attr($id) . '?autoplay='.esc_attr($autoplay).'&rel=' . esc_attr($rel) . '&enablejsapi=1&wmode=opaque" frameborder="0" allow="encrypted-media" allowfullscreen></iframe></div>';
}
add_shortcode( 'pb_youtubevideo', 'mtheme_pagebuilder_youtube_video' );

/*Vimeo Video ShortCode*/
function mtheme_pagebuilder_vimeo_video( $atts ) {
   extract( shortcode_atts( array(
		'id' => null,
		'width' 	=> false,
		'height' 	=> false,
		'autoplay' => 1,
		'rel' => 1,
		'ytlogo' => null,
		'theme' => null,
		'info' => null,
		'hd' => '1',
		'playlist' => null,
		'time' => '0',
		'border' => '0',
      ), $atts ) );

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);
 
   return '<div class="fitVids"><iframe class="pb-vimeo-player lazyload" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" src="'.$protocol.'://player.vimeo.com/video/' . esc_attr($id) . '?autoplay='.esc_attr($autoplay).'" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
}
add_shortcode( 'pb_vimeovideo', 'mtheme_pagebuilder_vimeo_video' );

/*Google Video ShortCote*/
function mtheme_gen_google_video( $atts ) {
   extract( shortcode_atts( array(
		'id' => null,
		'width' 	=> false,
		'height' 	=> false,
      ), $atts ) );
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
 
   return '<div class="fitVids"><embed id=VideoPlayback src='.$protocol.'://video.google.com/googleplayer.swf?docid=' . esc_attr($id) . '&amp;hl=en&amp;fs=true style=width:' . esc_attr($width) . 'px;height:' . esc_attr($height) . 'px allowFullScreen=true allowScriptAccess=always type=application/x-shockwave-flash> </embed></div>';
}
add_shortcode( 'google_video', 'mtheme_gen_google_video' );

/*Vimeo Video Shortcode*/
function mtheme_gen_vimeo_video( $atts ) {
   extract( shortcode_atts( array(
		'id' => null,
		'width' => false,
		'height' => false,
		'title' => '0',
		'byline' => '0',
		'portrait' => '0',
		'border' => '0',
      ), $atts ) );
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
 
   return '<div class="fitVids"><iframe class="lazyload" src="'.$protocol.'://player.vimeo.com/video/' . esc_attr($id) . '?title=' . esc_attr($title) . '&amp;byline=' . esc_attr($byline) . '&amp;portrait=' . esc_attr($portrait) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></iframe></div>';
}
add_shortcode( 'vimeo_video', 'mtheme_gen_vimeo_video' );

/*DailyMotion Video ShortCode*/
function mtheme_gen_dailymotion_video( $atts ) {
   extract( shortcode_atts( array(
      'id' => null,
      'border' => '0',
		'width' 	=> false,
		'height' 	=> false,
      ), $atts ) );
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
 
   return '<div class="fitVids"><iframe width="' . esc_attr($width) . '" height="' . esc_attr($height) . '" src="'.$protocol.'://www.dailymotion.com/embed/video/' . esc_attr($id) . '"></iframe></div>';
}
add_shortcode( 'dailymotion_video', 'mtheme_gen_dailymotion_video' );

/*Facebook Video ShortCode*/
function mtheme_gen_facebook_video( $atts ) {
   extract( shortcode_atts( array(
      'id' => null,
		'width' 	=> false,
		'height' 	=> false,
      ), $atts ) );
	  
	if (!$width) $width = MTHEME_MAX_CONTENT_WIDTH;
	if (!$height && $width) $height=mtheme_gen_height($width);
	if (!$width && $height) $width=mtheme_gen_width($height);

	if( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
 
   return '<div class="fitVids"><object width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"><param name="movie" value="'.$protocol.'://www.facebook.com/v/' . esc_attr($id) . '"></param><param name="allowFullScreen" value="true"></param><embed src="'.$protocol.'://www.facebook.com/v/' . esc_attr($id) . '" type="application/x-shockwave-flash" allowfullscreen="true" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></embed></object></div>';
}
add_shortcode( 'facebook_video', 'mtheme_gen_facebook_video' );
?>
