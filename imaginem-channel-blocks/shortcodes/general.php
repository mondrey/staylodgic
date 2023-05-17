<?php
function mtheme_codepack_display_current_year( $atts, $content = null ) {
	$current_year = date("Y");
	return $current_year;
}
add_shortcode('theme_display_current_year', 'mtheme_codepack_display_current_year');
function mtheme_get_the_ip_address() {
	$ip = $_SERVER['REMOTE_ADDR'];
	return $ip;
}
function mtheme_timelines( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'heading' => '',
	), $atts));

	
	$timelines = '<div class="timeline-wrap">'.do_shortcode($content).'</div>';
	$timelines = '<ul class="timeline-list">'.do_shortcode($content).'</ul>';

	return $timelines;

}
add_shortcode('timelines', 'mtheme_timelines');

function mtheme_timeline( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'time' => '',
		'title' => '',
	), $atts));


$timeline = '<li class="timeline-list-event is-animated animated fadeInUpSlight" data-date="'.esc_attr($time).'">
<span class="is-animated animated fadeInSlightRight">'.$time.'</span>
<h3>'.$title.'</h3>
  <div class="timeline-contents">'.$content.'</div>
</li>';

return $timeline;

}
add_shortcode('timeline', 'mtheme_timeline');

function mtheme_imagedrops( $atts, $content = null ) {
   extract( shortcode_atts( array(
   		'image_size' => '',
   		'id1' => '',
   		'id2' => '',
   		'id3' => ''
      ), $atts ) );

	  $image_src_1 = '';
	  $image_src_2 = '';
	  $image_src_3 = '';
   $image_array_1 = wp_get_attachment_image_src($id1,$image_size,false);
   if ( isset( $image_array_1[0] ) ) {
		$image_src_1 = $image_array_1[0];
   }

   $image_array_2 = wp_get_attachment_image_src($id2,$image_size,false);
   if ( isset( $image_array_2[0] ) ) {
		$image_src_2 = $image_array_2[0];
   }

   $image_array_3 = wp_get_attachment_image_src($id3,$image_size,false);
   if ( isset( $image_array_3[0] ) ) {
		$image_src_3 = $image_array_3[0];
   }

	$imagedrops = '<div class="imagedrops-wrap is-animation-group">';
	$imagedrops .= '
	<div class="imagedrop-element imagedrop-element-1">
		<div class="imagedrop-element-inner imagedrop-inner-1">
			<img data-animationdelay="400" class="imagedrop image-drop-1 animated-group-element animation-standby-group-item mtheme-animated mtheme-zoomIn" alt="'.esc_attr( imaginem_codepack_get_alt_text($id1) ).'" src="'.esc_url($image_src_1).'" />
		</div>
	</div>';
	$imagedrops .= '
	<div class="imagedrop-element imagedrop-element-2">
		<div class="imagedrop-element-inner imagedrop-inner-2">
			<img data-animationdelay="800" class="imagedrop image-drop-2 animated-group-element animation-standby-group-item mtheme-animated mtheme-zoomIn" alt="'.esc_attr( imaginem_codepack_get_alt_text($id2) ).'" src="'.esc_url($image_src_2).'" />
		</div>
	</div>';
	$imagedrops .= '
	<div class="imagedrop-element imagedrop-element-3">
		<div class="imagedrop-element-inner imagedrop-inner-3">
			<img data-animationdelay="1200" class="imagedrop image-drop-3 animated-group-element animation-standby-group-item mtheme-animated mtheme-zoomIn" alt="'.esc_attr( imaginem_codepack_get_alt_text($id3) ).'" src="'.esc_url($image_src_3).'" />
		</div>
	</div>';
	$imagedrops .= '</div>';

	return $imagedrops;
}
add_shortcode('imagedrops', 'mtheme_imagedrops');
function mtheme_flipbox( $atts, $content = null ) {
   extract( shortcode_atts( array(
   		'title' => '',
   		'icon' => '',
		'target' => '',
		'nofollow' => '',
   		'image_size' => '',
   		'image_front' => '',
   		'image_back' => '',
   		'button_text' => '',
   		'button_link' => ''
      ), $atts ) );

	if ($target=="on") {
		$target=' target="_blank"';
	} else {
		$target='';
	}
	if ($nofollow=="on") {
		$nofollow=' rel="nofollow"';
	} else {
		$nofollow='';
	}
	$animated = '';

	$icon = html_entity_decode( $icon );

    $image_array_1 = wp_get_attachment_image_src($image_front,$image_size,false);
    $image_src_1 = $image_array_1[0];

    $image_array_2 = wp_get_attachment_image_src($image_back,$image_size,false);
    $image_src_2 = $image_array_2[0];

	$flipbox = '<div class="flipbox-wrap '.$animated.' text-is-bright">';
		$flipbox .= '<div class="flipbox-card-flip">';
			$flipbox .= '<div class="flipbox-card-front" style="background-image: url('.esc_url($image_src_1).');">';
				$flipbox .= '<div class="flipbox-card-container-front">';
					$flipbox .= '<div class="flipbox-icon">'.$icon.'</div>';
					$flipbox .= '<h2>'.$title.'</h2>';
					$flipbox .= '<p>'.$content.'</p>';
				$flipbox .= '</div>';
				$flipbox .= '<div class="flip-overlay"></div>';
			$flipbox .= '</div>';
			$flipbox .= '<div class="flipbox-card-back" style="background-image: url('.esc_url($image_src_2).');">';
				$flipbox .= '<div class="flipbox-card-container-back">';
					$flipbox .= '<a href="'.esc_url($button_link).'" '.$target.$nofollow.' class="mtheme-button">'.$button_text.'</a>';
				$flipbox .= '</div>';
				$flipbox .= '<div class="flip-overlay"></div>';
			$flipbox .= '</div>';
		$flipbox .= '</div>';
	$flipbox .= '</div>';

return $flipbox;

}
add_shortcode('flipbox', 'mtheme_flipbox');
function mtheme_eventinfobox( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'type' => 'column',
		'status' => 'false',
		'when' => 'true',
		'when_date' => 'true',
		'when_time' => 'true',
		'when_text' => 'When',
		'when_icon' => 'ion-ios-clock',
		'where' => 'true',
		'where_text' => 'Where',
		'where_icon' => 'ion-ios-location',
		'cost' => 'true',
		'cost_text' => 'Cost',
		'cost_icon' => 'ion-ios-pricetag',
		'capacity' => 'true',
		'capacity_text' => 'Capacity',
		'capacity_icon' => 'ion-ios-people',
		'remaining' => 'true',
		'remaining_text' => 'Remaining',
		'remaining_icon' => 'ion-ios-person',
	), $atts ) );


	$event_startdate='';
	$event_starttime='';
	$event_endtime='';
	$event_enddate='';
	$event_notice='';
	$event_venue_name='';
	$event_venue_street='';
	$event_venue_state='';
	$event_venue_postal='';
	$event_venue_country='';
	$event_venue_phone='';
	$event_venue_website='';
	$event_venue_currency='';
	$event_venue_cost='';

	$events_page_id = get_the_id();
	$custom = get_post_custom(get_the_id());
	if (isset($custom['pagemeta_event_startdate'][0])) $event_startdate=$custom['pagemeta_event_startdate'][0];
	if (isset($custom['pagemeta_event_starttime'][0])) $event_starttime=$custom['pagemeta_event_starttime'][0];
	if (isset($custom['pagemeta_event_endtime'][0])) $event_endtime=$custom['pagemeta_event_endtime'][0];
	if (isset($custom['pagemeta_event_enddate'][0])) $event_enddate=$custom['pagemeta_event_enddate'][0];

	if (isset($custom['pagemeta_event_notice'][0])) $event_notice=$custom['pagemeta_event_notice'][0];

	if (isset($custom['pagemeta_event_venue_name'][0])) $event_venue_name=$custom['pagemeta_event_venue_name'][0];
	if (isset($custom['pagemeta_event_venue_street'][0])) $event_venue_street=$custom['pagemeta_event_venue_street'][0];
	if (isset($custom['pagemeta_event_venue_state'][0])) $event_venue_state=$custom['pagemeta_event_venue_state'][0];
	if (isset($custom['pagemeta_event_venue_postal'][0])) $event_venue_postal=$custom['pagemeta_event_venue_postal'][0];
	if (isset($custom['pagemeta_event_venue_country'][0])) $event_venue_country=$custom['pagemeta_event_venue_country'][0];
	if (isset($custom['pagemeta_event_venue_phone'][0])) $event_venue_phone=$custom['pagemeta_event_venue_phone'][0];
	if (isset($custom['pagemeta_event_venue_website'][0])) $event_venue_website=$custom['pagemeta_event_venue_website'][0];

	if (isset($custom['pagemeta_event_venue_currency'][0])) $event_venue_currency=$custom['pagemeta_event_venue_currency'][0];
	if (isset($custom['pagemeta_event_venue_cost'][0])) $event_venue_cost=$custom['pagemeta_event_venue_cost'][0];
	if (isset($custom['pagemeta_event_venue_capacity'][0])) $event_venue_capacity=$custom['pagemeta_event_venue_capacity'][0];
	if (isset($custom['pagemeta_event_venue_remaining'][0])) $event_venue_remaining=$custom['pagemeta_event_venue_remaining'][0];

	if ($type=='column') {
		$events_sep_class = 'span4 float-left';
		$no_of_columns = 0;
		if ( $when == 'true' ) {
			$no_of_columns++;
		}
		if ( $where == 'true' ) {
			$no_of_columns++;
		}
		if ( $cost == 'true' ) {
			$no_of_columns++;
		}
		if ( 1 === $no_of_columns ) {
			$events_sep_class = 'span12';
		}
		if ( 2 === $no_of_columns ) {
			$events_sep_class = 'float-left';
		}
		$events_style = 'column';
	}
	if ($type=='centered') {
		$events_sep_class = 'events-centered-style';
		$events_style = 'centered';
	}
	if ($type=='list') {
		$events_sep_class = 'events-list-style';
		$events_style = 'list';
	}
	$eventinfobox = '';

	if ( $when=='true' || $where=='true' || $cost=='true'  || $capacity=='true' || $status=='true' ) {
		$eventinfobox = '<div class="events-info-wrap mtheme-supercell">';
			if ( $when=='true' || $where=='true' || $cost=='true'  || $capacity=='true' ) {
				$eventinfobox .= '<div class="events-details-wrap clearfix">';

					if ($when=='true') {
						$eventinfobox .= '<div class="'.$events_sep_class.' events-details-block">';

						$event_schedule_heading = '<h3 class="event-heading"><i class="event-icon event-icon-spaced '.$when_icon.'"></i><span>'.$when_text.'</span></h3>';

						if (isSet($event_startdate) && $event_startdate<>"") {
							$event_start_datetime = explode(" ", $event_startdate);
						}
						if (isSet($event_enddate) && $event_enddate<>"") {
							$event_end_datetime = explode(" ", $event_enddate);
						}
						
						$event_schedule = false;
						if (isSet($event_startdate) && $event_startdate<>"") {
							$eventstartline = '<li>'.date_i18n( get_option( 'date_format' ), strtotime($event_start_datetime[0]) ).'</li>';
							$event_schedule = true;
						}
						$event_time_format = imaginem_codepack_get_option_data('events_time_format');
						if (isSet($event_enddate) && $event_enddate<>"") {
							$eventendline = '';
							if ( $event_startdate !== $event_enddate ) {
								$eventendline = '<li>'.date_i18n( get_option( 'date_format' ), strtotime($event_end_datetime[0]) ).'</li>';
								$event_schedule = true;
							}
						}
						
						$eventinfobox .= $event_schedule_heading;
						
						if ( $event_start_datetime[0] == $event_end_datetime[0] ) {
							$eventendline = '';
						}

						if ( isSet($event_start_datetime[1]) ) {
							if ($event_time_format == "24hr") {
								$event_start_time = date_i18n( 'H:i', strtotime($event_start_datetime[1]) );
							} else {
								$event_start_time = date_i18n( 'h:i A', strtotime($event_start_datetime[1]) );
							}
						}
						if ( isSet($event_end_datetime[1]) ) {
							if ($event_time_format == "24hr") {
								$event_end_time = date_i18n( 'H:i', strtotime($event_end_datetime[1]) );
							} else {
								$event_end_time = date_i18n( 'h:i A', strtotime($event_end_datetime[1]) );
							}
						}
						
						$event_date_same_with_different_time = false;
						if ( $event_start_datetime[0] == $event_end_datetime[0] && $event_start_datetime[1] !== $event_end_datetime[1] ) {
							$event_date_same_with_different_time = true;
						}
						$event_same_day_and_time = false;
						if ( $event_start_datetime[0] == $event_end_datetime[0] && $event_start_datetime[1] == $event_end_datetime[1] ) {
							$event_same_day_and_time = true;
						}

						if ( 'true' === $when_date ) {
							if ($event_schedule) {
								$eventinfobox .= '<ul class="event-details entry-content event-date-time">';
								$eventinfobox .= $eventstartline.$eventendline;
								if ( $event_date_same_with_different_time ) {
									$eventinfobox .= '<li>' . $event_start_time . '<span class="start-end-time-seperator">-</span>' . $event_end_time . '</li>';
								}
								$eventinfobox .= '</ul>';
							}
						}

						if ( 'true' === $when_time && !$event_date_same_with_different_time ) {
							$eventinfobox .= '<ul class="event-details entry-content event-date-time">';

							if ( isSet($event_start_datetime[1]) ) {
								$eventinfobox .= '<li>';
								$eventinfobox .= $event_start_time;
								$eventinfobox .= '</li>';
							}
							if ( !$event_same_day_and_time ) {
								if ( isSet($event_end_datetime[1]) ) {
									$eventinfobox .= '<li>';
									$eventinfobox .= $event_end_time;
									$eventinfobox .= '</li>';
								}
							}

							$eventinfobox .= '</ul>';
						}
						$eventinfobox .= '</div>';

					}

					if ($where=='true') {

						if ($event_venue_name<>"") {
							$eventinfobox .= '<div class="'.$events_sep_class.' events-details-block">';
								$eventinfobox .= '<h3 class="event-heading"><i class="event-icon event-icon-spaced '.$where_icon.'"></i><span>'.$where_text.'</span></h3>';
								$eventinfobox .= '<ul class="event-details entry-content event-address">';

								$address_format = imaginem_codepack_get_option_data( 'events_address_format' );

								$display_address_format = array(
									'venue'  => $event_venue_name,
									'street' => $event_venue_street,
									'state'  => $event_venue_state,
									'zip'    => $event_venue_postal
								);

								$address_order = array( 'venue','street','state','zip' );

								if ( 'sszv' === $address_format ) {
									$address_order = array( 'street','state','zip','venue' );
								}
								if ( 'zvss' === $address_format ) {
									$address_order = array( 'zip','venue','street','state' );
								}

								$add_seperator = false;

								$eventinfobox .= '<li>';
								foreach ($address_order as $value) {

									$got_value = $display_address_format[$value];

									if ( isset($got_value) && '' !== $got_value ) {
										if ( $add_seperator ) {
											$eventinfobox .= '<span class="event-address-sep"> , </span>';
										}
										$eventinfobox .= '<span class='.esc_attr( $value ).'>'. $got_value .'</span>';
										$add_seperator = true;
									}
								}
								$eventinfobox .= '</li>';

								$event_country = imaginem_codepack_country_list("display",$event_venue_country);
								if ($event_country<>"Choose Country") {
									$eventinfobox .= '<li>'.$event_country.'</li>';
								}
								$eventinfobox .= '<li>'.$event_venue_phone.'</li>';
								$eventinfobox .= '<li><a href="'.esc_url($event_venue_website).'">'.$event_venue_website.'</a></li>';

								$eventinfobox .= '</ul>';
							$eventinfobox .= '</div>';
						}
					}

					if ($cost=='true') {
						if ($event_venue_cost<>"") {
							$eventinfobox .= '<div class="'.$events_sep_class.' events-details-block">';
							$eventinfobox .= '<h3 class="event-heading"><i class="event-icon event-icon-spaced '.$cost_icon.'"></i><span>'.$cost_text.'</span></h3>';
							$eventinfobox .= '<ul class="event-details event-price">';

							$eventinfobox .= '<li>'. $event_venue_currency . " " . $event_venue_cost .'</li>';
							$eventinfobox .= '</ul>';
							$eventinfobox .= '</div>';
						}
					}

					if ($capacity=='true') {
						if ( isset( $event_venue_capacity ) && $event_venue_capacity<>"") {
							$eventinfobox .= '<div class="'.$events_sep_class.' events-details-block">';
							$eventinfobox .= '<h3 class="event-heading"><i class="event-icon event-icon-spaced '.$capacity_icon.'"></i><span>'.$capacity_text.'</span></h3>';
							$eventinfobox .= '<ul class="event-details event-capacity">';

							$eventinfobox .= '<li>' . $event_venue_capacity . '</li>';
							$eventinfobox .= '</ul>';

							if ($remaining=='true') {
								if ($event_venue_remaining<>"") {
									$eventinfobox .= '<div class="event-capacity-remaining-wrap">';
									$eventinfobox .= '<h3 class="event-heading"><i class="event-icon event-icon-spaced '.$remaining_icon.'"></i><span>'.$remaining_text.'</span></h3>';
									$eventinfobox .= '<ul class="event-details event-capacity-remaining">';
		
									$eventinfobox .= '<li>' . $event_venue_remaining . '</li>';
									$eventinfobox .= '</ul>';
									$eventinfobox .= '</div>';
								}
							}
							$eventinfobox .= '</div>';
						}
					}


				$eventinfobox .= '</div>';
				}
			if ($status=='true') {
				switch ($event_notice) {
					case 'postponed':
						$eventinfobox .= '<div class="event-status-wrap"><div class="event-status">'.imaginem_codepack_get_option_data('event_postponed').'</div></div>';
						break;
					case 'cancelled':
						$eventinfobox .= '<div class="event-status-wrap"><div class="event-status">'.imaginem_codepack_get_option_data('event_cancelled').'</div></div>';
						break;
					case 'pastevent':
						$eventinfobox .= '<div class="event-status-wrap"><div class="event-status">'.imaginem_codepack_get_option_data('event_pastevent').'</div></div>';
						break;
					case 'fullevent':
						$eventinfobox .= '<div class="event-status-wrap"><div class="event-status">'.imaginem_codepack_get_option_data('event_fullevent').'</div></div>';
						break;
					default:
						break;
				}
			}
		$eventinfobox .= '</div>';
	}
	return $eventinfobox;

}
add_shortcode('eventinfobox', 'mtheme_eventinfobox');
function mtheme_display_page_sidebar( $atts, $content = null ) {
   extract( shortcode_atts( array(
   		'sidebar_choice' => ''
      ), $atts ) );
	if ( is_active_sidebar( $sidebar_choice ) ) {
		dynamic_sidebar($sidebar_choice);
	}
}
add_shortcode('sidebar', 'mtheme_display_page_sidebar');
function mtheme_fullpage( $atts, $content = null ) {
   extract( shortcode_atts( array(
   		'id' => '',
   		'class' => '',
		'border_style' => 'none',
		'border_width' => '1',
		'border_color' => '#eeeeee',
		'background_image' => '',
		'background_color' => '',
		'top' =>'',
		'text_align' => 'center',
		'bottom' => '',
		'textcolor' => ''
      ), $atts ) );

	global $fullblockid;
	if (!isSet($fullblockid)) {
		$fullblockid=1;
	} else {
		$fullblockid++;
	}
	if ($id !='' ) {
		$display_id = $id;
	} else {
		$display_id = get_the_id() . '-' . $fullblockid;
	}

	$fullpage='';
	$textclass='';
   	if ($textcolor=="bright") { $textclass="textbright"; }
	$fullpage .= '<div class="fullpage-block fullpage-block-'. $display_id . ' ' . $class . '" ';
	$fullpage .= ' style="background-color: '.$background_color.'; background-image: url('.$background_image.');';
	if ($border_style!="none") {
		$fullpage .= ' border-style:'.$border_style.'; border-color:'.$border_color.'; border-width:'.$border_width.'px; border-left:none; border-right:none;';
	}
	$fullpage .= ' padding-top:'.$top.'px; padding-bottom:'.$bottom.'px;';
	$fullpage .= ' background-attachment:fixed; background-position:50% 50%; background-repeat: repeat;">';
	$fullpage .= '<div class="fullpage-item '.$textclass.'">'.do_shortcode($content).'</div>';
	$fullpage .= '</div>';
	return $fullpage;
}
add_shortcode('fullpageblock', 'mtheme_fullpage');
function mtheme_Hr( $atts, $content = null ) {
   return '<div class="clearfix"></div><div class="hrule"></div>';
}
add_shortcode('hr', 'mtheme_Hr');

