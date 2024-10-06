<?php
namespace Staylodgic;

class Data {


	public function __construct() {
		// Hook into the save_post action
		add_action( 'save_post', array( $this, 'update_reservations_array_on_save' ), 13, 3 );
		add_action( 'save_post', array( $this, 'create_activities_customer_on_save' ), 13, 3 );
		// Hook into the wp_trash_post action
		add_action( 'wp_trash_post', array( $this, 'remove_reservation_from_array' ) );
		add_action( 'trashed_post', array( $this, 'remove_reservation_from_array' ) );
		add_action( 'save_post', array( $this, 'check_post_status_and_remove_reservation' ) );
	}

	/**
	 * Method check_post_status_and_remove_reservation
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function check_post_status_and_remove_reservation( $post_id ) {
		// Check if this is an autosave or a revision.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Get the post object.
		$post = get_post( $post_id );

		// Check if the post status is 'draft'.
		if ( 'draft' === $post->post_status ) {
			// Call your function to remove the reservation from the array.
			$this->remove_reservation_from_array( $post_id );
		}
	}

	/**
	 * Method get_customer_meta_data
	 *
	 * @param $customer_array
	 * @param $customer_post_id
	 *
	 * @return void
	 */
	public static function get_customer_meta_data( $customer_array, $customer_post_id ) {
		$output = array();

		// Loop through the customer array
		foreach ( $customer_array as $item ) {
			if ( 'seperator' !== $item['type'] ) {
				// Get the meta value for the current item's 'id'
				$meta_value = get_post_meta( $customer_post_id, $item['id'], true );
				// Add an entry to the output array, with 'name' as the key and the meta value as the value
				$output[ $item['name'] ] = $meta_value;
			}
		}

		return $output;
	}

	/**
	 * Method initiate_customer_save
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 *
	 * @return void
	 */
	public function initiate_customer_save( $post_id, $post, $update ) {
		$customer_choice   = get_post_meta( $post_id, 'staylodgic_customer_choice', true );
		$booking_number    = get_post_meta( $post_id, 'staylodgic_booking_number', true );
		$existing_customer = get_post_meta( $post_id, 'staylodgic_existing_customer', true );

		$full_name = get_post_meta( $post_id, 'staylodgic_full_name', true );

		// Check if customer post exists
		// error_log("customer_choice: " . $customer_choice . '||' . $booking_number);
		$customer_id = get_post_meta( $post_id, 'staylodgic_customer_id', true );
		// error_log("checking customer post: " . $customer_id . '||' . $post_id . '||' . $full_name);

		if ( \Staylodgic\Common::is_customer_valid_post( $existing_customer ) ) {
			if ( 'existing' == $customer_choice ) {

				// error_log("Updating: " . $existing_customer . '||' . $booking_number);
				update_post_meta( $post_id, 'staylodgic_customer_id', $existing_customer );

			}
		}

		// Check if the post is being trashed
		if ( 'trash' === $post->post_status ) {
			return; // Exit the function if the post is being trashed
		}

		if ( ! \Staylodgic\Common::is_customer_valid_post( $customer_id ) ) {
			if ( 'existing' !== $customer_choice ) {
				// error_log("Customer does not exist: " . $customer_id . '||' . $full_name);
				// Create new customer from the filled inputs in reservation
				self::create_customer_from_reservation_post( $post_id );
			}
		}
	}

	/**
	 * Method Triggered when a post is saved. If the post type is 'slgc_reservations' and is not autosaved or revision, it updates the reservation details.
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 *
	 * @return void
	 */
	public function create_activities_customer_on_save( $post_id, $post, $update ) {

		if ( ! \Staylodgic\Common::is_activities_valid_post( $post_id, $post ) ) {
			return;
		}

		$this->initiate_customer_save( $post_id, $post, $update );
	}

