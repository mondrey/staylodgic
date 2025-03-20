<?php

namespace Staylodgic;

class Staylodgic_Registration_Posts {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_guest_registration_submenu_page' ) );

		// Add an action for saving the form data.
		add_action( 'admin_post_staylodgic_save_guestregistry', array( $this, 'save_guestregistry_shortcode' ) );
	}

	/**
	 * Register Registration post
	 *
	 * @return void
	 */
	public function init() {
		$args = array(
			'labels'             => array(
				'name'          => __( 'Guest Registrations', 'staylodgic' ),
				'add_new'       => __( 'Create a Registration', 'staylodgic' ),
				'add_new_item'  => __( 'Add New Registration', 'staylodgic' ),
				'menu_name'     => __( 'Guest Registrations', 'staylodgic' ),
				'singular_name' => __( 'Guest Registration', 'staylodgic' ),
				'all_items'     => __( 'All Registrations', 'staylodgic' ),
			),
			'singular_label'     => __( 'Guest Registration', 'staylodgic' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'has_archive'        => true,
			'menu_position'      => 38,
			'menu_icon'          => 'dashicons-book-alt',
			'rewrite'            => array( 'slug' => 'registrations' ),
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);

		register_post_type( 'staylodgic_guestrgs', $args );

		register_taxonomy(
			'staylodgic_grgscat',
			array( 'staylodgic_guestrgs' ),
			array(
				'labels'       => array(
					'name'          => __( 'Sections', 'staylodgic' ),
					'menu_name'     => __( 'Sections', 'staylodgic' ),
					'singular_name' => __( 'Section', 'staylodgic' ),
					'all_items'     => __( 'All Sections', 'staylodgic' ),
				),
				'public'       => true,
				'hierarchical' => true,
				'show_ui'      => true,
				'rewrite'      => array(
					'slug'         => 'guestregistry-section',
					'hierarchical' => true,
					'with_front'   => false,
				),
			)
		);
	}

	/**
	 * Add sub page
	 *
	 * @return void
	 */
	public function add_guest_registration_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=staylodgic_guestrgs',
			__( 'Guest Registration Shortcodes', 'staylodgic' ),
			__( 'Form Fields', 'staylodgic' ),
			'edit_posts',
			'staylodgic_guestrgs_shortcodes',
			array( $this, 'submenu_page_callback' )
		);
	}

	/**
	 * Displays the submenu page.
	 */
	public function submenu_page_callback() {

		// Check if user has the required capability
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Retrieve saved data
		$saved_shortcode = get_option( 'staylodgic_guestregistry_shortcode', '' );

		if ( '' === $saved_shortcode ) {
			$form_gen_instance = new \Staylodgic\Form_Generator();
			$saved_shortcode   = $form_gen_instance->default_shortcodes();
		}

		$saved_shortcode = stripslashes( $saved_shortcode );

		// HTML for the submenu page
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Guest Registration Fields', 'staylodgic' ) . '</h1>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'staylodgic_guestregistry_save_shortcode', 'staylodgic_guestregistry_nonce' );
		echo '<input type="hidden" name="action" value="staylodgic_save_guestregistry">';
		echo '<textarea name="staylodgic_guestregistry_shortcode" style="width:100%;height:200px;">' . esc_textarea( $saved_shortcode ) . '</textarea>';
		echo '<br><input type="submit" value="Save" class="button button-primary">';
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Handles saving the shortcode.
	 *
	 * @return void
	 */
	public function save_guestregistry_shortcode() {

		// Check the nonce for security
		if ( ! isset( $_POST['staylodgic_guestregistry_nonce'] ) || ! check_admin_referer( 'staylodgic_guestregistry_save_shortcode', 'staylodgic_guestregistry_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed', 'staylodgic' ), 403 );
		}

		// Check if user has the required capability
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Permission denied', 'staylodgic' ), 403 );
		}

		// Check if data has been submitted
		if ( isset( $_POST['staylodgic_guestregistry_shortcode'] ) ) {
			$shortcode_data = sanitize_textarea_field( wp_unslash( $_POST['staylodgic_guestregistry_shortcode'] ) );
			update_option( 'staylodgic_guestregistry_shortcode', $shortcode_data );
		}

		// Redirect back to the settings page
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'staylodgic_guestrgs_shortcodes',
					'updated' => 'true',
				),
				admin_url( 'edit.php?post_type=staylodgic_guestrgs' )
			)
		);
		exit;
	}
}
