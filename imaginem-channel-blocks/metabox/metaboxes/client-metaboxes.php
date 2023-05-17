<?php
function themecore_client_metadata() {

	$mtheme_sidebar_options = themecore_generate_sidebarlist('client');

	$options_fonts = themecore_google_fonts();

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

	$mtheme_client_box = array(
		'id' => 'clientmeta-box',
		'title' => esc_html__('Client Metabox','themecore'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			array(
				'name' => esc_html__('Client Settings','themecore'),
				'id' => 'pagemeta_page_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Client Notice','themecore'),
				'id' => 'pagemeta_client_notice',
				'type' => 'notice',
				'std' => '',
				'desc' => esc_html__('Add a Password to this client page to password protect all proofing galleries associated with the client.','themecore')
				),
			array(
				'name' => esc_html__('Client Name','themecore'),
				'id' => 'pagemeta_client_name',
				'type' => 'text',
				'desc' => esc_html__('Client Name.','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Description for client','themecore'),
				'id' => 'pagemeta_client_desc',
				'type' => 'textarea',
				'desc' => esc_html__('This description is displayed for client.','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Header Settings','themecore'),
				'id' => 'pagemeta_sep_page_title',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Background Image for Client Password Page','themecore'),
				'id' => 'pagemeta_client_background_image',
				'type' => 'upload',
				'target' => 'image',
				'std' => '',
				'desc' => esc_html__('Upload or provide full url of background. eg. http://www.domain.com/path/image.jpg','themecore')
				),
			array(
				'name' => esc_html__('Switch Menu','themecore'),
				'id' => 'pagemeta_menu_choice',
				'type' => 'select',
				'desc' => esc_html__('Select a different menu for this page','themecore'),
				'options' => themecore_generate_menulist()
				),
			array(
				'name' => esc_html__('Instagram Footer','themecore'),
				'id' => 'pagemeta_instagram_footer',
				'type' => 'select',
				'std' => 'enable',
				'desc' => esc_html__('Enable / Disable Instagram Footer','themecore'),
				'options' => array(
					'enable' => esc_attr__('Enable','themecore'),
					'disable' => esc_attr__('Disable','themecore')
					)
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
				'name' => esc_html__('Page Background','themecore'),
				'id' => 'pagemeta_background_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Background','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Page Background','themecore'),
				'id' => 'pagemeta_sep-page_backgrounds',
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
	return $mtheme_client_box;
}
/*
* Meta options for client post type
*/
function themecore_clientitem_metaoptions(){
	$mtheme_client_box = themecore_client_metadata();
	themecore_generate_metaboxes( $mtheme_client_box,get_the_id() );
}
?>