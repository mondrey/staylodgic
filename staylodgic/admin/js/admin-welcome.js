jQuery(document).ready(function ($) {
	"use strict";

	$('.view-all-features').click(function () {
		$('html, body').animate({
			scrollTop: $('.admin-page-two-main').offset().top
		}, 1000); // Adjust the duration (1000ms) as needed
	});

	$('#start-bookings-button').click(function () {
		startBookingsTour();
	});
	$('#start-activities-button').click(function () {
		startActivitiesTour();
	});
	$('#start-registration-button').click(function () {
		startRegistrationTour();
	});

	function startBookingsTour() {

		const driver = window.driver.js.driver;
		const driverObj = driver({
			showProgress: true,
			overlayColor: 'blue',
			steps: [
				{ element: 'a[href="admin.php?page=staylodgic-slg-settings-panel"]', popover: { title: 'Step: 1: Hotel Settings', description: 'Configure your hotel from this page. This should be the first step.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_rooms', popover: { title: 'Step 2: Create Rooms', description: 'Create atleast one room and attach a featured image to it. You can add more images to display a gallery for the room.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_staylodgic-slg-dashboard', popover: { title: 'Step 3: Availability Calendar', description: 'The Overview section has several pages. Open the Availability Calendar page and set Room Quantity and Rates for required months to open them.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_bookings', popover: { title: 'Final Step: Create Reservations', description: 'That\'s it. Now you can create reservations.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_staylodgic-slg-dashboard', popover: { title: 'Bookings Overview', description: 'Display overview of daily and upcoming bookings.', side: "right", align: 'start' } },
			]
		});

		driverObj.drive();
	}

	function startActivitiesTour() {

		const driver = window.driver.js.driver;
		const driverObj = driver({
			showProgress: true,
			overlayColor: 'blue',
			steps: [
				{ element: 'a[href="admin.php?page=staylodgic-slg-settings-panel"]', popover: { title: 'Step: 1: Hotel Settings', description: 'Configure your hotel.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_actvties', popover: { title: 'Step: 2: Create Activities', description: 'Create acitivities and assign daily timings for schedule. Attach a featured image and add more to form a gallery.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_actvtres', popover: { title: 'Step 3: Create Activity Reservation', description: 'Create activity reservations.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_actvtres', popover: { title: 'Activity Ticket', description: 'The acitvitiy ticket will be generated once you save an acitivity reservation.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_staylodgic-slg-dashboard', popover: { title: 'Activity Overview', description: 'Display overview of daily and upcoming activity reservations.', side: "right", align: 'start' } },
			]
		});

		driverObj.drive();
	}

	function startRegistrationTour() {

		const driver = window.driver.js.driver;
		const driverObj = driver({
			showProgress: true,
			overlayColor: 'blue',
			steps: [
				{ element: '#menu-posts-staylodgic_bookings', popover: { title: 'Step: 1: Create Registration', description: 'Edit the reservation and Create Registration under the customer. Next click Edit Registration button.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_guestrgs', popover: { title: 'Step 2: Registration Link', description: 'When you Edit a Registration, it gives you the option to Generate a QR Code. You can send the QR Code to the guest for online registration.', side: "right", align: 'start' } },
				{ element: '#menu-posts-staylodgic_guestrgs', popover: { title: 'Step 3: View Registration', description: 'Filled Registrations will be displayed inside the registration post.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_staylodgic-settings', popover: { title: 'Step 4: Export Registration', description: 'You can export a CSV of all guest registration for a chosen month.', side: "right", align: 'start' } },
			]
		});

		driverObj.drive();
	}

});
