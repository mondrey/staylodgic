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
class Blog_List extends Widget_Base {

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
		return 'blog-list';
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
		return __( 'Blog List', 'imaginem-blocks' );
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
		return 'eicon-post-list';
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
				'label' => __( 'Blog List', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'small' => __( 'Small', 'imaginem-blocks' ),
				],
				'default' => 'default',
				'prefix_class' => 'blog-list-style-',
			]
		);

		$this->add_control(
		    'cat_slugs',
		    [
		        'type' => \Elementor\Controls_Manager::SELECT2,
		        'label' => __('Choose categories to list', 'imaginem-blocks'),
		        'options' => themecore_elementor_categories('blog'),
		        'multiple' => true,
		        'default' => '',
		        'label_block' => true,
		    ]
		);

		$this->add_control(
		'category_display',
		[
		    'type' => \Elementor\Controls_Manager::SELECT,
		    'label' => __('Display categories', 'imaginem-blocks'),
		    'desc' => __('Display categories', 'imaginem-blocks'),
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
				'label' => __('Title Color', 'mthemelocal'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-content .entry-post-title h2' => 'color: {{VALUE}};',
					'{{WRAPPER}} .entry-content .entry-post-title h2 a' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'titlehovercolor',
			[
				'label' => __('Title Hover Color', 'mthemelocal'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .entry-content .entry-post-title h2 a:hover' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'elementor' ),
				'name' => 'titletype',
				'selector' => '{{WRAPPER}} .entry-content .entry-post-title h2 a',
			]
		);

		$this->add_control(
		    'descriptioncolor',
			[
				'label' => __('Content Color', 'mthemelocal'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .postsummary-spacing' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'descriptiontype',
				'selector' => '.entry-content {{WRAPPER}} .postsummary-spacing',
			]
		);

		$this->add_control(
		    'iconcolor',
			[
				'label' => __('iCon Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .postsummarywrap i' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .postsummarywrap i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		    'worktypecolor',
			[
				'label' => __('Date Color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .postsummarywrap .post-single-meta' => 'color: {{VALUE}};',
					'{{WRAPPER}} .postsummarywrap a' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
		    ]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Date Typography', 'imaginem-blocks' ),
				'name' => 'datetypography',
				'selector' => '{{WRAPPER}} .datecomment',
			]
		);


		$this->add_control(
			'readmorecolor',
			[
				'label' => __( 'Readmore Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .entry-blog-contents-wrap .arrow-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .entry-blog-contents-wrap svg g' => 'stroke: {{VALUE}};',
					'{{WRAPPER}} .entry-blog-contents-wrap svg path' => 'stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'label' => __( 'Readmore Typography', 'imaginem-blocks' ),
				'name' => 'readmoretypography',
				'selector' => '{{WRAPPER}} .entry-blog-contents-wrap .arrow-link',
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

		$cat_slugs = '';
		if ( is_array($settings['cat_slugs']) ) {
			$cat_slugs = implode (",", $settings['cat_slugs']);
		}

		$shortcode = '[bloglist cat_slugs="'.$cat_slugs.'" limit="'.$settings['limit'].'"]';

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
