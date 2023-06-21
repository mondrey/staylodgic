<?php
namespace Cognitive;
class AvailablityCalendar extends AvailablityCalendarBase {

	public function __construct($startDate = null, $endDate = null) {
		parent::__construct($startDate, $endDate);
	}

	private function displayOccupancy_TableDataBlock() {
		ob_start();
		?>
		<td class="calendarCell rowHeader">
			<div class="occupancyStats-wrap">
				<div class="occupancyStats-inner">
					<div class="occupancy-total">
						Occupancy
					<span class="occupancy-total-stats">
					<?php
					echo $this->calculateOccupancyTotalForRange( $this->startDate, $this->endDate );
					?><span>%</span>
					</span>
					</div>
				</div>
			</div>
		</td>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	public function todayCSSTag( $occupancydate ) {
		$today_status_class = '';
		if ( $occupancydate == $this->today ) {
			$today_status_class = "is-today";
		}
		return $today_status_class;
	}

	private function displayAdrOccupancyRange_TableDataBlock( $dates ) {
		$number_of_columns = 0;
		foreach ($dates as $date) :
			$number_of_columns++;
			$occupancydate = $date->format('Y-m-d');
			ob_start();
			?>
				<td class="calendarCell monthHeader occupancy-stats <?php echo $this->todayCSSTag( $occupancydate ); ?>">
					<div class="occupancyStats-wrap">
						<div class="occupancyStats-inner">
							<div class="occupancy-adr">ADR: <?php echo $this->calculateAdrForDate( $occupancydate ); ?></div>
							<div class="occupancy-percentage"><?php echo $this->calculateOccupancyForDate( $occupancydate ); ?><span>%</span></div>
						</div>
					</div>
				</td>
			<?php
		endforeach;
		$output = ob_get_clean();
		return $output;
	}

	private function displayDate_TableDataBlock( $dates ) {
		$today = $this->today;
		$number_of_columns = 0;
		$markNumDays = $this->numDays + 1;
		foreach ($dates as $date) :
			$number_of_columns++;
			$month = $date->format('F');
			$column_class = '';
			if ( $number_of_columns < $markNumDays ) {
				$column_class = "rangeSelected";
			}
			$occupancydate = $date->format('Y-m-d');
			if ( $occupancydate == $today ) {
				$month = 'Today';
			}
			ob_start();
			?>
			<td class="calendarCell monthHeader <?php echo $this->todayCSSTag( $occupancydate ); ?> <?php echo $column_class; ?>">
				<div class="monthDayinfo-wrap">
					<div class="month"><?php echo $month; ?></div>
					<div class="day-letter"><?php echo $date->format('D'); ?></div>
					<div class="day"><?php echo $date->format('j'); ?></div>
				</div>
			</td>
			<?php
		endforeach;
		$output = ob_get_clean();
		return $output;
	}

	private function ReservedTab( $reservation_data, $checkout_list, $current_day, $calendar_start ) {
		$display = false;
		$tab = array();
		$row = 0;
		$room = 1;
		foreach ($reservation_data as $reservation) {
			$start_date_display = '';
			$guest_name = '';
			$reservatoin_id = $reservation['id'];
			$booking_number = cognitive_get_booking_number($reservation['id']);
			$guest_name = cognitive_get_reservation_guest_name($reservation['id']);
			$reserved_days = cognitive_count_reservation_days( $reservation['id'] );
			$checkin = cognitive_get_checkin_date( $reservation['id'] );
			$checkout = cognitive_get_checkout_date( $reservation['id'] );
			$reservation_status = cognitive_get_reservation_status( $reservation['id'] );
			$row++;
	
			if ( !array_key_exists($reservatoin_id, $checkout_list) ) {
	
				$newCheckin = $checkin; // Checkin date of the new value to be added
				$hasConflict = false; // Flag to track if there is a conflict
				// Iterate through the existing array
				foreach ($checkout_list as $value) {
					$checkoutDate = $value['checkout'];
				
					// Compare the new checkin date with existing checkout dates
					if ($newCheckin <= $checkoutDate) {
						$hasConflict = true;
						break; // Stop iterating if a conflict is found
					}
				}
	
				$givenCheckinDate = $checkin;
				// Filter the array based on the check-in date and existing checkout dates
				$filteredArray = array_filter($checkout_list, function($value) use ($givenCheckinDate) {
					return $value['checkout'] > $givenCheckinDate;
				});
				// Extract the room numbers from the filtered array
				$roomNumbers = array_column($filteredArray, 'room');
	
				// Check for missing room numbers
				$missingNumber = false;
				sort($roomNumbers);
	
				if (!empty($roomNumbers)) {
					for ($i = 1; $i <= max($roomNumbers); $i++) {
						if (!in_array($i, $roomNumbers)) {
							$missingNumber = $i;
							break;
						}
					}
				}
	
				// Output the result
				if ($missingNumber) {
					$room = $missingNumber;
				} else {
					$givenDate = $checkin;
					$recordCount = 0;
	
					foreach ($checkout_list as $value) {
						$checkoutDate = $value['checkout'];
					
						if ($checkoutDate > $givenDate) {
							$recordCount++;
						}
					}
					
					if ($hasConflict) {
						//The new checkin date falls within existing checkout dates.";
						$room = $recordCount + 1;
					} else {
						//The new checkin date is outside existing checkout dates.";
						$room = $recordCount - 1;
					}
				}
	
	
				if (empty($checkout_list)) {
					$room = 1;
				}
	
				$checkout_list[$reservatoin_id]['room']=$room;
				$checkout_list[$reservatoin_id]['checkin']=$checkin;
				$checkout_list[$reservatoin_id]['checkout']=$checkout;
			}
	
			if ( array_key_exists($reservatoin_id, $checkout_list) ) {
				$room = $checkout_list[$reservatoin_id]['room'];
			}
	
			$reservation_edit_link = get_edit_post_link($reservation['id']);
			$display_info = $guest_name . '<span>Booking.com</span>';
			if ( $reservation['start'] <> 'no' ) {
				$start_date = new \DateTime();
				$start_date->setTimestamp($reservation['checkin']);
				$start_date_display = $start_date->format('M j, Y');
				$width = ( 80 * ( $reserved_days ) ) - 3;
				$tab[$room] = '<a class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reservation-edit-link" href="' . $reservation_edit_link . '"><div class="reserved-tab-wrap reserved-tab-with-info reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-guest="'.$guest_name.'" data-room="'.$room.'" data-row="'.$row.'" data-bookingnumber="'.$booking_number.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab reserved-tab-days-'.$reserved_days.'"><div data-tabwidth="'.$width.'" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">'.$display_info.'</div></div></div></div></a>';
				$display = true;
			} else {
				if ( $current_day <> $checkout ) {
					// Get the checkin day for this as it's in the past of start of the availblablity calendar.
					// So this tab is happening from the past and needs to be labled athough an extention.
					$check_in_date_past = new \DateTime();
					$check_in_date_past->setTimestamp($reservation['checkin']);
					$check_in_date_past = $check_in_date_past->format('Y-m-d');
					$daysBetween = cognitive_countDaysBetweenDates($check_in_date_past, $current_day);
					$width = ( 80 * ( $reserved_days - $daysBetween ) ) - 3;
					if ( $check_in_date_past < $calendar_start && $calendar_start == $current_day ) {
						$tab[$room] = '<a class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reservation-edit-link" href="' . $reservation_edit_link . '"><div class="reserved-tab-wrap reserved-tab-with-info reserved-from-past reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-guest="'.$guest_name.'" data-room="'.$room.'" data-row="'.$row.'" data-bookingnumber="'.$booking_number.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab reserved-tab-days-'.$reserved_days.'"><div data-tabwidth="'.$width.'" class="reserved-tab-inner"><div class="ota-sign"></div><div class="guest-name">'.$display_info.'</div></div></div></div></a>';
					} else {
						$tab[$room] = '<div class="reservation-tab-is-'.$reservation_status.' reservation-tab-id-'.$reservatoin_id.' reserved-tab-wrap reserved-extended reservation-'.$reservation_status.'" data-reservationstatus="'.$reservation_status.'" data-room="'.$room.'" data-row="'.$row.'" data-reservationid="'.$reservation['id'].'" data-checkin="'.$checkin.'" data-checkout="'.$checkout.'"><div class="reserved-tab"></div></div>';
					}
					$display = true;
				}
			}
	
		}
		
		krsort($tab);
		$tab_array = array();
		$htmltab = '';
	
		if ($display) {
	
			foreach ($tab as $key => $value) {
				$htmltab .= $value;
			}
	
		}
		$tab_array['tab']=$htmltab;
		$tab_array['checkout']=$checkout_list;
	
		return $tab_array;
	}
	

	public function getAvailabilityCalendar() {

		$dates = $this->getDates();
		$today = $this->today;
		
		ob_start();
		?>
		<table id="calendarTable" data-calstart="<?php echo $this->startDate; ?>" data-calend="<?php echo $this->endDate; ?>">
			<tr class="calendarRow">
				<?php
				echo $this->displayOccupancy_TableDataBlock();
				echo $this->displayAdrOccupancyRange_TableDataBlock( $dates );
				?>
			</tr>
			<tr class="calendarRow">
				<td class="calendarCell rowHeader"></td>
				<?php
				echo $this->displayDate_TableDataBlock( $dates );
				?>
			</tr>
			<?php 
			foreach ( $this->roomlist as $roomId => $roomName ) :
				$checkout_list = array();
				?>
				<tr class="calendarRow calendar-room-row" data-id="<?php echo $roomId; ?>">
					<td class="calendarCell rowHeader"><?php echo $roomName; ?></td>
					<?php foreach ($dates as $date) : ?>
							<?php
							$dateString = $date->format('Y-m-d');
							$reservation_data = array();
							$reservation_data = cognitive_is_date_reserved($dateString, $roomId);
							$remaining_rooms = cognitive_remaining_rooms_for_day($roomId, $dateString);
							$reserved_room_count = cognitive_count_reservations_for_day($roomId, $dateString);
							$max_room_count = cognitive_get_max_quantity_for_room($roomId, $dateString);
							$reserved_rooms = cognitive_calculate_reserved_rooms($dateString,$roomId);
							$room_rate = \Cognitive\Rates::getRoomRateByDate( $roomId, $dateString );
							$occupancy_status_class = "";
							if ( cognitive_is_room_for_day_fullybooked($roomId, $dateString) ) {
								$occupancy_status_class = "fully-booked";
							}
							$today_status_class = '';
							if ( $dateString == $today ) {
								$today_status_class = "is-today";
							}
							echo '<td class="calendarCell '.$today_status_class.' '.$occupancy_status_class.'">';
							?>
							<div class="calendar-info-wrap">
							<div class="calendar-info">
							<a href="#" class="quantity-link" data-remaining="<?php echo $remaining_rooms; ?>" data-reserved="<?php echo $reserved_rooms; ?>" data-date="<?php echo $dateString; ?>" data-room="<?php echo $roomId; ?>"><?php echo $remaining_rooms; ?></a>
							<?php
							if (!empty($room_rate) && isset($room_rate) && $room_rate > 0) {
								echo '<a class="roomrate-link" href="#">'.$room_rate.'</a>';
							}
							?>
							</div>
							</div>
							<div class="reservation-tab-wrap" data-day="<?php echo $dateString; ?>">
							<?php
							if ( $reservation_data ) {
								$reservation_module = array();
								//echo cognitive_generate_reserved_tab( $reservation_data, $checkout_list );
								$reservation_module = $this->ReservedTab( $reservation_data, $checkout_list, $dateString, $this->startDate );
								echo $reservation_module['tab'];
								$checkout_list = $reservation_module['checkout'];
								//print_r( $checkout_list );
							}
							?>
							</div>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php
		$output = ob_get_clean();
		return $output;
	}
}
