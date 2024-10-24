<?php
class Staylodgic_Room_Posts {


	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'staylodgic_enable_room_sort' ) );
		add_action( 'wp_ajax_room_sort', array( $this, 'staylodgic_save_room_order' ) );

		// Hook into `current_screen` to check which admin page is being viewed
		add_action( 'current_screen', array( $this, 'check_current_screen_for_room_posts' ) );
		add_action( 'current_screen', array( $this, 'sort_admin_init' ) );
	}

	public function check_current_screen_for_room_posts() {
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( 'toplevel_page_class-staylodgic-room-posts' === $screen && $screen->id ) {
				add_filter( 'posts_orderby', array( $this, 'staylodgic_room_orderby' ) );
			}
		}
	}

	/**
	 * Register Room columns
	 *
	 * @return void
	 */
	public function staylodgic_enable_room_sort() {
		add_submenu_page(
			'edit.php?post_type=slgc_room',
			'Sort rooms',
			'Sort Rooms',
			'edit_posts',
			'staylodgic-sort-rooms', // Use a unique and meaningful slug
			array( $this, 'staylodgic_sort_room' )
		);
	}

	public function staylodgic_room_orderby( $orderby ) {
		global $wpdb;
		$orderby = "{$wpdb->posts}.menu_order, {$wpdb->posts}.post_date DESC";
		return ( $orderby );
	}
	public function staylodgic_sort_room() {
		$room = new WP_Query( 'post_type=slgc_room&posts_per_page=-1&orderby=menu_order&order=ASC' );
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Sort room', 'staylodgic' ); ?> <img src="<?php echo esc_url( home_url() . '/wp-admin/images/loading.gif' ); ?>" id="loading-animation" /></h2>
			<div class="description">
				<?php esc_html_e( 'Drag and Drop the slides to order them', 'staylodgic' ); ?>
			</div>
			<ul id="portfolio-list">
				<?php
				while ( $room->have_posts() ) :
					$room->the_post();
					?>
					<li id="<?php the_id(); ?>">
						<div>
							<?php
							$image_url = wp_get_attachment_thumb_url( get_post_thumbnail_id() );
							$custom    = get_post_custom( get_the_ID() );
							$room_cats = get_the_terms( get_the_ID(), 'slgc_roomtype' );

							?>
							<?php
							if ( $image_url ) {
								echo '<img class="staylodgic_admin_sort_image" src="' . esc_url( $image_url ) . '" width="30px" height="30px" alt="" />';
							}
							?>
							<span class="staylodgic_admin_sort_title"><?php the_title(); ?></span>
							<?php
							if ( $room_cats ) {
								?>
								<span class="staylodgic_admin_sort_categories">
									<?php
									foreach ( $room_cats as $taxonomy ) {
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

	/**
	 * Save Room order
	 *
	 * @return void
	 */
	public function staylodgic_save_room_order() {

		// Check for nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'staylodgic-nonce-admin' ) ) {
			wp_die();
		}

		global $wpdb;

		$order   = explode( ',', $_POST['order'] );
		$counter = 0;

		foreach ( $order as $sort_id ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'menu_order' => intval( $counter ) ),
				array( 'ID' => intval( $sort_id ) )
			);
			++$counter;
		}
		die( 1 );
	}

	/**
	 * Register Room post
	 *
	 * @return void
	 */
	public function init() {
		/*
		 * Register Room Post
		 */
		$args = array(
			'labels'             => array(
				'name'          => __( 'Rooms', 'staylodgic' ),
				'add_new'       => __( 'Create a Room', 'staylodgic' ),
				'add_new_item'  => __( 'Add New Room', 'staylodgic' ),
				'menu_name'     => __( 'Rooms', 'staylodgic' ),
				'singular_name' => __( 'Room', 'staylodgic' ),
				'all_items'     => __( 'All Rooms', 'staylodgic' ),
			),
			'singular_label'     => __( 'Room', 'staylodgic' ),
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'has_archive'        => true,
			'menu_position'      => 35,
			'menu_icon'          => 'dashicons-superhero-alt',
			'rewrite'            => array( 'slug' => 'rooms' ),
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);

		register_post_type( 'slgc_room', $args );
		/*
		 * Add Taxonomy
		 */
		register_taxonomy(
			'slgc_roomtype',
			array( 'staylodgic_room' ),
			array(
				'hierarchical'   => true,
				'label'          => 'Room Category',
				'singular_label' => 'staylodgic_roomtypes',
				'rewrite'        => true,
			)
		);
	}
	/**
	 * Load styles and scripts
	 *
	 * @return void
	 */
	public function sort_admin_init() {
		if ( is_admin() ) {
			// Load only if in a Post or Page Manager
			if ( 'edit.php' === basename( $_SERVER['PHP_SELF'] ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'mtheme-activity-sorter-CSS', plugin_dir_url( __FILE__ ) . 'css/style.css', false, '1.0', 'all' );
				$screen = get_current_screen();
				if ( 'slgc_room_page_staylodgic-sort-rooms' === $screen->id ) {
					wp_enqueue_script( 'post-sorter-JS', plugin_dir_url( __FILE__ ) . 'js/post-sorter.js', array( 'jquery' ), '1.1', true );
				}
			}
		}
	}
}
$staylodgic_room_post_type = new Staylodgic_Room_Posts();
?>
