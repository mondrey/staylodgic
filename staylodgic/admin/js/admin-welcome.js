jQuery(document).ready(function ($) {
	"use strict";

    $('#start-tour-button').click(function() {
        startDriverTour();
    });

	function startDriverTour() {
		
		const driver = window.driver.js.driver;
		const driverObj = driver({
			showProgress: true,
			overlayColor: 'blue',
			steps: [
			  { element: 'a[href="admin.php?page=slgc-settings-panel"]', popover: { title: 'Step: 1: Hotel Settings', description: 'Configure your hotel from this page. This should be the first step.', side: "right", align: 'start' }},
			  { element: '#menu-posts-slgc_room', popover: { title: 'Step 2: Create Rooms', description: 'Create atleast one room and attach a featured image to it. You can add more images to display a gallery for the room.', side: "right", align: 'start' }},
			  { element: '#toplevel_page_slgc-dashboard', popover: { title: 'Step 3: Availability Calendar', description: 'The Overview section has several pages. Open the Availability Calendar page and set Room Quantity and Rates for required months to open them.', side: "right", align: 'start' }},
			  { element: '#menu-posts-slgc_reservations', popover: { title: 'Final Step: Create Reservations', description: 'That\'s it. Now you can create reservations.', side: "right", align: 'start' }}
			]
		  });
		  
		  driverObj.drive();
	}
	
});
