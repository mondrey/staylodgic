<?php

/**
 * Plugin Name: Staylodgic Site Admin
 * Plugin URI:  http://yourwebsite.com/
 * Description: Manage site settings for Staylodgic in the network admin.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  http://yourwebsite.com/
 * Network:     true
 */

namespace StaylodgicAdmin;

class SiteAdmin
{

    public function __construct()
    {

        add_filter('network_edit_site_nav_links', array($this, 'staylodgicadmin_siteadmin_siteinfo_tab'));

        add_action('network_admin_menu', array($this, 'staylodgicadmin_siteadmin_new_options'));

        add_action('current_screen', array($this, 'staylodgicadmin_siteadmin_page_title'));

        add_action('network_admin_edit_staylodgicupdate',  array($this, 'staylodgicadmin_siteadmin_save'));

        add_action('network_admin_notices', array($this, 'staylodgicadmin_notice'));

        add_action('init', array($this, 'initialize_user_role'));

        add_action('login_enqueue_scripts', array($this, 'staylodgicadmin_login_logo'));

        add_action('after_signup_form', array($this, 'add_terms_and_conditions_link'));
        add_action('before_signup_form', array($this, 'add_signup_logo'));

        $this->staylodgic_admin_load();

    }

    public function add_terms_and_conditions_link() {
        echo '<div class="sign-up-form-terms">';
        echo '<p class="terms-conditions">By signing up, you agree to our <a href="https://staylodgic.com/privacy-policy/" target="_blank">Privacy Policy</a> and <a href="https://staylodgic.com/terms-and-conditions/" target="_blank">Terms and Conditions</a>.</p>';
        echo '</div>';

    }
    public function add_signup_logo() {
        echo '<div class="sign-up-form-logo">';
        echo '<img src="'.plugins_url('images/staylodgic-logo-black.png', __FILE__).'" alt="logo"/>';
        echo '</div>';
    }
    

    public function staylodgicadmin_login_logo() {
        $logo_url = plugins_url('images/staylodgic-logo-black.png', __FILE__);
        ?>
        <style type="text/css">
            #loginform {
                background: rgba(255,255,255,0.3);
            }
            /* General body styling */
            body.login {
                background: #d8d8ff; /* Light grey background for a modern look */
            }

            /* Customizing the login form */
            body.login form {
                background: none; /* White background for the form */
                border: 0; /* Light border around the form */
                padding: 20px; /* Adding some padding */
                border-radius: 8px; /* Rounded corners for a modern touch */
                box-shadow: none;
            }

