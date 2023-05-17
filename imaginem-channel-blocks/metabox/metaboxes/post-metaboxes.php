<?php
function themecore_post_metadata() {
	$mtheme_sidebar_options = themecore_generate_sidebarlist('post');

	$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

	$mtheme_post_metapack=array();

	$mtheme_post_metapack['main'] = array(
		'id' => 'common-pagemeta-box',
		'title' => esc_html__('General Page Metabox','themecore'),
		'page' => 'post',
		'context' => 'normal',
		'priority' => 'core',
		'fields' => array(
			array(
				'name' => esc_html__('Page Options','themecore'),
				'id' => 'pagemeta_page_section_id',
				'class' => 'condition-only-with-core',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Options','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Page Options','themecore'),
				'id' => 'pagemeta_sep_page_options',
				'class' => 'condition-only-with-core',
				'type' => 'seperator',
				),
			array(
				'name' => esc_html__('Attach Images','themecore'),
				'id' => 'pagemeta_image_attachments',
				'class' => 'condition-only-with-core',
				'std' => esc_html__('Upload Images','themecore'),
				'type' => 'image_gallery',
				'desc' => esc_html__('Used with Gallery post type to display slideshow.','themecore')
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
				'name' => esc_html__('Page Settings','themecore'),
				'id' => 'pagemeta_page_title_seperator',
				'type' => 'break',
				'sectiontitle' => esc_html__('Page Settings','themecore'),
				'std' => ''
				),
			array(
				'name' => esc_html__('Header Settings','themecore'),
				'id' => 'pagemeta_sep-header_settings',
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
				'name' => esc_html__('Page Title','themecore'),
				'id' => 'pagemeta_page_title',
				'type' => 'select',
				'desc' => esc_html__('Page Title','themecore'),
				'std' => 'default',
				'options' => array(
					'default' => esc_attr__('Default','themecore'),
					'show' => esc_attr__('Show','themecore'),
					'hide' => esc_attr__('Hide','themecore')
					)
				),
			array(
				'name' => esc_html__('Page Background','themecore'),
				'id' => 'pagemeta_background_section_id',
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

$mtheme_post_metapack['video'] = array(
	'id' => 'video-meta-box',
	'title' => esc_html__('Video Metabox','themecore'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => esc_html__('HTML5 Video','themecore'),
			'id' => 'pagemeta_video_meta_section1_id',
			'type' => 'break',
			'sectiontitle' => esc_html__('HTML5 Video','themecore'),
			'std' => ''
			),
		array(
			'name' => esc_html__('M4V File URL','themecore'),
			'id' => 'pagemeta_video_m4v_file',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Enter M4V File URL ( Required )','themecore')
			),
		array(
			'name' => esc_html__('OGV File URL','themecore'),
			'id' => 'pagemeta_video_ogv_file',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Enter OGV File URL','themecore')
			),
		array(
			'name' => esc_html__('Poster Image','themecore'),
			'id' => 'pagemeta_video_poster_file',
			'type' => 'upload',
			'target' => 'image',
			'std' => '',
			'desc' => esc_html__('Poster Image','themecore')
			),
		array(
			'name' => esc_html__('Video Hosts','themecore'),
			'id' => 'pagemeta_video_meta_section2_id',
			'type' => 'break',
			'std' => '',
			'sectiontitle' => esc_html__('Video Hosts','themecore')
			),
		array(
			'name' => esc_html__('Youtube Video ID','themecore'),
			'id' => 'pagemeta_video_youtube_id',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Youtube video ID','themecore')
			),
		array(
			'name' => esc_html__('Vimeo Video ID','themecore'),
			'id' => 'pagemeta_video_vimeo_id',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Vimeo video ID','themecore')
			),
		array(
			'name' => esc_html__('Daily Motion Video ID','themecore'),
			'id' => 'pagemeta_video_dailymotion_id',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Daily Motion video ID','themecore')
			),
		array(
			'name' => esc_html__('Google Video ID','themecore'),
			'id' => 'pagemeta_video_google_id',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Google video ID','themecore')
			),
		array(
			'name' => esc_html__('Video Embed Code','themecore'),
			'id' => 'pagemeta_video_embed_code',
			'type' => 'textarea',
			'std' => '',
			'desc' => esc_html__('Video Embed code. You can grab embed codes from hosted video sites.','themecore')
			)
		)
	);

$mtheme_post_metapack['audio'] = array(
	'id' => 'audio-meta-box',
	'title' => esc_html__('Audio Metabox','themecore'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => esc_html__('Audio Embed code','themecore'),
			'id' => 'pagemeta_audio_meta_section1_id',
			'type' => 'break',
			'sectiontitle' => esc_html__('Audio Embed code','themecore'),
			'std' => ''
			),
		array(
			'name' => esc_html__('Audio Embed code','themecore'),
			'id' => 'pagemeta_audio_embed',
			'type' => 'textarea',
			'std' => '',
			'desc' => esc_html__('eg. Soundcloud embed code','themecore')
			)
		)
	);

$mtheme_post_metapack['link'] = array(
	'id' => 'link-meta-box',
	'title' => esc_html__('Link Metabox','themecore'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => esc_html__('Link URL','themecore'),
			'id' => 'pagemeta_meta_link',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Please provide full url. eg. http://www.domain.com/path/','themecore')
			)
		)
	);

$mtheme_post_metapack['image'] = array(
	'id' => 'image-meta-box',
	'title' => esc_html__('Image Metabox','themecore'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => esc_html__('Enable Lightbox','themecore'),
			'id' => 'pagemeta_meta_lightbox',
			'type' => 'select',
			'options' => array(
				'enabled_lightbox' => esc_html__('Enable Lightbox','themecore'),
				'disable_lightbox' => esc_html__('Disable Lighbox','themecore')
				)
			)
		)
	);

$mtheme_post_metapack['quote'] = array(
	'id' => 'quote-meta-box',
	'title' => esc_html__('Quote Metabox','themecore'),
	'page' => 'post',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		array(
			'name' => esc_html__('Quote','themecore'),
			'id' => 'pagemeta_meta_quote',
			'type' => 'textarea',
			'std' => '',
			'desc' => esc_html__('Enter quote here','themecore')
			),
		array(
			'name' => esc_html__('Author','themecore'),
			'id' => 'pagemeta_meta_quote_author',
			'type' => 'text',
			'std' => '',
			'desc' => esc_html__('Author','themecore')
			)
		)
	);
return $mtheme_post_metapack;
}

// Callback function to show fields in meta box
function themecore_video_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_video_meta_box = $mtheme_post_metapack['video'];
	themecore_generate_metaboxes($mtheme_video_meta_box, get_the_id() );
}

function themecore_audio_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_audio_meta_box = $mtheme_post_metapack['audio'];
	themecore_generate_metaboxes($mtheme_audio_meta_box, get_the_id() );
}

function themecore_common_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_common_meta_box = $mtheme_post_metapack['main'];
	themecore_generate_metaboxes($mtheme_common_meta_box,get_the_id());
}

function themecore_link_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_link_meta_box = $mtheme_post_metapack['link'];
	themecore_generate_metaboxes($mtheme_link_meta_box, get_the_id() );
}

function themecore_image_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_image_meta_box = $mtheme_post_metapack['image'];
	themecore_generate_metaboxes($mtheme_image_meta_box, get_the_id() );
}

function themecore_quote_show_box() {
	$mtheme_post_metapack = themecore_post_metadata();
	$mtheme_quote_meta_box = $mtheme_post_metapack['quote'];
	themecore_generate_metaboxes($mtheme_quote_meta_box, get_the_id() );
}
?>