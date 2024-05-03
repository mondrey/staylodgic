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
            <h1 id="edit-site">Edit Site: <?php echo $site->blogname ?></h1>
            <p class="edit-site-actions">
                <a href="<?php echo esc_url(get_home_url($id, '/')) ?>">Visit</a> | <a href="<?php echo esc_url(get_admin_url($id)) ?>">Dashboard</a>
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
                <input type="hidden" name="id" value="<?php echo $id ?>" />
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="site_max_rooms">Max Rooms</label></th>
                        <td><input name="site_max_rooms" class="network-options-settings" type="number" id="site_max_rooms" value="<?php echo esc_attr(get_blog_option($id, 'site_max_rooms')) ?>" /></td>
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
