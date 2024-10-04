<?php

/**
 * Method staylodgic_getLoggedInUserEmail
 *
 * @return void
 */
function staylodgic_getLoggedInUserEmail() {
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
function staylodgic_get_page_title_by_template($template) {
    $pages = staylodgic_get_template_pages();
    foreach ($pages as $page) {
        if ($page['template'] === $template) {
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
            'title' => 'Book Room',
            'slug' => 'book-room',
            'template' => 'template-bookroom.php',
            'content' => '[hotel_booking_search]'
        ),
        array(
            'title' => 'Book Activity',
            'slug' => 'book-activity',
            'template' => 'template-bookactivity.php',
            'content' => '[activity_booking_search]'
        ),
        array(
            'title' => 'Booking Details',
            'slug' => 'booking-details',
            'template' => 'template-bookingdetails.php',
            'content' => '[hotel_booking_details]'
        ),
        array(
            'title' => 'Guest Registration',
            'slug' => 'guest-registration',
            'template' => 'template-guestregistration.php',
            'content' => '[guest_registration]'
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
    // Generate a random RGB color
    $red = mt_rand(0, 255);
    $green = mt_rand(0, 255);
    $blue = mt_rand(0, 255);

    // Convert RGB to hex
    $hex = sprintf("#%02x%02x%02x", $red, $green, $blue);

    return $hex;
}

/**
 * Method staylodgic_hex_to_rgb
 *
 * @param $hex $hex [explicite description]
 *
 * @return void
 */
function staylodgic_hex_to_rgb($hex) {
    // Remove '#' if present
    $hex = str_replace('#', '', $hex);

    // Check if the input is a valid hex color
    if (!preg_match('/^[a-f0-9]{6}$/i', $hex)) {
        // If not valid, generate a random hex color
        $hex = staylodgic_random_color_hex();
    }

    // Split into R, G, B substrings
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Return RGB values as an array
    return array('r' => $r, 'g' => $g, 'b' => $b);
}

/**
 * Method staylodgic_applyTimezoneToDateAndTime
 *
 * @param $date $date [explicite description]
 * @param $time $time [explicite description]
 * @param $timezone $timezone [explicite description]
 *
 * @return void
 */
function staylodgic_applyTimezoneToDateAndTime($date, $time, $timezone) {
    try {
        // Parse the timezone offset
        $offsetPattern = '/GMT([+-])(\d{1,2}):(\d{2})/';
        if (!preg_match($offsetPattern, $timezone, $matches)) {
            throw new Exception("Invalid timezone format");
        }

        $sign = $matches[1];
        $hours = (int)$matches[2];
        $minutes = (int)$matches[3];
        $offsetInSeconds = ($hours * 3600 + $minutes * 60) * ($sign === '+' ? 1 : -1);

        // Combine date and time and create DateTime object
        $dateTime = new DateTime($date . ' ' . $time);

        // Apply the offset
        $dateTime->modify($offsetInSeconds . ' seconds');

        // Return the adjusted date and time
        return [
            'adjustedDate' => $dateTime->format('Y-m-d'),
            'adjustedTime' => $dateTime->format('H:i:s')
        ];
    } catch (Exception $e) {
        // Handle exceptions or invalid input
        return "Error: " . $e->getMessage();
    }
}

/**
 * Method staylodgic_get_GmtTimezoneChoices
 *
 * @return void
 */
function staylodgic_get_GmtTimezoneChoices() {
    $timezones = [];

    // Start from GMT-12:00 to GMT+14:00
    for ($i = -12; $i <= 14; $i++) {
        $timezone = $i < 0 ? "GMT$i:00" : ($i > 0 ? "GMT+$i:00" : "GMT+00:00");
        $timezones[$timezone] = esc_html__($timezone, 'staylodgic');
    }

    // Add half-hour and 45-minute offsets if needed
    // Example: $timezones['gmt+5:30'] = esc_html__('GMT+5:30', 'staylodgic');

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

	$page_list = array('none' => 'Choose a page');

// Array of allowed templates

    $page_list['template-bookroom.php'] = 'Book Room';
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

	$page_list = array('none' => 'Choose a page');

// Array of allowed templates

    $page_list['template-bookroom.php'] = 'Book Room';
    $page_list['template-bookactivity.php'] = 'Book Activity';
    $page_list['template-guestregistration.php'] = 'Guest Registration';
    $page_list['template-bookingdetails.php'] = 'Booking Details';

    return $page_list;
}
/**
 * Method staylodgic_get_pages_for_select
 *
 * @return void
 */
function staylodgic_get_pages_for_select() {
    // Get an array of all pages
    $pages = get_pages(); 
    $page_list = array();

	$page_list = array('none' => 'Choose a page');

    // Loop through the pages and add them to the list
    foreach ($pages as $page) {
        $page_list[$page->ID] = $page->post_title;
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
function staylodgic_is_valid_sync_interval($qtysync_interval) {
    // Retrieve the array of sync intervals
    $sync_intervals = staylodgic_sync_intervals();

    // Check if the provided interval is a key in the sync intervals array
    return array_key_exists($qtysync_interval, $sync_intervals);
}

/**
 * Method staylodgic_sync_intervals
 *
 * @return void
 */
function staylodgic_sync_intervals() {
	$sync_intervals = array(
		'1' => esc_attr__('One Minute', 'staylodgic'),
		'5' => esc_attr__('Five Minutes', 'staylodgic'),
		'10' => esc_attr__('Ten Minutes', 'staylodgic'),
		'15' => esc_attr__('Fifteen Minutes', 'staylodgic'),
		'30' => esc_attr__('Thirty Minutes', 'staylodgic'),
		'60' => esc_attr__('Sixty Minutes', 'staylodgic')
	);

	return $sync_intervals;
}
/**
 * Method staylodgic_formatDate
 *
 * @param $stay_date_string $stay_date_string [explicite description]
 * @param $formatChoice $formatChoice [explicite description]
 *
 * @return void
 */
function staylodgic_formatDate($stay_date_string, $formatChoice = 'monthshort_first')
{
	$formattedDate = '';
	$dateTime = new DateTime($stay_date_string);

	switch ($formatChoice) {
		case 'default':
			$formattedDate = $dateTime->format('Y-m-d');
			break;
		case 'short':
			$formattedDate = $dateTime->format('M d, Y');
			break;
		case 'long':
			$formattedDate = $dateTime->format('F d, Y');
			break;
		case 'monthshort_first':
			$formattedDate = $dateTime->format('M jS, Y');
			break;
		case 'monthshort_after':
			$formattedDate = $dateTime->format('jS M, Y');
			break;
		// Add more format choices as needed
		default:
			$formattedDate = $dateTime->format('Y-m-d');
			break;
	}

	return $formattedDate;
}
/**
 * Method staylodgic_get_booking_substatuses
 *
 * @return void
 */
function staylodgic_get_booking_substatuses()
{
    $bookingSubStatuses = array(
        'completed'      => esc_attr__('Completed', 'staylodgic'),
        'checkedin'      => esc_attr__('Checked-In', 'staylodgic'),
        'checkedout'     => esc_attr__('Checked-Out', 'staylodgic'),
        'noshow'         => esc_attr__('No Show', 'staylodgic'),
        'onhold'         => esc_attr__('On Hold', 'staylodgic'),
        'pendingpayment' => esc_attr__('Pending Payment', 'staylodgic'),
        'refunded'       => esc_attr__('Refunded', 'staylodgic'),
        'inprogress'     => esc_attr__('In Progress', 'staylodgic'),
        'expired'        => esc_attr__('Expired', 'staylodgic'),
    );

    return $bookingSubStatuses;
}
/**
 * Method staylodgic_get_booking_statuses
 *
 * @return void
 */
function staylodgic_get_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'staylodgic'),
        'cancelled' => esc_attr__('Cancelled', 'staylodgic'),
        'pending'   => esc_attr__('Pending', 'staylodgic'),
    );

    return $bookingStatuses;
}
/**
 * Method staylodgic_get_new_booking_statuses
 *
 * @return void
 */
function staylodgic_get_new_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'staylodgic'),
        'pending'   => esc_attr__('Pending', 'staylodgic'),
    );

    return $bookingStatuses;
}
/**
 * Method staylodgic_get_AllBedLayouts
 *
 * @param $bedNames $bedNames [explicite description]
 *
 * @return void
 */
