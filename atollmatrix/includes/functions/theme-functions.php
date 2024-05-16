<?php
function atollmatrix_get_site_description() {
	$site_description_status = atollmatrix_get_option_data( 'tag_textlogo_status' );

	$site_description_tag = '';
	if ( $site_description_status ) {
		$custom_logo_url = atollmatrix_get_option_data( 'custom_logo_url' );
		$home_url_path   = home_url( '/' );

		if ( '' !== $custom_logo_url ) {
			$home_url_path = $custom_logo_url;
		}

		$site_description_tag = '';
		$site_description     = get_bloginfo( 'description' );

		if ( '' !== $site_description ) {
			$site_description_tag = '<div class="site-description"><a href="' . esc_url( $home_url_path ) . '">' . $site_description . '</a></div>';
		}
	}
	return $site_description_tag;
}
function atollmatrix_menu_has_cart() {
	$support_status = false;
	if ( class_exists( 'woocommerce' ) ) {
		if ( atollmatrix_get_option_data( 'enable_header_cart' ) ) {
			$support_status = true;
		}
	}
	return $support_status;
}
function atollmatrix_set_header_class_from_type( $headertype ) {
	$class = 'header-type-auto';
	switch ( $headertype ) {
		case 'auto':
			$class = 'header-type-auto';
			break;
		case 'overlay':
			$class = 'header-type-overlay';
			break;
		default:
			$class = 'header-type-auto';
			break;
	}
	return $class;
}
function atollmatrix_get_header_type_class( $header_page_id ) {
	$header_type = get_post_meta( $header_page_id, 'pagemeta_header_type', true );

	switch ( $header_type ) {
		case 'auto':
			$type = 'header-type-auto';
			break;
		case 'overlay':
			$type = 'header-type-overlay';
			break;
		case 'inverse':
			$type = 'header-type-inverse';
			break;
		case 'inverse-overlay':
			$type = 'header-type-inverse-overlay';
			break;
		default:
			$type = 'header-type-auto';
			break;
	}

	return $type;
}
function atollmatrix_elementor_in_preview() {
	$elementor_in_preview = false;
	if ( class_exists( '\Elementor\Plugin' ) ) {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$elementor_in_preview = true;
		}
	}
	return $elementor_in_preview;
}
/**
 * Compontent functions
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.0.0
 */

/**
 * Minimal Header check
 */
function atollmatrix_header_is_minimal() {
	$header_is_minimal = false;
	$header_menu_type  = atollmatrix_get_option_data( 'menu_type' );

	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$header_menu_type = apply_filters( 'header_style', $header_menu_type );
	}
	if ( 'minimal-logo' === $header_menu_type ) {
		$header_is_minimal = true;
	}
	return $header_is_minimal;
}
function atollmatrix_header_is_compact() {
	$header_is_compact = false;
	$header_menu_type  = atollmatrix_get_option_data( 'menu_type' );

	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$header_menu_type = apply_filters( 'header_style', $header_menu_type );
	}
	if ( 'compact-minimal-top' === $header_menu_type || 'compact-minimal-left' === $header_menu_type ) {
		$header_is_compact = true;
	}
	return $header_is_compact;
}
function atollmatrix_header_is_toggle_main_menu() {
	$header_is_toggle_main = false;
	$header_menu_type      = atollmatrix_get_option_data( 'menu_type' );

	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$header_menu_type = apply_filters( 'header_style', $header_menu_type );
	}
	if ( 'toggle-main-menu' === $header_menu_type ) {
		$header_is_toggle_main = true;
	}
	return $header_is_toggle_main;
}
/**
 * Page has sidebar
 *
 * @param string $the_page_id Page ID.
 */
function atollmatrix_page_has_sidebar( $the_page_id ) {

	$sidebar_present       = false;
	$mtheme_sidebar_choice = get_post_meta( $the_page_id, 'pagemeta_sidebar_choice', true );

	if ( isset( $mtheme_sidebar_choice ) || ! empty( $mtheme_sidebar_choice ) ) {
		if ( is_active_sidebar( $mtheme_sidebar_choice ) ) {
			$sidebar_present = true;
		}
	}

	return $sidebar_present;
}
/**
 * Defer function
 *
 * @param string $tag Tag.
 * @param string $handle Handle.
 * @return $tag
 */
function atollmatrix_add_defer_attribute( $tag, $handle ) {
	// add script handles to the array below.
	$scripts_to_defer = array( 'atollmatrix-common', 'hoverIntent', 'easing', 'wp-embed', 'atollmatrix-verticalmenu', 'superfish', 'velocity', 'velocity-ui', 'touchswipe' );

	foreach ( $scripts_to_defer as $defer_script ) {
		if ( $defer_script === $handle ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}
	}
	return $tag;
}
/**
 * Gradient display function
 *
 * @param string $angle Angle.
 * @param string $color_one Color One.
 * @param string $color_two Color Two.
 * @return $style
 */
function atollmatrix_css_apply_gradient( $angle, $color_one, $color_two ) {

	$style = 'background-image: linear-gradient( ' . $angle . ', ' . $color_one . ' 10%, ' . $color_two . ' 100%);';

	return $style;

}
/**
 * Check if proofing is protected
 *
 * @return $protected
 */
function atollmatrix_is_proofing_client_protected() {
	$protected = false;
	if ( is_singular( 'proofing' ) ) {
		$client_id       = get_post_meta( get_the_id(), 'pagemeta_client_names', true );
		$proofing_status = get_post_meta( get_the_id(), 'pagemeta_proofing_status', true );
		if ( isset( $client_id ) && ! empty( $client_id ) && '' !== $client_id ) {
			if ( post_password_required( $client_id ) ) {
				$protected = true;
			}
		}
	}
	return $protected;
}
/**
 * Fullscreen has Audio
 *
 * @return $sound_found
 */
function atollmatrix_fullscreen_has_audio() {

	$featured_page = atollmatrix_get_active_fullscreen_post();
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$_type         = get_post_type( $featured_page );
		$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
	}

	$custom      = get_post_custom( $featured_page );
	$mp3_ext     = '';
	$mp3_sep     = '';
	$m4a_ext     = '';
	$m4a_sep     = '';
	$oga_ext     = '';
	$mp3_file    = false;
	$m4a_file    = false;
	$oga_file    = false;
	$sound_found = false;

	if ( isset( $custom['pagemeta_slideshow_mp3'][0] ) ) {
		$mp3_file = $custom['pagemeta_slideshow_mp3'][0];
	}
	if ( isset( $custom['pagemeta_slideshow_m4a'][0] ) ) {
		$m4a_file = $custom['pagemeta_slideshow_m4a'][0];
	}
	if ( isset( $custom['pagemeta_slideshow_oga'][0] ) ) {
		$oga_file = $custom['pagemeta_slideshow_oga'][0];
	}
	if ( $mp3_file ) {
		$sound_found = true;
		$mp3_ext     = 'mp3';
		if ( $m4a_file || $oga_file ) {
			$mp3_sep = ',';
		}
	}
	if ( $m4a_file ) {
		$sound_found = true;
		$m4a_ext     = 'm4a';
		if ( $oga_file ) {
			$m4a_sep = ',';
		}
	}
	if ( $oga_file ) {
		$sound_found = true;
		$oga_ext     = 'oga';
	}
	return $sound_found;
}
/**
 * Split slider CSS Generator
 *
 * @param boolean $preset_page_id The Page ID.
 * @return $slide_css
 */
function atollmatrix_splitslider_slide_gen_css( $preset_page_id = false ) {
	$featured_page = atollmatrix_get_active_fullscreen_post();

	if ( $preset_page_id ) {
		$featured_page = $preset_page_id;
	}
	// WPML Detector.
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$_type         = get_post_type( $featured_page );
		$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
	}
	// The Image IDs.
	$filter_image_ids = atollmatrix_get_custom_attachments( $featured_page );
	$i                = 0;
	$slide_css        = '';
	// Loop through the images.
	foreach ( $filter_image_ids as $attachment_id ) {
		$attachment = get_post( $attachment_id );
		$image_uri  = $attachment->guid;

		$thumb_imagearray = wp_get_attachment_image_src( $attachment->ID, 'full', false );
		$the_image_uri    = $thumb_imagearray[0];
		if ( isset( $the_image_uri ) ) {

			if ( ! isset( $first_image ) ) {
				$first_image = $the_image_uri;
			}

			$slide_css .= '.main-image-count-' . $i . ' { background-image: url(' . esc_url( $the_image_uri ) . '); }';
			$slide_css .= '.responsive-image-count-' . $i . ' { background-image: url(' . esc_url( $the_image_uri ) . '); }';
			$i++;
		}
	}

	if ( 0 !== $i % 2 ) {
		$slide_css .= '.main-image-count-first { background-image: url(' . esc_url( $first_image ) . '); }';
	}
	return $slide_css;
}
/**
 * Slideshow Static title
 *
 * @param string $slideshow_post_id Slideshow ID.
 * @param string $slideshowtype Slideshow type.
 */
function atollmatrix_maybe_display_slideshow_static_titles( $slideshow_post_id, $slideshowtype ) {

	$static_description                = '';
	$static_title                      = '';
	$static_link_text                  = '';
	$slideshow_link                    = '';
	$slideshow_title                   = '';
	$slideshow_caption                 = '';
	$coverphoto_textalign              = '';
	$static_url                        = '';
	$slideshow_titledesc               = 'enable';
	$cover_style                       = '';
	$slideshow_no_description          = '';
	$slideshow_no_description_no_title = '';
	$static_msg_display                = false;

	$custom = get_post_custom( $slideshow_post_id );
	if ( isset( $custom['pagemeta_static_title'][0] ) ) {
		$static_title = $custom['pagemeta_static_title'][0];
	}
	if ( isset( $custom['pagemeta_static_description'][0] ) ) {
		$static_description = $custom['pagemeta_static_description'][0];
	}
	if ( isset( $custom['pagemeta_static_link_text'][0] ) ) {
		$static_link_text = $custom['pagemeta_static_link_text'][0];
	}
	if ( isset( $custom['pagemeta_static_url'][0] ) ) {
		$static_url = $custom['pagemeta_static_url'][0];
	}
	if ( isset( $custom['pagemeta_cover_style'][0] ) ) {
		$cover_style = $custom['pagemeta_cover_style'][0];
	}
	if ( isset( $custom['pagemeta_coverphoto_textalign'][0] ) ) {
		$coverphoto_textalign = $custom['pagemeta_coverphoto_textalign'][0];
	}
	if ( '' === $static_description ) {
		$slideshow_no_description = 'slideshow_text_shift_up';
	}
	if ( '' === $static_description && '' === $static_title ) {
		$slideshow_no_description_no_title = 'slideshow_text_shift_up';
	}

	if ( $static_link_text ) {
		$slideshow_link = '<div class="static_slideshow_content_link ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $static_url ) . '">' . esc_attr( $static_link_text ) . '<span></span></a></div>';
	}
	if ( $static_title ) {
		$slideshow_title = '<h1 class="static_slideshow_title ' . $slideshow_no_description . ' ">' . esc_attr( $static_title ) . '</h1>';
	}
	if ( $static_description ) {
		$slideshow_caption = '<div class="static_slideshow_caption slideshow_caption_break">' . do_shortcode( $static_description ) . '</div>';
	}

	if ( '' !== $static_link_text || '' !== $static_title || '' !== $static_description || '' !== $static_url ) {
		$static_msg_display = true;
		$slide_ui_color     = atollmatrix_get_first_slide_ui_color( $slideshow_post_id );

		if ( 'cover' === $slideshowtype ) {
			$static_tag = '<div id="slideshow-text-box" class="coverphoto-static-text coverphoto-outer-wrap slideshow-outer-wrap"><div id="coverphoto-text-wrap" class="cover-photo-' . $coverphoto_textalign . ' slideshow-content-wrap fullscreen-coverphoto-outer coverphoto-type-' . esc_attr( $cover_style ) . '"><div class="fullscreen-coverphoto-inner coverphoto-text-container">' . $slideshow_title . $slideshow_caption . $slideshow_link . '</div></div></div>';
		} else {
			$static_tag = '<div id="static_slidecaption" class="slideshow-content-wrap">' . $slideshow_title . $slideshow_caption . $slideshow_link . '</div>';
		}
		if ( '' !== $static_tag ) {
			echo wp_kses( $static_tag, atollmatrix_get_allowed_tags() );
		}
	}
}
/**
 * Post Format Icons
 *
 * @param string $postformat Post format.
 * @return $postformat_icon
 */
function atollmatrix_get_postformat_icon( $postformat ) {
	switch ( $postformat ) {
		case 'video':
			$postformat_icon = 'feather-icon-play';
			break;
		case 'audio':
			$postformat_icon = 'feather-icon-volume';
			break;
		case 'gallery':
			$postformat_icon = 'feather-icon-layers';
			break;
		case 'quote':
			$postformat_icon = 'feather-icon-speech-bubble';
			break;
		case 'link':
			$postformat_icon = 'feather-icon-link';
			break;
		case 'aside':
			$postformat_icon = 'feather-icon-align-justify';
			break;
		case 'image':
			$postformat_icon = 'feather-icon-image';
			break;
		default:
			$postformat_icon = 'feather-icon-paper';
			break;
	}
	return $postformat_icon;
}
/**
 * Get post format
 *
 * @return $postformat
 */
function atollmatrix_get_postformat() {
	$postformat = get_post_format();
	if ( empty( $postformat ) ) {
		$postformat = 'standard';
	}
	return $postformat;
}
/**
 * Generate CSS
 *
 * @param string $customizer_css Array with CSS values.
 */
