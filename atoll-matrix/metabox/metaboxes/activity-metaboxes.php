<?php
function atollmatrix_activity_metadata()
{

    $atollmatrix_imagepath     = get_template_directory_uri() . '/framework/options/images/metaboxes/';
    $atollmatrix_imagepath_alt = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_imagepath = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_activity_box = array(
        'id'       => 'activitymeta-box',
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
                'name' => __('Description', 'atollmatrix'),
                'id'   => 'atollmatrix_activity_desc',
                'type' => 'textarea',
                'desc' => __('Description', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Guests', 'atollmatrix'),
                'id'   => 'atollmatrix_max_guests',
                'type' => 'range',
                'min'  => '1',
                'max'  => '100',
                'step' => '1',
                'unit' => 'guests',
                'desc' => __('Max guests allowed.', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Rate', 'atollmatrix'),
                'id'   => 'atollmatrix_activity_rate',
                'type' => 'text',
                'desc' => __('Rate for this activity.', 'atollmatrix'),
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
                'name'    => esc_html__('Activity Schedule', 'atollmatrix'),
                'id'      => 'atollmatrix_activity_schedule',
                'type'    => 'actvity_schedule',
                'desc'    => esc_html__('Activity Schedule Setup', 'atollmatrix'),
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
    return $atollmatrix_activity_box;
}
function atollmatrix_activity_changelog()
{

    $atollmatrix_activity_changelog = array(
        'id'       => 'activitymeta-box-changelog',
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
    return $atollmatrix_activity_changelog;
}
/*
 * Meta options for Room post type
 */
function atollmatrix_activityitem_metaoptions()
{
    $atollmatrix_activity_box = atollmatrix_activity_metadata();
    atollmatrix_generate_metaboxes($atollmatrix_activity_box, get_the_id());
}
function atollmatrix_activityitem_changelog()
{
    $atollmatrix_activity_changelog = atollmatrix_activity_changelog();
    atollmatrix_generate_metaboxes($atollmatrix_activity_changelog, get_the_id());
}
