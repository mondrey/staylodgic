<?php
function imaginem_codepack_share_post() {

	$page_id = get_the_id();
	$link = get_permalink( $page_id );

	$media = imaginem_codepack_featured_image_link( $page_id );
	$title = get_the_title( $page_id );
	
	$socialshare = array();
	
	$share_facebook = true;
	if ( $share_facebook ) {
		$socialshare['facebook'] = array (
					'fab fa-facebook-f' => 'https://www.facebook.com/sharer.php?u='. esc_url( $link ) .'&t='. urlencode( $title )
		);
	}
	$share_twitter = true;
	if ( $share_twitter ) {
		$socialshare['twitter'] = array (
					'fab fa-twitter' => 'https://twitter.com/home?status='.urlencode( $title ).'+'. esc_url( $link )
				);
	}
	$share_linkedin = true;
	if ( $share_linkedin ) {
		$socialshare['linkedin'] = array (
					'fab fa-linkedin-in' => 'https://linkedin.com/shareArticle?mini=true&amp;url='.esc_url( $link ).'&amp;title='.urlencode( $title )
				);
	}
	$share_googleplus = true;
	if ( $share_googleplus ) {
		$socialshare['googleplus'] = array (
					'fab fa-google-plus-g' => 'https://plus.google.com/share?url='. esc_url( $link )
				);
	}
	$share_reddit = true;
	if ( $share_reddit ) {
		$socialshare['reddit'] = array (
					'fab fa-reddit-alien' => 'https://reddit.com/submit?url='.esc_url( $link ).'&amp;title='.urlencode( $title )
				);
	}
	$share_tumblr = true;
	if ( $share_tumblr ) {
		$socialshare['tumblr'] = array (
					'fab fa-tumblr' => 'https://www.tumblr.com/share/link?url='.esc_url( $link ).'&amp;name='.urlencode( $title ).'&amp;description='.urlencode( $title )
				);
	}
	$share_pinterest = true;
	if ( $share_pinterest ) {
		$socialshare['pinterest'] = array (
					'fab fa-pinterest-p' => 'https://pinterest.com/pin/create/bookmarklet/?media=' .esc_url( $media ) .'&url='. esc_url( $link ) .'&is_video=false&description='.urlencode( $title )
				);
	}
	$share_email = true;
	if ( $share_email ) {
	$socialshare['email'] = array (
				'far fa-envelope' => 'mailto:email@address.com?subject=Interesting%20Link&body=' . $title . " " .  esc_url( $link )
			);
	}

	$postshare = '';
	$postshare .= '<div class="post-element-share-toggle"> ';
	$postshare .= '<div class="post-element-share">';
	$postshare .= '<div class="post-element-share-icon">';
	$postshare .= '<span>share</span>';
	$postshare .= '<div class="post-element-share-btn"><i class="fas fa-share-alt"></i></div>';
	$postshare .= '</div>';
	$postshare .= '</div>';
	$postshare .= '<div id="post-element-share-hide" class="post-element-share-active">';
	$postshare .= '<ul class="post-element-share-list">';

	$count = 0;
	foreach($socialshare as $key => $share){
	  foreach( $share as $icon => $url){
		$count ++;
		$postshare .= '<li class="post-element-share-'.esc_attr($key).'"><a target="_blank" href="'. esc_url( $url ).'"><i class="'.$icon.'"></i></a></li>';
	  }
	}
	
	$postshare .= '</ul>';
	$postshare .= '</div>';
	$postshare .= '</div>';

	echo $postshare;
}
add_action( 'superlens_postshare', 'imaginem_codepack_share_post' );
function imaginem_codepack_multi_image( $the_image_id, $have_image_url, $link, $type, $the_image_title, $class, $lazyload = false, $srcset_status = false, $srcset_prefix = '' ) {

	if ( '' === $type ) {
		$type = 'fullsize';
	}
	$output = '';

	$image_url   = wp_get_attachment_image_src( $the_image_id, $type );
	$image_url   = $image_url[0];
	$img_obj     = get_post( $the_image_id );
	$img_alt     = get_post_meta( $the_image_id, '_wp_attachment_image_alt', true );
	$permalink   = get_permalink( $the_image_id );
	$srcset      = wp_get_attachment_image_srcset( $the_image_id, $type );
	$srcsetsizes = wp_get_attachment_image_sizes( $the_image_id, $type );

	if ( true === $link ) {
		$output = '<a href="' . esc_url( $permalink ) . '">';
	}

	$fallback_image = imageinem_codepack_get_placeholder_image( $type );

	$data_src     = '';
	$fallback_src = '';
	$data_srcset  = '';
	$srcsizes     = 'sizes="' . $srcsetsizes . '"';

	if ( 'true' === $lazyload ) {
		$data_src     = 'data-';
        $data_srcset  = 'data-srcset="' . $srcset . '" ';
        $srcsizes     = 'data-sizes="' . $srcsetsizes . '"';
		$fallback_src = 'src="' . esc_url( $fallback_image ) . '" ';
		$class        = $class . ' lazyload';
	} else {
		$data_srcset = 'srcset="' . $srcset . '" ';
    }
    if ( ! $srcset_status ) {
		$data_src     = 'data-flickity-lazyload-';
        $data_srcset  = 'data-flickity-lazyload-srcset="' . $srcset . '" ';
        $srcsizes     = 'data-sizes="' . $srcsetsizes . '"';
		$fallback_src = 'src="' . esc_url( $fallback_image ) . '" ';
		$class        = $class . '';
    }
	if ( $have_image_url ) {
		$have_image_id = imageinem_codepack_get_placeholder_image( $have_image_url );
		$img_alt       = imageinem_codepack_alt_text( $have_image_id );
		$output       .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $have_image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
	} else {
		if ( isset( $image_url ) && '' !== $image_url ) {
			if ( $class ) {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
			} else {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" />';
			}
		}
	}

	if ( true === $link ) {
		$output .= '</a>';
	}

	return $output;
}
if ( !function_exists( 'imaginem_codepack_display_client_details' ) ) {
function imaginem_codepack_display_client_details($client_id , $title = false , $desc = false , $client_time = false, $client_location = false , $proofing_id = false , $pagetitle = false ) {

    if ( isSet($client_id) ) {
        $post_thumbnail_id = get_post_thumbnail_id( $client_id );
        $image_data = wp_get_attachment_image_src( $post_thumbnail_id, 'superlens-gridblock-square-big', false );
        $image_link = $image_data[0];
        $client_data = get_post_custom($client_id);
        $proofing_data = get_post_custom( $proofing_id );
        $client_name = '';
        $client_desc = '';
        if (isset($proofing_data['pagemeta_proofing_startdate'][0])) $proofing_startdate=$proofing_data['pagemeta_proofing_startdate'][0];
        if (isset($proofing_data['pagemeta_proofing_location'][0])) $proofing_location=$proofing_data['pagemeta_proofing_location'][0];
        if (isset($client_data['pagemeta_client_name'][0])) {
            $client_name=$client_data['pagemeta_client_name'][0];
        }
        if (isset($client_data['pagemeta_client_desc'][0])) {
            $client_desc=$client_data['pagemeta_client_desc'][0];
        }

        $client_info = '<div class="proofing-client-wrap">';
        
            $client_info .= '<div class="proofing-client-image"><img src="'.$image_link.'" alt="client" /></div>';
            $client_info .= '<div class="proofing-client-info-wrap">';
                if ($pagetitle) {
                    $client_info .= '<div class="proofing-page-title"><h1 class="entry-title">'.get_the_title( get_the_id() ).'</h1></div>';
                }
                if ($title) {
                    $client_info .= '<div class="proofing-client-title">'.$client_name.'</div>';
                }
                if ( is_singular('proofing') || is_singular('clients') ) {

                    $password_protected = false;
                    if ( is_singular('proofing') ) {
                        $client_id_check = get_post_meta( get_the_id() , 'pagemeta_client_names', true);
                        if ( isSet($client_id_check) ) {
                            if ( post_password_required($client_id_check) ) {
                                $password_protected = true;
                            }
                        }
                    }
                    if ( is_singular('clients') ) {
                        if ( post_password_required() ) {
                            $password_protected = true;
                        }
                    }
                    if ($desc) {
                        $client_info .= '<div class="proofing-client-desc">'.$client_desc.'</div>';
                    }
                }
                if ( $client_time || $client_location ) {
                    $client_info .= '<ul class="event-details event-date-time">';
                    if ($client_time) {
                        if ( isSet($proofing_startdate) && $proofing_startdate<>"" ) {
							$new_proofing_startdate = explode( ' ', $proofing_startdate );
							$formatted_proofing_startdate = date_i18n( get_option( 'date_format' ), strtotime( $new_proofing_startdate[0] ) );
                            $client_info .= '<li><i class="feather-icon-clock"></i>' .$formatted_proofing_startdate . ' ' . $new_proofing_startdate[1].'</li>';
                        }
                    }
                    if ($client_location) {
                        if ( isSet($proofing_location) && $proofing_location<>"" ) {
                            $client_info .= '<li><i class="feather-icon-map"></i>' .$proofing_location.'</li>';
                        }
                    }
                    $client_info .= '</ul>';
                }
            $client_info .= '</div>';
        $client_info .= '</div>';
    }

    return $client_info;

}
}
if ( !function_exists( 'imaginem_codepack_proofing_client_single_info' ) ) {
function imaginem_codepack_proofing_client_single_info($id,$pagetitle,$client_name,$client_desc,$client_time,$client_location) {

    $custom = get_post_custom( $id );
    $proofing_status = '';
    $proofing_download = '';

    if (isset($custom['pagemeta_proofing_status'][0])) $proofing_status=$custom['pagemeta_proofing_status'][0];
    if (isset($custom['pagemeta_proofing_download'][0])) $proofing_download=$custom['pagemeta_proofing_download'][0];
    if (isset($custom['pagemeta_client_names'][0])) $client_id=$custom['pagemeta_client_names'][0];

    $proofing_client_info = '';
    $proofing_client_info .= '<div class="proofing-content-wrap">';
        $proofing_client_info .= '<div class="proofing-content">';
            $proofing_locked_msg = esc_html__("Proofing gallery selection has been locked.","superlens");
            $proofing_active_msg = esc_html__("Proofing gallery is active for selection.","superlens");

            $proofing_disable_msg = esc_html__("Please contact us to activate this proofing gallery.","superlens");
            $proofing_download_msg = esc_html__("Proofing gallery Locked for Download.","superlens");
            if ( post_password_required() ) {
                // If password
            } else {
                    if ( isSet($client_id) && $client_id<>"" && $client_id<>"none" ) {
                        $proofing_client_info .= '<div class="entry-content client-gallery-details proofing-client-details">';
                        $proofing_client_info .= '<div class="proofing-client-details-inner">';
                        $proofing_client_info .= imaginem_codepack_display_client_details( $client_id , $client_name , $client_desc , $client_time, $client_location , $proofing_id = get_the_id() , $pagetitle );
                        if ( isSet($proofing_download) && $proofing_download <>"" && $proofing_status == "download") {
                        $button_style = "";
                        $proofing_client_info .= '<div class="button-shortcode '.esc_attr($button_style).' proofing-gallery-button">';
                            $proofing_client_info .= '<a target="_blank" href="'.esc_url($proofing_download).'">';
                                $proofing_client_info .= '<div class="mtheme-button big-button">';
                                    $proofing_client_info .= '<i class="fa fa-download"></i> ';
                                    $proofing_client_info .= esc_html__('Download','superlens');
                                $proofing_client_info .= '</div>';
                            $proofing_client_info .= '</a>';
                        $proofing_client_info .= '</div>';
                        }
                        $proofing_client_info .= '</div>';
                        $proofing_client_info .= '</div>';
                    }
                ?>
            <?php
            // end of password check
            }
        $proofing_client_info .= '</div>';
    $proofing_client_info .= '</div>';

    return $proofing_client_info;
}
}
if ( !function_exists( 'imaginem_codepack_show_continue_reading' ) ) {
    function imaginem_codepack_show_continue_reading( $continue_text , $link ) {
        $output = '<div class="centered-container">';
        if ( $link ) {
            $output .= '<a class="arrow-link link--arrowed" href="'.esc_url( $link ).'">';
        } else {
            $output .= '<span class="arrow-link link--arrowed">';
        }
		$output .= '<span class="continue-icon-wrap"><svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32">
		<g fill="none" stroke="#000" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
		<circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
		<path class="arrow-icon--arrow" d="M16.14 9.93L22.21 16l-6.07 6.07M8.23 16h13.98"></path>
		</g>
		</svg></span>';
            $output .=  '<span class="continue-text">'.esc_html( $continue_text ).'</span>';
        if ( $link ) {    
            $output .= '</a>';
        } else {
            $output .= '</span>';
        }
        $output .= '</div>';
        return $output;
    }
}
if ( !function_exists( 'imaginem_codepack_get_image_id_from_url' ) ) {
    function imaginem_codepack_get_image_id_from_url($image_url) {
        $attachment = attachment_url_to_postid($image_url);
        if ( $attachment ) {
            return $attachment;
        } else {
            return false;
        }
    }
}
class mtheme_MegaMenu_Nav_Menu extends Walker_Nav_Menu {
    /**
        * @see Walker_Nav_Menu::start_lvl()
        * @since 3.0.0
        *
        * @param string $output Passed by reference.
        * @param int $depth Depth of page.
        */
    function start_lvl(&$output, $depth = 0, $args = array()) {}

