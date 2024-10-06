<?php

namespace Staylodgic;

class Availablity_Calendar extends Availablity_Calendar_Base {


	public function __construct( $stay_start_date = null, $stay_end_date = null, $cached_data = null, $calendar_data = null, $reservation_tabs = null, $using_cache = false, $avail_confirmed_only = false ) {
		parent::__construct( $stay_start_date, $stay_end_date, $calendar_data, $reservation_tabs, $avail_confirmed_only );

		// WordPress AJAX action hook
		add_action( 'wp_ajax_get_selected_range_availability_calendar', array( $this, 'get_selected_range_availability_calendar' ) );
		add_action( 'wp_ajax_nopriv_get_selected_range_availability_calendar', array( $this, 'get_selected_range_availability_calendar' ) );

		add_action( 'admin_menu', array( $this, 'availablity_calendar_display' ) );

		// Register the AJAX action
		add_action( 'wp_ajax_fetch_occupancy_percentage_for_calendar_range', array( $this, 'fetch_occupancy_percentage_for_calendar_range' ) );
		add_action( 'wp_ajax_nopriv_fetch_occupancy_percentage_for_calendar_range', array( $this, 'fetch_occupancy_percentage_for_calendar_range' ) );

		// Add the AJAX action to both the front-end and the admin
		add_action( 'wp_ajax_update_avail_display_confirmed_status', array( $this, 'update_avail_display_confirmed_status' ) );
		add_action( 'wp_ajax_nopriv_update_avail_display_confirmed_status', array( $this, 'update_avail_display_confirmed_status' ) );
	}

	/**
	 * Method generate_room_warnings
	 *
	 * @param $the_room_id $the_room_id
	 *
	 * @return void
	 */
	public function generate_room_warnings( $the_room_id ) {
		$room_output = '';

		$total_rooms = get_post_meta( $the_room_id, 'staylodgic_max_rooms_of_type', true );
		if ( '' === $total_rooms ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Max room undefined', 'staylodgic' ) . '</p></div>';
		}
		if ( '0' === $total_rooms ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Max room is zero', 'staylodgic' ) . '</p></div>';
		}

		$base_rate = get_post_meta( $the_room_id, 'staylodgic_base_rate', true );
		if ( '' === $base_rate ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Base rate undefined', 'staylodgic' ) . '</p></div>';
		}
		if ( '0' === $base_rate ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Base rate is zero', 'staylodgic' ) . '</p></div>';
		}

		$max_guests = get_post_meta( $the_room_id, 'staylodgic_max_guests', true );
		if ( '' === $max_guests ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Max guest number undefined', 'staylodgic' ) . '</p></div>';
		}
		if ( '0' === $max_guests ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Max guest number is zero', 'staylodgic' ) . '</p></div>';
		}

		$bedsetup = get_post_meta( $the_room_id, 'staylodgic_alt_bedsetup', true );
		if ( ! is_array( $bedsetup ) || ! isset( $bedsetup ) ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'Beds undefined', 'staylodgic' ) . '</p></div>';
		}

		$image_id = get_post_thumbnail_id( $the_room_id );
		if ( ! $image_id ) {
			$room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __( 'No featured image', 'staylodgic' ) . '</p></div>';
		}

