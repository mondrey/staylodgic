<?php
namespace AtollMatrix;
class Reservations {

	private $reservation_id;
	private $reservation_id_excluded;
	private $date;
	private $room_id;
	
	public function __construct( $date = false, $room_id = false, $reservation_id = false, $reservation_id_excluded = false ) {
		$this->reservation_id          = $reservation_id;
		$this->reservation_id_excluded = $reservation_id_excluded;
		$this->date                    = $date;
		$this->room_id                 = $room_id;
	}

	public static function getConfirmedReservations() {
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
		return new \WP_Query($args);
	}

	public static function getRoomsforReservation( $booking_number ) {
		$args = array(
			'post_type'  => 'reservations',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query' => array(
				array(
					'key'   => 'pagemeta_booking_number',
					'value' => $booking_number,
				),
			),
		);
		return new \WP_Query($args);
	}

	public function getGuestforReservation( $booking_number ) {
		$args = array(
			'post_type' => 'customers',
			'meta_query' => array(
				array(
					'key' => 'pagemeta_booking_number',
					'value' => $booking_number,
				),
			),
		);
		return new \WP_Query($args);
	}


	public function getReservationsForRoom( $room_id = false ) {

		if ( ! $room_id ) {
			$room_id = $this->room_id;
		}

		$args = array(
			'post_type'       => 'reservations',
			'posts_per_page'  => -1,
			'post_status'     => 'publish',
			'meta_query'      => array(
				'relation'    => 'AND',
				array(
					'key'     => 'pagemeta_room_name',
					'value'   => $room_id,
					'compare' => '='
				),
			),
		);
		return new \WP_Query($args);
	}

	public function calculateReservedRooms() {

		$query          = $this->getReservationsForRoom();
		$reserved_rooms = 0;
	
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
	
				$reservation_id = get_the_ID();
				$custom         = get_post_custom($reservation_id);
	
				if (isset($custom['pagemeta_checkin_date'][0]) && isset($custom['pagemeta_checkout_date'][0])) {
					$checkin       = strtotime($custom['pagemeta_checkin_date'][0]);
					$checkout      = strtotime($custom['pagemeta_checkout_date'][0]);
					$selected_date = strtotime($this->date);
	
					if ($selected_date >= $checkin && $selected_date < $checkout) {
						$reserved_rooms++;
					}
				}
			}
		}
	
		wp_reset_postdata();
	
		return $reserved_rooms;
	}

	public function countReservationsForDay( $room_id = false, $day = false, $excluded_reservation_id = false ) {

		$occupied_count = 0;
		if ( ! $room_id ) {
			$room_id = $this->room_id;
		}
		if ( ! $day ) {
			$day = $this->date;
		}
		if (! $excluded_reservation_id ) {
			$excluded_reservation_id = $this->reservation_id_excluded;
		}
		
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
					
					$checkout = $this->getCheckoutDate( $reservation_id );
					if ( $day < $checkout ) {
						$occupied_count++;
					}
				}
				return $occupied_count;
			} elseif (!empty($reservation_ids)) {
				$max_room_count = \AtollMatrix\Rooms::getMaxQuantityForRoom($room_id, $day);
				return $max_room_count;
			}
		}
	
		return 0;
	}

	public function getBookingNumber() {
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta($this->reservation_id, 'pagemeta_booking_number', true);
	
		if (!$booking_number) {
			// Handle error if booking number not found
			return '';
		}
	
		return $booking_number;
	}

	public function getReservationGuestName() {
		// Get the booking number from the reservation post meta
		$booking_number = $this->getBookingNumber();
		
		if (!$booking_number) {
			// Handle error if booking number not found
			return '';
		}
	
		// Query the customer post with the matching booking number
		$customer_query = $this->getGuestforReservation( $booking_number );

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

	public function countReservationDays() {

		$reservation_post_id = $this->reservation_id;
		// Get the check-in and check-out dates for the reservation
		$checkin_date = get_post_meta($reservation_post_id, 'pagemeta_checkin_date', true);
		$checkout_date = get_post_meta($reservation_post_id, 'pagemeta_checkout_date', true);
	
		// Calculate the number of days
		$datetime1 = new \DateTime($checkin_date);
		$datetime2 = new \DateTime($checkout_date);
		$interval = $datetime1->diff($datetime2);
		$num_days = $interval->days;
	
		return $num_days;
	}

	public function getCheckinDate( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the check-in and check-out dates for the reservation
		$checkin_date = get_post_meta($reservation_id, 'pagemeta_checkin_date', true);
	
		return $checkin_date;
	}

	public function getCheckoutDate( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the check-in and check-out dates for the reservation
		$checkout_date = get_post_meta($reservation_id, 'pagemeta_checkout_date', true);
	
		return $checkout_date;
	}

	public function getReservationStatus( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the reservation status for the reservation
		$reservation_status = get_post_meta($reservation_id, 'pagemeta_reservation_status', true);
	
		return $reservation_status;
	}

	public static function getRoomIDsForBooking_number( $booking_number ) {

		$rooms_query = self::getRoomsforReservation( $booking_number );
		$room_names  = array();

		if ($rooms_query->have_posts()) {
			while ($rooms_query->have_posts()) {
				$rooms_query->the_post();
	
				// Use the post property of the WP_Query object
				$room_id = get_post_meta($rooms_query->post->ID, 'pagemeta_room_name', true);
	
				// Use the room ID to get the room's post title
				$room_post = get_post($room_id);
				if ($room_post) {
					$room_names[] = $room_post->ID;
				}
			}
			wp_reset_postdata(); // Reset the postdata
		}
	
		return $room_names;
	}

	public function getRoomTitleForReservation( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the room post ID from the reservation's meta data
		$room_post_id = get_post_meta($reservation_id, 'pagemeta_room_name', true);
	
		if ($room_post_id) {
			// Retrieve the room post using the ID
			$room_post = get_post($room_post_id);
	
			if ($room_post) {
				// Return the room's title
				return $room_post->post_title;
			}
		}
	
		// Return null if no room was found for the reservation
		return null;
	}

	public static function getReservationIDsForCustomer( $customer_id ) {
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

	public static function getEditLinksForReservations( $reservation_array ) {
		$links = '<ul>';
		foreach ($reservation_array as $post_id) {
			$room_name = self::getRoomNameForReservation( $post_id );
			$edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
			$links .= '<li><p><a href="' . $edit_link . '" title="' . $room_name . '">Edit Reservation ' . $post_id . '<br/><small>' . $room_name . '</small></a></p></li>';
		}
		$links .= '</ul>';
		return $links;
	}

	public function getCustomerEditLinkForReservation( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the customer post ID from the reservation's meta data
		$customer_post_id = get_post_meta($reservation_id, 'pagemeta_customer_id', true);
	
		if ($customer_post_id) {
			// Retrieve the customer post using the ID
			$customer_post = get_post($customer_post_id);
	
			if ($customer_post) {
				// Get the admin URL and create the link
				$edit_link = admin_url('post.php?post=' . $customer_post_id . '&action=edit');
				return '<a href="' . $edit_link . '">' . $customer_post->post_title . '</a>';
			}
		}
	
		// Return null if no customer was found for the reservation
		return null;
	}
	
	public static function getRoomNameForReservation( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}

		// Get room id from post meta
		$room_id = get_post_meta($reservation_id, 'pagemeta_room_name', true);
	
		// If room id exists, get the room's post title
		if ($room_id) {
			$room_post = get_post($room_id);
			if ($room_post) {
				return $room_post->post_title;
			}
		}
	
		return null;
	}

	public function isRoom_For_Day_Fullybooked( $roomId = false, $dateString = false, $excluded_reservation_id = null ) {

		if ( ! $roomId ) {
			$roomId = $this->room_id;
		}
		if ( ! $dateString ) {
			$dateString = $this->date;
		}
		if ( ! $excluded_reservation_id ) {
			$excluded_reservation_id = $this->reservation_id_excluded;
		}

		$reserved_room_count = $this->countReservationsForDay( $room_id = $roomId, $day = $dateString, $excluded_reservation_id );
	
		$max_count = \AtollMatrix\Rooms::getMaxQuantityForRoom( $roomId, $dateString );
		$avaiblable_count = $max_count - $reserved_room_count;
		if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
			$avaiblable_count = 0;
		}
		if ( 0 == $avaiblable_count ) {
			return true;
		}
		
		return false;
	}
	

	public function isRoom_Fullybooked_For_DateRange( $roomId = false, $checkin_date = false, $checkout_date = false, $reservationid = false ) {

		if ( ! $roomId ) {
			$roomId = $this->room_id;
		}
		if ( ! $reservationid ) {
			$reservationid = $this->reservation_id;
		}

		// get the date range
		$start = new \DateTime($checkin_date);
		$end = new \DateTime($checkout_date);
		$interval = new \DateInterval('P1D');
		$daterange = new \DatePeriod($start, $interval, $end);
	
		foreach ($daterange as $date) {
			// Check if the room is fully booked for the given date
			if ( $this->isRoom_For_Day_Fullybooked( $roomId, $date->format("Y-m-d"), $reservationid ) ) {
				// If the room is fully booked for any of the dates in the range, return true
				return true;
			}
		}
	
		// If the room is not fully booked for any of the dates in the range, return false
		return false;
	}

	public function isConfirmed_Reservation( $reservation_id ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the reservation status for the reservation
		$reservation_status = get_post_meta($reservation_id, 'pagemeta_reservation_status', true);
	
		if ( 'confirmed' == $reservation_status ) {
			return true;
		}
	
		return false;
	
	}

	public function remainingRooms_For_Day( $dateString = false, $room_id = false, $excluded_reservation_id = false ) {

		if ( ! $room_id ) {
			$room_id = $this->room_id;
		}
		if ( ! $dateString ) {
			$dateString = $this->date;
		}
		if ( ! $excluded_reservation_id ) {
			$excluded_reservation_id = $this->reservation_id_excluded;
		}
		
		$reserved_room_count = $this->countReservationsForDay( $room_id, $dateString, $excluded_reservation_id );
	
		$max_count = \AtollMatrix\Rooms::getMaxQuantityForRoom( $room_id, $dateString );
		$avaiblable_count = $max_count - $reserved_room_count;
		if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
			$avaiblable_count = 0;
		}
		
		return $avaiblable_count;
	}


	// Function to check if a date falls within a reservation
	public function isDate_Reserved( $dateString = false, $room_id = false ) {

		if ( ! $room_id ) {
			$room_id = $this->room_id;
		}
		if ( ! $dateString ) {
			$dateString = $this->date;
		}

		$currentDate = strtotime( $dateString );
		$start = false;

		$query = $this->getReservationsForRoom( $room_id );

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
					$post_room_id=$custom['pagemeta_room_name'][0];
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

				if ( $post_room_id == $room_id ) {
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

	}

	public function getReservation_Customer_ID( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta( $reservation_id, 'pagemeta_booking_number', true );
	
		if ( !$booking_number ) {
			// Handle error if booking number not found
			return '';
		}
	
		// Query the customer post with the matching booking number
		$customer_query = $this->getGuestforReservation( $booking_number );
	
		if ( $customer_query->have_posts() ) {
			$customer_post = $customer_query->posts[0];
	
			// Restore the original post data
			wp_reset_postdata();
	
			// Return the ID of the customer post
			return $customer_post->ID;
		}
	
		// No matching customer found
		return '';
	}

	public function haveCustomer( $reservation_id ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta($reservation_id, 'pagemeta_booking_number', true);
	
		if (!$booking_number) {
			// Handle error if booking number not found
			return false;
		}
	
		// Query the customer post with the matching booking number
		$customer_query = $this->getGuestforReservation( $booking_number );
	
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

	public function getCustomer_MetaData( $customer_array, $customer_post_id ) {
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

	/**
	 * Retrieves and validates the reservations array for the given room type
	 */
	public function getReservations_Array( $room_id ) {

		if ( ! $room_id ) {
			$room_id = $this->room_id;
		}

		$reservations_array = get_post_meta($room_id, 'reservations_array', true);

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

}
