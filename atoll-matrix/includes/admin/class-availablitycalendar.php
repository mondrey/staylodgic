<?php
namespace AtollMatrix;

class AvailablityCalendar extends AvailablityCalendarBase {

	public function __construct( $startDate = null, $endDate = null ) {
		parent::__construct( $startDate, $endDate );

		// WordPress AJAX action hook
		add_action( 'wp_ajax_get_Selected_Range_AvailabilityCalendar', array( $this, 'get_Selected_Range_AvailabilityCalendar' ) );
		add_action( 'wp_ajax_nopriv_get_Selected_Range_AvailabilityCalendar', array( $this, 'get_Selected_Range_AvailabilityCalendar' ) );

		add_action( 'admin_menu', array( $this, 'room_Reservation_Plugin_Add_Admin_Menu' ) );

		// Register the AJAX action
		add_action( 'wp_ajax_fetchOccupancy_Percentage_For_Calendar_Range', array( $this, 'fetchOccupancy_Percentage_For_Calendar_Range' ) );
		add_action( 'wp_ajax_nopriv_fetchOccupancy_Percentage_For_Calendar_Range', array( $this, 'fetchOccupancy_Percentage_For_Calendar_Range' ) );

	}

	public function fetchOccupancy_Percentage_For_Calendar_Range( $startDate = false, $endDate = false, $onlyFullOccupancy = false ) {
		// Perform necessary security checks or validation here
	
		// Check if AJAX POST values are set
		$isAjaxRequest = isset( $_POST['start'] ) && isset( $_POST['end'] );
	
		// Calculate current date
		$currentDate = current_time('Y-m-d');
	
		// Calculate end date as 90 days from the current date
		$endDate = date('Y-m-d', strtotime($currentDate . ' +90 days'));
	
		if ( ! $startDate ) {
			// Use the current date as the start date
			$startDate = $currentDate;
		}
	
		// Retrieve start and end dates from the AJAX request if not provided
		if ( $isAjaxRequest ) {
			$startDate = sanitize_text_field( $_POST['start'] );
			$endDate   = sanitize_text_field( $_POST['end'] );
		}
	
		if ( isset( $startDate ) && isset( $endDate ) ) {
			
			$dates = \AtollMatrix\Common::getDates_Between( $startDate, $endDate );
	
			$occupancy_data = array();
	
			foreach ( $dates as $date ) :
				$occupancydate                    = $date;
				$occupancyPercentage              = $this->calculateOccupancyForDate( $occupancydate );
	
				// Check if only full occupancy dates should be included
				if ( $onlyFullOccupancy && $occupancyPercentage < 100 ) {
					continue; // Skip this date if not full occupancy
				}
	
				$occupancy_data[ $occupancydate ] = $occupancyPercentage;
			endforeach;
	
			// Send occupancy data as JSON response if it's an AJAX request
			if ( $isAjaxRequest ) {
				wp_send_json( $occupancy_data );
			} else {
				return $occupancy_data; // Return the array if it's not an AJAX request
			}
		} else {
			if ( $isAjaxRequest ) {
				wp_send_json_error('Invalid date range!');
			} else {
				return array(); // Return an empty array if it's not an AJAX request and the date range is invalid
			}
		}
	}			

	// Add the Availability menu item to the admin menu
	public function room_Reservation_Plugin_Add_Admin_Menu() {
		add_menu_page(
			'Availability',
			'Availability',
			'manage_options',
			'atmx-availability',
			array( $this, 'room_Reservation_Plugin_Display_Availability_Calendar' ),
			'dashicons-calendar-alt',
			20
		);
	}
	// Callback function to display the Availability page
	public function room_Reservation_Plugin_Display_Availability_Calendar() {
		// Check if user has sufficient permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Output the HTML for the Availability page
		?>
		<div class="wrap">
			<h1>Availability</h1>
			<?php
			// Add any custom HTML content here
			?>
		</div>
		<div class="calendar-controls-wrap">
			<button id="prev">Previous</button>
			<button id="prev-half">Prev 15</button>
			<button id="prev-week">Prev 7</button>
			<input type="text" class="availabilitycalendar" id="availabilitycalendar" name="availabilitycalendar" value="" />
			<button id="next-week">Next 7</button>
			<button id="next-half">Next 15</button>
			<button id="next">Next</button>
			<a href="#" id="quantity-popup-link" data-bs-toggle="modal" data-bs-target="#quantity-popup">Update Quantity</a>
			<a href="#" id="rates-popup-link" data-bs-toggle="modal" data-bs-target="#rates-popup">Update Rates</a>
		</div>
		<div id="container">
			<div id="calendar">
				<?php
				$calendar = $this->getAvailabilityCalendar();
				// error_log ( $calendar );
				echo $calendar;
				?>
			</div>
		</div>
		<?php

		\AtollMatrix\Modals::quanityModal();
		\AtollMatrix\Modals::ratesModal();
	}

