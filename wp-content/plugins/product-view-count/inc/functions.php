<?php
if( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Gets the site's base URL
 *
 * @uses get_bloginfo()
 *
 * @return string $url the site URL
 */
if( ! function_exists( 'pvc_site_url' ) ) :
function pvc_site_url() {
	$url = get_bloginfo( 'url' );

	return $url;
}
endif;

// =============================================================================
// DATABASE FUNCTIONS FOR PRODUCT VIEW COUNT
// =============================================================================

/**
 * Get table names for PVC database tables
 *
 * @return array Array containing table names
 */
if( ! function_exists( 'pvc_get_table_names' ) ) :
function pvc_get_table_names() {
	global $wpdb;

	return [
		'views' => $wpdb->prefix . 'pvc_product_views',
		'logs' => $wpdb->prefix . 'pvc_view_logs'
	];
}
endif;

/**
 * Check if PVC database tables exist
 *
 * @return bool True if tables exist, false otherwise
 */
if( ! function_exists( 'pvc_tables_exist' ) ) :
function pvc_tables_exist() {
	global $wpdb;

	$tables = pvc_get_table_names();

	$views_table_exists = $wpdb->get_var( $wpdb->prepare(
		"SHOW TABLES LIKE %s",
		$tables['views']
	) );

	$logs_table_exists = $wpdb->get_var( $wpdb->prepare(
		"SHOW TABLES LIKE %s",
		$tables['logs']
	) );

	return $views_table_exists && $logs_table_exists;
}
endif;

/**
 * Get total product count
 *
 * @return int Total number of published products
 */
if( ! function_exists( 'pvc_get_total_products' ) ) :
function pvc_get_total_products() {
	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM {$wpdb->posts}
		 WHERE post_type = 'product' AND post_status = 'publish'"
	);
}
endif;

/**
 * Get products with view counts from post meta
 *
 * @return int Number of products with view counts in post meta
 */
if( ! function_exists( 'pvc_get_products_with_meta_views' ) ) :
function pvc_get_products_with_meta_views() {
	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
		 WHERE meta_key = 'product_view_count' AND meta_value > 0"
	);
}
endif;

/**
 * Get total views from post meta
 *
 * @return int Total views from post meta
 */
if( ! function_exists( 'pvc_get_total_meta_views' ) ) :
function pvc_get_total_meta_views() {
	global $wpdb;

	return (int) $wpdb->get_var(
		"SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta}
		 WHERE meta_key = 'product_view_count'"
	);
}
endif;

/**
 * Get products with view counts from database tables
 *
 * @return int Number of products with view counts in database tables
 */
if( ! function_exists( 'pvc_get_products_with_table_views' ) ) :
function pvc_get_products_with_table_views() {
	global $wpdb;

	$tables = pvc_get_table_names();

	return (int) $wpdb->get_var(
		"SELECT COUNT(*) FROM {$tables['views']}"
	);
}
endif;

/**
 * Get total views from database tables
 *
 * @return int Total views from database tables
 */
if( ! function_exists( 'pvc_get_total_table_views' ) ) :
function pvc_get_total_table_views() {
	global $wpdb;

	$tables = pvc_get_table_names();

	return (int) $wpdb->get_var(
		"SELECT SUM(total_views) FROM {$tables['views']}"
	);
}
endif;

/**
 * Check if product already exists in views table
 *
 * @param int $product_id Product ID to check
 * @return bool True if product exists, false otherwise
 */
if( ! function_exists( 'pvc_product_exists_in_views_table' ) ) :
function pvc_product_exists_in_views_table( $product_id ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	$exists = $wpdb->get_var( $wpdb->prepare(
		"SELECT product_id FROM {$tables['views']} WHERE product_id = %d",
		$product_id
	) );

	return ! empty( $exists );
}
endif;

/**
 * Insert product view record into aggregated views table
 *
 * @param int $product_id Product ID
 * @param int $total_views Total views
 * @param int $unique_views Unique views
 * @param int $guest_views Guest views
 * @param int $user_views User views
 * @return bool|int False on failure, number of rows affected on success
 */
