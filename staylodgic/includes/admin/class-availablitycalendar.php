<?php

namespace Staylodgic;

class AvailablityCalendar extends AvailablityCalendarBase
{

    public function __construct($startDate = null, $stay_end_date = null, $cached_data = null, $calendarData = null, $reservation_tabs = null, $usingCache = false, $availConfirmedOnly = false)
    {
        parent::__construct($startDate, $stay_end_date, $calendarData, $reservation_tabs, $availConfirmedOnly);

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
    
    /**
     * Method generateRoomWarnings
     *
     * @param $roomID $roomID
     *
     * @return void
     */
    public function generateRoomWarnings($roomID) {
        $room_output = '';

        $total_rooms = get_post_meta($roomID, 'staylodgic_max_rooms_of_type', true);
        if ('' == $total_rooms) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Max room undefined', 'staylodgic') . '</p></div>';
        }
        if ('0' == $total_rooms) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Max room is zero', 'staylodgic') . '</p></div>';
        }

        $base_rate = get_post_meta($roomID, 'staylodgic_base_rate', true);
        if ('' == $base_rate) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Base rate undefined', 'staylodgic') . '</p></div>';
        }
        if ('0' == $base_rate) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Base rate is zero', 'staylodgic') . '</p></div>';
        }

        $max_guests = get_post_meta($roomID, 'staylodgic_max_guests', true);
        if ('' == $max_guests) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Max guest number undefined', 'staylodgic') . '</p></div>';
        }
        if ('0' == $max_guests) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Max guest number is zero', 'staylodgic') . '</p></div>';
        }

        $bedsetup = get_post_meta($roomID, 'staylodgic_alt_bedsetup', true);
        if (!is_array($bedsetup) || !isset($bedsetup)) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('Beds undefined', 'staylodgic') . '</p></div>';
        }

        $image_id  = get_post_thumbnail_id($roomID);
        if (!$image_id) {
            $room_output .= '<div class="availability-warning"><p class="availability-room-warning-notice"><i class="fa-solid fa-triangle-exclamation"></i> ' . __('No featured image', 'staylodgic') . '</p></div>';
        }

        return $room_output;
    }
    
    /**
     * Method update_availDisplayConfirmedStatus
     *
     * @return void
     */
    public function update_availDisplayConfirmedStatus()
    {

        // Verify the nonce
        if (!isset($_POST['staylodgic_availabilitycalendar_nonce']) || !check_admin_referer('staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            wp_send_json_error(['message' => 'Failed']);
            return;
        }
        // Check if the confirmed_only value is set
        if (isset($_POST['confirmed_only'])) {

            if (0 == $_POST['confirmed_only']) {
                // Update the option based on the switch value
                update_option('staylodgic_availsettings_confirmed_only', 0);
            } else {
                // Update the option based on the switch value
                update_option('staylodgic_availsettings_confirmed_only', 1);
            }

            // Return a success response
            wp_send_json_success();
        } else {
            // Return an error response
            wp_send_json_error('The confirmed_only value is not set.');
        }
    }
    
    /**
     * Method fetchOccupancy_Percentage_For_Calendar_Range
     *
     * @param $startDate $startDate
     * @param $stay_end_date $stay_end_date
     * @param $onlyFullOccupancy $onlyFullOccupancy
     *
     * @return void
     */
    public function fetchOccupancy_Percentage_For_Calendar_Range($startDate = false, $stay_end_date = false, $onlyFullOccupancy = false)
    {
        // Perform necessary security checks or validation here

        // Check if AJAX POST values are set
        $isAjaxRequest = isset($_POST['start']) && isset($_POST['end']);

        // Calculate current date
        $stay_current_date = current_time('Y-m-d');

        // Calculate end date as 90 days from the current date
        $stay_end_date = date('Y-m-d', strtotime($stay_current_date . ' +90 days'));

        if (!$startDate) {
            // Use the current date as the start date
            $startDate = $stay_current_date;
        }

        // Retrieve start and end dates from the AJAX request if not provided
        if ($isAjaxRequest) {
            $startDate = sanitize_text_field($_POST['start']);
            $stay_end_date   = sanitize_text_field($_POST['end']);
        }

        if (isset($startDate) && isset($stay_end_date)) {

            $dates = \Staylodgic\Common::getDates_Between($startDate, $stay_end_date);

            $occupancy_data = array();

            foreach ($dates as $date) :
                $occupancydate       = $date;
                $occupancyPercentage = $this->calculateOccupancyForDate($occupancydate);

                // Check if only full occupancy dates should be included
                if ($onlyFullOccupancy && $occupancyPercentage < 100) {
                    continue; // Skip this date if not full occupancy
                }

                $occupancy_data[$occupancydate] = $occupancyPercentage;
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

    /**
     * Method AvailablityCalendarDisplay Add the Availability menu item to the admin menu    
     *
     * @return void
     */
    public function AvailablityCalendarDisplay()
    {
        // Add the parent menu item
        add_submenu_page(
            'slgc-dashboard',
            __('Availability Calendar', 'staylodgic'),
            __('Availability Calendar', 'staylodgic'),
            'edit_posts',
            'slgc-availability',
            array($this, 'room_Reservation_Plugin_Display_Availability_Calendar'), // Callback for the parent page (can be empty if not needed)
        );
    }

    /**
     * Method room_Reservation_Plugin_Display_Availability_Calendar_Yearly Callback function to display the Availability page   
     *
     * @return void
     */
    public function room_Reservation_Plugin_Display_Availability_Calendar_Yearly()
    {
        // Output the HTML for the Availability page
?>
        <div class="wrap">
            <h1><?php _e('Availability Calendar', 'staylodgic'); ?></h1>
            <?php
            if (!\Staylodgic\Rooms::hasRooms()) {
                echo '<h1>' . __('No Rooms Found', 'staylodgic') . '</h1>';
                return;
            }
            ?>
        </div>
    <?php
    }
    
    /**
     * Method getDisplayConfirmedStatus
     *
     * @return void
     */
    public function getDisplayConfirmedStatus()
    {
        $this->availConfirmedOnly = get_option('staylodgic_availsettings_confirmed_only');

        // Check if the option is not found and set it to '1'
        if ($this->availConfirmedOnly === false) {
            update_option('staylodgic_availsettings_confirmed_only', true);
            $this->availConfirmedOnly = true;
        }

        $confirmed_status = '';

        return $this->availConfirmedOnly;
    }

    /**
     * Method room_Reservation_Plugin_Display_Availability_Calendar Callback function to display the Availability page    
     *
     * @return void
     */
    public function room_Reservation_Plugin_Display_Availability_Calendar()
    {
        // Check if user has sufficient permissions
        if (!current_user_can('edit_posts')) {
            return;
        }

        // Output the HTML for the Availability page
    ?>
        <div class="wrap">
            <?php
            if (!\Staylodgic\Rooms::hasRooms()) {
                echo '<h1>' . __('No Rooms Found', 'staylodgic') . '</h1>';
                return;
            } else {

                echo '<h1>' . __('Availability Calendar', 'staylodgic') . '</h1>';
            }
            echo \Staylodgic\Modals::rateQtyToasts();

            $confirmed_status = '';
            if ($this->getDisplayConfirmedStatus()) {
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
                        $availabilitycalendar = wp_create_nonce('staylodgic-availabilitycalendar-nonce');
                        echo '<input type="hidden" name="staylodgic_availabilitycalendar_nonce" value="' . esc_attr($availabilitycalendar) . '" />';
                        ?>
                    </li>
                    <li class="nav-item">
                        <div data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Next Month" class="calendar-nav-buttons" id="nextmonth"><i class="fa-solid fa-arrow-right"></i></div>
                    </li>
                    <li class="nav-item nav-item-seperator">
                        <div class="calendar-nav-buttons calendar-text-button" id="quantity-modal-link" data-bs-toggle="modal" data-bs-target="#quantity-modal"><i class="fas fa-hashtag"></i><?php _e('Quanity', 'staylodgic'); ?></div>
                    </li>
                    <li class="nav-item">
                        <div class="calendar-nav-buttons calendar-text-button" id="rates-modal-link" data-bs-toggle="modal" data-bs-target="#rates-modal"><i class="fas fa-dollar-sign"></i><?php _e('Rate', 'staylodgic'); ?></div>
                    </li>
                    <li class="nav-item nav-item-seperator">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="calendar-booking-status" <?php echo esc_attr($confirmed_status); ?>>
                            <label class="form-check-label" for="calendar-booking-status"><?php _e('Display Confirmed', 'staylodgic'); ?></label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div id="container">
            <div id="calendar">
                <?php
                $calendar = $this->getAvailabilityCalendar();

                echo $calendar;
                ?>
            </div>
        </div>
<?php
        \Staylodgic\Modals::quanityModal();
        \Staylodgic\Modals::ratesModal();
    }
    
    /**
     * Method get_Selected_Range_AvailabilityCalendar
     *
     * @return void
     */
    public function get_Selected_Range_AvailabilityCalendar()
    {

        // Verify the nonce
        if (!isset($_POST['staylodgic_availabilitycalendar_nonce']) || !check_admin_referer('staylodgic-availabilitycalendar-nonce', 'staylodgic_availabilitycalendar_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            wp_send_json_error(['message' => 'Failed']);
            return;
        }

        // Check if the request has necessary data
        if (!isset($_POST['start_date'], $_POST['end_date'])) {
            wp_die('Missing parameters');
        }

        // Sanitize inputs
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date   = sanitize_text_field($_POST['end_date']);

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
    
    /**
     * Method getAvailabilityCalendar
     *
     * @param $startDate $startDate
     * @param $stay_end_date $stay_end_date
     *
     * @return void
     */
    public function getAvailabilityCalendar($startDate = false, $stay_end_date = false)
    {

        if (!$startDate) {
            $startDate = $this->startDate;
            $stay_end_date   = $this->stay_end_date;
        } else {
            $startDate = new \DateTime($startDate);
            $stay_end_date   = new \DateTime($stay_end_date);
        }

        $dates = $this->getDates($startDate, $stay_end_date);
        $today = $this->today;

        if ($startDate instanceof \DateTime) {
            $startDateString = $startDate->format('Y-m-d');
        } else {
            $startDateString = $startDate;
        }

        if ($stay_end_date instanceof \DateTime) {
            $endDateString = $stay_end_date->format('Y-m-d');
        } else {
            $endDateString = $stay_end_date;
        }

        $table_start = '<table id="calendarTable" data-calstart="' . esc_attr($startDateString) . '" data-calend="' . esc_attr($endDateString) . '">';

        $this->roomlist = \Staylodgic\Rooms::getRoomList();

        $room_output    = '';
        $all_room_output  = '';

        foreach ($this->roomlist as $roomID => $stay_room_name) :

            $cache_instance = new \Staylodgic\Cache($roomID, $startDateString, $endDateString);
            // $cache_instance->deleteCache();
            $transient_key   = $cache_instance->generateRoomCacheKey();
            $cached_calendar = $cache_instance->get_cache($transient_key);

            $room_reservations_instance = new \Staylodgic\Reservations($dateString = false, $roomID);

            // $room_reservations_instance->cleanup_Reservations_Array( $roomID );
            $room_reservations_instance->calculateAndUpdateRemainingRoomCountsForAllDates();

            $use_cache = true;
            $this->usingCache = false;
            $this->cached_data = array();
            $this->calendarData = array();


            if (isset($cached_calendar['qty_rates'])) {
                $cached_qty_rates = $cached_calendar['qty_rates'];

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

                        if ($remaining_rooms !== $cached_qty_rates[$dateString]['qty']) {

                            $use_cache = false;
                            $cache_instance->deleteCache($transient_key);
                            break; // Exit the loop
                        }
                        if ($room_rate !== $cached_qty_rates[$dateString]['rate']) {
                            
                            $use_cache = false;
                            $cache_instance->deleteCache($transient_key);
                            break; // Exit the loop
                        }
                    }
                }
            }

            if ($cache_instance->has_cache($transient_key) && true == $cache_instance->isCacheAllowed() && true == $use_cache) {
                
                if (isset($cached_calendar)) {

                    $this->cached_data = $cached_calendar;

                    $this->usingCache = true;
                }
            }

            $this->reservation_tabs  = array();
            $cache_qty_rate = array();
            $cache_output   = array();

            $room_output = '<tr class="calendarRow calendar-room-row" data-id="' . esc_attr($roomID) . '">';
            $room_output .= '<td class="calendarCell rowHeader">';
            $room_output .= esc_html($stay_room_name);

            $room_output .= $this->generateRoomWarnings($roomID);

            $room_output .= '</td>';

            if (!$this->usingCache) {
                $reservation_instance = new \Staylodgic\Reservations(false, $roomID);
                $reservations = $reservation_instance->getReservationsForRoom($startDateString, $endDateString, false, false, $roomID);
            }

            foreach ($dates as $date) :
                $dateString       = $date->format('Y-m-d');
                $reservation_data = array();

                $create_reservation_tag = true;

                if (!$this->usingCache) {
                    $reservation_instance = new \Staylodgic\Reservations($dateString, $roomID);
                    $reservation_data     = $reservation_instance->buildReservationsDataForRoomForDay($reservations, false, false, false, false);

                    $remaining_rooms = $reservation_instance->remainingRooms_For_Day();

                    if (0 == $remaining_rooms) {
                        $room_was_opened = $reservation_instance->wasRoom_Ever_Opened();
                        if (false === $room_was_opened) {
                            $remaining_rooms = '/';
                        }
                        $create_reservation_tag = false;
                    }

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
                    $reservation_data = $this->cached_data['cellData'][$dateString]['reservation_data'];
                    $remaining_rooms = $this->cached_data['cellData'][$dateString]['remaining_rooms'];
                    $room_rate = $this->cached_data['cellData'][$dateString]['room_rate'];
                    $occupancy_status_class = $this->cached_data['cellData'][$dateString]['occupancy_status_class'];
                    $create_reservation_tag = $this->cached_data['cellData'][$dateString]['create_reservation_tag'];
                }

                $room_output .= '<td class="calendarCell ' . esc_attr($this->startOfMonthCSSTag($dateString)) . ' ' . esc_attr($occupancy_status_class) . '">';

                $room_output .= '<div class="calendar-info-wrap">';
                $room_output .= '<div class="calendar-info">';
                $room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Quantity" href="#" class="quantity-link" data-remaining="' . esc_attr($remaining_rooms) . '" data-date="' . esc_attr($dateString) . '" data-room="' . esc_attr($roomID) . '">' . esc_html($remaining_rooms) . '</a>';

                if (!empty($room_rate) && isset($room_rate) && $room_rate > 0) {
                    $room_output .= '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Rate" class="roomrate-link" href="#" data-rate="' . esc_attr($room_rate) . '" data-date="' . esc_attr($dateString) . '" data-room="' . esc_attr($roomID) . '">' . esc_html($room_rate) . '</a>';
                }

                if (!$this->usingCache) {
                    $cache_qty_rate[$dateString]['qty'] = $remaining_rooms;
                    $cache_qty_rate[$dateString]['rate'] = $room_rate;
                }

                $room_output .= '</div>';

                $room_output .= '</div>';

                if ($create_reservation_tag) {
                    $createEndDate = new \DateTime($dateString);
                    $createEndDate->modify('+1 day');
                    $createOneDayAhead = $createEndDate->format('Y-m-d');
                    $new_post_link = admin_url('post-new.php?post_type=slgc_reservations&createfromdate=' . esc_attr($dateString) . '&createtodate=' . esc_attr($createOneDayAhead) . '&roomID=' . esc_attr($roomID));
                    $room_output .= '<div class="cal-create-reservation"><a data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="New Booking" href="' . esc_url($new_post_link) . '">+</a></div>';
                }

                $room_output .= '<div class="reservation-tab-wrap" data-day="' . esc_attr($dateString) . '">';
                if ($reservation_data) {
                    $reservation_module = array();
                    //echo staylodgic_generate_reserved_tab( $reservation_data, $reservation_tabs );
                    $reservation_module = $this->ReservedTab($reservation_data, $dateString, $startDateString);
                    $room_output .= $reservation_module['tab'];
                    // $this->reservation_tabs = $reservation_module[ 'checkout' ];
                    //print_r( $reservation_tabs );

                }
                $room_output .= '</div>';
                $room_output .= '</td>';
            endforeach;
            $room_output .= '</tr>';

            $this->calendarData['qty_rates'] = $cache_qty_rate;

            $all_room_output .= $room_output;

            if (!$this->usingCache) {

                $cache_instance->setCache($transient_key, $this->calendarData);
            }

        endforeach;

        $stats_row = '<tr class="calendarRow">';
        $stats_row .= self::displayOccupancy_TableDataBlock($startDate, $stay_end_date);
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
    
    /**
     * Method displayOccupancy_TableDataBlock
     *
     * @param $startDate $startDate
     * @param $stay_end_date $stay_end_date
     *
     * @return void
     */
    private function displayOccupancy_TableDataBlock($startDate = false, $stay_end_date = false)
    {
        if (!$startDate) {
            $startDate = $this->startDate;
            $stay_end_date   = $this->stay_end_date;
        }

        if ($startDate instanceof \DateTime) {
            $startDateString = $startDate->format('Y-m-d');
        } else {
            $startDateString = $startDate;
        }

        if ($stay_end_date instanceof \DateTime) {
            $stay_end_date->modify('-5 days');
            $endDateString = $stay_end_date->format('Y-m-d');
        } else {
            $stay_end_date = new \DateTime($stay_end_date);
            $stay_end_date->modify('-5 days');
            $endDateString = $stay_end_date->format('Y-m-d');
        }

        $occupancy_percent = esc_html($this->calculateOccupancyTotalForRange($startDateString, $endDateString));

        $output = '<td class="calendarCell rowHeader">';
        $output .= '<div data-occupancypercent="' . esc_attr($occupancy_percent) . '" class="occupancyStats-wrap occupancy-percentage">';
        $output .= '<div class="occupancyStats-inner">';
        $output .= '<div class="occupancy-total">';
        $output .= '<span class="occupancy-total-stats">';
        $output .= '<span class="occupancy-percent-symbol">';
        $output .= esc_html($occupancy_percent);
        $output .= '%</span>';
        $output .= __('Occupancy', 'staylodgic');
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</td>';
        return $output;
    }
    
    /**
     * Method todayCSSTag
     *
     * @param $occupancydate $occupancydate
     *
     * @return void
     */
    public function todayCSSTag($occupancydate)
    {
        $today_status_class = '';
        if ($occupancydate == $this->today) {
            $today_status_class = "is-today";
        }
        return $today_status_class;
    }
    
    /**
     * Method startOfMonthCSSTag
     *
     * @param $occupancydate $occupancydate
     *
     * @return void
     */
    public function startOfMonthCSSTag($occupancydate)
    {
        $startOfMonth_class = '';

        $yearMonth = substr($occupancydate, 0, 7); // This gives 'YYYY-MM'

        // Create the first day of the month string for the given date
        $firstDayOfOccupancyMonth = $yearMonth . '-01';

        // Compare the provided date with the first day of its month
        if ($occupancydate == $firstDayOfOccupancyMonth) {
            $startOfMonth_class = "start-of-month";
        }

        return $startOfMonth_class;
    }
    
    /**
     * Method displayOccupancyRange_TableDataBlock
     *
     * @param $dates $dates
     *
     * @return void
     */
    private function displayOccupancyRange_TableDataBlock($dates)
    {
        $number_of_columns = 0;
        $output            = '';

        foreach ($dates as $date) :
            $number_of_columns++;
            $occupancydate = $date->format('Y-m-d');

            $remaining_rooms = $this->calculateRemainingRoomsForDate($occupancydate);

            $output .= '<td data-roomsremaining="' . esc_attr($remaining_rooms) . '" class="calendarCell monthHeader occupancy-stats occupancy-percent-' . esc_attr($remaining_rooms) . ' ' . esc_attr($this->todayCSSTag($occupancydate)) . ' ' . esc_attr($this->startOfMonthCSSTag($occupancydate)) . '">';
            $output .= '<div class="occupancyStats-wrap">';
            $output .= '<div class="occupancyStats-inner">';
            $output .= '<div class="occupancy-adr">';
            $output .= __('Rooms<br/>Open', 'staylodgic');
            $output .= '</div>';
            $output .= '<div class="occupancy-percentage">';
            $output .= esc_html($remaining_rooms);
            $output .= '<span></span>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</td>';
        endforeach;

        return $output;
    }
    
    /**
     * Method displayDate_TableDataBlock
     *
     * @param $dates $dates
     * @param $numDays $numDays
     *
     * @return void
     */
    private function displayDate_TableDataBlock($dates = false, $numDays = false)
    {
        
        $today             = $this->today;
        $number_of_columns = 0;
        if (!$numDays) {
            $markNumDays = $this->numDays + 1;
        } else {
            $markNumDays = $numDays + 1;
        }

        $output = '';

        foreach ($dates as $date) :
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
    
    /**
     * Method createMasonryTabs
     *
     * @param $reservation_id $reservation_id
     * @param $checkin $checkin
     * @param $checkout $checkout
     *
     * @return void
     */
    private function createMasonryTabs($reservation_id, $checkin, $checkout)
    {
        if (!array_key_exists($reservation_id, $this->reservation_tabs)) {

            $newCheckin  = $checkin; // Checkin date of the new value to be added
            $hasConflict = false; // Flag to track if there is a conflict
            // Iterate through the existing array
            foreach ($this->reservation_tabs as $value) {
                $stay_checkout_date = $value['checkout'];

                // Compare the new checkin date with existing checkout dates
                if ($newCheckin <= $stay_checkout_date) {
                    $hasConflict = true;
                    break; // Stop iterating if a conflict is found
                }
            }

            $givenCheckinDate = $checkin;
            // Filter the array based on the check-in date and reservations has not checkedout
            $filteredArray = array_filter($this->reservation_tabs, function ($value) use ($givenCheckinDate) {
                return $value['checkout'] > $givenCheckinDate;
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
                    $stay_checkout_date = $value['checkout'];

                    if ($stay_checkout_date > $givenDate) {
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

            $this->reservation_tabs[$reservation_id]['room']     = $room;
            $this->reservation_tabs[$reservation_id]['checkin']  = $checkin;
            $this->reservation_tabs[$reservation_id]['checkout'] = $checkout;
        }
    }
    
    /**
     * Method ReservedTab
     *
     * @param $reservation_data $reservation_data
     * @param $current_day $current_day
     * @param $calendar_start $calendar_start
     *
     * @return void
     */
    private function ReservedTab($reservation_data, $current_day, $calendar_start)
    {
        $display = false;
        $tab     = array();
        $row     = 0;
        $room    = 1;
        foreach ($reservation_data as $reservation) {
            $start_date_display    = '';
            $guest_name            = '';
            $reservation_id        = $reservation['id'];

            if (!$this->usingCache) {
                $reservation_instance  = new \Staylodgic\Reservations($date = false, $room_id = false, $reservation_id = $reservation['id']);
                $booking_number        = $reservation_instance->get_booking_number();
                $guest_name            = $reservation_instance->getReservationGuestName();
                $reserved_days         = $reservation_instance->countReservationDays();
                $checkin               = $reservation_instance->get_checkin_date();
                $checkout              = $reservation_instance->getCheckoutDate();
                $reservation_status    = $reservation_instance->get_reservation_status();
                $reservation_substatus = $reservation_instance->get_reservation_sub_status();
                $booking_channel       = $reservation_instance->getReservationChannel();

                $reservation_edit_link = get_edit_post_link($reservation['id']);

                $this->calendarData['tabsData'][$reservation_id][$current_day]['get_booking_number'] = $booking_number;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationGuestName'] = $guest_name;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['countReservationDays'] = $reserved_days;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['get_checkin_date'] = $checkin;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getCheckoutDate'] = $checkout;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['get_reservation_status'] = $reservation_status;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['get_reservation_sub_status'] = $reservation_substatus;
                $this->calendarData['tabsData'][$reservation_id][$current_day]['getReservationChannel'] = $booking_channel;

                $this->calendarData['tabsData'][$reservation_id]['reservation_edit_link'] = $reservation_edit_link;
            } else {
                $booking_number        = $this->cached_data['tabsData'][$reservation_id][$current_day]['get_booking_number'];
                $guest_name            = $this->cached_data['tabsData'][$reservation_id][$current_day]['getReservationGuestName'];
                $reserved_days         = $this->cached_data['tabsData'][$reservation_id][$current_day]['countReservationDays'];
                $checkin               = $this->cached_data['tabsData'][$reservation_id][$current_day]['get_checkin_date'];
                $checkout              = $this->cached_data['tabsData'][$reservation_id][$current_day]['getCheckoutDate'];
                $reservation_status    = $this->cached_data['tabsData'][$reservation_id][$current_day]['get_reservation_status'];
                $reservation_substatus = $this->cached_data['tabsData'][$reservation_id][$current_day]['get_reservation_sub_status'];
                $booking_channel       = $this->cached_data['tabsData'][$reservation_id][$current_day]['getReservationChannel'];

                $reservation_edit_link = $this->cached_data['tabsData'][$reservation_id]['reservation_edit_link'];
            }

            $row++;

            if ('cancelled' == $reservation_status && $this->availConfirmedOnly) {
                continue;
            }
            if ('pending' == $reservation_status && $this->availConfirmedOnly) {
                continue;
            }

            $this->createMasonryTabs($reservation_id, $checkin, $checkout);

            if (array_key_exists($reservation_id, $this->reservation_tabs)) {
                $room = $this->reservation_tabs[$reservation_id]['room'];
            }

            $display_info          = $guest_name;
            if ($reservation['start'] != 'no') {
                $start_date = new \DateTime();
                $start_date->setTimestamp($reservation['checkin']);
                $start_date_display = $start_date->format('M j, Y');
                $width              = (80 * ($reserved_days)) - 3;
                $tab[$room]       = '<a class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reservation-edit-link" href="' . esc_attr($reservation_edit_link) . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-' . esc_attr($reservation_status) . ' reservation-substatus-' . esc_attr($reservation_substatus) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-guest="' . esc_attr($guest_name) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-bookingnumber="' . esc_attr($booking_number) . '" data-reservationid="' . $reservation['id'] . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr($reserved_days) . '"><div data-tabwidth="' . esc_attr($width) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html($display_info) . '<span>' . esc_html($booking_channel) . '</span></div></div></div></div></a>';
                $display            = true;
            } else {
                if ($current_day != $checkout) {
                    // Get the checkin day for this as it's in the past of start of the availblablity calendar.
                    // So this tab is happening from the past and needs to be labled athough an extention.
                    $check_in_date_past = new \DateTime();
                    $check_in_date_past->setTimestamp($reservation['checkin']);
                    $check_in_date_past = $check_in_date_past->format('Y-m-d');

                    $daysBetween        = \Staylodgic\Common::countDays_BetweenDates($check_in_date_past, $current_day);

                    $width              = (80 * ($reserved_days - $daysBetween)) - 3;

                    if ($check_in_date_past < $calendar_start && $calendar_start == $current_day) {
                        $tab[$room] = '<a class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reservation-edit-link" href="' . esc_attr($reservation_edit_link) . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-' . esc_attr($reservation_status) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-guest="' . esc_attr($guest_name) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-bookingnumber="' . esc_attr($booking_number) . '" data-reservationid="' . esc_attr($reservation['id']) . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab reserved-tab-days-' . esc_attr($reserved_days) . '"><div data-tabwidth="' . esc_attr($width) . '" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">' . esc_html($display_info) . '<span>' . esc_html($booking_channel) . '</span></div></div></div></div></a>';
                    } else {
                        $tab[$room] = '<div class="reservation-tab-is-' . esc_attr($reservation_status) . ' ' . esc_attr($reservation_substatus) . ' reservation-tab-id-' . esc_attr($reservation_id) . ' reserved-tab-wrap reserved-extended reservation-' . esc_attr($reservation_status) . ' reservation-substatus-' . esc_attr($reservation_substatus) . '" data-reservationstatus="' . esc_attr($reservation_status) . '" data-room="' . esc_attr($room) . '" data-row="' . esc_attr($row) . '" data-reservationid="' . esc_attr($reservation['id']) . '" data-checkin="' . esc_attr($checkin) . '" data-checkout="' . esc_attr($checkout) . '"><div class="reserved-tab"></div></div>';
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
        $tab_array['tab']      = $htmltab;

        return $tab_array;
    }
}

$instance = new \Staylodgic\AvailablityCalendar();
