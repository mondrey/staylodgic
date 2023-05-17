<?php
class mTheme_Instagram_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'mtheme_instagram_widget', 'description' => __( 'Instagram Embed Widget', 'mthemelocal') );
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('instagram_details',__('superlens Instagram', 'mthemelocal'), $widget_ops,$control_ops);
		
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('', 'mthemelocal') : $instance['title'], $instance, $this->id_base);
		
		echo $before_widget;
		if ( $title) {
			echo $before_title . $title . $after_title;
		}
		
		$columns = 1;
		$token = superlens_get_option_data('insta_token');
		if ( isSet($token) ) {
			if ( shortcode_exists('insta_carousel') ) {
				$insta_image_limit = superlens_get_option_data('insta_image_limit');
				if ( !isSet($insta_image_limit) || $insta_image_limit==0 ) {
					$insta_image_limit = 6;
				}
				echo '<div class="instagram-block-wrap clearfix">';
						$insta_username = superlens_get_option_data('insta_username');
						if ($insta_username<>"") {
						//echo '<h3 class="instagram-username"><a href="https://instagram.com/'. esc_attr($insta_username).'"><i class="fa fa-instagram"></i>'.esc_html($insta_username).'</a></h3>';
						}
					$columns = 6;
					$token = superlens_get_option_data('insta_token');
					if ( isSet($token) ) {
						if ( shortcode_exists('insta_carousel') ) {
							$insta_image_limit = superlens_get_option_data('insta_image_limit');
							if ( !isSet($insta_image_limit) || $insta_image_limit==0 ) {
								$insta_image_limit = 6;
							}
							echo do_shortcode('[insta_carousel count="'.$insta_image_limit.'" columns="'.$columns.'" token="'.$token.'"]');
						}
					}
				echo '</div>';
			}
		}

		echo $after_widget;

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		

		return $instance;
	}

	public function form( $instance ) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$text = isset($instance['text']) ? esc_attr($instance['text']) : '';
	?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		
<?php
	}

}
function mTheme_Instagram_Widget_register_widgets() {
	register_widget( 'mTheme_Instagram_Widget' );
}
add_action( 'widgets_init', 'mTheme_Instagram_Widget_register_widgets' );