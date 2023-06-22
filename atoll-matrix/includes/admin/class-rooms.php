<?php
namespace AtollMatrix;
class Rooms {

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

}
