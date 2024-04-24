<?php
namespace Staylodgic;

class EmailDispatcher {

    private $to;
    private $subject;
    private $message;
    private $headers;
    private $attachments;

    public function __construct($to, $subject) {
        $this->to = $to;
        $this->subject = $subject;
        $this->headers = array();
        $this->attachments = array();
    }

    public function setHTMLContent() {
        $this->headers[] = 'Content-Type: text/html; charset=UTF-8';
        return $this;
    }

    public function addAttachment($path) {
        $this->attachments[] = $path;
        return $this;
    }

    public function setBookingConfirmationTemplate($bookingDetails) {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $property_emailfooter = staylodgic_get_option('property_emailfooter');
        $property_emailfooter_formatted = nl2br($property_emailfooter);

        $emailMessage = "<h1>Thank you for your reservation, {$bookingDetails['guestName']}!</h1>";
        $emailMessage .= "<p>We have recieved your booking.</p>";
        $emailMessage .= "<h2>Booking Details</h2>";
        $emailMessage .= "<p><strong>Booking Number:</strong> {$bookingDetails['bookingNumber']}</p>";
        $emailMessage .= "<p><strong>Room:</strong> {$bookingDetails['roomTitle']}</p>";
        $emailMessage .= "<p><strong>Check-in Date:</strong> {$bookingDetails['checkinDate']}</p>";
        $emailMessage .= "<p><strong>Check-out Date:</strong> {$bookingDetails['checkoutDate']}</p>";
        $emailMessage .= "<p><strong>Adults:</strong> {$bookingDetails['adultGuests']}</p>";
        $emailMessage .= "<p><strong>Children:</strong> {$bookingDetails['childrenGuests']}</p>";
        $emailMessage .= "<p><strong>Total Cost:</strong> {$total_price}</p>";
        $emailMessage .= "<p>We look forward to welcoming you and ensuring a pleasant stay.</p>";
        $emailMessage .= "<p>{$property_emailfooter_formatted}</p>";

        $this->message = $emailMessage;
        return $this;
    }

    public function setActivityConfirmationTemplate($bookingDetails) {

        $total_price = staylodgic_price($bookingDetails['totalCost']);
        $activity_emailfooter = staylodgic_get_option('activity_property_emailfooter');
        $activity_emailfooter_formatted = nl2br($activity_emailfooter);

        $emailMessage = "<h1>Thank you for your reservation, {$bookingDetails['guestName']}!</h1>";
        $emailMessage .= "<p>We have recieved your booking.</p>";
        $emailMessage .= "<h2>Booking Details</h2>";
        $emailMessage .= "<p><strong>Booking Number:</strong> {$bookingDetails['bookingNumber']}</p>";
        $emailMessage .= "<p><strong>Activity Name:</strong> {$bookingDetails['roomTitle']}</p>";
        $emailMessage .= "<p><strong>Activity Date:</strong> {$bookingDetails['checkinDate']}</p>";
        $emailMessage .= "<p><strong>Adults:</strong> {$bookingDetails['adultGuests']}</p>";
        $emailMessage .= "<p><strong>Children:</strong> {$bookingDetails['childrenGuests']}</p>";
        $emailMessage .= "<p><strong>Total Cost:</strong> {$total_price}</p>";
        $emailMessage .= "<p>We look forward to welcoming you and ensuring a pleasant stay.</p>";
        $emailMessage .= "<p>{$activity_emailfooter_formatted}</p>";

        $this->message = $emailMessage;
        return $this;
    }

    public function send() {
        return wp_mail($this->to, $this->subject, $this->message, $this->headers, $this->attachments);
    }

    // $email = new EmailDispatcher('recipient@example.com', 'Simple Email', 'This is a simple email.');
    // if ($email->send()) {
    //     echo 'Email sent successfully.';
    // } else {
    //     echo 'Failed to send email.';
    // }

    // $htmlMessage = '<html><body><h1>Hello, World!</h1><p>This is an HTML email.</p></body></html>';
    // $email = new EmailDispatcher('recipient@example.com', 'HTML Email', $htmlMessage);
    // $email->setHTMLContent();

    // if ($email->send()) {
    //     echo 'HTML email sent successfully.';
    // } else {
    //     echo 'Failed to send HTML email.';
    // }

    // $email = new EmailDispatcher('recipient@example.com', 'Email with Attachment', 'This email contains attachments.');
    // $email->addAttachment('/path/to/file1.jpg')
    //       ->addAttachment('/path/to/file2.pdf');

    // if ($email->send()) {
    //     echo 'Email with attachments sent successfully.';
    // } else {
    //     echo 'Failed to send email with attachments.';
    // }
    
}
