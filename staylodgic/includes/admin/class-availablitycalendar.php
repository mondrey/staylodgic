<?php
namespace Staylodgic;

class AvailablityCalendar extends AvailablityCalendarBase
{

    public function __construct($startDate = null, $endDate = null, $cachedData = null, $calendarData = null, $reservation_tabs = null, $usingCache = false, $availConfirmedOnly = false)
    {
        parent::__construct($startDate, $endDate, $calendarData, $reservation_tabs, $availConfirmedOnly);

        // WordPress AJAX action hook
        add_action('wp_ajax_get_Selected_Range_AvailabilityCalendar', array($this, 'get_Selected_Range_AvailabilityCalendar'));
        add_action('wp_ajax_nopriv_get_Selected_Range_AvailabilityCalendar', array($this, 'get_Selected_Range_AvailabilityCalendar'));

        add_action('admin_menu', array($this, 'AvailablityCalendarDisplay'));

        // Register the AJAX action
        add_action('wp_ajax_fetchOccupancy_Percentage_For_Calendar_Range', array($this, 'fetchOccupancy_Percentage_For_Calendar_Range'));
        add_action('wp_ajax_nopriv_fetchOccupancy_Percentage_For_Calendar_Range', array($this, 'fetchOccupancy_Percentage_For_Calendar_Range'));

        // Add the AJAX action to both the front-end and the admin
        add_action('wp_ajax_update_availDisplayConfirmedStatus', array($this, 'update_availDisplayConfirmedStatus'));
        add_action('wp_ajax_nopriv_update_availDisplayConfirmedStatus', array($this, 'update_availDisplayConfirmedStatus'));

    }

