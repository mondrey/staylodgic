<?php
namespace Staylodgic;

class Reservations
{

    private $reservation_id;
    private $reservation_id_excluded;
    private $date;
    private $room_id;

    /**
     * Summary of __construct
     * @param mixed $date
     * @param mixed $room_id
     * @param mixed $reservation_id
     * @param mixed $reservation_id_excluded
     */
    public function __construct($date = false, $room_id = false, $reservation_id = false, $reservation_id_excluded = false)
    {
        $this->reservation_id          = $reservation_id;
        $this->reservation_id_excluded = $reservation_id_excluded;
        $this->date                    = $date;
        $this->room_id                 = $room_id;

        add_action('wp_ajax_get_AvailableRooms', array($this, 'get_AvailableRooms'));
        add_action('wp_ajax_nopriv_get_AvailableRooms', array($this, 'get_AvailableRooms'));

        add_action('wp_ajax_getBookingDetails', array($this, 'getBookingDetails'));
        add_action('wp_ajax_nopriv_getBookingDetails', array($this, 'getBookingDetails'));

    }    

    public function getBookingDetails($booking_number) {
        $booking_number = $_POST['booking_number'];
    
        $activityFound = false;

        // Fetch reservation details
        $reservationQuery = self::getReservationforBooking($booking_number);

        if ( ! $reservationQuery->have_posts() ) {
            $reservation_instance = new \Staylodgic\Activity();
            $reservationQuery     = $reservation_instance->getReservationforActivity($booking_number);

            $activityFound = true;
        }

        // Verify the nonce
        if (!isset($_POST[ 'staylodgic_bookingdetails_nonce' ]) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
    
        ob_start(); // Start output buffering
        echo "<div class='element-container-group'>";
        if ($reservationQuery->have_posts()) {
            echo "<div class='reservation-details'>";
            while ($reservationQuery->have_posts()) {
                $reservationQuery->the_post();
                $reservationID = get_the_ID();
    
                $reservation_details_status = get_post_meta($reservationID, 'staylodgic_reservation_status', true);
                // Display reservation details
                echo "<h3>Reservation ID: " . esc_html($booking_number) . "</h3>";
                if ( $activityFound ) {
                    echo "<p>Activity Date: " . esc_html(get_post_meta($reservationID, 'staylodgic_checkin_date', true)) . "</p>";

                    $guestID = self::getGuest_id_forActivity($booking_number);
                    
                } else {
                    echo "<p>Check-in Date: " . esc_html(get_post_meta($reservationID, 'staylodgic_checkin_date', true)) . "</p>";
                    echo "<p>Check-out Date: " . esc_html(get_post_meta($reservationID, 'staylodgic_checkout_date', true)) . "</p>";
                    echo "<p class='reservation-details-status-outer ".esc_attr( $reservation_details_status )."'>Status: <span class='reservation-details-status'>" . esc_html( $reservation_details_status ) . "</span></p>";

                    $guestID = self::getGuest_id_forReservation($booking_number);
                }
                
                // Add other reservation details as needed
            }
            echo "</div>";
        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }
    
        $guestID = false;
        // Fetch guest details
        if ($guestID) {
            echo "<div class='guest-details'>";
            echo "<h3>Guest Information:</h3>";
            echo "<p>Guest ID: " . esc_html($guestID) . "</p>";
            echo "<p>Full Name: " . esc_html(get_post_meta($guestID, 'staylodgic_full_name', true)) . "</p>";
            echo "<p>Email Address: " . esc_html(get_post_meta($guestID, 'staylodgic_email_address', true)) . "</p>";
            // Add other guest details as needed
            echo "</div>";
        } else {
            //echo "<p>No guest details found for Booking Number: " . esc_html($booking_number) . "</p>";
        }
        echo "</div>";

        $informationSheet = ob_get_clean(); // Get the buffer content and clean the buffer
        echo $informationSheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }

    public static function getConfirmedReservations()
    {
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'     => 'staylodgic_reservation_status',
                    'value'   => 'confirmed',
                    'compare' => '=',
                ),
            ),
        );
        return new \WP_Query($args);
    }

    public static function getReservationforBooking($booking_number)
    {
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'staylodgic_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        return new \WP_Query($args);
    }
    public static function getReservationIDforBooking($booking_number)
    {
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'staylodgic_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            return $reservation->ID;
        }

        return false; // Return an false if no reservatuib found
    }

    public function getGuest_id_forActivity($booking_number)
    {
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
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'staylodgic_customer_id', true);
            return $customer_id;
        }

        return false; // Return an empty query if no guest found
    }

    public function getGuest_id_forReservation($booking_number)
    {
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'staylodgic_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'staylodgic_customer_id', true);
            return $customer_id;
        }

        return false; // Return an empty query if no guest found
    }

    public function getGuestforReservation($booking_number)
    {
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'staylodgic_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'staylodgic_customer_id', true);

            if (!empty($customer_id)) {
                $customer_args = array(
                    'post_type'   => 'slgc_customers',
                    'p'           => $customer_id,
                    'post_status' => 'publish',
                );
                return new \WP_Query($customer_args);
            }
        }

        return new \WP_Query(); // Return an empty query if no guest found
    }

    public function getReservationsForRoom( $checkin_date = false, $checkout_date = false, $reservation_status = false, $reservation_substatus = false, $room_id = false ) {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
    
        // Start with the basic meta query for room ID
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'staylodgic_room_id',
                'value'   => $room_id,
                'compare' => '=',
            ),
        );
    
        // Add reservation status to the meta query if provided
        if ($reservation_status !== false) {
            $meta_query[] = array(
                'key'     => 'staylodgic_reservation_status',
                'value'   => $reservation_status,
                'compare' => '=',
            );
        }
    
        // Add reservation substatus to the meta query if provided
        if ($reservation_substatus !== false) {
            $meta_query[] = array(
                'key'     => 'staylodgic_reservation_substatus',
                'value'   => $reservation_substatus,
                'compare' => '=',
            );
        }
    
        // Modify the meta query for overlapping reservations
        if ($checkin_date !== false || $checkout_date !== false) {
            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key'     => 'staylodgic_checkin_date',
                    'value'   => $checkout_date,
                    'compare' => '<=',
                    'type'    => 'DATE'
                ),
                array(
                    'key'     => 'staylodgic_checkout_date',
                    'value'   => $checkin_date,
                    'compare' => '>=',
                    'type'    => 'DATE'
                ),
            );
        }
    
        $args = array(
            'post_type'      => 'slgc_reservations',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => $meta_query,
        );
    
        return new \WP_Query($args);
    }
    
     

    public function calculateReservedRooms()
    {

        $query          = $this->getReservationsForRoom();
        $reserved_rooms = 0;

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $reservation_id = get_the_ID();
                $custom         = get_post_custom($reservation_id);

                $reservation_status = get_post_meta($reservation_id, 'staylodgic_reservation_status', true);
                if ('confirmed' == $reservation_status) {
                    if (isset($custom[ 'staylodgic_checkin_date' ][ 0 ]) && isset($custom[ 'staylodgic_checkout_date' ][ 0 ])) {
                        $checkin       = strtotime($custom[ 'staylodgic_checkin_date' ][ 0 ]);
                        $checkout      = strtotime($custom[ 'staylodgic_checkout_date' ][ 0 ]);
                        $selected_date = strtotime($this->date);

                        if ($selected_date >= $checkin && $selected_date < $checkout) {
                            $reserved_rooms++;
                        }
                    }
                }
            }
        }

        wp_reset_postdata();

        return $reserved_rooms;
    }

    /**
     * Retrieves, validates, and updates the reservations array for the given room type
     */
    public function cleanup_Reservations_Array($room_id)
    {

        $reservations_array = get_post_meta($room_id, 'reservations_array', true);

        if (empty($reservations_array)) {
            $reservations_array = [];
        } else {
            $reservations_array = is_array($reservations_array) ? $reservations_array : json_decode($reservations_array, true);

            if (!is_array($reservations_array)) {
                error_log('Failed to convert reservations array to array!');
                return [];
            }

            // Filter out non-existing IDs
            foreach ($reservations_array as $date => &$ids) {
                foreach ($ids as $key => $id) {
                    if (!get_post($id)) {
                        unset($ids[$key]);
                    }
                }
            }

            // Clean up any empty arrays left after unsetting IDs
            $reservations_array = array_filter($reservations_array);

            // Update the reservations array metadata
            update_post_meta($room_id, 'reservations_array', json_encode($reservations_array));
        }

        return $reservations_array;
    }
    /**
     * Calculates and updates the remaining room count for all dates of a given room ID.
     * 
     * @param int $room_id The ID of the room.
     * @return array An associative array with dates as keys and the number of remaining rooms as values.
     */
    public function calculateAndUpdateRemainingRoomCountsForAllDates($room_id = false) {
        if (!$room_id) {
            $room_id = $this->room_id;
        }

        // Retrieve the total rooms available for the room for all dates
        $quantity_array = get_post_meta($room_id, 'quantity_array', true);

        if (!is_array($quantity_array)) {
            $quantity_array = [];
        }

        // Retrieve the reservations for the room for all dates
        $reservations_array = $this->getReservations_Array($room_id);
        if (!is_array($reservations_array)) {
            $reservations_array = [];
        }

        // // Remove this from here
        // if (is_array($reservations_array)) {

        //     // error_log(print_r($reservations_array, 1));
        
        //     foreach ($reservations_array as $date => &$ids) { // Use a reference (&) to modify the array directly
        //         foreach ($ids as $key => $id) {
        //             if (get_post($id)) {
        //                 $booking_number = get_post_meta($id, 'staylodgic_booking_number', true);
        //                 if ('' == $booking_number) {
        //                     $booking_number = '--------------------------------------------------';
        //                 }
        //                 // error_log('================');
        //                 // error_log('Date:' . $date);
        //                 // error_log('POST ID:' . $id);
        //                 // error_log('Booking Number:' . $booking_number);
        //                 // error_log('================');
        //             } else {
        //                 echo $id . ' The post does not exist.';
        //                 unset($ids[$key]); // Remove the ID from the array
        //             }
        //         }
        //     }
        
        //     // Clean up any empty arrays left after unsetting IDs
        //     $reservations_array = array_filter($reservations_array);
        
        //     // error_log('Modified reservations array:');
        //     // error_log(print_r($reservations_array, 1));
        // }
        // // remove this up to here      

        // Initialize the remaining rooms count array
        $remaining_rooms_count = self::getRemainingRoomCountArray( $room_id );
        if (!is_array($remaining_rooms_count)) {
            $remaining_rooms_count = [];
        }

        // Iterate over each date in the quantity array
        foreach ($quantity_array as $date => $total_rooms) {
            // Calculate the number of reserved rooms for the date
            $reserved_rooms = isset($reservations_array[$date]) ? count($reservations_array[$date]) : 0;

            // Calculate the remaining rooms for the date
            $remaining_rooms = $total_rooms - $reserved_rooms;

            // Update the remaining rooms count for the date
            $remaining_rooms_count[$date] = $remaining_rooms;
        }

        // Update the remaining rooms count in the metadata
        update_post_meta($room_id, 'remaining_rooms_count', json_encode($remaining_rooms_count));

        return $remaining_rooms_count;
    }



    /**
     * Calculates and updates the remaining room count for a given date and room ID.
     * 
     * @param string $date The date to check availability for.
     * @param int $room_id The ID of the room.
     * @return int The number of remaining rooms.
     */
    public function calculateAndUpdateRemainingRoomCount($date = false, $room_id = false) {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$date) {
            $date = $this->date;
        }
        // Retrieve the total rooms available for the room on the given date
        $quantity_array = get_post_meta($room_id, 'quantity_array', true);
        $total_rooms = isset($quantity_array[$date]) ? $quantity_array[$date] : 0;

        // Retrieve the reservations for the room on the given date
        $reservations_array = $this->getReservations_Array($room_id);
        
        // error_log( 'print_r( $reservations_array,1 )' );
        // error_log( print_r( $reservations_array,1 ) );
        $reserved_rooms = isset($reservations_array[$date]) ? count($reservations_array[$date]) : 0;

        // Calculate the remaining rooms
        $remaining_rooms = $total_rooms - $reserved_rooms;

        // Update the remaining rooms count in the metadata
        $remaining_rooms_count = self::getRemainingRoomCountArray( $room_id );
        if (!is_array($remaining_rooms_count)) {
            $remaining_rooms_count = [];
        }
        $remaining_rooms_count[$date] = $remaining_rooms;
        update_post_meta($room_id, 'remaining_rooms_count', json_encode($remaining_rooms_count));

        return $remaining_rooms;
    }

    /**
     * Retrieves and returns the remaining room count array for a given room ID.
     * 
     * @param int $roomId The ID of the room.
     * @return array The associative array of remaining room counts, with dates as keys.
     */
    public function getRemainingRoomCountArray( $room_id = false ) {
        if (!$room_id) {
            $room_id = $this->room_id;
        }
        // Fetch the JSON-encoded remaining rooms count from metadata
        $remainingQuantityArray_json = get_post_meta($room_id, 'remaining_rooms_count', true);

        // Decode the JSON string
        $remainingQuantityArray = json_decode($remainingQuantityArray_json, true);

        // Check if the result is an array, if not, return an empty array
        if (!is_array($remainingQuantityArray)) {
            $remainingQuantityArray = [];
        }

        return $remainingQuantityArray;
    }


    /**
     * Gets the remaining room count from the 'remaining_rooms_count' meta field for a given date and room ID.
     * 
     * @param string $date The date to check availability for.
     * @param int $room_id The ID of the room.
     * @return int The number of remaining rooms.
     */
    public function getDirectRemainingRoomCount($date = false, $room_id = false) {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$date) {
            $date = $this->date;
        }

        if ( \Staylodgic\Rooms::isChannelRoomBooked($room_id, $date) ) {
            return '0';
        }

        // Retrieve the remaining rooms count array for the room
        $remaining_rooms_count = self::getRemainingRoomCountArray( $room_id );

        // Check if the array and the specific date entry exist
        if (is_array($remaining_rooms_count) && isset($remaining_rooms_count[$date])) {
            return $remaining_rooms_count[$date];
        } else {
            // Return 0 or an appropriate default/fallback value if the data is not found
            return 0;
        }
    }

    /**
     * Calculates the total remaining rooms for all rooms on a given date.
     * 
     * @param string $date The date for which to calculate the total remaining rooms.
     * @return int The total number of remaining rooms across all rooms for the given date.
     */
    public function getTotalRemainingForAllRoomsOnDate($date = false) {

        if (!$date) {
            $date = $this->date;
        }
        $totalRemaining = 0;

        // Retrieve all rooms using the provided Rooms::queryRooms() method
        $rooms = Rooms::queryRooms();

        // Loop through each room
        foreach ($rooms as $room) {
            // Get the remaining room count array for the room
            $remainingRoomsArray = $this->getRemainingRoomCountArray($room->ID);

            // Add the remaining count for the specific date to the total
            if (isset($remainingRoomsArray[$date])) {

                if ( \Staylodgic\Rooms::isChannelRoomBooked($room->ID, $date) ) {
                    $remainingRoomsArray[$date] = 0;
                }

                $totalRemaining += $remainingRoomsArray[$date];
            }
        }

        return $totalRemaining;
    }


    public function updateRemainingRoomCount($room_id) {
        $reservations_array = $this->getReservations_Array($room_id);
        $quantity_array = get_post_meta($room_id, 'quantity_array', true);
        // error_log( 'print_r( $reservations_array,1 )' );
        // error_log( print_r( $reservations_array,1 ) );
        // Initialize remaining rooms count
        $remainingRoomsCount = [];

        // Calculate remaining rooms for each date
        foreach ($quantity_array as $date => $totalRooms) {
            $reservedRooms = isset($reservations_array[$date]) ? count($reservations_array[$date]) : 0;
            $remainingRoomsCount[$date] = $totalRooms - $reservedRooms;
        }

        // Update the remaining rooms count meta field
        update_post_meta($room_id, 'remaining_rooms_count', json_encode($remainingRoomsCount));
    }

    public function countReservationsForDay($room_id = false, $day = false, $excluded_reservation_id = false)
    {

        $occupied_count = 0;
        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$day) {
            $day = $this->date;
        }
        if (!$excluded_reservation_id) {
            $excluded_reservation_id = $this->reservation_id_excluded;
        }

        // Retrieve the reservations array for the room type
        $reservations_array_json = get_post_meta($room_id, 'reservations_array', true);

        //print_r($reservations_array_json );
        // If the reservations array is empty or not a JSON string, return 0
        if (empty($reservations_array_json) || !is_string($reservations_array_json)) {
            return 0;
        }

        // Decode the reservations array from JSON to an array
        $reservations_array = json_decode($reservations_array_json, true);

        // Check if the decoding was successful
        if ($reservations_array === null) {
            return 0;
        }
        // Check if the day exists in the reservations array
        if (isset($reservations_array[ $day ])) {
            $reservation_ids = $reservations_array[ $day ];

            // Check if the reservation IDs is an array
            if (is_array($reservation_ids)) {
                // Loop through reservation ID and see if checkout is on the same day.
                // If so don't count it as an occupied room
                foreach ($reservation_ids as $reservation_id) {

                    // If this reservation should be excluded from the count, skip this loop iteration
                    if ($reservation_id == $excluded_reservation_id) {
                        continue;
                    }

                    $checkout = $this->getCheckoutDate($reservation_id);
                    if ($day < $checkout) {
                        $occupied_count++;
                    }
                }
                return $occupied_count;
            } elseif (!empty($reservation_ids)) {
                $max_room_count = \Staylodgic\Rooms::getMaxQuantityForRoom($room_id, $day);
                return $max_room_count;
            }
        }

        return 0;
    }

    public function getNumberOfAdultsForReservation( $reservation_id = false )
    {

        if (false !== $reservation_id) {
            $this->reservation_id = $reservation_id;
        }

        $number_of_adults = get_post_meta($this->reservation_id, 'staylodgic_reservation_room_adults', true);

        if ( isset($number_of_adults) && $number_of_adults ) {
            return $number_of_adults;
        }

        return false;
    }
    public function getNumberOfChildrenForReservation( $reservation_id = false )
    {

        if (false !== $reservation_id) {
            $this->reservation_id = $reservation_id;
        }

        $number_of_children = get_post_meta($this->reservation_id, 'staylodgic_reservation_room_children', true);
        if ( isset($number_of_children['number']) && $number_of_children ) {
            return $number_of_children['number'];
        }

        return false;
    }
    public function getTotalOccupantsForReservation( $reservation_id = false )
    {

        if (false !== $reservation_id) {
            $this->reservation_id = $reservation_id;
        }

        $number_of_adults = $this->getNumberOfAdultsForReservation();
        $number_of_children = $this->getNumberOfChildrenForReservation();

        return intval( $number_of_adults ) + intval( $number_of_children );
    }

    public function getBookingDetailsPageLinkForGuest()
    {
        // Get the booking number from the reservation post meta
        $booking_page_link = staylodgic_get_option('page_bookingdetails');

        return $booking_page_link;
    }

    public function getBookingNumber()
    {
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($this->reservation_id, 'staylodgic_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return '';
        }

        return $booking_number;
    }

    public function getReservationGuestName( $booking_number = false )
    {
        // Get the booking number from the reservation post meta
        if (!$booking_number) {
            $booking_number = $this->getBookingNumber();
        }

        if (!$booking_number) {
            // Handle error if booking number not found
            return '';
        }

        // Query the customer post with the matching booking number
        $customer_query = $this->getGuestforReservation($booking_number);

        if ($customer_query->have_posts()) {
            $customer_post = $customer_query->posts[ 0 ];
            // Retrieve the guest's full name from the customer post meta
            $guest_full_name = get_post_meta($customer_post->ID, 'staylodgic_full_name', true);

            // Restore the original post data
            wp_reset_postdata();

            return $guest_full_name;
        }

        // No matching customer found and no name in reservation's metadata
        return '';
    }

    public function isGuestCurrentlyStaying()
    {
        $reservation_post_id = $this->reservation_id;
        $today_date          = date('Y-m-d'); // Get today's date

        // Get the check-in and check-out dates for the reservation
        $checkin_date  = get_post_meta($reservation_post_id, 'staylodgic_checkin_date', true);
        $checkout_date = get_post_meta($reservation_post_id, 'staylodgic_checkout_date', true);

        // Check if today's date is within the reservation period
        if ($today_date >= $checkin_date && $today_date <= $checkout_date) {
            return true; // Guest is currently staying
        } else {
            return false; // Guest is not currently staying
        }
    }

    public function isGuestCheckingInToday()
    {
        $reservation_post_id = $this->reservation_id;
        $today_date          = date('Y-m-d'); // Get today's date

        // Get the check-in date for the reservation
        $checkin_date = get_post_meta($reservation_post_id, 'staylodgic_checkin_date', true);

        // Check if today's date is the check-in date
        return $today_date === $checkin_date;
    }

    public function isGuestCheckingOutToday()
    {
        $reservation_post_id = $this->reservation_id;
        $today_date          = date('Y-m-d'); // Get today's date

        // Get the check-out date for the reservation
        $checkout_date = get_post_meta($reservation_post_id, 'staylodgic_checkout_date', true);

        // Check if today's date is the check-out date
        return $today_date === $checkout_date;
    }

    public function countReservationDays()
    {

        $reservation_post_id = $this->reservation_id;
        // Get the check-in and check-out dates for the reservation
        $checkin_date  = get_post_meta($reservation_post_id, 'staylodgic_checkin_date', true);
        $checkout_date = get_post_meta($reservation_post_id, 'staylodgic_checkout_date', true);

        // Calculate the number of days
        $datetime1 = new \DateTime($checkin_date);
        $datetime2 = new \DateTime($checkout_date);
        $interval  = $datetime1->diff($datetime2);
        $num_days  = $interval->days;

        return $num_days;
    }

    public function getReservationChannel($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $booking_channel = get_post_meta($reservation_id, 'staylodgic_booking_channel', true);

        return $booking_channel;
    }

    public function getCheckinDate($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $checkin_date = get_post_meta($reservation_id, 'staylodgic_checkin_date', true);

        return $checkin_date;
    }

    public function getCheckoutDate($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $checkout_date = get_post_meta($reservation_id, 'staylodgic_checkout_date', true);

        return $checkout_date;
    }

    public function getReservationStatus($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation status for the reservation
        $reservation_status = get_post_meta($reservation_id, 'staylodgic_reservation_status', true);

        return $reservation_status;
    }
    public function getReservationSubStatus($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation sub status for the reservation
        $reservation_substatus = get_post_meta($reservation_id, 'staylodgic_reservation_substatus', true);

        return $reservation_substatus;
    }

    public static function getRoomIDsForBooking_number($booking_number)
    {

        $rooms_query = self::getReservationforBooking($booking_number);
        $room_names  = array();

        if ($rooms_query->have_posts()) {
            while ($rooms_query->have_posts()) {
                $rooms_query->the_post();

                // Use the post property of the WP_Query object
                $room_id = get_post_meta($rooms_query->post->ID, 'staylodgic_room_id', true);

                // Use the room ID to get the room's post title
                $room_post = get_post($room_id);
                if ($room_post) {
                    $room_names[  ] = $room_post->ID;
                }
            }
            wp_reset_postdata(); // Reset the postdata
        }

        return $room_names;
    }

    public function getRoomTitleForReservation($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the room post ID from the reservation's meta data
        $room_post_id = get_post_meta($reservation_id, 'staylodgic_room_id', true);

        if ($room_post_id) {
            // Retrieve the room post using the ID
            $room_post = get_post($room_post_id);

            if ($room_post) {
                // Return the room's title
                return $room_post->post_title;
            }
        }

        // Return null if no room was found for the reservation
        return null;
    }

    /**
     * Summary of getReservationIDsForCustomer
     * @param mixed $customer_id
     * @return array
     */
    public static function getReservationIDsForCustomer($customer_id)
    {
        $args = array(
            'post_type'  => 'slgc_reservations',
            'meta_query' => array(
                array(
                    'key'     => 'staylodgic_customer_id',
                    'value'   => $customer_id,
                    'compare' => '=',
                ),
            ),
        );
        $posts           = get_posts($args);
        $reservation_ids = array();
        foreach ($posts as $post) {
            $reservation_ids[  ] = $post->ID;
        }
        return $reservation_ids;
    }

    public function getEditLinksForReservations($reservation_array)
    {
        $links = '<ul>';
        foreach ($reservation_array as $post_id) {
            $room_name = self::getRoomNameForReservation($post_id);
            $edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
            $links .= '<li><p><a href="' . $edit_link . '" title="' . $room_name . '">Edit Reservation ' . $post_id . '<br/><small>' . $room_name . '</small></a></p></li>';
        }
        $links .= '</ul>';
        return $links;
    }

    public function getCustomerEditLinkForReservation($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the customer post ID from the reservation's meta data
        $customer_post_id = get_post_meta($reservation_id, 'staylodgic_customer_id', true);

        if ($customer_post_id) {
            // Check if the customer post exists
            $customer_post = get_post($customer_post_id);
            if ($customer_post) {
                // Get the admin URL and create the link
                $edit_link = admin_url('post.php?post=' . $customer_post_id . '&action=edit');
                return '<a href="' . $edit_link . '">' . $customer_post->post_title . '</a>';
            }
        } else {
            // If customer post doesn't exist, retrieve customer name from reservation post
            $reservation_post = get_post($reservation_id);
            if ($reservation_post) {
                $customer_name = get_post_meta($reservation_id, 'staylodgic_full_name', true);
                if (!empty($customer_name)) {
                    return $customer_name;
                }
            }
        }

        // Return null if no customer was found for the reservation
        return null;
    }

    public function getRoomNameForReservation($reservation_id = false)
    {

        // Get room id from post meta
        $room_id = get_post_meta($reservation_id, 'staylodgic_room_id', true);

        // If room id exists, get the room's post title
        if ($room_id) {
            $room_post = get_post($room_id);
            if ($room_post) {
                return $room_post->post_title;
            }
        }

        return null;
    }

    public function isRoom_For_Day_Fullybooked($roomId = false, $dateString = false, $excluded_reservation_id = null)
    {

        if (!$roomId) {
            $roomId = $this->room_id;
        }
        if (!$dateString) {
            $dateString = $this->date;
        }
        if (!$excluded_reservation_id) {
            $excluded_reservation_id = $this->reservation_id_excluded;
        }

        $reserved_room_count = $this->countReservationsForDay($room_id = $roomId, $day = $dateString, $excluded_reservation_id);

        $max_count        = \Staylodgic\Rooms::getMaxQuantityForRoom($roomId, $dateString);
        $avaiblable_count = $max_count - $reserved_room_count;
        if (empty($avaiblable_count) || !isset($avaiblable_count)) {
            $avaiblable_count = 0;
        }
        if (0 == $avaiblable_count) {
            return true;
        }

        return false;
    }

    public function splitArray_By_ContinuousDays($inputArray)
    {
        $outputArray = array();

        foreach ($inputArray as $key => $dates) {
            $tempArray    = array();
            $subset       = array();
            $previousDate = null;

            foreach ($dates as $date => $value) {
                if ($previousDate === null || (strtotime($previousDate . ' + 1 day') == strtotime($date))) {
                    $subset[ $date ] = $value;
                } else {
                    $tempArray[  ] = $subset;
                    $subset        = array($date => $value);
                }

                $previousDate = $date;
            }

            $tempArray[  ]       = $subset;
            $outputArray[ $key ] = $tempArray;
        }

        return $outputArray;
    }

    public function daysFullyBooked_For_DateRange($checkin_date = false, $checkout_date = false)
    {
        // Initialize the date range
        $start     = new \DateTime($checkin_date);
        $end       = new \DateTime($checkout_date);
        $interval  = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);
    
        // Array to store daily total room availability
        $dailyRoomAvailability = array();
    
        // Query all rooms
        $room_list = \Staylodgic\Rooms::queryRooms();
    
        // Initialize the array for each date in the range
        foreach ($daterange as $date) {
            $date_string = $date->format("Y-m-d");
            $dailyRoomAvailability[$date_string] = 0;
        }
    
        // Accumulate room availability for each day
        foreach ($room_list as $room) {
            foreach ($daterange as $date) {
                $date_string = $date->format("Y-m-d");

                // Adjust the date string to be one day earlier
                $adjusted_date = new \DateTime($date_string);
                $adjusted_date->modify('-1 day');
                $adjusted_date_string = $adjusted_date->format("Y-m-d");

                //$max_room_count = \Staylodgic\Rooms::getMaxQuantityForRoom($room->ID, $date_string);
                $reservation_instance = new \Staylodgic\Reservations( $date_string, $room->ID );
                $remaining_rooms      = $reservation_instance->remainingRooms_For_Day();
                // error_log( '-------------------- Fully booked percent check');
                // error_log( $room->ID );
                // error_log( $date_string );
                // error_log( $remaining_rooms );
                // error_log( '-------------------- booked percent check');
                $dailyRoomAvailability[$date_string] += $remaining_rooms;
            }
        }
    
        // Identify fully booked days
        $fullyBookedDays = array();
        foreach ($dailyRoomAvailability as $date => $availability) {
            if ($availability === 0) {
                $fullyBookedDays[] = $date;
            }
        }
    
        return $fullyBookedDays;
    }    

    public function Availability_of_Rooms_For_DateRange($checkin_date = false, $checkout_date = false, $limit = 10)
    {
        // get the date range
        $start     = new \DateTime($checkin_date);
        $end       = new \DateTime($checkout_date);
        $interval  = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);
    
        $room_availablity = array();
    
        $room_list = \Staylodgic\Rooms::queryRooms();
    
        $count = 0;
    
        foreach ($room_list as $room) {
            foreach ($daterange as $date) {
                $date_string = $date->format("Y-m-d");
                // Check if the room is fully booked for the given date
                if (!$this->isRoom_For_Day_Fullybooked($room->ID, $date_string, $reservationid = false)) {
                    // If the room is fully booked for any of the dates in the range, return true
                    $room_availablity[$room->ID][$date_string] = '1';
                    $count++;
                    if ($count >= $limit) {
                        break 2; // Exit the loop after adding 3 rooms
                    }
                }
            }
        }
    
        // If the room is not fully booked for any of the dates in the range, return false
        $sub_set_room_availablity = self::splitArray_By_ContinuousDays($room_availablity);
    
        return $sub_set_room_availablity;
    }    

    public function isRoom_Fullybooked_For_DateRange($roomId = false, $checkin_date = false, $checkout_date = false, $reservationid = false)
    {

        if (!$roomId) {
            $roomId = $this->room_id;
        }
        if (!$reservationid) {
            $reservationid = $this->reservation_id;
        }

        // get the date range
        $start     = new \DateTime($checkin_date);
        $end       = new \DateTime($checkout_date);
        $interval  = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);

        foreach ($daterange as $date) {
            // Check if the room is fully booked for the given date
            if ($this->isRoom_For_Day_Fullybooked($roomId, $date->format("Y-m-d"), $reservationid)) {
                // If the room is fully booked for any of the dates in the range, return true
                return true;
            }
        }

        // If the room is not fully booked for any of the dates in the range, return false
        return false;
    }

    public function isConfirmed_Reservation($reservation_id)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation status for the reservation
        $reservation_status = get_post_meta($reservation_id, 'staylodgic_reservation_status', true);

        if ('confirmed' == $reservation_status) {
            return true;
        }

        return false;

    }

    // Checks if room was ever opened with a count, even zero.
    public function wasRoom_Ever_Opened($dateString = false, $room_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$dateString) {
            $dateString = $this->date;
        }

        $max_count = \Staylodgic\Rooms::getMaxQuantityForRoom($room_id, $dateString);
        return $max_count;
    }

    public function remainingRooms_For_Day($dateString = false, $room_id = false, $excluded_reservation_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$dateString) {
            $dateString = $this->date;
        }
        if (!$excluded_reservation_id) {
            $excluded_reservation_id = $this->reservation_id_excluded;
        }

        $reserved_room_count = $this->countReservationsForDay($room_id, $dateString, $excluded_reservation_id);

        $max_count        = \Staylodgic\Rooms::getMaxQuantityForRoom($room_id, $dateString);
        $avaiblable_count = $max_count - $reserved_room_count;
        if (empty($avaiblable_count) || !isset($avaiblable_count)) {
            $avaiblable_count = 0;
        }

        return $avaiblable_count;
    }

    // Function to check if a date falls within a reservation
    public function buildReservationsDataForRoomForDay( $reservations, $reservation_status = false, $reservation_substatus = false, $dateString = false, $room_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$dateString) {
            $dateString = $this->date;
        }

        $currentDate = strtotime($dateString);
        $start       = false;
        // error_log( 'print_r( $dateString,1 )');
        // error_log( print_r( $dateString,1 ));
  

        $reservation_checkin  = '';
        $reservation_checkout = '';
        $reservedRooms        = array();
        $reserved_data        = array();
        $found                = false;

        if ($reservations->have_posts()) {

                // Print the number of found posts
    //echo 'Number of posts found: ' . $reservations->found_posts;

            while ($reservations->have_posts()) {
                $reservations->the_post();

                $reservedRooms[  ] = get_the_ID();
                $reservation_id    = get_the_ID();
                $custom            = get_post_custom(get_the_ID());
                if (isset($custom[ 'staylodgic_reservation_checkin' ][ 0 ])) {
                    $dateRangeValue = $custom[ 'staylodgic_reservation_checkin' ][ 0 ];
                }
                if (isset($custom[ 'staylodgic_room_id' ][ 0 ])) {
                    $post_room_id = $custom[ 'staylodgic_room_id' ][ 0 ];
                }

                // Date will be like so $dateRangeValue = "2023-05-21 to 2023-05-24";
                //$dateRangeParts = explode(" to ", $dateRangeValue);

                $checkin  = '';
                $checkout = '';
                if (isset($custom[ 'staylodgic_checkin_date' ][ 0 ])) {
                    $checkin = $custom[ 'staylodgic_checkin_date' ][ 0 ];
                }
                if (isset($custom[ 'staylodgic_checkout_date' ][ 0 ])) {
                    $checkout = $custom[ 'staylodgic_checkout_date' ][ 0 ];
                }
                //echo '----->'.$checkin.'<-----';
                // if (count($dateRangeParts) >= 2) {
                //     $checkin = $dateRangeParts[0];
                //     $checkout = $dateRangeParts[1];
                // }

                // $checkin_start_datetime = explode(" ", $reservation_checkin);
                // $reservation_checkin_date = $checkin_start_datetime[0];

                // $checkout_start_datetime = explode(" ", $reservation_checkout);
                // $reservation_checkout_date = $checkout_start_datetime[0];

                $reservationStartDate = strtotime($checkin);
                $reservationEndDate   = strtotime($checkout);
                $numberOfDays         = floor(($reservationEndDate - $reservationStartDate) / (60 * 60 * 24)) + 1;

                // if ( $reservation_checkin_date == $date && $room_id == $roomtype ) {
                //     echo 'Reserved';
                // }

                if ($post_room_id == $room_id) {
                    // echo $currentDate . '<br/>' . $reservationStartDate . '<br/>';
                    // echo $currentDate . '<br/>' . $reservationEndDate . '<br/>';
                    // Check if the current date falls within the reservation period
                    if ($currentDate >= $reservationStartDate && $currentDate < $reservationEndDate) {
                        // Check if the reservation spans the specified number of days
                        $reservationDuration = floor(($reservationEndDate - $reservationStartDate) / (60 * 60 * 24)) + 1;
                        if ($numberOfDays > 0) {
                            if ($currentDate == $reservationStartDate) {
                                $start = 'yes';
                            } else {
                                $start = 'no';
                            }
                            $reservation_data[ 'id' ]      = $reservation_id;
                            $reservation_data[ 'checkin' ] = $reservationStartDate;
                            $reservation_data[ 'start' ]   = $start;
                            $reserved_data[  ]             = $reservation_data; // Date is part of a reservation for the specified number of days
                            $found                         = true;
                        }
                    }
                }

            }
        }

        if ($found) {
            return $reserved_data;
        } else {
            return false;
        }

    }

    public function getReservation_Customer_ID($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($reservation_id, 'staylodgic_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return '';
        }

        // Query the customer post with the matching booking number
        $customer_id = $this->getGuest_id_forReservation($booking_number);
        // No matching customer found
        return $customer_id;
    }

    public function haveCustomer($reservation_id)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($reservation_id, 'staylodgic_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return false;
        }

        // Query the customer post with the matching booking number
        $customer_query = $this->getGuestforReservation($booking_number);
        // error_log(print_r($customer_query, true));
        // Check if a customer post exists
        if ($customer_query->have_posts()) {
            // Restore the original post data
            wp_reset_postdata();

            // Return true if a matching customer post is found
            return true;
        }

        // No matching customer found, return false
        return false;
    }
    /**
     * Retrieves and validates the reservations array for the given room type
     */
    public function getReservations_Array($room_id)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }

        $reservations_array = get_post_meta($room_id, 'reservations_array', true);

        if (empty($reservations_array)) {
            $reservations_array = [  ];
        } else {
            $reservations_array = is_array($reservations_array) ? $reservations_array : json_decode($reservations_array, true);

            if (!is_array($reservations_array)) {
                error_log('Failed to convert reservations array to array!');
                return [  ];
            }
        }

        return $reservations_array;
    }

    public function get_AvailableRooms()
    {
        $checkin_date    = $_POST[ 'checkin' ];
        $checkout_date   = $_POST[ 'checkout' ];
        $reservationid   = $_POST[ 'reservationid' ];
        $available_rooms = array();

        $room_list = \Staylodgic\Rooms::queryRooms();

        foreach ($room_list as $room) {
            $is_fullybooked = $this->isRoom_Fullybooked_For_DateRange($room->ID, $checkin_date, $checkout_date, $reservation_id = $reservationid);

            // if not fully booked add to available rooms
            if (!$is_fullybooked) {
                $available_rooms[ $room->ID ] = $room->post_title; // changed here
            }
        }

        echo json_encode($available_rooms);
        wp_die(); // this is required to terminate immediately and return a proper response
    }

}

$instance = new \Staylodgic\Reservations();
