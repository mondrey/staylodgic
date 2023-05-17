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
class Google_Map extends Widget_Base {

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
		return 'google-map';
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
		return __( 'Google Map', 'imaginem-blocks' );
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
		return 'eicon-google-maps';
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
		return [ 'googlemap-key', 'googlemap-display'];
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
				'label' => __( 'Google Map', 'imaginem-blocks' ),
			]
		);

		$googlemap_api = get_theme_mod( 'googlemap_api' );

		if ( $googlemap_api == "" ) {
			$this->add_control(
			    'api_notice',
			    [
				    'type'  => Controls_Manager::RAW_HTML,
				    'raw'   => 'Please enter your Google Map API to Theme Customizer > General section',
				    'label_block' => true,
			    ]
		    );
		}

		$this->add_control(
		    'map_notice',
		    [
			    'label' => __( 'Find Latitude & Longitude', 'imaginem-blocks' ),
			    'type'  => Controls_Manager::RAW_HTML,
			    'raw'   => '<form id="googlemapaddress"><input type="text" id="mtheme-map-find-address" class="mtheme-map-find-address" style="margin-top:7px;"><input type="submit" value="Search" class="elementor-button elementor-button-default" style="margin-top:7px;"></form><div id="mtheme-output-result" class="mtheme-output-result" style="margin-top:7px;"></div>',
			    'label_block' => true,
		    ]
	    );

		$this->add_control(
			'map_lat',
			[
				'label' => __( 'Latitude', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '1.2833754',
				'default' => '1.2833754',
			]
		);

		$this->add_control(
			'map_lng',
			[
				'label' => __( 'Longitude', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '103.86072639999998',
				'default' => '103.86072639999998',
				'separator' => true,
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom Level', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 25,
					],
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'imaginem-blocks' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .mtheme-map' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'map_type',
			[
				'label' => __( 'Map Type', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'roadmap' => __( 'Road Map', 'imaginem-blocks' ),
					'satellite' => __( 'Satellite', 'imaginem-blocks' ),
					'hybrid' => __( 'Hybrid', 'imaginem-blocks' ),
					'terrain' => __( 'Terrain', 'imaginem-blocks' ),
				],
				'default' => 'roadmap',
			]
		);

		$this->add_control(
			'zoom_control',
			[
				'label' => __( 'Show Zoom Control', 'imaginem-blocks' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'zoom_control_position',
			[
				'label' => __( 'Control Position', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'RIGHT_BOTTOM' => __( 'Bottom Right (Default)', 'imaginem-blocks' ),
					'TOP_LEFT' => __( 'Top Left', 'imaginem-blocks' ),
					'TOP_CENTER' => __( 'Top Center', 'imaginem-blocks' ),
					'TOP_RIGHT' => __( 'Top Right', 'imaginem-blocks' ),
					'LEFT_CENTER' => __( 'Left Center', 'imaginem-blocks' ),
					'RIGHT_CENTER' => __( 'Right Center', 'imaginem-blocks' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'imaginem-blocks' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'imaginem-blocks' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'imaginem-blocks' ),
				],
				'default' => 'RIGHT_BOTTOM',
				'condition' => [
					'zoom_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'default_ui',
			[
				'label' => __( 'Show Default UI', 'imaginem-blocks' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'map_type_control',
			[
				'label' => __( 'Map Type Control', 'imaginem-blocks' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'map_type_control_style',
			[
				'label' => __( 'Control Styles', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'DEFAULT' => __( 'Default', 'imaginem-blocks' ),
					'HORIZONTAL_BAR' => __( 'Horizontal Bar', 'imaginem-blocks' ),
					'DROPDOWN_MENU' => __( 'Dropdown Menu', 'imaginem-blocks' ),
				],
				'default' => 'DEFAULT',
				'condition' => [
					'map_type_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'map_type_control_position',
			[
				'label' => __( 'Control Position', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'TOP_LEFT' => __( 'Top Left (Default)', 'imaginem-blocks' ),
					'TOP_CENTER' => __( 'Top Center', 'imaginem-blocks' ),
					'TOP_RIGHT' => __( 'Top Right', 'imaginem-blocks' ),
					'LEFT_CENTER' => __( 'Left Center', 'imaginem-blocks' ),
					'RIGHT_CENTER' => __( 'Right Center', 'imaginem-blocks' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'imaginem-blocks' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'imaginem-blocks' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'imaginem-blocks' ),
				],
				'default' => 'TOP_LEFT',
				'condition' => [
					'map_type_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'streetview_control',
			[
				'label' => __( 'Show Streetview Control', 'imaginem-blocks' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no'
			]
		);

		$this->add_control(
			'streetview_control_position',
			[
				'label' => __( 'Streetview Position', 'imaginem-blocks' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'RIGHT_BOTTOM' => __( 'Bottom Right (Default)', 'imaginem-blocks' ),
					'TOP_LEFT' => __( 'Top Left', 'imaginem-blocks' ),
					'TOP_CENTER' => __( 'Top Center', 'imaginem-blocks' ),
					'TOP_RIGHT' => __( 'Top Right', 'imaginem-blocks' ),
					'LEFT_CENTER' => __( 'Left Center', 'imaginem-blocks' ),
					'RIGHT_CENTER' => __( 'Right Center', 'imaginem-blocks' ),
					'BOTTOM_LEFT' => __( 'Bottom Left', 'imaginem-blocks' ),
					'BOTTOM_CENTER' => __( 'Bottom Center', 'imaginem-blocks' ),
					'BOTTOM_RIGHT' => __( 'Bottom Right', 'imaginem-blocks' ),
				],
				'default' => 'RIGHT_BOTTOM',
				'condition' => [
					'streetview_control' => 'yes',
				],
				'separator' => false,
			]
		);

		$this->add_control(
			'custom_map_style',
			[
				'label' => __( 'Custom Map Style', 'imaginem-blocks' ),
				'type' => Controls_Manager::TEXTAREA,
				'description' => __('Get new map style codes from: <br/><a href="https://mapstyle.withgoogle.com/" target="_blank">Google Map Styling Wizard</a> <br/><a href="https://snazzymaps.com/explore" target="_blank">Snazzy Maps</a><br/>Copy Paste style code to display.'),
				'condition' => [
					'map_type' => 'roadmap',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'imaginem-blocks' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
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
			'marker',
			[
				'label' => __( 'Marker Color', 'imaginem-blocks' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .map-dot' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .map-pulse' => 'background: {{VALUE}};',
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

		$mtheme_map_styles = $settings['custom_map_style'];

		if ( 0 === absint( $settings['zoom']['size'] ) ) {
			$settings['zoom']['size'] = 10;
		}

		$this->add_render_attribute('mtheme-google-map-custom', 'data-mtheme-map-style', $mtheme_map_styles);

		if ( 'yes' == $settings['zoom_control'] ) {
			$zoom_control_data = 'data-mtheme-map-zoom-control="true" data-mtheme-map-zoom-control-position="'.$settings['zoom_control_position'].'"';
		} else {
			$zoom_control_data = 'data-mtheme-map-zoom-control="false"';
		}

		if ( 'yes' == $settings['default_ui'] ) {
			$defaultui = "true";
		} else {
			$defaultui = "false";
		}

		if ( 'yes' == $settings['map_type_control'] ) {
			$map_type_control = 'data-mtheme-map-type-control="true" data-mtheme-map-type-control-style="'.$settings['map_type_control_style'].'" data-mtheme-map-type-control-position="'.$settings['map_type_control_position'].'"';
		} else {
			$map_type_control = 'data-mtheme-map-type-control="false"';
		}

		if ( 'yes' == $settings['streetview_control'] ) {
			$streetview_control = 'data-mtheme-map-streetview-control="true" data-mtheme-map-streetview-position="'.$settings['streetview_control_position'].'"';
		} else {
			$streetview_control = "false";
		}
		?>
		<div id="mtheme-map-<?php echo esc_attr($this->get_id()); ?>" class="mtheme-map" 
		<?php echo $this->get_render_attribute_string('mtheme-google-map-custom'); ?> 
		<?php echo $zoom_control_data; ?> 
		data-mtheme-map-defaultui="<?php echo $defaultui; ?>" 
		data-mtheme-map-type="<?php echo $settings['map_type']; ?>" 
		<?php echo $map_type_control; ?> 
		<?php echo $streetview_control; ?> 
		data-mtheme-map-lat="<?php echo $settings['map_lat']; ?>" 
		data-mtheme-map-lng="<?php echo $settings['map_lng']; ?>" 
		data-mtheme-map-zoom="<?php echo $settings['zoom']['size']; ?>" 
		data-mtheme-map-infowindow-width="<?php echo $settings['infowindow_max_width']; ?>">
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