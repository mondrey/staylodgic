<?php
function staylodgic_room_metadata()
{

    $staylodgic_imagepath     = get_template_directory_uri() . '/framework/options/images/metaboxes/';
    $staylodgic_imagepath_alt = get_template_directory_uri() . '/framework/options/images/';

    $staylodgic_imagepath = get_template_directory_uri() . '/framework/options/images/';

    $staylodgic_room_box = array(
        'id'       => 'roommeta-box',
        'title'    => 'Room Metabox',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name'         => __('Fullscreen Settings', 'staylodgic'),
                'id'           => 'staylodgic_page_section_id',
                'type'         => 'break',
                'sectiontitle' => __('Page Settings', 'staylodgic'),
                'std'          => '',
            ),
            array(
                'name' => __('Add Images', 'staylodgic'),
                'id'   => 'staylodgic_image_attachments',
                'std'  => 'Upload Images',
                'type' => 'image_gallery',
                'desc' => __('Add images for slideshow.', 'staylodgic'),
            ),
            array(
                'name' => __('Title', 'staylodgic'),
                'id'   => 'staylodgic_title',
                'type' => 'text',
                'desc' => __('Title.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Rooms of this type', 'staylodgic'),
                'id'   => 'staylodgic_max_rooms_of_type',
                'type' => 'text',
                'desc' => __('Rooms of this type. This will be the maximum number avialbable for the room type.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Base Rate', 'staylodgic'),
                'id'   => 'staylodgic_base_rate',
                'type' => 'text',
                'desc' => __('Base rate for this room type.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Guests', 'staylodgic'),
                'id'   => 'staylodgic_max_guests',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'guests',
                'desc' => __('Max guests allowed.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Set Max Adult Limit on/off', 'staylodgic'),
                'id'   => 'staylodgic_max_adult_limit_status',
                'type' => 'switch',
                'desc' => __('', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Adults:', 'staylodgic'),
                'id'   => 'staylodgic_max_adults',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'adults',
                'desc' => __('Max adults allowed:', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Set Max Children Limit on/off', 'staylodgic'),
                'id'   => 'staylodgic_max_children_limit_status',
                'type' => 'switch',
                'desc' => __('', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Children:', 'staylodgic'),
                'id'   => 'staylodgic_max_children',
                'type' => 'range',
                'min'  => '0',
                'max'  => '9',
                'step' => '1',
                'unit' => 'children',
                'desc' => __('Max children allowed:', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Beds:', 'staylodgic'),
                'id'   => 'staylodgic_beds',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'bed(s)',
                'desc' => __('Beds', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Bathrooms:', 'staylodgic'),
                'id'   => 'staylodgic_bathrooms',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'bathroom(s)',
                'desc' => __('Bathrooms', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Accomodation Size:', 'staylodgic'),
                'id'   => 'staylodgic_room_size',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9999',
                'step' => '1',
                'unit' => 'sqf',
                'desc' => __('Size:', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name'    => __('Room view', 'staylodgic'),
                'id'      => 'staylodgic_roomview',
                'type'    => 'select',
                'desc'    => __('Choose room view', 'staylodgic'),
                'options' => staylodgic_get_room_views(),
            ),
            array(
                'name' => __('Room facilities ( comma seperated )', 'staylodgic'),
                'id'   => 'staylodgic_room_facilities',
                'type' => 'textarea',
                'desc' => __('Room facilities', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Description', 'staylodgic'),
                'id'   => 'staylodgic_room_desc',
                'type' => 'textarea',
                'desc' => __('Description', 'staylodgic'),
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
                'name'    => esc_html__('Bed Setup Set', 'staylodgic'),
                'id'      => 'staylodgic_alt_bedsetup',
                'target'  => 'bedsetup',
                'type'    => 'bedsetup_set',
                'heading' => 'subhead',
                'desc'    => esc_html__('Bed Setup', 'staylodgic'),
                'std'     => '',
            ),
            array(
                'name'    => __('Switch Menu', 'staylodgic'),
                'id'      => 'staylodgic_menu_choice',
                'type'    => 'select',
                'desc'    => __('Select a different menu for this page', 'staylodgic'),
                'options' => staylodgic_generate_menulist(),
            ),
        ),
    );
    return $staylodgic_room_box;
}
function staylodgic_room_changelog()
{

    $staylodgic_room_changelog = array(
        'id'       => 'roommeta-box-changelog',
        'title'    => 'Room Changelog',
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
    return $staylodgic_room_changelog;
}
/*
 * Meta options for Room post type
 */
function staylodgic_roomitem_metaoptions()
{
    $staylodgic_room_box = staylodgic_room_metadata();
    staylodgic_generate_metaboxes($staylodgic_room_box, get_the_id());
}
function staylodgic_roomitem_changelog()
{
    $staylodgic_room_changelog = staylodgic_room_changelog();
    staylodgic_generate_metaboxes($staylodgic_room_changelog, get_the_id());
}
