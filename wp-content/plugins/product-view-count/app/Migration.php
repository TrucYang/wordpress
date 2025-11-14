<?php

namespace WPPlugines\Product_View_Count\App;

use WPPlugines\Product_View_Count\App\Database;

/**
 * Migration class for Product View Count plugin
 * Handles migration from post meta to custom database tables
 */
class Migration {

    private $plugin;
    private $database;
    private $batch_size = 50; // Process products in batches
    private $migration_option = 'pvc_migration_status';
    private $migration_version = '2.0.0';

    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->database = new Database( $plugin );
    }

    /**
     * Check if migration is needed and run it
     */
    public function maybe_run_migration() {
        $migration_status = get_option( $this->migration_option, [] );
        $current_version = $migration_status['version'] ?? '0';

        // Check if migration is needed
        if ( version_compare( $current_version, $this->migration_version, '<' ) ) {
            $this->log( 'Migration needed. Starting migration process...' );
            $this->run_migration();
        }
    }

    /**
     * Run the complete migration process
     */
    public function run_migration() {
        try {
            // Update migration status to 'running'
            $this->update_migration_status( 'running', 'Migration in progress...' );

            // Ensure database tables exist
            $this->database->force_create_tables();

            // Get migration statistics
            $stats = $this->get_migration_stats();
            $this->log( "Migration stats: {$stats['total_products']} products, {$stats['products_with_views']} with view counts" );

            if ( $stats['total_products'] === 0 ) {
                $this->complete_migration( 'No products found to migrate' );
                return;
            }

            // Run migration in batches
            $migrated_count = 0;
            $batch_number = 0;

            while ( $migrated_count < $stats['products_with_views'] ) {
                $batch_result = $this->migrate_batch( $batch_number );
                
                if ( $batch_result === false ) {
                    throw new \Exception( 'Batch migration failed at batch ' . $batch_number );
                }

                $migrated_count += $batch_result;
                $batch_number++;

                // Update progress
                $progress = round( ( $migrated_count / $stats['products_with_views'] ) * 100, 2 );
                $this->update_migration_status( 'running', "Migrated {$migrated_count}/{$stats['products_with_views']} products ({$progress}%)" );

                // Prevent timeout on large datasets
                if ( function_exists( 'set_time_limit' ) ) {
                    set_time_limit( 30 );
                }
            }

            // Verify migration
            $verification = $this->verify_migration();
            if ( ! $verification['success'] ) {
                throw new \Exception( 'Migration verification failed: ' . $verification['message'] );
            }

            // Complete migration
            $this->complete_migration( "Successfully migrated {$migrated_count} products" );

        } catch ( \Exception $e ) {
            $this->handle_migration_error( $e );
        }
    }

    /**
     * Get migration statistics
     */
    private function get_migration_stats() {
        return [
            'total_products' => pvc_get_total_products(),
            'products_with_views' => pvc_get_products_with_meta_views()
        ];
    }

    /**
     * Migrate a batch of products
     */
    private function migrate_batch( $batch_number ) {
        $offset = $batch_number * $this->batch_size;

        // Get products with view counts in this batch using our database function
        $products = pvc_get_products_for_migration( $this->batch_size, $offset );

        if ( empty( $products ) ) {
            return 0; // No more products to migrate
        }

        $migrated_in_batch = 0;

        foreach ( $products as $product ) {
            if ( $this->migrate_product( $product->ID, (int) $product->view_count ) ) {
                $migrated_in_batch++;
            }
        }

        $this->log( "Batch {$batch_number}: Migrated {$migrated_in_batch}/{$this->batch_size} products" );

        return $migrated_in_batch;
    }

    /**
     * Migrate a single product's view count
     */
    private function migrate_product( $product_id, $view_count ) {
        try {
            // Check if already migrated using our database function
            if ( pvc_product_exists_in_views_table( $product_id ) ) {
                $this->log( "Product {$product_id} already migrated, skipping" );
                return true;
            }

            // Insert into aggregated views table using our database function
            $result = pvc_insert_product_views(
                $product_id,
                $view_count,
                $view_count, // Assume all views were unique for legacy data
                $view_count, // Assume all views were from guests
                0 // No user views for legacy data
            );

            if ( $result === false ) {
                $this->log( "Failed to migrate product {$product_id}" );
                return false;
            }

            // Create placeholder view logs for analytics (spread over last 30 days)
            $this->create_placeholder_logs( $product_id, $view_count );

            return true;

        } catch ( \Exception $e ) {
            $this->log( "Error migrating product {$product_id}: " . $e->getMessage() );
            return false;
        }
    }

    /**
     * Create placeholder view logs for analytics functionality
     */
    private function create_placeholder_logs( $product_id, $view_count ) {
        global $wpdb;

        // Don't create too many logs for performance
        $log_count = min( $view_count, 100 );
        
        if ( $log_count === 0 ) {
            return;
        }

        // Spread logs over the last 30 days
        $days_back = min( 30, $log_count );
        $views_per_day = ceil( $log_count / $days_back );

        for ( $day = 0; $day < $days_back; $day++ ) {
            $date = date( 'Y-m-d H:i:s', strtotime( "-{$day} days" ) );
            
            for ( $view = 0; $view < $views_per_day && ( $day * $views_per_day + $view ) < $log_count; $view++ ) {
                $wpdb->insert(
                    $this->database->views_table,
                    [
                        'product_id' => $product_id,
                        'user_id' => 0,
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Migration Script',
                        'referrer' => '',
                        'session_id' => 'migration_' . $product_id . '_' . $day . '_' . $view,
                        'is_unique' => 1,
                        'is_bot' => 0,
                        'user_type' => 'guest',
                        'viewed_at' => $date
                    ]
                );
            }
        }
    }

    /**
     * Verify migration completed successfully
     */
    private function verify_migration() {
        // Count products with post meta view counts using our database function
        $meta_count = pvc_get_products_with_meta_views();

        // Count products in new table using our database function
        $table_count = pvc_get_products_with_table_views();

        if ( $meta_count > $table_count ) {
            return [
                'success' => false,
                'message' => "Mismatch: {$meta_count} products with meta, {$table_count} in new table"
            ];
        }

        return [
            'success' => true,
            'message' => "Verification passed: {$table_count} products migrated"
        ];
    }

    /**
     * Complete migration successfully
     */
    private function complete_migration( $message ) {
        $this->update_migration_status( 'completed', $message );
        $this->log( 'Migration completed successfully: ' . $message );
        
        // Schedule admin notice
        set_transient( 'pvc_migration_notice', [
            'type' => 'success',
            'message' => 'Product View Count migration completed successfully. ' . $message
        ], DAY_IN_SECONDS );
    }

    /**
     * Handle migration error
     */
    private function handle_migration_error( \Exception $e ) {
        $error_message = 'Migration failed: ' . $e->getMessage();
        $this->update_migration_status( 'failed', $error_message );
        $this->log( $error_message );

        // Schedule admin notice
        set_transient( 'pvc_migration_notice', [
            'type' => 'error',
            'message' => 'Product View Count migration failed. Please contact support. Error: ' . $e->getMessage()
        ], DAY_IN_SECONDS );
    }

    /**
     * Update migration status
     */
    private function update_migration_status( $status, $message = '' ) {
        update_option( $this->migration_option, [
            'version' => $this->migration_version,
            'status' => $status,
            'message' => $message,
            'timestamp' => current_time( 'mysql' )
        ] );
    }

    /**
     * Log migration messages
     */
    private function log( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'PVC Migration: ' . $message );
        }
    }

    /**
     * Get migration status for admin display
     */
    public function get_migration_status() {
        return get_option( $this->migration_option, [
            'version' => '0',
            'status' => 'pending',
            'message' => 'Migration not started',
            'timestamp' => ''
        ] );
    }

    /**
     * Force re-run migration (for debugging)
     */
    public function force_migration() {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            delete_option( $this->migration_option );
            $this->run_migration();
        }
    }

    /**
     * AJAX handler for manual migration trigger
     */
    public function ajax_trigger_migration() {
        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Insufficient permissions' );
        }

        // Check nonce
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'pvc_migration_nonce' ) ) {
            wp_die( 'Security check failed' );
        }

        // Reset migration status and run
        delete_option( $this->migration_option );
        $this->run_migration();

        wp_send_json_success( [
            'message' => 'Migration completed successfully',
            'status' => $this->get_migration_status()
        ] );
    }

    /**
     * Create test data for migration testing (debug only)
     */
    public function create_test_data() {
        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return false;
        }

        // Get some products using our database function
        $products = pvc_get_products_for_sample_data( 5 );

        if ( empty( $products ) ) {
            return false;
        }

        $created = 0;
        foreach ( $products as $product ) {
            $view_count = rand( 10, 500 );
            update_post_meta( $product->ID, 'product_view_count', $view_count );
            $created++;
        }

        $this->log( "Created test data for {$created} products" );
        return $created;
    }

    /**
     * Get detailed migration information for admin display
     */
    public function get_migration_info() {
        $status = $this->get_migration_status();

        // Get current counts using our database functions
        $meta_count = pvc_get_products_with_meta_views();
        $table_count = pvc_get_products_with_table_views();
        $total_views_meta = pvc_get_total_meta_views();
        $total_views_table = pvc_get_total_table_views();

        return [
            'status' => $status,
            'meta_products' => $meta_count,
            'table_products' => $table_count,
            'meta_total_views' => $total_views_meta,
            'table_total_views' => $total_views_table,
            'migration_needed' => $meta_count > $table_count
        ];
    }
}
