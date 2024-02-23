(function ($) {
	$(document).ready(function () {

		// Check if the element with the 'ticketqrcode' ID exists
		var qrcodeElement = document.getElementById('ticketqrcode');
		if (qrcodeElement) {
			var qrcodeValue = qrcodeElement.getAttribute('data-qrcode');
			
			// Generate the QR Code
			new QRCode(qrcodeElement, {
				text: qrcodeValue,
				width: 128,
				height: 128,
				colorDark : "#000000",
				colorLight : "#ececec",
				correctLevel : QRCode.CorrectLevel.H
			});
		}

        // Debounce function
        function debounce(func, wait) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

		class GuestInput {
			constructor(element) {
				this.element = element;
				this.input = this.element.find('.number-value');
				this.setupEvents();
				this.calculateSum('none');

				this.debouncedUpdateActivityPrices = debounce(this.updateActivityPrices.bind(this), 500);
			}
		
			setupEvents() {
				this.element.on('click', '.minus-btn', () => this.decrement());
				this.element.on('click', '.plus-btn', () => this.increment());
			}
		
			decrement() {
				var guest = this.input.attr('data-guest');
				var value = parseInt(this.input.val());
				var minValue = 1;
				if (guest === "child") {
					minValue = 0;
				}
				if (value > minValue) {
					this.input.val(value - 1);
					this.calculateSum('minus');
					if (guest === "child") {
						$('.front-booking-adult-child-value').text( value - 1 );
						$("#guest-age input[data-counter='" + (value - 1) + "']").remove();
					} else {
						$('.front-booking-adult-adult-value').text( value - 1 );
					}
				}
			}
		
			increment() {
				var guest = this.input.attr('data-guest');
				var value = parseInt(this.input.val());
				if (isNaN(value)) {
					value = 0;
				}
				$('.child-number-notice').hide();
				this.input.val(value + 1);
				this.calculateSum('plus');
				if (guest === "child") {
					var child_age_input = this.input.attr('data-childageinput');
					$('.front-booking-adult-child-value').text( value + 1 );
					var extraInput = $("<input name='"+child_age_input+"' type='text' data-counter='" + value + "' placeholder='Age'>");
					$("#guest-age").append(extraInput);
				} else {
					$('.front-booking-adult-adult-value').text( value + 1 );
				}
			}

			updateActivityPrices( totalPeople ) {
				var activityPerPerson = $('[data-priceof="activityperperson"]').val();
				
				console.log( totalPeople );
				var totalRate = totalPeople * activityPerPerson;
				
				if ($('#atollmatrix_reservation_checkin').length > 0) {

					var dateStr = $('#atollmatrix_reservation_checkin').val();
					var isValidDate = moment(dateStr, 'YYYY-MM-DD', true).isValid(); // Using moment.js for date validation
				
					$('[data-priceof="activitysubtotal"]').val( totalRate.toFixed(2) );
					$('[data-priceof="activitytotal"]').val('');
					$('.input-tax-summary-wrap-inner').remove();

					if (isValidDate) {
						$.ajax({
							url: ajaxurl, // 'ajaxurl' is a global variable defined by WordPress
							type: 'POST',
							data: {
								action: 'get_activity_schedules',
								selected_date: dateStr,
								the_post_id: atollmatrix_admin_vars.post_id,
								totalpeople: totalPeople
							},
							beforeSend: function( xhr ) {
								$('.activity-schedules-container-wrap').addClass('ajax-processing');
							},
							success: function(response) {
								if (response.success) {
									// Update the activity schedules container with the response data
									$('.activity-schedules-container-wrap').html(response.data);
								}
							},
							complete: function() {
								// Remove the class after the AJAX request is complete
								$('.activity-schedules-container-wrap').removeClass('ajax-processing');
							}
						});
					} else {
						console.log('Invalid date');
					}


				}
			}
		
			calculateSum( process ) {
				var sum = 0;
				$('.number-input .number-value').each(function () {
					sum += parseInt($(this).val());
				});
				console.log('Total sum:', sum);
				if ( process === 'plus' || process === 'minus' )
				if ($('[data-priceof="activityperperson"]').length > 0) {
					this.debouncedUpdateActivityPrices(sum);
				}
				return sum;
			}
		}

		// Apply the class to all elements with the '.number-input' class
		$('.number-input').each(function () {
			new GuestInput($(this));
		});

	});
})(jQuery);
