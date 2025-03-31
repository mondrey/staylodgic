<?php

namespace Staylodgic;

class Options_Panel {


	/**
	 * Options panel arguments.
	 */
	protected $args = array();

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
	protected $settings = array();

	/**
	 * Our class constructor.
	 */
	public function __construct( array $args, array $settings ) {
		$this->args              = $args;
		$this->settings          = $settings;
		$this->parent_page       = $this->args['parent_page'] ?? esc_html__( 'staylodgic', 'staylodgic' );
		$this->title             = $this->args['title'] ?? esc_html__( 'Settings', 'staylodgic' );
		$this->slug              = $this->args['slug'] ?? sanitize_key( $this->title );
		$this->option_name       = $this->args['option_name'] ?? sanitize_key( $this->title );
		$this->option_group_name = $this->option_name . '_group';
		$this->user_capability   = $args['user_capability'] ?? 'edit_posts';

		add_action( 'admin_menu', array( $this, 'export_settings' ) );
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Hook into admin_init to catch the form submission
		add_action( 'admin_init', array( $this, 'staylodgic_import_settings' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_uploader' ) );
	}

	/**
	 * Method Function to create custom pages
	 *
	 * @param $title $title [explicite description]
	 * @param $template $template [explicite description]
	 * @param $content $content [explicite description]
	 * @param $slug $slug [explicite description]
	 *
	 * @return void
	 */
	public function create_custom_page( $title, $template, $content, $slug ) {
		$existing_page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing_page ) {
			return $existing_page->ID; // Return existing page ID if the page exists
		}

		$page_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => $slug, // Set the slug for the page
			'meta_input'   => array(
				'_wp_page_template' => $template,
			),
		);

		$page_id = wp_insert_post( $page_data );

		return $page_id;
	}

	/**
	 * Method Function to create initial pages
	 *
	 * @return void
	 */
	public function create_initial_pages() {

		$pages = staylodgic_get_template_pages();

		foreach ( $pages as $page ) {
			$this->create_custom_page( $page['title'], $page['template'], $page['content'], $page['slug'] );
		}

		// After creating pages, create the menu
		$this->create_booking_menu();
	}

