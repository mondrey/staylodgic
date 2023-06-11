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
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => $booking_number
			),
			array(
				'name' => esc_html__('Reservation Status','themecore'),
				'id' => 'pagemeta_reservation_notice',
				'class' => 'reservation_notice',
				'type' => 'select',
				'desc' => esc_html__('Reservation Status','themecore'),
				'options' => array(
					'active'    => esc_attr__('Active','themecore'),
					'inactive'  => esc_attr__('Hide from Listings','themecore'),
					'postponed' => esc_attr__('Display as Postponed','themecore'),
					'cancelled' => esc_attr__('Display as Cancelled','themecore'),
					'fullreservation' => esc_attr__('Reservation is Full','themecore'),
					'pastreservation' => esc_attr__('Past Reservation','themecore')
					),
			),
			array(
				'name' => esc_html__('Room','themecore'),
				'id' => 'pagemeta_room_name',
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
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_checkout_date',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_room_cost',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Room Price','themecore'),
				'std' => ''
			),
			array(
				'name' => '',
				'id' => 'pagemeta_reservation_room_paid',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Paid Price','themecore'),
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
				'name' => 'Guests',
				'id' => 'pagemeta_reservation_room_guests',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Guests','imaginem-blocks-ii'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Customer','themecore'),
				'id' => 'pagemeta_customer_checkin',
				'type' => 'customer',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Full Name','themecore'),
				'id' => 'pagemeta_full_name',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Email Address','themecore'),
				'id' => 'pagemeta_email_address',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Phone Number','themecore'),
				'id' => 'pagemeta_phone_number',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Street Address','themecore'),
				'id' => 'pagemeta_street_address',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('City','themecore'),
				'id' => 'pagemeta_city',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('State','themecore'),
				'id' => 'pagemeta_state',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Zip Code','themecore'),
				'id' => 'pagemeta_zip_code',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Country','themecore'),
				'id' => 'pagemeta_country',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
		)
	);
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