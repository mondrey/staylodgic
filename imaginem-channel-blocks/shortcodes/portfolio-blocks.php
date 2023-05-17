<?php
// Before After
if (!function_exists('mtheme_BeforeAfter')) {
    function mtheme_BeforeAfter($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "urls" => '',
            "id1" => '',
            "id2" => '',
            "image_size" => '',
            "format" => 'vertical',
            "pb_image_ids" => ''
        ), $atts));
        
        $uniqueID           = uniqid();

        $image_array_1 = wp_get_attachment_image_src($id1,$image_size,false);
        $image_src_1 = $image_array_1[0];

        $image_array_2 = wp_get_attachment_image_src($id2,$image_size,false);
        $image_src_2 = $image_array_2[0];
        
        $beforeafter = '<div id="before-after-id-' . $uniqueID . '" date-id="before-after-' . $uniqueID . '" class="before-after-detect before-after-shortcode before-after-container" data-orientation="vertical">';
        $beforeafter .= '<div class="twentytwenty-container">';
        $beforeafter .= '<img src="' . esc_url($image_src_1) . '" alt="'.esc_attr( imaginem_codepack_get_alt_text($id1) ).'" />';
        $beforeafter .= '<img src="' . esc_url($image_src_2) . '" alt="'.esc_attr( imaginem_codepack_get_alt_text($id2) ).'"/>';
        $beforeafter .= '</div>';
        $beforeafter .= '</div>';
        
        return $beforeafter;
        
    }
}
add_shortcode("beforeafter", "mtheme_BeforeAfter");

//Metro Gallery [metro]
if (!function_exists('mtheme_MetroGrids')) {
    function mtheme_MetroGrids($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "size" => 'thumbnail',
            "exclude_featured" => 'false',
            "format" => '',
            "edgetoedge" => 'false',
            "start" => '',
            "animated" => 'false',
            "end" => '',
            "pb_image_ids" => '',
            "columns" => '4',
            "title" => "false",
            "description" => "false",
            "id" => '1',
            "pageid" => ''
        ), $atts));
        
        $galleryID = uniqid();
        // Set a default
        $animation_class = ' grid-animate-display-all';
        if ($animated == "true") {
            $animation_class = ' animation-standby-portfolio animated fadeIn';
        }
        
        $portfolio_count = 0;
        $thumbnailcount  = 0;
        if ($pageid != '') {
            $thepageID = get_the_id();
        }
        
        if (trim($pb_image_ids) <> '') {
            $filter_image_ids = explode(',', $pb_image_ids);
        } else {
            $filter_image_ids = imaginem_codepack_get_custom_attachments($pageid);
        }
        
        if ($end < $start) {
            $end   = '';
            $start = '';
        }
        
        $uniqureID = get_the_id() . "-" . uniqid();
        
        if ($filter_image_ids) {
            
            $thumbnails = '<div class="gridblock-metro-wrap gridblock-metro-wrap-' . $uniqureID . ' clearfix">';
            $thumbnails .= '<div class="gridblock-metro">';
            
            $featuredID = get_post_thumbnail_id();
            
            foreach ($filter_image_ids as $attachment_id) {

                $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
                if ( !$check_if_image_present ) {
                    continue;
                }
                
                $thumbnailcount++;
                
                if ($start != '') {
                    if ($thumbnailcount < $start) {
                        continue;
                    }
                }
                if ($end != '') {
                    if ($thumbnailcount > $end) {
                        continue;
                    }
                }
                
                if ($exclude_featured == 'true') {
                    if ($featuredID == $attachment_id)
                        continue; // skip rest of the loop
                }
                
                $imagearray = wp_get_attachment_image_src($attachment_id, 'superlens-gridblock-full', false);
                $imageURI   = $imagearray[0];
                
                $thumbnail_imagearray = wp_get_attachment_image_src($attachment_id, 'superlens-gridblock-square-big', false);
                $thumbnail_imageURI   = $thumbnail_imagearray[0];
                
                $bigthumbnail_imagearray = wp_get_attachment_image_src($attachment_id, 'superlens-gridblock-square-big', false);
                $bigthumbnail_imageURI   = $bigthumbnail_imagearray[0];
                
                $imageID = get_post($attachment_id);
                
                $imageTitle = '';
                $imageDesc  = '';
                if (isSet($imageID->post_title)) {
                    $imageTitle = $imageID->post_title;
                }
                if (isSet($imageID->post_content)) {
                    $imageDesc = $imageID->post_content;
                }
                
                $portfolio_count++;
                
                $cell_name = "following-cell";
                if ($portfolio_count == 1) {
                    $cell_name          = "first-cell";
                    $thumbnail_imageURI = $bigthumbnail_imageURI;
                }
                
                // if ($portfolio_count==1) {
                // 	$thumbnails .=   '<li class="clearfix"></li>';
                // }
                
                $thumbnails .= '<div class="gridblock-element ' . $animation_class . ' gridblock-col-' . $portfolio_count . ' gridblock-cell-' . $cell_name . '">';
                
                $thumbnails .= '<div class="gridblock-grid-element gridblock-element-inner" data-rel="' . get_the_id() . '">';
                $thumbnails .= imaginem_codepack_activate_lightbox(
                    $lightbox_type = "default",
                    $ID = get_the_id(),
                    $predefined = $imageURI,
                    $mediatype = "image",
                    $title = $imageTitle,
                    $class = "column-gridblock-lightbox lightbox-image",
                    $set = "metro-grid",
                    $data_name = "default",
                    $external_thumbnail_id = $attachment_id,
                    $imageDataID=$attachment_id,
                    $galleryID
                );
                $thumbnails .= '<div class="gridblock-background-hover">';
                $thumbnails .= '<div class="gridblock-links-wrap">';
                
                
                $thumbnails .= '<span class="column-gridblock-icon"><span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i></span></span>';
                
                
                
                $thumbnails .= '</div>';
                $thumbnails .= '</div>';
                
                $thumbnails .= '<img class="preload-image displayed-image" src="' . $thumbnail_imageURI . '" alt="' . imaginem_codepack_get_alt_text($featuredID) . '">';
                $thumbnails .= '</a>';
                $thumbnails .= '</div>';
                $thumbnails .= '</div>';
            }
            $thumbnails .= '</div>';
            $thumbnails .= '</div>';
            
            return $thumbnails;
            
        }
    }
}
add_shortcode("metrogrid", "mtheme_MetroGrids");
//Thumbnails for Gallery [thumbnails]
if (!function_exists('mtheme_Thumbnails')) {
    function mtheme_Thumbnails($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "size" => 'thumbnail',
            "exclude_featured" => 'false',
            "filtersort" => 'none',
            "attachment_linking" => 'false',
            "style" => 'classic',
            "imagesize" => 'defaultsize',
            "filter" => '',
            "effect" => 'default',
            "format" => '',
            "like" => 'no',
            "filterall" => 'All',
            "viewmore" => 'View Project',
            "lazyload" => 'true',
            "animated_hovercard" => 'enable',
            "start" => '',
            "end" => '',
            "linktype" => 'lightbox',
            "animated" => 'true',
            "gutter" => 'spaced',
            "boxtitle" => '',
            "pb_image_ids" => '',
            "columns" => '4',
            "title" => "false",
            "description" => "false",
            "id" => '1',
            "pageid" => ''
        ), $atts));

        if ($effect=="{{effect}}") {
            $effect = "default";
        }
        if ($effect=="default") {
            $effect = "none";
        }

        $galleryID = uniqid();

        $effect_class            = ' has-effect-'.$effect;
        $relayout_on_image_class = '';

        $only_title_class = '';

        switch ($style) {
            case 'wall-spaced':
                $gutter = "spaced";
                if ($title == 'true' && $description == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            
            case 'wall-grid':
                $gutter = "nospace";
                if ($title == 'true' && $description == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            case 'card-spaced':
                $boxtitle = false;
                $gutter = 'spaced';
                $format = 'card';
                $effect = 'default';
                break;

            case 'card-grid':
                $boxtitle = false;
                $gutter = 'nospace';
                $format = 'card';
                $effect = 'default';
                break;
            
            default:
                break;
        }

        if (!isSet($style)) {
            $style = "classic";
        }

        $animation_class = '';
        if ($style == "wall-spaced" || $style == "wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
            $boxtitle = "true";
        } else {
            $boxtitle = "false";
        }
        if ($animated == "true" && $style<>"wall-spaced" && $style<>"wall-grid" ) {
            $animation_class = ' animation-standby-portfolio animated fadeIn';
        }
        
        $gridblock_is_masonary = "";

        // Set a default
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        
        $gridblock_is_masonary = imaginem_codepack_get_grid_masonry_class($format);
        $column_type = imaginem_codepack_get_grid_column_type($columns);
        $portfolioImage_type = imaginem_codepack_get_grid_image_size($format,$columns);
        $relayout_on_image_class = imaginem_codepack_grid_relayout_class($format,$columns);

        if ( 'masonary' === $format && 'fullsize' === $imagesize ) {
            $portfolioImage_type = 'superlens-gridblock-original';
        }

        if ( $format == "masonary" || $format == "scatter" ) {
            $animation_class = ' grid-animate-display-all';
        }
        
        if ($format == "portrait") {
            $protected_placeholder = '/images/blank-grid-portrait.png';
        } else {
            $protected_placeholder = '/images/blank-grid.png';
        }

        $preload_tag = '<div class="preloading-placeholder"><span class="preload-image-animation"></span><img src="' . get_template_directory_uri() . $protected_placeholder . '" alt="preloading" /></div>';
        
        $portfolio_count    = 0;
        $thumbnailcount     = 0;
        $thepageID          = get_the_id();
        $animated_status = '';
        if ( 'disable' == $animated_hovercard ) {
            $animated_status = '-disabled';
        }
        
        $pb_image_ids = trim($pb_image_ids);
        if (!empty($pb_image_ids)) {
            $filter_image_ids = explode(',', $pb_image_ids);
        } else {
            if ($pageid == '') {
                $pageid = get_the_id();
            }
            $filter_image_ids = imaginem_codepack_get_custom_attachments($pageid);
        }
        
        if ($end < $start) {
            $end   = '';
            $start = '';
        }
        
        $thumbnail_gutter_class = '';
        if ($gutter == "nospace") {
            $thumbnail_gutter_class = ' thumnails-gutter-active ';
        }
        
        $title_desc_class = '';
        if ($title == "false" && $description == "false") {
            $title_desc_class = " no-title-no-desc";
        }
        $boxtitle_class = '';
        if ($boxtitle == "true") {
            $boxtitle_class = " boxtitle-active";
        }
        
        $filterable_tags = array();

        $relayout_on_image_class = imaginem_codepack_grid_relayout_class($format,$columns);
        
        if ($filter_image_ids) {
            
            $thumbnails = '<div class="thumbnails-shortcode gridblock-columns-wrap grid-style-'.$style.'-wrap clearfix">';
            $thumbnails .= '<div class="thumbnails-grid-container lightgallery-container thumbnail-link-is-' . esc_attr( $linktype ) . ' grid-style-'.$style.' thumbnail-gutter-' . $gutter . ' ' . $gridblock_is_masonary . ' ' . $only_title_class . ' ' . $relayout_on_image_class . $thumbnail_gutter_class . $title_desc_class . $boxtitle_class . ' gridblock-' . $column_type . ' '. $effect_class.'"  data-columns="' . $columns . '">';
            
            $featuredID = get_post_thumbnail_id();
            
            foreach ($filter_image_ids as $attachment_id) {
                
                $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
                if ( !$check_if_image_present ) {
                    continue;
                }                
                
                $thumbnailcount++;
                
                if ($start != '') {
                    if ($thumbnailcount < $start) {
                        continue;
                    }
                }
                if ($end != '') {
                    if ($thumbnailcount > $end) {
                        continue;
                    }
                }
                
                if ($exclude_featured == 'true') {
                    if ($featuredID == $attachment_id)
                        continue; // skip rest of the loop
                }
                
                $thumbnail_tags = get_the_terms($attachment_id, 'filtertag');
                
                $filter_attribute = '';
                // Build the filters
                if (isSet($thumbnail_tags) && is_array($thumbnail_tags)) {
                    foreach ($thumbnail_tags as $key => $value) {
                        $thumbnail_tag_slug                   = $value->slug;
                        $thumbnail_tag_name                   = $value->name;
                        $filter_attribute .=' filter-'.$thumbnail_tag_slug;
                        $filterable_tags[$thumbnail_tag_slug] = $thumbnail_tag_name;
                    }
                }

                $purchase_url = false;
                
                // echo '<pre>';
                // print_r($filterable_tags);
                // echo '</pre>';
                
                $imagearray = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
                $imageURI   = $imagearray[0];
                
                $thumbnail_imagearray = wp_get_attachment_image_src($attachment_id, $portfolioImage_type, false);
                $thumbnail_imageURI   = $thumbnail_imagearray[0];
                
                $title_link = '';
                $imageTitle = '';
                $imageDesc  = '';
                $imageID    = get_post($attachment_id);
                $link_url   = get_post_meta($attachment_id, 'mtheme_attachment_fullscreen_url', true);
                $purchase_url   = get_post_meta($attachment_id, 'mtheme_attachment_purchase_url', true);

                if ( $purchase_url ) {
                    $purchase_link = $purchase_url;
                } else {
                    $purchase_link = false;
                }
                
                if (isSet($imageID->post_title)) {
                    $imageTitle = $imageID->post_title;
                }
                if (isSet($imageID->post_content)) {
                    $imageDesc = $imageID->post_content;
                }
                
                if ($portfolio_count == $columns)
                    $portfolio_count = 0;
                $portfolio_count++;
                
                // if ($portfolio_count==1) {
                // 	$thumbnails .=   '<li class="clearfix"></li>';
                // }
                $thumbnails .= '<div class="gridblock-element ' . $animation_class . ' isotope-displayed gridblock-thumbnail-id-' . $attachment_id . ' gridblock-col-' . $portfolio_count .$filter_attribute. '">';

                if ($like == "yes") {
                    $thumbnails .= '<div class="mtheme-post-like-wrap">';
                    $thumbnails .= superlens_display_like_link( $attachment_id );
                    $thumbnails .= '</div>';
                }
                
                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $hreflink = get_permalink();
                    $height = '';
                    //$thumbnails .= '<a class="gridblock-element-card-link" href="' . esc_url( $hreflink ) . '">';

                    $card_link = false;
                    if ($linktype == "lightbox") {
                        $thumbnails .= imaginem_codepack_activate_lightbox(
                            $lightbox_type = "default",
                            $ID = get_the_id(),
                            $predefined = $imageURI, $mediatype = "image",
                            $imagetitletag = $imageTitle,
                            $class = "lightbox-image gridblock-element-card-link",
                            $set = "thumbnails-grid",
                            $data_name = "default",
                            $external_thumbnail_id = $attachment_id,
                            $imageDataID=$attachment_id,
                            $galleryID
                        );
                        $card_link = true;
                    }
                    if ($linktype == "url") {
                        if (isSet($link_url) && $link_url <> "") {
                            $button_target  = '';
                            $button_target  = get_post_meta( $attachment_id, 'mtheme_fullscreen_url_target', true );
                            $linktarget     = '';
                            if ( 'blank' === $button_target ) {
                                $linktarget = 'target="_blank" ';
                            }

                            $thumbnails .= '<a ' . $linktarget . 'class="gridblock-element-card-link" href="' . esc_url($link_url) . '" data-title="' . esc_attr($imageTitle) . '">';
                            $card_link = true;
                        }
                    }
                    if ($linktype == "purchase") {
                        if (isSet($purchase_url) && $purchase_url <> "") {
                            $button_target  = '';
                            $button_target  = get_post_meta( $attachment_id, 'mtheme_purchase_url_target', true );
                            $linktarget     = '';
                            if ( 'blank' === $button_target ) {
                                $linktarget = 'target="_blank" ';
                            }

                            $thumbnails .= '<a ' . $linktarget . 'class="gridblock-element-card-link" href="' . esc_url($purchase_url) . '" data-title="' . esc_attr($imageTitle) . '">';
                            $card_link = true;
                        }
                    }
                    if ($linktype == "download") {
                        $thumbnails .= '<a class="gridblock-element-card-link" href="' . esc_url($imageURI) . '" data-title="' . esc_attr($imageTitle) . '" download>';
                        $card_link = true;
                    }
                    
                    if ($attachment_linking == "true") {
                        $thumbnails .= '<a class="gridblock-element-card-link" href="' . get_attachment_link($attachment_id) . '">';
                        $card_link = true;
                    }

                    $thumbnails .= '<div class="gridblock-element-card" style="'.$height.'background-image:url('. esc_url( $thumbnail_imagearray[0] ) .');">';
                    $thumbnails .= '<div class="gridblock-background-card">';
                }                
                $thumbnails .= '<div class="gridblock-ajax gridblock-grid-element gridblock-element-inner" data-rel="' . get_the_id() . '">';
                
                if (!isSet($linktype)) {
                    $linktype = "lightbox";
                }
                $thumbnail_icon = imaginem_codepack_get_portfolio_icon('lightbox');
                
                $thumbnails .= '<div class="gridblock-background-hover">';

                $url_linked = false;
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($linktype == "download" && $boxtitle != "true") {
                        
                            $thumbnails .= '<div class="gridblock-links-wrap">';

                            $thumbnails .= imaginem_codepack_activate_lightbox(
                                $lightbox_type = "default",
                                $ID = get_the_id(),
                                $predefined = $imageURI,
                                $mediatype = "image",
                                $imagetitletag = $imageTitle,
                                $class = "lightbox-image",
                                $set = "thumbnails-grid",
                                $data_name = "default",
                                $external_thumbnail_id = $attachment_id,
                                $imageDataID=$attachment_id,
                                $galleryID
                                );
                            
                                $thumbnails .= '<span class="column-gridblock-icon"><span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i></span></span>';
                            
                            $thumbnails .= '</a>';
                    }
                    
                    if ($linktype == "lightbox") {
                        $thumbnails .= imaginem_codepack_activate_lightbox(
                            $lightbox_type = "default",
                            $ID = get_the_id(),
                            $predefined = $imageURI, $mediatype = "image",
                            $imagetitletag = $imageTitle,
                            $class = "lightbox-image gridblock-sole-link",
                            $set = "thumbnails-grid",
                            $data_name = "default",
                            $external_thumbnail_id = $attachment_id,
                            $imageDataID=$attachment_id,
                            $galleryID
                        );
                        $thumbnail_icon = imaginem_codepack_get_portfolio_icon('lightbox');
                        $url_linked     = true;
                    }
                    if ($linktype == "url") {
                        if (isSet($link_url) && $link_url <> "") {
                            $button_target  = '';
                            $button_target  = get_post_meta( $attachment_id, 'mtheme_fullscreen_url_target', true );
                            $linktarget     = '';
                            if ( 'blank' === $button_target ) {
                                $linktarget = 'target="_blank" ';
                            }

                            $thumbnails .= '<a ' . $linktarget . 'class="gridblock-sole-link" href="' . esc_url($link_url) . '" data-title="' . esc_attr($imageTitle) . '">';
                            $thumbnail_icon = imaginem_codepack_get_portfolio_icon('link');
                            $url_linked     = true;
                            $title_link = $link_url;
                        } else {
                            $thumbnail_icon = '';
                        }
                    }
                    if ($linktype == "purchase") {
                        if (isSet($purchase_url) && $purchase_url <> "") {
                            $button_target  = '';
                            $button_target  = get_post_meta( $attachment_id, 'mtheme_purchase_url_target', true );
                            $linktarget     = '';
                            if ( 'blank' === $button_target ) {
                                $linktarget = 'target="_blank" ';
                            }

                            $thumbnails .= '<a ' . $linktarget . 'class="gridblock-sole-link" href="' . esc_url($purchase_url) . '" data-title="' . esc_attr($imageTitle) . '">';
                            $thumbnail_icon = imaginem_codepack_get_portfolio_icon('purchase');
                            $url_linked     = true;
                            $title_link = $purchase_url;
                        } else {
                            $thumbnail_icon = '';
                        }
                    }
                    if ($linktype == "download") {
                        $sole_link_class = '';
                        if ($boxtitle=="true") {
                            $sole_link_class = 'class="gridblock-sole-link" ';
                        }
                        $thumbnails .= '<a '.$sole_link_class.'href="' . esc_url($imageURI) . '" data-title="' . esc_attr($imageTitle) . '" download>';
                        $thumbnail_icon = imaginem_codepack_get_portfolio_icon('download');
                        $url_linked     = true;
                    }
                    
                    if ($linktype != "download") {
                        $thumbnails .= '<div class="gridblock-links-wrap">';
                    }
                    
                    if ($attachment_linking == "true") {
                        $thumbnails .= '<a class="column-gridblock-icon" href="' . get_attachment_link($attachment_id) . '">';
                        $thumbnails .= '<span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i></span>';
                        $thumbnails .= '</a>';
                    }
                    
                    if ( $boxtitle == "true" ) {
                        if ( $title == "true" || $description == "true" ) {
                            $thumbnail_icon = "";
                        } 
                    }
                    if ($thumbnail_icon<>"") {
                            $thumbnails .= '<span class="column-gridblock-icon"><span class="hover-icon-effect"><i class="' . $thumbnail_icon . '"></i></span></span>';
                    }
                    
                    if ($linktype != "download") {
                        $thumbnails .= '</div>';
                    }

                }

                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($boxtitle == "true") {

                        if ($title == "true" || $description == "true") {

                            $thumbnails .= '<div class="boxtitle-hover">';
                            $thumbnails .= '<div class="work-details">';

                            if ($title == 'true') {
                                
                                $thumbnails .= '<h4>';
                                $thumbnails .= $imageTitle;
                                $thumbnails .= '</h4>';
                            }
                            if ($description == 'true') {
                                $thumbnails .= '<p class="entry-content work-description">' . $imageDesc . '</p>';
                            }

                            $thumbnails .= '</div>';
                            $thumbnails .= '</div>';
                        }
                    }
                }

                if ($url_linked) {
                    $thumbnails .= '</a>';
                }
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($linktype == "download" && $boxtitle != "true") {
                        $thumbnails .= '</div>';
                    }
                }

                $thumbnails .= '</div>'; // background hover

                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ( isSet($thumbnail_imagearray[0]) && $thumbnail_imagearray[0]<>"" ) {
                        if ($lazyload=="true") {
                            $fallback_image = imageinem_codepack_get_placeholder_image($portfolioImage_type);
                            $thumbnails .= '<img class="preload-image lazyload displayed-image" src="'. esc_url( $fallback_image ) .'" data-src="' . $thumbnail_imagearray[0] . '" alt="' . imaginem_codepack_get_alt_text($featuredID) . '">';   
                        } else {
                            $thumbnails .= '<img class="preload-image displayed-image" src="' . $thumbnail_imagearray[0] . '" alt="' . imaginem_codepack_get_alt_text($featuredID) . '">';
                        }
                        
                    }
                }
                
                $thumbnails .= '</div>';
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($boxtitle != "true") {
                        if ($title == "true" || $description == "true") {
                            $thumbnails .= '<div class="work-details">';
                            if ($title == 'true') {
                                $thumbnails .= '<h4>';
                                $thumbnails .= $imageTitle;
                                $thumbnails .= '</h4>';
                            }
                            if ($description == 'true') {
                                $thumbnails .= '<p class="entry-content work-description">' . $imageDesc . '</p>';
                            }
                            $thumbnails .= '</div>';
                        }
                    }
                }

                if ( $style == "card-spaced" || $style == "card-grid" ) {
                    if ( $title == 'true' || $description == 'true' ) {
                        $thumbnails .= '<div class="work-details text-is-bright">';
                        if ($title == 'true') {
                            $thumbnails .= '<h4 class="infobox-animate' . $animated_status . '">' . $imageTitle . '</h4>';
                        }
                        $thumbnails .= '<div class="card-work-details">';
                        if ($description == 'true') {
                            $thumbnails .= '<p class="entry-content infobox-animate' . $animated_status . ' work-description">' . $imageDesc . '</p>';
                        }

                        if ($viewmore!='') {
                            $thumbnails .= '<div class="infobox-animate' . $animated_status . '">';
                            $thumbnails .= imaginem_codepack_show_continue_reading( $viewmore , false);
                            $thumbnails .= '</div>';
                        }

                        $thumbnails .= '</div>';
                        $thumbnails .= '</div>';
                    }
                }

                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $thumbnails .= '</div>';
                    $thumbnails .= '</div>';
                    if ( $card_link ) {
                        $thumbnails .= '</a>';
                    }
                } 
                $thumbnails .= '</div>';
            }
            $thumbnails .= '</div>';
            $thumbnails .= '</div>';
            
            
            $filter_thumbnails = "";
            if ($filter == "tags") {
                
                $filter_thumbnails .= '<div class="gridblock-filter-select-wrap">';
                
                $filter_thumbnails .= '<div id="gridblock-filters">';
                $filter_thumbnails .= '<ul class="gridblock-filter-categories">';
                
                if ($filterall<>"") {
                    $filter_tag_text  = $filterall;
                } else {
                    $filter_tag_text = "All Items";
                }
                $filter_thumbnails .= '<li class="filter-icon"><i class="far fa-compass"></i></li>';
                $filter_thumbnails .= '<li class="filter-all-control">';
                $filter_thumbnails .= '<a data-filter="*" data-title="' . $filter_tag_text . '" href="#">';
                $filter_thumbnails .= '<span class="filter-seperator filter-seperator-all"></span>' . $filter_tag_text;
                $filter_thumbnails .= '</a>';
                $filter_thumbnails .= '</li>';
                
                $categories = get_categories('orderby=slug&taxonomy=worktypes&title_li=');

                $filter_seperator='';
                
                if ( 'asc' === $filtersort ) {
                    asort( $filterable_tags );
                }
                if ( 'desc' === $filtersort ) {
                    arsort( $filterable_tags );
                }

                foreach ($filterable_tags as $key=>$value) {

                        $filter_thumbnails .= '<li class="filter-control filter-category-control filter-' . $key . ' filter-control-' . $key . '">';
                        $filter_thumbnails .= '<a data-filter=".filter-' . $key . '" data-title="' . $value . '" href="#">';
                        $filter_thumbnails .= '<span class="filter-seperator filter-seperator-main">' . $filter_seperator . '</span>' . $value;
                        $filter_thumbnails .= '</a>';
                        $filter_thumbnails .= '</li>';
                }
                
                $filter_thumbnails .= '</ul>';

                $filter_thumbnails .= '</div>';
                $filter_thumbnails .= '</div>';
                //End of If Filter
            }

            return '<div class="isotope-grid-outer-wrap">' . $filter_thumbnails . $thumbnails . '</div>';
            
        }
    }
}
add_shortcode("thumbnails", "mtheme_Thumbnails");
/**
 * Portfolio Grid
 */
