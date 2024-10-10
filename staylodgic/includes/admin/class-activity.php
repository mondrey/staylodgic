<?php

namespace Staylodgic;

class Activity {


	protected $stay_booking_number;
	private $reservation_id;
	protected $stay_checkin_date;
	protected $staynights;
	protected $stay_adult_guests;
	protected $stay_children_guests;
	protected $children_age;
	protected $stay_total_guests;
	protected $activities_array;

	public function __construct(
		$stay_booking_number = null,
		$reservation_id = false,
		$stay_checkin_date = null,
		$staynights = null,
		$stay_adult_guests = null,
		$stay_children_guests = null,
		$children_age = null,
		$stay_total_guests = null,
		$activities_array = array()
	) {
		add_action( 'wp_ajax_get_activity_schedules', array( $this, 'get_activity_schedules_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_get_activity_schedules', array( $this, 'get_activity_schedules_ajax_handler' ) );

		add_action( 'wp_ajax_get_activity_frontend_schedules', array( $this, 'get_activity_frontend_schedules_ajax_handler' ) );
		add_action( 'wp_ajax_nopriv_get_activity_frontend_schedules', array( $this, 'get_activity_frontend_schedules_ajax_handler' ) );

		add_action( 'wp_ajax_process_selected_activity', array( $this, 'process_selected_activity' ) );
		add_action( 'wp_ajax_nopriv_process_selected_activity', array( $this, 'process_selected_activity' ) );

		add_action( 'wp_ajax_book_activity', array( $this, 'book_activity' ) );
		add_action( 'wp_ajax_nopriv_book_activity', array( $this, 'book_activity' ) );

		add_shortcode( 'activity_booking_search', array( $this, 'activity_search_shortcode' ) );

		add_filter( 'the_content', array( $this, 'activity_content' ) );

		$this->stay_booking_number  = uniqid();
		$this->reservation_id       = $reservation_id;
		$this->stay_checkin_date    = $stay_checkin_date;
		$this->staynights           = $staynights;
		$this->stay_adult_guests    = $stay_adult_guests;
		$this->stay_children_guests = $stay_children_guests;
		$this->children_age         = $children_age;
		$this->stay_total_guests    = $stay_total_guests;
		$this->activities_array     = $activities_array;
	}

	/**
	 * Get the reservation status
	 *
	 * @param $reservation_id
	 *
	 * @return string
	 */
	public function get_reservation_status( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}

		$reservation_status = get_post_meta( $reservation_id, 'staylodgic_reservation_status', true );

		return $reservation_status;
	}

	/**
	 * Get the reservation sub status
	 *
	 * @param $reservation_id
	 *
	 * @return string $reservation_substatus
	 */
	public function get_reservation_sub_status( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}

		$reservation_substatus = get_post_meta( $reservation_id, 'staylodgic_reservation_substatus', true );

