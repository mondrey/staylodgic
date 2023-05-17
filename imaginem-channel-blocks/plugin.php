<?php
namespace ImaginemBlocks;

use ImaginemBlocks\Widgets\Imaginem_Blocks;
use ImaginemBlocks\Widgets\Pricing_Services;
use ImaginemBlocks\Widgets\Pricing_Table;
use ImaginemBlocks\Widgets\ContactForm;
use ImaginemBlocks\Widgets\Portfolio_Grid;
use ImaginemBlocks\Widgets\Worktype_Albums;
use ImaginemBlocks\Widgets\Post_Heading;

use ImaginemBlocks\Widgets\Proofing_Grid;
use ImaginemBlocks\Widgets\Proofing_Archive;
use ImaginemBlocks\Widgets\Proofing_Client_Info;

use ImaginemBlocks\Widgets\Blog_List;
use ImaginemBlocks\Widgets\Blog_Grid;
use ImaginemBlocks\Widgets\Blog_Parallax;

use ImaginemBlocks\Widgets\Events_Info;
use ImaginemBlocks\Widgets\Events_Grid;
use ImaginemBlocks\Widgets\Events_Swiper;

use ImaginemBlocks\Widgets\Progress_Bar;
use ImaginemBlocks\Widgets\Testimonials;
use ImaginemBlocks\Widgets\Slideshow_Carousel;
use ImaginemBlocks\Widgets\Em_Image_Carousel;
use ImaginemBlocks\Widgets\Swiper_Slides;
use ImaginemBlocks\Widgets\Image_Drops;
use ImaginemBlocks\Widgets\BeforeAfter;
use ImaginemBlocks\Widgets\Em_FlipBox;
use ImaginemBlocks\Widgets\Google_Map;
use ImaginemBlocks\Widgets\Service_Box;
use ImaginemBlocks\Widgets\Split_Headlines;
use ImaginemBlocks\Widgets\Section_Heading;
use ImaginemBlocks\Widgets\Thumbnails_Grid;
use ImaginemBlocks\Widgets\Team_Member;
use ImaginemBlocks\Widgets\Inline_Editing;
use ImaginemBlocks\Widgets\Scroll_Indicator;
use ImaginemBlocks\Widgets\Share_Page;
use ImaginemBlocks\Widgets\Em_Works_Carousel;
use ImaginemBlocks\Widgets\Em_Vertical_Images;
use ImaginemBlocks\Widgets\Imaginem_Animated_Headings;
use ImaginemBlocks\Widgets\Imaginem_Info_Box;
use ImaginemBlocks\Widgets\Imaginem_Timeline;
use ImaginemBlocks\Widgets\Imaginem_Timeline_Contents;
use ImaginemBlocks\Widgets\Imaginem_EasyPieChart;
use ImaginemBlocks\Widgets\Imaginem_Fotorama;
use ImaginemBlocks\Widgets\Imaginem_Pulse_Play_Button;
use ImaginemBlocks\Widgets\Imaginem_Card_Slider;
use ImaginemBlocks\Widgets\Em_Image_Reel;
use ImaginemBlocks\Widgets\Imaginem_Button;
use ImaginemBlocks\Widgets\Imaginem_Countdown;

