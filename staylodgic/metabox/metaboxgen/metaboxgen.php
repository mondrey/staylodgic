<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly
function staylodgic_generate_metaboxes( $meta_data, $post_id ) {
	// Use nonce for verification

	$the_menu_style = staylodgic_get_option_data( 'menu_type' );
	echo '<input type="hidden" name="staylodgic_meta_box_nonce" value="', esc_attr( wp_create_nonce( 'metabox-nonce' ) ), '" />';

	echo '<div class="metabox-wrapper theme-menu-style-' . esc_attr( $the_menu_style ) . ' clearfix">';
	$countcolumns = 0;
	foreach ( $meta_data['fields'] as $field ) {
		// get current post meta data
		$meta = get_post_meta( $post_id, $field['id'], true );

		if ( staylodgic_page_is_built_with_elementor( $post_id ) ) {
			$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
			if ( isset( $elementor_page_settings[ $field['id'] ] ) ) {
				$meta = $elementor_page_settings[ $field['id'] ];
			}
		}

		$class           = '';
		$trigger_element = '';
		$trigger         = '';

		$titleclass = 'is_title';
		if ( isset( $field['heading'] ) ) {
			if ( 'subhead' === $field['heading'] ) {
				$titleclass = 'is_subtitle';
			}
		}

		if ( isset( $field['group'] ) ) {
			if ( 'group' === $field['group'] ) {
				$titleclass = 'is-a-group';
			}
		}
		if ( isset( $field['group'] ) ) {
			if ( 'group-end' === $field['group'] ) {
				$titleclass = 'is-the-group-end';
			}
		}

		if ( isset( $field['class'] ) ) {
			$class = $field['class'];
		}
		if ( ! isset( $field['toggleClass'] ) ) {
			$field['toggleClass'] = '';
		}
		if ( ! isset( $field['toggleAction'] ) ) {
			$field['toggleAction'] = '';
		}
		if ( isset( $field['triggerStatus'] ) ) {
			if ( 'on' === $field['triggerStatus'] ) {
				$trigger_element = 'trigger_element';
			}

			$trigger  = "<span data-toggleClass='" . $field['toggleClass'] . "' ";
			$trigger .= "data-toggleAction='" . $field['toggleAction'] . "' ";
			$trigger .= "data-toggleID='" . $field['id'] . "' ";
			$trigger .= "data-parentclass='" . $field['class'] . "' ";
			$trigger .= '></span>';
		}

		if ( 'nobreak' === $field['type'] ) {
			$titleclass     .= ' is_nobreak';
			$div_column_open = true;
		}
		if ( 'break' === $field['type'] ) {
			$titleclass .= ' is_break';
			if ( $countcolumns > 0 ) {
				if ( $div_is_open ) {
					echo '</div>';
				}
			}
			++$countcolumns;
			echo '<div class="metabox-column">';
			$div_column_open = true;
		}
		$div_is_open          = true;
		$trigger_allowed_html = array(
			'span' => array(
				'data-toggleClass'  => array(),
				'data-toggleAction' => array(),
				'data-toggleID'     => array(),
				'data-parentclass'  => array(),
			),
		);
		echo '<div class="metabox-fields metaboxtype_', esc_attr( $field['type'] ), ' ' . esc_attr( $class ) . ' ' . esc_attr( $titleclass ) . ' ' . esc_attr( $trigger_element ) . '">',
		wp_kses( $trigger, staylodgic_get_booking_allowed_tags() ),
		'<div class="metabox_label"><label for="', esc_attr( $field['id'] ), '"></label></div>';
		if ( isset( $field['type'] ) ) {

			if ( 'break' !== $field['type'] ) {
				if ( '' !== $field['name'] ) {
					echo '<div id="' . esc_attr( $field['id'] ) . '-section-title" class="sectiontitle clearfix">' . esc_attr( $field['name'] ) . '</div>';
				}
			}

			switch ( $field['type'] ) {

				case 'image_gallery':
					// SPECIAL CASE:
					// std controls button text; unique meta key for image uploads
					$meta          = get_post_meta( $post_id, 'staylodgic_image_ids', true );
					$thumbs_output = '';
					$button_text   = ( ! empty( $meta ) ) ? esc_html__( 'Edit Gallery', 'staylodgic' ) : esc_html( $field['std'] );
					$renew_meta    = '';

					if ( ! empty( $meta ) ) {
						$field['std']  = esc_html__( 'Edit Gallery', 'staylodgic' );
						$thumbs        = explode( ',', $meta );
						$thumbs_output = '';
						$imageidcount  = 0;

						foreach ( $thumbs as $thumb ) {
							if ( wp_attachment_is_image( $thumb ) ) {
								$got_attached_image = wp_get_attachment_image( $thumb, 'thumbnail' );

								if ( isset( $got_attached_image ) && '' !== $got_attached_image ) {
									if ( 0 < $imageidcount ) {
										$renew_meta .= ',';
									}
									++$imageidcount;

									$thumbs_output .= '<li data-thumbnailimageid="' . esc_attr( $thumb ) . '">' . $got_attached_image . '</li>';
									$renew_meta    .= $thumb;
								}
							}
						}
					}

					echo '<td>
						<input type="button" class="button" name="' . esc_attr( $field['id'] ) . '" id="staylodgic_images_upload" value="' . esc_attr( $button_text ) . '" />
						<input type="hidden" name="staylodgic_meta[staylodgic_image_ids]" id="staylodgic_image_ids" value="' . esc_attr( ! empty( $renew_meta ) ? $renew_meta : 'false' ) . '" />
						<ul class="mtheme-gallery-thumbs">' . wp_kses_post( $thumbs_output ) . '</ul>
					</td>';

					break;

				case 'multi_upload':
					// SPECIAL CASE:
					// std controls button text; unique meta key for image uploads
					$meta          = get_post_meta( $post_id, esc_attr( $field['id'] ), true );
					$thumbs_output = '';
					$button_text   = ( ! empty( $meta ) ) ? esc_html__( 'Edit Gallery', 'staylodgic' ) : esc_html( $field['std'] );

					if ( ! empty( $meta ) ) {
						$field['std']  = esc_html__( 'Edit Gallery', 'staylodgic' );
						$thumbs        = explode( ',', $meta );
						$thumbs_output = '';

						foreach ( $thumbs as $thumb ) {
							$thumbs_output .= '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
						}
					}

					echo '<td>
							<input type="button" data-galleryid="' . esc_attr( $field['id'] ) . '" data-imageset="' . esc_attr( $meta ) . '" class="button meta-multi-upload" name="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $button_text ) . '" />
					
							<input type="hidden" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( ! empty( $meta ) ? $meta : 'false' ) . '" />
					
							<ul class="mtheme-multi-thumbs multi-gallery-' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $thumbs_output ) . '</ul>
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
							'orderby'        => 'menu_order',
						)
					);
					if ( $images ) {
						foreach ( $images as $id => $image ) {
							$attachment_id       = $image->ID;
							$imagearray          = wp_get_attachment_image_src( $attachment_id, 'thumbnail', false );
							$image_uri           = $imagearray[0];
							$attachment_image_id = get_post( $attachment_id );
							$image_title         = $image->post_title;
							echo '<img src="' . esc_url( $image_uri ) . '" alt="' . esc_attr__( 'image', 'staylodgic' ) . '" />';
						}
					} else {
						echo esc_html__( 'No images found.', 'staylodgic' );
					}
					break;

				case 'seperator':
					echo '<hr/>';

					if ( isset( $field['action'] ) && 'display_choices_for_customer' === $field['action'] ) {
						echo '<a class="choice-customer-existing">' . esc_html__( 'Or choose an existing customer', 'staylodgic' ) . '</a>';
					}

					break;

					// Color picker
				case 'color':
					$default_color = '';
					if ( isset( $value['std'] ) ) {
						if ( $val !== $value['std'] ) {
							$default_color = ' data-default-color="' . esc_attr( $value['std'] ) . '" ';
						}
					}
					$color_value = $meta ? $meta : $field['std'];
					echo '<input data-alpha-enabled="true" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="colorswatch of-color"  type="text" value="' . esc_attr( $color_value ) . '" />';

					break;

				case 'upload':
					if ( '' !== $meta ) {
						$image_url_id         = staylodgic_get_image_id_from_url( $meta );
						$image_thumbnail_data = wp_get_attachment_image_src( $image_url_id, 'thumbnail', true );
						$image_thumbnail_url  = $image_thumbnail_data[0];
						if ( $image_thumbnail_url ) {
							echo '<img height="100px" src="' . esc_url( $image_thumbnail_url ) . '" />';
						}
					}
					echo '<div>';
					$upload_value = $meta ? $meta : $field['std'];
					echo '<input type="text" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $upload_value ) . '" size="30" />';
					echo '<button class="button-shortcodegen-uploader" data-id="' . esc_attr( $field['id'] ) . '" value="Upload">Upload</button>';
					echo '</div>';
					break;

				case 'disabled':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" disabled />';
					echo '<input type="hidden" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';

					break;

				case 'staylodgic_registration_data':
					$registration_instance = new \Staylodgic\Guest_Registry();
					$registration_instance->display_registration();

					break;

				case 'readonly':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input readonly type="text" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					break;

				case 'taxgenerate':
					$the_post_id    = $field['page_id'];
					$tax_gen_status = get_post_meta( $the_post_id, 'staylodgic_tax', true );
					$tax_gen_html   = get_post_meta( $the_post_id, 'staylodgic_tax_html_data', true );
					$tax_gen_data   = get_post_meta( $the_post_id, 'staylodgic_tax_data', true );

					echo '<div id="input-tax-summary">';
					echo '<div class="input-tax-summary-wrap">';
					if ( 'enabled' === $tax_gen_status ) {
						echo '<div class="input-tax-summary-wrap-inner">';
						$allowed_tax_gen_html = array(
							'html' => array(),
							'body' => array(),
							'div'  => array(
								'class' => array(),
							),
							'span' => array(
								'class'         => array(),
								'date-price'    => array(),
								'date-currency' => array(),
								'data-number'   => array(),
								'data-type'     => array(),
								'data-duration' => array(),
							),
						);
						echo wp_kses( $tax_gen_html, $allowed_tax_gen_html );
						echo '</div>';
					}
					if ( 'excluded' === $tax_gen_status ) {
						echo '<div class="input-tax-summary-wrap-inner">';
						echo 'Tax Exluded';
						echo '</div>';
					}
					echo '</div>';
					echo '</div>';
					break;

				case 'activity_list_generate':
					$the_post_id = $field['page_id'];
					$activity    = new Staylodgic\Activity();
					$activity->get_activities( $the_post_id );

					break;

				case 'offview':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="hidden" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					break;

				case 'offview_display_ticket_result':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="hidden" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';

					if ( isset( $text_value ) && '' !== $text_value ) {
						$activity = new Staylodgic\Activity();
						$ticket   = $activity->display_ticket( $field['page_id'], $text_value );

						echo wp_kses( $ticket, staylodgic_get_allowed_tags() );

						echo '<div class="ticket-save-pdf-button">';
						echo '<button data-file="registration-' . esc_attr( $field['page_id'] ) . '" data-id="' . esc_attr( $field['page_id'] ) . '" id="save-pdf-ticket-button" class="save-pdf-ticket-button button button-primary button-large">Save PDF</button>';
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
					echo '<input type="text" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					break;

				case 'number':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="number" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="5" />';
					break;

				case 'registration':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';

					if ( $text_value ) {
						$registry_instance = new \Staylodgic\Guest_Registry();
						$res_reg_ids       = $registry_instance->fetch_res_reg_ids_by_booking_number( $text_value );
						if ( isset( $res_reg_ids['stay_reservation_id'] ) && $res_reg_ids['guest_register_id'] ) {
							$guest_registry = $registry_instance->output_registration_and_occupancy( $res_reg_ids['stay_reservation_id'], $res_reg_ids['guest_register_id'], 'text' );

							echo wp_kses( $guest_registry, staylodgic_get_allowed_tags() );

						} else {
							echo '<div class="registration-notice booking-number-not-found">';
							echo esc_html__( 'Booking number not found.', 'staylodgic' );
							echo '</div>';
						}
					}

					break;

				case 'currency':
					$text_value = $meta ? $meta : $field['std'];

					echo '<input type="number" ';
					if ( isset( $field['datatype'] ) ) {
						echo 'data-priceof="' . esc_attr( $field['datatype'] ) . '"';
					}
					$readonly = '';
					if ( isset( $field['inputis'] ) ) {
						echo ' readonly';
					}
					echo ' data-currencyformat="2" class="' . esc_attr( $class ) . ' currency-input" min="0" step="0.01" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					break;

				case 'currencyarray':
					$date_time  = gmdate( 'Y-m-d H:i:s' );
					$text_value = $meta ? $meta : $field['std'];

					echo '<input type="number" ';
					if ( isset( $field['datatype'] ) ) {
						echo 'data-priceof="' . esc_attr( $field['datatype'] ) . '"';
					}
					$readonly = '';
					if ( isset( $field['inputis'] ) ) {
						echo ' readonly';
					}
					echo ' data-currencyformat="2" class="' . esc_attr( $class ) . ' currency-input" min="0" step="0.01" name="staylodgic_reservation_room_paid[' . esc_attr( $date_time ) . ']" id="', esc_attr( $field['id'] ), '" value="" size="30" />';

					$woo_payment_order_id       = get_post_meta( $post_id, 'staylodgic_woo_order_id', true );

					$payments       = get_post_meta( $post_id, 'staylodgic_reservation_room_paid', true );
					$total_cost     = get_post_meta( $post_id, 'staylodgic_reservation_total_room_cost', true );
					$total_payments = 0;
					if ( is_array( $payments ) && ! empty( $payments ) ) {
						echo '<ul class="metabox-payments">';
						foreach ( $payments as $timestamp => $value ) {
							if ( isset( $value ) && '' !== $value ) {
								$payment_id = 'payment-' . sanitize_title( $timestamp );
								echo '<li class="' . esc_attr( $payment_id ) . '">';
								echo '<div class="payment-date-lister">';
								echo wp_kses( staylodgic_price( $value ), staylodgic_get_allowed_tags() );
								echo ' [<span class="remove-payment" data-timestamp="$timestamp" data-index="$index">remove</span>] ' . esc_html( $timestamp );
								echo '<input type="hidden" name="staylodgic_reservation_room_paid[' . esc_attr( $timestamp ) . ']" value="' . esc_attr( $value ) . '" size="30" />';
								echo '</div>';
								echo '</li>';

								$total_payments = $total_payments + $value;
							}
						}
						echo '<li>';
						echo '<p class="reservation-payment-balance"><strong>' . esc_html__( 'Balance', 'staylodgic' ) . '</strong></p>';
						$balance     = intval( $total_cost ) - intval( $total_payments );
						$payment_set = staylodgic_price( $balance );
						echo wp_kses( $payment_set, staylodgic_get_price_allowed_tags() );
						echo '</li>';
						echo '</ul>';
					}

					if ( $order = wc_get_order( $woo_payment_order_id ) ) {
						echo '<div class="woo-stay-payment">';
						$order_number = $order->get_order_number();
						$order_link   = admin_url( 'post.php?post=' . $woo_payment_order_id . '&action=edit' );
						echo esc_html__( 'This reservation was linked to a payment order. You can view the full order details here: ','staylodgic' );
						echo '<div class="woo-stay-payment-link">';
						echo '<a href="' . esc_url( $order_link ) . '" target="_blank">';
						echo 'Order #' . esc_html( $order_number );
						echo '</a>';
						echo '</div>';
						echo '</div>';
					}

					break;

				case 'switch':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="switch-toggle">';
					echo '<input type="hidden" class="meta-switch-toggle ' . esc_attr( $class ) . '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					echo '<div class="switch-toggle-slide"><div class="switch-inner"></div></div>';
					echo '</div>';
					break;

				case 'bedsetup_set':
					$text_value = $meta ? $meta : $field['std'];
					// Set the HTML code for the new bed setup

					$data = array();
					$data = $meta;

					if ( isset( $field['target'] ) ) {
						$field['options'] = staylodgic_get_select_target_options( $field['target'] );
					}

					if ( isset( $data ) && is_array( $data ) ) {
						$repeat_count = 0;
						foreach ( $data as $unique_id => $values ) {
							echo '<div class="bed-setup-dynamic-container" data-unique-id="' . esc_attr( $unique_id ) . '">';
							echo '<h3>Bed Layout</h3>';
							echo '<div class="bedlayout-wrap" data-repeat="staylodgic_alt_bedsetup_${unique_id}">';
							echo '<div class="bedlayout">';
							if ( isset( $values['bedtype'] ) && isset( $values['bednumber'] ) ) {
								foreach ( $values['bedtype'] as $index => $bedtype ) {
									$bednumber = $values['bednumber'][ $index ];

									if ( ! empty( $bedtype ) ) {
										$found_data = true;
										$age        = '';
										$class      = '';
										$field_id   = 'field_id'; // Replace with your actual field ID

										echo '<div class="bedlayout-box" id="bedlayout-box-' . esc_attr( $unique_id ) . '">';
										echo '<div class="selectbox-type-selector"><select class="bedtype-select" name="staylodgic_alt_bedsetup[' . esc_attr( $unique_id ) . '][bedtype][]" id="bed_type_' . esc_attr( $field_id ) . '_' . esc_attr( $repeat_count ) . '">';

										foreach ( $field['options'] as $key => $option ) {
											echo '<option value="' . esc_attr( $key ) . '"', $bedtype === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
										}

										echo '</select>';
										echo ' X <input placeholder="0" min="0" type="number" name="staylodgic_alt_bedsetup[' . esc_attr( $unique_id ) . '][bednumber][]" value="' . esc_attr( $bednumber ) . '" id="bed_number' . esc_attr( $repeat_count ) . '" />';

										echo '<div class="remove-bedlayout">Remove</div>';
										echo '</div>';

										echo '</div>';
									}

									++$repeat_count;
								}
							}
							echo '</div>';
							echo '</div>';
							echo '</div>';
						}
					}

					echo '
                        <div id="bed_setup_container" class="bed-setup-container-template">
                        <div class="metabox_label"><label for="staylodgic_alt_bedsetup_${unique_id}"></label></div>
                        <div id="staylodgic_alt_bedsetup_${unique_id}-section-title" class="sectiontitle clearfix">Set single or multiple beds</div>
                        <div class="bedlayout-wrap" data-repeat="staylodgic_alt_bedsetup_${unique_id}">
                        <div class="bedlayout">
                            <div class="bedlayout-box" id="bedlayout-box">
                                <select disabled class="bedtype-select" name="staylodgic_alt_bedsetup[${unique_id}][bedtype][]" id="bed_type_staylodgic_alt_bedsetup_${unique_id}_0">
                                <option value="twinbed">Twin bed</option>
                                <option value="fullbed">Full bed</option>
                                <option value="queenbed">Queen bed</option>
                                <option value="kingbed">King bed</option>
                                <option value="bunkbed">Bunk bed</option>
                                <option value="sofabed">Sofa bed</option>
                                </select> X
                                <input disabled placeholder="0" type="number" min="0" name="staylodgic_alt_bedsetup[${unique_id}][bednumber][]" value="" id="bed_number${unique_id}_0">
                            </div>
                        </div>
                        <span class="add-bedlayout-box">Add beds</span>
                        <span class="add-bedlayout-box-notice">Max Reached!</span>
                        </div>
                        <div class="metabox-description">Add bed layouts to the room.</div>
                        </div>
                    ';
					// add an input button
					echo '<div id="bed-inputs-container"></div>';
					echo '<span id="add-bed-setup-button" class="add-bedlayout-box">Add bed choices</span>';

					break;

				case 'bedsetup_repeat':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="bedlayout-wrap" data-repeat="' . esc_attr( $field['id'] ) . '">';
					echo '<div class="bedlayout">';
					$repeat_count = 0;
					$found_data   = false;
					if ( isset( $meta ) && is_array( $meta ) ) {
						foreach ( $meta['bedtype'] as $value ) {
							if ( isset( $value ) && '' !== $value ) {
								$found_data = true;
								$age        = '';
								if ( isset( $meta['bedtype'][ $repeat_count ] ) ) {
									$bedtype = $meta['bedtype'][ $repeat_count ];
								}
								if ( isset( $meta['bednumber'][ $repeat_count ] ) ) {
									$bednumber = $meta['bednumber'][ $repeat_count ];
								}
								$class = '';
								if ( isset( $field['target'] ) ) {
									$field['options'] = staylodgic_get_select_target_options( $field['target'] );
								}
								echo '<div class="bedlayout-box" id="bedlayout-box">';
								echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox bedtype-select" name="', esc_attr( $field['id'] ) . '[bedtype][]" id="bed_type_' . esc_attr( $field['id'] ) . '_' . esc_attr( $repeat_count ) . '">';
								foreach ( $field['options'] as $key => $option ) {
									if ( 0 === (int) $key ) {
										$key = __( 'All the items', 'staylodgic' );
									}
									echo '<option value="' . esc_attr( $key ) . '"', $bedtype === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
								}
								echo '</select>';
								echo ' X <input placeholder="0" min="0" type="number" name="' . esc_attr( $field['id'] ) . '[bednumber][]" value="' . esc_attr( $bednumber ) . '" id="bed_number' . esc_attr( $repeat_count ) . '" /></div>';
								if ( $repeat_count > 0 ) {
									echo '<div class="remove-bedlayout">Remove</div>';
								}
								echo '</div>';
							}
							++$repeat_count;
						}
					}
					if ( ! $found_data ) {
						if ( isset( $field['target'] ) ) {
							$field['options'] = staylodgic_get_select_target_options( $field['target'] );
						}
						echo '<div class="bedlayout-box" id="bedlayout-box">';
						echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr( $field['id'] ) . '[bedtype][]" id="bed_type_' . esc_attr( $field['id'] ) . '_0">';
						foreach ( $field['options'] as $key => $option ) {
							if ( 0 === (int) $key ) {
								$key = __( 'All the items', 'staylodgic' );
							}
							echo '<option value="' . esc_attr( $key ) . '"', $meta === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
						}
						echo '</select>';
						echo ' X <input placeholder="How many" min="0" type="number" name="' . esc_attr( $field['id'] ) . '[bednumber][]" value="" id="bed_number0" /></div>';
						echo '</div>';
					}
					echo '</div>';
					echo '<span class="add-bedlayout-box">' . esc_html__( 'Add bed type', 'staylodgic' ) . '</span>';
					echo '<span class="add-bedlayout-box-notice">' . esc_html__( 'Max Reached!', 'staylodgic' ) . '</span>';
					echo '</div>';
					break;

				case 'taxsetup_repeat':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="taxlayout-wrap" data-repeat="' . esc_attr( $field['id'] ) . '">';
					echo '<div class="taxlayout">';
					$repeat_count = 0;
					$taxlabel     = '';
					$taxtype      = '';
					$taxnumber    = '';
					$found_data   = false;

					if ( isset( $meta ) && is_array( $meta ) ) {
						if ( isset( $meta['taxnumber'] ) ) {
							foreach ( $meta['taxnumber'] as $value ) {
								if ( isset( $value ) && '' !== $value ) {
									$found_data = true;

									$age = '';
									if ( isset( $meta['taxtype'][ $repeat_count ] ) ) {
										$taxtype = $meta['taxtype'][ $repeat_count ];
									}
									if ( isset( $meta['taxnumber'][ $repeat_count ] ) ) {
										$taxnumber = $meta['taxnumber'][ $repeat_count ];
									}
									if ( isset( $meta['taxlabel'][ $repeat_count ] ) ) {
										$taxlabel = $meta['taxlabel'][ $repeat_count ];
									}
									$class = '';
									echo '<div class="taxlayout-box" id="taxlayout-box">';
									echo '<input placeholder="Label" type="text" name="' . esc_attr( $field['id'] ) . '[taxlabel][]" value="' . esc_attr( $taxlabel ) . '" id="tax_label' . esc_attr( $repeat_count ) . '" />';
									echo '<div class="selectbox-type-selector">';
									if ( isset( $field['choice'] ) && '' === $field['choice'] ) {
										echo '<select class="chosen-select-metabox taxtype-select" name="', esc_attr( $field['id'] ) . '[taxtype][]" id="tax_type_' . esc_attr( $field['id'] ) . '_' . esc_attr( $repeat_count ) . '">';
										foreach ( $field['options'] as $key => $option ) {
											if ( '0' === $key ) {
												$key = __( 'All the items', 'staylodgic' );
											}
											echo '<option value="' . esc_attr( $key ) . '"', $taxtype === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
										}
										echo '</select> X ';
									}
									echo '<input placeholder="0" type="text" name="' . esc_attr( $field['id'] ) . '[taxnumber][]" value="' . esc_attr( $taxnumber ) . '" id="tax_number' . esc_attr( $repeat_count ) . '" /></div>';
									if ( $repeat_count > 0 ) {
										echo '<div class="remove-taxlayout">Remove</div>';
									}
									echo '</div>';
								}
								++$repeat_count;
							}
						}
					}
					if ( ! $found_data ) {
						echo '<div class="taxlayout-box" id="taxlayout-box">';
						echo '<input placeholder="Label" type="text" name="' . esc_attr( $field['id'] ) . '[taxlabel][]" value="" id="tax_label0" />';
						echo '<div class="selectbox-type-selector">';
						if ( isset( $field['choice'] ) && '' === $field['choice'] ) {
							echo '<select class="chosen-select-metabox" name="', esc_attr( $field['id'] ) . '[taxtype][]" id="tax_type_' . esc_attr( $field['id'] ) . '_0">';
							foreach ( $field['options'] as $key => $option ) {
								echo '<option value="' . esc_attr( $key ) . '"', $meta === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
							}

							echo '</select> X ';
						}
						echo '<input placeholder="Value% or Value" type="text" name="' . esc_attr( $field['id'] ) . '[taxnumber][]" value="" id="tax_number0" /></div>';
						echo '</div>';
					}
					echo '</div>';
					echo '<span class="add-taxlayout-box">' . esc_html__( 'Add layout', 'staylodgic' ) . '</span>';
					echo '<span class="add-taxlayout-box-notice">' . esc_html__( 'Max Reached!', 'staylodgic' ) . '</span>';
					echo '</div>';
					break;

				case 'repeat_text':
					$text_value = $meta ? $meta : $field['std'];
					echo '<div class="movethis-wrap" data-repeat="' . esc_attr( $field['id'] ) . '">';
					echo '<div class="movethis">';

					$repeat_count = 0;
					$found_data   = false;
					if ( isset( $meta ) && is_array( $meta ) ) {
						foreach ( $meta['age'] as $value ) {
							if ( isset( $value ) && '' !== $value ) {
								$found_data = true;
								$age        = '';
								if ( isset( $meta['age'][ $repeat_count ] ) ) {
									$age = $meta['age'][ $repeat_count ];
								}
								echo '<div class="text-box" id="text-box">';
								echo '<input placeholder="' . esc_attr__( 'Age', 'staylodgic' ) . '" type="text" name="' . esc_attr( $field['id'] ) . '[age][]" value="' . esc_attr( $age ) . '" id="box_size' . esc_attr( $repeat_count ) . '" />';
								if ( $repeat_count > 0 ) {
									echo '<span class="remove-box">' . esc_html__( 'Remove', 'staylodgic' ) . '</span>';
								}
								echo '</div>';
							}
							++$repeat_count;
						}
					}
					if ( ! $found_data ) {
						echo '<div class="text-box" id="text-box">';
						echo '</div>';
					}
					echo '</div>';
					echo '<span class="add-box">' . esc_html__( 'Add Child', 'staylodgic' ) . '</span>';
					echo '<span class="add-box-notice">' . esc_html__( 'Max Reached!', 'staylodgic' ) . '</span>';
					echo '</div>';
					break;

				case 'actvity_schedule':
					$text_value = $meta ? $meta : $field['std'];

					// Set the HTML code for the event schedule
					echo '<div id="event_schedule_container" class="event-schedule-container-template">
                            <div class="metabox_label"><label for="staylodgic_activity_schedule_${day}"></label></div>
                            <div class="schedule-wrap" data-repeat="staylodgic_activity_schedule_${day}">';

					// Loop through each day of the week
					$days_of_week = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
					foreach ( $days_of_week as $day ) {
						$day_lower = strtolower( $day );
						echo '
                            <div class="day-schedule" id="day_schedule_' . esc_attr( $day_lower ) . '">
                                <div class="day-title">' . esc_html( $day ) . '</div>
                                <div class="time-inputs">';

						// Inside the loop where input fields are created
						if ( ! empty( $text_value[ $day_lower ] ) ) {
							foreach ( $text_value[ $day_lower ] as $time ) {
								echo '
                                        <div class="time-input-wrapper">
                                            <input type="time" name="staylodgic_activity_schedule[' . esc_attr( $day_lower ) . '][]" value="' . esc_attr( $time ) . '">
                                            <span class="remove-time-input"><i class="dashicons dashicons-remove"></i></span>
                                        </div>';
							}
						} else {
							// If no saved times, add an empty input field with a remove button
							echo '
                                    <div class="time-input-wrapper">
                                        <input type="time" name="staylodgic_activity_schedule[' . esc_attr( $day_lower ) . '][]" value="">
                                        <span class="remove-time-input"><i class="dashicons dashicons-remove"></i></span>
                                    </div>';
						}

						echo '
                                </div>
                                <span class="add-time-input">Add Time</span>
                            </div>';
					}

					echo '
                            </div>
                        </div>
                    ';

					break;

				case 'country':
					$text_value = $meta ? $meta : $field['std'];
					echo '<select class="chosen-select-metabox" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '">';
					$country_list = staylodgic_country_list( 'select', $meta );
					echo wp_kses( $country_list, staylodgic_get_allowed_tags() );
					echo '</select>';

					break;
				case 'datepicker':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="text" class="' . esc_attr( $class ) . ' datepicker" data-enable-time="true" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					break;
				case 'hidden':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input type="hidden" name="', esc_attr( $field['id'] ), '_hidden" id="', esc_attr( $field['id'] ), '_hidden" value="' . esc_attr( $text_value ) . '" />';
					break;
				case 'activity_reservation':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input data-postid="' . esc_attr( get_the_id() ) . '" type="text" class="' . esc_attr( $class ) . ' activity-reservation" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					echo '<div id="activity-reservation-details"></div>';
					break;
				case 'reservation':
					$text_value = $meta ? $meta : $field['std'];
					echo '<input data-postid="' . esc_attr( get_the_id() ) . '" type="text" class="' . esc_attr( $class ) . ' reservation" data-enable-time="true" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" value="' . esc_attr( $text_value ) . '" size="30" />';
					echo '<div id="reservation-details"></div>';
					break;
				case 'textarea':
					$textarea_value = $meta ? $meta : $field['std'];
					echo '<textarea name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" cols="60" rows="4" >' . esc_textarea( $textarea_value ) . '</textarea>';
					break;
				case 'wpeditor':
					$textarea_value = $meta ? $meta : $field['std'];
					$editor_id      = esc_attr( $field['id'] );
					$settings       = array(
						'textarea_name' => esc_attr( $field['id'] ),
						'media_buttons' => false, // Set to true if you want to allow media uploads
						'textarea_rows' => 10, // Number of rows for the editor
						'teeny'         => true, // Use teeny editor if you want a simplified version
					);
					wp_editor( $textarea_value, $editor_id, $settings );
					break;
				case 'select':
					$class = '';
					if ( isset( $field['target'] ) ) {
						$field['options'] = staylodgic_get_select_target_options( $field['target'] );
					}
					echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox choice-', esc_attr( $field['id'] ), '" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '">';
					foreach ( $field['options'] as $key => $option ) {
						if ( '0' === $key ) {
							$key = __( 'All the items', 'staylodgic' );
						}
						echo '<option value="' . esc_attr( $key ) . '"' . ( strval( $meta ) === strval( $key ) ? ' selected' : '' ) . '>' . esc_html( $option ) . '</option>';
					}
					echo '</select></div>';

					break;

				case 'changelog':
					$post_id = get_the_id();

					// Retrieve the change log for the post
					$change_log = get_post_meta( $post_id, 'staylodgic_change_log', true );

					if ( is_array( $change_log ) && ! empty( $change_log ) ) {

						$reversed_change_log = array_reverse( $change_log );

						echo '<div class="settings-change-log">';
						if ( is_array( $reversed_change_log ) && ! empty( $reversed_change_log ) ) {
							echo '<ol>';
							foreach ( $reversed_change_log as $change ) {
								echo '<li>';
								echo '<strong>' . esc_html( $change['field_id'] ) . '</strong> changed by ' . esc_html( $change['user'] ) . ' on ' . esc_html( $change['timestamp'] ) . '<br>';
								// Format old and new values using the format_value function
								echo '<strong>Old Value:</strong> ' . wp_kses( staylodgic_format_value( $change['old_value'] ), staylodgic_get_allowed_tags() ) . '<hr/>';
								echo '<strong>New Value:</strong> ' . wp_kses( staylodgic_format_value( $change['new_value'] ), staylodgic_get_allowed_tags() ) . '<hr/>';
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
					$meal_plans = staylodgic_get_option( 'mealplan' );

					if ( is_array( $meal_plans ) && count( $meal_plans ) > 0 ) {
						$included_meal_plans = array();
						$optional_meal_plans = array();

						foreach ( $meal_plans as $id => $plan ) {
							if ( 'included' === $plan['choice'] ) {
								$included_meal_plans[ $id ] = $plan;
							} elseif ( 'optional' === $plan['choice'] ) {
								$optional_meal_plans[ $id ] = $plan;
							}
						}

						$html_input = '';

						echo '<div class="room-included-meals">';
						if ( is_array( $included_meal_plans ) && count( $included_meal_plans ) > 0 ) {
							foreach ( $included_meal_plans as $id => $plan ) {
								if ( isset( $plan['mealtype'] ) ) {
									$html_input .= staylodgic_get_mealplan_labels( $plan['mealtype'] ) . __( ' included. ', 'staylodgic' );
								}
							}
						}
						$textarea_value = $meta ? $meta : $html_input;
						echo '<textarea name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '" cols="60" rows="4" >' . esc_textarea( $textarea_value ) . '</textarea>';

						echo '</div>';

					}

					break;

				case 'bedlayout':
					$the_post_id = get_the_ID(); // Replace this with the actual post ID
					$room_id     = get_post_meta( $the_post_id, 'staylodgic_room_id', true );

					$booking_instance  = new \Staylodgic\Booking();
					$bed_layout_inputs = $booking_instance->generate_bed_metabox( $room_id, $field['id'], $meta );

					echo '<div id="metabox-bedlayout" data-field="' . esc_attr( $field['id'] ) . '" data-metavalue="' . esc_attr( $meta ) . '">';
					echo wp_kses( $bed_layout_inputs, staylodgic_get_bedlayout_allowed_tags() );

					echo '</div>';

					break;

				case 'mealplan':
					$meal_plans = staylodgic_get_option( 'mealplan' );

					if ( is_array( $meal_plans ) && count( $meal_plans ) > 0 ) {
						$included_meal_plans = array();
						$optional_meal_plans = array();

						foreach ( $meal_plans as $id => $plan ) {
							if ( 'included' === $plan['choice'] ) {
								$included_meal_plans[ $id ] = $plan;
							} elseif ( 'optional' === $plan['choice'] ) {
								$optional_meal_plans[ $id ] = $plan;
							}
						}

						echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '">';
						echo '<option value="none"', 'none' === $meta ? ' selected' : '', '>None</option>';
						foreach ( $optional_meal_plans as $key => $option ) {
							echo '<option value="' . esc_attr( $option['mealtype'] ) . '"', $meta === $option['mealtype'] ? ' selected' : '', '>' . esc_html( staylodgic_get_mealplan_labels( $option['mealtype'] ) ) . '</option>';
						}
						echo '</select></div>';
					}

					break;

				case 'payments':
					$class = '';
					if ( isset( $field['target'] ) ) {
						$field['options'] = staylodgic_get_select_target_options( $field['target'] );
					}
					echo '<div class="selectbox-type-selector"><select class="chosen-select-metabox" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '">';
					foreach ( $field['options'] as $key => $option ) {
						if ( '0' === $key ) {
							$key = __( 'All the items', 'staylodgic' );
						}
						echo '<option value="' . esc_attr( $key ) . '"', $meta === $key ? ' selected' : '', '>', esc_attr( $option ), '</option>';
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

					$room_occupant_data = array();

					if ( isset( $field['datafrom'] ) ) {
						if ( 'roomtype' === $field['datafrom'] ) {
							$room = get_posts( 'post_type=staylodgic_rooms&numberposts=-1&order=ASC' );
							if ( $room ) {
								foreach ( $room as $key => $list ) {
									$custom = get_post_custom( $list->ID );
									if ( isset( $custom['staylodgic_max_adult_limit_status'][0] ) ) {
										$adult_limit_status = $custom['staylodgic_max_adult_limit_status'][0];
										if ( '1' === $adult_limit_status ) {
											$max_adults = $custom['staylodgic_max_adults'][0];
										}
									}
									if ( isset( $custom['staylodgic_max_children_limit_status'][0] ) ) {
										$children_limit_status = $custom['staylodgic_max_children_limit_status'][0];
										if ( '1' === $children_limit_status ) {
											$max_children = $custom['staylodgic_max_children'][0];
										}
									}
									if ( isset( $custom['staylodgic_max_guests'][0] ) ) {
										$max_guests = $custom['staylodgic_max_guests'][0];
									}
									$room_occupant_data[ $list->ID ]['max_adults']   = $max_adults;
									$room_occupant_data[ $list->ID ]['max_children'] = $max_children;
									$room_occupant_data[ $list->ID ]['max_guests']   = $max_guests;
								}
							}
						}
					}
					if ( isset( $field['unit'] ) ) {
						$json_occupants = wp_json_encode( $room_occupant_data );
						echo "<div class='occupant-" . esc_attr( $field['occupant'] ) . " occupants-range ranger-min-max-wrap' data-room='' data-occupant='" . esc_attr( $field['occupant'] ) . "' data-occupants='" . esc_attr( $json_occupants ) . "'><span class='ranger-min-value'>" . esc_attr( $field['min'] ) . '</span>';
						echo '<span class="ranger-max-value">' . esc_attr( $field['max'] ) . '</span></div>';
						echo '<div id="' . esc_attr( $field['id'] ) . '_slider"></div>';
						echo '<div class="ranger-bar">';
					}
					if ( ! isset( $meta ) || '' === $meta ) {
						if ( 0 === (int) $meta ) {
							$meta = '0';
						} else {
							$meta = $field['std'];
						}
					}
					$meta = floatval( $meta );
					echo '<input id="' . esc_attr( $field['id'] ) . '" class="of-input input-occupant input-occupant-' . esc_attr( $field['occupant'] ) . '" name="' . esc_attr( $field['id'] ) . '" type="text" value="' . esc_attr( $meta ) . '"';

					if ( isset( $field['unit'] ) ) {
						if ( isset( $field['min'] ) ) {
							echo ' min="' . esc_attr( $field['min'] );
						}
						if ( isset( $field['max'] ) ) {
							echo '" max="' . esc_attr( $field['max'] );
						}
						if ( isset( $field['step'] ) ) {
							echo '" step="' . esc_attr( $field['step'] );
						}
						echo '" />';
						if ( isset( $field['unit'] ) ) {
							echo '<span>' . esc_attr( $field['unit'] ) . '</span>';
						}
						echo '</div>';
					} else {
						echo ' />';
					}

					break;

				case 'reservation_for_customer':
					$reservation_instance = new \Staylodgic\Reservations();
					$reservation_array    = \Staylodgic\Reservations::get_reservation_ids_for_customer( $field['customer_id'] );
					$bookings             = $reservation_instance->get_edit_links_for_reservations( $reservation_array );

					$activity_instance = new \Staylodgic\Activity();
					$activity_array    = \Staylodgic\Activity::get_activity_ids_for_customer( $field['customer_id'] );
					$activities        = $activity_instance->get_edit_links_for_activity( $activity_array );

					if ( '<ul></ul>' !== $bookings ) {
						echo '<h4 class="metabox-bookings-found">Bookings</h4>';
						echo wp_kses( $bookings, staylodgic_get_guest_activities_allowed_tags() );
					}
					if ( '<ul></ul>' !== $activities ) {
						echo '<h4 class="metabox-bookings-found">Activities</h4>';
						echo wp_kses( $activities, staylodgic_get_guest_activities_allowed_tags() );
					}

					if ( '<ul></ul>' === $bookings ) {
						echo '<div class="metabox-no-bookings-found">No Bookings found</div>';
						echo '<br/>';
					}
					if ( '<ul></ul>' === $activities ) {
						echo '<div class="metabox-no-bookings-found">No Activities found</div>';
					}
					break;

				case 'get_customer_data':
					$customer_array = staylodgic_get_customer_array();

					$post_type = get_post_type( $field['id'] );

					if ( 'staylodgic_actvtres' === $post_type ) {
						$reservation_instance = new \Staylodgic\Activity();
					} else {
						$reservation_instance = new \Staylodgic\Reservations();
					}
					$customer_post_id   = $reservation_instance->get_reservation_customer_id( $field['id'] );
					$customer_post_edit = get_edit_post_link( $customer_post_id );
					echo '<a class="button button-primary button-large customer-edit-button" href="' . esc_url( $customer_post_edit ) . '">' . esc_html__( 'Edit Customer', 'staylodgic' ) . '</a><span class="customer-choice-inbetween"></span><a class="choice-customer-existing">' . esc_html__( 'or choose an existing customer', 'staylodgic' ) . '</a>';
					$customer_data = \Staylodgic\Data::get_customer_meta_data( $customer_array, $customer_post_id );

					$customer_data_list = \Staylodgic\Customers::generate_customer_html_list( $customer_data );

					echo wp_kses( $customer_data_list, staylodgic_get_guest_activities_allowed_tags() );

					break;
				case 'reservation_registration':
					$reservation_instance = new \Staylodgic\Reservations( $date = false, $room_id = false, $reservation_id = get_the_id() );
					$bookingnumber        = $reservation_instance->get_booking_number();

					if ( $bookingnumber ) {
						$registry_instance = new \Staylodgic\Guest_Registry();
						$res_reg_ids       = $registry_instance->fetch_res_reg_ids_by_booking_number( $bookingnumber );
						if ( isset( $res_reg_ids['guest_register_id'] ) ) {
							$registration_and_occupancy_text  = $registry_instance->output_registration_and_occupancy( $res_reg_ids['stay_reservation_id'], $res_reg_ids['guest_register_id'], 'text' );
							$registration_and_occupancy_icons = $registry_instance->output_registration_and_occupancy( $res_reg_ids['stay_reservation_id'], $res_reg_ids['guest_register_id'], 'icons' );

							echo wp_kses( $registration_and_occupancy_text, staylodgic_get_guest_activities_allowed_tags() );
							echo wp_kses( $registration_and_occupancy_icons, staylodgic_get_guest_activities_allowed_tags() );

							$guestregistration_post_edit = get_edit_post_link( $res_reg_ids['guest_register_id'] );
							echo '<a class="button button-primary button-large" href="' . esc_url( $guestregistration_post_edit ) . '">Edit Guest Registration</a>';
						} else {
							echo '<a data-bookingnumber="' . esc_attr( $bookingnumber ) . '" class="create-guest-registration button button-primary button-large" href="#">Create Guest Registration</a>';
						}
					}
					break;

				case 'guests':
					$output = '';

					$room_occupant_data = array();

					if ( isset( $field['datafrom'] ) ) {
						if ( 'roomtype' === $field['datafrom'] ) {

							$room = get_posts( 'post_type=staylodgic_rooms&numberposts=-1&order=ASC' );
							if ( $room ) {
								foreach ( $room as $key => $list ) {
									$max_adults   = 'disabled';
									$max_children = 'disabled';
									$max_guests   = '0';
									$custom       = get_post_custom( $list->ID );
									if ( isset( $custom['staylodgic_max_adult_limit_status'][0] ) ) {
										$adult_limit_status = $custom['staylodgic_max_adult_limit_status'][0];
										if ( '1' === $adult_limit_status ) {
											$max_adults = $custom['staylodgic_max_adults'][0];
										}
									}
									if ( isset( $custom['staylodgic_max_children_limit_status'][0] ) ) {
										$children_limit_status = $custom['staylodgic_max_children_limit_status'][0];
										if ( '1' === $children_limit_status ) {
											$max_children = $custom['staylodgic_max_children'][0];
										}
									}
									if ( isset( $custom['staylodgic_max_guests'][0] ) ) {
										$max_guests = $custom['staylodgic_max_guests'][0];
									}
									$room_occupant_data[ $list->ID ]['max_adults']   = $max_adults;
									$room_occupant_data[ $list->ID ]['max_children'] = $max_children;
									$room_occupant_data[ $list->ID ]['max_guests']   = $max_guests;
								}
							}
						}
					}

					$json_occupants = wp_json_encode( $room_occupant_data );

					if ( ! isset( $meta ) || '' === $meta ) {
						if ( 0 === (int) $meta ) {
							$meta = '0';
						} else {
							$meta = $field['std'];
						}
					}
					echo "<div id='" . esc_attr( $field['id'] ) . "_wrap' class='number-input occupant-" . esc_attr( $field['occupant'] ) . " occupants-range' data-room='0' data-occupant='" . esc_attr( $field['occupant'] ) . "' min='" . esc_attr( $field['min'] ) . "' max='" . esc_attr( $field['min'] ) . "' data-occupants='" . esc_attr( $json_occupants ) . "' >";
					echo '<span class="minus-btn">-</span>';

					echo '<input data-guest="' . esc_attr( $field['occupant'] ) . '" ';
					$child_age_input = '';
					if ( 'child' === $field['occupant'] ) {
						if ( isset( $meta['number'] ) ) {
							$meta_value = $meta['number'];
						} else {
							$meta_value = '0';
						}
						$name_property = $field['id'] . '[number]';

						echo 'data-childageinput="' . esc_attr( $field['id'] ) . '[age][]"';
					} else {
						$meta_value    = $meta;
						$name_property = $field['id'];
					}
					echo ' data-guestmax="0" data-adultmax="0" data-childmax="0" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $meta_value ) . '" name="' . esc_attr( $name_property ) . '" type="text" class="number-value" readonly>';
					echo '<span class="plus-btn">+</span>';
					echo '</div>';
					echo '<div class="occupant-' . esc_attr( $field['occupant'] ) . '-notify notify-number-over-max">Exceeds maximum</div>';
					if ( 'child' === $field['occupant'] ) {
						echo '<div class="child-number-max-notice">Maximum occupancy: <span class="child-number-max"></span></div>';
						echo '<div class="combined-child-number-max-notice">Combined occupancy: <span class="combined-child-number-max"></span></div>';
						echo '<div id="guest-age">';
						if ( isset( $meta['number'] ) ) {
							for ( $i = 0; $i < $meta['number']; $i++ ) {
								$age = isset( $meta['age'][ $i ] ) ? $meta['age'][ $i ] : '';
								echo "<input name='" . esc_attr( $field['id'] ) . "[age][]' type='number' data-counter='" . esc_attr( $i ) . "' value='" . esc_html( $age ) . "' placeholder='Enter age'>";
							}
						}
						echo '</div>';
					} else {
						echo '<div class="adult-number-max-notice">Maximum occupancy: <span class="adult-number-max"></span></div>';
						echo '<div class="combined-adult-number-max-notice">Combined occupancy: <span class="combined-adult-number-max"></span></div>';
					}

					break;

				case 'range':
					$output = '';
					echo '<div class="ranger-min-max-wrap-outer">';
					if ( isset( $field['unit'] ) ) {
						echo '<div class="ranger-min-max-wrap"><span class="ranger-min-value">' . esc_attr( $field['min'] ) . '</span>';
						echo '<span class="ranger-max-value">' . esc_attr( $field['max'] ) . '</span></div>';
						echo '<div id="' . esc_attr( $field['id'] ) . '_slider"></div>';
						echo '<div class="ranger-bar">';
					}
					if ( ! isset( $meta ) || '' === $meta ) {
						if ( 0 === (int) $meta ) {
							$meta = '0';
						} else {
							$meta = $field['std'];
						}
					}
					$meta = floatval( $meta );
					echo '<input id="' . esc_attr( $field['id'] ) . '" class="of-input" name="' . esc_attr( $field['id'] ) . '" type="text" value="' . esc_attr( $meta ) . '"';

					if ( isset( $field['unit'] ) ) {
						if ( isset( $field['min'] ) ) {
							echo ' min="' . esc_attr( $field['min'] );
						}
						if ( isset( $field['max'] ) ) {
							echo '" max="' . esc_attr( $field['max'] );
						}
						if ( isset( $field['step'] ) ) {
							echo '" step="' . esc_attr( $field['step'] );
						}
						echo '" />';
						if ( isset( $field['unit'] ) ) {
							echo '<span>' . esc_attr( $field['unit'] ) . '</span>';
						}
						echo '</div>';
					} else {
						echo ' />';
					}
					echo '</div>';

					break;

				case 'radio':
					foreach ( $field['options'] as $option ) {
						echo '<input type="radio" name="', esc_attr( $field['id'] ), '" value="', esc_attr( $option ), '"', $meta === $option ? ' checked="checked"' : '', ' />', esc_html( $option );
					}
					break;

				case 'image':
					$output = '';
					foreach ( $field['options'] as $key => $option ) {
						$selected = '';
						$checked  = '';
						if ( '' === $meta ) {
							if ( isset( $field['std'] ) ) {
								$meta = $field['std'];
							}
						}
						if ( '' !== $meta ) {
							if ( $meta === $key ) {
								$selected = ' of-radio-img-selected';
								$checked  = ' checked="checked"';
							}
						}
						echo '<input type="radio" id="' . esc_attr( $field['id'] . '_' . $key ) . '" class="of-radio-img-radio" value="' . esc_attr( $key ) . '" name="' . esc_attr( $field['id'] ) . '" ' . esc_attr( $checked ) . ' />';
						echo '<div class="of-radio-img-label">' . esc_html( $key ) . '</div>';
						echo '<img data-holder="' . esc_attr( $field['id'] . '_' . $key ) . '" data-value="' . esc_attr( $key ) . '" src="' . esc_url( $option ) . '" alt="' . esc_attr( $option ) . '" class="metabox-image-radio-selector of-radio-img-img' . esc_attr( $selected ) . '" />';
					}
					break;

				case 'checkbox':
					echo '<input type="checkbox" name="', esc_attr( $field['id'] ), '" id="', esc_attr( $field['id'] ), '"', $meta ? ' checked="checked"' : '', ' />';
					break;
			}
		}

		$notice_class = '';
		if ( isset( $field['type'] ) && 'notice' === $field['type'] ) {
			$notice_class = ' big-notice';
		}
		if ( isset( $field['desc'] ) && '' !== $field['desc'] ) {
			echo '<div class="metabox-description' . esc_attr( $notice_class ) . '">', esc_html( $field['desc'] ), '</div>';
		}

		if ( isset( $field['datatype'] ) && 'roomsubtotal' === $field['datatype'] ) {
			if ( staylodgic_has_tax() ) {
				echo '<br/><span id="reservation-tax-generate" class="button button-primary button-small">' . esc_html__( 'Generate Total', 'staylodgic' ) . '</span>&nbsp;';
				echo '<span id="reservation-tax-exclude" class="button button-secondary button-small">' . esc_html__( 'Exclude Tax', 'staylodgic' ) . '</span><br/><br/>';
			} else {
				echo '<span id="reservation-tax-exclude" class="button button-primary button-small">' . esc_html__( 'General Total', 'staylodgic' ) . '</span><br/><br/>';
			}
		}
		if ( isset( $field['datatype'] ) && 'activitysubtotal' === $field['datatype'] ) {
			if ( staylodgic_has_activity_tax() ) {
				echo '<br/><span id="activity-tax-generate" class="button button-primary button-small">' . esc_html__( 'Generate Total', 'staylodgic' ) . '</span>&nbsp;';
				echo '<span id="activity-tax-exclude" class="button button-secondary button-small">' . esc_html__( 'Exclude Tax', 'staylodgic' ) . '</span><br/><br/>';
			} else {
				echo '<span id="activity-tax-exclude" class="button button-primary button-small">' . esc_html__( 'Generate Total', 'staylodgic' ) . '</span><br/><br/>';
			}
		}
		echo '</div>';
	}

	if ( isset( $div_column_open ) && $div_column_open ) {
		echo '</div>';
	}

	echo '</div>';
}


/**
 * Save image ids
 */
function staylodgic_save_images() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( empty( $_POST['ids'] ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'staylodgic-nonce-admin' ) ) {
		return;
	}

	// Check user capability
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( isset( $_POST['ids'], $_POST['post_id'] ) ) {
		$ids_got = sanitize_text_field( wp_unslash( $_POST['ids'] ) );
		$ids     = rtrim( $ids_got, ',' );
		$post_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		update_post_meta( $post_id, 'staylodgic_image_ids', $ids );
	}

	// update thumbs
	$thumbs        = explode( ',', $ids );
	$thumbs_output = '';
	foreach ( $thumbs as $thumb ) {
		echo '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
	}

	die();
}
add_action( 'wp_ajax_staylodgic_save_images', 'staylodgic_save_images' );
/**
 * Save image ids
 */
function staylodgic_multo_gallery_save_images() {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Verify nonce
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( empty( $_POST['ids'] ) || empty( $nonce ) || ! wp_verify_nonce( $nonce, 'staylodgic-nonce-admin' ) ) {
		return;
	}

	// Check user capability
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	if ( isset( $_POST['ids'], $_POST['gallerysetid'], $_POST['post_id'] ) ) {
		$ids_got   = sanitize_text_field( wp_unslash( $_POST['ids'] ) );
		$ids       = rtrim( $ids_got, ',' );
		$galleryid = sanitize_text_field( wp_unslash( $_POST['gallerysetid'] ) );
		$post_id   = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );

		update_post_meta( $post_id, $galleryid, $ids );

		$getmeta = get_post_meta( $post_id, $galleryid, true );
	}

	// update thumbs
	$thumbs        = explode( ',', $ids );
	$thumbs_output = '';
	foreach ( $thumbs as $thumb ) {
		echo '<li>' . wp_get_attachment_image( $thumb, 'thumbnail' ) . '</li>';
	}

	die();
}
add_action( 'wp_ajax_staylodgic_multo_gallery_save_images', 'staylodgic_multo_gallery_save_images' );
// Save data from meta box
add_action( 'save_post', 'staylodgic_pre_process', 5, 3 );
add_action( 'save_post', 'staylodgic_checkdata' );
add_action( 'save_post', 'staylodgic_post_process', 15, 3 );

function staylodgic_pre_process( $post_id, $post, $update ) {
	// Check if this is a revision or auto-save.
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}

	// Ensure the action is only run for the 'staylodgic_bookings' custom post type.
	if ( 'staylodgic_bookings' !== $post->post_type ) {
		return;
	}

	// Now you can safely run your custom code here.
	// For example, use post meta data
	$checkin  = get_post_meta( $post_id, 'staylodgic_checkin_date', true );
	$checkout = get_post_meta( $post_id, 'staylodgic_checkout_date', true );
	$room_id  = get_post_meta( $post_id, 'staylodgic_room_id', true );

	// removeCache
	\Staylodgic\Cache::invalidate_caches_by_room_and_date( $room_id, $checkin, $checkout );

	// Perform actions or operations based on the meta value.
	// For example:
	// update_post_meta($post_id, 'another_meta_key', $new_meta_value);
}
function staylodgic_post_process( $post_id, $post, $update ) {
	// Check if this is a revision or auto-save.
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}

	// Ensure the action is only run for the 'staylodgic_bookings' custom post type.
	if ( 'staylodgic_bookings' !== $post->post_type ) {
		return;
	}

	// Now you can safely run your custom code here.
	// For example, use post meta data
	$checkin  = get_post_meta( $post_id, 'staylodgic_checkin_date', true );
	$checkout = get_post_meta( $post_id, 'staylodgic_checkout_date', true );
	$room_id  = get_post_meta( $post_id, 'staylodgic_room_id', true );

	\Staylodgic\Cache::invalidate_caches_by_room_and_date( $room_id, $checkin, $checkout );
}

