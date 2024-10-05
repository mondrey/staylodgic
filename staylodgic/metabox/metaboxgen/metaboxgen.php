<?php
function staylodgic_generate_sidebarlist($sidebarlist_type)
{
    $max_sidebars = 50;
    if ($sidebarlist_type == "events") {
        $sidebar_options                    = array();
        $sidebar_options['events_sidebar']  = 'Default Events Sidebar';
        $sidebar_options['default_sidebar'] = 'Default Sidebar';
        for ($sidebar_count = 1; $sidebar_count <= $max_sidebars; $sidebar_count++) {

            if (staylodgic_get_option_data('mthemesidebar-' . $sidebar_count) != "") {
                $active_sidebar                                     = staylodgic_get_option_data('mthemesidebar-' . $sidebar_count);
                $sidebar_options['mthemesidebar-' . $sidebar_count] = $active_sidebar;
            }
        }
    }
    if ($sidebarlist_type == "proofing") {
        $sidebar_options                     = array();
        $sidebar_options['proofing_sidebar'] = 'Default Proofing Sidebar';
        $sidebar_options['default_sidebar']  = 'Default Sidebar';
        for ($sidebar_count = 1; $sidebar_count <= $max_sidebars; $sidebar_count++) {

            if (staylodgic_get_option_data('mthemesidebar-' . $sidebar_count) != "") {
                $active_sidebar                                     = staylodgic_get_option_data('mthemesidebar-' . $sidebar_count);
                $sidebar_options['mthemesidebar-' . $sidebar_count] = $active_sidebar;
            }
        }
    }
    if ($sidebarlist_type == "portfolio") {
        $sidebar_options                      = array();
        $sidebar_options['portfolio_sidebar'] = 'Default Portfolio Sidebar';
        $sidebar_options['default_sidebar']   = 'Default Sidebar';
        for ($sidebar_count = 1; $sidebar_count <= $max_sidebars; $sidebar_count++) {

            if (staylodgic_get_option_data('mthemesidebar-' . $sidebar_count) != "") {
                $active_sidebar                                     = staylodgic_get_option_data('mthemesidebar-' . $sidebar_count);
                $sidebar_options['mthemesidebar-' . $sidebar_count] = $active_sidebar;
            }
        }
    }
    if ($sidebarlist_type == "post" || $sidebarlist_type == "page") {
        $sidebar_options                    = array();
        $sidebar_options['default_sidebar'] = 'Default Sidebar';
        if (class_exists('woocommerce')) {
            if ($sidebarlist_type == "page") {
                $sidebar_options['woocommerce_sidebar'] = 'Default WooCommerce Sidebar';
            }
        }
        for ($sidebar_count = 1; $sidebar_count <= $max_sidebars; $sidebar_count++) {

            if (staylodgic_get_option_data('mthemesidebar-' . $sidebar_count) != "") {
                $active_sidebar                                     = staylodgic_get_option_data('mthemesidebar-' . $sidebar_count);
                $sidebar_options['mthemesidebar-' . $sidebar_count] = $active_sidebar;
            }
        }
    }
    if (isset($sidebar_options)) {
        return $sidebar_options;
    } else {
        return false;
    }
}
function staylodgic_generate_metaboxes($meta_data, $post_id)
{
    // Use nonce for verification

    $the_menu_style = staylodgic_get_option_data('menu_type');
    echo '<input type="hidden" name="staylodgic_meta_box_nonce" value="', wp_create_nonce('metabox-nonce'), '" />';

    echo '<div class="metabox-wrapper theme-menu-style-' . $the_menu_style . ' clearfix">';
    $countcolumns = 0;
    foreach ($meta_data['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post_id, $field['id'], true);

        if (staylodgic_page_is_built_with_elementor($post_id)) {
            $elementor_page_settings = get_post_meta($post_id, '_elementor_page_settings', true);
            if (isset($elementor_page_settings[$field['id']])) {
                $meta = $elementor_page_settings[$field['id']];
            }
        }

        $class           = "";
        $trigger_element = "";
        $trigger         = "";

        $titleclass = "is_title";
        if (isset($field['heading'])) {
            if ($field['heading'] == "subhead") {
                $titleclass = "is_subtitle";
            }
        }

        if (isset($field['group'])) {
            if ($field['group'] == "group") {
                $titleclass = "is-a-group";
            }
        }
        if (isset($field['group'])) {
            if ($field['group'] == "group-end") {
                $titleclass = "is-the-group-end";
            }
        }

        if (isset($field['class'])) {
            $class = $field['class'];
        }
        if (!isset($field['toggleClass'])) {
            $field['toggleClass'] = '';
        }
        if (!isset($field['toggleAction'])) {
            $field['toggleAction'] = '';
        }
        if (isset($field['triggerStatus'])) {
            if ($field['triggerStatus'] == "on") {
                $trigger_element = "trigger_element";
            }

            $trigger = "<span data-toggleClass='" . $field['toggleClass'] . "' ";
            $trigger .= "data-toggleAction='" . $field['toggleAction'] . "' ";
            $trigger .= "data-toggleID='" . $field['id'] . "' ";
            $trigger .= "data-parentclass='" . $field['class'] . "' ";
            $trigger .= "></span>";
        }

        if ($field['type'] == "nobreak") {
            $titleclass .= " is_nobreak";
            if ($field['sectiontitle'] != "") {
            }
            $div_column_open = true;
        }
        if ($field['type'] == "break") {
            $titleclass .= " is_break";
            if ($countcolumns > 0) {
                if ($div_is_open) {
                    echo '</div>';
                }
            }
            $countcolumns++;
            echo '<div class="metabox-column">';
            if ($field['sectiontitle'] != "") {
            }
            $div_column_open = true;
        }
        $div_is_open = true;
        echo '<div class="metabox-fields metaboxtype_', $field['type'], ' ' . $class . " " . $titleclass . " " . $trigger_element . '">',
        $trigger,
        '<div class="metabox_label"><label for="', $field['id'], '"></label></div>';
        if (isset($field['type'])) {

            if ($field['type'] != "break" && $field['type'] != "break") {
                if ($field['name'] != "") {
                    echo '<div id="' . $field['id'] . '-section-title" class="sectiontitle clearfix">' . $field['name'] . '</div>';
                }
            }

            switch ($field['type']) {

                case 'selected_proofing_images':
                    $filter_image_ids = staylodgic_get_proofing_attachments($post_id);
                    $found_selection  = false;
                    if ($filter_image_ids) {

                        foreach ($filter_image_ids as $attachment_id) {
                            $proofing_status = get_post_meta($attachment_id, 'checked', true);
                            if ($proofing_status == "true") {
                                $found_selection = true;
                            }
                        }

                        if ($found_selection) {

                            echo '<div class="proofing-admin-selection">';
                            echo '<ul>';
                            foreach ($filter_image_ids as $attachment_id) {
                                $proofing_status = get_post_meta($attachment_id, 'checked', true);
                                if ($proofing_status == "true") {
                                    $thumbnail_imagearray = wp_get_attachment_image_src($attachment_id, 'thumbnail', false);
                                    $thumbnail_imageURI   = $thumbnail_imagearray[0];
                                    echo '<li class="images"><img src="' . esc_url($thumbnail_imageURI) . '" alt="' . esc_attr__('selected', 'staylodgic') . '" /></li>';
                                    $found_selection = true;
                                }
                            }
                            foreach ($filter_image_ids as $attachment_id) {
                                $proofing_status = get_post_meta($attachment_id, 'checked', true);
                                if ($proofing_status == "true") {
                                    echo '<li>' . basename(get_attached_file($attachment_id)) . '</li>';
                                    $found_selection = true;
                                }
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                    }

                    if (!$found_selection) {
                        echo '<div class="proofing-none-selected">';
                        _e('No selection found.', 'staylodgic');
                        echo '</div>';
                    }

                    break;

                case 'image_gallery':
                    // SPECIAL CASE:
                    // std controls button text; unique meta key for image uploads
                    $meta          = get_post_meta($post_id, 'staylodgic_image_ids', true);
                    $thumbs_output = '';
                    $button_text   = ($meta) ? esc_html__('Edit Gallery', 'staylodgic') : $field['std'];
                    $renew_meta    = '';
                    if ($meta) {
                        $field['std']  = esc_html__('Edit Gallery', 'staylodgic');
                        $thumbs        = explode(',', $meta);
                        $thumbs_output = '';
                        $imageidcount  = 0;
                        foreach ($thumbs as $thumb) {
                            if (wp_attachment_is_image($thumb)) {

                                $got_attached_image = wp_get_attachment_image($thumb, 'thumbnail');
                                if (isset($got_attached_image) && $got_attached_image != "") {
                                    if ($imageidcount > 0) {
                                        $renew_meta = $renew_meta . ',';
                                    }
                                    $imageidcount++;

                                    $thumbs_output .= '<li data-thumbnailimageid="' . esc_attr($thumb) . '">' . $got_attached_image . '</li>';
                                    $renew_meta .= $thumb;
                                }
                            }
                        }
                    }

                    echo
                    '<td>
			    		<input type="button" class="button" name="' . esc_attr($field['id']) . '" id="staylodgic_images_upload" value="' . esc_attr($button_text) . '" />

			    		<input type="hidden" name="staylodgic_meta[staylodgic_image_ids]" id="staylodgic_image_ids" value="' . esc_attr($renew_meta ? $renew_meta : 'false') . '" />

			    		<ul class="mtheme-gallery-thumbs">' . $thumbs_output . '</ul>
			    	</td>';

                    break;

                case 'proofing_gallery':
                    // SPECIAL CASE:
                    // std controls button text; unique meta key for image uploads
                    $meta          = get_post_meta($post_id, 'staylodgic_proofing_image_ids', true);
                    $thumbs_output = '';
                    $button_text   = ($meta) ? esc_html__('Edit Proofing Gallery', 'staylodgic') : $field['std'];
                    $renew_meta    = '';
                    if ($meta) {
                        $field['std']  = esc_html__('Edit Proofing Gallery', 'staylodgic');
                        $thumbs        = explode(',', $meta);
                        $thumbs_output = '';
                        $imageidcount  = 0;
                        foreach ($thumbs as $thumb) {
                            if (wp_attachment_is_image($thumb)) {

                                $got_attached_image = wp_get_attachment_image($thumb, 'thumbnail');
                                if (isset($got_attached_image) && $got_attached_image != "") {
                                    if ($imageidcount > 0) {
                                        $renew_meta = $renew_meta . ',';
                                    }
                                    $imageidcount++;

                                    $thumbs_output .= '<li data-thumbnailimageid="' . esc_attr($thumb) . '">' . $got_attached_image . '</li>';
                                    $renew_meta .= $thumb;
                                }
                            }
                        }
                    }

                    echo
                    '<td>
			    		<input type="button" class="button" name="' . esc_attr($field['id']) . '" id="staylodgic_proofing_images_upload" value="' . esc_attr($button_text) . '" />

			    		<input type="hidden" name="staylodgic_meta[staylodgic_proofing_image_ids]" id="staylodgic_proofing_image_ids" value="' . esc_attr($renew_meta ? $renew_meta : 'false') . '" />

			    		<ul class="mtheme-proofing-gallery-thumbs">' . $thumbs_output . '</ul>
			    	</td>';

                    break;

                case 'multi_upload':
                    // SPECIAL CASE:
                    // std controls button text; unique meta key for image uploads
                    $meta          = get_post_meta($post_id, esc_attr($field['id']), true);
                    $thumbs_output = '';
                    $button_text   = ($meta) ? esc_html__('Edit Gallery', 'staylodgic') : $field['std'];
                    if ($meta) {
                        $field['std']  = esc_html__('Edit Gallery', 'staylodgic');
                        $thumbs        = explode(',', $meta);
                        $thumbs_output = '';
                        foreach ($thumbs as $thumb) {
                            $thumbs_output .= '<li>' . wp_get_attachment_image($thumb, 'thumbnail') . '</li>';
                        }
                    }

                    echo
                    '<td>
			    		<input type="button" data-galleryid="' . esc_attr($field['id']) . '" data-imageset="' . esc_attr($meta) . '" class="button meta-multi-upload" name="' . esc_attr($field['id']) . '" value="' . esc_attr($button_text) . '" />

			    		<input type="hidden" name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" value="' . esc_attr($meta ? $meta : 'false') . '" />

			    		<ul class="mtheme-multi-thumbs multi-gallery-' . esc_attr($field['id']) . '">' . $thumbs_output . '</ul>
			    	</td>';

                    break;

                case 'display_image_attachments':
                    $images = get_children(
                        array(
                            'post_parent'    => $post_id,
                            'post_status'    => 'inherit',
                            'post_type'      => 'attachment',
                            'post_mime_type' => 'image',
                            'order'          => 'ASC',
                            'numberposts'    => -1,
                            'orderby'        => 'menu_order'
                        )
                    );
                    if ($images) {
                        foreach ($images as $id => $image) {
                            $attatchmentID = $image->ID;
                            $imagearray    = wp_get_attachment_image_src($attatchmentID, 'thumbnail', false);
                            $imageURI      = $imagearray[0];
                            $imageID       = get_post($attatchmentID);
                            $imageTitle    = $image->post_title;
                            echo '<img src="' . esc_url($imageURI) . '" alt="' . esc_attr__('image', 'staylodgic') . '" />';
                        }
                    } else {
                        echo esc_html__('No images found.', 'staylodgic');
                    }
                    break;

                case "seperator":
                    echo '<hr/>';

                    if (isset($field['action']) && 'display_choices_for_customer' == $field['action']) {
                        echo '<a class="choice-customer-existing">' . __('Or choose an existing customer', 'staylodgic') . '</a>';
                    }

                    break;

                    // Color picker
                case "color":
                    $default_color = '';
                    if (isset($value['std'])) {
                        if ($val != $value['std']) {
                            $default_color = ' data-default-color="' . esc_attr($value['std']) . '" ';
                        }
                    }
                    $color_value = $meta ? $meta : $field['std'];
                    echo '<input data-alpha-enabled="true" name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '" class="colorswatch of-color"  type="text" value="' . esc_attr($color_value) . '" />';

                    break;

                case 'upload':
                    if ($meta != "") {
                        $image_url_id         = staylodgic_get_image_id_from_url($meta);
                        $image_thumbnail_data = wp_get_attachment_image_src($image_url_id, "thumbnail", true);
                        $image_thumbnail_url  = $image_thumbnail_data[0];
                        if ($image_thumbnail_url) {
                            echo '<img height="100px" src="' . esc_url($image_thumbnail_url) . '" />';
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
                    $tablet_value  = "0";
                    $mobile_value  = "0";

                    if (isset($text_value) && $text_value != "") {
                        $css_values = explode(',', $text_value);
                        if (isset($css_values[0])) {
                            $desktop_value = $css_values[0];
                            $tablet_value  = $css_values[0];
                            $mobile_value  = $css_values[0];
                        }
                        if (isset($css_values[1])) {
                            $tablet_value = $css_values[1];
                            $mobile_value = $tablet_value;
                        }
                        if (isset($css_values[2])) {
                            $mobile_value = $css_values[2];
                        }
                    }

                    echo '<span class="responsive-data-media">';
                    echo '<span class="responsive-cue-icons dashicons dashicons-desktop"></span><span title="Desktop" class="responsive-data-fields responsive-data-desktop">' . $desktop_value . '</span>';
                    echo '<span class="responsive-cue-icons dashicons dashicons-tablet"></span><span title="Tablet" class="responsive-data-fields responsive-data-tablet">' . $tablet_value . '</span>';
                    echo '<span class="responsive-cue-icons dashicons dashicons-smartphone"></span><span title="Mobile" class="responsive-data-fields responsive-data-mobile">' . $mobile_value . '</span>';
                    echo '</span>';

                    echo '<input type="text" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;

                case 'disabled':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="text" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" disabled />';
                    echo '<input type="hidden" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';

                    break;

                case 'staylodgic_registration_data':

                    $registration_instance = new \Staylodgic\GuestRegistry();
                    $registration_instance->display_registration();

                    break;

                case 'readonly':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input readonly type="text" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;

                case 'taxgenerate':

                    $the_post_id = $field['page_id'];
                    $taxStatus = get_post_meta($the_post_id, 'staylodgic_tax', true);
                    $taxHTML = get_post_meta($the_post_id, 'staylodgic_tax_html_data', true);
                    $taxData = get_post_meta($the_post_id, 'staylodgic_tax_data', true);

                    echo '<div id="input-tax-summary">';
                    echo '<div class="input-tax-summary-wrap">';
                    if ('enabled' == $taxStatus) {
                        echo '<div class="input-tax-summary-wrap-inner">';
                        echo $taxHTML;
                        error_log('------ tax out -------');
                        error_log($the_post_id);
                        error_log($taxStatus);
                        error_log(print_r($taxHTML, true));
                        echo '</div>';
                    }
                    if ('excluded' == $taxStatus) {
                        echo '<div class="input-tax-summary-wrap-inner">';
                        echo 'Tax Exluded';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '</div>';
                    break;

                case 'activity_list_generate':

                    $the_post_id = $field['page_id'];
                    $activity = new Staylodgic\Activity();
                    echo $activity->get_activities($the_post_id);

                    break;

                case 'offview':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="hidden" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;

                case 'offview_display_ticket_result':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="hidden" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . $text_value . '" size="30" />';

                    // error_log ( '------- Display ticket off view display ------' );
                    // error_log ( $field['page_id'], $text_value );
                    if (isset($text_value) && '' !== $text_value) {
                        $activity = new Staylodgic\Activity();
                        $ticket = $activity->display_ticket($field['page_id'], $text_value);

                        echo $ticket;
                        echo '<div class="ticket-save-pdf-button">';
                        echo '<button data-file="registration-' . $field['page_id'] . '" data-id="' . $field['page_id'] . '" id="save-pdf-ticket-button" class="save-pdf-ticket-button button button-primary button-large">Save PDF</button>';
                        echo '</div>';
                    } else {
                        echo '<div class="ticket-generate">Please create reservation to generate a ticket.<br/>Ticket will display after the reservation is published.</div>';
                    }

                    break;

                case 'generate-qrcode':
                    echo '<button class="button button-secondary" id="generate-qr-code">Generate QR Code</button>';
                    echo '<div id="qrcode"></div>'; // Container for the QR code
                    break;

                case 'text':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="text" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;

                case 'number':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="number" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="5" />';
                    break;

                case 'registration':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="text" class="' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';

                    if ($text_value) {
                        $registry_instance = new \Staylodgic\GuestRegistry();
                        $res_reg_ids =  $registry_instance->fetch_res_reg_ids_by_booking_number($text_value);
                        if (isset($res_reg_ids['reservationID']) && $res_reg_ids['guestRegisterID']) {
                            echo $registry_instance->output_registration_and_occupancy($res_reg_ids['reservationID'], $res_reg_ids['guestRegisterID'], 'text');
                        } else {
                            echo '<div class="registration-notice booking-number-not-found">';
                            echo __('Booking number not found.', 'staylodgic');
                            echo '</div>';
                        }
                        //echo $registry_instance->output_registration_and_occupancy($res_reg_ids['reservationID'], $res_reg_ids['guestRegisterID'], 'icons');
                    }

                    break;

                case 'currency':
                    $text_value = $meta ? $meta : $field['std'];
                    if (isset($field['datatype'])) {
                        $priceof =  'data-priceof="' . $field['datatype'] . '"';
                    }
                    $readonly = '';
                    if (isset($field['inputis'])) {
                        $readonly =  ' readonly';
                    }
                    echo '<input type="number" ' . $priceof . $readonly . ' data-currencyformat="2" class="' . $class . ' currency-input" min="0" step="0.01" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;

                case 'currencyarray':
                    $dateTime = date("Y-m-d H:i:s");
                    $text_value = $meta ? $meta : $field['std'];
                    if (isset($field['datatype'])) {
                        $priceof = 'data-priceof="' . $field['datatype'] . '"';
                    }
                    $readonly = '';
                    if (isset($field['inputis'])) {
                        $readonly = ' readonly';
                    }
                    echo '<input type="number" ' . $priceof . $readonly . ' data-currencyformat="2" class="' . $class . ' currency-input" min="0" step="0.01" name="staylodgic_reservation_room_paid[' . $dateTime . ']" id="', esc_attr($field['id']), '" value="" size="30" />';
                    echo "<ul>";

                    $payments = get_post_meta(get_the_id(), 'staylodgic_reservation_room_paid', true);
                    $total_cost = get_post_meta(get_the_id(), 'staylodgic_reservation_total_room_cost', true);
                    $total_payments = 0;
                    if (is_array($payments) && !empty($payments)) {
                        echo "<ul>";
                        foreach ($payments as $timestamp => $value) {
                            if (isset($value) && '' !== $value) {
                                $payment_id = 'payment-' . sanitize_title($timestamp);
                                echo '<li class="' . $payment_id . '">';
                                echo '<div class="payment-date-lister">';
                                echo staylodgic_price($value);
                                echo ' [<span class="remove-payment" data-timestamp="$timestamp" data-index="$index">remove</span>] ' . $timestamp;
                                echo '<input type="hidden" name="staylodgic_reservation_room_paid[' . $timestamp . ']" value="' . $value . '" size="30" />';
                                echo '</div>';
                                echo "</li>";

                                $total_payments = $total_payments + $value;
                            }
                        }
                        echo "</ul>";
                        echo '<p class="reservation-payment-balance">' . __('Balance', 'staylodgic') . '</p>';
                        $balance = intval($total_cost) - intval($total_payments);
                        echo staylodgic_price($balance);
                    }

                    break;

                case 'switch':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<div class="switch-toggle">';
                    echo '<input type="hidden" class="meta-switch-toggle ' . $class . '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    echo '<div class="switch-toggle-slide"><div class="switch-inner"></div></div>';
                    echo '</div>';
                    break;

                case 'bedsetup_set':
                    $text_value = $meta ? $meta : $field['std'];
                    // Set the HTML code for the new bed setup
                    $bed_container = '
                        <div id="bed_setup_container" class="bed-setup-container-template">
                        <div class="metabox_label"><label for="staylodgic_alt_bedsetup_${uniqueID}"></label></div>
                        <div id="staylodgic_alt_bedsetup_${uniqueID}-section-title" class="sectiontitle clearfix">Set single or multiple beds</div>
                        <div class="bedlayout-wrap" data-repeat="staylodgic_alt_bedsetup_${uniqueID}">
                        <div class="bedlayout">
                            <div class="bedlayout-box" id="bedlayout-box">
                                <select disabled class="bedtype-select" name="staylodgic_alt_bedsetup[${uniqueID}][bedtype][]" id="bed_type_staylodgic_alt_bedsetup_${uniqueID}_0">
                                <option value="twinbed">Twin bed</option>
                                <option value="fullbed">Full bed</option>
                                <option value="queenbed">Queen bed</option>
                                <option value="kingbed">King bed</option>
                                <option value="bunkbed">Bunk bed</option>
                                <option value="sofabed">Sofa bed</option>
                                </select> X
                                <input disabled placeholder="0" type="number" min="0" name="staylodgic_alt_bedsetup[${uniqueID}][bednumber][]" value="" id="bed_number${uniqueID}_0">
                            </div>
                        </div>
                        <span class="add-bedlayout-box">Add beds</span>
                        <span class="add-bedlayout-box-notice">Max Reached!</span>
                        </div>
                        <div class="metabox-description">Add bed layouts to the room.</div>
                        </div>
                    ';

                    // error_log( '===== bed layout');
                    // error_log( $field['id']);
                    // error_log( print_r($meta, true));

                    $data = array();
                    $data = $meta;

                    if (isset($field['target'])) {
                        $field['options'] = staylodgic_get_select_target_options($field['target']);
                    }

                    if (isset($data) && is_array($data)) {
                        $repeat_count = 0;
                        foreach ($data as $uniqueID => $values) {
                            echo '<div class="bed-setup-dynamic-container" data-unique-id="' . $uniqueID . '">';
                            echo '<h3>Bed Layout</h3>';
                            echo '<div class="bedlayout-wrap" data-repeat="staylodgic_alt_bedsetup_${uniqueID}">';
                            echo '<div class="bedlayout">';
                            if (isset($values['bedtype']) && isset($values['bednumber'])) {
                                foreach ($values['bedtype'] as $index => $bedtype) {
                                    $bednumber = $values['bednumber'][$index];

                                    if (!empty($bedtype)) {
                                        $found_data = true;
                                        $age = '';
                                        $class = '';
                                        $field_id = 'field_id'; // Replace with your actual field ID

                                        echo '<div class="bedlayout-box" id="bedlayout-box-' . $uniqueID . '">';
                                        echo '<div class="selectbox-type-selector"><select class="bedtype-select" name="staylodgic_alt_bedsetup[' . $uniqueID . '][bedtype][]" id="bed_type_' . $field_id . '_' . $repeat_count . '">';

                                        foreach ($field['options'] as $key => $option) {
                                            if ($key == '0') {
                                                $key = __('All the items', 'staylodgic');
                                            }
                                            echo '<option value="' . esc_attr($key) . '"', $bedtype == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                                        }

                                        echo '</select>';
                                        echo ' X <input placeholder="0" min="0" type="number" name="staylodgic_alt_bedsetup[' . $uniqueID . '][bednumber][]" value="' . esc_attr($bednumber) . '" id="bed_number' . $repeat_count . '" />';

                                        echo '<div class="remove-bedlayout">Remove</div>';
                                        echo '</div>';

                                        echo '</div>';
                                    }

                                    $repeat_count++;
                                }
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }


                    echo $bed_container;
                    // add an input button
                    echo '<div id="bed-inputs-container"></div>';
                    echo '<span id="add-bed-setup-button" class="add-bedlayout-box">Add bed choices</span>';

                    break;

                case 'bedsetup_repeat':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<div class="bedlayout-wrap" data-repeat="' . $field['id'] . '">';
                    echo '<div class="bedlayout">';
                    $repeat_count = 0;
                    $found_data   = false;
                    if (isset($meta) && is_array($meta)) {
                        foreach ($meta['bedtype'] as $value) {
                            if (isset($value) && $value != "") {
                                $found_data = true;
                                $age        = '';
                                if (isset($meta['bedtype'][$repeat_count])) {
                                    $bedtype = $meta['bedtype'][$repeat_count];
                                }
                                if (isset($meta['bednumber'][$repeat_count])) {
                                    $bednumber = $meta['bednumber'][$repeat_count];
                                }
                                $class = '';
                                if (isset($field['target'])) {
                                    $field['options'] = staylodgic_get_select_target_options($field['target']);
                                }
                                echo '<div class="bedlayout-box" id="bedlayout-box">';
                                echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox bedtype-select" name="', esc_attr($field['id']) . '[bedtype][]" id="bed_type_' . $field['id'] . '_' . $repeat_count . '">';
                                foreach ($field['options'] as $key => $option) {
                                    if ($key == '0') {
                                        $key = __('All the items', 'staylodgic');
                                    }
                                    echo '<option value="' . esc_attr($key) . '"', $bedtype == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                                }
                                echo '</select>';
                                echo ' X <input placeholder="0" min="0" type="number" name="' . esc_attr($field['id']) . '[bednumber][]" value="' . esc_attr($bednumber) . '" id="bed_number' . $repeat_count . '" /></div>';
                                if ($repeat_count > 0) {
                                    echo '<div class="remove-bedlayout">Remove</div>';
                                }
                                echo '</div>';
                            }
                            $repeat_count++;
                        }
                    }
                    if (!$found_data) {
                        if (isset($field['target'])) {
                            $field['options'] = staylodgic_get_select_target_options($field['target']);
                        }
                        echo '<div class="bedlayout-box" id="bedlayout-box">';
                        echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr($field['id']) . '[bedtype][]" id="bed_type_' . $field['id'] . '_0">';
                        foreach ($field['options'] as $key => $option) {
                            if ($key == '0') {
                                $key = __('All the items', 'staylodgic');
                            }
                            echo '<option value="' . esc_attr($key) . '"', $meta == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                        }
                        echo '</select>';
                        echo ' X <input placeholder="How many" min="0" type="number" name="' . esc_attr($field['id']) . '[bednumber][]" value="" id="bed_number0" /></div>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '<span class="add-bedlayout-box">' . esc_html__('Add bed type', 'staylodgic') . '</span>';
                    echo '<span class="add-bedlayout-box-notice">' . esc_html__('Max Reached!', 'staylodgic') . '</span>';
                    echo '</div>';
                    break;

                case 'taxsetup_repeat':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<div class="taxlayout-wrap" data-repeat="' . $field['id'] . '">';
                    echo '<div class="taxlayout">';
                    $repeat_count = 0;
                    $taxlabel     = '';
                    $taxtype      = '';
                    $taxnumber    = '';
                    $found_data   = false;

                    if (isset($meta) && is_array($meta)) {
                        if (isset($meta['taxnumber'])) {
                            foreach ($meta['taxnumber'] as $value) {
                                if (isset($value) && $value != "") {
                                    $found_data = true;

                                    $age = '';
                                    if (isset($meta['taxtype'][$repeat_count])) {
                                        $taxtype = $meta['taxtype'][$repeat_count];
                                    }
                                    if (isset($meta['taxnumber'][$repeat_count])) {
                                        $taxnumber = $meta['taxnumber'][$repeat_count];
                                    }
                                    if (isset($meta['taxlabel'][$repeat_count])) {
                                        $taxlabel = $meta['taxlabel'][$repeat_count];
                                    }
                                    $class = '';
                                    echo '<div class="taxlayout-box" id="taxlayout-box">';
                                    echo '<input placeholder="Label" type="text" name="' . esc_attr($field['id']) . '[taxlabel][]" value="' . esc_attr($taxlabel) . '" id="tax_label' . $repeat_count . '" />';
                                    echo '<div class="selectbox-type-selector">';
                                    if (isset($field['choice']) && '' == $field['choice']) {
                                        echo '<select class="chosen-select-metabox taxtype-select" name="', esc_attr($field['id']) . '[taxtype][]" id="tax_type_' . $field['id'] . '_' . $repeat_count . '">';
                                        foreach ($field['options'] as $key => $option) {
                                            if ($key == '0') {
                                                $key = __('All the items', 'staylodgic');
                                            }
                                            echo '<option value="' . esc_attr($key) . '"', $taxtype == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                                        }
                                        echo '</select> X ';
                                    }
                                    echo '<input placeholder="0" type="text" name="' . esc_attr($field['id']) . '[taxnumber][]" value="' . esc_attr($taxnumber) . '" id="tax_number' . $repeat_count . '" /></div>';
                                    if ($repeat_count > 0) {
                                        echo '<div class="remove-taxlayout">Remove</div>';
                                    }
                                    echo '</div>';
                                }
                                $repeat_count++;
                            }
                        }
                    }
                    if (!$found_data) {
                        echo '<div class="taxlayout-box" id="taxlayout-box">';
                        echo '<input placeholder="Label" type="text" name="' . esc_attr($field['id']) . '[taxlabel][]" value="" id="tax_label0" />';
                        echo '<div class="selectbox-type-selector">';
                        if (isset($field['choice']) && '' == $field['choice']) {
                            echo '<select class="chosen-select-metabox" name="', esc_attr($field['id']) . '[taxtype][]" id="tax_type_' . $field['id'] . '_0">';
                            foreach ($field['options'] as $key => $option) {
                                echo '<option value="' . esc_attr($key) . '"', $meta == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                            }

                            echo '</select> X ';
                        }
                        echo '<input placeholder="Value% or Value" type="text" name="' . esc_attr($field['id']) . '[taxnumber][]" value="" id="tax_number0" /></div>';
                        echo '</div>';
                    }
                    echo '</div>';
                    echo '<span class="add-taxlayout-box">' . esc_html__('Add layout', 'staylodgic') . '</span>';
                    echo '<span class="add-taxlayout-box-notice">' . esc_html__('Max Reached!', 'staylodgic') . '</span>';
                    echo '</div>';
                    break;

                case 'repeat_text':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<div class="movethis-wrap" data-repeat="' . $field['id'] . '">';
                    echo '<div class="movethis">';

                    $repeat_count = 0;
                    $found_data   = false;
                    if (isset($meta) && is_array($meta)) {
                        foreach ($meta['age'] as $value) {
                            if (isset($value) && $value != "") {
                                $found_data = true;
                                $age        = '';
                                if (isset($meta['age'][$repeat_count])) {
                                    $age = $meta['age'][$repeat_count];
                                }
                                echo '<div class="text-box" id="text-box">';
                                echo '<input placeholder="' . esc_attr__('Age', 'staylodgic') . '" type="text" name="' . esc_attr($field['id']) . '[age][]" value="' . esc_attr($age) . '" id="box_size' . $repeat_count . '" />';
                                if ($repeat_count > 0) {
                                    echo '<span class="remove-box">' . esc_html__('Remove', 'staylodgic') . '</span>';
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
                    echo '<span class="add-box">' . esc_html__('Add Child', 'staylodgic') . '</span>';
                    echo '<span class="add-box-notice">' . esc_html__('Max Reached!', 'staylodgic') . '</span>';
                    echo '</div>';
                    break;

                case 'actvity_schedule':
                    $text_value = $meta ? $meta : $field['std'];

                    error_log('------ Events Schedule ------');
                    error_log(print_r($text_value, true));

                    // Set the HTML code for the event schedule
                    $schedule_container = '
                        <div id="event_schedule_container" class="event-schedule-container-template">
                            <div class="metabox_label"><label for="staylodgic_activity_schedule_${day}"></label></div>
                            <div class="schedule-wrap" data-repeat="staylodgic_activity_schedule_${day}">';

                    // Loop through each day of the week
                    $days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days_of_week as $day) {
                        $day_lower = strtolower($day);
                        $schedule_container .= '
                            <div class="day-schedule" id="day_schedule_' . $day_lower . '">
                                <div class="day-title">' . $day . '</div>
                                <div class="time-inputs">';

                        // Inside the loop where input fields are created
                        if (!empty($text_value[$day_lower])) {
                            foreach ($text_value[$day_lower] as $time) {
                                $schedule_container .= '
                                        <div class="time-input-wrapper">
                                            <input type="time" name="staylodgic_activity_schedule[' . $day_lower . '][]" value="' . esc_attr($time) . '">
                                            <span class="remove-time-input"><i class="dashicons dashicons-remove"></i></span>
                                        </div>';
                            }
                        } else {
                            // If no saved times, add an empty input field with a remove button
                            $schedule_container .= '
                                    <div class="time-input-wrapper">
                                        <input type="time" name="staylodgic_activity_schedule[' . $day_lower . '][]" value="">
                                        <span class="remove-time-input"><i class="dashicons dashicons-remove"></i></span>
                                    </div>';
                        }

                        $schedule_container .= '
                                </div>
                                <span class="add-time-input">Add Time</span>
                            </div>';
                    }

                    $schedule_container .= '
                            </div>
                        </div>
                    ';

                    echo $schedule_container;
                    break;



                case 'timepicker':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<select name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '">';
                    $start = strtotime('12am');
                    for ($i = 0; $i < (24 * 4); $i++) {

                        $tod     = $start + ($i * 15 * 60);
                        $display = date('h:i A', $tod);

                        if (substr($display, 0, 2) == '00') {
                            $display = '12' . substr($display, 2);
                        }
                        if ($meta == $display) {
                            $timeselected = 'selected="selected"';
                        } else {
                            $timeselected = "";
                        }

                        $display_user_time = $display;
                        $event_time_format = staylodgic_get_option_data('events_time_format');
                        if ($event_time_format == "24hr") {
                            $display_user_time = date('H:i', $tod);
                        }
                        echo '<option value="' . esc_attr($display) . '" ' . $timeselected . '>' . esc_attr($display_user_time) . '</option>';
                    }
                    echo '</select>';

                    break;

                case 'country':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<select class="chosen-select-metabox" name="' . esc_attr($field['id']) . '" id="' . esc_attr($field['id']) . '">';
                    echo staylodgic_country_list('select', $meta);
                    echo '</select>';

                    break;
                case 'datepicker':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="text" class="' . $class . ' datepicker" data-enable-time="true" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    break;
                case 'hidden':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input type="hidden" name="', esc_attr($field['id']), '_hidden" id="', esc_attr($field['id']), '_hidden" value="' . esc_attr($text_value) . '" />';
                    break;
                case 'activity_reservation':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input data-postid="' . get_the_id() . '" type="text" class="' . $class . ' activity-reservation" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    echo '<div id="activity-reservation-details"></div>';
                    break;
                case 'reservation':
                    $text_value = $meta ? $meta : $field['std'];
                    echo '<input data-postid="' . get_the_id() . '" type="text" class="' . $class . ' reservation" data-enable-time="true" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="' . esc_attr($text_value) . '" size="30" />';
                    echo '<div id="reservation-details"></div>';
                    break;
                case 'textarea':
                    $textarea_value = $meta ? $meta : $field['std'];
                    echo '<textarea name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" cols="60" rows="4" >' . esc_textarea($textarea_value) . '</textarea>';
                    break;
                case 'wpeditor':
                    $textarea_value = $meta ? $meta : $field['std'];
                    $editor_id = esc_attr($field['id']);
                    $settings = array(
                        'textarea_name' => esc_attr($field['id']),
                        'media_buttons' => false, // Set to true if you want to allow media uploads
                        'textarea_rows' => 10, // Number of rows for the editor
                        'teeny' => true, // Use teeny editor if you want a simplified version
                    );
                    wp_editor($textarea_value, $editor_id, $settings);
                    break;
                case 'fontselector':
                    $class = '';
                    if (isset($field['target'])) {
                        $field['options'] = staylodgic_get_select_target_options($field['target']);
                    }

                    echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox metabox_google_font_select" name="', $field['id'], '" id="', $field['id'], '">';
                    foreach ($field['options'] as $key => $option) {
                        echo '<option  data-font="' . esc_attr($option) . '" value="' . esc_attr($key) . '"', $meta == $key ? ' selected="selected"' : '', '>', esc_attr($option), '</option>';
                    }
                    echo '</select></div>';

                    $googlefont_text = __('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ 0123456789', 'staylodgic');

                    $hide = " hide";
                    if ($key != "none" && $key != "") {
                        $hide = "";
                    }

                    echo '<p class="' . esc_attr($field['id'] . '_metabox_googlefont_previewer metabox_google_font_preview' . $hide) . '">' . esc_html($googlefont_text) . '</p>';

                    break;
                case 'select':
                    $class = '';
                    if (isset($field['target'])) {
                        $field['options'] = staylodgic_get_select_target_options($field['target']);
                    }
                    echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox choice-', esc_attr($field['id']), '" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
                    foreach ($field['options'] as $key => $option) {
                        if ($key == '0') {
                            $key = __('All the items', 'staylodgic');
                        }
                        echo '<option value="' . esc_attr($key) . '"', $meta == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                    }
                    echo '</select></div>';

                    break;

                case 'changelog':
                    $post_id = get_the_id();

                    // Retrieve the change log for the post
                    $change_log = get_post_meta($post_id, 'staylodgic_change_log', true);

                    // echo '<pre>';
                    // print_r($change_log);
                    // echo '</pre>';
                    // Check if the change log exists and is an array
                    if (is_array($change_log) && !empty($change_log)) {

                        $reversed_change_log = array_reverse($change_log);

                        echo '<div class="settings-change-log">';
                        if (is_array($reversed_change_log) && !empty($reversed_change_log)) {
                            echo '<ol>';
                            foreach ($reversed_change_log as $change) {
                                echo '<li>';
                                echo '<strong>' . $change['field_id'] . '</strong> changed by ' . $change['user'] . ' on ' . $change['timestamp'] . '<br>';
                                // Format old and new values using the format_value function
                                echo '<strong>Old Value:</strong> ' . staylodgic_format_value($change['old_value']) . '<hr/>';
                                echo '<strong>New Value:</strong> ' . staylodgic_format_value($change['new_value']) . '<hr/>';
                                echo '</li>';
                            }
                            echo '</ol>';
                        } else {
                            echo 'No change log available for this post.';
                        }
                        echo '</div>';
                    } else {
                        echo 'No change log available for this post.';
                    }
                    break;

                case 'mealplan_included':

                    $meal_plans = staylodgic_get_option('mealplan');

                    if (is_array($meal_plans) && count($meal_plans) > 0) {
                        $included_meal_plans = array();
                        $optional_meal_plans = array();

                        foreach ($meal_plans as $id => $plan) {
                            if ($plan['choice'] === 'included') {
                                $included_meal_plans[$id] = $plan;
                            } elseif ($plan['choice'] === 'optional') {
                                $optional_meal_plans[$id] = $plan;
                            }
                        }

                        $html = '';
                        $html_input = '';

                        echo '<div class="room-included-meals">';
                        if (is_array($included_meal_plans) && count($included_meal_plans) > 0) {
                            foreach ($included_meal_plans as $id => $plan) {
                                if (isset($plan['mealtype'])) {
                                    $html_input .= staylodgic_get_mealplan_labels($plan['mealtype']) . __(' included. ', 'staylodgic');
                                }
                            }
                        }
                        $textarea_value = $meta ? $meta : $html_input;
                        echo '<textarea name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" cols="60" rows="4" >' . esc_textarea($textarea_value) . '</textarea>';

                        echo '</div>';

                        echo $html;
                    }

                    break;

                case 'bedlayout':

                    $the_post_id = get_the_ID(); // Replace this with the actual post ID
                    $room_id = get_post_meta($the_post_id, 'staylodgic_room_id', true);

                    $booking_instance = new \Staylodgic\Booking();
                    $bedlayoutInputs     = $booking_instance->generate_bed_metabox($room_id, $field['id'], $meta);

                    echo '<div id="metabox-bedlayout" data-field="' . esc_attr($field['id']) . '" data-metavalue="' . esc_attr($meta) . '">';
                    echo $bedlayoutInputs;
                    echo '</div>';

                    break;

                case 'mealplan':

                    $meal_plans = staylodgic_get_option('mealplan');

                    if (is_array($meal_plans) && count($meal_plans) > 0) {
                        $included_meal_plans = array();
                        $optional_meal_plans = array();

                        foreach ($meal_plans as $id => $plan) {
                            if ($plan['choice'] === 'included') {
                                $included_meal_plans[$id] = $plan;
                            } elseif ($plan['choice'] === 'optional') {
                                $optional_meal_plans[$id] = $plan;
                            }
                        }

                        $html = '';
                        echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
                        echo '<option value="none"', $meta == 'none' ? ' selected' : '', '>None</option>';
                        foreach ($optional_meal_plans as $key => $option) {
                            echo '<option value="' . esc_attr($option['mealtype']) . '"', $meta == $option['mealtype'] ? ' selected' : '', '>' . staylodgic_get_mealplan_labels($option['mealtype']) . '</option>';
                        }
                        echo '</select></div>';

                        echo $html;
                    }

                    break;

                case 'payments':
                    $class = '';
                    if (isset($field['target'])) {
                        $field['options'] = staylodgic_get_select_target_options($field['target']);
                    }
                    echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
                    foreach ($field['options'] as $key => $option) {
                        if ($key == '0') {
                            $key = __('All the items', 'staylodgic');
                        }
                        echo '<option value="' . esc_attr($key) . '"', $meta == $key ? ' selected' : '', '>', esc_attr($option), '</option>';
                    }
                    echo '</select></div>';
                    echo '<div id="payment-reservation-details"></div>';

                    break;

                    // Basic text input
                case 'occupants':
                    $output = '';

                    $max_adults   = 'disabled';
                    $max_children = 'disabled';
                    $max_guests   = '0';

                    $roomOccupantData = array();

                    if (isset($field['datafrom'])) {
                        if ('roomtype' == $field['datafrom']) {
                            $room = get_posts('post_type=slgc_room&numberposts=-1&order=ASC');
                            if ($room) {
                                foreach ($room as $key => $list) {
                                    $custom = get_post_custom($list->ID);
                                    if (isset($custom["staylodgic_max_adult_limit_status"][0])) {
                                        $adult_limit_status = $custom["staylodgic_max_adult_limit_status"][0];
                                        if ('1' == $adult_limit_status) {
                                            $max_adults = $custom["staylodgic_max_adults"][0];
                                        }
                                    }
                                    if (isset($custom["staylodgic_max_children_limit_status"][0])) {
                                        $children_limit_status = $custom["staylodgic_max_children_limit_status"][0];
                                        if ('1' == $children_limit_status) {
                                            $max_children = $custom["staylodgic_max_children"][0];
                                        }
                                    }
                                    if (isset($custom["staylodgic_max_guests"][0])) {
                                        $max_guests = $custom["staylodgic_max_guests"][0];
                                    }
                                    $roomOccupantData[$list->ID]['max_adults']   = $max_adults;
                                    $roomOccupantData[$list->ID]['max_children'] = $max_children;
                                    $roomOccupantData[$list->ID]['max_guests']   = $max_guests;
                                }
                            }
                        }
                    }
                    if (isset($field['unit'])) {
                        $jsonOccupants = json_encode($roomOccupantData);
                        echo "<div class='occupant-" . $field['occupant'] . " occupants-range ranger-min-max-wrap' data-room='' data-occupant='" . $field['occupant'] . "' data-occupants='" . $jsonOccupants . "'><span class='ranger-min-value'>" . esc_attr($field['min']) . "</span>";
                        echo '<span class="ranger-max-value">' . esc_attr($field['max']) . '</span></div>';
                        echo '<div id="' . esc_attr($field['id']) . '_slider"></div>';
                        echo '<div class="ranger-bar">';
                    }
                    if (!isset($meta) || $meta == "") {
                        if ($meta == 0) {
                            $meta = "0";
                        } else {
                            $meta = $field['std'];
                        }
                    }
                    $meta = floatval($meta);
                    echo '<input id="' . esc_attr($field['id']) . '" class="of-input input-occupant input-occupant-' . $field['occupant'] . '" name="' . esc_attr($field['id']) . '" type="text" value="' . esc_attr($meta) . '"';

                    if (isset($field['unit'])) {
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

                    $reservation_instance = new \Staylodgic\Reservations();
                    $reservation_array = \Staylodgic\Reservations::getReservationIDsForCustomer($field['customer_id']);
                    $bookings = $reservation_instance->getEditLinksForReservations($reservation_array);

                    $activity_instance = new \Staylodgic\Activity();
                    $activity_array = \Staylodgic\Activity::get_activity_ids_for_customer($field['customer_id']);
                    $activities = $activity_instance->get_edit_links_for_activity($activity_array);

                    if ( '<ul></ul>' != $bookings ) {
                        echo '<h4 class="metabox-bookings-found">Bookings</h4>';
                        echo $bookings;
                    }
                    if ( '<ul></ul>' != $activities ) {
                        echo '<h4 class="metabox-bookings-found">Activities</h4>';
                        echo $activities;
                    }

                    if ( '<ul></ul>' == $bookings ) {
                        echo '<div class="metabox-no-bookings-found">No Bookings found</div>';
                        echo '<br/>';
                    }
                    if ( '<ul></ul>' == $activities ) {
                        echo '<div class="metabox-no-bookings-found">No Activities found</div>';
                    }
                    break;

                case 'get_customer_data':

                    $customer_array       = staylodgic_get_customer_array();

                    $post_type = get_post_type($field['id']);

                    if ('slgc_activityres' == $post_type) {
                        $reservation_instance = new \Staylodgic\Activity();
                    } else {
                        $reservation_instance = new \Staylodgic\Reservations();
                    }
                    $customer_post_id     = $reservation_instance->get_reservation_customer_id($field['id']);
                    $customer_post_edit   = get_edit_post_link($customer_post_id);
                    echo '<a class="button button-primary button-large customer-edit-button" href="' . esc_url($customer_post_edit) . '">' . __('Edit Customer', 'staylodgic') . '</a><span class="customer-choice-inbetween"></span><a class="choice-customer-existing">' . __('or choose an existing customer', 'staylodgic') . '</a>';
                    $customer_data = \Staylodgic\Data::getCustomer_MetaData($customer_array, $customer_post_id);

                    echo \Staylodgic\Customers::generateCustomerHtmlList($customer_data);

                    break;
                case 'reservation_registration':

                    $reservation_instance = new \Staylodgic\Reservations($date = false, $room_id = false, $reservation_id = get_the_id());
                    $bookingnumber = $reservation_instance->get_booking_number();

                    if ($bookingnumber) {
                        $registry_instance = new \Staylodgic\GuestRegistry();
                        $res_reg_ids =  $registry_instance->fetch_res_reg_ids_by_booking_number($bookingnumber);
                        if (isset($res_reg_ids['guestRegisterID'])) {
                            echo $registry_instance->output_registration_and_occupancy($res_reg_ids['reservationID'], $res_reg_ids['guestRegisterID'], 'text');
                            echo $registry_instance->output_registration_and_occupancy($res_reg_ids['reservationID'], $res_reg_ids['guestRegisterID'], 'icons');
                            $guestregistration_post_edit   = get_edit_post_link($res_reg_ids['guestRegisterID']);
                            echo '<a class="button button-primary button-large" href="' . $guestregistration_post_edit . '">Edit Guest Registration</a>';
                        } else {
                            echo '<a data-bookingnumber="' . $bookingnumber . '" class="create-guest-registration button button-primary button-large" href="#">Create Guest Registration</a>';
                        }
                    }
                    break;

                case 'guests':
                    $output = "";

                    $roomOccupantData = array();

                    if (isset($field['datafrom'])) {
                        if ('roomtype' == $field['datafrom']) {

                            $room = get_posts('post_type=slgc_room&numberposts=-1&order=ASC');
                            if ($room) {
                                foreach ($room as $key => $list) {
                                    $max_adults   = 'disabled';
                                    $max_children = 'disabled';
                                    $max_guests   = '0';
                                    $custom       = get_post_custom($list->ID);
                                    if (isset($custom["staylodgic_max_adult_limit_status"][0])) {
                                        $adult_limit_status = $custom["staylodgic_max_adult_limit_status"][0];
                                        if ('1' == $adult_limit_status) {
                                            $max_adults = $custom["staylodgic_max_adults"][0];
                                        }
                                    }
                                    if (isset($custom["staylodgic_max_children_limit_status"][0])) {
                                        $children_limit_status = $custom["staylodgic_max_children_limit_status"][0];
                                        if ('1' == $children_limit_status) {
                                            $max_children = $custom["staylodgic_max_children"][0];
                                        }
                                    }
                                    if (isset($custom["staylodgic_max_guests"][0])) {
                                        $max_guests = $custom["staylodgic_max_guests"][0];
                                    }
                                    $roomOccupantData[$list->ID]['max_adults']   = $max_adults;
                                    $roomOccupantData[$list->ID]['max_children'] = $max_children;
                                    $roomOccupantData[$list->ID]['max_guests']   = $max_guests;
                                }
                            }
                        }
                    }

                    $jsonOccupants = json_encode($roomOccupantData);

                    if (!isset($meta) || $meta == "") {
                        if ($meta == 0) {
                            $meta = "0";
                        } else {
                            $meta = $field['std'];
                        }
                    }
                    echo "<div id='" . esc_attr($field['id']) . "_wrap' class='number-input occupant-" . $field['occupant'] . " occupants-range' data-room='0' data-occupant='" . $field['occupant'] . "' min='" . $field['min'] . "' max='" . $field['min'] . "' data-occupants='" . $jsonOccupants . "' >";
                    echo '<span class="minus-btn">-</span>';

                    $child_age_input = '';
                    if ('child' == $field['occupant']) {
                        error_log('----- Number of Children ');
                        error_log(print_r($meta, 1));
                        if (isset($meta['number'])) {
                            $meta_value = $meta['number'];
                        } else {
                            $meta_value = '0';
                        }
                        $name_property = $field['id'] . '[number]';

                        $child_age_input = 'data-childageinput="' . $field['id'] . '[age][]"';
                    } else {
                        $meta_value    = $meta;
                        $name_property = $field['id'];
                    }
                    echo '<input data-guest="' . $field['occupant'] . '" ' . $child_age_input . ' data-guestmax="0" data-adultmax="0" data-childmax="0" id="' . esc_attr($field['id']) . '" value="' . esc_attr($meta_value) . '" name="' . $name_property . '" type="text" class="number-value" readonly>';
                    echo '<span class="plus-btn">+</span>';
                    echo '</div>';
                    echo '<div class="occupant-' . $field['occupant'] . '-notify notify-number-over-max">Exceeds maximum</div>';
                    if ('child' == $field['occupant']) {
                        echo '<div class="child-number-max-notice">Maximum occupancy: <span class="child-number-max"></span></div>';
                        echo '<div class="combined-child-number-max-notice">Combined occupancy: <span class="combined-child-number-max"></span></div>';
                        echo '<div id="guest-age">';
                        if (isset($meta['number'])) {
                            for ($i = 0; $i < $meta['number']; $i++) {
                                $age = isset($meta['age'][$i]) ? $meta['age'][$i] : '';
                                echo "<input name='" . $field['id'] . "[age][]' type='number' data-counter='" . $i . "' value='" . $age . "' placeholder='Enter age'>";
                            }
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="adult-number-max-notice">Maximum occupancy: <span class="adult-number-max"></span></div>';
                        echo '<div class="combined-adult-number-max-notice">Combined occupancy: <span class="combined-adult-number-max"></span></div>';
                    }

                    break;

                case 'range':
                    $output = "";
                    echo '<div class="ranger-min-max-wrap-outer">';
                    if (isset($field['unit'])) {
                        echo '<div class="ranger-min-max-wrap"><span class="ranger-min-value">' . esc_attr($field['min']) . '</span>';
                        echo '<span class="ranger-max-value">' . esc_attr($field['max']) . '</span></div>';
                        echo '<div id="' . esc_attr($field['id']) . '_slider"></div>';
                        echo '<div class="ranger-bar">';
                    }
                    if (!isset($meta) || $meta == "") {
                        if ($meta == 0) {
                            $meta = "0";
                        } else {
                            $meta = $field['std'];
                        }
                    }
                    $meta = floatval($meta);
                    echo '<input id="' . esc_attr($field['id']) . '" class="of-input" name="' . esc_attr($field['id']) . '" type="text" value="' . esc_attr($meta) . '"';

                    if (isset($field['unit'])) {
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
                    echo '</div>';

                    break;

                case 'radio':
                    foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', esc_attr($field['id']), '" value="', esc_attr($option), '"', $meta == $option ? ' checked="checked"' : '', ' />', $option;
                    }
                    break;

                case 'image':
                    $output = "";
                    foreach ($field['options'] as $key => $option) {
                        $selected = '';
                        $checked  = '';
                        if ($meta == '') {
                            if (isset($field['std'])) {
                                $meta = $field['std'];
                            }
                        }
                        if ($meta != '') {
                            if ($meta == $key) {
                                $selected = ' of-radio-img-selected';
                                $checked  = ' checked="checked"';
                            }
                        }
                        echo '<input type="radio" id="' . esc_attr($field['id'] . '_' . $key) . '" class="of-radio-img-radio" value="' . esc_attr($key) . '" name="' . esc_attr($field['id']) . '" ' . esc_attr($checked) . ' />';
                        echo '<div class="of-radio-img-label">' . esc_html($key) . '</div>';
                        echo '<img data-holder="' . esc_attr($field['id'] . '_' . $key) . '" data-value="' . esc_attr($key) . '" src="' . esc_url($option) . '" alt="' . esc_attr($option) . '" class="metabox-image-radio-selector of-radio-img-img' . esc_attr($selected) . '" />';
                    }
                    break;

                case 'checkbox':
                    echo '<input type="checkbox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '"', $meta ? ' checked="checked"' : '', ' />';
                    break;
            }
        }

        $notice_class = '';
        if (isset($field['type']) && $field['type'] == "notice") {
            $notice_class = " big-notice";
        }
        if (isset($field['desc']) && '' !== $field['desc']) {
            echo '<div class="metabox-description' . esc_attr($notice_class) . '">', esc_html($field['desc']), '</div>';
        }

        if (isset($field['datatype']) && 'roomsubtotal' == $field['datatype']) {
            echo '<br/><span id="reservation-tax-generate" class="button button-primary button-small">' . __('Generate Total', 'staylodgic') . '</span>&nbsp;';
            echo '<span id="reservation-tax-exclude" class="button button-secondary button-small">' . __('Exclude Tax', 'staylodgic') . '</span><br/><br/>';
        }
        if (isset($field['datatype']) && 'activitysubtotal' == $field['datatype']) {
            echo '<br/><span id="activity-tax-generate" class="button button-primary button-small">' . __('Generate Total', 'staylodgic') . '</span>&nbsp;';
            echo '<span id="activity-tax-exclude" class="button button-secondary button-small">' . __('Exclude Tax', 'staylodgic') . '</span><br/><br/>';
        }
        echo '</div>';
    }

    if (isset($div_column_open) && $div_column_open) {
        echo '</div>';
    }

    echo '</div>';
}

/**
 * Save image ids
 */
function staylodgic_save_proofing_images()
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
        return;
    }

    if (!current_user_can('edit_posts')) {
        return;
    }

    $ids = strip_tags(rtrim($_POST['ids'], ','));
    update_post_meta($_POST['post_id'], 'staylodgic_proofing_image_ids', $ids);

    // update thumbs
    $thumbs        = explode(',', $ids);
    $thumbs_output = '';
    foreach ($thumbs as $thumb) {
        echo '<li>' . wp_get_attachment_image($thumb, 'thumbnail') . '</li>';
    }

    die();
}
add_action('wp_ajax_staylodgic_save_proofing_images', 'staylodgic_save_proofing_images');

/**
 * Save image ids
 */
function staylodgic_save_images()
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
        return;
    }

    if (!current_user_can('edit_posts')) {
        return;
    }

    $ids = strip_tags(rtrim($_POST['ids'], ','));
    update_post_meta($_POST['post_id'], 'staylodgic_image_ids', $ids);

    // update thumbs
    $thumbs        = explode(',', $ids);
    $thumbs_output = '';
    foreach ($thumbs as $thumb) {
        echo '<li>' . wp_get_attachment_image($thumb, 'thumbnail') . '</li>';
    }

    die();
}
add_action('wp_ajax_staylodgic_save_images', 'staylodgic_save_images');
/**
 * Save image ids
 */
function staylodgic_multo_gallery_save_images()
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'staylodgic-nonce-admin')) {
        return;
    }

    if (!current_user_can('edit_posts')) {
        return;
    }

    $ids       = strip_tags(rtrim($_POST['ids'], ','));
    $galleryid = $_POST['gallerysetid'];
    update_post_meta($_POST['post_id'], $galleryid, $ids);

    $getmeta = get_post_meta($_POST['post_id'], $galleryid, true);

    // update thumbs
    $thumbs        = explode(',', $ids);
    $thumbs_output = '';
    foreach ($thumbs as $thumb) {
        echo '<li>' . wp_get_attachment_image($thumb, 'thumbnail') . '</li>';
    }

    die();
}
add_action('wp_ajax_staylodgic_multo_gallery_save_images', 'staylodgic_multo_gallery_save_images');
// Save data from meta box
add_action('save_post', 'staylodgic_preProcess', 5, 3);
add_action('save_post', 'staylodgic_checkdata');
add_action('save_post', 'staylodgic_postProcess', 15, 3);

function staylodgic_preProcess($post_id, $post, $update)
{
    // Check if this is a revision or auto-save.
    if (wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    // Ensure the action is only run for the 'slgc_reservations' custom post type.
    if ($post->post_type != 'slgc_reservations') {
        return;
    }

    // Now you can safely run your custom code here.
    // For example, use post meta data
    $checkin = get_post_meta($post_id, 'staylodgic_checkin_date', true);
    $checkout = get_post_meta($post_id, 'staylodgic_checkout_date', true);
    $room_id = get_post_meta($post_id, 'staylodgic_room_id', true);

    error_log('-------- removeCache staylodgic_preProcess ---------');
    error_log($checkin . ' ++++ ' . $checkout);
    \Staylodgic\Cache::invalidate_caches_by_room_and_date($room_id, $checkin, $checkout);

    // Perform actions or operations based on the meta value.
    // For example:
    // update_post_meta($post_id, 'another_meta_key', $new_meta_value);
}
function staylodgic_postProcess($post_id, $post, $update)
{
    // Check if this is a revision or auto-save.
    if (wp_is_post_revision($post_id) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
        return;
    }

    // Ensure the action is only run for the 'slgc_reservations' custom post type.
    if ($post->post_type != 'slgc_reservations') {
        return;
    }

    // Now you can safely run your custom code here.
    // For example, use post meta data
    $checkin = get_post_meta($post_id, 'staylodgic_checkin_date', true);
    $checkout = get_post_meta($post_id, 'staylodgic_checkout_date', true);
    $room_id = get_post_meta($post_id, 'staylodgic_room_id', true);

    error_log('-------- removeCache staylodgic_postProcess ---------');
    error_log($checkin . ' ++++ ' . $checkout);
    \Staylodgic\Cache::invalidate_caches_by_room_and_date($room_id, $checkin, $checkout);

    // Perform actions or operations based on the meta value.
    // For example:
    // update_post_meta($post_id, 'another_meta_key', $new_meta_value);
}

// Hook the function to 'save_post' action.
function staylodgic_checkdata($post_id)
{

    // verify nonce
    if (isset($_POST['staylodgic_meta_box_nonce'])) {
        if (!wp_verify_nonce($_POST['staylodgic_meta_box_nonce'], 'metabox-nonce')) {
            return $post_id;
        }
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (isset($_POST['post_type'])) {
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }

    if (isset($_POST['staylodgic_meta_box_nonce'])) {
        $staylodgic_post_type_got = get_post_type($post_id);

        switch ($staylodgic_post_type_got) {
                // case 'page':
                //     $staylodgic_common_page_box = staylodgic_page_metadata();
                //     staylodgic_savedata($staylodgic_common_page_box, $post_id);
                //     break;
            case 'slgc_room':
                $staylodgic_room_box = staylodgic_room_metadata();
                staylodgic_savedata($staylodgic_room_box, $post_id);
                break;
            case 'slgc_activity':
                $staylodgic_activity_box = staylodgic_activity_metadata();
                staylodgic_savedata($staylodgic_activity_box, $post_id);
                break;
            case 'slgc_activityres':
                $staylodgic_activityres_box = staylodgic_activityres_metadata();
                staylodgic_savedata($staylodgic_activityres_box, $post_id);
                break;
            case 'slgc_guestregistry':
                $registry_box = staylodgic_registry_metadata();
                staylodgic_savedata($registry_box, $post_id);
                break;
            case 'slgc_reservations':
                $reservations_box = staylodgic_reservations_metadata();
                staylodgic_savedata($reservations_box, $post_id);
                break;
            case 'slgc_customers':
                $customers_box = staylodgic_customers_metadata();
                staylodgic_savedata($customers_box, $post_id);
                break;

            default:
                # code...
                break;
        }
    }
}

function staylodgic_savedata($staylodgic_metaboxdata, $post_id)
{

    //error_log('------ Reservation Metabox-------');
    // error_log( print_r( $_POST, 1) );
    // delete_post_meta($post_id, 'staylodgic_change_log');
    if (is_array($staylodgic_metaboxdata['fields'])) {
        foreach ($staylodgic_metaboxdata['fields'] as $field) {
            $field_id = $field['id'];
            $old = get_post_meta($post_id, $field_id, true);
            $new = isset($_POST[$field_id]) ? $_POST[$field_id] : '';

            if ('staylodgic_reservation_room_paid' == $field_id) {
                // Get the first element of the array
                $firstElement = reset($new);
                // Check if the first element is empty
                if (empty($firstElement)) {
                    // Remove the first element
                    array_shift($new);

                    foreach ($new as $record) {
                        if (!in_array($record, $old)) {
                            // Record is missing from $old, add it to the missingRecords array
                            $missingRecords[] = $record;
                        }
                    }

                    if (empty($missingRecords)) {
                        // echo "No records missing in the new array.";
                    } else {
                        $new = '';
                        $old = '';
                    }
                }
            }
            if ($new !== $old) {
                // Create or retrieve the log array
                $change_log = get_post_meta($post_id, 'staylodgic_change_log', true);
                if (!is_array($change_log)) {
                    $change_log = array();
                }
                // Create a log entry with timestamp, field ID, old value, and new value

                if ('' == $field['name']) {
                    $storage_name = $field['desc'];
                } else {
                    $storage_name = $field['name'];
                }

                $current_user = wp_get_current_user();
                $username = $current_user->user_login;

                $log_entry = array(
                    'timestamp' => current_time('mysql'),
                    'user' => $username,
                    'field_id' => $storage_name,
                    'old_value' => $old,
                    'new_value' => $new,
                );

                // Add the log entry to the change log
                $change_log[] = $log_entry;

                // Update the post meta field with the new value
                delete_post_meta($post_id, $field_id);
                update_post_meta($post_id, $field_id, $new);

                // Update the change log in the post meta
                update_post_meta($post_id, 'staylodgic_change_log', $change_log);
            }
        }
    }
}
