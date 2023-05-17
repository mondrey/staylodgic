<?php
class Imaginem_Fullscreen_Posts {

    function __construct() 
    {
		add_action('init', array( $this, 'init'));
        add_action('admin_init', array( $this, 'sort_admin_init'));
        add_filter("manage_edit-fullscreen_columns", array( $this, 'mtheme_fullscreen_edit_columns'));
		add_action("manage_posts_custom_column",  array( $this, 'mtheme_fullscreen_custom_columns'));
		add_action('admin_menu', array( $this, 'mtheme_enable_fullscreen_sort') );
		add_action('wp_ajax_fullscreen_sort', array( $this, 'mtheme_save_fullscreen_order'));

		if( is_admin() ) {
			if ( isSet($_GET["page"]) ) {
				if ( $_GET["page"] == "class-imaginem-fullscreen-posts.php" ) {
					add_filter( 'posts_orderby', array( $this, 'mtheme_fullscreen_orderby'));
				}
			}
		}
	}

	function mtheme_enable_fullscreen_sort() {
	    add_submenu_page('edit.php?post_type=fullscreen', 'Sort Fullscreen', 'Sort Fullscreen', 'edit_posts', basename(__FILE__), array( $this, 'mtheme_sort_fullscreen'));
	}
	function mtheme_fullscreen_orderby($orderby){
		global $wpdb;
		$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
		return($orderby);
	}
	/**
	 * Display Sort admin
	 *
	 * @return void
	 * @author Soul
	 **/
	function mtheme_sort_fullscreen() {
		$gallery = new WP_Query('post_type=fullscreen&posts_per_page=-1&orderby=menu_order&order=ASC');
	?>
		<div class="wrap">
		<h2>Sort Fullscreen<img src="<?php echo home_url(); ?>/wp-admin/images/loading.gif" id="loading-animation" /></h2>
		<div class="description">
		Drag and Drop the slides to order them
		</div>
		<ul id="portfolio-list">
		<?php while ( $gallery->have_posts() ) : $gallery->the_post(); ?>
			<li id="<?php the_id(); ?>">
			<div>
			<?php 
			$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id() );
			$custom = get_post_custom(get_the_ID());
			$gallery_cats = get_the_terms( get_the_ID(), 'fullscreengallery' );
			
			?>
			<?php if ($image_url) { echo '<img class="mtheme_admin_sort_image" src="'.$image_url.'" width="30px" height="30px" alt="" />'; } ?>
			<span class="mtheme_admin_sort_title"><?php the_title(); ?></span>
			<?php
			if ($gallery_cats) {
			?>
			<span class="mtheme_admin_sort_categories"><?php foreach ($gallery_cats as $taxonomy) { echo ' | ' . $taxonomy->name; } ?></span>
			<?php
			}
			?>
			</div>

			</li>
		<?php endwhile; ?>
		</div><!-- End div#wrap //-->
	 
	<?php
	}

	/**
	 * Upadate the gallery Sort order
	 *
	 * @return void
	 * @author Soul
	 **/
	function mtheme_save_fullscreen_order() {
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
	* Portfolio Admin columns
	*/
	function mtheme_fullscreen_custom_columns($column){
	    global $post;
	    $custom = get_post_custom();
		$image_url=wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );
		
		$full_image_id = get_post_thumbnail_id(($post->ID), 'thumbnail'); 
		$full_image_url = wp_get_attachment_image_src($full_image_id,'full');  
		if ( isset ($full_image_url[0]) ) {
			$full_image_url = $full_image_url[0];
		}

		$mtheme_shortname = "sceneone";
	    switch ($column)
	    {
	        case "featured_image":
	            if ( isSet($image_url) && $image_url<>"" ) {
	            echo '<a class="thickbox" href="'.$full_image_url.'"><img src="'.$image_url.'" width="40px" height="40px" alt="featured" /></a>';
	            }
	            break;
	        case "fullscreen_type":
	            if ( isset($custom["pagemeta_fullscreen_type"][0]) ) { echo $custom["pagemeta_fullscreen_type"][0]; }
	            break;
	        case "fullscreengallery":
	            echo get_the_term_list($post->ID, 'fullscreengallery', '', ', ','');
	            break;
	    }
	}

	function mtheme_fullscreen_edit_columns($columns){
	    $columns = array(
	        "cb" => "<input type=\"checkbox\" />",
	        "title" => __('Featured Title','mthemelocal'),
	        "fullscreengallery" => __('Categories','mthemelocal'),
	        "fullscreen_type" => __('Fullscreen Type','mthemelocal'),
	        "featured_image" => __('Image','mthemelocal')
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

	    $mtheme_fullscreen_slug="fullscreen";
	    if (function_exists('superlens_get_option_data')) {
	    	$mtheme_fullscreen_slug = superlens_get_option_data('fullscreen_permalink_slug');
		}
	    if ( $mtheme_fullscreen_slug=="" || !isSet($mtheme_fullscreen_slug) ) {
	        $mtheme_fullscreen_slug="fullscreen";
	    }
	    $args = array(
	        'label' => __('Fullscreen Pages','mthemelocal'),
	        'description' => __('Manage your Fullscreen posts','mthemelocal'),
	        'singular_label' => __('Fullscreen','mthemelocal'),
	        'public' => true,
	        'show_ui' => true,
	        'capability_type' => 'post',
	        'hierarchical' => false,
	        'menu_position' => 5,
	        'menu_icon' => plugin_dir_url( __FILE__ ) . 'images/fullscreen.png',
	        'rewrite' => array('slug' => $mtheme_fullscreen_slug),//Use a slug like "work" or "project" that shouldnt be same with your page name
	        'supports' => array('title', 'author', 'thumbnail','revisions')//Boxes will be shown in the panel
	       );
	 
	    register_post_type( 'fullscreen' , $args );
	    register_taxonomy("fullscreengallery", array("fullscreen"), array("hierarchical" => true, "label" => "Fullscreen Categories", "singular_label" => "Fullscreen Category", "rewrite" => true));
		 
		/*
		* Hooks for the Portfolio and Featured viewables
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
				//wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('thickbox');
				wp_enqueue_style('thickbox');
				wp_enqueue_style( 'mtheme-fullscreen-sorter-CSS',  plugin_dir_url( __FILE__ ) . '/css/style.css', false, '1.0', 'all' );
				if ( isSet($_GET["page"]) ) {
					if ( $_GET["page"] == "class-imaginem-fullscreen-posts.php" ) {
						wp_enqueue_script("post-sorter-JS", plugin_dir_url( __FILE__ ) . "js/post-sorter.js", array( 'jquery' ), "1.0");
					}
				}
			}
		}
	}
    
}
$mtheme_fullscreen_post_type = new Imaginem_Fullscreen_Posts();
?>