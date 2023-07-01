<?php
namespace AtollMatrix;
class EventBatchProcessor {
	private $batchSize = 50;

	public function __construct() {
		add_action('wp_ajax_process_event_batch', array($this, 'process_event_batch')); // wp_ajax_ hook for logged-in users
		add_action('wp_ajax_nopriv_process_event_batch', array($this, 'process_event_batch')); // wp_ajax_nopriv_ hook for non-logged-in users
		add_action('admin_menu', array($this, 'add_admin_menu')); // This now points to the add_admin_menu function
		add_action('wp_ajax_insert_events_batch', array($this, 'insert_events_batch'));
		add_action('wp_ajax_nopriv_insert_events_batch', array($this, 'insert_events_batch'));

		// ...
		add_action('wp_ajax_save_ical_room_meta', array($this, 'save_ical_room_meta'));
		add_action('wp_ajax_nopriv_save_ical_room_meta', array($this, 'save_ical_room_meta'));

		add_action('wp_ajax_find_future_cancelled_reservations',  array($this, 'find_future_cancelled_reservations'));
		add_action('wp_ajax_nopriv_find_future_cancelled_reservations',  array($this, 'find_future_cancelled_reservations'));

	}

	public function find_future_cancelled_reservations() {

		$processRoomID = $_POST['room_id'];
		$processICS_ID = $_POST['ics_id'];

		// error_log( print_r($_POST, true) );
		// // Extract UIDs from processedEvents
		$processedUIDs = array_map(function($event) {
			return $event['UID'];
		}, $_POST['processedEvents']);

	// To Test - Remove records from the processedUIDs array as a test to see cancellations are reported
	// if (!empty($processedUIDs)) {
	// 	$removedUID = array_pop($processedUIDs);
	// 	$removedUID = array_pop($processedUIDs);
	// 	$removedUID = array_pop($processedUIDs);
	// }

	
		// // Get the signature from the $_POST data
		$signature = $_POST['signature'];
	
		// Get today's date
		$today = date('Y-m-d');
	
		// Run a query for reservation posts
		$args = array(
			'post_type'  => 'atmx_reservations',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'atollmatrix_ics_signature',
					'value' => $signature,
				),
				array(
					'key'     => 'atollmatrix_checkin_date',
					'value'   => $today,
					'compare' => '>=',  // check-in date is today or in the future
					'type'    => 'DATE'
				)
			)
		);
		$query = new \WP_Query($args);
	
		$potentiallyCancelled = [];
	
		// If the query has posts
		if ($query->have_posts()) {
			// For each post in the query
			while ($query->have_posts()) {
				// Move the internal pointer
				$query->the_post();
	
				// Get the post meta
				$booking_number = get_post_meta(get_the_ID(), 'atollmatrix_booking_number', true);
	
				// If the booking number doesn't exist in processed UIDs, it's potentially cancelled
				if (!in_array($booking_number, $processedUIDs)) {
					$potentiallyCancelled[] = $booking_number;
				}
			}
	
			// Restore original Post Data
			wp_reset_postdata();
		}
	
		// Prepare the response data
		$responseData = array(
			'success' => true,
			'cancelledReservations' => $potentiallyCancelled,
			'icsID' => $processICS_ID,
		);

		$room_ical_data = get_post_meta($processRoomID, 'room_ical_data', true);

		if (isset($room_ical_data[$processICS_ID])) {
			$room_ical_data[$processICS_ID]['ical_synced'] = true;
		}
		
		//error_log( print_r( $room_ical_data, true ) );
		update_post_meta( $processRoomID, 'room_ical_data', $room_ical_data );
	
		// Send the JSON response
		wp_send_json_success($responseData);
	}
	

	public function insert_events_batch() {
		// Get the processed events data from the request
		$processedEvents = $_POST['processedEvents'];
		$processRoomID   = $_POST['room_id'];
		$processICS_URL  = $_POST['ics_url'];
		$processICS_ID   = $_POST['ics_id'];

		$get_ICS_array = get_post_meta( get_the_ID(), 'room_ical_links', true );

		$successCount = 0; // Counter for successfully inserted posts
		$skippedCount = 0; // Counter for skipped posts
	
		// Loop through the processed events and insert posts
		foreach ($processedEvents as $event) {
			$booking_number = $event['UID'];
			$checkin        = $event['DATA']['CHECKIN'];
			$checkout       = $event['DATA']['CHECKOUT'];
			$name           = $event['SUMMARY'];
			$description    = $event['DESCRIPTION'];
			$signature      = $event['SIGNATURE'];
			
			$room_id = $_POST['room_id'];
			$ics_url = $_POST['ics_url'];

			$existing_post = get_posts(array(
				'post_type' => 'atmx_reservations',
				'meta_query' => array(
					array(
						'key' => 'atollmatrix_booking_number',
						'value' => $booking_number,
					),
				),
			));
	
			// If an existing post is found, skip inserting a new post
			if ($existing_post) {
				$skippedCount++;
				continue;
			}
	
			$post_data = array(
				'post_type'     => 'atmx_reservations',  // Your custom post type
				'post_title'    => $booking_number,  // Set the booking number as post title
				'post_status'   => 'publish',       // The status you want to give new posts
				'meta_input'    => array(
					'atollmatrix_room_id'            => $room_id,
					'atollmatrix_reservation_status' => 'confirmed',
					'atollmatrix_checkin_date'       => $checkin,
					'atollmatrix_checkout_date'      => $checkout,
					'atollmatrix_booking_number'     => $booking_number,   // Set the booking number as post meta
					'atollmatrix_full_name'          => $name,             // Customer name
					'atollmatrix_reservation_notes'  => $description,      // Description
					'atollmatrix_ics_signature'      => $signature,        // ICS File hash
					'atollmatrix_customer_choice'    => 'new',             // ICS File hash
					// add other meta data you need
				),
			);
			// Insert the post
			$post_id = wp_insert_post($post_data);
			// $post_id = true;
	
			if ($post_id) {
				$successCount++;
			}
		}
	
		// Prepare the response data
		$responseData = array(
			'success' => true,
			'successCount' => $successCount,
			'skippedCount' => $skippedCount,
			'icsID' => $processICS_ID
		);
	
		// Send the JSON response
		wp_send_json_success($responseData);
	}

	public function add_admin_menu() {
		add_menu_page(
			'Atoll Matrix',
			'Atoll Matrix',
			'manage_options',
			'atoll-matrix',
			array($this, 'display_main_page')
		);

		add_submenu_page(
			'atoll-matrix', // This is the slug of the parent menu
			'Import iCal',
			'Import iCal',
			'manage_options',
			'import-ical',
			array($this, 'display_admin_page')
		);
	}

	public function display_main_page() {
		// The HTML content of the 'Atoll Matrix' page goes here
		echo "<h1>Welcome to Atoll Matrix</h1>";
	}

	public function display_admin_page() {
		// The HTML content of your 'Import iCal' page goes here
		echo "<div class='main-sync-form-wrap'>";
		echo "<div id='result'></div>";
		echo "<div id='sync-form'>";
		echo "<h1>Welcome to Atoll Matrix</h1>";
	
		echo "<form id='room_ical_form' method='post'>";
		echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';
	
		$rooms = Rooms::queryRooms();
		foreach($rooms as $room) {
			// Get meta
			$room_ical_data = get_post_meta( $room->ID, 'room_ical_data', true );

			echo '<div class="room_ical_links_wrapper" data-room-id="' . $room->ID . '">';
			echo "<h2>" . $room->post_title . "</h2>";
			if(is_array($room_ical_data) && count($room_ical_data) > 0){
				foreach ($room_ical_data as $ical_id => $ical_link) {

					if (isset($room_ical_data[$ical_id])) {
						$ical_synced = $room_ical_data[$ical_id]['ical_synced'];
					}

					echo '<div class="room_ical_link_group">';
					echo '<input readonly type="text" name="room_ical_links_id[]" value="' . esc_attr($ical_link['ical_id']) . '">';
					echo '<input readonly type="url" name="room_ical_links_url[]" value="' . esc_attr($ical_link['ical_url']) . '">';
					echo '<input readonly type="text" name="room_ical_links_comment[]" value="' . esc_attr($ical_link['ical_comment']) . '">';
					echo '<button type="button" class="unlock_button"><i class="fas fa-lock"></i></button>'; // Unlock button
					
					$button_save_mode = 'Active';
					if ( !$ical_synced ) {
						$button_save_mode = 'Sync to Activate';
					}
					
					echo '<button type="button" class="sync_button" data-ics-id="' . esc_attr($ical_id) . '" data-ics-url="' . esc_attr($ical_link['ical_url']) . '" data-room-id="' . esc_attr($room->ID) . '">'.$button_save_mode.'</button>'; // Sync button
					echo '</div>';
				}
			} else {
				echo '<div class="room_ical_link_group">';
				echo '<input type="url" name="room_ical_links_url[]">';
				echo '<input type="text" name="room_ical_links_comment[]">';
				echo '</div>';
			}
			echo '<button type="button" class="add_more_ical">Add more</button>';
			echo '</div>';
		}
	
		echo '<input type="submit" id="save_all_ical_rooms" value="Save">';
		echo "</form>";
		echo "</div>";
		echo "</div>";
	}
	
	public function save_ical_room_meta() {
		// Perform nonce check and other validations as needed
		// ...
		if(!isset($_POST['ical_form_nonce']) || !wp_verify_nonce($_POST['ical_form_nonce'], 'ical_form_nonce')) {
			wp_send_json_error('Invalid nonce');
		}
	
		$room_ids           = $_POST['room_ids'];
		$room_links_id      = $_POST['room_ical_links_id'];
		$room_links_url     = $_POST['room_ical_links_url'];
		$room_links_comment = $_POST['room_ical_links_comment'];


		//error_log( print_r( $_POST , true ) );
		for ($i = 0; $i < count($room_ids); $i++) {
			$room_id = $room_ids[$i];
			$room_links = array();
		
			// Ensure that $room_links_url[$i] is an array before trying to count its elements
			if (isset($room_links_url[$i]) && is_array($room_links_url[$i])) {
				for ($j = 0; $j < count($room_links_url[$i]); $j++) {

					// Get the old room data
					$old_room_links = get_post_meta($room_id, 'room_ical_data', true);

					// Check if the URL is valid
					if (filter_var($room_links_url[$i][$j], FILTER_VALIDATE_URL)) {
						// Check if a unique ID is already assigned
						if ( '' == $room_links_id[$i][$j] ) {
							$room_links_id[$i][$j] = uniqid();
						}

						$file_md5Hash = md5( sanitize_url($room_links_url[$i][$j]) );

						// Check if the URL is the same as before
						$ical_synced = false;
						if (isset($old_room_links[$file_md5Hash]) && $old_room_links[$file_md5Hash]['ical_url'] == $room_links_url[$i][$j]) {
							$ical_synced = $old_room_links[$file_md5Hash]['ical_synced'];
						}

						$room_links[$file_md5Hash] = array(
							'ical_id'      => sanitize_text_field($room_links_id[$i][$j]),
							'ical_synced'  => $ical_synced,
							'ical_url'     => sanitize_url($room_links_url[$i][$j]),
							'ical_comment' => sanitize_text_field($room_links_comment[$i][$j]),
						);
					}
				}
			}
		
			// Update the meta field in the database.
			update_post_meta($room_id, 'room_ical_data', $room_links);
		}		
	
		// You can return a success response here
		wp_send_json_success('Successfully stored');
	}

	public function process_event_batch() {

		// Create a new instance of the parser.
		$parser = new \ICal\ICal();

		$room_id = $_POST['room_id'];
		$ics_url = $_POST['ics_url'];

		$file_contents = file_get_contents($ics_url);
		// Check if the feed is empty or incomplete
		if ($file_contents === false || empty($file_contents)) {
			wp_send_json_error('Error: The iCal feed is empty or could not be retrieved.');
			return;
		}
		if (strpos($file_contents, 'BEGIN:VCALENDAR') === false || strpos($file_contents, 'END:VCALENDAR') === false) {
			wp_send_json_error('Error: The iCal feed is incomplete.');
			return;
		}

		// Delete the transient if it exists.
		delete_transient('atollmatrix_unprocessed_reservation_import');
		$transient_used = false;
		if (false !== ($events = get_transient('atollmatrix_unprocessed_reservation_import'))) {
			// The events are stored in the transient
			$transient_used = true;
		}

		// Parse the ICS file and store the events in a transient.
		$file_path    = $ics_url;
		$file_md5Hash = md5($file_path);
		$parser->initString($file_contents);  // Change this line
		$events = $parser->events();
		set_transient('atollmatrix_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS); // store for 12 hours

		// Check if the events are already stored in a transient.
		$events = get_transient('atollmatrix_unprocessed_reservation_import');

		// Check if the events transient is empty.
		if (!$events) {
			// If empty, display an error or take appropriate action.
			wp_send_json_error('No events found.');
		}


		if (!$events) {
			// If not, parse the ICS file and store the events in a transient.
			$parser->initFile($file_path);
			$events = $parser->events();
			set_transient('atollmatrix_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS); // store for 12 hours
		}
		

		// Define the batch size.
		$batchSize = 10; // reduce batch size for testing purposes
	
		// Process a batch of events.
		$processedEvents = [];
		for ($i = 0; $i < $this->batchSize; $i++) {
			// Check if there are any more events to process.
			if (empty($events)) {
				break;
			}
										
			// Get the next event.
			$event = array_shift($events);

			$description = $event->description;

			$eventData = []; // Initialize the $eventData array
			
			$descriptionLines = explode("\n", $description);
			foreach ($descriptionLines as $line) {
				$parts = explode(':', $line, 2);
				$key   = isset($parts[0]) ? trim($parts[0]) : '';
				$value = isset($parts[1]) ? trim($parts[1]) : '';
				if (array_key_exists($key, $eventData)) {
					$eventData[$key] = $value;
				}
			
				// Extract check-in and check-out dates from the description
				if ($key === 'CHECKIN') {
					// Extract date portion and remove time
					$checkinDate = date('Y-m-d', strtotime($value));
					$eventData['CHECKIN'] = $checkinDate;
				} elseif ($key === 'CHECKOUT') {
					// Extract date portion and remove time
					$checkoutDate = date('Y-m-d', strtotime($value));
					$eventData['CHECKOUT'] = $checkoutDate;
				}
			}

			$checkin_date = date('Y-m-d', strtotime($event->dtstart));
			$checkout_date = date('Y-m-d', strtotime($event->dtend));
			
			$processedEvent = [
				'SIGNATURE'   => $file_md5Hash,
				'CREATED'     => $event->created,
				'DTEND'       => $event->dtend,
				'DTSTART'     => $event->dtstart,
				'SUMMARY'     => $event->summary,
				'CHECKIN'     => $checkin_date,
				'CHECKOUT'    => $checkout_date,
				'UID'         => $event->uid,
				'DATA'        => $eventData,
				'DESCRIPTION' => $description
			];
			

			$processedEvents[] = $processedEvent;
			// Update the transient with the remaining events.
			set_transient('atollmatrix_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS);
		}
	
		// Return the processed events and the number of remaining events.
		$response = array(
			'success' => true,
			'data' => array(
				'processed'               => $processedEvents,
				'remaining'               => count($events),
				'transient_used'          => $transient_used,
				'processedBookingNumbers' => array_column($processedEvents, 'UID'),
			),
		);
		wp_send_json($response);
	}
}

// Instantiate the class
$eventBatchProcessor = new EventBatchProcessor();
