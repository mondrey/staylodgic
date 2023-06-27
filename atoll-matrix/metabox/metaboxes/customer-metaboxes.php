<?php
function atollmatrix_customers_metadata() {
	$atollmatrix_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$atollmatrix_sidebar_options = atollmatrix_generate_sidebarlist('customers');

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
		'title' => esc_html__('Customers Metabox','atollmatrix'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Customer Settings','atollmatrix'),
				'id' => 'atollmatrix_customers_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Customers Settings','atollmatrix'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Customer','atollmatrix'),
				'id' => 'atollmatrix_customer_checkin',
				'type' => 'customer',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Full Name','atollmatrix'),
				'id' => 'atollmatrix_full_name',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Email Address','atollmatrix'),
				'id' => 'atollmatrix_email_address',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Phone Number','atollmatrix'),
				'id' => 'atollmatrix_phone_number',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Street Address','atollmatrix'),
				'id' => 'atollmatrix_street_address',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('City','atollmatrix'),
				'id' => 'atollmatrix_city',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('State','atollmatrix'),
				'id' => 'atollmatrix_state',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Zip Code','atollmatrix'),
				'id' => 'atollmatrix_zip_code',
				'type' => 'text',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Country','atollmatrix'),
				'id' => 'atollmatrix_country',
				'type' => 'country',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => '',
				'std' => ''
			),
			array(
				'name' => esc_html__('Page Settings','atollmatrix'),
				'id' => 'atollmatrix_page_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Settings','atollmatrix'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Reservations','atollmatrix'),
				'id' => 'atollmatrix_reservation_list',
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
function atollmatrix_customersitem_metaoptions(){
	$customers_box = atollmatrix_customers_metadata();
	atollmatrix_generate_metaboxes($customers_box,get_the_id());
}
?>