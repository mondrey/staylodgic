<?php

namespace Staylodgic;

class Analytics_Activity {

	private $activity_id;
	private $info;
	private $type;
	private $data;
	private $options;
	private $guests;
	private $bookings;
	private $display_today;
	private $display_tomorrow;
	private $display_dayafter;
	private $activities;
	private $activity_labels;
	private $activity_colors;

	public function __construct( $activity_id, $info = 'today', $type = 'bar', $data = array(), $options = array(), $guests = array(), $bookings = array(), $activities = array(), $activity_labels = array(), $activity_colors = array() ) {
		$this->activity_id     = $activity_id;
		$this->info            = $info;
		$this->type            = $type;
		$this->data            = $data;
		$this->options         = $options;
		$this->guests          = $guests;
		$this->bookings        = $bookings;
		$this->activities      = $activities;
		$this->activity_labels = $activity_labels;
		$this->activity_colors = $activity_colors;

		$this->display_today    = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS' ) ) . '</span>';
		$this->display_tomorrow = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS', strtotime( '+1 day' ) ) ) . '</span>';
		$this->display_dayafter = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS', strtotime( '+2 days' ) ) ) . '</span>';

		add_action( 'admin_menu', array( $this, 'staylodgic_dashboard' ) );
	}

	/**
	 * Method load_activities
	 *
	 * @return void
	 */
	public function load_activities() {
		if ( ! \Staylodgic\Activity::has_activities() ) {
			return;
		}

		// Initialize the arrays
		$this->activities      = array();
		$this->activity_labels = array();
		$this->activity_colors = array();

		$activity_query = new \WP_Query(
			array(
				'post_type'      => 'slgc_activity',
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			)
		);

		if ( $activity_query->have_posts() ) {
			while ( $activity_query->have_posts() ) {
				$activity_query->the_post();
				$post_id             = get_the_ID();
				$stay_activity_title = get_the_title();
				$hex_color           = get_post_meta( get_the_ID(), 'staylodgic_dashboard_color', true );

				$rgb_values              = staylodgic_hex_to_rgb( $hex_color );
				$this->activity_colors[] = 'rgba(' . $rgb_values['r'] . ',' . $rgb_values['g'] . ',' . $rgb_values['b'] . ',0.5)';

				$this->activities[ $post_id ]               = $stay_activity_title;
				$this->activity_labels[ $post_id ]['label'] = $stay_activity_title;
				$this->activity_labels[ $post_id ]['count'] = 0;
			}
			wp_reset_postdata();
		}
	}

	/**
	 * Add the Availability menu item to the admin menu
	 *
	 * @return void
	 */
	public function staylodgic_dashboard() {
		// Add the Availability submenu item under the parent menu
		add_submenu_page(
			'slgc-dashboard',
			__( 'Activity Overview', 'staylodgic' ),
			__( 'Activity Overview', 'staylodgic' ),
			'edit_posts',
			'slgc-activity-dashboard',
			array( $this, 'activity_display_dashboard' )
		);
	}

	/**
	 * Method activity_display_dashboard
	 *
	 * @return void
	 */
	public function activity_display_dashboard() {

		echo '<div class="staylodgic_analytics_wrap">';

		if ( ! \Staylodgic\Activity::has_activities() ) {
			echo '<h1>' . esc_html_e( 'No Activities Found', 'staylodgic' ) . '</h1>';
			echo '<p>' . esc_html_e( 'Please configure atleast 1 activity from Activities section', 'staylodgic' ) . '</p>';
			return;
		} else {

			// Add the logo image below the heading
			echo '<div class="staylodgic-overview-heading">';
			echo '<h1>' . esc_html_e( 'Activity Overview', 'staylodgic' ) . '</h1>';
			echo '</div>';

			// Create an instance of the ChartGenerator class
			$analytics = new \Staylodgic\Analytics_Activity( $activity_id = false );
			echo $analytics->display_stats();
		}

		echo '</div>';
	}


