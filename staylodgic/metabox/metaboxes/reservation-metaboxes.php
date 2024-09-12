<?php
function staylodgic_reservations_metadata()
{

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

    $room_names = get_posts('post_type=slgc_room&orderby=title&numberposts=-1&order=ASC');

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

    $reservation_id = get_the_ID();

    $reservations_box = array(
        'id'       => 'reservationsmeta-box',
        'title'    => esc_html__('Reservations Metabox', 'staylodgic'),
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'core',
        'fields'   => array(
            array(
                'name'         => esc_html__('Reservation Settings', 'staylodgic'),
                'id'           => 'staylodgic_reservations_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('Reservations Settings', 'staylodgic'),
                'std'          => '',
            ),
            array(
                'name' => esc_html__('Reservation', 'staylodgic'),
                'id'   => 'staylodgic_sep_page_options',
                'type' => 'seperator',
            ),
            array(
                'name'    => 'Booking Number',
                'id'      => 'staylodgic_booking_number',
                'type'    => 'readonly',
                'class'   => 'reservation_number_box',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => $booking_number,
            ),
            array(
                'name'    => esc_html__('Reservation', 'staylodgic'),
                'id'      => 'staylodgic_reservation_checkin',
                'type'    => 'reservation',
                'class'   => 'reservation_date',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_checkin_date',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_checkout_date',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => esc_html__('Room', 'staylodgic'),
                'id'      => 'staylodgic_room_id',
                'class'   => 'room_choice',
                'type'    => 'select',
                'target'  => 'room_names',
                'desc'    => esc_html__('Room.', 'staylodgic'),
                'options' => '',
            ),
            array(
                'name'    => esc_html__('Reservation Status', 'staylodgic'),
                'id'      => 'staylodgic_reservation_status',
                'class'   => 'reservation_status',
                'type'    => 'select',
                'desc'    => esc_html__('Reservation Status', 'staylodgic'),
                'options' => staylodgic_get_booking_statuses(),
            ),
            array(
                'name'    => esc_html__('Reservation Sub Status', 'staylodgic'),
                'id'      => 'staylodgic_reservation_substatus',
                'class'   => 'reservation_status',
                'type'    => 'select',
                'desc'    => esc_html__('Reservation Sub Status', 'staylodgic'),
                'options' => staylodgic_get_booking_substatuses(),
            ),
            array(
                'name'    => 'Referral / Channel',
                'id'      => 'staylodgic_booking_channel',
                'type'    => 'text',
                'class'   => 'reservation_status',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => 'Admin: Staylodgic',
            ),
            array(
                'name'     => 'Adults',
                'id'       => 'staylodgic_reservation_room_adults',
                'type'     => 'guests',
                'occupant' => 'adult',
                'datafrom' => 'roomtype',
                'maxcap'   => 'staylodgic_max_adults',
                'min'      => '1',
                'max'      => '9',
                'step'     => '1',
                'unit'     => 'adults',
                'class'    => 'reservation_guest',
                'heading'  => 'subhead',
                'desc'     => '',
                'std'      => '1',
            ),
            array(
                'name'     => 'Children',
                'id'       => 'staylodgic_reservation_room_children',
                'type'     => 'guests',
                'occupant' => 'child',
                'datafrom' => 'roomtype',
                'min'      => '0',
                'max'      => '9',
                'step'     => '1',
                'unit'     => 'children',
                'maxcap'   => 'staylodgic_max_children',
                'class'    => 'reservation_guest',
                'heading'  => 'subhead',
                'desc'     => '',
                'std'      => '0',
            ),
            array(
                'name'     => 'Bed Layout',
                'id'       => 'staylodgic_reservation_room_bedlayout',
                'type'     => 'bedlayout',
                'class'    => 'textsmall',
                'desc'     => '',
                'std'      => '',
            ),
            array(
                'name'     => 'Meals Included',
                'id'       => 'staylodgic_reservation_room_mealplan_included',
                'type'     => 'mealplan_included',
                'class'    => 'reservation_meals',
                'desc'     => esc_html__('Included Meals', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'     => 'Meal Plan',
                'id'       => 'staylodgic_reservation_room_mealplan',
                'type'     => 'mealplan',
                'class'    => 'reservation_meals',
                'desc'     => esc_html__('Meal Plan', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'     => 'Room Rate',
                'id'       => 'staylodgic_reservation_rate_per_night',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'group'    => 'group',
                'datatype' => 'roompernight',
                'desc'     => esc_html__('Per night price', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'     => '',
                'id'       => 'staylodgic_reservation_subtotal_room_cost',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'roomsubtotal',
                'desc'     => esc_html__('Subtotal', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_reservation_tax',
                'page_id'      => $reservation_id,
                'type'    => 'taxgenerate',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'     => '',
                'id'       => 'staylodgic_reservation_total_room_cost',
                'type'     => 'currency',
                'inputis'  => 'readonly',
                'class'    => 'reservation_total',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'roomtotal',
                'desc'     => esc_html__('Total', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'     => 'Payments',
                'id'       => 'staylodgic_reservation_room_paid',
                'type'     => 'currencyarray',
                'class'    => 'reservation_meals',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'payment',
                'desc'     => '',
                'std'      => '',
            ),
            array(
                'name' => __('Notes', 'staylodgic'),
                'id'   => 'staylodgic_reservation_notes',
                'type' => 'textarea',
                'desc' => __('Notes.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name'         => esc_html__('Page Settings', 'staylodgic'),
                'id'           => 'staylodgic_page_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('Page Settings', 'staylodgic'),
                'std'          => '',
            ),
            array(
                'name'    => esc_html__('Customer', 'staylodgic'),
                'id'      => 'staylodgic_customer_choice',
                'class'   => 'customer_choice',
                'type'    => 'select',
                'desc'    => esc_html__('Customer choice', 'staylodgic'),
                'options' => staylodgic_customer_select_choices( $reservation_id ),
            ),
        ),
    );

    $customer = staylodgic_get_customer_array();

    $reservation_id = get_the_ID();

    $customer_existing = array(
        array(
            'name'    => esc_html__('Choose an existing customer', 'staylodgic'),
            'id'      => 'staylodgic_existing_customer',
            'class'   => 'metabox_existing_customers',
            'type'    => 'select',
            'target'  => 'existing_customers',
            'desc'    => esc_html__('Choose an existing customer.', 'staylodgic'),
            'options' => '',
        ),
    );
    $customer_datafetch = array(
        array(
            'name' => esc_html__('Registration', 'staylodgic'),
            'id'   => 'staylodgic_sep_page_options',
            'type' => 'seperator',
        ),
        array(
            'name'    => esc_html__('Guest Registration', 'staylodgic'),
            'id'      => 'staylodgic_reservation_registration',
            'class'   => 'reservation_registration',
            'type'    => 'reservation_registration',
            'desc'    => esc_html__('Guest registration.', 'staylodgic'),
        ),
        array(
            'name'    => esc_html__('Choose an existing customer', 'staylodgic'),
            'id'      => 'staylodgic_existing_customer',
            'class'   => 'metabox_existing_customers',
            'type'    => 'select',
            'target'  => 'existing_customers',
            'desc'    => esc_html__('Choose an existing customer.', 'staylodgic'),
            'options' => '',
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

    $reservation_instance = new \Staylodgic\Reservations();
    if (!$reservation_instance->haveCustomer($reservation_id)) {
        $reservations_box[ 'fields' ] = array_merge($reservations_box[ 'fields' ], $customer_existing);
        $reservations_box[ 'fields' ] = array_merge($reservations_box[ 'fields' ], $customer);
    } else {
        $reservations_box[ 'fields' ] = array_merge($reservations_box[ 'fields' ], $customer_datafetch);
    }
    return $reservations_box;
}

function staylodgic_customer_select_choices( $reservation_id ) {

    $reservation_instance = new \Staylodgic\Reservations();
    if (!$reservation_instance->haveCustomer($reservation_id)) {
        $choices = array(
            'new'      => esc_attr__('Create new from this post', 'staylodgic'),
            'existing' => esc_attr__('Choose existing', 'staylodgic'),
        );
    } else {
        $choices = array(
            'new'      => esc_attr__('Current selected', 'staylodgic'),
            'existing' => esc_attr__('Choose existing', 'staylodgic'),
        );
    }

    return $choices;

}
function staylodgic_reservations_changelog()
{

    $staylodgic_rerservation_changelog = array(
        'id'       => 'reservation-box-changelog',
        'title'    => 'Reservation Changelog',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name'    => esc_html__('', 'staylodgic'),
                'id'           => 'staylodgic_changelog',
                'type'         => 'changelog',
                'std'          => '',
            ),
        ),
    );
    return $staylodgic_rerservation_changelog;
}
/*
 * Meta options for Reservations post type
 */
function staylodgic_reservationsitem_metaoptions()
{
    $reservations_box = staylodgic_reservations_metadata();
    staylodgic_generate_metaboxes($reservations_box, get_the_id());
}
function staylodgic_reservationsitem_changelog()
{
    $staylodgic_rerservations_changelog = staylodgic_reservations_changelog();
    staylodgic_generate_metaboxes($staylodgic_rerservations_changelog, get_the_id());
}

