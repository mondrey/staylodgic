<?php
namespace Staylodgic;

class Availablity_Calendar_Base {

	protected $today;
	protected $week_ago;
	protected $stay_end_date;
	protected $rooms;
	protected $roomlist;
	protected $stay_start_date;
	protected $calendar_data;
	protected $reservation_tabs;
	protected $using_cache;
	protected $cached_data;
	protected $avail_confirmed_only;

	public function __construct( $stay_start_date = null, $stay_end_date = null ) {
		$this->set_start_date( $stay_start_date );
		$this->set_end_date( $stay_end_date );
		$this->get_today();
		$this->using_cache = false;
	}

	/**
	 * Method get_today
	 *
	 * @return void
	 */
	public function get_today() {
		$today       = new \DateTime();
		$this->today = $today->format( 'Y-m-d' );
	}

	/**
	 * Method set_start_date
	 *
	 * @param $stay_start_date
	 *
	 * @return void
	 */
	public function set_start_date( $stay_start_date ) {
		// Set stay_start_date to the 1st of the current month
		$this->stay_start_date = date( 'Y-m-01' );
	}

	/**
	 * Method set_end_date
	 *
	 * @param $stay_end_date $stay_end_date
	 *
	 * @return void
	 */
	public function set_end_date( $stay_end_date ) {
		// Set stay_end_date to the 5th of the next month
		$this->stay_end_date = date( 'Y-m-05', strtotime( '+1 month' ) );
	}

	/**
	 * Method set_num_days
	 *
	 * @param $stay_start_date $stay_start_date
	 * @param $stay_end_date $stay_end_date
	 *
	 * @return void
	 */
	public function set_num_days( $stay_start_date = false, $stay_end_date = false ) {

		if ( ! $stay_start_date ) {
			$start_date_obj = new \DateTime( $this->stay_start_date );
			$end_date_obj   = new \DateTime( $this->stay_end_date );
		} else {
			$start_date_obj = $stay_start_date instanceof \DateTime ? $stay_start_date : new \DateTime( $stay_start_date );
			$end_date_obj   = $stay_end_date instanceof \DateTime ? $stay_end_date : new \DateTime( $stay_end_date );
		}

		$stay_num_days = $start_date_obj->diff( $end_date_obj )->days + 1;

		return $stay_num_days;
	}

	/**
	 * Method get_dates
	 *
	 * @param $stay_start_date $stay_start_date
	 * @param $stay_end_date $stay_end_date
	 *
	 * @return void
	 */
	public function get_dates( $stay_start_date = false, $stay_end_date = false ) {

		if ( ! $stay_start_date ) {
			$start_date = new \DateTime( $this->stay_start_date );
			$end_date   = new \DateTime( $this->stay_end_date );
		} else {
			$start_date = $stay_start_date;
			$end_date   = $stay_end_date;
		}

		$number_of_days = self::set_num_days( $stay_start_date, $stay_end_date );

		$dates = array();
		for ( $day = 0; $day < $number_of_days; $day++ ) {
			if ( $stay_start_date instanceof \DateTime ) {
				$stay_current_date = clone $stay_start_date;
			} else {
				$stay_current_date = new \DateTime( $stay_start_date );
			}
			$stay_current_date->add( new \DateInterval( "P{$day}D" ) );
			$dates[] = $stay_current_date;
		}
		return $dates;
	}

	/**
	 * Method calculate_occupancy_total_for_range
	 *
	 * @param $start_date_string $start_date_string
	 * @param $end_date_string $end_date_string
	 *
	 * @return void
	 */
	public function calculate_occupancy_total_for_range( $start_date_string, $end_date_string ) {
		$stay_start_date   = new \DateTime( $start_date_string );
		$stay_end_date     = new \DateTime( $end_date_string );
		$stay_current_date = clone $stay_start_date;

		$total_occupancy_percentage = 0;
		$days_count                 = 0;

		while ( $stay_current_date <= $stay_end_date ) {
			$current_date_string         = $stay_current_date->format( 'Y-m-d' );
			$occupancy_percentage        = $this->calculate_occupancy_for_date( $current_date_string );
			$total_occupancy_percentage += $occupancy_percentage;
			++$days_count;
			$stay_current_date->modify( '+1 day' );
		}

		if ( $days_count > 0 ) {
			$average_occupancy_percentage = round( $total_occupancy_percentage / $days_count );
		} else {
			$average_occupancy_percentage = 0;
		}

		return $average_occupancy_percentage;
	}

