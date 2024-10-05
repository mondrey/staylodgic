<?php

namespace Staylodgic;

class Analytics_Bookings {

	private $booking_id;
	private $info;
	private $type;
	private $data;
	private $options;
	private $guests;
	private $bookings;
	private $display_today;
	private $display_tomorrow;
	private $display_dayafter;

	public function __construct( $booking_id, $info = 'today', $type = 'bar', $data = array(), $options = array(), $guests = array(), $bookings = array() ) {
		$this->booking_id = $booking_id;
		$this->info       = $info;
		$this->type       = $type;
		$this->data       = $data;
		$this->options    = $options;
		$this->guests     = $guests;
		$this->bookings   = $bookings;

		$this->display_today    = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS' ) ) . '</span>';
		$this->display_tomorrow = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS', strtotime( '+1 day' ) ) ) . '</span>';
		$this->display_dayafter = '<span class="display-stat-date">' . esc_html( gmdate( 'M jS', strtotime( '+2 day' ) ) ) . '</span>';

		add_action( 'admin_menu', array( $this, 'staylodgic_dashboard' ) );
	}

	/**
	 * Method staylodgic_dashboard
	 *
	 * @return void
	 */
	public function staylodgic_dashboard() {
		add_menu_page(
			__( 'Overview', 'staylodgic' ),
			__( 'Overview', 'staylodgic' ),
			'edit_posts',
			'slgc-dashboard',
			array( $this, 'display_dashboard' ),
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48IS0tIUZvbnQgQXdlc29tZSBGcmVlIDYuNS4yIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlL2ZyZWUgQ29weXJpZ2h0IDIwMjQgRm9udGljb25zLCBJbmMuLS0+PHBhdGggZmlsbD0iIzYzRTZCRSIgZD0iTTAgMjU2YTI1NiAyNTYgMCAxIDEgNTEyIDBBMjU2IDI1NiAwIDEgMSAwIDI1NnpNMjg4IDk2YTMyIDMyIDAgMSAwIC02NCAwIDMyIDMyIDAgMSAwIDY0IDB6TTI1NiA0MTZjMzUuMyAwIDY0LTI4LjcgNjQtNjRjMC0xNy40LTYuOS0zMy4xLTE4LjEtNDQuNkwzNjYgMTYxLjdjNS4zLTEyLjEtLjItMjYuMy0xMi4zLTMxLjZzLTI2LjMgLjItMzEuNiAxMi4zTDI1Ny45IDI4OGMtLjYgMC0xLjMgMC0xLjkgMGMtMzUuMyAwLTY0IDI4LjctNjQgNjRzMjguNyA2NCA2NCA2NHpNMTc2IDE0NGEzMiAzMiAwIDEgMCAtNjQgMCAzMiAzMiAwIDEgMCA2NCAwek05NiAyODhhMzIgMzIgMCAxIDAgMC02NCAzMiAzMiAwIDEgMCAwIDY0em0zNTItMzJhMzIgMzIgMCAxIDAgLTY0IDAgMzIgMzIgMCAxIDAgNjQgMHoiLz48L3N2Zz4=',
			32 // Position parameter
		);

		// Add the first submenu page. Often this duplicates the main menu page.
		add_submenu_page(
			'slgc-dashboard',          // Parent slug
			__( 'Bookings Overview', 'staylodgic' ),                    // Page title
			__( 'Bookings Overview', 'staylodgic' ),                    // Menu title
			'edit_posts',               // Capability
			'slgc-dashboard',          // Menu slug
			array( $this, 'display_dashboard' ) // Callback function
		);
	}

	/**
	 * Method display_dashboard
	 *
	 * @return void
	 */
	public function display_dashboard() {

		echo '<div class="staylodgic_analytics_wrap">';

		if ( \Staylodgic\Rooms::has_rooms() ) {
			// Add the logo image below the heading
			echo '<div class="staylodgic-overview-heading">';
			echo '<h1>' . __( 'Bookings Overview', 'staylodgic' ) . '</h1>';
			echo '</div>';
		} else {
			echo '<h1>' . __( 'No Rooms Found', 'staylodgic' ) . '</h1>';
			echo __( '<p>Please configure atleast 1 Room from Rooms section</p>', 'staylodgic' );
			return;
		}

		// Create an instance of the ChartGenerator class
		$analytics = new \Staylodgic\Analytics_Bookings( $booking_id = false );
		echo $analytics->display_stats();

		echo '</div>';
	}

