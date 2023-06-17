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
	if (!cognitive_reservation_is_valid_post($post_id, $post)) {
		return;
	}

	$room_type = get_post_meta($post_id, 'pagemeta_room_name', true);
	$checkin_date = get_post_meta($post_id, 'pagemeta_checkin_date', true);
	$checkout_date = get_post_meta($post_id, 'pagemeta_checkout_date', true);
	$reservation_status = get_post_meta($post_id, 'pagemeta_reservation_status', true);

	remove_reservation_from_all_rooms($post_id); // Remove the reservation from all rooms

	if ( coginitive_confirmed_reservation($post_id) ) {
		// Add reservation to the new room type
		update_reservations_array_on_change($room_type, $checkin_date, $checkout_date, $post_id);
	}

	// Check if customer post exists
	$customer_id = get_post_meta($post_id, 'pagemeta_customer_id', true);
	if (!$customer_id) {
		// Create new customer from the filled inputs in reservation
		cognitive_create_customer_from_reservation_post($post_id);
	}
}

/**
 * Remove the reservation from all rooms.
 */
function remove_reservation_from_all_rooms($reservation_post_id) {
	$room_types = get_posts(['post_type' => 'room']);
	error_log("remove_reservation_from_all_rooms is called with ID: " . $reservation_post_id);
	foreach ($room_types as $room) {
		$reservations_array = get_reservations_array($room->ID);

		if (!empty($reservations_array)) {
			error_log("Before removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));
			
			$reservations_array = remove_id_from_reservations_array($reservation_post_id, $reservations_array);

			error_log("After removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));
		}

		update_post_meta($room->ID, 'reservations_array', json_encode($reservations_array));
	}
}

/**
 * Remove the reservation ID from the entire array
 */
function remove_id_from_reservations_array($reservation_post_id, $reservations_array) {
	foreach ($reservations_array as $date => &$reservations) {
		foreach ($reservations as $key => $id) {
			if ($id == $reservation_post_id) {
				unset($reservations[$key]);
			}
		}
		// Reset the array keys
		$reservations = array_values($reservations);
	}

	return $reservations_array;
}


/**
 * Add dates to the reservations array for a given reservation post ID.
 */
function add_dates_to_reservations_array($dates, $reservation_post_id, $reservations_array) {
	foreach ($dates as $date) {
		if (isset($reservations_array[$date])) {
			if (is_array($reservations_array[$date])) {
				$reservations_array[$date][] = $reservation_post_id;
			} else {
				$reservations_array[$date] = [$reservations_array[$date], $reservation_post_id];
			}
		} else {
			$reservations_array[$date] = [$reservation_post_id];
		}
	}

	return $reservations_array;
}

/**
 * Remove dates from the reservations array for a given reservation post ID.
 */
function remove_dates_from_reservations_array($dates, $reservation_post_id, $reservations_array) {
	foreach ($dates as $date) {
		if (isset($reservations_array[$date])) {
			$reservation_ids = $reservations_array[$date];
			if (($key = array_search($reservation_post_id, $reservation_ids)) !== false) {
				unset($reservations_array[$date][$key]);
				// Reset the array keys
				$reservations_array[$date] = array_values($reservations_array[$date]);
			}
		}
	}

	return $reservations_array;
}

/**
 * Checks if the post is valid for processing
 */
function cognitive_reservation_is_valid_post($post_id, $post) {
	return !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'reservations' && get_post_status($post_id) !== 'draft';
}

/**
 * Updates the reservations array when changes are made to a reservation post.
 */
function update_reservations_array_on_change($room_type, $checkin_date, $checkout_date, $reservation_post_id) {
	$reservations_array = get_reservations_array($room_type);
	$previous_checkin_date = get_post_meta($room_type, 'previous_checkin_date', true);
	$previous_checkout_date = get_post_meta($room_type, 'previous_checkout_date', true);

	$previous_dates = get_dates_between($previous_checkin_date, $previous_checkout_date);
	$updated_dates = get_dates_between($checkin_date, $checkout_date);

	$reservations_array = remove_dates_from_reservations_array($previous_dates, $reservation_post_id, $reservations_array);
	$reservations_array = add_dates_to_reservations_array($updated_dates, $reservation_post_id, $reservations_array);

	update_post_meta($room_type, 'reservations_array', json_encode($reservations_array));
	update_post_meta($room_type, 'previous_checkin_date', $checkin_date);
	update_post_meta($room_type, 'previous_checkout_date', $checkout_date);
}

/**
 * Retrieves and validates the reservations array for the given room type
 */
function get_reservations_array($room_type) {
	$reservations_array = get_post_meta($room_type, 'reservations_array', true);

	if (empty($reservations_array)) {
		$reservations_array = [];
	} else {
		$reservations_array = is_array($reservations_array) ? $reservations_array : json_decode($reservations_array, true);

		if (!is_array($reservations_array)) {
			error_log('Failed to convert reservations array to array!');
			return [];
		}
	}

	return $reservations_array;
}

/**
 * Removes the post ID from the specified dates
 */
function remove_post_id_from_dates($reservations_array, $dates, $post_id) {
	foreach ($dates as $date) {
		if (isset($reservations_array[$date])) {
			$key = array_search($post_id, $reservations_array[$date]);
			if ($key !== false) {
				unset($reservations_array[$date][$key]);
				$reservations_array[$date] = array_values($reservations_array[$date]);
			}
		}
	}
	return $reservations_array;
}

/**
 * Adds the post ID to the specified dates
 */
function add_post_id_to_dates($reservations_array, $dates, $post_id) {
	foreach ($dates as $date) {
		if (!isset($reservations_array[$date])) {
			$reservations_array[$date] = [];
		}
		$reservations_array[$date][] = $post_id;
	}
	return $reservations_array;
}

/**
 * Gets all the dates between two given dates
 */
function get_dates_between($start_date, $end_date) {
	$dates = [];
	$current_date = strtotime($start_date);
	$end_date = strtotime($end_date);

	while ($current_date <= $end_date) {
		$dates[] = date('Y-m-d', $current_date);
		$current_date = strtotime('+1 day', $current_date);
	}

	return $dates;
}

function cognitive_get_customer_meta_data($customer_array, $customer_post_id) {
	$output = array();

	// Loop through the customer array
	foreach ($customer_array as $item) {
		if ( 'seperator' !== $item['type'] ) {
			// Get the meta value for the current item's 'id'
			$meta_value = get_post_meta($customer_post_id, $item['id'], true);
			// Add an entry to the output array, with 'name' as the key and the meta value as the value
			$output[$item['name']] = $meta_value;
		}
	}

	return $output;
}

function cognitive_check_customer_exists($reservation_post_id) {
	// Get the booking number from the reservation post meta
	$booking_number = get_post_meta($reservation_post_id, 'pagemeta_booking_number', true);

	if (!$booking_number) {
		// Handle error if booking number not found
		return false;
	}

	// Query the customer post with the matching booking number
	$customer_query = new WP_Query(array(
		'post_type' => 'customers',
		'meta_query' => array(
			array(
				'key' => 'pagemeta_booking_number',
				'value' => $booking_number,
			),
		),
	));

	// Check if a customer post exists
	if ($customer_query->have_posts()) {
		// Restore the original post data
		wp_reset_postdata();

		// Return true if a matching customer post is found
		return true;
	}

	// No matching customer found, return false
	return false;
}

function cognitive_get_reservation_ids_for_customer_id($customer_id) {
	$args = array(
		'post_type'  => 'reservations',
		'meta_query' => array(
			array(
				'key'   => 'pagemeta_customer_id',
				'value' => $customer_id,
			)
		)
	);

	$reservations = get_posts($args);
	$reservation_ids = array();
	foreach ($reservations as $reservation) {
		// Fetch the booking number for this reservation
		$booking_number = get_post_meta($reservation->ID, 'pagemeta_booking_number', true);
		
		$reservation_ids[$reservation->ID] = $booking_number;
	}

	return $reservation_ids;
}


function cognitive_get_reservation_customer_id($reservation_post_id) {
	// Get the booking number from the reservation post meta
	$booking_number = get_post_meta($reservation_post_id, 'pagemeta_booking_number', true);

	if (!$booking_number) {
		// Handle error if booking number not found
		return '';
	}

	// Query the customer post with the matching booking number
	$customer_query = new WP_Query(array(
		'post_type' => 'customers',
		'meta_query' => array(
			array(
				'key' => 'pagemeta_booking_number',
				'value' => $booking_number,
			),
		),
	));

	if ($customer_query->have_posts()) {
		$customer_post = $customer_query->posts[0];

		// Restore the original post data
		wp_reset_postdata();

		// Return the ID of the customer post
		return $customer_post->ID;
	}

	// No matching customer found
	return '';
}


function cognitive_get_reservation_guest_name($reservation_post_id) {
	// Get the booking number from the reservation post meta
	$booking_number = get_post_meta($reservation_post_id, 'pagemeta_booking_number', true);

	if (!$booking_number) {
		// Handle error if booking number not found
		return '';
	}

	// Query the customer post with the matching booking number
	$customer_query = new WP_Query(array(
		'post_type' => 'customers',
		'meta_query' => array(
			array(
				'key' => 'pagemeta_booking_number',
				'value' => $booking_number,
			),
		),
	));

	if ($customer_query->have_posts()) {
		$customer_post = $customer_query->posts[0];

		// Retrieve the guest's full name from the customer post meta
		$guest_full_name = get_post_meta($customer_post->ID, 'pagemeta_full_name', true);

		// Restore the original post data
		wp_reset_postdata();

		return $guest_full_name;
	}

	// No matching customer found
	return '';
}


function cognitive_get_availability( $roomID ) {
	// Get the availability matrix field values for the Room post
	$availabilityMatrix = get_field('availability_matrix', $roomID);

	// Return the availability matrix
	return $availabilityMatrix;
}

function cognitive_calculate_reserved_rooms($date, $roomtype) {
	$args = array(
		'post_type' => 'reservations',
		'posts_per_page' => -1,
		'post_status' => 'publish', // Retrieve only published posts
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'pagemeta_room_name',
				'value' => $roomtype,
				'compare' => '='
			)
		)
	);
	
	$query = new WP_Query($args);
	$reserved_rooms = 0;

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();

			$reservation_id = get_the_ID();
			$custom = get_post_custom($reservation_id);

			if (isset($custom['pagemeta_checkin_date'][0]) && isset($custom['pagemeta_checkout_date'][0])) {
				$checkin = strtotime($custom['pagemeta_checkin_date'][0]);
				$checkout = strtotime($custom['pagemeta_checkout_date'][0]);

				$selected_date = strtotime($date);

				if ($selected_date >= $checkin && $selected_date < $checkout) {
					$reserved_rooms++;
				}
			}
		}
	}

	wp_reset_postdata();

	return $reserved_rooms;
}


