<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Exit if accessed directly

// Register new Options panel.
$panel_args = array(
	'parent_page'     => 'staylodgic-settings',
	'title'           => 'Hotel Settings',
	'option_name'     => 'staylodgic_settings',
	'slug'            => 'staylodgic-slg-settings-panel',
	'user_capability' => 'edit_posts',
	'tabs'            => array(
		'property'          => '<i class="fa fa-building"></i> <span class="options-menu-link-text">' . esc_html__( 'Property', 'staylodgic' ) . '</span>',
		'activity-property' => '<i class="fa fa-suitcase"></i> <span class="options-menu-link-text">' . esc_html__( 'Activity Property', 'staylodgic' ) . '</span>',
		'currency'          => '<i class="fa fa-dollar"></i> <span class="options-menu-link-text">' . esc_html__( 'Currency', 'staylodgic' ) . '</span>',
		'general'           => '<i class="fa fa-cogs"></i> <span class="options-menu-link-text">' . esc_html__( 'General', 'staylodgic' ) . '</span>',
		'logo'              => '<i class="fa-solid fa-panorama"></i> <span class="options-menu-link-text">' . esc_html__( 'Logo', 'staylodgic' ) . '</span>',
		'discounts'         => '<i class="fa fa-percent"></i> <span class="options-menu-link-text">' . esc_html__( 'Discounts', 'staylodgic' ) . '</span>',
		'mealplan'          => '<i class="fa fa-cutlery"></i> <span class="options-menu-link-text">' . esc_html__( 'Meal Plan', 'staylodgic' ) . '</span>',
		'perperson'         => '<i class="fa fa-user"></i> <span class="options-menu-link-text">' . esc_html__( 'Per person price', 'staylodgic' ) . '</span>',
		'tax'               => '<i class="fa fa-calculator"></i> <span class="options-menu-link-text">' . esc_html__( 'Room Tax', 'staylodgic' ) . '</span>',
		'activity-tax'      => '<i class="fa fa-calculator"></i> <span class="options-menu-link-text">' . esc_html__( 'Activity Tax', 'staylodgic' ) . '</span>',
	),
);

$currencies       = \Staylodgic\Common::get_staylodgic_currencies();
$currency_symbols = \Staylodgic\Common::get_staylodgic_currency_symbols();
$curr_choices     = array();

// Generate the select list
foreach ( $currencies as $currency_code => $currency_name ) {
	$currency_symbol                = $currency_symbols[ $currency_code ];
	$curr_choices[ $currency_code ] = $currency_name . ' ( ' . $currency_symbol . ' )';
}

$country_options = staylodgic_country_list( 'select-array', '' );

