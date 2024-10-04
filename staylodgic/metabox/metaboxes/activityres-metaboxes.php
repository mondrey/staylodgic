<?php
function staylodgic_activityres_metadata()
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

    $reservation_id = get_the_ID();

    // Generate unique booking number
    $booking_number = uniqid();

    $activityres_box = array(
        'id'       => 'activityresmeta-box',
        'title'    => esc_html__('activityres Metabox', 'staylodgic'),
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'core',
        'fields'   => array(
            array(
                'name'         => esc_html__('Reservation Settings', 'staylodgic'),
                'id'           => 'staylodgic_activityres_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('activityres Settings', 'staylodgic'),
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
                'type'    => 'activity_reservation',
                'class'   => 'activity_reservation_date',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'     => 'Adults',
                'id'       => 'staylodgic_reservation_activity_adults',
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
                'id'       => 'staylodgic_reservation_activity_children',
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
                'name'    => 'Referral / Channel',
                'id'      => 'staylodgic_booking_channel',
                'type'    => 'text',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => 'Admin: Staylodgic',
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
                'name'    => 'Ticket',
                'id'      => 'staylodgic_activity_id',
                'type'    => 'offview_display_ticket_result',
                'page_id'      => $reservation_id,
                'display_by_id' => 'activity_names',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_activity_list',
                'type'    => 'activity_list_generate',
                'page_id'      => $reservation_id,
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_activity_time',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
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
                'name'     => 'Per Person Rate',
                'id'       => 'staylodgic_reservation_rate_per_person',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'group'    => 'group',
                'datatype' => 'activityperperson',
                'desc'     => esc_html__('Per night price', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'     => '',
                'id'       => 'staylodgic_reservation_subtotal_activity_cost',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'activitysubtotal',
                'desc'     => esc_html__('Subtotal', 'staylodgic'),
                'std'      => '',
            ),
            array(
                'name'    => '',
                'id'      => 'staylodgic_activity_tax',
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
                'datatype' => 'activitytotal',
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
                'options' => array(
                    'new'      => esc_attr__('Create new from this post', 'staylodgic'),
                    'existing' => esc_attr__('Choose existing', 'staylodgic'),
                ),
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
        ),
    );

    $customer = staylodgic_get_customer_array();

    $customer_datafetch = array(
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

    $reservation_instance = new \Staylodgic\Activity();
    if (!$reservation_instance->have_customer($reservation_id)) {
        $activityres_box[ 'fields' ] = array_merge($activityres_box[ 'fields' ], $customer);
    } else {
        $activityres_box[ 'fields' ] = array_merge($activityres_box[ 'fields' ], $customer_datafetch);
    }
    return $activityres_box;
}
function staylodgic_activityres_changelog()
{

    $staylodgic_activityres_changelog = array(
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
    return $staylodgic_activityres_changelog;
}
/*
 * Meta options for activityres post type
 */
function staylodgic_activityresitem_metaoptions()
{
    $activityres_box = staylodgic_activityres_metadata();
    staylodgic_generate_metaboxes($activityres_box, get_the_id());
}
function staylodgic_activityresitem_changelog()
{
    $staylodgic_activityres_changelog = staylodgic_activityres_changelog();
    staylodgic_generate_metaboxes($staylodgic_activityres_changelog, get_the_id());
}

