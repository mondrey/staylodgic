<?php

namespace AtollMatrix;

class Booking
{

	protected $checkinDate;
	protected $checkoutDate;
	protected $staynights;
	protected $adultGuests;
	protected $childrenGuests;
	protected $totalGuests;
	protected $totalChargeableGuests;
	protected $roomArray;
	protected $ratesArray;
	protected $canAccomodate;
	protected $bookingNumber;
	protected $children_age;
	protected $bookingSearchResults;

	public function __construct(
		$bookingNumber = null,
		$checkinDate = null,
		$checkoutDate = null,
		$staynights = null,
		$adultGuests = null,
		$childrenGuests = null,
		$children_age = null,
		$totalGuests = null,
		$totalChargeableGuests = null,
		$roomArray = null,
		$ratesArray = null,
		$canAccomodate = null,
		$bookingSearchResults = null
	) {
		$this->checkinDate           = $checkinDate;
		$this->checkoutDate          = $checkoutDate;
		$this->staynights            = $staynights;
		$this->adultGuests           = $adultGuests;
		$this->childrenGuests        = $childrenGuests;
		$this->totalGuests           = $totalGuests;
		$this->totalChargeableGuests = $totalChargeableGuests;
		$this->children_age          = $children_age;
		$this->roomArray             = $roomArray;
		$this->ratesArray            = $ratesArray;
		$this->canAccomodate         = $canAccomodate;
		$this->bookingSearchResults  = $bookingSearchResults;
		$this->bookingNumber         = uniqid();

		add_shortcode('hotel_booking_search', array($this, 'hotelBooking_SearchForm'));
		// AJAX handler to save room metadata

		add_action('wp_ajax_booking_BookingSearch', array($this, 'booking_BookingSearch'));
		add_action('wp_ajax_nopriv_booking_BookingSearch', array($this, 'booking_BookingSearch'));

		add_action('wp_ajax_process_RoomPrice', array($this, 'process_RoomPrice')); // For logged-in users
		add_action('wp_ajax_nopriv_process_RoomPrice', array($this, 'process_RoomPrice')); // For non-logged-in users

		add_action('wp_ajax_process_SelectedRoom', array($this, 'process_SelectedRoom')); // For logged-in users
		add_action('wp_ajax_nopriv_process_SelectedRoom', array($this, 'process_SelectedRoom')); // For non-logged-in users
	}

	public function process_RoomData(
		$bookingnumber = null,
		$room_id = null,
		$room_price = null,
		$bed_layout = null,
		$meal_plan = null,
		$meal_plan_price = null
	)
	{
		// Get the data sent via AJAX

		$roomName = \AtollMatrix\Rooms::getRoomName_FromID($room_id);

		$booking_results = self::getBookingTransient( $bookingnumber );

		// Perform any processing you need with the data
		// For example, you can save it to the database or perform calculations

		// Return a response (you can modify this as needed)
		$response = array(
			'success' => true,
			'message' => 'Data: ' . $roomName . ',received successfully.',
		);

		if ( is_array( $booking_results ) ) {

			error_log( '====== From Transient ======' );
			error_log( print_r( $booking_results , true ));

			$booking_results['choice']['room_id'] = $room_id;
			$booking_results['choice']['bedlayout'] = $bed_layout;
			$booking_results['choice']['mealplan'] = $meal_plan;

			$booking_results['choice']['mealplan_price'] = 0;
			if ( 'none' !== $meal_plan ) {
				$booking_results['choice']['mealplan_price'] = $booking_results[$room_id]['meal_plan'][$booking_results['choice']['mealplan']];
			}

			$booking_results['choice']['room_id'] = $room_id;

			self::setBookingTransient( $booking_results, $bookingnumber);

			error_log( '====== Saved Transient ======' );
			error_log( print_r( $booking_results , true ));

			error_log( '====== Specific Room ======' );
			error_log( print_r( $booking_results[$room_id] , true ));

		} else {
			$booking_results = false;
		}

		// Send the JSON response
		return $booking_results;
	}

	public function process_SelectedRoom()
	{

		$bookingnumber   = sanitize_text_field($_POST['bookingnumber']);
		$room_id         = sanitize_text_field($_POST['room_id']);
		$room_price      = sanitize_text_field($_POST['room_price']);
		$bed_layout      = sanitize_text_field($_POST['bed_layout']);
		$meal_plan       = sanitize_text_field($_POST['meal_plan']);
		$meal_plan_price = sanitize_text_field($_POST['meal_plan_price']);

		$booking_results = self::process_RoomData(
			$bookingnumber,
			$room_id,
			$room_price,
			$bed_layout,
			$meal_plan,
			$meal_plan_price
		);
		
		if ( is_array( $booking_results ) ) {

			$html = self::bookingSummary(
				$booking_results['choice']['room_id'],
				$booking_results[$room_id]['roomtitle'],
				$booking_results['checkin'],
				$booking_results['checkout'],
				$booking_results['staynights'],
				$booking_results['adults'],
				$booking_results['children'],
				$booking_results['choice']['bedlayout'],
				$booking_results['choice']['mealplan'],
				$booking_results['choice']['mealplan_price'],
				$booking_results[$room_id]['staydate'],
				$booking_results[$room_id]['totalroomrate']
			);

		} else {
			$html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>Session timed out. Please reload the page.</div>';
		}

		// Send the JSON response
		wp_send_json($html);
	}

