<?php

namespace Staylodgic;

class AvailablityCalendarYear extends AvailablityCalendarBase
{

    public function __construct($startDate = null, $endDate = null)
    {
        parent::__construct($startDate, $endDate);

        add_action('admin_menu', array($this, 'AvailablityCalendarYearDisplay'));
    }

    // Add the Availability menu item to the admin menu
    public function AvailablityCalendarYearDisplay()
    {
        // Add the Availability submenu item under the parent menu
        add_submenu_page(
            'slgc-dashboard',
            __('Availability Annum', 'staylodgic'),
            __('Availability Annum', 'staylodgic'),
            'manage_options',
            'slgc-availability-yearly',
            array($this, 'room_Reservation_Plugin_Display_Availability_Calendar_Yearly')
        );
    }
    public function room_Reservation_Plugin_Display_Availability_Calendar_Yearly()
    {
        // Output the HTML for the Availability page
?>
        <div class="wrap">
            <h1><?php _e('12 Months Availability Overview', 'staylodgic'); ?></h1>
            <?php
            if (!\Staylodgic\Rooms::hasRooms()) {
                echo '<h1>' . __('No Rooms Found', 'staylodgic') . '</h1>';
                return;
            }


            echo '<div class="calendars-container">';
            // Display the calendar for the current month and the next six months
            $current_month = date('n');
            $current_year = date('Y');
            for ($i = 0; $i <= 12; $i++) {
                $month = ($current_month + $i - 1) % 12 + 1;
                $year = $current_year + floor(($current_month + $i - 1) / 12);
                $this->display_monthly_calendar($month, $year);
            }
            echo '</div>';
            ?>
        </div>
<?php
    }

    public function display_monthly_calendar($month, $year)
    {
        // Calculate the first and last day of the month
        $first_day_of_month = mktime(0, 0, 0, $month, 1, $year);

        // Get the names of the days of the week
        $days_of_week = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        // Get the full month name
        $month_name = date('F', $first_day_of_month);

        // Start the calendar table and display the month name and year
        echo '<div class="calendar-container">';
        echo '<h2>' . esc_html($year) . '</h2>';
        echo '<h3>' . esc_html($month_name) . '</h3>';
        echo '<table class="availability-calendar">';
        echo '<tr>';

        // Display the names of the days of the week
        foreach ($days_of_week as $day) {
            echo '<th class="day-header">' . esc_html($day) . '</th>';
        }

        echo '</tr><tr>';

        // Fill in the days of the month
        $day_of_week = date('w', $first_day_of_month);

        // Pad the calendar with empty cells if the month doesn't start on Sunday
        for ($i = 0; $i < $day_of_week; $i++) {
            echo '<td></td>';
        }

        for ($day = 1; $day <= date('t', $first_day_of_month); $day++) {
            if ($day_of_week == 7) {
                // Start a new row for each week
                echo '</tr><tr>';
                $day_of_week = 0;
            }

            // Check if the day is fully booked
            $date = sprintf('%d-%02d-%02d', $year, $month, $day);
            $reservation_instance = new \Staylodgic\Reservations();
            $remainingRooms = $reservation_instance->getTotalRemainingForAllRoomsOnDate($date);
            $class = $remainingRooms == 0 ? ' fully-booked' : '';

            // Display the day number with the class
            echo '<td data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' . esc_attr($remainingRooms) . '" data-remaining="' . esc_attr($remainingRooms) . '" class="day-cell' . esc_attr($class) . '">' . esc_html($day) . '</td>';

            $day_of_week++;
        }

        // Fill in the remaining cells if the month doesn't end on Saturday
        while ($day_of_week < 7) {
            echo '<td></td>';
            $day_of_week++;
        }

        echo '</tr>';
        echo '</table>';
        echo '</div>'; // Close the calendar-container div
    }
}

$instance = new \Staylodgic\AvailablityCalendarYear();
