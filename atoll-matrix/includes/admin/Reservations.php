<?php
namespace Cognitive;
class Reservations {

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
		
		$query = new \WP_Query($args);

		return $query;
	}

}
