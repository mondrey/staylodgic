<?php
class atollmatrix_Reservation_Posts {

	function __construct() 
	{	
		add_action('init', array( $this, 'init'));

		add_filter("manage_edit-reservations_columns", array( $this, 'reservations_edit_columns'));
		add_filter('manage_posts_custom_column' , array( $this, 'reservations_custom_columns'));
	}

	// Kbase lister
	function reservations_edit_columns($columns){
		$new_columns = array(
			"mreservation_section" => __('Section','mthemelocal'),
			"reservation_customer" => __('Customer','mthemelocal'),
			"reservation_room" => __('Room','mthemelocal')
		);
	
		return array_merge($columns, $new_columns);
	}
	function reservations_custom_columns($columns) {
		global $post;
		$custom = get_post_custom();
		$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );
		
		$full_image_id = get_post_thumbnail_id(($post->ID), 'fullimage'); 
		$full_image_url = wp_get_attachment_image_src($full_image_id,'fullimage');  
		if ( isset ($full_image_url[0]) ) {
			$full_image_url = $full_image_url[0];
		}

		$reservation_instance = new \AtollMatrix\Reservations( $date = false, $room_id = false, $reservation_id = $post->ID );
		switch ($columns) {
			case "reservation_customer":
				$customer_name = $reservation_instance->getCustomerEditLinkForReservation();
				echo $customer_name;
				break;
			case "reservation_room":
				$room_title = $reservation_instance->getRoomTitleForReservation();
				echo $room_title;
				break;
			case "mreservation_section":
				echo get_the_term_list( get_the_id(), 'reservationsection', '', ', ','' );
				break;
		} 
	}
	/*
	* kbase Admin columns
	*/
	
	/**
	 * Registers TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function init()
	{
		/*
		* Register Featured Post Manager
		*/
		//add_action('init', 'atollmatrix_featured_register');
		//add_action('init', 'atollmatrix_kbase_register');//Always use a shortname like "atollmatrix_" not to see any 404 errors
		/*
		* Register kbase Post Manager
		*/

		$atollmatrix_reservations_slug="reservations";
		if (function_exists('atollmatrix_get_option_data')) {
			$atollmatrix_reservations_slug = atollmatrix_get_option_data('reservations_permalink_slug');
		}
		if ( $atollmatrix_reservations_slug=="" || !isSet($atollmatrix_reservations_slug) ) {
			$atollmatrix_reservations_slug="reservations";
		}
		$args = array(
			'labels' => array(
				'name' => 'Reservations',
				'menu_name' => 'Reservations',
				'singular_name' => 'Reservation',
				'all_items' => 'All Reservations'
			),
			'singular_label' => __('Reservation','mthemelocal'),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'has_archive' =>true,
			'menu_position' => 6,
			'menu_icon' => plugin_dir_url( __FILE__ ) . 'images/portfolio.png',
			'rewrite' => array('slug' => $atollmatrix_reservations_slug),//Use a slug like "work" or "project" that shouldnt be same with your page name
			'supports' => array('title', 'author', 'thumbnail')//Boxes will be shown in the panel
		);
	
		register_post_type( 'reservations' , $args );
		/*
		* Add Taxonomy for kbase 'Type'
		*/
		register_taxonomy( 'reservationsection', array( 'reservations' ),
			array(
				'labels' => array(
					'name' => 'Sections',
					'menu_name' => 'Sections',
					'singular_name' => 'Section',
					'all_items' => 'All Sections'
				),
				'public' => true,
				'hierarchical' => true,
				'show_ui' => true,
				'rewrite' => array( 'slug' => 'reservations-section', 'hierarchical' => true, 'with_front' => false ),
			)
		);

	}
	
}
$atollmatrix_kbase_post_type = new atollmatrix_Reservation_Posts();
?>