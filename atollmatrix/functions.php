<?php
/**
 * Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.0.0
 */

/**
 * Rewrite flush
 */
function atollmatrix_rewrite_flush() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'atollmatrix_rewrite_flush' );
if ( ! function_exists( 'atollmatrix_setup' ) ) {
	/**
	 * Setup
	 */
	function atollmatrix_setup() {

		remove_theme_support( 'widgets-block-editor' );
		// Add Background Support.
		add_theme_support( 'custom-background' );
		add_theme_support( 'responsive-embeds' );
		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );
		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );
		// Register Menu.
		register_nav_menu( 'main_menu', 'Main Menu' );
		register_nav_menu( 'mobile_menu', 'Mobile Menu' );
		load_theme_textdomain( 'atollmatrix', get_template_directory() . '/languages' );
		$locale      = get_locale();
		$locale_file = get_template_directory() . '/languages/$locale.php';
		if ( is_readable( $locale_file ) ) {
			require_once $locale_file;
		}
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio' ) );

		set_post_thumbnail_size( 150, 150, true ); // Default thumbnail size.
		add_image_size( 'atollmatrix-gridblock-square-big', 770, 770, true ); // Square.
		add_image_size( 'atollmatrix-gridblock-tiny', 160, 160, true ); // Sidebar Thumbnails.
		add_image_size( 'atollmatrix-gridblock-large', 770, 550, true ); // Portfolio.
		add_image_size( 'atollmatrix-gridblock-large-portrait', 550, 770, true ); // Portrait.
		add_image_size( 'atollmatrix-gridblock-full', 1400, '', true ); // Fullwidth.
		add_image_size( 'atollmatrix-gridblock-full-medium', 800, '', true ); // Medium.

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/**
		 * Add support for Gutenberg.
		 */
		add_theme_support( 'align-wide' );

		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__( 'Black', 'atollmatrix' ),
					'slug'  => 'black',
					'color' => '#000000',
				),
				array(
					'name'  => esc_html__( 'Gray', 'atollmatrix' ),
					'slug'  => 'gray',
					'color' => '#676767',
				),
				array(
					'name'  => esc_html__( 'Light gray', 'atollmatrix' ),
					'slug'  => 'light-gray',
					'color' => '#eeeeee',
				),
				array(
					'name'  => esc_html__( 'Dark gray', 'atollmatrix' ),
					'slug'  => 'dark-gray',
					'color' => '#333333',
				),
			)
		);

		if ( atollmatrix_get_option_data( 'rightclick_disable' ) ) {
			add_action( 'atollmatrix_contextmenu_msg', 'atollmatrix_contextmenu_msg_enable' );
		}

	}
}
add_action( 'after_setup_theme', 'atollmatrix_setup' );
/**
 * Gutenberg
 */
function atollmatrix_gutenberg_styles() {

	// Load the theme styles within Gutenberg.
	wp_enqueue_style( 'atollmatrix-gutenberg', get_theme_file_uri( '/css/gutenberg.css' ), false, '1.1.2', 'all' );

	// Add custom fonts to Gutenberg.
	wp_enqueue_style( 'atollmatrix-gutenberg-fonts', atollmatrix_fonts_url(), array(), '1.0.0' );
}
add_action( 'enqueue_block_editor_assets', 'atollmatrix_gutenberg_styles' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function atollmatrix_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'atollmatrix_content_width', 960 );
}
add_action( 'after_setup_theme', 'atollmatrix_content_width', 0 );

require_once get_template_directory() . '/includes/functions/big-arrays.php';
require_once get_template_directory() . '/includes/functions/theme-functions.php';
require_once get_template_directory() . '/includes/functions/class-atollmatrix-generateresponsiveset.php';
require_once get_template_directory() . '/includes/customize/register.php';
/**
 * Maximum sidebars
 */
function atollmatrix_get_max_sidebars() {
	$max_sidebars = 50;
	return $max_sidebars;
}
add_action( 'atollmatrix_display_portfolio_single_navigation', 'atollmatrix_display_portfolio_single_navigation_action' );
/**
 * Display portfolio navigation
 */
function atollmatrix_display_portfolio_single_navigation_action() {
	if ( is_singular( 'portfolio' ) || is_singular( 'events' ) ) {

		if ( is_singular( 'portfolio' ) ) {
			$mtheme_post_archive_link              = get_post_type_archive_link( 'portfolio' );
			$theme_options_mtheme_post_arhive_link = atollmatrix_get_option_data( 'portfolio_archive_page' );
			$portfolio_nav                         = atollmatrix_get_custom_post_nav();
		}
		if ( is_singular( 'events' ) ) {
			$mtheme_post_archive_link              = get_post_type_archive_link( 'events' );
			$theme_options_mtheme_post_arhive_link = atollmatrix_get_option_data( 'events_archive_page' );
			$portfolio_nav                         = atollmatrix_get_custom_post_nav( 'events' );
		}
		if ( '' !== $theme_options_mtheme_post_arhive_link && '0' !== $theme_options_mtheme_post_arhive_link && $theme_options_mtheme_post_arhive_link > 0 ) {
			$mtheme_post_archive_link = get_page_link( $theme_options_mtheme_post_arhive_link );
		}
		if ( isset( $portfolio_nav['prev'] ) ) {
			$previous_portfolio = $portfolio_nav['prev'];
		}
		if ( isset( $portfolio_nav['next'] ) ) {
			$next_portfolio = $portfolio_nav['next'];
		}
		?>

	<div class="portfolio-nav-wrap">
		<nav>
			<div class="portfolio-nav">
				<span class="portfolio-nav-item portfolio-prev">
				<?php
				if ( isset( $portfolio_nav['prev'] ) ) {
					?>
					<a href="<?php echo esc_url( get_permalink( $previous_portfolio ) ); ?>"><i class="ion-ios-arrow-thin-left"></i></a>
					<?php
				} else {
					?>
					<span><i class="ion-ios-arrow-thin-left"></i></span>
					<?php
				}
				?>
				</span>
				<span class="portfolio-nav-item portfolio-nav-archive">
					<a href="<?php echo esc_url( $mtheme_post_archive_link ); ?>"><i class="ion-ios-keypad-outline"></i></a>
				</span>
				<span class="portfolio-nav-item portfolio-next">
				<?php
				if ( isset( $portfolio_nav['next'] ) ) {
					?>
					<a href="<?php echo esc_url( get_permalink( $next_portfolio ) ); ?>"><i class="ion-ios-arrow-thin-right"></i></a>
					<?php
				} else {
					?>
					<span><i class="ion-ios-arrow-thin-right"></i></span>
					<?php
				}
				?>
				</span>
			</div>
		</nav>
	</div>

		<?php
	}
}

/**
 * Customizer styles.
 */
function theme_customize_style() {
	wp_enqueue_style( 'customize-styles', get_template_directory_uri() . '/includes/customize/css/customize-controls.css', array(), '1.0' );
}
add_action( 'customize_controls_enqueue_scripts', 'theme_customize_style' );

if ( is_admin() ) {
	/**
	 * Admin related scripts and styles
	 */
	function atollmatrix_admin_post_style_scripts() {
		if ( function_exists( 'get_current_screen' ) ) {
			$current_admin_screen = get_current_screen();
		}
		if ( isset( $current_admin_screen ) ) {
			if ( 'post' === $current_admin_screen->base ) {
				$post_gallery_ids = get_post_meta( get_the_ID(), '_mtheme_image_ids', true );
				wp_localize_script(
					'jquery',
					'themecore_admin_vars',
					array(
						'post_id'      => get_the_ID(),
						'post_gallery' => $post_gallery_ids,
						'nonce'        => wp_create_nonce( 'themecore-nonce-metagallery' ),
					)
				);
			}
		}
	}
	add_action( 'admin_enqueue_scripts', 'atollmatrix_admin_post_style_scripts' );
}

