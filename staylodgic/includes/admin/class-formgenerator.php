<?php

namespace Staylodgic;

class FormGenerator
{

    // Constructor
    public function __construct()
    {
        // Register shortcodes
        $this->registerShortcodes();
    }

    // Function to render an input element
    private function renderInput($inputObject)
    {
        // Check for required attributes
        if (!isset($inputObject->type) || !isset($inputObject->id)) {
            throw new \Exception("Input type and ID are required.");
        }

        $predefined     = false;
        $signature_data = '';
        $type           = esc_attr($inputObject->type);
        $id             = esc_attr($inputObject->id);
        $name           = esc_attr($inputObject->name ?? $id);
        $class          = esc_attr($inputObject->class ?? 'form-control');
        $value          = esc_attr($inputObject->value ?? '');
        $placeholder    = esc_attr($inputObject->placeholder ?? '');
        $label          = isset($inputObject->label) ? esc_html($inputObject->label) : null;
        $required       = isset($inputObject->required) && $inputObject->required === 'true' ? 'required' : '';

        // Check if 'guest' parameter is present in the URL
        if (current_user_can('manage_options') && isset($_GET['guest']) && !empty($_GET['guest'])) {
            $guest             = sanitize_text_field($_GET['guest']); // Sanitize the input
            $post_id           = get_the_ID(); // Get current post ID
            $registration_data = get_post_meta($post_id, 'staylodgic_registration_data', true); // Retrieve all registration data

            // Check if there's specific registration data for the provided guest ID
            if (isset($registration_data[$guest])) {
                $predefined = true;
                if (isset($registration_data[$guest][$id]['value'])) {
                    $value = $registration_data[$guest][$id]['value'];
                    if ('checkbox' == $type && 'true' == $value) {
                        $inputObject->checked = true;
                    }
                }
                $registration_id = $registration_data[$guest]['registration_id'];
                $upload_dir      = wp_upload_dir();
                $signature_url   = $upload_dir['baseurl'] . '/signatures/' . $registration_id . '.png';

                $signature_data = 'data-signature-image-url="' . $signature_url . '"';
            }
        }

        // Start output buffering
        ob_start();

        $label_class = 'control-label';
        $form_class  = 'form-floating';
        if ('checkbox' == $type) {
            $form_class = 'form-check';
        }
        echo '<div class="form-group ' . $form_class . '" needs-validation>';
        switch ($type) {
            case 'text':
            case 'number':
            case 'tel':
            case 'time':
            case 'email':
            case 'password':
                // Common types of inputs
                echo "<input data-label='" . esc_attr($label) . "' placeholder='" . esc_attr($label) . "' type='" . esc_attr($type) . "' id='" . esc_attr($id) . "' data-id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='" . esc_attr($class) . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($placeholder) . "' " . esc_attr($required) . ">";

                break;
            case 'date':
                // Common types of inputs
                echo "<input data-label='" . esc_attr($label) . "' placeholder='" . esc_attr($label) . "' type='text' id='" . esc_attr($id) . "' data-id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='flatpickr-date-time " . esc_attr($class) . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($placeholder) . "' " . esc_attr($required) . ">";
                break;
            case 'textarea':
                echo "<textarea data-label='" . esc_attr($label) . "' placeholder='" . esc_attr($placeholder) . "' id='" . esc_attr($id) . "' data-id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='" . esc_attr($class) . "' " . esc_attr($required) . ">" . esc_textarea($value) . "</textarea>";
                break;
            case 'signature':

                echo '<div class="signature-container">
                <canvas id="signature-pad" ' . esc_attr($signature_data) . ' class="signature-pad" width="400" height="200"></canvas>
                <div id="clear-signature">Clear</div>
                <input data-label="' . esc_attr($label) . '" data-id="signature-data" type="hidden" id="signature-data" name="signature-data">
                </div>';
                break;
            case 'checkbox':
                // Checkbox inputs
                $checked = isset($inputObject->checked) && $inputObject->checked ? 'checked' : '';
                echo "<input data-label='" . esc_attr($label) . "' data-id='" . esc_attr($id) . "' type='checkbox' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='form-check-input' value='" . esc_attr($value) . "' " . esc_attr($checked) . ">";
                $label_class = 'form-check-label';                
                break;
            case 'button':
            case 'submit':
                // Button types (button and submit)
                $value = esc_attr($inputObject->value ?? 'Button');
                $predefined_data_attr = '';
                if ($predefined) {
                    $predefined_data_attr = 'data-guest=' . $guest;
                }
                echo "<button " . esc_attr($predefined_data_attr) . " type='" . esc_attr($type) . "' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='" . esc_attr($class) . "'>" . esc_html($value) . "</button>";
                break;
            case 'select':
                // [form_input type="select" id="mySelect" name="mySelect" class="form-control" value="option1" options="option1:Option 1,option2:Option 2,option3:Option 3"]
                if ('countries' == $inputObject->target) {
                    $options = staylodgic_country_list('select', '');
                } else {
                    $options = $this->parseSelectOptions($inputObject->options ?? '');
                }
                $countries = staylodgic_country_list('select-alt', '');
                $options   = $this->parseSelectOptions($countries);
                // error_log(print_r($options, true));
                echo "<select data-label='" . esc_attr($label) . "' data-id='" . esc_attr($id) . "' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' class='form-select' aria-label='Default select'>";
                foreach ($options as $optionValue => $optionLabel) {
                    $selected = $optionValue == $value ? 'selected' : '';
                    echo "<option value='" . esc_attr($optionValue) . "' " . esc_attr($selected) . ">" . esc_html($optionLabel) . "</option>";
                }
                echo "</select>";
                
                break;
                // Add more cases for different input types as needed
            default:
                throw new \Exception("Unsupported input type: $type");
        }

        // Render label if provided
        if ($label) {
            echo "<label for='" . esc_attr($id) . "' class='" . esc_attr($label_class) . "'>" . esc_html($label) . "</label>";
        }

        echo '</div>';

        return ob_get_clean();
    }