function staylodgic_get_AllBedLayouts($bedNames)
{
	$html           = '';
	$bedNames_array = explode(' ', $bedNames);
	foreach ($bedNames_array as $key => $bedName) {
		$html .= staylodgic_get_BedLayout($bedName, $key);
	}

	return $html;
}
/**
 * Method staylodgic_get_BedLayout
 *
 * @param $bedName $bedName [explicite description]
 * @param $bedFieldID $bedFieldID [explicite description]
 *
 * @return void
 */
function staylodgic_get_BedLayout($bedName, $bedFieldID = null)
{

    switch ($bedName) {
        case 'fullbed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'queenbed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'kingbed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'sofabed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'bunkbed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'twinbed':
            $html = '<div class="guest-bed type-twinbed-twinbed-' . $bedFieldID . ' guest-bed-' . $bedName . '"></div>';
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
function staylodgic_format_value($value)
{
    $formatted_elements = '';
    if (is_array($value)) {
        $formatted_start = '<ul>';
        foreach ($value as $key => $item) {
            $formatted_elements .= '<li><strong>' . $key . ':</strong> ' . staylodgic_format_value($item) . '</li>';
        }
        $formatted_end = '</ul>';
        if ('' == $formatted_elements) {
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
 * @param $originalDate $originalDate [explicite description]
 *
 * @return void
 */
function staylodgic_readable_date($originalDate)
{
    $formattedDate = date("F jS, Y", strtotime($originalDate));

    return $formattedDate;
}

/**
 * Method staylodgic_get_option
 *
 * @param $option $option [explicite description]
 * @param $default $default [explicite description]
 *
 * @return void
 */
function staylodgic_get_option($option, $default = '') {
    $settings = get_option('staylodgic_settings');

    if (is_array($settings) && isset($settings[$option])) {
		//error_log( print_r($settings, true) );
        return $settings[$option];
    }

    return $default;
}

/**
 * Method staylodgic_price
 *
 * @param $originalPrice $originalPrice [explicite description]
 *
 * @return void
 */
function staylodgic_price($originalPrice)
{
    $currency           = staylodgic_get_option('currency', 'USD');
    $currency_position  = staylodgic_get_option('currency_position', 'left');
    $thousand_seperator = staylodgic_get_option('thousand_seperator', ',');
    $decimal_seperator  = staylodgic_get_option('decimal_seperator', '.');
    $number_of_decimals = staylodgic_get_option('number_of_decimals', '2');

    // Format the price using number_format
    $price = number_format($originalPrice, $number_of_decimals, $decimal_seperator, $thousand_seperator);
	
    if ('' == $price) {
        $price = $originalPrice;
    }

    $formatted_price = '<span class="formatted-price" date-price="' . esc_attr($price) . '" date-currency="' . esc_attr($currency) . '">';
    // Adjust the position of the currency symbol
    if ($currency_position === 'left') {
        $formatted_price .= '<span class="currency">' . $currency . '</span>' . ' ' . '<span class="price">' . $price . '</span>';
    } else {
        $formatted_price .= '<span class="price">' . $price . '</span>' . ' ' . '<span class="currency">' . $currency . '</span>';
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
function staylodgic_reverse_percentage($total, $percentages)
{
    $initial_value = $total;
    foreach ($percentages as $percentage) {
        $initial_value = $initial_value / (1 + ($percentage / 100));
    }
    return $initial_value;
}

/**
 * Method staylodgic_has_tax
 *
 * @return void
 */
function staylodgic_has_tax()
{

    $taxFlag = staylodgic_get_option('enable_taxes');
    return $taxFlag;

}
/**
 * Method staylodgic_has_activity_tax
 *
 * @return void
 */
function staylodgic_has_activity_tax()
{

    $taxFlag = staylodgic_get_option('enable_activitytaxes');
    return $taxFlag;

}

/**
 * Method staylodgic_get_mealplan_labels
 *
 * @param $mealtype $mealtype [explicite description]
 *
 * @return void
 */
function staylodgic_get_mealplan_labels($mealtype)
{
    switch ($mealtype) {
        case 'BB':
            return __('Breakfast', 'staylodgic');
        case 'HB':
            return __('Halfboard', 'staylodgic');
        case 'FB':
            return __('Fullboard', 'staylodgic');
        case 'AN':
            return __('All inclusive', 'staylodgic');
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
function staylodgic_delete_booking_transient($stay_booking_number)
{
    delete_transient($stay_booking_number);
}
/**
 * Method staylodgic_set_booking_transient
 *
 * @param $data $data [explicite description]
 * @param $stay_booking_number $stay_booking_number [explicite description]
 *
 * @return void
 */
function staylodgic_set_booking_transient($data, $stay_booking_number)
{
	
    set_transient($stay_booking_number, $data, 20 * MINUTE_IN_SECONDS);
}
/**
 * Method staylodgic_get_booking_transient
 *
 * @param $stay_booking_number $stay_booking_number [explicite description]
 *
 * @return void
 */
function staylodgic_get_booking_transient($stay_booking_number = null)
{
    return get_transient($stay_booking_number);
}
/**
 * Method staylodgic_get_allowed_tags
 *
 * @return void
 */
function staylodgic_get_allowed_tags() {
	$structure_allowed_tags = array(
        'caption'  => array(),
		'col'      => array(
				'span'    => true,
		),
		'colgroup'      => array(
				'span'    => true,
		),
		'table'    => array(),
		'tbody'    => array(),
		'td'       => array(
            'class'  => true,
			'colspan' => true,
			'headers' => true,
			'rowspan' => true,
		),
		'tfoot'    => array(),
		'th'       => array(
				'abbr'    => true,
				'colspan' => true,
				'headers' => true,
				'rowspan' => true,
				'scope'   => true,
		),
		'thead'    => array(),
		'tr'       => array(),
		'a'          => array(
			'id'     => true,
			'class'  => true,
			'href'   => true,
			'rel'    => true,
			'rev'    => true,
			'name'   => true,
			'target' => true,
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
			'id'         => true,
			'style'      => true,
			'class'      => true,
			'data-color' => true,
			'align'      => true,
			'dir'        => true,
			'lang'       => true,
			'xml:lang'   => true,
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
			'class' => true,
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
			'dir'      => true,
			'id'       => true,
			'class'    => true,
			'align'    => true,
			'lang'     => true,
			'xml:lang' => true,
		),
		'small'      => array(),
		'strike'     => array(),
		'strong'     => array(),
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
	);

	return $structure_allowed_tags;
}