<?php
namespace Staylodgic;

class BatchProcessorBase
{
    private $batchSize = 50;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu')); // This now points to the add_admin_menu function
    }

    public function add_admin_menu()
    {
        // Add the main menu page.
        add_menu_page(
            'Staylodgic Admin',             // Page title
            'Staylodgic',                   // Menu title
            'manage_options',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page'), // Callback function
            '',                             // Icon URL
            34                              // Position
        );

        // Add the first submenu page. Often this duplicates the main menu page.
        add_submenu_page(
            'staylodgic-settings',          // Parent slug
            'Main',                    // Page title
            'Main',                    // Menu title
            'manage_options',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page') // Callback function
        );
    }

    public function display_main_page()
    {
        // The HTML content of the 'Staylodgic' page goes here
        echo "<h1>Welcome to Staylodgic</h1>";

    }

    /**
     * Check if the URL is ready for syncing based on a 15-minute interval.
     * Returns the number of minutes left until the URL is ready for syncing, if not ready.
     *
     * @param string $url The URL to check.
     * @return mixed True if the URL can be synced, or the number of minutes left if not ready.
     */
    private function is_url_ready_for_sync($url) {
        $transient_key = 'sync_last_time_' . md5($url);
        $last_sync_time = get_transient($transient_key);

        if ($last_sync_time !== false) {
            $elapsed_time = time() - $last_sync_time;
            $remaining_time = 15 * MINUTE_IN_SECONDS - $elapsed_time;
            if ($remaining_time > 0) {
                // Return the number of minutes left until the URL is ready for syncing.
                return ceil($remaining_time / MINUTE_IN_SECONDS);
            }
        }

        // Update the sync time and allow syncing.
        set_transient($transient_key, time(), 15 * MINUTE_IN_SECONDS);
        return true;
    }

    public function process_event_batch(
        $room_id = false,
        $ics_url = false,
    )
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

        // Create a new instance of the parser.
        $parser = new \ICal\ICal();

        $jsonOutput = false;
        if ( ! $room_id ) {
            $room_id = $_POST[ 'room_id' ];
            $ics_url = $_POST[ 'ics_url' ];
            $jsonOutput = true;
        }

        // Check if the URL is ready for syncing.
        $sync_check = self::is_url_ready_for_sync($ics_url);
        if (is_numeric($sync_check)) {
            wp_send_json_error('Error: Syncing is not allowed yet for this URL. Please wait for ' . $sync_check . ' more minutes.');
            return;
        }

        $file_contents = file_get_contents($ics_url);
        // Check if the feed is empty or incomplete
        if ($file_contents === false || empty($file_contents)) {
            if ( $jsonOutput ) {
                wp_send_json_error('Error: The iCal feed is empty or could not be retrieved.');
            }
            return;
        }
        if (strpos($file_contents, 'BEGIN:VCALENDAR') === false || strpos($file_contents, 'END:VCALENDAR') === false) {
            if ( $jsonOutput ) {
                wp_send_json_error('Error: The iCal feed is incomplete.');
            }
            return;
        }

        // Delete the transient if it exists.
        delete_transient('staylodgic_unprocessed_reservation_import');
        $transient_used = false;
        if (false !== ($events = get_transient('staylodgic_unprocessed_reservation_import'))) {
            // The events are stored in the transient
            $transient_used = true;
        }

        // Parse the ICS file and store the events in a transient.
        $file_path    = $ics_url;
        $file_md5Hash = md5($file_path);
        $parser->initString($file_contents); // Change this line
        $events = $parser->events();
        set_transient('staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS); // store for 12 hours

        // Check if the events are already stored in a transient.
        $events = get_transient('staylodgic_unprocessed_reservation_import');

        // Check if the events transient is empty.
        if (!$events) {
            // If empty, display an error or take appropriate action.
            if ( $jsonOutput ) {
                wp_send_json_error('No events found.');
            }
        }

        if (!$events) {
            // If not, parse the ICS file and store the events in a transient.
            $parser->initFile($file_path);
            $events = $parser->events();
            set_transient('staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS); // store for 12 hours
        }

        // Define the batch size.
        $batchSize = 10; // reduce batch size for testing purposes

        // Process a batch of events.
        $processedEvents = [  ];
        for ($i = 0; $i < $this->batchSize; $i++) {
            // Check if there are any more events to process.
            if (empty($events)) {
                break;
            }

            // Get the next event.
            $event = array_shift($events);

            $description = $event->description;

            $eventData = [  ]; // Initialize the $eventData array

            $descriptionLines = explode("\n", $description ?? '');
            foreach ($descriptionLines as $line) {
                $parts = explode(':', $line, 2);
                $key   = isset($parts[ 0 ]) ? trim($parts[ 0 ]) : '';
                $value = isset($parts[ 1 ]) ? trim($parts[ 1 ]) : '';
                if (array_key_exists($key, $eventData)) {
                    $eventData[ $key ] = $value;
                }

                // Extract check-in and check-out dates from the description
                if ($key === 'CHECKIN') {
                    // Extract date portion and remove time
                    $checkinDate            = date('Y-m-d', strtotime($value));
                    $eventData[ 'CHECKIN' ] = $checkinDate;
                } elseif ($key === 'CHECKOUT') {
                    // Extract date portion and remove time
                    $checkoutDate            = date('Y-m-d', strtotime($value));
                    $eventData[ 'CHECKOUT' ] = $checkoutDate;
                }
            }

            $checkin_date  = date('Y-m-d', strtotime($event->dtstart));
            $checkout_date = date('Y-m-d', strtotime($event->dtend));

            $processedEvent = [
                'SIGNATURE'   => $file_md5Hash,
                'CREATED'     => $event->created,
                'DTEND'       => $event->dtend,
                'DTSTART'     => $event->dtstart,
                'SUMMARY'     => $event->summary,
                'CHECKIN'     => $checkin_date,
                'CHECKOUT'    => $checkout_date,
                'UID'         => $event->uid,
                'DATA'        => $eventData,
                'DESCRIPTION' => $description,
             ];

            $processedEvents[  ] = $processedEvent;
            // Update the transient with the remaining events.
            set_transient('staylodgic_unprocessed_reservation_import', $events, 12 * HOUR_IN_SECONDS);
        }

        // Return the processed events and the number of remaining events.
        $response = array(
            'success' => true,
            'data'    => array(
                'processed'               => $processedEvents,
                'remaining'               => count($events),
                'transient_used'          => $transient_used,
                'processedBookingNumbers' => array_column($processedEvents, 'UID'),
            ),
        );

        if ( $jsonOutput ) {
            wp_send_json($response);
        } else {
            return $response;
        }
        
    }
}

// Instantiate the class
$BatchProcessorBase = new \Staylodgic\BatchProcessorBase();