    /**
        * @see Walker_Nav_Menu::end_lvl()
        * @since 3.0.0
        *
        * @param string $output Passed by reference.
        * @param int $depth Depth of page.
        */
    function end_lvl(&$output, $depth = 0, $args = array()) {}

    /**
        * @see Walker::start_el()
        * @since 3.0.0
        *
        * @param string $output Passed by reference. Used to append additional content.
        * @param object $item Menu item data object.
        * @param int $depth Depth of menu item. Used for padding.
        * @param int $current_page Menu item ID.
        * @param object $args
        */
    function start_el(&$output, $object, $depth = 0, $args = array(), $id = 0) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr( $object->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $object->type ) {
            $original_title = get_term_field( 'name', $object->object_id, $object->object, 'raw' );
        } elseif ( 'post_type' == $object->type ) {
            $original_object = get_post( $object->object_id );
            $original_title = $original_object->post_title;
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $object->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $object->title;

        if ( isset( $object->post_status ) && 'draft' == $object->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)','mthemelocal'), $object->title );
        }

        $title = empty( $object->label ) ? $title : $object->label;
        $value = get_post_meta( $item_id, 'menu-item-megamenu-'.$item_id,true);
        $value = ($value=="on") ? "checked='checked'"  : "";

        $type = get_post_meta( $item_id, 'menu-item-megamenu-layout-'.$item_id,true);

        $menu_columns = get_post_meta( $item_id, 'mega-menu-columns-'.$item_id,true);
        $enable_textbox = get_post_meta( $item_id, 'menu-item-enable-textbox-'.$item_id,true);
        $enable_textbox= ($enable_textbox=="on") ? "checked='checked'"  : "";

        $textbox = get_post_meta( $item_id, 'menu-item-textbox-'.$item_id,true);

        $multi_classes = implode(' ', $classes );
?>
        <li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo esc_attr($multi_classes); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title"><?php echo esc_html( $title ); ?></span>
                    <span class="item-controls">
                        <span class="item-type item-type-mtheme-default"><?php echo esc_html( $object->type_label ); ?></span>
                        <a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php _e('Edit Menu Item','mtheme_local'); ?>" href="<?php
                            echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><span class="screen-reader-text"><?php _e( 'Edit Menu Item' ,'mtheme_local'); ?></span>
                        </a>
                    </span>
                </dt>
            </dl>