		return $room_output;
	}

	/**
	 * Method update_avail_display_confirmed_status
	 *
	 * @return void
	 */
	public function update_avail_display_confirmed_status() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_availabilitycalendar_nonce'] ) || ! check_admin_referer( 'staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}
		// Check if the confirmed_only value is set
		if ( isset( $_POST['confirmed_only'] ) ) {

			if ( 0 == $_POST['confirmed_only'] ) {
				// Update the option based on the switch value
				update_option( 'staylodgic_availsettings_confirmed_only', 0 );
			} else {
				// Update the option based on the switch value
				update_option( 'staylodgic_availsettings_confirmed_only', 1 );
			}

			// Return a success response
			wp_send_json_success();
		} else {
			// Return an error response
			wp_send_json_error( 'The confirmed_only value is not set.' );
		}
	}

	/**
	 * Method fetch_occupancy_percentage_for_calendar_range
	 *
	 * @param $stay_start_date $stay_start_date
	 * @param $stay_end_date $stay_end_date
	 * @param $only_full_occupancy $only_full_occupancy
	 *
	 * @return void
	 */
	public function fetch_occupancy_percentage_for_calendar_range( $stay_start_date = false, $stay_end_date = false, $only_full_occupancy = false ) {
		// Perform necessary security checks or validation here

		// Check if AJAX POST values are set
		$is_ajax_request = isset( $_POST['start'] ) && isset( $_POST['end'] );

		// Calculate current date
		$stay_current_date = current_time( 'Y-m-d' );

		// Calculate end date as 90 days from the current date
		$stay_end_date = gmdate( 'Y-m-d', strtotime( $stay_current_date . ' +90 days' ) );

		if ( ! $stay_start_date ) {
			// Use the current date as the start date
			$stay_start_date = $stay_current_date;
		}

		// Retrieve start and end dates from the AJAX request if not provided
		if ( $is_ajax_request ) {
			$stay_start_date = sanitize_text_field( $_POST['start'] );
			$stay_end_date   = sanitize_text_field( $_POST['end'] );
		}

		if ( isset( $stay_start_date ) && isset( $stay_end_date ) ) {

			$dates = \Staylodgic\Common::get_dates_between( $stay_start_date, $stay_end_date );

			$occupancy_data = array();

			foreach ( $dates as $date ) :
				$occupancydate        = $date;
				$occupancy_percentage = $this->calculate_occupancy_for_date( $occupancydate );

				// Check if only full occupancy dates should be included
				if ( $only_full_occupancy && $occupancy_percentage < 100 ) {
					continue; // Skip this date if not full occupancy
				}

				$occupancy_data[ $occupancydate ] = $occupancy_percentage;
			endforeach;

			// Send occupancy data as JSON response if it's an AJAX request
			if ( $is_ajax_request ) {
				wp_send_json( $occupancy_data );
			} else {
				return $occupancy_data; // Return the array if it's not an AJAX request
			}
		} elseif ( $is_ajax_request ) {
				wp_send_json_error( 'Invalid date range!' );
		} else {
			return array(); // Return an empty array if it's not an AJAX request and the date range is invalid

		}
	}

	/**
	 * Method availablity_calendar_display Add the Availability menu item to the admin menu
	 *
	 * @return void
	 */
	public function availablity_calendar_display() {
		// Add the parent menu item
		add_submenu_page(
			'slgc-dashboard',
			__( 'Availability Calendar', 'staylodgic' ),
			__( 'Availability Calendar', 'staylodgic' ),
			'edit_posts',
			'slgc-availability',
			array( $this, 'room_reservation_plugin_display_availability_calendar' ), // Callback for the parent page (can be empty if not needed)
		);
	}

	/**
	 * Method room_reservation_plugin_display_availability_calendar_yearly Callback function to display the Availability page
	 *
	 * @return void
	 */
	public function room_reservation_plugin_display_availability_calendar_yearly() {
		// Output the HTML for the Availability page
		?>
		<div class="wrap">
			<h1><?php _e( 'Availability Calendar', 'staylodgic' ); ?></h1>
			<?php
			if ( ! \Staylodgic\Rooms::has_rooms() ) {
				echo '<h1>' . __( 'No Rooms Found', 'staylodgic' ) . '</h1>';
				return;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Method get_display_confirmed_status
	 *
	 * @return void
	 */
	public function get_display_confirmed_status() {
		$this->avail_confirmed_only = get_option( 'staylodgic_availsettings_confirmed_only' );

		// Check if the option is not found and set it to '1'
		if ( $this->avail_confirmed_only === false ) {
			update_option( 'staylodgic_availsettings_confirmed_only', true );
			$this->avail_confirmed_only = true;
		}

		$confirmed_status = '';

		return $this->avail_confirmed_only;
	}

	/**
	 * Method room_reservation_plugin_display_availability_calendar Callback function to display the Availability page
	 *
	 * @return void
	 */
	public function room_reservation_plugin_display_availability_calendar() {
		// Check if user has sufficient permissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Output the HTML for the Availability page
		?>
		<div class="wrap">
			<?php
			if ( ! \Staylodgic\Rooms::has_rooms() ) {
				echo '<h1>' . __( 'No Rooms Found', 'staylodgic' ) . '</h1>';
				return;
			} else {

				echo '<h1>' . __( 'Availability Calendar', 'staylodgic' ) . '</h1>';
			}
			echo \Staylodgic\Modals::rateQtyToasts();

			$confirmed_status = '';
			if ( $this->get_display_confirmed_status() ) {
				$confirmed_status = 'checked';
			}

			// Add any custom HTML content here
			?>
		</div>
		<div class="calendar-controls-wrap">
			<div class="calendar-controls">
				<ul class="calendar-controls-list">
					<li class="nav-item">
						<div class="preloader-element-outer">
							<div class="preloader-element"></div>
						</div>
					</li>
					<li class="nav-item">
						<div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Previous Month" class="calendar-nav-buttons" id="prevmonth"><i class="fa-solid fa-arrow-left"></i></div>
					</li>
					<li class="nav-item">
						<input type="month" class="availabilitycalendar" id="availabilitycalendar" name="availabilitycalendar" value="" />
						<?php
						$availabilitycalendar = wp_create_nonce( 'staylodgic-availabilitycalendar-nonce' );
						echo '<input type="hidden" name="staylodgic_availabilitycalendar_nonce" value="' . esc_attr( $availabilitycalendar ) . '" />';
						?>
					</li>
					<li class="nav-item">
						<div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Next Month" class="calendar-nav-buttons" id="nextmonth"><i class="fa-solid fa-arrow-right"></i></div>
					</li>
					<li class="nav-item nav-item-seperator">
						<div class="calendar-nav-buttons calendar-text-button" id="quantity-modal-link" data-bs-toggle="modal" data-bs-target="#quantity-modal"><i class="fas fa-hashtag"></i><?php _e( 'Quanity', 'staylodgic' ); ?></div>
					</li>
					<li class="nav-item">
						<div class="calendar-nav-buttons calendar-text-button" id="rates-modal-link" data-bs-toggle="modal" data-bs-target="#rates-modal"><i class="fas fa-dollar-sign"></i><?php _e( 'Rate', 'staylodgic' ); ?></div>
					</li>
					<li class="nav-item nav-item-seperator">
						<div class="form-check form-switch">
							<input class="form-check-input" type="checkbox" role="switch" id="calendar-booking-status" <?php echo esc_attr( $confirmed_status ); ?>>
							<label class="form-check-label" for="calendar-booking-status"><?php _e( 'Display Confirmed', 'staylodgic' ); ?></label>
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div id="container">
			<div id="calendar">
				<?php
				$calendar = $this->get_availability_calendar();

				echo $calendar;
				?>
			</div>
		</div>
		<?php
		\Staylodgic\Modals::quanity_modal();
		\Staylodgic\Modals::rates_modal();
	}

	/**
	 * Method get_selected_range_availability_calendar
	 *
	 * @return void
	 */
	public function get_selected_range_availability_calendar() {

		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_availabilitycalendar_nonce'] ) || ! check_admin_referer( 'staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		// Check if the request has necessary data
		if ( ! isset( $_POST['start_date'], $_POST['end_date'] ) ) {
			wp_die( 'Missing parameters' );
		}

		// Sanitize inputs
		$start_date = sanitize_text_field( $_POST['start_date'] );
		$end_date   = sanitize_text_field( $_POST['end_date'] );

		// Validate inputs
		if ( ! strtotime( $start_date ) || ! strtotime( $end_date ) ) {
			wp_die( 'Invalid dates' );
		}

		$this->get_display_confirmed_status();

		ob_start();
		echo $this->get_availability_calendar( $start_date, $end_date );
		$output = ob_get_clean();
		echo $output;

		// end execution
		wp_die();
	}

	/**
	 * Method get_availability_calendar
	 *
	 * @param $stay_start_date $stay_start_date
	 * @param $stay_end_date $stay_end_date
	 *
	 * @return void
	 */
	public function get_availability_calendar( $stay_start_date = false, $stay_end_date = false ) {

		if ( ! $stay_start_date ) {
			$stay_start_date = $this->stay_start_date;
			$stay_end_date   = $this->stay_end_date;
		} else {
			$stay_start_date = new \DateTime( $stay_start_date );
			$stay_end_date   = new \DateTime( $stay_end_date );
		}

		$dates = $this->get_dates( $stay_start_date, $stay_end_date );
		$today = $this->today;

		if ( $stay_start_date instanceof \DateTime ) {
			$start_date_string = $stay_start_date->format( 'Y-m-d' );
		} else {
			$start_date_string = $stay_start_date;
		}

		if ( $stay_end_date instanceof \DateTime ) {
			$end_date_string = $stay_end_date->format( 'Y-m-d' );
		} else {
			$end_date_string = $stay_end_date;
		}

		$table_start = '<table id="calendarTable" data-calstart="' . esc_attr( $start_date_string ) . '" data-calend="' . esc_attr( $end_date_string ) . '">';

		$this->roomlist = \Staylodgic\Rooms::getRoomList();

		$room_output     = '';
		$all_room_output = '';

		foreach ( $this->roomlist as $the_room_id => $stay_room_name ) :

			$cache_instance = new \Staylodgic\Cache( $the_room_id, $start_date_string, $end_date_string );

			$transient_key   = $cache_instance->generate_room_cache_key();
			$cached_calendar = $cache_instance->get_cache( $transient_key );

			$room_reservations_instance = new \Staylodgic\Reservations( $stay_date_string = false, $the_room_id );

			$room_reservations_instance->calculate_and_update_remaining_room_counts_for_all_dates();

			$use_cache           = true;
			$this->using_cache   = false;
			$this->cached_data   = array();
			$this->calendar_data = array();

			if ( isset( $cached_calendar['qty_rates'] ) ) {
				$cached_qty_rates = $cached_calendar['qty_rates'];

				foreach ( $dates as $date ) {

					$stay_date_string = $date->format( 'Y-m-d' );

					$reservation_instance = new \Staylodgic\Reservations( $stay_date_string, $the_room_id );
					$remaining_rooms      = $reservation_instance->remaining_rooms_for_day();
					$room_rate            = \Staylodgic\Rates::get_room_rate_by_date( $the_room_id, $stay_date_string );

					if ( 0 == $remaining_rooms ) {
						$room_was_opened = $reservation_instance->was_room_ever_opened();
						if ( false === $room_was_opened ) {
							$remaining_rooms = '/';
						}
					}

					if ( isset( $cached_qty_rates[ $stay_date_string ] ) ) {

						if ( $remaining_rooms !== $cached_qty_rates[ $stay_date_string ]['qty'] ) {

							$use_cache = false;
							$cache_instance->delete_cache( $transient_key );
							break; // Exit the loop
						}
						if ( $room_rate !== $cached_qty_rates[ $stay_date_string ]['rate'] ) {

							$use_cache = false;
							$cache_instance->delete_cache( $transient_key );
							break; // Exit the loop
						}
					}
				}
			}

			if ( $cache_instance->has_cache( $transient_key ) && true == $cache_instance->is_cache_allowed() && true == $use_cache ) {

				if ( isset( $cached_calendar ) ) {

					$this->cached_data = $cached_calendar;

					$this->using_cache = true;
				}
			}

			$this->reservation_tabs = array();
			$cache_qty_rate         = array();
			$cache_output           = array();

			$room_output  = '<tr class="calendarRow calendar-room-row" data-id="' . esc_attr( $the_room_id ) . '">';
			$room_output .= '<td class="calendarCell rowHeader">';
			$room_output .= esc_html( $stay_room_name );

			$room_output .= $this->generate_room_warnings( $the_room_id );

			$room_output .= '</td>';

			if ( ! $this->using_cache ) {
				$reservation_instance = new \Staylodgic\Reservations( false, $the_room_id );
				$reservations         = $reservation_instance->get_reservations_for_room( $start_date_string, $end_date_string, false, false, $the_room_id );
			}

			foreach ( $dates as $date ) :
				$stay_date_string = $date->format( 'Y-m-d' );
				$reservation_data = array();

				$create_reservation_tag = true;

				if ( ! $this->using_cache ) {
					$reservation_instance = new \Staylodgic\Reservations( $stay_date_string, $the_room_id );
					$reservation_data     = $reservation_instance->build_reservations_data_for_room_for_day( $reservations, false, false, false, false );

					$remaining_rooms = $reservation_instance->remaining_rooms_for_day();

					if ( 0 === $remaining_rooms ) {
						$room_was_opened = $reservation_instance->was_room_ever_opened();
						if ( false === $room_was_opened ) {
							$remaining_rooms = '/';
						}
						$create_reservation_tag = false;
					}

					$room_rate              = \Staylodgic\Rates::get_room_rate_by_date( $the_room_id, $stay_date_string );
					$occupancy_status_class = '';
					if ( $reservation_instance->is_room_for_the_day_fullybooked() ) {
						$occupancy_status_class = 'fully-booked';
					} else {
						$occupancy_status_class = 'room-available';
					}

					$this->calendar_data['cellData'][ $stay_date_string ]['reservation_data']       = $reservation_data;
					$this->calendar_data['cellData'][ $stay_date_string ]['remaining_rooms']        = $remaining_rooms;
					$this->calendar_data['cellData'][ $stay_date_string ]['room_rate']              = $room_rate;
					$this->calendar_data['cellData'][ $stay_date_string ]['occupancy_status_class'] = $occupancy_status_class;
					$this->calendar_data['cellData'][ $stay_date_string ]['create_reservation_tag'] = $create_reservation_tag;
				} else {
					$reservation_data       = $this->cached_data['cellData'][ $stay_date_string ]['reservation_data'];
					$remaining_rooms        = $this->cached_data['cellData'][ $stay_date_string ]['remaining_rooms'];
					$room_rate              = $this->cached_data['cellData'][ $stay_date_string ]['room_rate'];
					$occupancy_status_class = $this->cached_data['cellData'][ $stay_date_string ]['occupancy_status_class'];
					$create_reservation_tag = $this->cached_data['cellData'][ $stay_date_string ]['create_reservation_tag'];
				}

				$room_output .= '<td class="calendarCell ' . esc_attr( $this->start_of_month_css_tag( $stay_date_string ) ) . ' ' . esc_attr( $occupancy_status_class ) . '">';

				$room_output .= '<div class="calendar-info-wrap">';
				$room_output .= '<div class="calendar-info">';
				$room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Quantity" href="#" class="quantity-link" data-remaining="' . esc_attr( $remaining_rooms ) . '" data-date="' . esc_attr( $stay_date_string ) . '" data-room="' . esc_attr( $the_room_id ) . '">' . esc_html( $remaining_rooms ) . '</a>';

				if ( ! empty( $room_rate ) && isset( $room_rate ) && $room_rate > 0 ) {
					$room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rate" class="roomrate-link" href="#" data-rate="' . esc_attr( $room_rate ) . '" data-date="' . esc_attr( $stay_date_string ) . '" data-room="' . esc_attr( $the_room_id ) . '">' . esc_html( $room_rate ) . '</a>';
				}

				if ( ! $this->using_cache ) {
					$cache_qty_rate[ $stay_date_string ]['qty']  = $remaining_rooms;
					$cache_qty_rate[ $stay_date_string ]['rate'] = $room_rate;
				}

				$room_output .= '</div>';

				$room_output .= '</div>';

				if ( $create_reservation_tag ) {
					$create_end_date = new \DateTime( $stay_date_string );
					$create_end_date->modify( '+1 day' );
					$create_one_day_ahead = $create_end_date->format( 'Y-m-d' );
					$new_post_link        = admin_url( 'post-new.php?post_type=slgc_reservations&createfromdate=' . esc_attr( $stay_date_string ) . '&createtodate=' . esc_attr( $create_one_day_ahead ) . '&the_room_id=' . esc_attr( $the_room_id ) );
					$room_output         .= '<div class="cal-create-reservation"><a data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="New Booking" href="' . esc_url( $new_post_link ) . '">+</a></div>';
				}

				$room_output .= '<div class="reservation-tab-wrap" data-day="' . esc_attr( $stay_date_string ) . '">';
				if ( $reservation_data ) {
					$reservation_module = array();

					$reservation_module = $this->reserved_tab( $reservation_data, $stay_date_string, $start_date_string );
					$room_output       .= $reservation_module['tab'];

				}
				$room_output .= '</div>';
				$room_output .= '</td>';
			endforeach;
			$room_output .= '</tr>';

			$this->calendar_data['qty_rates'] = $cache_qty_rate;

			$all_room_output .= $room_output;

			if ( ! $this->using_cache ) {

				$cache_instance->set_cache( $transient_key, $this->calendar_data );
			}

		endforeach;

		$stats_row     = '<tr class="calendarRow">';
		$stats_row    .= self::display_occupancy_table_data_block( $stay_start_date, $stay_end_date );
		$stats_row    .= self::display_occupancy_range_table_data_block( $dates );
		$stats_row    .= '</tr>';
		$stats_row    .= '<tr class="calendarRow">';
		$stats_row    .= '<td class="calendarCell rowHeader"></td>';
		$stay_num_days = $this->set_num_days( $start_date_string, $end_date_string );
		$stats_row    .= self::display_date_table_data_block( $dates, $stay_num_days );
		$stats_row    .= '</tr>';

		$table_end = '</table>';

		$output = $table_start . $stats_row . $all_room_output . $table_end;
		return $output;
	}

	/**
	 * Method display_occupancy_table_data_block
	 *
	 * @param $stay_start_date $stay_start_date
	 * @param $stay_end_date $stay_end_date
	 *
	 * @return void
	 */
	private function display_occupancy_table_data_block( $stay_start_date = false, $stay_end_date = false ) {
		if ( ! $stay_start_date ) {
			$stay_start_date = $this->stay_start_date;
			$stay_end_date   = $this->stay_end_date;
		}

		if ( $stay_start_date instanceof \DateTime ) {
			$start_date_string = $stay_start_date->format( 'Y-m-d' );
		} else {
			$start_date_string = $stay_start_date;
		}

		if ( $stay_end_date instanceof \DateTime ) {
			$stay_end_date->modify( '-5 days' );
			$end_date_string = $stay_end_date->format( 'Y-m-d' );
		} else {
			$stay_end_date = new \DateTime( $stay_end_date );
			$stay_end_date->modify( '-5 days' );
			$end_date_string = $stay_end_date->format( 'Y-m-d' );
		}

		$occupancy_percent = esc_html( $this->calculate_occupancy_total_for_range( $start_date_string, $end_date_string ) );

		$output  = '<td class="calendarCell rowHeader">';
		$output .= '<div data-occupancypercent="' . esc_attr( $occupancy_percent ) . '" class="occupancyStats-wrap occupancy-percentage">';
		$output .= '<div class="occupancyStats-inner">';
		$output .= '<div class="occupancy-total">';
		$output .= '<span class="occupancy-total-stats">';
		$output .= '<span class="occupancy-percent-symbol">';
		$output .= esc_html( $occupancy_percent );
		$output .= '%</span>';
		$output .= __( 'Occupancy', 'staylodgic' );
		$output .= '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</td>';
		return $output;
	}

	/**
	 * Method today_css_tag
	 *
	 * @param $occupancydate $occupancydate
	 *
	 * @return void
	 */
	public function today_css_tag( $occupancydate ) {
		$today_status_class = '';
		if ( $occupancydate == $this->today ) {
			$today_status_class = 'is-today';
		}
		return $today_status_class;
	}

	/**
	 * Method start_of_month_css_tag
	 *
	 * @param $occupancydate $occupancydate
	 *
	 * @return void
	 */
	public function start_of_month_css_tag( $occupancydate ) {
		$startOfMonth_class = '';

		$yearMonth = substr( $occupancydate, 0, 7 ); // This gives 'YYYY-MM'

		// Create the first day of the month string for the given date
		$firstDayOfOccupancyMonth = $yearMonth . '-01';

		// Compare the provided date with the first day of its month
		if ( $occupancydate == $firstDayOfOccupancyMonth ) {
			$startOfMonth_class = 'start-of-month';
		}

		return $startOfMonth_class;
	}

	/**
	 * Method display_occupancy_range_table_data_block
	 *
	 * @param $dates $dates
	 *
	 * @return void
	 */
	private function display_occupancy_range_table_data_block( $dates ) {
		$number_of_columns = 0;
		$output            = '';

		foreach ( $dates as $date ) :
			++$number_of_columns;
			$occupancydate = $date->format( 'Y-m-d' );

			$remaining_rooms = $this->calculate_remaining_rooms_for_date( $occupancydate );

			$output .= '<td data-roomsremaining="' . esc_attr( $remaining_rooms ) . '" class="calendarCell monthHeader occupancy-stats occupancy-percent-' . esc_attr( $remaining_rooms ) . ' ' . esc_attr( $this->today_css_tag( $occupancydate ) ) . ' ' . esc_attr( $this->start_of_month_css_tag( $occupancydate ) ) . '">';
			$output .= '<div class="occupancyStats-wrap">';
			$output .= '<div class="occupancyStats-inner">';
			$output .= '<div class="occupancy-adr">';
			$output .= __( 'Rooms<br/>Open', 'staylodgic' );
			$output .= '</div>';
			$output .= '<div class="occupancy-percentage">';
			$output .= esc_html( $remaining_rooms );
			$output .= '<span></span>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</td>';
		endforeach;

		return $output;
	}

	/**
	 * Method display_date_table_data_block
	 *
	 * @param $dates $dates
	 * @param $stay_num_days $stay_num_days
	 *
	 * @return void
	 */
	private function display_date_table_data_block( $dates = false, $stay_num_days = false ) {

		$today             = $this->today;
		$number_of_columns = 0;
		if ( ! $stay_num_days ) {
			$markNumDays = $this->stay_num_days + 1;
		} else {
			$markNumDays = $stay_num_days + 1;
		}

		$output = '';

		foreach ( $dates as $date ) :
			++$number_of_columns;
			$month        = $date->format( 'M' );
			$column_class = '';
			if ( $number_of_columns < $markNumDays ) {
				$column_class = 'rangeSelected';
			}
			$occupancydate = $date->format( 'Y-m-d' );
			if ( $occupancydate == $today ) {
				$month = 'Today';
			}
			$output .= '<td class="calendarCell monthHeader ' . esc_attr( $this->today_css_tag( $occupancydate ) ) . ' ' . esc_attr( $this->start_of_month_css_tag( $occupancydate ) ) . ' ' . esc_attr( $column_class ) . '">';
			$output .= '<div class="monthDayinfo-wrap">';
			$output .= '<div class="month">';
			$output .= esc_html( $month );
			$output .= '</div>';
			$output .= '<div class="day-letter">';
			$output .= esc_html( $date->format( 'D' ) );
			$output .= '</div>';
			$output .= '<div class="day">';
			$output .= esc_html( $date->format( 'j' ) );
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</td>';
		endforeach;
		return $output;
	}

	/**
	 * Method create_masonry_tabs
	 *
	 * @param $reservation_id $reservation_id
	 * @param $checkin $checkin
	 * @param $checkout $checkout
	 *
	 * @return void
	 */
	private function create_masonry_tabs( $reservation_id, $checkin, $checkout ) {
		if ( ! array_key_exists( $reservation_id, $this->reservation_tabs ) ) {

			$new_checkin  = $checkin; // Checkin date of the new value to be added
			$has_conflict = false; // Flag to track if there is a conflict
			// Iterate through the existing array
			foreach ( $this->reservation_tabs as $value ) {
				$stay_checkout_date = $value['checkout'];

				// Compare the new checkin date with existing checkout dates
				if ( $new_checkin <= $stay_checkout_date ) {
					$has_conflict = true;
					break; // Stop iterating if a conflict is found
				}
			}

			$given_checkin_date = $checkin;
			// Filter the array based on the check-in date and reservations has not checkedout
			$filtered_array = array_filter(
				$this->reservation_tabs,
				function ( $value ) use ( $given_checkin_date ) {
					return $value['checkout'] > $given_checkin_date;
				}
			);

			// Extract the room numbers from the filtered array
			$room_numbers = array_column( $filtered_array, 'room' );

			// Check for missing room numbers
			$missing_number = false;
			sort( $room_numbers );

			if ( ! empty( $room_numbers ) ) {
				for ( $i = 1; $i <= max( $room_numbers ); $i++ ) {
					if ( ! in_array( $i, $room_numbers ) ) {
						$missing_number = $i;
						break;
					}
				}
			}

			// Output the result
			if ( $missing_number ) {
				$room = $missing_number;
			} else {
				$given_date   = $checkin;
				$record_count = 0;

				foreach ( $this->reservation_tabs as $value ) {
					$stay_checkout_date = $value['checkout'];

					if ( $stay_checkout_date > $given_date ) {
						++$record_count;
					}
				}

				if ( $has_conflict ) {
					//The new checkin date falls within existing checkout dates.";
					$room = $record_count + 1;
				} else {
					//The new checkin date is outside existing checkout dates.";
					$room = $record_count - 1;
				}
			}

			if ( empty( $this->reservation_tabs ) ) {
				$room = 1;
			}
			if ( $room < 0 ) {
				$room = 1;
			}

			$this->reservation_tabs[ $reservation_id ]['room']     = $room;
			$this->reservation_tabs[ $reservation_id ]['checkin']  = $checkin;
			$this->reservation_tabs[ $reservation_id ]['checkout'] = $checkout;
		}
	}

	/**
	 * Method reserved_tab
	 *
	 * @param $reservation_data $reservation_data
	 * @param $current_day $current_day
	 * @param $calendar_start $calendar_start
	 *
	 * @return void
	 */
	private function reserved_tab( $reservation_data, $current_day, $calendar_start ) {
		$display = false;
		$tab     = array();
		$row     = 0;
		$room    = 1;
		foreach ( $reservation_data as $reservation ) {
			$start_date_display = '';
			$guest_name         = '';
			$reservation_id     = $reservation['id'];

			if ( ! $this->using_cache ) {
				$reservation_instance  = new \Staylodgic\Reservations( $date = false, $room_id = false, $reservation_id = $reservation['id'] );
				$booking_number        = $reservation_instance->get_booking_number();
				$guest_name            = $reservation_instance->get_reservation_guest_name();
				$reserved_days         = $reservation_instance->count_reservation_days();
				$checkin               = $reservation_instance->get_checkin_date();
				$checkout              = $reservation_instance->get_checkout_date();
				$reservation_status    = $reservation_instance->get_reservation_status();
				$reservation_substatus = $reservation_instance->get_reservation_sub_status();
				$booking_channel       = $reservation_instance->get_reservation_channel();

				$reservation_edit_link = get_edit_post_link( $reservation['id'] );

				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_booking_number']         = $booking_number;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_guest_name'] = $guest_name;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['count_reservation_days']     = $reserved_days;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_checkin_date']           = $checkin;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_checkout_date']          = $checkout;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_status']     = $reservation_status;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_sub_status'] = $reservation_substatus;
				$this->calendar_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_channel']    = $booking_channel;

				$this->calendar_data['the_tabs_data'][ $reservation_id ]['reservation_edit_link'] = $reservation_edit_link;
			} else {
				$booking_number        = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_booking_number'];
				$guest_name            = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_guest_name'];
				$reserved_days         = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['count_reservation_days'];
				$checkin               = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_checkin_date'];
				$checkout              = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_checkout_date'];
				$reservation_status    = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_status'];
				$reservation_substatus = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_sub_status'];
				$booking_channel       = $this->cached_data['the_tabs_data'][ $reservation_id ][ $current_day ]['get_reservation_channel'];

				$reservation_edit_link = $this->cached_data['the_tabs_data'][ $reservation_id ]['reservation_edit_link'];
			}

			++$row;

			if ( 'cancelled' === $reservation_status && $this->avail_confirmed_only ) {
				continue;
			}
			if ( 'pending' === $reservation_status && $this->avail_confirmed_only ) {
				continue;
			}

			$this->create_masonry_tabs( $reservation_id, $checkin, $checkout );

			if ( array_key_exists( $reservation_id, $this->reservation_tabs ) ) {
				$room = $this->reservation_tabs[ $reservation_id ]['room'];
			}

			$display_info = $guest_name;
			if ( $reservation['start'] != 'no' ) {
				$start_date = new \DateTime();
				$start_date->setTimestamp( $reservation['checkin'] );
				$start_date_display = $start_date->format( 'M j, Y' );
				$width              = ( 80 * ( $reserved_days ) ) - 3;
				$tab[ $room ]       = '<a class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservation_id ) . ' reservation-edit-link" href="' . esc_attr( $reservation_edit_link ) . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-' . esc_attr( $reservation_status ) . ' reservation-substatus-' . esc_attr( $reservation_substatus ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-guest="' . esc_attr( $guest_name ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-bookingnumber="' . esc_attr( $booking_number ) . '" data-reservationid="' . $reservation['id'] . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr( $reserved_days ) . '"><div data-tabwidth="' . esc_attr( $width ) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html( $display_info ) . '<span>' . esc_html( $booking_channel ) . '</span></div></div></div></div></a>';
				$display            = true;
			} elseif ( $current_day != $checkout ) {
					// Get the checkin day for this as it's in the past of start of the availblablity calendar.
					// So this tab is happening from the past and needs to be labled athough an extention.
					$check_in_date_past = new \DateTime();
					$check_in_date_past->setTimestamp( $reservation['checkin'] );
					$check_in_date_past = $check_in_date_past->format( 'Y-m-d' );

					$days_between = \Staylodgic\Common::count_days_between_dates( $check_in_date_past, $current_day );

					$width = ( 80 * ( $reserved_days - $days_between ) ) - 3;

				if ( $check_in_date_past < $calendar_start && $calendar_start == $current_day ) {
					$tab[ $room ] = '<a class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservation_id ) . ' reservation-edit-link" href="' . esc_attr( $reservation_edit_link ) . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-' . esc_attr( $reservation_status ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-guest="' . esc_attr( $guest_name ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-bookingnumber="' . esc_attr( $booking_number ) . '" data-reservationid="' . esc_attr( $reservation['id'] ) . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr( $reserved_days ) . '"><div data-tabwidth="' . esc_attr( $width ) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html( $display_info ) . '<span>' . esc_html( $booking_channel ) . '</span></div></div></div></div></a>';
				} else {
					$tab[ $room ] = '<div class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservation_id ) . ' reserved-tab-wrap reserved-extended reservation-' . esc_attr( $reservation_status ) . ' reservation-substatus-' . esc_attr( $reservation_substatus ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-reservationid="' . esc_attr( $reservation['id'] ) . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab"></div></div>';
				}
					$display = true;
			}
		}

		krsort( $tab );
		$tab_array = array();
		$htmltab   = '';

		if ( $display ) {

			foreach ( $tab as $key => $value ) {
				$htmltab .= $value;
			}
		}
		$tab_array['tab'] = $htmltab;

		return $tab_array;
	}
}

$instance = new \Staylodgic\Availablity_Calendar();
