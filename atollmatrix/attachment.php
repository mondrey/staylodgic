<?php
/*
* Attachment Page
*/
if (!current_user_can('manage_options')) {
	// Display the content for admin users
	echo '<p>You do not have permission to view this content.</p>';

	return;
}
get_header();
get_template_part( 'loop', 'attachment' );
get_footer();