if (!function_exists('mPortfolioGrids')) {
    function mPortfolioGrids($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "pageid" => '',
            "style" => 'classic',
            "format" => '',
            "imagesize" => 'defaultsize',
            "effect" => 'default',
            "columns" => '4',
            "limit" => '-1',
            "like" => 'no',
            "animated_hovercard" => 'enable',
            "filter_seperator" => '',
            "viewmore" => 'View Project',
            "filterall" => 'All',
            "filter_subcats" => "false",
            "category_display" => "true",
            "gutter" => 'spaced',
            "boxtitle" => 'false',
            "boxthumbnail_link" => 'false',
            "title" => 'true',
            "desc" => 'true',
            "worktype_slugs" => '',
            "pagination" => 'false',
            "animated" => 'true',
            "type" => 'filter'
        ), $atts));

        // Set default effect
        if ($effect=="default") {
            $effect = "none";
        }

        $galleryID = uniqid();

        // empty hide filter
        $hide_filter  = '';
        
        // setup effect class
        $effect_class = ' has-effect-'.$effect;
        $relayout_on_image_class = '';
        
        // empty classes for filter and portfolio
        $portfoliogrid               = '';
        $subcat_filter_portfoliogrid = '';

        $only_title_class = '';

        // Lightbox or direct link for wall
        switch ($style) {
            case 'wall-spaced':
                $boxtitle = "box-directlink";
                if ($boxthumbnail_link=="lightbox") {
                    $boxtitle = "box-lightbox";
                }
                $gutter = "spaced";
                if ($title == 'true' && $desc == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            
            case 'wall-grid':
                $boxtitle = "box-directlink";
                if ($boxthumbnail_link=="lightbox") {
                    $boxtitle = "box-lightbox";
                }
                $gutter = "nospace";
                if ($title == 'true' && $desc == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;

            case 'card-spaced':
                $boxtitle = false;
                $gutter = 'spaced';
                $format = 'card';
                $effect = 'default';
                break;

            case 'card-grid':
                $boxtitle = false;
                $gutter = 'nospace';
                $format = 'card';
                $effect = 'default';
                break;
            
            default:
                break;
        }

        // set default style
        if (!isSet($style)) {
            $style = "classic";
        }
        
        
        $animation_class = '';
        if ($style == "wall-spaced" || $style == "wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }
        if ($animated == "true" && $style<>"wall-spaced" && $style<>"wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }

        // Set a default
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        
        $gridblock_is_masonary = imaginem_codepack_get_grid_masonry_class($format);
        $column_type = imaginem_codepack_get_grid_column_type($columns);
        $portfolioImage_type = imaginem_codepack_get_grid_image_size($format,$columns);
        $relayout_on_image_class = imaginem_codepack_grid_relayout_class($format,$columns);

        if ( 'masonary' === $format && 'fullsize' === $imagesize ) {
            $portfolioImage_type = 'superlens-gridblock-original';
        }

        if ($format == "masonary") {
            $animation_class = ' grid-animate-display-all';
        }
        
        if ($format == "portrait") {
            $protected_placeholder = '/images/blank-grid-portrait.png';
        } else {
            $protected_placeholder = '/images/blank-grid.png';
        }

        $thumbnail_gutter_class = 'portfolio-gutter-' . $gutter . ' ';
        if ($gutter == "nospace") {
            $thumbnail_gutter_class .= 'thumnails-gutter-active ';
        }
        if ($title <> "true" && $desc <> "true") {
            $thumbnail_gutter_class .= 'no-title-no-desc ';
        }

        $boxtitle_class = '';
        if ($boxtitle == "box-lightbox" || $boxtitle == "box-directlink") {
            $boxtitle_class = " boxtitle-active";
        }
        $flag_new_row = true;
        
        $portfoliogrid .= '<div class="portfolio-grid-container lightgallery-container portfolio-gridblock-columns-wrap">';
        $portfoliogrid .= '<div id="gridblock-container" class="' . $thumbnail_gutter_class . $gridblock_is_masonary . ' ' . $only_title_class . ' ' . $relayout_on_image_class . $boxtitle_class . $effect_class.' grid-style-'.$style.' gridblock-' . $column_type . ' clearfix" data-columns="' . $columns . '">';
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
        
        $count           = 0;
        $terms           = array();
        $work_slug_array = array();
        //echo $worktype_slugs;
        if ($worktype_slugs != "") {
            $type_explode = explode(",", $worktype_slugs);
            foreach ($type_explode as $work_slug) {
                $terms[] = $work_slug;
            }
            query_posts(array(
                'post_type' => 'portfolio',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'paged' => $paged,
                'posts_per_page' => $limit,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'worktypes',
                        'field' => 'slug',
                        'terms' => $terms,
                        'operator' => 'IN'
                    )
                )
            ));
        } else {
            query_posts(array(
                'post_type' => 'portfolio',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'paged' => $paged,
                'posts_per_page' => $limit
            ));
        }
        
        $idCount               = 1;
        $portfolio_count       = 0;
        $portfolio_total_count = 0;
        $portfoliofilters      = array();
        $animated_status       = '';
        if ( 'disable' == $animated_hovercard ) {
            $animated_status = '-disabled';
        }
        $view_more_class       = ' viewmore-present';
        if ( '' == $viewmore ) {
            $view_more_class = ' viewmore-absent';
        }

        
        if (have_posts()):
            while (have_posts()):
                the_post();
                //echo $type, $portfolio_type;
                $custom                 = get_post_custom(get_the_ID());
                $portfolio_cats         = get_the_terms(get_the_ID(), 'worktypes');
                $lightboxvideo          = "";
                $thumbnail              = "";
                $customlink_URL         = "";
                $description            = "";
                $portfolio_link_type    = "DirectURL";
                $portfolio_thumb_header = "Image";
                $the_only_link          = false;
                $the_protected_link     = false;
                
                if (isset($custom['pagemeta_thumbnail_linktype'][0])) {
                    $portfolio_link_type = $custom['pagemeta_thumbnail_linktype'][0];
                }
                if (isset($custom['pagemeta_lightbox_video'][0])) {
                    $lightboxvideo = $custom['pagemeta_lightbox_video'][0];
                }
                if (isset($custom['pagemeta_customthumbnail'][0])) {
                    $thumbnail = $custom['pagemeta_customthumbnail'][0];
                }
                if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                    $description = $custom['pagemeta_thumbnail_desc'][0];
                }
                if (isset($custom['pagemeta_customlink'][0])) {
                    $customlink_URL = $custom['pagemeta_customlink'][0];
                }
                if (isset($custom['pagemeta_portfoliotype'][0])) {
                    $portfolio_thumb_header = $custom['pagemeta_portfoliotype'][0];
                }
                
                // if boxed title then link directly.
                if ($boxtitle == "box-directlink") {
                    if ( 'Customlink' === $portfolio_link_type ) {
                        // Keep as is
                    } else {
                        $portfolio_link_type = "DirectURL";
                    }
                }
                if ($boxtitle == "box-lightbox") {
                    $portfolio_link_type = "Lightbox";
                }
                if ($boxtitle != "box-lightbox" && $boxtitle != "box-directlink") {
                    $boxtitle = false;
                }
                
                if ($portfolio_count == $columns)
                    $portfolio_count = 0;
                
                $add_space_class = '';
                if ($gutter != 'nospace') {
                    if ($title == 'false' && $desc == 'false') {
                        $add_space_class = 'gridblock-cell-bottom-space';
                    }
                }
                
                $protected  = "";
                $icon_class = "column-gridblock-icon";
                $portfolio_count++;
                $portfolio_total_count++;
                
                $gridblock_ajax_class = '';
                if ($type == 'ajax') {
                    $gridblock_ajax_class = "gridblock-ajax ";
                }

                $featured_image_id = get_post_thumbnail_id( get_the_id() );
                $featured_image_data = get_post($featured_image_id);
                $featuredimg_purchase_url = '';
                $featuredimg_title = '';
                $featuredimg_desc = '';
                $purchase_link = false;

                if (isSet($featured_image_data->post_title)) {
                    $featuredimg_title = $featured_image_data->post_title;
                }
                if (isSet($featured_image_data->post_content)) {
                    $featuredimg_desc = $featured_image_data->post_content;
                }

                $featuredimg_purchase_url   = get_post_meta( $featured_image_id, 'mtheme_attachment_purchase_url', true);

                if ( $featuredimg_purchase_url ) {
                    $purchase_link = $featuredimg_purchase_url;
                } else {
                    $purchase_link = false;
                }

                $featured_image_url = '';
                $height = '';
                if ( has_post_thumbnail() ) {
                    $featured_image_url = imaginem_codepack_featured_image_link( get_the_id(), $portfolioImage_type );
                }
                
                // Generate main DIV tag with portfolio information with filterable tags
                $portfoliogrid .= '<div class="gridblock-element' . $animation_class . ' isotope-displayed gridblock-element-id-' . get_the_ID() . ' gridblock-element-order-' . $portfolio_total_count . ' ' . $add_space_class . ' gridblock-filterable ';
                if (is_array($portfolio_cats)) {
                    foreach ($portfolio_cats as $taxonomy) {
                        $portfoliogrid .= 'filter-' . $taxonomy->slug . ' ';
                        if ($pagination == 'true') {
                            if (in_array($taxonomy->slug, $portfoliofilters)) {
                            } else {
                                $portfoliofilters[] = $taxonomy->slug;
                            }
                        }
                    }
                }
                $idCount++;
                $portfoliogrid .= '" data-portfolio="portfolio-' . get_the_ID() . '" data-id="id-' . $idCount . '">';

                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $hreflink = get_permalink();
                    if ( 'Customlink' === $portfolio_link_type ) {
                        $hreflink = $customlink_URL;
                    }
                    $portfoliogrid .= '<a class="gridblock-element-card-link" href="' . esc_url( $hreflink ) . '">';
                    $portfoliogrid .= '<div class="gridblock-element-card" style="'.$height.'background-image:url('. esc_url( $featured_image_url ) .');">';
                    $portfoliogrid .= '<div class="gridblock-background-card">';
                }

                $portfoliogrid .= '<div class="' . $gridblock_ajax_class . 'gridblock-grid-element gridblock-element-inner" data-portfolioid="' . get_the_id() . '">';
                
                if ($like == "yes") {
                    $portfoliogrid .= '<div class="mtheme-post-like-wrap">';
                    $portfoliogrid .= superlens_display_like_link(get_the_id());
                    $portfoliogrid .= '</div>';
                }
                $portfoliogrid .= '<div class="gridblock-background-hover">';
                
                if (post_password_required()) {
                    $the_only_link       = true;
                    $the_protected_link  = true;
                    $portfolio_link_type = "DirectURL";
                    $protected           = " gridblock-protected";
                    $iconclass           = "";
                }
                
                
                //if Password Required
                
                //Make sure it's not a slideshow
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    //Switch check for Linked Type
                    //Switch check for Linked Type
                    //
                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                    }
                    
                    if ($portfolio_link_type <> "") {
                        
                        if ($portfolio_link_type == "Lightbox_DirectURL") {
                            $portfoliogrid .= '<a class="column-gridblock-icon" href="' . get_permalink() . '">';
                            $portfoliogrid .= '<span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i></span>';
                            $portfoliogrid .= '</a>';
                        }
                        
                        
                        switch ($portfolio_link_type) {
                            case 'DirectURL':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                if (post_password_required()) {
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('locked') . '"></i>';
                                } else {
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                }
                                break;
                            
                            case 'Customlink':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . $customlink_URL . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('link') . '"></i>';
                                break;
                            
                            case 'Lightbox_DirectURL':
                                if ($lightboxvideo <> "") {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = $featuredimg_title,
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = $featuredimg_title,
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-image", $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            case 'Lightbox':
                                $the_only_link = true;
                                if ($lightboxvideo <> "") {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = $featuredimg_title,
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default", $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = $featuredimg_title,
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-image",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            default:
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                break;
                        }
                        
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                        }
                        // if it is not boxed title
                        if (!$boxtitle) {
                            if (isSet($icon_class)) {
                                if ($the_only_link) {
                                    $portfoliogrid .= '<span class="column-gridblock-icon">';
                                }
                                $portfoliogrid .= '<span class="hover-icon-effect">' . $icon_class . '</span>';
                                if ($the_only_link) {
                                    $portfoliogrid .= '</span>';
                                }
                            }
                        }
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '</div>';
                        }
                        if ($boxtitle) {
                            $portfoliogrid .= '<div class="boxtitle-hover">';
                                if ($title == 'true' || $desc == 'true') {
                                    $portfoliogrid .= '<div class="work-details'.$view_more_class.'">';
                                    $hreflink = get_permalink();
                                    if ( $category_display == 'yes' ) {
                                        $current_terms    = wp_get_object_terms(get_the_ID(), 'worktypes');
                                        $current_worktype = '';
                                        $seperator        = ' , ';
                                        // foreach ($current_terms as $the_term) {
                                        //     if ($the_term === end($current_terms)) {
                                        //         $seperator = '';
                                        //     }
                                        //     $current_worktype .= $the_term->name . $seperator;
                                        // }
                                        $portfoliogrid .= '<div class="worktype-categories">' . $current_terms[0]->name . '</div>';
                                    }
                                    if ($title == 'true') {
                                        $portfoliogrid .= '<h4>' . get_the_title() . '</h4>';
                                    }
                                    if ($desc == 'true') {
                                        $portfoliogrid .= '<p class="entry-content work-description">' . $description . '</p>';
                                    }

                                    $portfoliogrid .= '</div>';
                                }
                            $portfoliogrid .= '</div>';
                        }
                        $portfoliogrid .= '</a>';
                    }
                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '</div>';
                    }
                }

                if ($category_display == 'yes') {
                    $current_terms    = wp_get_object_terms(get_the_ID(), 'worktypes');
                    $current_worktype = '';
                    $seperator        = ' , ';
                    foreach ($current_terms as $the_term) {
                        if ($the_term === end($current_terms)) {
                            $seperator = '';
                        }
                        $current_worktype .= $the_term->name . $seperator;
                    }
                }
                
                $portfoliogrid .= '</div>';
                
                $fade_in_class = "";
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($thumbnail <> "") {
                        // Custom Image
                        $portfoliogrid .= '<img src="' . $thumbnail . '" class="' . $fade_in_class . 'displayed-image" alt="thumbnail" />';
                    } else {
                        $portfoliogrid .= imaginem_codepack_display_post_image(get_the_ID(), $have_image_url = "", $link = false, $imagetype = $portfolioImage_type, $imagetitle = imaginem_codepack_image_title(get_the_ID()), $class = $fade_in_class . "displayed-image", $lazyload=true);
                        
                    }
                }
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    $portfoliogrid .= '</div>';
                }

                if ($style == "classic" ) {
                    if ($title == 'true' || $desc == 'true') {
                        $portfoliogrid .= '<div class="work-details'.$view_more_class.'">';
                        $hreflink = get_permalink();
                        if ( 'Customlink' === $portfolio_link_type ) {
                            $hreflink = $customlink_URL;
                        }
                        if ( $style=="classic" && $category_display == 'yes' ) {
                            $portfoliogrid .= '<div class="worktype-categories">' . $current_worktype . '</div>';
                        }
                        if ($title == 'true') {
                            if ($type != "ajax") {
                                $portfoliogrid .= '<h4><a href="' . $hreflink . '">' . get_the_title() . '</a></h4>';
                            } else {
                                $portfoliogrid .= '<h4>';
                                $portfoliogrid .= get_the_title();
                                $portfoliogrid .= '</h4>';
                            }
                        }
                        if ($desc == 'true') {
                            $portfoliogrid .= '<p class="entry-content work-description">' . $description . '</p>';
                        }

                        $portfoliogrid .= '</div>';
                    }
                }

                if ( $style == "card-spaced" || $style == "card-grid" ) {
                    if ( $title == 'true' || $desc == 'true' || $category_display == 'yes' ) {
                        $portfoliogrid .= '<div class="work-details'.$view_more_class.' text-is-bright">';
                        $hreflink = get_permalink();
                        if ( $category_display == 'yes' ) {
                            $current_terms = wp_get_object_terms( get_the_ID(), 'worktypes'  );
                            $current_worktype = '';
                            $seperator = '';
                            foreach( $current_terms as $the_term ) {
                                $current_worktype .= $the_term->name . $seperator;
                                break;
                            }
                            $portfoliogrid .= '<div class="infobox-animate' . $animated_status . ' worktype-categories">' . $current_worktype . '</div>';
                        }
                        if ($title == 'true') {
                            $portfoliogrid .= '<h4 class="infobox-animate' . $animated_status . '">' . get_the_title() . '</h4>';
                        }
                        $portfoliogrid .= '<div class="card-work-details">';
                        if ($desc == 'true') {
                            $portfoliogrid .= '<p class="entry-content infobox-animate' . $animated_status . ' work-description">' . $description . '</p>';
                        }

                        if ( $viewmore != '' ) {
                            $portfoliogrid .= '<div class="infobox-animate' . $animated_status . '">';
                            $portfoliogrid .= imaginem_codepack_show_continue_reading( $viewmore , false);
                            $portfoliogrid .= '</div>';
                        }

                        $portfoliogrid .= '</div>';
                        $portfoliogrid .= '</div>';
                    }
                }
                
                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $portfoliogrid .= '</div>';
                    $portfoliogrid .= '</div>';
                    $portfoliogrid .= '</div>';
                    $portfoliogrid .= '</a>';
                }                
                $portfoliogrid .= '</div>';
            //if ($portfolio_count==$columns)  $portfoliogrid .='</div>';
            endwhile;
        endif;
        // if ($format=="masonary") {
        // 	$portfoliogrid .= '</div>';
        // }
        $portfoliogrid .= '</div>';
        
        if ($pagination == 'true') {
            $portfolio_pagination = imaginem_codepack_pagination();
            if ($portfolio_pagination<>"") {
                $portfoliogrid .= '<div class="clearfix">';
                $portfoliogrid .= $portfolio_pagination;
                $portfoliogrid .= '</div>';
            }
        }
        $portfoliogrid .= '</div>';
        
        wp_reset_query();
        
        if ($type == "filter" || $type == "ajax") {
            
            $filter_portfoliogrid = "";
            
            $countquery = array(
                'post_type' => 'portfolio',
                'worktypes' => $worktype_slugs,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'posts_per_page' => -1
            );
            query_posts($countquery);
            if (have_posts()):
                while (have_posts()):
                    the_post();
                endwhile;
            endif;
            
            if ($type == "ajax") {
                $filter_portfoliogrid .= '	<div class="ajax-gridblock-block-wrap clearfix">';
                $filter_portfoliogrid .= '	<div class="ajax-gallery-navigation clearfix">';
                $filter_portfoliogrid .= '		<a class="ajax-navigation-arrow ajax-next" href="#"><i class="feather-icon-arrow-right"></i></a>';
                $filter_portfoliogrid .= '		<a class="ajax-navigation-arrow ajax-hide" href="#"><i class="feather-icon-align-justify"></i></a>';
                $filter_portfoliogrid .= '		<a class="ajax-navigation-arrow ajax-prev" href="#"><i class="feather-icon-arrow-left"></i></a>';
                $filter_portfoliogrid .= '		<span class="ajax-loading"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></span>';
                $filter_portfoliogrid .= '	</div>';
                $filter_portfoliogrid .= '	<div class="ajax-gridblock-window">';
                $filter_portfoliogrid .= '		<div id="ajax-gridblock-wrap"></div>';
                $filter_portfoliogrid .= '	</div>';
                $filter_portfoliogrid .= '	</div>';
            }
            $filter_portfoliogrid .= '<div class="gridblock-filter-select-wrap">';
            
            $filter_portfoliogrid .= '<div id="gridblock-filters">';
            $filter_portfoliogrid .= '<ul class="gridblock-filter-categories">';
            
            $filter_portfoliogrid .= '<li class="filter-icon"><i class="far fa-compass"></i></li>';
            $filter_portfoliogrid .= '<li class="filter-all-control">';
            $filter_portfoliogrid .= '<a data-filter="*" data-title="' . $filterall . '" href="#">';
            $filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-all"></span>' . $filterall;
            $filter_portfoliogrid .= '</a>';
            $filter_portfoliogrid .= '</li>';
            
            //$categories=  get_categories('child_of='.$portfolio_cat_ID.'&orderby=slug&taxonomy=worktypes&title_li=');
            if ($worktype_slugs != '')
                $all_works = explode(",", $worktype_slugs);
            if ($filter_subcats == "true") {
                $categories = get_categories('orderby=slug&parent=0&taxonomy=worktypes&title_li=');
            } else {
                $categories = get_categories('orderby=slug&taxonomy=worktypes&title_li=');
            }
            foreach ($categories as $category) {
                
                $taxonomy = 'worktypes';
                
                // Using Term Slug
                $term_slug = $category->slug;
                $term      = get_term_by('slug', $term_slug, $taxonomy);
                
                // Enter only if Works is not set - means all included OR if work types are defined in shortcode
                if (!isSet($all_works) || in_array($term_slug, $all_works)) {
                    // Fetch the count
                    //echo $term->count;
                    if ($pagination == 'true') {
                        if (is_array($portfoliofilters)) {
                            $filter_found = false;
                            $hide_filter  = '';
                            if (in_array($category->slug, $portfoliofilters)) {
                                $filter_found = true;
                            }
                            
                        }
                        if (!$filter_found) {
                            $hide_filter = 'style="display:none;"';
                            //echo $category->slug;
                        }
                    }
                    $filter_portfoliogrid .= '<li ' . $hide_filter . ' class="filter-control filter-category-control filter-' . $category->slug . ' filter-control-' . $category->slug . '">';
                    $filter_portfoliogrid .= '<a data-filter=".filter-' . $category->slug . '" data-title="' . $category->name . '" href="#">';
                    $filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-main">' . $filter_seperator . '</span>' . $category->name;
                    $filter_portfoliogrid .= '</a>';
                    $filter_portfoliogrid .= '</li>';
                    
                    // Populate Subcategories
                    if ($filter_subcats == "true") {
                        $portfolio_subcategories = get_categories('orderby=slug&taxonomy=worktypes&child_of=' . $category->term_id . '&title_li=');
                        //print_r($portfolio_subcategories);
                        
                        foreach ($portfolio_subcategories as $portfolio_subcategory) {
                            //print_r($portfolio_subcategory->slug);
                            $sub_filter_seperator = '';
                            $subcat_filter_portfoliogrid .= '<li class="filter-' . $category->slug . '-of-parent filter-subcat-control filter-control filter-' . $portfolio_subcategory->slug . ' filter-control-' . $portfolio_subcategory->slug . '">';
                            $subcat_filter_portfoliogrid .= '<a data-filter=".filter-' . $portfolio_subcategory->slug . '" data-title="' . $portfolio_subcategory->name . '" href="#">';
                            $subcat_filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-sub">' . $sub_filter_seperator . '</span>' . $portfolio_subcategory->name;
                            $subcat_filter_portfoliogrid .= '</a>';
                            $subcat_filter_portfoliogrid .= '</li>';
                        }
                    }
                }
            }
            
            $filter_portfoliogrid .= '</ul>';
            
            if ($subcat_filter_portfoliogrid <> '' && $filter_subcats == "true") {
                $subcat_filter_portfoliogrid = '<ul class="griblock-filters-subcats">' . $subcat_filter_portfoliogrid . '</ul>';
            }
            $filter_portfoliogrid .= $subcat_filter_portfoliogrid;
            $filter_portfoliogrid .= '</div>';
            $filter_portfoliogrid .= '</div>';
            //End of If Filter
        }
        
        if (isSet($filter_portfoliogrid)) {
            $portfoliogrid = '<div class="isotope-grid-outer-wrap">' . $filter_portfoliogrid . $portfoliogrid . '</div>';
        }
        //Reset query after Filters
        
        wp_reset_query();
        return $portfoliogrid;
    }
}
add_shortcode("portfoliogrid", "mPortfolioGrids");


