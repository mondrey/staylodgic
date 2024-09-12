<?php
function staylodgic_activity_metadata()
{

    $staylodgic_activity_box = array(
        'id'       => 'activitymeta-box',
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
                'name' => __('Description', 'staylodgic'),
                'id'   => 'staylodgic_activity_desc',
                'type' => 'wpeditor',
                'desc' => __('Description', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Max Guests', 'staylodgic'),
                'id'   => 'staylodgic_max_guests',
                'type' => 'range',
                'min'  => '1',
                'max'  => '100',
                'step' => '1',
                'unit' => 'guests',
                'desc' => __('Max guests allowed.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Rate', 'staylodgic'),
                'id'   => 'staylodgic_activity_rate',
                'type' => 'number',
                'desc' => __('Rate for this activity.', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Graph color', 'staylodgic'),
                'id'   => 'staylodgic_dashboard_color',
                'std'  => staylodgic_random_color_hex(),
                'type' => 'color',
                'desc' => __('Graph color which is presented in Activity Overview.', 'staylodgic'),
            ),
            array(
                'name'         => esc_html__('Page Settings', 'staylodgic'),
                'id'           => 'staylodgic_page_section_id',
                'type'         => 'break',
                'sectiontitle' => esc_html__('Page Settings', 'staylodgic'),
                'std'          => '',
            ),
            array(
                'name'    => esc_html__('Activity Schedule', 'staylodgic'),
                'id'      => 'staylodgic_activity_schedule',
                'type'    => 'actvity_schedule',
                'desc'    => esc_html__('Activity Schedule Setup', 'staylodgic'),
                'std'     => '',
            ),
        ),
    );
    return $staylodgic_activity_box;
}
function staylodgic_activity_changelog()
{

    $staylodgic_activity_changelog = array(
        'id'       => 'activitymeta-box-changelog',
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
    return $staylodgic_activity_changelog;
}
/*
 * Meta options for Activity post type
 */
function staylodgic_activityitem_metaoptions()
{
    $staylodgic_activity_box = staylodgic_activity_metadata();
    staylodgic_generate_metaboxes($staylodgic_activity_box, get_the_id());
}
function staylodgic_activityitem_changelog()
{
    $staylodgic_activity_changelog = staylodgic_activity_changelog();
    staylodgic_generate_metaboxes($staylodgic_activity_changelog, get_the_id());
}
