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
                'min'  => '0',
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
                'id'   => 'atollmatrix_size',
                'type' => 'range',
                'min'  => '1',
                'max'  => '9999',
                'step' => '1',
                'unit' => 'sqf',
                'desc' => __('Size:', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Description for gallery thumbnail ( Gallery )', 'atollmatrix'),
                'id'   => 'atollmatrix_thumbnail_desc',
                'type' => 'textarea',
                'desc' => __('This description is displayed below each thumbnail.', 'atollmatrix'),
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
                'name'    => esc_html__('Bed Setup', 'atollmatrix'),
                'id'      => 'atollmatrix_bedsetup_repeat',
                'target'  => 'bedsetup',
                'type'    => 'bedsetup_repeat',
                'heading' => 'subhead',
                'desc'    => esc_html__('Bed Setup', 'atollmatrix'),
                'std'     => '',
            ),
            array(
                'name'    => esc_html__('Alternate Bed Setup #1 ( optional )', 'atollmatrix'),
                'id'      => 'atollmatrix_alt_bedsetup_repeat',
                'target'  => 'bedsetup',
                'type'    => 'bedsetup_repeat',
                'heading' => 'subhead',
                'desc'    => esc_html__('Optional Setup', 'atollmatrix'),
                'std'     => '',
            ),
            array(
                'name'    => esc_html__('Alternate Bed Setup #2 ( optional )', 'atollmatrix'),
                'id'      => 'atollmatrix_alt_bedsetup_second_repeat',
                'target'  => 'bedsetup',
                'type'    => 'bedsetup_repeat',
                'heading' => 'subhead',
                'desc'    => esc_html__('Optional Setup', 'atollmatrix'),
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
/*
 * Meta options for Room post type
 */
function atollmatrix_roomitem_metaoptions()
{
    $atollmatrix_room_box = atollmatrix_room_metadata();
    atollmatrix_generate_metaboxes($atollmatrix_room_box, get_the_id());
}
