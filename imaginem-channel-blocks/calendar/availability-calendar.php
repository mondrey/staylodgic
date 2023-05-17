<?php
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

	$args = array(
		'post_type' => 'reservations', // Replace with your actual custom post type slug
		'posts_per_page' => -1, // Retrieve all reserved rooms
	);
	
	$query = new WP_Query($args);

	$reservation_checkin  = '';
	$reservation_checkout = '';
	$reservedRooms        = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			
			$reservedRooms[] = get_the_ID();
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
						return true; // Date is part of a reservation for the specified number of days
					}
				}
			}

		}
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

    return false; // Date is not reserved
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
						if (cognitive_is_date_reserved($dateString, $roomId)) {
							echo 'Reserved';
						}
						?>
						Quantity: <?php echo cognitive_get_room_type_quantity($roomId); ?><br>
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
}

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
						if (cognitive_is_date_reserved($dateString, $roomId)) {
							echo 'Reserved';
						}
						?>
						Quantity: <?php echo cognitive_get_room_type_quantity($roomId); ?><br>
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

// WordPress AJAX action hook
add_action('wp_ajax_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');
add_action('wp_ajax_nopriv_cognitive_ajax_get_availability_calendar', 'cognitive_ajax_get_availability_calendar');

