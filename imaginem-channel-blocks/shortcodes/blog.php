<?php
///////// Recent Blog Lists ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mGridBlog' ) ) {
	function mGridBlog($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"columns" => '4',
			"animated" => 'true',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"cat_id" => '',
			"post_type" => '',
			"excerpt_length" => '15',
			"readmore_text" => '',
			"pagination" => 'false'
		), $atts));

	if ($columns==4) { 
		$column_type="four";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==3) { 
		$column_type="three";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==2) { 
		$column_type="two";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==1) { 
		$column_type="one";
		$portfolioImage_type="superlens-gridblock-full";
		}

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;
	$portfoliogrid='';
	$portfoliogrid .= '<div class="gridblock-columns-wrap clearfix">';
	$portfoliogrid .= '<div id="gridblock-container" class="portfolio-gutter-spaced gridblock-'.$column_type.'">';

	$limit = get_option( 'posts_per_page' );

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	query_posts(array(
		'cat' => $cat_id,
		'paged' => $paged,
		'posts_per_page' => $limit
		));

	if (have_posts()) : while (have_posts()) : the_post();
		//echo $type, $portfolio_type;

	$postformat = get_post_format();
	if($postformat == "") $postformat="standard";

	$portfolio_thumb_header="Image";

	if ($portfolio_count==$columns) $portfolio_count=0;

	$protected="";
	$icon_class="column-gridblock-icon";
	$portfolio_count++;

	//if ($portfolio_count==1) $portfoliogrid .= '<li class="clearfix"></li>';
    $animation_class = '';
    if ($animated == "true") {
        $animation_class = ' animation-standby-portfolio animated fadeInUpSlow';
    }
	$portfoliogrid .= '<div class="blog-grid-element'.$animation_class.' gridblock-element gridblock-col-'.$portfolio_count.'">';

		$portfoliogrid .= '<div class="blog-grid-element-inner">';
		
		$linkcenter ='';
		$linkcenter="gridblock-link-center";

		$postformat_icon = imaginem_codepack_get_postformat_icon($postformat);

		//if Password Required
		if ( post_password_required() ) {
			$protected=" portfolio-protected"; $iconclass="";
			$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
			$portfoliogrid .= '<span class="grid-icon-status"><i class="fa fa-lock fa-2x"></i></span>';
			$portfoliogrid .= '<div class="portfolio-protected"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
		} else {

			if ( ! has_post_thumbnail() ) {
				$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
				$portfoliogrid .= '<span class="grid-icon-status"><i class="'.$postformat_icon.' fa-2x"></i></span>';
				$portfoliogrid .= '<div class="gridblock-blank-element"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
			}

			if ( has_post_thumbnail() ) {
			//Make sure it's not a slideshow
				//Switch check for Linked Type
				$portfoliogrid .= '<a class="gridblock-image-link blog-grid-element-has-image gridblock-columns" href="'.get_permalink() .'" rel="bookmark">';
				$portfoliogrid .= '<span class="grid-icon-status"><i class="'.$postformat_icon.' fa-2x"></i></span>';
			// Display Hover icon trigger classes

			// If it aint slideshow then display a background. Otherwise one is active in slideshow thumbnails.
			// Custom Thumbnail
			//Display Image
				$portfoliogrid .= imaginem_codepack_display_post_image (
					get_the_ID(),
					$have_image_url="",
					$link=false,
					$type=$portfolioImage_type,
					$imagetitle='',
					$class="preload-image displayed-image",
					$lazyload = true
				);
			} else {
				
			}
		}
		$portfoliogrid .= '</a>';
		$portfoliogrid .= '<div class="summary-info">';
			if ($date=='true') {
				$portfoliogrid .='<div class="summary-date"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
			}
			$category = get_the_category();
			if ($comments == 'true' ) {
				$portfoliogrid .= '<div class="summary-comment">';

				$num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
				if ( comments_open() ) {
					if ( $num_comments == 0 ) {
						$comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
					} elseif ( $num_comments > 1 ) {
						$comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
					} else {
						$comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
					}
					$portfoliogrid .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
				}
				$portfoliogrid .='</div>';
			}
		$portfoliogrid .='</div>';

		$portfoliogrid .= '<div class="blog-grid-element-content">';
			// If either of title and description needs to be displayed.
			if ($title=="true" || $description=="true") {
				$portfoliogrid .='<div class="work-details">';
					$hreflink = get_permalink();
					if ($title=="true") { $portfoliogrid .='<h4><a href="'.$hreflink.'" rel="bookmark" title="'. get_the_title() .'">'. get_the_title() .'</a></h4>'; }
					$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);
					if ($postformat=='quote') $summary_content = get_post_meta( get_the_id() , 'pagemeta_meta_quote', true);
					if ($description=="true") { $portfoliogrid .= '<div class="entry-content work-description">'. $summary_content .'</div>'; }
				$portfoliogrid .='</div>';
			}
			if ($readmore_text!='') {
				$portfoliogrid .= '<div class="button-blog-continue"><a href="'.$hreflink.'">'.$readmore_text.'</a></div>';
			}

		$portfoliogrid .= '</div>';
	$portfoliogrid .= '</div>';
	$portfoliogrid .='</div>';


	endwhile; endif;
	$portfoliogrid .='</div>';
	$portfoliogrid .='</div>';

		if ($pagination=='true') $portfoliogrid .= imaginem_codepack_pagination();
		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("gridblog", "mGridBlog");
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'imaginem_shortcodes_blogparallax' ) ) {
	function imaginem_shortcodes_blogparallax($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"title" => 'true',
			"desc" => 'true',
			"limit" => '-1',
			'height_type' => 'fixed',
			"height" => '0',
			"excerpt_length" => '25',
			"readmore_text" => 'Continue Reading',
			"cat_slugs" => '',
			"post_type" => ''
		), $atts));

	$column_type="listbox";
	$portfolioImage_type="superlens-gridblock-large";

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;

	$height_class = '';
	if ($height=='' || $height == '0') {
		$height = '';
		$height_class = ' fullheight-parallax';
	} else {
		$height = 'height:'.$height . 'px;';
	}
	$output='';
	$output .= '<div class="gridblock-blog-parallax gridblock-parallax-wrap clearfix">';
	$output .= '<ul>';
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	$readmore_text = imaginem_codepack_get_option_data( 'read_more', 'Continue Reading' );

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slugs,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'meta_query' => array(
			    array(
			     'key' => '_thumbnail_id',
			     'compare' => 'EXISTS'
			    ),
			),
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slugs,
			'paged' => $paged,
			'posts_per_page' => $limit,
		    'meta_query' => array(
		        array(
		         'key' => '_thumbnail_id',
		         'compare' => 'EXISTS'
		        ),
		    )
			));	
	}

	if (have_posts()) : while (have_posts()) : the_post();

	$image_url = imaginem_codepack_featured_image_link(get_the_id());

	$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);

	$postformat = get_post_format();
	if($postformat == "") {
		$postformat="standard";
	}
	if ($postformat == "quote") {
		$quote=get_post_meta(get_the_id(), 'pagemeta_meta_quote', true);
		$quote_author=get_post_meta(get_the_id(), 'pagemeta_meta_quote_author', true);
		$summary_content = '';
		$summary_content .= '<span class="quote_say"><i class="quote-say-left fa fa-quote-left"></i>'.$quote.'</span>';
		if ($quote_author != "") {
			$summary_content .= '<span class="quote_author">&#8212;&nbsp;' . $quote_author.'</span>';
		}
	}

	$current_terms = wp_get_object_terms( get_the_ID(), 'category'  );
	$current_worktype = '';
	$seperator = ',';
	foreach( $current_terms as $the_term ) {
		if ($the_term === end($current_terms)) {
			$seperator = '';
		}
		$current_worktype .= $the_term->name . $seperator;
	}

	if (!isSet($image_url) || $image_url=="") {
		$image_url = '';
	}

	$jarallax_data_tag = "data-jarallax='{\"speed\": 0.3}'";
	$fadeinstyle ="fadeIn";

				$output .= '<li '.$jarallax_data_tag.' class="jarallax lazyload vertical-parallax-image'.$height_class.'" style="'.$height.'background-image:url('. esc_url( $image_url ) .');">';
				$output .= '<div class="bg-detect-and-display slideshow-box-info work-details">';
					$blog_link = get_permalink();
					if (isSet($customlink_URL) && $customlink_URL<>"") {
						$blog_link = $customlink_URL;
					}
			$output .= '<a href="'.$blog_link.'">';
			$output .= '<div class="heading-block">';
			if ($title=="true") {
				$output .= '<h2 class="line-standby photocard-title">
				'.get_the_title().'
				</h2>';
			}
			if ($date=="true") {
				$output .= '<h3 class="photocard-subtitle">
					'.get_the_date().'
				</h3>';
			}
			$output .= '</div>';
			if ($desc=="true") {
				$output .= '<div class="photocard-contents">
				<p>'.$summary_content.'</p>
				</div>';
			}
			$output .= '</a>';
	if (!empty($readmore_text)) {
		if ($postformat != "quote") {
			$output .= imaginem_codepack_show_continue_reading( $readmore_text , $blog_link);
		}
	}
				$output .='</div>';

				$output .='</li>';

	endwhile; endif;
	$output .='</ul>';
	$output .='</div>';

		wp_reset_query();
		return $output;
	}
}
add_shortcode("blog_parallax", "imaginem_shortcodes_blogparallax");
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'imaginem_shortcodes_blogphoto' ) ) {
	function imaginem_shortcodes_blogphoto($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"columns" => '4',
			"readmore_text" => 'Continue Reading',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"cat_slug" => '',
			"excerpt_length" => '25',
			"post_type" => '',
			"pagination" => 'true'
		), $atts));

	$column_type="listbox";
	$portfolioImage_type="superlens-gridblock-large";

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;
	$portfoliogrid='';
	$portfoliogrid .= '<div class="gridblock-blogphoto clearfix">';

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slug,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'meta_query' => array(
			    array(
			     'key' => '_thumbnail_id',
			     'compare' => 'EXISTS'
			    ),
			),
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slug,
			'paged' => $paged,
			'posts_per_page' => $limit,
		    'meta_query' => array(
		        array(
		         'key' => '_thumbnail_id',
		         'compare' => 'EXISTS'
		        ),
		    )
			));	
	}

	if (have_posts()) : while (have_posts()) : the_post();

	$postformat = get_post_format();
	if($postformat == "") {
		$postformat="standard";
	}

	$image_url = imaginem_codepack_featured_image_link(get_the_id());
	$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);

	if ($postformat == "quote") {
		$quote=get_post_meta(get_the_id(), 'pagemeta_meta_quote', true);
		$quote_author=get_post_meta(get_the_id(), 'pagemeta_meta_quote_author', true);
		$summary_content = '';
		$summary_content .= '<span class="quote_say"><i class="quote-say-left fa-quote-left fa"></i>'.$quote.'<i class="quote-say-right fa fa-quote-right"></i></span>';
		if ($quote_author != "") {
			$summary_content .= '<span class="quote_author">&#8212;&nbsp;' . $quote_author.'</span>';
		}
	}

	$current_terms = wp_get_object_terms( get_the_ID(), 'category'  );
	$current_worktype = '';
	$seperator = ',';
	foreach( $current_terms as $the_term ) {
		if ($the_term === end($current_terms)) {
			$seperator = '';
		}
		$current_worktype .= $the_term->name . $seperator;
	}

	if (!isSet($image_url) || $image_url=="") {
		$image_url = '';
	}

		$portfoliogrid .= do_shortcode('[photocard_two text_align="center" button="'.$readmore_text.'" background_color="none" image_block="center" button_link="'.get_permalink().'" image="'.$image_url.'" title="'.get_the_title().'" subtitle="'.get_the_date().'"]'.$summary_content.'[/photocard_two]');

	endwhile; endif;
	$portfoliogrid .='</div>';

		if ($pagination=='true') $portfoliogrid .= imaginem_codepack_pagination();
		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("blog_photo", "imaginem_shortcodes_blogphoto");
