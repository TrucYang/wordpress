<?php
/**
 * Product Metabox for View Count Settings
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
 * @subpackage ProductMetabox
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class ProductMetabox extends Base {

    public $plugin;

    /**
     * Constructor function
     */
    public function __construct( $plugin = [] ) {
        $this->plugin = $plugin;
        $this->slug = $plugin['TextDomain'] ?? 'product-view-count';
        $this->name = $plugin['Name'] ?? 'Product View Count';
        $this->version = $plugin['Version'] ?? '2.0.0';
    }

    /**
     * Add metabox to product edit screen
     */
    public function add_metabox() {
        add_meta_box(
            'pvc-product-settings',
            __( 'Product View Count Settings', 'product-view-count' ),
            [ $this, 'render_metabox' ],
            'product',
            'side',
            'default'
        );
    }

    /**
     * Render metabox content
     */
    public function render_metabox( $post ) {
        // Add nonce for security
        wp_nonce_field( 'pvc_product_settings_nonce', 'pvc_product_settings_nonce' );

        // Get current values
        $select_type = get_post_meta( $post->ID, '_pvc_select_type', true ) ?: 'real';
        $show_hide = get_post_meta( $post->ID, '_pvc_show_hide', true ) ?: 'show';
        $count_type = get_post_meta( $post->ID, '_pvc_count_type', true ) ?: 'total';
        $min_number = get_post_meta( $post->ID, '_pvc_min_number', true ) ?: '';
        $max_number = get_post_meta( $post->ID, '_pvc_max_number', true ) ?: '';

        ?>
        <div class="pvc-metabox-content">
            <table class="form-table">
                <!-- Select Type -->
                <tr>
                    <th scope="row">
                        <label for="pvc_select_type"><?php _e( 'Select Type', 'product-view-count' ); ?></label>
                    </th>
                    <td>
                        <select name="pvc_select_type" id="pvc_select_type" class="widefat">
                            <option value="real" <?php selected( $select_type, 'real' ); ?>><?php _e( 'Real', 'product-view-count' ); ?></option>
                            <option value="random" <?php selected( $select_type, 'random' ); ?>><?php _e( 'Random', 'product-view-count' ); ?></option>
                            <option value="mixed" <?php selected( $select_type, 'mixed' ); ?>><?php _e( 'Mixed', 'product-view-count' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'Choose how view counts are calculated for this product.', 'product-view-count' ); ?></p>
                    </td>
                </tr>

                <!-- Show/Hide Toggle -->
                <tr>
                    <th scope="row">
                        <label for="pvc_show_hide"><?php _e( 'Display', 'product-view-count' ); ?></label>
                    </th>
                    <td>
                        <select name="pvc_show_hide" id="pvc_show_hide" class="widefat">
                            <option value="show" <?php selected( $show_hide, 'show' ); ?>><?php _e( 'Show', 'product-view-count' ); ?></option>
                            <option value="hide" <?php selected( $show_hide, 'hide' ); ?>><?php _e( 'Hide', 'product-view-count' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'Whether to display view count on frontend.', 'product-view-count' ); ?></p>
                    </td>
                </tr>

                <!-- Count Type -->
                <tr>
                    <th scope="row">
                        <label for="pvc_count_type"><?php _e( 'Count Type', 'product-view-count' ); ?></label>
                    </th>
                    <td>
                        <select name="pvc_count_type" id="pvc_count_type" class="widefat">
                            <option value="total" <?php selected( $count_type, 'total' ); ?>><?php _e( 'Total number of views', 'product-view-count' ); ?></option>
                            <option value="daily" <?php selected( $count_type, 'daily' ); ?>><?php _e( 'Daily views', 'product-view-count' ); ?></option>
                            <option value="weekly" <?php selected( $count_type, 'weekly' ); ?>><?php _e( 'Weekly views', 'product-view-count' ); ?></option>
                        </select>
                        <p class="description"><?php _e( 'Which view count type to display.', 'product-view-count' ); ?></p>
                    </td>
                </tr>

                <!-- Min Number -->
                <tr class="pvc-random-field" style="<?php echo ( $select_type === 'real' ) ? 'display: none;' : ''; ?>">
                    <th scope="row">
                        <label for="pvc_min_number"><?php _e( 'Min Number', 'product-view-count' ); ?></label>
                    </th>
                    <td>
                        <input type="number" name="pvc_min_number" id="pvc_min_number" value="<?php echo esc_attr( $min_number ); ?>" class="widefat" min="0" />
                        <p class="description"><?php _e( 'Minimum value for random view count generation.', 'product-view-count' ); ?></p>
                    </td>
                </tr>

                <!-- Max Number -->
                <tr class="pvc-random-field" style="<?php echo ( $select_type === 'real' ) ? 'display: none;' : ''; ?>">
                    <th scope="row">
                        <label for="pvc_max_number"><?php _e( 'Max Number', 'product-view-count' ); ?></label>
                    </th>
                    <td>
                        <input type="number" name="pvc_max_number" id="pvc_max_number" value="<?php echo esc_attr( $max_number ); ?>" class="widefat" min="0" />
                        <p class="description"><?php _e( 'Maximum value for random view count generation.', 'product-view-count' ); ?></p>
                    </td>
                </tr>
            </table>

            <!-- Current View Count Display -->
            <div class="pvc-current-stats" style="margin-top: 15px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                <h4 style="margin: 0 0 10px 0;"><?php _e( 'Current Statistics', 'product-view-count' ); ?></h4>
                <?php
                $real_views = get_post_meta( $post->ID, 'product_view_count', true ) ?: 0;
                $calculated_views = $this->calculate_display_count( $post->ID );
                ?>
                <p><strong><?php _e( 'Real Views:', 'product-view-count' ); ?></strong> <?php echo number_format( $real_views ); ?></p>
                <p><strong><?php _e( 'Display Count:', 'product-view-count' ); ?></strong> <?php echo number_format( $calculated_views ); ?></p>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Toggle min/max fields based on select type
            $('#pvc_select_type').on('change', function() {
                var selectType = $(this).val();
                if (selectType === 'real') {
                    $('.pvc-random-field').hide();
                } else {
                    $('.pvc-random-field').show();
                }
            });

            // Validate min/max values
            $('#pvc_min_number, #pvc_max_number').on('change', function() {
                var minVal = parseInt($('#pvc_min_number').val()) || 0;
                var maxVal = parseInt($('#pvc_max_number').val()) || 0;
                
                if (maxVal > 0 && minVal > maxVal) {
                    alert('<?php _e( 'Minimum value cannot be greater than maximum value.', 'product-view-count' ); ?>');
                    $(this).focus();
                }
            });
        });
        </script>

        <style>
        .pvc-metabox-content .form-table th {
            width: 120px;
            padding: 8px 0;
            font-weight: 600;
        }
        .pvc-metabox-content .form-table td {
            padding: 8px 0;
        }
        .pvc-metabox-content .description {
            font-size: 12px;
            color: #666;
            margin: 5px 0 0 0;
        }
        .pvc-current-stats {
            font-size: 13px;
        }
        .pvc-current-stats p {
            margin: 5px 0;
        }
        </style>
        <?php
    }

    /**
     * Save metabox data
     */
    public function save_metabox( $post_id ) {
        // Check if nonce is valid
        if ( ! isset( $_POST['pvc_product_settings_nonce'] ) || ! wp_verify_nonce( $_POST['pvc_product_settings_nonce'], 'pvc_product_settings_nonce' ) ) {
            return;
        }

        // Check if user has permission to edit
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if this is an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check if this is a product
        if ( get_post_type( $post_id ) !== 'product' ) {
            return;
        }

        // Sanitize and save data
        $fields = [
            '_pvc_select_type' => 'sanitize_text_field',
            '_pvc_show_hide' => 'sanitize_text_field',
            '_pvc_count_type' => 'sanitize_text_field',
            '_pvc_min_number' => 'absint',
            '_pvc_max_number' => 'absint'
        ];

        foreach ( $fields as $field => $sanitize_function ) {
            $form_field = str_replace( '_pvc_', 'pvc_', $field );
            
            if ( isset( $_POST[ $form_field ] ) ) {
                $value = call_user_func( $sanitize_function, $_POST[ $form_field ] );
                update_post_meta( $post_id, $field, $value );
            }
        }

        // Validate min/max values
        $min_number = get_post_meta( $post_id, '_pvc_min_number', true );
        $max_number = get_post_meta( $post_id, '_pvc_max_number', true );

        if ( $min_number && $max_number && $min_number > $max_number ) {
            // Swap values if min > max
            update_post_meta( $post_id, '_pvc_min_number', $max_number );
            update_post_meta( $post_id, '_pvc_max_number', $min_number );
        }
    }

    /**
     * Calculate display count based on settings
     */
    public function calculate_display_count( $product_id ) {
        $select_type = get_post_meta( $product_id, '_pvc_select_type', true ) ?: 'real';
        $count_type = get_post_meta( $product_id, '_pvc_count_type', true ) ?: 'total';
        $min_number = get_post_meta( $product_id, '_pvc_min_number', true ) ?: 0;
        $max_number = get_post_meta( $product_id, '_pvc_max_number', true ) ?: 100;

        // Get real view count
        $real_views = $this->get_real_view_count( $product_id, $count_type );

        switch ( $select_type ) {
            case 'random':
                return $min_number && $max_number ? rand( $min_number, $max_number ) : $real_views;
                
            case 'mixed':
                $random_views = $min_number && $max_number ? rand( $min_number, $max_number ) : 0;
                return $real_views + $random_views;
                
            case 'real':
            default:
                return $real_views;
        }
    }

    /**
     * Get real view count based on type
     */
    private function get_real_view_count( $product_id, $count_type ) {
        global $wpdb;

        switch ( $count_type ) {
            case 'daily':
                // Get today's views
                $views_table = $wpdb->prefix . 'pvc_view_logs';
                if ( $wpdb->get_var( "SHOW TABLES LIKE '$views_table'" ) ) {
                    return $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM $views_table 
                         WHERE product_id = %d AND DATE(viewed_at) = CURDATE() AND is_bot = 0",
                        $product_id
                    ) ) ?: 0;
                }
                return 0;

            case 'weekly':
                // Get this week's views
                $views_table = $wpdb->prefix . 'pvc_view_logs';
                if ( $wpdb->get_var( "SHOW TABLES LIKE '$views_table'" ) ) {
                    return $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM $views_table 
                         WHERE product_id = %d AND YEARWEEK(viewed_at) = YEARWEEK(NOW()) AND is_bot = 0",
                        $product_id
                    ) ) ?: 0;
                }
                return 0;

            case 'total':
            default:
                // Get total views from post meta (fallback to database if available)
                return get_post_meta( $product_id, 'product_view_count', true ) ?: 0;
        }
    }
}
