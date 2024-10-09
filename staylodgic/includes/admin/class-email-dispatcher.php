<?php

namespace Staylodgic;

class Email_Dispatcher {


	private $to;
	private $subject;
	private $message;
	private $headers;
	private $attachments;

	public function __construct( $to, $subject ) {
		$this->to          = $to;
		$this->subject     = $subject;
		$this->headers     = array();
		$this->attachments = array();
	}

	/**
	 * Method set_html_content
	 *
	 * @return void
	 */
	public function set_html_content() {
		$this->headers[] = 'Content-Type: text/html; charset=UTF-8';
		return $this;
	}

	/**
	 * Method add_attachment
	 *
	 * @param $path
	 *
	 * @return void
	 */
	public function add_attachment( $path ) {
		$this->attachments[] = $path;
		return $this;
	}

	/**
	 * Method set_registration_template
	 *
	 * @param $registration_data
	 * @param $registration_post_id
	 *
	 * @return void
	 */
	public function set_registration_template( $registration_data, $registration_post_id ) {

		$email_message = '';

		if ( is_array( $registration_data ) && ! empty( $registration_data ) ) {
			foreach ( $registration_data as $info_key => $info_value ) {
				// Skip the registration_id in the inner loop since it's handled separately
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

					$email_message .= '<strong><span class="registration-label">' . esc_html( $info_value['label'] ) . ':</span></strong> <span class="registration-data">' . esc_html( $info_value['value'] ) . '</span></p>';
				} elseif ( isset( $guest_data['registration_id'] ) ) {
						$registration_id = $guest_data['registration_id'];
						$email_message  .= '<strong><span class="registration-label">Registration ID:</span></strong> <span class="registration-data">' . esc_html( $registration_id ) . '</span></p>';
				}
			}
			$post_edit_link         = get_edit_post_link( $registration_post_id );
			$registry_bookingnumber = get_post_meta( $registration_post_id, 'staylodgic_registry_bookingnumber', true );

			$guest_registry = new Guest_Registry();
			$res_reg_ids    = $guest_registry->fetch_res_reg_ids_by_booking_number( $registry_bookingnumber );

			$stay_reservation_id = $res_reg_ids['stay_reservation_id'];
			$register_id         = $res_reg_ids['guest_register_id'];

			$reservation_id_edit_link = get_edit_post_link( $stay_reservation_id );

			$email_message .= '<p><a href="' . esc_url( $post_edit_link ) . '">' . __( 'View Guest Registration', 'staylodgic' ) . '</a></p>';
			$email_message .= '<p><a href="' . esc_url( $reservation_id_edit_link ) . '">' . __( 'View Booking', 'staylodgic' ) . '</a></p>';
		}

		$this->message = $email_message;

