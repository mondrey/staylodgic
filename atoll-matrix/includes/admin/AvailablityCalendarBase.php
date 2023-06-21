<?php
namespace Cognitive;
class AvailablityCalendarBase {
	protected $today;
	protected $weekAgo;
	protected $endDate;
	protected $rooms;
	protected $roomlist;
	protected $startDate;
	protected $numDays;

	public function __construct($startDate = null, $endDate = null) {
		$this->setStartDate($startDate);
		$this->setEndDate($endDate);
		
		$this->rooms = \Cognitive\Rooms::queryRooms();
		$this->roomlist = \Cognitive\Rooms::getRoomList();

		$this->confirmedReservations = \Cognitive\Reservations::getConfirmedReservations();
		$this->getToday();
	}

	public function getToday() {
		$today = new \DateTime();
		$this->today = $today->format('Y-m-d');
	}

	public function setStartDate($startDate) {
		if ($startDate === null) {
			$week_ago = (new \DateTime())->modify('-9 days');
			$this->startDate = $week_ago->format('Y-m-d');
		} else {
			$this->startDate = (new \DateTime($startDate))->format('Y-m-d');
		}
	}

	public function setEndDate($endDate) {
		if ($endDate === null) {
			$end_date = (new \DateTime())->modify('+30 days');
			$this->endDate = $end_date->format('Y-m-d');
		} else {
			$this->endDate = (new \DateTime($endDate))->format('Y-m-d');
		}
	}

	public function setNumDays() {
		$startDate = new \DateTime($this->startDate);
		$endDate = new \DateTime($this->endDate);
		$this->numDays = $endDate->diff($startDate)->days + 1;
	}

	public function getDates() {
		$startDate = new \DateTime($this->startDate);
		$endDate = new \DateTime($this->endDate);

		$this->setNumDays();

		$dates = [];
		for ($day = 0; $day < 30; $day++) {
			$currentDate = clone $startDate;
			$currentDate->add(new \DateInterval("P{$day}D"));
			$dates[] = $currentDate;
		}
		return $dates;
	}

	public function calculateOccupancyTotalForRange( $startDateString, $endDateString ) {
		$startDate = new \DateTime( $startDateString );
		$endDate = new \DateTime( $endDateString );
		$currentDate = clone $startDate;
		
		$totalOccupancyPercentage = 0;
		$daysCount = 0;
	
		while ( $currentDate <= $endDate ) {
			$currentDateString = $currentDate->format('Y-m-d');
			$occupancyPercentage = $this->calculateOccupancyForDate( $currentDateString );
			$totalOccupancyPercentage += $occupancyPercentage;
			$daysCount++;
			$currentDate->modify('+1 day');
		}
	
		if ($daysCount > 0) {
			$averageOccupancyPercentage = round( $totalOccupancyPercentage / $daysCount );
		} else {
			$averageOccupancyPercentage = 0;
		}
	
		return $averageOccupancyPercentage;
	}

	function calculateAdrForDate( $currentdateString ) {
		$currentDate = new \DateTime( $currentdateString );
		$totalRoomRevenue = 0;
		$numberOfRoomsSold = 0;
	
		if ( $this->confirmedReservations->have_posts() ) {
			while ( $this->confirmedReservations->have_posts() ) {
				$this->confirmedReservations->the_post();
	
				$reservationStartDate = get_post_meta( get_the_ID(), 'pagemeta_checkin_date', true );
				$reservationEndDate = get_post_meta( get_the_ID(), 'pagemeta_checkout_date', true );
	
				$reservationStartDate = new \DateTime( $reservationStartDate );
				$reservationEndDate = new \DateTime( $reservationEndDate );
	
				// Check if the current date falls within the reservation period
				if ( $currentDate >= $reservationStartDate && $currentDate < $reservationEndDate ) {
					$roomID = get_post_meta(get_the_ID(), 'pagemeta_room_name', true);
	
					// Get the room rate for the current date
					$roomRate = \Cognitive\Rates::getRoomRateByDate( $roomID, $currentDate->format('Y-m-d') );
	
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
	
	public function calculateOccupancyForDate($currentdateString) {
		$totalOccupiedRooms = 0;
		$totalAvailableRooms = 0;
	
		foreach($this->rooms as $room){
			// Increment the total number of occupied rooms
			$totalOccupiedRooms += cognitive_calculate_reserved_rooms( $currentdateString, $room->ID );
			// Increment the total number of available rooms
			$totalAvailableRooms += cognitive_get_max_quantity_for_room( $room->ID, $currentdateString);
	
			//echo '<br>'.$currentdateString.'<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
			//echo '<br>'. $room->ID . '||' . $totalOccupiedRooms. '||' . $totalAvailableRooms . '<br>';
		}
	
		wp_reset_postdata();
	
		// Calculate the occupancy percentage
		if ($totalAvailableRooms > 0) {
			$occupancyPercentage = round(($totalOccupiedRooms / $totalAvailableRooms) * 100);
		} else {
			$occupancyPercentage = 0;
		}
	
		return $occupancyPercentage;
	}

}
