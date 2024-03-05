<?php
function atollmatrix_activityres_metadata()
{
    $atollmatrix_imagepath = plugin_dir_url(__FILE__) . 'assets/images/';

    $atollmatrix_sidebar_options = atollmatrix_generate_sidebarlist('activityres');

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

    $reservation_id = get_the_ID();

    // Generate unique booking number
    $booking_number = uniqid();

    $activityres_box = array(
        'id'       => 'activityresmeta-box',
        'title'    => esc_html__('activityres Metabox', 'atollmatrix'),
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'core',
        'fields'   => array(
            array(
                'name'         => esc_html__('Reservation Settings', 'atollmatrix'),
                'id'           => 'atollmatrix_activityres_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('activityres Settings', 'atollmatrix'),
                'std'          => '',
            ),
            array(
                'name' => esc_html__('Reservation', 'atollmatrix'),
                'id'   => 'atollmatrix_sep_page_options',
                'type' => 'seperator',
            ),
            array(
                'name'    => 'Booking Number',
                'id'      => 'atollmatrix_booking_number',
                'type'    => 'readonly',
                'class'   => 'reservation_number_box',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => $booking_number,
            ),
            array(
                'name'    => esc_html__('Reservation', 'atollmatrix'),
                'id'      => 'atollmatrix_reservation_checkin',
                'type'    => 'activity_reservation',
                'class'   => 'activity_reservation_date',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_booking_channel',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => 'Admin: Atollmatrix',
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
                'id'      => 'atollmatrix_activity_id',
                'type'    => 'offview_display_result',
                'display_by_id' => 'activity_names',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_activity_list',
                'type'    => 'activity_list_generate',
                'page_id'      => $reservation_id,
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_activity_time',
                'type'    => 'offview',
                'class'   => 'textsmall',
                'heading' => 'subhead',
                'desc'    => '',
                'std'     => '',
            ),
            array(
                'name'    => esc_html__('Reservation Status', 'atollmatrix'),
                'id'      => 'atollmatrix_reservation_status',
                'class'   => 'reservation_status',
                'type'    => 'select',
                'desc'    => esc_html__('Reservation Status', 'atollmatrix'),
                'options' => atollmatrix_get_booking_statuses(),
            ),
            array(
                'name'    => esc_html__('Reservation Sub Status', 'atollmatrix'),
                'id'      => 'atollmatrix_reservation_substatus',
                'class'   => 'reservation_status',
                'type'    => 'select',
                'desc'    => esc_html__('Reservation Sub Status', 'atollmatrix'),
                'options' => atollmatrix_get_booking_substatuses(),
            ),
            array(
                'name'     => 'Per Person Rate',
                'id'       => 'atollmatrix_reservation_rate_per_person',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'group'    => 'group',
                'datatype' => 'activityperperson',
                'desc'     => esc_html__('Per night price', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'     => '',
                'id'       => 'atollmatrix_reservation_subtotal_activity_cost',
                'type'     => 'currency',
                'class'    => 'textsmall',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'activitysubtotal',
                'desc'     => esc_html__('Subtotal', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'    => '',
                'id'      => 'atollmatrix_activity_tax',
                'page_id'      => $reservation_id,
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
                'class'    => 'reservation_total',
                'heading'  => 'subhead',
                'group'    => 'group',
                'datatype' => 'activitytotal',
                'desc'     => esc_html__('Total', 'atollmatrix'),
                'std'      => '',
            ),
            array(
                'name'     => 'Payments',
                'id'       => 'atollmatrix_reservation_room_paid',
                'type'     => 'currencyarray',
                'class'    => 'reservation_meals',
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
                'name' => esc_html__('Guest Information', 'atollmatrix'),
                'id'   => 'atollmatrix_sep_page_options',
                'type' => 'seperator',
            ),
            array(
                'name'     => 'Adults',
                'id'       => 'atollmatrix_reservation_activity_adults',
                'type'     => 'guests',
                'occupant' => 'adult',
                'datafrom' => 'roomtype',
                'maxcap'   => 'atollmatrix_max_adults',
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
                'id'       => 'atollmatrix_reservation_activity_children',
                'type'     => 'guests',
                'occupant' => 'child',
                'datafrom' => 'roomtype',
                'min'      => '0',
                'max'      => '9',
                'step'     => '1',
                'unit'     => 'children',
                'maxcap'   => 'atollmatrix_max_children',
                'class'    => 'reservation_guest',
                'heading'  => 'subhead',
                'desc'     => '',
                'std'      => '0',
            ),
            array(
                'name' => esc_html__('Registration', 'atollmatrix'),
                'id'   => 'atollmatrix_sep_page_options',
                'type' => 'seperator',
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

    $reservation_instance = new \AtollMatrix\Activity();
    if (!$reservation_instance->haveCustomer($reservation_id)) {
        $activityres_box[ 'fields' ] = array_merge($activityres_box[ 'fields' ], $customer);
    } else {
        $activityres_box[ 'fields' ] = array_merge($activityres_box[ 'fields' ], $customer_datafetch);
    }
    return $activityres_box;
}
function atollmatrix_activityres_changelog()
{

    $atollmatrix_activityres_changelog = array(
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
    return $atollmatrix_activityres_changelog;
}
/*
 * Meta options for activityres post type
 */
function atollmatrix_activityresitem_metaoptions()
{
    $activityres_box = atollmatrix_activityres_metadata();
    atollmatrix_generate_metaboxes($activityres_box, get_the_id());
}
function atollmatrix_activityresitem_changelog()
{
    $atollmatrix_activityres_changelog = atollmatrix_activityres_changelog();
    atollmatrix_generate_metaboxes($atollmatrix_activityres_changelog, get_the_id());
}