//Hrule [hr]
function mtheme_Hr_top( $atts, $content = null ) {
   return '<div class="clearfix"></div><div class="hrule top"><a href="#">'.__('Top','mtheme').'</a></div>';
}
add_shortcode('hr_top', 'mtheme_Hr_top');

//Hrule [hr]
function mtheme_Hr_padding( $atts, $content = null ) {
   return '<div class="clearfix"></div><div class="hr_padding"></div>';
}
add_shortcode('hr_padding', 'mtheme_Hr_padding');
/**
 * List shortcode.
 *
 * @ [list type=(check,star,note,play,bullet)]
 */
function mtheme_List( $atts, $content = null ) {
   extract( shortcode_atts( array(
	  'icon' => 'fa fa-check',
	  'content_richtext' => '',
	  'color' => '#EC3939'
      ), $atts ) );

   	$content_richtext = html_entity_decode($content_richtext);
   	$checklist = '<div class="checklist">';
	//$checklist .= str_replace('<ul>', '<ul class="icons-ul">', $content);
	$content_richtext = str_replace('</li>', '</span></li>', $content_richtext);
	$checklist .= str_replace('<li>', '<li class="animation-standby animated fadeInUpSlow"><i style="color:'.$color.';" class="'.$icon.'"></i><span>', $content_richtext);
	$checklist .= '</div>';

	return $checklist;
}
add_shortcode('checklist', 'mtheme_List');

/**
 * Notice / Alerts
 */
function mtheme_Alert( $atts, $content = null ) {

   extract( shortcode_atts( array(
	  'type' => 'yellow',
	  'icon' => 'feather-icon-cross'
      ), $atts ) );

	$notice ='<div class="noticebox info_'.$type.'">';
	$notice .= '<i class="'.$icon.'"></i>';
	$notice .= '<div class="notice-text">'. trim( do_shortcode( wpautop( html_entity_decode($content) ) ) ).'</div>';
	$notice .= '</div>';
	
	return $notice;
		
}
add_shortcode('alert', 'mtheme_Alert');


//DropCaps [dropcap1] letter [/dropcap1]
function mtheme_DropCap( $atts, $content = null ) {
   extract( shortcode_atts( array(
      'type' => '1',
      ), $atts ) );
   return '<span class="mtheme-dropcap dropcap'.$type.'">' . $content . '</span>';
}
add_shortcode('dropcap', 'mtheme_DropCap');

//Button [button link="yourlink.html"] text [/button]
function mtheme_Button( $atts, $content = null ) {

   extract( shortcode_atts( array(
      'link' => '#',
      'button_icon' => '',
	  'target' => '',
	  'button_align' => '',
	  'button_color' => '',
	  'animated' => 'animation-standby animated fadeIn',
	  'size'=> 'normal',
      ), $atts ) );

   $uniqueID = uniqid();
	  
	if ($target=="_blank") {
		$target=' target="_blank"';
	} else {
		$target='';
	}
	if ($button_align=="{{button_align}}") { $button_align=''; }
	if ($button_align=='') {
		$button_align_style='';
	} else {
		$button_align_style= ' style="text-align:'.$button_align.';"';
	}

	$button_style_tag = '';
	$custom_style_added_class = '';
	$data_tag = '';
	if (isSet($button_color) && !empty($button_color) ) {
		$button_style_tag = 'style="border-color: '.$button_color.'; color: '.$button_color.';" ';
		$custom_style_added_class = ' button-has-a-color';
		$data_tag = 'data-backgroundafter="'.$button_color.'" data-buttonid="'.$uniqueID.'" ';
	}

	$button = '<div class="'.$animated.' button-shortcode button-shortcode-'.$uniqueID.'"'.$button_align_style.'>';
	$button .= '<a href="'.esc_url($link).'" ' . $target . '>';
	$button .= '<div '.$button_style_tag.$data_tag.' class="mtheme-button'.$custom_style_added_class.'">';
	if (isSet($button_icon) && !empty($button_icon) ) {
		$button .= '<span class="button-icon"><i class="'.$button_icon.'"></i></span>';
	}
	$button .= trim($content);
	$button .= '</div>';
	$button .= '</a>';
	$button .= '</div>';
	
   return $button;
}
add_shortcode('button', 'mtheme_Button');

