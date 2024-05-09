jQuery(document).ready(function ($) {
	"use strict";

    $('#start-tour-button').click(function() {
        startDriverTour(); // Assuming this function initializes and starts your Driver.js tour

		console.log('skdjfhkdsjfh');
    });

	function startDriverTour() {
		const driver = window.driver.js.driver;
		const driverObj = driver({
			showProgress: true,
			steps: [
			  { element: 'a[href="admin.php?page=slgc-settings-panel"]', popover: { title: 'Hotel Settings', description: 'Step: 1: Configure your hotel from this page. This should be the first step.', side: "right", align: 'start' }},
			  { element: '#menu-posts-slgc_room', popover: { title: 'Create Rooms', description: 'Step 2: Create atleast one room and attach a featured image to it. You can add more images to display a gallery for the room.', side: "right", align: 'start' }},
			  { element: '#toplevel_page_slgc-dashboard', popover: { title: 'Availability Calendar', description: 'Step 3: Open Availability Calendar and set Room Quantity and Rates for required months to open them.', side: "right", align: 'start' }},
			  { element: '#menu-posts-slgc_reservations', popover: { title: 'Create Reservations', description: 'Step 4: That\'s it. Now you can create reservations.', side: "right", align: 'start' }},
			  { popover: { title: 'Accept Reservations', description: 'And that is all, Just 4 steps to start accepting reservations.' } }
			]
		  });
		  
		  driverObj.drive();
	}
	
});
