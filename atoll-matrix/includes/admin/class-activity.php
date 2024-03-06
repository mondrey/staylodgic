<?php
namespace AtollMatrix;

class Activity
{

    protected $bookingNumber;
    private $reservation_id;
    protected $checkinDate;
    protected $staynights;
    protected $adultGuests;
    protected $childrenGuests;
    protected $children_age;
    protected $totalGuests;
    protected $activitiesArray;
    
    public function __construct(
        $bookingNumber = null,
        $reservation_id = false,
        $checkinDate = null,
        $staynights = null,
        $adultGuests = null,
        $childrenGuests = null,
        $children_age = null,
        $totalGuests = null,
        $activitiesArray = array()
    )
    {
        add_action('wp_ajax_get_activity_schedules', array($this, 'get_activity_schedules_ajax_handler'));
        add_action('wp_ajax_nopriv_get_activity_schedules', array($this, 'get_activity_schedules_ajax_handler'));

        add_action('wp_ajax_get_activity_frontend_schedules', array($this, 'get_activity_frontend_schedules_ajax_handler'));
        add_action('wp_ajax_nopriv_get_activity_frontend_schedules', array($this, 'get_activity_frontend_schedules_ajax_handler'));

        add_action('wp_ajax_process_SelectedActivity', array($this, 'process_SelectedActivity'));
        add_action('wp_ajax_nopriv_process_SelectedActivity', array($this, 'process_SelectedActivity'));

        add_action('wp_ajax_bookActivity', array($this, 'bookActivity'));
        add_action('wp_ajax_nopriv_bookActivity', array($this, 'bookActivity'));

        add_shortcode('activity_booking_search', array($this, 'activity_search_shortcode'));


        add_filter('the_content', array($this, 'activity_content'));

        $this->bookingNumber         = uniqid();
        $this->reservation_id        = $reservation_id;
        $this->checkinDate           = $checkinDate;
        $this->staynights            = $staynights;
        $this->adultGuests           = $adultGuests;
        $this->childrenGuests        = $childrenGuests;
        $this->children_age          = $children_age;
        $this->totalGuests           = $totalGuests;
        $this->activitiesArray           = $activitiesArray;

    }

    public static function getReservationforActivity($booking_number)
    {
        $args = array(
            'post_type'      => 'atmx_activityres',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        return new \WP_Query($args);
    }

    public function getGuest_id_forReservation($booking_number)
    {
        $args = array(
            'post_type'      => 'atmx_activityres',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'atollmatrix_customer_id', true);
            return $customer_id;
        }

        return false; // Return an empty query if no guest found
    }

    public function getActivityNameForReservation($reservation_id = false)
    {

        // Get room id from post meta
        $room_id = get_post_meta($reservation_id, 'atollmatrix_activity_id', true);

        // If room id exists, get the room's post title
        if ($room_id) {
            $room_post = get_post($room_id);
            if ($room_post) {
                return $room_post->post_title;
            }
        }

        return null;
    }

    public function isConfirmed_Reservation($reservation_id)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the reservation status for the reservation
        $reservation_status = get_post_meta($reservation_id, 'atollmatrix_reservation_status', true);

        if ('confirmed' == $reservation_status) {
            return true;
        }

