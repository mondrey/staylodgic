<?php
class staylodgic_Activity_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'sort_admin_init'));
        add_action('admin_menu', array($this, 'staylodgic_enable_activity_sort'));
        add_action('wp_ajax_activity_sort', array($this, 'staylodgic_save_activity_order'));

        if (is_admin()) {
            if (isset($_GET["page"])) {
                if ($_GET["page"] == "class-imaginem-activity-posts.php") {
                    add_filter('posts_orderby', array($this, 'staylodgic_activity_orderby'));
                }
            }
        }
    }

    public function staylodgic_enable_activity_sort()
    {
        add_submenu_page('edit.php?post_type=slgc_activity', __('Sort activities', 'staylodgic'), __('Sort Activities', 'staylodgic'), 'edit_posts', basename(__FILE__), array($this, 'staylodgic_sort_activity'));
    }
    public function staylodgic_activity_orderby($orderby)
    {
        global $wpdb;
        $orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
        return ($orderby);
    }
    public function staylodgic_sort_activity()
    {
        $activity = new WP_Query('post_type=slgc_activity&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
        <div class="wrap">
            <h2><?php _e('Sort activity', 'staylodgic'); ?> <img src="<?php echo esc_url(home_url() . '/wp-admin/images/loading.gif'); ?>" id="loading-animation" /></h2>
            <div class="description">
                <?php _e('Drag and Drop the slides to order them', 'staylodgic'); ?>
            </div>
            <ul id="portfolio-list">
                <?php while ($activity->have_posts()) : $activity->the_post(); ?>
                    <li id="<?php the_id(); ?>">
                        <div>
                            <?php
                            $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id());
                            $custom    = get_post_custom(get_the_ID());
                            $activity_cats = get_the_terms(get_the_ID(), 'slgc_activitytype');

                            ?>
                            <?php if ($image_url) {
                                echo '<img class="staylodgic_admin_sort_image" src="' . esc_url($image_url) . '" width="30px" height="30px" alt="" />';
                            } ?>
                            <span class="staylodgic_admin_sort_title"><?php the_title(); ?></span>
                            <?php
                            if ($activity_cats) {
                            ?>
                                <span class="staylodgic_admin_sort_categories">
                                    <?php foreach ($activity_cats as $taxonomy) {
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
    public function staylodgic_save_activity_order()
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
     *
     * @return    void
     */
    public function init()
    {
        /*
         * Register activity Post Manager
         */
        $labels = array(
            'name'               => _x('Activities', 'post type general name', 'staylodgic'),
            'singular_name'      => _x('Activity', 'post type singular name', 'staylodgic'),
            'menu_name'          => _x('Activities', 'admin menu', 'staylodgic'),
            'name_admin_bar'     => _x('Activity', 'add new on admin bar', 'staylodgic'),
            'add_new'            => _x('Add New', 'activity', 'staylodgic'),
            'add_new_item'       => __('Add New Activity', 'staylodgic'),
            'new_item'           => __('New Activity', 'staylodgic'),
            'edit_item'          => __('Edit Activity', 'staylodgic'),
            'view_item'          => __('View Activity', 'staylodgic'),
            'all_items'          => __('All Activities', 'staylodgic'),
            'search_items'       => __('Search Activities', 'staylodgic'),
            'parent_item_colon'  => __('Parent Activities:', 'staylodgic'),
            'not_found'          => __('No activities found.', 'staylodgic'),
            'not_found_in_trash' => __('No activities found in Trash.', 'staylodgic')
        );

        $args = array(
            'labels'           => $labels,
            'public'          => true,
            'publicly_queryable' => false,
            'show_ui'         => true,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => true,
            'menu_position'   => 39,
            'menu_icon'       => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTIyNCAzMkg2NEM0Ni4zIDMyIDMyIDQ2LjMgMzIgNjR2NjRjMCAxNy43IDE0LjMgMzIgMzIgMzJINDQxLjRjNC4yIDAgOC4zLTEuNyAxMS4zLTQuN2w0OC00OGM2LjItNi4yIDYuMi0xNi40IDAtMjIuNmwtNDgtNDhjLTMtMy03LjEtNC43LTExLjMtNC43SDI4OGMwLTE3LjctMTQuMy0zMi0zMi0zMnMtMzIgMTQuMy0zMiAzMnpNNDgwIDI1NmMwLTE3LjctMTQuMy0zMi0zMi0zMkgyODhWMTkySDIyNHYzMkg3MC42Yy00LjIgMC04LjMgMS43LTExLjMgNC43bC00OCA0OGMtNi4yIDYuMi02LjIgMTYuNCAwIDIyLjZsNDggNDhjMyAzIDcuMSA0LjcgMTEuMyA0LjdINDQ4YzE3LjcgMCAzMi0xNC4zIDMyLTMyVjI1NnpNMjg4IDQ4MFYzODRIMjI0djk2YzAgMTcuNyAxNC4zIDMyIDMyIDMyczMyLTE0LjMgMzItMzJ6Ii8+PC9zdmc+',
            'rewrite'         => array('slug' => 'activities'), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('slgc_activity', $args);
        /*
         * Add Taxonomy for activity 'Type'
         */
        register_taxonomy('slgc_activitytype', array("staylodgic_activity"), array("hierarchical" => true, "label" => "Activity Category", "singular_label" => "staylodgic_activitytypes", "rewrite" => true));

        /*
     * Hooks for the activity and Featured viewables
     */
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
                wp_enqueue_style('mtheme-activity-sorter-CSS', plugin_dir_url(__FILE__) . 'css/style.css', false, '1.0', 'all');
                if (isset($_GET["page"])) {
                    if ($_GET["page"] == "class-staylodgic-activity-posts.php") {
                        wp_enqueue_script("post-sorter-JS", plugin_dir_url(__FILE__) . "js/post-sorter.js", array('jquery'), "1.1");
                    }
                }
            }
        }
    }
}
$staylodgic_activity_post_type = new staylodgic_Activity_Posts();
?>