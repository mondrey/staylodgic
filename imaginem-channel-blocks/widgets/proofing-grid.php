<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Icons_Manager;
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
class Proofing_Grid extends Widget_Base {

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
		return 'proofing-grid';
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
		return __( 'Proofing Grid', 'imaginem-blocks' );
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
		return 'eicon-gallery-grid';
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
		return [ 'isotope' ];
		//return [ 'jarallax', 'parallaxi' ];
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
				'label' => __( 'Proofing Gallery', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'proofing_status',
			[
				'label' => __( 'Proofing Status', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'active' => __( 'Active', 'imaginem-blocks' ),
					'lock' => __( 'Lock', 'imaginem-blocks' ),
				],
				'default' => 'active',
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
		'style',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT,
		        'label' => __('Style of Proofing', 'imaginem-blocks'),
		        'desc' => __('Style of Proofing', 'imaginem-blocks'),
		        'options' => [
		            'wall-spaced' => __('Box Spaced', 'imaginem-blocks'),
		            'wall-grid' => __('Box Grid', 'imaginem-blocks'),
		        ],
		        'default' => 'wall-spaced',
		    ]
		);

		$this->add_control(
		    'effect',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT,
		        'label' => __('Hover Effect', 'imaginem-blocks'),
		        'desc' => __('Hover Effect', 'imaginem-blocks'),
		        'options' => [
		            'default' => __('Default', 'imaginem-blocks'),
		            'none' => __('None', 'imaginem-blocks'),
		            'tilt' => __('Tilt', 'imaginem-blocks'),
		            'blur' => __('Blur', 'imaginem-blocks'),
		            'zoom' => __('Zoom', 'imaginem-blocks')
		        ],
		        'default' => 'default',
		    ]
		);

		$this->add_control(
		'type',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Proofing listing', 'imaginem-blocks'),
		    'desc' => __('Proofing listing', 'imaginem-blocks'),
		    'options' => [
		    	'filter' => __('Filterable', 'imaginem-blocks'),
		        'no-filter' => __('No Filter', 'imaginem-blocks')
		    ],
		    'default' => 'filter',
            ]
        );

		$this->add_control(
		'format',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Image format', 'imaginem-blocks'),
		    'desc' => __('Image format', 'imaginem-blocks'),
		    'options' => [
		        'landscape' => __('Landscape','imaginem-blocks'),
		        'square' => __('Square','imaginem-blocks'),
		        'portrait' => __('Portrait','imaginem-blocks'),
		        'masonary' => __('Masonry','imaginem-blocks')
		    ],
			'default' => 'landscape',
            ]
        );

		$this->add_control(
		'download',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Download in Thumbnail', 'imaginem-blocks'),
		    'desc' => __('title', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );

		$this->add_control(
		'title',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Title', 'imaginem-blocks'),
		    'desc' => __('title', 'imaginem-blocks'),
		    'options' => [
		        'imageid' => __('Image ID','imaginem-blocks'),
		        'filename' => __('File Name','imaginem-blocks'),
		        'imagetitle' => __('Image Title','imaginem-blocks'),
		        'false' => __('None','imaginem-blocks')
		    ],
			'default' => 'imageid',
            ]
        );

		$this->add_control(
			'newicon',
			[
				'label' => __( 'Notice Icon', 'imaginem-blocks' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
			]
		);
		
		$this->add_control(
			'notice',
			[
				'label' => __( 'Notice', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Notice text for client', 'imaginem-blocks' ),
				'default' => '',
			]
		);

		$this->add_control(
			'button_url',
			[
				'label' => __( 'Button Link', 'imaginem-blocks' ),
				'desc' => __('Can be used to provide zipped link of gallery', 'imaginem-blocks'),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'imaginem-blocks' ),
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __( 'Button Text', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter button text', 'imaginem-blocks' ),
				'default' => __( 'Button', 'imaginem-blocks' ),
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
					'{{WRAPPER}} .work-details h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} .work-details h4 a' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'titletype',
				'selector' => '{{WRAPPER}} .work-details h4,{{WRAPPER}} .work-details h4 a',
			]
		);

		$this->add_control(
		    'thumbnailhover',
			[
				'label' => __('Thumbnail Hover Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .gridblock-background-hover' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'iconcolor',
			[
				'label' => __('Icon Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .column-gridblock-icon i' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'iconhovercolor',
			[
				'label' => __('Icon Hover Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .column-gridblock-icon:hover i' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'iconbackgroundcolor',
			[
				'label' => __('Icon Background Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .column-gridblock-icon::after' => 'border-color: {{VALUE}};background-color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'iconhoverbackgroundcolor',
			[
				'label' => __('Icon Background Hover Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .column-gridblock-icon:hover::after' => 'border-color: {{VALUE}};background-color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'filter',
			[
				'label' => __('Filter Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} #proofing-status-count' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_control(
		    'filterline',
			[
				'label' => __('Filter Line', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters ul' => 'border-color: {{VALUE}};',
				],
		    ]
		);
		$this->add_control(
		    'filterhover',
			[
				'label' => __('Filter Hover Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters li a:hover' => 'color: {{VALUE}};',
				],
		    ]
		);
		$this->add_control(
		    'filteractive',
			[
				'label' => __('Filter Active Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters li a::after' => 'background: {{VALUE}};',
					'{{WRAPPER}} #gridblock-filters li a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} #gridblock-filters a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} #gridblock-filters li .is-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} #gridblock-filters li .is-active:hover' => 'color: {{VALUE}};',
				],
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Filter Typography', 'imaginem-blocks' ),
				'name' => 'filtertype',
				'selector' => '{{WRAPPER}} #gridblock-filters li a',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Selected Count Typography', 'imaginem-blocks' ),
				'name' => 'selectedtype',
				'selector' => '{{WRAPPER}} #proofing-status-count',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Notice Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .proofing-download-description' => 'color: {{VALUE}};',
					'.entry-content {{WRAPPER}} proofing-download-description p' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_background',
			[
				'label' => __( 'Notice Background', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .proofing-download-section' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Notice Typography', 'imaginem-blocks' ),
				'name' => 'noticetype',
				'selector' => '{{WRAPPER}} .proofing-download-section',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .proofing-download-section .mtheme-button' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .proofing-download-section .mtheme-button:hover' => 'background-color: {{VALUE}};color: #fff;',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_htextcolor',
			[
				'label' => __( 'Button Hover Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .proofing-download-section .mtheme-button:hover' => 'color: {{VALUE}};',
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

		$url = $settings['button_url']['url'];
		$url_target = $settings['button_url']['is_external'];
		$url_nofollow = $settings['button_url']['nofollow'];

		$the_icon_data = $settings['newicon'];
		$the_icon = $the_icon_data['value'];

		$icon_html = '';

		if ( '' !== $the_icon ) {
			ob_start();
			Icons_Manager::render_icon( $settings['newicon'], [ 'aria-hidden' => 'true' ] );
			$icon_html = ob_get_clean();

			$icon_html = htmlentities( $icon_html );
		}

		$shortcode = '[proofing_gallery notice="'.htmlspecialchars($settings['notice']).'" icon="'.$icon_html.'" button_url="'.$url.'" url_target="'.$url_target.'" url_nofollow="'.$url_nofollow.'" button_text="'.htmlspecialchars($settings['button_text']).'" download="'.$settings['download'].'" proofingstatus="'.$settings['proofing_status'].'" effect="'.$settings['effect'].'" type="'.$settings['type'].'" style="'.$settings['style'].'" columns="'.$settings['columns'].'" format="'.$settings['format'].'" title="'.$settings['title'].'"]';
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
					'field'       => 'notice',
					'type'        => __( 'Enter Notice text', 'imaginem-blocks' ),
					'editor_type' => 'VISAL'
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Button Text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
