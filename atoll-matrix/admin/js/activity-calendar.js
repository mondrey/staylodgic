(function ($) {
	$(document).ready(function () {

		const flatpickrInstance = flatpickr(".activity-reservation", {
			showMonths: 1,
			dateFormat: "Y-m-d",
			enableTime: false,
		});

		function getActivityGuestNumbers() {
			var totalPeople;
			var activityAdults = $('#atollmatrix_reservation_activity_adults').val();
			var activityChildren = $('#atollmatrix_reservation_activity_children').val();

			totalPeople = parseInt(activityAdults) + parseInt(activityChildren);

			return totalPeople;
		}
		function activityCurrencyKeyIn() {

			$('[data-priceof="activityperperson"]').on('input', function(e) {
				var activityPerPerson = $(this).val();
				
				totalPeople = getActivityGuestNumbers();
				console.log( totalPeople );
				var totalRate = totalPeople * activityPerPerson;
				$('[data-priceof="activitysubtotal"]').val( totalRate.toFixed(2) );
				$('[data-priceof="activitytotal"]').val('');
				$('.input-tax-summary-wrap-inner').remove();
			});
			$('[data-priceof="activitysubtotal"]').on('input', function(e) {
				var totalRate = $(this).val();

				totalPeople = getActivityGuestNumbers();

				var activityPerPerson = totalRate / totalPeople;
				$('[data-priceof="activityperperson"]').val( activityPerPerson.toFixed(2) );
				$('[data-priceof="activitytotal"]').val('');
				$('.input-tax-summary-wrap-inner').remove();
	
			});

		}
		activityCurrencyKeyIn();

		$('#activity-tax-generate').on('click', function(e) {
			// Get the selected booking number
			var subtotal_for_tax = $('[data-priceof="activitysubtotal"]').val();
			var totalStayNights = 1;
			var adults = $('#atollmatrix_reservation_activity_adults').val();
			var children = $('#atollmatrix_reservation_activity_children').val();
	
			if ( '' == children ) {
				children = 0;
			}
	
			var totalGuests = parseInt( adults ) + parseInt( children );
			console.log('Total guests ' + totalGuests);
			var postID = $('input[name="post_ID"]').val();
		
			// Make an Ajax request to fetch the room names
			$.ajax({
			  url: ajaxurl, // WordPress Ajax URL
			  type: 'POST',
			  data: {
				action: 'generateTax', // Custom Ajax action
				post_id: postID,
				nonce: atollmatrix_admin_vars.nonce,
				subtotal: subtotal_for_tax,
				staynights: totalStayNights,
				total_guests: totalGuests,
				tax_type: 'activities'
			  },
			  success: function(response) {
				console.log( response );
				// Handle the Ajax response
				// Display the room names in the desired element
				$('#input-tax-summary').html(response.html);
				$('#atollmatrix_reservation_total_room_cost').val( response.total.toFixed(2) );
			  },
			  error: function(xhr, status, error) {
				// Handle any errors that occur during the Ajax request
				console.log(xhr.responseText);
			  }
			});
		});

		$('#activity-tax-exclude').on('click', function(e) {
			// Get the selected booking number
			var subtotal_for_tax= $('[data-priceof="activitysubtotal"]').val();
			var postID = $('input[name="post_ID"]').val();
		
			// Make an Ajax request to fetch the room names
			$.ajax({
			  url: ajaxurl, // WordPress Ajax URL
			  type: 'POST',
			  data: {
				action: 'excludeTax', // Custom Ajax action
				post_id: postID,
				subtotal: subtotal_for_tax,
				nonce: atollmatrix_admin_vars.nonce
			  },
			  success: function(response) {
				console.log( response );
				// Handle the Ajax response
				// Display the room names in the desired element
				$('#atollmatrix_reservation_total_room_cost').val(subtotal_for_tax);
				$('.input-tax-summary-wrap-inner').remove();
				$('#input-tax-summary').html('<div class="input-tax-summary-wrap-inner">' + response + '</div>');
			  },
			  error: function(xhr, status, error) {
				// Handle any errors that occur during the Ajax request
				console.log(xhr.responseText);
			  }
			});
		});

	});
})(jQuery);
