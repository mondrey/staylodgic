<?php
namespace Staylodgic;

class Data
{

    public function __construct()
    {
        // Hook into the save_post action
        add_action('save_post', array($this, 'updateReservationsArray_On_Save'), 13, 3);
        add_action('save_post', array($this, 'createActivitiesCustomer_On_Save'), 13, 3);
        // Hook into the wp_trash_post action
        add_action('wp_trash_post', array($this, 'removeReservation_From_Array'));
        add_action('trashed_post', array($this, 'removeReservation_From_Array'));
        add_action('save_post', array($this, 'check_post_status_and_remove_reservation'));
    }

    public function check_post_status_and_remove_reservation($post_id) {
        // Check if this is an autosave or a revision.
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
            return;
        }
    
        // Get the post object.
        $post = get_post($post_id);
    
        // Check if the post status is 'draft'.
        if ($post->post_status == 'draft') {
            // Call your function to remove the reservation from the array.
            $this->removeReservation_From_Array($post_id);
        }
    }

    public static function getCustomer_MetaData($customer_array, $customer_post_id)
    {
        $output = array();

        // Loop through the customer array
        foreach ($customer_array as $item) {
            if ('seperator' !== $item[ 'type' ]) {
                // Get the meta value for the current item's 'id'
                $meta_value = get_post_meta($customer_post_id, $item[ 'id' ], true);
                // Add an entry to the output array, with 'name' as the key and the meta value as the value
                $output[ $item[ 'name' ] ] = $meta_value;
            }
        }

        return $output;
    }

    public function initiateCustomerSave( $post_id, $post, $update ) {
        $customer_choice    = get_post_meta($post_id, 'staylodgic_customer_choice', true);
        $booking_number     = get_post_meta($post_id, 'staylodgic_booking_number', true);
        $existing_customer  = get_post_meta($post_id, 'staylodgic_existing_customer', true);

        $full_name = get_post_meta($post_id, 'staylodgic_full_name', true);

        // Check if customer post exists
        // error_log("customer_choice: " . $customer_choice . '||' . $booking_number);
        $customer_id = get_post_meta($post_id, 'staylodgic_customer_id', true);
        // error_log("checking customer post: " . $customer_id . '||' . $post_id . '||' . $full_name);

        if (\Staylodgic\Common::isCustomer_valid_post($existing_customer)) {
            if ('existing' == $customer_choice) {

                // error_log("Updating: " . $existing_customer . '||' . $booking_number);
                update_post_meta($post_id, 'staylodgic_customer_id', $existing_customer);

            }
        }

        // Check if the post is being trashed
        if ($post->post_status === 'trash') {
            return; // Exit the function if the post is being trashed
        }

        if (!\Staylodgic\Common::isCustomer_valid_post($customer_id)) {
            if ('existing' !== $customer_choice) {
                // error_log("Customer does not exist: " . $customer_id . '||' . $full_name);
                // Create new customer from the filled inputs in reservation
                self::create_Customer_From_Reservation_Post($post_id);
            }
        }
    }

    /**
     * Triggered when a post is saved. If the post type is 'slgc_reservations' and is not autosaved or revision, it updates the reservation details.
     */
    public function createActivitiesCustomer_On_Save($post_id, $post, $update)
    {

        error_log("It is here " . $post_id);

        if (!\Staylodgic\Common::isActivities_valid_post($post_id, $post)) {
            return;
        }

        $this->initiateCustomerSave($post_id, $post, $update);

    }

    public function create_Customer_From_Reservation_Post($reservation_post_id)
    {
        // Retrieve the reservation post using the ID
        $reservation_post = get_post($reservation_post_id);
        $customer_post_id = false;

        if (!$reservation_post) {
            // Handle error if reservation post not found
            return;
        }

        // Retrieve the necessary post meta data from the reservation post
        $full_name       = get_post_meta($reservation_post_id, 'staylodgic_full_name', true);
        $email_address   = get_post_meta($reservation_post_id, 'staylodgic_email_address', true);
        $phone_number    = get_post_meta($reservation_post_id, 'staylodgic_phone_number', true);
        $street_address  = get_post_meta($reservation_post_id, 'staylodgic_street_address', true);
        $city            = get_post_meta($reservation_post_id, 'staylodgic_city', true);
        $state           = get_post_meta($reservation_post_id, 'staylodgic_state', true);
        $zip_code        = get_post_meta($reservation_post_id, 'staylodgic_zip_code', true);
        $country         = get_post_meta($reservation_post_id, 'staylodgic_country', true);
        $booking_number  = get_post_meta($reservation_post_id, 'staylodgic_booking_number', true);
        $customer_choice = get_post_meta($reservation_post_id, 'staylodgic_customer_choice', true);

        if ('existing' !== $customer_choice) {
            if ('' !== $full_name) {
                error_log("Customer saving: " . $reservation_post_id . '||' . $full_name . '||' . $booking_number);
                // Create customer post
                $customer_post_data = array(
                    'post_type' => 'slgc_customers', // Your custom post type for customers
                    'post_title' => $full_name, // Set the customer's full name as post title
                    'post_status' => 'publish', // The status you want to give new posts
                    'meta_input' => array(
                        'staylodgic_full_name'      => $full_name,
                        'staylodgic_email_address'  => $email_address,
                        'staylodgic_phone_number'   => $phone_number,
                        'staylodgic_street_address' => $street_address,
                        'staylodgic_city'           => $city,
                        'staylodgic_state'          => $state,
                        'staylodgic_zip_code'       => $zip_code,
                        'staylodgic_country'        => $country,
                        // add other meta data you need
                    ),
                );

                // Insert the post
                $customer_post_id = wp_insert_post($customer_post_data);
            }
        }

        if (!$customer_post_id) {
            // Handle error while creating customer post
            return;
        }

        // Update the reservation post with the customer post ID
        update_post_meta($reservation_post_id, 'staylodgic_customer_id', $customer_post_id);
    }

    function removeReservation_From_Array($post_id)
    {
        // Check if the post is of the "reservations" post type
        if (get_post_type($post_id) === 'slgc_reservations') {
            $room_type           = get_post_meta($post_id, 'staylodgic_room_id', true);
            $reservation_post_id = $post_id;

            // Call the remove_reservation_from_array function
            self::removeReservation_ID($room_type, $reservation_post_id);

            // Update the remaining room count
            if ($room_type) {
                $reservation_instance = new \Staylodgic\Reservations();
                try {
                    $reservation_instance->updateRemainingRoomCount($room_type);
                } catch (\Exception $e) {
                    // Handle exceptions or log errors
                    error_log("Error updating remaining room count: " . $e->getMessage());
                }
            }
        }
    }

    public function removeReservation_ID($room_type, $reservation_post_id)
    {
        // Retrieve the reservations array for the room type
        $reservations_array_json = get_post_meta($room_type, 'staylodgic_reservations_array', true);

        // Check if the reservations array is empty or not a JSON string
        if (empty($reservations_array_json) || !is_string($reservations_array_json)) {
            return;
        }

        // Decode the reservations array from JSON to an array
        $reservations_array = json_decode($reservations_array_json, true);

        // Check if the decoding was successful
        if ($reservations_array === null) {
            return;
        }

        // Convert the reservation post ID to a string for comparison
        $reservation_post_id = (string) $reservation_post_id;

        // Iterate over each date in the reservations array
        foreach ($reservations_array as $date => &$reservation_ids) {
            // Check if the reservation_ids is a JSON string
            if (is_string($reservation_ids)) {
                $reservation_ids = json_decode($reservation_ids, true);
            }

            if (is_array($reservation_ids)) {
                // Check if the reservation post ID exists in the array
                $index = array_search($reservation_post_id, $reservation_ids);
                if ($index !== false) {
                    // Remove the reservation post ID from the array
                    unset($reservation_ids[$index]);
                    // Reset the array keys
                    $reservation_ids = array_values($reservation_ids);
                }
            }

            // Check if there are no more reservation IDs in the array
            if (empty($reservation_ids)) {
                // Remove the date from the reservations array
                unset($reservations_array[$date]);
            }
        }

        // Encode the reservations array back to JSON
        $reservations_array_json = json_encode($reservations_array);
        // Update the reservations array meta field

        update_post_meta($room_type, 'staylodgic_reservations_array', $reservations_array_json);
        // $reservations_array_json = get_post_meta($room_type, 'staylodgic_reservations_array', true);
        // print_r( $reservations_array_json );die();
    }

    /**
     * Triggered when a post is saved. If the post type is 'slgc_reservations' and is not autosaved or revision, it updates the reservation details.
     */
    public function updateReservationsArray_On_Save($post_id, $post, $update)
    {

        // error_log("is here " . $post_id);

        if (!\Staylodgic\Common::isReservation_valid_post($post_id, $post)) {
            return;
        }

        $room_type          = get_post_meta($post_id, 'staylodgic_room_id', true);
        $checkin_date       = get_post_meta($post_id, 'staylodgic_checkin_date', true);
        $checkout_date      = get_post_meta($post_id, 'staylodgic_checkout_date', true);
        $reservation_status = get_post_meta($post_id, 'staylodgic_reservation_status', true);
        $customer_choice    = get_post_meta($post_id, 'staylodgic_customer_choice', true);
        $booking_number     = get_post_meta($post_id, 'staylodgic_booking_number', true);
        $existing_customer  = get_post_meta($post_id, 'staylodgic_existing_customer', true);

        $full_name = get_post_meta($post_id, 'staylodgic_full_name', true);

        self::removeReservationID_From_All_Rooms($post_id); // Remove the reservation from all rooms

        $reservation_instance = new \Staylodgic\Reservations();
        if ($reservation_instance->isConfirmed_Reservation($post_id)) {
            // Add reservation to the new room type
            self::updateReservationsArray_On_Change($room_type, $checkin_date, $checkout_date, $post_id);
        }

        $this->initiateCustomerSave($post_id, $post, $update);

        // Check if the post is being trashed
        if ($post->post_status === 'trash') {
            return; // Exit the function if the post is being trashed
        }
        
        // Assuming room_type is the ID of the room associated with the reservation
        if ($room_type) {
            $reservation_instance = new \Staylodgic\Reservations();
            $reservation_instance->updateRemainingRoomCount($room_type);
        }
    }

    /**
     * Updates the reservations array when changes are made to a reservation post.
     */
    public static function updateReservationsArray_On_Change($room_id, $checkin_date, $checkout_date, $reservation_post_id)
    {
        $reservation_instance = new \Staylodgic\Reservations();
        $reservations_array   = $reservation_instance->getReservations_Array($room_id);

        $previous_checkin_date  = get_post_meta($reservation_post_id, 'staylodgic_previous_checkin_date', true);
        $previous_checkout_date = get_post_meta($reservation_post_id, 'staylodgic_previous_checkout_date', true);

        // Adjust the checkout dates to be one day earlier
        $previous_checkout_date = date('Y-m-d', strtotime($previous_checkout_date . ' -1 day'));
        $adjusted_checkout_date = date('Y-m-d', strtotime($checkout_date . ' -1 day'));

        $previous_dates = \Staylodgic\Common::getDates_Between($previous_checkin_date, $previous_checkout_date);
        $updated_dates  = \Staylodgic\Common::getDates_Between($checkin_date, $adjusted_checkout_date);

        $reservations_array = self::removeDates_From_ReservationsArray($previous_dates, $reservation_post_id, $reservations_array);
        $reservations_array = self::addDates_To_ReservationsArray($updated_dates, $reservation_post_id, $reservations_array);

        update_post_meta($room_id, 'staylodgic_reservations_array', json_encode($reservations_array));
        update_post_meta($reservation_post_id, 'staylodgic_previous_checkin_date', $checkin_date);
        update_post_meta($reservation_post_id, 'staylodgic_previous_checkout_date', $checkout_date); // Keeping original checkout date for records
    }


    /**
     * Remove dates from the reservations array for a given reservation post ID.
     */
    public static function removeDates_From_ReservationsArray($dates, $reservation_post_id, $reservations_array)
    {
        foreach ($dates as $date) {
            if (isset($reservations_array[$date])) {
                $reservation_ids = $reservations_array[$date];
                if (($key = array_search($reservation_post_id, $reservation_ids)) !== false) {
                    unset($reservations_array[$date][$key]);
                    // Reset the array keys
                    $reservations_array[$date] = array_values($reservations_array[$date]);
                }
            }
        }

        return $reservations_array;
    }

    /**
     * Add dates to the reservations array for a given reservation post ID.
     */
    public static function addDates_To_ReservationsArray($dates, $reservation_post_id, $reservations_array)
    {
        foreach ($dates as $date) {
            if (isset($reservations_array[$date])) {
                if (is_array($reservations_array[$date])) {
                    $reservations_array[$date][] = $reservation_post_id;
                } else {
                    $reservations_array[$date] = [$reservations_array[$date], $reservation_post_id];
                }
            } else {
                $reservations_array[$date] = [$reservation_post_id];
            }
        }

        return $reservations_array;
    }

    /**
     * Remove the reservation ID from the entire array
     */
    public static function removeIDs_From_ReservationsArray($reservation_post_id, $reservations_array)
    {
        foreach ($reservations_array as $date => &$reservations) {
            foreach ($reservations as $key => $id) {
                if ($id == $reservation_post_id) {
                    unset($reservations[$key]);
                }
            }
            // Reset the array keys
            $reservations = array_values($reservations);
        }

        return $reservations_array;
    }

    /**
     * Remove the reservation from all rooms.
     */
    public static function removeReservationID_From_All_Rooms($reservation_post_id)
    {
        $room_types = get_posts(['post_type' => 'slgc_room']);
        //error_log("remove reservation_from_all_rooms is called with ID: " . $reservation_post_id);
        foreach ($room_types as $room) {

            $reservation_instance = new \Staylodgic\Reservations();
            $reservations_array   = $reservation_instance->getReservations_Array($room->ID);

            if (!empty($reservations_array)) {
                //error_log("Before removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));

                $reservations_array = self::removeIDs_From_ReservationsArray($reservation_post_id, $reservations_array);

                //error_log("After removing ID {$reservation_post_id} from room {$room->ID}: " . print_r($reservations_array, true));
            }

            update_post_meta($room->ID, 'staylodgic_reservations_array', json_encode($reservations_array));
        }
    }
}
$instance = new \Staylodgic\Data();