		return $reservation_substatus;
	}

	/**
	 * Get Checkin Date
	 *
	 * @param $reservation_id
	 *
	 * @return string $checkin_date
	 */
	public function get_checkin_date( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}

		$checkin_date = get_post_meta( $reservation_id, 'staylodgic_reservation_checkin', true );

		return $checkin_date;
	}

	/**
	 * Get Customer Edit Link For Reservation
	 *
	 * @param $reservation_id
	 *
	 * @return string $customer_name
	 */
	public function get_customer_edit_link_for_reservation( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the customer post ID from the reservation's meta data
		$customer_post_id = get_post_meta( $reservation_id, 'staylodgic_customer_id', true );

		if ( $customer_post_id ) {
			// Check if the customer post exists
			$customer_post = get_post( $customer_post_id );
			if ( $customer_post ) {
				// Get the admin URL and create the link
				$edit_link = admin_url( 'post.php?post=' . esc_attr( $customer_post_id ) . '&action=edit' );
				return '<a href="' . esc_url( $edit_link ) . '">' . esc_html( $customer_post->post_title ) . '</a>';
			}
		} else {
			// If customer post doesn't exist, retrieve customer name from reservation post
			$reservation_post = get_post( $reservation_id );
			if ( $reservation_post ) {
				$customer_name = get_post_meta( $reservation_id, 'staylodgic_full_name', true );
				if ( ! empty( $customer_name ) ) {
					return $customer_name;
				}
			}
		}

		// Return null if no customer was found for the reservation
		return null;
	}

	/**
	 * Method get_booking_number
	 *
	 * @return string $booking_number
	 */
	public function get_booking_number() {
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta( $this->reservation_id, 'staylodgic_booking_number', true );

		if ( ! $booking_number ) {
			// Handle error if booking number not found
			return '';
		}

		return $booking_number;
	}

	/**
	 * Method get_activity_time
	 *
	 * @param $reservation_id
	 *
	 * @return string $time
	 */
	public function get_activity_time( $reservation_id = false ) {

		if ( false !== $reservation_id ) {
			$this->reservation_id = $reservation_id;
		}

		$time = get_post_meta( $this->reservation_id, 'staylodgic_activity_time', true );

		return $time;
	}

	/**
	 * Method get_number_of_adults_for_reservation
	 *
	 * @param $reservation_id
	 *
	 * @return $number_of_adults
	 */
	public function get_number_of_adults_for_reservation( $reservation_id = false ) {

		if ( false !== $reservation_id ) {
			$this->reservation_id = $reservation_id;
		}

		$number_of_adults = get_post_meta( $this->reservation_id, 'staylodgic_reservation_activity_adults', true );

		if ( isset( $number_of_adults ) && $number_of_adults ) {
			return $number_of_adults;
		}

		return false;
	}
	/**
	 * Method get_number_of_children_for_reservation
	 *
	 * @param $reservation_id
	 *
	 * @return array $number_of_children
	 */
	public function get_number_of_children_for_reservation( $reservation_id = false ) {

		if ( false !== $reservation_id ) {
			$this->reservation_id = $reservation_id;
		}

		$number_of_children = get_post_meta( $this->reservation_id, 'staylodgic_reservation_activity_children', true );
		if ( isset( $number_of_children['number'] ) && $number_of_children ) {
			return $number_of_children['number'];
		}

		return false;
	}

	/**
	 * Method get_total_occupants_for_reservation
	 *
	 * @param $reservation_id
	 *
	 * @return intval
	 */
	public function get_total_occupants_for_reservation( $reservation_id = false ) {

		if ( false !== $reservation_id ) {
			$this->reservation_id = $reservation_id;
		}

		$number_of_adults   = $this->get_number_of_adults_for_reservation();
		$number_of_children = $this->get_number_of_children_for_reservation();

		return intval( $number_of_adults ) + intval( $number_of_children );
	}

	/**
	 * Method has_activities
	 *
	 * @return boolean
	 */
	public static function has_activities() {
		$roomlist   = array();
		$activities = self::query_activities(); // Call query_rooms() method here
		if ( $activities ) {
			return true;
		}
		return false;
	}

	/**
	 * Method query_activities
	 *
	 * @return array $activities
	 */
	public static function query_activities() {
		$activities = get_posts(
			array(
				'post_type'   => 'slgc_activity',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
				'numberposts' => -1,
				'post_status' => 'publish',
			)
		);
		return $activities;
	}


	/**
	 * Method get_reservation_for_activity
	 *
	 * @param $booking_number
	 *
	 * @return array $args
	 */
	public static function get_reservation_for_activity( $booking_number ) {
		$args = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $booking_number,
				),
			),
		);
		return new \WP_Query( $args );
	}

	/**
	 * Method get_guest_id_for_reservation
	 *
	 * @param $booking_number
	 *
	 * @return string $customer_id
	 */
	public function get_guest_id_for_reservation( $booking_number ) {
		$args              = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $booking_number,
				),
			),
		);
		$reservation_query = new \WP_Query( $args );

		if ( $reservation_query->have_posts() ) {
			$reservation = $reservation_query->posts[0];
			$customer_id = get_post_meta( $reservation->ID, 'staylodgic_customer_id', true );
			return $customer_id;
		}

		return false; // Return an empty query if no guest found
	}

	/**
	 * Method get_activity_name_for_reservation
	 *
	 * @param $reservation_id
	 *
	 * @return void
	 */
	public function get_activity_name_for_reservation( $reservation_id = false ) {

		// Get room id from post meta
		$room_id = get_post_meta( $reservation_id, 'staylodgic_activity_id', true );

		// If room id exists, get the room's post title
		if ( $room_id ) {
			$room_post = get_post( $room_id );
			if ( $room_post ) {
				return $room_post->post_title;
			}
		}

		return null;
	}

	/**
	 * Method is_confirmed_reservation
	 *
	 * @param $reservation_id
	 *
	 * @return void
	 */
	public function is_confirmed_reservation( $reservation_id ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the reservation status for the reservation
		$reservation_status = get_post_meta( $reservation_id, 'staylodgic_reservation_status', true );

		if ( 'confirmed' === $reservation_status ) {
			return true;
		}

		return false;
	}

	/**
	 * Method get_activity_id_for_booking
	 *
	 * @param $booking_number
	 *
	 * @return void
	 */
	public static function get_activity_id_for_booking( $booking_number ) {
		$args              = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $booking_number,
				),
			),
		);
		$reservation_query = new \WP_Query( $args );

		if ( $reservation_query->have_posts() ) {
			$reservation = $reservation_query->posts[0];
			return $reservation->ID;
		}

		return false; // Return an false if no reservatuib found
	}

	/**
	 * Method get_activity_schedules_ajax_handler
	 *
	 * @return void
	 */
	public function get_activity_schedules_ajax_handler() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		$selected_date = isset( $_POST['selected_date'] ) ? sanitize_text_field( $_POST['selected_date'] ) : null;
		$total_people  = isset( $_POST['totalpeople'] ) ? sanitize_text_field( $_POST['totalpeople'] ) : null;
		$the_post_id   = isset( $_POST['the_post_id'] ) ? sanitize_text_field( $_POST['the_post_id'] ) : null;

		// Call the method and capture the output
		ob_start();
		$this->display_activity_schedules_with_availability( $selected_date, $the_post_id, $total_people );
		$output = ob_get_clean();

		// Return the output as a JSON response
		wp_send_json_success( $output );
	}

	/**
	 * Method get_activity_frontend_schedules_ajax_handler
	 *
	 * @return void
	 */
	public function get_activity_frontend_schedules_ajax_handler() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_searchbox_nonce'] ) || ! check_admin_referer( 'staylodgic-searchbox-nonce', 'staylodgic_searchbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$selected_date = isset( $_POST['selected_date'] ) ? sanitize_text_field( $_POST['selected_date'] ) : null;
		$total_people  = isset( $_POST['totalpeople'] ) ? sanitize_text_field( $_POST['totalpeople'] ) : null;
		$the_post_id   = isset( $_POST['the_post_id'] ) ? sanitize_text_field( $_POST['the_post_id'] ) : null;

		$number_of_children = 0;
		$number_of_adults   = 0;
		$number_of_guests   = 0;
		$children_age       = array();

		if ( isset( $_POST['number_of_adults'] ) ) {
			$number_of_adults = $_POST['number_of_adults'];
		}

		if ( isset( $_POST['number_of_children'] ) ) {
			$number_of_children = $_POST['number_of_children'];
		}

		if ( isset( $_POST['children_age'] ) ) {
			// Loop through all the select elements with the class 'children-age-selector'
			foreach ( $_POST['children_age'] as $selected_age ) {
				// Sanitize and store the selected values in an array
				$children_age[] = sanitize_text_field( $selected_age );
			}
		}

		// Call the method and capture the output
		ob_start();
		$this->display_activity_frontend_schedules_with_availability(
			$selected_date,
			$the_post_id,
			$total_people,
			$children_age,
			$number_of_children,
			$number_of_adults
		);
		$output = ob_get_clean();

		// Return the output as a JSON response
		wp_send_json_success( $output );
	}

	/**
	 * Method display_activity_frontend_schedules_with_availability
	 *
	 * @return void
	 */
	public function display_activity_frontend_schedules_with_availability(
		$selected_date = null,
		$the_post_id = false,
		$total_people = false,
		$children_age = null,
		$number_of_children = null,
		$number_of_adults = null
	) {

		$this->children_age         = $children_age;
		$this->stay_children_guests = $number_of_children;
		$this->stay_adult_guests    = $number_of_adults;
		$this->stay_checkin_date    = $selected_date;

		$this->activities_array = array();

		$number_of_guests = intval( $number_of_adults ) + intval( $number_of_children );

		$this->stay_total_guests = $number_of_guests;

		// Use today's date if $selected_date is not provided
		if ( is_null( $selected_date ) ) {
			$selected_date = gmdate( 'Y-m-d' );
		}

		if ( null !== get_post_meta( $the_post_id, 'staylodgic_activity_time', true ) ) {
			$existing_activity_time = get_post_meta( $the_post_id, 'staylodgic_activity_time', true );
		}
		if ( null !== get_post_meta( $the_post_id, 'staylodgic_activity_id', true ) ) {
			$existing_activity_id = get_post_meta( $the_post_id, 'staylodgic_activity_id', true );
		}

		$this->activities_array['date']         = $selected_date;
		$this->activities_array['adults']       = $this->stay_adult_guests;
		$this->activities_array['children']     = $this->stay_children_guests;
		$this->activities_array['children_age'] = $this->children_age;
		$this->activities_array['person_total'] = $this->stay_total_guests;

		// Get the day of the week for the selected date
		$day_of_week = strtolower( gmdate( 'l', strtotime( $selected_date ) ) );

		// Query all activity posts
		$args       = array(
			'post_type'      => 'slgc_activity',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		);
		$activities = new \WP_Query( $args );

		echo '<form action="" method="post" id="hotel-acitivity-listing" class="needs-validation" novalidate>';
		$roomlistingbox = wp_create_nonce( 'staylodgic-roomlistingbox-nonce' );
		echo '<input type="hidden" name="staylodgic_roomlistingbox_nonce" value="' . esc_attr( $roomlistingbox ) . '" />';

		echo '<div id="activity-data" data-bookingnumber="' . esc_attr( $this->stay_booking_number ) . '" data-children="' . esc_attr( $this->stay_children_guests ) . '" data-adults="' . esc_attr( $this->stay_adult_guests ) . '" data-guests="' . esc_attr( $this->stay_total_guests ) . '" data-checkin="' . esc_attr( $this->stay_checkin_date ) . '">';
		// Start the container div
		echo '<div class="activity-schedules-container">';

		// Loop through each activity post
		if ( $activities->have_posts() ) {
			while ( $activities->have_posts() ) {
				$activities->the_post();
				$post_id = get_the_ID();

				$activity_schedule = get_post_meta( $post_id, 'staylodgic_activity_schedule', true );
				$max_guests        = get_post_meta( $post_id, 'staylodgic_max_guests', true );

				if ( null !== get_post_meta( $post_id, 'staylodgic_activity_rate', true ) ) {
					$activity_rate = get_post_meta( $post_id, 'staylodgic_activity_rate', true );
				}

				// Display the activity identifier (e.g., post title)
				echo '<div class="activity-schedule room-occupied-group" id="activity-schedule-' . esc_attr( $post_id ) . '">';

				if ( null !== get_post_meta( $post_id, 'staylodgic_activity_desc', true ) ) {
					$activity_desc = get_post_meta( $post_id, 'staylodgic_activity_desc', true );
				}

				if ( null !== $post_id ) {
					$activity_image = staylodgic_featured_image_link( $post_id );
				}

				echo '<div class="activity-column-one">';
				$image_id      = get_post_thumbnail_id( $post_id );
				$fullimage_url = wp_get_attachment_image_url( $image_id, 'staylodgic-full' );
				$image_url     = wp_get_attachment_image_url( $image_id, 'staylodgic-large-square' );
				echo '<a href="' . esc_url( $fullimage_url ) . '" data-toggle="lightbox" data-gallery="lightbox-gallery-' . esc_attr( $post_id ) . '">';
				echo '<img class="lightbox-trigger activity-summary-image" data-image="' . esc_url( $fullimage_url ) . '" src="' . esc_url( $fullimage_url ) . '" alt="Activity">';
				echo '</a>';
				$supported_gallery = staylodgic_output_custom_image_links( $post_id );
				if ( $supported_gallery ) {
					echo wp_kses( $supported_gallery, staylodgic_get_allowed_tags() );
				}
				echo '</div>';
				echo '<div class="activity-column-two">';
				echo '<h4 class="activity-title">' . esc_html( get_the_title() ) . '</h4>';
				echo '<div class="activity-desc entry-content">' . wp_kses_post( $activity_desc ) . '</div>';

				// Display the time slots for the day of the week that matches the selected date
				if ( ! empty( $activity_schedule ) && isset( $activity_schedule[ $day_of_week ] ) ) {
					echo '<div class="day-schedule">';
					foreach ( $activity_schedule[ $day_of_week ] as $index => $time ) {
						// Calculate remaining spots for this time slot

						$remaining_spots = $this->calculate_remaining_spots( $post_id, $selected_date, $time, $max_guests );

						$remaining_spots_compare = $remaining_spots;
						$existing_found          = false;

						if ( (int) $existing_activity_id === (int) $post_id && (int) $time === (int) $existing_activity_time ) {

							$reserved_for_guests    = $this->get_activity_reservation_numbers( $the_post_id );
							$existing_spots_for_day = $reserved_for_guests['total'];

							$remaining_spots_compare = $remaining_spots + $existing_spots_for_day;
							$existing_found          = true;
						}

						$active_class = 'time-disabled';

						if ( (int) $this->stay_total_guests <= (int) $remaining_spots_compare && 0 !== (int) $remaining_spots ) {
							$active_class = 'time-active';
							if ( $existing_found ) {
								$active_class .= ' time-choice';
							}
						}

						$time_index = $day_of_week . '-' . $index;

						if ( '' !== $time ) {
							$total_rate                                  = intval( $activity_rate * $this->stay_total_guests );
							$this->activities_array[ $post_id ][ $time ] = $total_rate;
							echo '<span class="time-slot ' . esc_attr( $active_class ) . '" id="time-slot-' . esc_attr( $time_index ) . '" data-activity="' . esc_attr( $post_id ) . '" data-time="' . esc_attr( $time ) . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> ' . esc_attr( $time ) . '</span><span class="time-slots-remaining">( ' . esc_attr( $remaining_spots ) . ' of ' . esc_attr( $max_guests ) . ' remaining )</span><div class="activity-rate" data-activityprice="' . esc_attr( $total_rate ) . '">' . wp_kses_post( staylodgic_price( $total_rate ) ) . '</div></span> ';
						} else {
							echo '<span class="time-slot-unavailable time-slot ' . esc_attr( $active_class ) . '" id="time-slot-' . esc_attr( $time_index ) . '" data-activity="' . esc_attr( $post_id ) . '" data-time="' . esc_attr( $time ) . '"><span class="activity-time-slot">Unavailable</span></span> ';
						}
					}
					echo '</div>';
				}
				echo '</div>';

				echo '</div>'; // Close the activity-schedule div
			}
		}

		// Close the container div
		echo '</div>';
		echo '</div>';
		$this->register_guest_form();
		echo '</form>';

		staylodgic_set_booking_transient( $this->activities_array, $this->stay_booking_number );
		$activities_data = staylodgic_get_booking_transient( $this->stay_booking_number );
		// Reset post data
		wp_reset_postdata();
	}

	/**
	 * Method process_selected_activity
	 *
	 * @return void
	 */
	public function process_selected_activity() {

		$bookingnumber  = sanitize_text_field( $_POST['bookingnumber'] );
		$activity_id    = sanitize_text_field( $_POST['activity_id'] );
		$activity_date  = sanitize_text_field( $_POST['activity_date'] );
		$activity_time  = sanitize_text_field( $_POST['activity_time'] );
		$activity_price = sanitize_text_field( $_POST['activity_price'] );

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_roomlistingbox_nonce'] ) || ! check_admin_referer( 'staylodgic-roomlistingbox-nonce', 'staylodgic_roomlistingbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$booking_results = $this->process_activity_data(
			$bookingnumber,
			$activity_id,
			$activity_date,
			$activity_time,
			$activity_price
		);

		if ( is_array( $booking_results ) ) {

			$html = $this->booking_summary(
				$bookingnumber,
				$booking_results['choice']['activity_id'],
				$booking_results['choice']['activity_name'],
				$booking_results['date'],
				$booking_results['choice']['time'],
				$booking_results['adults'],
				$booking_results['children'],
				$booking_results['choice']['price'],
			);
		} else {
			$html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>' . __( 'Session timed out. Please reload the page', 'staylodgic' ) . '</div>';
		}

		// Send the JSON response
		wp_send_json( $html );
	}

	/**
	 * Method process_activity_data
	 *
	 * @return void
	 */
	public function process_activity_data(
		$bookingnumber = null,
		$activity_id = null,
		$activity_date = null,
		$activity_time = null,
		$activity_price = null
	) {
		// Get the data sent via AJAX

		$stay_activity_name = $this->get_activity_name_from_id( $activity_id );

		$booking_results = staylodgic_get_booking_transient( $bookingnumber );

		// Return a response (you can modify this as needed)
		$response = array(
			'success' => true,
			'message' => 'Data: ' . $stay_activity_name . ',received successfully.',
		);

		if ( is_array( $booking_results ) ) {

			$booking_results['bookingnumber']           = $bookingnumber;
			$booking_results['choice']['activity_id']   = $activity_id;
			$booking_results['choice']['activity_name'] = $stay_activity_name;
			$booking_results['choice']['date']          = $activity_date;
			$booking_results['choice']['time']          = $activity_time;
			$booking_results['choice']['price']         = $booking_results[ $activity_id ][ $activity_time ];

			staylodgic_set_booking_transient( $booking_results, $bookingnumber );
		} else {
			$booking_results = false;
		}

		// Send the JSON response
		return $booking_results;
	}

	/**
	 * Method get_activity_name_from_id
	 *
	 * @param $activity_id
	 *
	 * @return void
	 */
	public function get_activity_name_from_id( $activity_id ) {
		$activity_post = get_post( $activity_id );
		if ( $activity_post ) {
			$activity_name = $activity_post->post_title;
		}

		return $activity_name;
	}

	/**
	 * Method booking_data_fields
	 *
	 * @return void
	 */
	public function booking_data_fields() {
		$data_fields = array(
			'full_name'      => __( 'Full Name', 'staylodgic' ),
			'passport'       => __( 'Passport No', 'staylodgic' ),
			'email_address'  => __( 'Email Address', 'staylodgic' ),
			'phone_number'   => __( 'Phone Number', 'staylodgic' ),
			'street_address' => __( 'Street Address', 'staylodgic' ),
			'city'           => __( 'City', 'staylodgic' ),
			'state'          => __( 'State/Province', 'staylodgic' ),
			'zip_code'       => __( 'Zip Code', 'staylodgic' ),
			'country'        => __( 'Country', 'staylodgic' ),
			'guest_comment'  => __( 'Notes', 'staylodgic' ),
			'guest_consent'  => __( 'By clicking "Book this Activity" you agree to our terms and conditions and privacy policy.', 'staylodgic' ),
		);

		return $data_fields;
	}

	public function register_guest_form() {
		$country_options = staylodgic_country_list( 'select', '' );

		$html          = '<div class="registration-column registration-column-two" id="booking-summary">';
		$bookingnumber = '';
		$activity_id   = '';
		$perdayprice   = '';
		$total         = '';
		$booking_results[ $activity_id ]['roomtitle'] = '';

		$roomtitle = $booking_results[ $activity_id ]['roomtitle'];
		$html     .= self::booking_summary(
			$bookingnumber,
			$activity_id,
			$roomtitle,
			$this->stay_checkin_date,
			$this->staynights,
			$this->stay_adult_guests,
			$this->stay_children_guests,
			$perdayprice,
			$total
		);
		$html     .= '</div>';

		$bookingsuccess = self::booking_successful();

		$form_inputs = self::booking_data_fields();

		echo '
		<div class="registration_form_outer registration_request">
			<div class="registration_form_wrap">
				<div class="registration_form">
					<div class="registration-column registration-column-one registration_form_inputs">
						<div class="booking-backto-activitychoice"><div class="booking-backto-roomchoice-inner"><i class="fa-solid fa-arrow-left"></i> ' . esc_html__( 'Back', 'staylodgic' ) . '</div></div>
						<h3>' . esc_html__( 'Registration', 'staylodgic' ) . '</h3>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'Full Name', 'staylodgic' ) . '" type="text" class="form-control" id="full_name" name="full_name" required>
							<label for="full_name" class="control-label">' . esc_html( $form_inputs['full_name'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'Passport No.', 'staylodgic' ) . '" type="text" class="form-control" id="passport" name="passport" required>
							<label for="passport" class="control-label">' . esc_html( $form_inputs['passport'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'Email Address', 'staylodgic' ) . '" type="email" class="form-control" id="email_address" name="email_address" required>
							<label for="email_address" class="control-label">' . esc_html( $form_inputs['email_address'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'Phone Number', 'staylodgic' ) . '" type="tel" class="form-control" id="phone_number" name="phone_number" required>
							<label for="phone_number" class="control-label">' . esc_html( $form_inputs['phone_number'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'Street Address', 'staylodgic' ) . '" type="text" class="form-control" id="street_address" name="street_address">
							<label for="street_address" class="control-label">' . esc_html( $form_inputs['street_address'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<input placeholder="' . esc_attr__( 'City', 'staylodgic' ) . '" type="text" class="form-control" id="city" name="city">
							<label for="city" class="control-label">' . esc_html( $form_inputs['city'] ) . '</label>
						</div>
						<div class="row">
							<div class="col">
								<div class="form-group form-floating">
									<input placeholder="' . esc_attr__( 'State', 'staylodgic' ) . '" type="text" class="form-control" id="state" name="state">
									<label for="state" class="control-label">' . esc_html( $form_inputs['state'] ) . '</label>
								</div>
							</div>
							<div class="col">
								<div class="form-group form-floating">
									<input placeholder="' . esc_attr__( 'Zip Code', 'staylodgic' ) . '" type="text" class="form-control" id="zip_code" name="zip_code">
									<label for="zip_code" class="control-label">' . esc_html( $form_inputs['zip_code'] ) . '</label>
								</div>
							</div>
						</div>
						<div class="form-group form-floating">
							<select required class="form-control" id="country" name="country" >
							' . wp_kses( $country_options, staylodgic_get_allowed_tags() ) . '
							</select>
							<label for="country" class="control-label">' . esc_html( $form_inputs['country'] ) . '</label>
						</div>
						<div class="form-group form-floating">
							<textarea class="form-control" id="guest_comment" name="guest_comment"></textarea>
							<label for="guest_comment" class="control-label">' . esc_html( $form_inputs['guest_comment'] ) . '</label>
						</div>
						<div class="checkbox guest-consent-checkbox">
							<label for="guest_consent">
								<input type="checkbox" class="form-check-input" id="guest_consent" name="guest_consent" required /><span class="consent-notice">' . esc_html( $form_inputs['guest_consent'] ) . '</span>
								<div class="invalid-feedback">
									' . esc_html__( 'Consent is required for booking.', 'staylodgic' ) . '
								</div>
							</label>
						</div>
					</div>
					' . wp_kses( $html, staylodgic_get_allowed_tags() ) . '
				</div>
			</div>
		</div>';
	}

	/**
	 * Method booking_successful
	 *
	 * @return void
	 */
	public function booking_successful() {

		$reservation_instance = new \Staylodgic\Reservations();
		$booking_page_link    = $reservation_instance->get_booking_details_page_link_for_guest();

		echo '
		<div class="registration_form_outer registration_successful">
			<div class="registration_form_wrap">
				<div class="registration_form">
					<div class="registration-successful-inner">
						<h3>' . esc_html__( 'Booking Successful', 'staylodgic' ) . '</h3>
						<p>
							' . esc_html__( 'Hi,', 'staylodgic' ) . '
						</p>
						<p>
							' . esc_html__( 'Your booking number is:', 'staylodgic' ) . ' <span class="booking-number">' . esc_html( $this->stay_booking_number ) . '</span>
						</p>
						<p>
							' . esc_html__( 'Please contact us to cancel, modify or if there\'s any questions regarding the booking.', 'staylodgic' ) . '
						</p>
						<p class="booking-successful-button">
							<div id="booking-details" class="book-button not-fullwidth booking-successful-button">
								<a href="' . esc_url( $booking_page_link ) . '">' . esc_html__( 'Booking Details', 'staylodgic' ) . '</a>
							</div>
						</p>
					</div>
				</div>
			</div>
		</div>';
	}

	/**
	 * Method booking_summary
	 *
	 * @return void
	 */
	public function booking_summary(
		$bookingnumber = null,
		$activity_id = null,
		$activity_name = null,
		$checkin = null,
		$time = null,
		$adults = null,
		$children = null,
		$totalrate = null
	) {

		$totalguests = intval( $adults ) + intval( $children );
		$totalprice  = array();

		$html = '<div id="booking-summary-wrap">';
		if ( '' !== $activity_name ) {
			$html .= '<div class="room-summary"><span class="summary-room-name">' . esc_html( $activity_name ) . '</span></div>';
		}

		$html .= '<div class="main-summary-wrap">';

		$html .= \Staylodgic\Common::generate_person_icons( $adults, $children );

		$html .= '</div>';

		$html .= '<div class="stay-summary-wrap">';

		$html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
		$html .= '<div class="summary-heading checkin-summary-heading">' . esc_html__( 'Activity Time:', 'staylodgic' ) . '</div>';
		$html .= '<div class="checkin-summary">' . esc_html( $checkin ) . '</div>';
		$html .= '<div class="checkin-summary">' . esc_html( $time ) . '</div>';

		$html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
		$html .= '</div>';

		if ( '' !== $totalrate ) {
			$subtotalprice = intval( $totalrate );
			$html         .= '<div class="price-summary-wrap">';

			if ( staylodgic_has_tax() ) {
				$html .= '<div class="summary-heading total-summary-heading">' . esc_html__( 'Subtotal:', 'staylodgic' ) . '</div>';
				$html .= '<div class="price-summary">' . staylodgic_price( $subtotalprice ) . '</div>';
			}

			$html .= '<div class="summary-heading total-summary-heading">' . esc_html__( 'Total:', 'staylodgic' ) . '</div>';

			$staynights   = 1;
			$tax_instance = new \Staylodgic\Tax( 'activities' );
			$totalprice   = $tax_instance->apply_tax( $subtotalprice, $staynights, $totalguests, $output = 'html' );
			foreach ( $totalprice['details'] as $total_id => $totalvalue ) {
				$html .= '<div class="tax-summary tax-summary-details">' . $totalvalue . '</div>';
			}

			$html .= '<div class="tax-summary tax-summary-total">' . staylodgic_price( $totalprice['total'] ) . '</div>';
			$html .= '</div>';
		}

		if ( '' !== $activity_id ) {
			$html .= '<div class="form-group">';
			$html .= '<div id="bookingResponse" class="booking-response"></div>';
			$html .= '<div id="activity-register" class="book-button">' . esc_html__( 'Book this activity', 'staylodgic' ) . '</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}


	/**
	 * Method activity_content
	 *
	 * @param $content $content
	 *
	 * @return void
	 */
	public function activity_content( $content ) {
		if ( is_singular( 'slgc_activity' ) ) {
			$custom_content = $this->activity_booking_searchform();
			$content        = $custom_content . $content; // Prepend custom content
		}
		return $content;
	}

	/**
	 * Method activity_search_shortcode
	 *
	 * @return void
	 */
	public function activity_search_shortcode() {
		$search_form = $this->activity_booking_searchform();
		return $search_form;
	}

	/**
	 * Method get_name_for_activity
	 *
	 * @param $reservation_id
	 *
	 * @return void
	 */
	public function get_name_for_activity( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}

		// Get room id from post meta
		$activity_id = get_post_meta( $reservation_id, 'staylodgic_activity_id', true );

		// If room id exists, get the room's post title
		if ( $activity_id ) {
			$acitivity_post = get_post( $activity_id );
			if ( $acitivity_post ) {
				return $acitivity_post->post_title;
			}
		}

		return null;
	}

	/**
	 * Method get_edit_links_for_activity
	 *
	 * @param $reservation_array $reservation_array
	 *
	 * @return void
	 */
	public function get_edit_links_for_activity( $reservation_array ) {
		$links = '<ul>';
		foreach ( $reservation_array as $post_id ) {
			$room_name = self::get_name_for_activity( $post_id );
			$edit_link = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			$links    .= '<li><p><a href="' . esc_url( $edit_link ) . '" title="' . esc_attr( $room_name ) . '">Edit Reservation ' . esc_attr( $post_id ) . '<br/><small>' . esc_html( $room_name ) . '</small></a></p></li>';
		}
		$links .= '</ul>';
		return $links;
	}

	/**
	 * Method get_activity_ids_for_customer
	 *
	 * @param $customer_id $customer_id
	 *
	 * @return void
	 */
	public static function get_activity_ids_for_customer( $customer_id ) {
		$args            = array(
			'post_type'  => 'slgc_activityres',
			'meta_query' => array(
				array(
					'key'     => 'staylodgic_customer_id',
					'value'   => $customer_id,
					'compare' => '=',
				),
			),
		);
		$posts           = get_posts( $args );
		$reservation_ids = array();
		foreach ( $posts as $post ) {
			$reservation_ids[] = $post->ID;
		}
		return $reservation_ids;
	}

	/**
	 * Method get_guest_id_for_activity
	 *
	 * @param $booking_number $booking_number
	 *
	 * @return void
	 */
	public function get_guest_id_for_activity( $booking_number ) {
		$args              = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $booking_number,
				),
			),
		);
		$reservation_query = new \WP_Query( $args );

		if ( $reservation_query->have_posts() ) {
			$reservation = $reservation_query->posts[0];
			$customer_id = get_post_meta( $reservation->ID, 'staylodgic_customer_id', true );
			return $customer_id;
		}

		return false; // Return an empty query if no guest found
	}

	/**
	 * Method get_guest_for_activity
	 *
	 * @param $booking_number $booking_number
	 *
	 * @return void
	 */
	public function get_guest_for_activity( $booking_number ) {
		$args              = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $booking_number,
				),
			),
		);
		$reservation_query = new \WP_Query( $args );

		if ( $reservation_query->have_posts() ) {
			$reservation = $reservation_query->posts[0];
			$customer_id = get_post_meta( $reservation->ID, 'staylodgic_customer_id', true );

			if ( ! empty( $customer_id ) ) {
				$customer_args = array(
					'post_type'   => 'slgc_customers',
					'p'           => $customer_id,
					'post_status' => 'publish',
				);
				return new \WP_Query( $customer_args );
			}
		}

		return new \WP_Query(); // Return an empty query if no guest found
	}

	/**
	 * Method have_customer
	 *
	 * @param $reservation_id $reservation_id
	 *
	 * @return void
	 */
	public function have_customer( $reservation_id ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta( $reservation_id, 'staylodgic_booking_number', true );

		if ( ! $booking_number ) {
			// Handle error if booking number not found
			return false;
		}

		// Query the customer post with the matching booking number
		$customer_query = $this->get_guest_for_activity( $booking_number );
		// Check if a customer post exists
		if ( $customer_query->have_posts() ) {
			// Restore the original post data
			wp_reset_postdata();

			// Return true if a matching customer post is found
			return true;
		}

		// No matching customer found, return false
		return false;
	}

	/**
	 * Method get_reservation_customer_id
	 *
	 * @param $reservation_id $reservation_id
	 *
	 * @return void
	 */
	public function get_reservation_customer_id( $reservation_id = false ) {

		if ( ! $reservation_id ) {
			$reservation_id = $this->reservation_id;
		}
		// Get the booking number from the reservation post meta
		$booking_number = get_post_meta( $reservation_id, 'staylodgic_booking_number', true );

		if ( ! $booking_number ) {
			// Handle error if booking number not found
			return '';
		}

		// Query the customer post with the matching booking number
		$customer_id = $this->get_guest_id_for_activity( $booking_number );
		// No matching customer found
		return $customer_id;
	}

	/**
	 * Method activity_booking_searchform
	 *
	 * @return void
	 */
	public function activity_booking_searchform() {

		// Generate unique booking number
		staylodgic_set_booking_transient( '1', $this->stay_booking_number );
		ob_start();
		$searchbox_nonce         = wp_create_nonce( 'staylodgic-searchbox-nonce' );
		$availability_date_array = array();

		// Calculate current date
		$stay_current_date = current_time( 'Y-m-d' );
		// Calculate end date as 3 months from the current date
		$stay_end_date = gmdate( 'Y-m-d', strtotime( $stay_current_date . ' +1 month' ) );

		$fullybooked_dates         = array();
		$display_fullbooked_status = false; // Disabled

		if ( true === $display_fullbooked_status ) {
			$reservations_instance = new \Staylodgic\Reservations();
			$fullybooked_dates     = $reservations_instance->days_fully_booked_for_date_range( $stay_current_date, $stay_end_date );
		}
		?>
		<div class="staylodgic-content staylodgic-activity-booking">
			<div id="hotel-booking-form">

				<div class="front-booking-search">
					<div class="front-booking-calendar-wrap">
						<div class="front-booking-calendar-icon"><i class="fa-solid fa-calendar-days"></i></div>
						<div class="front-booking-calendar-date"><?php esc_html_e( 'Activity date', 'staylodgic' ); ?></div>
					</div>
					<div class="front-booking-guests-wrap">
						<div class="front-booking-guests-container"> <!-- New container -->
							<div class="front-booking-guest-adult-wrap">
								<div class="front-booking-guest-adult-icon"><span class="guest-adult-svg"></span><span class="front-booking-adult-adult-value">2</span></div>
							</div>
							<div class="front-booking-guest-child-wrap">
								<div class="front-booking-guest-child-icon"><span class="guest-child-svg"></span><span class="front-booking-adult-child-value">0</span></div>
							</div>
						</div>
						<div id="activitySearch" class="form-search-button"><?php esc_html_e( 'Search', 'staylodgic' ); ?></div>
					</div>
				</div>


				<div class="staylodgic_reservation_datepicker">
					<input type="hidden" name="staylodgic_searchbox_nonce" value="<?php echo esc_attr( $searchbox_nonce ); ?>" />
					<?php
					$activity_fully_booked_days = htmlspecialchars( wp_json_encode( $fullybooked_dates ), ENT_QUOTES, 'UTF-8' );
					?>
					<input data-booked="<?php echo esc_html( $activity_fully_booked_days ); ?>" type="date" id="activity-reservation-date" name="reservation_date">
				</div>
				<div class="staylodgic_reservation_room_guests_wrap">
					<div id="staylodgic_reservation_room_adults_wrap" class="number-input occupant-adult occupants-range">
						<div class="column-one">
							<label for="number-of-adults"><?php esc_html_e( 'Adults', 'staylodgic' ); ?></label>
						</div>
						<div class="column-two">
							<span class="minus-btn">-</span>
							<input data-guest="adult" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-adults" value="2" name="number_of_adults" type="text" class="number-value" readonly="">
							<span class="plus-btn">+</span>
						</div>
					</div>
					<div id="staylodgic_reservation_room_children_wrap" class="number-input occupant-child occupants-range">
						<div class="column-one">
							<label for="number-of-adults"><?php esc_html_e( 'Children', 'staylodgic' ); ?></label>
						</div>
						<div class="column-two">
							<span class="minus-btn">-</span>
							<input data-childageinput="children_age[]" data-guest="child" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-children" value="0" name="number_of_children" type="text" class="number-value" readonly="">
							<span class="plus-btn">+</span>
						</div>

					</div>
					<div id="guest-age"></div>
				</div>
				<div class="recommended-alt-wrap">
					<div class="recommended-alt-title"><?php esc_html_e( 'Rooms unavailable', 'staylodgic' ); ?></div>
					<div class="recommended-alt-description"><?php esc_html_e( 'Following range from your selection is avaiable.', 'staylodgic' ); ?></div>
					<div id="recommended-alt-dates"></div>
				</div>
				<div class="available-list">
					<div id="available-list-ajax"></div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method get_activities
	 *
	 * @param $the_post_id
	 *
	 * @return void
	 */
	public function get_activities( $the_post_id ) {

		$activities = '';

		if ( null !== get_post_meta( $the_post_id, 'staylodgic_reservation_checkin', true ) ) {
			$activity_date = get_post_meta( $the_post_id, 'staylodgic_reservation_checkin', true );

			// Check if $activity_date is a valid date
			if ( strtotime( $activity_date ) !== false ) {
				// Create an instance of the Activity class
				$reserved_for_guests    = $this->get_activity_reservation_numbers( $the_post_id );
				$existing_spots_for_day = $reserved_for_guests['total'];
				$activities            .= '<div class="activity-schedules-container-wrap">';
				$this->display_activity_schedules_with_availability( $activity_date, $the_post_id, $existing_spots_for_day ); // Today's date
				$activities .= '</div>';
			} else {
				// Handle the case where $activity_date is not a valid date
				$activities .= '<div class="activity-schedules-container-wrap"></div>';
			}
		}

		return $activities;
	}

	/**
	 * Method display_ticket
	 *
	 * @param $the_post_id
	 * @param $activity_id
	 *
	 * @return void
	 */
	public function display_ticket( $the_post_id, $activity_id ) {

		$ticket = '';

		if ( null !== get_post_meta( $the_post_id, 'staylodgic_activity_time', true ) ) {

			$property_logo_id = staylodgic_get_option( 'property_logo' );
			$property_name    = staylodgic_get_option( 'property_name' );
			$property_phone   = staylodgic_get_option( 'property_phone' );
			$property_address = staylodgic_get_option( 'property_address' );
			$property_header  = staylodgic_get_option( 'property_header' );
			$property_footer  = staylodgic_get_option( 'property_footer' );

			$hotel_logo = $property_logo_id ? wp_get_attachment_image_url( $property_logo_id, 'full' ) : '';

			$activity_id = get_post_meta( $the_post_id, 'staylodgic_activity_id', true );

			if ( null !== $activity_id ) {
				$activity_image = staylodgic_featured_image_link( $activity_id );
			}

			$booking_number = get_post_meta( $the_post_id, 'staylodgic_booking_number', true );
			$activity_date  = get_post_meta( $the_post_id, 'staylodgic_reservation_checkin', true );

			$staylodgic_customer_id = get_post_meta( $the_post_id, 'staylodgic_customer_id', true );
			$full_name              = get_post_meta( $staylodgic_customer_id, 'staylodgic_full_name', true );

			$ticket_price       = get_post_meta( $the_post_id, 'staylodgic_reservation_total_room_cost', true );
			$booking_number     = get_post_meta( $the_post_id, 'staylodgic_booking_number', true );
			$reservation_status = get_post_meta( $the_post_id, 'staylodgic_reservation_status', true );

			$data_array = staylodgic_get_select_target_options( 'activity_names' );
			$time       = $this->get_activity_time( $the_post_id );

			$reserved_for_guests = $this->get_activity_reservation_numbers( $the_post_id );
			$reserved_total      = $reserved_for_guests['total'];

			if ( isset( $data_array[ $activity_id ] ) && isset( $ticket_price ) && 0 < $ticket_price ) {

				$ticket  = '<div class="ticket-container-outer">';
				$ticket .= '<div data-file="' . esc_attr( $booking_number ) . '-' . esc_attr( $the_post_id ) . '" data-postid="' . esc_attr( $the_post_id ) . '" id="ticket-' . esc_attr( $booking_number ) . '" data-bookingnumber="' . esc_attr( $booking_number ) . '" class="ticket ticket-container">';
				$ticket .= '<div class="ticket-header">';
				$ticket .= '<p class="ticket-company">' . esc_html( $property_name ) . '</p>';
				$ticket .= '<p class="ticket-phone">' . esc_html( $property_phone ) . '</p>';
				$ticket .= '<p class="ticket-address">' . esc_html( $property_address ) . '</p>';
				$ticket .= '<p class="ticket-break"></p>';
				$ticket .= '<h1>' . esc_html( $data_array[ $activity_id ] ) . '</h1>';
				$ticket .= '<p class="ticket-date">' . gmdate( 'F jS Y', strtotime( $activity_date ) ) . '</p>';
				$ticket .= '</div>';
				$ticket .= '<div style="background: url(' . esc_url( $activity_image ) . '); background-size:cover" class="ticket-image">';
				$ticket .= '</div>';
				$ticket .= '<div class="ticket-info">';
				$ticket .= '<p>' . esc_html( $reserved_total ) . ' x <i class="fa-solid fa-user"></i></p>';
				$ticket .= '<p class="ticket-name">' . esc_html( $full_name ) . '</p>';
				$ticket .= '<p class="ticket-time"><i class="fa-regular fa-clock"></i> ' . esc_html( $time ) . '</p>';
				$ticket .= '<p class="ticket-price">' . staylodgic_price( $ticket_price ) . '</p>';
				$ticket .= '<div id="ticketqrcode" data-qrcode="' . esc_html( $booking_number ) . '" class="qrcode"></div>';
				$ticket .= '</div>';
				$ticket .= '<div class="ticket-button">' . esc_html( $reservation_status ) . '</div>';
				$ticket .= '</div>';
				$ticket .= '</div>';
			} elseif ( ! isset( $ticket_price ) || 0 >= $ticket_price ) {
					$ticket .= '<div class="ticket-price-not-found">' . __( 'Ticket price not found', 'staylodgic' ) . '</div>';
			}
		}

		return $ticket;
	}

	/**
	 * Method get_activity_reservation_numbers
	 *
	 * @param $the_post_id $the_post_id
	 *
	 * @return void
	 */
	public function get_activity_reservation_numbers( $the_post_id ) {
		$existing_adults         = get_post_meta( $the_post_id, 'staylodgic_reservation_activity_adults', true );
		$existing_children_array = get_post_meta( $the_post_id, 'staylodgic_reservation_activity_children', true );
		$existing_children       = is_array( $existing_children_array ) ? $existing_children_array['number'] : 0;

		// Set values to zero if they are empty
		$existing_adults   = ! empty( $existing_adults ) ? intval( $existing_adults ) : 0;
		$existing_children = ! empty( $existing_children ) ? intval( $existing_children ) : 0;

		$existing_spots_for_day = $existing_adults + $existing_children;

		return array(
			'adults'   => $existing_adults,
			'children' => $existing_children,
			'total'    => $existing_spots_for_day,
		);
	}

	/**
	 * Method display_activity_schedules_with_availability
	 *
	 * @param $selected_date
	 * @param $the_post_id
	 * @param $total_people
	 *
	 * @return void
	 */
	public function display_activity_schedules_with_availability( $selected_date = null, $the_post_id = false, $total_people = false ) {
		// Use today's date if $selected_date is not provided
		if ( is_null( $selected_date ) ) {
			$selected_date = gmdate( 'Y-m-d' );
		}

		if ( null !== get_post_meta( $the_post_id, 'staylodgic_activity_time', true ) ) {
			$existing_activity_time = get_post_meta( $the_post_id, 'staylodgic_activity_time', true );
		}
		if ( null !== get_post_meta( $the_post_id, 'staylodgic_activity_id', true ) ) {
			$existing_activity_id = get_post_meta( $the_post_id, 'staylodgic_activity_id', true );
		}

		// Get the day of the week for the selected date
		$day_of_week = strtolower( gmdate( 'l', strtotime( $selected_date ) ) );

		// Query all activity posts
		$args       = array(
			'post_type'      => 'slgc_activity',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		);
		$activities = new \WP_Query( $args );

		// Start the container div
		echo '<div class="spinner"></div><div class="activity-schedules-container">';

		echo '<h3>' . esc_html( ucfirst( $day_of_week ) ) . '</h3>';
		// Loop through each activity post
		if ( $activities->have_posts() ) {
			while ( $activities->have_posts() ) {
				$activities->the_post();
				$post_id = get_the_ID();

				$activity_schedule = get_post_meta( $post_id, 'staylodgic_activity_schedule', true );
				$max_guests        = get_post_meta( $post_id, 'staylodgic_max_guests', true );

				// Display the activity identifier (e.g., post title)
				echo '<div class="activity-schedule" id="activity-schedule-' . esc_attr( $post_id ) . '">';

				echo '<h4>' . esc_html( get_the_title() ) . '</h4>';

				// Display the time slots for the day of the week that matches the selected date
				if ( ! empty( $activity_schedule ) && isset( $activity_schedule[ $day_of_week ] ) ) {
					echo '<div class="day-schedule">';
					foreach ( $activity_schedule[ $day_of_week ] as $index => $time ) {
						// Calculate remaining spots for this time slot

						$remaining_spots = $this->calculate_remaining_spots( $post_id, $selected_date, $time, $max_guests );

						$remaining_spots_compare = $remaining_spots;
						$existing_found          = false;

						if ( (int) $existing_activity_id === (int) $post_id && (int) $time === (int) $existing_activity_time ) {

							$reserved_for_guests    = $this->get_activity_reservation_numbers( $the_post_id );
							$existing_spots_for_day = $reserved_for_guests['total'];

							$remaining_spots_compare = $remaining_spots + $existing_spots_for_day;
							$existing_found          = true;
						}

						$active_class = 'time-disabled';

						if ( (int) $total_people <= (int) $remaining_spots_compare && 0 !== (int) $remaining_spots && '' !== (int) $time ) {
							$active_class = 'time-active';
							if ( $existing_found ) {
								$active_class .= ' time-choice';
							}

							echo '<span class="time-slot ' . esc_attr( $active_class ) . '" id="time-slot-' . esc_attr( $day_of_week ) . '-' . esc_attr( $index ) . '" data-activity="' . esc_attr( $post_id ) . '" data-time="' . esc_attr( $time ) . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> ' . esc_attr( $time ) . '</span><span class="time-slots-remaining">( ' . esc_attr( $remaining_spots ) . ' of ' . esc_attr( $max_guests ) . ' remaining )</span></span> ';
						} else {
							echo '<span class="time-slot ' . esc_attr( $active_class ) . '" id="time-slot-' . esc_attr( $day_of_week ) . '-' . esc_attr( $index ) . '" data-activity="' . esc_attr( $post_id ) . '" data-time="' . esc_attr( $time ) . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> Unavailable</span><span class="time-slots-remaining">( - of - )</span></span> ';
						}
					}
					echo '</div>';
				}

				echo '</div>'; // Close the activity-schedule div
			}
		}

		// Close the container div
		echo '</div>';

		// Reset post data
		wp_reset_postdata();
	}

	/**
	 * Method calculate_remaining_spots
	 *
	 * @param $activity_id
	 * @param $selected_date
	 * @param $selected_time
	 * @param $max_guests
	 *
	 * @return void
	 */
	public function calculate_remaining_spots( $activity_id, $selected_date, $selected_time, $max_guests ) {
		// Query all reservation posts for this activity, date, and time
		$args         = array(
			'post_type'      => 'slgc_activityres',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_activity_id',
					'value' => $activity_id,
				),
				array(
					'key'   => 'staylodgic_reservation_checkin',
					'value' => $selected_date,
				),
				array(
					'key'   => 'staylodgic_activity_time',
					'value' => $selected_time,
				),
			),
		);
		$reservations = new \WP_Query( $args );

		// Calculate the total number of guests from the reservations
		$total_guests = 0;
		if ( $reservations->have_posts() ) {
			while ( $reservations->have_posts() ) {
				$reservations->the_post();
				$adults        = get_post_meta( get_the_ID(), 'staylodgic_reservation_activity_adults', true );
				$children      = get_post_meta( get_the_ID(), 'staylodgic_reservation_activity_children', true );
				$total_guests += $adults + $children['number'];
			}
		}

		wp_reset_postdata();
		// Calculate remaining spots
		$remaining_spots = $max_guests - $total_guests;

		return $remaining_spots;
	}

	/**
	 * Method build_reservation_array
	 *
	 * @param $booking_data
	 *
	 * @return void
	 */
	public function build_reservation_array( $booking_data ) {
		$stay_reservation_array = array();

		if ( array_key_exists( 'bookingnumber', $booking_data ) ) {
			$stay_reservation_array['bookingnumber'] = $booking_data['bookingnumber'];
		}
		if ( array_key_exists( 'choice', $booking_data ) ) {
			$stay_reservation_array['date'] = $booking_data['choice']['date'];
		}
		if ( array_key_exists( 'choice', $booking_data ) ) {
			$stay_reservation_array['activity_id'] = $booking_data['choice']['activity_id'];
		}
		if ( array_key_exists( 'choice', $booking_data ) ) {
			$stay_reservation_array['time'] = $booking_data['choice']['time'];
		}
		if ( array_key_exists( 'choice', $booking_data ) ) {
			$stay_reservation_array['price'] = $booking_data['choice']['price'];
		}
		if ( array_key_exists( 'adults', $booking_data ) ) {
			$stay_reservation_array['adults'] = $booking_data['adults'];
		}
		if ( array_key_exists( 'children', $booking_data ) ) {
			$stay_reservation_array['children'] = $booking_data['children'];
		}
		if ( array_key_exists( 'children_age', $booking_data ) ) {
			$stay_reservation_array['children_age'] = $booking_data['children_age'];
		}
		if ( array_key_exists( 'person_total', $booking_data ) ) {
			$stay_reservation_array['person_total'] = $booking_data['person_total'];
		}

		$stay_reservation_array['staynights'] = 1;

		$currency = staylodgic_get_option( 'currency' );
		if ( isset( $currency ) ) {
			$stay_reservation_array['currency'] = $currency;
		}

		$tax_instance = new \Staylodgic\Tax( 'activities' );

		$subtotalprice                           = intval( $stay_reservation_array['price'] );
		$stay_reservation_array['tax']           = $tax_instance->apply_tax( $subtotalprice, $stay_reservation_array['staynights'], $stay_reservation_array['person_total'], $output = 'data' );
		$stay_reservation_array['tax_html']      = $tax_instance->apply_tax( $subtotalprice, $stay_reservation_array['staynights'], $stay_reservation_array['person_total'], $output = 'html' );
		$rateperperson                           = intval( $subtotalprice ) / intval( $stay_reservation_array['person_total'] );
		$rateperperson_rounded                   = round( $rateperperson, 2 );
		$stay_reservation_array['rateperperson'] = $rateperperson_rounded;
		$stay_reservation_array['subtotal']      = round( $subtotalprice, 2 );
		$stay_reservation_array['total']         = $stay_reservation_array['tax']['total'];

		return $stay_reservation_array;
	}


	/**
	 * Method book_activity
	 *
	 * @return void
	 */
	public function book_activity() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_roomlistingbox_nonce'] ) || ! check_admin_referer( 'staylodgic-roomlistingbox-nonce', 'staylodgic_roomlistingbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$serialized_data = $_POST['bookingdata'];
		// Parse the serialized data into an associative array
		parse_str( $serialized_data, $form_data );

		// Generate unique booking number
		$booking_number = sanitize_text_field( $_POST['booking_number'] );
		$booking_data   = staylodgic_get_booking_transient( $booking_number );

		if ( ! isset( $booking_data ) ) {
			wp_send_json_error( 'Invalid or timeout. Please try again' );
		}
		// Obtain customer details from form submission
		$bookingdata    = sanitize_text_field( $_POST['bookingdata'] );
		$booking_number = sanitize_text_field( $_POST['booking_number'] );
		$full_name      = sanitize_text_field( $_POST['full_name'] );
		$passport       = sanitize_text_field( $_POST['passport'] );
		$email_address  = sanitize_email( $_POST['email_address'] );
		$phone_number   = sanitize_text_field( $_POST['phone_number'] );
		$street_address = sanitize_text_field( $_POST['street_address'] );
		$city           = sanitize_text_field( $_POST['city'] );
		$state          = sanitize_text_field( $_POST['state'] );
		$zip_code       = sanitize_text_field( $_POST['zip_code'] );
		$country        = sanitize_text_field( $_POST['country'] );
		$guest_comment  = sanitize_text_field( $_POST['guest_comment'] );
		$guest_consent  = sanitize_text_field( $_POST['guest_consent'] );

		// add other fields as necessary
		$rooms                  = array();
		$rooms['0']['id']       = $booking_data['choice']['activity_id'];
		$rooms['0']['quantity'] = '1';
		$adults                 = $booking_data['adults'];
		$children               = $booking_data['children'];

		$stay_reservation_data = self::build_reservation_array( $booking_data );

		$stay_reservation_data['customer']['full_name']      = $full_name;
		$stay_reservation_data['customer']['passport']       = $passport;
		$stay_reservation_data['customer']['email_address']  = $email_address;
		$stay_reservation_data['customer']['phone_number']   = $phone_number;
		$stay_reservation_data['customer']['street_address'] = $street_address;
		$stay_reservation_data['customer']['city']           = $city;
		$stay_reservation_data['customer']['state']          = $state;
		$stay_reservation_data['customer']['zip_code']       = $zip_code;
		$stay_reservation_data['customer']['country']        = $country;
		$stay_reservation_data['customer']['guest_comment']  = $guest_comment;
		$stay_reservation_data['customer']['guest_consent']  = $guest_consent;

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
				'staylodgic_guest_comment'  => $guest_comment,
				'staylodgic_guest_consent'  => $guest_consent,
				// add other meta data you need
			),
		);

		// Insert the post
		$customer_post_id = wp_insert_post( $customer_post_data );

		if ( ! $customer_post_id ) {
			wp_send_json_error( 'Could not save Customer: ' . $customer_post_id );
			return;
		}

		$checkin = $stay_reservation_data['date'];
		$room_id = $stay_reservation_data['activity_id'];

		$children_array           = array();
		$children_array['number'] = $stay_reservation_data['children'];

		foreach ( $stay_reservation_data['children_age'] as $key => $value ) {
			$children_array['age'][] = $value;
		}

		$tax_status = 'excluded';
		$tax_html   = false;
		if ( staylodgic_has_activity_tax() ) {
			$tax_status   = 'enabled';
			$tax_instance = new \Staylodgic\Tax( 'activities' );
			$tax_html     = $tax_instance->tax_summary( $stay_reservation_data['tax_html']['details'] );
		}

		$new_bookingstatus = staylodgic_get_option( 'new_bookingstatus' );
		if ( 'pending' !== $new_bookingstatus && 'confirmed' !== $new_bookingstatus ) {
			$new_bookingstatus = 'pending';
		}
		$new_bookingsubstatus = staylodgic_get_option( 'new_bookingsubstatus' );
		if ( '' !== $new_bookingstatus ) {
			$new_bookingsubstatus = 'onhold';
		}

		$reservation_booking_uid = \Staylodgic\Common::generate_uuid();

		$signature = md5( 'staylodgic_booking_system' );

		$booking_channel = 'Staylodgic';

		// Here you can also add other post data like post_title, post_content etc.
		$post_data = array(
			'post_type'   => 'slgc_activityres', // Your custom post type
			'post_title'  => $booking_number, // Set the booking number as post title
			'post_status' => 'publish', // The status you want to give new posts
			'meta_input'  => array(
				'staylodgic_activity_id'                   => $stay_reservation_data['activity_id'],
				'staylodgic_reservation_status'            => $new_bookingstatus,
				'staylodgic_reservation_substatus'         => $new_bookingsubstatus,
				'staylodgic_reservation_checkin'           => $checkin,
				'staylodgic_activity_time'                 => $stay_reservation_data['time'],
				'staylodgic_checkin_date'                  => $checkin,
				'staylodgic_tax'                           => $tax_status,
				'staylodgic_tax_html_data'                 => $tax_html,
				'staylodgic_tax_data'                      => $stay_reservation_data['tax'],
				'staylodgic_reservation_activity_adults'   => $stay_reservation_data['adults'],
				'staylodgic_reservation_activity_children' => $children_array,
				'staylodgic_reservation_rate_per_person'   => $stay_reservation_data['rateperperson'],
				'staylodgic_reservation_subtotal_activity_cost' => $stay_reservation_data['subtotal'],
				'staylodgic_reservation_total_room_cost'   => $stay_reservation_data['total'],
				'staylodgic_booking_number'                => $booking_number,
				'staylodgic_booking_uid'                   => $reservation_booking_uid,
				'staylodgic_customer_id'                   => $customer_post_id,
				'staylodgic_ics_signature'                 => $signature,
				'staylodgic_booking_data'                  => $stay_reservation_data,
				'staylodgic_booking_channel'               => $booking_channel,
			),
		);

		// Insert the post
		$reservation_post_id = wp_insert_post( $post_data );

		if ( $reservation_post_id ) {
			// Successfully created a reservation post
			$data_instance = new \Staylodgic\Data();
			$data_instance->update_reservations_array_on_save( $reservation_post_id, get_post( $reservation_post_id ), true );

			$stay_room_name = \Staylodgic\Rooms::get_room_name_from_id( $room_id );

			$email_tax_html = false;
			if ( 'enabled' === $tax_status ) {
				$email_tax_html = $stay_reservation_data['tax_html']['details'];
			}

			$booking_details = array(
				'guestName'            => $full_name,
				'stay_booking_number'  => $booking_number,
				'roomTitle'            => $stay_room_name,
				'stay_checkin_date'    => $checkin,
				'stay_adult_guests'    => $stay_reservation_data['adults'],
				'stay_children_guests' => $stay_reservation_data['children'],
				'subtotal'             => staylodgic_price( $stay_reservation_data['subtotal'] ),
				'tax'                  => $email_tax_html,
				'stay_total_cost'      => $stay_reservation_data['total'],
			);

			$email = new Email_Dispatcher( $email_address, __( 'Booking Confirmation for: ', 'staylodgic' ) . $booking_number );
			$email->set_html_content()->set_activity_confirmation_template( $booking_details );

			if ( $email->send() ) {
				// Confirmation email sent successfully to the guest
			} else {
				// Failed to send the confirmation email
			}
		} else {
			// Handle error
		}

		// Send a success response at the end of your function, if all operations are successful
		wp_send_json_success( 'Booking successfully registered.' );
		wp_die();
	}
}

$instance = new \Staylodgic\Activity();
