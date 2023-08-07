<?php

namespace AtollMatrix;

class Frontend
{

    public function __construct()
    {
        add_shortcode('hotel_booking_search', array($this, 'hotelBooking_SearchForm'));
        // AJAX handler to save room metadata

        add_action('wp_ajax_frontend_BookingSearch', array($this, 'frontend_BookingSearch'));
        add_action('wp_ajax_nopriv_frontend_BookingSearch', array($this, 'frontend_BookingSearch'));
    }

    public function saveBooking_Transient( $booking_number, $data ) {
        set_transient($booking_number, $data, 20 * MINUTE_IN_SECONDS);
    }

    public function hotelBooking_SearchForm()
    {
        // Generate unique booking number
        $booking_number = uniqid();
        self::saveBooking_Transient( $booking_number, '1' );
        ob_start();
        ?>
        <div id="hotel-booking-form">
            <form action="" method="post" id="hotel-booking">
                <div>
                    <input type="text" id="booking-number" value="<?php echo $booking_number; ?>" name="booking_number">
                </div>
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
                <div class="available-checkin-summary">
                    <h3>Check-in</h3>
                    <div class="pre-book-check-in"></div>
                    <h3>Last stay night</h3>
                    <div class="pre-book-stay-night"></div>
                    <h3>Check-out</h3>
                    <div class="pre-book-check-out"></div>
                    <h3>Stay Nights</h3>
                    <div class="pre-book-nights"></div>
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
        $newCheckinDate = new \DateTime($checkinDate);
        $newCheckoutDate = new \DateTime($checkoutDate);
        //$newCheckoutDate->add(new \DateInterval('P1D'));

        $reservation_instance = new \AtollMatrix\Reservations();

        $available_room_dates = $reservation_instance->Availability_of_Rooms_For_DateRange($newCheckinDate->format('Y-m-d'), $newCheckoutDate->format('Y-m-d'));

        error_log('---- Alternative Rooms Matrix Early');
        error_log(print_r($available_room_dates, true));
        $new_room_availability_array = array();

        // Process each sub-array
        foreach ($available_room_dates as $roomId => $subArray) {
            // Initialize the new sub-array for the current room
            $new_subArray = array();

            // Get the first and last keys of the inner arrays
            foreach ($subArray as $innerArray) {
                $keys = array_keys($innerArray);
                $firstKey = $keys[0];
                $lastKey = end($keys);

                // Keep only the first and last records and assign unique indexes
                $new_subArray[$firstKey] = array(
                    'check-in' => $firstKey,
                    'check-out' => $lastKey,
                );
            }

            // Add the new sub-array to the new room availability array
            $new_room_availability_array[$roomId] = $new_subArray;
        }
        $room_availabity_array = $new_room_availability_array;

        error_log('---- Alternative Room Availability Matrix Before');
        error_log(print_r($room_availabity_array, true));

        // Initialize an empty string
        $output = '';

        $processedDates = array(); // Array to store processed check-in and checkout dates
        $new_processedDates = array();

        foreach ($room_availabity_array as $key => $subset) {
            // Output the key of the subset
            error_log("Subset Key: $key\n");

            // Iterate through each sub array in the subset
            foreach ($subset as $subArray) {
                // Output the sub array
                error_log(print_r($subArray, true));
                $check_in_alt = $subArray['check-in'];
                $staylast = $subArray['check-out'];

                // Check if the current check-in and checkout dates have already been processed
                if (in_array([$check_in_alt, $staylast], $processedDates)) {
                    //error_log( 'Skipping .... ' . $check_in_alt, $staylast);
                    continue; // Skip processing identical dates
                }

                // Add the current check-in and checkout dates to the processed dates array
                $processedDates[] = [$check_in_alt, $staylast];

                // Get the date one day after the staylast
                $check_out_alt = date('Y-m-d', strtotime($staylast . ' +1 day'));

                $new_processedDates[$check_in_alt] = array(
                    'staylast' => $staylast,
                    'check-in' => $check_in_alt,
                    'check-out' => $check_out_alt,
                );

                // Perform operations with the sub array...
            }
        }

        error_log('---- Alternative Room Availability Matrix The Final');
        error_log(print_r($new_processedDates, true));
        ksort($new_processedDates);

        foreach ($new_processedDates as $key) {
            $staylast = $key['staylast'];
            $check_in_alt = $key['check-in'];
            $check_out_alt = $key['check-out'];

            // Format the dates as "Month Day" (e.g., "July 13th")
            $formattedFirstDate = date('F jS', strtotime($check_in_alt));

            $formattedNextDay = date('F jS', strtotime($check_out_alt));
            if (date('F', strtotime($staylast)) !== date('F', strtotime($check_in_alt))) {
                $formattedNextDay = date('F jS', strtotime($check_out_alt));
            } else {
                $formattedNextDay = date('jS', strtotime($check_out_alt));
            }

            $output .= "<span data-check-staylast='{$staylast}' data-check-in='{$check_in_alt}' data-check-out='{$check_out_alt}'>{$formattedFirstDate} - {$formattedNextDay}</span>, ";
        }

        // Remove the trailing comma and space
        $output = rtrim($output, ', ');

        // Print the output
        $room_availabity = '<div class="recommended-dates-wrap">' . $output . '</div>';
        error_log('---- Alternative Room Availability Matrix for Range');
        error_log(print_r($room_availabity, true));

        return $room_availabity;
    }

