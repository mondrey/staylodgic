<?php
class AtollMatrix_Registry_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-atmx_registry_columns", array($this, 'atmx_registry_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'atmx_registry_custom_columns'));
    }

    // Kbase lister
    public function atmx_registry_edit_columns($columns)
    {
        $new_columns = array(
            "registry_booking"      => __('Booking', 'atollmatrix'),
            "registry_reservations" => __('Reservations', 'atollmatrix'),
            "registry_rooms"        => __('Rooms', 'atollmatrix'),
            "mregistry_section"     => __('Section', 'atollmatrix'),
        );

        return array_merge($columns, $new_columns);
    }
    public function atmx_registry_custom_columns($columns)
    {
        global $post;

        $registry_post_id = $post->ID;
        $custom           = get_post_custom();
        $image_url        = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        // $registry_instance = new \AtollMatrix\Registrations();
        // $reservation_instance = new \AtollMatrix\Reservations();

        // switch ($columns) {
        //     case "registry_booking":
        //         echo $registry_instance->generateRegistrationBookingNumbers($registry_post_id);
        //         break;
        //     case "registry_reservations":
        //         $reservation_array = \AtollMatrix\Reservations::getReservationIDsForRegistration($registry_post_id);
        //         echo $reservation_instance->getEditLinksForReservations($reservation_array);
        //         break;
        //     case "registry_rooms":
        //         echo $registry_instance->generateRegistrationRooms($registry_post_id);
        //         break;
        //     case "mregistry_section":
        //         echo get_the_term_list(get_the_id(), 'atmx_registrycat', '', ', ', '');
        //         break;
        // }
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

        $atollmatrix_registry_slug = "registry";
        if (function_exists('atollmatrix_get_option_data')) {
            $atollmatrix_registry_slug = atollmatrix_get_option_data('registry_permalink_slug');
        }
        if ($atollmatrix_registry_slug == "" || !isset($atollmatrix_registry_slug)) {
            $atollmatrix_registry_slug = "registry";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Registrations',
                'menu_name'     => 'Registrations',
                'singular_name' => 'Registration',
                'all_items'     => 'All Registrations',
            ),
            'singular_label'     => __('Registration', 'atollmatrix'),
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
            'rewrite'            => array('slug' => $atollmatrix_registry_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('atmx_registry', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('atmx_registrycat', array('atmx_registry'),
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
                'rewrite'      => array('slug' => 'registry-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

}
$atollmatrix_kbase_post_type = new AtollMatrix_Registry_Posts();
