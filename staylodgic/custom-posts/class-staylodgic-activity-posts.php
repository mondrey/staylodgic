<?php
class staylodgic_Activity_Posts
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'sort_admin_init'));
        add_filter("manage_edit-activity_columns", array($this, 'activity_edit_columns'));
        add_action("manage_posts_custom_column", array($this, 'activity_custom_columns'));
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
        add_submenu_page('edit.php?post_type=slgc_activity', 'Sort activities', 'Sort Activities', 'edit_posts', basename(__FILE__), array($this, 'staylodgic_sort_activity'));
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
		<h2>Sort activity<img src="<?php echo home_url(); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>
		<div class="description">
		Drag and Drop the slides to order them
		</div>
		<ul id="portfolio-list">
		<?php while ($activity->have_posts()): $activity->the_post();?>
				<li id="<?php the_id();?>">
				<div>
				<?php
    $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id());
            $custom    = get_post_custom(get_the_ID());
            $activity_cats = get_the_terms(get_the_ID(), 'slgc_activitytype');

            ?>
				<?php if ($image_url) {echo '<img class="staylodgic_admin_sort_image" src="' . $image_url . '" width="30px" height="30px" alt="" />';}?>
				<span class="staylodgic_admin_sort_title"><?php the_title();?></span>
				<?php
    if ($activity_cats) {
                ?>
				<span class="staylodgic_admin_sort_categories"><?php foreach ($activity_cats as $taxonomy) {echo ' | ' . $taxonomy->name;}?></span>
				<?php
    }
            ?>
				</div>

				</li>
			<?php endwhile;?>
		</div><!-- End div#wrap //-->

	<?php
}
    public function staylodgic_save_activity_order()
    {
        global $wpdb; // WordPress database class

        $order   = explode(',', $_POST['order']);
        $counter = 0;

        foreach ($order as $sort_id) {
            $wpdb->update($wpdb->posts, array('menu_order' => $counter), array('ID' => $sort_id));
            $counter++;
        }
        die(1);
    }

    /*
     * activity Admin columns
     */
    public function activity_custom_columns($column)
    {
        global $post;
        $custom    = get_post_custom();
        $image_url = wp_get_attachment_thumb_url(get_post_thumbnail_id($post->ID));

        $full_image_id  = get_post_thumbnail_id(($post->ID), 'fullimage');
        $full_image_url = wp_get_attachment_image_src($full_image_id, 'fullimage');
        if (isset($full_image_url[0])) {
            $full_image_url = $full_image_url[0];
        }

        if (!defined('MTHEME')) {
            $staylodgic_shortname = "staylodgic_p2";
            define('MTHEME', $staylodgic_shortname);
        }

        switch ($column) {
            case "activity_image":
                if (isset($image_url) && $image_url != "") {
                    echo '<a class="thickbox" href="' . $full_image_url . '"><img src="' . $image_url . '" width="60px" height="60px" alt="featured" /></a>';
                }
                break;
            case "theme_description":
                if (isset($custom['staylodgic_thumbnail_desc'][0])) {echo $custom['staylodgic_thumbnail_desc'][0];}
                break;
            case "video":
                if (isset($custom['staylodgic_lightbox_video'][0])) {echo $custom['staylodgic_lightbox_video'][0];}
                break;
            case 'slgc_activitytype':
                echo get_the_term_list($post->ID, 'slgc_activitytype', '', ', ', '');
                break;
        }
    }

    public function activity_edit_columns($columns)
    {
        $columns = array(
            "cb"                    => "<input type=\"checkbox\" />",
            "title"                 => __('Activity Title', 'staylodgic'),
            "theme_description"     => __('Description', 'staylodgic'),
            "video"                 => __('Video', 'staylodgic'),
            "staylodgic_activitytypes" => __('slgc_activitytype', 'staylodgic'),
            "activity_image"            => __('Image', 'staylodgic'),
        );

        return $columns;
    }

    /**
     * Registers TinyMCE rich editor buttons
     *
     * @return    void
     */
    public function init()
    {
        /*
         * Register Featured Post Manager
         */
        //add_action('init', 'staylodgic_featured_register');
        //add_action('init', 'activity_register');//Always use a shortname like "staylodgic_" not to see any 404 errors
        /*
         * Register activity Post Manager
         */
        $staylodgic_activity_slug = "activities";
        if (function_exists('staylodgic_get_option_data')) {
            $staylodgic_activity_slug = staylodgic_get_option_data('activity_permalink_slug');
        }
        if ($staylodgic_activity_slug == "" || !isset($staylodgic_activity_slug)) {
            $staylodgic_activity_slug = "activities";
        }
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
            'show_ui'         => true,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => true,
            'menu_position'   => 39,
            'menu_icon'       => plugin_dir_url(__FILE__) . 'images/portfolio.png',
            'rewrite'         => array('slug' => $staylodgic_activity_slug), //Use a slug like "work" or "project" that shouldnt be same with your page name
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
                    if ($_GET["page"] == "class-imaginem-activity-posts.php") {
                        wp_enqueue_script("post-sorter-JS", plugin_dir_url(__FILE__) . "js/post-sorter.js", array('jquery'), "1.1");
                    }
                }
            }
        }
    }

}
$staylodgic_activity_post_type = new staylodgic_Activity_Posts();

