<?php

namespace Staylodgic;

class Invoicing {


	private $stay_reservation_id;
	private $booking_status;
	private $stay_booking_number;
	private $number_days;
	private $hotel_logo;
	private $hotel_name;
	private $hotel_header;
	private $hotel_address;
	private $hotel_phone;
	private $stay_customer_name;
	private $customer_email;
	private $got_checkin_date;
	private $got_checkout_date;
	private $stay_room_type;
	private $stay_number_of_guests;
	private $stay_number_of_adults;
	private $stay_number_of_children;
	private $stay_room_price;
	private $sub_total;
	private $taxes_and_fees;
	private $total_amount;
	private $hotel_footer;

	public function __construct(
		$stay_reservation_id = null,
		$booking_status = null,
		$stay_booking_number = null,
		$number_days = null,
		$hotel_logo = null,
		$hotel_name = null,
		$hotel_header = null,
		$hotel_address = null,
		$hotel_phone = null,
		$stay_customer_name = null,
		$customer_email = null,
		$got_checkin_date = null,
		$got_checkout_date = null,
		$stay_room_type = null,
		$stay_number_of_guests = null,
		$stay_number_of_adults = null,
		$stay_number_of_children = null,
		$stay_room_price = null,
		$sub_total = null,
		$taxes_and_fees = null,
		$total_amount = null,
		$hotel_footer = null
	) {
		$this->stay_reservation_id     = $stay_reservation_id;
		$this->booking_status          = $booking_status;
		$this->stay_booking_number     = $stay_booking_number;
		$this->number_days             = $number_days;
		$this->hotel_logo              = $hotel_logo;
		$this->hotel_name              = $hotel_name;
		$this->hotel_header            = $hotel_header;
		$this->hotel_address           = $hotel_address;
		$this->hotel_phone             = $hotel_phone;
		$this->stay_customer_name      = $stay_customer_name;
		$this->customer_email          = $customer_email;
		$this->got_checkin_date        = $got_checkin_date;
		$this->got_checkout_date       = $got_checkout_date;
		$this->stay_room_type          = $stay_room_type;
		$this->stay_number_of_guests   = $stay_number_of_guests;
		$this->stay_number_of_adults   = $stay_number_of_adults;
		$this->stay_number_of_children = $stay_number_of_children;
		$this->stay_room_price         = $stay_room_price;
		$this->stay_room_price         = $sub_total;
		$this->taxes_and_fees          = $taxes_and_fees;
		$this->total_amount            = $total_amount;
		$this->hotel_footer            = $hotel_footer;

		add_action( 'admin_menu', array( $this, 'add_invoicing_admin_menu' ) ); // This now points to the add_admin_menu function
		add_action( 'admin_menu', array( $this, 'add_activity_invoicing_admin_menu' ) ); // This now points to the add_admin_menu function

		add_action( 'wp_ajax_get_invoice_booking_details', array( $this, 'get_invoice_booking_details' ) );
		add_action( 'wp_ajax_nopriv_get_invoice_booking_details', array( $this, 'get_invoice_booking_details' ) );

		add_action( 'wp_ajax_get_invoice_activity_details', array( $this, 'get_invoice_activity_details' ) );
		add_action( 'wp_ajax_nopriv_get_invoice_activity_details', array( $this, 'get_invoice_activity_details' ) );
	}

