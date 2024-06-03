<?php

namespace Staylodgic;

class EmailDispatcher
{

    private $to;
    private $subject;
    private $message;
    private $headers;
    private $attachments;

    public function __construct($to, $subject)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->headers = array();
        $this->attachments = array();
    }

    public function setHTMLContent()
    {
        $this->headers[] = 'Content-Type: text/html; charset=UTF-8';
        return $this;
    }

    public function addAttachment($path)
    {
        $this->attachments[] = $path;
        return $this;
    }

    public function setRegistrationTemplate($registration_data, $registration_post_id)
    {

        error_log('Registration Data');
        error_log(print_r($registration_data, true));

        $emailMessage = '';

        if (is_array($registration_data) && !empty($registration_data)) {
            foreach ($registration_data as $info_key => $info_value) {
                // Skip the registration_id in the inner loop since it's handled separately
                if ($info_key != 'registration_id') {
                    error_log($info_value['type']);
                    error_log($info_value['value']);
                    if ($info_key == 'countries') {
                        $info_value['value'] = staylodgic_country_list('display', $info_value['value']);
                    }
                    if ($info_value['type'] == 'checkbox' && 'true' == $info_value['value']) {
                        $info_value['value'] = 'Yes';
                    }
                    if ($info_value['type'] == 'datetime-local') {
                        $date = new \DateTime($info_value['value']);
                        $formattedDate = $date->format('l, F j, Y g:i A');
                        $info_value['value'] = $formattedDate;
                    }

                    $emailMessage .= '<strong><span class="registration-label">' . esc_html($info_value['label']) . ':</span></strong> <span class="registration-data">' . esc_html($info_value['value']) . '</span></p>';
                } else {
                    if (isset($guest_data['registration_id'])) {
                        $registration_id = $guest_data['registration_id'];
                        $emailMessage .= '<strong><span class="registration-label">Registration ID:</span></strong> <span class="registration-data">' . esc_html($registration_id) . '</span></p>';
                    }
                }
            }
            $post_edit_link = get_edit_post_link($registration_post_id);
            $registry_bookingnumber = get_post_meta($registration_post_id, 'staylodgic_registry_bookingnumber', true);

            $guestRegistry = new GuestRegistry();
            $resRegIDs = $guestRegistry->fetchResRegIDsByBookingNumber($registry_bookingnumber);

            $reservationID = $resRegIDs['reservationID'];
            $registerID = $resRegIDs['guestRegisterID'];

            $reservationID_edit_link = get_edit_post_link($reservationID);

            $emailMessage .= '<p><a href="' . esc_url($post_edit_link) . '">View Guest Registration</a></p>';
            $emailMessage .= '<p><a href="' . esc_url($reservationID_edit_link) . '">View Booking</a></p>';
        }

        error_log('Registration Email');
        error_log($emailMessage);

        $this->message = $emailMessage;

        return $this;
    }

    public function setBookingConfirmationTemplate($bookingDetails)
    {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $property_emailfooter = staylodgic_get_option('property_emailfooter');
        $property_emailfooter_formatted = nl2br($property_emailfooter);

        $emailMessage  = '<h1>Thank you for your reservation, ' . esc_html($bookingDetails['guestName']) . '</h1>';
        $emailMessage .= '<p>We have recieved your booking.</p>';
        $emailMessage .= '<h2>Booking Details</h2>';
        $emailMessage .= '<p><strong>Booking Number:</strong> ' . esc_html($bookingDetails['bookingNumber']) . '</p>';
        $emailMessage .= '<p><strong>Name:</strong> ' . esc_html($bookingDetails['guestName']) . '</p>';
        $emailMessage .= '<p><strong>Room:</strong> ' . esc_html($bookingDetails['roomTitle']) . '</p>';
        $emailMessage .= '<p><strong>Meal Plan:</strong> ' . esc_html($bookingDetails['mealplan']) . '</p>';
        $emailMessage .= '<p><strong>Included Meal Plans:</strong> ' . esc_html($bookingDetails['included_mealplan']) . '</p>';
        $emailMessage .= '<p><strong>Check-in Date:</strong> ' . esc_html($bookingDetails['checkinDate']) . '</p>';
        $emailMessage .= '<p><strong>Check-out Date:</strong> ' . esc_html($bookingDetails['checkoutDate']) . '</p>';
        $emailMessage .= '<p><strong>Adults:</strong> ' . esc_html($bookingDetails['adultGuests']) . '</p>';
        $emailMessage .= '<p><strong>Children:</strong> ' . esc_html($bookingDetails['childrenGuests']) . '</p>';
        $emailMessage .= '<p><strong>Subtotal:</strong> ' . $bookingDetails['subtotal'] . '</p>';
        if ($bookingDetails['tax']) {
            $emailMessage .= '<p><strong>Tax:</strong></p>';
            foreach ($bookingDetails['tax'] as $totalID => $totalvalue) {
                $emailMessage .= '<p>' . wp_kses($totalvalue, staylodgic_get_allowed_tags()) . '</p>';
            }
        }
        $emailMessage .= '<p><strong>Total Cost:</strong> ' . $total_price . '</p>';
        $emailMessage .= '<p>We look forward to welcoming you and ensuring a pleasant stay.</p>';
        $emailMessage .= '<p>Please contact us to cancel, modify or if there are any questions regarding the booking.</p>';
        $emailMessage .= '<p>' . $property_emailfooter_formatted . '</p>';

        $this->message = $emailMessage;
        return $this;
    }

    public function setActivityConfirmationTemplate($bookingDetails)
    {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $activity_emailfooter = staylodgic_get_option('activity_property_emailfooter');
        $activity_emailfooter_formatted = nl2br($activity_emailfooter);

        $emailMessage  = '<h1>Thank you for your reservation, ' . esc_html($bookingDetails['guestName']) . '</h1>';
        $emailMessage .= '<p>We have recieved your booking.</p>';
        $emailMessage .= '<h2>Booking Details</h2>';
        $emailMessage .= '<p><strong>Booking Number:</strong> ' . esc_html($bookingDetails['bookingNumber']) . '</p>';
        $emailMessage .= '<p><strong>Name:</strong> ' . esc_html($bookingDetails['guestName']) . '</p>';
        $emailMessage .= '<p><strong>Activity Name:</strong> ' . esc_html($bookingDetails['roomTitle']) . '</p>';
        $emailMessage .= '<p><strong>Activity Date:</strong> ' . esc_html($bookingDetails['checkinDate']) . '</p>';
        $emailMessage .= '<p><strong>Adults:</strong> ' . esc_html($bookingDetails['adultGuests']) . '</p>';
        $emailMessage .= '<p><strong>Children:</strong> ' . esc_html($bookingDetails['childrenGuests']) . '</p>';
        $emailMessage .= '<p><strong>Subtotal:</strong> ' . $bookingDetails['subtotal'] . '</p>';
        if ($bookingDetails['tax']) {
            $emailMessage .= '<p><strong>Tax:</strong></p>';
            foreach ($bookingDetails['tax'] as $totalID => $totalvalue) {
                $emailMessage .= '<p>' . wp_kses($totalvalue, staylodgic_get_allowed_tags()) . '</p>';
            }
        }
        $emailMessage .= '<p><strong>Total Cost:</strong> ' . $total_price . '</p>';
        $emailMessage .= '<p>Thank you for choosing our services.</p>';
        $emailMessage .= '<p>Should you need any further information or wish to make specific arrangements, please feel free to contact us. We are here to assist you!</p>';
        $emailMessage .= '<p>' . $activity_emailfooter_formatted . '</p>';

        $this->message = $emailMessage;
        return $this;
    }

    public function send($cc = true)
    {
        if ($cc) {

            $cc_email = staylodgic_getLoggedInUserEmail();
            if ($cc_email) {
                $this->headers[] = 'Cc: ' . $cc_email;
            }
        }

        // Ensure the content type is set to HTML
        $this->headers[] = 'Content-Type: text/html; charset=UTF-8';

        // Add the font styling to the message
        $font_family = 'font-family: Helvetica, Arial, sans-serif;';
        $this->message = '<div style="' . $font_family . '">' . $this->message . '</div>';

        // Convert headers array to string format for wp_mail
        $headers_string = implode("\r\n", $this->headers);

        error_log('Sending Email');
        error_log($this->to);
        error_log($this->subject);
        error_log($this->message);
        error_log($headers_string);

        return wp_mail($this->to, $this->subject, $this->message, $headers_string, $this->attachments);
    }
}
