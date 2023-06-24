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
	}

	function insert_events_batch() {
		// Get the processed events data from the request
		$processedEvents = $_POST['processedEvents'];
	
		$successCount = 0; // Counter for successfully inserted posts
	
		// Loop through the processed events and insert posts
		foreach ($processedEvents as $event) {
			$booking_number = $event['UID'];
			$checkin = $event['DATA']['CHECKIN'];
			$checkout = $event['DATA']['CHECKOUT'];
			$name = $event['SUMMARY'];
			$description = $event['DESCRIPTION'];
			$ics_filepath = $event['ICSFILEPATH'];
			$ics_filehash = $event['ICSFILEHASH'];
			
			$room_id = '542';

			// Check if a post with the same booking number already exists
			$existing_post = get_posts(array(
				'post_type' => 'reservations',
				'meta_query' => array(
					array(
						'key' => 'atollmatrix_booking_number',
						'value' => $booking_number,
					),
				),
			));

			// If an existing post is found, skip inserting a new post
			if ($existing_post) {
				continue;
			}
	
			$post_data = array(
				'post_type'     => 'reservations',  // Your custom post type
				'post_title'    => $booking_number,  // Set the booking number as post title
				'post_status'   => 'publish',       // The status you want to give new posts
				'meta_input'    => array(
					'atollmatrix_room_id' => $room_id,
					'atollmatrix_reservation_status' => 'confirmed',
					'atollmatrix_checkin_date' => $checkin,
					'atollmatrix_checkout_date' => $checkout,
					'atollmatrix_booking_number' => $booking_number,  // Set the booking number as post meta
					'atollmatrix_full_name' => $name,  // Customer name
					'atollmatrix_reservation_notes' => $description,  // Description
					'atollmatrix_ics_filepath' => $ics_filepath,  // ICS File path
					'atollmatrix_ics_filehash' => $ics_filehash,  // ICS File hash
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
		echo "<h1>Import iCal</h1>";
		echo "<form id='import-ical-form' method='post' enctype='multipart/form-data'>";
		echo "<input type='file' name='ics_file' accept='.ics'>";
		echo "<input type='submit' value='Upload'>";
		echo "</form>";
		echo "<input type='button' id='process-events' value='Process Events'>";
		echo "<div id='result'></div>";
	}

	public function process_event_batch() {

		// Create a new instance of the parser.
		$parser = new \ICal\ICal();

		// Delete the transient if it exists.
		delete_transient('unprocessed_events');
		delete_transient('atollmatrix_unprocessed_reservation_import');

		// Parse the ICS file and store the events in a transient.
		$file_path = plugin_dir_path(__FILE__) . 'calendar.ics';
		$file_md5Hash = md5($file_path);
		$parser->initFile($file_path);
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
			$file_path = plugin_dir_path(__FILE__) . 'calendar.ics';
			$file_md5Hash = md5($file_path);
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
				$key = isset($parts[0]) ? trim($parts[0]) : '';
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
				'CREATED' => $event->created,
				'DTEND' => $event->dtend,
				'DTSTART' => $event->dtstart,
				'SUMMARY' => $event->summary,
				'CHECKIN' => $checkin_date,
				'CHECKOUT' => $checkout_date,
				'UID' => $event->uid,
				'DATA' => $eventData,
				'DESCRIPTION' => $description,
				'ICSFILEPATH' => $file_path,
				'ICSFILEHASH' => $file_md5Hash
			];
			

			$processedEvents[] = $processedEvent;
			// Update the transient with the remaining events.
			set_transient('atollmatrix_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS);
		}
	
		// Return the processed events and the number of remaining events.
		wp_send_json_success([
			'processed' => $processedEvents,
			'remaining' => count($events),
		]);
	}
}

// Instantiate the class
$eventBatchProcessor = new EventBatchProcessor();
