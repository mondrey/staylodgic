<?php
namespace Staylodgic;

class AvailablityCalendarBase
{
    protected $today;
    protected $weekAgo;
    protected $stay_end_date;
    protected $rooms;
    protected $roomlist;
    protected $startDate;
    protected $calendarData;
    protected $reservation_tabs;
    protected $usingCache;
    protected $cached_data;
    protected $availConfirmedOnly;

    public function __construct($startDate = null, $stay_end_date = null)
    {
        $this->setStartDate($startDate);
        $this->setEndDate($stay_end_date);
        $this->getToday();
        $this->usingCache = false;
    }
    
    /**
     * Method getToday
     *
     * @return void
     */
    public function getToday()
    {
        $today       = new \DateTime();
        $this->today = $today->format('Y-m-d');
    }
    
    /**
     * Method setStartDate
     *
     * @param $startDate
     *
     * @return void
     */
    public function setStartDate($startDate)
    {
        // Set startDate to the 1st of the current month
        $this->startDate = date('Y-m-01');
    }
        
    /**
     * Method setEndDate
     *
     * @param $stay_end_date $stay_end_date
     *
     * @return void
     */
    public function setEndDate($stay_end_date)
    {
        // Set stay_end_date to the 5th of the next month
        $this->stay_end_date = date('Y-m-05', strtotime('+1 month'));
    }
        
    /**
     * Method setNumDays
     *
     * @param $startDate $startDate
     * @param $stay_end_date $stay_end_date
     *
     * @return void
     */
    public function setNumDays($startDate = false, $stay_end_date = false)
    {

        if (!$startDate) {
            $start_Date = new \DateTime($this->startDate);
            $end_Date   = new \DateTime($this->stay_end_date);
        } else {
            $start_Date = $startDate instanceof \DateTime ? $startDate : new \DateTime($startDate);
            $end_Date   = $stay_end_date instanceof \DateTime ? $stay_end_date : new \DateTime($stay_end_date);
        }

        $numDays = $start_Date->diff($end_Date)->days + 1;

        return $numDays;
    }
    
    /**
     * Method getDates
     *
     * @param $startDate $startDate
     * @param $stay_end_date $stay_end_date
     *
     * @return void
     */
    public function getDates($startDate = false, $stay_end_date = false)
    {

        if (!$startDate) {
            $start_date = new \DateTime($this->startDate);
            $end_date   = new \DateTime($this->stay_end_date);
        } else {
            $start_date = $startDate;
            $end_date   = $stay_end_date;
        }

        $number_of_days = self::setNumDays( $startDate, $stay_end_date);

        $dates = [];
        for ($day = 0; $day < $number_of_days; $day++) {
            if ($startDate instanceof \DateTime) {
                $stay_current_date = clone $startDate;
            } else {
                $stay_current_date = new \DateTime($startDate);
            }
            $stay_current_date->add(new \DateInterval("P{$day}D"));
            $dates[] = $stay_current_date;
        }
        return $dates;
    }
    
    /**
     * Method calculateOccupancyTotalForRange
     *
     * @param $startDateString $startDateString
     * @param $endDateString $endDateString
     *
     * @return void
     */
    public function calculateOccupancyTotalForRange($startDateString, $endDateString)
    {
        $startDate   = new \DateTime($startDateString);
        $stay_end_date     = new \DateTime($endDateString);
        $stay_current_date = clone $startDate;

        $totalOccupancyPercentage = 0;
        $daysCount                = 0;

        while ($stay_current_date <= $stay_end_date) {
            $currentDateString         = $stay_current_date->format('Y-m-d');
            $occupancyPercentage       = $this->calculateOccupancyForDate($currentDateString);
            $totalOccupancyPercentage += $occupancyPercentage;
            $daysCount++;
            $stay_current_date->modify('+1 day');
        }

        if ($daysCount > 0) {
            $averageOccupancyPercentage = round($totalOccupancyPercentage / $daysCount);
        } else {
            $averageOccupancyPercentage = 0;
        }

        return $averageOccupancyPercentage;
    }
    
    /**
     * Method calculateAdrForDate
     *
     * @param $currentdateString $currentdateString
     *
     * @return void
     */
    public function calculateAdrForDate($currentdateString)
    {
        $stay_current_date       = new \DateTime($currentdateString);
        $totalRoomRevenue  = 0;
        $numberOfRoomsSold = 0;

        $confirmed_reservations = \Staylodgic\Reservations::getConfirmedReservations();

        if ($confirmed_reservations->have_posts()) {
            while ($confirmed_reservations->have_posts()) {
                $confirmed_reservations->the_post();

                $reservationStartDate = get_post_meta(get_the_ID(), 'staylodgic_checkin_date', true);
                $reservationEndDate   = get_post_meta(get_the_ID(), 'staylodgic_checkout_date', true);
                $reservationStartDate = new \DateTime($reservationStartDate);
                $reservationEndDate   = new \DateTime($reservationEndDate);

                  // Check if the current date falls within the reservation period
                if ($stay_current_date >= $reservationStartDate && $stay_current_date < $reservationEndDate) {
                    $roomID = get_post_meta(get_the_ID(), 'staylodgic_room_id', true);

                      // Get the room rate for the current date
                    $roomRate = \Staylodgic\Rates::getRoomRateByDate($roomID, $stay_current_date->format('Y-m-d'));

                    $totalRoomRevenue += $roomRate;
                    $numberOfRoomsSold++;
                }
            }
        }

        wp_reset_postdata();

          // Calculate ADR
        $adr = 0;
        if ($numberOfRoomsSold > 0) {
            $adr = round($totalRoomRevenue / $numberOfRoomsSold);
        }

        return $adr;
    }
    
    /**
     * Method calculateOccupancyForDate
     *
     * @param $currentdateString $currentdateString
     *
     * @return void
     */
    public function calculateOccupancyForDate($currentdateString)
    {
        $totalOccupiedRooms  = 0;
        $totalAvailableRooms = 0;
        $this->rooms         = \Staylodgic\Rooms::query_rooms();

        foreach ($this->rooms as $room) {
              // Increment the total number of occupied rooms

            $reservation_instance  = new \Staylodgic\Reservations($currentdateString, $room->ID);
            $totalOccupiedRooms   += $reservation_instance->getDirectRemainingRoomCount();
              // Increment the total number of available rooms
            $totalAvailableRooms += \Staylodgic\Rooms::getTotalOperatingRoomQtyForDate($room->ID, $currentdateString);

        }

        wp_reset_postdata();

          // Calculate the occupancy percentage
        if ($totalAvailableRooms > 0) {
            $occupancyPercentage = 100 - ( round(($totalOccupiedRooms / $totalAvailableRooms) * 100) );
        } else {
            $occupancyPercentage = 100;
        }

        return $occupancyPercentage;
    }
    
    /**
     * Method calculateRemainingRoomsForDate
     *
     * @param $currentdateString $currentdateString
     *
     * @return void
     */
    public function calculateRemainingRoomsForDate($currentdateString)
    {
        $totalRemainingRooms  = 0;
        $this->rooms         = \Staylodgic\Rooms::query_rooms();

        foreach ($this->rooms as $room) {
              // Increment the total number of occupied rooms

            $reservation_instance  = new \Staylodgic\Reservations($currentdateString, $room->ID);
            $totalRemainingRooms   += $reservation_instance->getDirectRemainingRoomCount();
        }

        wp_reset_postdata();

        return $totalRemainingRooms;
    }

}