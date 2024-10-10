<?php

namespace Staylodgic;

class Availablity_Calendar_Year extends Availablity_Calendar_Base {


	public function __construct( $stay_start_date = null, $stay_end_date = null ) {
		parent::__construct( $stay_start_date, $stay_end_date );

		add_action( 'admin_menu', array( $this, 'availablity_calendar_year_display' ) );
	}

	/**
	 * Method availablity_calendar_year_display Add the Availability menu item to the admin menu
	 *
	 * @return void
	 */
	public function availablity_calendar_year_display() {
		// Add the Availability submenu item under the parent menu
		add_submenu_page(
			'slgc-dashboard',
			__( 'Annual Availability', 'staylodgic' ),
			__( 'Annual Availability', 'staylodgic' ),
			'edit_posts',
			'slgc-availability-yearly',
			array( $this, 'room_reservation_plugin_display_availability_calendar_yearly' )
		);
	}

	/**
	 * Method room_reservation_plugin_display_availability_calendar_yearly
	 *
	 * @return void
	 */
	public function room_reservation_plugin_display_availability_calendar_yearly() {
		// Output the HTML for the Availability page
		?>
		<div class="wrap">
			<?php
			if ( ! \Staylodgic\Rooms::has_rooms() ) {
				echo '<h1>' . esc_html__( 'No Rooms Found', 'staylodgic' ) . '</h1>';
				return;
			} else {

				echo '<h1>' . esc_html__( '12 Months Availability Overview', 'staylodgic' ) . '</h1>';
			}

			echo '<div class="calendars-container">';
				// Display the calendar for the current month and the next six months
				$current_month = gmdate( 'n' );
				$current_year  = gmdate( 'Y' );
			for ( $i = 0; $i <= 12; $i++ ) {
				$month = ( $current_month + $i - 1 ) % 12 + 1;
				$year  = $current_year + floor( ( $current_month + $i - 1 ) / 12 );
				$this->display_monthly_calendar( $month, $year );
			}
			echo '</div>';
			?>
		</div>
		<?php
	}

	/**
	 * Method display_monthly_calendar
	 *
	 * @param $month $month
	 * @param $year $year
	 *
	 * @return void
	 */
	public function display_monthly_calendar( $month, $year ) {
		// Calculate the first and last day of the month
		$first_day_of_month = mktime( 0, 0, 0, $month, 1, $year );

		// Get the names of the days of the week
		$days_of_week = array( 'S', 'M', 'T', 'W', 'T', 'F', 'S' );

		// Get the full month name
		$month_name = gmdate( 'F', $first_day_of_month );

		// Start the calendar table and display the month name and year
		echo '<div class="calendar-container">';
		echo '<h2>' . esc_html( $year ) . '</h2>';
		echo '<h3>' . esc_html( $month_name ) . '</h3>';
		echo '<table class="availability-calendar">';
		echo '<tr>';

		// Display the names of the days of the week
		foreach ( $days_of_week as $day ) {
			echo '<th class="day-header">' . esc_html( $day ) . '</th>';
		}

		echo '</tr><tr>';

		// Fill in the days of the month
		$day_of_week = gmdate( 'w', $first_day_of_month );

		// Pad the calendar with empty cells if the month doesn't start on Sunday
		for ( $i = 0; $i < $day_of_week; $i++ ) {
			echo '<td></td>';
		}

		$days_in_month = gmdate( 't', $first_day_of_month ); // Store function result
		for ( $day = 1; $day <= $days_in_month; $day++ ) {

			if ( 7 === (int) $day_of_week ) {
				// Start a new row for each week
				echo '</tr><tr>';
				$day_of_week = 0;
			}

			// Check if the day is fully booked
			$date                 = sprintf( '%d-%02d-%02d', $year, $month, $day );
			$reservation_instance = new \Staylodgic\Reservations();
			$the_remaining_rooms  = $reservation_instance->get_total_remaining_for_all_rooms_on_date( $date );
			$class                = ''; // Initialize the variable with an empty string

			if ( 0 === (int) $the_remaining_rooms ) {
				$class = ' fully-booked';
			} else {
				$class = '';
			}

			// Display the day number with the class
			echo '<td data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' . esc_attr( $the_remaining_rooms ) . '" data-remaining="' . esc_attr( $the_remaining_rooms ) . '" class="day-cell' . esc_attr( $class ) . '">' . esc_html( $day ) . '</td>';

			++$day_of_week;
		}

		// Fill in the remaining cells if the month doesn't end on Saturday
		while ( $day_of_week < 7 ) {
			echo '<td></td>';
			++$day_of_week;
		}

		echo '</tr>';
		echo '</table>';
		echo '</div>'; // Close the calendar-container div
	}
}

$instance = new \Staylodgic\Availablity_Calendar_Year();
