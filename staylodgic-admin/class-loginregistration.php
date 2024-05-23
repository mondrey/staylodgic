<?php
namespace StaylodgicAdmin;

class LoginRegistration {

    public function __construct() {

        add_filter('gettext', array($this, 'mu_registration_text'), 10, 3);
        
    }

    public function mu_registration_text($translated_text, $untranslated_text, $domain) {
        global $pagenow;

        if (is_multisite() && $pagenow === 'wp-signup.php') {
            switch ($untranslated_text) {
                case 'Get your own %s account in seconds':
                    $translated_text = __('Register your property', 'staylodgic-admin');
                    break;
                case 'Get <em>another</em> %s site in seconds':
                    $translated_text = __('Register <em>another</em> property', 'staylodgic-admin');
                    break;
                case 'Welcome back, %s. By filling out the form below, you can <strong>add another site to your account</strong>. There is no limit to the number of sites you can have, so create to your heart&#8217;s content, but write responsibly!':
                    $translated_text = __('Welcome back, %s. By filling out the form below, you can <strong>add another property to your account</strong>', 'staylodgic-admin');
                    break;
                case 'Sites you are already a member of:':
                    $translated_text = __('Properties you are already a member of:', 'staylodgic-admin');
                    break;
                case 'If you are not going to use a great site domain, leave it for a new user. Now have at it!':
                    $translated_text = __('Proceed to register', 'staylodgic-admin');
                    break;
            }
        }

        return $translated_text;
    }

}

$custom_registration = new \StaylodgicAdmin\LoginRegistration();
