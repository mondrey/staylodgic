<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
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
class Imaginem_Timeline_Contents extends Widget_Base {

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
		return 'timeline-contents';
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
		return __( 'Timeline Contents', 'imaginem-blocks' );
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
				'label' => __( 'Timeline Contents', 'imaginem-blocks' ),
			]
		);
		$this->add_responsive_control(
			'padding_space',
			[
				'label' => __( 'Padding Space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .swiperslide-timeline-contents' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .swiperslide-timeline-contents',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Title Tag', 'imaginem-blocks' ),
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'year',[
				'default' => '2020',
				'label' => __( 'Year', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'timecolor',[
				'label' => __( 'Time Background', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}::after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} .timeline-content-slide-date' => 'background-color: {{VALUE}}'
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'timetextcolor',[
				'label' => __( 'Time Text', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .timeline-content-slide-date' => 'color: {{VALUE}}'
				],
				'label_block' => true
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
				'type' => Controls_Manager::WYSIWYG,
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
				'label' => __('Timeline bar line', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline-contents .timeline-dates.swiper-container::after' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .timeline-contents .timeline-button-next:before, {{WRAPPER}} .timeline-contents .timeline-button-prev:before' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'navigationbg',
			[
				'label' => __('Navigation Arrow Background', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline-contents .timeline-button-next, {{WRAPPER}} .timeline-contents .timeline-button-prev' => 'background: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'slidebg',
			[
				'label' => __('Slide Background', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiperslide-timeline-contents' => 'background: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Year Typography', 'imaginem-blocks' ),
				'name' => 'year_typography',
				'selector' => '{{WRAPPER}} .timeline-content-slide-date',
			]
		);
		
		$this->add_control(
		    'titletext',
			[
				'label' => __('Title color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .timeline-title' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .timeline-title',
			]
		);
		
		$this->add_control(
		    'desctext',
			[
				'label' => __('Contents color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-contents-wrap' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .swiper-contents-wrap',
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
		
		$portfolio_timeline = '<div class="swiperslide-timeline-contents">';
		$portfolio_timeline .= '<div class="swiper-timeline-contents-container">';
		$portfolio_timeline .= '<div class="timeline-contents swiper'. $uniqueID .'">';

		$portfolio_timeline .= '<div id="swiper'. $uniqueID .'" class="swiperslide-timeline-contents-container swiper-container" data-autoplay="5000" data-speed="700" data-id="swiper'. $uniqueID .'">';

		$year_count = 0;
		$datecount = 0;

		$portfolio_timeline .= '<div class="swiper-container timeline-dates">';
		$portfolio_timeline .= '<div class="swiper-wrapper">';
		foreach( $settings['timeline'] as $timeline ) {
					$datecount++;
					$portfolio_timeline .= '<div class="swiper-slide elementor-repeater-item-' . $timeline['_id'] . '"><div class="timeline-content-slide-date">'.$timeline['year'].'</div></div>';
		}
		$portfolio_timeline .= '</div>';
		$portfolio_timeline .= '</div>';

		$portfolio_timeline .= '<div class="swiper-container swiper-slide-contents">';
		$portfolio_timeline .= '<div class="swiper-wrapper">';
		foreach( $settings['timeline'] as $timeline ) {

			$year_count++;
			$image_style_tag = '';
			$portfolio_timeline .= '<div class="swiper-slide">';
			$portfolio_timeline .= '<h'.$settings['size'].' class="timeline-title">'.$timeline['title'].'</h4>';
			$portfolio_timeline .= '<div class="swiper-contents-wrap entry-content">';
			$portfolio_timeline .= $timeline['description'];
			$portfolio_timeline .= '</div>';
			$portfolio_timeline .= '</div>';
		}
		$portfolio_timeline .= '</div>';
		$portfolio_timeline .= '</div>';

		$portfolio_timeline .= '<div class="timeline-buttons-container">';
		$portfolio_timeline .= '<div class="timeline-button-next"></div>';
        $portfolio_timeline .= '<div class="timeline-button-prev"></div>';
		$portfolio_timeline .= '</div>';

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