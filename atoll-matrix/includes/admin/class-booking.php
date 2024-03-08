<?php

namespace AtollMatrix;

class Booking
{

    protected $checkinDate;
    protected $checkoutDate;
    protected $staynights;
    protected $adultGuests;
    protected $childrenGuests;
    protected $totalGuests;
    protected $totalChargeableGuests;
    protected $roomArray;
    protected $ratesArray;
    protected $canAccomodate;
    protected $bookingNumber;
    protected $children_age;
    protected $bookingSearchResults;
    protected $discountLabel;

    public function __construct(
        $bookingNumber = null,
        $checkinDate = null,
        $checkoutDate = null,
        $staynights = null,
        $adultGuests = null,
        $childrenGuests = null,
        $children_age = null,
        $totalGuests = null,
        $totalChargeableGuests = null,
        $roomArray = null,
        $ratesArray = null,
        $canAccomodate = null,
        $bookingSearchResults = null,
        $discountLabel = null
    ) {
        $this->checkinDate           = $checkinDate;
        $this->checkoutDate          = $checkoutDate;
        $this->staynights            = $staynights;
        $this->adultGuests           = $adultGuests;
        $this->childrenGuests        = $childrenGuests;
        $this->totalGuests           = $totalGuests;
        $this->totalChargeableGuests = $totalChargeableGuests;
        $this->children_age          = $children_age;
        $this->roomArray             = $roomArray;
        $this->ratesArray            = $ratesArray;
        $this->canAccomodate         = $canAccomodate;
        $this->bookingSearchResults  = $bookingSearchResults;
        $this->discountLabel         = $discountLabel;
        $this->bookingNumber         = uniqid();

        add_shortcode('hotel_booking_search', array($this, 'hotelBooking_SearchForm'));
        add_shortcode('hotel_booking_details', array($this, 'hotelBooking_Details'));
        // AJAX handler to save room metadata

        add_action('wp_ajax_booking_BookingSearch', array($this, 'booking_BookingSearch'));
        add_action('wp_ajax_nopriv_booking_BookingSearch', array($this, 'booking_BookingSearch'));

        add_action('wp_ajax_process_RoomPrice', array($this, 'process_RoomPrice')); // For logged-in users
        add_action('wp_ajax_nopriv_process_RoomPrice', array($this, 'process_RoomPrice')); // For non-logged-in users

        add_action('wp_ajax_process_SelectedRoom', array($this, 'process_SelectedRoom')); // For logged-in users
        add_action('wp_ajax_nopriv_process_SelectedRoom', array($this, 'process_SelectedRoom')); // For non-logged-in users

        add_action('wp_ajax_generate_BedMetabox', array($this, 'generate_BedMetabox_callback'), 10, 3); // For logged-in users

        add_action('wp_ajax_bookRooms', array($this, 'bookRooms'));
        add_action('wp_ajax_nopriv_bookRooms', array($this, 'bookRooms'));
        
    }

    public function process_RoomData(
        $bookingnumber = null,
        $room_id = null,
        $room_price = null,
        $bed_layout = null,
        $meal_plan = null,
        $meal_plan_price = null
    ) {
        // Get the data sent via AJAX

        $roomName = \AtollMatrix\Rooms::getRoomName_FromID($room_id);

        $booking_results = atollmatrix_get_booking_transient($bookingnumber);

        // Perform any processing you need with the data
        // For example, you can save it to the database or perform calculations

        // Return a response (you can modify this as needed)
        $response = array(
            'success' => true,
            'message' => 'Data: ' . $roomName . ',received successfully.',
        );

        if (is_array($booking_results)) {

            error_log('====== From Transient ======');
            error_log(print_r($booking_results, true));

            $booking_results[ 'choice' ][ 'room_id' ]   = $room_id;
            $booking_results[ 'choice' ][ 'bedlayout' ] = $bed_layout;
            $booking_results[ 'choice' ][ 'mealplan' ]  = $meal_plan;

            $booking_results[ 'choice' ][ 'mealplan_price' ] = 0;
            if ('none' !== $meal_plan) {
                $booking_results[ 'choice' ][ 'mealplan_price' ] = $booking_results[ $room_id ][ 'meal_plan' ][ $booking_results[ 'choice' ][ 'mealplan' ] ];
            }

            $booking_results[ 'choice' ][ 'room_id' ] = $room_id;

            atollmatrix_set_booking_transient($booking_results, $bookingnumber);

            error_log('====== Saved Transient ======');
            error_log(print_r($booking_results, true));

            error_log('====== Specific Room ======');
            error_log(print_r($booking_results[ $room_id ], true));

        } else {
            $booking_results = false;
        }

        // Send the JSON response
        return $booking_results;
    }

    public function process_SelectedRoom()
    {

        $bookingnumber   = sanitize_text_field($_POST[ 'bookingnumber' ]);
        $room_id         = sanitize_text_field($_POST[ 'room_id' ]);
        $room_price      = sanitize_text_field($_POST[ 'room_price' ]);
        $bed_layout      = sanitize_text_field($_POST[ 'bed_layout' ]);
        $meal_plan       = sanitize_text_field($_POST[ 'meal_plan' ]);
        $meal_plan_price = sanitize_text_field($_POST[ 'meal_plan_price' ]);

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_roomlistingbox_nonce' ]) || !check_admin_referer('atollmatrix-roomlistingbox-nonce', 'atollmatrix_roomlistingbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        $booking_results = self::process_RoomData(
            $bookingnumber,
            $room_id,
            $room_price,
            $bed_layout,
            $meal_plan,
            $meal_plan_price
        );

        if (is_array($booking_results)) {

            $html = self::bookingSummary(
                $bookingnumber,
                $booking_results[ 'choice' ][ 'room_id' ],
                $booking_results[ $room_id ][ 'roomtitle' ],
                $booking_results[ 'checkin' ],
                $booking_results[ 'checkout' ],
                $booking_results[ 'staynights' ],
                $booking_results[ 'adults' ],
                $booking_results[ 'children' ],
                $booking_results[ 'choice' ][ 'bedlayout' ],
                $booking_results[ 'choice' ][ 'mealplan' ],
                $booking_results[ 'choice' ][ 'mealplan_price' ],
                $booking_results[ $room_id ][ 'staydate' ],
                $booking_results[ $room_id ][ 'totalroomrate' ]
            );

        } else {
            $html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>Session timed out. Please reload the page.</div>';
        }

        // Send the JSON response
        wp_send_json($html);
    }

    public function process_RoomPrice()
    {

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_searchbox_nonce' ]) || !check_admin_referer('atollmatrix-searchbox-nonce', 'atollmatrix_searchbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        $bookingnumber   = sanitize_text_field($_POST[ 'booking_number' ]);
        $room_id         = sanitize_text_field($_POST[ 'room_id' ]);
        $room_price      = sanitize_text_field($_POST[ 'room_price' ]);
        $bed_layout      = sanitize_text_field($_POST[ 'bed_layout' ]);
        $meal_plan       = sanitize_text_field($_POST[ 'meal_plan' ]);
        $meal_plan_price = sanitize_text_field($_POST[ 'meal_plan_price' ]);

        $booking_results = self::process_RoomData(
            $bookingnumber,
            $room_id,
            $room_price,
            $bed_layout,
            $meal_plan,
            $meal_plan_price
        );

        if (is_array($booking_results)) {

            $html = self::getSelectedPlanPrice($room_id, $booking_results);

        } else {
            $html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>Session timed out. Please reload the page.</div>';
        }

        // Send the JSON response
        wp_send_json($html);
    }

    public function getSelectedPlanPrice($room_id, $booking_results)
    {
        $total_price_tag = atollmatrix_price(intval($booking_results[ $room_id ][ 'totalroomrate' ]) + intval($booking_results[ 'choice' ][ 'mealplan_price' ]));
        return $total_price_tag;
    }

