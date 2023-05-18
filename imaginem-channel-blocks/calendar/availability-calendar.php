<?php
function cognitive_get_reservation_guest_name($reservation_id) {
    $guest_name = '';
    $guest_name = get_post_meta($reservation_id, 'pagemeta_reservation_guest_name', true);
    return $guest_name;
}
function cognitive_generate_reserved_tab( $reservation_data ) {
    $tab = '';
	
	print_r(  $reservation_data );
    foreach ($reservation_data as $reservation) {
		$start_date_display = '';
		$guest_name = '';
        if ( $reservation['start'] <> 'no' ) {
			$start_date = new DateTime();
        	$start_date->setTimestamp($reservation['start']);
			$start_date_display = $start_date->format('M j, Y');
			$guest_name = cognitive_get_reservation_guest_name($reservation['id']);
			$display = 'Reserved for ' . $guest_name . ' - ' . $start_date_display;
		} else {
			$guest_name = cognitive_get_reservation_guest_name($reservation['id']);
			$display = 'Extended for ' . $guest_name;
		}
        $tab .= '<div class="reserved-tab-wrap"><div class="reserved-tab reserved-tab-days-3">'.$display.'</div></div>';
    }
    return $tab;
}
function cognitive_generate_unique_reservation_id( $reservation_post_id ) {
    // Generate a random string or use a timestamp as a unique identifier
    $unique_identifier = uniqid(); // Example: Random string
    // $unique_identifier = time(); // Example: Timestamp

    // Combine the reservation post ID with the unique identifier
    $reservation_id = $reservation_post_id . '-' . $unique_identifier;

    return $reservation_id;
}

//add_action('save_post_reservation', 'updateAvailabilityOnReservationSave', 10, 3);
function updateAvailabilityOnReservationSave($post_id, $post, $update) {
    // Check if this is a reservation post
    if ($post->post_type === 'reservations') {
        // Call the function to update availability based on reservation changes
        storeAvailabilityForReservation($post_id);
    }
}

function storeAvailabilityForReservation($reservation_id) {
    // Get the reservation data
    $start_date = get_field('pagemeta_reservation_checkin', $reservation_id); // Replace 'check_in' with the correct meta key for the check-in date field
    $end_date = get_field('pagemeta_reservation_checkout', $reservation_id); // Replace 'check_out' with the correct meta key for the check-out date field
    $room_id = get_field('pagemeta_room_name', $reservation_id); // Replace 'room' with the correct meta key for the room field
    $quantity = get_field('pagemeta_room_quantity', $reservation_id); // Replace 'quantity' with the correct meta key for the quantity field

    // Call the storeAvailability() function to update availability for the room based on the reservation
    storeAvailability($room_id, $start_date, $end_date, $quantity);
}