	/**
	 * Method create_customer_from_reservation_post
	 *
	 * @param $reservation_post_id
	 *
	 * @return void
	 */
	public function create_customer_from_reservation_post( $reservation_post_id ) {
		// Retrieve the reservation post using the ID
		$reservation_post = get_post( $reservation_post_id );
		$customer_post_id = false;

		if ( ! $reservation_post ) {
			// Handle error if reservation post not found
			return;
		}

		// Retrieve the necessary post meta data from the reservation post
		$full_name       = get_post_meta( $reservation_post_id, 'staylodgic_full_name', true );
		$email_address   = get_post_meta( $reservation_post_id, 'staylodgic_email_address', true );
		$phone_number    = get_post_meta( $reservation_post_id, 'staylodgic_phone_number', true );
		$street_address  = get_post_meta( $reservation_post_id, 'staylodgic_street_address', true );
		$city            = get_post_meta( $reservation_post_id, 'staylodgic_city', true );
		$state           = get_post_meta( $reservation_post_id, 'staylodgic_state', true );
		$zip_code        = get_post_meta( $reservation_post_id, 'staylodgic_zip_code', true );
		$country         = get_post_meta( $reservation_post_id, 'staylodgic_country', true );
		$booking_number  = get_post_meta( $reservation_post_id, 'staylodgic_booking_number', true );
		$customer_choice = get_post_meta( $reservation_post_id, 'staylodgic_customer_choice', true );

		if ( 'existing' !== $customer_choice ) {
			if ( '' !== $full_name ) {
				// Create customer post
				$customer_post_data = array(
					'post_type'   => 'slgc_customers', // Your custom post type for customers
					'post_title'  => $full_name, // Set the customer's full name as post title
					'post_status' => 'publish', // The status you want to give new posts
					'meta_input'  => array(
						'staylodgic_full_name'      => $full_name,
						'staylodgic_email_address'  => $email_address,
						'staylodgic_phone_number'   => $phone_number,
						'staylodgic_street_address' => $street_address,
						'staylodgic_city'           => $city,
						'staylodgic_state'          => $state,
						'staylodgic_zip_code'       => $zip_code,
						'staylodgic_country'        => $country,
						// add other meta data you need
					),
				);

				// Insert the post
				$customer_post_id = wp_insert_post( $customer_post_data );
			}
		}

		if ( ! $customer_post_id ) {
			// Handle error while creating customer post
			return;
		}

		// Update the reservation post with the customer post ID
		update_post_meta( $reservation_post_id, 'staylodgic_customer_id', $customer_post_id );
	}

	/**
	 * Method remove_reservation_from_array
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function remove_reservation_from_array( $post_id ) {
		// Check if the post is of the "reservations" post type
		if ( get_post_type( $post_id ) === 'slgc_reservations' ) {
			$room_type           = get_post_meta( $post_id, 'staylodgic_room_id', true );
			$reservation_post_id = $post_id;

			// Call the remove_reservation_from_array function
			self::remove_reservation_id( $room_type, $reservation_post_id );

			// Update the remaining room count
			if ( $room_type ) {
				$reservation_instance = new \Staylodgic\Reservations();
				try {
					$reservation_instance->update_remaining_room_count( $room_type );
				} catch ( \Exception $e ) {
					// Handle exceptions or log errors
					error_log( 'Error updating remaining room count: ' . $e->getMessage() );
				}
			}
		}
	}

	/**
	 * Method remove_reservation_id
	 *
	 * @param $room_type
	 * @param $reservation_post_id
	 *
	 * @return void
	 */
	public function remove_reservation_id( $room_type, $reservation_post_id ) {
		// Retrieve the reservations array for the room type
		$reservations_array_json = get_post_meta( $room_type, 'staylodgic_reservations_array', true );

		// Check if the reservations array is empty or not a JSON string
		if ( empty( $reservations_array_json ) || ! is_string( $reservations_array_json ) ) {
			return;
		}

		// Decode the reservations array from JSON to an array
		$reservations_array = json_decode( $reservations_array_json, true );

		// Check if the decoding was successful
		if ( null === $reservations_array ) {
			return;
		}

		// Convert the reservation post ID to a string for comparison
		$reservation_post_id = (string) $reservation_post_id;

		// Iterate over each date in the reservations array
		foreach ( $reservations_array as $date => &$reservation_ids ) {
			// Check if the reservation_ids is a JSON string
			if ( is_string( $reservation_ids ) ) {
				$reservation_ids = json_decode( $reservation_ids, true );
			}

			if ( is_array( $reservation_ids ) ) {
				// Check if the reservation post ID exists in the array
				$index = array_search( $reservation_post_id, $reservation_ids );
				if ( false !== $index ) {
					// Remove the reservation post ID from the array
					unset( $reservation_ids[ $index ] );
					// Reset the array keys
					$reservation_ids = array_values( $reservation_ids );
				}
			}

			// Check if there are no more reservation IDs in the array
			if ( empty( $reservation_ids ) ) {
				// Remove the date from the reservations array
				unset( $reservations_array[ $date ] );
			}
		}

		// Encode the reservations array back to JSON
		$reservations_array_json = wp_json_encode( $reservations_array );
		// Update the reservations array meta field

		update_post_meta( $room_type, 'staylodgic_reservations_array', $reservations_array_json );
	}

