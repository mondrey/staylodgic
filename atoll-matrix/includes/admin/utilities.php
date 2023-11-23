<?php
function get_booking_substatuses()
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
function get_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'atollmatrix'),
        'cancelled' => esc_attr__('Cancelled', 'atollmatrix'),
        'pending'   => esc_attr__('Pending', 'atollmatrix'),
    );

    return $bookingStatuses;
}
function get_new_booking_statuses()
{
    $bookingStatuses = array(
        'confirmed' => esc_attr__('Confirmed', 'atollmatrix'),
        'pending'   => esc_attr__('Pending', 'atollmatrix'),
    );

    return $bookingStatuses;
}
function get_BedLayout($bedName, $bedFieldID = null)
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

function atollmatrix_get_option($option, $default = '')
{
    $got_value = get_option('atollmatrix_settings')[ $option ] ?? $default;

    return $got_value;
}
function atollmatrix_price($originalPrice)
{
    $currency           = atollmatrix_get_option('currency');
    $currency_position  = atollmatrix_get_option('currency_position');
    $thousand_seperator = atollmatrix_get_option('thousand_seperator');
    $decimal_seperator  = atollmatrix_get_option('decimal_seperator');
    $number_of_decimals = atollmatrix_get_option('number_of_decimals');

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

function atollmatrix_generate_tax_summary($tax)
{
    $html = '<div class="input-tax-summary-wrap-inner">';
    foreach ($tax as $totalID => $totalvalue) {
        $html .= '<div class="tax-summary tax-summary-details">' . $totalvalue . '</div>';
    }
    $html .= '</div>';

    return $html;
}

function atollmatrix_apply_tax($roomrate, $nights, $guests, $output)
{

    $price      = array();
    $count      = 0;
    $taxPricing = atollmatrix_get_option('taxes');
    $subtotal   = $roomrate;

    if (atollmatrix_has_tax()) {
        foreach ($taxPricing as $tax) {
            $percentage = '';
            if ($tax[ 'type' ] === 'percentage') {
                $percentage = $tax[ 'number' ] . '%';
                if ($tax[ 'duration' ] === 'inrate') {
                    // Decrease the rate by the given percentage
                    $total = $roomrate * ($tax[ 'number' ] / 100);
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perperson') {
                    // Increase the rate by the fixed amount
                    $total = $guests * ($roomrate * $tax[ 'number' ] / 100);
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perday') {
                    // Increase the rate by the given percentage
                    $total = $nights * ($roomrate * $tax[ 'number' ] / 100);
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perpersonperday') {
                    // Increase the rate by the given percentage
                    $total = $nights * ($guests * ($roomrate * $tax[ 'number' ] / 100));
                    $roomrate += $total;
                }
            }
            if ($tax[ 'type' ] === 'fixed') {
                if ($tax[ 'duration' ] === 'inrate') {
                    // Decrease the rate by the given percentage
                    $total = $tax[ 'number' ];
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perperson') {
                    // Increase the rate by the fixed amount
                    $total = $guests * $tax[ 'number' ];
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perday') {
                    // Increase the rate by the given percentage
                    $total = $nights * $tax[ 'number' ];
                    $roomrate += $total;
                } elseif ($tax[ 'duration' ] === 'perpersonperday') {
                    // Increase the rate by the given percentage
                    $total = $nights * ($guests * $tax[ 'number' ]);
                    $roomrate += $total;
                }
            }
            if ('html' == $output) {
                $price[ 'details' ][ $count ] = '<span class="tax-value">' . atollmatrix_price($total) . '</span> - <span class="tax-label" data-number="' . $tax[ 'number' ] . '" data-type="' . $tax[ 'type' ] . '" data-duration="' . $tax[ 'duration' ] . '">' . ltrim($percentage . ' ' . $tax[ 'label' ]) . '</span>';
            } else {
                $price[ 'details' ][ $count ][ 'label' ] = ltrim($percentage . ' ' . $tax[ 'label' ]);
                $price[ 'details' ][ $count ][ 'total' ] = $total;
            }
            $count++;
        }
    }

    $price[ 'subtotal' ] = $subtotal;
    $price[ 'total' ]    = $roomrate;

    if ('single-value' == $output) {
        $price = $roomrate;
    }

    return $price;
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
    if ($bookingNumber === null) {
        // Use $this->bookingNumber if $bookingNumber is not supplied
        $bookingNumber = $this->bookingNumber;
    }

    return get_transient($bookingNumber);
}