if( ! function_exists( 'pvc_insert_product_views' ) ) :
function pvc_insert_product_views( $product_id, $total_views, $unique_views = null, $guest_views = null, $user_views = null ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	// Set defaults if not provided
	if ( $unique_views === null ) $unique_views = $total_views;
	if ( $guest_views === null ) $guest_views = $total_views;
	if ( $user_views === null ) $user_views = 0;

	$result = $wpdb->insert(
		$tables['views'],
		[
			'product_id' => $product_id,
			'total_views' => $total_views,
			'unique_views' => $unique_views,
			'guest_views' => $guest_views,
			'user_views' => $user_views,
			'last_viewed' => current_time( 'mysql' )
		],
		[ '%d', '%d', '%d', '%d', '%d', '%s' ]
	);

	return $result;
}
endif;

/**
 * Insert view log record
 *
 * @param int $product_id Product ID
 * @param array $args View log arguments
 * @return bool|int False on failure, number of rows affected on success
 */
if( ! function_exists( 'pvc_insert_view_log' ) ) :
function pvc_insert_view_log( $product_id, $args = [] ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	$defaults = [
		'user_id' => 0,
		'user_type' => 'guest',
		'user_role' => null,
		'ip_address' => '127.0.0.1',
		'user_agent' => 'Unknown',
		'referrer' => '',
		'session_id' => '',
		'is_unique' => 1,
		'is_bot' => 0,
		'country_code' => null,
		'viewed_at' => current_time( 'mysql' )
	];

	$args = wp_parse_args( $args, $defaults );

	$result = $wpdb->insert(
		$tables['logs'],
		[
			'product_id' => $product_id,
			'user_id' => $args['user_id'],
			'user_type' => $args['user_type'],
			'user_role' => $args['user_role'],
			'ip_address' => $args['ip_address'],
			'user_agent' => $args['user_agent'],
			'referrer' => $args['referrer'],
			'session_id' => $args['session_id'],
			'is_unique' => $args['is_unique'],
			'is_bot' => $args['is_bot'],
			'country_code' => $args['country_code'],
			'viewed_at' => $args['viewed_at']
		],
		[ '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' ]
	);

	return $result;
}
endif;

/**
 * Get products with view counts for migration (batch)
 *
 * @param int $limit Number of products to retrieve
 * @param int $offset Offset for pagination
 * @return array Array of products with view counts
 */
if( ! function_exists( 'pvc_get_products_for_migration' ) ) :
function pvc_get_products_for_migration( $limit = 50, $offset = 0 ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT p.ID, pm.meta_value as view_count
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		 WHERE p.post_type = 'product'
		 AND p.post_status = 'publish'
		 AND pm.meta_key = 'product_view_count'
		 AND pm.meta_value > 0
		 ORDER BY p.ID ASC
		 LIMIT %d OFFSET %d",
		$limit,
		$offset
	) );
}
endif;

/**
 * Get analytics data for date range
 *
 * @param string $start_date Start date (Y-m-d format)
 * @param string $end_date End date (Y-m-d format)
 * @return array Array of daily view data
 */
if( ! function_exists( 'pvc_get_analytics_data' ) ) :
function pvc_get_analytics_data( $start_date, $end_date ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	if ( ! pvc_tables_exist() ) {
		return [];
	}

	$results = $wpdb->get_results( $wpdb->prepare(
		"SELECT DATE(viewed_at) as date,
				COUNT(*) as total_views,
				COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_views
		 FROM {$tables['logs']}
		 WHERE viewed_at BETWEEN %s AND %s
		 AND is_bot = 0
		 GROUP BY DATE(viewed_at)
		 ORDER BY date ASC",
		$start_date . ' 00:00:00',
		$end_date . ' 23:59:59'
	) );

	return $results ?: [];
}
endif;

/**
 * Get top products for date range
 *
 * @param string $start_date Start date (Y-m-d format)
 * @param string $end_date End date (Y-m-d format)
 * @param int $limit Number of products to retrieve
 * @return array Array of top products
 */
