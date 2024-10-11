<?php
namespace Staylodgic;

class Cache {


	private $start_date;
	private $end_date;
	private $room_id;
	private $transient_key;
	private $contents;

	/**
	 * Summary of __construct
	 * @param mixed $date
	 * @param mixed $room_id
	 * @param mixed $reservation_id
	 * @param mixed $reservation_id_excluded
	 */
	public function __construct( $room_id = false, $start_date = false, $end_date = false, $transient_key = false, $contents = false ) {
		$this->start_date    = $start_date;
		$this->end_date      = $end_date;
		$this->room_id       = $room_id;
		$this->transient_key = $transient_key;
		$this->contents      = $contents;
	}

	/**
	 * Method Deletes all cache entries and the cache index.
	 *
	 * @return void
	 */
	public static function clear_all_cache() {
		$cache_index_key = 'staylodgic_avail_calendar_index';
		$cache_index     = get_transient( $cache_index_key );

		if ( is_array( $cache_index ) ) {
			foreach ( $cache_index as $cache_transient_key => $details ) {
				// Delete each transient in the index
				delete_transient( $cache_transient_key );
			}
		}

		// Clear the cache index
		delete_transient( $cache_index_key );
	}

	/**
	 * Method update_cache_index
	 *
	 * @param $transient_key
	 *
	 * @return void
	 */
	private function update_cache_index( $transient_key = false ) {

		if ( ! $transient_key ) {
			$transient_key = $this->transient_key;
		}

		$cache_index_key = 'staylodgic_avail_calendar_index';
		$cache_index     = get_transient( $cache_index_key );

		if ( ! $cache_index ) {
			$cache_index = array();
		}

		$cache_index[ $transient_key ] = array(
			'room_id'    => $this->room_id,
			'start_date' => $this->start_date,
			'end_date'   => $this->end_date,
		);

		set_transient( $cache_index_key, $cache_index, 0 ); // No expiration
	}

	/**
	 * Method generate_analytics_cache_key
	 *
	 * @param $key
	 *
	 * @return void
	 */
	public function generate_analytics_cache_key( $key ) {

		$transient_key = 'staylodgic_analytics_' . md5( $key );

		return $transient_key;
	}

	/**
	 * Method generate_room_cache_key
	 *
	 * @return void
	 */
	public function generate_room_cache_key() {

		$transient_key = 'staylodgic_avail_calendar_' . md5( $this->room_id . '_' . $this->start_date . '_' . $this->end_date );

		return $transient_key;
	}

	/**
	 * Method delete_cache
	 *
	 * @param $transient_key
	 *
	 * @return void
	 */
	public function delete_cache( $transient_key = false ) {

		if ( ! $transient_key ) {
			$transient_key = $this->transient_key;
		}
		// Generate the transient key in the same way it was generated when set
		$transient_key = $this->generate_room_cache_key();

		// Use delete_transient to remove it
		delete_transient( $transient_key );
		$this->delete_cache_index_entry();
	}

	/**
	 * Method delete_cache_index_entry
	 *
	 * @return void
	 */
	private function delete_cache_index_entry() {
		$cache_index_key = 'staylodgic_avail_calendar_index';
		$cache_index     = get_transient( $cache_index_key );

		if ( isset( $cache_index[ $this->transient_key ] ) ) {
			unset( $cache_index[ $this->transient_key ] );
			set_transient( $cache_index_key, $cache_index, 0 ); // Update the index without the deleted entry
		}
	}

	/**
	 * Method invalidate_caches_by_room_and_date
	 *
	 * @param $room_id
	 * @param $affected_start_date
	 * @param $affected_end_date
	 *
	 * @return void
	 */
	public static function invalidate_caches_by_room_and_date( $room_id, $affected_start_date, $affected_end_date ) {
		$cache_index_key = 'staylodgic_avail_calendar_index';
		$cache_index     = get_transient( $cache_index_key );

		if ( isset( $cache_index ) && is_array( $cache_index ) ) {

			foreach ( $cache_index as $cache_transient_key => $details ) {

				if ( (int) $details['room_id'] === (int) $room_id &&
					( $affected_start_date <= $details['end_date'] && $affected_end_date >= $details['start_date'] ) ) {
					delete_transient( $cache_transient_key );
					unset( $cache_index[ $cache_transient_key ] );
				}
			}

			set_transient( $cache_index_key, $cache_index, 0 );
		}
	}

	/**
	 * Method set_cache
	 *
	 * @param $transient_key
	 * @param $contents
	 *
	 * @return void
	 */
	public function set_cache( $transient_key = false, $contents = false ) {

		if ( ! $transient_key ) {
			$transient_key = $this->transient_key;
		}
		if ( ! $contents ) {
			$contents = $this->contents;
		}

		set_transient( $transient_key, $contents, 12 * HOUR_IN_SECONDS );
		$this->update_cache_index( $transient_key );
	}

	/**
	 * Method get_cache
	 *
	 * @param $transient_key
	 *
	 * @return void
	 */
	public function get_cache( $transient_key = false ) {

		if ( ! $transient_key ) {
			$transient_key = $this->transient_key;
		}
		$cached_calendar = get_transient( $transient_key );

		return $cached_calendar;
	}

	/**
	 * Method has_cache
	 *
	 * @param $transient_key
	 *
	 * @return void
	 */
	public function has_cache( $transient_key = false ) {

		if ( false !== $this->get_cache( $transient_key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Method is_cache_allowed
	 *
	 * @return void
	 */
	public function is_cache_allowed() {

		return true;
	}
}

$instance = new \Staylodgic\Cache();