if ( ! function_exists( 'atollmatrix_fonts_url' ) ) {
	/**
	 * Load fonts
	 */
	function atollmatrix_fonts_url() {
		$font_url = '';

		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		*/

		if ( 'off' !== _x( 'on', 'Google font: on or off', 'atollmatrix' ) ) {

			$extra_fonts   = '';
			$font_prefixed = false;
			$font_prefix   = '';

			$featured_page = atollmatrix_get_active_fullscreen_post();
			$custom        = get_post_custom( $featured_page );
			if ( isset( $custom['pagemeta_fullscreentitlefont_meta'][0] ) ) {
				$fullscreentitlefont = $custom['pagemeta_fullscreentitlefont_meta'][0];
				if ( atollmatrix_permit_font( $fullscreentitlefont ) ) {
					if ( '0' !== $fullscreentitlefont ) {
						if ( $font_prefixed ) {
							$font_prefix = '|';
						}
						$extra_fonts  .= $font_prefix . $fullscreentitlefont;
						$font_prefixed = true;
					}
				}
			}
			if ( isset( $custom['pagemeta_fullscreendescfont_meta'][0] ) ) {
				$fullscreendescfont = $custom['pagemeta_fullscreendescfont_meta'][0];
				if ( atollmatrix_permit_font( $fullscreendescfont ) ) {
					if ( '0' !== $fullscreendescfont ) {
						if ( $font_prefixed ) {
							$font_prefix = '|';
						}
						$extra_fonts  .= $font_prefix . $fullscreendescfont;
						$font_prefixed = true;
					}
				}
			}
			if ( isset( $custom['pagemeta_fullscreenbuttonfont_meta'][0] ) ) {
				$fullscreenbuttonfont = $custom['pagemeta_fullscreenbuttonfont_meta'][0];
				if ( atollmatrix_permit_font( $fullscreenbuttonfont ) ) {
					if ( '0' !== $fullscreenbuttonfont ) {
						if ( $font_prefixed ) {
							$font_prefix = '|';
						}
						$extra_fonts  .= $font_prefix . $fullscreenbuttonfont;
						$font_prefixed = true;
					}
				}
			}

			$pagetitle_font                = atollmatrix_get_option_data( 'pagetitle_font' );
			$textlogo_font                 = atollmatrix_get_option_data( 'textlogo_font' );
			$textlogo_weight               = atollmatrix_get_option_data( 'textlogo_weight' );
			$responsivetextlogo_weight     = atollmatrix_get_option_data( 'responsivetextlogo_weight' );
			$tag_textlogo_font             = atollmatrix_get_option_data( 'tag_textlogo_font' );
			$tag_textlogo_weight           = atollmatrix_get_option_data( 'tag_textlogo_weight' );
			$tag_responsivetextlogo_weight = atollmatrix_get_option_data( 'tag_responsivetextlogo_weight' );
			$footertext_font               = atollmatrix_get_option_data( 'footertext_font' );
			$footerwidgettitle_font        = atollmatrix_get_option_data( 'footerwidgettitle_font' );
			$page_contents_font            = atollmatrix_get_option_data( 'page_contents_font' );
			$sidebar_headings_font         = atollmatrix_get_option_data( 'sidebar_headings_font' );
			$sidebar_text_font             = atollmatrix_get_option_data( 'sidebar_text_font' );
			$page_headings_font            = atollmatrix_get_option_data( 'page_headings_font' );
			$page_general_font             = atollmatrix_get_option_data( 'page_general_font' );
			$menutext_font                 = atollmatrix_get_option_data( 'menutext_font' );
			$vertical_menutext_font        = atollmatrix_get_option_data( 'vertical_menutext_font' );
			$vertical_footer_font          = atollmatrix_get_option_data( 'vertical_footer_font' );
			$responsivemenutext_font       = atollmatrix_get_option_data( 'responsivemenutext_font' );

			if ( atollmatrix_permit_font( $footertext_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $footertext_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $footerwidgettitle_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $footerwidgettitle_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $pagetitle_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $pagetitle_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $textlogo_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				if ( '' !== $textlogo_weight || '' !== $responsivetextlogo_weight ) {

					$textlogo_weights = '';
					$comma            = '';
					if ( '' !== $textlogo_weight && '' !== $responsivetextlogo_weight ) {
						$comma = ',';
					}
					if ( '' !== $textlogo_weight ) {
						$textlogo_weights .= $textlogo_weight;
					}
					if ( '' !== $responsivetextlogo_weight ) {
						$textlogo_weights .= $comma . $responsivetextlogo_weight;
					}
					$textlogo_font = $textlogo_font . ':' . $textlogo_weights;
				}
				$extra_fonts  .= $font_prefix . $textlogo_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $tag_textlogo_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				if ( '' !== $tag_textlogo_weight || '' !== $tag_responsivetextlogo_weight ) {

					$tag_textlogo_weights = '';
					$comma                = '';
					if ( '' !== $tag_textlogo_weight && '' !== $tag_responsivetextlogo_weight ) {
						$comma = ',';
					}
					if ( '' !== $tag_textlogo_weight ) {
						$tag_textlogo_weights .= $tag_textlogo_weight;
					}
					if ( '' !== $tag_responsivetextlogo_weight ) {
						$tag_textlogo_weights .= $comma . $tag_responsivetextlogo_weight;
					}
					$tag_textlogo_font = $tag_textlogo_font . ':' . $tag_textlogo_weights;
				}
				$extra_fonts  .= $font_prefix . $tag_textlogo_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $page_contents_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $page_contents_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $sidebar_headings_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $sidebar_headings_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $page_headings_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $page_headings_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $page_general_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $page_general_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $menutext_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $menutext_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $vertical_menutext_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $vertical_menutext_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $vertical_footer_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $vertical_footer_font;
				$font_prefixed = true;
			}
			if ( atollmatrix_permit_font( $responsivemenutext_font ) ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts  .= $font_prefix . $responsivemenutext_font;
				$font_prefixed = true;
			}
			$general_theme_style = atollmatrix_get_option_data( 'general_theme_style' );
			if ( 'display' === $general_theme_style ) {
				if ( $font_prefixed ) {
					$font_prefix = '|';
				}
				$extra_fonts .= $font_prefix . 'Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap';
			}
			if ( '' !== $extra_fonts ) {
				$font_url = add_query_arg( 'family', rawurlencode( $extra_fonts ), '//fonts.googleapis.com/css' );
			}
		}
		return $font_url;
	}
}
function atollmatrix_disable_proofing_archive() {
	if ( is_post_type_archive( 'proofing' ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
if ( atollmatrix_get_option_data( 'proofing_archive_disable' ) ) {
	add_action( 'wp', 'atollmatrix_disable_proofing_archive' );
}

/**
 * Resounce hints
 *
 * @param type $urls url of font.
 * @param type $relation_type relation.
 */
function atollmatrix_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'atollmatrix-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'atollmatrix_resource_hints', 10, 2 );
/**
 * Show password pages in Elementor editor
 *
 * @param type $password_status password status.
 * @param type $post post ID
 */
function atollmatrix_show_password_pages_in_elementor_editor( $password_status, $post ) {
	if ( is_singular() ) {
		if ( class_exists( '\Elementor\Plugin' ) ) {
			if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
				$password_status = false;
			}
		}
	}
	return $password_status;
}
add_filter( 'post_password_required', 'atollmatrix_show_password_pages_in_elementor_editor', 10, 2 );
/**
 * Theme functions and scripts
 */
function atollmatrix_function_scripts_styles() {
	wp_enqueue_style( 'atollmatrix-fonts', atollmatrix_fonts_url(), array(), '1.0.0' );
	$default_font_load   = atollmatrix_get_option_data( 'default_font_load' );
	$general_theme_style = atollmatrix_get_option_data( 'general_theme_style' );
	
	wp_enqueue_style( 'atollmatrix-MainStyle', get_stylesheet_uri(), 'false', '2.2' );
	wp_register_style( 'atollmatrix-content-style', get_template_directory_uri() . '/css/styles-content.css', array( 'atollmatrix-MainStyle' ), '2.5', 'screen' );
	wp_register_style( 'atollmatrix-display-style', get_template_directory_uri() . '/css/styles-display.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );
	wp_register_style( 'atollmatrix-compact-style', get_template_directory_uri() . '/css/styles-compact.css', array( 'atollmatrix-MainStyle' ), '1.1', 'screen' );
	wp_register_style( 'atollmatrix-black-style', get_template_directory_uri() . '/css/styles-black.css', array( 'atollmatrix-MainStyle' ), '1.1', 'screen' );
	wp_register_style( 'atollmatrix-ResponsiveCSS', get_template_directory_uri() . '/css/responsive.css', array( 'atollmatrix-MainStyle' ), '1.6', 'screen' );

	if ( ! atollmatrix_is_fullscreen_post() ) {
		wp_enqueue_style( 'atollmatrix-content-style' );
	}

	$general_theme_style = atollmatrix_get_option_data( 'general_theme_style' );
	if ( 'compact' === $general_theme_style ) {
		wp_enqueue_style( 'atollmatrix-compact-style' );
	}
	if ( 'display' === $general_theme_style ) {
		wp_enqueue_style( 'atollmatrix-display-style' );
	}
	$general_theme_mode = atollmatrix_get_option_data( 'general_theme_mode' );
	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$general_theme_mode = apply_filters( 'general_theme_mode', $general_theme_mode );
	}
	if ( 'dark' === $general_theme_mode ) {
		wp_enqueue_style( 'atollmatrix-black-style' );
	}
	wp_enqueue_style( 'atollmatrix-ResponsiveCSS' );


	// wp_enqueue_script( 'lazysizes', get_template_directory_uri() . '/js/lazysizes.min.js', array( 'jquery' ), '1.0', true );
	// wp_enqueue_script( 'ls-unveilhooks' );
	wp_enqueue_script( 'atollmatrix-verticalmenu', get_template_directory_uri() . '/js/menu/verticalmenu.js', array( 'jquery' ), '3.2', true );

	if ( is_ssl() ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}

	$googlemap_apikey = atollmatrix_get_option_data( 'googlemap_apikey' );
	if ( ! isset( $googlemap_apikey ) ) {
		$googlemap_apikey = '';
	}
	if ( '' !== $googlemap_apikey ) {
		wp_register_script( 'googlemaps-api', esc_url( $protocol . '://maps.google.com/maps/api/js?key=' . $googlemap_apikey ), array( 'jquery' ), '1.0', false );
	}

	wp_register_style( 'atollmatrix-elements', get_template_directory_uri() . '/css/elements.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );

	wp_enqueue_script( 'easing' );
	wp_enqueue_script( 'hoverIntent' );

	wp_enqueue_script( 'atollmatrix-common', get_template_directory_uri() . '/js/common.js', array( 'jquery' ), '4.1', true );

	// Theme Style.
	$theme_style = 'light';

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/fonts/font-awesome/css/font-awesome.min.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );
	wp_enqueue_style( 'ion-icons', get_template_directory_uri() . '/css/fonts/ionicons/css/ionicons.min.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );
	wp_enqueue_style( 'feather-webfonts', get_template_directory_uri() . '/css/fonts/feather-webfont/feather.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );

	wp_enqueue_style( 'fontawesome-theme' );

	if ( ! atollmatrix_is_fullscreen_post() ) {
		wp_enqueue_style( 'et-fonts', get_template_directory_uri() . '/css/fonts/et-fonts/et-fonts.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );
		wp_enqueue_style( 'simple-line-icons', get_template_directory_uri() . '/css/fonts/simple-line-icons/simple-line-icons.css', array( 'atollmatrix-MainStyle' ), '1.0', 'screen' );
	}

	if ( is_singular( 'portfolio' ) || is_singular( 'mtheme_gallery' ) ) {
		wp_enqueue_script( 'event-move' );
		wp_enqueue_script( 'twentytwenty' );
	}
	if ( is_singular( 'mtheme_gallery' ) ) {
		wp_enqueue_script( 'fotorama' );
		wp_enqueue_style( 'fotorama' );
	}

	if ( is_404() ) {
		wp_enqueue_script( 'isotope' );
	}
	if ( is_archive() ) {
		wp_enqueue_script( 'isotope' );
	}
	// Conditional Load jQueries.
	if ( atollmatrix_got_shortcode( 'tabs' ) || atollmatrix_got_shortcode( 'accordion' ) ) {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-accordion' );
	}

	if ( atollmatrix_got_shortcode( 'beforeafter' ) ) {
		wp_enqueue_script( 'event-move' );
		wp_enqueue_script( 'twentytwenty' );
	}

	if ( atollmatrix_got_shortcode( 'portfoliogrid' ) || is_page_template( 'template-eventgallery.php' ) || is_page_template( 'template-photostorygallery.php' ) || atollmatrix_got_shortcode( 'thumbnails' ) || is_post_type_archive() || is_tax() || is_singular( 'mtheme_gallery' ) || is_singular( 'proofing' ) ) {
		wp_enqueue_script( 'isotope' );
	}

	if ( atollmatrix_got_shortcode( 'count' ) ) {
		wp_enqueue_script( 'odometer' );
	}
	// Counter.
	if ( atollmatrix_got_shortcode( 'counter' ) ) {
		wp_enqueue_script( 'donutchart' );
	}
	// Caraousel.
	if ( atollmatrix_got_shortcode( 'workscarousel' ) ) {
		wp_enqueue_script( 'owlcarousel' );
		wp_enqueue_style( 'owlcarousel' );
	}
	if ( atollmatrix_got_shortcode( 'woocommerce_carousel_bestselling' ) ) {
		wp_enqueue_script( 'owlcarousel' );
		wp_enqueue_style( 'owlcarousel' );
	}
	if ( atollmatrix_got_shortcode( 'map' ) ) {
		wp_enqueue_script( 'googlemaps-api' );
	}

	if ( atollmatrix_got_shortcode( 'woocommerce_featured_slideshow' ) || atollmatrix_got_shortcode( 'blogcarousel' ) || atollmatrix_got_shortcode( 'slideshowcarousel' ) || atollmatrix_got_shortcode( 'recent_blog_slideshow' ) || atollmatrix_got_shortcode( 'recent_portfolio_slideshow' ) || atollmatrix_got_shortcode( 'portfoliogrid' ) || atollmatrix_got_shortcode( 'testimonials' ) ) {
		wp_enqueue_script( 'owlcarousel' );
		wp_enqueue_style( 'owlcarousel' );
	}

	if ( atollmatrix_got_shortcode( 'audioplayer' ) || atollmatrix_got_shortcode( 'bloglist' ) || atollmatrix_got_shortcode( 'blogtimeline' ) || atollmatrix_got_shortcode( 'recentblog' ) ) {
		wp_enqueue_script( 'jplayer' );
		wp_enqueue_style( 'jplayer' );
	}

	if ( atollmatrix_got_shortcode( 'carousel_group' ) ) {
		wp_enqueue_script( 'owlcarousel' );
		wp_enqueue_style( 'owlcarousel' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( atollmatrix_is_fullscreen_post() ) {

		$fullscreen_page_id = atollmatrix_get_active_fullscreen_post();

		// Slideshow CSS data set.
		$slideshow_css_data   = array();
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'title',
			'class'    => '.responsive-titles h3, .entry-content .responsive-titles h3, .slideshow_title, .entry-content .slideshow_title, .static_slideshow_title, .entry-content .static_slideshow_title, .coverphoto-text-container .slideshow_title, .entry-content .coverphoto-text-container .slideshow_title, .coverphoto-text-container .static_slideshow_title, .entry-content .coverphoto-text-container .static_slideshow_title',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_fullscreentitlecolor_meta',
					'parameter' => 'color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreentitlesize_meta',
					'parameter' => 'font-size',
					'unit'      => 'px',
				),
				array(
					'meta'      => 'pagemeta_fullscreentitleweight_meta',
					'parameter' => 'font-weight',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreentitlespacing_meta',
					'parameter' => 'letter-spacing',
					'unit'      => 'px',
				),
				array(
					'meta'      => 'pagemeta_fullscreentitlelineheight_meta',
					'parameter' => 'line-height',
					'unit'      => '',
				),
			),
		);
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'description',
			'class'    => '.responsive-titles .description, .entry-content .responsive-titles .description, .slideshow_caption, .entry-content .slideshow_caption, .static_slideshow_caption, .entry-content .static_slideshow_caption, .coverphoto-text-container .slideshow_caption, .entry-content .coverphoto-text-container .slideshow_caption, .coverphoto-text-container .static_slideshow_caption, .entry-content .coverphoto-text-container .static_slideshow_caption,.entry-content .coverphoto-text-container .static_slideshow_caption a,.entry-content .coverphoto-text-container .static_slideshow_caption a:hover',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_fullscreendesccolor_meta',
					'parameter' => 'color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreendescsize_meta',
					'parameter' => 'font-size',
					'unit'      => 'px',
				),
				array(
					'meta'      => 'pagemeta_fullscreendescweight_meta',
					'parameter' => 'font-weight',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreendescspacing_meta',
					'parameter' => 'letter-spacing',
					'unit'      => 'px',
				),
				array(
					'meta'      => 'pagemeta_fullscreendesclineheight_meta',
					'parameter' => 'line-height',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreendescwidth_meta',
					'parameter' => 'width',
					'unit'      => 'px',
				),
			),
		);
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'coverborder',
			'class'    => '.coverphoto-type-underline .slideshow_title,.coverphoto-type-border .coverphoto-text-container,.coverphoto-type-topbottom .coverphoto-text-container',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_coverphotobordercolor_meta',
					'parameter' => 'border-color',
					'unit'      => '',
				),
			),
		);
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'coverbackground',
			'class'    => '.coverphoto-type-fill .coverphoto-text-container',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_coverphotobordercolor_meta',
					'parameter' => 'background-color',
					'unit'      => '',
				),
			),
		);
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'button',
			'class'    => '.positionaware-button',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_fullscreenbuttoncolor_meta',
					'parameter' => 'color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttoncolor_meta',
					'parameter' => 'border-color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttonsize_meta',
					'parameter' => 'font-size',
					'unit'      => 'px',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttonweight_meta',
					'parameter' => 'font-weight',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttonspacing_meta',
					'parameter' => 'letter-spacing',
					'unit'      => 'px',
				),
			),
		);
		$slideshow_css_data[] = array(
			'id'       => $fullscreen_page_id,
			'for'      => 'button',
			'class'    => '.positionaware-button:hover',
			'generate' => array(
				array(
					'meta'      => 'pagemeta_fullscreenbuttonhovercolor_meta',
					'parameter' => 'color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttoncolor_meta',
					'parameter' => 'border-color',
					'unit'      => '',
				),
				array(
					'meta'      => 'pagemeta_fullscreenbuttoncolor_meta',
					'parameter' => 'background-color',
					'unit'      => '',
				),
			),
		);
		// Loop through each set.
		foreach ( $slideshow_css_data as $data ) {

			$id       = $data['id'];
			$class    = $data['class'];
			$generate = array();
			$generate = $data['generate'];

			// Loop through the meta data.
			foreach ( $generate as $metadata ) {

				$meta      = $metadata['meta'];
				$parameter = $metadata['parameter'];
				$unit      = $metadata['unit'];

				$css = new atollmatrix_GenerateResponsiveSet( $id, $meta, $class, $parameter, $unit );
				wp_add_inline_style( 'atollmatrix-ResponsiveCSS', $css->mediaset );

			}
		}
	}

	$page_font_content  = atollmatrix_get_css_classes( 'page_font_content' );
	$page_headings_font = atollmatrix_get_css_classes( 'page_headings_font' );
	$page_general_font  = atollmatrix_get_css_classes( 'page_general_font' );

	$gen_css = array(
		'disable_lightbox_fullscreen'          => array( '.mtheme-lightbox .lg-fullscreen', 'display', 'toggle' ),
		'disable_lightbox_sizetoggle'          => array( '.mtheme-lightbox #lg-actual-size', 'display', 'toggle' ),
		'disable_lightbox_download'            => array( '.mtheme-lightbox #lg-zoom-out,.lg-toolbar #lg-download', 'display', 'toggle' ),
		'disable_lightbox_zoomcontrols'        => array( '.mtheme-lightbox #lg-zoom-out,.lg-toolbar #lg-zoom-in', 'display', 'toggle' ),
		'disable_lightbox_autoplay'            => array( '.mtheme-lightbox .lg-autoplay-button', 'display', 'toggle' ),
		'disable_lightbox_count'               => array( '.mtheme-lightbox #lg-counter', 'display', 'toggle' ),
		'disable_lightbox_title'               => array( '.mtheme-lightbox .lg-sub-html,.mtheme-lightbox .lightbox-text-wrap', 'display', 'toggle' ),
		'disable_lightbox_onlytitle'           => array( '.mtheme-lightbox .lightbox-text-title', 'display', 'toggle' ),
		'postformat_no_comments'               => array( '.no-comments', 'display', 'toggle' ),
		'responsivemenu_background'            => array( '.minimal-menu-overlay,.minimal-logo-overlay,.responsive-menu-overlay', 'background', 'gradient' ),
		'pagemeta_fullscreentitlefont_meta'    => array( '.responsive-titles h3, .entry-content .responsive-titles h3, #slidecaption .slideshow_title, .slideshow-content-wrap .static_slideshow_title, .coverphoto-text-container .slideshow_title, .coverphoto-text-container .static_slideshow_title', 'font-family', 'meta_fullscreen_font' ),
		'pagemeta_fullscreendescfont_meta'     => array( '.responsive-titles h3, .entry-content .responsive-titles h3, .fullscreen-slideshow .slideshow_caption, .slideshow-content-wrap .static_slideshow_caption,.coverphoto-text-container .slideshow_caption', 'font-family', 'meta_fullscreen_font' ),
		'pagemeta_fullscreenbuttonfont_meta'   => array( '.positionaware-button', 'font-family', 'meta_fullscreen_font' ),
		'pagemeta_carouselbg_color'            => array( '.responsive-titles', 'background', 'backgroundrgba' ),
		'page_contents_font'                   => array( $page_font_content, 'font-family', 'font' ),
		'page_headings_font'                   => array( $page_headings_font, 'font-family', 'font' ),
		'sidebar_headings_font'                => array( '.sidebar h3,.sidebar .product-title, .sidebar .woocommerce ul.product_list_widget li a, #events_list .recentpost_info .recentpost_title, #recentposts_list .recentpost_info .recentpost_title, #popularposts_list .popularpost_info .popularpost_title', 'font-family', 'font' ),
		'sidebar_text_font'                    => array( '#recentposts_list .recentpost_info .recentpost_title, #popularposts_list .popularpost_info .popularpost_title,.sidebar a,.contact_address_block .about_info, .sidebar-widget #searchform input, .sidebar-widget #searchform i, #recentposts_list p, #popularposts_list p,.sidebar-widget ul#recentcomments li,.sidebar', 'font-family', 'font' ),
		'page_general_font'                    => array( $page_general_font, 'font-family', 'font' ),
		'footertext_font'                      => array( '.footer-container-column .sidebar-widget h3,.footer-container-column,.footer-container-column .sidebar-widget,.horizontal-footer-copyright,.footer-end-block', 'font-family', 'font' ),
		'footerwidgettitle_font'               => array( '.footer-container-column .sidebar-widget .footer-widget-block > h3', 'font-family', 'font' ),
		'footertext_size'                      => array( '.sidebar-widget .footer-widget-block,.horizontal-footer-copyright,.horizontal-footer-copyright a', 'font-size', 'dimension' ),
		'footertext_letterspacing'             => array( '.sidebar-widget .footer-widget-block,.horizontal-footer-copyright,.horizontal-footer-copyright a', 'letter-spacing', 'dimension' ),
		'footertext_weight'                    => array( '.sidebar-widget .footer-widget-block,.horizontal-footer-copyright,.horizontal-footer-copyright a', 'font-weight', 'dimension' ),
		'footerwidgettitle_size'               => array( '.footer-container-column .sidebar-widget .footer-widget-block > h3', 'font-size', 'dimension' ),
		'footerwidgettitle_letterspacing'      => array( '.footer-container-column .sidebar-widget .footer-widget-block > h3', 'letter-spacing', 'dimension' ),
		'footerwidgettitle_weight'             => array( '.footer-container-column .sidebar-widget .footer-widget-block > h3', 'font-weight', 'dimension' ),
		'pagetitle_font'                       => array( '.title-container-outer-wrap .entry-title,.entry-title-wrap .entry-title,.single .title-container .entry-title', 'font-family', 'font' ),
		'pagtitle_size'                        => array( '.title-container-outer-wrap .entry-title,.entry-title-wrap .entry-title,.single .title-container .entry-title', 'font-size', 'dimension' ),
		'textlogo_font'                        => array( '.mobile-site-title-section h1 a, .mobile-site-title-section h1,.minimal-logo .header-site-title-section h1.site-title, .splitmenu-logo .header-site-title-section h1.site-title, .split-menu .header-site-title-section h1.site-title, .left-logo .header-site-title-section h1.site-title, .minimal-logo .header-site-title-section h1.site-title a, .splitmenu-logo .header-site-title-section h1.site-title a, .split-menu .header-site-title-section h1.site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section h1.site-title a, .vertical-site-title-section h1.site-title,.mobile-site-title-section a, .mobile-site-title-section,.minimal-logo .header-site-title-section .site-title, .splitmenu-logo .header-site-title-section .site-title, .split-menu .header-site-title-section .site-title, .left-logo .header-site-title-section .site-title, .minimal-logo .header-site-title-section .site-title a, .splitmenu-logo .header-site-title-section .site-title a, .split-menu .header-site-title-section .site-title a, .left-logo .header-site-title-section .site-title a, .header-site-title-section a,.vertical-site-title-section .site-title a, .vertical-site-title-section .site-title', 'font-family', 'font' ),
		'textlogo_size'                        => array( '.minimal-logo .header-site-title-section h1.site-title, .splitmenu-logo .header-site-title-section h1.site-title, .split-menu .header-site-title-section h1.site-title, .left-logo .header-site-title-section h1.site-title, .minimal-logo .header-site-title-section h1.site-title a, .splitmenu-logo .header-site-title-section h1.site-title a, .split-menu .header-site-title-section h1.site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section h1.site-title a, .vertical-site-title-section h1.site-title,.minimal-logo .header-site-title-section .site-title, .splitmenu-logo .header-site-title-section .site-title, .split-menu .header-site-title-section .site-title, .left-logo .header-site-title-section .site-title, .minimal-logo .header-site-title-section .site-title a, .splitmenu-logo .header-site-title-section .site-title a, .split-menu .header-site-title-section .site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section .site-title a, .vertical-site-title-section .site-title,.left-logo .header-site-title-section .site-title a', 'font-size', 'dimension' ),
		'textlogo_letterspacing'               => array( '.minimal-logo .header-site-title-section h1.site-title, .splitmenu-logo .header-site-title-section h1.site-title, .split-menu .header-site-title-section h1.site-title, .left-logo .header-site-title-section h1.site-title, .minimal-logo .header-site-title-section h1.site-title a, .splitmenu-logo .header-site-title-section h1.site-title a, .split-menu .header-site-title-section h1.site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section h1.site-title a, .vertical-site-title-section h1.site-title,.minimal-logo .header-site-title-section .site-title, .splitmenu-logo .header-site-title-section .site-title, .split-menu .header-site-title-section .site-title, .left-logo .header-site-title-section .site-title, .minimal-logo .header-site-title-section .site-title a, .splitmenu-logo .header-site-title-section .site-title a, .split-menu .header-site-title-section .site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section .site-title a, .vertical-site-title-section .site-title,.left-logo .header-site-title-section .site-title a', 'letter-spacing', 'dimension' ),
		'textlogo_weight'                      => array( '.minimal-logo .header-site-title-section h1.site-title, .splitmenu-logo .header-site-title-section h1.site-title, .split-menu .header-site-title-section h1.site-title, .left-logo .header-site-title-section h1.site-title, .minimal-logo .header-site-title-section h1.site-title a, .splitmenu-logo .header-site-title-section h1.site-title a, .split-menu .header-site-title-section h1.site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section h1.site-title a, .vertical-site-title-section h1.site-title,.minimal-logo .header-site-title-section .site-title, .splitmenu-logo .header-site-title-section .site-title, .split-menu .header-site-title-section .site-title, .left-logo .header-site-title-section .site-title, .minimal-logo .header-site-title-section .site-title a, .splitmenu-logo .header-site-title-section .site-title a, .split-menu .header-site-title-section .site-title a, .left-logo .header-site-title-section h1.site-title a, .header-site-title-section a,.vertical-site-title-section .site-title a, .vertical-site-title-section .site-title,.left-logo .header-site-title-section .site-title a', 'font-weight', 'dimension' ),
		'responsivetextlogo_size'              => array( '.mobile-site-title-section h1 a, .mobile-site-title-section h1,.mobile-site-title-section a, .mobile-site-title-section', 'font-size', 'dimension' ),
		'responsivetextlogo_letterspacing'     => array( '.mobile-site-title-section h1 a, .mobile-site-title-section h1,.mobile-site-title-section a, .mobile-site-title-section', 'letter-spacing', 'dimension' ),
		'responsivetextlogo_weight'            => array( '.mobile-site-title-section h1 a, .mobile-site-title-section h1,.mobile-site-title-section a, .mobile-site-title-section', 'font-weight', 'dimension' ),
		'tag_textlogo_font'                    => array( '.header-site-title-section .site-description a,.vertical-site-title-section .site-description a, .vertical-site-title-section .site-description,.mobile-site-title-section .site-description a, .mobile-site-title-section .site-description', 'font-family', 'font' ),
		'tag_textlogo_size'                    => array( '.header-site-title-section .site-description a,.vertical-site-title-section .site-description a, .vertical-site-title-section .site-description', 'font-size', 'dimension' ),
		'tag_textlogo_letterspacing'           => array( '.header-site-title-section .site-description a,.vertical-site-title-section .site-description a, .vertical-site-title-section .site-description', 'letter-spacing', 'dimension' ),
		'tag_textlogo_weight'                  => array( '.header-site-title-section .site-description a,.vertical-site-title-section .site-description a, .vertical-site-title-section .site-description', 'font-weight', 'dimension' ),
		'tag_responsivetextlogo_size'          => array( '.mobile-site-title-section .site-description a, .mobile-site-title-section .site-description', 'font-size', 'dimension' ),
		'tag_responsivetextlogo_letterspacing' => array( '.mobile-site-title-section .site-description a, .mobile-site-title-section .site-description', 'letter-spacing', 'dimension' ),
		'tag_responsivetextlogo_weight'        => array( '.mobile-site-title-section .site-description a, .mobile-site-title-section .site-description', 'font-weight', 'dimension' ),
		'heading_one_size'                     => array( 'h1, .entry-content h1', 'font-size', 'dimension' ),
		'heading_two_size'                     => array( 'h2, .entry-content h2', 'font-size', 'dimension' ),
		'heading_three_size'                   => array( 'h3, .entry-content h3', 'font-size', 'dimension' ),
		'heading_four_size'                    => array( 'h4, .entry-content h4', 'font-size', 'dimension' ),
		'heading_five_size'                    => array( 'h5, .entry-content h5', 'font-size', 'dimension' ),
		'heading_six_size'                     => array( 'h6, .entry-content h6', 'font-size', 'dimension' ),
		'heading_one_letterspacing'            => array( 'h1, .entry-content h1', 'letter-spacing', 'dimension' ),
		'heading_two_letterspacing'            => array( 'h2, .entry-content h2', 'letter-spacing', 'dimension' ),
		'heading_three_letterspacing'          => array( 'h3, .entry-content h3', 'letter-spacing', 'dimension' ),
		'heading_four_letterspacing'           => array( 'h4, .entry-content h4', 'letter-spacing', 'dimension' ),
		'heading_five_letterspacing'           => array( 'h5, .entry-content h5', 'letter-spacing', 'dimension' ),
		'heading_six_letterspacing'            => array( 'h6, .entry-content h6', 'letter-spacing', 'dimension' ),
		'heading_one_weight'                   => array( 'h1, .entry-content h1', 'font-weight', 'dimension' ),
		'heading_two_weight'                   => array( 'h2, .entry-content h2', 'font-weight', 'dimension' ),
		'heading_three_weight'                 => array( 'h3, .entry-content h3', 'font-weight', 'dimension' ),
		'heading_four_weight'                  => array( 'h4, .entry-content h4', 'font-weight', 'dimension' ),
		'heading_five_weight'                  => array( 'h5, .entry-content h5', 'font-weight', 'dimension' ),
		'heading_six_weight'                   => array( 'h6, .entry-content h6', 'font-weight', 'dimension' ),
		'pagtitle_letterspacing'               => array( '.title-container-outer-wrap .entry-title,.entry-title-wrap .entry-title,.single .title-container .entry-title', 'letter-spacing', 'dimension' ),
		'pagtitle_weight'                      => array( '.title-container-outer-wrap .entry-title,.entry-title-wrap .entry-title,.single .title-container .entry-title', 'font-weight', 'dimension' ),
		'vertical_footer_font'                 => array( '.menu-is-vertical .vertical-footer-copyright', 'font-family', 'font' ),
		'vertical_menutext_font'               => array( '.vertical-menu,.vertical-menu ul.mtree > li > a,.vertical-menu ul.mtree a', 'font-family', 'font' ),
		'vertical_menutext_size'               => array( '.vertical-menu ul.mtree a', 'font-size', 'dimension' ),
		'vertical_menutext_letterspacing'      => array( '.vertical-menu ul.mtree a', 'letter-spacing', 'dimension' ),
		'vertical_menutext_weight'             => array( '.vertical-menu ul.mtree a', 'font-weight', 'dimension' ),
		'vertical_footertext_size'             => array( '.vertical-footer-copyright', 'font-size', 'dimension' ),
		'vertical_footertext_letterspacing'    => array( '.vertical-footer-copyright', 'letter-spacing', 'dimension' ),
		'vertical_footertext_weight'           => array( '.vertical-footer-copyright', 'font-weight', 'dimension' ),
		'menutext_font'                        => array( '.homemenu .sf-menu a,.homemenu .sf-menu,.homemenu .sf-menu .mega-item .children-depth-0 h6,.homemenu .sf-menu li.menu-item a', 'font-family', 'font' ),
		'menutext_size'                        => array( '.homemenu ul li a,.homemenu ul ul li a,.vertical-menu ul.mtree a, .simple-menu ul.mtree a, .responsive-mobile-menu ul.mtree a, .header-is-simple .responsive-mobile-menu ul.mtree a,.vertical-menu ul.mtree ul.sub-menu a', 'font-size', 'dimension' ),
		'menutextsub_size'                     => array( '.homemenu ul li ul li a,.vertical-menu ul.mtree ul a, .simple-menu ul.mtree ul a, .responsive-mobile-menu ul.mtree ul a, .header-is-simple .responsive-mobile-menu ul.mtree ul a,.vertical-menu ul.mtree ul.sub-menu ul a', 'font-size', 'dimension' ),
		'menutext_letterspacing'               => array( '.homemenu ul li a,.homemenu ul ul li a,.vertical-menu ul.mtree a, .simple-menu ul.mtree a, .responsive-mobile-menu ul.mtree a, .header-is-simple .responsive-mobile-menu ul.mtree a,.vertical-menu ul.mtree ul.sub-menu a', 'letter-spacing', 'dimension' ),
		'menutext_weight'                      => array( '.homemenu ul li a,.homemenu ul ul li a,.vertical-menu ul.mtree a, .simple-menu ul.mtree a, .responsive-mobile-menu ul.mtree a, .header-is-simple .responsive-mobile-menu ul.mtree a,.vertical-menu ul.mtree ul.sub-menu a', 'font-weight', 'dimension' ),
		'responsivemenutext_font'              => array( '.responsive-mobile-menu ul.mtree a,.responsive-mobile-menu ul.mtree,.theme-is-light .responsive-mobile-menu ul.mtree a', 'font-family', 'font' ),
		'responsivemenutext_size'              => array( '.responsive-mobile-menu ul.mtree a,.theme-is-light .responsive-mobile-menu ul.mtree a', 'font-size', 'dimension' ),
		'responsivemenutext_letterspacing'     => array( '.responsive-mobile-menu ul.mtree a,.theme-is-light .responsive-mobile-menu ul.mtree a', 'letter-spacing', 'dimension' ),
		'responsivemenutextsub_size'           => array( '.responsive-mobile-menu ul.mtree ul li a,.responsive-mobile-menu ul.mtree ul li a,.theme-is-light .responsive-mobile-menu ul.mtree ul li a', 'font-size', 'dimension' ),
		'responsivemenutext_weight'            => array( '.responsive-mobile-menu ul.mtree li a,.responsive-mobile-menu ul.mtree li a,.theme-is-light .responsive-mobile-menu ul.mtree li a', 'font-weight', 'dimension' ),
	);
	atollmatrix_gen_css( $gen_css );

	$proofing_is_protected = false;
	if ( atollmatrix_is_proofing_client_protected() ) {
		$proofing_is_protected = true;
	}

	if ( is_singular() && ! atollmatrix_is_fullscreen_post() ) {

		// Page is password protected.
		if ( post_password_required() || $proofing_is_protected ) {
			// get the featured Image.
			$image_link = atollmatrix_featured_image_link( get_the_id() );

			if ( is_singular( 'clients' ) ) {
				$image_link = get_post_meta( get_the_id(), 'pagemeta_client_background_image', true );
			}

			if ( '' !== $image_link ) {
				wp_add_inline_style( 'atollmatrix-ResponsiveCSS', '.site-back-cover { background-image: url(' . esc_url( $image_link ) . '); }' );
			}
		}
	}

	if ( is_singular() || atollmatrix_page_is_woo_shop() || atollmatrix_is_fullscreen_home() ) {

		// Set Opacity from Page.
		$got_page_id = get_the_id();
		if ( atollmatrix_is_fullscreen_home() ) {
			$got_page_id = atollmatrix_get_active_fullscreen_post();
		}

		$page_bg_color = get_post_meta( $got_page_id, 'pagemeta_pagebackground_color', true );

		if ( isset( $page_bg_color ) ) {
			if ( '' !== $page_bg_color ) {

				if ( atollmatrix_page_is_woo_shop() ) {
					$woo_shop_post_id = get_option( 'woocommerce_shop_page_id' );
					// Set Opacity from Page.
					$page_bg_color = get_post_meta( $woo_shop_post_id, 'pagemeta_pagebackground_color', true );

				}
				if ( atollmatrix_is_fullscreen_post() ) {
					if ( isset( $page_bg_color ) ) {
						$apply_background_color = 'body.page-is-fullscreen,#supersized li,.fotorama-style-contain .mtheme-fotorama,.fullscreen-horizontal-carousel { background:' . $page_bg_color . '; }';
						wp_add_inline_style( 'atollmatrix-ResponsiveCSS', $apply_background_color );
					}
				} else {
					if ( isset( $page_bg_color ) ) {
						// Page background color is set.
						if ( '' !== $page_bg_color ) {
							$apply_pagebackground_color = '.container-outer,.fullscreen-protected #password-protected { background: ' . $page_bg_color . '; }';
							wp_add_inline_style( 'atollmatrix-ResponsiveCSS', $apply_pagebackground_color );
						}
					}
				}
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'atollmatrix_function_scripts_styles' );
add_filter( 'redirect_canonical', 'atollmatrix_disable_redirect_canonical' );
/**
 * Canonical redirect
 *
 * @param type $redirect_url redirect url.
 */
function atollmatrix_disable_redirect_canonical( $redirect_url ) {
	if ( is_singular( 'portfoliogallery' ) ) {
		$redirect_url = false;
	}
	return $redirect_url;
}
function atollmatrix_prefix_register_elementor_locations( $elementor_theme_manager ) {
	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
}
add_action( 'elementor/theme/register_locations', 'atollmatrix_prefix_register_elementor_locations' );
add_filter( 'option_posts_per_page', 'atollmatrix_tax_filter_posts_per_page' );
/**
 * Texanomy
 *
 * @param string $value Page number.
 */
function atollmatrix_tax_filter_posts_per_page( $value ) {
	return ( is_tax( 'types' ) ) ? 1 : $value;
}
/**
 * Body classes
 *
 * @param string $classes Classes.
 */
function atollmatrix_body_class( $classes ) {

	if ( atollmatrix_menu_has_cart() ) {
		$classes[] = 'woo-cart-on';
	} else {
		$classes[] = 'woo-cart-off';
	}
	
	$classes[] = 'preloader-done';

	if ( wp_is_mobile() ) {
		$classes[] = 'parallax-is-off';
		$classes[] = 'mobile-detected';
		$classes[] = 'preloader-done';
	}

	if ( has_nav_menu( 'main_menu' ) ) {
		$classes[] = 'main-menu-active';
	} else {
		$classes[] = 'main-menu-inactive';
	}

	if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
		$classes[] = 'editor-is-active';
	}

	$disable_lightbox_title = atollmatrix_get_option_data( 'disable_lightbox_title' );
	if ( false === $disable_lightbox_title ) {
		$classes[] = 'lightbox-title-disabled';
	}
	$disable_lightbox_onlytitle = atollmatrix_get_option_data( 'disable_lightbox_onlytitle' );
	if ( false === $disable_lightbox_onlytitle ) {
		$classes[] = 'lightbox-title-only-disabled';
	}
	$general_theme_style = atollmatrix_get_option_data( 'general_theme_style' );
	if ( 'default' === $general_theme_style ) {
		$classes[] = 'general-theme-style-default';
	}
	if ( 'compact' === $general_theme_style ) {
		$classes[] = 'general-theme-style-compact';
	}
	$general_theme_mode = atollmatrix_get_option_data( 'general_theme_mode' );
	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$general_theme_mode = apply_filters( 'general_theme_mode', $general_theme_mode );
	}
	if ( 'default' === $general_theme_mode ) {
		$classes[] = 'general-theme-mod-default';
	}
	if ( 'dark' === $general_theme_mode ) {
		$classes[] = 'general-theme-mod-dark';
	}

	$lightbox_thumbnails_status = atollmatrix_get_option_data( 'lightbox_thumbnails_status' );
	if ( isset( $lightbox_thumbnails_status ) && 'enable' === $lightbox_thumbnails_status ) {
		$classes[] = 'lightbox-thumbnails-active';
	}

	$enable_gutenberg_lightbox = atollmatrix_get_option_data( 'enable_gutenberg_lightbox' );
	if ( isset( $enable_gutenberg_lightbox ) && true === $enable_gutenberg_lightbox ) {
		$classes[] = 'gutenberg-lightbox-enabled';
	} else {
		$classes[] = 'gutenberg-lightbox-disabled';
	}

	if ( is_tax( 'phototag' ) ) {
		$classes[] = 'edge-to-edge';
		$classes[] = 'searching-photostock';
	}

	$classes[] = 'fullscreen-mode-off';

	if ( atollmatrix_page_has_background() ) {
		$classes[] = 'page-has-full-background';
	}

	if ( is_active_sidebar( 'social_header' ) ) {
		$classes[] = 'menu-social-active';
	} else {
		$classes[] = 'menu-social-inactive';
	}

	if ( is_active_sidebar( 'footer_1' ) ) {
		$classes[] = 'footer-widgets-active';
	} else {
		$classes[] = 'footer-widgets-inactive';
	}
	if ( false === atollmatrix_get_option_data( 'theme_footer' ) ) {
		$classes[] = 'theme-footer-disabled';
	}
	if ( atollmatrix_get_option_data( 'rightclick_disable' ) ) {
		$classes[] = 'rightclick-block';
	}
	if ( atollmatrix_get_option_data( 'enable_animated_cursor' ) ) {
		$classes[] = 'animated-cursor-active';
	}
	if ( ! is_multi_author() ) {
		$classes[] = 'single-author';
	}

	if ( class_exists( 'woocommerce' ) ) {

		$cart_item_total = WC()->cart->get_cart_contents_count();
		if ( $cart_item_total > 0 ) {
			$classes[] = 'wooshop-is-not-empty';
		} else {
			$classes[] = 'wooshop-is-empty';
		}
		$hide_empty_cart = atollmatrix_get_option_data( 'hide_empty_cart' );
		if ( $hide_empty_cart ) {
			$classes[] = 'wooshop-hide-empty-cart';
		}
		if ( is_shop() ) {
			$shop_layout = false;
			$shop_layout = atollmatrix_get_option_data( 'shop_page_layout' );
			if ( 'fullwidth' !== $shop_layout ) {
				if ( is_active_sidebar( 'woocommerce_sidebar' ) ) {
					$classes[] = 'wooshop-has-sidebar-archive';
					$classes[] = 'wooshop-has-sidebar-' . $shop_layout;
				} else {
					$classes[] = 'wooshop-no-sidebar-archive';
				}
			} else {
				$classes[] = 'wooshop-no-sidebar-archive';
			}
		}
		if ( is_product_category() || is_product_tag() ) {
			$shop_archive_layout = false;
			$shop_archive_layout = atollmatrix_get_option_data( 'shop_archive_layout' );
			if ( 'fullwidth' !== $shop_archive_layout ) {
				if ( is_active_sidebar( 'woocommerce_sidebar' ) ) {
					$classes[] = 'wooshop-has-sidebar-archive';
					$classes[] = 'wooshop-has-sidebar-' . $shop_archive_layout;
				} else {
					$classes[] = 'wooshop-no-sidebar-archive';
				}
			} else {
				$classes[] = 'wooshop-no-sidebar-archive';
			}
		}
	}

	if ( ! is_archive() ) {
		if ( post_password_required() ) {
			$classes[] = 'mtheme-password-required';
		}
	}

	$skin_style = 'light';
	$classes[]  = 'theme-is-' . $skin_style;
	if ( atollmatrix_is_in_demo() ) {
		$classes[] = 'demo';
	}

	// Site Layout.
	$classes[] = 'default-layout';

	if ( is_front_page() && is_home() ) {
		$main_logo = atollmatrix_get_option_data( 'main_logo' );
		if ( isset( $main_logo ) ) {
			if ( '' === $main_logo ) {
				$classes[] = 'menu-has-site-title';
			}
		} else {
			$classes[] = 'menu-has-site-title';
		}
	}

	$enable_stickymenu = atollmatrix_get_option_data( 'enable_stickymenu' );

	if ( $enable_stickymenu ) {
		$classes[] = 'stickymenu-enabled-sitewide';
	}

	$header_menu_type = atollmatrix_get_option_data( 'menu_type' );

	if ( function_exists( 'theme_demo_feature_mode' ) ) {
		$header_menu_type = apply_filters( 'header_style', $header_menu_type );
	}

	switch ( $header_menu_type ) {
		case 'centered-logo':
			$classes[] = 'centered-logo';
			$classes[] = 'menu-is-horizontal';
			break;

		case 'left-logo':
			$classes[] = 'left-logo';
			$classes[] = 'menu-is-horizontal';
			break;

		case 'left-logo-boxed':
				$classes[] = 'left-logo';
				$classes[] = 'left-logo-boxed';
				$classes[] = 'menu-is-horizontal';
			break;

		case 'split-menu':
			$classes[] = 'split-menu';
			$classes[] = 'menu-is-horizontal';
			break;

		case 'vertical-menu':
			$classes[] = 'vertical-menu-default';
			$classes[] = 'menu-is-vertical';
			break;

		case 'toggle-main-menu':
			$classes[] = 'toggle-main-menu';
			$classes[] = 'left-logo';
			$classes[] = 'menu-is-horizontal';
			break;

		default:
			$classes[] = 'left-logo';
			$classes[] = 'menu-is-horizontal';
			break;
	}

	$page_data = get_post_custom( get_the_id() );

	if ( atollmatrix_is_fullscreen_post() ) {
		$classes[]             = 'page-is-fullscreen';
		$classes[]             = 'fullscreen-header-bright';
		$fullscreen_type_class = atollmatrix_get_fullscreen_type();
		if ( ! isset( $fullscreen_type_class ) || '' === $fullscreen_type_class ) {
			$fullscreen_type_class = 'unknown-type';
		} else {

			if ( 'slideshow' === $fullscreen_type_class || 'particles' === $fullscreen_type_class || 'coverphoto' === $fullscreen_type_class ) {
				$slideshow_custom = get_post_custom( atollmatrix_get_active_fullscreen_post() );
				if ( isset( $slideshow_custom['pagemeta_fullscreenslideshow_transition'][0] ) ) {
					$slideshow_transition = $slideshow_custom['pagemeta_fullscreenslideshow_transition'][0];
					if ( isset( $slideshow_transition ) ) {
						if ( 'wave' === $slideshow_transition ) {
							$classes[] = 'fullscreen-slideshow-transition-wave';
						}
						if ( 'fade' === $slideshow_transition ) {
							$classes[] = 'fullscreen-slideshow-transition-fade';
						}
						if ( 'zoom' === $slideshow_transition ) {
							$classes[] = 'fullscreen-slideshow-transition-zoom';
						}
					} else {
						$classes[] = 'fullscreen-slideshow-transition-wave';
					}
				}
			}

			if ( 'fotorama' === $fullscreen_type_class ) {
				$fotorama_custom = get_post_custom( atollmatrix_get_active_fullscreen_post() );
				if ( isset( $fotorama_custom['pagemeta_fotorama_fill'][0] ) ) {
					$fotorama_fill_mode = $fotorama_custom['pagemeta_fotorama_fill'][0];
					if ( isset( $fotorama_fill_mode ) ) {
						$classes[] = 'fotorama-style-' . $fotorama_fill_mode;
					}
				}
				if ( isset( $fotorama_custom['pagemeta_fotorama_thumbnails'][0] ) ) {
					$fotorama_thumbnails = $fotorama_custom['pagemeta_fotorama_thumbnails'][0];
					if ( isset( $fotorama_thumbnails ) ) {
						$classes[] = 'fotorama-thumbnails-' . $fotorama_thumbnails;
					}
				}
			}
			if ( 'video' === $fullscreen_type_class ) {
				$video_custom = get_post_custom( atollmatrix_get_active_fullscreen_post() );
				if ( isset( $video_custom['atollmatrix_youtubevideo'][0] ) ) {
					$classes[] = 'fullscreen-video-type-youtube';
				}
				if ( isset( $video_custom['atollmatrix_vimeovideo'][0] ) ) {
					$classes[] = 'fullscreen-video-type-vimeo';
				}
				if ( isset( $video_custom['atollmatrix_html5_mp4'][0] ) || isset( $video_custom['atollmatrix_html5_wemb'][0] ) ) {
					$classes[] = 'fullscreen-video-type-html5';
				}
			}
		}
		if ( is_singular( 'mtheme_photostory' ) ) {
			$fullscreen_type_class = 'fotorama';
			$fotorama_custom       = get_post_custom( get_the_id() );
			if ( isset( $fotorama_custom['pagemeta_fotorama_fill'][0] ) ) {
				$fotorama_fill_mode = $fotorama_custom['pagemeta_fotorama_fill'][0];
				if ( isset( $fotorama_fill_mode ) ) {
					$classes[] = 'fotorama-style-' . $fotorama_fill_mode;
				}
			}
		}
		if ( is_singular( 'mtheme_photostory' ) ) {
			$fullscreen_type_class = 'fotorama';
		}
		$classes[] = 'fullscreen-' . $fullscreen_type_class;

		$featured_page = atollmatrix_get_active_fullscreen_post();
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$_type         = get_post_type( $featured_page );
			$featured_page = icl_object_id( $featured_page, $_type, true, ICL_LANGUAGE_CODE );
		}
	} else {
		$classes[] = 'page-is-not-fullscreen';
	}

	if ( is_archive() ) {
		$classes[] = 'header-is-default';
	}

	$classes[] = 'fullscreen-ui-switchable';

	$hide_pagetitle = atollmatrix_get_option_data( 'hide_pagetitle' );
	if ( true === $hide_pagetitle ) {
		$classes[] = 'page-has-no-title-sitewide';
	}

	$header_is_set = false;

	if ( is_singular() || atollmatrix_is_fullscreen_home() ) {

		// Page is password protected.
		$page_is_password_protected = false;
		if ( post_password_required() ) {
			// get the featured Image.
			$page_is_password_protected = true;
			$image_link                 = atollmatrix_featured_image_link( get_the_id() );
			if ( '' !== $image_link ) {
				$classes[] = 'password-with-background';
			}

			if ( ! atollmatrix_header_is_compact() ) {
				$classes[]     = 'header-type-overlay';
				$header_is_set = true;
			}
		} else {

			$header_page_id = get_the_id();
			if ( atollmatrix_is_fullscreen_home() ) {
				$header_page_id = atollmatrix_get_active_fullscreen_post();
			}

			if ( atollmatrix_is_proofing_client_protected() ) {
				$header_page_id = get_post_meta( get_the_id(), 'pagemeta_client_names', true );
			}

			$header_page_data = get_post_custom( $header_page_id );
			if ( isset( $header_page_data['pagemeta_header_type'][0] ) ) {

				$classes[]     = atollmatrix_get_header_type_class( $header_page_id );
				$header_is_set = true;

			} else {
				if ( ! atollmatrix_header_is_compact() ) {
					if ( atollmatrix_is_proofing_client_protected() ) {
						if ( ! atollmatrix_elementor_in_preview() ) {
							$classes[] = 'header-type-overlay';
						}
					} else {
						$classes[] = 'header-type-auto';
					}
				}
				$header_is_set = true;
			}

			$page_opacity  = get_post_meta( get_the_id(), 'pagemeta_pagebackground_opacity', true );
			$page_bg_color = get_post_meta( get_the_id(), 'pagemeta_pagebackground_color', true );

			if ( isset( $page_opacity ) && 'default' !== $page_opacity && '100' !== $page_opacity && '' !== $page_opacity ) {
				$page_transparency_class = 'page-is-transparent';
			} else {
				$page_transparency_class = 'page-is-opaque';
			}

			$bg_choice = get_post_meta( get_the_id(), 'pagemeta_meta_background_choice', true );
			if ( isset( $bg_choice ) && 'none' === $bg_choice ) {
				$classes[] = 'page-media-not-set';
			}

			if ( isset( $page_transparency_class ) ) {
				$classes[] = $page_transparency_class;
			}

			$page_title = get_post_meta( get_the_id(), 'pagemeta_page_title', true );

			if ( is_singular( 'proofing' ) ) {
				$client_id       = get_post_meta( get_the_id(), 'pagemeta_client_names', true );
				$proofing_status = get_post_meta( get_the_id(), 'pagemeta_proofing_status', true );
				if ( isset( $client_id ) ) {
					if ( post_password_required( $client_id ) ) {
						$classes[] = 'password-protected-client-mode';
						if ( atollmatrix_elementor_in_preview() ) {
							$header_page_id = get_the_id();
							$classes[]      = atollmatrix_get_header_type_class( $header_page_id );
						}
					}
				}
				if ( isset( $proofing_status ) ) {
					$classes[] = 'proofing-status-' . $proofing_status;
				}
			}
			if ( class_exists( 'woocommerce' ) ) {
				if ( is_shop() || is_product_category() || is_product_tag() ) {
					$classes[]     = 'header-type-auto';
					$header_is_set = true;
				}
			}
			if ( is_singular( 'clients' ) ) {
				if ( post_password_required() ) {
					$classes[] = 'password-protected-client-mode';
					$classes[] = 'header-type-overlay';
				}
			}
			if ( isset( $page_title ) && 'hide' === $page_title ) {
				$classes[] = 'page-has-no-title';
			}
			if ( isset( $page_title ) && 'show' === $page_title ) {
				$classes[] = 'page-has-title';
			}
		}
	}

	if ( atollmatrix_header_is_compact() ) {
		$classes[] = 'header-type-auto';
	} else {
		if ( is_404() ) {
			$headertype_404 = atollmatrix_get_option_data( 'headertype_404' );
			$classes[]      = atollmatrix_set_header_class_from_type( $headertype_404 );
			$header_is_set  = true;
		}

		if ( is_archive() || is_search() ) {
			$general_archive_header = atollmatrix_get_option_data( 'general_archive_header' );
			$classes[]              = atollmatrix_set_header_class_from_type( $general_archive_header );
			$header_is_set          = true;
		}

		if ( ! $header_is_set ) {
			$classes[] = 'header-type-auto';
		}
	}

	$classes[] = 'theme-fullwidth';
	$classes[] = 'body-dashboard-push';

	$footerwidget_status = atollmatrix_get_option_data( 'footerwidget_status' );
	if ( $footerwidget_status ) {
		$classes[] = 'footer-is-on';
	} else {
		$classes[] = 'footer-is-off';
	}

	if ( is_singular() ) {

		if ( function_exists( 'the_gutenberg_project' ) ) {
			if ( has_blocks( get_the_id() ) ) {
				$classes[] = 'gutenberg-active';
			}
		}

		$pagestyle = atollmatrix_get_pagestyle( get_the_id() );
		if ( 'rightsidebar' === $pagestyle ) {
			$classes[] = 'rightsidebar';
			$classes[] = 'page-has-sidebar';
		}
		if ( 'leftsidebar' === $pagestyle ) {
			$classes[] = 'leftsidebar';
			$classes[] = 'page-has-sidebar';
		}
		if ( 'nosidebar' === $pagestyle ) {
			$classes[] = 'nosidebar';
		}
		if ( 'edge-to-edge' === $pagestyle ) {
			$classes[] = 'edge-to-edge';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'atollmatrix_body_class' );
/**
 * Page menu args
 *
 * @param string $args Arguments.
 */
function atollmatrix_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'atollmatrix_page_menu_args' );
/**
 * Expert Length
 *
 * @param string $length Length.
 */
