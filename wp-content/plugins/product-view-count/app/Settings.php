<?php
/**
 * All settings related functions
 */
namespace WPPlugines\Product_View_Count\App;
use WPPlugines\Product_View_Count\Helper;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Settings as Settings_API;

/**
 * @package Plugin
 * @subpackage Settings
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class Settings extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin = [] ) {
		$this->plugin	= $plugin;
		$this->slug		= $plugin['TextDomain'] ?? 'product-view-count';
		$this->name		= $plugin['Name'] ?? 'Product View Count';
		$this->version	= $plugin['Version'] ?? '2.0.0';
	}
	
	public function init_menu() {
		
		$site_config = [
			'PHP Version'				=> PHP_VERSION,
			'WordPress Version' 		=> get_bloginfo( 'version' ),
			'WooCommerce Version'		=> is_plugin_active( 'woocommerce/woocommerce.php' ) ? get_option( 'woocommerce_version' ) : 'Not Active',
			'Memory Limit'				=> defined( 'WP_MEMORY_LIMIT' ) && WP_MEMORY_LIMIT ? WP_MEMORY_LIMIT : 'Not Defined',
			'Debug Mode'				=> defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Enabled' : 'Disabled',
			'Active Plugins'			=> get_option( 'active_plugins' ),
		];

		$settings = [
			'id'            => $this->slug,
			'label'         => $this->name,
			'title'         => "{$this->name} v{$this->version}",
			'header'        => $this->name,
			// 'parent'     => 'woocommerce',
			// 'priority'   => 10,
			// 'capability' => 'manage_options',
			// 'icon'       => 'dashicons-wordpress',
			// 'position'   => 25,
			// 'topnav'	=> true,
			'sections'      => [
				'product-view-count_basic'	=> [
					'id'        => 'product-view-count_basic',
					'label'     => __( 'Basic Settings', 'product-view-count' ),
					'icon'      => 'dashicons-admin-tools',
					// 'color'		=> '#4c3f93',
					'sticky'	=> false,
					'fields'    => [
						'views_text' => [
							'id'        => 'views_text',
							'label'     => __( 'Label', 'product-view-count' ),
							'type'      => 'text',
							'default'   => __( 'View', 'product-view-count' ),
							'readonly'  => false, // true|false
							'disabled'  => false, // true|false
						],
						'display_view_count' => [
							'id'      => 'display_view_count',
							'label'     => __( 'Display View Count', 'product-view-count' ),
							'type'      => 'checkbox',
							'options'   => [
								'shop'  	=> __( 'Shop Page', 'product-view-count' ),
								'single' 	=> __( 'Single Page', 'product-view-count' ),
								'admin'  	=> __( 'Admin Product Column', 'product-view-count' ),
							],
							'default'   => [ 'shop', 'single', 'admin' ],
							'disabled'  => false, // true|false
							'multiple'  => true, // true|false
						],
						'ip_limit_hours' => [
							'id'        => 'ip_limit_hours',
							'label'     => __( 'IP Limit Hours', 'product-view-count' ),
							'type'      => 'number',
							'default'   => 1,
							'desc'      => __( 'Hours to wait before counting another view from the same IP address', 'product-view-count' ),
							'min'       => 0,
							'max'       => 24,
						],
						'exclude_bots' => [
							'id'        => 'exclude_bots',
							'label'     => __( 'Exclude Bots', 'product-view-count' ),
							'type'      => 'checkbox',
							'options'   => [
								'enabled' => __( 'Exclude known bots and crawlers from view counts', 'product-view-count' ),
							],
							'default'   => [ 'enabled' ],
							'multiple'  => true,
						],
					]
				],
				'product-view-count_trending' => [
					'id'        => 'product-view-count_trending',
					'label'     => __( 'Trending Products', 'product-view-count' ),
					'icon'      => 'dashicons-chart-line',
					'sticky'	=> false,
					'fields'    => [
						'trending_enabled' => [
							'id'        => 'trending_enabled',
							'label'     => __( 'Enable Trending Badges', 'product-view-count' ),
							'type'      => 'checkbox',
							'options'   => [
								'enabled' => __( 'Show trending badges on products', 'product-view-count' ),
							],
							'default'   => [ 'enabled' ],
							'multiple'  => true,
						],
						'trending_days' => [
							'id'        => 'trending_days',
							'label'     => __( 'Trending Period (Days)', 'product-view-count' ),
							'type'      => 'number',
							'default'   => 7,
							'desc'      => __( 'Number of days to consider for trending calculation', 'product-view-count' ),
							'min'       => 1,
							'max'       => 365,
						],
						'trending_threshold_type' => [
							'id'        => 'trending_threshold_type',
							'label'     => __( 'Trending Threshold Type', 'product-view-count' ),
							'type'      => 'select',
							'options'   => [
								'percentage' => __( 'Top Percentage', 'product-view-count' ),
								'fixed'      => __( 'Fixed Number of Views', 'product-view-count' ),
							],
							'default'   => 'percentage',
						],
						'trending_threshold_value' => [
							'id'        => 'trending_threshold_value',
							'label'     => __( 'Threshold Value', 'product-view-count' ),
							'type'      => 'number',
							'default'   => 10,
							'desc'      => __( 'For percentage: top X% of products. For fixed: minimum number of views', 'product-view-count' ),
							'min'       => 1,
						],
						'trending_badge_text' => [
							'id'        => 'trending_badge_text',
							'label'     => __( 'Badge Text', 'product-view-count' ),
							'type'      => 'text',
							'default'   => '🔥 Trending',
							'desc'      => __( 'Text to display on trending badges', 'product-view-count' ),
						],
					]
				],
				'product-view-count_email' => [
					'id'        => 'product-view-count_email',
					'label'     => __( 'Email Reports', 'product-view-count' ),
					'icon'      => 'dashicons-email-alt',
					'sticky'	=> false,
					'fields'    => [
						'weekly_enabled' => [
							'id'        => 'weekly_enabled',
							'label'     => __( 'Weekly Reports', 'product-view-count' ),
							'type'      => 'checkbox',
							'options'   => [
								'enabled' => __( 'Send weekly analytics reports', 'product-view-count' ),
							],
							'default'   => [],
							'multiple'  => true,
						],
						'monthly_enabled' => [
							'id'        => 'monthly_enabled',
							'label'     => __( 'Monthly Reports', 'product-view-count' ),
							'type'      => 'checkbox',
							'options'   => [
								'enabled' => __( 'Send monthly analytics reports', 'product-view-count' ),
							],
							'default'   => [],
							'multiple'  => true,
						],
						'email_recipients' => [
							'id'        => 'email_recipients',
							'label'     => __( 'Recipients', 'product-view-count' ),
							'type'      => 'textarea',
							'default'   => get_option( 'admin_email' ),
							'desc'      => __( 'Email addresses to send reports to (one per line)', 'product-view-count' ),
							'rows'      => 3,
						],
					]
				],
				'product-view-count_analytics'	=> [
					'id'        => 'product-view-count_analytics',
					'label'     => __( 'Analytics', 'product-view-count' ),
					'icon'      => 'dashicons-chart-bar',
					'sticky'	=> false,
					'hide_form'	=> true,
					'template'  => PVC_DIR . '/views/analytics.php',
					'fields'    => []
				],
				'product-view-count_migration'	=> [
					'id'        => 'product-view-count_migration',
					'label'     => __( 'Migration', 'product-view-count' ),
					'icon'      => 'dashicons-database-import',
					'sticky'	=> false,
					'hide_form'	=> true,
					'template'  => PVC_DIR . '/views/migration.php',
					'fields'    => []
				],
				'product-view-count_tools'	=> [
					'id'        => 'product-view-count_tools',
					'label'     => __( 'Tools', 'product-view-count' ),
					'icon'      => 'dashicons-hammer',
					'sticky'	=> false,
					'fields'    => [
						'report' => [
							'id'      => 'report',
							'label'     => __( 'Report', 'product-view-count' ),
							'type'      => 'textarea',
							'desc'     	=> '<button id="product-view-count_report-copy" class="button button-primary"><span class="dashicons dashicons-admin-page"></span></button>',
							'columns'   => 24,
							'rows'      => 10,
							'default'   => json_encode( $site_config, JSON_PRETTY_PRINT ),
							'readonly'  => true,
						],
					]
				],
			],
		];

		new Settings_API( $settings );
	}
}