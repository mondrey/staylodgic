<?php
/**
 * Pages Helper Class
 *
 * Responsible for creating initial pages with shortcodes and template mapping.
 *
 * @package Staylodgic
 */

namespace Staylodgic\Helpers;

class Pages_Helper {

	/**
	 * Create all initial pages defined in staylodgic_get_template_pages().
	 *
	 * @return void
	 */
	public static function create_initial_pages() {
		$pages = self::get_template_pages();

		foreach ( $pages as $page ) {
			self::create_custom_page(
				$page['title'] ?? '',
				$page['template'] ?? 'default',
				$page['content'] ?? '',
				$page['slug'] ?? sanitize_title( $page['title'] ?? '' )
			);
		}
	}

	/**
	 * Creates a custom page if it doesn't already exist.
	 *
	 * @param string $title    The title of the page.
	 * @param string $template Template file (e.g., 'template-book.php').
	 * @param string $content  Page content (e.g., shortcode).
	 * @param string $slug     URL slug for the page.
	 * @return int|false       Page ID on success, false on failure.
	 */
	public static function create_custom_page( $title, $template, $content, $slug ) {
		$existing_page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing_page ) {
			return $existing_page->ID;
		}

		$page_data = array(
			'post_title'   => wp_strip_all_tags( $title ),
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => $slug,
			'meta_input'   => array(
				'_wp_page_template' => $template,
			),
		);

		return wp_insert_post( $page_data );
	}

	/**
	 * Returns predefined list of template pages.
	 *
	 * @return array
	 */
	public static function get_template_pages() {
		return array(
			array(
				'title'    => 'Book Room',
				'slug'     => 'book-room',
				'template' => 'template-bookroom.php',
				'content'  => '[staylodgic_hotel_booking_search]',
			),
			array(
				'title'    => 'Book Activity',
				'slug'     => 'book-activity',
				'template' => 'template-bookactivity.php',
				'content'  => '[staylodgic_activity_booking_search]',
			),
			array(
				'title'    => 'Booking Details',
				'slug'     => 'booking-details',
				'template' => 'template-bookingdetails.php',
				'content'  => '[staylodgic_hotel_booking_details]',
			),
			array(
				'title'    => 'Guest Registration',
				'slug'     => 'guest-registration',
				'template' => 'template-guestregistration.php',
				'content'  => '[staylodgic_guest_registration]',
			),
		);
	}

	/**
	 * Returns the page title based on template file name.
	 *
	 * @param string $template Template file to search for.
	 * @return string|null     Page title if found, null if not.
	 */
	public static function get_page_title_by_template( $template ) {
		$pages = self::get_template_pages();
		foreach ( $pages as $page ) {
			if ( $page['template'] === $template ) {
				return $page['title'];
			}
		}
		return null;
	}
}
