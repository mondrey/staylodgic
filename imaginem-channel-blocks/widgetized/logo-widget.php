<?php
class mTheme_Logo_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'mtheme_logo_widget', 'description' => __( 'Logo Widget', 'mthemelocal') );
		parent::__construct('logo_widget',__('superlens Logo', 'mthemelocal'), $widget_ops);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		
		?>

<?php

		echo $before_widget;
		
		$theme_style=superlens_get_option_data('theme_style');
		$main_logo_dark=superlens_get_option_data('main_logo_dark');
		$main_logo_bright=superlens_get_option_data('main_logo_bright');

		if ( $main_logo_bright =='') {
			$main_logo_bright = $main_logo_dark;
		}
		
		$main_header_type = superlens_get_option_data('main_header_type');

		if ( $main_logo_bright<>"" ) {
			echo '<img class="footer-logo footer-logo-theme-light" src="'.esc_url($main_logo_bright).'" alt="logo" />';
		}
		if ($main_logo_dark <> "") {
			echo '<img class="footer-logo footer-logo-theme-dark" src="'.esc_url($main_logo_dark).'" alt="logo" />';
		}
		if ( $main_logo_bright == "" && $main_logo_dark == "" ) {
			if ($theme_style=="dark") {
				echo '<img class="footer-logo footer-logo-theme-light" src="'.esc_url(MTHEME_PATH.'/images/logo.png').'" alt="logo" />';
			} else {
				echo '<img class="footer-logo footer-logo-theme-dark" src="'.esc_url(MTHEME_PATH.'/images/logo_dark.png').'" alt="logo" />';
			}
		}

		echo $after_widget;

	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		return $instance;
	}

	public function form( $instance ) {
	?>

		<p>Displays Logo set in theme options. No other settings required for widget.</p>
		
<?php
	}

}
function mTheme_Logo_Widget_register_widgets() {
	register_widget( 'mTheme_Logo_Widget' );
}
add_action( 'widgets_init', 'mTheme_Logo_Widget_register_widgets' );