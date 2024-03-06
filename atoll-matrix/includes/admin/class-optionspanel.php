<?php

namespace AtollMatrix;

class OptionsPanel
{

    /**
     * Options panel arguments.
     */
    protected $args = [  ];

    /**
     * Options panel title.
     */
    protected $title = '';

    /**
     * Options panel title.
     */
    protected $parent_page = '';

    /**
     * Options panel slug.
     */
    protected $slug = '';

    /**
     * Option name to use for saving our options in the database.
     */
    protected $option_name = '';

    /**
     * Option group name.
     */
    protected $option_group_name = '';

    /**
     * User capability allowed to access the options page.
     */
    protected $user_capability = '';

    /**
     * Our array of settings.
     */
    protected $settings = [  ];

    /**
     * Our class constructor.
     */
    public function __construct(array $args, array $settings)
    {
        $this->args              = $args;
        $this->settings          = $settings;
        $this->parent_page       = $this->args[ 'parent_page' ] ?? esc_html__('atoll-matrix', 'atollmatrix');
        $this->title             = $this->args[ 'title' ] ?? esc_html__('Settings', 'atollmatrix');
        $this->slug              = $this->args[ 'slug' ] ?? sanitize_key($this->title);
        $this->option_name       = $this->args[ 'option_name' ] ?? sanitize_key($this->title);
        $this->option_group_name = $this->option_name . '_group';
        $this->user_capability   = $args[ 'user_capability' ] ?? 'manage_options';

        add_action('admin_menu', [ $this, 'export_settings' ]);
        add_action('admin_menu', [ $this, 'register_menu_page' ]);
        add_action('admin_init', [ $this, 'register_settings' ]);
        // Hook into admin_init to catch the form submission
        add_action('admin_init', [ $this, 'atollmatrix_import_settings' ]);

        add_action('admin_enqueue_scripts', [ $this, 'enqueue_media_uploader' ]);

    }

    public function atollmatrix_import_settings() {
        // Check if our nonce is set and verify it.
        if (
            isset($_POST['import_settings_nonce_field']) &&
            wp_verify_nonce($_POST['import_settings_nonce_field'], 'import_settings_nonce')
        ) {
            // Check if action is set to import_settings
            if (isset($_POST['action']) && $_POST['action'] === 'import_settings') {
                // Decode the JSON data
                $import_data = json_decode(stripslashes($_POST['import_settings_data']), true);

                error_log( '---------- IMPORT DATA ----------');
                error_log( print_r( $import_data, true ));

                // Validate the decoded data. This depends on your settings structure.
                // Here, we assume $import_data is an associative array matching your options structure.
                if (is_array($import_data)) {
                    // Iterate through each setting to validate and sanitize it
                    $sanitized_data = [];
                    foreach ($import_data as $key => $value) {
                        if (isset($this->settings[$key]) && $this->settings[$key]['type'] == 'checkbox') {
                            // Convert "1" to "on" for checkboxes
                            $sanitized_data[$key] = ($value == "1") ? 'on' : ''; // Convert 1 to 'on', anything else to '' (unchecked)
                        } elseif (is_array($value)) {
                            // The value is an array, handle each element according to its expected type
                            foreach ($value as $subKey => $subValue) {
                                if (is_array($subValue)) {
                                    // If the subValue is also an array, apply further sanitization as needed
                                    // This example assumes subValue might be a structured array needing detailed sanitization
                                    foreach ($subValue as $fieldKey => $fieldValue) {
                                        // Apply sanitization based on fieldKey or expected data type
                                        // This is a placeholder for actual sanitization logic
                                        $sanitized_data[$key][$subKey][$fieldKey] = sanitize_text_field($fieldValue);
                                    }
                                } else {
                                    // For simple nested arrays, directly apply a generic sanitization
                                    $sanitized_data[$key][$subKey] = sanitize_text_field($subValue);
                                }
                            }
                        } else {
                            // For other non-array settings, apply generic sanitization or specific based on type
                            $sanitized_data[$key] = sanitize_text_field($value);
                        }
                    }                    

                    error_log( '---------- Santized DATA ----------');
                    error_log( print_r( $sanitized_data, true ));

                    // Update the settings in the database
                    update_option('atollmatrix_settings', $sanitized_data);

                    // Optionally, add a message to show success or redirect back to the settings page
                    add_settings_error('atollmatrix_settings', 'settings_updated', 'Settings imported successfully.', 'updated');
                } else {
                    // Handle error in case JSON is invalid
                    add_settings_error('atollmatrix_settings', 'settings_error', 'Invalid JSON data provided.', 'error');
                }
            }
        }
    }


    public function enqueue_media_uploader() {
        wp_enqueue_media();
        wp_enqueue_style('thickbox'); // if not included
    }

    /**
     * Register the new menu page.
     */
    public function register_menu_page()
    {
        add_submenu_page(
            $this->parent_page,
            $this->title,
            $this->title,
            $this->user_capability,
            $this->slug,
            [ $this, 'render_options_page' ]
        );
    }

