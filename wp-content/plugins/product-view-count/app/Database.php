<?php
/**
 * Database operations for Product View Count AI
 */
namespace WPPlugines\Product_View_Count\App;

use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Database
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class Database extends Base {

    public $plugin;
    public $table_name;
    public $views_table;

    /**
     * Constructor function
     */
    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->slug = $plugin['TextDomain'] ?? 'product-view-count';
        $this->name = $plugin['Name'] ?? 'Product View Count';
        $this->version = $plugin['Version'] ?? '2.0.0';

        global $wpdb;
        $this->table_name = $wpdb->prefix . 'pvc_product_views';
        $this->views_table = $wpdb->prefix . 'pvc_view_logs';
    }

    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Main views table for aggregated data
        $sql_views = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            total_views bigint(20) DEFAULT 0,
            unique_views bigint(20) DEFAULT 0,
            guest_views bigint(20) DEFAULT 0,
            user_views bigint(20) DEFAULT 0,
            last_viewed datetime DEFAULT CURRENT_TIMESTAMP,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY product_id (product_id),
            KEY last_viewed (last_viewed)
        ) $charset_collate;";

        // Detailed view logs table
        $sql_logs = "CREATE TABLE {$this->views_table} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_type varchar(20) DEFAULT 'guest',
            user_role varchar(50) DEFAULT NULL,
            ip_address varchar(45) NOT NULL,
            user_agent text,
            referrer text,
            session_id varchar(100),
            is_unique tinyint(1) DEFAULT 1,
            is_bot tinyint(1) DEFAULT 0,
            country_code varchar(2) DEFAULT NULL,
            viewed_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY product_id (product_id),
            KEY user_id (user_id),
            KEY ip_address (ip_address),
            KEY viewed_at (viewed_at),
            KEY is_unique (is_unique),
            KEY is_bot (is_bot)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_views );
        dbDelta( $sql_logs );

        // Update version
        update_option( 'pvc_db_version', '2.0.0' );
    }

    /**
     * Check if tables exist and create if needed
     */
    public function maybe_create_tables() {
        global $wpdb;

        $installed_version = get_option( 'pvc_db_version', '0' );

        if ( version_compare( $installed_version, '2.0.0', '<' ) ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( "PVC: Creating database tables. Current version: {$installed_version}" );
            }
            $this->create_tables();
        }
    }

    /**
     * Force create tables (for debugging)
     */
    public function force_create_tables() {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( "PVC: Force creating database tables" );
        }
        $this->create_tables();
    }

    /**
     * Record a product view with advanced tracking
     */
    public function record_view( $product_id, $args = [] ) {
        global $wpdb;

        $defaults = [
            'user_id' => get_current_user_id(),
            'ip_address' => $this->get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referrer' => wp_get_referer() ?? '',
            'session_id' => $this->get_session_id()
        ];

        $args = wp_parse_args( $args, $defaults );

        // Check if it's a bot
        $is_bot = $this->is_bot( $args['user_agent'] );
        if ( $is_bot ) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( "PVC: Bot detected, not counting view. User agent: " . $args['user_agent'] );
            }
            return false; // Don't count bot views
        }

        // Check IP limiting (1 hour window)
        $is_unique = $this->is_unique_view( $product_id, $args['ip_address'] );

        // Determine user type and role
        $user_type = $args['user_id'] > 0 ? 'user' : 'guest';
        $user_role = null;
        
        if ( $args['user_id'] > 0 ) {
            $user = get_userdata( $args['user_id'] );
            $user_role = $user ? implode( ',', $user->roles ) : null;
        }

        // Insert detailed log
        $log_data = [
            'product_id' => $product_id,
            'user_id' => $args['user_id'] ?: null,
            'user_type' => $user_type,
            'user_role' => $user_role,
            'ip_address' => $args['ip_address'],
            'user_agent' => $args['user_agent'],
            'referrer' => $args['referrer'],
            'session_id' => $args['session_id'],
            'is_unique' => $is_unique ? 1 : 0,
            'is_bot' => 0,
            'viewed_at' => current_time( 'mysql' )
        ];

        // Use our database function to insert the view log
        $insert_result = pvc_insert_view_log( $product_id, $args );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            if ( $insert_result === false ) {
                error_log( "PVC: Failed to insert view log. Error: " . $wpdb->last_error );
            } else {
                error_log( "PVC: Successfully inserted view log for product {$product_id}" );
            }
        }

        // Update aggregated data
        $this->update_aggregated_views( $product_id, $user_type, $is_unique );

        return $insert_result !== false;
    }

    /**
     * Update aggregated view counts
     */
    private function update_aggregated_views( $product_id, $user_type, $is_unique ) {
        global $wpdb;

        // Get current counts using our database function
        $current = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE product_id = %d",
            $product_id
        ) );

        if ( $current ) {
            // Update existing record
            $data = [
                'total_views' => $current->total_views + 1,
                'unique_views' => $is_unique ? $current->unique_views + 1 : $current->unique_views,
                'guest_views' => $user_type === 'guest' ? $current->guest_views + 1 : $current->guest_views,
                'user_views' => $user_type === 'user' ? $current->user_views + 1 : $current->user_views,
                'last_viewed' => current_time( 'mysql' )
            ];
        } else {
            // Create new record
            $data = [
                'total_views' => 1,
                'unique_views' => $is_unique ? 1 : 0,
                'guest_views' => $user_type === 'guest' ? 1 : 0,
                'user_views' => $user_type === 'user' ? 1 : 0,
                'last_viewed' => current_time( 'mysql' )
            ];
        }

        // Use our database function to upsert the data
        pvc_upsert_aggregated_views( $product_id, $data );

        // Also update post meta for backward compatibility
        $total_views = $current ? $current->total_views + 1 : 1;
        $meta_result = update_post_meta( $product_id, 'product_view_count', $total_views );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( "PVC: Updated post meta for product {$product_id}. New count: {$total_views}. Result: " . ( $meta_result ? 'success' : 'failed' ) );
        }
    }

    /**
     * Check if view is unique (IP-based, 1 hour window)
     */
    private function is_unique_view( $product_id, $ip_address ) {
        global $wpdb;

        $one_hour_ago = date( 'Y-m-d H:i:s', strtotime( '-1 hour' ) );

        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->views_table} 
             WHERE product_id = %d AND ip_address = %s AND viewed_at > %s",
            $product_id, $ip_address, $one_hour_ago
        ) );

        return $existing == 0;
    }

    /**
     * Check if user agent is a bot
     */
    private function is_bot( $user_agent ) {
        $bot_patterns = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'facebookexternalhit', 'twitterbot', 'rogerbot',
            'linkedinbot', 'embedly', 'quora link preview', 'showyoubot',
            'outbrain', 'pinterest/0.', 'developers.google.com/+/web/snippet',
            'slackbot', 'vkshare', 'w3c_validator', 'redditbot', 'applebot',
            'whatsapp', 'flipboard', 'tumblr', 'bitlybot', 'skypeuripreview',
            'nuzzel', 'discordbot', 'google page speed', 'qwantify',
            'pinterestbot', 'bitrix link preview', 'xing-contenttabreceiver',
            'chrome-lighthouse', 'telegrambot'
        ];

        $user_agent_lower = strtolower( $user_agent );
        
        foreach ( $bot_patterns as $pattern ) {
            if ( strpos( $user_agent_lower, $pattern ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user IP address
     */
    private function get_user_ip() {
        $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ( $ip_keys as $key ) {
            if ( array_key_exists( $key, $_SERVER ) === true ) {
                foreach ( explode( ',', $_SERVER[$key] ) as $ip ) {
                    $ip = trim( $ip );
                    if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get or create session ID
     */
    private function get_session_id() {
        // Check if session can be started safely
        if ( session_status() === PHP_SESSION_NONE && ! headers_sent() ) {
            session_start();
        }

        // If we have a session ID, return it
        if ( session_id() ) {
            return session_id();
        }

        // Fallback: generate a unique ID based on user IP and user agent
        $user_ip = $this->get_user_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $unique_string = $user_ip . '|' . $user_agent . '|' . date( 'Y-m-d' );

        return 'pvc_' . md5( $unique_string );
    }

    /**
     * Get analytics data for date range
     */
    public function get_analytics_data( $start_date, $end_date ) {
        global $wpdb;

        // Ensure tables exist
        $this->maybe_create_tables();

        // Check if views table exists
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$this->views_table}'" );
        if ( ! $table_exists ) {
            return [];
        }

        // Daily view counts
        $daily_views = $wpdb->get_results( $wpdb->prepare(
            "SELECT DATE(viewed_at) as date,
                    COUNT(*) as total_views,
                    COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_views
             FROM {$this->views_table}
             WHERE viewed_at BETWEEN %s AND %s
             AND is_bot = 0
             GROUP BY DATE(viewed_at)
             ORDER BY date ASC",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ) );

        return $daily_views ?: [];
    }

    /**
     * Get top products for date range
     */
    public function get_top_products( $start_date, $end_date, $limit = 10 ) {
        global $wpdb;

        // Ensure tables exist
        $this->maybe_create_tables();

        // Check if views table exists
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$this->views_table}'" );
        if ( ! $table_exists ) {
            // Fallback to post meta if new tables don't exist yet
            return $this->get_top_products_fallback( $limit );
        }

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT p.ID, p.post_title as name,
                    COUNT(vl.id) as total_views,
                    COUNT(CASE WHEN vl.is_unique = 1 THEN 1 END) as unique_views
             FROM {$wpdb->posts} p
             INNER JOIN {$this->views_table} vl ON p.ID = vl.product_id
             WHERE p.post_type = 'product'
             AND p.post_status = 'publish'
             AND vl.viewed_at BETWEEN %s AND %s
             AND vl.is_bot = 0
             GROUP BY p.ID
             ORDER BY total_views DESC
             LIMIT %d",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59',
            $limit
        ) );

        if ( empty( $results ) ) {
            // Fallback to post meta if no data in new tables
            return $this->get_top_products_fallback( $limit );
        }

        // Enhance with product data
        foreach ( $results as &$product ) {
            if ( function_exists( 'wc_get_product' ) ) {
                $product_obj = wc_get_product( $product->ID );
                if ( $product_obj ) {
                    $product->sku = $product_obj->get_sku();
                    $product->image = wp_get_attachment_image_url( $product_obj->get_image_id(), 'thumbnail' );
                    $product->edit_url = admin_url( 'post.php?post=' . $product->ID . '&action=edit' );
                    $product->view_url = get_permalink( $product->ID );
                }
            } else {
                $product->sku = '';
                $product->image = '';
                $product->edit_url = get_edit_post_link( $product->ID );
                $product->view_url = get_permalink( $product->ID );
            }
        }

        return $results ?: [];
    }

    /**
     * Fallback method using post meta
     */
    private function get_top_products_fallback( $limit = 10 ) {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT p.ID, p.post_title as name,
                    CAST(pm.meta_value AS UNSIGNED) as total_views,
                    CAST(pm.meta_value AS UNSIGNED) as unique_views
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'product'
             AND p.post_status = 'publish'
             AND pm.meta_key = 'product_view_count'
             AND pm.meta_value > 0
             ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
             LIMIT %d",
            $limit
        ) );

        // Enhance with product data
        foreach ( $results as &$product ) {
            if ( function_exists( 'wc_get_product' ) ) {
                $product_obj = wc_get_product( $product->ID );
                if ( $product_obj ) {
                    $product->sku = $product_obj->get_sku();
                    $product->image = wp_get_attachment_image_url( $product_obj->get_image_id(), 'thumbnail' );
                    $product->edit_url = admin_url( 'post.php?post=' . $product->ID . '&action=edit' );
                    $product->view_url = get_permalink( $product->ID );
                }
            } else {
                $product->sku = '';
                $product->image = '';
                $product->edit_url = admin_url( 'post.php?post=' . $product->ID . '&action=edit' );
                $product->view_url = get_permalink( $product->ID );
            }
        }

        return $results ?: [];
    }

    /**
     * Get summary statistics
     */
    public function get_stats( $start_date, $end_date ) {
        global $wpdb;

        // Ensure tables exist
        $this->maybe_create_tables();

        // Check if views table exists
        $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$this->views_table}'" );
        if ( ! $table_exists ) {
            return $this->get_stats_fallback();
        }

        $stats = $wpdb->get_row( $wpdb->prepare(
            "SELECT COUNT(*) as total_views,
                    COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_visitors,
                    COUNT(DISTINCT product_id) as products_viewed
             FROM {$this->views_table}
             WHERE viewed_at BETWEEN %s AND %s
             AND is_bot = 0",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ) );

        if ( ! $stats ) {
            return $this->get_stats_fallback();
        }

        // Get top viewed product
        $top_product = $wpdb->get_row( $wpdb->prepare(
            "SELECT p.ID, p.post_title as name, COUNT(vl.id) as views
             FROM {$wpdb->posts} p
             INNER JOIN {$this->views_table} vl ON p.ID = vl.product_id
             WHERE p.post_type = 'product'
             AND vl.viewed_at BETWEEN %s AND %s
             AND vl.is_bot = 0
             GROUP BY p.ID
             ORDER BY views DESC
             LIMIT 1",
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ) );

        $avg_views = $stats->products_viewed > 0 ? $stats->total_views / $stats->products_viewed : 0;

        return [
            'totalViews' => (int) $stats->total_views,
            'uniqueVisitors' => (int) $stats->unique_visitors,
            'avgViewsPerProduct' => round( $avg_views, 1 ),
            'topViewedProduct' => $top_product ? [
                'id' => $top_product->ID,
                'name' => $top_product->name,
                'views' => (int) $top_product->views
            ] : null
        ];
    }

    /**
     * Fallback stats using post meta
     */
    private function get_stats_fallback() {
        global $wpdb;

        // Get total views from post meta
        $total_views = $wpdb->get_var(
            "SELECT SUM(CAST(meta_value AS UNSIGNED))
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'product_view_count'
             AND p.post_type = 'product'
             AND p.post_status = 'publish'"
        );

        // Get product count with views
        $products_with_views = $wpdb->get_var(
            "SELECT COUNT(*)
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'product_view_count'
             AND p.post_type = 'product'
             AND p.post_status = 'publish'
             AND CAST(pm.meta_value AS UNSIGNED) > 0"
        );

        // Get top viewed product
        $top_product = $wpdb->get_row(
            "SELECT p.ID, p.post_title as name, CAST(pm.meta_value AS UNSIGNED) as views
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'product'
             AND p.post_status = 'publish'
             AND pm.meta_key = 'product_view_count'
             ORDER BY CAST(pm.meta_value AS UNSIGNED) DESC
             LIMIT 1"
        );

        $total_views = (int) $total_views;
        $products_with_views = (int) $products_with_views;
        $avg_views = $products_with_views > 0 ? $total_views / $products_with_views : 0;

        return [
            'totalViews' => $total_views,
            'uniqueVisitors' => $total_views, // Approximate since we don't have unique data
            'avgViewsPerProduct' => round( $avg_views, 1 ),
            'topViewedProduct' => $top_product ? [
                'id' => $top_product->ID,
                'name' => $top_product->name,
                'views' => (int) $top_product->views
            ] : null
        ];
    }

    /**
     * Reset all view counts
     */
    public function reset_all_counts() {
        // Use our database function
        return pvc_reset_all_counts();
    }

    /**
     * Reset specific product count
     */
    public function reset_product_count( $product_id ) {
        // Use our database function
        return pvc_reset_product_count( $product_id );
    }

    /**
     * Create sample data for testing (only if no data exists)
     */
    public function create_sample_data() {
        global $wpdb;

        // Check if we already have data
        $existing_data = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->views_table}" );
        if ( $existing_data > 0 ) {
            return false; // Don't create sample data if real data exists
        }

        // Get some products to work with
        $products = $wpdb->get_results(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish' LIMIT 5"
        );

        if ( empty( $products ) ) {
            return false; // No products to work with
        }

        // Create sample view data for the last 30 days
        $sample_data_created = false;
        for ( $i = 30; $i >= 0; $i-- ) {
            $date = date( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) );

            foreach ( $products as $product ) {
                // Random number of views per day (0-20)
                $daily_views = rand( 0, 20 );

                for ( $j = 0; $j < $daily_views; $j++ ) {
                    // Random time during the day
                    $view_time = date( 'Y-m-d H:i:s', strtotime( $date ) + rand( 0, 86400 ) );

                    // Insert sample view log
                    $wpdb->insert( $this->views_table, [
                        'product_id' => $product->ID,
                        'user_id' => rand( 0, 1 ) ? null : rand( 1, 10 ), // Some guest, some user views
                        'user_type' => rand( 0, 1 ) ? 'guest' : 'user',
                        'ip_address' => '192.168.1.' . rand( 1, 254 ),
                        'user_agent' => 'Mozilla/5.0 (Sample Data)',
                        'is_unique' => rand( 0, 1 ),
                        'is_bot' => 0,
                        'viewed_at' => $view_time
                    ] );

                    $sample_data_created = true;
                }

                // Update post meta with total count
                $total_count = $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$this->views_table} WHERE product_id = %d",
                    $product->ID
                ) );
                update_post_meta( $product->ID, 'product_view_count', $total_count );
            }
        }

        if ( $sample_data_created ) {
            // Update aggregated data
            foreach ( $products as $product ) {
                $stats = $wpdb->get_row( $wpdb->prepare(
                    "SELECT COUNT(*) as total_views,
                            COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_views,
                            COUNT(CASE WHEN user_type = 'guest' THEN 1 END) as guest_views,
                            COUNT(CASE WHEN user_type = 'user' THEN 1 END) as user_views
                     FROM {$this->views_table} WHERE product_id = %d",
                    $product->ID
                ) );

                if ( $stats ) {
                    $wpdb->replace( $this->table_name, [
                        'product_id' => $product->ID,
                        'total_views' => $stats->total_views,
                        'unique_views' => $stats->unique_views,
                        'guest_views' => $stats->guest_views,
                        'user_views' => $stats->user_views,
                        'last_viewed' => current_time( 'mysql' )
                    ] );
                }
            }
        }

        return $sample_data_created;
    }
}