//Blog Carousel
if ( !function_exists( 'mBlogCarousel' ) ) {
	function mBlogCarousel($atts, $content = null) {
		extract(shortcode_atts(array(
			"post_type" => '',
			"cat_slug" => '',
			"format" => '',
			"carousel_type" => 'owl',
			"columns" => '4',
			"limit" => '-1',
			"title" => 'true',
			"desc" => 'true',
			"boxtitle" => 'true',
			"cat_slug" => '',
			"pagination" => 'true'
		), $atts));

	$uniqureID=get_the_id()."-".uniqid();
	$column_type="four";
	$portfolioImage_type="superlens-gridblock-large";
	if ($columns==4) { 
		$column_type="four";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==3) { 
		$column_type="three";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==2) { 
		$column_type="three";
		$portfolioImage_type="superlens-gridblock-large";
		}

	if ( $format == "portrait") {
		if ($columns==4) { 
			$portfolioImage_type="superlens-gridblock-large-portrait";
			}
		if ($columns==3) { 
			$portfolioImage_type="superlens-gridblock-large-portrait";
			}
		if ($columns==2) {
			$portfolioImage_type="superlens-gridblock-large-portrait";
			}
		if ($columns==1) {
			$portfolioImage_type="superlens-gridblock-full";
			}
	}

	if ($cat_slug=="-1") { $cat_slug=''; }
	$portfolio_count=0;
	$flag_new_row=true;
	$portfoliogrid='';

	if ($post_type<>"") {
			$type_explode = explode(",", $post_type);
			foreach ($type_explode as $postformat) {
				$count++;
				$postformat_slug = "post-format-" . $postformat;
				$terms[] .= $postformat_slug;
			}
			
			query_posts(array(
				'category_name' => $cat_slug,
				'posts_per_page' => $limit,
				'tax_query' => array(
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => $terms
							)
						)
				));
		} else {
			query_posts(array(
				'category_name' => $cat_slug,
				'posts_per_page' => $limit
				));	
		}

	if ($carousel_type=="owl") {

		$portfoliogrid .= '<div class="gridblock-owlcarousel-wrap clearfix">';
		$portfoliogrid .= '<div id="owl-'.$uniqureID.'" class="owl-carousel">';

		if (have_posts()) : while (have_posts()) : the_post();

			//echo $type, $portfolio_type;
		$custom = get_post_custom(get_the_ID());
		$portfolio_cats = get_the_terms( get_the_ID(), 'worktypes' );
		$lightboxvideo="";
		$thumbnail="";
		$customlink_URL="";
		$portfolio_thumb_header="Image";

		if ($portfolio_count==$columns) $portfolio_count=0;

		$protected="";
		$icon_class="column-gridblock-icon";
		$portfolio_count++;
		$image_status ='';
		if ( ! has_post_thumbnail() & !post_password_required() ) {
			$image_status = ' blog-no-image';
		}
		$portfoliogrid .= '<div class="gridblock-grid-element gridblock-blog-carousel'.$image_status.'">';

				$postformat = get_post_format();
				if($postformat == "") $postformat="standard";

				$postformat_icon = imaginem_codepack_get_postformat_icon($postformat);

				if ( post_password_required() ) {
					$protected=" gridblock-protected"; $iconclass="";
					$portfoliogrid .= '<a class="'.$protected.' gridblock-image-link gridblock-columns" href="'.get_permalink().'" >';
					//$portfoliogrid .= '<span class="hover-icon-effect"><i class="fa fa-lock fa-2x"></i></span>';
				} else {
					//Switch check for Linked Type
					$portfoliogrid .= '<a class="gridblock-blog-carousel-link" href="'.get_permalink() .'">';

				}
				$portfoliogrid .= '<div class="gridblock-background-hover">';
					$portfoliogrid .= '<div class="gridblock-links-wrap">';

				//if Password Required
				if ( !post_password_required() && has_post_thumbnail() ) {
					$portfoliogrid .= '<span class="column-gridblock-icon"><span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i></span></span>';
				}
				if ( ! has_post_thumbnail() & !post_password_required() ) {
					$portfoliogrid .= '<span class="column-gridblock-icon"><span class="grid-icon-status"><i class="'.$postformat_icon.' fa-2x"></i></span></span>';
				}
				// End of links wrap
				$portfoliogrid .= '</div>';
				if ($boxtitle=="true") {

					$current_terms = wp_get_object_terms( get_the_ID(), 'category' );
					$current_worktype = $current_terms[0]->name; 
				
					$portfoliogrid .= '<span class="boxtitle-hover">';
					$portfoliogrid .= '<h4 class="boxtitle">'.get_the_title().'</h4>';
					$portfoliogrid .= '<span class="boxtitle-worktype">'.$current_worktype.'</span>';
					$portfoliogrid .= '</span>';
				}
			$portfoliogrid .= '</div>';
			$portfoliogrid .= '</a>';
			if ( post_password_required() ) {

				$portfoliogrid .= '<div class="gridblock-protected">';
				$portfoliogrid .= '<span class="column-gridblock-icon"><span class="hover-icon-effect"><i class="feather-icon-lock"></i></span></span>';
				if ( $format == "portrait" ) {
					$protected_placeholder = '/images/blank-grid-portrait.png';
				} else {
					$protected_placeholder = '/images/blank-grid.png';
				}
				$portfoliogrid .= '<img src="'.get_template_directory_uri().$protected_placeholder.'" alt="blank" />';
				$portfoliogrid .= '</div>';

			} else {
				if ( ! has_post_thumbnail() ) {
					$portfoliogrid .= '<div class="gridblock-blank-element"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
				} else {
					// Slideshow then generate slideshow shortcode
					$portfoliogrid .= imaginem_codepack_display_post_image (
						get_the_ID(),
						$have_image_url="",
						$link=false,
						$type=$portfolioImage_type,
						$imagetitle=imaginem_codepack_image_title( get_the_ID() ),
						$class="displayed-image"
					);

				}
			}

		$portfoliogrid .='</div>';

		endwhile; endif;
		$portfoliogrid .='</div>';
		$portfoliogrid .='</div>';
		$portfoliogrid .='
		<script>
		/* <![CDATA[ */
		(function($){
		$(window).on( "load", function() {
			$("#owl-'.$uniqureID.'").owlCarousel({
			    responsive:{
			        0:{
			            items:1,
			            nav:true
			        },
			        400:{
			            items:1,
			            nav:true
			        },
			        600:{
			            items:2,
			            nav:true
			        },
			        800:{
			            items:2,
			            nav:true
			        },
			        1000:{
			            items:'.$columns.',
			            nav:true
			        }
			    },
				items: '.$columns.',
				nav : true,
				navText : ["",""],
				loop: true
			});
		})
		})(jQuery);
		/* ]]> */
		</script>
		';

	}

		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("blogcarousel", "mBlogCarousel");
///////// Recent Blog Lists ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mRecentBlog' ) ) {
	function mRecentBlog($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"columns" => '4',
			"animated" => 'true',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"cat_slug" => '',
			"post_type" => '',
			"excerpt_length" => '15',
			"readmore_text" => '',
			"pagination" => 'false'
		), $atts));

	if ($columns==4) { 
		$column_type="four";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==3) { 
		$column_type="three";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==2) { 
		$column_type="two";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==1) { 
		$column_type="one";
		$portfolioImage_type="superlens-gridblock-full";
		}

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;
	$portfoliogrid='';
	$portfoliogrid .= '<div class="gridblock-columns-wrap clearfix">';
	$portfoliogrid .= '<div id="gridblock-container" class="portfolio-gutter-spaced gridblock-'.$column_type.'">';

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slug,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slug,
			'paged' => $paged,
			'posts_per_page' => $limit
			));	
	}

	if (have_posts()) : while (have_posts()) : the_post();
		//echo $type, $portfolio_type;

	$postformat = get_post_format();
	if($postformat == "") $postformat="standard";

	$portfolio_thumb_header="Image";

	if ($portfolio_count==$columns) $portfolio_count=0;

	$protected="";
	$icon_class="column-gridblock-icon";
	$portfolio_count++;

	//if ($portfolio_count==1) $portfoliogrid .= '<li class="clearfix"></li>';
    $animation_class = '';
    if ($animated == "true") {
        $animation_class = ' animation-standby-portfolio animated fadeInUpSlow';
    }
	$portfoliogrid .= '<div class="blog-grid-element'.$animation_class.' gridblock-element gridblock-col-'.$portfolio_count.'">';

		$portfoliogrid .= '<div class="blog-grid-element-inner">';
		
		$linkcenter ='';
		$linkcenter="gridblock-link-center";

		$postformat_icon = imaginem_codepack_get_postformat_icon($postformat);

		//if Password Required
		if ( post_password_required() ) {
			$protected=" portfolio-protected"; $iconclass="";
			$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
			$portfoliogrid .= '<span class="grid-icon-status"><i class="fa fa-lock fa-2x"></i></span>';
			$portfoliogrid .= '<div class="portfolio-protected"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
		} else {

			if ( ! has_post_thumbnail() ) {
				$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
				$portfoliogrid .= '<span class="grid-icon-status"><i class="'.$postformat_icon.' fa-2x"></i></span>';
				$portfoliogrid .= '<div class="gridblock-blank-element"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
			}

			if ( has_post_thumbnail() ) {
			//Make sure it's not a slideshow
				//Switch check for Linked Type
				$portfoliogrid .= '<a class="gridblock-image-link blog-grid-element-has-image gridblock-columns" href="'.get_permalink() .'" rel="bookmark">';
				$portfoliogrid .= '<span class="grid-icon-status"><i class="'.$postformat_icon.' fa-2x"></i></span>';
			// Display Hover icon trigger classes

			// If it aint slideshow then display a background. Otherwise one is active in slideshow thumbnails.
			// Custom Thumbnail
			//Display Image
				$portfoliogrid .= imaginem_codepack_display_post_image (
					get_the_ID(),
					$have_image_url="",
					$link=false,
					$type=$portfolioImage_type,
					$imagetitle='',
					$class="preload-image displayed-image",
					$lazyload = true
				);
			} else {
				
			}
		}
		$portfoliogrid .= '</a>';
		$portfoliogrid .= '<div class="summary-info">';
			if ($date=='true') {
				$portfoliogrid .='<div class="summary-date"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
			}
			$category = get_the_category();
			if ($comments == 'true' ) {
				$portfoliogrid .= '<div class="summary-comment">';

				$num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
				if ( comments_open() ) {
					if ( $num_comments == 0 ) {
						$comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
					} elseif ( $num_comments > 1 ) {
						$comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
					} else {
						$comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
					}
					$portfoliogrid .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
				}
				$portfoliogrid .='</div>';
			}
		$portfoliogrid .='</div>';

		$portfoliogrid .= '<div class="blog-grid-element-content">';
			// If either of title and description needs to be displayed.
			if ($title=="true" || $description=="true") {
				$portfoliogrid .='<div class="work-details">';
					$hreflink = get_permalink();
					if ($title=="true") { $portfoliogrid .='<h4><a href="'.$hreflink.'" rel="bookmark" title="'. get_the_title() .'">'. get_the_title() .'</a></h4>'; }
					$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);
					if ($postformat=='quote') $summary_content = get_post_meta( get_the_id() , 'pagemeta_meta_quote', true);
					if ($description=="true") { $portfoliogrid .= '<div class="entry-content work-description">'. $summary_content .'</div>'; }
				$portfoliogrid .='</div>';
			}
			if ($readmore_text!='') {
				$portfoliogrid .= imaginem_codepack_show_continue_reading( $readmore_text , $hreflink);
			}

		$portfoliogrid .= '</div>';
	$portfoliogrid .= '</div>';
	$portfoliogrid .='</div>';


	endwhile; endif;
	$portfoliogrid .='</div>';
	$portfoliogrid .='</div>';

		if ($pagination=='true') $portfoliogrid .= imaginem_codepack_pagination();
		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("recentblog", "mRecentBlog");