// Function to check if a date falls within a reservation
function cognitive_is_date_reserved( $date, $roomtype ) {

	$currentDate = strtotime( $date );
	$start = false;

	$args = array(
		'post_type' => 'reservations',
		'posts_per_page' => -1,
		'post_status' => 'publish', // Retrieve only published posts
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'pagemeta_room_name',
				'value' => $roomtype,
				'compare' => '='
			)
		)
	);
	
	$query = new WP_Query($args);

	$reservation_checkin  = '';
	$reservation_checkout = '';
	$reservedRooms        = array();
	$reserved_data = array();
	$found = false;

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			
			$reservedRooms[] = get_the_ID();
			$reservation_id = get_the_ID();
			$custom = get_post_custom( get_the_ID() );
			if (isset($custom['pagemeta_reservation_checkin'][0])) {
				$dateRangeValue=$custom['pagemeta_reservation_checkin'][0];
			}
			if (isset($custom['pagemeta_room_name'][0])) {
				$room_id=$custom['pagemeta_room_name'][0];
			}

			// Date will be like so $dateRangeValue = "2023-05-21 to 2023-05-24";
			//$dateRangeParts = explode(" to ", $dateRangeValue);
			
			$checkin = '';
			$checkout = '';
			if (isset($custom['pagemeta_checkin_date'][0])) {
				$checkin=$custom['pagemeta_checkin_date'][0];
			}
			if (isset($custom['pagemeta_checkout_date'][0])) {
				$checkout=$custom['pagemeta_checkout_date'][0];
			}
			//echo '----->'.$checkin.'<-----';
			// if (count($dateRangeParts) >= 2) {
			// 	$checkin = $dateRangeParts[0];
			// 	$checkout = $dateRangeParts[1];
			// }

			// $checkin_start_datetime = explode(" ", $reservation_checkin);
			// $reservation_checkin_date = $checkin_start_datetime[0];

			// $checkout_start_datetime = explode(" ", $reservation_checkout);
			// $reservation_checkout_date = $checkout_start_datetime[0];

			$reservationStartDate = strtotime($checkin);
			$reservationEndDate = strtotime($checkout);
			$numberOfDays = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;

			// if ( $reservation_checkin_date == $date && $room_id == $roomtype ) {
			// 	echo 'Reserved';
			// }

			if ( $room_id == $roomtype ) {
				// echo $currentDate . '<br/>' . $reservationStartDate . '<br/>';
				// echo $currentDate . '<br/>' . $reservationEndDate . '<br/>';
				// Check if the current date falls within the reservation period
				if ( $currentDate >= $reservationStartDate && $currentDate < $reservationEndDate ) {
					// Check if the reservation spans the specified number of days
					$reservationDuration = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;
					if ( $numberOfDays > 0 ) {
						if ( $currentDate == $reservationStartDate ) {
							$start = 'yes';
						} else {
							$start = 'no';
						}
						$reservation_data['id'] = $reservation_id;
						$reservation_data['checkin'] = $reservationStartDate;
						$reservation_data['start'] = $start;
						$reserved_data[]=$reservation_data; // Date is part of a reservation for the specified number of days
						$found = true;
					}
				}
			}

		}
	}

	if ( $found ) {
		return $reserved_data;
	} else {
		return false;
	}

	// foreach ( $reservationData as $reservation ) {
	// 	$reservationStartDate = strtotime( $reservation['start_date'] );
	// 	$reservationEndDate = strtotime( $reservation['end_date'] );

	// 	// Check if the current date falls within the reservation period
	// 	if ( $currentDate >= $reservationStartDate && $currentDate <= $reservationEndDate ) {
	// 		// Check if the reservation spans the specified number of days
	// 		$reservationDuration = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;
	//         if ( $reservationDuration === $numberOfDays ) {
	//             return true; // Date is part of a reservation for the specified number of days
	//         }
	//     }
	// }

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

