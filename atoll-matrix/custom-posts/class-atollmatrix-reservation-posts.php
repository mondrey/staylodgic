<?php
class atollmatrix_Reservation_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-atmx_reservations_columns", array($this, 'atmx_reservations_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'atmx_reservations_custom_columns'));
    }

    // Kbase lister
    public function atmx_reservations_edit_columns($columns)
    {
        unset($columns[ 'author' ]);
        unset($columns[ 'date' ]);
        $new_columns = array(
            //"mreservation_section" => __('Section', 'atollmatrix'),
            "reservation_customer"   => __('Customer', 'atollmatrix'),
            "reservation_bookingnr"  => __('Booking Number', 'atollmatrix'),
            "reservation_checkinout" => __('Checkin / Checkout', 'atollmatrix'),
            "reservation_registered" => __('Registered', 'atollmatrix'),
            "reservation_nights"     => __('Nights', 'atollmatrix'),
            "reservation_status"     => __('Status', 'atollmatrix'),
            "reservation_substatus"  => __('Sub Status', 'atollmatrix'),
            "reservation_room"       => __('Room', 'atollmatrix'),
        );

        return array_merge($columns, $new_columns);
    }
    public function atmx_reservations_custom_columns($columns)
    {
        global $post;
        $custom    = get_post_custom();
        $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[ 0 ])) {
            $full_image_url = $full_image_url[ 0 ];
        }

        $reservation_instance = new \AtollMatrix\Reservations($date = false, $room_id = false, $reservation_id = $post->ID);
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
                        echo '<p class="post-status-reservation post-status-reservation-checkin">' . __('Check-in', 'atollmatrix') . '</p>';
                    } elseif ($reservation_todaycheckout) {
                        echo '<p class="post-status-reservation post-status-reservation-checkout">' . __('Check-out', 'atollmatrix') . '</p>';
                    } else {
                        echo '<p class="post-status-reservation post-status-reservation-staying">' . __('Staying', 'atollmatrix') . '</p>';
                    }
                }
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkin"><i class="fa-solid fa-arrow-right"></i> ' . atollmatrix_formatDate($reservation_checkin) . '</p>';
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkout"><i class="fa-solid fa-arrow-left"></i> ' . atollmatrix_formatDate($reservation_checkout) . '</p>';

                break;
            case "reservation_registered":
                $registry_instance = new \AtollMatrix\GuestRegistry();
                $resRegIDs =  $registry_instance->fetchResRegIDsByBookingNumber( $bookingnumber );
                if ( $resRegIDs ) {
                    $registry_instance->outputRegistrationAndOccupancy($resRegIDs['reservationID'], $resRegIDs['guestRegisterID'], 'icons');
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
                echo get_the_term_list(get_the_id(), 'atmx_rescat', '', ', ', '');
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
        //add_action('init', 'atollmatrix_featured_register');
        //add_action('init', 'atollmatrix_kbase_register');//Always use a shortname like "atollmatrix_" not to see any 404 errors
        /*
         * Register kbase Post Manager
         */

        $atollmatrix_reservations_slug = "reservations";
        if (function_exists('atollmatrix_get_option_data')) {
            $atollmatrix_reservations_slug = atollmatrix_get_option_data('reservations_permalink_slug');
        }
        if ($atollmatrix_reservations_slug == "" || !isset($atollmatrix_reservations_slug)) {
            $atollmatrix_reservations_slug = "reservations";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Reservations',
                'menu_name'     => 'Reservations',
                'singular_name' => 'Reservation',
                'all_items'     => 'All Reservations',
            ),
            'singular_label'     => __('Reservation', 'atollmatrix'),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 6,
            'menu_icon'          => plugin_dir_url(__FILE__) . 'images/portfolio.png',
            'rewrite'            => array('slug' => $atollmatrix_reservations_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('atmx_reservations', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('atmx_rescat', array('atmx_reservations'),
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
$atollmatrix_kbase_post_type = new atollmatrix_Reservation_Posts();
