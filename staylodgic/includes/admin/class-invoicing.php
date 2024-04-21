<?php

namespace Staylodgic;

class Invoicing
{

    private $reservationID;
    private $bookingStatus;
    private $bookingNumber;
    private $numberDays;
    private $hotelLogo;
    private $hotelName;
    private $hotelHeader;
    private $hotelAddress;
    private $hotelPhone;
    private $customerName;
    private $customerEmail;
    private $checkInDate;
    private $checkOutDate;
    private $roomType;
    private $numberOfGuests;
    private $numberofAdults;
    private $numberofChildren;
    private $roomPrice;
    private $subTotal;
    private $taxesAndFees;
    private $totalAmount;
    private $hotelFooter;

    public function __construct(
        $reservationID = null,
        $bookingStatus = null,
        $bookingNumber = null,
        $numberDays = null,
        $hotelLogo = null,
        $hotelName = null,
        $hotelHeader = null,
        $hotelAddress = null,
        $hotelPhone = null,
        $customerName = null,
        $customerEmail = null,
        $checkInDate = null,
        $checkOutDate = null,
        $roomType = null,
        $numberOfGuests = null,
        $numberofAdults = null,
        $numberofChildren = null,
        $roomPrice = null,
        $subTotal = null,
        $taxesAndFees = null,
        $totalAmount = null,
        $hotelFooter = null
    ) {
        $this->reservationID    = $reservationID;
        $this->bookingStatus    = $bookingStatus;
        $this->bookingNumber    = $bookingNumber;
        $this->numberDays    = $numberDays;
        $this->hotelLogo        = $hotelLogo;
        $this->hotelName        = $hotelName;
        $this->hotelHeader      = $hotelHeader;
        $this->hotelAddress     = $hotelAddress;
        $this->hotelPhone       = $hotelPhone;
        $this->customerName     = $customerName;
        $this->customerEmail    = $customerEmail;
        $this->checkInDate      = $checkInDate;
        $this->checkOutDate     = $checkOutDate;
        $this->roomType         = $roomType;
        $this->numberOfGuests   = $numberOfGuests;
        $this->numberofAdults   = $numberofAdults;
        $this->numberofChildren = $numberofChildren;
        $this->roomPrice        = $roomPrice;
        $this->roomPrice        = $subTotal;
        $this->taxesAndFees     = $taxesAndFees;
        $this->totalAmount      = $totalAmount;
        $this->hotelFooter      = $hotelFooter;

        add_action('admin_menu', array($this, 'add_invoicing_admin_menu')); // This now points to the add_admin_menu function
        add_action('admin_menu', array($this, 'add_activity_invoicing_admin_menu')); // This now points to the add_admin_menu function

        add_action('wp_ajax_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));
        add_action('wp_ajax_nopriv_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));

        add_action('wp_ajax_getInvoiceActivityDetails', array($this, 'getInvoiceActivityDetails'));
        add_action('wp_ajax_nopriv_getInvoiceActivityDetails', array($this, 'getInvoiceActivityDetails'));

    }

    public function add_invoicing_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=slgc_reservations', // Set the parent slug to your custom post type slug
            'Invoices',
            'Invoices',
            'manage_options',
            'staylodgic-invoicing',
            array($this, 'booking_invoices')
        );
    }
    public function add_activity_invoicing_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=slgc_activityres', // Set the parent slug to your custom post type slug
            'Invoices',
            'Invoices',
            'manage_options',
            'staylodgic-activity-invoicing',
            array($this, 'activity_invoices')
        );
    }

    public function activity_invoices()
    {

        echo "<h1>Activity Invoices</h1>";
        echo '<div class="admin-staylodgic-content">';

        echo self::activityBooking_Search();

        echo '</div>';

    }
    public function booking_invoices()
    {

        echo "<h1>Invoices</h1>";
        echo '<div class="admin-staylodgic-content">';

        echo self::hotelBooking_Search();

        echo '</div>';

    }

    public function getInvoiceActivityDetails()
    {

        $booking_number = $_POST[ 'booking_number' ];

        // Fetch reservation details
        $reservations_instance = new \Staylodgic\Activity();
        $reservationID         = $reservations_instance->getActivityIDforBooking($booking_number);

        $reservations_instance = new \Staylodgic\Activity($date = false, $activity_id = false, $reservationID);
        // Verify the nonce
        if (!isset($_POST[ 'staylodgic_bookingdetails_nonce' ]) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        // Hotel Information
        $property_logo_id = staylodgic_get_option('activity_property_logo');
        $property_name    = staylodgic_get_option('activity_property_name');
        $property_phone   = staylodgic_get_option('activity_property_phone');
        $property_address = staylodgic_get_option('activity_property_address');
        $property_header  = staylodgic_get_option('activity_property_header');
        $property_footer  = staylodgic_get_option('activity_property_footer');

        $this->reservationID    = $reservationID;
        $this->hotelName    = $property_name;
        $this->hotelPhone   = $property_phone;
        $this->hotelAddress = $property_address;
        $this->hotelHeader  = $property_header;
        $this->hotelFooter  = $property_footer;
        $this->hotelLogo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

        if ($reservationID) {
            $this->bookingNumber = $booking_number;
            $this->checkInDate   = get_post_meta($reservationID, 'staylodgic_checkin_date', true);

            $adults = get_post_meta($reservationID, 'staylodgic_reservation_activity_adults', true);

            $children = array();
            $children = get_post_meta($reservationID, 'staylodgic_reservation_activity_children', true);
            error_log('children array');
            error_log( print_r( $children, true ));
            $this->numberofAdults   = $adults;

            $totalGuests            = intval($adults);
            
            if ( isset( $children[ 'number' ] )) {
                $this->numberofChildren = $children[ 'number' ];
                $totalGuests            += intval( $children[ 'number' ]);
            }
            
            $this->numberOfGuests   = $totalGuests;
            
            $this->bookingStatus = 'Booking Pending';
            if ( $reservations_instance->isConfirmed_Reservation($reservationID) ) {
                $this->bookingStatus = 'Booking Confirmed';
            }
            $this->roomType = $reservations_instance->getActivityNameForReservation($reservationID);
            // Add other reservation details as needed

            $taxStatus = get_post_meta($reservationID, 'staylodgic_tax', true);
            $taxHTML   = get_post_meta($reservationID, 'staylodgic_tax_html_data', true);
            $taxData   = get_post_meta($reservationID, 'staylodgic_tax_data', true);

            $tax_summary = '<div id="input-tax-summary">';
            $tax_summary .= '<div class="input-tax-summary-wrap">';
            if ('enabled' == $taxStatus) {
                $tax_summary .= '<div class="input-tax-summary-wrap-inner">';
                $tax_summary .= $taxHTML;
                error_log('------ tax out -------');
                error_log(print_r($taxHTML, true));
                $tax_summary .= '</div>';
            }
            $tax_summary .= '</div>';
            $tax_summary .= '</div>';

            $this->taxesAndFees = $tax_summary;

            $ratePerPerson = get_post_meta($reservationID, 'staylodgic_reservation_rate_per_person', true);
            $subTotal     = get_post_meta($reservationID, 'staylodgic_reservation_subtotal_activity_cost', true);
            $totalAmount  = get_post_meta($reservationID, 'staylodgic_reservation_total_room_cost', true);

            $this->roomPrice   = $ratePerPerson;
            $this->subTotal    = $subTotal;
            $this->totalAmount = $totalAmount;

            // Fetch guest details
            $guestID = $reservations_instance->getGuest_id_forReservation($booking_number);
            if ($guestID) {
                $this->customerName  = get_post_meta($guestID, 'staylodgic_full_name', true);
                $this->customerEmail = get_post_meta($guestID, 'staylodgic_email_address', true);
            }

        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }

        $informationSheet = $this->invoiceActivityTemplate(
            $this->reservationID,
            $this->bookingStatus,
            $this->bookingNumber,
            $this->numberDays,
            $this->hotelLogo,
            $this->hotelName,
            $this->hotelHeader,
            $this->hotelAddress,
            $this->hotelPhone,
            $this->customerName,
            $this->customerEmail,
            $this->checkInDate,
            $this->checkOutDate,
            $this->roomType,
            $this->numberOfGuests,
            $this->numberofAdults,
            $this->numberofChildren,
            $this->roomPrice,
            $this->subTotal,
            $this->taxesAndFees,
            $this->totalAmount,
            $this->hotelFooter
        );
        echo $informationSheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }

    public function getInvoiceBookingDetails()
    {

        $booking_number = $_POST[ 'booking_number' ];

        // Fetch reservation details
        $reservations_instance = new \Staylodgic\Reservations();
        $reservationID         = $reservations_instance->getReservationIDforBooking($booking_number);

        $reservations_instance = new \Staylodgic\Reservations($date = false, $room_id = false, $reservationID);
        // Verify the nonce
        if (!isset($_POST[ 'staylodgic_bookingdetails_nonce' ]) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }

        // Hotel Information
        $property_logo_id = staylodgic_get_option('property_logo');
        $property_name    = staylodgic_get_option('property_name');
        $property_phone   = staylodgic_get_option('property_phone');
        $property_address = staylodgic_get_option('property_address');
        $property_header  = staylodgic_get_option('property_header');
        $property_footer  = staylodgic_get_option('property_footer');

        $this->reservationID    = $reservationID;
        $this->hotelName    = $property_name;
        $this->hotelPhone   = $property_phone;
        $this->hotelAddress = $property_address;
        $this->hotelHeader  = $property_header;
        $this->hotelFooter  = $property_footer;
        $this->hotelLogo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

        if ($reservationID) {
            $this->bookingNumber = $booking_number;
            $this->checkInDate   = get_post_meta($reservationID, 'staylodgic_checkin_date', true);
            $this->checkOutDate  = get_post_meta($reservationID, 'staylodgic_checkout_date', true);

            $adults = get_post_meta($reservationID, 'staylodgic_reservation_room_adults', true);

            $children = array();
            $children = get_post_meta($reservationID, 'staylodgic_reservation_room_children', true);
            error_log('children array');
            error_log( print_r( $children, true ));
            $this->numberofAdults   = $adults;

            $totalGuests            = intval($adults);
            
            if ( isset( $children[ 'number' ] )) {
                $this->numberofChildren = $children[ 'number' ];
                $totalGuests            += intval( $children[ 'number' ]);
            }
            
            $this->numberOfGuests   = $totalGuests;
            
            $this->bookingStatus = 'Booking Pending';
            if ( $reservations_instance->isConfirmed_Reservation($reservationID) ) {
                $this->bookingStatus = 'Booking Confirmed';
            }
            $this->roomType = $reservations_instance->getRoomNameForReservation($reservationID);
            $this->numberDays = $reservations_instance->countReservationDays($reservationID);
            // Add other reservation details as needed

            $taxStatus = get_post_meta($reservationID, 'staylodgic_tax', true);
            $taxHTML   = get_post_meta($reservationID, 'staylodgic_tax_html_data', true);
            $taxData   = get_post_meta($reservationID, 'staylodgic_tax_data', true);

            $tax_summary = '<div id="input-tax-summary">';
            $tax_summary .= '<div class="input-tax-summary-wrap">';
            if ('enabled' == $taxStatus) {
                $tax_summary .= '<div class="input-tax-summary-wrap-inner">';
                $tax_summary .= $taxHTML;
                error_log('------ tax out -------');
                error_log(print_r($taxHTML, true));
                $tax_summary .= '</div>';
            }
            $tax_summary .= '</div>';
            $tax_summary .= '</div>';

            $this->taxesAndFees = $tax_summary;

            $ratePerNight = get_post_meta($reservationID, 'staylodgic_reservation_rate_per_night', true);
            $subTotal     = get_post_meta($reservationID, 'staylodgic_reservation_subtotal_room_cost', true);
            $totalAmount  = get_post_meta($reservationID, 'staylodgic_reservation_total_room_cost', true);

            $this->roomPrice   = $ratePerNight;
            $this->subTotal    = $subTotal;
            $this->totalAmount = $totalAmount;

            // Fetch guest details
            $guestID = $reservations_instance->getGuest_id_forReservation($booking_number);
            if ($guestID) {
                $this->customerName  = get_post_meta($guestID, 'staylodgic_full_name', true);
                $this->customerEmail = get_post_meta($guestID, 'staylodgic_email_address', true);
            }

        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }

        $informationSheet = $this->invoiceTemplate(
            $this->reservationID,
            $this->bookingStatus,
            $this->bookingNumber,
            $this->numberDays,
            $this->hotelLogo,
            $this->hotelName,
            $this->hotelHeader,
            $this->hotelAddress,
            $this->hotelPhone,
            $this->customerName,
            $this->customerEmail,
            $this->checkInDate,
            $this->checkOutDate,
            $this->roomType,
            $this->numberOfGuests,
            $this->numberofAdults,
            $this->numberofChildren,
            $this->roomPrice,
            $this->subTotal,
            $this->taxesAndFees,
            $this->totalAmount,
            $this->hotelFooter
        );
        echo $informationSheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }

    public function activityBooking_Search()
    {
        ob_start();
        $staylodgic_bookingdetails_nonce = wp_create_nonce('staylodgic-bookingdetails-nonce');
        ?>

            <div id="hotel-booking-form">

                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr($staylodgic_bookingdetails_nonce); ?>" />
                                <input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label">Booking No.</label>
                            </div>
                        </div>
                        <div id="invoiceActivityDetails" class="form-search-button">Search</div>
                    </div>
                </div>

			<div class="booking-details-lister">
				<div id="booking-details-ajax"></div>
			</div>
		</div>
		<?php
return ob_get_clean();
    }


    public function hotelBooking_Search()
    {
        ob_start();
        $staylodgic_bookingdetails_nonce = wp_create_nonce('staylodgic-bookingdetails-nonce');
        ?>

            <div id="hotel-booking-form">

                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="staylodgic_bookingdetails_nonce" value="<?php echo esc_attr($staylodgic_bookingdetails_nonce); ?>" />
                                <input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label">Booking No.</label>
                            </div>
                        </div>
                        <div id="invoiceDetails" class="form-search-button">Search</div>
                    </div>
                </div>

			<div class="booking-details-lister">
				<div id="booking-details-ajax"></div>
			</div>
		</div>
		<?php
return ob_get_clean();
    }

    public function invoiceActivityTemplate(
        $reservationID,
        $bookingStatus,
        $bookingNumber,
        $numberDays,
        $hotelLogo,
        $hotelName,
        $hotelHeader,
        $hotelAddress,
        $hotelPhone,
        $customerName,
        $customerEmail,
        $checkInDate,
        $checkOutDate,
        $roomType,
        $numberOfGuests,
        $numberofAdults,
        $numberofChildren,
        $roomPrice,
        $subTotal,
        $taxesAndFees,
        $totalAmount,
        $hotelFooter
    ) {
        $currentDate = date('F jS, Y'); // Outputs: January 1st, 2024
        ob_start();
        ?>
        <div class="invoice-container-buttons">
        <button data-title="Guest Registration <?php echo $bookingNumber; ?>" data-id="<?php echo $bookingNumber; ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button">Print Invoice</button>
        <button data-file="registration-<?php echo $bookingNumber; ?>" data-id="<?php echo $bookingNumber; ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button">Save PDF</button>
        </div>
        <div class="invoice-container" data-bookingnumber="<?php echo $bookingNumber; ?>">
        <div class="invoice-container-inner">
        <div id="invoice-hotel-header">
            <section id="invoice-hotel-logo">
                <img class="invoice-logo" src="<?php echo $hotelLogo; ?>" />
            </section>
            <section id="invoice-info">
                <p><?php echo $hotelHeader; ?></p>
                <p>Invoice No: <?php echo $bookingNumber . '-' . $reservationID; ?></p>
                <p>Invoice Date: <?php echo $currentDate; ?></p>
                <p class="invoice-booking-status"><?php echo $bookingStatus; ?></p>
            </section>
        </div>
        <section id="invoice-hotel-info">
                <p><strong><?php echo $hotelName; ?></strong></p>
                <p><?php echo $hotelAddress; ?></p>
                <p><?php echo $hotelPhone; ?></p>
        </section>
        <section id="invoice-customer-info">
            <h2>Bill to:</h2>
            <p>Name: <?php echo $customerName; ?></p>
            <p>Email: <?php echo $customerEmail; ?></p>
        </section>

        <div id="invoice-booking-information">

        <section id="invoice-booking-details">
            <h2>Activity Booking Details</h2>
            <p><span>Booking No:</span><?php echo $bookingNumber; ?></p>
            <p><span>Activity Date:</span><?php echo $checkInDate; ?></p>
            <p><span>Activity Type:</span><?php echo $roomType; ?></p>
            <p><span>Adults:</span><?php echo $numberofAdults; ?></p>
            <?php
            if ($numberofChildren > 0) {
            ?>
            <p><span>Children:</span><?php echo $numberofChildren; ?></p>
            <?php
            }
        ?>
        </section>

        <section id="invoice-booking-pricing">
            <h2>Activity Price</h2>
            <p class="nightly-rate-info"><span class="nightly-rate"><?php echo staylodgic_price($roomPrice); ?></span><span class="nights"> x <?php echo $numberDays; ?> Per Person</span></p>
            <?php
        $reservations_instance = new \Staylodgic\Activity();
        $reservationID         = $reservations_instance->getActivityIDforBooking($bookingNumber);
        $taxStatus             = get_post_meta($reservationID, 'staylodgic_tax', true);
        if ('enabled' == $taxStatus) {
            ?>
            <div class="subtotal-info"><p class="subtotal">Sub Total:</p><p><?php echo staylodgic_price($subTotal); ?></p></div>
            <p>Taxes and Fees: <?php echo $taxesAndFees; ?></p>
            <?php
        }
        ?>
            <div class="invoice-total"><strong>Total Amount:</p><p><?php echo staylodgic_price($totalAmount); ?></strong></div>
        </section>
        </div>

        </div>
        <footer>
            <div class="invoice-footer"><?php echo $hotelFooter; ?></div>
        </footer>
        </div>
        <?php
return ob_get_clean();
    }

    public function invoiceTemplate(
        $reservationID,
        $bookingStatus,
        $bookingNumber,
        $numberDays,
        $hotelLogo,
        $hotelName,
        $hotelHeader,
        $hotelAddress,
        $hotelPhone,
        $customerName,
        $customerEmail,
        $checkInDate,
        $checkOutDate,
        $roomType,
        $numberOfGuests,
        $numberofAdults,
        $numberofChildren,
        $roomPrice,
        $subTotal,
        $taxesAndFees,
        $totalAmount,
        $hotelFooter
    ) {
        $currentDate = date('F jS, Y'); // Outputs: January 1st, 2024
        ob_start();
        ?>
        <div class="invoice-container-buttons">
        <button data-title="Guest Registration <?php echo $bookingNumber; ?>" data-id="<?php echo $bookingNumber; ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button">Print Invoice</button>
        <button data-file="registration-<?php echo $bookingNumber; ?>" data-id="<?php echo $bookingNumber; ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button">Save PDF</button>
        </div>
        <div class="invoice-container" data-bookingnumber="<?php echo $bookingNumber; ?>">
        <div class="invoice-container-inner">
        <div id="invoice-hotel-header">
            <section id="invoice-hotel-logo">
                <img class="invoice-logo" src="<?php echo $hotelLogo; ?>" />
            </section>
            <section id="invoice-info">
                <p><?php echo $hotelHeader; ?></p>
                <p>Invoice No: <?php echo $bookingNumber . '-' . $reservationID; ?></p>
                <p>Invoice Date: <?php echo $currentDate; ?></p>
                <p class="invoice-booking-status"><?php echo $bookingStatus; ?></p>
            </section>
        </div>
        <section id="invoice-hotel-info">
                <p><strong><?php echo $hotelName; ?></strong></p>
                <p><?php echo $hotelAddress; ?></p>
                <p><?php echo $hotelPhone; ?></p>
        </section>
        <section id="invoice-customer-info">
            <h2>Bill to:</h2>
            <p>Name: <?php echo $customerName; ?></p>
            <p>Email: <?php echo $customerEmail; ?></p>
        </section>

        <div id="invoice-booking-information">

        <section id="invoice-booking-details">
            <h2>Booking Details</h2>
            <p><span>Booking No:</span><?php echo $bookingNumber; ?></p>
            <p><span>Check-in Date:</span><?php echo $checkInDate; ?></p>
            <p><span>Check-out Date:</span><?php echo $checkOutDate; ?></p>
            <p><span>Room Type:</span><?php echo $roomType; ?></p>
            <p><span>Adults:</span><?php echo $numberofAdults; ?></p>
            <?php
            if ($numberofChildren > 0) {
            ?>
            <p><span>Children:</span><?php echo $numberofChildren; ?></p>
            <?php
            }
        ?>
        </section>

        <section id="invoice-booking-pricing">
            <h2>Room Price</h2>
            <p class="nightly-rate-info"><span class="nightly-rate"><?php echo staylodgic_price($roomPrice); ?></span><span class="nights"> x <?php echo $numberDays; ?> Nights</span></p>
            <?php
        $reservations_instance = new \Staylodgic\Reservations();
        $reservationID         = $reservations_instance->getReservationIDforBooking($bookingNumber);
        $taxStatus             = get_post_meta($reservationID, 'staylodgic_tax', true);
        if ('enabled' == $taxStatus) {
            ?>
            <div class="subtotal-info"><p class="subtotal">Sub Total:</p><p><?php echo staylodgic_price($subTotal); ?></p></div>
            <p>Taxes and Fees: <?php echo $taxesAndFees; ?></p>
            <?php
        }
        ?>
            <div class="invoice-total"><strong>Total Amount:</p><p><?php echo staylodgic_price($totalAmount); ?></strong></div>
        </section>
        </div>

        </div>
        <footer>
            <div class="invoice-footer"><?php echo $hotelFooter; ?></div>
        </footer>
        </div>
        <?php
return ob_get_clean();
    }

}

$instance = new \Staylodgic\Invoicing();