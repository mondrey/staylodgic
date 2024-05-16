<?php
if ( class_exists( 'Kirki' ) ) {
	$fullscreenposts = Kirki_Helper::get_posts(
		array(
			'post_type'      => 'fullscreen',
			'posts_per_page' => -1,
		)
	);

	$default_fullscreen = false;

	if ( ! empty( $fullscreenposts ) ) {
		foreach ( $fullscreenposts as $key => $value ) {
			// Get first ID as default
			$default_fullscreen = $key;
			break;
		}
	}

	function atollmatrix_kirki_add_field( $args ) {
		Kirki::add_field( 'atollmatrix', $args );
	}
	// Add our config to differentiate from other themes/plugins
	// that may use Kirki at the same time.
	Kirki::add_config(
		'atollmatrix',
		array(
			'capability'     => 'edit_theme_options',
			'option_type'    => 'theme_mod',
			'disable_loader' => true,
		)
	);

	function atollmatrix_goto_top_status( $control ) {
		if ( true === $control->manager->get_setting( 'enable_goto_top' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

	// Condition Based Start
	function atollmatrix_choice_menu_is_vertical_callback( $control ) {
		if ( 'vertical-menu' === $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

	function atollmatrix_choice_instagram_not_in_verticalmenu_callback( $control ) {
		if ( 'instagram-verticalmenu' === $control->manager->get_setting( 'instagram_location' )->value() ) {
			return false;
		} else {
			return true;
		}
	}

	function atollmatrix_choice_menu_is_not_vertical_callback( $control ) {
		if ( 'vertical-menu' !== $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

	function atollmatrix_choice_menu_split_callback( $control ) {
		if ( 'split-menu' === $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}

	function atollmatrix_choice_menu_is_not_centered_callback( $control ) {
		if ( 'split-menu' !== $control->manager->get_setting( 'menu_type' )->value() && 'vertical-menu' !== $control->manager->get_setting( 'menu_type' )->value() && 'centered-logo' !== $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}
	function atollmatrix_choice_menu_default_left_callback( $control ) {
		if ( 'split-menu' !== $control->manager->get_setting( 'menu_type' )->value() && 'vertical-menu' !== $control->manager->get_setting( 'menu_type' )->value() && 'centered-logo' !== $control->manager->get_setting( 'menu_type' )->value() && 'compact-minimal-top' !== $control->manager->get_setting( 'menu_type' )->value() && 'compact-minimal-left' !== $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}
	function atollmatrix_choice_menu_centered_callback( $control ) {
		if ( 'centered-logo' === $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}
	function atollmatrix_choice_menu_compact_top_callback( $control ) {
		if ( 'compact-minimal-top' === $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}
	function atollmatrix_choice_menu_compact_left_callback( $control ) {
		if ( 'compact-minimal-left' === $control->manager->get_setting( 'menu_type' )->value() ) {
			return true;
		} else {
			return false;
		}
	}
	function atollmatrix_choice_themestyle_not_display_callback( $control ) {
		if ( 'display' === $control->manager->get_setting( 'general_theme_style' )->value() ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * Add Sections.
	 *
	 * We'll be doing things a bit differently here, just to demonstrate an example.
	 * We're going to define 1 section per control-type just to keep things clean and separate.
	 *
	 */
	$panels   = array(
		'atollmatrix_logo_panel'           => array( esc_attr__( 'Logos', 'atollmatrix' ) ),
		'atollmatrix_general_panel'        => array( esc_attr__( 'General', 'atollmatrix' ) ),
		'atollmatrix_mainmenu_panel'       => array( esc_attr__( 'Main Menu', 'atollmatrix' ) ),
		'atollmatrix_responsivemenu_panel' => array( esc_attr__( 'Responsive Menu', 'atollmatrix' ) ),
		'atollmatrix_sidebar_panel'        => array( esc_attr__( 'Sidebar', 'atollmatrix' ) ),
		'atollmatrix_shop_panel'           => array( esc_attr__( 'Shop', 'atollmatrix' ) ),
	);
	$sections = array(
		'atollmatrix_menutype_section'             => array( esc_attr__( 'Header Type', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_logo_section'                 => array( esc_attr__( 'Main Logo', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_textlogo_section'             => array( esc_attr__( 'Text Logo', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_responsivelogo_section'       => array( esc_attr__( 'Responsive Logo', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_footerlogo_section'           => array( esc_attr__( 'Footer Logo', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_themestyle_section'           => array( esc_attr__( 'Theme Style', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_map_api_section'              => array( esc_attr__( 'GoogleMap API', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_api_section'                  => array( esc_attr__( 'Instagram API', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_pagegeneral_section'          => array( esc_attr__( 'Page General', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_pagecolors_section'           => array( esc_attr__( 'Page Colors', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_commentcolors_section'        => array( esc_attr__( 'Post Info and Comment Colors', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_postnavcolors_section'        => array( esc_attr__( 'Post Navigation Colors', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_pagenavcolors_section'        => array( esc_attr__( 'Pagination Colors', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_pagefont_section'             => array( esc_attr__( 'Page Fonts', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_sidebarfont_section'          => array( esc_attr__( 'Sidebar Fonts', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_pagetitle_section'            => array( esc_attr__( 'Page Title', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_footerfont_section'           => array( esc_attr__( 'Footer Font', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_content_section'              => array( esc_attr__( 'Content Headings', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_search_section'               => array( esc_attr__( 'Search', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_archivetitles_section'        => array( esc_attr__( 'Archive Titles', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_commentlabels_section'        => array( esc_attr__( 'Comment Labels', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_menutext_section'             => array( esc_attr__( 'Menu Typography', 'atollmatrix' ), '', 'atollmatrix_mainmenu_panel' ),
		'atollmatrix_menucolors_section'           => array( esc_attr__( 'Menu Colors', 'atollmatrix' ), '', 'atollmatrix_mainmenu_panel' ),
		'atollmatrix_responsivemenutext_section'   => array( esc_attr__( 'Responsive Menu Typography', 'atollmatrix' ), '', 'atollmatrix_responsivemenu_panel' ),
		'atollmatrix_responsivemenucolors_section' => array( esc_attr__( 'Responsive Menu Colors', 'atollmatrix' ), '', 'atollmatrix_responsivemenu_panel' ),
		'atollmatrix_preloader_section'            => array( esc_attr__( 'Preloader', 'atollmatrix' ), '', 'atollmatrix_logo_panel' ),
		'atollmatrix_home_section'                 => array( esc_attr__( 'Fullscreen Home', 'atollmatrix' ), '', '' ),
		'atollmatrix_rightclickblock_section'      => array( esc_attr__( 'Right Click Block', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_elementor_section'            => array( esc_attr__( 'Elementor', 'atollmatrix' ), '', 'atollmatrix_general_panel' ),
		'atollmatrix_fullscreenmedia_section'      => array( esc_attr__( 'Fullscreen Media', 'atollmatrix' ), '', '' ),
		'atollmatrix_fotoramaslides_section'       => array( esc_attr__( 'Fotorama Slides', 'atollmatrix' ), '', '' ),
		'atollmatrix_404_section'                  => array( esc_attr__( '404', 'atollmatrix' ), '', '' ),
		'atollmatrix_events_section'               => array( esc_attr__( 'Events', 'atollmatrix' ), '', '' ),
		'atollmatrix_portfolio_section'            => array( esc_attr__( 'Portfolio', 'atollmatrix' ), '', '' ),
		'atollmatrix_blog_section'                 => array( esc_attr__( 'Blog', 'atollmatrix' ), '', '' ),
		'atollmatrix_proofing_section'             => array( esc_attr__( 'Proofing', 'atollmatrix' ), '', '' ),
		'atollmatrix_shop_options_section'         => array( esc_attr__( 'Shop Options', 'atollmatrix' ), '', 'atollmatrix_shop_panel' ),
		'atollmatrix_cart_dashbar_section'         => array( esc_attr__( 'Dash Cart Colors', 'atollmatrix' ), '', 'atollmatrix_shop_panel' ),
		'atollmatrix_toggle_cart_section'          => array( esc_attr__( 'Toggle Cart', 'atollmatrix' ), '', 'atollmatrix_shop_panel' ),
		'atollmatrix_lightbox_section'             => array( esc_attr__( 'Lightbox', 'atollmatrix' ), '', '' ),
		'atollmatrix_addsidebar_section'           => array( esc_attr__( 'Add a Sidebar', 'atollmatrix' ), '', 'atollmatrix_sidebar_panel' ),
		'atollmatrix_sidebarcolors_section'        => array( esc_attr__( 'Sidebar Colors', 'atollmatrix' ), '', 'atollmatrix_sidebar_panel' ),
		'atollmatrix_footer_section'               => array( esc_attr__( 'Footer', 'atollmatrix' ), '', '' ),
	);

	foreach ( $panels as $panel_id => $panel ) {
		$panel_args = array(
			'title'    => $panel[0],
			'priority' => 30,
		);
		Kirki::add_panel( $panel_id, $panel_args );
	}
	foreach ( $sections as $section_id => $section ) {
		$section_args = array(
			'title'    => $section[0],
			'panel'    => $section[2],
			'priority' => 30,
		);
		Kirki::add_section( $section_id, $section_args );
	}

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'image',
			'settings'        => 'verticalmenu_logo',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Menu Logo', 'atollmatrix' ),
			'description'     => esc_attr__( 'Vertical Menu Logo', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	// Logo Height
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'settings'        => 'vertical_logo_height',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Logo Width', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '203',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-logo-wrap img',
					'property' => 'width',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'vertical_logo_topspace',
			'label'           => esc_html__( 'Vertical Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '90',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-logo-wrap',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'vertical_logo_bottomspace',
			'label'           => esc_html__( 'Vertical Logo Bottom Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '50',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-logo-wrap',
					'property' => 'padding-bottom',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Left Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'vertical_logo_leftspace',
			'label'           => esc_html__( 'Vertical Logo Left Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '36',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-logo-wrap',
					'property' => 'padding-left',
					'units'    => 'px',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'toggle',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'vertical_menu_keep_open',
			'label'           => esc_html__( 'Show page with current menu open', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => false,
			'priority'        => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'image',
			'settings'        => 'main_logo',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Primary Logo ( Dark )', 'atollmatrix' ),
			'description'     => esc_attr__( 'Primary Logo', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'image',
			'settings'        => 'secondary_logo',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Secondary Logo ( Bright )', 'atollmatrix' ),
			'description'     => esc_attr__( 'Secondary Logo', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'image',
			'settings'    => 'responsive_logo',
			'label'       => esc_attr__( 'Responsive Logo', 'atollmatrix' ),
			'description' => esc_attr__( 'Responsive Logo', 'atollmatrix' ),
			'section'     => 'atollmatrix_responsivelogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'image',
			'settings'    => 'footer_logo',
			'label'       => esc_attr__( 'Footer Logo', 'atollmatrix' ),
			'description' => esc_attr__( 'Footer Logo', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'footer_logo_url',
			'label'       => esc_attr__( 'Footer Logo url', 'atollmatrix' ),
			'description' => esc_attr__( 'Footer logo url', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	// Page title
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'hide_pagetitle',
			'label'    => esc_html__( 'Remove Page title', 'atollmatrix' ),
			'section'  => 'atollmatrix_pagetitle_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'pagetitle_font',
			'label'    => esc_html__( 'Page Title Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_pagetitle_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'pagtitle_size',
			'label'       => esc_attr__( 'Page Title Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagetitle_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'pagtitle_letterspacing',
			'label'       => esc_attr__( 'Page Title Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagetitle_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'pagtitle_weight',
			'label'       => esc_attr__( 'Page Title Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagetitle_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagtitle_color',
			'label'     => esc_html__( 'Page Title Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagetitle_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.title-container-outer-wrap .entry-title',
					'property' => 'color',
				),
			),
		)
	);
	// Footer Font
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'footertext_font',
			'label'    => esc_html__( 'Footer Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_footerfont_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'footertext_size',
			'label'       => esc_attr__( 'Footer Text Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'footertext_letterspacing',
			'label'       => esc_attr__( 'Footer Text Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'footertext_weight',
			'label'       => esc_attr__( 'Footer Text Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'footerwidgettitle_font',
			'label'    => esc_html__( 'Footer widegt title Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_footerfont_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'footerwidgettitle_size',
			'label'       => esc_attr__( 'Footer widegt title Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'footerwidgettitle_letterspacing',
			'label'       => esc_attr__( 'Footer widegt title Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'footerwidgettitle_weight',
			'label'       => esc_attr__( 'Footer widegt title Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_footerfont_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	// Page Comments
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'disable_pagecomments',
			'label'    => esc_html__( 'Disable page comments', 'atollmatrix' ),
			'section'  => 'atollmatrix_pagegeneral_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_one_size',
			'label'       => esc_attr__( 'Content H1 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_one_letterspacing',
			'label'       => esc_attr__( 'Content H1 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_one_weight',
			'label'       => esc_attr__( 'Content H1 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_two_size',
			'label'       => esc_attr__( 'Content H2 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_two_letterspacing',
			'label'       => esc_attr__( 'Content H2 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_two_weight',
			'label'       => esc_attr__( 'Content H2 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_three_size',
			'label'       => esc_attr__( 'Content H3 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_three_letterspacing',
			'label'       => esc_attr__( 'Content H3 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_three_weight',
			'label'       => esc_attr__( 'Content H3 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_four_size',
			'label'       => esc_attr__( 'Content H4 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_four_letterspacing',
			'label'       => esc_attr__( 'Content H4 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_four_weight',
			'label'       => esc_attr__( 'Content H4 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_five_size',
			'label'       => esc_attr__( 'Content H5 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_five_letterspacing',
			'label'       => esc_attr__( 'Content H5 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_five_weight',
			'label'       => esc_attr__( 'Content H5 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_six_size',
			'label'       => esc_attr__( 'Content H6 Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'heading_six_letterspacing',
			'label'       => esc_attr__( 'Content H6 Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'heading_six_weight',
			'label'       => esc_attr__( 'Content H6 Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_content_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'search_buttontext',
			'label'    => esc_html__( 'Search button tooltip text', 'atollmatrix' ),
			'section'  => 'atollmatrix_search_section',
			'default'  => esc_html__( 'Search', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'search_mobileform',
			'label'    => esc_html__( 'Mobile menu search', 'atollmatrix' ),
			'section'  => 'atollmatrix_search_section',
			'default'  => true,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'search_placeholder',
			'label'    => esc_html__( 'Search input placeholder text', 'atollmatrix' ),
			'section'  => 'atollmatrix_search_section',
			'default'  => '',
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_search_notfoundtitleprefix',
			'label'    => esc_html__( 'Search results title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Search Results for:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_tag_titleprefix',
			'label'    => esc_html__( 'Tag archive title', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Tag:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_category_titleprefix',
			'label'    => esc_html__( 'Category title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Category:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_author_titleprefix',
			'label'    => esc_html__( 'Author title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Author:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_year_titleprefix',
			'label'    => esc_html__( 'Yearly title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Yearly:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_monthly_titleprefix',
			'label'    => esc_html__( 'Monthly title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Monthly:', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'archive_daily_titleprefix',
			'label'    => esc_html__( 'Daily title prefix', 'atollmatrix' ),
			'section'  => 'atollmatrix_archivetitles_section',
			'default'  => esc_html__( 'Daily:', 'atollmatrix' ),
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'commentlabel_override',
			'label'    => esc_html__( 'Over-ride comment fields', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentinfo_nocomment',
			'label'    => esc_html__( 'No Comments', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'No Comments', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentinfo_onecomment',
			'label'    => esc_html__( 'One Comment', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'One Comment', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'commentinfo_morecomments',
			'label'       => esc_html__( 'Comments', 'atollmatrix' ),
			'description' => esc_html__( 'Comment number will display before text', 'atollmatrix' ),
			'section'     => 'atollmatrix_commentlabels_section',
			'default'     => esc_html__( 'Comments', 'atollmatrix' ),
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentinfo_commentclosed',
			'label'    => esc_html__( 'Comments are closed', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Comments are closed', 'atollmatrix' ),
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_leavecomment',
			'label'    => esc_html__( 'Leave a Comment', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Leave a Comment', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_commentfield',
			'label'    => esc_html__( 'Comment field', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Comment', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_namefield',
			'label'    => esc_html__( 'Name field', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Name', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_emailfield',
			'label'    => esc_html__( 'Email field', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Email', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_websitefield',
			'label'    => esc_html__( 'Website field', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Website', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'commentlabel_button',
			'label'    => esc_html__( 'Comment Submit Button', 'atollmatrix' ),
			'section'  => 'atollmatrix_commentlabels_section',
			'default'  => esc_html__( 'Post Comment', 'atollmatrix' ),
			'priority' => 10,
		)
	);

	// Page Comments
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'enable_animated_cursor',
			'label'    => esc_html__( 'Enable Animated Cursor', 'atollmatrix' ),
			'section'  => 'atollmatrix_themestyle_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'enable_goto_top',
			'label'    => esc_html__( 'Goto Top Indicator', 'atollmatrix' ),
			'section'  => 'atollmatrix_themestyle_section',
			'default'  => true,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'active_callback' => array(
				array(
					'setting'  => 'enable_goto_top',
					'operator' => '===',
					'value'    => true,
				),
			),
			'settings'        => 'goto_top_location',
			'label'           => esc_html__( 'Goto Top Location', 'atollmatrix' ),
			'section'         => 'atollmatrix_themestyle_section',
			'default'         => 'default',
			'choices'         => array(
				'default' => esc_html__( 'Right', 'atollmatrix' ),
				'left'    => esc_html__( 'Left', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'general_theme_style',
			'label'    => esc_html__( 'Theme Style', 'atollmatrix' ),
			'section'  => 'atollmatrix_themestyle_section',
			'default'  => 'default',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default', 'atollmatrix' ),
				'compact' => esc_html__( 'Compact', 'atollmatrix' ),
				'display' => esc_html__( 'Display', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'active_callback' => array(
				array(
					'setting'  => 'general_theme_style',
					'operator' => '!==',
					'value'    => 'display',
				),
			),
			'settings'        => 'default_font_load',
			'label'           => esc_html__( 'Load default font', 'atollmatrix' ),
			'section'         => 'atollmatrix_themestyle_section',
			'default'         => 'active',
			'priority'        => 10,
			'multiple'        => 1,
			'choices'         => array(
				'active'  => esc_html__( 'Active', 'atollmatrix' ),
				'disable' => esc_html__( 'Disable', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'general_theme_mode',
			'label'    => esc_html__( 'Theme Mode', 'atollmatrix' ),
			'section'  => 'atollmatrix_themestyle_section',
			'default'  => 'default',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default ( Bright )', 'atollmatrix' ),
				'dark'    => esc_html__( 'Dark', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_border',
			'label'     => esc_html__( 'Pagination circle border', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination a, .woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_numbers',
			'label'     => esc_html__( 'Pagination numbers', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination a, .woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_hoverborder',
			'label'     => esc_html__( 'Pagination hover circle border', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination-navigation a:hover, .pagination a:hover, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_hovernumber',
			'label'     => esc_html__( 'Pagination hover number', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination-navigation a:hover, .pagination a:hover, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_currentbackground',
			'label'     => esc_html__( 'Pagination Current Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination span.current, .pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current',
					'property' => 'background-color',
				),
				array(
					'element'  => '.pagination span.current, .pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'pagenav_hovercolor',
			'label'     => esc_html__( 'Pagination Current Number', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagenavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.pagination span.current, .pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li span.current',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'postnav_background',
			'label'     => esc_html__( 'Post navigation background', 'atollmatrix' ),
			'section'   => 'atollmatrix_postnavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.portfolio-nav',
					'property' => 'background-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'postnav_hoverbackground',
			'label'     => esc_html__( 'Post navigation hover background', 'atollmatrix' ),
			'section'   => 'atollmatrix_postnavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.portfolio-nav:hover',
					'property' => 'background-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'postnav_icons',
			'label'     => esc_html__( 'Post navigation icons color', 'atollmatrix' ),
			'section'   => 'atollmatrix_postnavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.portfolio-nav-item i',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'postnav_hovericons',
			'label'     => esc_html__( 'Post navigation icons hover color', 'atollmatrix' ),
			'section'   => 'atollmatrix_postnavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.portfolio-nav-item:hover a i',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'postnav_hovericonsbackground',
			'label'     => esc_html__( 'Post navigation icons hover background', 'atollmatrix' ),
			'section'   => 'atollmatrix_postnavcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.portfolio-nav-item a:hover',
					'property' => 'background-color',
				),
			),
		)
	);


	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_heading',
			'label'     => esc_html__( 'Comment Heading', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'h3#reply-title,.entry-content .comment-reply-title, .comment-reply-title',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formborder',
			'label'     => esc_html__( 'Comment Form Field border', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#respond #commentform textarea, #respond #commentform input',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formborderactive',
			'label'     => esc_html__( 'Comment Form Field Active border', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#respond #commentform textarea:focus, #respond #commentform input:focus',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formtext',
			'label'     => esc_html__( 'Comment Form Field Text', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#respond #commentform textarea, #respond #commentform input',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formlabel',
			'label'     => esc_html__( 'Comment Form Field Label', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#commentform label,#commentform .logged-in-as,#commentform .logged-in-as a',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formbutton',
			'label'     => esc_html__( 'Comment Form Button', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#commentform input#submit',
					'property' => 'color',
				),
				array(
					'element'  => '#commentform input#submit',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formbuttonhover',
			'label'     => esc_html__( 'Comment Form Button Hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#commentform input#submit:hover',
					'property' => 'color',
				),
				array(
					'element'  => '#commentform input#submit:hover',
					'property' => 'border-color',
				),
				array(
					'element'  => '#commentform input#submit:hover',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formbuttontexthover',
			'label'     => esc_html__( 'Comment Form Button Text Hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#commentform input#submit:hover',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'comment_formbox',
			'label'     => esc_html__( 'Comment Form Box', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.comments-wrap-outer .comment-respond',
					'property' => 'background',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_info_summary',
			'label'     => esc_html__( 'Post info summary', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.single-post .postsummarywrap',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_info_summarytext',
			'label'     => esc_html__( 'Post info summary text', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.postsummarywrap .post-single-meta, .postsummarywrap a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_info_summaryicons',
			'label'     => esc_html__( 'Post info summary icons', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.postsummarywrap i',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_author_bio',
			'label'     => esc_html__( 'Author bio background', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.author-info',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_author_biotext',
			'label'     => esc_html__( 'Author Bio text', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content h2.author-heading,.entry-content h3.author-title,.entry-content .author-description',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_authorbutton',
			'label'     => esc_html__( 'Author bio button', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content .author-bio .mtheme-button',
					'property' => 'color',
				),
				array(
					'element'  => '.entry-content .author-bio .mtheme-button',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_authorbuttonhover',
			'label'     => esc_html__( 'Author bio button hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content .author-bio .mtheme-button:hover',
					'property' => 'color',
				),
				array(
					'element'  => '.entry-content .author-bio .mtheme-button:hover',
					'property' => 'border-color',
				),
				array(
					'element'  => '.entry-content .author-bio .mtheme-button:hover',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'post_authorbuttonhovertext',
			'label'     => esc_html__( 'Author bio button hover text', 'atollmatrix' ),
			'section'   => 'atollmatrix_commentcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content .author-bio .mtheme-button:hover',
					'property' => 'color',
				),
			),
		)
	);

	// Accent Color
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'accent_color',
			'label'     => esc_html__( 'Accent Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => atollmatrix_get_css_classes( 'accent_color_classes' ),
					'property' => 'color',
				),
				array(
					'element'  => '.work-details .arrow-link svg g, .entry-blog-contents-wrap .arrow-link svg g',
					'property' => 'stroke',
				),
			),
		)
	);

	// Page Colors
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'page_titles_color',
			'label'     => esc_html__( 'Page Contents', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => atollmatrix_get_css_classes( 'page_content' ),
					'property' => 'color',
				),
				array(
					'element'  => '.theme-hover-arrow::before',
					'property' => 'background-color',
				),
			),
		)
	);
	// Page Colors
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'page_background_color',
			'label'     => esc_html__( 'Page Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.menu-is-horizontal .outer-wrap,.container-outer,.comment-respond,.commentform-wrap .comment.odd,ol.commentlist li.comment',
					'property' => 'background-color',
				),
			),
		)
	);

	// Page Colors
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'page_contents_color',
			'label'     => esc_html__( 'Page Titles', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => atollmatrix_get_css_classes( 'page_titles' ),
					'property' => 'color',
				),
			),
		)
	);

	// Paragraph Link Color
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'ptag_link_color',
			'label'     => esc_html__( 'Content link color', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content p > a,p a,a',
					'property' => 'color',
				),
			),
		)
	);

	// Paragraph Link Hover Color
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'ptag_link_hover_color',
			'label'     => esc_html__( 'Content link hover color', 'atollmatrix' ),
			'section'   => 'atollmatrix_pagecolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.entry-content p > a:hover,p a:hover,a:hover',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'page_general_font',
			'label'       => esc_html__( 'General page font', 'atollmatrix' ),
			'description' => esc_attr__( 'Default: sans-serif', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagefont_section',
			'default'     => 'sans-serif',
			'priority'    => 10,
			'choices'     => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'page_headings_font',
			'label'       => esc_html__( 'Page Headings Font', 'atollmatrix' ),
			'description' => esc_attr__( 'Default: sans-serif', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagefont_section',
			'default'     => 'sans-serif',
			'priority'    => 10,
			'choices'     => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'page_contents_font',
			'label'       => esc_html__( 'Contents Font', 'atollmatrix' ),
			'description' => esc_attr__( 'Default: sans-serif', 'atollmatrix' ),
			'section'     => 'atollmatrix_pagefont_section',
			'default'     => 'sans-serif',
			'priority'    => 10,
			'choices'     => Kirki_Fonts::get_font_choices(),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'sidebar_headings_font',
			'label'       => esc_html__( 'Sidebar Headings Font', 'atollmatrix' ),
			'description' => esc_attr__( 'Default: sans-serif', 'atollmatrix' ),
			'section'     => 'atollmatrix_sidebarfont_section',
			'default'     => 'sans-serif',
			'priority'    => 10,
			'choices'     => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'sidebar_text_font',
			'label'       => esc_html__( 'Sidebar Text Font', 'atollmatrix' ),
			'description' => esc_attr__( 'Default: sans-serif', 'atollmatrix' ),
			'section'     => 'atollmatrix_sidebarfont_section',
			'default'     => 'sans-serif',
			'priority'    => 10,
			'choices'     => Kirki_Fonts::get_font_choices(),
		)
	);

	// Resopnsive Menu Text
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'responsivemenutext_font',
			'label'    => esc_html__( 'Menu Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_responsivemenutext_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'responsivemenutext_size',
			'label'       => esc_attr__( 'Menu Text Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_responsivemenutext_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'responsivemenutext_letterspacing',
			'label'       => esc_attr__( 'Menu Text Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_responsivemenutext_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'responsivemenutextsub_size',
			'label'       => esc_attr__( 'Sub Menu Text Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_responsivemenutext_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'responsivemenutext_weight',
			'label'       => esc_attr__( 'Menu Text Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_responsivemenutext_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'color',
			'choices'  => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings' => 'responsivemenu_background_one',
			'label'    => esc_html__( 'Menu Gradient Color 1', 'atollmatrix' ),
			'section'  => 'atollmatrix_responsivemenucolors_section',
			'default'  => '',
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'color',
			'choices'  => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings' => 'responsivemenu_background_two',
			'label'    => esc_html__( 'Menu Gradient Color 2', 'atollmatrix' ),
			'section'  => 'atollmatrix_responsivemenucolors_section',
			'default'  => '',
			'priority' => 10,
		)
	);

	// Responsive Colors
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_bar_color',
			'label'     => esc_html__( 'Menu Bar Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mobile-menu-toggle::after',
					'property' => 'background-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_toggle_color',
			'label'     => esc_html__( 'Menu Toggle Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mobile-toggle-menu-trigger span::before, .mobile-toggle-menu-trigger span::after, .mobile-toggle-menu-trigger span, .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::before, .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::after,.menu-is-onscreen .mobile-toggle-menu-trigger span::before, .menu-is-onscreen .mobile-toggle-menu-trigger span::after, .menu-is-onscreen .mobile-toggle-menu-trigger span, .menu-is-onscreen .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::before, .menu-is-onscreen .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::after',
					'property' => 'background-color',
				),
				array(
					'element'  => '.responsive-menu-wrap .wpml-lang-selector-wrap a, .responsive-menu-wrap .wpml-lang-selector-wrap',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_color',
			'label'     => esc_html__( 'Menu Item Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.theme-is-light .responsive-mobile-menu ul.mtree a, .menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text a:hover, .menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text a, .responsive-mobile-menu .social-header-wrap ul li.contact-text a, .responsive-mobile-menu .address-text, .responsive-mobile-menu .contact-text, .header-is-simple.theme-is-light .responsive-mobile-menu ul.mtree a,.vertical-menu #mobile-searchform i, .simple-menu #mobile-searchform i, .responsive-mobile-menu #mobile-searchform i,.menu-is-horizontal .responsive-mobile-menu .social-icon i, .menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.social-icon i',
					'property' => 'color',
				),
				array(
					'element'  => '.vertical-menu #mobile-searchform input, .simple-menu #mobile-searchform input, .responsive-mobile-menu #mobile-searchform input',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_socialcolor',
			'label'     => esc_html__( 'Social Icons Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text a:hover, .menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text a, .responsive-mobile-menu .social-header-wrap ul li.contact-text a, .responsive-mobile-menu .address-text,.menu-is-horizontal .responsive-mobile-menu .social-icon i, .menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.social-icon i, .menu-is-vertical .responsive-mobile-menu .social-header-wrap ul li.contact-text a:hover, .menu-is-vertical .responsive-mobile-menu .social-header-wrap ul li.contact-text a,.menu-is-vertical .responsive-mobile-menu .social-icon i, .menu-is-vertical .responsive-mobile-menu .social-header-wrap ul li.social-icon i',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_hover_color',
			'label'     => esc_html__( 'Menu Item Hover Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.theme-is-light .responsive-mobile-menu ul.mtree li li a:hover, .header-is-simple.theme-is-light .responsive-mobile-menu ul.mtree li li a:hover, .theme-is-light .responsive-mobile-menu ul.mtree li > a:hover, .theme-is-light .responsive-mobile-menu ul.mtree a:hover,.menu-is-horizontal .responsive-mobile-menu ul li.social-icon:hover i,.responsive-mobile-menu #mobile-searchform:hover i',
					'property' => 'color',
				),
				array(
					'element'  => '.vertical-menu #mobile-searchform input:focus, .simple-menu #mobile-searchform input:focus, .responsive-mobile-menu #mobile-searchform input:focus',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_hover_socialcolor',
			'label'     => esc_html__( 'Social Icon Hover Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .responsive-mobile-menu .social-header-wrap ul li.contact-text a:hover,body.menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text a:hover,body.menu-is-horizontal .responsive-mobile-menu .social-header-wrap ul li.contact-text:hover, body .responsive-mobile-menu .social-header-wrap ul li.contact-text:hover a,.menu-is-vertical .responsive-mobile-menu ul li.social-icon:hover i,.menu-is-vertical .responsive-mobile-menu ul li.contact-text a:hover,.menu-is-vertical .responsive-mobile-menu ul li.contact-text a:hover,.menu-is-horizontal .responsive-mobile-menu ul li.social-icon:hover i,.menu-is-horizontal .responsive-mobile-menu ul li.contact-text a:hover,.menu-is-horizontal .responsive-mobile-menu ul li.contact-text a:hover',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_hyphen_color',
			'label'     => esc_html__( 'Menu Hyphen', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.theme-is-light ul.mtree > li::before',
					'property' => 'background-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_arrow_color',
			'label'     => esc_html__( 'Menu Arrow', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.theme-is-light .responsive-mobile-menu ul.mtree ul.sub-menu li.mtree-node > a::after, .theme-is-light .responsive-mobile-menu ul.mtree li.mtree-node > a::after',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_open_color',
			'label'     => esc_html__( 'Opened Menu Items', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.header-is-simple.theme-is-light .responsive-mobile-menu ul.mtree li.mtree-open > a, .theme-is-light .responsive-mobile-menu ul.mtree li.mtree-open > a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_opensub_color',
			'label'     => esc_html__( 'Opened Submenu Menu Items', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.theme-is-light .responsive-mobile-menu ul.mtree li li a, .header-is-simple.theme-is-light .responsive-mobile-menu ul.mtree li li a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'responsivemenu_searchinput_color',
			'label'     => esc_html__( 'Responsive Search Input', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivemenucolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.responsive-mobile-menu #mobile-searchform input',
					'property' => 'color',
				),
			),
		)
	);

	// Stickymenu
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'toggle',
			'settings'        => 'enable_stickymenu',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Enable Sticky Menu', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutype_section',
			'default'         => false,
			'priority'        => 10,
		)
	);

	// Stickymenu
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'wpml_lang_selector_enable',
			'label'    => esc_html__( 'Enable WPML language selector', 'atollmatrix' ),
			'section'  => 'atollmatrix_menutype_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	// Menu Type
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'wpml_style',
			'label'    => esc_html__( 'WPML language style', 'atollmatrix' ),
			'section'  => 'atollmatrix_menutype_section',
			'default'  => 'default',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default'   => esc_html__( 'Flags', 'atollmatrix' ),
				'lang-code' => esc_html__( 'Language', 'atollmatrix' ),
				'flag-code' => esc_html__( 'Flag + Language', 'atollmatrix' ),
			),
		)
	);

	// Menu Type
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'menu_type',
			'label'    => esc_html__( 'Menu Style', 'atollmatrix' ),
			'section'  => 'atollmatrix_menutype_section',
			'default'  => 'left-logo',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'left-logo'        => esc_html__( 'Left Logo', 'atollmatrix' ),
				'left-logo-boxed'  => esc_html__( 'Left Boxed', 'atollmatrix' ),
				'centered-logo'    => esc_html__( 'Centered Logo', 'atollmatrix' ),
				'split-menu'       => esc_html__( 'Split Menu', 'atollmatrix' ),
				'toggle-main-menu' => esc_html__( 'Toggle Menu', 'atollmatrix' ),
				'vertical-menu'    => esc_html__( 'Vertical Menu', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'submenu_indicator',
			'label'    => esc_html__( 'Add Parent menu submenu indicators', 'atollmatrix' ),
			'section'  => 'atollmatrix_menutext_section',
			'default'  => false,
			'priority' => 10,
		)
	);


	// Menu Text
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'settings'        => 'menutext_font',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Font', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'default'         => 'sans-serif',
			'priority'        => 10,
			'choices'         => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'menutext_size',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Menu Text Size', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'menutext_letterspacing',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Menu Text Letterpacing', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'menutextsub_size',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Sub Menu Text Size', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'text',
			'settings'        => 'menutext_weight',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Menu Text Weight', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);

	// Vertical Menu Text
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'settings'        => 'vertical_menutext_font',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Menu Font', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'default'         => 'sans-serif',
			'priority'        => 10,
			'choices'         => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'vertical_menutext_size',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Menu Text Size', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'vertical_menutext_letterspacing',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Menu Text Letterpacing', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'text',
			'settings'        => 'vertical_menutext_weight',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Menu Text Weight', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	// End
	// Vertical Footer Text
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'settings'        => 'vertical_footer_font',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Footer Font', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'default'         => 'sans-serif',
			'priority'        => 10,
			'choices'         => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'vertical_footertext_size',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Footer Text Size', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'dimension',
			'settings'        => 'vertical_footertext_letterspacing',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Footer Text Letterpacing', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'text',
			'settings'        => 'vertical_footertext_weight',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_attr__( 'Vertical Footer Text Weight', 'atollmatrix' ),
			'description'     => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'         => 'atollmatrix_menutext_section',
			'priority'        => 10,
			'default'         => '',
		)
	);
	// End

	// Vertical Menu Colors

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.vertical-menu ul.mtree i,.vertical-menu ul.mtree a,.vertical-menu-wrap ul.mtree li.mtree-node > a::after,.vertical-menu ul.mtree li li a,.vertical-menu ul.mtree .sub-menu .sub-menu a',
					'property' => 'color',
				),
				array(
					'element'  => '.vertical-menu ul.mtree > li.mtree-open::before',
					'property' => 'background-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_hover_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Hovered Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.vertical-menu ul.mtree li:hover i, .vertical-menu ul.mtree li > a:hover, .vertical-menu ul.mtree a:hover',
					'property' => 'color',
				),
			),
		)
	);


	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_opened_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Opened Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.vertical-menu ul.mtree li.mtree-open > a',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_footertext_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Footer Text Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-footer-copyright',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_socialicons_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Footer Social icons Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical.header-type-overlay .vertical-footer-wrap .social-header-wrap ul li i,.menu-is-vertical.header-type-inverse .vertical-footer-wrap .social-header-wrap ul li i,.menu-is-vertical.fullscreen-header-dark .vertical-footer-wrap .social-header-wrap ul li.address-text i,.menu-is-vertical.fullscreen-header-bright .vertical-footer-wrap .social-header-wrap ul li i,.menu-is-vertical .vertical-footer-wrap .social-header-wrap ul li,.menu-is-vertical .vertical-footer-wrap .address-text a, .menu-is-vertical .vertical-footer-wrap .social-icon a,.menu-is-vertical .vertical-footer-wrap .social-icon i,.menu-is-vertical .vertical-footer-wrap .social-header-wrap ul li.social-icon i,.menu-is-vertical .vertical-footer-wrap .social-header-wrap ul li.contact-text a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'verticalmenu_socialicons_hover_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Footer Hover Social icons Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-vertical .vertical-footer-wrap .social-icon a:hover.menu-is-vertical .vertical-footer-wrap .social-icon a:hover,.menu-is-vertical .vertical-footer-wrap ul li.social-icon:hover i,.menu-is-vertical .vertical-footer-wrap .vertical-footer-wrap .social-icon:hover,.menu-is-vertical .vertical-footer-wrap .vertical-footer-wrap .social-icon i:hover,.menu-is-vertical .vertical-footer-wrap .social-header-wrap ul li.contact-text a:hover',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'background',
			'settings'        => 'verticalmenu_background',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Menu Background', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => array(
				'background-color'      => 'rgba(80, 80, 80, 1)',
				'background-image'      => '',
				'background-repeat'     => 'no-repeat',
				'background-position'   => 'center center',
				'background-size'       => 'cover',
				'background-attachment' => 'fixed',
			),
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element' => '.vertical-menu-wrap',
				),
			),
		)
	);

	// End

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active.header-type-overlay .header-cart i,.split-menu.sticky-nav-active.menu-is-horizontal .homemenu ul:first-child > li > a,.inverse-sticky.stickymenu-active.page-is-not-fullscreen .homemenu ul:first-child > li > a, .header-type-auto .homemenu ul:first-child > li .wpml-lang-selector-wrap, .header-type-auto .homemenu ul:first-child > li .wpml-lang-selector-wrap a,.header-type-auto .homemenu ul:first-child > li > a, .header-type-auto .header-cart i, .header-type-auto-bright .homemenu ul:first-child > li > a, .header-type-auto.fullscreen-header-bright .homemenu ul:first-child > li > a, .header-type-bright .homemenu ul:first-child > li > a,.inverse-sticky.stickymenu-active.page-is-not-fullscreen .homemenu ul:first-child > li > a, .header-type-auto.fullscreen-slide-dark .homemenu ul:first-child > li > a, .header-type-auto .homemenu ul:first-child > li > a, .header-type-auto .header-cart i, .header-type-auto-dark .homemenu ul:first-child > li > a, .header-type-auto.fullscreen-slide-dark .homemenu ul:first-child > li > a,.compact-layout.page-is-not-fullscreen.header-type-bright .menu-social-header .social-header-wrap .social-icon i,.compact-layout.page-is-not-fullscreen.header-type-bright .homemenu ul:first-child > li > a,.compact-layout.page-is-not-fullscreen.header-type-auto.fullscreen-header-bright .menu-social-header .social-header-wrap .social-icon i,.compact-layout.page-is-not-fullscreen.header-type-auto.fullscreen-header-bright .homemenu ul:first-child > li > a,.compact-layout.page-is-not-fullscreen.header-type-auto .homemenu ul:first-child > li > a',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_background_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Background Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.split-menu.menu-is-horizontal .outer-wrap,.header-type-auto.page-is-not-fullscreen.split-menu.menu-is-horizontal .outer-wrap,.header-type-auto.stickymenu-active.menu-is-horizontal .outer-wrap.stickymenu-zone,.header-type-auto.centered-logo.menu-is-horizontal .outer-wrap,.minimal-logo.menu-is-horizontal .outer-wrap, .splitmenu-logo.menu-is-horizontal .outer-wrap, .left-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.minimal-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.splitmenu-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.left-logo.menu-is-horizontal .outer-wrap,.sticky-nav-active.menu-is-horizontal .outer-wrap,.split-menu.sticky-nav-active.menu-is-horizontal .outer-wrap',
					'property' => 'background',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_border_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Border Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.split-menu.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.split-menu.menu-is-horizontal .outer-wrap,.minimal-logo.menu-is-horizontal .outer-wrap, .splitmenu-logo.menu-is-horizontal .outer-wrap, .left-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.minimal-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.splitmenu-logo.menu-is-horizontal .outer-wrap, .header-type-auto.page-is-not-fullscreen.left-logo.menu-is-horizontal .outer-wrap',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_overlay_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Overlay Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-overlay .header-cart i, .header-type-overlay .homemenu ul:first-child > li .wpml-lang-selector-wrap, .header-type-overlay .homemenu ul:first-child > li .wpml-lang-selector-wrap a, .header-type-overlay .homemenu ul:first-child > li > a, .header-type-overlay .header-cart i',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_inverse_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Inverse Menu Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse-overlay .homemenu .wpml-lang-selector-wrap,.header-type-inverse-overlay .homemenu ul:first-child > li > a,.header-type-inverse .header-cart i, .header-type-inverse .homemenu ul:first-child > li .wpml-lang-selector-wrap, .header-type-inverse .homemenu ul:first-child > li .wpml-lang-selector-wrap a, .header-type-inverse .homemenu ul:first-child > li > a, .header-type-inverse .header-cart i',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_background_inverse_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Background Inverse Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse.split-menu.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen.split-menu.menu-is-horizontal .outer-wrap,.header-type-inverse.stickymenu-active.menu-is-horizontal .outer-wrap.stickymenu-zone,.header-type-inverse.left-logo.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen.left-logo.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen .outer-wrap',
					'property' => 'background',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_border_inverse_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Menu Border Inverse Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse.split-menu.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen.split-menu.menu-is-horizontal .outer-wrap,.header-type-inverse.left-logo.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen.left-logo.menu-is-horizontal .outer-wrap,.header-type-inverse.page-is-not-fullscreen .outer-wrap',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'submenu_bgcolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Submenu Background', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.homemenu ul ul',
					'property' => 'background',
				),
				array(
					'element'  => '.homemenu ul ul',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'submenu_megaheadingcolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Mega Menu Headings', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.homemenu .sf-menu .mega-item .children-depth-0 h6',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'submenu_textcolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Submenu Items', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.homemenu ul ul li a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'submenu_texthovercolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Submenu Items Hover', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.homemenu ul ul li a:hover',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'social_headercolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Social icons color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse .menu-social-header .social-header-wrap .social-icon i,.header-type-inverse .social-header-wrap ul li i,.header-type-inverse .menu-social-header .social-header-wrap ul li,.header-type-inverse .menu-social-header .social-header-wrap .contact-text a,.menu-social-header .social-header-wrap ul li.contact-text a,.menu-social-header .social-header-wrap ul li.contact-text,.header-type-overlay .menu-social-header .social-header-wrap .social-icon i,.header-type-overlay .menu-social-header .social-header-wrap ul li,.header-type-overlay .menu-social-header .social-header-wrap .contact-text,.header-type-overlay .social-header-wrap ul li i,.header-type-overlay .header-cart i,.menu-social-header .social-header-wrap .social-icon i, .header-site-title-section, .header-cart i, .main-menu-on.menu-inverse-on .menu-social-header .social-header-wrap .social-icon i, .main-menu-on.menu-inverse-on .header-cart i,.menu-social-header .social-header-wrap ul li,.menu-social-header .social-header-wrap ul li,.social-header-wrap ul li.address-text i,.header-type-overlay .menu-social-header .social-header-wrap .contact-text a',
					'property' => 'color',
				),
				array(
					'element'  => '.menu-social-header .social-header-wrap ul li::after',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'fullscreensocial_headercolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Fullsreeen Social icons color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.fullscreen-header-bright .menu-social-header .social-header-wrap .contact-text a,.fullscreen-header-dark .social-header-wrap ul li.address-text i,.fullscreen-header-dark .menu-social-header .social-header-wrap .social-icon i,.fullscreen-header-bright .menu-social-header .social-header-wrap .contact-text,.fullscreen-header-bright .social-header-wrap ul li.address-text i,.fullscreen-header-bright .menu-social-header .social-header-wrap .social-icon i,.fullscreen-header-bright .social-header-wrap ul li i,.fullscreen-header-bright .menu-social-header .social-header-wrap ul li,.fullscreen-header-dark .social-header-wrap ul li i,.fullscreen-header-dark .menu-social-header .social-header-wrap ul li',
					'property' => 'color',
				),
				array(
					'element'  => '.fullscreen-header-bright .menu-social-header .social-header-wrap ul li::after,.fullscreen-header-dark .menu-social-header .social-header-wrap ul li::after',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'social_headercolor_inverse',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Inverse Menu Social icons color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse-overlay .menu-social-header .social-header-wrap .contact-text a,.header-type-inverse .menu-social-header .social-header-wrap .contact-text a, .menu-social-header .social-header-wrap ul li.contact-text a,.header-type-inverse .social-header-wrap ul li i,.header-type-inverse .menu-social-header .social-header-wrap .contact-text,.header-type-inverse .social-header-wrap ul li.address-text i,.header-type-inverse .menu-social-header .social-header-wrap ul li,.header-type-inverse-overlay .menu-social-header .social-header-wrap .social-icon i,.header-type-inverse-overlay .menu-social-header .social-header-wrap ul li, .header-type-inverse-overlay .menu-social-header .social-header-wrap .contact-text, .header-type-inverse-overlay .header-cart i,.menu-social-header .social-header-wrap ul li,.header-type-inverse-overlay .social-header-wrap ul li.address-text i, .header-type-inverse .menu-social-header .social-header-wrap .social-icon i, .header-type-inverse .header-cart i',
					'property' => 'color',
				),
				array(
					'element'  => '.menu-social-header .social-header-wrap ul li::after',
					'property' => 'border-color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_onepage_underline',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'OnePage Menu Underline Color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active .homemenu > ul > li.active > a::after',
					'property' => 'background',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_toggle_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Color for toggle as Main Menu', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:before,.stickymenu-active.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:after,.stickymenu-active.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span,.stickymenu-active.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span,.stickymenu-active.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:before,.stickymenu-active.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:after,.menu-is-onscreen:not(.header-type-overlay).toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span::before, .menu-is-onscreen:not(.header-type-overlay).toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span::after, .menu-is-onscreen:not(.header-type-overlay).toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::before, .menu-is-onscreen:not(.header-type-overlay).toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-open .mobile-toggle-menu-trigger span::after,.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:after,.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span:before,.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-dark:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span:after,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-dark:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span:before,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-dark:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-bright:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span:after,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-bright:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span:before,.toggle-main-menu:not(.mobile-mode-active).fullscreen-header-bright:not(.menu-is-onscreen) .mobile-toggle-menu-trigger span,.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span::before,.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span::after,.header-type-overlay.toggle-main-menu:not(.mobile-mode-active) .mobile-toggle-menu-trigger span',
					'property' => 'background',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_stickymenu_textcolor',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Sticky Menu color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active.header-type-inverse-overlay .header-cart i,.stickymenu-active .homemenu .wpml-lang-selector-wrap,.stickymenu-active.header-type-inverse-overlay .homemenu ul:first-child > li > a,.stickymenu-active.header-type-inverse .header-cart i,.stickymenu-active.header-type-inverse .homemenu ul:first-child > li .wpml-lang-selector-wrap,.stickymenu-active.header-type-inverse .homemenu ul:first-child > li > a,.stickymenu-active.header-type-overlay .homemenu ul:first-child > li .wpml-lang-selector-wrap,.stickymenu-active.header-type-overlay .homemenu ul:first-child > li > a,.stickymenu-active.header-type-overlay .menu-social-header .social-header-wrap .social-icon i,.stickymenu-active.header-type-overlay .menu-social-header .social-header-wrap .contact-text,.stickymenu-active.header-type-overlay .menu-social-header .social-header-wrap .contact-text a,.stickymenu-active.header-type-overlay .header-site-title-section a,.stickymenu-active.header-type-overlay .header-cart i',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'            => 'color',
			'choices'         => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'        => 'mainmenu_stickymenu_color',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Sticky Menu Background color', 'atollmatrix' ),
			'section'         => 'atollmatrix_menucolors_section',
			'default'         => '',
			'priority'        => 10,
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.header-type-inverse.stickymenu-active.menu-is-horizontal .outer-wrap.stickymenu-zone,.header-type-auto.stickymenu-active.menu-is-horizontal .outer-wrap.stickymenu-zone,.stickymenu-active.menu-is-horizontal .outer-wrap.stickymenu-zone',
					'property' => 'background',
				),
			),
		)
	);


	// Right Click Block
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'rightclick_disable',
			'label'     => esc_html__( 'Right Click Block', 'atollmatrix' ),
			'section'   => 'atollmatrix_rightclickblock_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'textarea',
			'settings'    => 'rightclick_disabletext',
			'label'       => esc_html__( 'Right Click Block', 'atollmatrix' ),
			'description' => esc_html__( 'This text appears in the popup when right click is disabled.', 'atollmatrix' ),
			'section'     => 'atollmatrix_rightclickblock_section',
			'default'     => esc_html__( 'You can enable/disable right clicking from Theme Options and customize this message too.', 'atollmatrix' ),
			'priority'    => 10,
		)
	);
	/**
	 * Typography Control.
	 */
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'typography',
			'settings'  => 'rcm_typsography',
			'label'     => esc_attr__( 'Typography Control Label', 'atollmatrix' ),
			'section'   => 'atollmatrix_rightclickblock_section',
			'priority'  => 10,
			'transport' => 'auto',
			'default'   => array(
				'font-family'    => 'inherit',
				'variant'        => '300',
				'font-size'      => '28px',
				'line-height'    => '1.314',
				'letter-spacing' => '0',
				'color'          => '#ffffff',
			),
			'output'    => array(
				array(
					'element' => '.dimmer-text',
				),
			),
			'choices'   => array(
				'fonts' => array(
					'google'   => array( 'popularity', 60 ),
					'families' => array(
						'custom' => array(
							'text'     => 'Quick Fonts',
							'children' => array(
								array(
									'id'   => 'helvetica-neue',
									'text' => 'Helvetica Neue',
								),
								array(
									'id'   => 'linotype-authentic',
									'text' => 'Linotype Authentic',
								),
							),
						),
					),
					'variants' => array(
						'helvetica-neue'     => array( 'regular', '900' ),
						'linotype-authentic' => array( 'regular', '100', '300' ),
					),
				),
			),
		)
	);
	// Rcm Background
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'background',
			'settings'  => 'rcm_background',
			'label'     => esc_html__( 'Right Click Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_rightclickblock_section',
			'default'   => array(
				'background-color'      => 'rgba(0, 0, 0, 0.8)',
				'background-image'      => '',
				'background-repeat'     => 'no-repeat',
				'background-position'   => 'center center',
				'background-size'       => 'cover',
				'background-attachment' => 'fixed',
			),
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '#dimmer',
				),
			),
		)
	);

	// Elementor
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'elementor_style_settings',
			'label'       => esc_html__( 'Elementor Style', 'atollmatrix' ),
			'description' => esc_html__( 'Disable Elementor Default font and style to Theme Defaults. The choice is found in wp-admin > Elementor > Settings > General Tab', 'atollmatrix' ),
			'section'     => 'atollmatrix_elementor_section',
			'default'     => 'auto',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'default' => esc_html__( 'Theme Defaults', 'atollmatrix' ),
				'keep'    => esc_html__( 'Keep as is', 'atollmatrix' ),
			),
		)
	);

	// Elementor Footer
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'elementor_themebuilder_footer_overide',
			'label'    => esc_html__( 'Themebuilder Footer', 'atollmatrix' ),
			'section'  => 'atollmatrix_elementor_section',
			'default'  => 'overide',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'overide'         => esc_html__( 'Replace with Elementor footer', 'atollmatrix' ),
				'withthemefooter' => esc_html__( 'Display with theme footer', 'atollmatrix' ),
			),
		)
	);

	// Google Maps Api
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'googlemap_api',
			'label'    => esc_html__( 'Google Map API', 'atollmatrix' ),
			'section'  => 'atollmatrix_map_api_section',
			'default'  => '',
			'priority' => 10,
		)
	);
	if ( shortcode_exists( 'instagram-feed' ) ) {
		$insta_notice = '<p class="customizer-theme-notice"><strong>Important Notice:</strong><br/>Instagram is shutting down the Legacy API and generating tokens using apps and plugins. Please authenticate your Instagram account using the Instagram Feeds plugin from Dashboard. After authenticating the instagram account, simply use the controls in this panel to display the footer instagram feeds.</p><p>Legacy API key input ( Discontinued )</p>';
	} else {
		$insta_notice = '<p class="customizer-theme-notice"><strong>Important Notice:</strong><br/>Instagram is shutting down the Legacy API and generating tokens using apps and plugins. Please install and activate <a target="_blank" href="https://wordpress.org/plugins/instagram-feed/">Smash Balloon Social Photo Feed</a> plugin. After authenticating your instagram account with the plugin, simply use the controls in this panel to display the footer instagram feeds.</p><p>Legacy API key input ( Discontinued )</p>';
	}

	// Instagram Maps Api
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'instagram_api',
			'label'       => esc_html__( 'Instagram API', 'atollmatrix' ),
			'description' => $insta_notice,
			'section'     => 'atollmatrix_api_section',
			'default'     => '',
			'priority'    => 10,
		)
	);
	// Instagram Enable
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'instagram_footer',
			'label'    => esc_html__( 'Enable Instagram Footer', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => false,
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'instagram_widget_location',
			'label'    => esc_html__( 'Instagram Location', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => 'above',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'above' => esc_html__( 'Above Widgets', 'atollmatrix' ),
				'below' => esc_html__( 'Below Widgets', 'atollmatrix' ),
			),
		)
	);

	// Menu Type
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'select',
			'settings'        => 'instagram_location',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Display Instagram in Vertical Menu', 'atollmatrix' ),
			'section'         => 'atollmatrix_api_section',
			'default'         => 'left-logo',
			'priority'        => 10,
			'multiple'        => 1,
			'choices'         => array(
				'instagram-pagefooter'   => esc_html__( 'Display in Page Footer', 'atollmatrix' ),
				'instagram-verticalmenu' => esc_html__( 'Display in Vertical Menu', 'atollmatrix' ),
			),
		)
	);
	// Instagram Username
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'insta_username',
			'description' => esc_html__( 'Displays in Page Footer.', 'atollmatrix' ),
			'label'       => esc_attr__( 'Instagram username', 'atollmatrix' ),
			'section'     => 'atollmatrix_api_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	// Instagram Image Limit
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'insta_image_limit',
			'label'    => esc_html__( 'Instagram Image Limit', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => '20',
			'priority' => 10,
			'choices'  => array(
				'min'  => 15,
				'max'  => 20,
				'step' => 1,
			),
		)
	);
	// Instagram row
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'insta_image_rows',
			'label'    => esc_html__( 'Instagram Row', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => '2',
			'priority' => 10,
			'choices'  => array(
				'min'  => 1,
				'max'  => 2,
				'step' => 1,
			),
		)
	);
	// Instagram columns
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'insta_image_columns',
			'label'    => esc_html__( 'Instagram Columns', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => '8',
			'priority' => 10,
			'choices'  => array(
				'min'  => 4,
				'max'  => 8,
				'step' => 1,
			),
		)
	);
	// Instagram row
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'slider',
			'settings'    => 'insta_image_container',
			'label'       => esc_html__( 'Instagram Container Width', 'atollmatrix' ),
			'description' => esc_html__( 'Please reload or resize the browser window to see new grid size', 'atollmatrix' ),
			'section'     => 'atollmatrix_api_section',
			'default'     => '55',
			'priority'    => 10,
			'choices'     => array(
				'min'  => 55,
				'max'  => 100,
				'step' => 1,
			),
			'transport'   => 'auto',
			'output'      => array(
				array(
					'element'  => '.insta-grid-wrap',
					'property' => 'width',
					'units'    => '%',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'insta_image_space',
			'label'    => esc_html__( 'Instagram Grid Space', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => 'false',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default', 'atollmatrix' ),
				'nogap'   => esc_html__( 'No Gap', 'atollmatrix' ),
			),
		)
	);
	// Slideshow Effect
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'insta_transition',
			'label'    => esc_html__( 'Instagram Transition', 'atollmatrix' ),
			'section'  => 'atollmatrix_api_section',
			'default'  => 'false',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'false'             => esc_html__( 'Disable Slideshow', 'atollmatrix' ),
				'random'            => esc_html__( 'Random', 'atollmatrix' ),
				'fadeInOut'         => 'fadeInOut',
				'slideLeft'         => 'slideLeft',
				'slideRight'        => 'slideRight',
				'slideTop'          => 'slideTop',
				'slideBottom'       => 'slideBottom',
				'rotateLeft'        => 'rotateLeft',
				'rotateRight'       => 'rotateRight',
				'rotateTop'         => 'rotateTop',
				'rotateBottom'      => 'rotateBottom',
				'scale'             => 'scale',
				'rotate3d'          => 'rotate3d',
				'rotateLeftScale'   => 'rotateLeftScale',
				'rotateRightScale'  => 'rotateRightScale',
				'rotateTopScale'    => 'rotateTopScale',
				'rotateBottomScale' => 'rotateBottomScale',
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'custom_logo_url',
			'label'       => esc_attr__( 'Custom Logo url', 'atollmatrix' ),
			'description' => esc_attr__( 'Custom logo url instead of default', 'atollmatrix' ),
			'section'     => 'atollmatrix_logo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	// Logo Height
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'settings'        => 'logo_height',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Logo Height', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '50',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.menu-is-horizontal .logo img',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'logo_topspace',
			'label'           => esc_html__( 'Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '42',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => 'body.menu-is-horizontal .logo img',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'split-menu',
				),
			),
			'settings'        => 'splitmenulogo_topspace',
			'label'           => esc_html__( 'Split Menu Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '26',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.split-menu.menu-is-horizontal .logo img',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'split-menu',
				),
			),
			'settings'        => 'splitmenulogo_centeroffset',
			'label'           => esc_html__( 'Split Menu Center Offset', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '0',
			'priority'        => 10,
			'choices'         => array(
				'min'  => -500,
				'max'  => 500,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.split-menu .homemenu',
					'property' => 'left',
					'units'    => 'px',
				),
			),
		)
	);

	// Centered Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'centered-logo',
				),
			),
			'settings'        => 'logo_centered_topspace',
			'label'           => esc_html__( 'Centered Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '60',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.centered-logo.menu-is-horizontal .logo img',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Centered Logo Left Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'centered-logo',
				),
			),
			'settings'        => 'logo_centered_leftspace',
			'label'           => esc_html__( 'Centered Logo Left Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '0',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.centered-logo.menu-is-horizontal .logo img',
					'property' => 'padding-left',
					'units'    => 'px',
				),
			),
		)
	);

	// Centered Logo Bottom Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'centered-logo',
				),
			),
			'settings'        => 'logo_centered_bottomspace',
			'label'           => esc_html__( 'Centered Logo Bottom Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '18',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.centered-logo.menu-is-horizontal .logo img',
					'property' => 'padding-bottom',
					'units'    => 'px',
				),
			),
		)
	);

	// Compact Top Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'compact-minimal-top',
				),
			),
			'settings'        => 'logo_compact_top_topspace',
			'label'           => esc_html__( 'Compact-Top Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '9',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.compact-layout.compact-minimal-top:not(.mobile-mode-active).menu-is-horizontal .logo img',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Compact Left Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'compact-minimal-left',
				),
			),
			'settings'        => 'logo_compact_left_topspace',
			'label'           => esc_html__( 'Compact-Left Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '70',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.compact-layout.compact-minimal-left:not(.mobile-mode-active).menu-is-horizontal .logo img',
					'property' => 'margin-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Condition Based End

	// Logo Left Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'logo_leftspace',
			'label'           => esc_html__( 'Logo Left Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '70',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => 'body.menu-is-horizontal .logo img',
					'property' => 'padding-left',
					'units'    => 'px',
				),
				array(
					'element'  => '.compact-layout.compact-minimal-left:not(.mobile-mode-active).menu-is-horizontal .logo img',
					'property' => 'margin-left',
					'units'    => 'px',
				),
			),
		)
	);
	// Sticky Menu Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'logo_sticky_topspace',
			'label'           => esc_html__( 'Sticky Menu Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '24',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active.menu-is-horizontal .logo',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);
	// Logo Height
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'settings'        => 'logo_sticky_height',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Sticky Menu Logo Height', 'atollmatrix' ),
			'section'         => 'atollmatrix_logo_section',
			'default'         => '50',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.stickymenu-active.menu-is-horizontal .logo img',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'textlogo_tag',
			'label'    => esc_html__( 'Text Logo HTML Tag', 'atollmatrix' ),
			'section'  => 'atollmatrix_textlogo_section',
			'default'  => 'h1',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'h1'  => esc_html__( 'H1', 'atollmatrix' ),
				'h2'  => esc_html__( 'H2', 'atollmatrix' ),
				'h3'  => esc_html__( 'H3', 'atollmatrix' ),
				'h4'  => esc_html__( 'H4', 'atollmatrix' ),
				'h5'  => esc_html__( 'H5', 'atollmatrix' ),
				'h6'  => esc_html__( 'H6', 'atollmatrix' ),
				'div' => esc_html__( 'DIV', 'atollmatrix' ),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'textlogo_font',
			'label'    => esc_html__( 'Text Logo Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_textlogo_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'textlogo_size',
			'label'       => esc_attr__( 'Text Logo Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'textlogo_letterspacing',
			'label'       => esc_attr__( 'Text Logo Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'textlogo_weight',
			'label'       => esc_attr__( 'Text Logo Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'responsivetextlogo_size',
			'label'       => esc_attr__( 'Responsive Text Logo Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'responsivetextlogo_letterspacing',
			'label'       => esc_attr__( 'Responsive Text Logo Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'responsivetextlogo_weight',
			'label'       => esc_attr__( 'Responsive Text Logo Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	// Site Tag
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'tag_textlogo_status',
			'label'    => esc_html__( 'Display site tag below text logo', 'atollmatrix' ),
			'section'  => 'atollmatrix_textlogo_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'tag_textlogo_font',
			'label'    => esc_html__( 'Tag Logo Font', 'atollmatrix' ),
			'section'  => 'atollmatrix_textlogo_section',
			'default'  => 'sans-serif',
			'priority' => 10,
			'choices'  => Kirki_Fonts::get_font_choices(),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'tag_textlogo_size',
			'label'       => esc_attr__( 'Tag Logo Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'tag_textlogo_letterspacing',
			'label'       => esc_attr__( 'Tag Logo Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'tag_textlogo_weight',
			'label'       => esc_attr__( 'Tag Logo Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'tag_responsivetextlogo_size',
			'label'       => esc_attr__( 'Responsive Tag Logo Size', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 12px , 12em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'dimension',
			'settings'    => 'tag_responsivetextlogo_letterspacing',
			'label'       => esc_attr__( 'Responsive Tag Logo Letterpacing', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 1px , 1em', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'tag_responsivetextlogo_weight',
			'label'       => esc_attr__( 'Responsive Tag Logo Weight', 'atollmatrix' ),
			'description' => esc_attr__( 'eg. 100, 200, 300, 400, 500, 600, 700, 800, 900', 'atollmatrix' ),
			'section'     => 'atollmatrix_textlogo_section',
			'priority'    => 10,
			'default'     => '',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'text_logo_color',
			'label'     => esc_html__( 'Text Logo Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_textlogo_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .header-site-title-section h1.site-title,body .header-site-title-section .site-title,.vertical-site-title-section h1.site-title,.vertical-site-title-section .site-title',
					'property' => 'color',
				),
				array(
					'element'  => 'body .header-site-title-section h1.site-title a,body .header-site-title-section .site-title a,.vertical-site-title-section h1.site-title a,.vertical-site-title-section .site-title a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'text_taglogo_color',
			'label'     => esc_html__( 'Tag Logo Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_textlogo_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .header-site-title-section .site-description',
					'property' => 'color',
				),
				array(
					'element'  => 'body .header-site-title-section .site-description a',
					'property' => 'color',
				),
			),
		)
	);

	// Text logo
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'textlogo_topspace',
			'label'           => esc_html__( 'Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_textlogo_section',
			'default'         => '50',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => 'body.minimal-logo .header-site-title-section, body.splitmenu-logo .header-site-title-section, body.left-logo .header-site-title-section',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);
	// Text logo
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'textlogo_leftspace',
			'label'           => esc_html__( 'Logo Left Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_textlogo_section',
			'default'         => '70',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => 'body.minimal-logo .header-site-title-section, body.splitmenu-logo .header-site-title-section, body.left-logo .header-site-title-section',
					'property' => 'padding-left',
					'units'    => 'px',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'text_mobilelogo_color',
			'label'     => esc_html__( 'Mobile Text Logo Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_textlogo_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .mobile-site-title-section h1.site-title,body .mobile-site-title-section .site-title',
					'property' => 'color',
				),
				array(
					'element'  => 'body .mobile-site-title-section h1.site-title a,body .mobile-site-title-section .site-title a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'text_mobiletaglogo_color',
			'label'     => esc_html__( 'Mobile Tag Logo Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_textlogo_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .mobile-site-title-section .site-description',
					'property' => 'color',
				),
				array(
					'element'  => 'body .mobile-site-title-section .site-description a',
					'property' => 'color',
				),
			),
		)
	);

	// Text logo
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'centered-logo',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'split-menu',
				),
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'settings'        => 'mobile_textlogo_topspace',
			'label'           => esc_html__( 'Mobile Text Logo Top Space', 'atollmatrix' ),
			'section'         => 'atollmatrix_textlogo_section',
			'default'         => '12',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => 'body .mobile-site-title-section',
					'property' => 'top',
					'units'    => 'px',
				),
				array(
					'element'  => 'body.admin-bar .mobile-site-title-section',
					'property' => 'top',
					'units'    => 'px',
				),
			),
		)
	);

	// Responsive Logo Height
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'responsive_logo_height',
			'label'     => esc_html__( 'Logo Height', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivelogo_section',
			'default'   => '22',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.logo-mobile .logoimage',
					'property' => 'height',
					'units'    => 'px',
				),
			),
		)
	);
	// Responsive Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'responsive_logo_topmargin',
			'label'     => esc_html__( 'Logo Top Space', 'atollmatrix' ),
			'section'   => 'atollmatrix_responsivelogo_section',
			'default'   => '21',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.logo-mobile .logoimage',
					'property' => 'top',
					'units'    => 'px',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'responsive_menu_keep_open',
			'label'    => esc_html__( 'Show page with current menu open', 'atollmatrix' ),
			'section'  => 'atollmatrix_responsivelogo_section',
			'default'  => false,
			'priority' => 10,
		)
	);

	// Footer Logo Height
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'footer_logo_width',
			'label'     => esc_html__( 'Logo Width', 'atollmatrix' ),
			'section'   => 'atollmatrix_footerlogo_section',
			'default'   => '123',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 0,
				'max'  => 800,
				'step' => 1,
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright .footer-logo-image',
					'property' => 'width',
					'units'    => 'px',
				),
			),
		)
	);
	// Footer Logo Top Space
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'footer_logo_topmargin',
			'label'     => esc_html__( 'Logo Top Space', 'atollmatrix' ),
			'section'   => 'atollmatrix_footerlogo_section',
			'default'   => '0',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright .footer-logo-image',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);
	// Footer Logo Bottom Space
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'footer_logo_bottommargin',
			'label'     => esc_html__( 'Logo Bottom Space', 'atollmatrix' ),
			'section'   => 'atollmatrix_footerlogo_section',
			'default'   => '0',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 0,
				'max'  => 300,
				'step' => 1,
			),
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright .footer-logo-image',
					'property' => 'padding-bottom',
					'units'    => 'px',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'preloader_style',
			'label'    => esc_html__( 'Preloader Style', 'atollmatrix' ),
			'section'  => 'atollmatrix_preloader_section',
			'default'  => 'false',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default', 'atollmatrix' ),
				'spinner' => esc_html__( 'Spinner', 'atollmatrix' ),
			),
		)
	);

	// Preloader
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'preloader_color',
			'label'     => esc_html__( 'Preloader Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_preloader_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.menu-is-vertical .themeloader__figure,.menu-is-vertical.page-is-not-fullscreen .loading-bar,.menu-is-vertical.page-is-fullscreen .loading-bar,.menu-is-horizontal .themeloader__figure,.menu-is-horizontal.page-is-not-fullscreen .loading-bar,.menu-is-horizontal.page-is-fullscreen .loading-bar',
					'property' => 'border-color',
				),
				array(
					'element'  => '.menu-is-vertical.page-is-not-fullscreen .loading-bar:after,.menu-is-vertical.page-is-fullscreen .loading-bar:after,.menu-is-horizontal.page-is-not-fullscreen .loading-bar:after,.menu-is-horizontal.page-is-fullscreen .loading-bar:after',
					'property' => 'background-color',
				),
			),
		)
	);

	// Preloader Background
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'background',
			'settings'  => 'preloader_background',
			'label'     => esc_html__( 'Preloader Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_preloader_section',
			'default'   => array(
				'background-color'      => '#505050',
				'background-image'      => '',
				'background-repeat'     => 'no-repeat',
				'background-position'   => 'center center',
				'background-size'       => 'cover',
				'background-attachment' => 'fixed',
			),
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.preloader-style-default.loading-spinner,.preloader-cover-screen',
				),
			),
		)
	);

	// Fullscreen Controls
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'audio_loop',
			'label'     => esc_html__( 'Loop Audio', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'audio_volume',
			'label'     => esc_html__( 'On Start Volume', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => '75',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 1,
				'max'  => 100,
				'step' => 1,
			),
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'fullscreen_disableresponsiveset',
			'description' => 'Use source image for all devices',
			'label'       => esc_html__( 'Disable Responsive Image Set', 'atollmatrix' ),
			'section'     => 'atollmatrix_fullscreenmedia_section',
			'default'     => false,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'hprogressbar_enable',
			'label'     => esc_html__( 'Progress Bar', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'hplaybutton_enable',
			'label'     => esc_html__( 'Slideshow Play button', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'hnavigation_enable',
			'label'     => esc_html__( 'Slideshow Navigation Arrows', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'hcontrolbar_enable',
			'label'     => esc_html__( 'Slideshow Controls', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'slideshow_autoplay',
			'label'     => esc_html__( 'Slideshow Autoplay', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'slideshow_pause_on_last',
			'label'     => esc_html__( 'Slideshow Pause on Last Slide', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'slideshow_pause_hover',
			'label'     => esc_html__( 'Slideshow Pause on Hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'slideshow_vertical_center',
			'label'     => esc_html__( 'Vertical Center Images', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'slideshow_horizontal_center',
			'label'     => esc_html__( 'Horizontal Center Images', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'slideshow_interval',
			'label'     => esc_html__( 'Length between transitions', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => '8000',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 500,
				'max'  => 20000,
				'step' => 1,
			),
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'slideshow_transition_speed',
			'label'     => esc_html__( 'Speed of transition', 'atollmatrix' ),
			'section'   => 'atollmatrix_fullscreenmedia_section',
			'default'   => '1000',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 500,
				'max'  => 20000,
				'step' => 1,
			),
			'transport' => 'auto',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'fullscreen_permalink_slug',
			'label'       => esc_html__( 'Fullscreen Permalink slug', 'atollmatrix' ),
			'description' => esc_html__( 'Requires a unique slug name. After changing the slug name please make sure to flush the old cache by visiting wp-admin > Settings > Permalinks . Visiting the wp-admin page will auto renew permalinks. Otherwise it can give a 404 page not found error.', 'atollmatrix' ),
			'section'     => 'atollmatrix_fullscreenmedia_section',
			'default'     => '',
			'priority'    => 10,
		)
	);

	// Fullscreen Controls
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'fotorama_autoplay',
			'label'     => esc_html__( 'Fotorama Autoplay', 'atollmatrix' ),
			'section'   => 'atollmatrix_fotoramaslides_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'slider',
			'settings'  => 'fotorama_autoplay_speed',
			'label'     => esc_html__( 'Autoplay Speed', 'atollmatrix' ),
			'section'   => 'atollmatrix_fotoramaslides_section',
			'default'   => '8000',
			'priority'  => 10,
			'choices'   => array(
				'min'  => 500,
				'max'  => 20000,
				'step' => 1,
			),
			'transport' => 'auto',
		)
	);


	//Hompepage
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'fullcscreen_henable',
			'label'     => esc_html__( 'Enable Fullscreen Home', 'atollmatrix' ),
			'section'   => 'atollmatrix_home_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	if ( ! empty( $fullscreenposts ) ) {

		// Slideshow for Page Settings
		atollmatrix_kirki_add_field(
			array(
				'type'        => 'select',
				'settings'    => 'fullcscreen_hselected',
				'label'       => esc_html__( 'Slideshow for Homepage', 'atollmatrix' ),
				'description' => esc_html__( 'Choose slideshow for homepage', 'atollmatrix' ),
				'section'     => 'atollmatrix_home_section',
				'default'     => $default_fullscreen,
				'priority'    => 10,
				'multiple'    => 1,
				'choices'     => $fullscreenposts,
			)
		);
	}

	// 404
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'headertype_404',
			'label'    => esc_html__( '404 Header Type', 'atollmatrix' ),
			'section'  => 'atollmatrix_404_section',
			'default'  => 'auto',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'auto'    => esc_html__( 'Default', 'atollmatrix' ),
				'overlay' => esc_html__( 'Overlay', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'background',
			'settings'  => 'general_404_background',
			'label'     => esc_html__( '404 Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_404_section',
			'default'   => array(
				'background-color'      => '#eaeaea',
				'background-image'      => '',
				'background-repeat'     => 'no-repeat',
				'background-position'   => 'center center',
				'background-size'       => 'cover',
				'background-attachment' => 'fixed',
			),
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element' => '.error404',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'general_404_color',
			'label'     => esc_html__( '404 Text Color', 'atollmatrix' ),
			'section'   => 'atollmatrix_404_section',
			'default'   => '#000000',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mtheme-404-wrap .mtheme-404-error-message1,.entry-content .mtheme-404-wrap h4,.mtheme-404-wrap #searchbutton i',
					'property' => 'color',
				),
				array(
					'element'  => '.mtheme-404-wrap #searchform input',
					'property' => 'border-color',
				),
				array(
					'element'  => '.mtheme-404-wrap #searchform input',
					'property' => 'color',
				),
				array(
					'element'  => '.mtheme-404-wrap .mtheme-404-icon i',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'pagenoutfound_title',
			'label'       => esc_html__( '404 Title', 'atollmatrix' ),
			'description' => esc_html__( '404 Page not found title', 'atollmatrix' ),
			'section'     => 'atollmatrix_404_section',
			'default'     => '404 Page not Found!',
			'priority'    => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'pagenoutfound_search',
			'label'       => esc_html__( '404 Search Text', 'atollmatrix' ),
			'description' => esc_html__( '404 Search Text', 'atollmatrix' ),
			'section'     => 'atollmatrix_404_section',
			'default'     => 'Would you like to search for the page',
			'priority'    => 10,
		)
	);

	// Events
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'events_time_format',
			'label'    => esc_html__( 'Events Time format', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'auto',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'conventional' => esc_html__( 'AM/PM', 'atollmatrix' ),
				'24hr'         => esc_html__( '24 Hrs', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'events_address_format',
			'label'    => esc_html__( 'Events Address format', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'default',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default', 'atollmatrix' ),
				'sszv'    => esc_html__( 'Street, State, Zip, Venue', 'atollmatrix' ),
				'zvss'    => esc_html__( 'Zip, Venue, Street, State', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'event_gallery_title',
			'label'    => esc_html__( 'Archive Event gallery title', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => esc_html__( 'Events', 'atollmatrix' ),
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'dropdown-pages',
			'settings' => 'events_archive_page',
			'label'    => esc_html__( 'Custom Events Archive Page', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 0,
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'event_archive_nav',
			'label'     => esc_html__( 'Events Archive Navigation', 'atollmatrix' ),
			'section'   => 'atollmatrix_events_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'event_achivelisting',
			'label'    => esc_html__( 'Events Archive Grid Column', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => '3',
			'priority' => 10,
			'choices'  => array(
				'min'  => 1,
				'max'  => 4,
				'step' => 1,
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'events_readmore',
			'label'    => esc_html__( 'Events Readmore', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => esc_html__( 'Continue Reading', 'atollmatrix' ),
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'event_comments',
			'label'     => esc_html__( 'Event Comments', 'atollmatrix' ),
			'section'   => 'atollmatrix_events_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'events_permalink_slug',
			'label'       => esc_html__( 'Event Permalink slug', 'atollmatrix' ),
			'description' => esc_html__( 'Requires a unique slug name. After changing the slug name please make sure to flush the old cache by visiting wp-admin > Settings > Permalinks . Visiting the wp-admin page will auto renew permalinks. Otherwise it can give a 404 page not found error.', 'atollmatrix' ),
			'section'     => 'atollmatrix_events_section',
			'default'     => '',
			'priority'    => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'event_postponed',
			'label'    => esc_html__( 'Postponed Event Text', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'This event has been postponed',
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'event_cancelled',
			'label'    => esc_html__( 'Cancelled Event Text', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'This event has been cancelled',
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'event_fullevent',
			'label'    => esc_html__( 'Text to notify that event is full', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'Sorry, participation for this event is full',
			'priority' => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'event_pastevent',
			'label'    => esc_html__( 'Past Event Text', 'atollmatrix' ),
			'section'  => 'atollmatrix_events_section',
			'default'  => 'This is a past event',
			'priority' => 10,
		)
	);


	// Proofing
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'proofing_archive_format',
			'label'       => esc_html__( 'Proofing archive format', 'atollmatrix' ),
			'description' => esc_html__( 'Image format for Proofing archives', 'atollmatrix' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'landscape',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'landscape' => esc_html__( 'Landscape', 'atollmatrix' ),
				'portrait'  => esc_html__( 'Portrait', 'atollmatrix' ),
				'square'    => esc_html__( 'Square', 'atollmatrix' ),
				'masonary'  => esc_html__( 'Masonry', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'proofing_achivelisting',
			'label'       => esc_html__( 'Proofing archive format', 'atollmatrix' ),
			'description' => esc_html__( 'Proofing archive listing columns', 'atollmatrix' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => '3',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'4' => esc_html__( 'Four', 'atollmatrix' ),
				'3' => esc_html__( 'Three', 'atollmatrix' ),
				'2' => esc_html__( 'Two', 'atollmatrix' ),
				'1' => esc_html__( 'One', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'proofing_archive_disable',
			'label'     => esc_html__( 'Disable proofing archive', 'atollmatrix' ),
			'section'   => 'atollmatrix_proofing_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_archive_title',
			'label'       => esc_html__( 'Proofing Archive Title', 'atollmatrix' ),
			'description' => esc_html__( 'This is the Title for Proofing archive', 'atollmatrix' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'Proofing Archive',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_all_items',
			'label'       => esc_html__( 'Proofing All items Text', 'kordex' ),
			'description' => esc_html__( 'Displayed as first item in filterable', 'kordex' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'All items',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_selected_items',
			'label'       => esc_html__( 'Proofing Selected items Text', 'kordex' ),
			'description' => esc_html__( 'Displayed as selected in filterable', 'kordex' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'Selected',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_rejected_items',
			'label'       => esc_html__( 'Proofing Rejected items Text', 'kordex' ),
			'description' => esc_html__( 'Displayed as rejected in filterable', 'kordex' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'Rejected',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_editorchoice_items',
			'label'       => esc_html__( 'Proofing Editors choice Text', 'kordex' ),
			'description' => esc_html__( 'Displayed as editors choice in filterable', 'kordex' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'Editors Choice',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_selected_count',
			'label'       => esc_html__( 'Proofing Count Selected Text', 'kordex' ),
			'description' => esc_html__( 'Displayed as selected count in filterable', 'kordex' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => 'Selected',
			'priority'    => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'proofing_permalink_slug',
			'label'       => esc_html__( 'Proofing Permalink slug', 'atollmatrix' ),
			'description' => esc_html__( 'Requires a unique slug name. After changing the slug name please make sure to flush the old cache by visiting wp-admin > Settings > Permalinks . Visiting the wp-admin page will auto renew permalinks. Otherwise it can give a 404 page not found error.', 'atollmatrix' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => '',
			'priority'    => 10,
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'client_permalink_slug',
			'label'       => esc_html__( 'Client Permalink slug', 'atollmatrix' ),
			'description' => esc_html__( 'Requires a unique slug name. After changing the slug name please make sure to flush the old cache by visiting wp-admin > Settings > Permalinks . Visiting the wp-admin page will auto renew permalinks. Otherwise it can give a 404 page not found error.', 'atollmatrix' ),
			'section'     => 'atollmatrix_proofing_section',
			'default'     => '',
			'priority'    => 10,
		)
	);


	// Portfolio
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'portfolio_comments',
			'label'     => esc_html__( 'Portfolio Comments', 'atollmatrix' ),
			'section'   => 'atollmatrix_portfolio_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'dropdown-pages',
			'settings' => 'portfolio_archive_page',
			'label'    => esc_html__( 'Custom Portfolio Archive Page', 'atollmatrix' ),
			'section'  => 'atollmatrix_portfolio_section',
			'default'  => 0,
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'portfolio_archive_nav',
			'label'     => esc_html__( 'Portfolio Archive Navigation', 'atollmatrix' ),
			'section'   => 'atollmatrix_portfolio_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'portfolio_archive_format',
			'label'       => esc_html__( 'Portfolio archive format', 'atollmatrix' ),
			'description' => esc_html__( 'Image format for Portfolio archives', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'landscape',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'landscape' => esc_html__( 'Landscape', 'atollmatrix' ),
				'portrait'  => esc_html__( 'Portrait', 'atollmatrix' ),
				'square'    => esc_html__( 'Square', 'atollmatrix' ),
				'masonary'  => esc_html__( 'Masonry', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'portfolio_achivelisting',
			'label'       => esc_html__( 'Portfolio archive format', 'atollmatrix' ),
			'description' => esc_html__( 'Portfolio archive listing columns', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => '3',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'4' => esc_html__( 'Four', 'atollmatrix' ),
				'3' => esc_html__( 'Three', 'atollmatrix' ),
				'2' => esc_html__( 'Two', 'atollmatrix' ),
				'1' => esc_html__( 'One', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'portfolio_archive_title',
			'label'       => esc_html__( 'Portfolio Archive Title', 'atollmatrix' ),
			'description' => esc_html__( 'This is also Label and Title for Portfolio archive', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'Portfolios',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'portfolio_permalink_slug',
			'label'       => esc_html__( 'Portfolio Permalink slug', 'atollmatrix' ),
			'description' => esc_html__( 'Requires a unique slug name. After changing the slug name please make sure to flush the old cache by visiting wp-admin > Settings > Permalinks . Visiting the wp-admin page will auto renew permalinks. Otherwise it can give a 404 page not found error.', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => '',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'portfolio_allitems',
			'label'       => esc_html__( 'Portfolio All items Text', 'atollmatrix' ),
			'description' => esc_html__( 'Displayed as first item in filterable', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'All',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'portfolio_fullscreen_viewtext',
			'label'       => esc_html__( 'Fullscreen Portfolio Button', 'atollmatrix' ),
			'description' => esc_html__( 'Displayed in Fullscreen portfolio slideshow', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'Details',
			'priority'    => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'portfolio_recently',
			'label'       => esc_html__( 'Display Recent Portfolios', 'atollmatrix' ),
			'description' => esc_html__( 'Displays Carousel of Portfolios in details pages', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => true,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'select',
			'settings'    => 'portfolio_recently_format',
			'label'       => esc_html__( 'Recent portfolios image format', 'atollmatrix' ),
			'description' => esc_html__( 'Image format for recent portfolios in portfolio details posts', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'landscape',
			'priority'    => 10,
			'multiple'    => 1,
			'choices'     => array(
				'landscape' => esc_html__( 'Landscape', 'atollmatrix' ),
				'portrait'  => esc_html__( 'Portrait', 'atollmatrix' ),
				'square'    => esc_html__( 'Square', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'portfolio_recentlink',
			'label'     => esc_html__( 'Portfolio Carousel Direct Link', 'atollmatrix' ),
			'section'   => 'atollmatrix_portfolio_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'text',
			'settings'    => 'portfolio_carousel_heading',
			'label'       => esc_html__( 'Recent Portfolio Heading', 'atollmatrix' ),
			'description' => esc_html__( 'Recent portfolio title', 'atollmatrix' ),
			'section'     => 'atollmatrix_portfolio_section',
			'default'     => 'Recently in Portfolio',
			'priority'    => 10,
		)
	);

	//Blog
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'postformat_imagelightbox',
			'label'       => esc_html__( 'Enable lightbox for standard post details featured image.', 'atollmatrix' ),
			'description' => esc_html__( 'Applies to blog posts that do not use Elementor pagebuilder.', 'atollmatrix' ),
			'section'     => 'atollmatrix_blog_section',
			'default'     => false,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_author',
			'label'     => esc_html__( 'Blog post Author info', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_date',
			'label'     => esc_html__( 'Blog post Date', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_tags',
			'label'     => esc_html__( 'Blog post Tags', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_categories',
			'label'     => esc_html__( 'Blog post Categories', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_comment',
			'label'     => esc_html__( 'Blog post Comment Info', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postsingle_navigation',
			'label'     => esc_html__( 'Blog post Navigation', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postformat_fullcontent',
			'label'     => esc_html__( 'Display full contents in archive', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postformat_no_comments',
			'label'     => esc_html__( 'Display Comment are Closed Notice', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'blog_category_style',
			'label'    => esc_html__( 'Blog category listing style', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => 'grid',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'default' => esc_html__( 'Default', 'atollmatrix' ),
				'grid'    => esc_html__( 'Grid', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'blog_grid_achivestyle',
			'label'    => esc_html__( 'Blog category grid columns', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => '3',
			'priority' => 10,
			'choices'  => array(
				'min'  => 1,
				'max'  => 4,
				'step' => 1,
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'author_bio',
			'label'     => esc_html__( 'Display author bio', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'author_publishedby',
			'label'    => esc_html__( 'Published by', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => '',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'author_postsby',
			'label'    => esc_html__( 'Posts by', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => '',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'read_more',
			'label'    => esc_html__( 'Readmore text', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => 'Continue Reading',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'postnavigation_sameterm',
			'label'     => esc_html__( 'Post Navigation in same Category', 'atollmatrix' ),
			'section'   => 'atollmatrix_blog_section',
			'default'   => false,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'postnavigation_prev',
			'label'    => esc_html__( 'Post navigation Prev text', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => 'Prev',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'postnavigation_next',
			'label'    => esc_html__( 'Post navigation Next text', 'atollmatrix' ),
			'section'  => 'atollmatrix_blog_section',
			'default'  => 'Next',
			'priority' => 10,
		)
	);

	//Shop
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'enable_header_cart',
			'label'       => esc_html__( 'Enable header cart', 'atollmatrix' ),
			'description' => esc_attr__( 'Enable header cart', 'atollmatrix' ),
			'section'     => 'atollmatrix_shop_options_section',
			'default'     => false,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'hide_empty_cart',
			'label'       => esc_html__( 'Hide empty cart', 'atollmatrix' ),
			'description' => esc_attr__( 'Hide empty cart', 'atollmatrix' ),
			'section'     => 'atollmatrix_shop_options_section',
			'default'     => false,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	// Stickymenu
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'woo_star_rating',
			'label'    => esc_html__( 'Enable star rating', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => false,
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'shop_page_layout',
			'label'    => esc_html__( 'Shop page layout', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'fullwidth',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'fullwidth' => esc_html__( 'Fullwidth', 'atollmatrix' ),
				'right'     => esc_html__( 'Sidebar Right', 'atollmatrix' ),
				'left'      => esc_html__( 'Sidebar Left', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'shop_archive_layout',
			'label'    => esc_html__( 'Shop archive layout', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'fullwidth',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'fullwidth' => esc_html__( 'Fullwidth', 'atollmatrix' ),
				'right'     => esc_html__( 'Sidebar Right', 'atollmatrix' ),
				'left'      => esc_html__( 'Sidebar Left', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'your_cart_text',
			'label'    => esc_html__( 'Toggle sidebar your cart text', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'Your Cart',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'cart_is_empty_text',
			'label'    => esc_html__( 'Toggle sidebar empty cart text', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'Your cart is currently empty',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'view_cart_button_text',
			'label'    => esc_html__( 'Toggle sidebar view cart text', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'View Cart',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'checkout_cart_button_text',
			'label'    => esc_html__( 'Toggle cidebar checkout text', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => 'Checkout',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'slider',
			'settings' => 'shop_product_count',
			'label'    => esc_html__( 'Shop product count', 'atollmatrix' ),
			'section'  => 'atollmatrix_shop_options_section',
			'default'  => '12',
			'priority' => 10,
			'choices'  => array(
				'min'  => 1,
				'max'  => 45,
				'step' => 1,
			),
		)
	);

	// Dashcart
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_header_qty_indicator_background',
			'label'     => esc_html__( 'Header qty indicator background', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.header-cart .item-count',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_header_qty_indicator_text',
			'label'     => esc_html__( 'Header qty indicator text', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.header-cart .item-count',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_cart_close_icon',
			'label'     => esc_html__( 'Dash cart close icon', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.header-cart-close',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_background',
			'label'     => esc_html__( 'Dash cart background', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mtheme-header-cart',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_title',
			'label'     => esc_html__( 'Dash cart title', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mtheme-header-cart h3',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_item_title',
			'label'     => esc_html__( 'Dash cart item title', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.cart-elements .cart-title',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_item_qty',
			'label'     => esc_html__( 'Dash cart item qty', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.cart-elements .cart-item-quantity-wrap',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_item_buttons',
			'label'     => esc_html__( 'Dash cart buttons', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.cart-buttons a',
					'property' => 'border-color',
				),
				array(
					'element'  => '.cart-buttons a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_item_buttons_hover',
			'label'     => esc_html__( 'Dash cart buttons hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.cart-buttons a:hover',
					'property' => 'border-color',
				),
				array(
					'element'  => '.cart-buttons a:hover',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'dashcart_item_buttons_hover_text',
			'label'     => esc_html__( 'Dash cart buttons hover text', 'atollmatrix' ),
			'section'   => 'atollmatrix_cart_dashbar_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.cart-buttons a:hover',
					'property' => 'color',
				),
			),
		)
	);


	// Lightbox
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'toggle',
			'settings'    => 'enable_gutenberg_lightbox',
			'label'       => esc_html__( 'Enable Lightbox for Gutenberg', 'atollmatrix' ),
			'description' => esc_attr__( 'Adds Lightbox for Media File linked images and galleries in Gutenberg editor. Supports Alt text as title.', 'atollmatrix' ),
			'section'     => 'atollmatrix_lightbox_section',
			'default'     => false,
			'priority'    => 10,
			'transport'   => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_fullscreen',
			'label'     => esc_html__( 'Fullscreen', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_sizetoggle',
			'label'     => esc_html__( 'Sizing', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_download',
			'label'     => esc_html__( 'Download', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_zoomcontrols',
			'label'     => esc_html__( 'Zoom', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_autoplay',
			'label'     => esc_html__( 'Autoplay', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_count',
			'label'     => esc_html__( 'Count', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_title',
			'label'     => esc_html__( 'All Lightbox Text', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_onlytitle',
			'label'     => esc_html__( 'Title', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_onlydesc',
			'label'     => esc_html__( 'Description', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'toggle',
			'settings'  => 'disable_lightbox_onlypurchase',
			'label'     => esc_html__( 'Purchase', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => true,
			'priority'  => 10,
			'transport' => 'auto',
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'lightbox_thumbnails_status',
			'label'    => esc_html__( 'Lightbox Thumbnails', 'atollmatrix' ),
			'section'  => 'atollmatrix_lightbox_section',
			'default'  => 'disable',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'disable' => esc_html__( 'Disable', 'atollmatrix' ),
				'enable'  => esc_html__( 'Enable', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'select',
			'settings' => 'lightbox_transition',
			'label'    => esc_html__( 'Lightbox Transition', 'atollmatrix' ),
			'section'  => 'atollmatrix_lightbox_section',
			'default'  => 'lg-zoom-out',
			'priority' => 10,
			'multiple' => 1,
			'choices'  => array(
				'lg-slide'                    => esc_html__( 'Slide', 'atollmatrix' ),
				'lg-fade'                     => esc_html__( 'Fade', 'atollmatrix' ),
				'lg-zoom-in'                  => esc_html__( 'Zoom in', 'atollmatrix' ),
				'lg-zoom-in-big'              => esc_html__( 'Zoom in Big', 'atollmatrix' ),
				'lg-zoom-out'                 => esc_html__( 'Zoom Out', 'atollmatrix' ),
				'lg-zoom-out-big'             => esc_html__( 'Zoom Out big', 'atollmatrix' ),
				'lg-zoom-out-in'              => esc_html__( 'Zoom Out in', 'atollmatrix' ),
				'lg-zoom-in-out'              => esc_html__( 'Zoom in Out', 'atollmatrix' ),
				'lg-soft-zoom'                => esc_html__( 'Soft Zoom', 'atollmatrix' ),
				'lg-scale-up'                 => esc_html__( 'Scale Up', 'atollmatrix' ),
				'lg-slide-circular'           => esc_html__( 'Slide Circular', 'atollmatrix' ),
				'lg-slide-circular-vertical'  => esc_html__( 'Slide Circular vertical', 'atollmatrix' ),
				'lg-slide-vertical'           => esc_html__( 'Slide Vertical', 'atollmatrix' ),
				'lg-slide-vertical-growth'    => esc_html__( 'Slide Vertical growth', 'atollmatrix' ),
				'lg-slide-skew-only'          => esc_html__( 'Slide Skew only', 'atollmatrix' ),
				'lg-slide-skew-only-rev'      => esc_html__( 'Slide Skew only reverse', 'atollmatrix' ),
				'lg-slide-skew-only-y'        => esc_html__( 'Slide Skew only y', 'atollmatrix' ),
				'lg-slide-skew-only-y-rev'    => esc_html__( 'Slide Skew only y reverse', 'atollmatrix' ),
				'lg-slide-skew'               => esc_html__( 'Slide Skew', 'atollmatrix' ),
				'lg-slide-skew-rev'           => esc_html__( 'Slide Skew reverse', 'atollmatrix' ),
				'lg-slide-skew-cross'         => esc_html__( 'Slide Skew cross', 'atollmatrix' ),
				'lg-slide-skew-cross-rev'     => esc_html__( 'Slide Skew cross reverse', 'atollmatrix' ),
				'lg-slide-skew-ver'           => esc_html__( 'Slide Skew vertically', 'atollmatrix' ),
				'lg-slide-skew-ver-rev'       => esc_html__( 'Slide Skew vertically reverse', 'atollmatrix' ),
				'lg-slide-skew-ver-cross'     => esc_html__( 'Slide Skew vertically cross', 'atollmatrix' ),
				'lg-slide-skew-ver-cross-rev' => esc_html__( 'Slide Skew vertically cross reverse', 'atollmatrix' ),
				'lg-lollipop'                 => esc_html__( 'Lollipop', 'atollmatrix' ),
				'lg-lollipop-rev'             => esc_html__( 'Lollipop reverse', 'atollmatrix' ),
				'lg-rotate'                   => esc_html__( 'Rotate', 'atollmatrix' ),
				'lg-rotate-rev'               => esc_html__( 'Rotate reverse', 'atollmatrix' ),
				'lg-tube'                     => esc_html__( 'Tube', 'atollmatrix' ),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'     => 'text',
			'settings' => 'lightbox_purchase_text',
			'label'    => esc_html__( 'Purchase link text', 'atollmatrix' ),
			'section'  => 'atollmatrix_lightbox_section',
			'default'  => 'Purchase',
			'priority' => 10,
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_bgcolor',
			'label'     => esc_html__( 'Lightbox background color', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mtheme-lightbox.lg-outer',
					'property' => 'background-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_elementbgcolor',
			'label'     => esc_html__( 'Lightbox element colors', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.mtheme-lightbox #lg-counter,.mtheme-lightbox #lg-counter, .mtheme-lightbox .lg-sub-html, .mtheme-lightbox .lg-toolbar .lg-icon, .mtheme-lightbox .lg-actions .lg-next, .mtheme-lightbox .lg-actions .lg-prev',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_titlecolor',
			'label'     => esc_html__( 'Lightbox Title', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .mtheme-lightbox .lg-sub-html,body .mtheme-lightbox .lg-sub-html h4',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_descriptioncolor',
			'label'     => esc_html__( 'Lightbox Description', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .mtheme-lightbox .entry-content',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_purchasecolor',
			'label'     => esc_html__( 'Lightbox Purchase Link', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .lightbox-purchase > a',
					'property' => 'color',
				),
				array(
					'element'  => 'body .lightbox-purchase > a',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'lightbox_purchasecolorhover',
			'label'     => esc_html__( 'Lightbox Purchase Link Hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_lightbox_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => 'body .lightbox-purchase > a:hover',
					'property' => 'color',
				),
				array(
					'element'  => 'body .lightbox-purchase > a:hover',
					'property' => 'border-color',
				),
			),
		)
	);

	// Sidebars
	for ( $sidebar_count = 1; $sidebar_count <= 50; $sidebar_count++ ) {

		atollmatrix_kirki_add_field(
			array(
				'type'        => 'text',
				'settings'    => 'mthemesidebar-' . $sidebar_count,
				'label'       => esc_attr__( 'Sidebar Name ', 'atollmatrix' ) . $sidebar_count,
				'description' => esc_attr__( 'Activate extra sidebar widget. Enter name', 'atollmatrix' ),
				'section'     => 'atollmatrix_addsidebar_section',
				'priority'    => 10,
				'default'     => '',
			)
		);
	}
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'sidebar_headingcolor',
			'label'     => esc_html__( 'Sidebar Headings', 'atollmatrix' ),
			'section'   => 'atollmatrix_sidebarcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.sidebar h3,.sidebar .product-title, .sidebar .woocommerce ul.product_list_widget li a, #events_list .recentpost_info .recentpost_title, #recentposts_list .recentpost_info .recentpost_title, #popularposts_list .popularpost_info .popularpost_title',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'sidebar_linkcolor',
			'label'     => esc_html__( 'Sidebar Links', 'atollmatrix' ),
			'section'   => 'atollmatrix_sidebarcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#recentposts_list .recentpost_info .recentpost_title, #popularposts_list .popularpost_info .popularpost_title,.sidebar a',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'sidebar_textcolor',
			'label'     => esc_html__( 'Sidebar Text', 'atollmatrix' ),
			'section'   => 'atollmatrix_sidebarcolors_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.contact_address_block .about_info, .sidebar-widget #searchform input, .sidebar-widget #searchform i, #recentposts_list p, #popularposts_list p,.sidebar-widget ul#recentcomments li,.sidebar',
					'property' => 'color',
				),
			),
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'     => 'toggle',
			'settings' => 'theme_footer',
			'label'    => esc_html__( 'Page Footer', 'atollmatrix' ),
			'section'  => 'atollmatrix_footer_section',
			'default'  => true,
			'priority' => 10,
		)
	);
	// Footers
	atollmatrix_kirki_add_field(
		array(
			'type'        => 'textarea',
			'settings'    => 'footer_copyright',
			'label'       => esc_html__( 'Footer Text', 'atollmatrix' ),
			'description' => esc_attr__( 'Use [theme_display_current_year] to display current year', 'atollmatrix' ),
			'section'     => 'atollmatrix_footer_section',
			'priority'    => 10,
			'default'     => 'Copyright 2020',
		)
	);

	// Footer Padding Top
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'settings'        => 'responsive_footer_padding_top',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Footer Padding Top', 'atollmatrix' ),
			'section'         => 'atollmatrix_footer_section',
			'default'         => '40',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.footer-outer-wrap #copyright',
					'property' => 'padding-top',
					'units'    => 'px',
				),
			),
		)
	);

	// Footer Padding Bottom
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'slider',
			'settings'        => 'responsive_footer_padding_bottom',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '!==',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Footer Padding Bottom', 'atollmatrix' ),
			'section'         => 'atollmatrix_footer_section',
			'default'         => '40',
			'priority'        => 10,
			'choices'         => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'transport'       => 'auto',
			'output'          => array(
				array(
					'element'  => '.footer-outer-wrap #copyright',
					'property' => 'padding-bottom',
					'units'    => 'px',
				),
			),
		)
	);

	// Footers
	atollmatrix_kirki_add_field(
		array(
			'type'            => 'textarea',
			'settings'        => 'vertical_footer_copyright',
			'active_callback' => array(
				array(
					'setting'  => 'menu_type',
					'operator' => '===',
					'value'    => 'vertical-menu',
				),
			),
			'label'           => esc_html__( 'Vertical Menu Footer Text', 'atollmatrix' ),
			'description'     => esc_attr__( 'Use [theme_display_current_year] to display current year', 'atollmatrix' ),
			'section'         => 'atollmatrix_footer_section',
			'priority'        => 10,
			'default'         => 'Copyright 2022',
		)
	);

	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'footer_background',
			'label'     => esc_html__( 'Footer Background', 'atollmatrix' ),
			'section'   => 'atollmatrix_footer_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright,.footer-outer-wrap',
					'property' => 'background',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'footer_textcolor',
			'label'     => esc_html__( 'Footer text', 'atollmatrix' ),
			'section'   => 'atollmatrix_footer_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.footer-container-column .sidebar-widget .mc4wp-form input[type="submit"],.footer-container-column .sidebar-widget .mc4wp-form input,.footer-container-column label,.horizontal-footer-copyright,.footer-container-column .sidebar-widget .contact_address_block span:before,.footer-container-column .sidebar-widget .footer-widget-block #searchform i,.footer-container-column .sidebar-widget .footer-widget-block.widget_search #searchform input,.sidebar-widget .footer-widget-block.widget_search #searchform input,.footer-container-column table td,.footer-container-column .contact_name,.sidebar-widget .footer-widget-block,.footer-container-column .wp-caption p.wp-caption-text,.footer-widget-block,.footer-container-column .footer-widget-block strong,.footer-container-wrap,#copyright,#footer .social-header-wrap,#footer .social-header-wrap ul li.contact-text a,.footer-container-wrap .sidebar-widget,.footer-container-wrap .opening-hours dt.week',
					'property' => 'color',
				),
				array(
					'element'  => '.footer-container-column #wp-calendar caption,.footer-container-column #wp-calendar thead th,.footer-container-column #wp-calendar tfoot',
					'property' => 'background-color',
				),
				array(
					'element'  => '.footer-container-column .sidebar-widget .mc4wp-form input[type="submit"],.footer-container-column .sidebar-widget .mc4wp-form input,.footer-container-column input,.footer-container-column #wp-calendar tbody td,.sidebar-widget .footer-widget-block.widget_search #searchform input',
					'property' => 'border-color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'footer_headingcolor',
			'label'     => esc_html__( 'Footer Headings', 'atollmatrix' ),
			'section'   => 'atollmatrix_footer_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '.footer-container-column .sidebar-widget h1,.footer-container-column .sidebar-widget h2,.footer-container-column .sidebar-widget h3,.footer-container-column .sidebar-widget h4,.footer-container-column .sidebar-widget h5,.footer-container-column .sidebar-widget h6',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'footer_link',
			'label'     => esc_html__( 'Footer links', 'atollmatrix' ),
			'section'   => 'atollmatrix_footer_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright .horizontal-footer-copyright a,.footer-container-column .sidebar-widget .social-header-wrap ul li.contact-text i,.footer-container-column .sidebar-widget .social-header-wrap ul li.social-icon i,.sidebar-widget .footer-widget-block a,.footer-container-wrap a,#copyright a,.footer-widget-block a,.footer-container-column .sidebar-widget .product-title,.footer-container-column .sidebar-widget .woocommerce ul.product_list_widget li a,.footer-container-column #events_list .recentpost_info .recentpost_title,.footer-container-column #recentposts_list .recentpost_info .recentpost_title,.footer-container-column #popularposts_list .popularpost_info .popularpost_title',
					'property' => 'color',
				),
			),
		)
	);
	atollmatrix_kirki_add_field(
		array(
			'type'      => 'color',
			'choices'   => array(
				'alpha' => true,
				'palettes' => [ '', '#b3842f', '#8a5796', '#6868ac', '#a2c8c9', '#47996b', '#769e51', '#cfbe54' ],
			),
			'settings'  => 'footer_linkhover',
			'label'     => esc_html__( 'Footer link hover', 'atollmatrix' ),
			'section'   => 'atollmatrix_footer_section',
			'default'   => '',
			'priority'  => 10,
			'transport' => 'auto',
			'output'    => array(
				array(
					'element'  => '#copyright .horizontal-footer-copyright a:hover,.footer-container-wrap a:hover,#copyright a:hover,.footer-widget-block a:hover,.sidebar-widget .footer-widget-block a:hover',
					'property' => 'color',
				),
			),
		)
	);
}
