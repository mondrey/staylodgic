<?php
class mtheme_Social_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 'classname' => 'MSocial_Widget', 'description' => __('Generate social links', 'mthemelocal') );
		$control_ops = array('width' => 600, 'height' => 350);
		parent::__construct( 'msocial-widget', __('superlens Social Widget', 'mthemelocal'), $widget_ops,$control_ops);
	}

	public function widget( $args, $instance ) {
		extract( $args );
	
		$title = apply_filters('widget_title', $instance['title'] );
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		$imgcaption = $instance['imgcaption'];
		$call_us = $instance['call_us'];
		$call_us_link = $instance['call_us_link'];	
		$address = $instance['address'];	

		$icon_size = $instance['icon_size'];
		$animation = $instance['animation'];
		$icon_opacity = $instance['icon_opacity'];
		$newtab = $instance['newtab'];
		$nofollow = $instance['nofollow'];
		$alignment = $instance['alignment'];
		
		$socials = array(
			'custom1name' => 'RSS Link',
			'custom1icon' => 'fas fa-rss',
			'custom2name' => 'Facebook',
			'custom2icon' => 'fab fa-facebook-f',
			'custom3name' => 'Twitter',
			'custom3icon' => 'fab fa-twitter',
			'custom4name' => 'Tripadvisor',
			'custom4icon' => 'fab fa-tripadvisor',
			'custom5name' => 'Dribbble',
			'custom5icon' => 'fab fa-dribbble',
			'custom6name' => 'Google+',
			'custom6icon' => 'fab fa-google-plus',
			'custom7name' => 'LinkedIn',
			'custom7icon' => 'fab fa-linkedin',
			'custom8name' => 'Tumblr',
			'custom8icon' => 'fab fa-tumblr',
			'custom9name' => 'Youtube',
			'custom9icon' => 'fab fa-youtube',
			'custom10name' => 'Pinterest',
			'custom10icon' => 'fab fa-pinterest',
			'custom11name' => 'Flickr',
			'custom11icon' => 'fab fa-flickr',
			'custom12name' => 'Skype',
			'custom12icon' => 'fab fa-skype',
			'custom13name' => 'Instagram',
			'custom13icon' => 'fab fa-instagram',
			'custom14name' => 'Behance',
			'custom14icon' => 'fab fa-behance',
			'custom15name' => 'Vimeo',
			'custom15icon' => 'fab fa-vimeo-square',
			'custom16name' => 'Vine',
			'custom16icon' => 'fab fa-vine',
			'custom17name' => '500px',
			'custom17icon' => 'fab fa-500px',
			'custom18name' => 'VK',
			'custom18icon' => 'fab fa-vk',
			'custom19name' => 'e-Mail',
			'custom19icon' => 'fa fa-envelope',
			'custom20name' => 'Custom icon 1',
			'custom20icon' => '',
			'custom21name' => 'Custom icon 2',
			'custom21icon' => '',
			'custom22name' => 'Custom icon 3',
			'custom22icon' => '',
			'custom23name' => 'Custom icon 4',
			'custom23icon' => '',
			'custom24name' => 'Custom icon 5',
			'custom24icon' => '',
			'custom25name' => 'Custom icon 6',
			'custom25icon' => '');

		for ($social_count=1; $social_count <=19; $social_count++ ) {
			$customicon[$social_count] = $socials['custom'.$social_count.'icon'];
			$customname[$social_count] = $socials['custom'.$social_count.'name'];
			if ( isset($instance['custom'.$social_count.'url']) ) {
				$customurl[$social_count] = $instance['custom'.$social_count.'url'];
			}
		}
		for ($social_count=20; $social_count <=25; $social_count++ ) {
			if ( isset( $instance['custom'.$social_count.'icon'] ) && ! empty( $instance['custom'.$social_count.'icon'] ) ) {
				$customicon[$social_count] = $instance['custom'.$social_count.'icon'];
			} else {
				$customicon[$social_count] = '';
			}
			$customname[$social_count] = $socials['custom'.$social_count.'name'];
			if ( isset($instance['custom'.$social_count.'url']) ) {
				$customurl[$social_count] = $instance['custom'.$social_count.'url'];
			}
		}
	
		if($icon_size == 'default') {
			$icon_size = '16';
		}
		
		$icon_opacity = '0.9';
		$icon_ie = $icon_opacity * 100;
		$nofollow = '';
		if ($newtab == 'yes') {
			$newtab = "target=\"_blank\"";
			} else {
			$newtab = '';
			}
		$alignment = '';

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
		
		echo '<div class="social-header-wrap">';
		echo '<ul>';
		for ($social_count=1; $social_count <=25; $social_count++ ) {
			// Custom Icon 1
			if ( isset($customurl[$social_count]) && $customurl[$social_count] != '' && $customname[$social_count] != '' && $customicon[$social_count] != '' ) {
				?>
				<li class="social-icon">
				<a aria-label="<?php echo esc_attr($customname[$social_count]); ?>" href="<?php echo esc_url($customurl[$social_count]); ?>" <?php echo $nofollow; ?> <?php echo $newtab; ?>>
					<i class="<?php echo esc_attr($customicon[$social_count]); ?>"></i>
				</a>
				</li>
				<?php
			} else {
				echo ''; //If no URL inputed
			}
		
		}


		// Address
		if ( $address != '' && $address != ' ' ) {
			?>
			<li class="address-text">
			<i class="fa fa-map"></i><?php echo $address; ?>
			</li>
			<?php
		}			
		// Call us
		if ( $call_us != '' && $call_us != ' ' ) {
			?>
			<li class="contact-text">
			<?php if ($call_us_link<>"") { echo '<a href="'.$call_us_link.'">'; }?>
			<i class="fa fa-phone-square"></i><span class="contact-number"><?php echo $call_us; ?></span>
			<?php if ($call_us_link<>"") { echo '</a>'; }?>
			</li>
			<?php
		}
		echo '</ul>';
		echo '</div>';
	
		/* After widget (defined by themes). */
		
		echo $after_widget;
	}

	/* Update the widget settings  */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip Tags For Text Boxes */
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['imgcaption'] = $new_instance['imgcaption'];
		$instance['icon_size'] = $new_instance['icon_size'];
		$instance['animation'] = $new_instance['animation'];
		$instance['icon_opacity'] = $new_instance['icon_opacity'];
		$instance['newtab'] = $new_instance['newtab'];
		$instance['nofollow'] = $new_instance['nofollow'];
		$instance['alignment'] = $new_instance['alignment'];
		$instance['call_us'] = strip_tags( $new_instance['call_us'] );
		$instance['call_us_link'] = strip_tags( $new_instance['call_us_link'] );
		$instance['address'] = strip_tags( $new_instance['address'] );
		
		for ($social_count=1; $social_count <=25; $social_count++ ) {
			$instance['custom'.$social_count.'name'] = strip_tags( $new_instance['custom'.$social_count.'name'] );
			$instance['custom'.$social_count.'icon'] = strip_tags( $new_instance['custom'.$social_count.'icon'] );
			$instance['custom'.$social_count.'url'] = strip_tags( $new_instance['custom'.$social_count.'url'] );
		}
		
		
		return $instance;
	}

	public function form( $instance ) {

		$defaults = array( 
			'title' => __('Follow Us!', 'mthemelocal'),
			'text' => '',
			'imgcaption' => __('Follow Us on', 'mthemelocal'), 
			'icon_size' => 'default',
			'icon_opacity' => 'default',
			'newtab' => 'yes',
			'nofollow' => 'on',
			'alignment' => 'center',
			'call_us' => '',
			'call_us_link' => '',
			'address' => '',
			'custom1name' => 'RSS Link',
			'custom1icon' => 'rss',
			'custom1url' => '',
			'custom2name' => 'Facebook',
			'custom2icon' => 'facebook',
			'custom2url' => '',
			'custom3name' => 'Twitter',
			'custom3icon' => 'twitter',
			'custom3url' => '',
			'custom4name' => 'Tripadvisor',
			'custom4icon' => 'tripadvisor',
			'custom4url' => '',
			'custom5name' => 'Dribbble',
			'custom5icon' => 'dribbble',
			'custom5url' => '',
			'custom6name' => 'Google+',
			'custom6icon' => 'google-plus',
			'custom6url' => '',
			'custom7name' => 'LinkedIn',
			'custom7icon' => 'linkedin',
			'custom7url' => '',
			'custom8name' => 'Tumblr',
			'custom8icon' => 'tumblr',
			'custom8url' => '',
			'custom9name' => 'Youtube',
			'custom9icon' => 'youtube',
			'custom9url' => '',
			'custom10name' => 'Pinterest',
			'custom10icon' => 'pinterest',
			'custom10url' => '',
			'custom11name' => 'Flickr',
			'custom11icon' => 'flickr',
			'custom11url' => '',
			'custom12name' => 'Skype',
			'custom12icon' => 'skype',
			'custom12url' => '',
			'custom13name' => 'Instagram',
			'custom13icon' => 'instagram',
			'custom13url' => '',
			'custom14name' => 'Behance',
			'custom14icon' => 'behance',
			'custom14url' => '',
			'custom15name' => 'Vimeo',
			'custom15icon' => 'vimeo-square',
			'custom15url' => '',
			'custom16name' => 'Vine',
			'custom16icon' => 'vine',
			'custom16url' => '',
			'custom17name' => '500px',
			'custom17icon' => '500px',
			'custom17url' => '',
			'custom18name' => 'VK',
			'custom18icon' => 'vk',
			'custom18url' => '',
			'custom19name' => 'e-Mail',
			'custom19icon' => 'email',
			'custom19url' => '',
			'custom20name' => 'Custom icon 1',
			'custom20icon' => '',
			'custom20url' => '',
			'custom21name' => 'Custom icon 2',
			'custom21icon' => '',
			'custom21url' => '',
			'custom22name' => 'Custom icon 3',
			'custom22icon' => '',
			'custom22url' => '',
			'custom23name' => 'Custom icon 4',
			'custom23icon' => '',
			'custom23url' => '',
			'custom24name' => 'Custom icon 5',
			'custom24icon' => '',
			'custom24url' => '',
			'custom25name' => 'Custom icon 6',
			'custom25icon' => '',
			'custom25url' => ''
			);
			
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
	
		<div>
		<h2>Social Settings</h2>

<!-- Open in new tab: Dropdown -->
		<p>
			<label for="<?php echo $this->get_field_id( 'newtab' ); ?>"><?php _e('Open in new tab?', 'mthemelocal'); ?></label><br />
			<select class="widefat" id="<?php echo $this->get_field_id( 'newtab' ); ?>" name="<?php echo $this->get_field_name( 'newtab' ); ?>">
			<option value="yes" <?php if($instance['newtab'] == 'yes') { echo 'selected'; } ?>>Yes</option>
			<option value="no" <?php if($instance['newtab'] == 'no') { echo 'selected'; } ?>>No</option>
			</select>
		</p>

		
		<h2>Contact Text</h2>
		<p>Fill this with any contact info you like. eg. Call us: +123 456 7890</p>
		<!-- Call us -->
		<p>
			<label for="<?php echo $this->get_field_id( 'call_us' ); ?>"><?php _e('Contact text:', 'mthemelocal'); ?></label><br />
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'call_us' ); ?>" name="<?php echo $this->get_field_name( 'call_us' ); ?>" value="<?php echo esc_attr($instance['call_us']); ?>" />
		</p>
		<!-- Call us link -->
		<p>
			<label for="<?php echo $this->get_field_id( 'call_us_link' ); ?>"><?php _e('Contact text link:', 'mthemelocal'); ?></label><br />
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'call_us_link' ); ?>" name="<?php echo $this->get_field_name( 'call_us_link' ); ?>" value="<?php echo esc_attr($instance['call_us_link']); ?>" />
		</p>	

		<h2>Address</h2>
		<p>Company, Street, City, Country</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'address' ); ?>"><?php _e('Address:', 'mthemelocal'); ?></label><br />
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>" value="<?php echo esc_attr($instance['address']); ?>" />
		</p>	

		<h2>Social Links</h2>
		<?php
		for ($social_count=1; $social_count <=19; $social_count++ ) {
			$the_label = 'URL';
			if ( 19 === $social_count ) {
				$the_label = 'mailto:email@address.com';
			}
		?>
		<div>
			<h2><strong><?php echo $social_count; ?></strong></h2>
			<label for="<?php echo $this->get_field_id( 'custom'.$social_count.'url' ); ?>"><?php echo $defaults['custom'.$social_count.'name']; ?>: <?php echo $the_label; ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'custom'.$social_count.'url' ); ?>" name="<?php echo $this->get_field_name( 'custom'.$social_count.'url' ); ?>" value="<?php echo esc_attr($instance['custom'.$social_count.'url']); ?>" />
		</div>
		<?php
		}
		?>
		<?php
		for ($social_count=20; $social_count <=25; $social_count++ ) {
			$the_label = 'Fontawesome class:';
		?>
		<div>
			<h2><strong><?php echo $social_count; ?></strong></h2>
			<p>
			<label for="<?php echo $this->get_field_id( 'custom'.$social_count.'icon' ); ?>">
			<?php echo $the_label; ?><br/><small>Example:</small><br/>fab fa-facebook-f</label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'custom'.$social_count.'icon' ); ?>" name="<?php echo $this->get_field_name( 'custom'.$social_count.'icon' ); ?>" value="<?php echo esc_attr($instance['custom'.$social_count.'icon']); ?>" />
			</p>
			<p>
			<label><?php echo 'Enter URL'; ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'custom'.$social_count.'url' ); ?>" name="<?php echo $this->get_field_name( 'custom'.$social_count.'url' ); ?>" value="<?php echo esc_attr($instance['custom'.$social_count.'url']); ?>" />
			</p>
		</div>
		<?php
		}
		?>
		
		</div>
		

	<?php
	}
}
function mtheme_Social_Widget_register_widgets() {
	register_widget( 'mtheme_Social_Widget' );
}
add_action( 'widgets_init', 'mtheme_Social_Widget_register_widgets' );
?>