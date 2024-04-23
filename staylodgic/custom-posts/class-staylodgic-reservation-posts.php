<?php
class staylodgic_Reservation_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-slgc_reservations_columns", array($this, 'slgc_reservations_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'slgc_reservations_custom_columns'));

        add_filter('manage_edit-slgc_reservations_sortable_columns', array($this, 'slgc_reservations_sortable_columns'));

        add_action('pre_get_posts', array($this, 'slgc_reservations_orderby'));

    }

    public function slgc_reservations_orderby($query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
    
        $orderby = $query->get('orderby');
    
        if ('reservation_checkinout' == $orderby) {
            $query->set('meta_key', 'staylodgic_checkin_date'); // Assuming 'reservation_checkin_date' is the meta key for check-in date
            $query->set('orderby', 'meta_value');
        }
    }
    
    
    public function slgc_reservations_sortable_columns($columns)
    {
        $columns['reservation_checkinout'] = 'reservation_checkinout';
        return $columns;
    }
    

    // Kbase lister
    public function slgc_reservations_edit_columns($columns)
    {
        unset($columns[ 'author' ]);
        unset($columns[ 'date' ]);
        $new_columns = array(
            //"mreservation_section" => __('Section', 'staylodgic'),
            "reservation_customer"   => __('Customer', 'staylodgic'),
            "reservation_bookingnr"  => __('Booking Number', 'staylodgic'),
            "reservation_checkinout" => __('Checkin / Checkout', 'staylodgic'),
            "reservation_registered" => __('Registered', 'staylodgic'),
            "reservation_nights"     => __('Nights', 'staylodgic'),
            "reservation_status"     => __('Status', 'staylodgic'),
            "reservation_substatus"  => __('Sub Status', 'staylodgic'),
            "reservation_room"       => __('Room', 'staylodgic'),
        );

        return array_merge($columns, $new_columns);
    }
    public function slgc_reservations_custom_columns($columns)
    {
        global $post;
        $custom    = get_post_custom();
        $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[ 0 ])) {
            $full_image_url = $full_image_url[ 0 ];
        }

        $reservation_instance = new \Staylodgic\Reservations($date = false, $room_id = false, $reservation_id = $post->ID);
        $bookingnumber = $reservation_instance->getBookingNumber();

        switch ($columns) {
            case "reservation_customer":
                $customer_name = $reservation_instance->getCustomerEditLinkForReservation();
                echo $customer_name;
                break;
            case "reservation_bookingnr":
                echo $bookingnumber;
                break;
            case "reservation_checkinout":
                $reservation_checkin       = $reservation_instance->getCheckinDate();
                $reservation_checkout      = $reservation_instance->getCheckoutDate();
                $reservation_staying       = $reservation_instance->isGuestCurrentlyStaying();
                $reservation_todaycheckin  = $reservation_instance->isGuestCheckingInToday();
                $reservation_todaycheckout = $reservation_instance->isGuestCheckingOutToday();
                if ($reservation_staying) {
                    if ($reservation_todaycheckin) {
                        echo '<p class="post-status-reservation post-status-reservation-checkin">' . __('Check-in', 'staylodgic') . '</p>';
                    } elseif ($reservation_todaycheckout) {
                        echo '<p class="post-status-reservation post-status-reservation-checkout">' . __('Check-out', 'staylodgic') . '</p>';
                    } else {
                        echo '<p class="post-status-reservation post-status-reservation-staying">' . __('Staying', 'staylodgic') . '</p>';
                    }
                }
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkin"><i class="fa-solid fa-arrow-right"></i> ' . staylodgic_formatDate($reservation_checkin) . '</p>';
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkout"><i class="fa-solid fa-arrow-left"></i> ' . staylodgic_formatDate($reservation_checkout) . '</p>';

                break;
            case "reservation_registered":
                $registry_instance = new \Staylodgic\GuestRegistry();
                $resRegIDs =  $registry_instance->fetchResRegIDsByBookingNumber( $bookingnumber );
                if ( $resRegIDs ) {
                    echo $registry_instance->outputRegistrationAndOccupancy($resRegIDs['reservationID'], $resRegIDs['guestRegisterID'], 'icons');
                }
                break;
            case "reservation_nights":
                $reservation_nights = $reservation_instance->countReservationDays();
                echo $reservation_nights;
                break;
            case "reservation_status":
                $reservation_status = $reservation_instance->getReservationStatus();
                echo ucfirst($reservation_status);
                break;
            case "reservation_substatus":
                $reservation_substatus = $reservation_instance->getReservationSubStatus();
                echo ucfirst($reservation_substatus);
                break;
            case "reservation_room":
                $room_title = $reservation_instance->getRoomTitleForReservation();
                echo $room_title;
                break;
            case "mreservation_section":
                echo get_the_term_list(get_the_id(), 'slgc_rescat', '', ', ', '');
                break;
        }
    }
    /*
     * kbase Admin columns
     */

    /**
     * Registers TinyMCE rich editor buttons
     *
     * @return    void
     */
    public function init()
    {
        /*
         * Register Featured Post Manager
         */
        //add_action('init', 'staylodgic_featured_register');
        //add_action('init', 'staylodgic_kbase_register');//Always use a shortname like "staylodgic_" not to see any 404 errors
        /*
         * Register kbase Post Manager
         */

        $staylodgic_reservations_slug = "reservations";
        if (function_exists('staylodgic_get_option_data')) {
            $staylodgic_reservations_slug = staylodgic_get_option_data('reservations_permalink_slug');
        }
        if ($staylodgic_reservations_slug == "" || !isset($staylodgic_reservations_slug)) {
            $staylodgic_reservations_slug = "reservations";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Reservations',
                'menu_name'     => 'Reservations',
                'singular_name' => 'Reservation',
                'all_items'     => 'All Reservations',
            ),
            'singular_label'     => __('Reservation', 'staylodgic'),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 36,
            'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0NDggNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTk2IDBDNDMgMCAwIDQzIDAgOTZWNDE2YzAgNTMgNDMgOTYgOTYgOTZIMzg0aDMyYzE3LjcgMCAzMi0xNC4zIDMyLTMycy0xNC4zLTMyLTMyLTMyVjM4NGMxNy43IDAgMzItMTQuMyAzMi0zMlYzMmMwLTE3LjctMTQuMy0zMi0zMi0zMkgzODQgOTZ6bTAgMzg0SDM1MnY2NEg5NmMtMTcuNyAwLTMyLTE0LjMtMzItMzJzMTQuMy0zMiAzMi0zMnptMzItMjQwYzAtOC44IDcuMi0xNiAxNi0xNkgzMzZjOC44IDAgMTYgNy4yIDE2IDE2cy03LjIgMTYtMTYgMTZIMTQ0Yy04LjggMC0xNi03LjItMTYtMTZ6bTE2IDQ4SDMzNmM4LjggMCAxNiA3LjIgMTYgMTZzLTcuMiAxNi0xNiAxNkgxNDRjLTguOCAwLTE2LTcuMi0xNi0xNnM3LjItMTYgMTYtMTZ6Ii8+PC9zdmc+',
            'rewrite'            => array('slug' => $staylodgic_reservations_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('slgc_reservations', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('slgc_rescat', array('slgc_reservations'),
            array(
                'labels'       => array(
                    'name'          => 'Sections',
                    'menu_name'     => 'Sections',
                    'singular_name' => 'Section',
                    'all_items'     => 'All Sections',
                ),
                'public'       => true,
                'hierarchical' => true,
                'show_ui'      => true,
                'rewrite'      => array('slug' => 'reservations-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

}
$staylodgic_kbase_post_type = new staylodgic_Reservation_Posts();