            <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
                <?php if( 'custom' == $object->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
                            <?php _e( 'URL','mtheme_local' ); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->url ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-wide">
                    <label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
                        <?php _e( 'Navigation Label','mtheme_local' ); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->title ); ?>" />
                    </label>
                </p>
                <p class="field-title-attribute field-attr-title description description-wide">
                    <label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
                        <?php _e( 'Title Attribute','mtheme_local' ); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo sanitize_text_field( $object->post_excerpt ); ?>" />
                    </label>
                </p>

                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $object->target, '_blank' ); ?> />
                        <?php _e( 'Open link in a new tab','mtheme_local'); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
                        <?php _e( 'CSS Classes (optional)','mtheme_local' ); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $object->classes ) ); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
                        <?php _e( 'Link Relationship (XFN)','mtheme_local' ); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->xfn ); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
                        <?php _e( 'Description','mtheme_local' ); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $object->description ); ?></textarea>
                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.','mtheme_local'); ?></span>
                    </label>
                </p>

                <div class="menu-item-actions description-wide submitbox">
                    <?php
                    /* New fields insertion starts here */
                    if($depth == 0) {
                    ?>  
                    <p class="mtheme_megamenu_box mtheme_megamenu_choice clearfix">
                        <label for="menu-item-megamenu-<?php echo esc_attr($item_id); ?>"><?php _e('Mega Menu','mtheme_local'); ?></label>
                        <input type="checkbox" id="menu-item-megamenu-<?php echo esc_attr($item_id); ?>"  name="menu-item-megamenu-<?php echo esc_attr($item_id); ?>" <?php echo esc_attr($value); ?> />
                        <input type="hidden" name="menu-item-megamenu-layout-<?php echo esc_attr($item_id); ?>" id="menu-item-megamenu-layout-<?php echo esc_attr($item_id); ?>" value="column" />
                        <select name="mega-menu-columns-<?php echo esc_attr($item_id); ?>" id="mega-menu-columns-<?php echo esc_attr($item_id); ?>">
                            <option <?php if($menu_columns == "2 Columns") echo("selected") ?>><?php _e('2 Columns','mtheme_local'); ?></option>
                            <option <?php if($menu_columns == "3 Columns") echo("selected") ?>><?php _e('3 Columns','mtheme_local'); ?></option>
                            <option <?php if($menu_columns == "4 Columns") echo("selected") ?>><?php _e('4 Columns','mtheme_local'); ?></option>
                        </select>
                    </p> 
                    <?php
                    }
                    /* New fields insertion ends here */
                    ?>
                    <?php
                    //}
                    /* New fields insertion ends here */
                    ?>
                    <?php if( 'custom' != $object->type ) : ?>
                        <p class="link-to-original">
                            <?php printf( __('Original:','mtheme_local').' %s', '<a href="' . esc_attr( $object->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
                        echo wp_nonce_url(
                                add_query_arg(
                                        array(
                                                'action' => 'delete-menu-item',
                                                'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'delete-menu_item_' . $item_id
                        ); ?>"><?php _e('Remove','mtheme_local'); ?>
                    </a> 
                    <span class="meta-sep"> | </span> 
                    <a class="item-cancel submitcancel" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php   echo add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) );
                            ?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php _e('Cancel','mtheme_local'); ?>
                    </a>
                </div>
                <input type="hidden" name="menu-item-megamenu-label-<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr($object->title); ?>" />
                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->object_id ); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->object ); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->menu_item_parent ); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->menu_order ); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $object->type ); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
            <?php
            $output .= ob_get_clean();
    }

    /**
        * @see Walker::end_el()
        * @since 3.0.0
        *
        * @param string $output Passed by reference. Used to append additional content.
        * @param object $item Page data object. Not used.
        * @param int $depth Depth of page. Not Used.
        */
    function end_el(&$output, $object, $depth = 0, $args = array(), $id = 0) {
        $output .= "</li>\n";
    }
}
add_filter( 'wp_edit_nav_menu_walker', 'mtheme_extend_adminsettings_walker' , 100);
if ( !function_exists( 'mtheme_extend_adminsettings_walker' ) ) {
    function mtheme_extend_adminsettings_walker($name) {
        return 'mtheme_MegaMenu_Nav_Menu';
    }
}

add_action( 'wp_update_nav_menu_item', 'mtheme_update_menu', 100, 3);
if ( !function_exists( 'mtheme_update_menu' ) ) {
    function mtheme_update_menu($menu_id, $menu_item_db) {
        $menu_label = '';
        if (isset($_POST["menu-item-megamenu-label-".$menu_item_db])) {
            $value = $_POST["menu-item-megamenu-label-".$menu_item_db];
        }
        
        $value = '';
        $value_type = '';
        $value_textbox ='';
        $value_item_textbox ='';
        $value_menu_columns ='';
        
        if (isset($_POST['menu-item-megamenu-'.$menu_item_db])) {
            $value = $_POST['menu-item-megamenu-'.$menu_item_db];
        }
        if (isset($_POST['menu-item-megamenu-layout-'.$menu_item_db])) {
            $value_type = $_POST['menu-item-megamenu-layout-'.$menu_item_db];
        }
        if (isset($_POST['mega-menu-columns-'.$menu_item_db])) {
            $value_menu_columns = $_POST['mega-menu-columns-'.$menu_item_db];
        }

        update_post_meta( $menu_item_db, 'menu-item-megamenu-'.$menu_item_db , $value );
        update_post_meta( $menu_item_db, 'menu-item-megamenu-layout-'.$menu_item_db , $value_type );
        update_post_meta( $menu_item_db, 'mega-menu-columns-'.$menu_item_db, $value_menu_columns );
    }
}
class mtheme_Menu_Megamenu extends Walker_Nav_Menu  {
    /**
        * @see Walker::$tree_type
        * @since 3.0.0
        * @var string
        */
    var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
    
    var $choice_of_menu = "none";
    var $megamenu_active = false;
    var $menu_row_counter = 0;

    /**
        * @see Walker::$db_fields
        * @since 3.0.0
        * @todo Decouple this.
        * @var array
        */
    var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

    /**
        * @see Walker::start_lvl()
        * @since 3.0.0
        *
        */
    function start_lvl(&$output, $depth = 0, $args = array()) {
    global $wp_query;
        $indent = str_repeat("\t", $depth);
        $element = 'ul'; $widget_class = '';
        $this->menu_row_counter = 0;
        if( $depth==0 && $this->megamenu_active ) {
            $element = 'ul';
        }
        
        $output .= "\n$indent<{$element} class=\"children children-depth-".$depth." clearfix  $widget_class \">\n";
    }