//Recent Works Carousel
if (!function_exists('mLightboxCarousel')) {
    function mLightboxCarousel($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "pageid" => '',
            "pb_image_ids" => '',
            "format" => '',
            "carousel_type" => 'owl',
            "lazyload" => 'false',
            "smartspeed" => '1000',
            "autoplay" => 'false',
            "thumbnails" => 'true',
            "autoplayinterval" => '5000',
            "lightbox" => 'true',
            "pagination" => 'false',
            "columns" => '4',
            "limit" => '-1',
            "title" => 'true',
            "desc" => 'true',
            "displaytitle" => 'true',
            "worktype_slug" => '',
            "pagination" => 'false'
        ), $atts));
        
        $galleryID           = uniqid();
        $uniqureID           = get_the_id() . "-" . uniqid();
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        if ($columns == 4) {
            $column_type         = "four";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 3) {
            $column_type         = "three";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 2) {
            $column_type         = "three";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        
        if ($format == "portrait") {
            if ($columns == 4) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 3) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 2) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 1) {
                $portfolioImage_type = "superlens-gridblock-full";
            }
        }
        
        if ($format == "full") {
            $portfolioImage_type = "superlens-gridblock-full";
        }
        
        if ($worktype_slug == "-1") {
            $worktype_slug = '';
        }
        $portfolio_count = 0;
        $flag_new_row    = true;
        $portfoliogrid   = '';
        
        if ($carousel_type == "owl") {
            
            $portfoliogrid .= '<div class="gridblock-owlcarousel-wrap lightgallery-container lightgallery-status-'.esc_attr($lightbox).' clearfix">';
            $portfoliogrid .= '<div id="owl-' . $uniqureID . '" class="owl-carousel owl-works-detect" data-id="owl-' . $uniqureID . '" data-lazyload="'.$lazyload.'" data-columns="'.$columns.'" data-pagination="'.$pagination.'" data-autoplaytimeout="'.$autoplayinterval.'" data-smartspeed="'.$smartspeed.'" data-autoplay="'.$autoplay.'">';

            $filter_image_ids = explode(',', $pb_image_ids);
            
            $count_lightboxes = 0;
            
            if ($filter_image_ids) {
                foreach ($filter_image_ids as $attachment_id) {

                    $check_if_image_present = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
                    if ( !$check_if_image_present ) {
                        continue;
                    }
                    
                    //echo $type, $portfolio_type;
                    $custom                 = get_post_custom(get_the_ID());
                    $portfolio_cats         = get_the_terms(get_the_ID(), 'worktypes');
                    $lightboxvideo          = "";
                    $thumbnail              = "";
                    $customlink_URL         = "";
                    $portfolio_thumb_header = "Image";
                    
                    $imagearray = wp_get_attachment_image_src($attachment_id, 'fullsize', false);
                    $imageURI   = $imagearray[0];
                    
                    $thumbnail_imagearray = wp_get_attachment_image_src($attachment_id, $portfolioImage_type, false);
                    $thumbnail_imageURI   = $thumbnail_imagearray[0];
                    
                    if (isSet($thumbnail_imageURI)) {
                        
                        $imageTitle = '';
                        $imageDesc  = '';
                        $imageID    = get_post($attachment_id);
                        if (isSet($imageID->post_title)) {
                            $imageTitle = $imageID->post_title;
                        }
                        if (isSet($imageID->post_content)) {
                            $imageDesc = $imageID->post_content;
                        }
                        
                        if ($portfolio_count == $columns)
                            $portfolio_count = 0;
                        
                        $protected  = "";
                        $icon_class = "column-gridblock-icon";
                        $portfolio_count++;
                        $portfoliogrid .= '<div class="gridblock-grid-element">';
                        if ($lightbox =="true") {
                            $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                $lightbox_type = "default",
                                $ID = get_the_ID(),
                                $link = $imageURI,
                                $mediatype = "image",
                                $the_imagetitle = $imageTitle,
                                $class = "column-gridblock-lightbox lightbox-image",
                                $set="owlslideshow-lightbox-set",
                                $data_name = "default",
                                $external_thumbnail_id = $attachment_id,
                                $imageDataID=$attachment_id,
                                $galleryID
                            );
                        }
                        $portfoliogrid .= '<div class="gridblock-background-hover">';

                        if ($lightbox =="true") {
                            $portfoliogrid .= '<div class="gridblock-links-wrap">';
                            
                            $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                            
                            if ($icon_class)
                                $portfoliogrid .= '<span class="column-gridblock-icon"><span class="hover-icon-effect">' . $icon_class . '</span></span>';
                            
                            $portfoliogrid .= '</div>';
                        }
                        if ($displaytitle == "true") {
                            $portfoliogrid .= '<div class="boxtitle-hover">';
                            $portfoliogrid .= '<h3>';
                            $portfoliogrid .= $imageTitle;
                            $portfoliogrid .= '</h3>';
                            $portfoliogrid .= '</div>';
                        }
                        $portfoliogrid .= '</div>';
                        
                        $portfoliogrid .= imaginem_codepack_display_post_image( $attachment_id, $have_image_url = $thumbnail_imageURI, $link = false, $type = $portfolioImage_type, $imagetitle = $imageTitle, $class = "displayed-image", $lazyload = true);
                        
                        if ($lightbox =="true") {
                            $portfoliogrid .= '</a>';
                        }
                        $count_lightboxes++;
                        
                        $portfoliogrid .= '</div>';
                        
                    }
                    
                }
            }
            $portfoliogrid .= '</div>';
            $portfoliogrid .= '</div>';
            
        }
        
        wp_reset_query();
        return $portfoliogrid;
    }
}
add_shortcode("lightboxcarousel", "mLightboxCarousel");