    public function frontend_BookingSearch()
    {
        $room_type = '';
        $number_of_children = 0;
        $number_of_adults = 0;
        $number_of_guests = 0;
        $reservation_date = '';
        $booking_number = '';

        if (isset($_POST['booking_number'])) {
            $booking_number = $_POST['booking_number'];
        }

        if (isset($_POST['reservation_date'])) {
            $reservation_date = $_POST['reservation_date'];
        }

        if (isset($_POST['number_of_adults'])) {
            $number_of_adults = $_POST['number_of_adults'];
        }

        if (isset($_POST['number_of_children'])) {
            $number_of_children = $_POST['number_of_children'];
        }

        $number_of_guests = intval($number_of_adults) + intval($number_of_children);

        if (isset($_POST['room_type'])) {
            $room_type = $_POST['room_type'];
        }

        $chosenDate = \AtollMatrix\Common::splitDateRange($reservation_date);

        $checkinDate = '';
        $checkoutDate = '';

        if (isset($chosenDate['startDate'])) {
            $checkinDate = $chosenDate['startDate'];
        }
        if (isset($chosenDate['endDate'])) {
            $checkoutDate = $chosenDate['endDate'];
        }

        $checkoutDate = date('Y-m-d', strtotime($checkoutDate . ' -1 day'));

        // Perform your query here, this is just an example
        $result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_adults, Number of Children: $number_of_children";
        error_log(print_r($result, true));
        $room_instance = new \AtollMatrix\Rooms();

        // Get a combined array of rooms and rates which are available for the dates.
        $combo_array = $room_instance->getAvailable_Rooms_Rates_Occupants_For_DateRange($checkinDate, $checkoutDate);

        error_log('Value of $combo_array["rooms"]:');
        error_log(print_r($combo_array['rooms'], true));

        $available_room_dates = array();

        $room_availabity = false;

        if (count($combo_array['rooms']) == 0) {

            $room_availabity = self::alternative_BookingDates($checkinDate, $checkoutDate);
        }

        //set_transient($booking_number, $combo_array, 20 * MINUTE_IN_SECONDS);
        // error_log(print_r($combo_array, true));
        // error_log("Rooms array");
        // error_log(print_r($room_array, true));
        // error_log("Date Range from picker");
        // error_log(print_r($checkinDate, true));
        // error_log(print_r($checkoutDate, true));

        // Always die in functions echoing AJAX content
        $list = self::listRooms_And_Quantities($combo_array);
        ob_start();
        echo '<div id="reservation-data" data-bookingnumber="' . $booking_number . '" data-children="' . $number_of_children . '" data-adults="' . $number_of_adults . '" data-guests="' . $number_of_guests . '" data-checkin="' . $checkinDate . '" data-checkout="' . $checkoutDate . '">';
        echo $list;
        echo self::register_Guest_Form();
        echo '<div id="bookingResponse" class="booking-response"></div>';
        echo self::paymentHelper_Form($booking_number);
        $output = ob_get_clean();
        $response['booking_data'] = $combo_array;
        $response['roomlist'] = $output;
        $response['alt_recommends'] = $room_availabity;
        echo json_encode($response, JSON_UNESCAPED_SLASHES);
        die();
    }

