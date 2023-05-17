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
class Imaginem_Timeline extends Widget_Base {

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
		return 'timeline';
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
		return __( 'Timeline', 'imaginem-blocks' );
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
				'label' => __( 'Timeline', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'default' => '6000',
				'type' => 'text',
				'label' => __('Autoplay Interval', 'imaginem-blocks'),
				'desc' => __('Autoplay Interval ( 6000 default). Set 0 to disable', 'imaginem-blocks'),
			]
		);

		$this->add_control(
			'iconplace',
			[
				'label' => __( 'Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'right',
				'prefix_class' => 'timeline-content-alignment-',
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
			]
		);

		$this->add_responsive_control(
			'top_alignment',
			[
				'label' => __( 'Content Top Space', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide-content' => 'top: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'left_alignment',
			[
				'label' => __( 'Content Left Space', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide-content' => 'left: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'description_width',
			[
				'label' => __( 'Description width', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 310,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide-content' => 'max-width: {{SIZE}}px;',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'timelineimage',[
				'label' => __( 'Image', 'imaginem-blocks' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$repeater->add_control(
			'year',[
				'default' => '2020',
				'label' => __( 'Year', 'imaginem-blocks' ),
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
				'label' => __( 'Description', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'button',[
				'name' => 'button',
				'label' => __( 'Button Text', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'link',[
				'label' => __( 'Link URL', 'imaginem-blocks' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'separator' => 'before',
				'label_block' => true,
			]
		);

		$this->add_control(
			'timeline',
			[
				'label' => __( 'Timeline', 'imaginem-blocks' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ title }}',
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
		    'timelinebar',
			[
				'label' => __('Timeline bar', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-pagination' => 'background: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'timelinedates',
			[
				'label' => __('Timeline dates', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-pagination-bullet' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'timelineactivedate',
			[
				'label' => __('Timeline active date', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-pagination-bullet-active' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'timelineactivemark',
			[
				'label' => __('Timeline active mark', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-pagination-bullet::before' => 'background-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'navigation',
			[
				'label' => __('Navigation Arrow', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-button-prev i, {{WRAPPER}} .timeline .swiper-button-next i' => 'border-color: {{VALUE}};color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'slideoverlay',
			[
				'label' => __('Slide Overlay', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide::after' => 'background-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'yearcolor',
			[
				'label' => __('Year color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide .timeline-year' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Year Typography', 'imaginem-blocks' ),
				'name' => 'year_typography',
				'selector' => '{{WRAPPER}} .timeline .swiper-slide .timeline-year',
			]
		);
		
		$this->add_control(
		    'titletext',
			[
				'label' => __('Title color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide .timeline-title' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .timeline .swiper-slide .timeline-title',
			]
		);
		
		$this->add_control(
		    'desctext',
			[
				'label' => __('Description color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .swiper-slide .timeline-text' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .timeline .swiper-slide .timeline-text',
			]
		);

		$this->add_control(
		    'readmore',
			[
				'label' => __('Readmore color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline .arrow-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .timeline .arrow-link svg g' => 'stroke: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Readmore Typography', 'imaginem-blocks' ),
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .arrow-link',
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



		$uniqueID = uniqid();
		//$shortcode = '[swiperslides columns="'.$settings['columns'].'" autoplay="'.$settings['autoplay'].'" lightbox="'.$settings['lightbox'].'" pb_image_ids="'.$pb_image_ids.'"]';
		
		$portfolio_timeline = '<div class="swiperslide-timeline">';
		$portfolio_timeline .= '<div class="swiper-timeline-container">';
		$portfolio_timeline .= '<div class="timeline swiper'. $uniqueID .'">';

		$portfolio_timeline .= '<div id="swiper'. $uniqueID .'" class="swiperslide-timeline-container swiper-container" data-autoplay="' .$settings['autoplay']. '" data-id="swiper'. $uniqueID .'">';
		$portfolio_timeline .= '<div class="swiper-wrapper">';
		
		$year_count = 0;
		foreach( $settings['timeline'] as $timeline ) {

			$nofollow='';
			$target='';
	
			$url = $timeline['link']['url'];
			$url_target = $timeline['link']['is_external'];
			$url_nofollow = $timeline['link']['nofollow'];
	
			if ( '_blank' === $url_target ) {
				$target=' target="_blank"';
			}
			if ( 'on' === $url_target ) {
				$nofollow=' rel="nofollow"';
			}

			$year_count++;
			$image_style_tag = '';
			if ( isset($timeline['timelineimage']['url']) ) {
				$image_style_tag = ' data-background="'.$timeline['timelineimage']['url'].'"';
			}
			$portfolio_timeline .= '<div class="swiper-timeline swiper-slide swiper-lazy"'.$image_style_tag.' data-year="'.$timeline['year'].'">';
			$portfolio_timeline .= '<div class="swiper-slide-content"><span class="timeline-year">'.$timeline['year'].'</span>';
			$portfolio_timeline .= '<h4 class="timeline-title">'.$timeline['title'].'</h4>';
			$portfolio_timeline .= '<p class="timeline-text">'.$timeline['description'].'</p>';
			if ( '' !== $timeline['button'] && '' !== $url ) {
				$portfolio_timeline .= '<div class="timeline-button">';
				$portfolio_timeline .= imaginem_codepack_show_continue_reading( $timeline['button'] , $url);
				$portfolio_timeline .= '</div>';
				//$portfolio_timeline .= '<a href="' . esc_url( $url ) . '" ' . $target . $nofollow . ' class="timeline-button mtheme-button">' . $timeline['button'] . '</a>';
			}
			$portfolio_timeline .= '</div>';
			$portfolio_timeline .= '</div>';
		}

		$portfolio_timeline .= '</div>';


		$portfolio_timeline .= '<div class="swiper-button-prev"><i class="ion-ios-arrow-thin-up"></i></div>';
		$portfolio_timeline .= '<div class="swiper-button-next"><i class="ion-ios-arrow-thin-down"></i></div>';
		$portfolio_timeline .= '<div class="swiper-pagination"></div>';
		
		$portfolio_timeline .= '</div>';
		$portfolio_timeline .= '</div>';
		$portfolio_timeline .= '</div>';
		$portfolio_timeline .= '</div>';
		
		echo $portfolio_timeline;
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