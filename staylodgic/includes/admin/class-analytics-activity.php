<?php

namespace Staylodgic;

class ActivityAnalytics
{
    private $id;
    private $info;
    private $type;
    private $data;
    private $options;
    private $guests;
    private $bookings;
    private $activities;
    private $activityLabels;

    public function __construct($id, $info = 'today', $type = 'bar', $data = [  ], $options = [  ], $guests = array(), $bookings = array(), $activities = array(),$activityLabels = array())
    {
        $this->id       = $id;
        $this->info     = $info;
        $this->type     = $type;
        $this->data     = $data;
        $this->options  = $options;
        $this->guests   = $guests;
        $this->bookings = $bookings;
        $this->activities = $activities;
        $this->activityLabels = $activityLabels;

        add_action('admin_menu', array($this, 'staylodgic_dashboard'));
    }

    public function loadActivities()
    {
        if (! \Staylodgic\Activity::hasActivities()) {
            return;
        }

        // Initialize the arrays
        $this->activities = [];
        $this->activityLabels = [];

        $activityQuery = new \WP_Query([
            'post_type'      => 'slgc_activity',
            'posts_per_page' => -1,
        ]);

        if ($activityQuery->have_posts()) {
            while ($activityQuery->have_posts()) {
                $activityQuery->the_post();
                $post_id = get_the_ID();
                $activityTitle = get_the_title();
                $this->activities[$post_id] = $activityTitle;
                $this->activityLabels[$post_id]['label'] = $activityTitle;
                $this->activityLabels[$post_id]['count'] = 0;
            }
            wp_reset_postdata();
        }

    }

    // Add the Availability menu item to the admin menu
    public function staylodgic_dashboard()
    {
        // Add the Availability submenu item under the parent menu
        add_submenu_page(
            'slgc-dashboard',
            'Activity Dasboard',
            'Activity Dasboard',
            'manage_options',
            'slgc-activity-dashboard',
            array($this, 'activity_display_dashboard')
        );

    }

    public function activity_display_dashboard()
    {

        echo '<div class="staylodgic_analytics_wrap">';

        // Add the logo image below the heading
        echo '<div class="staylodgic-main-logo"></div>';

        if ( ! \Staylodgic\Activity::hasActivities() ) {
            echo '<h1>' . __('No Activities Found','staylodgic') . '</h1>';
            return;
        } else {
    
            // Create an instance of the ChartGenerator class
            $analytics = new \Staylodgic\ActivityAnalytics($id = false);
            echo $analytics->display_stats();
        }

        echo '</div>';
    }