    public function update_availDisplayConfirmedStatus() {

        // Verify the nonce
        if (!isset($_POST[ 'staylodgic_availabilitycalendar_nonce' ]) || !check_admin_referer('staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
        // Check if the confirmed_only value is set
        if (isset($_POST['confirmed_only'])) {

            error_log(' Only confirmed bookings ');
            error_log( $_POST['confirmed_only'] );

            if ( 0 == $_POST['confirmed_only'] ) {
                // Update the option based on the switch value
                update_option('staylodgic_availsettings_confirmed_only', 0 );
            } else {
                // Update the option based on the switch value
                update_option('staylodgic_availsettings_confirmed_only', 1 );
            }
    
            // Return a success response
            wp_send_json_success();
        } else {
            // Return an error response
            wp_send_json_error('The confirmed_only value is not set.');
        }
    }    

    public function fetchOccupancy_Percentage_For_Calendar_Range($startDate = false, $endDate = false, $onlyFullOccupancy = false)
    {
        // Perform necessary security checks or validation here

        // Check if AJAX POST values are set
        $isAjaxRequest = isset($_POST[ 'start' ]) && isset($_POST[ 'end' ]);

        // Calculate current date
        $currentDate = current_time('Y-m-d');

        // Calculate end date as 90 days from the current date
        $endDate = date('Y-m-d', strtotime($currentDate . ' +90 days'));

        if (!$startDate) {
            // Use the current date as the start date
            $startDate = $currentDate;
        }

        // Retrieve start and end dates from the AJAX request if not provided
        if ($isAjaxRequest) {
            $startDate = sanitize_text_field($_POST[ 'start' ]);
            $endDate   = sanitize_text_field($_POST[ 'end' ]);
        }

        if (isset($startDate) && isset($endDate)) {

            $dates = \Staylodgic\Common::getDates_Between($startDate, $endDate);

            $occupancy_data = array();

            foreach ($dates as $date):
                $occupancydate       = $date;
                $occupancyPercentage = $this->calculateOccupancyForDate($occupancydate);

                // Check if only full occupancy dates should be included
                if ($onlyFullOccupancy && $occupancyPercentage < 100) {
                    continue; // Skip this date if not full occupancy
                }

                $occupancy_data[ $occupancydate ] = $occupancyPercentage;
            endforeach;

            // Send occupancy data as JSON response if it's an AJAX request
            if ($isAjaxRequest) {
                wp_send_json($occupancy_data);
            } else {
                return $occupancy_data; // Return the array if it's not an AJAX request
            }
        } else {
            if ($isAjaxRequest) {
                wp_send_json_error('Invalid date range!');
            } else {
                return array(); // Return an empty array if it's not an AJAX request and the date range is invalid
            }
        }
    }

    // Add the Availability menu item to the admin menu
    public function AvailablityCalendarDisplay()
    {
        // Add the parent menu item
        add_menu_page(
            'View Availability',
            'View Availability',
            'manage_options',
            'slgc-availability',
            array($this, 'room_Reservation_Plugin_Display_Availability_Calendar'), // Callback for the parent page (can be empty if not needed)
            'dashicons-visibility',
            33
        );

    }

    // Callback function to display the Availability page
    public function room_Reservation_Plugin_Display_Availability_Calendar_Yearly()
    {
        // Output the HTML for the Availability page
        ?>
		<div class="wrap">
			<h1><?php _e('Availability Calendar','staylodgic'); ?></h1>
			<?php
            if ( ! \Staylodgic\Rooms::hasRooms() ) {
                echo '<h1>' . __('No Rooms Found','staylodgic') . '</h1>';
                return;
            }
        ?>
		</div>
        <?php
    }

    public function getDisplayConfirmedStatus() {
        $this->availConfirmedOnly = get_option('staylodgic_availsettings_confirmed_only');

        // Check if the option is not found and set it to '1'
        if ($this->availConfirmedOnly === false) {
            update_option('staylodgic_availsettings_confirmed_only', true);
            $this->availConfirmedOnly = true;
        }

        $confirmed_status = '';

        return $this->availConfirmedOnly;
    }
    // Callback function to display the Availability page
    public function room_Reservation_Plugin_Display_Availability_Calendar()
    {
        // Check if user has sufficient permissions
        if (!current_user_can('manage_options')) {
            return;
        }

        // Output the HTML for the Availability page
        ?>
		<div class="wrap">
			<h1><?php _e('Availability Calendar','staylodgic'); ?></h1>
			<?php
            if ( ! \Staylodgic\Rooms::hasRooms() ) {
                echo '<h1>' . __('No Rooms Found','staylodgic') . '</h1>';
                return;
            }
            echo \Staylodgic\Modals::rateQtyToasts();

            $confirmed_status = '';
            if ( $this->getDisplayConfirmedStatus() ) {
                $confirmed_status = 'checked';
            }

// Add any custom HTML content here
        ?>
		</div>
        <div class="calendar-controls-wrap">
            <div class="calendar-controls">
                <ul class="calendar-controls-list">
                    <li class="nav-item">
                    <div class="preloader-element-outer"><div class="preloader-element"></div></div>
                    </li>
                    <li class="nav-item">
                        <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Previous Month" class="calendar-nav-buttons" id="prevmonth"><i class="fa-solid fa-arrow-left"></i></div>
                    </li>
                    <li class="nav-item">
                        <input type="text" class="availabilitycalendar" id="availabilitycalendar" name="availabilitycalendar" value="" />
                        <?php
                        $availabilitycalendar = wp_create_nonce('staylodgic-availabilitycalendar-nonce');
                        echo '<input type="hidden" name="staylodgic_availabilitycalendar_nonce" value="' . esc_attr($availabilitycalendar) . '" />';
                        ?>
                    </li>
                    <li class="nav-item">
                        <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Next Month" class="calendar-nav-buttons" id="nextmonth"><i class="fa-solid fa-arrow-right"></i></div>
                    </li>
                    <li class="nav-item nav-item-seperator">
                        <div class="calendar-nav-buttons calendar-text-button" id="quantity-modal-link" data-bs-toggle="modal" data-bs-target="#quantity-modal"><i class="fas fa-hashtag"></i>Quanity</div>
                    </li>
                    <li class="nav-item">
                        <div class="calendar-nav-buttons calendar-text-button" id="rates-modal-link" data-bs-toggle="modal" data-bs-target="#rates-modal"><i class="fas fa-dollar-sign"></i>Rate</div>
                    </li>
                    <li class="nav-item nav-item-seperator">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="calendar-booking-status" <?php echo esc_attr($confirmed_status); ?>>
                            <label class="form-check-label" for="calendar-booking-status">Display Confirmed</label>
                        </div>
                    </li>
                </ul>
            </div>
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
        \Staylodgic\Modals::quanityModal();
        \Staylodgic\Modals::ratesModal();
    }

    public function get_Selected_Range_AvailabilityCalendar()
    {

        // Verify the nonce
        if (!isset($_POST[ 'staylodgic_availabilitycalendar_nonce' ]) || !check_admin_referer('staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        // Check if the request has necessary data
        if (!isset($_POST[ 'start_date' ], $_POST[ 'end_date' ])) {
            wp_die('Missing parameters');
        }

        // Sanitize inputs
        $start_date = sanitize_text_field($_POST[ 'start_date' ]);
        $end_date   = sanitize_text_field($_POST[ 'end_date' ]);
        // error_log( 'Here:' . $start_date . ' ' . ':' . $end_date );

        // Validate inputs
        if (!strtotime($start_date) || !strtotime($end_date)) {
            wp_die('Invalid dates');
        }

        $this->getDisplayConfirmedStatus();

        ob_start();
        echo $this->getAvailabilityCalendar($start_date, $end_date);
        $output = ob_get_clean();
        echo $output;

        // end execution
        wp_die();
    }

    public function getAvailabilityCalendar($startDate = false, $endDate = false)
    {

        if (!$startDate) {
            $startDate = $this->startDate;
            $endDate   = $this->endDate;
        } else {
            $startDate = new \DateTime($startDate);
            $endDate   = new \DateTime($endDate);
        }

        $dates = $this->getDates($startDate, $endDate);
        $today = $this->today;

        if ($startDate instanceof \DateTime) {
            $startDateString = $startDate->format('Y-m-d');
        } else {
            $startDateString = $startDate;
        }

        if ($endDate instanceof \DateTime) {
            $endDateString = $endDate->format('Y-m-d');
        } else {
            $endDateString = $endDate;
        }

        $table_start = '<table id="calendarTable" data-calstart="' . esc_attr($startDateString) . '" data-calend="' . esc_attr($endDateString) . '">';

        $this->roomlist = \Staylodgic\Rooms::getRoomList();
        
		$room_output    = '';
		$all_room_output  = '';

        foreach ($this->roomlist as $roomID => $roomName):
            // error_log('cache date');
            // error_log($roomID . ' ' . $startDateString . ' ' . $endDateString);
            
            $cache_instance = new \Staylodgic\Cache($roomID, $startDateString, $endDateString);
            // $cache_instance->deleteCache();
            $transient_key   = $cache_instance->generateRoomCacheKey();
            $cached_calendar = $cache_instance->getCache($transient_key);

            $room_reservations_instance = new \Staylodgic\Reservations($dateString = false, $roomID);

            // $room_reservations_instance->cleanup_Reservations_Array( $roomID );
            $room_reservations_instance->calculateAndUpdateRemainingRoomCountsForAllDates();
			// error_log($transient_key);
			// error_log('----------');

			$use_cache = true;
            $this->usingCache = false;
            $this->cachedData = array();
            $this->calendarData = array();


			if ( isset( $cached_calendar['qty_rates'] )) {
				$cached_qty_rates = $cached_calendar['qty_rates'];

				// error_log('--------- Cache --------');
				// error_log('--------- Cache QTY RATES --------');
				//error_log(print_r($cached_calendar['qty_rates'], true));
	
				foreach ($dates as $date) {
	
					$dateString       = $date->format('Y-m-d');
	
					$reservation_instance = new \Staylodgic\Reservations($dateString, $roomID);
					$remaining_rooms = $reservation_instance->remainingRooms_For_Day();
					$room_rate              = \Staylodgic\Rates::getRoomRateByDate($roomID, $dateString);
	
					if (0 == $remaining_rooms) {
						$room_was_opened = $reservation_instance->wasRoom_Ever_Opened();
						if (false === $room_was_opened) {
							$remaining_rooms = '/';
						}
					}
	
					//$current_qty_rates[$dateString][$remaining_rooms] = $room_rate;
	
					if (isset($cached_qty_rates[$dateString])) {
	
						// error_log( $remaining_rooms . ' --#qty#-- ' . $cached_qty_rates[$dateString]['qty'] );
						// error_log( $room_rate . ' --#rate#-- ' . $cached_qty_rates[$dateString]['rate'] );

						if ( $remaining_rooms !== $cached_qty_rates[$dateString]['qty'] ) {
	
							// error_log( $remaining_rooms . ' --#compare-qty#-- ' . $cached_qty_rates[$dateString]['qty'] );
							$use_cache = false;
							$cache_instance->deleteCache($transient_key);
							break; // Exit the loop
						}
						if ( $room_rate !== $cached_qty_rates[$dateString]['rate'] ) {
							// error_log( $room_rate . ' --#compare-rate#-- ' . $cached_qty_rates[$dateString]['rate'] );
							$use_cache = false;
							$cache_instance->deleteCache($transient_key);
							break; // Exit the loop
						}
					}
	
				}
			}

            if ($cache_instance->hasCache($transient_key) && true == $cache_instance->isCacheAllowed() && true == $use_cache ) {

                // error_log('--------- Using Cache --------');
				// error_log('--------- Cache QTY RATES --------');
				// error_log(print_r($cached_calendar['qty_rates'], true));
				if ( isset( $cached_calendar )) {

					$this->cachedData = $cached_calendar;

                    $this->usingCache = true;
				}

            }

            // error_log('--------- Without using Cache ------------------------------------');

            // error_log('--------- Cache Data --------');
            // error_log(print_r($this->cachedData, true));

            $this->reservation_tabs  = array();
            $cache_qty_rate = array();
            $cache_output   = array();

            $room_output = '<tr class="calendarRow calendar-room-row" data-id="' . esc_attr($roomID) . '">';
            $room_output .= '<td class="calendarCell rowHeader">';
            $room_output .= esc_html($roomName);
            $room_output .= '</td>';

            if ( ! $this->usingCache ) {
                $reservation_instance = new \Staylodgic\Reservations(false, $roomID);
                $reservations = $reservation_instance->getReservationsForRoom($startDateString, $endDateString, false, false, $roomID);
            }

            foreach ($dates as $date):
                $dateString       = $date->format('Y-m-d');
                $reservation_data = array();

                $create_reservation_tag = true;

                if ( ! $this->usingCache ) {
                    $reservation_instance = new \Staylodgic\Reservations($dateString, $roomID);
                    $reservation_data     = $reservation_instance->buildReservationsDataForRoomForDay( $reservations, false, false, false, false );
                    // error_log( print_r($reservation_data,1));
                    // $remaining_room_count  = $reservation_instance->getDirectRemainingRoomCount();
                    $remaining_rooms = $reservation_instance->remainingRooms_For_Day();
                    // $reserved_rooms       = $reservation_instance->calculateReservedRooms();

                    if (0 == $remaining_rooms) {
                        $room_was_opened = $reservation_instance->wasRoom_Ever_Opened();
                        if (false === $room_was_opened) {
                            $remaining_rooms = '/';
                        }
                        $create_reservation_tag = false;
                    }

                    // $max_room_count = \Staylodgic\Rooms::getMaxQuantityForRoom( $roomID, $dateString );

                    $room_rate              = \Staylodgic\Rates::getRoomRateByDate($roomID, $dateString);
                    $occupancy_status_class = "";
                    if ($reservation_instance->isRoom_For_Day_Fullybooked()) {
                        $occupancy_status_class = "fully-booked";
                    } else {
                        $occupancy_status_class = "room-available";
                    }

                    $this->calendarData['cellData'][$dateString]['reservation_data'] = $reservation_data;
                    $this->calendarData['cellData'][$dateString]['remaining_rooms'] = $remaining_rooms;
                    $this->calendarData['cellData'][$dateString]['room_rate'] = $room_rate;
                    $this->calendarData['cellData'][$dateString]['occupancy_status_class'] = $occupancy_status_class;
                    $this->calendarData['cellData'][$dateString]['create_reservation_tag'] = $create_reservation_tag;

                } else {
                    $reservation_data = $this->cachedData['cellData'][$dateString]['reservation_data'];
                    $remaining_rooms = $this->cachedData['cellData'][$dateString]['remaining_rooms'];
                    $room_rate = $this->cachedData['cellData'][$dateString]['room_rate'];
                    $occupancy_status_class = $this->cachedData['cellData'][$dateString]['occupancy_status_class'];
                    $create_reservation_tag = $this->cachedData['cellData'][$dateString]['create_reservation_tag'];
                }

                $room_output .= '<td class="calendarCell ' . esc_attr($this->startOfMonthCSSTag($dateString)) . ' ' . esc_attr($occupancy_status_class) . '">';

                $room_output .= '<div class="calendar-info-wrap">';
                $room_output .= '<div class="calendar-info">';
                $room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Quantity" href="#" class="quantity-link" data-remaining="' . esc_attr($remaining_rooms) . '" data-date="' . esc_attr($dateString) . '" data-room="' . esc_attr($roomID) . '">' . esc_html($remaining_rooms) . '</a>';

                if (!empty($room_rate) && isset($room_rate) && $room_rate > 0) {
                    $room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rate" class="roomrate-link" href="#" data-rate="' . esc_attr($room_rate) . '" data-date="' . esc_attr($dateString) . '" data-room="' . esc_attr($roomID) . '">' . esc_html($room_rate) . '</a>';
                }

                if ( ! $this->usingCache ) {
                    $cache_qty_rate[ $dateString ][ 'qty' ] = $remaining_rooms;
                    $cache_qty_rate[ $dateString ][ 'rate' ] = $room_rate;
                }

                $room_output .= '</div>';

                $room_output .= '</div>';

                if ( $create_reservation_tag ) {
                    $createEndDate = new \DateTime($dateString);
                    $createEndDate->modify('+1 day');
                    $createOneDayAhead = $createEndDate->format('Y-m-d');
                    $new_post_link = admin_url('post-new.php?post_type=slgc_reservations&createfromdate=' . esc_attr($dateString).'&createtodate='.esc_attr($createOneDayAhead).'&roomID='.esc_attr($roomID));
                    $room_output .= '<div class="cal-create-reservation"><a href="' . esc_url($new_post_link) . '">+</a></div>';
                }

                $room_output .= '<div class="reservation-tab-wrap" data-day="' . esc_attr($dateString) . '">';
                if ($reservation_data) {
                    $reservation_module = array();
                    //echo staylodgic_generate_reserved_tab( $reservation_data, $reservation_tabs );
                    $reservation_module = $this->ReservedTab($reservation_data, $dateString, $startDateString);
                    $room_output .= $reservation_module[ 'tab' ];
                    // $this->reservation_tabs = $reservation_module[ 'checkout' ];
                    //print_r( $reservation_tabs );
                    
                }
                $room_output .= '</div>';
                $room_output .= '</td>';
            endforeach;
            $room_output .= '</tr>';

            $this->calendarData[ 'qty_rates' ] = $cache_qty_rate;

            $all_room_output .= $room_output;

            if ( ! $this->usingCache ) {

                error_log('--------- Saving Cache Data --------');
                error_log('--------- Room '.$roomName.' --------');
                error_log(print_r($this->calendarData, true));

                $cache_instance->setCache($transient_key, $this->calendarData);

            }
            
        endforeach;

        $stats_row = '<tr class="calendarRow">';
        $stats_row .= self::displayOccupancy_TableDataBlock($startDate, $endDate);
        $stats_row .= self::displayOccupancyRange_TableDataBlock($dates);
        $stats_row .= '</tr>';
        $stats_row .= '<tr class="calendarRow">';
        $stats_row .= '<td class="calendarCell rowHeader"></td>';
        $numDays = $this->setNumDays($startDateString, $endDateString);
        $stats_row .= self::displayDate_TableDataBlock($dates, $numDays);
        $stats_row .= '</tr>';

        $table_end = '</table>';

		$output = $table_start . $stats_row . $all_room_output . $table_end;
        return $output;
    }

    private function displayOccupancy_TableDataBlock($startDate = false, $endDate = false)
    {
        if (!$startDate) {
            $startDate = $this->startDate;
            $endDate   = $this->endDate;
        }
    
        if ($startDate instanceof \DateTime) {
            $startDateString = $startDate->format('Y-m-d');
        } else {
            $startDateString = $startDate;
        }
    
        if ($endDate instanceof \DateTime) {
            $endDate->modify('-5 days');
            $endDateString = $endDate->format('Y-m-d');
        } else {
            $endDate = new \DateTime($endDate);
            $endDate->modify('-5 days');
            $endDateString = $endDate->format('Y-m-d');
        }
    
        $occupancy_percent = esc_html($this->calculateOccupancyTotalForRange($startDateString, $endDateString));

        $output = '<td class="calendarCell rowHeader">';
        $output .= '<div data-occupancypercent="'.esc_attr( $occupancy_percent ).'" class="occupancyStats-wrap occupancy-percentage">';
        $output .= '<div class="occupancyStats-inner">';
        $output .= '<div class="occupancy-total">';
        $output .= '<span class="occupancy-total-stats">';
        $output .= esc_html( $occupancy_percent );
        $output .= '<span class="occupancy-percent-symbol">%</span>';
        $output .= __('Occupancy', 'staylodgic');
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</td>';
        return $output;
    }    

    public function todayCSSTag($occupancydate)
    {
        $today_status_class = '';
        if ($occupancydate == $this->today) {
            $today_status_class = "is-today";
        }
        return $today_status_class;
    }

    public function startOfMonthCSSTag($occupancydate)
    {
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

    private function displayOccupancyRange_TableDataBlock($dates)
    {
        $number_of_columns = 0;
        $output            = '';

        foreach ($dates as $date):
            $number_of_columns++;
            $occupancydate = $date->format('Y-m-d');
            
            $remaining_rooms = $this->calculateRemainingRoomsForDate($occupancydate);

            $output .= '<td data-roomsremaining="'.esc_attr( $remaining_rooms ).'" class="calendarCell monthHeader occupancy-stats occupancy-percent-' . esc_attr($remaining_rooms) . ' ' . esc_attr($this->todayCSSTag($occupancydate)) . ' ' . esc_attr($this->startOfMonthCSSTag($occupancydate)) . '">';
            $output .= '<div class="occupancyStats-wrap">';
            $output .= '<div class="occupancyStats-inner">';
            $output .= '<div class="occupancy-adr">';
            $output .= __('Rooms<br/>Open', 'staylodgic');
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

    private function displayDate_TableDataBlock($dates = false, $numDays = false)
    {
        // error_log( 'Number of days: ' . $numDays );
        $today             = $this->today;
        $number_of_columns = 0;
        if (!$numDays) {
            $markNumDays = $this->numDays + 1;
        } else {
            $markNumDays = $numDays + 1;
        }

        $output = '';

        foreach ($dates as $date):
            $number_of_columns++;
            $month        = $date->format('M');
            $column_class = '';
            if ($number_of_columns < $markNumDays) {
                $column_class = "rangeSelected";
            }
            $occupancydate = $date->format('Y-m-d');
            if ($occupancydate == $today) {
                $month = 'Today';
            }
            $output .= '<td class="calendarCell monthHeader ' . esc_attr($this->todayCSSTag($occupancydate)) . ' ' . esc_attr($this->startOfMonthCSSTag($occupancydate)) . ' ' . esc_attr($column_class) . '">';
            $output .= '<div class="monthDayinfo-wrap">';
            $output .= '<div class="month">';
            $output .= esc_html($month);
            $output .= '</div>';
            $output .= '<div class="day-letter">';
            $output .= esc_html($date->format('D'));
            $output .= '</div>';
            $output .= '<div class="day">';
            $output .= esc_html($date->format('j'));
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</td>';
        endforeach;
        return $output;
    }

    private function createMasonryTabs( $reservation_id, $checkin, $checkout ) {
        if (!array_key_exists($reservation_id, $this->reservation_tabs)) {

            $newCheckin  = $checkin; // Checkin date of the new value to be added
            $hasConflict = false; // Flag to track if there is a conflict
            // Iterate through the existing array
            foreach ($this->reservation_tabs as $value) {
                $checkoutDate = $value[ 'checkout' ];

                // Compare the new checkin date with existing checkout dates
                if ($newCheckin <= $checkoutDate) {
                    $hasConflict = true;
                    // echo 'has conflict : ' . $newCheckin . ' with ' . $checkoutDate;
                    break; // Stop iterating if a conflict is found
                }
            }

            $givenCheckinDate = $checkin;
            // Filter the array based on the check-in date and reservations has not checkedout
            $filteredArray = array_filter($this->reservation_tabs, function ($value) use ($givenCheckinDate) {
                return $value[ 'checkout' ] > $givenCheckinDate;
            });

            // Extract the room numbers from the filtered array
            $roomNumbers = array_column($filteredArray, 'room');

            // Check for missing room numbers
            $missingNumber = false;
            sort($roomNumbers);

            if (!empty($roomNumbers)) {
                for ($i = 1; $i <= max($roomNumbers); $i++) {
                    if (!in_array($i, $roomNumbers)) {
                        $missingNumber = $i;
                        break;
                    }
                }
            }

            // Output the result
            if ($missingNumber) {
                $room = $missingNumber;
            } else {
                $givenDate   = $checkin;
                $recordCount = 0;

                foreach ($this->reservation_tabs as $value) {
                    $checkoutDate = $value[ 'checkout' ];

                    if ($checkoutDate > $givenDate) {
                        $recordCount++;
                    }
                }

                if ($hasConflict) {
                    //The new checkin date falls within existing checkout dates.";
                    $room = $recordCount + 1;
                } else {
                    //The new checkin date is outside existing checkout dates.";
                    $room = $recordCount - 1;
                }
            }

            if (empty($this->reservation_tabs)) {
                $room = 1;
            }
            if ($room < 0) {
                $room = 1;
            }

            $this->reservation_tabs[ $reservation_id ][ 'room' ]     = $room;
            $this->reservation_tabs[ $reservation_id ][ 'checkin' ]  = $checkin;
            $this->reservation_tabs[ $reservation_id ][ 'checkout' ] = $checkout;
        }

    }

    private function ReservedTab($reservation_data, $current_day, $calendar_start)
    {
        $display = false;
        $tab     = array();
        $row     = 0;
        $room    = 1;
        foreach ($reservation_data as $reservation) {
            $start_date_display    = '';
            $guest_name            = '';
            $reservation_id        = $reservation[ 'id' ];

            if ( ! $this->usingCache ) {
                $reservation_instance  = new \Staylodgic\Reservations($date = false, $room_id = false, $reservation_id = $reservation[ 'id' ]);
                $booking_number        = $reservation_instance->getBookingNumber();
                $guest_name            = $reservation_instance->getReservationGuestName();
                $reserved_days         = $reservation_instance->countReservationDays();
                $checkin               = $reservation_instance->getCheckinDate();
                $checkout              = $reservation_instance->getCheckoutDate();
                $reservation_status    = $reservation_instance->getReservationStatus();
                $reservation_substatus = $reservation_instance->getReservationSubStatus();
                $booking_channel       = $reservation_instance->getReservationChannel();

                $reservation_edit_link = get_edit_post_link($reservation[ 'id' ]);

                $this->calendarData['tabsData'][$reservation_id][$current_day]['getBookingNumber'] = $booking_number;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationGuestName'] = $guest_name;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['countReservationDays'] = $reserved_days;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getCheckinDate'] = $checkin;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getCheckoutDate'] = $checkout;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationStatus'] = $reservation_status;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationSubStatus'] = $reservation_substatus;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationChannel'] = $booking_channel;
                
                $this->calendarData['tabsData'][$reservation_id]['reservation_edit_link'] = $reservation_edit_link;

            } else {
                $booking_number        = $this->cachedData['tabsData'][$reservation_id][$current_day]['getBookingNumber'];
                $guest_name            = $this->cachedData['tabsData'][$reservation_id][$current_day]['getReservationGuestName'];
                $reserved_days         = $this->cachedData['tabsData'][$reservation_id][$current_day]['countReservationDays'];
                $checkin               = $this->cachedData['tabsData'][$reservation_id][$current_day]['getCheckinDate'];
                $checkout              = $this->cachedData['tabsData'][$reservation_id][$current_day]['getCheckoutDate'];
                $reservation_status    = $this->cachedData['tabsData'][$reservation_id][$current_day]['getReservationStatus'];
                $reservation_substatus = $this->cachedData['tabsData'][$reservation_id][$current_day]['getReservationSubStatus'];
                $booking_channel       = $this->cachedData['tabsData'][$reservation_id][$current_day]['getReservationChannel'];                

                $reservation_edit_link = $this->cachedData['tabsData'][$reservation_id]['reservation_edit_link'];                
            }

            $row++;

            if ( 'cancelled' == $reservation_status && $this->availConfirmedOnly ) {
                continue;
            }
            if ( 'pending' == $reservation_status && $this->availConfirmedOnly ) {
                continue;
            }

            $this->createMasonryTabs( $reservation_id, $checkin, $checkout );

            if (array_key_exists($reservation_id, $this->reservation_tabs)) {
                $room = $this->reservation_tabs[ $reservation_id ][ 'room' ];
            }

            $display_info          = $guest_name;
            if ($reservation[ 'start' ] != 'no') {
                $start_date = new \DateTime();
                $start_date->setTimestamp($reservation[ 'checkin' ]);
                $start_date_display = $start_date->format('M j, Y');
                $width              = (80 * ($reserved_days)) - 3;
                $tab[ $room ]       = '<a class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reservation-edit-link" href="' . esc_attr($reservation_edit_link) . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-' . esc_attr($reservation_status) . ' reservation-substatus-' . esc_attr($reservation_substatus) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-guest="' . esc_attr($guest_name) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-bookingnumber="' . esc_attr($booking_number) . '" data-reservationid="' . $reservation[ 'id' ] . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr($reserved_days) . '"><div data-tabwidth="' . esc_attr($width) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html($display_info) . '<span>' . esc_html($booking_channel) . '</span></div></div></div></div></a>';
                $display            = true;
            } else {
                if ($current_day != $checkout) {
                    // Get the checkin day for this as it's in the past of start of the availblablity calendar.
                    // So this tab is happening from the past and needs to be labled athough an extention.
                    $check_in_date_past = new \DateTime();
                    $check_in_date_past->setTimestamp($reservation[ 'checkin' ]);
                    $check_in_date_past = $check_in_date_past->format('Y-m-d');

                    $daysBetween        = \Staylodgic\Common::countDays_BetweenDates($check_in_date_past, $current_day);
                    
                    $width              = (80 * ($reserved_days - $daysBetween)) - 3;
                    
                    if ($check_in_date_past < $calendar_start && $calendar_start == $current_day) {
                        $tab[ $room ] = '<a class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reservation-edit-link" href="' . esc_attr($reservation_edit_link) . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-' . esc_attr($reservation_status) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-guest="' . esc_attr($guest_name) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-bookingnumber="' . esc_attr($booking_number) . '" data-reservationid="' . esc_attr($reservation[ 'id' ]) . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr($reserved_days) . '"><div data-tabwidth="' . esc_attr($width) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html($display_info) . '<span>' . esc_html($booking_channel) . '</span></div></div></div></div></a>';
                    } else {
                        $tab[ $room ] = '<div class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reserved-tab-wrap reserved-extended reservation-' . esc_attr($reservation_status) . ' reservation-substatus-' . esc_attr($reservation_substatus) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-reservationid="' . esc_attr($reservation[ 'id' ]) . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab"></div></div>';
                    }
                    $display = true;
                }
            }

        }

        krsort($tab);
        $tab_array = array();
        $htmltab   = '';

        if ($display) {

            foreach ($tab as $key => $value) {
                $htmltab .= $value;
            }

        }
        $tab_array[ 'tab' ]      = $htmltab;

        return $tab_array;
    }

}

$instance = new \Staylodgic\AvailablityCalendar();