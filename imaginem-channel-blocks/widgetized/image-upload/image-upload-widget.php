<?php
class mtheme_image_widget extends WP_Widget {
 
	/**
	 * Register widget with WordPress.
	 */
public function __construct() {
		parent::__construct(
	 		'mtheme_image_widget', // Base ID
			__('superlens Image Upload Widget'), // Name
			array( 'description' => __( 'Theme widget to upload image', 'mtheme-local' ), ) // Args
		);
	}
 
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$name = apply_filters( 'widget_name', $instance['name'] );
		$width = apply_filters( 'widget_width', $instance['width'] );
		$image_uri = apply_filters( 'widget_image_uri', $instance['image_uri'] );
		echo $before_widget;
		if ( isSet($instance['image_uri'])) {
		if ( $instance['image_uri']<>"" ) {

		?>
        	<img class="footer-mtheme-image" width="<?php echo $width; ?>" src="<?php echo esc_url($instance['image_uri']); ?>" alt="logo" />
    <?php
		}
    	}
		echo $after_widget;
	}
 
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['name'] = ( ! empty( $new_instance['name'] ) ) ? strip_tags( $new_instance['name'] ) : '';
		$instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['image_uri'] = ( ! empty( $new_instance['image_uri'] ) ) ? strip_tags( $new_instance['image_uri'] ) : '';
		return $instance;
	}
 
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
        if ( isset( $instance[ 'image_uri' ] ) ) {
			$image_uri = $instance[ 'image_uri' ];
		}
		else {
			$image_uri = __( '', 'iuw' );
		}

		if ( isset( $instance[ 'width' ] ) ) {
			$width = $instance[ 'width' ];
		} else {
			$width='260';
		}
		?>
    <div class="mtheme-image-uploader-widget">
    	<p><label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', 'iuw'); ?><br /></label>
      	<input type="text" name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('width'); ?>" value="<?php echo $width; ?>" class="widefat" style="width:60px" /> pixels
        </p>

        <p><label for="<?php echo $this->get_field_id('image_uri'); ?>">Image</label><br />
      	<?php
      	if ( isset( $image_uri) && $image_uri<>"" ) {
      	?>
        <img class="custom_media_image" src="<?php echo $image_uri; ?>" style="background:#eee;margin:0 0 20px 0;padding:0;max-width:100px;float:left;display:inline-block" />
        <?php
    	}
    	?>
        </p>
        <input type="text" class="widefat custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo $image_uri; ?>"><br /><br />
        <input type="button" value="<?php _e( 'Upload Image', 'iuw' ); ?>" class="button custom_media_upload" id="custom_image_uploader"/><br /><br />
    </div>
		<?php 
	}
	
}
function mtheme_image_widget_register_widgets() {
	register_widget( 'mtheme_image_widget' );
}
add_action( 'widgets_init', 'mtheme_image_widget_register_widgets' );
function mtheme_image_script(){
	wp_enqueue_script('jquery');
	// This will enqueue the Media Uploader script
	wp_enqueue_media();
	wp_enqueue_script('adsScript', plugins_url( '/js/image-upload-widget.js' , __FILE__ ));
}
add_action('admin_enqueue_scripts', 'mtheme_image_script');