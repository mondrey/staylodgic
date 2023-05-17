<?php

/**
 * Demo Impoter
 *
 */

class Imaginemcore_Demo_Importer {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'demo_load_admin_styles') );
		add_filter( 'pt-ocdi/import_files', array( $this, 'import_files' ) );
		add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
		add_filter( 'pt-ocdi/plugin_intro_text', array( $this, 'ocdi_plugin_intro_text' ) );
		// actions
		add_action( 'pt-ocdi/before_content_import', array( $this, 'before_import_setup' ) );
		add_action( 'pt-ocdi/before_content_import_execution', array( $this, 'before_import_setup' ) );
		add_action( 'pt-ocdi/after_import', array( $this, 'after_import_setup' ) );
	}

    public function demo_load_admin_styles() {
        wp_enqueue_style('demo-importer-style', plugin_dir_url( __FILE__ ) .'assets/css/style.css', array(), false, 'screen' );
    }

	function ocdi_plugin_intro_text( $default_text ) {
	    $default_text .= '<div class="ocdi__intro-text"><p><h3>Welcome to our Superlens Demo Importer</h3>Let us know through our <strong><a target="_blank" href="https://imaginemthemes.co/">Support Forum</a></strong> if you have trouble importing the demo.</p><p>You can <strong><a href="https://imaginemthemes.co/wp-login.php?action=register">Register to support forum</a></strong> then create a <strong><a href="https://imaginemthemes.co/forums/forum/superlens-wordpress-theme/">New Support Thread here</a></strong></p><br/><br/><br/></div>';

	    return $default_text;
	}

	/**
	 * Import demo files
	 *
	 * @return array
	 */
	public function import_files() {
		return array(
			array(
				'import_file_name'           => 'Classic Demo',
				'categories'                 => array(),
				'local_import_file'          => plugin_dir_path( __FILE__ ) . 'demo-data/complete/superlens-full-demo.xml',
				'local_import_widget_file'   => plugin_dir_path( __FILE__ ) . 'demo-data/complete/superlens-widgets.json',
				'import_customizer_file_url' => plugins_url( 'demo-import/demo-data/complete/superlens-customizer.dat', dirname(__FILE__) ),
				'import_preview_image_url'   => plugins_url( 'demo-import/demo-data/complete/screenshot.jpg', dirname(__FILE__) ),
				'import_notice'              => __( 'This process will import demo <strong>with WooCommerce</strong> if the plugin is active.<br/><br/>If the demo import is not successful then it is likely due to server settings. Let us know through our <a style="box-shadow: none !important;" target="_blank" href="http://support.imaginemthemes.co/">Support Forum</a> for us to help.', 'themecore' ),
				'preview_url'                => '',
			),
			array(
				'import_file_name'           => 'Classic Demo',
				'categories'                 => array(),
				'local_import_file'          => plugin_dir_path( __FILE__ ) . 'demo-data/complete/superlens-full-demo.xml',
				'local_import_widget_file'   => plugin_dir_path( __FILE__ ) . 'demo-data/complete/superlens-widgets.json',
				'import_customizer_file_url' => plugins_url( 'demo-import/demo-data/complete/superlens-customizer.dat', dirname(__FILE__) ),
				'import_preview_image_url'   => plugins_url( 'demo-import/demo-data/complete/screenshot.jpg', dirname(__FILE__) ),
				'import_notice'              => __( 'This process will import demo <strong>with WooCommerce</strong> if the plugin is active.<br/><br/>If the demo import is not successful then it is likely due to server settings. Let us know through our <a style="box-shadow: none !important;" target="_blank" href="http://support.imaginemthemes.co/">Support Forum</a> for us to help.', 'themecore' ),
				'preview_url'                => '',
			),
		);
	}

	public function before_import_setup() {
		// Before Import Setup
	}

	public function after_import_setup( $selected_import ) {
		$this->assign_menus();
		if ( 'Onepage' === $selected_import['import_file_name'] ) {
			$this->assign_onepage();
			$this->assign_onepagemenus();
		} elseif ( 'Neptune ( Vertical Menu - Compact )' === $selected_import['import_file_name'] ) {
			$this->assign_neptunepage();
			$this->assign_neptunemenus();
		} elseif ( 'Ceres ( Toggle Menu )' === $selected_import['import_file_name'] ) {
			$this->assign_cerespage();
			$this->assign_ceresmenus();
		} elseif ( 'Ceres Fullscreen ( Toggle Menu )' === $selected_import['import_file_name'] ) {
			$this->assign_pages();
			$this->assign_ceresmenus();
		} else {
			$this->assign_pages();
			$this->assign_menus();
		}
		//$this->import_revsliders();
		$this->set_permalinks();
	}

	public function assign_menus() {
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menus     = wp_get_nav_menus();

		if($menus) {
			foreach($menus as $menu) {
				if( $menu->name == 'Main Menu' ) {
					$locations['main_menu'] = $menu->term_id;
					$locations['mobile_menu'] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	public function assign_ceresmenus() {
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menus     = wp_get_nav_menus();

		if($menus) {
			foreach($menus as $menu) {
				if( $menu->name == 'Ceres Menu' ) {
					$locations['main_menu'] = $menu->term_id;
					$locations['mobile_menu'] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	public function assign_neptunemenus() {
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menus     = wp_get_nav_menus();

		if($menus) {
			foreach($menus as $menu) {
				if( $menu->name == 'Venus Menu' ) {
					$locations['main_menu'] = $menu->term_id;
					$locations['mobile_menu'] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	public function assign_onepagemenus() {
		$locations = get_theme_mod( 'nav_menu_locations' );
		$menus     = wp_get_nav_menus();

		if($menus) {
			foreach($menus as $menu) {
				if( $menu->name == 'Onepage Menu' ) {
					$locations['main_menu'] = $menu->term_id;
					$locations['mobile_menu'] = $menu->term_id;
				}
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	public function assign_cerespage() {

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Ceres Home' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );

	}

	public function assign_neptunepage() {

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Works I' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );

	}

	public function assign_onepage() {

		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'OnePage' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );

	}

	public function assign_pages() {

		update_option( 'show_on_front', 'posts' );

	}

	public function import_revsliders() {

		// Import Revslider
		if( class_exists('UniteFunctionsRev') ) { // if revslider is activated
			$slider = new RevSlider();
			$filepath = plugin_dir_path( __FILE__ ) . 'demo-data/complete/fullscreenrev.zip';
			ob_start();
			$slider->importSliderFromPost(true, false, $filepath);
			ob_clean();
			ob_end_clean();

			$slider = new RevSlider();
			$filepath = plugin_dir_path( __FILE__ ) . 'demo-data/onepage/onepagehero.zip';
			ob_start();
			$slider->importSliderFromPost(true, false, $filepath);
			ob_clean();
			ob_end_clean();
		}

	}

	public function set_permalinks() {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		update_option( 'rewrite_rules', false );
		$wp_rewrite->flush_rules( true );
	}

}

function imaginemcore_demo_importer() {
	return imaginemcore_demo_importer::instance();
}
imaginemcore_demo_importer();