function cognitive_get_room_type_quantity( $room_id ) {
	$custom = get_post_custom( $room_id );
	if (isset($custom['pagemeta_max_rooms_of_type'][0])) {
		$room_max_rooms_of_type = $custom['pagemeta_max_rooms_of_type'][0];

		return $room_max_rooms_of_type;
	}
	return false;
}
/**
 * Retrieves the room rate for a given room ID and date.
 *
 * @param int $roomID The ID of the room.
 * @param string $date The date to retrieve the rate for.
 *
 * @return mixed The room rate for the given date, or null if not set.
 */
function cognitive_get_room_rate_by_date($roomID, $date) {
	// Get the room rate array from the post meta data.
	$roomRateArray = get_post_meta($roomID, 'roomrate_array', true);
	
	// If the room rate array is set and the date exists in the array, return the rate.
	if (is_array($roomRateArray) && isset($roomRateArray[$date])) {
		return $roomRateArray[$date];
	}

	$rate = cognitive_get_room_type_base_rate( $roomID );
	return $rate;
}

function cognitive_get_room_type_base_rate( $room_id ) {
	$custom = get_post_custom( $room_id );
	if (isset($custom['pagemeta_base_rate'][0])) {
		$base_rate = $custom['pagemeta_base_rate'][0];

		return $base_rate;
	}
	return false;
}
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
	echo cognitive_get_availability_calendar( false, false );
	?>

</div>
</div>
	<?php

	cognitive_quanity_modal();
	cognitive_rates_modal();
}
function cognitive_remaining_rooms_for_day($roomId, $dateString) {

	$reserved_room_count = cognitive_count_reservations_for_day($roomId, $dateString);
	$max_count = cognitive_get_max_quantity_for_room( $roomId, $dateString );
	$avaiblable_count = $max_count - $reserved_room_count;
	if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
		$avaiblable_count = 0;
	}
	
	return $avaiblable_count;
}