    public function get_chart_config($id)
    {
        $configs = [
            'past_twelve_months_bookings' => [
                'info'    => 'past_twelve_months_bookings',
                'heading' => 'Bookings for past twelve months',
                'cache'   => true,
                'type'    => 'line',
                'options' => [
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                         ],
                     ],
                 ],
             ],
            'past_twelve_months_revenue'  => [
                'info'    => 'past_twelve_months_revenue',
                'heading' => 'Revenue for past twelve months',
                'cache'   => true,
                'type'    => 'bar',
                'options' => [
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                         ],
                     ],
                 ],
             ],
            'bookings_today'              => [
                'info'    => 'today',
                'heading' => 'Today',
                'cache'   => false,
                'type'    => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales'     => [
                        'r' => [
                            'pointLabels' => [
                                'display'           => false,
                                'centerPointLabels' => true,
                                'font'              => [
                                    'size' => 18,
                                 ],
                             ],
                         ],
                     ],
                 ],
             ],
            'bookings_tomorrow'           => [
                'info'    => 'tomorrow',
                'heading' => 'Tomorrow',
                'cache'   => false,
                'type'    => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales'     => [
                        'r' => [
                            'pointLabels' => [
                                'display'           => false,
                                'centerPointLabels' => true,
                                'font'              => [
                                    'size' => 18,
                                 ],
                             ],
                         ],
                     ],
                 ],
             ],
            'bookings_dayafter'           => [
                'info'    => 'dayafter',
                'heading' => 'Day After',
                'cache'   => false,
                'type'    => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales'     => [
                        'r' => [
                            'pointLabels' => [
                                'display'           => false,
                                'centerPointLabels' => true,
                                'font'              => [
                                    'size' => 18,
                                 ],
                             ],
                         ],
                     ],
                 ],
             ],
            // Add more chart configurations here...
         ];

        // Only process data for the requested chart
        if (isset($configs[ $id ])) {
            switch ($id) {
                case 'past_twelve_months_bookings':
                    $configs[ $id ][ 'data' ] = $this->get_past_twelve_months_bookings_data();
                    break;
                case 'past_twelve_months_revenue':
                    $configs[ $id ][ 'data' ] = $this->get_past_twelve_months_revenue_data();
                    break;
                case 'bookings_today':
                    $configs[ $id ][ 'data' ] = $this->get_current_day_stats_data();
                    break;
                case 'bookings_tomorrow':
                    $configs[ $id ][ 'data' ] = $this->get_tomorrow_stats_data();
                    break;
                case 'bookings_dayafter':
                    $configs[ $id ][ 'data' ] = $this->get_dayafter_stats_data();
                    break;
                    // Add cases for other charts as needed...
            }
        }

        return $configs[ $id ] ?? null;
    }

    private function get_dayafter_stats_data()
    {
        $dayafter      = date('Y-m-d', strtotime('+2 day'));
        $checkinCount  = 0;
        $checkoutCount = 0;
        $stayingCount  = 0;

        $query = new \WP_Query([
            'post_type'      => 'slgc_activityres',
            'posts_per_page' => -1,
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => 'staylodgic_reservation_checkin',
                    'value'   => $dayafter,
                    'compare' => '=',
                 ],
             ],
         ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'staylodgic_booking_number', true);
                $activity_id = get_post_meta(get_the_ID(), 'staylodgic_activity_id', true);
                $status         = get_post_meta(get_the_ID(), 'staylodgic_reservation_status', true);
                $checkin        = get_post_meta(get_the_ID(), 'staylodgic_reservation_checkin', true);
                $checkout       = get_post_meta(get_the_ID(), 'staylodgic_checkout_date', true);

                if ($status == 'confirmed') {
                    if ($checkin == $dayafter) {

                        // Increment the count for the activity_id
                        if (!isset($this->activityLabels[$activity_id])) {
                            $this->activityLabels[$activity_id]['count'] = 0;
                        }
                        $this->activityLabels[$activity_id]['count']++;
                        $this->add_guest($booking_number, 'dayafter', 'checkin', $checkin );
                    }
                }
            }
        }
        wp_reset_postdata();

        return [
            'labels'   => array_column($this->activityLabels, 'label'),
            'datasets' => [
                [
                    'data'            => array_column($this->activityLabels, 'count'),
                    'backgroundColor' => [ 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ],
                 ],
             ],
         ];
    }

    private function add_guest($booking_number = false, $day = 'today', $type = 'checkin', $checkin = false, $checkout = false)
    {
        if ($booking_number) {
            // Fetch guest details
            $reservation_instance = new \Staylodgic\Activity();
            $guestID              = $reservation_instance->getGuest_id_forReservation($booking_number);
            if ($guestID) {
                $name = esc_html(get_post_meta($guestID, 'staylodgic_full_name', true));
    
                // Generate a UUID using the static method from the Common class
                $uuid = \Staylodgic\Common::generateUUID();
    
                // Use the combination of guestID and UUID as the key
                $uniqueKey = $guestID . '-' . $uuid;
    
                $this->guests[$day][$type][$guestID][$uniqueKey]['booking_number'] = $booking_number;
                $this->guests[$day][$type][$guestID][$uniqueKey]['name']           = $name;
                $this->guests[$day][$type][$guestID][$uniqueKey]['checkin']        = $checkin;
                $this->guests[$day][$type][$guestID][$uniqueKey]['checkout']       = $checkout;
            }
        }
    }

    private function get_tomorrow_stats_data()
    {
        $tomorrow      = date('Y-m-d', strtotime('+1 day'));
        $checkinCount  = 0;
        $checkoutCount = 0;
        $stayingCount  = 0;

        $query = new \WP_Query([
            'post_type'      => 'slgc_activityres',
            'posts_per_page' => -1,
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => 'staylodgic_reservation_checkin',
                    'value'   => $tomorrow,
                    'compare' => '=',
                 ],
             ],
         ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'staylodgic_booking_number', true);
                $activity_id = get_post_meta(get_the_ID(), 'staylodgic_activity_id', true);
                $status         = get_post_meta(get_the_ID(), 'staylodgic_reservation_status', true);
                $checkin        = get_post_meta(get_the_ID(), 'staylodgic_reservation_checkin', true);
                $checkout       = get_post_meta(get_the_ID(), 'staylodgic_checkout_date', true);

                if ($status == 'confirmed') {
                    if ($checkin == $tomorrow) {

                        // Increment the count for the activity_id
                        if (!isset($this->activityLabels[$activity_id])) {
                            $this->activityLabels[$activity_id]['count'] = 0;
                        }
                        $this->activityLabels[$activity_id]['count']++;
                        $this->add_guest($booking_number, 'tomorrow', 'checkin', $checkin );
                    }
                }
            }
        }
        wp_reset_postdata();

        return [
            'labels'   => array_column($this->activityLabels, 'label'),
            'datasets' => [
                [
                    'data'            => array_column($this->activityLabels, 'count'),
                    'backgroundColor' => [ 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ],
                 ],
             ],
         ];
    }

    private function get_current_day_stats_data()
    {
        $today         = date('Y-m-d');
        $checkinCount  = 0;
        $checkoutCount = 0;
        $stayingCount  = 0;

        $query = new \WP_Query([
            'post_type'      => 'slgc_activityres',
            'posts_per_page' => -1,
            'meta_query'     => [
                'relation' => 'OR',
                [
                    'key'     => 'staylodgic_reservation_checkin',
                    'value'   => $today,
                    'compare' => '=',
                 ],
             ],
         ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'staylodgic_booking_number', true);
                $activity_id = get_post_meta(get_the_ID(), 'staylodgic_activity_id', true);
                $status         = get_post_meta(get_the_ID(), 'staylodgic_reservation_status', true);
                $checkin        = get_post_meta(get_the_ID(), 'staylodgic_reservation_checkin', true);
                $checkout       = get_post_meta(get_the_ID(), 'staylodgic_checkout_date', true);
                
                if ($status == 'confirmed') {
                    if ($checkin == $today) {

                        // Increment the count for the activity_id
                        if (!isset($this->activityLabels[$activity_id])) {
                            $this->activityLabels[$activity_id]['count'] = 0;
                        }
                        $this->activityLabels[$activity_id]['count']++;
                        $this->add_guest($booking_number, 'today', 'checkin', $checkin );
                    }
                }
            }
        }
        wp_reset_postdata();

        return [
            'labels'   => array_column($this->activityLabels, 'label'),
            'datasets' => [
                [
                    'data'            => array_column($this->activityLabels, 'count'),
                    'backgroundColor' => [ 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ],
                 ],
             ],
         ];
    }

    private function get_past_twelve_months_revenue_data()
    {
        $labels        = [  ];
        $revenueData   = [  ];
        $currentMonth  = date('Y-m');
        $revenue_count = 0;

        $cache = new \Staylodgic\Cache();

        for ($i = 12; $i >= 0; $i--) {
            $month      = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[  ] = date('F', strtotime($month));

            // Check if the data is cached
            $cacheKey = $cache->generateAnalyticsCacheKey('twelve_months_revenue_' . $month);

            if ($cache->hasCache($cacheKey)) {
                // Use cached data
                $cachedData   = $cache->getCache($cacheKey);
                $totalRevenue = $cachedData;
            } else {

                error_log('Not using Cache revenue data:' . $month);
                // Query for revenue
                $revenueQuery = new \WP_Query([
                    'post_type'      => 'slgc_activityres',
                    'posts_per_page' => -1,
                    'meta_query'     => [
                        'relation' => 'AND',
                        [
                            'key'     => 'staylodgic_reservation_checkin',
                            'value'   => $month,
                            'compare' => 'LIKE',
                         ],
                        [
                            'key'     => 'staylodgic_reservation_status',
                            'value'   => 'confirmed',
                            'compare' => '=',
                         ],
                     ],
                 ]);

                $totalRevenue = 0;
                if ($revenueQuery->have_posts()) {
                    while ($revenueQuery->have_posts()) {
                        $revenueQuery->the_post();
                        $totalRevenue += (float) get_post_meta(get_the_ID(), 'staylodgic_reservation_total_room_cost', true);
                    }
                }
                wp_reset_postdata();

                // Cache the data if it's not the current month
                if ($month != $currentMonth) {
                    $cache->setCache($cacheKey, $totalRevenue);
                }
            }

            $revenueData[  ] = $totalRevenue;
            $revenue_count += intval( $totalRevenue );
        }

        $this->bookings[ 'revenue' ] = $revenue_count;

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'         => __('Monthly Revenue','staylodgic'),
                    'data'          => $revenueData,
                    'useGradient'   => true,
                    'gradientStart' => 'rgba(177, 14, 236,1)',
                    'gradientEnd'   => 'rgba(83, 0, 255, 1)',
                    'borderColor'   => 'rgba(75, 192, 192, 1)',
                    'fill'          => false,
                 ],
             ],
         ];
    }

    private function get_past_twelve_months_bookings_data()
    {
        $labels        = [  ];
        $confirmedData = [  ];
        $cancelledData = [  ];
        $currentMonth  = date('Y-m');

        $confirmed_count = 0;
        $cancelled_count = 0;

        $cache = new \Staylodgic\Cache();

        for ($i = 12; $i >= 0; $i--) {
            $month      = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[  ] = date('F', strtotime($month));

            // Check if the data is cached
            $cacheKey = $cache->generateAnalyticsCacheKey('bookings_data_' . $month);
            // $cache->deleteCache($cacheKey);
            if ($cache->hasCache($cacheKey)) {
                // Use cached data
                $cachedData        = $cache->getCache($cacheKey);
                $confirmedData[  ] = $cachedData[ 'confirmed' ];
                $cancelledData[  ] = $cachedData[ 'cancelled' ];

            } else {

                error_log('Not using Cache bookings data:' . $month);
                // Query for confirmed bookings
                $confirmedQuery = new \WP_Query([
                    'post_type'      => 'slgc_activityres',
                    'posts_per_page' => -1,
                    'meta_query'     => [
                        'relation' => 'AND',
                        [
                            'key'     => 'staylodgic_reservation_checkin',
                            'value'   => $month,
                            'compare' => 'LIKE',
                         ],
                        [
                            'key'     => 'staylodgic_reservation_status',
                            'value'   => 'confirmed',
                            'compare' => '=',
                         ],
                     ],
                 ]);
                $confirmedData[  ] = $confirmedQuery->found_posts;

                // Query for cancelled bookings
                $cancelledQuery = new \WP_Query([
                    'post_type'      => 'slgc_activityres',
                    'posts_per_page' => -1,
                    'meta_query'     => [
                        'relation' => 'AND',
                        [
                            'key'     => 'staylodgic_reservation_checkin',
                            'value'   => $month,
                            'compare' => 'LIKE',
                         ],
                        [
                            'key'     => 'staylodgic_reservation_status',
                            'value'   => 'cancelled',
                            'compare' => '=',
                         ],
                     ],
                 ]);
                $cancelledData[  ] = $cancelledQuery->found_posts;

                if ($month != $currentMonth) {
                    $cacheData = [ 'confirmed' => $confirmedQuery->found_posts, 'cancelled' => $cancelledQuery->found_posts ];
                    error_log('Caching Data: ' . print_r($cacheData, true));
                    $cache->setCache($cacheKey, $cacheData);
                }
            }

        }

        // Calculate the total counts
        foreach ($confirmedData as $count) {
            $confirmed_count += $count;
        }
        foreach ($cancelledData as $count) {
            $cancelled_count += $count;
        }

        $this->bookings[ 'confirmed' ] = $confirmed_count;
        $this->bookings[ 'cancelled' ] = $cancelled_count;

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => __('Confirmed Bookings','staylodgic'),
                    'data'            => $confirmedData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor'     => 'rgba(79,0,255,1)',
                    'fill'            => false,
                 ],
                [
                    'label'           => __('Cancelled Bookings','staylodgic'),
                    'data'            => $cancelledData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor'     => 'rgba(255,99,132,1)',
                    'fill'            => false,
                 ],
             ],
         ];
    }

    public function chart_generator($id)
    {
        // $atts = shortcode_atts(['id' => ''], $atts, 'chart');

        $config = $this->get_chart_config($id);
        if (!$config) {
            return 'Chart not found.';
        }

        $chart          = new \Staylodgic\ActivityAnalytics($id, $config[ 'info' ], $config[ 'type' ], $config[ 'data' ], $config[ 'options' ], $this->guests);
        $rendered_chart = $chart->render();

        $chart_output = '';

        $chart_output .= '<div class="staylodgic_analytics_chart staylodgic_analytics_chart_' . $config[ 'type' ] . ' ">';
        $chart_output .= '<h2 class="staylodgic_analytics_subheading">';
        $chart_output .= $config[ 'heading' ];
        $chart_output .= '</h2>';
        $chart_output .= $rendered_chart;
        $chart_output .= '</div>';

        return $chart_output;
    }

    public function guest_list()
    {
        // Initialize the guest list HTML
        $guestListHtml = '';

        // Iterate over each day in the guests array
        foreach ($this->guests as $day => $statuses) {

            $guestListHtml .= '<div class="staylodgic_analytics_table_wrap">';
            // Add a heading for the day
            if ('today' == $day) {
                $guestListHtml .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __('Today','staylodgic') . '</h2>';
            } elseif ('tomorrow' == $day) {
                $guestListHtml .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __('Tomorrow','staylodgic') . '</h2>';
            } elseif ('dayafter' == $day) {
                $guestListHtml .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __('Day After','staylodgic') . '</h2>';
            } else {
                $guestListHtml .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . ucfirst($day) . '</h2>';
            }

            // Sort the statuses array
            uksort($statuses, function ($a, $b) {
                $order = [ 'checkin', 'staying', 'checkout' ]; // Define your custom order
                return array_search($a, $order) - array_search($b, $order);
            });

            // Iterate over each status (staying, checkout, checkin) for the day
            foreach ($statuses as $status => $guests) {
                $count = 0;

                $font_icon = '';
                if ('checkin' == $status) {
                    $font_icon = '<i class="fas fa-sign-in-alt"></i>';
                }
                if ('checkout' == $status) {
                    $font_icon = '<i class="fas fa-sign-out-alt"></i>';
                }
                if ('staying' == $status) {
                    $font_icon = '<i class="fa-solid fa-bed"></i>';
                }

                $guestListHtml .= '<div class="staylodgic_table_outer">';
                $guestListHtml .= "<h3>" . $font_icon . ucfirst($status) . "</h3>";

                $guestListHtml .= '<table class="staylodgic_analytics_table table table-hover">';
                $guestListHtml .= '<thead class="table-light">';
                $guestListHtml .= '<tr>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-number number-column" scope="col"><i class="fas fa-hashtag"></i></th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-booking-number" scope="col"><i class="fas fa-hashtag"></i> Booking</th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-name" scope="col"><i class="fas fa-user"></i> Guest Name</th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-activity" scope="col"><i class="fas fa-bed"></i> Activity</th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-registration" scope="col"><i class="fas fa-clipboard-list"></i> Registration</th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-notes" scope="col"><i class="fas fa-sticky-note"></i> Notes</th>';
                $guestListHtml .= '<th class="table-cell-heading table-cell-heading-checkin" scope="col"><i class="fas fa-sign-in-alt"></i> Activity Date</th>';
                $guestListHtml .= '</tr>';
                $guestListHtml .= '</thead>';
                $guestListHtml .= '<tbody class="table-group-divider">';
                // Iterate over each guest and add them to the table
                foreach ($guests as $guestId => $bookings) {
                    error_log( '-------bookings-------');
                    error_log( print_r( $bookings,1 ));
                    foreach ($bookings as $booking) { // Iterate over each booking for the guest
                        $count++;
                        error_log( '-------booking-------');
                        error_log( print_r( $booking,1 ));
                        $reservations_instance = new \Staylodgic\Activity();
                        $reservation_id        = $reservations_instance->getActivityIDforBooking($booking[ 'booking_number' ]);

                        $checkinDate  = new \DateTime($booking[ 'checkin' ]);
                        $checkoutDate = new \DateTime($booking[ 'checkout' ]);
                        $nights       = $checkoutDate->diff($checkinDate)->days;

                        $guestListHtml .= '<tr>';
                        $guestListHtml .= '<th class="number-column" scope="row">' . $count . '</th>';
                        $guestListHtml .= '<td scope="row">';
                        $guestListHtml .= $booking[ 'booking_number' ];
                        $guestListHtml .= '</td>';
                        $guestListHtml .= '<td scope="row">';
                        $guestListHtml .= '<a href="' . esc_url(get_edit_post_link($reservation_id)) . '">';
                        $guestListHtml .= ucwords(strtolower($booking[ 'name' ]));
                        $guestListHtml .= '</a>';
                        $guestListHtml .= '</td>';
                        $guestListHtml .= '<td scope="row">';
                        
                        $room_name = $reservations_instance->getActivityNameForReservation($reservation_id);

                        $guestListHtml .= $room_name;
                        $guestListHtml .= '</td>';
                        $guestListHtml .= '<td scope="row">';

                        $registry_instance = new \Staylodgic\GuestRegistry();
                        $resRegIDs         = $registry_instance->fetchResRegIDsByBookingNumber($booking[ 'booking_number' ]);
                        if (isset($resRegIDs) && is_array($resRegIDs)) {
                            $guestListHtml .= $registry_instance->outputRegistrationAndOccupancy($resRegIDs[ 'reservationID' ], $resRegIDs[ 'guestRegisterID' ], 'icons');
                        }
                        $guestListHtml .= '</td>';

                        $notes             = get_post_meta($reservation_id, 'staylodgic_reservation_notes', true);
                        $notes_with_breaks = nl2br($notes);

                        $guestListHtml .= '<td scope="row">' . $notes_with_breaks . '</td>';
                        $guestListHtml .= '<td scope="row">' . $booking[ 'checkin' ] . '</td>';
                        $guestListHtml .= '</tr>';
                    }
                }
                $guestListHtml .= '</tbody>';
                $guestListHtml .= '</table>';
                $guestListHtml .= '</div>';
            }
            $guestListHtml .= '</div>';
        }

        return $guestListHtml;
    }
    public function display_stats()
    {

        $this->loadActivities();

        $past_twelve_months_bookings = $this->chart_generator('past_twelve_months_bookings');
        $past_twelve_months_revenue  = $this->chart_generator('past_twelve_months_revenue');
        $bookings_today              = $this->chart_generator('bookings_today');
        $bookings_tomorrow           = $this->chart_generator('bookings_tomorrow');
        $bookings_dayafter           = $this->chart_generator('bookings_dayafter');

        $guestListHtml = $this->guest_list();

        $row_one = '';

        $row_one .= '<div class="staylodgic_anaytlics_row_one">';
        $row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_today">' . $bookings_today . '</div>';
        $row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_tomorrow">' . $bookings_tomorrow . '</div>';
        $row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_dayafter">' . $bookings_dayafter . '</div>';
        $row_one .= '</div>';

        $dashboard = $row_one . $guestListHtml . $past_twelve_months_bookings . $past_twelve_months_revenue;
        return $dashboard;

    }

    public function render()
    {
        $data    = htmlspecialchars(json_encode($this->data), ENT_QUOTES, 'UTF-8');
        $options = htmlspecialchars(json_encode($this->options), ENT_QUOTES, 'UTF-8');

        // Initialize the guest list HTML
        $guestListHtml = '';

        return <<<HTML
    <canvas id="{$this->id}" class="staylodgic-chart" data-type="{$this->type}" data-data="{$data}" data-options="{$options}"></canvas>
    $guestListHtml
    HTML;
    }

}

$analytics = new \Staylodgic\ActivityAnalytics($id = false);