class staylodgic_Activitycategory_add_image
{

    public function __construct()
    {
        add_action('admin_head', array(&$this, 'staylodgic_admin_head'));
        add_action('edit_term', array(&$this, 'staylodgic_save_tax_pic'));
        add_action('create_term', array(&$this, 'staylodgic_save_tax_pic'));
        add_filter("manage_edit-staylodgic_activitytypes_columns", array(&$this, 'staylodgic_activitytype_columns'));
        add_action("manage_staylodgic_activitytypes_custom_column", array(&$this, 'staylodgic_manage_workype_columns'), 10, 3);
    }

    // Add to admin_init function

    public function staylodgic_activitytype_columns($columns)
    {
        $columns['activitytype_image'] = 'Image';
        return $columns;
    }

    // Add to admin_init function

    public function staylodgic_manage_workype_columns($value, $columns, $term_id)
    {
        $staylodgic_activitytype_image_id = get_option('staylodgic_activitytype_image_id' . $term_id);
        switch ($columns) {
            case 'activitytype_image':
                if ($staylodgic_activitytype_image_id) {
                    $staylodgic_activitytype_image_url = wp_get_attachment_image_src($staylodgic_activitytype_image_id, 'thumbnail', false);
                    $value                          = '<img src="' . $staylodgic_activitytype_image_url[0] . '" width="100px" height="auto" />';
                }
                break;

            default:
                break;
        }
        return $value;
    }

    public function staylodgic_admin_head()
    {
        $taxonomies = get_taxonomies();
        $taxonomies = array('slgc_activitytype'); // uncomment and specify particular taxonomies you want to add image feature.
        if (is_array($taxonomies)) {
            foreach ($taxonomies as $z_taxonomy) {
                add_action($z_taxonomy . '_add_form_fields', array(&$this, 'staylodgic_tax_field'));
                add_action($z_taxonomy . '_edit_form_fields', array(&$this, 'staylodgic_tax_field'));
            }
        }
    }