//post list [postlist cat=3 num=5]
function mtheme_post_list($atts, $content = null) {
        extract(shortcode_atts(array(
                "num" => '5',
                "cat" => ''
        ), $atts));
        global $post;
        $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=post_date&category='.$cat);
        $retour='<div class="postlist"><ul>';
        foreach($myposts as $post) :
                setup_postdata($post);
             $retour.='<li><a href="'.get_permalink().'">'.the_title("","",false).'</a></li>';
        endforeach;
        $retour.='</ul></div> ';
		wp_reset_query();
        return $retour;
}
add_shortcode("posts", "mtheme_post_list");

/**
 * Usage: [pagelist child_of=x] x = id of the parent page, default = 0
 * Example: [pagelist child_of=12]
**/

function mtheme_pagelist($atts, $content = null) {
        extract(shortcode_atts(array(
                "childof" => ''
        ), $atts));
 $output = wp_list_pages('echo=0&child_of='.$childof.'&sort_column=menu_order&title_li=');
 return '<div class="postlist"><ul>'.$output.'</ul></div>';
}
add_shortcode('pages', 'mtheme_pagelist');
//Google Maps Shortcode
function mtheme_do_googleMaps($atts, $content = null) {
   extract(shortcode_atts(array(
      "width" => '460',
      "height" => '480',
      "src" => ''
   ), $atts));
   return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed" ></iframe>';
}
add_shortcode("googlemap", "mtheme_do_googleMaps");

//Clear [clear]
function mtheme_Clear( $atts, $content = null ) {
   return '<div class="clear"></div>';
}
add_shortcode('clear', 'mtheme_Clear');

//Column2 [column2] text [/column2]
function mtheme_Column( $atts, $content = null ) {
   extract( shortcode_atts( array(
	  'span' => '2',
	  'last' => 'false'
      ), $atts ) );
   $lastclass='';
   $lastshortcodeclass='';
   if ($last=="yes") {
   		$lastclass = ' clearfix';
   		$lastshortcodeclass = ' last-shortcode-column';
   }
   $column = '<div class="mtheme-supercell">';
   $column .= '<div class="shortcode-column span'.$span.$lastshortcodeclass.$lastclass.'">';
   $column .= do_shortcode(trim($content));
   $column .= '</div>';
   $column .= '</div>';
   return $column;
}
add_shortcode('column', 'mtheme_Column');

//Toggle [toggle] text [/toggle]
function mtheme_Toggle( $atts, $content = null ) {

  	extract(shortcode_atts(array(
		"title" => 'Toggle',
		"state" => 'closed'
	), $atts));

  	$toggle_status="";
	if ($state=="open") { 
		$toggle_status="active";
	}
	  
	$toggle	=  '<div class="toggle-shortcode-wrap clearfix">';
	$toggle	.=	'<h2 class="toggle-shortcode '.$toggle_status.'">' . $title . '</h2>';
	$toggle .=	'<div class="toggle-container toggle-display-'.$state.'">';
	$toggle .=	wpautop( html_entity_decode($content) );
	$toggle	.=	'</div>';
	$toggle	.=	'</div>';
	//$toggle = do_shortcode($toggle);

	return $toggle;
}
add_shortcode('toggle', 'mtheme_Toggle');


//Highlight [highlight] text [/highlight]
function mtheme_Highlight( $atts, $content = null ) {
   return '<span class="highlight">' . do_shortcode($content) . '</span>';
}
add_shortcode('highlight', 'mtheme_Highlight');

//Big Italic
function mtheme_big_italic( $atts, $content = null ) {
		$big_italic = '<div class="big-italic">' . do_shortcode($content) . '</div>';

   return $big_italic;
}
add_shortcode('big_italic', 'mtheme_big_italic');

//Pullquote Right [pullquote_right] text [/pullquote_right]
function mtheme_Pullquote( $atts, $content = null ) {

   extract( shortcode_atts( array(
	  'align' => 'center',
	  'animated'=> 'none'
      ), $atts ) );

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}

   $pullquote_left_symbol ='';
   $pullquote_right_symbol ='';

	$pullquote = '<div class="pullquote-'.$align.' '.$animated.'">' . do_shortcode( $pullquote_left_symbol . wpautop( html_entity_decode($content) ) . $pullquote_right_symbol ) . '</div>';

   return $pullquote;
}
add_shortcode('pullquote', 'mtheme_Pullquote');

/* Lightbox */
function mtheme_Lightbox( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"lightbox_url" => '',
		"lightbox_video_url" => '',
		"thumbnail_url" => '',
		"title" => '',
		'align' => ''
	), $atts));

$lightbox_type = "lightbox-image";

$uniqueID=get_the_id()."-".dechex(mt_rand(1,65535));

if ($lightbox_video_url<>"") {
	$lightbox_url=$lightbox_video_url;
	$lightbox_type = "lightbox-video";
}
$lightbox = '<div class="lightbox-shortcode gridblock-grid-element gridblock-element-inner">';
			$lightbox .= imaginem_codepack_activate_lightbox (
					$lightbox_type="default",
					$ID=get_the_id(),
					$predefined=$lightbox_url,
					$mediatype="image",
					$imagetitle=get_the_title(),
					$class="column-gridblock-lightbox ".$lightbox_type,
					$set="lightbox-shortcode-".$uniqueID,
					$data_name="default",
					$thumbnail_url,
					$imageDataID=false
				);
	$lightbox .= '<div class="gridblock-background-hover">
		<div class="gridblock-links-wrap">';
				$lightbox .= '<span class="column-gridblock-icon"><span class="hover-icon-effect">
					<i class="feather-icon-maximize"></i>
				</span>
				</span>
		</div>
	</div>
	<img class="displayed-image" alt="thumbnail-image" src="'.$thumbnail_url.'">
	</a>
</div>';

   return $lightbox;// . $imagesrc . $after;
}
add_shortcode('lightbox', 'mtheme_Lightbox');

//Picture frame [pictureframe]
function mtheme_PictureFrame( $atts, $content = null ) {
	extract(shortcode_atts(array(
		"width" => '150',
		"height" => '',
		"zoom" => '',
		"title" => 'Untitled',
		"align" => 'none',
		"link" => 'none',
		"lightbox" => 'true',
		"image" => ''
	), $atts));
	
	$quality=MTHEME_IMAGE_QUALITY;
	$class="none";
	$before='';
	$after='';
	$fade='';
	$img_align='';
	
	
	if ( $height=="" || $height==0 ) { $height="auto"; } else { $height=$height . "px"; }
	$width=$width . "px";
	
	if ($align=="left") {$img_align="img-align-left";}
	if ($align=="right") {$img_align="img-align-right";}
	if ($align=="center") {$img_align="img-align-center";}
	if ($link<>"") {
		$before='<a title="'.$title.'" href="'. $link . '">';
		$after='</a>';
		$fade="portfolio-fadein";
		}
	if ($lightbox=="true") {
		$before='<a data-lightbox="magnific-image" title="'.$title.'" href="'. $image . '">';
		$after='</a>';
		$fade="pictureframe-image";
		}
	
	$class="pictureframe " . $img_align . " " . $fade;
		
	$imagesrc = '<img src="'. $image . '" class="'.$class.'" style="width:' . $width .'; height:'. $height .'" alt="thumbnail" />';


   return $before . $imagesrc . $after;
}
add_shortcode('pictureframe', 'mtheme_PictureFrame');

/*
* jQuery UI - Tabs shortcode
*/
global $olt_tab_shortcode_count, $olt_tab_shortcode_tabs;
$olt_tab_shortcode_count = 0;
function mtheme_display_shortcode_tab($atts,$content)
{
global $olt_tab_shortcode_count, $post, $olt_tab_shortcode_tabs;
extract(shortcode_atts(array(
'title' => null,
'class' => null,
), $atts));

if (!is_array($olt_tab_shortcode_tabs)) {
	$olt_tab_shortcode_tabs=array();
}

ob_start();

if($title):
$olt_tab_shortcode_tabs[] = array(
"title" => $title,
"id" => esc_attr($title)."-".$olt_tab_shortcode_count,
"class" => $class
);
?><div id="<?php echo sanitize_title_with_dashes($title)."-".$olt_tab_shortcode_count; ?>" >
<div class="tab-contents">
<?php
$content = wpautop(html_entity_decode($content));
echo do_shortcode( $content );
?>
</div>
</div><?php
elseif($post->post_title):
$olt_tab_shortcode_tabs[] = array(
"title" => $post->post_title,
"id" => esc_attr($post->post_title)."-".$olt_tab_shortcode_count,
"class" =>$class
);
?><div id="<?php echo sanitize_title_with_dashes($post->post_title)."-".$olt_tab_shortcode_count; ?>" >
<?php
$content = html_entity_decode($content);
echo do_shortcode( $content );
?>
</div><?php
else:
?>
<span style="color:red">Please enter a title attribute like [tab title="title name"]tab content[tab]</span>
<?php
endif;
$olt_tab_shortcode_count++;
return ob_get_clean();
}

function mtheme_display_shortcode_tabs( $attr, $content )
{
// wordpress function
extract(shortcode_atts(array(
'type' => "horizontal"
), $attr));

global $olt_tab_shortcode_count,$post, $olt_tab_shortcode_tabs;
$olt_tab_shortcode_tabs="";
$vertical_tabs = "";
if( isset( $attr['vertical_tabs']) ):
$vertical_tabs = ( (bool)$attr['vertical_tabs'] ? "vertical-tabs": "");
unset($attr['vertical_tabs']);
endif;

// $attr['disabled'] = (bool)$attr['disabled'];
if ( isset($attr['collapsible']) ) $attr['collapsible'] = (bool)$attr['collapsible'];
$query_atts = shortcode_atts(
array(
'collapsible' => false,
'event' =>'click'
), $attr);
// there might be a better way of doing this
$id = "random-tab-id-".rand(0,1000);

ob_start();
?>
<div id="<?php echo esc_attr($id); ?>" class="clearfix tabs-shortcode <?php echo esc_attr($vertical_tabs); ?>"><?php

$content = (substr($content,0,6) =="<br />" ? substr( $content,6 ): $content);
$content = str_replace("]<br />","]",$content);

$str = do_shortcode( $content ); ?>
<?php
$count_tab_counter = 0;
foreach( $olt_tab_shortcode_tabs as $li_tab ):
$count_tab_counter++;
endforeach;
if ($type!="vertical") {
	$css_tab_length= 100/$count_tab_counter;
}
if (isSet($css_tab_length)) {
	if ($css_tab_length==0) {
		$css_tab_length = '100';
	}
	$css_tab_length = 'width:' . $css_tab_length . '%';
} else {
	$css_tab_length = "";
}
?>
<ul>
<?php
$tab_counter=0;
foreach( $olt_tab_shortcode_tabs as $li_tab ):
$tab_counter++;
?><li style="<?php echo $css_tab_length; ?>" <?php if( $li_tab['class']): ?> class='<?php echo esc_attr($li_tab['class']);?>' <?php endif; ?> ><a href="#<?php echo sanitize_title_with_dashes($li_tab['id']); ?>"><?php echo $li_tab['title']; ?></a></li><?php
endforeach;



?></ul><?php echo $str; ?></div>
<script type="text/javascript"> /* <![CDATA[ */
jQuery(document).ready(function($) {
	<?php
	if ( $type != "vertical" ) {
	?>
	jQuery("#<?php echo $id ?>").tabs(<?php echo json_encode($query_atts); ?> );
	<?php
	} else {
	?>
	jQuery("#<?php echo $id ?>").tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	jQuery("#<?php echo $id ?> li").removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	<?php
	}
	?>
});
/* ]]> */
</script>

<?php
$post_content = ob_get_clean();

return str_replace("\r\n", '',$post_content);
}

function olt_tabs_shortcode_init() {
    
    add_shortcode('tab', 'mtheme_display_shortcode_tab'); // Individual tab
    add_shortcode('tabs', 'mtheme_display_shortcode_tabs'); // The shell
}

add_action('init','olt_tabs_shortcode_init');

global $olt_accordion_shortcode_count;
$olt_accordion_shortcode_count = 0;

