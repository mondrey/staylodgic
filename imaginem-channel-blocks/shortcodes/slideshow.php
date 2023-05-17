<?php
function mtheme_imagereel($atts, $content = null) {
	extract(shortcode_atts(array(
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"lightbox" => 'no',
		"display_title" => 'yes',
		"display_desc" => 'yes',
		"display_button" => 'yes',
		"slidestyle" => 'fade',
		"mobileoffset" => 'no',
		"desktopoffset" => 'no',
		"target" => '',
		"url" => '',
		"scrollindicator" => 'disable',
		"swiperpagination" => 'yes',
		"autoplay" => 'true',
		"autoplayactive" => 'no',
		"height" => '',
		"format" => '',
		"carousel_type" => 'swiper',
		"columns" => '4',
		"limit" => '-1',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();

$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$set_style='';
$portfoliogrid2='';

if ( 'no' ==  $autoplayactive ) {
	$autoplay = 0;
}

if ($height=="{{height}}") { $height=''; }
if ($height<>'') {
	$set_style= ' style="height:'.$height.'px;"';
}

$slide_columntype_class = '';
if ( '1' === $columns ) {
	$slide_columntype_class = ' single-column-slider';
} else {
	$slide_columntype_class = ' multi-column-slider';
}

if ($carousel_type=="swiper") {
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ( $columns=="" || $columns=="{{columns}}" ) {
		$columns = "4";
	}

	$lightbox_code = '';
	$carousel = '';

	if ( $filter_image_ids ) {

		$totalslides = count( $filter_image_ids );
		$uniqureID=get_the_id()."-".uniqid();
		$carousel ='<div class="multislider-container-outer reel-slider">';
		$carousel .= '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container swiper-init swiper-slides-overlay shortcode-multislider-container swiperpagination-'.$swiperpagination.' swiper-columns-'.$columns.$slide_columntype_class.' desktopoffset-'.$desktopoffset.' mobileoffset-'.$mobileoffset.'" data-slidestyle="'.$slidestyle.'" data-slidetotal="' . esc_attr( $totalslides ) . '" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-desktopoffset="'.$desktopoffset.'" data-mobileoffset="'.$mobileoffset.'" data-id="'.$uniqureID.'">
	    <div class="swiper-wrapper">';

		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

			$custom = get_post_custom(get_the_ID());
			$attachment = get_post( $attachment_id );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment->ID, 'fullsize', false);
			$imageURI = $imagearray[0];

			$img_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true);
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment->ID, $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment->ID, $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			$imageTitle = $attachment->post_title;
			$imageDesc = $attachment->post_content;

			$link_text = '';
			$link_url = '';
			$slideshow_link = '';
			$slideshow_color ='';

			$link_text = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
			$link_url = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
			$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

			$slideshow_titledesc = "enable";

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {
				$portfolio_count++;

					$title_code = '<div class="swiper-contents">';
						if ( 'yes' === $display_title ) {
							$title_code .= '<div class="swiper-title">' . sanitize_text_field( $imageTitle ) . '</div>';
						}
						if ( 'yes' === $display_desc ) {
							$title_code .= '<div class="swiper-desc">' . sanitize_text_field( $imageDesc ) . '</div>';
						}
					$button_color = "bright";
					if ( 'yes' === $display_button ) {
						if ($link_url<>"" && $link_text<>"") {
							$title_code .= '<div class="button-shortcode '.$button_color.'"><a title="" href="'.esc_url($link_url).'"><div class="mtheme-button">'. esc_attr($link_text) .'</div></a></div>';
						}
					}
					$title_code .= '</div>';

				if ( 'yes' === $lightbox ) {
					$lightbox_code = imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imageTitle="",
						$class="swiper-slide-lightbox lightbox-image",
						$set="swiper-slide-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id
					);
					$lightbox_code .='</a>';
				}

				

				$image_tag = '<img class="image-reel-image" src="'.esc_url($imageURI).'" alt="' . esc_attr( $img_alt ) . '" />';
		        $carousel .= '<div class="swiper-slide">'.$image_tag.'</div>';

			}

		}
		$carousel .='</div>';
	    if ( $portfolio_count > $columns ) {
		    $carousel .='<div class="swiper-pagination"></div>';
		    
		    $carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
		    $carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';
		}

		$carousel .='</div>';
		if ( 'enable' === $scrollindicator ) {
			$carousel .= '<a class="mouse-scroll-indicator-link" ' . $target . ' href="' . esc_url( $url ) . '">';
			$carousel .= '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
			$carousel .= '</a>';
		}
		$carousel .='</div>';
    }
}

	return $carousel;
}
add_shortcode("imagereel", "mtheme_imagereel");
function mtheme_parallax_slides($atts, $content = null) {
	extract(shortcode_atts(array(
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"lightbox" => 'no',
		"display_title" => 'yes',
		"display_desc" => 'yes',
		"display_button" => 'yes',
		"slidestyle" => 'swipe',
		"mobileoffset" => 'no',
		"desktopoffset" => 'no',
		"target" => '',
		"url" => '',
		"scrollindicator" => 'disable',
		"swiperpagination" => 'yes',
		"autoplay" => 'true',
		"height" => '',
		"format" => '',
		"carousel_type" => 'swiper',
		"columns" => '4',
		"limit" => '-1',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();

$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$set_style='';
$portfoliogrid2='';

if ($height=="{{height}}") { $height=''; }
if ($height<>'') {
	$set_style= ' style="height:'.$height.'px;"';
}

$slide_columntype_class = '';
if ( '1' === $columns ) {
	$slide_columntype_class = ' single-column-slider';
} else {
	$slide_columntype_class = ' multi-column-slider';
}

if ($carousel_type=="swiper") {
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	$columns = 1;

	$lightbox_code = '';
	$carousel = '';

	if ( $filter_image_ids ) {
		
		$uniqureID=get_the_id()."-".uniqid();
		$carousel ='<div class="swiper-container-outer swiper-parallax-container-outer">';
		$carousel .= '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container swiper-init swiper-slides-overlay shortcode-swiper-container shortcode-parallax-container swiperpagination-'.$swiperpagination.' swiper-columns-'.$columns.$slide_columntype_class.' desktopoffset-'.$desktopoffset.' mobileoffset-'.$mobileoffset.'" data-slidestyle="'.$slidestyle.'" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-desktopoffset="'.$desktopoffset.'" data-mobileoffset="'.$mobileoffset.'" data-id="'.$uniqureID.'">
	    <div class="swiper-wrapper">';

		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

			$custom = get_post_custom(get_the_ID());
			$attachment = get_post( $attachment_id );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment->ID , 'fullsize', false);
			$imageURI = $imagearray[0];
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			$imageTitle = $attachment->post_title;
			$imageDesc = $attachment->post_content;

			$link_text = '';
			$link_url = '';
			$slideshow_link = '';
			$slideshow_color ='';

			$link_text = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
			$link_url = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
			$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

			$slideshow_titledesc = "enable";

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {
				$portfolio_count++;

					$title_code = '<div class="swiper-parallax-contents">';
						if ( 'yes' === $display_title ) {
							$title_code .= '<div class="swiper-title">' . sanitize_text_field( $imageTitle ) . '</div>';
						}
						if ( 'yes' === $display_desc ) {
							$title_code .= '<div class="swiper-desc">' . sanitize_text_field( $imageDesc ) . '</div>';
						}
					$button_color = "bright";
					if ( 'yes' === $display_button ) {
						if ($link_url<>"" && $link_text<>"") {
							$title_code .= '<div class="button-shortcode '.$button_color.'"><a title="" href="'.esc_url($link_url).'"><div class="mtheme-button">'. esc_attr($link_text) .'</div></a></div>';
						}
					}
					$title_code .= '</div>';

				if ( 'yes' === $lightbox ) {
					$lightbox_code = imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imageTitle="",
						$class="swiper-slide-lightbox lightbox-image",
						$set="swiper-slide-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id
					);
					$lightbox_code .='</a>';
				}

		        $carousel .= '<div class="swiper-slide"><div class="slide-inner" style="background-image: url('.esc_url($imageURI).');">'.$lightbox_code.$title_code.'</div></div>';

			}

		}
		$carousel .='</div>';
	    if ( $portfolio_count > $columns ) {
		    $carousel .='<div class="swiper-pagination"></div>';
		    
		    $carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
		    $carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';
		}

		$carousel .='</div>';
		if ( 'enable' === $scrollindicator ) {
			$carousel .= '<a class="mouse-scroll-indicator-link" ' . $target . ' href="' . esc_url( $url ) . '">';
			$carousel .= '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
			$carousel .= '</a>';
		}
		$carousel .='</div>';
    }
}

	return $carousel;
}
add_shortcode("parallax_slides", "mtheme_parallax_slides");
// Fotorama Images
function mtheme_fotorama_images($atts, $content = null) {
	extract(shortcode_atts(array(
		"thumbnails" => "yes",
		"filltype" => 'cover',
		"transition" => 'crossfade',
		"autoplay" => 'false',
		"mobileoffset" => 'false',
		"desktopoffset" => 'false',
		"width" => '100',
		"height" => '80',
		"autoplayspeed" => '8000',
		"hash" => 'false',
		"displaydesc" => "false",
		"displaytitle" => "false",
		"pageid" => '',
		"pb_image_ids" => '',
		"displaytitle" => 'false',
		"desc" => 'true',
		"worktype_slug" => ''
	), $atts));

$uniqureID=get_the_id()."-".uniqid();
$column_type="four";
$portfolioImage_type="superlens-gridblock-full";
$portfolioImage_type2="superlens-gridblock-large";

if ($worktype_slug=="-1") { $worktype_slug=''; }
$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$portfoliogrid2='';

$fotorama_autoplay = 'false';
if ( 'true' === $autoplay ) {
	$fotorama_autoplay = $autoplayspeed;
}

$fotorama = '<div class="mtheme-fotorama thumbnail-is-'. esc_attr( $thumbnails ) .' fotorama-height-is-'. esc_attr( $height ).'">';
$fotorama .= '<div class="fotorama"
 data-fit="'.$filltype.'"
 data-nav="thumbs"
 data-shuffle="false"
 data-loop="true"
 data-width="100%"
 data-adjustheight="'.$height.'"
 data-desktopoffset="'.$desktopoffset.'"
 data-mobileoffset="'.$mobileoffset.'"
 data-thumbwidth="60"
 data-thumbheight="60"
 data-keyboard="true"
 data-hash="'.$hash.'"
 data-transition="'.$transition.'"
 data-transition-duration="800"
 data-autoplay="'.$fotorama_autoplay.'"
 data-auto="false"
 >';
	
	 $filter_image_ids = explode(',', $pb_image_ids);
	 
	if ( $filter_image_ids ) {
		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

				//echo $type, $portfolio_type;
			$custom = get_post_custom(get_the_ID());
			$portfolio_cats = get_the_terms( get_the_ID(), 'worktypes' );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment_id , 'fullsize', false);
			$imageURI = $imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment_id , 'superlens-gridblock-tiny', false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			if ( isSet($imageURI) && !empty( $imageURI ) ) {

				$imageID = get_post($attachment_id);
				$imageTitle = $imageID->post_title;
				$imageDesc= $imageID->post_content;

				$displaypagetitle='';

				$title_desc='';
				$title_desc = 'data-caption="';
				if ( 'true' === $displaytitle ) {
					if ( '' !== $imageTitle ) {
						$title_desc .= '<h1>'.sanitize_text_field($imageTitle).'</h1>';
					}
				}
				if ( 'true' === $displaydesc ) {
					if ( '' !== $imageDesc ) {
						$title_desc .= '<p>'.sanitize_text_field($imageDesc).'</p>';
					}
				}
				$title_desc .= '" ';

				$fotorama .= '<a '.$title_desc.' href="' . esc_url( $imageURI ) .'">';
				$fotorama .= '<img src="'.esc_url($thumbnail_imageURI).'" alt="'.esc_attr($imageTitle).'" />';
				$fotorama .= '</a>';
			}

		}
	}
	
	$fotorama .='</div>';
	$fotorama .='<div class="fotorama-caption"></div>';
	$fotorama .='</div>';

	return $fotorama;
}
add_shortcode("fotorama_images", "mtheme_fotorama_images");
/**
 * [mtheme_imageslideshow description]
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function mtheme_carousel_slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"lightbox" => 'no',
		"display_title" => 'yes',
		"display_desc" => 'yes',
		"display_button" => 'yes',
		"slidestyle" => 'slide',
		"target" => '',
		"url" => '',
		"scrollindicator" => 'disable',
		"swiperpagination" => 'yes',
		"autoplay" => 'true',
		"height" => '',
		"format" => '',
		"carousel_type" => 'swiper',
		"columns" => '3',
		"limit" => '-1',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();

$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$set_style='';
$portfoliogrid2='';

$slideshow_contents = false;
$title_code = '';

if ($height=="{{height}}") { $height=''; }
if ($height<>'') {
	$set_style= ' style="height:'.$height.'px;"';
}

if ($carousel_type=="swiper") {
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ( $columns=="" || $columns=="{{columns}}" ) {
		$columns = "4";
	}

	$lightbox_code = '';
	$carousel = '';

	if ( $filter_image_ids ) {

		$uniqureID=get_the_id()."-".uniqid();
		$carousel ='<div class="swiper-container-outer">';
		$carousel .= '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container swiper-init swiper-slides-overlay shortcode-swiper-container swiper-columns-'.$columns.'" data-slidestyle="'.$slidestyle.'" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-id="'.$uniqureID.'">
	    <div class="swiper-wrapper">';

		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

			$custom = get_post_custom(get_the_ID());
			$attachment = get_post( $attachment_id );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment->ID , 'fullsize', false);
			$imageURI = $imagearray[0];
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			$imageTitle = $attachment->post_title;
			$imageDesc = $attachment->post_content;

			$link_text = '';
			$link_url = '';
			$slideshow_link = '';
			$slideshow_color ='';

			$link_text = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
			$link_url = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
			$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

			$slideshow_titledesc = "enable";

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {
				$portfolio_count++;

					if ( $slideshow_contents ) {
						$title_code = '<div class="swiper-contents">';
							if ( 'yes' === $display_title ) {
								$title_code .= '<div class="swiper-title">' . sanitize_text_field( $imageTitle ) . '</div>';
							}
							if ( 'yes' === $display_desc ) {
								$title_code .= '<div class="swiper-desc">' . sanitize_text_field( $imageDesc ) . '</div>';
							}
						$button_color = "bright";
						if ( 'yes' === $display_button ) {
							if ($link_url<>"" && $link_text<>"") {
								$title_code .= '<div class="button-shortcode '.$button_color.'"><a title="" href="'.esc_url($link_url).'"><div class="mtheme-button">'. esc_attr($link_text) .'</div></a></div>';
							}
						}
						$title_code .= '</div>';
					}

				if ( 'yes' === $lightbox ) {
					$lightbox_code = imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imageTitle="",
						$class="swiper-slide-lightbox lightbox-image",
						$set="swiper-slide-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id
					);
					$lightbox_code .='</a>';
				}

				$carousel .= '<div class="swiper-slide">';
				$carousel .= imaginem_codepack_multi_image (
					$attachment_id,
					$have_image_url=false,
					$link=false,
					$type=$portfolioImage_type,
					$imagetitle=$imageTitle,
					$class= 'carousel-slideshow-image',
					$lazyload_status = true,
					$srcset_status = false,
					$srcset_prefix = ''
				);				
				$carousel .= '</div>';

			}

		}
		$carousel .='</div>';
	    if ( $portfolio_count > $columns ) {
		    $carousel .='<div class="swiper-pagination"></div>';
		    
		    $carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
		    $carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';
		}

		$carousel .='</div>';
		if ( 'enable' === $scrollindicator ) {
			$carousel .= '<a class="mouse-scroll-indicator-link" ' . $target . ' href="' . esc_url( $url ) . '">';
			$carousel .= '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
			$carousel .= '</a>';
		}
		$carousel .='</div>';
    }
}

	return $carousel;
}
add_shortcode("carousel_slideshow", "mtheme_carousel_slideshow");
/**
 * [mtheme_imageslideshow description]
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function mtheme_flickity_slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"windowtype" => '',
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"slideshowtype" => 'slideshow',
		"smartspeed" => '1000',
		"autoplay" => 'false',
		"thumbnails" => 'true',
		"lazyload" => 'true',
		"autoplayinterval" => '5000',
		"lightbox" => 'false',
		"format" => '',
		"carousel_type" => 'owl',
		"columns" => '4',
		"limit" => '-1',
		"displaytitle" => 'false',
		"desc" => 'true',
		"height" => '',
		"boxtitle" => 'true',
		"worktype_slug" => '',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();

if ($windowtype=="ajax") {
	$uniqureID = "ajax";
}
if ($autoplay <> "true") {
	$autoplay="false";
}
$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

if ($worktype_slug=="-1") { $worktype_slug=''; }
$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$portfoliogrid2='';

if ($windowtype=="ajax") {
	$lightbox = "false";
}
$lightbox_container_class = '';
if ( $lightbox == 'true' ) {
	$lightbox_container_class = ' lightgallery-container';
}
if ($carousel_type=="owl") {

	$portfoliogrid_line1 = '<div class="flickity-carousel-wrap clearfix">';
	$portfoliogrid_line2 = '<div id="'.$uniqureID.'" class="flickity-carousel-inner flickity-carousel-detect'.$lightbox_container_class.' flickity-carousel-type-'.$slideshowtype.'" data-autoplaytimeout="'.$autoplayinterval.'" data-smartspeed="'.$smartspeed.'" data-id="'.$uniqureID.'" data-autoplay="'.$autoplay.'" data-lazyload="true" data-type="'.$slideshowtype.'">';
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ($lazyload=="true") {
		$class_status = "flickity-lazy";
	} else {
		$class_status = "flickity-slide-image";
	}

	if ( $filter_image_ids ) {
		foreach ( $filter_image_ids as $attachment_id) {


            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

				//echo $type, $portfolio_type;
			$custom = get_post_custom(get_the_ID());
			$portfolio_cats = get_the_terms( get_the_ID(), 'worktypes' );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment_id , 'fullsize', false);
			$imageURI = $imagearray[0];			
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment_id , $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment_id , $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			if ($portfolio_count==$columns) $portfolio_count=0;

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {

				$imageID = get_post($attachment_id);
				$imageTitle = $imageID->post_title;
				$imageDesc= $imageID->post_content;

				$protected="";
				$icon_class="column-gridblock-icon";
				$portfolio_count++;
				$portfoliogrid .= '<div class="flickity-carousel-cell">';

				// Large image sequence
				if ($lightbox=="true") {
					$portfoliogrid .= imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imagetitle=$imageTitle,
						$class="lightbox-image",
						$set="flickity-lightbox-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id
					);
				}
				
					$portfoliogrid .= imaginem_codepack_multi_image (
						$attachment_id,
						$have_image_url=false,
						$link=false,
						$type=$portfolioImage_type,
						$imagetitle=$imageTitle,
						$class= $class_status,
						$lazyload_status = true,
						$srcset_status = false,
						$srcset_prefix = ''
					);
					if ($lightbox=="true") {
						$portfoliogrid .= '</a>';
					}
						if ($displaytitle=='true') {
							$portfoliogrid .= '<div class="flickity-slideshow-title">'.$imageTitle.'</div>';
						}
	
					$portfoliogrid .='</div>';
	
				}
	
			}
		}
		$portfoliogrid .='</div>';
	}
	
		$portfoliogrid .='</div>';
		if ($windowtype=="ajax") {
			$portfoliogrid_script='';
		}
	
		$slideshow_1 = $portfoliogrid_line1 . $portfoliogrid_line2 . $portfoliogrid;
	
		return $slideshow_1;
	}
add_shortcode("flickity_slideshow", "mtheme_flickity_slideshow");
/**
 * [mtheme_imageslideshow description]
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function mtheme_postswiperslides($atts, $content = null) {
	extract(shortcode_atts(array(
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"autoplay" => 'false',
		"post_type" => 'portfolio',
		"worktype_slugs" => '',
		"swiperpagination" => 'no',
		"format" => '',
		"height" => '',
		"slideshow_titledesc" => 'enable',
		"columns" => '4',
		"limit" => '-1',
		"pagination" => 'false'
	), $atts));


	if ( $columns=="" || $columns=="{{columns}}" ) {
		$columns = "4";
	}

	$slide_columntype_class = '';
	if ( '1' === $columns ) {
		$slide_columntype_class = ' single-column-slider';
	} else {
		$slide_columntype_class = ' multi-column-slider';
	}

	if ( $post_type == "portfolio") {
		$query = array(
			'post_type' => 'portfolio',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'worktypes' => $worktype_slugs,
			'posts_per_page' => $limit,
			);
		
	}
	if ( $post_type == "events" ) {

		if ($worktype_slugs != "") {
            $type_explode = explode(",", $worktype_slugs);
            foreach ($type_explode as $work_slug) {
                $terms[] = $work_slug;
			}
			
			$query = array(
				'post_type' => 'events',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'pagemeta_event_notice',
						'value' => 'inactive',
						'compare' => 'NOT IN'
					)
				),
				'tax_query' => array(
					array(
						'taxonomy' => 'eventsection',
						'field' => 'slug',
						'terms' => $terms,
						'operator' => 'IN'
					)
				)
			);
		} else {
			$query = array(
				'post_type' => 'events',
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit,
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'pagemeta_event_notice',
						'value' => 'inactive',
						'compare' => 'NOT IN'
					)
				)
			);
		}
	}

	query_posts($query);

$uniqureID=get_the_id()."-".uniqid();

$set_style = '';
if ($height=="{{height}}") { $height=''; }
if ($height<>'') {
	$set_style= ' style="height:'.$height.'px;"';
}

$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$portfoliogrid2='';

	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	$lightbox_code = '';
	$link_text = 'View Project';



		$uniqureID=get_the_id()."-".uniqid();

		$carousel = '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container events-swiper shortcode-swiper-container'.$slide_columntype_class.'" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-id="'.$uniqureID.'">';
	    $carousel .= '<div class="swiper-wrapper">';

		if (have_posts()) : while (have_posts()) : the_post();

			if ( has_post_thumbnail() ) {

				$description= '';
				$custom = get_post_custom(get_the_ID());
                if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                    $description = $custom['pagemeta_thumbnail_desc'][0];
                }

				$imageURI = imaginem_codepack_featured_image_link( get_the_id() );

			    $carousel .= '<div class="swiper-slide slide-color-light" style="background-image: url('.esc_url($imageURI).'); background-size: cover; background-position: 50% 0%;">';

			    $carousel .= '<a href="'.get_permalink().'">';
		        
		        if ($slideshow_titledesc=="enable") {
		        	$carousel .=  '<div class="swiper-contents">';
				        $carousel .=  '<div class="swiper-title swiper-title-bright">' . get_the_title() . '</div>';

	                    if ($post_type == "events") {
	                        $event_start_datetime = '';
	                        $event_end_datetime   = '';
	                        $event_startdate      = '';
	                        $event_enddate        = '';
	                        if (isset($custom['pagemeta_event_startdate'][0]))
	                            $event_startdate = $custom['pagemeta_event_startdate'][0];
	                        if (isset($custom['pagemeta_event_enddate'][0]))
	                            $event_enddate = $custom['pagemeta_event_enddate'][0];
	                        $event_start_datetime = explode(" ", $event_startdate);
	                        $event_end_datetime   = explode(" ", $event_enddate);
	                        $start_date           = date_i18n(get_option('date_format'), strtotime($event_start_datetime[0]));
	                        $end_date             = date_i18n(get_option('date_format'), strtotime($event_end_datetime[0]));
	                            $carousel .= '<div class="events-summary-wrap summary-info">';
	                        $carousel .= '<div class="worktype-categories">' . $start_date . '<span class="date-seperator"> - </span>' . $end_date . '</div>';
	                            $carousel .= '</div>';
	                    }

				        if ( $description<>'' ) {
				        	$carousel .=  '<div class="swiper-desc swiper-desc-bright">' . $description . '</div>';
				        }
			        if ($link_text<>"") {
						//$carousel .=  '<div class="button-shortcode text-is-bright"><a title="" href="'.get_permalink().'"><div class="mtheme-button">'. esc_html($link_text) .'</div></a></div>';
					}
					$carousel .=  '</div>';
				}

				$carousel .=  '</a>';

				$carousel .=  '</div>';

			}

	    endwhile; endif;
		$carousel .='</div>';
		    $carousel .='<div class="swiper-pagination"></div>';
		    
		    $carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
		    $carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';

		$carousel .='</div>';

	wp_reset_query();
	return $carousel;
}
add_shortcode("postswiperslides", "mtheme_postswiperslides");/**
 * [mtheme_imageslideshow description]
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function mtheme_swiperslides($atts, $content = null) {
	extract(shortcode_atts(array(
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"lightbox" => 'no',
		"display_title" => 'yes',
		"display_desc" => 'yes',
		"display_button" => 'yes',
		"slidestyle" => 'fade',
		"mobileoffset" => 'no',
		"desktopoffset" => 'no',
		"target" => '',
		"url" => '',
		"scrollindicator" => 'disable',
		"swiperpagination" => 'yes',
		"autoplay" => 'true',
		"height" => '',
		"format" => '',
		"carousel_type" => 'swiper',
		"columns" => '4',
		"limit" => '-1',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();
$galleryID = uniqid();

$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$set_style='';
$portfoliogrid2='';

if ($height=="{{height}}") { $height=''; }
if ($height<>'') {
	$set_style= ' style="height:'.$height.'px;"';
}

$slide_columntype_class = '';
if ( '1' === $columns ) {
	$slide_columntype_class = ' single-column-slider';
} else {
	$slide_columntype_class = ' multi-column-slider';
}

if ($carousel_type=="swiper") {
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ( $columns=="" || $columns=="{{columns}}" ) {
		$columns = "4";
	}

	$lightbox_code = '';
	$carousel = '';

	if ( $filter_image_ids ) {
		
		$uniqureID= 'multi-slide-' . get_the_id() . '-' .uniqid();
		$carousel ='<div class="swiper-container-outer">';
		$carousel .= '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container swiper-init swiper-slides-overlay shortcode-swiper-container swiperpagination-'.$swiperpagination.' swiper-columns-'.$columns.$slide_columntype_class.' desktopoffset-'.$desktopoffset.' mobileoffset-'.$mobileoffset.'" data-slidestyle="'.$slidestyle.'" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-desktopoffset="'.$desktopoffset.'" data-mobileoffset="'.$mobileoffset.'" data-id="'.$uniqureID.'">
	    <div class="swiper-wrapper">';

		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

			$custom = get_post_custom(get_the_ID());
			$attachment = get_post( $attachment_id );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment->ID , 'fullsize', false);
			$imageURI = $imagearray[0];
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment->ID , $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			$imageTitle = $attachment->post_title;
			$imageDesc = $attachment->post_content;

			$link_text = '';
			$link_url = '';
			$slideshow_link = '';
			$slideshow_color ='';

			$link_text = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
			$link_url = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
			$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

			$slideshow_titledesc = "enable";

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {
				$portfolio_count++;

					$title_code = '<div class="swiper-contents">';
						if ( 'yes' === $display_title ) {
							$title_code .= '<div class="swiper-info-animate swiper-title">' . sanitize_text_field( $imageTitle ) . '</div>';
						}
						if ( 'yes' === $display_desc ) {
							$title_code .= '<div class="swiper-info-animate swiper-desc">' . sanitize_text_field( $imageDesc ) . '</div>';
						}
					$button_color = "bright";
					if ( 'yes' === $display_button ) {
						if ($link_url<>"" && $link_text<>"") {
							$title_code .= '<div class="swiper-info-animate button-shortcode '.$button_color.'"><a title="" href="'.esc_url($link_url).'"><div class="mtheme-button">'. esc_attr($link_text) .'</div></a></div>';
						}
					}
					$title_code .= '</div>';

				if ( 'yes' === $lightbox ) {
					$lightbox_code = imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imageTitle="",
						$class="swiper-slide-lightbox lightbox-image",
						$set="swiper-slide-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id,
						$galleryID
					);
					$lightbox_code .='</a>';
				}

		        $carousel .= '<div class="swiper-slide swiper-lazy" data-background="'.esc_url($imageURI).'">'.$lightbox_code.$title_code.'</div>';

			}

		}
		$carousel .='</div>';
	    if ( $portfolio_count > $columns ) {
		    $carousel .='<div class="swiper-pagination"></div>';
		    
		    $carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
		    $carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';
		}

		$carousel .='</div>';
		if ( 'enable' === $scrollindicator ) {
			$carousel .= '<a class="mouse-scroll-indicator-link" ' . $target . ' href="' . esc_url( $url ) . '">';
			$carousel .= '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
			$carousel .= '</a>';
		}
		$carousel .='</div>';
    }
}

	return $carousel;
}
add_shortcode("swiperslides", "mtheme_swiperslides");
/**
 * Blog Slideshow .
 *
 * @ [flexislideshow link=(lightbox,direct,none)]
 */
