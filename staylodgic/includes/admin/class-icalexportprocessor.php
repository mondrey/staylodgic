<?php

namespace Staylodgic;

class IcalExportProcessor
{
    private $batchSize = 50;

    public function __construct()
    {

        add_action('admin_menu', array($this, 'export_csv_bookings'));
        add_action('admin_menu', array($this, 'export_csv_registrations'));
        add_action('wp_ajax_download_ical', array($this, 'ajax_download_reservations_csv'));
        add_action('wp_ajax_download_registrations_ical', array($this, 'ajax_download_guest_registrations_csv'));
    }

    public function ajax_download_guest_registrations_csv()
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

        $month = isset($_POST['month']) ? $_POST['month'] : false;
        error_log('init month is ' . $month);
        if ($month) {
            $this->download_guest_registrations_csv($month);
        }
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public function ajax_download_reservations_csv()
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

        $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : false;
        $month = isset($_POST['month']) ? $_POST['month'] : false;
        error_log('init month is ' . $month);
        if ($room_id) {
            $this->download_reservations_csv($room_id, $month);
        }
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public function export_csv_registrations()
    {
        add_submenu_page(
            'staylodgic-settings',
            // This is the slug of the parent menu
            __('Export Guest Registrations', 'staylodgic'),
            __('Export Guest Registrations', 'staylodgic'),
            'manage_options',
            'slgc-export-registrations-ical',
            array($this, 'csv_registrations_export')
        );
    }

    public function export_csv_bookings()
    {
        add_submenu_page(
            'staylodgic-settings',
            // This is the slug of the parent menu
            __('Export Bookings', 'staylodgic'),
            __('Export Bookings', 'staylodgic'),
            'manage_options',
            'slgc-export-booking-ical',
            array($this, 'csv_bookings_export')
        );
    }