            /* Customizing the logo */
            #login h1 a {
                background-image: url('<?php echo esc_url($logo_url); ?>');
                height: 80px;
                width: 320px;
                background-size: contain;
                background-repeat: no-repeat;
                margin-bottom: 20px; /* Adding some space below the logo */
            }

            /* Customizing input fields */
            body.login form .input, 
            body.login input[type="text"] {
                border: 1px solid #dcdcdc; /* Light border for input fields */
                padding: 12px; /* Adding some padding */
                border-radius: 4px; /* Slightly rounded corners */
                font-size: 16px; /* Increasing font size */
            }

            /* Customizing the login button */
            body.login .button-primary {
                background: #0073aa; /* WordPress blue */
                border-color: #0073aa;
                box-shadow: none;
                text-shadow: none;
                color: #ffffff; /* White text */
                border-radius: 4px; /* Slightly rounded corners */
                padding: 12px 20px; /* Adjusting padding */
                font-size: 16px; /* Increasing font size */
                transition: background 0.3s ease; /* Smooth transition */
            }

            body.login .button-primary:hover,
            body.login .button-primary:focus {
                background: #005d8b; /* Darker blue on hover/focus */
                border-color: #005d8b;
            }

            /* Customizing links */
            body.login #nav {
                border-top: 2px solid #000;
                padding-top: 30px;
            }
            body.login #backtoblog {
                display: none;
            }
            body.login #nav a, 
            body.login #backtoblog a {
                color: #0073aa; /* WordPress blue */
            }

            .login #nav a:hover, 
            .login #backtoblog a:hover {
                color: #005d8b; /* Darker blue on hover */
            }

            /* Styling error messages */
            .login .message, 
            .login .error {
                border-left: 4px solid #0073aa; /* Blue border for messages */
                padding: 12px; /* Adding some padding */
                border-radius: 4px; /* Rounded corners */
            }

            /* Ensuring footer links are styled appropriately */
            .login #backtoblog {
                margin-top: 20px;
            }
        </style>
    <?php
    }

    public function initialize_user_role() {
        add_action('add_user_to_blog', array($this, 'set_user_role_to_editor'), 10, 3);
    }

    public function set_user_role_to_editor($user_id, $role, $blog_id) {
        // error_log('------ set_user_role_to_editor is fired.');
        // error_log('blog id: ' . $blog_id);
        // error_log('user id: ' . $user_id);
        // Switch to the new blog
        switch_to_blog($blog_id);
        
        // Get the user object
        $user = new \WP_User($user_id);
        // error_log( print_r( $user, 1));
        // Set the user's role to Editor
        $user->set_role('editor');
        
        // Restore the current blog
        restore_current_blog();
    }

    public function staylodgic_admin_load() {
        require_once plugin_dir_path(__FILE__) . '/class-loginregistration.php';
    }

    function staylodgicadmin_notice()
    {

        if (isset($_GET['updated']) && isset($_GET['page']) && 'staylodgic' === $_GET['page']) {

?>
            <div id="message" class="updated notice is-dismissible">
                <p>Settings Saved!</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        <?php

        }
    }

    function staylodgicadmin_siteadmin_save()
    {

        $id = absint($_POST['id']);

        check_admin_referer('staylodgic-network-check' . $id); // nonce check

        update_blog_option($id, 'site_max_rooms', sanitize_text_field($_POST['site_max_rooms']));
        update_blog_option($id, 'site_sync_feature', sanitize_text_field($_POST['site_sync_feature']));
        update_blog_option($id, 'site_sync_interval', sanitize_text_field($_POST['site_sync_interval']));

        wp_safe_redirect(
            add_query_arg(
                array(
                    'page' => 'staylodgic',
                    'id' => $id,
                    'updated' => 'true'
                ),
                network_admin_url('sites.php')
            )
        );
        exit;
    }

    function staylodgicadmin_siteadmin_page_title($current_screen)
    {

        global $title;

        if ('sites_page_staylodgic-network' === $current_screen->id && isset($_GET['id']) && $_GET['id']) {
            $blog_details = get_blog_details(array('blog_id' => $_GET['id']));
            $title = __('Edit Site:') . ' ' . $blog_details->blogname;
        }
    }

    public function staylodgicadmin_siteadmin_siteinfo_tab($tabs)
    {

        $tabs['site-staylodgicsite'] = array(
            'label' => 'Staylodgic',
            'url' => add_query_arg('page', 'staylodgic', 'sites.php'),
            'cap' => 'manage_sites'
        );
        return $tabs;
    }

    public function staylodgicadmin_siteadmin_new_options()
    {
        add_submenu_page('', 'Edit site', 'Edit site', 'manage_network_options', 'staylodgic', array($this, 'staylodgicadmin_siteadmin_options_callback'));
    }

    public function staylodgicadmin_siteadmin_options_callback()
    {

        // do not worry about that, we will check it too
        $id = absint($_REQUEST['id']);

        $site = get_site($id);

        ?>
        <div class="wrap">
            <h1 id="edit-site">Edit Site: <?php echo esc_html($site->blogname); ?></h1>
            <p class="edit-site-actions">
                <a href="<?php echo esc_url(get_home_url($id, '/')); ?>">Visit</a> | <a href="<?php echo esc_url(get_admin_url($id)); ?>">Dashboard</a>
            </p>
            <?php
            // navigation tabs
            network_edit_site_nav(
                array(
                    'blog_id'  => $id,
                    'selected' => 'site-staylodgicsite' // current tab
                )
            );

            $site_sync_feature = get_blog_option($id, 'site_sync_feature', 'disabled'); // Default to 'standard' if no value is set yet
            $site_sync_interval = get_blog_option($id, 'site_sync_interval', 'disabled'); // Default to 'standard' if no value is set yet
            ?>
            <form method="post" action="edit.php?action=staylodgicupdate">
                <?php wp_nonce_field('staylodgic-network-check' . $id); ?>
                <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>" />
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="site_max_rooms">Max Rooms</label></th>
                        <td><input name="site_max_rooms" class="network-options-settings" type="number" id="site_max_rooms" value="<?php echo esc_attr(get_blog_option($id, 'site_max_rooms')); ?>" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="site_sync_feature">Sync Feature</label></th>
                        <td>
                            <select name="site_sync_feature" id="site_sync_feature" class="network-options-settings">
                                <option value="disabled" <?php selected($site_sync_feature, 'disabled'); ?>>Disabled</option>
                                <option value="disable-notify" <?php selected($site_sync_feature, 'disable-notify'); ?>>Disable and Notify</option>
                                <option value="enabled" <?php selected($site_sync_feature, 'enabled'); ?>>Enabled</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="site_sync_interval">Sync Interval</label></th>
                        <td>
                            <select name="site_sync_interval" id="site_sync_interval" class="network-options-settings">
                                <option value="1" <?php selected($site_sync_interval, '1'); ?>>1</option>
                                <option value="5" <?php selected($site_sync_interval, '5'); ?>>5</option>
                                <option value="10" <?php selected($site_sync_interval, '10'); ?>>10</option>
                                <option value="15" <?php selected($site_sync_interval, '15'); ?>>15</option>
                                <option value="30" <?php selected($site_sync_interval, '30'); ?>>30</option>
                                <option value="60" <?php selected($site_sync_interval, '60'); ?>>60</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
<?php
    }
}



// Instantiate the class
if (is_multisite()) {
    $instance = new \StaylodgicAdmin\SiteAdmin();
}