	/**
	 * Method get_chart_config
	 *
	 * @param $activity_id $activity_id
	 *
	 * @return void
	 */
	public function get_chart_config( $activity_id ) {
		$configs = array(
			'past_twelve_months_bookings' => array(
				'info'    => 'past_twelve_months_bookings',
				'heading' => __( 'Bookings for past twelve months', 'staylodgic' ),
				'cache'   => true,
				'type'    => 'line',
				'options' => array(
					'scales' => array(
						'y' => array(
							'beginAtZero' => true,
						),
					),
				),
			),
			'past_twelve_months_revenue'  => array(
				'info'    => 'past_twelve_months_revenue',
				'heading' => __( 'Revenue for past twelve months', 'staylodgic' ),
				'cache'   => true,
				'type'    => 'bar',
				'options' => array(
					'scales' => array(
						'y' => array(
							'beginAtZero' => true,
						),
					),
				),
			),
			'bookings_today'              => array(
				'info'    => 'today',
				'heading' => __( 'Today', 'staylodgic' ) . ' ' . $this->display_today,
				'cache'   => false,
				'type'    => 'polarArea',
				'options' => array(
					'responsive' => true,
					'scales'     => array(
						'r' => array(
							'pointLabels' => array(
								'display'           => false,
								'centerPointLabels' => true,
								'font'              => array(
									'size' => 18,
								),
							),
						),
					),
				),
			),
			'bookings_tomorrow'           => array(
				'info'    => 'tomorrow',
				'heading' => __( 'Tomorrow', 'staylodgic' ) . ' ' . $this->display_tomorrow,
				'cache'   => false,
				'type'    => 'polarArea',
				'options' => array(
					'responsive' => true,
					'scales'     => array(
						'r' => array(
							'pointLabels' => array(
								'display'           => false,
								'centerPointLabels' => true,
								'font'              => array(
									'size' => 18,
								),
							),
						),
					),
				),
			),
			'bookings_dayafter'           => array(
				'info'    => 'dayafter',
				'heading' => __( 'Day After', 'staylodgic' ) . ' ' . $this->display_dayafter,
				'cache'   => false,
				'type'    => 'polarArea',
				'options' => array(
					'responsive' => true,
					'scales'     => array(
						'r' => array(
							'pointLabels' => array(
								'display'           => false,
								'centerPointLabels' => true,
								'font'              => array(
									'size' => 18,
								),
							),
						),
					),
				),
			),
			// Add more chart configurations here...
		);

		// Only process data for the requested chart
		if ( isset( $configs[ $activity_id ] ) ) {
			switch ( $activity_id ) {
				case 'past_twelve_months_bookings':
					$configs[ $activity_id ]['data'] = $this->get_past_twelve_months_bookings_data();
					break;
				case 'past_twelve_months_revenue':
					$configs[ $activity_id ]['data'] = $this->get_past_twelve_months_revenue_data();
					break;
				case 'bookings_today':
					$configs[ $activity_id ]['data'] = $this->get_current_day_stats_data();
					break;
				case 'bookings_tomorrow':
					$configs[ $activity_id ]['data'] = $this->get_tomorrow_stats_data();
					break;
				case 'bookings_dayafter':
					$configs[ $activity_id ]['data'] = $this->get_dayafter_stats_data();
					break;
					// Add cases for other charts as needed...
			}
		}

		return $configs[ $activity_id ] ?? null;
	}

