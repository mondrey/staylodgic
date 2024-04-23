<?php
class Staylodgic_Customer_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-slgc_customers_columns", array($this, 'slgc_customers_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'slgc_customers_custom_columns'));
    }

    // Kbase lister
    public function slgc_customers_edit_columns($columns)
    {
        $new_columns = array(
            "customer_booking"      => __('Booking', 'staylodgic'),
            "customer_reservations" => __('Reservations', 'staylodgic'),
            "customer_rooms"        => __('Rooms', 'staylodgic'),
            "mcustomer_section"     => __('Section', 'staylodgic'),
        );

        return array_merge($columns, $new_columns);
    }
    public function slgc_customers_custom_columns($columns)
    {
        global $post;

        $customer_post_id = $post->ID;
        $custom           = get_post_custom();
        $image_url        = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        $customer_instance = new \Staylodgic\Customers();

        switch ($columns) {
            case "customer_booking":
                echo $customer_instance->generateCustomerBookingNumbers($customer_post_id);
                break;
            case "customer_reservations":

                $post_type = get_post_type( get_the_ID() );

                $reservation_instance = new \Staylodgic\Activity();
                $reservation_array = \Staylodgic\Activity::getActivityIDsForCustomer($customer_post_id);
                if ( is_array( $reservation_array ) && !empty( $reservation_array ) ) {
                    echo '<i class="fas fa-umbrella-beach"></i>';
                    echo $reservation_instance->getEditLinksForActivity($reservation_array);
                }

                $reservation_instance = new \Staylodgic\Reservations();
                $reservation_array = \Staylodgic\Reservations::getReservationIDsForCustomer($customer_post_id);
                if ( is_array( $reservation_array ) && !empty( $reservation_array ) ) {
                    echo '<i class="fas fa-bed"></i>';
                    echo $reservation_instance->getEditLinksForReservations($reservation_array);
                }

                break;
            case "customer_rooms":
                echo $customer_instance->generateCustomerRooms($customer_post_id);
                break;
            case "mcustomer_section":
                echo get_the_term_list(get_the_id(), 'slgc_customercat', '', ', ', '');
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

        $staylodgic_customers_slug = "customers";
        if (function_exists('staylodgic_get_option_data')) {
            $staylodgic_customers_slug = staylodgic_get_option_data('customers_permalink_slug');
        }
        if ($staylodgic_customers_slug == "" || !isset($staylodgic_customers_slug)) {
            $staylodgic_customers_slug = "customers";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Customers',
                'menu_name'     => 'Customers',
                'singular_name' => 'Customer',
                'all_items'     => 'All Customers',
            ),
            'singular_label'     => __('Customer', 'staylodgic'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 37,
            'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0NDggNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTIyNCAyNTZBMTI4IDEyOCAwIDEgMCAyMjQgMGExMjggMTI4IDAgMSAwIDAgMjU2em0tNDUuNyA0OEM3OS44IDMwNCAwIDM4My44IDAgNDgyLjNDMCA0OTguNyAxMy4zIDUxMiAyOS43IDUxMkg0MTguM2MxNi40IDAgMjkuNy0xMy4zIDI5LjctMjkuN0M0NDggMzgzLjggMzY4LjIgMzA0IDI2OS43IDMwNEgxNzguM3oiLz48L3N2Zz4=',
            'rewrite'            => array('slug' => $staylodgic_customers_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('slgc_customers', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('slgc_customercat', array('slgc_customers'),
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
                'rewrite'      => array('slug' => 'customers-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

}
$staylodgic_kbase_post_type = new Staylodgic_Customer_Posts();
