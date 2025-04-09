<?php
namespace Staylodgic;

class Staylodgic_Init {

	public function __construct() {
		$this->staylodgic_actions();
		$this->staylodgic_load_custom_posts();
		$this->staylodgic_load_availablity_calendar();

		add_filter( 'upload_mimes', array( $this, 'allow_ics_upload' ) );
	}

	/**
	 * Add ICS file extension and mime type to the allowed list
	 *
	 * @param string $mime_types
	 *
	 * @return $mime_types
	 */
	public function allow_ics_upload( $mime_types ) {
		$mime_types['ics'] = 'text/calendar';
		return $mime_types;
	}

	private function staylodgic_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'staylodgic_load_front_end_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'staylodgic_load_admin_styles' ) );

		add_action( 'admin_init', array( $this, 'staylodgic_registryitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'staylodgic_reservationsitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'staylodgic_activityresitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'staylodgic_customersitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'staylodgic_roomitemmetabox_init' ) );
		add_action( 'admin_init', array( $this, 'staylodgic_activityitemmetabox_init' ) );

		add_action( 'init', array( $this, 'staylodgic_load_metaboxes' ) );
		add_action( 'init', array( $this, 'staylodgic_load_themeoptions' ) );

		add_action( 'after_setup_theme', array( $this, 'staylodgic_custom_image_size' ) );

