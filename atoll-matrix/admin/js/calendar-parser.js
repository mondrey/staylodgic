(function ($) {
	$(document).ready(function () {
		var fileSignature = [];
		var originalProcessedEvents = [];
		var processedEvents = []; // Array to store all processed events
		var tbody; // Variable to store the reference to the tbody element

		$('.room_ical_links_wrapper').on('click', '.sync_button', function(e) {
			e.preventDefault();

			$(".sync_button").prop("disabled", true);

			var roomID = $(this).data('room-id');
			var icsURL = $(this).data('ics-url');
			var icsID = $(this).data('ics-id');

			// Function to process and send events in batches
			function processEventsBatch(events) {
				var batchSize = 5; // Number of events to process in each batch
				var eventsBatch = events.splice(0, batchSize); // Get the next batch of events
				// console.log( 'The process: ' + originalProcessedEvents );
				// Make an AJAX request to insert the reservation posts
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'insert_events_batch', // This should match the action hook in your functions.php file
						room_id: roomID,
						ics_url: icsURL,
						ics_id: icsID,
						processedEvents: eventsBatch // Pass the processed events batch to the server
					},
					success: function(response) {
						if (response.success) {
							var successCount = response.data.successCount;
							var skippedCount = response.data.skippedCount;
							var skippedCount = response.data.icsID;

							// Display the successfully inserted reservation posts
							$.each(eventsBatch, function(index, event) {
								fileSignature = event.SIGNATURE;
								if (index < successCount) {
									var row = $('<tr>');
									row.append('<td>' + event.SIGNATURE + '</td>');
									row.append('<td>' + event.CREATED + '</td>');
									row.append('<td>' + event.DTEND + '</td>');
									row.append('<td>' + event.DTSTART + '</td>');
									row.append('<td>' + event.SUMMARY + '</td>');
									row.append('<td>' + event.CHECKIN + '</td>');
									row.append('<td>' + event.CHECKOUT + '</td>');
									row.append('<td>' + event.UID + '</td>');
									tbody.append(row);
								} else if (index < successCount + skippedCount) {
									// Display a message for skipped posts
									$('#result').append('<p>Post with booking number ' + event.UID + ' already exists. Skipped.</p>');
								}
							});

							$('#result').append('<p>' + successCount + ' reservation posts inserted successfully.</p>');

							// Check if there are more events to process
							if (events.length > 0) {
								// Process the next batch of events after a short delay (e.g., 1 second)
								setTimeout(function() {
									processEventsBatch(events);
								}, 1000);
							} else {
								console.log('processedEvents');
								console.log(fileSignature);
								// No more events to process, trigger AJAX call to find future cancelled reservations
								findFutureCancelledReservations(fileSignature);
							}
						} else {
							$('#result').append('<p>Error inserting reservation posts.</p>');
						}
					},
					error: function(xhr, status, error) {
						$('#result').append('<p>An error occurred while inserting reservation posts: ' + error + '</p>');

						// Check if there are more events to process
						if (events.length > 0) {
							// Process the next batch of events after a short delay (e.g., 1 second)
							setTimeout(function() {
								processEventsBatch(events);
							}, 1000);
						}
					}
				});
			}

			// Function to trigger AJAX call for finding future cancelled reservations
			function findFutureCancelledReservations( signature ) {
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'find_future_cancelled_reservations',
						processedEvents: originalProcessedEvents, // Convert to JSON string
						signature: signature, // Pass the signature in the AJAX request
						room_id: roomID,
						ics_id: icsID,
					},
					success: function(response) {


						if (response.success) {
						  var cancelledReservations = response.data.cancelledReservations;
						  if (cancelledReservations.length > 0) {
							// Display the list of future cancelled reservations
							var resultList = $('<ul>');
							$.each(cancelledReservations, function(index, bookingNumber) {
							  var listItem = $('<li>').text(bookingNumber);
							  resultList.append(listItem);
							});
							$('#result').append('<p>Future Cancelled Reservations:</p>').append(resultList);
						  } else {
							$('#result').append('<p>No future cancelled reservations found.</p>');
						  }

						  $("button.sync_button[data-ics-id='" + response.data.icsID + "']").text('Active');
						  $(".sync_button").prop("disabled", false);

						} else {
						  $('#result').append('<p>Error occurred while retrieving future cancelled reservations.</p>');
						}
					},
					error: function(xhr, status, error) {
						// Handle error
						$('#result').append('<p>Error occurred while retrieving future cancelled reservations.</p>');
					}
				});
			}


			// Make an AJAX request to process the events
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'process_event_batch', // This should match the action hook in your functions.php file
					room_id: roomID,
					ics_url: icsURL
				},
				success: function(response) {
					if(response.success) {
						originalProcessedEvents = JSON.parse(JSON.stringify(response.data.processed)); // Create a deep copy of the original processed events
						processedEvents = response.data.processed;
						var transientUsed = response.data.transient_used;
						
						// Create the table
						var table = $('<table>');
						var thead = $('<thead>');
						tbody = $('<tbody>'); // Assign the tbody reference to the global variable

						// Create table headers
						var headerRow = $('<tr>');
						headerRow.append('<th>SIGNATURE</th>');
						headerRow.append('<th>CREATED</th>');
						headerRow.append('<th>DTEND</th>');
						headerRow.append('<th>DTSTART</th>');
						headerRow.append('<th>SUMMARY</th>');
						headerRow.append('<th>CHECKIN</th>');
						headerRow.append('<th>CHECKOUT</th>');
						headerRow.append('<th>UID</th>');
						thead.append(headerRow);

						// Add the table to the page
						table.append(thead);
						table.append(tbody);
						$('#result').empty().append(table);

						$('#result').append('<p>Processing ' + processedEvents.length + ' events...</p>');

						// Display a message in the #result element
						if (transientUsed) {
							$('#result').append('<p>Events were processed using the existing transient.</p>');
						} else {
							$('#result').append('<p>Events were processed by parsing the ICS file and storing them in the transient.</p>');
						}

						// Start processing events in batches
						processEventsBatch(processedEvents);
					} else {
						$('#result').html('<p>Error processing events.</p>');
					}
				},
				error: function(xhr, status, error) {
					$('#result').html('<p>An error occurred while processing events: ' + error + '</p>');
				}
			});
		});

		$('.room_ical_links_wrapper .add_more_ical').click(function(){
			var group = '<div class="room_ical_link_group">';
			group += '<input type="url" name="room_ical_links_url[]">';
			group += '<input type="text" name="room_ical_links_comment[]">';
			group += '<button type="button" class="remove_ical_group">Remove</button>';
			group += '</div>';
		
			$(this).before(group);
		});

		$('.room_ical_links_wrapper').on('click', '.unlock_button', function() {
			var group = $(this).closest('.room_ical_link_group');
			var inputs = group.find('input');
		
			if (inputs.prop('readonly')) {
				inputs.prop('readonly', false);
				$(this).html('<i class="fas fa-unlock"></i>');
			} else {
				inputs.prop('readonly', true);
				$(this).html('<i class="fas fa-lock"></i>');
			}
		});
		  
		
		// Event delegation is used here to make sure dynamically added buttons also get this event
		$('.room_ical_links_wrapper').on('click', '.remove_ical_group', function(){
			$(this).parent('.room_ical_link_group').remove();
		});
		
		$('#save_all_ical_rooms').click(function(e){
			e.preventDefault();
			var room_ids = [];
			var room_links_id = [];
			var room_links_url = [];
			var room_links_comment = [];
		
			// Get the nonce value from the form
			var nonce = $('input[name="ical_form_nonce"]').val();
			
			$('.room_ical_links_wrapper').each(function(){
				var room_id = $(this).data('room-id');
				
				room_ids.push(room_id);
			
				var room_links_group = $(this).find('.room_ical_link_group').map(function(){
					var id = $(this).find('input[name="room_ical_links_id[]"]').val();
					var url = $(this).find('input[name="room_ical_links_url[]"]').val();
					var comment = $(this).find('input[name="room_ical_links_comment[]"]').val();
					return {
						id: id,
						url: url,
						comment: comment
					};
				}).get();
			
				// Filter out any URLs that are not valid.
				room_links_group = room_links_group.filter(function(link) {
					try {
						new URL(link.url);
						return true;
					} catch (_) {
						return false;  
					}
				});
			
				room_links_id.push(room_links_group.map(link => link.id));
				room_links_url.push(room_links_group.map(link => link.url));
				room_links_comment.push(room_links_group.map(link => link.comment));
			});			
		
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'save_ical_room_meta',
					room_ids: room_ids,
					room_ical_links_id: room_links_id,
					room_ical_links_url: room_links_url,
					room_ical_links_comment: room_links_comment,
					ical_form_nonce: nonce  // Send the nonce
				}
			}).done(function(response){
				if(response.success) {
					// The success message from server is in response.data
					// var successMessage = response.data;
					// Display the success message in your form. Here I'm just alerting it.
					location.reload();
				} else {
					// If for some reason success was false (like if wp_send_json_error() was called), you can handle that here
					alert("There was an error");
				}
			}).fail(function(jqXHR, textStatus, errorThrown){
				console.log("Request failed: " + textStatus);
				console.log("Error: " + errorThrown);
				console.log(jqXHR);
			});
		});		

	});
})(jQuery);