function atollmatrix_excerpt_length( $length ) {
	return 80;
}
add_filter( 'excerpt_length', 'atollmatrix_excerpt_length' );
/**
 * Register Sidebars.
 */
function atollmatrix_widgets_init() {
	// Default Sidebar.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Default Sidebar', 'atollmatrix' ),
			'id'            => 'default_sidebar',
			'description'   => esc_html__( 'Default sidebar selected for pages, blog posts and archives.', 'atollmatrix' ),
			'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Menu Social', 'atollmatrix' ),
			'id'            => 'social_header',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);

	// Default Events Sidebar.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Default Events Sidebar', 'atollmatrix' ),
			'id'            => 'events_sidebar',
			'description'   => esc_html__( 'Default sidebar for events pages.', 'atollmatrix' ),
			'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
	// Default Portfolio Sidebar.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Default Portfolio Sidebar', 'atollmatrix' ),
			'id'            => 'portfolio_sidebar',
			'description'   => esc_html__( 'Default sidebar for portfolio pages.', 'atollmatrix' ),
			'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside></div>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
	if ( class_exists( 'woocommerce' ) ) {
		// Default WooCommerce Sidebar.
		register_sidebar(
			array(
				'name'          => esc_html__( 'Default WooCommerce Sidebar', 'atollmatrix' ),
				'id'            => 'woocommerce_sidebar',
				'description'   => esc_html__( 'Default sidebar for woocommerce pages.', 'atollmatrix' ),
				'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside></div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}

	// Dynamic Sidebar.
	for ( $sidebar_count = 1; $sidebar_count <= 50; $sidebar_count++ ) {
		if ( '' !== atollmatrix_get_option_data( 'mthemesidebar-' . $sidebar_count ) ) {
			register_sidebar(
				array(
					'name'          => esc_html( atollmatrix_get_option_data( 'mthemesidebar-' . $sidebar_count ) ),
					'id'            => 'mthemesidebar-' . esc_attr( $sidebar_count ),
					'before_widget' => '<div class="sidebar-widget"><aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside></div>',
					'before_title'  => '<h3>',
					'after_title'   => '</h3>',
				)
			);
		}
	}

	// Mobile Menu.
	register_sidebar(
		array(
			'name'          => esc_html__( 'Mobile Social', 'atollmatrix' ),
			'id'            => 'mobile_social_header',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);

	// Footer
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer', 'atollmatrix' ),
			'id'            => 'site_footer',
			'before_widget' => '<aside id="%1$s" class="footer-widget-block widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column One', 'atollmatrix' ),
			'id'            => 'footer_column_one',
			'before_widget' => '<aside id="%1$s" class="footer-widget-block widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column Two', 'atollmatrix' ),
			'id'            => 'footer_column_two',
			'before_widget' => '<aside id="%1$s" class="footer-widget-block widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);
	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer Column Three', 'atollmatrix' ),
			'id'            => 'footer_column_three',
			'before_widget' => '<aside id="%1$s" class="footer-widget-block widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		)
	);

}
add_action( 'widgets_init', 'atollmatrix_widgets_init' );
/**
 * Load core
 */
