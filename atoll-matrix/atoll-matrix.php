<?php
/**
 * Plugin Name: Atoll Matrix
 * Description: Atoll Matrix Booking System
 * Plugin URI:  https://imaginemthemes.co/
 * Version:     1.0
 * Author:      iMaginem
 * Author URI:  https://imaginemthemes.co/
 * Text Domain: atollmatrix
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'AtollMatrix_Blocks__FILE__', __FILE__ );

/**
 * Load Imaginem Blocks
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function AtollMatrix_Load() {
	// Load localization file
	load_plugin_textdomain( 'atollmatrix' );
	require( __DIR__ . '/atoll-matrix-loader.php' );

}
add_action( 'plugins_loaded', 'AtollMatrix_Load' );