<?php
namespace AtollMatrix;

class Activity
{
    public function __construct()
    {
        add_action('wp_ajax_get_activity_schedules', array($this, 'get_activity_schedules_ajax_handler'));
        add_action('wp_ajax_nopriv_get_activity_schedules', array($this, 'get_activity_schedules_ajax_handler'));

    }


    public function get_activity_schedules_ajax_handler() {
        $selected_date = isset($_POST['selected_date']) ? sanitize_text_field($_POST['selected_date']) : null;

        error_log('AJAX handler triggered. Selected date: ' . $selected_date);
    
        // Call the method and capture the output
        ob_start();
        $this->display_activity_schedules_with_availability($selected_date);
        $output = ob_get_clean();
    
        // Return the output as a JSON response
        wp_send_json_success($output);
    }

    public function display_activity_schedules_with_availability($selected_date = null) {
        // Use today's date if $selected_date is not provided
        if (is_null($selected_date)) {
            $selected_date = date('Y-m-d');
        }
    
        // Get the day of the week for the selected date
        $day_of_week = strtolower(date('l', strtotime($selected_date)));
    
        // Query all activity posts
        $args = array(
            'post_type' => 'atmx_activity',
            'posts_per_page' => -1,
        );
        $activities = new \WP_Query($args);
    
        // Start the container div
        echo '<div class="activity-schedules-container">';
    
        // Loop through each activity post
        if ($activities->have_posts()) {
            while ($activities->have_posts()) {
                $activities->the_post();
                $post_id = get_the_ID();
                $activity_schedule = get_post_meta($post_id, 'atollmatrix_activity_schedule', true);
                $max_guests = get_post_meta($post_id, 'atollmatrix_max_guests', true);
    
                // Display the activity identifier (e.g., post title)
                echo '<div class="activity-schedule" id="activity-schedule-' . $post_id . '">';
                echo '<h3>' . get_the_title() . '</h3>';
    
                // Display the time slots for the day of the week that matches the selected date
                if (!empty($activity_schedule) && isset($activity_schedule[$day_of_week])) {
                    echo '<div class="day-schedule">';
                    echo '<strong>' . ucfirst($day_of_week) . ':</strong> ';
                    foreach ($activity_schedule[$day_of_week] as $index => $time) {
                        // Calculate remaining spots for this time slot
                        $remaining_spots = $this->calculate_remaining_spots($post_id, $selected_date, $time, $max_guests);
                        // echo $selected_date;
                        echo '<span class="time-slot" id="time-slot-' . $day_of_week . '-' . $index . '" data-activity="'.$post_id.'" data-time="' . $time . '">' . $time . ' (' . $remaining_spots . ' spots remaining)</span> ';
                    }
                    echo '</div>';
                }
    
                echo '</div>'; // Close the activity-schedule div
            }
        }
    
        // Close the container div
        echo '</div>';
    
        // Reset post data
        wp_reset_postdata();
    }     
    
    public function calculate_remaining_spots($activity_id, $selected_date, $selected_time, $max_guests) {
        // Query all reservation posts for this activity, date, and time
        $args = array(
            'post_type' => 'atmx_activityres',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'atollmatrix_activity_id',
                    'value' => $activity_id,
                ),
                array(
                    'key' => 'atollmatrix_reservation_checkin',
                    'value' => $selected_date,
                ),
                array(
                    'key' => 'atollmatrix_activity_time',
                    'value' => $selected_time,
                ),
            ),
        );
        $reservations = new \WP_Query($args);
    
        // Calculate the total number of guests from the reservations
        $total_guests = 0;
        if ($reservations->have_posts()) {
            while ($reservations->have_posts()) {
                $reservations->the_post();
                $adults = get_post_meta(get_the_ID(), 'atollmatrix_reservation_activity_adults', true);
                $children = get_post_meta(get_the_ID(), 'atollmatrix_reservation_activity_children', true);
                $total_guests += $adults + $children['number'];
            }
        }
    
        // Calculate remaining spots
        $remaining_spots = $max_guests - $total_guests;
    
        return $remaining_spots;
    }
    

}

$instance = new \AtollMatrix\Activity();
