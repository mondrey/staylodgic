<?php

namespace Staylodgic;

class Invoicing
{

    private $stay_reservation_id;
    private $bookingStatus;
    private $stay_booking_number;
    private $numberDays;
    private $hotel_logo;
    private $hotel_name;
    private $hotel_header;
    private $hotel_address;
    private $hotel_phone;
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
    private $hotel_footer;

    public function __construct(
        $stay_reservation_id = null,
        $bookingStatus = null,
        $stay_booking_number = null,
        $numberDays = null,
        $hotel_logo = null,
        $hotel_name = null,
        $hotel_header = null,
        $hotel_address = null,
        $hotel_phone = null,
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
        $hotel_footer = null
    ) {
        $this->stay_reservation_id    = $stay_reservation_id;
        $this->bookingStatus    = $bookingStatus;
        $this->stay_booking_number    = $stay_booking_number;
        $this->numberDays    = $numberDays;
        $this->hotel_logo        = $hotel_logo;
        $this->hotel_name        = $hotel_name;
        $this->hotel_header      = $hotel_header;
        $this->hotel_address     = $hotel_address;
        $this->hotel_phone       = $hotel_phone;
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
        $this->hotel_footer      = $hotel_footer;

        add_action('admin_menu', array($this, 'add_invoicing_admin_menu')); // This now points to the add_admin_menu function
        add_action('admin_menu', array($this, 'add_activity_invoicing_admin_menu')); // This now points to the add_admin_menu function

        add_action('wp_ajax_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));
        add_action('wp_ajax_nopriv_getInvoiceBookingDetails', array($this, 'getInvoiceBookingDetails'));

        add_action('wp_ajax_getInvoiceActivityDetails', array($this, 'getInvoiceActivityDetails'));
        add_action('wp_ajax_nopriv_getInvoiceActivityDetails', array($this, 'getInvoiceActivityDetails'));
    }
    
    /**
     * Method add_invoicing_admin_menu
     *
     * @return void
     */
    public function add_invoicing_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=slgc_reservations', // Set the parent slug to your custom post type slug
            __('Invoices', 'staylodgic'),
            __('Invoices', 'staylodgic'),
            'edit_posts',
            'staylodgic-invoicing',
            array($this, 'booking_invoices')
        );
    }    
    /**
     * Method add_activity_invoicing_admin_menu
     *
     * @return void
     */
    public function add_activity_invoicing_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=slgc_activityres', // Set the parent slug to your custom post type slug
            __('Invoices', 'staylodgic'),
            __('Invoices', 'staylodgic'),
            'edit_posts',
            'staylodgic-activity-invoicing',
            array($this, 'activity_invoices')
        );
    }
    
    /**
     * Method activity_invoices
     *
     * @return void
     */
    public function activity_invoices()
    {

        echo '<h1>' . __('Activity Invoices', 'staylodgic') . '</h1>';
        echo '<div class="admin-staylodgic-content">';

        echo self::activityBooking_Search();

        echo '</div>';
    }
        
    /**
     * Method booking_invoices
     *
     * @return void
     */
    public function booking_invoices()
    {

        echo '<h1>' . __('Invoices', 'staylodgic') . '</h1>';
        echo '<div class="admin-staylodgic-content">';

        echo self::hotelBooking_Search();

        echo '</div>';
    }
    
    /**
     * Method getInvoiceActivityDetails
     *
     * @return void
     */
    public function getInvoiceActivityDetails()
    {

        $booking_number = $_POST['booking_number'];

        // Fetch reservation details
        $reservations_instance = new \Staylodgic\Activity();
        $stay_reservation_id         = $reservations_instance->get_activity_id_for_booking($booking_number);

        $reservations_instance = new \Staylodgic\Activity($date = false, $activity_id = false, $stay_reservation_id);
        // Verify the nonce
        if (!isset($_POST['staylodgic_bookingdetails_nonce']) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error(['message' => 'Failed']);
            return;
        }

        // Hotel Information
        $property_logo_id = staylodgic_get_option('activity_property_logo');
        $property_name    = staylodgic_get_option('activity_property_name');
        $property_phone   = staylodgic_get_option('activity_property_phone');
        $property_address = staylodgic_get_option('activity_property_address');
        $property_header  = staylodgic_get_option('activity_property_header');
        $property_footer  = staylodgic_get_option('activity_property_footer');

        $this->stay_reservation_id    = $stay_reservation_id;
        $this->hotel_name    = $property_name;
        $this->hotel_phone   = $property_phone;
        $this->hotel_address = $property_address;
        $this->hotel_header  = $property_header;
        $this->hotel_footer  = $property_footer;
        $this->hotel_logo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

        if ($stay_reservation_id) {
            $this->stay_booking_number = $booking_number;
            $this->checkInDate   = get_post_meta($stay_reservation_id, 'staylodgic_checkin_date', true);

            $adults = get_post_meta($stay_reservation_id, 'staylodgic_reservation_activity_adults', true);

            $children = array();
            $children = get_post_meta($stay_reservation_id, 'staylodgic_reservation_activity_children', true);
            
            $this->numberofAdults   = $adults;

            $stay_total_guests            = intval($adults);

            if (isset($children['number'])) {
                $this->numberofChildren = $children['number'];
                $stay_total_guests            += intval($children['number']);
            }

            $this->numberOfGuests   = $stay_total_guests;

            $this->bookingStatus = __('Booking Pending', 'staylodgic');
            if ($reservations_instance->is_confirmed_reservation($stay_reservation_id)) {
                $this->bookingStatus = __('Booking Confirmed', 'staylodgic');
            }
            $this->roomType = $reservations_instance->get_activity_name_for_reservation($stay_reservation_id);
            // Add other reservation details as needed

            $taxStatus = get_post_meta($stay_reservation_id, 'staylodgic_tax', true);
            $taxHTML   = get_post_meta($stay_reservation_id, 'staylodgic_tax_html_data', true);
            $taxData   = get_post_meta($stay_reservation_id, 'staylodgic_tax_data', true);

            $tax_summary = '<div id="input-tax-summary">';
            $tax_summary .= '<div class="input-tax-summary-wrap">';
            if ('enabled' == $taxStatus) {
                $tax_summary .= '<div class="input-tax-summary-wrap-inner">';
                $tax_summary .= $taxHTML;
                
                $tax_summary .= '</div>';
            }
            $tax_summary .= '</div>';
            $tax_summary .= '</div>';

            $this->taxesAndFees = $tax_summary;

            $ratePerPerson = get_post_meta($stay_reservation_id, 'staylodgic_reservation_rate_per_person', true);
            $subTotal     = get_post_meta($stay_reservation_id, 'staylodgic_reservation_subtotal_activity_cost', true);
            $totalAmount  = get_post_meta($stay_reservation_id, 'staylodgic_reservation_total_room_cost', true);

            $this->roomPrice   = $ratePerPerson;
            $this->subTotal    = $subTotal;
            $this->totalAmount = $totalAmount;

            // Fetch guest details
            $stay_guest_id = $reservations_instance->get_guest_id_for_reservation($booking_number);
            if ($stay_guest_id) {
                $this->customerName  = get_post_meta($stay_guest_id, 'staylodgic_full_name', true);
                $this->customerEmail = get_post_meta($stay_guest_id, 'staylodgic_email_address', true);
            }
        } else {
            echo '<p>' . __('No reservation found for Booking Number:', 'staylodgic') . ' ' . esc_html($booking_number) . '</p>';
        }

        $information_sheet = $this->invoiceActivityTemplate(
            $this->stay_reservation_id,
            $this->bookingStatus,
            $this->stay_booking_number,
            $this->numberDays,
            $this->hotel_logo,
            $this->hotel_name,
            $this->hotel_header,
            $this->hotel_address,
            $this->hotel_phone,
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
            $this->hotel_footer
        );
        echo $information_sheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }
    
    /**
     * Method getInvoiceBookingDetails
     *
     * @return void
     */
    public function getInvoiceBookingDetails()
    {

        $booking_number = $_POST['booking_number'];

        // Fetch reservation details
        $reservations_instance = new \Staylodgic\Reservations();
        $stay_reservation_id         = $reservations_instance->get_reservation_id_for_booking($booking_number);

        $reservations_instance = new \Staylodgic\Reservations($date = false, $room_id = false, $stay_reservation_id);
        // Verify the nonce
        if (!isset($_POST['staylodgic_bookingdetails_nonce']) || !check_admin_referer('staylodgic-bookingdetails-nonce', 'staylodgic_bookingdetails_nonce')) {
            // Nonce verification failed; handle the error or reject the request
            // For example, you can return an error response
            wp_send_json_error(['message' => 'Failed']);
            return;
        }

        // Hotel Information
        $property_logo_id = staylodgic_get_option('property_logo');
        $property_name    = staylodgic_get_option('property_name');
        $property_phone   = staylodgic_get_option('property_phone');
        $property_address = staylodgic_get_option('property_address');
        $property_header  = staylodgic_get_option('property_header');
        $property_footer  = staylodgic_get_option('property_footer');

        $this->stay_reservation_id    = $stay_reservation_id;
        $this->hotel_name    = $property_name;
        $this->hotel_phone   = $property_phone;
        $this->hotel_address = $property_address;
        $this->hotel_header  = $property_header;
        $this->hotel_footer  = $property_footer;
        $this->hotel_logo    = $property_logo_id ? wp_get_attachment_image_url($property_logo_id, 'full') : '';

        if ($stay_reservation_id) {
            $this->stay_booking_number = $booking_number;
            $this->checkInDate   = get_post_meta($stay_reservation_id, 'staylodgic_checkin_date', true);
            $this->checkOutDate  = get_post_meta($stay_reservation_id, 'staylodgic_checkout_date', true);

            $adults = get_post_meta($stay_reservation_id, 'staylodgic_reservation_room_adults', true);

            $children = array();
            $children = get_post_meta($stay_reservation_id, 'staylodgic_reservation_room_children', true);
            
            $this->numberofAdults   = $adults;

            $stay_total_guests            = intval($adults);

            if (isset($children['number'])) {
                $this->numberofChildren = $children['number'];
                $stay_total_guests            += intval($children['number']);
            }

            $this->numberOfGuests   = $stay_total_guests;

            $this->bookingStatus = __('Booking Pending', 'staylodgic');
            if ($reservations_instance->is_confirmed_reservation($stay_reservation_id)) {
                $this->bookingStatus = __('Booking Confirmed', 'staylodgic');
            }
            $this->roomType = $reservations_instance->get_room_name_for_reservation($stay_reservation_id);
            $this->numberDays = $reservations_instance->count_reservation_days($stay_reservation_id);
            // Add other reservation details as needed

            $taxStatus = get_post_meta($stay_reservation_id, 'staylodgic_tax', true);
            $taxHTML   = get_post_meta($stay_reservation_id, 'staylodgic_tax_html_data', true);
            $taxData   = get_post_meta($stay_reservation_id, 'staylodgic_tax_data', true);

            $tax_summary = '<div id="input-tax-summary">';
            $tax_summary .= '<div class="input-tax-summary-wrap">';
            if ('enabled' == $taxStatus) {
                $tax_summary .= '<div class="input-tax-summary-wrap-inner">';
                $tax_summary .= $taxHTML;
                
                $tax_summary .= '</div>';
            }
            $tax_summary .= '</div>';
            $tax_summary .= '</div>';

            $this->taxesAndFees = $tax_summary;

            $ratePerNight = get_post_meta($stay_reservation_id, 'staylodgic_reservation_rate_per_night', true);
            $subTotal     = get_post_meta($stay_reservation_id, 'staylodgic_reservation_subtotal_room_cost', true);
            $totalAmount  = get_post_meta($stay_reservation_id, 'staylodgic_reservation_total_room_cost', true);

            $this->roomPrice   = $ratePerNight;
            $this->subTotal    = $subTotal;
            $this->totalAmount = $totalAmount;

            // Fetch guest details
            $stay_guest_id = $reservations_instance->get_guest_id_for_reservation($booking_number);
            if ($stay_guest_id) {
                $this->customerName  = get_post_meta($stay_guest_id, 'staylodgic_full_name', true);
                $this->customerEmail = get_post_meta($stay_guest_id, 'staylodgic_email_address', true);
            }
        } else {
            echo '<p>' . __('No reservation found for Booking Number:', 'staylodgic') . ' ' . esc_html($booking_number) . '</p>';
        }

        $information_sheet = $this->invoiceTemplate(
            $this->stay_reservation_id,
            $this->bookingStatus,
            $this->stay_booking_number,
            $this->numberDays,
            $this->hotel_logo,
            $this->hotel_name,
            $this->hotel_header,
            $this->hotel_address,
            $this->hotel_phone,
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
            $this->hotel_footer
        );
        echo $information_sheet; // Encode the HTML content as JSON
        wp_die(); // Terminate and return a proper response
    }
    
    /**
     * Method activityBooking_Search
     *
     * @return void
     */
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
                            <label for="booking_number" class="control-label"><?php echo __('Booking No.', 'staylodgic'); ?></label>
                        </div>
                    </div>
                    <div id="invoiceActivityDetails" class="form-search-button"><?php echo __('Search', 'staylodgic'); ?></div>
                </div>
            </div>

            <div class="booking-details-lister">
                <div id="booking-details-ajax"></div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Method hotelBooking_Search
     *
     * @return void
     */
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
                            <label for="booking_number" class="control-label"><?php echo __('Booking No.', 'staylodgic'); ?></label>
                        </div>
                    </div>
                    <div id="invoiceDetails" class="form-search-button"><?php echo __('Search', 'staylodgic'); ?></div>
                </div>
            </div>

            <div class="booking-details-lister">
                <div id="booking-details-ajax"></div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }
    
    /**
     * Method invoiceActivityTemplate
     *
     * @return void
     */
    public function invoiceActivityTemplate(
        $stay_reservation_id,
        $bookingStatus,
        $stay_booking_number,
        $numberDays,
        $hotel_logo,
        $hotel_name,
        $hotel_header,
        $hotel_address,
        $hotel_phone,
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
        $hotel_footer
    ) {
        $activity_property_logo_width  = staylodgic_get_option('activity_property_logo_width');
        $stay_current_date = date('F jS, Y'); // Outputs: January 1st, 2024
        ob_start();
    ?>
        <div class="invoice-container-buttons">
            <button data-title="Guest Registration <?php echo esc_attr($stay_booking_number); ?>" data-id="<?php echo esc_attr($stay_booking_number); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php echo __('Print Invoice', 'staylodgic'); ?></button>
            <button data-file="registration-<?php echo esc_attr($stay_booking_number); ?>" data-id="<?php echo esc_attr($stay_booking_number); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php echo __('Save PDF', 'staylodgic'); ?></button>
        </div>
        <div class="invoice-container-outer">
            <div class="invoice-container" data-bookingnumber="<?php echo esc_attr($stay_booking_number); ?>">
                <div class="invoice-container-inner">
                    <div id="invoice-hotel-header">
                        <section id="invoice-hotel-logo">
                            <img class="invoice-logo" src="<?php echo esc_url($hotel_logo); ?>" width="<?php echo esc_attr($activity_property_logo_width) . 'px'; ?>" height="auto" />
                        </section>
                        <section id="invoice-info">
                            <p><?php echo esc_html($hotel_header); ?></p>
                            <p><?php echo __('Invoice No:', 'staylodgic'); ?> <?php echo esc_html($stay_booking_number . '-' . $stay_reservation_id); ?></p>
                            <p><?php echo __('Invoice Date:', 'staylodgic'); ?> <?php echo esc_html($stay_current_date); ?></p>
                            <p class="invoice-booking-status"><?php echo esc_html($bookingStatus); ?></p>
                        </section>
                    </div>
                    <section id="invoice-hotel-info">
                        <p><strong><?php echo esc_html($hotel_name); ?></strong></p>
                        <p><?php echo esc_html($hotel_address); ?></p>
                        <p><?php echo esc_html($hotel_phone); ?></p>
                    </section>
                    <section id="invoice-customer-info">
                        <h2><?php echo __('Bill to:', 'staylodgic'); ?></h2>
                        <p><?php echo __('Name:', 'staylodgic'); ?> <?php echo esc_html($customerName); ?></p>
                        <p><?php echo __('Email:', 'staylodgic'); ?> <?php echo esc_html($customerEmail); ?></p>
                    </section>

                    <div id="invoice-booking-information">

                        <section id="invoice-booking-details">
                            <h2><?php echo __('Activity Booking Details', 'staylodgic'); ?></h2>
                            <p><span><?php echo __('Booking No:', 'staylodgic'); ?></span><?php echo esc_html($stay_booking_number); ?></p>
                            <p><span><?php echo __('Activity Date:', 'staylodgic'); ?></span><?php echo esc_html($checkInDate); ?></p>
                            <p><span><?php echo __('Activity Type:', 'staylodgic'); ?></span><?php echo esc_html($roomType); ?></p>
                            <p><span><?php echo __('Adults:', 'staylodgic'); ?></span><?php echo esc_html($numberofAdults); ?></p>
                            <?php
                            if ($numberofChildren > 0) {
                            ?>
                                <p><span><?php echo __('Children:', 'staylodgic'); ?></span><?php echo esc_html($numberofChildren); ?></p>
                            <?php
                            }
                            ?>
                        </section>

                        <section id="invoice-booking-pricing">
                            <h2><?php echo __('Activity Price', 'staylodgic'); ?></h2>
                            <p class="nightly-rate-info"><span class="nightly-rate"><?php echo staylodgic_price($roomPrice); ?></span><span class="nights"> x <?php echo esc_html($numberDays); ?> <?php echo __('Per Person', 'staylodgic'); ?></span></p>
                            <?php
                            $reservations_instance = new \Staylodgic\Activity();
                            $stay_reservation_id         = $reservations_instance->get_activity_id_for_booking($stay_booking_number);
                            $taxStatus             = get_post_meta($stay_reservation_id, 'staylodgic_tax', true);
                            if ('enabled' == $taxStatus) {
                            ?>
                                <div class="subtotal-info">
                                    <p class="subtotal"><?php echo __('Sub Total:', 'staylodgic'); ?></p>
                                    <p><?php echo staylodgic_price($subTotal); ?></p>
                                </div>
                                <p><?php echo __('Taxes and Fees:', 'staylodgic'); ?> <?php echo $taxesAndFees; ?></p>
                            <?php
                            }
                            ?>
                            <div class="invoice-total">
                                <strong>
                                    <p><?php echo __('Total Amount:', 'staylodgic'); ?></p>
                                    <p class="price-total"><?php echo staylodgic_price($totalAmount); ?></p>
                                </strong>
                            </div>
                        </section>
                    </div>

                </div>
                <footer>
                    <div class="invoice-footer"><?php echo esc_html($hotel_footer); ?></div>
                </footer>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }
    
    /**
     * Method invoiceTemplate
     *
     * @return void
     */
    public function invoiceTemplate(
        $stay_reservation_id,
        $bookingStatus,
        $stay_booking_number,
        $numberDays,
        $hotel_logo,
        $hotel_name,
        $hotel_header,
        $hotel_address,
        $hotel_phone,
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
        $hotel_footer
    ) {

        $property_logo_width  = staylodgic_get_option('property_logo_width');
        $stay_current_date = date('F jS, Y'); // Outputs: January 1st, 2024
        ob_start();
    ?>
        <div class="invoice-container-buttons">
            <button data-title="Guest Registration <?php echo esc_attr($stay_booking_number); ?>" data-id="<?php echo esc_attr($stay_booking_number); ?>" id="print-invoice-button" class="button button-secondary paper-document-button print-invoice-button"><?php echo __('Print Invoice', 'staylodgic'); ?></button>
            <button data-file="registration-<?php echo esc_attr($stay_booking_number); ?>" data-id="<?php echo esc_attr($stay_booking_number); ?>" id="save-pdf-invoice-button" class="button button-secondary paper-document-button save-pdf-invoice-button"><?php echo __('Save PDF', 'staylodgic'); ?></button>
        </div>
        <div class="invoice-container-outer">
            <div class="invoice-container" data-bookingnumber="<?php echo esc_attr($stay_booking_number); ?>">
                <div class="invoice-container-inner">
                    <div id="invoice-hotel-header">
                        <section id="invoice-hotel-logo">
                            <img class="invoice-logo" src="<?php echo esc_url($hotel_logo); ?>" width="<?php echo esc_attr($property_logo_width) . 'px'; ?>" height="auto" />
                        </section>
                        <section id="invoice-info">
                            <p><?php echo $hotel_header; ?></p>
                            <p><?php echo __('Invoice No:', 'staylodgic'); ?> <?php echo esc_html($stay_booking_number . '-' . $stay_reservation_id); ?></p>
                            <p><?php echo __('Invoice Date:', 'staylodgic'); ?> <?php echo esc_html($stay_current_date); ?></p>
                            <p class="invoice-booking-status"><?php echo esc_html($bookingStatus); ?></p>
                        </section>
                    </div>
                    <section id="invoice-hotel-info">
                        <p><strong><?php echo esc_html($hotel_name); ?></strong></p>
                        <p><?php echo esc_html($hotel_address); ?></p>
                        <p><?php echo esc_html($hotel_phone); ?></p>
                    </section>
                    <section id="invoice-customer-info">
                        <h2><?php echo __('Bill to:', 'staylodgic'); ?></h2>
                        <p><?php echo __('Name:', 'staylodgic'); ?> <?php echo esc_html($customerName); ?></p>
                        <p><?php echo __('Email:', 'staylodgic'); ?> <?php echo esc_html($customerEmail); ?></p>
                    </section>

                    <div id="invoice-booking-information">

                        <section id="invoice-booking-details">
                            <h2><?php echo __('Booking Details', 'staylodgic'); ?></h2>
                            <p><span><?php echo __('Booking No:', 'staylodgic'); ?></span><?php echo esc_html($stay_booking_number); ?></p>
                            <p><span><?php echo __('Check-in Date:', 'staylodgic'); ?></span><?php echo esc_html($checkInDate); ?></p>
                            <p><span><?php echo __('Check-out Date:', 'staylodgic'); ?></span><?php echo esc_html($checkOutDate); ?></p>
                            <p><span><?php echo __('Room Type:', 'staylodgic'); ?></span><?php echo esc_html($roomType); ?></p>
                            <p><span><?php echo __('Adults:', 'staylodgic'); ?></span><?php echo esc_html($numberofAdults); ?></p>
                            <?php
                            if ($numberofChildren > 0) {
                            ?>
                                <p><span><?php echo __('Children:', 'staylodgic'); ?></span><?php echo esc_html($numberofChildren); ?></p>
                            <?php
                            }
                            ?>
                        </section>

                        <section id="invoice-booking-pricing">
                            <h2><?php echo __('Room Price', 'staylodgic'); ?></h2>
                            <p class="nightly-rate-info"><span class="nightly-rate"><?php echo staylodgic_price($roomPrice); ?></span><span class="nights"> x <?php echo esc_html($numberDays); ?> <?php echo __('Nights', 'staylodgic'); ?></span></p>
                            <?php
                            $reservations_instance = new \Staylodgic\Reservations();
                            $stay_reservation_id         = $reservations_instance->get_reservation_id_for_booking($stay_booking_number);
                            $taxStatus             = get_post_meta($stay_reservation_id, 'staylodgic_tax', true);
                            if ('enabled' == $taxStatus) {
                            ?>
                                <div class="subtotal-info">
                                    <p class="subtotal"><?php echo __('Sub Total:', 'staylodgic'); ?></p>
                                    <p><?php echo staylodgic_price($subTotal); ?></p>
                                </div>
                                <p><?php echo __('Taxes and Fees:', 'staylodgic'); ?> <?php echo $taxesAndFees; ?></p>
                            <?php
                            }
                            ?>
                            <div class="invoice-total">
                                <strong>
                                    <p><?php echo __('Total Amount:', 'staylodgic'); ?></p>
                                    <p><?php echo staylodgic_price($totalAmount); ?></p>
                                </strong>
                            </div>
                        </section>
                    </div>

                </div>
                <footer>
                    <div class="invoice-footer"><?php echo esc_html($hotel_footer); ?></div>
                </footer>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}

$instance = new \Staylodgic\Invoicing();
