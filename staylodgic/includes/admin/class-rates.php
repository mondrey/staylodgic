<?php

namespace Staylodgic;

class Rates {

	/**
	 * Method get_room_rate_by_date
	 *
	 * @param $the_room_id
	 * @param $date
	 *
	 * @return void
	 */
	public static function get_room_rate_by_date( $room_id, $date ) {
		// Get the room rate array from the post meta data.
		$room_rate_array = get_post_meta( $room_id, 'staylodgic_roomrate_array', true );

		// If the room rate array is set and the date exists in the array, return the rate.
		if ( is_array( $room_rate_array ) && isset( $room_rate_array[ $date ] ) ) {
			return $room_rate_array[ $date ];
		}

		$rate = self::getRoomTypeBaseRate( $room_id );
		return $rate;
	}

	/**
	 * Method getRoomTypeBaseRate
	 *
	 * @param $room_id
	 *
	 * @return void
	 */
	public static function getRoomTypeBaseRate( $room_id ) {
		$custom = get_post_custom( $room_id );
		if ( isset( $custom['staylodgic_base_rate'][0] ) ) {
			$base_rate = $custom['staylodgic_base_rate'][0];

			return $base_rate;
		}
		return false;
	}
}
