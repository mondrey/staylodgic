<?php
function cognitive_hotel_booking_search_form() {
    ob_start();
    ?>
    <div id="hotel-booking-form">
        <form action="" method="post" id="hotel-booking">
            <div>
                <label for="reservation-date">Book Date:</label>
                <input type="date" id="reservation-date" name="reservation_date">
            </div>
            <div>
                <label for="number-of-guests">Number of Adults:</label>
                <input type="number" id="number-of-guests" name="number_of_guests" min="1">
            </div>
            <div class="children-number" data-agelimitofchild="13">
                <label for="number-of-children">Number of Children:</label>
                <input id="number-of-children" name="number_of_children" min="0">
            </div>
            <div id="bookingSearch" class="div-button">Search</div>

            <div class="available-list">
                <div id="available-list-ajax"></div>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('hotel_booking_search', 'cognitive_hotel_booking_search_form');

add_action('wp_ajax_cognitive_frontend_booking_search', 'cognitive_frontend_booking_search');
add_action('wp_ajax_nopriv_cognitive_frontend_booking_search', 'cognitive_frontend_booking_search');
function cognitive_frontend_booking_search() {
    // The $_POST contains our form data
    $reservation_date = $_POST['reservation_date'];
    $number_of_guests = $_POST['number_of_guests'];
    $number_of_children = $_POST['number_of_children'];
    $room_type = $_POST['room_type'];

    $chosenDate = cognitive_splitDateRange( $reservation_date );

    $checkinDate = $chosenDate['startDate'];
    $checkoutDate = $chosenDate['endDate'];
    
    // Perform your query here, this is just an example
    $result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_guests, Number of Children: $number_of_children";
    $room_array = cognitive_get_available_rooms_for_date_range( $checkinDate, $checkoutDate );
    // Always die in functions echoing AJAX content
    $list = cognitive_list_rooms_and_quantities($room_array);
    ob_start();
    echo '<div id="reservation-data" data-children="' .$number_of_children. '" data-adults="' .$number_of_guests. '" data-checkin="' . $checkinDate . '" data-checkout="' . $checkoutDate . '">';
    echo $list;
    echo cognitive_register_guest_form();
    echo '<div id="bookingResponse" class="booking-response"></div>';
	$output = ob_get_clean();
	echo $output;
    die();
}

function cognitive_list_rooms_and_quantities($room_array) {
    // Initialize empty string to hold HTML
    $html = '';
    
    // Iterate through each room
    foreach ($room_array as $id => $room_info) {
        // Get quantity and room title
        foreach ($room_info as $quantity => $title) {
            // Append a div for the room with the room ID as a data attribute
            $html .= '<div data-room-id="' . $id . '">';
            // Append the room title
            $html .= '<h2>' . $title . '</h2>';
            // Append a select element for the quantity
            $html .= '<select name="room_quantity">';
            // Append an option for each possible quantity
            for ($i = 0; $i <= $quantity; $i++) {
                $html .= '<option value="' . $i . '">' . $i . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
        }
    }
    
    // Return the resulting HTML string
    return $html;
}

function cognitive_register_guest_form() {
    $country_options = themecore_country_list("select", "");

    $form_html = <<<HTML
    <div class="registration_form">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="email_address">Email Address</label>
            <input type="email" class="form-control" id="email_address" name="email_address" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
        </div>
        <div class="form-group">
            <label for="street_address">Street Address</label>
            <input type="text" class="form-control" id="street_address" name="street_address" required>
        </div>
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control" id="city" name="city" required>
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
            <select class="form-control" id="country" name="country" required>
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






