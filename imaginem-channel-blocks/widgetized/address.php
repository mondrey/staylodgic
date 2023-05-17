<?php
class mTheme_Address_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'mtheme_contact_widget', 'description' => __( 'Display your contact details', 'mthemelocal') );
		parent::__construct('contact_details',__('superlens Address', 'mthemelocal'), $widget_ops);
		
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', empty($instance['title']) ? __('', 'mthemelocal') : $instance['title'], $instance, $this->id_base);
		$text = $instance['text'];
		$phone = $instance['phone'];
		$phone_link = '';
		if (isSet($instance['phone_link'])) {
			$phone_link = $instance['phone_link'];
		}
		$cellphone = $instance['cellphone'];
		$cellphone_link = '';
		if (isSet($instance['cellphone_link'])) {
			$cellphone_link = $instance['cellphone_link'];
		}
		$email= $instance['email'];
		$address = $instance['address'];
		$city = $instance['city'];
		$state = $instance['state'];
		$zip = $instance['zip'];
		$name = $instance['name'];
		
		if(!empty($city) && !empty($state)){ 
			$city = $city.',&nbsp;'.$state;
		}elseif(!empty($state)){
			$city = $state;
		}

		$phone_link_start = '';
		$phone_link_end = '';
		if( isSet($phone_link) && $phone_link<>"" ){ 
			$phone_link_start = '<a href="'.esc_url($phone_link).'">';
			$phone_link_end = '</a>';
		}		
		
		$cellphone_link_start = '';
		$cellphone_link_end = '';
		if( isSet($cellphone_link) && $cellphone_link<>"" ){ 
			$cellphone_link_start = '<a href="'.esc_url($cellphone_link).'">';
			$cellphone_link_end = '</a>';
		}		
		
		
		echo $before_widget;
		if ( $title)
			echo $before_title . $title . $after_title;
		
		?>
			<ul class="contact_address_block">
			<?php if(!empty($text)):?><li class="about_info"><?php echo $text;?></li><?php endif;?>
			<?php if(!empty($name)):?><li><span class="contact_name"><?php echo $name;?></span></li><?php endif;?>
			<?php if(!empty($address)):?><li><span class="contact_address"><?php echo $address;?></span></li><?php endif;?>
			<?php
			if( !empty($city) && !empty($zip) ) {
			?>
			<li>
			<?php if(!empty($city)):?><span class="contact_city"><?php echo $city;?></span><?php endif;?>
			<?php if(!empty($zip)):?><span class="contact_zip"><?php echo $zip;?></span><?php endif;?>
			</li>
			<?php
			}
			?>
			<?php if(!empty($phone)):?><li class="address-widget-has-icon"><span class="contact_phone"><?php echo $phone_link_start . $phone . $phone_link_end;?></span></li><?php endif;?>
			<?php if(!empty($cellphone)):?><li class="address-widget-has-icon"><span class="contact_mobile"><?php echo $cellphone_link_start . $cellphone . $cellphone_link_end;?></span></li><?php endif;?>
			<?php if(!empty($email)):?><li class="address-widget-has-icon"><span class="contact_email"><a href="mailto:<?php echo antispambot($email);?>"><?php echo antispambot($email);?></a></span></li><?php endif;?>
			</ul>
		<?php
		echo $after_widget;

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['phone'] = strip_tags($new_instance['phone']);
		$instance['phone_link'] = strip_tags($new_instance['phone_link']);
		$instance['cellphone'] = strip_tags($new_instance['cellphone']);
		$instance['cellphone_link'] = strip_tags($new_instance['cellphone_link']);
		$instance['email'] = strip_tags($new_instance['email']);
		$instance['address'] = strip_tags($new_instance['address']);
		$instance['city'] = strip_tags($new_instance['city']);
		$instance['state'] = strip_tags($new_instance['state']);
		$instance['zip'] = strip_tags($new_instance['zip']);
		$instance['name'] = strip_tags($new_instance['name']);
		

		return $instance;
	}

	public function form( $instance ) {
		//Defaults
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$text = isset($instance['text']) ? esc_attr($instance['text']) : '';
		$phone = isset($instance['phone']) ? esc_attr($instance['phone']) : '';
		$phone_link = isset($instance['phone_link']) ? esc_attr($instance['phone_link']) : '';
		$cellphone = isset($instance['cellphone']) ? esc_attr($instance['cellphone']) : '';
		$cellphone_link = isset($instance['cellphone_link']) ? esc_attr($instance['cellphone_link']) : '';
		$email = isset($instance['email']) ? esc_attr($instance['email']) : '';
		$address = isset($instance['address']) ? esc_attr($instance['address']) : '';
		$city = isset($instance['city']) ? esc_attr($instance['city']) : '';
		$state = isset($instance['state']) ? esc_attr($instance['state']) : '';
		$zip = isset($instance['zip']) ? esc_attr($instance['zip']) : '';
		$name = isset($instance['name']) ? esc_attr($instance['name']) : '';
	?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Intro text:', 'mthemelocal'); ?></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" ><?php echo esc_textarea($text); ?></textarea></p>
		
		<p><label for="<?php echo $this->get_field_id('name'); ?>"><?php _e('Company:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo esc_attr($name); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('address'); ?>"><?php _e('Address:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('city'); ?>"><?php _e('City:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" value="<?php echo esc_attr($city); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('state'); ?>"><?php _e('State:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('state'); ?>" name="<?php echo $this->get_field_name('state'); ?>" type="text" value="<?php echo esc_attr($state); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('zip'); ?>"><?php _e('Zip:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('zip'); ?>" name="<?php echo $this->get_field_name('zip'); ?>" type="text" value="<?php echo esc_attr($zip); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('Phone:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php echo esc_attr($phone); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('phone_link'); ?>"><?php _e('Phone Link:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('phone_link'); ?>" name="<?php echo $this->get_field_name('phone_link'); ?>" type="text" value="<?php echo esc_attr($phone_link); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('cellphone'); ?>"><?php _e('Cell phone:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('cellphone'); ?>" name="<?php echo $this->get_field_name('cellphone'); ?>" type="text" value="<?php echo esc_attr($cellphone); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('cellphone_link'); ?>"><?php _e('Cell phone link:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('cellphone_link'); ?>" name="<?php echo $this->get_field_name('cellphone_link'); ?>" type="text" value="<?php echo esc_attr($cellphone_link); ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('e-mail:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>" /></p>
		
<?php
	}

}
function mTheme_Address_Widget_register_widgets() {
	register_widget( 'mTheme_Address_Widget' );
}
add_action( 'widgets_init', 'mTheme_Address_Widget_register_widgets' );