///////// Recent Blog Lists ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mBlogList' ) ) {
	function mBlogList($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"bloglist_style" => 'default',
			"date" => 'true',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"blogtimealign" => 'top',
			"cat_slugs" => '',
			"excerpt_length" => '15',
			"post_type" => '',
			"pagination" => 'false'
		), $atts));

	$column_type="listbox";
	$portfolioImage_type="superlens-gridblock-large";

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slugs,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slugs,
			'paged' => $paged,
			'posts_per_page' => $limit
			));	
	}
		ob_start();
		get_template_part( 'loop', 'blog' );
		$bloglist = '<div class="mtheme-shortcode-bloglist">';
		$bloglist .= ob_get_clean();
		$bloglist .= '</div>';
		wp_reset_query();
		return $bloglist;
	}
}
add_shortcode("bloglist", "mBlogList");
///////// Recent Blog Lists Small ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mBlogListSmall' ) ) {
	function mBlogListSmall($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"bloglist_style" => 'default',
			"date" => 'true',
			"columns" => '4',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"blogtimealign" => 'top',
			"cat_slug" => '',
			"excerpt_length" => '15',
			"post_type" => '',
			"pagination" => 'false'
		), $atts));

	$column_type="listbox";
	$portfolioImage_type="superlens-gridblock-large";

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slug,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slug,
			'paged' => $paged,
			'posts_per_page' => $limit
			));	
	}
		ob_start();
		get_template_part( 'loop', 'blog' );
		$bloglist = '<div class="mtheme-shortcode-bloglist bloglist-small sc-bloglist-columns-'.$columns.' date-time-style-'.$blogtimealign.'">';
		$bloglist .= ob_get_clean();
		$bloglist .= '</div>';
		wp_reset_query();
		return $bloglist;
	}
}
add_shortcode("bloglistsmall", "mBlogListSmall");

