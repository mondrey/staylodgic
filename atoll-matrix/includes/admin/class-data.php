<?php
namespace AtollMatrix;
class Data {

	public function __construct() {
		// Hook into the save_post action
		add_action('save_post', array($this, 'updateReservationsArray_On_Save'), 13, 3);
		// Hook into the wp_trash_post action
		add_action('wp_trash_post', array($this, 'removeReservation_From_Array'));
		add_action('trashed_post', array($this, 'removeReservation_From_Array'));
	}

	public function create_Customer_From_Reservation_Post($reservation_post_id) {
		// Retrieve the reservation post using the ID
		$reservation_post = get_post($reservation_post_id);
		$customer_post_id = false;
	
		if (!$reservation_post) {
			// Handle error if reservation post not found
			return;
		}
	
		// Retrieve the necessary post meta data from the reservation post
		$full_name = get_post_meta($reservation_post_id, 'atollmatrix_full_name', true);
		$email_address = get_post_meta($reservation_post_id, 'atollmatrix_email_address', true);
		$phone_number = get_post_meta($reservation_post_id, 'atollmatrix_phone_number', true);
		$street_address = get_post_meta($reservation_post_id, 'atollmatrix_street_address', true);
		$city = get_post_meta($reservation_post_id, 'atollmatrix_city', true);
		$state = get_post_meta($reservation_post_id, 'atollmatrix_state', true);
		$zip_code = get_post_meta($reservation_post_id, 'atollmatrix_zip_code', true);
		$country = get_post_meta($reservation_post_id, 'atollmatrix_country', true);
		$booking_number = get_post_meta($reservation_post_id, 'atollmatrix_booking_number', true);
		$customer_choice = get_post_meta($reservation_post_id, 'atollmatrix_customer_choice', true);

		if ( 'existing' !== $customer_choice ) {
			if ( '' !== $full_name ) {
				error_log("Customer saving: " . $reservation_post_id . '||'. $full_name);
				// Create customer post
				$customer_post_data = array(
					'post_type'     => 'customers',  // Your custom post type for customers
					'post_title'    => $full_name,   // Set the customer's full name as post title
					'post_status'   => 'publish',    // The status you want to give new posts
					'meta_input'    => array(
						'atollmatrix_full_name' => $full_name,
						'atollmatrix_email_address' => $email_address,
						'atollmatrix_phone_number' => $phone_number,
						'atollmatrix_street_address' => $street_address,
						'atollmatrix_city' => $city,
						'atollmatrix_state' => $state,
						'atollmatrix_zip_code' => $zip_code,
						'atollmatrix_country' => $country,
						// add other meta data you need
					),
				);
		
				// Insert the post
				$customer_post_id = wp_insert_post($customer_post_data);
			}
		}
	
		if (!$customer_post_id) {
			// Handle error while creating customer post
			return;
		}
	
		// Update the reservation post with the customer post ID
		update_post_meta($reservation_post_id, 'atollmatrix_customer_id', $customer_post_id);
	}

	function removeReservation_From_Array($post_id) {
		// Check if the post is of the "reservations" post type
		if (get_post_type($post_id) === 'reservations') {
			$room_type = get_post_meta($post_id, 'atollmatrix_room_id', true);
			$reservation_post_id = $post_id;
			
			// Call the remove_reservation_from_array function
			self::removeReservation_ID($room_type, $reservation_post_id);
		}
	}

