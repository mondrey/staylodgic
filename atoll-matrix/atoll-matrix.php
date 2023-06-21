<?php
/**
 * Plugin Name: Atoll Matrix
 * Description: Imaginem Cognitive Blocks
 * Plugin URI:  https://imaginemthemes.co/
 * Version:     1.0
 * Author:      iMaginem
 * Author URI:  https://imaginemthemes.co/
 * Text Domain: atoll-matrix
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'Imaginem_Blocks__FILE__', __FILE__ );

/**
 * Load Imaginem Blocks
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function AtollMatrix_Load() {
	// Load localization file
	load_plugin_textdomain( 'atoll-matrix' );
    require( __DIR__ . '/atoll-matrix-loader.php' );

}
add_action( 'plugins_loaded', 'AtollMatrix_Load' );