    /**
     * Register the settings.
     */
    public function export_settings()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'export_settings') {
            // Security check, for example, check user permissions and nonces
    
            // Fetch all settings
            $option_name = 'atollmatrix_settings'; // Replace with your actual option name
            $settings = get_option($option_name);
    
            // Encode settings to JSON
            $json_settings = json_encode($settings);
    
            // Set headers to force download
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="settings-export.json"');
            header('Pragma: no-cache');
            header('Expires: 0');
    
            // Serve the file
            echo $json_settings;
            exit;
        }
    }

    /**
     * Register the settings.
     */
    public function register_settings()
    {
        register_setting($this->option_group_name, $this->option_name, [
            'sanitize_callback' => [ $this, 'sanitize_fields' ],
            'default'           => $this->get_defaults(),
         ]);

        add_settings_section(
            $this->option_name . '_sections',
            false,
            false,
            $this->option_name
        );

        foreach ($this->settings as $key => $args) {
            $type     = $args[ 'type' ] ?? 'text';
            $callback = "render_{$type}_field";
            if (method_exists($this, $callback)) {
                $tr_class = '';
                if (array_key_exists('tab', $args)) {
                    $tr_class .= 'atollmatrix-tab-item atollmatrix-tab-item--' . sanitize_html_class($args[ 'tab' ]);
                }
                add_settings_field(
                    $key,
                    $args[ 'label' ],
                    [ $this, $callback ],
                    $this->option_name,
                    $this->option_name . '_sections',
                    [
                        'label_for' => $key,
                        'class'     => $tr_class,
                     ]
                );
            }
        }
    }

    /**
     * Saves our fields.
     */
    public function sanitize_fields($values) {
        $new_values = [];
        foreach ($this->settings as $key => $args) {
            if (isset($values[$key])) {
                // List of keys that use the same sanitization function
                $discount_keys = ['discount_lastminute', 'discount_earlybooking', 'discount_longstay'];
        
                if (in_array($key, $discount_keys)) {
                    $new_values[$key] = $this->sanitize_discount_fileds($values[$key]);
                } else {
                    // Handle other fields as before
                    $field_type = $args['type'];
                    $sanitize_callback = $args['sanitize_callback'] ?? $this->get_sanitize_callback_by_type($field_type);
                    $new_values[$key] = call_user_func($sanitize_callback, $values[$key], $args);
                }
            }
        }        
        return $new_values;
    }
    
    protected function sanitize_discount_fileds($value) {
        $sanitized_value = [];
        if (is_array($value)) {
            foreach ($value as $sub_key => $sub_value) {
                switch ($sub_key) {
                    case 'label':
                        $sanitized_value['label'] = sanitize_text_field($sub_value);
                        break;
                    case 'days':
                        // Assuming 'days' should be an integer
                        $sanitized_value['days'] = intval($sub_value);
                        break;
                    case 'percent':
                        // Assuming 'percent' should be a float
                        $sanitized_value['percent'] = floatval($sub_value);
                        break;
                }
            }
        }
        return $sanitized_value;
    }

    /**
     * Returns sanitize callback based on field type.
     */
    protected function get_sanitize_callback_by_type($field_type)
    {
        switch ($field_type) {
            case 'select':
                return [ $this, 'sanitize_select_field' ];
                break;
            case 'textarea':
                return 'wp_kses_post';
                break;
            case 'checkbox':
                return [ $this, 'sanitize_checkbox_field' ];
                break;
            case 'repeatable_tax':
                return [ $this, 'sanitize_tax_field' ];
                break;
            case 'activity_repeatable_tax':
                return [ $this, 'sanitize_tax_field' ];
                break;
            case 'repeatable_perperson':
                return [ $this, 'sanitize_tax_field' ];
                break;
            case 'repeatable_mealplan':
                return [ $this, 'sanitize_tax_field' ];
                break;
            case 'media_upload':
                return 'absint';
                break;
            default:
            case 'text':
                return 'sanitize_text_field';
                break;
        }
    }

    /**
     * Returns default values.
     */
    protected function get_defaults()
    {
        $defaults = [  ];
        foreach ($this->settings as $key => $args) {
            $defaults[ $key ] = $args[ 'default' ] ?? '';
        }
        return $defaults;
    }

    /**
     * Sanitizes the tax field.
     */
    protected function sanitize_tax_field($value = '', $field_args = [  ])
    {
        return $value;
    }
    /**
     * Sanitizes the checkbox field.
     */
    protected function sanitize_checkbox_field($value = '', $field_args = [  ])
    {
        return ('on' === $value) ? 1 : 0;
    }

    /**
     * Sanitizes the select field.
     */
    protected function sanitize_select_field($value = '', $field_args = [  ])
    {
        $choices = $field_args[ 'choices' ] ?? [  ];
        if (array_key_exists($value, $choices)) {
            return $value;
        }
    }

    /**
     * Renders the options page.
     */
    public function render_options_page()
    {
        if (!current_user_can($this->user_capability)) {
            return;
        }

        if (isset($_GET[ 'settings-updated' ])) {
            add_settings_error(
                $this->option_name . '_mesages',
                $this->option_name . '_message',
                esc_html__('Settings Saved', 'atollmatrix'),
                'updated'
            );

            \AtollMatrix\Cache::clearAllCache();
        }

        settings_errors($this->option_name . '_mesages');

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="atollmatrix-tabform-wrapper">
            <?php $this->render_tabs();?>
            <div class="atollmatrix-tab-content active" id="tab-property">
            <form action="options.php" method="post" class="atollmatrix-options-form">
                <?php
settings_fields($this->option_group_name);
        do_settings_sections($this->option_name);
        submit_button('Save Settings');
        ?>
            </form>
            </div>
            </div>
            <?php
    // Add an export button
    echo '<form method="post">';
    echo '<input type="hidden" name="action" value="export_settings" />';
    submit_button('Export Settings');
    echo '</form>';
?>
<?php
// Button to open the import modal
echo '<button type="button" id="import-settings-button" class="button-secondary">Import Settings</button>';

// Modal structure
echo '<div id="import-settings-modal" class="atollmatrix-modal" style="display:none;">
        <div class="atollmatrix-modal-content">
            <span class="atollmatrix-close">&times;</span>
            <form id="import-settings-form" method="post">
                ' . wp_nonce_field('import_settings_nonce', 'import_settings_nonce_field', true, false) . '
                <textarea name="import_settings_data" rows="10" cols="50" placeholder="Paste JSON data here"></textarea>
                <input type="hidden" name="action" value="import_settings">
                <input type="submit" class="button-primary" value="Import Settings">
            </form>
        </div>
      </div>';
?>
        </div>
        <?php
}

