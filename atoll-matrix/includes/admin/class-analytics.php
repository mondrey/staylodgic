<?php

namespace AtollMatrix;

class Analytics {
    private $id;
    private $info;
    private $type;
    private $data;
    private $options;
    private $guests;
    private $bookings;

    public function __construct($id, $info = 'today', $type = 'bar', $data = [], $options = [], $guests = array(), $bookings = array() ) {
        $this->id = $id;
        $this->info = $info;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
        $this->guests = $guests;
        $this->bookings = $bookings;
    }

    public function get_chart_config($id) {
        $configs = [
            'chart1' => [
                'info' => 'past_twelve_months_bookings',
                'cache' => true,
                'type' => 'line',
                'options' => [
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ],
            'chart2' => [
                'info' => 'past_twelve_months_revenue',
                'cache' => true,
                'type' => 'bar',
                'options' => [
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ],
            'chart3' => [
                'info' => 'past_twelve_months_adr',
                'cache' => true,
                'type' => 'bar',
                'options' => [
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true
                        ]
                    ]
                ]
            ],
            'chart4' => [
                'info' => 'today',
                'cache' => false,
                'type' => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales' => [
                        'r' => [
                          'pointLabels' => [
                            'display' => true,
                            'centerPointLabels' => true,
                            'font' => [
                              'size' => 18
                            ]
                          ]
                        ]
                    ],
                ]
            ],
            'chart5' => [
                'info' => 'tomorrow',
                'cache' => false,
                'type' => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales' => [
                        'r' => [
                          'pointLabels' => [
                            'display' => true,
                            'centerPointLabels' => true,
                            'font' => [
                              'size' => 18
                            ]
                          ]
                        ]
                    ],
                ]
            ],
            'chart6' => [
                'info' => 'dayafter',
                'cache' => false,
                'type' => 'polarArea',
                'options' => [
                    'responsive' => true,
                    'scales' => [
                        'r' => [
                          'pointLabels' => [
                            'display' => true,
                            'centerPointLabels' => true,
                            'font' => [
                              'size' => 18
                            ]
                          ]
                        ]
                    ],
                ]
            ],
            // Add more chart configurations here...
        ];

        // Only process data for the requested chart
        if (isset($configs[$id])) {
            switch ($id) {
                case 'chart1':
                    $configs[$id]['data'] = $this->get_past_twelve_months_bookings_data();
                    break;
                case 'chart2':
                    $configs[$id]['data'] = $this->get_past_twelve_months_revenue_data();
                    break;
                case 'chart3':
                    $configs[$id]['data'] = $this->get_past_twelve_months_adr_data();
                    break;
                case 'chart4':
                    $configs[$id]['data'] = $this->get_current_day_stats_data();
                    break;
                case 'chart5':
                    $configs[$id]['data'] = $this->get_tomorrow_stats_data();
                    break;
                case 'chart6':
                    $configs[$id]['data'] = $this->get_dayafter_stats_data();
                    break;
                // Add cases for other charts as needed...
            }
        }
    
        return $configs[$id] ?? null;
    }

    private function get_dayafter_stats_data() {
        $dayafter = date('Y-m-d', strtotime('+2 day'));
        $checkinCount = 0;
        $checkoutCount = 0;
        $stayingCount = 0;
    
        $query = new \WP_Query([
            'post_type' => 'atmx_reservations',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $dayafter,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $dayafter,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $dayafter,
                    'compare' => '<=',
                    'type' => 'DATE'
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $dayafter,
                    'compare' => '>=',
                    'type' => 'DATE'
                ]
            ],
        ]);
    
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'atollmatrix_booking_number', true);
                $status = get_post_meta(get_the_ID(), 'atollmatrix_reservation_status', true);
                $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
                
                if ($status == 'confirmed') {
                    if ($checkin == $dayafter) {
                        $checkinCount++;
                        $this->add_guest( $booking_number, 'dayafter', 'checkin');
                    }
                    if ($checkout == $dayafter) {
                        $checkoutCount++;
                        $this->add_guest( $booking_number, 'dayafter', 'checkout');
                    }
                    if ($checkin < $dayafter && $checkout > $dayafter) {
                        $stayingCount++;
                        $this->add_guest( $booking_number, 'dayafter', 'staying');
                    }
                }
            }
        }
        wp_reset_postdata();
    
        return [
            'labels' => ['Check-ins', 'Check-outs', 'Staying'],
            'datasets' => [
                [
                    'data' => [$checkinCount, $checkoutCount, $stayingCount],
                    'backgroundColor' => ['rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                ],
            ],            
        ];
    }

    private function add_guest( $booking_number = false, $day = 'today', $type = 'checkin' ) {
        if ($booking_number) {
            // Fetch guest details
            $reservation_instance = new \AtollMatrix\Reservations();
            $guestID = $reservation_instance->getGuest_id_forReservation($booking_number);
            if ($guestID) {
                $name = esc_html(get_post_meta($guestID, 'atollmatrix_full_name', true));
                $this->guests[$day][$type][$guestID] = $name;
            }
        }
    }

    private function get_tomorrow_stats_data() {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $checkinCount = 0;
        $checkoutCount = 0;
        $stayingCount = 0;
    
        $query = new \WP_Query([
            'post_type' => 'atmx_reservations',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $tomorrow,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $tomorrow,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $tomorrow,
                    'compare' => '<=',
                    'type' => 'DATE'
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $tomorrow,
                    'compare' => '>=',
                    'type' => 'DATE'
                ]
            ],
        ]);
    
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'atollmatrix_booking_number', true);
                $status = get_post_meta(get_the_ID(), 'atollmatrix_reservation_status', true);
                $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
    
                if ($status == 'confirmed') {
                    if ($checkin == $tomorrow) {
                        $checkinCount++;
                        $this->add_guest( $booking_number, 'tomorrow', 'checkin');
                    }
                    if ($checkout == $tomorrow) {
                        $checkoutCount++;
                        $this->add_guest( $booking_number, 'tomorrow', 'checkout');
                    }
                    if ($checkin < $tomorrow && $checkout > $tomorrow) {
                        $stayingCount++;
                        $this->add_guest( $booking_number, 'tomorrow', 'staying');
                    }
                }
            }
        }
        wp_reset_postdata();
    
        return [
            'labels' => ['Check-ins', 'Check-outs', 'Staying'],
            'datasets' => [
                [
                    'data' => [$checkinCount, $checkoutCount, $stayingCount],
                    'backgroundColor' => ['rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                ],
            ],            
        ];
    }    

    private function get_current_day_stats_data() {
        $today = date('Y-m-d');
        $checkinCount = 0;
        $checkoutCount = 0;
        $stayingCount = 0;
    
        $query = new \WP_Query([
            'post_type' => 'atmx_reservations',
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $today,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $today,
                    'compare' => '=',
                ],
                [
                    'key' => 'atollmatrix_checkin_date',
                    'value' => $today,
                    'compare' => '<=',
                    'type' => 'DATE'
                ],
                [
                    'key' => 'atollmatrix_checkout_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATE'
                ]
            ],
        ]);
    
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $booking_number = get_post_meta(get_the_ID(), 'atollmatrix_booking_number', true);
                $status = get_post_meta(get_the_ID(), 'atollmatrix_reservation_status', true);
                $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
    
                if ($status == 'confirmed') {
                    if ($checkin == $today) {
                        $checkinCount++;
                        $this->add_guest( $booking_number, 'today', 'checkin');
                    }
                    if ($checkout == $today) {
                        $checkoutCount++;
                        $this->add_guest( $booking_number, 'today', 'checkout');
                    }
                    if ($checkin < $today && $checkout > $today) {
                        $stayingCount++;
                        $this->add_guest( $booking_number, 'today', 'staying');
                    }
                }
            }
        }
        wp_reset_postdata();
    
        return [
            'labels' => ['Check-ins', 'Check-outs', 'Staying'],
            'datasets' => [
                [
                    'data' => [$checkinCount, $checkoutCount, $stayingCount],
                    'backgroundColor' => ['rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                ],
            ],            
        ];
    }

    private function get_past_twelve_months_adr_data() {
        $labels = [];
        $adrData = [];
        $currentMonth = date('Y-m');

        $cache = new \AtollMatrix\Cache();

        for ($i = 12; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));
    
            // Check if the data is cached
            $cacheKey = $cache->generateAnalyticsCacheKey('twelve_months_adr_'. $month);
    
            if ($cache->hasCache($cacheKey)) {
                // Use cached data
                $cachedData = $cache->getCache($cacheKey);
                $adr = $cachedData;
            } else {

                error_log('Not using Cache adr data:' . $month );

                // Query for revenue and nights
                $revenueQuery = new \WP_Query([
                    'post_type' => 'atmx_reservations',
                    'posts_per_page' => -1,
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key' => 'atollmatrix_checkin_date',
                            'value' => $month,
                            'compare' => 'LIKE',
                        ],
                        [
                            'key' => 'atollmatrix_reservation_status',
                            'value' => 'confirmed',
                            'compare' => '=',
                        ],
                    ],
                ]);
    
                $totalRevenue = 0;
                $totalNights = 0;
                if ($revenueQuery->have_posts()) {
                    while ($revenueQuery->have_posts()) {
                        $revenueQuery->the_post();
                        $totalRevenue += (float)get_post_meta(get_the_ID(), 'atollmatrix_reservation_total_room_cost', true);
                        
                        $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                        $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
                        if ($checkin && $checkout) {
                            $checkinDate = new \DateTime($checkin);
                            $checkoutDate = new \DateTime($checkout);
                            $nights = $checkoutDate->diff($checkinDate)->days;
                            $totalNights += $nights;
                        }
                    }
                }
                wp_reset_postdata();
    
                $adr = $totalNights > 0 ? round($totalRevenue / $totalNights) : 0; // Round the ADR value
    
                // Cache the data if it's not the current month
                if ($month != $currentMonth) {
                    $cache->setCache($cacheKey, $adr);
                }
            }
    
            $adrData[] = $adr;
        }
    
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Average Daily Rate (ADR)',
                    'data' => $adrData,
                    'useGradient' => true,
                    'gradientStart' => 'rgba(255,0,0,1)',
                    'gradientEnd' => 'rgba(83, 0, 255, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => false,
                ],
            ],
        ];
    }
    

    private function get_past_twelve_months_revenue_data() {
        $labels = [];
        $revenueData = [];
        $currentMonth = date('Y-m');
        $revenue_count = 0;

        $cache = new \AtollMatrix\Cache();
    
        for ($i = 12; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));

            // Check if the data is cached
            $cacheKey = $cache->generateAnalyticsCacheKey('twelve_months_revenue_'. $month);
    
            if ($cache->hasCache($cacheKey)) {
                // Use cached data
                $cachedData = $cache->getCache($cacheKey);
                $totalRevenue = $cachedData;
            } else {

                error_log('Not using Cache revenue data:' . $month );
                // Query for revenue
                $revenueQuery = new \WP_Query([
                    'post_type' => 'atmx_reservations',
                    'posts_per_page' => -1,
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key' => 'atollmatrix_checkin_date',
                            'value' => $month,
                            'compare' => 'LIKE',
                        ],
                        [
                            'key' => 'atollmatrix_reservation_status',
                            'value' => 'confirmed',
                            'compare' => '=',
                        ],
                    ],
                ]);
    
                $totalRevenue = 0;
                if ($revenueQuery->have_posts()) {
                    while ($revenueQuery->have_posts()) {
                        $revenueQuery->the_post();
                        $totalRevenue += (float)get_post_meta(get_the_ID(), 'atollmatrix_reservation_total_room_cost', true);
                    }
                }
                wp_reset_postdata();
    
                // Cache the data if it's not the current month
                if ($month != $currentMonth) {
                    $cache->setCache($cacheKey, $totalRevenue);
                }
            }
    
            $revenueData[] = $totalRevenue;
            $revenue_count += $totalRevenue;
        }
    
        $this->bookings['revenue'] = $revenue_count;
    
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $revenueData,
                    'useGradient' => true,
                    'gradientStart' => 'rgba(177, 14, 236,1)',
                    'gradientEnd' => 'rgba(83, 0, 255, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => false,
                ],
            ],
        ];
    }
    
    
    
    private function get_past_twelve_months_bookings_data() {
        $labels = [];
        $confirmedData = [];
        $cancelledData = [];
        $currentMonth = date('Y-m');
    
        $confirmed_count = 0;
        $cancelled_count = 0;

        $cache = new \AtollMatrix\Cache();
    
        for ($i = 12; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));
    
            // Check if the data is cached
            $cacheKey = $cache->generateAnalyticsCacheKey('bookings_data_'. $month);
    // $cache->deleteCache($cacheKey);
            if ($cache->hasCache($cacheKey)) {
                // Use cached data
                $cachedData = $cache->getCache($cacheKey);
                $confirmedData[] = $cachedData['confirmed'];
                $cancelledData[] = $cachedData['cancelled'];

            } else {

                error_log('Not using Cache bookings data:' . $month );
                // Query for confirmed bookings
                $confirmedQuery = new \WP_Query([
                    'post_type' => 'atmx_reservations',
                    'posts_per_page' => -1,
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key' => 'atollmatrix_checkin_date',
                            'value' => $month,
                            'compare' => 'LIKE',
                        ],
                        [
                            'key' => 'atollmatrix_reservation_status',
                            'value' => 'confirmed',
                            'compare' => '=',
                        ],
                    ],
                ]);
                $confirmedData[] = $confirmedQuery->found_posts;
    
                // Query for cancelled bookings
                $cancelledQuery = new \WP_Query([
                    'post_type' => 'atmx_reservations',
                    'posts_per_page' => -1,
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key' => 'atollmatrix_checkin_date',
                            'value' => $month,
                            'compare' => 'LIKE',
                        ],
                        [
                            'key' => 'atollmatrix_reservation_status',
                            'value' => 'cancelled',
                            'compare' => '=',
                        ],
                    ],
                ]);
                $cancelledData[] = $cancelledQuery->found_posts;
    
                if ($month != $currentMonth) {
                    $cacheData = ['confirmed' => $confirmedQuery->found_posts, 'cancelled' => $cancelledQuery->found_posts];
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

        $this->bookings['confirmed'] = $confirmed_count;
        $this->bookings['cancelled'] = $cancelled_count;
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Confirmed Bookings',
                    'data' => $confirmedData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(79,0,255,1)',
                    'fill' => false,
                ],
                [
                    'label' => 'Cancelled Bookings',
                    'data' => $cancelledData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255,99,132,1)',
                    'fill' => false,
                ],
            ],
        ];
    }
    
    public function chart_generator($id) {
        // $atts = shortcode_atts(['id' => ''], $atts, 'chart');
    
        $config = $this->get_chart_config($id);
        if (!$config) {
            return 'Chart not found.';
        }
    
        $chart = new Analytics($id, $config['info'], $config['type'], $config['data'], $config['options'], $this->guests);
        return $chart->render();
    }
    public function stats_shortcode($atts) {
        $atts = shortcode_atts(['id' => ''], $atts, 'stats');

        $chart1 = $this->chart_generator('chart1');
        $chart2 = $this->chart_generator('chart2');
        $chart3 = $this->chart_generator('chart3');
        $chart4 = $this->chart_generator('chart4');
        $chart5 = $this->chart_generator('chart5');
        $chart6 = $this->chart_generator('chart6');

        // Initialize the guest list HTML
        $guestListHtml = '';
    
        // Iterate over each day in the guests array
        foreach ($this->guests as $day => $statuses) {
            // Add a heading for the day
            if ('today' == $day) {
                $guestListHtml .= "<h2>Today</h2>";
            } elseif ('tomorrow' == $day) {
                $guestListHtml .= "<h2>Tomorrow</h2>";
            } elseif ('dayafter' == $day) {
                $guestListHtml .= "<h2>Day After</h2>";
            } else {
                $guestListHtml .= "<h2>" . ucfirst($day) . "</h2>";
            }
    
            // Iterate over each status (staying, checkout, checkin) for the day
            foreach ($statuses as $status => $guests) {
                $guestListHtml .= "<h3>" . ucfirst($status) . " Guests</h3><ul>";
    
                // Iterate over each guest and add them to the list
                foreach ($guests as $guestId => $guestName) {
                    $guestListHtml .= "<li>$guestName</li>";
                }
                $guestListHtml .= "</ul>";
            }
        }

        error_log( print_r(  $this->bookings, true));
    
        return $guestListHtml . $chart1 . $chart2 . $chart3 . $chart4 . $chart5 . $chart6;

    }
    
    public function render() {
        $data = htmlspecialchars(json_encode($this->data), ENT_QUOTES, 'UTF-8');
        $options = htmlspecialchars(json_encode($this->options), ENT_QUOTES, 'UTF-8');
    
        // Initialize the guest list HTML
        $guestListHtml = '';
    
        return <<<HTML
    <canvas id="{$this->id}" class="atollmatrix-chart" data-type="{$this->type}" data-data="{$data}" data-options="{$options}"></canvas>
    $guestListHtml
    HTML;
    }     
    
}

// Create an instance of the ChartGenerator class
$analytics = new \AtollMatrix\Analytics( $id = false );

add_shortcode('atollmatrix_stats', [$analytics, 'stats_shortcode']);

