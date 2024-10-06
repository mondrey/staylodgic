<?php

namespace Staylodgic;

class Payments {


	public function __construct() {
		// AJAX handler to save room metadata
		add_action( 'wp_ajax_processReservationPayment', array( $this, 'process_reservation_payment' ) );
		add_action( 'wp_ajax_nopriv_processReservationPayment', array( $this, 'process_reservation_payment' ) );

		// Add the booking number to the checkout page
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'add_booking_number_to_checkout' ) );
		add_action( 'woocommerce_before_thankyou', array( $this, 'add_booking_number_to_checkout' ) );

		add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_booking_after_checkout' ), 10, 3 );

		add_action( 'woocommerce_payment_failed', array( $this, 'handle_payment_failure' ), 10, 2 );

		// Add custom column to WooCommerce Order list table
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_booking_number_column' ) );

		// Display custom column value in WooCommerce Order list table
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'display_booking_number_column_value' ), 10, 2 );

		add_action( 'wp_ajax_get_room_names', array( $this, 'get_room_names_callback' ) );
		add_action( 'wp_ajax_nopriv_get_room_names', array( $this, 'get_room_names_callback' ) );

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'modify_cart_item_prices' ) );
	}

	/**
	 * Method modify_cart_item_prices
	 *
	 * @param $cart $cart [explicite description]
	 *
	 * @return void
	 */
	public function modify_cart_item_prices( $cart ) {
		foreach ( $cart->get_cart() as $cart_item ) {
			// Get the WooCommerce session
			$session = WC()->session;

			// Get the total price from the session
			$total_price = $session->get( 'total_price' );
			$cart_item['data']->set_price( $total_price );
		}
	}

	/**
	 * Create a unique payment request link for a pre-created product with a booking number in WooCommerce.
	 *
	 * @param int $product_id The ID of the pre-created product to be added to the cart.
	 * @param float $booked_price The price for the booked room.
	 * @param int $booking_number The booking number associated with the product.
	 * @return string|bool The unique payment request link if successful, false otherwise.
	 */
	public function create_payment_request( $product_id, $booked_price, $booking_number ) {
		// Ensure WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		// Create a unique order key
		$order_key = uniqid();

		// Generate the payment request link with the order key
		$payment_request_link = wc_get_checkout_url() . '?key=' . $order_key;

		// Create a new order with the unique order key
		$order = wc_create_order( array( 'order_key' => $order_key ) );

		// Add the product to the order
		$product = wc_get_product( $product_id );
		$order->add_product( $product, 1 );

		// Set the booking number as order meta data
		$order->update_meta_data( 'booking_number', $booking_number );

		// Save the order
		$order->save();

		return $payment_request_link;
	}

	/**
	 * Method Ajax callback function to retrieve room names
	 *
	 * @return void
	 */
	public function get_room_names_callback() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		// Check if the booking number is provided in the Ajax request
		if ( isset( $_POST['booking_number'] ) ) {
			$booking_number = $_POST['booking_number'];

			// Query the reservations to get the room names for the selected booking number
			$reservation_args = array(
				'post_type'  => 'slgc_reservations',
				'meta_query' => array(
					array(
						'key'     => 'staylodgic_booking_number',
						'value'   => $booking_number,
						'compare' => '=',
					),
				),
			);

			$reservations = get_posts( $reservation_args );

			// Create an ordered list
			$room_list = '<ol>';

			foreach ( $reservations as $reservation ) {
				// Get the room name associated with the reservation
				$room_name = get_the_title( get_post_meta( $reservation->ID, 'staylodgic_room_id', true ) );

				// Get the reservation edit link
				$reservation_edit_link = get_edit_post_link( $reservation->ID );

				// Create the room name with the reservation edit link as a list item
				$room_list .= '<li><a href="' . $reservation_edit_link . '">' . $room_name . '</a></li>';
			}

			// Close the ordered list
			$room_list .= '</ol>';

			$product_id   = '476';
			$booked_price = '731';

			$payment_request = self::create_payment_request( $product_id, $booked_price, $booking_number );

			// Return the room list as a response to the Ajax request
			echo $room_list . $payment_request;
		}

		// Always use wp_die() at the end of Ajax callback functions
		wp_die();
	}

	/**
	 * Method add_booking_number_column
	 *
	 * @param $columns $columns [explicite description]
	 *
	 * @return void
	 */
	public function add_booking_number_column( $columns ) {
		// Add the custom column after the order total column
		$columns['booking_number'] = 'Booking Details:';
		return $columns;
	}

	public function display_booking_number_column_value( $column, $post_id ) {
		if ( 'booking_number' === $column ) {
			// Get the booking number from the order meta data
			$booking_number = get_post_meta( $post_id, 'staylodgic_booking_number', true );

			if ( $booking_number ) {
				$args = array(
					'post_type'      => 'slgc_reservations',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'meta_key'       => 'staylodgic_booking_number',
					'meta_value'     => $booking_number,
				);

				$reservations = get_posts( $args );

				if ( ! empty( $reservations ) ) {
					$links = array();

					foreach ( $reservations as $reservation ) {
						$room_id   = get_post_meta( $reservation->ID, 'staylodgic_room_id', true );
						$room_name = get_the_title( $room_id );

						$customer_id = get_post_meta( $reservation->ID, 'staylodgic_customer_id', true );

						if ( ! empty( $room_name ) ) {
							$reservation_link = get_edit_post_link( $reservation->ID );
							$links[]          = '<li><a href="' . $reservation_link . '">' . $room_name . '</a></li>';
						}
					}

					if ( ! empty( $links ) ) {
						$customer_link = get_edit_post_link( $customer_id );
						$customer_name = get_the_title( $customer_id );
						echo '<p><strong>Booking No: ' . $booking_number . '</strong></p>';
						echo '<p><strong><a href="' . $customer_link . '">' . $customer_name . '</a></strong></p>';
						echo '<ol>' . implode( '', $links ) . '</ol>';
					} else {
						echo '-';
					}
				} else {
					echo '-';
				}
			} else {
				echo '-';
			}
		}
	}

	/**
	 * Method process_reservation_payment
	 *
	 * @return void
	 */
	public function process_reservation_payment() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		if ( isset( $_POST['total'] ) ) {
			$total          = sanitize_text_field( $_POST['total'] );
			$booking_number = sanitize_text_field( $_POST['booking_number'] );

			// Set the checkout started time in the session
			WC()->session->set( 'checkout_started', time() );

			// Empty the cart before adding the product
			WC()->cart->empty_cart();

			// Add the product to the cart
			$product_id = 476; // Product ID to add to cart
			$quantity   = 1; // Quantity of the product
			WC()->cart->add_to_cart( $product_id, $quantity );

			// Save the booking number in a session or transient for later use
			// Example: Save in session
			WC()->session->set( 'booking_number', $booking_number );
			WC()->session->set( 'total_price', $total );

			// Prepare the response data
			$response_data = array();

			// Check if the product was added to the cart successfully
			if ( ! WC()->cart->is_empty() ) {
				// Get the checkout URL
				$checkout_url = wc_get_checkout_url();

				// Set the redirect URL in the response data
				$response_data['redirect_url'] = $checkout_url;
			} else {
				// Set an error message in the response data
				$response_data['error_message'] = 'Failed to add the product to the cart.';
			}

			wp_send_json_success( $response_data );
		} else {
			wp_send_json_error( 'Invalid request.' );
		}
	}

	/**
	 * Method add_booking_number_to_checkout
	 *
	 * @return void
	 */
	public function add_booking_number_to_checkout() {
		// Get the booking number from the session
		$booking_number = WC()->session->get( 'booking_number' );

		// Display the booking number
		if ( ! empty( $booking_number ) ) {
			echo '<p data-booking-number="' . esc_html( $booking_number ) . '" class="booking-number">Booking Number: ' . esc_html( $booking_number ) . '</p>';
		}
	}

	/**
	 * Method process_booking_after_checkout
	 *
	 * @param $order_id $order_id [explicite description]
	 * @param $posted_data $posted_data [explicite description]
	 * @param $order $order [explicite description]
	 *
	 * @return void
	 */
	public function process_booking_after_checkout( $order_id, $posted_data, $order ) {
		// Get the booking number from the session
		$booking_number = WC()->session->get( 'booking_number' );

		// Perform your custom function based on the booking number
		if ( ! empty( $booking_number ) ) {
			// Call your custom function here and pass the booking number
			$this->set_booking_payment_done( $booking_number, $order );
		}
	}

	/**
	 * Method set_booking_payment_done
	 *
	 * @param $booking_number $booking_number [explicite description]
	 * @param $order $order [explicite description]
	 *
	 * @return void
	 */
	public function set_booking_payment_done( $booking_number, $order ) {
		// Perform your custom logic based on the booking number
		// This function will be executed after the checkout is successful

		// Save the booking number to the order meta
		$order->update_meta_data( 'staylodgic_booking_number', $booking_number );
		$order->save();
	}

	/**
	 * Method handle_payment_failure
	 *
	 * @param $order_id $order_id [explicite description]
	 * @param $retry_count $retry_count [explicite description]
	 *
	 * @return void
	 */
	public function handle_payment_failure( $order_id, $retry_count ) {
		// Get the booking number from the session
		$booking_number = WC()->session->get( 'booking_number' );
		// Perform actions for payment failure
		// You can retrieve the order details using the order ID if needed

		// Example: Log the payment failure

		// Example: Send a notification email to the site admin
		$admin_email = get_option( 'admin_email' );
		$subject     = __( 'Payment Failed for Order ID: ', 'staylodgic' ) . $order_id;
		$message     = __( 'Payment failed for order ID: ', 'staylodgic' ) . $order_id;
		wp_mail( $admin_email, $subject, $message );

		// Example: Redirect the customer to a specific page
		wp_redirect( home_url( '/payment-failure' ) );
		exit;
	}

	/**
	 * Method get_order_status
	 *
	 * @param $order_id $order_id [explicite description]
	 *
	 * @return void
	 */
	public function get_order_status( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order ) {
			return $order->get_status();
		}

		return ''; // Return an empty string if the order is not found
	}

	/**
	 * Method generate_invoice
	 *
	 * @param $order_id $order_id [explicite description]
	 *
	 * @return void
	 */
	public function generate_invoice( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order ) {
			// Initialize an empty string to store the invoice HTML
			$invoice_html = '';

			// Start building the invoice HTML
			$invoice_html .= '<div class="invoice">';
			$invoice_html .= '<div class="invoice-header">';
			$invoice_html .= '<h1>' . __( 'Invoice', 'staylodgic' ) . '</h1>';
			$invoice_html .= '</div>';
			$invoice_html .= '<div class="invoice-body">';

			// Get the order number
			$order_number  = $order->get_order_number();
			$invoice_html .= '<h2>' . __( 'Order Details', 'staylodgic' ) . '</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>' . __( 'Order Number:', 'staylodgic' ) . '</th>';
			$invoice_html .= '<td>' . $order_number . '</td>';
			$invoice_html .= '</tr>';

			// Get the general order information
			$order_status = $order->get_status();
			$order_date   = $order->get_date_created()->format( 'Y-m-d H:i:s' );
			$order_total  = $order->get_total();
			// Add more general order information as needed

			// Get the billing information
			$billing_address = $order->get_address( 'billing' );
			$billing_name    = $billing_address['first_name'] . ' ' . $billing_address['last_name'];
			$billing_address = $billing_address['address_1'];
			$billing_city    = $billing_address['city'];
			// Add more billing information as needed

			// Get the line items (products)
			$line_items    = $order->get_items();
			$products_html = '';

			foreach ( $line_items as $item ) {
				$product_name     = $item->get_name();
				$product_quantity = $item->get_quantity();
				$product_price    = $item->get_total();
				// Add more product details as needed

				// Build the HTML for each product row
				$products_html .= '<tr>';
				$products_html .= '<td>' . $product_name . '</td>';
				$products_html .= '<td>' . $product_quantity . '</td>';
				$products_html .= '<td>' . $product_price . '</td>';
				$products_html .= '</tr>';
			}

			// Complete the invoice HTML
			$invoice_html .= '</table>';

			$invoice_html .= '<h2>' . __( 'Billing Information', 'staylodgic' ) . '</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>' . __( 'Name:', 'staylodgic' ) . '</th>';
			$invoice_html .= '<td>' . $billing_name . '</td>';
			$invoice_html .= '</tr>';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>' . __( 'Address:', 'staylodgic' ) . '</th>';
			$invoice_html .= '<td>' . $billing_address . '</td>';
			$invoice_html .= '</tr>';
			// Add more billing information as needed

			$invoice_html .= '</table>';

			$invoice_html .= '<h2>' . __( 'Products', 'staylodgic' ) . '</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>' . __( 'Product Name', 'staylodgic' ) . '</th>';
			$invoice_html .= '<th>' . __( 'Quantity', 'staylodgic' ) . '</th>';
			$invoice_html .= '<th>' . __( 'Price', 'staylodgic' ) . '</th>';
			$invoice_html .= '</tr>';
			$invoice_html .= $products_html;
			$invoice_html .= '</table>';

			$invoice_html .= '<div class="invoice-total">';
			$invoice_html .= '<h3>' . __( 'Total:', 'staylodgic' ) . ' ' . $order_total . '</h3>';
			$invoice_html .= '</div>';

			$invoice_html .= '</div>';
			$invoice_html .= '</div>';

			// Return the generated invoice HTML
			return $invoice_html;
		}

		return ''; // Return an empty string if the order is not found
	}
}

new \Staylodgic\Payments();
