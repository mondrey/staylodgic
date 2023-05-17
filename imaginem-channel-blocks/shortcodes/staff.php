<?php
function mtheme_theme_Socials( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"social_icon" => '',
		"social_link" => '',
		"social_color" => '',
		"align" => 'false'
	), $atts));

	if ($align =="false" ) {
		// Initially built for staff shortcode. For individual use it will have to have the align variable set.
		$socials = '<li><a href="'.$social_link.'">'.html_entity_decode( $social_icon ).'</a></li>';
	} else {
		$socials = '<div class="social-shortcode align-'.$align.' social-margin-'.$align.'"><a title="'.$social_text.'" href="'.$social_link.'"><i style="color:'.$social_color.'" class="'.$social_icon.'"></i></a></div>';
	}
	return $socials;
}
add_shortcode('socials', 'mtheme_theme_Socials');
//People
function mtheme_People( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"name" => '',
		"animated" => 'none',
		"socialicons" => '',
		"image" => '',
		"image_size" => '',
		"link" => '',
		"link_target" => '',
		'imageid' => '',
		"title" => '',
		"desc" => ''
	), $atts));

if ( !empty($imageid)) {
	$image_array = wp_get_attachment_image_src($imageid,$image_size,false);
	//print_r($image_array);
	$image_src = $image_array[0];
} else {
	$image_src=$image;
}

if ($animated != 'none') {
	$animated = 'animation-standby animated ' .$animated;
}

$personsocials='';
if ($socialicons<>"false") {
	$personsocials = '<ul class="person-socials">';
	$personsocials .= do_shortcode( html_entity_decode($content) );
	$personsocials .= '</ul>';
}

$persondescription='';
if ($desc<>"") {
	$persondescription = '<div class="person-desc">'. wpautop( html_entity_decode($desc) ).'</div>';
}

$people_link_start = '';
$people_link_end = '';

$person = '
<div class="person box-title">
	<div class="person-image-wrap">
		<div class="person-image '.$animated.'">
			<img src="'.esc_url($image_src).'" alt="'.esc_attr($name).'" />
		</div>
		'.$personsocials.'
	</div>
	<div class="person-details">
	<h4 class="staff-position">'.$title.'</h4>
	<h3>'.$people_link_start.$name.$people_link_end.'</h3>
	'.$persondescription.'
	</div>
</div>';

   return $person;
}
add_shortcode('staff', 'mtheme_People');
// Testimonials
add_shortcode('testimonials', 'mtheme_Testimonials');
function mtheme_Testimonials($atts, $content) {
	extract(shortcode_atts(array(
		"columns" => '1',
		"autoplay" => 'false',
		"autoplayinterval" => '10000'
	), $atts));

		$uniqueID = "testimonial-id-".rand(0,1000);
		$qblock = '';
$qblock .= '
<div class="testimonials-wrap">
	<div id="testimony-'.$uniqueID.'" data-type="testimony" data-columns="'.$columns.'" data-autoplay="'.$autoplay.'" data-autoplaytimeout="'.$autoplayinterval.'" data-id="testimony-'.$uniqueID.'" class="owl-carousel-detect owl-carousel owl-carousel-column-' . $columns . '">
';
		$qblock .= do_shortcode($content);
		$qblock .= '
	</div>
</div>';

		return $qblock;
}
// The Saying
function mtheme_Testimonial($atts, $content) {
	extract(shortcode_atts(array(
		"quote" => '',
		"name" => '',
		"columns" => '1',
		"company" => '',
		"link" => '',
		"link_type" => '',
		"position" =>'',
		"image" => '',
		"imageid" => ''
	), $atts));

	if ( !empty($imageid)) {
		$image_array = wp_get_attachment_image_src($imageid,'superlens-gridblock-square-big',false);
		//print_r($image_array);
		$image_src = $image_array[0];
	} else {
		$image_src=$image;
	}

	$link_target = '';
	if ($link_type<>"") {
		if ($link_type=="_blank") {
			$link_target = 'target="_blank"';
		}
		if ($link_type=="_self") {
			$link_target = 'target="_self"';
		}
	}

	$testimonial = '';
	$testimonial .= '<div class="testimonial-wrap">';

	$testimonial_client_info = '<div class="client-info-quote">';
		$testimonial_client_info .= '<span class="client-name"><cite>';
		$testimonial_client_info .= $name;
		$testimonial_client_info .= '</cite></span>';
		$testimonial_client_info .= '<span class="client-company">';
		$link=trim($link);
		if (!empty( $link )) { 
			$testimonial_client_info .= '<a '.$link_target.' href="'.esc_url($link).'">';
		}
		$testimonial_client_info .=  $company;
		if (!empty( $link )) { 
			$testimonial_client_info .= '</a>';
		}
		$testimonial_client_info .= '</span>';
	$testimonial_client_info .= '</div>';

	if ( $image_src != '' ) {
		$testimonial .= '<div class="client-details client-background-fill">';
		$testimonial .= '<div class="client-image lazyload" data-bg="' . esc_url( $image_src ). '"></div>';
		$testimonial .= '</div>';
	}

		$testimonial .= '<div class="testimonial-say-wrap">';
		$testimonial .= '<div class="testimonial-say">';
			$testimonial .= '<div class="client-say"><div class="testimonial-quote">';
			$testimonial .= wpautop( html_entity_decode($quote) );
			$testimonial .= '</div></div>';
			if ( '1' === $columns ) {
				$testimonial .= $testimonial_client_info;
			}
		$testimonial .= '</div>';



			$testimonial .= '<div class="client-info">';
				if ( $image_src != '' ) {
					$testimonial .= '<div class="client-details client-shape-fill">';
					$testimonial .= '<div class="client-image lazyload" data-bg="' . esc_url( $image_src ). '"></div>';
					$testimonial .= '</div>';
				}
			$testimonial .= '</div>';

		if ( '1' !== $columns ) {
			$testimonial .= $testimonial_client_info;
		}

			
	$testimonial .= '</div>';
	$testimonial .= '</div>';
	return $testimonial;
}
add_shortcode('testimonial', 'mtheme_Testimonial');

