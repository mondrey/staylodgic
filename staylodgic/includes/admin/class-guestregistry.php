<?php

namespace Staylodgic;

class GuestRegistry
{

    protected $bookingNumber;
    private static $idLabelMap = [];
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
        $this->bookingNumber = get_post_meta(get_the_id(), 'staylodgic_registry_bookingnumber', true);

        add_shortcode('guest_registration', array($this, 'guestRegistration'));

        // add_shortcode('staylodgic_cf7_digitalsignature', array($this, 'staylodgic_cf7_digital_signature_shortcode'));

        add_action('wp_ajax_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));
        add_action('wp_ajax_nopriv_requestRegistrationDetails', array($this, 'requestRegistrationDetails'));

        // Add a filter to modify the content of slgc_guestregistry posts
        add_filter('the_content', array($this, 'append_shortcode_to_content'));

        add_action('wp_ajax_save_guestregistration_data', array($this, 'save_guestregistration_data'));
        add_action('wp_ajax_nopriv_save_guestregistration_data', array($this, 'save_guestregistration_data'));

        add_action('wp_ajax_get_guest_post_permalink', array($this, 'get_guest_post_permalink'));
        add_action('wp_ajax_nopriv_get_guest_post_permalink', array($this, 'get_guest_post_permalink'));

        add_action('wp_ajax_delete_registration', array($this, 'delete_registration'));
        add_action('wp_ajax_nopriv_delete_registration', array($this, 'delete_registration'));

        add_action('wp_ajax_create_guest_registration', array($this, 'create_guest_registration_ajax_handler'));
    }

