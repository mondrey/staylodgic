<?php
class Imaginem_Room_Posts {

    function __construct() 
    {	
        add_action('init', array( $this, 'init'));
        add_action('admin_init', array( $this, 'sort_admin_init'));
        add_filter("manage_edit-room_columns", array( $this, 'room_edit_columns'));
		add_action("manage_posts_custom_column",  array( $this, 'room_custom_columns'));
		add_action('admin_menu', array( $this, 'mtheme_enable_room_sort') );
		add_action('wp_ajax_room_sort', array( $this, 'mtheme_save_room_order'));

		if( is_admin() ) {
			if ( isSet($_GET["page"]) ) {
				if ( $_GET["page"] == "class-imaginem-room-posts.php" ) {
					add_filter( 'posts_orderby', array( $this, 'mtheme_room_orderby'));
				}
			}
		}
	}

	function mtheme_enable_room_sort() {
	    add_submenu_page('edit.php?post_type=room', 'Sort rooms', 'Sort Rooms', 'edit_posts', basename(__FILE__), array( $this, 'mtheme_sort_room'));
	}
	function mtheme_room_orderby($orderby){
		global $wpdb;
		$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
		return($orderby);
	}
	function mtheme_sort_room() {
		$room = new WP_Query('post_type=room&posts_per_page=-1&orderby=menu_order&order=ASC');
	?>
		<div class="wrap">
		<h2>Sort room<img src="<?php echo home_url(); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>
		<div class="description">
		Drag and Drop the slides to order them
		</div>
		<ul id="portfolio-list">
		<?php while ( $room->have_posts() ) : $room->the_post(); ?>
			<li id="<?php the_id(); ?>">
			<div>
			<?php 
			$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id() );
			$custom = get_post_custom(get_the_ID());
			$room_cats = get_the_terms( get_the_ID(), 'roomtypes' );
			
			?>
			<?php if ($image_url) { echo '<img class="mtheme_admin_sort_image" src="'.$image_url.'" width="30px" height="30px" alt="" />'; } ?>
			<span class="mtheme_admin_sort_title"><?php the_title(); ?></span>
			<?php
			if ($room_cats) {
			?>
			<span class="mtheme_admin_sort_categories"><?php foreach ($room_cats as $taxonomy) { echo ' | ' . $taxonomy->name; } ?></span>
			<?php
			}
			?>
			</div>

			</li>
		<?php endwhile; ?>
		</div><!-- End div#wrap //-->
	 
