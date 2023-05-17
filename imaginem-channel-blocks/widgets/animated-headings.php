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
class Imaginem_Animated_Headings extends Widget_Base {

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
		return 'imaginem-animated-headings';
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
		return __( 'Animated Headings', 'imaginem-blocks' );
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
			'autoplayinterval',
			[
				'default' => '3500',
				'type'    => 'text',
				'label'   => __('Autoplay Interval', 'imaginem-blocks'),
				'desc'    => __('Autoplay Interval ( 3500 default)', 'imaginem-blocks'),
			]
		);

		$this->add_control(
			'effect',
			[
				'type'    => \Elementor\Controls_Manager::SELECT,
				'label'   => __('Effect', 'imaginem-blocks'),
				'desc'    => __('Effect', 'imaginem-blocks'),
				'options' => [
					'rotate-element' => __( 'Rotate', 'imaginem-blocks' ),
					'zoom' => __( 'Zoom', 'imaginem-blocks' ),
				],
				'default' => 'rotate-element',
			]
		);

		$this->add_responsive_control(
			'align',
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
				],
				'default' => '',
				'prefix_class' => 'animated-headline-align-',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'static_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Static Title', 'imaginem-blocks'),
				'default' => __( 'Static text', 'imaginem-blocks' ),
				'placeholder' => __( 'Enter your title', 'imaginem-blocks' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title', [
				'std' => '',
				'default' => 'Title',
				'type' => Controls_Manager::TEXT,
				'group_title' => 'Content',
				'label' => __('Title', 'imaginem-blocks'),
			]
		);

		$this->add_control(
			'rotatingtitles',
			[
				'label' => __( 'Rotating Single Line Title', 'imaginem-blocks' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => __( 'Title #1', 'imaginem-blocks' ),
					],
				],
				'title_field' => '{{ title }}',
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
			'statictext',
			[
				'label' => __('Static Title', 'imaginem-blocks'),
				'std' => '',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .fliptitles-intro .fliptitles-static-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Static Typography', 'imaginem-blocks' ),
				'name' => 'static_typography',
				'selector' => '.entry-content {{WRAPPER}} .fliptitles-intro h1.fliptitles-headline .fliptitles-static-title',
			]
		);

		$this->add_control(
			'rotatingtext',
			[
				'label' => __( 'Rotating Titles', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .fliptitles-intro .fliptitles-words-wrapper' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Rotating Typography', 'imaginem-blocks' ),
				'name' => 'rotating_typography',
				'selector' => '.entry-content {{WRAPPER}} .fliptitles-headline .fliptitles-words-wrapper, .entry-content {{WRAPPER}} .fliptitles-headline .fliptitles-words-wrapper b, .entry-content {{WRAPPER}} .fliptitles-headline .fliptitles-words-wrapper i',
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

		$rotating = '';
		$count    = 0;
		$word_visible_class = ' class="word-visible"';

		foreach( $settings['rotatingtitles'] as $rotating_title ) {
			$rotating .= '<b' . $word_visible_class . '>' . $rotating_title['title'] . '</b>';
			$word_visible_class = '';
		}
		$static_text = '';
		if ( '' !== $settings['static_title'] ) {
			$static_text = '<span class="fliptitles-static-title">' . $settings['static_title'] . '</span>';
		}
		?>
		<div class="fliptitles-intro" data-delay="<?php echo $settings['autoplayinterval']; ?>">
		<h1 class="fliptitles-headline <?php echo $settings['effect']; ?>">
			<?php
			echo $static_text;
			?>
			<span class="fliptitles-words-wrapper">
				<?php
				echo $rotating;
				?>
			</span>
		</h1>
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