	/**
	 * Method add_invoicing_admin_menu
	 *
	 * @return void
	 */
	public function add_invoicing_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=staylodgic_bookings', // Set the parent slug to your custom post type slug
			esc_html__( 'Invoices', 'staylodgic' ),
			esc_html__( 'Invoices', 'staylodgic' ),
			'edit_posts',
			'staylodgic-invoicing',
			array( $this, 'booking_invoices' )
		);
	}
	/**
	 * Method add_activity_invoicing_admin_menu
	 *
	 * @return void
	 */
	public function add_activity_invoicing_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=staylodgic_actvtres', // Set the parent slug to your custom post type slug
			esc_html__( 'Invoices', 'staylodgic' ),
			esc_html__( 'Invoices', 'staylodgic' ),
			'edit_posts',
			'staylodgic-activity-invoicing',
			array( $this, 'activity_invoices' )
		);
	}

	/**
	 * Method activity_invoices
	 *
	 * @return void
	 */
	public function activity_invoices() {

		echo '<h1>' . esc_html__( 'Activity Invoices', 'staylodgic' ) . '</h1>';
		echo '<div class="admin-staylodgic-content">';

		$activity_invoice = self::activity_booking_searcher();
		echo wp_kses( $activity_invoice, staylodgic_get_guest_invoice_tags() );

		echo '</div>';
	}

	/**
	 * Method booking_invoices
	 *
	 * @return void
	 */
	public function booking_invoices() {

		echo '<h1>' . esc_html__( 'Invoices', 'staylodgic' ) . '</h1>';
		echo '<div class="admin-staylodgic-content">';

		$booking_invoice = self::hotel_booking_searcher();
		echo wp_kses( $booking_invoice, staylodgic_get_guest_invoice_tags() );

		echo '</div>';
	}

	/**
	 * Method get_invoice_activity_details
	 *
	 * @return void
	 */
	public function get_invoice_activity_details() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_bookingdetails_nonce'] ) || ! check_admin_referer( 'staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$booking_number = '';
		if ( isset( $_POST['booking_number'] ) ) {
			$booking_number = sanitize_text_field( wp_unslash( $_POST['booking_number'] ) );
		}
		// Fetch reservation details
		$reservations_instance = new \Staylodgic\Activity();
		$stay_reservation_id   = $reservations_instance->get_activity_id_for_booking( $booking_number );

		$date                  = false;
		$activity_id           = false;
		$reservations_instance = new \Staylodgic\Activity( $date, $activity_id, $stay_reservation_id );

		// Hotel Information
		$property_logo_id = staylodgic_get_option( 'activity_property_logo' );
		$property_name    = staylodgic_get_option( 'activity_property_name' );
		$property_phone   = staylodgic_get_option( 'activity_property_phone' );
		$property_address = staylodgic_get_option( 'activity_property_address' );
		$property_header  = staylodgic_get_option( 'activity_property_header' );
		$property_footer  = staylodgic_get_option( 'activity_property_footer' );

		$this->stay_reservation_id = $stay_reservation_id;
		$this->hotel_name          = $property_name;
		$this->hotel_phone         = $property_phone;
		$this->hotel_address       = $property_address;
		$this->hotel_header        = $property_header;
		$this->hotel_footer        = $property_footer;
		$this->hotel_logo          = $property_logo_id ? wp_get_attachment_image_url( $property_logo_id, 'full' ) : '';

		if ( $stay_reservation_id ) {
			$this->stay_booking_number = $booking_number;
			$this->got_checkin_date    = get_post_meta( $stay_reservation_id, 'staylodgic_checkin_date', true );

			$adults = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_activity_adults', true );

			$children = array();
			$children = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_activity_children', true );

			$this->stay_number_of_adults = $adults;

			$stay_total_guests = intval( $adults );

			if ( isset( $children['number'] ) ) {
				$this->stay_number_of_children = $children['number'];
				$stay_total_guests            += intval( $children['number'] );
			}

			$this->stay_number_of_guests = $stay_total_guests;

			$this->booking_status = esc_html__( 'Booking Pending', 'staylodgic' );
			if ( $reservations_instance->is_confirmed_reservation( $stay_reservation_id ) ) {
				$this->booking_status = esc_html__( 'Booking Confirmed', 'staylodgic' );
			}
			$this->stay_room_type = $reservations_instance->get_activity_name_for_reservation( $stay_reservation_id );
			// Add other reservation details as needed

			$tax_gen_status = get_post_meta( $stay_reservation_id, 'staylodgic_tax', true );
			$tax_gen_html   = get_post_meta( $stay_reservation_id, 'staylodgic_tax_html_data', true );
			$tax_gen_data   = get_post_meta( $stay_reservation_id, 'staylodgic_tax_data', true );

			$tax_summary  = '<div id="input-tax-summary">';
			$tax_summary .= '<div class="input-tax-summary-wrap">';
			if ( 'enabled' === $tax_gen_status ) {
				$tax_summary .= '<div class="input-tax-summary-wrap-inner">';
				$tax_summary .= $tax_gen_html;

				$tax_summary .= '</div>';
			}
			$tax_summary .= '</div>';
			$tax_summary .= '</div>';

			$this->taxes_and_fees = $tax_summary;

			$rate_per_person = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_rate_per_person', true );
			$sub_total       = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_subtotal_activity_cost', true );
			$total_amount    = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_total_room_cost', true );

			$this->stay_room_price = $rate_per_person;
			$this->sub_total       = $sub_total;
			$this->total_amount    = $total_amount;

			// Fetch guest details
			$stay_guest_id = $reservations_instance->get_guest_id_for_reservation( $booking_number );
			if ( $stay_guest_id ) {
				$this->stay_customer_name = get_post_meta( $stay_guest_id, 'staylodgic_full_name', true );
				$this->customer_email     = get_post_meta( $stay_guest_id, 'staylodgic_email_address', true );
			}
		} else {
			echo '<p>' . esc_html__( 'No reservation found for Booking Number:', 'staylodgic' ) . ' ' . esc_html( $booking_number ) . '</p>';
		}

		$information_sheet = $this->invoice_activity_template(
			$this->stay_reservation_id,
			$this->booking_status,
			$this->stay_booking_number,
			$this->number_days,
			$this->hotel_logo,
			$this->hotel_name,
			$this->hotel_header,
			$this->hotel_address,
			$this->hotel_phone,
			$this->stay_customer_name,
			$this->customer_email,
			$this->got_checkin_date,
			$this->got_checkout_date,
			$this->stay_room_type,
			$this->stay_number_of_guests,
			$this->stay_number_of_adults,
			$this->stay_number_of_children,
			$this->stay_room_price,
			$this->sub_total,
			$this->taxes_and_fees,
			$this->total_amount,
			$this->hotel_footer
		);

		echo wp_kses( $information_sheet, staylodgic_get_invoice_form_tags() );
		wp_die(); // Terminate and return a proper response
	}

	/**
	 * Method get_invoice_booking_details
	 *
	 * @return void
	 */
	public function get_invoice_booking_details() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_bookingdetails_nonce'] ) || ! check_admin_referer( 'staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$booking_number = '';
		if ( isset( $_POST['booking_number'] ) ) {
			$booking_number = sanitize_text_field( wp_unslash( $_POST['booking_number'] ) );
		}

		// Fetch reservation details
		$reservations_instance = new \Staylodgic\Reservations();
		$stay_reservation_id   = $reservations_instance->get_reservation_id_for_booking( $booking_number );

		$date                  = false;
		$room_id               = false;
		$reservations_instance = new \Staylodgic\Reservations( $date, $room_id, $stay_reservation_id );
		// Hotel Information
		$property_logo_id = staylodgic_get_option( 'property_logo' );
		$property_name    = staylodgic_get_option( 'property_name' );
		$property_phone   = staylodgic_get_option( 'property_phone' );
		$property_address = staylodgic_get_option( 'property_address' );
		$property_header  = staylodgic_get_option( 'property_header' );
		$property_footer  = staylodgic_get_option( 'property_footer' );

		$this->stay_reservation_id = $stay_reservation_id;
		$this->hotel_name          = $property_name;
		$this->hotel_phone         = $property_phone;
		$this->hotel_address       = $property_address;
		$this->hotel_header        = $property_header;
		$this->hotel_footer        = $property_footer;
		$this->hotel_logo          = $property_logo_id ? wp_get_attachment_image_url( $property_logo_id, 'full' ) : '';

		if ( $stay_reservation_id ) {
			$this->stay_booking_number = $booking_number;
			$this->got_checkin_date    = get_post_meta( $stay_reservation_id, 'staylodgic_checkin_date', true );
			$this->got_checkout_date   = get_post_meta( $stay_reservation_id, 'staylodgic_checkout_date', true );

			$adults = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_room_adults', true );

			$children = array();
			$children = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_room_children', true );

			$this->stay_number_of_adults = $adults;

			$stay_total_guests = intval( $adults );

			if ( isset( $children['number'] ) ) {
				$this->stay_number_of_children = $children['number'];
				$stay_total_guests            += intval( $children['number'] );
			}

			$this->stay_number_of_guests = $stay_total_guests;

			$this->booking_status = esc_html__( 'Booking Pending', 'staylodgic' );
			if ( $reservations_instance->is_confirmed_reservation( $stay_reservation_id ) ) {
				$this->booking_status = esc_html__( 'Booking Confirmed', 'staylodgic' );
			}
			$this->stay_room_type = $reservations_instance->get_room_name_for_reservation( $stay_reservation_id );
			$this->number_days    = $reservations_instance->count_reservation_days( $stay_reservation_id );
			// Add other reservation details as needed

			$tax_gen_status = get_post_meta( $stay_reservation_id, 'staylodgic_tax', true );
			$tax_gen_html   = get_post_meta( $stay_reservation_id, 'staylodgic_tax_html_data', true );
			$tax_gen_data   = get_post_meta( $stay_reservation_id, 'staylodgic_tax_data', true );

			$tax_summary  = '<div id="input-tax-summary">';
			$tax_summary .= '<div class="input-tax-summary-wrap">';
			if ( 'enabled' === $tax_gen_status ) {
				$tax_summary .= '<div class="input-tax-summary-wrap-inner">';
				$tax_summary .= $tax_gen_html;

				$tax_summary .= '</div>';
			}
			$tax_summary .= '</div>';
			$tax_summary .= '</div>';

			$this->taxes_and_fees = $tax_summary;

			$rate_per_night = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_rate_per_night', true );
			$sub_total      = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_subtotal_room_cost', true );
			$total_amount   = get_post_meta( $stay_reservation_id, 'staylodgic_reservation_total_room_cost', true );

			$this->stay_room_price = $rate_per_night;
			$this->sub_total       = $sub_total;
			$this->total_amount    = $total_amount;

			// Fetch guest details
			$stay_guest_id = $reservations_instance->get_guest_id_for_reservation( $booking_number );
			if ( $stay_guest_id ) {
				$this->stay_customer_name = get_post_meta( $stay_guest_id, 'staylodgic_full_name', true );
				$this->customer_email     = get_post_meta( $stay_guest_id, 'staylodgic_email_address', true );
			}
		} else {
			echo '<p>' . esc_html__( 'No reservation found for Booking Number:', 'staylodgic' ) . ' ' . esc_html( $booking_number ) . '</p>';
		}

		$information_sheet = $this->invoice_template(
			$this->stay_reservation_id,
			$this->booking_status,
			$this->stay_booking_number,
			$this->number_days,
			$this->hotel_logo,
			$this->hotel_name,
			$this->hotel_header,
			$this->hotel_address,
			$this->hotel_phone,
			$this->stay_customer_name,
			$this->customer_email,
			$this->got_checkin_date,
			$this->got_checkout_date,
			$this->stay_room_type,
			$this->stay_number_of_guests,
			$this->stay_number_of_adults,
			$this->stay_number_of_children,
			$this->stay_room_price,
			$this->sub_total,
			$this->taxes_and_fees,
			$this->total_amount,
			$this->hotel_footer
		);

		echo wp_kses( $information_sheet, staylodgic_get_invoice_form_tags() );
		wp_die(); // Terminate and return a proper response
	}

	/**
	 * Method activity_booking_searcher
	 *
	 * @return void
	 */
	public function activity_booking_searcher() {
		ob_start();
		$staylodgic_bookingdetails_nonce = wp_create_nonce( 'staylodgic-bookingdetails-nonce' );
		?>

		<div id="hotel-booking-form">

			<div class="front-booking-search">
				<div class="front-booking-number-wrap">
					<div class="front-booking-number-container">
						<div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
							<input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr( $staylodgic_bookingdetails_nonce ); ?>" />
							<input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
							<label for="booking_number" class="control-label"><?php echo esc_html__( 'Booking No.', 'staylodgic' ); ?></label>
						</div>
					</div>
					<div id="invoiceActivityDetails" class="form-search-button"><?php echo esc_html__( 'Search', 'staylodgic' ); ?></div>
				</div>
			</div>

			<div class="booking-details-lister">
				<div id="booking-details-ajax"></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method hotel_booking_searcher
	 *
	 * @return void
	 */
	public function hotel_booking_searcher() {
		ob_start();
		$staylodgic_bookingdetails_nonce = wp_create_nonce( 'staylodgic-bookingdetails-nonce' );
		?>

		<div id="hotel-booking-form">

			<div class="front-booking-search">
				<div class="front-booking-number-wrap">
					<div class="front-booking-number-container">
						<div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
							<input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr( $staylodgic_bookingdetails_nonce ); ?>" />
							<input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
							<label for="booking_number" class="control-label"><?php echo esc_html__( 'Booking No.', 'staylodgic' ); ?></label>
						</div>
					</div>
					<div id="invoiceDetails" class="form-search-button"><?php echo esc_html__( 'Search', 'staylodgic' ); ?></div>
				</div>
			</div>

			<div class="booking-details-lister">
				<div id="booking-details-ajax"></div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method invoice_activity_template
	 *
	 * @return void
	 */
	public function invoice_activity_template(
		$stay_reservation_id,
		$booking_status,
		$stay_booking_number,
		$number_days,
		$hotel_logo,
		$hotel_name,
		$hotel_header,
		$hotel_address,
		$hotel_phone,
		$stay_customer_name,
		$customer_email,
		$got_checkin_date,
		$got_checkout_date,
		$stay_room_type,
		$stay_number_of_guests,
		$stay_number_of_adults,
		$stay_number_of_children,
		$stay_room_price,
		$sub_total,
		$taxes_and_fees,
		$total_amount,
		$hotel_footer
	) {
		$activity_property_logo_width = staylodgic_get_option( 'activity_property_logo_width' );
		$stay_current_date            = gmdate( 'F jS, Y' ); // Outputs: January 1st, 2024
		ob_start();
		?>
		<div class="invoice-container-buttons">
			<button data-title="Guest Registration <?php echo esc_attr( $stay_booking_number ); ?>" data-id="<?php echo esc_attr( $stay_booking_number ); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php echo esc_html__( 'Print Invoice', 'staylodgic' ); ?></button>
			<button data-file="registration-<?php echo esc_attr( $stay_booking_number ); ?>" data-id="<?php echo esc_attr( $stay_booking_number ); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php echo esc_html__( 'Save PDF', 'staylodgic' ); ?></button>
		</div>
		<div class="invoice-container-outer">
			<div class="invoice-container" data-bookingnumber="<?php echo esc_attr( $stay_booking_number ); ?>">
				<div class="invoice-container-inner">
					<div id="invoice-hotel-header">
						<section id="invoice-hotel-logo">
							<img class="invoice-logo" src="<?php echo esc_url( $hotel_logo ); ?>" width="<?php echo esc_attr( $activity_property_logo_width ) . 'px'; ?>" height="auto" />
						</section>
						<section id="invoice-info">
							<p><?php echo esc_html( $hotel_header ); ?></p>
							<p><?php echo esc_html__( 'Invoice No:', 'staylodgic' ); ?> <?php echo esc_html( $stay_booking_number . '-' . $stay_reservation_id ); ?></p>
							<p><?php echo esc_html__( 'Invoice Date:', 'staylodgic' ); ?> <?php echo esc_html( $stay_current_date ); ?></p>
							<p class="invoice-booking-status"><?php echo esc_html( $booking_status ); ?></p>
						</section>
					</div>
					<section id="invoice-hotel-info">
						<p><strong><?php echo esc_html( $hotel_name ); ?></strong></p>
						<p><?php echo esc_html( $hotel_address ); ?></p>
						<p><?php echo esc_html( $hotel_phone ); ?></p>
					</section>
					<section id="invoice-customer-info">
						<h2><?php echo esc_html__( 'Bill to:', 'staylodgic' ); ?></h2>
						<p><?php echo esc_html__( 'Name:', 'staylodgic' ); ?> <?php echo esc_html( $stay_customer_name ); ?></p>
						<p><?php echo esc_html__( 'Email:', 'staylodgic' ); ?> <?php echo esc_html( $customer_email ); ?></p>
					</section>

					<div id="invoice-booking-information">

						<section id="invoice-booking-details">
							<h2><?php echo esc_html__( 'Activity Booking Details', 'staylodgic' ); ?></h2>
							<p><span><?php echo esc_html__( 'Booking No:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_booking_number ); ?></p>
							<p><span><?php echo esc_html__( 'Activity Date:', 'staylodgic' ); ?></span><?php echo esc_html( $got_checkin_date ); ?></p>
							<p><span><?php echo esc_html__( 'Activity Type:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_room_type ); ?></p>
							<p><span><?php echo esc_html__( 'Adults:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_number_of_adults ); ?></p>
							<?php
							if ( $stay_number_of_children > 0 ) {
								?>
								<p><span><?php echo esc_html__( 'Children:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_number_of_children ); ?></p>
								<?php
							}
							?>
						</section>

						<section id="invoice-booking-pricing">
							<h2><?php echo esc_html__( 'Activity Price', 'staylodgic' ); ?></h2>
							<p class="nightly-rate-info"><span class="nightly-rate"><?php echo wp_kses( staylodgic_price( $stay_room_price ), staylodgic_get_price_tags() ); ?></span><span class="nights"> x <?php echo esc_html( $number_days ); ?> <?php echo esc_html__( 'Per Person', 'staylodgic' ); ?></span></p>
							<?php
							$reservations_instance = new \Staylodgic\Activity();
							$stay_reservation_id   = $reservations_instance->get_activity_id_for_booking( $stay_booking_number );
							$tax_gen_status        = get_post_meta( $stay_reservation_id, 'staylodgic_tax', true );
							if ( 'enabled' === $tax_gen_status ) {
								?>
								<div class="subtotal-info">
									<p class="subtotal"><?php echo esc_html__( 'Sub Total:', 'staylodgic' ); ?></p>
									<p><?php echo wp_kses( staylodgic_price( $sub_total ), staylodgic_get_price_tags() ); ?></p>
								</div>
								<p><?php echo esc_html__( 'Taxes and Fees:', 'staylodgic' ); ?> <?php echo wp_kses( $taxes_and_fees, staylodgic_get_invoice_tax_details_tags() ); ?></p>
								<?php
							}
							?>
							<div class="invoice-total">
								<strong>
									<p><?php echo esc_html__( 'Total Amount:', 'staylodgic' ); ?></p>
									<p class="price-total"><?php echo wp_kses( staylodgic_price( $total_amount ), staylodgic_get_price_tags() ); ?></p>
								</strong>
							</div>
						</section>
					</div>

				</div>
				<footer>
					<div class="invoice-footer"><?php echo esc_html( $hotel_footer ); ?></div>
				</footer>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method invoice_template
	 *
	 * @return void
	 */
	public function invoice_template(
		$stay_reservation_id,
		$booking_status,
		$stay_booking_number,
		$number_days,
		$hotel_logo,
		$hotel_name,
		$hotel_header,
		$hotel_address,
		$hotel_phone,
		$stay_customer_name,
		$customer_email,
		$got_checkin_date,
		$got_checkout_date,
		$stay_room_type,
		$stay_number_of_guests,
		$stay_number_of_adults,
		$stay_number_of_children,
		$stay_room_price,
		$sub_total,
		$taxes_and_fees,
		$total_amount,
		$hotel_footer
	) {

		$property_logo_width = staylodgic_get_option( 'property_logo_width' );
		$stay_current_date   = gmdate( 'F jS, Y' ); // Outputs: January 1st, 2024
		ob_start();
		?>
		<div class="invoice-container-buttons">
			<button data-title="Guest Registration <?php echo esc_attr( $stay_booking_number ); ?>" data-id="<?php echo esc_attr( $stay_booking_number ); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php echo esc_html__( 'Print Invoice', 'staylodgic' ); ?></button>
			<button data-file="registration-<?php echo esc_attr( $stay_booking_number ); ?>" data-id="<?php echo esc_attr( $stay_booking_number ); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php echo esc_html__( 'Save PDF', 'staylodgic' ); ?></button>
		</div>
		<div class="invoice-container-outer">
			<div class="invoice-container" data-bookingnumber="<?php echo esc_attr( $stay_booking_number ); ?>">
				<div class="invoice-container-inner">
					<div id="invoice-hotel-header">
						<section id="invoice-hotel-logo">
							<img class="invoice-logo" src="<?php echo esc_url( $hotel_logo ); ?>" width="<?php echo esc_attr( $property_logo_width ) . 'px'; ?>" height="auto" />
						</section>
						<section id="invoice-info">
							<p><?php echo wp_kses( $hotel_header, staylodgic_get_allowed_tags() ); ?></p>
							<p><?php echo esc_html__( 'Invoice No:', 'staylodgic' ); ?> <?php echo esc_html( $stay_booking_number . '-' . $stay_reservation_id ); ?></p>
							<p><?php echo esc_html__( 'Invoice Date:', 'staylodgic' ); ?> <?php echo esc_html( $stay_current_date ); ?></p>
							<p class="invoice-booking-status"><?php echo esc_html( $booking_status ); ?></p>
						</section>
					</div>
					<section id="invoice-hotel-info">
						<p><strong><?php echo esc_html( $hotel_name ); ?></strong></p>
						<p><?php echo esc_html( $hotel_address ); ?></p>
						<p><?php echo esc_html( $hotel_phone ); ?></p>
					</section>
					<section id="invoice-customer-info">
						<h2><?php echo esc_html__( 'Bill to:', 'staylodgic' ); ?></h2>
						<p><?php echo esc_html__( 'Name:', 'staylodgic' ); ?> <?php echo esc_html( $stay_customer_name ); ?></p>
						<p><?php echo esc_html__( 'Email:', 'staylodgic' ); ?> <?php echo esc_html( $customer_email ); ?></p>
					</section>

					<div id="invoice-booking-information">

						<section id="invoice-booking-details">
							<h2><?php echo esc_html__( 'Booking Details', 'staylodgic' ); ?></h2>
							<p><span><?php echo esc_html__( 'Booking No:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_booking_number ); ?></p>
							<p><span><?php echo esc_html__( 'Check-in Date:', 'staylodgic' ); ?></span><?php echo esc_html( $got_checkin_date ); ?></p>
							<p><span><?php echo esc_html__( 'Check-out Date:', 'staylodgic' ); ?></span><?php echo esc_html( $got_checkout_date ); ?></p>
							<p><span><?php echo esc_html__( 'Room Type:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_room_type ); ?></p>
							<p><span><?php echo esc_html__( 'Adults:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_number_of_adults ); ?></p>
							<?php
							if ( $stay_number_of_children > 0 ) {
								?>
								<p><span><?php echo esc_html__( 'Children:', 'staylodgic' ); ?></span><?php echo esc_html( $stay_number_of_children ); ?></p>
								<?php
							}
							?>
						</section>

						<section id="invoice-booking-pricing">
							<h2><?php echo esc_html__( 'Room Price', 'staylodgic' ); ?></h2>
							<p class="nightly-rate-info"><span class="nightly-rate"><?php echo wp_kses( staylodgic_price( $stay_room_price ), staylodgic_get_price_tags() ); ?></span><span class="nights"> x <?php echo esc_html( $number_days ); ?> <?php echo esc_html__( 'Nights', 'staylodgic' ); ?></span></p>
							<?php
							$reservations_instance = new \Staylodgic\Reservations();
							$stay_reservation_id   = $reservations_instance->get_reservation_id_for_booking( $stay_booking_number );
							$tax_gen_status        = get_post_meta( $stay_reservation_id, 'staylodgic_tax', true );
							if ( 'enabled' === $tax_gen_status ) {
								?>
								<div class="subtotal-info">
									<p class="subtotal"><?php echo esc_html__( 'Sub Total:', 'staylodgic' ); ?></p>
									<p><?php echo wp_kses( staylodgic_price( $sub_total ), staylodgic_get_price_tags() ); ?></p>
								</div>
								<p><?php echo esc_html__( 'Taxes and Fees:', 'staylodgic' ); ?> <?php echo wp_kses( $taxes_and_fees, staylodgic_get_invoice_tax_details_tags() ); ?></p>
								<?php
							}
							?>
							<div class="invoice-total">
								<strong>
									<p><?php echo esc_html__( 'Total Amount:', 'staylodgic' ); ?></p>
									<p><?php echo wp_kses( staylodgic_price( $total_amount ), staylodgic_get_price_tags() ); ?></p>
								</strong>
							</div>
						</section>
					</div>

				</div>
				<footer>
					<div class="invoice-footer"><?php echo esc_html( $hotel_footer ); ?></div>
				</footer>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
