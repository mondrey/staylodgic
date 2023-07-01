<?php
class AtollMatrix_Payment_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-atmx_payments_columns", array($this, 'atmx_payments_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'atmx_payments_custom_columns'));

    }
    // Kbase lister
    public function atmx_payments_edit_columns($columns)
    {
        $new_columns = array(
            "payment_booking"      => __('Booking', 'mthemelocal'),
            "payment_reservations" => __('Reservations', 'mthemelocal'),
            "payment_rooms"        => __('Rooms', 'mthemelocal'),
            "mpayment_section"     => __('Section', 'mthemelocal'),
        );

        return array_merge($columns, $new_columns);
    }

    public function atmx_payments_custom_columns($columns)
    {
        global $post;

        $payment_post_id = $post->ID;
        $custom          = get_post_custom();
        $image_url       = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        // $payment_instance = new \AtollMatrix\Payments();

        switch ($columns) {
            case "payment_booking":
                //echo $payment_instance->generatePaymentBookingNumbers( $payment_post_id );
                break;
            case "payment_reservations":
                // $reservation_array = \AtollMatrix\Reservations::getReservationIDsForPayment( $payment_post_id );
                // echo \AtollMatrix\Reservations::getEditLinksForReservations( $reservation_array );
                break;
            case "payment_rooms":
                // echo $payment_instance->generatePaymentRooms( $payment_post_id );
                break;
            case "mpayment_section":
                // echo get_the_term_list( get_the_id(), 'atmx_paymentcat', '', ', ','' );
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

        $atollmatrix_payments_slug = "payments";
        if (function_exists('atollmatrix_get_option_data')) {
            $atollmatrix_payments_slug = atollmatrix_get_option_data('payments_permalink_slug');
        }
        if ($atollmatrix_payments_slug == "" || !isset($atollmatrix_payments_slug)) {
            $atollmatrix_payments_slug = "payments";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Payments',
                'menu_name'     => 'Payments',
                'singular_name' => 'Payment',
                'all_items'     => 'All Payments',
            ),
            'singular_label'     => __('Payment', 'mthemelocal'),
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
            'rewrite'            => array('slug' => $atollmatrix_payments_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('atmx_payments', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('atmx_paymentcat', array('atmx_payments'),
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
                'rewrite'      => array('slug' => 'payments-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

}
$atollmatrix_kbase_post_type = new AtollMatrix_Payment_Posts();