	/**
	 * Method get_chart_config
	 *
	 * @param $booking_id
	 *
	 * @return void
	 */
	public function get_chart_config( $booking_id ) {

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
			'past_twelve_months_adr'      => array(
				'info'    => 'past_twelve_months_adr',
				'heading' => __( 'ADR for past twelve months', 'staylodgic' ),
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
								'display'           => true,
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
								'display'           => true,
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
								'display'           => true,
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
		if ( isset( $configs[ $booking_id ] ) ) {
			switch ( $booking_id ) {
				case 'past_twelve_months_bookings':
					$configs[ $booking_id ]['data'] = $this->get_past_twelve_months_bookings_data();
					break;
				case 'past_twelve_months_revenue':
					$configs[ $booking_id ]['data'] = $this->get_past_twelve_months_revenue_data();
					break;
				case 'past_twelve_months_adr':
					$configs[ $booking_id ]['data'] = $this->get_past_twelve_months_adr_data();
					break;
				case 'bookings_today':
					$configs[ $booking_id ]['data'] = $this->get_current_day_stats_data();
					break;
				case 'bookings_tomorrow':
					$configs[ $booking_id ]['data'] = $this->get_tomorrow_stats_data();
					break;
				case 'bookings_dayafter':
					$configs[ $booking_id ]['data'] = $this->get_dayafter_stats_data();
					break;
					// Add cases for other charts as needed...
			}
		}

		return $configs[ $booking_id ] ?? null;
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

		$query = new \WP_Query(
			array(
				'post_type'      => 'slgc_reservations',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $dayafter,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $dayafter,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $dayafter,
						'compare' => '<=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $dayafter,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_checkin_date', true );
				$checkout       = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $dayafter ) {
						++$checkin_count;
						$this->add_guest( $booking_number, 'dayafter', 'checkin', $checkin, $checkout );
					}
					if ( $checkout == $dayafter ) {
						++$checkout_count;
						$this->add_guest( $booking_number, 'dayafter', 'checkout', $checkin, $checkout );
					}
					if ( $checkin < $dayafter && $checkout > $dayafter ) {
						++$staying_count;
						$this->add_guest( $booking_number, 'dayafter', 'staying', $checkin, $checkout );
					}
				}
			}
		}
		wp_reset_postdata();

		return array(
			'labels'   => array( 'Check-ins', 'Check-outs', 'Staying' ),
			'datasets' => array(
				array(
					'data'            => array( $checkin_count, $checkout_count, $staying_count ),
					'backgroundColor' => array( 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ),
				),
			),
		);
	}

	/**
	 * Method add_guest
	 *
	 * @param $booking_number $booking_number
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
			$reservation_instance = new \Staylodgic\Reservations();
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

	private function get_tomorrow_stats_data() {
		$tomorrow       = gmdate( 'Y-m-d', strtotime( '+1 day' ) );
		$checkin_count  = 0;
		$checkout_count = 0;
		$staying_count  = 0;

		$query = new \WP_Query(
			array(
				'post_type'      => 'slgc_reservations',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $tomorrow,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $tomorrow,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $tomorrow,
						'compare' => '<=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $tomorrow,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_checkin_date', true );
				$checkout       = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $tomorrow ) {
						++$checkin_count;
						$this->add_guest( $booking_number, 'tomorrow', 'checkin', $checkin, $checkout );
					}
					if ( $checkout == $tomorrow ) {
						++$checkout_count;
						$this->add_guest( $booking_number, 'tomorrow', 'checkout', $checkin, $checkout );
					}
					if ( $checkin < $tomorrow && $checkout > $tomorrow ) {
						++$staying_count;
						$this->add_guest( $booking_number, 'tomorrow', 'staying', $checkin, $checkout );
					}
				}
			}
		}
		wp_reset_postdata();

		return array(
			'labels'   => array( 'Check-ins', 'Check-outs', 'Staying' ),
			'datasets' => array(
				array(
					'data'            => array( $checkin_count, $checkout_count, $staying_count ),
					'backgroundColor' => array( 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ),
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
				'post_type'      => 'slgc_reservations',
				'posts_per_page' => -1,
				'meta_query'     => array(
					'relation' => 'OR',
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $today,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $today,
						'compare' => '=',
					),
					array(
						'key'     => 'staylodgic_checkin_date',
						'value'   => $today,
						'compare' => '<=',
						'type'    => 'DATE',
					),
					array(
						'key'     => 'staylodgic_checkout_date',
						'value'   => $today,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$booking_number = get_post_meta( get_the_ID(), 'staylodgic_booking_number', true );
				$status         = get_post_meta( get_the_ID(), 'staylodgic_reservation_status', true );
				$checkin        = get_post_meta( get_the_ID(), 'staylodgic_checkin_date', true );
				$checkout       = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );

				if ( 'confirmed' === $status ) {
					if ( $checkin == $today ) {
						++$checkin_count;
						$this->add_guest( $booking_number, 'today', 'checkin', $checkin, $checkout );
					}
					if ( $checkout == $today ) {
						++$checkout_count;
						$this->add_guest( $booking_number, 'today', 'checkout', $checkin, $checkout );
					}
					if ( $checkin < $today && $checkout > $today ) {
						++$staying_count;
						$this->add_guest( $booking_number, 'today', 'staying', $checkin, $checkout );
					}
				}
			}
		}
		wp_reset_postdata();

		return array(
			'labels'   => array( 'Check-ins', 'Check-outs', 'Staying' ),
			'datasets' => array(
				array(
					'data'            => array( $checkin_count, $checkout_count, $staying_count ),
					'backgroundColor' => array( 'rgba(255,0,0,0.5)', 'rgba(83, 0, 255, 0.5)', 'rgba(255, 206, 86, 0.5)' ),
				),
			),
		);
	}

	/**
	 * Method get_past_twelve_months_adr_data
	 *
	 * @return void
	 */
	private function get_past_twelve_months_adr_data() {
		$labels             = array();
		$adr_data           = array();
		$stay_current_month = gmdate( 'Y-m' );

		$cache = new \Staylodgic\Cache();

		for ( $i = 12; $i >= 0; $i-- ) {
			$month    = gmdate( 'Y-m', strtotime( "$stay_current_month -$i month" ) );
			$labels[] = gmdate( 'F', strtotime( $month ) );

			// Check if the data is cached
			$cache_key = $cache->generate_analytics_cache_key( 'analytics_bookings_twelve_months_adr_' . $month );

			if ( $cache->has_cache( $cache_key ) ) {
				// Use cached data
				$cached_data = $cache->get_cache( $cache_key );
				$adr         = $cached_data;
			} else {

				// Query for revenue and nights
				$revenue_query = new \WP_Query(
					array(
						'post_type'      => 'slgc_reservations',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_checkin_date',
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
				$total_nights  = 0;
				if ( $revenue_query->have_posts() ) {
					while ( $revenue_query->have_posts() ) {
						$revenue_query->the_post();
						$total_revenue += (float) get_post_meta( get_the_ID(), 'staylodgic_reservation_total_room_cost', true );

						$checkin  = get_post_meta( get_the_ID(), 'staylodgic_checkin_date', true );
						$checkout = get_post_meta( get_the_ID(), 'staylodgic_checkout_date', true );
						if ( $checkin && $checkout ) {
							$stay_checkin_date  = new \DateTime( $checkin );
							$stay_checkout_date = new \DateTime( $checkout );
							$nights             = $stay_checkout_date->diff( $stay_checkin_date )->days;
							$total_nights      += $nights;
						}
					}
				}
				wp_reset_postdata();

				$adr = $total_nights > 0 ? round( $total_revenue / $total_nights ) : 0; // Round the ADR value

				// Cache the data if it's not the current month
				if ( $month !== $stay_current_month ) {
					$cache->setCache( $cache_key, $adr );
				}
			}

			$adr_data[] = $adr;
		}

		return array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label'         => __( 'Average Daily Rate (ADR)', 'staylodgic' ),
					'data'          => $adr_data,
					'useGradient'   => true,
					'gradientStart' => 'rgba(255,0,0,1)',
					'gradientEnd'   => 'rgba(83, 0, 255, 1)',
					'borderColor'   => 'rgba(75, 192, 192, 1)',
					'fill'          => false,
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
			$cache_key = $cache->generate_analytics_cache_key( 'analytics_bookings_twelve_months_revenue_' . $month );

			if ( $cache->has_cache( $cache_key ) ) {
				// Use cached data
				$cached_data   = $cache->get_cache( $cache_key );
				$total_revenue = $cached_data;
			} else {

				// Query for revenue
				$revenue_query = new \WP_Query(
					array(
						'post_type'      => 'slgc_reservations',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_checkin_date',
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
				if ( $month != $stay_current_month ) {
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
			$cache_key = $cache->generate_analytics_cache_key( 'analytics_bookings_data_' . $month );

			if ( $cache->has_cache( $cache_key ) ) {
				// Use cached data
				$cached_data      = $cache->get_cache( $cache_key );
				$confirmed_data[] = $cached_data['confirmed'];
				$cancelled_data[] = $cached_data['cancelled'];
			} else {

				// Query for confirmed bookings
				$confirmed_query  = new \WP_Query(
					array(
						'post_type'      => 'slgc_reservations',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_checkin_date',
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
						'post_type'      => 'slgc_reservations',
						'posts_per_page' => -1,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'staylodgic_checkin_date',
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
	 * @param $booking_id $booking_id
	 *
	 * @return void
	 */
	public function chart_generator( $booking_id ) {

		$config = $this->get_chart_config( $booking_id );
		if ( ! $config ) {
			return 'Chart not found.';
		}

		$chart          = new \Staylodgic\Analytics_Bookings( $booking_id, $config['info'], $config['type'], $config['data'], $config['options'], $this->guests );
		$rendered_chart = $chart->render();

		$chart_output = '';

		$chart_output .= '<div class="staylodgic_analytics_chart staylodgic_analytics_chart_' . $config['type'] . ' ">';
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

			$guest_list_html .= '<div class="staylodgic_analytics_table_wrap staylodgic-analytics-' . esc_attr( $day ) . '">';
			// Add a heading for the day
			if ( 'today' == $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . esc_attr( $day ) . '">' . __( 'Today', 'staylodgic' ) . ' ' . $this->display_today . '</h2>';
			} elseif ( 'tomorrow' == $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . esc_attr( $day ) . '">' . __( 'Tomorrow', 'staylodgic' ) . ' ' . $this->display_tomorrow . '</h2>';
			} elseif ( 'dayafter' == $day ) {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . esc_attr( $day ) . '">' . __( 'Day After', 'staylodgic' ) . ' ' . $this->display_dayafter . '</h2>';
			} else {
				$guest_list_html .= '<h2 class="staylodgic_analytics_subheading staylodgic_dayis_' . esc_attr( $day ) . '">' . esc_html( ucfirst( $day ) ) . '</h2>';
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
					$font_icon = '<i class="fas fa-sign-in-alt"></i>';
				}
				if ( 'checkout' === $status ) {
					$font_icon = '<i class="fas fa-sign-out-alt"></i>';
				}
				if ( 'staying' === $status ) {
					$font_icon = '<i class="fa-solid fa-bed"></i>';
				}

				$guest_list_html .= '<div class="staylodgic_table_outer">';
				$guest_list_html .= '<div class="staylodgic_table sub-heading"><h3>' . $font_icon . ' ' . ucfirst( $status ) . '</h3></div>';

				$guest_list_html .= '<table class="staylodgic_analytics_table table table-hover" data-export-title="Reservation - ' . $status . ' ' . esc_html( $day ) . '">';
				$guest_list_html .= '<thead class="table-light">';
				$guest_list_html .= '<tr>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-booking-number" scope="col"><i class="fas fa-hashtag"></i> ' . __( 'Booking', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-name" scope="col"><i class="fas fa-user"></i> ' . __( 'Guest Name', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-room" scope="col"><i class="fas fa-bed"></i> ' . __( 'Room', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th data-orderable="false" class="table-cell-heading table-cell-heading-registration" scope="col"><i class="fas fa-clipboard-list"></i> ' . __( 'Persons', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th data-orderable="false" class="table-cell-heading table-cell-heading-registration" scope="col"><i class="fas fa-clipboard-list"></i> ' . __( 'Registration', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th data-orderable="false" class="table-cell-heading table-cell-heading-notes" scope="col"><i class="fas fa-sticky-note"></i> ' . __( 'Notes', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-checkin" scope="col"><i class="fas fa-sign-in-alt"></i> ' . __( 'Check-in Date', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-checkout" scope="col"><i class="fas fa-sign-out-alt"></i> ' . __( 'Check-out Date', 'staylodgic' ) . '</th>';
				$guest_list_html .= '<th class="table-cell-heading table-cell-heading-nights nights-column" scope="col"><i class="fas fa-moon"></i> ' . __( 'Nights', 'staylodgic' ) . '</th>';
				$guest_list_html .= '</tr>';
				$guest_list_html .= '</thead>';
				$guest_list_html .= '<tbody class="table-group-divider">';
				// Iterate over each guest and add them to the table
				foreach ( $guests as $stay_guest_id => $bookings ) {

					foreach ( $bookings as $booking ) { // Iterate over each booking for the guest
						++$count;

						$reservations_instance = new \Staylodgic\Reservations();
						$reservation_id        = $reservations_instance->get_reservation_id_for_booking( $booking['booking_number'] );

						$stay_checkin_date  = new \DateTime( $booking['checkin'] );
						$stay_checkout_date = new \DateTime( $booking['checkout'] );
						$nights             = $stay_checkout_date->diff( $stay_checkin_date )->days;

						$guest_list_html .= '<tr>';
						$guest_list_html .= '<th scope="row">';
						$guest_list_html .= esc_html( $count ) . '. ';
						$guest_list_html .= '<a href="' . esc_url( get_edit_post_link( $reservation_id ) ) . '">';
						$guest_list_html .= $booking['booking_number'];
						$guest_list_html .= '</a>';
						$guest_list_html .= '</th>';
						$guest_list_html .= '<td scope="row">';
						$guest_list_html .= ucwords( strtolower( $booking['name'] ) );
						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$room_name = $reservations_instance->get_room_name_for_reservation( $reservation_id );
						$bedlayout = get_post_meta( $reservation_id, 'staylodgic_reservation_room_bedlayout', true );

						$guest_list_html .= $room_name;
						$guest_list_html .= '<div class="booking-dashboard bed-layout">' . staylodgic_get_all_bed_layouts( $bedlayout ) . '</div>';
						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$adults           = $reservations_instance->get_number_of_adults_for_reservation( $reservation_id );
						$children         = $reservations_instance->get_number_of_children_for_reservation( $reservation_id );
						$guest_list_html .= $adults;
						' + ' . $children;
						if ( 0 < $children ) {
							$guest_list_html .= ' + ' . $children;
						}

						$guest_list_html .= '</td>';
						$guest_list_html .= '<td scope="row">';

						$registry_instance = new \Staylodgic\GuestRegistry();
						$res_reg_ids       = $registry_instance->fetch_res_reg_ids_by_booking_number( $booking['booking_number'] );
						if ( isset( $res_reg_ids ) && is_array( $res_reg_ids ) ) {
							$guest_list_html .= $registry_instance->output_registration_and_occupancy( $res_reg_ids['reservationID'], $res_reg_ids['guestRegisterID'], 'default' );
							$guest_list_html .= '<div class="booking-dashboard registration">';
							$guest_list_html .= '<a title="View Registrations" href="' . get_edit_post_link( $res_reg_ids['reservationID'] ) . '"><i class="fa-solid fa-pen-to-square"></i></a>';
							$guest_list_html .= '<a title="Registration Link" href="' . get_permalink( $res_reg_ids['guestRegisterID'] ) . '"><i class="fa-regular fa-id-card"></i></a>';
							$guest_list_html .= '</div>';
						}
						$guest_list_html .= '</td>';

						$notes             = get_post_meta( $reservation_id, 'staylodgic_reservation_notes', true );
						$notes_with_breaks = nl2br( $notes );

						$guest_list_html .= '<td scope="row">' . $notes_with_breaks . '</td>';
						$guest_list_html .= '<td scope="row">' . $booking['checkin'] . '</td>';
						$guest_list_html .= '<td scope="row">' . $booking['checkout'] . '</td>';
						$guest_list_html .= '<td class="nights-column" scope="row">' . $nights . '</td>';
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

		$past_twelve_months_bookings = $this->chart_generator( 'past_twelve_months_bookings' );
		$past_twelve_months_revenue  = $this->chart_generator( 'past_twelve_months_revenue' );
		$past_twelve_months_adr      = $this->chart_generator( 'past_twelve_months_adr' );
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

		$dashboard = $row_one . $guest_list_html . $past_twelve_months_bookings . $past_twelve_months_revenue . $past_twelve_months_adr;
		return $dashboard;
	}

	/**
	 * Method render
	 *
	 * @return void
	 */
	public function render() {
		$data    = htmlspecialchars( wp_json_encode( $this->data ), ENT_QUOTES, 'UTF-8' );
		$options = htmlspecialchars( wp_json_encode( $this->options ), ENT_QUOTES, 'UTF-8' );

		// Initialize the guest list HTML
		$guest_list_html = '';

		return <<<HTML
	<canvas id="{$this->booking_id}" class="staylodgic-chart" data-type="{$this->type}" data-data="{$data}" data-options="{$options}"></canvas>
	$guest_list_html
	HTML;
	}
}

$booking_id = false;
$analytics  = new \Staylodgic\Analytics_Bookings( $booking_id );