function mtheme_accordiontab($atts,$content)
{
	global $olt_accordion_shortcode_count,$post;
	extract(shortcode_atts(array(
		'title' => null,
		'class' => null,
	), $atts));
	
	ob_start();
	
	if($title):
		?>
		<h3 class="animation-standby animated fadeIn"><a href="#<?php echo esc_attr($title)."-".$olt_accordion_shortcode_count; ?>"><?php echo $title; ?></a></h3>
		<div class="accordian-shortcode-content <?php echo $class; ?>" >
			<?php
			$content = wpautop(html_entity_decode($content) );
			echo do_shortcode( $content );
			?>
		</div>
		<?php
	elseif($post->post_title):
	?>
		<div id="<?php echo esc_attr($post->post_title)."-".$olt_accordion_shortcode_count; ?>" >
			<?php
			$content = html_entity_decode($content);
			echo do_shortcode( $content );
			?>
		</div>
	<?php
	else:
	?>
		<span style="color:red">Please enter a title attribute like [accordion title="title name"]accordion content[accordion]</span>
		<?php 	
	endif;
	$olt_accordion_shortcode_count++;
	return ob_get_clean();
}

function mtheme_accordiontabs($attr,$content)
{
	// wordpress function 
	global $olt_accordion_shortcode_count,$post;
	
	if (isset($attr['autoHeight'])) $attr['autoHeight'] =  (bool)$attr['autoHeight'];
	if (isset($attr['disabled'])) $attr['disabled'] =  (bool)$attr['disabled'];
	if ($attr['active']==-1) {
		$attr['active'] =  '-1';
	} else {
		$attr['active'] =  (int)$attr['active'];
	}
	if (isset($attr['clearStyle'])) $attr['clearStyle'] = (bool)$attr['clearStyle'];
	if (isset($attr['collapsible'])) $attr['collapsible'] = (bool)$attr['collapsible'];
	if (isset($attr['fillSpace'])) $attr['fillSpace']= (bool)$attr['fillSpace'];
	$query_atts = shortcode_atts(
		array(
			'heightStyle' => 'content',
			'autoHeight' => false, 
			'disabled' => false,
			'active'	=> 0,
			'animated' => 'slide',
			'clearStyle' => false,
			'collapsible' => true,
			'event'=>'click',
			'fillSpace'=>false
		), $attr);
	
	// there might be a better way of doing this
	$id = "random-accordion-id-".rand(0,1000);
	
	$content = (substr($content,0,6) =="<br />" ? substr($content,6): $content);
	$content = str_replace("]<br />","]",$content);
	ob_start();
	?>
	<div id="<?php echo esc_attr($id); ?>" data-accordionid="<?php echo esc_attr($id); ?>" data-activetab="<?php echo esc_attr($attr['active']); ?>" class="mtheme-accordion-detect wp-accordion accordions-shortcode">
		<?php echo do_shortcode( $content ); ?> 
	</div>
	<?php
	$post_content = ob_get_clean();
	
	return str_replace("\r\n", '',$post_content);
}

function olt_accordions_shortcode_init() {
    
    add_shortcode('accordion', 'mtheme_accordiontab'); // Individual accordion
    add_shortcode('accordions', 'mtheme_accordiontabs'); // The shell
	
}
add_action('init','olt_accordions_shortcode_init');

function mtheme_progressbar_group($atts, $content = null) {
	$progressbar = '<div class="progressbar-wrap">';
	$progressbar .= do_shortcode($content);
	$progressbar .= '</div>';
	return $progressbar;
}
add_shortcode('progress_group', 'mtheme_progressbar_group');
// Progress Bars
function mtheme_progressbar($atts, $content = null) {
	extract(shortcode_atts(array(
		'color' => null,
		'percentage' => '50',
		'animated' => 'none',
		'unit' => '%'
	), $atts));


	$rgba_css         = '';
	$background_color = '';
	$rgb              = mtheme_shortcodefunction_hex_to_rgb($color);
	
	if ( isset( $rgb[0] ) ) {
		$rgb_css = $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2];
		$rgba_css = 'rgba(' . $rgb_css . ',0.1);';
		$background_color = "background: rgba(".$rgb_css.",1);";
	}

	$skill ='<div class="progress-element">';
	$skill .='<h3 class="progressbar-title"><span class="'.$animated.' skill-bar-percent">'.$atts['percentage'].$atts['unit'].'</span>'.$atts['title'].'</h3>';
	$skill .='<div class="skillbar">';
	$skill .='<div class="skillbar-bar" data-percentage="'.$atts['percentage'].'"></div>';
	$skill .='</div>';
	$skill .='</div>';
	return $skill;
}
add_shortcode('progressbar', 'mtheme_progressbar');

// Counter
function mtheme_counter($atts, $content = null) {
	extract(shortcode_atts(array(
		"size" => '150',
		"title" => '',
		"animated" => 'none',
		"percentage" => '90',
		"textsize" => '32',
		"bgcolor" => '#f0f0f0',
		"fgcolor" => '#EC3939',
		"donutwidth" => '3'
	), $atts));
	if ($percentage>100) {
		$percentage="100";
	}

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}
	$uniqurePageID=get_the_id()."-".dechex(mt_rand(1,65535));
	$counter = '';
	$counter .= '<div class="donutcounter-wrap '.$animated.' service-column">';
	$counter .='<div class="donutcounter-item" id="donutchart-'.$uniqurePageID.'" data-percent="'.$percentage.'"></div>';
	$counter .= '<div class="service-content">';
	$counter .= '<h4>'.$title.'</h4>';
	$counter .= '<div class="service-details">'. wpautop( html_entity_decode($content) ).'</div>';
	$counter .= '</div>';
	$counter .= '</div>';
	$counter .="
<script>
jQuery(document).ready(function($){
	$('#donutchart-".$uniqurePageID."').waypoint(function() {
	$('#donutchart-".$uniqurePageID."').donutchart({
		'size': '".$size."',
		'donutwidth': ".$donutwidth.",
		'fgColor' : '".$fgcolor."',
		'bgColor' : '".$bgcolor."',
		'textsize': '".$textsize."'
	});
	$('#donutchart-".$uniqurePageID."').donutchart('animate');
	}, { offset: 'bottom-in-view',triggerOnce: true });
});
</script>";
	return $counter;
}
add_shortcode('counter', 'mtheme_counter');

/* Font Awesome */
function mtheme_fontawesome($atts, $content = null) {
	extract( shortcode_atts( array(
        'class' => 'fa fa-wrench'
    ), $atts ));
    $fontawesome = '<i class="'.$class.'"></i>';
    return $fontawesome;
}
add_shortcode('fontawesome', 'mtheme_fontawesome');

// Dividers
function mtheme_divider($atts, $content = null) {
	extract(shortcode_atts(array(
		'title' => '',
		'type' => '',
		'textcolor' => '',
		'textbackground' => '',
		'responsive_top' => 'no',
		'style' => 'blank',
		'top' => '30',
		'bottom' => ''
	), $atts));
	$divider = '';
	$stylecss ='padding-top:'.$top.'px;';
	if ( $type == "{{type}}") {
		$type = '';
	}
	if ( $type == "default") {
		$type = 'default-divider ';
	}
	if ( $type == "dark") {
		$type = 'dark-divider ';
	}
	if ( $type == "bright") {
		$type = 'bright-divider ';
	}
	$responsive_mode = '';
	if ( $responsive_top == "yes") {
		$responsive_mode = ' divider-responsive';
	}
	$titlespan ='';
	$divider .= '<div class="clearfix divider-wrap divider-common '.$type.' divider-'.$style.'">'.$titlespan.'</div>';
	
	return $divider;
}
add_shortcode('divider', 'mtheme_divider');

// ###################################
// Headings
// ###################################
function mtheme_heading($atts, $content = null) {
	shortcode_atts(array(
		'title' => '',
		'descstyle' => 'none',
		'headingstyle' => 'default',
		'titlebottomspace' => '',
		'url_nofollow' => '',
		'url_target' => '',
		'two_url_nofollow' => '',
		'twp_url_target' => '',
		'titlecolor' => '',
		'subtitlecolor' => '',
		'subtitle' => '',
		'button_url' => '',
		'button_text' => '',
		'button_two_url' => '',
		'button_two_text' => '',
		'description' => '',
		'size' => '3',
	), $atts);

	$title = $atts['title'];
	$headingstyle = $atts['headingstyle'];
	if ( isset( $atts['url_nofollow']) ) {
		$url_nofollow = $atts['url_nofollow'];
	} else {
		$url_nofollow = '';
	}
	if ( isset( $atts['url_target']) ) {
		$url_target = $atts['url_target'];
	} else {
		$url_target = '';
	}
	
	$subtitle = $atts['subtitle'];

	if ( isset( $atts['button_url']) ) {
		$button_url = $atts['button_url'];
	} else {
		$button_url = '';
	}
	if ( isset( $atts['button_text']) ) {
		$button_text = $atts['button_text'];
	} else {
		$button_text = '';
	}
	if ( isset( $atts['two_url_nofollow']) ) {
		$two_url_nofollow = $atts['two_url_nofollow'];
	} else {
		$two_url_nofollow = '';
	}
	if ( isset( $atts['two_url_target']) ) {
		$two_url_target = $atts['two_url_target'];
	} else {
		$two_url_target = '';
	}
	if ( isset( $atts['button_two_url']) ) {
		$button_two_url = $atts['button_two_url'];
	} else {
		$button_two_url = '';
	}
	if ( isset( $atts['button_two_text']) ) {
		$button_two_text = $atts['button_two_text'];
	} else {
		$button_two_text = '';
	}
	if ( isset( $atts['description']) ) {
		$description = $atts['description'];
	} else {
		$description = '';
	}
	$size = $atts['size'];

	$divider = '';
	$fontsize_tag = '';
	$title_color_tag = '';
	$textalign='';
	$animated='';
	$title_subheading='';

	$target = '';
	if ($url_target) {
		$target .=' target="_blank"';
	}
	if ($url_nofollow) {
		$target .=' rel="nofollow"';
	}

	$two_target = '';
	if ($two_url_target) {
		$two_target .=' target="_blank"';
	}
	if ($two_url_nofollow) {
		$two_target .=' rel="nofollow"';
	}

	$heading = '<div class="section-heading headingstyle-'.esc_attr($headingstyle).'">';
	$title_heading = '';
	if ( isSet($title) && $title <> "" ) {
		$title_heading = '<h'. $size .' class="entry-title section-title">'.html_entity_decode($title).'</h'.esc_attr($size).'>';
	}
	if ( isSet($subtitle) && $subtitle<>"") {
		$title_subheading = '<h5 class="entry-sub-title section-sub-title"><span>'.html_entity_decode($subtitle).'</span></h5>';
	}
	if ( $headingstyle=="subtitle" ) {
		$heading .= $title_subheading . $title_heading;
	} else {
		$heading .= $title_heading . $title_subheading;
	}
	if ( trim($description) ) {
		$heading .= '<div class="section-description clearfix"><div class="section-description-inner">'.wpautop( html_entity_decode($description) ).'</div></div>';
	}
	if ( $button_text<>"" && $button_url<>"" ) {
		$heading .= '<div class="section-button">';
		$heading .= '<a class="button-one-link" '.$target.' href="'.esc_url($button_url).'"><div class="button-one mtheme-button">';
		$heading .= $button_text;
		$heading .= '</div></a>';
		if ( '' !== $button_two_text ) {
			$heading .= '<a class="button-two-link" '.$two_target.' href="'.esc_url($button_two_url).'"><div class="button-two mtheme-button">';
			$heading .= $button_two_text;
			$heading .= '</div></a>';
		}
		$heading .= '</div>';
	}
	$heading .= '</div>';
	return $heading;
}
add_shortcode('heading', 'mtheme_heading');
// ###################################
// Headings
// ###################################
function builder_button($atts, $content = null) {
	shortcode_atts(array(
		'url_nofollow' => '',
		'url_target' => '',
		'button_url' => '',
		'button_text' => '',
	), $atts);

	$url_nofollow = $atts['url_nofollow'];
	$url_target = $atts['url_target'];
	$button_url = $atts['button_url'];
	$button_text = $atts['button_text'];

	$target = '';
	if ($url_target) {
		$target .=' target="_blank"';
	}
	if ($url_nofollow) {
		$target .=' rel="nofollow"';
	}


	$heading = '<div class="section-button">';

	if ( $button_text<>"" && $button_url<>"" ) {
		$heading .= '<div class="section-button">';
		$heading .= '<a class="button-one-link" '.$target.' href="'.esc_url($button_url).'"><div class="button-one mtheme-button">';
		$heading .= $button_text;
		$heading .= '</div></a>';
		$heading .= '</div>';
	}
	$heading .= '</div>';
	return $heading;
}
add_shortcode('theme_button', 'builder_button');