function atollmatrix_gen_css( $customizer_css ) {
	foreach ( $customizer_css as $option_id => $properties ) {
		$css_args = array(
			'class'    => $properties[0],
			'property' => $properties[1],
			'type'     => $properties[2],
		);

		unset( $css );

		$type = $css_args['type'];

		if ( 'gradient' !== $type ) {
			$value = atollmatrix_get_option_data( $option_id );
		} else {
			$value        = array();
			$value['one'] = atollmatrix_get_option_data( $option_id . '_one' );
			$value['two'] = atollmatrix_get_option_data( $option_id . '_two' );
		}
		switch ( $type ) {
			case 'meta_fullscreen_font':
				$featured_page = atollmatrix_get_active_fullscreen_post();
				$custom        = get_post_custom( $featured_page );
				if ( isset( $custom[ $option_id ][0] ) ) {
					$fullscreentitlefont = $custom[ $option_id ][0];
					if ( atollmatrix_permit_font( $fullscreentitlefont ) ) {
						if ( '' !== $fullscreentitlefont ) {
							$fullscreentitlefontname = substr( $fullscreentitlefont, 0, strpos( $fullscreentitlefont, ':' ) );
							if ( '' !== $fullscreentitlefontname ) {
								$css = $css_args['class'] . ' { ' . $css_args['property'] . ' : "' . $fullscreentitlefontname . '"; }';
							}
						}
					}
				}
				break;
			case 'backgroundrgba':
				$featured_page = atollmatrix_get_active_fullscreen_post();
				$custom        = get_post_custom( $featured_page );
				if ( isset( $custom[ $option_id ][0] ) ) {
					$value   = $custom[ $option_id ][0];
					$opacity = '1';
					if ( isset( $custom[ $option_id . '_opacity' ][0] ) ) {
						$opacity = $custom[ $option_id . '_opacity' ][0];
						if ( 'transparent' === $opacity ) {
							$opacity = '0';
						} else {
							$opacity = (int) $opacity / 100;
						}
					}
					$rgba_color = atollmatrix_hex_to_rgb( $value, true );
					$css        = $css_args['class'] . '{ background: rgba(' . $rgba_color . ',' . $opacity . '); }';
				}
				break;
			case 'font':
				if ( atollmatrix_permit_font( $value ) ) {
					if ( '' !== $value ) {
						$css = $css_args['class'] . ' { ' . $css_args['property'] . ' : "' . $value . '"; }';
					}
				}
				break;
			case 'toggle':
				if ( false === $value && 'display' === $css_args['property'] ) {
					$css = $css_args['class'] . ' {  display: none; }';
				}
				break;
			case 'gradient':
				if ( '' !== $value['one'] && '' === $value['two'] ) {
					$css = $css_args['class'] . '{ background: ' . $value['one'] . '; }';

				}
				if ( '' !== $value['one'] && '' !== $value['two'] ) {
					$css = $css_args['class'] . '{ background-image: linear-gradient( 135deg, ' . $value['one'] . ' 10%, ' . $value['two'] . ' 100%); }';
				}
				break;
			default:
				if ( '' !== $value ) {
					$css = $css_args['class'] . ' { ' . $css_args['property'] . ' : ' . $value . '; }';
				}
				break;
		}
		if ( isset( $css ) ) {
			wp_add_inline_style( 'atollmatrix-ResponsiveCSS', $css );
		}
	}
}
/**
 * Font permit
 *
 * @param string $font Font.
 * @return false
 */
function atollmatrix_permit_font( $font ) {
	$exclude = array( 'sans-serif', 'PT Mono' );
	if ( isset( $font ) && '' !== $font ) {
		if ( ! in_array( $font, $exclude, true ) ) {
			return true;
		}
	}
	return false;
}
function atollmatrix_page_is_built_with_elementor( $post_id ) {
	$status = get_post_meta( $post_id, '_elementor_edit_mode', true );
	return $status;
}
function atollmatrix_get_pagestyle( $post_id ) {

	$post_id = get_the_id();

	$pagestyle     = 'rightsidebar';
	$got_pagestyle = get_post_meta( get_the_id(), 'pagemeta_pagestyle', true );

	if ( class_exists( 'woocommerce' ) ) {

		$woocommerce_page_detected = false;

		if ( is_shop() ) {
			$shop_layout               = atollmatrix_get_option_data( 'shop_page_layout' );
			$woocommerce_page_detected = true;
		}

		if ( is_product_category() || is_product_tag() ) {
			$shop_layout               = atollmatrix_get_option_data( 'shop_archive_layout' );
			$woocommerce_page_detected = true;
		}

		if ( $woocommerce_page_detected ) {

			switch ( $shop_layout ) {
				case 'right':
					$got_pagestyle = 'rightsidebar';
					break;
				case 'left':
					$got_pagestyle = 'leftsidebar';
					break;
				case 'fullwidth':
					$got_pagestyle = 'nosidebar';
					break;

				default:
					$got_pagestyle = 'nosidebar';
					break;
			}
		}
	}

	switch ( $got_pagestyle ) {
		case 'rightsidebar':
			$pagestyle = 'rightsidebar';
			break;
		case 'leftsidebar':
			$pagestyle = 'leftsidebar';
			break;
		case 'nosidebar':
			$pagestyle = 'nosidebar';
			break;
		case 'edge-to-edge':
			$pagestyle = 'edge-to-edge';
			break;

		default:
			$pagestyle = 'nosidebar';
			if ( is_active_sidebar( 'default_sidebar' ) ) {
				$pagestyle = 'rightsidebar';
			}
			if ( atollmatrix_page_is_built_with_elementor( $post_id ) ) {
				$pagestyle = 'edge-to-edge';
				update_post_meta( $post_id, 'pagemeta_pagestyle', 'edge-to-edge' );
			}

			break;
	}

	return $pagestyle;
}
function atollmatrix_display_client_password( $id, $client_title = true, $desc = true, $eventdetails = false, $proofing_id = false, $pagetitle = false ) {

	$passwordbox  = '';
	$passwordbox .= '<div id="vertical-center-wrap">';
	$passwordbox .= '<div class="vertical-center-outer">';
	$passwordbox .= '<div class="vertical-center-inner">';
	$passwordbox .= '<div class="entry-content proofing-card-wrap client-gallery-protected" id="password-protected">';
	$passwordbox .= '<div class="proofing-card-section client-info-details">';
	$passwordbox .= atollmatrix_display_client_details( $id, $client_title, $desc, $eventdetails, $proofing_id, $pagetitle );
	$passwordbox .= '</div>';
	$passwordbox .= '<div class="proofing-card-section client-gallery-password-form">';
	$passwordbox .= '<div class="locked-status"><i class="ion-ios-locked"></i></div>';
	$passwordbox .= get_the_password_form();
	$passwordbox .= '</div>';
	$passwordbox .= '</div>';
	$passwordbox .= '</div>';
	$passwordbox .= '</div>';
	$passwordbox .= '</div>';

	echo wp_kses( $passwordbox, atollmatrix_get_allowed_tags() );
}
function atollmatrix_show_continue_reading( $continue_text, $link ) {
	$output  = '<a class="theme-btn-link" href="' . esc_url( $link ) . '">';
	$output .= '<div class="theme-btn theme-btn-outline-primary theme-hover-arrow">' . esc_html( $continue_text ) . '</div>';
	$output .= '</a>';
	return $output;
}
function atollmatrix_multiscroll_block_build( $left_section, $right_section, $responsive_element ) {
	$multiscroll_block  = '<div id="fullscreen-multiscroll">';
	$multiscroll_block .= '<div id="multiscroll">';
	$multiscroll_block .= '<div class="ms-left splitslider-section">';
	$multiscroll_block .= $left_section;
	$multiscroll_block .= '</div>';
	$multiscroll_block .= '<div class="ms-right splitslider-section">';
	$multiscroll_block .= $right_section;
	$multiscroll_block .= '</div>';
	$multiscroll_block .= '</div>';
	$multiscroll_block .= '</div>';

	$responsive_block  = '<div id="responsive-multiscroll">';
	$responsive_block .= $responsive_element;
	$responsive_block .= '</div>';

	return $multiscroll_block . $responsive_block;
}
function atollmatrix_convert_breaks_to_list( $data = '' ) {
	$data      = nl2br( $data );
	$bits      = explode( '<br />', $data );
	$newstring = '<ul>';
	foreach ( $bits as $bit ) {
		$newstring .= '<li>' . $bit . '</li>';
	}
	$newstring .= '</ul>';

	return $newstring;
}
function atollmatrix_convert_newlines_to_list( $data = '' ) {
	$bits      = explode( '\n', $data );
	$newstring = '<ul>';
	foreach ( $bits as $bit ) {
		$newstring .= '<li>' . $bit . '</li>';
	}
	$newstring .= '</ul>';

	return $newstring;
}

function atollmatrix_get_image_id_from_url( $image_url ) {
	$attachment = attachment_url_to_postid( $image_url );
	if ( $attachment ) {
		return $attachment;
	} else {
		return false;
	}
}
function atollmatrix_generate_fullscreen_portfoliocarousel( $worktype_slugs, $slideshow_titledesc ) {
	$carousel = '';
	$count    = 0;
	$limit    = -1;

	if ( '' !== $worktype_slugs ) {
		$type_explode = explode( ',', $worktype_slugs );
		foreach ( $type_explode as $work_slug ) {
			$terms[] = $work_slug;
		}
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
			'tax_query'      => array(
				array(
					'taxonomy' => 'types',
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				),
			),
		);
	} else {
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
		);
	}

	$portfolioslideshow = new WP_Query( $args );

	if ( $portfolioslideshow->have_posts() ) :
		while ( $portfolioslideshow->have_posts() ) :
			$portfolioslideshow->the_post();

			$image_uri      = atollmatrix_featured_image_link( get_the_ID() );
			$portfolio      = get_post_custom( get_the_ID() );
			$customlink_url = '';
			if ( isset( $portfolio['pagemeta_thumbnail_desc'][0] ) ) {
				$description = $portfolio['pagemeta_thumbnail_desc'][0];
			}
			if ( isset( $portfolio['pagemeta_customlink'][0] ) ) {
				$customlink_url = $portfolio['pagemeta_customlink'][0];
			}

			$link_url = get_permalink();

			if ( '' !== $customlink_url ) {
				$link_url = $customlink_url;
			}
			$link_text = atollmatrix_get_option_data( 'portfolio_fullscreen_viewtext' );

			$image_title = get_the_title();
			$alt         = get_the_title();
			$image_desc  = $description;

			$count++;
			$carousel .= '<li data-id="' . esc_attr( $count ) . '" data-position="0" data-title="' . esc_attr( $image_title ) . '" class="hc-slides slide-' . esc_attr( $count ) . '">';
			$carousel .= '<div class="hc-image-wrap">';
			$carousel .= '<img src="' . esc_url( $image_uri ) . '" alt="' . esc_attr( $image_title ) . '"/>';
			if ( 'enable' === $slideshow_titledesc ) {
				$carousel .= '<div class="responsive-titles">';
				$carousel .= '<div class="title"><h3 class="colorswitch">' . $image_title . '</h3></div>';
				$carousel .= '<div class="description colorswitch">' . $image_desc . '</div>';
				$carousel .= '<div class="carousel-button text-is-bright"><a class="mtheme-button" href="' . esc_url( $link_url ) . '">' . $link_text . '</a></div>';
				$carousel .= '</div>';
			}
			$carousel .= '</div>';
			$carousel .= '</li>';
		endwhile;
	endif;
	wp_reset_postdata();
	return $carousel;
}
function atollmatrix_generate_fullscreen_carousel( $filter_image_ids, $slideshow_titledesc ) {
	$carousel = '';
	$count    = 0;
	foreach ( $filter_image_ids as $attachment_id ) {
		$attachment = get_post( $attachment_id );
		$image_uri  = $attachment->guid;

		$thumb_imagearray = wp_get_attachment_image_src( $attachment->ID, 'gridblock-full', false );
		$thumb_image_uri  = $thumb_imagearray[0];
		$image_title      = $attachment->post_title;
		$image_desc       = $attachment->post_content;
		$link_text        = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
		$link_url         = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
		$button_target    = get_post_meta( $attachment->ID, 'mtheme_fullscreen_url_target', true );
		$featured_page    = atollmatrix_get_active_fullscreen_post();

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$_type         = get_post_type( $featured_page );
			$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
		}
		$custom         = get_post_custom( $featured_page );
		$carousel_title = 'enable';
		$carousel_desc  = 'enable';
		if ( isset( $custom['pagemeta_carousel_title'][0] ) ) {
				$carousel_title = $custom['pagemeta_carousel_title'][0];
		}
		if ( isset( $custom['pagemeta_carousel_desc'][0] ) ) {
			$carousel_desc = $custom['pagemeta_carousel_desc'][0];
		}

		$linktarget = '';
		if ( 'blank' === $button_target ) {
			$linktarget = "target='_blank' ";
		}

		$button_link = '';
		if ( '' !== $link_url ) {
			$button_link = '<a ' . $linktarget . 'class="positionaware-button" href="' . esc_url( $link_url ) . '">' . esc_attr( $link_text ) . '</a>';
		}

		$carousel_title_block   = '';
		$carousel_desc_block    = '';
		$carousel_button_block  = '';
		$carousel_caption_block = false;

		if ( '' !== $thumb_image_uri ) {
			$count++;
			$carousel .= '<li data-id="' . esc_attr( $count ) . '" data-position="0" data-title="' . esc_attr( $image_title ) . '" class="hc-slides slide-' . esc_attr( $count ) . '">';
			$carousel .= '<div class="hc-image-wrap">';
			$carousel .= '<img src="' . esc_url( $thumb_image_uri ) . '" alt="' . esc_attr( $image_title ) . '"/>';
			if ( 'enable' === $slideshow_titledesc ) {

				if ( 'disable' !== $carousel_title ) {
					$carousel_title_block   = '<div class="title"><h3 class="colorswitch">' . sanitize_text_field( $image_title ) . '</h3></div>';
					$carousel_caption_block = true;
				}
				if ( 'disable' !== $carousel_desc ) {
					$carousel_desc_block    = '<div class="description colorswitch">' . sanitize_text_field( $image_desc ) . '</div>';
					$carousel_caption_block = true;
				}
				if ( '' !== $link_url ) {
					$carousel_button_block  = '<div class="description desc-button colorswitch">' . $button_link . '</div>';
					$carousel_caption_block = true;
				}

				if ( $carousel_caption_block ) {
					$carousel .= '<div class="responsive-titles">';
					$carousel .= $carousel_title_block;
					$carousel .= $carousel_desc_block;
					$carousel .= $carousel_button_block;
					$carousel .= '</div>';
				}
			}
			$carousel .= '</div>';
			$carousel .= '</li>';
		}
	}
	return $carousel;
}
function atollmatrix_get_placeholder_image( $type ) {
	switch ( $type ) {
		case 'atollmatrix-gridblock-square-big':
			$fallback_file = 'placeholder-770x770';
			break;
		case 'atollmatrix-gridblock-large':
			$fallback_file = 'placeholder-770x550';
			break;
		case 'atollmatrix-gridblock-large-portrait':
			$fallback_file = 'placeholder-550x770';
			break;
		case 'atollmatrix-gridblock-full-medium':
			$fallback_file = 'placeholder-770x550';
			break;
		case 'atollmatrix-gridblock-full':
			$fallback_file = 'placeholder-770x550';
			break;
		default:
			$fallback_file = 'placeholder-770x550';
			break;
	}
	$fallback_image = get_template_directory_uri() . '/images/placeholders/' . $fallback_file . '.gif';
	return $fallback_image;
}
function atollmatrix_proofing_client_single_info( $id ) {

	$custom                   = get_post_custom( $id );
	$proofing_status          = '';
	$dont_show_page           = false;
	$proofing_proofing_column = '3';
	$proofing_proofing_format = 'landscape';
	$proofing_download        = '';

	if ( isset( $custom['pagemeta_proofing_status'][0] ) ) {
		$proofing_status = $custom['pagemeta_proofing_status'][0];
	}
	if ( isset( $custom['pagemeta_proofing_startdate'][0] ) ) {
		$proofing_startdate = $custom['pagemeta_proofing_startdate'][0];
	}
	if ( isset( $custom['pagemeta_proofing_client'][0] ) ) {
		$proofing_client = $custom['pagemeta_proofing_client'][0];
	}
	if ( isset( $custom['pagemeta_proofing_location'][0] ) ) {
		$proofing_location = $custom['pagemeta_proofing_location'][0];
	}
	if ( isset( $custom['pagemeta_proofing_column'][0] ) ) {
		$proofing_proofing_column = $custom['pagemeta_proofing_column'][0];
	}
	if ( isset( $custom['pagemeta_proofing_format'][0] ) ) {
		$proofing_proofing_format = $custom['pagemeta_proofing_format'][0];
	}
	if ( isset( $custom['pagemeta_proofing_download'][0] ) ) {
		$proofing_download = $custom['pagemeta_proofing_download'][0];
	}
	if ( isset( $custom['pagemeta_client_names'][0] ) ) {
		$client_id = $custom['pagemeta_client_names'][0];
	}

	$proofing_client_info  = '';
	$proofing_client_info .= '<div class="proofing-content-wrap">';
	$proofing_client_info .= '<div class="proofing-content">';
	$proofing_locked_msg   = esc_html__( 'Proofing gallery selection has been locked.', 'atollmatrix' );
	$proofing_active_msg   = esc_html__( 'Proofing gallery is active for selection.', 'atollmatrix' );

	$proofing_disable_msg  = esc_html__( 'Please contact us to activate this proofing gallery.', 'atollmatrix' );
	$proofing_download_msg = esc_html__( 'Proofing gallery Locked for Download.', 'atollmatrix' );
	if ( ! post_password_required() ) {
		if ( isset( $client_id ) && '' !== $client_id && 'none' !== $client_id ) {
			$proofing_client_info .= '<div class="entry-content client-gallery-details proofing-client-details">';
			$proofing_client_info .= '<div class="proofing-client-details-inner">';
			$proofing_client_info .= atollmatrix_display_client_details( $client_id, $client_title = true, $desc = true, $eventdetails = true, $proofing_id = get_the_id(), $pagetitle = true );
			if ( isset( $proofing_download ) && '' !== $proofing_download && 'download' === $proofing_status ) {
				$button_style          = '';
				$proofing_client_info .= '<div class="button-shortcode ' . esc_attr( $button_style ) . ' proofing-gallery-button">';
				$proofing_client_info .= '<a target="_blank" href="' . esc_url( $proofing_download ) . '">';
				$proofing_client_info .= '<div class="mtheme-button big-button">';
				$proofing_client_info .= '<i class="fa fa-download"></i> ';
				$proofing_client_info .= esc_html__( 'Download', 'atollmatrix' );
				$proofing_client_info .= '</div>';
				$proofing_client_info .= '</a>';
				$proofing_client_info .= '</div>';
			}
			$proofing_client_info .= '</div>';
			$proofing_client_info .= '</div>';
		}
	}
	$proofing_client_info .= '</div>';
	$proofing_client_info .= '</div>';

	return $proofing_client_info;
}
function atollmatrix_get_svg_icon_url_of( $icon ) {
	if ( false !== strpos( $icon, 'et-icon-' ) ) {
		$url = get_template_directory_uri() . '/css/fonts/svgs/et-icon/' . $icon . '.svg';
	}
	if ( false !== strpos( $icon, 'ion-' ) ) {
		$url = get_template_directory_uri() . '/css/fonts/svgs/ionicon/' . $icon . '.svg';
	}
	if ( false !== strpos( $icon, 'simpleicon-' ) ) {
		$url = get_template_directory_uri() . '/css/fonts/svgs/simpleicon/' . $icon . '.svg';
	}
	return $url;
}


