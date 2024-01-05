<?php

namespace AtollMatrix;

class Invoicing
{

    private $bookingNumber;
    private $hotelName;
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

    public function __construct(
        $bookingNumber = null,
        $hotelName = null,
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
        $totalAmount = null
    ) {
        $this->bookingNumber    = $bookingNumber;
        $this->hotelName        = $hotelName;
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

        add_action('admin_menu', array($this, 'add_invoicing_admin_menu')); // This now points to the add_admin_menu function

        add_action('wp_ajax_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));
        add_action('wp_ajax_nopriv_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));

    }

    public function add_invoicing_admin_menu()
    {
        add_submenu_page(
            'atoll-matrix',
            // This is the slug of the parent menu
            'Invoices',
            'Invoices',
            'manage_options',
            'atollmatrix-invoicing',
            array($this, 'booking_invoices')
        );
    }

    public function booking_invoices()
    {

        echo "<h1>Invoices</h1>";
        echo '<div class="admin-atollmatrix-content">';

        echo self::hotelBooking_Search();

        // Hotel Information
        $this->hotelName    = "Ocean View Resort";
        $this->hotelAddress = "123 Seaside Lane, Coastal Town";
        $this->hotelPhone   = "555-0123";

        // Customer Information
        $this->customerName  = "John Doe";
        $this->customerEmail = "johndoe@example.com";

        // Booking Details
        $this->checkInDate    = "2024-02-15";
        $this->checkOutDate   = "2024-02-20";
        $this->roomType       = "Deluxe Sea View";
        $this->numberOfGuests = 2;

        // Pricing
        $this->roomPrice    = "200.00";
        $this->subTotal     = "200.00";
        $this->taxesAndFees = "50.00";
        $this->totalAmount  = "250.00";

        echo '</div>';

    }

    public function getInvoiceBookingDetails()
    {

        $booking_number = $_POST[ 'booking_number' ];

        // Fetch reservation details
        $reservations_instance = new \AtollMatrix\Reservations();
        $reservationID         = $reservations_instance->getReservationIDforBooking($booking_number);

        // Verify the nonce
        if (!isset($_POST[ 'atollmatrix_bookingdetails_nonce' ]) || !check_admin_referer('atollmatrix-bookingdetails-nonce', 'atollmatrix_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error([ 'message' => 'Failed' ]);
            return;
        }
        if ($reservationID) {
            $this->bookingNumber = $booking_number;
            $this->checkInDate   = get_post_meta($reservationID, 'atollmatrix_checkin_date', true);
            $this->checkOutDate  = get_post_meta($reservationID, 'atollmatrix_checkout_date', true);

            $adults = get_post_meta($reservationID, 'atollmatrix_reservation_room_adults', true);

            $children = array();
            $children = get_post_meta($reservationID, 'atollmatrix_reservation_room_children', true);

            $this->numberofAdults   = $adults;
            $this->numberofChildren = $children[ 'number' ];
            $totalGuests            = intval($adults + $children[ 'number' ]);
            $this->numberOfGuests   = $totalGuests;

            $this->roomType = $reservations_instance->getRoomNameForReservation($reservationID);
            // Add other reservation details as needed

            $taxStatus = get_post_meta($reservationID, 'atollmatrix_tax', true);
            $taxHTML   = get_post_meta($reservationID, 'atollmatrix_tax_html_data', true);
            $taxData   = get_post_meta($reservationID, 'atollmatrix_tax_data', true);

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

            $ratePerNight = get_post_meta($reservationID, 'atollmatrix_reservation_rate_per_night', true);
            $subTotal     = get_post_meta($reservationID, 'atollmatrix_reservation_subtotal_room_cost', true);
            $totalAmount  = get_post_meta($reservationID, 'atollmatrix_reservation_total_room_cost', true);

            $this->roomPrice   = $ratePerNight;
            $this->subTotal    = $subTotal;
            $this->totalAmount = $totalAmount;

            // Fetch guest details
            $guestID = $reservations_instance->getGuest_id_forReservation($booking_number);
            if ($guestID) {
                $this->customerName  = get_post_meta($guestID, 'atollmatrix_full_name', true);
                $this->customerEmail = get_post_meta($guestID, 'atollmatrix_email_address', true);
            }

        } else {
            echo "<p>No reservation found for Booking Number: " . esc_html($booking_number) . "</p>";
        }

        $informationSheet = $this->invoiceTemplate(
            $this->bookingNumber,
            $this->hotelName,
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
            $this->totalAmount
        );
        echo $informationSheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }

    public function hotelBooking_Search()
    {
        ob_start();
        $atollmatrix_bookingdetails_nonce = wp_create_nonce('atollmatrix-bookingdetails-nonce');
        ?>

            <div id="hotel-booking-form">

            <div class="calendar-insights-wrap">
                <div id="check-in-display">Check-in: <span>-</span></div>
                <div id="check-out-display">Check-out: <span>-</span></div>
                <div id="last-night-display">Last-night: <span>-</span></div>
                <div id="nights-display">Nights: <span>-</span></div>
            </div>

                <div class="front-booking-search">
                    <div class="front-booking-number-wrap">
                        <div class="front-booking-number-container">
                            <div class="form-group form-floating form-floating-booking-number form-bookingnumber-request">
                                <input type="hidden" name="atollmatrix_bookingdetails_nonce" value="<?php echo esc_attr($atollmatrix_bookingdetails_nonce); ?>" />
                                <input placeholder="Booking No." type="text" class="form-control" id="booking_number" name="booking_number" required>
                                <label for="booking_number" class="control-label">Booking No.</label>
                            </div>
                        </div>
                        <div id="invoiceDetails" class="div-button">Search</div>
                    </div>
                </div>

			<div class="booking-details-lister">
				<div id="booking-details-ajax"></div>
			</div>
		</div>
		<?php
return ob_get_clean();
    }

    public function invoiceTemplate(
        $bookingNumber,
        $hotelName,
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
        $totalAmount
    ) {
        ob_start();
        ?>
        <section id="hotel-info">
            <h2>Hotel Information</h2>
            <p>Name: <?php echo $hotelName; ?></p>
            <p>Address: <?php echo $hotelAddress; ?></p>
            <p>Phone: <?php echo $hotelPhone; ?></p>
        </section>

        <section id="customer-info">
            <h2>Customer Information</h2>
            <p>Name: <?php echo $customerName; ?></p>
            <p>Email: <?php echo $customerEmail; ?></p>
        </section>

        <section id="booking-details">
            <h2>Booking Details</h2>
            <p>Booking No: <?php echo $bookingNumber; ?></p>
            <p>Check-in Date: <?php echo $checkInDate; ?></p>
            <p>Check-out Date: <?php echo $checkOutDate; ?></p>
            <p>Room Type: <?php echo $roomType; ?></p>
            <p>Number of Adults: <?php echo $numberofAdults; ?></p>
            <?php
if ($numberofChildren > 0) {
            ?>
            <p>Number of Children: <?php echo $numberofChildren; ?></p>
            <?php
}
        ?>
        </section>

        <section id="pricing">
            <h2>Pricing</h2>
            <p>Room Price: <?php echo atollmatrix_price($roomPrice); ?></p>
            <?php
$reservations_instance = new \AtollMatrix\Reservations();
        $reservationID         = $reservations_instance->getReservationIDforBooking($bookingNumber);
        $taxStatus             = get_post_meta($reservationID, 'atollmatrix_tax', true);
        if ('enabled' == $taxStatus) {
            ?>
            <p>Sub Total: <?php echo atollmatrix_price($subTotal); ?></p>
            <p>Taxes and Fees: <?php echo $taxesAndFees; ?></p>
            <?php
}
        ?>
            <p>Total Amount: <?php echo atollmatrix_price($totalAmount); ?></p>
        </section>

        <footer>
            <p>Thank you for your booking!</p>
            <p>Terms and conditions:</p>
        </footer>
        <?php
return ob_get_clean();
    }

}

$instance = new \AtollMatrix\Invoicing();