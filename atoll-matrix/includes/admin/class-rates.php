<?php
namespace AtollMatrix;
class Rates {

	public static function getRoomRateByDate( $roomID, $date ) {
		// Get the room rate array from the post meta data.
		$roomRateArray = get_post_meta($roomID, 'roomrate_array', true);

		// If the room rate array is set and the date exists in the array, return the rate.
		if ( is_array( $roomRateArray ) && isset( $roomRateArray[$date] )) {
			return $roomRateArray[$date];
		}

		$rate = self::getRoomTypeBaseRate( $roomID );
		return $rate;
	}

	public static function getRoomTypeBaseRate( $room_id ) {
		$custom = get_post_custom( $room_id );
		if (isset($custom['atollmatrix_base_rate'][0])) {
			$base_rate = $custom['atollmatrix_base_rate'][0];
	
			return $base_rate;
		}
		return false;
	}

}