	<?php
	}
	function mtheme_save_room_order() {
		global $wpdb; // WordPress database class
	 
		$order = explode(',', $_POST['order']);
		$counter = 0;
	 
		foreach ($order as $sort_id) {
			$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $sort_id) );
			$counter++;
		}
		die(1);
	}

	/*
	* room Admin columns
	*/
	function room_custom_columns($column){
	    global $post;
	    $custom = get_post_custom();
		$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );
		
		$full_image_id = get_post_thumbnail_id(($post->ID), 'fullimage'); 
		$full_image_url = wp_get_attachment_image_src($full_image_id,'fullimage');  
		if ( isset ($full_image_url[0]) ) {
			$full_image_url = $full_image_url[0];
		}

		if (!defined('MTHEME')) {
			$mtheme_shortname = "mtheme_p2";
			define('MTHEME', $mtheme_shortname);
		}

	    switch ($column)
	    {
	        case "room_image":
				if ( isset($image_url) && $image_url<>"" ) {
	            echo '<a class="thickbox" href="'.$full_image_url.'"><img src="'.$image_url.'" width="60px" height="60px" alt="featured" /></a>';
				}
	            break;
	        case "theme_description":
	            if ( isset($custom['pagemeta_thumbnail_desc'][0]) ) { echo $custom['pagemeta_thumbnail_desc'][0]; }
	            break;
	        case "video":
	            if ( isset($custom['pagemeta_lightbox_video'][0]) ) { echo $custom['pagemeta_lightbox_video'][0]; }
	            break;
	        case 'roomtypes':
	            echo get_the_term_list($post->ID, 'roomtypes', '', ', ','');
	            break;
	    } 
	}

	function room_edit_columns($columns){
	    $columns = array(
	        "cb" => "<input type=\"checkbox\" />",
	        "title" => __('Room Title','mthemelocal'),
	        "theme_description" => __('Description','mthemelocal'),
			"video" => __('Video','mthemelocal'),
	        "roomtypes" => __('roomtypes','mthemelocal'),
			"room_image" => __('Image','mthemelocal')
	    );
	 
	    return $columns;
	}
	
	/**
	 * Registers TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function init()
	{
		/*
		* Register Featured Post Manager
		*/
		//add_action('init', 'mtheme_featured_register');
		//add_action('init', 'room_register');//Always use a shortname like "mtheme_" not to see any 404 errors
		/*
		* Register room Post Manager
		*/
	    $mtheme_room_slug="rooms";
	    if (function_exists('superlens_get_option_data')) {
	    	$mtheme_room_slug = superlens_get_option_data('room_permalink_slug');
		}
	    if ( $mtheme_room_slug=="" || !isSet($mtheme_room_slug) ) {
	        $mtheme_room_slug="rooms";
	    }
	    $mtheme_room_singular_refer = "Rooms";
	    if (function_exists('superlens_get_option_data')) {
	    	$mtheme_room_singular_refer = superlens_get_option_data('room_archive_title');
		}
		if ( '' === $mtheme_room_singular_refer || empty($mtheme_room_singular_refer) ) {
			$mtheme_room_singular_refer = "Rooms";
		}
	    $args = array(
	        'label' => $mtheme_room_singular_refer,
	        'singular_label' => __('room','mthemelocal'),
	        'public' => true,
	        'show_ui' => true,
	        'capability_type' => 'post',
	        'hierarchical' => false,
	        'has_archive' =>true,
			'menu_position' => 6,
	    	'menu_icon' => plugin_dir_url( __FILE__ ) . 'images/portfolio.png',
	        'rewrite' => array('slug' => $mtheme_room_slug),//Use a slug like "work" or "project" that shouldnt be same with your page name
	        'supports' => array('title', 'author', 'thumbnail')//Boxes will be shown in the panel
	       );
	 
	    register_post_type( 'room' , $args );
		/*
		* Add Taxonomy for room 'Type'
		*/
		register_taxonomy('roomtypes', array("room"), array("hierarchical" => true, "label" => "Room Category", "singular_label" => "roomtypes", "rewrite" => true));
		 
		/*
		* Hooks for the room and Featured viewables
		*/
	}
	/**
	 * Enqueue Scripts and Styles
	 *
	 * @return	void
	 */
	function sort_admin_init()
	{
		if( is_admin() ) {
			// Load only if in a Post or Page Manager	
			if ('edit.php' == basename($_SERVER['PHP_SELF'])) {
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
				wp_enqueue_style( 'mtheme-room-sorter-CSS',  plugin_dir_url( __FILE__ ) . '/css/style.css', false, '1.0', 'all' );
				if ( isSet($_GET["page"]) ) {
					if ( $_GET["page"] == "class-imaginem-room-posts.php" ) {
						wp_enqueue_script("post-sorter-JS", plugin_dir_url( __FILE__ ) . "js/post-sorter.js", array( 'jquery' ), "1.1");
					}
				}
			}
		}
	}
    
}
$mtheme_room_post_type = new Imaginem_Room_Posts();


class mtheme_Roomcategory_add_image {

	function __construct() {
		add_action('admin_head', array(&$this,'mtheme_admin_head') );
		add_action('edit_term', array(&$this,'mtheme_save_tax_pic') );
		add_action('create_term', array(&$this,'mtheme_save_tax_pic') );
		add_filter("manage_edit-roomtypes_columns", array(&$this,'mtheme_roomtype_columns') );
		add_action("manage_roomtypes_custom_column", array(&$this,'mtheme_manage_workype_columns'),10,3 );
	}

	// Add to admin_init function
	 
	function mtheme_roomtype_columns($columns) {
	    $columns['roomtype_image'] = 'Image';
	    return $columns;
	}

	// Add to admin_init function
	 
	function mtheme_manage_workype_columns($value,$columns,$term_id) {
		$mtheme_roomtype_image_id = get_option('mtheme_roomtype_image_id' . $term_id);
	    switch ($columns) {
	        case 'roomtype_image':
	        		if ($mtheme_roomtype_image_id) {
	        			$mtheme_roomtype_image_url = wp_get_attachment_image_src( $mtheme_roomtype_image_id, 'thumbnail', false );
	            		$value = '<img src="'.$mtheme_roomtype_image_url[0].'" width="100px" height="auto" />';
	            	}
	            break;
	 
	        default:
	            break;
	    }
	    return $value;
	}

