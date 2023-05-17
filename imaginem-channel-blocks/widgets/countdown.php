<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget.
 *
 * Elementor widget that displays before and after block.
 *
 * @since 1.0.0
 */
class Imaginem_Countdown extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve icon box widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'imaginem-countdown';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon box widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Countdown', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon box widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-flip-box';
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
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'countdown'];
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
			'section_content',
			[
				'label' => __( 'Countdown', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'datechoice',
			[
				'label' => __( 'Date Choice', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'events' => __( 'Event post End date', 'imaginem-blocks' ),
					'custom' => __( 'Custom Date', 'imaginem-blocks' ),
				],
				'default' => 'events',
			]
		);

		$this->add_control(
			'due_date',
			[
				'label' => __( 'Due Date', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'default' => date( 'Y/m/d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				'condition' => [
					'datechoice' => 'custom',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'imaginem-blocks' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'countdown_color',
			[
				'label' => __( 'Text color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theme-countdown' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Text Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .theme-countdown',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Clock Typography', 'imaginem-blocks' ),
				'name' => 'clock_typography',
				'selector' => '{{WRAPPER}} .theme-countdown .countdown-time',
			]
		);
		$this->add_control(
			'date_color',
			[
				'label' => __( 'Clock color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .theme-countdown .countdown-time' => 'color: {{VALUE}};',
				],
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
		$settings = $this->get_settings_for_display();
		
		
		$id       = 'countdown-' . rand(0,1000);

		if ( 'custom' == $settings['datechoice'] ) {
			$due_date = $settings['due_date'];
		} else {
			if ( is_singular( 'events' ) ) {
				$custom = get_post_custom( get_the_ID() );
				if (isset($custom['pagemeta_event_enddate'][0])) {
					$event_enddate = $custom['pagemeta_event_enddate'][0];
					$due_date = $event_enddate;
				}
			}
		}

		$ended = imaginem_codepack_get_option_data('event_countdown_ended');
		$day = imaginem_codepack_get_option_data('event_countdown_day');
		$days = imaginem_codepack_get_option_data('event_countdown_days');
		$week = imaginem_codepack_get_option_data('event_countdown_week');
		$weeks = imaginem_codepack_get_option_data('event_countdown_weeks');
		$year = imaginem_codepack_get_option_data('event_countdown_year');
		$years = imaginem_codepack_get_option_data('event_countdown_years');

		$countdown = '<div id="' . $id . '" class="theme-countdown" data-countid="'.$id.'" data-ended="'.$ended.'" data-day="'.$day.'" data-days="'.$days.'" data-week="'.$week.'" data-weeks="'.$weeks.'" data-year="'.$year.'" data-years="'.$years.'" data-finaldate="'.$due_date.'"></div>';
	
		echo $countdown;
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
					'field'       => 'title',
					'type'        => __( 'Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'subtitle',
					'type'        => __( 'Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'content',
					'type'        => __( 'Content', 'imaginem-blocks' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Button Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}