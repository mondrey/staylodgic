<?php
namespace AtollMatrix;

class BookingBatchProcessor extends BatchProcessorBase
{
    private $batchSize = 50;

    public function __construct()
    {
        add_action('wp_ajax_process_event_batch', array($this, 'process_event_batch')); // wp_ajax_ hook for logged-in users
        add_action('wp_ajax_nopriv_process_event_batch', array($this, 'process_event_batch')); // wp_ajax_nopriv_ hook for non-logged-in users
        add_action('admin_menu', array($this, 'add_booking_admin_menu')); // This now points to the add_admin_menu function
        add_action('wp_ajax_insert_events_batch', array($this, 'insert_events_batch'));
        add_action('wp_ajax_nopriv_insert_events_batch', array($this, 'insert_events_batch'));

        // ...
        add_action('wp_ajax_save_ical_booking_meta', array($this, 'save_ical_booking_meta'));
        add_action('wp_ajax_nopriv_save_ical_booking_meta', array($this, 'save_ical_booking_meta'));

        add_action('wp_ajax_find_future_cancelled_reservations', array($this, 'find_future_cancelled_reservations'));
        add_action('wp_ajax_nopriv_find_future_cancelled_reservations', array($this, 'find_future_cancelled_reservations'));

    }

    public function find_future_cancelled_reservations(
        $processRoomID = false,
        $processICS_ID = false,
        $processedEvents = false,
        $signature = false
    )
    {

        $jsonOutput = false;
        if ( ! $processRoomID ) {
            $processRoomID = $_POST[ 'room_id' ];
            $processICS_ID = $_POST[ 'ics_id' ];
            $processedEvents = $_POST[ 'processedEvents' ];
            $signature = $_POST[ 'signature_id' ];
            $jsonOutput = true; 
        }

        // error_log( print_r($_POST, true) );
        // // Extract UIDs from processedEvents
        $processedUIDs = array_map(function ($event) {
            return $event[ 'UID' ];
        }, $processedEvents);

        // To Test - Remove records from the processedUIDs array as a test to see cancellations are reported
        // if (!empty($processedUIDs)) {
        //     $removedUID = array_pop($processedUIDs);
        //     $removedUID = array_pop($processedUIDs);
        //     $removedUID = array_pop($processedUIDs);
        // }
        error_log( '--------------- Post Events array -----------' );
        error_log( print_r( $processedEvents, 1 ) );
        $earliestDate = null;
        foreach ($processedEvents as $event) {
            if (isset($event['DTSTART']) && !empty($event['DTSTART'])) {
                // Convert DTSTART to a DateTime object
                $eventDate = \DateTime::createFromFormat('Ymd', $event['DTSTART']);
        
                if ($earliestDate === null || $eventDate < $earliestDate) {
                    $earliestDate = $eventDate;
                }
            }
        }
        // // Get the signature from the $_POST data

        // Get today's date
        $daystart = '';
        if ($earliestDate instanceof \DateTime) {
            $daystart = $earliestDate->format('Y-m-d');
        }

        // Run a query for reservation posts
        $args = array(
            'post_type'  => 'atmx_reservations',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'   => 'atollmatrix_ics_signature',
                    'value' => $signature,
                ),
                array(
                    'key'     => 'atollmatrix_checkin_date',
                    'value'   => $daystart,
                    'compare' => '>=',
                    // check-in date is today or in the future
                    'type'    => 'DATE',
                ),
            ),
        );
        $query = new \WP_Query($args);

        $potentiallyCancelled = [  ];

        // If the query has posts
        if ($query->have_posts()) {
            // For each post in the query
            while ($query->have_posts()) {
                // Move the internal pointer
                $query->the_post();

				$reservation_id = get_the_ID();
                $reservation_status = get_post_meta($reservation_id, 'atollmatrix_reservation_status', true);
                // Get the post meta
                $booking_number = get_post_meta( $reservation_id, 'atollmatrix_booking_number', true);
				error_log( '--------------- duplicate checking booking_number -----------' );
				error_log( $booking_number );
                // If the booking number doesn't exist in processed UIDs, it's potentially cancelled
                if (!in_array($booking_number, $processedUIDs)) {
                    $potentiallyCancelled[  ] = $booking_number;
                    
                    $import_missing = atollmatrix_get_option('import_missing');
                    error_log( '--------------- Import Action Status -----------' );
                    error_log( $import_missing );
                    if ( 'cancel' == $import_missing ) {
                        if ( 'cancelled' !== $reservation_status ) {
                            update_post_meta($reservation_id, 'atollmatrix_reservation_status', 'cancelled');
                        }
                    }
                    if ( 'delete' == $import_missing ) {
                        $reservation_instance = new \AtollMatrix\Reservations($date = false, $room_id = false, $reservation_id);
                        $customer_post_id = $reservation_instance->getGuest_id_forReservation( $booking_number );
                        // Delete Customer for Reservation
                        wp_delete_post($customer_post_id, true);
                        // Delete Booking
                        wp_delete_post($reservation_id, true);
                    }

                }
            }

            // Restore original Post Data
            wp_reset_postdata();
        }

        // Prepare the response data
        $responseData = array(
            'success'               => true,
            'cancelledReservations' => $potentiallyCancelled,
            'icsID'                 => $processICS_ID,
        );

        $room_ical_data = get_post_meta($processRoomID, 'room_ical_data', true);

        if (isset($room_ical_data[ $processICS_ID ])) {
            $room_ical_data[ $processICS_ID ][ 'ical_synced' ] = true;
        }

        //error_log( print_r( $room_ical_data, true ) );
        update_post_meta($processRoomID, 'room_ical_data', $room_ical_data);

        if ( $jsonOutput ) {
            // Send the JSON response
            wp_send_json_success($responseData);
        } else {
            return $responseData;
        }
    }

	public function insert_events_batch(
        $processedEvents = false,
        $processICS_ID = false
    )
    {
        $jsonOutput = false;
        if ( ! $processedEvents ) {
            // Get the processed events data from the request
            $processedEvents = $_POST[ 'processedEvents' ];
            $processICS_ID   = $_POST[ 'ics_id' ];
            $jsonOutput = true;
        }

        $successCount = 0; // Counter for successfully inserted posts
        $skippedCount = 0; // Counter for skipped posts

        // Loop through the processed events and insert posts
        foreach ($processedEvents as $event) {
            $booking_number = $event[ 'UID' ];
            $booking_uid    = $event[ 'UID' ];
            $checkin        = $event[ 'DATA' ][ 'CHECKIN' ];
            $checkout       = $event[ 'DATA' ][ 'CHECKOUT' ];
            $name           = $event[ 'SUMMARY' ];
            $description    = $event[ 'DESCRIPTION' ];
            $signature      = $event[ 'SIGNATURE' ];

            $room_id = $_POST[ 'room_id' ];

            $existing_post = get_posts(
                array(
                    'post_type'  => 'atmx_reservations',
                    'meta_query' => array(
                        array(
                            'key'   => 'atollmatrix_booking_number',
                            'value' => $booking_number,
                        ),
                    ),
                )
            );

			if ($existing_post) {
				$existing_post_id = $existing_post[0]->ID;
				$existing_checkin = get_post_meta($existing_post_id, 'atollmatrix_checkin_date', true);
				$existing_checkout = get_post_meta($existing_post_id, 'atollmatrix_checkout_date', true);
			
				// Compare existing check-in and check-out dates with the new ones
				if ($existing_checkin !== $checkin || $existing_checkout !== $checkout) {
					// Update the existing post with new check-in and check-out dates
					update_post_meta($existing_post_id, 'atollmatrix_checkin_date', $checkin);
					update_post_meta($existing_post_id, 'atollmatrix_checkout_date', $checkout);
				}


				$skippedCount++;
				continue;
			}

			$post_data = array(
				'post_type'   => 'atmx_reservations',
				// Your custom post type
				'post_title'  => $booking_number,
				// Set the booking number as post title
				'post_status' => 'publish',
				// The status you want to give new posts
				'meta_input'  => array(
					'atollmatrix_room_id'            => $room_id,
					'atollmatrix_reservation_status' => 'confirmed',
					'atollmatrix_checkin_date'       => $checkin,
					'atollmatrix_checkout_date'      => $checkout,
					'atollmatrix_booking_number'     => $booking_number,
					'atollmatrix_booking_uid'        => $booking_uid,
					// Set the booking number as post meta
					'atollmatrix_full_name'          => $name,
					// Customer name
					'atollmatrix_reservation_notes'  => $description,
					// Description
					'atollmatrix_ics_signature'      => $signature,
					// ICS File hash
					'atollmatrix_customer_choice'    => 'new',
					// ICS File hash
					// add other meta data you need
				),
			);
			// Insert the post
			$post_id = wp_insert_post($post_data);
			// $post_id = true;

			if ($post_id) {
				$successCount++;
			}
        }

        // Prepare the response data
        $responseData = array(
            'success'      => true,
            'successCount' => $successCount,
            'skippedCount' => $skippedCount,
            'icsID'        => $processICS_ID,
        );

        if ( $jsonOutput ) {
            // Send the JSON response
            wp_send_json_success($responseData);
        } else {
            return $responseData;
        }
    }

    public function add_booking_admin_menu()
    {

        add_submenu_page(
            'atoll-matrix',
            // This is the slug of the parent menu
            'Import iCal Bookings',
            'Import iCal Bookings',
            'manage_options',
            'import-booking-ical',
            array($this, 'ical_import')
        );
        add_submenu_page(
            'atoll-matrix',
            // This is the slug of the parent menu
            'Export iCal Bookings',
            'Export iCal Bookings',
            'manage_options',
            'export-ical',
            array($this, 'ical_export')
        );
    }

    public function ical_availability()
    {
        // The HTML content of the 'Atoll Matrix' page goes here
        echo "<h1>Welcome to ical_availability</h1>";
    }

    public function ical_export()
    {
        // The HTML content of the 'Atoll Matrix' page goes here
        echo "<h1>Export ICS Calendar</h1>";
    }

    public function ical_import()
    {
        // The HTML content of your 'Import iCal' page goes here
        echo "<div class='main-sync-form-wrap'>";
        echo "<div id='sync-form'>";
        echo "<h1>Import ICS Calendar</h1>";

        echo "<form id='room_ical_form' method='post'>";
        echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';

        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            // Get meta
            $room_ical_data = get_post_meta($room->ID, 'room_ical_data', true);

            echo '<div class="room_ical_links_wrapper" data-room-id="' . $room->ID . '">';
            echo "<h2>" . $room->post_title . "</h2>";
            if (is_array($room_ical_data) && count($room_ical_data) > 0) {
                foreach ($room_ical_data as $ical_id => $ical_link) {

                    if (isset($room_ical_data[ $ical_id ])) {
                        $ical_synced = $room_ical_data[ $ical_id ][ 'ical_synced' ];
                    }

                    echo '<div class="room_ical_link_group">';
                    echo '<input readonly type="text" name="room_ical_links_id[]" value="' . esc_attr($ical_link[ 'ical_id' ]) . '">';
                    echo '<input readonly type="url" name="room_ical_links_url[]" value="' . esc_attr($ical_link[ 'ical_url' ]) . '">';
                    echo '<input readonly type="text" name="room_ical_links_comment[]" value="' . esc_attr($ical_link[ 'ical_comment' ]) . '">';
                    echo '<button type="button" class="unlock_button"><i class="fas fa-lock"></i></button>'; // Unlock button

                    $button_save_mode = __('Sync', 'atollmatrix');

                    echo '<button data-type="sync-booking" type="button" class="sync_button" data-ics-id="' . esc_attr($ical_id) . '" data-ics-url="' . esc_attr($ical_link[ 'ical_url' ]) . '" data-room-id="' . esc_attr($room->ID) . '">' . $button_save_mode . '</button>'; // Sync button
                    echo '</div>';
                }
            } else {
                echo '<div class="room_ical_link_group">';
                echo '<input type="url" name="room_ical_links_url[]">';
                echo '<input type="text" name="room_ical_links_comment[]">';
                echo '</div>';
            }
            echo '<button type="button" class="add_more_ical">Add more</button>';
            echo '</div>';
        }

        echo '<input data-type="sync-booking" type="submit" id="save_all_ical_rooms" value="Save">';
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo \AtollMatrix\Modals::syncBookingModal();
    }

    public function save_ical_booking_meta()
    {
        // Perform nonce check and other validations as needed
        // ...
        if (!isset($_POST[ 'ical_form_nonce' ]) || !wp_verify_nonce($_POST[ 'ical_form_nonce' ], 'ical_form_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        $room_ids           = $_POST[ 'room_ids' ];
        $room_links_id      = $_POST[ 'room_ical_links_id' ];
        $room_links_url     = $_POST[ 'room_ical_links_url' ];
        $room_links_comment = $_POST[ 'room_ical_links_comment' ];

        //error_log( print_r( $_POST , true ) );
        for ($i = 0; $i < count($room_ids); $i++) {
            $room_id    = $room_ids[ $i ];
            $room_links = array();

            // Ensure that $room_links_url[$i] is an array before trying to count its elements
            if (isset($room_links_url[ $i ]) && is_array($room_links_url[ $i ])) {
                for ($j = 0; $j < count($room_links_url[ $i ]); $j++) {

                    // Get the old room data
                    $old_room_links = get_post_meta($room_id, 'room_ical_data', true);

                    // Check if the URL is valid
                    if (filter_var($room_links_url[ $i ][ $j ], FILTER_VALIDATE_URL)) {
                        // Check if a unique ID is already assigned
                        if ('' == $room_links_id[ $i ][ $j ]) {
                            $room_links_id[ $i ][ $j ] = uniqid();
                        }

                        $file_md5Hash = md5(esc_url($room_links_url[ $i ][ $j ]));

                        // Check if the URL is the same as before
                        $ical_synced = false;
                        if (isset($old_room_links[ $file_md5Hash ]) && $old_room_links[ $file_md5Hash ][ 'ical_url' ] == $room_links_url[ $i ][ $j ]) {
                            $ical_synced = $old_room_links[ $file_md5Hash ][ 'ical_synced' ];
                        }

                        $room_links[ $file_md5Hash ] = array(
                            'ical_id'      => sanitize_text_field($room_links_id[ $i ][ $j ]),
                            'ical_synced'  => $ical_synced,
                            'ical_url'     => esc_url($room_links_url[ $i ][ $j ]),
                            'ical_comment' => sanitize_text_field($room_links_comment[ $i ][ $j ]),
                        );
                    }
                }
            }

            // Update the meta field in the database.
            update_post_meta($room_id, 'room_ical_data', $room_links);
        }

        // You can return a success response here
        wp_send_json_success('Successfully stored');
    }
}

// Instantiate the class
$BookingBatchProcessor = new BookingBatchProcessor();
