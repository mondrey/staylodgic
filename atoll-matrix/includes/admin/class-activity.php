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
        $total_people = isset($_POST['totalpeople']) ? sanitize_text_field($_POST['totalpeople']) : null;
        $the_post_id = isset($_POST['the_post_id']) ? sanitize_text_field($_POST['the_post_id']) : null;

        error_log('AJAX handler triggered. Selected date: ' . $selected_date);
    
        // Call the method and capture the output
        ob_start();
        $this->display_activity_schedules_with_availability($selected_date, $the_post_id, $total_people);
        $output = ob_get_clean();
    
        // Return the output as a JSON response
        wp_send_json_success($output);
    }

    /**
     * Get the number of adults, children, and the total for a reservation.
     *
     * @param int $the_post_id The post ID of the reservation.
     * @return array An array containing the number of adults, children, and the total.
     */
    public function getActivityReservationNumbers($the_post_id) {
        $existing_adults = get_post_meta($the_post_id, 'atollmatrix_reservation_activity_adults', true);
        $existing_children_array = get_post_meta($the_post_id, 'atollmatrix_reservation_activity_children', true);
        $existing_children = is_array($existing_children_array) ? $existing_children_array['number'] : 0;

        // Set values to zero if they are empty
        $existing_adults = !empty($existing_adults) ? intval($existing_adults) : 0;
        $existing_children = !empty($existing_children) ? intval($existing_children) : 0;

        $existing_spots_for_day = $existing_adults + $existing_children;

        return [
            'adults' => $existing_adults,
            'children' => $existing_children,
            'total' => $existing_spots_for_day
        ];
    }

    public function display_activity_schedules_with_availability($selected_date = null, $the_post_id = false, $total_people = false) {
        // Use today's date if $selected_date is not provided
        if (is_null($selected_date)) {
            $selected_date = date('Y-m-d');
        }

        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_time', true)) {
            $existing_activity_time = get_post_meta($the_post_id, 'atollmatrix_activity_time', true);
        }
        if (null !== get_post_meta($the_post_id, 'atollmatrix_activity_id', true)) {
            $existing_activity_id = get_post_meta($the_post_id, 'atollmatrix_activity_id', true);
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
        echo '<div class="spinner"></div><div class="activity-schedules-container">';
    

        echo '<h3>' . ucfirst($day_of_week) . '</h3>';
        // Loop through each activity post
        if ($activities->have_posts()) {
            while ($activities->have_posts()) {
                $activities->the_post();
                $post_id = get_the_ID();

                $activity_schedule = get_post_meta($post_id, 'atollmatrix_activity_schedule', true);
                $max_guests = get_post_meta($post_id, 'atollmatrix_max_guests', true);
    
                // Display the activity identifier (e.g., post title)
                echo '<div class="activity-schedule" id="activity-schedule-' . $post_id . '">';

                echo '<h4>' . get_the_title() . '</h4>';
    
                // Display the time slots for the day of the week that matches the selected date
                if (!empty($activity_schedule) && isset($activity_schedule[$day_of_week])) {
                    echo '<div class="day-schedule">';
                    foreach ($activity_schedule[$day_of_week] as $index => $time) {
                        // Calculate remaining spots for this time slot
                        
                        $remaining_spots = $this->calculate_remaining_spots($post_id, $selected_date, $time, $max_guests);

                        $remaining_spots_compare = $remaining_spots;
                        $existing_found = false;

                        if ( $existing_activity_id == $post_id && $time == $existing_activity_time ) {

                            $reservedForGuests = $this->getActivityReservationNumbers( $the_post_id );
                            $existing_spots_for_day = $reservedForGuests['total'];

                            $remaining_spots_compare = $remaining_spots + $existing_spots_for_day;
                            $existing_found = true;
                        }
                        // echo $selected_date;
                        $active_class = "time-disabled";

                        if ( $total_people <= $remaining_spots_compare && 0 !== $remaining_spots ) {
                            $active_class = "time-active";
                            if ( $existing_found ) {
                                $active_class .= ' time-choice';
                            }
                        }
                        echo '<span class="time-slot '.$active_class.'" id="time-slot-' . $day_of_week . '-' . $index . '" data-activity="'.$post_id.'" data-time="' . $time . '"><span class="activity-time-slot">' . $time . '</span><span class="time-slots-remaining">( ' . $remaining_spots . ' of ' .$max_guests. ' remaining )</span></span> ';
                        
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
