<?php
add_action( 'admin_init', 'cognitive_populate_demo_bookings' );
function cognitive_populate_demo_bookings() {
	if (!isset($_GET['populate_data'])) {
		return;
	}
	// Define room details
	$rooms = [
		['id' => 33, 'qty' => 2],
		['id' => 31, 'qty' => 1],
		['id' => 28, 'qty' => 1],
		['id' => 22, 'qty' => 3],
	];

	// Define demo customer data
	$customer_data = [];
	for ($i=0; $i<50; $i++) {
		$random_number = rand(1, 1000);  // Random number for customer name
		$customer_data[] = [
			'name' => "Customer $random_number",
			'email' => "customer$random_number@gmail.com",
			'phone' => str_pad($i, 10, "1", STR_PAD_LEFT),
			'address' => "$i Main St",
			'city' => "City $i",
			'state' => "State $i",
			'zip' => str_pad($i, 5, "0", STR_PAD_LEFT),
			'country' => "Country $i"
		];
	}

	// Define demo booking dates
	$dates = [];
	for ($i=2; $i<=30; $i++) {
		$start_date = date('Y-m-d', strtotime("+$i days"));
		$end_date = date('Y-m-d', strtotime($start_date . ' +' . rand(1, 7) . ' days')); // Random duration up to 7 days
		$dates[] = ['checkin' => $start_date, 'checkout' => $end_date];
	}

	// Loop through each room
	foreach ($rooms as $room) {
		// Generate bookings for each room based on its qty
		for($i = 0; $i < $room['qty']; $i++) {
			// Pick customer data
			$customer = $customer_data[$i];
			// Generate a unique booking number
			$booking_number = uniqid('booking-');

			// Create customer post
			$customer_post_data = array(
				'post_type'     => 'customers',
				'post_title'    => $customer['name'],
				'post_status'   => 'publish',
				'meta_input'    => array(
					'pagemeta_full_name' => $customer['name'],
					'pagemeta_email_address' => $customer['email'],
					'pagemeta_phone_number' => $customer['phone'],
					'pagemeta_street_address' => $customer['address'],
					'pagemeta_city' => $customer['city'],
					'pagemeta_state' => $customer['state'],
					'pagemeta_zip_code' => $customer['zip'],
					'pagemeta_country' => $customer['country'],
					'pagemeta_booking_number' => $booking_number,
				),
			);
			$customer_post_id = wp_insert_post($customer_post_data);

			// Pick date range
			$date = $dates[$i];

			// Create reservation post
			$post_data = array(
				'post_type'     => 'reservations',
				'post_title'    => $booking_number,
				'post_status'   => 'publish',
				'meta_input'    => array(
					'pagemeta_room_name' => $room['id'],
					'pagemeta_checkin_date' => $date['checkin'],
					'pagemeta_checkout_date' => $date['checkout'],
					'pagemeta_booking_number' => $booking_number,
					'pagemeta_customer_id' => $customer_post_id,
				),
			);
			$post_id = wp_insert_post($post_data);
			if($post_id) {
				update_reservations_array_on_save($post_id, get_post($post_id), true);
			}
		}
	}
}
