<?php
namespace Staylodgic;

class Common {


	/**
	 * Method reduce_date
	 *
	 * @param $stay_date_string
	 * @param $days $days
	 * @param $return_as_string
	 *
	 * @return void
	 */
	public static function reduce_date( $stay_date_string, $days, $return_as_string = true ) {
		$date = new \DateTime( $stay_date_string );
		$date->modify( "-$days days" );

		if ( $return_as_string ) {
			return $date->format( 'Y-m-d' );
		} else {
			return $date;
		}
	}

	/**
	 * Method generate_person_icons
	 *
	 * @param $adults $adults
	 * @param $children $children
	 *
	 * @return void
	 */
	public static function generate_person_icons( $adults = 0, $children = 0 ) {

		$html = '';

		if ( $adults > 0 ) {
			for ( $display_adult_count = 0; $display_adult_count < $adults; $display_adult_count++ ) {
				$html .= '<span class="guest-adult-svg"></span>';
			}
		}
		if ( $children > 0 ) {
			for ( $display_children_count = 0; $display_children_count < $children; $display_children_count++ ) {
				$html .= '<span class="guest-child-svg"></span>';
			}
		}

		return $html;
	}

	/**
	 * Method generate_uuid
	 *
	 * @return void
	 */
	public static function generate_uuid() {
		// Generate a version 4 UUID
		return sprintf(
			'%04x%04x-%04x-4%03x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			wp_rand( 0, 0xffff ),
			wp_rand( 0, 0xffff ),
			// 16 bits for "time_mid"
			wp_rand( 0, 0xffff ),
			// 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
			wp_rand( 0, 0x0fff ) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low"
			wp_rand( 0, 0xffff ),
			// 48 bits for "node"
			wp_rand( 0, 0xffff ),
			wp_rand( 0, 0xffff ),
			wp_rand( 0, 0xffff )
		);
	}

	/**
	 * Method country_code_to_emoji
	 *
	 * @param $code
	 *
	 * @return void
	 */
	public static function country_code_to_emoji( $code ) {
		$emoji       = '';
		$code        = strtoupper( $code );
		$code_length = strlen( $code ); // Call strlen() once and store the result in a variable

		for ( $i = 0; $i < $code_length; $i++ ) {
			$emoji .= '&#' . ( ord( $code[ $i ] ) + 127397 ) . ';';
		}
		return $emoji;
	}

	/**
	 * Method split_date_range
	 *
	 * @param $date_range
	 *
	 * @return void
	 */
	public static function split_date_range( $date_range ) {
		// Split the date range into start and end dates
		$date_range_array = explode( ' to ', $date_range );

		if ( count( $date_range_array ) < 2 && ! empty( $date_range_array[0] ) ) {
			// Use the single date as both start and end date
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $stay_start_date;
		} elseif ( count( $date_range_array ) < 2 && empty( $date_range_array[0] ) ) {
			// Return null if date_range is invalid
			return null;
		} else {
			$stay_start_date = $date_range_array[0];
			$stay_end_date   = $date_range_array[1];
		}

		// If the end date is empty, set it to the start date
		if ( empty( $stay_end_date ) ) {
			$stay_end_date = $stay_start_date;
		}

		// Return start and end date as an array
		return array(
			'stay_start_date' => $stay_start_date,
			'stay_end_date'   => $stay_end_date,
		);
	}

	/**
	 * Method count_days_between_dates
	 *
	 * @param $stay_start_date
	 * @param $stay_end_date
	 *
	 * @return void
	 */
	public static function count_days_between_dates( $stay_start_date, $stay_end_date ) {
		// Create DateTime objects for the start and end dates
		$start_date_time = new \DateTime( $stay_start_date );
		$end_date_time   = new \DateTime( $stay_end_date );

		// Calculate the difference between the two dates
		$interval = $end_date_time->diff( $start_date_time );

		// Extract the number of days from the interval
		$days_between = $interval->days;

		// Return the result
		return $days_between;
	}