    // add image field in add form
    public function staylodgic_tax_field($taxonomy)
    {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');
        wp_enqueue_media();

        if (empty($taxonomy)) {
            echo '<div class="form-field">
					<label for="staylodgic_activitytype_input">Image</label>
					<input size="40" type="text" name="staylodgic_activitytype_input" id="staylodgic_activitytype_input" value="" />
					<input type="text" name="staylodgic_activitytype_image_id" id="staylodgic_activitytype_image_id" value="" />
				</div>';
        } else {

            $staylodgic_activitytype_input_url = '';
            $staylodgic_activitytype_image_id  = '';

            if (isset($taxonomy->term_id)) {
                //$staylodgic_activitytype_input_url = get_option('staylodgic_activitytype_input' . $taxonomy->term_id);
                $staylodgic_activitytype_image_id = get_option('staylodgic_activitytype_image_id' . $taxonomy->term_id);
            }

            echo '<tr class="form-field">
			<th scope="row" valign="top"><label for="staylodgic_activitytype_input">Image</label></th>
			<td>
			<input type="hidden" name="staylodgic_activitytype_image_id" id="staylodgic_activitytype_image_id" value="' . $staylodgic_activitytype_image_id . '" />
			<a class="button" id="staylodgic_upload_work_image">Set category image</a>
			<div class="inside" id="featured_activitytype_image_wrap">';
            if (!empty($staylodgic_activitytype_image_id)) {
                $staylodgic_activitytype_image_url = wp_get_attachment_image_src($staylodgic_activitytype_image_id, 'thumbnail', false);
                echo '<img id="featured_activitytype_image" src="' . $staylodgic_activitytype_image_url[0] . '" style="max-width:200px;border: 1px solid #ccc;padding: 5px;box-shadow: 5px 5px 10px #ccc;margin-top: 10px;" >';
                echo '<a style="display:block;" id="remove_activitytype_image" href="#">Remove category Image</a>';
            }
            echo '</div>';
            echo '</td></tr><br/>';
        }
        ?>
	<script>
	jQuery(document).ready(function($){
		// Get input target field
		var targetfield="staylodgic_activitytype_input";

		jQuery("#staylodgic_upload_work_image").click( function( event ) {
			var jQueryel = jQuery(this);
			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( typeof(custom_file_frame)!=="undefined" ) {
				custom_file_frame.open();
				return;
			}

			// Create the media frame.
			custom_file_frame = wp.media.frames.customHeader = wp.media({
				// Set the title of the modal.
				title: jQueryel.data("choose"),

				// Tell the modal to show only images. Ignore if want ALL
				library: {
					type: 'image'
				},
				// Customize the submit button.
				button: {
					// Set the text of the button.
					text: jQueryel.data("update")
				}
			});

			custom_file_frame.on( "select", function() {
				// Grab the selected attachment.
				var attachment = custom_file_frame.state().get("selection").first();
				var active_image = jQuery('#featured_activitytype_image');

				if (active_image.length > 0 ) {
					$(active_image).attr('src', attachment.attributes.sizes.thumbnail.url);
				} else {
					var activitytypeImg = jQuery('<img/>');
						activitytypeImg.attr('id','featured_activitytype_image')
						activitytypeImg.attr('src', attachment.attributes.sizes.thumbnail.url);
						activitytypeImg.attr('style',"max-width:200px;border: 1px solid #ccc;padding: 5px;box-shadow: 5px 5px 10px #ccc;margin-top: 10px;")
						activitytypeImg.appendTo('#featured_activitytype_image_wrap');

					jQuery( '<a style="display:block;" id="remove_activitytype_image" href="#">Remove activitytype Image</a>' ).appendTo( "#featured_activitytype_image_wrap" );
				}
				jQuery("#staylodgic_activitytype_image_id").val(attachment.id);
			});

			custom_file_frame.open();
		});

		jQuery("#featured_activitytype_image_wrap").on("click", "#remove_activitytype_image", function(){
			jQuery('#remove_activitytype_image,#featured_activitytype_image').remove();
			jQuery('#staylodgic_activitytype_image_id').val("");
			return false;
		});
	});
	</script>
	<?php
}

    // save our taxonomy image while edit or save term
    public function staylodgic_save_tax_pic($term_id)
    {
        if (isset($_POST['staylodgic_activitytype_image_id'])) {
            update_option('staylodgic_activitytype_image_id' . $term_id, $_POST['staylodgic_activitytype_image_id']);
        }
    }

    // output taxonomy image url for the given term_id (NULL by default)
    public function staylodgic_activitytype_input_url($term_id = null)
    {
        if ($term_id) {
            $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
            return get_option('staylodgic_activitytype_input' . $current_term->term_id);
        }
    }

}
$staylodgic_Activitycategory_add_image = new staylodgic_Activitycategory_add_image();
?>