	/**
	 * Method Triggered when a post is saved. If the post type is 'slgc_reservations' and is not autosaved or revision, it updates the reservation details.
	 *
	 * @param $post_id $post_id
	 * @param $post $post
	 * @param $update $update
	 *
	 * @return void
	 */
	public function update_reservations_array_on_save( $post_id, $post, $update ) {

		if ( ! \Staylodgic\Common::is_reservation_valid_post( $post_id, $post ) ) {
			return;
		}

		$room_type          = get_post_meta( $post_id, 'staylodgic_room_id', true );
		$checkin_date       = get_post_meta( $post_id, 'staylodgic_checkin_date', true );
		$checkout_date      = get_post_meta( $post_id, 'staylodgic_checkout_date', true );
		$reservation_status = get_post_meta( $post_id, 'staylodgic_reservation_status', true );
		$customer_choice    = get_post_meta( $post_id, 'staylodgic_customer_choice', true );
		$booking_number     = get_post_meta( $post_id, 'staylodgic_booking_number', true );
		$existing_customer  = get_post_meta( $post_id, 'staylodgic_existing_customer', true );

		$full_name = get_post_meta( $post_id, 'staylodgic_full_name', true );

		self::remove_reservation_id_from_all_rooms( $post_id ); // Remove the reservation from all rooms

		$reservation_instance = new \Staylodgic\Reservations();
		if ( $reservation_instance->is_confirmed_reservation( $post_id ) ) {
			// Add reservation to the new room type
			self::update_reservations_array_on_change( $room_type, $checkin_date, $checkout_date, $post_id );
		}

		$this->initiate_customer_save( $post_id, $post, $update );

		// Check if the post is being trashed
		if ( 'trash' === $post->post_status ) {
			return; // Exit the function if the post is being trashed
		}

		if ( $room_type ) {
			$reservation_instance = new \Staylodgic\Reservations();
			$reservation_instance->update_remaining_room_count( $room_type );
		}
	}

