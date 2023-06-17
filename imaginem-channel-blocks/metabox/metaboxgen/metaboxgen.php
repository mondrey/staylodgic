<?php
function themecore_generate_sidebarlist( $sidebarlist_type ) {
	$max_sidebars = 50;
	if ($sidebarlist_type=="events") {
		$sidebar_options=array();
		$sidebar_options['events_sidebar']='Default Events Sidebar';
		$sidebar_options['default_sidebar']='Default Sidebar';
		for ($sidebar_count=1; $sidebar_count <= $max_sidebars; $sidebar_count++ ) {

			if ( themecore_get_option_data('mthemesidebar-'.$sidebar_count) <> "" ) {
				$active_sidebar = themecore_get_option_data('mthemesidebar-'.$sidebar_count);
				$sidebar_options['mthemesidebar-'.$sidebar_count] = $active_sidebar;
			}
		}
	}
	if ($sidebarlist_type=="proofing") {
		$sidebar_options=array();
		$sidebar_options['proofing_sidebar']='Default Proofing Sidebar';
		$sidebar_options['default_sidebar']='Default Sidebar';
		for ($sidebar_count=1; $sidebar_count <= $max_sidebars; $sidebar_count++ ) {

			if ( themecore_get_option_data('mthemesidebar-'.$sidebar_count) <> "" ) {
				$active_sidebar = themecore_get_option_data('mthemesidebar-'.$sidebar_count);
				$sidebar_options['mthemesidebar-'.$sidebar_count] = $active_sidebar;
			}
		}
	}
	if ($sidebarlist_type=="portfolio") {
		$sidebar_options=array();
		$sidebar_options['portfolio_sidebar']='Default Portfolio Sidebar';
		$sidebar_options['default_sidebar']='Default Sidebar';
		for ($sidebar_count=1; $sidebar_count <= $max_sidebars; $sidebar_count++ ) {

			if ( themecore_get_option_data('mthemesidebar-'.$sidebar_count) <> "" ) {
				$active_sidebar = themecore_get_option_data('mthemesidebar-'.$sidebar_count);
				$sidebar_options['mthemesidebar-'.$sidebar_count] = $active_sidebar;
			}
		}
	}
	if ($sidebarlist_type=="post" || $sidebarlist_type=="page" ) {
		$sidebar_options=array();
		$sidebar_options['default_sidebar']='Default Sidebar';
		if ( class_exists( 'woocommerce' ) ) {
			if ( $sidebarlist_type=="page" ) {
				$sidebar_options['woocommerce_sidebar']='Default WooCommerce Sidebar';
			}
		}
		for ($sidebar_count=1; $sidebar_count <= $max_sidebars; $sidebar_count++ ) {

			if ( themecore_get_option_data('mthemesidebar-'.$sidebar_count) <> "" ) {
				$active_sidebar = themecore_get_option_data('mthemesidebar-'.$sidebar_count);
				$sidebar_options['mthemesidebar-'.$sidebar_count] = $active_sidebar;
			}
		}
	}
	if (isSet($sidebar_options)) {
		return $sidebar_options;
	} else {
		return false;
	}
}
function themecore_generate_metaboxes($meta_data,$post_id) {
	// Use nonce for verification
	
	$the_menu_style = themecore_get_option_data('menu_type');
	echo '<input type="hidden" name="mtheme_meta_box_nonce" value="', wp_create_nonce( 'metabox-nonce' ), '" />';
	
	echo '<div class="metabox-wrapper theme-menu-style-'.$the_menu_style.' clearfix">';
	$countcolumns=0;
	foreach ($meta_data['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post_id, $field['id'], true);

		if ( themecore_page_is_built_with_elementor( $post_id ) ) {
			$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
			if ( isSet($elementor_page_settings[ $field['id'] ]) ) {
				$meta = $elementor_page_settings[ $field['id'] ];
			}
		}

		$class="";
		$trigger_element="";
		$trigger="";
		
		$titleclass="is_title";
		if ( isSet($field['heading']) ) {
			if ( $field['heading']=="subhead" ) $titleclass="is_subtitle";
		}

		if (isset($field['class'])) {
			$class = $field['class'];
		}
		if (!isset($field['toggleClass'])) {
			$field['toggleClass']='';
		}
		if (!isset($field['toggleAction'])) {
			$field['toggleAction']='';
		}
		if (isset($field['triggerStatus'])) {
			if ($field['triggerStatus']=="on") $trigger_element="trigger_element";
			$trigger = "<span data-toggleClass='".$field['toggleClass']."' ";
			$trigger .= "data-toggleAction='".$field['toggleAction']."' ";
			$trigger .= "data-toggleID='".$field['id']."' ";
			$trigger .= "data-parentclass='".$field['class']."' ";
			$trigger .= "></span>";
		}

		if ( $field['type']=="nobreak" ) {
			$titleclass .=" is_nobreak";
			if ($field['sectiontitle']<>"") {
			}
			$div_column_open = true;
		}
		if ( $field['type']=="break" ) {
			$titleclass .=" is_break";
			if ( $countcolumns > 0 ) {
				if ( $div_is_open ) {
					echo '</div>';
				}
			}
			$countcolumns++;
			echo '<div class="metabox-column">';
			if ($field['sectiontitle']<>"") {
			}
			$div_column_open = true;
		}
		$div_is_open = true;
		echo '<div class="metabox-fields metaboxtype_', $field['type'] ,' '. $class . " " . $titleclass. " " . $trigger_element .'">',
				$trigger,
				'<div class="metabox_label"><label for="', $field['id'], '"></label></div>';
		if ( isSet($field['type']) ) {
			
			if ( $field['type']!="break" && $field['type']!="break") {
				if ( $field['name']!="" ) {
					echo '<div id="'.$field['id'].'-section-title" class="sectiontitle clearfix">'.$field['name'].'</div>';
				}
			}
			
			switch ($field['type']) {

			case 'selected_proofing_images':
				$filter_image_ids = themecore_get_proofing_attachments ( $post_id );
				$found_selection = false;
				if ( $filter_image_ids ) {

					foreach ( $filter_image_ids as $attachment_id) {
						$proofing_status = get_post_meta($attachment_id,'checked',true);
						if ($proofing_status=="true") {
							$found_selection = true;
						}
					}

					if ( $found_selection ) {

						echo '<div class="proofing-admin-selection">';
						echo '<ul>';
						foreach ( $filter_image_ids as $attachment_id) {
							$proofing_status = get_post_meta($attachment_id,'checked',true);
							if ($proofing_status=="true") {
								$thumbnail_imagearray = wp_get_attachment_image_src( $attachment_id , 'thumbnail' , false);
								$thumbnail_imageURI = $thumbnail_imagearray[0];
								echo '<li class="images"><img src="'.esc_url($thumbnail_imageURI).'" alt="'.esc_attr__('selected','themecore').'" /></li>';
								$found_selection = true;
							}
						}
						foreach ( $filter_image_ids as $attachment_id) {
							$proofing_status = get_post_meta($attachment_id,'checked',true);
							if ($proofing_status=="true") {
								echo '<li>' . basename( get_attached_file( $attachment_id ) ) . '</li>';
								$found_selection = true;
							}
						}
						echo '</ul>';
						echo '</div>';
					}
				}

				if (!$found_selection) {
					echo '<div class="proofing-none-selected">';
					_e('No selection found.','themecore');
					echo '</div>';
				}


				break;

			case 'image_gallery':
				// SPECIAL CASE:
				// std controls button text; unique meta key for image uploads
				$meta = get_post_meta( $post_id, '_mtheme_image_ids', true );
				$thumbs_output = '';
				$button_text = ($meta) ? esc_html__('Edit Gallery', 'themecore') : $field['std'];
				$renew_meta = '';
				if( $meta ) {
					$field['std'] = esc_html__('Edit Gallery', 'themecore');
					$thumbs = explode(',', $meta);
					$thumbs_output = '';
					$imageidcount = 0;
					foreach( $thumbs as $thumb ) {
						if ( wp_attachment_is_image( $thumb ) ) {

							$got_attached_image = wp_get_attachment_image( $thumb, 'thumbnail' );
							if ( isSet($got_attached_image) && $got_attached_image<>"" ) {
								if ($imageidcount>0) {
									$renew_meta = $renew_meta . ',';
								}
								$imageidcount++;

								$thumbs_output .= '<li data-thumbnailimageid="'.esc_attr($thumb).'">' . $got_attached_image . '</li>';
								$renew_meta .= $thumb;
							}
						}
					}

				}

			    echo 
			    	'<td>
			    		<input type="button" class="button" name="' . esc_attr( $field['id'] ) . '" id="mtheme_images_upload" value="' . esc_attr($button_text) .'" />
			    		
			    		<input type="hidden" name="mtheme_meta[_mtheme_image_ids]" id="_mtheme_image_ids" value="' . esc_attr($renew_meta ? $renew_meta : 'false') . '" />

			    		<ul class="mtheme-gallery-thumbs">' . $thumbs_output . '</ul>
			    	</td>';

			    break;

			case 'proofing_gallery':
				// SPECIAL CASE:
				// std controls button text; unique meta key for image uploads
				$meta = get_post_meta( $post_id, '_mtheme_proofing_image_ids', true );
				$thumbs_output = '';
				$button_text = ($meta) ? esc_html__('Edit Proofing Gallery', 'themecore') : $field['std'];
				$renew_meta = '';
				if( $meta ) {
					$field['std'] = esc_html__('Edit Proofing Gallery', 'themecore');
					$thumbs = explode(',', $meta);
					$thumbs_output = '';
					$imageidcount = 0;
					foreach( $thumbs as $thumb ) {
						if ( wp_attachment_is_image( $thumb ) ) {

							$got_attached_image = wp_get_attachment_image( $thumb, 'thumbnail' );
							if ( isSet($got_attached_image) && $got_attached_image<>"" ) {
								if ($imageidcount>0) {
									$renew_meta = $renew_meta . ',';
								}
								$imageidcount++;

								$thumbs_output .= '<li data-thumbnailimageid="'.esc_attr($thumb).'">' . $got_attached_image . '</li>';
								$renew_meta .= $thumb;
							}
						}
					}

				}

			    echo 
			    	'<td>
			    		<input type="button" class="button" name="' . esc_attr( $field['id'] ) . '" id="mtheme_proofing_images_upload" value="' . esc_attr($button_text) .'" />
			    		
			    		<input type="hidden" name="mtheme_meta[_mtheme_proofing_image_ids]" id="_mtheme_proofing_image_ids" value="' . esc_attr($renew_meta ? $renew_meta : 'false') . '" />

			    		<ul class="mtheme-proofing-gallery-thumbs">' . $thumbs_output . '</ul>
			    	</td>';

			    break;

			case 'multi_upload':
				// SPECIAL CASE:
				// std controls button text; unique meta key for image uploads
				$meta = get_post_meta( $post_id, esc_attr( $field['id'] ) , true );
				$thumbs_output = '';
				$button_text = ($meta) ? esc_html__('Edit Gallery', 'themecore') : $field['std'];
				if( $meta ) {
					$field['std'] = esc_html__('Edit Gallery', 'themecore');
					$thumbs = explode(',', $meta);
					$thumbs_output = '';
					foreach( $thumbs as $thumb ) {
						$thumbs_output .= '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
					}
				}

			    echo 
			    	'<td>
			    		<input type="button" data-galleryid="'.esc_attr( $field['id'] ).'" data-imageset="'.esc_attr($meta).'" class="button meta-multi-upload" name="' . esc_attr( $field['id'] ) . '" value="' . esc_attr($button_text) .'" />
			    		
			    		<input type="hidden" name="'.esc_attr( $field['id'] ).'" id="'.esc_attr( $field['id'] ).'" value="' . esc_attr($meta ? $meta : 'false') . '" />

			    		<ul class="mtheme-multi-thumbs multi-gallery-'.esc_attr( $field['id'] ).'">' . $thumbs_output . '</ul>
			    	</td>';

			    break;

				case 'display_image_attachments' :
					$images = get_children( array( 
								'post_parent' => $post_id,
								'post_status' => 'inherit',
								'post_type' => 'attachment',
								'post_mime_type' => 'image',
								'order' => 'ASC',
								'numberposts' => -1,
								'orderby' => 'menu_order' )
								);
					if ($images) {
						foreach ( $images as $id => $image ) {
							$attatchmentID = $image->ID;
							$imagearray = wp_get_attachment_image_src( $attatchmentID , 'thumbnail', false);
							$imageURI = $imagearray[0];
							$imageID = get_post($attatchmentID);
							$imageTitle = $image->post_title;
							echo '<img src="'. esc_url( $imageURI ).'" alt="'.esc_attr__('image','themecore').'" />';
						}
					} else {
						echo esc_html__('No images found.','themecore');
					}
					break;

				case "seperator":
					echo '<hr/>';

					break;

			// Color picker
				case "color":
					$default_color = '';
					if ( isset($value['std']) ) {
						if ( $val !=  $value['std'] )
							$default_color = ' data-default-color="' .esc_attr( $value['std'] ). '" ';
					}
					$color_value = $meta ? $meta : $field['std'];
					echo '<input data-alpha-enabled="true" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="colorSwatch of-color"  type="text" value="' . esc_attr( $color_value ) . '" />';

					break;

				case 'upload':
					if ($meta!="") {
						$image_url_id = themecore_get_image_id_from_url($meta);
						$image_thumbnail_data = wp_get_attachment_image_src( $image_url_id , "thumbnail" , true );
						$image_thumbnail_url = $image_thumbnail_data[0];
						if ($image_thumbnail_url) {	
							echo '<img height="100px" src="'. esc_url( $image_thumbnail_url ).'" />';
						}
					}
					echo '<div>';
					$upload_value = $meta ? $meta : $field['std'];
					echo '<input type="text" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($upload_value) . '" size="30" />';
					echo '<button class="button-shortcodegen-uploader" data-id="' . $field['id'] . '" value="Upload">Upload</button>';
					echo '</div>';
					break;

				case 'text-responsive':
					$text_value = $meta ? $meta : $field['std'];

					$desktop_value = "0";
					$tablet_value = "0";
					$mobile_value = "0";

					if (isSet($text_value) && $text_value<>"") {
						$css_values = explode(',', $text_value);
						if ( isSet($css_values[0]) ) {
							$desktop_value = $css_values[0];
							$tablet_value = $css_values[0];
							$mobile_value = $css_values[0];
						}
						if ( isSet($css_values[1]) ) {
							$tablet_value = $css_values[1];
							$mobile_value = $tablet_value;
						}
						if ( isSet($css_values[2]) ) {
							$mobile_value = $css_values[2];
						}
					}

					echo '<span class="responsive-data-media">';
					echo '<span class="responsive-cue-icons dashicons dashicons-desktop"></span><span title="Desktop" class="responsive-data-fields responsive-data-desktop">'.$desktop_value.'</span>';
					echo '<span class="responsive-cue-icons dashicons dashicons-tablet"></span><span title="Tablet" class="responsive-data-fields responsive-data-tablet">'.$tablet_value.'</span>';
					echo '<span class="responsive-cue-icons dashicons dashicons-smartphone"></span><span title="Mobile" class="responsive-data-fields responsive-data-mobile">'.$mobile_value.'</span>';
					echo '</span>';

					echo '<input type="text" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					break;

				case 'disabled':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" disabled />';
					echo '<input type="hidden" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';

					break;

				case 'readonly':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input readonly type="text" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					break;
					
				case 'offview':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="hidden" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					break;

				case 'text':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="'.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					break;

				case 'switch':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="switch-toggle">';
					echo '<input type="hidden" class="meta-switch-toggle '.$class.'" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					echo '<div class="switch-toggle-slide"><div class="switch-inner"></div></div>';
					echo '</div>';
					break;

				case 'bedsetup_repeat':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="bedlayout-wrap" data-repeat="'.$field['id'].'">';
					echo '<div class="bedlayout">';
					$repeat_count = 0;
					$found_data=false;
					if (isSet($meta) && is_array($meta) ) {
						foreach ($meta['bedtype'] as $value) {
							if (isSet($value) && $value<>"") {
								$found_data = true;
								$age = '';
								if ( isSet( $meta['bedtype'][$repeat_count] ) ) {
									$bedtype = $meta['bedtype'][$repeat_count];
								}
								if ( isSet( $meta['bednumber'][$repeat_count] ) ) {
									$bednumber = $meta['bednumber'][$repeat_count];
								}
								$class='';
								if (isset($field['target'])) {
									$field['options'] = themecore_get_select_target_options($field['target']);
								}
								echo '<div class="bedlayout-box" id="bedlayout-box">';
								echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox bedtype-select" name="', esc_attr($field['id']).'[bedtype][]" id="bed_type'.$repeat_count.'">';
								foreach ($field['options'] as $key => $option) {
									if ($key=='0') {
										$key = __('All the items','themecore');
									}
									echo '<option value="'. esc_attr($key) .'"', $bedtype == $key ? ' selected' : '', '>', esc_attr($option) , '</option>';
								}
								echo '</select>';
								echo ' X <input placeholder="0" type="text" name="'. esc_attr($field['id']).'[bednumber][]" value="'. esc_attr($bednumber) .'" id="bed_number'.$repeat_count.'" /></div>';
								if ( $repeat_count > 0 ) {
									echo '<div class="remove-bedlayout">Remove</div>';
								}
								echo '</div>';
							}
							$repeat_count++;
						}
					}
					if (!$found_data) {
						if (isset($field['target'])) {
							$field['options'] = themecore_get_select_target_options($field['target']);
						}
						echo '<div class="bedlayout-box" id="bedlayout-box">';
						echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr($field['id']).'[bedtype][]" id="bed_type0">';
						foreach ($field['options'] as $key => $option) {
							if ($key=='0') {
								$key = __('All the items','themecore');
							}
							echo '<option value="'. esc_attr($key) .'"', $meta == $key ? ' selected' : '', '>', esc_attr($option) , '</option>';
						}
						echo '</select>';
						echo ' X <input placeholder="How many" type="text" name="'. esc_attr($field['id']).'[bednumber][]" value="" id="bed_number0" /></div>';
						echo '</div>';
					}
					echo '</div>';
					echo '<span class="add-bedlayout-box">'.esc_html__('Add layout','themecore').'</span>';
					echo '<span class="add-bedlayout-box-notice">'.esc_html__('Max Reached!','themecore').'</span>';
					echo '</div>';
					break;

				case 'repeat_text':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="movethis-wrap" data-repeat="'.$field['id'].'">';
					echo '<div class="movethis">';
					

					$repeat_count = 0;
					$found_data=false;
					if (isSet($meta) && is_array($meta) ) {
						foreach ($meta['age'] as $value) {
							if (isSet($value) && $value<>"") {
								$found_data = true;
								$age = '';
								if ( isSet( $meta['age'][$repeat_count] ) ) {
									$age = $meta['age'][$repeat_count];
								}
								echo '<div class="text-box" id="text-box">';
								echo '<input placeholder="'.esc_attr__('Age','themecore').'" type="text" name="'. esc_attr($field['id']).'[age][]" value="'. esc_attr($age) .'" id="box_size'.$repeat_count.'" />';
								if ($repeat_count>0) {
									echo '<span class="remove-box">'.esc_html__('Remove','themecore').'</span>';
								}
								echo '</div>';
							}
							$repeat_count++;
						}
					}
					if (!$found_data) {
						echo '<div class="text-box" id="text-box">';
						echo '</div>';
					}
					echo '</div>';
					echo '<span class="add-box">'.esc_html__('Add Child','themecore').'</span>';
					echo '<span class="add-box-notice">'.esc_html__('Max Reached!','themecore').'</span>';
					echo '</div>';
					break;
				case 'timepicker':
					$text_value = $meta ? $meta : $field['std'];
					echo '<select name="'.esc_attr($field['id']).'" id="'.esc_attr($field['id']).'">';
					$start = strtotime('12am');
					for ($i = 0; $i < (24 * 4); $i++) {
						
						$tod = $start + ($i * 15 * 60);
						$display = date('h:i A', $tod);

						if (substr($display, 0, 2) == '00') {
								$display = '12' . substr($display, 2);
						}
						if ($meta==$display) {
							$timeselected='selected="selected"';
						} else {
							$timeselected="";
						}

						$display_user_time = $display;
						$event_time_format = themecore_get_option_data('events_time_format');
						if ($event_time_format == "24hr") {
							$display_user_time = date('H:i', $tod);
						}
						echo '<option value="' . esc_attr($display) . '" '.$timeselected.'>' . esc_attr($display_user_time) . '</option>';
					} 
					echo '</select>';

					break;

				case 'country':
					$text_value = $meta ? $meta : $field['std'];
					echo '<select name="'.esc_attr($field['id']).'" id="'.esc_attr($field['id']).'">';
					echo themecore_country_list('select',$meta);
					echo '</select>';

					break;
				case 'datepicker':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="'.$class.' datepicker" data-enable-time="true" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					break;
				case 'hidden':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="hidden" name="', esc_attr($field['id']), '_hidden" id="', esc_attr($field['id']), '_hidden" value="' . esc_attr($text_value) . '" />';
					break;
				case 'reservation':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input data-postid="'.get_the_id().'" type="text" class="'.$class.' reservation" data-enable-time="true" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
					echo '<div id="reservation-details"></div>';
					break;
				case 'textarea':
					$textarea_value = $meta ? $meta : $field['std'];
					echo '<textarea name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" cols="60" rows="4" >' . esc_textarea($textarea_value) . '</textarea>';
					break;
				case 'fontselector':
					$class='';
					if (isset($field['target'])) {
						$field['options'] = themecore_get_select_target_options($field['target']);
					}
					
					echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox metabox_google_font_select" name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $key => $option) {
						echo '<option  data-font="' . esc_attr( $option ) . '" value="'. esc_attr($key) .'"', $meta == $key ? ' selected="selected"' : '', '>', esc_attr($option) , '</option>';
					}
					echo '</select></div>';

					$googlefont_text = __('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789','themecore');

					$hide = " hide";
					if ($key != "none" && $key != "") {
						$hide = "";
					} 

					echo '<p class="'.esc_attr( $field['id'].'_metabox_googlefont_previewer metabox_google_font_preview'.$hide ).'">'. esc_html( $googlefont_text ) .'</p>';
					
					break;
				case 'select':
					$class='';
					if (isset($field['target'])) {
						$field['options'] = themecore_get_select_target_options($field['target']);
					}
					echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
					foreach ($field['options'] as $key => $option) {
						if ($key=='0') {
							$key = __('All the items','themecore');
						}
						echo '<option value="'. esc_attr($key) .'"', $meta == $key ? ' selected' : '', '>', esc_attr($option) , '</option>';
					}
					echo '</select></div>';

					if ( isSet( $field['target'] ) && isSet( $meta ) ) {
						if ($field['target']=="client_names") {
							if ( get_post_type($meta) == 'clients' ) {
								if( themecore_has_password($meta) ){
									echo '<div class="metabox-notice metabox-notice-ok">';
									echo esc_html__('Client selected has password protection.','themecore');
									echo '<br/><strong>';
									echo esc_html__('Gallery password protected.','themecore');
									echo '</strong></div>';
								} else {
									echo '<div class="metabox-notice metabox-notice-no-pass">';
									echo esc_html__('Client selected does not have password protection.','themecore');
									echo '<br/>';
									echo esc_html__('The gallery will be available for everyone.','themecore');
									echo '<br/><br/>';
									echo esc_html__('Add a password to the Client page to protect the gallery.','themecore');
									echo '</div>';
								}
							}
						}
					}
					
					break;

				// Basic text input
				case 'occupants':
					$output = '';

					$max_adults = 'disabled';
					$max_children = 'disabled';
					$max_guests = '0';

					$roomOccupantData = array();

					if ( isset($field['datafrom']) ) {
						if ( 'roomtype' == $field['datafrom'] ) {
							$room = get_posts('post_type=room&numberposts=-1&order=ASC');
							if ($room) {
								foreach($room as $key => $list) {
									$custom = get_post_custom($list->ID);
									if ( isSet($custom[ "pagemeta_max_adult_limit_status"][0]) ) { 
										$adult_limit_status = $custom[ "pagemeta_max_adult_limit_status"][0];
										if ( '1' == $adult_limit_status ) {
											$max_adults = $custom[ "pagemeta_max_adults"][0];
										}
									}
									if ( isSet($custom[ "pagemeta_max_children_limit_status"][0]) ) { 
										$children_limit_status = $custom[ "pagemeta_max_children_limit_status"][0];
										if ( '1' == $children_limit_status ) {
											$max_children = $custom[ "pagemeta_max_children"][0];
										}
									}
									if ( isSet($custom[ "pagemeta_max_guests"][0]) ) { 
										$max_guests = $custom[ "pagemeta_max_guests"][0]; 
									}
									$roomOccupantData[$list->ID]['max_adults'] = $max_adults;
									$roomOccupantData[$list->ID]['max_children'] = $max_children;
									$roomOccupantData[$list->ID]['max_guests'] = $max_guests;
								}
							}
						}
					}
					if ( isset($field['unit']) ) {
						$jsonOccupants = json_encode($roomOccupantData);
						echo "<div class='occupant-".$field['occupant']." occupants-range ranger-min-max-wrap' data-room='' data-occupant='".$field['occupant']."' data-occupants='". $jsonOccupants ."'><span class='ranger-min-value'>".esc_attr($field['min'])."</span>";
						echo '<span class="ranger-max-value">'.esc_attr($field['max']).'</span></div>';
						echo '<div id="' . esc_attr( $field['id'] ) . '_slider"></div>';
						echo '<div class="ranger-bar">';
					}
					if ( !isSet($meta) || $meta=="" ) { 
						if ($meta==0) {$meta="0";} else {$meta=$field['std'];}
					}
					$meta=floatval($meta);
					echo '<input id="' . esc_attr( $field['id'] ) . '" class="of-input input-occupant input-occupant-'.$field['occupant'].'" name="' . esc_attr( $field['id'] ) . '" type="text" value="'.esc_attr($meta).'"';
					
					if ( isset($field['unit']) ) {
						if (isset($field['min'])) {
							echo ' min="' . esc_attr($field['min']);
						}
						if (isset($field['max'])) {
							echo '" max="' . esc_attr($field['max']);
						}
						if (isset($field['step'])) {
							echo '" step="' . esc_attr($field['step']);
						}
						echo '" />';
						if (isset($field['unit'])) {
							echo '<span>' . esc_attr($field['unit']) . '</span>';
						}
						echo '</div>';
					} else {
						echo ' />';
					}
					
				break;

				case 'reservation_for_customer':
			
					print_r ( cognitive_get_reservation_ids_for_customer_id($field['customer_id']) );

					break;

				case 'get_customer_data':

					$customer_array = cognitive_get_customer_array();
					$customer_post_id = cognitive_get_reservation_customer_id( $field['id'] );
					$customer_post_edit = get_edit_post_link( $customer_post_id );
					echo '<a class="button button-primary button-large" href="'.$customer_post_edit.'">Edit Customer</a>';
					$customer_data = cognitive_get_customer_meta_data( $customer_array, $customer_post_id);
			
					echo cognitive_generate_customer_html_list($customer_data);

					break;

				case 'number':
					$output="";

					$roomOccupantData = array();

					if ( isset($field['datafrom']) ) {
						if ( 'roomtype' == $field['datafrom'] ) {


							$room = get_posts('post_type=room&numberposts=-1&order=ASC');
							if ($room) {
								foreach($room as $key => $list) {
									$max_adults = 'disabled';
									$max_children = 'disabled';
									$max_guests = '0';
									$custom = get_post_custom($list->ID);
									if ( isSet($custom[ "pagemeta_max_adult_limit_status"][0]) ) { 
										$adult_limit_status = $custom[ "pagemeta_max_adult_limit_status"][0];
										if ( '1' == $adult_limit_status ) {
											$max_adults = $custom[ "pagemeta_max_adults"][0];
										}
									}
									if ( isSet($custom[ "pagemeta_max_children_limit_status"][0]) ) { 
										$children_limit_status = $custom[ "pagemeta_max_children_limit_status"][0];
										if ( '1' == $children_limit_status ) {
											$max_children = $custom[ "pagemeta_max_children"][0];
										}
									}
									if ( isSet($custom[ "pagemeta_max_guests"][0]) ) { 
										$max_guests = $custom[ "pagemeta_max_guests"][0]; 
									}
									$roomOccupantData[$list->ID]['max_adults'] = $max_adults;
									$roomOccupantData[$list->ID]['max_children'] = $max_children;
									$roomOccupantData[$list->ID]['max_guests'] = $max_guests;
								}
							}
						}
					}
					
					$jsonOccupants = json_encode($roomOccupantData);

					if ( !isSet($meta) || $meta=="" ) { 
						if ($meta==0) {$meta="0";} else {$meta=$field['std'];}
					}
					echo "<div id='" . esc_attr( $field['id'] ) . "_wrap' class='number-input occupant-".$field['occupant']." occupants-range' data-room='0' data-occupant='".$field['occupant']."' min='" . $field['min'] . "' max='" . $field['min'] . "' data-occupants='". $jsonOccupants ."' >";
					echo '<span class="minus-btn">-</span>';
					
					if ( 'child' == $field['occupant'] ) {
						if ( isset($meta['number']) ) {
							$meta_value = $meta['number'];
						} else {
							$meta_value = '0';
						}
						$name_property = $field['id'] . '[number]';
					} else {
						$meta_value = $meta;
						$name_property = $field['id'];
					}
					echo '<input data-guest="'.$field['occupant'].'" data-guestmax="0" data-adultmax="0" data-childmax="0" id="' . esc_attr( $field['id'] ) . '" value="'.esc_attr($meta_value).'" name="' . $name_property . '" type="text" class="number-value" readonly>';
					echo '<span class="plus-btn">+</span>';
					echo '</div>';
					echo '<div class="occupant-'.$field['occupant'].'-notify notify-number-over-max">Exceeds maximum</div>';
					if ( 'child' == $field['occupant'] ) {
						echo '<div class="child-number-max-notice">Maximum occupancy: <span class="child-number-max"></span></div>';
						echo '<div class="combined-child-number-max-notice">Combined occupancy: <span class="combined-child-number-max"></span></div>';
						echo '<div id="guest-age">';
						if ( isset( $meta['number'] ) ) {
							for ($i = 0; $i < $meta['number']; $i++) {
								$age = isset($meta['age'][$i]) ? $meta['age'][$i] : '';
								echo "<input name='pagemeta_reservation_room_children[age][]' type='text' data-counter='" . $i . "' value='" . $age . "' placeholder='Enter age'>";
							}
						}
						echo '</div>';
					} else {
						echo '<div class="adult-number-max-notice">Maximum occupancy: <span class="adult-number-max"></span></div>';
						echo '<div class="combined-adult-number-max-notice">Combined occupancy: <span class="combined-adult-number-max"></span></div>';
					}
					
					
				break;

				case 'range':
					$output="";
					if ( isset($field['unit']) ) {
						echo '<div class="ranger-min-max-wrap"><span class="ranger-min-value">'.esc_attr($field['min']).'</span>';
						echo '<span class="ranger-max-value">'.esc_attr($field['max']).'</span></div>';
						echo '<div id="' . esc_attr( $field['id'] ) . '_slider"></div>';
						echo '<div class="ranger-bar">';
					}
					if ( !isSet($meta) || $meta=="" ) { 
						if ($meta==0) {$meta="0";} else {$meta=$field['std'];}
					}
					$meta=floatval($meta);
					echo '<input id="' . esc_attr( $field['id'] ) . '" class="of-input" name="' . esc_attr( $field['id'] ) . '" type="text" value="'.esc_attr($meta).'"';
					
					if ( isset($field['unit']) ) {
						if (isset($field['min'])) {
							echo ' min="' . esc_attr($field['min']);
						}
						if (isset($field['max'])) {
							echo '" max="' . esc_attr($field['max']);
						}
						if (isset($field['step'])) {
							echo '" step="' . esc_attr($field['step']);
						}
						echo '" />';
						if (isset($field['unit'])) {
							echo '<span>' . esc_attr($field['unit']) . '</span>';
						}
						echo '</div>';
					} else {
						echo ' />';
					}
					
				break;

				case 'radio':
					foreach ($field['options'] as $option) {
						echo '<input type="radio" name="', esc_attr($field['id']), '" value="', esc_attr($option), '"', $meta == $option ? ' checked="checked"' : '', ' />', $option;
					}
					break;

				case 'image':
					$output="";
					foreach ($field['options'] as $key => $option) {
						$selected = '';
						$checked = '';
						if ( $meta == '' ) {
							if ( isSet($field['std']) ) $meta=$field['std'];
							}
						if ( $meta != '' ) {
							if ( $meta == $key ) {
								$selected = ' of-radio-img-selected';
								$checked = ' checked="checked"';
							}
						}
						echo '<input type="radio" id="' . esc_attr( $field['id'] .'_'. $key) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $field['id']) . '" '. esc_attr($checked) .' />';
						echo '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
						echo '<img data-holder="'.esc_attr($field['id'] .'_'. $key).'" data-value="' . esc_attr( $key ) . '" src="' . esc_url( $option ) . '" alt="' . esc_attr($option) .'" class="metabox-image-radio-selector of-radio-img-img' . esc_attr($selected) .'" />';
					}
					break;

				case 'checkbox':
					echo '<input type="checkbox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '"', $meta ? ' checked="checked"' : '', ' />';
					break;
			}
		}

		$notice_class = '';
		if ( isSet($field['type']) && $field['type']=="notice") {
			$notice_class=" big-notice";
		}
		if ( isSet($field['desc']) ) echo '<div class="metabox-description'.esc_attr($notice_class).'">', esc_html($field['desc']), '</div>';
		echo '</div>';
	}

	if ( isSet($div_column_open) && $div_column_open )  {
		echo '</div>';
	}
	
	echo '</div>';
}

