<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Imaginem Blocks
 *
 * Elementor widget for Imaginem Blocks.
 *
 * @since 1.0.0
 */
class Events_Info extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'events-info';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Events Info', 'imaginem-blocks-ii' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-meta-data';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'imaginem-elements' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Events Info', 'imaginem-blocks-ii' ),
			]
		);

		$this->add_control(
			'liststyle',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('List Style', 'imaginem-blocks-ii'),
				'desc' => __('Listing style of events', 'imaginem-blocks-ii'),
				'options' => [
					'default' => __('Default','imaginem-blocks-ii'),
					'list' => __('Column List','imaginem-blocks-ii')
				],
				'default'=>'default',
				'prefix_class' => 'event-list-style-',
			]
		);

		$this->add_control(
			'when',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display When', 'imaginem-blocks-ii'),
				'desc' => __('Display When', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
			]
		);
		$this->add_control(
			'when_date',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Date', 'imaginem-blocks-ii'),
				'desc' => __('Display Date', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
			]
		);
		$this->add_control(
			'when_time',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Time', 'imaginem-blocks-ii'),
				'desc' => __('Display Time', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
			]
		);

		$this->add_control(
		    'when_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('When Text', 'imaginem-blocks-ii'),
				'default' => __( 'When', 'imaginem-blocks-ii' ),
				'placeholder' => __( 'When', 'imaginem-blocks-ii' ),
				'label_block' => true,
		    ]
		);

		$this->add_control(
			'when_icon',
			[
				'label' => __( 'When Icon', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::ICON,
				'options' => mtheme_elementor_icons(),
				'default' => 'ion-ios-clock',
			]
		);

		$this->add_control(
			'where',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Where', 'imaginem-blocks-ii'),
				'desc' => __('Display Where', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
				'separator' => 'before',
			]
		);

		$this->add_control(
		    'where_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Where Text', 'mthemelocal'),
				'default' => __( 'Where', 'imaginem-blocks-ii' ),
				'placeholder' => __( 'Where', 'imaginem-blocks-ii' ),
				'label_block' => true,
		    ]
		);

		$this->add_control(
			'where_icon',
			[
				'label' => __( 'When Icon', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::ICON,
				'options' => mtheme_elementor_icons(),
				'default' => 'ion-ios-location',
			]
		);

		$this->add_control(
			'cost',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Cost', 'imaginem-blocks-ii'),
				'desc' => __('Display Cost', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
				'separator' => 'before',
			]
		);

		$this->add_control(
		    'cost_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Cost Text', 'imaginem-blocks-ii'),
				'default' => __( 'Cost', 'imaginem-blocks-ii' ),
				'placeholder' => __( 'Cost', 'imaginem-blocks-ii' ),
				'label_block' => true,
		    ]
		);

		$this->add_control(
			'cost_icon',
			[
				'label' => __( 'Cose Icon', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::ICON,
				'options' => mtheme_elementor_icons(),
				'default' => 'ion-ios-pricetag',
			]
		);

		$this->add_control(
			'capacity',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Capacity', 'imaginem-blocks-ii'),
				'desc' => __('Display Capacity', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
				'separator' => 'before',
			]
		);

		$this->add_control(
		    'capacity_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Capacity Text', 'imaginem-blocks-ii'),
				'default' => __( 'Capacity', 'imaginem-blocks-ii' ),
				'placeholder' => __( 'Capacity', 'imaginem-blocks-ii' ),
				'label_block' => true,
		    ]
		);

		$this->add_control(
			'capacity_icon',
			[
				'label' => __( 'Capacity Icon', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::ICON,
				'options' => mtheme_elementor_icons(),
				'default' => 'ion-ios-people',
			]
		);

		$this->add_control(
			'remaining',
			[
				'type' => 'select',
				'group_title' => 'Properties',
				'label' => __('Display Remaining capacity', 'imaginem-blocks-ii'),
				'desc' => __('Display Remaining capacity', 'imaginem-blocks-ii'),
				'options' => [
					'true' => __('Yes','imaginem-blocks-ii'),
					'false' => __('No','imaginem-blocks-ii')
				],
				'default'=>'true',
				'separator' => 'before',
			]
		);

		$this->add_control(
		    'remaining_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Remaining capacity Text', 'imaginem-blocks-ii'),
				'default' => __( 'Remaining capacity', 'imaginem-blocks-ii' ),
				'placeholder' => __( 'Remaining capacity', 'imaginem-blocks-ii' ),
				'label_block' => true,
		    ]
		);
		$this->add_control(
			'remaining_icon',
			[
				'label' => __( 'Remaining Icon', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::ICON,
				'options' => mtheme_elementor_icons(),
				'default' => 'ion-ios-person',
			]
		);

		$this->add_control(
			'status',
			[
				'type' => 'select',
				'group_title' => 'Status',
				'label' => __('Display Status', 'imaginem-blocks-ii'),
				'desc' => __('Display Status', 'imaginem-blocks-ii'),
				'options' => [
					'false' => __('No','imaginem-blocks-ii'),
					'true' => __('Yes','imaginem-blocks-ii')
				],
				'default'=>'true',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'imaginem-blocks-ii' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .events-details-wrap .event-icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'.entry-content {{WRAPPER}} .events-details-wrap .event-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .events-details-wrap .event-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'selector' => '.entry-content {{WRAPPER}} .events-details-wrap .event-heading',
			]
		);

		$this->add_control(
			'info_color',
			[
				'label' => __( 'Info Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .events-details-wrap ul li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '.entry-content {{WRAPPER}} .events-details-wrap ul li',
			]
		);


		$this->add_control(
			'info_link_color',
			[
				'label' => __( 'Info Link Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .events-details-wrap a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'info_link_hover_color',
			[
				'label' => __( 'Info Link Hover Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .events-details-wrap a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_status_bg',
			[
				'label' => __( 'Event Status Background', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .event-status' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'event_status_text',
			[
				'label' => __( 'Event Status Text', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid post style from overwriting
					'.entry-content {{WRAPPER}} .event-status' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'event_status_typography',
				'selector' => '.entry-content {{WRAPPER}} .event-status',
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$shortcode = '[eventinfobox status="'.$settings['status'].'" when="'.$settings['when'].'" when_date="'.$settings['when_date'].'" when_time="'.$settings['when_time'].'" when_text="'.htmlspecialchars($settings['when_text']).'" when_icon="'.$settings['when_icon'].'" where="'.$settings['where'].'" where_text="'.htmlspecialchars($settings['where_text']).'" where_icon="'.$settings['where_icon'].'" cost="'.$settings['cost'].'" cost_text="'.htmlspecialchars($settings['cost_text']).'" cost_icon="'.$settings['cost_icon'].'" capacity="'.$settings['capacity'].'" capacity_text="'.htmlspecialchars($settings['capacity_text']).'" capacity_icon="'.$settings['capacity_icon'].'" remaining="'.$settings['remaining'].'" remaining_icon="'.$settings['remaining_icon'].'" remaining_text="'.htmlspecialchars($settings['remaining_text']).'"]';

		echo do_shortcode($shortcode);
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function content_template() {}

	public function add_wpml_support() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$widgets[ $this->get_name() ] = [
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'when_text',
					'type'        => __( 'When Text', 'imaginem-blocks-ii' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'where_text',
					'type'        => __( 'Where Text', 'imaginem-blocks-ii' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'cost_text',
					'type'        => __( 'Cost Text', 'imaginem-blocks-ii' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
