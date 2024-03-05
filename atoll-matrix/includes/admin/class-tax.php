<?php
namespace AtollMatrix;

class Tax
{

    private $tax_type;

    public function __construct($tax_type = 'room')
    {
        $this->tax_type = $tax_type;

        add_action('wp_ajax_generateTax', array($this, 'generateTax'));
        add_action('wp_ajax_nopriv_generateTax', array($this, 'generateTax'));

        add_action('wp_ajax_excludeTax', array($this, 'excludeTax'));
        add_action('wp_ajax_nopriv_excludeTax', array($this, 'excludeTax'));

    }

    public function excludeTax()
    {
        $response = array();

        $the_post_id = sanitize_text_field($_POST[ 'post_id' ]);
        $subtotal    = sanitize_text_field($_POST[ 'subtotal' ]);

        // Verify the nonce
        if (!isset($_POST[ 'nonce' ])) {
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
        update_post_meta($the_post_id, 'atollmatrix_tax', 'excluded');
        delete_post_meta($the_post_id, 'atollmatrix_tax_html_data');
        delete_post_meta($the_post_id, 'atollmatrix_tax_data');
        update_post_meta($the_post_id, 'atollmatrix_reservation_total_room_cost', $subtotal);

        // Send the JSON response
        wp_send_json('Tax Exluded');

    }

    public function generateTax()
    {
        // Initialize the response array
        $response = array();

        // Check if the necessary POST data is set
        if (isset($_POST[ 'subtotal' ], $_POST[ 'staynights' ], $_POST[ 'total_guests' ])) {
            // Sanitize and retrieve the input data
            $subtotal    = sanitize_text_field($_POST[ 'subtotal' ]);
            $staynights  = sanitize_text_field($_POST[ 'staynights' ]);
            $totalGuests = sanitize_text_field($_POST[ 'total_guests' ]);
            $the_post_id = sanitize_text_field($_POST[ 'post_id' ]);

            $tax_type = 'room';
            if ( isset( $_POST[ 'tax_type' ]) ) {
                $tax_type = sanitize_text_field($_POST[ 'tax_type' ]);
            }

            // Verify the nonce
            if (!isset($_POST[ 'nonce' ])) {
                wp_send_json_error([ 'message' => 'Failed' ]);
                return;
            }

            // Calculate the total price
            if ( 'activities' == $tax_type ) {
                $tax_instance = new \AtollMatrix\Tax('activities');
            } else {
                $tax_instance = new \AtollMatrix\Tax('room');
            }
            $tax_data     = $tax_instance->apply_tax($subtotal, $staynights, $totalGuests, $output = 'data');
            $tax          = $tax_instance->apply_tax($subtotal, $staynights, $totalGuests, $output = 'html');

            if ($tax) {

                $html = $tax_instance->tax_summary($tax[ 'details' ]);

                $response[ 'html' ]  = $html;
                $response[ 'total' ] = $tax[ 'total' ];

                // Add the response data as post meta
                update_post_meta($the_post_id, 'atollmatrix_tax', 'enabled');
                update_post_meta($the_post_id, 'atollmatrix_tax_html_data', $html);
                update_post_meta($the_post_id, 'atollmatrix_tax_data', $tax_data);
                update_post_meta($the_post_id, 'atollmatrix_reservation_total_room_cost', $tax[ 'total' ]);

            } else {
                $response[ 'error' ] = 'Calculation error';
            }
        } else {
            $response[ 'error' ] = 'Missing input data';
        }

        // Send the JSON response
        wp_send_json($response);
    }

    public function tax_summary($tax)
    {
        $html = '<div class="input-tax-summary-wrap-inner">';
        foreach ($tax as $totalID => $totalvalue) {
            $html .= '<div class="tax-summary tax-summary-details">' . $totalvalue . '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    public function apply_tax($roomrate, $nights, $guests, $output)
    {

        $price = array();
        $count = 0;

        if ('room' == $this->tax_type) {
            $taxPricing = atollmatrix_get_option('taxes');
        }
        if ('activities' == $this->tax_type) {
            $taxPricing = atollmatrix_get_option('activity_taxes');
        }

        $subtotal = $roomrate;

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

}

$instance = new \AtollMatrix\Tax();