		add_action( 'admin_menu', array( $this, 'remove_admin_notices_on_specific_page' ) );
	}

	/**
	 * List of specific admin pages to remove notices from
	 *
	 * @return void
	 */
	public function remove_admin_notices_on_specific_page() {
		$pages_to_remove_notices = array(
			'staylodgic-slg-availability',
			'staylodgic-slg-availability-yearly',
			'staylodgic-slg-dashboard',
			'staylodgic-invoicing',
			'staylodgic-activity-invoicing',
			'staylodgic-slg-activity-dashboard',
			'class-staylodgic-room-posts.php',
			'staylodgic-settings',
			'staylodgic-slg-settings-panel',
			'staylodgic-slg-export-booking-ical',
			'staylodgic-slg-export-registrations-ical',
			'staylodgic-slg-import-availability-ical',
			'staylodgic-slg-export-availability-ical',
		);

		$screen = get_current_screen(); // Get the current admin screen.

		if ( $screen && in_array( $screen->id, $pages_to_remove_notices, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
			add_filter( 'admin_footer_text', '__return_empty_string', 11 );
			add_filter( 'update_footer', '__return_empty_string', 11 );
		}
	}

	/**
	 * Register images size
	 *
	 * @return void
	 */
	public function staylodgic_custom_image_size() {
		add_image_size( 'staylodgic-large-square', 770, 770, true ); // Square.
	}

	/**
	 * Load Custom Post types
	 *
	 * @return void
	 */
	public function staylodgic_load_custom_posts() {
		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-reservation-posts.php';
		new \Staylodgic\Staylodgic_Reservation_Posts();

		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-customer-posts.php';
		new \Staylodgic\Staylodgic_Customer_Posts();

		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-registration-posts.php';
		new \Staylodgic\Staylodgic_Registration_Posts();

		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-room-posts.php';
		new \Staylodgic\Staylodgic_Room_Posts();

		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-activity-posts.php';
		new \Staylodgic\Staylodgic_Activity_Posts();

		require_once plugin_dir_path( __FILE__ ) . '/custom-posts/class-staylodgic-activity-reservation-posts.php';
		new \Staylodgic\Staylodgic_Activity_Reservation_Posts();
	}

	/**
	 * Load availability calendar
	 *
	 * @return void
	 */
	public function staylodgic_load_availablity_calendar() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-cron.php';
		new \Staylodgic\Cron();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin-property-data.php';
		require_once plugin_dir_path( __FILE__ ) . 'vendors/ics-parser/src/ICal/ICal.php';
		require_once plugin_dir_path( __FILE__ ) . 'vendors/ics-parser/src/ICal/Event.php';

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-analytics-bookings.php';
		$booking_id = false;
		new \Staylodgic\Analytics_Bookings( $booking_id );

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-activity.php';
		new \Staylodgic\Activity();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-analytics-activity.php';
		$activity_id = false;
		new \Staylodgic\Analytics_Activity( $activity_id );

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-welcome-screen.php';
		new \Staylodgic\Welcome_Screen();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-batch-processor-base.php';
		new \Staylodgic\Batch_Processor_Base();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-ical-export-processor.php';
		new \Staylodgic\Ical_Export_Processor();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-availability-batch-processor.php';
		new \Staylodgic\Availability_Batch_Processor();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-availablity-calendar-base.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-availablity-calendar.php';
		new \Staylodgic\Availablity_Calendar();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-availablity-calendar-year.php';
		new \Staylodgic\Availablity_Calendar_Year();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-cache.php';
		new \Staylodgic\Cache();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-rooms.php';
		new \Staylodgic\Rooms();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-rates.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-customers.php';

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-reservations.php';
		new \Staylodgic\Reservations();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-common.php';

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-data.php';
		new \Staylodgic\Data();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-modals.php';

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-booking.php';
		new \Staylodgic\Booking();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-email-dispatcher.php';

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-invoicing.php';
		new \Staylodgic\Invoicing();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-guest-registry.php';
		new \Staylodgic\Guest_Registry();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-form-generator.php';
		new \Staylodgic\Form_Generator();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-tax.php';
		new \Staylodgic\Tax();

		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/utilities.php';
	}

	/**
	 * Load Theme options panel
	 *
	 * @return void
	 */
	public function staylodgic_load_themeoptions() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-options-panel.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin/options-init.php';
	}


	/**
	 * Load metaboxes
	 *
	 * @return void
	 */
	public function staylodgic_load_metaboxes() {
		require_once plugin_dir_path( __FILE__ ) . '/includes/theme-gens.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxgen/metaboxgen.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/registry-metaboxes.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/reservation-metaboxes.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/activityres-metaboxes.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/customer-metaboxes.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/room-metaboxes.php';
		require_once plugin_dir_path( __FILE__ ) . '/metabox/metaboxes/activity-metaboxes.php';
	}

	/**
	 * Load Admin styles
	 *
	 * @return void
	 */
	public function staylodgic_load_admin_styles( $hook ) {
		wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . 'assets/js/select2/js/select2.full.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . 'assets/js/select2/css/select2.min.css', array(), false, 'screen' );

		wp_register_script( 'staylodgic-parser', plugin_dir_url( __FILE__ ) . 'admin/js/booking-parser.js', array( 'jquery' ), null, true );
		wp_register_script( 'html2canvas', plugin_dir_url( __FILE__ ) . 'assets/js/html2canvas.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'jsPDF', plugin_dir_url( __FILE__ ) . 'assets/js/jspdf/jspdf.umd.min.js', array( 'jquery' ), null, true );
		wp_register_script( 'staylodgic-invoice', plugin_dir_url( __FILE__ ) . 'admin/js/invoice.js', array( 'jquery', 'jsPDF', 'html2canvas' ), null, true );
		wp_register_style( 'staylodgic-invoice', plugin_dir_url( __FILE__ ) . 'admin/css/invoice.css', false, 'screen' );
		wp_register_style( 'staylodgic-dashboard', plugin_dir_url( __FILE__ ) . 'admin/css/dashboard.css', false, 'screen' );
		wp_register_style( 'flatpickr', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen' );
		wp_register_style( 'flatpickr-extra', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr-extra-style.css', array(), '1.0', 'screen' );
		wp_register_script( 'flatpickr', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'flatpickr-monthselect', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/monthselect.js', array( 'flatpickr' ), '1.0', true );
		wp_register_style( 'flatpickr-monthselect', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/monthselect.css', false, 'screen' );
		wp_register_script( 'staylodgic-admin-post-meta', plugin_dir_url( __FILE__ ) . 'admin/js/admin-post-meta.js', array( 'jquery', 'wp-api', 'wp-data' ), '1.0', true );
		wp_register_script( 'qrcodejs', plugin_dir_url( __FILE__ ) . 'assets/js/qrcode.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'staylodgic-admin-styles', plugin_dir_url( __FILE__ ) . 'admin/css/style.css', false, 'screen' );
		wp_register_style( 'staylodgic-indicator-icons', plugin_dir_url( __FILE__ ) . 'admin/css/indicator-icons.css', false, 'screen' );

		wp_register_style( 'staylodgic-availability-admin-styles', plugin_dir_url( __FILE__ ) . 'admin/css/admin-core-applies.css', false, 'screen' );
		wp_enqueue_style( 'staylodgic-admin-common', plugin_dir_url( __FILE__ ) . 'admin/css/admin-common.css', false, 'screen' );
		wp_enqueue_style( 'staylodgic-availability-styles', plugin_dir_url( __FILE__ ) . 'admin/css/availability-calendar.css', false, 'screen' );
		wp_enqueue_script( 'staylodgic-availability-scripts', plugin_dir_url( __FILE__ ) . 'admin/js/availability-calendar.js', array( 'jquery' ), null, true );

		wp_register_style( 'driver-js-css', plugin_dir_url( __FILE__ ) . 'admin/js/driverjs/driver.css', false, 'screen' );
		wp_register_script( 'driver-js-init', plugin_dir_url( __FILE__ ) . 'admin/js/driverjs/driver.js.iife.js', array( 'jquery' ), null, true );
		wp_register_script( 'staylodgic-welcome', plugin_dir_url( __FILE__ ) . 'admin/js/admin-welcome.js', array( 'jquery' ), null, true );

		wp_register_style( 'staylodgic-availability-yearly-styles', plugin_dir_url( __FILE__ ) . 'admin/css/availability-yearly-calendar.css', false, 'screen' );
		wp_register_script( 'staylodgic-availability-yearly-scripts', plugin_dir_url( __FILE__ ) . 'admin/js/availability-yearly-calendar.js', array( 'jquery' ), null, true );

		wp_enqueue_script( 'staylodgic-activity-scripts', plugin_dir_url( __FILE__ ) . 'admin/js/activity-calendar.js', array( 'jquery' ), null, true );

		wp_enqueue_script( 'staylodgic-common-scripts', plugin_dir_url( __FILE__ ) . 'admin/js/common.js', array( 'jquery' ), null, true );

		wp_register_style( 'fontawesome-6', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/fontawesome.css', false, 'screen' );
		wp_register_style( 'fontawesome-6-brands', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/all.css', false, 'screen' );
		wp_register_style( 'fontawesome-6-solid', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/solid.css', false, 'screen' );

		wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen' );

		wp_register_style( 'dataTables', plugin_dir_url( __FILE__ ) . 'admin/js/DataTables/datatables.min.css', false, 'screen' );
		wp_register_script( 'dataTables', plugin_dir_url( __FILE__ ) . 'admin/js/DataTables/datatables.min.js', array( 'jquery' ), null, true );

		wp_register_script( 'staylodgic-chartjs', plugin_dir_url( __FILE__ ) . 'admin/js/chart.js', array( 'jquery' ), null, true );
		wp_register_script( 'staylodgic-bookingchart', plugin_dir_url( __FILE__ ) . 'admin/js/booking-charts.js', array( 'staylodgic-chartjs', 'dataTables' ), null, true );

		if ( function_exists( 'get_current_screen' ) ) {
			$current_admin_screen = get_current_screen();
		}

		// Custom post type
		if ( 'staylodgic_customers' === $current_admin_screen->post_type ) {

			wp_enqueue_style( 'fontawesome-6' );
			wp_enqueue_style( 'fontawesome-6-brands' );
			wp_enqueue_style( 'fontawesome-6-solid' );

		}

		// Is an admin screen
		if ( isset( $current_admin_screen ) ) {

			// For all posts
			if ( 'post' === $current_admin_screen->base ) {
				wp_enqueue_media();

				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_script( 'select2' );
				wp_enqueue_style( 'select2' );
				wp_enqueue_style( 'flatpickr' );
				wp_enqueue_script( 'flatpickr' );
				wp_enqueue_style( 'flatpickr-extra' );
				wp_enqueue_script( 'staylodgic-admin-post-meta' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'qrcodejs' );

				wp_enqueue_script( 'jquery-ui-slider' );

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

			}

			// Invoicing pages
			if ( 'staylodgic_bookings_page_staylodgic-invoicing' === $current_admin_screen->base ) {
				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

				wp_enqueue_script( 'staylodgic-invoice' );
				wp_localize_script(
					'staylodgic-invoice',
					'staylodgicData',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ),
					)
				);

				wp_enqueue_style( 'staylodgic-invoice' );
			}

			// Staylodgic Dashboard
			if ( 'toplevel_page_staylodgic-slg-dashboard' === $current_admin_screen->base ) {

				wp_enqueue_style( 'staylodgic-dashboard' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_script( 'staylodgic-bookingchart' );
				wp_enqueue_style( 'dataTables-bootstrap5' );
				wp_enqueue_style( 'dataTables-bootstrap5-responsive' );
				wp_enqueue_script( 'staylodgic-dataTables' );

				wp_enqueue_script( 'staylodgic-dataTables-responsive' );
				wp_enqueue_script( 'staylodgic-dataTables-PDFmake' );
				wp_enqueue_script( 'staylodgic-dataTables-vsf-fonts' );
				wp_enqueue_script( 'staylodgic-dataTables-bootstrap-responsive' );
				wp_enqueue_script( 'staylodgic-chartjs' );

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );
			}

			// Activity pages
			if ( isset( $current_admin_screen->base ) && 'overview_page_staylodgic-slg-activity-dashboard' === $current_admin_screen->base ) {

				wp_enqueue_style( 'staylodgic-dashboard' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_script( 'staylodgic-bookingchart' );
				wp_enqueue_style( 'dataTables-bootstrap5' );
				wp_enqueue_style( 'dataTables-bootstrap5-responsive' );
				wp_enqueue_script( 'staylodgic-dataTables' );
				wp_enqueue_script( 'staylodgic-dataTables-responsive' );
				wp_enqueue_script( 'staylodgic-dataTables-PDFmake' );
				wp_enqueue_script( 'staylodgic-dataTables-vsf-fonts' );
				wp_enqueue_script( 'staylodgic-dataTables-bootstrap-responsive' );
				wp_enqueue_script( 'staylodgic-chartjs' );

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );
			}

			// Import Calendar
			if ( 'staylodgic_page_import-booking-ical' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Export Calendar
			if ( 'staylodgic_page_staylodgic-slg-export-booking-ical' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'flatpickr' );
				wp_enqueue_script( 'flatpickr' );
				wp_enqueue_script( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-extra' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Registrations
			if ( 'staylodgic_page_staylodgic-slg-export-registrations-ical' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'flatpickr' );
				wp_enqueue_script( 'flatpickr' );
				wp_enqueue_script( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-extra' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Import availability ical
			if ( 'staylodgic_page_staylodgic-slg-import-availability-ical' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Export availability
			if ( 'staylodgic_page_staylodgic-slg-export-availability-ical' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_script( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Import availability
			if ( 'staylodgic_page_import-availablity' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Activity invoice
			if ( 'staylodgic_actvtres_page_staylodgic-activity-invoicing' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-invoice' );
				wp_localize_script(
					'staylodgic-invoice',
					'staylodgicData',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ),
					)
				);

				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// General Invoicing
			if ( 'staylodgic_page_staylodgic-invoicing' === $current_admin_screen->base ) {

				wp_enqueue_script( 'staylodgic-invoice' );
				wp_localize_script(
					'staylodgic-invoice',
					'staylodgicData',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ),
					)
				);

				wp_enqueue_style( 'staylodgic-invoice' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

			}

			// Reservations
			if ( $current_admin_screen && 'edit' === $current_admin_screen->base && 'staylodgic_bookings' === $current_admin_screen->post_type ) {

				wp_enqueue_script( 'staylodgic-parser' );
				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

			}

			// Activity Reservations
			if ( $current_admin_screen && 'staylodgic_actvtres' === $current_admin_screen->post_type ) {

				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

				wp_enqueue_script( 'staylodgic-invoice' );
				wp_localize_script(
					'staylodgic-invoice',
					'staylodgicData',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ),
					)
				);

				wp_enqueue_style( 'staylodgic-invoice' );

			}

			// Guest registry
			if ( 'staylodgic_guestrgs' === $current_admin_screen->post_type && ( 'post.php' === $hook || 'post-new.php' === $hook ) ) {

				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

				wp_enqueue_script( 'staylodgic-invoice' );
				wp_localize_script(
					'staylodgic-invoice',
					'staylodgicData',
					array(
						'pluginUrl' => plugin_dir_url( __FILE__ ),
					)
				);

				wp_enqueue_style( 'staylodgic-invoice' );

			}
			wp_localize_script(
				'jquery',
				'staylodgic_admin_vars',
				array(
					'post_id' => get_the_ID(),
					'nonce'   => wp_create_nonce( 'staylodgic-nonce-admin' ),
				)
			);

			// Page settings
			if ( 'staylodgic_page_staylodgic-slg-settings-panel' === $current_admin_screen->base ) {

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_script( 'staylodgic-admin-options', plugin_dir_url( __FILE__ ) . 'admin/js/admin-options.js', array( 'jquery' ), null, true );
				wp_enqueue_style( 'staylodgic-admin-options', plugin_dir_url( __FILE__ ) . 'admin/css/admin-options.css', false, 'screen' );

				wp_enqueue_script( 'jquery-ui-sortable' );

				wp_enqueue_style( 'jquery-ui-sortable' );

				wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'assets/js/select2/js/select2.full.min.js', array( 'jquery' ), null, true );
				wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'assets/js/select2/css/select2.min.css', array(), false, 'screen' );
			}

			// Yearly Availability
			if ( isset( $current_admin_screen->base ) && 'overview_page_staylodgic-slg-availability-yearly' === $current_admin_screen->base ) {

				wp_enqueue_style( 'staylodgic-availability-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );
				wp_enqueue_style( 'staylodgic-availability-yearly-styles' );
				wp_enqueue_script( 'staylodgic-availability-yearly-scripts' );

				wp_enqueue_script( 'velocity', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.min.js', array( 'jquery' ), null, true );
				wp_enqueue_script( 'velocity-ui', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.ui.js', array( 'jquery' ), null, true );

				wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), null, true );
				wp_register_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen' );
				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_media();

				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_script( 'staylodgic-admin-post-meta' );

			}

			// Settings
			if ( isset( $current_admin_screen->base ) && 'toplevel_page_staylodgic-settings' === $current_admin_screen->base ) {

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_style( 'driver-js-css' );
				wp_enqueue_script( 'driver-js-init' );
				wp_enqueue_script( 'staylodgic-welcome' );

			}

			// Availability
			if ( isset( $current_admin_screen->base ) && 'overview_page_staylodgic-slg-availability' === $current_admin_screen->base ) {

				wp_enqueue_style( 'staylodgic-availability-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_style( 'staylodgic-availability-styles' );
				wp_enqueue_script( 'staylodgic-availability-scripts' );

				wp_enqueue_script( 'velocity', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.min.js', array( 'jquery' ), null, true );
				wp_enqueue_script( 'velocity-ui', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.ui.js', array( 'jquery' ), null, true );

				wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), null, true );
				wp_register_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen' );

				wp_enqueue_style( 'bootstrap' );
				wp_enqueue_script( 'bootstrap' );

				wp_enqueue_style( 'fontawesome-6' );
				wp_enqueue_style( 'fontawesome-6-brands' );
				wp_enqueue_style( 'fontawesome-6-solid' );

				wp_enqueue_media();

				wp_enqueue_style( 'staylodgic-admin-styles' );
				wp_enqueue_style( 'staylodgic-indicator-icons' );

				wp_enqueue_style( 'flatpickr' );
				wp_enqueue_script( 'flatpickr' );
				wp_enqueue_script( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-monthselect' );
				wp_enqueue_style( 'flatpickr-extra' );
				wp_enqueue_script( 'staylodgic-admin-post-meta' );

			}
		}

		// General responsive styles
		wp_enqueue_style( 'staylodgic-admin-responsive', plugin_dir_url( __FILE__ ) . 'admin/css/admin-responsive.css', false, 'screen' );
	}

	/**
	 * Load frontend scripts styles
	 *
	 * @return void
	 */
	public function staylodgic_load_front_end_scripts_styles() {

		wp_register_style( 'staylodgic-frontendstyle', plugin_dir_url( __FILE__ ) . 'admin/css/frontend-booking.css', array(), '1.0', 'screen' );
		wp_register_style( 'flatpickr', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen' );
		wp_register_style( 'flatpickr-extra', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr-extra-style.css', array(), '1.0', 'screen' );

		wp_register_script( 'flatpickr', plugin_dir_url( __FILE__ ) . 'assets/js/flatpickr/flatpickr.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'staylodgic-frontend-calendar', plugins_url( 'assets/js/frontend-calendar.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		wp_register_script( 'staylodgic-payment-helper', plugins_url( 'assets/js/payment-helper.js', __FILE__ ), array( 'jquery' ), '1.0', true );
		wp_register_style( 'staylodgic-indicator-icons', plugin_dir_url( __FILE__ ) . 'admin/css/indicator-icons.css', false, 'screen' );
		wp_localize_script(
			'staylodgic-frontend-calendar',
			'frontendAjax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'post_id' => get_the_ID(),
				'nonce'   => wp_create_nonce( 'staylodgic-nonce-search' ),
			)
		);

		wp_enqueue_script( 'velocity', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'velocity-ui', plugin_dir_url( __FILE__ ) . 'assets/js/velocity.ui.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'staylodgic-common-scripts', plugin_dir_url( __FILE__ ) . 'admin/js/common.js', array( 'jquery' ), null, true );

		wp_register_style( 'fontawesome-6', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/fontawesome.css', false, 'screen' );
		wp_register_style( 'fontawesome-6-brands', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/all.css', false, 'screen' );
		wp_register_style( 'fontawesome-6-solid', plugin_dir_url( __FILE__ ) . 'assets/fonts/fontawesome-free-6.7.2-web/css/solid.css', false, 'screen' );

		wp_enqueue_script( 'staylodgic-frontend-calendar', array( 'jquery' ), null, true );
		wp_enqueue_script( 'staylodgic-payment-helper' );
		wp_enqueue_style( 'staylodgic-frontendstyle' );
		wp_enqueue_style( 'staylodgic-indicator-icons' );
		wp_enqueue_style( 'flatpickr' );
		wp_enqueue_script( 'flatpickr' );
		wp_enqueue_style( 'flatpickr-extra' );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_style( 'fontawesome-6' );
		wp_enqueue_style( 'fontawesome-6-brands' );
		wp_enqueue_style( 'fontawesome-6-solid' );

		wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), null, true );
		wp_register_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen' );
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_script( 'bootstrap' );

		wp_enqueue_script( 'bs5-lightbox', plugin_dir_url( __FILE__ ) . 'assets/js/bs5-lightbox/index.bundle.min.js', array( 'bootstrap' ), null, true );

		// Check if we are viewing a single post/page
		if ( is_singular() ) {
			global $post;
			// Check if the post content contains the Contact Form 7 shortcode
			if ( has_shortcode( $post->post_content, 'staylodgic_form_input' ) || 'staylodgic_guestrgs' === get_post_type() ) {
				// Enqueue the Signature Pad script
				wp_enqueue_script( 'staylodgic-guest-registration', plugin_dir_url( __FILE__ ) . 'assets/js/guest-registration.js', array(), '1.0.0', true );
				wp_enqueue_script( 'signature-pad', plugin_dir_url( __FILE__ ) . 'assets/js/signature_pad.umd.min.js', array(), '1.0.0', true );
				// Enqueue any additional scripts required for the digital signature
				wp_enqueue_script( 'staylodgic-digital-signature', plugin_dir_url( __FILE__ ) . 'assets/js/digital-signature.js', array( 'signature-pad' ), '1.0.0', true );
				wp_enqueue_style( 'staylodgic-digital-signature', plugin_dir_url( __FILE__ ) . 'assets/css/digital-signature.css', false, 'screen' );
			}
		}

		wp_enqueue_style( 'staylodgic-admin-responsive', plugin_dir_url( __FILE__ ) . 'admin/css/frontend-responsive.css', false, 'screen' );
	}

	// Registry Metabox
	public function staylodgic_registryitemmetabox_init() {
		add_meta_box( 'staylodgic-registry-meta', esc_html__( 'Registry Options', 'staylodgic' ), 'staylodgic_registryitem_metaoptions', 'staylodgic_guestrgs', 'normal', 'low' );
		add_meta_box( 'staylodgic-registry-changelog', esc_html__( 'Registry Changelog', 'staylodgic' ), 'staylodgic_registryitem_changelog', 'staylodgic_guestrgs', 'normal', 'low' );
	}
	// Reservations Metabox
	public function staylodgic_reservationsitemmetabox_init() {
		add_meta_box( 'activityresInfo-meta', esc_html__( 'Activity Reservations Options', 'staylodgic' ), 'staylodgic_activityresitem_metaoptions', 'staylodgic_actvtres', 'normal', 'low' );
		add_meta_box( 'activityresInfo-changelog', esc_html__( 'Activity Reservations Changelog', 'staylodgic' ), 'staylodgic_activityresitem_changelog', 'staylodgic_actvtres', 'normal', 'low' );
	}
	// ActivityRes Metabox
	public function staylodgic_activityresitemmetabox_init() {
		add_meta_box( 'reservationsInfo-meta', esc_html__( 'Reservation Options', 'staylodgic' ), 'staylodgic_reservationsitem_metaoptions', 'staylodgic_bookings', 'normal', 'low' );
		add_meta_box( 'reservationsInfo-changelog', esc_html__( 'Reservation Changelog', 'staylodgic' ), 'staylodgic_reservationsitem_changelog', 'staylodgic_bookings', 'normal', 'low' );
	}
	// Customer Metabox
	public function staylodgic_customersitemmetabox_init() {
		add_meta_box( 'staylodgic-customers-meta', esc_html__( 'Customer Options', 'staylodgic' ), 'staylodgic_customersitem_metaoptions', 'staylodgic_customers', 'normal', 'low' );
	}
	// Room Metabox
	public function staylodgic_roomitemmetabox_init() {
		add_meta_box( 'staylodgic-room-meta', esc_html__( 'Room Options', 'staylodgic' ), 'staylodgic_roomitem_metaoptions', 'staylodgic_rooms', 'normal', 'low' );
		add_meta_box( 'staylodgic-room-changelog', esc_html__( 'Room Changelog', 'staylodgic' ), 'staylodgic_roomitem_changelog', 'staylodgic_rooms', 'normal', 'low' );
	}
	// Acitivity Metabox
	public function staylodgic_activityitemmetabox_init() {
		add_meta_box( 'staylodgic-activity-meta', esc_html__( 'Activity Options', 'staylodgic' ), 'staylodgic_activityitem_metaoptions', 'staylodgic_actvties', 'normal', 'low' );
		add_meta_box( 'staylodgic-activity-changelog', esc_html__( 'Activity Changelog', 'staylodgic' ), 'staylodgic_activityitem_changelog', 'staylodgic_actvties', 'normal', 'low' );
	}
}
