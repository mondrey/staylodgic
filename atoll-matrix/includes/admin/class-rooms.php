<?php

namespace AtollMatrix;

class Rooms
{

    public function __construct($date = false, $room_id = false, $reservation_id = false, $reservation_id_excluded = false)
    {
        // AJAX handler to save room metadata
        add_action('wp_ajax_update_RoomAvailability', array($this, 'update_RoomAvailability'));
        add_action('wp_ajax_nopriv_update_RoomAvailability', array($this, 'update_RoomAvailability'));

        // AJAX handler to save room metadata
        add_action('wp_ajax_update_RoomRate', array($this, 'update_RoomRate'));
        add_action('wp_ajax_nopriv_update_RoomRate', array($this, 'update_RoomRate'));
    }

    public static function queryRooms()
    {
        $rooms = get_posts(
            array(
                'post_type' => 'atmx_room',
                'orderby' => 'title',
                'numberposts' => -1,
                'order' => 'ASC',
                'post_status' => 'publish',
            )
        );
        return $rooms;
    }

    public static function getRoomList()
    {
        $roomlist = [];
        $rooms = self::queryRooms(); // Call queryRooms() method here
        if ($rooms) {
            foreach ($rooms as $key => $list) {
                $roomlist[$list->ID] = $list->post_title;
            }
        } else {
            $roomlist[0] = "Rooms not found.";
        }
        return $roomlist;
    }

    public static function isChannelRoomBooked($room_id, $dateString)
    {
        $channelArray = get_post_meta($room_id, 'channel_quantity_array', true);

        // Check if the channel_quantity_array exists and the quanitity field is available
        if (!empty($channelArray) && isset($channelArray['quantity'])) {
            $quantityArray = $channelArray['quantity'];
        }

        // Check if the quantity_array exists and the date is available
        if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
            if ( '0' == $quantityArray[$dateString] ) {
                return true;
            }
        }

