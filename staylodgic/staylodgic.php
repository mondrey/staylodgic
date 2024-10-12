<?php
/**
 * Plugin Name: Staylodgic
 * Description: Staylodgic Booking System
 * Plugin URI:  https://staylodgic.com/
 * Version:     1.0.4
 * Author:      Staylodgic
 * Author URI:  https://staylodgic.com/
 * Text Domain: staylodgic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

define( 'STAYLODGIC_BLOCKS__FILE__', __FILE__ );

add_filter( 'show_admin_bar', '__return_false' );

/**
 * Load Staylodgic
 * @since 1.0.0
 */
function staylodgic_load() {
	// Load localization file
	load_plugin_textdomain( 'staylodgic' );
	require __DIR__ . '/staylodgic-loader.php';
}
add_action( 'plugins_loaded', 'staylodgic_load' );