    /**
        * @see Walker::end_lvl()
        * @since 3.0.0
        *
        * @param string $output Passed by reference. Used to append additional content.
        * @param int $depth Depth of page. Used for padding.
        */
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);

        $element = 'ul';
        $output .= "$indent</{$element}>\n";

    }

    /**
        * @see Walker::start_el()
        * @since 3.0.0
        *
        * @param string $output Passed by reference. Used to append additional content.
        * @param object $item Menu item data object.
        * @param int $depth Depth of menu item. Used for padding.
        * @param int $current_page Menu item ID.
        * @param object $args
        */
    function start_el(&$output, $object, $depth = 0, $args = array(), $id = 0) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $value = get_post_meta( $object->ID, 'menu-item-megamenu-'.$object->ID,true);
        $value = ($value=="on") ? true  : false ;

        $type = get_post_meta( $object->ID, 'menu-item-megamenu-layout-'.$object->ID,true);

        $enable_textbox = get_post_meta( $object->ID, 'menu-item-enable-textbox-'.$object->ID,true);
        $textbox = get_post_meta( $object->ID, 'menu-item-textbox-'.$object->ID,true);

        if($depth==0) {
            $this->megamenu_active = $value;

            if($this->megamenu_active) {
                if($type=="column")
                    $this->choice_of_menu = "column";
            }

        }

        $classes = empty( $object->classes ) ? array() : (array) $object->classes;
        $classes[] = 'menu-item-' . $object->ID;
        $ex_class = '';
        
        $megaItem=($this->megamenu_active)? "mega-item":"";

        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $object, $args ) );
        $menu_columns=get_post_meta( $object->ID, 'mega-menu-columns-'.$object->ID,true);
        if($menu_columns && $this->megamenu_active){
            $menu_column_class = " mega_width ";

            if($menu_columns == "2 Columns"){
            $menu_column_class .= "mega-two";
            }elseif($menu_columns == "3 Columns"){
            $menu_column_class .= "mega-three";
            }else{
            $menu_column_class .= "mega-four";
            }
            $class_names.=$menu_column_class." ";
        }
        $class_names = ' class="' . esc_attr( $class_names )." $ex_class $megaItem ". '   "';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $object->ID, $object, $args );
        $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';


        if(!$this->megamenu_active) {     
            $output .= $indent . '<li' . $id  . $class_names .'>';

            $attributes  = ! empty( $object->attr_title ) ? ' title="'  . sanitize_text_field( $object->attr_title ) .'"' : '';
            $attributes .= ! empty( $object->target )     ? ' target="' . esc_attr( $object->target     ) .'"' : '';
            $attributes .= ! empty( $object->xfn )        ? ' rel="'    . esc_attr( $object->xfn        ) .'"' : '';
            $attributes .= ! empty( $object->url )        ? ' href="'   . esc_url( $object->url        ) .'"' : '';

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters( 'the_title', $object->title, $object->ID ) . $args->link_after;
            $item_output .= '</a> ';
            $item_output .= $args->after;

        } elseif ($this->choice_of_menu=="column") {
            
            if ($depth==1) {
                $output .= $indent . '<li '.$class_names.'><div' . $id  . $class_names .'>';
                $this->menu_row_counter++;
            } else {
                $output .= $indent . '<li' . $id .  $class_names .'>';
            }
            
            $attributes  = ! empty( $object->attr_title ) ? ' title="'  . sanitize_text_field( $object->attr_title ) .'"' : '';
            $attributes .= ! empty( $object->target )     ? ' target="' . esc_attr( $object->target     ) .'"' : '';
            $attributes .= ! empty( $object->xfn )        ? ' rel="'    . esc_attr( $object->xfn        ) .'"' : '';
            $attributes .= ! empty( $object->url )        ? ' href="'   . esc_url( $object->url        ) .'"' : '';

            $item_output = $args->before;
            if($depth==1) {
                $item_output .= '<h6>';
            } else {
                $item_output .= '<a'. $attributes .'>';
            }


            $item_output .= $args->link_before . apply_filters( 'the_title', $object->title, $object->ID ) . $args->link_after;

            if($depth==1) {
                $item_output .= '</h6>';
            } else {
                $item_output .= '</a>';
            }

            $item_output .= $args->after;

        }
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $object, $depth, $args );
    }

    /**
        * @see Walker::end_el()
        * @since 3.0.0
        *
        * @param string $output Passed by reference. Used to append additional content.
        * @param object $item Page data object. Not used.
        * @param int $depth Depth of page. Not Used.
        */
    function end_el(&$output, $object, $depth = 0, $args = array(), $current_object_id = 0){
        if(!$this->megamenu_active) {
            $output .= "</li>\n";
        } else {
            if($depth==1) {
                $output .= "</div>\n</li>";
            } else {
                $output .= "</li>\n";
            }
        }
    }
}
if ( !function_exists( 'imaginem_codepack_get_svg_icon_url_of' ) ) {
    function imaginem_codepack_get_svg_icon_url_of($icon){

    	$url = false;
    	
    	if (strpos($icon, 'et-icon-') !== false) {
    	    $url = get_template_directory_uri() . '/css/fonts/svgs/et-icon/' . $icon . '.svg';
    	}
    	if (strpos($icon, 'ion-') !== false) {
    	    $url = get_template_directory_uri() . '/css/fonts/svgs/ionicon/' . $icon . '.svg';
    	}
    	if (strpos($icon, 'simpleicon-') !== false) {
    	    $url = get_template_directory_uri() . '/css/fonts/svgs/simpleicon/' . $icon . '.svg';
    	}

    	return $url;
    }
}
if ( !function_exists( 'imaginem_codepack_get_grid_masonry_class' ) ) {
    function imaginem_codepack_get_grid_masonry_class($format) {
    	$gridblock_is_masonary = "";
    	if ( $format == "masonary") {
    		$gridblock_is_masonary = "gridblock-masonary";
        }
    	if ( $format == "scatter") {
    		$gridblock_is_masonary = "gridblock-scatter";
    	}
    	return $gridblock_is_masonary;
    }
}
if ( !function_exists( 'imaginem_codepack_get_grid_column_type' ) ) {
    function imaginem_codepack_get_grid_column_type($columns) {
        $column_type="four";
    	if ($columns==5) { 
    		$column_type="five";
    	}
    	if ($columns==4) { 
    		$column_type="four";
    	}
    	if ($columns==3) { 
    		$column_type="three";
    	}
    	if ($columns==2) { 
    		$column_type="two";
    	}
    	if ($columns==1) { 
    		$column_type="one";
    	}

    	return $column_type;
    }
}
if ( !function_exists( 'imaginem_codepack_grid_relayout_class' ) ) {
    function imaginem_codepack_grid_relayout_class($format,$columns) {
    	$relayout_on_image_class = "";
    	if ($columns==1) { 
    		$relayout_on_image_class = "relayout-on-image-load";
    	}
    	if ( $format == "masonary") {
    		$relayout_on_image_class = "relayout-on-image-load";
        }
    	if ( $format == "scatter") {
    		$relayout_on_image_class = "relayout-on-image-load";
    	}

    	return $relayout_on_image_class;
    }
}
if ( !function_exists( 'imaginem_codepack_get_grid_image_size' ) ) {
    function imaginem_codepack_get_grid_image_size($format,$columns) {
    	if ($columns==5) { 
    		$portfolioImage_type="superlens-gridblock-large";
    	}
    	if ($columns==4) { 
    		$portfolioImage_type="superlens-gridblock-large";
    	}
    	if ($columns==3) { 
    		$portfolioImage_type="superlens-gridblock-large";
    	}
    	if ($columns==2) { 
    		$portfolioImage_type="superlens-gridblock-large";
    	}
    	if ($columns==1) { 
    		$relayout_on_image_class = "relayout-on-image-load";
    		$portfolioImage_type="superlens-gridblock-full";
    	}

        if ( $format == "portrait") {
            if ($columns==5) { 
                $portfolioImage_type="superlens-gridblock-large-portrait";
            }
            if ($columns==4) { 
                $portfolioImage_type="superlens-gridblock-large-portrait";
            }
            if ($columns==3) { 
                $portfolioImage_type="superlens-gridblock-large-portrait";
            }
            if ($columns==2) {
                $portfolioImage_type="superlens-gridblock-large-portrait";
            }
            if ($columns==1) {
                $relayout_on_image_class = "relayout-on-image-load";
                $portfolioImage_type="superlens-gridblock-full";
            }
        }

    	if ( $format == "square") {
    		if ($columns==5) { 
    			$portfolioImage_type="superlens-gridblock-square-big";
    		}
    		if ($columns==4) { 
    			$portfolioImage_type="superlens-gridblock-square-big";
    		}
    		if ($columns==3) { 
    			$portfolioImage_type="superlens-gridblock-square-big";
    		}
    		if ($columns==2) {
    			$portfolioImage_type="superlens-gridblock-square-big";
    		}
    		if ($columns==1) {
    			$relayout_on_image_class = "relayout-on-image-load";
    			$portfolioImage_type="superlens-gridblock-full";
    		}
    	}
    	$gridblock_is_masonary = "";
    	if ( $format == "masonary") {

    		$gridblock_is_masonary = "gridblock-masonary ";
            $relayout_on_image_class = "relayout-on-image-load";
            
    		if ($columns==5) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==4) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==3) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==2) {
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==1) {
    			$portfolioImage_type="superlens-gridblock-full";
    		}
        }
    	if ( $format == "scatter") {

    		$gridblock_is_masonary = "gridblock-scatter ";
            $relayout_on_image_class = "relayout-on-image-load";
            
    		if ($columns==5) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==4) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==3) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==2) {
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==1) {
    			$portfolioImage_type="superlens-gridblock-full";
    		}
    	}
    	if ( $format == "card") {
			
    		if ($columns==5) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==4) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==3) { 
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==2) {
    			$portfolioImage_type="superlens-gridblock-full-medium";
    		}
    		if ($columns==1) {
    			$portfolioImage_type="superlens-gridblock-full";
    		}
    	}

    	return $portfolioImage_type;
    }
}
if ( !function_exists( 'imaginem_codepack_display_elementloader' ) ) {
    function imaginem_codepack_display_elementloader($intensity="default") {
    	return '<svg class="materialcircular materialcircular-'.$intensity.'" height="50" width="50">
      <circle class="materialpath" cx="25" cy="25" r="20" fill="none" stroke-width="6" stroke-miterlimit="10" />
    </svg>';
    }
}
if ( !function_exists( 'imaginem_codepack_description_limitter' ) ) {
    function imaginem_codepack_description_limitter( $excerpt = '' , $limit=20 ,$suffix = '') {

    	$the_text = '';

		$wstring = explode( '\n', wordwrap($excerpt, $limit, '\n' ) );
		
		return $wstring[0] . $suffix;
    }
}
if ( !function_exists( 'imaginem_codepack_trim_sentence' ) ) {
    function imaginem_codepack_trim_sentence($desc="",$charlength=20,$end = true, $suffix = '') {
    	$excerpt = $desc;

    	$the_text="";

    	if ( mb_strlen( $excerpt ) > $charlength ) {
    		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
    		$exwords = explode( ' ', $subex );
    		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
    		if ( $excut < 0 ) {
    			$the_text = mb_substr( $subex, 0, $excut );
    		} else {
    			$the_text = $subex;
    		}
			if ( $end ) {
				if ( '' !== $suffix) {
					$the_text .= $suffix;
				} else {
					$the_text .= '[...]';
				}
    			
			}
    	} else {
    		$the_text = $excerpt;
    	}
    	return $the_text;
    }
}
if ( !function_exists( 'imaginem_codepack_get_portfolio_icon' ) ) {
    function imaginem_codepack_get_portfolio_icon( $postformat = "image" ) {
    	switch ($postformat) {
    		case 'directlink':
    			$postformat_icon = "ion-ios-plus-empty";
    			break;
    		case 'lightbox':
    			$postformat_icon = "ion-ios-search";
    			break;
    		case 'link':
    			$postformat_icon = "ion-ios-arrow-thin-up";
    			break;
    		case 'play':
    			$postformat_icon = "ion-ios-play-outline";
    			break;
    		case 'cross':
    			$postformat_icon = "ion-ios-close-empty";
    			break;
    		case 'proofing-check':
    			$postformat_icon = "ion-ios-heart-outline";
    			break;
    		case 'proofing-cross':
    			$postformat_icon = "ion-ios-heart";
    			break;
    		case 'check':
    			$postformat_icon = "ion-ios-checkmark-outline";
    			break;
    		case 'download':
    			$postformat_icon = "ion-ios-download-outline";
    			break;
    		case 'purchase':
    			$postformat_icon = "ion-ios-cart-outline";
    			break;
    		case 'albums':
    			$postformat_icon = "ion-ios-albums-outline";
    			break;
    		case 'selected':
    			$postformat_icon = "ion-ios-heart";
    			break;
            case 'locked':
                $postformat_icon = "ion-ios-locked-outline";
                break;
            case 'editor-recommended-unchecked':
                $postformat_icon = "ion-android-radio-button-off";
                break;
    		case 'editor-recommended':
    			$postformat_icon = "ion-android-radio-button-on";
    			break;
    		case 'ajax':
    			$postformat_icon = "ion-ios-eye-outline";
    			break;
    		default:
    			$postformat_icon = "ion-ios-plus-empty";
    			break;
    	}

    	return $postformat_icon;
    }
}
if ( !function_exists( 'imaginem_codepack_get_postformat_icon' ) ) {
    function imaginem_codepack_get_postformat_icon( $postformat = "standard" ) {
    	switch ($postformat) {
    		case 'video':
    			$postformat_icon = "ion-ios-film-outline";
    			break;
    		case 'audio':
    			$postformat_icon = "ion-ios-musical-notes";
    			break;
    		case 'gallery':
    			$postformat_icon = "ion-ios-albums-outline";
    			break;
    		case 'quote':
    			$postformat_icon = "ion-ios-chatbubble-outline";
    			break;
    		case 'link':
    			$postformat_icon = "ion-ios-redo-outline";
    			break;
    		case 'aside':
    			$postformat_icon = "ion-ios-redo-outline";
    			break;
    		case 'image':
    			$postformat_icon = "ion-ios-camera-outline";
    			break;
    		default:
    			$postformat_icon = "ion-ios-compose-outline";
    			break;
    	}

    	return $postformat_icon;
    }
}
// Get Attached images applied with custom script
if ( !function_exists( 'imaginem_codepack_get_pagemeta_infobox_set' ) ) {
    function imaginem_codepack_get_pagemeta_infobox_set( $page_id ) {
    	$filter_image_ids = false;
    	$the_image_ids = get_post_meta( $page_id , 'pagemeta_infoboxes');
    	if ($the_image_ids) {
    		$filter_image_ids = explode(',', $the_image_ids[0]);
    		return $filter_image_ids;
    	}
    }
}
if ( !function_exists( 'imaginem_codepack_country_list' ) ) {
    function imaginem_codepack_country_list($output_type="select",$selected=""){
    	$countries = array
    	(
    		'none' => "Choose Country",
    		'AF' => 'Afghanistan',
    		'AX' => 'Aland Islands',
    		'AL' => 'Albania',
    		'DZ' => 'Algeria',
    		'AS' => 'American Samoa',
    		'AD' => 'Andorra',
    		'AO' => 'Angola',
    		'AI' => 'Anguilla',
    		'AQ' => 'Antarctica',
    		'AG' => 'Antigua And Barbuda',
    		'AR' => 'Argentina',
    		'AM' => 'Armenia',
    		'AW' => 'Aruba',
    		'AU' => 'Australia',
    		'AT' => 'Austria',
    		'AZ' => 'Azerbaijan',
    		'BS' => 'Bahamas',
    		'BH' => 'Bahrain',
    		'BD' => 'Bangladesh',
    		'BB' => 'Barbados',
    		'BY' => 'Belarus',
    		'BE' => 'Belgium',
    		'BZ' => 'Belize',
    		'BJ' => 'Benin',
    		'BM' => 'Bermuda',
    		'BT' => 'Bhutan',
    		'BO' => 'Bolivia',
    		'BA' => 'Bosnia And Herzegovina',
    		'BW' => 'Botswana',
    		'BV' => 'Bouvet Island',
    		'BR' => 'Brazil',
    		'IO' => 'British Indian Ocean Territory',
    		'BN' => 'Brunei Darussalam',
    		'BG' => 'Bulgaria',
    		'BF' => 'Burkina Faso',
    		'BI' => 'Burundi',
    		'KH' => 'Cambodia',
    		'CM' => 'Cameroon',
    		'CA' => 'Canada',
    		'CV' => 'Cape Verde',
    		'KY' => 'Cayman Islands',
    		'CF' => 'Central African Republic',
    		'TD' => 'Chad',
    		'CL' => 'Chile',
    		'CN' => 'China',
    		'CX' => 'Christmas Island',
    		'CC' => 'Cocos (Keeling) Islands',
    		'CO' => 'Colombia',
    		'KM' => 'Comoros',
    		'CG' => 'Congo',
    		'CD' => 'Congo, Democratic Republic',
    		'CK' => 'Cook Islands',
    		'CR' => 'Costa Rica',
    		'CI' => 'Cote D\'Ivoire',
    		'HR' => 'Croatia',
    		'CU' => 'Cuba',
    		'CY' => 'Cyprus',
    		'CZ' => 'Czech Republic',
    		'DK' => 'Denmark',
    		'DJ' => 'Djibouti',
    		'DM' => 'Dominica',
    		'DO' => 'Dominican Republic',
    		'EC' => 'Ecuador',
    		'EG' => 'Egypt',
    		'SV' => 'El Salvador',
    		'GQ' => 'Equatorial Guinea',
    		'ER' => 'Eritrea',
    		'EE' => 'Estonia',
    		'ET' => 'Ethiopia',
    		'FK' => 'Falkland Islands (Malvinas)',
    		'FO' => 'Faroe Islands',
    		'FJ' => 'Fiji',
    		'FI' => 'Finland',
    		'FR' => 'France',
    		'GF' => 'French Guiana',
    		'PF' => 'French Polynesia',
    		'TF' => 'French Southern Territories',
    		'GA' => 'Gabon',
    		'GM' => 'Gambia',
    		'GE' => 'Georgia',
    		'DE' => 'Germany',
    		'GH' => 'Ghana',
    		'GI' => 'Gibraltar',
    		'GR' => 'Greece',
    		'GL' => 'Greenland',
    		'GD' => 'Grenada',
    		'GP' => 'Guadeloupe',
    		'GU' => 'Guam',
    		'GT' => 'Guatemala',
    		'GG' => 'Guernsey',
    		'GN' => 'Guinea',
    		'GW' => 'Guinea-Bissau',
    		'GY' => 'Guyana',
    		'HT' => 'Haiti',
    		'HM' => 'Heard Island & Mcdonald Islands',
    		'VA' => 'Holy See (Vatican City State)',
    		'HN' => 'Honduras',
    		'HK' => 'Hong Kong',
    		'HU' => 'Hungary',
    		'IS' => 'Iceland',
    		'IN' => 'India',
    		'ID' => 'Indonesia',
    		'IR' => 'Iran, Islamic Republic Of',
    		'IQ' => 'Iraq',
    		'IE' => 'Ireland',
    		'IM' => 'Isle Of Man',
    		'IL' => 'Israel',
    		'IT' => 'Italy',
    		'JM' => 'Jamaica',
    		'JP' => 'Japan',
    		'JE' => 'Jersey',
    		'JO' => 'Jordan',
    		'KZ' => 'Kazakhstan',
    		'KE' => 'Kenya',
    		'KI' => 'Kiribati',
    		'KR' => 'Korea',
    		'KW' => 'Kuwait',
    		'KG' => 'Kyrgyzstan',
    		'LA' => 'Lao People\'s Democratic Republic',
    		'LV' => 'Latvia',
    		'LB' => 'Lebanon',
    		'LS' => 'Lesotho',
    		'LR' => 'Liberia',
    		'LY' => 'Libyan Arab Jamahiriya',
    		'LI' => 'Liechtenstein',
    		'LT' => 'Lithuania',
    		'LU' => 'Luxembourg',
    		'MO' => 'Macao',
    		'MK' => 'Macedonia',
    		'MG' => 'Madagascar',
    		'MW' => 'Malawi',
    		'MY' => 'Malaysia',
    		'MV' => 'Maldives',
    		'ML' => 'Mali',
    		'MT' => 'Malta',
    		'MH' => 'Marshall Islands',
    		'MQ' => 'Martinique',
    		'MR' => 'Mauritania',
    		'MU' => 'Mauritius',
    		'YT' => 'Mayotte',
    		'MX' => 'Mexico',
    		'FM' => 'Micronesia, Federated States Of',
    		'MD' => 'Moldova',
    		'MC' => 'Monaco',
    		'MN' => 'Mongolia',
    		'ME' => 'Montenegro',
    		'MS' => 'Montserrat',
    		'MA' => 'Morocco',
    		'MZ' => 'Mozambique',
    		'MM' => 'Myanmar',
    		'NA' => 'Namibia',
    		'NR' => 'Nauru',
    		'NP' => 'Nepal',
    		'NL' => 'Netherlands',
    		'AN' => 'Netherlands Antilles',
    		'NC' => 'New Caledonia',
    		'NZ' => 'New Zealand',
    		'NI' => 'Nicaragua',
    		'NE' => 'Niger',
    		'NG' => 'Nigeria',
    		'NU' => 'Niue',
    		'NF' => 'Norfolk Island',
    		'MP' => 'Northern Mariana Islands',
    		'NO' => 'Norway',
    		'OM' => 'Oman',
    		'PK' => 'Pakistan',
    		'PW' => 'Palau',
    		'PS' => 'Palestinian Territory, Occupied',
    		'PA' => 'Panama',
    		'PG' => 'Papua New Guinea',
    		'PY' => 'Paraguay',
    		'PE' => 'Peru',
    		'PH' => 'Philippines',
    		'PN' => 'Pitcairn',
    		'PL' => 'Poland',
    		'PT' => 'Portugal',
    		'PR' => 'Puerto Rico',
    		'QA' => 'Qatar',
    		'RE' => 'Reunion',
    		'RO' => 'Romania',
    		'RU' => 'Russian Federation',
    		'RW' => 'Rwanda',
    		'BL' => 'Saint Barthelemy',
    		'SH' => 'Saint Helena',
    		'KN' => 'Saint Kitts And Nevis',
    		'LC' => 'Saint Lucia',
    		'MF' => 'Saint Martin',
    		'PM' => 'Saint Pierre And Miquelon',
    		'VC' => 'Saint Vincent And Grenadines',
    		'WS' => 'Samoa',
    		'SM' => 'San Marino',
    		'ST' => 'Sao Tome And Principe',
    		'SA' => 'Saudi Arabia',
    		'SN' => 'Senegal',
    		'RS' => 'Serbia',
    		'SC' => 'Seychelles',
    		'SL' => 'Sierra Leone',
    		'SG' => 'Singapore',
    		'SK' => 'Slovakia',
    		'SI' => 'Slovenia',
    		'SB' => 'Solomon Islands',
    		'SO' => 'Somalia',
    		'ZA' => 'South Africa',
    		'GS' => 'South Georgia And Sandwich Isl.',
    		'ES' => 'Spain',
    		'LK' => 'Sri Lanka',
    		'SD' => 'Sudan',
    		'SR' => 'Suriname',
    		'SJ' => 'Svalbard And Jan Mayen',
    		'SZ' => 'Swaziland',
    		'SE' => 'Sweden',
    		'CH' => 'Switzerland',
    		'SY' => 'Syrian Arab Republic',
    		'TW' => 'Taiwan',
    		'TJ' => 'Tajikistan',
    		'TZ' => 'Tanzania',
    		'TH' => 'Thailand',
    		'TL' => 'Timor-Leste',
    		'TG' => 'Togo',
    		'TK' => 'Tokelau',
    		'TO' => 'Tonga',
    		'TT' => 'Trinidad And Tobago',
    		'TN' => 'Tunisia',
    		'TR' => 'Turkey',
    		'TM' => 'Turkmenistan',
    		'TC' => 'Turks And Caicos Islands',
    		'TV' => 'Tuvalu',
    		'UG' => 'Uganda',
    		'UA' => 'Ukraine',
    		'AE' => 'United Arab Emirates',
    		'GB' => 'United Kingdom',
    		'US' => 'United States',
    		'UM' => 'United States Outlying Islands',
    		'UY' => 'Uruguay',
    		'UZ' => 'Uzbekistan',
    		'VU' => 'Vanuatu',
    		'VE' => 'Venezuela',
    		'VN' => 'Viet Nam',
    		'VG' => 'Virgin Islands, British',
    		'VI' => 'Virgin Islands, U.S.',
    		'WF' => 'Wallis And Futuna',
    		'EH' => 'Western Sahara',
    		'YE' => 'Yemen',
    		'ZM' => 'Zambia',
    		'ZW' => 'Zimbabwe',
    	);
    	$country_list = false;
    	if ($output_type=="select") {
    		$country_list="";
    		foreach ($countries as $key => $option) {
    		    if ($selected==$key) {
    		    	$country_selected='selected="selected"';
    		    } else {
    		    	$country_selected="";
    		    }
    			$country_list .= '<option value="'. esc_attr($key) .'" '.$country_selected.'>'. esc_attr($option) . '</option>';
    		}
    	}
    	if ($output_type=="display") {
    		if (array_key_exists($selected,$countries)) {
    			$country_list = $countries[$selected];
    		}
    	}
    	return $country_list;
    }
}
if ( !function_exists( 'imaginem_codepack_is_hex_color' ) ) {
    function imaginem_codepack_is_hex_color($color) {
    	if(preg_match('/^#[a-f0-9]{6}$/i', $color)) {
    		return true;
    	}
    	return false;
    }
}
/**
 * [mtheme_shortcodefunction_hex_to_rgb description]
 * @param  [type] $color [description]
 * @return [type]        [description]
 */
