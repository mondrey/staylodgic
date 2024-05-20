<?php
get_header();

// Define the home choice variable
$home_choice = 'template-bookroom.php'; // Set this variable to one of your template filenames as needed

// Array of allowed templates
$allowed_templates = [
    'template-bookroom.php',
    'template-guestregistration.php',
    'template-bookingdetails.php',
    'template-bookactivity.php'
];

// Check if the home choice is in the allowed templates and load it
if (in_array($home_choice, $allowed_templates)) {
    locate_template($home_choice, true);
}

get_footer();
?>