//Recent Works Carousel
if (!function_exists('mWorksCarousel')) {
    function mWorksCarousel($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "pageid" => '',
            "format" => '',
            "carousel_type" => 'owl',
            "category_display" => "true",
            "autoplayinterval" => '5000',
            "smartspeed" => '1000',
            "autoplay" => 'false',
            "columns" => '4',
            "limit" => '-1',
            "title" => 'true',
            "lazyload" => 'false',
            "directlink" => 'false',
            "boxtitle" => 'true',
            "worktype_slug" => '',
            "pagination" => 'true'
        ), $atts));
        
        $galleryID = uniqid();
        $uniqureID           = get_the_id() . "-" . uniqid();
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        if ($columns == 4) {
            $column_type         = "four";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 3) {
            $column_type         = "three";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 2) {
            $column_type         = "three";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        
        if ($format == "portrait") {
            if ($columns == 4) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 3) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 2) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 1) {
                $portfolioImage_type = "superlens-gridblock-full";
            }
        }

        if ( 'square' === $format ) {
            $portfolioImage_type = "superlens-gridblock-square-big";
        }
        
        if ($worktype_slug == "-1") {
            $worktype_slug = '';
        }
        $portfolio_count = 0;
        $flag_new_row    = true;
        $portfoliogrid   = '';
        
        if ($carousel_type == "owl") {
            
            $portfoliogrid .= '<div class="gridblock-owlcarousel-wrap clearfix">';
            $portfoliogrid .= '<div id="owl-' . $uniqureID . '" data-id="owl-' . $uniqureID . '" data-lazyload="'.$lazyload.'" data-columns="'.$columns.'" data-pagination="'.$pagination.'" data-autoplaytimeout="'.$autoplayinterval.'" data-smartspeed="'.$smartspeed.'" data-autoplay="'.$autoplay.'" class="owl-carousel owl-works-detect lightgallery-container" >';
            if (get_query_var('paged')) {
                $paged = get_query_var('paged');
            } elseif (get_query_var('page')) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }
            query_posts(array(
                'post_type' => 'portfolio',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'worktypes' => $worktype_slug,
                'paged' => $paged,
                'posts_per_page' => $limit
            ));
            
            if (have_posts()):
                while (have_posts()):
                    the_post();
                    
                    //echo $type, $portfolio_type;
                    $custom                 = get_post_custom(get_the_ID());
                    $portfolio_cats         = get_the_terms(get_the_ID(), 'worktypes');
                    $lightboxvideo          = "";
                    $portfolio_link_type    = "DirectURL";
                    $thumbnail              = "";
                    $customlink_URL         = "";
                    $portfolio_thumb_header = "Image";

                    $featured_image_id = get_post_thumbnail_id( get_the_id() );
                    $featured_image_data = get_post($featured_image_id);
                    
                    $the_only_link      = false;
                    $the_protected_link = false;
                    
                    if (isset($custom['pagemeta_thumbnail_linktype'][0])) {
                        $portfolio_link_type = $custom['pagemeta_thumbnail_linktype'][0];
                    }
                    if (isset($custom['pagemeta_lightbox_video'][0])) {
                        $lightboxvideo = $custom['pagemeta_lightbox_video'][0];
                    }
                    if (isset($custom['pagemeta_customthumbnail'][0])) {
                        $thumbnail = $custom['pagemeta_customthumbnail'][0];
                    }
                    if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                        $description = $custom['pagemeta_thumbnail_desc'][0];
                    }
                    if (isset($custom['pagemeta_customlink'][0])) {
                        $customlink_URL = $custom['pagemeta_customlink'][0];
                    }
                    if (isset($custom['pagemeta_portfoliotype'][0])) {
                        $portfolio_thumb_header = $custom['pagemeta_portfoliotype'][0];
                    }

                    if ($directlink=="true") {
                        $portfolio_link_type = "DirectURL";
                    }
                    
                    if ($portfolio_count == $columns)
                        $portfolio_count = 0;
                    
                    $protected  = "";
                    $icon_class = "column-gridblock-icon";

                    $portfolio_count++;
                    $portfoliogrid .= '<div class="gridblock-grid-element">';
                    
                    $portfoliogrid .= '<div class="gridblock-background-hover">';

                    $link_open = false;
                    
                    if (post_password_required()) {
                        $portfolio_link_type = "DirectURL";
                    }

                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                    }

                        if ($portfolio_link_type == "Lightbox_DirectURL") {
                            $portfoliogrid .= '<a class="column-gridblock-icon" href="' . get_permalink() . '">';
                            $portfoliogrid .= '<span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i></span>';
                            $portfoliogrid .= '</a>';
                        }
                        
                        switch ($portfolio_link_type) {
                            case 'DirectURL':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                $link_open = true;
                                break;
                            
                            case 'Customlink':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . $customlink_URL . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('link') . '"></i>';
                                $link_open = true;
                                break;
                            
                            case 'Lightbox_DirectURL':
                                if ($lightboxvideo <> "") {
                                    $get_lightbox_link = imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = get_the_title(),
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                    );
                                    if ($get_lightbox_link) {
                                        $portfoliogrid .= $get_lightbox_link;
                                        $link_open = true;
                                    }
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $get_lightbox_link = imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default", $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = imaginem_codepack_image_title(get_the_ID()),
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-image",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                    );
                                    if ($get_lightbox_link) {
                                        $portfoliogrid .= $get_lightbox_link;
                                        $link_open = true;
                                    }
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            case 'Lightbox':
                                $the_only_link = true;
                                if ($lightboxvideo <> "") {
                                    $get_lightbox_link = imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default", $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = get_the_title(),
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                    );
                                    if ($get_lightbox_link) {
                                        $portfoliogrid .= $get_lightbox_link;
                                        $link_open = true;
                                    }
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $get_lightbox_link = imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = imaginem_codepack_image_title(get_the_ID()),
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-image",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                    );
                                    if ($get_lightbox_link) {
                                        $portfoliogrid .= $get_lightbox_link;
                                        $link_open = true;
                                    }
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            default:
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                $link_open = true;
                                break;
                        }
                        
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                        }

                        if (post_password_required()) {
                            $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('locked') . '"></i>';
                        }
                        //$portfoliogrid .= '<span class="gridblock-image-hover">';
                        if (isSet($icon_class)) {
                            if ($the_only_link) {
                                $portfoliogrid .= '<span class="column-gridblock-icon">';
                            }
                            $portfoliogrid .= '<span class="hover-icon-effect">' . $icon_class . '</span>';
                            if ($the_only_link) {
                                $portfoliogrid .= '</span>';
                            }
                        }
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '</div>';
                        }
                        if ($link_open) {
                            $portfoliogrid .= '</a>';
                        }
                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '</div>';
                    }
                    //$portfoliogrid .= '</span>';
                    // If it aint slideshow then display a background. Otherwise one is active in slideshow thumbnails.
                    // Custom Thumbnail
                    if ($boxtitle == "true") {
                        
                        $current_terms    = wp_get_object_terms(get_the_ID(), 'worktypes');
                        $current_worktype = '';
                        $seperator        = ',';
                        foreach ($current_terms as $the_term) {
                            if ($the_term === end($current_terms)) {
                                $seperator = '';
                            }
                            $current_worktype .= $the_term->name . $seperator;
                        }
                        
                        $portfoliogrid .= '<span class="boxtitle-hover">';
                        $portfoliogrid .= '<a href="' . get_permalink() . '">';
                        $portfoliogrid .= get_the_title();
                        $portfoliogrid .= '</a>';
                        if ( 'true' === $category_display ) {
                            $portfoliogrid .= '<span class="boxtitle-worktype">' . $current_worktype . '</span>';
                        }
                        $portfoliogrid .= '</span>';
                    }
                    $portfoliogrid .= '</div>';
                    
                    if ($thumbnail <> "") {
                        $portfoliogrid .= '<img src="' . $thumbnail . '" class="displayed-image" alt="thumbnail" />';
                    } else {
                        // Slideshow then generate slideshow shortcode
                        $portfoliogrid .= imaginem_codepack_display_post_image(get_the_ID(), $have_image_url = "", $link = false, $imagetype = $portfolioImage_type, $imagetitle = imaginem_codepack_image_title(get_the_ID()), $class = "displayed-image");
                        
                    }
                
                    
                    $portfoliogrid .= '</div>';
                endwhile;
            endif;
            $portfoliogrid .= '</div>';
            $portfoliogrid .= '</div>';
            
            if ($pagination <> "true") {
                $pagination = "false";
            }
            
        }
        
        wp_reset_query();
        return $portfoliogrid;
    }
}
add_shortcode("workscarousel", "mWorksCarousel");
// Since version 2.5
if (!function_exists('mtheme_shortcode_worktype_albums')) {
    function mtheme_shortcode_worktype_albums($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "style" => 'classic',
            "effect" => 'default',
            "gutter" => 'spaced',
            "boxtitle" => 'false',
            "worktype_slugs" => '',
            "format" => '',
            "columns" => 4,
            "item_text" => 'image',
            "items_text" => 'images',
            "item_count" => true,
            "title" => true,
            "animated" => 'true',
            "description" => 'true',
            "worktype_icon" => 'ion-ios-albums-outline'
        ), $atts));

        $galleryID = uniqid();
        // empty hide filter
        $hide_filter  = '';
        
        // setup effect class
        $effect_class = ' has-effect-'.$effect;
        $relayout_on_image_class = '';
        
        // empty classes for filter and portfolio
        $portfoliogrid               = '';
        $subcat_filter_portfoliogrid = '';

        $only_title_class = '';

        // Lightbox or direct link for wall
        switch ($style) {
            case 'wall-spaced':
                $boxtitle = "box-directlink";
                $gutter = "spaced";
                if ($title == 'true' && $description == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            
            case 'wall-grid':
                $boxtitle = "box-directlink";
                $gutter = "nospace";
                if ($title == 'true' && $description == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            
            default:
                break;
        }

        // set default style
        if (!isSet($style)) {
            $style = "classic";
        }

        $animation_class = '';
        if ($style == "wall-spaced" || $style == "wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }
        if ($animated == "true" && $style<>"wall-spaced" && $style<>"wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }
        
        if ($worktype_icon == "") {
            $worktype_icon = 'ion-ios-albums-outline';
        }
        
        // Set a default
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        
        $gridblock_is_masonary = imaginem_codepack_get_grid_masonry_class($format);
        $column_type = imaginem_codepack_get_grid_column_type($columns);
        $portfolioImage_type = imaginem_codepack_get_grid_image_size($format,$columns);
        $relayout_on_image_class = imaginem_codepack_grid_relayout_class($format,$columns);

        if ($format == "masonary") {
            $animation_class = ' grid-animate-display-all';
        }

        if ($format == "portrait") {
            $protected_placeholder = '/images/blank-grid-portrait.png';
        } else {
            $protected_placeholder = '/images/blank-grid.png';
        }

        $thumbnail_gutter_class = 'portfolio-gutter-' . $gutter . ' ';
        if ($gutter == "nospace") {
            $thumbnail_gutter_class .= 'thumnails-gutter-active ';
        }
        if ($title <> "true" && $desc <> "true") {
            $thumbnail_gutter_class .= 'no-title-no-desc ';
        }

        $boxtitle_class = '';
        if ($boxtitle == "box-lightbox" || $boxtitle == "box-directlink") {
            $boxtitle_class = " boxtitle-active";
        }
        $flag_new_row = true;
        
        $portfoliogrid = '<div class="gridblock-columns-wrap portfolio-albums clearfix">';
        $portfoliogrid .= '<div id="gridblock-container" class="' . $thumbnail_gutter_class . $gridblock_is_masonary . ' ' . $only_title_class . ' ' . $relayout_on_image_class . $boxtitle_class . $effect_class.' grid-style-'.$style.' gridblock-' . $column_type . ' clearfix" data-columns="' . $columns . '">';
        
        //$categories=  get_categories('child_of='.$portfolio_cat_ID.'&orderby=slug&taxonomy=worktypes&title_li=');
        if ($worktype_slugs != '')
            $all_works = explode(",", $worktype_slugs);
        $categories = get_categories('orderby=slug&taxonomy=worktypes&title_li=');
        
        foreach ($categories as $category) {
            $taxonomy = 'worktypes'; // can be category, post_tag, or custom taxonomy name
            
            // Use any one of the three methods below
            
            // Using Term ID
            //$term_id = $category->term_id;
            //$term = get_term_by('id', $term_id, $taxonomy);
            
            // Using Term Name
            //$term_name = 'A Category';
            //$term = get_term_by('name', $term_name, $taxonomy);
            
            // Using Term Slug
            $term_slug = $category->slug;
            $term      = get_term_by('slug', $term_slug, $taxonomy);
            
            // Enter only if Works is not set - means all included OR if work types are defined in shortcode
            if (!isSet($all_works) || in_array($term_slug, $all_works)) {
                // Fetch the count
                //echo $term->count;
                
                
                $hreflink                 = get_term_link($category->slug, 'worktypes');
                $mtheme_worktype_image_id = get_option('mtheme_worktype_image_id' . $category->term_id);
                $work_type_image          = wp_get_attachment_image_src($mtheme_worktype_image_id, $portfolioImage_type, false);
                
                $portfoliogrid .= '<div class="gridblock-element ' . $animation_class . ' ' . '">';
                $portfoliogrid .= '<div class="gridblock-grid-element gridblock-element-inner">';
                $portfoliogrid .= '<div class="gridblock-background-hover">';
                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . esc_url($hreflink) . '">';

                if ($item_count == 'true') {
				    $portfoliogrid .= '<div class="gridblock-links-wrap">
						<span class="column-gridblock-icon">
							<span class="hover-icon-effect">
								<i class="' . $worktype_icon . '"></i>
							</span>
						</span>';
                    
                    //Count the items and reset
                    $countquery = array(
                        'post_type' => 'portfolio',
                        'worktypes' => $category->slug,
                        'posts_per_page' => -1
                    );
                    query_posts($countquery);
                    $item_counter = 0;
                    if (have_posts()):
                        while (have_posts()):
                            the_post();
                            $item_counter++;
                        endwhile;
                    endif;
                    
                    wp_reset_query();
                    
                    //Check number of items
                    if ($item_counter == 1) {
                        $count_suffix = $item_text;
                    } else {
                        $count_suffix = $items_text;
                    }
                    
                
                    $portfoliogrid .= '</div>';
                }

                if ($style <> "classic" ) {
                    $portfoliogrid .= '<div class="boxtitle-hover">';
                        if ($title == 'true' || $description == 'true') {
                            
                            $portfoliogrid .= '<div class="work-details">';
                            $portfoliogrid .= '<div class="infobox-animate-disabled worktype-categories">' . $item_counter . ' ' . $count_suffix . '</div>';
                            if ($title == 'true') {
                                $portfoliogrid .= '<h4>' . $category->name . '</h4>';
                            }
                            if ($description == 'true') {
                                $portfoliogrid .= '<p class="entry-content work-description">' . $category->description . '</p>';
                            }

                            $portfoliogrid .= '</div>';
                        }
                    $portfoliogrid .= '</div>';
                }

                $portfoliogrid .= '</a>';
				$portfoliogrid .= '</div>';
                
                //Display image
                if ( isset( $work_type_image[0] ) ) {
                    $portfoliogrid .= '<img class="preload-image displayed-image" src="' . $work_type_image[0] . '" alt="' . get_the_title() . '">';
                }
                //$portfoliogrid .= '</span>';
                $portfoliogrid .= '</div>';
                if ($style == "classic" ) {
                    if ($title == "true" || $description == "true") {
                        $portfoliogrid .= '<div class="work-details">';
                        if ($title == 'true') {
                            $portfoliogrid .= '<h4>';
                            $portfoliogrid .= '<a href="' . $hreflink . '">';
                            $portfoliogrid .= $category->name;
                            $portfoliogrid .= '</a>';
                            $portfoliogrid .= '</h4>';
                        }
                        if ($description == 'true') {
                            $portfoliogrid .= '<p class="entry-content work-description">' . $category->description . '</p>';
                        }
                        $portfoliogrid .= '</div>';
                    }
                }
                
                $portfoliogrid .= '</div>';
            }
        }
        $portfoliogrid .= '</div>';
        $portfoliogrid .= '</div>';
        
        return $portfoliogrid;
    }
}
add_shortcode("worktype_albums", "mtheme_shortcode_worktype_albums");
/**
 * Portfolio Grid
 */
if (!function_exists('mmtheme_GalleryGrids')) {
    function mmtheme_GalleryGrids($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "pageid" => '',
            "format" => '',
            "columns" => '4',
            "limit" => '-1',
            "animated" => 'true',
            "grid_post_type" => '',
            "grid_taxonomy" => '',
            "displaycategory" => 'false',
            "gutter" => 'spaced',
            "boxtitle" => 'true',
            "title" => 'true',
            "desc" => 'true',
            "worktype_slugs" => '',
            "pagination" => 'false',
            "type" => 'filter'
        ), $atts));

        if ($animated == "true") {
            $animation_class = ' animation-standby-portfolio animated fadeIn';
        }
        
        $galleryID = uniqid();
        $portfoliogrid       = '';
        $relayout_on_image_class = "";
        // Set a default
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        
        if ($columns == 4) {
            $column_type         = "four";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 3) {
            $column_type         = "three";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 2) {
            $column_type         = "two";
            $portfolioImage_type = "superlens-gridblock-large";
        }
        if ($columns == 1) {
            $column_type         = "one";
            $relayout_on_image_class = "relayout-on-image-load";
            $portfolioImage_type = "superlens-gridblock-full";
        }
        
        if ($format == "portrait") {
            if ($columns == 4) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 3) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 2) {
                $portfolioImage_type = "superlens-gridblock-large-portrait";
            }
            if ($columns == 1) {
                $relayout_on_image_class = "relayout-on-image-load";
                $portfolioImage_type = "superlens-gridblock-full";
            }
        }
        $gridblock_is_masonary = "";
        if ($format == "masonary") {
            
            $gridblock_is_masonary = "gridblock-masonary ";
            $relayout_on_image_class = "relayout-on-image-load";

            if ($columns == 4) {
                $portfolioImage_type = "superlens-gridblock-full-medium";
            }
            if ($columns == 3) {
                $portfolioImage_type = "superlens-gridblock-full-medium";
            }
            if ($columns == 2) {
                $portfolioImage_type = "superlens-gridblock-full-medium";
            }
            if ($columns == 1) {
                $portfolioImage_type = "superlens-gridblock-full";
            }
        }
        
        if ($format == "portrait") {
            $protected_placeholder = '/images/blank-grid-portrait.png';
        } else {
            $protected_placeholder = '/images/blank-grid.png';
        }
        //$preload_tag = '<div class="preloading-placeholder"><span class="preload-image-animation"></span><img src="'.get_template_directory_uri().$protected_placeholder.'" alt="preloading" /></div>';
        $thumbnail_gutter_class = 'portfolio-gutter-' . $gutter . ' ';
        if ($gutter == "nospace") {
            $thumbnail_gutter_class .= 'thumnails-gutter-active ';
        }
        $flag_new_row = true;
        
        if ($format == "square") {
            $portfolioImage_type = "superlens-gridblock-square-big";
        }

        $style = "classic";
        
        $xtra_class = '';
        if ($desc == 'false') {
            $xtra_class = ' gridblock-desc-off';
        }
        $portfoliogrid .= '<div class="gridblock-columns-wrap  grid-style-'.$style.'-wrap clearfix">';

        $portfoliogrid .= '<div id="gridblock-container" class="' . $thumbnail_gutter_class . $gridblock_is_masonary . 'gridblock-' . $column_type . $xtra_class . ' post-grid-' . $grid_post_type . ' '.$relayout_on_image_class.' grid-style-'.$style.' clearfix" data-columns="' . $columns . '">';
        // if ($format=="masonary") {
        // 	$portfoliogrid .= '<div class="gridblock-masonary-inner">';
        // }
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
        
        $count           = 0;
        $terms           = array();
        $work_slug_array = array();
        //echo $worktype_slugs;
        if ($worktype_slugs != "" && $grid_taxonomy != '') {
            $type_explode = explode(",", $worktype_slugs);
            foreach ($type_explode as $work_slug) {
                $terms[] = $work_slug;
            }
            query_posts(array(
                'post_type' => $grid_post_type,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'paged' => $paged,
                'posts_per_page' => $limit,
                'tax_query' => array(
                    array(
                        'taxonomy' => $grid_taxonomy,
                        'field' => 'slug',
                        'terms' => $terms,
                        'operator' => 'IN'
                    )
                )
            ));
        } else {
            query_posts(array(
                'post_type' => $grid_post_type,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'paged' => $paged,
                'posts_per_page' => $limit
            ));
        }
        
        $idCount               = 1;
        $portfolio_count       = 0;
        $portfolio_total_count = 0;
        
        if (have_posts()):
            while (have_posts()):
                the_post();
                //echo $type, $portfolio_type;
                $custom                 = get_post_custom(get_the_ID());
                $portfolio_cats         = get_the_terms(get_the_ID(), 'fullscreengallery');
                $lightboxvideo          = "";
                $thumbnail              = "";
                $customlink_URL         = "";
                $description            = "";
                $portfolio_thumb_header = "Image";
                
                if (isset($custom['pagemeta_thumbnail_linktype'][0])) {
                    $portfolio_link_type = $custom['pagemeta_thumbnail_linktype'][0];
                }
                if (isset($custom['pagemeta_lightbox_video'][0])) {
                    $lightboxvideo = $custom['pagemeta_lightbox_video'][0];
                }
                if (isset($custom['pagemeta_customthumbnail'][0])) {
                    $thumbnail = $custom['pagemeta_customthumbnail'][0];
                }
                if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                    $description = $custom['pagemeta_thumbnail_desc'][0];
                }
                if (isset($custom['pagemeta_customlink'][0])) {
                    $customlink_URL = $custom['pagemeta_customlink'][0];
                }
                if (isset($custom['pagemeta_portfoliotype'][0])) {
                    $portfolio_thumb_header = $custom['pagemeta_portfoliotype'][0];
                }
                
                if ($portfolio_count == $columns)
                    $portfolio_count = 0;
                
                $add_space_class = '';
                $xtra_class      = '';
                if ($gutter != 'nospace') {
                    if ($title == 'false' && $desc == 'false') {
                        $add_space_class = 'gridblock-cell-bottom-space';
                    }
                }
                $title_desc_class = '';
                if ($grid_post_type == "clients") {
                    if ($title == "false") {
                        $title_desc_class = " no-title-no-desc";
                        $add_space_class  = '';
                    }
                }
                $boxtitle_class = '';
                if ($boxtitle == "true") {
                    $boxtitle_class = " boxtitle-active";
                }
                
                $protected  = "";
                $icon_class = "column-gridblock-icon";
                $portfolio_count++;
                $portfolio_total_count++;
                
                $gridblock_ajax_class = '';
                if ($type == 'ajax') {
                    $gridblock_ajax_class = "gridblock-ajax ";
                }
                
                // Generate main DIV tag with portfolio information with filterable tags
                $portfoliogrid .= '<div class="gridblock-element '.$animation_class.' gridblock-element-id-' . get_the_ID() . ' gridblock-element-order-' . $portfolio_total_count . ' ' . $add_space_class . $boxtitle_class . $title_desc_class . ' gridblock-filterable ';
                if (is_array($portfolio_cats)) {
                    foreach ($portfolio_cats as $taxonomy) {
                        $portfoliogrid .= 'filter-' . $taxonomy->slug . ' ';
                    }
                }
                $idCount++;
                $portfoliogrid .= '" data-portfolio="portfolio-' . get_the_ID() . '" data-id="id-' . $idCount . '">';
                $portfoliogrid .= '<div class="' . $gridblock_ajax_class . 'gridblock-grid-element gridblock-element-inner" data-portfolioid="' . get_the_id() . '">';
                
                $portfoliogrid .= '<div class="gridblock-background-hover">';
                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                $portfoliogrid .= '<div class="gridblock-links-wrap">';
                
                
                if (post_password_required()) {
                    $portfoliogrid .= '<span class="column-gridblock-icon">';
                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('locked') . '"></i>';
                    //$portfoliogrid .= '<span class="gridblock-image-hover">';
                    if (isSet($icon_class)) {
                        $portfoliogrid .= '<span class="hover-icon-effect">' . $icon_class . '</span>';
                    }
                    $portfoliogrid .= '</span>';
                } else {
                    
                    $portfoliogrid .= '<span class="column-gridblock-icon">';
                    $icon_class = '<i class="ion-ios-albums-outline"></i>';
                    //$portfoliogrid .= '<span class="gridblock-image-hover">';
                    if (isSet($icon_class)) {
                        $portfoliogrid .= '<span class="hover-icon-effect">' . $icon_class . '</span>';
                    }
                    $portfoliogrid .= '</span>';
                }
                
                $portfoliogrid .= '</div>';
                $portfoliogrid .= '</a>';
                
                if ($boxtitle == "true") {
                    
                    $current_terms    = wp_get_object_terms(get_the_ID(), 'worktypes');
                    $current_worktype = '';
                    $seperator        = ',';
                    foreach ($current_terms as $the_term) {
                        if ($the_term === end($current_terms)) {
                            $seperator = '';
                        }
                        $current_worktype .= $the_term->name . $seperator;
                    }
                    
                    $portfoliogrid .= '<span class="boxtitle-hover">';
                    $portfoliogrid .= '<a href="' . get_permalink() . '">';
                    $portfoliogrid .= get_the_title();
                    $portfoliogrid .= '</a>';
                    $portfoliogrid .= '<span class="boxtitle-worktype">' . $current_worktype . '</span>';
                    $portfoliogrid .= '</span>';
                }
                $portfoliogrid .= '</div>';
                
                if ($thumbnail <> "") {
                    $portfoliogrid .= '<img src="' . $thumbnail . '" class="displayed-image" alt="thumbnail" />';
                } else {
                    // Slideshow then generate slideshow shortcode
                    $portfoliogrid .= imaginem_codepack_display_post_image(
                        get_the_ID(), 
                        $have_image_url = "",
                        $link = false,
                        $imagetype = $portfolioImage_type,
                        $imagetitle = imaginem_codepack_image_title(get_the_ID()),
                        $class = "displayed-image",
                        $lazyload = true
                    );
                    
                }
                $portfoliogrid .= '</div>';
                if ($title == 'true' || $desc == 'true') {
                    $portfoliogrid .= '<div class="work-details">';
                    $hreflink = get_permalink();
                    if ($title == 'true') {
                        if ($type != "ajax") {
                            $portfoliogrid .= '<h4><a href="' . $hreflink . '">' . get_the_title() . '</a></h4>';
                        } else {
                            $portfoliogrid .= '<h4>';
                            $portfoliogrid .= '' . get_the_title() . '';
                            $portfoliogrid .= '</h4>';
                        }
                    }
                    if ($desc == 'true') {
                        $portfoliogrid .= '<p class="entry-content work-description">' . $description . '</p>';
                    }
                    $portfoliogrid .= '</div>';
                }
                
                
                $portfoliogrid .= '</div>';
            //if ($portfolio_count==$columns)  $portfoliogrid .='</div>';
            endwhile;
        endif;
        // if ($format=="masonary") {
        // 	$portfoliogrid .= '</div>';
        // }
        $portfoliogrid .= '</div>';
        $portfoliogrid .= '</div>';
        
        if ($pagination == 'true') {
            $portfoliogrid .= '<div class="clearfix">';
            $portfoliogrid .= imaginem_codepack_pagination();
            $portfoliogrid .= '</div>';
        }
        
        wp_reset_query();
        return $portfoliogrid;
    }
}
add_shortcode("gallerygrid", "mmtheme_GalleryGrids");
/**
 * Portfolio Grid
 */
