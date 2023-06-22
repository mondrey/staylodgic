<?php
class AtollMatrix_Customer_Posts {

	function __construct() 
	{
		add_action('init', array( $this, 'init'));

		add_filter("manage_edit-customers_columns", array( $this, 'customers_edit_columns'));
		add_filter('manage_posts_custom_column' , array( $this, 'customers_custom_columns'));
	}

	// Kbase lister
	function customers_edit_columns($columns){
		$new_columns = array(
			"customer_booking" => __('Booking','mthemelocal'),
			"customer_reservations" => __('Reservations','mthemelocal'),
			"customer_rooms" => __('Rooms','mthemelocal'),
			"mcustomer_section" => __('Section','mthemelocal')
		);
	
		return array_merge($columns, $new_columns);
	}
	function customers_custom_columns($columns) {
		global $post;
		$custom = get_post_custom();
		$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );
		
		$full_image_id = get_post_thumbnail_id(($post->ID), 'fullimage'); 
		$full_image_url = wp_get_attachment_image_src($full_image_id,'fullimage');  
		if ( isset ($full_image_url[0]) ) {
			$full_image_url = $full_image_url[0];
		}

		if (isset($custom['pagemeta_booking_number'][0])) {
			$booking_number=$custom['pagemeta_booking_number'][0];
		}

		switch ($columns)
		{
			case "customer_booking":
				echo $booking_number;
				break;
			case "customer_reservations":
				$reservation_array = \AtollMatrix\Reservations::getReservationIDsForCustomer( $post->ID );
				echo \AtollMatrix\Reservations::getEditLinksForReservations( $reservation_array );
				break;
			case "customer_rooms":
				$room_ids = \AtollMatrix\Reservations::getRoomIDsForBooking_number( $booking_number );
				$room_names_string = \AtollMatrix\Rooms::getRoomNames_FromIDs($room_ids);
				echo $room_names_string;
				break;
			case "mcustomer_section":
				echo get_the_term_list( get_the_id(), 'customersection', '', ', ','' );
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
		//add_action('init', 'mtheme_featured_register');
		//add_action('init', 'mtheme_kbase_register');//Always use a shortname like "mtheme_" not to see any 404 errors
		/*
		* Register kbase Post Manager
		*/

		$mtheme_customers_slug="customers";
		if (function_exists('superlens_get_option_data')) {
			$mtheme_customers_slug = superlens_get_option_data('customers_permalink_slug');
		}
		if ( $mtheme_customers_slug=="" || !isSet($mtheme_customers_slug) ) {
			$mtheme_customers_slug="customers";
		}
		$args = array(
			'labels' => array(
				'name' => 'Customers',
				'menu_name' => 'Customers',
				'singular_name' => 'Customer',
				'all_items' => 'All Customers'
			),
			'singular_label' => __('Customer','mthemelocal'),
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
			'rewrite' => array('slug' => $mtheme_customers_slug),//Use a slug like "work" or "project" that shouldnt be same with your page name
			'supports' => array('title', 'author', 'thumbnail')//Boxes will be shown in the panel
		);
	
		register_post_type( 'customers' , $args );
		/*
		* Add Taxonomy for kbase 'Type'
		*/
		register_taxonomy( 'customersection', array( 'customers' ),
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
				'rewrite' => array( 'slug' => 'customers-section', 'hierarchical' => true, 'with_front' => false ),
			)
		);

	}
	
}
$mtheme_kbase_post_type = new AtollMatrix_Customer_Posts();
?>