function mtheme_BlogSlideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"cat_slug" => '',
		"lightbox" => 'true',
		"autoplay" => 'false',
		"transition" => 'fade',
		"limit" => ''
	), $atts));
	
	//echo $type, $portfolio_type;
	query_posts(array(
		'category_name' => $cat_slug,
		'posts_per_page' => $limit
		));

	$uniqureID=get_the_id()."-".uniqid();

	$portfolioImage_type="superlens-gridblock-full";

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-blog-slideshow clearfix">';
	$output .= '<div id="owl-'.$uniqureID.'" class="owl-carousel owl-slideshow-element">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			if ( has_post_thumbnail() ) {
				$output .= '<li class="slideshow-box-wrapper">';
				$output .= '<div class="slideshow-box-image">';

				$lightbox_image = imaginem_codepack_featured_image_link( get_the_id() );

				$lightbox_media = $lightbox_image;

				$custom = get_post_custom(get_the_ID());

				if ( isset($custom['pagemeta_lightbox_video'][0]) ) { 
					$lightbox_media=$custom['pagemeta_lightbox_video'][0];
				}
				// Large image sequence
				if ($lightbox=="true") {
					$output .= imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$lightbox_media,
						$mediatype="image",
						$imagetitle=get_the_title(),
						$class="lightbox-image",
						$set="blog-slideshow-lightbox-set",
						$data_name="default",
                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                        $imageDataID=get_post_thumbnail_id( get_the_id() )
					);
				}

				$output .= imaginem_codepack_display_post_image (
					get_the_ID(),
					$have_image_url="",
					$link=false,
					$theimage_type=$portfolioImage_type,
					$imagetitle='',
					$class="displayed-image"
				);
				$output .= '</a>';
				$output .= '</div>';
				$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
				$output .= '<div class="slideshow-box-title"><h2><a href="'.get_permalink().'">'.get_the_title().'</a></h2></div>';

				$output .= '<div class="slideshow-box-info">';
					$output .='<div class="slideshow-box-categories">';
					foreach((get_the_category()) as $category) { 
					    $output .= '<span>'.$category->cat_name.'</span>';
					} 
					$output .='</div>';
					$category = get_the_category();
					$output .= '<div class="slideshow-box-comment">';

					$num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
					if ( comments_open() ) {
						if ( $num_comments == 0 ) {
							$comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
						} elseif ( $num_comments > 1 ) {
							$comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
						} else {
							$comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
						}
						$output .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
					}
					$output .='</div>';
					$output .='<div class="slideshow-box-date"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
				$output .='</div>';

				$output .= '</div>';
				$output .= '</div>';
				$output .='</li>';
			}

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';

	$output .='
	<script>
	/* <![CDATA[ */
	(function($){
	$(window).on( "load", function() {
		 var sync1 = $("#owl-'.$uniqureID.'");
		 sync1.owlCarousel({
			items: 1,
			autoplay: '.$autoplay.',
			nav: true,
			autoHeight : true,
			loop: true,
			navText : ["",""],
			singleItem : true,
			animateOut: "fadeOut"
		 });';
	$output .= '
	})
	})(jQuery);
	/* ]]> */
	</script>
	';

	
	wp_reset_query();
	return $output;
	
}
add_shortcode("recent_blog_slideshow", "mtheme_BlogSlideshow");