if ( !function_exists( 'mtheme_shortcodefunction_hex_to_rgb' ) ) {
    function mtheme_shortcodefunction_hex_to_rgb($color) {
    	if (substr($color, 0, 1) === '#') {
    		$color = substr($color, 1);
    	}

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                     $color[2].$color[3],
                                     $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }
}
/**
 * [superlens_excerpt_limit description]
 * @param  [type] $limit [description]
 * @return [type]        [description]
 */
if ( !function_exists( 'imaginem_codepack_excerpt_limit' ) ) {
    function imaginem_codepack_excerpt_limit($limit) {
    	  if (!is_numeric($limit)) {
    	  	$limit = 15;
    	  }
          $excerpt = explode(' ', get_the_excerpt(), $limit);
          if (count($excerpt)>=$limit) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt);
          } else {
            $excerpt = implode(" ",$excerpt);
          } 
          $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
          return $excerpt . '[...]';
     }
 }
/**
 * [imaginem_codepack_activate_lightbox Activate a Lightbox]
 * @param  [type] $lightbox_type [lightbox type]
 * @param  [type] $ID            [image ID]
 * @param  [type] $predefined    [predefined link of lightbox image]
 * @param  [type] $mediatype     [image or video]
 * @param  [type] $title         [the title]
 * @param  [type] $class         [class to add]
 * @param  [type] $navigation    [more than one image]
 * @return [type]                [description]
 */
