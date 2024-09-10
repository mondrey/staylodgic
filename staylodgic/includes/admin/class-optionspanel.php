<?php

namespace Staylodgic;

class OptionsPanel
{

    /**
     * Options panel arguments.
     */
    protected $args = [];

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
    protected $settings = [];

    /**
     * Our class constructor.
     */
    public function __construct(array $args, array $settings)
    {
        $this->args              = $args;
        $this->settings          = $settings;
        $this->parent_page       = $this->args['parent_page'] ?? esc_html__('staylodgic', 'staylodgic');
        $this->title             = $this->args['title'] ?? esc_html__('Settings', 'staylodgic');
        $this->slug              = $this->args['slug'] ?? sanitize_key($this->title);
        $this->option_name       = $this->args['option_name'] ?? sanitize_key($this->title);
        $this->option_group_name = $this->option_name . '_group';
        $this->user_capability   = $args['user_capability'] ?? 'edit_posts';

        add_action('admin_menu', [$this, 'export_settings']);
        add_action('admin_menu', [$this, 'register_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
        // Hook into admin_init to catch the form submission
        add_action('admin_init', [$this, 'staylodgic_import_settings']);

        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
    }

// Function to create custom pages
public function create_custom_page($title, $template, $content, $slug) {
    $existing_page = get_page_by_path($slug, OBJECT, 'page');
    if ($existing_page) {
        return $existing_page->ID; // Return existing page ID if the page exists
    }

    $page_data = array(
        'post_title'    => $title,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_name'     => $slug, // Set the slug for the page
        'meta_input'    => array(
            '_wp_page_template' => $template,
        ),
    );

    $page_id = wp_insert_post($page_data);

    return $page_id;
}

// Function to create initial pages
public function create_initial_pages() {
    
    $pages = staylodgic_get_template_pages();

    foreach ($pages as $page) {
        $this->create_custom_page($page['title'], $page['template'], $page['content'], $page['slug']);
    }

    // After creating pages, create the menu
    $this->create_booking_menu();
}

// Function to create or update the booking menu
public function create_booking_menu() {
    $menu_name = 'booking-menu';
    $menu_exists = wp_get_nav_menu_object($menu_name);

    // Delete the existing menu if it exists
    if ($menu_exists) {
        wp_delete_nav_menu($menu_exists->term_id);
    }

    // Create a new menu
    $menu_id = wp_create_nav_menu($menu_name);

    // Get the template file names from theme options
    $menu_templates = array(
        'booking_menu_one' => staylodgic_get_option('booking_menu_one'),
        'booking_menu_two' => staylodgic_get_option('booking_menu_two'),
        'booking_menu_three' => staylodgic_get_option('booking_menu_three'),
        'booking_menu_four' => staylodgic_get_option('booking_menu_four'),
    );

    // Find pages by template files
    $menu_items = array();
    foreach ($menu_templates as $template) {
        $query = new \WP_Query(array(
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => $template,
            'posts_per_page' => 1,
        ));
        if ($query->have_posts()) {
            $query->the_post();
            $menu_items[] = get_the_ID();
        }
        wp_reset_postdata();
    }

    // Add new menu items
    foreach ($menu_items as $page_id) {
        if ($page_id) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-object-id' => $page_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
            ));
        }
    }