	public function removeReservation_ID($room_type, $reservation_post_id) {
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

	/**
	 * Triggered when a post is saved. If the post type is 'reservations' and is not autosaved or revision, it updates the reservation details.
	 */
	public function updateReservationsArray_On_Save($post_id, $post, $update) {

		error_log("is here " . $post_id );

		if ( ! \AtollMatrix\Common::isReservation_valid_post( $post_id, $post ) ) {
			return;
		}

		$room_type = get_post_meta($post_id, 'atollmatrix_room_id', true);
		$checkin_date = get_post_meta($post_id, 'atollmatrix_checkin_date', true);
		$checkout_date = get_post_meta($post_id, 'atollmatrix_checkout_date', true);
		$reservation_status = get_post_meta($post_id, 'atollmatrix_reservation_status', true);
		$customer_choice = get_post_meta($post_id, 'atollmatrix_customer_choice', true);
		$booking_number = get_post_meta($post_id, 'atollmatrix_booking_number', true);
		$existing_customer = get_post_meta($post_id, 'atollmatrix_existing_customer', true);

		$full_name = get_post_meta($post_id, 'atollmatrix_full_name', true);

		self::removeReservationID_From_All_Rooms($post_id); // Remove the reservation from all rooms

		$reservation_instance = new \AtollMatrix\Reservations();
		if ( $reservation_instance->isConfirmed_Reservation( $post_id ) ) {
			// Add reservation to the new room type
			self::updateReservationsArray_On_Change($room_type, $checkin_date, $checkout_date, $post_id);
		}

		// Check if customer post exists
		error_log("customer_choice: " . $customer_choice . '||'. $booking_number );
		$customer_id = get_post_meta($post_id, 'atollmatrix_customer_id', true);
		error_log("checking customer post: " . $customer_id . '||'. $post_id . '||' . $full_name );

		if ( \AtollMatrix\Common::isCustomer_valid_post( $existing_customer ) ) {
			if ( 'existing' == $customer_choice ) {
				
				error_log("Updating: " . $existing_customer . '||'. $booking_number );
				update_post_meta($post_id, 'atollmatrix_customer_id', $existing_customer);

			}
		}

		// Check if the post is being trashed
		if ($post->post_status === 'trash') {
			return; // Exit the function if the post is being trashed
		}

		if ( ! \AtollMatrix\Common::isCustomer_valid_post( $customer_id ) ) {
			if ( 'existing' !== $customer_choice ) {
				error_log("Customer does not exist: " . $customer_id . '||'. $full_name);
				// Create new customer from the filled inputs in reservation
				self::create_Customer_From_Reservation_Post($post_id);
			}
		}
	}


	/**
	 * Updates the reservations array when changes are made to a reservation post.
	 */
	public static function updateReservationsArray_On_Change( $room_id, $checkin_date, $checkout_date, $reservation_post_id ) {
		
		$reservation_instance = new \AtollMatrix\Reservations();
		$reservations_array = $reservation_instance->getReservations_Array( $room_id );

		$previous_checkin_date = get_post_meta($room_id, 'previous_checkin_date', true);
		$previous_checkout_date = get_post_meta($room_id, 'previous_checkout_date', true);

		$previous_dates = \AtollMatrix\Common::getDates_Between($previous_checkin_date, $previous_checkout_date);
		$updated_dates = \AtollMatrix\Common::getDates_Between($checkin_date, $checkout_date);

		$reservations_array = self::removeDates_From_ReservationsArray($previous_dates, $reservation_post_id, $reservations_array);
		$reservations_array = self::addDates_To_ReservationsArray($updated_dates, $reservation_post_id, $reservations_array);

		update_post_meta($room_id, 'reservations_array', json_encode($reservations_array));
		update_post_meta($room_id, 'previous_checkin_date', $checkin_date);
		update_post_meta($room_id, 'previous_checkout_date', $checkout_date);
	}

	/**
	 * Remove dates from the reservations array for a given reservation post ID.
	 */
	public static function removeDates_From_ReservationsArray($dates, $reservation_post_id, $reservations_array) {
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
	 * Add dates to the reservations array for a given reservation post ID.
	 */
	public static function addDates_To_ReservationsArray($dates, $reservation_post_id, $reservations_array) {
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
	 * Remove the reservation ID from the entire array
	 */
	public static function removeIDs_From_ReservationsArray( $reservation_post_id, $reservations_array ) {
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
	 * Remove the reservation from all rooms.
	 */
	public static function removeReservationID_From_All_Rooms( $reservation_post_id ) {
		$room_types = get_posts(['post_type' => 'room']);
		//error_log("remove reservation_from_all_rooms is called with ID: " . $reservation_post_id);
		foreach ($room_types as $room) {

			$reservation_instance = new \AtollMatrix\Reservations();
			$reservations_array = $reservation_instance->getReservations_Array( $room->ID );

			if (!empty($reservations_array)) {
				//error_log("Before removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));
				
				$reservations_array = self::removeIDs_From_ReservationsArray($reservation_post_id, $reservations_array);

				//error_log("After removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));
			}

			update_post_meta($room->ID, 'reservations_array', json_encode($reservations_array));
		}
	}
}
$instance = new \AtollMatrix\Data();
