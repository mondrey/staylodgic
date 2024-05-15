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

function disable_export_tools_for_non_network_admins() {
    // Check if the current user is a network admin
    if (!is_super_admin()) {
        // Remove the export tools menu
        remove_submenu_page('tools.php', 'export.php');
        remove_submenu_page('tools.php', 'import.php');

        // Disable access to the export tools page directly via URL
        if (isset($_GET['page']) && $_GET['page'] === 'export') {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Disable access to the export tools page directly via URL
        if (isset($_GET['page']) && $_GET['page'] === 'import') {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }
}

// Hook the function to the admin_menu action
add_action('admin_menu', 'disable_export_tools_for_non_network_admins', 999);


function disable_sections_for_non_network_admins() {
    // Check if the current user is a network admin
    if (!is_super_admin()) {
        // Remove the Themes menu
        remove_submenu_page('themes.php', 'themes.php');
        remove_submenu_page('themes.php', 'theme-editor.php');
        remove_menu_page('themes.php');

        remove_submenu_page('users.php', 'user-new.php');
        remove_submenu_page('tools.php', 'tools.php');
    }
}

// Hook the function to the admin_menu action
add_action('admin_menu', 'disable_sections_for_non_network_admins', 999);

function disable_sections_admin_bar($wp_admin_bar) {
    // Check if the current user is a network admin
    if (!is_super_admin()) {
        // Remove the Themes menu from the admin bar
        $wp_admin_bar->remove_node('appearance');

        // Remove the Users menu from the admin bar
        $wp_admin_bar->remove_node('users');
    }
}

// Hook the function to the admin_bar_menu action
add_action('admin_bar_menu', 'disable_sections_admin_bar', 999);

function disable_direct_access_to_sections() {
    // Check if the current user is a network admin
    if (!is_super_admin() && (
        stripos($_SERVER['REQUEST_URI'], 'themes.php') !== false || 
        stripos($_SERVER['REQUEST_URI'], 'theme-editor.php') !== false || 
        stripos($_SERVER['REQUEST_URI'], 'user-new.php') !== false)) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
}

// Hook the function to the admin_init action
add_action('admin_init', 'disable_direct_access_to_sections');
