<?php
/**
 * Email Reporting System for Product View Count AI
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
 * @subpackage EmailReports
 * @author WPPlugines <alimranakash.bd@gmail.com>
 */
class EmailReports extends Base {

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
     * Initialize email reporting hooks
     */
    public function init_hooks() {
        add_action( 'pvc_weekly_report', [ $this, 'send_weekly_report' ] );
        add_action( 'pvc_monthly_report', [ $this, 'send_monthly_report' ] );
        add_action( 'init', [ $this, 'schedule_reports' ] );
        add_action( 'wp_ajax_pvc_test_email', [ $this, 'send_test_email' ] );
    }

    /**
     * Schedule email reports
     */
    public function schedule_reports() {
        $settings = Helper::get_option( 'product-view-count_email', 'email_settings', [
            'weekly_enabled' => false,
            'monthly_enabled' => false,
            'recipients' => [ get_option( 'admin_email' ) ]
        ] );

        // Schedule weekly report
        if ( $settings['weekly_enabled'] && ! wp_next_scheduled( 'pvc_weekly_report' ) ) {
            wp_schedule_event( strtotime( 'next monday 9:00' ), 'weekly', 'pvc_weekly_report' );
        } elseif ( ! $settings['weekly_enabled'] && wp_next_scheduled( 'pvc_weekly_report' ) ) {
            wp_clear_scheduled_hook( 'pvc_weekly_report' );
        }

        // Schedule monthly report
        if ( $settings['monthly_enabled'] && ! wp_next_scheduled( 'pvc_monthly_report' ) ) {
            wp_schedule_event( strtotime( 'first day of next month 9:00' ), 'monthly', 'pvc_monthly_report' );
        } elseif ( ! $settings['monthly_enabled'] && wp_next_scheduled( 'pvc_monthly_report' ) ) {
            wp_clear_scheduled_hook( 'pvc_monthly_report' );
        }
    }

    /**
     * Send weekly report
     */
    public function send_weekly_report() {
        $start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
        $end_date = date( 'Y-m-d', strtotime( '-1 day' ) );
        
        $this->send_report( 'weekly', $start_date, $end_date );
    }

    /**
     * Send monthly report
     */
    public function send_monthly_report() {
        $start_date = date( 'Y-m-01', strtotime( '-1 month' ) );
        $end_date = date( 'Y-m-t', strtotime( '-1 month' ) );
        
        $this->send_report( 'monthly', $start_date, $end_date );
    }

    /**
     * Send analytics report
     */
    private function send_report( $type, $start_date, $end_date ) {
        $settings = Helper::get_option( 'product-view-count_email', 'email_settings', [
            'recipients' => [ get_option( 'admin_email' ) ]
        ] );

        if ( empty( $settings['recipients'] ) ) {
            return false;
        }

        // Get analytics data using our centralized functions
        $stats = pvc_get_analytics_stats( $start_date, $end_date );
        $top_products = pvc_get_top_products( $start_date, $end_date, 10 );
        $analytics_data = pvc_get_analytics_data( $start_date, $end_date );

        // Generate email content
        $subject = sprintf( 
            __( '%s Product Views Report - %s', 'product-view-count' ),
            ucfirst( $type ),
            get_bloginfo( 'name' )
        );

        $message = $this->generate_email_template( $type, $start_date, $end_date, $stats, $top_products, $analytics_data );

        // Send email to each recipient
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
        ];

        foreach ( $settings['recipients'] as $recipient ) {
            wp_mail( $recipient, $subject, $message, $headers );
        }

