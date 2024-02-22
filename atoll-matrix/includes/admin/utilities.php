<?php
function atollmatrix_applyTimezoneToDateAndTime($date, $time, $timezone) {
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

function atollmatrix_get_GmtTimezoneChoices() {
    $timezones = [];

    // Start from GMT-12:00 to GMT+14:00
    for ($i = -12; $i <= 14; $i++) {
        $timezone = $i < 0 ? "GMT$i:00" : ($i > 0 ? "GMT+$i:00" : "GMT+00:00");
        $timezones[$timezone] = esc_html__($timezone, 'atollmatrix');
    }

    // Add half-hour and 45-minute offsets if needed
    // Example: $timezones['gmt+5:30'] = esc_html__('GMT+5:30', 'atollmatrix');

    return $timezones;
}
function atollmatrix_get_pages_for_select() {
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

function atollmatrix_is_valid_sync_interval($qtysync_interval) {
    // Retrieve the array of sync intervals
    $sync_intervals = atollmatrix_sync_intervals();

    // Check if the provided interval is a key in the sync intervals array
    return array_key_exists($qtysync_interval, $sync_intervals);
}

function atollmatrix_sync_intervals() {
	$sync_intervals = array(
		'5' => esc_attr__('Five Minutes', 'atollmatrix'),
		'10' => esc_attr__('Ten Minutes', 'atollmatrix'),
		'15' => esc_attr__('Fifteen Minutes', 'atollmatrix'),
		'30' => esc_attr__('Thirty Minutes', 'atollmatrix'),
		'60' => esc_attr__('Sixty Minutes', 'atollmatrix')
	);

	return $sync_intervals;
}
function atollmatrix_formatDate($dateString, $formatChoice = 'monthshort_first')
{
	$formattedDate = '';
	$dateTime = new DateTime($dateString);

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
function atollmatrix_get_booking_substatuses()
{
    $bookingSubStatuses = array(
        'completed'      => esc_attr__('Completed', 'atollmatrix'),
        'checkedin'      => esc_attr__('Checked-In', 'atollmatrix'),
        'checkedout'     => esc_attr__('Checked-Out', 'atollmatrix'),
        'noshow'         => esc_attr__('No Show', 'atollmatrix'),
        'onhold'         => esc_attr__('On Hold', 'atollmatrix'),
        'pendingpayment' => esc_attr__('Pending Payment', 'atollmatrix'),
        'refunded'       => esc_attr__('Refunded', 'atollmatrix'),
        'inprogress'     => esc_attr__('In Progress', 'atollmatrix'),
        'expired'        => esc_attr__('Expired', 'atollmatrix'),
    );

    return $bookingSubStatuses;
}
function atollmatrix_get_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'atollmatrix'),
        'cancelled' => esc_attr__('Cancelled', 'atollmatrix'),
        'pending'   => esc_attr__('Pending', 'atollmatrix'),
    );

    return $bookingStatuses;
}
function atollmatrix_get_new_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'atollmatrix'),
        'pending'   => esc_attr__('Pending', 'atollmatrix'),
    );

    return $bookingStatuses;
}
function atollmatrix_get_BedLayout($bedName, $bedFieldID = null)
{

    switch ($bedName) {
        case 'kingbed':
            $html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
            break;
        case 'twinbed':
            $html = '<div class="guest-bed type-twinbed-twinbed-' . $bedFieldID . ' guest-bed-' . $bedName . '"></div>';
            break;
    }

    return $html;
}
// Function to recursively format arrays as strings
function atollmatrix_format_value($value)
{
    $formatted_elements = '';
    if (is_array($value)) {
        $formatted_start = '<ul>';
        foreach ($value as $key => $item) {
            $formatted_elements .= '<li><strong>' . $key . ':</strong> ' . atollmatrix_format_value($item) . '</li>';
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
function atollmatrix_readable_date($originalDate)
{
    $formattedDate = date("F jS, Y", strtotime($originalDate));

    return $formattedDate;
}

function atollmatrix_get_option($option, $default = '') {
    $settings = get_option('atollmatrix_settings');

    if (is_array($settings) && isset($settings[$option])) {
		//error_log( print_r($settings, true) );
        return $settings[$option];
    }

    return $default;
}

function atollmatrix_price($originalPrice)
{
    $currency           = atollmatrix_get_option('currency', 'USD');
    $currency_position  = atollmatrix_get_option('currency_position', 'left');
    $thousand_seperator = atollmatrix_get_option('thousand_seperator', ',');
    $decimal_seperator  = atollmatrix_get_option('decimal_seperator', '.');
    $number_of_decimals = atollmatrix_get_option('number_of_decimals', '2');

	error_log( '---------------------- NUMBER FORMAT-------------------------');
	error_log( $originalPrice );
	error_log ( $number_of_decimals );
	error_log ( $decimal_seperator );
	error_log ( $thousand_seperator );
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
// function to reverse find the initial value from the total amount which has been derived with multiple percentages as an array, added to the initial value
// Example usage
// $total = 53.00;
// $percentages = [10.00, 16.00];

// $initialValue = atollmatrix_reverse_percentage($total, $percentages);

// echo "The initial value is: $" . number_format($initialValue, 2);
// The initial value is: $37.30
function atollmatrix_reverse_percentage($total, $percentages)
{
    $initial_value = $total;
    foreach ($percentages as $percentage) {
        $initial_value = $initial_value / (1 + ($percentage / 100));
    }
    return $initial_value;
}

function atollmatrix_has_tax()
{

    $taxFlag = atollmatrix_get_option('enable_taxes');
    return $taxFlag;

}

function atollmatrix_display_cancelled()
{

    $display_cancelled = atollmatrix_get_option('display_cancelled');
    return $display_cancelled;

}

function atollmatrix_get_mealplan_labels($mealtype)
{
    switch ($mealtype) {
        case 'BB':
            return __('Breakfast', 'atollmatrix');
        case 'HB':
            return __('Halfboard', 'atollmatrix');
        case 'FB':
            return __('Fullboard', 'atollmatrix');
        case 'AN':
            return __('All inclusive', 'atollmatrix');
        default:
            return '';
    }
}

function atollmatrix_delete_booking_transient($bookingNumber)
{
    delete_transient($bookingNumber);
}
function atollmatrix_set_booking_transient($data, $bookingNumber)
{
    error_log('----- Saving Transisent -----');
    error_log($bookingNumber);
    error_log(print_r($data, true));
    set_transient($bookingNumber, $data, 20 * MINUTE_IN_SECONDS);
}
function atollmatrix_get_booking_transient($bookingNumber = null)
{
    return get_transient($bookingNumber);
}
function atollmatrix_get_allowed_tags() {
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