function storeAvailability($roomID, $start, $end, $quantity) {
    // Get the Room post object
    $room = get_post($roomID);

    // Check if the Room post exists
    if ($room) {
        // Calculate the number of days between start and end dates
        $start_date = new DateTime($start);
        $end_date = new DateTime($end);
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($start_date, $interval, $end_date);
        
        // Initialize the availability matrix array
        $availabilityMatrix = array();

        // Iterate over each date in the range
        foreach ($date_range as $date) {
            // Format the date in the desired format
            $formatted_date = $date->format('Y-m-d');
            
            // Calculate the availability for the date
            $existing_availability = get_field('availability_matrix', $roomID);
            $reserved_quantity = get_reserved_quantity($roomID, $formatted_date); // Custom function to get the reserved quantity for the room on the date
            
            $available = $quantity - $reserved_quantity;
            $availability = max(0, $available);

            // Store the availability in the matrix array
            $availabilityMatrix[] = array(
                'date' => $formatted_date,
                'availability' => $availability,
            );
        }

        // Update the availability matrix field for the Room post
        update_field('availability_matrix', $availabilityMatrix, $roomID);
    }
}
function cognitive_get_availability( $roomID ) {
    // Get the availability matrix field values for the Room post
    $availabilityMatrix = get_field('availability_matrix', $roomID);

    // Return the availability matrix
    return $availabilityMatrix;
}
// Function to check if a date falls within a reservation
function cognitive_is_date_reserved( $date, $roomtype ) {

	$currentDate = strtotime( $date );
	$start = false;

    $args = array(
        'post_type' => 'reservations',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'pagemeta_room_name',
                'value' => $roomtype,
                'compare' => '='
            )
        )
    );
	
	$query = new WP_Query($args);

	$reservation_checkin  = '';
	$reservation_checkout = '';
	$reservedRooms        = array();
	$reserved_data = array();
	$found = false;

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			
			$reservedRooms[] = get_the_ID();
			$reservation_id = get_the_ID();
			$custom = get_post_custom( get_the_ID() );
			if (isset($custom['pagemeta_reservation_checkin'][0])) {
				$dateRangeValue=$custom['pagemeta_reservation_checkin'][0];
			}
			if (isset($custom['pagemeta_room_name'][0])) {
				$room_id=$custom['pagemeta_room_name'][0];
			}

			// Date will be like so $dateRangeValue = "2023-05-21 to 2023-05-24";
			$dateRangeParts = explode(" to ", $dateRangeValue);
			
			$checkin = '';
			$checkout = '';
			if (count($dateRangeParts) >= 2) {
				$checkin = $dateRangeParts[0];
				$checkout = $dateRangeParts[1];
			}

			// $checkin_start_datetime = explode(" ", $reservation_checkin);
			// $reservation_checkin_date = $checkin_start_datetime[0];

			// $checkout_start_datetime = explode(" ", $reservation_checkout);
			// $reservation_checkout_date = $checkout_start_datetime[0];

			$reservationStartDate = strtotime($checkin);
			$reservationEndDate = strtotime($checkout);
			$numberOfDays = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;

			// if ( $reservation_checkin_date == $date && $room_id == $roomtype ) {
			// 	echo 'Reserved';
			// }

			if ( $room_id == $roomtype ) {
				// echo $currentDate . '<br/>' . $reservationStartDate . '<br/>';
				// echo $currentDate . '<br/>' . $reservationEndDate . '<br/>';
				// Check if the current date falls within the reservation period
				if ( $currentDate >= $reservationStartDate && $currentDate <= $reservationEndDate ) {
					// Check if the reservation spans the specified number of days
					$reservationDuration = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;
					if ( $numberOfDays > 0 ) {
						if ( $currentDate == $reservationStartDate ) {
							$start = $reservationStartDate;
						} else {
							$start = 'no';
						}
						$reservation_data['id'] = $reservation_id;
						$reservation_data['start'] = $start;
						$reserved_data[]=$reservation_data; // Date is part of a reservation for the specified number of days
						$found = true;
					}
				}
			}

		}
	}

	if ( $found ) {
		return $reserved_data;
	} else {
		return false;
	}

	// foreach ( $reservationData as $reservation ) {
	// 	$reservationStartDate = strtotime( $reservation['start_date'] );
	// 	$reservationEndDate = strtotime( $reservation['end_date'] );

	// 	// Check if the current date falls within the reservation period
	// 	if ( $currentDate >= $reservationStartDate && $currentDate <= $reservationEndDate ) {
	// 		// Check if the reservation spans the specified number of days
	// 		$reservationDuration = floor( ( $reservationEndDate - $reservationStartDate ) / ( 60 * 60 * 24 ) ) + 1;
    //         if ( $reservationDuration === $numberOfDays ) {
    //             return true; // Date is part of a reservation for the specified number of days
    //         }
    //     }
    // }

}
// Add the Availability menu item to the admin menu
function cognitive_room_reservation_plugin_add_admin_menu() {
	add_menu_page(
		'Availability',
		'Availability',
		'manage_options',
		'room-availability',
		'cognitive_room_reservation_plugin_display_availability',
		'dashicons-calendar-alt',
		20
	);
}
add_action( 'admin_menu', 'cognitive_room_reservation_plugin_add_admin_menu' );

function cognitive_get_room_type_quantity( $room_id ) {
	$custom = get_post_custom( $room_id );
	if (isset($custom['pagemeta_max_rooms_of_type'][0])) {
		$room_max_rooms_of_type = $custom['pagemeta_max_rooms_of_type'][0];

		return $room_max_rooms_of_type;
	}
	return false;
}
function cognitive_get_room_type_base_rate( $room_id ) {
	$custom = get_post_custom( $room_id );
	if (isset($custom['pagemeta_base_rate'][0])) {
		$base_rate = $custom['pagemeta_base_rate'][0];

		return $base_rate;
	}
	return false;
}
// Callback function to display the Availability page
function cognitive_room_reservation_plugin_display_availability() {
	// Check if user has sufficient permissions
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Output the HTML for the Availability page
	?>
	<div class="wrap">
		<h1>Availability</h1>
		<?php
		// Add any custom HTML content here
		?>
	</div>
	<input type="text" class="availabilitycalendar" id="availabilitycalendar" name="availabilitycalendar" value=""/>
	<a href="#" id="popup-link" data-bs-toggle="modal" data-bs-target="#admin-popup">Update Quantity</a>

	<div id="container">
	
<div id="calendar">



<?php echo cognitive_get_availability_calendar(); ?>

</div>
</div>
	<?php
}

