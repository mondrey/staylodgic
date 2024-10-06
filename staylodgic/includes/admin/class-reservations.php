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
    
    /**
     * Method getBookingDetails
     *
     * @param $booking_number
     *
     * @return void
     */
    public function getBookingDetails($booking_number) {
        $booking_number = $_POST['booking_number'];
    
        $activityFound = false;

        // Fetch reservation details
        $stay_reservation_query = self::get_reservationfor_booking($booking_number);

        if ( ! $stay_reservation_query->have_posts() ) {
            $reservation_instance = new \Staylodgic\Activity();
            $stay_reservation_query     = $reservation_instance->get_reservation_for_activity($booking_number);

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
        if ($stay_reservation_query->have_posts()) {
            echo "<div class='reservation-details'>";
            while ($stay_reservation_query->have_posts()) {
                $stay_reservation_query->the_post();
                $stay_reservation_id = get_the_ID();
    
                $reservation_details_status = get_post_meta($stay_reservation_id, 'staylodgic_reservation_status', true);
                // Display reservation details
                echo "<h3>Reservation ID: " . esc_html($booking_number) . "</h3>";
                if ( $activityFound ) {
                    echo "<p>Activity Date: " . esc_html(get_post_meta($stay_reservation_id, 'staylodgic_checkin_date', true)) . "</p>";

                    $stay_guest_id = self::get_guest_id_for_activity($booking_number);
                    
                } else {
                    echo "<p>Check-in Date: " . esc_html(get_post_meta($stay_reservation_id, 'staylodgic_checkin_date', true)) . "</p>";
                    echo "<p>Check-out Date: " . esc_html(get_post_meta($stay_reservation_id, 'staylodgic_checkout_date', true)) . "</p>";
                    echo "<p class='reservation-details-status-outer ".esc_attr( $reservation_details_status )."'>Status: <span class='reservation-details-status'>" . esc_html( $reservation_details_status ) . "</span></p>";

                    $stay_guest_id = self::get_guest_id_for_reservation($booking_number);
                }
                
                // Add other reservation details as needed
            }
            echo "</div>";
        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }
    
        $stay_guest_id = false;
        // Fetch guest details
        if ($stay_guest_id) {
            echo "<div class='guest-details'>";
            echo "<p>Full Name: " . esc_html(get_post_meta($stay_guest_id, 'staylodgic_full_name', true)) . "</p>";
            echo "<p>Email Address: " . esc_html(get_post_meta($stay_guest_id, 'staylodgic_email_address', true)) . "</p>";
            // Add other guest details as needed
            echo "</div>";
        } else {
            // No guest details found
        }
        echo "</div>";

        $information_sheet = ob_get_clean(); // Get the buffer content and clean the buffer
        echo $information_sheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }
    
    /**
     * Method getConfirmedReservations
     *
     * @return void
     */
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
    
    /**
     * Method get_reservationfor_booking
     *
     * @param $booking_number
     *
     * @return void
     */
    public static function get_reservationfor_booking($booking_number)
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
        
    /**
     * Method get_reservation_id_for_booking
     *
     * @param $booking_number
     *
     * @return void
     */
    public static function get_reservation_id_for_booking($booking_number)
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
    
    /**
     * Method get_guest_id_for_activity
     *
     * @param $booking_number
     *
     * @return void
     */
    public function get_guest_id_for_activity($booking_number)
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
    
    /**
     * Method get_guest_id_for_reservation
     *
     * @param $booking_number
     *
     * @return void
     */
    public function get_guest_id_for_reservation($booking_number)
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
    
    /**
     * Method getGuestforReservation
     *
     * @param $booking_number
     *
     * @return void
     */
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
    
    /**
     * Method get_reservations_for_room
     *
     * @param $checkin_date
     * @param $checkout_date
     * @param $reservation_status
     * @param $reservation_substatus
     * @param $room_id $room_id
     *
     * @return void
     */
    public function get_reservations_for_room( $checkin_date = false, $checkout_date = false, $reservation_status = false, $reservation_substatus = false, $room_id = false ) {

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
    
    /**
     * Method getRoomReservationsForDateRange
     *
     * @param $stay_start_date
     * @param $stay_end_date
     * @param $the_room_id
     *
     * @return void
     */
    public function getRoomReservationsForDateRange( $stay_start_date, $stay_end_date, $the_room_id )
    {

        $query          = $this->get_reservations_for_room( $stay_start_date, $stay_end_date, $reservation_status = 'confirmed', $reservation_substatus = false, $the_room_id );
        $reserved_rooms = 0;

        $reserved_array = array();

        $date_range = \Staylodgic\Common::create_in_between_date_range_array($stay_start_date, $stay_end_date);
        
        // Set all as zero
        foreach ($date_range as $date) {
            $reserved_array[$date] = 0;
        }

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $reservation_id = get_the_ID();
                $custom         = get_post_custom($reservation_id);

                if (isset($custom[ 'staylodgic_checkin_date' ][ 0 ]) && isset($custom[ 'staylodgic_checkout_date' ][ 0 ])) {
                    $checkin       = $custom[ 'staylodgic_checkin_date' ][ 0 ];
                    $checkout      = $custom[ 'staylodgic_checkout_date' ][ 0 ];

                    foreach ($date_range as $date) {
                        $reserved_rooms = 0;
                        if ( isset( $reserved_array[$date] )) {
                            $reserved_rooms = $reserved_array[$date];
                        }
                        if ($date >= $checkin && $date < $checkout) {
                            $reserved_rooms++;
                        }
                        $reserved_array[$date] = $reserved_rooms;
                    }
                }
                
            }
        }

        wp_reset_postdata();

        return $reserved_array;
    }
    
    /**
     * Method calculateReservedRooms
     *
     * @return void
     */
    public function calculateReservedRooms()
    {

        $query          = $this->get_reservations_for_room();
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
     * Method Retrieves, validates, and updates the reservations array for the given room type
     *
     * @param $room_id $room_id [explicite description]
     *
     * @return void
     */
    public function cleanup_Reservations_Array($room_id)
    {

        $reservations_array = get_post_meta($room_id, 'staylodgic_reservations_array', true);

        if (empty($reservations_array)) {
            $reservations_array = [];
        } else {
            $reservations_array = is_array($reservations_array) ? $reservations_array : json_decode($reservations_array, true);

            if (!is_array($reservations_array)) {
                // Failed to convert reservations array to array
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
            update_post_meta($room_id, 'staylodgic_reservations_array', json_encode($reservations_array));
        }

        return $reservations_array;
    }
    /**
     * Calculates and updates the remaining room count for all dates of a given room ID.
     * 
     * @param int $room_id The ID of the room.
     * @return array An associative array with dates as keys and the number of remaining rooms as values.
     */
    public function calculate_and_update_remaining_room_counts_for_all_dates($room_id = false) {
        if (!$room_id) {
            $room_id = $this->room_id;
        }

        // Retrieve the total rooms available for the room for all dates
        $quantity_array = get_post_meta($room_id, 'staylodgic_quantity_array', true);

        if (!is_array($quantity_array)) {
            $quantity_array = [];
        }

        // Retrieve the reservations for the room for all dates
        $reservations_array = $this->get_reservations_array($room_id);
        if (!is_array($reservations_array)) {
            $reservations_array = [];
        }

        // Initialize the remaining rooms count array
        $remaining_rooms_count = self::get_remaining_room_count_array( $room_id );
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
        update_post_meta($room_id, 'staylodgic_remaining_rooms_count', json_encode($remaining_rooms_count));

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
        $quantity_array = get_post_meta($room_id, 'staylodgic_quantity_array', true);
        $total_rooms = isset($quantity_array[$date]) ? $quantity_array[$date] : 0;

        // Retrieve the reservations for the room on the given date
        $reservations_array = $this->get_reservations_array($room_id);
        
        $reserved_rooms = isset($reservations_array[$date]) ? count($reservations_array[$date]) : 0;

        // Calculate the remaining rooms
        $remaining_rooms = $total_rooms - $reserved_rooms;

        // Update the remaining rooms count in the metadata
        $remaining_rooms_count = self::get_remaining_room_count_array( $room_id );
        if (!is_array($remaining_rooms_count)) {
            $remaining_rooms_count = [];
        }
        $remaining_rooms_count[$date] = $remaining_rooms;
        update_post_meta($room_id, 'staylodgic_remaining_rooms_count', json_encode($remaining_rooms_count));

        return $remaining_rooms;
    }

    /**
     * Retrieves and returns the remaining room count array for a given room ID.
     * 
     * @param int $stay_room_id The ID of the room.
     * @return array The associative array of remaining room counts, with dates as keys.
     */
    public function get_remaining_room_count_array( $room_id = false ) {
        if (!$room_id) {
            $room_id = $this->room_id;
        }
        // Fetch the JSON-encoded remaining rooms count from metadata
        $remainingQuantityArray_json = get_post_meta($room_id, 'staylodgic_remaining_rooms_count', true);

        // Decode the JSON string
        $remaining_quantity_array = json_decode($remainingQuantityArray_json, true);

        // Check if the result is an array, if not, return an empty array
        if (!is_array($remaining_quantity_array)) {
            $remaining_quantity_array = [];
        }

        return $remaining_quantity_array;
    }


    /**
     * Gets the remaining room count from the 'staylodgic_remaining_rooms_count' meta field for a given date and room ID.
     * 
     * @param string $date The date to check availability for.
     * @param int $room_id The ID of the room.
     * @return int The number of remaining rooms.
     */
    public function get_direct_remaining_room_count($date = false, $room_id = false) {

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
        $remaining_rooms_count = self::get_remaining_room_count_array( $room_id );

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

        // Retrieve all rooms using the provided Rooms::query_rooms() method
        $rooms = Rooms::query_rooms();

        // Loop through each room
        foreach ($rooms as $room) {
            // Get the remaining room count array for the room
            $remainingRoomsArray = $this->get_remaining_room_count_array($room->ID);

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

    
    /**
     * Method update_remaining_room_count
     *
     * @param $room_id $room_id [explicite description]
     *
     * @return void
     */
    public function update_remaining_room_count($room_id) {
        $reservations_array = $this->get_reservations_array($room_id);
        $quantity_array = get_post_meta($room_id, 'staylodgic_quantity_array', true);
        
        // Initialize remaining rooms count
        $remainingRoomsCount = [];

        // Calculate remaining rooms for each date
        foreach ($quantity_array as $date => $totalRooms) {
            $reservedRooms = isset($reservations_array[$date]) ? count($reservations_array[$date]) : 0;
            $remainingRoomsCount[$date] = $totalRooms - $reservedRooms;
        }

        // Update the remaining rooms count meta field
        update_post_meta($room_id, 'staylodgic_remaining_rooms_count', json_encode($remainingRoomsCount));
    }
    
    /**
     * Method countReservationsForDay
     *
     * @param $room_id $room_id [explicite description]
     * @param $day $day [explicite description]
     * @param $excluded_reservation_id $excluded_reservation_id [explicite description]
     *
     * @return void
     */
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
        $reservations_array_json = get_post_meta($room_id, 'staylodgic_reservations_array', true);

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

                    $checkout = $this->get_checkout_date($reservation_id);
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
    
    /**
     * Method get_number_of_adults_for_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_number_of_adults_for_reservation( $reservation_id = false )
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

    /**
     * Method get_number_of_children_for_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_number_of_children_for_reservation( $reservation_id = false )
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
        
    /**
     * Method get_total_occupants_for_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_total_occupants_for_reservation( $reservation_id = false )
    {

        if (false !== $reservation_id) {
            $this->reservation_id = $reservation_id;
        }

        $number_of_adults = $this->get_number_of_adults_for_reservation();
        $number_of_children = $this->get_number_of_children_for_reservation();

        return intval( $number_of_adults ) + intval( $number_of_children );
    }
    
    /**
     * Method get_booking_details_page_link_for_guest
     *
     * @return void
     */
    public function get_booking_details_page_link_for_guest()
    {
        // Get the booking number from the reservation post meta
        $booking_page_link = home_url('/booking-details/');

        return $booking_page_link;
    }
    
    /**
     * Method get_booking_number
     *
     * @return void
     */
    public function get_booking_number()
    {
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($this->reservation_id, 'staylodgic_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return '';
        }

        return $booking_number;
    }
    
    /**
     * Method get_reservation_guest_name
     *
     * @param $booking_number $booking_number [explicite description]
     *
     * @return void
     */
    public function get_reservation_guest_name( $booking_number = false )
    {
        // Get the booking number from the reservation post meta
        if (!$booking_number) {
            $booking_number = $this->get_booking_number();
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
    
    /**
     * Method isGuestCurrentlyStaying
     *
     * @return void
     */
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
    
    /**
     * Method isGuestCheckingInToday
     *
     * @return void
     */
    public function isGuestCheckingInToday()
    {
        $reservation_post_id = $this->reservation_id;
        $today_date          = date('Y-m-d'); // Get today's date

        // Get the check-in date for the reservation
        $checkin_date = get_post_meta($reservation_post_id, 'staylodgic_checkin_date', true);

        // Check if today's date is the check-in date
        return $today_date === $checkin_date;
    }
    
    /**
     * Method isGuestCheckingOutToday
     *
     * @return void
     */
    public function isGuestCheckingOutToday()
    {
        $reservation_post_id = $this->reservation_id;
        $today_date          = date('Y-m-d'); // Get today's date

        // Get the check-out date for the reservation
        $checkout_date = get_post_meta($reservation_post_id, 'staylodgic_checkout_date', true);

        // Check if today's date is the check-out date
        return $today_date === $checkout_date;
    }
    
    /**
     * Method count_reservation_days
     *
     * @return void
     */
    public function count_reservation_days()
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
    
    /**
     * Method get_reservation_channel
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_reservation_channel($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $booking_channel = get_post_meta($reservation_id, 'staylodgic_booking_channel', true);

        return $booking_channel;
    }
    
    /**
     * Method get_checkin_date
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_checkin_date($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $checkin_date = get_post_meta($reservation_id, 'staylodgic_checkin_date', true);

        return $checkin_date;
    }
    
    /**
     * Method get_checkout_date
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_checkout_date($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the check-in and check-out dates for the reservation
        $checkout_date = get_post_meta($reservation_id, 'staylodgic_checkout_date', true);

        return $checkout_date;
    }
    
    /**
     * Method get_reservation_status
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_reservation_status($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation status for the reservation
        $reservation_status = get_post_meta($reservation_id, 'staylodgic_reservation_status', true);

        return $reservation_status;
    }
       
    /**
     * Method get_reservation_sub_status
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_reservation_sub_status($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation sub status for the reservation
        $reservation_substatus = get_post_meta($reservation_id, 'staylodgic_reservation_substatus', true);

        return $reservation_substatus;
    }
    
    /**
     * Method getRoomIDsForBooking_number
     *
     * @param $booking_number $booking_number [explicite description]
     *
     * @return void
     */
    public static function getRoomIDsForBooking_number($booking_number)
    {

        $rooms_query = self::get_reservationfor_booking($booking_number);
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
    
    /**
     * Method getRoomTitleForReservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
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
    
    /**
     * Method getEditLinksForReservations
     *
     * @param $reservation_array $reservation_array [explicite description]
     *
     * @return void
     */
    public function getEditLinksForReservations($reservation_array)
    {
        $links = '<ul>';
        foreach ($reservation_array as $post_id) {
            $room_name = self::get_room_name_for_reservation($post_id);
            $edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
            $links .= '<li><p><a href="' . $edit_link . '" title="' . $room_name . '">Edit Reservation ' . $post_id . '<br/><small>' . $room_name . '</small></a></p></li>';
        }
        $links .= '</ul>';
        return $links;
    }
    
    /**
     * Method get_customer_edit_link_for_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_customer_edit_link_for_reservation($reservation_id = false)
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
    
    /**
     * Method get_room_name_for_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_room_name_for_reservation($reservation_id = false)
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
    
    /**
     * Method is_room_for_the_day_fullybooked
     *
     * @param $stay_room_id $stay_room_id [explicite description]
     * @param $stay_date_string $stay_date_string [explicite description]
     * @param $excluded_reservation_id $excluded_reservation_id [explicite description]
     *
     * @return void
     */
    public function is_room_for_the_day_fullybooked($stay_room_id = false, $stay_date_string = false, $excluded_reservation_id = null)
    {

        if (!$stay_room_id) {
            $stay_room_id = $this->room_id;
        }
        if (!$stay_date_string) {
            $stay_date_string = $this->date;
        }
        if (!$excluded_reservation_id) {
            $excluded_reservation_id = $this->reservation_id_excluded;
        }

        $reserved_room_count = $this->countReservationsForDay($room_id = $stay_room_id, $day = $stay_date_string, $excluded_reservation_id);

        $max_count        = \Staylodgic\Rooms::getMaxQuantityForRoom($stay_room_id, $stay_date_string);
        $avaiblable_count = $max_count - $reserved_room_count;
        if (empty($avaiblable_count) || !isset($avaiblable_count)) {
            $avaiblable_count = 0;
        }
        if (0 == $avaiblable_count) {
            return true;
        }

        return false;
    }
    
    /**
     * Method splitArray_By_ContinuousDays
     *
     * @param $inputArray $inputArray [explicite description]
     *
     * @return void
     */
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
    
    /**
     * Method days_fully_booked_for_date_range
     *
     * @param $checkin_date $checkin_date [explicite description]
     * @param $checkout_date $checkout_date [explicite description]
     *
     * @return void
     */
    public function days_fully_booked_for_date_range($checkin_date = false, $checkout_date = false)
    {
        // Initialize the date range
        $start     = new \DateTime($checkin_date);
        $end       = new \DateTime($checkout_date);
        $interval  = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);
    
        // Array to store daily total room availability
        $dailyRoomAvailability = array();
    
        // Query all rooms
        $room_list = \Staylodgic\Rooms::query_rooms();
    
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

                $reservation_instance = new \Staylodgic\Reservations( $date_string, $room->ID );
                $remaining_rooms      = $reservation_instance->remaining_rooms_for_day();
                
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
    
    /**
     * Method availability_of_rooms_for_date_range
     *
     * @param $checkin_date $checkin_date [explicite description]
     * @param $checkout_date $checkout_date [explicite description]
     * @param $limit $limit [explicite description]
     *
     * @return void
     */
    public function availability_of_rooms_for_date_range($checkin_date = false, $checkout_date = false, $limit = 10)
    {
        // get the date range
        $start     = new \DateTime($checkin_date);
        $end       = new \DateTime($checkout_date);
        $interval  = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);
    
        $room_availablity = array();
    
        $room_list = \Staylodgic\Rooms::query_rooms();
    
        $count = 0;
    
        foreach ($room_list as $room) {
            foreach ($daterange as $date) {
                $date_string = $date->format("Y-m-d");
                // Check if the room is fully booked for the given date
                if (!$this->is_room_for_the_day_fullybooked($room->ID, $date_string, $reservationid = false)) {
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
    
    /**
     * Method isRoom_Fullybooked_For_DateRange
     *
     * @param $stay_room_id $stay_room_id [explicite description]
     * @param $checkin_date $checkin_date [explicite description]
     * @param $checkout_date $checkout_date [explicite description]
     * @param $reservationid $reservationid [explicite description]
     *
     * @return void
     */
    public function isRoom_Fullybooked_For_DateRange($stay_room_id = false, $checkin_date = false, $checkout_date = false, $reservationid = false)
    {

        if (!$stay_room_id) {
            $stay_room_id = $this->room_id;
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
            if ($this->is_room_for_the_day_fullybooked($stay_room_id, $date->format("Y-m-d"), $reservationid)) {
                // If the room is fully booked for any of the dates in the range, return true
                return true;
            }
        }

        // If the room is not fully booked for any of the dates in the range, return false
        return false;
    }
    
    /**
     * Method is_confirmed_reservation
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function is_confirmed_reservation($reservation_id)
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

    /**
     * Method Checks if room was ever opened with a count, even zero.   
     *
     * @param $stay_date_string $stay_date_string [explicite description]
     * @param $room_id $room_id [explicite description]
     *
     * @return void
     */
    public function was_room_ever_opened($stay_date_string = false, $room_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$stay_date_string) {
            $stay_date_string = $this->date;
        }

        $max_count = \Staylodgic\Rooms::getMaxQuantityForRoom($room_id, $stay_date_string);
        return $max_count;
    }
    
    /**
     * Method remaining_rooms_for_day
     *
     * @param $stay_date_string $stay_date_string [explicite description]
     * @param $room_id $room_id [explicite description]
     * @param $excluded_reservation_id $excluded_reservation_id [explicite description]
     *
     * @return void
     */
    public function remaining_rooms_for_day($stay_date_string = false, $room_id = false, $excluded_reservation_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$stay_date_string) {
            $stay_date_string = $this->date;
        }
        if (!$excluded_reservation_id) {
            $excluded_reservation_id = $this->reservation_id_excluded;
        }

        $reserved_room_count = $this->countReservationsForDay($room_id, $stay_date_string, $excluded_reservation_id);

        $max_count        = \Staylodgic\Rooms::getMaxQuantityForRoom($room_id, $stay_date_string);
        $avaiblable_count = $max_count - $reserved_room_count;
        if (empty($avaiblable_count) || !isset($avaiblable_count)) {
            $avaiblable_count = 0;
        }

        return $avaiblable_count;
    }

    /**
     * Method Function to check if a date falls within a reservation  
     *
     * @param $reservations $reservations [explicite description]
     * @param $reservation_status $reservation_status [explicite description]
     * @param $reservation_substatus $reservation_substatus [explicite description]
     * @param $stay_date_string $stay_date_string [explicite description]
     * @param $room_id $room_id [explicite description]
     *
     * @return void
     */
    public function build_reservations_data_for_room_for_day( $reservations, $reservation_status = false, $reservation_substatus = false, $stay_date_string = false, $room_id = false)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }
        if (!$stay_date_string) {
            $stay_date_string = $this->date;
        }

        $stay_current_date = strtotime($stay_date_string);
        $start       = false;

        $reservation_checkin  = '';
        $reservation_checkout = '';
        $reservedRooms        = array();
        $reserved_data        = array();
        $found                = false;

        if ($reservations->have_posts()) {

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

                $checkin  = '';
                $checkout = '';
                if (isset($custom[ 'staylodgic_checkin_date' ][ 0 ])) {
                    $checkin = $custom[ 'staylodgic_checkin_date' ][ 0 ];
                }
                if (isset($custom[ 'staylodgic_checkout_date' ][ 0 ])) {
                    $checkout = $custom[ 'staylodgic_checkout_date' ][ 0 ];
                }

                $reservation_start_date = strtotime($checkin);
                $reservation_end_date   = strtotime($checkout);
                $numberOfDays         = floor(($reservation_end_date - $reservation_start_date) / (60 * 60 * 24)) + 1;

                if ($post_room_id == $room_id) {
                    // Check if the current date falls within the reservation period
                    if ($stay_current_date >= $reservation_start_date && $stay_current_date < $reservation_end_date) {
                        // Check if the reservation spans the specified number of days
                        $reservationDuration = floor(($reservation_end_date - $reservation_start_date) / (60 * 60 * 24)) + 1;
                        if ($numberOfDays > 0) {
                            if ($stay_current_date == $reservation_start_date) {
                                $start = 'yes';
                            } else {
                                $start = 'no';
                            }
                            $reservation_data[ 'id' ]      = $reservation_id;
                            $reservation_data[ 'checkin' ] = $reservation_start_date;
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
    
    /**
     * Method get_reservation_customer_id
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function get_reservation_customer_id($reservation_id = false)
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
        $customer_id = $this->get_guest_id_for_reservation($booking_number);
        // No matching customer found
        return $customer_id;
    }
    
    /**
     * Method have_customer
     *
     * @param $reservation_id $reservation_id [explicite description]
     *
     * @return void
     */
    public function have_customer($reservation_id)
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
     * Method Retrieves and validates the reservations array for the given room type
     *
     * @param $room_id $room_id [explicite description]
     *
     * @return void
     */
    public function get_reservations_array($room_id)
    {

        if (!$room_id) {
            $room_id = $this->room_id;
        }

        $reservations_array = get_post_meta($room_id, 'staylodgic_reservations_array', true);

        if (empty($reservations_array)) {
            $reservations_array = [  ];
        } else {
            $reservations_array = is_array($reservations_array) ? $reservations_array : json_decode($reservations_array, true);

            if (!is_array($reservations_array)) {
                // Failed to convert reservations array to array!
                return [  ];
            }
        }

        return $reservations_array;
    }
    
    /**
     * Method get_AvailableRooms
     *
     * @return void
     */
    public function get_AvailableRooms()
    {
        $checkin_date    = $_POST[ 'checkin' ];
        $checkout_date   = $_POST[ 'checkout' ];
        $reservationid   = $_POST[ 'reservationid' ];
        $available_rooms = array();

        $room_list = \Staylodgic\Rooms::query_rooms();

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
