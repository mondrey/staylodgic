<?php
function staylodgic_registry_metadata()
{

    $staylodgic_imagepath     = get_template_directory_uri() . '/framework/options/images/metaboxes/';
    $staylodgic_imagepath_alt = get_template_directory_uri() . '/framework/options/images/';

    $staylodgic_imagepath = get_template_directory_uri() . '/framework/options/images/';

    $staylodgic_registry_box = array(
        'id'       => 'registrymeta-box',
        'title'    => 'Registry Metabox',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name' => __('Booking Number', 'staylodgic'),
                'id'   => 'staylodgic_registry_bookingnumber',
                'type' => 'registration',
                'desc' => __('Booking Number', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('QR Code for URL', 'staylodgic'),
                'id'   => 'staylodgic_registry_qrcode',
                'type' => 'generate-qrcode',
                'desc' => __('QR Code', 'staylodgic'),
                'std'  => '',
            ),
            array(
                'name' => __('Registration Data', 'staylodgic'),
                'id'   => 'staylodgic_registration_data',
                'type' => 'staylodgic_registration_data',
                'desc' => __('Registration Data', 'staylodgic'),
                'std'  => '',
            ),
        ),
    );
    return $staylodgic_registry_box;
}
function staylodgic_registry_changelog()
{

    $staylodgic_registry_changelog = array(
        'id'       => 'registrymeta-box-changelog',
        'title'    => 'Registry Changelog',
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
    return $staylodgic_registry_changelog;
}
/*
 * Meta options for Registry post type
 */
function staylodgic_registryitem_metaoptions()
{
    $staylodgic_registry_box = staylodgic_registry_metadata();
    staylodgic_generate_metaboxes($staylodgic_registry_box, get_the_id());
}
function staylodgic_registryitem_changelog()
{
    $staylodgic_registry_changelog = staylodgic_registry_changelog();
    staylodgic_generate_metaboxes($staylodgic_registry_changelog, get_the_id());
}
