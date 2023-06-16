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
				'name' => __('Max Guests','themecore'),
				'id' => 'pagemeta_max_guests',
				'type' => 'range',
				'min' => '1',
				'max' => '9',
				'step' => '1',
				'unit' => 'guests',
				'desc' => __('Max guests allowed.','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Set Max Adult Limit on/off','themecore'),
				'id' => 'pagemeta_max_adult_limit_status',
				'type' => 'switch',
				'desc' => __('','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Max Adults:','themecore'),
				'id' => 'pagemeta_max_adults',
				'type' => 'range',
				'min' => '0',
				'max' => '9',
				'step' => '1',
				'unit' => 'adults',
				'desc' => __('Max adults allowed:','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Set Max Children Limit on/off','themecore'),
				'id' => 'pagemeta_max_children_limit_status',
				'type' => 'switch',
				'desc' => __('','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Max Children:','themecore'),
				'id' => 'pagemeta_max_children',
				'type' => 'range',
				'min' => '0',
				'max' => '9',
				'step' => '1',
				'unit' => 'children',
				'desc' => __('Max children allowed:','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Beds:','themecore'),
				'id' => 'pagemeta_beds',
				'type' => 'range',
				'min' => '1',
				'max' => '9',
				'step' => '1',
				'unit' => 'bed(s)',
				'desc' => __('Beds','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Bathrooms:','themecore'),
				'id' => 'pagemeta_bathrooms',
				'type' => 'range',
				'min' => '1',
				'max' => '9',
				'step' => '1',
				'unit' => 'bathroom(s)',
				'desc' => __('Bathrooms','themecore'),
				'std' => ''
			),
			array(
				'name' => __('Accomodation Size:','themecore'),
				'id' => 'pagemeta_size',
				'type' => 'range',
				'min' => '1',
				'max' => '9999',
				'step' => '1',
				'unit' => 'sqf',
				'desc' => __('Size:','themecore'),
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
				'name' => esc_html__('Bed Setup','themecore'),
				'id' => 'pagemeta_bedsetup_repeat',
				'target' => 'bedsetup',
				'type' => 'bedsetup_repeat',
				'heading' => 'subhead',
				'desc' => esc_html__('Bed Setup','themecore'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Alternate Bed Setup #1 ( optional )','themecore'),
				'id' => 'pagemeta_alt_bedsetup_repeat',
				'target' => 'bedsetup',
				'type' => 'bedsetup_repeat',
				'heading' => 'subhead',
				'desc' => esc_html__('Optional Setup','themecore'),
				'std' => ''
			),
			array(
				'name' => esc_html__('Alternate Bed Setup #2 ( optional )','themecore'),
				'id' => 'pagemeta_alt_bedsetup_second_repeat',
				'target' => 'bedsetup',
				'type' => 'bedsetup_repeat',
				'heading' => 'subhead',
				'desc' => esc_html__('Optional Setup','themecore'),
				'std' => ''
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