    public function bookingSummary(
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

        $totalguests = intval($adults) + intval($children);
        $totalprice  = array();

        $html = '<div id="booking-summary-wrap">';
        if ('' !== $room_name) {
            $html .= '<div class="room-summary"><span class="summary-room-name">' . esc_html( $room_name ) . '</span></div>';
        }

        $html .= '<div class="main-summary-wrap">';
        if ($adults > 0) {
            for ($displayAdultCount = 0; $displayAdultCount < $adults; $displayAdultCount++) {
                $html .= '<span class="guest-adult-svg"></span>';
            }
        }
        if ($children > 0) {
            for ($displayChildrenCount = 0; $displayChildrenCount < $children; $displayChildrenCount++) {
                $html .= '<span class="guest-child-svg"></span>';
            }
        }
        if ('' !== $bedtype) {
            $html .= '<div class="bed-summary">' . self::get_AllBedLayouts($bedtype) . '</div>';
        }
        $html .= '</div>';

        if ('' !== $room_id) {
            $html .= '<div class="meal-summary-wrap">';
            if ('' !== self::generate_MealPlanIncluded($room_id)) {
                $html .= '<div class="meal-summary"><span class="summary-mealtype-inlcuded">' . self::generate_MealPlanIncluded($room_id) . '</span></div>';
            }
            if ('none' !== $mealtype) {
                $html .= '<div class="summary-icon mealplan-summary-icon"><i class="fa-solid fa-utensils"></i></div>';
                $html .= '<div class="summary-heading mealplan-summary-heading">'. __('Mealplan','atollmatrix') . ':</div>';
                $html .= '<div class="meal-summary"><span class="summary-mealtype-name">' . atollmatrix_get_mealplan_labels($mealtype) . '</span></div>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="stay-summary-wrap">';

        $html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
        $html .= '<div class="summary-heading checkin-summary-heading">'. __('Check-in:','atollmatrix') . '</div>';
        $html .= '<div class="checkin-summary">' . esc_html( $checkin ) . '</div>';
        $html .= '<div class="summary-heading checkout-summary-heading">Check-out:</div>';
        $html .= '<div class="checkout-summary">' . esc_html( $checkout ) . '</div>';

        $html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
        $html .= '<div class="summary-heading staynight-summary-heading">'. __('Nights:','atollmatrix') . '</div>';
        $html .= '<div class="staynight-summary">' . esc_html( $staynights ) . '</div>';
        $html .= '</div>';

        if ('' !== $totalroomrate) {
            $subtotalprice = intval($totalroomrate) + intval($mealprice);
            $html .= '<div class="price-summary-wrap">';

            if (atollmatrix_has_tax()) {
                $html .= '<div class="summary-heading total-summary-heading">'. __('Subtotal:','atollmatrix') . '</div>';
                $html .= '<div class="price-summary">' . atollmatrix_price($subtotalprice) . '</div>';
            }

            $html .= '<div class="summary-heading total-summary-heading">'. __('Total:','atollmatrix') . '</div>';

            $tax_instance = new \AtollMatrix\Tax('room');
            $totalprice = $tax_instance->apply_tax($subtotalprice, $staynights, $totalguests, $output = 'html');
            foreach ($totalprice[ 'details' ] as $totalID => $totalvalue) {
                $html .= '<div class="tax-summary tax-summary-details">' . wp_kses( $totalvalue, atollmatrix_get_allowed_tags() ) . '</div>';
            }

            $html .= '<div class="tax-summary tax-summary-total">' . atollmatrix_price($totalprice[ 'total' ]) . '</div>';
            $html .= '</div>';
        }

        if ('' !== $room_id) {
            $html .= '<div class="form-group">';
            $html .= '<div id="bookingResponse" class="booking-response"></div>';
            $html .= '<div id="booking-register" class="book-button">'. __('Book this room','atollmatrix') . '</div>';
            // $html .= self::paymentHelperButton($totalprice[ 'total' ], $bookingnumber);
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function hotelBooking_SearchForm()
    {
        // Generate unique booking number
        atollmatrix_set_booking_transient('1', $this->bookingNumber);
        ob_start();
        $searchbox_nonce       = wp_create_nonce('atollmatrix-searchbox-nonce');
        $availabilityDateArray = array();

        // Calculate current date
        $currentDate = current_time('Y-m-d');
        // Calculate end date as 3 months from the current date
        $endDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));

        $fullybooked_dates = array();
        $display_fullbooked_status = false;
        if ( true === $display_fullbooked_status ) {
            $reservations_instance = new \AtollMatrix\Reservations();
            $fullybooked_dates     = $reservations_instance->daysFullyBooked_For_DateRange($currentDate, $endDate);    
        }
        
        // error_log( '-------------------- availability percent check');
        // error_log( print_r( $fullybooked_dates, true ));
        // error_log( '-------------------- availability percent check');
        ?>
		<div class="atollmatrix-content">
            <div id="hotel-booking-form">
                <div class="front-booking-search">
                    <div class="front-booking-calendar-wrap">
                        <div class="front-booking-calendar-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div class="front-booking-calendar-date"><?php _e('Choose stay dates','atollmatrix'); ?></div>
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
                        <div id="bookingSearch" class="form-search-button"><?php _e('Search','atollmatrix'); ?></div>
                    </div>
                </div>


				<div class="atollmatrix_reservation_datepicker">
					<input type="hidden" name="atollmatrix_searchbox_nonce" value="<?php echo esc_attr($searchbox_nonce); ?>" />
					<input data-booked="<?php echo htmlspecialchars(json_encode($fullybooked_dates), ENT_QUOTES, 'UTF-8'); ?>" type="date" id="reservation-date" name="reservation_date">
				</div>
                <div class="atollmatrix_reservation_room_guests_wrap">
                    <div id="atollmatrix_reservation_room_adults_wrap" class="number-input occupant-adult occupants-range">
                        <div class="column-one">
                            <label for="number-of-adults"><?php _e('Adults','atollmatrix'); ?></label>
                        </div>
                        <div class="column-two">
                            <span class="minus-btn">-</span>
                            <input data-guest="adult" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-adults" value="2" name="number_of_adults" type="text" class="number-value" readonly="">
                            <span class="plus-btn">+</span>
                        </div>
                    </div>
                    <div id="atollmatrix_reservation_room_children_wrap" class="number-input occupant-child occupants-range">
                        <div class="column-one">
                            <label for="number-of-adults"><?php _e('Children','atollmatrix'); ?></label>
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
					<div class="recommended-alt-title"><?php _e('Rooms unavailable','atollmatrix'); ?></div><div class="recommended-alt-description"><?php _e('Following range from your selection is avaiable.','atollmatrix'); ?></div>
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

    public function hotelBooking_Details()
    {
        ob_start();
        $atollmatrix_bookingdetails_nonce = wp_create_nonce('atollmatrix-bookingdetails-nonce');
        ?>
		<div class="atollmatrix-content">
            <div id="hotel-booking-form">

                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="atollmatrix_bookingdetails_nonce" value="<?php echo esc_attr($atollmatrix_bookingdetails_nonce); ?>" />
                                <input placeholder="<?php _e('Booking No.','atollmatrix'); ?>" type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label"><?php _e('Booking No.','atollmatrix'); ?></label>
                            </div>
                        </div>
                        <div data-request="bookingdetails" id="bookingDetails" class="form-search-button"><?php _e('Search','atollmatrix'); ?></div>
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

    public function alternative_BookingDates($checkinDate, $checkoutDate)
    {

        // Perform the greedy search by adjusting the check-in and check-out dates
        $newCheckinDate  = new \DateTime($checkinDate);
        $newCheckoutDate = new \DateTime($checkoutDate);
        //$newCheckoutDate->add(new \DateInterval('P1D'));

        $reservation_instance = new \AtollMatrix\Reservations();

        $availableRoomDates = $reservation_instance->Availability_of_Rooms_For_DateRange($newCheckinDate->format('Y-m-d'), $newCheckoutDate->format('Y-m-d'));

        // error_log('---- Alternative Rooms Matrix Early');
        // error_log(print_r($availableRoomDates, true));
        $new_room_availability_array = array();

        // Process each sub-array
        foreach ($availableRoomDates as $roomId => $subArray) {
            // Initialize the new sub-array for the current room
            $newSubArray = array();

            // Get the first and last keys of the inner arrays
            foreach ($subArray as $innerArray) {
                $keys     = array_keys($innerArray);
                $firstKey = $keys[ 0 ];
                $lastKey  = end($keys);

                // Keep only the first and last records and assign unique indexes
                $newSubArray[ $firstKey ] = array(
                    'check-in'  => $firstKey,
                    'check-out' => $lastKey,
                );
            }

            // Add the new sub-array to the new room availability array
            $new_room_availability_array[ $roomId ] = $newSubArray;
        }
        $roomAvailabityArray = $new_room_availability_array;

        // error_log('---- Alternative Room Availability Matrix Before');
        // error_log(print_r($roomAvailabityArray, true));

        // Initialize an empty string
        $output = '';

        $processedDates    = array(); // Array to store processed check-in and checkout dates
        $newProcessedDates = array();

        foreach ($roomAvailabityArray as $key => $subset) {
            // Output the key of the subset
            // error_log("Subset Key: $key\n");

            // Iterate through each sub array in the subset
            foreach ($subset as $subArray) {
                // Output the sub array
                // error_log(print_r($subArray, true));
                $checkInAlt = $subArray[ 'check-in' ];
                $staylast   = $subArray[ 'check-out' ];

                // Check if the current check-in and checkout dates have already been processed
                if (in_array([ $checkInAlt, $staylast ], $processedDates)) {
                    //error_log( 'Skipping .... ' . $checkInAlt, $staylast);
                    continue; // Skip processing identical dates
                }

                // Add the current check-in and checkout dates to the processed dates array
                $processedDates[  ] = [ $checkInAlt, $staylast ];

                // Get the date one day after the staylast
                $checkOutAlt = $staylast;

                $newProcessedDates[ $checkInAlt ] = array(
                    'staylast'  => $staylast,
                    'check-in'  => $checkInAlt,
                    'check-out' => $checkOutAlt,
                );

                // Perform operations with the sub array...
            }
        }

        // error_log('---- Alternative Room Availability Matrix The Final');
        // error_log(print_r($newProcessedDates, true));
        ksort($newProcessedDates);

        foreach ($newProcessedDates as $key) {
            $staylast    = $key[ 'staylast' ];
            $checkInAlt  = $key[ 'check-in' ];
            $checkOutAlt = $key[ 'check-out' ];
        
            // Format the dates as "Month Day" (e.g., "July 13th")
            $formattedFirstDate = date('F jS', strtotime($checkInAlt));
        
            // Add one day to the checkout date
            $checkoutPlusOne = date('Y-m-d', strtotime($checkOutAlt . ' +1 day'));
        
            // Format the next day
            $formattedNextDay = date('F jS', strtotime($checkoutPlusOne));
            if (date('F', strtotime($staylast)) !== date('F', strtotime($checkInAlt))) {
                $formattedNextDay = date('F jS', strtotime($checkoutPlusOne));
            } else {
                $formattedNextDay = date('jS', strtotime($checkoutPlusOne));
            }
        
            $output .= "<span data-check-staylast='".esc_attr($staylast)."' data-check-in='".esc_attr($checkInAlt)."' data-check-out='".esc_attr($checkoutPlusOne)."'>".esc_attr($formattedFirstDate)." - ".esc_attr($formattedNextDay)."</span>";
        }
        
        // Remove the trailing comma and space
        $output = rtrim($output, ', ');

        // Print the output
        $roomAvailabity = '<div class="recommended-dates-wrap">' . $output . '</div>';
        // error_log('---- Alternative Room Availability Matrix for Range');
        // error_log(print_r($roomAvailabity, true));

        return $roomAvailabity;
    }

    public function booking_BookingSearch()
    {
        $room_type          = '';
        $number_of_children = 0;
        $number_of_adults   = 0;
        $number_of_guests   = 0;
        $children_age       = array();
        $reservation_date   = '';

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_searchbox_nonce' ]) || !check_admin_referer('atollmatrix-searchbox-nonce', 'atollmatrix_searchbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        error_log('----booking search info');
        error_log( print_r( $_POST,1 ));

        if (isset($_POST[ 'reservation_date' ])) {
            $reservation_date = $_POST[ 'reservation_date' ];
        }

        if (isset($_POST[ 'number_of_adults' ])) {
            $number_of_adults = $_POST[ 'number_of_adults' ];
        }

        if (isset($_POST[ 'number_of_children' ])) {
            $number_of_children = $_POST[ 'number_of_children' ];
        }

        // // *********** To be removed
        // $reservation_date = '2023-09-27 to 2023-09-30';
        // $number_of_adults = 1;
        // // ***********

        $freeStayAgeUnder = atollmatrix_get_option('childfreestay');

        // error_log('---- Free Stay');
        // error_log(print_r($freeStayAgeUnder, true));

        $freeStayChildCount = 0;

        if (isset($_POST[ 'children_age' ])) {
            // Loop through all the select elements with the class 'children-age-selector'
            foreach ($_POST[ 'children_age' ] as $selected_age) {
                // Sanitize and store the selected values in an array
                $children_age[  ] = sanitize_text_field($selected_age);
                if ($selected_age < $freeStayAgeUnder) {
                    $freeStayChildCount = $freeStayChildCount + 1;
                }
            }
        }

        $this->children_age = $children_age;

        // error_log('---- Children Age');
        // error_log(print_r($this->children_age, true));

        $number_of_guests = intval($number_of_adults) + intval($number_of_children);

        $this->adultGuests           = $number_of_adults;
        $this->childrenGuests        = $number_of_children;
        $this->totalGuests           = $number_of_guests;
        $this->totalChargeableGuests = $number_of_guests - $freeStayChildCount;

        if (isset($_POST[ 'room_type' ])) {
            $room_type = $_POST[ 'room_type' ];
        }

        $chosenDate = \AtollMatrix\Common::splitDateRange($reservation_date);

        $checkinDate  = '';
        $checkoutDate = '';

        if (isset($chosenDate[ 'startDate' ])) {
            $checkinDate     = $chosenDate[ 'startDate' ];
            $checkinDate_obj = new \DateTime($chosenDate[ 'startDate' ]);
        }
        if (isset($chosenDate[ 'endDate' ])) {
            $checkoutDate     = $chosenDate[ 'endDate' ];
            $checkoutDate_obj = new \DateTime($checkoutDate);

            $realCheckoutDate     = date('Y-m-d', strtotime($checkoutDate . ' +1 day'));
            $realCheckoutDate_obj = new \DateTime($realCheckoutDate);
        }

        // Calculate the number of nights
        $staynights = $checkinDate_obj->diff($realCheckoutDate_obj)->days;

        $this->checkinDate  = $checkinDate;
        $this->checkoutDate = $realCheckoutDate;
        $this->staynights   = $staynights;

        $this->bookingSearchResults                       = array();
        $this->bookingSearchResults[ 'bookingnumber' ]    = $this->bookingNumber;
        $this->bookingSearchResults[ 'checkin' ]          = $this->checkinDate;
        $this->bookingSearchResults[ 'checkout' ]         = $this->checkoutDate;
        $this->bookingSearchResults[ 'staynights' ]       = $this->staynights;
        $this->bookingSearchResults[ 'adults' ]           = $this->adultGuests;
        $this->bookingSearchResults[ 'children' ]         = $this->childrenGuests;
        $this->bookingSearchResults[ 'children_age' ]     = $this->children_age;
        $this->bookingSearchResults[ 'totalguest' ]       = $this->totalGuests;
        $this->bookingSearchResults[ 'chargeableguests' ] = $this->totalChargeableGuests;

        // Perform your query here, this is just an example
        //$result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_adults, Number of Children: $number_of_children";
        // error_log(print_r($result, true));
        $room_instance = new \AtollMatrix\Rooms();

        // Get a combined array of rooms and rates which are available for the dates.
        $combo_array = $room_instance->getAvailable_Rooms_Rates_Occupants_For_DateRange($this->checkinDate, $checkoutDate);

        $this->roomArray     = $combo_array[ 'rooms' ];
        $this->ratesArray    = $combo_array[ 'rates' ];
        $this->canAccomodate = $combo_array[ 'occupants' ];

        // error_log('Value of $combo_array["rooms"]:');
        // error_log(print_r($combo_array['rooms'], true));

        $availableRoomDates = array();

        $roomAvailability = false;

        if (count($combo_array[ 'rooms' ]) == 0) {

            $roomAvailability = self::alternative_BookingDates($checkinDate, $checkoutDate);
        }

        //set_transient($bookingNumber, $combo_array, 20 * MINUTE_IN_SECONDS);
        // error_log(print_r($combo_array, true));
        // error_log("Rooms array");
        // error_log(print_r($roomArray, true));
        // error_log("Date Range from picker");
        // error_log(print_r($checkinDate, true));
        // error_log(print_r($checkoutDate, true));

        // Always die in functions echoing AJAX content
        $list = self::list_Rooms_And_Quantities();

        ob_start();
        echo '<form action="" method="post" id="hotel-room-listing" class="needs-validation" novalidate>';
        $roomlistingbox = wp_create_nonce('atollmatrix-roomlistingbox-nonce');
        echo '<input type="hidden" name="atollmatrix_roomlistingbox_nonce" value="' . esc_attr($roomlistingbox) . '" />';
        echo '<div id="reservation-data" data-bookingnumber="' . esc_attr($this->bookingNumber) . '" data-children="' . esc_attr($this->childrenGuests) . '" data-adults="' . esc_attr($this->adultGuests) . '" data-guests="' . esc_attr($this->totalGuests) . '" data-checkin="' . esc_attr($this->checkinDate) . '" data-checkout="' . esc_attr($this->checkoutDate) . '">';
        echo $list;
        echo '</div>';
        echo self::register_Guest_Form();
        echo '</form>';
        $output                       = ob_get_clean();
        $response[ 'booking_data' ]   = $combo_array;
        $response[ 'roomlist' ]       = $output;
        $response[ 'alt_recommends' ] = $roomAvailability;
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        die();
    }

    public function list_Rooms_And_Quantities()
    {
        // error_log('====== rates ');
        // error_log(print_r($combo_array, true));
        // Initialize empty string to hold HTML
        $html = '';

        $html .= self::listRooms();

        // Return the resulting HTML string
        return $html;
    }

    public function can_ThisRoom_Accomodate($room_id)
    {

        $status = true;
        if ($this->canAccomodate[ $room_id ][ 'guests' ] < $this->totalGuests) {
            // error_log('Cannot accomodate number of guests');
            $status = false;
        }

        if ($this->canAccomodate[ $room_id ][ 'adults' ] < $this->adultGuests) {
            // error_log('Cannot accomodate number of adults');
            $status = false;
        }
        if ($this->canAccomodate[ $room_id ][ 'children' ] < $this->childrenGuests) {
            // error_log('Cannot accomodate number of children');
            $status = false;
        }

        return $status;
    }

    public function listRooms()
    {

        $html  = '';
        $count = 0;
        // Iterate through each room
        foreach ($this->roomArray as $id => $room_info) {

            $room_data = get_post_custom($id);

            // Get quantity and room title
            // error_log('====== can accomodate ');
            // error_log(print_r($this->canAccomodate, true));

            $can_ThisRoom_Accomodate = self::can_ThisRoom_Accomodate($id);
            if (!$can_ThisRoom_Accomodate) {
                continue;
            }

            $max_guest_number       = intval($this->canAccomodate[ $id ][ 'guests' ]);
            $max_child_guest_number = intval($this->canAccomodate[ $id ][ 'guests' ] - 1);
            // Append a div for the room with the room ID as a data attribute
            $html .= '<div class="room-occupied-group" data-adults="' . esc_attr($this->canAccomodate[ $id ][ 'adults' ]) . '" data-children="' . esc_attr($this->canAccomodate[ $id ][ 'children' ]) . '" data-guests="' . esc_attr($this->canAccomodate[ $id ][ 'guests' ]) . '" data-room-id="' . esc_attr($id) . '">';
            $html .= '<div class="room-details">';

            foreach ($room_info as $quantity => $title) {

                $html .= '<div class="room-details-row">';
                $html .= '<div class="room-details-column">';

                $html .= '<div class="room-details-image">';
                $image_id  = get_post_thumbnail_id($id);
                $image_url = wp_get_attachment_image_url($image_id, 'atollmatrix-large-square'); // Get the URL of the custom-sized image
                $html .= '<img class="lightbox-trigger room-summary-image" data-image="' . esc_url($image_url) . '" src="' . esc_url($image_url) . '" alt="Room featured image">';
                $html .= '</div>';

                $html .= '<div class="room-details-stats">';

                if (isset($room_data[ "atollmatrix_roomview" ][ 0 ])) {
                    $roomview       = $room_data[ "atollmatrix_roomview" ][ 0 ];
                    $roomview_array = atollmatrix_get_room_views();
                    if (array_key_exists($roomview, $roomview_array)) {
                        $html .= '<div class="room-summary-roomview"><span class="room-summary-icon"><i class="fa-regular fa-eye"></i></span>' . esc_html($roomview_array[ $roomview ]) . '</div>';
                    }
                }

                if (isset($room_data[ "atollmatrix_room_size" ][ 0 ])) {
                    $roomsize = $room_data[ "atollmatrix_room_size" ][ 0 ];
                    $html .= '<div class="room-summary-roomsize"><span class="room-summary-icon"><i class="fa-solid fa-vector-square"></i></span>' . esc_html($roomsize) . ' ft²</div>';
                }
                $html .= '</div>';

                $html .= '<div class="room-details-heading">';
                // Append the room title

                $this->bookingSearchResults[ $id ][ 'roomtitle' ] = $title;

                $html .= '<h2>' . esc_html($title) . '</h2>';

                $html .= '</div>';

                if (isset($room_data[ "atollmatrix_room_desc" ][ 0 ])) {
                    $room_desc = $room_data[ "atollmatrix_room_desc" ][ 0 ];
                    $html .= '<div class="room-summary-roomdesc">' . esc_html($room_desc) . '</div>';
                }

                $html .= '<div class="room-details-facilities">';
                if (isset($room_data[ "atollmatrix_room_facilities" ][ 0 ])) {
                    $room_facilities = $room_data[ "atollmatrix_room_facilities" ][ 0 ];
                    $html .= atollmatrix_string_to_html_spans($room_facilities, $class = 'room-summary-facilities');
                }
                $html .= '</div>';

                $html .= '</div>';
                $html .= '<div class="room-details-column">';

                if ($this->adultGuests > 0) {
                    for ($displayAdultCount = 0; $displayAdultCount < $this->adultGuests; $displayAdultCount++) {
                        $html .= '<span class="guest-adult-svg"></span>';
                    }
                }
                if ($this->childrenGuests > 0) {
                    for ($displayChildrenCount = 0; $displayChildrenCount < $this->childrenGuests; $displayChildrenCount++) {
                        $html .= '<span class="guest-child-svg"></span>';
                    }
                }

                // // Append a select element for the quantity
                // $html .= '<select data-room-id="' . $id . '" name="room_quantity">';
                // // Append an option for each possible quantity
                // for ($i = 0; $i <= $quantity; $i++) {
                //     $html .= '<option value="' . $i . '">' . $i . '</option>';
                // }
                // $html .= '</select>';
                $html .= '<div class="checkin-staydate-wrap">';

                $total_roomrate                                       = self::calculateRoomPriceTotal($id);
                $this->bookingSearchResults[ $id ][ 'totalroomrate' ] = $total_roomrate;

                $html .= '<div class="room-price-total" data-roomprice="' . esc_attr($total_roomrate) . '">' . atollmatrix_price($total_roomrate) . '</div>';
                $html .= '<div class="preloader-element-outer"><div class="preloader-element"></div></div>';
                if ( isset( $this->bookingSearchResults[ $id ]['discountlabel'] ) ) {
                    $html .= '<div class="room-price-discount-label">'. esc_html($this->bookingSearchResults[ $id ]['discountlabel']) . '</div>';
                }

                $html .= self::generate_MealPlanIncluded($id);

                $html .= '<div class="roomchoice-selection">';

                $html .= '<div class="roomchoice-seperator roomchoice-bedlayout">';
                $html .= '<div class="bedlayout-wrap">';
                $html .= '<div class="room-summary-heading room-bedlayout-heading" for="room-number-input">Bed Layout</div>';
                $html .= '</div>';
                $html .= '<input class="roomchoice" name="room[' . esc_attr($id) . '][quantity]" type="hidden" data-type="room-number" data-roominputid="' . esc_attr($id) . '" data-roomqty="' . esc_attr($quantity) . '" id="room-input-' . esc_attr($id) . '" min="0" max="' . esc_attr($quantity) . '" value="1">';
                $html .= self::generate_BedInformation($id);
                $html .= '</div>';

                $html .= '<div class="roomchoice-seperator roomchoice-mealplan">';
                $html .= self::generate_MealPlanRadio($id);
                $html .= '</div>';

                $html .= '<div class="room-button-wrap">';
                $html .= '<div data-room-button-id="' . esc_attr($id) . '" class="choose-room-button book-button">' . __('Choose this room', 'atollmatrix') . '</div>';
                $html .= '</div>';

                $html .= '</div>';

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';

            }

            $html .= '<div class="stay-summary-wrap">';
            $html .= '<div class="checkin-summary">Check-in: ' . atollmatrix_readable_date($this->checkinDate) . '</div>';
            $html .= '<div class="checkout-summary">Check-out: ' . atollmatrix_readable_date($this->checkoutDate) . '</div>';
            $html .= '<div class="staynight-summary">Nights: ' . esc_attr($this->staynights) . '</div>';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';

            // error_log( print_r( $this->bookingSearchResults , true ));
            atollmatrix_set_booking_transient($this->bookingSearchResults, $this->bookingNumber);
        }

        return $html;
    }

    public function displayBookingPerDay($ratesArray_date)
    {
        $total_roomrate = 0;
        $html           = '';
        foreach ($ratesArray_date as $staydate => $roomrate) {
            $html .= '<div class="checkin-staydate"><span class="number-of-rooms"></span>' . esc_html($staydate) . ' - ' . atollmatrix_price($roomrate) . '</div>';

            $roomrate = self::applyPricePerPerson($roomrate);

            $total_roomrate = $total_roomrate + $roomrate;
        }

        return $html;
    }

    /**
     * Calculates long-stay discount.
     *
     * @param string $checkinDate      The check-in date.
     * @param string $checkoutDate     The check-out date.
     * @param int    $longStayWindow   The number of days defining the long-stay window.
     * @param float  $longStayDiscount The percentage discount to apply for long stays.
     * @return float                   Calculated discount percentage or zero if not applicable.
     */
    public function calculateLongStayDiscount($checkinDate, $checkoutDate, $longStayWindow, $longStayDiscount)
    {
        $checkinDateTime  = new \DateTime($checkinDate);
        $checkoutDateTime = new \DateTime($checkoutDate);

        $interval = $checkinDateTime->diff($checkoutDateTime);
        $stayDuration = (int) $interval->format('%a'); // Difference in days

        // Check if the stay duration is equal to or greater than the long-stay window
        if ($stayDuration >= $longStayWindow) {
            return $longStayDiscount;
        } else {
            return 0; // No discount if the stay is shorter than the long-stay window
        }
    }

    /**
     * Calculates early booking discount.
     *
     * @param string $checkinDate          The check-in date.
     * @param int    $earlyBookingWindow   The number of days defining the early booking window.
     * @param float  $earlyBookingDiscount The percentage discount to apply.
     * @return float                       Calculated discount percentage or zero if not applicable.
     */
    public function calculateEarlyBookingDiscount($checkinDate, $earlyBookingWindow, $earlyBookingDiscount)
    {
        $currentDate     = new \DateTime(); // Current date
        $checkinDateTime = new \DateTime($checkinDate); // Check-in date

        $interval         = $currentDate->diff($checkinDateTime);
        $daysUntilCheckin = (int) $interval->format('%a'); // Difference in days

        // Check if the booking is made sufficiently in advance
        if ($daysUntilCheckin >= $earlyBookingWindow) {
            return $earlyBookingDiscount;
        } else {
            return 0; // No discount if outside the early booking window
        }
    }

    /**
     * Calculates last-minute discount.
     *
     * @param string $checkinDate          The check-in date.
     * @param int    $lastMinuteWindow     The number of days defining the last-minute window.
     * @param float  $lastMinuteDiscount   The percentage discount to apply.
     * @return float                       Calculated discount percentage or zero if not applicable.
     */
    public function calculateLastMinuteDiscount($checkinDate, $lastMinuteWindow, $lastMinuteDiscount)
    {
        $currentDate     = new \DateTime(); // Current date
        $checkinDateTime = new \DateTime($checkinDate); // Check-in date

        $interval         = $currentDate->diff($checkinDateTime);
        $daysUntilCheckin = (int) $interval->format('%a'); // Difference in days

        // Check if the booking is within the last-minute window
        if ($daysUntilCheckin <= $lastMinuteWindow) {
            return $lastMinuteDiscount;
        } else {
            return 0; // No discount if outside the last-minute window
        }
    }

    /**
     * Calculates the highest applicable discount among last-minute, early booking, and long-stay,
     * and identifies the type of discount.
     *
     * @param string $checkinDate        The check-in date.
     * @param string $checkoutDate       The check-out date.
     * @param array  $discountParameters Parameters for each discount type.
     * @return array                     An array with the highest discount value and its type.
     */
    public function calculateHighestDiscount($checkinDate, $checkoutDate)
    {
        $discountParameters = array();
        $discountLabel = '';
    
        // Check and set parameters for last-minute discount
        $discount_lastminute = atollmatrix_get_option('discount_lastminute');
        if ($discount_lastminute && isset($discount_lastminute['days'], $discount_lastminute['percent'])) {
            $discountParameters['lastminute'] = [
                'window' => $discount_lastminute['days'],
                'discount' => $discount_lastminute['percent'],
                'label' => $discount_lastminute['label']
            ];
        }
    
        // Check and set parameters for early booking discount
        $discount_earlybooking = atollmatrix_get_option('discount_earlybooking');
        if ($discount_earlybooking && isset($discount_earlybooking['days'], $discount_earlybooking['percent'])) {
            $discountParameters['earlybooking'] = [
                'window' => $discount_earlybooking['days'],
                'discount' => $discount_earlybooking['percent'],
                'label' => $discount_earlybooking['label']
            ];
        }
    
        // Check and set parameters for long-stay discount
        $discount_longstay = atollmatrix_get_option('discount_longstay');
        if ($discount_longstay && isset($discount_longstay['days'], $discount_longstay['percent'])) {
            $discountParameters['longstay'] = [
                'window' => $discount_longstay['days'],
                'discount' => $discount_longstay['percent'],
                'label' => $discount_longstay['label']
            ];
        }
    
        // Initialize discounts
        $lastMinuteDiscount = $earlyBookingDiscount = $longStayDiscount = 0;
    
        // Calculate discounts if parameters are set
        if (isset($discountParameters['lastminute'])) {
            $lastMinuteDiscount = $this->calculateLastMinuteDiscount(
                $checkinDate,
                $discountParameters['lastminute']['window'],
                $discountParameters['lastminute']['discount']
            );
        }
    
        if (isset($discountParameters['earlybooking'])) {
            $earlyBookingDiscount = $this->calculateEarlyBookingDiscount(
                $checkinDate,
                $discountParameters['earlybooking']['window'],
                $discountParameters['earlybooking']['discount']
            );
        }
    
        if (isset($discountParameters['longstay'])) {
            $longStayDiscount = $this->calculateLongStayDiscount(
                $checkinDate,
                $checkoutDate,
                $discountParameters['longstay']['window'],
                $discountParameters['longstay']['discount']
            );
        }
    
        // Find the highest discount and its type
        $highestDiscount = max($lastMinuteDiscount, $earlyBookingDiscount, $longStayDiscount);
        $discountType = '';
    
        if ($highestDiscount == $lastMinuteDiscount && isset($discountParameters['lastminute'])) {
            $discountType = 'lastminute';
        } elseif ($highestDiscount == $earlyBookingDiscount && isset($discountParameters['earlybooking'])) {
            $discountType = 'earlybooking';
        } elseif ($highestDiscount == $longStayDiscount && isset($discountParameters['longstay'])) {
            $discountType = 'longstay';
        }
    
        if ($discountType !== '') {
            $discountLabel = $discountParameters[$discountType]['label'];
        }
    
        return ['discountValue' => $highestDiscount, 'discountType' => $discountType, 'discountLabel' => $discountLabel];
    }    

    public function calculateRoomPriceTotal($room_id)
    {
        $total_roomrate = 0;
        $html           = '';

        // Assuming this finds the earliest check-in date from the rates
        $checkinDate = $this->findCheckinDate($room_id);
        $checkoutDate = $this->findCheckoutDate($room_id);

        // if ($lastminute_discountPercent > 0) {
        //     $this->bookingSearchResults[ $room_id ]['discountlabel'] = $lastminute_label;
        // }

        $highestDiscountInfo = $this->calculateHighestDiscount($checkinDate, $checkoutDate);
        $highestDiscountValue = $highestDiscountInfo['discountValue'];
        $highestDiscountType = $highestDiscountInfo['discountType'];
        $highestDiscountLabel = $highestDiscountInfo['discountLabel'];
        // Use $highestDiscountValue and $highestDiscountType as needed
        if ($highestDiscountValue > 0) {
            $this->bookingSearchResults[ $room_id ]['discountlabel'] = $highestDiscountLabel;
        }

        // Apply the rates
        foreach ($this->ratesArray[$room_id]['date'] as $staydate => $roomrate) {
            $roomrate = self::applyPricePerPerson($roomrate);

            if ($highestDiscountValue > 0) {
                $roomrate = $roomrate - (($roomrate / 100) * $highestDiscountValue);
            }

            $this->bookingSearchResults[$room_id]['staydate'][$staydate] = $roomrate;
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
    public function findCheckinDate($room_id)
    {
        if (empty($this->ratesArray[ $room_id ][ 'date' ])) {
            return ''; // Return empty string if there are no dates
        }

        $earliestDate = null;
        foreach ($this->ratesArray[ $room_id ][ 'date' ] as $staydate => $roomrate) {
            if (is_null($earliestDate) || strtotime($staydate) < strtotime($earliestDate)) {
                $earliestDate = $staydate;
            }
        }

        return $earliestDate;
    }

    /**
     * Finds the latest checkout date for a given room.
     *
     * @param int $room_id The ID of the room.
     * @return string      The latest checkout date in 'Y-m-d' format, or an empty string if no dates are found.
     */
    public function findCheckoutDate($room_id)
    {
        if (empty($this->ratesArray[$room_id]['date'])) {
            return ''; // Return empty string if there are no dates
        }

        $latestDate = null;
        foreach ($this->ratesArray[$room_id]['date'] as $staydate => $roomrate) {
            if (is_null($latestDate) || strtotime($staydate) > strtotime($latestDate)) {
                $latestDate = $staydate;
            }
        }

        return $latestDate;
    }

    private function applyPricePerPerson($roomrate)
    {

        $perPersonPricing = atollmatrix_get_option('perpersonpricing');
        if ( isset( $perPersonPricing ) && is_array( $perPersonPricing ) ) {
            foreach ($perPersonPricing as $pricing) {
                if ($this->totalChargeableGuests == $pricing[ 'people' ]) {
                    if ($pricing[ 'type' ] === 'percentage' && $pricing[ 'total' ] === 'decrease') {
                        // Decrease the rate by the given percentage
                        $roomrate -= ($roomrate * $pricing[ 'number' ] / 100);
                    } elseif ($pricing[ 'type' ] === 'fixed' && $pricing[ 'total' ] === 'increase') {
                        // Increase the rate by the fixed amount
                        $roomrate += $pricing[ 'number' ];
                    } elseif ($pricing[ 'type' ] === 'percentage' && $pricing[ 'total' ] === 'increase') {
                        // Increase the rate by the given percentage
                        $roomrate += ($roomrate * $pricing[ 'number' ] / 100);
                    } elseif ($pricing[ 'type' ] === 'fixed' && $pricing[ 'total' ] === 'decrease') {
                        // Decrease the rate by the fixed amount
                        $roomrate -= $pricing[ 'number' ];
                    }
                }
            }
            
        }

        return $roomrate;
    }

    public function generate_BedMetabox_callback()
    {
        if (isset($_POST[ 'roomID' ])) {
            $room_id = $_POST[ 'roomID' ];
        }
        if (isset($_POST[ 'fieldID' ])) {
            $meta_field = $_POST[ 'fieldID' ];
        }
        if (isset($_POST[ 'metaValue' ])) {
            $meta_value = $_POST[ 'metaValue' ];
        }

        if ('' !== $room_id) {
            $html = self::generate_BedMetabox($room_id, $meta_field, $meta_value);
        } else {
            $html = '<span class="bedlayout-room-notfound-error">'. __('Room not found!','atollmatrix') . '</span>';
        }

        wp_send_json($html);
    }

    public function generate_BedMetabox($room_id, $meta_field, $meta_value)
    {

        $html = '';

        $room_data = get_post_custom($room_id);

        $bedinputcount = 0;

        // error_log($room_id);
        // error_log($meta_field);
        // error_log($meta_value);

        if (isset($room_data[ "atollmatrix_alt_bedsetup" ][ 0 ])) {
            $bedsetup       = $room_data[ "atollmatrix_alt_bedsetup" ][ 0 ];
            $bedsetup_array = unserialize($bedsetup);

            foreach ($bedsetup_array as $roomId => $roomData) {
                // Get the bed layout for this room

                $bedLayout = '';
                $bedCount  = 0;
                foreach ($roomData[ 'bedtype' ] as $bedFieldID => $bedName) {
                    $bedQty = $roomData[ 'bednumber' ][ $bedFieldID ];
                    if ($bedCount > 0) {
                        $bedLayout .= ' ';
                    }
                    for ($i = 0; $i < $bedQty; $i++) {
                        if ($i > 0) {
                            $bedLayout .= ' ';
                        }
                        $bedLayout .= $bedName;
                    }
                    $bedCount++;
                }

                $bedinputcount++;

                $html .= "<label for='" . esc_attr($meta_field) . "-" . esc_attr($bedinputcount) . "'>";
                $html .= "<input type='radio' id='" . esc_attr($meta_field) . "-" . esc_attr($bedinputcount) . "' name='" . esc_attr($meta_field) . "' value='".esc_attr($bedLayout)."'";

                // Check the first radio input by default
                if ($meta_value === $bedLayout) {
                    $html .= " checked";
                }

                $html .= '>';
                $html .= '<span class="checkbox-label checkbox-bed-label">';
                $html .= '<div class="guest-bed-wrap guest-bed-' . sanitize_title($bedLayout) . '-wrap">';
                foreach ($roomData[ 'bedtype' ] as $bedFieldID => $bedName) {

                    $bedQty = $roomData[ 'bednumber' ][ $bedFieldID ];
                    for ($i = 0; $i < $bedQty; $i++) {
                        $html .= atollmatrix_get_BedLayout($bedName, $bedFieldID . '-' . $i);
                    }
                }
                $html .= '</div>';
                $html .= '</span>';
                $html .= '</label>';
            }
        }

        return $html;
    }

    public function generate_BedInformation($room_id)
    {

        $html = '';

        $room_data = get_post_custom($room_id);

        if (isset($room_data[ "atollmatrix_alt_bedsetup" ][ 0 ])) {
            $bedsetup       = $room_data[ "atollmatrix_alt_bedsetup" ][ 0 ];
            $bedsetup_array = unserialize($bedsetup);

            $firstRoomId = array_key_first($bedsetup_array);

            foreach ($bedsetup_array as $roomId => $roomData) {
                // Get the bed layout for this room

                $bedLayout = '';
                $bedCount  = 0;
                foreach ($roomData[ 'bedtype' ] as $bedFieldID => $bedName) {
                    $bedQty = $roomData[ 'bednumber' ][ $bedFieldID ];
                    if ($bedCount > 0) {
                        $bedLayout .= ' ';
                    }
                    for ($i = 0; $i < $bedQty; $i++) {
                        if ($i > 0) {
                            $bedLayout .= ' ';
                        }
                        $bedLayout .= $bedName;
                    }
                    $bedCount++;
                }

                $this->bookingSearchResults[ $room_id ][ 'bedlayout' ][ sanitize_title($bedLayout) ] = true;

                $html .= "<label for='room-".esc_attr($room_id)."-bedlayout-".esc_attr($bedLayout)."'>";
                $html .= "<input type='radio' id='room-".esc_attr($room_id)."-bedlayout-".esc_attr($bedLayout)."' name='room[".esc_attr($room_id)."][bedlayout]' value='".esc_attr($bedLayout)."'";

                // Check the first radio input by default
                if ($roomId === $firstRoomId) {
                    $html .= " checked";
                }

                $html .= '>';
                $html .= '<span class="checkbox-label checkbox-bed-label">';
                $html .= '<div class="guest-bed-wrap guest-bed-' . sanitize_title($bedLayout) . '-wrap">';
                foreach ($roomData[ 'bedtype' ] as $bedFieldID => $bedName) {

                    $bedQty = $roomData[ 'bednumber' ][ $bedFieldID ];
                    for ($i = 0; $i < $bedQty; $i++) {
                        $html .= atollmatrix_get_BedLayout($bedName, $bedFieldID . '-' . $i);
                    }
                }
                $html .= '</div>';
                $html .= '</span>';
                $html .= '</label>';
            }
        }

        return $html;
    }

    public function get_AllBedLayouts($bedNames)
    {
        $html           = '';
        $bedNames_array = explode(' ', $bedNames);
        foreach ($bedNames_array as $key => $bedName) {
            $html .= atollmatrix_get_BedLayout($bedName, $key);
        }

        return $html;
    }

    public function paymentHelperButton($total, $bookingnumber)
    {
        $payment_button = '<div data-paytotal="' . esc_attr($total) . '" data-bookingnumber="' . esc_attr($bookingnumber) . '" id="woo-bookingpayment" class="book-button">'.__('Pay Booking','atollmatrix').'</div>';
        return $payment_button;
    }

    public function bookingDataFields()
    {
        $dataFields = [
            'full_name'      => __('Full Name','atollmatrix'),
            'passport'       => __('Passport No','atollmatrix'),
            'email_address'  => __('Email Address','atollmatrix'),
            'phone_number'   => __('Phone Number','atollmatrix'),
            'street_address' => __('Street Address','atollmatrix'),
            'city'           => __('City','atollmatrix'),
            'state'          => __('State/Province','atollmatrix'),
            'zip_code'       => __('Zip Code','atollmatrix'),
            'country'        => __('Country','atollmatrix'),
            'guest_comment'  => __('Notes','atollmatrix'),
            'guest_consent'  => __('By clicking "Book this Room" you agree to our terms and conditions and privacy policy.','atollmatrix'),
         ];

        return $dataFields;
    }

    public function register_Guest_Form()
    {
        $country_options = atollmatrix_country_list("select", "");

        $html = '<div class="registration-column registration-column-two" id="booking-summary">';
        $html .= self::bookingSummary(
            $bookingnumber = '',
            $room_id = '',
            $booking_results[ $room_id ][ 'roomtitle' ] = '',
            $this->checkinDate,
            $this->checkoutDate,
            $this->staynights,
            $this->adultGuests,
            $this->childrenGuests,
            $bedlayout = '',
            $mealplan = '',
            $choice = '',
            $perdayprice = '',
            $total = ''
        );
        $html .= '</div>';

        $bookingsuccess = self::booking_Successful();

        $formInputs = self::bookingDataFields();

        $consent_text = __('Consent is required for booking.','atollmatrix');

        $form_html = <<<HTML
		<div class="registration_form_outer registration_request">
			<div class="registration_form_wrap">
				<div class="registration_form">
					<div class="registration-column registration-column-one registration_form_inputs">
                    <div class="booking-backto-roomschoice"><div class="booking-backto-roomchoice-inner"><i class="fa-solid fa-arrow-left"></i> Back</div></div>
                    <h3>Registration</h3>
                    <div class="form-group form-floating">
						<input placeholder="Full Name" type="text" class="form-control" id="full_name" name="full_name" required>
						<label for="full_name" class="control-label">$formInputs[full_name]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="Passport No." type="text" class="form-control" id="passport" name="passport" required>
						<label for="passport" class="control-label">$formInputs[passport]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="" type="email" class="form-control" id="email_address" name="email_address" required>
						<label for="email_address" class="control-label">$formInputs[email_address]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="" type="tel" class="form-control" id="phone_number" name="phone_number" required>
						<label for="phone_number" class="control-label">$formInputs[phone_number]</label>
					</div>
                    <div class="form-group form-floating">
                        <input placeholder="" type="text" class="form-control" id="street_address" name="street_address">
                        <label for="street_address" class="control-label">$formInputs[street_address]</label>
                    </div>
                    <div class="form-group form-floating">
                        <input placeholder="" type="text" class="form-control" id="city" name="city">
                        <label for="city" class="control-label">$formInputs[city]</label>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group form-floating">
                                <input placeholder="" type="text" class="form-control" id="state" name="state">
                                <label for="state" class="control-label">$formInputs[state]</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group form-floating">
                                <input placeholder="" type="text" class="form-control" id="zip_code" name="zip_code">
                                <label for="zip_code" class="control-label">$formInputs[zip_code]</label>
                            </div>
                        </div>
                    </div>
					<div class="form-group form-floating">
						<select required placeholder="" class="form-control" id="country" name="country" >
						$country_options
						</select>
						<label for="country" class="control-label">$formInputs[country]</label>
					</div>
					<div class="form-group form-floating">
					<textarea placeholder="" class="form-control" id="guest_comment" name="guest_comment"></textarea>
					<label for="guest_comment" class="control-label">$formInputs[guest_comment]</label>
					</div>
					<div class="checkbox guest-consent-checkbox">
					<label for="guest_consent">
						<input type="checkbox" class="form-check-input" id="guest_consent" name="guest_consent" required /><span class="consent-notice">$formInputs[guest_consent]</span>
                        <div class="invalid-feedback">
                            $consent_text
                        </div>
                    </label>
					</div>
				</div>

				$html

				</div>
			</div>
		</div>
HTML;

        return $form_html . $bookingsuccess;
    }

    public function booking_Successful()
    {
        $reservation_instance = new \AtollMatrix\Reservations();
        $booking_page_link    = $reservation_instance->getBookingDetailsPageLinkForGuest();
    
        $booking_details_link_text = __('Booking Details', 'atollmatrix');
        $booking_details_link = '<a href="' . esc_attr(esc_url(get_page_link($booking_page_link))) . '">' . $booking_details_link_text . '</a>';
    
        $booking_successful_text = __('Booking Successful', 'atollmatrix');
        $hi_text = __('Hi,', 'atollmatrix');
        $your_booking_number_is_text = __('Your booking number is:', 'atollmatrix');
        $please_contact_us_text = __('Please contact us to cancel, modify or if there\'s any questions regarding the booking.', 'atollmatrix');
    
        $success_html = <<<HTML
        <div class="registration_form_outer registration_successful">
            <div class="registration_form_wrap">
                <div class="registration_form">
                <div class="registration-successful-inner">
                    <h3>$booking_successful_text</h3>
                    <p>
                        $hi_text
                    </p>
                    <p>
                        $your_booking_number_is_text <span class="booking-number">$this->bookingNumber</span>
                    </p>
                    <p>
                        $please_contact_us_text
                    </p>
                    <p>
                        <div id="booking-details" class="book-button not-fullwidth">$booking_details_link</div>
                    </p>
                </div>
                </div>
            </div>
        </div>
    HTML;
    
        return $success_html;
    }    

    public function generate_MealPlanIncluded($room_id)
    {

        $mealPlans = atollmatrix_get_option('mealplan');

        if (is_array($mealPlans) && count($mealPlans) > 0) {
            $includedMealPlans = array();
            $optionalMealPlans = array();

            foreach ($mealPlans as $id => $plan) {
                if ($plan[ 'choice' ] === 'included') {
                    $includedMealPlans[ $id ] = $plan;
                } elseif ($plan[ 'choice' ] === 'optional') {
                    $optionalMealPlans[ $id ] = $plan;
                }
            }

            $html = '';
            if (is_array($includedMealPlans) && count($includedMealPlans) > 0) {
                $html .= '<div class="room-included-meals">';
                foreach ($includedMealPlans as $id => $plan) {
                    $html .= '<i class="fa-solid fa-square-check"></i>';
                    $html .= atollmatrix_get_mealplan_labels($plan[ 'mealtype' ]) . __(' included.', 'atollmatrix');
                    $html .= '<label>';
                    $html .= '<input hidden type="text" name="room[' . esc_attr($room_id) . '][meal_plan][included]" value="' . esc_attr($plan[ 'mealtype' ]) . '">';
                    $html .= '</label>';
                    $this->bookingSearchResults[ $room_id ][ 'meal_plan' ][ $plan[ 'mealtype' ] ] = 'included';
                }
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function generate_MealPlanRadio($room_id)
    {

        $mealPlans = atollmatrix_get_option('mealplan');

        if (is_array($mealPlans) && count($mealPlans) > 0) {
            $includedMealPlans = array();
            $optionalMealPlans = array();

            foreach ($mealPlans as $id => $plan) {
                if ($plan[ 'choice' ] === 'included') {
                    $includedMealPlans[ $id ] = $plan;
                } elseif ($plan[ 'choice' ] === 'optional') {
                    $optionalMealPlans[ $id ] = $plan;
                }
            }

            $html = '';
            if (is_array($optionalMealPlans) && count($optionalMealPlans) > 0) {
                $html .= '<div class="room-mealplans">';
                $html .= '<div class="mealplans-wrap">';
                $html .= '<div class="room-summary-heading room-mealplans-heading">Mealplans</div>';
                $html .= '<div class="room-mealplan-input-wrap">';
                $html .= '<div class="room-mealplan-input">';
                $html .= '<label for="room-' . esc_attr($room_id) . '-meal_plan-optional-none">';
                $html .= '<input class="mealtype-input" type="radio" data-mealprice="none" id="room-' . esc_attr($room_id) . '-meal_plan-optional-none" name="room[' . esc_attr($room_id) . '][meal_plan][optional]" value="none" checked><span class="checkbox-label">' . __('Not selected', 'atollmatrix') . '</span>';
                $html .= '</label>';
                $html .= '</div>';
                foreach ($optionalMealPlans as $id => $plan) {
                    $html .= '<div class="room-mealplan-input">';
                    $mealprice = $plan[ 'price' ] * $this->staynights;
                    $html .= '<label for="room-' . esc_attr($room_id) . '-meal_plan-optional-' . esc_attr($plan[ 'mealtype' ]) . '">';
                    $html .= '<input class="mealtype-input" type="radio" data-mealprice="' . esc_attr($mealprice) . '" id="room-' . esc_attr($room_id) . '-meal_plan-optional-' . esc_attr($plan[ 'mealtype' ]) . '" name="room[' . esc_attr($room_id) . '][meal_plan][optional]" value="' . esc_attr($plan[ 'mealtype' ]) . '"><span class="room-mealplan-price checkbox-label">' . atollmatrix_price($mealprice) . '<span class="mealplan-text">' . atollmatrix_get_mealplan_labels($plan[ 'mealtype' ]) . '</span></span>';
                    $html .= '</label>';
                    $this->bookingSearchResults[ $room_id ][ 'meal_plan' ][ $plan[ 'mealtype' ] ] = $mealprice;

                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function canAccomodate_to_rooms($rooms, $adults = false, $children = false)
    {

        $min_adults         = 0;
        $max_adults_total   = false;
        $max_children_total = false;
        $max_guests_total   = 0;
        $max_children       = false;
        $max_adults         = false;
        $max_guests         = 0;
        $can_occomodate     = array();
        $will_accomodate    = true;
        $guests             = intval($adults) + intval($children);
        $can_occomodate     = array();

        foreach ($rooms as $room) {
            $room_id  = $room[ 'id' ];
            $room_qty = $room[ 'quantity' ];

            $room_data = get_post_custom($room_id);

            if (isset($room_data[ "atollmatrix_max_guests" ][ 0 ])) {
                $max_guest_for_room = $room_data[ "atollmatrix_max_guests" ][ 0 ];
                $max_guests         = $max_guest_for_room * $room_qty;
            }
            if (isset($room_data[ "atollmatrix_max_adult_limit_status" ][ 0 ])) {
                $adult_limit_status = $room_data[ "atollmatrix_max_adult_limit_status" ][ 0 ];
                if ('1' == $adult_limit_status) {
                    $max_adults = $room_data[ "atollmatrix_max_adults" ][ 0 ];
                    $max_adults = $max_adults * $room_qty;
                } else {
                    $max_adults = $max_guest_for_room;
                }
            }
            if (isset($room_data[ "atollmatrix_max_children_limit_status" ][ 0 ])) {
                $children_limit_status = $room_data[ "atollmatrix_max_children_limit_status" ][ 0 ];
                if ('1' == $children_limit_status) {
                    $max_children = $room_data[ "atollmatrix_max_children" ][ 0 ];
                    $max_children = $max_children * $room_qty;
                } else {
                    $max_children = $max_guest_for_room - 1;
                }
            }

            if ($max_adults) {
                $max_adults_total = $max_adults_total + $max_adults;
            }
            if ($max_children) {
                $max_children_total = $max_children_total + $max_children;
            }
            $max_guests_total = $max_guests_total + $max_guests;
            $min_adults       = $min_adults + $room_qty;

            $can_occomodate[ $room_id ][ 'qty' ]          = $room_qty;
            $can_occomodate[ $room_id ][ 'max_adults' ]   = $max_adults;
            $can_occomodate[ $room_id ][ 'max_children' ] = $max_children;
            $can_occomodate[ $room_id ][ 'max_guests' ]   = $max_guests;

        }

        $can_occomodate[ 'allow' ]              = false;
        $can_occomodate[ 'error' ]              = 'Too many guests for choice';
        $can_occomodate[ 'max_adults_total' ]   = $max_adults_total;
        $can_occomodate[ 'max_children_total' ] = $max_children_total;
        $can_occomodate[ 'max_guests_total' ]   = $max_guests_total;
        $can_occomodate[ 'adults' ]             = $adults;
        $can_occomodate[ 'children' ]           = $children;
        $can_occomodate[ 'guests' ]             = $guests;
        $can_occomodate[ 'min_adults' ]         = $min_adults;

        if ($can_occomodate[ 'max_guests_total' ] >= $guests) {
            $can_occomodate[ 'allow' ] = true;
            $can_occomodate[ 'error' ] = '';
        }
        if ($can_occomodate[ 'max_children_total' ]) {
            if ($can_occomodate[ 'max_children_total' ] < $children) {
                $can_occomodate[ 'allow' ] = false;
                $can_occomodate[ 'error' ] = 'Number of children exceed for choice of room';
            }
        }
        // if ($can_occomodate['max_adults_total']) {
        //     if ($can_occomodate['max_adults_total'] < $adults) {
        //         $can_occomodate['allow'] = false;
        //         $can_occomodate['error'] = 'Too many guests to accomodate choice';
        //     }
        // }
        if ($can_occomodate[ 'min_adults' ] > $adults) {
            $can_occomodate[ 'allow' ] = false;
            $can_occomodate[ 'error' ] = 'Should have atleast 1 adult in each room';
        }

        return $can_occomodate;

    }

    public function canAccomodate_everyone_to_room($room_id, $adults = false, $children = false)
    {

        $max_children    = false;
        $max_adults      = false;
        $max_guests      = false;
        $can_occomodate  = array();
        $will_accomodate = true;

        $total_guests = intval($adults + $children);

        $room_data = get_post_custom($room_id);
        if (isset($room_data[ "atollmatrix_max_adult_limit_status" ][ 0 ])) {
            $adult_limit_status = $room_data[ "atollmatrix_max_adult_limit_status" ][ 0 ];
            if ('1' == $adult_limit_status) {
                $max_adults = $room_data[ "atollmatrix_max_adults" ][ 0 ];
            }
        }
        if (isset($room_data[ "atollmatrix_max_children_limit_status" ][ 0 ])) {
            $children_limit_status = $room_data[ "atollmatrix_max_children_limit_status" ][ 0 ];
            if ('1' == $children_limit_status) {
                $max_children = $room_data[ "atollmatrix_max_children" ][ 0 ];
            }
        }
        if (isset($room_data[ "atollmatrix_max_guests" ][ 0 ])) {
            $max_guests = $room_data[ "atollmatrix_max_guests" ][ 0 ];
        }

        if ($max_children) {
            $can_occomodate[ $room_id ][ 'children' ] = true;
            if ($children > $max_children) {
                $can_occomodate[ $room_id ][ 'children' ] = false;
                $will_accomodate                          = false;
            }
        }
        if ($max_adults) {
            $can_occomodate[ $room_id ][ 'adults' ] = true;
            if ($adults > $max_adults) {
                $can_occomodate[ $room_id ][ 'adults' ] = false;
                $will_accomodate                        = false;
            }
        }
        if ($max_guests) {
            $can_occomodate[ $room_id ][ 'guests' ] = true;
            if ($total_guests > $max_guests) {
                $can_occomodate[ $room_id ][ 'guests' ] = false;
                $will_accomodate                        = false;
            }
        }

        $can_occomodate[ $room_id ][ 'allow' ] = $will_accomodate;

        return $can_occomodate;

    }

    public function buildReservationArray($booking_data)
    {
        $reservationArray = [  ];

        if (array_key_exists('bookingnumber', $booking_data)) {
            $reservationArray[ 'bookingnumber' ] = $booking_data[ 'bookingnumber' ];
        }
        if (array_key_exists('checkin', $booking_data)) {
            $reservationArray[ 'checkin' ] = $booking_data[ 'checkin' ];
        }
        if (array_key_exists('checkout', $booking_data)) {
            $reservationArray[ 'checkout' ] = $booking_data[ 'checkout' ];
        }
        if (array_key_exists('staynights', $booking_data)) {
            $reservationArray[ 'staynights' ] = $booking_data[ 'staynights' ];
        }
        if (array_key_exists('adults', $booking_data)) {
            $reservationArray[ 'adults' ] = $booking_data[ 'adults' ];
        }
        if (array_key_exists('children', $booking_data)) {
            $reservationArray[ 'children' ] = $booking_data[ 'children' ];
        }
        if (array_key_exists('children_age', $booking_data)) {
            $reservationArray[ 'children_age' ] = $booking_data[ 'children_age' ];
        }
        if (array_key_exists('totalguest', $booking_data)) {
            $reservationArray[ 'totalguest' ] = $booking_data[ 'totalguest' ];
        }
        if (array_key_exists('chargeableguests', $booking_data)) {
            $reservationArray[ 'chargeableguests' ] = $booking_data[ 'chargeableguests' ];
        }
        if (array_key_exists('room_id', $booking_data[ 'choice' ])) {
            $reservationArray[ 'room_id' ]   = $booking_data[ 'choice' ][ 'room_id' ];
            $reservationArray[ 'room_data' ] = $booking_data[ $booking_data[ 'choice' ][ 'room_id' ] ];
        }
        if (array_key_exists('bedlayout', $booking_data[ 'choice' ])) {
            $reservationArray[ 'bedlayout' ] = $booking_data[ 'choice' ][ 'bedlayout' ];
        }
        if (array_key_exists('mealplan', $booking_data[ 'choice' ])) {
            $reservationArray[ 'mealplan' ] = $booking_data[ 'choice' ][ 'mealplan' ];
        }
        if (array_key_exists('mealplan_price', $booking_data[ 'choice' ])) {
            $reservationArray[ 'mealplan_price' ] = $booking_data[ 'choice' ][ 'mealplan_price' ];
        }
        if (array_key_exists('mealplan_price', $booking_data[ 'choice' ])) {
            $reservationArray[ 'mealplan_price' ] = $booking_data[ 'choice' ][ 'mealplan_price' ];
        }
        if (array_key_exists('mealplan_price', $booking_data[ 'choice' ])) {
            $reservationArray[ 'mealplan_price' ] = $booking_data[ 'choice' ][ 'mealplan_price' ];
        }

        $currency = atollmatrix_get_option('currency');
        if (isset($currency)) {
            $reservationArray[ 'currency' ] = $currency;
        }

        $tax_instance = new \AtollMatrix\Tax('room');

        $subtotalprice                  = intval($reservationArray[ 'room_data' ][ 'totalroomrate' ]) + intval($reservationArray[ 'mealplan_price' ]);
        $reservationArray[ 'tax' ]      = $tax_instance->apply_tax($subtotalprice, $reservationArray[ 'staynights' ], $reservationArray[ 'totalguest' ], $output = 'data');
        $reservationArray[ 'tax_html' ] = $tax_instance->apply_tax($subtotalprice, $reservationArray[ 'staynights' ], $reservationArray[ 'totalguest' ], $output = 'html');

        $ratepernight                       = intval($subtotalprice) / intval($reservationArray[ 'staynights' ]);
        $ratepernight_rounded               = round($ratepernight, 2);
        $reservationArray[ 'ratepernight' ] = $ratepernight_rounded;
        $reservationArray[ 'subtotal' ]     = round($subtotalprice, 2);
        $reservationArray[ 'total' ]        = $reservationArray[ 'tax' ][ 'total' ];

        return $reservationArray;
    }

    // Ajax function to book rooms
    public function bookRooms()
    {

        error_log('------- booking posted data -------');
        error_log(print_r($_POST, true));

        $serializedData = $_POST[ 'bookingdata' ];
        // Parse the serialized data into an associative array
        parse_str($serializedData, $formData);

        error_log('------- booking posted deserialized data -------');
        error_log(print_r($formData, true));

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_roomlistingbox_nonce' ]) || !check_admin_referer('atollmatrix-roomlistingbox-nonce', 'atollmatrix_roomlistingbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        // Generate unique booking number
        $booking_number = sanitize_text_field($_POST[ 'booking_number' ]);
        $booking_data   = atollmatrix_get_booking_transient($booking_number);

        if (!isset($booking_data)) {
            wp_send_json_error('Invalid or timeout. Please try again');
        }
        // Obtain customer details from form submission
        $bookingdata    = sanitize_text_field($_POST[ 'bookingdata' ]);
        $booking_number = sanitize_text_field($_POST[ 'booking_number' ]);
        $full_name      = sanitize_text_field($_POST[ 'full_name' ]);
        $passport       = sanitize_text_field($_POST[ 'passport' ]);
        $email_address  = sanitize_email($_POST[ 'email_address' ]);
        $phone_number   = sanitize_text_field($_POST[ 'phone_number' ]);
        $street_address = sanitize_text_field($_POST[ 'street_address' ]);
        $city           = sanitize_text_field($_POST[ 'city' ]);
        $state          = sanitize_text_field($_POST[ 'state' ]);
        $zip_code       = sanitize_text_field($_POST[ 'zip_code' ]);
        $country        = sanitize_text_field($_POST[ 'country' ]);
        $guest_comment  = sanitize_text_field($_POST[ 'guest_comment' ]);
        $guest_consent  = sanitize_text_field($_POST[ 'guest_consent' ]);

        error_log('------- Transient Booking Data -------');
        error_log($booking_number);
        error_log(print_r($booking_data, true));
        error_log('------- Transient Booking Data End -------');
        // add other fields as necessary

        $rooms                      = array();
        $rooms[ '0' ][ 'id' ]       = $booking_data[ 'choice' ][ 'room_id' ];
        $rooms[ '0' ][ 'quantity' ] = '1';
        $adults                     = $booking_data[ 'adults' ];
        $children                   = $booking_data[ 'children' ];

        $reservationData = self::buildReservationArray($booking_data);

        $reservationData[ 'customer' ][ 'full_name' ]      = $full_name;
        $reservationData[ 'customer' ][ 'passport' ]       = $passport;
        $reservationData[ 'customer' ][ 'email_address' ]  = $email_address;
        $reservationData[ 'customer' ][ 'phone_number' ]   = $phone_number;
        $reservationData[ 'customer' ][ 'street_address' ] = $street_address;
        $reservationData[ 'customer' ][ 'city' ]           = $city;
        $reservationData[ 'customer' ][ 'state' ]          = $state;
        $reservationData[ 'customer' ][ 'zip_code' ]       = $zip_code;
        $reservationData[ 'customer' ][ 'country' ]        = $country;
        $reservationData[ 'customer' ][ 'guest_comment' ]  = $guest_comment;
        $reservationData[ 'customer' ][ 'guest_consent' ]  = $guest_consent;

        error_log('------- Final Booking Data -------');
        error_log(print_r($reservationData, true));
        error_log('------- Final Booking Data End -------');

        // Check if number of people can be occupied by room
        $can_accomodate = self::canAccomodate_to_rooms($rooms, $adults, $children);
        // error_log(print_r($can_accomodate, true));
        if (false == $can_accomodate[ 'allow' ]) {
            wp_send_json_error($can_accomodate[ 'error' ]);
        }
        // error_log(print_r($can_accomodate, true));
        // error_log("Rooms:");
        // error_log(print_r($rooms, true));

        //wp_send_json_error(' Temporary block for debugging ');
        // Create customer post
        $customer_post_data = array(
            'post_type' => 'atmx_customers', // Your custom post type for customers
            'post_title' => $full_name, // Set the customer's full name as post title
            'post_status' => 'publish', // The status you want to give new posts
            'meta_input' => array(
                'atollmatrix_full_name'      => $full_name,
                'atollmatrix_email_address'  => $email_address,
                'atollmatrix_phone_number'   => $phone_number,
                'atollmatrix_street_address' => $street_address,
                'atollmatrix_city'           => $city,
                'atollmatrix_state'          => $state,
                'atollmatrix_zip_code'       => $zip_code,
                'atollmatrix_country'        => $country,
                'atollmatrix_guest_comment'  => $guest_comment,
                'atollmatrix_guest_consent'  => $guest_consent,
                // add other meta data you need
            ),
        );

        // Insert the post
        $customer_post_id = wp_insert_post($customer_post_data);

        if (!$customer_post_id) {
            wp_send_json_error('Could not save Customer: ' . $customer_post_id);
            return;
        }

        $checkin  = $reservationData[ 'checkin' ];
        $checkout = $reservationData[ 'checkout' ];
        $room_id  = $reservationData[ 'room_id' ];

        $children_array             = array();
        $children_array[ 'number' ] = $reservationData[ 'children' ];

        foreach ($reservationData[ 'children_age' ] as $key => $value) {
            $children_array[ 'age' ][  ] = $value;
        }

        $tax_status = 'excluded';
        $tax_html   = false;
        if (atollmatrix_has_tax()) {
            $tax_status = 'enabled';
            $tax_instance = new \AtollMatrix\Tax('room');
            $tax_html   = $tax_instance->tax_summary($reservationData[ 'tax_html' ][ 'details' ]);
        }

        $new_bookingstatus = atollmatrix_get_option('new_bookingstatus');
        if ('pending' !== $new_bookingstatus && 'confirmed' !== $new_bookingstatus) {
            $new_bookingstatus = 'pending';
        }
        $new_bookingsubstatus = atollmatrix_get_option('new_bookingsubstatus');
        if ('' !== $new_bookingstatus) {
            $new_bookingsubstatus = 'onhold';
        }

        $reservation_booking_uid = \AtollMatrix\Common::generateUuid();

        $signature = md5('atollmatrix_booking_system');

        $sync_status           = 'complete';
        $availabilityProcessor = new AvailabilityBatchProcessor();
        if ($availabilityProcessor->isSyncingInProgress()) {
            $sync_status = 'incomplete';
        }

        $booking_channel = 'Atollmatrix';

        // Here you can also add other post data like post_title, post_content etc.
        $post_data = array(
            'post_type' => 'atmx_reservations', // Your custom post type
            'post_title' => $booking_number, // Set the booking number as post title
            'post_status' => 'publish', // The status you want to give new posts
            'meta_input' => array(
                'atollmatrix_room_id'                        => $room_id,
                'atollmatrix_reservation_status'             => $new_bookingstatus,
                'atollmatrix_reservation_substatus'          => $new_bookingsubstatus,
                'atollmatrix_checkin_date'                   => $checkin,
                'atollmatrix_checkout_date'                  => $checkout,
                'atollmatrix_tax'                            => $tax_status,
                'atollmatrix_tax_html_data'                  => $tax_html,
                'atollmatrix_tax_data'                       => $reservationData[ 'tax' ],
                'atollmatrix_reservation_room_bedlayout'     => $reservationData[ 'bedlayout' ],
                'atollmatrix_reservation_room_mealplan'      => $reservationData[ 'mealplan' ],
                'atollmatrix_reservation_room_adults'        => $reservationData[ 'adults' ],
                'atollmatrix_reservation_room_children'      => $children_array,
                'atollmatrix_reservation_rate_per_night'     => $reservationData[ 'ratepernight' ],
                'atollmatrix_reservation_subtotal_room_cost' => $reservationData[ 'subtotal' ],
                'atollmatrix_reservation_total_room_cost'    => $reservationData[ 'total' ],
                'atollmatrix_booking_number'                 => $booking_number,
                'atollmatrix_booking_uid'                    => $reservation_booking_uid,
                'atollmatrix_customer_id'                    => $customer_post_id,
                'atollmatrix_sync_status'                    => $sync_status,
                'atollmatrix_ics_signature'                  => $signature,
                'atollmatrix_booking_data'                   => $reservationData,
                'atollmatrix_booking_channel'                => $booking_channel,
            ),
        );

        // Insert the post
        $reservation_post_id = wp_insert_post($post_data);

        if ($reservation_post_id) {
            // Successfully created a reservation post
            $data_instance = new \AtollMatrix\Data();
            $data_instance->updateReservationsArray_On_Save($reservation_post_id, get_post($reservation_post_id), true);

            $roomName = \AtollMatrix\Rooms::getRoomName_FromID($room_id);

            $bookingDetails = [
                'guestName'      => $full_name,
                'bookingNumber'  => $booking_number,
                'roomTitle'      => $roomName,
                'checkinDate'    => $checkin,
                'checkoutDate'   => $checkout,
                'adultGuests'    => $reservationData[ 'adults' ],
                'childrenGuests' => $reservationData[ 'children' ],
                'totalCost'      => $reservationData[ 'total' ],
             ];

            $email = new EmailDispatcher($email_address, 'Room Booking Confirmation for:' . $booking_number);
            $email->setHTMLContent()->setBookingConfirmationTemplate($bookingDetails);

            if ($email->send()) {
                // echo 'Confirmation email sent successfully to the guest.';
            } else {
                // echo 'Failed to send the confirmation email.';
            }

        } else {
            // Handle error
        }

        // Send a success response at the end of your function, if all operations are successful
        wp_send_json_success('Booking successfully registered.');
        wp_die();
    }
}

$instance = new \AtollMatrix\Booking();
