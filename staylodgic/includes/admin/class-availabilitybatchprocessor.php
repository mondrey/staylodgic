<?php
namespace Staylodgic;

use Error;

class AvailabilityBatchProcessor extends BatchProcessorBase
{
    private $batchSize = 50;
    private $error_urls = array();

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_availability_admin_menu')); // This now points to the add_admin_menu function

        add_action('wp_ajax_save_ical_availability_meta', array($this, 'save_ical_availability_meta'));
        add_action('wp_ajax_nopriv_save_ical_availability_meta', array($this, 'save_ical_availability_meta'));

        // Add the new admin page
        add_action('admin_menu', array($this, 'add_export_availability_admin_menu'));

        // Add the export handler hook
        add_action('init', array($this, 'add_ics_rewrite_rule'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'handle_ics_export'));
        
        // Check and schedule the cron event
        $this->schedule_cron_event();

        // Hook the function to the cron event
        add_action('staylodgic_ical_availability_processor_event', array($this, 'ical_availability_processor'));

        // Add custom interval
        add_filter('cron_schedules', array($this, 'add_cron_interval'));

        // Initialize batch count
        if (!get_option('ical_processing_batch_count')) {
            update_option('ical_processing_batch_count', 0);
        }

        // Add the cron hook for batch processing
        $this->add_cron_hook();
    }

    public function add_availability_admin_menu()
    {
        add_submenu_page(
            'staylodgic-settings',
            // This is the slug of the parent menu
            'Import iCal Availabilitiy',
            'Import iCal Availabilitiy',
            'manage_options',
            'slgc-import-availability-ical',
            array($this, 'ical_availability_import')
        );
    }

    public function add_ics_rewrite_rule() {
        add_rewrite_rule('^ics-export/room/([0-9]+)/?', 'index.php?staylodgic_ics_room=$matches[1]', 'top');
    }
    public function register_query_vars($vars) {
        $vars[] = 'staylodgic_ics_room';
        return $vars;
    }
    public function handle_ics_export() {
        $roomId = get_query_var('staylodgic_ics_room');
        if ($roomId) {
            $this->handle_export_request($roomId);
            exit;
        }
    }    

    public function schedule_cron_event() {

        $qtysync_interval = null; // or set a default value
        $settings = get_option('staylodgic_settings');

        if (is_array($settings) && isset($settings['qtysync_interval'])) {
            $qtysync_interval = $settings['qtysync_interval'];

            // Define the cron schedule based on the validated interval
            switch ($qtysync_interval) {
                case '1':
                    $schedule = 'staylodgic_1_minute';
                    break;
                case '5':
                    $schedule = 'staylodgic_5_minutes';
                    break;
                case '10':
                    $schedule = 'staylodgic_10_minutes';
                    break;
                case '15':
                    $schedule = 'staylodgic_15_minutes';
                    break;
                case '30':
                    $schedule = 'staylodgic_30_minutes';
                    break;
                case '60':
                    $schedule = 'staylodgic_60_minutes';
                    break;
                default:
                    $schedule = 'staylodgic_5_minutes'; // Default case
                    break;
            }
        
            // Schedule the cron event if it's not already scheduled
            if (!wp_next_scheduled('staylodgic_ical_availability_processor_event')) {
                wp_schedule_event(time(), $schedule, 'staylodgic_ical_availability_processor_event');
            }

        }
    }    

    // Custom intervals for cron job
    public function add_cron_interval($schedules) {
        $sync_intervals = staylodgic_sync_intervals(); // Get intervals from your function

        foreach ($sync_intervals as $interval => $display) {
            $schedules["staylodgic_{$interval}_minutes"] = array(
                'interval' => intval($interval) * 60,
                'display' => $display
            );
        }

        // error_log( print_r( $schedules, true ) );
        return $schedules;
    }

    public function save_ical_availability_meta()
    {
        // Perform nonce check and other validations as needed
        // ...
        if (!isset($_POST[ 'ical_form_nonce' ]) || !wp_verify_nonce($_POST[ 'ical_form_nonce' ], 'ical_form_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        $room_ids = null;
        $room_links_id = null;
        $room_links_url = null;
        $room_links_comment = null;

        if (isset($_POST['room_ical_links_id'])) {
            $room_links_id = $_POST['room_ical_links_id'];
        }
        if (isset($_POST['room_ical_links_url'])) {
            $room_links_url = $_POST['room_ical_links_url'];
        }
        if (isset($_POST['room_ical_links_comment'])) {
            $room_links_comment = $_POST['room_ical_links_comment'];
        }

        if (isset($_POST['room_ids'])) {
            $room_ids = $_POST['room_ids'];
            

            //error_log( print_r( $_POST , true ) );
            for ($i = 0; $i < count($room_ids); $i++) {
                $room_id    = $room_ids[ $i ];
                $room_links = array();
                
                $old_room_data = get_post_meta($room_id, 'staylodgic_channel_quantity_array', true);
                error_log( '----- Before Stored iCal Data' );
                error_log( print_r( $old_room_data , true ) );
                // Ensure that $room_links_url[$i] is an array before trying to count its elements
                if (isset($room_links_url[ $i ]) && is_array($room_links_url[ $i ])) {
                    for ($j = 0; $j < count($room_links_url[ $i ]); $j++) {

                        // Get the old room data
                        $old_room_links = get_post_meta($room_id, 'staylodgic_availability_ical_data', true);

                        // Check if the URL is valid
                        if (filter_var($room_links_url[ $i ][ $j ], FILTER_VALIDATE_URL)) {
                            // Check if a unique ID is already assigned
                            
                            if (!isset($room_links_id[$i][$j]) || '' == $room_links_id[$i][$j]) {
                                $room_links_id[ $i ][ $j ] = uniqid();
                            }

                            $file_md5Hash = md5(esc_url($room_links_url[ $i ][ $j ]));

                            // Check if the URL is the same as before
                            $ical_synced = false;
                            if (isset($old_room_links[ $file_md5Hash ]) && $old_room_links[ $file_md5Hash ][ 'ical_url' ] == $room_links_url[ $i ][ $j ]) {
                                $ical_synced = $old_room_links[ $file_md5Hash ][ 'ical_synced' ];
                            }

                            $room_links[ $file_md5Hash ] = array(
                                'ical_id'      => sanitize_text_field($room_links_id[ $i ][ $j ]),
                                'ical_synced'  => $ical_synced,
                                'ical_url'     => esc_url($room_links_url[ $i ][ $j ]),
                                'ical_comment' => sanitize_text_field($room_links_comment[ $i ][ $j ]),
                            );
                        }
                    }
                }

                // Update the meta field in the database.
                update_post_meta($room_id, 'staylodgic_availability_ical_data', $room_links);
                delete_post_meta($room_id, 'staylodgic_channel_quantity_array', null);
            }
            
            // Sync on Save processing full batch ( process intensive )
            self::ical_availability_processor(true);
            
            if (!wp_next_scheduled('continue_ical_availability_processing')) {
                wp_schedule_single_event(time(), 'continue_ical_availability_processing');
            }
        
            wp_send_json_success('Successfully stored and batch processing initiated');
        } else {
            wp_send_json_success('Room data not found');
        }
    }

    public function areCalendarsConfigured() {
        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            $room_ical_data = get_post_meta($room->ID, 'staylodgic_availability_ical_data', true);
            if (!empty($room_ical_data)) {
                // Calendar URLs exist
                return true;
            }
        }
        // No calendar URLs set
        return false;
    }

    /**
     * Checks if the syncing process is currently in progress.
     * 
     * @return bool Returns true if syncing is in progress, false otherwise.
     */
    public function isSyncingInProgress() {
        // First, check if calendars are configured
        if (!$this->areCalendarsConfigured()) {
            // If no calendars are configured, syncing is not in progress
            return false;
        }
        // Then check the is_syncing flag
        return get_option('is_syncing', false);
    }    

    public function ical_availability_processor($process_all = false) {
        $rooms = Rooms::queryRooms();
        $processed_rooms = get_option('staylodgic_processed_rooms', []);

        // Increment batch count only if not processing all
        if (!$process_all) {
            // Set the is_syncing flag
            update_option('is_syncing', true);
            $batch_count = get_option('ical_processing_batch_count', 0) + 1;
            update_option('ical_processing_batch_count', $batch_count);
        } else {
            $batch_count = 'manual';
        }

        $count = 0;
        $changedDateRanges = array();

        foreach ($rooms as $room) {
            if (in_array($room->ID, $processed_rooms)) {
                continue; // Skip already processed rooms
            }

            $room_ical_data = get_post_meta($room->ID, 'staylodgic_availability_ical_data', true);

            if (is_array($room_ical_data) && count($room_ical_data) > 0) {
                foreach ($room_ical_data as $ical_link) {
                    $ics_url = $ical_link['ical_url'];
                    $ics_id = $ical_link[ 'ical_id' ];

                    $startProcessTime = microtime(true); // Start time measurement

                    $file_ok = true;
                    $file_contents = file_get_contents($ics_url);
                    // Check if the feed is empty or incomplete
                    if ($file_contents === false || empty($file_contents)) {
                        $file_ok = false;
                    }
                    if (strpos($file_contents, 'BEGIN:VCALENDAR') === false || strpos($file_contents, 'END:VCALENDAR') === false) {
                        $file_ok = false;
                    }
                    
                    $blocked_dates['ical'][$room->ID]['quantity'] = self::process_availability_link([], $room->ID, $ics_url, $file_contents);
                    $endProcessTime = microtime(true); // End time measurement
                    $elapsedProcessTime = $endProcessTime - $startProcessTime; // Calculate elapsed time
                    // Process and save/update $blocked_dates as required
                    $blocked_dates['ical'][$room->ID]['stats'][$ics_id]['syncdate'] = date('Y-m-d');
                    $blocked_dates['ical'][$room->ID]['stats'][$ics_id]['synctime'] = date('H:i:s');
                    $blocked_dates['ical'][$room->ID]['stats'][$ics_id]['file_ok'] = $file_ok; // Store elapsed time
                    $blocked_dates['ical'][$room->ID]['stats'][$ics_id]['syncprocessing_time'] = $elapsedProcessTime; // Store elapsed time
                    $blocked_dates['ical'][$room->ID]['stats'][$ics_id]['batch_count'] = $batch_count;

                }
            }

            $processed_rooms[] = $room->ID;
            update_option('staylodgic_processed_rooms', $processed_rooms); // Update the list of processed rooms

            $count++;

            if (!$process_all && $count < $this->batchSize) {
                // Reset batch count when all rooms are processed
                update_option('ical_processing_batch_count', 0);
                // Clear the is_syncing flag when all rooms are processed or on manual full process
                update_option('is_syncing', false);
            }

            if (!$process_all && $count >= $this->batchSize) {
                break; // Stop processing after reaching batch size
            }

            if (isset($blocked_dates['ical'][$room->ID])) {

                update_post_meta($room->ID, 'staylodgic_channel_quantity_array', $blocked_dates['ical'][$room->ID]);
                
            }
        }

        if ( isset($blocked_dates) && is_array( $blocked_dates ) ) {

            error_log( '----- Blocked dates being processed ' . $count );
            error_log( print_r($blocked_dates, 1) );
            error_log( '-----------------------------------' );
        }

        // Clear the is_syncing flag under two conditions:
        // 1. All rooms are processed in batch mode.
        // 2. When processing all rooms in one go (manual processing).
        if (($count < $this->batchSize && !$process_all) || $process_all) {
            update_option('is_syncing', false);
        }

        if ($count < $this->batchSize) {
            // All rooms processed, reset the list
            update_option('staylodgic_processed_rooms', []);
            // Optionally trigger a notification that processing is complete
        }

        // Schedule next batch
        if (!wp_next_scheduled('continue_ical_availability_processing')) {
            wp_schedule_single_event(time() + 300, 'continue_ical_availability_processing'); // 300 seconds = 5 minutes
        }
    } 
    
    public function add_cron_hook() {
        add_action('continue_ical_availability_processing', array($this, 'ical_availability_processor'));
    }
    
    public function process_availability_link(
        $blocked_dates = array(),
        $room_id = false,
        $ics_url = false,
        $file_contents = false
    )
    {
        // Create a new instance of the parser.
        $parser = new \ICal\ICal();

        error_log( '----- AVAILABILITY FILE VALID ' );
        // Parse the ICS file
        $parser->initString($file_contents);
        $events = $parser->events();
    
        foreach ($events as $event) {
            // Extract start and end dates
            $start_date = new \DateTime($event->dtstart);
            $end_date = new \DateTime($event->dtend);
    
            // Iterate over the date range
            while ($start_date < $end_date) {
                // Add date to blocked dates, excluding the end date (checkout date)
                $blocked_dates[$start_date->format('Y-m-d')] = '0';
                $start_date->modify('+1 day');
            }
        }
    
        // Return the array of blocked dates
        return $blocked_dates;
    }

    public function ical_availability_import()
    {

        // The HTML content of your 'Import iCal' page goes here
        echo '<div class="expor-import-calendar">';
        echo '<div class="main-sync-form-wrap">';
        echo '<div id="sync-form">';
        echo '<h1>Sync Your Calendar</h1>';
        echo '<p>Keep your bookings up-to-date. Connect your iCalendar feeds to synchronize your booking availability with your StayLogic calendar. Simply enter the URLs for the calendars you wish to sync below.</p>';
        echo '<div class="how-to-admin">';
        echo '<h2>How to Sync:</h2>';
        echo '<ol>';
        echo '<li>Find your iCalendar URL from the external booking platform or calendar service.</li>';
        echo '<li>Enter the iCalendar URL in the input field below.</li>';
        echo '<li>Set a label for your reference.</li>';
        echo '</ol>';
        echo '</div>';

        echo "<form id='room_ical_form' method='post'>";
        echo '<input type="hidden" name="ical_form_nonce" value="' . wp_create_nonce('ical_form_nonce') . '">';

        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            // Get meta
            $room_ical_data = get_post_meta($room->ID, 'staylodgic_availability_ical_data', true);
            $room_channel_availability = get_post_meta($room->ID, 'staylodgic_channel_quantity_array', true);

            echo '<div class="room_ical_links_wrapper" data-room-id="' . $room->ID . '">';
            echo '<div class="import-export-heading">' . $room->post_title . '</div>';
            if (is_array($room_ical_data) && count($room_ical_data) > 0) {
                foreach ($room_ical_data as $ical_id => $ical_link) {

                    if (isset($room_ical_data[ $ical_id ])) {
                        $ical_synced = $room_ical_data[ $ical_id ][ 'ical_synced' ];
                    }

                    echo '<div class="room_ical_link_group input-group">';
                    echo '<input readonly hidden type="text" name="room_ical_links_id[]" value="' . esc_attr($ical_link[ 'ical_id' ]) . '">';
                    echo '<span class="input-group-text">url</span>';
                    echo '<input readonly class="form-control" type="url" name="room_ical_links_url[]" value="' . esc_attr($ical_link[ 'ical_url' ]) . '">';
                    echo '<span class="input-group-text">Label</span>';
                    echo '<input readonly class="form-control" type="text" name="room_ical_links_comment[]" value="' . esc_attr($ical_link[ 'ical_comment' ]) . '">';
                    echo '<button type="button" class="unlock_button btn btn-warning"><i class="fas fa-lock"></i></button>'; // Unlock button
                    echo '</div>';
                    if (is_array($room_channel_availability) && isset($room_channel_availability['stats'])) {
                        foreach ($room_channel_availability['stats'] as $key => $value) {
                            // Check if the key matches the pattern or criteria you're looking for
                            if ($key === $ical_link[ 'ical_id' ] ) {

                                $syncDate = $room_channel_availability['stats'][$key]['syncdate'];
                                $syncTime = $room_channel_availability['stats'][$key]['synctime'];
                                $timezone = staylodgic_get_option('timezone');
                                
                                $adjustedValues = staylodgic_applyTimezoneToDateAndTime($syncDate, $syncTime, $timezone);

                                echo '<div class="availability-sync-stats">';
                                echo '<span>Last sync: '.$adjustedValues['adjustedDate'].'</span>';
                                echo '<span>Time: '.$adjustedValues['adjustedTime'].'</span>';
                                echo '<span>Processed in ( seconds ): '.$room_channel_availability['stats'][$key]['syncprocessing_time'].'</span>';
                                if ( ! $room_channel_availability['stats'][$key]['file_ok'] ) {
                                    echo '<span class="file-error">File error</span>';
                                }
                                echo '</div>';
                                break;
                            }
                        }
                    }
                }
            } else {
                echo '<div class="room_ical_link_group input-group">';
                echo '<span class="input-group-text">url</span>';
                echo '<input aria-label="url" class="form-control" type="url" name="room_ical_links_url[]">';
                echo '<span class="input-group-text">Label</span>';
                echo '<input aria-label="Label" class="form-control" type="text" name="room_ical_links_comment[]">';
                echo '</div>';
            }
            echo '<button type="button" class="add_more_ical button button-secondary button-small">Add more</button>';
            echo '</div>';
        }
        echo '<button data-type="sync-availability" class="btn btn-primary" type="button" id="save_all_ical_rooms">';
        echo '<span class="spinner-zone spinner-border-sm" aria-hidden="true"></span>';
        echo '<span role="status"> Save All</span>';
        echo '</button>';
        //echo '<input data-type="sync-availability" class="button button-primary button-large" type="submit" id="save_all_ical_rooms" value="Save All">';
        echo "</form>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function add_export_availability_admin_menu() {
        add_submenu_page(
            'staylodgic-settings', // Replace with the slug of the parent menu item
            'Export iCal Availability',
            'Export iCal Availability',
            'manage_options',
            'slgc-export-availability-ical',
            array($this, 'export_availability_ical_page')
        );
    }

    public function export_availability_ical_page() {

        // The HTML content of your 'Import iCal' page goes here
        echo '<div class="expor-import-calendar main-sync-form-wrap">';
        echo '<div id="export-import-form">';
        echo '<h1>iCal Feeds</h1>';
        echo '<p>Synchronize availability for your rooms with other softwares using iCal feeds.</p>';
        echo '<div class="how-to-admin">';
        echo '<h2>How to:</h2>';
        echo '<ol>';
        echo '<li>Copy the url for the room.</li>';
        echo '<li>Enter it to the software which is compatible with iCal feeds for availability.</li>';
        echo '</ol>';
        echo '</div>';

        $rooms = Rooms::queryRooms();
        foreach ($rooms as $room) {
            // Get meta
            $room_ical_data = get_post_meta($room->ID, 'staylodgic_availability_ical_data', true);
          
            echo '<div class="room_ical_links_wrapper" data-room-id="' . $room->ID . '">';
            echo '<div class="import-export-heading">' . $room->post_title . '</div>';

            echo '<div class="room_ical_link_group">';
            // The URL to trigger the export functionality
            $exportUrl = home_url('/ics-export/room/') . $room->ID;

            $exportUrl .='&room='.$room->ID;

            // Page content
            echo "<div class='export-ical-wrap input-group'>";
            echo "<input class='form-control urlField' type='text' value='{$exportUrl}' readonly>";
            echo '<div class="btn btn-outline-secondary copy-url-button" data-bs-delay="0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Copy to clipboard"><i class="fa-solid fa-copy"></i></div>';
            echo "</div>";

            echo '</div>';
            echo '</div>';
        }

        echo "</div>";
        echo "</div>";
    }

    public function handle_export_request($roomId) {
    
        // Retrieve the 'staylodgic_quantity_array' and 'staylodgic_channel_quantity_array'
        $room_reservations_instance = new \Staylodgic\Reservations( $dateString = false, $roomId );
        $room_reservations_instance->calculateAndUpdateRemainingRoomCountsForAllDates();
        $remainingQuantityArray = $room_reservations_instance->getRemainingRoomCountArray();
        
        $channelArray = get_post_meta($roomId, 'staylodgic_channel_quantity_array', true);
        $channelQuantityArray = isset($channelArray['quantity']) ? $channelArray['quantity'] : [];

        $new_remainingQuantityArray = $this->filterFutureDates($remainingQuantityArray);
        // Merge the arrays - channelQuantityArray values will overwrite remainingQuantityArray values for any matching keys
        $mergedArray = array_merge($new_remainingQuantityArray, $channelQuantityArray);

        // Determine if the request is coming from a browser or a server
        $mode = $this->detect_request_mode();

        // Generate the .ics file with the merged array
        $this->generate_ics_file($roomId, $mergedArray, $mode);

        exit;
    }

    public function filterFutureDates($remainingQuantityArray) {
        $filteredArray = [];
        $currentDate = date('Y-m-d');
    
        foreach ($remainingQuantityArray as $date => $quantity) {
            if ($date >= $currentDate) {
                $filteredArray[$date] = $quantity;
            }
        }
    
        return $filteredArray;
    }
    
    private function detect_request_mode() {
        // Check for certain server variables typical in browser requests
        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            // Likely a browser request
            return 'download';
        }
        // Default to server mode for API calls, scripts, etc.
        return 'server';
    }
    
    private function generate_ics_file($roomId, $quantityArray, $mode) {
        // Start of the ICS file
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//Your Company//Your Calendar//EN\r\n";
    
        // Iterate over the quantity array
        foreach ($quantityArray as $date => $quantity) {
            if ($quantity == 0) {
                // Format the date for ICS
                $icsDate = new \DateTime($date);
                $icsDateStr = $icsDate->format('Ymd');
        
                // Create a copy of the date and add one day for DTEND
                $icsEndDate = clone $icsDate;
                $icsEndDate->modify('+1 day');
                $icsEndDateStr = $icsEndDate->format('Ymd');
        
                $icsReadableDate = $icsDate->format('Y-m-d');
                
                // Create an event for the unavailable date
                $icsContent .= "BEGIN:VEVENT\r\n";
                $icsContent .= "UID:" . uniqid() . "@staylodgic\r\n";
                $icsContent .= "DTSTAMP:" . gmdate('Ymd') . 'T' . gmdate('His') . "Z\r\n";
                $icsContent .= "DTSTART;VALUE=DATE:" . $icsDateStr . "\r\n";
                $icsContent .= "DTEND;VALUE=DATE:" . $icsEndDateStr . "\r\n";
                $icsContent .= "SUMMARY:Unavailable\r\n";
                $icsContent .= "DESCRIPTION:Unavailable on " . $icsReadableDate . "\r\n";
                $icsContent .= "STATUS:CONFIRMED\r\n";
                $icsContent .= "END:VEVENT\r\n";
            }
        }        
    
        // End of the ICS file
        $icsContent .= "END:VCALENDAR";
    
        if ($mode === 'server') {
            // Output the .ics content directly for server-to-server requests
            echo $icsContent;
        } else {
            // Set headers for .ics file download for user requests
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="room-' . $roomId . '-availability.ics"');
            echo $icsContent;
        }
    }
    


}

// Instantiate the class
$AvailabilityBatchProcessor = new \Staylodgic\AvailabilityBatchProcessor();
