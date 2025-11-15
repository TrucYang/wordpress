<?php
/**
 * Plugin Name: Product View Count
 * Description: Advanced WooCommerce product view tracking with AI-powered analytics dashboard, trending badges, and comprehensive reporting.
 * Plugin URI:  https://wpplugines.com/
 * Author:      Al Imran Akash
 * Author URI:  https://profiles.wordpress.org/al-imran-akash/
 * Version: 	2.0.0
 * Text Domain: product-view-count
 * Domain Path: /languages
 *
 * Product_View_Count is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Product_View_Count is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace WPPlugines\Product_View_Count;
use Codexpert\Plugin\Notice;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the plugin
 * @package Plugin
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
final class Plugin {
	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * The constructor method
	 * 
	 * @access private
	 * 
	 * @since 0.9
	 */
	private function __construct() {
		/**
		 * Includes required files
		 */
		$this->include();

		/**
		 * Defines contants
		 */
		$this->define();

		/**
		 * Runs actual hooks
		 */
		$this->hook();
	}

	/**
	 * Includes files
	 * 
	 * @access private
	 * 
	 * @uses composer
	 * @uses psr-4
	 */
	private function include() {
		require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );
	}

	/**
	 * Define variables and constants
	 * 
	 * @access private
	 * 
	 * @uses get_plugin_data
	 * @uses plugin_basename
	 */
	private function define() {

		/**
		 * Define some constants
		 * 
		 * @since 0.9
		 */
		define( 'PVC', __FILE__ );
		define( 'PVC_DIR', dirname( PVC ) );
		define( 'PVC_ASSET', plugins_url( 'assets', PVC ) );
		define( 'PVC_DEBUG', apply_filters( 'product-view-count_debug', true ) );

		/**
		 * The plugin data
		 * 
		 * @since 0.9
		 * @var $plugin
		 */
		$this->plugin					= get_plugin_data( PVC );
		$this->plugin['basename']		= plugin_basename( PVC );
		$this->plugin['file']			= PVC;
		$this->plugin['server']			= apply_filters( 'wpplugines-plugin_server', 'https://wpplugines.com' );
		$this->plugin['min_php']		= '5.6';
		$this->plugin['min_wp']			= '4.0';
		$this->plugin['doc_id']			= 1960;
		$this->plugin['icon']			= PVC_ASSET . '/img/icon.png';
		$this->plugin['depends']		= [ 'woocommerce/woocommerce.php' => 'WooCommerce' ];
	}

	/**
	 * Hooks
	 * 
	 * @access private
	 * 
	 * Executes main plugin features
	 *
	 * To add an action, use $instance->action()
	 * To apply a filter, use $instance->filter()
	 * To register a shortcode, use $instance->register()
	 * To add a hook for logged in users, use $instance->priv()
	 * To add a hook for non-logged in users, use $instance->nopriv()
	 * 
	 * @return void
	 */
	private function hook() {

		if( is_admin() ) :

			/**
			 * Admin facing hooks
			 */
			$admin = new App\Admin( $this->plugin );
			$admin->activate( 'install' );
			$admin->action( 'admin_footer', 'modal' );
			$admin->action( 'plugins_loaded', 'i18n' );
			$admin->action( 'admin_enqueue_scripts', 'enqueue_scripts' );
			$admin->filter( "plugin_action_links_{$this->plugin['basename']}", 'action_links' );
			$admin->filter( 'plugin_row_meta', 'plugin_row_meta', 10, 2 );

			/**
			 * Migration handling
			 */
			$migration = new App\Migration( $this->plugin );

			// Run migration on activation or version update
			add_action( 'init', function() use ( $migration ) {
				// Check if this is a new activation or version update
				$current_version = get_option( 'pvc_plugin_version', '0' );
				$plugin_version = $this->plugin['Version'];

				if ( version_compare( $current_version, $plugin_version, '<' ) ) {
					// Update plugin version
					update_option( 'pvc_plugin_version', $plugin_version );

					// Run migration if needed
					$migration->maybe_run_migration();
				}
			} );

			// AJAX handlers for migration
			add_action( 'wp_ajax_pvc_trigger_migration', [ $migration, 'ajax_trigger_migration' ] );
			$admin->action( 'save_post', 'update_cache', 10, 3 );
			$admin->action( 'admin_footer_text', 'footer_text' );
			$admin->filter( 'manage_edit-product_columns', 'product_columns' );
			$admin->action( 'manage_product_posts_custom_column', 'view_count_columns', 10, 2 );
			$admin->filter( 'manage_edit-product_sortable_columns', 'sortable_columns' );
			$admin->action( 'pre_get_posts', 'view_orderby' );
			$admin->action( 'init', 'init_admin' );

			/**
			 * Product Metabox hooks
			 */
			$product_metabox = new App\ProductMetabox( $this->plugin );
			$product_metabox->action( 'add_meta_boxes', 'add_metabox' );
			$product_metabox->action( 'save_post_product', 'save_metabox' );



			/**
			 * Settings related hooks
			 */
			$settings = new App\Settings( $this->plugin );
			$settings->action( 'plugins_loaded', 'init_menu' );

			/**
			 * Renders different notices
			 * 
			 * @package wpplugines\Plugin
			 * 
 			 * @author Al Imran Akash <alimranakash.bd@gmail.com>
			 */
			$notice = new Notice( $this->plugin );

			/**
			 * Asks to participate in a survey
			 * 
			 * @package wpplugines\Plugin
			 * 
 			 * @author Al Imran Akash <alimranakash.bd@gmail.com>
			 */
			$survey = new App\Survey( $this->plugin );

		else : // !is_admin() ?

			/**
			 * Front facing hooks
			 */
			$front = new App\Front( $this->plugin );
			$front->action( 'wp_head', 'head' );
			$front->action( 'wp_footer', 'modal' );
			$front->action( 'wp_enqueue_scripts', 'enqueue_scripts' );
			$front->action( 'admin_bar_menu', 'add_admin_bar', 70 );
			// $front->action( 'woocommerce_after_shop_loop_item', 'shop_loop_item' );
			// $front->action( 'woocommerce_single_product_summary', 'display_product_view_count' );
			$front->action( 'template_redirect', 'track_product_view' );

			// Debug action for testing (only if WP_DEBUG is enabled)
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$front->action( 'wp_ajax_pvc_test_tracking', 'ajax_test_tracking' );
				$front->action( 'wp_ajax_nopriv_pvc_test_tracking', 'ajax_test_tracking' );
			}

			/**
			 * Frontend Display hooks
			 */
			$frontend_display = new App\FrontendDisplay( $this->plugin );
			$frontend_display->action( 'init', 'init_frontend_hooks' );


			/**
			 * Shortcode related hooks
			 */
			$shortcode = new App\Shortcode( $this->plugin );
			$shortcode->register( 'product_view_grid', 'render_product_view_grid' );

		endif;

		/**
		 * Api related hooks
		 */
		$api = new App\Api( $this->plugin );
		$api->action( 'rest_api_init', 'register_routes' );

		/**
		 * Widget hooks
		 *
		 * Executes on both the admin area and front area
		 */
		$widget = new App\Widget( $this->plugin );
		$widget->action( 'widgets_init', 'register_product_view_count_widget' );

		/**
		 * Shortcode registration
		 */
		if ( ! is_admin() ) {
			// $frontend_display = new App\FrontendDisplay( $this->plugin );
			// add_shortcode( 'product_view_count', [ $frontend_display, 'view_count_shortcode' ] );
		}



		/**
		 * WooCommerce Integration hooks
		 */
		$woocommerce = new App\WooCommerce( $this->plugin );
		$woocommerce->action( 'init', 'init_hooks' );

		/**
		 * Email Reports hooks
		 */
		$email_reports = new App\EmailReports( $this->plugin );
		$email_reports->action( 'init', 'init_hooks' );
	}

	/**
	 * Cloning is forbidden.
	 * 
	 * @access public
	 */
	public function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 * 
	 * @access public
	 */
	public function __wakeup() { }

	/**
	 * Instantiate the plugin
	 * 
	 * @access public
	 * 
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


}

Plugin::instance();