protected function render_tabs()
{
    if (empty($this->args['tabs'])) {
        return;
    }

    $tabs = $this->args['tabs'];
    ?>
    <div class="nav-tab-wrapper atollmatrix-tabs">
        <div class="atollmatrix-tabs-container">
            <?php
            $first_tab = true;
            // Example heading for a group of tabs
            echo '<h3 class="atollmatrix-tab-heading">General Settings</h3>';
            foreach ($tabs as $id => $label) {
                // Example condition to add a heading before a specific tab
                if ($id === 'general') {
                    echo '<h3 class="atollmatrix-tab-heading">Hotel Settings</h3>';
                }
                ?>
                <a href="#" data-tab="<?php echo esc_attr($id); ?>" class="nav-tab<?php echo ($first_tab) ? ' nav-tab-active' : ''; ?>"><?php echo ucfirst($label); ?></a>
                <?php
                $first_tab = false;
            }
            ?>
        </div>
    </div>
    <?php
}


    /**
     * Returns an option value.
     */
    protected function get_option_value($option_name)
    {
        $option = get_option($this->option_name);
        if ( is_array( $option )) {
            if (!array_key_exists($option_name, $option)) {
                return array_key_exists('default', $this->settings[ $option_name ]) ? $this->settings[ $option_name ][ 'default' ] : '';
            }
        } else {
            return '';
        }
        return $option[ $option_name ];
    }

    // Media uploading
    public function render_media_upload_field($args) {
        $option_name = $args['label_for'];
        $value = $this->get_option_value($option_name);
        $image = ' button">Upload image';
        $image_size = 'full'; // it should be thumbnail, medium or large
        $display = 'none'; // display state of the "Remove image" button
    
        if ($value) {
            $image_attributes = wp_get_attachment_image_src($value, $image_size);
            if ($image_attributes) {
                $image = '"><img src="' . esc_url($image_attributes[0]) . '" style="max-height:100px;display:block;" />';
                $display = 'inline-block';
            }
        }
    
        echo '
        <div>
            <a href="#" class="upload_image_button' . $image . '</a>
            <input type="hidden" name="' . $this->option_name . '[' . esc_attr($args['label_for']) . ']" id="' . esc_attr($args['label_for']) . '" value="' . esc_attr($value) . '" />
            <a href="#" class="remove_image_button" style="display:' . esc_attr($display) . '">Remove image</a>
        </div>';
    }
    

/**
 * Renders perperson field.
 */
    public function render_repeatable_perperson_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

        ?>
