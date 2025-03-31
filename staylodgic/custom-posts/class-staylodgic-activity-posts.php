<?php

namespace Staylodgic;

class Staylodgic_Activity_Posts {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'staylodgic_enable_activity_sort' ) );
		add_action( 'wp_ajax_activity_sort', array( $this, 'staylodgic_save_activity_order' ) );

		add_action( 'current_screen', array( $this, 'check_current_screen' ) );
		add_action( 'current_screen', array( $this, 'sort_admin_init' ) );
	}

	public function check_current_screen() {
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( 'toplevel_page_class-imaginem-activity-posts' === $screen && $screen->id ) {
				add_filter( 'posts_orderby', array( $this, 'staylodgic_activity_orderby' ) );
			}
		}
	}

	/**
	 * Activity sort
	 *
	 * @return void
	 */
	public function staylodgic_enable_activity_sort() {
		add_submenu_page(
			'edit.php?post_type=staylodgic_actvties',
			__( 'Sort activities', 'staylodgic' ),
			__( 'Sort Activities', 'staylodgic' ),
			'edit_posts',
			'staylodgic-sort-activities', // Use a unique and meaningful slug
			array( $this, 'staylodgic_sort_activity' )
		);
	}

	public function staylodgic_activity_orderby( $orderby ) {
		global $wpdb;
		$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
		return ( $orderby );
	}

	public function staylodgic_sort_activity() {
		$activity = new \WP_Query( 'post_type=staylodgic_actvties&posts_per_page=-1&orderby=menu_order&order=ASC' );
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Sort activity', 'staylodgic' ); ?> <img src="<?php echo esc_url( home_url() . '/wp-admin/images/loading.gif' ); ?>" id="loading-animation" /></h2>
			<div class="description">
				<?php esc_html_e( 'Drag and Drop the slides to order them', 'staylodgic' ); ?>
			</div>
			<ul id="portfolio-list">
				<?php
				while ( $activity->have_posts() ) :
					$activity->the_post();
					?>
					<li id="<?php the_id(); ?>">
						<div>
							<?php
							$image_url     = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
							$custom        = get_post_custom( get_the_ID() );
							$activity_cats = get_the_terms( get_the_ID(), 'staylodgic_actvtiestype' );

							?>
							<?php
							if ( $image_url ) {
								echo '<img class="staylodgic_admin_sort_image" src="' . esc_url( $image_url ) . '" width="30px" height="30px" alt="" />';
							}
							?>
							<span class="staylodgic_admin_sort_title"><?php the_title(); ?></span>
							<?php
							if ( $activity_cats ) {
								?>
								<span class="staylodgic_admin_sort_categories">
									<?php
									foreach ( $activity_cats as $taxonomy ) {
										echo ' | ' . esc_html( $taxonomy->name );
									}
									?>
								</span>
								<?php
							}
							?>
						</div>

					</li>
				<?php endwhile; ?>
		</div>

		<?php
	}
	public function staylodgic_save_activity_order() {

		// Check for nonce security
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'staylodgic-nonce-admin' ) ) {
			wp_die( esc_html__( 'Invalid nonce.', 'staylodgic' ), 403 );
		}

		// Ensure user has the correct capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access.', 'staylodgic' ), 403 );
		}

		// Validate 'order' input
		if ( ! isset( $_POST['order'] ) || empty( $_POST['order'] ) || ( ! is_string( $_POST['order'] ) && ! is_array( $_POST['order'] ) ) ) {
			wp_die( esc_html__( 'Invalid data.', 'staylodgic' ), 400 );
		}

		// Sanitize and process the 'order' input
		$order_raw = sanitize_text_field( wp_unslash( $_POST['order'] ) );
		$order_ids = explode( ',', $order_raw );

		// Loop through the order and update each post's menu order
		if ( is_array( $order_ids ) && ! empty( $order_ids ) ) {
			$counter = 0;

			foreach ( $order_ids as $sort_id ) {
				// Sanitize each ID
				$sort_id = intval( sanitize_text_field( $sort_id ) );

				// Update the post using wp_update_post()
				if ( $sort_id > 0 ) { // Ensure valid IDs
					wp_update_post(
						array(
							'ID'         => $sort_id,
							'menu_order' => $counter,
						)
					);
					++$counter;
				}
			}
		}

		// Send a success response
		wp_send_json_success( 'Activity order updated successfully.' );
	}

	/**
	 *
	 * @return    void
	 */
	public function init() {
		/*
		 * Register activity Post Manager
		 */
		$labels = array(
			'name'               => _x( 'Activities', 'post type general name', 'staylodgic' ),
			'singular_name'      => _x( 'Activity', 'post type singular name', 'staylodgic' ),
			'menu_name'          => _x( 'Activities', 'admin menu', 'staylodgic' ),
			'name_admin_bar'     => _x( 'Activity', 'add new on admin bar', 'staylodgic' ),
			'add_new'            => _x( 'Add New Activity', 'activity', 'staylodgic' ),
			'add_new_item'       => __( 'Add New Activity', 'staylodgic' ),
			'new_item'           => __( 'New Activity', 'staylodgic' ),
			'edit_item'          => __( 'Edit Activity', 'staylodgic' ),
			'view_item'          => __( 'View Activity', 'staylodgic' ),
			'all_items'          => __( 'All Activities', 'staylodgic' ),
			'search_items'       => __( 'Search Activities', 'staylodgic' ),
			'parent_item_colon'  => __( 'Parent Activities:', 'staylodgic' ),
			'not_found'          => __( 'No activities found.', 'staylodgic' ),
			'not_found_in_trash' => __( 'No activities found in Trash.', 'staylodgic' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'has_archive'        => true,
			'menu_position'      => 39,
			'menu_icon'          => 'dashicons-location-alt',
			'rewrite'            => array( 'slug' => 'activities' ),
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);

		register_post_type( 'staylodgic_actvties', $args );
		/*
		 * Add Taxonomy for activity
		 */
		register_taxonomy(
			'staylodgic_actvtiestype',
			array( 'staylodgic_actvties' ),
			array(
				'hierarchical'   => true,
				'label'          => 'Activity Category',
				'singular_label' => 'staylodgic_activitytypes',
				'rewrite'        => true,
			)
		);
	}
	/**
	 * Enqueue Scripts and Styles
	 *
	 * @return    void
	 */
	public function sort_admin_init() {
		if ( is_admin() ) {
			// Load only if in a Post or Page Manager
			if ( isset( $_SERVER['PHP_SELF'] ) && 'edit.php' === basename( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'mtheme-activity-sorter-CSS', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '1.0', 'all' );
				$screen = get_current_screen();

				if ( 'staylodgic_actvties_page_staylodgic-sort-activities' === $screen->id ) {
					wp_enqueue_script( 'post-sorter-JS', plugin_dir_url( __FILE__ ) . 'js/post-sorter.js', array( 'jquery' ), '1.1', true );
				}
			}
		}
	}
}
