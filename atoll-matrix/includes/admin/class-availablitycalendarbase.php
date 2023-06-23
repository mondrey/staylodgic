<?php
namespace AtollMatrix;
class AvailablityCalendarBase {
	protected $today;
	protected $weekAgo;
	protected $endDate;
	protected $rooms;
	protected $roomlist;
	protected $startDate;

	public function __construct($startDate = null, $endDate = null) {
		$this->setStartDate($startDate);
		$this->setEndDate($endDate);
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

	public function setNumDays( $startDate = false, $endDate = false ) {
		
		if ( !$startDate ) {
			$start_Date = new \DateTime($this->startDate);
			$end_Date = new \DateTime($this->endDate);
		} else {
			$start_Date = $startDate instanceof \DateTime ? $startDate : new \DateTime($startDate);
			$end_Date = $endDate instanceof \DateTime ? $endDate : new \DateTime($endDate);
		}
			
		$numDays = $start_Date->diff($end_Date)->days + 1;
	
		if ( !$startDate ) {
			error_log( 'Start: ' . $startDate . ' End: ' . $endDate . ' Number: '. $numDays );
		}
		return $numDays;
	}
	
	public function getDates( $startDate = false, $endDate = false ) {

		if ( !$startDate ) {
			$start_date = new \DateTime($this->startDate);
			$end_date = new \DateTime($this->endDate);
		} else {
			$start_date = $startDate;
			$end_date = $endDate;
		}

		$dates = [];
		for ($day = 0; $day < 30; $day++) {
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

		$confirmed_reservations = \AtollMatrix\Reservations::getConfirmedReservations();
	
		if ( $confirmed_reservations->have_posts() ) {
			while ( $confirmed_reservations->have_posts() ) {
				$confirmed_reservations->the_post();
	
				$reservationStartDate = get_post_meta( get_the_ID(), 'atollmatrix_checkin_date', true );
				$reservationEndDate = get_post_meta( get_the_ID(), 'atollmatrix_checkout_date', true );
	
				$reservationStartDate = new \DateTime( $reservationStartDate );
				$reservationEndDate = new \DateTime( $reservationEndDate );
	
				// Check if the current date falls within the reservation period
				if ( $currentDate >= $reservationStartDate && $currentDate < $reservationEndDate ) {
					$roomID = get_post_meta(get_the_ID(), 'atollmatrix_room_id', true);
	
					// Get the room rate for the current date
					$roomRate = \AtollMatrix\Rates::getRoomRateByDate( $roomID, $currentDate->format('Y-m-d') );
	
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

		
		$this->rooms = \AtollMatrix\Rooms::queryRooms();
		
		foreach($this->rooms as $room){
			// Increment the total number of occupied rooms

			$reservation_instance = new \AtollMatrix\Reservations( $currentdateString, $room->ID );
			$totalOccupiedRooms += $reservation_instance->calculateReservedRooms();
			// Increment the total number of available rooms
			$totalAvailableRooms += \AtollMatrix\Rooms::getMaxQuantityForRoom( $room->ID, $currentdateString);
	
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