		return $this;
	}

	/**
	 * Method set_booking_confirmation_template
	 *
	 * @param $booking_details
	 *
	 * @return void
	 */
	public function set_booking_confirmation_template( $booking_details ) {

		$total_price                    = staylodgic_price( $booking_details['stay_total_cost'] );
		$property_emailfooter           = staylodgic_get_option( 'property_emailfooter' );
		$property_emailfooter_formatted = nl2br( $property_emailfooter );

		$email_message  = '<h1>' . __( 'Thank you for your reservation', 'staylodgic' ) . ', ' . esc_html( $booking_details['guestName'] ) . '</h1>';
		$email_message .= '<p>' . __( 'We have recieved your booking.', 'staylodgic' ) . '</p>';
		$email_message .= '<h2>' . __( 'Booking Details', 'staylodgic' ) . '</h2>';
		$email_message .= '<p><strong>' . __( 'Booking Number:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_booking_number'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Name:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['guestName'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Room:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['roomTitle'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Meal Plan:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['mealplan'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Included Meal Plans:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['included_mealplan'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Check-in Date:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_checkin_date'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Check-out Date:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_checkout_date'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Adults:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_adult_guests'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Children:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_children_guests'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Subtotal:', 'staylodgic' ) . '</strong> ' . $booking_details['subtotal'] . '</p>';
		if ( $booking_details['tax'] ) {
			$email_message .= '<p><strong>' . __( 'Tax:', 'staylodgic' ) . '</strong></p>';
			foreach ( $booking_details['tax'] as $total_id => $totalvalue ) {
				$email_message .= '<p>' . wp_kses( $totalvalue, staylodgic_get_allowed_tags() ) . '</p>';
			}
		}
		$email_message .= '<p><strong>' . __( 'Total Cost:', 'staylodgic' ) . '</strong> ' . $total_price . '</p>';
		$email_message .= '<p>' . __( 'We look forward to welcoming you and ensuring a pleasant stay.', 'staylodgic' ) . '</p>';
		$email_message .= '<p>' . __( 'Please contact us to cancel, modify or if there are any questions regarding the booking.', 'staylodgic' ) . '</p>';
		$email_message .= '<p>' . $property_emailfooter_formatted . '</p>';

		$this->message = $email_message;
		return $this;
	}

	/**
	 * Method set_activity_confirmation_template
	 *
	 * @param $booking_details
	 *
	 * @return void
	 */
	public function set_activity_confirmation_template( $booking_details ) {

		$total_price                    = staylodgic_price( $booking_details['stay_total_cost'] );
		$activity_emailfooter           = staylodgic_get_option( 'activity_property_emailfooter' );
		$activity_emailfooter_formatted = nl2br( $activity_emailfooter );

		$email_message  = '<h1>' . __( 'Thank you for your reservation', 'staylodgic' ) . ', ' . esc_html( $booking_details['guestName'] ) . '</h1>';
		$email_message .= '<p>' . __( 'We have recieved your booking.', 'staylodgic' ) . '</p>';
		$email_message .= '<h2>' . __( 'Booking Details', 'staylodgic' ) . '</h2>';
		$email_message .= '<p><strong>' . __( 'Booking Number:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_booking_number'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Name:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['guestName'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Activity Name:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['roomTitle'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Activity Date:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_checkin_date'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Adults:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_adult_guests'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Children:', 'staylodgic' ) . '</strong> ' . esc_html( $booking_details['stay_children_guests'] ) . '</p>';
		$email_message .= '<p><strong>' . __( 'Subtotal:', 'staylodgic' ) . '</strong> ' . $booking_details['subtotal'] . '</p>';
		if ( $booking_details['tax'] ) {
			$email_message .= '<p><strong>' . __( 'Tax:', 'staylodgic' ) . '</strong></p>';
			foreach ( $booking_details['tax'] as $total_id => $totalvalue ) {
				$email_message .= '<p>' . wp_kses( $totalvalue, staylodgic_get_allowed_tags() ) . '</p>';
			}
		}
		$email_message .= '<p><strong>' . __( 'Total Cost:', 'staylodgic' ) . '</strong> ' . $total_price . '</p>';
		$email_message .= '<p>' . __( 'Thank you for choosing our services.', 'staylodgic' ) . '</p>';
		$email_message .= '<p>' . __( 'Should you need any further information or wish to make specific arrangements, please feel free to contact us. We are here to assist you!', 'staylodgic' ) . '</p>';
		$email_message .= '<p>' . $activity_emailfooter_formatted . '</p>';

		$this->message = $email_message;
		return $this;
	}

	/**
	 * Method send
	 *
	 * @param $cc
	 *
	 * @return void
	 */
	public function send( $cc = true ) {
		if ( $cc ) {

			$cc_email = staylodgic_get_loggedin_user_email();
			if ( $cc_email ) {
				$this->headers[] = 'Cc: ' . $cc_email;
			}
		}

		// Ensure the content type is set to HTML
		$this->headers[] = 'Content-Type: text/html; charset=UTF-8';

		// Add the font styling to the message
		$font_family   = 'font-family: Helvetica, Arial, sans-serif;';
		$this->message = '<div style="' . $font_family . '">' . $this->message . '</div>';

		// Convert headers array to string format for wp_mail
		$headers_string = implode( "\r\n", $this->headers );

		return wp_mail( $this->to, $this->subject, $this->message, $headers_string, $this->attachments );
	}
}
