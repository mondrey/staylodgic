<?php
function atollmatrix_registry_metadata()
{

    $atollmatrix_imagepath     = get_template_directory_uri() . '/framework/options/images/metaboxes/';
    $atollmatrix_imagepath_alt = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_imagepath = get_template_directory_uri() . '/framework/options/images/';

    $atollmatrix_registry_box = array(
        'id'       => 'registrymeta-box',
        'title'    => 'Registry Metabox',
        'page'     => 'page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(
            array(
                'name'         => __('Registry Settings', 'atollmatrix'),
                'id'           => 'atollmatrix_page_section_id',
                'type'         => 'break',
                'sectiontitle' => __('Page Settings', 'atollmatrix'),
                'std'          => '',
            ),
            array(
                'name' => __('Booking Number', 'atollmatrix'),
                'id'   => 'atollmatrix_registry_bookingnumber',
                'type' => 'text',
                'desc' => __('Booking Number', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('Registration Data', 'atollmatrix'),
                'id'   => 'atollmatrix_registration_data',
                'type' => 'atollmatrix_registration_data',
                'desc' => __('Registration Data', 'atollmatrix'),
                'std'  => '',
            ),
            array(
                'name' => __('QR Code for URL', 'atollmatrix'),
                'id'   => 'atollmatrix_registry_qrcode',
                'type' => 'generate-qrcode',
                'desc' => __('QR Code', 'atollmatrix'),
                'std'  => '',
            ),
        ),
    );
    return $atollmatrix_registry_box;
}
function atollmatrix_registry_changelog()
{

    $atollmatrix_registry_changelog = array(
        'id'       => 'registrymeta-box-changelog',
        'title'    => 'Registry Changelog',
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
    return $atollmatrix_registry_changelog;
}
/*
 * Meta options for Registry post type
 */
function atollmatrix_registryitem_metaoptions()
{
    $atollmatrix_registry_box = atollmatrix_registry_metadata();
    atollmatrix_generate_metaboxes($atollmatrix_registry_box, get_the_id());
}
function atollmatrix_registryitem_changelog()
{
    $atollmatrix_registry_changelog = atollmatrix_registry_changelog();
    atollmatrix_generate_metaboxes($atollmatrix_registry_changelog, get_the_id());
}
