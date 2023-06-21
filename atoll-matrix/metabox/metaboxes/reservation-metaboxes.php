<?php
function themecore_reservations_metadata() {
	$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$mtheme_sidebar_options = themecore_generate_sidebarlist('reservations');

	// Pull all the Featured into an array
	$bg_slideshow_pages = get_posts('post_type=fullscreen&orderby=title&numberposts=-1&order=ASC');

	if ($bg_slideshow_pages) {
		$options_bgslideshow['none'] = "Not Selected";
		foreach($bg_slideshow_pages as $key => $list) {
			$custom = get_post_custom($list->ID);
			if ( isset($custom["fullscreen_type"][0]) ) { 
				$slideshow_type=$custom["fullscreen_type"][0]; 
			} else {
			$slideshow_type="";
			}
			if ($slideshow_type<>"Fullscreen-Video") {
				$options_bgslideshow[$list->ID] = $list->post_title;
			}
		}
	} else {
		$options_bgslideshow[0]="Featured pages not found.";
	}

	$room_names = get_posts('post_type=room&orderby=title&numberposts=-1&order=ASC');

	if ($room_names) {
		$options_room_names['none'] = "Not Selected";
		foreach($room_names as $key => $list) {
			$custom = get_post_custom($list->ID);
			$options_room_names[$list->ID] = $list->post_title;
		}
	} else {
		$options_room_names[0]="Rooms not found.";
	}

	// Generate unique booking number
	$booking_number = uniqid('booking-');

	$reservations_box = array(
		'id' => 'reservationsmeta-box',
		'title' => esc_html__('Reservations Metabox','themecore'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Reservation Settings','themecore'),
				'id' => 'pagemeta_reservations_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Reservations Settings','themecore'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Reservation Options','themecore'),
				'id' => 'pagemeta_sep_page_options',
				'type' => 'seperator',
				),
			array(
				'name' => '',
				'id' => 'pagemeta_booking_number',
				'type' => 'readonly',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => $booking_number
			),
			array(
				'name' => esc_html__('Reservation Status','themecore'),
				'id' => 'pagemeta_reservation_status',
				'class' => 'reservation_status',
				'type' => 'select',
				'desc' => esc_html__('Reservation Status','themecore'),
				'options' => array(
					'confirmed'    => esc_attr__('Confirmed','themecore'),
					'cancelled'  => esc_attr__('Cancelled','themecore'),
					'pending' => esc_attr__('Pending','themecore')
					),
			),
			array(
				'name' => esc_html__('Room','themecore'),
				'id' => 'pagemeta_room_name',
				'class' => 'room_choice',
				'type' => 'select',
				'target' => 'room_names',
				'desc' => esc_html__('Room.','themecore'),
				'options' => ''
				),
			array(
				'name' => esc_html__('Reservation','themecore'),
				'id' => 'pagemeta_reservation_checkin',
				'type' => 'reservation',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_checkin_date',
				'type' => 'offview',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_checkout_date',
				'type' => 'offview',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_per_night_cost',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Per night price','themecore'),
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_total_room_cost',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Total Room price','themecore'),
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_room_paid',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Paid Total','themecore'),
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_room_paid_balance',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Balance Total','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Notes','themecore'),
				'id' => 'pagemeta_reservation_notes',
				'type' => 'textarea',
				'desc' => __('Notes.','themecore'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Page Settings','themecore'),
				'id' => 'pagemeta_page_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => 'Adults',
				'id' => 'pagemeta_reservation_room_adults',
				'type' => 'number',
				'occupant' => 'adult',
				'datafrom' => 'roomtype',
				'maxcap' => 'pagemeta_max_adults',
				'min' => '1',
				'max' => '9',
				'step' => '1',
				'unit' => 'adults',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => '0'
			),
			array(
				'name' => 'Children',
				'id' => 'pagemeta_reservation_room_children',
				'type' => 'number',
				'occupant' => 'child',
				'datafrom' => 'roomtype',
				'min' => '0',
				'max' => '9',
				'step' => '1',
				'unit' => 'children',
				'maxcap' => 'pagemeta_max_children',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => '0'
			),
		)
	);

	$customer = cognitive_get_customer_array();

	$reservation_id = get_the_ID();

	$customer_datafetch = array(
		array(
			'name' => esc_html__('Customer','themecore'),
			'id' => 'pagemeta_sep_page_options',
			'type' => 'seperator',
			),
		array(
			'name' => '',
			'id' => $reservation_id,
			'type' => 'get_customer_data',
			'class' => '',
			'heading' => '',
			'desc' => '',
			'std' => ''
		)
	);

	

	if ( ! cognitive_check_customer_exists( $reservation_id ) ) {
		$reservations_box['fields'] = array_merge($reservations_box['fields'], $customer);
	} else {
		$reservations_box['fields'] = array_merge($reservations_box['fields'], $customer_datafetch);
	}
	return $reservations_box;
}
/*
* Meta options for Reservations post type
*/
function themecore_reservationsitem_metaoptions(){
	$reservations_box = themecore_reservations_metadata();
	themecore_generate_metaboxes($reservations_box,get_the_id());
}
?>