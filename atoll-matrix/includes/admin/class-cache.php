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
     * Deletes all cache entries and the cache index.
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

    public function generateAnalyticsCacheKey( $key )
    {
        
        $transient_key = 'staylodgic_analytics_' . md5($key);

        return $transient_key;
    }

    public function generateRoomCacheKey()
    {
        
        $transient_key = 'staylodgic_avail_calendar_' . md5($this->room_id . '_' . $this->start_date . '_' . $this->end_date);

        return $transient_key;
    }

    public function deleteCache( $transient_key = false ) {

        if (!$transient_key) {
            $transient_key = $this->transient_key;
        }
        // Generate the transient key in the same way it was generated when set
        $transient_key = $this->generateRoomCacheKey();
        
        // Use delete_transient to remove it
        delete_transient($transient_key);
        $this->deleteCacheIndexEntry();
    }

    private function deleteCacheIndexEntry() {
        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey);

        if (isset($cacheIndex[$this->transient_key])) {
            unset($cacheIndex[$this->transient_key]);
            set_transient($cacheIndexKey, $cacheIndex, 0); // Update the index without the deleted entry
        }
    }

    public static function invalidateCachesByRoomAndDate($room_id, $affectedStartDate, $affectedEndDate) {
        $cacheIndexKey = 'staylodgic_avail_calendar_index';
        $cacheIndex = get_transient($cacheIndexKey);

        if ( isset( $cacheIndex ) && is_array( $cacheIndex ) ) {
            error_log('-------- Removing Cache ---------');
            error_log( $room_id );
            error_log(print_r($cacheIndex,true));
    
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

    public function getCache($transient_key = false)
    {

        if (!$transient_key) {
            $transient_key = $this->transient_key;
        }
        $cached_calendar = get_transient($transient_key);

        return $cached_calendar;
    }

    public function hasCache($transient_key = false)
    {

        if (false !== $this->getCache($transient_key)) {
            return true;
        }

        return false;
    }

    public function isCacheAllowed()
    {

        return true;

    }

}

$instance = new \Staylodgic\Cache();
