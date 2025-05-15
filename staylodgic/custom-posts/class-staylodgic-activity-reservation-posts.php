<?php

namespace Staylodgic;

class Staylodgic_Activity_Reservation_Posts {


	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );

		add_filter( 'manage_edit-staylodgic_actvtres_columns', array( $this, 'staylodgic_actvtres_edit_columns' ) );
		add_filter( 'manage_staylodgic_actvtres_posts_custom_column', array( $this, 'staylodgic_actvtres_custom_columns' ) );
	}

	/**
	 * Activity reservation post columns
	 *
	 * @return void
	 */
	public function staylodgic_actvtres_edit_columns( $columns ) {
		unset( $columns['author'] );
		$new_columns = array(
			'reservation_customer'  => __( 'Customer', 'staylodgic' ),
			'reservation_bookingnr' => __( 'Booking Number', 'staylodgic' ),
			'reservation_checkin'   => __( 'Activity Day', 'staylodgic' ),
			'reservation_status'    => __( 'Status', 'staylodgic' ),
			'reservation_substatus' => __( 'Sub Status', 'staylodgic' ),
			'reservation_activitiy' => __( 'Activity', 'staylodgic' ),
		);

		return array_merge( $columns, $new_columns );
	}

	public function staylodgic_actvtres_custom_columns( $columns ) {
		global $post;
		$custom    = get_post_custom();
		$image_url = wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );

		$full_image_id  = get_post_thumbnail_id( ( $post->ID ), 'fullimage' );
		$full_image_url = wp_get_attachment_image_src( $full_image_id, 'fullimage' );
		if ( isset( $full_image_url[0] ) ) {
			$full_image_url = $full_image_url[0];
		}
		$stay_booking_number  = null;
		$reservation_id       = $post->ID;
		$reservation_instance = new \Staylodgic\Activity( $stay_booking_number, $reservation_id );
		$bookingnumber        = $reservation_instance->get_booking_number();

		switch ( $columns ) {
			case 'reservation_customer':
				$customer_name = $reservation_instance->get_customer_edit_link_for_reservation();
				if ( null !== $customer_name ) {
					echo wp_kses( $customer_name, staylodgic_get_allowed_tags() );
				}
				break;
			case 'reservation_bookingnr':
				echo esc_attr( $bookingnumber );
				break;
			case 'reservation_checkin':
				$reservation_checkin = $reservation_instance->get_checkin_date();
				echo '<p class="post-status-reservation-date post-status-reservation-date-checkin"><i class="fa-solid fa-arrow-right"></i> ' . esc_attr( staylodgic_format_date( $reservation_checkin ) ) . '</p>';

				break;
			case 'reservation_status':
				$reservation_status = $reservation_instance->get_reservation_status();

				echo '<p class="booking-status-' . esc_attr( $reservation_status ) . '">';
				echo esc_attr( ucfirst( $reservation_status ) );
				echo '</p>';
				if ( staylodgic_has_payment_order( $reservation_id ) ) {
					echo '<p class="booking-status-payment">Payment Order</p>';
				}
				break;
			case 'reservation_substatus':
				$reservation_substatus = $reservation_instance->get_reservation_sub_status();
				echo esc_attr( ucfirst( $reservation_substatus ) );
				break;
			case 'reservation_activitiy':
				$activity_title = $reservation_instance->get_name_for_activity();
				echo esc_html( $activity_title );
				break;
		}
	}

	/**
	 * Register activityreservation Post Manager
	 *
	 * @return void
	 */
	public function init() {

		$labels = array(
			'name'               => _x( 'Activity Reservations', 'post type general name', 'staylodgic' ),
			'singular_name'      => _x( 'Activity Reservation', 'post type singular name', 'staylodgic' ),
			'menu_name'          => _x( 'Activity Reservations', 'admin menu', 'staylodgic' ),
			'name_admin_bar'     => _x( 'Activity Reservation', 'add new on admin bar', 'staylodgic' ),
			'add_new'            => _x( 'Add New Reservation', 'activity reservation', 'staylodgic' ),
			'add_new_item'       => __( 'Add New Activity Reservation', 'staylodgic' ),
			'new_item'           => __( 'New Activity Reservation', 'staylodgic' ),
			'edit_item'          => __( 'Edit Activity Reservation', 'staylodgic' ),
			'view_item'          => __( 'View Activity Reservation', 'staylodgic' ),
			'all_items'          => __( 'All Activity Reservations', 'staylodgic' ),
			'search_items'       => __( 'Search Activity Reservations', 'staylodgic' ),
			'parent_item_colon'  => __( 'Parent Activity Reservations:', 'staylodgic' ),
			'not_found'          => __( 'No activity reservations found.', 'staylodgic' ),
			'not_found_in_trash' => __( 'No activity reservations found in Trash.', 'staylodgic' ),
		);

		$args = array(
			'labels'             => $labels,
			'singular_label'     => __( 'Activity Reservation', 'staylodgic' ),
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'has_archive'        => true,
			'menu_position'      => 40,
			'menu_icon'          => 'dashicons-tickets-alt',
			'rewrite'            => array( 'slug' => 'activityreservations' ),
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);

		register_post_type( 'staylodgic_actvtres', $args );
		/*
		 * Add Taxonomy for activityreservation
		 */
		register_taxonomy(
			'staylodgic_acrestype',
			array( 'staylodgic_actvtres' ),
			array(
				'hierarchical'   => true,
				'label'          => 'Activity Reservation Category',
				'singular_label' => 'staylodgic_activityrestypes',
				'rewrite'        => true,
			)
		);
	}
}
