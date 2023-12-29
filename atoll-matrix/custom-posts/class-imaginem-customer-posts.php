<?php
class AtollMatrix_Customer_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-atmx_customers_columns", array($this, 'atmx_customers_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'atmx_customers_custom_columns'));
    }

    // Kbase lister
    public function atmx_customers_edit_columns($columns)
    {
        $new_columns = array(
            "customer_booking"      => __('Booking', 'atollmatrix'),
            "customer_reservations" => __('Reservations', 'atollmatrix'),
            "customer_rooms"        => __('Rooms', 'atollmatrix'),
            "mcustomer_section"     => __('Section', 'atollmatrix'),
        );

        return array_merge($columns, $new_columns);
    }
    public function atmx_customers_custom_columns($columns)
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

        $customer_instance = new \AtollMatrix\Customers();

        switch ($columns) {
            case "customer_booking":
                echo $customer_instance->generateCustomerBookingNumbers($customer_post_id);
                break;
            case "customer_reservations":
                $reservation_array = \AtollMatrix\Reservations::getReservationIDsForCustomer($customer_post_id);
                echo \AtollMatrix\Reservations::getEditLinksForReservations($reservation_array);
                break;
            case "customer_rooms":
                echo $customer_instance->generateCustomerRooms($customer_post_id);
                break;
            case "mcustomer_section":
                echo get_the_term_list(get_the_id(), 'atmx_customercat', '', ', ', '');
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

        $atollmatrix_customers_slug = "customers";
        if (function_exists('atollmatrix_get_option_data')) {
            $atollmatrix_customers_slug = atollmatrix_get_option_data('customers_permalink_slug');
        }
        if ($atollmatrix_customers_slug == "" || !isset($atollmatrix_customers_slug)) {
            $atollmatrix_customers_slug = "customers";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Customers',
                'menu_name'     => 'Customers',
                'singular_name' => 'Customer',
                'all_items'     => 'All Customers',
            ),
            'singular_label'     => __('Customer', 'atollmatrix'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 6,
            'menu_icon'          => plugin_dir_url(__FILE__) . 'images/portfolio.png',
            'rewrite'            => array('slug' => $atollmatrix_customers_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('atmx_customers', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('atmx_customercat', array('atmx_customers'),
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
$atollmatrix_kbase_post_type = new AtollMatrix_Customer_Posts();