	/**
	 * Method Function to create or update the booking menu
	 *
	 * @return void
	 */
	public function create_booking_menu() {
		$menu_name   = 'booking-menu';
		$menu_exists = wp_get_nav_menu_object( $menu_name );

		// Delete the existing menu if it exists
		if ( $menu_exists ) {
			wp_delete_nav_menu( $menu_exists->term_id );
		}

		// Create a new menu
		$menu_id = wp_create_nav_menu( $menu_name );

		// Get the template file names from theme options
		$menu_templates = array(
			'booking_menu_one'   => staylodgic_get_option( 'booking_menu_one' ),
			'booking_menu_two'   => staylodgic_get_option( 'booking_menu_two' ),
			'booking_menu_three' => staylodgic_get_option( 'booking_menu_three' ),
			'booking_menu_four'  => staylodgic_get_option( 'booking_menu_four' ),
		);

		// Find pages by template files
		$menu_items = array();
		foreach ( $menu_templates as $template ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'page',
					'meta_key'       => '_wp_page_template',
					'meta_value'     => $template,
					'posts_per_page' => 1,
				)
			);
			if ( $query->have_posts() ) {
				$query->the_post();
				$menu_items[] = get_the_ID();
			}
			wp_reset_postdata();
		}

		// Add new menu items
		foreach ( $menu_items as $page_id ) {
			if ( $page_id ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-object-id' => $page_id,
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}

		// Set the menu as Main Menu and Mobile Menu
		// $locations = get_theme_mod('nav_menu_locations'); // Get all theme locations
		$locations['main_menu']   = $menu_id; // Assign the menu to Main Menu
		$locations['mobile_menu'] = $menu_id; // Assign the menu to Mobile Menu
		set_theme_mod( 'nav_menu_locations', $locations ); // Update the locations
	}

	/**
	 * Method staylodgic_import_settings
	 *
	 * @return void
	 */
	public function staylodgic_import_settings() {

		if ( isset( $_POST['action'] ) && 'import_settings' === $_POST['action'] && isset( $_FILES['import_settings_file'] ) ) {
			// Prevent unauthorized access
			$nonce = isset( $_POST['import_settings_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['import_settings_nonce_field'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'import_settings_nonce' ) ) {
				wp_die(
					esc_html__( 'Security check failed.', 'staylodgic' ),
					esc_html__( 'Unauthorized Request', 'staylodgic' ),
					array( 'response' => 403 )
				);
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'You do not have permission to perform this action.', 'staylodgic' ),
					esc_html__( 'Access Denied', 'staylodgic' ),
					array( 'response' => 403 )
				);
			}

			if ( isset( $_FILES['import_settings_file'] ) ) {
				$file = array(
					'name'     => isset( $_FILES['import_settings_file']['name'] ) ? sanitize_file_name( $_FILES['import_settings_file']['name'] ) : '',
					'type'     => isset( $_FILES['import_settings_file']['type'] ) ? sanitize_mime_type( $_FILES['import_settings_file']['type'] ) : '',
					'tmp_name' => isset( $_FILES['import_settings_file']['tmp_name'] ) ? esc_url_raw( $_FILES['import_settings_file']['tmp_name'] ) : '',
					'error'    => isset( $_FILES['import_settings_file']['error'] ) ? intval( $_FILES['import_settings_file']['error'] ) : UPLOAD_ERR_NO_FILE,
					'size'     => isset( $_FILES['import_settings_file']['size'] ) ? intval( $_FILES['import_settings_file']['size'] ) : 0,
				);
			}
			// Ensure the file was uploaded without errors
			if ( UPLOAD_ERR_OK === $file['error'] && 'application/json' === $file['type'] ) {
				// Read the file and decode the JSON data
				global $wp_filesystem;
				if ( ! function_exists( 'WP_Filesystem' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				WP_Filesystem();

				$json_data = '';
				if ( is_readable( $file['tmp_name'] ) ) {
					$json_data = $wp_filesystem->get_contents( $file['tmp_name'] );
				}
				$import_data = json_decode( $json_data, true );

				// Validate the decoded data. This depends on your settings structure.
				// Here, we assume $import_data is an associative array matching your options structure.
				if ( is_array( $import_data ) ) {
					// Iterate through each setting to validate and sanitize it
					$sanitized_data = array();
					foreach ( $import_data as $key => $value ) {
						if ( isset( $this->settings[ $key ] ) && 'checkbox' === $this->settings[ $key ]['type'] ) {
							// Convert "1" to "on" for checkboxes
							// Check if the value is equal to '1'
							if ( '1' === $value ) {
								// If the value is '1', set the sanitized data key to 'on'
								$sanitized_data[ $key ] = 'on';
							} else {
								// If the value is not '1', set the sanitized data key to an empty string
								$sanitized_data[ $key ] = '';
							}
						} elseif ( is_array( $value ) ) {
							// The value is an array, handle each element according to its expected type
							foreach ( $value as $import_sub_key => $import_sub_value ) {
								if ( is_array( $import_sub_value ) ) {
									// If the import_sub_value is also an array, apply further sanitization as needed
									// This example assumes import_sub_value might be a structured array needing detailed sanitization
									foreach ( $import_sub_value as $field_key => $field_value ) {
										// Apply sanitization based on field_key or expected data type
										// This is a placeholder for actual sanitization logic
										$sanitized_data[ $key ][ $import_sub_key ][ $field_key ] = sanitize_text_field( $field_value );
									}
								} else {
									// For simple nested arrays, directly apply a generic sanitization
									$sanitized_data[ $key ][ $import_sub_key ] = sanitize_text_field( $import_sub_value );
								}
							}
						} elseif ( isset( $this->settings[ $key ] ) && 'textarea' === $this->settings[ $key ]['type'] ) {
							// Check if the input type is textarea
							$sanitized_data[ $key ] = sanitize_textarea_field( $value ); // Preserves new lines
						} else {
							// For other non-array settings, apply generic sanitization
							$sanitized_data[ $key ] = sanitize_text_field( $value );
						}
					}

					// Update the settings in the database
					update_option( 'staylodgic_settings', $sanitized_data );

					// Optionally, add a message to show success or redirect back to the settings page
					add_settings_error(
						$this->option_name . '_mesages',
						$this->option_name . '_message',
						esc_html__( 'Settings imported successfully.', 'staylodgic' ),
						'updated'
					);
				} else {
					// Handle error in case JSON is invalid
					add_settings_error(
						$this->option_name . '_mesages',
						$this->option_name . '_message',
						esc_html__( 'Invalid JSON data provided.', 'staylodgic' ),
						'error'
					);
				}
			}
		}
	}


	/**
	 * Method enqueue_media_uploader
	 *
	 * @return void
	 */
	public function enqueue_media_uploader() {
		wp_enqueue_media();
		wp_enqueue_style( 'thickbox' ); // if not included
	}

	/**
	 * Method Register the new menu page.
	 *
	 * @return void
	 */
	public function register_menu_page() {
		add_submenu_page(
			'staylodgic-settings',
			$this->title,
			$this->title,
			$this->user_capability,
			$this->slug,
			array( $this, 'render_options_page' )
		);
	}

	/**
	 * Method Register the settings.
	 *
	 * @return void
	 */
	public function export_settings() {
		if ( isset( $_POST['action'] ) && 'export_settings' === $_POST['action'] ) {
			// Security check, for example, check user permissions and nonces
			// Verify nonce
			$nonce = isset( $_POST['export_settings_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['export_settings_nonce_field'] ) ) : '';

			if ( ! wp_verify_nonce( $nonce, 'export_settings_nonce' ) ) {
				wp_die(
					esc_html__( 'Security check failed.', 'staylodgic' ),
					esc_html__( 'Unauthorized Request', 'staylodgic' ),
					array( 'response' => 403 )
				);
			}

			// Verify user capability
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(
					esc_html__( 'You do not have permission to export settings.', 'staylodgic' ),
					esc_html__( 'Access Denied', 'staylodgic' ),
					array( 'response' => 403 )
				);
			}
			// Fetch all settings
			$option_name = 'staylodgic_settings'; // Replace with your actual option name
			$settings    = get_option( $option_name );

			// Encode settings to JSON
			$json_settings = wp_json_encode( $settings );

			// Set headers to force download
			header( 'Content-Type: application/json' );
			header( 'Content-Disposition: attachment; filename="settings-export.json"' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );

			// Escape JSON output with wp_kses_post
			echo wp_kses_post( $json_settings );
			exit;
		}
	}
	public function section_heading_callback() {
		echo '<h2 class="section_heading">' . esc_html( $this->option_name ) . '</h2>';
	}

	/**
	 * Method Register the settings.
	 *
	 * @return void
	 */
	public function register_settings() {

		// phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic -- false positive
		register_setting(
			'staylodgic_settings_group',
			'staylodgic_settings',
			array(
				'sanitize_callback' => array( $this, 'sanitize_fields' ),
				'default'           => $this->get_defaults(),
			)
		);

		add_settings_section(
			'staylodgic_settings_sections',
			'', // Set the title to an empty string because we'll add it in the callback
			array( $this, 'section_heading_callback' ), // Use the custom callback
			$this->option_name
		);

		foreach ( $this->settings as $key => $args ) {
			$type     = $args['type'] ?? 'text';
			$callback = "render_{$type}_field";
			if ( method_exists( $this, $callback ) ) {
				$tr_class = '';
				if ( array_key_exists( 'tab', $args ) ) {
					$tr_class .= 'staylodgic-tab-item staylodgic-tab-item--' . sanitize_html_class( $args['tab'] );
				}

				add_settings_field(
					$key,
					$args['label'],
					array( $this, $callback ),
					$this->option_name,
					$this->option_name . '_sections',
					array(
						'label_for' => $key,
						'class'     => $tr_class,
					)
				);
			}
		}
	}

	/**
	 * Method Saves our fields.
	 *
	 * @param $values $values [explicite description]
	 *
	 * @return void
	 */
	public function sanitize_fields( $values ) {
		$new_values = array();
		foreach ( $this->settings as $key => $args ) {
			if ( isset( $values[ $key ] ) ) {
				// List of keys that use the same sanitization function
				$discount_keys = array( 'discount_lastminute', 'discount_earlybooking', 'discount_longstay' );

				if ( in_array( $key, $discount_keys, true ) ) {
					$new_values[ $key ] = $this->sanitize_discount_fileds( $values[ $key ] );
				} else {
					// Handle other fields as before
					$field_type         = $args['type'];
					$sanitize_callback  = $args['sanitize_callback'] ?? $this->get_sanitize_callback_by_type( $field_type );
					$new_values[ $key ] = call_user_func( $sanitize_callback, $values[ $key ], $args );
				}
			}
		}
		return $new_values;
	}

	/**
	 * Method sanitize_discount_fileds
	 *
	 * @param $value $value [explicite description]
	 *
	 * @return void
	 */
	protected function sanitize_discount_fileds( $value ) {
		$sanitized_value = array();
		if ( is_array( $value ) ) {
			foreach ( $value as $sub_key => $sub_value ) {
				switch ( $sub_key ) {
					case 'label':
						$sanitized_value['label'] = sanitize_text_field( $sub_value );
						break;
					case 'days':
						$sanitized_value['days'] = intval( $sub_value );
						break;
					case 'percent':
						$sanitized_value['percent'] = floatval( $sub_value );
						break;
				}
			}
		}
		return $sanitized_value;
	}

	/**
	 * Method Returns sanitize callback based on field type.
	 *
	 * @param $field_type $field_type [explicite description]
	 *
	 * @return void
	 */
	protected function get_sanitize_callback_by_type( $field_type ) {
		switch ( $field_type ) {
			case 'select':
				return array( $this, 'sanitize_select_field' );
			case 'number':
				return 'sanitize_text_field';
			case 'textarea':
				return 'wp_kses_post';
			case 'checkbox':
				return array( $this, 'sanitize_checkbox_field' );
			case 'repeatable_tax':
				return array( $this, 'sanitize_tax_field' );
			case 'activity_repeatable_tax':
				return array( $this, 'sanitize_tax_field' );
			case 'repeatable_perperson':
				return array( $this, 'sanitize_tax_field' );
			case 'repeatable_mealplan':
				return array( $this, 'sanitize_tax_field' );
			case 'media_upload':
				return 'absint';
			default:
			case 'text':
				return 'sanitize_text_field';
		}
	}

	/**
	 * Method Returns default values.
	 *
	 * @return void
	 */
	protected function get_defaults() {
		$defaults = array();
		foreach ( $this->settings as $key => $args ) {
			$defaults[ $key ] = $args['default'] ?? '';
		}
		return $defaults;
	}

	/**
	 * Method Sanitizes the tax field.
	 *
	 * @param $value $value [explicite description]
	 * @param $field_args $field_args [explicite description]
	 *
	 * @return void
	 */
	protected function sanitize_tax_field( $value = '', $field_args = array() ) {
		return $value;
	}

	/**
	 * Method Sanitizes the checkbox field.
	 *
	 * @param $value $value [explicite description]
	 * @param $field_args $field_args [explicite description]
	 *
	 * @return void
	 */
	protected function sanitize_checkbox_field( $value = '', $field_args = array() ) {
		return ( 'on' === $value ) ? 1 : 0;
	}

	/**
	 * Method Sanitizes the select field.
	 *
	 * @param $value $value [explicite description]
	 * @param $field_args $field_args [explicite description]
	 *
	 * @return void
	 */
	protected function sanitize_select_field( $value = '', $field_args = array() ) {
		$choices = $field_args['choices'] ?? array();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}
	}

	/**
	 * Method Renders the options page.
	 *
	 * @return void
	 */
	public function render_options_page() {

		// Verify the nonce if the form was submitted from this settings group
		if ( isset( $_POST['option_page'] ) && sanitize_text_field( wp_unslash( $_POST['option_page'] ) ) === $this->option_group_name ) {
			check_admin_referer( 'options.php' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to update settings.', 'staylodgic' ),
				esc_html__( 'Access Denied', 'staylodgic' ),
				array( 'response' => 403 )
			);
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error(
				$this->option_name . '_mesages',
				$this->option_name . '_message',
				esc_html__( 'Settings Saved', 'staylodgic' ),
				'updated'
			);

			\Staylodgic\Cache::clear_all_cache();

			$this->create_initial_pages();
		}

		settings_errors( $this->option_name . '_mesages' );

		?>
		<div class="wrap">
			<h1 class="options-heading"><span><?php esc_html_e( 'Hotel Settings', 'staylodgic' ); ?></span></h1>
			<div class="staylodgic-tabform-wrapper menu-closed">
				<?php $this->render_tabs(); ?>
				<div class="staylodgic-tab-content active" id="tab-property">
					<form action="options.php" method="post" class="staylodgic-options-form">
						<?php
						settings_fields( $this->option_group_name );
						do_settings_sections( $this->option_name );
						submit_button( 'Save Settings' );
						echo '<div class="help-guide-footer">View Staylodgic <a target="_blank" href="https://staylodgic.com/staylodgic-help-guide-viewer/">Help Guide</a></div>';
						?>
					</form>
				</div>
			</div>
			<?php
			$export_nonce = esc_attr( wp_create_nonce( 'export_settings_nonce' ) );
			// Add an export button
			echo '<form class="export-import-form" method="post">';
			echo '<div class="toggle-section">';
			echo '<div id="toggle-button"><i class="fa-solid fa-wrench"></i></div>';
			echo '</div>';
			echo '<div class="import-export-section" style="display: none;">';
			echo '<input type="hidden" name="export_settings_nonce_field" value="' . esc_attr( $export_nonce ) . '">';
			echo '<input type="hidden" name="action" value="export_settings" />';
			echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Export Settings">';
			echo '<button type="button" id="import-settings-button" class="button button-secondary">Import Settings</button>';
			echo '</div>';
			echo '</form>';
			?>
			<?php
			$nonce = esc_attr( wp_create_nonce( 'import_settings_nonce' ) );
			// Modal structure
			echo '<div id="import-settings-modal" class="staylodgic-modal" style="display:none;">
        <div class="staylodgic-modal-content">
            <span class="staylodgic-close">&times;</span>
            <form id="import-settings-form" method="post" enctype="multipart/form-data">
            <div class="import-file-upload-section">
                <input type="hidden" name="import_settings_nonce_field" value="' . esc_attr( $nonce ) . '">
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

	/**
	 * Method render_tabs
	 *
	 * @return void
	 */
	protected function render_tabs() {
		if ( empty( $this->args['tabs'] ) ) {
			return;
		}

		$tabs = $this->args['tabs'];
		?>
		<div class="nav-tab-wrapper staylodgic-tabs">
			<div class="staylodgic-tabs-container">
				<?php
				$first_tab = true;
				// Example heading for a group of tabs
				echo '<h3 class="staylodgic-tab-heading">' . esc_html__( 'General Settings', 'staylodgic' ) . '</h3>';
				foreach ( $tabs as $id => $label ) {
					// Example condition to add a heading before a specific tab
					if ( 'general' === $id ) {
						echo '<h3 class="staylodgic-tab-heading">' . esc_html__( 'Hotel Settings', 'staylodgic' ) . '</h3>';
					}
					$tab_section = ucfirst( $label );
					?>
					<a href="#" data-heading="<?php echo esc_attr( $label ); ?>" data-tab="<?php echo esc_attr( $id ); ?>" class="nav-tab<?php echo ( $first_tab ) ? ' nav-tab-active' : ''; ?>"><?php echo wp_kses( $tab_section, staylodgic_get_allowed_tags() ); ?></a>
					<?php
					$first_tab = false;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Method Returns an option value.
	 *
	 * @param $option_name $option_name [explicite description]
	 *
	 * @return void
	 */
	protected function get_option_value( $option_name ) {
		$option = get_option( $this->option_name );
		if ( is_array( $option ) ) {
			if ( ! array_key_exists( $option_name, $option ) ) {
				return array_key_exists( 'default', $this->settings[ $option_name ] ) ? $this->settings[ $option_name ]['default'] : '';
			}
		} else {
			return '';
		}
		return $option[ $option_name ];
	}

	/**
	 * Method Media uploading
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_media_upload_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$image       = esc_html__( 'Upload image', 'staylodgic' ); // Escape the static text for internationalization
		$image_size  = 'full'; // it should be thumbnail, medium, or large
		$display     = 'none'; // Display state of the "Remove image" button

		echo '<div class="options-image-display">
				<a href="#" class="upload_image_button button">';

		if ( $value ) {
			$image_attributes = wp_get_attachment_image_src( $value, $image_size );
			if ( $image_attributes ) {
				// Use esc_url for the image source URL and output the HTML image tag safely
				echo '<img src="' . esc_url( $image_attributes[0] ) . '" style="max-height:100px;display:block;" />';
				$display = 'inline-block';
			}
		}

		echo '</a>'; // Output $image variable here to ensure it is displayed correctly

		echo '<input type="hidden" name="' . esc_attr( $this->option_name ) . '[' . esc_attr( $args['label_for'] ) . ']" id="' . esc_attr( $args['label_for'] ) . '" value="' . esc_attr( $value ) . '" />
			  <a href="#" class="remove_image_button" style="display:' . esc_attr( $display ) . '"><i class="dashicons dashicons-remove"></i></a>
			</div>';
	}

	/**
	 * Method Renders perperson field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_repeatable_perperson_field( $args ) {
		$option_name = $args['label_for'];
		$array       = $this->get_option_value( $option_name );
		$description = '';
		if ( isset( $this->settings[ $option_name ] ) && isset( $this->settings[ $option_name ]['description'] ) ) {
			// If the description exists, assign it to $description
			$description = $this->settings[ $option_name ]['description'];
		}

		?>
		<div class="repeatable-perperson-template" style="display: none;">
			<div class="repeatable">
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'People', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_people" name="people">
						<option value="1"><?php esc_html_e( '1', 'staylodgic' ); ?></option>
						<option value="3"><?php esc_html_e( '3', 'staylodgic' ); ?></option>
						<option value="4"><?php esc_html_e( '4', 'staylodgic' ); ?></option>
						<option value="5"><?php esc_html_e( '5', 'staylodgic' ); ?></option>
						<option value="6"><?php esc_html_e( '6', 'staylodgic' ); ?></option>
						<option value="7"><?php esc_html_e( '7', 'staylodgic' ); ?></option>
						<option value="8"><?php esc_html_e( '8', 'staylodgic' ); ?></option>
						<option value="9"><?php esc_html_e( '9', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Value', 'staylodgic' ); ?></span>
					<input disabled type="number" placeholder="Value" id="<?php echo esc_attr( $args['label_for'] ); ?>_number" name="number" value="">
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_type" name="type">
						<option value="fixed"><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
						<option value="percentage"><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Difference', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_total" name="total">
						<option value="increase"><?php esc_html_e( 'Increase', 'staylodgic' ); ?></option>
						<option value="decrease"><?php esc_html_e( 'Decrease', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
			</div>
		</div>
		<div id="repeatable-perperson-container">
			<?php

			$count = 0;
			if ( is_array( $array ) ) {
				foreach ( $array as $key => $value ) {
					++$count;
					if ( isset( $value['people'] ) ) {
						?>
						<div class="repeatable">
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'People', 'staylodgic' ); ?></span>
								<select data-width="80" id="<?php echo esc_attr( $args['label_for'] ); ?>_people_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][people]">
									<option value="1" <?php selected( '1', $value['people'], true ); ?>><?php esc_html_e( '1', 'staylodgic' ); ?></option>
									<option value="3" <?php selected( '3', $value['people'], true ); ?>><?php esc_html_e( '3', 'staylodgic' ); ?></option>
									<option value="4" <?php selected( '4', $value['people'], true ); ?>><?php esc_html_e( '4', 'staylodgic' ); ?></option>
									<option value="5" <?php selected( '5', $value['people'], true ); ?>><?php esc_html_e( '5', 'staylodgic' ); ?></option>
									<option value="6" <?php selected( '6', $value['people'], true ); ?>><?php esc_html_e( '6', 'staylodgic' ); ?></option>
									<option value="7" <?php selected( '7', $value['people'], true ); ?>><?php esc_html_e( '7', 'staylodgic' ); ?></option>
									<option value="8" <?php selected( '8', $value['people'], true ); ?>><?php esc_html_e( '8', 'staylodgic' ); ?></option>
									<option value="9" <?php selected( '9', $value['people'], true ); ?>><?php esc_html_e( '9', 'staylodgic' ); ?></option>
								</select>
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Value', 'staylodgic' ); ?></span>
								<input type="number" class="perpersonpricing_number_setter" id="<?php echo esc_attr( $args['label_for'] ); ?>_number_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][number]" value="<?php echo esc_attr( $value['number'] ); ?>">
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
								<select data-width="150" id="<?php echo esc_attr( $args['label_for'] ); ?>_type_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][type]">
									<option value="fixed" <?php selected( 'fixed', $value['type'], true ); ?>><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
									<option value="percentage" <?php selected( 'percentage', $value['type'], true ); ?>><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
								</select>
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Difference', 'staylodgic' ); ?></span>
								<select data-width="150" id="<?php echo esc_attr( $args['label_for'] ); ?>_total_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][total]">
									<option value="increase" <?php selected( 'increase', $value['total'], true ); ?>><?php esc_html_e( 'Increase', 'staylodgic' ); ?></option>
									<option value="decrease" <?php selected( 'decrease', $value['total'], true ); ?>><?php esc_html_e( 'Decrease', 'staylodgic' ); ?></option>
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
		<button id="addperperson-repeatable" class="button button-secondary"><?php esc_html_e( 'Add new', 'staylodgic' ); ?></button>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders Mealplan field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_repeatable_mealplan_field( $args ) {
		$option_name = $args['label_for'];
		$array       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';

		?>
		<div class="repeatable-mealplan-template" style="display: none;">
			<div class="repeatable new-container">
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Meal', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_mealtype" name="mealtype">
						<option value="RO"><?php esc_html_e( 'Room Only', 'staylodgic' ); ?></option>
						<option value="BB"><?php esc_html_e( 'Bed and Breakfast', 'staylodgic' ); ?></option>
						<option value="HB"><?php esc_html_e( 'Half Board', 'staylodgic' ); ?></option>
						<option value="FB"><?php esc_html_e( 'Full Board', 'staylodgic' ); ?></option>
						<option value="AN"><?php esc_html_e( 'All-Inclusive', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_choice" name="choice">
						<option value="included"><?php esc_html_e( 'Included in rate', 'staylodgic' ); ?></option>
						<option value="optional"><?php esc_html_e( 'Optional', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Price', 'staylodgic' ); ?></span>
					<input disabled type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>_price" name="price" value="">
				</span>
				<span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
			</div>
		</div>
		<div id="repeatable-mealplan-container">
			<?php
			$count = 0;
			if ( is_array( $array ) ) {
				foreach ( $array as $key => $value ) {
					++$count;
					if ( isset( $value['mealtype'] ) ) {
						?>
						<div class="repeatable">
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Meal', 'staylodgic' ); ?></span>
								<select data-width="170" id="<?php echo esc_attr( $args['label_for'] ); ?>_mealtype_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][mealtype]">
									<option value="RO" <?php selected( 'RO', $value['mealtype'], true ); ?>><?php esc_html_e( 'Room Only', 'staylodgic' ); ?></option>
									<option value="BB" <?php selected( 'BB', $value['mealtype'], true ); ?>><?php esc_html_e( 'Bed and Breakfast', 'staylodgic' ); ?></option>
									<option value="HB" <?php selected( 'HB', $value['mealtype'], true ); ?>><?php esc_html_e( 'Half Board', 'staylodgic' ); ?></option>
									<option value="FB" <?php selected( 'FB', $value['mealtype'], true ); ?>><?php esc_html_e( 'Full Board', 'staylodgic' ); ?></option>
									<option value="AN" <?php selected( 'AN', $value['mealtype'], true ); ?>><?php esc_html_e( 'All-Inclusive', 'staylodgic' ); ?></option>
								</select>
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
								<select data-width="150" id="<?php echo esc_attr( $args['label_for'] ); ?>_choice_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][choice]">
									<option value="included" <?php selected( 'included', $value['choice'], true ); ?>><?php esc_html_e( 'Included in rate', 'staylodgic' ); ?></option>
									<option value="optional" <?php selected( 'optional', $value['choice'], true ); ?>><?php esc_html_e( 'Optional', 'staylodgic' ); ?></option>
								</select>
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Price', 'staylodgic' ); ?></span>
								<input type="number" class="mealplan-style-setter" id="<?php echo esc_attr( $args['label_for'] ); ?>_price_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][price]" value="<?php echo esc_attr( $value['price'] ); ?>">
							</span>
							<span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
						</div>
						<?php
					}
				}
			}
			?>
		</div>
		<button id="addmealplan-repeatable" class="button button-secondary"><?php esc_html_e( 'Add new', 'staylodgic' ); ?></button>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders tax field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_repeatable_tax_field( $args ) {
		$option_name = $args['label_for'];
		$array       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';

		?>
		<div class="repeatable-tax-template" style="display: none;">
			<div class="repeatable">
				<span class="fa-solid fa-sort"></span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Name', 'staylodgic' ); ?></span>
					<input disabled type="text" placeholder="Label" id="<?php echo esc_attr( $args['label_for'] ); ?>_label" name="label" value="">
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Value', 'staylodgic' ); ?></span>
					<input disabled type="number" placeholder="Value" id="<?php echo esc_attr( $args['label_for'] ); ?>_number" name="number" value="">
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_type" name="type">
						<option value="fixed"><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
						<option value="percentage"><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Frequency', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_duration" name="duration">
						<option value="inrate"><?php esc_html_e( 'Add to rate', 'staylodgic' ); ?></option>
						<option value="perperson"><?php esc_html_e( 'Per person', 'staylodgic' ); ?></option>
						<option value="perday"><?php esc_html_e( 'Per day', 'staylodgic' ); ?></option>
						<option value="perpersonperday"><?php esc_html_e( 'Per person per day', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
			</div>
		</div>
		<div class="repeatable-tax-container-wrap">
			<div id="repeatable-tax-container">
				<?php

				$count = 0;
				if ( is_array( $array ) ) {
					foreach ( $array as $key => $value ) {
						++$count;
						if ( isset( $value['label'] ) ) {
							?>
							<div class="repeatable">
								<span class="fa-solid fa-sort"></span>
								<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Name', 'staylodgic' ); ?></span>
									<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>_label_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_attr( $value['label'] ); ?>">
								</span>
								<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Value', 'staylodgic' ); ?></span>
									<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>_number_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][number]" value="<?php echo esc_attr( $value['number'] ); ?>">
								</span>
								<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
									<select id="<?php echo esc_attr( $args['label_for'] ); ?>_type_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][type]">
										<option value="fixed" <?php selected( 'fixed', $value['type'], true ); ?>><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
										<option value="percentage" <?php selected( 'percentage', $value['type'], true ); ?>><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
									</select>
								</span>
								<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Frequency', 'staylodgic' ); ?></span>
									<select id="<?php echo esc_attr( $args['label_for'] ); ?>_duration_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][duration]">
										<option value="inrate" <?php selected( 'inrate', $value['duration'], true ); ?>><?php esc_html_e( 'Add to rate', 'staylodgic' ); ?></option>
										<option value="perperson" <?php selected( 'perperson', $value['duration'], true ); ?>><?php esc_html_e( 'Per person', 'staylodgic' ); ?></option>
										<option value="perday" <?php selected( 'perday', $value['duration'], true ); ?>><?php esc_html_e( 'Per Day', 'staylodgic' ); ?></option>
										<option value="perpersonperday" <?php selected( 'perpersonperday', $value['duration'], true ); ?>><?php esc_html_e( 'Per person per day', 'staylodgic' ); ?></option>
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
			<button id="addtax-repeatable" class="button button-secondary"><?php esc_html_e( 'Add new', 'staylodgic' ); ?></button>
		</div>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders tax field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_activity_repeatable_tax_field( $args ) {
		$option_name = $args['label_for'];
		$array       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';

		?>
		<div class="repeatable-activitytax-template" style="display: none;">
			<div class="repeatable">
				<span class="fa-solid fa-sort"></span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Name', 'staylodgic' ); ?></span>
					<input disabled type="text" placeholder="Label" id="<?php echo esc_attr( $args['label_for'] ); ?>_label" name="label" value="">
				</span>
				<span class="input-label-outer"><span class="input-label-inner">Value</span>
					<input disabled type="number" placeholder="Value" id="<?php echo esc_attr( $args['label_for'] ); ?>_number" name="number" value="">
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_type" name="type">
						<option value="fixed"><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
						<option value="percentage"><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Frequency', 'staylodgic' ); ?></span>
					<select disabled id="<?php echo esc_attr( $args['label_for'] ); ?>_duration" name="duration">
						<option value="inrate"><?php esc_html_e( 'Add to rate', 'staylodgic' ); ?></option>
						<option value="perperson"><?php esc_html_e( 'Per person', 'staylodgic' ); ?></option>
					</select>
				</span>
				<span class="remove-set-button"><i class="dashicons dashicons-remove"></i></span>
			</div>
		</div>
		<div id="repeatable-activitytax-container">
			<?php

			$count = 0;
			if ( is_array( $array ) ) {
				foreach ( $array as $key => $value ) {
					++$count;
					if ( isset( $value['label'] ) ) {
						?>
						<div class="repeatable">
							<span class="fa-solid fa-sort"></span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Name', 'staylodgic' ); ?></span>
								<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>_label_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_attr( $value['label'] ); ?>">
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Value', 'staylodgic' ); ?></span>
								<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>_number_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][number]" value="<?php echo esc_attr( $value['number'] ); ?>">
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Type', 'staylodgic' ); ?></span>
								<select id="<?php echo esc_attr( $args['label_for'] ); ?>_type_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][type]">
									<option value="fixed" <?php selected( 'fixed', $value['type'], true ); ?>><?php esc_html_e( 'Fixed', 'staylodgic' ); ?></option>
									<option value="percentage" <?php selected( 'percentage', $value['type'], true ); ?>><?php esc_html_e( 'Percentage', 'staylodgic' ); ?></option>
								</select>
							</span>
							<span class="input-label-outer"><span class="input-label-inner"><?php esc_html_e( 'Frequency', 'staylodgic' ); ?></span>
								<select id="<?php echo esc_attr( $args['label_for'] ); ?>_duration_<?php echo esc_attr( $count ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][<?php echo esc_attr( $key ); ?>][duration]">
									<option value="inrate" <?php selected( 'inrate', $value['duration'], true ); ?>><?php esc_html_e( 'Add to rate', 'staylodgic' ); ?></option>
									<option value="perperson" <?php selected( 'perperson', $value['duration'], true ); ?>><?php esc_html_e( 'Per person', 'staylodgic' ); ?></option>
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
		<button id="addtax-activity-repeatable" class="button button-secondary"><?php esc_html_e( 'Add new', 'staylodgic' ); ?></button>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a text field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_promotion_discount_field( $args ) {
		$option_name = $args['label_for'];
		$values      = $this->get_option_value( $option_name );

		// Ensure $values is an array and set default values if not set
		$values = is_array( $values ) ? $values : array(
			'label'   => '',
			'days'    => '',
			'percent' => '',
		);

		$description = $this->settings[ $option_name ]['description'] ?? '';
		?>
		<span class="discount-input-outer">
			<span class="discount-display-label">Label</span>
			<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>_label" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][label]" value="<?php echo esc_attr( $values['label'] ); ?>" placeholder="Label for discount">
		</span>
		<span class="discount-input-outer">
			<span class="discount-display-label">Days</span>
			<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>_days" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][days]" value="<?php echo esc_attr( $values['days'] ); ?>" placeholder="Number of days">
		</span>
		<span class="discount-input-outer">
			<span class="discount-display-label">Percent</span>
			<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>_percent" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>][percent]" value="<?php echo esc_attr( $values['percent'] ); ?>" placeholder="Discount percent">
		</span>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<p class="description"><strong><?php esc_html_e( 'Discounts are not stackable. Only the maximum discount is applied if multiple discounts are eligible.', 'staylodgic' ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a text field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_text_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a text field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_number_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';
		?>
		<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a textarea field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_textarea_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';
		$rows        = $this->settings[ $option_name ]['rows'] ?? '4';
		$cols        = $this->settings[ $option_name ]['cols'] ?? '50';
		?>
		<textarea type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" rows="<?php echo esc_attr( absint( $rows ) ); ?>" cols="<?php echo esc_attr( absint( $cols ) ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"><?php echo esc_attr( $value ); ?></textarea>
		<?php if ( $description ) { ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a checkbox field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';
		?>
		<label class="staylodgic-checkbox-container">
			<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]" <?php checked( $value, 1, true ); ?>>
			<span class="checkmark"></span>
		</label>
		<?php if ( $description ) { ?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * Method Renders a select field.
	 *
	 * @param $args $args [explicite description]
	 *
	 * @return void
	 */
	public function render_select_field( $args ) {
		$option_name = $args['label_for'];
		$value       = $this->get_option_value( $option_name );
		$description = $this->settings[ $option_name ]['description'] ?? '';
		$choices     = $this->settings[ $option_name ]['choices'] ?? array();
		$inputwidth  = $this->settings[ $option_name ]['inputwidth'] ?? '';
		?>
		<select class="single-options-select" data-width="<?php echo esc_attr( $inputwidth ); ?>" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $this->option_name ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]">
			<?php
			foreach ( $choices as $choice_v => $label ) {
				?>
				<option value="<?php echo esc_attr( $choice_v ); ?>" <?php selected( $choice_v, $value, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php
			}
			?>
		</select>
		<?php
		if ( $description ) {
			?>
			<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php
		}
		?>
		<?php
	}
}
