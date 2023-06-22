<?php
namespace AtollMatrix;
class Common {

	public static function countryCodeToEmoji($code) {
		$emoji = '';
		$code = strtoupper($code);
		for ($i = 0; $i < strlen($code); $i++) {
			$emoji .= '&#' . (ord($code[$i]) + 127397) . ';';
		}
		return $emoji;
	}

	public static function splitDateRange( $dateRange ) {
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

	public static function countDays_BetweenDates( $startDate, $endDate ) {
		// Create DateTime objects for the start and end dates
		$startDateTime = new \DateTime($startDate);
		$endDateTime = new \DateTime($endDate);
	
		// Calculate the difference between the two dates
		$interval = $endDateTime->diff($startDateTime);
	
		// Extract the number of days from the interval
		$daysBetween = $interval->days;
	
		// Return the result
		return $daysBetween;
	}

	// Function to create an array of dates between two dates
	public static function create_inBetween_DateRange_Array( $startDate, $endDate ) {
		$dateRangeArray = array();

		$currentDate = strtotime($startDate);
		$endDate = strtotime($endDate);

		while ($currentDate <= $endDate) {
			$dateRangeArray[] = date('Y-m-d', $currentDate);
			$currentDate = strtotime('+1 day', $currentDate);
		}

		return $dateRangeArray;
	}

	/**
	 * Gets all the dates between two given dates
	 */
	public static function getDates_Between( $start_date, $end_date ) {
		$dates = [];
		$current_date = strtotime($start_date);
		$end_date = strtotime($end_date);

		while ($current_date <= $end_date) {
			$dates[] = date('Y-m-d', $current_date);
			$current_date = strtotime('+1 day', $current_date);
		}

		return $dates;
	}

	/**
	 * Updates the reservations array when changes are made to a reservation post.
	 */
	public static function updateReservationsArray_On_Change( $room_id, $checkin_date, $checkout_date, $reservation_post_id ) {
		
		$reservation_instance = new \AtollMatrix\Reservations();
		$reservations_array = $reservation_instance->getReservations_Array( $room_id );

		$previous_checkin_date = get_post_meta($room_id, 'previous_checkin_date', true);
		$previous_checkout_date = get_post_meta($room_id, 'previous_checkout_date', true);

		$previous_dates = self::getDates_Between($previous_checkin_date, $previous_checkout_date);
		$updated_dates = self::getDates_Between($checkin_date, $checkout_date);

		$reservations_array = self::removeDates_From_ReservationsArray($previous_dates, $reservation_post_id, $reservations_array);
		$reservations_array = self::addDates_To_ReservationsArray($updated_dates, $reservation_post_id, $reservations_array);

		update_post_meta($room_id, 'reservations_array', json_encode($reservations_array));
		update_post_meta($room_id, 'previous_checkin_date', $checkin_date);
		update_post_meta($room_id, 'previous_checkout_date', $checkout_date);
	}

	/**
	 * Checks if the post is valid for processing
	 */
	public static function isReservation_valid_post( $post_id, $post ) {
		return !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'reservations' && get_post_status($post_id) !== 'draft';
	}

	/**
	 * Checks if the post is valid for processing
	 */
	public static function isCustomer_valid_post( $post_id ) {
		$post = get_post($post_id);
		return $post !== null && !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'customers' && get_post_status($post_id) !== 'draft';
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
