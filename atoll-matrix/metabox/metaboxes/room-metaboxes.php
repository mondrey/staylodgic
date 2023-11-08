<?php
function atollmatrix_room_metadata()
{

    $atollmatrix_imagepath     = get_template_directory_uri() . '/framework/options/images/metaboxes/';
    $atollmatrix_imagepath_alt = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_imagepath = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_room_box = array(
        'id'       => 'roommeta-box',
        'title'    => 'Room Metabox',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name'         => __('Fullscreen Settings', 'atollmatrix'),
                'id'           => 'atollmatrix_page_section_id',
                'type'         => 'break',
                'sectiontitle' => __('Page Settings', 'atollmatrix'),
                'std'          => '',
            ),
            array(
                'name' => __('Add Images', 'atollmatrix'),
                'id'   => 'atollmatrix_image_attachments',
                'std'  => 'Upload Images',
                'type' => 'image_gallery',
                'desc' => __('Add images for slideshow.', 'atollmatrix'),
            ),
            array(
                'name' => __('Title', 'atollmatrix'),
                'id'   => 'atollmatrix_title',
                'type' => 'text',
                'desc' => __('Title.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Rooms of this type', 'atollmatrix'),
                'id'   => 'atollmatrix_max_rooms_of_type',
                'type' => 'text',
                'desc' => __('Rooms of this type. This will be the maximum number avialbable for the room type.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Base Rate', 'atollmatrix'),
                'id'   => 'atollmatrix_base_rate',
                'type' => 'text',
                'desc' => __('Base rate for this room type.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Guests', 'atollmatrix'),
                'id'   => 'atollmatrix_max_guests',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'guests',
                'desc' => __('Max guests allowed.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Set Max Adult Limit on/off', 'atollmatrix'),
                'id'   => 'atollmatrix_max_adult_limit_status',
                'type' => 'switch',
                'desc' => __('', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Adults:', 'atollmatrix'),
                'id'   => 'atollmatrix_max_adults',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'adults',
                'desc' => __('Max adults allowed:', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Set Max Children Limit on/off', 'atollmatrix'),
                'id'   => 'atollmatrix_max_children_limit_status',
                'type' => 'switch',
                'desc' => __('', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Children:', 'atollmatrix'),
                'id'   => 'atollmatrix_max_children',
                'type' => 'range',
                'min'  => '0',
                'max'  => '9',
                'step' => '1',
                'unit' => 'children',
                'desc' => __('Max children allowed:', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Beds:', 'atollmatrix'),
                'id'   => 'atollmatrix_beds',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'bed(s)',
                'desc' => __('Beds', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Bathrooms:', 'atollmatrix'),
                'id'   => 'atollmatrix_bathrooms',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9',
                'step' => '1',
                'unit' => 'bathroom(s)',
                'desc' => __('Bathrooms', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Accomodation Size:', 'atollmatrix'),
                'id'   => 'atollmatrix_room_size',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9999',
                'step' => '1',
                'unit' => 'sqf',
                'desc' => __('Size:', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name'    => __('Room view', 'atollmatrix'),
                'id'      => 'atollmatrix_roomview',
                'type'    => 'select',
                'desc'    => __('Choose room view', 'atollmatrix'),
                'options' => atollmatrix_get_room_views(),
            ),
            array(
                'name' => __('Room facilities ( comma seperated )', 'atollmatrix'),
                'id'   => 'atollmatrix_room_facilities',
                'type' => 'textarea',
                'desc' => __('Room facilities', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Description', 'atollmatrix'),
                'id'   => 'atollmatrix_room_desc',
                'type' => 'textarea',
                'desc' => __('Description', 'atollmatrix'),
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
                'name'    => esc_html__('Bed Setup Set', 'atollmatrix'),
                'id'      => 'atollmatrix_alt_bedsetup',
                'target'  => 'bedsetup',
                'type'    => 'bedsetup_set',
                'heading' => 'subhead',
                'desc'    => esc_html__('Bed Setup', 'atollmatrix'),
                'std'     => '',
            ),
            array(
                'name'    => __('Switch Menu', 'atollmatrix'),
                'id'      => 'atollmatrix_menu_choice',
                'type'    => 'select',
                'desc'    => __('Select a different menu for this page', 'atollmatrix'),
                'options' => atollmatrix_generate_menulist(),
            ),
        ),
    );
    return $atollmatrix_room_box;
}
function atollmatrix_room_changelog()
{

    $atollmatrix_room_changelog = array(
        'id'       => 'roommeta-box-changelog',
        'title'    => 'Room Changelog',
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
    return $atollmatrix_room_changelog;
}
/*
 * Meta options for Room post type
 */
function atollmatrix_roomitem_metaoptions()
{
    $atollmatrix_room_box = atollmatrix_room_metadata();
    atollmatrix_generate_metaboxes($atollmatrix_room_box, get_the_id());
}
function atollmatrix_roomitem_changelog()
{
    $atollmatrix_room_changelog = atollmatrix_room_changelog();
    atollmatrix_generate_metaboxes($atollmatrix_room_changelog, get_the_id());
}
