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
class Swiper_Slides extends Widget_Base {

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
		return 'swiper-slides';
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
		return __( 'Multi Slides', 'imaginem-blocks' );
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
				'label' => __( 'Swiper Slideshow', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
			'wp_gallery',
			[
				'label' => __( 'Add Images', 'imaginem-blocks' ),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'4' => __( '4', 'imaginem-blocks' ),
					'3' => __( '3', 'imaginem-blocks' ),
					'2' => __( '2', 'imaginem-blocks' ),
					'1' => __( '1', 'imaginem-blocks' ),
				],
				'default' => '2',
			]
		);

		$this->add_control(
			'slideshowimage_mode',
			[
				'label' => __( 'Slideshow Image', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'cover' => __( 'Cover', 'imaginem-blocks' ),
					'fit'   => __( 'Fit', 'imaginem-blocks' ),
				],
				'default'      => 'cover',
				'prefix_class' => 'slideshow-image-display-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'background_overlay_opacity',
			[
				'label' => __( 'Overlay Opacity', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .25,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-slides-overlay .swiper-slide:after' => 'opacity: {{SIZE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
		'autoplay',
		[
			'default' => '5000',
			'type' => 'text',
			'label' => __('Autoplay Interval', 'imaginem-blocks'),
			'desc' => __('Autoplay Interval ( 5000 default)', 'imaginem-blocks'),
			'separator' => 'before',
		]
		);

		$this->add_control(
			'slidestyle',
			[
				'type' => 'select',
				'label' => __('Slide style', 'imaginem-blocks'),
				'desc' => __('Slide style', 'imaginem-blocks'),
				'options' => [
					'slide' => __('Slide','imaginem-blocks'),
					'fade' => __('Fade','imaginem-blocks')
				],
				'condition' => [
					'columns' => '1',
				],
				'default'=>'slide',
			]
			);
	
		$this->add_control(
			'swiperpagination',
			[
				'label' => __( 'Pagination', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Default', 'imaginem-blocks' ),
					'fraction' => __( 'Fraction', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => __( 'Lightbox', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'no' => __( 'No', 'imaginem-blocks' ),
					'yes' => __( 'Yes', 'imaginem-blocks' ),
				],
				'default' => 'no',
			]
		);

		$this->add_control(
			'heightstyle',
			[
				'type' => 'select',
				'label' => __('Height', 'imaginem-blocks'),
				'desc' => __('Height style', 'imaginem-blocks'),
				'options' => [
					'none' => __('Default','imaginem-blocks'),
					'full' => __('Full height','imaginem-blocks'),
					'custom' => __('Custom height','imaginem-blocks')
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
				'label' => __('Offset height for desktop menu', 'imaginem-blocks'),
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
			'mobile_adjustheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Offset height for responsive menu', 'imaginem-blocks'),
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'heightstyle' => 'full',
				],
			]
		);

		$this->add_responsive_control(
			'customheight',
			[
				'label' => __( 'Height', 'imaginem-blocks' ),
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
					'{{WRAPPER}} .shortcode-swiper-container.swiper-container' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'scrollindicator',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __('Scroll Indicator', 'imaginem-blocks'),
				'desc' => __('Style of Thumbnails', 'imaginem-blocks'),
				'options' => [
					'disable' => __('Disable', 'imaginem-blocks'),
					'enable' => __('Enable', 'imaginem-blocks'),
				],
				'default' => 'disable',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'scroll_align',
			[
				'label' => __( 'Scroll indicator align', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left'   => __( 'Left', 'imaginem-blocks' ),
					'center' => __( 'Center', 'imaginem-blocks' ),
					'right'  => __( 'Right', 'imaginem-blocks' ),
				],
				'condition' => [
					'scrollindicator' => 'enable',
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
				'condition' => [
					'scrollindicator' => 'enable',
				],
		    ]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contenttext',
			[
				'label' => __( 'Slideshow Content', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'slideshowtext_align',
			[
				'label' => __( 'Slideshow Text Align', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left'   => __( 'Left', 'imaginem-blocks' ),
					'center' => __( 'Center', 'imaginem-blocks' ),
					'right'  => __( 'Right', 'imaginem-blocks' ),
				],
				'default'      => 'left',
				'prefix_class' => 'slideshow-text-align-',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'left_alignment',
			[
				'label' => __( 'Content Left Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'slideshowtext_align!' => 'center',
				],
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-contents' => 'left: {{SIZE}}%;',
				],
			]
		);
		$this->add_responsive_control(
			'bottom_alignment',
			[
				'label' => __( 'Content Bottom Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-contents' => 'bottom: {{SIZE}}%;',
				],
			]
		);
		$this->add_responsive_control(
			'content_space',
			[
				'label' => __( 'Content Padding', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-contents' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'slideshowtext_align!' => 'center',
				],
			]
		);
		$this->add_responsive_control(
			'description_width',
			[
				'label' => __( 'Description width', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 360,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-desc' => 'max-width: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'display_title',
			[
				'label' => __( 'Display Title', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
			]
		);

		$this->add_control(
			'display_desc',
			[
				'label' => __( 'Display Description', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
			]
		);

		$this->add_control(
			'display_button',
			[
				'label' => __( 'Display Button', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
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
			'paginationcolor',
			[
				'label' => __( 'Pagination Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-pagination-bullet' => 'background: {{VALUE}};',
					'{{WRAPPER}} .shortcode-swiper-container .swiper-pagination-bullet::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'paginationcoloractive',
			[
				'label' => __( 'Pagination Active Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-pagination-bullet-active.swiper-pagination-bullet' => 'background: {{VALUE}};',
					'{{WRAPPER}} .shortcode-swiper-container .swiper-pagination-bullet-active.swiper-pagination-bullet::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow',
			[
				'label' => __( 'Arrow Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .swiper-button-prev i,{{WRAPPER}} .swiper-button-next i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .shortcode-swiper-container .swiper-button-next,{{WRAPPER}} .shortcode-swiper-container .swiper-button-prev' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-title' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .shortcode-swiper-container .swiper-title',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .shortcode-swiper-container .swiper-desc' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .shortcode-swiper-container .swiper-desc',
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
				'label' => __( 'Button One Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button:hover' => 'background-color: {{VALUE}};color: #fff;',
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
				'label' => __( 'Button One Solid Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button' => 'background-color: {{VALUE}};border-color: {{VALUE}};color: #fff;',
				],
				'condition' => [
					'button_one_style' => 'solid',
				],
			]
		);

		$this->add_control(
			'button_one_hsolidcolor',
			[
				'label' => __( 'Button One Hover Solid Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};color: #fff;',
				],
				'condition' => [
					'button_one_style' => 'solid',
				],
			]
		);

		$this->add_control(
			'button_one_textcolor',
			[
				'label' => __( 'Button One Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'button_one_htextcolor',
			[
				'label' => __( 'Button One Hover Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .swiper-contents .mtheme-button:hover' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'indicator_color',
			[
				'label' => __( 'Scroll Indicator', 'imaginem-blocks' ),
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

		if ( ! $settings['wp_gallery'] ) {
			return;
		}

		$ids = wp_list_pluck( $settings['wp_gallery'], 'id' );
		$pb_image_ids = implode( ',', $ids );

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

		$shortcode = '[swiperslides url="'.$url.'" mobileoffset="'.$settings['mobile_adjustheight'].'" desktopoffset="'.$settings['desktop_adjustheight'].'" display_title="'.$settings['display_title'].'" swiperpagination="'.$settings['swiperpagination'].'" display_desc="'.$settings['display_desc'].'" display_button="'.$settings['display_button'].'" target="'.$target.'" lightbox="'.$settings['lightbox'].'" scrollindicator="'.$settings['scrollindicator'].'" slidestyle="'.$settings['slidestyle'].'" columns="'.$settings['columns'].'" autoplay="'.$settings['autoplay'].'" pb_image_ids="'.$pb_image_ids.'"]';

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