<?php

namespace Staylodgic;

class Batch_Processor_Base {

	private $batch_size = 50;

	public function __construct() {
	}

	/**
	 * Check if the URL is ready for syncing based on a 15-minute interval.
	 * Returns the number of minutes left until the URL is ready for syncing, if not ready.
	 *
	 * @param string $url The URL to check.
	 * @return mixed True if the URL can be synced, or the number of minutes left if not ready.
	 */
	private function is_url_ready_for_sync( $url ) {
		$transient_key  = 'staylodgic_sync_last_time_' . md5( $url );
		$last_sync_time = get_transient( $transient_key );

		if ( false !== $last_sync_time ) {
			$elapsed_time   = time() - $last_sync_time;
			$remaining_time = 15 * MINUTE_IN_SECONDS - $elapsed_time;
			if ( $remaining_time > 0 ) {
				// Return the number of minutes left until the URL is ready for syncing.
				return ceil( $remaining_time / MINUTE_IN_SECONDS );
			}
		}

		// Update the sync time and allow syncing.
		set_transient( $transient_key, time(), 15 * MINUTE_IN_SECONDS );
		return true;
	}

	/**
	 * Method process_event_batch
	 *
	 * @return void
	 */
	public function process_event_batch(
		$room_id = false,
		$ics_url = false,
	) {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		// Create a new instance of the parser.
		$parser = new \ICal\ICal();

		$json_output = false;
		if ( ! $room_id ) {
			if ( isset( $_POST['room_id'], $_POST['ics_url'] ) ) {
				$room_id     = sanitize_text_field( wp_unslash( $_POST['room_id'] ) );
				$ics_url     = esc_url_raw( wp_unslash( $_POST['ics_url'] ) );
				$json_output = true;
			}
		}

		// Check if the URL is ready for syncing.
		$sync_check = self::is_url_ready_for_sync( $ics_url );
		if ( is_numeric( $sync_check ) ) {
			wp_send_json_error( 'Error: Syncing is not allowed yet for this URL. Please wait for ' . esc_html( $sync_check ) . ' more minutes.' );
			return;
		}

		$response = wp_remote_get( $ics_url );

		// Check for errors in the HTTP request
		if ( is_wp_error( $response ) ) {
			if ( $json_output ) {
				wp_send_json_error( 'Error: The iCal feed could not be retrieved.' );
			}
			return;
		}

		// Get the body of the response
		$file_contents = wp_remote_retrieve_body( $response );

		// Check if the feed is empty or incomplete
		if ( empty( $file_contents ) ) {
			if ( $json_output ) {
				wp_send_json_error( 'Error: The iCal feed is empty.' );
			}
			return;
		}

		if ( strpos( $file_contents, 'BEGIN:VCALENDAR' ) === false || strpos( $file_contents, 'END:VCALENDAR' ) === false ) {
			if ( $json_output ) {
				wp_send_json_error( 'Error: The iCal feed is incomplete.' );
			}
			return;
		}

		// Delete the transient if it exists.
		delete_transient( 'staylodgic_unprocessed_reservation_import' );
		$transient_used = false;
		$events         = get_transient( 'staylodgic_unprocessed_reservation_import' );

		if ( false !== $events ) {
			// The events are stored in the transient
			$transient_used = true;
		}

		// Parse the ICS file and store the events in a transient.
		$file_path     = $ics_url;
		$file_md5_hash = md5( $file_path );
		$parser->initString( $file_contents ); // Change this line
		$events = $parser->events();
		set_transient( 'staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS ); // store for 12 hours

		// Check if the events are already stored in a transient.
		$events = get_transient( 'staylodgic_unprocessed_reservation_import' );

		// Check if the events transient is empty.
		if ( ! $events ) {
			// If empty, display an error or take appropriate action.
			if ( $json_output ) {
				wp_send_json_error( 'No events found.' );
			}
		}

		if ( ! $events ) {
			// If not, parse the ICS file and store the events in a transient.
			$parser->initFile( $file_path );
			$events = $parser->events();
			set_transient( 'staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS ); // store for 12 hours
		}

		// Define the batch size.
		$batch_size = 10; // reduce batch size for testing purposes

		// Process a batch of events.
		$processed_events = array();
		for ( $i = 0; $i < $this->batch_size; $i++ ) {
			// Check if there are any more events to process.
			if ( empty( $events ) ) {
				break;
			}

			// Get the next event.
			$event = array_shift( $events );

			$description = $event->description;

			$event_data = array(); // Initialize the $event_data array

			$description_lines = explode( "\n", $description ?? '' );
			foreach ( $description_lines as $line ) {
				$parts = explode( ':', $line, 2 );
				$key   = isset( $parts[0] ) ? trim( $parts[0] ) : '';
				$value = isset( $parts[1] ) ? trim( $parts[1] ) : '';
				if ( array_key_exists( $key, $event_data ) ) {
					$event_data[ $key ] = $value;
				}

				// Extract check-in and check-out dates from the description
				if ( 'CHECKIN' === $key ) {
					// Extract date portion and remove time
					$stay_checkin_date     = gmdate( 'Y-m-d', strtotime( $value ) );
					$event_data['CHECKIN'] = $stay_checkin_date;
				} elseif ( 'CHECKOUT' === $key ) {
					// Extract date portion and remove time
					$stay_checkout_date     = gmdate( 'Y-m-d', strtotime( $value ) );
					$event_data['CHECKOUT'] = $stay_checkout_date;
				}
			}

			$checkin_date  = gmdate( 'Y-m-d', strtotime( $event->dtstart ) );
			$checkout_date = gmdate( 'Y-m-d', strtotime( $event->dtend ) );

			$processed_event = array(
				'SIGNATURE'   => $file_md5_hash,
				'CREATED'     => $event->created,
				'DTEND'       => $event->dtend,
				'DTSTART'     => $event->dtstart,
				'SUMMARY'     => $event->summary,
				'CHECKIN'     => $checkin_date,
				'CHECKOUT'    => $checkout_date,
				'UID'         => $event->uid,
				'DATA'        => $event_data,
				'DESCRIPTION' => $description,
			);

			$processed_events[] = $processed_event;
			// Update the transient with the remaining events.
			set_transient( 'staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS );
		}

		// Return the processed events and the number of remaining events.
		$response = array(
			'success' => true,
			'data'    => array(
				'processed'               => $processed_events,
				'remaining'               => count( $events ),
				'transient_used'          => $transient_used,
				'processedBookingNumbers' => array_column( $processed_events, 'UID' ),
			),
		);

		if ( $json_output ) {
			wp_send_json( $response );
		} else {
			return $response;
		}
	}
}

// Instantiate the class
$instance = new \Staylodgic\Batch_Processor_Base();
