<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;

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
class Imaginem_Card_Slider extends Widget_Base {

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
		return 'card-slider';
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
		return __( 'Card Slider', 'imaginem-blocks' );
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'slideimage',[
				'std' => '',
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$repeater->add_control(
			'newicon',[
				'label' => __( 'Choose Icon', 'imaginem-blocks' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		$repeater->add_control(
			'subtitle',[
				'default' => 'Subtitle',
				'label' => __( 'Subtitle', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'title',[
				'default' => 'Title',
				'label' => __( 'Title', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'description',[
				'default' => '',
				'label' => __( 'Description', 'imaginem-blocks' ),
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'link',[
				'type' => Controls_Manager::URL,
				'label' => __('Button Link', 'imaginem-blocks'),
				'placeholder' => __( 'https://your-link.com', 'imaginem-blocks' ),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'button_text',[
				'type' => Controls_Manager::TEXT,
				'label' => __('Button Text', 'imaginem-blocks'),
				'default' => __( 'Button', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter link text', 'imaginem-blocks' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'cards',
			[
				'label' => __( 'Cards', 'imaginem-blocks' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ title }}',
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
			'overlayeffect',
			[
				'type' => 'select',
				'label' => __('Overlay hover effect', 'imaginem-blocks'),
				'desc' => __('Overlay Effect', 'imaginem-blocks'),
				'options' => [
					'zoom-in' => __('Zoom-in','imaginem-blocks'),
					'zoom-out' => __('Zoom-out','imaginem-blocks'),
					'none' => __('None','imaginem-blocks')
				],
				'default'=>'zoom-in',
				'prefix_class' => 'swiper-overlay-',
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
				'label_on' => __( 'Show', 'imaginem-blocks' ),
				'label_off' => __( 'Hide', 'imaginem-blocks' ),
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
				'label_on' => __( 'Show', 'imaginem-blocks' ),
				'label_off' => __( 'Hide', 'imaginem-blocks' ),
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

		$this->add_control(
			'display_subtitle',
			[
				'label' => __( 'Display Subtitle', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
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

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon size', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .infobox-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
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

		$this->add_responsive_control(
			'padding_space',
			[
				'label' => __( 'Padding Space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .infobox-card-container-back,{{WRAPPER}} .infobox-card-container-front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		    'overlay',
			[
				'label' => __('Overlay color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-slides-overlay .card-slider::after' => 'background-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'iconcolor',
			[
				'label' => __('Icon color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .infobox-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .infobox-card-flip h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .infobox-card-flip h2',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .infobox-card-flip p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .infobox-card-flip p',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .text-is-bright .mtheme-button' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'{{WRAPPER}} .text-is-bright .mtheme-button:hover' => 'background-color: {{VALUE}};color: #fff;',
				],
			]
		);

		$this->add_control(
			'button_htextcolor',
			[
				'label' => __( 'Button Hover Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .text-is-bright .mtheme-button:hover' => 'color: {{VALUE}};',
				],
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

		$target = '';
		$nofollow = '';

		$url = $settings['link']['url'];
		$url_target = $settings['link']['is_external'];
		$url_nofollow = $settings['link']['nofollow'];

		$swiperpagination = $settings['swiperpagination'];
		$columns = $settings['columns'];

		$mobileoffset = $settings['mobile_adjustheight'];
		$desktopoffset = $settings['desktop_adjustheight'];
		$display_title = $settings['display_title'];
		$swiperpagination = $settings['swiperpagination'];
		$display_desc = $settings['display_desc'];
		$display_button = $settings['display_button'];
		$scrollindicator = $settings['scrollindicator'];
		$slidestyle = $settings['slidestyle'];
		$columns = $settings['columns'];
		$autoplay = $settings['autoplay'];


		if ($url_nofollow) {
			$target .=' rel="nofollow"';
		}
		if ($url_target) {
			$target .=' target="_blank"';
		}

		$uniqureID=get_the_id()."-".uniqid();
		$galleryID = uniqid();
		
		
		$card_count=0;
		$flag_new_row=true;
		$portfoliogrid='';
		$set_style='';
		$portfoliogrid2='';
		
		$height = '';

		if ($height<>'') {
			$set_style= ' style="height:'.$height.'px;"';
		}
		
		$slide_columntype_class = '';
		$slide_columntype_class = ' card-column-slider';
		

		if ( $columns=="" || $columns=="{{columns}}" ) {
			$columns = "4";
		}

		$lightbox_code = '';
		$carousel = '';

		
		$uniqureID= 'multi-slide-' . get_the_id() . '-' .uniqid();
		$carousel ='<div class="swiper-container-outer swiper-card-slider-outer">';
		$carousel .= '<div id="'.$uniqureID.'"'.$set_style.' class="swiper-container swiper-init swiper-slides-overlay shortcode-swiper-container swiperpagination-'.$swiperpagination.' swiper-columns-'.$columns.$slide_columntype_class.' desktopoffset-'.$desktopoffset.' mobileoffset-'.$mobileoffset.'" data-slidestyle="'.$slidestyle.'" data-swiperpagination="'.$swiperpagination.'" data-autoplay="'.$autoplay.'" data-columns="'.$columns.'" data-desktopoffset="'.$desktopoffset.'" data-mobileoffset="'.$mobileoffset.'" data-id="'.$uniqureID.'">
		<div class="swiper-wrapper">';

		foreach( $settings['cards'] as $cards ) {

			if ( isset($cards['slideimage']['url']) ) {
				$imageURI = $cards['slideimage']['url'];
			}
			// if ( !$check_if_image_present ) {
			// 	continue;
			// }

			$card_count++;
			
			$imageTitle = $cards['title'];
			$imageDesc = $cards['description'];
			$button_text = $cards['button_text'];

			$url = $cards['link']['url'];
			$url_target = $cards['link']['is_external'];
			$url_nofollow = $cards['link']['nofollow'];
	
			if ( '_blank' === $url_target ) {
				$target=' target="_blank"';
			}
			if ( 'on' === $url_target ) {
				$nofollow=' rel="nofollow"';
			}

			ob_start();
			Icons_Manager::render_icon( $cards['newicon'], [ 'aria-hidden' => 'true' ] );
			$icon_html = ob_get_clean();

			$link_text = '';
			$link_url = '';
			$slideshow_link = '';
			$slideshow_color ='';

			$infoboxnumber = '';

			$carousel .= '<div class="swiper-slide card-slider swiper-lazy" data-background="'.esc_url($imageURI).'">';

					$carousel .= '<div class="infobox-wrap infobox-swiperslides text-is-bright">';
					$carousel .= '<div class="infobox-card-flip">';
						$carousel .= '<div class="infobox-card-front">';
							$carousel .= '<div class="infobox-card-container-front">';
								$carousel .= '<div class="infobox-number">'.$infoboxnumber.'</div>';
								$carousel .= '<div class="infobox-inner">';
									$carousel .= '<div class="infobox-icon infobox-animate">';
									$carousel .= $icon_html;
									$carousel .= '</div>';
									if ( 'yes' === $settings['display_subtitle'] ) {
										$carousel .= '<h3 class="infobox-animate">'.$cards['subtitle'].'</h3>';
									}
									if ( 'yes' === $settings['display_title'] ) {
										$carousel .= '<h2 class="infobox-animate">'.$cards['title'].'</h2>';
									}
									if ( 'yes' === $settings['display_desc'] ) {
										$carousel .= '<div class="infobox-animate infobox-contents">'.$cards['description'].'</div>';
									}
									if ( 'yes' === $settings['display_button'] ) {
										if ( isset( $cards['link']['url'] ) && '' !== $cards['link']['url'] ) {
											$carousel .= '<a href="'.esc_url( $cards['link']['url'] ).'" '.$url_target.$nofollow.' class="infobox-animate mtheme-button">'.$cards['button_text'].'</a>';
										} else {
											$carousel .= '<div class="infobox-animate mtheme-button-blank"></div>';
										}
									}
									if ( 'no' === $settings['display_button'] ) {
										$carousel .= '<div class="infobox-animate mtheme-button-blank"></div>';
									}
									$carousel .= '</div>';
								$carousel .= '</div>';
						$carousel .= '</div>';
					$carousel .= '</div>';
				$carousel .= '</div>';

			$carousel .= '</div>';


		}
		$carousel .='</div>';
		if ( $card_count > $columns ) {
			$carousel .='<div class="swiper-pagination"></div>';
			
			$carousel .='<div class="swiper-button-prev"><i class="feather-icon-arrow-left"></i></div>';
			$carousel .='<div class="swiper-button-next"><i class="feather-icon-arrow-right"></i></div>';
		}

		$carousel .='</div>';
		if ( 'enable' === $scrollindicator ) {
			$carousel .= '<a class="mouse-scroll-indicator-link" ' . $target . ' href="' . esc_url( $url ) . '">';
			$carousel .= '<div class="mouse-scroll-indicator-wrap"><div class="mouse-scroll-indicator"></div></div>';
			$carousel .= '</a>';
		}
		$carousel .='</div>';
		
		echo $carousel;
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