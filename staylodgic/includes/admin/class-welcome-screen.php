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
			esc_html__( 'Staylodgic Admin', 'staylodgic' ),
			esc_html__( 'Staylodgic', 'staylodgic' ),
			'edit_posts',
			'staylodgic-settings',
			array( $this, 'display_main_page' ),
			'dashicons-admin-generic',
			31
		);

		add_submenu_page(
			'staylodgic-settings',
			esc_html__( 'Main', 'staylodgic' ),
			esc_html__( 'Main', 'staylodgic' ),
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
		echo '<li><a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_bookings"><i class="fas fa-plus-square"></i> New Reservation</a></li>';
		echo '<li><a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-dashboard"><i class="fas fa-chart-bar"></i> Booking Overview</a></li>';
		echo '<li><a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-availability"><i class="fas fa-calendar-alt"></i> Availability Calendar</a></li>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
		echo '<div class="admin-column admin-column2 admin-page-wrapper">';

		echo '<div class="section-features">';

		$current_user = wp_get_current_user();

		echo '<div class="welcome-user-icon"><i class="fa-solid fa-circle-user"></i></div>';
		echo '<h1>Welcome ' . esc_html( $current_user->user_login ) . '!</h1>';
		echo '<div class="welcome-text">';
		echo '<p class="main-greet">' . esc_html__( 'New to Staylodgic?', 'staylodgic' ) . '</p>';
		echo '<a class="view-help-guide" target="_blank" href="https://staylodgic.com/staylodgic-help-guide-viewer/">View Help Guide</a>';
		echo '</div>';
		echo '<div class="guided-tour-link-wrap">';
		echo '<div class="guided-tour-heading">Guided tours</div>';
		echo '<div id="start-bookings-button" class="guided-tour-link"><i class="fa-solid fa-arrow-right"></i> ' . esc_html__( 'How to accept bookings?', 'staylodgic' ) . '</div>';
		echo '<div id="start-activities-button" class="guided-tour-link"><i class="fa-solid fa-arrow-right"></i> ' . esc_html__( 'How to accept activities?', 'staylodgic' ) . '</div>';
		echo '<div id="start-registration-button" class="guided-tour-link guided-tour-last-link"><i class="fa-solid fa-arrow-right"></i> ' . esc_html__( 'How to create guest registration?', 'staylodgic' ) . '</div>';
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
		echo '<h4><i class="fa-solid fa-gear"></i> ' . esc_html__( 'Hotel Settings', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li><a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Setup New Hotel', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-bed"></i> ' . esc_html__( 'Rooms for Reservation', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_rooms">' . esc_html__( 'Create Rooms', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-availability">' . esc_html__( 'Add Room Rates', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-availability">' . esc_html__( 'Add Room Quantity', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 4: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_bookings">' . esc_html__( 'Create Reservations', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-biking"></i> ' . esc_html__( 'Setup Activities', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_actvties">' . esc_html__( 'Create Activities', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_actvties">' . esc_html__( 'Add Scheduled Time to Week', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_actvtres">' . esc_html__( 'Create Activity Reservations', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-tachometer-alt"></i> ' . esc_html__( 'Using Dashboard', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-dashboard">' . esc_html__( 'View Bookings Overview', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-activity-dashboard">' . esc_html__( 'View Activities Overview', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-availability">' . esc_html__( 'View Availability Calendar', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-availability-yearly">' . esc_html__( 'View Annual Availability', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '</div>'; // End of first left column

		echo '<div class="left-column">';
		echo '<h4><i class="fas fa-users"></i> ' . esc_html__( 'Customer Registry', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_customers">' . esc_html__( 'Create customers', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-user-check"></i> ' . esc_html__( 'Guest Registration', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>Step 1: <a href="' . esc_url( admin_url() ) . '/edit.php?post_type=staylodgic_guestrgs&page=staylodgic_guestrgs_shortcodes">' . esc_html__( 'Customize registration form', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 2: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_guestrgs">' . esc_html__( 'Create guest registration', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 3: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_guestrgs">' . esc_html__( 'Online registration', 'staylodgic' ) . '</a></li>';
		echo '<li>Step 4: <a href="' . esc_url( admin_url() ) . '/post-new.php?post_type=staylodgic_guestrgs">' . esc_html__( 'Send Links or QR Code', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-file-invoice-dollar"></i> ' . esc_html__( 'Invoicing', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/edit.php?post_type=staylodgic_bookings&page=staylodgic-invoicing">' . esc_html__( 'Bookings Invoice', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/edit.php?post_type=staylodgic_actvtres&page=staylodgic-activity-invoicing">' . esc_html__( 'Activity Invoice', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-file-import"></i> ' . esc_html__( 'Import / Export', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-export-booking-ical">' . esc_html__( 'Export CSV Bookings', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-export-registrations-ical">' . esc_html__( 'Export Guests Registration for Month', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '</div>'; // End of second left column

		echo '<div class="left-column">';

		echo '<h4><i class="fas fa-hand-holding-usd"></i> ' . esc_html__( 'Taxes', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Fixed tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Percentage tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Per day tax', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Per person tax', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-utensils"></i> ' . esc_html__( 'Meal plans', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Create free plans', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Create paid plans', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-user-tag"></i> ' . esc_html__( 'Per Person Pricing', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Set fixed price increments', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Increment by percentage per occupant', 'staylodgic' ) . '</a></li>';
		echo '</ul>';

		echo '<h4><i class="fas fa-percent"></i> ' . esc_html__( 'Discounts', 'staylodgic' ) . '</h4>';
		echo '<ul>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Last minute discount', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Early booking discount', 'staylodgic' ) . '</a></li>';
		echo '<li>- <a href="' . esc_url( admin_url() ) . '/admin.php?page=staylodgic-slg-settings-panel">' . esc_html__( 'Long stay discount', 'staylodgic' ) . '</a></li>';
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