function cognitive_count_reservations_for_day($room_id, $day, $excluded_reservation_id = null) {

	$occupied_count = 0;
	// Retrieve the reservations array for the room type
	$reservations_array_json = get_post_meta($room_id, 'reservations_array', true);
	if ( DEBUG_MODE ) {
		print_r($reservations_array_json );
	}
	//print_r($reservations_array_json );
	// If the reservations array is empty or not a JSON string, return 0
	if (empty($reservations_array_json) || !is_string($reservations_array_json)) {
		return 0;
	}
	
	// Decode the reservations array from JSON to an array
	$reservations_array = json_decode($reservations_array_json, true);
	
	// Check if the decoding was successful
	if ($reservations_array === null) {
		return 0;
	}
	// Check if the day exists in the reservations array
	if (isset($reservations_array[$day])) {
		$reservation_ids = $reservations_array[$day];
		
		// Check if the reservation IDs is an array
		if (is_array($reservation_ids)) {
			
			// Loop through reservation ID and see if checkout is on the same day.
			// If so don't count it as an occupied room
			foreach( $reservation_ids as $reservation_id ) {

				// If this reservation should be excluded from the count, skip this loop iteration
				if ($reservation_id == $excluded_reservation_id) continue;

				$checkout = cognitive_get_checkout_date( $reservation_id );
				if ( $day < $checkout ) {
					$occupied_count++;
				}
			}
			return $occupied_count;
		} elseif (!empty($reservation_ids)) {
			$max_room_count = cognitive_get_max_quantity_for_room($room_id, $day);
			return $max_room_count;
		}
	}

	return 0;
}

