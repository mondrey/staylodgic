<?php
class mtheme_PortfolioGallery_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'widget_mtheme_portfolio_gallery', 'description' => 'Generates an image gallery.' );
		parent::__construct('mtheme_portfolio_gallery', __('superlens Portfolio Gallery Widget'), $widget_ops);
	}

	public function widget($args, $instance) {
		extract($args, EXTR_SKIP);

		$clickaction="";
		$thepage="";
		$thecategory="";
		$listchoice="";
		$galleryorder="";
		$thumbsize="";
		$desc="";
		$thelimit	=	-1;
		$columnbreak="3";
		
		$height=82;
		$width=82;
		
		$gridtype="infobar-portfoliogrid";
		
		if (isset($instance['clickaction'])) { $clickaction = $instance['clickaction']; }
		if (isset($instance['theportfolio'])) { $theportfolio= $instance['theportfolio']; }
		if (isset($instance['thelimit'])) { $thelimit=$instance['thelimit']; }
		if (isset($instance['desc'])) { $desc=$instance['desc']; }
		if (isset($instance['galleryorder'])) { $galleryorder=$instance['galleryorder']; }
		if (isset($instance['thumbsize'])) { $thumbsize=$instance['thumbsize']; }

		
		echo $before_widget;
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);


			$gridtype="infobar-portfoliogrid";
			$count=0;
			$vkey="video";
			?>
			
			<div class="widget-portfolio-gallery">
				<?php
				if ( !empty( $title ) ) { 
					echo $before_title . $title . $after_title; 
				};
			?>
			<div class="portfolio-gallery-description">
				<?php
				echo $desc;
				?>
			</div>
			<ul class="<?php echo $gridtype; ?>">
			
			<?php
					if ($theportfolio=="BLANK") { $theportfolio=""; }

					$newquery = array(
					'post_type' => 'portfolio',
					'worktypes' => $theportfolio,
					'orderby' => $galleryorder,
					'order' => 'ASC',
					'posts_per_page' => $thelimit,
					);
					query_posts($newquery);
				
					if (have_posts()) : while (have_posts()) : the_post();
					
					$count++;
					
					$image_id = get_post_thumbnail_id(( get_the_id() ), 'fullpage'); 
					$image_url = wp_get_attachment_image_src($image_id,'fullpage');  
					$image_url = $image_url[0];					
					
					$thumbnail_image_id = get_post_thumbnail_id(( get_the_id() ), 'superlens-gridblock-square-big'); 
					$thumbnail_image_url = wp_get_attachment_image_src($image_id,'superlens-gridblock-square-big');  
					$thumbnail_image_url = $thumbnail_image_url[0];

					$srcset      = wp_get_attachment_image_srcset( $thumbnail_image_id, 'superlens-gridblock-square-big' );
					$srcsetsizes = wp_get_attachment_image_sizes( $thumbnail_image_id, 'superlens-gridblock-square-big' );
					
					$imageTitle = get_the_title( get_the_id() );
					
					$hovericon="imageicon";
					$lightbox_url=$image_url;
					
					if ($count==$columnbreak) { 
						$class_string=$hovericon;
						$count=0;
					} else {
						$class_string="rightspace " . $hovericon;
					}
					
					?>

						<li class="<?php echo $class_string; ?> lazyload-wrapper">
						<?php

						if ($clickaction=="lightbox" && !post_password_required() ) { ?>
							<a class="lightbox-active lightbox-image" data-rel="gallery-portfoliogallery" data-thumb="<?php echo esc_attr($thumbnail_image_url); ?>" data-thumbnail="<?php echo esc_url($lightbox_url); ?>" data-src="<?php echo esc_url($lightbox_url); ?>" href="<?php echo esc_url($lightbox_url); ?>">
							<?php
							} else {
							?>
							<a href="<?php the_permalink() ?>">
							<?php
						}
						?>
								<?php
								$have_image_url=$thumbnail_image_url;
								$fallback_src = '';

								$fallback_image = superlens_get_placeholder_image('superlens-gridblock-square-big');

								$data_src = "data-";
								$fallback_src = 'src="'. esc_url( $fallback_image ) .'" ';
								$class="sidegalleryfade";
								$class = $class . ' lazyload';
								
								$img_alt = superlens_get_alt_text($image_id);
								echo '<img '.$fallback_src.$data_src.'src="'. esc_url( $have_image_url ) .'" data-srcset="'.esc_attr($srcset).'" data-sizes="'.esc_attr($srcsetsizes).'" alt="'. esc_attr( $img_alt ) .'" class="'.$class.'"/>';
								?>
							</a>
						</li>			
					
					<?php
					endwhile; endif;
					wp_reset_query();
			?>
			</ul>
			<div class="clear"></div>
			</div>
		<?php
		echo $after_widget;
	}

	public function update($new_instance, $old_instance) {
		//$instance = $old_instance;
		//$instance['title'] = strip_tags($new_instance['title']);
		//$instance['clickaction'] = strip_tags($new_instance['clickaction']);
		return $new_instance;

		//return $instance;
	}

	public function form($instance) {
	
		$clickaction="";
		$thepage="";
		$thecategory="";
		$listchoice="";
		$galleryorder="";
		$thumbsize="";
		$desc="";
		$thelimit="";
		$theportfolio="";
	
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Sidebar Gallery' ) );
		$title = strip_tags($instance['title']);
		if (isset($instance['clickaction'])) { $clickaction = esc_attr($instance['clickaction']); }
		if (isset($instance['thepage'])) { $thepage = esc_attr($instance['thepage']); }
		if (isset($instance['thecategory'])) { $thecategory= esc_attr($instance['thecategory']); }
		if (isset($instance['desc'])) { $desc= esc_attr($instance['desc']); }
		if (isset($instance['theportfolio'])) { $theportfolio= esc_attr($instance['theportfolio']); }
		if (isset($instance['listchoice'])) { $listchoice=esc_attr($instance['listchoice']); }
		if (isset($instance['galleryorder'])) { $galleryorder=esc_attr($instance['galleryorder']); }
		if (isset($instance['thelimit'])) { $thelimit=esc_attr($instance['thelimit']); }
		
		// Start Generate a Page List
		$pagelist = get_pages();
		$page_options = array();
		$page_options[] = '<option value="BLANK">Select a Page</option>';
 
		foreach ($pagelist as $page) {
			if ($thepage==$page->ID) { $selected=' selected="selected"';} else { $selected=""; }
			$page_options[] = '<option value="' . $page->ID .'"' . $selected . '>' . $page->post_title . '</option>';
		}
		// End Generate a Page List
		
		// Start Generate a Category List
		$categories = get_categories();
		$cat_options = array();
		$cat_options[] = '<option value="BLANK">Select a Category</option>';
 
		foreach ($categories as $cat) {
			if ($thecategory==$cat->cat_ID) { $selected=' selected="selected"';} else { $selected=""; }
			$cat_options[] = '<option value="' . $cat->cat_ID .'"' . $selected . '>' . $cat->name . '</option>';
		}
		// End Generate a Category List
		
		// Start Generate a Category List
		$portfolios = get_categories('taxonomy=worktypes&title_li=');
		$portfolio_options = array();
		$portfolio_options[] = '<option value="BLANK">All items</option>';
 
 		if (is_array($portfolios)) {
			foreach ($portfolios as $portfolio) {
				$selected="";
				if (isSet($portfolio->slug)) {
					if ($theportfolio==$portfolio->slug) {
						$selected=' selected="selected"';
					}
					$portfolio_options[] = '<option value="' . $portfolio->slug .'"' . $selected . '>' . $portfolio->name . '</option>';
				}
			}
		}
		
	?>
	<?php // Title Field ?>	
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','mthemelocal'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	
	<?php // Portfolio List ?>	
	<p>
		<label for="<?php echo $this->get_field_id('theportfolio'); ?>">
			<?php _e('Select Portfolio <br/><small><strong>If List-Choice is Portfolio</strong></small> :','mthemelocal'); ?>
		</label>
		<select id="<?php echo $this->get_field_id('theportfolio'); ?>" class="widefat" name="<?php echo $this->get_field_name('theportfolio'); ?>">
			<?php echo implode('', $portfolio_options); ?>
		</select>
	</p>
	
	<?php // Click Action Field ?>		
	<p>
		<label for="<?php echo $this->get_field_id('clickaction'); ?>">
			<?php _e('Click Action:','mthemelocal'); ?>
		</label>
	<select id="<?php echo $this->get_field_id('clickaction'); ?>" class="widefat" name="<?php echo $this->get_field_name('clickaction'); ?>">
		<option value="lightbox"<?php echo ($clickaction === 'lightbox' ? ' selected="selected"' : ''); ?>>Lightbox</option>
		<option value="directlink"<?php echo ($clickaction === 'directlink' ? ' selected="selected"' : ''); ?>>Direct Link</option>
	</select>
	</p>

	<p><label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Description', 'mtheme_local'); ?><br /></label>
  	<textarea class="widefat" id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="text" ><?php echo $desc; ?></textarea>
    </p>
	<?php // Click Action Field ?>		
	<p>
		<label for="<?php echo $this->get_field_id('galleryorder'); ?>">
			<?php _e('Image Order:','mthemelocal'); ?>
		</label>
	<select id="<?php echo $this->get_field_id('galleryorder'); ?>" class="widefat" name="<?php echo $this->get_field_name('galleryorder'); ?>">
		<option value="menu_order"<?php echo ($galleryorder === 'menu_order' ? ' selected="selected"' : ''); ?>>Menu Order</option>
		<option value="rand"<?php echo ($galleryorder === 'rand' ? ' selected="selected"' : ''); ?>>Random</option>
		<option value="date"<?php echo ($galleryorder === 'date' ? ' selected="selected"' : ''); ?>>Date</option>
	</select>
	</p>
	
	<?php // Limit List ?>	
	<p>
		<label for="<?php echo $this->get_field_id('thelimit'); ?>">
			<?php _e('Limit Portfolio:','mthemelocal'); ?>
		</label>
		<select id="<?php echo $this->get_field_id('thelimit'); ?>" class="widefat" name="<?php echo $this->get_field_name('thelimit'); ?>">
			<option value="1"<?php echo ($thelimit === '1' ? ' selected="selected"' : ''); ?>>1</option>
			<option value="2"<?php echo ($thelimit === '2' ? ' selected="selected"' : ''); ?>>2</option>
			<option value="3"<?php echo ($thelimit === '3' ? ' selected="selected"' : ''); ?>>3</option>
			<option value="4"<?php echo ($thelimit === '4' ? ' selected="selected"' : ''); ?>>4</option>
			<option value="5"<?php echo ($thelimit === '5' ? ' selected="selected"' : ''); ?>>5</option>
			<option value="6"<?php echo ($thelimit === '6' ? ' selected="selected"' : ''); ?>>6</option>
			<option value="7"<?php echo ($thelimit === '7' ? ' selected="selected"' : ''); ?>>7</option>
			<option value="8"<?php echo ($thelimit === '8' ? ' selected="selected"' : ''); ?>>8</option>
			<option value="9"<?php echo ($thelimit === '9' ? ' selected="selected"' : ''); ?>>9</option>
			<option value="10"<?php echo ($thelimit === '10' ? ' selected="selected"' : ''); ?>>10</option>
			<option value="11"<?php echo ($thelimit === '11' ? ' selected="selected"' : ''); ?>>11</option>
			<option value="12"<?php echo ($thelimit === '12' ? ' selected="selected"' : ''); ?>>12</option>
			<option value="13"<?php echo ($thelimit === '13' ? ' selected="selected"' : ''); ?>>13</option>
			<option value="14"<?php echo ($thelimit === '14' ? ' selected="selected"' : ''); ?>>14</option>
			<option value="15"<?php echo ($thelimit === '15' ? ' selected="selected"' : ''); ?>>15</option>
			<option value="20"<?php echo ($thelimit === '20' ? ' selected="selected"' : ''); ?>>20</option>
			<option value="25"<?php echo ($thelimit === '25' ? ' selected="selected"' : ''); ?>>25</option>
			<option value="30"<?php echo ($thelimit === '30' ? ' selected="selected"' : ''); ?>>30</option>
			<option value="-1"<?php echo ($thelimit === '-1' ? ' selected="selected"' : ''); ?>>Unlimited</option>
		</select>
	</p>


	
<?php
	}
}
function mtheme_PortfolioGallery_Widget_register_widgets() {
	register_widget( 'mtheme_PortfolioGallery_Widget' );
}
add_action( 'widgets_init', 'mtheme_PortfolioGallery_Widget_register_widgets' );
?>