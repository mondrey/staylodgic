<?php
namespace AtollMatrix;

class Modals
{
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
		<div class="modal fade" id="quantity-popup" tabindex="-1" aria-labelledby="quantity-popup-label" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
						<h5 class="modal-title" id="quantity-popup-label">Popup Title</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="quantity-popup-input">Quantity:</label>
							<input type="number" class="form-control" name="quantity" placeholder="Quantity" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker">Date:</label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="Select date">
						</div>
						<div class="form-group">
							<label for="room">Room:</label>
							<select class="form-select" name="room">
								<?php
$featured_pages         = get_posts('post_type=atmx_room&orderby=title&numberposts=-1&order=ASC');
        $list_options[ 'none' ] = "Not Selected";
        if ($featured_pages) {
            foreach ($featured_pages as $key => $list) {
                $list_options[ $list->ID ] = $list->post_title;
            }
        } else {
            $list_options[ 0 ] = "Rooms not found.";
        }
        foreach ($list_options as $key => $option) {
            echo '<option value="' . esc_attr($key) . '">', esc_attr($option), '</option>';
        }
        ?>
						</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary save-changes">Save changes</button>
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
		<div class="modal fade" id="rates-popup" tabindex="-1" aria-labelledby="rates-popup-label" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
						<h5 class="modal-title" id="rates-popup-label">Popup Title</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="rates-popup-input">Rate:</label>
							<input type="number" class="form-control" name="rate" placeholder="Rate" value="0" min="0">
						</div>
						<div class="form-group">
							<label for="modaldatepicker">Date:</label>
							<input type="text" class="form-control modaldatepicker" name="modaldatepicker" placeholder="Select date">
						</div>
						<div class="form-group">
							<label for="room">Room:</label>
							<select class="form-select" name="room">
								<?php
$featured_pages         = get_posts('post_type=atmx_room&orderby=title&numberposts=-1&order=ASC');
        $list_options[ 'none' ] = "Not Selected";
        if ($featured_pages) {
            foreach ($featured_pages as $key => $list) {
                $list_options[ $list->ID ] = $list->post_title;
            }
        } else {
            $list_options[ 0 ] = "Rooms not found.";
        }
        foreach ($list_options as $key => $option) {
            echo '<option value="' . esc_attr($key) . '">', esc_attr($option), '</option>';
        }
        ?>
						</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary save-changes">Save changes</button>
					</div>
				</div>
			</div>
		</div>
		<?php
}
}
?>