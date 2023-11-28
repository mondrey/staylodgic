(function ($) {
	$(document).ready(function () {

		class GuestInput {
			constructor(element) {
				this.element = element;
				this.input = this.element.find('.number-value');
				this.setupEvents();
				this.calculateSum();
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
					this.calculateSum();
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
				this.calculateSum();
				if (guest === "child") {
					var child_age_input = this.input.attr('data-childageinput');
					$('.front-booking-adult-child-value').text( value + 1 );
					var extraInput = $("<input name='"+child_age_input+"' type='text' data-counter='" + value + "' placeholder='Age'>");
					$("#guest-age").append(extraInput);
				} else {
					$('.front-booking-adult-adult-value').text( value + 1 );
				}
			}
		
			calculateSum() {
				var sum = 0;
				$('.number-input .number-value').each(function () {
					sum += parseInt($(this).val());
				});
				console.log('Total sum:', sum);
				return sum;
			}
		}

		// Apply the class to all elements with the '.number-input' class
		$('.number-input').each(function () {
			new GuestInput($(this));
		});

	});
})(jQuery);