<div class="repeatable-perperson-template" style="display: none;">
<div class="repeatable">
            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_people"
            name="people"
            >
            <option value="1"><?php _e('1', 'atollmatrix');?></option>
            <option value="3"><?php _e('3', 'atollmatrix');?></option>
            <option value="4"><?php _e('4', 'atollmatrix');?></option>
            <option value="5"><?php _e('5', 'atollmatrix');?></option>
            <option value="6"><?php _e('6', 'atollmatrix');?></option>
            <option value="7"><?php _e('7', 'atollmatrix');?></option>
            <option value="8"><?php _e('8', 'atollmatrix');?></option>
            <option value="9"><?php _e('9', 'atollmatrix');?></option>
            </select>
            <input disabled
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number"
                name="number"
                value="">
            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type"
            name="type"
            >
            <option value="fixed"><?php _e('Fixed', 'atollmatrix');?></option>
            <option value="percentage"><?php _e('Percentage', 'atollmatrix');?></option>
            </select>
            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_total"
            name="total"
            >
            <option value="increase"><?php _e('Increase', 'atollmatrix');?></option>
            <option value="decrease"><?php _e('Decrease', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
</div>
<div id="repeatable-perperson-container">
<?php

        $count = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if (isset($value[ 'people' ])) {
                    ?>
            <div class="repeatable">
            <select
            data-width="80"
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_people_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][people]"
            >
            <option value="1" <?php selected('1', $value[ 'people' ], true);?>><?php _e('1', 'atollmatrix');?></option>
            <option value="3" <?php selected('3', $value[ 'people' ], true);?>><?php _e('3', 'atollmatrix');?></option>
            <option value="4" <?php selected('4', $value[ 'people' ], true);?>><?php _e('4', 'atollmatrix');?></option>
            <option value="5" <?php selected('5', $value[ 'people' ], true);?>><?php _e('5', 'atollmatrix');?></option>
            <option value="6" <?php selected('6', $value[ 'people' ], true);?>><?php _e('6', 'atollmatrix');?></option>
            <option value="7" <?php selected('7', $value[ 'people' ], true);?>><?php _e('7', 'atollmatrix');?></option>
            <option value="8" <?php selected('8', $value[ 'people' ], true);?>><?php _e('8', 'atollmatrix');?></option>
            <option value="9" <?php selected('9', $value[ 'people' ], true);?>><?php _e('9', 'atollmatrix');?></option>
            </select>
            <input
                type="text"
                class="perpersonpricing_number_setter"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][number]"
                value="<?php echo esc_attr($value[ 'number' ]); ?>">
            <select
            data-width="150"
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][type]"
            >
            <option value="fixed" <?php selected('fixed', $value[ 'type' ], true);?>><?php _e('Fixed', 'atollmatrix');?></option>
            <option value="percentage" <?php selected('percentage', $value[ 'type' ], true);?>><?php _e('Percentage', 'atollmatrix');?></option>
            </select>
            <select
            data-width="150"
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_total_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][total]"
            >
            <option value="increase" <?php selected('increase', $value[ 'total' ], true);?>><?php _e('Increase', 'atollmatrix');?></option>
            <option value="decrease" <?php selected('decrease', $value[ 'total' ], true);?>><?php _e('Decrease', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
        <?php
}
            }
        }
        ?>
        </div>
        <button id="addperperson-repeatable"><?php _e('Add New Section', 'atollmatrix');?></button>
            <?php
if ($description) {
            ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

/**
 * Renders Mealplan field.
 */
    public function render_repeatable_mealplan_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 3);
        // }
        error_log('----- mealplan array -----');
        error_log(print_r($array, 1));

        ?>
<div class="repeatable-mealplan-template" style="display: none;">
<div class="repeatable">
        <select disabled
        id="<?php echo esc_attr($args[ 'label_for' ]); ?>_mealtype"
        name="mealtype"
        >
        <option value="RO"><?php _e('Room Only', 'atollmatrix');?></option>
        <option value="BB"><?php _e('Bed and Breakfast', 'atollmatrix');?></option>
        <option value="HB"><?php _e('Half Board', 'atollmatrix');?></option>
        <option value="FB"><?php _e('Full Board', 'atollmatrix');?></option>
        <option value="AN"><?php _e('All-Inclusive', 'atollmatrix');?></option>
        </select>
        <select disabled
        id="<?php echo esc_attr($args[ 'label_for' ]); ?>_choice"
        name="choice"
        >
        <option value="included"><?php _e('Included in rate', 'atollmatrix');?></option>
        <option value="optional"><?php _e('Optional', 'atollmatrix');?></option>
        </select>
        <input disabled
            type="text"
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_price"
            name="price"
            value="">
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
        <br/>
        </div>