// Password form
add_action( 'atollmatrix_display_password_form', 'atollmatrix_display_password_form_action' );
function atollmatrix_display_password_form_action() {
	echo '<div id="password-protected" class="clearfix">';
	echo '<div class="passwordform-wrap">';
	echo '<div class="protected-indicate-icon"><i class="ion-ios-locked-outline"></i></div>';
	echo '<div class="entry-content password-form-padding">';
	echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
	do_action( 'atollmatrix_demo_password' );
	echo wp_kses( get_the_password_form(), atollmatrix_get_allowed_tags() );
	echo '</div>';
	echo '</div>';
	echo '</div>';
}

function atollmatrix_reservation_screen_modal() {
	echo '<div id="modal-reservation">';
		echo '<div class="window-modal-outer">';
			echo '<div class="window-modal-inner">';
				echo '<div class="window-modal-text entry-content">';
					echo '<span class="reservation-modal-exit"><i class="ion-ios-close-empty"></i></span>';
					get_template_part( '/includes/reservation', 'page' );
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_action( 'atollmatrix_reservation_screen', 'atollmatrix_reservation_screen_modal' );
function atollmatrix_contextmenu_msg_enable() {
	$rightclicktext = atollmatrix_get_option_data( 'rightclick_disabletext' );
	$output         = '<div id="dimmer"><div class="dimmer-outer"><div class="dimmer-inner"><div class="dimmer-text">' . $rightclicktext . '</div></div></div></div>';
	echo wp_kses( $output, atollmatrix_get_allowed_tags() );
}
add_action( 'atollmatrix_display_elementloader', 'atollmatrix_display_elementloader_build' );
function atollmatrix_display_elementloader_build() {

	$preloader_style = atollmatrix_get_option_data( 'preloader_style' );

	switch ( $preloader_style ) {
		case 'spinner':
			$start_loading_tag = '<div class="preloader-cover-screen"><div class="preloader-cover-logo"></div></div>';
			$preloader_tag     = $start_loading_tag;
			$preloader_tag    .= '<div class="preloader-style-spinner loading-spinner-primary loading-spinner-detect loading-spinner">';
			$preloader_tag    .= '<div class="loading-right-side">';
			$preloader_tag    .= '<div class="loading-bar"></div>';
			$preloader_tag    .= '</div>';
			$preloader_tag    .= '<div class="loading-left-side">';
			$preloader_tag    .= '<div class="loading-bar"></div>';
			$preloader_tag    .= '</div>';
			$preloader_tag    .= '</div>';
			break;
		case 'default':
			$preloader_tag  = '<div class="preloader-style-default loading-spinner-primary loading-spinner-detect loading-spinner">';
			$preloader_tag .= '<div class="themeloader"><div class="themeloader__figure"></div></div>';
			$preloader_tag .= '</div>';
			break;

		default:
			$preloader_tag  = '<div class="preloader-style-default loading-spinner-primary loading-spinner-detect loading-spinner">';
			$preloader_tag .= '<div class="themeloader"><div class="themeloader__figure"></div></div>';
			$preloader_tag .= '</div>';
			break;
	}

	echo wp_kses( $preloader_tag, atollmatrix_get_allowed_tags() );
}
add_action( 'atollmatrix_display_dashboard_elementloader', 'atollmatrix_display_dashboard_elementloader_build' );
function atollmatrix_display_dashboard_elementloader_build() {
	$preloader_tag  = '<div class="loading-dashboard-spinner loading-dashboard-spinner-detect loading-dashboard-spinner">';
	$preloader_tag .= '<div class="themeloader"><div class="themeloader__figure"></div></div>';
	$preloader_tag .= '</div>';
	echo wp_kses( $preloader_tag, atollmatrix_get_allowed_tags() );
}
add_action( 'atollmatrix_display_fullscreen_toggle', 'atollmatrix_display_fullscreen_toggle_build' );
function atollmatrix_display_fullscreen_toggle_build() {
	echo '<div class="slideshow-control-item mtheme-fullscreen-toggle fullscreen-toggle-off"><i class="feather-icon-plus"></i></div>';
}
/**
 * [atollmatrix_language_selector_flags description]
 * @return [type] [description]
 */
function atollmatrix_language_selector_flags( $flags = true, $language = false ) {
	$wpml_lang_bar = false;
	if ( function_exists( 'icl_get_languages' ) ) {
		$languages  = icl_get_languages( 'skip_missing=N&orderby=KEY&order=asc&link_empty_to=str' );
		$wpml_style = atollmatrix_get_option_data( 'wpml_style' );
		if ( ! empty( $languages ) ) {
			$wpml_lang_bar = '<div class="wpml-flags-language-list ' . esc_attr( $wpml_style ) . '">';
			foreach ( $languages as $l ) {
				if ( ! $l['active'] ) {
					$wpml_lang_bar .= '<span class="selectable">';
				} else {
					$wpml_lang_bar .= '<span class="non-selectable language-active">';
				}

				if ( ! $l['active'] ) {
					$wpml_lang_bar .= '<a href="' . esc_url( $l['url'] ) . '">';
				}
				if ( $l['country_flag_url'] ) {
					if ( 'lang-code' !== $wpml_style ) {
						$wpml_lang_bar .= '<img src="' . esc_url( $l['country_flag_url'] ) . '" height="12" alt="' . esc_attr( $l['language_code'] ) . '" width="18" />';
					}
				}
				if ( 'default' !== $wpml_style ) {
					$wpml_lang_bar .= $l['translated_name'];
				}
				if ( ! $l['active'] ) {
					$wpml_lang_bar .= '</a>';
				}
				$wpml_lang_bar .= '</span>';
			}
			$wpml_lang_bar .= '</div>';
		}
	}
	return $wpml_lang_bar;
}
/**
 * [atollmatrix_is_in_demo description]
 * @param  boolean $output [description]
 * @return [type]          [description]
 */
function atollmatrix_is_in_demo( $output = false ) {
	$output = false;
	return $output;
}
/**
 * [atollmatrix_is_in_demo description]
 * @param  boolean $output [description]
 * @return [type]          [description]
 */
function atollmatrix_disabled_for_demo( $output = false ) {
	$output = false;
	if ( function_exists( 'theme_feature_disabled_for_demo' ) ) {
		$output = true;
	} else {
		$output = false;
	}
	return $output;
}
add_action( 'atollmatrix_page_necessities', 'atollmatrix_page_necessities_build' );
function atollmatrix_page_necessities_build( $got_page_id = false ) {
	if ( is_singular() ) {
		if ( ! $got_page_id ) {
			$page_data = get_post_custom( get_the_id() );
		}
		if ( isset( $page_data['pagemeta_pagestyle'][0] ) ) {
			$pagestyle = $page_data['pagemeta_pagestyle'][0];
			if ( 'split-page' === $pagestyle ) {
				echo '<div class="split-page-image"></div>';
			}
		}
	}
}
function atollmatrix_menu_is_vertical( $output = false ) {
	$output           = false;
	$header_menu_type = atollmatrix_get_option_data( 'menu_type' );
	if ( atollmatrix_is_in_demo() ) {
		if ( false !== atollmatrix_get_option_data( 'menu_type' ) ) {
			$header_menu_type = atollmatrix_get_option_data( 'menu_type' );
		}
	}
	if ( 'vertical-menu' === $header_menu_type || 'vertical-menu-right' === $header_menu_type ) {
		$output = true;
	}
	return $output;
}
function atollmatrix_menu_is_horizontal( $output = false ) {
	$output           = false;
	$header_menu_type = atollmatrix_get_option_data( 'header_menu_type' );
	if ( atollmatrix_is_in_demo() ) {
		if ( false !== atollmatrix_demo_get_data( 'menu_type' ) ) {
			$header_menu_type = atollmatrix_demo_get_data( 'menu_type' );
		}
	}
	if ( 'left-detatched' === $header_menu_type ) {
		$output = true;
	}
	if ( 'left-logo' === $header_menu_type ) {
		$output = true;
	}
	if ( 'center-logo' === $header_menu_type ) {
		$output = true;
	}
	if ( 'split-menu' === $header_menu_type ) {
		$output = true;
	}
	return $output;
}
function atollmatrix_page_supports_fullscreen( $got_page_id = false ) {
	$status = false;
	if ( ! $got_page_id ) {
		$got_page_id = get_the_id();
	}
	if ( atollmatrix_is_fullscreen_post() ) {
		$status = true;
	}
	$custom = get_post_custom( $got_page_id );
	if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
		$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
		if ( 'photowall' === $fullscreen_type || 'carousel' === $fullscreen_type || 'swiperslides' === $fullscreen_type ) {
				$status = false;
		}
	}
	if ( post_password_required() ) {
		$status = false;
	}

	return $status;
}
function atollmatrix_get_background_choice( $got_page_id = false ) {

	$atollmatrix_background_choice = false;
	if ( $got_page_id ) {
		$atollmatrix_background_choice = get_post_meta( $got_page_id, 'pagemeta_meta_background_choice', true );
		if ( ! isset( $atollmatrix_background_choice ) ) {
			$atollmatrix_background_choice = 'none';
		}
	}

	return $atollmatrix_background_choice;
}
function atollmatrix_proofing_is_password_protected() {
	$protection_status = false;
	$custom            = get_post_custom( get_the_ID() );
	if ( isset( $custom['pagemeta_client_names'][0] ) ) {
		$client_id = $custom['pagemeta_client_names'][0];
	}
	if ( post_password_required( $client_id ) ) {
		$protection_status = true;
	}
	return $protection_status;
}
function atollmatrix_demo_password_show() {
	if ( atollmatrix_is_in_demo() ) {
		return false;
	}
}
add_action( 'atollmatrix_demo_password', 'atollmatrix_demo_password_show' );
function atollmatrix_screencover_enable() {
	echo '<div class="fullscreen-screencover"></div>';
}
add_action( 'atollmatrix_screencover', 'atollmatrix_screencover_enable' );
function atollmatrix_display_client_details( $client_id, $client_title = false, $desc = false, $eventdetails = false, $proofing_id = false, $pagetitle = false ) {

	if ( isset( $client_id ) ) {
		$post_thumbnail_id = get_post_thumbnail_id( $client_id );
		$image_data        = wp_get_attachment_image_src( $post_thumbnail_id, 'atollmatrix-gridblock-square-big', false );
		if ( isset( $image_data[0] ) ) {
			$image_link = $image_data[0];
		}
		$client_data   = get_post_custom( $client_id );
		$proofing_data = get_post_custom( $proofing_id );
		$client_name   = '';
		$client_desc   = '';
		if ( isset( $proofing_data['pagemeta_proofing_startdate'][0] ) ) {
			$proofing_startdate = $proofing_data['pagemeta_proofing_startdate'][0];
		}
		if ( isset( $proofing_data['pagemeta_proofing_location'][0] ) ) {
			$proofing_location = $proofing_data['pagemeta_proofing_location'][0];
		}
		if ( isset( $client_data['pagemeta_client_name'][0] ) ) {
			$client_name = $client_data['pagemeta_client_name'][0];
		}
		if ( isset( $client_data['pagemeta_client_desc'][0] ) ) {
			$client_desc = $client_data['pagemeta_client_desc'][0];
		}

		$client_info = '<div class="proofing-client-wrap">';
		if ( isset( $image_link ) ) {
			$client_info .= '<div class="proofing-client-image"><img src="' . esc_url( $image_link ) . '" alt="' . esc_attr__( 'client', 'atollmatrix' ) . '" /></div>';
		}
		$client_info .= '<div class="proofing-client-info-wrap">';
		if ( $pagetitle ) {
			$client_info .= '<div class="proofing-page-title"><h1 class="entry-title">' . get_the_title( get_the_id() ) . '</h1></div>';
		}
		if ( $client_title ) {
			$client_info .= '<div class="proofing-client-title">' . $client_name . '</div>';
		}
		if ( is_singular( 'proofing' ) || is_singular( 'clients' ) ) {

			$password_protected = false;
			if ( is_singular( 'proofing' ) ) {
				$client_id_check = get_post_meta( get_the_id(), 'pagemeta_client_names', true );
				if ( isset( $client_id_check ) ) {
					if ( post_password_required( $client_id_check ) ) {
						$password_protected = true;
					}
				}
			}
			if ( is_singular( 'clients' ) ) {
				if ( post_password_required() ) {
					$password_protected = true;
				}
			}
			if ( $desc ) {
				$client_info .= '<div class="proofing-client-desc">' . $client_desc . '</div>';
			}
		}
		if ( $eventdetails ) {
			$client_info .= '<ul class="event-details event-date-time">';
			if ( isset( $proofing_startdate ) && '' !== $proofing_startdate ) {
				$client_info .= '<li><i class="feather-icon-clock"></i>' . $proofing_startdate . '</li>';
			}
			if ( isset( $proofing_location ) && '' !== $proofing_location ) {
				$client_info .= '<li><i class="feather-icon-map"></i>' . $proofing_location . '</li>';
			}
			$client_info .= '</ul>';
		}
		$client_info .= '</div>';
		$client_info .= '</div>';
	}
	return $client_info;
}
function atollmatrix_get_client_name( $client_id ) {
	if ( isset( $client_id ) ) {
		$client_data = get_post_custom( $client_id );
		$client_name = '';
		if ( isset( $client_data['pagemeta_client_name'][0] ) ) {
			$client_name = $client_data['pagemeta_client_name'][0];
		}
	}
	return $client_name;
}
function atollmatrix_generate_supersized_script_from_portfolio() {
	$featured_page = atollmatrix_get_active_fullscreen_post();
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) { // this is to not break code in case WPML is turned off, etc.
		$_type         = get_post_type( $featured_page );
		$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
	}
	//Slideshow Settings
	$slideshow_autoplay      = atollmatrix_get_option_data( 'slideshow_autoplay' );
	$slideshow_pause_on_last = atollmatrix_get_option_data( 'slideshow_pause_on_last' );
	$slideshow_pause_hover   = atollmatrix_get_option_data( 'slideshow_pause_hover' );
	$slideshow_random        = atollmatrix_get_option_data( 'slideshow_random' );
	$slideshow_interval      = atollmatrix_get_option_data( 'slideshow_interval', '8000' );

	$custom = get_post_custom( $featured_page );
	if ( isset( $custom['pagemeta_fullscreenslideshow_transition'][0] ) ) {
		$slideshow_transition_getter = $custom['pagemeta_fullscreenslideshow_transition'][0];
		switch ( $slideshow_transition_getter ) {
			case 'wave':
				$slideshow_transition = '0';
				break;
			case 'fade':
				$slideshow_transition = '1';
				break;
			case 'zoom':
				$slideshow_transition = '1';
				break;

			default:
				$slideshow_transition = '0';
				break;
		}
	} else {
		$slideshow_transition = '1';
	}

	$slideshow_transition_speed     = atollmatrix_get_option_data( 'slideshow_transition_speed', '1000' );
	$slideshow_portrait             = atollmatrix_get_option_data( 'slideshow_portrait' );
	$slideshow_landscape            = atollmatrix_get_option_data( 'slideshow_landscape' );
	$slideshow_fit_always           = atollmatrix_get_option_data( 'slideshow_fit_always' );
	$slideshow_vertical_center      = atollmatrix_get_option_data( 'slideshow_vertical_center' );
	$slideshow_horizontal_center    = atollmatrix_get_option_data( 'slideshow_horizontal_center' );
	$fullscreen_menu_toggle         = atollmatrix_get_option_data( 'fullscreen_menu_toggle' );
	$fullscreen_menu_toggle_nothome = atollmatrix_get_option_data( 'fullscreen_menu_toggle_nothome' );
	$rootpath                       = get_stylesheet_directory_uri();

	if ( ! $slideshow_autoplay ) {
		$slideshow_autoplay = 0;
	}
	if ( ! $slideshow_pause_on_last ) {
		$slideshow_pause_on_last = 0;
	}
	if ( ! $slideshow_pause_hover ) {
		$slideshow_pause_hover = 0;
	}
	if ( ! $slideshow_fit_always ) {
		$slideshow_fit_always = 0;
	}
	if ( ! $slideshow_portrait ) {
		$slideshow_portrait = 0;
	}
	if ( ! $slideshow_landscape ) {
		$slideshow_landscape = 0;
	}
	if ( ! $slideshow_vertical_center ) {
		$slideshow_vertical_center = 0;
	}
	if ( ! $slideshow_horizontal_center ) {
		$slideshow_horizontal_center = 0;
	}

	$supersized_image_path = get_template_directory_uri() . '/images/supersized/';
	$slideshow_thumbnails  = '';
	$featured_linked       = false;
	$the_attatchment_url   = '';
	$post_link             = '';
	$thelimit              = -1;
	$count                 = 0;
	$page_background_class = '';
	// Don't Populate list if no Featured page is set

	$count           = 0;
	$limit           = -1;
	$terms           = array();
	$work_slug_array = array();
	$worktype_slugs  = '';
	if ( isset( $custom['pagemeta_photowall_workstypes'][0] ) ) {
		$worktype_slugs = $custom['pagemeta_photowall_workstypes'][0];
	}
	$args = array();
	if ( '' !== $worktype_slugs ) {
		$type_explode = explode( ',', $worktype_slugs );
		foreach ( $type_explode as $work_slug ) {
			$terms[] = $work_slug;
		}
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
			'tax_query'      => array(
				array(
					'taxonomy' => 'types',
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				),
			),
		);
	} else {
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
		);
	}

	$portfolioslideshow = new WP_Query( $args );

	if ( '' !== $featured_page ) {
		$slideshow_thumbnails_status = '0';
		if ( 'thumbnails' === $slideshow_thumbnails ) {
			$slideshow_thumbnails_status = '1';
		}
		// Static Titles and Description block
		$fullscreen_type      = '';
		$static_description   = '';
		$static_title         = '';
		$static_link_text     = '';
		$slideshow_link       = '';
		$slideshow_title_text = '';
		$slideshow_caption    = '';
		$static_url           = '';
		$fullscreen_infobox   = '';
		$cover_style          = '';
		$slideshow_titledesc  = 'enable';
		$custom               = get_post_custom( $featured_page );
		if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
			$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
		}
		if ( isset( $custom['pagemeta_static_title'][0] ) ) {
			$static_title = $custom['pagemeta_static_title'][0];
		}
		if ( isset( $custom['pagemeta_static_description'][0] ) ) {
			$static_description = $custom['pagemeta_static_description'][0];
		}
		if ( isset( $custom['pagemeta_static_link_text'][0] ) ) {
			$static_link_text = $custom['pagemeta_static_link_text'][0];
		}
		if ( isset( $custom['pagemeta_static_url'][0] ) ) {
			$static_url = $custom['pagemeta_static_url'][0];
		}
		if ( isset( $custom['pagemeta_fullscreen_infobox'][0] ) ) {
			$fullscreen_infobox = $custom['pagemeta_fullscreen_infobox'][0];
		}
		if ( isset( $custom['pagemeta_slideshow_titledesc'][0] ) ) {
			$slideshow_titledesc = $custom['pagemeta_slideshow_titledesc'][0];
		}
		if ( isset( $custom['pagemeta_cover_style'][0] ) ) {
			$cover_style = $custom['pagemeta_cover_style'][0];
		}

		$custom = get_post_custom( $featured_page );
		if ( isset( $custom['pagemeta_fullscreenslideshow_cover'][0] ) ) {
			$pagemeta_fullscreenslideshow_cover = $custom['pagemeta_fullscreenslideshow_cover'][0];
			switch ( $pagemeta_fullscreenslideshow_cover ) {
				case 'cover':
					$slideshow_fit_always = 0;
					$slideshow_portrait   = 0;
					$slideshow_landscape  = 0;
					break;
				case 'landscape':
					$slideshow_fit_always = 0;
					$slideshow_portrait   = 0;
					$slideshow_landscape  = 1;
					break;
				case 'portrait':
					$slideshow_fit_always = 0;
					$slideshow_portrait   = 1;
					$slideshow_landscape  = 0;
					break;
				case 'always':
					$slideshow_fit_always = 1;
					$slideshow_portrait   = 1;
					$slideshow_landscape  = 1;
					break;

				default:
					$slideshow_fit_always = 0;
					$slideshow_portrait   = 0;
					$slideshow_landscape  = 0;
					break;
			}
		} else {
			$slideshow_fit_always = 0;
			$slideshow_portrait   = 0;
			$slideshow_landscape  = 0;
		}

		$slideshow_no_description = '';
		if ( '' === $static_description ) {
			$slideshow_no_description = 'slideshow_text_shift_up';
		}
		$slideshow_no_description_no_title = '';
		if ( '' === $static_description && '' === $static_title ) {
			$slideshow_no_description_no_title = 'slideshow_text_shift_up';
		}

		$static_msg_display = false;

		if ( $static_link_text ) {
			$slideshow_link = '<div class="static_slideshow_content_link ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $static_url ) . '">' . esc_attr( $static_link_text ) . '</a></div>';
		}
		if ( $static_title ) {
			$slideshow_title_text = '<h1 class="static_slideshow_title ' . $slideshow_no_description . ' slideshow_title_animation">' . esc_attr( $static_title ) . '</h1>';
		}
		if ( $static_description ) {
			$slideshow_caption = '<div class="static_slideshow_caption">' . do_shortcode( $static_description ) . '</div>';
		}

		if ( '' !== $static_link_text || '' !== $static_title || '' !== $static_description || '' !== $static_url ) {
			$static_msg_display = true;
		}

		if ( isset( $portfolioslideshow ) ) {
			ob_start();
			?>
			jQuery(function($){	
				jQuery.supersized({
					slideshow               :   1,
					autoplay				:	<?php echo esc_js( $slideshow_autoplay ); ?>,
					start_slide             :   1,
					image_path				:	'<?php echo esc_js( $supersized_image_path ); ?>',
					stop_loop				:	<?php echo esc_js( $slideshow_pause_on_last ); ?>,
					random					: 	0,
					slide_interval          :   <?php echo esc_js( $slideshow_interval ); ?>,
					transition              :   <?php echo esc_js( $slideshow_transition ); ?>,
					transition_speed		:	<?php echo esc_js( $slideshow_transition_speed ); ?>,
					new_window				:	0,
					pause_hover             :   <?php echo esc_js( $slideshow_pause_hover ); ?>,
					keyboard_nav            :   1,
					performance				:	2,
					image_protect			:	0,			   
					min_width		        :   0,
					min_height		        :   0,
					vertical_center         :   <?php echo esc_js( $slideshow_vertical_center ); ?>,
					horizontal_center       :   <?php echo esc_js( $slideshow_horizontal_center ); ?>,
					fit_always				:	<?php echo esc_js( $slideshow_fit_always ); ?>,
					fit_portrait         	:   <?php echo esc_js( $slideshow_portrait ); ?>,
					fit_landscape			:   <?php echo esc_js( $slideshow_landscape ); ?>,
					slide_links				:	'blank',
					thumb_links				:	1,
					thumbnail_navigation    :   <?php echo esc_js( $slideshow_thumbnails_status ); ?>,
					slides 					:  	[
			<?php
			if ( $portfolioslideshow->have_posts() ) :
				while ( $portfolioslideshow->have_posts() ) :
					$portfolioslideshow->the_post();

					$featured_image_id = get_post_thumbnail_id( get_the_ID() );
					$image_uri         = atollmatrix_featured_image_link( get_the_ID() );
					$srcset            = wp_get_attachment_image_srcset( $featured_image_id, 'full' );
					$srcsetsizes       = wp_get_attachment_image_sizes( $featured_image_id, 'full' );

					if ( isset( $image_uri ) ) {

						$portfolio      = get_post_custom( get_the_ID() );
						$customlink_url = '';
						if ( isset( $portfolio['pagemeta_thumbnail_desc'][0] ) ) {
							$description = $portfolio['pagemeta_thumbnail_desc'][0];
						}
						if ( isset( $portfolio['pagemeta_customlink'][0] ) ) {
							$customlink_url = $portfolio['pagemeta_customlink'][0];
						}

						$the_image_title = get_the_title();
						$img_alt         = atollmatrix_get_alt_text( $featured_image_id );
						$the_image_desc  = $description;
						$thumb_image_uri = '';
						$link_text       = '';
						$link_url        = '';
						$slideshow_link  = '';
						$slideshow_color = '';

						$count++;
						if ( $count > 1 ) {
							echo ',';
						}
						$slideshow_title_text = '';
						$slideshow_caption    = '';
						$find                 = array( '\r\n', '\n', '\r' );
						$replace              = '';
						$the_image_desc       = str_replace( $find, $replace, $the_image_desc );
						$slide_color          = 'bright';
						$slideshow_color      = '<div class="fullscreen-slideshow-color" data-color="' . esc_attr( $slide_color ) . '"></div>';

						if ( ! $static_msg_display ) {
							// If static message is not filled in page meta fields
							$slideshow_no_description = '';
							if ( ! $the_image_desc ) {
								$slideshow_no_description = 'slideshow_text_shift_up';
							}
							$slideshow_no_description_no_title = '';
							if ( ! $the_image_desc && ! $the_image_title ) {
								$slideshow_no_description_no_title = 'slideshow_text_shift_up';
							}

							$link_url = get_permalink();

							if ( '' !== $customlink_url ) {
								$link_url = $customlink_url;
							}
							$link_text = atollmatrix_get_option_data( 'portfolio_fullscreen_viewtext' );

							if ( $link_text ) {
								$slideshow_link = '<div class="slideshow_content_links ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $link_url ) . '">' . esc_html( $link_text ) . '</a></div>';
							}
							if ( $the_image_title ) {
								$slideshow_title_text = '<h1 class="slideshow_title ' . $slideshow_no_description . ' slideshow_title_animation">' . $the_image_title . '</h1>';
							}
							if ( $the_image_desc ) {
								$slideshow_caption = '<div class="slideshow_caption">' . do_shortcode( $the_image_desc ) . '</div>';
							}
							$slideshow_display_code = '<div class="slideshow-content-wrap' . $page_background_class . '">' . $slideshow_title_text . $slideshow_caption . $slideshow_link . '</div>';

						} else {
							$slideshow_color        = '';
							$slideshow_link         = '';
							$slideshow_title_text   = '';
							$slideshow_caption      = '';
							$slideshow_display_code = '';
						}

						if ( 'disable' === $slideshow_titledesc || $static_msg_display ) {
							$slideshow_display_code = '';
						}
						$slideshow_image_data = "{image : '" . esc_url( $image_uri ) . "', srcset : '" . $srcset . "', srcsetsizes : '" . $srcsetsizes . "', alttext : '" . esc_js( $img_alt ) . "', title : '" . $slideshow_color . $slideshow_display_code . "', thumb : '" . esc_url( $thumb_image_uri ) . "', url : ''}";
						echo wp_kses( $slideshow_image_data, atollmatrix_get_allowed_tags() );
					}
				endwhile;
			endif;
			wp_reset_postdata();
			?>
				],
				progress_bar			:	1,
				mouse_scrub				:	1
			});
			if ($.fn.swipe) {
				jQuery('.page-is-fullscreen #supersized,.page-is-not-fullscreen #supersized').swipe({
						excludedElements: "button, input, select, textarea, .noSwipe",
						swipeLeft: function() {
						jQuery('#nextslide').trigger('click');
					},
						swipeRight: function() {
						jQuery('#prevslide').trigger('click');
					}
				});
			}
		});
			<?php
			$mtheme_slideshow_supersized_script = ob_get_contents();
			ob_end_clean();
			return $mtheme_slideshow_supersized_script;
		} else {
			return false;
		}
	}
}
function atollmatrix_generate_supersized_script( $preset_page_id = false, $isbackground = false ) {
	$featured_page = atollmatrix_get_active_fullscreen_post();

	if ( $isbackground ) {
		$bg_choice = atollmatrix_get_background_choice( get_the_id() );

		if ( 'none' !== $bg_choice ) {
			$preset_page_id = false;
			switch ( $bg_choice ) {
				case 'options_slideshow':
					$preset_page_id = atollmatrix_get_option_data( 'general_bgslideshow' );
					break;
				case 'image_attachments':
					$preset_page_id = get_the_ID();
					break;
			}
		}
	}

	if ( $preset_page_id ) {
		$featured_page = $preset_page_id;
	}
	// WPML Detector
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$_type         = get_post_type( $featured_page );
		$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
	}
	//The Image IDs
	$filter_image_ids        = atollmatrix_get_custom_attachments( $featured_page );
	$slideshow_autoplay      = atollmatrix_get_option_data( 'slideshow_autoplay' );
	$slideshow_pause_on_last = atollmatrix_get_option_data( 'slideshow_pause_on_last' );
	$slideshow_pause_hover   = atollmatrix_get_option_data( 'slideshow_pause_hover' );
	$slideshow_random        = atollmatrix_get_option_data( 'slideshow_random' );
	$slideshow_interval      = atollmatrix_get_option_data( 'slideshow_interval', '8000' );
	if ( $isbackground ) {
		$slideshow_transition = '1';
	} else {
		$custom = get_post_custom( $featured_page );
		if ( isset( $custom['pagemeta_fullscreenslideshow_transition'][0] ) ) {
			$slideshow_transition_getter = $custom['pagemeta_fullscreenslideshow_transition'][0];
			switch ( $slideshow_transition_getter ) {
				case 'wave':
					$slideshow_transition = '0';
					break;
				case 'fade':
					$slideshow_transition = '1';
					break;
				case 'zoom':
					$slideshow_transition = '1';
					break;
				default:
					$slideshow_transition = '0';
					break;
			}
		} else {
			$slideshow_transition = '1';
		}
	}
	$slideshow_transition_speed     = atollmatrix_get_option_data( 'slideshow_transition_speed', '1000' );
	$slideshow_portrait             = atollmatrix_get_option_data( 'slideshow_portrait' );
	$slideshow_landscape            = atollmatrix_get_option_data( 'slideshow_landscape' );
	$slideshow_fit_always           = atollmatrix_get_option_data( 'slideshow_fit_always' );
	$slideshow_vertical_center      = atollmatrix_get_option_data( 'slideshow_vertical_center' );
	$slideshow_horizontal_center    = atollmatrix_get_option_data( 'slideshow_horizontal_center' );
	$fullscreen_menu_toggle         = atollmatrix_get_option_data( 'fullscreen_menu_toggle' );
	$fullscreen_menu_toggle_nothome = atollmatrix_get_option_data( 'fullscreen_menu_toggle_nothome' );
	$rootpath                       = get_stylesheet_directory_uri();

	if ( ! $slideshow_autoplay ) {
		$slideshow_autoplay = 0;
	}
	if ( class_exists( '\Elementor\Plugin' ) ) {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$slideshow_autoplay = 0;
		}
	}
	if ( ! $slideshow_pause_on_last ) {
		$slideshow_pause_on_last = 0;
	}
	if ( ! $slideshow_pause_hover ) {
		$slideshow_pause_hover = 0;
	}
	if ( ! $slideshow_fit_always ) {
		$slideshow_fit_always = 0;
	}
	if ( ! $slideshow_portrait ) {
		$slideshow_portrait = 0;
	}
	if ( ! $slideshow_landscape ) {
		$slideshow_landscape = 0;
	}
	if ( ! $slideshow_vertical_center ) {
		$slideshow_vertical_center = 0;
	}
	if ( ! $slideshow_horizontal_center ) {
		$slideshow_horizontal_center = 0;
	}

	$supersized_image_path = get_template_directory_uri() . '/images/supersized/';
	$slideshow_thumbnails  = '';
	$featured_linked       = false;
	$the_attatchment_url   = '';
	$post_link             = '';
	$thelimit              = -1;
	$count                 = 0;

	if ( defined( 'ICL_LANGUAGE_CODE' ) ) { // this is to not break code in case WPML is turned off, etc.
		$_type         = get_post_type( $featured_page );
		$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
	}
	$page_background_class = '';
	// Don't Populate list if no Featured page is set
	if ( '' !== $featured_page ) {
		if ( $filter_image_ids ) {
			$slideshow_thumbnails_status = '0';
			if ( 'thumbnails' === $slideshow_thumbnails ) {
				$slideshow_thumbnails_status = '1';
			}

			$fullscreen_type      = '';
			$static_description   = '';
			$static_title         = '';
			$static_link_text     = '';
			$slideshow_link       = '';
			$slideshow_title_text = '';
			$slideshow_caption    = '';
			$coverphoto_textalign = '';
			$static_url           = '';
			$fullscreen_infobox   = '';
			$cover_style          = '';
			$slideshow_titledesc  = 'enable';
			$custom               = get_post_custom( $featured_page );

			if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
				$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
			}
			if ( isset( $custom['pagemeta_static_title'][0] ) ) {
				$static_title = $custom['pagemeta_static_title'][0];
			}
			if ( isset( $custom['pagemeta_static_description'][0] ) ) {
				$static_description = $custom['pagemeta_static_description'][0];
			}
			if ( isset( $custom['pagemeta_static_link_text'][0] ) ) {
				$static_link_text = $custom['pagemeta_static_link_text'][0];
			}
			if ( isset( $custom['pagemeta_static_url'][0] ) ) {
				$static_url = $custom['pagemeta_static_url'][0];
			}
			if ( isset( $custom['pagemeta_fullscreen_infobox'][0] ) ) {
				$fullscreen_infobox = $custom['pagemeta_fullscreen_infobox'][0];
			}
			if ( isset( $custom['pagemeta_slideshow_titledesc'][0] ) ) {
				$slideshow_titledesc = $custom['pagemeta_slideshow_titledesc'][0];
			}
			if ( isset( $custom['pagemeta_cover_style'][0] ) ) {
				$cover_style = $custom['pagemeta_cover_style'][0];
			}
			if ( isset( $custom['pagemeta_coverphoto_textalign'][0] ) ) {
				$coverphoto_textalign = $custom['pagemeta_coverphoto_textalign'][0];
			}

			$custom = get_post_custom( $featured_page );
			if ( isset( $custom['pagemeta_fullscreenslideshow_cover'][0] ) ) {
				$pagemeta_fullscreenslideshow_cover = $custom['pagemeta_fullscreenslideshow_cover'][0];
				switch ( $pagemeta_fullscreenslideshow_cover ) {
					case 'cover':
						$slideshow_fit_always = 0;
						$slideshow_portrait   = 0;
						$slideshow_landscape  = 0;
						break;
					case 'landscape':
						$slideshow_fit_always = 0;
						$slideshow_portrait   = 0;
						$slideshow_landscape  = 1;
						break;
					case 'portrait':
						$slideshow_fit_always = 0;
						$slideshow_portrait   = 1;
						$slideshow_landscape  = 0;
						break;
					case 'always':
						$slideshow_fit_always = 1;
						$slideshow_portrait   = 1;
						$slideshow_landscape  = 1;
						break;
					default:
						$slideshow_fit_always = 0;
						$slideshow_portrait   = 0;
						$slideshow_landscape  = 0;
						break;
				}
			} else {
				$slideshow_fit_always = 0;
				$slideshow_portrait   = 0;
				$slideshow_landscape  = 0;
			}

			$slideshow_no_description = '';
			if ( '' === $static_description ) {
				$slideshow_no_description = 'slideshow_text_shift_up';
			}
			$slideshow_no_description_no_title = '';
			if ( '' === $static_description && '' === $static_title ) {
				$slideshow_no_description_no_title = 'slideshow_text_shift_up';
			}

			$static_msg_display = false;

			if ( $static_link_text ) {
				$slideshow_link = '<div class="static_slideshow_content_link ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $static_url ) . '">' . esc_attr( $static_link_text ) . '</a></div>';
			}
			if ( $static_title ) {
				$slideshow_title_text = '<h1 class="static_slideshow_title ' . $slideshow_no_description . ' slideshow_title_animation">' . esc_attr( $static_title ) . '</h1>';
			}
			if ( $static_description ) {
				$slideshow_caption = '<div class="static_slideshow_caption">' . do_shortcode( $static_description ) . '</div>';
			}
			if ( '' !== $static_link_text || '' !== $static_title || '' !== $static_description || '' !== $static_url ) {
				$static_msg_display = true;
			}
			if ( isset( $filter_image_ids ) && is_array( $filter_image_ids ) ) {
				ob_start();
				?>
				jQuery(function($){	
					jQuery.supersized({
						slideshow               :   1,
						autoplay				:	<?php echo esc_js( $slideshow_autoplay ); ?>,
						start_slide             :   1,
						image_path				:	'<?php echo esc_js( $supersized_image_path ); ?>',
						stop_loop				:	<?php echo esc_js( $slideshow_pause_on_last ); ?>,
						random					: 	0,
						slide_interval          :   <?php echo esc_js( $slideshow_interval ); ?>,
						transition              :   <?php echo esc_js( $slideshow_transition ); ?>,
						transition_speed		:	<?php echo esc_js( $slideshow_transition_speed ); ?>,
						new_window				:	0,
						pause_hover             :   <?php echo esc_js( $slideshow_pause_hover ); ?>,
						keyboard_nav            :   1,
						performance				:	2,
						image_protect			:	0,			   
						min_width		        :   0,
						min_height		        :   0,
						vertical_center         :   <?php echo esc_js( $slideshow_vertical_center ); ?>,
						horizontal_center       :   <?php echo esc_js( $slideshow_horizontal_center ); ?>,
						fit_always				:	<?php echo esc_js( $slideshow_fit_always ); ?>,
						fit_portrait         	:   <?php echo esc_js( $slideshow_portrait ); ?>,
						fit_landscape			:   <?php echo esc_js( $slideshow_landscape ); ?>,
						slide_links				:	'blank',
						thumb_links				:	1,
						thumbnail_navigation    :   <?php echo esc_js( $slideshow_thumbnails_status ); ?>,
						slides 					:  	[
				<?php
				foreach ( $filter_image_ids as $attachment_id ) {
					$attachment = get_post( $attachment_id );
					if ( isset( $attachment ) ) {
						$alt       = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
						$caption   = $attachment->post_excerpt;
						$image_uri = wp_get_attachment_image_src( $attachment_id, 'full', false );
						$image_uri = $image_uri[0];
						if ( isset( $image_uri ) && '' !== $image_uri ) {
							$the_image_title = $attachment->post_title;
							$the_image_desc  = $attachment->post_content;
							$the_image_desc  = esc_js( $the_image_desc );
							$thumb_image_uri = '';
							$link_text       = '';
							$link_url        = '';
							$slideshow_link  = '';
							$slideshow_color = '';
							$srcset          = '';
							$srcsetsizes     = '';
							$link_text       = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
							$link_url        = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
							$slide_color     = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );
							$img_alt         = '';
							$img_alt         = atollmatrix_get_alt_text( $attachment->ID );

							// If linking is On
							if ( 1 === $featured_linked || true === $featured_linked ) {
								$the_attatchment_url = get_attachment_link( $image->ID );
							}

							$srcset      = wp_get_attachment_image_srcset( $attachment_id, 'full' );
							$srcsetsizes = wp_get_attachment_image_sizes( $attachment_id, 'full' );

							$count++;
							if ( $count > 1 ) {
								echo ',';
							}
							$slideshow_title_text = '';
							$slideshow_caption    = '';
							$find                 = array( '\r\n', '\n', '\r' );
							$replace              = '';
							$the_image_desc       = str_replace( $find, $replace, $the_image_desc );
							$the_image_desc       = sanitize_text_field( $the_image_desc );

							if ( ! $slide_color ) {
								$slide_color = 'bright';
							}
							$slideshow_color = '<div class="fullscreen-slideshow-color" data-color="' . esc_attr( $slide_color ) . '"></div>';

							if ( ! $static_msg_display ) {
								$slideshow_no_description = '';
								if ( ! $the_image_desc ) {
									$slideshow_no_description = 'slideshow_text_shift_up';
								}
								$slideshow_no_description_no_title = '';
								if ( ! $the_image_desc && ! $the_image_title ) {
									$slideshow_no_description_no_title = 'slideshow_text_shift_up';
								}
								if ( 'particles' !== $fullscreen_type && 'coverphoto' !== $fullscreen_type ) {
									if ( $link_text ) {
										$slideshow_link = '<div class="slideshow_content_links ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $link_url ) . '">' . esc_attr( $link_text ) . '</a></div>';
									}
									if ( $the_image_title ) {
										$slideshow_title_text = '<h1 class="slideshow_title ' . $slideshow_no_description . ' slideshow_title_animation">' . esc_js( $the_image_title ) . '</h1>';
									}
									if ( $the_image_desc ) {
										$slideshow_caption = '<div class="slideshow_caption">' . esc_js( $the_image_desc ) . '</div>';
									}
									$slideshow_display_code = '<div class="slideshow-content-wrap' . $page_background_class . '">' . $slideshow_title_text . $slideshow_caption . $slideshow_link . '</div>';
								} else {
									if ( ! isset( $slide_ui_color ) ) {
										$slide_ui_color = '';
									}
									if ( $link_text ) {
										$slideshow_link = '<div class="static_slideshow_content_link ' . $slideshow_no_description_no_title . '"><a class="positionaware-button" href="' . esc_url( $link_url ) . '">' . esc_attr( $link_text ) . '</a></div>';
									}
									if ( $the_image_title ) {
										$slideshow_title_text = '<div class="slideshow_title ' . $slideshow_no_description . '">' . esc_js( $the_image_title ) . '</div>';
									}
									if ( $the_image_desc ) {
										$slideshow_caption = '<div class="slideshow_caption">' . esc_js( $the_image_desc ) . '</div>';
									}
									$slideshow_caption = '<div class="coverphoto-outer-wrap fullscreen-slide-' . $slide_ui_color . '"><div id="coverphoto-text-wrap" class="cover-photo-' . $coverphoto_textalign . ' slideshow-content-wrap fullscreen-coverphoto-outer coverphoto-type-' . $cover_style . '"><div class="fullscreen-coverphoto-inner coverphoto-text-container">' . $slideshow_title_text . $slideshow_caption . $slideshow_link . '</div></div></div>';

									$slideshow_display_code = $slideshow_caption;
								}
							} else {
								$slideshow_color        = '';
								$slideshow_link         = '';
								$slideshow_title_text   = '';
								$slideshow_caption      = '';
								$slideshow_display_code = '';
							}

							if ( 'disable' === $slideshow_titledesc || $static_msg_display ) {
								$slideshow_display_code = '';
							}
							if ( $isbackground ) {
								$slideshow_display_code = '';
							}
							if ( atollmatrix_get_option_data( 'fullscreen_disableresponsiveset' ) ) {
								$srcset      = '';
								$srcsetsizes = '';
							}
							$slideshow_image_data = "{image : '" . esc_url( $image_uri ) . "', srcset : '" . $srcset . "', srcsetsizes : '" . $srcsetsizes . "', alttext : '" . esc_js( $img_alt ) . "', title : '" . $slideshow_color . $slideshow_display_code . "', thumb : '" . esc_url( $thumb_image_uri ) . "', url : ''}";
							echo wp_kses( $slideshow_image_data, atollmatrix_get_allowed_tags() );
						}
					}
				}
				?>
				],
				progress_bar			:	1,					
				mouse_scrub				:	1
			});
			if ($.fn.swipe) {
				jQuery('.page-is-fullscreen #supersized,.page-is-not-fullscreen #supersized').swipe({
					excludedElements: 'button, input, select, textarea, .noSwipe',
					swipeLeft: function() {
					jQuery('#nextslide').trigger('click');
				},
					swipeRight: function() {
					jQuery('#prevslide').trigger('click');
				}
			});
		}
		});
				<?php
				$mtheme_slideshow_supersized_script = ob_get_contents();
				ob_end_clean();
				if ( 0 !== $count ) {
					return $mtheme_slideshow_supersized_script;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
}
function atollmatrix_has_password( $id ) {
	$checking_for_password = get_post( $id );
	if ( ! empty( $checking_for_password->post_password ) ) {
		return true;
	}
	return false;
}
function atollmatrix_demo_get_data( $get ) {
	$demoswitch = false;
	if ( atollmatrix_is_in_demo() ) {
		if ( wp_verify_nonce( $_REQUEST['demowpnonce'], 'demowpnonce' ) ) {
			if ( isset( $_GET[ $get ] ) ) {
				$demoswitch = array();
				if ( isset( $_GET['woo_style'] ) ) {
					$woo_style = $_GET['woo_style'];
					if ( 'withsidebar' === $woo_style ) {
						$demoswitch['woo_style'] = '1';
					}
					if ( 'fullwidth' === $woo_style ) {
						$demoswitch['woo_style'] = '0';
					}
				}
				if ( isset( $_GET['menu_type'] ) ) {
					$menu_type = $_GET['menu_type'];
					if ( 'horizontal' === $menu_type ) {
						$demoswitch['menu_type'] = 'left-detatched';
					}
					if ( 'center' === $menu_type ) {
						$demoswitch['menu_type'] = 'center-logo';
					}
					if ( 'split-menu' === $menu_type ) {
						$demoswitch['menu_type'] = 'split-menu';
					}
				}
				if ( isset( $get ) && isset( $demoswitch[ $get ] ) ) {
					$demoswitch = $demoswitch[ $get ];
					return $demoswitch;
				} else {
					return false;
				}
			}
		}
	}
}
function atollmatrix_page_has_background() {
	$page_background = false;
	if ( is_singular() ) {
		$bg_choice = get_post_meta( get_the_id(), 'pagemeta_meta_background_choice', true );
	}
	if ( isset( $bg_choice ) ) {
		switch ( $bg_choice ) {
			case 'featured_image':
			case 'custom_url':
			case 'options_image':
			case 'options_slideshow':
			case 'image_attachments':
			case 'fullscreen_post':
			case 'video_background':
				$page_background = true;
				break;
			default:
				$page_background = false;
		}
	}
	return $page_background;
}
function atollmatrix_page_is_woo_shop() {
	$woo_shop_post_id = false;
	if ( class_exists( 'woocommerce' ) ) {
		if ( is_shop() ) {
			$woo_shop_post_id = get_option( 'woocommerce_shop_page_id' );
		}
	}
	return $woo_shop_post_id;
}
add_action( 'wp_ajax_nopriv_atollmatrix_proofing_checker', 'atollmatrix_proofing_checker' );
add_action( 'wp_ajax_atollmatrix_proofing_checker', 'atollmatrix_proofing_checker' );

function atollmatrix_proofing_checker() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
		$proofing_status = 'unchecked';

		if ( isset( $_POST['proofing_status'] ) ) {
			$proofing_status = $_POST['proofing_status'];
		}

		$image_id = $_POST['image_id'];
		if ( 'unchecked' === $proofing_status ) {
			if ( ! atollmatrix_disabled_for_demo() ) {
				update_post_meta( $image_id, 'checked', 'true' );
			}
			echo 'checked:' . esc_attr( $image_id );
		} else {
			if ( ! atollmatrix_disabled_for_demo() ) {
				update_post_meta( $image_id, 'checked', 'false' );
			}
			echo 'unchecked:' . esc_attr( $image_id );
		}
	}
	exit;
}
add_action( 'wp_ajax_nopriv_atollmatrix_editor_recommended_checker', 'atollmatrix_editor_recommended_checker' );
add_action( 'wp_ajax_atollmatrix_editor_recommended_checker', 'atollmatrix_editor_recommended_checker' );

