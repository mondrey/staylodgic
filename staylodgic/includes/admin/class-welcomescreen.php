<?php

namespace Staylodgic;

class WelcomeScreen
{
   

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu')); // This now points to the add_admin_menu function
    }

    public function add_admin_menu()
    {

        add_menu_page(
            __('Staylodgic Admin', 'staylodgic'),             // Page title
            __('Staylodgic', 'staylodgic'),                   // Menu title
            'edit_posts',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page'), // Callback function
            'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTQ5NS45IDE2Ni42YzMuMiA4LjcgLjUgMTguNC02LjQgMjQuNmwtNDMuMyAzOS40YzEuMSA4LjMgMS43IDE2LjggMS43IDI1LjRzLS42IDE3LjEtMS43IDI1LjRsNDMuMyAzOS40YzYuOSA2LjIgOS42IDE1LjkgNi40IDI0LjZjLTQuNCAxMS45LTkuNyAyMy4zLTE1LjggMzQuM2wtNC43IDguMWMtNi42IDExLTE0IDIxLjQtMjIuMSAzMS4yYy01LjkgNy4yLTE1LjcgOS42LTI0LjUgNi44bC01NS43LTE3LjdjLTEzLjQgMTAuMy0yOC4yIDE4LjktNDQgMjUuNGwtMTIuNSA1Ny4xYy0yIDkuMS05IDE2LjMtMTguMiAxNy44Yy0xMy44IDIuMy0yOCAzLjUtNDIuNSAzLjVzLTI4LjctMS4yLTQyLjUtMy41Yy05LjItMS41LTE2LjItOC43LTE4LjItMTcuOGwtMTIuNS01Ny4xYy0xNS44LTYuNS0zMC42LTE1LjEtNDQtMjUuNEw4My4xIDQyNS45Yy04LjggMi44LTE4LjYgLjMtMjQuNS02LjhjLTguMS05LjgtMTUuNS0yMC4yLTIyLjEtMzEuMmwtNC43LTguMWMtNi4xLTExLTExLjQtMjIuNC0xNS44LTM0LjNjLTMuMi04LjctLjUtMTguNCA2LjQtMjQuNmw0My4zLTM5LjRDNjQuNiAyNzMuMSA2NCAyNjQuNiA2NCAyNTZzLjYtMTcuMSAxLjctMjUuNEwyMi40IDE5MS4yYy02LjktNi4yLTkuNi0xNS45LTYuNC0yNC42YzQuNC0xMS45IDkuNy0yMy4zIDE1LjgtMzQuM2w0LjctOC4xYzYuNi0xMSAxNC0yMS40IDIyLjEtMzEuMmM1LjktNy4yIDE1LjctOS42IDI0LjUtNi44bDU1LjcgMTcuN2MxMy40LTEwLjMgMjguMi0xOC45IDQ0LTI1LjRsMTIuNS01Ny4xYzItOS4xIDktMTYuMyAxOC4yLTE3LjhDMjI3LjMgMS4yIDI0MS41IDAgMjU2IDBzMjguNyAxLjIgNDIuNSAzLjVjOS4yIDEuNSAxNi4yIDguNyAxOC4yIDE3LjhsMTIuNSA1Ny4xYzE1LjggNi41IDMwLjYgMTUuMSA0NCAyNS40bDU1LjctMTcuN2M4LjgtMi44IDE4LjYtLjMgMjQuNSA2LjhjOC4xIDkuOCAxNS41IDIwLjIgMjIuMSAzMS4ybDQuNyA4LjFjNi4xIDExIDExLjQgMjIuNCAxNS44IDM0LjN6TTI1NiAzMzZhODAgODAgMCAxIDAgMC0xNjAgODAgODAgMCAxIDAgMCAxNjB6Ii8+PC9zdmc+',                             // Icon URL
            31                             // Position
        );

        // Add the first submenu page. Often this duplicates the main menu page.
        add_submenu_page(
            'staylodgic-settings',          // Parent slug
            __('Main', 'staylodgic'),                    // Page title
            __('Main', 'staylodgic'),                    // Menu title
            'edit_posts',               // Capability
            'staylodgic-settings',          // Menu slug
            array($this, 'display_main_page') // Callback function
        );
    }

    public function display_main_page()
    {
        // The HTML content of the 'Staylodgic' page goes here

echo '<div class="admin-container">';
echo '<div class="admin-column admin-column1">';
echo '<div class="section-main">';
echo '<div class="admin-page-header">';
echo '<h2>Hotel Management</h2>';
echo '<h1>Staylodgic<span class="the-dot">.</span></h1>';
echo '</div>';
echo '<ul class="admin-horizontal-list">';
echo '<li><i class="fas fa-plus-square"></i> New Reservation</li>';
echo '<li><i class="fas fa-chart-bar"></i> Booking Overview</li>';
echo '<li><i class="fas fa-calendar-alt"></i> Availability Calendar</li>';
echo '</ul>';
echo '</div>';
echo '</div>';
echo '<div class="admin-column admin-column2 admin-page-wrapper">';

    echo '<div class="section-features">';

    $current_user = wp_get_current_user();

    echo '<h1>Welcome '.$current_user->user_login.'</h1>';
    echo '<div id="start-bookings-button" class="button-primary">' . __('How to accept bookings', 'staylodgic') . '</div>';
    echo '<div id="start-activities-button" class="button-primary">' . __('How to accept bookings', 'staylodgic') . '</div>';
    echo '<div id="start-registration-button" class="button-primary">' . __('How to create guest registration', 'staylodgic') . '</div>';
    echo '</div>';

echo '</div>';
echo '</div>';

        // Header
        echo '<div class="admin-page-two-main">';
        echo '<div class="admin-page-twp-header">';
        echo '<h2>Hotel Management</h2>';
        echo '<h1>Staylodgic<span class="the-dot">.</span></h1>';
        echo '</div>';

        echo '<div class="admin-page-two-wrapper">';
        echo '<div class="content-container">';
        echo '<div class="left-columns">'; // Container for both content columns

        echo '<div class="left-column">';
        echo '<h4><i class="fa-solid fa-gear"></i> Hotel Settings</h4>';
        echo '<ul>';
        echo '<li>Setup New Hotel</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-bed"></i> Rooms for Reservation</h4>';
        echo '<ul>';
        echo '<li>Step 1: Create Rooms</li>';
        echo '<li>Step 2: Add Room Rates</li>';
        echo '<li>Step 3: Add Room Quantity</li>';
        echo '<li>Step 4: Create Reservations</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-biking"></i> Setup Activities</h4>';
        echo '<ul>';
        echo '<li>Step 1: Create Activities</li>';
        echo '<li>Step 2: Add Scheduled Time to Week</li>';
        echo '<li>Step 3: Create Activity Reservations</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-tachometer-alt"></i> Using Dashboard</h4>';
        echo '<ul>';
        echo '<li>- View Bookings Overview</li>';
        echo '<li>- View Activities Overview</li>';
        echo '<li>- View Availability Calendar</li>';
        echo '<li>- View Annual Availability</li>';
        echo '</ul>';

        echo '</div>'; // End of first left column

        echo '<div class="left-column">';
        echo '<h4><i class="fas fa-users"></i> Customer Registry</h4>';
        echo '<ul>';
        echo '<li>- Create new customers</li>';
        echo '<li>- Assign existing customers</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-user-check"></i> Guest Registration</h4>';
        echo '<ul>';
        echo '<li>Step 1: Customize registration fields</li>';
        echo '<li>Step 2: Create guest registration</li>';
        echo '<li>Step 3: Online registration</li>';
        echo '<li>Step 4: Send Links or QR Code</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-file-invoice-dollar"></i> Invoicing</h4>';
        echo '<ul>';
        echo '<li>- Generate invoices for bookings</li>';
        echo '<li>- Generate invoices for activities</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-file-import"></i> Import / Export</h4>';
        echo '<ul>';
        echo '<li>- Export CSV Bookings</li>';
        echo '<li>- Export Guests Registration for Month</li>';
        echo '<li>- Import and Sync iCal Availability<br/><span class="feature-update">( Future premium update )</span></li>';
        echo '<li>- Export iCal Availability Feeds<br/><span class="feature-update">( Future premium update )</span></li>';
        echo '</ul>';

        echo '</div>'; // End of second left column

        echo '<div class="left-column">';

        echo '<h4><i class="fas fa-hand-holding-usd"></i> Taxes</h4>';
        echo '<ul>';
        echo '<li>- Fixed tax</li>';
        echo '<li>- Percentage tax</li>';
        echo '<li>- Per day tax</li>';
        echo '<li>- Per person tax</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-utensils"></i> Meal plans</h4>';
        echo '<ul>';
        echo '<li>- Create free plans</li>';
        echo '<li>- Create paid plans</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-user-tag"></i> Per Person Pricing</h4>';
        echo '<ul>';
        echo '<li>- Set fixed price increments</li>';
        echo '<li>- Increment by percentage per occupant</li>';
        echo '</ul>';

        echo '<h4><i class="fas fa-percent"></i> Discounts</h4>';
        echo '<ul>';
        echo '<li>- Last minute discount</li>';
        echo '<li>- Early booking discount</li>';
        echo '<li>- Long stay discount</li>';
        echo '</ul>';

        echo '</div>'; // End of second left column

        echo '</div>'; // End of left-columns container

        echo '<div class="right-column">';
        echo '<div class="svg-container">';
        echo '<!-- SVG or SVG CSS Background here -->';
        echo '</div>';
        echo '</div>'; // End of right column

        echo '</div>'; // End of content container
        echo '</div>'; // End of page wrapper
        echo '</div>'; // End of page wrapper

    }

}

// Instantiate the class
$BatchProcessorBase = new \Staylodgic\WelcomeScreen();