// Hook the function to 'save_post' action.
function staylodgic_checkdata( $post_id ) {

	// Verify nonce
	$nonce = isset( $_POST['staylodgic_meta_box_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['staylodgic_meta_box_nonce'] ) ) : '';

	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'metabox-nonce' ) ) {
		return $post_id;
	}

	// Check user capability
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( isset( $_POST['staylodgic_meta_box_nonce'] ) ) {
		$staylodgic_post_type_got = get_post_type( $post_id );

		switch ( $staylodgic_post_type_got ) {
			case 'staylodgic_rooms':
				$staylodgic_room_box = staylodgic_room_metadata();
				staylodgic_savedata( $staylodgic_room_box, $post_id );
				break;
			case 'staylodgic_actvties':
				$staylodgic_activity_box = staylodgic_activity_metadata();
				staylodgic_savedata( $staylodgic_activity_box, $post_id );
				break;
			case 'staylodgic_actvtres':
				$staylodgic_activityres_box = staylodgic_activityres_metadata();
				staylodgic_savedata( $staylodgic_activityres_box, $post_id );
				break;
			case 'staylodgic_guestrgs':
				$registry_box = staylodgic_registry_metadata();
				staylodgic_savedata( $registry_box, $post_id );
				break;
			case 'staylodgic_bookings':
				$reservations_box = staylodgic_reservations_metadata();
				staylodgic_savedata( $reservations_box, $post_id );
				break;
			case 'staylodgic_customers':
				$customers_box = staylodgic_customers_metadata();
				staylodgic_savedata( $customers_box, $post_id );
				break;

			default:
				# code...
				break;
		}
	}
}

