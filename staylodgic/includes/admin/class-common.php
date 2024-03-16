<?php
namespace Staylodgic;

class Common
{
	public static function generateUuid() {
		// Generate a version 4 UUID
		return sprintf('%04x%04x-%04x-4%03x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),
			// 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
			mt_rand(0, 0x0fff) | 0x4000,
			// 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low"
			mt_rand(0, 0xffff),
			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
	
	public static function countryCodeToEmoji($code)
	{
		$emoji = '';
		$code  = strtoupper($code);
		for ($i = 0; $i < strlen($code); $i++) {
			$emoji .= '&#' . (ord($code[$i]) + 127397) . ';';
		}
		return $emoji;
	}

	public static function splitDateRange($dateRange)
	{
		// Split the date range into start and end dates
		$dateRangeArray = explode(" to ", $dateRange);

		if (count($dateRangeArray) < 2 && !empty($dateRangeArray[0])) {
			// Use the single date as both start and end date
			$startDate = $dateRangeArray[0];
			$endDate   = $startDate;
		} elseif (count($dateRangeArray) < 2 && empty($dateRangeArray[0])) {
			// Return null if dateRange is invalid
			return null;
		} else {
			$startDate = $dateRangeArray[0];
			$endDate   = $dateRangeArray[1];
		}

		// If the end date is empty, set it to the start date
		if (empty($endDate)) {
			$endDate = $startDate;
		}

		// Return start and end date as an array
		return array('startDate' => $startDate, 'endDate' => $endDate);
	}

	public static function countDays_BetweenDates($startDate, $endDate)
	{
		// Create DateTime objects for the start and end dates
		$startDateTime = new \DateTime($startDate);
		$endDateTime   = new \DateTime($endDate);

		// Calculate the difference between the two dates
		$interval = $endDateTime->diff($startDateTime);

		// Extract the number of days from the interval
		$daysBetween = $interval->days;

		// Return the result
		return $daysBetween;
	}

	// Function to create an array of dates between two dates
	public static function create_inBetween_DateRange_Array($startDate, $endDate)
	{
		$dateRangeArray = array();

		$currentDate = strtotime($startDate);
		$endDate     = strtotime($endDate);

		while ($currentDate <= $endDate) {
			$dateRangeArray[] = date('Y-m-d', $currentDate);
			$currentDate      = strtotime('+1 day', $currentDate);
		}

		return $dateRangeArray;
	}

	/**
	 * Gets all the dates between two given dates
	 */
	public static function getDates_Between($start_date, $end_date)
	{
		$dates        = [];
		$current_date = strtotime($start_date);
		$end_date     = strtotime($end_date);

		while ($current_date <= $end_date) {
			$dates[]      = date('Y-m-d', $current_date);
			$current_date = strtotime('+1 day', $current_date);
		}

		return $dates;
	}

	/**
	 * Checks if the post is valid for processing
	 */
	public static function isReservation_valid_post($post_id, $post)
	{
		return !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'slgc_reservations' && get_post_status($post_id) !== 'draft';
	}

	/**
	 * Checks if the post is valid for processing
	 */
	public static function isActivities_valid_post($post_id, $post)
	{
		return !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'slgc_activityres' && get_post_status($post_id) !== 'draft';
	}

	/**
	 * Checks if the post is valid for processing
	 */
	public static function isCustomer_valid_post($post_id)
	{
		$post = get_post($post_id);
		return $post !== null && !wp_is_post_autosave($post_id) && !wp_is_post_revision($post_id) && $post->post_type === 'slgc_customers' && get_post_status($post_id) !== 'draft';
	}

	public static function get_staylodgic_currencies()
	{
		$currencies = array(
			'AED' => __('United Arab Emirates dirham', 'woocommerce'),
			'AFN' => __('Afghan afghani', 'woocommerce'),
			'ALL' => __('Albanian lek', 'woocommerce'),
			'AMD' => __('Armenian dram', 'woocommerce'),
			'ANG' => __('Netherlands Antillean guilder', 'woocommerce'),
			'AOA' => __('Angolan kwanza', 'woocommerce'),
			'ARS' => __('Argentine peso', 'woocommerce'),
			'AUD' => __('Australian dollar', 'woocommerce'),
			'AWG' => __('Aruban florin', 'woocommerce'),
			'AZN' => __('Azerbaijani manat', 'woocommerce'),
			'BAM' => __('Bosnia and Herzegovina convertible mark', 'woocommerce'),
			'BBD' => __('Barbadian dollar', 'woocommerce'),
			'BDT' => __('Bangladeshi taka', 'woocommerce'),
			'BGN' => __('Bulgarian lev', 'woocommerce'),
			'BHD' => __('Bahraini dinar', 'woocommerce'),
			'BIF' => __('Burundian franc', 'woocommerce'),
			'BMD' => __('Bermudian dollar', 'woocommerce'),
			'BND' => __('Brunei dollar', 'woocommerce'),
			'BOB' => __('Bolivian boliviano', 'woocommerce'),
			'BRL' => __('Brazilian real', 'woocommerce'),
			'BSD' => __('Bahamian dollar', 'woocommerce'),
			'BTC' => __('Bitcoin', 'woocommerce'),
			'BTN' => __('Bhutanese ngultrum', 'woocommerce'),
			'BWP' => __('Botswana pula', 'woocommerce'),
			'BYR' => __('Belarusian ruble (old)', 'woocommerce'),
			'BYN' => __('Belarusian ruble', 'woocommerce'),
			'BZD' => __('Belize dollar', 'woocommerce'),
			'CAD' => __('Canadian dollar', 'woocommerce'),
			'CDF' => __('Congolese franc', 'woocommerce'),
			'CHF' => __('Swiss franc', 'woocommerce'),
			'CLP' => __('Chilean peso', 'woocommerce'),
			'CNY' => __('Chinese yuan', 'woocommerce'),
			'COP' => __('Colombian peso', 'woocommerce'),
			'CRC' => __('Costa Rican col&oacute;n', 'woocommerce'),
			'CUC' => __('Cuban convertible peso', 'woocommerce'),
			'CUP' => __('Cuban peso', 'woocommerce'),
			'CVE' => __('Cape Verdean escudo', 'woocommerce'),
			'CZK' => __('Czech koruna', 'woocommerce'),
			'DJF' => __('Djiboutian franc', 'woocommerce'),
			'DKK' => __('Danish krone', 'woocommerce'),
			'DOP' => __('Dominican peso', 'woocommerce'),
			'DZD' => __('Algerian dinar', 'woocommerce'),
			'EGP' => __('Egyptian pound', 'woocommerce'),
			'ERN' => __('Eritrean nakfa', 'woocommerce'),
			'ETB' => __('Ethiopian birr', 'woocommerce'),
			'EUR' => __('Euro', 'woocommerce'),
			'FJD' => __('Fijian dollar', 'woocommerce'),
			'FKP' => __('Falkland Islands pound', 'woocommerce'),
			'GBP' => __('Pound sterling', 'woocommerce'),
			'GEL' => __('Georgian lari', 'woocommerce'),
			'GGP' => __('Guernsey pound', 'woocommerce'),
			'GHS' => __('Ghana cedi', 'woocommerce'),
			'GIP' => __('Gibraltar pound', 'woocommerce'),
			'GMD' => __('Gambian dalasi', 'woocommerce'),
			'GNF' => __('Guinean franc', 'woocommerce'),
			'GTQ' => __('Guatemalan quetzal', 'woocommerce'),
			'GYD' => __('Guyanese dollar', 'woocommerce'),
			'HKD' => __('Hong Kong dollar', 'woocommerce'),
			'HNL' => __('Honduran lempira', 'woocommerce'),
			'HRK' => __('Croatian kuna', 'woocommerce'),
			'HTG' => __('Haitian gourde', 'woocommerce'),
			'HUF' => __('Hungarian forint', 'woocommerce'),
			'IDR' => __('Indonesian rupiah', 'woocommerce'),
			'ILS' => __('Israeli new shekel', 'woocommerce'),
			'IMP' => __('Manx pound', 'woocommerce'),
			'INR' => __('Indian rupee', 'woocommerce'),
			'IQD' => __('Iraqi dinar', 'woocommerce'),
			'IRR' => __('Iranian rial', 'woocommerce'),
			'IRT' => __('Iranian toman', 'woocommerce'),
			'ISK' => __('Icelandic kr&oacute;na', 'woocommerce'),
			'JEP' => __('Jersey pound', 'woocommerce'),
			'JMD' => __('Jamaican dollar', 'woocommerce'),
			'JOD' => __('Jordanian dinar', 'woocommerce'),
			'JPY' => __('Japanese yen', 'woocommerce'),
			'KES' => __('Kenyan shilling', 'woocommerce'),
			'KGS' => __('Kyrgyzstani som', 'woocommerce'),
			'KHR' => __('Cambodian riel', 'woocommerce'),
			'KMF' => __('Comorian franc', 'woocommerce'),
			'KPW' => __('North Korean won', 'woocommerce'),
			'KRW' => __('South Korean won', 'woocommerce'),
			'KWD' => __('Kuwaiti dinar', 'woocommerce'),
			'KYD' => __('Cayman Islands dollar', 'woocommerce'),
			'KZT' => __('Kazakhstani tenge', 'woocommerce'),
			'LAK' => __('Lao kip', 'woocommerce'),
			'LBP' => __('Lebanese pound', 'woocommerce'),
			'LKR' => __('Sri Lankan rupee', 'woocommerce'),
			'LRD' => __('Liberian dollar', 'woocommerce'),
			'LSL' => __('Lesotho loti', 'woocommerce'),
			'LYD' => __('Libyan dinar', 'woocommerce'),
			'MAD' => __('Moroccan dirham', 'woocommerce'),
			'MDL' => __('Moldovan leu', 'woocommerce'),
			'MGA' => __('Malagasy ariary', 'woocommerce'),
			'MKD' => __('Macedonian denar', 'woocommerce'),
			'MMK' => __('Burmese kyat', 'woocommerce'),
			'MNT' => __('Mongolian t&ouml;gr&ouml;g', 'woocommerce'),
			'MOP' => __('Macanese pataca', 'woocommerce'),
			'MRU' => __('Mauritanian ouguiya', 'woocommerce'),
			'MUR' => __('Mauritian rupee', 'woocommerce'),
			'MVR' => __('Maldivian rufiyaa', 'woocommerce'),
			'MWK' => __('Malawian kwacha', 'woocommerce'),
			'MXN' => __('Mexican peso', 'woocommerce'),
			'MYR' => __('Malaysian ringgit', 'woocommerce'),
			'MZN' => __('Mozambican metical', 'woocommerce'),
			'NAD' => __('Namibian dollar', 'woocommerce'),
			'NGN' => __('Nigerian naira', 'woocommerce'),
			'NIO' => __('Nicaraguan c&oacute;rdoba', 'woocommerce'),
			'NOK' => __('Norwegian krone', 'woocommerce'),
			'NPR' => __('Nepalese rupee', 'woocommerce'),
			'NZD' => __('New Zealand dollar', 'woocommerce'),
			'OMR' => __('Omani rial', 'woocommerce'),
			'PAB' => __('Panamanian balboa', 'woocommerce'),
			'PEN' => __('Sol', 'woocommerce'),
			'PGK' => __('Papua New Guinean kina', 'woocommerce'),
			'PHP' => __('Philippine peso', 'woocommerce'),
			'PKR' => __('Pakistani rupee', 'woocommerce'),
			'PLN' => __('Polish z&#x142;oty', 'woocommerce'),
			'PRB' => __('Transnistrian ruble', 'woocommerce'),
			'PYG' => __('Paraguayan guaran&iacute;', 'woocommerce'),
			'QAR' => __('Qatari riyal', 'woocommerce'),
			'RON' => __('Romanian leu', 'woocommerce'),
			'RSD' => __('Serbian dinar', 'woocommerce'),
			'RUB' => __('Russian ruble', 'woocommerce'),
			'RWF' => __('Rwandan franc', 'woocommerce'),
			'SAR' => __('Saudi riyal', 'woocommerce'),
			'SBD' => __('Solomon Islands dollar', 'woocommerce'),
			'SCR' => __('Seychellois rupee', 'woocommerce'),
			'SDG' => __('Sudanese pound', 'woocommerce'),
			'SEK' => __('Swedish krona', 'woocommerce'),
			'SGD' => __('Singapore dollar', 'woocommerce'),
			'SHP' => __('Saint Helena pound', 'woocommerce'),
			'SLL' => __('Sierra Leonean leone', 'woocommerce'),
			'SOS' => __('Somali shilling', 'woocommerce'),
			'SRD' => __('Surinamese dollar', 'woocommerce'),
			'SSP' => __('South Sudanese pound', 'woocommerce'),
			'STN' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'woocommerce'),
			'SYP' => __('Syrian pound', 'woocommerce'),
			'SZL' => __('Swazi lilangeni', 'woocommerce'),
			'THB' => __('Thai baht', 'woocommerce'),
			'TJS' => __('Tajikistani somoni', 'woocommerce'),
			'TMT' => __('Turkmenistan manat', 'woocommerce'),
			'TND' => __('Tunisian dinar', 'woocommerce'),
			'TOP' => __('Tongan pa&#x2bb;anga', 'woocommerce'),
			'TRY' => __('Turkish lira', 'woocommerce'),
			'TTD' => __('Trinidad and Tobago dollar', 'woocommerce'),
			'TWD' => __('New Taiwan dollar', 'woocommerce'),
			'TZS' => __('Tanzanian shilling', 'woocommerce'),
			'UAH' => __('Ukrainian hryvnia', 'woocommerce'),
			'UGX' => __('Ugandan shilling', 'woocommerce'),
			'USD' => __('United States (US) dollar', 'woocommerce'),
			'UYU' => __('Uruguayan peso', 'woocommerce'),
			'UZS' => __('Uzbekistani som', 'woocommerce'),
			'VEF' => __('Venezuelan bol&iacute;var', 'woocommerce'),
			'VES' => __('Bol&iacute;var soberano', 'woocommerce'),
			'VND' => __('Vietnamese &#x111;&#x1ed3;ng', 'woocommerce'),
			'VUV' => __('Vanuatu vatu', 'woocommerce'),
			'WST' => __('Samoan t&#x101;l&#x101;', 'woocommerce'),
			'XAF' => __('Central African CFA franc', 'woocommerce'),
			'XCD' => __('East Caribbean dollar', 'woocommerce'),
			'XOF' => __('West African CFA franc', 'woocommerce'),
			'XPF' => __('CFP franc', 'woocommerce'),
			'YER' => __('Yemeni rial', 'woocommerce'),
			'ZAR' => __('South African rand', 'woocommerce'),
			'ZMW' => __('Zambian kwacha', 'woocommerce'),
		);

		return $currencies;
	}

	public static function get_staylodgic_currency_symbols()
	{

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

	public static function get_symbol_for_currency($currency = '')
	{
		$currency = self::get_staylodgic_currencies();
		$symbols  = self::get_staylodgic_currency_symbols();

		$currency_symbol = $symbols[$currency];

		return $currency_symbol;
	}

}
