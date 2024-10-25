<?php

/**
 * Method staylodgic_get_loggedin_user_email
 *
 * @return void
 */
function staylodgic_get_loggedin_user_email() {
	$user = wp_get_current_user();

	if ( $user ) {
		if ( in_array( 'administrator', $user->roles ) || in_array( 'editor', $user->roles ) ) {
			return $user->user_email;
		}
	}

	return false;
}

/**
 * Method staylodgic_get_page_title_by_template
 *
 * @param $template $template [explicite description]
 *
 * @return void
 */
function staylodgic_get_page_title_by_template( $template ) {
	$pages = staylodgic_get_template_pages();
	foreach ( $pages as $page ) {
		if ( $page['template'] === $template ) {
			return $page['title'];
		}
	}
	return null; // Return null if the template is not found
}

/**
 * Method staylodgic_get_template_pages
 *
 * @return void
 */
function staylodgic_get_template_pages() {
	$pages = array(
		array(
			'title'    => 'Book Room',
			'slug'     => 'book-room',
			'template' => 'template-bookroom.php',
			'content'  => '[hotel_booking_search]',
		),
		array(
			'title'    => 'Book Activity',
			'slug'     => 'book-activity',
			'template' => 'template-bookactivity.php',
			'content'  => '[activity_booking_search]',
		),
		array(
			'title'    => 'Booking Details',
			'slug'     => 'booking-details',
			'template' => 'template-bookingdetails.php',
			'content'  => '[hotel_booking_details]',
		),
		array(
			'title'    => 'Guest Registration',
			'slug'     => 'guest-registration',
			'template' => 'template-guestregistration.php',
			'content'  => '[guest_registration]',
		),
		// Add more pages as needed
	);

	return $pages;
}

/**
 * Method staylodgic_random_color_hex
 *
 * @return void
 */
function staylodgic_random_color_hex() {

	$red   = wp_rand( 0, 255 );
	$green = wp_rand( 0, 255 );
	$blue  = wp_rand( 0, 255 );

	// Convert RGB to hex
	$hex = sprintf( '#%02x%02x%02x', $red, $green, $blue );

	return $hex;
}

/**
 * Method staylodgic_hex_to_rgb
 *
 * @param $hex $hex [explicite description]
 *
 * @return void
 */
function staylodgic_hex_to_rgb( $hex ) {
	// Remove '#' if present
	$hex = str_replace( '#', '', $hex );

	// Check if the input is a valid hex color
	if ( ! preg_match( '/^[a-f0-9]{6}$/i', $hex ) ) {
		// If not valid, generate a random hex color
		$hex = staylodgic_random_color_hex();
	}

	// Split into R, G, B substrings
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	// Return RGB values as an array
	return array(
		'r' => $r,
		'g' => $g,
		'b' => $b,
	);
}

/**
 * Method staylodgic_apply_timezone_to_date_and_time
 *
 * @param $date $date [explicite description]
 * @param $time $time [explicite description]
 * @param $timezone $timezone [explicite description]
 *
 * @return void
 */
function staylodgic_apply_timezone_to_date_and_time( $date, $time, $timezone ) {
	try {
		// Parse the timezone offset
		$offset_pattern = '/GMT([+-])(\d{1,2}):(\d{2})/';
		if ( ! preg_match( $offset_pattern, $timezone, $matches ) ) {
			throw new Exception( 'Invalid timezone format' );
		}

		$sign    = $matches[1];
		$hours   = (int) $matches[2];
		$minutes = (int) $matches[3];
		// Calculate the total number of seconds from hours and minutes
		$total_seconds = ( $hours * 3600 ) + ( $minutes * 60 );

		// Determine the sign multiplier based on the value of $sign
		$multiplier = -1; // Default to negative
		if ( '+' === $sign ) {
			$multiplier = 1; // Change to positive if $sign is '+'
		}

		// Calculate the final offset in seconds
		$offset_in_seconds = $total_seconds * $multiplier;

		// Combine date and time and create DateTime object
		$date_time = new DateTime( $date . ' ' . $time );

		// Apply the offset
		$date_time->modify( $offset_in_seconds . ' seconds' );

		// Return the adjusted date and time
		return array(
			'adjustedDate' => $date_time->format( 'Y-m-d' ),
			'adjustedTime' => $date_time->format( 'H:i:s' ),
		);
	} catch ( Exception $e ) {
		// Handle exceptions or invalid input
		return 'Error: ' . $e->getMessage();
	}
}

