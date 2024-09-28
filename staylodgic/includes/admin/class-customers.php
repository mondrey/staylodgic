<?php

namespace Staylodgic;

class Customers
{
    
    /**
     * Method generateCustomerHtmlList
     *
     * @param $array
     *
     * @return void
     */
    public static function generateCustomerHtmlList($array)
    {
        $html = '<ul class="existing-customer">';
        foreach ($array as $key => $value) {
            if ('Country' == $key) {
                $value = \Staylodgic\Common::countryCodeToEmoji($value) . ' ' . staylodgic_country_list('display', $value);
            }
            $html .= '<li><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
    /**
     * Method get_room_names_by_customer
     *
     * @param $customer_id
     *
     * @return void
     */
    public function get_room_names_by_customer($customer_id)
    {
        $args = array(
            'post_type'   => 'slgc_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'staylodgic_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts      = get_posts($args);
        $room_names = array();

        foreach ($posts as $post) {
            $room_id = get_post_meta($post->ID, 'staylodgic_room_id', true);
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
    
    /**
     * Method get_rooms_by_customer
     *
     * @param $customer_id
     *
     * @return void
     */
    public function get_rooms_by_customer($customer_id)
    {
        $args = array(
            'post_type'   => 'slgc_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'staylodgic_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts    = get_posts($args);
        $room_ids = array();

        foreach ($posts as $post) {
            $room_id = get_post_meta($post->ID, 'staylodgic_room_id', true);
            if (!empty($room_id)) {
                $room_ids[] = $room_id;
            }
        }

        // Return the array of room ids.
        return $room_ids;
    }
    
    /**
     * Method get_booking_numbers_by_customer
     *
     * @param $customer_id
     *
     * @return void
     */
    public function get_booking_numbers_by_customer($customer_id)
    {

        $booking_numbers = array();

        $args = array(
            'post_type'   => 'slgc_reservations',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'staylodgic_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts           = get_posts($args);

        foreach ($posts as $post) {
            $booking_number = get_post_meta($post->ID, 'staylodgic_booking_number', true);
            if (!empty($booking_number)) {
                $booking_numbers[] = $booking_number;
            }
        }

        $args = array(
            'post_type'   => 'slgc_activityres',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'staylodgic_customer_id',
                    'value' => $customer_id,
                ),
            ),
        );

        $posts           = get_posts($args);

        foreach ($posts as $post) {
            $booking_number = get_post_meta($post->ID, 'staylodgic_booking_number', true);
            if (!empty($booking_number)) {
                $booking_numbers[] = $booking_number;
            }
        }

        return $booking_numbers;
    }
    
    /**
     * Method generateCustomerRooms
     *
     * @param $customer_id
     *
     * @return void
     */
    public function generateCustomerRooms($customer_id)
    {

        $custom_room = '';
        $rooms = self::get_room_names_by_customer($customer_id);

        if (is_array($rooms) && !empty($rooms)) {
            $custom_room .= '<ul>';
            // Iterate over the room names and create a list item for each one
            foreach ($rooms as $room) {
                $custom_room .= '<li>' . esc_html($room) . '</li>';
            }
            $custom_room .= '</ul>';
        } else {
            $custom_room .= __('No rooms found.', 'staylodgic');
        }

        return $custom_room;
    }
    
    /**
     * Method generateCustomerBookingNumbers
     *
     * @param $customer_id
     *
     * @return void
     */
    public function generateCustomerBookingNumbers($customer_id)
    {

        $booking_numbers = self::get_booking_numbers_by_customer($customer_id);

        if (is_array($booking_numbers) && !empty($booking_numbers)) {
            echo '<ul>';
            // Iterate over the booking numbers and create a list item for each one
            foreach ($booking_numbers as $booking_number) {
                echo '<li>' . esc_html($booking_number) . '</li>';
            }
            echo '</ul>';
        } else {
            echo __('No booking numbers found.','staylodgic');
        }
    }
}
