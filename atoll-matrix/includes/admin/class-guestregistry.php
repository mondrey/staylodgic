<?php
namespace AtollMatrix;

class GuestRegistry
{

    protected $bookingNumber;

    public function __construct(
        $bookingNumber = null
    ) {
        $this->bookingNumber = uniqid();

        add_shortcode('guest_registration', array($this, 'guestRegistration'));

        add_action('wp_ajax_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));
        add_action('wp_ajax_nopriv_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));
    }

    public function registrationSuccessful()
    {

        $reservation_instance = new \AtollMatrix\Reservations();
        $booking_page_link    = $reservation_instance->getBookingDetailsPageLinkForGuest();

        $booking_details_link = '<a href="' . esc_attr(esc_url(get_page_link($booking_page_link))) . '">Booking Details</a>';

        $success_html = <<<HTML
		<div class="registration_form_outer registration_successful">
			<div class="registration_form_wrap">
				<div class="registration_form">
        <div class="registration-successful-inner">
            <h3>Booking Successful</h3>
            <p>
                Hi,
            </p>
            <p>
                Your booking number is: <span class="booking-number">$this->bookingNumber</span>
            </p>
            <p>
                Please contact us to cancel, modify or if there's any questions regarding the booking.
            </p>
            <p>
                <div id="booking-details" class="book-button not-fullwidth">$booking_details_link</div>
            </p>
        </div>
        </div>
        </div>
        </div>
HTML;

        return $success_html;
    }

    public function bookingDataFields()
    {
        $dataFields = [
            'full_name'      => 'Full Name',
            'passport'       => 'Passport No',
            'email_address'  => 'Email Address',
            'phone_number'   => 'Phone Number',
            'country'        => 'Country',
            'guest_consent'  => 'By clicking "Book this Room" you agree to our terms and conditions and privacy policy.',
         ];

        return $dataFields;
    }

    public function guestRegistrationForm( $booking_number )
    {
        $country_options = atollmatrix_country_list("select", "");

        $html = '<div class="registration-column registration-column-two" id="booking-summary">';
        $html .= '<p>Please enter registration details</p>';
        $html .= '</div>';

        $registrationSuccess = self::registrationSuccessful();

        $formInputs = self::bookingDataFields();

        $reservation_instance = new \AtollMatrix\Reservations();
        $reservation_id    = $reservation_instance->getReservationIDforBooking($booking_number);
        
        $reservation_instance = new \AtollMatrix\Reservations( $date = false, $room_id = false, $reservation_id);
        $numberOfOccupants = $reservation_instance->getTotalOccupantsForReservation($reservation_id);

        $form_loop = '';
        for ($i = 1; $i <= $numberOfOccupants; $i++) {
            $form_loop .= <<<HTML
                            <h4>Guest {$i}</h4>
                            <div class="form-group form-floating">
                                <input placeholder="Full Name" type="text" class="form-control" id="full_name{$i}" name="full_name{$i}" required>
                                <label for="full_name{$i}" class="control-label">{$formInputs['full_name']}</label>
                            </div>
                            <div class="form-group form-floating">
                                <input placeholder="Passport No." type="text" class="form-control" id="passport{$i}" name="passport{$i}" required>
                                <label for="passport{$i}" class="control-label">{$formInputs['passport']}</label>
                            </div>
                            <div class="form-group form-floating">
                                <select required placeholder="" class="form-control" id="country{$i}" name="country{$i}">
                                {$country_options}
                                </select>
                                <label for="country{$i}" class="control-label">{$formInputs['country']}</label>
                            </div>
                            <!-- Add any additional fields here -->
            HTML;
        }

        $form_html = <<<HTML
		<div class="guest_registration_form_outer registration_request">
			<div class="guest_registration_form_wrap">
				<div class="registration_form">
					<div class="registration-column registration-column-one registration_form_inputs">
                    <h3>Registration</h3>
                    
                    $form_loop

					<div class="form-group form-floating">
						<input placeholder="" type="email" class="form-control" id="email_address" name="email_address" required>
						<label for="email_address" class="control-label">$formInputs[email_address]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="" type="tel" class="form-control" id="phone_number" name="phone_number" required>
						<label for="phone_number" class="control-label">$formInputs[phone_number]</label>
					</div>

					<div class="checkbox guest-consent-checkbox">
					<label for="guest_consent">
						<input type="checkbox" class="form-check-input" id="guest_consent" name="guest_consent" required /><span class="consent-notice">$formInputs[guest_consent]</span>
                        <div class="invalid-feedback">
                            Consent is required for booking.
                        </div>
                    </label>
					</div>
				</div>

				$html

				</div>
			</div>
		</div>
HTML;

        return $form_html . $registrationSuccess;
    }

    public function requestRegistrationDetails($booking_number) {
        $booking_number = $_POST['booking_number'];
    
        // Fetch reservation details
        $reservation_instance = new \AtollMatrix\Reservations();
        $reservationQuery = $reservation_instance->getReservationforBooking($booking_number);

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_bookingdetails_nonce' ]) || !check_admin_referer('atollmatrix-bookingdetails-nonce', 'atollmatrix_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
    
        ob_start(); // Start output buffering
    
        if ($reservationQuery->have_posts()) {
            echo "<div class='reservation-details'>";
            while ($reservationQuery->have_posts()) {
                $reservationQuery->the_post();
                $reservationID = get_the_ID();
    
                // Display reservation details
                echo "<h3>Reservation ID: " . esc_html($reservationID) . "</h3>";
                echo "<p>Check-in Date: " . esc_html(get_post_meta($reservationID, 'atollmatrix_checkin_date', true)) . "</p>";
                echo "<p>Check-out Date: " . esc_html(get_post_meta($reservationID, 'atollmatrix_checkout_date', true)) . "</p>";
                // Add other reservation details as needed
            }
            echo "</div>";
        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }
    
        // Fetch guest details
        $guestID = $reservation_instance->getGuest_id_forReservation($booking_number);
        if ($guestID) {
            echo "<div class='guest-details'>";
            echo "<h3>Guest Information:</h3>";
            echo "<p>Guest ID: " . esc_html($guestID) . "</p>";
            echo "<p>Full Name: " . esc_html(get_post_meta($guestID, 'atollmatrix_full_name', true)) . "</p>";
            echo "<p>Email Address: " . esc_html(get_post_meta($guestID, 'atollmatrix_email_address', true)) . "</p>";
            // Add other guest details as needed
            echo "</div>";
        } else {
            echo "<p>No guest details found for Booking Number: " . esc_html($booking_number) . "</p>";
        }

        echo $this->guestRegistrationForm( $booking_number );
    
        $informationSheet = ob_get_clean(); // Get the buffer content and clean the buffer
        echo $informationSheet; // Directly output the HTML content
        wp_die(); // Terminate and return a proper response
    }

    public function guestRegistration()
    {
        ob_start();
        $atollmatrix_bookingdetails_nonce = wp_create_nonce('atollmatrix-bookingdetails-nonce');
        ?>
		<div class="atollmatrix-content">
            <div id="hotel-booking-form">

            <div class="calendar-insights-wrap">
                <div id="check-in-display">Check-in: <span>-</span></div>
                <div id="check-out-display">Check-out: <span>-</span></div>
                <div id="last-night-display">Last-night: <span>-</span></div>
                <div id="nights-display">Nights: <span>-</span></div>
            </div>

                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="atollmatrix_bookingdetails_nonce" value="<?php echo esc_attr($atollmatrix_bookingdetails_nonce); ?>" />
                                <input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label">Booking No.</label>
                            </div>
                        </div>
                        <div data-request="guestregistration" id="bookingDetails" class="div-button">Search</div>
                    </div>
                </div>

			<div class="guestregistration-details-lister">
				<div id="guestregistration-details-ajax"></div>
			</div>
		</div>
		</div>
		<?php
return ob_get_clean();
    }

}

$instance = new \AtollMatrix\GuestRegistry();