        return false;

    }

    public static function getActivityIDforBooking($booking_number)
    {
        $args = array(
            'post_type'      => 'atmx_activityres',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            return $reservation->ID;
        }

        return false; // Return an false if no reservatuib found
    }

    public function get_activity_schedules_ajax_handler() {
        $selected_date = isset($_POST['selected_date']) ? sanitize_text_field($_POST['selected_date']) : null;
        $total_people = isset($_POST['totalpeople']) ? sanitize_text_field($_POST['totalpeople']) : null;
        $the_post_id = isset($_POST['the_post_id']) ? sanitize_text_field($_POST['the_post_id']) : null;

        error_log('AJAX handler triggered. Selected date: ' . $selected_date);
    
        // Call the method and capture the output
        ob_start();
        $this->display_activity_schedules_with_availability($selected_date, $the_post_id, $total_people);
        $output = ob_get_clean();
    
        // Return the output as a JSON response
        wp_send_json_success($output);
    }

    public function get_activity_frontend_schedules_ajax_handler() {

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_searchbox_nonce' ]) || !check_admin_referer('atollmatrix-searchbox-nonce', 'atollmatrix_searchbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
        
        $selected_date = isset($_POST['selected_date']) ? sanitize_text_field($_POST['selected_date']) : null;
        $total_people = isset($_POST['totalpeople']) ? sanitize_text_field($_POST['totalpeople']) : null;
        $the_post_id = isset($_POST['the_post_id']) ? sanitize_text_field($_POST['the_post_id']) : null;

        $number_of_children = 0;
        $number_of_adults   = 0;
        $number_of_guests   = 0;
        $children_age       = array();

        if (isset($_POST[ 'number_of_adults' ])) {
            $number_of_adults = $_POST[ 'number_of_adults' ];
        }

        if (isset($_POST[ 'number_of_children' ])) {
            $number_of_children = $_POST[ 'number_of_children' ];
        }

        if (isset($_POST[ 'children_age' ])) {
            // Loop through all the select elements with the class 'children-age-selector'
            foreach ($_POST[ 'children_age' ] as $selected_age) {
                // Sanitize and store the selected values in an array
                $children_age[  ] = sanitize_text_field($selected_age);
            }
        }

        error_log('AJAX handler triggered. Selected date: ' . $selected_date);
    
        // Call the method and capture the output
        ob_start();
        $this->display_activity_frontend_schedules_with_availability(
            $selected_date,
            $the_post_id,
            $total_people,
            $children_age,
            $number_of_children,
            $number_of_adults
        );
        $output = ob_get_clean();
    
        // Return the output as a JSON response
        wp_send_json_success($output);
    }

    public function display_activity_frontend_schedules_with_availability(
        $selected_date = null,
        $the_post_id = false,
        $total_people = false,
        $children_age = null,
        $number_of_children = null,
        $number_of_adults = null
    ) {

        $this->children_age = $children_age;
        $this->childrenGuests = $number_of_children;
        $this->adultGuests = $number_of_adults;
        $this->checkinDate = $selected_date;

        $this->activitiesArray = array();

        $number_of_guests = intval($number_of_adults) + intval($number_of_children);

        $this->totalGuests = $number_of_guests;

        // Use today's date if $selected_date is not provided
        if (is_null($selected_date)) {
            $selected_date = date('Y-m-d');
        }

        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_time', true)) {
            $existing_activity_time = get_post_meta($the_post_id, 'atollmatrix_activity_time', true);
        }
        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_id', true)) {
            $existing_activity_id = get_post_meta($the_post_id, 'atollmatrix_activity_id', true);
        }

        $this->activitiesArray['date'] = $selected_date;
        $this->activitiesArray['adults'] = $this->adultGuests;
        $this->activitiesArray['children'] = $this->childrenGuests;
        $this->activitiesArray['children_age'] = $this->children_age;
        $this->activitiesArray['person_total'] = $this->totalGuests;
    
        // Get the day of the week for the selected date
        $day_of_week = strtolower(date('l', strtotime($selected_date)));
    
        // Query all activity posts
        $args = array(
            'post_type' => 'atmx_activity',
            'posts_per_page' => -1,
        );
        $activities = new \WP_Query($args);
    
        echo '<form action="" method="post" id="hotel-acitivity-listing" class="needs-validation" novalidate>';
        $roomlistingbox = wp_create_nonce('atollmatrix-roomlistingbox-nonce');
        echo '<input type="hidden" name="atollmatrix_roomlistingbox_nonce" value="' . esc_attr($roomlistingbox) . '" />';

        echo '<div id="activity-data" data-bookingnumber="' . $this->bookingNumber . '" data-children="' . $this->childrenGuests . '" data-adults="' . $this->adultGuests . '" data-guests="' . $this->totalGuests . '" data-checkin="' . $this->checkinDate . '">';
        // Start the container div
        echo '<div class="activity-schedules-container">';
    

        echo '<h3>' . ucfirst($day_of_week) . '</h3>';
        // Loop through each activity post
        if ($activities->have_posts()) {
            while ($activities->have_posts()) {
                $activities->the_post();
                $post_id = get_the_ID();

                $activity_schedule = get_post_meta($post_id, 'atollmatrix_activity_schedule', true);
                $max_guests = get_post_meta($post_id, 'atollmatrix_max_guests', true);

                if (null !== get_post_meta($post_id, 'atollmatrix_activity_rate', true)) {
                    $activity_rate= get_post_meta($post_id, 'atollmatrix_activity_rate', true);
                }
    
                // Display the activity identifier (e.g., post title)
                echo '<div class="activity-schedule room-occupied-group" id="activity-schedule-' . $post_id . '">';

                if (null !== get_post_meta($post_id, 'atollmatrix_activity_desc', true)) {
                    $activity_desc = get_post_meta($post_id, 'atollmatrix_activity_desc', true);
                }

                if (null !== $post_id) {
                    $activity_image = atollmatrix_featured_image_link($post_id);
                }

                echo '<div class="activity-column-one">';
                echo '<div class="activity-image" style="background-image: url('.esc_url($activity_image).');"></div>';
                echo '</div>';
                echo '<div class="activity-column-two">';
                echo '<h4 class="activity-title">' . get_the_title() . '</h4>';
                echo '<div class="activity-desc">'.$activity_desc.'</div>';

    
                // Display the time slots for the day of the week that matches the selected date
                if (!empty($activity_schedule) && isset($activity_schedule[$day_of_week])) {
                    echo '<div class="day-schedule">';
                    foreach ($activity_schedule[$day_of_week] as $index => $time) {
                        // Calculate remaining spots for this time slot
                        
                        $remaining_spots = $this->calculate_remaining_spots($post_id, $selected_date, $time, $max_guests);

                        $remaining_spots_compare = $remaining_spots;
                        $existing_found = false;

                        if ( $existing_activity_id == $post_id && $time == $existing_activity_time ) {

                            $reservedForGuests = $this->getActivityReservationNumbers( $the_post_id );
                            $existing_spots_for_day = $reservedForGuests['total'];

                            $remaining_spots_compare = $remaining_spots + $existing_spots_for_day;
                            $existing_found = true;
                        }
                        // echo $selected_date;
                        $active_class = "time-disabled";

                        if ( $this->totalGuests <= $remaining_spots_compare && 0 !== $remaining_spots ) {
                            $active_class = "time-active";
                            if ( $existing_found ) {
                                $active_class .= ' time-choice';
                            }
                        }

                        $time_index = $day_of_week . '-' . $index;

                        if ( '' !== $time) {
                            $total_rate = intval( $activity_rate * $this->totalGuests );
                            $this->activitiesArray[$post_id][$time] = $total_rate;
                            echo '<span class="time-slot '.$active_class.'" id="time-slot-' . $time_index . '" data-activity="'.$post_id.'" data-time="' . $time . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> ' . $time . '</span><span class="time-slots-remaining">( ' . $remaining_spots . ' of ' .$max_guests. ' remaining )</span><div class="activity-rate" data-activityprice="'.$total_rate.'">'. atollmatrix_price( $total_rate ) . '</div></span> ';
                        } else {
                            echo '<span class="time-slot-unavailable time-slot '.$active_class.'" id="time-slot-' . $time_index . '" data-activity="'.$post_id.'" data-time="' . $time . '"><span class="activity-time-slot">Unavailable</span></span> ';
                        }
                        
                    }
                    echo '</div>';
                }
                echo '</div>';
    
                echo '</div>'; // Close the activity-schedule div
            }
        }
    
        // Close the container div
        echo '</div>';
        echo '</div>';
        echo $this->register_Guest_Form();
        echo '</form>';
        error_log('Activities array');
        error_log(print_r( $this->activitiesArray, true ));
        atollmatrix_set_booking_transient($this->activitiesArray, $this->bookingNumber);
        $activities_data = atollmatrix_get_booking_transient( $this->bookingNumber );
        error_log('Activities array from transient');
        error_log(print_r($activities_data, true ));
        // Reset post data
        wp_reset_postdata();
    }

    public function process_SelectedActivity()
    {

        $bookingnumber   = sanitize_text_field($_POST[ 'bookingnumber' ]);
        $activity_id         = sanitize_text_field($_POST[ 'activity_id' ]);
        $activity_date         = sanitize_text_field($_POST[ 'activity_date' ]);
        $activity_time         = sanitize_text_field($_POST[ 'activity_time' ]);
        $activity_price      = sanitize_text_field($_POST[ 'activity_price' ]);

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_roomlistingbox_nonce' ]) || !check_admin_referer('atollmatrix-roomlistingbox-nonce', 'atollmatrix_roomlistingbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        $booking_results = $this->process_ActivityData(
            $bookingnumber,
            $activity_id,
            $activity_date,
            $activity_time,
            $activity_price
        );

        if (is_array($booking_results)) {

            $html = $this->bookingSummary(
                $bookingnumber,
                $booking_results[ 'choice' ][ 'activity_id' ],
                $booking_results[ 'choice' ][ 'activity_name' ],
                $booking_results[ 'date' ],
                $booking_results[ 'choice' ][ 'time' ],
                $booking_results[ 'adults' ],
                $booking_results[ 'children' ],
                $booking_results[ 'choice' ][ 'price' ],
            );

        } else {
            $html = '<div id="booking-summary-wrap" class="booking-summary-warning"><i class="fa-solid fa-circle-exclamation"></i>Session timed out. Please reload the page.</div>';
        }

        // Send the JSON response
        wp_send_json($html);
    }

    public function process_ActivityData(
        $bookingnumber = null,
        $activity_id = null,
        $activity_date = null,
        $activity_time = null,
        $activity_price = null
    ) {
        // Get the data sent via AJAX

        $activityName = $this->getActivityName_FromID($activity_id);

        $booking_results = atollmatrix_get_booking_transient($bookingnumber);

        // Perform any processing you need with the data
        // For example, you can save it to the database or perform calculations

        // Return a response (you can modify this as needed)
        $response = array(
            'success' => true,
            'message' => 'Data: ' . $activityName . ',received successfully.',
        );

        if (is_array($booking_results)) {

            error_log('====== From Transient ======');
            error_log(print_r($booking_results, true));
            
            $booking_results['bookingnumber'] = $bookingnumber;
            
            $booking_results['choice'][ 'activity_id' ] = $activity_id;
            $booking_results['choice'][ 'activity_name' ] = $activityName;
            $booking_results['choice'][ 'date' ] = $activity_date;
            $booking_results['choice'][ 'time' ] = $activity_time;
            $booking_results['choice'][ 'price' ] = $booking_results[$activity_id][$activity_time];

            atollmatrix_set_booking_transient($booking_results, $bookingnumber);

            error_log('====== Saved Activity Transient ======');
            error_log(print_r($booking_results, true));

            error_log('====== Specific Activity ======');
            error_log(print_r($booking_results[ 'choice' ], true));

        } else {
            $booking_results = false;
        }

        // Send the JSON response
        return $booking_results;
    }

    public function getActivityName_FromID($activity_id)
    {
        $activity_post = get_post($activity_id);
        if ($activity_post) {
            $activity_name = $activity_post->post_title;
        }

        return $activity_name;
    }

    public function bookingDataFields()
    {
        $dataFields = [
            'full_name'      => 'Full Name',
            'passport'       => 'Passport No',
            'email_address'  => 'Email Address',
            'phone_number'   => 'Phone Number',
            'street_address' => 'Street Address',
            'city'           => 'City',
            'state'          => 'State/Province',
            'zip_code'       => 'Zip Code',
            'country'        => 'Country',
            'guest_comment'  => 'Notes',
            'guest_consent'  => 'By clicking "Book this Room" you agree to our terms and conditions and privacy policy.',
         ];

        return $dataFields;
    }

    public function register_Guest_Form()
    {
        $country_options = atollmatrix_country_list("select", "");

        $html = '<div class="registration-column registration-column-two" id="booking-summary">';
        $html .= self::bookingSummary(
            $bookingnumber = '',
            $activity_id = '',
            $booking_results[ $activity_id ][ 'roomtitle' ] = '',
            $this->checkinDate,
            $this->staynights,
            $this->adultGuests,
            $this->childrenGuests,
            $perdayprice = '',
            $total = ''
        );
        $html .= '</div>';

        $bookingsuccess = self::booking_Successful();

        $formInputs = self::bookingDataFields();

        $form_html = <<<HTML
		<div class="registration_form_outer registration_request">
			<div class="registration_form_wrap">
				<div class="registration_form">
					<div class="registration-column registration-column-one registration_form_inputs">
                    <div class="booking-backto-activitychoice"><div class="booking-backto-roomchoice-inner"><i class="fa-solid fa-arrow-left"></i> Back</div></div>
                    <h3>Registration</h3>
                    <div class="form-group form-floating">
						<input placeholder="Full Name" type="text" class="form-control" id="full_name" name="full_name" required>
						<label for="full_name" class="control-label">$formInputs[full_name]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="Passport No." type="text" class="form-control" id="passport" name="passport" required>
						<label for="passport" class="control-label">$formInputs[passport]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="" type="email" class="form-control" id="email_address" name="email_address" required>
						<label for="email_address" class="control-label">$formInputs[email_address]</label>
					</div>
					<div class="form-group form-floating">
						<input placeholder="" type="tel" class="form-control" id="phone_number" name="phone_number" required>
						<label for="phone_number" class="control-label">$formInputs[phone_number]</label>
					</div>
                    <div class="form-group form-floating">
                        <input placeholder="" type="text" class="form-control" id="street_address" name="street_address">
                        <label for="street_address" class="control-label">$formInputs[street_address]</label>
                    </div>
                    <div class="form-group form-floating">
                        <input placeholder="" type="text" class="form-control" id="city" name="city">
                        <label for="city" class="control-label">$formInputs[city]</label>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group form-floating">
                                <input placeholder="" type="text" class="form-control" id="state" name="state">
                                <label for="state" class="control-label">$formInputs[state]</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group form-floating">
                                <input placeholder="" type="text" class="form-control" id="zip_code" name="zip_code">
                                <label for="zip_code" class="control-label">$formInputs[zip_code]</label>
                            </div>
                        </div>
                    </div>
					<div class="form-group form-floating">
						<select required placeholder="" class="form-control" id="country" name="country" >
						$country_options
						</select>
						<label for="country" class="control-label">$formInputs[country]</label>
					</div>
					<div class="form-group form-floating">
					<textarea placeholder="" class="form-control" id="guest_comment" name="guest_comment"></textarea>
					<label for="guest_comment" class="control-label">$formInputs[guest_comment]</label>
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

        return $form_html . $bookingsuccess;
    }

    public function booking_Successful()
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

    public function bookingSummary(
        $bookingnumber = null,
        $activity_id = null,
        $activity_name = null,
        $checkin = null,
        $time = null,
        $adults = null,
        $children = null,
        $totalrate = null
    ) {

        $totalguests = intval($adults) + intval($children);
        $totalprice  = array();

        $html = '<div id="booking-summary-wrap">';
        if ('' !== $activity_name) {
            $html .= '<div class="room-summary"><span class="summary-room-name">' . $activity_name . '</span></div>';
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
        $html .= '</div>';

        $html .= '<div class="stay-summary-wrap">';

        $html .= '<div class="summary-icon checkin-summary-icon"><i class="fa-regular fa-calendar-check"></i></div>';
        $html .= '<div class="summary-heading checkin-summary-heading">Activity Time:</div>';
        $html .= '<div class="checkin-summary">' . $checkin . '</div>';
        $html .= '<div class="checkin-summary">' . $time . '</div>';

        $html .= '<div class="summary-icon stay-summary-icon"><i class="fa-solid fa-moon"></i></div>';
        $html .= '</div>';

        if ('' !== $totalrate) {
            $subtotalprice = intval($totalrate);
            $html .= '<div class="price-summary-wrap">';

            if (atollmatrix_has_tax()) {
                $html .= '<div class="summary-heading total-summary-heading">Subtotal:</div>';
                $html .= '<div class="price-summary">' . atollmatrix_price($subtotalprice) . '</div>';
            }

            $html .= '<div class="summary-heading total-summary-heading">Total:</div>';

            $staynights = 1;
            $tax_instance = new \AtollMatrix\Tax('activities');
            $totalprice = $tax_instance->apply_tax($subtotalprice, $staynights, $totalguests, $output = 'html');
            foreach ($totalprice[ 'details' ] as $totalID => $totalvalue) {
                $html .= '<div class="tax-summary tax-summary-details">' . $totalvalue . '</div>';
            }

            $html .= '<div class="tax-summary tax-summary-total">' . atollmatrix_price($totalprice[ 'total' ]) . '</div>';
            $html .= '</div>';
        }

        if ('' !== $activity_id) {
            $html .= '<div class="form-group">';
            $html .= '<div id="bookingResponse" class="booking-response"></div>';
            $html .= '<div id="activity-register" class="book-button">Book this activity</div>';
            // $html .= self::paymentHelperButton($totalprice[ 'total' ], $bookingnumber);
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }


    function activity_content($content) {
        if (is_singular('atmx_activity')) {
            $custom_content = $this->activityBooking_SearchForm();
            $content = $custom_content . $content; // Prepend custom content
            // $content .= $custom_content; // Append custom content
        }
        return $content;
    }

    function activity_search_shortcode() {
        $search_form = $this->activityBooking_SearchForm();
        return $search_form;
    }

    public function getNameForActivity($reservation_id = false)
    {

        // Get room id from post meta
        $activity_id = get_post_meta($reservation_id, 'atollmatrix_activity_id', true);

        // If room id exists, get the room's post title
        if ($activity_id) {
            $acitivity_post = get_post($activity_id);
            if ($acitivity_post) {
                return $acitivity_post->post_title;
            }
        }

        return null;
    }

    public function getEditLinksForActivity($reservation_array)
    {
        $links = '<ul>';
        foreach ($reservation_array as $post_id) {
            $room_name = self::getNameForActivity($post_id);
            $edit_link = admin_url('post.php?post=' . $post_id . '&action=edit');
            $links .= '<li><p><a href="' . $edit_link . '" title="' . $room_name . '">Edit Reservation ' . $post_id . '<br/><small>' . $room_name . '</small></a></p></li>';
        }
        $links .= '</ul>';
        return $links;
    }

    /**
     * Summary of getReservationIDsForCustomer
     * @param mixed $customer_id
     * @return array
     */
    public static function getActivityIDsForCustomer($customer_id)
    {
        $args = array(
            'post_type'  => 'atmx_activityres',
            'meta_query' => array(
                array(
                    'key'     => 'atollmatrix_customer_id',
                    'value'   => $customer_id,
                    'compare' => '=',
                ),
            ),
        );
        $posts           = get_posts($args);
        $reservation_ids = array();
        foreach ($posts as $post) {
            $reservation_ids[  ] = $post->ID;
        }
        return $reservation_ids;
    }

    public function getGuest_id_forActivity($booking_number)
    {
        $args = array(
            'post_type'      => 'atmx_activityres',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'atollmatrix_customer_id', true);
            return $customer_id;
        }

        return false; // Return an empty query if no guest found
    }

    public function getGuestforActivity($booking_number)
    {
        $args = array(
            'post_type'      => 'atmx_activityres',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array(
                    'key'   => 'atollmatrix_booking_number',
                    'value' => $booking_number,
                ),
            ),
        );
        $reservation_query = new \WP_Query($args);

        if ($reservation_query->have_posts()) {
            $reservation = $reservation_query->posts[ 0 ];
            $customer_id = get_post_meta($reservation->ID, 'atollmatrix_customer_id', true);

            if (!empty($customer_id)) {
                $customer_args = array(
                    'post_type'   => 'atmx_customers',
                    'p'           => $customer_id,
                    'post_status' => 'publish',
                );
                return new \WP_Query($customer_args);
            }
        }

        return new \WP_Query(); // Return an empty query if no guest found
    }

    public function haveCustomer($reservation_id)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($reservation_id, 'atollmatrix_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return false;
        }

        // Query the customer post with the matching booking number
        $customer_query = $this->getGuestforActivity($booking_number);
        // error_log(print_r($customer_query, true));
        // Check if a customer post exists
        if ($customer_query->have_posts()) {
            // Restore the original post data
            wp_reset_postdata();

            // Return true if a matching customer post is found
            return true;
        }

        // No matching customer found, return false
        return false;
    }

    public function getReservation_Customer_ID($reservation_id = false)
    {

        if (!$reservation_id) {
            $reservation_id = $this->reservation_id;
        }
        // Get the booking number from the reservation post meta
        $booking_number = get_post_meta($reservation_id, 'atollmatrix_booking_number', true);

        if (!$booking_number) {
            // Handle error if booking number not found
            return '';
        }

        // Query the customer post with the matching booking number
        $customer_id = $this->getGuest_id_forActivity($booking_number);
        // No matching customer found
        return $customer_id;
    }

    public function activityBooking_SearchForm()
    {
        
        // Generate unique booking number
        atollmatrix_set_booking_transient('1', $this->bookingNumber);
        ob_start();
        $searchbox_nonce       = wp_create_nonce('atollmatrix-searchbox-nonce');
        $availabilityDateArray = array();

        // Calculate current date
        $currentDate = current_time('Y-m-d');
        // Calculate end date as 3 months from the current date
        $endDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));

        $fullybooked_dates = array();
        $display_fullbooked_status = false;
        if ( true === $display_fullbooked_status ) {
            $reservations_instance = new \AtollMatrix\Reservations();
            $fullybooked_dates     = $reservations_instance->daysFullyBooked_For_DateRange($currentDate, $endDate);    
        }
        // error_log( '-------------------- availability percent check');
        // error_log( print_r( $fullybooked_dates, true ));
        // error_log( '-------------------- availability percent check');
        ?>
		<div class="atollmatrix-content atollmatrix-activity-booking">
            <div id="hotel-booking-form">

                <div class="front-booking-search">
                    <div class="front-booking-calendar-wrap">
                        <div class="front-booking-calendar-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div class="front-booking-calendar-date">Choose activity date</div>
                    </div>
                    <div class="front-booking-guests-wrap">
                        <div class="front-booking-guests-container"> <!-- New container -->
                            <div class="front-booking-guest-adult-wrap">
                                <div class="front-booking-guest-adult-icon"><span class="guest-adult-svg"></span><span class="front-booking-adult-adult-value">2</span></div>
                            </div>
                            <div class="front-booking-guest-child-wrap">
                                <div class="front-booking-guest-child-icon"><span class="guest-child-svg"></span><span class="front-booking-adult-child-value">0</span></div>
                            </div>
                        </div>
                        <div id="activitySearch" class="form-search-button">Search</div>
                    </div>
                </div>


				<div class="atollmatrix_reservation_datepicker">
					<input type="hidden" name="atollmatrix_searchbox_nonce" value="<?php echo esc_attr($searchbox_nonce); ?>" />
					<input data-booked="<?php echo htmlspecialchars(json_encode($fullybooked_dates), ENT_QUOTES, 'UTF-8'); ?>" type="date" id="activity-reservation-date" name="reservation_date">
				</div>
                <div class="atollmatrix_reservation_room_guests_wrap">
                    <div id="atollmatrix_reservation_room_adults_wrap" class="number-input occupant-adult occupants-range">
                        <div class="column-one">
                            <label for="number-of-adults">Adults</label>
                        </div>
                        <div class="column-two">
                            <span class="minus-btn">-</span>
                            <input data-guest="adult" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-adults" value="2" name="number_of_adults" type="text" class="number-value" readonly="">
                            <span class="plus-btn">+</span>
                        </div>
                    </div>
                    <div id="atollmatrix_reservation_room_children_wrap" class="number-input occupant-child occupants-range">
                        <div class="column-one">
                            <label for="number-of-adults">Children</label>
                        </div>
                        <div class="column-two">
                            <span class="minus-btn">-</span>
                            <input data-childageinput="children_age[]" data-guest="child" data-guestmax="0" data-adultmax="0" data-childmax="0" id="number-of-children" value="0" name="number_of_children" type="text" class="number-value" readonly="">
                            <span class="plus-btn">+</span>
                        </div>

                    </div>
                    <div id="guest-age"></div>
                </div>
				<div class="recommended-alt-wrap">
					<div class="recommended-alt-title">Rooms unavailable</div><div class="recommended-alt-description">Following range from your selection is avaiable.</div>
					<div id="recommended-alt-dates"></div>
				</div>
			<div class="available-list">
				<div id="available-list-ajax"></div>
			</div>
		</div>
		</div>
		<?php
