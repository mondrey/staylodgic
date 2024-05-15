<?php
namespace StaylodgicAdmin;

class LoginRegistration {
    private $site_key;
    private $secret_key;

    public function __construct($site_key, $secret_key) {
        $this->site_key = $site_key;
        $this->secret_key = $secret_key;

        // Hook into WordPress
        // add_action('signup_extra_fields', array($this, 'display_recaptcha_and_fields')); // For multisite user registration
        // add_filter('wpmu_validate_user_signup', array($this, 'validate_recaptcha_and_fields')); // For multisite user validation
        // add_action('user_register', array($this, 'save_custom_fields'));
        // add_action('wp_enqueue_scripts', array($this, 'enqueue_recaptcha_script'));
    }

    // Enqueue Google reCAPTCHA script
    public function enqueue_recaptcha_script() {
        if (is_user_logged_in() || !isset($_GET['action']) || $_GET['action'] !== 'register') {
            return;
        }
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
    }

    // Display reCAPTCHA widget and additional fields on registration form
    public function display_recaptcha_and_fields() {
        ?>
        <p>
            <label for="first_name"><?php _e('First Name') ?><br/>
                <input type="text" name="first_name" id="first_name" class="input" value="<?php if (!empty($_POST['first_name'])) echo esc_attr($_POST['first_name']); ?>" size="25" /></label>
        </p>
        <p>
            <label for="last_name"><?php _e('Last Name') ?><br/>
                <input type="text" name="last_name" id="last_name" class="input" value="<?php if (!empty($_POST['last_name'])) echo esc_attr($_POST['last_name']); ?>" size="25" /></label>
        </p>
        <?php
        echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($this->site_key) . '"></div>';
    }

    // Validate reCAPTCHA response and additional fields
    public function validate_recaptcha_and_fields($result) {
        // Validate additional fields
        if (empty($_POST['first_name'])) {
            $result['errors']->add('first_name_error', __('<strong>ERROR</strong>: Please enter your first name.'));
        }
        if (empty($_POST['last_name'])) {
            $result['errors']->add('last_name_error', __('<strong>ERROR</strong>: Please enter your last name.'));
        }

        // Validate reCAPTCHA response
        if (isset($_POST['g-recaptcha-response'])) {
            $recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);
            $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$this->secret_key}&response={$recaptcha_response}");
            $response_body = wp_remote_retrieve_body($response);
            $result = json_decode($response_body);

            if (!$result->success) {
                $result['errors']->add('recaptcha_error', __('<strong>ERROR</strong>: Please complete the CAPTCHA.'));
            }
        } else {
            $result['errors']->add('recaptcha_error', __('<strong>ERROR</strong>: Please complete the CAPTCHA.'));
        }

        return $result;
    }

    // Save additional fields
    public function save_custom_fields($user_id) {
        if (isset($_POST['first_name'])) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
        }
        if (isset($_POST['last_name'])) {
            update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
        }
    }
}

// Usage example
$site_key = 'your_site_key';
$secret_key = 'your_secret_key';
$custom_registration = new \StaylodgicAdmin\LoginRegistration($site_key, $secret_key);
