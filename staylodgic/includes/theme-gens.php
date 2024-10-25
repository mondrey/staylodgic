<?php

/**
 * Add passed string to SPAN with class
 *
 * @param $input_string
 * @param $class
 *
 * @return string html_output
 */
function staylodgic_string_to_html_spans( $input_string, $css_class ) {
	// Split the input string by commas
	$pieces = explode( ',', $input_string );

	// Initialize an empty HTML string
	$html_output = '';

	// Iterate through the pieces and wrap each in a <span> tag
	foreach ( $pieces as $piece ) {
		// Remove leading and trailing whitespaces from each piece
		$piece = trim( $piece );

		// Add the piece wrapped in a <span> tag to the HTML output
		$html_output .= '<span class="' . $css_class . '"><span class="facility"><i class="fa-solid fa-check"></i>' . $piece . '</span></span> ';
	}

	// Remove the trailing comma and space
	$html_output = rtrim( $html_output, ', ' );

	return $html_output;
}

/**
 * Get featured image link
 *
 * @param $the_image_id
 *
 * @return string image_url
 */
function staylodgic_featured_image_link( $the_image_id ) {
	$image_url = '';

	if ( ! isset( $the_image_id ) ) {
		$the_image_id = get_the_id();
	}
	$image_id  = get_post_thumbnail_id( $the_image_id, 'full' );
	$image_url = wp_get_attachment_image_src( $image_id, 'full' );
	if ( isset( $image_url[0] ) ) {

		$image_url = $image_url[0];
	}
	return $image_url;
}
/**
 * Generate a list of existing menus
 *
 * @return string menu_select
 */
function staylodgic_generate_menulist() {
	$menus       = get_terms(
		array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
		)
	);
	$menu_select = false;
	if ( isset( $menus ) ) {
		$menu_select            = array();
		$menu_select['default'] = esc_html__( 'Default Menu', 'staylodgic' );

		foreach ( $menus as $menu ) {
			$menu_select[ $menu->term_id ] = $menu->name;
		}
	}
	return $menu_select;
}
/**
 * Get elementor page data
 *
 * @param $post_id
 * @param $field_id
 *
 * @return string got_data
 */
function staylodgic_get_elementor_data( $post_id, $field_id ) {

	$got_data = false;

	if ( staylodgic_page_is_built_with_elementor( $post_id ) ) {
		$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
		if ( isset( $elementor_page_settings[ $field_id ] ) ) {
			$got_data = $elementor_page_settings[ $field_id ];
		}
	}

	return $got_data;
}
/**
 * Get page layout
 *
 * @param $post_id
 *
 * @return string pagestyle
 */
function staylodgic_get_pagestyle( $post_id ) {
	$got_pagestyle = get_post_meta( $post_id, 'staylodgic_pagestyle', true );

	switch ( $got_pagestyle ) {
		case 'rightsidebar':
			$pagestyle = 'rightsidebar';
			break;
		case 'leftsidebar':
			$pagestyle = 'leftsidebar';
			break;
		case 'nosidebar':
			$pagestyle = 'nosidebar';
			break;
		case 'edge-to-edge':
			$pagestyle = 'edge-to-edge';
			break;

		default:
			$pagestyle = 'rightsidebar';

			if ( staylodgic_page_is_built_with_elementor( $post_id ) ) {
				$pagestyle = 'edge-to-edge';
			}

			break;
	}
	return $pagestyle;
}
/**
 * Check if page has password protection
 *
 * @param $id
 *
 * @return booleen
 */
function staylodgic_has_password( $id ) {
	$checking_for_password = get_post( $id );
	if ( ! empty( $checking_for_password->post_password ) ) {
		return true;
	}
	return false;
}
/**
 * Generate options for select input based on type
 *
 * @param $type
 *
 * @return array
 */
