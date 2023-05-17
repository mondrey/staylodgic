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
class Portfolio_Grid extends Widget_Base {

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
		return 'portfolio-grid';
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
		return __( 'Portfolio Grid', 'imaginem-blocks' );
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
				'label' => __( 'Portfolio Gallery', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'5' => __( '5', 'imaginem-blocks' ),
					'4' => __( '4', 'imaginem-blocks' ),
					'3' => __( '3', 'imaginem-blocks' ),
					'2' => __( '2', 'imaginem-blocks' ),
					'1' => __( '1', 'imaginem-blocks' ),
				],
				'default' => '3',
			]
		);

		$this->add_control(
		    'worktype_slugs',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT2,
		        'label' => __('Choose Work types to list', 'imaginem-blocks'),
		        'options' => themecore_elementor_categories('worktypes'),
		        'multiple' => true,
		        'default' => '',
		        'label_block' => true,
		    ]
		);

		$this->add_control(
		'style',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT,
		        'label' => __('Style of Portfolio', 'imaginem-blocks'),
		        'desc' => __('Style of Portfolio', 'imaginem-blocks'),
		        'options' => [
		            'classic' => __('Classic', 'imaginem-blocks'),
		            'wall-spaced' => __('Box Spaced', 'imaginem-blocks'),
					'wall-grid' => __('Box Grid', 'imaginem-blocks'),
		            'card-spaced' => __('Card Spaced', 'imaginem-blocks'),
		            'card-grid' => __('Card Grid', 'imaginem-blocks'),
		        ],
		        'default' => 'classic',
		    ]
		);

		$this->add_control(
			'animated_hovercard',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __('Animated Titles on Hover', 'imaginem-blocks'),
				'desc' => __('Animated Titles on Hover', 'imaginem-blocks'),
				'default' => 'enable',
				'options' => [
					'enable'  => __('Enable','imaginem-blocks'),
					'disable' => __('Disable','imaginem-blocks')
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-spaced',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'card_height',
			[
				'label' => __( 'Card Height', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 300,
						'max' => 800,
					],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-spaced',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gridblock-element-card' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'viewmore',
			[
			    'type' => 'text',
			    'label' => __('View more text', 'imaginem-blocks'),
				'default' => 'View Project',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-spaced',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'gutter_space',
			[
				'label' => __( 'Gutter Space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} #gridblock-container.portfolio-gutter-nospace .gridblock-element' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'wall-grid',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} #gridblock-container .gridblock-element-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}} #gridblock-container.grid-style-wall-grid .gridblock-grid-element' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}} #gridblock-container.grid-style-wall-spaced .gridblock-grid-element' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'card-spaced',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'wall-spaced',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'wall-grid',
						],
					],
				],
			]
		);

		$this->add_control(
		'boxthumbnail_link',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT,
		        'label' => __('Box Thumbnail Link', 'imaginem-blocks'),
		        'desc' => __('Box Thumbnail Link', 'imaginem-blocks'),
		        'options' => [
		            'direct' => __('Direct Link', 'imaginem-blocks'),
		            'lightbox' => __('Lightbox', 'imaginem-blocks'),
		        ],
		        'default' => 'direct',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'wall-grid',
						],
						[
							'name' => 'style',
							'operator' => '=',
							'value' => 'wall-spaced',
						],
					],
				],
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
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'style',
							'operator' => '!=',
							'value' => 'card-grid',
						],
						[
							'name' => 'style',
							'operator' => '!=',
							'value' => 'card-spaced',
						],
					],
				],
		        'default' => 'default',
		    ]
		);
		$this->add_control(
		'type',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Type of Portfolio list', 'imaginem-blocks'),
		    'desc' => __('Type of Portfolio list', 'imaginem-blocks'),
		    'options' => [
		        'no-filter' => __('No Filter', 'imaginem-blocks'),
		        'filter' => __('Filterable', 'imaginem-blocks')
		    ],
		    'default' => 'no-filter',
            ]
		);
		
		$this->add_control(
			'filterall',
			[
			    'type' => 'text',
			    'label' => __('Filter text for all filters', 'imaginem-blocks'),
				'desc' => __('Filter text for all filters', 'imaginem-blocks'),
				'default' => 'All',
				'condition' => [
					'type' => 'filter',
				],
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
			'conditions' => [
				'relation' => 'and',
				'terms' => [
					[
						'name' => 'style',
						'operator' => '!=',
						'value' => 'card-grid',
					],
					[
						'name' => 'style',
						'operator' => '!=',
						'value' => 'card-spaced',
					],
				],
			],
			'default' => 'landscape',
            ]
		);
		
		$this->add_control(
			'imagesize',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __('Image Size', 'imaginem-blocks'),
				'desc' => __('Image Size', 'imaginem-blocks'),
				'options' => [
					'defaultsize'  => __('Default','imaginem-blocks'),
					'fullsize' => __('Original','imaginem-blocks')
				],
				'default' => 'defaultsize',
				'condition' => [
					'format' => 'masonary',
				],
			]
		);

		$this->add_control(
		'category_display',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Display Worktypes', 'imaginem-blocks'),
		    'desc' => __('Display Worktypes', 'imaginem-blocks'),
		    'options' => [
		        'no' => __('No','imaginem-blocks'),
		        'yes' => __('Yes','imaginem-blocks')
		    ],
			'default' => 'no',
            ]
        );
		$this->add_control(
		'like',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Display like/heart', 'imaginem-blocks'),
		    'desc' => __('Displays like/heart', 'imaginem-blocks'),
		    'options' => [
		        'no' => __('No','imaginem-blocks'),
		        'yes' => __('Yes','imaginem-blocks')
		    ],
			'default' => 'no',
            ]
        );
		$this->add_control(
		'title',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Title', 'imaginem-blocks'),
		    'desc' => __('title', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
            ]
        );
		$this->add_control(
		'desc',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Description', 'imaginem-blocks'),
		    'desc' => __('Description', 'imaginem-blocks'),
		    'options' => [
		        'true' => __('Yes','imaginem-blocks'),
		        'false' => __('No','imaginem-blocks')
		    ],
			'default' => 'true',
		            ]
		        );

			$this->add_responsive_control(
				'textoverlayopacity',
				[
					'label' => __( 'Default text overlay opacity', 'imaginem-blocks-ii' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
							'min' => 0,
							'step' => 0.01,
						],
					],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'style',
								'operator' => '=',
								'value' => 'wall-grid',
							],
							[
								'name' => 'style',
								'operator' => '=',
								'value' => 'wall-spaced',
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} .gridblock-background-hover' => 'opacity: {{SIZE}};',
					],
				]
			);
	
			$this->add_responsive_control(
				'hovertextoverlayopacity',
				[
					'label' => __( 'Hover text overlay opacity', 'imaginem-blocks-ii' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 1,
							'min' => 0,
							'step' => 0.01,
						],
					],
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[
								'name' => 'style',
								'operator' => '=',
								'value' => 'wall-grid',
							],
							[
								'name' => 'style',
								'operator' => '=',
								'value' => 'wall-spaced',
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} .gridblock-grid-element:hover .gridblock-background-hover' => 'opacity: {{SIZE}};',
					],
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
		'pagination',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Generate Pagination', 'imaginem-blocks'),
		    'desc' => __('Generate Pagination', 'imaginem-blocks'),
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
					'{{WRAPPER}} .work-details h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} .work-details h4 a' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'titlehovercolor',
			[
				'label' => __('Title Hover Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .work-details h4 a:hover' => 'color: {{VALUE}};',
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
		    'descriptioncolor',
			[
				'label' => __('Description Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .work-description' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'descriptiontype',
				'selector' => '{{WRAPPER}} .work-description',
			]
		);

		$this->add_control(
		    'worktypecolor',
			[
				'label' => __('Worktype Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .worktype-categories' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Worktype Typography', 'imaginem-blocks' ),
				'name' => 'worktypetype',
				'selector' => '{{WRAPPER}} .worktype-categories',
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
					'{{WRAPPER}} .column-gridblock-icon::after' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .column-gridblock-icon:hover::after' => 'background-color: {{VALUE}};',
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
				],
				'separator' => 'before',
		    ]
		);
		$this->add_control(
		    'filterunderline',
			[
				'label' => __('Filter underline', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters ul' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_control(
		    'filterseperator',
			[
				'label' => __('Filter Seperator', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters .filter-seperator' => 'background: {{VALUE}};',
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
		$this->add_control(
		    'filteractiveunderline',
			[
				'label' => __('Filter Active Underline', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #gridblock-filters li .is-active, {{WRAPPER}} #gridblock-filters li a:focus, {{WRAPPER}} #gridblock-filters a:focus, {{WRAPPER}} #gridblock-filters li .is-active, {{WRAPPER}} #gridblock-filters li .is-active:hover' => 'border-color: {{VALUE}};',
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

		$worktype_slugs = '';
		if ( is_array($settings['worktype_slugs']) ) {
			$worktype_slugs = implode (",", $settings['worktype_slugs']);
		}
		//echo $worktype_slugs;

		$shortcode = '[portfoliogrid animated_hovercard="'.$settings['animated_hovercard'].'" filterall="'.$settings['filterall'].'"  viewmore="'.$settings['viewmore'].'" imagesize="'.$settings['imagesize'].'" boxthumbnail_link="'.$settings['boxthumbnail_link'].'" category_display="'.$settings['category_display'].'" effect="'.$settings['effect'].'" type="'.$settings['type'].'" style="'.$settings['style'].'" like='.$settings['like'].' columns="'.$settings['columns'].'" format="'.$settings['format'].'" worktype_slugs="'.$worktype_slugs.'" title="'.$settings['title'].'" desc="'.$settings['desc'].'" pagination="'.$settings['pagination'].'" limit="'.$settings['limit'].'"]';
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
					'field'       => 'filterall',
					'type'        => __( 'Filter All text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'viewmore',
					'type'        => __( 'View more text', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
