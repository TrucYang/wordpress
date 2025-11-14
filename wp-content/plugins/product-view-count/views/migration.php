<?php
/**
 * Migration Status View
 */

use WPPlugines\Product_View_Count\App\Migration;

// Get migration instance
$migration = new Migration();
$migration_info = $migration->get_migration_info();
$status = $migration_info['status'];

?>

<div class="pvc-migration-status">
    <div class="pvc-migration-header">
        <h2><?php _e( 'Data Migration Status', 'product-view-count' ); ?></h2>
        <p><?php _e( 'This page shows the status of migrating view count data from WordPress post meta to the new database tables.', 'product-view-count' ); ?></p>
    </div>

    <div class="pvc-migration-cards">
        <!-- Migration Status Card -->
        <div class="pvc-card">
            <h3><?php _e( 'Migration Status', 'product-view-count' ); ?></h3>
            <div class="pvc-status-indicator">
                <?php
                $status_class = '';
                $status_text = '';
                $status_icon = '';

                switch ( $status['status'] ) {
                    case 'completed':
                        $status_class = 'success';
                        $status_text = __( 'Completed', 'product-view-count' );
                        $status_icon = '✅';
                        break;
                    case 'running':
                        $status_class = 'warning';
                        $status_text = __( 'In Progress', 'product-view-count' );
                        $status_icon = '⏳';
                        break;
                    case 'failed':
                        $status_class = 'error';
                        $status_text = __( 'Failed', 'product-view-count' );
                        $status_icon = '❌';
                        break;
                    default:
                        $status_class = 'pending';
                        $status_text = __( 'Pending', 'product-view-count' );
                        $status_icon = '⏸️';
                }
                ?>
                <span class="status-badge <?php echo esc_attr( $status_class ); ?>">
                    <?php echo $status_icon; ?> <?php echo esc_html( $status_text ); ?>
                </span>
            </div>
            <p><strong><?php _e( 'Message:', 'product-view-count' ); ?></strong> <?php echo esc_html( $status['message'] ); ?></p>
            <?php if ( $status['timestamp'] ): ?>
                <p><strong><?php _e( 'Last Updated:', 'product-view-count' ); ?></strong> <?php echo esc_html( $status['timestamp'] ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Data Comparison Card -->
        <div class="pvc-card">
            <h3><?php _e( 'Data Comparison', 'product-view-count' ); ?></h3>
            <table class="pvc-comparison-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Source', 'product-view-count' ); ?></th>
                        <th><?php _e( 'Products', 'product-view-count' ); ?></th>
                        <th><?php _e( 'Total Views', 'product-view-count' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php _e( 'Post Meta (Old)', 'product-view-count' ); ?></td>
                        <td><?php echo number_format( $migration_info['meta_products'] ); ?></td>
                        <td><?php echo number_format( $migration_info['meta_total_views'] ); ?></td>
                    </tr>
                    <tr>
                        <td><?php _e( 'Database Tables (New)', 'product-view-count' ); ?></td>
                        <td><?php echo number_format( $migration_info['table_products'] ); ?></td>
                        <td><?php echo number_format( $migration_info['table_total_views'] ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Migration Actions -->
    <div class="pvc-migration-actions">
        <?php if ( $migration_info['migration_needed'] && $status['status'] !== 'running' ): ?>
            <div class="pvc-card">
                <h3><?php _e( 'Migration Required', 'product-view-count' ); ?></h3>
                <p><?php _e( 'Your site has view count data that needs to be migrated to the new database structure.', 'product-view-count' ); ?></p>
                <button id="pvc-start-migration" class="button button-primary">
                    <?php _e( 'Start Migration', 'product-view-count' ); ?>
                </button>
            </div>
        <?php elseif ( $status['status'] === 'running' ): ?>
            <div class="pvc-card">
                <h3><?php _e( 'Migration in Progress', 'product-view-count' ); ?></h3>
                <p><?php _e( 'Please wait while the migration completes. This page will refresh automatically.', 'product-view-count' ); ?></p>
                <div class="pvc-progress-bar">
                    <div class="pvc-progress-fill" style="width: 50%;"></div>
                </div>
            </div>
        <?php else: ?>
            <div class="pvc-card">
                <h3><?php _e( 'Migration Complete', 'product-view-count' ); ?></h3>
                <p><?php _e( 'All view count data has been successfully migrated to the new database structure.', 'product-view-count' ); ?></p>
                <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ): ?>
                    <button id="pvc-force-migration" class="button button-secondary">
                        <?php _e( 'Force Re-migration (Debug)', 'product-view-count' ); ?>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.pvc-migration-status {
    max-width: 1200px;
}

.pvc-migration-header {
    margin-bottom: 30px;
}

.pvc-migration-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.pvc-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pvc-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
}

.pvc-status-indicator {
    margin-bottom: 15px;
}

.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
}

.status-badge.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-badge.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-badge.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-badge.pending {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

.pvc-comparison-table {
    width: 100%;
    border-collapse: collapse;
}

.pvc-comparison-table th,
.pvc-comparison-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.pvc-comparison-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.pvc-migration-actions {
    margin-top: 30px;
}

.pvc-progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 15px;
}

.pvc-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007cba, #00a0d2);
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .pvc-migration-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Auto-refresh if migration is running
    <?php if ( $status['status'] === 'running' ): ?>
    setTimeout(function() {
        location.reload();
    }, 5000);
    <?php endif; ?>

    // Start migration button
    $('#pvc-start-migration').on('click', function() {
        if (!confirm('<?php _e( 'Are you sure you want to start the migration? This process may take several minutes.', 'product-view-count' ); ?>')) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true).text('<?php _e( 'Starting...', 'product-view-count' ); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pvc_trigger_migration',
                nonce: '<?php echo wp_create_nonce( 'pvc_migration_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e( 'Migration started successfully!', 'product-view-count' ); ?>');
                    location.reload();
                } else {
                    alert('<?php _e( 'Migration failed:', 'product-view-count' ); ?> ' + response.data);
                    $button.prop('disabled', false).text('<?php _e( 'Start Migration', 'product-view-count' ); ?>');
                }
            },
            error: function() {
                alert('<?php _e( 'An error occurred. Please try again.', 'product-view-count' ); ?>');
                $button.prop('disabled', false).text('<?php _e( 'Start Migration', 'product-view-count' ); ?>');
            }
        });
    });

    // Force migration button (debug only)
    $('#pvc-force-migration').on('click', function() {
        if (!confirm('<?php _e( 'This will reset and re-run the migration. Continue?', 'product-view-count' ); ?>')) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true).text('<?php _e( 'Processing...', 'product-view-count' ); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'pvc_trigger_migration',
                nonce: '<?php echo wp_create_nonce( 'pvc_migration_nonce' ); ?>'
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('<?php _e( 'An error occurred. Please try again.', 'product-view-count' ); ?>');
                $button.prop('disabled', false).text('<?php _e( 'Force Re-migration (Debug)', 'product-view-count' ); ?>');
            }
        });
    });
});
</script>