if ( !function_exists( 'imaginem_codepack_activate_lightbox' ) ) {
	function imaginem_codepack_activate_lightbox ($lightbox_type="default",$ID="",$predefined=false,$mediatype="image",$title="",$class="",$set=false,$data_name="default", $external_thumbnail_id =false, $imageDataID = false , $galleryID = 'containerset', $proofing = false ) {

		$galleru_id = '-id-container';
		$link = '';
		if ($data_name=="default") {
			$data_name = "data-src";
		}
		if ($ID=="") {
			$ID = get_the_id();
		}
		$gallery='';
		if ($set) {
			// for gallery
		}
		if ($predefined) {
			$link = $predefined;
		}
		if ($external_thumbnail_id) {
			$imagearray = wp_get_attachment_image_src($external_thumbnail_id, 'superlens-gridblock-tiny', false);
            $thumbnail_link   = $imagearray[0];
		} else {
			if ($predefined) {
				$thumbnail_link = $predefined;
			}
		}

        $featured_image_id = $imageDataID;
        $featured_image_data = get_post($featured_image_id);
        $featuredimg_purchase_url = '';
        $featuredimg_title = '';
        $featuredimg_desc = '';
        $purchase_link = false;

        if (isSet($featured_image_data->post_title)) {
            $featuredimg_title = $featured_image_data->post_title;
        }
        if (isSet($featured_image_data->post_content)) {
			if ( imaginem_codepack_get_option_data('status_lightbox_desc') ) {
				$featuredimg_desc = $featured_image_data->post_content;
			}
        }

        if ($title<>"") {
			$featuredimg_title = '';
			if ( imaginem_codepack_get_option_data('status_lightbox_title') ) {
				$featuredimg_title = $title;
			}
        }

        $featuredimg_purchase_url  = get_post_meta( $featured_image_id, 'mtheme_attachment_purchase_url', true);
        $button_target             = '';
        $button_target             = get_post_meta( $featured_image_id, 'mtheme_purchase_url_target', true );
        $linktarget                = '';
        if ( 'blank' === $button_target ) {
            $linktarget = 'target="_blank" ';
        }

        if ( $featuredimg_purchase_url ) {
            $purchase_link = $featuredimg_purchase_url;
        } else {
            $purchase_link = false;
        }

		$purchase_tag= '';
		$purchase_link_present= '';
		if ($purchase_link) {
            $purchase_text = imaginem_codepack_get_option_data('lightbox_purchase_text');
            if ($purchase_text=="") {
                $purchase_text = "Purchase";
			}
			if ( imaginem_codepack_get_option_data('status_lightbox_purchase') ) {
				$purchase_tag = '<span class="lightbox-purchase"><a ' . $linktarget . 'href="'.esc_url($purchase_link).'">'.$purchase_text.'</a></span>';
				$purchase_link_present = "has-purchase-link";
			}
		}

		$desc = '';
		if ( $featuredimg_desc<>"" || $purchase_tag<>"" ) {
			$desc =  '<div class="lightbox-text entry-content"><span class="lightbox-content">'.$featuredimg_desc.'</span>'.$purchase_tag.'</div>';
		}
		
		$html_subtext = '<div class="lightbox-text-wrap '.$purchase_link_present.'"><h4 class="lightbox-text">'.$featuredimg_title.'</h4>'.$desc.'</div>';

		$output = false;
		$like_tag = '';
		if ( $proofing ) {
			$like_data = '<span id="lightbox-like-id-'.$featured_image_id.'" class="lightbox-like-holder animated" data-lightboxlike="'.$featured_image_id.'"><span class="mtheme-like like-vote-icon"><i class="proofing-lightbox-like proofing-lightbox-clickready ion-ios-heart-outline"></i></span></span>';
			$like_tag = 'data-title="'.esc_attr($like_data).'"';
		}
		if ( isSet($link) && $link<>"" ) {
			$output='<a '.$like_tag.' data-elementor-open-lightbox="no" data-rel="gallery-'.$galleryID.'" data-thumb="'.esc_url( $thumbnail_link ).'" '.$gallery.'class="mobx lightbox-active '.$class.'" data-desc="'.esc_attr($html_subtext).'" href="'.esc_url( $link ).'" data-src="'.esc_url( $link ).'" '.$data_name.'="'.$link.'">';
		}
		return $output;
	}
}

