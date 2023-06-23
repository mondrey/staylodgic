<?php
namespace AtollMatrix;
class Rooms {

	public function __construct( $date = false, $room_id = false, $reservation_id = false, $reservation_id_excluded = false ) {
		// AJAX handler to save room metadata
		add_action('wp_ajax_update_RoomAvailability', array($this, 'update_RoomAvailability'));
		add_action('wp_ajax_nopriv_update_RoomAvailability', array($this, 'update_RoomAvailability'));

		// AJAX handler to save room metadata
		add_action('wp_ajax_update_RoomRate', array($this, 'update_RoomRate'));
		add_action('wp_ajax_nopriv_update_RoomRate', array($this, 'update_RoomRate'));
	}

	public static function queryRooms() {
		$rooms = get_posts(array(
			'post_type' => 'room',
			'orderby' => 'title',
			'numberposts' => -1,
			'order' => 'ASC',
			'post_status' => 'publish'
		));
		return $rooms;
	}

	public static function getRoomList() {
		$roomlist = [];
		$rooms = self::queryRooms();  // Call queryRooms() method here
		if ( $rooms ) {
			foreach( $rooms as $key => $list) {
				$roomlist[$list->ID] = $list->post_title;
			}
		} else {
			$roomlist[0]="Rooms not found.";
		}
		return $roomlist;
	}

	public static function getMaxQuantityForRoom($postID, $dateString) {
		$quantityArray = get_post_meta($postID, 'quantity_array', true);
		
		// Check if the quantity_array exists and the date is available
		if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
			return $quantityArray[$dateString];
		}
		
		return false;
	}

	public static function getRoomNames_FromIDs($room_ids) {
		$room_names = array();
	
		foreach ($room_ids as $room_id) {
			// Use the room ID to get the room's post title
			$room_post = get_post($room_id);
			if ($room_post) {
				$room_names[] = $room_post->post_title;
			}
		}
	
		$room_names_list = '<ul>';
		foreach ($room_names as $room_name) {
			$room_names_list .= '<li>' . $room_name . '</li>';
		}
		$room_names_list .= '</ul>';
	
		return $room_names_list;
	}

	public function getAvailableRooms_For_DateRange( $checkin_date, $checkout_date ) {
		$available_rooms = array();
	
		// get all rooms
		$room_list = $this->queryRooms();
	
		foreach($room_list as $room) {
			$count = $this->getMaxRoom_QTY_For_DateRange($room->ID, $checkin_date, $checkout_date, $reservationid = '');
			
			// if not fully booked add to available rooms
			if ( $count !== 0 ) {
				$available_rooms[$room->ID][$count] = $room->post_title; // changed here
			}
		}
	
		return $available_rooms;
	}

	public function getMaxRoom_QTY_For_DateRange($roomId, $checkin_date, $checkout_date, $reservationid) {
		// get the date range
		$start = new \DateTime($checkin_date);
		$end = new \DateTime($checkout_date);
		$interval = new \DateInterval('P1D');
		$daterange = new \DatePeriod($start, $interval, $end);
	
		$max_count = PHP_INT_MAX;
	
		foreach ($daterange as $date) {
			// Check if the room is fully booked for the given date
			$count = $this->getMaxRoom_QTY_ForDay($roomId, $date->format("Y-m-d"), $reservationid);
	
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
	
	
	public function getMaxRoom_QTY_ForDay($roomId, $dateString, $excluded_reservation_id = null) {
	
		$reservation_instance = new \AtollMatrix\Reservations( $dateString, $roomId, $reservation_id = false, $excluded_reservation_id);
		$reserved_room_count = $reservation_instance->countReservationsForDay();
	
		$max_count = \AtollMatrix\Rooms::getMaxQuantityForRoom( $roomId, $dateString );
		$avaiblable_count = $max_count - $reserved_room_count;
		if ( empty( $avaiblable_count ) || !isset( $avaiblable_count) ) {
			$avaiblable_count = 0;
		}
		return $avaiblable_count;
	}

	public function update_RoomAvailability() {
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


	public function update_RoomRate() {
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



}

$instance = new \AtollMatrix\Rooms();