	/**
	 * Method get_dayafter_stats_data
	 *
	 * @return void
	 */
	private function get_dayafter_stats_data() {
		$dayafter       = gmdate( 'Y-m-d', strtotime( '+2 day' ) );
		$checkin_count  = 0;
		$checkout_count = 0;
		$staying_count  = 0;
		$rgb_color      = array();

		$query = new \WP_Query(
			array(
				'post_type'      => 'slgc_activityres',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_reservation_checkin',
						'value'   => $dayafter,
						'compare' => '=',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$activity_id    = get_post_meta( get_the_ID(), 'staylodgic_activity_id', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_reservation_checkin', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $dayafter ) {

						// Increment the count for the activity_id
						if ( ! isset( $this->activity_labels[ $activity_id ][ $dayafter ] ) ) {
							$this->activity_labels[ $activity_id ][ $dayafter ]['count'] = 0;
						}
						++$this->activity_labels[ $activity_id ][ $dayafter ]['count'];
						$this->add_guest( $booking_number, 'dayafter', 'checkin', $checkin );
					}
				}
			}
		}
		wp_reset_postdata();

		$data = array();
		foreach ( $this->activity_labels as $activity_id => $labels ) {
			if ( isset( $labels[ $dayafter ]['count'] ) ) {
				$data[] = $labels[ $dayafter ]['count'];
			} else {
				$data[] = 0; // Set count to 0 if not found
			}
		}

