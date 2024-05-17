<?php
namespace StaylodgicAdmin;

class LoginRegistration {
    private $site_key;
    private $secret_key;

    public function __construct($site_key, $secret_key) {
        $this->site_key = $site_key;
        $this->secret_key = $secret_key;

        // Hook into WordPress
        add_action('signup_extra_fields', array($this, 'display_recaptcha_and_fields')); // For multisite user registration
        add_filter('wpmu_validate_user_signup', array($this, 'validate_recaptcha_and_fields')); // For multisite user validation
        add_action('user_register', array($this, 'save_custom_fields'));
        add_action('wp_initialize_site', array($this, 'initialize_site_custom_fields'), 10, 2); // Use wp_initialize_site for new site
        add_action('signup_hidden_fields', array($this, 'add_hidden_fields')); // Add hidden fields to the second form
        add_action('wp_enqueue_scripts', array($this, 'enqueue_recaptcha_script'));

        add_filter('gettext', array($this, 'mu_registration_text'), 10, 3);
    }

    // Add hidden fields to the second form
    public function add_hidden_fields() {
        ?>
        <input type="hidden" name="property_name" value="<?php echo isset($_POST['property_name']) ? esc_attr($_POST['property_name']) : ''; ?>" />
        <input type="hidden" name="property_longitude" value="<?php echo isset($_POST['property_longitude']) ? esc_attr($_POST['property_longitude']) : ''; ?>" />
        <input type="hidden" name="property_latitude" value="<?php echo isset($_POST['property_latitude']) ? esc_attr($_POST['property_latitude']) : ''; ?>" />
        <?php
    }

    public function mu_registration_text($translated_text, $untranslated_text, $domain) {
        global $pagenow;

        if (is_multisite() && $pagenow === 'wp-signup.php') {
            switch ($untranslated_text) {
                case 'Get your own %s account in seconds':
                    $translated_text = __('Register your hotel', 'staylodgic-admin');
                    break;
            }
        }

        return $translated_text;
    }

    // Enqueue Google reCAPTCHA script
    public function enqueue_recaptcha_script() {
        if (is_user_logged_in() || !isset($_GET['action']) || $_GET['action'] !== 'register') {
            return;
        }
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
    }

    // Process error fields
    private function display_field_with_error($errors, $field, $label, $type = 'text', $required = true) {
        $error_message = $errors->get_error_message($field . '_error');
        $required_attr = $required ? 'required="required"' : '';
        ?>
            <label for="<?php echo esc_attr($field); ?>"><?php echo esc_html($label); ?></label>
            <?php if ($error_message): ?>
                <p class="error" id="<?php echo esc_attr($field); ?>-error"><?php echo esc_html($error_message); ?></p>
            <?php endif; ?>
            <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" class="input" value="<?php if (!empty($_POST[$field])) echo esc_attr($_POST[$field]); ?>" maxlength="60" <?php echo $required_attr; ?> />
        <p>
        </p>
        <?php
    }

    // Display reCAPTCHA widget and additional fields on registration form
    public function display_recaptcha_and_fields($errors) {
        $this->display_field_with_error($errors, 'property_name', __('Property Name'));
        $this->display_field_with_error($errors, 'property_longitude', __('Property Longitude'));
        $this->display_field_with_error($errors, 'property_latitude', __('Property Latitude'));
        echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($this->site_key) . '"></div>';
    }

    // Validate reCAPTCHA response and additional fields
    public function validate_recaptcha_and_fields($result) {
        // Validate additional fields
        if (empty($_POST['property_name'])) {
            $result['errors']->add('property_name_error', __('Please enter your property name.'));
        }
        if (empty($_POST['property_longitude'])) {
            $result['errors']->add('property_longitude_error', __('Longitude required.'));
        }
        if (empty($_POST['property_latitude'])) {
            $result['errors']->add('property_latitude_error', __('Latitude required.'));
        }
        if ( isset( $_POST['signup_for'] )) {
            if ('user' == $_POST['signup_for']) {
                $result['errors']->add('signup_for_error', __('ERROR: Invalid option detected.'));
            }
        }

        // Validate reCAPTCHA response
        // if (isset($_POST['g-recaptcha-response'])) {
        //     $recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
        //     $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$this->secret_key}&response={$recaptcha_response}");
        //     $response_body = wp_remote_retrieve_body($response);
        //     $result_data = json_decode($response_body);

        //     if (!$result_data->success) {
        //         $result['errors']->add('recaptcha_error', __('ERROR: Please complete the CAPTCHA.'));
        //     }
        // } else {
        //     $result['errors']->add('recaptcha_error', __('ERROR: Please complete the CAPTCHA.'));
        // }

        return $result;
    }

    // Save additional fields to user meta
    public function save_custom_fields($user_id) {
        if (isset($_POST['hotel_name'])) {
            update_user_meta($user_id, 'hotel_name', sanitize_text_field($_POST['hotel_name']));
        }
        if (isset($_POST['hotel_longitude'])) {
            update_user_meta($user_id, 'hotel_longitude', sanitize_text_field($_POST['hotel_longitude']));
        }
        if (isset($_POST['hotel_latitude'])) {
            update_user_meta($user_id, 'hotel_latitude', sanitize_text_field($_POST['hotel_latitude']));
        }
    }

    // Save additional fields to the newly created site's theme options
    public function initialize_site_custom_fields($new_site, $args) {
        if (isset($_POST['hotel_name']) || isset($_POST['hotel_longitude']) || isset($_POST['hotel_latitude'])) {
            // Switch to the newly created site
            switch_to_blog($new_site->blog_id);

            if (isset($_POST['hotel_name'])) {
                set_theme_mod('hotel_name', sanitize_text_field($_POST['hotel_name']));
            }
            if (isset($_POST['hotel_longitude'])) {
                set_theme_mod('hotel_longitude', sanitize_text_field($_POST['hotel_longitude']));
            }
            if (isset($_POST['hotel_latitude'])) {
                set_theme_mod('hotel_latitude', sanitize_text_field($_POST['hotel_latitude']));
            }

            // Switch back to the original site
            restore_current_blog();
        }
    }
}

// Usage example
$site_key = 'your_site_key';
$secret_key = 'your_secret_key';
$custom_registration = new \StaylodgicAdmin\LoginRegistration($site_key, $secret_key);
