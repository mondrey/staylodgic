<?php
namespace AtollMatrix;

class Modals
{

	public static function rateQtyToasts() {
		$toast = '<div aria-live="polite" aria-atomic="true" class="availability-calendar-toasts position-relative">';
		$toast .= '<div class="toast-container top-0 end-0 p-3">';
	
		$toast .= '<div id="rateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">Rate Update</strong>';
		$toast .= '<small class="text-muted toast-time">just now</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= 'Rate updated successfully.';
		$toast .= '</div>';
		$toast .= '</div>';
	
		$toast .= '<div id="quantityToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">Quantity Update</strong>';
		$toast .= '<small class="text-muted toast-time">just now</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= 'Quantity updated successfully.';
		$toast .= '</div>';
		$toast .= '</div>';

		$toast .= '<div id="calendarToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">';
		$toast .= '<div class="toast-header">';
		$toast .= '<div class="toast-square"></div>';
		$toast .= '<strong class="me-auto">Calendar</strong>';
		$toast .= '<small class="text-muted toast-time">just now</small>';
		$toast .= '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>';
		$toast .= '</div>';
		$toast .= '<div class="toast-body">';
		$toast .= 'Calendar loaded.';
		$toast .= '</div>';
		$toast .= '</div>';
	
		$toast .= '</div>';
		$toast .= '</div>';
	
		return $toast;
	}
	
    public static function syncBookingModal()
    {
        ?>
		<!-- Modal -->
		<div class="modal fade" id="sync-booking-popup" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="sync-booking-popup-label">Import iCal Bookings</h1>
			</div>
			<div class="modal-body">
			<div class="before-importing-content">
			Importing can change status of existing bookings to cancelled if they are not found or deleted depending on your options settings. Please backup before proceeding.
			</div>
			<div class="progress">
				<div id="ical-sync-progress" class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
			</div>
			<p>
			<button type="button" class="process-ical-booking-sync btn btn-outline-primary">
			<span class="button-spinner-support spinner-border spinner-border-sm" aria-hidden="true"></span>
			Import
			</button>
		</p>
		<p><div id="result-notice"></div></p>
		<p><div id="result"></div></p>
		<p><div id="result-missing-bookings"></div></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="ical-close-button btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
			</div>
		</div>
		</div>
		<?php
}

    public static function quanityModal()
    {
        ?>
		<!-- Bootstrap Modal -->
		<div class="modal fade" id="quantity-modal" tabindex="-1" aria-labelledby="quantity-modal-label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
						<h5 class="modal-title" id="quantity-modal-label"><?php _e('Quantity', 'atollmatrix');?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="quantity-modal-input"><i class="fa-solid fa-hashtag"></i> <?php _e('Quantity', 'atollmatrix');?></label>
							<input type="number" class="form-control" name="quantity" placeholder="Quantity" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker"><i class="fa-solid fa-calendar"></i> <?php _e('Date:', 'atollmatrix');?></label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="Select date">
						</div>
						<div class="form-group">
							<label for="room"><i class="fa-solid fa-bed"></i> <?php _e('Room:', 'atollmatrix');?></label>
							<select class="form-select" name="room">
								<?php
$featured_pages         = get_posts('post_type=atmx_room&orderby=title&numberposts=-1&order=ASC');
        $list_options[ 'none' ] = "Not Selected";
        if ($featured_pages) {
            foreach ($featured_pages as $key => $list) {
                $list_options[ $list->ID ] = $list->post_title;
            }
        } else {
            $list_options[ 0 ] = __('Rooms not found.','atollmatrix');
        }
        foreach ($list_options as $key => $option) {
            echo '<option value="' . esc_attr($key) . '">', esc_attr($option), '</option>';
        }
        ?>
						</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Close', 'atollmatrix');?></button>
						<button type="button" class="btn btn-primary save-changes">
						<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <?php _e('Save changes', 'atollmatrix');?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
}

    public static function ratesModal()
    {
        ?>
		<!-- Bootstrap Modal -->
		<div class="modal fade" id="rates-modal" tabindex="-1" aria-labelledby="rates-modal-label" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
						<h5 class="modal-title" id="rates-modal-label"><?php _e('Set Rate', 'atollmatrix');?></h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="rates-modal-input"><i class="fas fa-dollar-sign"></i> <?php _e('Rate:', 'atollmatrix');?></label>
							<input type="number" class="form-control" name="rate" placeholder="Rate" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker"><i class="fa-solid fa-calendar"></i> <?php _e('Date:', 'atollmatrix');?></label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="Select date">
						</div>
						<div class="form-group">
							<label for="room"><i class="fa-solid fa-bed"></i> <?php _e('Room:', 'atollmatrix');?></label>
							<select class="form-select" name="room">
								<?php
$featured_pages         = get_posts('post_type=atmx_room&orderby=title&numberposts=-1&order=ASC');
        $list_options[ 'none' ] = "Not Selected";
        if ($featured_pages) {
            foreach ($featured_pages as $key => $list) {
                $list_options[ $list->ID ] = $list->post_title;
            }
        } else {
            $list_options[ 0 ] = __('Rooms not found.','atollmatrix');
        }
        foreach ($list_options as $key => $option) {
            echo '<option value="' . esc_attr($key) . '">', esc_attr($option), '</option>';
        }
        ?>
						</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php _e('Close', 'atollmatrix');?></button>
						<button type="button" class="btn btn-primary save-changes">
							<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> <?php _e('Save changes', 'atollmatrix');?></button>
					</div>
				</div>
			</div>
		</div>
		<?php
}
}
?>