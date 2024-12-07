<?php
/**
 * Plugin Name: Staylodgic
 * Description: Staylodgic Booking System
 * Plugin URI:  https://staylodgic.com/
 * Version:     1.0.0
 * Author:      Mohamed Musthafa
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: staylodgic
 *
 * Staylodgic is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Staylodgic is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

define( 'STAYLODGIC_VERSION', '1.0.0' );

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
