<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 * @since 1.0.0
 */
class Em_Image_Reel extends Widget_Base {

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
		return 'image-reel';
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
		return __( 'Image Reel', 'imaginem-blocks-ii' );
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
		return 'eicon-info-box';
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
		return [ 'imaginem-media' ];
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
		return [ 'swiper'];
		//return [ 'jarallax', 'parallaxi' ];
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the element requires.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Element styles dependencies.
	 */
	public function get_style_depends() {
		return [ 'swiper'];
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
				'label' => __( 'Image Reel', 'imaginem-blocks-ii' ),
			]
		);

		$this->add_control(
			'wp_gallery',
			[
				'label' => __( 'Add Images', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'autoplayactive',
			[
				'label' => __( 'Autoplay Active', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'no' => __( 'No', 'imaginem-blocks-ii' ),
					'yes' => __( 'Yes', 'imaginem-blocks-ii' ),
				],
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->add_control(
		'autoplay',
		[
			'default' => '5000',
			'type' => 'text',
			'label' => __('Autoplay Interval', 'imaginem-blocks-ii'),
			'desc' => __('Autoplay Interval ( 5000 default)', 'imaginem-blocks-ii'),
			'separator' => 'before',
		]
		);
	
		$this->add_control(
			'swiperpagination',
			[
				'label' => __( 'Pagination', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Default', 'imaginem-blocks-ii' ),
					'no' => __( 'No', 'imaginem-blocks-ii' ),
				],
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'heightstyle',
			[
				'type' => 'select',
				'label' => __('Height', 'imaginem-blocks-ii'),
				'desc' => __('Height style', 'imaginem-blocks-ii'),
				'options' => [
					'none' => __('Default','imaginem-blocks-ii'),
					'full' => __('Full height','imaginem-blocks-ii'),
					'custom' => __('Custom height','imaginem-blocks-ii')
				],
				'default'=>'none',
				'prefix_class' => 'swiper-height-style-',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'desktop_adjustheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Offset height for desktop menu', 'imaginem-blocks-ii'),
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'heightstyle' => 'full',
				],
			]
		);

		$this->add_control(
			'desktopoffsetheight',
			[
				'label' => __( 'Height', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SLIDER,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'desktop_adjustheight',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'heightstyle',
							'operator' => '=',
							'value' => 'full',
						],
					],
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .shortcode-multislider-container.swiper-container.desktopoffset-yes' => 'max-height: calc(100vh - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'mobile_adjustheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Offset height for Mobile Screen', 'imaginem-blocks-ii'),
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'heightstyle' => 'full',
				],
			]
		);

		$this->add_control(
			'mobileoffsetheight',
			[
				'label' => __( 'Height', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SLIDER,
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'mobile_adjustheight',
							'operator' => '=',
							'value' => 'yes',
						],
						[
							'name' => 'heightstyle',
							'operator' => '=',
							'value' => 'full',
						],
					],
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'.mobile-mode-active {{WRAPPER}} .shortcode-multislider-container.swiper-container.mobileoffset-yes' => 'max-height: calc(100vh - {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'customheight',
			[
				'label' => __( 'Height', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => [
					'heightstyle' => 'custom',
				],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 4000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .shortcode-multislider-container.swiper-container,{{WRAPPER}} .shortcode-multislider-container.swiper-container .swiper-slide img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'imaginem-blocks-ii' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'backgroundcolor',
			[
				'label' => __( 'Background Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-multislider-container .swiper-slide' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'paginationcolor',
			[
				'label' => __( 'Pagination Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-multislider-container .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'paginationcoloractive',
			[
				'label' => __( 'Pagination Active Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-multislider-container .swiper-pagination-bullet-active.swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow',
			[
				'label' => __( 'Arrow Color', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev i,{{WRAPPER}} .swiper-button-next i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrowbg',
			[
				'label' => __( 'Arrow Background', 'imaginem-blocks-ii' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev i,{{WRAPPER}} .swiper-button-next i' => 'background: {{VALUE}};',
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

		if ( ! $settings['wp_gallery'] ) {
			return;
		}

		$ids = wp_list_pluck( $settings['wp_gallery'], 'id' );
		$pb_image_ids = implode( ',', $ids );

		$target = '';
		$url    = '';

		$shortcode = '[imagereel url="" mobileoffset="'.$settings['mobile_adjustheight'].'" desktopoffset="'.$settings['desktop_adjustheight'].'" display_title="no" swiperpagination="'.$settings['swiperpagination'].'" display_desc="no" display_button="no" target="" lightbox="no" scrollindicator="disable" slidestyle="slide" columns="0" autoplayactive="' .$settings['autoplayactive']. '" autoplay="'.$settings['autoplay'].'" pb_image_ids="'.$pb_image_ids.'"]';

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
}