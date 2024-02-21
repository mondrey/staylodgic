<?php
class AtollMatrix_Init
{
    public function __construct()
    {
        $this->atollmatrix_actions();
        $this->atollmatrix_load_custom_posts();
        $this->atollmatrix_load_availablity_calendar();

        add_filter('upload_mimes', array($this, 'allow_ics_upload'));
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_google_fonts' ));
        add_action( 'wp_head', array($this,'preconnect_google_fonts' ));
    
    }

    public function allow_ics_upload($mime_types)
    {
        // Add ICS file extension and mime type to the allowed list
        $mime_types['ics'] = 'text/calendar';
        return $mime_types;
    }

    private function atollmatrix_actions()
    {
        add_action('wp_enqueue_scripts', array($this, 'atollmatrix_load_front_end_scripts_styles'));
        add_action('admin_enqueue_scripts', array($this, 'atollmatrix_load_admin_styles'));

        add_action('admin_init', array($this, 'atollmatrix_registryitemmetabox_init'));
        add_action('admin_init', array($this, 'atollmatrix_reservationsitemmetabox_init'));
        add_action('admin_init', array($this, 'atollmatrix_activityresitemmetabox_init'));
        add_action('admin_init', array($this, 'atollmatrix_customersitemmetabox_init'));
        add_action('admin_init', array($this, 'atollmatrix_roomitemmetabox_init'));
        add_action('admin_init', array($this, 'atollmatrix_activityitemmetabox_init'));

        add_action('init', array($this, 'atollmatrix_load_textdomain'));
        add_action('init', array($this, 'atollmatrix_load_metaboxes'));
        add_action('init', array($this, 'atollmatrix_load_themeoptions'));

        add_action( 'after_setup_theme', array($this, 'atollmatrix_custom_image_size'));
    }

    public function atollmatrix_custom_image_size()
    {
        add_image_size( 'atollmatrix-large-square', 770, 770, true ); // Square.
    }

    public function atollmatrix_load_textdomain()
    {
        load_plugin_textdomain('imaginem-blocks-ii', false, basename(dirname(__FILE__)) . '/languages');
    }

    public function atollmatrix_load_custom_posts()
    {
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-reservation-posts.php';
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-customer-posts.php';
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-registration-posts.php';
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-room-posts.php';
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-activity-posts.php';
        require_once plugin_dir_path(__FILE__) . '/custom-posts/class-atollmatrix-activityres-posts.php';
    }

    public function atollmatrix_load_availablity_calendar()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/admin-property-data.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin-demo-data.php';

