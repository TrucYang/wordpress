<?php
/**
 * REST API endpoints for Product View Count AI
 */
namespace WPPlugines\Product_View_Count\App;

use Codexpert\Plugin\Base;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage API
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class API extends Base {

    public $plugin;
    private $database;
    private $namespace = 'pvc/v1';

    /**
     * Constructor function
     */
    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->slug = $plugin['TextDomain'] ?? 'product-view-count';
        $this->name = $plugin['Name'] ?? 'Product View Count';
        $this->version = $plugin['Version'] ?? '2.0.0';

        // Initialize database only when needed to avoid loading issues
        add_action( 'init', [ $this, 'init_database' ] );
    }

    /**
     * Initialize database connection
     */
    public function init_database() {
        if ( ! $this->database ) {
            $this->database = new Database( $this->plugin );
        }
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Analytics chart data
        register_rest_route( $this->namespace, '/analytics/chart', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_chart_data' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ],
            'args' => [
                'start_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ],
                'end_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ]
            ]
        ] );

        // Top products
        register_rest_route( $this->namespace, '/analytics/top-products', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_top_products' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ],
            'args' => [
                'start_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ],
                'end_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ],
                'limit' => [
                    'default' => 10,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0 && $param <= 100;
                    }
                ]
            ]
        ] );

        // Analytics stats
        register_rest_route( $this->namespace, '/analytics/stats', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_stats' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ],
            'args' => [
                'start_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ],
                'end_date' => [
                    'required' => true,
                    'validate_callback' => [ $this, 'validate_date' ]
                ]
            ]
        ] );

        // Reset all counts
        register_rest_route( $this->namespace, '/analytics/reset-all', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'reset_all_counts' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ]
        ] );

        // Reset specific product count
        register_rest_route( $this->namespace, '/analytics/reset-product', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $this, 'reset_product_count' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ],
            'args' => [
                'product_id' => [
                    'required' => true,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0;
                    }
                ]
            ]
        ] );

        // Get trending products
        register_rest_route( $this->namespace, '/analytics/trending', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [ $this, 'get_trending_products' ],
            'permission_callback' => [ $this, 'check_admin_permissions' ],
            'args' => [
                'days' => [
                    'default' => 7,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0 && $param <= 365;
                    }
                ],
                'limit' => [
                    'default' => 10,
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0 && $param <= 100;
                    }
                ]
            ]
        ] );


    }

    /**
     * Get chart data for analytics dashboard
     */
    public function get_chart_data( WP_REST_Request $request ) {
        $start_date = $request->get_param( 'start_date' );
        $end_date = $request->get_param( 'end_date' );

        try {
            // Ensure database tables exist
            $this->database->maybe_create_tables();
            
            $data = $this->database->get_analytics_data( $start_date, $end_date );
            
            $labels = [];
            $total_views = [];
            $unique_views = [];

            if ( empty( $data ) ) {
                // Return empty data structure if no data found
                return new WP_REST_Response( [
                    'labels' => [],
                    'totalViews' => [],
                    'uniqueViews' => []
                ], 200 );
            }

            foreach ( $data as $row ) {
                $labels[] = date( 'M j', strtotime( $row->date ) );
                $total_views[] = (int) $row->total_views;
                $unique_views[] = (int) $row->unique_views;
            }

            return new WP_REST_Response( [
                'labels' => $labels,
                'totalViews' => $total_views,
                'uniqueViews' => $unique_views
            ], 200 );

        } catch ( Exception $e ) {
            error_log( 'PVC Chart Data Error: ' . $e->getMessage() );
            return new WP_Error( 'chart_data_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    /**
     * Get top products
     */
    public function get_top_products( WP_REST_Request $request ) {
        $start_date = $request->get_param( 'start_date' );
        $end_date = $request->get_param( 'end_date' );
        $limit = $request->get_param( 'limit' );

        try {
            // Ensure database tables exist
            $this->database->maybe_create_tables();
            
            $products = $this->database->get_top_products( $start_date, $end_date, $limit );
            
            // Ensure we return an array even if empty
            if ( empty( $products ) ) {
                $products = [];
            }
            
            return new WP_REST_Response( $products, 200 );

        } catch ( Exception $e ) {
            error_log( 'PVC Top Products Error: ' . $e->getMessage() );
            return new WP_Error( 'top_products_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    /**
     * Get analytics statistics
     */
    public function get_stats( WP_REST_Request $request ) {
        $start_date = $request->get_param( 'start_date' );
        $end_date = $request->get_param( 'end_date' );

        try {
            // Ensure database tables exist
            $this->database->maybe_create_tables();
            
            $stats = $this->database->get_stats( $start_date, $end_date );
            
            return new WP_REST_Response( $stats, 200 );

        } catch ( Exception $e ) {
            error_log( 'PVC Stats Error: ' . $e->getMessage() );
            return new WP_Error( 'stats_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    /**
     * Reset all view counts
     */
    public function reset_all_counts( WP_REST_Request $request ) {
        try {
            $result = $this->database->reset_all_counts();
            
            if ( $result ) {
                return new WP_REST_Response( [ 'success' => true, 'message' => 'All counts reset successfully' ], 200 );
            } else {
                return new WP_Error( 'reset_failed', 'Failed to reset counts', [ 'status' => 500 ] );
            }

        } catch ( Exception $e ) {
            return new WP_Error( 'reset_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    /**
     * Reset specific product count
     */
    public function reset_product_count( WP_REST_Request $request ) {
        $product_id = $request->get_param( 'product_id' );

        try {
            $result = $this->database->reset_product_count( $product_id );
            
            if ( $result ) {
                return new WP_REST_Response( [ 'success' => true, 'message' => 'Product count reset successfully' ], 200 );
            } else {
                return new WP_Error( 'reset_failed', 'Failed to reset product count', [ 'status' => 500 ] );
            }

        } catch ( Exception $e ) {
            return new WP_Error( 'reset_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    /**
     * Get trending products
     */
    public function get_trending_products( WP_REST_Request $request ) {
        $days = $request->get_param( 'days' );
        $limit = $request->get_param( 'limit' );

        $start_date = date( 'Y-m-d', strtotime( "-{$days} days" ) );
        $end_date = date( 'Y-m-d' );

        try {
            $products = $this->database->get_top_products( $start_date, $end_date, $limit );

            return new WP_REST_Response( $products, 200 );

        } catch ( Exception $e ) {
            return new WP_Error( 'trending_error', $e->getMessage(), [ 'status' => 500 ] );
        }
    }









    /**
     * Check admin permissions
     */
    public function check_admin_permissions( WP_REST_Request $request ) {
        return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
    }

    /**
     * Validate date parameter
     */
    public function validate_date( $param, $request, $key ) {
        return (bool) strtotime( $param );
    }
}
