<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly
function staylodgic_customers_metadata() {

	$room_names = get_posts( 'post_type=staylodgic_rooms&orderby=title&numberposts=-1&order=ASC' );

	if ( $room_names ) {
		$options_room_names['none'] = 'Not Selected';
		foreach ( $room_names as $key => $list ) {
			$custom                          = get_post_custom( $list->ID );
			$options_room_names[ $list->ID ] = $list->post_title;
		}
	} else {
		$options_room_names[0] = 'Rooms not found.';
	}

	$customer_id = get_the_ID();

	$customers_box = array(
		'id'       => 'customersmeta-box',
		'title'    => esc_html__( 'Customers Metabox', 'staylodgic' ),
		'page'     => 'page',
		'context'  => 'normal',
		'priority' => 'core',
		'fields'   => array(
			array(
				'name'         => esc_html__( 'Customer Settings', 'staylodgic' ),
				'id'           => 'staylodgic_customers_section_id',
				'type'         => 'break',
				'sectiontitle' => esc_html__( 'Customers Settings', 'staylodgic' ),
				'std'          => '',
			),
			array(
				'name'    => esc_html__( 'Customer', 'staylodgic' ),
				'id'      => 'staylodgic_customer_checkin',
				'type'    => 'customer',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Full Name', 'staylodgic' ),
				'id'      => 'staylodgic_full_name',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Email Address', 'staylodgic' ),
				'id'      => 'staylodgic_email_address',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Phone Number', 'staylodgic' ),
				'id'      => 'staylodgic_phone_number',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Street Address', 'staylodgic' ),
				'id'      => 'staylodgic_street_address',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'City', 'staylodgic' ),
				'id'      => 'staylodgic_city',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'State', 'staylodgic' ),
				'id'      => 'staylodgic_state',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Zip Code', 'staylodgic' ),
				'id'      => 'staylodgic_zip_code',
				'type'    => 'text',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'    => esc_html__( 'Country', 'staylodgic' ),
				'id'      => 'staylodgic_country',
				'type'    => 'country',
				'class'   => 'textsmall',
				'heading' => 'subhead',
				'desc'    => '',
				'std'     => '',
			),
			array(
				'name'         => esc_html__( 'Page Settings', 'staylodgic' ),
				'id'           => 'staylodgic_page_section_id',
				'type'         => 'break',
				'sectiontitle' => esc_html__( 'Page Settings', 'staylodgic' ),
				'std'          => '',
			),
			array(
				'name'        => esc_html__( 'Reservations', 'staylodgic' ),
				'id'          => 'staylodgic_reservation_list',
				'type'        => 'reservation_for_customer',
				'class'       => 'textsmall',
				'heading'     => 'subhead',
				'customer_id' => $customer_id,
				'desc'        => '',
				'std'         => '',
			),
		),
	);
	return $customers_box;
}
/*
 * Meta options for Customers post type
 */
function staylodgic_customersitem_metaoptions() {
	$customers_box = staylodgic_customers_metadata();
	staylodgic_generate_metaboxes( $customers_box, get_the_id() );
}