/**
 * Portfolio Slideshow .
 *
 * @ [flexislideshow link=(lightbox,direct,none)]
 */
function portfolioSlideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"worktype_slugs" => '',
		"windowtype" => '',
		"pageid" => '',
		"imagesize" => 'landscape',
		"pb_image_ids" => '',
		"button_text" => 'Visit Project',
		"slideshowtype" => 'slideshow',
		"smartspeed" => '1000',
		"autoplay" => 'false',
		"thumbnails" => 'true',
		"lazyload" => 'false',
		"autoplayinterval" => '5000',
		"lightbox" => 'true',
		"format" => '',
		"carousel_type" => 'owl',
		"columns" => '4',
		"limit" => '-1',
		"displaytitle" => 'false',
		"desc" => 'true',
		"height" => '',
		"boxtitle" => 'true',
		"worktype_slug" => '',
		"pagination" => 'false'
	), $atts));

	if ($limit=='') {
		$limit="-1";
	}
	
	//echo $type, $portfolio_type;
	$countquery = array(
		'post_type' => 'portfolio',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'worktypes' => $worktype_slugs,
		'posts_per_page' => $limit,
		);
	query_posts($countquery);

	$portfolioImage_type = "superlens-gridblock-large";
	if ($imagesize=="landscape") {
		$portfolioImage_type = "superlens-gridblock-large";
	}
	if ($imagesize=="portrait") {
		$portfolioImage_type = "superlens-gridblock-large-portrait";
	}
	if ($imagesize=="full") {
		$portfolioImage_type = "superlens-gridblock-full";
	}

	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	if ($lazyload=="true") {
		$class_status = "owl-lazy";
	} else {
		$class_status = "owl-slide-image";
	}
	$output = '<div class="gridblock-owlcarousel-wrap clearfix">';
	$output = '<div id="'.$uniqureID.'" class="owl-carousel owl-slideshow-element owl-carousel-detect owl-carousel-type-'.$slideshowtype.'" data-autoplaytimeout="'.$autoplayinterval.'" data-smartspeed="'.$smartspeed.'" data-id="'.$uniqureID.'" data-autoplay="'.$autoplay.'" data-lazyload="'.$lazyload.'" data-type="'.$slideshowtype.'">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			if ( has_post_thumbnail() ) {
				$output .= '<li class="slideshow-box-wrapper">';
				$output .= '<div class="slideshow-box-image">';

				$lightbox_image = imaginem_codepack_featured_image_link( get_the_id() );

				$lightbox_media = $lightbox_image;

				$custom = get_post_custom(get_the_ID());

				if ( isset($custom['pagemeta_lightbox_video'][0]) ) { 
					$lightbox_media=$custom['pagemeta_lightbox_video'][0];
				}
				

				$image_id = get_post_thumbnail_id( get_the_ID() , $portfolioImage_type); 
				$image_url = wp_get_attachment_image_src($image_id,$portfolioImage_type);  
				$image_url = $image_url[0];
				$img_obj = get_post($image_id);
				$img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

				$output .= imaginem_codepack_display_post_image (
					get_the_id(),
					$have_image_url=$image_url,
					$link=false,
					$type=$portfolioImage_type,
					$imagetitle=get_the_title(),
					$class= $class_status,
					$lazyload_status = $lazyload
				);

				$output .= '</div>';
				$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
				$output .= '<div class="slideshow-box-title"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';

			$output .= '<div class="slideshow-box-info">';
				$output .='<div class="slideshow-box-categories">';
				$categories = get_the_term_list( get_the_id(), 'worktypes', '', ' / ', '' );
				    $output .= '<span>'.$categories.'</span>';
				$output .='</div>';
			$output .='</div>';

			$output .='<a href="'.get_permalink().'"><div class="mtheme-button">'.esc_html($button_text).'</div></a>';

				$output .= '</div></div>';
				$output .='</li>';
			}

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("recent_portfolio_slideshow", "portfolioSlideshow");

