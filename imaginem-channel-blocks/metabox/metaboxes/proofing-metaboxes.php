<?php
function themecore_proofing_metadata() {
	$mtheme_sidebar_options = themecore_generate_sidebarlist("proofing");

// Pull all the Featured into an array
	$bg_slideshow_pages = get_posts('post_type=fullscreen&orderby=title&numberposts=-1&order=ASC');

	if ($bg_slideshow_pages) {
		$options_bgslideshow['none'] = "Not Selected";
		foreach($bg_slideshow_pages as $key => $list) {
			$custom = get_post_custom($list->ID);
			if ( isset($custom["fullscreen_type"][0]) ) { 
				$slideshow_type=$custom["fullscreen_type"][0]; 
			} else {
				$slideshow_type="";
			}
			if ($slideshow_type<>"Fullscreen-Video") {
				$options_bgslideshow[$list->ID] = $list->post_title;
			}
		}
	} else {
		$options_bgslideshow[0]="Featured pages not found.";
	}

	$client_names = get_posts('post_type=clients&orderby=title&numberposts=-1&order=ASC');
	if ($client_names) {
		$options_client_names['none'] = "Not Selected";
		foreach($client_names as $key => $list) {
			$custom = get_post_custom($list->ID);
			$options_client_names[$list->ID] = $list->post_title;
		}
	} else {
		$options_client_names[0]="Featured pages not found.";
	}

	$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$proofing_box = array(
		'id' => 'proofingmeta-box',
		'title' => esc_html__('Proofing Metabox','themecore'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Proofing Settings','themecore'),
				'id' => 'pagemeta_proofing_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Proofing Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Add Proofing Images','themecore'),
				'id' => 'pagemeta_image_attachments',
				'std' => esc_html__('Upload Images','themecore'),
				'type' => 'proofing_gallery',
				'desc' => esc_html__('Attach images for proofing.','themecore')
				),
			array(
				'name' => esc_html__('Proofing: Selected images and filenames','themecore'),
				'id' => 'pagemeta_selected_proofing_images',
				'std' => '',
				'type' => 'selected_proofing_images',
				'desc' => esc_html__('Use to locate images from a large collection.','themecore')
			),
			array(
				'name' => esc_html__('Page Style','themecore'),
				'id' => 'pagemeta_pagestyle',
				'type' => 'image',
				'std' => 'rightsidebar',
				'desc' => esc_html__('Note: Edge to Edge for Elementor Pagebuilder Layout','themecore'),
				'options' => array(
					'rightsidebar' => $mtheme_imagepath . 'page-right-sidebar.png',
					'leftsidebar' => $mtheme_imagepath . 'page-left-sidebar.png',
					'nosidebar' => $mtheme_imagepath . 'page-no-sidebar.png',
					'edge-to-edge' => $mtheme_imagepath . 'page-edge-to-edge.png')
				),
			array(
				'name' => esc_html__('Assign a Client','themecore'),
				'id' => 'pagemeta_client_names',
				'type' => 'select',
				'target' => 'client_names',
				'desc' => esc_html__('Note :Select client PhotoProofing is for.','themecore'),
				'options' => ''
				),
			array(
				'name' => esc_html__('Proofing Date','themecore'),
				'id' => 'pagemeta_proofing_startdate',
				'type' => 'datepicker',
				'class' => 'textsmall',
				'heading' => 'subhead',
				'desc' => esc_html__('Start date','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Location','themecore'),
				'id' => 'pagemeta_proofing_location',
				'type' => 'text',
				'heading' => 'subhead',
				'desc' => esc_html__('Location','themecore'),
				'std' => ''
				),
			array(
				'name' => __('Description for Infobox','themecore'),
				'id' => 'pagemeta_thumbnail_desc',
				'type' => 'textarea',
				'desc' => __('This description for infobox.','themecore'),
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
				'name' => esc_html__('Page Settings','themecore'),
				'id' => 'pagemeta_page_title_seperator',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Header Type','themecore'),
				'id' => 'pagemeta_header_type',
				'class' => 'condition-without-compact-style',
				'type' => 'select',
				'std' => 'enable',
				'desc' => esc_html__('Header Type for Horizontal menu','themecore'),
				'options' => array(
					'auto'            => esc_attr__('Default','themecore'),
					'inverse'         => esc_attr__('Inverse Opaque','themecore'),
					'overlay'         => esc_attr__('Overlay','themecore'),
					'inverse-overlay' => esc_attr__('Inverse Overlay','themecore'),
					)
				),
			array(
				'name' => esc_html__('Switch Menu','themecore'),
				'id' => 'pagemeta_menu_choice',
				'type' => 'select',
				'desc' => esc_html__('Select a different menu for this page','themecore'),
				'options' => themecore_generate_menulist()
				),
			array(
				'name' => esc_html__('Footer','themecore'),
				'id' => 'pagemeta_general_footer',
				'type' => 'select',
				'std' => 'enable',
				'desc' => esc_html__('Enable / Disable Footer','themecore'),
				'options' => array(
					'enable' => esc_attr__('Enable','themecore'),
					'disable' => esc_attr__('Disable','themecore')
					)
				),
			array(
				'name' => esc_html__('Page Title','themecore'),
				'id' => 'pagemeta_page_title',
				'type' => 'select',
				'desc' => esc_html__('Page Title','themecore'),
				'std' => 'default',
				'options' => array(
					'default' => esc_html__('Default','themecore'),
					'show' => esc_html__('Show','themecore'),
					'hide' => esc_html__('Hide','themecore')
					)
				),
			array(
				'name' => esc_html__('Page Background','themecore'),
				'id' => 'pagemeta_sep-page_backgrounds',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Page Color','themecore'),
				'id' => 'pagemeta_pagebackground_color',
				'type' => 'color',
				'desc' => esc_html__('Page color','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Background slides status','themecore'),
				'id' => 'pagemeta_background_status',
				'type' => 'select',
				'desc' => esc_html__('Enable or disable background images. Customizer set choice picks the image from Customizer > Page Backgrounds','themecore'),
				'std' => 'default',
				'options' => array(
					'customizer' => esc_attr__('Customizer set choice','themecore'),
					'enable' => esc_attr__('Using page attached images','themecore'),
					'disable' => esc_attr__('Disable','themecore')
					)
				),
			array(
				'name' => esc_html__('Background slides','themecore'),
				'id' => 'pagemeta_image_attachments',
				'class' => 'condition-only-with-core',
				'std' => esc_html__('Upload Images','themecore'),
				'type' => 'image_gallery',
				'desc' => esc_html__('Used to generate background slideshow.','themecore')
				),
		)
);
return $proofing_box;
}
/*
* Meta options for Proofing post type
*/
function themecore_proofingitem_metaoptions(){
	$proofing_box = themecore_proofing_metadata();
	themecore_generate_metaboxes($proofing_box,get_the_id());
}
?>