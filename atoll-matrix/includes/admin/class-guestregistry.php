<?php
namespace AtollMatrix;

use WPCF7_Submission;

class GuestRegistry
{

    protected $bookingNumber;
    private static $idLabelMap = [];

    public function __construct(
        $bookingNumber = null
    ) {
        $this->bookingNumber = uniqid();

        add_shortcode('guest_registration', array($this, 'guestRegistration'));
        
        add_action('wpcf7_init', array($this, 'register_cf7_signature_tag'));
        // add_shortcode('atollmatrix_cf7_digitalsignature', array($this, 'atollmatrix_cf7_digital_signature_shortcode'));

        add_action('wp_ajax_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));
        add_action('wp_ajax_nopriv_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));

        add_action('wpcf7_before_send_mail', array($this, 'capture_form_data_with_placeholders'));

        //add_shortcode('cf7_id_label_map', array($this, 'cf7_id_label_map_shortcode'));
        add_action('wpcf7_init', array($this, 'register_cf7_id_label_map_tag'));
    }
    
    public function register_cf7_id_label_map_tag() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_add_form_tag('atollmatrix_cf7_id_label_map', array($this, 'cf7_id_label_map_handler'));
        }
    }

    function cf7_id_label_map_handler($tag)
    {
        // Extract the values from the attr field
        $attr_values = shortcode_parse_atts($tag->attr);
        $id = $attr_values['id'] ?? '';
        $label = $attr_values['label'] ?? '';
    
        // Generate the hidden input field
        return '<input type="hidden" name="id_label_map[' . esc_attr($id) . ']" value="' . esc_attr($label) . '">';
    }
    
    public function capture_form_data_with_placeholders($contact_form) {
        $submission = WPCF7_Submission::get_instance();
        if (!$submission) {
            return;
        }

        // Retrieve the submitted data
        $posted_data = $submission->get_posted_data();

        // Prepare an array to hold the data with labels
        $form_data_with_labels = [];

        // Map the submitted data to the labels
        foreach ($posted_data as $key => $value) {
            if (isset(self::$idLabelMap[$key])) {
                $form_data_with_labels[self::$idLabelMap[$key]] = $value;
            } else {
                // For fields without a specific label, use the key as is
                $form_data_with_labels[$key] = $value;
            }
        }

        // Check if the signature data is present in the posted data
        if (isset($posted_data['signature-data'])) {
            // Retrieve the signature data
            $signature_data = $posted_data['signature-data'];
            // Add the signature data to the form data array
            $form_data_with_labels['signature-data'] = $signature_data;
        }

        // Do something with the labeled data
        // For example, logging it
        error_log('Labeled Form Data: ' . print_r($form_data_with_labels, true));

        // ... any other processing you need ...
    }

    public function register_cf7_signature_tag() {
        if ( function_exists('wpcf7_add_form_tag') ) {
            wpcf7_add_form_tag('atollmatrix_cf7_digitalsignature', array($this, 'atollmatrix_cf7_signature_handler'));
        }
    }

    public function atollmatrix_cf7_signature_handler($tag) {
        return '<div class="signature-container">
                    <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
                    <div id="clear-signature">Clear</div>
                    <input type="hidden" id="signature-data" name="signature-data">
                </div>';
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

        $regsitration_contactform_id = '48b9210';

        $contactform = do_shortcode(('[contact-form-7 id="'.$regsitration_contactform_id.'"]'));

        return $contactform;

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