<?php
class staylodgic_ActivityReservation_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));

        add_filter("manage_edit-slgc_activityres_columns", array($this, 'slgc_activityres_edit_columns'));
        add_filter('manage_slgc_activityres_posts_custom_column', array($this, 'slgc_activityres_custom_columns'));

        add_action('admin_init', array($this, 'sort_admin_init'));
        add_action('admin_menu', array($this, 'staylodgic_enable_activityreservation_sort'));
        add_action('wp_ajax_activityreservation_sort', array($this, 'staylodgic_save_activityreservation_order'));

        if (is_admin()) {
            if (isset($_GET["page"])) {
                if ($_GET["page"] == "class-imaginem-activityreservation-posts.php") {
                    add_filter('posts_orderby', array($this, 'staylodgic_activityres_orderby'));
                }
            }
        }
    }

    /**
     * Activity reservation post columns
     *
     * @return void
     */
    public function slgc_activityres_edit_columns($columns)
    {
        unset($columns['author']);
        $new_columns = array(
            //"mreservation_section" => __('Section', 'staylodgic'),
            "reservation_customer"   => __('Customer', 'staylodgic'),
            "reservation_bookingnr"  => __('Booking Number', 'staylodgic'),
            "reservation_checkin" => __('Activity Day', 'staylodgic'),
            "reservation_status"     => __('Status', 'staylodgic'),
            "reservation_substatus"  => __('Sub Status', 'staylodgic'),
            "reservation_activitiy"       => __('Activity', 'staylodgic'),
        );

        return array_merge($columns, $new_columns);
    }

    public function slgc_activityres_custom_columns($columns)
    {
        global $post;
        $custom    = get_post_custom();
        $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        $reservation_instance = new \Staylodgic\Activity($stay_booking_number = null, $reservation_id = $post->ID);
        $bookingnumber = $reservation_instance->get_booking_number();

        switch ($columns) {
            case "reservation_customer":
                $customer_name = $reservation_instance->get_customer_edit_link_for_reservation();
                if (null !== $customer_name) {
                    echo wp_kses($customer_name, staylodgic_get_allowed_tags());
                }
                break;
            case "reservation_bookingnr":
                echo esc_attr($bookingnumber);
                break;
            case "reservation_checkin":
                $reservation_checkin       = $reservation_instance->get_checkin_date();
                echo '<p class="post-status-reservation-date post-status-reservation-date-checkin"><i class="fa-solid fa-arrow-right"></i> ' . esc_attr(staylodgic_formatDate($reservation_checkin)) . '</p>';

                break;
            case "reservation_status":
                $reservation_status = $reservation_instance->get_reservation_status();
                echo esc_attr(ucfirst($reservation_status));
                break;
            case "reservation_substatus":
                $reservation_substatus = $reservation_instance->get_reservation_sub_status();
                echo esc_attr(ucfirst($reservation_substatus));
                break;
            case "reservation_activitiy":
                $activity_title = $reservation_instance->get_name_for_activity();
                echo esc_html($activity_title);
                break;
        }
    }

    /**
     * Activity post sort
     *
     * @return void
     */
    public function staylodgic_enable_activityreservation_sort()
    {
        add_submenu_page('edit.php?post_type=slgc_activityreservation', 'Sort activityreservations', 'Sort Activities', 'edit_posts', basename(__FILE__), array($this, 'staylodgic_sort_activityreservation'));
    }

    public function staylodgic_activityres_orderby($orderby)
    {
        global $wpdb;
        $orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
        return ($orderby);
    }

    public function staylodgic_sort_activityreservation()
    {
        $activityreservation = new WP_Query('post_type=slgc_activityreservation&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
        <div class="wrap">
            <h2><?php _e('Sort activityreservation', 'staylodgic'); ?> <img src="<?php echo esc_url(home_url() . '/wp-admin/images/loading.gif'); ?>" id="loading-animation" /></h2>
            <div class="description">
                <?php _e('Drag and Drop the slides to order them', 'staylodgic'); ?>
            </div>
            <ul id="portfolio-list">
                <?php while ($activityreservation->have_posts()) : $activityreservation->the_post(); ?>
                    <li id="<?php the_id(); ?>">
                        <div>
                            <?php
                            $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id());
                            $custom    = get_post_custom(get_the_ID());
                            $activityreservation_cats = get_the_terms(get_the_ID(), 'slgc_activityreservationtype');

                            ?>
                            <?php if ($image_url) {
                                echo '<img class="staylodgic_admin_sort_image" src="' . esc_url($image_url) . '" width="30px" height="30px" alt="" />';
                            } ?>
                            <span class="staylodgic_admin_sort_title"><?php the_title(); ?></span>
                            <?php
                            if ($activityreservation_cats) {
                            ?>
                                <span class="staylodgic_admin_sort_categories">
                                    <?php foreach ($activityreservation_cats as $taxonomy) {
                                        echo ' | ' . esc_html($taxonomy->name);
                                    } ?>
                                </span>
                            <?php
                            }
                            ?>
                        </div>

                    </li>
                <?php endwhile; ?>
        </div>

<?php
    }

    public function staylodgic_save_activityreservation_order()
    {
        global $wpdb; // WordPress database class

        $order   = explode(',', $_POST['order']);
        $counter = 0;

        foreach ($order as $sort_id) {
            $wpdb->update(
                $wpdb->posts,
                array('menu_order' => intval($counter)), // Ensuring integer
                array('ID' => intval($sort_id))          // Ensuring integer
            );
            $counter++;
        }
        die(1);
    }

    /**
     * Register activityreservation Post Manager
     *
     * @return void
     */
    public function init()
    {

        $labels = array(
            'name'               => _x('Activity Reservations', 'post type general name', 'staylodgic'),
            'singular_name'      => _x('Activity Reservation', 'post type singular name', 'staylodgic'),
            'menu_name'          => _x('Activity Reservations', 'admin menu', 'staylodgic'),
            'name_admin_bar'     => _x('Activity Reservation', 'add new on admin bar', 'staylodgic'),
            'add_new'            => _x('Add New Reservation', 'activity reservation', 'staylodgic'),
            'add_new_item'       => __('Add New Activity Reservation', 'staylodgic'),
            'new_item'           => __('New Activity Reservation', 'staylodgic'),
            'edit_item'          => __('Edit Activity Reservation', 'staylodgic'),
            'view_item'          => __('View Activity Reservation', 'staylodgic'),
            'all_items'          => __('All Activity Reservations', 'staylodgic'),
            'search_items'       => __('Search Activity Reservations', 'staylodgic'),
            'parent_item_colon'  => __('Parent Activity Reservations:', 'staylodgic'),
            'not_found'          => __('No activity reservations found.', 'staylodgic'),
            'not_found_in_trash' => __('No activity reservations found in Trash.', 'staylodgic')
        );

        $args = array(
            'labels'           => $labels,
            'singular_label'  => __('Activity Reservation', 'staylodgic'),
            'public'          => true,
            'publicly_queryable' => false,
            'show_ui'         => true,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => true,
            'menu_position'   => 40,
            'menu_icon'       => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1NzYgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTY0IDY0QzI4LjcgNjQgMCA5Mi43IDAgMTI4djY0YzAgOC44IDcuNCAxNS43IDE1LjcgMTguNkMzNC41IDIxNy4xIDQ4IDIzNSA0OCAyNTZzLTEzLjUgMzguOS0zMi4zIDQ1LjRDNy40IDMwNC4zIDAgMzExLjIgMCAzMjB2NjRjMCAzNS4zIDI4LjcgNjQgNjQgNjRINTEyYzM1LjMgMCA2NC0yOC43IDY0LTY0VjMyMGMwLTguOC03LjQtMTUuNy0xNS43LTE4LjZDNTQxLjUgMjk0LjkgNTI4IDI3NyA1MjggMjU2czEzLjUtMzguOSAzMi4zLTQ1LjRjOC4zLTIuOSAxNS43LTkuOCAxNS43LTE4LjZWMTI4YzAtMzUuMy0yOC43LTY0LTY0LTY0SDY0em02NCAxMTJsMCAxNjBjMCA4LjggNy4yIDE2IDE2IDE2SDQzMmM4LjggMCAxNi03LjIgMTYtMTZWMTc2YzAtOC44LTcuMi0xNi0xNi0xNkgxNDRjLTguOCAwLTE2IDcuMi0xNiAxNnpNOTYgMTYwYzAtMTcuNyAxNC4zLTMyIDMyLTMySDQ0OGMxNy43IDAgMzIgMTQuMyAzMiAzMlYzNTJjMCAxNy43LTE0LjMgMzItMzIgMzJIMTI4Yy0xNy43IDAtMzItMTQuMy0zMi0zMlYxNjB6Ii8+PC9zdmc+',
            'rewrite'         => array('slug' => 'activityreservations'),
            'supports' => array('title', 'author', 'thumbnail'),
        );

        register_post_type('slgc_activityres', $args);
        /*
         * Add Taxonomy for activityreservation
         */
        register_taxonomy('slgc_slgc_activityrestype', array("staylodgic_activityres"), array("hierarchical" => true, "label" => "Activity Reservation Category", "singular_label" => "staylodgic_activityrestypes", "rewrite" => true));
    }
    /**
     * Enqueue Scripts and Styles
     *
     * @return    void
     */
    public function sort_admin_init()
    {
        if (is_admin()) {
            // Load only if in a Post or Page Manager
            if ('edit.php' == basename($_SERVER['PHP_SELF'])) {
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
                wp_enqueue_style('mtheme-activityreservation-sorter-CSS', plugin_dir_url(__FILE__) . 'css/style.css', false, '1.0', 'all');
                if (isset($_GET["page"])) {
                    if ($_GET["page"] == "class-imaginem-activityreservation-posts.php") {
                        wp_enqueue_script("post-sorter-JS", plugin_dir_url(__FILE__) . "js/post-sorter.js", array('jquery'), "1.1");
                    }
                }
            }
        }
    }
}
$staylodgic_activityres_post_type = new staylodgic_ActivityReservation_Posts();
?>