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
class Imaginem_Button extends Widget_Base {

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
		return 'mtheme-button';
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
		return __( 'Theme Button', 'imaginem-blocks' );
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
		return 'eicon-button';
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
				'label' => __( 'Button', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'button_url',
			[
				'label' => __( 'Button Link', 'imaginem-blocks' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'imaginem-blocks' ),
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Button Text', 'imaginem-blocks' ),
				'default' => __( 'Button', 'imaginem-blocks' ),
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
					]
				],
				'default' => '',
				'prefix_class' => 'section-align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style',
			[
				'label' => __( 'Title', 'imaginem-blocks' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_one_style',
			[
				'label' => __( 'Button Style', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __('Default','imaginem-blocks'),
					'solid' => __('Solid','imaginem-blocks')
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_one_color',
			[
				'label' => __( 'Button Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .button-one.mtheme-button' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .button-one.mtheme-button:hover' => 'background-color: {{VALUE}};color: #fff;',
				],
				'separator' => 'before',
				'condition' => [
					'button_one_style' => 'default',
				],
			]
		);

		$this->add_control(
			'button_one_solidcolor',
			[
				'label' => __( 'Button Solid Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .button-one.mtheme-button' => 'background-color: {{VALUE}};border-color: {{VALUE}};color: #fff;',
				],
				'condition' => [
					'button_one_style' => 'solid',
				],
			]
		);

		$this->add_control(
			'button_one_hsolidcolor',
			[
				'label' => __( 'Button Hover Solid Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .button-one.mtheme-button:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};color: #fff;',
				],
				'condition' => [
					'button_one_style' => 'solid',
				],
			]
		);

		$this->add_control(
			'button_one_textcolor',
			[
				'label' => __( 'Button Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .button-one.mtheme-button' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'button_one_htextcolor',
			[
				'label' => __( 'Button Hover Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .button-one.mtheme-button:hover' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Button Typography', 'imaginem-blocks' ),
				'name' => 'buttontypography',
				'selector' => '.entry-content {{WRAPPER}} .mtheme-button',
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

		$url = $settings['button_url']['url'];
		$url_target = $settings['button_url']['is_external'];
		$url_nofollow = $settings['button_url']['nofollow'];

		$shortcode = '[theme_button button_url="'.$url.'" url_target="'.$url_target.'" url_nofollow="'.$url_nofollow.'" button_text="'.htmlspecialchars($settings['button_text']).'"]';

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
					'field'       => 'button_text',
					'type'        => __( 'Button Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