// (function ($) {
// 	$(document).ready(function () {
// 		var processedEvents = []; // Array to store all processed events

// 		$('#process-events').on('click', function(e) {
// 			e.preventDefault();

// 			// Function to process and send events in batches
// 			function processEventsBatch(events) {
// 				var batchSize = 5; // Number of events to process in each batch
// 				var eventsBatch = events.splice(0, batchSize); // Get the next batch of events

// 				// Make an AJAX request to insert the reservation posts
// 				$.ajax({
// 					type: 'POST',
// 					url: ajaxurl,
// 					data: {
// 						action: 'insert_events_batch', // This should match the action hook in your functions.php file
// 						processedEvents: eventsBatch // Pass the processed events batch to the server
// 					},
// 					success: function(response) {
// 						if (response.success) {
// 							var successCount = response.data.successCount;
// 							$('#result').append('<p>' + successCount + ' reservation posts inserted successfully.</p>');
// 						} else {
// 							$('#result').append('<p>Error inserting reservation posts.</p>');
// 						}

// 						// Check if there are more events to process
// 						if (events.length > 0) {
// 							// Process the next batch of events after a short delay (e.g., 1 second)
// 							setTimeout(function() {
// 								processEventsBatch(events);
// 							}, 1000);
// 						}
// 					},
// 					error: function(xhr, status, error) {
// 						$('#result').append('<p>An error occurred while inserting reservation posts: ' + error + '</p>');

