<?php
class mTheme_Events_List_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'mtheme_events_widget', 'description' => __( 'Displays list of event posts', 'mthemelocal') );
		parent::__construct('events_list',__('superlens Events List', 'mthemelocal'), $widget_ops);
		
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Events', 'mthemelocal') : $instance['title'], $instance, $this->id_base);
		$text = $instance['text'];
		$limit = '-1';
		if (isSet($instance['limit'])) {
			$limit = $instance['limit'];
		}
		
		echo $before_widget;		
		?>
<?php
	//echo $tag->slug;
	if (!isSet($limit) && $limit=="") {
		$limit="-1";
	}
    $args=array(
      'post_type' => 'events',
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'showposts'=>$limit,
      'ignore_sticky_posts'=>1
    );
    $r = null;
    $r = new WP_Query($args);
	
    if( $r->have_posts() ) :
	
		if ( $title) echo $before_title . $title . $after_title;
		if(!empty($text)):?><p class="portfoliorelated_widget_about"><?php echo $text;?></p><?php endif;		
		?>
		<div class="widget-portfolio-gallery">
		<ul class="infobar-portfoliogrid">
<?php while ($r->have_posts()) : $r->the_post();
	
	$custom = get_post_custom(get_the_ID());
	$description="";
	if ( isset($custom['pagemeta_thumbnail_desc'][0]) ) {
		$description=$custom['pagemeta_thumbnail_desc'][0];
		$description = superlens_trim_sentence($description,60);
	}
?>
			<li class="rightspace imageicon lazyload-wrapper">

				
<?php if (has_post_thumbnail() ): ?>
		<a class="recent_thumbnail" href="<?php echo get_permalink() ?>">
		<?php
		// Show Image
		$image_id = get_post_thumbnail_id(get_the_id(), "superlens-gridblock-tiny"); 
		$image_url = wp_get_attachment_image_src($image_id,"superlens-gridblock-tiny");  
		$image_url = $image_url[0];

		$img_obj = get_post($image_id);
		$img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
	
		echo '<img src="'. esc_url( $image_url ) .'" alt="'. esc_attr( get_the_title() ) .'" />';
		?>

				</a>
<?php endif;//disable_thumbnail ?>
			</li>
<?php endwhile; ?>
		</ul>
		<div class="clear"></div>
		</div>
<?php
endif;
//$post = $backup;  // copy it back
wp_reset_query(); // to use the original query again
?>
		
		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['limit'] = strip_tags($new_instance['limit']);
		

		return $instance;
	}

	public function form( $instance ) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$text = isset($instance['text']) ? esc_attr($instance['text']) : '';
		$limit = isset($instance['limit']) ? esc_attr($instance['limit']) : '';
	?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Intro text:', 'mthemelocal'); ?></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" ><?php echo $text; ?></textarea></p>

		<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit ( -1 for all ):', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" /></p>
		
<?php
	}

}
function mTheme_Events_List_Widget_register_widgets() {
	register_widget( 'mTheme_Events_List_Widget' );
}
add_action( 'widgets_init', 'mTheme_Events_List_Widget_register_widgets' );