// ###################################
// Post Heading
// ###################################
function mtheme_postheading($atts, $content = null) {
	extract(shortcode_atts(array(
		'title' => '',
		'title_subheading' => '',
		'descstyle' => 'none',
		'headingstyle' => 'default',
		'customfontsize' => '',
		'customfontweight' => '',
		'show_subtitle' => '',
		'show_date' => '',
		'titlebottomspace' => '',
		'url_nofollow' => '',
		'url_target' => '',
		'titlecolor' => '',
		'subtitlecolor' => '',
		'subtitle' => '',
		'align' => '',
		'description' => '',
		'animated' => 'none',
		'size' => '3',
		'style' => 'line',
		'top' => '40',
		'bottom' => '0',
		'marginbottom' => '60',
		'button' => '',
		'width' => '',
		'button_link' => ''
	), $atts));

	$divider = '';
	$fontsize_tag = '';
	$title_color_tag = '';
	
	if( imaginem_codepack_is_hex_color($titlecolor) ) {
		$title_color_tag ='color:'.$titlecolor.';';
	}
	$subtitle_style_tag = '';
	if( imaginem_codepack_is_hex_color($subtitlecolor) ) {
		$subtitle_style_tag ='color:'.$subtitlecolor.';';
	}

	if ($customfontsize<>"") {
		if ( is_numeric($customfontsize) ) {
			$fontsize_tag = 'font-size:'.$customfontsize.'px; line-height: 1.2;';
		}
	}
	$fontweight_tag = '';
	if ($customfontweight<>"") {
		if ( is_numeric($customfontweight) ) {
			$fontweight_tag = 'font-weight:'.$customfontweight.';';
		}
	}
	$fontbottomspace_tag = '';
	if ($titlebottomspace<>"") {
		if ( is_numeric($titlebottomspace) ) {
			$fontbottomspace_tag = 'margin-bottom:'.$titlebottomspace.'px;';
		}
	}

	$textalign='';

	if (isSet($top) && trim($top) > 0 ) {
		$top = $top . 'px';
	}
	if (isSet($bottom) && trim($bottom) > 0 ) {
		$bottom = $bottom . 'px';
	}
	if (isSet($width) && trim($width) > 0 ) {
		$width = 'width:' . $width . '%;';
	} else {
		$width='';
	}

	$heading = '<div class="post-heading section-heading headingstyle-'.esc_attr($headingstyle).'">';
	$title_heading = '<h'. $size .' class="entry-title section-title">'.get_the_title().'</h'.$size.'>';

	$title_subheading = '';

	if ( 'yes' == $show_subtitle ) {
		$post_type = get_post_type();
		switch ($post_type) {
			case 'post':
				$category = get_the_category();
				$the_category = $category[0]->cat_name;
				break;
			case 'events':
				$category =  get_the_terms( get_the_ID(), 'eventsection' );
				$the_category = $category[0]->name;
				break;
			case 'portfolio':
				$category =  get_the_terms( get_the_ID(), 'worktypes' );
				$the_category = $category[0]->name;
				break;
			
			default:
				# code...
				break;
		}
		
		if ( '' !== $the_category ) {
			$title_subheading = '<h5 class="entry-sub-title section-sub-title"><span>'. $the_category .'</span></h5>';
		}
		
	}

	$heading .= $title_subheading . $title_heading;
	
	if ( 'yes' == $show_date ) {
		$title_description = '<div class="section-description section-date clearfix"><div class="section-description-inner">'.get_the_date().'</div></div>';
		$heading .= $title_description;
	}

	$heading .= '</div>';
	return $heading;

}
add_shortcode('postheading', 'mtheme_postheading');

// ###################################
// Headings Tag
// ###################################
function mtheme_heading_tag($atts) {
	extract(shortcode_atts(array(
		'title' => '',
		'subtitle' => '',
		'align' => '',
		'animated' => 'none',
		'size' => '3',
		'top' => '40',
		'bottom' => '0',
	), $atts));
	$divider = '';

	$textalign='';
	if ($align<>'') {
		$textalign = ' style="text-align:'.$align.';"';
	}

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}

	$titletag = '<h'. $size .' class="entry-title">'.$title.'</h'.$size.'>';

	if (isSet($top) && trim($top) > 0 ) {
		$top = $top . 'px';
	}
	if (isSet($bottom) && trim($bottom) > 0 ) {
		$bottom = $bottom . 'px';
	}

	$heading = '<div class="heading-tag '.$animated.' section-align-'.$align.'" style="padding-top:'.$top.';padding-bottom:'.$bottom.';">';
	$heading .= $titletag;
	$heading .= '</div>';
	return $heading;
}
add_shortcode('heading_tag', 'mtheme_heading_tag');

// ###################################
// Information Box
// ###################################
function mtheme_infobox($atts, $content = null) {
	global $iconplace,$iconcolor,$iconbackground;
	$iconplace='';
	$iconcolor='';
	$iconbackground='';
	$servicebox='';
	extract(shortcode_atts(array(
		'column' => '4',
		'boxplace' => 'horizontal',
		'layout' => 'icon-with-title',
		'iconplace' => 'left',
		'iconcolor' => '',
		'iconbackground' => ''
	), $atts));
	$alignicon = "alignicon-top";
	if ($iconplace == "left") $alignicon = "alignicon-left";
	if ($iconplace == "right") $alignicon = "alignicon-right";

	$servicebox .= '<div class="service-column service-info-box service-column-'.$column.' service-boxes-'.$layout.' '.$alignicon.' '.$alignicon.'-'.$boxplace.' serviceboxes-'.$boxplace.' clearfix">';
	$servicebox .= do_shortcode($content);
	$servicebox .= '</div>';

	return $servicebox;
}
add_shortcode('infobox', 'mtheme_infobox');

// ###################################
// Information Item
// ###################################
	function mtheme_infobox_item($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => '',
			'link' => '',
			'linktext' => '',
			"animated" => 'none',
			'image' => '',
			'last_item' => 'no'
		), $atts));

		$service = '';
		$iconcolor_css='';
		$iconbackground_css='';
		$service_icon='';

		$animation = '';
		if ($animated != 'none') {
			$animation = 'animation-standby animated ' .$animated;
		}

		global $iconplace,$iconcolor,$iconbackground;

		$iconsize = "fa fa-2x";
		if ($iconcolor) $iconcolor_css = 'color:' .$iconcolor. ';';
		
		if ($image) $service_image = '<img class="service-image" src="'.$image.'" alt="info-image" />';
		if ( $link <>"" ) { $service_image = '<a class="service-image-link" href="'.esc_url($link).'" >' . $service_image . '</a>'; }

		$column_edge="service-item-space";
		if ($last_item=="yes") { $column_edge="clearfix"; }
		if ($last_item=="builder") { $column_edge=""; }
		$service .= '<div class="service-column service-item '.$animation.' '.$column_edge.'">';

		//if ( $link <>"" ) { $title = '<a href="'.esc_url($link).'" >' . $title . '</a>'; }
		if ($image) $service .= $service_image;
			$service .= '<div class="service-content">';
			$service .= '<h4>'.$title.'</h4>';
				$service .= '<div class="service-details">';
				$service .= wpautop( html_entity_decode($content) );
				if ($link!='' && $linktext!='' ) {
					$service .= '<div class="subtle-fade readmore-service"><a href="'.esc_url($link).'">'.$linktext.'</a></div>';
				}
				$service .= '</div>';
			$service .= '</div>';
		$service .= '</div>';

		return $service;
	}
add_shortcode('infobox_item', 'mtheme_infobox_item');

// ###################################
// ImageBox
// ###################################
	function mtheme_imagebox_item($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => '',
			'icon' => '',
			'link' => '',
			'target' => '',
			'linktext' => '',
			'displaytype' => '',
			'image' => '',
			'last_item' => 'no'
		), $atts));

		$the_content = wpautop( html_entity_decode($content) );

				$contentbox = '';
				$contentbox .= '<div class="imagebox-content-'.$displaytype.' imagebox-content-wrap">';
				$contentbox .= '<div class="imagebox-box">';
				if ( $title || $the_content ) {
					if ($title) {
						if ($link<>"" && $displaytype<>"inside") {
							$contentbox .= '<a class="textlink" target="'.esc_attr($target).'" href="'.esc_url($link).'">';
						}
						$contentbox .= '<div class="imagebox-title">' . $title . '</div>';
						if ($link<>"" && $displaytype<>"inside") {
							$contentbox .= '</a>';
						}
					}
					if ($the_content) {
						$contentbox .= '<div class="imagebox-desc">' . $the_content . '</div>';
					}
				}
				$contentbox .= '</div>';
				$contentbox .= '</div>';

				$uniqueID = rand(0,1000);

				$imagebox = '';
				$imagebox .= '<div class="imagebox-item-wrap animation-standby animated fadeIn imagebox-item-wrap-'.$displaytype.'">';
						if ($displaytype=="above") {
							$imagebox .= $contentbox;
						}
					$imagebox .= '<div class="imagebox-item">';
						$imagebox .= '<div class="imagebox-item-inner imagebox-shortcode-lightbox">';
						if ($link<>"") {
							switch ($target) {
								case '_blank':
									$imagebox .= '<a target="_blank" href="'.esc_url($link).'">';
									break;
								case '_self':
									$imagebox .= '<a target="_self" href="'.esc_url($link).'">';
									break;
								case 'lightboximage':
									$imagebox .= imaginem_codepack_activate_lightbox (
											$lightbox_type="default",
											$uniqueID,
											$predefined=$link,
											$mediatype="image",
											$title,
											$class="column-gridblock-lightbox ".$lightbox_type,
											$set="lightbox-shortcode-".$uniqueID,
											$data_name="default",
											$image,
											$imageDataID=false
										);
									break;
								case 'lightboxvideo':
									$imagebox .= imaginem_codepack_activate_lightbox (
											$lightbox_type="default",
											$uniqueID,
											$predefined=$link,
											$mediatype="image",
											$title,
											$class="column-gridblock-lightbox ".$lightbox_type,
											$set="lightbox-shortcode-".$uniqueID,
											$data_name="default",
											$image,
											$imageDataID=false
										);
									break;
								
								default:
									# code...
									break;
							}
							
						}
						$imagebox .= imaginem_codepack_display_post_image (
							get_the_id(),
							$have_image_url = $image,
							$imagelink =false,
							$type = "superlens-gridblock-full",
							$imagetitle = $title,
							$class="imagebox-image",
							$lazyload=true
						);
						if ($icon<>"") {
							$imagebox .= '<div class="imagebox-icon"><i class="'.$icon.'"></i></div>';
						}
						if ($displaytype=="inside") {
							$imagebox .= $contentbox;
						}
						if ($link<>"") {
							$imagebox .= '</a>';
						}
						$imagebox .= '</div>';
					$imagebox .= '</div>';
						if ($displaytype=="below") {
							$imagebox .= $contentbox;
						}
				$imagebox .= '</div>';

		return $imagebox;
	}
add_shortcode('imagebox_item', 'mtheme_imagebox_item');


// ###################################
// Service Box
// ###################################
function mtheme_servicebox($atts, $content = null) {
	global $iconplace,$iconcolor,$animationdelay,$iconbackground,$iconbackground_opacity,$iconborder,$animated,$boxplace;
	
	$iconplace='';
	$iconcolor='';
	$animated='';
	$iconbackground='';
	$iconbackground_opacity='';
	$servicebox='';
	$boxplace='';
	$iconborder = '';
	extract(shortcode_atts(array(
		'column' => '4',
		'boxplace' => 'horizontal',
		'layout' => 'icon-with-title',
		'iconplace' => 'left',
		'iconcolor' => '',
		'iconborder' => 'true',
		'iconbackground' => '',
		'animated' => 'none',
		'iconbackground_opacity' => ''
	), $atts));
	$alignicon = "alignicon-top";
	if ($iconplace == "left") $alignicon = "alignicon-left";
	if ($iconplace == "right") $alignicon = "alignicon-right";

	if ($iconborder=="false") {
		$iconborder="no-border";
	}

	if ($animated != 'none' && $boxplace=="horizontal" ) {
		$animated = 'animation-standby animated ' .$animated;
	}
	$servicebox .= '<div class="service-column '.$animated.' service-column-'.$column.' service-boxes-'.$layout.' '.$iconborder.' '.$alignicon.' '.$alignicon.'-'.$boxplace.' serviceboxes-'.$boxplace.' clearfix">';
	$servicebox .= do_shortcode( $content );
	$servicebox .= '</div>';

	return $servicebox;
}
add_shortcode('servicebox', 'mtheme_servicebox');