function staylodgic_get_select_target_options( $type ) {
	$list_options = array();

	switch ( $type ) {
		case 'post':
			$the_list = get_posts( 'orderby=title&numberposts=-1&order=ASC' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->ID ] = $list->post_title;
			}
			break;
		case 'page':
			$the_list = get_pages( 'title_li=&orderby=name' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->ID ] = $list->post_title;
			}
			break;
		case 'category':
			$the_list = get_categories( 'orderby=name&hide_empty=0' );
			foreach ( $the_list as $key => $list ) {
				$list_options[ $list->term_id ] = $list->name;
			}
			break;
		case 'bedsetup':
			$list_options = array(
				'twinbed'  => esc_html__( 'Twin bed', 'staylodgic' ),
				'fullbed'  => esc_html__( 'Full bed', 'staylodgic' ),
				'queenbed' => esc_html__( 'Queen bed', 'staylodgic' ),
				'kingbed'  => esc_html__( 'King bed', 'staylodgic' ),
				'bunkbed'  => esc_html__( 'Bunk bed', 'staylodgic' ),
				'sofabed'  => esc_html__( 'Sofa bed', 'staylodgic' ),
			);
			break;
		case 'booking_numbers':
			// Get all reservation posts
			$reservation_args = array(
				'post_type'      => 'slgc_reservations',
				'posts_per_page' => -1, // Retrieve all reservations
			);

			$reservations = get_posts( $reservation_args );

			// Create an array to store the booking numbers and customer names
			$booking_numbers         = array();
			$booking_numbers['none'] = 'Choose a booking';
			foreach ( $reservations as $reservation ) {
				$booking_number = get_post_meta( $reservation->ID, 'staylodgic_booking_number', true );
				$customer_id    = get_post_meta( $reservation->ID, 'staylodgic_customer_id', true );

				// Get the customer name based on the customer ID
				$customer_name = get_post_meta( $customer_id, 'staylodgic_full_name', true );

				// Add the booking number and customer name to the array
				$booking_numbers[ $booking_number ] = $booking_number . ' ' . $customer_name;
			}

			// Output the booking numbers and customer names
			$list_options = $booking_numbers;

			break;
		case 'room_names':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=slgc_room&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$list_options[ $list->ID ] = $list->post_title;
				}
			} else {
				$list_options[0] = 'Rooms not found.';
			}
			break;
		case 'activity_names':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=slgc_activity&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$list_options[ $list->ID ] = $list->post_title;
				}
			} else {
				$list_options[0] = 'Activities not found.';
			}
			break;
		case 'existing_customers':
			// Pull all the Featured into an array
			$featured_pages       = get_posts( 'post_type=slgc_customers&orderby=title&numberposts=-1&order=ASC' );
			$list_options['none'] = 'Not Selected';
			if ( $featured_pages ) {
				foreach ( $featured_pages as $key => $list ) {
					$list_options[ $list->ID ] = $list->post_title . ' ' . $list->ID;
				}
			} else {
				$list_options[0] = 'Customers not found.';
			}
			break;
	}

	return $list_options;
}
/**
 * Output an array or select choices with countries
 *
 * @param $output_type
 * @param $selected
 *
 * @return array
 */