if (!function_exists('mGridCreate')) {
    function mGridCreate($atts, $content = null)
    {
        extract(shortcode_atts(array(
            "pageid" => '',
            "grid_post_type" => '',
            "grid_tax_type" => '',
            "style" => 'classic',
            "format" => '',
            "allitem_text" => 'All items',
            "viewmore" => '',
            "effect" => 'default',
            "columns" => '4',
            "limit" => '-1',
            "like" => 'no',
            "filter_seperator" => '',
            "excerpt_length" => '25',
            "readmore_text" => 'Continue Reading',
            "animated_hovercard" => 'enable',
            "showdate" => "true",
            "showcomments" => "true",
            "filter_subcats" => "false",
            "category_display" => "true",
            "gutter" => 'spaced',
            "boxtitle" => 'false',
            "boxthumbnail_link" => 'false',
            "title" => 'true',
            "desc" => 'true',
            "worktype_slugs" => '',
            "pagination" => 'false',
            "animated" => 'true',
            "type" => 'no-filter'
        ), $atts));

        $galleryID = uniqid();
        $post_type_class ="";

        switch ($grid_post_type) {
            case 'events':
                $post_type_class = "events";
                break;

            case 'blog':
                $post_type_class = "blog";
                break;

            case 'photostory':
                $readmore_text = imaginem_codepack_get_option_data('photostory_archive_readmore');
                break;
            
            default:
                break;
        }
        // Set default effect
        if ($effect=="default") {
            $effect = "none";
        }

        $category_slug_name = $grid_tax_type;

        // empty hide filter
        $hide_filter  = '';
        
        // setup effect class
        $effect_class = ' has-effect-'.$effect;
        $relayout_on_image_class = '';
        
        // empty classes for filter and portfolio
        $portfoliogrid               = '';
        $subcat_filter_portfoliogrid = '';

        $only_title_class = '';

        // Lightbox or direct link for wall
        switch ($style) {
            case 'wall-spaced':
                $boxtitle = "box-directlink";
                if ($boxthumbnail_link=="lightbox") {
                    $boxtitle = "box-lightbox";
                }
                $gutter = "spaced";
                if ($title == 'true' && $desc == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;
            
            case 'wall-grid':
                $boxtitle = "box-directlink";
                if ($boxthumbnail_link=="lightbox") {
                    $boxtitle = "box-lightbox";
                }
                $gutter = "nospace";
                if ($title == 'true' && $desc == 'false') {
                    $only_title_class = "box-has-only-title";
                }
                break;

            case 'card-spaced':
                $boxtitle = false;
                $gutter = 'spaced';
                $format = 'portrait';
                $effect = 'default';
                break;

            case 'card-grid':
                $boxtitle = false;
                $gutter = 'nospace';
                $format = 'portrait';
                $effect = 'default';
                break;
            
            default:
                break;
        }

        // set default style
        if (!isSet($style)) {
            $style = "classic";
        }
        
        
        $animation_class = '';
        if ($style == "wall-spaced" || $style == "wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }
        if ($animated == "true" && $style<>"wall-spaced" && $style<>"wall-grid" ) {
            $animation_class = ' grid-animate-display-all';
        }

        // Set a default
        $column_type         = "four";
        $portfolioImage_type = "superlens-gridblock-large";
        $animated_status = '';
        if ( 'disable' == $animated_hovercard ) {
            $animated_status = '-disabled';
        }
        
        $gridblock_is_masonary = imaginem_codepack_get_grid_masonry_class($format);
        $column_type = imaginem_codepack_get_grid_column_type($columns);
        $portfolioImage_type = imaginem_codepack_get_grid_image_size($format,$columns);
        $relayout_on_image_class = imaginem_codepack_grid_relayout_class($format,$columns);

        if ($format == "masonary") {
            $animation_class = ' grid-animate-display-all';
        }
        
        if ($format == "portrait") {
            $protected_placeholder = '/images/blank-grid-portrait.png';
        } else {
            $protected_placeholder = '/images/blank-grid.png';
        }
        //$preload_tag = '<div class="preloading-placeholder"><span class="preload-image-animation"></span><img src="'.get_template_directory_uri().$protected_placeholder.'" alt="preloading" /></div>';
        $thumbnail_gutter_class = 'portfolio-gutter-' . $gutter . ' ';
        if ($gutter == "nospace") {
            $thumbnail_gutter_class .= 'thumnails-gutter-active ';
        }
        if ($title <> "true" && $desc <> "true") {
            $thumbnail_gutter_class .= 'no-title-no-desc ';
        }

        $boxtitle_class = '';
        if ($boxtitle == "box-lightbox" || $boxtitle == "box-directlink") {
            $boxtitle_class = " boxtitle-active";
        }
        $flag_new_row = true;
        
        $portfoliogrid .= '<div class="portfolio-grid-container portfolio-gridblock-columns-wrap grid-has-'.$post_type_class.'">';
        $portfoliogrid .= '<div id="gridblock-container" class="' . $thumbnail_gutter_class . $gridblock_is_masonary . ' ' . $only_title_class . ' ' . $relayout_on_image_class . $boxtitle_class . $effect_class.' grid-style-'.$style.' gridblock-' . $column_type . ' clearfix" data-columns="' . $columns . '">';
        if (get_query_var('paged')) {
            $paged = get_query_var('paged');
        } elseif (get_query_var('page')) {
            $paged = get_query_var('page');
        } else {
            $paged = 1;
        }
        
        $count           = 0;
        $terms           = array();
        $work_slug_array = array();
        //echo $worktype_slugs;
       if ($worktype_slugs != "") {
            $type_explode = explode(",", $worktype_slugs);
            foreach ($type_explode as $work_slug) {
                $terms[] = $work_slug;
            }
            if ($grid_post_type == "events") {
                query_posts(array(
                    'post_type' => $grid_post_type,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'paged' => $paged,
                    'posts_per_page' => $limit,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'pagemeta_event_notice',
                            'value' => 'inactive',
                            'compare' => 'NOT IN'
                        )
                    ),
                    'tax_query' => array(
                        array(
                            'taxonomy' => $category_slug_name,
                            'field' => 'slug',
                            'terms' => $terms,
                            'operator' => 'IN'
                        )
                    )
                ));
            } elseif ($grid_post_type == "blog") {
                query_posts(array(
                    'category_name' => $worktype_slugs,
                    'paged' => $paged,
                    'posts_per_page' => $limit
                ));
            } else {
                query_posts(array(
                    'post_type' => $grid_post_type,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'paged' => $paged,
                    'posts_per_page' => $limit,
                    'tax_query' => array(
                        array(
                            'taxonomy' => $grid_tax_type,
                            'field' => 'slug',
                            'terms' => $terms,
                            'operator' => 'IN'
                        )
                    )
                ));
            }
        } else {
            
            if ($grid_post_type == "events") {
                query_posts(array(
                    'post_type' => $grid_post_type,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'paged' => $paged,
                    'posts_per_page' => $limit,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'pagemeta_event_notice',
                            'value' => 'inactive',
                            'compare' => 'NOT IN'
                        )
                    )
                ));
            } elseif ($grid_post_type == "blog") {
                query_posts(array(
                    'paged' => $paged,
                    'posts_per_page' => $limit
                ));
            } else {
                query_posts(array(
                    'post_type' => $grid_post_type,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'paged' => $paged,
                    'posts_per_page' => $limit
                ));
            }
        }
        
        $idCount               = 1;
        $portfolio_count       = 0;
        $portfolio_total_count = 0;
        $portfoliofilters      = array();
        
        if (have_posts()):
            while (have_posts()):
                the_post();
                //echo $type, $portfolio_type;
                $custom                 = get_post_custom(get_the_ID());
                $portfolio_cats         = get_the_terms(get_the_ID(), $category_slug_name );
                $lightboxvideo          = '';
                $thumbnail              = '';
                $customlink_URL         = '';
                $description            = '';
                $portfolio_link_type    = 'DirectURL';
                $portfolio_thumb_header = 'Image';
                $the_only_link          = false;
                $the_protected_link     = false;
                
                if ( isset( $custom['pagemeta_thumbnail_linktype'][0] ) ) {
                    $portfolio_link_type = $custom['pagemeta_thumbnail_linktype'][0];
                }
                if ( isset( $custom['pagemeta_lightbox_video'][0] ) ) {
                    $lightboxvideo = $custom['pagemeta_lightbox_video'][0];
                }
                if ( isset( $custom['pagemeta_customthumbnail'][0] ) ) {
                    $thumbnail = $custom['pagemeta_customthumbnail'][0];
                }
                if ( $grid_post_type == "blog" ) {
                    $description = imaginem_codepack_excerpt_limit($excerpt_length);
                } else {
                    if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                        $description = $custom['pagemeta_thumbnail_desc'][0];
                    }
                }

                if (isset($custom['pagemeta_customlink'][0])) {
                    $customlink_URL = $custom['pagemeta_customlink'][0];
                }
                if (isset($custom['pagemeta_portfoliotype'][0])) {
                    $portfolio_thumb_header = $custom['pagemeta_portfoliotype'][0];
                }
                
                // if boxed title then link directly.
                if ($boxtitle == "box-directlink") {
                    $portfolio_link_type = "DirectURL";
                }
                if ($boxtitle == "box-lightbox") {
                    $portfolio_link_type = "Lightbox";
                }
                if ($boxtitle != "box-lightbox" && $boxtitle != "box-directlink") {
                    $boxtitle = false;
                }
                
                if ($portfolio_count == $columns)
                    $portfolio_count = 0;
                
                $add_space_class = '';
                if ($gutter != 'nospace') {
                    if ($title == 'false' && $desc == 'false') {
                        $add_space_class = 'gridblock-cell-bottom-space';
                    }
                }
                
                $protected  = "";
                $icon_class = "column-gridblock-icon";
                $portfolio_count++;
                $portfolio_total_count++;
                
                $gridblock_ajax_class = '';
                if ($type == 'ajax') {
                    $gridblock_ajax_class = "gridblock-ajax ";
                }

                $featured_image_id = get_post_thumbnail_id( get_the_id() );
                $featured_image_data = get_post($featured_image_id);
                $featuredimg_purchase_url = '';
                $featuredimg_title = '';
                $featuredimg_desc = '';
                $purchase_link = false;

                if (isSet($featured_image_data->post_title)) {
                    $featuredimg_title = $featured_image_data->post_title;
                }
                if (isSet($featured_image_data->post_content)) {
                    $featuredimg_desc = $featured_image_data->post_content;
                }

                $featuredimg_purchase_url   = get_post_meta( $featured_image_id, 'mtheme_attachment_purchase_url', true);

                $featured_image_url = '';
                $height = '';
                if ( has_post_thumbnail() ) {
                    $featured_image_url = imaginem_codepack_featured_image_link( get_the_id(), $portfolioImage_type );
                }
                
                // Generate main DIV tag with portfolio information with filterable tags
                $portfoliogrid .= '<div class="gridblock-element' . $animation_class . ' isotope-displayed gridblock-element-id-' . get_the_ID() . ' gridblock-element-order-' . $portfolio_total_count . ' ' . $add_space_class . ' gridblock-filterable ';
                if (is_array($portfolio_cats)) {
                    foreach ($portfolio_cats as $taxonomy) {
                        $portfoliogrid .= 'filter-' . $taxonomy->slug . ' ';
                        if ($pagination == 'true') {
                            if (in_array($taxonomy->slug, $portfoliofilters)) {
                            } else {
                                $portfoliofilters[] = $taxonomy->slug;
                            }
                        }
                    }
                }
                $idCount++;
                $portfoliogrid .= '" data-portfolio="portfolio-' . get_the_ID() . '" data-id="id-' . $idCount . '">';

                if ( $featuredimg_purchase_url ) {
                    $purchase_link = $featuredimg_purchase_url;
                } else {
                    $purchase_link = false;
                }
                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $hreflink = get_permalink();
                    $portfoliogrid .= '<a class="gridblock-element-card-link" href="' . esc_url( $hreflink ) . '">';
                    $portfoliogrid .= '<div class="gridblock-element-card" style="'.$height.'background-image:url('. esc_url( $featured_image_url ) .');">';
                    $portfoliogrid .= '<div class="gridblock-background-card">';
                }
                
                $portfoliogrid .= '<div class="' . $gridblock_ajax_class . 'gridblock-grid-element gridblock-element-inner" data-portfolioid="' . get_the_id() . '">';
                
                $portfoliogrid .= '<div class="gridblock-background-hover">';

                if ($like == "yes") {
                    $portfoliogrid .= '<div class="mtheme-post-like-wrap">';
                    $portfoliogrid .= superlens_display_like_link(get_the_id());
                    $portfoliogrid .= '</div>';
                }
                
                if (post_password_required()) {
                    $the_only_link       = true;
                    $the_protected_link  = true;
                    $portfolio_link_type = "DirectURL";
                    $protected           = " gridblock-protected";
                    $iconclass           = "";
                }
                
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                    }
                    
                    if ( $portfolio_link_type <> "" ) {
                        
                        if ($portfolio_link_type == "Lightbox_DirectURL") {
                            $portfoliogrid .= '<a class="column-gridblock-icon" href="' . get_permalink() . '">';
                            $portfoliogrid .= '<span class="hover-icon-effect"><i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i></span>';
                            $portfoliogrid .= '</a>';
                        }
                        
                        
                        switch ($portfolio_link_type) {
                            case 'DirectURL':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                if (post_password_required()) {
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('locked') . '"></i>';
                                } else {
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                }
                                break;
                            
                            case 'Customlink':
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . $customlink_URL . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('link') . '"></i>';
                                break;
                            
                            case 'Lightbox_DirectURL':
                                if ($lightboxvideo <> "") {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = $featuredimg_title,
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = $featuredimg_title,
                                        $class = "column-gridblock-icon column-gridblock-lightbox lightbox-image", $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            case 'Lightbox':
                                $the_only_link = true;
                                if ($lightboxvideo <> "") {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default",
                                        $ID = get_the_id(),
                                        $predefined = $lightboxvideo,
                                        $mediatype = "video",
                                        $imagetitle = $featuredimg_title,
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-video",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('play') . '"></i>';
                                } else {
                                    $portfoliogrid .= imaginem_codepack_activate_lightbox(
                                        $lightbox_type = "default", $ID = get_the_id(),
                                        $predefined = imaginem_codepack_featured_image_link(get_the_ID()),
                                        $mediatype = "image",
                                        $imagetitle = $featuredimg_title,
                                        $class = "gridblock-sole-link column-gridblock-lightbox lightbox-image",
                                        $set = "portfolio-grid",
                                        $data_name = "default",
                                        $external_thumbnail_id = get_post_thumbnail_id( get_the_id() ),
                                        $imageDataID=$featured_image_id,
                                        $galleryID
                                        );
                                    $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('lightbox') . '"></i>';
                                }
                                break;
                            default:
                                $the_only_link = true;
                                $portfoliogrid .= '<a class="gridblock-sole-link" href="' . get_permalink() . '">';
                                $icon_class = '<i class="' . imaginem_codepack_get_portfolio_icon('directlink') . '"></i>';
                                break;
                        }
                        
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '<div class="gridblock-links-wrap box-title-' . $boxtitle . '">';
                        }
                        // if it is not boxed title
                        if (!$boxtitle) {
                            if (isSet($icon_class)) {
                                if ($the_only_link) {
                                    $portfoliogrid .= '<span class="column-gridblock-icon">';
                                }
                                $portfoliogrid .= '<span class="hover-icon-effect">' . $icon_class . '</span>';
                                if ($the_only_link) {
                                    $portfoliogrid .= '</span>';
                                }
                            }
                        }
                        if ($portfolio_link_type != "Lightbox_DirectURL") {
                            $portfoliogrid .= '</div>';
                        }
                        if ($boxtitle) {
                            $portfoliogrid .= '<div class="boxtitle-hover">';
                                if ($title == 'true' || $desc == 'true') {
                                    $portfoliogrid .= '<div class="work-details">';
                                    $hreflink = get_permalink();
                                    if ( $category_display == 'yes' ) {
                                        $current_terms    = wp_get_object_terms(get_the_ID(), $category_slug_name);
                                        $current_worktype = '';
                                        $seperator        = ' , ';
                                        foreach ($current_terms as $the_term) {
                                            if ($the_term === end($current_terms)) {
                                                $seperator = '';
                                            }
                                            $current_worktype .= $the_term->name . $seperator;
                                        }
                                        $portfoliogrid .= '<div class="worktype-categories">' . $current_worktype . '</div>';
                                    }
                                    if ($title == 'true') {
                                        $portfoliogrid .= '<h4>' . get_the_title() . '</h4>';
                                    }
                                    if ($desc == 'true') {
                                        $portfoliogrid .= '<p class="entry-content work-description">' . $description . '</p>';
                                    }

                                    $portfoliogrid .= '</div>';
                                }
                            $portfoliogrid .= '</div>';
                        }
                        $portfoliogrid .= '</a>';
                    }
                    if ($portfolio_link_type == "Lightbox_DirectURL") {
                        $portfoliogrid .= '</div>';
                    }
                }

                if ($category_display == 'yes') {
                    $current_terms    = wp_get_object_terms(get_the_ID(), $category_slug_name );
                    $current_worktype = '';
                    $seperator        = ' , ';
                    foreach ($current_terms as $the_term) {
                        if ($the_term === end($current_terms)) {
                            $seperator = '';
                        }
                        $current_worktype .= $the_term->name . $seperator;
                    }
                }
                
                $portfoliogrid .= '</div>';
                
                $fade_in_class = "";
                if ( 'card-spaced' !== $style && 'card-grid' !== $style ) {
                    if ( $thumbnail <> '' ) {
                        $portfoliogrid .= '<img src="' . $thumbnail . '" class="' . $fade_in_class . 'displayed-image" alt="thumbnail" />';
                    } else {
                        // Slideshow then generate slideshow shortcode
                        $portfoliogrid .= imaginem_codepack_display_post_image(get_the_ID(), $have_image_url = "", $link = false, $imagetype = $portfolioImage_type, $imagetitle = imaginem_codepack_image_title(get_the_ID()), $class = $fade_in_class . "displayed-image", $lazyload=true);
                        
                    }
                }
                $portfoliogrid .= '</div>';

                if ( $grid_post_type=="blog" && $style=="classic" ) {
                    $portfoliogrid .= '<div class="summary-info">';
                        if ($showdate=='true') {
                            $portfoliogrid .='<div class="summary-date"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
                        }
                        $category = get_the_category();
                        if ($showcomments == 'true' ) {
                            $portfoliogrid .= '<div class="summary-comment">';

                            $num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
                            if ( comments_open() ) {
                                if ( $num_comments == 0 ) {
                                    $comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
                                } elseif ( $num_comments > 1 ) {
                                    $comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
                                } else {
                                    $comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
                                }
                                $portfoliogrid .= '<a href="' . get_comments_link( get_the_id() ) .'">'. $comments_desc.'</a>';
                            }
                            $portfoliogrid .='</div>';
                        }
                    $portfoliogrid .='</div>';
                }

                if ( $style == "card-spaced" || $style == "card-grid" ) {
                    if ( $title == 'true' || $desc == 'true' || $category_display == 'yes' ) {
                        $portfoliogrid .= '<div class="infobox-card-blog-summary-wrap">';
                        $portfoliogrid .= '<div class="card-infobox-date infobox-card-blog-summary"><i class="feather-icon-clock"></i> '.get_the_date().'</div>';
                        if ($showcomments == 'true' ) {
                            $portfoliogrid .= '<div class="card-infobox-comment infobox-card-blog-summary">';

                            $num_comments = get_comments_number( get_the_id() ); // get_comments_number returns only a numeric value
                            if ( comments_open() ) {
                                if ( $num_comments == 0 ) {
                                    $comments_desc = __('0 <i class="feather-icon-speech-bubble"></i>');
                                } elseif ( $num_comments > 1 ) {
                                    $comments_desc = $num_comments . __(' <i class="feather-icon-speech-bubble"></i>');
                                } else {
                                    $comments_desc = __('1 <i class="feather-icon-speech-bubble"></i>');
                                }
                                $portfoliogrid .= '<span class="infobox-comment">'. $comments_desc.'</span>';
                            }
                            $portfoliogrid .='</div>';
                        }
                        $portfoliogrid .='</div>';
                        $portfoliogrid .= '<div class="work-details text-is-bright">';
                        $hreflink = get_permalink();
                        if ( $category_display == 'yes' ) {
                            $current_terms = wp_get_object_terms( get_the_ID(), 'category'  );
                            $current_worktype = '';
                            $seperator = ',';
                            foreach( $current_terms as $the_term ) {
                                if ($the_term === end($current_terms)) {
                                    $seperator = '';
                                }
                                $current_worktype .= $the_term->name . $seperator;
                            }
                            $portfoliogrid .= '<div class="infobox-animate' . $animated_status . ' worktype-categories">' . $current_worktype . '</div>';
                        }
                        if ($title == 'true') {
                            $portfoliogrid .= '<h4 class="infobox-animate' . $animated_status . '">' . get_the_title() . '</h4>';
                        }
                        $portfoliogrid .= '<div class="card-work-details">';
                        if ($desc == 'true') {
                            $portfoliogrid .= '<p class="entry-content infobox-animate' . $animated_status . ' work-description">' . $description . '</p>';
                        }

                        if ($viewmore!='') {
                            $portfoliogrid .= '<div class="infobox-animate' . $animated_status . '">';
                            $portfoliogrid .= imaginem_codepack_show_continue_reading( $viewmore , false);
                            $portfoliogrid .= '</div>';
                        }

                        $portfoliogrid .= '</div>';
                        $portfoliogrid .= '</div>';
                    }
                }

                if ($style == "classic" ) {
                    if ($title == 'true' || $desc == 'true') {
                        $portfoliogrid .= '<div class="work-details">';
                        $hreflink = get_permalink();
                        if ($grid_post_type == "events") {
                            $event_start_datetime = '';
                            $event_end_datetime   = '';
                            $event_startdate      = '';
                            $event_enddate        = '';

                            if (isset($custom['pagemeta_event_startdate'][0])) {
                                $event_startdate = $custom['pagemeta_event_startdate'][0];
                            }

                            if (isset($custom['pagemeta_event_enddate'][0])) {
                                $event_enddate = $custom['pagemeta_event_enddate'][0];
                            }

                            $event_start_datetime = explode(" ", $event_startdate);
                            $event_end_datetime   = explode(" ", $event_enddate);
                            $start_date           = date_i18n(get_option('date_format'), strtotime($event_start_datetime[0]));
                            $end_date             = date_i18n(get_option('date_format'), strtotime($event_end_datetime[0]));
                                $portfoliogrid .= '<div class="events-summary-wrap">';
                            $portfoliogrid .= '<div class="worktype-categories">' . $start_date . '<span class="date-seperator"> - </span>' . $end_date . '</div>';
                                $portfoliogrid .= '</div>';
                        }
                        if ( $style=="classic" && $category_display == 'yes' && $current_worktype<>"" ) {
                            $portfoliogrid .= '<div class="worktype-categories">' . $current_worktype . '</div>';
                        }
                        if ($title == 'true') {
                            if ($type != "ajax") {
                                $portfoliogrid .= '<h4><a href="' . $hreflink . '">' . get_the_title() . '</a></h4>';
                            } else {
                                $portfoliogrid .= '<h4>';
                                $portfoliogrid .= get_the_title();
                                $portfoliogrid .= '</h4>';
                            }
                        }
                        if ($desc == 'true') {
                            $portfoliogrid .= '<p class="entry-content work-description">' . $description . '</p>';
                        }
                        
                        if ( 'blog' === $grid_post_type ) {
                            $readmore_text = imaginem_codepack_get_option_data('read_more');
                        }

                        if ($readmore_text!='') {
                            $portfoliogrid .= imaginem_codepack_show_continue_reading( $readmore_text , $hreflink);
                        }

                        $portfoliogrid .= '</div>';
                    }
                }
                
                if ( 'card-spaced' === $style || 'card-grid' === $style ) {
                    $portfoliogrid .= '</div>';
                    $portfoliogrid .= '</div>';
                    $portfoliogrid .= '</a>';
                }
                $portfoliogrid .= '</div>';
            //if ($portfolio_count==$columns)  $portfoliogrid .='</div>';
            endwhile;
        endif;
        // if ($format=="masonary") {
        //  $portfoliogrid .= '</div>';
        // }
        $portfoliogrid .= '</div>';
        
        if ($pagination == 'true') {
            $portfolio_pagination = imaginem_codepack_pagination();
            if ($portfolio_pagination<>"") {
                $portfoliogrid .= '<div class="clearfix">';
                $portfoliogrid .= $portfolio_pagination;
                $portfoliogrid .= '</div>';
            }
        }
        $portfoliogrid .= '</div>';
        
        wp_reset_query();
        
        if ($type == "filter" || $type == "ajax") {
            
            $filter_portfoliogrid = "";
            
            $filter_portfoliogrid .= '<div class="gridblock-filter-select-wrap">';
            
            $filter_portfoliogrid .= '<div id="gridblock-filters">';
            $filter_portfoliogrid .= '<ul class="gridblock-filter-categories">';
            $filter_portfoliogrid .= '<li class="filter-icon"><i class="far fa-compass"></i></li>';
            $filter_portfoliogrid .= '<li class="filter-all-control">';
            $filter_portfoliogrid .= '<a data-filter="*" data-title="' . $allitem_text . '" href="#">';
            $filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-all"></span>' . $allitem_text;
            $filter_portfoliogrid .= '</a>';
            $filter_portfoliogrid .= '</li>';
            
            //$categories=  get_categories('child_of='.$portfolio_cat_ID.'&orderby=slug&taxonomy=worktypes&title_li=');
            if ($worktype_slugs != '')
                $all_works = explode(",", $worktype_slugs);
            if ($filter_subcats == "true") {
                $categories = get_categories('orderby=slug&parent=0&taxonomy='.$category_slug_name.'&title_li=');
            } else {
                $categories = get_categories('orderby=slug&taxonomy='.$category_slug_name.'&title_li=');
            }

            $taxonomy = 'worktypes';
            if ($grid_post_type == "events") {
                $taxonomy = $category_slug_name;
            }

            foreach ($categories as $category) {
                
                // Using Term Slug
                $term_slug = $category->slug;
                $term      = get_term_by('slug', $term_slug, $taxonomy);
                
                // Enter only if Works is not set - means all included OR if work types are defined in shortcode
                if (!isSet($all_works) || in_array($term_slug, $all_works)) {
                    // Fetch the count
                    //echo $term->count;
                    if ($pagination == 'true') {
                        if (is_array($portfoliofilters)) {
                            $filter_found = false;
                            $hide_filter  = '';
                            if (in_array($category->slug, $portfoliofilters)) {
                                $filter_found = true;
                            }
                            
                        }
                        if (!$filter_found) {
                            $hide_filter = 'style="display:none;"';
                            //echo $category->slug;
                        }
                    }

                    $filter_portfoliogrid .= '<li ' . $hide_filter . ' class="filter-control filter-category-control filter-' . $category->slug . ' filter-control-' . $category->slug . '">';
                    $filter_portfoliogrid .= '<a data-filter=".filter-' . $category->slug . '" data-title="' . $category->name . '" href="#">';
                    $filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-main">' . $filter_seperator . '</span>' . $category->name;
                    $filter_portfoliogrid .= '</a>';
                    $filter_portfoliogrid .= '</li>';
                    
                    // Populate Subcategories
                    if ($filter_subcats == "true") {
                        $portfolio_subcategories = get_categories('orderby=slug&taxonomy='.$category_slug_name.'&child_of=' . $category->term_id . '&title_li=');
                        //print_r($portfolio_subcategories);
                        
                        foreach ($portfolio_subcategories as $portfolio_subcategory) {
                            //print_r($portfolio_subcategory->slug);
                            $sub_filter_seperator = '';
                            $subcat_filter_portfoliogrid .= '<li class="filter-' . $category->slug . '-of-parent filter-subcat-control filter-control filter-' . $portfolio_subcategory->slug . ' filter-control-' . $portfolio_subcategory->slug . '">';
                            $subcat_filter_portfoliogrid .= '<a data-filter=".filter-' . $portfolio_subcategory->slug . '" data-title="' . $portfolio_subcategory->name . '" href="#">';
                            $subcat_filter_portfoliogrid .= '<span class="filter-seperator filter-seperator-sub">' . $sub_filter_seperator . '</span>' . $portfolio_subcategory->name;
                            $subcat_filter_portfoliogrid .= '</a>';
                            $subcat_filter_portfoliogrid .= '</li>';
                        }
                    }
                }
            }
            
            $filter_portfoliogrid .= '</ul>';
            
            if ($subcat_filter_portfoliogrid <> '' && $filter_subcats == "true") {
                $subcat_filter_portfoliogrid = '<ul class="griblock-filters-subcats">' . $subcat_filter_portfoliogrid . '</ul>';
            }
            $filter_portfoliogrid .= $subcat_filter_portfoliogrid;
            $filter_portfoliogrid .= '</div>';
            $filter_portfoliogrid .= '</div>';
            //End of If Filter
        }
        
        if (isSet($filter_portfoliogrid)) {
            $portfoliogrid = '<div class="isotope-grid-outer-wrap">' . $filter_portfoliogrid . $portfoliogrid . '</div>';
        }
        //Reset query after Filters
        
        wp_reset_query();
        return $portfoliogrid;
    }
}
add_shortcode("gridcreate", "mGridCreate");
/**
 * AJAX Flexi Slideshow .
 *
 * @ [flexislideshow link=(lightbox,direct,none)]
 */