// Clients
//Obsolete
add_shortcode('clients', 'clients');
function clients($atts, $content) {
	extract(shortcode_atts(array(
		'column' => '5'
	), $atts));

	$clientbox = '<div class="client-column-'.$column.' clearfix">';
	$clientbox .= do_shortcode($content);
	$clientbox .= '</div>';

	return $clientbox;
}
// The Saying
function mtheme_Client($atts, $content) {
	extract(shortcode_atts(array(
		"logo" => '',
		"link" => '',
		"hovertitle" =>'',
		"last_item" => 'no'
	), $atts));

	$column_edge="client-item-space";
	if ($last_item=="yes") $column_edge="clearfix";
	$client = '<div class="client-item '.$column_edge.'">';
	if ( $link <>"" ) { $client .= '<a class="ntips" title="'.$hovertitle.'" href="'.$link.'" >'; }
	$client .= '<img src="' . $logo . '" alt="client" />';
	if ( $link <>"" ) { $client .= '</a>'; }
	$client .= '</div>';

	return $client;
}
add_shortcode('client', 'mtheme_Client');

// Display Logo Carousel
add_shortcode('carousel_group', 'mtheme_carousel_group');
function mtheme_carousel_group($atts, $content) {
	extract(shortcode_atts(array(
		'columns' => '5'
	), $atts));

	$uniqueID=get_the_id()."-".dechex(mt_rand(1,65535));

	$carousel_group = '<div class="shortcode-carousel-group clearfix">';
	$carousel_group .= '<div id="shortcode-carousel-owl-'.$uniqueID.'" class="owl-carousel">';
	$carousel_group .= do_shortcode($content);
	$carousel_group .= '</div>';
	$carousel_group .= '</div>';
	$carousel_group .='
	<script>
	/* <![CDATA[ */
	(function($){
	$(window).on( "load", function() {
		$("#shortcode-carousel-owl-'.$uniqueID.'").owlCarousel({
		    responsive:{
		        0:{
		            items:1,
		            nav:false
		        },
		        600:{
		            items:4,
		            nav:false
		        },
		        1024:{
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

	return $carousel_group;
}
// The Saying
function mtheme_carousel_item($atts, $content) {
	extract(shortcode_atts(array(
		"image" => '',
		"link" => '',
		'name' => ''
	), $atts));

	$carousel_item = '<div class="shortcode-carousel-item">';
	if ( trim($link) != "" ) { $carousel_item .= '<a href="'.esc_url($link).'" >'; }
	$carousel_item .= '<img src="' . esc_url($image) . '" alt="'.$name.'" class="stips" title="'.$name.'" />';
	if ( trim($link) <>"" ) { $carousel_item .= '</a>'; }
	$carousel_item .= '</div>';

	return $carousel_item;
}
add_shortcode('carousel_item', 'mtheme_carousel_item');
?>