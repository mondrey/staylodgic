<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
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
class Team_Member extends Widget_Base {

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
		return 'team-member';
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
		return __( 'Team Member', 'imaginem-blocks' );
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
		return 'eicon-person';
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
				'label' => __( 'Team Member', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
		'member_image',
		[	
			'std' => '',
			'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
		]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'shape',
			[
				'label' => __( 'Shape', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'square' => __( 'Square', 'imaginem-blocks' ),
					'circle' => __( 'Circle', 'imaginem-blocks' ),
				],
				'default' => 'square',
				'condition' => [
					'member_image!' => '',
				],
				'prefix_class' => 'team-image-shape-',
			]
		);

		$this->add_control(
		'title',
		[	
			'std' => '',
			'type' => Controls_Manager::TEXT,
			'group_title' => 'Content',
			'label' => __('Staff title', 'imaginem-blocks'),
		]
		);
		$this->add_control(
		'name',
		[	
			'std' => '',
			'type' => Controls_Manager::TEXT,
			'label' => __('Staff name', 'imaginem-blocks'),
		]
		);
		$this->add_control(
		'description',
		[	
			'std' => '',
			'type' => Controls_Manager::TEXTAREA,
			'label' => __('Staff Description', 'imaginem-blocks'),
		]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label' => __( 'Description Padding', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .person .person-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'imaginem-blocks' ),
				'type' => Controls_Manager::CHOOSE,
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
					'justify' => [
						'title' => __( 'Justified', 'imaginem-blocks' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .person-details' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
		'socialicons',
		[	
			'type' => Controls_Manager::SELECT,
			'label' => __('Display social icons', 'imaginem-blocks'),
			'desc' => __('Display social icons', 'imaginem-blocks'),
			'options' => [
				'true' => __('Yes','imaginem-blocks'),
				'false' => __('No','imaginem-blocks')
			],
			'default' => 'false',
			'prefix_class' => 'social-icons-active-',
		]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',[
				'label' => __( 'Icon', 'imaginem-blocks' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => '',
					'library' => 'solid',
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'url',[
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
			'social',
			[
				'label' => __( 'Social Icons', 'imaginem-blocks' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'url' => 'http://your-link.com'
					]
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '<i class="{{ icon }}"></i> {{{ url.url }}}',
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
					'{{WRAPPER}} .person.box-title i' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
		    'iconhovercolor',
			[
				'label' => __('Icon hover color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .person-socials a:hover i' => 'color: {{VALUE}};',
				],
		    ]
		);

		$this->add_control(
			'hoverbackgroundcolor',
			[
				'label' => __( 'Image Hover', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .person.box-title .person-image-wrap::after' => 'background: linear-gradient(to bottom, rgba(0,0,0,0) 0%,{{VALUE}} 100%);',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .person h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .person h4.staff-position' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .person h4.staff-position',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .person-desc' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .person-desc',
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
		
		foreach( $settings['social'] as $socialprofile ) {

			$the_icon_data = $socialprofile['icon'];
			//print_r( $the_icon_data );
			$the_icon = $the_icon_data['value'];
	
			$icon_html = '';
	
			if ( '' !== $the_icon ) {
				ob_start();
				Icons_Manager::render_icon( $socialprofile['icon'], [ 'aria-hidden' => 'true' ] );
				$icon_html = ob_get_clean();
	
				$icon_html = htmlentities( $icon_html );
			}
			$child_shortcode .= '[socials social_icon="' . htmlspecialchars( $icon_html ) . '" social_link="'.$socialprofile['url']['url'].'" social_target="'.$socialprofile['url']['is_external'].'"]';
		}

		$shortcode = '[staff title="'.htmlspecialchars($settings['title']).'" socialicons="'.$settings['socialicons'].'" name="'.htmlspecialchars($settings['name']).'" image="'.$settings['member_image']['url'].'"  imageid="'.$settings['member_image']['id'].'" image_size="'.$settings['image_size'].'" desc="'.htmlspecialchars($settings['description']).'"]'.$child_shortcode.'[/staff]';

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
					'type'        => __( 'Staff Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'name',
					'type'        => __( 'Staff Name', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'description',
					'type'        => __( 'Staff Description', 'imaginem-blocks' ),
					'editor_type' => 'AREA'
				],
			],
		];
		return $widgets;
	}
}