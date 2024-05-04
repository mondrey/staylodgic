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
            __('Staylodgic Admin', 'staylodgic'),             // Page title
            __('Staylodgic', 'staylodgic'),                   // Menu title
            'edit_posts',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page'), // Callback function
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTQ5NS45IDE2Ni42YzMuMiA4LjcgLjUgMTguNC02LjQgMjQuNmwtNDMuMyAzOS40YzEuMSA4LjMgMS43IDE2LjggMS43IDI1LjRzLS42IDE3LjEtMS43IDI1LjRsNDMuMyAzOS40YzYuOSA2LjIgOS42IDE1LjkgNi40IDI0LjZjLTQuNCAxMS45LTkuNyAyMy4zLTE1LjggMzQuM2wtNC43IDguMWMtNi42IDExLTE0IDIxLjQtMjIuMSAzMS4yYy01LjkgNy4yLTE1LjcgOS42LTI0LjUgNi44bC01NS43LTE3LjdjLTEzLjQgMTAuMy0yOC4yIDE4LjktNDQgMjUuNGwtMTIuNSA1Ny4xYy0yIDkuMS05IDE2LjMtMTguMiAxNy44Yy0xMy44IDIuMy0yOCAzLjUtNDIuNSAzLjVzLTI4LjctMS4yLTQyLjUtMy41Yy05LjItMS41LTE2LjItOC43LTE4LjItMTcuOGwtMTIuNS01Ny4xYy0xNS44LTYuNS0zMC42LTE1LjEtNDQtMjUuNEw4My4xIDQyNS45Yy04LjggMi44LTE4LjYgLjMtMjQuNS02LjhjLTguMS05LjgtMTUuNS0yMC4yLTIyLjEtMzEuMmwtNC43LTguMWMtNi4xLTExLTExLjQtMjIuNC0xNS44LTM0LjNjLTMuMi04LjctLjUtMTguNCA2LjQtMjQuNmw0My4zLTM5LjRDNjQuNiAyNzMuMSA2NCAyNjQuNiA2NCAyNTZzLjYtMTcuMSAxLjctMjUuNEwyMi40IDE5MS4yYy02LjktNi4yLTkuNi0xNS45LTYuNC0yNC42YzQuNC0xMS45IDkuNy0yMy4zIDE1LjgtMzQuM2w0LjctOC4xYzYuNi0xMSAxNC0yMS40IDIyLjEtMzEuMmM1LjktNy4yIDE1LjctOS42IDI0LjUtNi44bDU1LjcgMTcuN2MxMy40LTEwLjMgMjguMi0xOC45IDQ0LTI1LjRsMTIuNS01Ny4xYzItOS4xIDktMTYuMyAxOC4yLTE3LjhDMjI3LjMgMS4yIDI0MS41IDAgMjU2IDBzMjguNyAxLjIgNDIuNSAzLjVjOS4yIDEuNSAxNi4yIDguNyAxOC4yIDE3LjhsMTIuNSA1Ny4xYzE1LjggNi41IDMwLjYgMTUuMSA0NCAyNS40bDU1LjctMTcuN2M4LjgtMi44IDE4LjYtLjMgMjQuNSA2LjhjOC4xIDkuOCAxNS41IDIwLjIgMjIuMSAzMS4ybDQuNyA4LjFjNi4xIDExIDExLjQgMjIuNCAxNS44IDM0LjN6TTI1NiAzMzZhODAgODAgMCAxIDAgMC0xNjAgODAgODAgMCAxIDAgMCAxNjB6Ii8+PC9zdmc+',                             // Icon URL
            31                             // Position
        );

        // Add the first submenu page. Often this duplicates the main menu page.
        add_submenu_page(
            'staylodgic-settings',          // Parent slug
            __('Main', 'staylodgic'),                    // Page title
            __('Main', 'staylodgic'),                    // Menu title
            'edit_posts',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page') // Callback function
        );
    }

    public function display_main_page()
    {
        // The HTML content of the 'Staylodgic' page goes here

        echo '<div class="admin-page-wrapper">';
        echo '<div class="content-container">';
        echo '<div class="left-columns">'; // Container for both content columns

        echo '<div class="left-column">';
        echo '<h1>Staylodgic</h1>';
        echo '<h2>Hotel Management Software</h2>';
        echo '<h4>Setup Hotel for Bookings</h4>';
        echo '<ul>';
        echo '<li>Step 1: Create Rooms</li>';
        echo '<li>Step 2: Dashboard > View Availability</li>';
        echo '<ul>';
        echo '<li>- Add Room Rates</li>';
        echo '<li>- Add Room Quantity</li>';
        echo '</ul>';
        echo '<li>Step 3: Create Reservations</li>';
        echo '</ul>';

        echo '<h4>Setup Activities</h4>';
        echo '<ul>';
        echo '<li>Step 1: Create Activities</li>';
        echo '<ul>';
        echo '<li>- Add Scheduled Time to Week</li>';
        echo '</ul>';
        echo '<li>Step 2: Create Activity Reservations</li>';
        echo '</ul>';

        echo '<h4>Using Dashboard</h4>';
        echo '<ul>';
        echo '<li>- View Bookings Overview</li>';
        echo '<li>- View Activities Overview</li>';
        echo '<li>- View Availability Calendar</li>';
        echo '<li>- View Annual Availability</li>';
        echo '</ul>';

        echo '</div>'; // End of first left column

        echo '<div class="left-column">';
        echo '<h4>Customer Registry</h4>';
        echo '<ul>';
        echo '<li>- Create new customers</li>';
        echo '<li>- Assign existing customers</li>';
        echo '</ul>';

        echo '<h4>Guest Registration</h4>';
        echo '<ul>';
        echo '<li>Step 1: Create guest registration</li>';
        echo '<li>Step 2: Customize registration fields</li>';
        echo '<li>Step 3: Online registration</li>';
        echo '<ul>';
        echo '<li>- Email links</li>';
        echo '<li>- Use QR Code to scan and fill</li>';
        echo '</ul>';
        echo '</ul>';

        echo '<h4>Invoicing</h4>';
        echo '<ul>';
        echo '<li>- Add property logo</li>';
        echo '<li>- Add activity logo</li>';
        echo '<li>- Footer and Header</li>';
        echo '<li>- Generate invoices for bookings</li>';
        echo '<li>- Generate invoices for activities</li>';
        echo '</ul>';

        echo '<h4>Import / Export</h4>';
        echo '<ul>';
        echo '<li>- Export CSV Bookings</li>';
        echo '<li>- Export Guests Registration for Month</li>';
        echo '<li>- Import and Sync iCal Availability<br/><span class="feature-update">( Future premium update )</span></li>';
        echo '<li>- Export iCal Availability Feeds<br/><span class="feature-update">( Future premium update )</span></li>';
        echo '</ul>';

        echo '</div>'; // End of second left column

        echo '<div class="left-column">';

        echo '<h4>Taxes</h4>';
        echo '<ul>';
        echo '<li>- Fixed tax</li>';
        echo '<li>- Percentage tax</li>';
        echo '<li>- Per day tax</li>';
        echo '<li>- Per person tax</li>';
        echo '</ul>';

        echo '<h4>Meal plans</h4>';
        echo '<ul>';
        echo '<li>- Create free plans</li>';
        echo '<li>- Create paid plans</li>';
        echo '</ul>';

        echo '<h4>Per Person Pricing</h4>';
        echo '<ul>';
        echo '<li>- Set fixed price increments</li>';
        echo '<li>- Increment by percentage per occupant</li>';
        echo '</ul>';

        echo '<h4>Discounts</h4>';
        echo '<ul>';
        echo '<li>- Last minute discount</li>';
        echo '<li>- Early booking discount</li>';
        echo '<li>- Long stay discount</li>';
        echo '</ul>';

        echo '</div>'; // End of second left column

        echo '</div>'; // End of left-columns container

        echo '<div class="right-column">';
        echo '<div class="svg-container">';
        echo '<!-- SVG or SVG CSS Background here -->';
        echo '</div>';
        echo '</div>'; // End of right column

        echo '</div>'; // End of content container
        echo '</div>'; // End of page wrapper

    }

    /**
     * Check if the URL is ready for syncing based on a 15-minute interval.
     * Returns the number of minutes left until the URL is ready for syncing, if not ready.
     *
     * @param string $url The URL to check.
     * @return mixed True if the URL can be synced, or the number of minutes left if not ready.
     */
    private function is_url_ready_for_sync($url)
    {
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
    ) {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

        // Create a new instance of the parser.
        $parser = new \ICal\ICal();

        $jsonOutput = false;
        if (!$room_id) {
            $room_id = $_POST['room_id'];
            $ics_url = $_POST['ics_url'];
            $jsonOutput = true;
        }

        // Check if the URL is ready for syncing.
        $sync_check = self::is_url_ready_for_sync($ics_url);
        if (is_numeric($sync_check)) {
            wp_send_json_error('Error: Syncing is not allowed yet for this URL. Please wait for ' . esc_html($sync_check) . ' more minutes.');
            return;
        }

        $file_contents = file_get_contents($ics_url);
        // Check if the feed is empty or incomplete
        if ($file_contents === false || empty($file_contents)) {
            if ($jsonOutput) {
                wp_send_json_error('Error: The iCal feed is empty or could not be retrieved.');
            }
            return;
        }
        if (strpos($file_contents, 'BEGIN:VCALENDAR') === false || strpos($file_contents, 'END:VCALENDAR') === false) {
            if ($jsonOutput) {
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
            if ($jsonOutput) {
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
        $processedEvents = [];
        for ($i = 0; $i < $this->batchSize; $i++) {
            // Check if there are any more events to process.
            if (empty($events)) {
                break;
            }

            // Get the next event.
            $event = array_shift($events);

            $description = $event->description;

            $eventData = []; // Initialize the $eventData array

            $descriptionLines = explode("\n", $description ?? '');
            foreach ($descriptionLines as $line) {
                $parts = explode(':', $line, 2);
                $key   = isset($parts[0]) ? trim($parts[0]) : '';
                $value = isset($parts[1]) ? trim($parts[1]) : '';
                if (array_key_exists($key, $eventData)) {
                    $eventData[$key] = $value;
                }

                // Extract check-in and check-out dates from the description
                if ($key === 'CHECKIN') {
                    // Extract date portion and remove time
                    $checkinDate            = date('Y-m-d', strtotime($value));
                    $eventData['CHECKIN'] = $checkinDate;
                } elseif ($key === 'CHECKOUT') {
                    // Extract date portion and remove time
                    $checkoutDate            = date('Y-m-d', strtotime($value));
                    $eventData['CHECKOUT'] = $checkoutDate;
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

            $processedEvents[] = $processedEvent;
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

        if ($jsonOutput) {
            wp_send_json($response);
        } else {
            return $response;
        }
    }
}

// Instantiate the class
$BatchProcessorBase = new \Staylodgic\BatchProcessorBase();
