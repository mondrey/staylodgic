<?php
/**
 * Plugin Name: Staylodgic
 * Description: Staylodgic Booking System
 * Plugin URI:  https://imaginemthemes.co/
 * Version:     1.0
 * Author:      iMaginem
 * Author URI:  https://imaginemthemes.co/
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
function Staylodgic_Load()
{
    // Load localization file
    load_plugin_textdomain('staylodgic');
    require __DIR__ . '/staylodgic-loader.php';

}
add_action('plugins_loaded', 'Staylodgic_Load');
