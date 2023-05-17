<?php
function themecore_portfolio_metadata() {
	$mtheme_sidebar_options = themecore_generate_sidebarlist("portfolio");
	
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

	$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$portfolio_box = array(
		'id' => 'portfoliometa-box',
		'title' => esc_html__('Portfolio Metabox','themecore'),
		'page' => 'page',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Portfolio Settings','themecore'),
				'id' => 'pagemeta_portfolio_section_id',
				'type' => 'break',
				'sectiontitle' => esc_html__('Portfolio Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Portfolio Options','themecore'),
				'id' => 'pagemeta_sep_page_options',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Description for gallery thumbnail ( Portfolio Gallery )','themecore'),
				'id' => 'pagemeta_thumbnail_desc',
				'type' => 'textarea',
				'desc' => esc_html__('This description is displayed below each thumbnail.','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Gallery thumbnail options','themecore'),
				'id' => 'pagemeta_sep_page_options',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Gallery thumbnail link type','themecore'),
				'id' => 'pagemeta_thumbnail_linktype',
				'type' => 'image',
				'std' => 'Lightbox',
				'desc' => esc_html__('Link type of portfolio image in portfolio galleries.','themecore'),
				'options' => array(
					'Lightbox_DirectURL' => $mtheme_imagepath . 'portfolio-link-direct-lightbox.png',
					'Lightbox' => $mtheme_imagepath . 'portfolio-link-lightbox.png',
					'Customlink' => $mtheme_imagepath . 'portfolio-link-customlink.png',
					'DirectURL' => $mtheme_imagepath . 'portfolio-link-direct.png'
					)
				),
			array(
				'name' => esc_html__('Fill for Lightbox Video','themecore'),
				'id' => 'pagemeta_lightbox_video',
				'heading' => 'subhead',
				'class'=> 'portfoliolinktype',
				'type' => 'text',
				'desc' => esc_html__('To display a Lightbox Video. Eg: https://www.youtube.com/watch?v=D78TYCEG4 , https://vimeo.com/172881','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Fill for Custom Link','themecore'),
				'id' => 'pagemeta_customlink',
				'heading' => 'subhead',
				'class'=> 'portfoliolinktype',
				'type' => 'text',
				'desc' => esc_html__('For any link with full url','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Custom Thumbnail. (optional)','themecore'),
				'id' => 'pagemeta_customthumbnail',
				'type' => 'upload',
				'target' => 'image',
				'desc' => esc_html__('Thumbnail URL.','themecore'),
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
				'name' => esc_html__('Page Layout','themecore'),
				'id' => 'pagemeta_sep_page_options',
				'type' => 'seperator',
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
				'name' => esc_html__('Choice of Sidebar','themecore'),
				'id' => 'pagemeta_sidebar_choice',
				'type' => 'select',
				'desc' => esc_html__('For Sidebar Active Pages and Posts','themecore'),
				'options' => $mtheme_sidebar_options
				),
			array(
				'name' => esc_html__('Page Settings','themecore'),
				'id' => 'pagemeta_header_section_id',
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
				'name' => esc_html__('Instagram Footer','themecore'),
				'id' => 'pagemeta_instagram_footer',
				'type' => 'select',
				'std' => 'disable',
				'desc' => esc_html__('Enable / Disable Instagram Footer','themecore'),
				'options' => array(
					'disable' => esc_attr__('Disable','themecore'),
					'enable' => esc_attr__('Enable','themecore')
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
return $portfolio_box;
}
/*
* Meta options for Portfolio post type
*/
function themecore_portfolioitem_metaoptions(){
	$portfolio_box = themecore_portfolio_metadata();
	themecore_generate_metaboxes($portfolio_box,get_the_id());
}
?>