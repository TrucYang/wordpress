<?php
/**
 * All admin facing functions
 */
namespace WPPlugines\Product_View_Count\App;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Metabox;
use WPPlugines\Product_View_Count\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class Admin extends Base {

	public $plugin;
	private $database;
	private $api;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin = [] ) {
		$this->plugin	= $plugin;
		$this->slug		= $plugin['TextDomain'] ?? 'product-view-count';
		$this->name		= $plugin['Name'] ?? 'Product View Count';
		$this->server	= $plugin['server'] ?? 'https://wpplugines.com';
		$this->version	= $plugin['Version'] ?? '2.0.0';

		// Initialize database and API
		$this->database = new Database( $plugin );
		$this->api = new API( $plugin );
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'product-view-count', false, PVC_DIR . '/languages/' );
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {

		if( ! get_option( 'product-view-count_version' ) ){
			update_option( 'product-view-count_version', $this->version );
		}

		if( ! get_option( 'product-view-count_install_time' ) ){
			update_option( 'product-view-count_install_time', time() );
		}

		// Create database tables
		$this->database->maybe_create_tables();

		// Trigger migration if needed
		$migration = new Migration( $this->plugin );
		$migration->maybe_run_migration();
	}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'PVC_DEBUG' ) && PVC_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", PVC ), '', $this->version, 'all' );
		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", PVC ), [ 'jquery' ], $this->version, true );

		// Localize script for admin functionality
		wp_localize_script( $this->slug, 'pvcAdmin', [
			'restUrl' => rest_url( 'pvc/v1/' ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'strings' => [
				'confirmReset' => __( 'Are you sure you want to reset this product\'s view count?', 'product-view-count' ),
				'resetSuccess' => __( 'View count reset successfully!', 'product-view-count' ),
				'resetError' => __( 'Error resetting view count: ', 'product-view-count' )
			]
		] );

		// Dashboard assets are now loaded dynamically via JavaScript in Settings
	}

	public function action_links( $links ) {
		$this->admin_url = admin_url( 'admin.php' );

		$new_links = [
			'settings'	=> sprintf( '<a href="%1$s">' . __( 'Settings', 'product-view-count' ) . '</a>', add_query_arg( 'page', $this->slug, $this->admin_url ) )
		];
		
		return array_merge( $new_links, $links );
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		
		if ( $this->plugin['basename'] === $plugin_file ) {
			$plugin_meta['help'] = '<a href="https://help.wpplugines.com/" target="_blank" class="cx-help">' . __( 'Help', 'product-view-count' ) . '</a>';
		}

		return $plugin_meta;
	}

	public function update_cache( $post_id, $post, $update ) {
		wp_cache_delete( "pvc_{$post->post_type}", 'pvc' );
	}

	public function footer_text( $text ) {
		if( get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'If you like <strong>%1$s</strong>, please <a href="%2$s" target="_blank">leave us a %3$s rating</a> on WordPress.org! It\'d motivate and inspire us to make the plugin even better!', 'product-view-count' ), $this->name, "https://wordpress.org/support/plugin/{$this->slug}/reviews/?filter=5#new-post", '⭐⭐⭐⭐⭐' );
	}

	public function modal() {
		echo '
		<div id="product-view-count-modal" style="display: none">
			<img id="product-view-count-modal-loader" src="' . esc_attr( PVC_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}

	public function product_columns( $columns ) {
	    $display = Helper::get_option( 'product-view-count_basic', 'display_view_count' );

	    // Check if $display is not an array or 'admin' is not in the array
	    if ( ! is_array( $display ) || ! in_array( 'admin', $display ) ) {
	        return $columns;
	    }

	    // Insert the 'Views' column before the 'Date' column
	    $new_columns = array();

	    foreach ( $columns as $key => $value ) {
	        // Insert Views column before Date column
	        if ( $key === 'date' ) {
	            $new_columns['view'] = __( 'Views', 'woocommerce' );
	        }
	        $new_columns[$key] = $value;
	    }

	    // If 'date' column doesn't exist, add Views column at the end as fallback
	    if ( ! isset( $columns['date'] ) ) {
	        $new_columns['view'] = __( 'Views', 'woocommerce' );
	    }

	    return $new_columns;
	}


	public function view_count_columns( $column, $product_id ) {
		if ( $column == 'view' ) {
	        $view_count = get_post_meta( $product_id, 'product_view_count', true );
	        echo  $view_count ;
	    }
	}

	public function sortable_columns( $columns ) {
		$columns['view'] = 'product_view_count';
  		return $columns;
	}

	public function view_orderby( $query ) {
		if( ! is_admin() || ! $query->is_main_query() ) {
	    	return;
	  	}

	  	if ( 'product_view_count' === $query->get( 'orderby') ) {
	    	$query->set( 'orderby', 'meta_value' );
	    	$query->set( 'meta_key', 'product_view_count' );
	    	$query->set( 'meta_type', 'numeric' );
	  	}
	}

	/**
	 * Initialize admin hooks (menu removed - now handled by Settings)
	 */
	public function init_admin() {
		add_action( 'add_meta_boxes', [ $this, 'add_product_metabox' ] );
		add_action( 'admin_notices', [ $this, 'show_migration_notices' ] );
	}

	/**
	 * Show migration notices to admin users
	 */
	public function show_migration_notices() {
		// Only show to administrators
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check for migration notice
		$notice = get_transient( 'pvc_migration_notice' );
		if ( $notice ) {
			$class = $notice['type'] === 'success' ? 'notice-success' : 'notice-error';
			echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible">';
			echo '<p><strong>Product View Count:</strong> ' . esc_html( $notice['message'] ) . '</p>';
			echo '</div>';

			// Delete the transient after showing
			delete_transient( 'pvc_migration_notice' );
		}
	}

	/**
	 * Enqueue React dashboard assets
	 */
	public function enqueue_dashboard_assets() {
		// Check if built assets exist
		$admin_js = plugins_url( '/assets/dist/admin.js', PVC );
		$admin_css = plugins_url( '/assets/dist/main.css', PVC );

		if ( file_exists( PVC_DIR . '/assets/dist/admin.js' ) ) {
			wp_enqueue_script(
				'pvc-admin-dashboard',
				$admin_js,
				[],
				$this->version,
				true
			);

			// Localize script with API data
			wp_localize_script( 'pvc-admin-dashboard', 'pvcAdmin', [
				'apiUrl' => rest_url( 'pvc/v1' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'adminUrl' => admin_url( 'admin.php?page=product-view-count#product-view-count_analytics' ),
				'woocommerceUrl' => admin_url( 'edit.php?post_type=product' )
			] );
		}

		if ( file_exists( PVC_DIR . '/assets/dist/main.css' ) ) {
			wp_enqueue_style(
				'pvc-admin-dashboard',
				$admin_css,
				[],
				$this->version
			);
		}
	}

	/**
	 * Add product metabox for view count management
	 */
	public function add_product_metabox() {
		add_meta_box(
			'pvc-product-views',
			__( 'Product Views', 'product-view-count' ),
			[ $this, 'render_product_metabox' ],
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render product metabox
	 */
	public function render_product_metabox( $post ) {
		$view_count = get_post_meta( $post->ID, 'product_view_count', true ) ?: 0;

		wp_nonce_field( 'pvc_reset_product_nonce', 'pvc_reset_product_nonce' );
		?>
		<div class="pvc-metabox">
			<p><strong><?php _e( 'Total Views:', 'product-view-count' ); ?></strong> <?php echo number_format( $view_count ); ?></p>

			<p>
				<button type="button" class="button button-secondary" onclick="pvcResetProductCount(<?php echo $post->ID; ?>)">
					<?php _e( 'Reset View Count', 'product-view-count' ); ?>
				</button>
			</p>
		</div>
		<?php
	}
}