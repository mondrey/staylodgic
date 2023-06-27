<?php
namespace AtollMatrix;
class Payments {

	public function __construct() {
		// AJAX handler to save room metadata
		add_action('wp_ajax_processReservation',  array($this,'processReservation') );
		add_action('wp_ajax_nopriv_processReservation',  array($this,'processReservation') );

		add_action('woocommerce_before_calculate_totals',  array($this,'setProduct_Price_Dynamic') , 10, 1);

		// Add the booking number to the checkout page
		add_action( 'woocommerce_checkout_before_customer_details',  array($this,'addBookingNumber_To_Checkout' ) );
		add_action( 'woocommerce_before_thankyou',  array($this,'addBookingNumber_To_Checkout' ) );

		add_action('woocommerce_checkout_order_processed', array($this, 'processBooking_After_Checkout'), 10, 3);

		add_action('woocommerce_payment_failed',  array($this,'handle_Payment_Failure'), 10, 2 );


		// Add custom column to WooCommerce Order list table
		add_filter('manage_edit-shop_order_columns', array( $this,'add_booking_number_column') );


		// Display custom column value in WooCommerce Order list table
		add_action('manage_shop_order_posts_custom_column', array($this, 'display_booking_number_column_value'), 10, 2);
	}


	public function add_booking_number_column($columns) {
		// Add the custom column after the order total column
		$columns['booking_number'] = 'Booking Details:';
		return $columns;
	}

	public function display_booking_number_column_value($column, $post_id) {
		if ($column === 'booking_number') {
			// Get the booking number from the order meta data
			$booking_number = get_post_meta($post_id, 'atollmatrix_booking_number', true);

			if ($booking_number) {
				$args = array(
					'post_type'      => 'reservations',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'meta_key'       => 'atollmatrix_booking_number',
					'meta_value'     => $booking_number,
				);
	
				$reservations = get_posts($args);
				error_log(print_r($reservations, true));
	
				if (!empty($reservations)) {
					$links = array();
	
					foreach ($reservations as $reservation) {
						$room_id = get_post_meta($reservation->ID, 'atollmatrix_room_id', true);
						$room_name = get_the_title($room_id);
						
						$customer_id = get_post_meta($reservation->ID, 'atollmatrix_customer_id', true);
						

						if (!empty($room_name)) {
							$reservation_link = get_edit_post_link($reservation->ID);
							$links[] = '<li><a href="' . $reservation_link . '">' . $room_name . '</a></li>';
						}
					}
	
					if (!empty($links)) {
						$customer_link = get_edit_post_link($customer_id);
						$customer_name = get_the_title($customer_id);
						echo '<p><strong>Booking No: ' . $booking_number . '</strong></p>';
						echo '<p><strong><a href="' . $customer_link . '">' . $customer_name . '</a></strong></p>';
						echo '<ol>' . implode('', $links) . '</ol>';
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
		$order->update_meta_data('atollmatrix_booking_number', $booking_number);
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

	public function get_order_status($order_id) {
		$order = wc_get_order($order_id);
	
		if ($order) {
			return $order->get_status();
		}
	
		return ''; // Return an empty string if the order is not found
	}
	

	public function generate_invoice($order_id) {
		$order = wc_get_order($order_id);
	
		if ($order) {
			// Initialize an empty string to store the invoice HTML
			$invoice_html = '';
	
			// Start building the invoice HTML
			$invoice_html .= '<div class="invoice">';
			$invoice_html .= '<div class="invoice-header">';
			$invoice_html .= '<h1>Invoice</h1>';
			$invoice_html .= '</div>';
			$invoice_html .= '<div class="invoice-body">';
	
			// Get the order number
			$order_number = $order->get_order_number();
			$invoice_html .= '<h2>Order Details</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>Order Number:</th>';
			$invoice_html .= '<td>' . $order_number . '</td>';
			$invoice_html .= '</tr>';
	
			// Get the general order information
			$order_status = $order->get_status();
			$order_date = $order->get_date_created()->format('Y-m-d H:i:s');
			$order_total = $order->get_total();
			// Add more general order information as needed
	
			// Get the billing information
			$billing_address = $order->get_address('billing');
			$billing_name = $billing_address['first_name'] . ' ' . $billing_address['last_name'];
			$billing_address = $billing_address['address_1'];
			$billing_city = $billing_address['city'];
			// Add more billing information as needed
	
			// Get the line items (products)
			$line_items = $order->get_items();
			$products_html = '';
	
			foreach ($line_items as $item) {
				$product_name = $item->get_name();
				$product_quantity = $item->get_quantity();
				$product_price = $item->get_total();
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
	
			$invoice_html .= '<h2>Billing Information</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>Name:</th>';
			$invoice_html .= '<td>' . $billing_name . '</td>';
			$invoice_html .= '</tr>';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>Address:</th>';
			$invoice_html .= '<td>' . $billing_address . '</td>';
			$invoice_html .= '</tr>';
			// Add more billing information as needed
	
			$invoice_html .= '</table>';
	
			$invoice_html .= '<h2>Products</h2>';
			$invoice_html .= '<table class="invoice-table">';
			$invoice_html .= '<tr>';
			$invoice_html .= '<th>Product Name</th>';
			$invoice_html .= '<th>Quantity</th>';
			$invoice_html .= '<th>Price</th>';
			$invoice_html .= '</tr>';
			$invoice_html .= $products_html;
			$invoice_html .= '</table>';
	
			$invoice_html .= '<div class="invoice-total">';
			$invoice_html .= '<h3>Total: ' . $order_total . '</h3>';
			$invoice_html .= '</div>';
	
			$invoice_html .= '</div>';
			$invoice_html .= '</div>';
	
			// Return the generated invoice HTML
			return $invoice_html;
		}
	
		return ''; // Return an empty string if the order is not found
	}	

}

new \AtollMatrix\Payments();