    // Set the menu as Main Menu and Mobile Menu
    // $locations = get_theme_mod('nav_menu_locations'); // Get all theme locations
    $locations['main_menu'] = $menu_id; // Assign the menu to Main Menu
    $locations['mobile_menu'] = $menu_id; // Assign the menu to Mobile Menu
    set_theme_mod('nav_menu_locations', $locations); // Update the locations
}

    public function staylodgic_import_settings()
    {
        // Check if our nonce is set and verify it.
        if (
            isset($_POST['import_settings_nonce_field']) &&
            wp_verify_nonce($_POST['import_settings_nonce_field'], 'import_settings_nonce')
        ) {

            if (isset($_POST['action']) && $_POST['action'] === 'import_settings' && isset($_FILES['import_settings_file'])) {

                $file = $_FILES['import_settings_file'];
                // Ensure the file was uploaded without errors
                if ($file['error'] === UPLOAD_ERR_OK && $file['type'] === 'application/json') {
                    // Read the file and decode the JSON data
                    $json_data = file_get_contents($file['tmp_name']);
                    $import_data = json_decode($json_data, true);

                    // Log the import data
                    error_log('---------- IMPORT DATA ----------');
                    error_log(print_r($import_data, true));

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

                        error_log('---------- Santized DATA ----------');
                        error_log(print_r($sanitized_data, true));

                        // Update the settings in the database
                        update_option('staylodgic_settings', $sanitized_data);

                        // Optionally, add a message to show success or redirect back to the settings page
                        add_settings_error('staylodgic_settings', 'settings_updated', 'Settings imported successfully.', 'updated');
                    } else {
                        // Handle error in case JSON is invalid
                        add_settings_error('staylodgic_settings', 'settings_error', 'Invalid JSON data provided.', 'error');
                    }
                }
            }
        }
    }


    public function enqueue_media_uploader()
    {
        wp_enqueue_media();
        wp_enqueue_style('thickbox'); // if not included
    }

    /**
     * Register the new menu page.
     */
    public function register_menu_page()
    {
        add_submenu_page(
            'staylodgic-settings',
            $this->title,
            $this->title,
            $this->user_capability,
            $this->slug,
            [$this, 'render_options_page']
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
            $option_name = 'staylodgic_settings'; // Replace with your actual option name
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
    public function section_heading_callback()
    {
        echo '<h2 class="section_heading">' . esc_html($this->option_name) . '</h2>';
    }
    /**
     * Register the settings.
     */
    public function register_settings()
    {
        register_setting($this->option_group_name, $this->option_name, [
            'sanitize_callback' => [$this, 'sanitize_fields'],
            'default'           => $this->get_defaults(),
        ]);

        add_settings_section(
            $this->option_name . '_sections',
            '', // Set the title to an empty string because we'll add it in the callback
            [$this, 'section_heading_callback'], // Use the custom callback
            $this->option_name
        );

        foreach ($this->settings as $key => $args) {
            $type     = $args['type'] ?? 'text';
            $callback = "render_{$type}_field";
            if (method_exists($this, $callback)) {
                $tr_class = '';
                if (array_key_exists('tab', $args)) {
                    $tr_class .= 'staylodgic-tab-item staylodgic-tab-item--' . sanitize_html_class($args['tab']);
                }
                add_settings_field(
                    $key,
                    $args['label'],
                    [$this, $callback],
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
    public function sanitize_fields($values)
    {
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

    protected function sanitize_discount_fileds($value)
    {
        $sanitized_value = [];
        if (is_array($value)) {
            foreach ($value as $sub_key => $sub_value) {
                switch ($sub_key) {
                    case 'label':
                        $sanitized_value['label'] = sanitize_text_field($sub_value);
                        break;
                    case 'days':
                        $sanitized_value['days'] = intval($sub_value);
                        break;
                    case 'percent':
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
                return [$this, 'sanitize_select_field'];
                break;
            case 'number':
                return 'sanitize_text_field';
                break;
            case 'textarea':
                return 'wp_kses_post';
                break;
            case 'checkbox':
                return [$this, 'sanitize_checkbox_field'];
                break;
            case 'repeatable_tax':
                return [$this, 'sanitize_tax_field'];
                break;
            case 'activity_repeatable_tax':
                return [$this, 'sanitize_tax_field'];
                break;
            case 'repeatable_perperson':
                return [$this, 'sanitize_tax_field'];
                break;
            case 'repeatable_mealplan':
                return [$this, 'sanitize_tax_field'];
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
        $defaults = [];
        foreach ($this->settings as $key => $args) {
            $defaults[$key] = $args['default'] ?? '';
        }
        return $defaults;
    }

    /**
     * Sanitizes the tax field.
     */
    protected function sanitize_tax_field($value = '', $field_args = [])
    {
        return $value;
    }
    /**
     * Sanitizes the checkbox field.
     */
    protected function sanitize_checkbox_field($value = '', $field_args = [])
    {
        return ('on' === $value) ? 1 : 0;
    }

    /**
     * Sanitizes the select field.
     */
    protected function sanitize_select_field($value = '', $field_args = [])
    {
        $choices = $field_args['choices'] ?? [];
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
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                $this->option_name . '_mesages',
                $this->option_name . '_message',
                esc_html__('Settings Saved', 'staylodgic'),
                'updated'
            );

            \Staylodgic\Cache::clearAllCache();
            
            $this->create_initial_pages();
        }

        settings_errors($this->option_name . '_mesages');

?>
        <div class="wrap">
            <h1 class="options-heading"><span>Hotel Settings</span></h1>
            <div class="staylodgic-tabform-wrapper menu-closed">
                <?php $this->render_tabs(); ?>
                <div class="staylodgic-tab-content active" id="tab-property">
                    <form action="options.php" method="post" class="staylodgic-options-form">
                        <?php
                        settings_fields($this->option_group_name);
                        do_settings_sections($this->option_name);
                        submit_button('Save Settings');
                        echo '<div class="help-guide-footer">View Staylodgic <a target="_blank" href="https://staylodgic.com/staylodgic-help-guide-viewer/">Help Guide</a></div>';
                        ?>
                    </form>
                </div>
            </div>
            <?php
            // Add an export button
            echo '<form class="export-import-form" method="post">';
            echo '<div class="toggle-section">';
            echo '<div id="toggle-button"><i class="fa-solid fa-wrench"></i></div>';
            echo '</div>';
            echo '<div class="import-export-section" style="display: none;">';
            echo '<input type="hidden" name="action" value="export_settings" />';
            echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Export Settings">';
            echo '<button type="button" id="import-settings-button" class="button button-secondary">Import Settings</button>';
            echo '</div>';
            echo '</form>';
            ?>
            <?php
            // Modal structure
            echo '<div id="import-settings-modal" class="staylodgic-modal" style="display:none;">
        <div class="staylodgic-modal-content">
            <span class="staylodgic-close">&times;</span>
            <form id="import-settings-form" method="post" enctype="multipart/form-data">
            <div class="import-file-upload-section">
                ' . wp_nonce_field('import_settings_nonce', 'import_settings_nonce_field', true, false) . '
                <input type="file" name="import_settings_file" accept=".json">
                <input type="hidden" name="action" value="import_settings">
            </div>
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
        <div class="nav-tab-wrapper staylodgic-tabs">
            <div class="staylodgic-tabs-container">
                <?php
                $first_tab = true;
                // Example heading for a group of tabs
                echo '<h3 class="staylodgic-tab-heading">General Settings</h3>';
                foreach ($tabs as $id => $label) {
                    // Example condition to add a heading before a specific tab
                    if ($id === 'general') {
                        echo '<h3 class="staylodgic-tab-heading">Hotel Settings</h3>';
                    }
                ?>
                    <a href="#" data-heading="<?php echo esc_attr($label); ?>" data-tab="<?php echo esc_attr($id); ?>" class="nav-tab<?php echo ($first_tab) ? ' nav-tab-active' : ''; ?>"><?php echo ucfirst($label); ?></a>
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
        if (is_array($option)) {
            if (!array_key_exists($option_name, $option)) {
                return array_key_exists('default', $this->settings[$option_name]) ? $this->settings[$option_name]['default'] : '';
            }
        } else {
            return '';
        }
        return $option[$option_name];
    }

    // Media uploading
    public function render_media_upload_field($args)
    {
        $option_name = $args['label_for'];
        $value = $this->get_option_value($option_name);
        $image = 'Upload image';
        $image_size = 'full'; // it should be thumbnail, medium or large
        $display = 'none'; // display state of the "Remove image" button

        if ($value) {
            $image_attributes = wp_get_attachment_image_src($value, $image_size);
            if ($image_attributes) {
                $image = '<img src="' . esc_url($image_attributes[0]) . '" style="max-height:100px;display:block;" />';
                $display = 'inline-block';
            }
        }

        echo '
        <div class="options-image-display">
            <a href="#" class="upload_image_button button">' . $image . '</a>
            <input type="hidden" name="' . $this->option_name . '[' . esc_attr($args['label_for']) . ']" id="' . esc_attr($args['label_for']) . '" value="' . esc_attr($value) . '" />
            <a href="#" class="remove_image_button" style="display:' . esc_attr($display) . '"><i class="dashicons dashicons-remove"></i></a>
        </div>';
    }


    /**
     * Renders perperson field.
     */
    public function render_repeatable_perperson_field($args)
    {
        $option_name = $args['label_for'];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

    ?>
        <div class="repeatable-perperson-template" style="display: none;">
            <div class="repeatable">
                <span class="input-label-outer"><span class="input-label-inner">People</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_people" name="people">
                        <option value="1"><?php _e('1', 'staylodgic'); ?></option>
                        <option value="3"><?php _e('3', 'staylodgic'); ?></option>
                        <option value="4"><?php _e('4', 'staylodgic'); ?></option>
                        <option value="5"><?php _e('5', 'staylodgic'); ?></option>
                        <option value="6"><?php _e('6', 'staylodgic'); ?></option>
                        <option value="7"><?php _e('7', 'staylodgic'); ?></option>
                        <option value="8"><?php _e('8', 'staylodgic'); ?></option>
                        <option value="9"><?php _e('9', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Value</span>
                    <input disabled type="number" placeholder="Value" id="<?php echo esc_attr($args['label_for']); ?>_number" name="number" value="">
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Type</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_type" name="type">
                        <option value="fixed"><?php _e('Fixed', 'staylodgic'); ?></option>
                        <option value="percentage"><?php _e('Percentage', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Difference</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_total" name="total">
                        <option value="increase"><?php _e('Increase', 'staylodgic'); ?></option>
                        <option value="decrease"><?php _e('Decrease', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            </div>
        </div>
        <div id="repeatable-perperson-container">
            <?php

            $count = 0;
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $count++;
                    if (isset($value['people'])) {
            ?>
                        <div class="repeatable">
                            <span class="input-label-outer"><span class="input-label-inner">People</span>
                                <select data-width="80" id="<?php echo esc_attr($args['label_for']); ?>_people_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][people]">
                                    <option value="1" <?php selected('1', $value['people'], true); ?>><?php _e('1', 'staylodgic'); ?></option>
                                    <option value="3" <?php selected('3', $value['people'], true); ?>><?php _e('3', 'staylodgic'); ?></option>
                                    <option value="4" <?php selected('4', $value['people'], true); ?>><?php _e('4', 'staylodgic'); ?></option>
                                    <option value="5" <?php selected('5', $value['people'], true); ?>><?php _e('5', 'staylodgic'); ?></option>
                                    <option value="6" <?php selected('6', $value['people'], true); ?>><?php _e('6', 'staylodgic'); ?></option>
                                    <option value="7" <?php selected('7', $value['people'], true); ?>><?php _e('7', 'staylodgic'); ?></option>
                                    <option value="8" <?php selected('8', $value['people'], true); ?>><?php _e('8', 'staylodgic'); ?></option>
                                    <option value="9" <?php selected('9', $value['people'], true); ?>><?php _e('9', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Value</span>
                                <input type="number" class="perpersonpricing_number_setter" id="<?php echo esc_attr($args['label_for']); ?>_number_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][number]" value="<?php echo esc_attr($value['number']); ?>">
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Type</span>
                                <select data-width="150" id="<?php echo esc_attr($args['label_for']); ?>_type_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][type]">
                                    <option value="fixed" <?php selected('fixed', $value['type'], true); ?>><?php _e('Fixed', 'staylodgic'); ?></option>
                                    <option value="percentage" <?php selected('percentage', $value['type'], true); ?>><?php _e('Percentage', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Difference</span>
                                <select data-width="150" id="<?php echo esc_attr($args['label_for']); ?>_total_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][total]">
                                    <option value="increase" <?php selected('increase', $value['total'], true); ?>><?php _e('Increase', 'staylodgic'); ?></option>
                                    <option value="decrease" <?php selected('decrease', $value['total'], true); ?>><?php _e('Decrease', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <button id="addperperson-repeatable" class="button button-secondary"><?php _e('Add new', 'staylodgic'); ?></button>
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
        $option_name = $args['label_for'];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 3);
        // }
        // error_log('----- mealplan array -----');
        // error_log(print_r($array, 1));

    ?>
        <div class="repeatable-mealplan-template" style="display: none;">
            <div class="repeatable new-container">
                <span class="input-label-outer"><span class="input-label-inner">Meal</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_mealtype" name="mealtype">
                        <option value="RO"><?php _e('Room Only', 'staylodgic'); ?></option>
                        <option value="BB"><?php _e('Bed and Breakfast', 'staylodgic'); ?></option>
                        <option value="HB"><?php _e('Half Board', 'staylodgic'); ?></option>
                        <option value="FB"><?php _e('Full Board', 'staylodgic'); ?></option>
                        <option value="AN"><?php _e('All-Inclusive', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Type</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_choice" name="choice">
                        <option value="included"><?php _e('Included in rate', 'staylodgic'); ?></option>
                        <option value="optional"><?php _e('Optional', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Price</span>
                    <input disabled type="number" id="<?php echo esc_attr($args['label_for']); ?>_price" name="price" value="">
                </span>
                <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            </div>
        </div>
        <div id="repeatable-mealplan-container">
            <?php
            $count = 0;
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $count++;
                    if (isset($value['mealtype'])) {
            ?>
                        <div class="repeatable">
                            <span class="input-label-outer"><span class="input-label-inner">Meal</span>
                                <select data-width="170" id="<?php echo esc_attr($args['label_for']); ?>_mealtype_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][mealtype]">
                                    <option value="RO" <?php selected('RO', $value['mealtype'], true); ?>><?php _e('Room Only', 'staylodgic'); ?></option>
                                    <option value="BB" <?php selected('BB', $value['mealtype'], true); ?>><?php _e('Bed and Breakfast', 'staylodgic'); ?></option>
                                    <option value="HB" <?php selected('HB', $value['mealtype'], true); ?>><?php _e('Half Board', 'staylodgic'); ?></option>
                                    <option value="FB" <?php selected('FB', $value['mealtype'], true); ?>><?php _e('Full Board', 'staylodgic'); ?></option>
                                    <option value="AN" <?php selected('AN', $value['mealtype'], true); ?>><?php _e('All-Inclusive', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Type</span>
                                <select data-width="150" id="<?php echo esc_attr($args['label_for']); ?>_choice_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][choice]">
                                    <option value="included" <?php selected('included', $value['choice'], true); ?>><?php _e('Included in rate', 'staylodgic'); ?></option>
                                    <option value="optional" <?php selected('optional', $value['choice'], true); ?>><?php _e('Optional', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Price</span>
                                <input type="number" class="mealplan-style-setter" id="<?php echo esc_attr($args['label_for']); ?>_price_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($value['price']); ?>">
                            </span>
                            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <button id="addmealplan-repeatable" class="button button-secondary"><?php _e('Add new', 'staylodgic'); ?></button>
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
        $option_name = $args['label_for'];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

    ?>
        <div class="repeatable-tax-template" style="display: none;">
            <div class="repeatable">
                <span class="fa-solid fa-sort"></span>
                <span class="input-label-outer"><span class="input-label-inner">Name</span>
                    <input disabled type="text" placeholder="Label" id="<?php echo esc_attr($args['label_for']); ?>_label" name="label" value="">
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Value</span>
                    <input disabled type="number" placeholder="Value" id="<?php echo esc_attr($args['label_for']); ?>_number" name="number" value="">
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Type</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_type" name="type">
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Frequency</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_duration" name="duration">
                        <option value="inrate"><?php _e('Add to rate', 'staylodgic'); ?></option>
                        <option value="perperson"><?php _e('Per person', 'staylodgic'); ?></option>
                        <option value="perday"><?php _e('Per day', 'staylodgic'); ?></option>
                        <option value="perpersonperday"><?php _e('Per person per day', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            </div>
        </div>
        <div class="repeatable-tax-container-wrap">
            <div id="repeatable-tax-container">
                <?php

                $count = 0;
                if (is_array($array)) {
                    foreach ($array as $key => $value) {
                        $count++;
                        if (isset($value['label'])) {
                ?>
                            <div class="repeatable">
                                <span class="fa-solid fa-sort"></span>
                                <span class="input-label-outer"><span class="input-label-inner">Name</span>
                                    <input type="text" id="<?php echo esc_attr($args['label_for']); ?>_label_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($value['label']); ?>">
                                </span>
                                <span class="input-label-outer"><span class="input-label-inner">Value</span>
                                    <input type="number" id="<?php echo esc_attr($args['label_for']); ?>_number_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][number]" value="<?php echo esc_attr($value['number']); ?>">
                                </span>
                                <span class="input-label-outer"><span class="input-label-inner">Type</span>
                                    <select id="<?php echo esc_attr($args['label_for']); ?>_type_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][type]">
                                        <option value="fixed" <?php selected('fixed', $value['type'], true); ?>><?php _e('Fixed', 'staylodgic'); ?></option>
                                        <option value="percentage" <?php selected('percentage', $value['type'], true); ?>><?php _e('Percentage', 'staylodgic'); ?></option>
                                    </select>
                                </span>
                                <span class="input-label-outer"><span class="input-label-inner">Frequency</span>
                                    <select id="<?php echo esc_attr($args['label_for']); ?>_duration_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][duration]">
                                        <option value="inrate" <?php selected('inrate', $value['duration'], true); ?>><?php _e('Add to rate', 'staylodgic'); ?></option>
                                        <option value="perperson" <?php selected('perperson', $value['duration'], true); ?>><?php _e('Per person', 'staylodgic'); ?></option>
                                        <option value="perday" <?php selected('perday', $value['duration'], true); ?>><?php _e('Per Day', 'staylodgic'); ?></option>
                                        <option value="perpersonperday" <?php selected('perpersonperday', $value['duration'], true); ?>><?php _e('Per person per day', 'staylodgic'); ?></option>
                                    </select>
                                </span>
                                <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
                            </div>
                <?php
                        }
                    }
                }
                ?>
            </div>
            <button id="addtax-repeatable" class="button button-secondary"><?php _e('Add new', 'staylodgic'); ?></button>
        </div>
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
        $option_name = $args['label_for'];
        $array       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';

        // $setsOfThree = array();
        // if (isset($array) && is_array($array)) {
        //     $setsOfThree = array_chunk($array, 4);
        // }
        // error_log(print_r($array, 1));

    ?>
        <div class="repeatable-activitytax-template" style="display: none;">
            <div class="repeatable">
                <span class="fa-solid fa-sort"></span>
                <span class="input-label-outer"><span class="input-label-inner">Name</span>
                    <input disabled type="text" placeholder="Label" id="<?php echo esc_attr($args['label_for']); ?>_label" name="label" value="">
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Value</span>
                    <input disabled type="number" placeholder="Value" id="<?php echo esc_attr($args['label_for']); ?>_number" name="number" value="">
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Type</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_type" name="type">
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </span>
                <span class="input-label-outer"><span class="input-label-inner">Frequency</span>
                    <select disabled id="<?php echo esc_attr($args['label_for']); ?>_duration" name="duration">
                        <option value="inrate"><?php _e('Add to rate', 'staylodgic'); ?></option>
                        <option value="perperson"><?php _e('Per person', 'staylodgic'); ?></option>
                    </select>
                </span>
                <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            </div>
        </div>
        <div id="repeatable-activitytax-container">
            <?php

            $count = 0;
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $count++;
                    if (isset($value['label'])) {
            ?>
                        <div class="repeatable">
                            <span class="fa-solid fa-sort"></span>
                            <span class="input-label-outer"><span class="input-label-inner">Name</span>
                                <input type="text" id="<?php echo esc_attr($args['label_for']); ?>_label_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($value['label']); ?>">
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Value</span>
                                <input type="number" id="<?php echo esc_attr($args['label_for']); ?>_number_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][number]" value="<?php echo esc_attr($value['number']); ?>">
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Type</span>
                                <select id="<?php echo esc_attr($args['label_for']); ?>_type_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][type]">
                                    <option value="fixed" <?php selected('fixed', $value['type'], true); ?>><?php _e('Fixed', 'staylodgic'); ?></option>
                                    <option value="percentage" <?php selected('percentage', $value['type'], true); ?>><?php _e('Percentage', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="input-label-outer"><span class="input-label-inner">Frequency</span>
                                <select id="<?php echo esc_attr($args['label_for']); ?>_duration_<?php echo esc_attr($count); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo esc_attr($key); ?>][duration]">
                                    <option value="inrate" <?php selected('inrate', $value['duration'], true); ?>><?php _e('Add to rate', 'staylodgic'); ?></option>
                                    <option value="perperson" <?php selected('perperson', $value['duration'], true); ?>><?php _e('Per person', 'staylodgic'); ?></option>
                                </select>
                            </span>
                            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
                        </div>
            <?php
                    }
                }
            }
            ?>
        </div>
        <button id="addtax-activity-repeatable" class="button button-secondary"><?php _e('Add new', 'staylodgic'); ?></button>
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
        <span class="discount-input-outer">
            <span class="discount-display-label">Label</span>
            <input type="text" id="<?php echo esc_attr($args['label_for']); ?>_label" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][label]" value="<?php echo esc_attr($values['label']); ?>" placeholder="Label for discount">
        </span>
        <span class="discount-input-outer">
            <span class="discount-display-label">Days</span>
            <input type="number" id="<?php echo esc_attr($args['label_for']); ?>_days" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][days]" value="<?php echo esc_attr($values['days']); ?>" placeholder="Number of days">
        </span>
        <span class="discount-input-outer">
            <span class="discount-display-label">Percent</span>
            <input type="number" id="<?php echo esc_attr($args['label_for']); ?>_percent" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>][percent]" value="<?php echo esc_attr($values['percent']); ?>" placeholder="Discount percent">
        </span>
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
    ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>">
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
    public function render_number_field($args)
    {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
    ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>">
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        $rows        = $this->settings[$option_name]['rows'] ?? '4';
        $cols        = $this->settings[$option_name]['cols'] ?? '50';
    ?>
        <textarea type="text" id="<?php echo esc_attr($args['label_for']); ?>" rows="<?php echo esc_attr(absint($rows)); ?>" cols="<?php echo esc_attr(absint($cols)); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>]"><?php echo esc_attr($value); ?></textarea>
        <?php if ($description) { ?>
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
    ?>
        <label class="staylodgic-checkbox-container">
            <input type="checkbox" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>]" <?php checked($value, 1, true); ?>>
            <span class="checkmark"></span>
        </label>
        <?php if ($description) { ?>
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        $choices     = $this->settings[$option_name]['choices'] ?? [];
        $inputwidth  = $this->settings[$option_name]['inputwidth'] ?? '';
    ?>
        <select class="single-options-select" data-width="<?php echo esc_attr($inputwidth); ?>" id="<?php echo esc_attr($args['label_for']); ?>" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            foreach ($choices as $choice_v => $label) { ?>
                <option value="<?php echo esc_attr($choice_v); ?>" <?php selected($choice_v, $value, true); ?>><?php echo esc_html($label); ?></option>
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
    'parent_page'     => 'staylodgic-settings',
    'title'           => 'Hotel Settings',
    'option_name'     => 'staylodgic_settings',
    'slug'            => 'slgc-settings-panel',
    'user_capability' => 'edit_posts',
    'tabs'            => [
        'property'       => '<i class="fa fa-building"></i> <span class="options-menu-link-text">' . esc_html__('Property', 'staylodgic') . '</span>',
        'activity-property' => '<i class="fa fa-suitcase"></i> <span class="options-menu-link-text">' . esc_html__('Activity Property', 'staylodgic') . '</span>',
        'currency'      => '<i class="fa fa-dollar"></i> <span class="options-menu-link-text">' . esc_html__('Currency', 'staylodgic') . '</span>',
        'general'       => '<i class="fa fa-cogs"></i> <span class="options-menu-link-text">' . esc_html__('General', 'staylodgic') . '</span>',
        'logo'         => '<i class="fa-solid fa-panorama"></i> <span class="options-menu-link-text">' . esc_html__('Logo', 'staylodgic') . '</span>',
        'pages'         => '<i class="fa fa-file-text"></i> <span class="options-menu-link-text">' . esc_html__('Pages', 'staylodgic') . '</span>',
        'discounts'     => '<i class="fa fa-percent"></i> <span class="options-menu-link-text">' . esc_html__('Discounts', 'staylodgic') . '</span>',
        'mealplan'      => '<i class="fa fa-cutlery"></i> <span class="options-menu-link-text">' . esc_html__('Meal Plan', 'staylodgic') . '</span>',
        'perperson'     => '<i class="fa fa-user"></i> <span class="options-menu-link-text">' . esc_html__('Per person price', 'staylodgic') . '</span>',
        'tax'           => '<i class="fa fa-calculator"></i> <span class="options-menu-link-text">' . esc_html__('Room Tax', 'staylodgic') . '</span>',
        'activity-tax'  => '<i class="fa fa-calculator"></i> <span class="options-menu-link-text">' . esc_html__('Activity Tax', 'staylodgic') . '</span>',
    ],
];

$currencies       = \Staylodgic\Common::get_staylodgic_currencies();
$currency_symbols = \Staylodgic\Common::get_staylodgic_currency_symbols();
$curr_choices     = array();

// Generate the select list
foreach ($currencies as $currencyCode => $currencyName) {
    $currency_symbol               = $currency_symbols[$currencyCode];
    $curr_choices[$currencyCode] = $currencyName . ' ( ' . $currency_symbol . ' )';
}

$country_options = staylodgic_country_list('select-array', '');

$panel_settings = [
    'property_logo'         => [
        'label'       => esc_html__('Upload Logo', 'staylodgic'),
        'type'        => 'media_upload',
        'description' => 'Upload property logo.',
        'tab'         => 'property', // You can change the tab as per your requirement
    ],
    'property_logo_width'   => [
        'label'       => esc_html__('On-paper logo width in pixels', 'staylodgic'),
        'type'        => 'number',
        'default'        => '100',
        'description' => 'Width of registration and invoice logo in pixels.',
        'tab'         => 'property',
    ],
    'property_name'             => [
        'label'       => esc_html__('Name', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'property',
    ],
    'property_phone'             => [
        'label'       => esc_html__('Phone', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'property',
    ],
    'property_address'   => [
        'label'       => esc_html__('Address', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'property',
    ],
    'property_longitude'   => [
        'label'       => esc_html__('Property Longitude', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Longitude',
        'tab'         => 'property',
    ],
    'property_latitude'   => [
        'label'       => esc_html__('Property Latitude', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Latitude',
        'tab'         => 'property',
    ],
    'property_country'    => [
        'label'       => esc_html__('Country', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Country.',
        'choices'     => $country_options,
        'tab'         => 'property',
    ],
    'property_header'   => [
        'label'       => esc_html__('Invoice header', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Header text for invoice',
        'tab'         => 'property',
    ],
    'property_footer'   => [
        'label'       => esc_html__('Invoice footer', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Footer text for invoice',
        'tab'         => 'property',
    ],
    'property_emailfooter'   => [
        'label'       => esc_html__('Email footer', 'staylodgic'),
        'type'        => 'textarea',
        'description' => 'Footer text for email',
        'tab'         => 'property',
    ],

    'activity_property_logo'         => [
        'label'       => esc_html__('Upload Logo', 'staylodgic'),
        'type'        => 'media_upload',
        'tab'         => 'activity-property', // You can change the tab as per your requirement
    ],
    'activity_property_logo_width'   => [
        'label'       => esc_html__('On-paper logo width in pixels', 'staylodgic'),
        'type'        => 'number',
        'default'        => '100',
        'description' => 'Width of invoice logo in pixels.',
        'tab'         => 'activity-property',
    ],
    'activity_property_name'             => [
        'label'       => esc_html__('Name', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'activity-property',
    ],
    'activity_property_phone'             => [
        'label'       => esc_html__('Phone', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'activity-property',
    ],
    'activity_property_address'   => [
        'label'       => esc_html__('Address', 'staylodgic'),
        'type'        => 'text',
        'tab'         => 'activity-property',
    ],
    'activity_property_longitude'   => [
        'label'       => esc_html__('Property Longitude', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Longitude',
        'tab'         => 'activity-property',
    ],
    'activity_property_latitude'   => [
        'label'       => esc_html__('Property Latitude', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Latitude',
        'tab'         => 'activity-property',
    ],
    'activity_property_country'    => [
        'label'       => esc_html__('Country', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Country.',
        'choices'     => $country_options,
        'tab'         => 'activity-property',
    ],
    'activity_property_header'   => [
        'label'       => esc_html__('Invoice header', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Header text for invoice',
        'tab'         => 'activity-property',
    ],
    'activity_property_footer'   => [
        'label'       => esc_html__('Invoice footer', 'staylodgic'),
        'type'        => 'text',
        'description' => 'Footer text for invoice',
        'tab'         => 'activity-property',
    ],
    'activity_property_emailfooter'   => [
        'label'       => esc_html__('Email footer', 'staylodgic'),
        'type'        => 'textarea',
        'description' => 'Footer text for email',
        'tab'         => 'activity-property',
    ],
    'main_logo'         => [
        'label'       => esc_html__('Header logo', 'staylodgic'),
        'type'        => 'media_upload',
        'description' => 'Upload header logo.',
        'tab'         => 'logo', // You can change the tab as per your requirement
    ],
    'main_logo_height'   => [
        'label'       => esc_html__('Header logo height in pixels', 'staylodgic'),
        'type'        => 'number',
        'default'        => '100',
        'description' => 'Height of header logo in pixels.',
        'tab'         => 'logo',
    ],
    'responsive_logo'         => [
        'label'       => esc_html__('Responsive logo', 'staylodgic'),
        'type'        => 'media_upload',
        'description' => 'Upload header logo.',
        'tab'         => 'logo', // You can change the tab as per your requirement
    ],
    'responsive_logo_height'   => [
        'label'       => esc_html__('Responsive logo height in pixels', 'staylodgic'),
        'type'        => 'number',
        'default'        => '50',
        'description' => 'Height of header logo in pixels.',
        'tab'         => 'logo',
    ],
    'booking_menu_one'   => [
        'label'       => esc_html__('Menu One ( Homepage )', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Homepage for booking.',
        'choices'     => staylodgic_get_booking_homepages_for_select(),
        'tab'         => 'pages',
    ],
    'booking_menu_two'   => [
        'label'       => esc_html__('Menu Two', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Page as first menu item.',
        'choices'     => staylodgic_get_booking_pages_for_select(),
        'tab'         => 'pages',
    ],
    'booking_menu_three'   => [
        'label'       => esc_html__('Menu Three', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Page as second menu item.',
        'choices'     => staylodgic_get_booking_pages_for_select(),
        'tab'         => 'pages',
    ],
    'booking_menu_four'   => [
        'label'       => esc_html__('Menu Four', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Page as third menu item.',
        'choices'     => staylodgic_get_booking_pages_for_select(),
        'tab'         => 'pages',
    ],
    'discount_lastminute'             => [
        'label'       => esc_html__('Last minute discount', 'staylodgic'),
        'type'        => 'promotion_discount',
        'description' => 'Maximum days ahead for discount. More than the number of days from booking day the discount will not be applied.',
        'tab'         => 'discounts',
    ],
    'discount_earlybooking'             => [
        'label'       => esc_html__('Early booking discount', 'staylodgic'),
        'type'        => 'promotion_discount',
        'description' => 'How many days ahead to apply discount. Less than the number of days from booking day the discount will not be applied.',
        'tab'         => 'discounts',
    ],
    'discount_longstay'             => [
        'label'       => esc_html__('Long stay discount', 'staylodgic'),
        'type'        => 'promotion_discount',
        'description' => 'Lenght of days to stay to apply discount.',
        'tab'         => 'discounts',
    ],
    'enable_taxes'         => [
        'label'       => esc_html__('Enable Room Taxes', 'staylodgic'),
        'type'        => 'checkbox',
        'description' => '',
        'tab'         => 'tax',
    ],
    'enable_activitytaxes'         => [
        'label'       => esc_html__('Enable Activties Taxes', 'staylodgic'),
        'type'        => 'checkbox',
        'description' => '',
        'tab'         => 'activity-tax',
    ],
    'new_bookingstatus'    => [
        'label'       => esc_html__('Choose status for new bookings', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Choose status for new bookings.',
        'choices'     => staylodgic_get_new_booking_statuses(),
        'tab'         => 'general',
    ],
    'new_bookingsubstatus' => [
        'label'       => esc_html__('Choose sub status for new bookings', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Choose sub status for new bookings.',
        'choices'     => staylodgic_get_booking_substatuses(),
        'tab'         => 'general',
    ],
    'timezone'        => [
        'label'       => esc_html__('Select Time Zone', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'description' => 'Select your time zone relative to GMT.',
        'choices'     => staylodgic_get_GmtTimezoneChoices(),
        'tab'         => 'general',
    ],
    'sync_interval'   => [
        'label'       => esc_html__('iCal sync interval', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '100',
        'description' => 'iCal feeds sync interval between another calendar.',
        'choices'     => [
            '60'  => esc_html__('60', 'staylodgic'),
            '30'  => esc_html__('30', 'staylodgic'),
            '15'  => esc_html__('15', 'staylodgic'),
            '10'  => esc_html__('10', 'staylodgic'),
            '5'  => esc_html__('5', 'staylodgic'),
            '1'  => esc_html__('1', 'staylodgic'),
        ],
        'tab'         => 'general',
    ],
    'taxes'                => [
        'label'       => esc_html__('Room taxes', 'staylodgic'),
        'type'        => 'repeatable_tax',
        'description' => 'Room taxes',
        'tab'         => 'tax',
    ],
    'activity_taxes'                => [
        'label'       => esc_html__('Activity taxes', 'staylodgic'),
        'type'        => 'activity_repeatable_tax',
        'description' => 'Activity taxes',
        'tab'         => 'activity-tax',
    ],
    'childfreestay'        => [
        'label'       => esc_html__('Children under the age can stay for free', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '100',
        'description' => 'Under which age should be free stay',
        'choices'     => [
            '0'  => esc_html__('0', 'staylodgic'),
            '1'  => esc_html__('1', 'staylodgic'),
            '2'  => esc_html__('2', 'staylodgic'),
            '3'  => esc_html__('3', 'staylodgic'),
            '4'  => esc_html__('4', 'staylodgic'),
            '5'  => esc_html__('5', 'staylodgic'),
            '6'  => esc_html__('6', 'staylodgic'),
            '7'  => esc_html__('7', 'staylodgic'),
            '8'  => esc_html__('8', 'staylodgic'),
            '9'  => esc_html__('9', 'staylodgic'),
            '10' => esc_html__('10', 'staylodgic'),
            '11' => esc_html__('11', 'staylodgic'),
            '12' => esc_html__('12', 'staylodgic'),
            '13' => esc_html__('13', 'staylodgic'),
            '14' => esc_html__('14', 'staylodgic'),
            '15' => esc_html__('15', 'staylodgic'),
            '16' => esc_html__('16', 'staylodgic'),
            '17' => esc_html__('17', 'staylodgic'),
        ],
        'tab'         => 'perperson',
    ],
    'perpersonpricing'     => [
        'label'       => esc_html__('Per person price', 'staylodgic'),
        'type'        => 'repeatable_perperson',
        'description' => 'Price per person.',
        'tab'         => 'perperson',
    ],
    'mealplan'             => [
        'label'       => esc_html__('Meal Plan', 'staylodgic'),
        'type'        => 'repeatable_mealplan',
        'description' => 'Meal plans',
        'tab'         => 'mealplan',
    ],
    'currency'             => [
        'label'       => esc_html__('Currency', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'default'        => 'USD',
        'description' => 'Choose currency',
        'choices'     => $curr_choices,
        'tab'         => 'currency',
    ],
    'currency_position'    => [
        'label'       => esc_html__('Currency position', 'staylodgic'),
        'type'        => 'select',
        'inputwidth'  => '250',
        'default'        => 'left_space',
        'choices'     => [
            'left_space'  => esc_html__('Left with space', 'staylodgic'),
            'right_space' => esc_html__('Right with space', 'staylodgic'),
            'left'        => esc_html__('Left', 'staylodgic'),
            'right'       => esc_html__('Right', 'staylodgic'),
        ],
        'tab'         => 'currency',
    ],
    'thousand_seperator'   => [
        'label'       => esc_html__('Thousand seperator', 'staylodgic'),
        'type'        => 'text',
        'default'        => ',',
        'tab'         => 'currency',
    ],
    'decimal_seperator'    => [
        'label'       => esc_html__('Decimal seperator', 'staylodgic'),
        'type'        => 'text',
        'default'        => '.',
        'tab'         => 'currency',
    ],
    'number_of_decimals'   => [
        'label'       => esc_html__('Number of Decimals', 'staylodgic'),
        'type'        => 'number',
        'default'        => '2',
        'tab'         => 'currency',
    ],
];

new \Staylodgic\OptionsPanel($panel_args, $panel_settings);
