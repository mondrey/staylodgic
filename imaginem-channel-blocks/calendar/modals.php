<?php
function cognitive_quanity_modal() {
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
							$featured_pages = get_posts('post_type=room&orderby=title&numberposts=-1&order=ASC');
							$list_options['none'] = "Not Selected";
							if ($featured_pages) {
								foreach($featured_pages as $key => $list) {
									$list_options[$list->ID] = $list->post_title;
								}
							} else {
								$list_options[0]="Rooms not found.";
							}
							foreach ($list_options as $key => $option) {
								echo '<option value="'. esc_attr($key) .'">', esc_attr($option) , '</option>';
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
function cognitive_rates_modal() {
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
							$featured_pages = get_posts('post_type=room&orderby=title&numberposts=-1&order=ASC');
							$list_options['none'] = "Not Selected";
							if ($featured_pages) {
								foreach($featured_pages as $key => $list) {
									$list_options[$list->ID] = $list->post_title;
								}
							} else {
								$list_options[0]="Rooms not found.";
							}
							foreach ($list_options as $key => $option) {
								echo '<option value="'. esc_attr($key) .'">', esc_attr($option) , '</option>';
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
?>