///////// Recent Blog Lists ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mRecentBlogListBox' ) ) {
	function mRecentBlogListBox($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"columns" => '4',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"cat_slug" => '',
			"excerpt_length" => '15',
			"post_type" => '',
			"pagination" => 'false'
		), $atts));

	$column_type="listbox";
	$portfolioImage_type="superlens-gridblock-large";

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$flag_new_row=true;
	$portfoliogrid='';
	$portfoliogrid .= '<div class="gridblock-listbox gridblock-columns-wrap clearfix">';
	$portfoliogrid .= '<ul class="gridblock-'.$column_type.' clearfix">';

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slug,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slug,
			'paged' => $paged,
			'posts_per_page' => $limit
			));	
	}

	if (have_posts()) : while (have_posts()) : the_post();
		//echo $type, $portfolio_type;

	$postformat = get_post_format();
	if($postformat == "") $postformat="standard";

	$portfolio_thumb_header="Image";

	if ($portfolio_count==$columns) $portfolio_count=0;

	$protected="";
	$icon_class="column-gridblock-icon";
	$portfolio_count++;

	$portfoliogrid .= '<li class="gridblock-listbox-row gridblock-col-'.$portfolio_count.' clearfix">';
		
		$portfoliogrid .= '<div class="listbox-image">';

		$portfoliogrid .= '<span class="gridblock-link-hover">';
		
		$linkcenter ='';
		$linkcenter="gridblock-link-center";

		$postformat_icon = imaginem_codepack_get_postformat_icon($postformat);

		$portfoliogrid .= '<a href="'.get_permalink().'"><span class="hover-icon-effect column-gridblock-link '.$linkcenter.'"><i class="'.$postformat_icon.'"></i></span></a>';
		$portfoliogrid .= '</span>';


		//if Password Required
		if ( post_password_required() ) {
			$protected=" gridblock-protected"; $iconclass="";
			$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
			$portfoliogrid .= '<span class="grid-blank-status"><i class="fa fa-lock fa-2x"></i></span>';
			$portfoliogrid .= '<div class="gridblock-protected"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
		} else {

			if ( ! has_post_thumbnail() ) {
				$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
				$portfoliogrid .= '<span class="grid-blank-status"><i class="'.$postformat_icon.' fa-2x"></i></span>';
				$portfoliogrid .= '<div class="gridblock-protected"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
			}

			if ( has_post_thumbnail() ) {
			//Make sure it's not a slideshow
				//Switch check for Linked Type
			$portfoliogrid .= '<a class="gridblock-image-link gridblock-columns" href="'.get_permalink() .'" rel="bookmark" title="'.get_the_title().'">';
			// Display Hover icon trigger classes

			// If it aint slideshow then display a background. Otherwise one is active in slideshow thumbnails.
			$portfoliogrid .= '<span class="gridblock-background-hover"></span>';
			// Custom Thumbnail
			//Display Image
				$portfoliogrid .= imaginem_codepack_display_post_image (
					get_the_ID(),
					$have_image_url="",
					$link=false,
					$type=$portfolioImage_type,
					$imagetitle='',
					$class="preload-image displayed-image"
				);
			} else {
				$portfoliogrid .= '<a class="'.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
				$portfoliogrid .= '<div class="post-nothumbnail"></div>';
			} 
		}
		$portfoliogrid .= '</a>';
		
		$portfoliogrid .= '<div class="listbox-content">';
		$portfoliogrid .= '<div class="summary-info">';
			$category = get_the_category();
			if ($comments == 'true' ) {
				$portfoliogrid .= '<div class="summary-comment">';

				$num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
				if ( comments_open() ) {
					if ( $num_comments == 0 ) {
						$comments_desc = __('0 <i class="fa fa-comment-alt"></i>');
					} elseif ( $num_comments > 1 ) {
						$comments_desc = $num_comments . __(' <i class="fa fa-comment-alt"></i>');
					} else {
						$comments_desc = __('1 <i class="fa fa-comment-alt"></i>');
					}
					$portfoliogrid .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
				}
				$portfoliogrid .='</div>';
			}
			if ($date=='true') {
				$portfoliogrid .='<div class="summary-date"><i class="fa fa-clock-o"></i> '.get_the_date().'</div>';
			}
		$portfoliogrid .='</div>';
		$portfoliogrid .= '</div>';
		// If either of title and description needs to be displayed.
		if ($title=="true" || $description=="true") {
			$portfoliogrid .='<div class="work-details">';
				$hreflink = get_permalink();
				if ($title=="true") { $portfoliogrid .='<h4><a href="'.$hreflink.'" rel="bookmark" title="'. get_the_title() .'">'. get_the_title() .'</a></h4>'; }
				$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);
				if ($postformat=='quote') $summary_content = get_post_meta( get_the_id() , 'pagemeta_meta_quote', true);
				if ($description=="true") { $portfoliogrid .= '<p class="entry-content work-description">'. $summary_content .'</p>'; }
			$portfoliogrid .='</div>';
		}
		$portfoliogrid .= '</div>';

	$portfoliogrid .='</li>';

	endwhile; endif;
	$portfoliogrid .='</ul>';
	$portfoliogrid .='</div>';

		if ($pagination=='true') $portfoliogrid .= imaginem_codepack_pagination();
		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("recent_blog_listbox", "mRecentBlogListBox");
