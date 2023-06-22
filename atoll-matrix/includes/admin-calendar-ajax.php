<?php
add_action( 'wp_ajax_cognitive_book_rooms', 'cognitive_book_rooms' );
add_action( 'wp_ajax_nopriv_cognitive_book_rooms', 'cognitive_book_rooms' );
function cognitive_book_rooms() {

	// Check if our nonce is set.
	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['nonce'], 'themecore-nonce-search' ) ) {
		return;
	}

	// Generate unique booking number
	$booking_number = sanitize_text_field($_POST['booking_number']);
	$transient_booking_number = get_transient( $booking_number );
	delete_transient( $booking_number );
	if ( '1' != $transient_booking_number ) {
		wp_send_json_error( 'Invalid or timeout. Please try again' );
	}
	// Obtain customer details from form submission
	$full_name = sanitize_text_field($_POST['full_name']);
	$email_address = sanitize_email($_POST['email_address']);
	$phone_number = sanitize_text_field($_POST['phone_number']);
	$street_address = sanitize_text_field($_POST['street_address']);
	$city = sanitize_text_field($_POST['city']);
	$state = sanitize_text_field($_POST['state']);
	$zip_code = sanitize_text_field($_POST['zip_code']);
	$country = sanitize_text_field($_POST['country']);
	// add other fields as necessary

	// Create customer post
	$customer_post_data = array(
		'post_type'     => 'customers',  // Your custom post type for customers
		'post_title'    => $full_name,   // Set the customer's full name as post title
		'post_status'   => 'publish',    // The status you want to give new posts
		'meta_input'    => array(
			'pagemeta_full_name' => $full_name,
			'pagemeta_email_address' => $email_address,
			'pagemeta_phone_number' => $phone_number,
			'pagemeta_street_address' => $street_address,
			'pagemeta_city' => $city,
			'pagemeta_state' => $state,
			'pagemeta_zip_code' => $zip_code,
			'pagemeta_country' => $country,
			'pagemeta_booking_number' => $booking_number,  // Set the booking number as post meta
			// add other meta data you need
		),
	);

	// Insert the post
	$customer_post_id = wp_insert_post($customer_post_data);

	if(!$customer_post_id) {
		wp_send_json_error('Could not save Customer: ' . $customer_post_id );
		return;
	}


	$checkin = $_POST['checkin'];
	$checkout = $_POST['checkout'];
	$rooms = $_POST['rooms'];

	// Process the booking
	foreach ($rooms as $room) {
		$room_id = $room['id'];
		$quantity = (int)$room['quantity'];

		for($i = 0; $i < $quantity; $i++) {
			// Here you can also add other post data like post_title, post_content etc.
			$post_data = array(
				'post_type'     => 'reservations',  // Your custom post type
				'post_title'    => $booking_number,  // Set the booking number as post title
				'post_status'   => 'publish',       // The status you want to give new posts
				'meta_input'    => array(
					'pagemeta_room_name' => $room_id,
					'pagemeta_reservation_status' => 'confirmed',
					'pagemeta_checkin_date' => $checkin,
					'pagemeta_checkout_date' => $checkout,
					'pagemeta_booking_number' => $booking_number,  // Set the booking number as post meta
					'pagemeta_customer_id' => $customer_post_id,  // Link to the customer post
					// add other meta data you need
				),
			);

			// Insert the post
			$reservation_post_id = wp_insert_post($post_data);
			
			if($reservation_post_id) {
				// Successfully created a reservation post
				update_reservations_array_on_save($reservation_post_id, get_post($post_id), true);
			} else {
				// Handle error
			}
		}
	}
	// Send a success response at the end of your function, if all operations are successful
	wp_send_json_success('Booking successfully registered.');
	wp_die();
}

add_action('wp_ajax_cognitive_check_room_availability', 'cognitive_check_room_availability');
add_action('wp_ajax_nopriv_cognitive_check_room_availability', 'cognitive_check_room_availability');
function cognitive_check_room_availability() {
	$checkin_date = $_POST['checkin'];
	$checkout_date = $_POST['checkout'];
	$reservationid = $_POST['reservationid'];
	$available_rooms = array();

	$room_list = \AtollMatrix\Rooms::queryRooms();

	foreach($room_list as $room) {
		$reservation_instance = new \AtollMatrix\Reservations(  $dateString = false, $roomId = false, $reservation_id = $reservationid, $excluded_reservation_id = false );
		$is_fullybooked = $reservation_instance->isRoom_Fullybooked_For_DateRange($room->ID, $checkin_date, $checkout_date, $reservationid);
		
		// if not fully booked add to available rooms
		if (!$is_fullybooked) {
			$available_rooms[$room->ID] = $room->post_title; // changed here
		}
	}

	echo json_encode($available_rooms);
	wp_die(); // this is required to terminate immediately and return a proper response
}



// WordPress AJAX action hook
add_action('wp_ajax_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');
add_action('wp_ajax_nopriv_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');
// PHP function that generates the content
function cognitive_ajax_get_availability_calendar() {
	// Get the start and end dates from the AJAX request
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	// Define the start and end dates
	// Define the start and end dates as DateTime objects
	ob_start();
	//echo cognitive_get_availability_calendar( $start_date, $end_date );
	$availabilityCalendar = new AtollMatrix\AvailablityCalendar( $start_date, $end_date );
	echo $availabilityCalendar->getAvailabilityCalendar();
	$output = ob_get_clean();
	echo $output;
	wp_die();
}