function staylodgic_savedata( $staylodgic_metaboxdata, $post_id ) {

	// Verify the nonce first before accessing $_POST
	$nonce = isset( $_POST['staylodgic_meta_box_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['staylodgic_meta_box_nonce'] ) ) : '';

	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'metabox-nonce' ) ) {
		return;
	}

	// Ensure user has permission to edit this post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( is_array( $staylodgic_metaboxdata['fields'] ) ) {
		foreach ( $staylodgic_metaboxdata['fields'] as $field ) {
			$field_id = $field['id'];
			$old      = get_post_meta( $post_id, $field_id, true );
			$new      = isset( $_POST[ $field_id ] ) ? map_deep( wp_unslash( $_POST[ $field_id ] ), 'sanitize_text_field' ) : '';

			if ( 'staylodgic_reservation_room_paid' === $field_id ) {
				// Get the first element of the array
				$first_element = reset( $new );
				// Check if the first element is empty
				if ( empty( $first_element ) ) {
					// Remove the first element
					array_shift( $new );

					foreach ( $new as $record ) {
						if ( ! in_array( $record, $old, true ) ) {
							// Record is missing from $old, add it to the missing_records array
							$missing_records[] = $record;
						}
					}

					if ( empty( $missing_records ) ) {
						// No records missing in the new array
					} else {
						$new = '';
						$old = '';
					}
				}
			}
			if ( $new !== $old ) {
				// Create or retrieve the log array
				$change_log = get_post_meta( $post_id, 'staylodgic_change_log', true );
				if ( ! is_array( $change_log ) ) {
					$change_log = array();
				}
				// Create a log entry with timestamp, field ID, old value, and new value

				if ( '' === $field['name'] ) {
					$storage_name = $field['desc'];
				} else {
					$storage_name = $field['name'];
				}

				$current_user = wp_get_current_user();
				$username     = $current_user->user_login;

				$log_entry = array(
					'timestamp' => current_time( 'mysql' ),
					'user'      => $username,
					'field_id'  => $storage_name,
					'old_value' => $old,
					'new_value' => $new,
				);

				// Add the log entry to the change log
				$change_log[] = $log_entry;

				// Update the post meta field with the new value
				delete_post_meta( $post_id, $field_id );
				update_post_meta( $post_id, $field_id, $new );

				// Update the change log in the post meta
				update_post_meta( $post_id, 'staylodgic_change_log', $change_log );
			}
		}
	}
}