	public function get_Selected_Range_AvailabilityCalendar() {

		// Check if the request has necessary data
		if ( ! isset( $_POST['start_date'], $_POST['end_date'] ) ) {
			wp_die( 'Missing parameters' );
		}

		// Sanitize inputs
		$start_date = sanitize_text_field( $_POST['start_date'] );
		$end_date   = sanitize_text_field( $_POST['end_date'] );
		// error_log( 'Here:' . $start_date . ' ' . ':' . $end_date );

		// Validate inputs
		if ( ! strtotime( $start_date ) || ! strtotime( $end_date ) ) {
			wp_die( 'Invalid dates' );
		}

		ob_start();
		echo $this->getAvailabilityCalendar( $start_date, $end_date );
		$output = ob_get_clean();
		echo $output;

		// end execution
		wp_die();
	}

	public function getAvailabilityCalendar( $startDate = false, $endDate = false ) {

		if ( ! $startDate ) {
			$startDate = $this->startDate;
			$endDate   = $this->endDate;
		} else {
			$startDate = new \DateTime( $startDate );
			$endDate   = new \DateTime( $endDate );
		}

		$dates = $this->getDates( $startDate, $endDate );
		$today = $this->today;

		if ( $startDate instanceof \DateTime ) {
			$startDateString = $startDate->format( 'Y-m-d' );
		} else {
			$startDateString = $startDate;
		}

		if ( $endDate instanceof \DateTime ) {
			$endDateString = $endDate->format( 'Y-m-d' );
		} else {
			$endDateString = $endDate;
		}

		$output = '<table id="calendarTable" data-calstart="' . esc_attr( $startDateString ) . '" data-calend="' . esc_attr( $endDateString ) . '">';
			$output .= '<tr class="calendarRow">';
				$output .= self::displayOccupancy_TableDataBlock( $startDate, $endDate);
				$output .= self::displayOccupancyRange_TableDataBlock( $dates );
			$output .= '</tr>';
			$output .= '<tr class="calendarRow">';
				$output .= '<td class="calendarCell rowHeader"></td>';
				$numDays = $this->setNumDays( $startDateString, $endDateString );
				$output .= self::displayDate_TableDataBlock( $dates, $numDays );
			$output .= '</tr>';

			$this->roomlist = \AtollMatrix\Rooms::getRoomList();
			$room_output = '';
			foreach ( $this->roomlist as $roomId => $roomName ) :
				error_log('cache date');
				error_log( $roomId . ' ' . $startDateString . ' ' . $endDateString );
				error_log('----------');
				$cache_instance = new \AtollMatrix\Cache( $roomId, $startDateString, $endDateString );
				// $cache_instance->deleteCache();
				$transient_key = $cache_instance->generateRoomCacheKey();
				$cached_calendar = $cache_instance->getCache($transient_key);

				if ( $cache_instance->hasCache($transient_key) && true == $cache_instance->isCacheAllowed() ) {
					$room_output = $cached_calendar;
				} else {

					$room_reservations_instance = new \AtollMatrix\Reservations( $dateString = false, $roomId );
					$room_reservations_instance->calculateAndUpdateRemainingRoomCountsForAllDates();

					$checkout_list = array();

					$room_output .= '<tr class="calendarRow calendar-room-row" data-id="' . esc_attr( $roomId ) . '">';
						$room_output .= '<td class="calendarCell rowHeader">';
							$room_output .= esc_html( $roomName );
						$room_output .= '</td>';
						foreach ( $dates as $date ) :
							$dateString       = $date->format( 'Y-m-d' );
							$reservation_data = array();

							$reservation_instance = new \AtollMatrix\Reservations( $dateString, $roomId );
							$reservation_data     = $reservation_instance->isDate_Reserved();
							// $remaining_room_count  = $reservation_instance->getDirectRemainingRoomCount();
							$remaining_rooms      = $reservation_instance->remainingRooms_For_Day();
							// $reserved_rooms       = $reservation_instance->calculateReservedRooms();
							if ( 0 == $remaining_rooms ) {
								$room_was_opened      = $reservation_instance->wasRoom_Ever_Opened();
								if ( false === $room_was_opened ) {
									$remaining_rooms = '/';
								}
							}

							// $max_room_count = \AtollMatrix\Rooms::getMaxQuantityForRoom( $roomId, $dateString );

							$room_rate              = \AtollMatrix\Rates::getRoomRateByDate( $roomId, $dateString );
							$occupancy_status_class = "";
							if ( $reservation_instance->isRoom_For_Day_Fullybooked() ) {
								$occupancy_status_class = "fully-booked";
							} else {
								$occupancy_status_class = "room-available";
							}
							$room_output .= '<td class="calendarCell ' . esc_attr( $this->todayCSSTag( $dateString ) ) . ' ' . esc_attr( $this->startOfMonthCSSTag( $dateString ) ) . ' ' . esc_attr( $occupancy_status_class ) . '">';
							
							$room_output .= '<div class="calendar-info-wrap">';
								$room_output .= '<div class="calendar-info">';
									$room_output .= '<a href="#" class="quantity-link" data-remaining="'. esc_attr( $remaining_rooms ) . '" data-date="' . esc_attr( $dateString ) . '" data-room="' . esc_attr( $roomId ) . '">'. esc_html( $remaining_rooms ) . '</a>';

									if ( ! empty( $room_rate ) && isset( $room_rate ) && $room_rate > 0 ) {
										$room_output .= '<a class="roomrate-link" href="#">' . esc_html( $room_rate ) . '</a>';
									}
								$room_output .= '</div>';
							$room_output .= '</div>';
							$room_output .= '<div class="reservation-tab-wrap" data-day="' . esc_attr( $dateString ) .'">';
								if ( $reservation_data ) {
									$reservation_module = array();
									//echo atollmatrix_generate_reserved_tab( $reservation_data, $checkout_list );
									$reservation_module = $this->ReservedTab( $reservation_data, $checkout_list, $dateString, $startDateString );
									$room_output .= $reservation_module['tab'];
									$checkout_list = $reservation_module['checkout'];
									//print_r( $checkout_list );
								}
							$room_output .= '</div>';
							$room_output .= '</td>';
						endforeach;
					$room_output .= '</tr>';

					$cache_instance->setCache( $transient_key, $room_output);
				}
			endforeach;
			$output .= $room_output;
		$output .= '</table>';
		return $output;
	}