// PHP function that generates the content
function cognitive_get_availability_calendar( $startDate, $endDate ) {

	if ( ! $startDate && ! $endDate ) {
		// Define the start and end dates
		$today = new DateTime();
		$week_ago = (new DateTime())->modify('-9 days');
		$end_date = (new DateTime())->modify('+30 days');

		$startDate = $week_ago->format('Y-m-d');
		$endDate = $end_date->format('Y-m-d');

		$numDays = (new DateTime($endDate))->diff(new DateTime($startDate))->days + 1;

		// Generate an array of dates for the calendar
		$dates = [];
		for ($day = 0; $day < $numDays; $day++) {
			$currentDate = new DateTime($startDate);
			$currentDate->add(new DateInterval("P{$day}D"));
			$dates[] = $currentDate;
		}

	} else {
		$startDate = new DateTime($startDate);
		$endDate = new DateTime($endDate);
	
		// $startDate = new DateTime("2023-05-17");
		// $endDate = new DateTime("2023-06-22");
		
		// Calculate the number of days between the start and end dates
		$numDays = $endDate->diff($startDate)->days + 1;

		// Generate an array of dates for the calendar
		$dates = [];
		for ($day = 0; $day < 30; $day++) {
			$currentDate = clone $startDate;
			$currentDate->add(new DateInterval("P{$day}D"));
			$dates[] = $currentDate;
		}

		$startDate = $startDate->format('Y-m-d');
		$endDate = $endDate->format('Y-m-d');
	}
	$room_list = get_posts(array(
		'post_type' => 'room',
		'orderby' => 'title',
		'numberposts' => -1,
		'order' => 'ASC',
		'post_status' => 'publish' // Retrieve only published posts
	));
	if ($room_list) {
		foreach($room_list as $key => $list) {
			$rooms[$list->ID] = $list->post_title;
		}
	} else {
		$rooms[0]="Rooms not found.";
	}

	ob_start();
	// Get today's date
	$today = new DateTime();
	$today = $today->format('Y-m-d');
	?>
	<table id="calendarTable" data-calstart="<?php echo $startDate; ?>" data-calend="<?php echo $endDate; ?>">
		<tr class="calendarRow">
			<td class="calendarCell rowHeader">
				<div class="occupancyStats-wrap">
					<div class="occupancyStats-inner">
						<div class="occupancy-total">
							Occupancy
						<span class="occupancy-total-stats">
						<?php
						echo cognitive_calculate_range_occupancy( $startDate, $endDate );
						?><span>%</span>
						</span>
						</div>
					</div>
				</div>
			</td>
			<?php
			$number_of_columns = 0;
			$markNumDays = $numDays + 1;
			foreach ($dates as $date) :
				$number_of_columns++;
				$month = $date->format('F');
				$today_status_class = '';
				$occupancydate = $date->format('Y-m-d');
				if ( $occupancydate == $today ) {
					$today_status_class = "is-today";
					$month = "Today";
				}
			?>
					<td class="calendarCell monthHeader occupancy-stats <?php echo $today_status_class; ?>">
						<div class="occupancyStats-wrap">
							<div class="occupancyStats-inner">
								<div class="occupancy-adr">ADR: <?php echo cognitive_calculate_adr( $occupancydate ); ?></div>
								<div class="occupancy-percentage"><?php echo cognitive_calculate_occupancy( $occupancydate ); ?><span>%</span></div>
							</div>
						</div>
					</td>
			<?php endforeach; ?>
		</tr>
		<tr class="calendarRow">
			<td class="calendarCell rowHeader"></td>
			<?php
			$number_of_columns = 0;
			$markNumDays = $numDays + 1;
			foreach ($dates as $date) :
				$number_of_columns++;
				$month = $date->format('F');
				$column_class = '';
				if ( $number_of_columns < $markNumDays ) {
					$column_class = "rangeSelected";
				}
				$today_status_class = '';
				$checkdateString = $date->format('Y-m-d');
				if ( $checkdateString == $today ) {
					$today_status_class = "is-today";
					$month = "Today";
				}
			?>
					<td class="calendarCell monthHeader <?php echo $today_status_class; ?> <?php echo $column_class; ?>">
						<div class="monthDayinfo-wrap">
							<div class="month"><?php echo $month; ?></div>
							<div class="day-letter"><?php echo $date->format('D'); ?></div>
							<div class="day"><?php echo $date->format('j'); ?></div>
						</div>
					</td>
			<?php endforeach; ?>
		</tr>
		<?php foreach ($rooms as $roomId => $roomName) : ?>
			<?php
			$checkout_list = array();
			?>
			<tr class="calendarRow calendar-room-row" data-id="<?php echo $roomId; ?>">
				<td class="calendarCell rowHeader"><?php echo $roomName; ?></td>
				<?php foreach ($dates as $date) : ?>
						<?php
						$dateString = $date->format('Y-m-d');
						$reservation_data = array();
						$reservation_data = cognitive_is_date_reserved($dateString, $roomId);
						$remaining_rooms = cognitive_remaining_rooms_for_day($roomId, $dateString);
						$reserved_room_count = cognitive_count_reservations_for_day($roomId, $dateString);
						$max_room_count = cognitive_get_max_quantity_for_room($roomId, $dateString);
						$reserved_rooms = cognitive_calculate_reserved_rooms($dateString,$roomId);
						$room_rate = cognitive_get_room_rate_by_date( $roomId, $dateString );
						$occupancy_status_class = "";
						if ( cognitive_is_room_for_day_fullybooked($roomId, $dateString) ) {
							$occupancy_status_class = "fully-booked";
						}
						$today_status_class = '';
						if ( $dateString == $today ) {
							$today_status_class = "is-today";
						}
						echo '<td class="calendarCell '.$today_status_class.' '.$occupancy_status_class.'">';
						if (DEBUG_MODE) {
							if ( $reservation_data ) {
								print_r($reservation_data);
								echo '<br/>';
								echo 'Reserved';
								echo '<br/>';
								echo 'Total reservations:' . $reserved_room_count;
								echo '<br/>';
								echo 'Remaining Rooms:' . $remaining_rooms;
								echo '<br/>';
							}
						}
						if (DEBUG_MODE) {
							// Calculate the number of reserved rooms for the current date
							echo '<br/>';
							echo 'Number of rooms reserved is:';
							echo $reserved_rooms;
							echo '<br/>';
							echo 'Max Rooms:' . $max_room_count;
							echo '<br/>';
						}
						?>
						<div class="calendar-info-wrap">
						<div class="calendar-info">
						<a href="#" class="quantity-link" data-remaining="<?php echo $remaining_rooms; ?>" data-reserved="<?php echo $reserved_rooms; ?>" data-date="<?php echo $dateString; ?>" data-room="<?php echo $roomId; ?>"><?php echo $remaining_rooms; ?></a>
						<?php
						if (!empty($room_rate) && isset($room_rate) && $room_rate > 0) {
							echo '<a class="roomrate-link" href="#">'.$room_rate.'</a>';
						}
						?>
						</div>
						</div>
						<div class="reservation-tab-wrap" data-day="<?php echo $dateString; ?>">
						<?php
						if ( $reservation_data ) {
							$reservation_module = array();
							//echo cognitive_generate_reserved_tab( $reservation_data, $checkout_list );
							$reservation_module = cognitive_generate_reserved_tab( $reservation_data, $checkout_list, $dateString, $startDate );
							echo $reservation_module['tab'];
							$checkout_list = $reservation_module['checkout'];
							//print_r( $checkout_list );
						}
						?>
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
	$output = ob_get_clean();
	return $output;
}
function cognitive_generate_reserved_tab( $reservation_data, $checkout_list, $current_day, $calendar_start ) {
	$display = false;
	$tab = array();
	if (DEBUG_MODE) {
		print_r(  $reservation_data );
	}
	$row = 0;
	$room = 1;
	foreach ($reservation_data as $reservation) {
		$start_date_display = '';
		$guest_name = '';
		$reservatoin_id = $reservation['id'];
		$booking_number = cognitive_get_booking_number($reservation['id']);
		$guest_name = cognitive_get_reservation_guest_name($reservation['id']);
		$reserved_days = cognitive_count_reservation_days( $reservation['id'] );
		$checkin = cognitive_get_checkin_date( $reservation['id'] );
		$checkout = cognitive_get_checkout_date( $reservation['id'] );
		$reservation_status = cognitive_get_reservation_status( $reservation['id'] );
		$row++;

		if ( !array_key_exists($reservatoin_id, $checkout_list) ) {

			$newCheckin = $checkin; // Checkin date of the new value to be added
			$hasConflict = false; // Flag to track if there is a conflict
			//print_r( $checkout_list );
			// Iterate through the existing array
			foreach ($checkout_list as $value) {
				$checkoutDate = $value['checkout'];
			
				// Compare the new checkin date with existing checkout dates
				if ($newCheckin <= $checkoutDate) {
					$hasConflict = true;
					//echo 'Conflict' . $reservatoin_id;
					break; // Stop iterating if a conflict is found
				}
			}

			$givenCheckinDate = $checkin;
			//echo $givenCheckinDate . '-' . $reservatoin_id . ' ';
			// Filter the array based on the check-in date and existing checkout dates
			$filteredArray = array_filter($checkout_list, function($value) use ($givenCheckinDate) {
				return $value['checkout'] > $givenCheckinDate;
			});

			// print_r( $filteredArray );
			// echo '<br/>';
			// Extract the room numbers from the filtered array
			$roomNumbers = array_column($filteredArray, 'room');

			// Check for missing room numbers
			$missingNumber = false;
			sort($roomNumbers);

			if (!empty($roomNumbers)) {
				for ($i = 1; $i <= max($roomNumbers); $i++) {
					if (!in_array($i, $roomNumbers)) {
						$missingNumber = $i;
						break;
					}
				}
			}

			// Output the result
			if ($missingNumber) {
				// echo "The missing room number is: $missingNumber";
				// echo '<br/>';
				$room = $missingNumber;
			} else {
				$givenDate = $checkin;
				$recordCount = 0;

				foreach ($checkout_list as $value) {
					$checkoutDate = $value['checkout'];
				
					if ($checkoutDate > $givenDate) {
						$recordCount++;
					}
				}
				
				if ($hasConflict) {
					//echo "Conflict detected: The new checkin date falls within existing checkout dates.";
					$room = $recordCount + 1;
				} else {
					//echo "No conflict: The new checkin date is outside existing checkout dates.";
					$room = $recordCount - 1;
				}
			}
			// $highestRoom = 1; // Initialize with a lower value

			// foreach ($checkout_list as $value) {
			// 	$room = $value['room'];
			
			// 	if ($room > $highestRoom) {
			// 		$highestRoom = $room;
			// 	}
			// }


			if (empty($checkout_list)) {
				$room = 1;
			}

			$checkout_list[$reservatoin_id]['room']=$room;
			$checkout_list[$reservatoin_id]['checkin']=$checkin;
			$checkout_list[$reservatoin_id]['checkout']=$checkout;
		}

		if ( array_key_exists($reservatoin_id, $checkout_list) ) {
			$room = $checkout_list[$reservatoin_id]['room'];
		}

		// if ( $reservation['start'] <> 'no' ) {
		// 	$start_date = new DateTime();
		// 	$start_date->setTimestamp($reservation['start']);
		// 	$start_date_display = $start_date->format('M j, Y');
		// 	$display_info = $guest_name;
		// 	$width = ( 80 * ( $reserved_days + 1 ) ) - 3;
		// 	$tab['new'][] = '<div class="reserved-tab-wrap" data-room="'.$room.'" data-row="'.$row.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab reserved-tab-days-'.$reserved_days.'"><div style="width:'.$width.'px;" class="reserved-tab-inner">'.$display_info.'</div></div></div>';
		// 	$display = true;
		// } else {
		// 	$tab['existing'][] = '<div class="reserved-tab-wrap reserved-extended" data-room="'.$room.'" data-row="'.$row.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab"></div></div>';
		// 	$display = true;
		// }
		$reservation_edit_link = get_edit_post_link($reservation['id']);
		$display_info = $guest_name . '<span>Booking.com</span>';
		if ( $reservation['start'] <> 'no' ) {
			$start_date = new DateTime();
			$start_date->setTimestamp($reservation['checkin']);
			$start_date_display = $start_date->format('M j, Y');
			$width = ( 80 * ( $reserved_days ) ) - 3;
			$tab[$room] = '<a class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reservation-edit-link" href="' . $reservation_edit_link . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-guest="'.$guest_name.'" data-room="'.$room.'" data-row="'.$row.'" data-bookingnumber="'.$booking_number.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab reserved-tab-days-'.$reserved_days.'"><div data-tabwidth="'.$width.'" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">'.$display_info.'</div></div></div></div></a>';
			$display = true;
		} else {
			if ( $current_day <> $checkout ) {
				// Get the checkin day for this as it's in the past of start of the availblablity calendar.
				// So this tab is happening from the past and needs to be labled athough an extention.
				$check_in_date_past = new DateTime();
				$check_in_date_past->setTimestamp($reservation['checkin']);
				$check_in_date_past = $check_in_date_past->format('Y-m-d');
				$daysBetween = cognitive_countDaysBetweenDates($check_in_date_past, $current_day);
				$width = ( 80 * ( $reserved_days - $daysBetween ) ) - 3;
				if ( $check_in_date_past < $calendar_start && $calendar_start == $current_day ) {
					$tab[$room] = '<a class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reservation-edit-link" href="' . $reservation_edit_link . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-guest="'.$guest_name.'" data-room="'.$room.'" data-row="'.$row.'" data-bookingnumber="'.$booking_number.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab reserved-tab-days-'.$reserved_days.'"><div data-tabwidth="'.$width.'" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">'.$display_info.'</div></div></div></div></a>';
				} else {
					$tab[$room] = '<div class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reserved-tab-wrap reserved-extended reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-room="'.$room.'" data-row="'.$row.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab"></div></div>';
				}
				$display = true;
			}
		}

	}
	
	krsort($tab);
	$tab_array = array();
	$htmltab = '';

	if ($display) {

		foreach ($tab as $key => $value) {
			$htmltab .= $value;
		}

	}
	$tab_array['tab']=$htmltab;
	$tab_array['checkout']=$checkout_list;

	return $tab_array;
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
	echo cognitive_get_availability_calendar( $start_date, $end_date );
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
	$dateRange = cognitive_createDateRangeArray($startDate, $endDate);

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
	$dateRange = cognitive_createDateRangeArray($startDate, $endDate);

	// Update the quantity values for the specified date range
	foreach ($dateRange as $date) {
		$reserved_rooms = cognitive_calculate_reserved_rooms($date,$postID);
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

// Function to create an array of dates between two dates
function cognitive_createDateRangeArray($startDate, $endDate) {
	$dateRangeArray = array();

	$currentDate = strtotime($startDate);
	$endDate = strtotime($endDate);

	while ($currentDate <= $endDate) {
		$dateRangeArray[] = date('Y-m-d', $currentDate);
		$currentDate = strtotime('+1 day', $currentDate);
	}

	return $dateRangeArray;
}

function cognitive_get_max_quantity_for_room($postID, $dateString) {
	$quantityArray = get_post_meta($postID, 'quantity_array', true);
	
	// Check if the quantity_array exists and the date is available
	if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
		return $quantityArray[$dateString];
	}
	
	return false;
}
function cognitive_get_checkin_date($reservation_post_id) {
	// Get the check-in and check-out dates for the reservation
	$checkin_date = get_post_meta($reservation_post_id, 'pagemeta_checkin_date', true);

	return $checkin_date;
}
function cognitive_get_checkout_date($reservation_post_id) {
	// Get the check-in and check-out dates for the reservation
	$checkout_date = get_post_meta($reservation_post_id, 'pagemeta_checkout_date', true);

	return $checkout_date;
}
function cognitive_get_reservation_status($reservation_post_id) {
	// Get the reservation status for the reservation
	$reservation_status = get_post_meta($reservation_post_id, 'pagemeta_reservation_status', true);

	return $reservation_status;
}
function coginitive_confirmed_reservation($reservation_post_id) {
	// Get the reservation status for the reservation
	$reservation_status = get_post_meta($reservation_post_id, 'pagemeta_reservation_status', true);

	if ( 'confirmed' == $reservation_status ) {
		return true;
	}

	return false;

}
function cognitive_count_reservation_days($reservation_post_id) {
	// Get the check-in and check-out dates for the reservation
	$checkin_date = get_post_meta($reservation_post_id, 'pagemeta_checkin_date', true);
	$checkout_date = get_post_meta($reservation_post_id, 'pagemeta_checkout_date', true);

	// Calculate the number of days
	$datetime1 = new DateTime($checkin_date);
	$datetime2 = new DateTime($checkout_date);
	$interval = $datetime1->diff($datetime2);
	$num_days = $interval->days;

	return $num_days;
}
function cognitive_countDaysBetweenDates($startDate, $endDate) {
	// Create DateTime objects for the start and end dates
	$startDateTime = new DateTime($startDate);
	$endDateTime = new DateTime($endDate);

	// Calculate the difference between the two dates
	$interval = $endDateTime->diff($startDateTime);

	// Extract the number of days from the interval
	$daysBetween = $interval->days;

	// Return the result
	return $daysBetween;
}

function cognitive_is_room_for_day_fullybooked($roomId, $dateString, $excluded_reservation_id = null) {

	$reserved_room_count = cognitive_count_reservations_for_day($roomId, $dateString, $excluded_reservation_id);
	$max_count = cognitive_get_max_quantity_for_room( $roomId, $dateString );
	$avaiblable_count = $max_count - $reserved_room_count;
	if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
		$avaiblable_count = 0;
	}
	if ( 0 == $avaiblable_count ) {
		return true;
	}
	
	return false;
}

