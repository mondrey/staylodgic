<?php
class mtheme_aboutme_widget extends WP_Widget {
 
	/**
	 * Register widget with WordPress.
	 */
public function __construct() {
		parent::__construct(
	 		'mtheme_aboutme_widget', // Base ID
			__('superlens About me Widget'), // Name
			array( 'description' => __( 'Theme widget for About me information', 'mtheme-local' ), ) // Args
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
		$desc = apply_filters( 'widget_desc', $instance['desc'] );
		$button_text = apply_filters( 'widget_button_text', $instance['button_text'] );
		$button_url = apply_filters( 'widget_button_url', $instance['button_url'] );
		$image_id = apply_filters( 'widget_image_id', $instance['image_id'] );
		echo $before_widget;
		if ( isSet($instance['image_id'])) {
		if ( $instance['image_id']<>"" ) {

		?>
		<div class="aboutme-wrap">
			<h3><?php echo $name; ?></h3>
			<div class="lazyload-wrapper aboutme-inner-wrap margin-space-below">
					<?php
					$image_uri   = wp_get_attachment_image_src( $image_id, 'full', false );
					$image_uri   = $image_uri[0];
					$srcset      = wp_get_attachment_image_srcset( $image_id, 'full' );
					$srcsetsizes = wp_get_attachment_image_sizes( $image_id, 'full' );
					$img_alt     = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					?>
        		<img class="aboutme-image lazyload" src="<?php echo get_template_directory_uri(); ?>/images/placeholders/placeholder-750x750.gif" data-src="<?php echo esc_url($image_uri); ?>" data-srcset="<?php echo esc_attr($srcset); ?>" sizes="<?php echo esc_attr($srcsetsizes); ?>" alt="<?php echo esc_attr($img_alt); ?>" />
					
					<div class="aboutme-desc-wrap">
        	<div class="aboutme-desc">
        		<?php echo $desc; ?>
        	</div>
        	<?php
        	if ( isSet($button_text) && $button_text<>"" ) {
        	?>
        	<div class="aboutme-button">
        		<a href="<?php echo esc_url($button_url); ?>"><div class="mtheme-button"><?php echo $button_text; ?></div></a>
        	</div>
					</div>
					</div>
        	<?php
        	}
        	?>
        </div>
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
		$instance['desc'] = ( ! empty( $new_instance['desc'] ) ) ? strip_tags( $new_instance['desc'] ) : '';
		$instance['button_text'] = ( ! empty( $new_instance['button_text'] ) ) ? strip_tags( $new_instance['button_text'] ) : '';
		$instance['button_url'] = ( ! empty( $new_instance['button_url'] ) ) ? strip_tags( $new_instance['button_url'] ) : '';
		$instance['image_id'] = ( ! empty( $new_instance['image_id'] ) ) ? strip_tags( $new_instance['image_id'] ) : '';
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
        if ( isset( $instance[ 'image_id' ] ) ) {
			$image_id = $instance[ 'image_id' ];
		}
		else {
			$image_id = '';
		}

		if ( isset( $instance[ 'desc' ] ) ) {
			$desc = $instance[ 'desc' ];
		} else {
			$desc = '';
		}
		if ( isset( $instance[ 'button_text' ] ) ) {
			$button_text = $instance[ 'button_text' ];
		} else {
			$button_text = '';
		}
		if ( isset( $instance[ 'name' ] ) ) {
			$name = $instance[ 'name' ];
		} else {
			$name = '';
		}
		if ( isset( $instance[ 'button_url' ] ) ) {
			$button_url = $instance[ 'button_url' ];
		} else {
			$button_url = '';
		}
		?>
    <div class="mtheme-image-uploader-widget">
        <p><label for="<?php echo $this->get_field_id('image_id'); ?>">Image</label><br /><br />
      	<?php
      	if ($image_id<>"") {
					$image_uri = wp_get_attachment_image_src( $image_id, 'thumbnail', false );
					$image_uri = $image_uri[0];
      	?>
        <img class="custom_media_image aboutme_media_image" src="<?php echo esc_url($image_uri); ?>" style="background:#eee;margin:0 0 20px 0;padding:0;max-width:100px;display:block" />
        <?php
    	}
    	?>
        <input type="hidden" class="widefat custom_media_url aboutme_media_id" name="<?php echo $this->get_field_name('image_id'); ?>" id="<?php echo $this->get_field_id('image_id'); ?>" value="<?php echo esc_attr($image_id); ?>">
        <input type="button" value="<?php _e( 'Upload Image', 'mtheme_local' ); ?>" class="button aboutme_media_upload custom_media_upload" id="custom_image_uploader"/>
        </p>
    	
    	<p><label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Name', 'mtheme_local'); ?><br /></label>
      	<input type="text" name="<?php echo $this->get_field_name('name'); ?>" id="<?php echo $this->get_field_id('name'); ?>" value="<?php echo $name; ?>" class="widefat" />
        </p>

    	<p><label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Description', 'mtheme_local'); ?><br /></label>
      	<textarea class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="text" ><?php echo $desc; ?></textarea>
        </p>
    	<p><label for="<?php echo $this->get_field_id('button_text'); ?>"><?php _e('Button Text', 'mtheme_local'); ?><br /></label>
      	<input type="text" name="<?php echo $this->get_field_name('button_text'); ?>" id="<?php echo $this->get_field_id('button_text'); ?>" value="<?php echo $button_text; ?>" class="widefat" />
        </p>
    	<p><label for="<?php echo $this->get_field_id('button_url'); ?>"><?php _e('Button URL', 'mtheme_local'); ?><br /></label>
      	<input type="text" name="<?php echo $this->get_field_name('button_url'); ?>" id="<?php echo $this->get_field_id('button_url'); ?>" value="<?php echo esc_url($button_url); ?>" class="widefat" />
        </p>
    </div>
		<?php 
	}
	
}
function mtheme_aboutme_widget_register_widgets() {
	register_widget( 'mtheme_aboutme_widget' );
}
add_action( 'widgets_init', 'mtheme_aboutme_widget_register_widgets' );
function mtheme_aboutme_script(){
	wp_enqueue_script('jquery');
	// This will enqueue the Media Uploader script
	wp_enqueue_media();
	wp_enqueue_script('about-me-widget', plugins_url( '/js/about-me-widget.js' , __FILE__ ));
}
add_action('admin_enqueue_scripts', 'mtheme_aboutme_script');