        return false;
    }

    public static function getTotalRoomQtyForDate($room_id, $dateString)
    {

        $quantityArray = get_post_meta($room_id, 'quantity_array', true);

        $reservation_instance = new \AtollMatrix\Reservations($dateString, $room_id);
        $remaining = $reservation_instance->getDirectRemainingRoomCount( $dateString, $room_id );

        // Check if the quantity_array exists and the date is available
        if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
            return $quantityArray[$dateString] + $remaining;
        }

        return false;
    }

    public static function getMaxQuantityForRoom($room_id, $dateString)
    {

        if ( self::isChannelRoomBooked($room_id, $dateString) ) {
            return '0';
        }
        $quantityArray = get_post_meta($room_id, 'quantity_array', true);

        // Check if the quantity_array exists and the date is available
        if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
            return $quantityArray[$dateString];
        }

        return false;
    }

    public static function getRoomName_FromID($room_id)
    {
        $room_post = get_post($room_id);
        if ($room_post) {
            $room_name = $room_post->post_title;
        }

        return $room_name;
    }

    public static function getRoomNames_FromIDs($room_ids)
    {
        $room_names = array();

        foreach ($room_ids as $room_id) {
            // Use the room ID to get the room's post title
            $room_post = get_post($room_id);
            if ($room_post) {
                $room_names[] = $room_post->post_title;
            }
        }

        $room_names_list = '<ul>';
        foreach ($room_names as $room_name) {
            $room_names_list .= '<li>' . $room_name . '</li>';
        }
        $room_names_list .= '</ul>';

        return $room_names_list;
    }

    public function getAvailable_Rooms_For_DateRange($checkin_date, $checkout_date)
    {
        $available_rooms = array();

        // get all rooms
        $room_list = $this->queryRooms();

        foreach ($room_list as $room) {
            $count = $this->getMaxRoom_QTY_For_DateRange($room->ID, $checkin_date, $checkout_date, $reservationid = '');

            // if not fully booked add to available rooms
            if ($count !== 0) {
                $available_rooms[$room->ID][$count] = $room->post_title; // changed here
            }
        }

        return $available_rooms;
    }

    public function getAvailable_Rooms_Rates_Occupants_For_DateRange($checkin_date, $checkout_date)
    {
        $combo_array = array();
        $available_rooms = array();
        $available_roomrates = array();
        $can_accomodate = array();

        // get all rooms
        $room_list = self::queryRooms();

        foreach ($room_list as $room) {
            $count = self::getMaxRoom_QTY_For_DateRange($room->ID, $checkin_date, $checkout_date, $reservationid = '');

            // if not fully booked add to available rooms
            if ($count !== 0) {
                $available_rooms[$room->ID][$count] = $room->post_title; // changed here
                error_log('fetching rates for :' . $room->ID);
                $available_roomrates[$room->ID] = self::getRoom_RATE_For_DateRange($room->ID, $checkin_date, $checkout_date);
                // Get room occupany max numbers
                $can_accomodate[$room->ID] = self::getMax_room_occupants($room->ID);
                error_log(print_r($available_roomrates, true));
            }
        }

        $combo_array = array(
            'rooms' => $available_rooms,
            'rates' => $available_roomrates,
            'occupants' => $can_accomodate
        );

        return $combo_array;
    }

    public function getRoom_RATE_For_DateRange($roomId, $checkin_date, $checkout_date)
    {
        $start = new \DateTime($checkin_date);
        $end = new \DateTime($checkout_date);

        // Add one day to the end date
        $end->add(new \DateInterval('P1D'));

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);

        $rates_daterange = array();
        $roomrate_instance = new \AtollMatrix\Rates();

        $total_rate = 0;

        foreach ($daterange as $date) {
            //error_log('This is the room ' . $roomId . ' for ' . $date->format("Y-m-d"));
            $rate = $roomrate_instance->getRoomRateByDate($roomId, $date->format("Y-m-d"));
            $rates_daterange['date'][$date->format("Y-m-d")] = $rate;
            $total_rate = $total_rate + $rate;
        }

        $rates_daterange['total'] = $total_rate;

        // error_log(print_r($rates_daterange, true));

        return $rates_daterange;
    }

    public function getMax_room_occupants($room_id)
    {

        $max_children   = '999';
        $max_adults     = '999';
        $max_guests     = 0;
        $can_occomodate = array();
        $can_occomodate = array();


        $room_data = get_post_custom($room_id);
        if (isset($room_data["atollmatrix_max_adult_limit_status"][0])) {
            $adult_limit_status = $room_data["atollmatrix_max_adult_limit_status"][0];
            if ('1' == $adult_limit_status) {
                $max_adults = $room_data["atollmatrix_max_adults"][0];
            } else {
                $max_adults = '999';
            }
        }
        if (isset($room_data["atollmatrix_max_children_limit_status"][0])) {
            $children_limit_status = $room_data["atollmatrix_max_children_limit_status"][0];
            if ('1' == $children_limit_status) {
                $max_children = $room_data["atollmatrix_max_children"][0];
            } else {
                $max_children = '999';
            }
        }
        if (isset($room_data["atollmatrix_max_guests"][0])) {
            $max_guests = $room_data["atollmatrix_max_guests"][0];
        }

        $can_occomodate['adults']   = $max_adults;
        $can_occomodate['children'] = $max_children;
        $can_occomodate['guests']   = $max_guests;

        return $can_occomodate;

    }

    public function getMaxRoom_QTY_For_DateRange($roomId, $checkin_date, $checkout_date, $reservationid)
    {
        // get the date range
        $start = new \DateTime($checkin_date);
        $end = new \DateTime($checkout_date);
        // Add one day to the end date
        $end->add(new \DateInterval('P1D'));

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($start, $interval, $end);

        $max_count = PHP_INT_MAX;

        foreach ($daterange as $date) {
            // Check if the room is fully booked for the given date
            $count = $this->getMaxRoom_QTY_ForDay($roomId, $date->format("Y-m-d"), $reservationid);
            //error_log('QTY check for rooms:::::::: ' . $date->format("Y-m-d"));
            if ($count < $max_count) {
                $max_count = $count;
            }
        }

        // If no count was ever set, return false or whatever default value you need
        if ($max_count == PHP_INT_MAX) {
            return false;
        }

        // If the room is not fully booked for any of the dates in the range, return max_count
        return $max_count;
    }

    public function getMaxRoom_QTY_ForDay($roomId, $dateString, $excluded_reservation_id = null)
    {

        $reservation_instance = new \AtollMatrix\Reservations($dateString, $roomId, $reservation_id = false, $excluded_reservation_id);
        $reserved_room_count = $reservation_instance->countReservationsForDay();

        $max_count = \AtollMatrix\Rooms::getMaxQuantityForRoom($roomId, $dateString);
        $avaiblable_count = $max_count - $reserved_room_count;
        if (empty($avaiblable_count) || !isset($avaiblable_count)) {
            $avaiblable_count = 0;
        }
        return $avaiblable_count;
    }

    public function update_RoomAvailability()
    {
        if (isset($_POST['dateRange'])) {
            $dateRange = $_POST['dateRange'];
        } else {
            // Return an error response if dateRange is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing date range parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        if (isset($_POST['quantity'])) {
            $quantity = $_POST['quantity'];
        } else {
            // Return an error response if quantity is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing quantity parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        if (isset($_POST['postID'])) {
            $postID = $_POST['postID'];
        } else {
            // Return an error response if postID is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing post ID parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        // Split the date range into start and end dates
        $dateRangeArray = explode(" to ", $dateRange);
        if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
            // Use the single date as both start and end date
            $startDate = $dateRangeArray[0];
            $endDate = $startDate;
        } elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
            // Return an error response if dateRange is invalid
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Invalid date range.',
                ),
            );
            wp_send_json_error($response);
            return;
        } else {
            $startDate = $dateRangeArray[0];
            $endDate = $dateRangeArray[1];
        }

        // If the end date is empty, set it to the start date
        if (empty($endDate)) {
            $endDate = $startDate;
        }

        // Retrieve the existing quantity_array meta value
        $quantityArray = get_post_meta($postID, 'quantity_array', true);

        // If the quantity_array is not an array, initialize it as an empty array
        if (!is_array($quantityArray)) {
            $quantityArray = array();
        }

        // Generate an array of dates between the start and end dates
        $dateRange = \AtollMatrix\Common::create_inBetween_DateRange_Array($startDate, $endDate);

        // Update the quantity values for the specified date range
        foreach ($dateRange as $date) {

            $reservation_instance = new \AtollMatrix\Reservations($date, $postID);
            $reserved_rooms = $reservation_instance->calculateReservedRooms();

            $final_quantity = $quantity + $reserved_rooms;
            $quantityArray[$date] = $final_quantity;
        }

        // Update the metadata for the 'atmx_reservations' post
        if (!empty($postID) && is_numeric($postID)) {
            // Update the post meta with the modified quantity array
            update_post_meta($postID, 'quantity_array', $quantityArray);
            // Return a success response
            $response = array(
                'success' => true,
                'data' => array(
                    'message' => 'Room availability updated successfully.',
                ),
            );
            wp_send_json_success($response);
        } else {
            // Return an error response
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Invalid post ID.',
                ),
            );
            wp_send_json_error($response);
        }

        wp_die(); // Optional: Terminate script execution
    }

    public function update_RoomRate()
    {
        if (isset($_POST['dateRange'])) {
            $dateRange = $_POST['dateRange'];
        } else {
            // Return an error response if dateRange is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing date range parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        if (isset($_POST['rate'])) {
            $rate = $_POST['rate'];
        } else {
            // Return an error response if quantity is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing rate parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        if (isset($_POST['postID'])) {
            $postID = $_POST['postID'];
        } else {
            // Return an error response if postID is not set
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Missing post ID parameter.',
                ),
            );
            wp_send_json_error($response);
            return;
        }

        // Split the date range into start and end dates
        $dateRangeArray = explode(" to ", $dateRange);
        if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
            // Use the single date as both start and end date
            $startDate = $dateRangeArray[0];
            $endDate = $startDate;
        } elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
            // Return an error response if dateRange is invalid
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Invalid date range.',
                ),
            );
            wp_send_json_error($response);
            return;
        } else {
            $startDate = $dateRangeArray[0];
            $endDate = $dateRangeArray[1];
        }

        // If the end date is empty, set it to the start date
        if (empty($endDate)) {
            $endDate = $startDate;
        }

        // Retrieve the existing roomrate_array meta value
        $roomrateArray = get_post_meta($postID, 'roomrate_array', true);

        // If the quantity_array is not an array, initialize it as an empty array
        if (!is_array($roomrateArray)) {
            $roomrateArray = array();
        }

        // Generate an array of dates between the start and end dates
        $dateRange = \AtollMatrix\Common::create_inBetween_DateRange_Array($startDate, $endDate);

        // Update the quantity values for the specified date range
        foreach ($dateRange as $date) {
            $roomrateArray[$date] = $rate;
        }

        // Update the metadata for the 'atmx_reservations' post
        if (!empty($postID) && is_numeric($postID)) {
            // Update the post meta with the modified quantity array
            update_post_meta($postID, 'roomrate_array', $roomrateArray);
            // Return a success response
            $response = array(
                'success' => true,
                'data' => array(
                    'message' => 'Room rates updated successfully.',
                ),
            );
            wp_send_json_success($response);
        } else {
            // Return an error response
            $response = array(
                'success' => false,
                'data' => array(
                    'message' => 'Invalid post ID.',
                ),
            );
            wp_send_json_error($response);
        }

        wp_die(); // Optional: Terminate script execution
    }
}

$instance = new \AtollMatrix\Rooms();