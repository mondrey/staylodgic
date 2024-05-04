<?php
/**
 * Plugin Name: Staylodgic
 * Description: Staylodgic Booking System
 * Plugin URI:  https://staylodgic.com/
 * Version:     1.0
 * Author:      Staylodgic
 * Author URI:  https://staylodgic.com/
 * Text Domain: staylodgic
 */

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

define('Staylodgic_Blocks__FILE__', __FILE__);

/**
 * Load Imaginem Blocks
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function staylodgic_load()
{
    // Load localization file
    load_plugin_textdomain('staylodgic');
    require __DIR__ . '/staylodgic-loader.php';

}
add_action('plugins_loaded', 'staylodgic_load');

function staylodgic_admin_remove_posts_menu() {
    // Check if the current user is not an administrator
    if (!current_user_can('manage_options')) {
        remove_menu_page('edit.php'); // Removes the "Posts" menu
        remove_menu_page('edit-comments.php');
        remove_menu_page('tools.php');
        remove_menu_page('index.php');
    }
}
add_action('admin_menu', 'staylodgic_admin_remove_posts_menu');

// Disable support for comments and trackbacks in post types
function staylodgic_admin_disable_comments_post_types_support() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('post', 'trackbacks');
}
add_action('admin_init', 'staylodgic_admin_disable_comments_post_types_support');

// Close comments on the front-end
function staylodgic_admin_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'staylodgic_admin_disable_comments_status', 20, 2);
add_filter('pings_open', 'staylodgic_admin_disable_comments_status', 20, 2);

function staylodgic_redirect_non_admin_users() {
    // Check if the user is on the admin dashboard (index.php) and lacks the 'manage_options' capability
    if (is_admin() && !current_user_can('manage_options') && (strpos($_SERVER['REQUEST_URI'], 'index.php') !== false)) {
        // Redirect to the specific admin page
        wp_redirect(admin_url('admin.php?page=staylodgic-settings'));
        exit;
    }
}
add_action('admin_init', 'staylodgic_redirect_non_admin_users');

function staylodgic_custom_login_redirect($redirect_to, $request, $user) {
    // Use the admin_url function to redirect to a specific admin page
    return admin_url('admin.php?page=staylodgic-settings');
}
add_filter('login_redirect', 'staylodgic_custom_login_redirect', 10, 3);