// 						// Check if there are more events to process
// 						if (events.length > 0) {
// 							// Process the next batch of events after a short delay (e.g., 1 second)
// 							setTimeout(function() {
// 								processEventsBatch(events);
// 							}, 1000);
// 						}
// 					}
// 				});
// 			}

// 			// Make an AJAX request to process the events
// 			$.ajax({
// 				type: 'POST',
// 				url: ajaxurl,
// 				data: {
// 					action: 'process_event_batch' // This should match the action hook in your functions.php file
// 				},
// 				success: function(response) {
// 					if(response.success) {
// 						processedEvents = response.data.processed;

// 						// Create the table
// 						var table = $('<table>');
// 						var thead = $('<thead>');
// 						var tbody = $('<tbody>');

// 						// Create table headers
// 						var headerRow = $('<tr>');
// 						headerRow.append('<th>CREATED</th>');
// 						headerRow.append('<th>DTEND</th>');
// 						headerRow.append('<th>DTSTART</th>');
// 						headerRow.append('<th>SUMMARY</th>');
// 						headerRow.append('<th>CHECKIN</th>');
// 						headerRow.append('<th>CHECKOUT</th>');
// 						headerRow.append('<th>UID</th>');
// 						thead.append(headerRow);

// 						// Add the table to the page
// 						table.append(thead);
// 						table.append(tbody);
// 						$('#result').empty().append(table);

// 						// Start processing the events in batches
// 						processEventsBatch(processedEvents);
// 					} else {
// 						$('#result').html('<p>Error processing events.</p>');
// 					}
// 				},
// 				error: function(xhr, status, error) {
// 					$('#result').html('<p>An error occurred: ' + error + '</p>');
// 				}
// 			});
// 		});
// 	});
// })(jQuery);
