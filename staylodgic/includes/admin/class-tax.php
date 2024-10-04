<?php
namespace Staylodgic;

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
    
    /**
     * Method excludeTax
     *
     * @return void
     */
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
        update_post_meta($the_post_id, 'staylodgic_tax', 'excluded');
        delete_post_meta($the_post_id, 'staylodgic_tax_html_data');
        delete_post_meta($the_post_id, 'staylodgic_tax_data');
        update_post_meta($the_post_id, 'staylodgic_reservation_total_room_cost', $subtotal);

        // Send the JSON response
        wp_send_json('Tax Exluded');

    }
    
    /**
     * Method generateTax
     *
     * @return void
     */
    public function generateTax()
    {
        // Initialize the response array
        $response = array();

        // Check if the necessary POST data is set
        if (isset($_POST[ 'subtotal' ], $_POST[ 'staynights' ], $_POST[ 'total_guests' ])) {
            // Sanitize and retrieve the input data
            $subtotal    = sanitize_text_field($_POST[ 'subtotal' ]);
            $staynights  = sanitize_text_field($_POST[ 'staynights' ]);
            $stay_total_guests = sanitize_text_field($_POST[ 'total_guests' ]);
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
                $tax_instance = new \Staylodgic\Tax('activities');
            } else {
                $tax_instance = new \Staylodgic\Tax('room');
            }
            $tax_data     = $tax_instance->apply_tax($subtotal, $staynights, $stay_total_guests, $output = 'data');
            $tax          = $tax_instance->apply_tax($subtotal, $staynights, $stay_total_guests, $output = 'html');

            if ($tax) {

                $html = $tax_instance->tax_summary($tax[ 'details' ]);

                $response[ 'html' ]  = $html;
                $response[ 'total' ] = $tax[ 'total' ];

                // Add the response data as post meta
                update_post_meta($the_post_id, 'staylodgic_tax', 'enabled');
                update_post_meta($the_post_id, 'staylodgic_tax_html_data', $html);
                update_post_meta($the_post_id, 'staylodgic_tax_data', $tax_data);
                update_post_meta($the_post_id, 'staylodgic_reservation_total_room_cost', $tax[ 'total' ]);

            } else {
                $response[ 'error' ] = 'Calculation error';
            }
        } else {
            $response[ 'error' ] = 'Missing input data';
        }

        // Send the JSON response
        wp_send_json($response);
    }
    
    /**
     * Method tax_summary
     *
     * @param $tax $tax [explicite description]
     *
     * @return void
     */
    public function tax_summary($tax)
    {
        $html = '<div class="input-tax-summary-wrap-inner">';
        foreach ($tax as $total_id => $totalvalue) {
            $html .= '<div class="tax-summary tax-summary-details">' . $totalvalue . '</div>';
        }
        $html .= '</div>';

        return $html;
    }
    
    /**
     * Method apply_tax
     *
     * @param $roomrate $roomrate [explicite description]
     * @param $nights $nights [explicite description]
     * @param $guests $guests [explicite description]
     * @param $output $output [explicite description]
     *
     * @return void
     */
    public function apply_tax($roomrate, $nights, $guests, $output)
    {

        $price = array();
        $count = 0;

        $tax_has_status = false;
        if ('room' == $this->tax_type) {
            $taxPricing = staylodgic_get_option('taxes');

            $tax_has_status = staylodgic_has_tax();
        }
        if ('activities' == $this->tax_type) {
            $taxPricing = staylodgic_get_option('activity_taxes');

            $tax_has_status = staylodgic_has_activity_tax();
        }

        $subtotal = $roomrate;

        if ( $tax_has_status ) {
            foreach ($taxPricing as $tax) {
                $percentage = '';
                if ($tax[ 'type' ] === 'percentage') {
                    $percentage = $tax[ 'number' ] . '%';
                    if ($tax[ 'duration' ] === 'inrate') {
                        // Decrease the rate by the given percentage
                        $total = $subtotal * ($tax[ 'number' ] / 100);
                        $roomrate += $total;
                    } elseif ($tax[ 'duration' ] === 'perperson') {
                        // Increase the rate by the fixed amount
                        $total = $guests * ($subtotal * $tax[ 'number' ] / 100);
                        $roomrate += $total;
                    } elseif ($tax[ 'duration' ] === 'perday') {
                        // Increase the rate by the given percentage
                        $total = $nights * ($subtotal * $tax[ 'number' ] / 100);
                        $roomrate += $total;
                    } elseif ($tax[ 'duration' ] === 'perpersonperday') {
                        // Increase the rate by the given percentage
                        $total = $nights * ($guests * ($subtotal * $tax[ 'number' ] / 100));
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
                    $price[ 'details' ][ $count ] = '<span class="tax-value">' . staylodgic_price($total) . '</span> - <span class="tax-label" data-number="' . $tax[ 'number' ] . '" data-type="' . $tax[ 'type' ] . '" data-duration="' . $tax[ 'duration' ] . '">' . ltrim($percentage . ' ' . $tax[ 'label' ]) . '</span>';
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

$instance = new \Staylodgic\Tax();
