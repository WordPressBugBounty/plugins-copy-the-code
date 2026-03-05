<?php
/**
 * Analytics Database
 *
 * Single table (ctc_analytics) for copy analytics. Used by free plugin to store
 * events; Pro reads from the same table for detailed/export. Created in free on upgrade.
 *
 * @package CTC
 * @since 5.3.0
 */

namespace CTC\Analytics;

use CTC\Helper;

/**
 * Database class.
 *
 * @since 5.3.0
 */
class Database {

	/**
	 * Table name (without prefix).
	 *
	 * @var string
	 */
	const TABLE_NAME = 'ctc_analytics';

	/**
	 * Instance
	 *
	 * @var Database|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Database
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'ctc_analytics_cleanup', [ $this, 'cleanup_scheduled' ] );
	}

	/**
	 * Get table name with WordPress prefix.
	 *
	 * @return string
	 */
	public function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Create database table.
	 * Called from Updater when upgrading to 5.3.0+ or on first install.
	 *
	 * @since 5.3.0
	 * @return void
	 */
	public static function create_table() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = self::get()->get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			rule_id bigint(20) unsigned DEFAULT NULL,
			source varchar(50) NOT NULL DEFAULT 'global-injector',
			success tinyint(1) NOT NULL DEFAULT 1,
			failure_reason text DEFAULT NULL,
			post_id bigint(20) unsigned DEFAULT NULL,
			post_type varchar(20) DEFAULT NULL,
			page_url text DEFAULT NULL,
			device varchar(20) DEFAULT NULL,
			browser varchar(50) DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY rule_id (rule_id),
			KEY created_at (created_at),
			KEY source (source),
			KEY success (success)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Scheduled cleanup callback for analytics events.
	 *
	 * @since 5.4.0
	 * @return void
	 */
	public function cleanup_scheduled() {
		$this->cleanup_old_events();
	}

	/**
	 * Get analytics retention period in days.
	 *
	 * @since 5.4.0
	 * @return int
	 */
	public function get_retention_days() {
		$default_days = 395; // ~13 months.

		/**
		 * Filter analytics retention period in days.
		 *
		 * @since 5.4.0
		 *
		 * @param int $days Number of days to retain analytics events.
		 */
		$days = (int) apply_filters( 'ctc/analytics/retention_days', $default_days );

		if ( $days <= 0 ) {
			return $default_days;
		}

		return $days;
	}