/*-------------------------------------------------------------------------*/
/* Show featured image title */
/*-------------------------------------------------------------------------*/
/**
 * [imaginem_codepack_image_title description]
 * @param  [type] $ID [description]
 * @return [type]     [description]
 */
if ( !function_exists( 'imaginem_codepack_image_title' ) ) {
    function imaginem_codepack_image_title ($ID) {
    	$img_title='';
    	$image_id = get_post_thumbnail_id($ID);
    	$img_obj = get_post($image_id);
    	if (isSet($img_obj)){
    		$img_title = $img_obj->post_title;
    	}
    	return $img_title;
    }
}
/*-------------------------------------------------------------------------*/
/* Show featured image link */
/*-------------------------------------------------------------------------*/
/**
 * [imaginem_codepack_featured_image_link description]
 * @param  [type] $ID [description]
 * @return [type]     [description]
 */
if ( !function_exists( 'imaginem_codepack_featured_image_link' ) ) {
    function imaginem_codepack_featured_image_link ($ID , $size = 'full') {
    	$image_id = get_post_thumbnail_id($ID, $size); 
		$image_url = wp_get_attachment_image_src($image_id, $size);  
		if ( isset( $image_url[0] ) ) {
			$image_url = $image_url[0];
		} else {
			$image_url = '';
		}
    	return $image_url;
    }
}
// Get Attached images applied with custom script
/**
 * [imaginem_codepack_get_custom_attachments description]
 * @param  [type] $page_id [description]
 * @return [type]          [description]
 */
if ( !function_exists( 'imaginem_codepack_get_proofing_attachments' ) ) {
    function imaginem_codepack_get_proofing_attachments( $page_id ) {
    	$filter_image_ids = false;
    	$the_image_ids = get_post_meta( $page_id , '_mtheme_proofing_image_ids');
    	if ($the_image_ids) {
    		$filter_image_ids = explode(',', $the_image_ids[0]);
    		return $filter_image_ids;
    	}
    }
}
// Get Attached images applied with custom script
/**
 * [imaginem_codepack_get_custom_attachments description]
 * @param  [type] $page_id [description]
 * @return [type]          [description]
 */
