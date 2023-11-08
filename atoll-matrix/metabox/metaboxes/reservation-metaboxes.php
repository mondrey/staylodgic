<?php
function atollmatrix_reservations_metadata()
{
    $atollmatrix_imagepath = plugin_dir_url(__FILE__) . 'assets/images/';

    $atollmatrix_sidebar_options = atollmatrix_generate_sidebarlist('reservations');

    // Pull all the Featured into an array
    $bg_slideshow_pages = get_posts('post_type=fullscreen&orderby=title&numberposts=-1&order=ASC');

    if ($bg_slideshow_pages) {
        $options_bgslideshow[ 'none' ] = "Not Selected";
        foreach ($bg_slideshow_pages as $key => $list) {
            $custom = get_post_custom($list->ID);
            if (isset($custom[ "fullscreen_type" ][ 0 ])) {
                $slideshow_type = $custom[ "fullscreen_type" ][ 0 ];
            } else {
                $slideshow_type = "";
            }
            if ($slideshow_type != "Fullscreen-Video") {
                $options_bgslideshow[ $list->ID ] = $list->post_title;
            }
        }
    } else {
        $options_bgslideshow[ 0 ] = "Featured pages not found.";
    }

    $room_names = get_posts('post_type=atmx_room&orderby=title&numberposts=-1&order=ASC');

    if ($room_names) {
        $options_room_names[ 'none' ] = "Not Selected";
        foreach ($room_names as $key => $list) {
            $custom                          = get_post_custom($list->ID);
            $options_room_names[ $list->ID ] = $list->post_title;
        }
    } else {
        $options_room_names[ 0 ] = "Rooms not found.";
    }

    // Generate unique booking number
    $booking_number = uniqid();

    $reservations_box = array(
        'id'       => 'reservationsmeta-box',
        'title'    => esc_html__('Reservations Metabox', 'atollmatrix'),
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'core',
        'fields'   => array(
            array(
                'name'         => esc_html__('Reservation Settings', 'atollmatrix'),
                'id'           => 'atollmatrix_reservations_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('Reservations Settings', 'atollmatrix'),
                'std'          => '',
            ),
            array(
                'name' => esc_html__('Reservation Options', 'atollmatrix'),
                'id'   => 'atollmatrix_sep_page_options',
                'type' => 'seperator',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_booking_number',
                'type'    => 'readonly',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => $booking_number,
            ),
            array(
                'name'    => esc_html__('Reservation Status', 'atollmatrix'),
                'id'      => 'atollmatrix_reservation_status',
                'class'   => 'reservation_status',
                'type'    => 'select',
                'desc'    => esc_html__('Reservation Status', 'atollmatrix'),
                'options' => array(
                    'confirmed' => esc_attr__('Confirmed', 'atollmatrix'),
                    'cancelled' => esc_attr__('Cancelled', 'atollmatrix'),
                    'pending'   => esc_attr__('Pending', 'atollmatrix'),
                ),
            ),
            array(
                'name'    => esc_html__('Room', 'atollmatrix'),
                'id'      => 'atollmatrix_room_id',
                'class'   => 'room_choice',
                'type'    => 'select',
                'target'  => 'room_names',
                'desc'    => esc_html__('Room.', 'atollmatrix'),
                'options' => '',
            ),
            array(
                'name'    => esc_html__('Reservation', 'atollmatrix'),
                'id'      => 'atollmatrix_reservation_checkin',
                'type'    => 'reservation',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_checkin_date',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_checkout_date',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'     => 'Room Rate',
                'id'       => 'atollmatrix_reservation_rate_per_night',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'group'    => 'group',
                'datatype' => 'roompernight',
                'desc'     => esc_html__('Per night price', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'     => '',
                'id'       => 'atollmatrix_reservation_subtotal_room_cost',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'roomsubtotal',
                'desc'     => esc_html__('Subtotal', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_reservation_tax',
                'type'    => 'taxgenerate',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'     => '',
                'id'       => 'atollmatrix_reservation_total_room_cost',
                'type'     => 'currency',
                'inputis'  => 'readonly',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'roomtotal',
                'desc'     => esc_html__('Total', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'     => 'Payments',
                'id'       => 'atollmatrix_reservation_room_paid',
                'type'     => 'currencyarray',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'payment',
                'desc'     => '',
                'std'      => '',
            ),
            array(
                'name' => __('Notes', 'atollmatrix'),
                'id'   => 'atollmatrix_reservation_notes',
                'type' => 'textarea',
                'desc' => __('Notes.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name'         => esc_html__('Page Settings', 'atollmatrix'),
                'id'           => 'atollmatrix_page_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('Page Settings', 'atollmatrix'),
                'std'          => '',
            ),
            array(
                'name'     => 'Adults',
                'id'       => 'atollmatrix_reservation_room_adults',
                'type'     => 'guests',
                'occupant' => 'adult',
                'datafrom' => 'roomtype',
                'maxcap'   => 'atollmatrix_max_adults',
                'min'      => '1',
                'max'      => '9',
                'step'     => '1',
                'unit'     => 'adults',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'desc'     => '',
                'std'      => '0',
            ),
            array(
                'name'     => 'Children',
                'id'       => 'atollmatrix_reservation_room_children',
                'type'     => 'guests',
                'occupant' => 'child',
                'datafrom' => 'roomtype',
                'min'      => '0',
                'max'      => '9',
                'step'     => '1',
                'unit'     => 'children',
                'maxcap'   => 'atollmatrix_max_children',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'desc'     => '',
                'std'      => '0',
            ),
            array(
                'name'    => esc_html__('Customer', 'atollmatrix'),
                'id'      => 'atollmatrix_customer_choice',
                'class'   => 'customer_choice',
                'type'    => 'select',
                'desc'    => esc_html__('Customer choice', 'atollmatrix'),
                'options' => array(
                    'new'      => esc_attr__('Create new from this post', 'atollmatrix'),
                    'existing' => esc_attr__('Choose existing', 'atollmatrix'),
                ),
            ),
            array(
                'name'    => esc_html__('Choose an existing customer', 'atollmatrix'),
                'id'      => 'atollmatrix_existing_customer',
                'class'   => 'metabox_existing_customers',
                'type'    => 'select',
                'target'  => 'existing_customers',
                'desc'    => esc_html__('Choose an existing customer.', 'atollmatrix'),
                'options' => '',
            ),
        ),
    );

    $customer = atollmatrix_get_customer_array();

    $reservation_id = get_the_ID();

    $customer_datafetch = array(
        array(
            'name' => esc_html__('Customer', 'atollmatrix'),
            'id'   => 'atollmatrix_sep_page_options',
            'type' => 'seperator',
        ),
        array(
            'name'    => '',
            'id'      => $reservation_id,
            'type'    => 'get_customer_data',
            'class'   => '',
            'heading' => '',
            'desc'    => '',
            'std'     => '',
        ),
    );

    $reservation_instance = new \AtollMatrix\Reservations();
    if (!$reservation_instance->haveCustomer($reservation_id)) {
        $reservations_box[ 'fields' ] = array_merge($reservations_box[ 'fields' ], $customer);
    } else {
        $reservations_box[ 'fields' ] = array_merge($reservations_box[ 'fields' ], $customer_datafetch);
    }
    return $reservations_box;
}
function atollmatrix_reservations_changelog()
{

    $atollmatrix_rerservation_changelog = array(
        'id'       => 'reservation-box-changelog',
        'title'    => 'Reservation Changelog',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name'    => esc_html__('', 'atollmatrix'),
                'id'           => 'atollmatrix_changelog',
                'type'         => 'changelog',
                'std'          => '',
            ),
        ),
    );
    return $atollmatrix_rerservation_changelog;
}
/*
 * Meta options for Reservations post type
 */
function atollmatrix_reservationsitem_metaoptions()
{
    $reservations_box = atollmatrix_reservations_metadata();
    atollmatrix_generate_metaboxes($reservations_box, get_the_id());
}
function atollmatrix_reservationsitem_changelog()
{
    $atollmatrix_rerservations_changelog = atollmatrix_reservations_changelog();
    atollmatrix_generate_metaboxes($atollmatrix_rerservations_changelog, get_the_id());
}

