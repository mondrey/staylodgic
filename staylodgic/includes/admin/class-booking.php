<?php

namespace Staylodgic;

class Booking {


	protected $stay_checkin_date;
	protected $stay_checkout_date;
	protected $staynights;
	protected $stay_adult_guests;
	protected $stay_children_guests;
	protected $stay_total_guests;
	protected $total_chargeable_guests;
	protected $room_array;
	protected $rates_array;
	protected $room_can_accomodate;
	protected $stay_booking_number;
	protected $children_age;
	protected $booking_search_results;
	protected $discount_label;

	public function __construct(
		$stay_booking_number = null,
		$stay_checkin_date = null,
		$stay_checkout_date = null,
		$staynights = null,
		$stay_adult_guests = null,
		$stay_children_guests = null,
		$children_age = null,
		$stay_total_guests = null,
		$total_chargeable_guests = null,
		$room_array = null,
		$rates_array = null,
		$room_can_accomodate = null,
		$booking_search_results = null,
		$discount_label = null
	) {
		$this->stay_checkin_date       = $stay_checkin_date;
		$this->stay_checkout_date      = $stay_checkout_date;
		$this->staynights              = $staynights;
		$this->stay_adult_guests       = $stay_adult_guests;
		$this->stay_children_guests    = $stay_children_guests;
		$this->stay_total_guests       = $stay_total_guests;
		$this->total_chargeable_guests = $total_chargeable_guests;
		$this->children_age            = $children_age;
		$this->room_array              = $room_array;
		$this->rates_array             = $rates_array;
		$this->room_can_accomodate     = $room_can_accomodate;
		$this->booking_search_results  = $booking_search_results;
		$this->discount_label          = $discount_label;
		$this->stay_booking_number     = uniqid();

		add_shortcode( 'hotel_booking_search', array( $this, 'hotel_booking_search_form' ) );
		add_shortcode( 'hotel_booking_details', array( $this, 'hotel_booking_specs_details' ) );
		// AJAX handler to save room metadata

		add_action( 'wp_ajax_booking_booking_search', array( $this, 'booking_booking_search' ) );
		add_action( 'wp_ajax_nopriv_booking_booking_search', array( $this, 'booking_booking_search' ) );

		add_action( 'wp_ajax_process_room_price', array( $this, 'process_room_price' ) ); // For logged-in users
		add_action( 'wp_ajax_nopriv_process_room_price', array( $this, 'process_room_price' ) ); // For non-logged-in users

		add_action( 'wp_ajax_process_selected_room', array( $this, 'process_selected_room' ) ); // For logged-in users
		add_action( 'wp_ajax_nopriv_process_selected_room', array( $this, 'process_selected_room' ) ); // For non-logged-in users

		add_action( 'wp_ajax_generate_bed_metabox', array( $this, 'generate_bed_metabox_callback' ), 10, 3 ); // For logged-in users

		add_action( 'wp_ajax_book_rooms', array( $this, 'book_rooms' ) );
		add_action( 'wp_ajax_nopriv_book_rooms', array( $this, 'book_rooms' ) );
	}

	/**
	 * Method process_room_data
	 *
	 * @return void
	 */
	public function process_room_data(
		$bookingnumber = null,
		$room_id = null,
		$room_price = null,
		$bed_layout = null,
		$meal_plan = null,
		$meal_plan_price = null
	) {
		// Get the data sent via AJAX

		$stay_room_name = \Staylodgic\Rooms::get_room_name_from_id( $room_id );

		$booking_results = staylodgic_get_booking_transient( $bookingnumber );

		// Return a response (you can modify this as needed)
		$response = array(
			'success' => true,
			'message' => 'Data: ' . $stay_room_name . ',received successfully.',
		);

		if ( is_array( $booking_results ) ) {

			$booking_results['choice']['room_id']   = $room_id;
			$booking_results['choice']['bedlayout'] = $bed_layout;
			$booking_results['choice']['mealplan']  = $meal_plan;

			$booking_results['choice']['mealplan_price'] = 0;
			if ( 'none' !== $meal_plan ) {
				$booking_results['choice']['mealplan_price'] = $booking_results[ $room_id ]['meal_plan'][ $booking_results['choice']['mealplan'] ];
			}

			$booking_results['choice']['room_id'] = $room_id;

			staylodgic_set_booking_transient( $booking_results, $bookingnumber );

		} else {
			$booking_results = false;
		}

		// Send the JSON response
		return $booking_results;
	}

	/**
	 * Method process_selected_room
	 *
	 * @return void
	 */
	public function process_selected_room() {

		$bookingnumber   = sanitize_text_field( $_POST['bookingnumber'] );
		$room_id         = sanitize_text_field( $_POST['room_id'] );
		$room_price      = sanitize_text_field( $_POST['room_price'] );
		$bed_layout      = sanitize_text_field( $_POST['bed_layout'] );
		$meal_plan       = sanitize_text_field( $_POST['meal_plan'] );
		$meal_plan_price = sanitize_text_field( $_POST['meal_plan_price'] );

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_roomlistingbox_nonce'] ) || ! check_admin_referer( 'staylodgic-roomlistingbox-nonce', 'staylodgic_roomlistingbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$booking_results = self::process_room_data(
			$bookingnumber,
			$room_id,
			$room_price,
			$bed_layout,
			$meal_plan,
			$meal_plan_price
		);

		if ( is_array( $booking_results ) ) {

			$html = self::booking_summary(
				$bookingnumber,
				$booking_results['choice']['room_id'],
				$booking_results[ $room_id ]['roomtitle'],
				$booking_results['checkin'],
				$booking_results['checkout'],
				$booking_results['staynights'],
				$booking_results['adults'],
				$booking_results['children'],
				$booking_results['choice']['bedlayout'],
				$booking_results['choice']['mealplan'],
				$booking_results['choice']['mealplan_price'],
				$booking_results[ $room_id ]['staydate'],
				$booking_results[ $room_id ]['totalroomrate']
			);
		} else {
			$html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>' . __( 'Session timed out. Please reload the page.', 'staylodgic' ) . '</div>';
		}

		// Send the JSON response
		wp_send_json( $html );
	}

	/**
	 * Method process_room_price
	 *
	 * @return void
	 */
	public function process_room_price() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_searchbox_nonce'] ) || ! check_admin_referer( 'staylodgic-searchbox-nonce', 'staylodgic_searchbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$bookingnumber   = sanitize_text_field( $_POST['booking_number'] );
		$room_id         = sanitize_text_field( $_POST['room_id'] );
		$room_price      = sanitize_text_field( $_POST['room_price'] );
		$bed_layout      = sanitize_text_field( $_POST['bed_layout'] );
		$meal_plan       = sanitize_text_field( $_POST['meal_plan'] );
		$meal_plan_price = sanitize_text_field( $_POST['meal_plan_price'] );

		$booking_results = self::process_room_data(
			$bookingnumber,
			$room_id,
			$room_price,
			$bed_layout,
			$meal_plan,
			$meal_plan_price
		);

		if ( is_array( $booking_results ) ) {

			$html = self::get_selected_plan_price( $room_id, $booking_results );
		} else {
			$html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>' . __( 'Session timed out. Please reload the page.', 'staylodgic' ) . '</div>';
		}

