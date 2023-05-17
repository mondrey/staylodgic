<?php
function themecore_room_metadata() {

	$mtheme_imagepath =  get_template_directory_uri() . '/framework/options/images/metaboxes/';
	$mtheme_imagepath_alt =  get_template_directory_uri() . '/framework/options/images/';

	$mtheme_imagepath =  get_template_directory_uri() . '/framework/options/images/';

	$mtheme_room_box = array(
		'id' => 'roommeta-box',
		'title' => 'Room Metabox',
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
				'name' => __('Title','themecore'),
				'id' => 'pagemeta_title',
				'type' => 'text',
				'desc' => __('Title.','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Rooms of this type','themecore'),
				'id' => 'pagemeta_max_rooms_of_type',
				'type' => 'text',
				'desc' => __('Rooms of this type. This will be the maximum number avialbable for the room type.','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Base Rate','themecore'),
				'id' => 'pagemeta_base_rate',
				'type' => 'text',
				'desc' => __('Base rate for this room type.','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Guests','themecore'),
				'id' => 'pagemeta_guests',
				'type' => 'text',
				'desc' => __('Guests: (max. guests allowed).','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Children:','themecore'),
				'id' => 'pagemeta_children',
				'type' => 'text',
				'desc' => __('Children:  (free child)','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Beds:','themecore'),
				'id' => 'pagemeta_beds',
				'type' => 'text',
				'desc' => __('Beds','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Bathrooms:','themecore'),
				'id' => 'pagemeta_bathrooms',
				'type' => 'text',
				'desc' => __('Bathrooms','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Description for gallery thumbnail ( Gallery )','themecore'),
				'id' => 'pagemeta_thumbnail_desc',
				'type' => 'textarea',
				'desc' => __('This description is displayed below each thumbnail.','themecore'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Page Settings','themecore'),
				'id' => 'pagemeta_page_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => __('Active','themecore'),
				'id' => 'pagemeta_slideshow_titledesc',
				'type' => 'select',
				'std' => 'enable',
				'desc' => __('Display title and description','themecore'),
				'options' => array(
					'enable' => 'Enable',
					'disable' => 'Disable')
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
	return $mtheme_room_box;
}
/*
* Meta options for Room post type
*/
function themecore_roomitem_metaoptions(){
	$mtheme_room_box = themecore_room_metadata();
	themecore_generate_metaboxes($mtheme_room_box,get_the_id());
}
?>