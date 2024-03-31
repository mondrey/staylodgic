<?php
namespace Staylodgic;

class IcalExportProcessor
{
    private $batchSize = 50;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_booking_admin_menu')); // This now points to the add_admin_menu function

        add_action('admin_menu', array($this, 'add_booking_admin_menu'));
        add_action('wp_ajax_download_ical', array($this, 'ajax_download_reservations_ical'));
    
    }

    public function ajax_download_reservations_ical() {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }
        
        $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : false;
        if ($room_id) {
            $this->download_reservations_ical($room_id);
        }
        wp_die(); // this is required to terminate immediately and return a proper response
    }    

    public function add_booking_admin_menu()
    {
        add_submenu_page(
            'staylodgic',
            // This is the slug of the parent menu
            'Export iCal Bookings',
            'Export iCal Bookings',
            'manage_options',
            'export-booking-ical',
            array($this, 'ical_export')
        );
    }

    public function ical_export()
    {
        // The HTML content of the 'Staylodgic' page goes here
        echo "<h1>Export ICS Calendar</h1>";

        echo "<form id='room_ical_form' method='post'>";
        echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';

        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            // Get meta
            $room_ical_data = get_post_meta($room->ID, 'room_ical_data', true);

            echo '<div class="room_ical_export_wrapper" data-room-id="' . $room->ID . '">';
            echo "<h2>" . $room->post_title . "</h2>";
            echo '<button data-room-id="' . $room->ID . '" type="button" class="download_export_ical">Download</button>';
            echo '</div>';
        }
        echo "</form>";
    }

    public function generate_ical_from_reservations($reservations) {
        $ical = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Your Company//Your Calendar//EN\r\n";
    
        foreach ($reservations as $reservation) {
            $checkin_date = get_post_meta($reservation->ID, 'staylodgic_checkin_date', true);
            $checkout_date = get_post_meta($reservation->ID, 'staylodgic_checkout_date', true);
            $booking_number = get_post_meta($reservation->ID, 'staylodgic_booking_number', true);
            $reservation_status = get_post_meta($reservation->ID, 'staylodgic_reservation_status', true);
    
            // Format dates for iCal
            $checkin_date_ical = $this->format_date_for_ical($checkin_date);
            $checkout_date_ical = $this->format_date_for_ical($checkout_date);
    
            if ($checkin_date_ical && $checkout_date_ical) {
                $ical .= "BEGIN:VEVENT\r\n";
                $ical .= "UID:" . $booking_number . "\r\n";
                $ical .= "DTSTART:" . $checkin_date_ical . "\r\n";
                $ical .= "DTEND:" . $checkout_date_ical . "\r\n";
                $ical .= "SUMMARY:" . $reservation_status . "\r\n";
                $ical .= "END:VEVENT\r\n";
            }
        }
    
        $ical .= "END:VCALENDAR\r\n";
    
        return $ical;
    }
    
    private function format_date_for_ical($date) {
        if (!$date) return false;
        $timestamp = strtotime($date);
        return $timestamp ? date('Ymd\THis', $timestamp) : false;
    }
    
    public function download_reservations_ical($room_id) {
        $reservation_instance = new \Staylodgic\Reservations( $dateString = '', $room_id );
        $reservations_query = $reservation_instance->getReservationsForRoom(false, false, false, false, $room_id);
        
        // Extract post objects from WP_Query
        $reservations = $reservations_query->posts;
    
        $ical_content = $this->generate_ical_from_reservations($reservations);
    
        $currentDateTime = date('Y-m-d_H-i-s'); // Formats the date and time as YYYY-MM-DD_HH-MM-SS
        $filename = "reservations-{$room_id}-{$currentDateTime}.ics";
    
        header('Content-Type: text/calendar; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $ical_content;
        exit;
    }    
    

}

// Instantiate the class
$IcalExportProcessor = new IcalExportProcessor();
