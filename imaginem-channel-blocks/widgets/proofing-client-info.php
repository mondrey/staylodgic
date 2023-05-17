<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Imaginem Blocks
 *
 * Elementor widget for Imaginem Blocks.
 *
 * @since 1.0.0
 */
class Proofing_Client_Info extends Widget_Base {

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
		return 'proofing-client-info';
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
		return __( 'Proofing Info', 'imaginem-blocks' );
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
		return 'eicon-archive-title';
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
		return [ 'imaginem-proofing' ];
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
				'label' => __( 'Proofing Info', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'centered' => __( 'Centered', 'imaginem-blocks' ),
					'left' => __( 'Left', 'imaginem-blocks' ),
				],
				'default' => 'centered',
				'prefix_class' => 'client-info-style-',
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'curved' => __( 'Curved', 'imaginem-blocks' ),
					'circle' => __( 'Circle', 'imaginem-blocks' ),
					'square' => __( 'Square', 'imaginem-blocks' ),
				],
				'default' => 'curved',
				'prefix_class' => 'client-info-shape-',
			]
		);


		$this->add_responsive_control(
			'image_size',
			[
				'label' => __( 'Client Image Size', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1000,
					],
				],
				'selectors' => [
					'.single-proofing {{WRAPPER}} .proofing-client-details .proofing-client-image img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		'pagetitle',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Page Title', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );

		$this->add_control(
		'client_name',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Client Name', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );

		$this->add_control(
		'client_desc',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Client Description', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );

		$this->add_control(
		'client_time',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Time', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );


		$this->add_control(
		'client_location',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Location', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'imaginem-blocks' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
		    'titlecolor',
			[
				'label' => __('Title Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .proofing-client-details h1' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'titletype',
				'selector' => '.entry-content {{WRAPPER}} .proofing-client-details h1',
			]
		);

		$this->add_control(
		    'clientnamecolor',
			[
				'label' => __('Description Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.editor-is-active.password-protected-client-mode {{WRAPPER}} .proofing-client-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .proofing-client-title' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Client Name Typography', 'imaginem-blocks' ),
				'name' => 'clientnametype',
				'selector' => '.entry-content {{WRAPPER}} .proofing-client-title',
			]
		);

		$this->add_control(
		    'clientdescriptioncolor',
			[
				'label' => __('Client Description Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.editor-is-active.password-protected-client-mode {{WRAPPER}} .proofing-client-desc, .proofing-client-desc' => 'color: {{VALUE}};',
					'{{WRAPPER}} .proofing-client-desc' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Client Description Typography', 'imaginem-blocks' ),
				'name' => 'clientdesctype',
				'selector' => '{{WRAPPER}} .proofing-client-desc',
			]
		);

		$this->add_responsive_control(
			'descriptionwidth',
			[
				'label' => __( 'Description Width', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .proofing-client-details .proofing-client-desc' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		    'clientdetailscolor',
			[
				'label' => __('Client Details Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .proofing-content .event-details > li' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Client Details Typography', 'imaginem-blocks' ),
				'name' => 'clientdetailstype',
				'selector' => '{{WRAPPER}} .proofing-content .event-details > li',
			]
		);

		$this->add_control(
		    'clientdetailsiconcolor',
			[
				'label' => __('Client icon Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .event-details > li i' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Client icon Typography', 'imaginem-blocks' ),
				'name' => 'clientdetailsicontype',
				'selector' => '{{WRAPPER}} .event-details > li i',
			]
		);

		$this->add_responsive_control(
			'infobordersize',
			[
				'label' => __( 'Info Border Size', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'.single-proofing {{WRAPPER}} .proofing-client-image img' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
		    'infobordersizecolor',
			[
				'label' => __('Info Border Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.single-proofing {{WRAPPER}} .proofing-client-image img' => 'border-color: {{VALUE}};',
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

		$shortcode = '[proofing_client_info pagetitle="'.$settings['pagetitle'].'" client_name="'.$settings['client_name'].'" client_desc="'.$settings['client_desc'].'" client_time="'.$settings['client_time'].'" client_location="'.$settings['client_location'].'" ]';
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
