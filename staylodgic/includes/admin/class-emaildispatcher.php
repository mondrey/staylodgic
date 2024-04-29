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

    public function setBookingConfirmationTemplate($bookingDetails)
    {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $property_emailfooter = staylodgic_get_option('property_emailfooter');
        $property_emailfooter_formatted = nl2br($property_emailfooter);

        $emailMessage  = '<h1>Thank you for your reservation, '.esc_html($bookingDetails['guestName']).'</h1>';
        $emailMessage .= '<p>We have recieved your booking.</p>';
        $emailMessage .= '<h2>Booking Details</h2>';
        $emailMessage .= '<p><strong>Booking Number:</strong> '.esc_html($bookingDetails['bookingNumber']).'</p>';
        $emailMessage .= '<p><strong>Room:</strong> '.esc_html($bookingDetails['roomTitle']).'</p>';
        $emailMessage .= '<p><strong>Check-in Date:</strong> '.esc_html($bookingDetails['checkinDate']).'</p>';
        $emailMessage .= '<p><strong>Check-out Date:</strong> '.esc_html($bookingDetails['checkoutDate']).'</p>';
        $emailMessage .= '<p><strong>Adults:</strong> '.esc_html($bookingDetails['adultGuests']).'</p>';
        $emailMessage .= '<p><strong>Children:</strong> '.esc_html($bookingDetails['childrenGuests']).'</p>';
        $emailMessage .= '<p><strong>Total Cost:</strong> '.$total_price.'</p>';
        $emailMessage .= '<p>We look forward to welcoming you and ensuring a pleasant stay.</p>';
        $emailMessage .= '<p>Please contact us to cancel, modify or if there are any questions regarding the booking.</p>';
        $emailMessage .= '<p>'.$property_emailfooter_formatted.'</p>';

        $this->message = $emailMessage;
        return $this;
    }

    public function setActivityConfirmationTemplate($bookingDetails)
    {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $activity_emailfooter = staylodgic_get_option('activity_property_emailfooter');
        $activity_emailfooter_formatted = nl2br($activity_emailfooter);

        $emailMessage  = '<h1>Thank you for your reservation, '.esc_html($bookingDetails['guestName']).'</h1>';
        $emailMessage .= '<p>We have recieved your booking.</p>';
        $emailMessage .= '<h2>Booking Details</h2>';
        $emailMessage .= '<p><strong>Booking Number:</strong> '.esc_html($bookingDetails['bookingNumber']).'</p>';
        $emailMessage .= '<p><strong>Activity Name:</strong> '.esc_html($bookingDetails['roomTitle']).'</p>';
        $emailMessage .= '<p><strong>Activity Date:</strong> '.esc_html($bookingDetails['checkinDate']).'</p>';
        $emailMessage .= '<p><strong>Adults:</strong> '.esc_html($bookingDetails['adultGuests']).'</p>';
        $emailMessage .= '<p><strong>Children:</strong> '.esc_html($bookingDetails['childrenGuests']).'</p>';
        $emailMessage .= '<p><strong>Total Cost:</strong> '.$total_price.'</p>';
        $emailMessage .= '<p>Thank you for choosing our services.</p>';
        $emailMessage .= '<p>Should you need any further information or wish to make specific arrangements, please feel free to contact us. We are here to assist you!</p>';
        $emailMessage .= '<p>'.$activity_emailfooter_formatted.'</p>';

        $this->message = $emailMessage;
        return $this;
    }

    public function send()
    {
        return wp_mail($this->to, $this->subject, $this->message, $this->headers, $this->attachments);
    }

}
