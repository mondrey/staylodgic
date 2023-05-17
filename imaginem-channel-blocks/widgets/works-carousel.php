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
class Em_Works_Carousel extends Widget_Base {

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
		return 'em-works-carousel';
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
		return __( 'Works Carousel', 'imaginem-blocks' );
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
		return 'eicon-slider-push';
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
		return [ 'imaginem-portfolio' ];
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
		return [ 'owlcarousel'];
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
		return [ 'owlcarousel'];
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
				'label' => __( 'Works Carousel', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
		'format',
		[
			'type' => 'select',
			'label' => __('Carousel image type', 'imaginem-blocks'),
			'desc' => __('Carousel image type', 'imaginem-blocks'),
			'options' => [
				'landscape' => __('Landscape','imaginem-blocks'),
				'square' => __('Square','imaginem-blocks'),
				'portrait' => __('Portrait','imaginem-blocks')
			],
			'default'=>'landscape',
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
				'default' => '3',
			]
		);

		$this->add_control(
			'limit',
			[
				'std' => '-1',
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => __('Limit. -1 for unlimited', 'imaginem-blocks'),
				'desc' => __('Limit items. -1 for unlimited', 'imaginem-blocks'),
				'default' => '-1',
				]
		);

		$this->add_control(
			'directlink',
			[
				'type' => 'select',
				'label' => __('Direct Link', 'imaginem-blocks'),
				'desc' => __('Direct link to portfolio items', 'imaginem-blocks'),
				'options' => [
					'false' => __('No','imaginem-blocks'),
					'true' => __('Yes','imaginem-blocks')
				],
				'default'=>'false',
			]
			);

		$this->add_control(
		'lazyload',
		[
			'type' => 'select',
			'group_title' => 'Properties',
			'label' => __('Lazy Load', 'imaginem-blocks'),
			'desc' => __('Lazy Load', 'imaginem-blocks'),
			'options' => [
				'false' => __('No','imaginem-blocks'),
				'true' => __('Yes','imaginem-blocks')
			],
			'default'=>'false',
		]
		);

		$this->add_control(
		'autoplay',
		[
			'type' => 'select',
			'std' => 'false',
			'label' => __('Autoplay slideshow', 'imaginem-blocks'),
			'desc' => __('Autoplay slideshow', 'imaginem-blocks'),
			'options' => [
				'false' => __('No','imaginem-blocks'),
				'true' => __('Yes','imaginem-blocks')
			],
			'default'=>'false',
		]
		);

		$this->add_control(
		'autoplayinterval',
		[
			'default' => '5000',
			'type' => 'text',
			'label' => __('Autoplay Interval', 'imaginem-blocks'),
			'desc' => __('Autoplay Interval ( 5000 default)', 'imaginem-blocks'),
		]
		);

		$this->add_control(
		'smartspeed',
		[
			'default' => '1000',
			'type' => 'text',
			'label' => __('Slide Transition', 'imaginem-blocks'),
			'desc' => __('Slide Transition ( 1000 default)', 'imaginem-blocks'),
		]
		);

		$this->add_control(
		'displaytitle',
		[
			'type' => 'select',
			'label' => __('Dispay title', 'imaginem-blocks'),
			'desc' => __('Display title', 'imaginem-blocks'),
			'options' => [
				'true' => __('Yes','imaginem-blocks'),
				'false' => __('No','imaginem-blocks')
			],
			'default'=>'true',
		]
		);

		$this->add_control(
		'category_display',
		[
			'type' => 'select',
			'label' => __('Dispay category', 'imaginem-blocks'),
			'desc' => __('Display category', 'imaginem-blocks'),
			'options' => [
				'true' => __('Yes','imaginem-blocks'),
				'false' => __('No','imaginem-blocks')
			],
			'default'=>'false',
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
			'carouselpagination',
			[
				'label' => __( 'Pagination Dots', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'yes' => __( 'Yes', 'imaginem-blocks' ),
					'no' => __( 'No', 'imaginem-blocks' ),
				],
				'default' => 'yes',
				'prefix_class' => 'carousel-dots-',
			]
		);

		$this->add_control(
			'paginationcolor',
			[
				'label' => __( 'Pagination Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .owl-dot span' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .owl-dot.active span' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrowshape',
			[
				'label' => __( 'Arrow', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'circle' => __( 'Circle', 'imaginem-blocks' ),
					'square' => __( 'Square', 'imaginem-blocks' ),
				],
				'default' => 'default',
				'separator' => 'before',
				'prefix_class' => 'carousel-arrow-shape-',
			]
		);

		$this->add_control(
			'arrowbackground',
			[
				'label' => __( 'Arrow Background Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .owl-next,{{WRAPPER}} .owl-prev' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .owl-next,{{WRAPPER}} .owl-prev' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .gridblock-grid-element .boxtitle-hover a' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .gridblock-grid-element .boxtitle-hover a',
			]
		);

		$this->add_control(
			'category_color',
			[
				'label' => __( 'Category Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .gridblock-grid-element .boxtitle-worktype' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Category Typography', 'imaginem-blocks' ),
				'name' => 'category_typography',
				'selector' => '{{WRAPPER}} .gridblock-grid-element .boxtitle-worktype',
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

		//$shortcode = '[lightboxcarousel pagination="'.$settings['carouselpagination'].'" columns="'.$settings['columns'].'" format="'.$settings['format'].'" smartspeed="'.$settings['smartspeed'].'" lazyload="'.$settings['lazyload'].'" autoplayinterval="'.$settings['autoplayinterval'].'" lightbox="'.$settings['lightbox'].'" autoplay="'.$settings['autoplay'].'" displaytitle="'.$settings['displaytitle'].'" pb_image_ids="'.$pb_image_ids.'"]';
		$shortcode = '[workscarousel limit="'.$settings['limit'].'" directlink="'.$settings['directlink'].'" category_display="'.$settings['category_display'].'" boxtitle="'.$settings['displaytitle'].'" pagination="'.$settings['carouselpagination'].'" format="'.$settings['format'].'" worktype_slug="" autoplayinterval="'.$settings['autoplayinterval'].'" lazyload="'.$settings['lazyload'].'" autoplay="'.$settings['autoplay'].'" columns="'.$settings['columns'].'"]';

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