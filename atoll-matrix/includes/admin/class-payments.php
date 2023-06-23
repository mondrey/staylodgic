<?php
namespace AtollMatrix;
class Payments {

	public function __construct() {
		// AJAX handler to save room metadata
		add_action('wp_ajax_processReservation',  array($this,'processReservation') );
		add_action('wp_ajax_nopriv_processReservation',  array($this,'processReservation') );

		add_action('woocommerce_before_calculate_totals',  array($this,'setProduct_Price_Dynamic', 10, 1) );

		// Add the booking number to the checkout page
		add_action( 'woocommerce_checkout_before_customer_details',  array($this,'addBookingNumber_To_Checkout' ) );
		add_action( 'woocommerce_before_thankyou',  array($this,'addBookingNumber_To_Checkout' ) );

		add_action('woocommerce_checkout_order_processed',  array($this,'processBooking_After_Checkout', 10, 3) );

		add_action('woocommerce_payment_failed',  array($this,'handle_Payment_Failure', 10, 2) );
	}

	public function processReservation() {
		if (isset($_POST['total'])) {
			$total = sanitize_text_field($_POST['total']);
			$booking_number = sanitize_text_field($_POST['booking_number']);

			// Set the checkout started time in the session
			WC()->session->set('checkout_started', time());

			// Calculate the dynamic price based on the total value
			$dynamic_price = $total * 2; // Example calculation, adjust it based on your logic

			// Set the dynamic price for the product with ID 476
			self::setProduct_Price_Dynamic($dynamic_price);

			// Empty the cart before adding the product
			WC()->cart->empty_cart();

			// Add the product to the cart
			$product_id = 476; // Product ID to add to cart
			$quantity = 1; // Quantity of the product
			WC()->cart->add_to_cart($product_id, $quantity);

			// Save the booking number in a session or transient for later use
			// Example: Save in session
			WC()->session->set('booking_number', $booking_number);

			// Prepare the response data
			$response_data = array();

			// Check if the product was added to the cart successfully
			if (!WC()->cart->is_empty()) {
				// Get the checkout URL
				$checkout_url = wc_get_checkout_url();

				// Set the redirect URL in the response data
				$response_data['redirect_url'] = $checkout_url;
			} else {
				// Set an error message in the response data
				$response_data['error_message'] = 'Failed to add the product to the cart.';
			}

			wp_send_json_success($response_data);
		} else {
			wp_send_json_error('Invalid request.');
		}
	}

	public function setProduct_Price_Dynamic($cart) {
		if (is_admin() && !defined('DOING_AJAX')) {
			return;
		}

		// Make sure the $cart parameter is an instance of the WC_Cart class
		if (!($cart instanceof WC_Cart)) {
			error_log('Invalid $cart object: ' . print_r($cart, true));
			return;
		}

		// Loop through each cart item
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			$product = $cart_item['data'];
			$product_id = $product->get_id();

			// Set the dynamic price for the specific product
			if ($product_id === 476) {
				// Calculate the dynamic price based on your logic
				$dynamic_price = 100; // Set the desired dynamic price here

				// Set the dynamic price for the product
				$product->set_price($dynamic_price);
			}
		}
	}

	public function addBookingNumber_To_Checkout() {
		// Get the booking number from the session
		$booking_number = WC()->session->get( 'booking_number' );
		
		// Display the booking number
		if ( ! empty( $booking_number ) ) {
			echo '<p class="booking-number">Booking Number: ' . esc_html( $booking_number ) . '</p>';
		}
	}

	public function processBooking_After_Checkout($order_id, $posted_data, $order) {
		// Get the booking number from the session
		$booking_number = WC()->session->get('booking_number');
		
		// Perform your custom function based on the booking number
		if (!empty($booking_number)) {
			// Call your custom function here and pass the booking number
			$this->setBooking_Payment_Done($booking_number, $order);
		}
	}

	public function setBooking_Payment_Done($booking_number, $order) {
		// Perform your custom logic based on the booking number
		// This function will be executed after the checkout is successful
		
		// Save the booking number to the order meta
		$order->update_meta_data('booking_number', $booking_number);
		$order->save();
	}

	public function handle_Payment_Failure($order_id, $retry_count) {
		// Get the booking number from the session
		$booking_number = WC()->session->get('booking_number');
		// Perform actions for payment failure
		// You can retrieve the order details using the order ID if needed
		
		// Example: Log the payment failure
		error_log('Payment failed for order ID: ' . $order_id);
		
		// Example: Send a notification email to the site admin
		$admin_email = get_option('admin_email');
		$subject = 'Payment Failed for Order ID: ' . $order_id;
		$message = 'Payment failed for order ID: ' . $order_id;
		wp_mail($admin_email, $subject, $message);
		
		// Example: Redirect the customer to a specific page
		wp_redirect(home_url('/payment-failure'));
		exit;
	}

}