function staylodgic_country_list( $output_type = 'select', $selected = '' ) {
	$countries    = array(
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
		'CD' => 'Congo - Democratic Republic',
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
		'IR' => 'Iran - Islamic Republic Of',
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
		'FM' => 'Micronesia - Federated States Of',
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
		'PS' => 'Palestinian Territory - Occupied',
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
		'VG' => 'Virgin Islands - British',
		'VI' => 'Virgin Islands - U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);
	$country_list = false;
	if ( 'select' === $output_type ) {
		$country_list  = '';
		$country_list .= '<option selected disabled value="">Choose a country</option>';
		foreach ( $countries as $key => $option ) {
			if ( $selected === $key ) {
				$country_selected = 'selected="selected"';
			} else {
				$country_selected = '';
			}
			$country_list .= '<option value="' . esc_attr( $key ) . '" ' . $country_selected . '>' . esc_attr( $option ) . '</option>';
		}
	}
	if ( 'select-alt' === $output_type ) {
		$country_list = '';
		$count        = 0;
		foreach ( $countries as $key => $option ) {
			if ( $count > 0 ) {
				$country_list .= ',';
			}
			++$count;
			$country_list .= $key . ':' . $option;
		}
	}

	if ( 'select-array' === $output_type ) {
		$country_list = array();
		$countries    = array_merge( array( 'none' => 'Choose a country' ), $countries );
		$country_list = $countries;
	}
	if ( 'display' === $output_type ) {
		if ( array_key_exists( $selected, $countries ) ) {
			$country_list = $countries[ $selected ];
		}
	}
	return $country_list;
}

/**
 * Get image ID from a url if it is present in WordPress media library
 *
 * @param $image_url
 *
 * @return int|bool
 */
function staylodgic_get_image_id_from_url( $image_url ) {
	$attachment = attachment_url_to_postid( $image_url );
	if ( $attachment ) {
		return $attachment;
	} else {
		return false;
	}
}
/**
 * For a proofing set gallery
 *
 * @param $page_id
 *
 * @return string
 */
function staylodgic_get_proofing_attachments( $page_id ) {
	$filter_image_ids = false;
	$the_image_ids    = get_post_meta( $page_id, 'staylodgic_proofing_image_ids' );
	if ( $the_image_ids ) {
		$filter_image_ids = explode( ',', $the_image_ids[0] );
		return $filter_image_ids;
	}
}
/**
 * Get page attached image IDs
 *
 * @param $page_id
 *
 * @return string
 */
function staylodgic_get_custom_attachments( $page_id ) {
	$filter_image_ids = false;
	$the_image_ids    = get_post_meta( $page_id, 'staylodgic_image_ids' );
	if ( $the_image_ids ) {
		$filter_image_ids = explode( ',', $the_image_ids[0] );
		return $filter_image_ids;
	}
}
/**
 * Get image urls of page attachements
 *
 * @param $page_id
 *
 * @return array
 */
function staylodgic_get_custom_attachment_images( $page_id ) {
	$images        = array();
	$the_image_ids = get_post_meta( $page_id, 'staylodgic_image_ids', true );
	if ( $the_image_ids ) {
		$filter_image_ids = explode( ',', $the_image_ids );
		foreach ( $filter_image_ids as $image_id ) {
			$thumbnail_url  = wp_get_attachment_image_src( $image_id, 'thumbnail' )[0];
			$full_image_url = wp_get_attachment_image_src( $image_id, 'full' )[0];
			$images[]       = array(
				'thumbnail'  => $thumbnail_url,
				'full_image' => $full_image_url,
			);
		}
	}
	return $images;
}
/**
 * Get custom image links
 *
 * @param $page_id
 *
 * @return string
 */
function staylodgic_output_custom_image_links( $page_id ) {
	$images = staylodgic_get_custom_attachment_images( $page_id );
	$output = '';

	if ( empty( $images ) ) {
		return false;
	}

	$output .= '<div class="supporting-image-gallery">';
	foreach ( $images as $image ) {
		$output .= '<a class="lightbox-image"  data-gallery="lightbox-gallery-' . esc_attr( $page_id ) . '" data-toggle="lightbox" href="' . esc_url( $image['full_image'] ) . '">';
		$output .= '<img class="main-image" src="' . esc_url( $image['thumbnail'] ) . '" alt="main image">';
		$output .= '</a>';
	}
	$output .= '</div>';

	return $output;
}
/**
 * Check if page is built with elementor
 *
 * @param $post_id
 *
 * @return string
 */
function staylodgic_page_is_built_with_elementor( $post_id ) {
	$status = get_post_meta( $post_id, '_elementor_edit_mode', true );
	return $status;
}
/**
 * Get max sidebars
 *
 * @return int
 */
function staylodgic_get_max_sidebars() {
	$max_sidebars = 50;
	return $max_sidebars;
}
/**
 * Get option data
 *
 * @param $name
 * @param $default
 *
 * @return array
 */
function staylodgic_get_option_data( $name, $default_value = false ) {

	$opt_value = get_theme_mod( $name );
	if ( isset( $opt_value ) && '' !== $opt_value ) {
		return $opt_value;
	}
	return $default_value;
}

/**
 * Get customer page meta options
 *
 * @return array
 */
function staylodgic_get_customer_array() {
	$customer = array(
		array(
			'name'   => esc_html__( 'Create new customer', 'staylodgic' ),
			'id'     => 'staylodgic_sep_page_options',
			'action' => 'display_choices_for_customer',
			'type'   => 'seperator',
		),
		array(
			'name'    => esc_html__( 'Full Name', 'staylodgic' ),
			'id'      => 'staylodgic_full_name',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'Email Address', 'staylodgic' ),
			'id'      => 'staylodgic_email_address',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'Phone Number', 'staylodgic' ),
			'id'      => 'staylodgic_phone_number',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'Street Address', 'staylodgic' ),
			'id'      => 'staylodgic_street_address',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'City', 'staylodgic' ),
			'id'      => 'staylodgic_city',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'State', 'staylodgic' ),
			'id'      => 'staylodgic_state',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'Zip Code', 'staylodgic' ),
			'id'      => 'staylodgic_zip_code',
			'type'    => 'text',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
		array(
			'name'    => esc_html__( 'Country', 'staylodgic' ),
			'id'      => 'staylodgic_country',
			'type'    => 'country',
			'class'   => 'registration-field',
			'heading' => 'subhead',
			'desc'    => '',
			'std'     => '',
		),
	);

	return $customer;
}