/**
 * Method staylodgic_get_gmt_timezone_choices
 *
 * @return void
 */
function staylodgic_get_gmt_timezone_choices() {
	$timezones = array();

	// Start from GMT-12:00 to GMT+14:00
	for ( $i = -12; $i <= 14; $i++ ) {
		$timezone               = $i < 0 ? "GMT$i:00" : ( $i > 0 ? "GMT+$i:00" : 'GMT+00:00' );
		$timezones[ $timezone ] = esc_html( $timezone );
	}

	// Add half-hour and 45-minute offsets if needed
	// Example: $timezones['gmt+5:30'] = esc_html('GMT+5:30');

	return $timezones;
}
/**
 * Method staylodgic_get_booking_homepages_for_select
 *
 * @return void
 */
function staylodgic_get_booking_homepages_for_select() {
	// Get an array of all pages
	$page_list = array();

	$page_list = array( 'none' => 'Choose a page' );

	// Array of allowed templates

	$page_list['template-bookroom.php']     = 'Book Room';
	$page_list['template-bookactivity.php'] = 'Book Activity';

	return $page_list;
}
/**
 * Method staylodgic_get_booking_pages_for_select
 *
 * @return void
 */
function staylodgic_get_booking_pages_for_select() {
	// Get an array of all pages
	$page_list = array();

	$page_list = array( 'none' => 'Choose a page' );

	// Array of allowed templates

	$page_list['template-bookroom.php']          = 'Book Room';
	$page_list['template-bookactivity.php']      = 'Book Activity';
	$page_list['template-guestregistration.php'] = 'Guest Registration';
	$page_list['template-bookingdetails.php']    = 'Booking Details';

	return $page_list;
}
/**
 * Method staylodgic_get_pages_for_select
 *
 * @return void
 */
function staylodgic_get_pages_for_select() {
	// Get an array of all pages
	$pages     = get_pages();
	$page_list = array();

	$page_list = array( 'none' => 'Choose a page' );

	// Loop through the pages and add them to the list
	foreach ( $pages as $page ) {
		$page_list[ $page->ID ] = $page->post_title;
	}

	return $page_list;
}

/**
 * Method staylodgic_is_valid_sync_interval
 *
 * @param $qtysync_interval $qtysync_interval [explicite description]
 *
 * @return void
 */
function staylodgic_is_valid_sync_interval( $qtysync_interval ) {
	// Retrieve the array of sync intervals
	$sync_intervals = staylodgic_sync_intervals();

	// Check if the provided interval is a key in the sync intervals array
	return array_key_exists( $qtysync_interval, $sync_intervals );
}

/**
 * Method staylodgic_sync_intervals
 *
 * @return void
 */
function staylodgic_sync_intervals() {
	$sync_intervals = array(
		'1'  => esc_attr__( 'One Minute', 'staylodgic' ),
		'5'  => esc_attr__( 'Five Minutes', 'staylodgic' ),
		'10' => esc_attr__( 'Ten Minutes', 'staylodgic' ),
		'15' => esc_attr__( 'Fifteen Minutes', 'staylodgic' ),
		'30' => esc_attr__( 'Thirty Minutes', 'staylodgic' ),
		'60' => esc_attr__( 'Sixty Minutes', 'staylodgic' ),
	);

	return $sync_intervals;
}
/**
 * Method staylodgic_format_date
 *
 * @param $stay_date_string $stay_date_string [explicite description]
 * @param $format_choice $format_choice [explicite description]
 *
 * @return void
 */