	/**
	 * Method Function to create an array of dates between two dates
	 *
	 * @param $stay_start_date
	 * @param $stay_end_date
	 *
	 * @return void
	 */
	public static function create_in_between_date_range_array( $stay_start_date, $stay_end_date ) {
		$date_range_array = array();

		$stay_current_date = strtotime( $stay_start_date );
		$stay_end_date     = strtotime( $stay_end_date );

		while ( $stay_current_date <= $stay_end_date ) {
			$date_range_array[] = gmdate( 'Y-m-d', $stay_current_date );
			$stay_current_date  = strtotime( '+1 day', $stay_current_date );
		}

		return $date_range_array;
	}

	/**
	 * Method Gets all the dates between two given dates
	 *
	 * @param $start_date
	 * @param $end_date
	 *
	 * @return void
	 */
	public static function get_dates_between( $start_date, $end_date ) {
		$dates        = array();
		$current_date = strtotime( $start_date );
		$end_date     = strtotime( $end_date );

		while ( $current_date <= $end_date ) {
			$dates[]      = gmdate( 'Y-m-d', $current_date );
			$current_date = strtotime( '+1 day', $current_date );
		}

		return $dates;
	}

	/**
	 * Method Checks if the post is valid for processing
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function is_reservation_valid_post( $post_id, $post ) {
		return ! wp_is_post_autosave( $post_id ) && ! wp_is_post_revision( $post_id ) && 'staylodgic_bookings' === $post->post_type && get_post_status( $post_id ) !== 'draft';
	}

	/**
	 * Method Checks if the post is valid for processing
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @return void
	 */
	public static function is_activities_valid_post( $post_id, $post ) {
		return ! wp_is_post_autosave( $post_id ) && ! wp_is_post_revision( $post_id ) && 'staylodgic_actvtres' === $post->post_type && get_post_status( $post_id ) !== 'draft';
	}

