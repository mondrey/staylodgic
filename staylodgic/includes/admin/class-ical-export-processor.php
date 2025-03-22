<?php

namespace Staylodgic;

class Ical_Export_Processor {

	private $batch_size = 50;

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'export_csv_bookings' ) );
		add_action( 'admin_menu', array( $this, 'export_csv_registrations' ) );
		add_action( 'wp_ajax_download_ical', array( $this, 'ajax_download_reservations_csv' ) );
		add_action( 'wp_ajax_download_registrations_ical', array( $this, 'ajax_download_guest_registrations_csv' ) );
	}

	/**
	 * Method ajax_download_guest_registrations_csv
	 *
	 * @return void
	 */
	public function ajax_download_guest_registrations_csv() {

		// Check nonce validity
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'staylodgic-nonce-admin' ) ) {
			wp_die(
				esc_html__( 'Security check failed.', 'staylodgic' ),
				esc_html__( 'Unauthorized Request', 'staylodgic' ),
				array( 'response' => 403 )
			);
		}

		// Check user capability (adjust if needed)
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to perform this action.', 'staylodgic' ),
				esc_html__( 'Access Denied', 'staylodgic' ),
				array( 'response' => 403 )
			);
		}

		$month = false;
		if ( isset( $_POST['month'] ) ) {
			$month = sanitize_text_field( wp_unslash( $_POST['month'] ) );
		}

		if ( $month ) {
			$this->download_guest_registrations_csv( $month );
		}
		wp_die();
	}

	/**
	 * Method ajax_download_reservations_csv
	 *
	 * @return void
	 */
	public function ajax_download_reservations_csv() {

		// Check for nonce security
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'staylodgic-nonce-admin' ) ) {
			wp_die(
				esc_html__( 'Security check failed.', 'staylodgic' ),
				esc_html__( 'Unauthorized Request', 'staylodgic' ),
				array( 'response' => 403 )
			);
		}

		// Check user capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to perform this action.', 'staylodgic' ),
				esc_html__( 'Access Denied', 'staylodgic' ),
				array( 'response' => 403 )
			);
		}

		$room_id = isset( $_POST['room_id'] ) ? intval( $_POST['room_id'] ) : false;
		$month   = false;
		if ( isset( $_POST['month'] ) ) {
			$month = sanitize_text_field( wp_unslash( $_POST['month'] ) );
		}

		if ( $room_id ) {
			$this->download_reservations_csv( $room_id, $month );
		}
		wp_die();
	}

	/**
	 * Method export_csv_registrations
	 *
	 * @return void
	 */
	public function export_csv_registrations() {
		add_submenu_page(
			'staylodgic-settings',
			// This is the slug of the parent menu
			__( 'Export Guest Registrations', 'staylodgic' ),
			__( 'Export Guest Registrations', 'staylodgic' ),
			'edit_posts',
			'staylodgic-slg-export-registrations-ical',
			array( $this, 'csv_registrations_export' )
		);
	}

	/**
	 * Method export_csv_bookings
	 *
	 * @return void
	 */
	public function export_csv_bookings() {
		add_submenu_page(
			'staylodgic-settings',
			// This is the slug of the parent menu
			__( 'Export Bookings', 'staylodgic' ),
			__( 'Export Bookings', 'staylodgic' ),
			'edit_posts',
			'staylodgic-slg-export-booking-ical',
			array( $this, 'csv_bookings_export' )
		);
	}

	/**
	 * Method csv_registrations_export
	 *
	 * @return void
	 */
	public function csv_registrations_export() {
		// The HTML content of the 'Staylodgic' page goes here
		echo '<div class="expor-import-calendar">';
		echo '<div id="export-import-form">';
		echo '<h1>' . esc_html__( 'Export Guest Registrations', 'staylodgic' ) . '</h1>';
		echo '<p>' . esc_html__( 'Streamline your record management by exporting monthly guest registrations. Simply choose a month and click "Download" to create a CSV file containing detailed registration information.', 'staylodgic' ) . '</p>';
		echo '<div class="how-to-admin">';
		echo '<h2>' . esc_html__( 'How to Export:', 'staylodgic' ) . '</h2>';
		echo '<ol>';
		echo '<li>' . esc_html__( 'Choose the month for which you want to export guest registrations.', 'staylodgic' ) . '</li>';
		echo '<li>' . esc_html__( 'Click the "Donwload" button to download your file.', 'staylodgic' ) . '</li>';
		echo '</ol>';
		echo '</div>';

		echo "<form id='room_ical_form' method='post'>";
		echo '<input type="hidden" name="ical_form_nonce" value="' . esc_attr( wp_create_nonce( 'ical_form_nonce' ) ) . '">';

		echo '<div class="import-export-heading">' . esc_html__( 'Choose calendar month for export', 'staylodgic' ) . '</div>';
		echo '<input type="text" class="exporter_calendar" id="exporter_calendar" name="exporter_calendar" value="" />';
		echo '<div class="exporter_calendar-error-wrap">';
		echo '<div class="exporter_calendar-no-records">' . esc_html__( 'No Records Found', 'staylodgic' ) . '</div>';
		echo '</div>';
		echo '<div class="import-export-wrap">';

		echo '<button type="button" class="download_registrations_export_ical btn btn-primary">';
		echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
		echo '<span role="status"> ' . esc_html__( 'Download', 'staylodgic' ) . '</span>';
		echo '</button>';

		echo '</div>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Method csv_bookings_export
	 *
	 * @return void
	 */
	public function csv_bookings_export() {
		// The HTML content of the 'Staylodgic' page goes here
		echo '<div class="expor-import-calendar">';
		echo '<div id="export-import-form">';
		echo '<h1>' . esc_html__( 'Export Bookings', 'staylodgic' ) . '</h1>';
		echo '<p>' . esc_html__( 'Efficiently manage your records by exporting your room bookings. Select the room and month to generate a downloadable CSV file of the booking details.', 'staylodgic' ) . '</p>';
		echo '<div class="how-to-admin">';
		echo '<h2>' . esc_html__( 'How to Export:', 'staylodgic' ) . '</h2>';
		echo '<ol>';
		echo '<li>' . esc_html__( 'Choose the month for which you want to export bookings.', 'staylodgic' ) . '</li>';
		echo '<li>' . esc_html__( 'Click the "Donwload" button next to the choice of room to download your file.', 'staylodgic' ) . '</li>';
		echo '</ol>';
		echo '</div>';

		echo "<form id='room_ical_form' method='post'>";
		echo '<input type="hidden" name="ical_form_nonce" value="' . esc_attr( wp_create_nonce( 'ical_form_nonce' ) ) . '">';

		echo '<div class="import-export-heading">' . esc_html__( 'Choose calendar month for export', 'staylodgic' ) . '</div>';
		echo '<input type="text" class="exporter_calendar" id="exporter_calendar" name="exporter_calendar" value="" />';

		echo '<div class="import-export-wrap">';
		$rooms = Rooms::query_rooms();
		foreach ( $rooms as $room ) {
			// Get meta
			$room_ical_data = get_post_meta( $room->ID, 'room_ical_data', true );

			echo '<div class="room_ical_export_wrapper" data-room-id="' . esc_attr( $room->ID ) . '">';
			echo '<div class="import-export-heading">' . esc_html( $room->post_title ) . '</div>';

			echo '<button data-room-id="' . esc_attr( $room->ID ) . '" type="button" class="download_export_ical btn btn-primary">';
			echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
			echo '<span role="status"> ' . esc_html__( 'Download', 'staylodgic' ) . '</span>';
			echo '</button>';

			echo '</div>';
		}

		echo '</div>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Method generate_ical_from_reservations
	 *
	 * @param $reservations $reservations [explicite description]
	 *
	 * @return void
	 */
	public function generate_ical_from_reservations( $reservations ) {
		$ical = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Your Company//Your Calendar//EN\r\n";

		foreach ( $reservations as $reservation ) {
			$checkin_date       = get_post_meta( $reservation->ID, 'staylodgic_checkin_date', true );
			$checkout_date      = get_post_meta( $reservation->ID, 'staylodgic_checkout_date', true );
			$booking_number     = get_post_meta( $reservation->ID, 'staylodgic_booking_number', true );
			$reservation_status = get_post_meta( $reservation->ID, 'staylodgic_reservation_status', true );

			// Format dates for iCal
			$checkin_date_ical  = $this->format_date_for_ical( $checkin_date );
			$checkout_date_ical = $this->format_date_for_ical( $checkout_date );

			if ( $checkin_date_ical && $checkout_date_ical ) {
				$ical .= "BEGIN:VEVENT\r\n";
				$ical .= 'UID:' . esc_html( $booking_number ) . "\r\n";
				$ical .= 'DTSTART:' . esc_html( $checkin_date_ical ) . "\r\n";
				$ical .= 'DTEND:' . esc_html( $checkout_date_ical ) . "\r\n";
				$ical .= 'SUMMARY:' . esc_html( $reservation_status ) . "\r\n";
				$ical .= "END:VEVENT\r\n";
			}
		}

		$ical .= "END:VCALENDAR\r\n";

		return $ical;
	}

	/**
	 * Method format_date_for_ical
	 *
	 * @param $date $date [explicite description]
	 *
	 * @return void
	 */
	private function format_date_for_ical( $date ) {
		if ( ! $date ) {
			return false;
		}
		$timestamp = strtotime( $date );

		if ( $timestamp ) {
			return gmdate( 'Ymd\THis', $timestamp );
		} else {
			return false;
		}
	}

	/**
	 * Method download_reservations_ical
	 *
	 * @param $room_id $room_id [explicite description]
	 *
	 * @return void
	 */
	public function download_reservations_ical( $room_id ) {
		$stay_date_string     = '';
		$reservation_instance = new \Staylodgic\Reservations( $stay_date_string, $room_id );
		$reservations_query   = $reservation_instance->get_reservations_for_room( false, false, false, false, $room_id );

		// Extract post objects from WP_Query
		$reservations = $reservations_query->posts;

		$ical_content = $this->generate_ical_from_reservations( $reservations );

		$current_date_time = gmdate( 'Y-m-d_H-i-s' ); // Formats the date and time as YYYY-MM-DD_HH-MM-SS
		$filename          = 'reservations-' . sanitize_file_name( $room_id ) . '-' . sanitize_file_name( $current_date_time ) . '.ics';

		$filename = htmlspecialchars( $filename, ENT_QUOTES, 'UTF-8' );
		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		echo wp_kses_post( $ical_content );
		exit;
	}

	/**
	 * Method generate_csv_from_reservations
	 *
	 * @param $start_date $start_date [explicite description]
	 * @param $end_date $end_date [explicite description]
	 * @param $room_id $room_id [explicite description]
	 *
	 * @return void
	 */
	public function generate_csv_from_reservations( $start_date, $end_date, $room_id ) {

		$csv_data = "Booking Number,Room Name,Adults,Children,Checkin Date,Checkout Date,Reservation Status\r\n";

		// Initialize Reservations instance with start date and end date
		$reservation_instance = new \Staylodgic\Reservations( $start_date, $room_id );
		$reservations_query   = $reservation_instance->get_reservations_for_room( $start_date, $end_date, false, false, $room_id );

		// Extract post objects from WP_Query
		$reservations = $reservations_query->posts;

		// Loop through each reservation in the list of reservations
		foreach ( $reservations as $reservation ) {

			// Retrieve the check-in date for the reservation
			$checkin_date = get_post_meta(
				$reservation->ID,                    // Unique reservation ID
				'staylodgic_checkin_date',           // Meta key for check-in date
				true                                 // Retrieve as a single value
			);

			// Check if the check-in date is empty and assign a default value of '-' if it is
			if ( empty( $checkin_date ) ) {
				$checkin_date = '-';
			}

			// Retrieve the check-out date for the reservation
			$checkout_date = get_post_meta(
				$reservation->ID,                    // Unique reservation ID
				'staylodgic_checkout_date',          // Meta key for check-out date
				true                                 // Retrieve as a single value
			);

			// Check if the check-out date is empty and assign a default value of '-' if it is
			if ( empty( $checkout_date ) ) {
				$checkout_date = '-';
			}

			// Retrieve the booking number for the reservation
			$booking_number = get_post_meta(
				$reservation->ID,                    // Unique reservation ID
				'staylodgic_booking_number',         // Meta key for booking number
				true                                 // Retrieve as a single value
			);

			// Check if the booking number is empty and assign a default value of '-' if it is
			if ( empty( $booking_number ) ) {
				$booking_number = '-';
			}

			// Retrieve the reservation status
			$reservation_status = get_post_meta(
				$reservation->ID,                    // Unique reservation ID
				'staylodgic_reservation_status',     // Meta key for reservation status
				true                                 // Retrieve as a single value
			);

			// Check if the reservation status is empty and assign a default value of '-' if it is
			if ( empty( $reservation_status ) ) {
				$reservation_status = '-';
			}

			// Retrieve the room name using the reservation instance method
			$room_name = $reservation_instance->get_room_name_for_reservation(
				$reservation->ID                     // Unique reservation ID
			);

			// Check if the room name is empty and assign a default value of '-' if it is
			if ( empty( $room_name ) ) {
				$room_name = '-';
			}

			// Retrieve the number of adults using the reservation instance method
			$adults_number = $reservation_instance->get_number_of_adults_for_reservation(
				$reservation->ID                     // Unique reservation ID
			);

			// Check if the number of adults is empty and assign a default value of '0' if it is
			if ( empty( $adults_number ) ) {
				$adults_number = '0';
			}

			// Retrieve the number of children using the reservation instance method
			$children_number = $reservation_instance->get_number_of_children_for_reservation(
				$reservation->ID                     // Unique reservation ID
			);

			// Check if the number of children is empty and assign a default value of '0' if it is
			if ( empty( $children_number ) ) {
				$children_number = '0';
			}

			// Concatenate all retrieved data into a CSV-formatted string and add a newline character at the end
			$csv_data .= $booking_number . ',' . $room_name . ',' . $adults_number . ',' . $children_number . ',' .
				$checkin_date . ',' . $checkout_date . ',' . $reservation_status . "\r\n";
		}

		return $csv_data;
	}

	/**
	 * Method generate_guest_registration_csv_from_reservations
	 *
	 * @param $start_date $start_date [explicite description]
	 * @param $end_date $end_date [explicite description]
	 *
	 * @return void
	 */
	public function generate_guest_registration_csv_from_reservations( $start_date, $end_date ) {

		$csv_data_header = "Booking Number,Full Name,ID,Country,Booking Channel,Room Name,Checkin Date,Checkin Time,Checkout Date,Checkout Time\r\n";
		$csv_data        = '';

		$rooms = Rooms::query_rooms();
		foreach ( $rooms as $room ) {

			// Initialize Reservations instance with start date and end date
			$reservation_instance = new \Staylodgic\Reservations( $start_date );
			$reservations_query   = $reservation_instance->get_reservations_for_room( $start_date, $end_date, $reservation_status = 'confirmed', false, $room->ID );

			// Extract post objects from WP_Query
			$reservations = $reservations_query->posts;

			foreach ( $reservations as $reservation ) {

				// Retrieve the check-in date for the reservation or assign a default value if not available
				$checkin_date = get_post_meta(
					$reservation->ID,                  // The unique ID for the reservation
					'staylodgic_checkin_date',         // Meta key to retrieve the check-in date
					true                               // Retrieve as a single value
				);

				// Check if the check-in date is empty and assign a default value of '-' if it is
				if ( empty( $checkin_date ) ) {
					$checkin_date = '-';
				}

				// Retrieve the check-out date for the reservation or assign a default value if not available
				$checkout_date = get_post_meta(
					$reservation->ID,                  // The unique ID for the reservation
					'staylodgic_checkout_date',        // Meta key to retrieve the check-out date
					true                               // Retrieve as a single value
				);

				// Check if the check-out date is empty and assign a default value of '-' if it is
				if ( empty( $checkout_date ) ) {
					$checkout_date = '-';
				}

				// Retrieve the booking number for the reservation or assign a default value if not available
				$booking_number = get_post_meta(
					$reservation->ID,                  // The unique ID for the reservation
					'staylodgic_booking_number',       // Meta key to retrieve the booking number
					true                               // Retrieve as a single value
				);

				// Check if the booking number is empty and assign a default value of '-' if it is
				if ( empty( $booking_number ) ) {
					$booking_number = '-';
				}

				// Retrieve the booking channel for the reservation or assign a default value if not available
				$booking_channel = get_post_meta(
					$reservation->ID,                  // The unique ID for the reservation
					'staylodgic_booking_channel',      // Meta key to retrieve the booking channel
					true                               // Retrieve as a single value
				);

				// Check if the booking channel is empty and assign a default value of '-' if it is
				if ( empty( $booking_channel ) ) {
					$booking_channel = '-';
				}
				$room_name = $reservation_instance->get_room_name_for_reservation( $reservation->ID );

				$registry_instance = new \Staylodgic\Guest_Registry();
				$res_reg_ids       = $registry_instance->fetch_res_reg_ids_by_booking_number( $booking_number );

				if ( isset( $res_reg_ids ) && is_array( $res_reg_ids ) ) {

					$register_id       = $res_reg_ids['guest_register_id'];
					$registration_data = get_post_meta( $register_id, 'staylodgic_registration_data', true );

					if ( is_array( $registration_data ) && ! empty( $registration_data ) ) {
						foreach ( $registration_data as $guest_id => $guest_data ) {

							$fullname          = $guest_data['fullname']['value'];
							$passport          = $guest_data['passport']['value'];
							$checkin_date_time = $guest_data['checkin-date']['value'];
							$datetime_parts    = explode( ' ', $checkin_date_time );
							$checkin_date      = $datetime_parts[0];
							$checkin_time      = $datetime_parts[1];

							$checkout_date_time = $guest_data['checkout-date']['value'];
							$datetime_parts     = explode( ' ', $checkout_date_time );
							$checkout_date      = $datetime_parts[0];
							$checkout_time      = $datetime_parts[1];

							$country_code = $guest_data['countries']['value'];

							$country = staylodgic_country_list( 'display', $country_code );

							$csv_data .= "$booking_number,$fullname,$passport,$country,$booking_channel,$room_name,$checkin_date,$checkin_time,$checkout_date,$checkout_time\r\n";
						}
					}
				}
			}
		}

		if ( '' !== $csv_data ) {
			return $csv_data_header . $csv_data;
		} else {
			return false;
		}
	}

	/**
	 * Method download_reservations_csv
	 *
	 * @param $room_id $room_id [explicite description]
	 * @param $month $month [explicite description]
	 *
	 * @return void
	 */
	public function download_reservations_csv( $room_id, $month ) {
		// Calculate start date and end date of the selected month
		$start_date = gmdate( 'Y-m-01', strtotime( $month ) );  // First day of the selected month
		$end_date   = gmdate( 'Y-m-t', strtotime( $month ) );   // Last day of the selected month

		$csv_content = $this->generate_csv_from_reservations( $start_date, $end_date, $room_id );

		$current_date_time = gmdate( 'Y-m-d_H-i-s' );
		$filename          = 'reservations-' . esc_attr( $room_id ) . '-' . esc_attr( $start_date ) . '-' . esc_attr( $end_date ) . '-on-' . esc_attr( $current_date_time ) . '.csv';

		$filename = htmlspecialchars( $filename, ENT_QUOTES, 'UTF-8' );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		echo wp_kses_post( $csv_content );
		exit;
	}
	/**
	 * Method download_guest_registrations_csv
	 *
	 * @param $month $month [explicite description]
	 *
	 * @return void
	 */
	public function download_guest_registrations_csv( $month ) {

		$start_date = gmdate( 'Y-m-01', strtotime( $month ) );  // First day of the selected month
		$end_date   = gmdate( 'Y-m-t', strtotime( $month ) );   // Last day of the selected month

		$csv_content = $this->generate_guest_registration_csv_from_reservations( $start_date, $end_date );

		if ( $csv_content ) {
			$current_date_time = gmdate( 'Y-m-d_H-i-s' );                                                  // Formats the date and time as YYYY-MM-DD_HH-MM-SS
			$filename          = 'registrations-' . esc_attr( $start_date ) . '-' . esc_attr( $end_date ) . '-on-' . esc_attr( $current_date_time ) . '.csv';

			header( 'Content-Type: text/csv; charset=utf-8' );
			header( "Content-Disposition: attachment; filename=\"$filename\"" );
			echo wp_kses_post( $csv_content );
		}

		exit;
	}
}
