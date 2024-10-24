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
				{ element: 'a[href="admin.php?page=slgc-settings-panel"]', popover: { title: 'Step: 1: Hotel Settings', description: 'Configure your hotel from this page. This should be the first step.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_room', popover: { title: 'Step 2: Create Rooms', description: 'Create atleast one room and attach a featured image to it. You can add more images to display a gallery for the room.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_slgc-dashboard', popover: { title: 'Step 3: Availability Calendar', description: 'The Overview section has several pages. Open the Availability Calendar page and set Room Quantity and Rates for required months to open them.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_reservations', popover: { title: 'Final Step: Create Reservations', description: 'That\'s it. Now you can create reservations.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_slgc-dashboard', popover: { title: 'Bookings Overview', description: 'Display overview of daily and upcoming bookings.', side: "right", align: 'start' } },
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
				{ element: 'a[href="admin.php?page=slgc-settings-panel"]', popover: { title: 'Step: 1: Hotel Settings', description: 'Configure your hotel.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_activity', popover: { title: 'Step: 2: Create Activities', description: 'Create acitivities and assign daily timings for schedule. Attach a featured image and add more to form a gallery.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_activityres', popover: { title: 'Step 3: Create Activity Reservation', description: 'Create activity reservations.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_activityres', popover: { title: 'Activity Ticket', description: 'The acitvitiy ticket will be generated once you save an acitivity reservation.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_slgc-dashboard', popover: { title: 'Activity Overview', description: 'Display overview of daily and upcoming activity reservations.', side: "right", align: 'start' } },
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
				{ element: '#menu-posts-slgc_reservations', popover: { title: 'Step: 1: Create Registration', description: 'Edit the booking reservation and create registration under the customer. Next Edit the Registration.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_guestregistry', popover: { title: 'Step 2: Registration Link', description: 'When you edit a registration from the booking, it gives you the option to generate a QR Code. You can send the QR Code to the guest for online registration.', side: "right", align: 'start' } },
				{ element: '#menu-posts-slgc_guestregistry', popover: { title: 'Step 3: View Registration', description: 'Registrations will be display when you edit the registration directly or you can click Registration from the booking as well.', side: "right", align: 'start' } },
				{ element: '#toplevel_page_staylodgic-settings', popover: { title: 'Step 4: Export Registration', description: 'You can export a CSV of all guest registration for a chosen month.', side: "right", align: 'start' } },
			]
		});

		driverObj.drive();
	}

});