function cognitive_is_room_fullybooked_for_date_range($roomId, $checkin_date, $checkout_date, $reservationid) {
	// get the date range
	$start = new DateTime($checkin_date);
	$end = new DateTime($checkout_date);
	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($start, $interval, $end);

	foreach ($daterange as $date) {
		// Check if the room is fully booked for the given date
		if (cognitive_is_room_for_day_fullybooked($roomId, $date->format("Y-m-d"), $reservationid)) {
			// If the room is fully booked for any of the dates in the range, return true
			return true;
		}
	}

	// If the room is not fully booked for any of the dates in the range, return false
	return false;
}

add_action('wp_ajax_cognitive_check_room_availability', 'cognitive_check_room_availability');
add_action('wp_ajax_nopriv_cognitive_check_room_availability', 'cognitive_check_room_availability');
function cognitive_check_room_availability() {
	$checkin_date = $_POST['checkin'];
	$checkout_date = $_POST['checkout'];
	$reservationid = $_POST['reservationid'];
	$available_rooms = array();

	// get all rooms
	$room_list = get_posts(array(
		'post_type' => 'room',
		'orderby' => 'title',
		'numberposts' => -1,
		'order' => 'ASC',
		'post_status' => 'publish'
	));

	foreach($room_list as $room) {
		$is_fullybooked = cognitive_is_room_fullybooked_for_date_range($room->ID, $checkin_date, $checkout_date, $reservationid);
		
		// if not fully booked add to available rooms
		if (!$is_fullybooked) {
			$available_rooms[$room->ID] = $room->post_title; // changed here
		}
	}

	echo json_encode($available_rooms);
	wp_die(); // this is required to terminate immediately and return a proper response
}

