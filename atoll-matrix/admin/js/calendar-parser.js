(function ($) {
	$(document).ready(function () {
		var processedEvents = []; // Array to store all processed events
		var tbody; // Variable to store the reference to the tbody element

		$('#process-events').on('click', function(e) {
			e.preventDefault();

			// Function to process and send events in batches
			function processEventsBatch(events) {
				var batchSize = 5; // Number of events to process in each batch
				var eventsBatch = events.splice(0, batchSize); // Get the next batch of events

				// Make an AJAX request to insert the reservation posts
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: 'insert_events_batch', // This should match the action hook in your functions.php file
						processedEvents: eventsBatch // Pass the processed events batch to the server
					},
					success: function(response) {
						if (response.success) {
							var successCount = response.data.successCount;
							console.log(eventsBatch );
							// Display the successfully inserted reservation posts
							$.each(eventsBatch, function(index, event) {
								if (index < successCount) {
									var row = $('<tr>');
									row.append('<td>' + event.CREATED + '</td>');
									row.append('<td>' + event.DTEND + '</td>');
									row.append('<td>' + event.DTSTART + '</td>');
									row.append('<td>' + event.SUMMARY + '</td>');
									row.append('<td>' + event.CHECKIN + '</td>');
									row.append('<td>' + event.CHECKOUT + '</td>');
									row.append('<td>' + event.UID + '</td>');
									tbody.append(row);
								}
							});

							$('#result').append('<p>' + successCount + ' reservation posts inserted successfully.</p>');
						} else {
							$('#result').append('<p>Error inserting reservation posts.</p>');
						}

						// Check if there are more events to process
						if (events.length > 0) {
							// Process the next batch of events after a short delay (e.g., 1 second)
							setTimeout(function() {
								processEventsBatch(events);
							}, 1000);
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

			// Make an AJAX request to process the events
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action: 'process_event_batch' // This should match the action hook in your functions.php file
				},
				success: function(response) {
					if(response.success) {
						processedEvents = response.data.processed;

						// Create the table
						var table = $('<table>');
						var thead = $('<thead>');
						tbody = $('<tbody>'); // Assign the tbody reference to the global variable

						// Create table headers
						var headerRow = $('<tr>');
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
