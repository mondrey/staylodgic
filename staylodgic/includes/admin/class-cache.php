<?php
namespace Staylodgic;

class Cache
{

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
    public function __construct($room_id = false, $start_date = false, $end_date = false, $transient_key = false, $contents = false)
    {
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
    public static function clearAllCache() {
        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey);

        if (is_array($cacheIndex)) {
            foreach ($cacheIndex as $transientKey => $details) {
                // Delete each transient in the index
                delete_transient($transientKey);
            }
        }

        // Clear the cache index
        delete_transient($cacheIndexKey);
    }
    
    /**
     * Method updateCacheIndex
     *
     * @param $transient_key
     *
     * @return void
     */
    private function updateCacheIndex( $transient_key = false ) {

        if (!$transient_key) {
            $transient_key = $this->transient_key;
        }

        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey) ?: [];

        $cacheIndex[$transient_key] = [
            'room_id' => $this->room_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];

        set_transient($cacheIndexKey, $cacheIndex, 0); // No expiration
    }
    
    /**
     * Method generate_analytics_cache_key
     *
     * @param $key
     *
     * @return void
     */
    public function generate_analytics_cache_key( $key )
    {
        
        $transient_key = 'staylodgic_analytics_' . md5($key);

        return $transient_key;
    }
    
    /**
     * Method generateRoomCacheKey
     *
     * @return void
     */
    public function generateRoomCacheKey()
    {
        
        $transient_key = 'staylodgic_avail_calendar_' . md5($this->room_id . '_' . $this->start_date . '_' . $this->end_date);

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

        if (!$transient_key) {
            $transient_key = $this->transient_key;
        }
        // Generate the transient key in the same way it was generated when set
        $transient_key = $this->generateRoomCacheKey();
        
        // Use delete_transient to remove it
        delete_transient($transient_key);
        $this->deleteCacheIndexEntry();
    }
    
    /**
     * Method deleteCacheIndexEntry
     *
     * @return void
     */
    private function deleteCacheIndexEntry() {
        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey);

        if (isset($cacheIndex[$this->transient_key])) {
            unset($cacheIndex[$this->transient_key]);
            set_transient($cacheIndexKey, $cacheIndex, 0); // Update the index without the deleted entry
        }
    }
    
    /**
     * Method invalidateCachesByRoomAndDate
     *
     * @param $room_id
     * @param $affectedStartDate
     * @param $affectedEndDate
     *
     * @return void
     */
    public static function invalidateCachesByRoomAndDate($room_id, $affectedStartDate, $affectedEndDate) {
        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey);

        if ( isset( $cacheIndex ) && is_array( $cacheIndex ) ) {
    
            foreach ($cacheIndex as $transientKey => $details) {
                if ($details['room_id'] == $room_id && 
                    ($affectedStartDate <= $details['end_date'] && $affectedEndDate >= $details['start_date'])) {
                    delete_transient($transientKey);
                    unset($cacheIndex[$transientKey]);
                }
            }
    
            set_transient($cacheIndexKey, $cacheIndex, 0);
        }
    }
    
    /**
     * Method setCache
     *
     * @param $transient_key
     * @param $contents
     *
     * @return void
     */
    public function setCache($transient_key = false, $contents = false)
    {

		if ( ! $transient_key ) {
			$transient_key = $this->transient_key;
		}
		if ( ! $contents ) {
			$contents = $this->contents;
		}

        set_transient($transient_key, $contents, 12 * HOUR_IN_SECONDS);
        $this->updateCacheIndex( $transient_key );
    }
    
    /**
     * Method get_cache
     *
     * @param $transient_key
     *
     * @return void
     */
    public function get_cache($transient_key = false)
    {

        if (!$transient_key) {
            $transient_key = $this->transient_key;
        }
        $cached_calendar = get_transient($transient_key);

        return $cached_calendar;
    }
    
    /**
     * Method has_cache
     *
     * @param $transient_key
     *
     * @return void
     */
    public function has_cache($transient_key = false)
    {

        if (false !== $this->get_cache($transient_key)) {
            return true;
        }

        return false;
    }
    
    /**
     * Method is_cache_allowed
     *
     * @return void
     */
    public function is_cache_allowed()
    {

        return true;

    }

}

$instance = new \Staylodgic\Cache();