        require_once plugin_dir_path(__FILE__) . 'vendors/ics-parser/src/ICal/ICal.php';
        require_once plugin_dir_path(__FILE__) . 'vendors/ics-parser/src/ICal/Event.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-batchprocessorbase.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-bookingbatchprocessor.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-icalexportprocessor.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-availabilitybatchprocessor.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-availablitycalendarbase.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-availablitycalendar.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-cache.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-rooms.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-rates.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-customers.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-reservations.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-common.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-data.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-modals.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-payments.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-booking.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-emaildispatcher.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-invoicing.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-guestregistry.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-formgenerator.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/class-analytics.php';
        require_once plugin_dir_path(__FILE__) . 'includes/admin/utilities.php';
    }

    public function atollmatrix_load_themeoptions() {
        require_once plugin_dir_path(__FILE__) . '/options/options.php';
    }

    public function atollmatrix_load_metaboxes()
    {
        require_once plugin_dir_path(__FILE__) . '/includes/google-fonts.php';
        require_once plugin_dir_path(__FILE__) . '/includes/theme-gens.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxgen/metaboxgen.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/registry-metaboxes.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/reservation-metaboxes.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/activityres-metaboxes.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/customer-metaboxes.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/room-metaboxes.php';
        require_once plugin_dir_path(__FILE__) . '/metabox/metaboxes/activity-metaboxes.php';
    }

    function enqueue_google_fonts() {
        // Enqueue the main font style
        wp_enqueue_style( 'atollmatrix-google-fonts', 'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap', array(), null );
    }
    
    function preconnect_google_fonts() {
        // Preconnect for performance improvement
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    }

    public function atollmatrix_load_admin_styles( $hook )
    {
        wp_register_script('select2', plugin_dir_url(__FILE__) . 'assets/js/select2/js/select2.full.min.js', array('jquery'), null, true);
        wp_register_style('select2', plugin_dir_url(__FILE__) . 'assets/js/select2/css/select2.min.css', array(), false, 'screen');

        wp_register_script('atollmatrix-parser', plugin_dir_url(__FILE__) . 'admin/js/booking-parser.js', array('jquery'), null, true);
        wp_register_script('html2canvas', plugin_dir_url(__FILE__) . 'assets/js/html2canvas.min.js', array('jquery'), null, true);
        wp_register_script('jsPDF', plugin_dir_url(__FILE__) . 'assets/js/jspdf/jspdf.umd.js', array('jquery'), null, true);
        wp_register_script('atollmatrix-invoice', plugin_dir_url(__FILE__) . 'admin/js/invoice.js', array('jquery', 'jsPDF', 'html2canvas'), null, true);
        wp_register_style('atollmatrix-invoice', plugin_dir_url(__FILE__) . 'admin/css/invoice.css', false, 'screen');
        wp_register_style('atollmatrix-dashboard', plugin_dir_url(__FILE__) . 'admin/css/dashboard.css', false, 'screen');
        wp_register_style('flatpickr', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen');
        wp_register_style('flatpickr-extra', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr-extra-style.css', array(), '1.0', 'screen');
        wp_register_script('flatpickr', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr.js', array('jquery'), '1.0', true);
        wp_register_script('admin-post-meta', plugin_dir_url(__FILE__) . 'admin/js/admin-post-meta.js', array('jquery', 'wp-api', 'wp-data'), null, true);
        wp_register_script('qrcodejs', plugin_dir_url(__FILE__) . 'assets/js/qrcode.min.js', array('jquery'), null, true);
        wp_register_script('menu-image-admin', plugin_dir_url(__FILE__) . 'admin/js/menu-image-admin.js', array('jquery'), null, true);
        wp_register_style('menu-image-css', plugin_dir_url(__FILE__) . 'admin/js/menu-image-admin.css', array(), false, 'screen');
        wp_register_style('atollmatrix-admin-styles', plugin_dir_url(__FILE__) . 'admin/css/style.css', false, 'screen');

        wp_enqueue_style('availability-styles', plugin_dir_url(__FILE__) . 'admin/css/availability-calendar.css', false, 'screen');
        wp_enqueue_script('availability-scripts', plugin_dir_url(__FILE__) . 'admin/js/availability-calendar.js', array('jquery'), null, true);
        wp_enqueue_script('activity-scripts', plugin_dir_url(__FILE__) . 'admin/js/activity-calendar.js', array('jquery'), null, true);
        wp_enqueue_script('common-scripts', plugin_dir_url(__FILE__) . 'admin/js/common.js', array('jquery'), null, true);

        wp_register_style('fontawesome-6', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/fontawesome.css', false, 'screen');
        wp_register_style('fontawesome-6-brands', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/all.css', false, 'screen');
        wp_register_style('fontawesome-6-solid', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/solid.css', false, 'screen');

        wp_register_script('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), null, true);
        wp_register_style('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen');

        wp_register_script('atollmatrix-chartjs', plugin_dir_url(__FILE__) . 'admin/js/chart.js', array('jquery'), null, true);
        wp_register_script('atollmatrix-bookingchart', plugin_dir_url(__FILE__) . 'admin/js/booking-charts.js', array('atollmatrix-chartjs'), null, true);

        if (function_exists('get_current_screen')) {
            $current_admin_screen = get_current_screen();
        }
        if (isset($current_admin_screen)) {
            if ($current_admin_screen->base == 'post') {
                wp_enqueue_media();

                wp_enqueue_style('atollmatrix-admin-styles');

                wp_enqueue_script('select2');
                wp_enqueue_style('select2');
                wp_enqueue_style('flatpickr');
                wp_enqueue_script('flatpickr');
                wp_enqueue_style('flatpickr-extra');
                wp_enqueue_script('admin-post-meta');
                wp_enqueue_script('qrcodejs');

                wp_enqueue_script('jquery-ui-slider');

                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');
            
            }

            if ($current_admin_screen->base == 'atmx_reservations_page_atollmatrix-invoicing') {
                wp_enqueue_style('atollmatrix-admin-styles');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');
                
                wp_enqueue_script('atollmatrix-invoice');
                wp_localize_script('atollmatrix-invoice', 'atollmatrixData', array(
                    'pluginUrl' => plugin_dir_url(__FILE__),
                ));
            
                wp_enqueue_style('atollmatrix-invoice');
            }
            if ($current_admin_screen->base == 'toplevel_page_atmx-dashboard') {

                wp_enqueue_style('atollmatrix-dashboard');

                wp_enqueue_script('atollmatrix-bookingchart', plugin_dir_url(__FILE__) . 'admin/js/booking-charts.js', array('jquery'), null, true);

                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');
            }
            if ($current_admin_screen->base == 'atoll-matrix_page_import-booking-ical') {

                wp_enqueue_script('atollmatrix-parser');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

            }
            if ($current_admin_screen->base == 'atoll-matrix_page_export-booking-ical') {

                wp_enqueue_script('atollmatrix-parser');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                 wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

            }
            if ($current_admin_screen->base == 'atoll-matrix_page_import-availability-ical') {

                wp_enqueue_script('atollmatrix-parser');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

            }
            if ($current_admin_screen->base == 'atoll-matrix_page_import-availablity') {

                wp_enqueue_script('atollmatrix-parser');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

            }
            if ($current_admin_screen->base == 'atoll-matrix_page_atollmatrix-invoicing') {

                wp_enqueue_script('atollmatrix-invoice');
                wp_localize_script('atollmatrix-invoice', 'atollmatrixData', array(
                    'pluginUrl' => plugin_dir_url(__FILE__),
                ));
            
                wp_enqueue_style('atollmatrix-invoice');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

            }
            if ($current_admin_screen && $current_admin_screen->base == 'edit' && $current_admin_screen->post_type == 'atmx_reservations') {

                wp_enqueue_script('atollmatrix-parser');
                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

            }
            if ($current_admin_screen->post_type === 'atmx_guestregistry' && ($hook === 'post.php' || $hook === 'post-new.php')) {

                wp_enqueue_style('atollmatrix-admin-styles');

                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');
                
                wp_enqueue_script('atollmatrix-invoice');
                wp_localize_script('atollmatrix-invoice', 'atollmatrixData', array(
                    'pluginUrl' => plugin_dir_url(__FILE__),
                ));
            
                wp_enqueue_style('atollmatrix-invoice');

            }
            wp_localize_script(
                'jquery',
                'atollmatrix_admin_vars',
                array(
                    'post_id' => get_the_ID(),
                    'nonce'   => wp_create_nonce('atollmatrix-nonce-metagallery'),
                )
            );

            if ($current_admin_screen->base == 'atoll-matrix_page_atollmatrix-settings-panel') {
                wp_enqueue_script('admin_options', plugin_dir_url(__FILE__) . 'admin/js/admin-options.js', array('jquery'), null, true);
                wp_enqueue_style('admin_options', plugin_dir_url(__FILE__) . 'admin/css/admin-options.css', false, 'screen');
                // Enqueue jQuery UI Sortable
                wp_enqueue_script('jquery-ui-sortable');

                // Add CSS styles for the sortable placeholder
                wp_enqueue_style('jquery-ui-sortable');

                wp_enqueue_script('select2', plugin_dir_url(__FILE__) . 'assets/js/select2/js/select2.full.min.js', array('jquery'), null, true);
                wp_enqueue_style('select2', plugin_dir_url(__FILE__) . 'assets/js/select2/css/select2.min.css', array(), false, 'screen');
            }
            if ($current_admin_screen->base == 'toplevel_page_atmx-availability') {

                wp_enqueue_script('velocity', plugin_dir_url(__FILE__) . 'assets/js/velocity.min.js', array('jquery'), null, true);
                wp_enqueue_script('velocity-ui', plugin_dir_url(__FILE__) . 'assets/js/velocity.ui.js', array('jquery'), null, true);

                wp_register_script('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), null, true);
                wp_register_style('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen');
                wp_enqueue_style('bootstrap');
                wp_enqueue_script('bootstrap');

                wp_enqueue_style('fontawesome-6');
                wp_enqueue_style('fontawesome-6-brands');
                wp_enqueue_style('fontawesome-6-solid');

                wp_enqueue_media();

                wp_enqueue_style('atollmatrix-admin-styles');

                wp_enqueue_script('chosen');
                wp_enqueue_style('chosen');
                wp_enqueue_style('flatpickr');
                wp_enqueue_script('flatpickr');
                wp_enqueue_style('flatpickr-extra');
                wp_enqueue_script('admin-post-meta');

            }
        }
    }

    public function atollmatrix_load_front_end_scripts_styles()
    {

        wp_register_style('frontendstyle', plugin_dir_url(__FILE__) . 'admin/css/frontend-booking.css', array(), '1.0', 'screen');
        wp_register_style('flatpickr', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr.min.css', array(), '1.0', 'screen');
        wp_register_style('flatpickr-extra', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr-extra-style.css', array(), '1.0', 'screen');
        wp_register_script('flatpickr', plugin_dir_url(__FILE__) . 'assets/js/flatpickr/flatpickr.js', array('jquery'), '1.0', true);
        wp_register_script('frontend-calendar', plugins_url('assets/js/frontend-calendar.js', __FILE__), array('jquery'), '1.0', true);
        wp_register_script('payment-helper', plugins_url('assets/js/payment-helper.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('frontend-calendar', 'frontendAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'post_id' => get_the_ID(),
                'nonce'   => wp_create_nonce('atollmatrix-nonce-search'),
            )
        );

        wp_enqueue_script('velocity', plugin_dir_url(__FILE__) . 'assets/js/velocity.min.js', array('jquery'), null, true);
        wp_enqueue_script('velocity-ui', plugin_dir_url(__FILE__) . 'assets/js/velocity.ui.js', array('jquery'), null, true);
        wp_enqueue_script('common-scripts', plugin_dir_url(__FILE__) . 'admin/js/common.js', array('jquery'), null, true);

        wp_register_style('fontawesome-6', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/fontawesome.css', false, 'screen');
        wp_register_style('fontawesome-6-brands', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/all.css', false, 'screen');
        wp_register_style('fontawesome-6-solid', plugin_dir_url(__FILE__) . 'assets/fonts/fontawesome-free-6.4.0-web/css/solid.css', false, 'screen');

        wp_enqueue_script('frontend-calendar', array('jquery'), null, true);
        wp_enqueue_script('payment-helper');
        wp_enqueue_style('frontendstyle');
        wp_enqueue_style('flatpickr');
        wp_enqueue_script('flatpickr');
        wp_enqueue_style('flatpickr-extra');
        wp_enqueue_script('underscore');
        wp_enqueue_style('fontawesome-6');
        wp_enqueue_style('fontawesome-6-brands');
        wp_enqueue_style('fontawesome-6-solid');

        wp_register_script('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), null, true);
        wp_register_style('bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap/css/bootstrap.min.css', false, 'screen');
        wp_enqueue_style('bootstrap');
        wp_enqueue_script('bootstrap');

        // Check if we are viewing a single post/page
        if (is_singular()) {
            global $post;
            // Check if the post content contains the Contact Form 7 shortcode
            if ( has_shortcode($post->post_content, 'form_input') || get_post_type() == 'atmx_guestregistry' ) {
                // Enqueue the Signature Pad script
                wp_enqueue_script('guest-registration', plugin_dir_url(__FILE__) . 'assets/js/guest-registration.js', array(), '1.0.0', true);
                wp_enqueue_script('signature-pad', plugin_dir_url(__FILE__) . 'assets/js/signature_pad.umd.min.js', array(), '1.0.0', true);
                // Enqueue any additional scripts required for the digital signature
                wp_enqueue_script('atollmatrix-digital-signature', plugin_dir_url(__FILE__) . 'assets/js/digital-signature.js', array('signature-pad'), '1.0.0', true);
                wp_enqueue_style('atollmatrix-digital-signature', plugin_dir_url(__FILE__) . 'assets/css/digital-signature.css', false, 'screen');
            }
        }

    }

    // Registry Metabox
    public function atollmatrix_registryitemmetabox_init()
    {
        add_meta_box('registryInfo-meta', esc_html__('Registry Options', 'imaginem-blocks-ii'), 'atollmatrix_registryitem_metaoptions', 'atmx_guestregistry', 'normal', 'low');
        add_meta_box('registryInfo-changelog', esc_html__('Registry Changelog', 'imaginem-blocks-ii'), 'atollmatrix_registryitem_changelog', 'atmx_guestregistry', 'normal', 'low');
    }
    // Reservations Metabox
    public function atollmatrix_reservationsitemmetabox_init()
    {
        add_meta_box('activityresInfo-meta', esc_html__('Activity Reservations Options', 'imaginem-blocks-ii'), 'atollmatrix_activityresitem_metaoptions', 'atmx_activityres', 'normal', 'low');
        add_meta_box('activityresInfo-changelog', esc_html__('Activity Reservations Changelog', 'imaginem-blocks-ii'), 'atollmatrix_activityresitem_changelog', 'atmx_activityres', 'normal', 'low');
    }
    // ActivityRes Metabox
    public function atollmatrix_activityresitemmetabox_init()
    {
        add_meta_box('reservationsInfo-meta', esc_html__('Reservation Options', 'imaginem-blocks-ii'), 'atollmatrix_reservationsitem_metaoptions', 'atmx_reservations', 'normal', 'low');
        add_meta_box('reservationsInfo-changelog', esc_html__('Reservation Changelog', 'imaginem-blocks-ii'), 'atollmatrix_reservationsitem_changelog', 'atmx_reservations', 'normal', 'low');
    }
    // Customer Metabox
    public function atollmatrix_customersitemmetabox_init()
    {
        add_meta_box('customersInfo-meta', esc_html__('Customer Options', 'imaginem-blocks-ii'), 'atollmatrix_customersitem_metaoptions', 'atmx_customers', 'normal', 'low');
    }
    // Room Metabox
    public function atollmatrix_roomitemmetabox_init()
    {
        add_meta_box("room-meta", esc_html__("Room Options", "imaginem-blocks"), "atollmatrix_roomitem_metaoptions", "atmx_room", "normal", "low");
        add_meta_box("room-changelog", esc_html__("Room Changelog", "imaginem-blocks"), "atollmatrix_roomitem_changelog", "atmx_room", "normal", "low");
    }
    // Room Metabox
    public function atollmatrix_activityitemmetabox_init()
    {
        add_meta_box("activity-meta", esc_html__("Activity Options", "imaginem-blocks"), "atollmatrix_activityitem_metaoptions", "atmx_activity", "normal", "low");
        add_meta_box("activity-changelog", esc_html__("Activity Changelog", "imaginem-blocks"), "atollmatrix_activityitem_changelog", "atmx_activity", "normal", "low");
    }
}

new AtollMatrix_Init();