use ImaginemBlocks\Widgets\Em_Stories_Grid;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->add_actions();
		//$this->template_includes();
	}

	public function template_includes() {
		require_once( __DIR__ . '/templater/templates.php' );
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function add_actions() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

		// Register Widget Scripts
		add_action( 'elementor/editor/before_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/frontend/before_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );

		// Register for Editor Scripts
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'add_elementor_editor_scripts' ] );

		if ( class_exists('SitePress') ) {
			add_action( 'init', [ $this, 'add_wpml_support' ] );
		}
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require_once __DIR__ . '/widgets/flip-box.php';
		require_once __DIR__ . '/widgets/split-headlines.php';
		require_once __DIR__ . '/widgets/pricing-services.php';
		require_once __DIR__ . '/widgets/pricing-table.php';
		require_once __DIR__ . '/widgets/contactform.php';
		require_once __DIR__ . '/widgets/testimonials.php';
		require_once __DIR__ . '/widgets/google-map.php';
		require_once __DIR__ . '/widgets/progress-bar.php';
		require_once __DIR__ . '/widgets/section-heading.php';
		require_once __DIR__ . '/widgets/post-heading.php';
		require_once __DIR__ . '/widgets/portfolio-grid.php';
		require_once __DIR__ . '/widgets/worktype-albums.php';

		require_once __DIR__ . '/widgets/proofing-grid.php';
		require_once __DIR__ . '/widgets/proofing-archive.php';
		require_once __DIR__ . '/widgets/proofing-client-info.php';

		require_once __DIR__ . '/widgets/swiper-slides.php';
		require_once __DIR__ . '/widgets/blog-list.php';
		require_once __DIR__ . '/widgets/blog-grid.php';
		require_once __DIR__ . '/widgets/blog-parallax.php';

		require_once __DIR__ . '/widgets/events-info.php';
		require_once __DIR__ . '/widgets/events-grid.php';
		require_once __DIR__ . '/widgets/events-swiper.php';

		require_once __DIR__ . '/widgets/image-drops.php';
		require_once __DIR__ . '/widgets/service-box.php';
		require_once __DIR__ . '/widgets/slideshow-carousel.php';
		require_once __DIR__ . '/widgets/image-carousel.php';
		require_once __DIR__ . '/widgets/before-after.php';
		require_once __DIR__ . '/widgets/thumbnails-grid.php';
		require_once __DIR__ . '/widgets/team-member.php';
		require_once __DIR__ . '/widgets/scroll-indicator.php';
		require_once __DIR__ . '/widgets/share-page.php';
		require_once __DIR__ . '/widgets/vertical-images.php';
		require_once __DIR__ . '/widgets/animated-headings.php';
		require_once __DIR__ . '/widgets/info-box.php';
		require_once __DIR__ . '/widgets/timeline.php';
		require_once __DIR__ . '/widgets/timeline-contents.php';
		require_once __DIR__ . '/widgets/circular-counter.php';
		require_once __DIR__ . '/widgets/fotorama.php';
		require_once __DIR__ . '/widgets/pulse-play-button.php';
		require_once __DIR__ . '/widgets/card-slider.php';
		require_once __DIR__ . '/widgets/image-reel.php';
		require_once __DIR__ . '/widgets/button.php';
		require_once __DIR__ . '/widgets/countdown.php';
		require_once __DIR__ . '/widgets/stories-grid.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {
		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Section_Heading() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Button() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Split_Headlines() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Pricing_Services() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Pricing_Table() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new ContactForm() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Progress_Bar() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Slideshow_Carousel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Em_Image_Carousel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Swiper_Slides() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Service_Box() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Testimonials() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Image_Drops() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new BeforeAfter() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Google_Map() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Em_FlipBox() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Portfolio_Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Worktype_Albums() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Post_Heading() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Proofing_Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Proofing_Archive() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Proofing_Client_Info() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Blog_List() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Blog_Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Blog_Parallax() );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Events_Info() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Events_Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Events_Swiper() );
		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Thumbnails_Grid() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Team_Member() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Scroll_Indicator() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Share_Page() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Em_Vertical_Images() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Animated_Headings() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Info_Box() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Timeline() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Timeline_Contents() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_EasyPieChart() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Fotorama() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Pulse_Play_Button() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Card_Slider() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Em_Image_Reel() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Countdown() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Em_Stories_Grid() );


		//\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Inline_Editing() );
		//\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Imaginem_Blocks() );
	}

	private function widgets_with_items() {
		if ( class_exists('SitePress') ) {
			require( __DIR__ . '/wpml/wpml-translation-classes.php' );
		}
	}

	public function add_wpml_support() {
		$this->includes();
		$this->widgets_with_items();
		$section_widget = new Section_Heading();
		$section_widget->add_wpml_support();

		$splitheading_widget = new Split_Headlines();
		$splitheading_widget->add_wpml_support();

		$pricing_services_widget = new Pricing_Services();
		$pricing_services_widget->add_wpml_support();

		$pricing_table_widget = new Pricing_Table();
		$pricing_table_widget->add_wpml_support();

		$progress_bar_widget = new Progress_Bar();
		$progress_bar_widget->add_wpml_support();

		$service_box_widget = new Service_Box();
		$service_box_widget->add_wpml_support();

		$testimonial_widget = new Testimonials();
		$testimonial_widget->add_wpml_support();

		$flipbox_widget = new Em_FlipBox();
		$flipbox_widget->add_wpml_support();

		$worktype_widget = new Worktype_Albums();
		$worktype_widget->add_wpml_support();

		$thumbnails_widget = new Thumbnails_Grid();
		$thumbnails_widget->add_wpml_support();

		$portfolio_widget = new Portfolio_Grid();
		$portfolio_widget->add_wpml_support();

		$team_widget = new Team_Member();
		$team_widget->add_wpml_support();

		$share_widget = new Share_Page();
		$share_widget->add_wpml_support();

		$share_widget = new Imaginem_Button();
		$share_widget->add_wpml_support();

	}

	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'imaginem-elements',
			[
				'title' => __( 'iMaginem Elements', 'plugin-name' ),
				'icon' => 'fa fa-plug',
			]
		);
		$elements_manager->add_category(
			'imaginem-media',
			[
				'title' => __( 'iMaginem Media', 'plugin-name' ),
				'icon' => 'fa fa-plug',
			]
		);
		$elements_manager->add_category(
			'imaginem-portfolio',
			[
				'title' => __( 'iMaginem Portfolio', 'plugin-name' ),
				'icon' => 'fa fa-plug',
			]
		);
		$elements_manager->add_category(
			'imaginem-proofing',
			[
				'title' => __( 'iMaginem Proofing', 'plugin-name' ),
				'icon' => 'fa fa-plug',
			]
		);

	}



	public function add_elementor_editor_scripts() {

		$googlemap_api = get_theme_mod( 'googlemap_api' );
		if ( '' !== $googlemap_api ) {
			wp_enqueue_script( 'googlemap-key', 'https://maps.googleapis.com/maps/api/js?key=' . $googlemap_api );
			wp_enqueue_script( 'googlemap', plugins_url( '/assets/js/googlemap.js', Imaginem_Blocks__FILE__ ),[],'1.0');
		}
		wp_enqueue_style( 'ion-icons', plugins_url( '/assets/fonts/ionicons/css/ionicons.min.css', Imaginem_Blocks__FILE__ ),[],'1.0');
		wp_enqueue_style( 'simple-line-icons', plugins_url( '/assets/fonts/simple-line-icons/simple-line-icons.css', Imaginem_Blocks__FILE__ ),[],'1.0');
		wp_enqueue_style( 'et-fonts', plugins_url( '/assets/fonts/et-fonts/et-fonts.css', Imaginem_Blocks__FILE__ ),[],'1.0');

	}

	public function widget_scripts() {

		wp_enqueue_script( 'imaginem-blocks', plugins_url( '/assets/js/common-elementor.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], '1.2.5', true );
        wp_localize_script('imaginem-blocks', 'ajax_var', array(
            'url' => esc_url( admin_url('admin-ajax.php') ),
            'nonce' => wp_create_nonce('ajax-nonce')
        ));
		wp_register_script( 'odometer', plugins_url( '/assets/js/odometer.min.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );
		wp_register_script( 'easypiechart', plugins_url( '/assets/js/jquery.easypiechart.min.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );

		$googlemap_api = get_theme_mod( 'googlemap_api' );
		if ( $googlemap_api <>'' ) {
			wp_register_script( 'googlemap-key', 'https://maps.googleapis.com/maps/api/js?key=' . $googlemap_api );
			wp_register_script( 'googlemap-display', plugins_url( '/assets/js/googlemap-display.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );
			wp_localize_script( 'googlemap-display', 'themeCoreSettings', array( 'plugin_url' => plugin_dir_url(__FILE__) ));
		}

		wp_register_script( 'tilt', plugins_url( '/assets/js/tilt.jquery.js', Imaginem_Blocks__FILE__ ) );
		wp_register_script( 'isotope',  plugins_url( '/assets/js/isotope/jquery.isotope.min.js', Imaginem_Blocks__FILE__ ), [ 'jquery' , 'imagesloaded' , 'tilt' , 'imaginem-blocks' ], false, true );

		// Owl Carousel
		wp_register_style( 'owlcarousel', plugins_url( '/assets/js/owlcarousel/owl.carousel.css', Imaginem_Blocks__FILE__ ),[],'1.0');
		wp_register_script( 'owlcarousel', plugins_url( '/assets/js/owlcarousel/owl.carousel.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );

		// Before After
		wp_register_script( 'eventmove', plugins_url( '/assets/js/beforeafter/jquery.event.move.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );
		wp_register_script( 'twentytwenty', plugins_url( '/assets/js/beforeafter/jquery.twentytwenty.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );

		wp_register_script( 'countdown', plugins_url( '/assets/js/countdown/jquery.countdown.min.js', Imaginem_Blocks__FILE__ ), [ 'jquery' ], false, true );

	}


}

new Plugin();