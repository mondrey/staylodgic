<?php
namespace Cognitive;
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

}
