jQuery(document).ready(function($) {
	var portfolioList = jQuery('#portfolio-list');
	console.log( portfolioList );
	portfolioList.sortable({
		update: function(event, ui) {
			jQuery('#loading-animation').show(); // Show the animate loading gif while waiting
			console.log('Is here');
			opts = {
				url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
				type: 'POST',
				async: true,
				cache: false,
				dataType: 'json',
				data:{
					action: 'portfolio_sort', // Tell WordPress how to handle this ajax request
					order: portfolioList.sortable('toArray').toString() // Passes ID's of list items in	1,3,2 format
				},
				success: function(response) {
					jQuery('#loading-animation').hide(); // Hide the loading animation
					return; 
				},
				error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
					//alert('There was an error saving the updates '+textStatus+" "+e);
					jQuery('#loading-animation').hide(); // Hide the loading animation
					return; 
				}
			};
			jQuery.ajax(opts);
		}
	});
});