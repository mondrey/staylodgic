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
class Testimonials extends Widget_Base {

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
		return 'testimonials';
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
		return __( 'Testimonials', 'imaginem-blocks' );
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
		return 'eicon-testimonial-carousel';
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
			'section_content',
			[
				'label' => __( 'Testimonials', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle'         => __( 'Circle', 'imaginem-blocks' ),
					'square'         => __( 'Square', 'imaginem-blocks' ),
					'fill-overlap'   => __( 'Fill Overlap', 'imaginem-blocks' ),
				],
				'default' => 'circle',
				'prefix_class' => 'testimonial-image-shape-',
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'prefix_class' => 'testimonial-alignment-',
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
			'imageheight',
			[
				'label' => __( 'Image Height', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .testimonials-wrap .client-image' => 'height: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'shape' => 'fill-overlap',
				],
			]
		);

		$this->add_control(
			'columns',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'group_title' => 'Type',
				'label' => __('Columns', 'imaginem-blocks'),
				'desc' => __('Number of Columns', 'imaginem-blocks'),
				'options' => [
					'4' => '4',
					'3' => '3',
					'2' => '2',
					'1' => '1'
				],
				'default' => '1',
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',[
				'std' => '',
				'default' => 'Title',
				'type' => Controls_Manager::TEXT,
				'group_title' => 'Content',
				'label' => __('Staff title', 'imaginem-blocks'),
			]
		);
		$repeater->add_control(
			'thename',[
				'std' => '',
				'default' => 'Name',
				'type' => Controls_Manager::TEXT,
				'label' => __('Staff name', 'imaginem-blocks'),
			]
		);
		$repeater->add_control(
			'company',[
				'std' => '',
				'default' => 'Company',
				'type' => Controls_Manager::TEXT,
				'label' => __('Company name', 'imaginem-blocks'),
			]
		);
		$repeater->add_control(
			'quote',[
				'std' => '',
				'default' => 'Quote',
				'type' => Controls_Manager::TEXTAREA,
				'label' => __('Quote', 'imaginem-blocks'),
			]
		);
		$repeater->add_control(
			'member_image',[
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$repeater->add_control(
			'link',[
				'label' => __( 'Link URL', 'imaginem-blocks' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
				'label_block' => true,
			]
		);

		$this->add_control(
			'testimony',
			[
				'label' => __( 'Repeater List', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Title #1', 'imaginem-blocks' ),
						'thename' => __( 'Jane Doe', 'imaginem-blocks' ),
						'description' => __( 'Description', 'imaginem-blocks' ),
					],
				],
				'title_field' => '{{ thename }}',
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
		    'bordercolor',
			[
				'label' => __('Border color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonials-wrap .client-image' => 'border-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
			'borderwidth',
			[
				'label' => __( 'Border Width', 'imaginem-blocks' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .testimonials-wrap .client-image' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Box Shadow', 'imaginem-blocks' ),
				'selector' => '{{WRAPPER}} .testimonials-wrap .client-image',
			]
		);

		$this->add_control(
			'backgroundoverlay',
			[
				'label' => __( 'Fill Overlay', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-wrap:before,.testimonial-wrap' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quotesymbol',
			[
				'label' => __( 'Quote symbol color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .testimonial-wrap .testimonial-quote:before,{{WRAPPER}} .testimonial-wrap .testimonial-quote:after' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'quote_color',
			[
				'label' => __( 'Quotation Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .client-say' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Quotation Typography', 'imaginem-blocks' ),
				'name' => 'quote_typography',
				'selector' => '{{WRAPPER}} .client-say',
			]
		);

		$this->add_control(
			'companyinfo_color',
			[
				'label' => __( 'Company Info Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .client-info' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Company Info Typography', 'imaginem-blocks' ),
				'name' => 'company_typography',
				'selector' => '{{WRAPPER}} .client-info span',
			]
		);

		$this->add_control(
			'navcolors',
			[
				'label' => __( 'Navigation Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.entry-content {{WRAPPER}} .owl-next' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .owl-prev' => 'border-color: {{VALUE}};color: {{VALUE}};',
				],
				'separator' => 'before',
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


		$child_shortcode = '';
		
		foreach( $settings['testimony'] as $testimony ) {
			$child_shortcode .= '[testimonial columns="'.$settings['columns'].'" imageid="'.$testimony['member_image']['id'].'" image="'.$testimony['member_image']['url'].'" link="'.$testimony['link']['url'].'" link_type="'.$testimony['link']['is_external'].'" nofollow="'.$testimony['link']['nofollow'].'" name="'.htmlspecialchars($testimony['thename']).'" company="'.htmlspecialchars($testimony['company']).'" quote="'.htmlspecialchars($testimony['quote']).'"]';
		}

		$shortcode = '[testimonials columns="'.$settings['columns'].'" autoplayinterval="'.$settings['autoplayinterval'].'" autoplay="'.$settings['autoplay'].'"]'.$child_shortcode.'[/testimonials]';

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
			'conditions'        => [ 'widgetType' => $this->get_name() ],
			'fields'            => array(),
			'integration-class' => 'WPML_Themecore_Testimonials',
		];
		return $widgets;
	}
}