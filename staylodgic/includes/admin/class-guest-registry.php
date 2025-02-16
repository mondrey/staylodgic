<?php

namespace Staylodgic;

class Guest_Registry {


	protected $stay_booking_number;
	private static $id_label_map = array();
	private $stay_reservation_id;
	private $hotel_name;
	private $hotel_phone;
	private $hotel_address;
	private $hotel_header;
	private $hotel_footer;
	private $hotel_logo;

	public function __construct(
		$stay_booking_number = null,
		$stay_reservation_id = null,
		$hotel_name = null,
		$hotel_phone = null,
		$hotel_address = null,
		$hotel_header = null,
		$hotel_footer = null,
		$hotel_logo = null
	) {
		$this->stay_booking_number = get_post_meta( get_the_id(), 'staylodgic_registry_bookingnumber', true );

		add_shortcode( 'guest_registration', array( $this, 'stay_guest_registration' ) );

		add_action( 'wp_ajax_request_registration_details', array( $this, 'request_registration_details' ) );
		add_action( 'wp_ajax_nopriv_request_registration_details', array( $this, 'request_registration_details' ) );

		// Add a filter to modify the content of staylodgic_guestrgs posts
		add_filter( 'the_content', array( $this, 'append_shortcode_to_content' ) );

		add_action( 'wp_ajax_save_guestregistration_data', array( $this, 'save_guestregistration_data' ) );
		add_action( 'wp_ajax_nopriv_save_guestregistration_data', array( $this, 'save_guestregistration_data' ) );

		add_action( 'wp_ajax_get_guest_post_permalink', array( $this, 'get_guest_post_permalink' ) );
		add_action( 'wp_ajax_nopriv_get_guest_post_permalink', array( $this, 'get_guest_post_permalink' ) );

		add_action( 'wp_ajax_delete_registration', array( $this, 'delete_registration' ) );
		add_action( 'wp_ajax_nopriv_delete_registration', array( $this, 'delete_registration' ) );

		add_action( 'wp_ajax_create_guest_registration', array( $this, 'create_guest_registration_ajax_handler' ) );
	}

