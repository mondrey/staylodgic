<?php
function cognitive_get_room_names_from_ids($room_ids) {
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

function cognitive_extend_admin_search_join($join) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit.php page
    if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'customers' && $_GET['s'] != '') {
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'cognitive_extend_admin_search_join');

function cognitive_extend_admin_search_where($where) {
    global $pagenow, $wpdb;

    if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'customers' && $_GET['s'] != '') {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_key = 'pagemeta_booking_number' AND " . $wpdb->postmeta . ".meta_value LIKE $1)", 
            $where
        );
    }

    return $where;
}
add_filter('posts_where', 'cognitive_extend_admin_search_where');

function cognitive_get_edit_links_for_reservations($reservation_array) {
    $links = '<ul>';
    foreach ($reservation_array as $post_id) {
        $room_name = cognitive_get_room_name_for_reservation($post_id);
        $edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
        $links .= '<li><a href="' . $edit_link . '" title="' . $room_name . '">Edit Reservation ' . $post_id . '</a></li>';
    }
    $links .= '</ul>';
    return $links;
}


function cognitive_book_rooms() {

    // Check if our nonce is set.
    if ( ! isset( $_POST['nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['nonce'], 'themecore-nonce-search' ) ) {
        return;
    }

    // Generate unique booking number
    $booking_number = uniqid('booking-');

    // Obtain customer details from form submission
    $full_name = sanitize_text_field($_POST['full_name']);
    $email_address = sanitize_email($_POST['email_address']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $street_address = sanitize_text_field($_POST['street_address']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip_code = sanitize_text_field($_POST['zip_code']);
    $country = sanitize_text_field($_POST['country']);
    // add other fields as necessary

    // Create customer post
    $customer_post_data = array(
        'post_type'     => 'customers',  // Your custom post type for customers
        'post_title'    => $full_name,   // Set the customer's full name as post title
        'post_status'   => 'publish',    // The status you want to give new posts
        'meta_input'    => array(
            'pagemeta_full_name' => $full_name,
            'pagemeta_email_address' => $email_address,
            'pagemeta_phone_number' => $phone_number,
            'pagemeta_street_address' => $street_address,
            'pagemeta_city' => $city,
            'pagemeta_state' => $state,
            'pagemeta_zip_code' => $zip_code,
            'pagemeta_country' => $country,
            'pagemeta_booking_number' => $booking_number,  // Set the booking number as post meta
            // add other meta data you need
        ),
    );

    // Insert the post
    $customer_post_id = wp_insert_post($customer_post_data);

    if(!$customer_post_id) {
        // Handle error while creating customer post
        return;
    }


    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $rooms = $_POST['rooms'];

    // Process the booking
    foreach ($rooms as $room) {
        $room_id = $room['id'];
        $quantity = (int)$room['quantity'];

        for($i = 0; $i < $quantity; $i++) {
            // Here you can also add other post data like post_title, post_content etc.
            $post_data = array(
                'post_type'     => 'reservations',  // Your custom post type
                'post_title'    => $booking_number,  // Set the booking number as post title
                'post_status'   => 'publish',       // The status you want to give new posts
                'meta_input'    => array(
                    'pagemeta_room_name' => $room_id,
                    'pagemeta_checkin_date' => $checkin,
                    'pagemeta_checkout_date' => $checkout,
                    'pagemeta_booking_number' => $booking_number,  // Set the booking number as post meta
                    'pagemeta_customer_id' => $customer_post_id,  // Link to the customer post
                    // add other meta data you need
                ),
            );

            // Insert the post
            $post_id = wp_insert_post($post_data);
            
            if($post_id) {
                // Successfully created a reservation post
                update_reservations_array_on_save($post_id, get_post($post_id), true);
            } else {
                // Handle error
            }
        }
    }

    wp_die();
}
add_action( 'wp_ajax_cognitive_book_rooms', 'cognitive_book_rooms' );
add_action( 'wp_ajax_nopriv_cognitive_book_rooms', 'cognitive_book_rooms' );

function cognitive_get_customer_name_for_reservation($reservation_id) {
    // Get the customer post ID from the reservation's meta data
    $customer_post_id = get_post_meta($reservation_id, 'pagemeta_customer_id', true);

    if ($customer_post_id) {
        // Retrieve the customer post using the ID
        $customer_post = get_post($customer_post_id);

        if ($customer_post) {
            // Return the customer's full name, which is stored as the post title
            return $customer_post->post_title;
        }
    }

    // Return null if no customer was found for the reservation
    return null;
}

function cognitive_get_customer_edit_link_for_reservation($reservation_id) {
    // Get the customer post ID from the reservation's meta data
    $customer_post_id = get_post_meta($reservation_id, 'pagemeta_customer_id', true);

    if ($customer_post_id) {
        // Retrieve the customer post using the ID
        $customer_post = get_post($customer_post_id);

        if ($customer_post) {
            // Get the admin URL and create the link
            $edit_link = admin_url('post.php?post=' . $customer_post_id . '&action=edit');
            return '<a href="' . $edit_link . '">' . $customer_post->post_title . '</a>';
        }
    }

    // Return null if no customer was found for the reservation
    return null;
}



function cognitive_get_room_title_for_reservation($reservation_id) {
    // Get the room post ID from the reservation's meta data
    $room_post_id = get_post_meta($reservation_id, 'pagemeta_room_name', true);

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


