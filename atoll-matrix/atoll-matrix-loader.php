<?php
class AtollMatrix_Init {
	public function __construct() {
		$this->atollmatrix_actions();
		$this->atollmatrix_load_custom_posts();
		$this->atollmatrix_load_availablity_calendar();
	}

	private function atollmatrix_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'atollmatrix_load_front_end_scripts_styles') );
		add_action( 'admin_enqueue_scripts', array( $this, 'atollmatrix_load_admin_styles') );

		add_action( 'admin_init', array( $this, 'atollmatrix_reservationsitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'atollmatrix_customersitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'atollmatrix_roomitemmetabox_init' ) );

		add_action( 'init', array( $this, 'atollmatrix_load_textdomain' ) );
		add_action( 'init', array( $this, 'atollmatrix_load_metaboxes' ) );
	}

	public function atollmatrix_load_textdomain() {
		load_plugin_textdomain( 'imaginem-blocks-ii', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
	}


	public function atollmatrix_load_custom_posts() {
		require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-reservation-posts.php');
		require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-customer-posts.php');
		require_once (plugin_dir_path( __FILE__ ) . '/custom-posts/class-imaginem-room-posts.php');
	}

	public function atollmatrix_load_availablity_calendar() {
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin-property-data.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin-demo-data.php');

		require_once (plugin_dir_path( __FILE__ ) . 'vendors/ics-parser/src/ICal/ICal.php');
		require_once (plugin_dir_path( __FILE__ ) . 'vendors/ics-parser/src/ICal/Event.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-batchprocessor.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-availablitycalendarbase.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-availablitycalendar.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-rooms.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-rates.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-customers.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-reservations.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-common.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-data.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-modals.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-payments.php');
		require_once (plugin_dir_path( __FILE__ ) . 'includes/admin/class-frontend.php');
	}

	public function atollmatrix_load_metaboxes() {
		require_once (plugin_dir_path( __FILE__ ) . '/includes/google-fonts.php');
		require_once (plugin_dir_path( __FILE__ ) . '/includes/theme-gens.php');
		require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxgen/metaboxgen.php');
		require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/reservation-metaboxes.php');
		require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/customer-metaboxes.php');
		require_once (plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/room-metaboxes.php');
	}

	public function atollmatrix_load_admin_styles() {
		// wp_register_style('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.css', array(), false, 'screen' );
		// wp_register_script('chosen', plugin_dir_url( __FILE__ ) .'assets/js/chosen/chosen.jquery.js', array( 'jquery' ),null, true );
		wp_register_script('select2', plugin_dir_url( __FILE__ ) .'assets/js/select2/js/select2.full.min.js', array( 'jquery' ),null, true );
		wp_register_style('select2', plugin_dir_url( __FILE__ ) .'assets/js/select2/css/select2.min.css', array(), false, 'screen' );

		wp_register_script('atollmatrix-parser', plugin_dir_url( __FILE__ ) .'admin/js/calendar-parser.js', array( 'jquery' ),null, true );
		
		wp_register_style('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen' );
		wp_register_script('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.js', array( 'jquery' ),'1.0', true );
		wp_register_script('admin-post-meta', plugin_dir_url( __FILE__ ) .'admin/js/admin-post-meta.js', array( 'jquery','wp-api','wp-data'),null, true );
		wp_register_script('menu-image-admin', plugin_dir_url( __FILE__ ) .'admin/js/menu-image-admin.js', array( 'jquery' ),null, true );
		wp_register_style('menu-image-css', plugin_dir_url( __FILE__ ) .'admin/js/menu-image-admin.css', array(), false, 'screen' );
		wp_register_style('atollmatrix-admin-styles', plugin_dir_url( __FILE__ ) .'admin/css/style.css',false, 'screen' );

		wp_enqueue_style( 'room-reservation-plugin-availability-styles', plugin_dir_url( __FILE__ ) .'admin/css/availability-calendar.css',false, 'screen' );
		wp_enqueue_script( 'room-reservation-plugin-availability-scripts', plugin_dir_url( __FILE__ ) .'admin/js/availability-calendar.js', array( 'jquery' ),null, true );

		if ( function_exists('get_current_screen') ) {
			$current_admin_screen = get_current_screen();
		}
		if (isSet($current_admin_screen)) {
			if ( $current_admin_screen->base == 'post') {
				wp_enqueue_media();

				wp_enqueue_style('atollmatrix-admin-styles');

				wp_enqueue_script('select2');
				wp_enqueue_style('select2');
				wp_enqueue_style('flatpickr');
				wp_enqueue_script('flatpickr');
				wp_enqueue_script('admin-post-meta');

				wp_enqueue_script( 'jquery-ui-slider' );
			}
			if ( $current_admin_screen->base == 'atoll-matrix_page_import-ical') {

				wp_enqueue_script('atollmatrix-parser');
				
			}
			wp_localize_script(
				'jquery',
				'atollmatrix_admin_vars',
				array(
					'post_id' => get_the_ID(),
					'nonce'   => wp_create_nonce( 'atollmatrix-nonce-metagallery' ),
				)
			);

			if ( $current_admin_screen->base == 'toplevel_page_room-availability' ) {
				
				wp_enqueue_script( 'velocity', plugin_dir_url( __FILE__ ) .'assets/js/velocity.min.js', array( 'jquery' ),null, true );
				wp_enqueue_script( 'velocity-ui', plugin_dir_url( __FILE__ ) .'assets/js/velocity.ui.js', array( 'jquery' ),null, true );

				wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) .'assets/js/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ),null, true );
				wp_register_style('bootstrap', plugin_dir_url( __FILE__ ) .'assets/js/bootstrap/css/bootstrap.min.css',false, 'screen' );
				wp_enqueue_style( 'bootstrap');
				wp_enqueue_script('bootstrap');

				wp_enqueue_style( 'fontawesome-6', plugin_dir_url( __FILE__ ) .'assets/fonts/fontawesome-free-6.4.0-web/css/fontawesome.css',false, 'screen' );
				wp_enqueue_style( 'fontawesome-6-brands', plugin_dir_url( __FILE__ ) .'assets/fonts/fontawesome-free-6.4.0-web/css/all.css',false, 'screen' );
				wp_enqueue_style( 'fontawesome-6-solid', plugin_dir_url( __FILE__ ) .'assets/fonts/fontawesome-free-6.4.0-web/css/solid.css',false, 'screen' );

				wp_enqueue_media();

				wp_enqueue_style('atollmatrix-admin-styles');

				wp_enqueue_script('chosen');
				wp_enqueue_style('chosen');
				wp_enqueue_style('flatpickr');
				wp_enqueue_script('flatpickr');
				wp_enqueue_script('admin-post-meta');
				
			}
		}
	}

	public function atollmatrix_load_front_end_scripts_styles() {

		wp_register_style('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen' );
		wp_register_script('flatpickr', plugin_dir_url( __FILE__ ) .'assets/js/flatpickr/flatpickr.js', array( 'jquery' ),'1.0', true );
		wp_register_script( 'frontend-calendar', plugins_url( 'assets/js/frontend-calendar.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		wp_register_script( 'payment-helper', plugins_url( 'assets/js/payment-helper.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		wp_localize_script( 'frontend-calendar', 'frontendAjax',
			array(
				'ajaxurl'  => admin_url('admin-ajax.php'),
				'post_id' => get_the_ID(),
				'nonce'   => wp_create_nonce( 'atollmatrix-nonce-search' ),
			)
		);
		
		wp_enqueue_script( 'frontend-calendar', array('jquery'), null, true );
		wp_enqueue_script('payment-helper');
		wp_enqueue_style('flatpickr');
		wp_enqueue_script('flatpickr');
		
	}

		// Events Metabox
		public function atollmatrix_reservationsitemmetabox_init(){
			add_meta_box('reservationsInfo-meta', esc_html__('Reservation Options','imaginem-blocks-ii'), 'atollmatrix_reservationsitem_metaoptions', 'reservations', 'normal', 'low');
		}
		// Events Metabox
		public function atollmatrix_customersitemmetabox_init(){
			add_meta_box('customersInfo-meta', esc_html__('Customer Options','imaginem-blocks-ii'), 'atollmatrix_customersitem_metaoptions', 'customers', 'normal', 'low');
		}
		// Portfolio Metabox
		public function atollmatrix_roomitemmetabox_init(){
			add_meta_box("room-meta", esc_html__("Room Options","imaginem-blocks"), "atollmatrix_roomitem_metaoptions", "room", "normal", "low");
		}

}

new AtollMatrix_Init();