function staylodgic_format_date( $stay_date_string, $format_choice = 'monthshort_first' ) {
	$formatted_date = '';
	$date_time      = new DateTime( $stay_date_string );

	switch ( $format_choice ) {
		case 'default':
			$formatted_date = $date_time->format( 'Y-m-d' );
			break;
		case 'short':
			$formatted_date = $date_time->format( 'M d, Y' );
			break;
		case 'long':
			$formatted_date = $date_time->format( 'F d, Y' );
			break;
		case 'monthshort_first':
			$formatted_date = $date_time->format( 'M jS, Y' );
			break;
		case 'monthshort_after':
			$formatted_date = $date_time->format( 'jS M, Y' );
			break;
		// Add more format choices as needed
		default:
			$formatted_date = $date_time->format( 'Y-m-d' );
			break;
	}

	return $formatted_date;
}
/**
 * Method staylodgic_get_booking_substatuses
 *
 * @return void
 */
function staylodgic_get_booking_substatuses() {
	$booking_sub_statuses = array(
		'completed'      => esc_attr__( 'Completed', 'staylodgic' ),
		'checkedin'      => esc_attr__( 'Checked-In', 'staylodgic' ),
		'checkedout'     => esc_attr__( 'Checked-Out', 'staylodgic' ),
		'noshow'         => esc_attr__( 'No Show', 'staylodgic' ),
		'onhold'         => esc_attr__( 'On Hold', 'staylodgic' ),
		'pendingpayment' => esc_attr__( 'Pending Payment', 'staylodgic' ),
		'refunded'       => esc_attr__( 'Refunded', 'staylodgic' ),
		'inprogress'     => esc_attr__( 'In Progress', 'staylodgic' ),
		'expired'        => esc_attr__( 'Expired', 'staylodgic' ),
	);

	return $booking_sub_statuses;
}
/**
 * Method staylodgic_get_booking_statuses
 *
 * @return void
 */
function staylodgic_get_booking_statuses() {
	$booking_statuses = array(
		'confirmed' => esc_attr__( 'Confirmed', 'staylodgic' ),
		'cancelled' => esc_attr__( 'Cancelled', 'staylodgic' ),
		'pending'   => esc_attr__( 'Pending', 'staylodgic' ),
	);

	return $booking_statuses;
}
/**
 * Method staylodgic_get_new_booking_statuses
 *
 * @return void
 */
function staylodgic_get_new_booking_statuses() {
	$booking_statuses = array(
		'confirmed' => esc_attr__( 'Confirmed', 'staylodgic' ),
		'pending'   => esc_attr__( 'Pending', 'staylodgic' ),
	);

	return $booking_statuses;
}
/**
 * Method staylodgic_get_all_bed_layouts
 *
 * @param $bed_names $bed_names [explicite description]
 *
 * @return void
 */
function staylodgic_get_all_bed_layouts( $bed_names ) {
	$html            = '';
	$bed_names_array = explode( ' ', $bed_names );
	foreach ( $bed_names_array as $key => $bed_name ) {
		$html .= staylodgic_get_bed_layout( $bed_name, $key );
	}

	return $html;
}
/**
 * Method staylodgic_get_bed_layout
 *
 * @param $bed_name $bed_name [explicite description]
 * @param $bed_field_id $bed_field_id [explicite description]
 *
 * @return void
 */
function staylodgic_get_bed_layout( $bed_name, $bed_field_id = null ) {

	switch ( $bed_name ) {
		case 'fullbed':
			$html = '<div class="guest-bed guest-bed-' . sanitize_title( $bed_name ) . '"></div>';
			break;
		case 'queenbed':
			$html = '<div class="guest-bed guest-bed-' . sanitize_title( $bed_name ) . '"></div>';
			break;
		case 'kingbed':
			$html = '<div class="guest-bed guest-bed-' . sanitize_title( $bed_name ) . '"></div>';
			break;
		case 'sofabed':
			$html = '<div class="guest-bed guest-bed-' . sanitize_title( $bed_name ) . '"></div>';
			break;
		case 'bunkbed':
			$html = '<div class="guest-bed guest-bed-' . sanitize_title( $bed_name ) . '"></div>';
			break;
		case 'twinbed':
			$html = '<div class="guest-bed type-twinbed-twinbed-' . $bed_field_id . ' guest-bed-' . $bed_name . '"></div>';
			break;
	}

	return $html;
}

/**
 * Method Function to recursively format arrays as strings
 *
 * @param $value $value [explicite description]
 *
 * @return void
 */