	function mtheme_admin_head() {
	    $taxonomies = get_taxonomies();
	    $taxonomies = array('roomtypes'); // uncomment and specify particular taxonomies you want to add image feature.
	    if (is_array($taxonomies)) {
	        foreach ($taxonomies as $z_taxonomy) {
	            add_action($z_taxonomy . '_add_form_fields', array(&$this,'mtheme_tax_field') );
	            add_action($z_taxonomy . '_edit_form_fields', array(&$this,'mtheme_tax_field') );
	        }
	    }
	}

	// add image field in add form
	function mtheme_tax_field($taxonomy) {
	    wp_enqueue_style('thickbox');
	    wp_enqueue_script('thickbox');
		wp_enqueue_media();

	    if(empty($taxonomy)) {
	        echo '<div class="form-field">
	                <label for="mtheme_roomtype_input">Image</label>
	                <input size="40" type="text" name="mtheme_roomtype_input" id="mtheme_roomtype_input" value="" />
	                <input type="text" name="mtheme_roomtype_image_id" id="mtheme_roomtype_image_id" value="" />
	            </div>';
	    }
	    else{
	    	
	    	$mtheme_roomtype_input_url='';
	    	$mtheme_roomtype_image_id='';

	        if ( isSet($taxonomy->term_id) ) {
	        	//$mtheme_roomtype_input_url = get_option('mtheme_roomtype_input' . $taxonomy->term_id);
	        	$mtheme_roomtype_image_id = get_option('mtheme_roomtype_image_id' . $taxonomy->term_id);
	        }
	        
	        echo '<tr class="form-field">
			<th scope="row" valign="top"><label for="mtheme_roomtype_input">Image</label></th>
			<td>
			<input type="hidden" name="mtheme_roomtype_image_id" id="mtheme_roomtype_image_id" value="' . $mtheme_roomtype_image_id . '" />
			<a class="button" id="mtheme_upload_work_image">Set category image</a>
			<div class="inside" id="featured_roomtype_image_wrap">';
	        if(!empty($mtheme_roomtype_image_id)) {
	            $mtheme_roomtype_image_url = wp_get_attachment_image_src( $mtheme_roomtype_image_id, 'thumbnail', false );
	            echo '<img id="featured_roomtype_image" src="'.$mtheme_roomtype_image_url[0].'" style="max-width:200px;border: 1px solid #ccc;padding: 5px;box-shadow: 5px 5px 10px #ccc;margin-top: 10px;" >';
	            echo '<a style="display:block;" id="remove_roomtype_image" href="#">Remove category Image</a>';
	        }
	        echo '</div>';
	        echo '</td></tr><br/>';
	    }
	?>
	<script>
	jQuery(document).ready(function($){
		// Get input target field
		var targetfield="mtheme_roomtype_input";

		jQuery("#mtheme_upload_work_image").click( function( event ) {
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
				var active_image = jQuery('#featured_roomtype_image');

				if (active_image.length > 0 ) {
					$(active_image).attr('src', attachment.attributes.sizes.thumbnail.url);
				} else {
	  				var roomtypeImg = jQuery('<img/>');
	  					roomtypeImg.attr('id','featured_roomtype_image')
						roomtypeImg.attr('src', attachment.attributes.sizes.thumbnail.url);
						roomtypeImg.attr('style',"max-width:200px;border: 1px solid #ccc;padding: 5px;box-shadow: 5px 5px 10px #ccc;margin-top: 10px;")
						roomtypeImg.appendTo('#featured_roomtype_image_wrap');

					jQuery( '<a style="display:block;" id="remove_roomtype_image" href="#">Remove roomtype Image</a>' ).appendTo( "#featured_roomtype_image_wrap" );
				}
				
				jQuery("#mtheme_roomtype_image_id").val(attachment.id);
			});

			custom_file_frame.open();
		});

		jQuery("#featured_roomtype_image_wrap").on("click", "#remove_roomtype_image", function(){
			jQuery('#remove_roomtype_image,#featured_roomtype_image').remove();
			jQuery('#mtheme_roomtype_image_id').val("");
			return false;
		});
	});
	</script>
	<?php
	}

	// save our taxonomy image while edit or save term
	function mtheme_save_tax_pic($term_id) {
	    if (isset($_POST['mtheme_roomtype_image_id'])) {
	    	update_option('mtheme_roomtype_image_id' . $term_id, $_POST['mtheme_roomtype_image_id']);
	    }
	}

	// output taxonomy image url for the given term_id (NULL by default)
	function mtheme_roomtype_input_url($term_id = NULL) {
	    if ($term_id) {
	        $current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	        return get_option('mtheme_roomtype_input' . $current_term->term_id);
	    }
	}

}
$mtheme_Roomcategory_add_image = new mtheme_Roomcategory_add_image();
?>