	public function process_RoomPrice()
	{
		
		$bookingnumber   = sanitize_text_field($_POST['booking_number']);
		$room_id         = sanitize_text_field($_POST['room_id']);
		$room_price      = sanitize_text_field($_POST['room_price']);
		$bed_layout      = sanitize_text_field($_POST['bed_layout']);
		$meal_plan       = sanitize_text_field($_POST['meal_plan']);
		$meal_plan_price = sanitize_text_field($_POST['meal_plan_price']);

		$booking_results = self::process_RoomData(
			$bookingnumber,
			$room_id,
			$room_price,
			$bed_layout,
			$meal_plan,
			$meal_plan_price
		);

		if ( is_array( $booking_results ) ) {

			$html = self::getSelectedPlanPrice( $room_id, $booking_results );

		} else {
			$html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>Session timed out. Please reload the page.</div>';
		}

		// Send the JSON response
		wp_send_json($html);
	}

	public function getSelectedPlanPrice( $room_id, $booking_results )
	{
		$total_price_tag = atollmatrix_price( intval( $booking_results[$room_id]['totalroomrate'] ) + intval( $booking_results['choice']['mealplan_price'] ) );
		return $total_price_tag;
	}

	public function bookingSummary(
		$room_id = null,
		$room_name = null,
		$checkin = null,
		$checkout = null,
		$staynights = null,
		$adults = null,
		$children = null,
		$bedtype = null,
		$mealtype = null,
		$mealprice = null,
		$perdayprice = null,
		$totalroomrate = null
	)
	{

		$totalguests = intval( $adults ) + intval( $children );
		$totalprice = array();

		$html = '<div id="booking-summary-wrap">';
		if ( '' !== $room_name ) {
			$html .= '<div class="room-summary"><span class="summary-room-name">'.$room_name.'</span></div>';
		}

		$html .= '<div class="main-summary-wrap">';
		if ($adults > 0) {
			for ($displayAdultCount = 0; $displayAdultCount < $adults; $displayAdultCount++) {
				$html .= '<span class="guest-adult-svg"></span>';
			}
		}
		if ($children > 0) {
			for ($displayChildrenCount = 0; $displayChildrenCount < $children; $displayChildrenCount++) {
				$html .= '<span class="guest-child-svg"></span>';
			}
		}
		if ( '' !== $bedtype ) {
			$html .= '<div class="bed-summary">'. self::get_AllBedLayouts($bedtype).'</div>';
		}
		$html .= '</div>';

		if ( '' !== $room_id ) {
			$html .= '<div class="meal-summary-wrap">';
			if ( '' !== self::generate_MealPlanIncluded($room_id) ) {
				$html .= '<div class="meal-summary"><span class="summary-mealtype-inlcuded">'.self::generate_MealPlanIncluded($room_id).'</span></div>';
			}
			if ( 'none' !== $mealtype ) {
				$html .= '<div class="summary-icon mealplan-summary-icon"><i class="fa-solid fa-utensils"></i></div>';
				$html .= '<div class="summary-heading mealplan-summary-heading">Mealplan:</div>';
				$html .= '<div class="meal-summary"><span class="summary-mealtype-name">'.self::getMealPlanText($mealtype).'</span></div>';
			}
			$html .= '</div>';
		}

		$html .= '<div class="stay-summary-wrap">';

		$html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
		$html .= '<div class="summary-heading checkin-summary-heading">Check-in:</div>';
		$html .= '<div class="checkin-summary">'.$checkin.'</div>';
		$html .= '<div class="summary-heading checkout-summary-heading">Check-out:</div>';
		$html .= '<div class="checkout-summary">'.$checkout.'</div>';

		$html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
		$html .= '<div class="summary-heading staynight-summary-heading">Nights:</div>';
		$html .= '<div class="staynight-summary">'.$staynights.'</div>';
		$html .= '</div>';

		if ( '' !== $totalroomrate) {
			$subtotalprice = intval( $totalroomrate ) + intval( $mealprice );
			$html .= '<div class="price-summary-wrap">';
			$html .= '<div class="summary-heading total-summary-heading">Subtotal:</div>';
			$html .= '<div class="price-summary">'.atollmatrix_price( $subtotalprice ).'</div>';
			$html .= '<div class="summary-heading total-summary-heading">Total:</div>';

			$totalprice = self::applyPriceTax( $subtotalprice, $staynights, $totalguests );
			foreach ($totalprice['details'] as $totalID => $totalvalue) {
				$html .= '<div class="tax-summary tax-summary-details">'. $totalvalue .'</div>';
			}
			
			$html .= '<div class="tax-summary tax-summary-total">'. atollmatrix_price( $totalprice['total'] ) .'</div>';
			$html .= '</div>';
		}

		if ( '' !== $room_id ) {
			$html .= '<div class="form-group">';
			$html .= '<div id="bookingResponse" class="booking-response"></div>';
			$html .= '<div id="booking-register" class="book-button">Book this room</div>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	public function deleteBookingTransient( $bookingNumber )
	{
		delete_transient($bookingNumber);
	}
	public function setBookingTransient($data, $bookingNumber)
	{
		error_log( '----- Saving Transisent -----');
		error_log( $bookingNumber );
		error_log( print_r($data, true) );
		set_transient($bookingNumber, $data, 20 * MINUTE_IN_SECONDS);
	}
	public function getBookingTransient($bookingNumber = null)
	{
		if ($bookingNumber === null) {
			// Use $this->bookingNumber if $bookingNumber is not supplied
			$bookingNumber = $this->bookingNumber;
		}
		
		return get_transient($bookingNumber);
	}  

	public function hotelBooking_SearchForm()
	{
		// Generate unique booking number
		self::setBookingTransient('1', $this->bookingNumber);
		ob_start();
		?>
		<div class="atollmatrix-content">
		<div id="hotel-booking-form">
			<form action="" method="post" id="hotel-booking">
				<div>
					<label for="reservation-date">Book Date:</label>
					<input type="date" id="reservation-date" name="reservation_date">
				</div>
				<div>
					<label for="number-of-adults">Number of Adults:</label>
					<input type="number" id="number-of-adults" name="number_of_adults" min="1">
				</div>
				<div class="children-number" data-agelimitofchild="13">
					<label for="number-of-children">Number of Children:</label>
					<input type="number" id="number-of-children" name="number_of_children" min="0">
				</div>
				<div id="bookingSearch" class="div-button">Search</div>
				<div class="recommended-alt-wrap">
					<div class="recommended-alt-title">Rooms unavailable</br>Please choose among following dates which has
						availability</div>
					<div id="recommended-alt-dates"></div>
				</div>

				<div class="available-list">
					<div id="available-list-ajax"></div>
				</div>
			</form>
		</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function alternative_BookingDates($checkinDate, $checkoutDate)
	{

		// Perform the greedy search by adjusting the check-in and check-out dates
		$newCheckinDate  = new \DateTime($checkinDate);
		$newCheckoutDate = new \DateTime($checkoutDate);
		//$newCheckoutDate->add(new \DateInterval('P1D'));

		$reservation_instance = new \AtollMatrix\Reservations();

		$availableRoomDates = $reservation_instance->Availability_of_Rooms_For_DateRange($newCheckinDate->format('Y-m-d'), $newCheckoutDate->format('Y-m-d'));

		// error_log('---- Alternative Rooms Matrix Early');
		// error_log(print_r($availableRoomDates, true));
		$new_room_availability_array = array();

		// Process each sub-array
		foreach ($availableRoomDates as $roomId => $subArray) {
			// Initialize the new sub-array for the current room
			$newSubArray = array();

			// Get the first and last keys of the inner arrays
			foreach ($subArray as $innerArray) {
				$keys     = array_keys($innerArray);
				$firstKey = $keys[0];
				$lastKey  = end($keys);

				// Keep only the first and last records and assign unique indexes
				$newSubArray[$firstKey] = array(
					'check-in'  => $firstKey,
					'check-out' => $lastKey,
				);
			}

			// Add the new sub-array to the new room availability array
			$new_room_availability_array[$roomId] = $newSubArray;
		}
		$roomAvailabityArray = $new_room_availability_array;

		// error_log('---- Alternative Room Availability Matrix Before');
		// error_log(print_r($roomAvailabityArray, true));

		// Initialize an empty string
		$output = '';

		$processedDates    = array(); // Array to store processed check-in and checkout dates
		$newProcessedDates = array();

		foreach ($roomAvailabityArray as $key => $subset) {
			// Output the key of the subset
			// error_log("Subset Key: $key\n");

			// Iterate through each sub array in the subset
			foreach ($subset as $subArray) {
				// Output the sub array
				// error_log(print_r($subArray, true));
				$checkInAlt = $subArray['check-in'];
				$staylast   = $subArray['check-out'];

				// Check if the current check-in and checkout dates have already been processed
				if (in_array([$checkInAlt, $staylast], $processedDates)) {
					//error_log( 'Skipping .... ' . $checkInAlt, $staylast);
					continue; // Skip processing identical dates
				}

				// Add the current check-in and checkout dates to the processed dates array
				$processedDates[] = [$checkInAlt, $staylast];

				// Get the date one day after the staylast
				$checkOutAlt = date('Y-m-d', strtotime($staylast . ' +1 day'));

				$newProcessedDates[$checkInAlt] = array(
					'staylast'  => $staylast,
					'check-in'  => $checkInAlt,
					'check-out' => $checkOutAlt,
				);

				// Perform operations with the sub array...
			}
		}

		// error_log('---- Alternative Room Availability Matrix The Final');
		// error_log(print_r($newProcessedDates, true));
		ksort($newProcessedDates);

		foreach ($newProcessedDates as $key) {
			$staylast    = $key['staylast'];
			$checkInAlt  = $key['check-in'];
			$checkOutAlt = $key['check-out'];

			// Format the dates as "Month Day" (e.g., "July 13th")
			$formattedFirstDate = date('F jS', strtotime($checkInAlt));

			$formattedNextDay = date('F jS', strtotime($checkOutAlt));
			if (date('F', strtotime($staylast)) !== date('F', strtotime($checkInAlt))) {
				$formattedNextDay = date('F jS', strtotime($checkOutAlt));
			} else {
				$formattedNextDay = date('jS', strtotime($checkOutAlt));
			}

			$output .= "<span data-check-staylast='{$staylast}' data-check-in='{$checkInAlt}' data-check-out='{$checkOutAlt}'>{$formattedFirstDate} - {$formattedNextDay}</span>, ";
		}

		// Remove the trailing comma and space
		$output = rtrim($output, ', ');

		// Print the output
		$roomAvailabity = '<div class="recommended-dates-wrap">' . $output . '</div>';
		// error_log('---- Alternative Room Availability Matrix for Range');
		// error_log(print_r($roomAvailabity, true));

		return $roomAvailabity;
	}

	public function booking_BookingSearch()
	{
		$room_type          = '';
		$number_of_children = 0;
		$number_of_adults   = 0;
		$number_of_guests   = 0;
		$children_age       = array();
		$reservation_date   = '';

		if (isset($_POST['reservation_date'])) {
			$reservation_date = $_POST['reservation_date'];
		}

		if (isset($_POST['number_of_adults'])) {
			$number_of_adults = $_POST['number_of_adults'];
		}

		if (isset($_POST['number_of_children'])) {
			$number_of_children = $_POST['number_of_children'];
		}

		// // *********** To be removed
		// $reservation_date = '2023-09-27 to 2023-09-30';
		// $number_of_adults = 1;
		// // ***********

		$freeStayAgeUnder = atollmatrix_get_option('childfreestay');

		// error_log('---- Free Stay');
		// error_log(print_r($freeStayAgeUnder, true));

		$freeStayChildCount = 0;

		if (isset($_POST['children_age'])) {
			// Loop through all the select elements with the class 'children-age-selector'
			foreach ($_POST['children_age'] as $selected_age) {
				// Sanitize and store the selected values in an array
				$children_age[] = sanitize_text_field($selected_age);
				if ($selected_age < $freeStayAgeUnder) {
					$freeStayChildCount = $freeStayChildCount + 1;
				}
			}
		}

		$this->children_age = $children_age;

		// error_log('---- Children Age');
		// error_log(print_r($this->children_age, true));

		$number_of_guests = intval($number_of_adults) + intval($number_of_children);

		$this->adultGuests           = $number_of_adults;
		$this->childrenGuests        = $number_of_children;
		$this->totalGuests           = $number_of_guests;
		$this->totalChargeableGuests = $number_of_guests - $freeStayChildCount;

		if (isset($_POST['room_type'])) {
			$room_type = $_POST['room_type'];
		}

		$chosenDate = \AtollMatrix\Common::splitDateRange($reservation_date);

		$checkinDate  = '';
		$checkoutDate = '';

		if (isset($chosenDate['startDate'])) {
			$checkinDate     = $chosenDate['startDate'];
			$checkinDate_obj = new \DateTime($chosenDate['startDate']);
		}
		if (isset($chosenDate['endDate'])) {
			$checkoutDate     = $chosenDate['endDate'];
			$checkoutDate = date('Y-m-d', strtotime($checkoutDate . ' +1 day'));
			$checkoutDate_obj = new \DateTime($checkoutDate);
		}

		// Calculate the number of nights
		$staynights = $checkinDate_obj->diff($checkoutDate_obj)->days;

		$this->checkinDate  = $checkinDate;
		$this->checkoutDate = $checkoutDate;
		$this->staynights   = $staynights;

		$this->bookingSearchResults                     = array();
		$this->bookingSearchResults['bookingnumber']    = $this->bookingNumber;
		$this->bookingSearchResults['checkin']          = $this->checkinDate;
		$this->bookingSearchResults['checkout']         = $this->checkoutDate;
		$this->bookingSearchResults['staynights']       = $this->staynights;
		$this->bookingSearchResults['adults']           = $this->adultGuests;
		$this->bookingSearchResults['children']         = $this->childrenGuests;
		$this->bookingSearchResults['children_age']     = $this->children_age;
		$this->bookingSearchResults['totalguest']       = $this->totalGuests;
		$this->bookingSearchResults['chargeableguests'] = $this->totalChargeableGuests;

		// Perform your query here, this is just an example
		$result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_adults, Number of Children: $number_of_children";
		// error_log(print_r($result, true));
		$room_instance = new \AtollMatrix\Rooms();

		// Get a combined array of rooms and rates which are available for the dates.
		$combo_array = $room_instance->getAvailable_Rooms_Rates_Occupants_For_DateRange($this->checkinDate, $this->checkoutDate);

		$this->roomArray     = $combo_array['rooms'];
		$this->ratesArray    = $combo_array['rates'];
		$this->canAccomodate = $combo_array['occupants'];

		// error_log('Value of $combo_array["rooms"]:');
		// error_log(print_r($combo_array['rooms'], true));

		$availableRoomDates = array();

		$roomAvailabity = false;

		if (count($combo_array['rooms']) == 0) {

			$roomAvailabity = self::alternative_BookingDates($checkinDate, $checkoutDate);
		}

		//set_transient($bookingNumber, $combo_array, 20 * MINUTE_IN_SECONDS);
		// error_log(print_r($combo_array, true));
		// error_log("Rooms array");
		// error_log(print_r($roomArray, true));
		// error_log("Date Range from picker");
		// error_log(print_r($checkinDate, true));
		// error_log(print_r($checkoutDate, true));

		// Always die in functions echoing AJAX content
		$list = self::list_Rooms_And_Quantities();

		ob_start();
		echo '<div id="reservation-data" data-bookingnumber="' . $this->bookingNumber . '" data-children="' . $this->childrenGuests . '" data-adults="' . $this->adultGuests . '" data-guests="' . $this->totalGuests . '" data-checkin="' . $this->checkinDate . '" data-checkout="' . $this->checkoutDate . '">';
		echo $list;
		echo '</div>';
		echo self::register_Guest_Form();
		echo self::paymentHelper_Form($this->bookingNumber);
		$output                     = ob_get_clean();
		$response['booking_data']   = $combo_array;
		$response['roomlist']       = $output;
		$response['alt_recommends'] = $roomAvailabity;
		echo json_encode($response, JSON_UNESCAPED_SLASHES);
		die();
	}

	public function list_Rooms_And_Quantities()
	{
		// error_log('====== rates ');
		// error_log(print_r($combo_array, true));
		// Initialize empty string to hold HTML
		$html = '';

		$html .= self::listRooms();

		// Return the resulting HTML string
		return $html;
	}

	public function can_ThisRoom_Accomodate($room_id)
	{

		$status = true;
		if ($this->canAccomodate[$room_id]['guests'] < $this->totalGuests) {
			// error_log('Cannot accomodate number of guests');
			$status = false;
		}

		if ($this->canAccomodate[$room_id]['adults'] < $this->adultGuests) {
			// error_log('Cannot accomodate number of adults');
			$status = false;
		}
		if ($this->canAccomodate[$room_id]['children'] < $this->childrenGuests) {
			// error_log('Cannot accomodate number of children');
			$status = false;
		}

		return $status;
	}

	public function listRooms()
	{

		$html  = '';
		$count = 0;
		// Iterate through each room
		foreach ($this->roomArray as $id => $room_info) {

			$room_data = get_post_custom($id);

			// Get quantity and room title
			// error_log('====== can accomodate ');
			// error_log(print_r($this->canAccomodate, true));

			$can_ThisRoom_Accomodate = self::can_ThisRoom_Accomodate($id);
			if (!$can_ThisRoom_Accomodate) {
				continue;
			}

			$max_guest_number       = intval($this->canAccomodate[$id]['guests']);
			$max_child_guest_number = intval($this->canAccomodate[$id]['guests'] - 1);
			// Append a div for the room with the room ID as a data attribute
			$html .= '<div class="room-occupied-group" data-adults="' . $this->canAccomodate[$id]['adults'] . '" data-children="' . $this->canAccomodate[$id]['children'] . '" data-guests="' . $this->canAccomodate[$id]['guests'] . '" data-room-id="' . $id . '">';
			$html .= '<div class="room-details">';

			foreach ($room_info as $quantity => $title) {

				$html .= '<div class="room-details-row">';
				$html .= '<div class="room-details-column">';

				$html .= '<div class="room-details-image">';
				$image_id = get_post_thumbnail_id( $id );
				$image_url = wp_get_attachment_image_url( $image_id, 'atollmatrix-large-square' ); // Get the URL of the custom-sized image
				$html .= '<img class="room-summary-image" src="' . esc_url( $image_url ) . '" alt="Room featured image">';
				$html .= '</div>';

				$html .= '<div class="room-details-stats">';

				if (isset($room_data["atollmatrix_roomview"][0])) {
					$roomview = $room_data["atollmatrix_roomview"][0];
					$roomview_array = atollmatrix_get_room_views();
					if ( array_key_exists($roomview, $roomview_array)) {
						$html .= '<div class="room-summary-roomview"><span class="room-summary-icon"><i class="fa-regular fa-eye"></i></span>'.$roomview_array[$roomview].'</div>';
					}
				}

				if (isset($room_data["atollmatrix_room_size"][0])) {
					$roomsize = $room_data["atollmatrix_room_size"][0];
					$html .= '<div class="room-summary-roomsize"><span class="room-summary-icon"><i class="fa-solid fa-vector-square"></i></span>'.$roomsize.' ft²</div>';
				}
				$html .= '</div>';

				$html .= '<div class="room-details-heading">';
				// Append the room title

				$this->bookingSearchResults[$id]['roomtitle'] = $title;

				$html .= '<h2>' . $title . '</h2>';
		
				$html .= '</div>';

				if (isset($room_data["atollmatrix_room_desc"][0])) {
					$room_desc = $room_data["atollmatrix_room_desc"][0];
					$html .= '<div class="room-summary-roomdesc">'.$room_desc.'</div>';
				}

				$html .= '<div class="room-details-facilities">';
				if (isset($room_data["atollmatrix_room_facilities"][0])) {
					$room_facilities = $room_data["atollmatrix_room_facilities"][0];
					$html .= atollmatrix_string_to_html_spans( $room_facilities, $class='room-summary-facilities');
				}
				$html .= '</div>';

				$html .= '</div>';
				$html .= '<div class="room-details-column">';
				
				if ($this->adultGuests > 0) {
					for ($displayAdultCount = 0; $displayAdultCount < $this->adultGuests; $displayAdultCount++) {
						$html .= '<span class="guest-adult-svg"></span>';
					}
				}
				if ($this->childrenGuests > 0) {
					for ($displayChildrenCount = 0; $displayChildrenCount < $this->childrenGuests; $displayChildrenCount++) {
						$html .= '<span class="guest-child-svg"></span>';
					}
				}

				// // Append a select element for the quantity
				// $html .= '<select data-room-id="' . $id . '" name="room_quantity">';
				// // Append an option for each possible quantity
				// for ($i = 0; $i <= $quantity; $i++) {
				//     $html .= '<option value="' . $i . '">' . $i . '</option>';
				// }
				// $html .= '</select>';
				$html .= '<div class="checkin-staydate-wrap">';

				$total_roomrate                                   = self::displayBookingTotal($id);
				$this->bookingSearchResults[$id]['totalroomrate'] = $total_roomrate;

				$html .= '<div class="room-price-total" data-roomprice="' . esc_attr($total_roomrate) . '">' . atollmatrix_price($total_roomrate) . '</div>';
				$html .= self::generate_MealPlanIncluded($id);
				
				$html .= '<div class="roomchoice-selection">';
				
				$html .= '<div class="roomchoice-seperator roomchoice-bedlayout">';
				$html .= '<div class="bedlayout-wrap">';
				$html .= '<div class="room-summary-heading room-bedlayout-heading" for="room-number-input">Bed Layout</div>';
				$html .= '</div>';
				$html .= '<input class="roomchoice" name="room[' . $id . '][quantity]" type="hidden" data-type="room-number" data-roominputid="' . $id . '" data-roomqty="' . $quantity . '" id="room-input-' . $id . '" min="0" max="' . $quantity . '" value="1">';
				$html .= self::generate_BedInformation($id);
				$html .= '</div>';
			
				$html .= '<div class="roomchoice-seperator roomchoice-mealplan">';
				$html .= self::generate_MealPlanRadio($id);
				$html .= '</div>';

				$html .= '<div class="room-button-wrap">';
				$html .= '<div data-room-button-id="' . $id . '" id="booking-register" class="book-button">'. __('Choose this room','atollmatrix') . '</div>';
				$html .= '</div>';

				$html .= '</div>';

				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

			}

			$html .= '<div class="stay-summary-wrap">';
			$html .= '<div class="checkin-summary">Check-in: '. atollmatrix_readableDate( $this->checkinDate ).'</div>';
			$html .= '<div class="checkout-summary">Check-out: '.atollmatrix_readableDate( $this->checkoutDate ).'</div>';
			$html .= '<div class="staynight-summary">Nights: '.$this->staynights.'</div>';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</div>';

			// error_log( print_r( $this->bookingSearchResults , true ));
			self::setBookingTransient( $this->bookingSearchResults, $this->bookingNumber );
		}

		return $html;
	}

	public function displayBookingPerDay($ratesArray_date)
	{
		$total_roomrate = 0;
		$html           = '';
		foreach ($ratesArray_date as $staydate => $roomrate) {
				$html .= '<div class="checkin-staydate"><span class="number-of-rooms"></span>' . $staydate . ' - ' . atollmatrix_price($roomrate) . '</div>';

			$roomrate = self::applyPricePerPerson($roomrate);

			$total_roomrate = $total_roomrate + $roomrate;
		}

		return $html;
	}
	public function displayBookingTotal($room_id)
	{
		$total_roomrate = 0;
		$html           = '';

		// error_log('---- Person Pricing');
		// error_log(print_r($perPersonPricing, true));

		foreach ($this->ratesArray[$room_id]['date'] as $staydate => $roomrate) {

			$roomrate = self::applyPricePerPerson($roomrate);

			$this->bookingSearchResults[$room_id]['staydate'][$staydate] = $roomrate;

			$total_roomrate = $total_roomrate + $roomrate;
		}

		return $total_roomrate;
	}

	private function applyPricePerPerson($roomrate)
	{

		$perPersonPricing = atollmatrix_get_option('perpersonpricing');

		foreach ($perPersonPricing as $pricing) {
			if ($this->totalChargeableGuests == $pricing['people']) {
				if ($pricing['type'] === 'percentage' && $pricing['total'] === 'decrease') {
					// Decrease the rate by the given percentage
					$roomrate -= ($roomrate * $pricing['number'] / 100);
				} elseif ($pricing['type'] === 'fixed' && $pricing['total'] === 'increase') {
					// Increase the rate by the fixed amount
					$roomrate += $pricing['number'];
				} elseif ($pricing['type'] === 'percentage' && $pricing['total'] === 'increase') {
					// Increase the rate by the given percentage
					$roomrate += ($roomrate * $pricing['number'] / 100);
				} elseif ($pricing['type'] === 'fixed' && $pricing['total'] === 'decrease') {
					// Decrease the rate by the fixed amount
					$roomrate -= $pricing['number'];
				}
			}
		}

		return $roomrate;
	}

	private function applyPriceTax($roomrate, $nights, $guests)
	{

		$price = array();
		$count = 0;
		$taxPricing = atollmatrix_get_option('taxes');

		foreach ($taxPricing as $tax) {
			$percentage = '';
			if ( $tax['type'] === 'percentage' ) {
				$percentage = $tax['number'] . '%';
				if ( $tax['duration'] === 'inrate') {
					// Decrease the rate by the given percentage
					$total = $roomrate * ( $tax['number'] / 100 );
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perperson') {
					// Increase the rate by the fixed amount
					$total = $guests * ($roomrate * $tax['number'] / 100);
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perday') {
					// Increase the rate by the given percentage
					$total = $nights * ($roomrate * $tax['number'] / 100);
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perpersonperday') {
					// Increase the rate by the given percentage
					$total = $nights * ( $guests * ($roomrate * $tax['number'] / 100) );
					$roomrate += $total;
				}                
			}
			if ( $tax['type'] === 'fixed' ) {
				if ( $tax['duration'] === 'inrate') {
					// Decrease the rate by the given percentage
					$total = $tax['number'];
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perperson') {
					// Increase the rate by the fixed amount
					$total = $guests * $tax['number'];
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perday') {
					// Increase the rate by the given percentage
					$total = $nights * $tax['number'];
					$roomrate += $total;
				} elseif ( $tax['duration'] === 'perpersonperday') {
					// Increase the rate by the given percentage
					$total = $nights * ( $guests * $tax['number'] );
					$roomrate += $total;
				}
			}
			$price['details'][$count] = '<span class="tax-value">' . atollmatrix_price($total) . '</span> - <span class="tax-label" data-number="'.$tax['number'].'" data-type="'.$tax['type'].'" data-duration="'.$tax['duration'].'">' . $percentage . ' ' . $tax['label'] . '</span>';
			$count++;
		}

		$price['total'] = $roomrate;

		return $price;
	}

	public function generate_BedInformation($room_id)
	{

		$html = '';

		$room_data = get_post_custom($room_id);

		if (isset($room_data["atollmatrix_alt_bedsetup"][0])) {
			$bedsetup       = $room_data["atollmatrix_alt_bedsetup"][0];
			$bedsetup_array = unserialize($bedsetup);

			$firstRoomId = array_key_first($bedsetup_array);

			foreach ($bedsetup_array as $roomId => $roomData) {
				// Get the bed layout for this room
				
				$bedLayout = '';
				$bedCount = 0;
				foreach ($roomData['bedtype'] as $bedFieldID => $bedName) {
					$bedQty = $roomData['bednumber'][$bedFieldID];
					if ( $bedCount > 0 ) {
						$bedLayout .= ' ';
					}
					for ($i=0; $i < $bedQty; $i++) { 
						if ( $i > 0 ) {
							$bedLayout .= ' ';
						}
						$bedLayout .= $bedName;
					}
					$bedCount++;
				}

				$this->bookingSearchResults[$room_id]['bedlayout'][sanitize_title($bedLayout)] = true;

				$html .= "<label for='room-$room_id-bedlayout-$bedLayout'>";
				$html .= "<input type='radio' id='room-$room_id-bedlayout-$bedLayout' name='room[$room_id][bedlayout]' value='$bedLayout'";

				// Check the first radio input by default
				if ($roomId === $firstRoomId) {
					$html .= " checked";
				}

				$html .= '>';
				$html .= '<span class="checkbox-label checkbox-bed-label">';
				$html .= '<div class="guest-bed-wrap guest-bed-' . sanitize_title($bedLayout) . '-wrap">';
				foreach ($roomData['bedtype'] as $bedFieldID => $bedName) {

					$bedQty = $roomData['bednumber'][$bedFieldID];
					for ($i=0; $i < $bedQty; $i++) { 
						$html .= self::get_BedLayout( $bedName, $bedFieldID . '-' . $i );
					}
				}
				$html .= '</div>';
				$html .= '</span>';
				$html .= '</label>';
			}
		}

		return $html;
	}

	public function get_AllBedLayouts( $bedNames )
	{
		$html = '';
		$bedNames_array = explode( ' ', $bedNames );
		foreach ($bedNames_array as $key => $bedName) {
			$html .= self::get_BedLayout( $bedName, $key );
		}

		return $html;
	}

	public function get_BedLayout( $bedName, $bedFieldID = null )
	{
		
		switch ($bedName) {
			case 'kingbed':
				$html = '<div class="guest-bed guest-bed-' . sanitize_title($bedName) . '"></div>';
				break;
			case 'twinbed':
				$html = '<div class="guest-bed type-twinbed-twinbed-'.$bedFieldID.' guest-bed-' . $bedName . '"></div>';
				break;
		}

		return $html;
	}

	public function paymentHelper_Form()
	{
		$form_html = <<<HTML
			<form action="" method="post" id="paymentForm">
				<!-- Other form fields -->
				<input type="hidden" name="total" id="totalField" value="100">
				<input type="hidden" name="bookingNumber" id="bookingNumber" value="$this->bookingNumber">
				<div id="bookingPayment" class="div-button">Pay</div>
			</form>
HTML;
		return $form_html;
	}

	public function register_Guest_Form()
	{
		$country_options = atollmatrix_country_list("select", "");

		$html = '<div class="registration-column registration-column-two" id="booking-summary">';
		$html .= self::bookingSummary(
			$room_id = '',
			$booking_results[$room_id]['roomtitle'] = '',
			$this->checkinDate,
			$this->checkoutDate,
			$this->staynights,
			$this->adultGuests,
			$this->childrenGuests,
			$bedlayout = '',
			$mealplan = '',
			$choice = '',
			$perdayprice = '',
			$total = ''
		);
		$html .= '</div>';

		$form_html = <<<HTML
		<div class="registration_form_outer">
			<div class="booking-backto-roomschoice">Back to Room Choice</div>
			<div class="registration_form_wrap">
				<div class="registration_form">
				<form action="" method="post" id="guest-registration">

					<div class="registration-column registration-column-one registration_form_inputs">
					<div class="form-group">
						<input type="text" class="form-control" id="full_name" name="full_name" >
						<label for="full_name" class="control-label">Full Name</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="passport" name="passport" >
						<label for="passport" class="control-label">Passport No:</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="email" class="form-control" id="email_address" name="email_address" >
						<label for="email_address" class="control-label">Email Address</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="tel" class="form-control" id="phone_number" name="phone_number" >
						<label for="phone_number" class="control-label">Phone Number</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="street_address" name="street_address" >
						<label for="street_address" class="control-label">Street Address</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="city" name="city" >
						<label for="city" class="control-label">City</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="state" name="state">
						<label for="state" class="control-label">State/Province</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="zip_code" name="zip_code">
						<label for="zip_code" class="control-label">Zip Code</label><i class="bar"></i>
					</div>
					<div class="form-group">
						<select class="form-control" id="country" name="country" >
						$country_options
						</select>
						<label for="country" class="control-label">Country</label><i class="bar"></i>
					</div>
					<div class="form-group">
					<textarea class="form-control" id="guest_comment" name="guest_comment"></textarea>
					<label for="textarea" class="control-label">Textarea</label><i class="bar"></i>
					</div>
					<div class="checkbox">
					<label>
						<input type="checkbox" id="guest_consent" name="guest_consent" /><i class="helper"></i>I'm the label from a checkbox
					</label>
					</div>
				</form>
				</div>

				$html
				</div>
			</div>
		</div>
HTML;

		return $form_html;
	}

	public function generate_MealPlanIncluded($room_id)
	{

		$mealPlans = atollmatrix_get_option('mealplan');

		if (is_array($mealPlans) && count($mealPlans) > 0) {
			$includedMealPlans = array();
			$optionalMealPlans = array();

			foreach ($mealPlans as $id => $plan) {
				if ($plan['choice'] === 'included') {
					$includedMealPlans[$id] = $plan;
				} elseif ($plan['choice'] === 'optional') {
					$optionalMealPlans[$id] = $plan;
				}
			}

			$html = '';
			if (is_array($includedMealPlans) && count($includedMealPlans) > 0) {
				$html .= '<div class="room-included-meals">';
				foreach ($includedMealPlans as $id => $plan) {
					$html .= '<i class="fa-solid fa-square-check"></i>';
					$html .= self::getMealPlanText($plan['mealtype']) . __(' included.', 'atollmatrix');
					$html .= '<label>';
					$html .= '<input hidden type="text" name="room[' . $room_id . '][meal_plan][included]" value="' . $plan['mealtype'] . '">';
					$html .= '</label>';
					$this->bookingSearchResults[$room_id]['meal_plan'][$plan['mealtype']] = 'included';
				}
				$html .= '</div>';
			}
		}
		return $html;
	}

	public function generate_MealPlanRadio($room_id)
	{

		$mealPlans = atollmatrix_get_option('mealplan');

		if (is_array($mealPlans) && count($mealPlans) > 0) {
			$includedMealPlans = array();
			$optionalMealPlans = array();

			foreach ($mealPlans as $id => $plan) {
				if ($plan['choice'] === 'included') {
					$includedMealPlans[$id] = $plan;
				} elseif ($plan['choice'] === 'optional') {
					$optionalMealPlans[$id] = $plan;
				}
			}

			$html = '';
			if (is_array($optionalMealPlans) && count($optionalMealPlans) > 0) {
				$html .= '<div class="room-mealplans">';
				$html .= '<div class="mealplans-wrap">';
				$html .= '<div class="room-summary-heading room-mealplans-heading">Mealplans</div>';
				$html .= '<div class="room-mealplan-input-wrap">';
				$html .= '<div class="room-mealplan-input">';
				$html .= '<label for="room-' . $room_id . '-meal_plan-optional-none">';
				$html .= '<input class="mealtype-input" type="radio" data-mealprice="none" id="room-' . $room_id . '-meal_plan-optional-none" name="room[' . $room_id . '][meal_plan][optional]" value="none" checked><span class="checkbox-label">' . __('Not selected', 'atollmatrix') . '</span>';
				$html .= '</label>';
				$html .= '</div>';
				foreach ($optionalMealPlans as $id => $plan) {
					$html .= '<div class="room-mealplan-input">';
					$mealprice = $plan['price'] * $this->staynights;
					$html .= '<label for="room-' . $room_id . '-meal_plan-optional-' . $plan['mealtype'] . '">';
					$html .= '<input class="mealtype-input" type="radio" data-mealprice="' . $mealprice . '" id="room-' . $room_id . '-meal_plan-optional-' . $plan['mealtype'] . '" name="room[' . $room_id . '][meal_plan][optional]" value="' . $plan['mealtype'] . '"><span class="room-mealplan-price checkbox-label">' . atollmatrix_price($mealprice) . '<span class="mealplan-text">' . self::getMealPlanText($plan['mealtype']) . '</span></span>';
					$html .= '</label>';
					$this->bookingSearchResults[$room_id]['meal_plan'][$plan['mealtype']] = $mealprice;

					$html .= '</div>';
				}
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		return $html;
	}

	public function getMealPlanText($mealtype)
	{
		switch ($mealtype) {
			case 'BB':
				return __('Breakfast', 'atollmatrix');
			case 'HB':
				return __('Halfboard', 'atollmatrix');
			case 'FB':
				return __('Fullboard', 'atollmatrix');
			case 'AN':
				return __('All inclusive', 'atollmatrix');
			default:
				return '';
		}
	}
}

$instance = new \AtollMatrix\Booking();