</div>
<div id="repeatable-mealplan-container">
<?php
$count = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if (isset($value[ 'mealtype' ])) {
                    ?>
            <div class="repeatable">
                <select
                data-width="170"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_mealtype_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][mealtype]"
                >
                <option value="RO" <?php selected('RO', $value[ 'mealtype' ], true);?>><?php _e('Room Only', 'atollmatrix');?></option>
                <option value="BB" <?php selected('BB', $value[ 'mealtype' ], true);?>><?php _e('Bed and Breakfast', 'atollmatrix');?></option>
                <option value="HB" <?php selected('HB', $value[ 'mealtype' ], true);?>><?php _e('Half Board', 'atollmatrix');?></option>
                <option value="FB" <?php selected('FB', $value[ 'mealtype' ], true);?>><?php _e('Full Board', 'atollmatrix');?></option>
                <option value="AN" <?php selected('AN', $value[ 'mealtype' ], true);?>><?php _e('All-Inclusive', 'atollmatrix');?></option>
                </select>
            <select
            data-width="150"
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_choice_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][choice]"
            >
            <option value="included" <?php selected('included', $value[ 'choice' ], true);?>><?php _e('Included in rate', 'atollmatrix');?></option>
            <option value="optional" <?php selected('optional', $value[ 'choice' ], true);?>><?php _e('Optional', 'atollmatrix');?></option>
            </select>
            <input
                type="text"
                class="mealplan-style-setter"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_price_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][price]"
                value="<?php echo esc_attr($value[ 'price' ]); ?>">

            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
        <?php
}
            }
        }
        ?>
    </div>
    <button id="addmealplan-repeatable"><?php _e('Add New Section', 'atollmatrix');?></button>
        <?php
if ($description) {
            ?>
            <p class="description"><?php echo esc_html($description); ?></p>
        <?php
}
        ?>
    <?php
}

    /**
     * Renders tax field.
     */
    public function render_repeatable_tax_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

        ?>
<div class="repeatable-tax-template" style="display: none;">
<div class="repeatable">
<span class="dashicons dashicons-sort drag-handle"></span>
            <input disabled
                type="text" placeholder = "Label"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_label"
                name="label"
                value="">
            <input disabled
                type="text" placeholder = "Value"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number"
                name="number"
                value="">

            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type"
            name="type"
            >
            <option value="fixed">Fixed</option>
            <option value="percentage">Percentage</option>
            </select>
            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_duration"
            name="duration"
            >
            <option value="inrate"><?php _e('Add to rate', 'atollmatrix');?></option>
            <option value="perperson"><?php _e('Per person', 'atollmatrix');?></option>
            <option value="perday"><?php _e('Per day', 'atollmatrix');?></option>
            <option value="perpersonperday"><?php _e('Per person per day', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
</div>
<div id="repeatable-tax-container">
<?php

        $count = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if (isset($value[ 'label' ])) {
                    ?>
            <div class="repeatable">
            <span class="dashicons dashicons-sort drag-handle"></span>
            <input
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_label_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][label]"
                value="<?php echo esc_attr($value[ 'label' ]); ?>">

            <input
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][number]"
                value="<?php echo esc_attr($value[ 'number' ]); ?>">

            <select
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][type]"
            >
            <option value="fixed" <?php selected('fixed', $value[ 'type' ], true);?>><?php _e('Fixed', 'atollmatrix');?></option>
            <option value="percentage" <?php selected('percentage', $value[ 'type' ], true);?>><?php _e('Percentage', 'atollmatrix');?></option>
            </select>
            <select
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_duration_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][duration]"
            >
            <option value="inrate" <?php selected('inrate', $value[ 'duration' ], true);?>><?php _e('Add to rate', 'atollmatrix');?></option>
            <option value="perperson" <?php selected('perperson', $value[ 'duration' ], true);?>><?php _e('Per person', 'atollmatrix');?></option>
            <option value="perday" <?php selected('perday', $value[ 'duration' ], true);?>><?php _e('Per Day', 'atollmatrix');?></option>
            <option value="perpersonperday" <?php selected('perpersonperday', $value[ 'duration' ], true);?>><?php _e('Per person per day', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
        <?php
}
            }
        }
        ?>
        </div>
        <button id="addtax-repeatable"><?php _e('Add New Section', 'atollmatrix');?></button>
            <?php
if ($description) {
            ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

    /**
     * Renders tax field.
     */
    public function render_activity_repeatable_tax_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

        ?>
<div class="repeatable-activitytax-template" style="display: none;">
<div class="repeatable">
<span class="dashicons dashicons-sort drag-handle"></span>
            <input disabled
                type="text" placeholder = "Label"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_label"
                name="label"
                value="">
            <input disabled
                type="text" placeholder = "Value"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number"
                name="number"
                value="">

            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type"
            name="type"
            >
            <option value="fixed">Fixed</option>
            <option value="percentage">Percentage</option>
            </select>
            <select disabled
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_duration"
            name="duration"
            >
            <option value="inrate"><?php _e('Add to rate', 'atollmatrix');?></option>
            <option value="perperson"><?php _e('Per person', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
</div>
<div id="repeatable-activitytax-container">
<?php

        $count = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if (isset($value[ 'label' ])) {
                    ?>
            <div class="repeatable">
            <span class="dashicons dashicons-sort drag-handle"></span>
            <input
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_label_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][label]"
                value="<?php echo esc_attr($value[ 'label' ]); ?>">

            <input
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>_number_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][number]"
                value="<?php echo esc_attr($value[ 'number' ]); ?>">

            <select
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_type_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][type]"
            >
            <option value="fixed" <?php selected('fixed', $value[ 'type' ], true);?>><?php _e('Fixed', 'atollmatrix');?></option>
            <option value="percentage" <?php selected('percentage', $value[ 'type' ], true);?>><?php _e('Percentage', 'atollmatrix');?></option>
            </select>
            <select
            id="<?php echo esc_attr($args[ 'label_for' ]); ?>_duration_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>][<?php echo $key; ?>][duration]"
            >
            <option value="inrate" <?php selected('inrate', $value[ 'duration' ], true);?>><?php _e('Add to rate', 'atollmatrix');?></option>
            <option value="perperson" <?php selected('perperson', $value[ 'duration' ], true);?>><?php _e('Per person', 'atollmatrix');?></option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
        <?php
}
            }
        }
        ?>
        </div>
        <button id="addtax-activity-repeatable"><?php _e('Add New Section', 'atollmatrix');?></button>
            <?php
