<?php
class staylodgic_Reservation_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-slgc_reservations_columns", array($this, 'slgc_reservations_edit_columns'));
        add_filter('manage_slgc_reservations_posts_custom_column', array($this, 'slgc_reservations_custom_columns'));

        add_filter('manage_edit-slgc_reservations_sortable_columns', array($this, 'slgc_reservations_sortable_columns'));

        add_action('pre_get_posts', array($this, 'slgc_reservations_orderby'));
    }

    /**
     * Reservation post columns
     *
     * @return void
     */
    public function slgc_reservations_orderby($query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');

        if ('reservation_checkinout' == $orderby) {
            $query->set('meta_key', 'staylodgic_checkin_date');
            $query->set('orderby', 'meta_value');
        }
    }


    public function slgc_reservations_sortable_columns($columns)
    {
        $columns['reservation_checkinout'] = 'reservation_checkinout';
        return $columns;
    }

    public function slgc_reservations_edit_columns($columns)
    {
        unset($columns['author']);
        $new_columns = array(
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
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        $reservation_instance = new \Staylodgic\Reservations($date = false, $room_id = false, $reservation_id = $post->ID);
        $bookingnumber = $reservation_instance->get_booking_number();

        switch ($columns) {
            case "reservation_customer":
                $customer_name = $reservation_instance->get_customer_edit_link_for_reservation();
                if (null !== $customer_name) {
                    echo wp_kses($customer_name, staylodgic_get_allowed_tags());
                }
                break;
            case "reservation_bookingnr":
                echo esc_attr($bookingnumber);
                break;
            case "reservation_checkinout":
                $reservation_checkin       = $reservation_instance->get_checkin_date();
                $reservation_checkout      = $reservation_instance->get_checkout_date();
                $reservation_staying       = $reservation_instance->is_guest_currently_staying();
                $reservation_todaycheckin  = $reservation_instance->is_guest_checking_in_today();
                $reservation_todaycheckout = $reservation_instance->is_guest_checking_out_today();
                if ($reservation_staying) {
                    if ($reservation_todaycheckin) {
                        echo '<p class="post-status-reservation post-status-reservation-checkin">' . __('Check-in', 'staylodgic') . '</p>';
                    } elseif ($reservation_todaycheckout) {
                        echo '<p class="post-status-reservation post-status-reservation-checkout">' . __('Check-out', 'staylodgic') . '</p>';
                    } else {
                        echo '<p class="post-status-reservation post-status-reservation-staying">' . __('Staying', 'staylodgic') . '</p>';
                    }
                }
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkin"><i class="fa-solid fa-arrow-right"></i> ' . esc_attr(staylodgic_formatDate($reservation_checkin)) . '</p>';
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkout"><i class="fa-solid fa-arrow-left"></i> ' . esc_attr(staylodgic_formatDate($reservation_checkout)) . '</p>';

                break;
            case "reservation_registered":
                $registry_instance = new \Staylodgic\Guest_Registry();
                $res_reg_ids =  $registry_instance->fetch_res_reg_ids_by_booking_number($bookingnumber);
                if ($res_reg_ids) {
                    $registration_occupancy = $registry_instance->output_registration_and_occupancy($res_reg_ids['stay_reservation_id'], $res_reg_ids['guest_register_id'], 'icons');
                    echo wp_kses($registration_occupancy, staylodgic_get_allowed_tags());
                }
                break;
            case "reservation_nights":
                $reservation_nights = $reservation_instance->count_reservation_days();
                echo esc_attr($reservation_nights);
                break;
            case "reservation_status":
                $reservation_status = $reservation_instance->get_reservation_status();
                echo esc_attr(ucfirst($reservation_status));
                break;
            case "reservation_substatus":
                $reservation_substatus = $reservation_instance->get_reservation_sub_status();
                echo esc_attr(ucfirst($reservation_substatus));
                break;
            case "reservation_room":
                $room_title = $reservation_instance->get_room_title_for_reservation();
                echo esc_html($room_title);
                break;
            case "mreservation_section":
                echo get_the_term_list(get_the_id(), 'slgc_rescat', '', ', ', '');
                break;
        }
    }

    /**
     * Register Reservation post
     *
     * @return void
     */
    public function init()
    {
        /*
         * Register Post
         */
        $args = array(
            'labels'             => array(
                'name'          => __('Reservations', 'staylodgic'),
                'add_new'       => __('Create Reservation', 'staylodgic'),
                'add_new_item'  => __('Add New Reservation', 'staylodgic'),
                'menu_name'     => __('Reservations', 'staylodgic'),
                'singular_name' => __('Reservation', 'staylodgic'),
                'all_items'     => __('All Reservations', 'staylodgic'),
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
            'rewrite'            => array('slug' => 'reservations'),
            'supports' => array('title', 'author', 'thumbnail'),
        );

        register_post_type('slgc_reservations', $args);
        /*
         * Add Taxonomy
         */
        register_taxonomy(
            'slgc_rescat',
            array('slgc_reservations'),
            array(
                'labels'       => array(
                    'name'          => __('Sections', 'staylodgic'),
                    'menu_name'     => __('Sections', 'staylodgic'),
                    'singular_name' => __('Section', 'staylodgic'),
                    'all_items'     => __('All Sections', 'staylodgic'),
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
