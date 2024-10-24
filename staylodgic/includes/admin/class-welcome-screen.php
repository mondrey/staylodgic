<?php

namespace Staylodgic;

class Welcome_Screen {



	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) ); // This now points to the add_admin_menu function
	}

	/**
	 * Method add_admin_menu
	 *
	 * @return void
	 */
	public function add_admin_menu() {

		add_menu_page(
			__( 'Staylodgic Admin', 'staylodgic' ),
			__( 'Staylodgic', 'staylodgic' ),
			'edit_posts',
			'staylodgic-settings',
			array( $this, 'display_main_page' ),
			'dashicons-admin-generic',
			31
		);

		add_submenu_page(
			'staylodgic-settings',
			__( 'Main', 'staylodgic' ),
			__( 'Main', 'staylodgic' ),
			'edit_posts',
			'staylodgic-settings',
			array( $this, 'display_main_page' )
		);
	}

	/**
	 * Method display_main_page
	 *
	 * @return void
	 */
	public function display_main_page() {
		// The HTML content of the 'Staylodgic' page goes here

		echo '<div class="admin-container">';
		echo '<div class="admin-column admin-column1">';
		echo '<div class="section-main">';
		echo '<div class="admin-page-header">';
		echo '<div class="logo-staylodgic"></div>';
		echo '</div>';
		echo '<ul class="admin-horizontal-list">';
		echo '<li><a href="' . admin_url() . '/post-new.php?post_type=slgc_reservations"><i class="fas fa-plus-square"></i> New Reservation</a></li>';
		echo '<li><a href="' . admin_url() . '/admin.php?page=slgc-dashboard"><i class="fas fa-chart-bar"></i> Booking Overview</a></li>';
		echo '<li><a href="' . admin_url() . '/admin.php?page=slgc-availability"><i class="fas fa-calendar-alt"></i> Availability Calendar</a></li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
		echo '<div class="admin-column admin-column2 admin-page-wrapper">';

		echo '<div class="section-features">';

		$current_user = wp_get_current_user();

		echo '<div class="welcome-user-icon"><i class="fa-solid fa-circle-user"></i></div>';
		echo '<h1>Welcome ' . esc_html( $current_user->user_login ) . '!</h1>';
		echo '<div class="welcome-text">';
		echo '<p class="main-greet">' . __( 'New to Staylodgic?', 'staylodgic' ) . '</p>';
		echo '<a class="view-help-guide" target="_blank" href="https://staylodgic.com/staylodgic-help-guide-viewer/">View Help Guide</a>';
		echo '</div>';
		echo '<div class="guided-tour-link-wrap">';
		echo '<div class="guided-tour-heading">Guided tours</div>';
		echo '<div id="start-bookings-button" class="guided-tour-link"><i class="fa-solid fa-arrow-right"></i> ' . __( 'How to accept bookings?', 'staylodgic' ) . '</div>';
		echo '<div id="start-activities-button" class="guided-tour-link"><i class="fa-solid fa-arrow-right"></i> ' . __( 'How to accept activities?', 'staylodgic' ) . '</div>';
		echo '<div id="start-registration-button" class="guided-tour-link guided-tour-last-link"><i class="fa-solid fa-arrow-right"></i> ' . __( 'How to create guest registration?', 'staylodgic' ) . '</div>';
		echo '</div>';
		echo '<div class="view-all-features">View all features</div>';
		echo '</div>';

		echo '</div>';
		echo '</div>';

		// Header
		echo '<div class="admin-page-two-main">';
		echo '<div class="admin-page-two-header">';
		echo '<div class="logo-staylodgic-alt"></div>';
		echo '</div>';

		echo '<div class="admin-page-two-wrapper">';
		echo '<div class="content-container">';
		echo '<div class="left-columns">'; // Container for both content columns

		echo '<div class="left-column">';
		echo '<h4><i class="fa-solid fa-gear"></i> ' . __( 'Hotel Settings', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li><a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Setup New Hotel', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-bed"></i> ' . __( 'Rooms for Reservation', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . admin_url() . '/post-new.php?post_type=slgc_room">' . __( 'Create Rooms', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . admin_url() . '/admin.php?page=slgc-availability">' . __( 'Add Room Rates', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . admin_url() . '/admin.php?page=slgc-availability">' . __( 'Add Room Quantity', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 4: <a href="' . admin_url() . '/post-new.php?post_type=slgc_reservations">' . __( 'Create Reservations', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-biking"></i> ' . __( 'Setup Activities', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . admin_url() . '/post-new.php?post_type=slgc_activity">' . __( 'Create Activities', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . admin_url() . '/post-new.php?post_type=slgc_activity">' . __( 'Add Scheduled Time to Week', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . admin_url() . '/post-new.php?post_type=slgc_activityres">' . __( 'Create Activity Reservations', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-tachometer-alt"></i> ' . __( 'Using Dashboard', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-dashboard">' . __( 'View Bookings Overview', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-activity-dashboard">' . __( 'View Activities Overview', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-availability">' . __( 'View Availability Calendar', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-availability-yearly">' . __( 'View Annual Availability', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '</div>'; // End of first left column

		echo '<div class="left-column">';
		echo '<h4><i class="fas fa-users"></i> ' . __( 'Customer Registry', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/post-new.php?post_type=slgc_customers">' . __( 'Create customers', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-user-check"></i> ' . __( 'Guest Registration', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . admin_url() . '/edit.php?post_type=slgc_guestregistry&page=slgc_guestregistry_shortcodes">' . __( 'Customize registration form', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . admin_url() . '/post-new.php?post_type=slgc_guestregistry">' . __( 'Create guest registration', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . admin_url() . '/post-new.php?post_type=slgc_guestregistry">' . __( 'Online registration', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 4: <a href="' . admin_url() . '/post-new.php?post_type=slgc_guestregistry">' . __( 'Send Links or QR Code', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-file-invoice-dollar"></i> ' . __( 'Invoicing', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/edit.php?post_type=slgc_reservations&page=staylodgic-invoicing">' . __( 'Bookings Invoice', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/edit.php?post_type=slgc_activityres&page=staylodgic-activity-invoicing">' . __( 'Activity Invoice', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-file-import"></i> ' . __( 'Import / Export', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-export-booking-ical">' . __( 'Export CSV Bookings', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-export-registrations-ical">' . __( 'Export Guests Registration for Month', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '</div>'; // End of second left column

		echo '<div class="left-column">';

		echo '<h4><i class="fas fa-hand-holding-usd"></i> ' . __( 'Taxes', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Fixed tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Percentage tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Per day tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Per person tax', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-utensils"></i> ' . __( 'Meal plans', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Create free plans', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Create paid plans', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-user-tag"></i> ' . __( 'Per Person Pricing', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Set fixed price increments', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Increment by percentage per occupant', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-percent"></i> ' . __( 'Discounts', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Last minute discount', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Early booking discount', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . admin_url() . '/admin.php?page=slgc-settings-panel">' . __( 'Long stay discount', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '</div>'; // End of second left column

		echo '</div>'; // End of left-columns container

		echo '<div class="right-column">';
		echo '<div class="svg-container">';
		echo '<!-- SVG or SVG CSS Background here -->';
		echo '</div>';
		echo '</div>'; // End of right column

		echo '</div>'; // End of content container
		echo '</div>'; // End of page wrapper
		echo '</div>'; // End of page wrapper
	}
}

// Instantiate the class
$welcome_instance = new \Staylodgic\Welcome_Screen();
