<?php
class AtollMatrix_GuestRegistration_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_guest_registration_submenu_page'));

        add_filter("manage_edit-atmx_registrations_columns", array($this, 'atmx_registrations_edit_columns'));
        add_filter('manage_posts_custom_column', array($this, 'atmx_registrations_custom_columns'));
    }

    // Kbase lister
    public function atmx_registrations_edit_columns($columns)
    {
        $new_columns = array(
            "registration_booking"      => __('Booking', 'atollmatrix'),
            "registration_reservations" => __('Reservations', 'atollmatrix'),
            "registration_rooms"        => __('Rooms', 'atollmatrix'),
            "mregistration_section"     => __('Section', 'atollmatrix'),
        );

        return array_merge($columns, $new_columns);
    }
    public function atmx_registrations_custom_columns($columns)
    {
        global $post;

        $registration_post_id = $post->ID;
        $custom           = get_post_custom();
        $image_url        = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        // $registration_instance = new \AtollMatrix\Registrations();
        // $reservation_instance = new \AtollMatrix\Reservations();

        // switch ($columns) {
        //     case "registration_booking":
        //         echo $registration_instance->generateRegistrationBookingNumbers($registration_post_id);
        //         break;
        //     case "registration_reservations":
        //         $reservation_array = \AtollMatrix\Reservations::getReservationIDsForRegistration($registration_post_id);
        //         echo $reservation_instance->getEditLinksForReservations($reservation_array);
        //         break;
        //     case "registration_rooms":
        //         echo $registration_instance->generateRegistrationRooms($registration_post_id);
        //         break;
        //     case "mregistration_section":
        //         echo get_the_term_list(get_the_id(), 'atmx_registrationcat', '', ', ', '');
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

        $atollmatrix_registrations_slug = "registrations";
        if (function_exists('atollmatrix_get_option_data')) {
            $atollmatrix_registrations_slug = atollmatrix_get_option_data('registrations_permalink_slug');
        }
        if ($atollmatrix_registrations_slug == "" || !isset($atollmatrix_registrations_slug)) {
            $atollmatrix_registrations_slug = "registrations";
        }
        $args = array(
            'labels'             => array(
                'name'          => 'Guest Registrations',
                'menu_name'     => 'Guest Registrations',
                'singular_name' => 'Guest Registration',
                'all_items'     => 'All Registrations',
            ),
            'singular_label'     => __('Guest Registration', 'atollmatrix'),
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
            'rewrite'            => array('slug' => $atollmatrix_registrations_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('atmx_guestregistry', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('atmx_guestregistrycat', array('atmx_guestregistry'),
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
                'rewrite'      => array('slug' => 'guestregistry-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

    public function add_guest_registration_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=atmx_guestregistry', // Parent slug
            'Guest Registration Shortcodes', // Page title
            'Form Shortcodes', // Menu title
            'manage_options', // Capability
            'atmx_guestregistry_shortcodes', // Menu slug
            array($this, 'submenu_page_callback') // Callback function
        );
    }

    public function submenu_page_callback() {
        // Check if user has the required capability
        if (!current_user_can('manage_options')) {
            return;
        }

        // Check if data has been saved
        if (isset($_POST['atollmatrix_guestregistry_shortcode'])) {
            $shortcode_data = sanitize_textarea_field($_POST['atollmatrix_guestregistry_shortcode']);
            update_option('atollmatrix_guestregistry_shortcode', $shortcode_data);
        }

        // Retrieve saved data
        $saved_shortcode = get_option('atollmatrix_guestregistry_shortcode', '');
        $saved_shortcode = stripslashes($saved_shortcode);

        // HTML for the submenu page
        echo '<div class="wrap">';
        echo '<h1>Guest Registration Shortcodes</h1>';
        echo '<form method="post">';
        echo '<textarea name="atollmatrix_guestregistry_shortcode" style="width:100%;height:200px;">' . esc_textarea($saved_shortcode) . '</textarea>';
        echo '<br><input type="submit" value="Save" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

}
$atollmatrix_kbase_post_type = new AtollMatrix_GuestRegistration_Posts();
