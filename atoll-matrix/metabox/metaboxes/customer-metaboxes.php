<?php
function themecore_customers_metadata() {
	$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$mtheme_sidebar_options = themecore_generate_sidebarlist('customers');

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

	$customer_id = get_the_ID();

	$customers_box = array(
		'id' => 'customersmeta-box',
		'title' => esc_html__('Customers Metabox','themecore'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Customer Settings','themecore'),
				'id' => 'pagemeta_customers_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Customers Settings','themecore'),
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
				'name' => esc_html__('Booking Name','themecore'),
				'id' => 'pagemeta_booking_number',
				'type' => 'text',
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
				'type' => 'country',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
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
				'name' => esc_html__('Reservations','themecore'),
				'id' => 'pagemeta_reservation_list',
				'type' => 'reservation_for_customer',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'customer_id' => $customer_id,
				'desc' => '',
				'std' => ''
			),
		)
	);
	return $customers_box;
}
/*
* Meta options for Customers post type
*/
function themecore_customersitem_metaoptions(){
	$customers_box = themecore_customers_metadata();
	themecore_generate_metaboxes($customers_box,get_the_id());
}
?>