// ###################################
// Service Item
// ###################################
	function mtheme_servicebox_item($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => '',
			'link' => '',			
			'url_target' => '',			
			'url_nofollow' => '',			
			'linktext' => '',
			'counto' => '',
			'icon' => '',
			'drawicon' => '',
			'iconscale' => '',
			'pagebuilder' =>'',
			'last_item' => 'no'
		), $atts));

		$link = trim($link);
		$icon = html_entity_decode( $icon );
		$service = '';
		$iconcolor_css='';
		$iconbackground_css='';
		$iconbackground_rgba_css='';
		$iconborder_css='';
		$service_icon='';

		if ( $iconscale<>"large" ) {
			$iconscale = "small";
		}

		if ($iconscale<>"small" && $iconscale<>"large") {
			$iconscale="small";
		}

		global $iconplace,$animationdelay,$animated,$iconcolor,$iconbackground,$iconbackground_opacity,$iconborder,$boxplace;

		if ( !isSet($animationdelay) ) {
			$animationdelay=0;
		}

		$animationdelay = $animationdelay + 300;


		if ($animated != 'none' && $boxplace=="vertical" ) {
			$animated = 'animation-standby animated ' .$animated;
		}

		$iconsize = "fa-2x";
		if ($iconcolor) {
			$iconcolor_css = 'color:' . $iconcolor. ';';
		}
		if ($iconbackground) {
			$iconbackground_css = 'background-color:' .$iconbackground. ';';
			if ($iconbackground_opacity) {
				$rgb = mtheme_shortcodefunction_hex_to_rgb($iconbackground);
				$rgb_css = $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2];
				$iconbackground_rgba_css = 'background-color: rgba(' . $rgb_css . ',' . $iconbackground_opacity. ');';
			}
		}
		if ($iconborder=="true") {
			$iconborder_css = 'border-color:'.$iconcolor. ';';
		} else {
			$iconborder_css = '';
		}

		$uniqueID=get_the_id()."-".dechex(mt_rand(1,65535));
		
		if ( $iconplace =="top" ) { $iconsize = ""; }
		if ( $boxplace =="vertical" ) { $iconsize = ""; }
		$icon_tag = '<span class="icon-outer">'.$icon.'</span>';
		if ($drawicon=="draw") {
			$svg_icon_url = imaginem_codepack_get_svg_icon_url_of($icon);
			$icon_tag = '<span id="svg-animated-icon-'.esc_attr($uniqueID).'" data-iconcolor="'.$iconcolor.'" data-id="svg-animated-icon-'.esc_attr($uniqueID).'" data-icon="'.esc_url($svg_icon_url).'" class="has-svg-animated-icon"></span>';
		}
		$icon_present_class = '';
		if ($icon) {
			$icon_present_class = 'service-item-has-icon';
			$service_icon .= '<div class="service-icon">'.$icon_tag.'</div>';
		} else {
			$icon_present_class = 'service-item-no-icon';
		}

		$column_edge="service-item-space";
		if ($last_item=="yes") $column_edge="clearfix";
		if ($pagebuilder=="active") {
			$column_edge="";
		}
		$service .= '<div class="service-item '.$animated.' '.$column_edge.'">';
		$service .= '<div class="service-item-inner '.$icon_present_class.' service-icon-size-'.esc_attr($iconscale).'">';

		$target = '';
		if ($url_target) {
			$target .=' target="_blank"';
		}
		if ($url_nofollow) {
			$target .=' rel="nofollow"';
		}
		if ( $link <>"" ) { $title = '<a '.$target.' href="'.esc_url($link).'" >' . $title . '</a>'; }
		if ($icon) $service .= $service_icon;
		$service .= '<div class="service-content">';
		if ( $counto > 0 ) {
			$service .= '<div class="time-count-data" data-to="'.$counto.'">0</div>';
		}
		$service .= '<h4>'.$title.'</h4>';
		$service .= '<div class="service-details">';
		$service_content = do_shortcode($content);
		$service .= wpautop( html_entity_decode($service_content) );
		if ($link!='' && $linktext!='' ) {
			$service .= imaginem_codepack_show_continue_reading( $linktext , $link);
		}
		$service .= '</div>';
		$service .= '</div>';
		$service .= '</div>';
		$service .= '</div>';

		return $service;
	}
add_shortcode('servicebox_item', 'mtheme_servicebox_item');

// ###################################
// Callout Message
// ###################################
	function mtheme_callout_msg($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => '',
			'type' => '',
			'description' => '',
			'button' => '',
			'gradient_color' => '',
			'background_color' => '',
			'gradient_angle' => '',
			'animated' => 'none',
			'button_color' =>'',
			'button_icon' =>'',
			'button_type' =>'',
			'button_text' =>'',
			'button_link' => ''
		), $atts));

		if ($animated != 'none') {
			$animated = 'animation-standby animated ' .$animated;
		}

		$service = '';
		$iconcolor_css='';
		$iconbackground_css='';
		$service_icon='';

		$backgroundstyle = '';

		if($gradient_color<>"" && $background_color<>""){
			if ($gradient_angle<>"none") {
				switch ($gradient_angle) {
					case 'to_bottom':
						$gradient_direction = 'to bottom';
						break;
					case 'to_top':
						$gradient_direction = 'to top';
						break;
					case 'to_top_right':
						$gradient_direction = 'to top right';
						break;
					case 'to_top_left':
						$gradient_direction = 'to top left';
						break;
					case 'to_bottom_right':
						$gradient_direction = 'to bottom right';
						break;
					case 'to_bottom_left':
						$gradient_direction = 'to bottom left';
						break;
					
					default:
						$gradient_direction = 'to bottom';
						break;
				}
				$backgroundstyle = 'style="background: linear-gradient('.$gradient_direction.', '.$background_color.', '.$gradient_color.');"';
			}
		}

		if ($background_color<>"" && $gradient_color=="") {
			$background_color_rgb=superlens_hex2RGB($background_color,true);
			$backgroundstyle = 'style="background:rgba('. $background_color_rgb .',0.8);"';

		}

		$callout =  '<div '.$backgroundstyle.' class="callout-wrap '.$animated.' calltype-'.$type.' clearfix">';
			$callout .=  '<div class="callout clearfix">';
				$callout .=  '<div class="first-half">';
				$callout .= '<div class="callout-desc">'. wpautop( html_entity_decode($description) ) .'</div>';
				$callout .= '</div>';
				
				if (isSet($button_text) && !empty($button_text)) {
					$callout .=  '<div class="second-half">';
					$callout .= '<div class="callout-button">';
					$callout .= do_shortcode('[button link="'.$button_link.'" button_icon="'.$button_icon.'" button_color="'.$button_color.'"]'.$button_text.'[/button]');
					$callout .= '</div>';
					$callout .= '</div>';
				}
				
			$callout .= '</div>';
		$callout .= '</div>';
		return $callout;
	}
add_shortcode('callout', 'mtheme_callout_msg');

// ###################################
// Pricing Table
// ###################################
	function mtheme_pricing_table($atts, $content = null) {
		extract(shortcode_atts(array(
			'columns' => '4',
			'service' => 'false'
		), $atts));

		global $pricing_columns;
		$pricing_columns = "column".$columns;

		$service_class='';
		if ($service == 'true') {
			$service_class = "pricing-table-service ";
		}
		$pricing_table =  '<div class="pricing-table '.$service_class.'clearfix">';
		$pricing_table .= do_shortcode($content);
		$pricing_table .= '</div>';
		return $pricing_table;
	}
add_shortcode('pricing_table', 'mtheme_pricing_table');

// ###################################
// Pricing Column
// ###################################
	function mtheme_pricing_column($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => '',
			'title_bgcolor' =>'',
			'type' => '1',
			'link' => '',
			'link_target' => '',
			'button_text' => '',
			'animated' => 'none',
			'topspace' => '175',
			'featured' => 'false',
			'currency' => '$',
			'price' => '17',
			'fraction' => '17',
			'duration' => 'Monthly'
		), $atts));

		global $pricing_columns;

		$highlight='';
		if ($featured=="true") $highlight="pricing_highlight";
		$pricing_table = '<div class="pricing-column-target '.$pricing_columns.' '.$highlight.'">';
		$pricing_table .= '<div class="pricing-column-type-'.$type.' pricing-column">';
		$pricing_table .= '<div class="pricing-header">';
		$pricing_table .= '<div class="pricing-title"><h2>'.$title.'</h2></div>';

		$price_sep = explode('.',$price);

		$pricing_table .= '<div class="pricing-section">';
			$pricing_table .= '<div class="pricing-wrap">';
				$pricing_table .= '<div class="pricing-cell">';
					$pricing_table .= '<span class="pricing-currency">'.$currency.'</span>'.$price.'<span class="pricing-suffix">'.$fraction.'</span>';
				$pricing_table .= '</div>';
				$pricing_table .= '<div class="pricing-duration">'.$duration.'</div>';
			$pricing_table .= '</div>';
		$pricing_table .= '</div>';
		$pricing_table .= '</div>';

		$pricing_table .= '<ul>';
		$pricing_table .= do_shortcode($content);
		$pricing_table .= '</ul>';

		$target='';
		if ($link_target=="_blank") {
			$target=' target="_blank"';
		}

		$pricing_table .= '<div class="pricing-button-wrap">';
		$pricing_table .= '<a '.$target.' href="'.esc_url($link).'">';
		$pricing_table .= '<div class="mtheme-button">';
		$pricing_table .= $button_text;
		$pricing_table .= '</div>';
		$pricing_table .= '</a>';
		$pricing_table .= '</div>';

		$pricing_table .= '</div>';
		$pricing_table .= '</div>';
		return $pricing_table;
	}
add_shortcode('pricing_column', 'mtheme_pricing_column');

// ###################################
// Pricing Price
// ###################################
	function mtheme_pricing_price($atts, $content = null) {
		extract(shortcode_atts(array(

		), $atts));


		return $pricing_table;
	}
add_shortcode('pricing_price', 'mtheme_pricing_price');

// ###################################
// Pricing Service
// ###################################
	function mtheme_pricing_service($atts, $content = null) {

		$pricing_service = '<li class="pricing-service">';
			$content = wpautop( html_entity_decode($content) );
			$pricing_service .= do_shortcode( $content );
		$pricing_service .= '</li>';
		return $pricing_service;
	}
add_shortcode('pricing_service', 'mtheme_pricing_service');

// ###################################
// Pricing Row
// ###################################
	function mtheme_pricing_row($atts, $content = null) {
		extract(shortcode_atts(array(
			'type' => ''
		), $atts));

		
		$pricing_table = '<li class="pricing-row pricing-tick-'.$type.'">';
		if ($type) { $pricing_table .= '<i class="feather-icon-circle-check"></i>'; }
		if (!$type) { $pricing_table .= '<i class="feather-icon-cross"></i>'; }
		$pricing_table .= do_shortcode($content);
		$pricing_table .= '</li>';
		return $pricing_table;
	}
add_shortcode('pricing_row', 'mtheme_pricing_row');

// ###################################
// Pricing Footer
// ###################################
	function mtheme_pricing_footer($atts, $content = null) {
		extract(shortcode_atts(array(
			'title' => 'Pricing'
		), $atts));

		
		$pricing_table = '<li class="pricing-footer">';
		$pricing_table .= do_shortcode($content);
		$pricing_table .= '</li>';
		return $pricing_table;
	}
add_shortcode('pricing_footer', 'mtheme_pricing_footer');


