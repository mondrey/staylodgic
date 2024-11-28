<?php

namespace Staylodgic;

use Error;

class Availability_Batch_Processor extends Batch_Processor_Base {

	private $batch_size = 50;
	private $error_urls = array();

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_availability_admin_menu' ) );
		add_action( 'wp_ajax_save_ical_availability_meta', array( $this, 'save_ical_availability_meta' ) );
		add_action( 'wp_ajax_nopriv_save_ical_availability_meta', array( $this, 'save_ical_availability_meta' ) );
		add_action( 'admin_menu', array( $this, 'add_export_availability_admin_menu' ) );

		// Add the cron hook for batch processing
		$this->add_cron_hook();

		add_action( 'init', array( $this, 'add_ics_rewrite_rule' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_ics_export' ) );
	}


	/**
	 * Method add_cron_hook
	 *
	 * @return void
	 */
	public function add_cron_hook() {
		// Hook the function to the cron event
		add_action( 'staylodgic_ical_availability_processor_event', array( $this, 'ical_availability_processor' ) );
	}

	/**
	 * Method add_availability_admin_menu
	 *
	 * @return void
	 */
	public function add_availability_admin_menu() {
		add_submenu_page(
			'staylodgic-settings',
			// This is the slug of the parent menu
			__( 'Import iCal Availabilitiy', 'staylodgic' ),
			__( 'Import iCal Availabilitiy', 'staylodgic' ),
			'edit_posts',
			'slgc-import-availability-ical',
			array( $this, 'ical_availability_import' )
		);
	}

	/**
	 * Method add_ics_rewrite_rule
	 *
	 * @return void
	 */
	public function add_ics_rewrite_rule() {
		add_rewrite_rule( '^ics-export/room/([0-9]+)/?', 'index.php?staylodgic_ics_room=$matches[1]', 'top' );
	}
	public function register_query_vars( $vars ) {
		$vars[] = 'staylodgic_ics_room';
		return $vars;
	}
	public function handle_ics_export() {
		$stay_room_id = get_query_var( 'staylodgic_ics_room' );
		if ( $stay_room_id ) {
			$this->handle_export_request( $stay_room_id );
			exit;
		}
	}

	/**
	 * Method get_current_schedule
	 *
	 * @return void
	 */
	private function get_current_schedule() {
		// Check if the event is scheduled and return its interval
		$timestamp = wp_next_scheduled( 'staylodgic_ical_availability_processor_event' );
		if ( ! $timestamp ) {
			return false; // No event scheduled
		}

		// Get the cron array
		$cron = _get_cron_array();
		if ( ! isset( $cron[ $timestamp ]['staylodgic_ical_availability_processor_event'] ) ) {
			return false;
		}

		// Extract the schedule name from the cron entry
		foreach ( $cron[ $timestamp ]['staylodgic_ical_availability_processor_event'] as $event ) {
			if ( isset( $event['schedule'] ) ) {
				return $event['schedule']; // Return the schedule name
			}
		}

		return false; // Return false if no schedule found
	}

	/**
	 * Method save_ical_availability_meta
	 *
	 * @return void
	 */
	public function save_ical_availability_meta() {
		// Perform nonce check and other validations as needed
		if ( ! isset( $_POST['ical_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ical_form_nonce'] ) ), 'ical_form_nonce' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		$room_ids           = null;
		$room_links_id      = null;
		$room_links_url     = null;
		$room_links_comment = null;

		if ( isset( $_POST['room_ical_links_id'] ) ) {
			$room_links_id = sanitize_text_field( wp_unslash( $_POST['room_ical_links_id'] ) );
		}
		if ( isset( $_POST['room_ical_links_url'] ) ) {
			$room_links_url = esc_url_raw( wp_unslash( $_POST['room_ical_links_url'] ) );
		}
		if ( isset( $_POST['room_ical_links_comment'] ) ) {
			$room_links_comment = sanitize_textarea_field( wp_unslash( $_POST['room_ical_links_comment'] ) );
		}
		if ( isset( $_POST['room_ids'] ) ) {
			$room_ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['room_ids'] ) );

			$room_ids_count = count( $room_ids ); // Assign the count to a variable

			for ( $i = 0; $i < $room_ids_count; $i++ ) {
				$room_id    = $room_ids[ $i ];
				$room_links = array();

				$old_room_data = get_post_meta( $room_id, 'staylodgic_channel_quantity_array', true );

				// Ensure that $room_links_url[$i] is an array before trying to count its elements
				if ( isset( $room_links_url[ $i ] ) && is_array( $room_links_url[ $i ] ) ) {

					$room_links_count = count( $room_links_url[ $i ] ); // Assign the count to a variable

					for ( $j = 0; $j < $room_links_count; $j++ ) {

						// Get the old room data
						$old_room_links = get_post_meta( $room_id, 'staylodgic_availability_ical_data', true );

						// Check if the URL is valid
						if ( filter_var( $room_links_url[ $i ][ $j ], FILTER_VALIDATE_URL ) ) {
							// Check if a unique ID is already assigned

							if ( ! isset( $room_links_id[ $i ][ $j ] ) || '' == $room_links_id[ $i ][ $j ] ) {
								$room_links_id[ $i ][ $j ] = uniqid();
							}

							$file_md5_hash = md5( esc_url( $room_links_url[ $i ][ $j ] ) );

							// Check if the URL is the same as before
							$ical_synced = false;
							if ( isset( $old_room_links[ $file_md5_hash ] ) && $old_room_links[ $file_md5_hash ]['ical_url'] == $room_links_url[ $i ][ $j ] ) {
								$ical_synced = $old_room_links[ $file_md5_hash ]['ical_synced'];
							}

							$room_links[ $file_md5_hash ] = array(
								'ical_id'      => sanitize_text_field( $room_links_id[ $i ][ $j ] ),
								'ical_synced'  => $ical_synced,
								'ical_url'     => esc_url( $room_links_url[ $i ][ $j ] ),
								'ical_comment' => sanitize_text_field( $room_links_comment[ $i ][ $j ] ),
							);
						}
					}
				}

				// Update the meta field in the database.
				update_post_meta( $room_id, 'staylodgic_availability_ical_data', $room_links );
				delete_post_meta( $room_id, 'staylodgic_channel_quantity_array', null );
			}

			// Sync on Save processing full batch ( process intensive )
			$this->ical_availability_processor();

			if ( ! wp_next_scheduled( 'continue_ical_availability_processing' ) ) {
				wp_schedule_single_event( time(), 'continue_ical_availability_processing' );
			}

			wp_send_json_success( 'Successfully stored and batch processing initiated' );
		} else {
			wp_send_json_success( 'Room data not found' );
		}
	}

	/**
	 * Method are_calendars_configured
	 *
	 * @return void
	 */
	public function are_calendars_configured() {
		$rooms = Rooms::query_rooms();
		foreach ( $rooms as $room ) {
			$room_ical_data = get_post_meta( $room->ID, 'staylodgic_availability_ical_data', true );
			if ( ! empty( $room_ical_data ) ) {
				// Calendar URLs exist
				return true;
			}
		}
		// No calendar URLs set
		return false;
	}

	/**
	 * Checks if the syncing process is currently in progress.
	 *
	 * @return bool Returns true if syncing is in progress, false otherwise.
	 */
	public function is_syncing_in_progress() {
		// First, check if calendars are configured
		if ( ! $this->are_calendars_configured() ) {
			// If no calendars are configured, syncing is not in progress
			return false;
		}
		// Then check the is_syncing flag
		return get_option( 'is_syncing', false );
	}

	/**
	 * Method ical_availability_processor
	 *
	 * @return void
	 */
	public function ical_availability_processor() {
		$rooms               = Rooms::query_rooms();
		$count               = 0;
		$changed_date_ranges = array();

		foreach ( $rooms as $room ) {

			$room_ical_data = get_post_meta( $room->ID, 'staylodgic_availability_ical_data', true );

			if ( is_array( $room_ical_data ) && count( $room_ical_data ) > 0 ) {
				foreach ( $room_ical_data as $ical_link ) {
					$ics_url = $ical_link['ical_url'];
					$ics_id  = $ical_link['ical_id'];

					$start_process_time = microtime( true ); // Start time measurement

					$file_ok = true;
					// Use wp_remote_get instead of file_get_contents
					$response = wp_remote_get( $ics_url );

					// Check for errors in the request
					if ( is_wp_error( $response ) ) {
						$file_ok       = false;
						$file_contents = ''; // Set empty content if there's an error
					} else {
						// Retrieve the content from the response body
						$file_contents = wp_remote_retrieve_body( $response );
					}

					// Check if the feed is empty or incomplete
					if ( empty( $file_contents ) || strpos( $file_contents, 'BEGIN:VCALENDAR' ) === false || strpos( $file_contents, 'END:VCALENDAR' ) === false ) {
						$file_ok = false;
					}

					$blocked_dates['ical'][ $room->ID ]['quantity'] = self::process_availability_link( array(), $room->ID, $ics_url, $file_contents );
					$end_process_time                               = microtime( true ); // End time measurement
					$elapsed_process_time                           = $end_process_time - $start_process_time; // Calculate elapsed time
					// Process and save/update $blocked_dates as required
					$blocked_dates['ical'][ $room->ID ]['stats'][ $ics_id ]['syncdate']            = gmdate( 'Y-m-d' );
					$blocked_dates['ical'][ $room->ID ]['stats'][ $ics_id ]['synctime']            = gmdate( 'H:i:s' );
					$blocked_dates['ical'][ $room->ID ]['stats'][ $ics_id ]['file_ok']             = $file_ok; // Store elapsed time
					$blocked_dates['ical'][ $room->ID ]['stats'][ $ics_id ]['syncprocessing_time'] = $elapsed_process_time; // Store elapsed time
				}
			}

			if ( isset( $blocked_dates['ical'][ $room->ID ] ) ) {
				update_post_meta( $room->ID, 'staylodgic_channel_quantity_array', $blocked_dates['ical'][ $room->ID ] );
			}
		}
	}

	/**
	 * Method process_availability_link
	 *
	 * @return void
	 */
	public function process_availability_link(
		$blocked_dates = array(),
		$room_id = false,
		$ics_url = false,
		$file_contents = false
	) {
		// Create a new instance of the parser.
		$parser = new \ICal\ICal();

		// Parse the ICS file
		$parser->initString( $file_contents );
		$events = $parser->events();

		foreach ( $events as $event ) {
			// Extract start and end dates
			$start_date = new \DateTime( $event->dtstart );
			$end_date   = new \DateTime( $event->dtend );

			// Iterate over the date range
			while ( $start_date < $end_date ) {
				// Add date to blocked dates, excluding the end date (checkout date)
				$blocked_dates[ $start_date->format( 'Y-m-d' ) ] = '0';
				$start_date->modify( '+1 day' );
			}
		}

		// Return the array of blocked dates
		return $blocked_dates;
	}

	/**
	 * Method ical_availability_import
	 *
	 * @return void
	 */
	public function ical_availability_import() {

		// The HTML content of your 'Import iCal' page goes here
		echo '<div class="expor-import-calendar">';
		echo '<div class="main-sync-form-wrap">';
		echo '<div id="sync-form">';
		echo '<h1>' . esc_html__( 'Sync Your Calendar', 'staylodgic' ) . '</h1>';
		echo '<p>' . esc_html__( 'Keep your bookings up-to-date. Connect your iCalendar feeds to synchronize your booking availability with your StayLodgic calendar. Simply enter the URLs for the calendars you wish to sync below.', 'staylodgic' ) . '</p>';
		echo '<div class="how-to-admin">';
		echo '<h2>' . esc_html__( 'How to Sync:', 'staylodgic' ) . '</h2>';
		echo '<ol>';
		echo '<li>' . esc_html__( 'Find your iCalendar URL from the external booking platform or calendar service.', 'staylodgic' ) . '</li>';
		echo '<li>' . esc_html__( 'Enter the iCalendar URL in the input field below.', 'staylodgic' ) . '</li>';
		echo '<li>' . esc_html__( 'Set a label for your reference.', 'staylodgic' ) . '</li>';
		echo '</ol>';
		echo '</div>';

		echo "<form id='room_ical_form' method='post'>";
		echo '<input type="hidden" name="ical_form_nonce" value="' . esc_attr( wp_create_nonce( 'ical_form_nonce' ) ) . '">';

		$rooms = Rooms::query_rooms();
		foreach ( $rooms as $room ) {
			// Get meta
			$room_ical_data            = get_post_meta( $room->ID, 'staylodgic_availability_ical_data', true );
			$room_channel_availability = get_post_meta( $room->ID, 'staylodgic_channel_quantity_array', true );

			echo '<div class="room_ical_links_wrapper" data-room-id="' . esc_attr( $room->ID ) . '">';
			echo '<div class="import-export-heading">' . esc_html( $room->post_title ) . '</div>';
			if ( is_array( $room_ical_data ) && count( $room_ical_data ) > 0 ) {
				foreach ( $room_ical_data as $ical_id => $ical_link ) {

					if ( isset( $room_ical_data[ $ical_id ] ) ) {
						$ical_synced = $room_ical_data[ $ical_id ]['ical_synced'];
					}

					echo '<div class="room_ical_link_group input-group">';
					echo '<input readonly hidden type="text" name="room_ical_links_id[]" value="' . esc_attr( $ical_link['ical_id'] ) . '">';
					echo '<span class="input-group-text">' . esc_html__( 'url', 'staylodgic' ) . '</span>';
					echo '<input readonly class="form-control" type="url" name="room_ical_links_url[]" value="' . esc_attr( $ical_link['ical_url'] ) . '">';
					echo '<span class="input-group-text">' . esc_html__( 'Label', 'staylodgic' ) . '</span>';
					echo '<input readonly class="form-control" type="text" name="room_ical_links_comment[]" value="' . esc_attr( $ical_link['ical_comment'] ) . '">';
					echo '<button type="button" class="unlock_button btn btn-warning"><i class="fas fa-lock"></i></button>'; // Unlock button
					echo '</div>';
					if ( is_array( $room_channel_availability ) && isset( $room_channel_availability['stats'] ) ) {
						foreach ( $room_channel_availability['stats'] as $key => $value ) {
							// Check if the key matches the pattern or criteria you're looking for
							if ( $key === $ical_link['ical_id'] ) {

								$sync_date = $room_channel_availability['stats'][ $key ]['syncdate'];
								$sync_time = $room_channel_availability['stats'][ $key ]['synctime'];
								$timezone  = staylodgic_get_option( 'timezone' );

								$adjusted_values = staylodgic_apply_timezone_to_date_and_time( $sync_date, $sync_time, $timezone );

								echo '<div class="availability-sync-stats">';
								echo '<span>' . esc_html__( 'Last sync: ', 'staylodgic' ) . esc_html( $adjusted_values['adjustedDate'] ) . '</span>';
								echo '<span>' . esc_html__( 'Time: ', 'staylodgic' ) . esc_html( $adjusted_values['adjustedTime'] ) . '</span>';
								echo '<span>' . esc_html__( 'Processed in ( seconds ): ', 'staylodgic' ) . esc_html( $room_channel_availability['stats'][ $key ]['syncprocessing_time'] ) . '</span>';
								if ( ! $room_channel_availability['stats'][ $key ]['file_ok'] ) {
									echo '<span class="file-error">' . esc_html__( 'File error', 'staylodgic' ) . '</span>';
								}
								echo '</div>';
								break;
							}
						}
					}
				}
			} else {
				echo '<div class="room_ical_link_group input-group">';
				echo '<span class="input-group-text">' . esc_html__( 'url', 'staylodgic' ) . '</span>';
				echo '<input aria-label="url" class="form-control" type="url" name="room_ical_links_url[]">';
				echo '<span class="input-group-text">' . esc_html__( 'Label', 'staylodgic' ) . '</span>';
				echo '<input aria-label="Label" class="form-control" type="text" name="room_ical_links_comment[]">';
				echo '</div>';
			}
			echo '<button type="button" class="add_more_ical button button-secondary button-small">' . esc_html__( 'Add more', 'staylodgic' ) . '</button>';
			echo '</div>';
		}
		echo '<button data-type="sync-availability" class="btn btn-primary" type="button" id="save_all_ical_rooms">';
		echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
		echo '<span role="status"> ' . esc_html__( 'Save All', 'staylodgic' ) . '</span>';
		echo '</button>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Method add_export_availability_admin_menu
	 *
	 * @return void
	 */
	public function add_export_availability_admin_menu() {
		add_submenu_page(
			'staylodgic-settings', // Replace with the slug of the parent menu item
			__( 'Export iCal Availability', 'staylodgic' ),
			__( 'Export iCal Availability', 'staylodgic' ),
			'edit_posts',
			'slgc-export-availability-ical',
			array( $this, 'export_availability_ical_page' )
		);
	}

	/**
	 * Method export_availability_ical_page
	 *
	 * @return void
	 */
	public function export_availability_ical_page() {

		// The HTML content of your 'Import iCal' page goes here
		echo '<div class="expor-import-calendar main-sync-form-wrap">';
		echo '<div id="export-import-form">';
		echo '<h1>' . esc_html__( 'iCal Feeds', 'staylodgic' ) . '</h1>';
		echo '<p>' . esc_html__( 'Synchronize availability for your rooms with other softwares using iCal feeds.', 'staylodgic' ) . '</p>';
		echo '<div class="how-to-admin">';
		echo '<h2>' . esc_html__( 'How to:', 'staylodgic' ) . '</h2>';
		echo '<ol>';
		echo '<li>' . esc_html__( 'Copy the url for the room.', 'staylodgic' ) . '</li>';
		echo '<li>' . esc_html__( 'Enter it to the software which is compatible with iCal feeds for availability.', 'staylodgic' ) . '</li>';
		echo '</ol>';
		echo '</div>';

		$rooms = Rooms::query_rooms();
		foreach ( $rooms as $room ) {
			// Get meta
			$room_ical_data = get_post_meta( $room->ID, 'staylodgic_availability_ical_data', true );

			echo '<div class="room_ical_links_wrapper" data-room-id="' . esc_attr( $room->ID ) . '">';
			echo '<div class="import-export-heading">' . esc_html( $room->post_title ) . '</div>';

			echo '<div class="room_ical_link_group">';
			// The URL to trigger the export functionality
			$export_url = home_url( '/ics-export/room/' ) . esc_attr( $room->ID );

			$export_url .= '&room=' . esc_attr( $room->ID );

			// Page content
			echo "<div class='export-ical-wrap input-group'>";
			echo "<input class='form-control urlField' type='text' value='" . esc_url( $export_url ) . "' readonly>";
			echo '<div class="btn btn-outline-secondary copy-url-button" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' . esc_html__( 'Copy to clipboard', 'staylodgic' ) . '"><i class="fa-solid fa-copy"></i></div>';
			echo '</div>';

			echo '</div>';
			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Method handle_export_request
	 *
	 * @param $stay_room_id $stay_room_id [explicite description]
	 *
	 * @return void
	 */
	public function handle_export_request( $stay_room_id ) {

		// Retrieve the 'staylodgic_quantity_array' and 'staylodgic_channel_quantity_array'
		$room_reservations_instance = new \Staylodgic\Reservations( $stay_date_string = false, $stay_room_id );
		$room_reservations_instance->calculate_and_update_remaining_room_counts_for_all_dates();
		$remaining_quantity_array = $room_reservations_instance->get_remaining_room_count_array();

		$channel_array          = get_post_meta( $stay_room_id, 'staylodgic_channel_quantity_array', true );
		$channel_quantity_array = isset( $channel_array['quantity'] ) ? $channel_array['quantity'] : array();

		$new_remaining_quantity_array = $this->filterFutureDates( $remaining_quantity_array );
		// Merge the arrays - channel_quantity_array values will overwrite remaining_quantity_array values for any matching keys
		$merged_array = array_merge( $new_remaining_quantity_array, $channel_quantity_array );

		// Determine if the request is coming from a browser or a server
		$mode = $this->detect_request_mode();

		// Generate the .ics file with the merged array
		$this->generate_ics_file( $stay_room_id, $merged_array, $mode );

		exit;
	}

	/**
	 * Method filterFutureDates
	 *
	 * @param $remaining_quantity_array $remaining_quantity_array [explicite description]
	 *
	 * @return void
	 */
	public function filterFutureDates( $remaining_quantity_array ) {
		$filtered_array    = array();
		$stay_current_date = gmdate( 'Y-m-d' );

		foreach ( $remaining_quantity_array as $date => $quantity ) {
			if ( $date >= $stay_current_date ) {
				$filtered_array[ $date ] = $quantity;
			}
		}

		return $filtered_array;
	}

	/**
	 * Method detect_request_mode
	 *
	 * @return void
	 */
	private function detect_request_mode() {
		// Check for certain server variables typical in browser requests
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			// Likely a browser request
			return 'download';
		}
		// Default to server mode for API calls, scripts, etc.
		return 'server';
	}

	/**
	 * Method generate_ics_file
	 *
	 * @param $stay_room_id $stay_room_id [explicite description]
	 * @param $stay_quantity_array $stay_quantity_array [explicite description]
	 * @param $mode $mode [explicite description]
	 *
	 * @return void
	 */
	private function generate_ics_file( $stay_room_id, $stay_quantity_array, $mode ) {
		// Start of the ICS file
		$ics_content  = "BEGIN:VCALENDAR\r\n";
		$ics_content .= "VERSION:2.0\r\n";
		$ics_content .= "PRODID:-//Your Company//Your Calendar//EN\r\n";

		// Iterate over the quantity array
		foreach ( $stay_quantity_array as $date => $quantity ) {
			if ( 0 === (int) $quantity ) {
				// Format the date for ICS
				$ics_date     = new \DateTime( $date );
				$ics_date_str = $ics_date->format( 'Ymd' );

				// Create a copy of the date and add one day for DTEND
				$ics_end_date = clone $ics_date;
				$ics_end_date->modify( '+1 day' );
				$ics_end_date_str = $ics_end_date->format( 'Ymd' );

				$ics_readable_date = $ics_date->format( 'Y-m-d' );

				// Create an event for the unavailable date
				$ics_content .= "BEGIN:VEVENT\r\n";
				$ics_content .= 'UID:' . uniqid() . "@staylodgic\r\n";
				$ics_content .= 'DTSTAMP:' . gmdate( 'Ymd' ) . 'T' . gmdate( 'His' ) . "Z\r\n";
				$ics_content .= 'DTSTART;VALUE=DATE:' . esc_html( $ics_date_str ) . "\r\n";
				$ics_content .= 'DTEND;VALUE=DATE:' . esc_html( $ics_end_date_str ) . "\r\n";
				$ics_content .= "SUMMARY:Unavailable\r\n";
				$ics_content .= 'DESCRIPTION:Unavailable on ' . esc_html( $ics_readable_date ) . "\r\n";
				$ics_content .= "STATUS:CONFIRMED\r\n";
				$ics_content .= "END:VEVENT\r\n";
			}
		}

		// End of the ICS file
		$ics_content .= 'END:VCALENDAR';

		if ( 'server' === $mode ) {
			// Output the .ics content directly for server-to-server requests
			echo wp_kses_post( $ics_content );
		} else {
			// Set headers for .ics file download for user requests
			header( 'Content-Type: text/calendar; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename="room-' . esc_attr( $stay_room_id ) . '-availability.ics"' );
			echo wp_kses_post( $ics_content );
		}
	}
}

// Instantiate the class
$availability_batch_processor = new \Staylodgic\Availability_Batch_Processor();