	private function displayOccupancy_TableDataBlock( $startDate = false, $endDate = false ) {
		if ( ! $startDate ) {
			$startDate = $this->startDate;
			$endDate   = $this->endDate;
		}
		if ( $startDate instanceof \DateTime ) {
			$startDateString = $startDate->format( 'Y-m-d' );
		} else {
			$startDateString = $startDate;
		}
		if ( $endDate instanceof \DateTime ) {
			$endDateString = $endDate->format( 'Y-m-d' );
		} else {
			$endDateString = $endDate;
		}
		$output = '<td class="calendarCell rowHeader">';
			$output .= '<div class="occupancyStats-wrap">';
				$output .= '<div class="occupancyStats-inner">';
					$output .= '<div class="occupancy-total">';
					$output .= __('Occupancy','atollmatrix');
						$output .= '<span class="occupancy-total-stats">';
							$output .=  esc_html( $this->calculateOccupancyTotalForRange( $startDateString, $endDateString ) );
							$output .= '<span>%</span>';
						$output .= '</span>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</td>';
		return $output;
	}

	public function todayCSSTag( $occupancydate ) {
		$today_status_class = '';
		if ( $occupancydate == $this->today ) {
			$today_status_class = "is-today";
		}
		return $today_status_class;
	}

	public function startOfMonthCSSTag($occupancydate) {
		$startOfMonth_class = '';
	
		// Assuming $occupancydate is in 'Y-m-d' format, extract year and month
		$yearMonth = substr($occupancydate, 0, 7); // This gives 'YYYY-MM'
	
		// Create the first day of the month string for the given date
		$firstDayOfOccupancyMonth = $yearMonth . '-01';
	
		// Compare the provided date with the first day of its month
		if ($occupancydate == $firstDayOfOccupancyMonth) {
			$startOfMonth_class = "start-of-month";
		}
	
		return $startOfMonth_class;
	}