///////// Blog Timeline ///////////////
//++++++++++++++++++++++++++++++++++++++//
if ( !function_exists( 'mBlog_Timeline' ) ) {
	function mBlog_Timeline($atts, $content = null) {
		extract(shortcode_atts(array(
			"comments" => 'true',
			"date" => 'true',
			"columns" => '2',
			"limit" => '-1',
			"title" => 'true',
			"description" => 'true',
			"cat_slug" => '',
			"post_type" => '',
			"excerpt_length" => '15',
			"readmore_text" => '',
			"pagination" => 'false'
		), $atts));

	$columns = 2;
	if ($columns==4) { 
		$column_type="four";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==3) { 
		$column_type="three";
		$portfolioImage_type="superlens-gridblock-large";
		}
	if ($columns==2) { 
		$column_type="two";
		$portfolioImage_type="superlens-gridblock-full-medium";
		}
	if ($columns==1) { 
		$column_type="one";
		$portfolioImage_type="superlens-gridblock-full";
		}

	$portfolio_count=0;
	$postformats="";
	$terms='';
	$terms=array();
	$count=0;
	$blogcount=0;
	$flag_new_row=true;
	$portfoliogrid='';
	$portfoliogrid .= '<div class="gridblock-columns-wrap gridblock-timeline-block clearfix">';
	$portfoliogrid .= '<div id="gridblock-timeline" class="gridblock-two">';

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}

	if ($post_type<>"") {
		$type_explode = explode(",", $post_type);
		foreach ($type_explode as $postformat) {
			$count++;
			$postformat_slug = "post-format-" . $postformat;
			$terms[] .= $postformat_slug;
		}
		
		query_posts(array(
			'category_name' => $cat_slug,
			'posts_per_page' => $limit,
			'paged' => $paged,
			'tax_query' => array(
					array(
						'taxonomy' => 'post_format',
						'field' => 'slug',
						'terms' => $terms
						)
					)
			));
	} else {
		query_posts(array(
			'category_name' => $cat_slug,
			'paged' => $paged,
			'posts_per_page' => $limit
			));	
	}

	$prev_month='';

	if (have_posts()) : while (have_posts()) : the_post();
		//echo $type, $portfolio_type;

	$postformat = get_post_format();
	if($postformat == "") $postformat="standard";

	$portfolio_thumb_header="Image";

	if ($portfolio_count==$columns) $portfolio_count=0;

	$protected="";
	$icon_class="column-gridblock-icon";
	$portfolio_count++;

	$blogcount++;
	if ($blogcount % 2 == 0) {
		$align_to = 'right';
	} else {
		$align_to = 'left';
	}

	$this_post = get_post( get_the_id());
	$blogpost_time = strtotime($this_post->post_date);
	$curr_month = date('n', $blogpost_time);

	if ($curr_month!=$prev_month) {
		$blogcount = 1;
		$align_to = 'left';
		$portfoliogrid .= '<div class="blog-timeline-month-wrap clearfix">';
		$portfoliogrid .= '<div class="blog-timeline-month">'.get_the_date(). '</div>';
		$portfoliogrid .= '</div>';
	}
	//if ($portfolio_count==1) $portfoliogrid .= '<li class="clearfix"></li>';
	$portfoliogrid .= '<div class="blog-grid-element is-animated animated fadeInUp blog-grid-element-'.$align_to.' gridblock-element gridblock-col-'.$portfolio_count.'">';

		$postformat = get_post_format();
		if($postformat == "") $postformat="standard";
		$portfoliogrid .= '<div class="blog-grid-element-inner timeline-format-'.$postformat.'">';
		
		$linkcenter ='';
		$linkcenter="gridblock-link-center";

		$postformat_icon = imaginem_codepack_get_postformat_icon($postformat);

		//if Password Required
		if ( post_password_required() ) {
			$protected=" portfolio-protected"; $iconclass="";
			$portfoliogrid .= '<a class="grid-blank-element '.$protected.' gridblock-image-link gridblock-columns" title="'.get_the_title().'" href="'.get_permalink().'" >';
			$portfoliogrid .= '<span class="grid-icon-status"><i class="fa fa-lock fa-2x"></i></span>';
			$portfoliogrid .= '<div class="portfolio-protected"><img src="'.get_template_directory_uri().'/images/blank-grid.png" alt="blank" /></div>';
			$portfoliogrid .= '</a>';
		} else {
			ob_start();
			get_template_part( 'template-parts/postformats/'.$postformat );
			$portfoliogrid .= ob_get_clean();
		}

			$portfoliogrid .= '<div class="summary-info">';
				if ($date=='true') {
					$portfoliogrid .='<div class="summary-date"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
				}
				$category = get_the_category();
				if ($comments == 'true' ) {
					$portfoliogrid .= '<div class="summary-comment">';

					$num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
					if ( comments_open() ) {
						if ( $num_comments == 0 ) {
							$comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
						} elseif ( $num_comments > 1 ) {
							$comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
						} else {
							$comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
						}
						$portfoliogrid .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
					}
					$portfoliogrid .='</div>';
				}
			$portfoliogrid .='</div>';

		$portfoliogrid .= '<div class="blog-grid-element-content">';
			// If either of title and description needs to be displayed.
			if ($title=="true" || $description=="true") {
				$portfoliogrid .='<div class="work-details">';
					$hreflink = get_permalink();
					if ($title=="true") { $portfoliogrid .='<h4><a href="'.$hreflink.'" rel="bookmark" title="'. get_the_title() .'">'. get_the_title() .'</a></h4>'; }
					$summary_content = imaginem_codepack_excerpt_limit($excerpt_length);
					if ($readmore_text!='') {
						$summary_content .= '<div class="blogpost_readmore"><a href="'.$hreflink.'">'.$readmore_text.'</a></div>';
					}
					if ($postformat=='quote') $summary_content = get_post_meta( get_the_id() , 'pagemeta_meta_quote', true);
					if ($description=="true") { $portfoliogrid .= '<div class="entry-content work-description">'. $summary_content .'</div>'; }
				$portfoliogrid .='</div>';
			}

		$portfoliogrid .= '</div>';
	$portfoliogrid .= '</div>';
	$portfoliogrid .='</div>';

	$prev_month = $curr_month;
	endwhile; endif;
	$portfoliogrid .='</div>';
	$portfoliogrid .='</div>';

		if ($pagination=='true') $portfoliogrid .= imaginem_codepack_pagination();
		wp_reset_query();
		return $portfoliogrid;
	}
}
add_shortcode("blogtimeline", "mBlog_Timeline");
?>