function mtheme_Verticalimages($atts, $content = null)
{
    extract(shortcode_atts(array(
        "pageid" => '',
        "pb_image_ids" => '',
        'image_size' => 'fullsize',
        "dpage" => '',
        "height" => '',
        "linktype" => 'lightbox',
        "title" => 'false',
        "description" => 'false',
        "animation" => 'true',
        "width" => ''
    ), $atts));
    
    $galleryID = uniqid();
    if (trim($pb_image_ids) <> '') {
        $filter_image_ids = explode(',', $pb_image_ids);
    } else {
        $filter_image_ids = imaginem_codepack_get_custom_attachments($pageid);
    }
    
    $galleryID = uniqid();
    $uniqurePageID  = dechex(mt_rand(1, 65535));
    $found_an_image = false;
    if ($filter_image_ids) {
        $output = '
			<div class="lightgallery-container clearfix">
			<ul class="vertical_images">';
        foreach ($filter_image_ids as $attachment_id) {

            $check_if_image_present = wp_get_attachment_image_src($attachment_id, $image_size , false);
            if ( !$check_if_image_present ) {
                continue;
            }

            $imagearray = wp_get_attachment_image_src($attachment_id, $image_size, false);
            $imageURI   = $imagearray[0];
            if (isSet($imageURI) && $imageURI <> "") {
                $found_an_image = true;
            }
            $imageID    = get_post($attachment_id);
            $imageTitle = "";
            if (isSet($imageID->post_title)) {
                $imageTitle = $imageID->post_title;
            }
            $imageCaption = "";
            if (isSet($imageID->post_content)) {
                $imageCaption = $imageID->post_content;
            }

            $type = 'fullsize';
            $class = '';

            //$image_id    = get_post_thumbnail_id( ( $attachment_id ), $type );
            $lightbox_image_url = wp_get_attachment_image_src( $attachment_id, 'full', false);
            $lightbox_image_url = $lightbox_image_url[0];
            $image_url          = wp_get_attachment_image_src( $attachment_id, $image_size, false);
            $image_url          = $image_url[0];
            $img_alt            = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
            $srcset             = wp_get_attachment_image_srcset( $attachment_id, $image_size );
            $srcsetsizes        = wp_get_attachment_image_sizes( $attachment_id, $image_size );
            $fallback_image     = imageinem_codepack_get_placeholder_image( $type );
            $data_src           = '';
            $fallback_src       = '';
            $data_srcset        = '';
            $srcsizes           = 'sizes="' . $srcsetsizes . '"';
            $data_src           = 'data-';
            $data_srcset        = 'data-srcset="' . $srcset . '" ';
            $fallback_src       = 'src="' . esc_url( $fallback_image ) . '" ';
            $class              = $class . 'lazyload-after lazyload';

            $output .= '<li class="vertical-image-list">';
            $output .= '<div class="vertical-image-list-inner">';
            
            
            // $output .= imaginem_codepack_activate_lightbox(
            //     $lightbox_type = "default",
            //     $ID = get_the_id(),
            //     $predefined = $fullimageURI,
            //     $mediatype = "image",
            //     $imagetitle = $imageTitle,
            //     $lightbox_class = "vertical-images-link lightbox-image",
            //     $set = "vertical-images",
            //     $data_name = "default",
            //     $external_thumbnail_id = $attachment_id,
            //     $imageDataID=$attachment_id
            // );
            $output .= '<div class="vertical-image-wrap">';
            if ( $linktype == 'lightbox' ) {
                $output .= imaginem_codepack_activate_lightbox(
                    $lightbox_type = "default",
                    $ID = $attachment_id,
                    $predefined = $lightbox_image_url,
                    $mediatype = "image",
                    $imagetitletag = $imageTitle,
                    $lightbox_class = "lightbox-image vertical-image-lightbox",
                    $set = "vertical-grid",
                    $data_name = "default",
                    $external_thumbnail_id = $attachment_id,
                    $imageDataID=$attachment_id,
                    $galleryID
                    );
            }
            $output .= '<span class="gridblock-element-inner vertical-image-loader">';
            $output .= '<img ' . $fallback_src . $data_src . 'src="' . esc_url( $image_url ) . '" ' . $data_srcset . $srcsizes . ' alt="' . esc_attr( $img_alt ) . '" class="' . esc_attr( $class ) . '"/>';
            $output .= '</span>';

            if ( $linktype == 'lightbox' ) {
                $output .= '</a>';
            }
            $output .= '</div>';

            if ($animation == "true") {
                $animation_classes = ' is-animated animated fadeIn';
            } else {
                $animation_classes = '';
            }
            
            if ($title == "true" || $description == "true") {
                $output .= '<div class="work-details">';
                if ($title == 'true') {
                    $output .= '<h4>';
                    $output .= $imageTitle;
                    $output .= '</h4>';
                }
                if ($description == 'true') {
                    $output .= '<p class="entry-content work-description">' . $imageCaption . '</p>';
                }
                $output .= '</div>';
            }
            
            $output .= '</div>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        if ($found_an_image) {
            return $output;
        } else {
            return false;
        }
    }
}
add_shortcode("vertical_images", "mtheme_Verticalimages");

