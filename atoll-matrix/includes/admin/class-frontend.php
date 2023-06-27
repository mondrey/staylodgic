<?php
namespace AtollMatrix;
class Frontend {

	public function __construct() {
		add_shortcode('hotel_booking_search', array($this,'hotelBooking_SearchForm'));
		// AJAX handler to save room metadata

		add_action('wp_ajax_frontend_BookingSearch', array($this,'frontend_BookingSearch'));
		add_action('wp_ajax_nopriv_frontend_BookingSearch', array($this,'frontend_BookingSearch'));
	}
	
	public function hotelBooking_SearchForm() {
		// Generate unique booking number
		$booking_number = uniqid();
		set_transient( $booking_number, '1', 20 * MINUTE_IN_SECONDS );
		ob_start();
		?>
		<div id="hotel-booking-form">
			<form action="" method="post" id="hotel-booking">
				<div>
					<input type="text" id="booking-number" value="<?php echo $booking_number; ?>" name="booking_number">
				</div>
				<div>
					<label for="reservation-date">Book Date:</label>
					<input type="date" id="reservation-date" name="reservation_date">
				</div>
				<div>
					<label for="number-of-guests">Number of Adults:</label>
					<input type="number" id="number-of-guests" name="number_of_guests" min="1">
				</div>
				<div class="children-number" data-agelimitofchild="13">
					<label for="number-of-children">Number of Children:</label>
					<input id="number-of-children" name="number_of_children" min="0">
				</div>
				<div id="bookingSearch" class="div-button">Search</div>

				<div class="available-list">
					<div id="available-list-ajax"></div>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
	
	function frontend_BookingSearch() {
		$room_type = '';
		$number_of_children = '';
		$number_of_guests = '';
		$reservation_date = '';
		$booking_number = '';
		if (isset($_POST['booking_number'])) {
			$booking_number = $_POST['booking_number'];
		}
	
		if (isset($_POST['reservation_date'])) {
			$reservation_date = $_POST['reservation_date'];
		}
	
		if (isset($_POST['number_of_guests'])) {
			$number_of_guests = $_POST['number_of_guests'];
		}
	
		if (isset($_POST['number_of_children'])) {
			$number_of_children = $_POST['number_of_children'];
		}
	
		if (isset($_POST['room_type'])) {
			$room_type = $_POST['room_type'];
		}
	
		$chosenDate = \AtollMatrix\Common::splitDateRange($reservation_date);
	
		$checkinDate = '';
		$checkoutDate = '';
	
		if ( isset( $chosenDate['startDate'] ) ) {
			$checkinDate = $chosenDate['startDate'];
		}
		if ( isset( $chosenDate['endDate'] ) ) {
			$checkoutDate = $chosenDate['endDate'];
		}
	
		// Perform your query here, this is just an example
		$result = "Check-in Date: $checkinDate, Check-out Date: $checkoutDate, Number of Adults: $number_of_guests, Number of Children: $number_of_children";
	
		$room_instance = new \AtollMatrix\Rooms();
		$room_array = $room_instance->getAvailableRooms_For_DateRange($checkinDate, $checkoutDate);
		// Always die in functions echoing AJAX content
		$list = self::listRooms_And_Quantities($room_array);
		ob_start();
		echo '<div id="reservation-data" data-bookingnumber="' . $booking_number . '" data-children="' . $number_of_children . '" data-adults="' . $number_of_guests . '" data-checkin="' . $checkinDate . '" data-checkout="' . $checkoutDate . '">';
		echo $list;
		echo self::register_Guest_Form();
		echo '<div id="bookingResponse" class="booking-response"></div>';
		echo self::paymentHelper_Form($booking_number);
		$output = ob_get_clean();
		echo $output;
		die();
	}
	
	
	public function listRooms_And_Quantities($room_array) {
		// Initialize empty string to hold HTML
		$html = '';
		
		// Iterate through each room
		foreach ($room_array as $id => $room_info) {
			// Get quantity and room title
			foreach ($room_info as $quantity => $title) {
				// Append a div for the room with the room ID as a data attribute
				$html .= '<div data-room-id="' . $id . '">';
				// Append the room title
				$html .= '<h2>' . $title . '</h2>';
				// Append a select element for the quantity
				$html .= '<select name="room_quantity">';
				// Append an option for each possible quantity
				for ($i = 0; $i <= $quantity; $i++) {
					$html .= '<option value="' . $i . '">' . $i . '</option>';
				}
				$html .= '</select>';
				$html .= '</div>';
			}
		}
		
		// Return the resulting HTML string
		return $html;
	}
	
	public function paymentHelper_Form( $booking_number ){
		$form_html = <<<HTML
			<form action="" method="post" id="paymentForm">
				<!-- Other form fields -->
				<input type="hidden" name="total" id="totalField" value="100">
				<input type="hidden" name="booking_number" id="booking_number" value="$booking_number">
				<div id="bookingPayment" class="div-button">Pay</div>
			</form>
		HTML;
		return $form_html;
	}
	
	public function register_Guest_Form() {
		$country_options = atollmatrix_country_list("select", "");
	
		$form_html = <<<HTML
		<div class="registration_form">
			<div class="form-group">
				<label for="full_name">Full Name</label>
				<input type="text" class="form-control" id="full_name" name="full_name" >
			</div>
			<div class="form-group">
				<label for="email_address">Email Address</label>
				<input type="email" class="form-control" id="email_address" name="email_address" >
			</div>
			<div class="form-group">
				<label for="phone_number">Phone Number</label>
				<input type="tel" class="form-control" id="phone_number" name="phone_number" >
			</div>
			<div class="form-group">
				<label for="street_address">Street Address</label>
				<input type="text" class="form-control" id="street_address" name="street_address" >
			</div>
			<div class="form-group">
				<label for="city">City</label>
				<input type="text" class="form-control" id="city" name="city" >
			</div>
			<div class="form-group">
				<label for="state">State/Province</label>
				<input type="text" class="form-control" id="state" name="state">
			</div>
			<div class="form-group">
				<label for="zip_code">Zip Code</label>
				<input type="text" class="form-control" id="zip_code" name="zip_code">
			</div>
			<div class="form-group">
				<label for="country">Country</label>
				<select class="form-control" id="country" name="country" >
				$country_options
				</select>
			</div>
			<div class="form-group">
				<div id="bookingRegister" class="div-button">Book</div>
			</div>
		</div>
	HTML;
	
		return $form_html;
	}
}

$instance = new \AtollMatrix\Frontend();
