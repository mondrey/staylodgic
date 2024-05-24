<?php
// Check if the user is logged in
if (is_user_logged_in()) {

    // Define the home choice variable
    $home_choice = staylodgic_get_option('booking_menu_one'); // Fetch the chosen template from your options

    // Array of allowed templates
    $allowed_templates = [
        'template-bookroom.php',
        'template-guestregistration.php',
        'template-bookingdetails.php',
        'template-bookactivity.php'
    ];

    // Check if the home choice is in the allowed templates and load it
    if (in_array($home_choice, $allowed_templates)) {

        get_header();
        locate_template($home_choice, true);
        get_footer();

    } else {
        // Redirect to the login page if the user is not logged in
        wp_redirect(wp_login_url());
        exit;
    }
} else {
    // Redirect to the login page if the user is not logged in
    wp_redirect(wp_login_url());
    exit;
}
?>
