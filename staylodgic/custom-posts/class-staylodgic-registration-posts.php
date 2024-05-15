<?php
class Staylodgic_GuestRegistration_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_guest_registration_submenu_page'));
    }

    /**
     *
     * @return    void
     */
    public function init()
    {
        /*
         * Register Guest Registrations
         */

        $args = array(
            'labels'             => array(
                'name'          => __('Guest Registrations', 'staylodgic'),
                'add_new'       => __('Create a Registration', 'staylodgic'),
                'add_new_item'  => __( 'Add New Registration', 'staylodgic' ),
                'menu_name'     => __('Guest Registrations', 'staylodgic'),
                'singular_name' => __('Guest Registration', 'staylodgic'),
                'all_items'     => __('All Registrations', 'staylodgic'),
            ),
            'singular_label'     => __('Guest Registration', 'staylodgic'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => true,
            'menu_position'      => 38,
            'menu_icon'          => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTIyNCAyNTZBMTI4IDEyOCAwIDEgMCAyMjQgMGExMjggMTI4IDAgMSAwIDAgMjU2em0tNDUuNyA0OEM3OS44IDMwNCAwIDM4My44IDAgNDgyLjNDMCA0OTguNyAxMy4zIDUxMiAyOS43IDUxMkgzMjIuOGMtMy4xLTguOC0zLjctMTguNC0xLjQtMjcuOGwxNS02MC4xYzIuOC0xMS4zIDguNi0yMS41IDE2LjgtMjkuN2w0MC4zLTQwLjNjLTMyLjEtMzEtNzUuNy01MC4xLTEyMy45LTUwLjFIMTc4LjN6bTQzNS41LTY4LjNjLTE1LjYtMTUuNi00MC45LTE1LjYtNTYuNiAwbC0yOS40IDI5LjQgNzEgNzEgMjkuNC0yOS40YzE1LjYtMTUuNiAxNS42LTQwLjkgMC01Ni42bC0xNC40LTE0LjR6TTM3NS45IDQxN2MtNC4xIDQuMS03IDkuMi04LjQgMTQuOWwtMTUgNjAuMWMtMS40IDUuNSAuMiAxMS4yIDQuMiAxNS4yczkuNyA1LjYgMTUuMiA0LjJsNjAuMS0xNWM1LjYtMS40IDEwLjgtNC4zIDE0LjktOC40TDU3Ni4xIDM1OC43bC03MS03MUwzNzUuOSA0MTd6Ii8+PC9zdmc+',
            'rewrite'            => array('slug' => 'registrations'), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('slgc_guestregistry', $args);
        /*
         * Add Taxonomy for kbase 'Type'
         */
        register_taxonomy('slgc_guestregistrycat', array('slgc_guestregistry'),
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
                'rewrite'      => array('slug' => 'guestregistry-section', 'hierarchical' => true, 'with_front' => false),
            )
        );

    }

    public function add_guest_registration_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=slgc_guestregistry', // Parent slug
            'Guest Registration Shortcodes', // Page title
            'Form Shortcodes', // Menu title
            'edit_posts', // Capability
            'slgc_guestregistry_shortcodes', // Menu slug
            array($this, 'submenu_page_callback') // Callback function
        );
    }

    public function submenu_page_callback() {
        // Check if user has the required capability
        if (!current_user_can('edit_posts')) {
            return;
        }

        // Check if data has been saved
        if (isset($_POST['staylodgic_guestregistry_shortcode'])) {
            $shortcode_data = sanitize_textarea_field($_POST['staylodgic_guestregistry_shortcode']);
            update_option('staylodgic_guestregistry_shortcode', $shortcode_data);
        }

        // Retrieve saved data
        $saved_shortcode = get_option('staylodgic_guestregistry_shortcode', '');

        if ( '' == $saved_shortcode ) {
            $formGenInstance = new \Staylodgic\FormGenerator();
            $saved_shortcode = $formGenInstance->defaultShortcodes();
        }

        $saved_shortcode = stripslashes($saved_shortcode);

        // HTML for the submenu page
        echo '<div class="wrap">';
        echo '<h1>Guest Registration Shortcodes</h1>';
        echo '<form method="post">';
        echo '<textarea name="staylodgic_guestregistry_shortcode" style="width:100%;height:200px;">' . esc_textarea($saved_shortcode) . '</textarea>';
        echo '<br><input type="submit" value="Save" class="button button-primary">';
        echo '</form>';
        echo '</div>';
    }

}
$staylodgic_kbase_post_type = new Staylodgic_GuestRegistration_Posts();