	/**
	 * Method create_guest_registration_ajax_handler
	 *
	 * @return void
	 */
	public function create_guest_registration_ajax_handler() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'staylodgic-nonce-admin' ) ) {
			wp_die( esc_html__( 'Unauthorized request.', 'staylodgic' ) );
		}
		// Check user capabilities or nonce here for security
		$stay_booking_number = isset( $_POST['stay_booking_number'] ) ? sanitize_text_field( wp_unslash( $_POST['stay_booking_number'] ) ) : '';
		// Create a new guest registration post
		$post_id = wp_insert_post(
			array(
				'post_title'   => esc_html__( 'Registration for', 'staylodgic' ) . ' ' . wp_strip_all_tags( $stay_booking_number ),
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'staylodgic_guestrgs', // Ensure this is the correct post type
				'meta_input'   => array(
					'staylodgic_registry_bookingnumber' => $stay_booking_number,
				),
			)
		);

		if ( $post_id ) {
			// Successfully created post, return its ID
			echo esc_html( $post_id );
		} else {
			// There was an error
			echo 'error';
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Checks if a guest registration post exists for a given booking number.
	 *
	 * @param string $stay_booking_number The booking number to search for.
	 * @return bool|int Returns the guest register post ID if found, otherwise returns false.
	 */
	public function check_guest_registration_by_booking_number( $stay_booking_number ) {
		$guest_register_args = array(
			'post_type'      => 'staylodgic_guestrgs', // Adjust to your guest register post type
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_registry_bookingnumber', // Ensure this matches your actual meta key
					'value' => $stay_booking_number,
				),
			),
		);

		$guest_register_query = new \WP_Query( $guest_register_args );

		// Check if a guest register post is found
		if ( $guest_register_query->have_posts() ) {
			// Return the ID of the guest registration post
			return $guest_register_query->posts[0]->ID;
		}

		return false; // Return false if no guest registration post is found
	}

	/**
	 * Fetches the reservation and guest register post IDs based on a supplied booking number.
	 * Returns an associative array with 'stay_reservation_id' and 'guest_register_id' if both are found,
	 * otherwise returns false.
	 *
	 * @param string $stay_booking_number The booking number to search for.
	 * @return array|bool An associative array with 'stay_reservation_id' and 'guest_register_id', or false if not both found.
	 */
	public function fetch_res_reg_ids_by_booking_number( $stay_booking_number ) {
		$stay_reservation_args = array(
			'post_type'      => 'staylodgic_bookings', // Adjust to your reservation post type
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_booking_number',
					'value' => $stay_booking_number,
				),
			),
		);

		$guest_register_args = array(
			'post_type'      => 'staylodgic_guestrgs', // Adjust to your guest register post type
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'   => 'staylodgic_registry_bookingnumber', // Adjust if the meta key is different
					'value' => $stay_booking_number,
				),
			),
		);

		$stay_reservation_query = new \WP_Query( $stay_reservation_args );
		$guest_register_query   = new \WP_Query( $guest_register_args );

		// Check if both posts are found
		if ( $stay_reservation_query->have_posts() && $guest_register_query->have_posts() ) {
			$result = array(
				'stay_reservation_id' => $stay_reservation_query->posts[0]->ID,
				'guest_register_id'   => $guest_register_query->posts[0]->ID,
			);
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * Method allow_guest_registration
	 *
	 * @param $registration_post_id
	 *
	 * @return void
	 */
	public function allow_guest_registration( $registration_post_id ) {
		$allow  = true;
		$reason = '';

		$stay_booking_number = get_post_meta( $registration_post_id, 'staylodgic_registry_bookingnumber', true );

		$res_reg_ids = $this->fetch_res_reg_ids_by_booking_number( $stay_booking_number );

		$stay_reservation_id = $res_reg_ids['stay_reservation_id'];
		$register_id         = $res_reg_ids['guest_register_id'];

		$reservation_instance = new \Staylodgic\Reservations();

		$stay_checkin_date = $reservation_instance->get_checkin_date( $stay_reservation_id );
		// Convert check-in date to DateTime object
		$stay_checkin_date_obj = new \DateTime( $stay_checkin_date );

		// Get today's date
		$today     = new \DateTime();
		$yesterday = $today->modify( '-1 day' );

		// Check if check-in date has already passed
		if ( $yesterday > $stay_checkin_date_obj ) {
			$allow  = false;
			$reason = __( 'Check-in date has already passed', 'staylodgic' );
		} else {
			// Calculate the difference in days
			$date_diff = $today->diff( $stay_checkin_date_obj )->days;

			// If the difference is more than 7 days, set $allow to false
			if ( $date_diff > 7 ) {
				$allow  = false;
				$reason = __( 'Registration open 7 days before check-in', 'staylodgic' );
			}
		}

		// Get total occupants for the reservation
		$reservation_occupants = $reservation_instance->get_total_occupants_for_reservation( $stay_reservation_id );

		// Get registered guest count
		$registered_guest_count = $this->get_registered_guest_count( $register_id );

		if ( ( intval( $reservation_occupants ) + 2 ) < $registered_guest_count ) {
			$allow  = false;
			$reason = __( 'Total registrations allowed for this booking exceeded.', 'staylodgic' );
		}

		if ( $reason ) {
			$reason = '<div class="error-registration-reason">' . $reason . '</div>';
		}

		if ( current_user_can( 'edit_pages' ) ) {
			// The current user is an editor
			$allow = true;
		}

		return array(
			'allow'  => $allow,
			'reason' => $reason,
		);
	}

	/**
	 * Outputs the final registered and occupancy numbers for a given reservation in either text, fraction, or icons format.
	 *
	 * @param int $stay_reservation_id The ID of the reservation.
	 * @param int $register_id The ID used for registering.
	 * @param string $output_format Specifies the output format: 'text', 'fraction', or 'icons'.
	 */
	public function output_registration_and_occupancy( $stay_reservation_id, $register_id, $output_format = 'text' ) {
		$reservation_instance = new \Staylodgic\Reservations();

		// Get total occupants for the reservation
		$reservation_occupants = $reservation_instance->get_total_occupants_for_reservation( $stay_reservation_id );

		// Get registered guest count
		$registered_guest_count = $this->get_registered_guest_count( $register_id );

		$registration_output = '';

		// Determine the output format
		if ( 'icons' === $output_format ) {
			$registration_output .= '<div class="reservation-details">';
			// Output filled circles for registered guests
			for ( $i = 0; $i < $registered_guest_count; $i++ ) {
				$registration_output .= '<i class="fas fa-circle"></i> ';
			}
			// Output outline circles for the remaining occupancy
			for ( $i = $registered_guest_count; $i < $reservation_occupants; $i++ ) {
				$registration_output .= '<i class="far fa-circle"></i> ';
			}
			$registration_output .= '</div>';
		} elseif ( 'fraction' === $output_format ) {
			// Fallback to fraction if registered guests exceed total occupancy or fraction is requested
			$registration_output .= '<div class="reservation-details">';
			$registration_output .= '<div class="occupancy-details">';
			$registration_output .= '<span class="registration-label">';
			$registration_output .= 'Registered: ' . esc_html( $registered_guest_count ) . '/' . esc_html( $reservation_occupants ) . ' ';
			$registration_output .= '</span>';
			$registration_output .= '<a title="' . __( 'View Registrations', 'staylodgic' ) . '" href="' . esc_url( get_edit_post_link( $register_id ) ) . '">';
			$registration_output .= '<i class="fa-solid fa-pen-to-square"></i>';
			$registration_output .= '</a>';
			$registration_output .= '</div>';
			$registration_output .= '</div>';
		} else { // Default to text format
			$registration_output .= '<div class="reservation-details">';
			$registration_output .= '<div class="registered-occupants"><span class="registration-label">' . __( 'Total guests', 'staylodgic' ) . '</span>: ' . esc_html( $reservation_occupants ) . '</div>';
			$registration_output .= '<div class="registered-guests"><span class="registration-label">' . __( 'Registered guests', 'staylodgic' ) . '</span>: ' . esc_html( $registered_guest_count ) . '</div>';
			$registration_output .= '</div>';
		}

		return $registration_output;
	}

	/**
	 * Returns the count of registered guests from the registration_data array for a given reservation ID.
	 * If no ID is supplied, it attempts to fetch the current post ID.
	 *
	 * @param int|null $stay_registration_id Optional. The ID of the reservation post. Default null.
	 * @return int The number of registered guests.
	 */
	public function get_registered_guest_count( $stay_registration_id = null ) {
		// Use the supplied ID or fallback to the current post ID
		$id_to_use = $stay_registration_id ? $stay_registration_id : get_the_id();

		$registration_data = get_post_meta( $id_to_use, 'staylodgic_registration_data', true );
		if ( is_array( $registration_data ) ) {
			return count( $registration_data );
		}
		return 0; // Return 0 if registration_data is not an array or is empty
	}

	/**
	 * Method display_registration
	 *
	 * @return void
	 */
	public function display_registration() {

		// Hotel Information
		$property_logo_id = staylodgic_get_option( 'property_logo' );
		$property_name    = staylodgic_get_option( 'property_name' );
		$property_phone   = staylodgic_get_option( 'property_phone' );
		$property_address = staylodgic_get_option( 'property_address' );
		$property_header  = staylodgic_get_option( 'property_header' );
		$property_footer  = staylodgic_get_option( 'property_footer' );

		$this->hotel_name    = $property_name;
		$this->hotel_phone   = $property_phone;
		$this->hotel_address = $property_address;
		$this->hotel_header  = $property_header;
		$this->hotel_footer  = $property_footer;
		$this->hotel_logo    = $property_logo_id ? wp_get_attachment_image_url( $property_logo_id, 'full' ) : '';

		$registration_sheet = $this->registration_template(
			$this->stay_booking_number,
			$this->hotel_name,
			$this->hotel_phone,
			$this->hotel_address,
			$this->hotel_header,
			$this->hotel_footer,
			$this->hotel_logo
		);

		if ( isset( $registration_sheet ) ) {
			echo wp_kses( $registration_sheet, staylodgic_get_guest_registration_tags() );
		} else {
			echo '<div class="registrations-not-found-notice-wrap">';
			echo '<div class="registrations-not-found-notice">';
			echo esc_html__( 'Registrations not found', 'staylodgic' );
			echo '</div>';
			echo '</div>';
		}
	}

	/**
	 * Method registration_template
	 *
	 * @return void
	 */
	public function registration_template(
		$stay_booking_number,
		$hotel_name,
		$hotel_phone,
		$hotel_address,
		$hotel_header,
		$hotel_footer,
		$hotel_logo
	) {
		ob_start(); // Start output buffering

		$stay_current_date   = gmdate( 'F jS, Y' );
		$registration_data   = get_post_meta( get_the_id(), 'staylodgic_registration_data', true );
		$property_logo_width = staylodgic_get_option( 'property_logo_width' );

		if ( is_array( $registration_data ) && ! empty( $registration_data ) ) {
			foreach ( $registration_data as $guest_id => $guest_data ) {
				$post_url  = get_permalink( get_the_id() );
				$edit_url  = add_query_arg( 'guest', $guest_id, $post_url );
				$nonce_url = wp_nonce_url( $edit_url, 'edit_registration_' . $guest_id );

				?>
				<div class="invoice-buttons-container">
					<div class="invoice-container-buttons">
						<a href="<?php echo esc_url( $nonce_url ); ?>" target="_blank" class="button button-secondary registration-button edit-registration" data-guest-id="<?php echo esc_attr( $guest_id ); ?>"><?php esc_html_e( 'Edit', 'staylodgic' ); ?></a>
						<button data-title="<?php echo esc_attr( 'Guest Registration ' . $guest_data['registration_id'] ); ?>" data-id="<?php echo esc_attr( $guest_data['registration_id'] ); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php esc_html_e( 'Print', 'staylodgic' ); ?></button>
						<button data-file="registration-<?php echo esc_attr( $guest_data['registration_id'] ); ?>" data-id="<?php echo esc_attr( $guest_data['registration_id'] ); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php esc_html_e( 'Save PDF', 'staylodgic' ); ?></button>
					</div>
				</div>
	
				<div class="invoice-container" data-bookingnumber="<?php echo esc_attr( $guest_data['registration_id'] ); ?>">
					<div class="invoice-container-inner">
						<div id="invoice-hotel-header">
							<section id="invoice-hotel-logo">
								<img class="invoice-logo" src="<?php echo esc_url( $hotel_logo ); ?>" width="<?php echo esc_attr( $property_logo_width ) . 'px'; ?>" height="auto" />
							</section>
							<section id="invoice-info">
								<p><?php echo esc_html( $hotel_header ); ?></p>
								<p><?php esc_html_e( 'Booking Reference:', 'staylodgic' ); ?> <?php echo esc_html( $stay_booking_number ); ?></p>
								<p><?php esc_html_e( 'Date:', 'staylodgic' ); ?> <?php echo esc_html( $stay_current_date ); ?></p>
								<p class="invoice-booking-status"><?php esc_html_e( 'Guest registration', 'staylodgic' ); ?></p>
							</section>
						</div>
						<section id="invoice-hotel-info">
							<p><strong><?php echo esc_html( $hotel_name ); ?></strong></p>
							<p><?php echo esc_html( $hotel_address ); ?></p>
							<p><?php echo esc_html( $hotel_phone ); ?></p>
						</section>
						<section id="invoice-customer-info">
							<h2 id="invoice-subheading"><?php esc_html_e( 'Registration:', 'staylodgic' ); ?></h2>
							<div class="invoice-customer-registration">
								<?php
								foreach ( $guest_data as $info_key => $info_value ) {
									if ( 'registration_id' !== $info_key ) {
										if ( 'countries' === $info_key ) {
											$info_value['value'] = staylodgic_country_list( 'display', $info_value['value'] );
										}
										if ( 'checkbox' === $info_value['type'] && 'true' === $info_value['value'] ) {
											$info_value['value'] = 'Yes';
										}
										if ( 'datetime-local' === $info_value['type'] ) {
											$date                = new \DateTime( $info_value['value'] );
											$formatted_date      = $date->format( 'l, F j, Y g:i A' );
											$info_value['value'] = $formatted_date;
										}

										echo '<p class="type-container" data-type="' . esc_attr( $info_value['type'] ) . '" data-id="' . esc_attr( $info_key ) . '"><strong><span class="registration-label">' . esc_html( $info_value['label'] ) . ':</span></strong> <span class="registration-data">' . esc_html( $info_value['value'] ) . '</span></p>';
									}
								}

								if ( isset( $guest_data['registration_id'] ) ) {
									$registration_id = $guest_data['registration_id'];
									$upload_dir      = wp_upload_dir();
									$signature_url   = esc_url( $upload_dir['baseurl'] . '/signatures/' . $registration_id . '.png' );

									echo '<img class="registration-signature" src="' . esc_url( $signature_url ) . '" alt="' . esc_attr__( 'Signature', 'staylodgic' ) . '">';
								}
								?>
							</div>
						</section>
					</div>
					<footer>
						<div class="invoice-footer"><?php echo wp_kses_post( $hotel_footer ); ?></div>
					</footer>
				</div>
				<?php
				echo '<div class="registration-delete-container"><button class="button button-primary paper-document-button registration-button delete-registration" data-guest-id="' . esc_attr( $guest_id ) . '">' . esc_html__( 'Delete this registration', 'staylodgic' ) . '</button></div>';
			}

			echo '<div id="deleteConfirmationModal" class="staylodgic-modal" style="display: none;">';
			echo '<div class="staylodgic-modal-content">';
			echo '<h4>' . esc_html__( 'Confirm Deletion', 'staylodgic' ) . '</h4>';
			echo '<p>' . esc_html__( 'Are you sure you want to delete this registration?', 'staylodgic' ) . '</p>';
			echo '<button class="button button-primary" id="confirmDelete">' . esc_html__( 'Delete', 'staylodgic' ) . '</button>';
			echo '<button class="button button-secondary" id="cancelDelete">' . esc_html__( 'Cancel', 'staylodgic' ) . '</button>';
			echo '</div>';
			echo '</div>';

			return ob_get_clean(); // Return the buffered content
		} else {
			return null;
		}
	}

	/**
	 * Method save_guestregistration_data
	 *
	 * @return void
	 */
	public function save_guestregistration_data() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'staylodgic-nonce-search' ) ) {
			wp_die( esc_html__( 'Security check failed', 'staylodgic' ) );
		}

		$post_id      = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;
		$booking_data = isset( $_POST['booking_data'] ) ? map_deep( wp_unslash( $_POST['booking_data'] ), 'sanitize_text_field' ) : array();
		if ( isset( $_POST['signature_data'] ) ) {
			// Apply sanitize_textarea_field directly to $_POST['signature_data']
			$signature_data = sanitize_textarea_field( wp_unslash( $_POST['signature_data'] ) );
		}

		$guest_id = false;
		if ( isset( $_POST['guest_id'] ) ) {
			$guest_id = sanitize_textarea_field( wp_unslash( $_POST['guest_id'] ) );
		}

		$registration_data = array();

		// Create a directory if it doesn't exist
		$upload_dir     = wp_upload_dir();
		$signatures_dir = $upload_dir['basedir'] . '/signatures';
		if ( ! file_exists( $signatures_dir ) ) {
			wp_mkdir_p( $signatures_dir );
		}

		// Decode signature data and save as PNG
		if ( strpos( $signature_data, 'data:image/png;base64,' ) === 0 ) {
			$signature_data = str_replace( 'data:image/png;base64,', '', $signature_data );
			$signature_data = str_replace( ' ', '+', $signature_data );
			// retrieving stored signature image
			$signature_data = base64_decode( $signature_data );

			if ( false === $signature_data ) {
				// Decoding base64 signature failed.
			} else {
				global $wp_filesystem;

				if ( ! function_exists( 'WP_Filesystem' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				if ( WP_Filesystem() ) {
					$registration_id = $post_id . '_' . wp_rand(); // Random number prefixed with post_id
					$file            = $signatures_dir . '/' . $registration_id . '.png';

					// Write the file using WP_Filesystem
					$file_written = $wp_filesystem->put_contents( $file, $signature_data, FS_CHMOD_FILE );

					if ( false === $file_written ) {
						// Failed to save signature file.
					} else {

						$booking_data['registration_id'] = $registration_id;
						if ( isset( $booking_data['signature_data'] ) ) {
							unset( $booking_data['signature_data'] );
						}
						if ( isset( $booking_data['signature-data'] ) ) {
							unset( $booking_data['signature-data'] );
						}
						if ( isset( $booking_data['Sign'] ) ) {
							unset( $booking_data['Sign'] );
						}
						if ( is_array( get_post_meta( $post_id, 'staylodgic_registration_data', true ) ) ) {
							$registration_data = get_post_meta( $post_id, 'staylodgic_registration_data', true );
						}
						if ( isset( $booking_data['registration_id'] ) && $guest_id ) {
							$registration_id = $guest_id;
						}
						$registration_data[ $registration_id ] = $booking_data;
						update_post_meta( $post_id, 'staylodgic_registration_data', $registration_data );

						$email_address = staylodgic_get_loggedin_user_email();
						$page_title    = get_the_title( $post_id );

						$email = new Email_Dispatcher( $email_address, 'Online Check-in: ' . $page_title );
						$email->set_html_content()->set_registration_template( $booking_data, $post_id );

						$cc = false;

						if ( $email->send( $cc ) ) {
							// Confirmation email sent successfully to the guest
						} else {
							// Failed to send the confirmation email
						}
					}
				}
			}
		}

		$registration_successful = $this->stay_registration_successful( $post_id );
		echo wp_kses( $registration_successful, staylodgic_get_guest_registration_tags() );
		wp_die();
	}

	/**
	 * Method delete_registration
	 *
	 * @return void
	 */
	public function delete_registration() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'staylodgic-nonce-admin' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'staylodgic' ) );
		}

		// Sanitize guest_id
		$guest_id = isset( $_POST['guest_id'] ) ? sanitize_textarea_field( wp_unslash( $_POST['guest_id'] ) ) : 0;
		$post_id  = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

		// Remove registration data and signature
		$registration_data = get_post_meta( $post_id, 'staylodgic_registration_data', true );

		if ( isset( $registration_data[ $guest_id ] ) ) {
			$registration_id = $registration_data[ $guest_id ]['registration_id'];
			unset( $registration_data[ $guest_id ] );
			update_post_meta( $post_id, 'staylodgic_registration_data', $registration_data );

			// Delete signature file
			$upload_dir     = wp_upload_dir();
			$signature_file = $upload_dir['basedir'] . '/signatures/' . $registration_id . '.png';

			if ( file_exists( $signature_file ) ) {
				wp_delete_file( $signature_file );
			}

			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_die(); // This is required to terminate immediately and return a proper response
	}

	/**
	 * Method get_guest_post_permalink
	 *
	 * @return void
	 */
	public function get_guest_post_permalink() {

		// Verify the nonce
		if ( ! isset( $_POST['nonce'] ) || ! check_ajax_referer( 'staylodgic-nonce-admin', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce.' ) );
			return;
		}

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		if ( $post_id ) {
			$permalink = get_permalink( $post_id );
			wp_send_json_success( $permalink );
		} else {
			wp_send_json_error( 'Post ID is invalid.' );
		}
	}

	/**
	 * Method Appends the saved shortcode to the content of staylodgic_guestrgs posts.
	 *
	 * @param $content
	 *
	 * @return void
	 */
	public function append_shortcode_to_content( $content ) {
		// Check if we are viewing a single post of type 'staylodgic_guestrgs'
		if ( is_single() && 'staylodgic_guestrgs' === get_post_type() ) {

			$registration_allowed_data = $this->allow_guest_registration( get_the_id() );

			$registration_allowed = $registration_allowed_data['allow'];

			if ( ! $registration_allowed ) {
				$content .= '<div class="guestregistry-shortcode-content">' . $registration_allowed_data['reason'] . '</div>';
			} else {
				// Retrieve saved shortcode content
				$saved_shortcode = get_option( 'staylodgic_guestregistry_shortcode', '' );

				if ( '' === $saved_shortcode ) {
					$form_gen_instance = new \Staylodgic\Form_Generator();
					$saved_shortcode   = $form_gen_instance->default_shortcodes();
				}

				$saved_shortcode = stripslashes( $saved_shortcode );
				$form_start_tag  = '<div class="registration_form_wrap">';
				$form_start_tag .= '<div class="registration_form">';
				$form_start_tag .= '<div class="registration-column registration-column-one registration_form_inputs">';
				$form_start      = '[form_start id="guestregistration" class="guest-registration" action="submission_url" method="post"]';
				$form_submit     = '[form_input type="submit" id="submitregistration" class="book-button" value="' . __( 'Save Registration', 'staylodgic' ) . '"]';
				$form_end        = '[form_end]';
				$form_end_tag    = '</div>';
				$form_end_tag   .= '<div class="registration-column registration-column-two">';
				$form_end_tag   .= '<div id="booking-summary-wrap">';
				$form_end_tag   .= '<div class="summary-section-title">' . __( 'Online Registration', 'staylodgic' ) . '</div>';
				$form_end_tag   .= '<div class="summary-section-description"><p>' . __( 'Please fill the form for Online Registration.</p><p>You can fill according to the number of guests.</p><p>You can submit a registration if you think a mistake was made in a previous one.', 'staylodgic' ) . '</p></div>';
				$form_end_tag   .= '</div>';
				$form_end_tag   .= '</div>';
				$form_end_tag   .= '</div>';
				$form_end_tag   .= '</div>';

				$final_shortcode = $form_start_tag . $form_start . $saved_shortcode . $form_submit . $form_end . $form_end_tag;

				// Append the shortcode to the original content
				$content .= '<div class="guestregistry-shortcode-content">' . do_shortcode( $final_shortcode ) . '</div>';
			}
		}

		return $content;
	}

	/**
	 * Method stay_registration_successful
	 *
	 * @param $post_id $post_id [explicite description]
	 *
	 * @return void
	 */
	public function stay_registration_successful( $post_id ) {

		// Build the success HTML with localization
		$success_html = '<div class="guest_registration_form_outer">' .
			'<div class="guest_registration_form_wrap">' .
			'<div class="guest_registration_form">' .
			'<div class="registration-successful-inner">' .
			'<h3>' . esc_html__( 'Registration Successful', 'staylodgic' ) . '</h3>' .
			'<p>Hi,</p>' .
			'<p>' . esc_html__( 'Your registration was successful.', 'staylodgic' ) . '</p>' .
			'<p><a href="' . esc_url( get_the_permalink( $post_id ) ) . '" class="book-button button-inline">' . esc_html__( 'Register another', 'staylodgic' ) . '</a></p>' .
			'</div>' .
			'</div>' .
			'</div>' .
			'</div>';

		return $success_html;
	}


	/**
	 * Method booking_data_fields
	 *
	 * @return void
	 */
	public function booking_data_fields() {
		$data_fields = array(
			'full_name'     => __( 'Full Name', 'staylodgic' ),
			'passport'      => __( 'Passport No', 'staylodgic' ),
			'email_address' => __( 'Email Address', 'staylodgic' ),
			'phone_number'  => __( 'Phone Number', 'staylodgic' ),
			'country'       => __( 'Country', 'staylodgic' ),
			'guest_consent' => __( 'By clicking "Book this Room" you agree to our terms and conditions and privacy policy.', 'staylodgic' ),
		);

		return $data_fields;
	}

	/**
	 * Method request_registration_details
	 *
	 * @param $booking_number
	 *
	 * @return void
	 */
	public function request_registration_details( $booking_number ) {
		// Verify the nonce
		if ( ! isset( $_POST['staylodgic_bookingdetails_nonce'] ) || ! check_admin_referer( 'staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce' ) ) {
			// Nonce verification failed; handle the error or reject the request
			// For example, you can return an error response
			wp_send_json_error( array( 'message' => 'Failed' ) );
			return;
		}

		$booking_number = isset( $_POST['booking_number'] ) ? sanitize_text_field( wp_unslash( $_POST['booking_number'] ) ) : '';

		// Fetch reservation details
		$reservation_instance   = new \Staylodgic\Reservations();
		$stay_reservation_query = $reservation_instance->get_reservationfor_booking( $booking_number );

		ob_start(); // Start output buffering
		echo "<div class='element-container-group'>";
		if ( $stay_reservation_query->have_posts() ) {

			echo "<div class='reservation-details'>";
			while ( $stay_reservation_query->have_posts() ) {
				$stay_reservation_query->the_post();
				$stay_reservation_id = get_the_ID();

				// Display reservation details
				echo '<h3>' . esc_html__( 'Reservation ID:', 'staylodgic' ) . ' ' . esc_html( $stay_reservation_id ) . '</h3>';
				echo '<p>' . esc_html__( 'Check-in Date:', 'staylodgic' ) . ' ' . esc_html( get_post_meta( $stay_reservation_id, 'staylodgic_checkin_date', true ) ) . '</p>';
				echo '<p>' . esc_html__( 'Check-out Date:', 'staylodgic' ) . ' ' . esc_html( get_post_meta( $stay_reservation_id, 'staylodgic_checkout_date', true ) ) . '</p>';
				// Add other reservation details as needed
			}
			echo '</div>';
		} else {
			echo '<p>' . esc_html__( 'No reservation found for Booking Number:', 'staylodgic' ) . ' ' . esc_html( $booking_number ) . '</p>';
		}

		// Fetch guest details
		$stay_guest_id = $reservation_instance->get_guest_id_for_reservation( $booking_number );
		if ( $stay_guest_id ) {
			echo "<div class='guest-details'>";

			$registry_instance = new \Staylodgic\Guest_Registry();
			$res_reg_ids       = $registry_instance->fetch_res_reg_ids_by_booking_number( $booking_number );
			if ( $res_reg_ids ) {
				$guest_registration_url = get_permalink( $res_reg_ids['guest_register_id'] );
				echo '<a href="' . esc_url( $guest_registration_url ) . '" class="book-button button-inline">' . esc_html__( 'Proceed to register', 'staylodgic' ) . '</a>';
			}
			// Add other guest details as needed
			echo '</div>';
		} else {
			echo '<p>' . esc_html__( 'No guest details found for Booking Number:', 'staylodgic' ) . ' ' . esc_html( $booking_number ) . '</p>';
		}
		echo '</div>';

		$information_sheet = ob_get_clean(); // Get the buffer content and clean the buffer
		echo wp_kses( $information_sheet, staylodgic_get_guest_registration_tags() );
		wp_die(); // Terminate and return a proper response
	}

	/**
	 * Method stay_guest_registration
	 *
	 * @return void
	 */
	public function stay_guest_registration() {
		ob_start();
		$staylodgic_bookingdetails_nonce = wp_create_nonce( 'staylodgic-bookingdetails-nonce' );
		?>
		<div class="staylodgic-content">
			<div id="hotel-booking-form">
				<div class="front-booking-search">
					<div class="front-booking-number-wrap">
						<div class="front-booking-number-container">
							<div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
								<input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr( $staylodgic_bookingdetails_nonce ); ?>" />
								<input placeholder="<?php echo esc_html__( 'Booking No.', 'staylodgic' ); ?>" type="text" class="form-control" id="booking_number" name="booking_number" required>
								<label for="booking_number" class="control-label"><?php echo esc_html__( 'Booking No.', 'staylodgic' ); ?></label>
							</div>
						</div>
						<div data-request="guestregistration" id="booking_details" class="form-search-button"><?php echo esc_html__( 'Search', 'staylodgic' ); ?></div>
					</div>
				</div>

				<div class="guestregistration-details-lister">
					<div id="guestregistration-details-ajax"></div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}

$instance = new \Staylodgic\Guest_Registry();
