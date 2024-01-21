<?php
namespace AtollMatrix;

class GuestRegistry
{

    protected $bookingNumber;
    private static $idLabelMap = [];

    public function __construct(
        $bookingNumber = null
    ) {
        $this->bookingNumber = uniqid();

        add_shortcode('guest_registration', array($this, 'guestRegistration'));
        
        // add_shortcode('atollmatrix_cf7_digitalsignature', array($this, 'atollmatrix_cf7_digital_signature_shortcode'));

        add_action('wp_ajax_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));
        add_action('wp_ajax_nopriv_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));

        // Add a filter to modify the content of atmx_guestregistry posts
        add_filter('the_content', array($this, 'append_shortcode_to_content'));

        add_action('wp_ajax_save_guestregistration_data', array($this, 'save_guestregistration_data'));
        add_action('wp_ajax_nopriv_save_guestregistration_data', array($this, 'save_guestregistration_data'));

        add_action('wp_ajax_get_guest_post_permalink', array($this, 'get_guest_post_permalink'));
        add_action('wp_ajax_nopriv_get_guest_post_permalink', array($this, 'get_guest_post_permalink'));

    }

    function save_guestregistration_data() {
        error_log(print_r($_POST, true)); // Log the POST data
    
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'atollmatrix-nonce-search')) {
            wp_die('Security check failed');
        }
    
        $post_id = intval($_POST['post_id']); // Sanitize post ID
        $booking_data = $_POST['booking_data']; // Consider sanitizing this data
        $signature_data = $_POST['signature_data']; // Base64 data
    
        // Create a directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $signatures_dir = $upload_dir['basedir'] . '/signatures';
        if (!file_exists($signatures_dir)) {
            wp_mkdir_p($signatures_dir);
        }
    
        // Decode signature data and save as PNG
        if (strpos($signature_data, 'data:image/png;base64,') === 0) {
            $signature_data = str_replace('data:image/png;base64,', '', $signature_data);
            $signature_data = str_replace(' ', '+', $signature_data);
            $signature_data = base64_decode($signature_data);
    
            if ($signature_data === false) {
                error_log('Decoding base64 signature failed.');
            } else {
                $registration_id = $post_id . '_' . rand(); // Random number prefixed with post_id
                $file = $signatures_dir . '/' . $registration_id . '.png';
                if (file_put_contents($file, $signature_data) === false) {
                    error_log('Failed to save signature file.');
                } else {
                    error_log('Signature file saved successfully.');
                    $booking_data['Registration ID'] = $registration_id;
                    update_post_meta($post_id, 'booking_data', $booking_data);
                }
            }
        } else {
            error_log('Invalid signature data format.');
        }
    
        echo 'Data Saved';
        wp_die();
    }    

    function get_guest_post_permalink() {

        // Verify the nonce
        if (!isset($_POST[ 'nonce' ])) {
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        if ($post_id) {
            $permalink = get_permalink($post_id);
            wp_send_json_success($permalink);
        } else {
            wp_send_json_error('Post ID is invalid.');
        }
    }  

    /**
     * Appends the saved shortcode to the content of atmx_guestregistry posts.
     */
    public function append_shortcode_to_content($content)
    {
        // Check if we are viewing a single post of type 'atmx_guestregistry'
        if (is_single() && get_post_type() == 'atmx_guestregistry') {
            // Retrieve saved shortcode content
            $saved_shortcode = get_option('atollmatrix_guestregistry_shortcode', '');
            $saved_shortcode = stripslashes($saved_shortcode);

            $form_start = '[form_start action="submission_url" method="post"]';
            $form_submit = '[form_input type="submit" id="submitregistration" class="btn btn-primary" value="Save Registration"]';
            $form_end = '[form_end]';

            $final_shortcode = $form_start . $saved_shortcode . $form_submit . $form_end;


            // Append the shortcode to the original content
            $content .= '<div class="guestregistry-shortcode-content">' . do_shortcode($final_shortcode) . '</div>';
        }

        return $content;
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