if ($description) {
            ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

/**
 * Renders a text field.
 */
public function render_promotion_discount_field($args)
{
    $option_name = $args['label_for'];
    $values = $this->get_option_value($option_name);
    
    // Ensure $values is an array and set default values if not set
    $values = is_array($values) ? $values : ['label' => '', 'days' => '', 'percent' => ''];
    // error_log( print_r( $values,1 ));
    $description = $this->settings[$option_name]['description'] ?? '';
    ?>
        <input
            type="text"
            id="<?php echo esc_attr($args['label_for']); ?>_label"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][label]"
            value="<?php echo esc_attr($values['label']); ?>" placeholder="Label for discount">
        <input
            type="text"
            id="<?php echo esc_attr($args['label_for']); ?>_days"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][days]"
            value="<?php echo esc_attr($values['days']); ?>" placeholder="Number of days">
        <input
            type="text"
            id="<?php echo esc_attr($args['label_for']); ?>_percent"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][percent]"
            value="<?php echo esc_attr($values['percent']); ?>" placeholder="Discount percent">
        <?php
    if ($description) {
        ?>
            <p class="description"><?php echo esc_html($description); ?></p>
            <p class="description"><strong>Discounts are not stackable. Only the maximum discount is applied if multiple discounts are eligible.</p>
        <?php
    }
    ?>
    <?php
}

    /**
     * Renders a text field.
     */
    public function render_text_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';
        ?>
            <input
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>]"
                value="<?php echo esc_attr($value); ?>">
            <?php