	/**
	 * Method Updates the reservations array when changes are made to a reservation post.
	 *
	 * @param $room_id
	 * @param $checkin_date
	 * @param $checkout_date
	 * @param $reservation_post_id
	 *
	 * @return void
	 */
	public static function update_reservations_array_on_change( $room_id, $checkin_date, $checkout_date, $reservation_post_id ) {
		$reservation_instance = new \Staylodgic\Reservations();
		$reservations_array   = $reservation_instance->get_reservations_array( $room_id );

		$previous_checkin_date  = get_post_meta( $reservation_post_id, 'staylodgic_previous_checkin_date', true );
		$previous_checkout_date = get_post_meta( $reservation_post_id, 'staylodgic_previous_checkout_date', true );

		// Adjust the checkout dates to be one day earlier
		$previous_checkout_date = gmdate( 'Y-m-d', strtotime( $previous_checkout_date . ' -1 day' ) );
		$adjusted_checkout_date = gmdate( 'Y-m-d', strtotime( $checkout_date . ' -1 day' ) );

		$previous_dates = \Staylodgic\Common::get_dates_between( $previous_checkin_date, $previous_checkout_date );
		$updated_dates  = \Staylodgic\Common::get_dates_between( $checkin_date, $adjusted_checkout_date );

		$reservations_array = self::remove_dates_from_reservations_array( $previous_dates, $reservation_post_id, $reservations_array );
		$reservations_array = self::add_dates_to_reservations_array( $updated_dates, $reservation_post_id, $reservations_array );

		update_post_meta( $room_id, 'staylodgic_reservations_array', wp_json_encode( $reservations_array ) );
		update_post_meta( $reservation_post_id, 'staylodgic_previous_checkin_date', $checkin_date );
		update_post_meta( $reservation_post_id, 'staylodgic_previous_checkout_date', $checkout_date ); // Keeping original checkout date for records
	}

	/**
	 * Method Remove dates from the reservations array for a given reservation post ID.
	 *
	 * @param $dates
	 * @param $reservation_post_id
	 * @param $reservations_array
	 *
	 * @return void
	 */
	public static function remove_dates_from_reservations_array( $dates, $reservation_post_id, $reservations_array ) {
		foreach ( $dates as $date ) {
			if ( isset( $reservations_array[ $date ] ) ) {
				$reservation_ids = $reservations_array[ $date ];
				$key             = array_search( $reservation_post_id, $reservation_ids );

				if ( false !== $key ) {
					unset( $reservations_array[ $date ][ $key ] );
					// Reset the array keys
					$reservations_array[ $date ] = array_values( $reservations_array[ $date ] );
				}
			}
		}

		return $reservations_array;
	}

	/**
	 * Method Add dates to the reservations array for a given reservation post ID.
	 *
	 * @param $dates
	 * @param $reservation_post_id
	 * @param $reservations_array
	 *
	 * @return void
	 */
	public static function add_dates_to_reservations_array( $dates, $reservation_post_id, $reservations_array ) {
		foreach ( $dates as $date ) {
			if ( isset( $reservations_array[ $date ] ) ) {
				if ( is_array( $reservations_array[ $date ] ) ) {
					$reservations_array[ $date ][] = $reservation_post_id;
				} else {
					$reservations_array[ $date ] = array( $reservations_array[ $date ], $reservation_post_id );
				}
			} else {
				$reservations_array[ $date ] = array( $reservation_post_id );
			}
		}

		return $reservations_array;
	}

	/**
	 * Method Remove the reservation ID from the entire array
	 *
	 * @param $reservation_post_id
	 * @param $reservations_array
	 *
	 * @return void
	 */
	public static function remove_ids_from_reservations_array( $reservation_post_id, $reservations_array ) {
		foreach ( $reservations_array as $date => &$reservations ) {
			foreach ( $reservations as $key => $id ) {
				if ( $id === $reservation_post_id ) {
					unset( $reservations[ $key ] );
				}
			}
			// Reset the array keys
			$reservations = array_values( $reservations );
		}

		return $reservations_array;
	}

	/**
	 * Method Remove the reservation from all rooms.
	 *
	 * @param $reservation_post_id
	 *
	 * @return void
	 */
	public static function remove_reservation_id_from_all_rooms( $reservation_post_id ) {
		$room_types = get_posts( array( 'post_type' => 'slgc_room' ) );
		// Remove reservation_from_all_rooms is called with ID
		foreach ( $room_types as $room ) {

			$reservation_instance = new \Staylodgic\Reservations();
			$reservations_array   = $reservation_instance->get_reservations_array( $room->ID );

			if ( ! empty( $reservations_array ) ) {
				$reservations_array = self::remove_ids_from_reservations_array( $reservation_post_id, $reservations_array );
			}

			update_post_meta( $room->ID, 'staylodgic_reservations_array', wp_json_encode( $reservations_array ) );
		}
	}
}
$instance = new \Staylodgic\Data();
