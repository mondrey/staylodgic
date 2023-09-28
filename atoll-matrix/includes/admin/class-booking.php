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
        $bookingSearchResults = null
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
        $this->bookingNumber         = uniqid();

        add_shortcode('hotel_booking_search', array($this, 'hotelBooking_SearchForm'));
        // AJAX handler to save room metadata

        add_action('wp_ajax_booking_BookingSearch', array($this, 'booking_BookingSearch'));
        add_action('wp_ajax_nopriv_booking_BookingSearch', array($this, 'booking_BookingSearch'));

        add_action('wp_ajax_process_RoomData', array($this, 'process_RoomData')); // For logged-in users
        add_action('wp_ajax_nopriv_process_RoomData', array($this, 'process_RoomData')); // For non-logged-in users
    }

    public function process_RoomData()
    {
        // Get the data sent via AJAX
        $bookingnumber   = sanitize_text_field($_POST['bookingnumber']);
        $room_id         = sanitize_text_field($_POST['room_id']);
        $room_price      = sanitize_text_field($_POST['room_price']);
        $bed_layout      = sanitize_text_field($_POST['bed_layout']);
        $meal_plan       = sanitize_text_field($_POST['meal_plan']);
        $meal_plan_price = sanitize_text_field($_POST['meal_plan_price']);

        $roomName = \AtollMatrix\Rooms::getRoomName_FromID($room_id);

        $booking_results = self::getBookingTransient( $bookingnumber );

        $booking_results[$room_id]['choice']['bedlayout'] = $bed_layout;
        $booking_results[$room_id]['choice']['mealplan'] = $meal_plan;

        error_log( '====== From Transient ======' );
        error_log( print_r( $booking_results , true ));
        error_log( '====== Specific Room ======' );
        error_log( print_r( $booking_results[$room_id] , true ));
        // Perform any processing you need with the data
        // For example, you can save it to the database or perform calculations

        // Return a response (you can modify this as needed)
        $response = array(
            'success' => true,
            'message' => 'Data: ' . $roomName . ',received successfully.',
        );

        $html = self::bookingSummary(
            $room_id,
            $booking_results[$room_id]['roomtitle'],
            $booking_results['checkin'],
            $booking_results['checkout'],
            $booking_results['staynights'],
            $booking_results['adults'],
            $booking_results['children'],
            $booking_results[$room_id]['choice']['bedlayout'],
            $booking_results[$room_id]['choice']['mealplan'],
            $booking_results[$room_id]['meal_plan'][$booking_results[$room_id]['choice']['mealplan']],
            $booking_results[$room_id]['totalroomrate']
        );

        // Send the JSON response
        wp_send_json($html);
    }

    public function bookingSummary(
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
        $totalroomrate = null
    )
    {
        $html = '<div id="booking-summary-wrap">';
        if ( '' !== $room_name ) {
            $html .= '<div class="room-summary"><span class="summary-room-name">'.$room_name.'</span></div>';
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
        if ( '' !== $bedtype ) {
            $html .= '<div class="bed-summary">'.self::get_BedLayout($bedtype).'</div>';
        }
        $html .= '</div>';
        if ( '' !== $room_id ) {
            $html .= '<div class="meal-summary-wrap">';
            if ( '' !== self::generate_MealPlanIncluded($room_id) ) {
                $html .= '<div class="meal-summary"><span class="summary-mealtype-inlcuded">'.self::generate_MealPlanIncluded($room_id).'</span></div>';
            }
            if ( 'none' !== $mealtype ) {
                $html .= '<div class="summary-icon mealplan-summary-icon"><i class="fa-solid fa-utensils"></i></div>';
                $html .= '<div class="summary-heading mealplan-summary-heading">Mealplan:</div>';
                $html .= '<div class="meal-summary"><span class="summary-mealtype-name">'.self::getMealPlanText($mealtype).'</span></div>';
            }
            $html .= '</div>';

        }

        $html .= '<div class="stay-summary-wrap">';

        $html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
        $html .= '<div class="summary-heading checkin-summary-heading">Check-in:</div>';
        $html .= '<div class="checkin-summary">'.$checkin.'</div>';
        $html .= '<div class="summary-heading checkout-summary-heading">Check-out:</div>';
        $html .= '<div class="checkout-summary">'.$checkout.'</div>';

        $html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
        $html .= '<div class="summary-heading staynight-summary-heading">Nights:</div>';
        $html .= '<div class="staynight-summary">'.$staynights.'</div>';
        $html .= '</div>';

        if ( '' !== $totalroomrate) {
            $html .= '<div class="price-summary-wrap">';
            $html .= '<div class="summary-heading total-summary-heading">Total:</div>';
            $html .= '<div class="price-summary">'.atollmatrix_price( $totalroomrate + $mealprice ).'</div>';
            $html .= '<div class="tax-summary">Excluding tax</div>';
            $html .= '</div>';
        }

        if ( '' !== $room_id ) {
            $html .= '<div class="form-group">';
            $html .= '<div id="bookingRegister" class="book-button">Book this room</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function saveBooking_Transient($data)
    {
        set_transient($this->bookingNumber, $data, 20 * MINUTE_IN_SECONDS);
    }
    public function getBookingTransient($bookingNumber = null)
    {
        if ($bookingNumber === null) {
            // Use $this->bookingNumber if $bookingNumber is not supplied
            $bookingNumber = $this->bookingNumber;
        }
        
        return get_transient($bookingNumber);
    }  

    public function hotelBooking_SearchForm()
    {
        // Generate unique booking number
        self::saveBooking_Transient('1');
        ob_start();
        ?>
        <div id="hotel-booking-form">
            <form action="" method="post" id="hotel-booking">
                <div>
                    <label for="reservation-date">Book Date:</label>
                    <input type="date" id="reservation-date" name="reservation_date">
                </div>
                <div>
                    <label for="number-of-adults">Number of Adults:</label>
                    <input type="number" id="number-of-adults" name="number_of_adults" min="1">
                </div>
                <div class="children-number" data-agelimitofchild="13">
                    <label for="number-of-children">Number of Children:</label>
                    <input type="number" id="number-of-children" name="number_of_children" min="0">
                </div>
                <div id="bookingSearch" class="div-button">Search</div>
                <div class="recommended-alt-wrap">
                    <div class="recommended-alt-title">Rooms unavailable</br>Please choose among following dates which has
                        availability</div>
                    <div id="recommended-alt-dates"></div>
                </div>

                <div class="available-list">
                    <div id="available-list-ajax"></div>
                </div>
            </form>
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
                $firstKey = $keys[0];
                $lastKey  = end($keys);

                // Keep only the first and last records and assign unique indexes
                $newSubArray[$firstKey] = array(
                    'check-in'  => $firstKey,
                    'check-out' => $lastKey,
                );
            }

            // Add the new sub-array to the new room availability array
            $new_room_availability_array[$roomId] = $newSubArray;
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
                $checkInAlt = $subArray['check-in'];
                $staylast   = $subArray['check-out'];

                // Check if the current check-in and checkout dates have already been processed
                if (in_array([$checkInAlt, $staylast], $processedDates)) {
                    //error_log( 'Skipping .... ' . $checkInAlt, $staylast);
                    continue; // Skip processing identical dates
                }

                // Add the current check-in and checkout dates to the processed dates array
                $processedDates[] = [$checkInAlt, $staylast];

                // Get the date one day after the staylast
                $checkOutAlt = date('Y-m-d', strtotime($staylast . ' +1 day'));

                $newProcessedDates[$checkInAlt] = array(
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
            $staylast    = $key['staylast'];
            $checkInAlt  = $key['check-in'];
            $checkOutAlt = $key['check-out'];

            // Format the dates as "Month Day" (e.g., "July 13th")
            $formattedFirstDate = date('F jS', strtotime($checkInAlt));

            $formattedNextDay = date('F jS', strtotime($checkOutAlt));
            if (date('F', strtotime($staylast)) !== date('F', strtotime($checkInAlt))) {
                $formattedNextDay = date('F jS', strtotime($checkOutAlt));
            } else {
                $formattedNextDay = date('jS', strtotime($checkOutAlt));
            }

            $output .= "<span data-check-staylast='{$staylast}' data-check-in='{$checkInAlt}' data-check-out='{$checkOutAlt}'>{$formattedFirstDate} - {$formattedNextDay}</span>, ";
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

        if (isset($_POST['reservation_date'])) {
            $reservation_date = $_POST['reservation_date'];
        }

        if (isset($_POST['number_of_adults'])) {
            $number_of_adults = $_POST['number_of_adults'];
        }

        if (isset($_POST['number_of_children'])) {
            $number_of_children = $_POST['number_of_children'];
        }

        // // *********** To be removed
        // $reservation_date = '2023-09-27 to 2023-09-30';
        // $number_of_adults = 1;
        // // ***********

        $freeStayAgeUnder = atollmatrix_get_option('childfreestay');

        // error_log('---- Free Stay');
        // error_log(print_r($freeStayAgeUnder, true));

        $freeStayChildCount = 0;

        if (isset($_POST['children_age'])) {
            // Loop through all the select elements with the class 'children-age-selector'
            foreach ($_POST['children_age'] as $selected_age) {
                // Sanitize and store the selected values in an array
                $children_age[] = sanitize_text_field($selected_age);
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

        if (isset($_POST['room_type'])) {
            $room_type = $_POST['room_type'];
        }

        $chosenDate = \AtollMatrix\Common::splitDateRange($reservation_date);

        $checkinDate  = '';
        $checkoutDate = '';

        if (isset($chosenDate['startDate'])) {
            $checkinDate     = $chosenDate['startDate'];
            $checkinDate_obj = new \DateTime($chosenDate['startDate']);
        }
        if (isset($chosenDate['endDate'])) {
            $checkoutDate     = $chosenDate['endDate'];
            $checkoutDate_obj = new \DateTime($chosenDate['endDate']);
        }

        $checkoutDate = date('Y-m-d', strtotime($checkoutDate . ' -1 day'));

        // Calculate the number of nights
        $staynights = $checkinDate_obj->diff($checkoutDate_obj)->days;

        $this->checkinDate  = $checkinDate;
        $this->checkoutDate = $checkoutDate;
        $this->staynights   = $staynights;

        $this->bookingSearchResults                     = array();
        $this->bookingSearchResults['bookingnumber']    = $this->bookingNumber;
        $this->bookingSearchResults['checkin']          = $this->checkinDate;
        $this->bookingSearchResults['checkout']         = $this->checkoutDate;
        $this->bookingSearchResults['staynights']       = $this->staynights;
        $this->bookingSearchResults['adults']           = $this->adultGuests;
        $this->bookingSearchResults['children']         = $this->childrenGuests;
        $this->bookingSearchResults['children_age']     = $this->children_age;
        $this->bookingSearchResults['totalguest']       = $this->totalGuests;
        $this->bookingSearchResults['chargeableguests'] = $this->totalChargeableGuests;

        // Perform your query here, this is just an example
        $result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_adults, Number of Children: $number_of_children";
        // error_log(print_r($result, true));
        $room_instance = new \AtollMatrix\Rooms();

        // Get a combined array of rooms and rates which are available for the dates.
        $combo_array = $room_instance->getAvailable_Rooms_Rates_Occupants_For_DateRange($this->checkinDate, $this->checkoutDate);

        $this->roomArray     = $combo_array['rooms'];
        $this->ratesArray    = $combo_array['rates'];
        $this->canAccomodate = $combo_array['occupants'];

        // error_log('Value of $combo_array["rooms"]:');
        // error_log(print_r($combo_array['rooms'], true));

        $availableRoomDates = array();

        $roomAvailabity = false;

        if (count($combo_array['rooms']) == 0) {

            $roomAvailabity = self::alternative_BookingDates($checkinDate, $checkoutDate);
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
        echo '<div id="reservation-data" data-bookingnumber="' . $this->bookingNumber . '" data-children="' . $this->childrenGuests . '" data-adults="' . $this->adultGuests . '" data-guests="' . $this->totalGuests . '" data-checkin="' . $this->checkinDate . '" data-checkout="' . $this->checkoutDate . '">';
        echo $list;
        echo self::register_Guest_Form();
        echo '<div id="bookingResponse" class="booking-response"></div>';
        echo self::paymentHelper_Form($this->bookingNumber);
        echo '</div>';
        $output                     = ob_get_clean();
        $response['booking_data']   = $combo_array;
        $response['roomlist']       = $output;
        $response['alt_recommends'] = $roomAvailabity;
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
        $html .= '<div id="booking-summary">';
        $html .= self::bookingSummary(
            $room_id = '',
            $booking_results[$room_id]['roomtitle'] = '',
            $this->checkinDate,
            $this->checkoutDate,
            $this->staynights,
            $this->adultGuests,
            $this->childrenGuests,
            $bedlayout = '',
            $mealplan = '',
            $choice = '',
            $total = ''
        );
        $html .= '</div>';

        // Return the resulting HTML string
        return $html;
    }

    public function can_ThisRoom_Accomodate($room_id)
    {

        $status = true;
        if ($this->canAccomodate[$room_id]['guests'] < $this->totalGuests) {
            // error_log('Cannot accomodate number of guests');
            $status = false;
        }

        if ($this->canAccomodate[$room_id]['adults'] < $this->adultGuests) {
            // error_log('Cannot accomodate number of adults');
            $status = false;
        }
        if ($this->canAccomodate[$room_id]['children'] < $this->childrenGuests) {
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
            // Get quantity and room title
            // error_log('====== can accomodate ');
            // error_log(print_r($this->canAccomodate, true));

            $can_ThisRoom_Accomodate = self::can_ThisRoom_Accomodate($id);
            if (!$can_ThisRoom_Accomodate) {
                continue;
            }

            $max_guest_number       = intval($this->canAccomodate[$id]['guests']);
            $max_child_guest_number = intval($this->canAccomodate[$id]['guests'] - 1);
            // Append a div for the room with the room ID as a data attribute
            $html .= '<div class="room-occupied-group" data-adults="' . $this->canAccomodate[$id]['adults'] . '" data-children="' . $this->canAccomodate[$id]['children'] . '" data-guests="' . $this->canAccomodate[$id]['guests'] . '" data-room-id="' . $id . '">';

            $html .= '<div class="room-details">';

            foreach ($room_info as $quantity => $title) {

                $html .= '<div class="room-details-heading">';
                // Append the room title

                $this->bookingSearchResults[$id]['roomtitle'] = $title;

                $html .= '<h2>' . $title . '</h2>';
                $html .= '</div>';
                $html .= '<div class="room-details-row">';
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

                $total_roomrate                                   = self::displayBookingTotal($id);
                $this->bookingSearchResults[$id]['totalroomrate'] = $total_roomrate;

                $html .= '<div class="room-price-total" data-roomprice="' . esc_attr($total_roomrate) . '">' . atollmatrix_price($total_roomrate) . '</div>';

                $html .= '</div>';
                $html .= '</div>';

            }

            $html .= '<div class="room-details-column">';
            $html .= '<div class="roomchoice-bedlayout">';
            $html .= '<label for="room-number-input">Bed Layout</label><br/>';
            $html .= '<input class="roomchoice" name="room[' . $id . '][quantity]" type="hidden" data-type="room-number" data-roominputid="' . $id . '" data-roomqty="' . $quantity . '" id="room-input-' . $id . '" min="0" max="' . $quantity . '" value="1">';
            $html .= self::generate_BedInformation($id);
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="room-details-column">';
            $html .= '<div class="roomchoice-mealplan">';
            $html .= self::generate_MealPlanIncluded($id);
            $html .= self::generate_MealPlanRadio($id);
            $html .= '</div>';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';

            $html .= '</div>';

            // error_log( print_r( $this->bookingSearchResults , true ));
            self::saveBooking_Transient( $this->bookingSearchResults );
        }

        return $html;
    }

    public function displayBookingPerDay($ratesArray_date)
    {
        $total_roomrate = 0;
        $html           = '';
        foreach ($ratesArray_date as $staydate => $roomrate) {
            if ($per_day) {
                $html .= '<div class="checkin-staydate"><span class="number-of-rooms"></span>' . $staydate . ' - ' . atollmatrix_price($roomrate) . '</div>';
            }

            $roomrate = self::applyPricePerPerson($roomrate);

            $total_roomrate = $total_roomrate + $roomrate;
        }

        return $html;
    }
    public function displayBookingTotal($room_id)
    {
        $total_roomrate = 0;
        $html           = '';

        // error_log('---- Person Pricing');
        // error_log(print_r($perPersonPricing, true));

        foreach ($this->ratesArray[$room_id]['date'] as $staydate => $roomrate) {

            $roomrate = self::applyPricePerPerson($roomrate);

            $this->bookingSearchResults[$room_id]['staydate'][$staydate] = $roomrate;

            $total_roomrate = $total_roomrate + $roomrate;
        }

        return $total_roomrate;
    }

    private function applyPricePerPerson($roomrate)
    {

        $perPersonPricing = atollmatrix_get_option('perpersonpricing');

        foreach ($perPersonPricing as $pricing) {
            if ($this->totalChargeableGuests == $pricing['people']) {
                if ($pricing['type'] === 'percentage' && $pricing['total'] === 'decrease') {
                    // Decrease the rate by the given percentage
                    $roomrate -= ($roomrate * $pricing['number'] / 100);
                } elseif ($pricing['type'] === 'fixed' && $pricing['total'] === 'increase') {
                    // Increase the rate by the fixed amount
                    $roomrate += $pricing['number'];
                } elseif ($pricing['type'] === 'percentage' && $pricing['total'] === 'increase') {
                    // Increase the rate by the given percentage
                    $roomrate += ($roomrate * $pricing['number'] / 100);
                } elseif ($pricing['type'] === 'fixed' && $pricing['total'] === 'decrease') {
                    // Decrease the rate by the fixed amount
                    $roomrate -= $pricing['number'];
                }
            }
        }

        return $roomrate;
    }

    public function get_BedLayout($bedLayout)
    {
        switch ($bedLayout) {
            case 'kingbed':
                $html = '<div class="guest-bed-' . $bedLayout . '"></div>';
                break;
            case 'twinbed twinbed':
                $html = '<div class="type-twinbed-twinbed-one guest-bed-' . $bedLayout . '"></div><div class="type-twinbed-twinbed-two guest-bed-' . $bedLayout . '"></div>';
                break;
            case 'kingbed twinbed':
                $html = '<div class="type-kingbed-twinbed-one guest-bed-kingbed"></div><div class="type-kingbed-twinbed-two guest-bed-twinbed"></div>';
                break;

            default:
                $html = $bedLayout;
                break;
        }

        return $html;
    }

    public function generate_BedInformation($room_id)
    {

        $html = '';

        $room_data = get_post_custom($room_id);

        if (isset($room_data["atollmatrix_alt_bedsetup"][0])) {
            $bedsetup       = $room_data["atollmatrix_alt_bedsetup"][0];
            $bedsetup_array = unserialize($bedsetup);

            $firstRoomId = array_key_first($bedsetup_array);

            foreach ($bedsetup_array as $roomId => $roomData) {
                // Get the bed layout for this room
                $bedLayout = implode(' ', $roomData['bedtype']);

                $this->bookingSearchResults[$room_id]['bedlayout'][sanitize_title($bedLayout)] = true;

                $html .= "<label>";
                $html .= "<input type='radio' name='room[$room_id][bedlayout]' value='$bedLayout'";

                // Check the first radio input by default
                if ($roomId === $firstRoomId) {
                    $html .= " checked";
                }

                $html .= ">";
                $html .= self::get_BedLayout($bedLayout);
                $html .= "</label><br>";
            }
        }

        return $html;
    }

    public function paymentHelper_Form()
    {
        $form_html = <<<HTML
			<form action="" method="post" id="paymentForm">
				<!-- Other form fields -->
				<input type="hidden" name="total" id="totalField" value="100">
				<input type="hidden" name="bookingNumber" id="bookingNumber" value="$this->bookingNumber">
				<div id="bookingPayment" class="div-button">Pay</div>
			</form>
HTML;
        return $form_html;
    }

    public function register_Guest_Form()
    {
        $country_options = atollmatrix_country_list("select", "");

        $form_html = <<<HTML
		<div class="registration_form">
			<div class="form-group">
				<label for="full_name">Full Name</label>
				<input type="text" class="form-control" id="full_name" name="full_name" >
			</div>
			<div class="form-group">
				<label for="email_address">Email Address</label>
				<input type="email" class="form-control" id="email_address" name="email_address" >
			</div>
			<div class="form-group">
				<label for="phone_number">Phone Number</label>
				<input type="tel" class="form-control" id="phone_number" name="phone_number" >
			</div>
			<div class="form-group">
				<label for="street_address">Street Address</label>
				<input type="text" class="form-control" id="street_address" name="street_address" >
			</div>
			<div class="form-group">
				<label for="city">City</label>
				<input type="text" class="form-control" id="city" name="city" >
			</div>
			<div class="form-group">
				<label for="state">State/Province</label>
				<input type="text" class="form-control" id="state" name="state">
			</div>
			<div class="form-group">
				<label for="zip_code">Zip Code</label>
				<input type="text" class="form-control" id="zip_code" name="zip_code">
			</div>
			<div class="form-group">
				<label for="country">Country</label>
				<select class="form-control" id="country" name="country" >
				$country_options
				</select>
			</div>
		</div>
HTML;

        return $form_html;
    }

    public function generate_MealPlanIncluded($room_id)
    {

        $mealPlans = atollmatrix_get_option('mealplan');

        if (is_array($mealPlans) && count($mealPlans) > 0) {
            $includedMealPlans = array();
            $optionalMealPlans = array();

            foreach ($mealPlans as $id => $plan) {
                if ($plan['choice'] === 'included') {
                    $includedMealPlans[$id] = $plan;
                } elseif ($plan['choice'] === 'optional') {
                    $optionalMealPlans[$id] = $plan;
                }
            }

            $html = '';
            if (is_array($includedMealPlans) && count($includedMealPlans) > 0) {
                $html .= '<div class="room-included-meals">';
                foreach ($includedMealPlans as $id => $plan) {
                    $html .= '<i class="fa-solid fa-square-check"></i>';
                    $html .= self::getMealPlanText($plan['mealtype']) . __(' included.', 'atollmatrix');
                    $html .= '<input hidden type="text" name="room[' . $room_id . '][meal_plan][included]" value="' . $plan['mealtype'] . '">';

                    $this->bookingSearchResults[$room_id]['meal_plan'][$plan['mealtype']] = 'included';
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
                if ($plan['choice'] === 'included') {
                    $includedMealPlans[$id] = $plan;
                } elseif ($plan['choice'] === 'optional') {
                    $optionalMealPlans[$id] = $plan;
                }
            }

            $html = '';
            if (is_array($optionalMealPlans) && count($optionalMealPlans) > 0) {
                $html .= '<div class="room-mealplans">';
                $html .= '<i class="fa-solid fa-plate-wheat"></i><br/>';
                $html .= '<h3 class="room-mealplans-heading">Mealplans</h3>';
                $html .= '<div class="room-mealplan-input">';
                $html .= '<input type="radio" data-mealprice="none" name="room[' . $room_id . '][meal_plan][optional]" value="none" checked>' . __('Not selected', 'atollmatrix') . '<br>';
                $html .= '</div>';
                foreach ($optionalMealPlans as $id => $plan) {
                    $html .= '<div class="room-mealplan-input">';
                    $mealprice = $plan['price'] * $this->staynights;
                    $html .= '<input type="radio" data-mealprice="' . $mealprice . '" name="room[' . $room_id . '][meal_plan][optional]" value="' . $plan['mealtype'] . '">' . self::getMealPlanText($plan['mealtype']) . ' ' . '<span class="room-mealplan-price">' . atollmatrix_price($mealprice) . ' +</span>';

                    $this->bookingSearchResults[$room_id]['meal_plan'][$plan['mealtype']] = $mealprice;

                    $html .= '</div>';
                }
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function getMealPlanText($mealtype)
    {
        switch ($mealtype) {
            case 'BB':
                return __('Breakfast', 'atollmatrix');
            case 'HB':
                return __('Halfboard', 'atollmatrix');
            case 'FB':
                return __('Fullboard', 'atollmatrix');
            case 'AN':
                return __('All inclusive', 'atollmatrix');
            default:
                return '';
        }
    }
}

$instance = new \AtollMatrix\Booking();