<?php
/**
 * Frontend Display Logic for Product View Count
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
 * @subpackage FrontendDisplay
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class FrontendDisplay extends Base {

    public $plugin;
    private $metabox;

    /**
     * Constructor function
     */
    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->slug = $plugin['TextDomain'] ?? 'product-view-count';
        $this->name = $plugin['Name'] ?? 'Product View Count';
        $this->version = $plugin['Version'] ?? '2.0.0';

        // Initialize metabox for calculations
        $this->metabox = new ProductMetabox( $plugin );
    }

    /**
     * Display view count on shop page
     */
    public function display_shop_view_count() {
        global $product;
        
        if ( ! $product ) {
            return;
        }

        $this->render_view_count( $product->get_id(), 'shop' );
    }

    /**
     * Display view count on single product page
     */
    public function display_single_view_count() {
        global $product;
        
        if ( ! $product ) {
            return;
        }

        $this->render_view_count( $product->get_id(), 'single' );
    }

    /**
     * Render view count HTML
     */
    public function render_view_count( $product_id, $context = 'shop' ) {
        // Validate product ID
        if ( ! $product_id || ! is_numeric( $product_id ) ) {
            return;
        }

        // Check if view count should be displayed
        $show_hide = get_post_meta( $product_id, '_pvc_show_hide', true ) ?: 'show';

        if ( $show_hide === 'hide' ) {
            return;
        }

        // Check global settings (with fallback to show by default)
        $display_settings = [ 'shop', 'single' ]; // Default fallback

        if ( class_exists( 'WPPlugines\Product_View_Count\Helper' ) ) {
            $display_settings = Helper::get_option( 'product-view-count_basic', 'display_view_count', [ 'shop', 'single' ] );
        }

        // If settings is not an array, default to showing everywhere
        if ( ! is_array( $display_settings ) ) {
            $display_settings = [ 'shop', 'single' ];
        }

        if ( $context === 'shop' && ! in_array( 'shop', $display_settings ) ) {
            return;
        }

        if ( $context === 'single' && ! in_array( 'single', $display_settings ) ) {
            return;
        }

        // Get view count
        $view_count = $this->metabox->calculate_display_count( $product_id );
        
        if ( $view_count <= 0 ) {
            return;
        }

        // Get label text with fallback
        $label_text = __( 'Views', 'product-view-count' ); // Default fallback

        if ( class_exists( 'WPPlugines\Product_View_Count\Helper' ) ) {
            $label_text = Helper::get_option( 'product-view-count_basic', 'views_text', __( 'Views', 'product-view-count' ) );
        }

        // if ( empty( $label_text ) ) {
        //     $label_text = __( 'Views', 'product-view-count' );
        // }
        
        // Get count type for label
        $count_type = get_post_meta( $product_id, '_pvc_count_type', true ) ?: 'total';
        $type_label = $this->get_count_type_label( $count_type );

        // Render HTML
        $html = sprintf(
            '<div class="product-view-count pvc-context-%s pvc-type-%s" data-product-id="%d">
                <span class="pvc-icon">👁</span>
                <span class="pvc-count">%s</span>
                <span class="pvc-label">%s</span>
                <span class="pvc-type-label">%s</span>
            </div>',
            esc_attr( $context ),
            esc_attr( $count_type ),
            esc_attr( $product_id ),
            number_format( $view_count ),
            esc_html( $label_text ),
            esc_html( $type_label )
        );

        echo apply_filters( 'pvc_view_count_html', $html, $product_id, $view_count, $context );
    }

    /**
     * Get count type label
     */
    private function get_count_type_label( $count_type ) {
        $labels = [
            'total' => '',
            'daily' => __( '(today)', 'product-view-count' ),
            'weekly' => __( '(this week)', 'product-view-count' )
        ];

        return $labels[ $count_type ] ?? '';
    }

    /**
     * Add view count styles to frontend
     */
    public function add_frontend_styles() {
        ?>
        <style>
        .product-view-count {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #666;
            margin: 5px 0;
            line-height: 1.4;
        }

        .product-view-count .pvc-icon {
            font-size: 14px;
            opacity: 0.8;
        }

        .product-view-count .pvc-count {
            font-weight: 600;
            color: #333;
        }

        .product-view-count .pvc-label {
            font-size: 12px;
        }

        .product-view-count .pvc-type-label {
            font-size: 11px;
            opacity: 0.7;
            font-style: italic;
        }

        /* Context-specific styles */
        .pvc-context-shop {
            font-size: 12px;
        }

        .pvc-context-single {
            font-size: 14px;
            margin: 10px 0;
        }

        /* Type-specific styles */
        .pvc-type-daily .pvc-icon::before {
            content: '📅';
        }

        .pvc-type-weekly .pvc-icon::before {
            content: '📊';
        }

        /* WooCommerce integration */
        .woocommerce ul.products li.product .product-view-count {
            margin-top: 8px;
        }

        .woocommerce div.product .product-view-count {
            margin: 15px 0;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #0073aa;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .product-view-count {
                font-size: 11px;
            }
            
            .pvc-context-single {
                font-size: 12px;
            }
        }

        /* Dark theme support */
        @media (prefers-color-scheme: dark) {
            .product-view-count {
                color: #ccc;
            }
            
            .product-view-count .pvc-count {
                color: #fff;
            }
            
            .woocommerce div.product .product-view-count {
                background: #2a2a2a;
                border-left-color: #00a0d2;
            }
        }
        </style>
        <?php
    }

    /**
     * Get view count for AJAX requests
     */
    public function ajax_get_view_count() {
        // Check nonce
        if ( ! wp_verify_nonce( $_POST['nonce'] ?? '', 'pvc_ajax_nonce' ) ) {
            wp_die( __( 'Security check failed', 'product-view-count' ) );
        }

        $product_id = intval( $_POST['product_id'] ?? 0 );
        
        if ( ! $product_id ) {
            wp_send_json_error( __( 'Invalid product ID', 'product-view-count' ) );
        }

        $view_count = $this->metabox->calculate_display_count( $product_id );
        $count_type = get_post_meta( $product_id, '_pvc_count_type', true ) ?: 'total';
        
        wp_send_json_success( [
            'count' => $view_count,
            'formatted_count' => number_format( $view_count ),
            'type' => $count_type,
            'type_label' => $this->get_count_type_label( $count_type )
        ] );
    }

    /**
     * Add AJAX script for dynamic updates
     */
    public function add_ajax_script() {
        if ( ! is_woocommerce() && ! is_shop() && ! is_product() ) {
            return;
        }
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to update view count
            function updateViewCount(productId, element) {
                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'POST',
                    data: {
                        action: 'pvc_get_view_count',
                        product_id: productId,
                        nonce: '<?php echo wp_create_nonce( 'pvc_ajax_nonce' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            element.find('.pvc-count').text(response.data.formatted_count);
                            element.find('.pvc-type-label').text(response.data.type_label);
                        }
                    }
                });
            }

            // Auto-refresh view counts every 30 seconds for daily/weekly types
            $('.product-view-count[data-product-id]').each(function() {
                var $this = $(this);
                var productId = $this.data('product-id');
                var isDaily = $this.hasClass('pvc-type-daily');
                var isWeekly = $this.hasClass('pvc-type-weekly');
                
                if (isDaily || isWeekly) {
                    setInterval(function() {
                        updateViewCount(productId, $this);
                    }, 30000); // 30 seconds
                }
            });

            // Manual refresh on click (for development/testing)
            $('.product-view-count').on('dblclick', function() {
                var $this = $(this);
                var productId = $this.data('product-id');
                updateViewCount(productId, $this);
            });
        });
        </script>
        <?php
    }

    /**
     * Initialize frontend hooks
     */
    public function init_frontend_hooks() {
        // WooCommerce hooks
        add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'display_shop_view_count' ], 15 );
        add_action( 'woocommerce_single_product_summary', [ $this, 'display_single_view_count' ], 25 );
        
        // Styles and scripts
        add_action( 'wp_head', [ $this, 'add_frontend_styles' ] );
        add_action( 'wp_footer', [ $this, 'add_ajax_script' ] );
        
        // AJAX handlers
        add_action( 'wp_ajax_pvc_get_view_count', [ $this, 'ajax_get_view_count' ] );
        add_action( 'wp_ajax_nopriv_pvc_get_view_count', [ $this, 'ajax_get_view_count' ] );
    }

    /**
     * Get shortcode for manual placement
     */
    public function view_count_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'id' => get_the_ID(),
            'context' => 'shortcode'
        ], $atts, 'product_view_count' );

        if ( ! $atts['id'] ) {
            return '';
        }

        ob_start();
        $this->render_view_count( intval( $atts['id'] ), $atts['context'] );
        return ob_get_clean();
    }
}
