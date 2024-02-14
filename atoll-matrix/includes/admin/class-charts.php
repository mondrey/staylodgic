<?php

namespace AtollMatrix;

class ChartGenerator {
    private $id;
    private $type;
    private $data;
    private $options;

    public function __construct($id, $type = 'bar', $data = [], $options = []) {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data;
        $this->options = $options;
    }

    public static function get_chart_config($id) {
        $configs = [
            'chart1' => [
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
                'type' => 'doughnut',
                'data' => self::get_current_day_stats_data(),
                'options' => [
                    'responsive' => true,
                ]
            ],
            'chart5' => [
                'type' => 'doughnut',
                'data' => self::get_tomorrow_stats_data(),
                'options' => [
                    'responsive' => true,
                ]
            ],
            // Add more chart configurations here...
        ];
    
        return $configs[$id] ?? null;
    }

    private static function get_tomorrow_stats_data() {
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
                $status = get_post_meta(get_the_ID(), 'atollmatrix_reservation_status', true);
                $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
    
                if ($status == 'confirmed') {
                    if ($checkin == $tomorrow) {
                        $checkinCount++;
                    }
                    if ($checkout == $tomorrow) {
                        $checkoutCount++;
                    }
                    if ($checkin < $tomorrow && $checkout > $tomorrow) {
                        $stayingCount++;
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
                    'backgroundColor' => ['rgba(255,0,0,1)', 'rgba(83, 0, 255, 1)', 'rgba(255, 206, 86, 1)'],
                    'borderColor' => ['rgba(255,0,0,1)', 'rgba(83, 0, 255, 1)', 'rgba(255, 206, 86, 1)'],
                    'borderWidth' => 1,
                ],
            ],            
        ];
    }    

    private static function get_current_day_stats_data() {
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
                $status = get_post_meta(get_the_ID(), 'atollmatrix_reservation_status', true);
                $checkin = get_post_meta(get_the_ID(), 'atollmatrix_checkin_date', true);
                $checkout = get_post_meta(get_the_ID(), 'atollmatrix_checkout_date', true);
    
                if ($status == 'confirmed') {
                    if ($checkin == $today) {
                        $checkinCount++;
                    }
                    if ($checkout == $today) {
                        $checkoutCount++;
                    }
                    if ($checkin < $today && $checkout > $today) {
                        $stayingCount++;
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
                    'backgroundColor' => ['rgba(255,0,0,1)', 'rgba(83, 0, 255, 1)', 'rgba(255, 206, 86, 1)'],
                    'borderColor' => ['rgba(255,0,0,1)', 'rgba(83, 0, 255, 1)', 'rgba(255, 206, 86, 1)'],
                    'borderWidth' => 1,
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
       

    public static function chart_shortcode($atts) {
        $atts = shortcode_atts(['id' => ''], $atts, 'chart');
    
        $config = self::get_chart_config($atts['id']);
        if (!$config) {
            return 'Chart not found.';
        }
    
        $chart = new ChartGenerator($atts['id'], $config['type'], $config['data'], $config['options']);
        return $chart->render();
    }

    public function render() {
        $data = htmlspecialchars(json_encode($this->data), ENT_QUOTES, 'UTF-8');
        $options = htmlspecialchars(json_encode($this->options), ENT_QUOTES, 'UTF-8');
    
        return <<<HTML
    <canvas id="{$this->id}" class="atollmatrix-chart" data-type="{$this->type}" data-data="{$data}" data-options="{$options}"></canvas>
    HTML;
    }    
    
}

add_shortcode('atollmatrix_chart', ['\AtollMatrix\ChartGenerator', 'chart_shortcode']);