function staylodgic_format_value( $value ) {
	$formatted_elements = '';
	if ( is_array( $value ) ) {
		$formatted_start = '<ul>';
		foreach ( $value as $key => $item ) {
			$formatted_elements .= '<li><strong>' . $key . ':</strong> ' . staylodgic_format_value( $item ) . '</li>';
		}
		$formatted_end = '</ul>';
		if ( '' === $formatted_elements ) {
			return false;
		} else {
			return $formatted_start . $formatted_elements . $formatted_end;
		}
	} else {
		return $value;
	}
}
/**
 * Method staylodgic_readable_date
 *
 * @param $original_date $original_date [explicite description]
 *
 * @return void
 */
function staylodgic_readable_date( $original_date ) {
	$formatted_date = gmdate( 'F jS, Y', strtotime( $original_date ) );

	return $formatted_date;
}

/**
 * Method staylodgic_get_option
 *
 * @param $option $option [explicite description]
 * @param $default $default [explicite description]
 *
 * @return void
 */
function staylodgic_get_option( $option, $default_value = '' ) {
	$settings = get_option( 'staylodgic_settings' );

	if ( is_array( $settings ) && isset( $settings[ $option ] ) ) {
		return $settings[ $option ];
	}

	return $default_value;
}

/**
 * Method staylodgic_price
 *
 * @param $original_price $original_price [explicite description]
 *
 * @return void
 */
function staylodgic_price( $original_price ) {
	$currency           = staylodgic_get_option( 'currency', 'USD' );
	$currency_position  = staylodgic_get_option( 'currency_position', 'left' );
	$thousand_seperator = staylodgic_get_option( 'thousand_seperator', ',' );
	$decimal_seperator  = staylodgic_get_option( 'decimal_seperator', '.' );
	$number_of_decimals = staylodgic_get_option( 'number_of_decimals', '2' );

	// Format the price using number_format
	$price = number_format( $original_price, $number_of_decimals, $decimal_seperator, $thousand_seperator );

	if ( '' === $price ) {
		$price = $original_price;
	}

	$formatted_price = '<span class="formatted-price" date-price="' . esc_attr( $price ) . '" date-currency="' . esc_attr( $currency ) . '">';
	// Adjust the position of the currency symbol
	if ( 'left' === $currency_position ) {
		$formatted_price .= '<span class="currency">' . $currency . '</span> <span class="price">' . $price . '</span>';
	} else {
		$formatted_price .= '<span class="price">' . $price . '</span> <span class="currency">' . $currency . '</span>';
	}
	$formatted_price .= '</span>';

	return $formatted_price;
}

/**
 * Method staylodgic_reverse_percentage
 *
 * @param $total $total [explicite description]
 * @param $percentages $percentages [explicite description]
 *
 * @return void
 */
function staylodgic_reverse_percentage( $total, $percentages ) {
	$initial_value = $total;
	foreach ( $percentages as $percentage ) {
		$initial_value = $initial_value / ( 1 + ( $percentage / 100 ) );
	}
	return $initial_value;
}

/**
 * Method staylodgic_has_tax
 *
 * @return void
 */
function staylodgic_has_tax() {

	$tax_flag = staylodgic_get_option( 'enable_taxes' );
	return $tax_flag;
}
/**
 * Method staylodgic_has_activity_tax
 *
 * @return void
 */
function staylodgic_has_activity_tax() {

	$tax_flag = staylodgic_get_option( 'enable_activitytaxes' );
	return $tax_flag;
}

/**
 * Method staylodgic_get_mealplan_labels
 *
 * @param $mealtype $mealtype [explicite description]
 *
 * @return void
 */
function staylodgic_get_mealplan_labels( $mealtype ) {
	switch ( $mealtype ) {
		case 'BB':
			return __( 'Breakfast', 'staylodgic' );
		case 'HB':
			return __( 'Halfboard', 'staylodgic' );
		case 'FB':
			return __( 'Fullboard', 'staylodgic' );
		case 'AN':
			return __( 'All inclusive', 'staylodgic' );
		default:
			return '';
	}
}

/**
 * Method staylodgic_delete_booking_transient
 *
 * @param $stay_booking_number $stay_booking_number [explicite description]
 *
 * @return void
 */