// PHP function that generates the content
function cognitive_get_availability_calendar() {
// Define the start and end dates
$today = new DateTime();
$week_ago = (new DateTime())->modify('-7 days');
$end_date = (new DateTime())->modify('+30 days');

$startDate = $week_ago->format('Y-m-d');
$endDate = $end_date->format('Y-m-d');

$numDays = (new DateTime($endDate))->diff(new DateTime($startDate))->days + 1;
	
// Generate an array of dates for the calendar
$dates = [];
for ($day = 0; $day < $numDays; $day++) {
	$currentDate = new DateTime($startDate);
	$currentDate->add(new DateInterval("P{$day}D"));
	$dates[] = $currentDate;
}
	
	$room_list = get_posts('post_type=room&orderby=title&numberposts=-1&order=ASC');
	if ($room_list) {
		foreach($room_list as $key => $list) {
			$rooms[$list->ID] = $list->post_title;
		}
	} else {
		$rooms[0]="Rooms not found.";
	}

	ob_start();
	?>
	<table id="calendarTable">
		<tr class="calendarRow">
			<td class="calendarCell rowHeader"></td>
			<?php
			$currentMonth = '';
			foreach ($dates as $date) :
				$month = $date->format('F');
				if ($currentMonth !== $month) :
					$currentMonth = $month;
			?>
					<td class="calendarCell monthHeader">
						<div class="month"><?php echo $currentMonth; ?></div>
						<div class="day"><?php echo $date->format('j'); ?></div>
					</td>
				<?php else : ?>
					<td class="calendarCell">
						<div class="day"><?php echo $date->format('j'); ?></div>
					</td>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
		<?php foreach ($rooms as $roomId => $roomName) : ?>
			<tr class="calendarRow">
				<td class="calendarCell rowHeader"><?php echo $roomName; ?></td>
				<?php foreach ($dates as $date) : ?>
					<td class="calendarCell">
						<?php
						$dateString = $date->format('Y-m-d');
						$reservation_data = array();
						$reservation_data = cognitive_is_date_reserved($dateString, $roomId);
						if ( $reservation_data ) {
							print_r($reservation_data);
							echo '<br/>';
							echo 'Reserved';
							echo '<br/>';
							echo '-----<br/>';
							echo cognitive_generate_reserved_tab( $reservation_data );
						}
						?>
						Quantity: <a href="#" class="quantity-link" data-date="<?php echo $dateString; ?>" data-room="<?php echo $roomId; ?>"><?php echo cognitive_get_quantity_array_from_room($roomId, $dateString); ?></a><br>
						<?php
						$rate = cognitive_get_room_type_base_rate( $roomId );
						if (!empty($rate) && isset($rate) && $rate > 0) {
							echo 'Rate: $' . $rate;
						} else {
							echo 'Rate not available';
						}
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
	$output = ob_get_clean();
	echo $output;

	cognitive_quanity_modal();
}

// WordPress AJAX action hook
add_action('wp_ajax_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');
add_action('wp_ajax_nopriv_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');
// PHP function that generates the content
function cognitive_ajax_get_availability_calendar() {
	// Get the start and end dates from the AJAX request
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	// Define the start and end dates
	// Define the start and end dates as DateTime objects
	$startDate = new DateTime($start_date);
	$endDate = new DateTime($end_date);

	// $startDate = new DateTime("2023-05-17");
	// $endDate = new DateTime("2023-06-22");
	
	// Calculate the number of days between the start and end dates
	$numDays = $endDate->diff($startDate)->days + 1;
	
	// Generate an array of dates for the calendar
	$dates = [];
	for ($day = 0; $day < $numDays; $day++) {
		$currentDate = clone $startDate;
		$currentDate->add(new DateInterval("P{$day}D"));
		$dates[] = $currentDate;
	}
	
	$room_list = get_posts('post_type=room&orderby=title&numberposts=-1&order=ASC');
	if ($room_list) {
		foreach($room_list as $key => $list) {
			$rooms[$list->ID] = $list->post_title;
		}
	} else {
		$rooms[0]="Rooms not found.";
	}

	ob_start();
	?>
	<table id="calendarTable">
		<tr class="calendarRow">
			<td class="calendarCell rowHeader"></td>
			<?php
			$currentMonth = '';
			foreach ($dates as $date) :
				$month = $date->format('F');
				if ($currentMonth !== $month) :
					$currentMonth = $month;
			?>
					<td class="calendarCell monthHeader">
						<div class="month"><?php echo $currentMonth; ?></div>
						<div class="day"><?php echo $date->format('j'); ?></div>
					</td>
				<?php else : ?>
					<td class="calendarCell">
						<div class="day"><?php echo $date->format('j'); ?></div>
					</td>
				<?php endif; ?>
			<?php endforeach; ?>
		</tr>
		<?php foreach ($rooms as $roomId => $roomName) : ?>
			<tr class="calendarRow">
				<td class="calendarCell rowHeader"><?php echo $roomName; ?></td>
				<?php foreach ($dates as $date) : ?>
					<td class="calendarCell">
						<?php
						$dateString = $date->format('Y-m-d');
						$reservation_data = array();
						$reservation_data = cognitive_is_date_reserved($dateString, $roomId);
						if ( $reservation_data ) {
							print_r($reservation_data);
							echo '<br/>';
							echo 'Reserved';
							echo '<br/>';
							echo '-----<br/>';
							echo cognitive_generate_reserved_tab( $reservation_data );
						}
						?>
						Quantity: <?php echo cognitive_get_quantity_array_from_room($postID, $dateString); ?><br>
						<?php
						$rate = cognitive_get_room_type_base_rate( $roomId );
						if (!empty($rate) && isset($rate) && $rate > 0) {
							echo 'Rate: $' . $rate;
						} else {
							echo 'Rate not available';
						}
						?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
	$output = ob_get_clean();
	echo $output;
	wp_die();
}

