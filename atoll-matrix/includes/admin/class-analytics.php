<?php

namespace AtollMatrix;

class Analytics {
    private $id;
    private $info;
    private $type;
    private $data;
    private $options;
    private $guests;

    public function __construct($id, $info = 'today', $type = 'bar', $data = [], $options = [], $guests = array() ) {
        $this->id = $id;
        $this->info = $info;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
        $this->guests = $guests;
    }

    public function get_chart_config($id) {
        $configs = [
            'chart1' => [
                'info' => 'past_twelve_months_bookings',
                'type' => 'line',
                'data' => self::get_past_twelve_months_bookings_data(),
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
                'type' => 'bar',
                'data' => self::get_past_twelve_months_revenue_data(),
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
                'type' => 'bar',
                'data' => self::get_past_twelve_months_adr_data(),
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
                'type' => 'polarArea',
                'data' => $this->get_current_day_stats_data(),
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
                'type' => 'polarArea',
                'data' => $this->get_tomorrow_stats_data(),
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
                'type' => 'polarArea',
                'data' => $this->get_dayafter_stats_data(),
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

    private static function get_past_twelve_months_adr_data() {
        $labels = [];
        $adrData = [];
        $currentMonth = date('Y-m');
        for ($i = 12; $i >= 1; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));
    
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

    private static function get_past_twelve_months_revenue_data() {
        $labels = [];
        $revenueData = [];
        $currentMonth = date('Y-m');
        for ($i = 12; $i >= 1; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));
    
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
    
            $revenueData[] = $totalRevenue;
        }
    
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
    
    

    private static function get_past_twelve_months_bookings_data() {
        $labels = [];
        $confirmedData = [];
        $cancelledData = [];
        $currentMonth = date('Y-m');
        for ($i = 12; $i >= 1; $i--) {
            $month = date('Y-m', strtotime("$currentMonth -$i month"));
            $labels[] = date('F', strtotime($month));
    
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
        }
    
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
       

    public function chart_shortcode($atts) {
        $atts = shortcode_atts(['id' => ''], $atts, 'chart');
    
        $config = $this->get_chart_config($atts['id']);
        if (!$config) {
            return 'Chart not found.';
        }
    
        $chart = new Analytics($atts['id'], $config['info'], $config['type'], $config['data'], $config['options'], $this->guests);
        return $chart->render();
    }

    public function render() {
        $data = htmlspecialchars(json_encode($this->data), ENT_QUOTES, 'UTF-8');
        $options = htmlspecialchars(json_encode($this->options), ENT_QUOTES, 'UTF-8');
    
        // Initialize the guest list HTML
        $guestListHtml = '';
    
        // Check if there are guests for the current chart type
        if (isset($this->guests[$this->info])) {
            if ( 'today' == $this->info ) {
                $guestListHtml .= "<h2>Today</h2>";
            }
            if ( 'tomorrow' == $this->info ) {
                $guestListHtml .= "<h2>Tomorrow</h2>";
            }
            if ( 'dayafter' == $this->info ) {
                $guestListHtml .= "<h2>Day After</h2>";
            }
            foreach ($this->guests[$this->info] as $status => $guests) {
                $guestListHtml .= "<h3>" . ucfirst($status) . " Guests</h3><ul>";
                foreach ($guests as $guestId => $guestName) {
                    $guestListHtml .= "<li>$guestName</li>";
                }
                $guestListHtml .= "</ul>";
            }
        }
    
        return <<<HTML
    <canvas id="{$this->id}" class="atollmatrix-chart" data-type="{$this->type}" data-data="{$data}" data-options="{$options}"></canvas>
    $guestListHtml
    HTML;
    }     
    
}

// Create an instance of the ChartGenerator class
$analytics = new \AtollMatrix\Analytics( $id = false );

// Register the shortcode using the instance
add_shortcode('atollmatrix_chart', [$analytics, 'chart_shortcode']);