function cognitive_get_available_rooms_for_date_range( $checkin_date, $checkout_date ) {
	$available_rooms = array();

	// get all rooms
	$room_list = get_posts(array(
		'post_type' => 'room',
		'orderby' => 'title',
		'numberposts' => -1,
		'order' => 'ASC',
		'post_status' => 'publish'
	));

	foreach($room_list as $room) {
		$count = cognitive_get_max_room_qty_for_date_range($room->ID, $checkin_date, $checkout_date, $reservationid);
		
		// if not fully booked add to available rooms
		if ( $count !== 0 ) {
			$available_rooms[$room->ID][$count] = $room->post_title; // changed here
		}
	}

	return $available_rooms;
}

function cognitive_get_max_room_qty_for_date_range($roomId, $checkin_date, $checkout_date, $reservationid) {
	// get the date range
	$start = new DateTime($checkin_date);
	$end = new DateTime($checkout_date);
	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($start, $interval, $end);

	$max_count = PHP_INT_MAX;

	foreach ($daterange as $date) {
		// Check if the room is fully booked for the given date
		$count = cognitive_get_max_room_qty_for_day($roomId, $date->format("Y-m-d"), $reservationid);

		if ( $count < $max_count ) {
			$max_count = $count;
		}
	}

	// If no count was ever set, return false or whatever default value you need
	if ($max_count == PHP_INT_MAX) {
		return false;
	}
	
	// If the room is not fully booked for any of the dates in the range, return max_count
	return $max_count;
}


function cognitive_get_max_room_qty_for_day($roomId, $dateString, $excluded_reservation_id = null) {

	$reserved_room_count = cognitive_count_reservations_for_day($roomId, $dateString, $excluded_reservation_id);
	$max_count = cognitive_get_max_quantity_for_room( $roomId, $dateString );
	$avaiblable_count = $max_count - $reserved_room_count;
	if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
		$avaiblable_count = 0;
	}
	return $avaiblable_count;
}

