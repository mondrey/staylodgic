<?php

namespace Staylodgic;

class ExportBackup
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'custom_export_menu']);
        add_action('admin_init', [$this, 'handle_custom_export']);
        add_action('admin_head', [$this, 'export_add_js']);
    }
    
    /**
     * Method custom_export_menu
     *
     * @return void
     */
    public function custom_export_menu()
    {
        if (current_user_can('editor') || current_user_can('administrator')) {
            add_menu_page(
                __('Custom Export', 'staylodgic'),
                __('Download XML Records', 'staylodgic'),
                'edit_posts',
                'custom-export',
                [$this, 'custom_export_page'],
                'dashicons-download',
                40
            );
        }
    }
    
    /**
     * Method custom_export_page
     *
     * @return void
     */
    public function custom_export_page()
    {
?>
        <div class="wrap">
            <h1><?php _e('Download XML Records', 'staylodgic'); ?></h1>
            <form method="get" id="export-filters">
                <fieldset>
                    <legend class="screen-reader-text"><?php _e('Content to export', 'staylodgic'); ?></legend>
                    <input type="hidden" name="download" value="true" />
                    <p><label><input type="radio" name="content" value="all" checked="checked" aria-describedby="all-content-desc" /> <?php _e('All content', 'staylodgic'); ?></label></p>
                    <p class="description" id="all-content-desc"><?php _e('This will contain all of your booking posts.', 'staylodgic'); ?></p>

                    <?php
                    foreach (
                        get_post_types(
                            array(
                                '_builtin'   => false,
                                'can_export' => true,
                            ),
                            'objects'
                        ) as $post_type
                    ) :
                    ?>
                        <p><label><input type="radio" name="content" value="<?php echo esc_attr($post_type->name); ?>" /> <?php echo esc_html($post_type->label); ?></label></p>
                    <?php endforeach; ?>

                    <p><label><input type="radio" name="content" value="attachment" /> <?php _e('Media'); ?></label></p>
                    <ul id="attachment-filters" class="export-filters">
                        <li>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Date range:', 'staylodgic'); ?></legend>
                                <label for="attachment-start-date" class="label-responsive"><?php _e('Start date:', 'staylodgic'); ?></label>
                                <select name="attachment_start_date" id="attachment-start-date">
                                    <option value="0"><?php _e('&mdash; Select &mdash;', 'staylodgic'); ?></option>
                                    <?php $this->export_date_options('attachment'); ?>
                                </select>
                                <label for="attachment-end-date" class="label-responsive"><?php _e('End date:', 'staylodgic'); ?></label>
                                <select name="attachment_end_date" id="attachment-end-date">
                                    <option value="0"><?php _e('&mdash; Select &mdash;'); ?></option>
                                    <?php $this->export_date_options('attachment'); ?>
                                </select>
                            </fieldset>
                        </li>
                    </ul>
                </fieldset>
                <?php submit_button(__('Download Export File', 'staylodgic')); ?>
            </form>
        </div>
    <?php
    }
    
    /**
     * Method handle_custom_export
     *
     * @return void
     */
    public function handle_custom_export()
    {
        if (isset($_GET['download'])) {
            // Load the export function
            require_once ABSPATH . 'wp-admin/includes/export.php';

            $args = array();

            if (!isset($_GET['content']) || 'all' === $_GET['content']) {
                $args['content'] = 'all';
            } elseif ('attachment' === $_GET['content']) {
                $args['content'] = 'attachment';

                if ($_GET['attachment_start_date'] || $_GET['attachment_end_date']) {
                    $args['start_date'] = $_GET['attachment_start_date'];
                    $args['end_date'] = $_GET['attachment_end_date'];
                }
            } else {
                $args['content'] = $_GET['content'];
            }

            /**
             * Filters the export args.
             *
             * @since 3.5.0
             *
             * @param array $args The arguments to send to the exporter.
             */
            $args = apply_filters('export_args', $args);

            // Call the export function
            \export_wp($args);
            die();
        }
    }
    
    /**
     * Method export_add_js
     *
     * @return void
     */
    public function export_add_js()
    {
    ?>
        <script type="text/javascript">
            jQuery(function($) {
                var form = $('#export-filters'),
                    filters = form.find('.export-filters');
                filters.hide();
                form.find('input:radio').on('change', function() {
                    filters.slideUp('fast');
                    switch ($(this).val()) {
                        case 'attachment':
                            $('#attachment-filters').slideDown();
                            break;
                    }
                });
            });
        </script>
<?php
    }
    
    /**
     * Method export_date_options
     *
     * @param $post_type
     *
     * @return void
     */
    public function export_date_options($post_type = 'post')
    {
        global $wpdb, $wp_locale;

        $months = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
                FROM $wpdb->posts
                WHERE post_type = %s AND post_status != 'auto-draft'
                ORDER BY post_date DESC",
                $post_type
            )
        );

        $month_count = count($months);
        if (!$month_count || (1 === $month_count && 0 === (int)$months[0]->month)) {
            return;
        }

        foreach ($months as $date) {
            if (0 === (int)$date->year) {
                continue;
            }

            $month = zeroise($date->month, 2);

            printf(
                '<option value="%1$s">%2$s</option>',
                esc_attr($date->year . '-' . $month),
                $wp_locale->get_month($month) . ' ' . $date->year
            );
        }
    }
}

// Initialize the plugin
new \Staylodgic\ExportBackup();
?>