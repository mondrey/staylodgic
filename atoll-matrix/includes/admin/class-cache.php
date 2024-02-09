<?php
namespace AtollMatrix;

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
    public function __construct($start_date = false, $end_date = false, $room_id = false, $transient_key = false, $contents = false)
    {
        $this->start_date    = $start_date;
        $this->end_date      = $end_date;
        $this->room_id       = $room_id;
        $this->transient_key = $transient_key;
        $this->contents      = $contents;

    }

    public function generateRoomCacheKey()
    {
        
        $transient_key = 'atollmatrix_avail_calendar_' . md5($this->room_id . '_' . $this->start_date . '_' . $this->end_date);

        return $transient_key;
    }

    public function deleteCache() {
        // Generate the transient key in the same way it was generated when set
        $transient_key = $this->generateRoomCacheKey();
        
        // Use delete_transient to remove it
        delete_transient($transient_key);
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

$instance = new \AtollMatrix\Cache();
