<?php
function themecore_photostory_metadata() {

	$mtheme_imagepath =  get_template_directory_uri() . '/framework/options/images/metaboxes/';
	$mtheme_imagepath_alt =  get_template_directory_uri() . '/framework/options/images/';

	$mtheme_imagepath =  get_template_directory_uri() . '/framework/options/images/';

	$mtheme_photostory_box = array(
		'id' => 'photostorymeta-box',
		'title' => 'Photostory Metabox',
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => __('Fullscreen Settings','themecore'),
				'id' => 'pagemeta_page_section_id',
				'type' => 'break',
				'sectiontitle' => __('Page Settings','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Add Images','themecore'),
				'id' => 'pagemeta_image_attachments',
				'std' => 'Upload Images',
				'type' => 'image_gallery',
				'desc' => __('Add images for slideshow.','themecore')
			),
			array(
				'name' => __('Description for gallery thumbnail ( Story Gallery )','themecore'),
				'id' => 'pagemeta_thumbnail_desc',
				'type' => 'textarea',
				'desc' => __('This description is displayed below each thumbnail.','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Page Background color','themecore'),
				'id' => 'pagemeta_pagebackground_color',
				'type' => 'color',
				'desc' => __('Page background color','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Display Titles & Descrition','themecore'),
				'id' => 'pagemeta_slideshow_titledesc',
				'type' => 'select',
				'std' => 'enable',
				'desc' => __('Display title and description','themecore'),
				'options' => array(
					'enable' => 'Enable',
					'disable' => 'Disable')
			),
			array(
				'name' => esc_html__('Fotorama Fill mode','themecore'),
				'id' => 'pagemeta_fotorama_fill',
				'type' => 'select',
				'std' => 'enable',
				'desc' => esc_html__('Fotorama Fill mode','themecore'),
				'options' => array(
					'cover' => esc_attr__('Fill','themecore'),
					'contain' => esc_attr__('Fit','themecore')
					)
				),
			array(
				'name' => esc_html__('Fotorama Thumbnails','themecore'),
				'id' => 'pagemeta_fotorama_thumbnails',
				'type' => 'select',
				'std' => 'enable',
				'class' => 'page_type-fotorama page_type-trigger',
				'desc' => esc_html__('Fotorama Thumbnails','themecore'),
				'options' => array(
					'enable' => esc_attr__('Enable','themecore'),
					'disable' => esc_attr__('Disable','themecore')
					)
				),
			array(
				'name' => __('Switch Menu','themecore'),
				'id' => 'pagemeta_menu_choice',
				'type' => 'select',
				'desc' => __('Select a different menu for this page','themecore'),
				'options' => themecore_generate_menulist()
			),
		)
	);
	return $mtheme_photostory_box;
}
/*
* Meta options for Photostory post type
*/
function themecore_photostoryitem_metaoptions(){
	$mtheme_photostory_box = themecore_photostory_metadata();
	themecore_generate_metaboxes($mtheme_photostory_box,get_the_id());
}
?>