	private function displayOccupancyRange_TableDataBlock( $dates ) {
		$number_of_columns = 0;
		$output = '';

		foreach ( $dates as $date ) :
			$number_of_columns++;
			$occupancydate = $date->format( 'Y-m-d' );
			$output .= '<td class="calendarCell monthHeader occupancy-stats '. esc_attr( $this->todayCSSTag( $occupancydate ) ) . ' ' . esc_attr( $this->startOfMonthCSSTag( $occupancydate ) ) . '">';
				$output .= '<div class="occupancyStats-wrap">';
					$output .= '<div class="occupancyStats-inner">';
						$output .= '<div class="occupancy-adr">';
							$output .= __('Open','atollmatrix');
						$output .= '</div>';
					$output .= '<div class="occupancy-percentage">';
						$output .= esc_html( $this->calculateRemainingRoomsForDate( $occupancydate ) );
						$output .= '<span></span>';
					$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</td>';
		endforeach;

		return $output;
	}

	private function displayDate_TableDataBlock( $dates = false, $numDays = false ) {
		// error_log( 'Number of days: ' . $numDays );
		$today             = $this->today;
		$number_of_columns = 0;
		if ( ! $numDays ) {
			$markNumDays = $this->numDays + 1;
		} else {
			$markNumDays = $numDays + 1;
		}

		$output = '';

		foreach ( $dates as $date ) :
			$number_of_columns++;
			$month        = $date->format( 'M' );
			$column_class = '';
			if ( $number_of_columns < $markNumDays ) {
				$column_class = "rangeSelected";
			}
			$occupancydate = $date->format( 'Y-m-d' );
			if ( $occupancydate == $today ) {
				$month = 'Today';
			}
			$output .= '<td class="calendarCell monthHeader ' . esc_attr( $this->todayCSSTag( $occupancydate ) ) . ' ' . esc_attr( $this->startOfMonthCSSTag( $occupancydate ) ) . ' ' . esc_attr( $column_class ) . '">';
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

	private function ReservedTab( $reservation_data, $checkout_list, $current_day, $calendar_start ) {
		$display = false;
		$tab     = array();
		$row     = 0;
		$room    = 1;
		foreach ( $reservation_data as $reservation ) {
			$start_date_display   = '';
			$guest_name           = '';
			$reservatoin_id       = $reservation['id'];
			$reservation_instance = new \AtollMatrix\Reservations( $date = false, $room_id = false, $reservation_id = $reservation['id'] );
			$booking_number       = $reservation_instance->getBookingNumber();
			$guest_name           = $reservation_instance->getReservationGuestName();
			$reserved_days        = $reservation_instance->countReservationDays();
			$checkin              = $reservation_instance->getCheckinDate();
			$checkout             = $reservation_instance->getCheckoutDate();
			$reservation_status   = $reservation_instance->getReservationStatus();
			$reservation_substatus   = $reservation_instance->getReservationSubStatus();
			$booking_channel   = $reservation_instance->getReservationChannel();
			$row++;

			if ( ! array_key_exists( $reservatoin_id, $checkout_list ) ) {

				$newCheckin  = $checkin; // Checkin date of the new value to be added
				$hasConflict = false; // Flag to track if there is a conflict
				// Iterate through the existing array
				foreach ( $checkout_list as $value ) {
					$checkoutDate = $value['checkout'];

					// Compare the new checkin date with existing checkout dates
					if ( $newCheckin <= $checkoutDate ) {
						$hasConflict = true;
						// echo 'has conflict : ' . $newCheckin . ' with ' . $checkoutDate;
						break; // Stop iterating if a conflict is found
					}
				}

				$givenCheckinDate = $checkin;
				// Filter the array based on the check-in date and reservations has not checkedout
				$filteredArray = array_filter( $checkout_list, function ($value) use ($givenCheckinDate) {
					return $value['checkout'] > $givenCheckinDate;
				} );

				// Extract the room numbers from the filtered array
				$roomNumbers = array_column( $filteredArray, 'room' );

				// Check for missing room numbers
				$missingNumber = false;
				sort( $roomNumbers );

				if ( ! empty( $roomNumbers ) ) {
					for ( $i = 1; $i <= max( $roomNumbers ); $i++ ) {
						if ( ! in_array( $i, $roomNumbers ) ) {
							$missingNumber = $i;
							break;
						}
					}
				}

				// Output the result
				if ( $missingNumber ) {
					$room = $missingNumber;
				} else {
					$givenDate   = $checkin;
					$recordCount = 0;

					foreach ( $checkout_list as $value ) {
						$checkoutDate = $value['checkout'];

						if ( $checkoutDate > $givenDate ) {
							$recordCount++;
						}
					}

					if ( $hasConflict ) {
						//The new checkin date falls within existing checkout dates.";
						$room = $recordCount + 1;
					} else {
						//The new checkin date is outside existing checkout dates.";
						$room = $recordCount - 1;
					}
				}


				if ( empty( $checkout_list ) ) {
					$room = 1;
				}
				if ( $room < 0 ) {
					$room = 1;
				}

				$checkout_list[ $reservatoin_id ]['room']     = $room;
				$checkout_list[ $reservatoin_id ]['checkin']  = $checkin;
				$checkout_list[ $reservatoin_id ]['checkout'] = $checkout;
			}

			if ( array_key_exists( $reservatoin_id, $checkout_list ) ) {
				$room = $checkout_list[ $reservatoin_id ]['room'];
			}

			$reservation_edit_link = get_edit_post_link( $reservation['id'] );
			$display_info = $guest_name;
			if ( $reservation['start'] <> 'no' ) {
				$start_date = new \DateTime();
				$start_date->setTimestamp( $reservation['checkin'] );
				$start_date_display = $start_date->format( 'M j, Y' );
				$width              = ( 80 * ( $reserved_days ) ) - 3;
				$tab[ $room ]       = '<a class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservatoin_id ) . ' reservation-edit-link" href="' . esc_attr( $reservation_edit_link ) . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-' . esc_attr( $reservation_status ) . ' reservation-substatus-' . esc_attr( $reservation_substatus ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-guest="' . esc_attr( $guest_name ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-bookingnumber="' . esc_attr( $booking_number ) . '" data-reservationid="' . $reservation['id'] . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr( $reserved_days ) . '"><div data-tabwidth="' . esc_attr( $width ) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html( $display_info ) . '<span>'. esc_html( $booking_channel ) .'</span></div></div></div></div></a>';
				$display            = true;
			} else {
				if ( $current_day <> $checkout ) {
					// Get the checkin day for this as it's in the past of start of the availblablity calendar.
					// So this tab is happening from the past and needs to be labled athough an extention.
					$check_in_date_past = new \DateTime();
					$check_in_date_past->setTimestamp( $reservation['checkin'] );
					$check_in_date_past = $check_in_date_past->format( 'Y-m-d' );
					$daysBetween        = \AtollMatrix\Common::countDays_BetweenDates( $check_in_date_past, $current_day );
					$width              = ( 80 * ( $reserved_days - $daysBetween ) ) - 3;
					if ( $check_in_date_past < $calendar_start && $calendar_start == $current_day ) {
						$tab[ $room ] = '<a class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservatoin_id ) . ' reservation-edit-link" href="' . esc_attr( $reservation_edit_link ) . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-' . esc_attr( $reservation_status ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-guest="' . esc_attr( $guest_name ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-bookingnumber="' . esc_attr( $booking_number ) . '" data-reservationid="' . esc_attr( $reservation['id'] ) . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr( $reserved_days ) . '"><div data-tabwidth="' . esc_attr( $width ) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html( $display_info ) . '<span>'. esc_html( $booking_channel ) .'</span></div></div></div></div></a>';
					} else {
						$tab[ $room ] = '<div class="reservation-tab-is-' . esc_attr( $reservation_status ) . ' ' . esc_attr( $reservation_substatus ) . ' reservation-tab-id-' . esc_attr( $reservatoin_id ) . ' reserved-tab-wrap reserved-extended reservation-' . esc_attr( $reservation_status ) . ' reservation-substatus-' . esc_attr( $reservation_substatus ) . '" data-reservationstatus="' . esc_attr( $reservation_status ) . '" data-room="' . esc_attr( $room ) . '" data-row="' . esc_attr( $row ) . '" data-reservationid="' . esc_attr( $reservation['id'] ) . '" data-checkin="' . esc_attr( $checkin ) . '" data-checkout="' . esc_attr( $checkout ) . '"><div class="reserved-tab"></div></div>';
					}
					$display = true;
				}
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
		$tab_array['tab']      = $htmltab;
		$tab_array['checkout'] = $checkout_list;

		return $tab_array;
	}

}

$instance = new \AtollMatrix\AvailablityCalendar();