$panel_settings = array(
	'property_logo'                 => array(
		'label'       => esc_html__( 'Upload Logo', 'staylodgic' ),
		'type'        => 'media_upload',
		'description' => 'Upload property logo.',
		'tab'         => 'property', // You can change the tab as per your requirement
	),
	'property_logo_width'           => array(
		'label'       => esc_html__( 'On-paper logo width in pixels', 'staylodgic' ),
		'type'        => 'number',
		'default'     => '100',
		'description' => 'Width of registration and invoice logo in pixels.',
		'tab'         => 'property',
	),
	'property_name'                 => array(
		'label' => esc_html__( 'Name', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'property',
	),
	'property_phone'                => array(
		'label' => esc_html__( 'Phone', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'property',
	),
	'property_address'              => array(
		'label' => esc_html__( 'Address', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'property',
	),
	'property_longitude'            => array(
		'label'       => esc_html__( 'Property Longitude', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Longitude',
		'tab'         => 'property',
	),
	'property_latitude'             => array(
		'label'       => esc_html__( 'Property Latitude', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Latitude',
		'tab'         => 'property',
	),
	'property_country'              => array(
		'label'       => esc_html__( 'Country', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'description' => 'Country.',
		'choices'     => $country_options,
		'tab'         => 'property',
	),
	'property_header'               => array(
		'label'       => esc_html__( 'Invoice header', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Header text for invoice',
		'tab'         => 'property',
	),
	'property_footer'               => array(
		'label'       => esc_html__( 'Invoice footer', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Footer text for invoice',
		'tab'         => 'property',
	),
	'property_emailfooter'          => array(
		'label'       => esc_html__( 'Email footer', 'staylodgic' ),
		'type'        => 'textarea',
		'description' => 'Footer text for email',
		'tab'         => 'property',
	),

	'activity_property_logo'        => array(
		'label' => esc_html__( 'Upload Logo', 'staylodgic' ),
		'type'  => 'media_upload',
		'tab'   => 'activity-property', // You can change the tab as per your requirement
	),
	'activity_property_logo_width'  => array(
		'label'       => esc_html__( 'On-paper logo width in pixels', 'staylodgic' ),
		'type'        => 'number',
		'default'     => '100',
		'description' => 'Width of invoice logo in pixels.',
		'tab'         => 'activity-property',
	),
	'activity_property_name'        => array(
		'label' => esc_html__( 'Name', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'activity-property',
	),
	'activity_property_phone'       => array(
		'label' => esc_html__( 'Phone', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'activity-property',
	),
	'activity_property_address'     => array(
		'label' => esc_html__( 'Address', 'staylodgic' ),
		'type'  => 'text',
		'tab'   => 'activity-property',
	),
	'activity_property_longitude'   => array(
		'label'       => esc_html__( 'Property Longitude', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Longitude',
		'tab'         => 'activity-property',
	),
	'activity_property_latitude'    => array(
		'label'       => esc_html__( 'Property Latitude', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Latitude',
		'tab'         => 'activity-property',
	),
	'activity_property_country'     => array(
		'label'       => esc_html__( 'Country', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'description' => 'Country.',
		'choices'     => $country_options,
		'tab'         => 'activity-property',
	),
	'activity_property_header'      => array(
		'label'       => esc_html__( 'Invoice header', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Header text for invoice',
		'tab'         => 'activity-property',
	),
	'activity_property_footer'      => array(
		'label'       => esc_html__( 'Invoice footer', 'staylodgic' ),
		'type'        => 'text',
		'description' => 'Footer text for invoice',
		'tab'         => 'activity-property',
	),
	'activity_property_emailfooter' => array(
		'label'       => esc_html__( 'Email footer', 'staylodgic' ),
		'type'        => 'textarea',
		'description' => 'Footer text for email',
		'tab'         => 'activity-property',
	),
	'main_logo'                     => array(
		'label'       => esc_html__( 'Header logo', 'staylodgic' ),
		'type'        => 'media_upload',
		'description' => 'Upload header logo.',
		'tab'         => 'logo', // You can change the tab as per your requirement
	),
	'main_logo_height'              => array(
		'label'       => esc_html__( 'Header logo height in pixels', 'staylodgic' ),
		'type'        => 'number',
		'default'     => '100',
		'description' => 'Height of header logo in pixels.',
		'tab'         => 'logo',
	),
	'responsive_logo'               => array(
		'label'       => esc_html__( 'Responsive logo', 'staylodgic' ),
		'type'        => 'media_upload',
		'description' => 'Upload header logo.',
		'tab'         => 'logo', // You can change the tab as per your requirement
	),
	'responsive_logo_height'        => array(
		'label'       => esc_html__( 'Responsive logo height in pixels', 'staylodgic' ),
		'type'        => 'number',
		'default'     => '50',
		'description' => 'Height of header logo in pixels.',
		'tab'         => 'logo',
	),
	'discount_lastminute'           => array(
		'label'       => esc_html__( 'Last minute discount', 'staylodgic' ),
		'type'        => 'promotion_discount',
		'description' => 'Maximum days ahead for discount. More than the number of days from booking day the discount will not be applied.',
		'tab'         => 'discounts',
	),
	'discount_earlybooking'         => array(
		'label'       => esc_html__( 'Early booking discount', 'staylodgic' ),
		'type'        => 'promotion_discount',
		'description' => 'How many days ahead to apply discount. Less than the number of days from booking day the discount will not be applied.',
		'tab'         => 'discounts',
	),
	'discount_longstay'             => array(
		'label'       => esc_html__( 'Long stay discount', 'staylodgic' ),
		'type'        => 'promotion_discount',
		'description' => 'Lenght of days to stay to apply discount.',
		'tab'         => 'discounts',
	),
	'enable_taxes'                  => array(
		'label'       => esc_html__( 'Enable Room Taxes', 'staylodgic' ),
		'type'        => 'checkbox',
		'description' => '',
		'tab'         => 'tax',
	),
	'enable_activitytaxes'          => array(
		'label'       => esc_html__( 'Enable Activties Taxes', 'staylodgic' ),
		'type'        => 'checkbox',
		'description' => '',
		'tab'         => 'activity-tax',
	),
	'new_bookingstatus'             => array(
		'label'       => esc_html__( 'Choose status for new bookings', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'description' => 'Choose status for new bookings.',
		'choices'     => staylodgic_get_new_booking_statuses(),
		'tab'         => 'general',
	),
	'new_bookingsubstatus'          => array(
		'label'       => esc_html__( 'Choose sub status for new bookings', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'description' => 'Choose sub status for new bookings.',
		'choices'     => staylodgic_get_booking_substatuses(),
		'tab'         => 'general',
	),
	'timezone'                      => array(
		'label'       => esc_html__( 'Select Time Zone', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'description' => 'Select your time zone relative to GMT.',
		'choices'     => staylodgic_get_gmt_timezone_choices(),
		'tab'         => 'general',
	),
	'sync_interval'                 => array(
		'label'       => esc_html__( 'iCal sync interval', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '100',
		'description' => 'iCal feeds sync interval between another calendar.',
		'choices'     => array(
			'60' => esc_html__( '60', 'staylodgic' ),
			'30' => esc_html__( '30', 'staylodgic' ),
			'15' => esc_html__( '15', 'staylodgic' ),
			'10' => esc_html__( '10', 'staylodgic' ),
			'5'  => esc_html__( '5', 'staylodgic' ),
			'1'  => esc_html__( '1', 'staylodgic' ),
		),
		'tab'         => 'general',
	),
	'max_days_to_process'           => array(
		'label'       => esc_html__( 'Availability Calendar Update Range', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '100',
		'description' => esc_html__( 'Sets the maximum number of future days you can update room rates and availability for in the calendar.', 'staylodgic' ),
		'choices'     => array(
			'64'  => esc_html__( '2 Months', 'staylodgic' ),
			'94'  => esc_html__( '3 Months', 'staylodgic' ),
			'124' => esc_html__( '4 Months', 'staylodgic' ),
			'154' => esc_html__( '5 Months', 'staylodgic' ),
			'184' => esc_html__( '6 Months', 'staylodgic' ),
			'370' => esc_html__( '1 Year', 'staylodgic' ),
		),
		'tab'         => 'general',
	),
	'taxes'                         => array(
		'label'       => esc_html__( 'Room taxes', 'staylodgic' ),
		'type'        => 'repeatable_tax',
		'description' => 'Room taxes',
		'tab'         => 'tax',
	),
	'activity_taxes'                => array(
		'label'       => esc_html__( 'Activity taxes', 'staylodgic' ),
		'type'        => 'activity_repeatable_tax',
		'description' => 'Activity taxes',
		'tab'         => 'activity-tax',
	),
	'childfreestay'                 => array(
		'label'       => esc_html__( 'Children under the age can stay for free', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '100',
		'description' => 'Under which age should be free stay',
		'choices'     => array(
			'0'  => esc_html__( '0', 'staylodgic' ),
			'1'  => esc_html__( '1', 'staylodgic' ),
			'2'  => esc_html__( '2', 'staylodgic' ),
			'3'  => esc_html__( '3', 'staylodgic' ),
			'4'  => esc_html__( '4', 'staylodgic' ),
			'5'  => esc_html__( '5', 'staylodgic' ),
			'6'  => esc_html__( '6', 'staylodgic' ),
			'7'  => esc_html__( '7', 'staylodgic' ),
			'8'  => esc_html__( '8', 'staylodgic' ),
			'9'  => esc_html__( '9', 'staylodgic' ),
			'10' => esc_html__( '10', 'staylodgic' ),
			'11' => esc_html__( '11', 'staylodgic' ),
			'12' => esc_html__( '12', 'staylodgic' ),
			'13' => esc_html__( '13', 'staylodgic' ),
			'14' => esc_html__( '14', 'staylodgic' ),
			'15' => esc_html__( '15', 'staylodgic' ),
			'16' => esc_html__( '16', 'staylodgic' ),
			'17' => esc_html__( '17', 'staylodgic' ),
		),
		'tab'         => 'perperson',
	),
	'perpersonpricing'              => array(
		'label'       => esc_html__( 'Per person price', 'staylodgic' ),
		'type'        => 'repeatable_perperson',
		'description' => 'Price per person.',
		'tab'         => 'perperson',
	),
	'mealplan'                      => array(
		'label'       => esc_html__( 'Meal Plan', 'staylodgic' ),
		'type'        => 'repeatable_mealplan',
		'description' => 'Meal plans',
		'tab'         => 'mealplan',
	),
	'currency'                      => array(
		'label'       => esc_html__( 'Currency', 'staylodgic' ),
		'type'        => 'select',
		'inputwidth'  => '250',
		'default'     => 'USD',
		'description' => 'Choose currency',
		'choices'     => $curr_choices,
		'tab'         => 'currency',
	),
	'currency_position'             => array(
		'label'      => esc_html__( 'Currency position', 'staylodgic' ),
		'type'       => 'select',
		'inputwidth' => '250',
		'default'    => 'left_space',
		'choices'    => array(
			'left_space'  => esc_html__( 'Left with space', 'staylodgic' ),
			'right_space' => esc_html__( 'Right with space', 'staylodgic' ),
			'left'        => esc_html__( 'Left', 'staylodgic' ),
			'right'       => esc_html__( 'Right', 'staylodgic' ),
		),
		'tab'        => 'currency',
	),
	'thousand_seperator'            => array(
		'label'   => esc_html__( 'Thousand seperator', 'staylodgic' ),
		'type'    => 'text',
		'default' => ',',
		'tab'     => 'currency',
	),
	'decimal_seperator'             => array(
		'label'   => esc_html__( 'Decimal seperator', 'staylodgic' ),
		'type'    => 'text',
		'default' => '.',
		'tab'     => 'currency',
	),
	'number_of_decimals'            => array(
		'label'   => esc_html__( 'Number of Decimals', 'staylodgic' ),
		'type'    => 'number',
		'default' => '2',
		'tab'     => 'currency',
	),
);

new \Staylodgic\Options_Panel( $panel_args, $panel_settings );
