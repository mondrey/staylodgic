<?php
namespace AtollMatrix;

class GuestRegistry
{

    protected $bookingNumber;
    private static $idLabelMap = [  ];
    private $reservationID;
    private $hotelName;
    private $hotelPhone;
    private $hotelAddress;
    private $hotelHeader;
    private $hotelFooter;
    private $hotelLogo;

    public function __construct(
        $bookingNumber = null,
        $reservationID = null,
        $hotelName = null,
        $hotelPhone = null,
        $hotelAddress = null,
        $hotelHeader = null,
        $hotelFooter = null,
        $hotelLogo = null
    ) {
        $this->bookingNumber = get_post_meta(get_the_id(), 'atollmatrix_registry_bookingnumber', true);

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

        add_action('wp_ajax_delete_registration', array($this, 'delete_registration'));
        add_action('wp_ajax_nopriv_delete_registration', array($this, 'delete_registration'));

    }

    /**
     * Fetches the reservation and guest register post IDs based on a supplied booking number.
     * Returns an associative array with 'reservationID' and 'guestRegisterID' if both are found,
     * otherwise returns false.
     *
     * @param string $bookingNumber The booking number to search for.
     * @return array|bool An associative array with 'reservationID' and 'guestRegisterID', or false if not both found.
     */
    public function fetchResRegIDsByBookingNumber($bookingNumber)
    {
        $reservationArgs = array(
            'post_type'      => 'atmx_reservations', // Adjust to your reservation post type
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $bookingNumber,
                ),
            ),
        );

        $guestRegisterArgs = array(
            'post_type'      => 'atmx_guestregistry', // Adjust to your guest register post type
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_registry_bookingnumber', // Adjust if the meta key is different
                    'value' => $bookingNumber,
                ),
            ),
        );

        $reservationQuery   = new \WP_Query($reservationArgs);
        $guestRegisterQuery = new \WP_Query($guestRegisterArgs);

        // Check if both posts are found
        if ($reservationQuery->have_posts() && $guestRegisterQuery->have_posts()) {
            $result = array(
                'reservationID'   => $reservationQuery->posts[0]->ID,
                'guestRegisterID' => $guestRegisterQuery->posts[0]->ID,
            );
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Outputs the final registered and occupancy numbers for a given reservation in either text, fraction, or icons format.
     *
     * @param int $reservationID The ID of the reservation.
     * @param int $registerID The ID used for registering.
     * @param string $outputFormat Specifies the output format: 'text', 'fraction', or 'icons'.
     */
    public function outputRegistrationAndOccupancy($reservationID, $registerID, $outputFormat = 'text')
    {
        $reservation_instance = new \AtollMatrix\Reservations();

        // Get total occupants for the reservation
        $reservation_occupants = $reservation_instance->getTotalOccupantsForReservation($reservationID);

        // Get registered guest count
        $registeredGuestCount = $this->getRegisteredGuestCount($registerID);

        // Determine the output format
        if ($outputFormat === 'icons' && $registeredGuestCount <= $reservation_occupants) {
            echo '<div class="reservation-details">';
            // Output filled circles for registered guests
            for ($i = 0; $i < $registeredGuestCount; $i++) {
                echo '<i class="fas fa-circle"></i> ';
            }
            // Output outline circles for the remaining occupancy
            for ($i = $registeredGuestCount; $i < $reservation_occupants; $i++) {
                echo '<i class="far fa-circle"></i> ';
            }
            echo '</div>';
        } elseif ($outputFormat === 'fraction' || $registeredGuestCount > $reservation_occupants) {
            // Fallback to fraction if registered guests exceed total occupancy or fraction is requested
            echo '<div class="reservation-details">';
            echo '<div class="occupancy-details">Registered: ' . esc_html($registeredGuestCount) . '/' . esc_html($reservation_occupants) . '</div>';
            echo '</div>';
        } else { // Default to text format
            echo '<div class="reservation-details">';
            echo '<div class="registered-occupants">Total guests: ' . esc_html($reservation_occupants) . '</div>';
            echo '<div class="registered-guests">Registered guests: ' . esc_html($registeredGuestCount) . '</div>';
            echo '</div>';
        }
    }

    /**
     * Returns the count of registered guests from the registration_data array for a given reservation ID.
     * If no ID is supplied, it attempts to fetch the current post ID.
     *
     * @param int|null $registrationID Optional. The ID of the reservation post. Default null.
     * @return int The number of registered guests.
     */
    public function getRegisteredGuestCount($registrationID = null)
    {
        // Use the supplied ID or fallback to the current post ID
        $idToUse = $registrationID ? $registrationID : get_the_id();

        $registration_data = get_post_meta($idToUse, 'registration_data', true);
        if (is_array($registration_data)) {
            return count($registration_data);
        }
        return 0; // Return 0 if registration_data is not an array or is empty
    }

    public function display_registration()
    {

        // Hotel Information
        $property_logo_id = atollmatrix_get_option('property_logo');
        $property_name    = atollmatrix_get_option('property_name');
        $property_phone   = atollmatrix_get_option('property_phone');
        $property_address = atollmatrix_get_option('property_address');
        $property_header  = atollmatrix_get_option('property_header');
        $property_footer  = atollmatrix_get_option('property_footer');

        $this->hotelName    = $property_name;
        $this->hotelPhone   = $property_phone;
        $this->hotelAddress = $property_address;
        $this->hotelHeader  = $property_header;
        $this->hotelFooter  = $property_footer;
        $this->hotelLogo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

        $registrationSheet = $this->registrationTemplate(
            $this->bookingNumber,
            $this->hotelName,
            $this->hotelPhone,
            $this->hotelAddress,
            $this->hotelHeader,
            $this->hotelFooter,
            $this->hotelLogo
        );
        echo $registrationSheet;
    }

    public function registrationTemplate(
        $bookingNumber,
        $hotelName,
        $hotelPhone,
        $hotelAddress,
        $hotelHeader,
        $hotelFooter,
        $hotelLogo
    ) {
        $currentDate = date('F jS, Y'); // Outputs: January 1st, 2024

        $registration_data = get_post_meta(get_the_id(), 'registration_data', true);

        error_log('registration_data');
        error_log(print_r($registration_data, true));

        if (is_array($registration_data)) {
            foreach ($registration_data as $guest_id => $guest_data) {

                // Get the post URL
                $post_url = get_permalink(get_the_id()); // Assuming $post_id is the ID of the post

                // Append guest ID as a query parameter
                $edit_url = add_query_arg('guest', $guest_id, $post_url);

                // Add Edit and Delete buttons
                echo '<a href="' . esc_url($edit_url) . '" target="_blank" class="registration-button edit-registration" data-guest-id="' . esc_attr($guest_id) . '">Edit</a>';
                // Inside your PHP loop where you're echoing out the delete buttons
                echo '<button class="registration-button delete-registration" data-guest-id="' . esc_attr($guest_id) . '">Delete</button>';

                ob_start();
                ?>
        <button data-title="Guest Registration <?php echo $guest_data['registration_id']; ?>" data-id="<?php echo $guest_data['registration_id']; ?>" id="print-invoice-button" class="print-invoice-button">Print Invoice</button>
        <button data-file="registration-<?php echo $guest_data['registration_id']; ?>" data-id="<?php echo $guest_data['registration_id']; ?>" id="save-pdf-invoice-button" class="save-pdf-invoice-button">Save PDF</button>
        
        <div class="invoice-container" data-bookingnumber="<?php echo $guest_data['registration_id']; ?>">
        <div class="invoice-container-inner">
        <div id="invoice-hotel-header">
            <section id="invoice-hotel-logo">
                <img class="invoice-logo" src="<?php echo $hotelLogo; ?>" />
            </section>
            <section id="invoice-info">
                <p><?php echo $hotelHeader; ?></p>
                <p>Booking Reference: <?php echo $bookingNumber; ?></p>
                <p>Date: <?php echo $currentDate; ?></p>
                <p class="invoice-booking-status">Guest registration</p>
            </section>
        </div>
        <section id="invoice-hotel-info">
                <p><strong><?php echo $hotelName; ?></strong></p>
                <p><?php echo $hotelAddress; ?></p>
                <p><?php echo $hotelPhone; ?></p>
        </section>
        <section id="invoice-customer-info">
            <h2 id="invoice-subheading">Registration:</h2>
            <div class="invoice-customer-registration">
            <?php
// Display guest information
                foreach ($guest_data as $info_key => $info_value) {
                    // Skip the registration_id in the inner loop since it's handled separately
                    if ($info_key != 'registration_id') {
                        echo '<p class="type-container" data-type="' . esc_attr($info_value[ 'type' ]) . '" data-id="' . esc_attr($info_key) . '"><strong><span class="registration-label">' . esc_html($info_value[ 'label' ]) . ':</span></strong> <span class="registration-data">' . esc_html($info_value[ 'value' ]) . '</span></p>';
                    }
                }

                // Handle registration_id and signature image separately
                if (isset($guest_data[ 'registration_id' ])) {
                    $registration_id = $guest_data[ 'registration_id' ];
                    $upload_dir      = wp_upload_dir();
                    $signature_url   = $upload_dir[ 'baseurl' ] . '/signatures/' . $registration_id . '.png';

                    echo '<img class="registration-signature" src="' . esc_url($signature_url) . '" alt="Signature">';
                }

                ?>
            </div>
        </section>

        </div>
        <footer>
            <div class="invoice-footer"><?php echo $hotelFooter; ?></div>
        </footer>
        </div>
        <?php
}

            // After the loop, add the modal HTML
            echo '<div id="deleteConfirmationModal" class="modal" style="display: none;">';
            echo '<div class="modal-content">';
            echo '<span class="close-button">×</span>';
            echo '<h4>Confirm Deletion</h4>';
            echo '<p>Are you sure you want to delete this registration?</p>';
            echo '<button id="confirmDelete">Delete</button>';
            echo '<button id="cancelDelete">Cancel</button>';
            echo '</div>';
            echo '</div>';

        }
        return ob_get_clean();
    }

    function save_guestregistration_data()
    {
        error_log('Registration post save'); // Log the POST data
        error_log(print_r($_POST, true)); // Log the POST data

        // Verify nonce
        if (!isset($_POST[ 'nonce' ]) || !wp_verify_nonce($_POST[ 'nonce' ], 'atollmatrix-nonce-search')) {
            wp_die('Security check failed');
        }

        $post_id        = intval($_POST[ 'post_id' ]); // Sanitize post ID
        $booking_data   = $_POST[ 'booking_data' ]; // Consider sanitizing this data
        $signature_data = $_POST[ 'signature_data' ]; // Base64 data

        $guest_id = false;
        if (isset($_POST[ 'guest_id' ])) {
            $guest_id = $_POST[ 'guest_id' ]; // data
        }

        $registration_data = array();

        // Create a directory if it doesn't exist
        $upload_dir     = wp_upload_dir();
        $signatures_dir = $upload_dir[ 'basedir' ] . '/signatures';
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
                $file            = $signatures_dir . '/' . $registration_id . '.png';
                if (file_put_contents($file, $signature_data) === false) {
                    error_log('Failed to save signature file.');
                } else {
                    error_log('Signature file saved successfully.');
                    $booking_data[ 'registration_id' ] = $registration_id;
                    if (isset($booking_data[ 'signature_data' ])) {
                        unset($booking_data[ 'signature_data' ]);
                    }
                    if (isset($booking_data[ 'signature-data' ])) {
                        unset($booking_data[ 'signature-data' ]);
                    }
                    if (isset($booking_data[ 'Sign' ])) {
                        unset($booking_data[ 'Sign' ]);
                    }
                    if (null !== get_post_meta($post_id, 'registration_data', true)) {
                        $registration_data = get_post_meta($post_id, 'registration_data', true);
                    }
                    if (isset($booking_data['registration_id']) && $guest_id) {
                        $registration_id = $guest_id;
                    }
                    $registration_data[ $registration_id ] = $booking_data;
                    update_post_meta($post_id, 'registration_data', $registration_data);
                }
            }
        } else {
            error_log('Invalid signature data format.');
        }

        echo 'Data Saved';
        wp_die();
    }

    function delete_registration()
    {

        $guest_id = isset($_POST[ 'guest_id' ]) ? $_POST[ 'guest_id' ] : 0;
        $post_id  = isset($_POST[ 'post_id' ]) ? $_POST[ 'post_id' ] : 0;

        // Remove registration data and signature
        $registration_data = get_post_meta($post_id, 'registration_data', true);

        error_log('Delete guest data');
        error_log($post_id);
        error_log(print_r($registration_data, true));
        error_log('Delete guest data -- end');
        if (isset($registration_data[ $guest_id ])) {
            $registration_id = $registration_data[ $guest_id ][ 'registration_id' ];
            unset($registration_data[ $guest_id ]);
            update_post_meta($post_id, 'registration_data', $registration_data);

            // Delete signature file
            $upload_dir     = wp_upload_dir();
            $signature_file = $upload_dir[ 'basedir' ] . '/signatures/' . $registration_id . '.png';
            if (file_exists($signature_file)) {
                unlink($signature_file);
            }

            wp_send_json_success();
        } else {
            wp_send_json_error();
        }

        wp_die(); // This is required to terminate immediately and return a proper response
    }

    function get_guest_post_permalink()
    {

        // Verify the nonce
        if (!isset($_POST[ 'nonce' ])) {
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        $post_id = isset($_POST[ 'post_id' ]) ? intval($_POST[ 'post_id' ]) : 0;
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

            $form_start  = '[form_start id="guestregistration" class="guest-registration" action="submission_url" method="post"]';
            $form_submit = '[form_input type="submit" id="submitregistration" class="btn btn-primary" value="Save Registration"]';
            $form_end    = '[form_end]';

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
            'full_name'     => 'Full Name',
            'passport'      => 'Passport No',
            'email_address' => 'Email Address',
            'phone_number'  => 'Phone Number',
            'country'       => 'Country',
            'guest_consent' => 'By clicking "Book this Room" you agree to our terms and conditions and privacy policy.',
         ];

        return $dataFields;
    }

    public function requestRegistrationDetails($booking_number)
    {
        $booking_number = $_POST[ 'booking_number' ];

        // Fetch reservation details
        $reservation_instance = new \AtollMatrix\Reservations();
        $reservationQuery     = $reservation_instance->getReservationforBooking($booking_number);

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