/**
 * [mtheme_imageslideshow description]
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function mtheme_imageslideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"windowtype" => '',
		"pageid" => '',
		"imagesize" => 'superlens-gridblock-full',
		"pb_image_ids" => '',
		"slideshowtype" => 'slideshow',
		"smartspeed" => '1000',
		"autoplay" => 'false',
		"thumbnails" => 'true',
		"lazyload" => 'false',
		"autoplayinterval" => '5000',
		"lightbox" => 'true',
		"format" => '',
		"carousel_type" => 'owl',
		"columns" => '4',
		"limit" => '-1',
		"displaytitle" => 'false',
		"desc" => 'true',
		"height" => '',
		"boxtitle" => 'true',
		"worktype_slug" => '',
		"pagination" => 'false'
	), $atts));

$uniqureID=get_the_id()."-".uniqid();
$galleryID = uniqid();

if ($windowtype=="ajax") {
	$uniqureID = "ajax";
}
if ($autoplay <> "true") {
	$autoplay="false";
}
$column_type="four";
$portfolioImage_type=$imagesize;
$portfolioImage_type2="superlens-gridblock-tiny";

if ($worktype_slug=="-1") { $worktype_slug=''; }
$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$portfoliogrid2='';

if ($windowtype=="ajax") {
	$lightbox = "false";
}
$lightbox_container_class = '';
if ( $lightbox == 'true' ) {
	$lightbox_container_class = ' lightgallery-container';
}
if ($carousel_type=="owl") {

	$portfoliogrid_line1 = '<div class="gridblock-owlcarousel-wrap clearfix">';
	$portfoliogrid_line2 = '<div id="'.$uniqureID.'" class="owl-carousel owl-slideshow-element owl-carousel-detect'.$lightbox_container_class.' owl-carousel-type-'.$slideshowtype.'" data-autoplaytimeout="'.$autoplayinterval.'" data-smartspeed="'.$smartspeed.'" data-id="'.$uniqureID.'" data-autoplay="'.$autoplay.'" data-lazyload="'.$lazyload.'" data-type="'.$slideshowtype.'">';
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ($lazyload=="true") {
		$class_status = "owl-lazy";
	} else {
		$class_status = "owl-slide-image";
	}

	if ( $filter_image_ids ) {
		foreach ( $filter_image_ids as $attachment_id) {


            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

				//echo $type, $portfolio_type;
			$custom = get_post_custom(get_the_ID());
			$portfolio_cats = get_the_terms( get_the_ID(), 'worktypes' );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment_id , 'fullsize', false);
			$imageURI = $imagearray[0];			
			
			$slideshow_imagearray = wp_get_attachment_image_src( $attachment_id , $portfolioImage_type, false);
			$slideshow_imageURI = $slideshow_imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment_id , $portfolioImage_type2, false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			if ($portfolio_count==$columns) $portfolio_count=0;

			if ( isSet($slideshow_imageURI) && !empty( $slideshow_imageURI ) ) {

				$imageID = get_post($attachment_id);
				$imageTitle = $imageID->post_title;
				$imageDesc= $imageID->post_content;

				$protected="";
				$icon_class="column-gridblock-icon";
				$portfolio_count++;
				$portfoliogrid .= '<div class="gridblock-slideshow-element">';

				// Large image sequence
				if ($lightbox=="true") {
					$portfoliogrid .= imaginem_codepack_activate_lightbox (
						$lightbox_type="default",
						$ID=get_the_id(),
						$predefined=$imageURI,
						$mediatype="image",
						$imagetitle=$imageTitle,
						$class="lightbox-image",
						$set="owlslideshow-lightbox-set",
						$data_name="default",
						$external_thumbnail_id = $attachment_id,
						$imageDataID=$attachment_id,
						$galleryID
					);
				}
				
					$portfoliogrid .= imaginem_codepack_display_image_srcset (
						$attachment_id,
						$have_image_url=false,
						$link=false,
						$type=$portfolioImage_type,
						$imagetitle=$imageTitle,
						$class= $class_status,
						$lazyload_status = $lazyload
					);
				if ($lightbox=="true") {
					$portfoliogrid .= '</a>';
				}
					if ($displaytitle=='true') {
						$portfoliogrid .= '<div class="slideshow-owl-title">'.$imageTitle.'</div>';
					}

				$portfoliogrid .='</div>';

			}

		}
	}
	$portfoliogrid .='</div>';
}

	$portfoliogrid .='</div>';
	if ($windowtype=="ajax") {
		$portfoliogrid_script='';
	}

	$slideshow_1 = $portfoliogrid_line1 . $portfoliogrid_line2 . $portfoliogrid;

	return $slideshow_1;
}
add_shortcode("slideshowcarousel", "mtheme_imageslideshow");

//Recent Works Carousel
function mtheme_fotorama($atts, $content = null) {
	extract(shortcode_atts(array(
		"filltype" => 'cover',
		"transition" => 'crossfade',
		"autoplay" => 'false',
		"autoplayspeed" => '8000',
		"hash" => 'false',
		"pagetitle" => "false",
		"titledesc" => "enable",
		"titles" => "enable",
		"pageid" => '',
		"pb_image_ids" => '',
		"thumbnails" => 'true',
		"displaytitle" => 'false',
		"desc" => 'true',
		"worktype_slug" => ''
	), $atts));

$uniqureID=get_the_id()."-".uniqid();
$column_type="four";
$portfolioImage_type="superlens-gridblock-full";
$portfolioImage_type2="superlens-gridblock-large";

if ($worktype_slug=="-1") { $worktype_slug=''; }
$portfolio_count=0;
$flag_new_row=true;
$portfoliogrid='';
$portfoliogrid2='';

$fotorama_autoplay = 'false';
if ( 'true' === $autoplay ) {
	$fotorama_autoplay = $autoplayspeed;
}

$fotorama = '<div class="mtheme-fotorama">';
$fotorama .= '<div class="fotorama"
 data-fit="'.$filltype.'"
 data-nav="thumbs"
 data-shuffle="false"
 data-loop="true"
 data-thumbwidth="60"
 data-thumbheight="60"
 data-keyboard="true"
 data-hash="'.$hash.'"
 data-transition="'.$transition.'"
 data-transition-duration="800"
 data-autoplay="'.$fotorama_autoplay.'"
 data-auto="false"
 >';
	
	if (trim($pb_image_ids)<>'' ) {
		$filter_image_ids = explode(',', $pb_image_ids);
	} else {
		if ( !isSet($pageid) || empty($pageid) || $pageid=='' ) {
			$pageid = get_the_id();
		}
		$filter_image_ids = imaginem_codepack_get_custom_attachments ( $pageid );
	}

	if ( $filter_image_ids ) {
		foreach ( $filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
            if ( !$check_if_image_present ) {
                continue;
            }

				//echo $type, $portfolio_type;
			$custom = get_post_custom(get_the_ID());
			$portfolio_cats = get_the_terms( get_the_ID(), 'worktypes' );
			$lightboxvideo="";
			$thumbnail="";
			$customlink_URL="";
			$portfolio_thumb_header="Image";

			$imagearray = wp_get_attachment_image_src( $attachment_id , 'fullsize', false);
			$imageURI = $imagearray[0];

			$thumbnail_imagearray = wp_get_attachment_image_src( $attachment_id , 'superlens-gridblock-tiny', false);
			$thumbnail_imageURI = $thumbnail_imagearray[0];

			if ( isSet($imageURI) && !empty( $imageURI ) ) {

				$imageID = get_post($attachment_id);
				$imageTitle = $imageID->post_title;
				$imageDesc= $imageID->post_content;
				if ($titles<>"enable") {
					$imageTitle='';
				}
				$displaypagetitle='';
				if ($pagetitle=="true") {
					$displaypagetitle= '<h1>'.get_the_title().'</h1>';
				}

				$title_desc='';
				if ($titledesc=="enable") {
					if ($imageTitle<>"") {
						$title_desc = 'data-caption="'.esc_attr($displaypagetitle).'<h1>'.sanitize_text_field($imageTitle).'</h1>';
					}
					if ($imageDesc<>""){
						$title_desc .= '<p>'.esc_attr(sanitize_text_field($imageDesc)).'</p>';
					}
					$title_desc .= '" '; 
				}

				$fotorama .= '<a '.$title_desc.'href="'.$imageURI.'">';
				$fotorama .= '<img src="'.esc_url($thumbnail_imageURI).'" alt="'.esc_attr($imageTitle).'" />';
				$fotorama .= '</a>';
			}

		}
	}
	
	$fotorama .='</div>';
	$fotorama .='<div class="fotorama-caption"></div>';
	$fotorama .='</div>';

	return $fotorama;
}
add_shortcode("fotorama", "mtheme_fotorama");
?>