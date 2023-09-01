<?php
class AtollMatrixOptionsPanel
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
        $this->parent_page       = $this->args['parent_page'] ?? esc_html__('atoll-matrix', 'text_domain');
        $this->title             = $this->args['title'] ?? esc_html__('Settings', 'text_domain');
        $this->slug              = $this->args['slug'] ?? sanitize_key($this->title);
        $this->option_name       = $this->args['option_name'] ?? sanitize_key($this->title);
        $this->option_group_name = $this->option_name . '_group';
        $this->user_capability   = $args['user_capability'] ?? 'manage_options';

        add_action('admin_menu', [$this, 'register_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
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
            [$this, 'render_options_page']
        );
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
            false,
            false,
            $this->option_name
        );

        foreach ($this->settings as $key => $args) {
            $type     = $args['type'] ?? 'text';
            $callback = "render_{$type}_field";
            if (method_exists($this, $callback)) {
                $tr_class = '';
                if (array_key_exists('tab', $args)) {
                    $tr_class .= 'atollmatrix-tab-item atollmatrix-tab-item--' . sanitize_html_class($args['tab']);
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
    public function sanitize_fields($value)
    {
        $value     = (array) $value;
        $new_value = [];
        foreach ($this->settings as $key => $args) {
            $field_type       = $args['type'];
            $new_option_value = $value[$key] ?? '';
            if ($new_option_value) {
                $sanitize_callback = $args['sanitize_callback'] ?? $this->get_sanitize_callback_by_type($field_type);
                $new_value[$key]   = call_user_func($sanitize_callback, $new_option_value, $args);
            } elseif ('checkbox' === $field_type) {
                $new_value[$key] = 0;
            }
        }
        return $new_value;
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
            case 'textarea':
                return 'wp_kses_post';
                break;
            case 'checkbox':
                return [$this, 'sanitize_checkbox_field'];
                break;
            case 'repeatable_tax':
                return [$this, 'sanitize_tax_field'];
                break;
            case 'repeatable_perperson':
                return [$this, 'sanitize_tax_field'];
                break;
            case 'repeatable_mealplan':
                return [$this, 'sanitize_tax_field'];
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
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                $this->option_name . '_mesages',
                $this->option_name . '_message',
                esc_html__('Settings Saved', 'text_domain'),
                'updated'
            );
        }

        settings_errors($this->option_name . '_mesages');

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php $this->render_tabs();?>
            <form action="options.php" method="post" class="atollmatrix-options-form">
                <?php
                settings_fields($this->option_group_name);
                do_settings_sections($this->option_name);
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
}

    /**
     * Renders options page tabs.
     */
    protected function render_tabs()
    {
        if (empty($this->args['tabs'])) {
            return;
        }

        $tabs = $this->args['tabs'];
        ?>
        <h2 class="nav-tab-wrapper atollmatrix-tabs"><?php
$first_tab = true;
        foreach ($tabs as $id => $label) {?>
                <a href="#" data-tab="<?php echo esc_attr($id); ?>" class="nav-tab<?php echo ($first_tab) ? ' nav-tab-active' : ''; ?>"><?php echo ucfirst($label); ?></a>
                <?php
$first_tab = false;
        }
        ?></h2>
        <?php
}

    /**
     * Returns an option value.
     */
    protected function get_option_value($option_name)
    {
        $option = get_option($this->option_name);
        if (!array_key_exists($option_name, $option)) {
            return array_key_exists('default', $this->settings[$option_name]) ? $this->settings[$option_name]['default'] : '';
        }
        return $option[$option_name];
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
        error_log(print_r($array, 1));

        ?>
<div class="repeatable-perperson-template" style="display: none;">
<div class="repeatable">
            <select disabled
            id="<?php echo esc_attr($args['label_for']); ?>_people"
            name="people"
            >
            <option value="1">1</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            </select>
            <input disabled
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_number"
                name="number"
                value="">
            <select disabled
            id="<?php echo esc_attr($args['label_for']); ?>_type"
            name="type"
            >
            <option value="fixed">Fixed</option>
            <option value="percentage">Percentage</option>
            </select>
            <select disabled
            id="<?php echo esc_attr($args['label_for']); ?>_total"
            name="total"
            >
            <option value="increase">Increase</option>
            <option value="decrease">Decrease</option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
</div>
<div id="repeatable-perperson-container">
<?php

        $count = 0;
        if ( is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if ( isset( $value['people'] ) ) {
                ?>
            <div class="repeatable">
            <select
            id="<?php echo esc_attr($args['label_for']); ?>_people_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][people]"
            >
            <option value="1" <?php selected('1', $value['people'], true);?>>1</option>
            <option value="3" <?php selected('3', $value['people'], true);?>>3</option>
            <option value="4" <?php selected('4', $value['people'], true);?>>4</option>
            <option value="5" <?php selected('5', $value['people'], true);?>>5</option>
            <option value="6" <?php selected('6', $value['people'], true);?>>6</option>
            <option value="7" <?php selected('7', $value['people'], true);?>>7</option>
            <option value="8" <?php selected('8', $value['people'], true);?>>8</option>
            <option value="9" <?php selected('9', $value['people'], true);?>>9</option>
            </select>
            <input
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_number_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][number]"
                value="<?php echo esc_attr($value['number']); ?>">
            <select
            id="<?php echo esc_attr($args['label_for']); ?>_type_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][type]"
            >
            <option value="fixed" <?php selected('fixed', $value['type'], true);?>>Fixed</option>
            <option value="percentage" <?php selected('percentage', $value['type'], true);?>>Percentage</option>
            </select>
            <select
            id="<?php echo esc_attr($args['label_for']); ?>_total_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][total]"
            >
            <option value="increase" <?php selected('increase', $value['total'], true);?>>Increase</option>
            <option value="decrease" <?php selected('decrease', $value['total'], true);?>>Decrease</option>
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
        <button id="addperperson-repeatable">Add New Section</button>
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
        error_log('----- mealplan array -----');
        error_log(print_r($array, 1));

        ?>
<div class="repeatable-mealplan-template" style="display: none;">
<div class="repeatable">
        <select disabled
        id="<?php echo esc_attr($args['label_for']); ?>_mealtype"
        name="mealtype"
        >
        <option value="RO">Room Only</option>
        <option value="BB">Bed and Breakfast</option>
        <option value="HB">Half Board</option>
        <option value="FB">Full Board</option>
        <option value="AN">All-Inclusive</option>
        </select>
        <select disabled
        id="<?php echo esc_attr($args['label_for']); ?>_choice"
        name="choice"
        >
        <option value="included">Included in rate</option>
        <option value="optional">Optional</option>
        </select>
        <input disabled
            type="text"
            id="<?php echo esc_attr($args['label_for']); ?>_price"
            name="price"
            value="">
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
        <br/>
        </div>
</div>
<div id="repeatable-mealplan-container">
<?php
        $count = 0;
        if ( is_array($array)) {
            foreach ($array as $key => $value) {
                $count++;
                if ( isset( $value['mealtype'] ) ) {
                ?>
            <div class="repeatable">
                <select
                id="<?php echo esc_attr($args['label_for']); ?>_mealtype_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][mealtype]"
                >
                <option value="RO" <?php selected('RO', $value['mealtype'], true);?>>Room Only</option>
                <option value="BB" <?php selected('BB', $value['mealtype'], true);?>>Bed and Breakfast</option>
                <option value="HB" <?php selected('HB', $value['mealtype'], true);?>>Half Board</option>
                <option value="FB" <?php selected('FB', $value['mealtype'], true);?>>Full Board</option>
                <option value="AN" <?php selected('AN', $value['mealtype'], true);?>>All-Inclusive</option>
                </select>
            <select
            id="<?php echo esc_attr($args['label_for']); ?>_choice_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][choice]"
            >
            <option value="included" <?php selected('included', $value['choice'], true);?>>Included in rate</option>
            <option value="optional" <?php selected('optional', $value['choice'], true);?>>Optional</option>
            </select>
            <input
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_price_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][price]"
                value="<?php echo esc_attr($value['price']); ?>">

            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
        <?php
                }
        }
    }
        ?>
    </div>
    <button id="addmealplan-repeatable">Add New Section</button>
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
        error_log(print_r($array, 1));

        ?>
<div class="repeatable-tax-template" style="display: none;">
<div class="repeatable">
<span class="dashicons dashicons-sort drag-handle"></span>
            <input disabled
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_label"
                name="label"
                value="">
            <input disabled
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_number"
                name="number"
                value="">

            <select disabled
            id="<?php echo esc_attr($args['label_for']); ?>_type"
            name="type"
            >
            <option value="fixed">Fixed</option>
            <option value="percentage">Percentage</option>
            </select>
            <select disabled
            id="<?php echo esc_attr($args['label_for']); ?>_duration"
            name="duration"
            >
            <option value="inrate">Add to rate</option>
            <option value="perperson">Per person</option>
            <option value="perday">Per day</option>
            <option value="perpersonperday">Per person per day</option>
            </select>
            <span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
            <br/>
            </div>
</div>
<div id="repeatable-tax-container">
<?php

    $count = 0;
    if ( is_array($array)) {
        foreach ($array as $key => $value) {
            $count++;
            if ( isset( $value['label'] ) ) {
                ?>
            <div class="repeatable">
            <span class="dashicons dashicons-sort drag-handle"></span>
            <input
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_label_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][label]"
                value="<?php echo esc_attr($value['label']); ?>">

            <input
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>_number_<?php echo $count; ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][number]"
                value="<?php echo esc_attr($value['number']); ?>">

            <select
            id="<?php echo esc_attr($args['label_for']); ?>_type_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][type]"
            >
            <option value="fixed" <?php selected('fixed', $value['type'], true);?>>Fixed</option>
            <option value="percentage" <?php selected('percentage', $value['type'], true);?>>Percentage</option>
            </select>
            <select
            id="<?php echo esc_attr($args['label_for']); ?>_duration_<?php echo $count; ?>"
            name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>][<?php echo $key; ?>][duration]"
            >
            <option value="inrate" <?php selected('inrate', $value['duration'], true);?>>Add to rate</option>
            <option value="perperson" <?php selected('perperson', $value['duration'], true);?>>Per person</option>
            <option value="perday" <?php selected('perday', $value['duration'], true);?>>Per Day</option>
            <option value="perpersonperday" <?php selected('perpersonperday', $value['duration'], true);?>>Per person per day</option>
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
        <button id="addtax-repeatable">Add New Section</button>
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
    public function render_text_field($args)
    {
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        ?>
            <input
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>]"
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        $rows        = $this->settings[$option_name]['rows'] ?? '4';
        $cols        = $this->settings[$option_name]['cols'] ?? '50';
        ?>
            <textarea
                type="text"
                id="<?php echo esc_attr($args['label_for']); ?>"
                rows="<?php echo esc_attr(absint($rows)); ?>"
                cols="<?php echo esc_attr(absint($cols)); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>]"><?php echo esc_attr($value); ?></textarea>
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        ?>
            <input
                type="checkbox"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>]"
                <?php checked($value, 1, true);?>
            >
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
        $option_name = $args['label_for'];
        $value       = $this->get_option_value($option_name);
        $description = $this->settings[$option_name]['description'] ?? '';
        $choices     = $this->settings[$option_name]['choices'] ?? [];
        ?>
            <select
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo $this->option_name; ?>[<?php echo esc_attr($args['label_for']); ?>]"
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
        'tab-1'     => esc_html__('Tab 1', 'text_domain'),
        'tab-2'     => esc_html__('Tab 2', 'text_domain'),
        'currency'  => esc_html__('Currency', 'text_domain'),
        'mealplan'  => esc_html__('Meal Plan', 'text_domain'),
        'perperson' => esc_html__('Per person price', 'text_domain'),
        'tax'       => esc_html__('Tax', 'text_domain'),
    ],
];

