<?php
function atollmatrix_readableDate( $originalDate ) {
    $formattedDate = date("F jS, Y", strtotime($originalDate));

    return $formattedDate;
}

function atollmatrix_get_option($option, $default = '')
{
    $got_value = get_option('atollmatrix_settings')[$option] ?? $default;

    return $got_value;
}
function atollmatrix_price($price)
{
    $currency           = atollmatrix_get_option('currency');
    $currency_position  = atollmatrix_get_option('currency_position');
    $thousand_seperator = atollmatrix_get_option('thousand_seperator');
    $decimal_seperator  = atollmatrix_get_option('decimal_seperator');
    $number_of_decimals = atollmatrix_get_option('number_of_decimals');

    // Format the price using number_format
    $price = number_format($price, $number_of_decimals, $decimal_seperator, $thousand_seperator);

    $formatted_price = '<span class="formatted-price" date-price="'.esc_attr($price).'" date-currency="'.esc_attr($currency).'">';
    // Adjust the position of the currency symbol
    if ($currency_position === 'left') {
        $formatted_price .= '<span class="currency">'. $currency . '</span>' . ' ' . '<span class="price">'.$price. '</span>';
    } else {
        $formatted_price .= '<span class="price">'.$price. '</span>' . ' ' . '<span class="currency">'. $currency . '</span>';
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