	/**
	 * Method calculateAdrForDate
	 *
	 * @param $currentdate_string $currentdate_string
	 *
	 * @return void
	 */
	public function calculateAdrForDate( $currentdate_string ) {
		$stay_current_date    = new \DateTime( $currentdate_string );
		$total_room_revenue   = 0;
		$number_of_rooms_sold = 0;

		$confirmed_reservations = \Staylodgic\Reservations::getConfirmedReservations();

		if ( $confirmed_reservations->have_posts() ) {
			while ( $confirmed_reservations->have_posts() ) {
				$confirmed_reservations->the_post();

				$reservation_start_date = get_post_meta( get_the_ID(), 'staylodgic_checkin_date', true );
				$reservation_end_date   = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );
				$reservation_start_date = new \DateTime( $reservation_start_date );
				$reservation_end_date   = new \DateTime( $reservation_end_date );

					// Check if the current date falls within the reservation period
				if ( $stay_current_date >= $reservation_start_date && $stay_current_date < $reservation_end_date ) {
					$the_room_id = get_post_meta( get_the_ID(), 'staylodgic_room_id', true );

						// Get the room rate for the current date
					$the_room_rate = \Staylodgic\Rates::get_room_rate_by_date( $the_room_id, $stay_current_date->format( 'Y-m-d' ) );

					$total_room_revenue += $the_room_rate;
					++$number_of_rooms_sold;
				}
			}
		}

		wp_reset_postdata();

			// Calculate ADR
		$adr = 0;
		if ( $number_of_rooms_sold > 0 ) {
			$adr = round( $total_room_revenue / $number_of_rooms_sold );
		}

		return $adr;
	}

	/**
	 * Method calculate_occupancy_for_date
	 *
	 * @param $currentdate_string $currentdate_string
	 *
	 * @return void
	 */
	public function calculate_occupancy_for_date( $currentdate_string ) {
		$total_occupied_rooms  = 0;
		$total_available_rooms = 0;
		$this->rooms           = \Staylodgic\Rooms::query_rooms();

		foreach ( $this->rooms as $room ) {
				// Increment the total number of occupied rooms

			$reservation_instance  = new \Staylodgic\Reservations( $currentdate_string, $room->ID );
			$total_occupied_rooms += $reservation_instance->get_direct_remaining_room_count();
				// Increment the total number of available rooms
			$total_available_rooms += \Staylodgic\Rooms::get_total_operating_room_qty_for_date( $room->ID, $currentdate_string );

		}

		wp_reset_postdata();

			// Calculate the occupancy percentage
		if ( $total_available_rooms > 0 ) {
			$occupancy_percentage = 100 - ( round( ( $total_occupied_rooms / $total_available_rooms ) * 100 ) );
		} else {
			$occupancy_percentage = 100;
		}

		return $occupancy_percentage;
	}

	/**
	 * Method calculate_remaining_rooms_for_date
	 *
	 * @param $currentdate_string $currentdate_string
	 *
	 * @return void
	 */
	public function calculate_remaining_rooms_for_date( $currentdate_string ) {
		$total_remaining_rooms = 0;
		$this->rooms           = \Staylodgic\Rooms::query_rooms();

		foreach ( $this->rooms as $room ) {
				// Increment the total number of occupied rooms

			$reservation_instance   = new \Staylodgic\Reservations( $currentdate_string, $room->ID );
			$total_remaining_rooms += $reservation_instance->get_direct_remaining_room_count();
		}

		wp_reset_postdata();

		return $total_remaining_rooms;
	}
}
