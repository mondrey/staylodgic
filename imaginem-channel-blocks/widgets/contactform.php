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
class ContactForm extends Widget_Base {

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
		return 'contactform';
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
		return __( 'Contact 7 Form', 'imaginem-blocks' );
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
		return 'eicon-form-horizontal';
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
				'label' => __( 'ContactForm', 'imaginem-blocks' ),
			]
		);


		if ( function_exists( 'wpcf7' ) ) {
		    function mtheme_select_contact_form(){
		        $wpcf7_form_list = get_posts(array(
		            'post_type' => 'wpcf7_contact_form',
		            'showposts' => 999,
		        ));
		        $posts = array();

		        if ( ! empty( $wpcf7_form_list ) && ! is_wp_error( $wpcf7_form_list ) ){
		        foreach ( $wpcf7_form_list as $post ) {
		            $options[ $post->ID ] = $post->post_title;
		        }
		        return $options;
		        }
			}
			$this->add_control(
				'mtheme_wpcf7_form',
				[
					'label' => esc_html__( 'Select your contact form 7', 'imaginem-blocks'),
					'label_block' => true,
					'type' => Controls_Manager::SELECT,
					'options' => mtheme_select_contact_form(),
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'imaginem-blocks' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_color',
			[
				'label' => __( 'Input Fields', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form .wpcf7-form-control:not(.wpcf7-submit)' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Input Type', 'imaginem-blocks' ),
				'name' => 'input_type',
				'selector' => '{{WRAPPER}} .wpcf7-form-control:not(.wpcf7-submit)',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Label Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form label' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Label Type', 'imaginem-blocks' ),
				'name' => 'form_label',
				'selector' => '{{WRAPPER}} .wpcf7-form label',
			]
		);

		$this->add_control(
			'inputborder',
			[
				'label' => __( 'Input Border Style', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'curved' => __( 'Curved', 'imaginem-blocks' ),
					'bottom' => __( 'Bottom', 'imaginem-blocks' ),
					'none' => __( 'None', 'imaginem-blocks' ),
				],
				'default' => 'default',
				'prefix_class' => 'input-border-style-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'input_bordercolor',
			[
				'label' => __( 'Input Border color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form .wpcf7-form-control:not(.wpcf7-submit)' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'inputicons',
			[
				'label' => __( 'Input icons', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'imaginem-blocks' ),
					'none' => __( 'None', 'imaginem-blocks' ),
				],
				'default' => 'default',
				'prefix_class' => 'input-icons-',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'inputicons_color',
			[
				'label' => __( 'Input icons color ', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .wpcf7-form i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .mtheme-select-form-field .wpcf7-form-control-wrap:after' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Button Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .wpcf7-form input[type="submit"]' => 'background-color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .wpcf7-form .wpcf7-submit' => 'border-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_textcolor',
			[
				'label' => __( 'Button Text Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .wpcf7-form input[type="submit"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hovercolor',
			[
				'label' => __( 'Button Hover Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .wpcf7-form input[type="submit"]:hover' => 'background: {{VALUE}};',
					'.entry-content {{WRAPPER}} .wpcf7-form .wpcf7-submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hovertextcolor',
			[
				'label' => __( 'Button Hover Text', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.entry-content {{WRAPPER}} .wpcf7-form input[type="submit"]:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Button Typography', 'imaginem-blocks' ),
				'name' => 'button_typography',
				'selector' => '.entry-content {{WRAPPER}} .wpcf7-form .wpcf7-submit',
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

		?>
		<div class="mtheme-contact-form-container">
			<?php echo do_shortcode( '[contact-form-7 id="' . $settings['mtheme_wpcf7_form'] . '" ]' ); ?>
		</div>
		<?php
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