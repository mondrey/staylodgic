<?php
namespace ImaginemBlocks\Widgets;

use Elementor\Widget_Base;
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
class Share_Page extends Widget_Base {

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
		return 'share-page';
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
		return __( 'Share Page', 'imaginem-blocks' );
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
		return 'eicon-share';
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
			'section_title',
			[
				'label' => __( 'Share Page', 'imaginem-blocks' ),
			]
		);

		$this->add_control(
			'share',
			[
			    'std' => 'Share',
			    'type' => 'text',
			    'label' => __('Share text', 'imaginem-blocks'),
				'desc' => __('Share text displayed on hover', 'imaginem-blocks'),
			]
		);

		$this->add_control(
		'facebook',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Facebook', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'twitter',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Twitter', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'linkedin',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Linkedin', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'googleplus',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Googleplus', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'reddit',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Reddit', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'tumblr',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Tumblr', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'pinterest',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Pinterest', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);
		$this->add_control(
		'email',
		[
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label' => __('Email', 'imaginem-blocks'),
			'label_on' => __( 'Show', 'your-plugin' ),
			'label_off' => __( 'Hide', 'your-plugin' ),
			'return_value' => 'yes',
			'default' => 'yes',
		]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'imaginem-blocks' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Icon color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .portfolio-share li i' => 'color: {{VALUE}};',
					'.entry-content {{WRAPPER}} .portfolio-share .share-indicate' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'text_hover_color',
			[
				'label' => __( 'Hover icon color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .portfolio-share li:hover i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'text_hover_background',
			[
				'label' => __( 'Hover icon background colors', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'.entry-content {{WRAPPER}} .portfolio-share li:hover i' => 'background: {{VALUE}};',
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

		$media = themecore_featured_image_link( get_the_id() );
		$link  = get_permalink();
		$title = get_the_title();
		
		$socialshare = array (
				'facebook' => array (
					'fa-facebook' => 'http://www.facebook.com/sharer.php?u='. esc_url( $link ) .'&t='. urlencode( $title )
					),
				'twitter' => array (
					'fa-twitter' => 'http://twitter.com/home?status='.urlencode( $title ).'+'. esc_url( $link )
					),
				'linkedin' => array (
					'fa-linkedin' => 'http://linkedin.com/shareArticle?mini=true&amp;url='.esc_url( $link ).'&amp;title='.urlencode( $title )
					),
				'googleplus' => array (
					'fa-google-plus' => 'https://plus.google.com/share?url='. esc_url( $link )
					),
				'reddit' => array (
					'fa-reddit' => 'http://reddit.com/submit?url='.esc_url( $link ).'&amp;title='.urlencode( $title )
					),
				'tumblr' => array (
					'fa-tumblr' => 'http://www.tumblr.com/share/link?url='.esc_url( $link ).'&amp;name='.urlencode( $title ).'&amp;description='.urlencode( $title )
					),
				'pinterest' => array (
					'fa-pinterest' => 'http://pinterest.com/pin/create/bookmarklet/?media=' .esc_url( $media ) .'&url='. esc_url( $link ) .'&is_video=false&description='.urlencode( $title )
					),
				'email' => array (
					'fa-envelope' => 'mailto:email@address.com?subject=Interesting%20Link&body=' . $title . " " .  esc_url( $link )
					)
				);
		?>
		<ul class="portfolio-share">
		<?php
		foreach($socialshare as $key => $share){

			$social_status = false;

			if ( isset( $settings[$key] ) ) {
				$social_status = $settings[$key];
			}

			if ( 'yes' === $social_status ) {
				foreach( $share as $icon => $url){
					echo '<li class="share-this-'.$icon.'"><a target="_blank" href="'. esc_url( $url ).'"><i class="fa '.$icon.'"></i></a></li>';
				}
			}
		}
		?>
		<li class="share-indicate"><?php echo $settings['share']; ?></li>
		</ul>
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
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'share',
					'type'        => __( 'Share', 'imaginem-blocks' ),
					'editor_type' => 'LINE'
				],
			],
		];
		return $widgets;
	}
}
