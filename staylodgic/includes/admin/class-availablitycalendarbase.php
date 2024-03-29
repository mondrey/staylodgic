<?php
namespace Staylodgic;

class AvailablityCalendarBase
{
    protected $today;
    protected $weekAgo;
    protected $endDate;
    protected $rooms;
    protected $roomlist;
    protected $startDate;
    protected $calendarData;
    protected $reservation_tabs;
    protected $usingCache;
    protected $cachedData;
    protected $availConfirmedOnly;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        $this->getToday();
        $this->usingCache = false;
    }

    public function getToday()
    {
        $today       = new \DateTime();
        $this->today = $today->format('Y-m-d');
    }

    public function setStartDate($startDate)
    {
        // Set startDate to the 1st of the current month
        $this->startDate = date('Y-m-01');
    }
    
    public function setEndDate($endDate)
    {
        // Set endDate to the 5th of the next month
        $this->endDate = date('Y-m-05', strtotime('+1 month'));
    }
    
    public function setNumDays($startDate = false, $endDate = false)
    {

        if (!$startDate) {
            $start_Date = new \DateTime($this->startDate);
            $end_Date   = new \DateTime($this->endDate);
        } else {
            $start_Date = $startDate instanceof \DateTime ? $startDate : new \DateTime($startDate);
            $end_Date   = $endDate instanceof \DateTime ? $endDate : new \DateTime($endDate);
        }

        $numDays = $start_Date->diff($end_Date)->days + 1;

        if (!$startDate) {
            // error_log('Start: ' . $startDate . ' End: ' . $endDate . ' Number: ' . $numDays);
        }
        return $numDays;
    }

    public function getDates($startDate = false, $endDate = false)
    {

        if (!$startDate) {
            $start_date = new \DateTime($this->startDate);
            $end_date   = new \DateTime($this->endDate);
        } else {
            $start_date = $startDate;
            $end_date   = $endDate;
        }

        $number_of_days = self::setNumDays( $startDate, $endDate);

        $dates = [];
        for ($day = 0; $day < $number_of_days; $day++) {
            if ($startDate instanceof \DateTime) {
                $currentDate = clone $startDate;
            } else {
                $currentDate = new \DateTime($startDate);
            }
            $currentDate->add(new \DateInterval("P{$day}D"));
            $dates[] = $currentDate;
        }
        return $dates;
    }

    public function calculateOccupancyTotalForRange($startDateString, $endDateString)
    {
        $startDate   = new \DateTime($startDateString);
        $endDate     = new \DateTime($endDateString);
        $currentDate = clone $startDate;

        $totalOccupancyPercentage = 0;
        $daysCount                = 0;

        while ($currentDate <= $endDate) {
            $currentDateString         = $currentDate->format('Y-m-d');
            $occupancyPercentage       = $this->calculateOccupancyForDate($currentDateString);
            $totalOccupancyPercentage += $occupancyPercentage;
            $daysCount++;
            $currentDate->modify('+1 day');
        }

        if ($daysCount > 0) {
            $averageOccupancyPercentage = round($totalOccupancyPercentage / $daysCount);
        } else {
            $averageOccupancyPercentage = 0;
        }

        return $averageOccupancyPercentage;
    }

    public function calculateAdrForDate($currentdateString)
    {
        $currentDate       = new \DateTime($currentdateString);
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
                if ($currentDate >= $reservationStartDate && $currentDate < $reservationEndDate) {
                    $roomID = get_post_meta(get_the_ID(), 'staylodgic_room_id', true);

                      // Get the room rate for the current date
                    $roomRate = \Staylodgic\Rates::getRoomRateByDate($roomID, $currentDate->format('Y-m-d'));

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

    public function calculateOccupancyForDate($currentdateString)
    {
        $totalOccupiedRooms  = 0;
        $totalAvailableRooms = 0;
        $this->rooms         = \Staylodgic\Rooms::queryRooms();

        foreach ($this->rooms as $room) {
              // Increment the total number of occupied rooms

            $reservation_instance  = new \Staylodgic\Reservations($currentdateString, $room->ID);
            $totalOccupiedRooms   += $reservation_instance->getDirectRemainingRoomCount();
              // Increment the total number of available rooms
            $totalAvailableRooms += \Staylodgic\Rooms::getTotalOperatingRoomQtyForDate($room->ID, $currentdateString);

              //echo '<br>'.$currentdateString.'<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
              //echo '<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
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

    public function calculateRemainingRoomsForDate($currentdateString)
    {
        $totalRemainingRooms  = 0;
        $this->rooms         = \Staylodgic\Rooms::queryRooms();

        foreach ($this->rooms as $room) {
              // Increment the total number of occupied rooms

            $reservation_instance  = new \Staylodgic\Reservations($currentdateString, $room->ID);
            $totalRemainingRooms   += $reservation_instance->getDirectRemainingRoomCount();

              //echo '<br>'.$currentdateString.'<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
              //echo '<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
        }

        wp_reset_postdata();

        return $totalRemainingRooms;
    }

}