add_shortcode("portfolio_details", "portfolio_details");
function portfolio_details($atts, $content = null)
{
    extract(shortcode_atts(array(
        "project_title" => '',
        "like" => 'no',
        "alignment" => '',
        "project_detail_title" => '',
        "project_client_title" => '',
        "project_client_target" => '',
        "project_link_target" => '',
        "project_client" => '',
        "project_client_link" => '',
        "project_skills" => '',
        "project_link_title" => '',
        "project_link" => ''
    ), $atts));
    
    $galleryID = uniqid();
    $content = wpautop(html_entity_decode($content));
    
    $project_skills = explode(',', $project_skills);
    shuffle($project_skills);
    $skills_html = '<ul>';
    foreach ($project_skills as $p_skill) {
        $skills_html .= '<li>' . $p_skill . '</li>';
    }
    $skills_html .= '</ul>';
    
    if ($alignment == "center") {
        $alignment_class = "center";
    }
    if ($alignment == "right") {
        $alignment_class = "right";
    }
    if ($alignment == "left") {
        $alignment_class = "left";
    }

    $project_target_tag = '';
    $client_target_tag = '';

    if ($project_link_target=="yes") {
        $project_target_tag = 'target="_blank"';
    }
    if ($project_client_target=="yes") {
        $client_target_tag = 'target="_blank"';
    }
    
    $output = '<div class="portfolio-details-section-inner portfolio-details-align-' . $alignment . ' entry-content portfolio-header-right-inner">';
    $output .= '<h2 class="project-heading">' . $project_title . '</h2>';
    $output .= '<div class="portfolio-content-summary entry-content medium">' . $content . '</div>';
    $output .= '<div class="portfolio-details-wrap">';
    $output .= '<div class="portfolio-details-inner">';
    $output .= '<div class="project-details project-info project-skills-column">';
    $output .= '<h4>' . imaginem_codepack_get_option_data('portfolio_skills') . '</h4>';
    $output .= $skills_html;
    $output .= '</div>';
    if ( $project_link_title<>"" ) {

        $project_link_start = '';
        $project_link_end = '';
        $project_link_icon = '<i class="feather-icon-minus"></i>';
        if ( $project_link<>"" ) {
            $project_link_icon = '<i class="feather-icon-link"></i>';
            $project_link_start = '<a '.$project_target_tag.' href="'.esc_url($project_link).'">';
            $project_link_end = '</a>';
        }

        $output .= '<div class="project-details-link clear">';
        $output .= $project_link_icon;
        $output .= '<h4>'.$project_link_start. $project_link_title . $project_link_end. '</h4>';
        $output .= '</div>';
    }
    if ( $project_client<>"" ) {

        $client_link_start = '';
        $client_link_end = '';
        $client_link_icon = '<i class="feather-icon-minus"></i>';
        if ( $project_client_link<>"" ) {
            $client_link_icon = '<i class="feather-icon-link"></i>';
            $client_link_start = '<a '.$client_target_tag.' class="client-link" href="' . $project_client_link . '">';
            $client_link_end = '</a>';
        }

        $output .= '<div class="project-details-link clear">';
        $output .= $client_link_icon;
            $output .= '<h4>'.$client_link_start.'<span>' . $project_client . '</span>'.$client_link_end.'</h4>';
        $output .= '</div>';
    }
    if ($like == "yes") {
        $output .= superlens_display_like_link(get_the_id());
    }
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
    
}

