<?php

namespace Staylodgic;

class Form_Generator {


	// Constructor
	public function __construct() {
		// Register shortcodes
		$this->register_shortcodes();
	}

	/**
	 * Method Function to render an input element
	 *
	 * @param $input_object
	 *
	 * @return void
	 */
	private function render_input( $input_object ) {

		if ( current_user_can( 'edit_posts' ) && ! empty( $_GET ) ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! check_admin_referer( 'edit_registration_' . $_GET['guest'] ) ) {
				wp_die( esc_html__( 'Invalid nonce. Please try again.', 'staylodgic' ) );
				return; // If used inside a function, this will stop further execution
			}
		}

		// Valid nonce; proceed with action

		// Check for required attributes
		if ( ! isset( $input_object->type ) || ! isset( $input_object->id ) ) {
			throw new \Exception( 'Input type and ID are required.' );
		}

		$predefined = false;
		$required   = '';
		$label      = null;

		$signature_data = '';
		$type           = esc_attr( $input_object->type );
		$id             = esc_attr( $input_object->id );
		$name           = esc_attr( $input_object->name ?? $id );
		$class          = esc_attr( $input_object->class ?? 'form-control' );
		$value          = esc_attr( $input_object->value ?? '' );
		$placeholder    = esc_attr( $input_object->placeholder ?? '' );

		if ( isset( $input_object->label ) ) {
			$label = esc_html( $input_object->label );
		}
		if ( isset( $input_object->required ) && true === $input_object->required ) {
			$required = 'required';
		}

		// Check if 'guest' parameter is present in the URL
		if ( current_user_can( 'edit_posts' ) && isset( $_GET['guest'] ) && ! empty( $_GET['guest'] ) ) {
			$guest             = sanitize_text_field( $_GET['guest'] ); // Sanitize the input
			$post_id           = get_the_ID(); // Get current post ID
			$registration_data = get_post_meta( $post_id, 'staylodgic_registration_data', true ); // Retrieve all registration data

			// Check if there's specific registration data for the provided guest ID
			if ( isset( $registration_data[ $guest ] ) ) {
				$predefined = true;
				if ( isset( $registration_data[ $guest ][ $id ]['value'] ) ) {
					$value = $registration_data[ $guest ][ $id ]['value'];
					if ( 'checkbox' === $type && 'true' === $value ) {
						$input_object->checked = true;
					}
				}
				$registration_id = $registration_data[ $guest ]['registration_id'];
				$upload_dir      = wp_upload_dir();
				$signature_url   = $upload_dir['baseurl'] . '/signatures/' . $registration_id . '.png';

				$signature_data = 'data-signature-image-url="' . $signature_url . '"';
			}
		}

		// Start output buffering
		ob_start();

		$label_class = 'control-label';
		$form_class  = 'form-floating';
		if ( 'checkbox' === $type ) {
			$form_class = 'form-check';
		}
		echo '<div class="form-group ' . esc_attr( $form_class ) . '" needs-validation>';
		switch ( $type ) {
			case 'text':
			case 'number':
			case 'tel':
			case 'time':
			case 'email':
			case 'password':
				// Common types of inputs
				echo "<input data-label='" . esc_attr( $label ) . "' placeholder='" . esc_attr( $label ) . "' type='" . esc_attr( $type ) . "' id='" . esc_attr( $id ) . "' data-id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "' value='" . esc_attr( $value ) . "' placeholder='" . esc_attr( $placeholder ) . "' " . esc_attr( $required ) . '>';

				break;
			case 'date':
				// Common types of inputs
				echo "<input data-label='" . esc_attr( $label ) . "' placeholder='" . esc_attr( $label ) . "' type='date' id='" . esc_attr( $id ) . "' data-id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "' value='" . esc_attr( $value ) . "' placeholder='" . esc_attr( $placeholder ) . "' " . esc_attr( $required ) . '>';
				break;
			case 'datetime-local':
				// Common types of inputs
				echo "<input data-label='" . esc_attr( $label ) . "' placeholder='" . esc_attr( $label ) . "' type='datetime-local' id='" . esc_attr( $id ) . "' data-id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "' value='" . esc_attr( $value ) . "' placeholder='" . esc_attr( $placeholder ) . "' " . esc_attr( $required ) . '>';
				break;
			case 'textarea':
				echo "<textarea data-label='" . esc_attr( $label ) . "' placeholder='" . esc_attr( $placeholder ) . "' id='" . esc_attr( $id ) . "' data-id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "' " . esc_attr( $required ) . '>' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'signature':
				echo '<div class="signature-container">
                <canvas id="signature-pad" ' . esc_attr( $signature_data ) . ' class="signature-pad" width="400" height="200"></canvas>
                <div id="clear-signature">Clear</div>
                <input data-label="' . esc_attr( $label ) . '" data-id="signature-data" type="hidden" id="signature-data" name="signature-data">
                </div>';
				break;
			case 'checkbox':
				// Checkbox inputs
				$checked = isset( $input_object->checked ) && $input_object->checked ? 'checked' : '';
				echo "<input data-label='" . esc_attr( $label ) . "' data-id='" . esc_attr( $id ) . "' type='checkbox' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='form-check-input' value='" . esc_attr( $value ) . "' " . esc_attr( $checked ) . '>';
				$label_class = 'form-check-label';
				break;
			case 'button':
			case 'submit':
				// Button types (button and submit)
				$value                = esc_attr( $input_object->value ?? 'Button' );
				$predefined_data_attr = '';
				if ( $predefined ) {
					$predefined_data_attr = 'data-guest=' . $guest;
				}
				echo '<button ' . esc_attr( $predefined_data_attr ) . " type='" . esc_attr( $type ) . "' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "'>" . esc_html( $value ) . '</button>';
				break;
			case 'select':
				if ( 'countries' === $input_object->target ) {
					$options = staylodgic_country_list( 'select', '' );
				} else {
					$options = $this->parse_select_options( $input_object->options ?? '' );
				}
				$countries = staylodgic_country_list( 'select-alt', '' );
				$options   = $this->parse_select_options( $countries );

				echo "<select data-label='" . esc_attr( $label ) . "' data-id='" . esc_attr( $id ) . "' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='form-select' aria-label='Default select'>";
				foreach ( $options as $option_value => $option_label ) {
					$selected = '';
					if ( $option_value === $value ) {
						$selected = 'selected';
					}
					echo "<option value='" . esc_attr( $option_value ) . "' " . esc_attr( $selected ) . '>' . esc_html( $option_label ) . '</option>';
				}
				echo '</select>';

				break;
				// Add more cases for different input types as needed
			default:
				echo "<input data-label='" . esc_attr( $label ) . "' placeholder='" . esc_attr( $label ) . "' type='" . esc_attr( $type ) . "' id='" . esc_attr( $id ) . "' data-id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='" . esc_attr( $class ) . "' value='" . esc_attr( $value ) . "' placeholder='" . esc_attr( $placeholder ) . "' " . esc_attr( $required ) . '>';
		}

