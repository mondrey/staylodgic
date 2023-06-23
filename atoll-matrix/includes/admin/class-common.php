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


}