    public function create_guest_registration_ajax_handler()
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }
        // Check user capabilities or nonce here for security, e.g.,
        // if ( !current_user_can('edit_posts') ) wp_die();
        $bookingNumber = isset($_POST['bookingNumber']) ? sanitize_text_field($_POST['bookingNumber']) : '';

        // Create a new guest registration post
        $post_id = wp_insert_post(array(
            'post_title'   => wp_strip_all_tags('Registration for ' . $bookingNumber),
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'slgc_guestregistry', // Ensure this is the correct post type
            'meta_input' => array(
                'staylodgic_registry_bookingnumber' => $bookingNumber,
            ),
        ));

        if ($post_id) {
            // Successfully created post, return its ID
            echo $post_id;
        } else {
            // There was an error
            echo 'error';
        }

        wp_die(); // this is required to terminate immediately and return a proper response
    }

    /**
     * Checks if a guest registration post exists for a given booking number.
     *
     * @param string $bookingNumber The booking number to search for.
     * @return bool|int Returns the guest register post ID if found, otherwise returns false.
     */
    public function checkGuestRegistrationByBookingNumber($bookingNumber)
    {
        $guestRegisterArgs = array(
            'post_type'   => 'slgc_guestregistry', // Adjust to your guest register post type
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key' => 'staylodgic_registry_bookingnumber', // Ensure this matches your actual meta key
                    'value' => $bookingNumber,
                ),
            ),
        );

        $guestRegisterQuery = new \WP_Query($guestRegisterArgs);

        // Check if a guest register post is found
        if ($guestRegisterQuery->have_posts()) {
            // Return the ID of the guest registration post
            return $guestRegisterQuery->posts[0]->ID;
        }

        return false; // Return false if no guest registration post is found
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
            'post_type'   => 'slgc_reservations', // Adjust to your reservation post type
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'staylodgic_booking_number',
                    'value' => $bookingNumber,
                ),
            ),
        );

        $guestRegisterArgs = array(
            'post_type'   => 'slgc_guestregistry', // Adjust to your guest register post type
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key' => 'staylodgic_registry_bookingnumber', // Adjust if the meta key is different
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

    public function allowGuestRegistration($registration_post_id)
    {
        $allow = true;
        $reason = '';

        $bookingNumber = get_post_meta($registration_post_id, 'staylodgic_registry_bookingnumber', true);

        $resRegIDs = $this->fetchResRegIDsByBookingNumber($bookingNumber);

        $reservationID = $resRegIDs['reservationID'];
        $registerID = $resRegIDs['guestRegisterID'];

        $reservation_instance = new \Staylodgic\Reservations();

        $checkinDate = $reservation_instance->getCheckinDate($reservationID);
        // Convert check-in date to DateTime object
        $checkinDateObj = new \DateTime($checkinDate);

        // Get today's date
        $today = new \DateTime();

        // Check if check-in date has already passed
        if ($today > $checkinDateObj) {
            $allow = false;
            $reason = __('Check-in date has already passed','staylodgic');
        } else {
            // Calculate the difference in days
            $dateDiff = $today->diff($checkinDateObj)->days;

            // If the difference is more than 3 days, set $allow to false
            if ($dateDiff > 2) {
                $allow = false;
                $reason = __('Registration open 2 days before check-in','staylodgic');
            }
        }

        // Get total occupants for the reservation
        $reservation_occupants = $reservation_instance->getTotalOccupantsForReservation($reservationID);

        // Get registered guest count
        $registeredGuestCount = $this->getRegisteredGuestCount($registerID);

        if ((intval($reservation_occupants) + 2) < $registeredGuestCount) {
            $allow = false;
            $reason = __('Exceeds total registrations allowed for this booking', 'staylodgic');
        }

        if ($reason) {
            $reason = '<div class="error-registration-reason">' . $reason . '</div>';
        }

        return ['allow' => $allow, 'reason' => $reason];
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
        $reservation_instance = new \Staylodgic\Reservations();

        // Get total occupants for the reservation
        $reservation_occupants = $reservation_instance->getTotalOccupantsForReservation($reservationID);

        // Get registered guest count
        $registeredGuestCount = $this->getRegisteredGuestCount($registerID);

        $registration_output = '';

        // Determine the output format
        if ($outputFormat === 'icons' && $registeredGuestCount <= $reservation_occupants) {
            $registration_output .= '<div class="reservation-details">';
            // Output filled circles for registered guests
            for ($i = 0; $i < $registeredGuestCount; $i++) {
                $registration_output .= '<i class="fas fa-circle"></i> ';
            }
            // Output outline circles for the remaining occupancy
            for ($i = $registeredGuestCount; $i < $reservation_occupants; $i++) {
                $registration_output .= '<i class="far fa-circle"></i> ';
            }
            $registration_output .= '</div>';
        } elseif ($outputFormat === 'fraction' || $registeredGuestCount > $reservation_occupants) {
            // Fallback to fraction if registered guests exceed total occupancy or fraction is requested
            $registration_output .= '<div class="reservation-details">';
            $registration_output .= '<div class="occupancy-details">';
            $registration_output .= '<span class="registration-label">';
            $registration_output .= 'Registered: ' . esc_html($registeredGuestCount) . '/' . esc_html($reservation_occupants) . ' ';
            $registration_output .= '</span>';
            $registration_output .= '<a href="' . esc_url(get_edit_post_link($registerID)) . '">';
            $registration_output .= '<i class="fa-solid fa-eye"></i>';
            $registration_output .= '</a>';
            $registration_output .= '</div>';
            $registration_output .= '</div>';
        } else { // Default to text format
            $registration_output .= '<div class="reservation-details">';
            $registration_output .= '<div class="registered-occupants"><span class="registration-label">' . __('Total guests', 'staylodgic') . '</span>: ' . esc_html($reservation_occupants) . '</div>';
            $registration_output .= '<div class="registered-guests"><span class="registration-label">' . __('Registered guests', 'staylodgic') . '</span>: ' . esc_html($registeredGuestCount) . '</div>';
            $registration_output .= '</div>';
        }

        return $registration_output;
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

        $registration_data = get_post_meta($idToUse, 'staylodgic_registration_data', true);
        if (is_array($registration_data)) {
            return count($registration_data);
        }
        return 0; // Return 0 if registration_data is not an array or is empty
    }

    public function display_registration()
    {

        // Hotel Information
        $property_logo_id = staylodgic_get_option('property_logo');
        $property_name    = staylodgic_get_option('property_name');
        $property_phone   = staylodgic_get_option('property_phone');
        $property_address = staylodgic_get_option('property_address');
        $property_header  = staylodgic_get_option('property_header');
        $property_footer  = staylodgic_get_option('property_footer');

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

        if (isset($registrationSheet)) {
            echo $registrationSheet;
        } else {
            echo '<div class="registrations-not-found-notice-wrap">';
            echo '<div class="registrations-not-found-notice">';
            echo __('Registrations not found', 'staylodgic');
            echo '</div>';
            echo '</div>';
        }
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

        $registration_data = get_post_meta(get_the_id(), 'staylodgic_registration_data', true);

        // error_log('staylodgic_registration_data');
        // error_log(print_r($registration_data, true));

        if (is_array($registration_data) && !empty($registration_data)) {
            foreach ($registration_data as $guest_id => $guest_data) {

                // Get the post URL
                $post_url = get_permalink(get_the_id()); // Assuming $post_id is the ID of the post

                // Append guest ID as a query parameter
                $edit_url = add_query_arg('guest', $guest_id, $post_url);

                // Add Edit and Delete buttons
                echo '<div class="invoice-buttons-container">';
                echo '<div class="invoice-container-buttons">';
                echo '<a href="' . esc_url($edit_url) . '" target="_blank" class="button button-secondary registration-button edit-registration" data-guest-id="' . esc_attr($guest_id) . '">' . __('Edit', 'staylodgic') . '</a>';
                // Inside your PHP loop where you're echoing out the delete buttons
                ob_start();
?>
                <button data-title="Guest Registration <?php echo esc_attr($guest_data['registration_id']); ?>" data-id="<?php echo esc_attr($guest_data['registration_id']); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php _e('Print', 'staylodgic'); ?></button>
                <button data-file="registration-<?php echo esc_attr($guest_data['registration_id']); ?>" data-id="<?php echo esc_attr($guest_data['registration_id']); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php _e('Save PDF', 'staylodgic'); ?></button>
                </div>
                </div>
                <div class="invoice-container" data-bookingnumber="<?php echo esc_attr($guest_data['registration_id']); ?>">
                    <div class="invoice-container-inner">
                        <div id="invoice-hotel-header">
                            <section id="invoice-hotel-logo">
                                <img class="invoice-logo" src="<?php echo esc_url($hotelLogo); ?>" />
                            </section>
                            <section id="invoice-info">
                                <p><?php echo esc_html($hotelHeader); ?></p>
                                <p><?php _e('Booking Reference:', 'staylodgic'); ?> <?php echo esc_html($bookingNumber); ?></p>
                                <p><?php _e('Date:', 'staylodgic'); ?> <?php echo esc_html($currentDate); ?></p>
                                <p class="invoice-booking-status"><?php _e('Guest registration', 'staylodgic'); ?></p>
                            </section>
                        </div>
                        <section id="invoice-hotel-info">
                            <p><strong><?php echo esc_html($hotelName); ?></strong></p>
                            <p><?php echo esc_html($hotelAddress); ?></p>
                            <p><?php echo esc_html($hotelPhone); ?></p>
                        </section>
                        <section id="invoice-customer-info">
                            <h2 id="invoice-subheading"><?php _e('Registration:', 'staylodgic'); ?></h2>
                            <div class="invoice-customer-registration">
                                <?php
                                error_log('print_r($guest_data,1)');
                                error_log(print_r($guest_data,1));
                                // Display guest information
                                foreach ($guest_data as $info_key => $info_value) {
                                    // Skip the registration_id in the inner loop since it's handled separately
                                    if ($info_key != 'registration_id') {

                                        if ($info_key == 'countries') {
                                            $info_value['value'] = staylodgic_country_list('display', $info_value['value'] );
                                        }
                                        if ( $info_value['type'] == 'checkbox' && 'true' == $info_value['value'] ) {
                                            $info_value['value'] = 'Yes';
                                        }
                                        if ( $info_value['type'] == 'datetime-local' ) {
                                            $date = new \DateTime($info_value['value']);
                                            $formattedDate = $date->format('l, F j, Y g:i A');
                                            $info_value['value'] = $formattedDate;
                                        }

                                        echo '<p class="type-container" data-type="' . esc_attr($info_value['type']) . '" data-id="' . esc_attr($info_key) . '"><strong><span class="registration-label">' . esc_html($info_value['label']) . ':</span></strong> <span class="registration-data">' . esc_html($info_value['value']) . '</span></p>';
                                    }
                                }

                                // Handle registration_id and signature image separately
                                if (isset($guest_data['registration_id'])) {
                                    $registration_id = $guest_data['registration_id'];
                                    $upload_dir      = wp_upload_dir();
                                    $signature_url   = esc_url($upload_dir['baseurl'] . '/signatures/' . $registration_id . '.png');

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
                echo '<div class="registration-delete-container"><button class="button button-primary paper-document-button registration-button delete-registration" data-guest-id="' . esc_attr($guest_id) . '">' . __('Delete this registration', 'staylodgic') . '</button></div>';
            }

            // After the loop, add the modal HTML
            echo '<div id="deleteConfirmationModal" class="staylodgic-modal" style="display: none;">';
            echo '<div class="staylodgic-modal-content">';
            echo '<h4>' . __('Confirm Deletion', 'staylodgic') . '</h4>';
            echo '<p>' . __('Are you sure you want to delete this registration?', 'staylodgic') . '</p>';
            echo '<button class="button button-primary" id="confirmDelete">' . __('Delete', 'staylodgic') . '</button>';
            echo '<button class="button button-secondary" id="cancelDelete">' . __('Cancel', 'staylodgic') . '</button>';
            echo '</div>';
            echo '</div>';

            return ob_get_clean();
        } else {
            return null;
        }
    }

    public function save_guestregistration_data()
    {
        // error_log('Registration post save'); // Log the POST data
        // error_log(print_r($_POST, true)); // Log the POST data

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-search')) {
            wp_die('Security check failed');
        }

        $post_id        = intval($_POST['post_id']); // Sanitize post ID
        $booking_data   = $_POST['booking_data']; // Consider sanitizing this data
        $signature_data = $_POST['signature_data']; // Base64 data

        $guest_id = false;
        if (isset($_POST['guest_id'])) {
            $guest_id = $_POST['guest_id']; // data
        }

        $registration_data = array();

        // Create a directory if it doesn't exist
        $upload_dir     = wp_upload_dir();
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
                $file            = $signatures_dir . '/' . $registration_id . '.png';
                if (file_put_contents($file, $signature_data) === false) {
                    error_log('Failed to save signature file.');
                } else {
                    error_log('Signature file saved successfully.');
                    $booking_data['registration_id'] = $registration_id;
                    if (isset($booking_data['signature_data'])) {
                        unset($booking_data['signature_data']);
                    }
                    if (isset($booking_data['signature-data'])) {
                        unset($booking_data['signature-data']);
                    }
                    if (isset($booking_data['Sign'])) {
                        unset($booking_data['Sign']);
                    }
                    if (is_array(get_post_meta($post_id, 'staylodgic_registration_data', true))) {
                        $registration_data = get_post_meta($post_id, 'staylodgic_registration_data', true);
                    }
                    if (isset($booking_data['registration_id']) && $guest_id) {
                        $registration_id = $guest_id;
                    }
                    $registration_data[$registration_id] = $booking_data;
                    update_post_meta($post_id, 'staylodgic_registration_data', $registration_data);
                }
            }
        } else {
            error_log('Invalid signature data format.');
        }

        echo $this->registrationSuccessful($post_id);
        wp_die();
    }

    public function delete_registration()
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

        $guest_id = isset($_POST['guest_id']) ? $_POST['guest_id'] : 0;
        $post_id  = isset($_POST['post_id']) ? $_POST['post_id'] : 0;

        // Remove registration data and signature
        $registration_data = get_post_meta($post_id, 'staylodgic_registration_data', true);

        // error_log('Delete guest data');
        // error_log($post_id);
        // error_log(print_r($registration_data, true));
        // error_log('Delete guest data -- end');
        if (isset($registration_data[$guest_id])) {
            $registration_id = $registration_data[$guest_id]['registration_id'];
            unset($registration_data[$guest_id]);
            update_post_meta($post_id, 'staylodgic_registration_data', $registration_data);

            // Delete signature file
            $upload_dir     = wp_upload_dir();
            $signature_file = $upload_dir['basedir'] . '/signatures/' . $registration_id . '.png';
            if (file_exists($signature_file)) {
                unlink($signature_file);
            }

            wp_send_json_success();
        } else {
            wp_send_json_error();
        }

        wp_die(); // This is required to terminate immediately and return a proper response
    }

    public function get_guest_post_permalink()
    {

        // Verify the nonce
        if (!isset($_POST['nonce'])) {
            wp_send_json_error(['message' => 'Failed']);
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
     * Appends the saved shortcode to the content of slgc_guestregistry posts.
     */
    public function append_shortcode_to_content($content)
    {
        // Check if we are viewing a single post of type 'slgc_guestregistry'
        if (is_single() && get_post_type() == 'slgc_guestregistry') {

            $registrationAllowedData = $this->allowGuestRegistration(get_the_id());

            $registrationAllowed = $registrationAllowedData['allow'];
            // $registrationAllowed = true;
            if (!$registrationAllowed) {
                $content .= '<div class="guestregistry-shortcode-content">' . $registrationAllowedData['reason'] . '</div>';
            } else {
                // Retrieve saved shortcode content
                $saved_shortcode = get_option('staylodgic_guestregistry_shortcode', '');

                if ('' == $saved_shortcode) {
                    $formGenInstance = new \Staylodgic\FormGenerator();
                    $saved_shortcode = $formGenInstance->defaultShortcodes();
                }

                $saved_shortcode = stripslashes($saved_shortcode);
                $form_start_tag = '<div class="registration_form_wrap">';
                $form_start_tag .= '<div class="registration_form">';
                $form_start_tag .= '<div class="registration-column registration-column-one registration_form_inputs">';
                $form_start  = '[form_start id="guestregistration" class="guest-registration" action="submission_url" method="post"]';
                $form_submit = '[form_input type="submit" id="submitregistration" class="book-button" value="' . __('Save Registration', 'staylodgic') . '"]';
                $form_end    = '[form_end]';
                $form_end_tag = '</div>';
                $form_end_tag .= '<div class="registration-column registration-column-two">';
                $form_end_tag .= '<div id="booking-summary-wrap">';
                $form_end_tag .= '<div class="summary-section-title">' . __('Online Registration', 'staylodgic') . '</div>';
                $form_end_tag .= '<div class="summary-section-description"><p>' . __('Please fill the form for Online Registration.</p><p>You can fill according to the number of guests.</p><p>You can submit a registration if you think a mistake was made in a previous one.', 'staylodgic') . '</p></div>';
                $form_end_tag .= '</div>';
                $form_end_tag .= '</div>';
                $form_end_tag .= '</div>';
                $form_end_tag .= '</div>';

                $final_shortcode = $form_start_tag . $form_start . $saved_shortcode . $form_submit . $form_end . $form_end_tag;

                // Append the shortcode to the original content
                $content .= '<div class="guestregistry-shortcode-content">' . do_shortcode($final_shortcode) . '</div>';
            }
        }

        return $content;
    }

    public function registrationSuccessful($post_id)
    {
        // Localize and escape the button label
        $buttonLabel = esc_html__('Register another', 'staylodgic');

        // Construct the button with proper URL escaping
        $button = '<a href="' . esc_url(get_the_permalink($post_id)) . '" class="book-button button-inline">' . $buttonLabel . '</a>';

        // Localize the message
        $successMessage = esc_html__('Your registration was successful.', 'staylodgic');

        // Build the success HTML with localization for static strings using concatenated strings
        $success_html = '<div class="guest_registration_form_outer">' .
            '<div class="guest_registration_form_wrap">' .
            '<div class="guest_registration_form">' .
            '<div class="registration-successful-inner">' .
            '<h3>' . esc_html__('Registration Successful', 'staylodgic') . '</h3>' .
            '<p>Hi,</p>' .
            '<p>' . $successMessage . '</p>' .
            '<p>' . $button . '</p>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>';

        return $success_html;
    }


    public function bookingDataFields()
    {
        $dataFields = [
            'full_name'     => __('Full Name', 'staylodgic'),
            'passport'      => __('Passport No', 'staylodgic'),
            'email_address' => __('Email Address', 'staylodgic'),
            'phone_number'  => __('Phone Number', 'staylodgic'),
            'country'       => __('Country', 'staylodgic'),
            'guest_consent' => __('By clicking "Book this Room" you agree to our terms and conditions and privacy policy.', 'staylodgic'),
        ];

        return $dataFields;
    }

    public function requestRegistrationDetails($booking_number)
    {
        $booking_number = $_POST['booking_number'];

        // Fetch reservation details
        $reservation_instance = new \Staylodgic\Reservations();
        $reservationQuery     = $reservation_instance->getReservationforBooking($booking_number);

        // Verify the nonce
        if (!isset($_POST['staylodgic_bookingdetails_nonce']) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error(['message' => 'Failed']);
            return;
        }

        ob_start(); // Start output buffering
        echo "<div class='element-container-group'>";
        if ($reservationQuery->have_posts()) {

            echo "<div class='reservation-details'>";
            while ($reservationQuery->have_posts()) {
                $reservationQuery->the_post();
                $reservationID = get_the_ID();

                // Display reservation details
                echo "<h3>Reservation ID: " . esc_html($reservationID) . "</h3>";
                echo "<p>Check-in Date: " . esc_html(get_post_meta($reservationID, 'staylodgic_checkin_date', true)) . "</p>";
                echo "<p>Check-out Date: " . esc_html(get_post_meta($reservationID, 'staylodgic_checkout_date', true)) . "</p>";
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
            // echo "<p>Full Name: " . esc_html(get_post_meta($guestID, 'staylodgic_full_name', true)) . "</p>";
            // echo "<p>Email Address: " . esc_html(get_post_meta($guestID, 'staylodgic_email_address', true)) . "</p>";
            $registry_instance = new \Staylodgic\GuestRegistry();
            $resRegIDs =  $registry_instance->fetchResRegIDsByBookingNumber($booking_number);
            if ($resRegIDs) {
                $guest_registration_url = get_permalink($resRegIDs['guestRegisterID']);
                echo '<a href="' . esc_url($guest_registration_url) . '" class="book-button button-inline">' . __('Proceed to register', 'staylodgic') . '</a>';
            }
            // Add other guest details as needed
            echo "</div>";
        } else {
            echo "<p>No guest details found for Booking Number: " . esc_html($booking_number) . "</p>";
        }
        echo "</div>";

        $informationSheet = ob_get_clean(); // Get the buffer content and clean the buffer
        echo $informationSheet; // Directly output the HTML content
        wp_die(); // Terminate and return a proper response
    }

    public function guestRegistration()
    {
        ob_start();
        $staylodgic_bookingdetails_nonce = wp_create_nonce('staylodgic-bookingdetails-nonce');
        ?>
        <div class="staylodgic-content">
            <div id="hotel-booking-form">
                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr($staylodgic_bookingdetails_nonce); ?>" />
                                <input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label"><?php echo __('Booking No.', 'staylodgic'); ?></label>
                            </div>
                        </div>
                        <div data-request="guestregistration" id="bookingDetails" class="form-search-button"><?php echo __('Search', 'staylodgic'); ?></div>
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

$instance = new \Staylodgic\GuestRegistry();
