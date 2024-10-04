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

    /**
     * Method set_html_content
     *
     * @return void
     */
    public function set_html_content()
    {
        $this->headers[] = 'Content-Type: text/html; charset=UTF-8';
        return $this;
    }

    /**
     * Method addAttachment
     *
     * @param $path
     *
     * @return void
     */
    public function addAttachment($path)
    {
        $this->attachments[] = $path;
        return $this;
    }

    /**
     * Method setRegistrationTemplate
     *
     * @param $registration_data
     * @param $registration_post_id
     *
     * @return void
     */
    public function setRegistrationTemplate($registration_data, $registration_post_id)
    {

        $emailMessage = '';

        if (is_array($registration_data) && !empty($registration_data)) {
            foreach ($registration_data as $info_key => $info_value) {
                // Skip the registration_id in the inner loop since it's handled separately
                if ($info_key != 'registration_id') {
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
            $res_reg_ids = $guestRegistry->fetch_res_reg_ids_by_booking_number($registry_bookingnumber);

            $reservationID = $res_reg_ids['reservationID'];
            $registerID = $res_reg_ids['guestRegisterID'];

            $reservationID_edit_link = get_edit_post_link($reservationID);

            $emailMessage .= '<p><a href="' . esc_url($post_edit_link) . '">' . __('View Guest Registration', 'staylodgic') . '</a></p>';
            $emailMessage .= '<p><a href="' . esc_url($reservationID_edit_link) . '">' . __('View Booking', 'staylodgic') . '</a></p>';
        }

        $this->message = $emailMessage;

        return $this;
    }

    /**
     * Method setBookingConfirmationTemplate
     *
     * @param $booking_details
     *
     * @return void
     */
    public function setBookingConfirmationTemplate($booking_details)
    {

        $total_price = staylodgic_price($booking_details['totalCost']);
        $property_emailfooter = staylodgic_get_option('property_emailfooter');
        $property_emailfooter_formatted = nl2br($property_emailfooter);

        $emailMessage  = '<h1>' . __('Thank you for your reservation', 'staylodgic') . ', ' . esc_html($booking_details['guestName']) . '</h1>';
        $emailMessage .= '<p>' . __('We have recieved your booking.', 'staylodgic') . '</p>';
        $emailMessage .= '<h2>' . __('Booking Details', 'staylodgic') . '</h2>';
        $emailMessage .= '<p><strong>' . __('Booking Number:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_booking_number']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Name:', 'staylodgic') . '</strong> ' . esc_html($booking_details['guestName']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Room:', 'staylodgic') . '</strong> ' . esc_html($booking_details['roomTitle']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Meal Plan:', 'staylodgic') . '</strong> ' . esc_html($booking_details['mealplan']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Included Meal Plans:', 'staylodgic') . '</strong> ' . esc_html($booking_details['included_mealplan']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Check-in Date:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_checkin_date']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Check-out Date:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_checkout_date']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Adults:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_adult_guests']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Children:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_children_guests']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Subtotal:', 'staylodgic') . '</strong> ' . $booking_details['subtotal'] . '</p>';
        if ($booking_details['tax']) {
            $emailMessage .= '<p><strong>' . __('Tax:', 'staylodgic') . '</strong></p>';
            foreach ($booking_details['tax'] as $total_id => $totalvalue) {
                $emailMessage .= '<p>' . wp_kses($totalvalue, staylodgic_get_allowed_tags()) . '</p>';
            }
        }
        $emailMessage .= '<p><strong>' . __('Total Cost:', 'staylodgic') . '</strong> ' . $total_price . '</p>';
        $emailMessage .= '<p>' . __('We look forward to welcoming you and ensuring a pleasant stay.', 'staylodgic') . '</p>';
        $emailMessage .= '<p>' . __('Please contact us to cancel, modify or if there are any questions regarding the booking.', 'staylodgic') . '</p>';
        $emailMessage .= '<p>' . $property_emailfooter_formatted . '</p>';

        $this->message = $emailMessage;
        return $this;
    }

    /**
     * Method set_activity_confirmation_template
     *
     * @param $booking_details
     *
     * @return void
     */
    public function set_activity_confirmation_template($booking_details)
    {

        $total_price = staylodgic_price($booking_details['totalCost']);
        $activity_emailfooter = staylodgic_get_option('activity_property_emailfooter');
        $activity_emailfooter_formatted = nl2br($activity_emailfooter);

        $emailMessage  = '<h1>' . __('Thank you for your reservation', 'staylodgic') . ', ' . esc_html($booking_details['guestName']) . '</h1>';
        $emailMessage .= '<p>' . __('We have recieved your booking.', 'staylodgic') . '</p>';
        $emailMessage .= '<h2>' . __('Booking Details', 'staylodgic') . '</h2>';
        $emailMessage .= '<p><strong>' . __('Booking Number:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_booking_number']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Name:', 'staylodgic') . '</strong> ' . esc_html($booking_details['guestName']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Activity Name:', 'staylodgic') . '</strong> ' . esc_html($booking_details['roomTitle']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Activity Date:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_checkin_date']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Adults:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_adult_guests']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Children:', 'staylodgic') . '</strong> ' . esc_html($booking_details['stay_children_guests']) . '</p>';
        $emailMessage .= '<p><strong>' . __('Subtotal:', 'staylodgic') . '</strong> ' . $booking_details['subtotal'] . '</p>';
        if ($booking_details['tax']) {
            $emailMessage .= '<p><strong>' . __('Tax:', 'staylodgic') . '</strong></p>';
            foreach ($booking_details['tax'] as $total_id => $totalvalue) {
                $emailMessage .= '<p>' . wp_kses($totalvalue, staylodgic_get_allowed_tags()) . '</p>';
            }
        }
        $emailMessage .= '<p><strong>' . __('Total Cost:', 'staylodgic') . '</strong> ' . $total_price . '</p>';
        $emailMessage .= '<p>' . __('Thank you for choosing our services.', 'staylodgic') . '</p>';
        $emailMessage .= '<p>' . __('Should you need any further information or wish to make specific arrangements, please feel free to contact us. We are here to assist you!', 'staylodgic') . '</p>';
        $emailMessage .= '<p>' . $activity_emailfooter_formatted . '</p>';

        $this->message = $emailMessage;
        return $this;
    }

    /**
     * Method send
     *
     * @param $cc
     *
     * @return void
     */
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

        return wp_mail($this->to, $this->subject, $this->message, $headers_string, $this->attachments);
    }
}