return ob_get_clean();
    }

    public function getActivities( $the_post_id ) {

        $activities = '';

        if (null !== get_post_meta($the_post_id, 'atollmatrix_reservation_checkin', true)) {
            $activity_date = get_post_meta($the_post_id, 'atollmatrix_reservation_checkin', true);
            
            // Check if $activity_date is a valid date
            if (strtotime($activity_date) !== false) {
                // Create an instance of the Activity class
                $reservedForGuests = $this->getActivityReservationNumbers( $the_post_id );
                $existing_spots_for_day = $reservedForGuests['total'];
                $activities .= '<div class="activity-schedules-container-wrap">';
                $this->display_activity_schedules_with_availability($activity_date, $the_post_id, $existing_spots_for_day); // Today's date
                $activities .= '</div>';
            } else {
                // Handle the case where $activity_date is not a valid date
                $activities .= '<div class="activity-schedules-container-wrap"></div>';
            }
        }

        return $activities;
    }

    public function displayTicket( $the_post_id, $activity_id ) {

        $ticket = '';

        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_time', true)) {

            $property_logo_id = atollmatrix_get_option('property_logo');
            $property_name    = atollmatrix_get_option('property_name');
            $property_phone   = atollmatrix_get_option('property_phone');
            $property_address = atollmatrix_get_option('property_address');
            $property_header  = atollmatrix_get_option('property_header');
            $property_footer  = atollmatrix_get_option('property_footer');

            $hotelLogo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

            $activity_id = get_post_meta($the_post_id, 'atollmatrix_activity_id', true);

            if (null !== $activity_id) {
                $activity_image = atollmatrix_featured_image_link($activity_id);
            }
            
            $booking_number = get_post_meta($the_post_id, 'atollmatrix_booking_number', true);
            $activity_date = get_post_meta($the_post_id, 'atollmatrix_reservation_checkin', true);
            
            $atollmatrix_customer_id = get_post_meta($the_post_id, 'atollmatrix_customer_id', true);
            $full_name = get_post_meta($atollmatrix_customer_id, 'atollmatrix_full_name', true);
            
            $ticket_price = get_post_meta($the_post_id, 'atollmatrix_reservation_total_room_cost', true);
            $booking_number = get_post_meta($the_post_id, 'atollmatrix_booking_number', true);
            $reservation_status = get_post_meta($the_post_id, 'atollmatrix_reservation_status', true);            

            $data_array = atollmatrix_get_select_target_options('activity_names');
            $time = get_post_meta($the_post_id, 'atollmatrix_activity_time', true);

            $reservedForGuests = $this->getActivityReservationNumbers( $the_post_id );
            $reservedTotal = $reservedForGuests['total'];

            if ( isset( $data_array[$activity_id] ) && isset($ticket_price) && 0 < $ticket_price ) {

                $ticket = '<div class="ticket-container-outer">';
                $ticket .= '<div data-file="'.$booking_number.'-'.$the_post_id.'" data-postid="'.$the_post_id.'" id="ticket-'.$booking_number.'" data-bookingnumber="'.$booking_number.'" class="ticket ticket-container">';
                $ticket .= '<div class="ticket-header">';
                $ticket .= '<p class="ticket-company">'.$property_name.'</p>';
                $ticket .= '<p class="ticket-phone">'.$property_phone.'</p>';
                $ticket .= '<p class="ticket-address">'.$property_address.'</p>';
                $ticket .= '<p class="ticket-break"></p>';
                $ticket .= '<h1>'.$data_array[$activity_id].'</h1>';
                $ticket .= '<p class="ticket-date">'.date("F jS Y", strtotime($activity_date)).'</p>';
                $ticket .= '</div>';
                $ticket .= '<div style="background: url('.esc_url($activity_image).'); background-size:cover" class="ticket-image">';
                $ticket .= '</div>';
                $ticket .= '<div class="ticket-info">';
                $ticket .= '<p>'.$reservedTotal . ' x <i class="fa-solid fa-user"></i></p>';
                $ticket .= '<p class="ticket-name">'.$full_name.'</p>';
                $ticket .= '<p class="ticket-time"><i class="fa-regular fa-clock"></i> '.$time . '</p>';
                $ticket .= '<p class="ticket-price">'.atollmatrix_price($ticket_price).'</p>';
                $ticket .= '<div id="ticketqrcode" data-qrcode="'.$booking_number.'" class="qrcode"></div>';
                $ticket .= '</div>';
                $ticket .= '<div class="ticket-button">'.$reservation_status.'</div>';
                $ticket .= '</div>';
                $ticket .= '</div>';
                
            }
        }
        
        return $ticket;
    }

    /**
     * Get the number of adults, children, and the total for a reservation.
     *
     * @param int $the_post_id The post ID of the reservation.
     * @return array An array containing the number of adults, children, and the total.
     */
    public function getActivityReservationNumbers($the_post_id) {
        $existing_adults = get_post_meta($the_post_id, 'atollmatrix_reservation_activity_adults', true);
        $existing_children_array = get_post_meta($the_post_id, 'atollmatrix_reservation_activity_children', true);
        $existing_children = is_array($existing_children_array) ? $existing_children_array['number'] : 0;

        // Set values to zero if they are empty
        $existing_adults = !empty($existing_adults) ? intval($existing_adults) : 0;
        $existing_children = !empty($existing_children) ? intval($existing_children) : 0;

        $existing_spots_for_day = $existing_adults + $existing_children;

        return [
            'adults' => $existing_adults,
            'children' => $existing_children,
            'total' => $existing_spots_for_day
        ];
    }

    public function display_activity_schedules_with_availability($selected_date = null, $the_post_id = false, $total_people = false) {
        // Use today's date if $selected_date is not provided
        if (is_null($selected_date)) {
            $selected_date = date('Y-m-d');
        }
        
        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_time', true)) {
            $existing_activity_time = get_post_meta($the_post_id, 'atollmatrix_activity_time', true);
        }
        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_id', true)) {
            $existing_activity_id = get_post_meta($the_post_id, 'atollmatrix_activity_id', true);
        }
    
        // Get the day of the week for the selected date
        $day_of_week = strtolower(date('l', strtotime($selected_date)));
    
        // Query all activity posts
        $args = array(
            'post_type' => 'atmx_activity',
            'posts_per_page' => -1,
        );
        $activities = new \WP_Query($args);
    
        // Start the container div
        echo '<div class="spinner"></div><div class="activity-schedules-container">';
    

        echo '<h3>' . ucfirst($day_of_week) . '</h3>';
        // Loop through each activity post
        if ($activities->have_posts()) {
            while ($activities->have_posts()) {
                $activities->the_post();
                $post_id = get_the_ID();

                $activity_schedule = get_post_meta($post_id, 'atollmatrix_activity_schedule', true);
                $max_guests = get_post_meta($post_id, 'atollmatrix_max_guests', true);
    
                // Display the activity identifier (e.g., post title)
                echo '<div class="activity-schedule" id="activity-schedule-' . $post_id . '">';

                echo '<h4>' . get_the_title() . '</h4>';
    
                // Display the time slots for the day of the week that matches the selected date
                if (!empty($activity_schedule) && isset($activity_schedule[$day_of_week])) {
                    echo '<div class="day-schedule">';
                    foreach ($activity_schedule[$day_of_week] as $index => $time) {
                        // Calculate remaining spots for this time slot
                        
                        $remaining_spots = $this->calculate_remaining_spots($post_id, $selected_date, $time, $max_guests);

                        $remaining_spots_compare = $remaining_spots;
                        $existing_found = false;

                        if ( $existing_activity_id == $post_id && $time == $existing_activity_time ) {

                            $reservedForGuests = $this->getActivityReservationNumbers( $the_post_id );
                            $existing_spots_for_day = $reservedForGuests['total'];

                            $remaining_spots_compare = $remaining_spots + $existing_spots_for_day;
                            $existing_found = true;
                        }
                        // echo $selected_date;
                        $active_class = "time-disabled";

                        if ( $total_people <= $remaining_spots_compare && 0 !== $remaining_spots && '' !== $time ) {
                            $active_class = "time-active";
                            if ( $existing_found ) {
                                $active_class .= ' time-choice';
                            }

                            echo '<span class="time-slot '.$active_class.'" id="time-slot-' . $day_of_week . '-' . $index . '" data-activity="'.$post_id.'" data-time="' . $time . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> ' . $time . '</span><span class="time-slots-remaining">( ' . $remaining_spots . ' of ' .$max_guests. ' remaining )</span></span> ';
                        } else {
                            echo '<span class="time-slot '.$active_class.'" id="time-slot-' . $day_of_week . '-' . $index . '" data-activity="'.$post_id.'" data-time="' . $time . '"><span class="activity-time-slot"><i class="fa-regular fa-clock"></i> Unavailable</span><span class="time-slots-remaining">( - of - )</span></span> ';
                        }
                       
                        
                    }
                    echo '</div>';
                }
    
                echo '</div>'; // Close the activity-schedule div
            }
        }
    
        // Close the container div
        echo '</div>';
    
        // Reset post data
        wp_reset_postdata();
    }
    
    public function calculate_remaining_spots($activity_id, $selected_date, $selected_time, $max_guests) {
        // Query all reservation posts for this activity, date, and time
        $args = array(
            'post_type' => 'atmx_activityres',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'atollmatrix_activity_id',
                    'value' => $activity_id,
                ),
                array(
                    'key' => 'atollmatrix_reservation_checkin',
                    'value' => $selected_date,
                ),
                array(
                    'key' => 'atollmatrix_activity_time',
                    'value' => $selected_time,
                ),
            ),
        );
        $reservations = new \WP_Query($args);
    
        // Calculate the total number of guests from the reservations
        $total_guests = 0;
        if ($reservations->have_posts()) {
            while ($reservations->have_posts()) {
                $reservations->the_post();
                $adults = get_post_meta(get_the_ID(), 'atollmatrix_reservation_activity_adults', true);
                $children = get_post_meta(get_the_ID(), 'atollmatrix_reservation_activity_children', true);
                $total_guests += $adults + $children['number'];
            }
        }
    
        wp_reset_postdata();
        // Calculate remaining spots
        $remaining_spots = $max_guests - $total_guests;
    
        return $remaining_spots;
    }

    public function buildReservationArray($booking_data)
    {
        $reservationArray = [  ];

        if (array_key_exists('bookingnumber', $booking_data)) {
            $reservationArray[ 'bookingnumber' ] = $booking_data[ 'bookingnumber' ];
        }
        if (array_key_exists('choice', $booking_data)) {
            $reservationArray[ 'date' ] = $booking_data[ 'choice' ]['date'];
        }
        if (array_key_exists('choice', $booking_data)) {
            $reservationArray[ 'activity_id' ] = $booking_data[ 'choice' ]['activity_id'];
        }
        if (array_key_exists('choice', $booking_data)) {
            $reservationArray[ 'time' ] = $booking_data[ 'choice' ]['time'];
        }
        if (array_key_exists('choice', $booking_data)) {
            $reservationArray[ 'price' ] = $booking_data[ 'choice' ]['price'];
        }
        if (array_key_exists('adults', $booking_data)) {
            $reservationArray[ 'adults' ] = $booking_data[ 'adults' ];
        }
        if (array_key_exists('children', $booking_data)) {
            $reservationArray[ 'children' ] = $booking_data[ 'children' ];
        }
        if (array_key_exists('children_age', $booking_data)) {
            $reservationArray[ 'children_age' ] = $booking_data[ 'children_age' ];
        }
        if (array_key_exists('person_total', $booking_data)) {
            $reservationArray[ 'person_total' ] = $booking_data[ 'person_total' ];
        }

        $reservationArray[ 'staynights' ] = 1;

        $currency = atollmatrix_get_option('currency');
        if (isset($currency)) {
            $reservationArray[ 'currency' ] = $currency;
        }

        $tax_instance = new \AtollMatrix\Tax('activities');

        $subtotalprice                  = intval($reservationArray[ 'price' ]);
        $reservationArray[ 'tax' ]      = $tax_instance->apply_tax($subtotalprice, $reservationArray[ 'staynights' ], $reservationArray[ 'person_total' ], $output = 'data');
        $reservationArray[ 'tax_html' ] = $tax_instance->apply_tax($subtotalprice, $reservationArray[ 'staynights' ], $reservationArray[ 'person_total' ], $output = 'html');

        $rateperperson                       = intval($subtotalprice) / intval($reservationArray[ 'person_total' ]);
        $rateperperson_rounded               = round($rateperperson, 2);
        $reservationArray[ 'rateperperson' ] = $rateperperson_rounded;
        $reservationArray[ 'subtotal' ]     = round($subtotalprice, 2);
        $reservationArray[ 'total' ]        = $reservationArray[ 'tax' ][ 'total' ];

        return $reservationArray;
    }


    // Ajax function to book rooms
    public function bookActivity()
    {

        error_log('------- acitvity posted data -------');
        error_log(print_r($_POST, true));

        $serializedData = $_POST[ 'bookingdata' ];
        // Parse the serialized data into an associative array
        parse_str($serializedData, $formData);

        error_log('------- acitvity posted deserialized data -------');
        error_log(print_r($formData, true));

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_roomlistingbox_nonce' ]) || !check_admin_referer('atollmatrix-roomlistingbox-nonce', 'atollmatrix_roomlistingbox_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        // Generate unique booking number
        $booking_number = sanitize_text_field($_POST[ 'booking_number' ]);
        $booking_data   = atollmatrix_get_booking_transient($booking_number);

        if (!isset($booking_data)) {
            wp_send_json_error('Invalid or timeout. Please try again');
        }
        // Obtain customer details from form submission
        $bookingdata    = sanitize_text_field($_POST[ 'bookingdata' ]);
        $booking_number = sanitize_text_field($_POST[ 'booking_number' ]);
        $full_name      = sanitize_text_field($_POST[ 'full_name' ]);
        $passport       = sanitize_text_field($_POST[ 'passport' ]);
        $email_address  = sanitize_email($_POST[ 'email_address' ]);
        $phone_number   = sanitize_text_field($_POST[ 'phone_number' ]);
        $street_address = sanitize_text_field($_POST[ 'street_address' ]);
        $city           = sanitize_text_field($_POST[ 'city' ]);
        $state          = sanitize_text_field($_POST[ 'state' ]);
        $zip_code       = sanitize_text_field($_POST[ 'zip_code' ]);
        $country        = sanitize_text_field($_POST[ 'country' ]);
        $guest_comment  = sanitize_text_field($_POST[ 'guest_comment' ]);
        $guest_consent  = sanitize_text_field($_POST[ 'guest_consent' ]);

        error_log('------- Transient acitvity Data -------');
        error_log($booking_number);
        error_log(print_r($booking_data, true));
        error_log('------- Transient acitvity Data End -------');
        // add other fields as necessary

        $rooms                      = array();
        $rooms[ '0' ][ 'id' ]       = $booking_data[ 'choice' ][ 'activity_id' ];
        $rooms[ '0' ][ 'quantity' ] = '1';
        $adults                     = $booking_data[ 'adults' ];
        $children                   = $booking_data[ 'children' ];

        $reservationData = self::buildReservationArray($booking_data);

        $reservationData[ 'customer' ][ 'full_name' ]      = $full_name;
        $reservationData[ 'customer' ][ 'passport' ]       = $passport;
        $reservationData[ 'customer' ][ 'email_address' ]  = $email_address;
        $reservationData[ 'customer' ][ 'phone_number' ]   = $phone_number;
        $reservationData[ 'customer' ][ 'street_address' ] = $street_address;
        $reservationData[ 'customer' ][ 'city' ]           = $city;
        $reservationData[ 'customer' ][ 'state' ]          = $state;
        $reservationData[ 'customer' ][ 'zip_code' ]       = $zip_code;
        $reservationData[ 'customer' ][ 'country' ]        = $country;
        $reservationData[ 'customer' ][ 'guest_comment' ]  = $guest_comment;
        $reservationData[ 'customer' ][ 'guest_consent' ]  = $guest_consent;

        error_log('------- Final acitvity Data -------');
        error_log(print_r($reservationData, true));
        error_log('------- Final acitvity Data End -------');

        // error_log(print_r($can_accomodate, true));
        // error_log("Rooms:");
        // error_log(print_r($rooms, true));

        //wp_send_json_error(' Temporary block for debugging ');
        // Create customer post
        $customer_post_data = array(
            'post_type' => 'atmx_customers', // Your custom post type for customers
            'post_title' => $full_name, // Set the customer's full name as post title
            'post_status' => 'publish', // The status you want to give new posts
            'meta_input' => array(
                'atollmatrix_full_name'      => $full_name,
                'atollmatrix_email_address'  => $email_address,
                'atollmatrix_phone_number'   => $phone_number,
                'atollmatrix_street_address' => $street_address,
                'atollmatrix_city'           => $city,
                'atollmatrix_state'          => $state,
                'atollmatrix_zip_code'       => $zip_code,
                'atollmatrix_country'        => $country,
                'atollmatrix_guest_comment'  => $guest_comment,
                'atollmatrix_guest_consent'  => $guest_consent,
                // add other meta data you need
            ),
        );

        // Insert the post
        $customer_post_id = wp_insert_post($customer_post_data);

        if (!$customer_post_id) {
            wp_send_json_error('Could not save Customer: ' . $customer_post_id);
            return;
        }

        $checkin  = $reservationData[ 'date' ];
        $room_id  = $reservationData[ 'activity_id' ];

        $children_array             = array();
        $children_array[ 'number' ] = $reservationData[ 'children' ];

        foreach ($reservationData[ 'children_age' ] as $key => $value) {
            $children_array[ 'age' ][  ] = $value;
        }

        $tax_status = 'excluded';
        $tax_html   = false;
        if (atollmatrix_has_activity_tax()) {
            $tax_status = 'enabled';
            $tax_instance = new \AtollMatrix\Tax('activities');
            $tax_html   = $tax_instance->tax_summary($reservationData[ 'tax_html' ][ 'details' ]);
        }

        $new_bookingstatus = atollmatrix_get_option('new_bookingstatus');
        if ('pending' !== $new_bookingstatus && 'confirmed' !== $new_bookingstatus) {
            $new_bookingstatus = 'pending';
        }
        $new_bookingsubstatus = atollmatrix_get_option('new_bookingsubstatus');
        if ('' !== $new_bookingstatus) {
            $new_bookingsubstatus = 'onhold';
        }

        $reservation_booking_uid = \AtollMatrix\Common::generateUuid();

        $signature = md5('atollmatrix_booking_system');

        $booking_channel = 'Atollmatrix';

        // Here you can also add other post data like post_title, post_content etc.
        $post_data = array(
            'post_type' => 'atmx_activityres', // Your custom post type
            'post_title' => $booking_number, // Set the booking number as post title
            'post_status' => 'publish', // The status you want to give new posts
            'meta_input' => array(
                'atollmatrix_activity_id'                        => $reservationData[ 'activity_id' ],
                'atollmatrix_reservation_status'             => $new_bookingstatus,
                'atollmatrix_reservation_substatus'          => $new_bookingsubstatus,
                'atollmatrix_reservation_checkin'                   => $checkin,
                'atollmatrix_activity_time'                   => $reservationData[ 'time' ],
                'atollmatrix_checkin_date'                   => $checkin,
                'atollmatrix_tax'                            => $tax_status,
                'atollmatrix_tax_html_data'                  => $tax_html,
                'atollmatrix_tax_data'                       => $reservationData[ 'tax' ],
                'atollmatrix_reservation_activity_adults'        => $reservationData[ 'adults' ],
                'atollmatrix_reservation_activity_children'      => $children_array,
                'atollmatrix_reservation_rate_per_person'     => $reservationData[ 'rateperperson' ],
                'atollmatrix_reservation_subtotal_activity_cost' => $reservationData[ 'subtotal' ],
                'atollmatrix_reservation_total_room_cost'    => $reservationData[ 'total' ],
                'atollmatrix_booking_number'                 => $booking_number,
                'atollmatrix_booking_uid'                    => $reservation_booking_uid,
                'atollmatrix_customer_id'                    => $customer_post_id,
                'atollmatrix_ics_signature'                  => $signature,
                'atollmatrix_booking_data'                   => $reservationData,
                'atollmatrix_booking_channel'                => $booking_channel,
            ),
        );

        // Insert the post
        $reservation_post_id = wp_insert_post($post_data);

        if ($reservation_post_id) {
            // Successfully created a reservation post
            $data_instance = new \AtollMatrix\Data();
            $data_instance->updateReservationsArray_On_Save($reservation_post_id, get_post($reservation_post_id), true);

            $roomName = \AtollMatrix\Rooms::getRoomName_FromID($room_id);

            $bookingDetails = [
                'guestName'      => $full_name,
                'bookingNumber'  => $booking_number,
                'roomTitle'      => $roomName,
                'checkinDate'    => $checkin,
                'adultGuests'    => $reservationData[ 'adults' ],
                'childrenGuests' => $reservationData[ 'children' ],
                'totalCost'      => $reservationData[ 'total' ],
            ];

            $email = new EmailDispatcher($email_address, 'Room Booking Confirmation for:' . $booking_number);
            $email->setHTMLContent()->setBookingConfirmationTemplate($bookingDetails);

            if ($email->send()) {
                // echo 'Confirmation email sent successfully to the guest.';
            } else {
                // echo 'Failed to send the confirmation email.';
            }

        } else {
            // Handle error
        }

        // Send a success response at the end of your function, if all operations are successful
        wp_send_json_success('Booking successfully registered.');
        wp_die();
    }
    

}

$instance = new \AtollMatrix\Activity();
