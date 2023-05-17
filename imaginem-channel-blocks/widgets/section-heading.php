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
class Section_Heading extends Widget_Base {

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
		return 'section-heading';
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
		return __( 'Section Heading', 'imaginem-blocks' );
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
		return 'eicon-heading';
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
				'label' => __( 'Section Heading', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'headingsize',
			[
				'label' => __( 'Section Size', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default','imaginem-blocks'),
					'large' => __('Large','imaginem-blocks')
				],
				'prefix_class' => 'section-size-',
				'default' => 'default',
			]
		);

		$this->add_control(
			'headingstyle',
			[
				'label' => __( 'Section Style', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default','imaginem-blocks'),
					'subtitle' => __('Sub Title - Title','imaginem-blocks'),
					'title' => __('Title - Sub Title','imaginem-blocks')
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'subtitlestyle',
			[
				'label' => __( 'Subtitle Style', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default','imaginem-blocks'),
					'sidelines' => __('Sidelines','imaginem-blocks'),
					'reveal' => __('Reveal','imaginem-blocks'),
					'none' => __('None','imaginem-blocks')
				],
				'prefix_class' => 'subtitle-style-',
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'reveal_space',
			[
				'label' => __( 'Reveal Space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading.subtitle-style-reveal .section-sub-title::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'subtitlestyle' => 'reveal',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'imaginem-blocks' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'imaginem-blocks' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'imaginem-blocks' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'prefix_class' => 'section-align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'imaginem-blocks' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your title', 'imaginem-blocks' ),
				'default' => __( 'Add Your Heading Text Here', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Tag', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'1' => 'H1',
					'2' => 'H2',
					'3' => 'H3',
					'4' => 'H4',
					'5' => 'H5',
					'6' => 'H6'
				],
				'default' => '2',
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your subtitle', 'imaginem-blocks' ),
				'default' => __( 'Add Your Subheading Text Here', 'imaginem-blocks' ),
			]
		);

		$this->add_responsive_control(
			'titlewidth',
			[
				'label' => __( 'Title Width', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .section-title' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1400,
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'imaginem-blocks' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'reveal_color',
			[
				'label' => __( 'Subtitle Reveal Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'subtitlestyle' => 'reveal',
				],
				'selectors' => [
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading.subtitle-style-reveal .section-sub-title::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Subtitle Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .section-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtitleline_color',
			[
				'label' => __( 'Subtitle Line Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-default h5::before' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-default h5::after' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-subtitle h5::before' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-subtitle h5::after' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-title h5::after' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}}.elementor-widget-section-heading .headingstyle-title h5::before' => 'background: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'subtitlestyle',
							'operator' => '=',
							'value' => 'sidelines',
						],
						[
							'name' => 'subtitlestyle',
							'operator' => '=',
							'value' => 'default',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'typography',
				'selector' => '.entry-content {{WRAPPER}}.elementor-widget-section-heading .section-title',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Subtitle Typography', 'imaginem-blocks' ),
				'name' => 'subtitletypography',
				'selector' => '.entry-content {{WRAPPER}}.elementor-widget-section-heading .section-sub-title',
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

		$shortcode = '[heading headingstyle="'.$settings['headingstyle'].'" size="'.$settings['size'].'" title="'.htmlspecialchars($settings['title']).'" subtitle="'.htmlspecialchars($settings['subtitle']).'"]';

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
					'field'       => 'title',
					'type'        => __( 'Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'subtitle',
					'type'        => __( 'Subtitle', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description',
					'type'        => __( 'Description', 'imaginem-blocks' ),
					'editor_type' => 'VISUAL'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Button Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Button Two Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