		// Render label if provided
		if ( $label ) {
			echo "<label for='" . esc_attr( $id ) . "' class='" . esc_attr( $label_class ) . "'>" . esc_html( $label ) . '</label>';
		}

		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Method Helper function to parse select options
	 *
	 * @param $options_string
	 *
	 * @return void
	 */
	private function parse_select_options( $options_string ) {
		$options       = array();
		$options_pairs = explode( ',', $options_string );
		foreach ( $options_pairs as $pair ) {
			list($value, $label) = array_map( 'trim', explode( ':', $pair ) );
			$options[ $value ]   = $label;
		}
		return $options;
	}

	/**
	 * Method Shortcode for form start
	 *
	 * @param $atts
	 *
	 * @return void
	 */
	public function shortcode_form_start( $atts ) {
		$attributes = shortcode_atts(
			array(
				'action' => '',
				'method' => 'post',
				'class'  => '',
				'id'     => '',
			),
			$atts
		);

		$action = esc_attr( $attributes['action'] );
		$method = esc_attr( $attributes['method'] );
		$class  = esc_attr( $attributes['class'] );
		$id     = esc_attr( $attributes['id'] );

		return "<form id='{$id}' action='{$action}' method='{$method}' class='{$class}' novalidate>";
	}

	/**
	 * Method Shortcode for form end
	 *
	 * @return void
	 */
	public function shortcode_form_end() {
		return '</form>';
	}

	/**
	 * Method Shortcode for rendering an input
	 *
	 * @param $atts
	 *
	 * @return void
	 */
	public function shortcode_input( $atts ) {
		$attributes = shortcode_atts(
			array(
				'type'        => 'text',
				'id'          => '',
				'label'       => '',
				'name'        => '',
				'class'       => 'form-control',
				'value'       => '',
				'placeholder' => '',
				'options'     => '',
				'target'      => '',
				'required'    => '',
			),
			$atts
		);

		// Convert the array to an object
		$input_object = (object) $attributes;

		return $this->render_input( $input_object );
	}

	/**
	 * Method Register shortcodes with WordPress
	 *
	 * @return void
	 */
	private function register_shortcodes() {
		add_shortcode( 'form_start', array( $this, 'shortcode_form_start' ) );
		add_shortcode( 'form_end', array( $this, 'shortcode_form_end' ) );
		add_shortcode( 'form_input', array( $this, 'shortcode_input' ) );
	}

	/**
	 * Method default_shortcodes
	 *
	 * @return void
	 */
	public function default_shortcodes() {
		$shortcodes = '';

		$shortcodes .= "[form_input type=\"text\" id=\"bookingnumber\" label=\"Booking number\" required=\"true\"]\n";
		$shortcodes .= "[form_input type=\"text\" id=\"fullname\" label=\"Fullname\" required=\"true\"]\n";
		$shortcodes .= "[form_input type=\"text\" id=\"passport\" label=\"Passport number\" required=\"true\"]\n";
		$shortcodes .= "[form_input type=\"email\" id=\"email\" label=\"e-Mail\"]\n";
		$shortcodes .= "[form_input type=\"tel\" id=\"phone\" label=\"Phone number\"]\n";
		$shortcodes .= "[form_input type=\"datetime-local\" id=\"checkin-date\" label=\"Check-In\"]\n";
		$shortcodes .= "[form_input type=\"datetime-local\" id=\"checkout-date\" label=\"Check-Out\"]\n";
		$shortcodes .= "[form_input type=\"select\" id=\"countries\" name=\"countries\" class=\"form-control\" value=\"\" target=\"countries\" label=\"Countries\" required=\"true\"]\n";
		$shortcodes .= "[form_input type=\"checkbox\" id=\"checkbox1\" label=\"Agree to Terms\" name=\"termsCheckbox\" required=\"true\"]\n";
		$shortcodes .= "[form_input type=\"signature\" id=\"signature\" label=\"Signature\" name=\"signature\"]\n";

		return $shortcodes;
	}
}

// Instantiate the class
new \Staylodgic\Form_Generator();