    public function listRooms_And_Quantities($combo_array)
    {

        self::saveBooking_Transient( $booking_number, $combo_array );
        $room_array = $combo_array['rooms'];
        $rates_array = $combo_array['rates'];
        $can_accomodate = $combo_array['occupants'];
        error_log('====== rates ');
        error_log(print_r($combo_array, true));
        // Initialize empty string to hold HTML
        $html = '';
        $count = 0;

        // Iterate through each room
        foreach ($room_array as $id => $room_info) {
            // Get quantity and room title
            
            $max_guest_number = intval($can_accomodate[$id]['guests'] - 1);
            // Append a div for the room with the room ID as a data attribute
            $html .= '<div class="room-occupied-group" data-adults="'.$can_accomodate[$id]['adults'].'" data-children="'.$can_accomodate[$id]['children'].'" data-guests="'.$can_accomodate[$id]['guests'].'" data-room-id="' . $id . '">';

            foreach ($room_info as $quantity => $title) {

                // Append the room title
                $html .= '<h2>' . $title . '</h2>';
                $html .= '<label for="room-number-input">Number:</label>';
                $html .= '<div class="room-input-group">';
                $html .= '<button class="room-minus-btn">-</button>';
                $html .= '<input data-roominputid="'.$id.'" data-roomqty="'.$quantity.'" type="room-number" id="room-input-'.$id.'" min="0" max="'.$quantity.'" value="0">';
                $html .= '<button class="room-plus-btn">+</button>';
                $html .= '</div>';

                $count = 0;

                for ($i=0; $i < $quantity; $i++) {

                    $html .= '<div data-roomgroup="'.$id.'" data-roomqty="'.$quantity.'" class="room-occupants-wrap room-occupants-wrap-'.$id.'-'.$count.'">';
                    $html .= '<div class="room-occupants-inner">';
                    $html .= '<div class="room-occupants">';
                    $html .= '<label for="occupant-number-input">Adults:</label>';
                    $html .= '<div class="occupant-input-group">';
                    $html .= '<button class="occupant-minus-btn">-</button>';
                    $html .= '<input type="text" data-room="'.$id.'" data-room-number="'.$count.'" class="room-occupants occupant-adults" data-occupant="adults-input-'.$id.'" data-type="adults" min="1" id="adults-input-'.$id.'['.$count.'][]" value="1">';
                    $html .= '<button class="occupant-plus-btn">+</button>';
                    $html .= '</div>';
    
                    if ( $can_accomodate[$id]['children'] <> 0 ) {
                        $html .= '<label for="occupant-number-input">Children:</label>';
                        $html .= '<div class="occupant-input-group">';
                        $html .= '<button class="occupant-minus-btn">-</button>';
                        $html .= '<input type="text" data-room="'.$id.'" data-room-number="'.$count.'" class="room-occupants occupant-children" data-occupant="children-input-'.$id.'" data-type="children" min="0" id="children-input-'.$id.'['.$count.'][]" value="0">';
                        $html .= '<button class="occupant-plus-btn">+</button>';
                        for ($ageinputs=0; $ageinputs < $max_guest_number; $ageinputs++) {
                            $html .= '<input disabled data-room="'.$id.'" data-room-number="'.$count.'" class="room-occupants occupant-children-age occupant-children-age-'.$id.'" data-type="children-age" id="children-age-input-'.$id.'-'.$ageinputs.'" name="children-age-input-'.$id.'['.$ageinputs.'][]" type="number" placeholder="Enter age">';
                        }
                        $html .= '</div>';
                    }

                    $html .= '<hr/>';
    
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';

                    $count++;
                }

                // Append a select element for the quantity
                $html .= '<select data-room-id="' . $id . '" name="room_quantity">';
                // Append an option for each possible quantity
                for ($i = 0; $i <= $quantity; $i++) {
                    $html .= '<option value="' . $i . '">' . $i . '</option>';
                }
                $html .= '</select>';

            }

            $html .= '<div class="checkin-staydate-wrap">';
            
            $total_roomrate = 0;
            foreach ($rates_array[$id]['date'] as $staydate => $roomrate) {
                $html .= '<div class="checkin-staydate"><span class="number-of-rooms"></span>' . $staydate . ' - ' . $roomrate . '</div>';
                $total_roomrate = $total_roomrate + $roomrate;
            }
            
            $html .= '<div class="checkin-staydate-total">' . atollmatrix_price( $total_roomrate ) . '</div>';
            $html .= '</div>';

            $html .= '</div>';
        }

        // Return the resulting HTML string
        return $html;
    }

    public function paymentHelper_Form($booking_number)
    {
        $form_html = <<<HTML
			<form action="" method="post" id="paymentForm">
				<!-- Other form fields -->
				<input type="hidden" name="total" id="totalField" value="100">
				<input type="hidden" name="booking_number" id="booking_number" value="$booking_number">
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
			<div class="form-group">
				<div id="bookingRegister" class="div-button">Book</div>
			</div>
		</div>
HTML;

        return $form_html;
    }
}

$instance = new \AtollMatrix\Frontend();