function staylodgic_delete_booking_transient( $stay_booking_number ) {
	delete_transient( $stay_booking_number );
}
/**
 * Method staylodgic_set_booking_transient
 *
 * @param $data $data [explicite description]
 * @param $stay_booking_number $stay_booking_number [explicite description]
 *
 * @return void
 */
function staylodgic_set_booking_transient( $data, $stay_booking_number ) {

	set_transient( $stay_booking_number, $data, 20 * MINUTE_IN_SECONDS );
}
/**
 * Method staylodgic_get_booking_transient
 *
 * @param $stay_booking_number $stay_booking_number [explicite description]
 *
 * @return void
 */
function staylodgic_get_booking_transient( $stay_booking_number = null ) {
	return get_transient( $stay_booking_number );
}
function staylodgic_get_guest_activities_allowed_tags() {

	$allowed_html = array(
		'html'   => array(),
		'body'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'p'      => array(),
		'a'      => array(
			'href'  => array(),
			'title' => array(),
		),
		'br'     => array(),
		'small'  => array(),
		'strong' => array(),
		'div'    => array(
			'class' => array(),
		),
		'span'   => array(
			'class' => array(),
		),
		'i'      => array(
			'class' => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_bedlayout_allowed_tags() {

	$allowed_html = array(
		'html'  => array(),
		'body'  => array(),
		'label' => array(
			'for' => array(),
		),
		'input' => array(
			'type'  => array(),
			'id'    => array(),
			'name'  => array(),
			'value' => array(),
		),
		'span'  => array(
			'class' => array(),
		),
		'div'   => array(
			'class' => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_price_allowed_tags() {
	$allowed_html = array(
		'span' => array(
			'class'         => array(),
			'date-price'    => array(),
			'date-currency' => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_ticket_allowed_tags() {
	$allowed_html = array(
		'html' => array(),
		'body' => array(),
		'div'  => array(
			'class'              => array(),
			'data-file'          => array(),
			'data-postid'        => array(),
			'id'                 => array(),
			'data-bookingnumber' => array(),
			'style'              => array(),
			'data-qrcode'        => array(),
		),
		'p'    => array(
			'class' => array(),
		),
		'h1'   => array(),
		'i'    => array(
			'class' => array(),
		),
		'span' => array(
			'class'         => array(),
			'date-price'    => array(),
			'date-currency' => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_form_allowed_tags() {
	$allowed_html = array(
		'html'     => array(),
		'body'     => array(),
		'div'      => array(
			'class' => array(),
			'id'    => array(),
		),
		'i'        => array(
			'class' => array(),
		),
		'h3'       => array(),
		'input'    => array(
			'placeholder' => array(),
			'type'        => array(),
			'class'       => array(),
			'id'          => array(),
			'name'        => array(),
			'required'    => array(),
		),
		'label'    => array(
			'for'   => array(),
			'class' => array(),
		),
		'select'   => array(
			'required' => array(),
			'class'    => array(),
			'id'       => array(),
			'name'     => array(),
		),
		'option'   => array(
			'selected' => array(),
			'disabled' => array(),
			'value'    => array(),
		),
		'textarea' => array(
			'class' => array(),
			'id'    => array(),
			'name'  => array(),
		),
		'span'     => array(
			'class' => array(),
		),
		'p'        => array(),
		'a'        => array(
			'href' => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_booking_allowed_tags() {
	$allowed_html = array(
		'html'  => array(),
		'body'  => array(),
		'div'   => array(
			'class'               => array(),
			'data-adults'         => array(),
			'data-children'       => array(),
			'data-guests'         => array(),
			'data-room-id'        => array(),
			'data-roomprice'      => array(),
			'for'                 => array(),
			'data-room-button-id' => array(),
		),
		'a'     => array(
			'href'         => array(),
			'data-toggle'  => array(),
			'data-gallery' => array(),
			'class'        => array(),
		),
		'img'   => array(
			'class'      => array(),
			'data-image' => array(),
			'src'        => array(),
			'alt'        => array(),
		),
		'span'  => array(
			'class'         => array(),
			'date-price'    => array(),
			'date-currency' => array(),
		),
		'i'     => array(
			'class'       => array(),
			'aria-hidden' => array(),
		),
		'h2'    => array(),
		'label' => array(
			'for' => array(),
		),
		'input' => array(
			'hidden'           => array(),
			'type'             => array(),
			'name'             => array(),
			'value'            => array(),
			'class'            => array(),
			'data-type'        => array(),
			'data-roominputid' => array(),
			'data-roomqty'     => array(),
			'id'               => array(),
			'min'              => array(),
			'max'              => array(),
			'checked'          => array(),
			'data-mealprice'   => array(),
		),
	);

	return $allowed_html;
}
function staylodgic_get_calendar_allowed_tags() {
	$allowed_html = array(
		'html'  => array(),
		'body'  => array(),
		'table' => array(
			'id'            => array(),
			'data-calstart' => array(),
			'data-calend'   => array(),
		),
		'tr'    => array(
			'class'   => array(),
			'data-id' => array(),
		),
		'td'    => array(
			'class'               => array(),
			'data-roomsremaining' => array(),
		),
		'div'   => array(
			'data-occupancypercent'  => array(),
			'class'                  => array(),
			'data-day'               => array(),
			'data-reservationstatus' => array(),
			'data-guest'             => array(),
			'data-room'              => array(),
			'data-row'               => array(),
			'data-bookingnumber'     => array(),
			'data-reservationid'     => array(),
			'data-checkin'           => array(),
			'data-checkout'          => array(),
			'data-tabwidth'          => array(),
		),
		'span'  => array(
			'class' => array(),
		),
		'br'    => array(),
		'a'     => array(
			'data-bs-toggle'    => array(),
			'data-bs-placement' => array(),
			'data-bs-title'     => array(),
			'href'              => array(),
			'class'             => array(),
			'data-remaining'    => array(),
			'data-date'         => array(),
			'data-room'         => array(),
			'data-rate'         => array(),
			'data-bs-delay'     => array(),
		),
	);

	return $allowed_html;
}
/**
 * Method staylodgic_get_allowed_tags
 *
 * @return void
 */
function staylodgic_get_allowed_tags() {
	$structure_allowed_tags = array(
		'canvas'     => array(
			'id'           => array(),
			'class'        => array(),
			'data-type'    => array(),
			'data-data'    => array(),
			'data-options' => array(),
		),
		'option'     => array(
			'value'    => array(),
			'selected' => array(),
			'disabled' => array(),
		),
		'col'        => array(
			'span' => true,
		),
		'colgroup'   => array(
			'span' => true,
		),
		'table'      => array(
			'id'                => array(),
			'class'             => array(),
			'data-export-title' => array(),
			'data-calstart'     => array(),
			'data-calend'       => array(),
		),
		'tbody'      => array(
			'class' => array(),
		),
		'td'         => array(
			'class'               => true,
			'colspan'             => true,
			'headers'             => true,
			'rowspan'             => true,
			'scope'               => array(),
			'data-id'             => array(),
			'data-roomsremaining' => array(),
			'data-day'            => array(),
		),
		'tfoot'      => array(),
		'th'         => array(
			'class'   => array(),
			'abbr'    => true,
			'colspan' => true,
			'headers' => true,
			'rowspan' => true,
			'scope'   => true,
		),
		'thead'      => array(),
		'tr'         => array(),
		'a'          => array(
			'id'                => true,
			'data-gallery'      => array(),
			'data-toggle'       => array(),
			'class'             => true,
			'href'              => array(),
			'rel'               => true,
			'rev'               => true,
			'name'              => true,
			'target'            => true,
			'data-rate'         => array(),
			'data-date'         => array(),
			'data-room'         => array(),
			'data-bs-toggle'    => array(),
			'data-bs-placement' => array(),
			'data-bs-title'     => array(),
			'data-bs-delay'     => array(),
		),
		'form'       => array(
			'class'          => true,
			'id'             => true,
			'action'         => true,
			'accept'         => true,
			'accept-charset' => true,
			'enctype'        => true,
			'method'         => true,
			'name'           => true,
			'target'         => true,
		),
		'input'      => array(
			'class'   => array(),
			'id'      => array(),
			'type'    => array(),
			'name'    => array(),
			'value'   => array(),
			'checked' => array(),
		),
		'select'     => array(
			'class' => array(),
			'id'    => array(),
			'type'  => array(),
			'name'  => array(),
			'value' => array(),
		),
		'section'    => array(
			'id'       => true,
			'class'    => true,
			'align'    => true,
			'dir'      => true,
			'lang'     => true,
			'xml:lang' => true,
		),
		'svg'        => array(
			'id'      => true,
			'class'   => true,
			'width'   => true,
			'height'  => true,
			'viewbox' => true,
			'align'   => true,
			'xmlns'   => true,
		),
		'g'          => array(
			'fill'              => true,
			'stroke'            => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
		),
		'circle'     => array(
			'class' => true,
			'cx'    => true,
			'cy'    => true,
			'r'     => true,
		),
		'path'       => array(
			'class' => true,
			'd'     => true,
		),
		'abbr'       => array(),
		'acronym'    => array(),
		'b'          => array(),
		'bdo'        => array(
			'dir' => true,
		),
		'big'        => array(),
		'blockquote' => array(
			'cite'     => true,
			'lang'     => true,
			'xml:lang' => true,
		),
		'br'         => array(),
		'caption'    => array(
			'align' => true,
		),
		'div'        => array(
			'aria-live'             => array(),
			'aria-atomic'           => array(),
			'class'                 => array(),
			'id'                    => array(),
			'role'                  => array(),
			'style'                 => true,
			'data-color'            => true,
			'align'                 => true,
			'dir'                   => true,
			'lang'                  => true,
			'xml:lang'              => true,
			'data-occupancypercent' => array(),
			'data-remaining'        => array(),
			'data-date'             => array(),
			'data-room'             => array(),
			'data-bs-toggle'        => array(),
			'data-bs-placement'     => array(),
			'data-bs-title'         => array(),
			'data-bs-delay'         => array(),
		),
		'dl'         => array(),
		'dt'         => array(),
		'em'         => array(),
		'h1'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'h2'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'h3'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'h4'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'h5'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'h6'         => array(
			'class' => array(),
			'id'    => array(),
			'align' => true,
		),
		'hr'         => array(
			'align'   => true,
			'noshade' => true,
			'size'    => true,
			'width'   => true,
		),
		'i'          => array(
			'id'    => true,
			'class' => array(),
		),
		'img'        => array(
			'id'          => true,
			'class'       => true,
			'alt'         => true,
			'data-src'    => true,
			'data-srcset' => true,
			'srcset'      => true,
			'sizes'       => true,
			'align'       => true,
			'border'      => true,
			'height'      => true,
			'hspace'      => true,
			'longdesc'    => true,
			'vspace'      => true,
			'src'         => true,
			'usemap'      => true,
			'width'       => true,
		),
		'label'      => array(
			'for' => true,
		),
		'li'         => array(
			'data-id'       => true,
			'data-title'    => true,
			'data-position' => true,
			'class'         => true,
			'align'         => true,
			'value'         => true,
		),
		'p'          => array(
			'id'       => true,
			'class'    => true,
			'align'    => true,
			'dir'      => true,
			'lang'     => true,
			'xml:lang' => true,
		),
		'pre'        => array(
			'width' => true,
		),
		'q'          => array(
			'cite' => true,
		),
		'span'       => array(
			'dir'           => true,
			'id'            => true,
			'class'         => true,
			'align'         => true,
			'lang'          => true,
			'xml:lang'      => true,
			'data-price'    => array(),
			'data-currency' => array(),
		),
		'small'      => array(
			'class' => array(),
		),
		'strike'     => array(),
		'strong'     => array(
			'class' => array(),
		),
		'title'      => array(),
		'u'          => array(),
		'ul'         => array(
			'type'  => true,
			'id'    => true,
			'class' => true,
		),
		'ol'         => array(
			'id'       => true,
			'class'    => true,
			'start'    => true,
			'type'     => true,
			'reversed' => true,
		),
		'button'     => array(
			'type'            => array(),
			'class'           => array(),
			'data-bs-dismiss' => array(),
			'aria-label'      => array(),
		),
	);

	return $structure_allowed_tags;
}
