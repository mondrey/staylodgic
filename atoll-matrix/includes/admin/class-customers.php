<?php
namespace AtollMatrix;

class Customers
{

    public static function generateCustomerHtmlList($array)
    {
        $html = "<ul class='existing-customer'>";
        foreach ($array as $key => $value) {
            if ('Country' == $key) {
                $value = \AtollMatrix\Common::countryCodeToEmoji($value) . ' ' . atollmatrix_country_list('display', $value);
            }
            $html .= "<li><strong>{$key}:</strong> {$value}</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    public function get_room_names_by_customer($customer_id)
    {
        $args = array(
            'post_type'   => 'atmx_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'atollmatrix_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts      = get_posts($args);
        $room_names = array();

        foreach ($posts as $post) {
            $room_id = get_post_meta($post->ID, 'atollmatrix_room_id', true);
            if (!empty($room_id)) {
                // Fetch the room post by its ID.
                $room_post = get_post($room_id);
                // If the post exists and is published.
                if ($room_post && $room_post->post_status === 'publish') {
                    // Get the title of the post.
                    $room_name    = $room_post->post_title;
                    $room_names[] = $room_name;
                }
            }
        }

        // Return the array of room names.
        return $room_names;
    }

    public function get_rooms_by_customer($customer_id)
    {
        $args = array(
            'post_type'   => 'atmx_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'atollmatrix_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts    = get_posts($args);
        $room_ids = array();

        foreach ($posts as $post) {
            $room_id = get_post_meta($post->ID, 'atollmatrix_room_id', true);
            if (!empty($room_id)) {
                $room_ids[] = $room_id;
            }
        }

        // Return the array of room ids.
        return $room_ids;
    }

    public function get_booking_numbers_by_customer($customer_id)
    {
        $args = array(
            'post_type'   => 'atmx_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'atollmatrix_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts           = get_posts($args);
        $booking_numbers = array();

        foreach ($posts as $post) {
            $booking_number = get_post_meta($post->ID, 'atollmatrix_booking_number', true);
            if (!empty($booking_number)) {
                $booking_numbers[] = $booking_number;
            }
        }

        return $booking_numbers;
    }

    public function generateCustomerRooms($customer_id)
    {

        $rooms = self::get_room_names_by_customer($customer_id);

        if (is_array($rooms) && !empty($rooms)) {
            echo "<ul>";
            // Iterate over the room names and create a list item for each one
            foreach ($rooms as $room) {
                echo "<li>" . $room . "</li>";
            }
            echo "</ul>";
        } else {
            echo "No rooms found.";
        }
    }

    public function generateCustomerBookingNumbers($customer_id)
    {

        $booking_numbers = self::get_booking_numbers_by_customer($customer_id);

        if (is_array($booking_numbers) && !empty($booking_numbers)) {
            echo "<ul>";
            // Iterate over the booking numbers and create a list item for each one
            foreach ($booking_numbers as $booking_number) {
                echo "<li>" . $booking_number . "</li>";
            }
            echo "</ul>";
        } else {
            echo "No booking numbers found.";
        }
    }

}
