<?php
function get_fullscreen_infobox_posts( $post_type, $categories, $limit ) {

	$args = false;
	switch ($post_type) {
		case 'portfolio':
			$args = array(
				'post_type' => 'portfolio',
                'worktypes' => $categories,
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit
			);
			break;
		case 'events':
			$args = array(
				'post_type' => 'events',
				'eventsection' => $categories,
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit
			);
			break;
		case 'stories':
			$args = array(
				'post_type' => 'photostory',
				'photostorytypes' => $categories,
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit
			);
			break;
		case 'proofing':
			$args = array(
				'post_type' => 'proofing',
				'proofingsection' => $categories,
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => $limit
			);
			break;
		case 'woocommerce':
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => $limit,
				'product_cat' => $categories
			);
			break;
		default:
			# code...
			break;
	}

	return $args;
}
function infobox_for_fullscreen($atts, $content = null) {
	extract(shortcode_atts(array(
		'post_type' => 'portfolio',
		'limit' => '-1',
		'categories' => '',
		'autoplay' => 'false',
		'transition' => 'fade',
		'titlebox' => ''
	), $atts));

	$infobox_excerpt_limit  = imaginem_codepack_get_option_data( 'infobox_excerpt_limit' );
	$infobox_excerpt_suffix = imaginem_codepack_get_option_data( 'infobox_excerpt_suffix' );

	$got_query = get_fullscreen_infobox_posts( $post_type, $categories, $limit );

	$query = new WP_Query( $got_query );

	$portfolioImage_type = "superlens-gridblock-large";

	if ( 'true' !== $autoplay ) {
		$autoplay = 'false';
	}

	
	$infobox_autoplay       = imaginem_codepack_get_option_data( 'infobox_autoplay' );
	$infobox_autoplay_speed = imaginem_codepack_get_option_data( 'infobox_autoplay_speed' );

	switch ($post_type) {
		case 'portfolio':
			$box_heading = imaginem_codepack_get_option_data( 'infobox_portfolio_title' );
			break;
		case 'events':
			$box_heading = imaginem_codepack_get_option_data( 'infobox_events_title' );
			break;
		case 'stories':
			$box_heading = imaginem_codepack_get_option_data( 'infobox_stories_title' );
			break;
		case 'proofing':
			$box_heading = imaginem_codepack_get_option_data( 'infobox_proofing_title' );
			break;
		case 'woocommerce':
			$box_heading = imaginem_codepack_get_option_data( 'infobox_woocommerce_title' );
			break;
		
		default:
			$box_heading = imaginem_codepack_get_option_data( 'infobox_portfolio_title' );
			break;
	}

	$infobox_loop = 'true';

	if ( ! $infobox_autoplay ) {
		$infobox_autoplay = 'false';
	} else {
		$infobox_autoplay = 'true';
	}

	if ( '1' === $limit ) {
		$infobox_autoplay = 'false';
		$infobox_loop = 'false';
	}

	$output  = '<div class="gridblock-owlcarousel-wrap mtheme-infobox-type-' . esc_attr( $post_type ). ' mtheme-infobox-carousel pageload-after-done clearfix">';
	$output .= '<div class="mtheme-infobox-heading">' . esc_html( $box_heading ) . '<span class="mtheme-infobox-toggle"><i class="feather-icon-plus"></i></span></div>';
	$output .= '<div id="fullscreen-infobox" class="owl-carousel owl-slideshow-element" data-autoplay="'.esc_attr( $infobox_autoplay ).'" data-autoplayspeed="'.esc_attr( $infobox_autoplay_speed ).'" data-loop="'.esc_attr( $infobox_loop ).'">';

	// The Loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$custom = get_post_custom( get_the_ID() );
			$description    = '';
			$customlink_url = '';
			$thumbnail      = '';

			if ( isset( $custom['pagemeta_thumbnail_desc'][0] ) ) {
				$description = $custom['pagemeta_thumbnail_desc'][0];
			}
			if ( isset( $custom['pagemeta_customlink'][0] ) ) {
				$customlink_url = $custom['pagemeta_customlink'][0];
			}
			if ( isset( $custom['pagemeta_customthumbnail'][0] ) ) {
				$thumbnail = $custom['pagemeta_customthumbnail'][0];
			}

			$output .= '<div class="slideshow-box-wrapper">';
			$output .= '<div class="slideshow-box-image">';

			if ( has_post_thumbnail() ) {
				if ( '' !== $customlink_url ) {
					$output .= '<a href="'.esc_url( $customlink_url ).'">';
				} else {
					$output .= '<a href="'.get_permalink().'">';
				}
				if ($thumbnail<>"") {
					$output .= '<img src="'.$thumbnail.'" class="displayed-image" alt="thumbnail" />';
				} else {
					$output .= imaginem_codepack_display_post_image (
						get_the_ID(),
						$have_image_url="",
						$link=false,
						$theimage_type=$portfolioImage_type,
						$imagetitle='',
						$class="displayed-image"
					);
				}
				$output .= '</a>';
			} else {
					if ($customlink_url<>"") {
						$output .= '<a href="'.esc_url($customlink_url).'">';
					} else {
						$output .= '<a href="'.get_permalink().'">';
					}
					$output .= '<div class="gridblock-protected">';
					$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
					$protected_placeholder = '/images/blank-grid.png';
					$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
					$output .= '</div>';
				$output .= '</a>';						
			}	
			$output .= '</div>';
			$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
			$output .= '<h2 class="slideshow-box-title">';
			if ($customlink_url<>"") {
				$output .= '<a href="'.esc_url($customlink_url).'">'.get_the_title() . '</a>';
			} else {
				$output .= '<a href="'.esc_url( get_permalink() ).'">'.get_the_title() . '</a>';
			}
			$output .= '</h2>';

			if ( class_exists( 'woocommerce' ) ) {
				if ( 'woocommerce' === $post_type ) {
					ob_start();
					wc_get_template( 'loop/price.php' );
					$woo_price = ob_get_contents();
					ob_end_clean();

					$output .= '<div class="slideshow-box-price">'.$woo_price.'</div>';

					$description= imaginem_codepack_trim_sentence( get_the_excerpt() , 120, false );
				}
			}
			if ( 'proofing' === $post_type ) {

				if (isset($custom['pagemeta_client_names'][0])) {
					$client_id = $custom['pagemeta_client_names'][0];
					$client_data = get_post_custom($client_id);
					if (isset($client_data['pagemeta_client_name'][0])) {
						$client_name = $client_data['pagemeta_client_name'][0];
					}
				}
			}

			
			if ( '' !== $infobox_excerpt_limit ) {
				$description = imaginem_codepack_description_limitter( $description , $infobox_excerpt_limit , $infobox_excerpt_suffix );
			}

			$output .= '<div class="slideshow-box-description">';
				$output .= $description;
			$output .='</div>';

			$output .= '</div></div>';
			$output .='</div>';
		}
	}
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_infobox_for_fullscreen", "infobox_for_fullscreen");
// PageMeta
add_shortcode("display_pagemeta_infobox", "mtheme_PageMeta_Infobox");
function mtheme_PageMeta_Infobox($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '3',
		"autoplay" => 'false',
		"transition" => 'fade'
	), $atts));

	//$limit= imaginem_codepack_get_option_data('information_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="3";
	}

	$portfolioImage_type="superlens-gridblock-events";

	if (defined('ICL_LANGUAGE_CODE')) { // this is to not break code in case WPML is turned off, etc.
	    $_type  = get_post_type($curr_pageid);
	    $curr_pageid = icl_object_id($curr_pageid, $_type, true, ICL_LANGUAGE_CODE);
	}
	$filter_image_ids = imaginem_codepack_get_pagemeta_infobox_set ( get_the_id() );
	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="fullscreen-informationbox-outer clearfix">';
	$output .= '<div class="gridblock-owlcarousel-wrap fullscreen-informationbox-inner mtheme-events-carousel clearfix">';
	$output .= '<div id="owl-fullscreen-pagemeta" class="owl-carousel owl-slideshow-element">';
	
			foreach ( $filter_image_ids as $attachment_id) {
				$attachment = get_post( $attachment_id );
				if ( isSet($attachment_id) && $attachment_id<>"" ) {
					$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
					$caption = $attachment->post_excerpt;
					//$href = get_permalink( $attachment->ID ),
					$imageURI = wp_get_attachment_image_src( $attachment_id, 'superlens-gridblock-square-big', false );
					$imageURI = $imageURI[0];
					$imageTitle = $attachment->post_title;
					$imageDesc = $attachment->post_content;

					$thumb_imageURI = '';

					$link_text = ''; $link_url = ''; $slideshow_link = ''; $slideshow_color='';
					$link_text = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_link', true );
					$link_url = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_url', true );
					$slide_color = get_post_meta( $attachment->ID, 'mtheme_attachment_fullscreen_color', true );

			
					$output .= '<div class="slideshow-box-wrapper clearfix" data-card="'.$attachment_id.'">';
						$output .= '<div class="slideshow-box-image">';

						
							$output .= '<a href="'.esc_url( $link_url ).'">';

							if ( isSet($imageURI[0]) ) {
								$output .= imaginem_codepack_display_post_image (
									get_the_ID(),
									$have_image_url=$imageURI,
									$link=false,
									$theimage_type=$portfolioImage_type,
									$imagetitle='',
									$class="displayed-image"
								);
							} else {
								$output .= '<div class="gridblock-protected">';
									$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
									$protected_placeholder = '/images/blank-grid.png';
									$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
								$output .= '</div>';

							}
							$output .= '</a>';	

						$output .= '</div>';
						$output .= '<div class="slideshow-box-content">';
							$output .= '<div class="slideshow-box-content-inner">';
							
								$output .= '<a href="'.esc_url( $link_url ).'">';
									$output .= '<h2>'.$imageTitle.'</h2>';

									$output .= '<div class="slideshow-box-description">';
										$output .= $imageDesc;
									$output .='</div>';
									$output .= "<div class='slideshow-box-readmore hover-color-transtition'>Read More</div>";
								$output .='</a>';

							$output .= '</div>';
						$output .= '</div>';
					$output .='</div>';
				}

			}
	$output .='</div>';
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;
}
// Portfolio
function mtheme_Worktype_Infobox_Slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"worktype_slugs" => '',
		"autoplay" => 'false',
		"transition" => 'fade'
	), $atts));

	$limit= imaginem_codepack_get_option_data('worktype_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="";
	}
	
	//echo $type, $portfolio_type;
	if ($worktype_slugs!='') $all_works = explode(",", $worktype_slugs);
	$categories=  get_categories('orderby=slug&taxonomy=worktypes&number='.$limit.'&title_li=');

	$portfolioImage_type="superlens-gridblock-events";


	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-events-carousel clearfix">';
	$output .= '<div class="mtheme-events-heading">'. imaginem_codepack_get_option_data('worktype_box_title') . '</div>';
	$output .= '<div id="owl-fullscreen-infobox" class="owl-carousel owl-slideshow-element">';
	
			foreach ($categories as $category){
				$taxonomy = 'worktypes';

				$term_slug = $category->slug;
				$term = get_term_by('slug', $term_slug, $taxonomy);

				if ( !isSet($all_works) || in_array($term_slug, $all_works) ) {				

					$hreflink = get_term_link($category->slug,'worktypes');
					$mtheme_worktype_image_id = get_option('mtheme_worktype_image_id' . $category->term_id);
					$work_type_image = wp_get_attachment_image_src( $mtheme_worktype_image_id, $portfolioImage_type , false );

			
					$output .= '<div class="slideshow-box-wrapper">';
					$output .= '<div class="slideshow-box-image">';

					
						$output .= '<a href="'.esc_url( $hreflink ).'">';

						if ( isSet($work_type_image[0]) ) {
							$output .= imaginem_codepack_display_post_image (
								get_the_ID(),
								$have_image_url=$work_type_image[0],
								$link=false,
								$theimage_type=$portfolioImage_type,
								$imagetitle='',
								$class="displayed-image"
							);
						} else {
							$output .= '<div class="gridblock-protected">';
							$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
							$protected_placeholder = '/images/blank-grid.png';
							$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
							$output .= '</div>';

						}
						$output .= '</a>';	

					$output .= '</div>';
					$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
					$output .= '<h2 class="slideshow-box-title">';
					
					$output .= '<a href="'.esc_url( $hreflink ).'">'. $category->name . '</a>';
					$output .= '</h2>';

					$output .= '<div class="slideshow-box-description">';
						$output .= $category->description;
					$output .='</div>';

					$output .= '</div></div>';
					$output .='</div>';
				}

			}
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_worktype_infobox_slideshow", "mtheme_Worktype_Infobox_Slideshow");
// Portfolio
function portfolio_Infobox_Slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"worktype_slugs" => '',
		"autoplay" => 'false',
		"transition" => 'fade'
	), $atts));

	$limit= imaginem_codepack_get_option_data('portfolio_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="-1";
	}
	
	//echo $type, $portfolio_type;

	query_posts(array(
		'post_type' => 'portfolio',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'posts_per_page' => $limit
		));	

	$portfolioImage_type="superlens-gridblock-large";


	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$portfolio_box_heading = imaginem_codepack_get_option_data('portfolio_box_title');
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-infobox-carousel pageload-after-done clearfix">';
	$output .= '<div class="mtheme-infobox-heading">Portfolios</div>';
	$output .= '<div id="fullscreen-infobox" class="owl-carousel owl-slideshow-element">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			$custom = get_post_custom(get_the_ID());
			$description="";
			$customlink_URL='';
			$thumbnail='';
			if ( isset($custom['pagemeta_thumbnail_desc'][0]) ) { $description=$custom['pagemeta_thumbnail_desc'][0]; }
			if ( isset($custom['pagemeta_customlink'][0]) ) { $customlink_URL=$custom['pagemeta_customlink'][0]; }
			if ( isset($custom['pagemeta_customthumbnail'][0]) ) { $thumbnail=$custom['pagemeta_customthumbnail'][0]; }
			
			
				$output .= '<div class="slideshow-box-wrapper">';
				$output .= '<div class="slideshow-box-image">';

				
				if ( has_post_thumbnail() ) {
					if ($customlink_URL<>"") {
						$output .= '<a href="'.esc_url($customlink_URL).'">';
					} else {
						$output .= '<a href="'.get_permalink().'">';
					}
					if ($thumbnail<>"") {
						$output .= '<img src="'.$thumbnail.'" class="displayed-image" alt="thumbnail" />';
					} else {
						$output .= imaginem_codepack_display_post_image (
							get_the_ID(),
							$have_image_url="",
							$link=false,
							$theimage_type=$portfolioImage_type,
							$imagetitle='',
							$class="displayed-image"
							);
					}
					$output .= '</a>';
				} else {
						if ($customlink_URL<>"") {
							$output .= '<a href="'.esc_url($customlink_URL).'">';
						} else {
							$output .= '<a href="'.get_permalink().'">';
						}
						$output .= '<div class="gridblock-protected">';
						$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
						$protected_placeholder = '/images/blank-grid.png';
						$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
						$output .= '</div>';
					$output .= '</a>';						
				}	
				$output .= '</div>';
				$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
				$output .= '<h2 class="slideshow-box-title">';
				if ($customlink_URL<>"") {
					$output .= '<a href="'.esc_url($customlink_URL).'">'.get_the_title() . '</a>';
				} else {
					$output .= '<a href="'.esc_url( get_permalink() ).'">'.get_the_title() . '</a>';
				}
				$output .= '</h2>';

				$output .= '<div class="slideshow-box-description">';
					$output .= $description;
				$output .='</div>';

				$output .= '</div></div>';
				$output .='</div>';

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_portfolio_infobox_slideshow", "portfolio_Infobox_Slideshow");
// Events
function events_Infobox_Slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"worktype_slugs" => '',
		"autoplay" => 'false',
		"transition" => 'fade',
		"autoheight" => 'true'
	), $atts));

	$limit= imaginem_codepack_get_option_data('events_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="-1";
	}
	
	//echo $type, $portfolio_type;

	query_posts(array(
		'post_type' => 'events',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'posts_per_page' => $limit,
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'	 	=> 'pagemeta_event_notice',
				'value'	  	=> 'inactive',
				'compare' 	=> 'NOT IN',
			),
		),
		));	

	$portfolioImage_type="superlens-gridblock-events";


	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-events-carousel clearfix">';
	$output .= '<div class="mtheme-events-heading">'. imaginem_codepack_get_option_data('event_box_title') . '</div>';
	$output .= '<div id="owl-fullscreen-infobox" class="owl-carousel owl-slideshow-element">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			$custom = get_post_custom(get_the_ID());
			$description="";
			if ( isset($custom['pagemeta_thumbnail_desc'][0]) ) { $description=$custom['pagemeta_thumbnail_desc'][0]; }
			
			
				$output .= '<div class="slideshow-box-wrapper">';
				$output .= '<div class="slideshow-box-image">';

				
				if ( has_post_thumbnail() ) {
					$output .= '<a href="'.get_permalink().'">';
					$output .= imaginem_codepack_display_post_image (
						get_the_ID(),
						$have_image_url="",
						$link=false,
						$theimage_type=$portfolioImage_type,
						$imagetitle='',
						$class="displayed-image"
					);
					$output .= '</a>';
				} else {
					$output .= '<a href="'.get_permalink().'">';
						$output .= '<div class="gridblock-protected">';
						$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
						$protected_placeholder = '/images/blank-grid.png';
						$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
						$output .= '</div>';
					$output .= '</a>';						
				}
				$output .= '</div>';
				$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
				$output .= '<h2 class="slideshow-box-title"><a href="'.esc_url( get_permalink() ).'">'.get_the_title() . '</a></h2>';

				$output .= '<div class="slideshow-box-description">';
					$output .= $description;
				$output .='</div>';

				$output .= '</div></div>';
				$output .='</div>';

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_events_infobox_slideshow", "events_Infobox_Slideshow");
//Blog Carousel
function mtheme_Blog_Infobox_Slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"category_name" => '',
		"autoplay" => 'false',
		"transition" => 'fade',
		"cat_slug"=> ''
	), $atts));

	$limit= imaginem_codepack_get_option_data('blog_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="-1";
	}
	
	//echo $type, $portfolio_type;

	query_posts(array(
		'category_name' => $cat_slug,
		'posts_per_page' => $limit
		));	

	$portfolioImage_type="superlens-gridblock-events";


	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-events-carousel clearfix">';
	$output .= '<div class="mtheme-events-heading">'. imaginem_codepack_get_option_data('blog_box_title') . '</div>';
	$output .= '<div id="owl-fullscreen-infobox" class="owl-carousel owl-slideshow-element">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			$postformat = get_post_format();
			if($postformat == "") {
				$postformat="standard";
			}
			$custom = get_post_custom(get_the_ID());
			$description= imaginem_codepack_trim_sentence( get_the_excerpt() , 120 );
			if ( $postformat == "quote") {
				$description = get_post_meta(get_the_id(), 'pagemeta_meta_quote', true);
			}
			
				if ( has_post_thumbnail() ) {
					$output .= '<div class="slideshow-box-wrapper">';
					$output .= '<div class="slideshow-box-image">';

					
					if ( has_post_thumbnail() ) {
						$output .= '<a href="'.get_permalink().'">';
						$output .= imaginem_codepack_display_post_image (
							get_the_ID(),
							$have_image_url="",
							$link=false,
							$theimage_type=$portfolioImage_type,
							$imagetitle='',
							$class="displayed-image"
						);
						$output .= '</a>';
					} else {
						$output .= '<a href="'.get_permalink().'">';
							$output .= '<div class="gridblock-protected">';
							$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
							$protected_placeholder = '/images/blank-grid.png';
							$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
							$output .= '</div>';
						$output .= '</a>';						
					}	
					$output .= '</div>';
					$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
					$output .= '<h2 class="slideshow-box-title"><a href="'.esc_url( get_permalink() ).'">'.get_the_title() . '</a></h2>';

					$output .= '<div class="slideshow-box-description">';
						$output .= $description;
					$output .='</div>';

					$output .= '</div></div>';
					$output .='</div>';
				}

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_blog_infobox_slideshow", "mtheme_Blog_Infobox_Slideshow");
//WooCommerce Slideshows
function mtheme_WooCommerce_Infobox_Slideshow($atts, $content = null) {
	extract(shortcode_atts(array(
		"limit" => '-1',
		"category_name" => '',
		"autoplay" => 'false',
		"transition" => 'fade',
		"cat_slug"=> ''
	), $atts));

	$limit= imaginem_codepack_get_option_data('woocommerce_box_limit');

	if ( $limit=='' || !isSet($limit) || $limit=='0' ) {
		$limit="-1";
	}
	
	//echo $type, $portfolio_type;

	query_posts(array(
		'post_type' => 'product',
		'posts_per_page' => $limit
		));	

	$portfolioImage_type="superlens-gridblock-events";


	$uniqureID=get_the_id()."-".uniqid();

	if ($autoplay <> "true") {
		$autoplay="false";
	}
	$output = '<div class="gridblock-owlcarousel-wrap mtheme-events-carousel clearfix">';
	$output .= '<div class="mtheme-events-heading">'. imaginem_codepack_get_option_data('woocommerce_box_title') . '</div>';
	$output .= '<div id="owl-fullscreen-infobox" class="owl-carousel owl-slideshow-element">';
	
			if (have_posts()) : while (have_posts()) : the_post();

			$custom = get_post_custom(get_the_ID());
			$description= imaginem_codepack_trim_sentence( get_the_excerpt() , 60 );
			
				if ( has_post_thumbnail() ) {
					$output .= '<div class="slideshow-box-wrapper">';
					$output .= '<div class="slideshow-box-image">';

					
					if ( has_post_thumbnail() ) {
						$output .= '<a href="'.get_permalink().'">';
						$output .= imaginem_codepack_display_post_image (
							get_the_ID(),
							$have_image_url="",
							$link=false,
							$theimage_type=$portfolioImage_type,
							$imagetitle='',
							$class="displayed-image"
						);
						$output .= '</a>';
					} else {
						$output .= '<a href="'.get_permalink().'">';
							$output .= '<div class="gridblock-protected">';
							$output .= '<span class="hover-icon-effect"><i class="feather-icon-target"></i></span>';
							$protected_placeholder = '/images/blank-grid.png';
							$output .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
							$output .= '</div>';
						$output .= '</a>';						
					}
					$output .= '</div>';
					$output .= '<div class="slideshow-box-content"><div class="slideshow-box-content-inner">';
					$output .= '<h2 class="slideshow-box-title"><a href="'.esc_url( get_permalink() ).'">'.get_the_title() . '</a></h2>';

					ob_start();
					woocommerce_get_template('loop/price.php');
					$woo_price = ob_get_contents();
					ob_end_clean();

					$output .= '<div class="slideshow-box-price">'.$woo_price.'</div>';

					$output .= '<div class="slideshow-box-description">';
						$output .= $description;
					$output .='</div>';

					$output .= '</div></div>';
					$output .='</div>';
				}

			endwhile; endif;
	$output .='</div>';
	$output .='</div>';
	
	wp_reset_query();
	return $output;


}
add_shortcode("display_woocommerce_infobox_slideshow", "mtheme_WooCommerce_Infobox_Slideshow");
?>