	/**
	 * Delete old analytics events beyond the retention period.
	 *
	 * Uses small batches to avoid long-running deletes on large tables.
	 *
	 * @since 5.4.0
	 * @return void
	 */
	public function cleanup_old_events() {
		global $wpdb;

		$table_name = $this->get_table_name();
		$days       = $this->get_retention_days();

		// Compute cutoff date in UTC for the retention window.
		$cutoff = Helper::mysql_date_ago( "-{$days} days" );

		/**
		 * Filter the batch size used when cleaning up analytics events.
		 *
		 * @since 5.4.0
		 *
		 * @param int $batch_size Number of rows to delete per batch.
		 */
		$batch_size = (int) apply_filters( 'ctc/analytics/cleanup_batch_size', 500 );
		if ( $batch_size <= 0 ) {
			$batch_size = 500;
		}

		do {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$table_name} WHERE created_at < %s LIMIT %d",
					$cutoff,
					$batch_size
				)
			);
		} while ( $deleted && $deleted === $batch_size );
	}

	/**
	 * Insert copy event.
	 *
	 * @since 5.3.0
	 * @param array $data Keys: rule_id, source, success, failure_reason, post_id, post_type, page_url, device, browser.
	 * @return int|false Event ID on success, false on failure.
	 */
	public function insert_event( array $data ) {
		global $wpdb;

		$table_name = $this->get_table_name();

		$rule_id        = isset( $data['rule_id'] ) ? absint( $data['rule_id'] ) : null;
		$source         = isset( $data['source'] ) ? sanitize_text_field( $data['source'] ) : 'global-injector';
		$source         = $source ?: 'global-injector';
		$success        = isset( $data['success'] ) ? (bool) $data['success'] : true;
		$failure_reason = isset( $data['failure_reason'] ) ? sanitize_text_field( $data['failure_reason'] ) : null;
		$post_id        = isset( $data['post_id'] ) ? absint( $data['post_id'] ) : null;
		$post_type      = isset( $data['post_type'] ) ? sanitize_text_field( $data['post_type'] ) : null;
		$page_url       = isset( $data['page_url'] ) ? esc_url_raw( $data['page_url'] ) : null;
		$device         = isset( $data['device'] ) ? sanitize_text_field( $data['device'] ) : null;
		$browser        = isset( $data['browser'] ) ? sanitize_text_field( $data['browser'] ) : null;

		$row = [
			'rule_id'        => $rule_id,
			'source'         => $source,
			'success'        => $success ? 1 : 0,
			'failure_reason' => $failure_reason,
			'post_id'        => $post_id,
			'post_type'      => $post_type,
			'page_url'       => $page_url,
			'device'         => $device,
			'browser'        => $browser,
			'created_at'     => Helper::mysql_now(),
		];

		$result = $wpdb->insert(
			$table_name,
			$row,
			[ '%d', '%s', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s' ]
		);

		if ( false === $result ) {
			return false;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Get total successful copy count in date range.
	 *
	 * @since 5.3.0
	 * @param string   $date_from Start date (Y-m-d H:i:s).
	 * @param string   $date_to   End date (Y-m-d H:i:s).
	 * @param int|null $rule_id   Optional rule ID filter.
	 * @return int
	 */
	public function get_total_copies( $date_from, $date_to, $rule_id = null ) {
		global $wpdb;

		$table_name = $this->get_table_name();

		if ( null !== $rule_id && $rule_id > 0 ) {
			$sql = $wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_name} WHERE created_at >= %s AND created_at <= %s AND source = 'global-injector' AND success = 1 AND rule_id = %d",
				$date_from,
				$date_to,
				$rule_id
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT COUNT(*) FROM {$table_name} WHERE created_at >= %s AND created_at <= %s AND success = 1",
				$date_from,
				$date_to
			);
		}

		$count = $wpdb->get_var( $sql );

		return (int) $count;
	}

	/**
	 * Get rule-specific stats (for main-rule-list activity column).
	 *
	 * @since 5.3.0
	 * @param int    $rule_id   Rule ID.
	 * @param string $date_from Start date (Y-m-d H:i:s).
	 * @param string $date_to   End date (Y-m-d H:i:s).
	 * @return array Stats with total_copies, copies_24h, copies_7d, copies_30d, change_percent_24h.
	 */
	public function get_rule_stats( $rule_id, $date_from, $date_to ) {
		$total_copies = $this->get_total_copies( $date_from, $date_to, $rule_id );

		$now          = Helper::mysql_now();
		$date_24h_ago = Helper::mysql_date_ago( '-24 hours' );
		$date_7d_ago  = Helper::mysql_date_ago( '-7 days' );
		$date_30d_ago = Helper::mysql_date_ago( '-30 days' );

		$copies_24h = $this->get_total_copies( $date_24h_ago, $now, $rule_id );
		$copies_7d  = $this->get_total_copies( $date_7d_ago, $now, $rule_id );
		$copies_30d = $this->get_total_copies( $date_30d_ago, $now, $rule_id );

		$date_48h_ago       = Helper::mysql_date_ago( '-48 hours' );
		$previous_24h_count = $this->get_total_copies( $date_48h_ago, $date_24h_ago, $rule_id );
		$change_percent_24h = $this->calculate_change_percent( $copies_24h, $previous_24h_count );

		return [
			'rule_id'            => $rule_id,
			'total_copies'       => $total_copies,
			'copies_24h'         => $copies_24h,
			'copies_7d'          => $copies_7d,
			'copies_30d'         => $copies_30d,
			'change_percent_24h' => $change_percent_24h,
		];
	}

	/**
	 * Calculate percentage change between two counts.
	 *
	 * @since 5.3.0
	 * @param int $current  Current period count.
	 * @param int $previous Previous period count.
	 * @return float
	 */
	public function calculate_change_percent( $current, $previous ) {
		if ( 0 === (int) $previous ) {
			return $current > 0 ? 100.0 : 0.0;
		}
		return round( ( ( (int) $current - (int) $previous ) / (int) $previous ) * 100, 1 );
	}

	/**
	 * Get activity trends (timeline data).
	 *
	 * @since 5.3.0
	 * @param string $date_from Start date (Y-m-d H:i:s).
	 * @param string $date_to   End date (Y-m-d H:i:s).
	 * @param string $group_by  Group by 'hour' or 'day'.
	 * @return array Array of { date, count } objects.
	 */
	public function get_trends( $date_from, $date_to, $group_by = 'hour' ) {
		global $wpdb;

		$table_name = $this->get_table_name();

		$date_format = 'hour' === $group_by ? '%Y-%m-%d %H:00:00' : '%Y-%m-%d 00:00:00';

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT 
					DATE_FORMAT(created_at, %s) as date,
					COUNT(*) as count
				FROM {$table_name}
				WHERE created_at >= %s 
					AND created_at <= %s 
					AND source = 'global-injector'
					AND success = 1
				GROUP BY DATE_FORMAT(created_at, %s)
				ORDER BY date ASC",
				$date_format,
				$date_from,
				$date_to,
				$date_format
			),
			ARRAY_A
		);

		return $results ?? [];
	}

	/**
	 * Get top rules by copy count.
	 *
	 * @since 5.3.0
	 * @param int    $limit     Number of rules to return.
	 * @param string $date_from Start date (Y-m-d H:i:s).
	 * @param string $date_to   End date (Y-m-d H:i:s).
	 * @return array Array of { rule_id, count } objects.
	 */
	public function get_top_rules( $limit = 10, $date_from = null, $date_to = null ) {
		global $wpdb;

		$table_name = $this->get_table_name();
		$limit      = absint( $limit );
		$base_where = "source = 'global-injector' AND success = 1 AND rule_id IS NOT NULL";

		if ( null !== $date_from && null !== $date_to ) {
			$sql = $wpdb->prepare(
				"SELECT rule_id, COUNT(*) as count FROM {$table_name} WHERE {$base_where} AND created_at >= %s AND created_at <= %s GROUP BY rule_id ORDER BY count DESC LIMIT %d",
				$date_from,
				$date_to,
				$limit
			);
		} elseif ( null !== $date_from ) {
			$sql = $wpdb->prepare(
				"SELECT rule_id, COUNT(*) as count FROM {$table_name} WHERE {$base_where} AND created_at >= %s GROUP BY rule_id ORDER BY count DESC LIMIT %d",
				$date_from,
				$limit
			);
		} elseif ( null !== $date_to ) {
			$sql = $wpdb->prepare(
				"SELECT rule_id, COUNT(*) as count FROM {$table_name} WHERE {$base_where} AND created_at <= %s GROUP BY rule_id ORDER BY count DESC LIMIT %d",
				$date_to,
				$limit
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT rule_id, COUNT(*) as count FROM {$table_name} WHERE {$base_where} GROUP BY rule_id ORDER BY count DESC LIMIT %d",
				$limit
			);
		}

		$results = $wpdb->get_results( $sql, ARRAY_A );

		return $results ?? [];
	}

	/**
	 * Get active rules count (rules with at least one copy event).
	 *
	 * @since 5.3.0
	 * @param string $date_from Start date (Y-m-d H:i:s).
	 * @param string $date_to   End date (Y-m-d H:i:s).
	 * @return int Count of unique rules.
	 */
	public function get_active_rules_count( $date_from, $date_to ) {
		global $wpdb;

		$table_name = $this->get_table_name();

		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(DISTINCT rule_id) 
				FROM {$table_name}
				WHERE created_at >= %s 
					AND created_at <= %s 
					AND source = 'global-injector'
					AND success = 1",
				$date_from,
				$date_to
			)
		);

		return (int) $count;
	}
}