    // Helper function to parse select options
    private function parseSelectOptions($optionsString)
    {
        $options      = [];
        $optionsPairs = explode(',', $optionsString);
        foreach ($optionsPairs as $pair) {
            list($value, $label) = array_map('trim', explode(':', $pair));
            $options[$value]   = $label;
        }
        return $options;
    }

    // Shortcode for form start
    public function shortcodeFormStart($atts)
    {
        $attributes = shortcode_atts([
            'action' => '',
            'method' => 'post',
            'class'  => '',
            'id'     => '',
        ], $atts);

        $action = esc_attr($attributes['action']);
        $method = esc_attr($attributes['method']);
        $class  = esc_attr($attributes['class']);
        $id     = esc_attr($attributes['id']);

        return "<form id='{$id}' action='{$action}' method='{$method}' class='{$class}' novalidate>";
    }

    // Shortcode for form end
    public function shortcodeFormEnd()
    {
        return "</form>";
    }

    // Shortcode for rendering an input
    public function shortcodeInput($atts)
    {
        $attributes = shortcode_atts([
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
        ], $atts);

        // Convert the array to an object
        $inputObject = (object) $attributes;

        return $this->renderInput($inputObject);
    }

    // Register shortcodes with WordPress
    private function registerShortcodes()
    {
        add_shortcode('form_start', [$this, 'shortcodeFormStart']);
        add_shortcode('form_end', [$this, 'shortcodeFormEnd']);
        add_shortcode('form_input', [$this, 'shortcodeInput']);
    }

    public function defaultShortcodes()
    {
        $shortcodes = '';

        $shortcodes .= "[form_input type=\"text\" id=\"bookingnumber\" label=\"Booking number\" required=\"true\"]\n";
        $shortcodes .= "[form_input type=\"text\" id=\"fullname\" label=\"Fullname\" required=\"true\"]\n";
        $shortcodes .= "[form_input type=\"text\" id=\"passport\" label=\"Passport number\" required=\"true\"]\n";
        $shortcodes .= "[form_input type=\"email\" id=\"email\" label=\"e-Mail\"]\n";
        $shortcodes .= "[form_input type=\"tel\" id=\"phone\" label=\"Phone number\"]\n";
        $shortcodes .= "[form_input type=\"date\" id=\"checkin-date\" label=\"Check-In Date\"]\n";
        $shortcodes .= "[form_input type=\"date\" id=\"checkout-date\" label=\"Check-Out Date\"]\n";
        $shortcodes .= "[form_input type=\"select\" id=\"countries\" name=\"countries\" class=\"form-control\" value=\"\" target=\"countries\" label=\"Countries\" required=\"true\"]\n";
        $shortcodes .= "[form_input type=\"checkbox\" id=\"checkbox1\" label=\"Agree to Terms\" name=\"termsCheckbox\" required=\"true\"]\n";
        $shortcodes .= "[form_input type=\"signature\" id=\"signature\" label=\"Signature\" name=\"signature\"]\n";

        return $shortcodes;
    }
}

// Instantiate the class
new \Staylodgic\FormGenerator();
