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
class Imaginem_Fotorama extends Widget_Base {

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
		return 'fotorama';
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
		return __( 'Fotorama', 'imaginem-blocks' );
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
		return 'eicon-slideshow';
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
		return [ 'fotorama'];
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
		return [ 'fotorama'];
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
				'label' => __( 'Fotorama', 'imaginem-blocks' ),
			]
		);


		$this->add_control(
			'wp_gallery',
			[
				'label' => __( 'Add Images', 'imaginem-blocks' ),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$this->add_control(
		'thumbnails',
		[
			'type' => 'select',
			'group_title' => 'Type',
			'label' => __('Thumbnails', 'imaginem-blocks'),
			'desc' => __('Display Thumbnails', 'imaginem-blocks'),
			'options' => [
				'yes' => __('Yes','imaginem-blocks'),
				'no' => __('No','imaginem-blocks')
			],
			'default'=>'yes',
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
		'autoplayspeed',
		[
			'default' => '5000',
			'type' => 'text',
			'label' => __('Autoplay', 'imaginem-blocks'),
			'desc' => __('Autoplay ( 5000 default)', 'imaginem-blocks'),
			'condition' => [
				'autoplay' => 'true',
			],
		]
		);

		$this->add_control(
			'fullheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Full Height', 'imaginem-blocks'),
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'fotorama_height',
			[
				'label' => __( 'Height', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'condition' => [
					'fullheight!' => 'yes',
				],
			]
		);
		$this->add_control(
			'desktop_adjustheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Offset height for desktop menu', 'imaginem-blocks'),
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'fullheight' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_adjustheight',
			[
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __('Offset height for responsive menu', 'imaginem-blocks'),
				'label_on' => __( 'Show', 'your-plugin' ),
				'label_off' => __( 'Hide', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'fullheight' => 'yes',
				],
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
			'displaydesc',
			[
				'type' => 'select',
				'label' => __('Dispay dscription', 'imaginem-blocks'),
				'desc' => __('Display dscription', 'imaginem-blocks'),
				'options' => [
					'true' => __('Yes','imaginem-blocks'),
					'false' => __('No','imaginem-blocks')
				],
				'default'=>'true',
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
			'title_color',
			[
				'label' => __( 'Title Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.entry-content {{WRAPPER}} .fotorama__caption__wrap h1' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Title Typography', 'imaginem-blocks' ),
				'name' => 'title_typography',
				'selector' => '.entry-content {{WRAPPER}} .fotorama__caption__wrap h1',
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label' => __( 'Description Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.entry-content {{WRAPPER}} .fotorama__caption__wrap p' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Description Typography', 'imaginem-blocks' ),
				'name' => 'desc_typography',
				'selector' => '.entry-content {{WRAPPER}} .fotorama__caption__wrap p',
			]
		);

		$this->add_control(
			'caption_backgroundcolor',
			[
				'label' => __( 'Caption Background Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.entry-content {{WRAPPER}} .fotorama__caption__wrap' => 'background-color: {{VALUE}};',
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

		if ( ! $settings['wp_gallery'] ) {
			return;
		}
		$height = $settings['fotorama_height']['size'];
		if ( 'yes' === $settings['fullheight'] ) {
			$height = '100';
		}

		$ids = wp_list_pluck( $settings['wp_gallery'], 'id' );
		$pb_image_ids = implode( ',', $ids );
		//echo do_shortcode( '[fotorama autoplayspeed="'.$fotorama_autoplay_speed.'" autoplay="' . $fotorama_autoplay . '" titledesc="' . $slideshow_titledesc . '" filltype="' . $fotorama_fill . '" pageid=' . $featured_page . ']' );
		$shortcode = '[fotorama_images displaytitle="'.$settings['displaytitle'].'" displaydesc="'.$settings['displaydesc'].'" autoplay="'.$settings['autoplay'].'" autoplayspeed="'.$settings['autoplayspeed'].'" thumbnails="'.$settings['thumbnails'].'" mobileoffset="'.$settings['mobile_adjustheight'].'" desktopoffset="'.$settings['desktop_adjustheight'].'" height="'.$height.'" pb_image_ids="'.$pb_image_ids.'"]';

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