/**
 * Save image ids
 */
function themecore_save_proofing_images() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'themecore-nonce-metagallery' ) )
		return;
	
	if ( !current_user_can( 'edit_posts' ) ) return;
 
	$ids = strip_tags(rtrim($_POST['ids'], ','));
	update_post_meta($_POST['post_id'], '_mtheme_proofing_image_ids', $ids);

	// update thumbs
	$thumbs = explode(',', $ids);
	$thumbs_output = '';
	foreach( $thumbs as $thumb ) {
		echo '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
	}

	die();
}
add_action('wp_ajax_themecore_save_proofing_images', 'themecore_save_proofing_images');

/**
 * Save image ids
 */
function themecore_save_images() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'themecore-nonce-metagallery' ) )
		return;
	
	if ( !current_user_can( 'edit_posts' ) ) return;
 
	$ids = strip_tags(rtrim($_POST['ids'], ','));
	update_post_meta($_POST['post_id'], '_mtheme_image_ids', $ids);

	// update thumbs
	$thumbs = explode(',', $ids);
	$thumbs_output = '';
	foreach( $thumbs as $thumb ) {
		echo '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
	}

	die();
}
add_action('wp_ajax_themecore_save_images', 'themecore_save_images');
/**
 * Save image ids
 */
function multo_gallery_save_images() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'themecore-nonce-metagallery' ) )
		return;
	
	if ( !current_user_can( 'edit_posts' ) ) return;
 
	$ids = strip_tags(rtrim($_POST['ids'], ','));
	$galleryid = $_POST['gallerysetid'];
	update_post_meta($_POST['post_id'], $galleryid, $ids);

	$getmeta = get_post_meta( $_POST['post_id'], $galleryid , true );

	// update thumbs
	$thumbs = explode(',', $ids);
	$thumbs_output = '';
	foreach( $thumbs as $thumb ) {
		echo '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
	}

	die();
}
add_action('wp_ajax_multo_gallery_save_images', 'multo_gallery_save_images');
// Save data from meta box
add_action('save_post', 'themecore_checkdata');
function themecore_checkdata($post_id) {

	// verify nonce
	if ( isset($_POST['mtheme_meta_box_nonce']) ) {
		if (!wp_verify_nonce($_POST['mtheme_meta_box_nonce'], 'metabox-nonce')) {
			return $post_id;
		}
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}
	// check permissions
	if ( isset($_POST['post_type']) ) {
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}
	}

	if ( isset($_POST['mtheme_meta_box_nonce']) ) {
		$mtheme_post_type_got =  get_post_type($post_id);

		switch ($mtheme_post_type_got) {
			case 'page':
				$mtheme_common_page_box = themecore_page_metadata();
				themecore_savedata($mtheme_common_page_box,$post_id);
				break;
			case 'clients':
				$mtheme_client_box = themecore_client_metadata();
				themecore_savedata($mtheme_client_box,$post_id);
				break;
			case 'events':
				$events_box = themecore_events_metadata();
				themecore_savedata($events_box,$post_id);
				break;
			case 'portfolio':
				$portfolio_box = themecore_portfolio_metadata();
				themecore_savedata($portfolio_box,$post_id);
				break;
			case 'mtheme_food':
				$mtheme_food_box = themecore_food_metadata();
				themecore_savedata($mtheme_food_box,$post_id);
				break;
			case 'fullscreen':
				$mtheme_fullscreen_box = themecore_fullscreen_metadata();
				themecore_savedata($mtheme_fullscreen_box,$post_id);
				break;
			case 'photostory':
				$mtheme_photostory_box = themecore_photostory_metadata();
				themecore_savedata($mtheme_photostory_box,$post_id);
				break;
			case 'product':
				$mtheme_woocommerce_box = themecore_woocommerce_metadata();
				themecore_savedata($mtheme_woocommerce_box,$post_id);
				break;
			case 'proofing':
				$proofing_box = themecore_proofing_metadata();
				themecore_savedata($proofing_box,$post_id);
				break;
			case 'room':
				$mtheme_room_box = themecore_room_metadata();
				themecore_savedata($mtheme_room_box,$post_id);
				break;
			case 'reservations':
				$reservations_box = themecore_reservations_metadata();
				themecore_savedata($reservations_box,$post_id);
				break;
			case 'customers':
				$customers_box = themecore_customers_metadata();
				themecore_savedata($customers_box,$post_id);
				break;
			case 'post':
				$mtheme_post_metapack = themecore_post_metadata();

				themecore_savedata($mtheme_post_metapack['video'],$post_id);
				themecore_savedata($mtheme_post_metapack['link'],$post_id);
				themecore_savedata($mtheme_post_metapack['image'],$post_id);
				themecore_savedata($mtheme_post_metapack['quote'],$post_id);
				themecore_savedata($mtheme_post_metapack['audio'],$post_id);
				themecore_savedata($mtheme_post_metapack['main'],$post_id);
				break;
			
			default:
				# code...
				break;
		}
	}
	
}

	function themecore_savedata($mtheme_metaboxdata,$post_id) {

		if (is_array($mtheme_metaboxdata['fields'])) {
			foreach ($mtheme_metaboxdata['fields'] as $field) {
				$old = get_post_meta($post_id, $field['id'], true);
				$new = '';
				if ( isset($_POST[$field['id']]) ) {
					$new = $_POST[$field['id']];
				}
				
				if ( isSet($new) ) {
					if ($new && $new != $old) {
						delete_post_meta( $post_id , $field['id']);
						update_post_meta( $post_id, $field['id'], $new );
					} elseif ($new=="0") {
						delete_post_meta( $post_id , $field['id']);
						update_post_meta( $post_id, $field['id'], $new );
					} elseif ('' == $new && $old) {
						delete_post_meta( $post_id, $field['id'], $old );
					}
				}

			}
		}
	}
?>