<?php
// Ensure we have the required constants
if ( ! defined( 'PVC' ) || ! defined( 'ABSPATH' ) ) {
	return;
}

// Debug information (remove in production)
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	error_log( 'PVC Analytics Dashboard: Loading template from ' . __FILE__ );
}
?>
<div id="pvc-analytics-dashboard-wrapper">
	<div id="pvc-admin-dashboard"></div>
	<p class="description">
		<?php _e( 'View detailed analytics and statistics for your product views. This dashboard shows top viewed products, view trends, and other analytics data.', 'product-view-count' ); ?>
	</p>
</div>
<script>
jQuery(document).ready(function($) {
	// Initialize the analytics dashboard whenever the container exists
	function loadAnalyticsDashboard() {
		if (!$('#pvc-admin-dashboard').length) return;

		// Load dashboard assets if not already loaded
		if (!window.pvcDashboardLoaded) {
			window.pvcDashboardLoaded = true;

				// Load CSS
				if (!document.getElementById('pvc-dashboard-css')) {
					var css = document.createElement('link');
					css.id = 'pvc-dashboard-css';
					css.rel = 'stylesheet';
					<?php
					$css_url = plugins_url( 'assets/dist/main.css', PVC );
					if ( $css_url ) : ?>
					css.href = '<?php echo esc_url( $css_url ); ?>';
					document.head.appendChild(css);
					<?php else : ?>
					console.error('Failed to generate CSS URL');
					<?php endif; ?>
				}

				// Load JS and initialize
				if (!document.getElementById('pvc-dashboard-js')) {

						// Set up global data before the app initializes
						window.pvcAdmin = {
							apiUrl: '<?php echo esc_js( rtrim( home_url( '/wp-json/pvc/v1' ), '/' ) ); ?>',
							nonce: '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>',
							adminUrl: '<?php echo esc_js( admin_url( 'admin.php?page=product-view-count#product-view-count_analytics' ) ); ?>',
							woocommerceUrl: '<?php echo esc_js( admin_url( 'edit.php?post_type=product' ) ); ?>'
						};

					var script = document.createElement('script');
					script.id = 'pvc-dashboard-js';
					<?php
					$js_url = plugins_url( 'assets/dist/admin.js', PVC );
					if ( $js_url ) : ?>
					script.src = '<?php echo esc_url( $js_url ); ?>';
					script.onload = function() {
						// Initialize dashboard after script loads (legacy support)
						if (window.pvcInitDashboard) {
							window.pvcInitDashboard();
						}
						// The React bundle mounts on DOMContentLoaded. If this script was loaded after that,
						// dispatch the event so the listener in the bundle can run now.
						try {
							var ev = new Event('DOMContentLoaded');
							document.dispatchEvent(ev);
						} catch (e) {
							var evOld = document.createEvent('Event');
							evOld.initEvent('DOMContentLoaded', true, true);
							document.dispatchEvent(evOld);
						}
					};
					document.head.appendChild(script);
					<?php else : ?>
					console.error('Failed to generate JS URL');
					<?php endif; ?>
				}
		}
	}

	// Load on page load and when visible
	function maybeLoad() {
		setTimeout(loadAnalyticsDashboard, 100);
	}
	maybeLoad();

	// Load when Codexpert tabs switch
	$(document).on('click', '.cx-nav-tab', function() {
		maybeLoad();
	});

	// Also support legacy WP nav-tab clicks
	$(document).on('click', '.nav-tab[href="#product-view-count_analytics"]', function() {
		maybeLoad();
	});

	// When hash changes (e.g., deep link)
	$(window).on('hashchange', function() {
		maybeLoad();
	});
});
</script>