function atollmatrix_load_core_libaries() {
	require_once get_template_directory() . '/includes/admin/tgm/class-tgm-plugin-activation.php';
	require_once get_template_directory() . '/includes/admin/tgm/tgm-init.php';
}
atollmatrix_load_core_libaries();
/* Custom ajax loader */
add_filter( 'wpcf7_ajax_loader', 'atollmatrix_wpcf7_ajax_loader_icon' );
/**
 * Contact form 7 preloader define
 */
function atollmatrix_wpcf7_ajax_loader_icon() {
	return get_template_directory_uri() . '/images/preloader.png';
}
if ( function_exists( 'rev_slider_shortcode' ) ) {
	add_action( 'admin_init', 'atollmatrix_disable_revslider_notice' );
}
/**
 * Disable revslider notice.
 */
function atollmatrix_disable_revslider_notice() {
	update_option( 'revslider-valid-notice', 'false' );
}
$wpml_lang_selector_enable = atollmatrix_get_option_data( 'wpml_lang_selector_enable' );
if ( $wpml_lang_selector_enable && ! atollmatrix_menu_is_vertical() ) {
	add_filter( 'wp_nav_menu_items', 'atollmatrix_add_menu_wpml', 10, 2 );
}
if ( $wpml_lang_selector_enable ) {
	add_filter( 'atollmatrix_add_mobile_menu_wpml', 'atollmatrix_filter_add_mobile_menu_wpml' );
}
function atollmatrix_add_menu_wpml( $items, $args ) {
	if ( 'main_menu' === $args->theme_location ) {
		$wpml_lang_bar = atollmatrix_language_selector_flags( true, false );
		if ( $wpml_lang_bar ) {
			$items .= '<li>';
			$items .= '<div class="wpml-lang-selector-wrap">';
			$items .= '<div class="flags_language_selector">' . $wpml_lang_bar . '</div>';
			$items .= '</div>';
			$items .= '</li>';
		}
	}
	return $items;
}
function atollmatrix_filter_add_mobile_menu_wpml() {
	$wpml_lang_bar = atollmatrix_language_selector_flags( true, false );
	if ( $wpml_lang_bar ) {
		$items  = '<div class="wpml-lang-selector-wrap">';
		$items .= '<div class="flags_language_selector">' . $wpml_lang_bar . '</div>';
		$items .= '</div>';
		echo wp_kses( $items, atollmatrix_get_allowed_tags() );
	}
}
// WooCommerce Plugin is active.
if ( class_exists( 'woocommerce' ) ) {

	add_theme_support( 'woocommerce' );

	add_action( 'woocommerce_before_shop_loop_item_title', 'atollmatrix_woocommerce_template_loop_second_product_thumbnail', 11 );
	/**
	 * Woocommerce template loop for second thumbnail
	 */
	function atollmatrix_woocommerce_template_loop_second_product_thumbnail() {
		global $product, $woocommerce;

		$attachment_ids = $product->get_gallery_image_ids();

		if ( $attachment_ids ) {
			$secondary_image_id = $attachment_ids['0'];
			echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'mtheme-secondary-thumbnail-image attachment-shop-catalog woo-thumbnail-fadeOutUp' ) );
		}
	}

	if ( ! is_admin() ) {
		add_filter( 'post_class', 'atollmatrix_product_has_many_images' );
	}
	/**
	 * WooCommerce has many images
	 *
	 * @param string $classes Add class.
	 */
	function atollmatrix_product_has_many_images( $classes ) {
		global $product;

		$post_type = get_post_type( get_the_ID() );

		if ( 'product' === $post_type ) {

			$attachment_ids = $product->get_gallery_image_ids();
			if ( $attachment_ids ) {
				$secondary_image_id = $attachment_ids['0'];
				$classes[]          = 'mtheme-hover-thumbnail';
			}
		}

		return $classes;
	}
	/**
	 * WooCommerce sidebar checker
	 */
	function atollmatrix_woo_remove_sidebar_shop() {
		$shop_layout         = false;
		$shop_layout         = atollmatrix_get_option_data( 'shop_page_layout' );
		$shop_archive_layout = atollmatrix_get_option_data( 'shop_archive_layout' );

		if ( is_shop() && 'fullwidth' === $shop_layout ) {
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		}
		if ( is_product_category() && 'fullwidth' === $shop_archive_layout ) {
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		}
		if ( is_product_tag() && 'fullwidth' === $shop_archive_layout ) {
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		}
		if ( is_product() ) {
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		}
	}

	add_action( 'template_redirect', 'atollmatrix_woo_remove_sidebar_shop' );

	/**
	 * WooCommerce remove cart from archive
	 */
	function atollmatrix_remove_cart_button_from_products_archive() {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}
	/**
	 * Remove archive titles
	 */
	function atollmatrix_remove_archive_titles() {
		return false;
	}
	add_filter( 'woocommerce_show_page_title', 'atollmatrix_remove_archive_titles' );

	$disable_prettyphoto = true;
	if ( ! is_product() && class_exists( 'WC_Quick_View' ) ) {
		$disable_prettyphoto = false;
	}
	if ( $disable_prettyphoto ) {
		add_action( 'wp_enqueue_scripts', 'atollmatrix_remove_woocommerce_styles', 99 );
	}

	/**
	 * WooCommerce Lightbox
	 */
	function atollmatrix_remove_woocommerce_styles() {
		wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
		wp_dequeue_script( 'prettyPhoto-init' );
	}

	/**
	 * Filter loop per page for shop
	 *
	 * @param string $atollmatrix_loop_shop_per_page Page number.
	 */
	function atollmatrix_filter_loop_shop_per_page( $atollmatrix_loop_shop_per_page ) {
		$shop_product_count = atollmatrix_get_option_data( 'shop_product_count' );
		if ( isset( $shop_product_count ) && '' !== $shop_product_count ) {
			$atollmatrix_loop_shop_per_page = $shop_product_count;
		} else {
			$atollmatrix_loop_shop_per_page = 12;
		}
		return $atollmatrix_loop_shop_per_page;
	};

	add_filter( 'loop_shop_per_page', 'atollmatrix_filter_loop_shop_per_page', 10, 1 );

	// Change number or products per row to 3.
	add_filter( 'loop_shop_columns', 'atollmatrix_loop_columns' );
	if ( ! function_exists( 'atollmatrix_loop_columns' ) ) {
		/**
		 * Shop column number
		 */
		function atollmatrix_loop_columns() {
			$product_count = 3;
			return $product_count;
		}
	}

	/**
	 * Remove ratings from products thumbnail.
	 */
	function atollmatrix_remove_ratings_loop() {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
	if ( ! atollmatrix_get_option_data( 'woo_star_rating' ) ) {
		add_action( 'init', 'atollmatrix_remove_ratings_loop' );
	}

	/**
	* Add a custom link to the end of a specific menu that uses the wp_nav_menu() function
	*/
	if ( atollmatrix_menu_has_cart() ) {
		add_filter( 'wp_nav_menu_items', 'atollmatrix_add_menu_cart', 10, 2 );
		$header_menu_type = atollmatrix_get_option_data( 'menu_type' );
		if ( 'toggle-main-menu' === $header_menu_type ) {
			add_filter( 'atollmatrix_add_toggle_menu_cart', 'atollmatrix_filter_add_toggle_menu_cart' );
		}
		add_filter( 'atollmatrix_add_mobile_menu_cart', 'atollmatrix_filter_add_mobile_menu_cart' );
		add_action( 'atollmatrix_header_woocommerce_shopping_cart_counter', 'atollmatrix_woocommerce_item_count', 10, 2 );
		add_filter( 'woocommerce_add_to_cart_fragments', 'atollmatrix_woo_add_to_cart_fragment' );
	}
	function atollmatrix_add_menu_cart( $items, $args ) {
		if ( 'main_menu' === $args->theme_location ) {

			$cart_item_total = WC()->cart->get_cart_contents_count();
			$cart_class      = 'cart-is-not-empty';
			if ( 0 === $cart_item_total ) {
				$cart_class = 'cart-is-empty';
			}
			$items .= '<li class="woo-cart-menu-item"><div class="header-cart ' . esc_attr( $cart_class ) . ' header-cart-toggle"><i class="ion-bag"></i><span class="woocommerce-theme-menu-cart item-count">' . $cart_item_total . '</span></div></li>';
		}
		return $items;
	}
	function atollmatrix_filter_add_mobile_menu_cart() {
		$cart_item_total = WC()->cart->get_cart_contents_count();
		$cart_class      = 'cart-is-not-empty';
		if ( 0 === $cart_item_total ) {
			$cart_class = 'cart-is-empty';
		}
		$mobile_cart_toggle = '<div class="header-cart ' . esc_attr( $cart_class ) . ' header-cart-toggle"><i class="ion-bag"></i><span class="woocommerce-theme-menu-cart item-count">' . $cart_item_total . '</span></div>';
		echo wp_kses( $mobile_cart_toggle, atollmatrix_get_allowed_tags() );
	}
	function atollmatrix_filter_add_toggle_menu_cart() {
		$cart_item_total = WC()->cart->get_cart_contents_count();
		$cart_class      = 'cart-is-not-empty';
		if ( 0 === $cart_item_total ) {
			$cart_class = 'cart-is-empty';
		}
		$mobile_cart_toggle = '<div class="header-cart ' . esc_attr( $cart_class ) . ' header-cart-toggle"><i class="ion-bag"></i><span class="woocommerce-theme-menu-cart item-count">' . $cart_item_total . '</span></div>';
		echo wp_kses( $mobile_cart_toggle, atollmatrix_get_allowed_tags() );
	}

	/**
	 * Show cart contents / total Ajax
	 */
	add_filter( 'woocommerce_add_to_cart_fragments', 'atollmatrix_woocommerce_header_add_to_cart_fragment' );
	if ( ! function_exists( 'atollmatrix_woocommerce_header_add_to_cart_fragment' ) ) {
		function atollmatrix_woocommerce_header_add_to_cart_fragment( $fragments ) {
			$cart_item_total = WC()->cart->get_cart_contents_count();
			$cart_class      = 'cart-is-not-empty';
			if ( 0 === $cart_item_total ) {
				$cart_class = 'cart-is-empty';
			}
			ob_start();
			?>
			<div class="header-cart <?php echo esc_attr( $cart_class ); ?> header-cart-toggle"><i class="ion-bag"></i><span class="woocommerce-theme-menu-cart item-count"><?php echo esc_html( $cart_item_total ); ?></span></div>
			<?php
			$fragments['.header-cart.header-cart-toggle'] = ob_get_clean();
			return $fragments;
		}
	}

	if ( ! function_exists( 'atollmatrix_woocommerce_item_count' ) ) {
		function atollmatrix_woocommerce_item_count() {

			global $woocommerce;
			if ( isset( $woocommerce ) ) {
				if ( isset( $woocommerce->cart ) ) {
					$cart_class = 'cart-is-not-empty';
					if ( 0 === $woocommerce->cart->cart_contents_count ) {
						$cart_class = 'cart-is-empty';
					}
					?>
					<div class="mtheme-header-cart cart">
						<span class="header-cart header-cart-toggle"><i class="feather-icon-cross"></i></span>
						<?php
						if ( 0 === $woocommerce->cart->cart_contents_count ) {
							?>
							<div class="cart-contents">
									<div class="cart-empty">
									<?php
									$cart_is_empty_text = atollmatrix_get_option_data( 'cart_is_empty_text' );
									echo esc_html( $cart_is_empty_text );
									?>
									</div>		
							</div>		
							<?php
						} else {
							?>
							<div class="cart-contents">
								<?php
								foreach ( $woocommerce->cart->get_cart() as $cart_item ) :
									$allowedtags = array(
										'span'  => array(),
										'class' => array(),
									);
									?>
									<div class="cart-elements clearfix">
										<a href="<?php echo esc_url( get_permalink( $cart_item['product_id'] ) ); ?>">
										<div class="cart-element-image">
											<?php $thumbnail_id = ( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id']; ?>
											<?php echo get_the_post_thumbnail( $thumbnail_id, 'atollmatrix-gridblock-tiny' ); ?>
										</div>
										<div class="cart-content-text">
											<span class="cart-title"><?php echo get_the_title( $cart_item['product_id'] ); ?></span>
											<span class="cart-item-quantity-wrap"><span class="cart-item-quantity"><?php echo wp_kses( $cart_item['quantity'], $allowedtags ); ?> x </span><?php echo wp_kses( $woocommerce->cart->get_product_subtotal( $cart_item['data'], 1 ), $allowedtags ); ?></span>
										</div>
										</a>
									</div>
									<?php
								endforeach;
								?>
								<div class="cart-content-checkout">
									<?php
									$view_cart_button_text     = atollmatrix_get_option_data( 'view_cart_button_text' );
									$checkout_cart_button_text = atollmatrix_get_option_data( 'checkout_cart_button_text' );
									?>
									<div class="cart-view-link cart-buttons"><a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_cart_page_id' ) ) ); ?>"><?php echo esc_html( $view_cart_button_text ); ?></a></div>
									<div class="cart-checkout-link cart-buttons"><a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_checkout_page_id' ) ) ); ?>"><?php echo esc_html( $checkout_cart_button_text ); ?></a></div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
			}
		}
	}
	if ( ! function_exists( 'atollmatrix_woo_add_to_cart_fragment' ) ) {
		function atollmatrix_woo_add_to_cart_fragment( $fragments ) {
			global $woocommerce;

			$cart_class = 'cart-is-not-empty';
			if ( 0 === $woocommerce->cart->cart_contents_count ) {
				$cart_class = 'cart-is-empty';
			}
			ob_start();
			?>
			<div class="mtheme-header-cart <?php echo esc_attr( $cart_class ); ?> cart">
				<span class="header-cart-close"><i class="feather-icon-cross"></i></span>
				<h3>
				<?php
				$your_cart_text = atollmatrix_get_option_data( 'your_cart_text' );
				echo esc_html( $your_cart_text );
				?>
				</h3>
				<?php
				if ( 0 === $woocommerce->cart->cart_contents_count ) {
					?>
					<div class="cart-contents">
						<div class="cart-empty">
						<?php
						$cart_is_empty_text = atollmatrix_get_option_data( 'cart_is_empty_text' );
						echo esc_html( $cart_is_empty_text );
						?>
						</div>
					</div>		
					<?php
				}
				if ( $woocommerce->cart->cart_contents_count ) :
					?>
					<div class="cart-contents">
						<?php
						foreach ( $woocommerce->cart->cart_contents as $cart_item ) :
							$allowedtags = array(
								'span'  => array(),
								'class' => array(),
							);
							?>
							<div class="cart-elements clearfix">
								<a href="<?php echo esc_url( get_permalink( $cart_item['product_id'] ) ); ?>">
								<div class="cart-element-image">
									<?php $thumbnail_id = ( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id']; ?>
									<?php echo get_the_post_thumbnail( $thumbnail_id, 'atollmatrix-gridblock-tiny' ); ?>
								</div>
								<div class="cart-content-text">
									<span class="cart-title"><?php echo get_the_title( $cart_item['product_id'] ); ?></span>
									<span class="cart-item-quantity-wrap"><span class="cart-item-quantity"><?php echo wp_kses( $cart_item['quantity'], $allowedtags ); ?> x </span><?php echo wp_kses( $woocommerce->cart->get_product_subtotal( $cart_item['data'], 1 ), $allowedtags ); ?></span>
								</div>
								</a>
							</div>
							<?php
						endforeach;
						?>
						<div class="cart-content-checkout">
							<?php
							$view_cart_button_text     = atollmatrix_get_option_data( 'view_cart_button_text' );
							$checkout_cart_button_text = atollmatrix_get_option_data( 'checkout_cart_button_text' );
							?>
							<div class="cart-view-link cart-buttons"><a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_cart_page_id' ) ) ); ?>"><?php echo esc_html( $view_cart_button_text ); ?></a></div>
							<div class="cart-checkout-link cart-buttons"><a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_checkout_page_id' ) ) ); ?>"><?php echo esc_html( $checkout_cart_button_text ); ?></a></div>
						</div>
					</div>
					<?php
				endif;
				?>
			</div>
			<?php
			$header_cart           = ob_get_clean();
			$fragments['div.cart'] = $header_cart;

			return $fragments;
		}
	}
}
?>