    public function csv_registrations_export()
    {
        // The HTML content of the 'Staylodgic' page goes here
        echo '<div class="expor-import-calendar">';
        echo '<div id="export-import-form">';
        echo '<h1>' . __('Export Guest Registrations', 'staylodgic') . '</h1>';
        echo '<p>' . __('Streamline your record management by exporting monthly guest registrations. Simply choose a month and click "Download" to create a CSV file containing detailed registration information.', 'staylodgic') . '</p>';
        echo '<div class="how-to-admin">';
        echo '<h2>' . __('How to Export:', 'staylodgic') . '</h2>';
        echo '<ol>';
        echo '<li>' . __('Choose the month for which you want to export guest registrations.', 'staylodgic') . '</li>';
        echo '<li>' . __('Click the "Donwload" button to download your file.', 'staylodgic') . '</li>';
        echo '</ol>';
        echo '</div>';

        echo "<form id='room_ical_form' method='post'>";
        echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';

        echo '<div class="import-export-heading">Choose calendar month for export</div>';
        echo '<input type="text" class="exporter_calendar" id="exporter_calendar" name="exporter_calendar" value="" />';
        echo '<div class="exporter_calendar-error-wrap">';
        echo '<div class="exporter_calendar-no-records">' . __('No Records Found', 'staylodgic') . '</div>';
        echo '</div>';
        echo '<div class="import-export-wrap">';

        echo '<button type="button" class="download_registrations_export_ical btn btn-primary">';
        echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
        echo '<span role="status"> ' . __('Download', 'staylodgic') . '</span>';
        echo '</button>';

        echo "</div>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }

    public function csv_bookings_export()
    {
        // The HTML content of the 'Staylodgic' page goes here
        echo '<div class="expor-import-calendar">';
        echo '<div id="export-import-form">';
        echo '<h1>' . __('Export Bookings', 'staylodgic') . '</h1>';
        echo '<p>' . __('Efficiently manage your records by exporting your room bookings. Select the room and month to generate a downloadable CSV file of the booking details.', 'staylodgic') . '</p>';
        echo '<div class="how-to-admin">';
        echo '<h2>' . __('How to Export:', 'staylodgic') . '</h2>';
        echo '<ol>';
        echo '<li>' . __('Choose the month for which you want to export bookings.', 'staylodgic') . '</li>';
        echo '<li>' . __('Click the "Donwload" button next to the choice of room to download your file.', 'staylodgic') . '</li>';
        echo '</ol>';
        echo '</div>';

        echo "<form id='room_ical_form' method='post'>";
        echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';

        echo '<div class="import-export-heading">Choose calendar month for export</div>';
        echo '<input type="text" class="exporter_calendar" id="exporter_calendar" name="exporter_calendar" value="" />';

        echo '<div class="import-export-wrap">';
        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            // Get meta
            $room_ical_data = get_post_meta($room->ID, 'room_ical_data', true);

            echo '<div class="room_ical_export_wrapper" data-room-id="' . $room->ID . '">';
            echo '<div class="import-export-heading">' . $room->post_title . '</div>';

            echo '<button data-room-id="' . $room->ID . '" type="button" class="download_export_ical btn btn-primary">';
            echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
            echo '<span role="status"> ' . __('Download', 'staylodgic') . '</span>';
            echo '</button>';

            echo '</div>';
        }

        echo "</div>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }

    public function generate_ical_from_reservations($reservations)
    {
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

    private function format_date_for_ical($date)
    {
        if (!$date) return false;
        $timestamp = strtotime($date);
        return $timestamp ? date('Ymd\THis', $timestamp) : false;
    }

    public function download_reservations_ical($room_id)
    {
        $reservation_instance = new \Staylodgic\Reservations($dateString = '', $room_id);
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

    public function generate_csv_from_reservations($start_date, $end_date, $room_id)
    {

        $csv_data = "Booking Number,Room Name,Adults,Children,Checkin Date,Checkout Date,Reservation Status\r\n";

        // Initialize Reservations instance with start date and end date
        $reservation_instance = new \Staylodgic\Reservations($start_date, $room_id);
        $reservations_query = $reservation_instance->getReservationsForRoom($start_date, $end_date, false, false, $room_id);

        // Extract post objects from WP_Query
        $reservations = $reservations_query->posts;


        foreach ($reservations as $reservation) {
            $checkin_date = get_post_meta($reservation->ID, 'staylodgic_checkin_date', true) ?: '-';
            $checkout_date = get_post_meta($reservation->ID, 'staylodgic_checkout_date', true) ?: '-';
            $booking_number = get_post_meta($reservation->ID, 'staylodgic_booking_number', true) ?: '-';
            $reservation_status = get_post_meta($reservation->ID, 'staylodgic_reservation_status', true) ?: '-';
            $room_name = $reservation_instance->getRoomNameForReservation($reservation->ID);
            $adults_number = $reservation_instance->getNumberOfAdultsForReservation($reservation->ID);
            $children_number = $reservation_instance->getNumberOfChildrenForReservation($reservation->ID);

            $csv_data .= "$booking_number,$room_name,$adults_number,$children_number,$checkin_date,$checkout_date,$reservation_status\r\n";
        }

        return $csv_data;
    }

    public function generate_guest_registration_csv_from_reservations($start_date, $end_date)
    {

        $csv_data_header = "Booking Number,Full Name,ID,Country,Booking Channel,Room Name,Checkin Date,Checkin Time,Checkout Date,Checkout Time\r\n";
        $csv_data = '';
        // error_log('registrations');
        // error_log($start_date);
        // error_log($end_date);

        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {

            // Initialize Reservations instance with start date and end date
            $reservation_instance = new \Staylodgic\Reservations($start_date);
            $reservations_query = $reservation_instance->getReservationsForRoom($start_date, $end_date, $reservation_status = 'confirmed', false, $room->ID);

            // Extract post objects from WP_Query
            $reservations = $reservations_query->posts;

            foreach ($reservations as $reservation) {

                $checkin_date       = get_post_meta($reservation->ID, 'staylodgic_checkin_date', true) ?: '-';
                $checkout_date      = get_post_meta($reservation->ID, 'staylodgic_checkout_date', true) ?: '-';
                $booking_number     = get_post_meta($reservation->ID, 'staylodgic_booking_number', true) ?: '-';
                $booking_channel     = get_post_meta($reservation->ID, 'staylodgic_booking_channel', true) ?: '-';
                $room_name          = $reservation_instance->getRoomNameForReservation($reservation->ID);

                $registry_instance = new \Staylodgic\GuestRegistry();
                $resRegIDs         = $registry_instance->fetchResRegIDsByBookingNumber($booking_number);

                if (isset($resRegIDs) && is_array($resRegIDs)) {

                    $registerID = $resRegIDs['guestRegisterID'];
                    $registration_data = get_post_meta($registerID, 'staylodgic_registration_data', true);

                    // error_log('staylodgic_registration_data');
                    // error_log(print_r($registration_data, true));

                    if (is_array($registration_data) && !empty($registration_data)) {
                        foreach ($registration_data as $guest_id => $guest_data) {

                            $fullname          = $guest_data['fullname']['value'];
                            $passport          = $guest_data['passport']['value'];
                            $checkin_date_time = $guest_data['checkin-date']['value'];
                            $datetime_parts    = explode(' ', $checkin_date_time);
                            $checkin_date      = $datetime_parts[0];
                            $checkin_time      = $datetime_parts[1];

                            $checkout_date_time = $guest_data['checkout-date']['value'];
                            $datetime_parts     = explode(' ', $checkout_date_time);
                            $checkout_date      = $datetime_parts[0];
                            $checkout_time      = $datetime_parts[1];

                            $country_code = $guest_data['countries']['value'];

                            $country = staylodgic_country_list('display', $country_code);

                            $csv_data .= "$booking_number,$fullname,$passport,$country,$booking_channel,$room_name,$checkin_date,$checkin_time,$checkout_date,$checkout_time\r\n";
                        }
                    }
                }
            }
        }

        if ('' !== $csv_data) {
            return $csv_data_header . $csv_data;
        } else {
            return false;
        }
    }

    public function download_reservations_csv($room_id, $month)
    {
        // Calculate start date and end date of the selected month
        $start_date = date('Y-m-01', strtotime($month));  // First day of the selected month
        $end_date   = date('Y-m-t', strtotime($month));   // Last day of the selected month

        $csv_content = $this->generate_csv_from_reservations($start_date, $end_date, $room_id);

        $currentDateTime = date('Y-m-d_H-i-s');
        $filename        = "reservations-{$room_id}-{$start_date}-{$end_date}-on-{$currentDateTime}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $csv_content;
        exit;
    }
    public function download_guest_registrations_csv($month)
    {

        $start_date = date('Y-m-01', strtotime($month));  // First day of the selected month
        $end_date   = date('Y-m-t', strtotime($month));   // Last day of the selected month

        $csv_content = $this->generate_guest_registration_csv_from_reservations($start_date, $end_date);

        if ($csv_content) {
            $currentDateTime = date('Y-m-d_H-i-s');                                                  // Formats the date and time as YYYY-MM-DD_HH-MM-SS
            $filename        = "registrations-{$start_date}-{$end_date}-on-{$currentDateTime}.csv";

            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            echo $csv_content;
        }

        exit;
    }
}

// Instantiate the class
$IcalExportProcessor = new \Staylodgic\IcalExportProcessor();