/**
 * Portfolio Slideshow .
 *
 * @ [flexislideshow link=(lightbox,direct,none)]
 */
function portfolioParallax($atts, $content = null)
{
    extract(shortcode_atts(array(
        "limit" => '-1',
        "worktype_slugs" => '',
        'height_type' => 'fixed',
        "height" => '300'
    ), $atts));
    
    if ($limit == '') {
        $limit = "-1";
    }
    
    $count = 0;
    $galleryID = uniqid();
    //echo $type, $portfolio_type;
    $countquery = array(
        'post_type' => 'portfolio',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'worktypes' => $worktype_slugs,
        'posts_per_page' => $limit
    );
    query_posts($countquery);
    
    $portfolioImage_type = "superlens-gridblock-full";
    $output              = '<div class="gridblock-parallax-wrap clearfix">';
    $output .= '<ul>';
    
    $height_class = '';
    if ($height == 0 || $height_type == "window") {
        $height       = '100%;';
        $height_class = ' fullheight-parallax';
    } else {
        $height = $height . 'px';
    }
    
    if (have_posts()):
        while (have_posts()):
            the_post();
            
            if (has_post_thumbnail()) {
                
                $image_id  = get_post_thumbnail_id(get_the_ID(), $portfolioImage_type);
                $image_url = wp_get_attachment_image_src($image_id, $portfolioImage_type);
                $image_url = $image_url[0];
                $img_obj   = get_post($image_id);
                $img_alt   = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                
                $lightbox_image = imaginem_codepack_featured_image_link(get_the_id());
                $lightbox_media = $lightbox_image;
                $custom         = get_post_custom(get_the_ID());
                
                $description    = "";
                $customlink_URL = "";
                
                if (isset($custom['pagemeta_thumbnail_desc'][0])) {
                    $description = $custom['pagemeta_thumbnail_desc'][0];
                }
                if (isset($custom['pagemeta_customlink'][0])) {
                    $customlink_URL = $custom['pagemeta_customlink'][0];
                }
                
                $count++;

                $jarallax_data_tag = "data-jarallax='{\"speed\": 0.3}'";
                $fadeinstyle ="fadeIn";

                $output .= '<li '.$jarallax_data_tag.' class="element-fadein-after-load portfolio-parallax-image'.$height_class.'" style="height:'.$height.';background-image:url('. esc_url( $image_url ) .');">';

                $categories = get_the_term_list(get_the_id(), 'worktypes', '', ' , ', '');
                $categories = strip_tags($categories);

                $output .= '<div class="slideshow-box-info work-details animation-standby-portfolio animated flipInXSlight">';
                    $portfolio_link = get_permalink();
                    if (isSet($customlink_URL) && $customlink_URL<>"") {
                        $portfolio_link = $customlink_URL;
                    }
            $output .= '<div class="heading-block">
                <h2 class="line-standby photocard-title">
                '.get_the_title().'
                </h2>
                <h3 class="photocard-subtitle">
                    '.$categories.'
                </h3>
            </div>
            <div class="photocard-contents animation-standby-portfolio animated fadeInUpSlow">
            '.$description.'
            </div>';
            $view_button_text = imaginem_codepack_get_option_data('ajax_portfolio_viewtext');

            $output .= '
                    <a href="'.esc_url($portfolio_link).'">
                    <div class="mtheme-button">
                    '.$view_button_text.'
                    </div></a>';
                $output .='</div>';
                
                $output .= '</li>';
            }
        endwhile;
    endif;
    $output .= '</ul>';
    $output .= '</div>';
    
    wp_reset_query();
    return $output;
    
    
}
add_shortcode("recent_portfolio_parallax", "portfolioParallax");
?>