		return array(
			'labels'   => array_column( $this->activity_labels, 'label' ),
			'datasets' => array(
				array(
					'data'            => $data,
					'backgroundColor' => $this->activity_colors,
				),
			),
		);
	}

	/**
	 * Method add_guest
	 *
	 * @param $booking_number
	 * @param $day $day
	 * @param $type $type
	 * @param $checkin $checkin
	 * @param $checkout $checkout
	 *
	 * @return void
	 */
	private function add_guest( $booking_number = false, $day = 'today', $type = 'checkin', $checkin = false, $checkout = false ) {
		if ( $booking_number ) {
			// Fetch guest details
			$reservation_instance = new \Staylodgic\Activity();
			$stay_guest_id        = $reservation_instance->get_guest_id_for_reservation( $booking_number );
			if ( $stay_guest_id ) {
				$name = esc_html( get_post_meta( $stay_guest_id, 'staylodgic_full_name', true ) );

				// Generate a UUID using the static method from the Common class
				$uuid = \Staylodgic\Common::generate_uuid();

				// Use the combination of stay_guest_id and UUID as the key
				$unique_key = $stay_guest_id . '-' . $uuid;

				$this->guests[ $day ][ $type ][ $stay_guest_id ][ $unique_key ]['booking_number'] = $booking_number;
				$this->guests[ $day ][ $type ][ $stay_guest_id ][ $unique_key ]['name']           = $name;
				$this->guests[ $day ][ $type ][ $stay_guest_id ][ $unique_key ]['checkin']        = $checkin;
				$this->guests[ $day ][ $type ][ $stay_guest_id ][ $unique_key ]['checkout']       = $checkout;
			}
		}
	}

	/**
	 * Method get_tomorrow_stats_data
	 *
	 * @return void
	 */
	private function get_tomorrow_stats_data() {
		$tomorrow       = gmdate( 'Y-m-d', strtotime( '+1 day' ) );
		$checkin_count  = 0;
		$checkout_count = 0;
		$staying_count  = 0;

		$query = new \WP_Query(
			array(
				'post_type'      => 'slgc_activityres',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_reservation_checkin',
						'value'   => $tomorrow,
						'compare' => '=',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$activity_id    = get_post_meta( get_the_ID(), 'staylodgic_activity_id', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_reservation_checkin', true );
				$checkout       = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $tomorrow ) {

						// Increment the count for the activity_id
						if ( ! isset( $this->activity_labels[ $activity_id ][ $tomorrow ] ) ) {
							$this->activity_labels[ $activity_id ][ $tomorrow ]['count'] = 0;
						}
						++$this->activity_labels[ $activity_id ][ $tomorrow ]['count'];
						$this->add_guest( $booking_number, 'tomorrow', 'checkin', $checkin );
					}
				}
			}
		}
		wp_reset_postdata();

		$data = array();
		foreach ( $this->activity_labels as $activity_id => $labels ) {
			if ( isset( $labels[ $tomorrow ]['count'] ) ) {
				$data[] = $labels[ $tomorrow ]['count'];
			} else {
				$data[] = 0; // Set count to 0 if not found
			}
		}

		return array(
			'labels'   => array_column( $this->activity_labels, 'label' ),
			'datasets' => array(
				array(
					'data'            => $data,
					'backgroundColor' => $this->activity_colors,
				),
			),
		);
	}

	/**
	 * Method get_current_day_stats_data
	 *
	 * @return void
	 */
	private function get_current_day_stats_data() {
		$today          = gmdate( 'Y-m-d' );
		$checkin_count  = 0;
		$checkout_count = 0;
		$staying_count  = 0;

		$query = new \WP_Query(
			array(
				'post_type'      => 'slgc_activityres',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_reservation_checkin',
						'value'   => $today,
						'compare' => '=',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$activity_id    = get_post_meta( get_the_ID(), 'staylodgic_activity_id', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_reservation_checkin', true );
				$checkout       = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $today ) {

						// Increment the count for the activity_id
						if ( ! isset( $this->activity_labels[ $activity_id ][ $today ] ) ) {
							$this->activity_labels[ $activity_id ][ $today ]['count'] = 0;
						}
						++$this->activity_labels[ $activity_id ][ $today ]['count'];
						$this->add_guest( $booking_number, 'today', 'checkin', $checkin );
					}
				}
			}
		}
		wp_reset_postdata();

		$data = array();
		foreach ( $this->activity_labels as $activity_id => $labels ) {
			if ( isset( $labels[ $today ]['count'] ) ) {
				$data[] = $labels[ $today ]['count'];
			} else {
				$data[] = 0; // Set count to 0 if not found
			}
		}

		return array(
			'labels'   => array_column( $this->activity_labels, 'label' ),
			'datasets' => array(
				array(
					'data'            => $data,
					'backgroundColor' => $this->activity_colors,
				),
			),
		);
	}

	/**
	 * Method get_past_twelve_months_revenue_data
	 *
	 * @return void
	 */
	private function get_past_twelve_months_revenue_data() {
		$labels             = array();
		$revenue_data       = array();
		$stay_current_month = gmdate( 'Y-m' );
		$revenue_count      = 0;

		$cache = new \Staylodgic\Cache();

		for ( $i = 12; $i >= 0; $i-- ) {
			$month    = gmdate( 'Y-m', strtotime( "$stay_current_month -$i month" ) );
			$labels[] = gmdate( 'F', strtotime( $month ) );

			// Check if the data is cached
			$cache_key = $cache->generate_analytics_cache_key( 'analytics_activity_twelve_months_revenue_' . $month );

			if ( $cache->has_cache( $cache_key ) ) {
				// Use cached data
				$cached_data   = $cache->get_cache( $cache_key );
				$total_revenue = $cached_data;
			} else {

				// Query for revenue
				$revenue_query = new \WP_Query(
					array(
						'post_type'      => 'slgc_activityres',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_reservation_checkin',
								'value'   => $month,
								'compare' => 'LIKE',
							),
							array(
								'key'     => 'staylodgic_reservation_status',
								'value'   => 'confirmed',
								'compare' => '=',
							),
						),
					)
				);

				$total_revenue = 0;
				if ( $revenue_query->have_posts() ) {
					while ( $revenue_query->have_posts() ) {
						$revenue_query->the_post();
						$total_revenue += (float) get_post_meta( get_the_ID(), 'staylodgic_reservation_total_room_cost', true );
					}
				}
				wp_reset_postdata();

				// Cache the data if it's not the current month
				if ( $month !== $stay_current_month ) {
					$cache->setCache( $cache_key, $total_revenue );
				}
			}

			$revenue_data[] = $total_revenue;
			$revenue_count += intval( $total_revenue );
		}

		$this->bookings['revenue'] = $revenue_count;

		return array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label'         => __( 'Monthly Revenue', 'staylodgic' ),
					'data'          => $revenue_data,
					'useGradient'   => true,
					'gradientStart' => 'rgba(177, 14, 236,1)',
					'gradientEnd'   => 'rgba(83, 0, 255, 1)',
					'borderColor'   => 'rgba(75, 192, 192, 1)',
					'fill'          => false,
				),
			),
		);
	}

	/**
	 * Method get_past_twelve_months_bookings_data
	 *
	 * @return void
	 */
	private function get_past_twelve_months_bookings_data() {
		$labels             = array();
		$confirmed_data     = array();
		$cancelled_data     = array();
		$stay_current_month = gmdate( 'Y-m' );

		$confirmed_count = 0;
		$cancelled_count = 0;

		$cache = new \Staylodgic\Cache();

		for ( $i = 12; $i >= 0; $i-- ) {
			$month    = gmdate( 'Y-m', strtotime( "$stay_current_month -$i month" ) );
			$labels[] = gmdate( 'F', strtotime( $month ) );

			// Check if the data is cached
			$cache_key = $cache->generate_analytics_cache_key( 'analytics_activity_data_' . $month );

			if ( $cache->has_cache( $cache_key ) ) {
				// Use cached data
				$cached_data      = $cache->get_cache( $cache_key );
				$confirmed_data[] = $cached_data['confirmed'];
				$cancelled_data[] = $cached_data['cancelled'];
			} else {

				// Query for confirmed bookings
				$confirmed_query  = new \WP_Query(
					array(
						'post_type'      => 'slgc_activityres',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_reservation_checkin',
								'value'   => $month,
								'compare' => 'LIKE',
							),
							array(
								'key'     => 'staylodgic_reservation_status',
								'value'   => 'confirmed',
								'compare' => '=',
							),
						),
					)
				);
				$confirmed_data[] = $confirmed_query->found_posts;

				// Query for cancelled bookings
				$cancelled_query  = new \WP_Query(
					array(
						'post_type'      => 'slgc_activityres',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_reservation_checkin',
								'value'   => $month,
								'compare' => 'LIKE',
							),
							array(
								'key'     => 'staylodgic_reservation_status',
								'value'   => 'cancelled',
								'compare' => '=',
							),
						),
					)
				);
				$cancelled_data[] = $cancelled_query->found_posts;

				if ( $month !== $stay_current_month ) {
					$cache_data = array(
						'confirmed' => $confirmed_query->found_posts,
						'cancelled' => $cancelled_query->found_posts,
					);
					$cache->setCache( $cache_key, $cache_data );
				}
			}
		}

		// Calculate the total counts
		foreach ( $confirmed_data as $count ) {
			$confirmed_count += $count;
		}
		foreach ( $cancelled_data as $count ) {
			$cancelled_count += $count;
		}

		$this->bookings['confirmed'] = $confirmed_count;
		$this->bookings['cancelled'] = $cancelled_count;

		return array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label'           => __( 'Confirmed Bookings', 'staylodgic' ),
					'data'            => $confirmed_data,
					'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
					'borderColor'     => 'rgba(79,0,255,1)',
					'fill'            => false,
				),
				array(
					'label'           => __( 'Cancelled Bookings', 'staylodgic' ),
					'data'            => $cancelled_data,
					'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
					'borderColor'     => 'rgba(255,99,132,1)',
					'fill'            => false,
				),
			),
		);
	}

	/**
	 * Method chart_generator
	 *
	 * @param $activity_id $activity_id
	 *
	 * @return void
	 */
	public function chart_generator( $activity_id ) {

		$config = $this->get_chart_config( $activity_id );
		if ( ! $config ) {
			return 'Chart not found.';
		}

		$chart          = new \Staylodgic\Analytics_Activity( $activity_id, $config['info'], $config['type'], $config['data'], $config['options'], $this->guests );
		$rendered_chart = $chart->render();

		$chart_output = '';

		$chart_output .= '<div class="staylodgic_analytics_chart staylodgic_analytics_chart_' . esc_attr( $config['type'] ) . ' ">';
		$chart_output .= '<h2 class="staylodgic_analytics_subheading">';
		$chart_output .= $config['heading'];
		$chart_output .= '</h2>';
		$chart_output .= $rendered_chart;
		$chart_output .= '</div>';

		return $chart_output;
	}

	public function guest_list() {
		// Initialize the guest list HTML
		$guest_list_html = '';

		// Iterate over each day in the guests array
		foreach ( $this->guests as $day => $statuses ) {

			$guest_list_html .= '<div class="staylodgic_analytics_table_wrap">';
			// Add a heading for the day
			if ( 'today' === $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __( 'Today', 'staylodgic' ) . ' ' . wp_kses( $this->display_today, staylodgic_get_allowed_tags() ) . '</h2>';
			} elseif ( 'tomorrow' == $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __( 'Tomorrow', 'staylodgic' ) . ' ' . wp_kses( $this->display_tomorrow, staylodgic_get_allowed_tags() ) . '</h2>';
			} elseif ( 'dayafter' == $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . __( 'Day After', 'staylodgic' ) . ' ' . wp_kses( $this->display_dayafter, staylodgic_get_allowed_tags() ) . '</h2>';
			} else {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . $day . '">' . esc_html( ucfirst( $day ) ) . '</h2>';
			}
			// Sort the statuses array
			uksort(
				$statuses,
				function ( $a, $b ) {
					$order = array( 'checkin', 'staying', 'checkout' ); // Define your custom order
					return array_search( $a, $order ) - array_search( $b, $order );
				}
			);

			// Iterate over each status (staying, checkout, checkin) for the day
			foreach ( $statuses as $status => $guests ) {
				$count = 0;

				$font_icon = '';
				if ( 'checkin' === $status ) {
					$font_icon = '<i class="fas fa-sign-in-alt"></i> ';
				}
				if ( 'checkout' === $status ) {
					$font_icon = '<i class="fas fa-sign-out-alt"></i> ';
				}
				if ( 'staying' === $status ) {
					$font_icon = '<i class="fa-solid fa-bed"></i> ';
				}

				$guest_list_html .= '<div class="staylodgic_table_outer">';
				$guest_list_html .= '<h3>' . $font_icon . __( 'Activities', 'staylodgic' ) . '</h3>';

				$guest_list_html .= '<table class="staylodgic_analytics_table table table-hover" data-export-title="' . __( 'Activities for ', 'staylodgic' ) . esc_html( $day ) . '">';
				$guest_list_html .= '<thead class="table-light">';
				$guest_list_html .= '<tr>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-number number-column" scope="col"><i class="fas fa-hashtag"></i></th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-booking-number" scope="col"><i class="fas fa-hashtag"></i> ' . __( 'Booking', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-name" scope="col"><i class="fas fa-user"></i> ' . __( 'Guest Name', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-activity" scope="col"><i class="fas fa-bed"></i> ' . __( 'Activity', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-time" scope="col"><i class="fas fa-clock"></i> ' . __( 'Time', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th data-orderable="false" class="table-cell-heading table-cell-heading-persons" scope="col"><i class="fas fa-clipboard-list"></i> ' . __( 'Persons', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th data-orderable="false" class="table-cell-heading table-cell-heading-notes" scope="col"><i class="fas fa-sticky-note"></i> ' . __( 'Notes', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-checkin" scope="col"><i class="fas fa-sign-in-alt"></i> ' . __( 'Activity Date', 'staylodgic' ) . '</th>';
				$guest_list_html .= '</tr>';
				$guest_list_html .= '</thead>';
				$guest_list_html .= '<tbody class="table-group-divider">';
				// Iterate over each guest and add them to the table
				foreach ( $guests as $stay_guest_id => $bookings ) {

					foreach ( $bookings as $booking ) { // Iterate over each booking for the guest
						++$count;

						$reservations_instance = new \Staylodgic\Activity();
						$reservation_id        = $reservations_instance->get_activity_id_for_booking( $booking['booking_number'] );

						$stay_checkin_date  = new \DateTime( $booking['checkin'] );
						$stay_checkout_date = new \DateTime( $booking['checkout'] );
						$nights             = $stay_checkout_date->diff( $stay_checkin_date )->days;

						$guest_list_html .= '<tr>';
						$guest_list_html .= '<th class="number-column" scope="row">' . esc_html( $count ) . '</th>';
						$guest_list_html .= '<td scope="row">';
						$guest_list_html .= '<a href="' . esc_url( get_edit_post_link( $reservation_id ) ) . '">';
						$guest_list_html .= $booking['booking_number'];
						$guest_list_html .= '</a>';
						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';
						$guest_list_html .= ucwords( strtolower( $booking['name'] ) );
						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$room_name = $reservations_instance->get_activity_name_for_reservation( $reservation_id );

						$guest_list_html .= $room_name;
						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$guest_list_html .= $reservations_instance->get_activity_time( $reservation_id );

						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$adults   = $reservations_instance->get_number_of_adults_for_reservation( $reservation_id );
						$children = $reservations_instance->get_number_of_children_for_reservation( $reservation_id );

						$guest_list_html .= \Staylodgic\Common::generate_person_icons( $adults, $children );

						$guest_list_html .= '</td>';

						$notes             = get_post_meta( $reservation_id, 'staylodgic_reservation_notes', true );
						$notes_with_breaks = nl2br( $notes );

						$guest_list_html .= '<td scope="row">' . esc_html( $notes_with_breaks ) . '</td>';
						$guest_list_html .= '<td scope="row">' . esc_html( $booking['checkin'] ) . '</td>';
						$guest_list_html .= '</tr>';
					}
				}
				$guest_list_html .= '</tbody>';
				$guest_list_html .= '</table>';
				$guest_list_html .= '</div>';
			}
			$guest_list_html .= '</div>';
		}

		return $guest_list_html;
	}

	/**
	 * Method display_stats
	 *
	 * @return void
	 */
	public function display_stats() {

		$this->load_activities();

		$past_twelve_months_bookings = $this->chart_generator( 'past_twelve_months_bookings' );
		$past_twelve_months_revenue  = $this->chart_generator( 'past_twelve_months_revenue' );
		$bookings_today              = $this->chart_generator( 'bookings_today' );
		$bookings_tomorrow           = $this->chart_generator( 'bookings_tomorrow' );
		$bookings_dayafter           = $this->chart_generator( 'bookings_dayafter' );

		$guest_list_html = $this->guest_list();

		$row_one = '';

		$row_one .= '<div class="staylodgic_anaytlics_row_one">';
		$row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_today">' . $bookings_today . '</div>';
		$row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_tomorrow">' . $bookings_tomorrow . '</div>';
		$row_one .= '<div class="staylodgic_anaytlics_module staylodgic_chart_bookings_dayafter">' . $bookings_dayafter . '</div>';
		$row_one .= '</div>';

		$dashboard = $row_one . $guest_list_html . $past_twelve_months_bookings . $past_twelve_months_revenue;
		return $dashboard;
	}

	public function render() {
		$data    = htmlspecialchars( wp_json_encode( $this->data ), ENT_QUOTES, 'UTF-8' );
		$options = htmlspecialchars( wp_json_encode( $this->options ), ENT_QUOTES, 'UTF-8' );

		// Initialize the guest list HTML
		$guest_list_html = '';

		return '<canvas id="' . esc_attr( $this->activity_id ) . '" class="staylodgic-chart" data-type="' . esc_attr( $this->type ) . '" data-data="' . esc_attr( $data ) . '" data-options="' . esc_attr( $options ) . '"></canvas>' . $guest_list_html;
	}
}

$activity_id = false;
$analytics   = new \Staylodgic\Analytics_Activity( $activity_id );