// AJAX handler to save room metadata
add_action('wp_ajax_cognitive_update_room_availability', 'cognitive_update_room_availability');
add_action('wp_ajax_nopriv_cognitive_update_room_availability', 'cognitive_update_room_availability');
function cognitive_update_room_availability() {
	if (isset($_POST['dateRange'])) {
		$dateRange = $_POST['dateRange'];
	} else {
		// Return an error response if dateRange is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing date range parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['quantity'])) {
		$quantity = $_POST['quantity'];
	} else {
		// Return an error response if quantity is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing quantity parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	if (isset($_POST['postID'])) {
		$postID = $_POST['postID'];
	} else {
		// Return an error response if postID is not set
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Missing post ID parameter.'
			)
		);
		wp_send_json_error($response);
		return;
	}

	// Split the date range into start and end dates
	$dateRangeArray = explode(" to ", $dateRange);
	if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
		// Use the single date as both start and end date
		$startDate = $dateRangeArray[0];
		$endDate = $startDate;
	} elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
		// Return an error response if dateRange is invalid
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid date range.'
			)
		);
		wp_send_json_error($response);
		return;
	} else {
		$startDate = $dateRangeArray[0];
		$endDate = $dateRangeArray[1];
	}

	// If the end date is empty, set it to the start date
	if (empty($endDate)) {
		$endDate = $startDate;
	}

	// Retrieve the existing quantity_array meta value
	$quantityArray = get_post_meta($postID, 'quantity_array', true);

	// If the quantity_array is not an array, initialize it as an empty array
	if (!is_array($quantityArray)) {
		$quantityArray = array();
	}

	// Generate an array of dates between the start and end dates
	$dateRange = cognitive_createDateRangeArray($startDate, $endDate);

	// Update the quantity values for the specified date range
	foreach ($dateRange as $date) {
		$quantityArray[$date] = $quantity;
	}

	// Update the metadata for the 'reservations' post
	if (!empty($postID) && is_numeric($postID)) {
		// Update the post meta with the modified quantity array
		update_post_meta($postID, 'quantity_array', $quantityArray);
		// Return a success response
		$response = array(
			'success' => true,
			'data' => array(
				'message' => 'Room availability updated successfully.'
			)
		);
		wp_send_json_success($response);
	} else {
		// Return an error response
		$response = array(
			'success' => false,
			'data' => array(
				'message' => 'Invalid post ID.'
			)
		);
		wp_send_json_error($response);
	}

	wp_die(); // Optional: Terminate script execution
}

// Function to create an array of dates between two dates
function cognitive_createDateRangeArray($startDate, $endDate) {
	$dateRangeArray = array();

	$currentDate = strtotime($startDate);
	$endDate = strtotime($endDate);

	while ($currentDate <= $endDate) {
		$dateRangeArray[] = date('Y-m-d', $currentDate);
		$currentDate = strtotime('+1 day', $currentDate);
	}

	return $dateRangeArray;
}

function cognitive_get_quantity_array_from_room($postID, $dateString) {
    $quantityArray = get_post_meta($postID, 'quantity_array', true);
    
    // Check if the quantity_array exists and the date is available
    if (!empty($quantityArray) && isset($quantityArray[$dateString])) {
        return $quantityArray[$dateString];
    }
    
    return false;
}
?>