if( ! function_exists( 'pvc_get_top_products' ) ) :
function pvc_get_top_products( $start_date, $end_date, $limit = 10 ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	if ( ! pvc_tables_exist() ) {
		return pvc_get_top_products_fallback( $limit );
	}

	$results = $wpdb->get_results( $wpdb->prepare(
		"SELECT p.ID, p.post_title as name,
				COUNT(vl.id) as total_views,
				COUNT(CASE WHEN vl.is_unique = 1 THEN 1 END) as unique_views
		 FROM {$wpdb->posts} p
		 INNER JOIN {$tables['logs']} vl ON p.ID = vl.product_id
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

	return $results ?: [];
}
endif;

/**
 * Get top products fallback (using post meta)
 *
 * @param int $limit Number of products to retrieve
 * @return array Array of top products from post meta
 */
if( ! function_exists( 'pvc_get_top_products_fallback' ) ) :
function pvc_get_top_products_fallback( $limit = 10 ) {
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

	return $results ?: [];
}
endif;

/**
 * Get analytics statistics for date range
 *
 * @param string $start_date Start date (Y-m-d format)
 * @param string $end_date End date (Y-m-d format)
 * @return array Array of statistics
 */
if( ! function_exists( 'pvc_get_analytics_stats' ) ) :
function pvc_get_analytics_stats( $start_date, $end_date ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	if ( ! pvc_tables_exist() ) {
		return pvc_get_analytics_stats_fallback();
	}

	$stats = $wpdb->get_row( $wpdb->prepare(
		"SELECT COUNT(*) as total_views,
				COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_visitors,
				COUNT(DISTINCT product_id) as products_viewed
		 FROM {$tables['logs']}
		 WHERE viewed_at BETWEEN %s AND %s
		 AND is_bot = 0",
		$start_date . ' 00:00:00',
		$end_date . ' 23:59:59'
	) );

	if ( ! $stats ) {
		return pvc_get_analytics_stats_fallback();
	}

	// Get top viewed product
	$top_product = $wpdb->get_row( $wpdb->prepare(
		"SELECT p.ID, p.post_title as name, COUNT(vl.id) as views
		 FROM {$wpdb->posts} p
		 INNER JOIN {$tables['logs']} vl ON p.ID = vl.product_id
		 WHERE p.post_type = 'product'
		 AND vl.viewed_at BETWEEN %s AND %s
		 AND vl.is_bot = 0
		 GROUP BY p.ID
		 ORDER BY views DESC
		 LIMIT 1",
		$start_date . ' 00:00:00',
		$end_date . ' 23:59:59'
	) );

	return [
		'totalViews' => (int) $stats->total_views,
		'uniqueVisitors' => (int) $stats->unique_visitors,
		'avgViewsPerProduct' => $stats->products_viewed > 0 ? round( $stats->total_views / $stats->products_viewed, 1 ) : 0,
		'topViewedProduct' => $top_product ? [
			'id' => $top_product->ID,
			'name' => $top_product->name,
			'views' => (int) $top_product->views
		] : null
	];
}
endif;

/**
 * Get analytics statistics fallback (using post meta)
 *
 * @return array Array of statistics from post meta
 */
if( ! function_exists( 'pvc_get_analytics_stats_fallback' ) ) :
function pvc_get_analytics_stats_fallback() {
	global $wpdb;

	$total_views = $wpdb->get_var(
		"SELECT SUM(CAST(meta_value AS UNSIGNED)) FROM {$wpdb->postmeta}
		 WHERE meta_key = 'product_view_count'"
	);

	$products_with_views = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
		 WHERE meta_key = 'product_view_count' AND meta_value > 0"
	);

	$top_product = $wpdb->get_row(
		"SELECT p.ID, p.post_title as name, CAST(pm.meta_value AS UNSIGNED) as views
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		 WHERE p.post_type = 'product'
		 AND p.post_status = 'publish'
		 AND pm.meta_key = 'product_view_count'
		 AND pm.meta_value > 0
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
endif;

/**
 * Reset all view counts
 *
 * @return bool True on success, false on failure
 */
if( ! function_exists( 'pvc_reset_all_counts' ) ) :
function pvc_reset_all_counts() {
	global $wpdb;

	$tables = pvc_get_table_names();

	// Truncate custom tables
	$result1 = $wpdb->query( "TRUNCATE TABLE {$tables['views']}" );
	$result2 = $wpdb->query( "TRUNCATE TABLE {$tables['logs']}" );

	// Reset post meta
	$result3 = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = 'product_view_count'" );

	return $result1 !== false && $result2 !== false && $result3 !== false;
}
endif;

/**
 * Reset specific product view count
 *
 * @param int $product_id Product ID
 * @return bool True on success, false on failure
 */
if( ! function_exists( 'pvc_reset_product_count' ) ) :
function pvc_reset_product_count( $product_id ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	// Delete from custom tables
	$result1 = $wpdb->delete( $tables['views'], [ 'product_id' => $product_id ], [ '%d' ] );
	$result2 = $wpdb->delete( $tables['logs'], [ 'product_id' => $product_id ], [ '%d' ] );

	// Delete post meta
	$result3 = delete_post_meta( $product_id, 'product_view_count' );

	return $result1 !== false && $result2 !== false;
}
endif;

/**
 * Get view count from logs table for specific product
 *
 * @param int $product_id Product ID
 * @param string $start_date Optional start date (Y-m-d format)
 * @param string $end_date Optional end date (Y-m-d format)
 * @return int View count
 */
if( ! function_exists( 'pvc_get_view_count_from_logs' ) ) :
function pvc_get_view_count_from_logs( $product_id, $start_date = null, $end_date = null ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	if ( ! pvc_tables_exist() ) {
		return 0;
	}

	$where_clause = "WHERE product_id = %d AND is_bot = 0";
	$params = [ $product_id ];

	if ( $start_date && $end_date ) {
		$where_clause .= " AND viewed_at BETWEEN %s AND %s";
		$params[] = $start_date . ' 00:00:00';
		$params[] = $end_date . ' 23:59:59';
	}

	return (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*) FROM {$tables['logs']} {$where_clause}",
		...$params
	) );
}
endif;

/**
 * Get products for sample data creation
 *
 * @param int $limit Number of products to retrieve
 * @return array Array of product objects
 */
if( ! function_exists( 'pvc_get_products_for_sample_data' ) ) :
function pvc_get_products_for_sample_data( $limit = 5 ) {
	global $wpdb;

	return $wpdb->get_results( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts}
		 WHERE post_type = 'product' AND post_status = 'publish'
		 LIMIT %d",
		$limit
	) );
}
endif;

