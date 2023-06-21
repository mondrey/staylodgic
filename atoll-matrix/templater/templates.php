<?php

// Abort if called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'iMaginem_Templates_Manager' ) ) {

	/**
	 * Class definition
	 */
	class iMaginem_Templates_Manager {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Template option name
		 * @var string
		 */
		protected $option = 'imaginem_templates';

		/**
		 * Constructor
		 */
		public function init() {
			add_action( 'elementor/init', array( $this, 'register_templates_source' ) );
			add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ), 20 );
		}

		/**
		 * Register AJAX for templates
		 *
		 * @param $ajax
		 */
		public function register_ajax_actions( $ajax ) {
			
			$data = false;

			if ( isset( $_POST['actions'] ) ) {
				$actions = json_decode( wp_unslash( $_POST['actions'] ), true );
				foreach ( $actions as $id => $action_data ) {
					if ( ! isset( $action_data['get_template_data'] ) ) {
						$data = $action_data;
					}
				}
			} else {
				return;
			}

			// Prepare Ajax and register actions

			if ( $data && isset( $data['data'] ) ) {
				$data = $data['data'];

				if ( empty( $data['template_id'] ) ) {
					return;
				}
				if ( false === strpos( $data['template_id'], 'imaginem_' ) ) {
					return;
				}
				// All good
				$ajax->register_ajax_action( 'get_template_data', array( $this, 'get_imaginem_template_data' ) );

			} else {
				return;
			}
		}

		/**
		 * Populate template data
		 *
		 * @return args
		 */
		public function get_imaginem_template_data( $args ) {

			$source = Elementor\Plugin::instance()->templates_manager->get_source( 'imaginem-templates' );
			return $source->get_data( $args );

		}

		/**
		 * Get remote data
		 */
		public function register_templates_source() {
			require_once( __DIR__ . '/remote.php' );
			$elementor = Elementor\Plugin::instance();
			$elementor->templates_manager->register_source( 'iMaginem_Templates_Remote' );
		}

		/**
		 * Get Instance
		 */
		public static function get_instance() {

			// Set instance
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}

}

/**
 * Call Template manager
 *
 * @return instance
 */
function imaginem_templates_manager() {
	return iMaginem_Templates_Manager::get_instance();
}

imaginem_templates_manager()->init();