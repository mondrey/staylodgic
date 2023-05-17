<?php
class mtheme_Recent_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_posts', 'description' => __( "Displays the recent posts", 'mthemelocal') );
		parent::__construct('recent_posts', __('superlens Recent Posts', 'mthemelocal'), $widget_ops);
		$this->alt_option_name = 'widget_recent_posts';
	}

	public function widget($args, $instance) {
		$cache = wp_cache_get('mtheme_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'mthemelocal') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		
		if ( !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;
		else if ( $desc_length < 1 )
			$desc_length = 1;
		
		$disable_thumbnail = $instance['disable_thumbnail'] ? '1' : '0';
		$display_extra_type = $instance['display_extra_type'] ? $instance['display_extra_type'] :'time';
		
		$query = array('showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		if(!empty($instance['cat'])){
			$query['cat'] = implode(',', $instance['cat']);
		}

		$r = new WP_Query($query);
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul id="recentposts_list">
<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li>
<?php if(!$disable_thumbnail):?>
				<a class="recent_thumbnail" href="<?php echo get_permalink() ?>" title="<?php the_title();?>">
<?php if (has_post_thumbnail() ): ?>
		<?php
		// Show Image
		$output="";
		
		$image_id = get_post_thumbnail_id( get_the_id(), 'superlens-gridblock-tiny');
		$image_url = wp_get_attachment_image_src( $image_id, 'superlens-gridblock-tiny');
		$image_url = $image_url[0];

		$srcset      = wp_get_attachment_image_srcset( $image_id, 'superlens-gridblock-square-big' );
		$srcsetsizes = wp_get_attachment_image_sizes( $image_id, 'superlens-gridblock-square-big' );
		
		$permalink = get_permalink( get_the_id() );
		
		$have_image_url=$image_url;
		$fallback_src = '';

		$fallback_image = superlens_get_placeholder_image('superlens-gridblock-square-big');

		$data_src = "data-";
		$fallback_src = 'src="'. esc_url( $fallback_image ) .'" ';
		$class="sidegalleryfade";
		$class = $class . ' lazyload';
		$continue_text = imaginem_codepack_get_option_data( 'read_more', 'Read' );
		
		$img_alt = superlens_get_alt_text($image_id);
		echo '<img '.$fallback_src.$data_src.'src="'. esc_url( $have_image_url ) .'" data-srcset="'.esc_attr($srcset).'" data-sizes="'.esc_attr($srcsetsizes).'" alt="'. esc_attr( $img_alt ) .'" class="'.$class.'"/>';
		?>
<?php else:?>

<?php endif;//end has_post_thumbnail ?>
				</a>
<?php endif;//disable_thumbnail ?>
				<div class="recentpost_info">
					<p class="recentpost_date summary-date"><i class="feather-icon-clock"></i><?php echo get_the_date(); ?></p>
					<a class="recentpost_title" href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
					<a class="arrow-link link--arrowed" href="<?php the_permalink() ?>"><?php echo esc_html( $continue_text ); ?></a>
				</div>
			</li>
<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_query();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('mtheme_recent_posts', $cache, 'widget');
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['desc_length'] = (int) $new_instance['desc_length'];
		$instance['disable_thumbnail'] = !empty($new_instance['disable_thumbnail']) ? 1 : 0;
		$instance['display_extra_type'] = $new_instance['display_extra_type'];
		$instance['cat'] = $new_instance['cat'];

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	public function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$disable_thumbnail = isset( $instance['disable_thumbnail'] ) ? (bool) $instance['disable_thumbnail'] : false;
		$display_extra_type = isset( $instance['display_extra_type'] ) ? $instance['display_extra_type'] : 'time';
		$cat = isset($instance['cat']) ? $instance['cat'] : array();

		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;

		if ( !isset($instance['desc_length']) || !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;

		$categories = get_categories('orderby=name&hide_empty=0');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mthemelocal'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Posts to Display:', 'mthemelocal'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" size="3" /></		
		
		<p><label for="<?php echo $this->get_field_id('desc_length'); ?>"><?php _e('Description Lenght:', 'mthemelocal'); ?></label>
		<input id="<?php echo $this->get_field_id('desc_length'); ?>" name="<?php echo $this->get_field_name('desc_length'); ?>" type="text" value="<?php echo esc_attr($desc_length); ?>" size="3" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e( 'Select from Categories:' , 'mthemelocal'); ?></label>
			<select style="height:5.5em" name="<?php echo $this->get_field_name('cat'); ?>[]" id="<?php echo $this->get_field_id('cat'); ?>" class="widefat" multiple="multiple">
				<?php foreach($categories as $category):?>
				<option value="<?php echo $category->term_id;?>"<?php echo in_array($category->term_id, $cat)? ' selected="selected"':'';?>><?php echo $category->name;?></option>
				<?php endforeach;?>
			</select>
		</p>
<?php
	}
}
function mtheme_Recent_Posts_register_widgets() {
	register_widget( 'mtheme_Recent_Posts' );
}
add_action( 'widgets_init', 'mtheme_Recent_Posts_register_widgets' );
?>