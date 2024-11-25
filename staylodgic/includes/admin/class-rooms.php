<?php

namespace Staylodgic;

class Rooms {


	public function __construct( $date = false, $room_id = false, $reservation_id = false, $reservation_id_excluded = false ) {
		// AJAX handler to save room metadata
		add_action( 'wp_ajax_update_room_availability', array( $this, 'update_room_availability' ) );
		add_action( 'wp_ajax_nopriv_update_room_availability', array( $this, 'update_room_availability' ) );

		// AJAX handler to save room metadata
		add_action( 'wp_ajax_update_room_rate', array( $this, 'update_room_rate' ) );
		add_action( 'wp_ajax_nopriv_update_room_rate', array( $this, 'update_room_rate' ) );
	}

	/**
	 * Method has_rooms
	 *
	 * @return void
	 */
	public static function has_rooms() {
		$args = array(
			'post_type'      => 'slgc_room',
			'posts_per_page' => 1, // Only need to check if at least one room exists
			'fields'         => 'ids', // Only retrieve the post IDs
			'post_status'    => 'publish',
		);

		$query = new \WP_Query( $args );

		return $query->have_posts(); // Returns true if there is at least one room, false otherwise
	}

	/**
	 * Method query_rooms
	 *
	 * @return void
	 */
	public static function query_rooms() {
		$rooms = get_posts(
			array(
				'post_type'   => 'slgc_room',
				'numberposts' => -1,
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'post_status' => 'publish',
			)
		);
		return $rooms;
	}

	/**
	 * Method get_room_list
	 *
	 * @return void
	 */
	public static function get_room_list() {
		$roomlist = array();
		$rooms    = self::query_rooms(); // Call query_rooms() method here
		if ( $rooms ) {
			foreach ( $rooms as $key => $list ) {
				$roomlist[ $list->ID ] = $list->post_title;
			}
		} else {
			$roomlist[0] = 'Rooms not found.';
		}
		return $roomlist;
	}

