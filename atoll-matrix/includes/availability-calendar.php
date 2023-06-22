<?php
define( 'DEBUG_MODE', false );
function cognitive_create_customer_from_reservation_post($reservation_post_id) {
	// Retrieve the reservation post using the ID
	$reservation_post = get_post($reservation_post_id);

	if (!$reservation_post) {
		// Handle error if reservation post not found
		return;
	}

	// Retrieve the necessary post meta data from the reservation post
	$full_name = get_post_meta($reservation_post_id, 'pagemeta_full_name', true);
	$email_address = get_post_meta($reservation_post_id, 'pagemeta_email_address', true);
	$phone_number = get_post_meta($reservation_post_id, 'pagemeta_phone_number', true);
	$street_address = get_post_meta($reservation_post_id, 'pagemeta_street_address', true);
	$city = get_post_meta($reservation_post_id, 'pagemeta_city', true);
	$state = get_post_meta($reservation_post_id, 'pagemeta_state', true);
	$zip_code = get_post_meta($reservation_post_id, 'pagemeta_zip_code', true);
	$country = get_post_meta($reservation_post_id, 'pagemeta_country', true);
	$booking_number = get_post_meta($reservation_post_id, 'pagemeta_booking_number', true);

	error_log("Customer saving: " . $reservation_post_id . '||'. $full_name);

	if ( '' !== $full_name ) {
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
	}

	if (!$customer_post_id) {
		// Handle error while creating customer post
		return;
	}

	// Update the reservation post with the customer post ID
	update_post_meta($reservation_post_id, 'pagemeta_customer_id', $customer_post_id);
}
function cognitive_generate_unique_reservation_id( $reservation_post_id ) {
	// Generate a random string or use a timestamp as a unique identifier
	$unique_identifier = uniqid(); // Example: Random string
	// $unique_identifier = time(); // Example: Timestamp

	// Combine the reservation post ID with the unique identifier
	$reservation_id = $reservation_post_id . '-' . $unique_identifier;

	return $reservation_id;
}

// Hook into the wp_trash_post action
add_action('wp_trash_post', 'remove_reservation_from_array');
add_action('trashed_post', 'remove_reservation_from_array');

function remove_reservation_from_array($post_id) {
	// Check if the post is of the "reservations" post type
	if (get_post_type($post_id) === 'reservations') {
		$room_type = get_post_meta($post_id, 'pagemeta_room_name', true);
		$reservation_post_id = $post_id;
		
		// Call the remove_reservation_from_array function
		remove_reservation_id($room_type, $reservation_post_id);
	}
}

function remove_reservation_id($room_type, $reservation_post_id) {
	// Retrieve the reservations array for the room type
	$reservations_array_json = get_post_meta($room_type, 'reservations_array', true);
	
	// Check if the reservations array is empty or not a JSON string
	if (empty($reservations_array_json) || !is_string($reservations_array_json)) {
		return;
	}

	// Decode the reservations array from JSON to an array
	$reservations_array = json_decode($reservations_array_json, true);
	
	// Check if the decoding was successful
	if ($reservations_array === null) {
		return;
	}
	
	// Convert the reservation post ID to a string for comparison
	$reservation_post_id = (string) $reservation_post_id;
	
	// Iterate over each date in the reservations array
	foreach ($reservations_array as $date => &$reservation_ids) {
		// Check if the reservation_ids is a JSON string
		if (is_string($reservation_ids)) {
			$reservation_ids = json_decode($reservation_ids, true);
		}

		if (is_array($reservation_ids)) {
			// Check if the reservation post ID exists in the array
			$index = array_search($reservation_post_id, $reservation_ids);
			if ($index !== false) {
				// Remove the reservation post ID from the array
				unset($reservation_ids[$index]);
				// Reset the array keys
				$reservation_ids = array_values($reservation_ids);
			}
		}

		// Check if there are no more reservation IDs in the array
		if (empty($reservation_ids)) {
			// Remove the date from the reservations array
			unset($reservations_array[$date]);
		}
	}
	
	// Encode the reservations array back to JSON
	$reservations_array_json = json_encode($reservations_array);
	// Update the reservations array meta field
	
	update_post_meta($room_type, 'reservations_array', $reservations_array_json);
	// $reservations_array_json = get_post_meta($room_type, 'reservations_array', true);
	// print_r( $reservations_array_json );die();
}