// ###################################
// FontAwesome
// ###################################
add_shortcode('icongenerator', 'mtheme_fontawesome_icon_gen');
function mtheme_fontawesome_icon_gen($atts, $content = null) {

	extract(shortcode_atts(array(
		'size' => '',
		'align' => 'center',
		'icon' => 'feather-icon-disc',
		'iconcolor' => ''
	), $atts));

	$css_style = 'font-size:'.$size.'px; color:'.$iconcolor.' !important;';

	$fontawesome = '<div class="clearfix" style="text-align:'.$align.';">';
	$fontawesome .= '<i style="'.$css_style.'" class="shortcode-fontawesome-icon '.$icon.'"'.'></i>';
	$fontawesome .= '</div>';

	return $fontawesome;
}



// ###################################
// @ Since Version 2.4
// Counter
// ###################################
add_shortcode('odometercount', 'mtheme_counter_timer');
function mtheme_counter_timer($atts, $content = null) {

	extract(shortcode_atts(array(
		'icon' => '',
		'from' => '0',
		'title' => 'title',
		'decimal_places' => '0',
		'counto' => '1000',
		'iconcolor' => '',
	), $atts));

	$uniqueID=get_the_id()."-".dechex(mt_rand(1,65535));

	$fontawesome = '<div class="count-icon-wrap"><i class="time-count-icon '.$icon.'"'.'></i></div>';

	$counter = '';
	$counter = '<div class="shortcode-time-counter-block service-column">';
	$counter .= $fontawesome;
	$counter .= '<div class="time-count-data odometer time-count-class-'.$uniqueID.'" data-to="'.$counto.'" id="time-count-data-'.$uniqueID.'" data-id="time-count-data-'.$uniqueID.'">0</div>';
	$counter .= '<div class="service-content">';
	$counter .= '<h4>'.$title.'</h4>';
	if ($content<>"") {
		$counter .= '<div class="service-details">';
		$counter .= do_shortcode($content);
		$counter .= '</div>';
	}
	$counter .= '</div>';
	$counter .= '</div>';

	return $counter;
}

add_shortcode('anchor', 'mtheme_anchor');
function mtheme_anchor( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'id' => '#anchor'
	), $atts));

	$anchor = '<span id="'.$id.'" data-id="anchor"></span>';

	return $anchor;
}

// ###################################
// Hero Image II
// ###################################
add_shortcode('heroimage_ii', 'mtheme_heroimage_ii');
function mtheme_heroimage_ii( $atts, $content = null ) {
	extract(shortcode_atts(array(
			'image' => '',
			'text' => '',
			'text_location' => 'middle',
			'intensity' => 'light',
			'link' => '',
			'title' => '',
			'subtitle' => '',
			'icon' => '',
			'icon_image' => '',
			'offsetclass' => 'outer-wrap',
			'text_slide' => 'single',
			'text_decoration' => 'none'
	), $atts));

	$jarallax_data_tag = "modular-column-parallax data-jarallax='{\"speed\": -0.1}'";
	$output = '<div id="heroimage">';
$output .= '<div class="loading-spinner-centered loading-spinner-detect">
<span class="coverdots-1 coverdots-element"></span>
<span class="coverdots-2 coverdots-element"></span>
<span class="coverdots-3 coverdots-element"></span>
<span class="coverdots-4 coverdots-element"></span>
<span class="coverdots-5 coverdots-element"></span>
</div>';
	$output .= '<div class="block-reveal-image"></div>';
	$output .= '<div id="heroimage-background" class="heroimage-wrap jarallax-parent textlocation-'.$text_location.' intensity-'.$intensity.' image-cover " '.$jarallax_data_tag.' style="background-image: url('.esc_url($image).');"></div>';
	$output .= '<div id="gradient" class="hero-image-overlay"></div>';

	//$output .= '<div class="grad-background gradient-fill-1"></div>';
	//$output .= '<div class="grad-background gradient-fill-2"></div>';
	if ($text_slide!="disable") {
		$output .= '<div class="hero-text-wrap"><ul class="text-parallax '.$text_decoration.' '.$text_slide.'">';
			$output .= '	<li>';
			if ( $title<>"" ) {
				$output .= '<h1 class="hero-text-effect hero-text-effect-2 hero-title">'.html_entity_decode($title).'</h1>';
			}
			if ( trim($subtitle)<>"" ) {
				$output .= '<h2 class="hero-text-effect hero-text-effect-1 hero-subtitle">'.esc_html($subtitle).'</h2>';
			}
			if ( $content<>"" ) {
				$output .= '<div class="entry-content animated fadeIn animation-standby text-is-'.$intensity.'">';
				$output .= wpautop( html_entity_decode($content) );
				$output .= '</div>';
			}
			$link = trim($link);
			$text = trim($text);
			if ( isSet($link) && !empty($link) && isSet($text) && !empty($text) ) {
				$output .= '<a class="bright" href="'.esc_url($link).'">';
				$output .= '<div class="hero-button mtheme-button animated fadeIn animation-standby">'.esc_html($text).'</div>';
				$output .= '</a>';
			}
			$output .= '</li>';
		$output .= '</ul></div>';
	}
		$output .= '<div class="mouse-pointer-wrap">';
		$output .= '<div class="bright hero-link-to-base">';
		if ($icon=="true") {
			$output .= '<div class="mouse-pointer"><div class="indication-animated indicate-bounce mouse-wheel"></div></div>';
		}
		$output .= '</div>';
		$output .= '</div>';
	$output .= '</div>';

	return $output;
}
// ###################################
// Hero Image
// ###################################
add_shortcode('heroimage', 'mtheme_heroimage');
function mtheme_heroimage( $atts, $content = null ) {
	extract(shortcode_atts(array(
			'image' => '',
			'text' => '',
			'text_location' => 'middle',
			'intensity' => 'light',
			'link' => '',
			'icon' => '',
			'icon_image' => '',
			'offsetclass' => 'outer-wrap',
			'text_slide' => 'single',
			'text_decoration' => 'none'
	), $atts));


	$jarallax_data_tag = "";
	$output = '<div id="heroimage" class="heroimage-wrap intensity-'.$intensity.' textlocation-'.$text_location.'">';
$output .= '<div class="hero-loading-spinner loading-spinner-centered loading-spinner-detect">
<span class="coverdots-1"></span>
<span class="coverdots-2"></span>
<span class="coverdots-3"></span>
<span class="coverdots-4"></span>
<span class="coverdots-5"></span>
</div>';
	$output .= '<div class="block-reveal-image"></div>';
	$output .= '<div id="heroimage-background" class="heroimage intensity-'.$intensity.' image-cover " '.$jarallax_data_tag.' style="background-image: url('.esc_url($image).');"></div>';
	$output .= '<div id="gradient" class="hero-image-overlay"></div>';
	if ($text_slide!="disable") {
		$output .= '<div class="hero-text-wrap text-decoaration-'.$text_decoration.' animation-standby animated fadeIn">';
		$output .= '<div class="hero-text-wrap-inner">';
		$output .= '<ul class="'.$text_decoration.' '.$text_slide.'">';
		$output .= do_shortcode($content);
		$output .= '</ul>';
		$output .= '</div>';
		$output .= '</div>';
	}
	$output .= '<div class="mouse-pointer-wrap">';
	$output .= '<div class="bright hero-link-to-base">';
	if ($icon=="true") {
		$output .= '<div class="mouse-pointer"><div class="indication-animated indicate-bounce mouse-wheel"></div></div>';
	}
	$output .= '</div>';
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}
add_shortcode('heroimage_text', 'mtheme_heroimage_textslides');
function mtheme_heroimage_textslides($atts, $content = null) {
	extract(shortcode_atts(array(
			'title' => '',
			'subtitle' => ''
	), $atts));

	$output = '	<li>';
	if ( $subtitle<>"" ) {
		$output .= '<h2 class="hero-subtitle">'.esc_html($subtitle).'</h2>';
	}
	if ( $title<>"" ) {
		$output .= '<h1 class="hero-title">'.html_entity_decode($title).'</h1>';
	}
	$output .= '</li>';

	return $output;
}

// ###################################
// PhotoCard
// ###################################
add_shortcode('photocard_one', 'mtheme_photocard_one');
function mtheme_photocard_one( $atts, $content_richtext = null ) {
	extract(shortcode_atts(array(
			'image' => '',
			'imageid' => '',
			'title' => '',
			'text_align' => 'center',
			'subtitle' => '',
			'button_status' => '',
			'button' => '',
			'video_mp4' => '',
			'video_ogv' => '',
			'video_webm' => '',
			'animated' => 'none',
			'content_color' => '',
			'button_link' => '',
			'image_block' => 'left',
			'background_color' => ''
	), $atts));

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}

	$opposite = 'column-float-left column-half';
	if ($image_block=='left') {
		$opposite = 'column-float-right column-half';
	}
	if ($image_block=='bottom') {
		$opposite = 'column-float-none image-set-bottom';
	}
	if ($image_block=='top') {
		$opposite = 'column-float-none image-set-top';
	}

	if ( !empty($imageid)) {
		$image_array = wp_get_attachment_image_src($imageid,'superlens-gridblock-full',false);
		//print_r($image_array);
		$image_src = $image_array[0];
	} else {
		$image_src=$image;
	}

	if ($image_block=='bottom' || $image_block=='top' ) {
		$image_set = '
		<div class="photocard-image-wrap">
			<img class="fullwidth-image" src="'.esc_url($image_src).'"  alt="'.esc_attr( imaginem_codepack_get_alt_text($imageid) ).'" />
		</div>';
	} else {
		$video_class='';
		if ( !empty($video_mp4) && isSet($video_mp4) && $video_mp4!='' ) {
			$video_class = "photocard-video";
		}
		$image_set = '<div class="photocard-image-wrap '.$video_class.' column-half column-float-'.$image_block.'">';
			if ( !wp_is_mobile() ) {
				if ( !empty($video_mp4) && isSet($video_mp4) && $video_mp4!='' ) {
					$image_set .= '<div id="photocardvideo">';
					$image_set .= '<video autoplay loop muted>';
					    $image_set .= '<source src="'.esc_url($video_mp4).'" type="video/mp4" />';
					    $image_set .= '<source src="'.esc_url($video_webm).'" type="video/webm" />';
					    $image_set .= '<source src="'.esc_url($video_ogv).'" type="video/ogv" />';
					    $image_set .= '<img class="photocardvideo-fallback-image" src="'.esc_url($image_src).'" alt="videofallback" />';
					    //$image_set .= '<div class="photocard-image-container" style="background-image:url('.esc_url($image_src).');"></div>';
					$image_set .= '</video>';
					$image_set .= '</div>';
				} else {
					$image_set .= '<div class="block-reveal-image"></div>';
					$image_set .= '<div class="photocard-image-container" style="background-image:url('.esc_url($image_src).');"></div>';
				}
			} else {
				$image_set .= '<div class="photocard-image-container" style="background-image:url('.esc_url($image_src).');"></div>';
			}
		$image_set .= '</div>';
	}

	$css_style="";
	if ( !empty( $background_color ) ) {
		$css_style = 'style="background-color:'.$background_color.';"';
	}

	$content_set = '
	<div class="photocard-content-wrap photocard-'. $content_color .' '.$opposite.'" '.$css_style.'>
		<div class="photocard-contents-inner text-align-'.$text_align.'">
			<div class="heading-block">
				<h2 class="photocard-title '.$animated.' animation-1-5-sec">
				'.$title.'
				</h2>
				<h3 class="photocard-subtitle '.$animated.'">
					'.$subtitle.'
				</h3>
			</div>
			<div class="photocard-contents '.$animated.' animation-2-0-sec">
			'. wpautop( html_entity_decode($content_richtext) ).'
			</div>';
	if (!empty($button)) {
	$content_set .= '
			<a class="'.$animated.' animation-2-3-sec" href="'.esc_url($button_link).'">
			<div class="mtheme-button">
			'.$button.'
			</div>
			</a>';
	}
	$content_set .= '
		</div>
	</div>';

if ( $image_block=="bottom" ) {
	$final_set = $content_set . $image_set;
} else {
	$final_set = $image_set . $content_set;
}

	$output = '
<div class="photocard-wrap-common photocard-wrap-type-one clearfix">
'.$final_set.'
</div>
';

return $output;
}