// AJAX handler to save room metadata
add_action('wp_ajax_cognitive_update_room_rate', 'cognitive_update_room_rate');
add_action('wp_ajax_nopriv_cognitive_update_room_rate', 'cognitive_update_room_rate');
function cognitive_update_room_rate() {
	if (isset($_POST['dateRange'])) {
		$dateRange = $_POST['dateRange'];
	} else {
		// Return an error response if dateRange is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing date range parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['rate'])) {
		$rate = $_POST['rate'];
	} else {
		// Return an error response if quantity is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing rate parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['postID'])) {
		$postID = $_POST['postID'];
	} else {
		// Return an error response if postID is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing post ID parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	// Split the date range into start and end dates
	$dateRangeArray = explode(" to ", $dateRange);
	if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
		// Use the single date as both start and end date
		$startDate = $dateRangeArray[0];
		$endDate = $startDate;
	} elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
		// Return an error response if dateRange is invalid
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid date range.'
			)
		);
		wp_send_json_error($response);
		return;
	} else {
		$startDate = $dateRangeArray[0];
		$endDate = $dateRangeArray[1];
	}

	// If the end date is empty, set it to the start date
	if (empty($endDate)) {
		$endDate = $startDate;
	}

	// Retrieve the existing roomrate_array meta value
	$roomrateArray = get_post_meta($postID, 'roomrate_array', true);

	// If the quantity_array is not an array, initialize it as an empty array
	if (!is_array($roomrateArray)) {
		$roomrateArray = array();
	}

	// Generate an array of dates between the start and end dates
	$dateRange = \AtollMatrix\Common::create_inBetween_DateRange_Array( $startDate, $endDate );

	// Update the quantity values for the specified date range
	foreach ($dateRange as $date) {
		$roomrateArray[$date] = $rate;
	}

	// Update the metadata for the 'reservations' post
	if (!empty($postID) && is_numeric($postID)) {
		// Update the post meta with the modified quantity array
		update_post_meta($postID, 'roomrate_array', $roomrateArray);
		// Return a success response
		$response = array(
			'success' => true,
			'data' => array(
				'message' => 'Room rates updated successfully.'
			)
		);
		wp_send_json_success($response);
	} else {
		// Return an error response
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid post ID.'
			)
		);
		wp_send_json_error($response);
	}

	wp_die(); // Optional: Terminate script execution
}

// AJAX handler to save room metadata
add_action('wp_ajax_cognitive_update_room_availability', 'cognitive_update_room_availability');
add_action('wp_ajax_nopriv_cognitive_update_room_availability', 'cognitive_update_room_availability');
function cognitive_update_room_availability() {
	if (isset($_POST['dateRange'])) {
		$dateRange = $_POST['dateRange'];
	} else {
		// Return an error response if dateRange is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing date range parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['quantity'])) {
		$quantity = $_POST['quantity'];
	} else {
		// Return an error response if quantity is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing quantity parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['postID'])) {
		$postID = $_POST['postID'];
	} else {
		// Return an error response if postID is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing post ID parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	// Split the date range into start and end dates
	$dateRangeArray = explode(" to ", $dateRange);
	if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
		// Use the single date as both start and end date
		$startDate = $dateRangeArray[0];
		$endDate = $startDate;
	} elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
		// Return an error response if dateRange is invalid
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid date range.'
			)
		);
		wp_send_json_error($response);
		return;
	} else {
		$startDate = $dateRangeArray[0];
		$endDate = $dateRangeArray[1];
	}

	// If the end date is empty, set it to the start date
	if (empty($endDate)) {
		$endDate = $startDate;
	}

	// Retrieve the existing quantity_array meta value
	$quantityArray = get_post_meta($postID, 'quantity_array', true);

	// If the quantity_array is not an array, initialize it as an empty array
	if (!is_array($quantityArray)) {
		$quantityArray = array();
	}

	// Generate an array of dates between the start and end dates
	$dateRange = \AtollMatrix\Common::create_inBetween_DateRange_Array( $startDate, $endDate );

	// Update the quantity values for the specified date range
	foreach ($dateRange as $date) {

		$reservation_instance = new \AtollMatrix\Reservations( $date, $postID );
		$reserved_rooms = $reservation_instance->calculateReservedRooms();

		$final_quantity = $quantity + $reserved_rooms;
		$quantityArray[$date] = $final_quantity;
	}

	// Update the metadata for the 'reservations' post
	if (!empty($postID) && is_numeric($postID)) {
		// Update the post meta with the modified quantity array
		update_post_meta($postID, 'quantity_array', $quantityArray);
		// Return a success response
		$response = array(
			'success' => true,
			'data' => array(
				'message' => 'Room availability updated successfully.'
			)
		);
		wp_send_json_success($response);
	} else {
		// Return an error response
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid post ID.'
			)
		);
		wp_send_json_error($response);
	}

	wp_die(); // Optional: Terminate script execution
}

