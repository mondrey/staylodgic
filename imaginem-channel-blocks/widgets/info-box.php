<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor widget.
 *
 * Elementor widget that displays before and after block.
 *
 * @since 1.0.0
 */
class Imaginem_Info_Box extends Widget_Base {

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
		return 'imaginem-info-box';
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
		return __( 'Info Box', 'elementor' );
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
		return 'eicon-flip-box';
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
				'label' => __( 'Info Box', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
		'image_front',
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
			'background_overlay_opacity',
			[
				'label' => __( 'Overlay Opacity', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => .25,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .infobox-overlay' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
		    'infoboxnumber',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Infobox Number', 'imaginem-blocks'),
				'default' => __( 'Number', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter number', 'imaginem-blocks' ),
				'label_block' => true,
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'subtitle',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Sub Title', 'imaginem-blocks'),
				'default' => __( 'This is the sub heading', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your sub title', 'imaginem-blocks' ),
				'label_block' => true,
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'title',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Title', 'imaginem-blocks'),
				'default' => __( 'This is the heading', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your title', 'imaginem-blocks' ),
				'label_block' => true,
				'separator' => 'before',
		    ]
		);

		$this->add_control(
			'newicon',
			[
				'label' => __( 'Choose Icon', 'imaginem-blocks' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon size', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .infobox-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		    'content',
			[
		        'type' => Controls_Manager::TEXTAREA,
		        'label' => __('Box Content', 'imaginem-blocks'),
				'default' => __( 'Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Cras mattis consectetur purus sit amet fermentum.', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your description', 'imaginem-blocks' ),
				'rows' => 10,
				'separator' => 'none',
				'show_label' => false,
		    ]
		);

		$this->add_control(
			'reveal_last',
			[
				'label' => __( 'Hide and Reveal', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'desc'    => __( 'Description', 'imaginem-blocks' ),
				],
				'default'      => 'default',
				'prefix_class' => 'reveal-last-',
			]
		);

		$this->add_control(
		    'link',
			[
		        'type' => Controls_Manager::URL,
		        'label' => __('Button Link', 'imaginem-blocks'),
		        'placeholder' => __( 'https://your-link.com', 'imaginem-blocks' ),
				'separator' => 'before',
		    ]
		);

		$this->add_control(
		    'button_text',
			[
		        'type' => Controls_Manager::TEXT,
		        'label' => __('Button Text', 'imaginem-blocks'),
				'default' => __( 'Button', 'imaginem-blocks' ),
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

		$this->add_responsive_control(
			'padding_space',
			[
				'label' => __( 'Padding Space', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .infobox-card-container-back,{{WRAPPER}} .infobox-card-container-front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
		    'iconcolor',
			[
				'label' => __('Icon color', 'imaginem-blocks'),
		        'std' => '',
		        'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .infobox-icon' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .infobox-card-flip h2' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .infobox-card-flip h2',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .infobox-card-flip p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .infobox-card-flip p',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .text-is-bright .mtheme-button' => 'border-color: {{VALUE}};color: {{VALUE}};',
					'{{WRAPPER}} .text-is-bright .mtheme-button:hover' => 'background-color: {{VALUE}};color: #fff;',
				],
			]
		);

		$this->add_control(
			'button_htextcolor',
			[
				'label' => __( 'Button Hover Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .text-is-bright .mtheme-button:hover' => 'color: {{VALUE}};',
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
		$settings = $this->get_settings_for_display();

		$url           = $settings['link']['url'];
		$url_target    = $settings['link']['is_external'];
		$url_nofollow  = $settings['link']['nofollow'];
		$infoboxnumber = $settings['infoboxnumber'];
		$title         = $settings['title'];
		$subtitle      = $settings['subtitle'];
		$nofollow      = $url_nofollow;
		$target        = $url_target;
		$image_size    = $settings['image_size'];
		$image_front   = $settings['image_front']['id'];
		$button_text   = $settings['button_text'];
		$button_link   = $url;
		$content       = $settings['content'];
	

		$image_array_1 = wp_get_attachment_image_src($image_front,$image_size,false);
		$image_src_1 = $image_array_1[0];

		ob_start();
		Icons_Manager::render_icon( $settings['newicon'], [ 'aria-hidden' => 'true' ] );
		$icon_html = ob_get_clean();

		$infobox = '<div class="infobox-wrap text-is-bright">';
			$infobox .= '<div class="infobox-card-flip">';
				$infobox .= '<div class="infobox-card-front" data-expand="-20" style="background-image: url('.esc_url($image_src_1).');" data-bg="'.esc_url($image_src_1).'">';
					$infobox .= '<div class="infobox-card-container-front">';
						$infobox .= '<div class="infobox-number">'.$infoboxnumber.'</div>';
						$infobox .= '<div class="infobox-inner">';
							$infobox .= '<div class="infobox-icon infobox-animate">';
							$infobox .= $icon_html;
							$infobox .= '</div>';
							$infobox .= '<h3 class="infobox-animate">'.$subtitle.'</h3>';
							$infobox .= '<h2 class="infobox-animate">'.$title.'</h2>';
							$infobox .= '<p class="infobox-animate infobox-contents">'.$content.'</p>';
							if ( isset($button_link) && '' !== $button_link ) {
								$infobox .= '<a href="'.esc_url($button_link).'" '.$target.$nofollow.' class="infobox-animate mtheme-button">'.$button_text.'</a>';
							}
							$infobox .= '</div>';
						$infobox .= '</div>';
					$infobox .= '<div class="infobox-overlay"></div>';
				$infobox .= '</div>';
			$infobox .= '</div>';
		$infobox .= '</div>';
	
		echo $infobox;
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
					'type'        => __( 'Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'subtitle',
					'type'        => __( 'Title', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'content',
					'type'        => __( 'Content', 'imaginem-blocks' ),
					'editor_type' => 'AREA'
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