        return true;
    }

    /**
     * Generate email template
     */
    private function generate_email_template( $type, $start_date, $end_date, $stats, $top_products, $analytics_data ) {
        $site_name = get_bloginfo( 'name' );
        $site_url = get_site_url();
        $dashboard_url = admin_url( 'admin.php?page=product-views' );
        
        $period_text = sprintf( 
            __( '%s to %s', 'product-view-count' ),
            date( 'F j, Y', strtotime( $start_date ) ),
            date( 'F j, Y', strtotime( $end_date ) )
        );

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html( sprintf( __( '%s Product Views Report', 'product-view-count' ), ucfirst( $type ) ) ); ?></title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #0073aa, #005177); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .header p { margin: 10px 0 0; opacity: 0.9; }
                .content { padding: 30px 20px; }
                .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin: 20px 0; }
                .stat-card { background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center; border-left: 4px solid #0073aa; }
                .stat-number { font-size: 24px; font-weight: bold; color: #0073aa; margin-bottom: 5px; }
                .stat-label { font-size: 12px; color: #666; text-transform: uppercase; }
                .section { margin: 30px 0; }
                .section h2 { color: #0073aa; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
                .product-list { list-style: none; padding: 0; }
                .product-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
                .product-name { font-weight: 500; }
                .product-views { color: #0073aa; font-weight: bold; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 14px; }
                .button { display: inline-block; background: #0073aa; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin: 10px 0; }
                .chart-placeholder { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 6px; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1><?php echo esc_html( sprintf( __( '%s Product Views Report', 'product-view-count' ), ucfirst( $type ) ) ); ?></h1>
                    <p><?php echo esc_html( $period_text ); ?></p>
                </div>

                <div class="content">
                    <div class="section">
                        <h2><?php _e( 'Overview', 'product-view-count' ); ?></h2>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format( $stats['totalViews'] ); ?></div>
                                <div class="stat-label"><?php _e( 'Total Views', 'product-view-count' ); ?></div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format( $stats['uniqueVisitors'] ); ?></div>
                                <div class="stat-label"><?php _e( 'Unique Visitors', 'product-view-count' ); ?></div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number"><?php echo $stats['avgViewsPerProduct']; ?></div>
                                <div class="stat-label"><?php _e( 'Avg Views/Product', 'product-view-count' ); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if ( ! empty( $top_products ) ): ?>
                    <div class="section">
                        <h2><?php _e( 'Top Viewed Products', 'product-view-count' ); ?></h2>
                        <ul class="product-list">
                            <?php foreach ( array_slice( $top_products, 0, 5 ) as $index => $product ): ?>
                            <li class="product-item">
                                <span class="product-name">
                                    <?php echo ($index + 1) . '. ' . esc_html( $product->name ); ?>
                                </span>
                                <span class="product-views"><?php echo number_format( $product->total_views ); ?> views</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="section">
                        <h2><?php _e( 'Daily Breakdown', 'product-view-count' ); ?></h2>
                        <div class="chart-placeholder">
                            <?php if ( ! empty( $analytics_data ) ): ?>
                                <?php foreach ( array_slice( $analytics_data, -7 ) as $day ): ?>
                                    <p><?php echo date( 'M j', strtotime( $day->date ) ); ?>: <?php echo number_format( $day->total_views ); ?> views</p>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p><?php _e( 'No data available for this period', 'product-view-count' ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="<?php echo esc_url( $dashboard_url ); ?>" class="button">
                            <?php _e( 'View Full Analytics Dashboard', 'product-view-count' ); ?>
                        </a>
                    </div>
                </div>

                <div class="footer">
                    <p><?php echo sprintf( __( 'This report was generated by %s', 'product-view-count' ), '<strong>' . esc_html( $site_name ) . '</strong>' ); ?></p>
                    <p><a href="<?php echo esc_url( $site_url ); ?>"><?php echo esc_html( $site_url ); ?></a></p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Send test email (AJAX handler)
     */
    public function send_test_email() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized', 'product-view-count' ) );
        }

        check_ajax_referer( 'pvc_test_email', 'nonce' );

        $email = sanitize_email( $_POST['email'] ?? get_option( 'admin_email' ) );
        
        if ( ! is_email( $email ) ) {
            wp_send_json_error( __( 'Invalid email address', 'product-view-count' ) );
        }

        // Send test report with last 7 days data
        $start_date = date( 'Y-m-d', strtotime( '-7 days' ) );
        $end_date = date( 'Y-m-d' );
        
        $stats = pvc_get_analytics_stats( $start_date, $end_date );
        $top_products = pvc_get_top_products( $start_date, $end_date, 10 );
        $analytics_data = pvc_get_analytics_data( $start_date, $end_date );

        $subject = sprintf( 
            __( 'Test Product Views Report - %s', 'product-view-count' ),
            get_bloginfo( 'name' )
        );

        $message = $this->generate_email_template( 'test', $start_date, $end_date, $stats, $top_products, $analytics_data );

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
        ];

        $sent = wp_mail( $email, $subject, $message, $headers );

        if ( $sent ) {
            wp_send_json_success( __( 'Test email sent successfully!', 'product-view-count' ) );
        } else {
            wp_send_json_error( __( 'Failed to send test email', 'product-view-count' ) );
        }
    }

    /**
     * Get next scheduled report times
     */
    public function get_next_scheduled_times() {
        return [
            'weekly' => wp_next_scheduled( 'pvc_weekly_report' ),
            'monthly' => wp_next_scheduled( 'pvc_monthly_report' )
        ];
    }

    /**
     * Clear all scheduled reports
     */
    public function clear_scheduled_reports() {
        wp_clear_scheduled_hook( 'pvc_weekly_report' );
        wp_clear_scheduled_hook( 'pvc_monthly_report' );
    }
}
