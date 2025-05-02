<?php
/**
 * Plugin Name: Staylodgic
 * Description: Staylodgic Booking System
 * Plugin URI:  https://staylodgic.com/
 * Version:     1.0.6
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

// PHP version check: Require PHP 7.4 or higher
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Staylodgic', 'staylodgic' ) . '</strong> ' . esc_html__( 'requires PHP 7.4 or higher. Your server is running PHP', 'staylodgic' ) . ' ' . esc_html( PHP_VERSION ) . '. ' . esc_html__( 'Please upgrade PHP to use this plugin.', 'staylodgic' ) . '</p></div>';
	});

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( plugin_basename( __FILE__ ) );

	return;
}

define( 'STAYLODGIC_VERSION', '1.0.6' );

define( 'STAYLODGIC_BLOCKS__FILE__', __FILE__ );

add_filter( 'show_admin_bar', '__return_false' );

/**
 * Load Staylodgic
 * @since 1.0.0
 */
function staylodgic_load() {
	require __DIR__ . '/class-staylodgic-init.php';
	new \Staylodgic\Staylodgic_Init();
}
add_action( 'plugins_loaded', 'staylodgic_load' );

function staylodgic_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-page-helper.php';

	if ( class_exists( '\Staylodgic\Helpers\Pages_Helper' ) ) {
		\Staylodgic\Helpers\Pages_Helper::create_initial_pages();
	}

	// Set a transient to trigger redirect
	set_transient( '_staylodgic_activation_redirect', true, 30 );
}
register_activation_hook( __FILE__, 'staylodgic_activate_plugin' );
add_action( 'admin_init', 'staylodgic_redirect_after_activation' );
function staylodgic_redirect_after_activation() {
	// Check if redirect transient is set
	if ( get_transient( '_staylodgic_activation_redirect' ) ) {
		// Remove the transient
		delete_transient( '_staylodgic_activation_redirect' );

		// Prevent redirect on multisite bulk activation
		if ( is_network_admin() ) {
			return;
		}

		// Redirect to settings page
		wp_safe_redirect( admin_url( 'admin.php?page=staylodgic-settings' ) );
		exit;
	}
}
