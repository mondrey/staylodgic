<?php
namespace AtollMatrix;

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

        $type        = esc_attr($inputObject->type);
        $id          = esc_attr($inputObject->id);
        $name        = esc_attr($inputObject->name ?? $id);
        $class       = esc_attr($inputObject->class ?? 'form-control');
        $value       = esc_attr($inputObject->value ?? '');
        $placeholder = esc_attr($inputObject->placeholder ?? '');
        $label       = isset($inputObject->label) ? esc_html($inputObject->label) : null;
        $required = isset($inputObject->required) && $inputObject->required === 'true' ? 'required' : '';

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
            case 'date':
            case 'time':
            case 'email':
            case 'password':
                // Common types of inputs
                echo "<input data-label='$label' placeholder='$label' type='$type' id='$id' name='$name' class='$class' value='$value' placeholder='$placeholder' $required>";
                break;
            case 'textarea':
                echo "<textarea data-label='$label' placeholder='$label' id='$id' name='$name' class='$class' placeholder='$placeholder' $required>$value</textarea>";
                break;
            case 'signature':
                echo '<div class="signature-container">
                <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
                <div id="clear-signature">Clear</div>
                <input data-label="'.$label.'" type="hidden" id="signature-data" name="signature-data">
                </div>';
                break;
            case 'checkbox':
                // Checkbox inputs
                $checked = isset($inputObject->checked) && $inputObject->checked ? 'checked' : '';
                echo "<input data-label='$label' type='checkbox' id='$id' name='$name' class='form-check-input' value='$value' $checked>";
                $label_class = 'form-check-label';
                break;
            case 'button':
            case 'submit':
                // Button types (button and submit)
                $value = esc_attr($inputObject->value ?? 'Button');
                echo "<button type='$type' id='$id' name='$name' class='$class'>$value</button>";
                break;
            case 'select':
                // [form_input type="select" id="mySelect" name="mySelect" class="form-control" value="option1" options="option1:Option 1,option2:Option 2,option3:Option 3"]
                if ( 'countries' == $inputObject->target ) {
                    $options = atollmatrix_country_list('select', '');
                } else {
                    $options = $this->parseSelectOptions($inputObject->options ?? '');
                }
                $countries = atollmatrix_country_list('select-alt', '');
                $options = $this->parseSelectOptions($countries);
                error_log( print_r($options, true ) );
                echo "<select data-label='$label' id='$id' name='$name' class='form-select' aria-label='Default select example'>";
                foreach ($options as $optionValue => $optionLabel) {
                    $selected = $optionValue == $value ? 'selected' : '';
                    echo "<option value='$optionValue' $selected>$optionLabel</option>";
                }
                echo "</select>";
                break;
            // Add more cases for different input types as needed
            default:
                throw new \Exception("Unsupported input type: $type");
        }

        // Render label if provided
        if ($label) {
            echo "<label for='$id' class='$label_class'>$label</label>";
        }

        echo '</div>';

        return ob_get_clean();
    }

// Helper function to parse select options
    private function parseSelectOptions($optionsString)
    {
        $options      = [  ];
        $optionsPairs = explode(',', $optionsString);
        foreach ($optionsPairs as $pair) {
            list($value, $label) = array_map('trim', explode(':', $pair));
            $options[ $value ]   = $label;
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
            'id'  => '',
         ], $atts);

        $action = esc_attr($attributes[ 'action' ]);
        $method = esc_attr($attributes[ 'method' ]);
        $class  = esc_attr($attributes[ 'class' ]);
        $id  = esc_attr($attributes[ 'id' ]);

        return "<form id='{$id}' action='{$action}' method='{$method}' class='{$class}'>";
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
            'target'     => '',
            'required'    => '',
        ], $atts);

        // Convert the array to an object
        $inputObject = (object) $attributes;

        return $this->renderInput($inputObject);
    }


// Register shortcodes with WordPress
    private function registerShortcodes()
    {
        add_shortcode('form_start', [ $this, 'shortcodeFormStart' ]);
        add_shortcode('form_end', [ $this, 'shortcodeFormEnd' ]);
        add_shortcode('form_input', [ $this, 'shortcodeInput' ]);
    }
}

// Instantiate the class
new FormGenerator();