// ###################################
// PhotoCard Type 2
// ###################################
add_shortcode('photocard_two', 'mtheme_photocard_type_two');
function mtheme_photocard_type_two( $atts, $content_richtext = null ) {
	extract(shortcode_atts(array(
			'image' => '',
			'imageid' => '',
			'title' => '',
			'text_align' => 'center',
			'subtitle' => '',
			'button_status' => '',
			'button' => '',
			'video_mp4' => '',
			'video_ogv' => '',
			'video_webm' => '',
			'animated' => 'none',
			'content_color' => '',
			'button_link' => '',
			'image_block' => 'left',
			'background_color' => ''
	), $atts));

	$animated = '';

	$opposite = 'column-float-right column-half';
	if ($image_block=='left') {
		$opposite = 'column-float-left column-half';
	}
	if ($image_block=='center') {
		$opposite = 'column-float-center column-half';
	}
	if ($image_block=='bottom') {
		$opposite = 'column-float-none image-set-bottom';
	}
	if ($image_block=='top') {
		$opposite = 'column-float-none image-set-top';
	}

	if ( !empty($imageid)) {
		$image_array = wp_get_attachment_image_src($imageid,'superlens-gridblock-source',false);
		//print_r($image_array);
		$image_src = $image_array[0];
	} else {
		$image_src=$image;
	}

	$video_class='';
	$image_set='';
	if ( !empty($video_mp4) && isSet($video_mp4) && $video_mp4!='' ) {
		$video_class = "photocard-video";
	
		$image_set = '<div class="photocard-image-wrap '.$video_class.' column-half column-float-'.$image_block.'">';
			if ( !wp_is_mobile() ) {
				if ( !empty($video_mp4) && isSet($video_mp4) && $video_mp4!='' ) {
					$image_set .= '<div id="photocardvideo">';
					$image_set .= '<video autoplay loop muted>';
					    $image_set .= '<source src="'.esc_url($video_mp4).'" type="video/mp4" />';
					    $image_set .= '<source src="'.esc_url($video_webm).'" type="video/webm" />';
					    $image_set .= '<source src="'.esc_url($video_ogv).'" type="video/ogv" />';
					    $image_set .= '<img class="photocardvideo-fallback-image" src="'.esc_url($image_src).'" alt="videofallback" />';
					    //$image_set .= '<div class="photocard-image-container" style="background-image:url('.esc_url($image_src).');"></div>';
					$image_set .= '</video>';
					$image_set .= '</div>';
				} else {
				}
			} else {
				$image_set .= '<div class="photocard-image-container" style="background-image:url('.esc_url($image_src).');"></div>';
			}
		$image_set .= '</div>';
	}

	$css_style="";
	if ( empty( $background_color ) ) {
		$background_color="none";
	}
	if ($background_color=="none") {
		$css_style='';
	} else {
		$background_color_rgb = mtheme_shortcodefunction_hex_to_rgb($background_color);
		$background_color_rgb_css = $background_color_rgb[0] . ',' . $background_color_rgb[1] . ',' . $background_color_rgb[2];
		$background_color_rgb_css = 'rgba(' . $background_color_rgb_css . ',0.95);';

		$css_style = 'style="background-color:'.$background_color_rgb_css.'"';
	}
	$content_set = '
	<div class="photocard-content-wrap photocard-'. $content_color .' '.$opposite.'">
		<div class="photocard-contents-inner text-align-'.$text_align.'" '.$css_style.'>
			<div class="heading-block">
				<h2 class="photocard-title">
				'.$title.'
				</h2>
				<h3 class="photocard-subtitle">
					'.$subtitle.'
				</h3>
			</div>
			<div class="photocard-contents">
			'. wpautop( html_entity_decode($content_richtext) ).'
			</div>';
	if (!empty($button)) {
	$content_set .= '
			<a class="'.$animated.' animation-2-3-sec" href="'.esc_url($button_link).'">
			<div class="mtheme-button">
			'.$button.'
			</div>
			</a>';
	}
	$content_set .= '
		</div>
	</div>';

if ( $image_block=="bottom" ) {
	$final_set = $content_set . $image_set;
} else {
	$final_set = $image_set . $content_set;
}

$jarallax_data_tag = "data-jarallax='{\"speed\": 0.3}'";
	$output = '
<div class="photocard-image-two-wrap">
<div class="photocard-wrap-common photocard-wrap-type-two clearfix" '.$jarallax_data_tag.' style="background-image:url('.esc_url($image_src).');">
'.$final_set.'
</div>
</div>
';

return $output;
}


add_shortcode('singleimage', 'mtheme_singleimage');
function mtheme_singleimage ( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'image' => '',
		'imageid' => '',
		'align' => '',
		'link_url' => '',
		'link_type' => '',
		'animated' => '',
		'width' => '',
		'paddingtop' => '',
		'paddingbottom' => '',
		'margintop' => '',
		'marginbottom' => ''
	), $atts));

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}

	if ($paddingtop>0) {
		$paddingtop = $paddingtop . 'px';
	}
	if ($paddingbottom>0) {
		$paddingbottom = $paddingbottom . 'px';
	}
	if ($margintop<>"") {
		$margintop = $margintop . 'px';
	}
	if ($marginbottom<>"") {
		$marginbottom = $marginbottom . 'px';
	}
	if ($width>0) {
		$width = 'width:'.$width . 'px';
	} else {
		$width = '';
	}
	if ($link_url=="{{link_url}}") { $link_url=''; }
	if ($link_type=="{{link_type}}") { $link_type=''; }

	$open_link='';
	$close_link='';

	if ($link_url<>"") {
		if ($link_type<>"") {
			if ($link_type=="_blank") {
				$link_target = 'target="_blank"';
			}
			if ($link_type=="_self") {
				$link_target = 'target="_self"';
			}
		}
		$open_link = '<a '.$link_target.'href="'.esc_url($link_url).'">';
		$close_link = '</a>';
	}
	$extra_class = '';
	if ($align == "fullwidth") {
		$align = "center";
		$extra_class = "single-image-fullwidth ";
	}
	$output = '<div class="single-image-block-outer clearfix">';
	$output .= '<div class="single-image-block single-image-align-'.esc_attr($align).' '.$extra_class . $animated.'" >';
	$output .= $open_link . '<img src="'.esc_url($image).'" alt="'.esc_attr( imaginem_codepack_get_alt_text($imageid) ).'"/>'.$close_link;
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}

// Contents
function mtheme_displaypagecontent($atts, $content = null) {
	extract(shortcode_atts(array(
		'content' => ''
	), $atts));

	$content = get_the_content();
	$content = apply_filters('the_content', $content);
	$output = do_shortcode($content);
	return $output;
}
add_shortcode('pagecontent', 'mtheme_displaypagecontent');

// Multi Headline
function mtheme_multiheadline($atts, $content = null) {

	$output = '<div class="multi-headlines-outer">';
		$output .= '<div class="multi-headlines-wrap">';
		$output .= '<div class="multi-headlines">' . do_shortcode($content) . ' </div>';
		$output .= '</div>';
	$output .= '</div>';
	$result = html_entity_decode($output);
	return $result;
}
add_shortcode('multiheadline', 'mtheme_multiheadline');
// Contents
function mtheme_displayrichtext($atts, $content = null) {
	extract(shortcode_atts(array(
		'content_richtext' => ''
	), $atts));
	
	$output = html_entity_decode($content_richtext);
	return $output;
}
add_shortcode('displayrichtext', 'mtheme_displayrichtext');
// TextBox
function mtheme_textbox($atts, $content = null) {
	extract(shortcode_atts(array(
   		'padding_sides' => '0',
		'padding_top' => '0',
		'padding_bottom' => '0',
		'animated' => 'none',
		'margin_sides' => '0',
		'margin_top' => '0',
		'margin_bottom' => '0',
		'text_color' =>'',
		'background_color' =>'',
		'border_size' => '1',
		'border_color' => '',
		'border_style' => 'solid',
		'content_richtext' => ''
	), $atts));

	if ($animated != 'none') {
		$animated = 'animation-standby animated ' .$animated;
	}

	if ($padding_top <> 0) {
		$padding_top = $padding_top . 'px';
	}
	if ($padding_bottom <> 0) {
		$padding_bottom = $padding_bottom . 'px';
	}
	if ($padding_sides <> 0) {
		$padding_sides = $padding_sides . 'px';
	}
	if ($margin_top <> 0) {
		$margin_top = $margin_top . 'px';
	}
	if ($margin_bottom <> 0) {
		$margin_bottom = $margin_bottom . 'px';
	}
	if ($margin_sides <> 0) {
		$margin_sides = $margin_sides . 'px';
	}

	$border_size = 'border-width:'.$border_size.'px;';
	$border_color = 'border-color:'.$border_color.';';
	$border_style = 'border-style:'.$border_style.';';
	$padding = 'padding: '.$padding_top. ' ' .$padding_sides.' '.$padding_bottom;
	$margin = 'margin: '.$margin_top. ' ' .$margin_sides.' '.$margin_bottom;
	$background = 'background-color: '.$background_color.';';
	$text_color = 'color: '.$text_color.';';
	$output = '<div class="text-box-inner-wrap">';
	$output .= '<div class="text-box-inner '.$animated.'" style="'.$background.$text_color.$border_size.$border_color.$border_style.'">';
	$output .= wpautop(html_entity_decode($content));
	$output .= '</div>';
	$output .= '</div>';
	return $output;
}
add_shortcode('textbox', 'mtheme_textbox');

// Contents
function mtheme_display_currentyear($atts, $content = null) {
	extract(shortcode_atts(array(
		'content_richtext' => ''
	), $atts));
	
	$output = date('Y');
	return $output;
}
add_shortcode('display_current_year', 'mtheme_display_currentyear');

// Hline
function mtheme_hline($atts, $content = null) {
	extract(shortcode_atts(array(
		'style' => 'single',
		'height' => '1',
		'linecolor' => '',
		'top' => '30',
		'bottom' => ''
	), $atts));
	$divider = '';
	$stylecss ='';
	if ( isSet($linecolor) && !empty($linecolor) ) {
		$stylecss .= 'border-color:'.$linecolor.';';
	}
	if ( isSet($height) && !empty($height) ) {
		$stylecss .= 'border-width:'.$height.'px;';
	}
	$divider .= '<div class="clearfix hline-common hline-'.$style.'" style="'.$stylecss.'"></div>';
	
	return $divider;
}
add_shortcode('hline', 'mtheme_hline');

//Button [button link="yourlink.html"] text [/button]
function mtheme_Modal( $atts, $content = null ) {

   extract( shortcode_atts( array(
      'link' => '#',
      'modal_id' => '',
      'buttontext' => '',
      'button_icon' => '',
	  'target' => '',
	  'button_align' => '',
	  'button_color' => '',
	  'size'=> 'normal',
      ), $atts ) );

    $uniqueID = uniqid();

 $modalwindow_popup = '
 <div class="modal-dimmer" id="'.$modal_id.'">
	 <div class="modal-dimmer-outer">
		 <div class="modal-dimmer-inner md-modal md-effect-2">
		 	<div class="modal-dimmer-text md-content entry-content">
		 	<div class="modal-close-button"><i class="simpleicon-close"></i></div>
		 	'.do_shortcode( wpautop( html_entity_decode($content) ) ).'
		 	</div>
		 </div>
	 </div>
 </div>
 ';
	
	$button = $modalwindow_popup;
	if ($target=="_blank") { $target=' target="_blank"'; }
	if ($button_align=="{{button_align}}") { $button_align=''; }
	if ($button_align=='') {
		$button_align_style='';
	} else {
		$button_align_style= ' style="text-align:'.$button_align.';"';
	}


	$button_style_tag = '';
	$custom_style_added_class = '';
	$data_tag = '';
	if (isSet($button_color) && !empty($button_color) ) {
		$button_style_tag = 'style="border-color: '.$button_color.'; color: '.$button_color.';" ';
		$custom_style_added_class = ' button-has-a-color';
		$data_tag = 'data-backgroundafter="'.$button_color.'" data-buttonid="'.$uniqueID.'" ';
	}

	$button .= '<div class="button-shortcode button-shortcode-'.$uniqueID.'"'.$button_align_style.'>';
	$button .= '<div data-modalid="'.$modal_id.'" class="modal-trigger-button">';
	$button .= '<div  '.$button_style_tag.$data_tag.' class="mtheme-button'.$custom_style_added_class.'">';
	if (isSet($button_icon) && !empty($button_icon) ) {
		$button .= '<span class="button-icon"><i class="'.$button_icon.'"></i></span>';
	}
	$button .= trim($buttontext);
	$button .= '</div>';
	$button .= '</div>';
	$button .= '</div>';
	
   return $button;
}
add_shortcode('modalwindow', 'mtheme_Modal');
?>