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
class Scroll_Indicator extends Widget_Base {

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
		return 'scroll-indicator';
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
		return __( 'Scroll Indicator', 'imaginem-blocks' );
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
		return 'eicon-scroll';
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
				'label' => __( 'Scroll Indicator', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'scroll_align',
			[
				'label' => __( 'Scroll Indicate', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left'   => __( 'Left', 'imaginem-blocks' ),
					'center' => __( 'Center', 'imaginem-blocks' ),
					'right'  => __( 'Right', 'imaginem-blocks' ),
				],
				'default'      => 'center',
				'prefix_class' => 'scroll-indiciate-align-',
			]
		);

		$this->add_control(
		    'link',
			[
		        'type' => Controls_Manager::URL,
		        'label' => __('Link to', 'imaginem-blocks'),
		        'placeholder' => __( 'https://your-link.com', 'imaginem-blocks' ),
				'separator' => 'before',
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
			'text_color',
			[
				'label' => __( 'Indicator Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .mouse-scroll-indicator'        => 'border-color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .mouse-scroll-indicator::after' => 'background: {{VALUE}};',
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
		$settings = $this->get_settings();

		$target = '';

		$url = $settings['link']['url'];
		$url_target = $settings['link']['is_external'];
		$url_nofollow = $settings['link']['nofollow'];

		if ($url_nofollow) {
			$target .=' rel="nofollow"';
		}
		if ($url_target) {
			$target .=' target="_blank"';
		}

		if ( isset( $url ) && '' !== $url ) {
			echo '<a ' . $target . ' href="' . esc_url( $url ) . '">';
		}
		echo '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
		if ( isset( $url ) && '' !== $url ) {
			echo '</a>';
		}
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
}