if ($description) {
            ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

    /**
     * Renders a textarea field.
     */
    public function render_textarea_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';
        $rows        = $this->settings[ $option_name ][ 'rows' ] ?? '4';
        $cols        = $this->settings[ $option_name ][ 'cols' ] ?? '50';
        ?>
            <textarea
                type="text"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>"
                rows="<?php echo esc_attr(absint($rows)); ?>"
                cols="<?php echo esc_attr(absint($cols)); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>]"><?php echo esc_attr($value); ?></textarea>
            <?php if ($description) {?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

    /**
     * Renders a checkbox field.
     */
    public function render_checkbox_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';
        ?>
        <label class="atollmatrix-checkbox-container">
            <input
                type="checkbox"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>]"
                <?php checked($value, 1, true);?>
            >
            <span class="checkmark"></span>
    </label>
            <?php if ($description) {?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

    /**
     * Renders a select field.
     */
    public function render_select_field($args)
    {
        $option_name = $args[ 'label_for' ];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[ $option_name ][ 'description' ] ?? '';
        $choices     = $this->settings[ $option_name ][ 'choices' ] ?? [  ];
        $inputwidth  = $this->settings[ $option_name ][ 'inputwidth' ] ?? '';
        ?>
            <select
                data-width="<?php echo esc_attr($inputwidth); ?>"
                id="<?php echo esc_attr($args[ 'label_for' ]); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args[ 'label_for' ]); ?>]"
            >
            <?php
foreach ($choices as $choice_v => $label) {?>
                <option value="<?php echo esc_attr($choice_v); ?>" <?php selected($choice_v, $value, true);?>><?php echo esc_html($label); ?></option>
                <?php
}
        ?>
            </select>
            <?php
if ($description) {
            ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php
}
        ?>
        <?php
}

}

// Register new Options panel.
$panel_args = [
    'parent_page'     => 'atoll-matrix',
    'title'           => 'Settings',
    'option_name'     => 'atollmatrix_settings',
    'slug'            => 'atollmatrix-settings-panel',
    'user_capability' => 'manage_options',
    'tabs'            => [
        'property'       => esc_html__('Property', 'atollmatrix'),
        'activity-property'       => esc_html__('Activity Property', 'atollmatrix'),
        'currency'      => esc_html__('Currency', 'atollmatrix'),
        'general'       => esc_html__('General', 'atollmatrix'),
        'pages'         => esc_html__('Pages', 'atollmatrix'),
        'discounts' => esc_html__('Discounts', 'atollmatrix'),
        'mealplan'      => esc_html__('Meal Plan', 'atollmatrix'),
        'perperson'     => esc_html__('Per person price', 'atollmatrix'),
        'tax'           => esc_html__('Room Tax', 'atollmatrix'),
        'activity-tax'           => esc_html__('Activity Tax', 'atollmatrix'),
        'import-export' => esc_html__('Import/Export', 'atollmatrix'),
     ],
 ];

$currencies       = \AtollMatrix\Common::get_atollmatrix_currencies();
$currency_symbols = \AtollMatrix\Common::get_atollmatrix_currency_symbols();
$curr_choices     = array();

// Generate the select list
foreach ($currencies as $currencyCode => $currencyName) {
    $currency_symbol               = $currency_symbols[ $currencyCode ];
    $curr_choices[ $currencyCode ] = $currencyName . ' ( ' . $currency_symbol . ' )';
}

$panel_settings = [
    'property_logo'         => [
        'label'       => esc_html__('Upload Logo', 'atollmatrix'),
        'type'        => 'media_upload',
        'description' => 'Upload your logo here.',
        'tab'         => 'property', // You can change the tab as per your requirement
    ],
    'property_name'             => [
        'label'       => esc_html__('Name', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'property',
    ],
    'property_phone'             => [
        'label'       => esc_html__('Phone', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'property',
    ],
    'property_address'   => [
        'label'       => esc_html__('Address', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'property',
    ],
    'property_header'   => [
        'label'       => esc_html__('Invoice header', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'property',
    ],
    'property_footer'   => [
        'label'       => esc_html__('Invoice footer', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'property',
    ],

    'activity_property_logo'         => [
        'label'       => esc_html__('Upload Logo', 'atollmatrix'),
        'type'        => 'media_upload',
        'description' => 'Upload your logo here.',
        'tab'         => 'activity-property', // You can change the tab as per your requirement
    ],
    'activity_property_name'             => [
        'label'       => esc_html__('Name', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'activity-property',
    ],
    'activity_property_phone'             => [
        'label'       => esc_html__('Phone', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'activity-property',
    ],
    'activity_property_address'   => [
        'label'       => esc_html__('Address', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'activity-property',
    ],
    'activity_property_header'   => [
        'label'       => esc_html__('Invoice header', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'activity-property',
    ],
    'activity_property_footer'   => [
        'label'       => esc_html__('Invoice footer', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'activity-property',
    ],

    'page_bookingsearch'   => [
        'label'       => esc_html__('Booking search page', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Booking search page.',
        'choices'     => atollmatrix_get_pages_for_select(),
        'tab'         => 'pages',
     ],
     'page_activitybookingsearch'   => [
        'label'       => esc_html__('Activities search page', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Activities search page.',
        'choices'     => atollmatrix_get_pages_for_select(),
        'tab'         => 'pages',
     ],
    'page_bookingdetails'  => [
        'label'       => esc_html__('Booking details', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Booking details.',
        'choices'     => atollmatrix_get_pages_for_select(),
        'tab'         => 'pages',
     ],
    'import_missing'       => [
        'label'       => esc_html__('Action for missing bookings', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Action for missing bookings on import.',
        'choices'     => [
            'cancel' => esc_html__('Cancel', 'atollmatrix'),
            'delete' => esc_html__('Delete', 'atollmatrix'),
         ],
        'tab'         => 'import-export',
     ],
    'qtysync_interval'     => [
        'label'       => esc_html__('Availability Sync interval', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Availability Sync interval',
        'choices'     => atollmatrix_sync_intervals(),
        'tab'         => 'import-export',
     ],
    'discount_lastminute'             => [
        'label'       => esc_html__('Last minute discount', 'atollmatrix'),
        'type'        => 'promotion_discount',
        'description' => 'Maximum days ahead for discount. More than the number of days from booking day the discount will not be applied.',
        'tab'         => 'discounts',
     ],
    'discount_earlybooking'             => [
        'label'       => esc_html__('Early booking discount', 'atollmatrix'),
        'type'        => 'promotion_discount',
        'description' => 'How many days ahead to apply discount. Less than the number of days from booking day the discount will not be applied.',
        'tab'         => 'discounts',
     ],
    'discount_longstay'             => [
        'label'       => esc_html__('Long stay discount', 'atollmatrix'),
        'type'        => 'promotion_discount',
        'description' => 'Lenght of days to stay to apply discount.',
        'tab'         => 'discounts',
     ],
    'enable_taxes'         => [
        'label'       => esc_html__('Enable Room Taxes', 'atollmatrix'),
        'type'        => 'checkbox',
        'description' => '',
        'tab'         => 'tax',
    ],
    'enable_activitytaxes'         => [
        'label'       => esc_html__('Enable Activties Taxes', 'atollmatrix'),
        'type'        => 'checkbox',
        'description' => '',
        'tab'         => 'activity-tax',
    ],
    'display_cancelled' => [
        'label'       => esc_html__('Display cancelled bookings in availability calendar', 'atollmatrix'),
        'type'        => 'checkbox',
        'description' => '',
        'tab'         => 'general',
     ],
    'new_bookingstatus'    => [
        'label'       => esc_html__('Choose status for new bookings', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Choose status for new bookings.',
        'choices'     => atollmatrix_get_new_booking_statuses(),
        'tab'         => 'general',
     ],
    'new_bookingsubstatus' => [
        'label'       => esc_html__('Choose sub status for new bookings', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Choose sub status for new bookings.',
        'choices'     => atollmatrix_get_booking_substatuses(),
        'tab'         => 'general',
     ],
    'timezone'             => [
        'label'       => esc_html__('Select Time Zone', 'atollmatrix'),
        'type'        => 'select',
        'description' => 'Select your time zone relative to GMT.',
        'choices'     => atollmatrix_get_GmtTimezoneChoices(),
        'tab'         => 'general',
     ],
    // Tab 2
    'option_3'             => [
        'label'       => esc_html__('Text Option', 'atollmatrix'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'tab-2',
     ],
    'option_4'             => [
        'label'       => esc_html__('Textarea Option', 'atollmatrix'),
        'type'        => 'textarea',
        'description' => 'My textarea field description.',
        'tab'         => 'tab-2',
     ],
    'taxes'                => [
        'label'       => esc_html__('Room taxes', 'atollmatrix'),
        'type'        => 'repeatable_tax',
        'description' => 'My textarea field description.',
        'tab'         => 'tax',
     ],
    'activity_taxes'                => [
        'label'       => esc_html__('Activity taxes', 'atollmatrix'),
        'type'        => 'activity_repeatable_tax',
        'description' => 'My textarea field description.',
        'tab'         => 'activity-tax',
     ],
     'childfreestay'        => [
        'label'       => esc_html__('Children under the age can stay for free', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '100',
        'description' => 'My select field description.',
        'choices'     => [
            '0'  => esc_html__('0', 'atollmatrix'),
            '1'  => esc_html__('1', 'atollmatrix'),
            '2'  => esc_html__('2', 'atollmatrix'),
            '3'  => esc_html__('3', 'atollmatrix'),
            '4'  => esc_html__('4', 'atollmatrix'),
            '5'  => esc_html__('5', 'atollmatrix'),
            '6'  => esc_html__('6', 'atollmatrix'),
            '7'  => esc_html__('7', 'atollmatrix'),
            '8'  => esc_html__('8', 'atollmatrix'),
            '9'  => esc_html__('9', 'atollmatrix'),
            '10' => esc_html__('10', 'atollmatrix'),
            '11' => esc_html__('11', 'atollmatrix'),
            '12' => esc_html__('12', 'atollmatrix'),
            '13' => esc_html__('13', 'atollmatrix'),
            '14' => esc_html__('14', 'atollmatrix'),
            '15' => esc_html__('15', 'atollmatrix'),
            '16' => esc_html__('16', 'atollmatrix'),
            '17' => esc_html__('17', 'atollmatrix'),
         ],
        'tab'         => 'perperson',
     ],
    'perpersonpricing'     => [
        'label'       => esc_html__('Per person price', 'atollmatrix'),
        'type'        => 'repeatable_perperson',
        'description' => 'My textarea field description.',
        'tab'         => 'perperson',
     ],
    'mealplan'             => [
        'label'       => esc_html__('Meal Plan', 'atollmatrix'),
        'type'        => 'repeatable_mealplan',
        'description' => 'My textarea field description.',
        'tab'         => 'mealplan',
     ],
    'currency'             => [
        'label'       => esc_html__('Currency', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'default'        => 'USD',
        'description' => 'My select field description.',
        'choices'     => $curr_choices,
        'tab'         => 'currency',
     ],
    'currency_position'    => [
        'label'       => esc_html__('Currency position', 'atollmatrix'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'default'        => 'left_space',
        'description' => 'My select field description.',
        'choices'     => [
            'left_space'  => esc_html__('Left with space', 'atollmatrix'),
            'right_space' => esc_html__('Right with space', 'atollmatrix'),
            'left'        => esc_html__('Left', 'atollmatrix'),
            'right'       => esc_html__('Right', 'atollmatrix'),
         ],
        'tab'         => 'currency',
     ],
    'thousand_seperator'   => [
        'label'       => esc_html__('Thousand seperator', 'atollmatrix'),
        'type'        => 'text',
        'default'        => ',',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
     ],
    'decimal_seperator'    => [
        'label'       => esc_html__('Decimal seperator', 'atollmatrix'),
        'type'        => 'text',
        'default'        => '.',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
     ],
    'number_of_decimals'   => [
        'label'       => esc_html__('Number of Decimals', 'atollmatrix'),
        'type'        => 'text',
        'default'        => '2',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
     ],
     'import_settings_data'   => [
        'label'       => esc_html__('Import Settings', 'atollmatrix'),
        'type'        => 'textarea',
        'default'        => '2',
        'description' => 'My field 1 description.',
        'tab'         => 'import',
     ],
 ];

new \AtollMatrix\OptionsPanel($panel_args, $panel_settings);