function atollmatrix_editor_recommended_checker() {

	if ( wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {

		$mtheme_editor_choice = 'unchecked';

		if ( isset( $_POST['mtheme_editor_choice'] ) ) {
			$mtheme_editor_choice = $_POST['mtheme_editor_choice'];
		}

		$image_id = $_POST['image_id'];
		if ( 'editorunchecked' === $mtheme_editor_choice ) {
			if ( ! atollmatrix_disabled_for_demo() ) {
				update_post_meta( $image_id, 'editorchoice', 'true' );
			}
			echo 'checked:' . esc_attr( $image_id );
		} else {
			if ( ! atollmatrix_disabled_for_demo() ) {
				update_post_meta( $image_id, 'editorchoice', 'false' );
			}
			echo 'unchecked:' . esc_attr( $image_id );
		}
	}
	exit;
}
add_action( 'wp_ajax_nopriv_atollmatrix_post_like_vote', 'atollmatrix_post_like_vote' );
add_action( 'wp_ajax_atollmatrix_post_like_vote', 'atollmatrix_post_like_vote' );
function atollmatrix_post_like_vote() {
	if ( wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {

		if ( isset( $_POST['post_id'] ) ) {

			if ( function_exists( 'mtheme_get_the_ip_address' ) ) {
				$ip = mtheme_get_the_ip_address();
			}
			$post_id  = $_POST['post_id'];
			$meta_ip  = get_post_meta( $post_id, 'voted_ip' );
			$voted_ip = $meta_ip[0];

			if ( ! is_array( $voted_ip ) ) {
				$voted_ip = array();
			}

			$meta_count = get_post_meta( $post_id, 'votes_count', true );

			if ( ! atollmatrix_has_already_voted( $post_id ) ) {
				$voted_ip[ $ip ] = time();

				update_post_meta( $post_id, 'voted_ip', $voted_ip );
				update_post_meta( $post_id, 'votes_count', ++$meta_count );

				echo esc_html( $meta_count ) . ':' . esc_html( $post_id );
			} else {
				echo 'already voted';
			}
		}
	}
	exit;
}
function atollmatrix_has_already_voted( $post_id ) {

	$meta_ip = get_post_meta( $post_id, 'voted_ip' );
	if ( isset( $meta_ip[0] ) ) {
		$voted_ip = $meta_ip[0];
		if ( ! is_array( $voted_ip ) ) {
			$voted_ip = array();
		}
		if ( function_exists( 'mtheme_get_the_ip_address' ) ) {
			$ip = mtheme_get_the_ip_address();
		}

		if ( in_array( $ip, array_keys( $voted_ip ), true ) ) {

			$time            = $voted_ip[ $ip ];
			$now             = time();
			$ipcheck_minutes = 0;
			$ipcheck_hours   = 0;
			$ipcheck_days    = 7;
			$minutes_seconds = $ipcheck_minutes * 60;

			if ( 0 !== $ipcheck_hours ) {
				$hours_seconds = $ipcheck_hours * 3600;
			} else {
				$hours_seconds = 0;
			}
			if ( 0 !== $ipcheck_days ) {
				$days_seconds = $ipcheck_days * 86400;
			} else {
				$days_seconds = 0;
			}
			//Add time limit in seconds
			$ipcheck_timelimit = $minutes_seconds + $hours_seconds + $days_seconds;
			$elapsed_time      = round( ( $now - $time ) / $ipcheck_timelimit );
			//Time limit check
			if ( 1 < $elapsed_time ) {
				return false;
			}
			return true;
		}
	}
	return false;
}
function atollmatrix_display_like_link( $post_id ) {
	$vote_count = get_post_meta( $post_id, 'votes_count', true );
	if ( ! $vote_count ) {
		$vote_count = '0';
	}

	$votedclass = '';
	if ( atollmatrix_has_already_voted( $post_id ) ) {
		$votedclass = 'alreadyvoted';
	}

	$output  = '<div class="mtheme-post-like ' . esc_attr( $votedclass ) . '">';
	$output .= '<div class="post-link-count-wrap" data-count_id="' . esc_attr( $post_id ) . '"><span class="post-like-count">' . esc_attr( $vote_count ) . '</span></div>';
	if ( atollmatrix_has_already_voted( $post_id ) ) {
		$output .= '<span class="mtheme-like like-vote-icon voted animation-standby animated"><i class="vote-like-icon ion-ios-heart"></i></span>';
	} else {

		$vote_status_class = 'vote-ready';
		if ( is_admin() ) {
			$vote_status_class = 'vote-disabled';
		}

		$output .= '<span class="' . $vote_status_class . '" data-post_id="' . esc_attr( $post_id ) . '">
					<span class="mtheme-like like-vote-icon like-notvoted animation-standby animated"><i class="vote-like-icon ion-ios-heart-outline"></i></span>
					</span>';
	}
	$output .= '</div>';

	return $output;
}
// Demo Data Fetcher
function atollmatrix_demo_data_fetch( $get_this ) {
	$got_data = '';
	if ( isset( $get_this ) ) {
		$active_feature = $get_this;
		if ( 'middle' === $active_feature ) {
			$got_data = 'header-middle';
		}
		if ( 'minimal' === $active_feature ) {
			$got_data = 'minimal-header';
		}
		if ( 'topcenter' === $active_feature ) {
			$got_data = 'header-center';
		}
		if ( 'left' === $active_feature ) {
			$got_data = 'header-left';
		}
		if ( 'boxed-middle' === $active_feature ) {
			$got_data = 'boxed-header-middle';
		}
		if ( 'boxed-left' === $active_feature ) {
			$got_data = 'boxed-header-left';
		}
		if ( 'vertical' === $active_feature ) {
			$got_data = 'vertical-menu';
		}
		if ( 'shop_fullwidth' === $active_feature ) {
			$got_data = 'shop_fullwidth';
		}
		if ( 'themeskin' === $active_feature ) {
			$got_data = 'themeskin';
		}
	}
	return $got_data;
}
function atollmatrix_trim_sentence( $desc = '', $charlength = 20 ) {
	$excerpt  = $desc;
	$the_text = '';

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex   = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$the_text = mb_substr( $subex, 0, $excut );
		} else {
			$the_text = $subex;
		}
		$the_text .= '..';
	} else {
		$the_text = $excerpt;
	}
	return $the_text;
}
// Get single image slide color
function atollmatrix_get_first_slide_ui_color( $get_slideshow_from_page_id ) {
		// Store slide data for jQuery
	$filter_image_ids = atollmatrix_get_custom_attachments( $get_slideshow_from_page_id );
	if ( $filter_image_ids ) {
		$slide_counter    = 0;
		$last_slide_count = count( $filter_image_ids ) - 1;
		foreach ( $filter_image_ids as $attachment_id ) {
			$attachment  = get_post( $attachment_id );
			$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );
			if ( ! $slide_color ) {
				$slide_color = 'bright';
			}
			break;
		}
	}
	return $slide_color;
}
// Will be called from slideshow script
function atollmatrix_populate_slide_ui_colors( $get_slideshow_from_page_id ) {
		// Store slide data for jQuery
	$filter_image_ids = atollmatrix_get_custom_attachments( $get_slideshow_from_page_id );
	if ( $filter_image_ids ) {
		$slide_counter    = 0;
		$last_slide_count = count( $filter_image_ids ) - 1;
		echo '<ul id="slideshow-data" data-lastslide="' . esc_attr( $last_slide_count ) . '">';
		foreach ( $filter_image_ids as $attachment_id ) {
			$attachment = get_post( $attachment_id );
			if ( isset( $attachment->ID ) ) {
				$full_uri = wp_get_attachment_image_src( $attachment_id, 'full', false );
				$full_uri = $full_uri[0];

				$thumbnail_uri   = wp_get_attachment_image_src( $attachment_id, 'thumbnail', false );
				$thumbnail_uri   = $thumbnail_uri[0];
				$thumbnail_title = '';
				if ( isset( $attachment->post_title ) ) {
					$thumbnail_title = $attachment->post_title;
				}

				$header_color = get_post_meta( $attachment->ID, 'mtheme_fullscreen_header_color', true );
				$slide_color  = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

				if ( ! $slide_color ) {
					$slide_color = 'bright';
				}
				if ( ! $header_color ) {
					$header_color = 'bright';
				}
				echo '<li class="slide-' . esc_attr( $slide_counter ) . '" data-header="' . esc_attr( $header_color ) . '" data-slide="' . esc_attr( $slide_counter ) . '" data-color="' . esc_attr( $slide_color ) . '" data-src="' . esc_attr( $full_uri ) . '" data-thumbnail="' . esc_attr( $thumbnail_uri ) . '" data-title="' . esc_attr( $thumbnail_title ) . '"></li>';
				$slide_counter++;
			}
		}
		echo '</ul>';
	}
}
function atollmatrix_populate_portfolio_ui_colors( $get_slideshow_from_page_id ) {

	$carousel         = '';
	$count            = 0;
	$limit            = -1;
	$slide_counter    = 0;
	$last_slide_count = 0;

	$custom = get_post_custom( $get_slideshow_from_page_id );

	$worktype_slugs = '';
	if ( isset( $custom['pagemeta_photowall_workstypes'][0] ) ) {
		$worktype_slugs = $custom['pagemeta_photowall_workstypes'][0];
	}

	if ( '' !== $worktype_slugs ) {
		$type_explode = explode( ',', $worktype_slugs );
		foreach ( $type_explode as $work_slug ) {
			$terms[] = $work_slug;
		}
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
			'tax_query'      => array(
				array(
					'taxonomy' => 'types',
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				),
			),
		);
	} else {
		$args = array(
			'post_type'      => 'portfolio',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => $limit,
		);
	}

	$portfolioslideshow = new WP_Query( $args );

	if ( $portfolioslideshow->have_posts() ) :
		while ( $portfolioslideshow->have_posts() ) :
			$portfolioslideshow->the_post();
			if ( has_post_thumbnail() ) {
				$last_slide_count++;
			}
		endwhile;
	endif;
	wp_reset_postdata();
	if ( $last_slide_count > 0 ) {
		$last_slide_count--;
	}

	$portfolioslideshow = new WP_Query( $args );

	echo '<ul id="slideshow-data" data-lastslide="' . esc_attr( $last_slide_count ) . '">';

	if ( $portfolioslideshow->have_posts() ) :
		while ( $portfolioslideshow->have_posts() ) :
			$portfolioslideshow->the_post();
			if ( has_post_thumbnail() ) {
				$featured_image_id = get_post_thumbnail_id( get_the_ID() );
				$attachment        = get_post( $featured_image_id );
				$full_uri          = wp_get_attachment_image_src( $featured_image_id, 'full', false );
				$full_uri          = $full_uri[0];
				$thumbnail_uri     = wp_get_attachment_image_src( $featured_image_id, 'thumbnail', false );
				$thumbnail_uri     = $thumbnail_uri[0];
				$thumbnail_title   = '';
				if ( isset( $attachment->post_title ) ) {
					$thumbnail_title = $attachment->post_title;
				}

				$header_color = get_post_meta( $featured_image_id, 'mtheme_fullscreen_header_color', true );
				$slide_color  = get_post_meta( $featured_image_id, 'mtheme_attachment_fullscreen_color', true );

				if ( ! $slide_color ) {
					$slide_color = 'bright';
				}
				if ( ! $header_color ) {
					$header_color = 'bright';
				}
				echo '<li class="slide-' . esc_attr( $slide_counter ) . '" data-header="' . esc_attr( $header_color ) . '" data-slide="' . esc_attr( $slide_counter ) . '" data-color="' . esc_attr( $slide_color ) . '" data-src="' . esc_attr( $full_uri ) . '" data-thumbnail="' . esc_attr( $thumbnail_uri ) . '" data-title="' . esc_attr( $thumbnail_title ) . '"></li>';
				$slide_counter++;
			}

		endwhile;
	endif;
	wp_reset_postdata();
	echo '</ul>';
}
function atollmatrix_get_slideshow_count( $get_slideshow_from_page_id ) {
	$count            = false;
	$filter_image_ids = atollmatrix_get_custom_attachments( $get_slideshow_from_page_id );
	if ( $filter_image_ids ) {
		$slide_counter = 0;
		$count         = count( $filter_image_ids );
	}
	return $count;
}
function atollmatrix_get_custom_post_nav( $custom_type = 'portfolio' ) {
	$postlist_args = array(
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_type'      => $custom_type,
	);
	$postlist      = get_posts( $postlist_args );
	$ids           = array();
	foreach ( $postlist as $thepost ) {
		$ids[] = $thepost->ID;
	}

	// get and echo previous and next post in the same taxonomy
	$thisindex = array_search( get_the_id(), $ids, true );

	$custom_post_navigation = array();
	if ( isset( $ids[ $thisindex - 1 ] ) ) {
		$custom_post_navigation['prev'] = $ids[ $thisindex - 1 ];
	}
	if ( isset( $ids[ $thisindex + 1 ] ) ) {
		$custom_post_navigation['next'] = $ids[ $thisindex + 1 ];
	}
	return $custom_post_navigation;
}
if ( ! function_exists( 'atollmatrix_get_option_data' ) ) {
	function atollmatrix_get_option_data( $field_id, $default_value = '' ) {
		if ( $field_id ) {
			if ( ! $default_value ) {
				if ( class_exists( 'Kirki' ) && isset( Kirki::$fields[ $field_id ] ) && isset( Kirki::$fields[ $field_id ]['default'] ) ) {
					$default_value = Kirki::$fields[ $field_id ]['default'];
				}
				if ( atollmatrix_get_customizer_default( $field_id ) ) {
					$default_value = atollmatrix_get_customizer_default( $field_id );
				}
			}
			$value = get_theme_mod( $field_id, $default_value );
			return $value;
		}
		return false;
	}
}
function atollmatrix_excerpt_max_charlength( $charlength ) {
	$excerpt  = get_the_excerpt();
	$the_text = '';

	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex   = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$the_text = mb_substr( $subex, 0, $excut );
		} else {
			$the_text = $subex;
		}
		$the_text .= '[...]';
	} else {
		$the_text = $excerpt;
	}
	return $the_text;
}
//Get page header status
function atollmatrix_get_page_header_status() {
	$page_header_status = 'Show';
	$page_header_status = get_post_meta( get_the_id(), 'pagemeta_pagetitle_header', true );
	return $page_header_status;
}
//Revolution Slider selector populate
function atollmatrix_rev_slider_selectors() {
	$mtheme_revslides                         = array();
	$mtheme_revslides['mtheme-none-selected'] = 'Not Selected';
	if ( function_exists( 'rev_slider_shortcode' ) ) {

		$query_sliders = array();
		if ( class_exists( 'RevSlider' ) ) {
			$rev_slider = new RevSlider();
			if ( method_exists( $rev_slider, 'getAllSliderAliases' ) ) {
				$query_sliders = $rev_slider->getAllSliderAliases();
			}
		}
		if ( isset( $query_sliders ) ) {
			foreach ( $query_sliders as $sliders ) {
				$mtheme_revslides[ $sliders ] = $sliders;
			}
		}
	}
	return $mtheme_revslides;
}
// Check fullscreen type
function atollmatrix_is_fullscreen_home() {
	$fullscreen_check = atollmatrix_get_option_data( 'fullcscreen_henable' );
	if ( is_home() && true === $fullscreen_check ) {
		return true;
	} else {
		return false;
	}
}
function atollmatrix_get_fullscreen_type_from_id( $the_page_id ) {
	$fullscreen_type = false;
	$custom          = get_post_custom( $the_page_id );
	if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
		$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
	}
	return $fullscreen_type;
}
function atollmatrix_get_fullscreen_type() {
	$fullscreen_type  = false;
	$fullscreen_check = atollmatrix_get_option_data( 'fullcscreen_henable' );
	if ( is_home() && true === $fullscreen_check ) {
		$custom = get_post_custom( atollmatrix_get_option_data( 'fullcscreen_hselected' ) );
	} else {
		$custom = get_post_custom( get_the_id() );
	}
	if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
		$fullscreen_type = $custom['pagemeta_fullscreen_type'][0];
	}
	return $fullscreen_type;
}
// Check if it's a fullscreen post
function atollmatrix_is_fullscreen_post() {
	$fullscreen_post_check = false;
	if ( is_singular( 'fullscreen' ) ) {
		$fullscreen_post_check = true;
	}
	if ( is_singular( 'mtheme_photostory' ) ) {
		$fullscreen_post_check = true;
	}
	$fullscreen_check = atollmatrix_get_option_data( 'fullcscreen_henable' );
	if ( is_home() && true === $fullscreen_check ) {
		$fullscreen_post_check = true;
	}
	return $fullscreen_post_check;
}
// Get active fullscreen post
function atollmatrix_get_active_fullscreen_post() {
	$fullscreen_check = atollmatrix_get_option_data( 'fullcscreen_henable' );
	if ( is_home() && true === $fullscreen_check ) {
		$fullscreen_page_id = atollmatrix_get_option_data( 'fullcscreen_hselected' );
	} else {
		$fullscreen_page_id = get_the_id();
	}
	return $fullscreen_page_id;
}
/*
Check fullscreen type and return the correct page.
*/
if ( ! function_exists( 'atollmatrix_get_fullscreen_file' ) ) {
	function atollmatrix_get_fullscreen_file( $fullscreen_type ) {
		$fullscreen_load = false;
		switch ( $fullscreen_type ) {

			case 'kenburns':
				$fullscreen_load = 'kenburns';
				break;
			case 'splitslider':
				$fullscreen_load = 'splitslider';
				break;
			case 'particles':
				$fullscreen_load = 'particles';
				break;
			case 'coverphoto':
				$fullscreen_load = 'coverphoto';
				break;
			case 'fotorama':
				$fullscreen_load = 'fotorama';
				break;
			case 'carousel':
				$fullscreen_load = 'carousel';
				break;
			case 'slideshow':
				$fullscreen_load = 'supersized';
				break;
			case 'portfolioslideshow':
				$fullscreen_load = 'portfolioslideshow';
				break;
			case 'portfoliocarousel':
				$fullscreen_load = 'portfoliocarousel';
				break;
			case 'video':
				$fullscreen_load = 'video';
				break;
			case 'revslider':
				$fullscreen_load = 'revslider';
				break;
			case 'swiperslides':
				$fullscreen_load = 'swiperslides';
				break;
			default:
				break;
		}
		return $fullscreen_load;
	}
}
// Get Attached images applied with custom script
function atollmatrix_get_custom_attachments( $page_id ) {
	$filter_image_ids = false;
	$the_image_ids    = get_post_meta( $page_id, '_mtheme_image_ids' );
	if ( $the_image_ids ) {
		$filter_image_ids = explode( ',', $the_image_ids[0] );
		return $filter_image_ids;
	}
}
// Get Attached images applied with custom script
function atollmatrix_get_pagemeta_infobox_set( $page_id ) {
	$filter_image_ids = false;
	$the_image_ids    = get_post_meta( $page_id, 'pagemeta_infoboxes' );
	if ( $the_image_ids ) {
		$filter_image_ids = explode( ',', $the_image_ids[0] );
		return $filter_image_ids;
	}
}
function atollmatrix_extract_googlefont_data( $got_font ) {
	if ( $got_font ) {
		$font_pieces   = explode( ':', $got_font );
		$font_css_name = $font_pieces[0];
		$font_name     = str_replace( ' ', '+', $font_pieces[0] );

		if ( isset( $font_pieces[1] ) ) {
			$font_variants = $font_pieces[1];
			$font_variants = str_replace( '|', ',', $font_pieces[1] );
		} else {
			$font_variants = '';
		}
		if ( isset( $font_pieces[2] ) ) {
			$font_subsets = $font_pieces[2];
			$font_subsets = str_replace( '|', ',', $font_pieces[2] );
			$font_subsets = '&subset=' . $font_subsets;
		} else {
			$font_subsets = '';
		}
		if ( is_ssl() ) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		$font_url                = $protocol . '://fonts.googleapis.com/css?family=' . $font_name . ':' . $font_variants . $font_subsets;
		$google_font['variants'] = $font_variants;
		$google_font['cssname']  = $font_css_name;
		$google_font['name']     = $font_name;
		$google_font['url']      = $font_url;
		return $google_font;
	}
}
// Enqueque Font
function atollmatrix_enqueue_font( $section_name ) {
	$got_font = atollmatrix_get_option_data( $section_name );
	if ( $got_font ) {
		$font_pieces = explode( ':', $got_font );
		$font_name   = $font_pieces[0];
		$font_name   = str_replace( ' ', '+', $font_pieces[0] );

		if ( isset( $font_pieces[1] ) ) {
			$font_variants = $font_pieces[1];
			$font_variants = str_replace( '|', ',', $font_pieces[1] );
		} else {
			$font_variants = '';
		}
		if ( isset( $font_pieces[2] ) ) {
			$font_subsets = $font_pieces[2];
			$font_subsets = str_replace( '|', ',', $font_pieces[2] );
			$font_subsets = '&subset=' . $font_subsets;
		} else {
			$font_subsets = '';
		}

		if ( is_ssl() ) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}

		$font_url            = $protocol . '://fonts.googleapis.com/css?family=' . $font_name . ':' . $font_variants . $font_subsets;
		$google_font['name'] = $font_name;
		$google_font['url']  = $font_url;
		return $google_font;
	}

}
//Apply Font used by Dynamic_CSS
function atollmatrix_apply_font( $font_name, $font_classes ) {
	$got_font = atollmatrix_get_option_data( $font_name );
	if ( $got_font ) {
		$font_pieces = explode( ':', $got_font );
		$font_name   = $font_pieces[0];
		$dynamic_css = $font_classes . "{ font-family:'" . $font_name . "'; }";
		return $dynamic_css;
	}
}
//Change Class called from Dynamic_CSS
function atollmatrix_change_class( $class, $property, $value, $important ) {
	if ( '' !== $important ) {
		$important = ' !' . $important;
	}
	$output_value = '{' . $property . ':' . $value . $important . ';}';
	$dynamic_css  = $class . $output_value;
	return $dynamic_css;
}
// Displays alt text based on ID
function atollmatrix_get_alt_text( $attached_image_id ) {
	$alt = get_post_meta( $attached_image_id, '_wp_attachment_image_alt', true );
	return $alt;
}
// Excerpt Limit
function atollmatrix_excerpt_limit( $limit ) {
	$excerpt = explode( ' ', get_the_excerpt(), $limit );
	if ( count( $excerpt ) >= $limit ) {
		array_pop( $excerpt );
		$excerpt = implode( ' ', $excerpt ) . '...';
	} else {
		$excerpt = implode( ' ', $excerpt );
	}
	$excerpt = preg_replace( '`\[[^\]]*\]`', '', $excerpt );
	return $excerpt;
}
function atollmatrix_content_limit( $limit ) {
	$content = explode( ' ', get_the_content(), $limit );
	if ( count( $content ) >= $limit ) {
		array_pop( $content );
		$content = implode( ' ', $content ) . '...';
	} else {
		$content = implode( ' ', $content );
	}
	$content = preg_replace( '/\[.+\]/', '', $content );
	$content = apply_filters( 'the_content', $content );
	$content = str_replace( ']]>', ']]&gt;', $content );
	return $content;
}
// Detect User Agent
// Detect special conditions devices
function atollmatrix_get_device() {
	if ( wp_is_mobile() ) {
		$device_is = 'mobile';
	} else {
		$device_is = 'desktop';
	}
	return $device_is;
}
/*
Tag Cloud Font size modifier
*/
function atollmatrix_tag_cloud_filter( $args = array() ) {
	$args['smallest'] = 13;
	$args['largest']  = 24;
	$args['unit']     = 'px';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'atollmatrix_tag_cloud_filter', 90 );