$currencies       = \AtollMatrix\Common::get_atollmatrix_currencies();
$currency_symbols = \AtollMatrix\Common::get_atollmatrix_currency_symbols();
$curr_choices     = array();

// Generate the select list
foreach ($currencies as $currencyCode => $currencyName) {
    $currency_symbol             = $currency_symbols[$currencyCode];
    $curr_choices[$currencyCode] = $currencyName . ' ( ' . $currency_symbol . ' )';
}

$panel_settings = [
    // Tab 1
    'option_1'           => [
        'label'       => esc_html__('Checkbox Option', 'text_domain'),
        'type'        => 'checkbox',
        'description' => 'My checkbox field description.',
        'tab'         => 'tab-1',
    ],
    'option_2'           => [
        'label'       => esc_html__('Select Option', 'text_domain'),
        'type'        => 'select',
        'description' => 'My select field description.',
        'choices'     => [
            ''         => esc_html__('Select', 'text_domain'),
            'choice_1' => esc_html__('Choice 1', 'text_domain'),
            'choice_2' => esc_html__('Choice 2', 'text_domain'),
            'choice_3' => esc_html__('Choice 3', 'text_domain'),
        ],
        'tab'         => 'tab-1',
    ],
    // Tab 2
    'option_3'           => [
        'label'       => esc_html__('Text Option', 'text_domain'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'tab-2',
    ],
    'option_4'           => [
        'label'       => esc_html__('Textarea Option', 'text_domain'),
        'type'        => 'textarea',
        'description' => 'My textarea field description.',
        'tab'         => 'tab-2',
    ],
    'taxes'              => [
        'label'       => esc_html__('Taxes', 'text_domain'),
        'type'        => 'repeatable_tax',
        'description' => 'My textarea field description.',
        'tab'         => 'tax',
    ],
    'perpersonpricing'   => [
        'label'       => esc_html__('Per person price', 'text_domain'),
        'type'        => 'repeatable_perperson',
        'description' => 'My textarea field description.',
        'tab'         => 'perperson',
    ],
    'childfreestay'   => [
        'label'       => esc_html__('Children under the age can stay for free', 'text_domain'),
        'type'        => 'select',
        'description' => 'My select field description.',
        'choices'     => [
            '0' => esc_html__('0', 'text_domain'),
            '1' => esc_html__('1', 'text_domain'),
            '2' => esc_html__('2', 'text_domain'),
            '3' => esc_html__('3', 'text_domain'),
            '4' => esc_html__('4', 'text_domain'),
            '5' => esc_html__('5', 'text_domain'),
            '6' => esc_html__('6', 'text_domain'),
            '7' => esc_html__('7', 'text_domain'),
            '8' => esc_html__('8', 'text_domain'),
            '9' => esc_html__('9', 'text_domain'),
            '10' => esc_html__('10', 'text_domain'),
            '11' => esc_html__('11', 'text_domain'),
            '12' => esc_html__('12', 'text_domain'),
            '13' => esc_html__('13', 'text_domain'),
            '14' => esc_html__('14', 'text_domain'),
            '15' => esc_html__('15', 'text_domain'),
            '16' => esc_html__('16', 'text_domain'),
            '17' => esc_html__('17', 'text_domain'),
        ],
        'tab'         => 'perperson',
    ],
    'mealplan'           => [
        'label'       => esc_html__('Meal Plan', 'text_domain'),
        'type'        => 'repeatable_mealplan',
        'description' => 'My textarea field description.',
        'tab'         => 'mealplan',
    ],
    'currency'           => [
        'label'       => esc_html__('Currency', 'text_domain'),
        'type'        => 'select',
        'description' => 'My select field description.',
        'choices'     => $curr_choices,
        'tab'         => 'currency',
    ],
    'currency_position'  => [
        'label'       => esc_html__('Currency position', 'text_domain'),
        'type'        => 'select',
        'description' => 'My select field description.',
        'choices'     => [
            'left_space'  => esc_html__('Left with space', 'text_domain'),
            'right_space' => esc_html__('Right with space', 'text_domain'),
            'left'        => esc_html__('Left', 'text_domain'),
            'right'       => esc_html__('Right', 'text_domain'),
        ],
        'tab'         => 'currency',
    ],
    'thousand_seperator' => [
        'label'       => esc_html__('Thousand seperator', 'text_domain'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
    ],
    'decimal_seperator'  => [
        'label'       => esc_html__('Decimal seperator', 'text_domain'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
    ],
    'number_of_decimals' => [
        'label'       => esc_html__('Number of Decimals', 'text_domain'),
        'type'        => 'text',
        'description' => 'My field 1 description.',
        'tab'         => 'currency',
    ],
];

new AtollMatrixOptionsPanel($panel_args, $panel_settings);