function cognitive_splitDateRange($dateRange) {
	// Split the date range into start and end dates
	$dateRangeArray = explode(" to ", $dateRange);
	
	if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
		// Use the single date as both start and end date
		$startDate = $dateRangeArray[0];
		$endDate = $startDate;
	} elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
		// Return null if dateRange is invalid
		return null;
	} else {
		$startDate = $dateRangeArray[0];
		$endDate = $dateRangeArray[1];
	}

	// If the end date is empty, set it to the start date
	if (empty($endDate)) {
		$endDate = $startDate;
	}

	// Return start and end date as an array
	return array('startDate' => $startDate, 'endDate' => $endDate);
}

function cognitive_generate_customer_html_list($array) {
	$html = "<ul class='existing-customer'>";
	foreach ($array as $key => $value) {
		if ( 'Country' == $key ) {
			$value = cognitive_countryCodeToEmoji($value) . ' ' . themecore_country_list('display',$value);
		}
		$html .= "<li><strong>{$key}:</strong> {$value}</li>";
	}
	$html .= "</ul>";
	return $html;
}

function cognitive_get_reservation_ids_for_customer($customer_id) {
	$args = array(
		'post_type'  => 'reservations',
		'meta_query' => array(
			array(
				'key'     => 'pagemeta_customer_id',
				'value'   => $customer_id,
				'compare' => '=',
			),
		),
	);
	$posts = get_posts($args);
	$reservation_ids = array();
	foreach ($posts as $post) {
		$reservation_ids[] = $post->ID;
	}
	return $reservation_ids;
}

function cognitive_get_room_name_for_reservation($post_id) {
	// Get room id from post meta
	$room_id = get_post_meta($post_id, 'pagemeta_room_name', true);

	// If room id exists, get the room's post title
	if ($room_id) {
		$room_post = get_post($room_id);
		if ($room_post) {
			return $room_post->post_title;
		}
	}

	return null;
}

function cognitive_get_room_ids_for_booking_number($booking_number) {
	$args = array(
		'post_type'  => 'reservations',
		'meta_query' => array(
			array(
				'key'   => 'pagemeta_booking_number',
				'value' => $booking_number,
			)
		)
	);

	$reservations = get_posts($args);
	$room_names = array();
	foreach ($reservations as $reservation) {
		$room_id = get_post_meta($reservation->ID, 'pagemeta_room_name', true);
		// Use the room ID to get the room's post title
		$room_post = get_post($room_id);
		if ($room_post) {
			$room_names[] = $room_post->ID;
		}
	}

	return $room_names;
}

function cognitive_get_booking_number($reservation_post_id) {
	// Get the booking number from the reservation post meta
	$booking_number = get_post_meta($reservation_post_id, 'pagemeta_booking_number', true);

	if (!$booking_number) {
		// Handle error if booking number not found
		return '';
	}

	return $booking_number;
}

function cognitive_calculate_adr($currentdateString) {
	$currentDate = new DateTime($currentdateString);
	$totalRoomRevenue = 0;
	$numberOfRoomsSold = 0;

	$args = array(
		'post_type'      => 'reservations',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'pagemeta_reservation_status',
				'value'   => 'confirmed',
				'compare' => '=',
			),
		),
	);
	

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();

			$reservationStartDate = get_post_meta(get_the_ID(), 'pagemeta_checkin_date', true);
			$reservationEndDate = get_post_meta(get_the_ID(), 'pagemeta_checkout_date', true);

			$reservationStartDate = new DateTime($reservationStartDate);
			$reservationEndDate = new DateTime($reservationEndDate);

			// Check if the current date falls within the reservation period
			if ($currentDate >= $reservationStartDate && $currentDate < $reservationEndDate) {
				$roomID = get_post_meta(get_the_ID(), 'pagemeta_room_name', true);

				// Get the room rate for the current date
				$roomRate = cognitive_get_room_rate_by_date($roomID, $currentDate->format('Y-m-d'));

				$totalRoomRevenue += $roomRate;
				$numberOfRoomsSold++;
			}
		}
	}

	wp_reset_postdata();

	// Calculate ADR
	$adr = ($numberOfRoomsSold > 0) ? round( $totalRoomRevenue / $numberOfRoomsSold ) : 0;

	return $adr;
}

function cognitive_calculate_occupancy($currentdateString) {
	$totalOccupiedRooms = 0;
	$totalAvailableRooms = 0;

	$args = array(
		'post_type'      => 'room',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	);

	$rooms = get_posts($args);

	foreach($rooms as $room){
		// Increment the total number of occupied rooms
		$totalOccupiedRooms += cognitive_calculate_reserved_rooms( $currentdateString, $room->ID );
		// Increment the total number of available rooms
		$totalAvailableRooms += cognitive_get_max_quantity_for_room( $room->ID, $currentdateString);

		//echo '<br>'.$currentdateString.'<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
		//echo '<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
	}

	wp_reset_postdata();

	// Calculate the occupancy percentage
	if ($totalAvailableRooms > 0) {
		$occupancyPercentage = round(($totalOccupiedRooms / $totalAvailableRooms) * 100);
	} else {
		$occupancyPercentage = 0;
	}	

	return $occupancyPercentage;
}

function cognitive_calculate_range_occupancy($startDateString, $endDateString) {
	$startDate = new DateTime($startDateString);
	$endDate = new DateTime($endDateString);
	$currentDate = clone $startDate;
	
	$totalOccupancyPercentage = 0;
	$daysCount = 0;

	while ($currentDate <= $endDate) {
		$currentDateString = $currentDate->format('Y-m-d');
		$occupancyPercentage = cognitive_calculate_occupancy($currentDateString);
		$totalOccupancyPercentage += $occupancyPercentage;
		$daysCount++;
		$currentDate->modify('+1 day');
	}

	if ($daysCount > 0) {
		$averageOccupancyPercentage = round($totalOccupancyPercentage / $daysCount);
	} else {
		$averageOccupancyPercentage = 0;
	}

	return $averageOccupancyPercentage;
}