/*-------------------------------------------------------------------------*/
/* Check for shortcode */
/*-------------------------------------------------------------------------*/
function atollmatrix_got_shortcode( $shortcode = '' ) {
	global $post;
	if ( isset( $post->ID ) ) {
		$post_to_check = get_post( get_the_ID() );
	}
	$found = false;

	if ( ! $shortcode ) {
		return $found;
	}
	if ( isset( $post_to_check ) ) {
		if ( false !== stripos( $post_to_check->post_content, '[' . $shortcode ) ) {
			$found = true;
		}
	}
	return $found;
}
function atollmatrix_get_select_target_options( $type ) {
	$list_options = array();

	switch ( $type ) {
		case 'post':
			$the_list = get_posts( 'orderby=title&numberposts=-1&order=ASC' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->ID ] = $list->post_title;
			}
			break;
		case 'page':
			$the_list = get_pages( 'title_li=&orderby=name' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->ID ] = $list->post_title;
			}
			break;
		case 'category':
			$the_list = get_categories( 'orderby=name&hide_empty=0' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->term_id ] = $list->name;
			}
			break;
		case 'backgroundslideshow_choices':
			$list_options = array(
				'options_slideshow' => esc_html__( 'Customizer Set Slideshow Images', 'atollmatrix' ),
				'image_attachments' => esc_html__( 'Slideshow using Image Attachments', 'atollmatrix' ),
				'none'              => esc_html__( 'none', 'atollmatrix' ),
			);
			break;
		case 'portfolio_category':
			$the_list = get_categories( 'taxonomy=types&title_li=' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->slug ] = $list->name;
			}
			array_unshift( $list_options, 'All the items' );
			break;
		case 'client_names':
			$featured_pages       = get_posts( 'post_type=clients&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$list_options[ $list->ID ] = $list->post_title;
				}
			} else {
				$list_options[0] = 'Clients not found.';
			}
			break;
		case 'fullscreen_slideshow_posts':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=fullscreen&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$custom = get_post_custom( $list->ID );
					if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
						$slideshow_type = $custom['pagemeta_fullscreen_type'][0];
					} else {
						$slideshow_type = '';
					}
					if ( 'video' !== $slideshow_type && '' !== $slideshow_type && 'photowall' !== $slideshow_type && 'revslider' !== $slideshow_type ) {
						$list_options[ $list->ID ] = $list->post_title;
					}
				}
			} else {
				$list_options[0] = 'Featured pages not found.';
			}
			break;
		case 'fullscreen_video_bg':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=fullscreen&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$custom = get_post_custom( $list->ID );
					if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
						$slideshow_type = $custom['pagemeta_fullscreen_type'][0];
					} else {
						$slideshow_type = '';
					}
					if ( 'video' === $slideshow_type ) {
						if ( isset( $custom['pagemeta_html5_mp4'][0] ) || isset( $custom['pagemeta_youtubevideo'][0] ) ) {
							$list_options[ $list->ID ] = $list->post_title;
						}
					}
				}
			} else {
				$list_options[0] = 'Featured pages not found.';
			}
			break;
		case 'fullscreen_posts':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=fullscreen&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$custom = get_post_custom( $list->ID );
					if ( isset( $custom['pagemeta_fullscreen_type'][0] ) ) {
						$slideshow_type = $custom['pagemeta_fullscreen_type'][0];
					} else {
						$slideshow_type = '';
					}
					$list_options[ $list->ID ] = $list->post_title;
				}
			} else {
				$list_options[0] = 'Featured pages not found.';
			}
			break;
	}

	return $list_options;
}
/*-------------------------------------------------------------------------*/
/* Shorten text to closest complete word from provided text */
/*-------------------------------------------------------------------------*/
function atollmatrix_shortentext( $textblock, $textlen ) {
	if ( $textblock ) {
		$output = substr( substr( $textblock, 0, $textlen ), 0, strrpos( substr( $textblock, 0, $textlen ), ' ' ) );
		return $output;
	}
}
/*-------------------------------------------------------------------------*/
/* Show featured image link */
/*-------------------------------------------------------------------------*/
function atollmatrix_featured_image_link( $the_image_id ) {
	if ( ! isset( $the_image_id ) ) {
		$the_image_id = get_the_id();
	}
	$image_id  = get_post_thumbnail_id( $the_image_id, 'full' );
	$image_url = wp_get_attachment_image_src( $image_id, 'full' );
	if ( isset ( $image_url[0] ) ) {
		$image_url = $image_url[0];
	}
	return $image_url;
}
/*-------------------------------------------------------------------------*/
/* Show featured image title */
/*-------------------------------------------------------------------------*/
function atollmatrix_featured_image_title( $the_image_id ) {
	$img_title = '';
	$image_id  = get_post_thumbnail_id( $the_image_id );
	$img_obj   = get_post( $image_id );
	if ( isset( $img_obj ) ) {
		$img_title = $img_obj->post_title;
	}
	return $img_title;
}
/*-------------------------------------------------------------------------*/
/* Show attached image real link */
/*-------------------------------------------------------------------------*/
function atollmatrix_featured_image_real_link( $the_image_id ) {
	$image_id  = get_post_thumbnail_id( $the_image_id, 'full' );
	$image_url = wp_get_attachment_image_src( $image_id, 'full' );
	$image_url = $image_url[0];
	return $image_url;
}
/*-------------------------------------------------------------------------*/
/* Resize images and cross check if WP MU using blog ID */
/*-------------------------------------------------------------------------*/
function atollmatrix_showimage( $image, $link_url, $resize, $height, $width, $quality, $crop, $have_image_title, $class ) {
	$image_url = $image;
	$output    = '';
	if ( '' !== $link_url ) {
		$output = '<a href="' . esc_url( $link_url ) . '">';
	}
	if ( true === $resize ) {
		if ( $image ) {
			if ( $class ) {
				$output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $have_image_title ) . '" class="' . $class . '"/>';
			} else {
				$output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $have_image_title ) . '" />';
			}
		}
	}
	if ( false === $resize ) {
		if ( $image_url ) {
			if ( $class ) {
				$output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $have_image_title ) . '" class="' . $class . '"/>';
			} else {
				$output .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $have_image_title ) . '" />';
			}
		}
	}
	if ( '' !== $link_url ) {
		$output .= '</a>';
	}
	return $output;
}
function atollmatrix_display_post_image( $the_image_id, $have_image_url, $link, $type, $the_image_title, $class, $lazyload = false ) {

	if ( '' === $type ) {
		$type = 'fullsize';
	}
	$output = '';

	$image_id  = get_post_thumbnail_id( ( $the_image_id ), $type );
	$image_url = wp_get_attachment_image_src( $image_id, $type );
	if ( isset( $image_url[0] ) ) {
		$image_url = $image_url[0];
	}
	$img_obj   = get_post( $image_id );
	$img_alt   = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$permalink = get_permalink( $the_image_id );

	if ( true === $link ) {
		$output = '<a href="' . esc_url( $permalink ) . '">';
	}

	$fallback_image = atollmatrix_get_placeholder_image( $type );

	$data_src     = '';
	$fallback_src = '';
	if ( 'true' === $lazyload ) {
		$data_src     = 'data-';
		$fallback_src = 'src="' . esc_url( $fallback_image ) . '" ';
		$class        = $class . ' lazyload';
	}
	if ( $have_image_url ) {
		$have_image_id = atollmatrix_get_image_id_from_url( $have_image_url );
		$img_alt       = atollmatrix_get_alt_text( $have_image_id );
		$output       .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $have_image_url ) . '" alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
	} else {
		if ( isset( $image_url ) && '' !== $image_url ) {
			if ( $class ) {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
			} else {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $img_alt ) . '" />';
			}
		}
	}

	if ( true === $link ) {
		$output .= '</a>';
	}

	echo wp_kses( $output, atollmatrix_get_allowed_tags() );
}
function atollmatrix_display_post_image_srcset( $the_image_id, $have_image_url, $link, $type, $the_image_title, $class, $lazyload = false ) {

	if ( '' === $type ) {
		$type = 'fullsize';
	}
	$output = '';

	$image_id    = get_post_thumbnail_id( ( $the_image_id ), $type );
	$image_url   = wp_get_attachment_image_src( $image_id, $type );
	$image_url   = $image_url[0];
	$img_obj     = get_post( $image_id );
	$img_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$permalink   = get_permalink( $the_image_id );
	$srcset      = wp_get_attachment_image_srcset( $image_id, $type );
	$srcsetsizes = wp_get_attachment_image_sizes( $image_id, $type );

	if ( true === $link ) {
		$output = '<a href="' . esc_url( $permalink ) . '">';
	}

	$fallback_image = atollmatrix_get_placeholder_image( $type );

	$data_src     = '';
	$fallback_src = '';
	$data_srcset  = '';
	$srcsizes     = 'sizes="' . $srcsetsizes . '"';

	if ( 'true' === $lazyload ) {
		$data_src     = 'data-';
		$data_srcset  = 'data-srcset="' . $srcset . '" ';
		$fallback_src = 'src="' . esc_url( $fallback_image ) . '" ';
		$class        = $class . ' lazyload';
	} else {
		$data_srcset = 'srcset="' . $srcset . '" ';
	}
	if ( $have_image_url ) {
		$have_image_id = atollmatrix_get_image_id_from_url( $have_image_url );
		$img_alt       = atollmatrix_get_alt_text( $have_image_id );
		$output       .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $have_image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
	} else {
		if ( isset( $image_url ) && '' !== $image_url ) {
			if ( $class ) {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
			} else {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" />';
			}
		}
	}

	if ( true === $link ) {
		$output .= '</a>';
	}

	echo wp_kses( $output, atollmatrix_get_allowed_tags() );
}
/*-------------------------------------------------------------------------*/
/* Get Page ID by Slug */
/*-------------------------------------------------------------------------*/
function atollmatrix_get_page_id( $page_slug ) {
	$page_id = get_page_by_path( $page_slug );
	if ( $page_id ) :
		return $page_id->ID;
	else :
		return null;
	endif;
}
/*-------------------------------------------------------------------------*/
/* Get Page ID by Title */
/*-------------------------------------------------------------------------*/
function atollmatrix_get_page_title_by_id( $page_id ) {
	$page = get_post( $page_id );
	if ( $page ) :
		return $page->post_title;
	else :
		return null;
	endif;
}
/*-------------------------------------------------------------------------*/
/* Get Page Link by Title */
/*-------------------------------------------------------------------------*/
function atollmatrix_get_page_link_by_title( $page_title ) {
	$page = get_page_by_title( $page_title );
	if ( $page ) :
		return get_permalink( $page->ID );
	else :
		return '#';
	endif;
}
/*-------------------------------------------------------------------------*/
/* Get Page link by Slug */
/*-------------------------------------------------------------------------*/
function atollmatrix_get_page_link_by_slug( $page_slug ) {
	$page = get_page_by_path( $page_slug );
	if ( $page ) :
		return get_permalink( $page->ID );
	else :
		return '#';
	endif;
}
/*-------------------------------------------------------------------------*/
/* Get Page link by ID */
/*-------------------------------------------------------------------------*/
function atollmatrix_get_page_link_by_id( $page_id ) {
	$page = get_post( $page_id );
	if ( $page ) :
		return get_permalink( $page->ID );
	else :
		return '#';
	endif;
}
function atollmatrix_round_num( $num, $to_nearest ) {
	return floor( $num / $to_nearest ) * $to_nearest;
}
// Custom Pagination codes
function atollmatrix_pagination( $pages = '', $range = 4 ) {
	$pagination = '';
	$showitems  = ( $range * 2 ) + 1;

	global $paged;

	if ( get_query_var( 'paged' ) ) {
		$got_the_page = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$got_the_page = get_query_var( 'page' );
	} else {
		$got_the_page = 1;
	}
	if ( empty( $got_the_page ) ) {
		$got_the_page = 1;
	}

	if ( '' === $pages ) {
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if ( ! $pages ) {
			$pages = 1;
		}
	}

	if ( 1 !== $pages ) {
		$pagination .= '<div class="pagination-navigation">';
		$pagination .= '<div class="pagination">';
		if ( 2 < $got_the_page && $got_the_page > $range + 1 && $showitems < $pages ) {
			$pagination .= '<a class="pagination-first" href="' . esc_url( get_pagenum_link( 1 ) ) . '">&laquo;</a>';
		}
		if ( 1 < $got_the_page && $showitems < $pages ) {
			$pagination .= '<a class="pagination-previous" href="' . esc_url( get_pagenum_link( $got_the_page - 1 ) ) . '">&lsaquo;</a>';
		}

		for ( $i = 1; $i <= $pages; $i++ ) {
			if ( 1 !== $pages && ( ! ( $i >= $got_the_page + $range + 1 || $i <= $got_the_page - $range - 1 ) || $pages <= $showitems ) ) {
				$pagination .= ( $got_the_page === $i ) ? '<span class="current">' . $i . '</span>' : '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="inactive">' . $i . '</a>';
			}
		}

		if ( $got_the_page < $pages && $showitems < $pages ) {
			$pagination .= '<a href="' . esc_url( get_pagenum_link( $got_the_page + 1 ) ) . '">&rsaquo;</a>';
		}
		if ( $got_the_page < $pages - 1 && $got_the_page + $range - 1 < $pages && $showitems < $pages ) {
			$pagination .= '<a href="' . esc_url( get_pagenum_link( $pages ) ) . '">&raquo;</a>';
		}
		$pagination .= '</div>';
		$pagination .= '</div>';
	}
	echo wp_kses( $pagination, atollmatrix_get_allowed_tags() );
}
function atollmatrix_hex_to_rgb( $hex_str, $return_as_string = false, $seperator = ',' ) {
	$hex_str   = preg_replace( '/[^0-9A-Fa-f]/', '', $hex_str );
	$rgb_array = array();
	if ( 6 === strlen( $hex_str ) ) {
		$color_val          = hexdec( $hex_str );
		$rgb_array['red']   = 0xFF & ( $color_val >> 0x10 );
		$rgb_array['green'] = 0xFF & ( $color_val >> 0x8 );
		$rgb_array['blue']  = 0xFF & $color_val;
	} elseif ( 3 === strlen( $hex_str ) ) {
		$rgb_array['red']   = hexdec( str_repeat( substr( $hex_str, 0, 1 ), 2 ) );
		$rgb_array['green'] = hexdec( str_repeat( substr( $hex_str, 1, 1 ), 2 ) );
		$rgb_array['blue']  = hexdec( str_repeat( substr( $hex_str, 2, 1 ), 2 ) );
	} else {
		return false;
	}
	return $return_as_string ? implode( $seperator, $rgb_array ) : $rgb_array;
}
?>
