<?php
add_action('wp_ajax_cognitive_process_reservation', 'cognitive_process_reservation');
add_action('wp_ajax_nopriv_cognitive_process_reservation', 'cognitive_process_reservation');

function cognitive_process_reservation() {
	if (isset($_POST['total'])) {
		$total = sanitize_text_field($_POST['total']);
		$booking_number = sanitize_text_field($_POST['booking_number']);

		// Set the checkout started time in the session
		WC()->session->set('checkout_started', time());

		// Calculate the dynamic price based on the total value
		$dynamic_price = $total * 2; // Example calculation, adjust it based on your logic

		// Set the dynamic price for the product with ID 476
		cognitive_set_product_price_dynamic($dynamic_price);

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


add_action('woocommerce_before_calculate_totals', 'cognitive_set_product_price_dynamic', 10, 1);

function cognitive_set_product_price_dynamic($cart) {
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

// Add the booking number to the checkout page
add_action( 'woocommerce_checkout_before_customer_details', 'cognitive_add_booking_number_to_checkout' );
add_action( 'woocommerce_before_thankyou', 'cognitive_add_booking_number_to_checkout' );
function cognitive_add_booking_number_to_checkout() {
	// Get the booking number from the session
	$booking_number = WC()->session->get( 'booking_number' );
	
	// Display the booking number
	if ( ! empty( $booking_number ) ) {
		echo '<p class="booking-number">Booking Number: ' . esc_html( $booking_number ) . '</p>';
	}
}

add_action('woocommerce_checkout_order_processed', 'cognitive_process_booking_after_checkout', 10, 3);

function cognitive_process_booking_after_checkout($order_id, $posted_data, $order) {
	// Get the booking number from the session
	$booking_number = WC()->session->get('booking_number');
	
	// Perform your custom function based on the booking number
	if (!empty($booking_number)) {
		// Call your custom function here and pass the booking number
		cognitive_set_booking_payment_done($booking_number, $order);
	}
}

function cognitive_set_booking_payment_done($booking_number, $order) {
	// Perform your custom logic based on the booking number
	// This function will be executed after the checkout is successful
	
	// Save the booking number to the order meta
	$order->update_meta_data('booking_number', $booking_number);
	$order->save();
}


add_action('woocommerce_payment_failed', 'cognitive_handle_payment_failure', 10, 2);
function cognitive_handle_payment_failure($order_id, $retry_count) {
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

function cognitive_check_abandoned_checkouts() {
	$abandoned_timeout = 60 * 60; // Timeout period in seconds (e.g., 1 hour)
	$checkout_started = WC()->session->get('checkout_started');
	$booking_number = WC()->session->get('booking_number');
	if ($checkout_started && (time() - $checkout_started) > $abandoned_timeout) {
		// Abandoned checkout detected, perform your desired actions
		// - Empty the cart
		// - Log the abandoned checkout
		// - Send a notification email
		// - Redirect the customer, etc.
		
		// Empty the cart
		WC()->cart->empty_cart();
		
		// Log the abandoned checkout
		error_log('Abandoned checkout detected: ' . WC()->session->get('order_awaiting_payment'));
		
		// Send a notification email
		$admin_email = get_option('admin_email');
		$subject = 'Abandoned Checkout';
		$message = 'A customer has abandoned their checkout.';
		wp_mail($admin_email, $subject, $message);
	}
}
add_action('cognitive_check_abandoned_checkouts_event', 'cognitive_check_abandoned_checkouts');

function schedule_check_abandoned_checkouts_event() {
	if (!wp_next_scheduled('cognitive_check_abandoned_checkouts_event')) {
		wp_schedule_event(time(), '15_minutes', 'cognitive_check_abandoned_checkouts_event');
	}
}
add_action('wp', 'schedule_check_abandoned_checkouts_event');

