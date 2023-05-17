<?php
function themecore_woocommerce_metadata() {
$mtheme_sidebar_options = themecore_generate_sidebarlist('portfolio');

$mtheme_imagepath =  plugin_dir_url( __FILE__ ) . 'assets/images/';

$mtheme_woocommerce_box = array(
	'id' => 'woocommercemeta-box',
	'title' => esc_html__('Woocommerce Metabox','themecore'),
	'page' => 'page',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
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
return $mtheme_woocommerce_box;
}
/*
* Meta options for Portfolio post type
*/
function themecore_woocommerceitem_metaoptions(){
	$mtheme_woocommerce_box = themecore_woocommerce_metadata();
	themecore_generate_metaboxes($mtheme_woocommerce_box,get_the_id());
}
?>