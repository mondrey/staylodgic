<?php
class staylodgic_Room_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'sort_admin_init'));
        add_action('admin_menu', array($this, 'staylodgic_enable_room_sort'));
        add_action('wp_ajax_room_sort', array($this, 'staylodgic_save_room_order'));

        if (is_admin()) {
            if (isset($_GET["page"])) {
                if ($_GET["page"] == "class-staylodgic-room-posts.php") {
                    add_filter('posts_orderby', array($this, 'staylodgic_room_orderby'));
                }
            }
        }
    }

    public function staylodgic_enable_room_sort()
    {
        add_submenu_page('edit.php?post_type=slgc_room', 'Sort rooms', 'Sort Rooms', 'edit_posts', basename(__FILE__), array($this, 'staylodgic_sort_room'));
    }
    public function staylodgic_room_orderby($orderby)
    {
        global $wpdb;
        $orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
        return ($orderby);
    }
    public function staylodgic_sort_room()
    {
        $room = new WP_Query('post_type=slgc_room&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
        <div class="wrap">
            <h2><?php _e('Sort room', 'staylodgic'); ?> <img src="<?php echo esc_url(home_url() . '/wp-admin/images/loading.gif'); ?>" id="loading-animation" /></h2>
            <div class="description">
                <?php _e('Drag and Drop the slides to order them', 'staylodgic'); ?>
            </div>
            <ul id="portfolio-list">
                <?php while ($room->have_posts()) : $room->the_post(); ?>
                    <li id="<?php the_id(); ?>">
                        <div>
                            <?php
                            $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id());
                            $custom    = get_post_custom(get_the_ID());
                            $room_cats = get_the_terms(get_the_ID(), 'slgc_roomtype');

                            ?>
                            <?php if ($image_url) {
                                echo '<img class="staylodgic_admin_sort_image" src="' . $image_url . '" width="30px" height="30px" alt="" />';
                            } ?>
                            <span class="staylodgic_admin_sort_title"><?php the_title(); ?></span>
                            <?php
                            if ($room_cats) {
                            ?>
                                <span class="staylodgic_admin_sort_categories">
                                    <?php foreach ($room_cats as $taxonomy) {
                                        echo ' | ' .  esc_html($taxonomy->name);
                                    } ?>
                                </span>
                            <?php
                            }
                            ?>
                        </div>

                    </li>
                <?php endwhile; ?>
        </div><!-- End div#wrap //-->

<?php
    }
    public function staylodgic_save_room_order()
    {

        // Check for nonce security
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
            wp_die();
        }

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
     * Registers TinyMCE rich editor buttons
     *
     * @return    void
     */
    public function init()
    {
        /*
         * Register Room Post
         */
        $args = array(
            'labels'            => array(
                'name'          => __('Rooms', 'staylodgic'),
                'add_new'       => __('Create a Room', 'staylodgic'),
                'add_new_item'  => __( 'Add New Room', 'staylodgic' ),
                'menu_name'     => __('Rooms', 'staylodgic'),
                'singular_name' => __('Room', 'staylodgic'),
                'all_items'     => __('All Rooms', 'staylodgic'),
            ),
            'singular_label'  => __('Room', 'staylodgic'),
            'public'          => true,
            'publicly_queryable' => false,
            'show_ui'         => true,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => true,
            'menu_position'   => 35,
            'menu_icon'       => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NDAgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTMyIDMyYzE3LjcgMCAzMiAxNC4zIDMyIDMyVjMyMEgyODhWMTYwYzAtMTcuNyAxNC4zLTMyIDMyLTMySDU0NGM1MyAwIDk2IDQzIDk2IDk2VjQ0OGMwIDE3LjctMTQuMyAzMi0zMiAzMnMtMzItMTQuMy0zMi0zMlY0MTZIMzUyIDMyMCA2NHYzMmMwIDE3LjctMTQuMyAzMi0zMiAzMnMtMzItMTQuMy0zMi0zMlY2NEMwIDQ2LjMgMTQuMyAzMiAzMiAzMnptMTQ0IDk2YTgwIDgwIDAgMSAxIDAgMTYwIDgwIDgwIDAgMSAxIDAtMTYweiIvPjwvc3ZnPg==',
            'rewrite'         => array('slug' => 'rooms'), //Use a slug like "work" or "project" that shouldnt be same with your page name
            'supports' => array('title', 'author', 'thumbnail'), //Boxes will be shown in the panel
        );

        register_post_type('slgc_room', $args);
        /*
         * Add Taxonomy for room 'Type'
         */
        register_taxonomy('slgc_roomtype', array("staylodgic_room"), array("hierarchical" => true, "label" => "Room Category", "singular_label" => "staylodgic_roomtypes", "rewrite" => true));

        /*
     * Hooks for the room and Featured viewables
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
                wp_enqueue_style('mtheme-room-sorter-CSS', plugin_dir_url(__FILE__) . 'css/style.css', false, '1.0', 'all');
                if (isset($_GET["page"])) {
                    if ($_GET["page"] == "class-staylodgic-room-posts.php") {
                        wp_enqueue_script("post-sorter-JS", plugin_dir_url(__FILE__) . "js/post-sorter.js", array('jquery'), "1.1");
                    }
                }
            }
        }
    }
}
$staylodgic_room_post_type = new staylodgic_Room_Posts();
?>