<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

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
class Service_Box extends Widget_Base {

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
		return 'service-box';
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
		return __( 'Service Box', 'imaginem-blocks' );
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
		return 'eicon-icon-box';
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
		return [ 'jquery-numerator' ];
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
				'label' => __( 'Service Box', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
			'newicon',
			[
				'label' => __( 'Choose Icon', 'imaginem-blocks' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'stacked' => __( 'Stacked', 'imaginem-blocks' ),
					'framed' => __( 'Framed', 'imaginem-blocks' ),
				],
				'default' => 'default',
				'prefix_class' => 'service-box-view-',
				'condition' => [
					'icon!' => '',
				],
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => __( 'Circle', 'imaginem-blocks' ),
					'square' => __( 'Square', 'imaginem-blocks' ),
				],
				'default' => 'circle',
				'condition' => [
					'view!' => 'default',
					'icon!' => '',
				],
				'prefix_class' => 'service-box-shape-',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .service-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .icon-outer' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding_space',
			[
				'label' => __( 'Icon padding space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .icon-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'icon_space',
			[
				'label' => __( 'Space', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .service-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'iconplace',
			[
				'label' => __( 'Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
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
		

		$this->add_control(
		    'title',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Service Title', 'imaginem-blocks'),
				'default' => __( 'This is the heading', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your title', 'imaginem-blocks' ),
				'label_block' => true,
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'counto',
			[
				'label' => __( 'Count to Number', 'imaginem-blocks' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0,
		    ]
		);

		$this->add_control(
		    'content',
			[
		        'type' => Controls_Manager::TEXTAREA,
		        'label' => __('Service Content', 'imaginem-blocks'),
				'default' => __( 'Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras mattis consectetur purus sit amet fermentum.', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your description', 'imaginem-blocks' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
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

		$this->add_control(
		    'linktext',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Link Text', 'imaginem-blocks'),
				'default' => __( 'Link Text', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter link text', 'imaginem-blocks' ),
				'label_block' => true,
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
		    'iconcolor',
			[
				'label' => __('Icon color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-outer i' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'iconbackground',
			[
				'label' => __('Icon background color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}} .icon-outer' => 'background-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'iconborder',
			[
				'label' => __('Icon border color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'condition' => [
					'view' => 'framed',
				],
				'selectors' => [
					'{{WRAPPER}} .icon-outer' => 'border-color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
			'rotate',
			[
				'label' => __( 'Rotate', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .service-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'iconpadding',
			[
				'label' => __( 'Icon Padding', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .icon-outer' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					'view!' => 'default',
				],
			]
		);

		$this->add_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'imaginem-blocks' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .icon-outer' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'view' => 'framed',
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
					'{{WRAPPER}} .service-content h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} .service-content h4 a' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .service-content h4',
			]
		);

		$this->add_control(
			'count_color',
			[
				'label' => __( 'Count Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .time-count-data' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Count Typography', 'imaginem-blocks' ),
				'name' => 'count_typography',
				'selector' => '{{WRAPPER}} .time-count-data',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .service-content' => 'color: {{VALUE}};',
					'{{WRAPPER}} .service-content p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_color',
			[
				'label' => __( 'Read More Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .service-content .arrow-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .service-content svg g' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .service-content svg path' => 'stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .service-details',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Readmore Typography', 'imaginem-blocks' ),
				'name' => 'readmore_typography',
				'selector' => '{{WRAPPER}} .service-content .theme-hover-arrow',
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

		$url = $settings['link']['url'];
		$url_target = $settings['link']['is_external'];
		$url_nofollow = $settings['link']['nofollow'];

		$the_icon_data = $settings['newicon'];
		$the_icon = $the_icon_data['value'];

		$icon_html = '';

		if ( '' !== $the_icon ) {
			ob_start();
			Icons_Manager::render_icon( $settings['newicon'], [ 'aria-hidden' => 'true' ] );
			$icon_html = ob_get_clean();

			$icon_html = htmlentities( $icon_html );
		}

		$shortcode = '[servicebox column="1" iconplace="'.$settings['iconplace'].'" boxplace="horizontal"] [servicebox_item counto="'.$settings['counto'].'" icon="'.$icon_html.'" drawicon="static" title="'.htmlentities($settings['title']).'" link="'.$url.'" url_target="'.$url_target.'" url_nofollow="'.$url_nofollow.'" linktext="'.htmlentities($settings['linktext']).'" last_item="no"] '.$settings['content'].' [/servicebox_item] [/servicebox]';
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
					'type'        => __( 'Service Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'content',
					'type'        => __( 'Service Content', 'imaginem-blocks' ),
					'editor_type' => 'AREA'
				],
				[
					'field'       => 'linktext',
					'type'        => __( 'Link Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}