	/**
	 * Method Checks if the post is valid for processing
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public static function is_customer_valid_post( $post_id ) {
		$post = get_post( $post_id );
		return null !== $post && ! wp_is_post_autosave( $post_id ) && ! wp_is_post_revision( $post_id ) && 'staylodgic_customers' === $post->post_type && get_post_status( $post_id ) !== 'draft';
	}

	public static function get_staylodgic_currencies() {
		$currencies = array(
			'AED' => __( 'United Arab Emirates dirham', 'staylodgic' ),
			'AFN' => __( 'Afghan afghani', 'staylodgic' ),
			'ALL' => __( 'Albanian lek', 'staylodgic' ),
			'AMD' => __( 'Armenian dram', 'staylodgic' ),
			'ANG' => __( 'Netherlands Antillean guilder', 'staylodgic' ),
			'AOA' => __( 'Angolan kwanza', 'staylodgic' ),
			'ARS' => __( 'Argentine peso', 'staylodgic' ),
			'AUD' => __( 'Australian dollar', 'staylodgic' ),
			'AWG' => __( 'Aruban florin', 'staylodgic' ),
			'AZN' => __( 'Azerbaijani manat', 'staylodgic' ),
			'BAM' => __( 'Bosnia and Herzegovina convertible mark', 'staylodgic' ),
			'BBD' => __( 'Barbadian dollar', 'staylodgic' ),
			'BDT' => __( 'Bangladeshi taka', 'staylodgic' ),
			'BGN' => __( 'Bulgarian lev', 'staylodgic' ),
			'BHD' => __( 'Bahraini dinar', 'staylodgic' ),
			'BIF' => __( 'Burundian franc', 'staylodgic' ),
			'BMD' => __( 'Bermudian dollar', 'staylodgic' ),
			'BND' => __( 'Brunei dollar', 'staylodgic' ),
			'BOB' => __( 'Bolivian boliviano', 'staylodgic' ),
			'BRL' => __( 'Brazilian real', 'staylodgic' ),
			'BSD' => __( 'Bahamian dollar', 'staylodgic' ),
			'BTC' => __( 'Bitcoin', 'staylodgic' ),
			'BTN' => __( 'Bhutanese ngultrum', 'staylodgic' ),
			'BWP' => __( 'Botswana pula', 'staylodgic' ),
			'BYR' => __( 'Belarusian ruble (old)', 'staylodgic' ),
			'BYN' => __( 'Belarusian ruble', 'staylodgic' ),
			'BZD' => __( 'Belize dollar', 'staylodgic' ),
			'CAD' => __( 'Canadian dollar', 'staylodgic' ),
			'CDF' => __( 'Congolese franc', 'staylodgic' ),
			'CHF' => __( 'Swiss franc', 'staylodgic' ),
			'CLP' => __( 'Chilean peso', 'staylodgic' ),
			'CNY' => __( 'Chinese yuan', 'staylodgic' ),
			'COP' => __( 'Colombian peso', 'staylodgic' ),
			'CRC' => __( 'Costa Rican col&oacute;n', 'staylodgic' ),
			'CUC' => __( 'Cuban convertible peso', 'staylodgic' ),
			'CUP' => __( 'Cuban peso', 'staylodgic' ),
			'CVE' => __( 'Cape Verdean escudo', 'staylodgic' ),
			'CZK' => __( 'Czech koruna', 'staylodgic' ),
			'DJF' => __( 'Djiboutian franc', 'staylodgic' ),
			'DKK' => __( 'Danish krone', 'staylodgic' ),
			'DOP' => __( 'Dominican peso', 'staylodgic' ),
			'DZD' => __( 'Algerian dinar', 'staylodgic' ),
			'EGP' => __( 'Egyptian pound', 'staylodgic' ),
			'ERN' => __( 'Eritrean nakfa', 'staylodgic' ),
			'ETB' => __( 'Ethiopian birr', 'staylodgic' ),
			'EUR' => __( 'Euro', 'staylodgic' ),
			'FJD' => __( 'Fijian dollar', 'staylodgic' ),
			'FKP' => __( 'Falkland Islands pound', 'staylodgic' ),
			'GBP' => __( 'Pound sterling', 'staylodgic' ),
			'GEL' => __( 'Georgian lari', 'staylodgic' ),
			'GGP' => __( 'Guernsey pound', 'staylodgic' ),
			'GHS' => __( 'Ghana cedi', 'staylodgic' ),
			'GIP' => __( 'Gibraltar pound', 'staylodgic' ),
			'GMD' => __( 'Gambian dalasi', 'staylodgic' ),
			'GNF' => __( 'Guinean franc', 'staylodgic' ),
			'GTQ' => __( 'Guatemalan quetzal', 'staylodgic' ),
			'GYD' => __( 'Guyanese dollar', 'staylodgic' ),
			'HKD' => __( 'Hong Kong dollar', 'staylodgic' ),
			'HNL' => __( 'Honduran lempira', 'staylodgic' ),
			'HRK' => __( 'Croatian kuna', 'staylodgic' ),
			'HTG' => __( 'Haitian gourde', 'staylodgic' ),
			'HUF' => __( 'Hungarian forint', 'staylodgic' ),
			'IDR' => __( 'Indonesian rupiah', 'staylodgic' ),
			'ILS' => __( 'Israeli new shekel', 'staylodgic' ),
			'IMP' => __( 'Manx pound', 'staylodgic' ),
			'INR' => __( 'Indian rupee', 'staylodgic' ),
			'IQD' => __( 'Iraqi dinar', 'staylodgic' ),
			'IRR' => __( 'Iranian rial', 'staylodgic' ),
			'IRT' => __( 'Iranian toman', 'staylodgic' ),
			'ISK' => __( 'Icelandic kr&oacute;na', 'staylodgic' ),
			'JEP' => __( 'Jersey pound', 'staylodgic' ),
			'JMD' => __( 'Jamaican dollar', 'staylodgic' ),
			'JOD' => __( 'Jordanian dinar', 'staylodgic' ),
			'JPY' => __( 'Japanese yen', 'staylodgic' ),
			'KES' => __( 'Kenyan shilling', 'staylodgic' ),
			'KGS' => __( 'Kyrgyzstani som', 'staylodgic' ),
			'KHR' => __( 'Cambodian riel', 'staylodgic' ),
			'KMF' => __( 'Comorian franc', 'staylodgic' ),
			'KPW' => __( 'North Korean won', 'staylodgic' ),
			'KRW' => __( 'South Korean won', 'staylodgic' ),
			'KWD' => __( 'Kuwaiti dinar', 'staylodgic' ),
			'KYD' => __( 'Cayman Islands dollar', 'staylodgic' ),
			'KZT' => __( 'Kazakhstani tenge', 'staylodgic' ),
			'LAK' => __( 'Lao kip', 'staylodgic' ),
			'LBP' => __( 'Lebanese pound', 'staylodgic' ),
			'LKR' => __( 'Sri Lankan rupee', 'staylodgic' ),
			'LRD' => __( 'Liberian dollar', 'staylodgic' ),
			'LSL' => __( 'Lesotho loti', 'staylodgic' ),
			'LYD' => __( 'Libyan dinar', 'staylodgic' ),
			'MAD' => __( 'Moroccan dirham', 'staylodgic' ),
			'MDL' => __( 'Moldovan leu', 'staylodgic' ),
			'MGA' => __( 'Malagasy ariary', 'staylodgic' ),
			'MKD' => __( 'Macedonian denar', 'staylodgic' ),
			'MMK' => __( 'Burmese kyat', 'staylodgic' ),
			'MNT' => __( 'Mongolian t&ouml;gr&ouml;g', 'staylodgic' ),
			'MOP' => __( 'Macanese pataca', 'staylodgic' ),
			'MRU' => __( 'Mauritanian ouguiya', 'staylodgic' ),
			'MUR' => __( 'Mauritian rupee', 'staylodgic' ),
			'MVR' => __( 'Maldivian rufiyaa', 'staylodgic' ),
			'MWK' => __( 'Malawian kwacha', 'staylodgic' ),
			'MXN' => __( 'Mexican peso', 'staylodgic' ),
			'MYR' => __( 'Malaysian ringgit', 'staylodgic' ),
			'MZN' => __( 'Mozambican metical', 'staylodgic' ),
			'NAD' => __( 'Namibian dollar', 'staylodgic' ),
			'NGN' => __( 'Nigerian naira', 'staylodgic' ),
			'NIO' => __( 'Nicaraguan c&oacute;rdoba', 'staylodgic' ),
			'NOK' => __( 'Norwegian krone', 'staylodgic' ),
			'NPR' => __( 'Nepalese rupee', 'staylodgic' ),
			'NZD' => __( 'New Zealand dollar', 'staylodgic' ),
			'OMR' => __( 'Omani rial', 'staylodgic' ),
			'PAB' => __( 'Panamanian balboa', 'staylodgic' ),
			'PEN' => __( 'Sol', 'staylodgic' ),
			'PGK' => __( 'Papua New Guinean kina', 'staylodgic' ),
			'PHP' => __( 'Philippine peso', 'staylodgic' ),
			'PKR' => __( 'Pakistani rupee', 'staylodgic' ),
			'PLN' => __( 'Polish z&#x142;oty', 'staylodgic' ),
			'PRB' => __( 'Transnistrian ruble', 'staylodgic' ),
			'PYG' => __( 'Paraguayan guaran&iacute;', 'staylodgic' ),
			'QAR' => __( 'Qatari riyal', 'staylodgic' ),
			'RON' => __( 'Romanian leu', 'staylodgic' ),
			'RSD' => __( 'Serbian dinar', 'staylodgic' ),
			'RUB' => __( 'Russian ruble', 'staylodgic' ),
			'RWF' => __( 'Rwandan franc', 'staylodgic' ),
			'SAR' => __( 'Saudi riyal', 'staylodgic' ),
			'SBD' => __( 'Solomon Islands dollar', 'staylodgic' ),
			'SCR' => __( 'Seychellois rupee', 'staylodgic' ),
			'SDG' => __( 'Sudanese pound', 'staylodgic' ),
			'SEK' => __( 'Swedish krona', 'staylodgic' ),
			'SGD' => __( 'Singapore dollar', 'staylodgic' ),
			'SHP' => __( 'Saint Helena pound', 'staylodgic' ),
			'SLL' => __( 'Sierra Leonean leone', 'staylodgic' ),
			'SOS' => __( 'Somali shilling', 'staylodgic' ),
			'SRD' => __( 'Surinamese dollar', 'staylodgic' ),
			'SSP' => __( 'South Sudanese pound', 'staylodgic' ),
			'STN' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'staylodgic' ),
			'SYP' => __( 'Syrian pound', 'staylodgic' ),
			'SZL' => __( 'Swazi lilangeni', 'staylodgic' ),
			'THB' => __( 'Thai baht', 'staylodgic' ),
			'TJS' => __( 'Tajikistani somoni', 'staylodgic' ),
			'TMT' => __( 'Turkmenistan manat', 'staylodgic' ),
			'TND' => __( 'Tunisian dinar', 'staylodgic' ),
			'TOP' => __( 'Tongan pa&#x2bb;anga', 'staylodgic' ),
			'TRY' => __( 'Turkish lira', 'staylodgic' ),
			'TTD' => __( 'Trinidad and Tobago dollar', 'staylodgic' ),
			'TWD' => __( 'New Taiwan dollar', 'staylodgic' ),
			'TZS' => __( 'Tanzanian shilling', 'staylodgic' ),
			'UAH' => __( 'Ukrainian hryvnia', 'staylodgic' ),
			'UGX' => __( 'Ugandan shilling', 'staylodgic' ),
			'USD' => __( 'United States (US) dollar', 'staylodgic' ),
			'UYU' => __( 'Uruguayan peso', 'staylodgic' ),
			'UZS' => __( 'Uzbekistani som', 'staylodgic' ),
			'VEF' => __( 'Venezuelan bol&iacute;var', 'staylodgic' ),
			'VES' => __( 'Bol&iacute;var soberano', 'staylodgic' ),
			'VND' => __( 'Vietnamese &#x111;&#x1ed3;ng', 'staylodgic' ),
			'VUV' => __( 'Vanuatu vatu', 'staylodgic' ),
			'WST' => __( 'Samoan t&#x101;l&#x101;', 'staylodgic' ),
			'XAF' => __( 'Central African CFA franc', 'staylodgic' ),
			'XCD' => __( 'East Caribbean dollar', 'staylodgic' ),
			'XOF' => __( 'West African CFA franc', 'staylodgic' ),
			'XPF' => __( 'CFP franc', 'staylodgic' ),
			'YER' => __( 'Yemeni rial', 'staylodgic' ),
			'ZAR' => __( 'South African rand', 'staylodgic' ),
			'ZMW' => __( 'Zambian kwacha', 'staylodgic' ),
		);

		return $currencies;
	}

	/**
	 * Method get_staylodgic_currency_symbols
	 *
	 * @return void
	 */
	public static function get_staylodgic_currency_symbols() {

		$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x62f;.&#x639;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => '&#8376;',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#1088;&#1089;&#1076;',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'E',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);

		return $symbols;
	}

	/**
	 * Method get_symbol_for_currency
	 *
	 * @param $currency
	 *
	 * @return void
	 */
	public static function get_symbol_for_currency( $currency = '' ) {
		$currency = self::get_staylodgic_currencies();
		$symbols  = self::get_staylodgic_currency_symbols();

		$currency_symbol = $symbols[ $currency ];

		return $currency_symbol;
	}
}