/**
 * Get aggregated stats for a product from logs
 *
 * @param int $product_id Product ID
 * @return object|null Stats object or null
 */
if( ! function_exists( 'pvc_get_product_stats_from_logs' ) ) :
function pvc_get_product_stats_from_logs( $product_id ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	if ( ! pvc_tables_exist() ) {
		return null;
	}

	return $wpdb->get_row( $wpdb->prepare(
		"SELECT COUNT(*) as total_views,
				COUNT(CASE WHEN is_unique = 1 THEN 1 END) as unique_views,
				COUNT(CASE WHEN user_type = 'guest' THEN 1 END) as guest_views,
				COUNT(CASE WHEN user_type = 'user' THEN 1 END) as user_views
		 FROM {$tables['logs']}
		 WHERE product_id = %d AND is_bot = 0",
		$product_id
	) );
}
endif;

/**
 * Update or insert aggregated view data
 *
 * @param int $product_id Product ID
 * @param array $data View data
 * @return bool|int False on failure, number of rows affected on success
 */
if( ! function_exists( 'pvc_upsert_aggregated_views' ) ) :
function pvc_upsert_aggregated_views( $product_id, $data ) {
	global $wpdb;

	$tables = pvc_get_table_names();

	$defaults = [
		'total_views' => 0,
		'unique_views' => 0,
		'guest_views' => 0,
		'user_views' => 0,
		'last_viewed' => current_time( 'mysql' )
	];

	$data = wp_parse_args( $data, $defaults );
	$data['product_id'] = $product_id;

	return $wpdb->replace( $tables['views'], $data );
}
endif;