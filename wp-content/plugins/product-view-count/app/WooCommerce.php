<?php
/**
 * WooCommerce Integration Enhancements
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
 * @subpackage WooCommerce
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class WooCommerce extends Base {

    public $plugin;

    /**
     * Constructor function
     */
    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->slug = $plugin['TextDomain'] ?? 'product-view-count';
        $this->name = $plugin['Name'] ?? 'Product View Count';
        $this->version = $plugin['Version'] ?? '2.0.0';
        // Database operations now use centralized functions from inc/functions.php
    }

    /**
     * Add bulk actions to product list
     */
    public function add_bulk_actions( $bulk_actions ) {
        $bulk_actions['pvc_reset_views'] = __( 'Reset View Counts', 'product-view-count' );
        return $bulk_actions;
    }

    /**
     * Handle bulk reset action
     */
    public function handle_bulk_reset( $redirect_to, $doaction, $post_ids ) {
        if ( $doaction !== 'pvc_reset_views' ) {
            return $redirect_to;
        }

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return $redirect_to;
        }

        $reset_count = 0;
        foreach ( $post_ids as $post_id ) {
            if ( get_post_type( $post_id ) === 'product' ) {
                pvc_reset_product_count( $post_id );
                $reset_count++;
            }
        }

        $redirect_to = add_query_arg( 'pvc_reset_count', $reset_count, $redirect_to );
        return $redirect_to;
    }

    /**
     * Show admin notice after bulk reset
     */
    public function show_bulk_reset_notice() {
        if ( ! empty( $_REQUEST['pvc_reset_count'] ) ) {
            $reset_count = intval( $_REQUEST['pvc_reset_count'] );
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' .
                _n( 'Reset view count for %s product.', 'Reset view counts for %s products.', $reset_count, 'product-view-count' ) .
                '</p></div>',
                $reset_count
            );
        }
    }

    /**
     * Add trending badge to product loop
     */
    public function add_trending_badge() {
        global $product;
        
        if ( ! $product ) {
            return;
        }

        // Check if trending is enabled
        $trending_enabled = Helper::get_option( 'product-view-count_trending', 'trending_enabled', [] );
        if ( ! in_array( 'enabled', $trending_enabled ) ) {
            return;
        }

        // Get trending settings
        $days = Helper::get_option( 'product-view-count_trending', 'trending_days', 7 );
        $threshold_type = Helper::get_option( 'product-view-count_trending', 'trending_threshold_type', 'percentage' );
        $threshold_value = Helper::get_option( 'product-view-count_trending', 'trending_threshold_value', 10 );

        $trending_settings = [
            'days' => $days,
            'threshold_type' => $threshold_type,
            'threshold_value' => $threshold_value
        ];

        if ( $this->is_product_trending( $product->get_id(), $trending_settings ) ) {
            $badge_text = Helper::get_option( 'product-view-count_trending', 'trending_badge_text', '🔥 Trending' );
            echo '<div class="pvc-trending-badge">' . esc_html( $badge_text ) . '</div>';
        }
    }

    /**
     * Check if product is trending
     */
    private function is_product_trending( $product_id, $settings ) {
        $days = $settings['days'];
        $start_date = date( 'Y-m-d', strtotime( "-{$days} days" ) );
        $end_date = date( 'Y-m-d' );

        // Get product's view count for the period using our database function
        $product_views = pvc_get_view_count_from_logs( $product_id, $start_date, $end_date );

        if ( $settings['threshold_type'] === 'percentage' ) {
            // Get top products to calculate percentage threshold using our database function
            $top_products = pvc_get_top_products( $start_date, $end_date, 1000 );

            if ( empty( $top_products ) ) {
                return false;
            }

            $total_products = count( $top_products );
            $threshold_position = ceil( $total_products * ( $settings['threshold_value'] / 100 ) );
            
            // Find product's position in ranking
            $position = 0;
            foreach ( $top_products as $index => $product ) {
                if ( $product->ID == $product_id ) {
                    $position = $index + 1;
                    break;
                }
            }

            return $position > 0 && $position <= $threshold_position;
        } else {
            // Fixed number threshold
            return $product_views >= $settings['threshold_value'];
        }
    }

    /**
     * Add trending badge styles
     */
    public function add_trending_styles() {
        ?>
        <style>
        .pvc-trending-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: linear-gradient(45deg, #ff6b6b, #ff8e53);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            animation: pvc-pulse 2s infinite;
        }

        @keyframes pvc-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .woocommerce ul.products li.product {
            position: relative;
        }

        .single-product .pvc-trending-badge {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
            top: auto;
            left: auto;
        }
        </style>
        <?php
    }

    /**
     * Add view count to product structured data
     */
    public function add_structured_data( $markup, $product ) {
        if ( ! is_product() ) {
            return $markup;
        }

        $view_count = get_post_meta( $product->get_id(), 'product_view_count', true );
        
        if ( $view_count ) {
            $markup['interactionStatistic'] = [
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/ViewAction',
                'userInteractionCount' => $view_count
            ];
        }

        return $markup;
    }

    /**
     * Add view count to product export
     */
    public function add_export_column( $columns ) {
        $columns['view_count'] = __( 'View Count', 'product-view-count' );
        return $columns;
    }

    /**
     * Export view count data
     */
    public function export_view_count( $value, $product ) {
        return get_post_meta( $product->get_id(), 'product_view_count', true ) ?: 0;
    }

    /**
     * Add view count to product quick edit
     */
    public function add_quick_edit_field( $column_name, $post_type ) {
        if ( $column_name !== 'view' || $post_type !== 'product' ) {
            return;
        }
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e( 'View Count', 'product-view-count' ); ?></span>
                    <span class="input-text-wrap">
                        <input type="number" name="product_view_count" class="text" value="" min="0" />
                    </span>
                </label>
                <p class="description"><?php _e( 'Set to 0 to reset view count', 'product-view-count' ); ?></p>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Save quick edit view count
     */
    public function save_quick_edit( $post_id ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( get_post_type( $post_id ) !== 'product' ) {
            return;
        }

        if ( isset( $_POST['product_view_count'] ) ) {
            $view_count = intval( $_POST['product_view_count'] );

            if ( $view_count === 0 ) {
                // Reset count using our database function
                pvc_reset_product_count( $post_id );
            } else {
                // Update count
                update_post_meta( $post_id, 'product_view_count', $view_count );
            }
        }
    }

    /**
     * Add JavaScript for quick edit
     */
    public function add_quick_edit_script() {
        global $current_screen;
        
        if ( $current_screen->id !== 'edit-product' ) {
            return;
        }
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('body').on('click', '.editinline', function() {
                var post_id = $(this).closest('tr').attr('id').replace('post-', '');
                var view_count = $(this).closest('tr').find('.column-view').text().trim();
                
                setTimeout(function() {
                    $('input[name="product_view_count"]').val(view_count);
                }, 100);
            });
        });
        </script>
        <?php
    }

    /**
     * Initialize WooCommerce hooks
     */
    public function init_hooks() {
        // Bulk actions
        add_filter( 'bulk_actions-edit-product', [ $this, 'add_bulk_actions' ] );
        add_filter( 'handle_bulk_actions-edit-product', [ $this, 'handle_bulk_reset' ], 10, 3 );
        add_action( 'admin_notices', [ $this, 'show_bulk_reset_notice' ] );

        // Trending badges
        add_action( 'woocommerce_before_shop_loop_item_title', [ $this, 'add_trending_badge' ], 5 );
        add_action( 'woocommerce_single_product_summary', [ $this, 'add_trending_badge' ], 5 );
        add_action( 'wp_head', [ $this, 'add_trending_styles' ] );

        // Structured data
        add_filter( 'woocommerce_structured_data_product', [ $this, 'add_structured_data' ], 10, 2 );

        // Export functionality
        add_filter( 'woocommerce_product_export_column_names', [ $this, 'add_export_column' ] );
        add_filter( 'woocommerce_product_export_product_default_columns', [ $this, 'add_export_column' ] );
        add_filter( 'woocommerce_product_export_product_column_view_count', [ $this, 'export_view_count' ], 10, 2 );

        // Quick edit
        add_action( 'quick_edit_custom_box', [ $this, 'add_quick_edit_field' ], 10, 2 );
        add_action( 'save_post', [ $this, 'save_quick_edit' ] );
        add_action( 'admin_footer', [ $this, 'add_quick_edit_script' ] );
    }
}
