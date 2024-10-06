<?php

namespace Staylodgic;

class Modals {


	/**
	 * Method rate_qty_toasts
	 *
	 * @return void
	 */
	public static function rate_qty_toasts() {
		$toast  = '<div aria-live="polite" aria-atomic="true" class="availability-calendar-toasts position-relative">';
		$toast .= '<div class="toast-container position-fixed bottom-0 end-0 p-3">';

		$toast .= '<div id="rateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">' . __( 'Rate Updated', 'staylodgic' ) . '</strong>';
		$toast .= '<small class="text-muted toast-time">' . __( 'just now', 'staylodgic' ) . '</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="' . __( 'Close', 'staylodgic' ) . '"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= __( 'Rate updated successfully.', 'staylodgic' );
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '<div id="rateToastFail" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">' . __( 'Rate Failed', 'staylodgic' ) . '</strong>';
		$toast .= '<small class="text-muted toast-time">' . __( 'just now', 'staylodgic' ) . '</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="' . __( 'Close', 'staylodgic' ) . '"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= __( 'Rate update failed.', 'staylodgic' );
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '<div id="quantityToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">' . __( 'Quantity Updated', 'staylodgic' ) . '</strong>';
		$toast .= '<small class="text-muted toast-time">' . __( 'just now', 'staylodgic' ) . '</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="' . __( 'Close', 'staylodgic' ) . '"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= __( 'Quantity updated successfully.', 'staylodgic' );
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '<div id="quantityToastFail" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">' . __( 'Quantity Failed', 'staylodgic' ) . '</strong>';
		$toast .= '<small class="text-muted toast-time">' . __( 'just now', 'staylodgic' ) . '</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="' . __( 'Close', 'staylodgic' ) . '"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= __( 'Quantity update failed.', 'staylodgic' );
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '<div id="calendarToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">' . __( 'Calendar', 'staylodgic' ) . '</strong>';
		$toast .= '<small class="text-muted toast-time">' . __( 'just now', 'staylodgic' ) . '</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="' . __( 'Close', 'staylodgic' ) . '"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= __( 'Calendar loaded.', 'staylodgic' );
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '</div>';
		$toast .= '</div>';

		return $toast;
	}

	/**
	 * Method sync_booking_modal
	 *
	 * @return void
	 */
	public static function sync_booking_modal() {
		?>
		<!-- Modal -->
		<div class="modal fade" id="sync-booking-popup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="sync-booking-popup-label"><?php echo __( 'Import iCal Bookings', 'staylodgic' ); ?></h1>
					</div>
					<div class="modal-body">
						<div class="before-importing-content">
							<?php echo __( 'Importing can change status of existing bookings to cancelled if they are not found or deleted depending on your options settings. Please backup before proceeding.', 'staylodgic' ); ?>
						</div>
						<div class="progress">
							<div id="ical-sync-progress" class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
						</div>
						<p>
							<button type="button" class="process-ical-booking-sync btn btn-outline-primary">
								<span class="button-spinner-support spinner-border spinner-border-sm" aria-hidden="true"></span>
								<?php echo __( 'Import', 'staylodgic' ); ?>
							</button>
						</p>
						<p>
						<div id="result-notice"></div>
						</p>
						<p>
						<div id="result"></div>
						</p>
						<p>
						<div id="result-missing-bookings"></div>
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="ical-close-button btn btn-secondary" data-bs-dismiss="modal"><?php echo __( 'Close', 'staylodgic' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Method quanity_modal
	 *
	 * @return void
	 */
	public static function quanity_modal() {
		?>
		<!-- Bootstrap Modal -->
		<div class="modal fade" id="quantity-modal" tabindex="-1" aria-labelledby="quantity-modal-label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="quantity-modal-label"><?php _e( 'Quantity', 'staylodgic' ); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __( 'Close', 'staylodgic' ); ?>"></button>
					</div>
					<div class=" modal-body">
						<div class="form-group">
							<label for="quantity-modal-input"><i class="fa-solid fa-hashtag"></i> <?php _e( 'Quantity', 'staylodgic' ); ?></label>
							<input type="number" class="form-control" name="quantity" placeholder="<?php echo __( 'Quantity', 'staylodgic' ); ?>" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker"><i class="fa-solid fa-calendar"></i> <?php _e( 'Date:', 'staylodgic' ); ?></label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="<?php echo __( 'Select date', 'staylodgic' ); ?>">
						</div>
						<div class="form-group">
							<label for="room"><i class="fa-solid fa-bed"></i> <?php _e( 'Room:', 'staylodgic' ); ?></label>
							<select class="form-select" name="room">
								<?php
								$rooms                = get_posts( 'post_type=slgc_room&orderby=title&numberposts=-1&order=ASC' );
								$list_options['none'] = 'Not Selected';
								if ( $rooms ) {
									foreach ( $rooms as $key => $list ) {
										$list_options[ $list->ID ] = $list->post_title;
									}
								} else {
									$list_options[0] = __( 'Rooms not found.', 'staylodgic' );
								}
								foreach ( $list_options as $key => $option ) {
									echo '<option value="' . esc_attr( $key ) . '">', esc_attr( $option ), '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', 'staylodgic' ); ?></button>
						<button type="button" class="btn btn-primary save-changes">
							<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <?php _e( 'Save changes', 'staylodgic' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Method rates_modal
	 *
	 * @return void
	 */
	public static function rates_modal() {
		?>
		<!-- Bootstrap Modal -->
		<div class="modal fade" id="rates-modal" tabindex="-1" aria-labelledby="rates-modal-label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="rates-modal-label"><?php _e( 'Set Rate', 'staylodgic' ); ?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __( 'Close', 'staylodglic' ); ?>"></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="rates-modal-input"><i class="fas fa-dollar-sign"></i> <?php _e( 'Rate:', 'staylodgic' ); ?></label>
							<input type="number" class="form-control" name="rate" placeholder="<?php echo __( 'Rate', 'staylodglic' ); ?>" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker"><i class="fa-solid fa-calendar"></i> <?php _e( 'Date:', 'staylodgic' ); ?></label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="<?php echo __( 'Select date', 'staylodglic' ); ?>">
						</div>
						<div class="form-group">
							<label for="room"><i class="fa-solid fa-bed"></i> <?php _e( 'Room:', 'staylodgic' ); ?></label>
							<select class="form-select" name="room">
								<?php
								$featured_pages       = get_posts( 'post_type=slgc_room&orderby=title&numberposts=-1&order=ASC' );
								$list_options['none'] = __( 'Not Selected', 'staylodgic' );
								if ( $featured_pages ) {
									foreach ( $featured_pages as $key => $list ) {
										$list_options[ $list->ID ] = $list->post_title;
									}
								} else {
									$list_options[0] = __( 'Rooms not found.', 'staylodgic' );
								}
								foreach ( $list_options as $key => $option ) {
									echo '<option value="' . esc_attr( $key ) . '">', esc_attr( $option ), '</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e( 'Close', 'staylodgic' ); ?></button>
						<button type="button" class="btn btn-primary save-changes">
							<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <?php _e( 'Save changes', 'staylodgic' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
?>