if ( !function_exists( 'imaginem_codepack_get_custom_attachments' ) ) {
    function imaginem_codepack_get_custom_attachments( $page_id ) {
    	$filter_image_ids = false;
    	$the_image_ids = get_post_meta( $page_id , '_mtheme_image_ids');
    	if ($the_image_ids) {
    		$filter_image_ids = explode(',', $the_image_ids[0]);
    		return $filter_image_ids;
    	}
    }
}
// Displays alt text based on ID
if ( !function_exists( 'imaginem_codepack_get_alt_text' ) ) {
    function imaginem_codepack_get_alt_text($attatchmentID) {
    	$alt = get_post_meta($attatchmentID, '_wp_attachment_image_alt', true);
    	return $alt;
    }
}
if ( !function_exists( 'imageinem_codepack_get_placeholder_image' ) ) {
    function imageinem_codepack_get_placeholder_image($type) {
    	switch ($type) {
    		case 'superlens-gridblock-large':
    			$fallback_file = 'placeholder-770x550';
    			break;
    		case 'superlens-gridblock-large-portrait':
    			$fallback_file = 'placeholder-550x770';
    			break;
    		case 'superlens-gridblock-full-medium':
    			$fallback_file = 'placeholder-770x550';
    			break;
            case 'superlens-gridblock-full':
                $fallback_file = 'placeholder-770x550';
                break;
    		case 'superlens-gridblock-square-big':
    			$fallback_file = 'placeholder-770x770';
    			break;
    		
    		default:
    			$fallback_file = 'placeholder-770x550';
    			break;
    	}
    	$fallback_image = get_template_directory_uri().'/images/placeholders/'.$fallback_file.'.gif';
    	return 	$fallback_image;
    }
}
function imaginem_codepack_display_image_srcset( $the_image_id, $have_image_url, $link, $type, $the_image_title, $class, $lazyload = false ) {

	if ( '' === $type ) {
		$type = 'fullsize';
	}
	$output = '';

	$image_url   = wp_get_attachment_image_src( $the_image_id, $type );
	$image_url   = $image_url[0];
	$img_obj     = get_post( $the_image_id );
	$img_alt     = get_post_meta( $the_image_id, '_wp_attachment_image_alt', true );
	$permalink   = get_permalink( $the_image_id );
	$srcset      = wp_get_attachment_image_srcset( $the_image_id, $type );
	$srcsetsizes = wp_get_attachment_image_sizes( $the_image_id, $type );

	if ( true === $link ) {
		$output = '<a href="' . esc_url( $permalink ) . '">';
	}

	$fallback_image = imageinem_codepack_get_placeholder_image( $type );

	$data_src     = '';
	$fallback_src = '';
	$data_srcset  = '';
	$srcsizes     = 'sizes="' . $srcsetsizes . '"';

	if ( 'true' === $lazyload ) {
		$data_src     = 'data-';
        $data_srcset  = 'data-srcset="' . $srcset . '" ';
        $srcsizes     = 'data-sizes="' . $srcsetsizes . '"';
		$fallback_src = 'src="' . esc_url( $fallback_image ) . '" ';
		$class        = $class . ' lazyload';
	} else {
		$data_srcset = 'srcset="' . $srcset . '" ';
	}
	if ( $have_image_url ) {
		$have_image_id = imageinem_codepack_get_placeholder_image( $have_image_url );
		$img_alt       = imageinem_codepack_alt_text( $have_image_id );
		$output       .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $have_image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
	} else {
		if ( isset( $image_url ) && '' !== $image_url ) {
			if ( $class ) {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
			} else {
				$output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" />';
			}
		}
	}

	if ( true === $link ) {
		$output .= '</a>';
	}

	return $output;
}
if ( !function_exists( 'imaginem_codepack_display_post_image' ) ) {
    function imaginem_codepack_display_post_image ($ID,$have_image_url,$link,$type,$title,$class,$lazyload=false) {

    	if ($type=="") $type="fullsize";
    	$output="";
    	
    	$image_id = get_post_thumbnail_id(($ID), $type); 
		$image_url = wp_get_attachment_image_src($image_id,$type);
		if ( isset( $image_url[0]) ) {
			$image_url = $image_url[0];
		}

    	$img_obj = get_post($image_id);
    	$img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    	
    	$permalink = get_permalink( $ID );
    	
    	if ($link==true) {
    		$output = '<a href="' . esc_url( $permalink ) . '">';
    	}

    	$fallback_image = imageinem_codepack_get_placeholder_image($type);

    	$data_src = '';
    	$fallback_src = '';
    	if ($lazyload=="true") {
    		$data_src = "data-";
    		$fallback_src = 'src="'. esc_url( $fallback_image ) .'" ';
    		$class = $class . ' lazyload';
    	}
    	if ($have_image_url) {
    		$img_alt = imaginem_codepack_get_alt_text($ID);
            if ($img_alt=="") {
                $have_image_url_id = imaginem_codepack_get_image_id_from_url($have_image_url);
                $img_alt = imaginem_codepack_get_alt_text($have_image_url_id);
            }
    		$output .= '<img '.$data_src.'src="'. esc_url( $have_image_url ) .'" alt="'. esc_attr( $img_alt ) .'" class="'. $class .'"/>';
    	} else {
    		if (isSet($image_url) && $image_url<>"") {
    			if ($class) {
    				$output .= '<img '.$fallback_src.$data_src.'src="'. esc_url( $image_url ) .'" alt="'. esc_attr( $img_alt ) .'" class="'. $class .'"/>';
    			} else {
    				$output .= '<img '.$fallback_src.$data_src.'src="'. esc_url( $image_url ) .'" alt="'. esc_attr( $img_alt ) .'" />';
    			}
    		}
    	}
    	
    	if ($link==true) {
    		$output .= '</a>';
    	}
    	
    	return $output;
    }
}
if ( !function_exists( 'imaginem_codepack_display_like_link' ) ) {
    function imaginem_codepack_display_like_link($post_id) {
    	$themename = MTHEME;

    	$vote_count = get_post_meta($post_id, "votes_count", true);
    	
    	if (! $vote_count) $vote_count="0";

    	$output = '<div class="mtheme-post-like-wrap">';
    	$output .= '<div class="mtheme-post-like">';
    	if(imaginem_ingencreative_hasAlreadyVoted($post_id))
    		$output .= ' <span class="mtheme-like like-vote-icon like-alreadyvoted"><i class="fa fa-thumbs-o-up"></i></span>';
    	else
    		$output .= '<a class="vote-ready" href="#" data-post_id="'.$post_id.'">
    					<span class="mtheme-like like-vote-icon like-notvoted"><i class="fa fa-thumbs-o-up"></i></span>
    				</a>';
    	$output .= '<div class="post-link-count-wrap" data-count_id="'.$post_id.'"><span class="post-like-count">' . $vote_count . '</span> found this helpful</div>';
    	$output .= '</div>';
    	$output .= '</div>';
    	
    	return $output;
    }
}
// Custom Pagination codes
if ( !function_exists( 'imaginem_codepack_pagination' ) ) {
    function imaginem_codepack_pagination($pages = '', $range = 4) {
    	$pagination='';
         $showitems = ($range * 2)+1; 
     
        global $paged;
    	if ( get_query_var('paged') ) {
    		$paged = get_query_var('paged');
    	} elseif ( get_query_var('page') ) {
    		$paged = get_query_var('page');
    	} else {
    		$paged = 1;
    	}
         if(empty($paged)) $paged = 1;
     
         if($pages == '')
         {
             global $wp_query;
             $pages = $wp_query->max_num_pages;
             if(!$pages)
             {
                 $pages = 1;
             }
         }  
     
         if(1 != $pages)
         {
             $pagination .= '<div class="pagination-navigation">';
             $pagination .=  "<div class=\"pagination\"><span class=\"pagination-info\">". __("Page ","mthemelocal") . $paged. __(" of ","mthemelocal") .$pages."</span>";
             if($paged > 2 && $paged > $range+1 && $showitems < $pages) $pagination .=  "<a class='pagination-first' href='". esc_url( get_pagenum_link(1) )."'>&laquo;</a>";
             if($paged > 1 && $showitems < $pages) $pagination .=  "<a class='pagination-previous' href='".esc_url( get_pagenum_link($paged - 1) )."'>&lsaquo;</a>";
     
             for ($i=1; $i <= $pages; $i++)
             {
                 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                 {
                     $pagination .=  ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".esc_url( get_pagenum_link($i) )."' class=\"inactive\">".$i."</a>";
                 }
             }
     
             if ($paged < $pages && $showitems < $pages) $pagination .=  "<a href=\"".esc_url( get_pagenum_link($paged + 1) )."\">&rsaquo;</a>"; 
             if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) $pagination .=  "<a href='".esc_url( get_pagenum_link($pages) )."'>&raquo;</a>";
             $pagination .=  "</div>";
             $pagination .=  "</div>";
         }
         return $pagination;
    }
}
if ( !function_exists( 'imaginem_codepack_get_option_data' ) ) {
    function imaginem_codepack_get_option_data( $field_id, $default_value = '' ) {
        if ( function_exists( 'superlens_get_option_data' ) ) {
            $default = superlens_get_option_data( $field_id, $default_value );
            return $default;
        } else {
            return false;
        }
    }
}
?>