	/**
	 * Method is_channel_room_booked
	 *
	 * @param $room_id $room_id [explicite description]
	 * @param $stay_date_string $stay_date_string [explicite description]
	 *
	 * @return void
	 */
	public static function is_channel_room_booked( $room_id, $stay_date_string ) {
		$channel_array = get_post_meta( $room_id, 'staylodgic_channel_quantity_array', true );

		// Check if the channel_quantity_array exists and the quanitity field is available
		if ( ! empty( $channel_array ) && isset( $channel_array['quantity'] ) ) {
			$stay_quantity_array = $channel_array['quantity'];
		}

		// Check if the quantity_array exists and the date is available
		if ( ! empty( $stay_quantity_array ) && isset( $stay_quantity_array[ $stay_date_string ] ) ) {
			if ( '0' === $stay_quantity_array[ $stay_date_string ] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Method get_total_operating_room_qty_for_date
	 *
	 * @param $room_id $room_id [explicite description]
	 * @param $stay_date_string $stay_date_string [explicite description]
	 *
	 * @return void
	 */
	public static function get_total_operating_room_qty_for_date( $room_id, $stay_date_string ) {

		$stay_quantity_array = get_post_meta( $room_id, 'staylodgic_quantity_array', true );

		// Check if the quantity_array exists and the date is available
		if ( ! empty( $stay_quantity_array ) && isset( $stay_quantity_array[ $stay_date_string ] ) ) {
			return $stay_quantity_array[ $stay_date_string ];
		}

		return false;
	}

	/**
	 * Method get_max_quantity_for_room
	 *
	 * @param $room_id $room_id [explicite description]
	 * @param $stay_date_string $stay_date_string [explicite description]
	 *
	 * @return void
	 */
	public static function get_max_quantity_for_room( $room_id, $stay_date_string ) {

		if ( self::is_channel_room_booked( $room_id, $stay_date_string ) ) {
			return '0';
		}
		$stay_quantity_array = get_post_meta( $room_id, 'staylodgic_quantity_array', true );

		// Check if the quantity_array exists and the date is available
		if ( ! empty( $stay_quantity_array ) && isset( $stay_quantity_array[ $stay_date_string ] ) ) {
			return $stay_quantity_array[ $stay_date_string ];
		}

		return false;
	}

	/**
	 * Method get_room_name_from_id
	 *
	 * @param $room_id $room_id [explicite description]
	 *
	 * @return void
	 */
	public static function get_room_name_from_id( $room_id ) {
		$room_post = get_post( $room_id );
		if ( $room_post ) {
			$room_name = $room_post->post_title;
		}

		return $room_name;
	}

	/**
	 * Method get_room_names_from_ids
	 *
	 * @param $room_ids $room_ids [explicite description]
	 *
	 * @return void
	 */
	public static function get_room_names_from_ids( $room_ids ) {
		$room_names = array();

		foreach ( $room_ids as $room_id ) {
			// Use the room ID to get the room's post title
			$room_post = get_post( $room_id );
			if ( $room_post ) {
				$room_names[] = $room_post->post_title;
			}
		}

		$room_names_list = '<ul>';
		foreach ( $room_names as $room_name ) {
			$room_names_list .= '<li>' . $room_name . '</li>';
		}
		$room_names_list .= '</ul>';

		return $room_names_list;
	}

	/**
	 * Method get_available_rooms_for_date_range
	 *
	 * @param $checkin_date $checkin_date [explicite description]
	 * @param $checkout_date $checkout_date [explicite description]
	 *
	 * @return void
	 */
	public function get_available_rooms_for_date_range( $checkin_date, $checkout_date ) {
		$available_rooms = array();

		// get all rooms
		$room_list = $this->query_rooms();

		foreach ( $room_list as $room ) {
			$count = $this->get_max_room_qty_for_date_range( $room->ID, $checkin_date, $checkout_date, $reservationid = '' );

			// if not fully booked add to available rooms
			if ( 0 !== (int) $count ) {
				$available_rooms[ $room->ID ][ $count ] = $room->post_title; // changed here
			}
		}

		return $available_rooms;
	}

	/**
	 * Method get_available_rooms_rates_occupants_for_date_range
	 *
	 * @param $checkin_date $checkin_date [explicite description]
	 * @param $checkout_date $checkout_date [explicite description]
	 *
	 * @return void
	 */
	public function get_available_rooms_rates_occupants_for_date_range( $checkin_date, $checkout_date ) {
		$combo_array         = array();
		$available_rooms     = array();
		$available_roomrates = array();
		$can_accomodate      = array();

		// get all rooms
		$room_list = self::query_rooms();

		foreach ( $room_list as $room ) {
			$count = self::get_max_room_qty_for_date_range( $room->ID, $checkin_date, $checkout_date, $reservationid = '' );

			// if not fully booked add to available rooms
			if ( 0 !== (int) $count ) {
				$available_rooms[ $room->ID ][ $count ] = $room->post_title; // changed here

				$available_roomrates[ $room->ID ] = self::get_room_rate_for_date_range( $room->ID, $checkin_date, $checkout_date );
				// Get room occupany max numbers
				$can_accomodate[ $room->ID ] = self::get_max_room_occupants( $room->ID );

			}
		}

		$combo_array = array(
			'rooms'     => $available_rooms,
			'rates'     => $available_roomrates,
			'occupants' => $can_accomodate,
		);

		return $combo_array;
	}

	/**
	 * Method get_room_rate_for_date_range
	 *
	 * @param $stay_room_id $stay_room_id [explicite description]
	 * @param $checkin_date $checkin_date [explicite description]
	 * @param $checkout_date $checkout_date [explicite description]
	 *
	 * @return void
	 */
	public function get_room_rate_for_date_range( $stay_room_id, $checkin_date, $checkout_date ) {
		$start = new \DateTime( $checkin_date );
		$end   = new \DateTime( $checkout_date );

		// Add one day to the end date
		$end->add( new \DateInterval( 'P1D' ) );

		$interval  = new \DateInterval( 'P1D' );
		$daterange = new \DatePeriod( $start, $interval, $end );

		$rates_daterange   = array();
		$roomrate_instance = new \Staylodgic\Rates();

		$total_rate = 0;

		foreach ( $daterange as $date ) {

			$rate = $roomrate_instance->get_room_rate_by_date( $stay_room_id, $date->format( 'Y-m-d' ) );
			$rates_daterange['date'][ $date->format( 'Y-m-d' ) ] = $rate;
			$total_rate = $total_rate + $rate;
		}

		$rates_daterange['total'] = $total_rate;

		return $rates_daterange;
	}

	/**
	 * Method get_max_room_occupants
	 *
	 * @param $room_id $room_id [explicite description]
	 *
	 * @return void
	 */
	public function get_max_room_occupants( $room_id ) {

		$max_children   = '999';
		$max_adults     = '999';
		$max_guests     = 0;
		$can_occomodate = array();
		$can_occomodate = array();

		$room_data = get_post_custom( $room_id );
		if ( isset( $room_data['staylodgic_max_adult_limit_status'][0] ) ) {
			$adult_limit_status = $room_data['staylodgic_max_adult_limit_status'][0];
			if ( '1' === $adult_limit_status ) {
				$max_adults = $room_data['staylodgic_max_adults'][0];
			} else {
				$max_adults = '999';
			}
		}
		if ( isset( $room_data['staylodgic_max_children_limit_status'][0] ) ) {
			$children_limit_status = $room_data['staylodgic_max_children_limit_status'][0];
			if ( '1' === $children_limit_status ) {
				$max_children = $room_data['staylodgic_max_children'][0];
			} else {
				$max_children = '999';
			}
		}
		if ( isset( $room_data['staylodgic_max_guests'][0] ) ) {
			$max_guests = $room_data['staylodgic_max_guests'][0];
		}

		$can_occomodate['adults']   = $max_adults;
		$can_occomodate['children'] = $max_children;
		$can_occomodate['guests']   = $max_guests;

		return $can_occomodate;
	}

	/**
	 * Method get_max_room_qty_for_date_range
	 *
	 * @param $stay_room_id $stay_room_id [explicite description]
	 * @param $checkin_date $checkin_date [explicite description]
	 * @param $checkout_date $checkout_date [explicite description]
	 * @param $reservationid $reservationid [explicite description]
	 *
	 * @return void
	 */
	public function get_max_room_qty_for_date_range( $stay_room_id, $checkin_date, $checkout_date, $reservationid ) {
		// get the date range
		$start = new \DateTime( $checkin_date );
		$end   = new \DateTime( $checkout_date );
		// Add one day to the end date
		$end->add( new \DateInterval( 'P1D' ) );

		$interval  = new \DateInterval( 'P1D' );
		$daterange = new \DatePeriod( $start, $interval, $end );

		$max_count = PHP_INT_MAX;

		foreach ( $daterange as $date ) {
			// Check if the room is fully booked for the given date
			$count = $this->get_max_room_qty_for_day( $stay_room_id, $date->format( 'Y-m-d' ), $reservationid );

			if ( $count < $max_count ) {
				$max_count = $count;
			}
		}

		// If no count was ever set, return false or whatever default value you need
		if ( PHP_INT_MAX === $max_count ) {
			return false;
		}

		// If the room is not fully booked for any of the dates in the range, return max_count
		return $max_count;
	}

	/**
	 * Method get_max_room_qty_for_day
	 *
	 * @param $stay_room_id $stay_room_id [explicite description]
	 * @param $stay_date_string $stay_date_string [explicite description]
	 * @param $excluded_reservation_id $excluded_reservation_id [explicite description]
	 *
	 * @return void
	 */
	public function get_max_room_qty_for_day( $stay_room_id, $stay_date_string, $excluded_reservation_id = null ) {

		$reservation_instance = new \Staylodgic\Reservations( $stay_date_string, $stay_room_id, $reservation_id = false, $excluded_reservation_id );
		$reserved_room_count  = $reservation_instance->count_reservations_for_day();

		$max_count        = self::get_max_quantity_for_room( $stay_room_id, $stay_date_string );
		$avaiblable_count = $max_count - $reserved_room_count;
		if ( empty( $avaiblable_count ) || ! isset( $avaiblable_count ) ) {
			$avaiblable_count = 0;
		}
		return $avaiblable_count;
	}

	/**
	 * Method update_room_availability
	 *
	 * @return void
	 */
	public function update_room_availability() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_availabilitycalendar_nonce'] ) || ! check_admin_referer( 'staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		if ( isset( $_POST['date_range'] ) ) {
			// Unslash and sanitize the input
			$date_range = sanitize_text_field( wp_unslash( $_POST['date_range'] ) );
		} else {
			// Return an error response if date_range is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '101',
					'message' => 'Missing date range parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		if ( isset( $_POST['quantity'] ) ) {
			$quantity = $_POST['quantity'];

			if ( '' === $quantity ) {
				$quantity = 0;
				// Return an error response if quantity is not set
				$response = array(
					'success' => false,
					'data'    => array(
						'code'    => '102',
						'message' => 'Missing quantity parameter.',
					),
				);
				wp_send_json_error( $response );
				return;
			}
			if ( 0 > $quantity ) {
				$quantity = 0;
				// Return an error response if quantity is not set
				$response = array(
					'success' => false,
					'data'    => array(
						'code'    => '102',
						'message' => 'Missing quantity parameter.',
					),
				);
				wp_send_json_error( $response );
				return;
			}
		} else {
			// Return an error response if quantity is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '102',
					'message' => 'Missing quantity parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		if ( isset( $_POST['stay_post_id'] ) ) {
			$stay_post_id = $_POST['stay_post_id'];
		} else {
			// Return an error response if stay_post_id is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '103',
					'message' => 'Missing post ID parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		// Split the date range into start and end dates
		$date_range_array = explode( ' to ', $date_range );
		if ( count( $date_range_array ) < 2 && ! empty( $date_range_array[0] ) ) {
			// Use the single date as both start and end date
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $stay_start_date;
		} elseif ( count( $date_range_array ) < 2 && empty( $date_range_array[0] ) ) {
			// Return an error response if date_range is invalid
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '104',
					'message' => 'Invalid date range.',
				),
			);
			wp_send_json_error( $response );
			return;
		} else {
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $date_range_array[1];
		}

		// If the end date is empty, set it to the start date
		if ( empty( $stay_end_date ) ) {
			$stay_end_date = $stay_start_date;
		}

		$number_of_days_in_selection = \Staylodgic\Common::count_days_between_dates( $stay_start_date, $stay_end_date );

		if ( $number_of_days_in_selection > 64 ) {
			// Return an error response if date_range is invalid
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '105',
					'message' => 'Too many days to process.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		// Retrieve the existing quantity_array meta value
		$stay_quantity_array = get_post_meta( $stay_post_id, 'staylodgic_quantity_array', true );

		// If the quantity_array is not an array, initialize it as an empty array
		if ( ! is_array( $stay_quantity_array ) ) {
			$stay_quantity_array = array();
		}

		// Generate an array of dates between the start and end dates
		$date_range = \Staylodgic\Common::create_in_between_date_range_array( $stay_start_date, $stay_end_date );

		$reservation_instance = new \Staylodgic\Reservations();
		$reserved_array       = $reservation_instance->get_room_reservations_for_date_range( $stay_start_date, $stay_end_date, $stay_post_id );

		$room_data = get_post_custom( $stay_post_id );
		$max_rooms = 0;
		if ( isset( $room_data['staylodgic_max_rooms_of_type'][0] ) ) {
			$max_rooms = $room_data['staylodgic_max_rooms_of_type'][0];
		}

		// Update the quantity values for the specified date range
		foreach ( $date_range as $date ) {

			$reservation_instance = new \Staylodgic\Reservations( $date, $stay_post_id );

			$reserved_rooms = $reserved_array[ $date ];
			$final_quantity = $quantity + $reserved_rooms;

			$prevent_max_room_exceed = false;
			if ( $prevent_max_room_exceed ) {
				if ( $max_rooms < $final_quantity ) {
					$response = array(
						'success' => false,
						'data'    => array(
							'code'    => '106',
							'message' => 'Exceeds maximum total (' . esc_attr( $max_rooms ) . ') quantity for this room.',
						),
					);
					wp_send_json_error( $response );
					return;
				}
			}

			$stay_quantity_array[ $date ] = $final_quantity;
		}

		// Update the metadata for the 'slgc_reservations' post
		if ( ! empty( $stay_post_id ) && is_numeric( $stay_post_id ) && is_array( $stay_quantity_array ) ) {
			// Update the post meta with the modified quantity array
			update_post_meta( $stay_post_id, 'staylodgic_quantity_array', $stay_quantity_array );
			// Return a success response
			$response = array(
				'success' => true,
				'data'    => array(
					'message' => 'Room availability updated successfully.',
				),
			);
			wp_send_json_success( $response );
		} else {
			// Return an error response
			$response = array(
				'success' => false,
				'data'    => array(
					'code'    => '107',
					'message' => 'Invalid post ID.',
				),
			);
			wp_send_json_error( $response );
		}

		wp_die(); // Optional: Terminate script execution
	}

	/**
	 * Method update_room_rate
	 *
	 * @return void
	 */
	public function update_room_rate() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_availabilitycalendar_nonce'] ) || ! check_admin_referer( 'staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		if ( isset( $_POST['date_range'] ) ) {
			$date_range = $_POST['date_range'];
		} else {
			// Return an error response if date_range is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Missing date range parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		if ( isset( $_POST['rate'] ) ) {
			$rate = $_POST['rate'];

			if ( '' === $rate ) {
				$response = array(
					'success' => false,
					'data'    => array(
						'message' => 'Invalid rate',
					),
				);
				wp_send_json_error( $response );
				return;
			}
			if ( 0 >= $rate ) {
				$response = array(
					'success' => false,
					'data'    => array(
						'message' => 'Invalid rate',
					),
				);
				wp_send_json_error( $response );
				return;
			}
		} else {
			// Return an error response if quantity is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Missing rate parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		if ( isset( $_POST['stay_post_id'] ) ) {
			$stay_post_id = $_POST['stay_post_id'];
		} else {
			// Return an error response if stay_post_id is not set
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Missing post ID parameter.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		// Split the date range into start and end dates
		$date_range_array = explode( ' to ', $date_range );
		if ( count( $date_range_array ) < 2 && ! empty( $date_range_array[0] ) ) {
			// Use the single date as both start and end date
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $stay_start_date;
		} elseif ( count( $date_range_array ) < 2 && empty( $date_range_array[0] ) ) {
			// Return an error response if date_range is invalid
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Invalid date range.',
				),
			);
			wp_send_json_error( $response );
			return;
		} else {
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $date_range_array[1];
		}

		// If the end date is empty, set it to the start date
		if ( empty( $stay_end_date ) ) {
			$stay_end_date = $stay_start_date;
		}

		$number_of_days_in_selection = \Staylodgic\Common::count_days_between_dates( $stay_start_date, $stay_end_date );

		if ( $number_of_days_in_selection > 64 ) {
			// Return an error response if date_range is invalid
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Too many days to process.',
				),
			);
			wp_send_json_error( $response );
			return;
		}

		// Retrieve the existing roomrate_array meta value
		$got_room_rate_array = get_post_meta( $stay_post_id, 'staylodgic_roomrate_array', true );

		// If the quantity_array is not an array, initialize it as an empty array
		if ( ! is_array( $got_room_rate_array ) ) {
			$got_room_rate_array = array();
		}

		// Generate an array of dates between the start and end dates
		$date_range = \Staylodgic\Common::create_in_between_date_range_array( $stay_start_date, $stay_end_date );

		// Update the quantity values for the specified date range
		foreach ( $date_range as $date ) {
			$got_room_rate_array[ $date ] = $rate;
		}

		// Update the metadata for the 'slgc_reservations' post
		if ( ! empty( $stay_post_id ) && is_numeric( $stay_post_id ) ) {
			// Update the post meta with the modified quantity array
			update_post_meta( $stay_post_id, 'staylodgic_roomrate_array', $got_room_rate_array );
			// Return a success response
			$response = array(
				'success' => true,
				'data'    => array(
					'message' => 'Room rates updated successfully.',
				),
			);
			wp_send_json_success( $response );
		} else {
			// Return an error response
			$response = array(
				'success' => false,
				'data'    => array(
					'message' => 'Invalid post ID.',
				),
			);
			wp_send_json_error( $response );
		}

		wp_die(); // Optional: Terminate script execution
	}
}

$instance = new \Staylodgic\Rooms();