// Hook into the save_post action
add_action('save_post', 'update_reservations_array_on_save', 13, 3);

/**
 * Triggered when a post is saved. If the post type is 'reservations' and is not autosaved or revision, it updates the reservation details.
 */
function update_reservations_array_on_save($post_id, $post, $update) {
	if ( ! \AtollMatrix\Common::isReservation_valid_post( $post_id, $post ) ) {
		return;
	}

	$room_type = get_post_meta($post_id, 'pagemeta_room_name', true);
	$checkin_date = get_post_meta($post_id, 'pagemeta_checkin_date', true);
	$checkout_date = get_post_meta($post_id, 'pagemeta_checkout_date', true);
	$reservation_status = get_post_meta($post_id, 'pagemeta_reservation_status', true);

	$full_name = get_post_meta($post_id, 'pagemeta_full_name', true);

	\AtollMatrix\Common::removeReservationID_From_All_Rooms($post_id); // Remove the reservation from all rooms

	$reservation_instance = new \AtollMatrix\Reservations();
	if ( $reservation_instance->isConfirmed_Reservation( $post_id ) ) {
		// Add reservation to the new room type
		\AtollMatrix\Common::updateReservationsArray_On_Change($room_type, $checkin_date, $checkout_date, $post_id);
	}

	// Check if customer post exists
	$customer_id = get_post_meta($post_id, 'pagemeta_customer_id', true);
	error_log("checking customer post: " . $customer_id . '||'. $post_id . '||' . $full_name );

	if ( ! \AtollMatrix\Common::isCustomer_valid_post( $customer_id ) ) {
		error_log("Customer does not exist: " . $customer_id . '||'. $full_name);
		// Create new customer from the filled inputs in reservation
		cognitive_create_customer_from_reservation_post($post_id);
	}
}

// Add the Availability menu item to the admin menu
function cognitive_room_reservation_plugin_add_admin_menu() {
	add_menu_page(
		'Availability',
		'Availability',
		'manage_options',
		'room-availability',
		'cognitive_room_reservation_plugin_display_availability',
		'dashicons-calendar-alt',
		20
	);
}
add_action( 'admin_menu', 'cognitive_room_reservation_plugin_add_admin_menu' );

// Callback function to display the Availability page
function cognitive_room_reservation_plugin_display_availability() {
	// Check if user has sufficient permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Output the HTML for the Availability page
	?>
	<div class="wrap">
		<h1>Availability</h1>
		<?php
		// Add any custom HTML content here
		?>
	</div>
	<div class="calendar-controls-wrap">
		<button id="prev">Previous</button>
		<button id="prev-half">Prev 15</button>
		<button id="prev-week">Prev 7</button>
		<input type="text" class="availabilitycalendar" id="availabilitycalendar" name="availabilitycalendar" value=""/>
		<button id="next-week">Next 7</button>
		<button id="next-half">Next 15</button>
		<button id="next">Next</button>
		<a href="#" id="quantity-popup-link" data-bs-toggle="modal" data-bs-target="#quantity-popup">Update Quantity</a>
		<a href="#" id="rates-popup-link" data-bs-toggle="modal" data-bs-target="#rates-popup">Update Rates</a>
	</div>
	<div id="container">
<div id="calendar">
	<?php
	// Create an instance of the AvailablityCalendar class
	$availabilityCalendar = new AtollMatrix\AvailablityCalendar();

	// Call the getAvailabilityCalendar() method
	echo $availabilityCalendar->getAvailabilityCalendar();
	?>
</div>
</div>
	<?php

	cognitive_quanity_modal();
	cognitive_rates_modal();
}