		// Send the JSON response
		wp_send_json( $html );
	}

	/**
	 * Method get_selected_plan_price
	 *
	 * @param $room_id $room_id
	 * @param $booking_results $booking_results
	 *
	 * @return void
	 */
	public function get_selected_plan_price( $room_id, $booking_results ) {
		$total_price_tag = staylodgic_price( intval( $booking_results[ $room_id ]['totalroomrate'] ) + intval( $booking_results['choice']['mealplan_price'] ) );
		return $total_price_tag;
	}

	/**
	 * Method booking_summary
	 *
	 * @return void
	 */
	public function booking_summary(
		$bookingnumber = null,
		$room_id = null,
		$room_name = null,
		$checkin = null,
		$checkout = null,
		$staynights = null,
		$adults = null,
		$children = null,
		$bedtype = null,
		$mealtype = null,
		$mealprice = null,
		$perdayprice = null,
		$totalroomrate = null
	) {

		$totalguests = intval( $adults ) + intval( $children );
		$totalprice  = array();

		$html = '<div id="booking-summary-wrap">';
		if ( '' !== $room_name ) {
			$html .= '<div class="room-summary"><span class="summary-room-name">' . esc_html( $room_name ) . '</span></div>';
		}

		$html .= '<div class="main-summary-wrap">';

		$html .= \Staylodgic\Common::generate_person_icons( $adults, $children );

		if ( '' !== $bedtype ) {
			$html .= '<div class="bed-summary">' . staylodgic_get_all_bed_layouts( $bedtype ) . '</div>';
		}
		$html .= '</div>';

		if ( '' !== $room_id ) {
			$html .= '<div class="meal-summary-wrap">';
			if ( '' !== self::generate_meal_plan_included( $room_id ) ) {
				$html .= '<div class="meal-summary"><span class="summary-mealtype-inlcuded">' . self::generate_meal_plan_included( $room_id ) . '</span></div>';
			}
			if ( 'none' !== $mealtype ) {
				$html .= '<div class="summary-icon mealplan-summary-icon"><i class="fa-solid fa-utensils"></i></div>';
				$html .= '<div class="summary-heading mealplan-summary-heading">' . __( 'Mealplan', 'staylodgic' ) . ':</div>';
				$html .= '<div class="meal-summary"><span class="summary-mealtype-name">' . staylodgic_get_mealplan_labels( $mealtype ) . '</span></div>';
			}
			$html .= '</div>';
		}

		$html .= '<div class="stay-summary-wrap">';

		$html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
		$html .= '<div class="summary-heading checkin-summary-heading">' . __( 'Check-in:', 'staylodgic' ) . '</div>';
		$html .= '<div class="checkin-summary">' . esc_html( $checkin ) . '</div>';
		$html .= '<div class="summary-heading checkout-summary-heading">' . __( 'Check-out:', 'staylodgic' ) . '</div>';
		$html .= '<div class="checkout-summary">' . esc_html( $checkout ) . '</div>';

		$html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
		$html .= '<div class="summary-heading staynight-summary-heading">' . __( 'Nights:', 'staylodgic' ) . '</div>';
		$html .= '<div class="staynight-summary">' . esc_html( $staynights ) . '</div>';
		$html .= '</div>';

		if ( '' !== $totalroomrate ) {
			$subtotalprice = intval( $totalroomrate ) + intval( $mealprice );
			$html         .= '<div class="price-summary-wrap">';

			if ( staylodgic_has_tax() ) {
				$html .= '<div class="summary-heading total-summary-heading">' . __( 'Subtotal:', 'staylodgic' ) . '</div>';
				$html .= '<div class="price-summary">' . staylodgic_price( $subtotalprice ) . '</div>';
			}

			$html .= '<div class="summary-heading total-summary-heading">' . __( 'Total:', 'staylodgic' ) . '</div>';

			$tax_instance = new \Staylodgic\Tax( 'room' );
			$totalprice   = $tax_instance->apply_tax( $subtotalprice, $staynights, $totalguests, $output = 'html' );
			foreach ( $totalprice['details'] as $total_id => $totalvalue ) {
				$html .= '<div class="tax-summary tax-summary-details">' . wp_kses( $totalvalue, staylodgic_get_allowed_tags() ) . '</div>';
			}

			$html .= '<div class="tax-summary tax-summary-total">' . staylodgic_price( $totalprice['total'] ) . '</div>';
			$html .= '</div>';
		}

		if ( '' !== $room_id ) {
			$html .= '<div class="form-group">';
			$html .= '<div id="bookingResponse" class="booking-response"></div>';
			$html .= '<div id="booking-register" class="book-button">' . __( 'Book this room', 'staylodgic' ) . '</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Method hotel_booking_search_form
	 *
	 * @return void
	 */
	public function hotel_booking_search_form() {
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
		$display_fullbooked_status = false;
		if ( true === $display_fullbooked_status ) {
			$reservations_instance = new \Staylodgic\Reservations();
			$fullybooked_dates     = $reservations_instance->days_fully_booked_for_date_range( $stay_current_date, $stay_end_date );
		}
		?>
		<div class="staylodgic-content">
			<div id="hotel-booking-form">
				<div class="front-booking-search">
					<div class="front-booking-calendar-wrap">
						<div class="front-booking-calendar-icon"><i class="fa-solid fa-calendar-days"></i></div>
						<div class="front-booking-calendar-date"><?php _e( 'Choose stay dates', 'staylodgic' ); ?></div>
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
						<div id="bookingSearch" class="form-search-button"><?php _e( 'Search', 'staylodgic' ); ?></div>
					</div>
				</div>


				<div class="staylodgic_reservation_datepicker">
					<input type="hidden" name="staylodgic_searchbox_nonce" value="<?php echo esc_attr( $searchbox_nonce ); ?>" />
					<?php
					// Encode the fully booked dates
					$encoded_dates = wp_json_encode( $fullybooked_dates );

					// Escape the encoded dates for output
					$escaped_dates = htmlspecialchars( $encoded_dates, ENT_QUOTES, 'UTF-8' );
					?>
					<input data-booked="<?php echo $escaped_dates; ?>" type="date" id="reservation-date" name="reservation_date">
				</div>
				<div class="staylodgic_reservation_room_guests_wrap">
					<div id="staylodgic_reservation_room_adults_wrap" class="number-input occupant-adult occupants-range">
						<div class="column-one">
							<label for="number-of-adults"><?php _e( 'Adults', 'staylodgic' ); ?></label>
						</div>
						<div class="column-two">
							<span class="minus-btn">-</span>
							<input data-guest="adult" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-adults" value="2" name="number_of_adults" type="text" class="number-value" readonly="">
							<span class="plus-btn">+</span>
						</div>
					</div>
					<div id="staylodgic_reservation_room_children_wrap" class="number-input occupant-child occupants-range">
						<div class="column-one">
							<label for="number-of-adults"><?php _e( 'Children', 'staylodgic' ); ?></label>
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
	 * Method hotel_booking_specs_details
	 *
	 * @return void
	 */
	public function hotel_booking_specs_details() {
		ob_start();
		$staylodgic_bookingdetails_nonce = wp_create_nonce( 'staylodgic-bookingdetails-nonce' );
		?>
		<div class="staylodgic-content">
			<div id="hotel-booking-form">

				<div class="front-booking-search">
					<div class="front-booking-number-wrap">
						<div class="front-booking-number-container">
							<div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
								<input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr( $staylodgic_bookingdetails_nonce ); ?>" />
								<input placeholder="<?php _e( 'Booking No.', 'staylodgic' ); ?>" type="text" class="form-control" id="booking_number" name="booking_number" required>
								<label for="booking_number" class="control-label"><?php _e( 'Booking No.', 'staylodgic' ); ?></label>
							</div>
						</div>
						<div data-request="bookingdetails" id="booking_details" class="form-search-button"><?php _e( 'Search', 'staylodgic' ); ?></div>
					</div>
				</div>

				<div class="booking-details-lister">
					<div id="booking-details-ajax"></div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Method alternative_booking_dates
	 *
	 * @param $stay_checkin_date $stay_checkin_date
	 * @param $stay_checkout_date $stay_checkout_date
	 * @param $max_occpuants $max_occpuants
	 *
	 * @return void
	 */
	public function alternative_booking_dates( $stay_checkin_date, $stay_checkout_date, $max_occpuants ) {

		// Perform the greedy search by adjusting the check-in and check-out dates
		$new_checkin_date  = new \DateTime( $stay_checkin_date );
		$new_checkout_date = new \DateTime( $stay_checkout_date );

		$reservation_instance = new \Staylodgic\Reservations();
		$room_instance        = new \Staylodgic\Rooms();

		$available_room_dates = $reservation_instance->availability_of_rooms_for_date_range( $new_checkin_date->format( 'Y-m-d' ), $new_checkout_date->format( 'Y-m-d' ), 3 );

		$new_room_availability_array = array();

		// Process each sub-array
		foreach ( $available_room_dates as $stay_room_id => $sub_array ) {
			// Initialize the new sub-array for the current room
			$new_sub_array = array();

			// Get the first and last keys of the inner arrays
			foreach ( $sub_array as $inner_array ) {
				$keys      = array_keys( $inner_array );
				$first_key = $keys[0];
				$last_key  = end( $keys );

				// Keep only the first and last records and assign unique indexes
				$new_sub_array[ $first_key ] = array(
					'check-in'  => $first_key,
					'check-out' => $last_key,
				);
			}
			$can_accomodate = $room_instance->get_max_room_occupants( $stay_room_id );
			if ( $can_accomodate['guests'] >= $max_occpuants ) {
				// Add the new sub-array to the new room availability array
				$new_room_availability_array[ $stay_room_id ] = $new_sub_array;
			}
		}
		$room_availability_array = $new_room_availability_array;

		// Initialize an empty string
		$output = '';

		$processed_dates     = array(); // Array to store processed check-in and checkout dates
		$new_processed_dates = array();

		foreach ( $room_availability_array as $key => $subset ) {

			// Iterate through each sub array in the subset
			foreach ( $subset as $sub_array ) {

				$checkin_alt = $sub_array['check-in'];
				$staylast    = $sub_array['check-out'];

				// Check if the current check-in and checkout dates have already been processed
				if ( in_array( array( $checkin_alt, $staylast ), $processed_dates, true ) ) {
					continue; // Skip processing identical dates
				}

				// Add the current check-in and checkout dates to the processed dates array
				$processed_dates[] = array( $checkin_alt, $staylast );

				// Get the date one day after the staylast
				$checkout_alt = $staylast;

				$new_processed_dates[ $checkin_alt ] = array(
					'staylast'  => $staylast,
					'check-in'  => $checkin_alt,
					'check-out' => $checkout_alt,
				);

				// Perform operations with the sub array...
			}
		}

		ksort( $new_processed_dates );

		foreach ( $new_processed_dates as $key ) {
			$staylast     = $key['staylast'];
			$checkin_alt  = $key['check-in'];
			$checkout_alt = $key['check-out'];

			// Format the dates as "Month Day" (e.g., "July 13th")
			$formatted_first_date = gmdate( 'F jS', strtotime( $checkin_alt ) );

			// Add one day to the checkout date
			$checkout_plus_one = gmdate( 'Y-m-d', strtotime( $checkout_alt . ' +1 day' ) );

			// Format the next day
			$formatted_next_day = gmdate( 'F jS', strtotime( $checkout_plus_one ) );
			if ( gmdate( 'F', strtotime( $staylast ) ) !== gmdate( 'F', strtotime( $checkin_alt ) ) ) {
				$formatted_next_day = gmdate( 'F jS', strtotime( $checkout_plus_one ) );
			} else {
				$formatted_next_day = gmdate( 'jS', strtotime( $checkout_plus_one ) );
			}

			$output .= "<span data-check-staylast='" . esc_attr( $staylast ) . "' data-check-in='" . esc_attr( $checkin_alt ) . "' data-check-out='" . esc_attr( $checkout_plus_one ) . "'>" . esc_attr( $formatted_first_date ) . ' - ' . esc_attr( $formatted_next_day ) . '</span>';
		}

		// Remove the trailing comma and space
		$output = rtrim( $output, ', ' );

		if ( '' !== $output ) {
			$output_text = '<div class="recommended-alt-title"><i class="fas fa-calendar-times"></i>' . __( 'Rooms unavailable', 'staylodgic' ) . '</div><div class="recommended-alt-description">' . __( 'Following range from your selection is avaiable.', 'staylodgic' ) . '</div>';
		} else {
			$output_text = '<div class="recommended-alt-title"><i class="fas fa-calendar-times"></i>' . __( 'Rooms unavailable', 'staylodgic' ) . '</div><div class="recommended-alt-description">' . __( 'No rooms found within your selection.', 'staylodgic' ) . '</div>';
		}
		// Print the output
		$room_availability = '<div class="recommended-dates-wrap">' . $output_text . $output . '</div>';

		return $room_availability;
	}

	/**
	 * Method booking_booking_search
	 *
	 * @return void
	 */
	public function booking_booking_search() {
		$room_type          = '';
		$number_of_children = 0;
		$number_of_adults   = 0;
		$number_of_guests   = 0;
		$children_age       = array();
		$reservation_date   = '';

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_searchbox_nonce'] ) || ! check_admin_referer( 'staylodgic-searchbox-nonce', 'staylodgic_searchbox_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		if ( isset( $_POST['reservation_date'] ) ) {
			$reservation_date = $_POST['reservation_date'];
		}

		if ( isset( $_POST['number_of_adults'] ) ) {
			$number_of_adults = $_POST['number_of_adults'];
		}

		if ( isset( $_POST['number_of_children'] ) ) {
			$number_of_children = $_POST['number_of_children'];
		}

		$free_stay_age_under = staylodgic_get_option( 'childfreestay' );

		$free_stay_child_count = 0;

		if ( isset( $_POST['children_age'] ) ) {
			// Loop through all the select elements with the class 'children-age-selector'
			foreach ( $_POST['children_age'] as $selected_age ) {
				// Sanitize and store the selected values in an array
				$children_age[] = sanitize_text_field( $selected_age );
				if ( $selected_age < $free_stay_age_under ) {
					$free_stay_child_count = $free_stay_child_count + 1;
				}
			}
		}

		$this->children_age = $children_age;

		$number_of_guests = intval( $number_of_adults ) + intval( $number_of_children );

		$this->stay_adult_guests       = $number_of_adults;
		$this->stay_children_guests    = $number_of_children;
		$this->stay_total_guests       = $number_of_guests;
		$this->total_chargeable_guests = $number_of_guests - $free_stay_child_count;

		if ( isset( $_POST['room_type'] ) ) {
			$room_type = $_POST['room_type'];
		}

		$chosen_date = \Staylodgic\Common::split_date_range( $reservation_date );

		$stay_checkin_date  = '';
		$stay_checkout_date = '';

		if ( isset( $chosen_date['stay_start_date'] ) ) {
			$stay_checkin_date = $chosen_date['stay_start_date'];
			$checkin_date_obj  = new \DateTime( $chosen_date['stay_start_date'] );
		}
		if ( isset( $chosen_date['stay_end_date'] ) ) {
			$stay_checkout_date = $chosen_date['stay_end_date'];
			$checkout_date_obj  = new \DateTime( $stay_checkout_date );

			$real_checkout_date     = gmdate( 'Y-m-d', strtotime( $stay_checkout_date . ' +1 day' ) );
			$real_checkout_date_obj = new \DateTime( $real_checkout_date );
		}

		// Calculate the number of nights
		$staynights = $checkin_date_obj->diff( $real_checkout_date_obj )->days;

		$this->stay_checkin_date  = $stay_checkin_date;
		$this->stay_checkout_date = $real_checkout_date;
		$this->staynights         = $staynights;

		$this->booking_search_results                     = array();
		$this->booking_search_results['bookingnumber']    = $this->stay_booking_number;
		$this->booking_search_results['checkin']          = $this->stay_checkin_date;
		$this->booking_search_results['checkout']         = $this->stay_checkout_date;
		$this->booking_search_results['staynights']       = $this->staynights;
		$this->booking_search_results['adults']           = $this->stay_adult_guests;
		$this->booking_search_results['children']         = $this->stay_children_guests;
		$this->booking_search_results['children_age']     = $this->children_age;
		$this->booking_search_results['totalguest']       = $this->stay_total_guests;
		$this->booking_search_results['chargeableguests'] = $this->total_chargeable_guests;

		$room_instance = new \Staylodgic\Rooms();

		// Get a combined array of rooms and rates which are available for the dates.
		$combo_array = $room_instance->get_available_rooms_rates_occupants_for_date_range( $this->stay_checkin_date, $stay_checkout_date );

		$this->room_array          = $combo_array['rooms'];
		$this->rates_array         = $combo_array['rates'];
		$this->room_can_accomodate = $combo_array['occupants'];

		$available_room_dates = array();

		$requested_room_availability = false;

		if ( count( $combo_array['rooms'] ) === 0 ) {

			$requested_room_availability = self::alternative_booking_dates( $stay_checkin_date, $stay_checkout_date, $number_of_guests );
		}

		$list = self::list_rooms_and_quantities();

		ob_start();
		if ( $list ) {
			echo '<form action="" method="post" id="hotel-room-listing" class="needs-validation" novalidate>';
			$roomlistingbox = wp_create_nonce( 'staylodgic-roomlistingbox-nonce' );
			echo '<input type="hidden" name="staylodgic_roomlistingbox_nonce" value="' . esc_attr( $roomlistingbox ) . '" />';
			echo '<div id="reservation-data" data-bookingnumber="' . esc_attr( $this->stay_booking_number ) . '" data-children="' . esc_attr( $this->stay_children_guests ) . '" data-adults="' . esc_attr( $this->stay_adult_guests ) . '" data-guests="' . esc_attr( $this->stay_total_guests ) . '" data-checkin="' . esc_attr( $this->stay_checkin_date ) . '" data-checkout="' . esc_attr( $this->stay_checkout_date ) . '">';
			echo $list;
			echo '</div>';
		} else {
			echo '<div class="no-rooms-found">';
			echo '<div class="no-rooms-title">' . __( 'Rooms unavailable for choice', 'staylodgic' ) . '</div>';
			echo '<div class="no-rooms-description">' . __( 'Please choose a different range.', 'staylodgic' ) . '</div>';
			echo '</div>';
		}
		echo self::register_guest_form();
		echo '</form>';
		$output                     = ob_get_clean();
		$response['booking_data']   = $combo_array;
		$response['roomlist']       = $output;
		$response['alt_recommends'] = $requested_room_availability;
		echo wp_json_encode( $response, JSON_UNESCAPED_SLASHES );
		die();
	}

	/**
	 * Method list_rooms_and_quantities
	 *
	 * @return void
	 */
	public function list_rooms_and_quantities() {
		// Initialize empty string to hold HTML
		$html = '';

		$html .= self::list_rooms();

		// Return the resulting HTML string
		return $html;
	}

	/**
	 * Method can_this_room_accomodate
	 *
	 * @param $room_id $room_id
	 *
	 * @return void
	 */
	public function can_this_room_accomodate( $room_id ) {

		$status = true;
		if ( $this->room_can_accomodate[ $room_id ]['guests'] < $this->stay_total_guests ) {
			// Cannot accomodate number of guests
			$status = false;
		}

		if ( $this->room_can_accomodate[ $room_id ]['adults'] < $this->stay_adult_guests ) {
			// Cannot accomodate number of adults
			$status = false;
		}
		if ( $this->room_can_accomodate[ $room_id ]['children'] < $this->stay_children_guests ) {
			// Cannot accomodate number of children
			$status = false;
		}

		return $status;
	}

	/**
	 * Method list_rooms
	 *
	 * @return void
	 */
	public function list_rooms() {

		$html  = '';
		$count = 0;
		// Iterate through each room
		foreach ( $this->room_array as $id => $room_info ) {

			$room_data = get_post_custom( $id );

			$can_this_room_accomodate = self::can_this_room_accomodate( $id );
			if ( ! $can_this_room_accomodate ) {
				continue;
			}

			$max_guest_number       = intval( $this->room_can_accomodate[ $id ]['guests'] );
			$max_child_guest_number = intval( $this->room_can_accomodate[ $id ]['guests'] - 1 );
			// Append a div for the room with the room ID as a data attribute
			$html .= '<div class="room-occupied-group" data-adults="' . esc_attr( $this->room_can_accomodate[ $id ]['adults'] ) . '" data-children="' . esc_attr( $this->room_can_accomodate[ $id ]['children'] ) . '" data-guests="' . esc_attr( $this->room_can_accomodate[ $id ]['guests'] ) . '" data-room-id="' . esc_attr( $id ) . '">';
			$html .= '<div class="room-details">';

			foreach ( $room_info as $quantity => $title ) {

				$html .= '<div class="room-details-row">';
				$html .= '<div class="room-details-column">';

				$html             .= '<div class="room-details-image">';
				$image_id          = get_post_thumbnail_id( $id );
				$fullimage_url     = wp_get_attachment_image_url( $image_id, 'staylodgic-full' ); // Get the URL of the custom-sized image
				$image_url         = wp_get_attachment_image_url( $image_id, 'staylodgic-large-square' ); // Get the URL of the custom-sized image
				$html             .= '<a href="' . esc_url( $fullimage_url ) . '" data-toggle="lightbox" data-gallery="lightbox-gallery-' . esc_attr( $id ) . '">';
				$html             .= '<img class="lightbox-trigger room-summary-image" data-image="' . esc_url( $image_url ) . '" src="' . esc_url( $image_url ) . '" alt="Room">';
				$html             .= '</a>';
				$supported_gallery = staylodgic_output_custom_image_links( $id );
				if ( $supported_gallery ) {
					$html .= staylodgic_output_custom_image_links( $id );
				}
				$html .= '</div>';

				$html .= '<div class="room-details-stats">';

				if ( isset( $room_data['staylodgic_roomview'][0] ) ) {
					$roomview       = $room_data['staylodgic_roomview'][0];
					$roomview_array = staylodgic_get_room_views();
					if ( array_key_exists( $roomview, $roomview_array ) ) {
						$html .= '<div class="room-summary-roomview"><span class="room-summary-icon"><i class="fa-regular fa-eye"></i></span>' . esc_html( $roomview_array[ $roomview ] ) . '</div>';
					}
				}

				if ( isset( $room_data['staylodgic_room_size'][0] ) ) {
					$roomsize = $room_data['staylodgic_room_size'][0];
					$html    .= '<div class="room-summary-roomsize"><span class="room-summary-icon"><i class="fa-solid fa-vector-square"></i></span>' . esc_html( $roomsize ) . ' ft²</div>';
				}
				$html .= '</div>';

				$html .= '<div class="room-details-heading">';
				// Append the room title

				$this->booking_search_results[ $id ]['roomtitle'] = $title;

				$html .= '<h2>' . esc_html( $title ) . '</h2>';

				$html .= '</div>';

				if ( isset( $room_data['staylodgic_room_desc'][0] ) ) {
					$room_desc = $room_data['staylodgic_room_desc'][0];
					$html     .= '<div class="room-summary-roomdesc">' . esc_html( $room_desc ) . '</div>';
				}

				$html .= '<div class="room-details-facilities">';
				if ( isset( $room_data['staylodgic_room_facilities'][0] ) ) {
					$room_facilities = $room_data['staylodgic_room_facilities'][0];
					$html           .= staylodgic_string_to_html_spans( $room_facilities, $class = 'room-summary-facilities' );
				}
				$html .= '</div>';

				$html .= '</div>';
				$html .= '<div class="room-details-column room-details-second-column">';

				$html .= \Staylodgic\Common::generate_person_icons( $this->stay_adult_guests, $this->stay_children_guests );

				$html .= '<div class="checkin-staydate-wrap">';

				$total_roomrate                                       = self::calculate_room_price_total( $id );
				$this->booking_search_results[ $id ]['totalroomrate'] = $total_roomrate;

				$html .= '<div class="room-price-total" data-roomprice="' . esc_attr( $total_roomrate ) . '">' . staylodgic_price( $total_roomrate ) . '</div>';
				$html .= '<div class="preloader-element-outer"><div class="preloader-element"></div></div>';
				if ( isset( $this->booking_search_results[ $id ]['discountlabel'] ) ) {
					$html .= '<div class="room-price-discount-label"><i class="fa fa-star" aria-hidden="true"></i> ' . esc_html( $this->booking_search_results[ $id ]['discountlabel'] ) . '</div>';
				}

				$html .= self::generate_meal_plan_included( $id );

				$html .= '<div class="roomchoice-selection">';

				$html .= '<div class="roomchoice-seperator roomchoice-bedlayout">';
				$html .= '<div class="bedlayout-wrap">';
				$html .= '<div class="room-summary-heading room-bedlayout-heading" for="room-number-input">Bed Layout</div>';
				$html .= '</div>';
				$html .= '<input class="roomchoice" name="room[' . esc_attr( $id ) . '][quantity]" type="hidden" data-type="room-number" data-roominputid="' . esc_attr( $id ) . '" data-roomqty="' . esc_attr( $quantity ) . '" id="room-input-' . esc_attr( $id ) . '" min="0" max="' . esc_attr( $quantity ) . '" value="1">';
				$html .= self::generate_bed_information( $id );
				$html .= '</div>';

				$html .= '<div class="roomchoice-seperator roomchoice-mealplan">';
				$html .= self::generate_meal_plan_radio( $id );
				$html .= '</div>';

				$html .= '<div class="room-button-wrap">';
				$html .= '<div data-room-button-id="' . esc_attr( $id ) . '" class="choose-room-button book-button">' . __( 'Choose this room', 'staylodgic' ) . '</div>';
				$html .= '</div>';

				$html .= '</div>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}

			$html .= '<div class="stay-summary-wrap">';
			$html .= '<div class="checkin-summary">Check-in: ' . staylodgic_readable_date( $this->stay_checkin_date ) . '</div>';
			$html .= '<div class="checkout-summary">Check-out: ' . staylodgic_readable_date( $this->stay_checkout_date ) . '</div>';
			$html .= '<div class="staynight-summary">Nights: ' . esc_attr( $this->staynights ) . '</div>';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';

			staylodgic_set_booking_transient( $this->booking_search_results, $this->stay_booking_number );
		}

		return $html;
	}

	/**
	 * Method display_booking_per_day
	 *
	 * @param $rates_array_date $rates_array_date
	 *
	 * @return void
	 */
	public function display_booking_per_day( $rates_array_date ) {
		$total_roomrate = 0;
		$html           = '';
		foreach ( $rates_array_date as $staydate => $roomrate ) {
			$html .= '<div class="checkin-staydate"><span class="number-of-rooms"></span>' . esc_html( $staydate ) . ' - ' . staylodgic_price( $roomrate ) . '</div>';

			$roomrate = self::apply_price_per_person( $roomrate );

			$total_roomrate = $total_roomrate + $roomrate;
		}

		return $html;
	}

	/**
	 * Calculates long-stay discount.
	 *
	 * @param string $stay_checkin_date      The check-in date.
	 * @param string $stay_checkout_date     The check-out date.
	 * @param int    $long_stay_window   The number of days defining the long-stay window.
	 * @param float  $long_stay_discount The percentage discount to apply for long stays.
	 * @return float                   Calculated discount percentage or zero if not applicable.
	 */
	public function calculate_long_stay_discount( $stay_checkin_date, $stay_checkout_date, $long_stay_window, $long_stay_discount ) {
		$stay_checkin_date_time  = new \DateTime( $stay_checkin_date );
		$stay_checkout_date_time = new \DateTime( $stay_checkout_date );

		$interval      = $stay_checkin_date_time->diff( $stay_checkout_date_time );
		$stay_duration = (int) $interval->format( '%a' ); // Difference in days

		// Check if the stay duration is equal to or greater than the long-stay window
		if ( $stay_duration >= $long_stay_window ) {
			return $long_stay_discount;
		} else {
			return 0; // No discount if the stay is shorter than the long-stay window
		}
	}

	/**
	 * Calculates early booking discount.
	 *
	 * @param string $stay_checkin_date          The check-in date.
	 * @param int    $early_booking_window   The number of days defining the early booking window.
	 * @param float  $early_booking_discount The percentage discount to apply.
	 * @return float                       Calculated discount percentage or zero if not applicable.
	 */
	public function calculate_early_booking_discount( $stay_checkin_date, $early_booking_window, $early_booking_discount ) {
		$stay_current_date      = new \DateTime(); // Current date
		$stay_checkin_date_time = new \DateTime( $stay_checkin_date ); // Check-in date

		$interval           = $stay_current_date->diff( $stay_checkin_date_time );
		$days_until_checkin = (int) $interval->format( '%a' ); // Difference in days

		// Check if the booking is made sufficiently in advance
		if ( $days_until_checkin >= $early_booking_window ) {
			return $early_booking_discount;
		} else {
			return 0; // No discount if outside the early booking window
		}
	}

	/**
	 * Calculates last-minute discount.
	 *
	 * @param string $stay_checkin_date          The check-in date.
	 * @param int    $last_minute_window     The number of days defining the last-minute window.
	 * @param float  $last_minute_discount   The percentage discount to apply.
	 * @return float                       Calculated discount percentage or zero if not applicable.
	 */
	public function calculate_last_minute_discount( $stay_checkin_date, $last_minute_window, $last_minute_discount ) {
		$stay_current_date      = new \DateTime(); // Current date
		$stay_checkin_date_time = new \DateTime( $stay_checkin_date ); // Check-in date

		$interval           = $stay_current_date->diff( $stay_checkin_date_time );
		$days_until_checkin = (int) $interval->format( '%a' ); // Difference in days

		// Check if the booking is within the last-minute window
		if ( $days_until_checkin <= $last_minute_window ) {
			return $last_minute_discount;
		} else {
			return 0; // No discount if outside the last-minute window
		}
	}

	/**
	 * Calculates the highest applicable discount among last-minute, early booking, and long-stay,
	 * and identifies the type of discount.
	 *
	 * @param string $stay_checkin_date        The check-in date.
	 * @param string $stay_checkout_date       The check-out date.
	 * @param array  $discount_parameters Parameters for each discount type.
	 * @return array                     An array with the highest discount value and its type.
	 */
	public function calculate_highest_discount( $stay_checkin_date, $stay_checkout_date ) {
		$discount_parameters = array();
		$discount_label      = '';

		// Check and set parameters for last-minute discount
		$discount_lastminute = staylodgic_get_option( 'discount_lastminute' );
		if ( $discount_lastminute && isset( $discount_lastminute['days'], $discount_lastminute['percent'] ) ) {
			$discount_parameters['lastminute'] = array(
				'window'   => $discount_lastminute['days'],
				'discount' => $discount_lastminute['percent'],
				'label'    => $discount_lastminute['label'],
			);
		}

		// Check and set parameters for early booking discount
		$discount_earlybooking = staylodgic_get_option( 'discount_earlybooking' );
		if ( $discount_earlybooking && isset( $discount_earlybooking['days'], $discount_earlybooking['percent'] ) ) {
			$discount_parameters['earlybooking'] = array(
				'window'   => $discount_earlybooking['days'],
				'discount' => $discount_earlybooking['percent'],
				'label'    => $discount_earlybooking['label'],
			);
		}

		// Check and set parameters for long-stay discount
		$discount_longstay = staylodgic_get_option( 'discount_longstay' );
		if ( $discount_longstay && isset( $discount_longstay['days'], $discount_longstay['percent'] ) ) {
			$discount_parameters['longstay'] = array(
				'window'   => $discount_longstay['days'],
				'discount' => $discount_longstay['percent'],
				'label'    => $discount_longstay['label'],
			);
		}

		// Initialize discounts
		$last_minute_discount   = 0;
		$early_booking_discount = 0;
		$long_stay_discount     = 0;

		// Calculate discounts if parameters are set
		if ( isset( $discount_parameters['lastminute'] ) ) {
			$last_minute_discount = $this->calculate_last_minute_discount(
				$stay_checkin_date,
				$discount_parameters['lastminute']['window'],
				$discount_parameters['lastminute']['discount']
			);
		}

		if ( isset( $discount_parameters['earlybooking'] ) ) {
			$early_booking_discount = $this->calculate_early_booking_discount(
				$stay_checkin_date,
				$discount_parameters['earlybooking']['window'],
				$discount_parameters['earlybooking']['discount']
			);
		}

		if ( isset( $discount_parameters['longstay'] ) ) {
			$long_stay_discount = $this->calculate_long_stay_discount(
				$stay_checkin_date,
				$stay_checkout_date,
				$discount_parameters['longstay']['window'],
				$discount_parameters['longstay']['discount']
			);
		}

		// Find the highest discount and its type
		$highest_discount = max( $last_minute_discount, $early_booking_discount, $long_stay_discount );
		$discount_type    = '';

		if ( (int) $highest_discount === (int) $last_minute_discount && isset( $discount_parameters['lastminute'] ) ) {
			$discount_type = 'lastminute';
		} elseif ( (int) $highest_discount === (int) $early_booking_discount && isset( $discount_parameters['earlybooking'] ) ) {
			$discount_type = 'earlybooking';
		} elseif ( (int) $highest_discount === (int) $long_stay_discount && isset( $discount_parameters['longstay'] ) ) {
			$discount_type = 'longstay';
		}

		if ( '' !== $discount_type ) {
			$discount_label = $discount_parameters[ $discount_type ]['label'];
		}

		return array(
			'discountValue'  => $highest_discount,
			'discount_type'  => $discount_type,
			'discount_label' => $discount_label,
		);
	}

	/**
	 * Method calculate_room_price_total
	 *
	 * @param $room_id $room_id
	 *
	 * @return void
	 */
	public function calculate_room_price_total( $room_id ) {
		$total_roomrate = 0;
		$html           = '';

		$stay_checkin_date  = $this->find_checkin_date( $room_id );
		$stay_checkout_date = $this->find_checkout_date( $room_id );

		$highest_discount_info  = $this->calculate_highest_discount( $stay_checkin_date, $stay_checkout_date );
		$highest_discount_value = $highest_discount_info['discountValue'];
		$highest_discount_type  = $highest_discount_info['discount_type'];
		$highest_discount_label = $highest_discount_info['discount_label'];

		if ( $highest_discount_value > 0 ) {
			$this->booking_search_results[ $room_id ]['discountlabel'] = $highest_discount_label;
		}

		// Apply the rates
		foreach ( $this->rates_array[ $room_id ]['date'] as $staydate => $roomrate ) {
			$roomrate = self::apply_price_per_person( $roomrate );

			if ( $highest_discount_value > 0 ) {
				$roomrate = $roomrate - ( ( $roomrate / 100 ) * $highest_discount_value );
			}

			$this->booking_search_results[ $room_id ]['staydate'][ $staydate ] = $roomrate;
			$total_roomrate += $roomrate;
		}

		return $total_roomrate;
	}

	/**
	 * Finds the earliest check-in date for a given room.
	 *
	 * @param int $room_id The ID of the room.
	 * @return string      The earliest check-in date in 'Y-m-d' format, or an empty string if no dates are found.
	 */
	public function find_checkin_date( $room_id ) {
		if ( empty( $this->rates_array[ $room_id ]['date'] ) ) {
			return ''; // Return empty string if there are no dates
		}

		$earliest_date = null;
		foreach ( $this->rates_array[ $room_id ]['date'] as $staydate => $roomrate ) {
			if ( is_null( $earliest_date ) || strtotime( $staydate ) < strtotime( $earliest_date ) ) {
				$earliest_date = $staydate;
			}
		}

		return $earliest_date;
	}

	/**
	 * Finds the latest checkout date for a given room.
	 *
	 * @param int $room_id The ID of the room.
	 * @return string      The latest checkout date in 'Y-m-d' format, or an empty string if no dates are found.
	 */
	public function find_checkout_date( $room_id ) {
		if ( empty( $this->rates_array[ $room_id ]['date'] ) ) {
			return ''; // Return empty string if there are no dates
		}

		$latest_date = null;
		foreach ( $this->rates_array[ $room_id ]['date'] as $staydate => $roomrate ) {
			if ( is_null( $latest_date ) || strtotime( $staydate ) > strtotime( $latest_date ) ) {
				$latest_date = $staydate;
			}
		}

		return $latest_date;
	}

	/**
	 * Method apply_price_per_person
	 *
	 * @param $roomrate $roomrate
	 *
	 * @return void
	 */
	private function apply_price_per_person( $roomrate ) {

		$per_person_pricing = staylodgic_get_option( 'perpersonpricing' );
		if ( isset( $per_person_pricing ) && is_array( $per_person_pricing ) ) {
			foreach ( $per_person_pricing as $pricing ) {
				if ( $this->total_chargeable_guests === $pricing['people'] ) {
					if ( 'percentage' === $pricing['type'] && 'decrease' === $pricing['total'] ) {
						// Decrease the rate by the given percentage
						$roomrate -= ( $roomrate * $pricing['number'] / 100 );
					} elseif ( 'fixed' === $pricing['type'] && 'increase' === $pricing['total'] ) {
						// Increase the rate by the fixed amount
						$roomrate += $pricing['number'];
					} elseif ( 'percentage' === $pricing['type'] && 'increase' === $pricing['total'] ) {
						// Increase the rate by the given percentage
						$roomrate += ( $roomrate * $pricing['number'] / 100 );
					} elseif ( 'fixed' === $pricing['type'] && 'decrease' === $pricing['total'] ) {
						// Decrease the rate by the fixed amount
						$roomrate -= $pricing['number'];
					}
				}
			}
		}

		return $roomrate;
	}

	/**
	 * Method generate_bed_metabox_callback
	 *
	 * @return void
	 */
	public function generate_bed_metabox_callback() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		if ( isset( $_POST['the_room_id'] ) ) {
			$room_id = $_POST['the_room_id'];
		}
		if ( isset( $_POST['fieldID'] ) ) {
			$meta_field = $_POST['fieldID'];
		}
		if ( isset( $_POST['metaValue'] ) ) {
			$meta_value = $_POST['metaValue'];
		}

		if ( '' !== $room_id ) {
			$html = self::generate_bed_metabox( $room_id, $meta_field, $meta_value );
		} else {
			$html = '<span class="bedlayout-room-notfound-error">' . __( 'Room not found!', 'staylodgic' ) . '</span>';
		}

		wp_send_json( $html );
	}

	/**
	 * Method generate_bed_metabox
	 *
	 * @param $room_id $room_id
	 * @param $meta_field $meta_field
	 * @param $meta_value $meta_value
	 *
	 * @return void
	 */
	public function generate_bed_metabox( $room_id, $meta_field, $meta_value ) {

		$html = '';

		$room_data = get_post_custom( $room_id );

		$bed_input_count = 0;

		if ( isset( $room_data['staylodgic_alt_bedsetup'][0] ) ) {
			$bedsetup       = $room_data['staylodgic_alt_bedsetup'][0];
			$bedsetup_array = unserialize( $bedsetup );

			foreach ( $bedsetup_array as $stay_room_id => $stay_room_data ) {
				// Get the bed layout for this room

				$stay_bed_layout = '';
				$bed_count       = 0;
				foreach ( $stay_room_data['bedtype'] as $bed_field_id => $bed_name ) {
					$bed_qty = $stay_room_data['bednumber'][ $bed_field_id ];
					if ( $bed_count > 0 ) {
						$stay_bed_layout .= ' ';
					}
					for ( $i = 0; $i < $bed_qty; $i++ ) {
						if ( $i > 0 ) {
							$stay_bed_layout .= ' ';
						}
						$stay_bed_layout .= $bed_name;
					}
					++$bed_count;
				}

				++$bed_input_count;

				$html .= "<label for='" . esc_attr( $meta_field ) . '-' . esc_attr( $bed_input_count ) . "'>";
				$html .= "<input type='radio' id='" . esc_attr( $meta_field ) . '-' . esc_attr( $bed_input_count ) . "' name='" . esc_attr( $meta_field ) . "' value='" . esc_attr( $stay_bed_layout ) . "'";

				// Check the first radio input by default
				if ( $meta_value === $stay_bed_layout ) {
					$html .= ' checked';
				}

				$html .= '>';
				$html .= '<span class="checkbox-label checkbox-bed-label">';
				$html .= '<div class="guest-bed-wrap guest-bed-' . sanitize_title( $stay_bed_layout ) . '-wrap">';
				foreach ( $stay_room_data['bedtype'] as $bed_field_id => $bed_name ) {

					$bed_qty = $stay_room_data['bednumber'][ $bed_field_id ];
					for ( $i = 0; $i < $bed_qty; $i++ ) {
						$html .= staylodgic_get_BedLayout( $bed_name, $bed_field_id . '-' . $i );
					}
				}
				$html .= '</div>';
				$html .= '</span>';
				$html .= '</label>';
			}
		}

		return $html;
	}

	/**
	 * Method generate_bed_information
	 *
	 * @param $room_id $room_id
	 *
	 * @return void
	 */
	public function generate_bed_information( $room_id ) {

		$html = '';

		$room_data = get_post_custom( $room_id );

		if ( isset( $room_data['staylodgic_alt_bedsetup'][0] ) ) {
			$bedsetup       = $room_data['staylodgic_alt_bedsetup'][0];
			$bedsetup_array = unserialize( $bedsetup );

			$first_room_id = array_key_first( $bedsetup_array );

			foreach ( $bedsetup_array as $stay_room_id => $stay_room_data ) {
				// Get the bed layout for this room

				$stay_bed_layout = '';
				$bed_count       = 0;
				foreach ( $stay_room_data['bedtype'] as $bed_field_id => $bed_name ) {
					$bed_qty = $stay_room_data['bednumber'][ $bed_field_id ];
					if ( $bed_count > 0 ) {
						$stay_bed_layout .= ' ';
					}
					for ( $i = 0; $i < $bed_qty; $i++ ) {
						if ( $i > 0 ) {
							$stay_bed_layout .= ' ';
						}
						$stay_bed_layout .= $bed_name;
					}
					++$bed_count;
				}

				$this->booking_search_results[ $room_id ]['bedlayout'][ sanitize_title( $stay_bed_layout ) ] = true;

				$html .= "<label for='room-" . esc_attr( $room_id ) . '-bedlayout-' . esc_attr( $stay_bed_layout ) . "'>";
				$html .= "<input type='radio' id='room-" . esc_attr( $room_id ) . '-bedlayout-' . esc_attr( $stay_bed_layout ) . "' name='room[" . esc_attr( $room_id ) . "][bedlayout]' value='" . esc_attr( $stay_bed_layout ) . "'";

				// Check the first radio input by default
				if ( $stay_room_id === $first_room_id ) {
					$html .= ' checked';
				}

				$html .= '>';
				$html .= '<span class="checkbox-label checkbox-bed-label">';
				$html .= '<div class="guest-bed-wrap guest-bed-' . sanitize_title( $stay_bed_layout ) . '-wrap">';
				foreach ( $stay_room_data['bedtype'] as $bed_field_id => $bed_name ) {

					$bed_qty = $stay_room_data['bednumber'][ $bed_field_id ];
					for ( $i = 0; $i < $bed_qty; $i++ ) {
						$html .= staylodgic_get_BedLayout( $bed_name, $bed_field_id . '-' . $i );
					}
				}
				$html .= '</div>';
				$html .= '</span>';
				$html .= '</label>';
			}
		}

		return $html;
	}

	/**
	 * Method payment_helper_button
	 *
	 * @param $total $total
	 * @param $bookingnumber $bookingnumber
	 *
	 * @return void
	 */
	public function payment_helper_button( $total, $bookingnumber ) {
		$payment_button = '<div data-paytotal="' . esc_attr( $total ) . '" data-bookingnumber="' . esc_attr( $bookingnumber ) . '" id="woo-bookingpayment" class="book-button">' . __( 'Pay Booking', 'staylodgic' ) . '</div>';
		return $payment_button;
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
			'guest_consent'  => __( 'By clicking "Book this Room" you agree to our terms and conditions and privacy policy.', 'staylodgic' ),
		);

		return $data_fields;
	}

	/**
	 * Method register_guest_form
	 *
	 * @return void
	 */
	public function register_guest_form() {
		$country_options = staylodgic_country_list( 'select', '' );

		$html = '<div class="registration-column registration-column-two" id="booking-summary">';

		$bookingnumber = '';
		$room_id       = '';
		$roomtitle     = $booking_results[ $room_id ]['roomtitle'] = '';
		$bedlayout     = '';
		$mealplan      = '';
		$choice        = '';
		$perdayprice   = '';
		$total         = '';

		$html .= self::booking_summary(
			$bookingnumber,
			$room_id,
			$roomtitle,
			$this->stay_checkin_date,
			$this->stay_checkout_date,
			$this->staynights,
			$this->stay_adult_guests,
			$this->stay_children_guests,
			$bedlayout,
			$mealplan,
			$choice,
			$perdayprice,
			$total
		);
		$html .= '</div>';

		$bookingsuccess = self::booking_successful();

		$form_inputs = self::booking_data_fields();

		$form_html = '
		<div class="registration_form_outer registration_request">
			<div class="registration_form_wrap">
				<div class="registration_form">
					<div class="registration-column registration-column-one registration_form_inputs">
						<div class="booking-backto-activitychoice"><div class="booking-backto-roomchoice-inner"><i class="fa-solid fa-arrow-left"></i> ' . __( 'Back', 'staylodgic' ) . '</div></div>
						<h3>' . __( 'Registration', 'staylodgic' ) . '</h3>
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
							' . $country_options . '
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
									' . __( 'Consent is required for booking.', 'staylodgic' ) . '
								</div>
							</label>
						</div>
					</div>
					' . $html . '
				</div>
			</div>
		</div>';

		return $form_html . $bookingsuccess;
	}

	/**
	 * Method booking_successful
	 *
	 * @return void
	 */
	public function booking_successful() {
		$reservation_instance = new \Staylodgic\Reservations();
		$booking_page_link    = $reservation_instance->get_booking_details_page_link_for_guest();

		$success_html = '
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
						<div id="booking-details" class="book-button not-fullwidth booking-successful-button">
							<a href="' . esc_url( $booking_page_link ) . '">' . esc_html__( 'Booking Details', 'staylodgic' ) . '</a>
						</div>
					</div>
				</div>
			</div>
		</div>';

		return $success_html;
	}

	/**
	 * Method get_meal_plan_label
	 *
	 * @param $key $key
	 *
	 * @return void
	 */
	public function get_meal_plan_label( $key ) {
		$labels = array(
			'RO' => __( 'Room Only', 'staylodgic' ),
			'BB' => __( 'Bed and Breakfast', 'staylodgic' ),
			'HB' => __( 'Half Board', 'staylodgic' ),
			'FB' => __( 'Full Board', 'staylodgic' ),
			'AN' => __( 'All-Inclusive', 'staylodgic' ),
		);

		return isset( $labels[ $key ] ) ? $labels[ $key ] : $key;
	}

	/**
	 * Method get_included_meal_plan_keys_from_data
	 *
	 * @param $room_data $room_data
	 * @param $return_string $return_string
	 *
	 * @return void
	 */
	public function get_included_meal_plan_keys_from_data( $room_data, $return_string = false ) {
		// Check if meal_plan key exists in room_data and is an array
		if ( isset( $room_data['meal_plan'] ) && is_array( $room_data['meal_plan'] ) ) {
			// Filter the meal_plan array to get only the entries with 'included' as the value
			$included_meal_plans = array_filter(
				$room_data['meal_plan'],
				function ( $value ) {
					return 'included' === $value;
				}
			);

			// Get the keys of the filtered array
			$keys = array_keys( $included_meal_plans );

			// Map the keys to their corresponding labels using the current instance context
			$labels = array_map( array( $this, 'get_meal_plan_label' ), $keys );

			// Return as a comma-separated string or an array based on the $return_string parameter
			return $return_string ? implode( ', ', $labels ) : $labels;
		}

		// Return an empty array or an empty string if meal_plan is not set or not an array
		return $return_string ? '' : array();
	}

	/**
	 * Method generate_meal_plan_included
	 *
	 * @param $room_id $room_id
	 *
	 * @return void
	 */
	public function generate_meal_plan_included( $room_id ) {

		$meal_plans = staylodgic_get_option( 'mealplan' );

		if ( is_array( $meal_plans ) && count( $meal_plans ) > 0 ) {
			$included_meal_plans = array();
			$optional_meal_plans = array();

			foreach ( $meal_plans as $id => $plan ) {
				if ( 'included' === $plan['choice'] ) {
					$included_meal_plans[ $id ] = $plan;
				} elseif ( 'optional' === $plan['choice'] ) {
					$optional_meal_plans[ $id ] = $plan;
				}
			}

			$html = '';
			if ( is_array( $included_meal_plans ) && count( $included_meal_plans ) > 0 ) {
				$html .= '<div class="room-included-meals">';
				foreach ( $included_meal_plans as $id => $plan ) {
					$html .= '<i class="fa-solid fa-square-check"></i>';
					$html .= staylodgic_get_mealplan_labels( $plan['mealtype'] ) . __( ' included.', 'staylodgic' );
					$html .= '<label>';
					$html .= '<input hidden type="text" name="room[' . esc_attr( $room_id ) . '][meal_plan][included]" value="' . esc_attr( $plan['mealtype'] ) . '">';
					$html .= '</label>';
					$this->booking_search_results[ $room_id ]['meal_plan'][ $plan['mealtype'] ] = 'included';
				}
				$html .= '</div>';
			}
		}
		return $html;
	}

	/**
	 * Method generate_meal_plan_radio
	 *
	 * @param $room_id $room_id
	 *
	 * @return void
	 */
	public function generate_meal_plan_radio( $room_id ) {

		$meal_plans = staylodgic_get_option( 'mealplan' );

		if ( is_array( $meal_plans ) && count( $meal_plans ) > 0 ) {
			$included_meal_plans = array();
			$optional_meal_plans = array();

			foreach ( $meal_plans as $id => $plan ) {
				if ( 'included' === $plan['choice'] ) {
					$included_meal_plans[ $id ] = $plan;
				} elseif ( 'optional' === $plan['choice'] ) {
					$optional_meal_plans[ $id ] = $plan;
				}
			}

			$html = '';
			if ( is_array( $optional_meal_plans ) && count( $optional_meal_plans ) > 0 ) {
				$html .= '<div class="room-mealplans">';
				$html .= '<div class="mealplans-wrap">';
				$html .= '<div class="room-summary-heading room-mealplans-heading">' . __( 'Mealplans', 'staylodgic' ) . '</div>';
				$html .= '<div class="room-mealplan-input-wrap">';
				$html .= '<div class="room-mealplan-input">';
				$html .= '<label for="room-' . esc_attr( $room_id ) . '-meal_plan-optional-none">';
				$html .= '<input class="mealtype-input" type="radio" data-mealprice="none" id="room-' . esc_attr( $room_id ) . '-meal_plan-optional-none" name="room[' . esc_attr( $room_id ) . '][meal_plan][optional]" value="none" checked><span class="checkbox-label">' . __( 'Not selected', 'staylodgic' ) . '</span>';
				$html .= '</label>';
				$html .= '</div>';
				foreach ( $optional_meal_plans as $id => $plan ) {
					$html     .= '<div class="room-mealplan-input">';
					$mealprice = $plan['price'] * $this->staynights;
					$html     .= '<label for="room-' . esc_attr( $room_id ) . '-meal_plan-optional-' . esc_attr( $plan['mealtype'] ) . '">';
					$html     .= '<input class="mealtype-input" type="radio" data-mealprice="' . esc_attr( $mealprice ) . '" id="room-' . esc_attr( $room_id ) . '-meal_plan-optional-' . esc_attr( $plan['mealtype'] ) . '" name="room[' . esc_attr( $room_id ) . '][meal_plan][optional]" value="' . esc_attr( $plan['mealtype'] ) . '"><span class="room-mealplan-price checkbox-label">' . staylodgic_price( $mealprice ) . '<span class="mealplan-text">' . staylodgic_get_mealplan_labels( $plan['mealtype'] ) . '</span></span>';
					$html     .= '</label>';
					$this->booking_search_results[ $room_id ]['meal_plan'][ $plan['mealtype'] ] = $mealprice;

					$html .= '</div>';
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		return $html;
	}

	/**
	 * Method can_accomodate_to_rooms
	 *
	 * @param $rooms $rooms
	 * @param $adults $adults
	 * @param $children $children
	 *
	 * @return void
	 */
	public function can_accomodate_to_rooms( $rooms, $adults = false, $children = false ) {

		$min_adults         = 0;
		$max_adults_total   = false;
		$max_children_total = false;
		$max_guests_total   = 0;
		$max_children       = false;
		$max_adults         = false;
		$max_guests         = 0;
		$can_occomodate     = array();
		$will_accomodate    = true;
		$guests             = intval( $adults ) + intval( $children );
		$can_occomodate     = array();

		foreach ( $rooms as $room ) {
			$room_id  = $room['id'];
			$room_qty = $room['quantity'];

			$room_data = get_post_custom( $room_id );

			if ( isset( $room_data['staylodgic_max_guests'][0] ) ) {
				$max_guest_for_room = $room_data['staylodgic_max_guests'][0];
				$max_guests         = $max_guest_for_room * $room_qty;
			}
			if ( isset( $room_data['staylodgic_max_adult_limit_status'][0] ) ) {
				$adult_limit_status = $room_data['staylodgic_max_adult_limit_status'][0];
				if ( '1' === $adult_limit_status ) {
					$max_adults = $room_data['staylodgic_max_adults'][0];
					$max_adults = $max_adults * $room_qty;
				} else {
					$max_adults = $max_guest_for_room;
				}
			}
			if ( isset( $room_data['staylodgic_max_children_limit_status'][0] ) ) {
				$children_limit_status = $room_data['staylodgic_max_children_limit_status'][0];
				if ( '1' === $children_limit_status ) {
					$max_children = $room_data['staylodgic_max_children'][0];
					$max_children = $max_children * $room_qty;
				} else {
					$max_children = $max_guest_for_room - 1;
				}
			}

			if ( $max_adults ) {
				$max_adults_total = $max_adults_total + $max_adults;
			}
			if ( $max_children ) {
				$max_children_total = $max_children_total + $max_children;
			}
			$max_guests_total = $max_guests_total + $max_guests;
			$min_adults       = $min_adults + $room_qty;

			$can_occomodate[ $room_id ]['qty']          = $room_qty;
			$can_occomodate[ $room_id ]['max_adults']   = $max_adults;
			$can_occomodate[ $room_id ]['max_children'] = $max_children;
			$can_occomodate[ $room_id ]['max_guests']   = $max_guests;
		}

		$can_occomodate['allow']              = false;
		$can_occomodate['error']              = 'Too many guests for choice';
		$can_occomodate['max_adults_total']   = $max_adults_total;
		$can_occomodate['max_children_total'] = $max_children_total;
		$can_occomodate['max_guests_total']   = $max_guests_total;
		$can_occomodate['adults']             = $adults;
		$can_occomodate['children']           = $children;
		$can_occomodate['guests']             = $guests;
		$can_occomodate['min_adults']         = $min_adults;

		if ( $can_occomodate['max_guests_total'] >= $guests ) {
			$can_occomodate['allow'] = true;
			$can_occomodate['error'] = '';
		}
		if ( $can_occomodate['max_children_total'] ) {
			if ( $can_occomodate['max_children_total'] < $children ) {
				$can_occomodate['allow'] = false;
				$can_occomodate['error'] = 'Number of children exceed for choice of room';
			}
		}
		if ( $can_occomodate['min_adults'] > $adults ) {
			$can_occomodate['allow'] = false;
			$can_occomodate['error'] = 'Should have atleast 1 adult in each room';
		}

		return $can_occomodate;
	}

	/**
	 * Method can_accomodate_everyone_to_room
	 *
	 * @param $room_id $room_id
	 * @param $adults $adults
	 * @param $children $children
	 *
	 * @return void
	 */
	public function can_accomodate_everyone_to_room( $room_id, $adults = false, $children = false ) {

		$max_children    = false;
		$max_adults      = false;
		$max_guests      = false;
		$can_occomodate  = array();
		$will_accomodate = true;

		$total_guests = intval( $adults + $children );

		$room_data = get_post_custom( $room_id );
		if ( isset( $room_data['staylodgic_max_adult_limit_status'][0] ) ) {
			$adult_limit_status = $room_data['staylodgic_max_adult_limit_status'][0];
			if ( '1' === $adult_limit_status ) {
				$max_adults = $room_data['staylodgic_max_adults'][0];
			}
		}
		if ( isset( $room_data['staylodgic_max_children_limit_status'][0] ) ) {
			$children_limit_status = $room_data['staylodgic_max_children_limit_status'][0];
			if ( '1' === $children_limit_status ) {
				$max_children = $room_data['staylodgic_max_children'][0];
			}
		}
		if ( isset( $room_data['staylodgic_max_guests'][0] ) ) {
			$max_guests = $room_data['staylodgic_max_guests'][0];
		}

		if ( $max_children ) {
			$can_occomodate[ $room_id ]['children'] = true;
			if ( $children > $max_children ) {
				$can_occomodate[ $room_id ]['children'] = false;
				$will_accomodate                        = false;
			}
		}
		if ( $max_adults ) {
			$can_occomodate[ $room_id ]['adults'] = true;
			if ( $adults > $max_adults ) {
				$can_occomodate[ $room_id ]['adults'] = false;
				$will_accomodate                      = false;
			}
		}
		if ( $max_guests ) {
			$can_occomodate[ $room_id ]['guests'] = true;
			if ( $total_guests > $max_guests ) {
				$can_occomodate[ $room_id ]['guests'] = false;
				$will_accomodate                      = false;
			}
		}

		$can_occomodate[ $room_id ]['allow'] = $will_accomodate;

		return $can_occomodate;
	}

	/**
	 * Method build_reservation_array
	 *
	 * @param $booking_data $booking_data
	 *
	 * @return void
	 */
	public function build_reservation_array( $booking_data ) {
		$stay_reservation_array = array();

		if ( array_key_exists( 'bookingnumber', $booking_data ) ) {
			$stay_reservation_array['bookingnumber'] = $booking_data['bookingnumber'];
		}
		if ( array_key_exists( 'checkin', $booking_data ) ) {
			$stay_reservation_array['checkin'] = $booking_data['checkin'];
		}
		if ( array_key_exists( 'checkout', $booking_data ) ) {
			$stay_reservation_array['checkout'] = $booking_data['checkout'];
		}
		if ( array_key_exists( 'staynights', $booking_data ) ) {
			$stay_reservation_array['staynights'] = $booking_data['staynights'];
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
		if ( array_key_exists( 'totalguest', $booking_data ) ) {
			$stay_reservation_array['totalguest'] = $booking_data['totalguest'];
		}
		if ( array_key_exists( 'chargeableguests', $booking_data ) ) {
			$stay_reservation_array['chargeableguests'] = $booking_data['chargeableguests'];
		}
		if ( array_key_exists( 'room_id', $booking_data['choice'] ) ) {
			$stay_reservation_array['room_id']   = $booking_data['choice']['room_id'];
			$stay_reservation_array['room_data'] = $booking_data[ $booking_data['choice']['room_id'] ];
		}
		if ( array_key_exists( 'bedlayout', $booking_data['choice'] ) ) {
			$stay_reservation_array['bedlayout'] = $booking_data['choice']['bedlayout'];
		}
		if ( array_key_exists( 'mealplan', $booking_data['choice'] ) ) {
			$stay_reservation_array['mealplan'] = $booking_data['choice']['mealplan'];
		}
		if ( array_key_exists( 'mealplan_price', $booking_data['choice'] ) ) {
			$stay_reservation_array['mealplan_price'] = $booking_data['choice']['mealplan_price'];
		}
		if ( array_key_exists( 'mealplan_price', $booking_data['choice'] ) ) {
			$stay_reservation_array['mealplan_price'] = $booking_data['choice']['mealplan_price'];
		}
		if ( array_key_exists( 'mealplan_price', $booking_data['choice'] ) ) {
			$stay_reservation_array['mealplan_price'] = $booking_data['choice']['mealplan_price'];
		}

		$currency = staylodgic_get_option( 'currency' );
		if ( isset( $currency ) ) {
			$stay_reservation_array['currency'] = $currency;
		}

		$tax_instance = new \Staylodgic\Tax( 'room' );

		$subtotalprice                      = intval( $stay_reservation_array['room_data']['totalroomrate'] ) + intval( $stay_reservation_array['mealplan_price'] );
		$stay_reservation_array['tax']      = $tax_instance->apply_tax( $subtotalprice, $stay_reservation_array['staynights'], $stay_reservation_array['totalguest'], $output = 'data' );
		$stay_reservation_array['tax_html'] = $tax_instance->apply_tax( $subtotalprice, $stay_reservation_array['staynights'], $stay_reservation_array['totalguest'], $output = 'html' );

		$ratepernight                           = intval( $subtotalprice ) / intval( $stay_reservation_array['staynights'] );
		$ratepernight_rounded                   = round( $ratepernight, 2 );
		$stay_reservation_array['ratepernight'] = $ratepernight_rounded;
		$stay_reservation_array['subtotal']     = round( $subtotalprice, 2 );
		$stay_reservation_array['total']        = $stay_reservation_array['tax']['total'];

		return $stay_reservation_array;
	}

	/**
	 * Method book_rooms Ajax function to book rooms
	 *
	 * @return void
	 */
	public function book_rooms() {

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

		$rooms                  = array();
		$rooms['0']['id']       = $booking_data['choice']['room_id'];
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

		// Check if number of people can be occupied by room
		$can_accomodate = self::can_accomodate_to_rooms( $rooms, $adults, $children );

		if ( false === $can_accomodate['allow'] ) {
			wp_send_json_error( $can_accomodate['error'] );
		}
		// Create customer post
		$customer_post_data = array(
			'post_type'   => 'slgc_customers',
			'post_title'  => $full_name,
			'post_status' => 'publish',
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

		$checkin  = $stay_reservation_data['checkin'];
		$checkout = $stay_reservation_data['checkout'];
		$room_id  = $stay_reservation_data['room_id'];

		$children_array           = array();
		$children_array['number'] = $stay_reservation_data['children'];

		foreach ( $stay_reservation_data['children_age'] as $key => $value ) {
			$children_array['age'][] = $value;
		}

		$tax_status = 'excluded';
		$tax_html   = false;
		if ( staylodgic_has_tax() ) {
			$tax_status   = 'enabled';
			$tax_instance = new \Staylodgic\Tax( 'room' );
			$tax_html     = $tax_instance->tax_summary( $stay_reservation_data['tax_html']['details'] );
		}

		$new_bookingstatus = staylodgic_get_option( 'new_bookingstatus' );
		if ( 'pending' !== $new_bookingstatus && 'confirmed' !== $new_bookingstatus ) {
			$new_bookingstatus = 'pending';
		}
		$new_bookingsubstatus = staylodgic_get_option( 'new_bookingsubstatus' );
		if ( '' === $new_bookingstatus ) {
			$new_bookingsubstatus = 'onhold';
		}

		$reservation_booking_uid = \Staylodgic\Common::generate_uuid();

		$signature = md5( 'staylodgic_booking_system' );

		$sync_status            = 'complete';
		$availability_processor = new Availability_Batch_Processor();
		if ( $availability_processor->is_syncing_in_progress() ) {
			$sync_status = 'incomplete';
		}

		$booking_channel = 'Staylodgic';

		// Here you can also add other post data like post_title, post_content etc.
		$post_data = array(
			'post_type'   => 'slgc_reservations',
			'post_title'  => $booking_number,
			'post_status' => 'publish',
			'meta_input'  => array(
				'staylodgic_room_id'                     => $room_id,
				'staylodgic_reservation_status'          => $new_bookingstatus,
				'staylodgic_reservation_substatus'       => $new_bookingsubstatus,
				'staylodgic_checkin_date'                => $checkin,
				'staylodgic_checkout_date'               => $checkout,
				'staylodgic_tax'                         => $tax_status,
				'staylodgic_tax_html_data'               => $tax_html,
				'staylodgic_tax_data'                    => $stay_reservation_data['tax'],
				'staylodgic_reservation_room_bedlayout'  => $stay_reservation_data['bedlayout'],
				'staylodgic_reservation_room_mealplan'   => $stay_reservation_data['mealplan'],
				'staylodgic_reservation_room_adults'     => $stay_reservation_data['adults'],
				'staylodgic_reservation_room_children'   => $children_array,
				'staylodgic_reservation_rate_per_night'  => $stay_reservation_data['ratepernight'],
				'staylodgic_reservation_subtotal_room_cost' => $stay_reservation_data['subtotal'],
				'staylodgic_reservation_total_room_cost' => $stay_reservation_data['total'],
				'staylodgic_booking_number'              => $booking_number,
				'staylodgic_booking_uid'                 => $reservation_booking_uid,
				'staylodgic_customer_id'                 => $customer_post_id,
				'staylodgic_sync_status'                 => $sync_status,
				'staylodgic_ics_signature'               => $signature,
				'staylodgic_booking_data'                => $stay_reservation_data,
				'staylodgic_booking_channel'             => $booking_channel,
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

			$included_mealplans = $this->get_included_meal_plan_keys_from_data( $stay_reservation_data['room_data'], true );

			$booking_details = array(
				'guestName'            => $full_name,
				'stay_booking_number'  => $booking_number,
				'roomTitle'            => $stay_room_name,
				'included_mealplan'    => $included_mealplans,
				'mealplan'             => $this->get_meal_plan_label( $stay_reservation_data['mealplan'] ),
				'stay_checkin_date'    => $checkin,
				'stay_checkout_date'   => $checkout,
				'stay_adult_guests'    => $stay_reservation_data['adults'],
				'stay_children_guests' => $stay_reservation_data['children'],
				'subtotal'             => staylodgic_price( $stay_reservation_data['subtotal'] ),
				'tax'                  => $email_tax_html,
				'stay_total_cost'      => $stay_reservation_data['total'],
			);

			$email = new Email_Dispatcher( $email_address, 'Room Booking Confirmation for: ' . $booking_number );
			$email->set_html_content()->set_booking_confirmation_template( $booking_details );

			if ( $email->send() ) {
				// Sent successfully code here
			} else {
				// Failed to send codings here
			}
		} else {
			// Handle error
		}

		// Send a success response at the end of your function, if all operations are successful
		wp_send_json_success( 'Booking successfully registered.' );
		wp_die();
	}
}

$instance = new \Staylodgic\Booking();
