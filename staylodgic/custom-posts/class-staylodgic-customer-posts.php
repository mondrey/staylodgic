<?php
class Staylodgic_Customer_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-slgc_customers_columns", array($this, 'slgc_customers_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'slgc_customers_custom_columns'));
    }

    /**
     * Custom post columns
     *
     * @return void
     */
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
                echo esc_attr($customer_instance->generateCustomerBookingNumbers($customer_post_id));
                break;
            case "customer_reservations":

                $post_type = get_post_type(get_the_ID());

                $reservation_instance = new \Staylodgic\Activity();
                $reservation_array = \Staylodgic\Activity::get_activity_ids_for_customer($customer_post_id);
                if (is_array($reservation_array) && !empty($reservation_array)) {
                    echo '<i class="fas fa-umbrella-beach"></i>';
                    $editlinks = $reservation_instance->get_edit_links_for_activity($reservation_array);
                    echo wp_kses($editlinks, staylodgic_get_allowed_tags());
                }

                $reservation_instance = new \Staylodgic\Reservations();
                $reservation_array = \Staylodgic\Reservations::getReservationIDsForCustomer($customer_post_id);
                if (is_array($reservation_array) && !empty($reservation_array)) {
                    echo '<i class="fas fa-bed"></i>';
                    $editlinks = $reservation_instance->getEditLinksForReservations($reservation_array);
                    echo wp_kses($editlinks, staylodgic_get_allowed_tags());
                }

                break;
            case "customer_rooms":
                $customer_room = $customer_instance->generateCustomerRooms($customer_post_id);
                if (isset($customer_room)) {
                    echo wp_kses($customer_room, staylodgic_get_allowed_tags());
                }
                break;
            case "mcustomer_section":
                echo get_the_term_list(get_the_id(), 'slgc_customercat', '', ', ', '');
                break;
        }
    }

    /**
     * Register Customer post
     *
     * @return void
     */
    public function init()
    {

        $args = array(
            'labels'             => array(
                'name'          => __('Customers', 'staylodgic'),
                'add_new'       => __('Create a Customer', 'staylodgic'),
                'add_new_item'  => __('Add New Customer', 'staylodgic'),
                'menu_name'     => __('Customers', 'staylodgic'),
                'singular_name' => __('Customer', 'staylodgic'),
                'all_items'     => __('All Customers', 'staylodgic'),
            ),
            'singular_label'     => __('Customer', 'staylodgic'),
            'public'             => true,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 37,
            'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0NDggNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTIyNCAyNTZBMTI4IDEyOCAwIDEgMCAyMjQgMGExMjggMTI4IDAgMSAwIDAgMjU2em0tNDUuNyA0OEM3OS44IDMwNCAwIDM4My44IDAgNDgyLjNDMCA0OTguNyAxMy4zIDUxMiAyOS43IDUxMkg0MTguM2MxNi40IDAgMjkuNy0xMy4zIDI5LjctMjkuN0M0NDggMzgzLjggMzY4LjIgMzA0IDI2OS43IDMwNEgxNzguM3oiLz48L3N2Zz4=',
            'rewrite'            => array('slug' => 'customers'),
            'supports' => array('title', 'author', 'thumbnail'),
        );

        register_post_type('slgc_customers', $args);
        /*
         * Add Taxonomy
         */
        register_taxonomy(
            'slgc_customercat',
            array('slgc_customers'),
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
                'rewrite'      => array('slug' => 'customers-section', 'hierarchical' => true, 'with_front' => false),
            )
        );
    }
}
$staylodgic_kbase_post_type = new Staylodgic_Customer_Posts();
