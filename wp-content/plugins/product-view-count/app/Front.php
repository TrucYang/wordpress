<?php
/**
 * All public facing functions
 */
namespace WPPlugines\Product_View_Count\App;
use Codexpert\Plugin\Base;
use WPPlugines\Product_View_Count\Helper;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class Front extends Base {

	public $plugin;
	private $database;
	private $tracked_products = []; // Track which products have been counted in this request

	/**
	 * Constructor function
	 */
	public function __construct( $plugin = [] ) {
		$this->plugin	= $plugin;
		$this->slug		= $plugin['TextDomain'] ?? 'product-view-count';
		$this->name		= $plugin['Name'] ?? 'Product View Count';
		$this->version	= $plugin['Version'] ?? '2.0.0';
	}

	public function add_admin_bar( $admin_bar ) {
		if( ! current_user_can( 'manage_options' ) ) return;

		$admin_bar->add_menu( [
			'id'    => $this->slug,
			'title' => $this->name,
			'href'  => add_query_arg( 'page', $this->slug, admin_url( 'admin.php' ) ),
			'meta'  => [
				'title' => $this->name,            
			],
		] );
	}

	public function head() {}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'PVC_DEBUG' ) && PVC_DEBUG ? '' : '.min';

		// if ( shortcode_exists( 'product_view_grid' ) ) {
	        // wp_enqueue_style( 'woocommerce-general' );
	    // }

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", PVC ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", PVC ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'PVC', apply_filters( "{$this->slug}-localized", $localized ) );
	}

	public function shop_loop_item() {

		$display = Helper::get_option( 'product-view-count_basic', 'display_view_count' );

		if ( in_array( 'shop', $display ) ) {
			global $product;

			// Get the current product ID.
	 		$product_id = $product->get_id();

	 		// Get the current view count for the product.
	 		$view_count = get_post_meta( $product_id, 'product_view_count', true );

	 		// If the view count is empty, set it to 0.
	 		if ( empty( $view_count ) ) {
		        $view_count = 0;
		    }

		    printf( '<div id="product-view-count-panel"><span class="product-view-count">%s %d</span></div>', __( 'Views: ', 'product-view-count' ), esc_html( $view_count ) );
		}
	}

	public function product_view_count() {
		// Get the current product ID.
	    $product_id = get_the_ID();

	    // Skip if not a product
	    if ( get_post_type( $product_id ) !== 'product' ) {
	        return;
	    }

	    // Prevent duplicate tracking in the same request
	    if ( in_array( $product_id, $this->tracked_products ) ) {
	        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	            error_log( "PVC: Product {$product_id} already tracked in this request, skipping (product_view_count)" );
	        }
	        return;
	    }

	    // Mark this product as tracked
	    $this->tracked_products[] = $product_id;

	    // Initialize database if needed
	    if ( ! isset( $this->database ) ) {
	        $this->database = new Database( $this->plugin );
	        $this->database->maybe_create_tables();
	    }

	    // Record the view with advanced tracking
	    $this->database->record_view( $product_id );
	}

	public function track_product_view() {
		// Only track on single product pages
		if ( ! is_product() ) {
			return;
		}

		// Get the current product ID
		$product_id = get_the_ID();

		// Skip if not a valid product
		if ( ! $product_id || get_post_type( $product_id ) !== 'product' ) {
			return;
		}

		// Prevent duplicate tracking in the same request
		if ( in_array( $product_id, $this->tracked_products ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( "PVC: Product {$product_id} already tracked in this request, skipping" );
			}
			return;
		}

		// Mark this product as tracked
		$this->tracked_products[] = $product_id;

		// Initialize database if needed
		if ( ! isset( $this->database ) ) {
			$this->database = new Database( $this->plugin );
			$this->database->maybe_create_tables();
		}

		// Debug logging
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "PVC: Tracking view for product ID: {$product_id}" );
		}

		// Record the view with advanced tracking
		$result = $this->database->record_view( $product_id );

		// Debug logging
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "PVC: View recording result: " . ( $result ? 'success' : 'failed' ) );
		}
	}

	/**
	 * Test function to manually trigger view tracking (for debugging)
	 */
	public function test_view_tracking( $product_id = null ) {
		if ( ! $product_id ) {
			// Get first product for testing
			$products = get_posts( [
				'post_type' => 'product',
				'posts_per_page' => 1,
				'post_status' => 'publish'
			] );

			if ( empty( $products ) ) {
				return "No products found for testing";
			}

			$product_id = $products[0]->ID;
		}

		// Clear tracking for this test
		$this->tracked_products = [];

		// Initialize database if needed
		if ( ! isset( $this->database ) ) {
			$this->database = new Database( $this->plugin );
			$this->database->force_create_tables(); // Force create for testing
		}

		// Record a test view
		$result = $this->database->record_view( $product_id );

		// Get current count
		$count = get_post_meta( $product_id, 'product_view_count', true );

		return "Test result for product {$product_id}: " . ( $result ? 'success' : 'failed' ) . ". Current count: {$count}. Tracked products in this request: " . implode( ', ', $this->tracked_products );
	}

	/**
	 * AJAX handler for testing view tracking
	 */
	public function ajax_test_tracking() {
		// Only allow if WP_DEBUG is enabled
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			wp_die( 'Debug mode not enabled' );
		}

		$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : null;
		$result = $this->test_view_tracking( $product_id );

		// Also test database tables
		global $wpdb;
		$views_table = $wpdb->prefix . 'pvc_view_logs';
		$aggregated_table = $wpdb->prefix . 'pvc_product_views';

		$views_exist = $wpdb->get_var( "SHOW TABLES LIKE '$views_table'" );
		$aggregated_exist = $wpdb->get_var( "SHOW TABLES LIKE '$aggregated_table'" );

		$debug_info = "\n\nDEBUG INFO:\n";
		$debug_info .= "Views table exists: " . ( $views_exist ? 'YES' : 'NO' ) . "\n";
		$debug_info .= "Aggregated table exists: " . ( $aggregated_exist ? 'YES' : 'NO' ) . "\n";

		if ( $views_exist && $product_id ) {
			$view_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $views_table WHERE product_id = %d", $product_id ) );
			$debug_info .= "Views in database for product {$product_id}: {$view_count}\n";
		}

		wp_die( $result . $debug_info );
	}

	public function display_product_view_count() {

		$display = Helper::get_option( 'product-view-count_basic', 'display_view_count' );

		if ( in_array( 'single', $display ) ) {

			global $post;

		    // Get the current post ID.
		    $post_id = $post->ID;

		    // Get the current view count for the product.
		    $view_count = get_post_meta( $post_id, 'product_view_count', true );

		    // If the view count is empty, set it to 0.
		    if ( empty( $view_count ) ) {
		        $view_count = 0;
		    }

		    // Display the view count.
		    echo '<p class="product-view-count">'. __( 'Views: ', 'product-view-count' ) .' ' . esc_attr( $view_count ) . '</p>';
		}
	}



	public function modal() {
		echo '
		<div id="product-view-count-modal" style="display: none">
			<img id="product-view-count-modal-loader" src="' . esc_attr( PVC_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}