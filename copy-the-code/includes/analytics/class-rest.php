<?php
/**
 * Analytics REST API
 *
 * Registers analytics REST endpoints (events, stats, trends, summary).
 *
 * @package CTC
 * @since 5.3.0
 */

namespace CTC\Analytics;

use CTC\Analytics\Database;
use CTC\Helper;

/**
 * Analytics REST API
 *
 * @since 5.3.0
 */
class Rest {

	/**
	 * Instance
	 *
	 * @since 5.3.0
	 *
	 * @access private
	 * @var Rest|null
	 */
	private static $instance;

	/**
	 * REST namespace
	 *
	 * @var string
	 */
	private $namespace = 'ctc/v1';

	/**
	 * Get instance.
	 *
	 * @since 5.3.0
	 *
	 * @return Rest
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 5.3.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register analytics REST routes
	 *
	 * @since 5.3.0
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/analytics/events',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'track_event' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			$this->namespace,
			'/analytics/stats',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_analytics_stats' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/analytics/rules/(?P<id>\d+)/stats',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_rule_stats' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/analytics/trends',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_analytics_trends' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/analytics/summary',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_analytics_summary' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);
	}

	/**
	 * Check permissions for analytics endpoints (admin).
	 *
	 * @since 5.3.0
	 * @return bool
	 */
	public function check_permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Track copy event.
	 * Writes to ctc_analytics (single table for free and Pro). Accepts full payload from frontend.
	 *
	 * @since 5.3.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function track_event( $request ) {
		// Basic abuse protection: per-IP rate limiting.
		if ( $this->is_rate_limited( $request ) ) {
			return new \WP_Error(
				'ctc_analytics_rate_limited',
				__( 'Too many analytics events from this client. Please slow down.', 'ctc' ),
				[ 'status' => 429 ]
			);
		}

		$data = $request->get_json_params();

		$metadata       = isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : [];
		$error_reason   = isset( $data['error_reason'] ) ? sanitize_text_field( $data['error_reason'] ) : null;
		$failure_reason = isset( $data['failure_reason'] ) ? sanitize_text_field( $data['failure_reason'] ) : $error_reason;
		$source         = isset( $data['source'] ) ? sanitize_text_field( $data['source'] ) : ( isset( $metadata['source'] ) ? sanitize_text_field( $metadata['source'] ) : 'global-injector' );
		$post_id        = isset( $data['post_id'] ) ? absint( $data['post_id'] ) : ( isset( $metadata['post_id'] ) ? absint( $metadata['post_id'] ) : null );
		$post_type      = isset( $data['post_type'] ) ? sanitize_text_field( $data['post_type'] ) : ( isset( $metadata['post_type'] ) ? sanitize_text_field( $metadata['post_type'] ) : null );
		$page_url       = isset( $data['page_url'] ) ? esc_url_raw( $data['page_url'] ) : ( isset( $metadata['page_url'] ) ? esc_url_raw( $metadata['page_url'] ) : null );

		$event_data = [
			'rule_id'        => isset( $data['rule_id'] ) ? absint( $data['rule_id'] ) : null,
			'source'         => $source ?: 'global-injector',
			'success'        => isset( $data['success'] ) ? (bool) $data['success'] : true,
			'failure_reason' => $failure_reason,
			'post_id'        => $post_id,
			'post_type'      => $post_type,
			'page_url'       => $page_url,
			'device'         => isset( $data['device'] ) ? sanitize_text_field( $data['device'] ) : null,
			'browser'        => isset( $data['browser'] ) ? sanitize_text_field( $data['browser'] ) : null,
		];

		// Validate payload schema and field lengths.
		$validation_error = $this->validate_event_payload( $event_data );
		if ( $validation_error ) {
			return $validation_error;
		}

		$db       = Database::get();
		$event_id = $db->insert_event( $event_data );

		if ( false === $event_id ) {
			return new \WP_Error(
				'event_insert_failed',
				__( 'Failed to track event.', 'ctc' ),
				[ 'status' => 500 ]
			);
		}

		return rest_ensure_response(
			[
				'success'  => true,
				'event_id' => $event_id,
			]
		);
	}

	/**
	 * Determine if the current request should be rate limited.
	 *
	 * Uses a simple per-IP counter over a short time window stored in a transient.
	 *
	 * @since 5.4.0
	 * @param \WP_REST_Request $request Request object.
	 * @return bool True when rate limit exceeded.
	 */
	private function is_rate_limited( $request ) {
		$ip = $this->get_client_ip();

		// If we cannot determine the IP, skip rate limiting rather than blocking.
		if ( ! $ip ) {
			return false;
		}

		$key          = 'ctc_analytics_rate_' . md5( $ip );
		$window       = (int) apply_filters( 'ctc/analytics/rate_limit_window', 60 ); // seconds.
		$max_requests = (int) apply_filters( 'ctc/analytics/rate_limit_max_requests', 60 ); // events per window.

		$data = get_transient( $key );

		$now = time();

		if ( ! is_array( $data ) || ! isset( $data['count'], $data['expires_at'] ) || $data['expires_at'] <= $now ) {
			// New window.
			$data = [
				'count'      => 1,
				'expires_at' => $now + $window,
			];
			set_transient( $key, $data, $window );
			return false;
		}

		if ( $data['count'] >= $max_requests ) {
			return true;
		}

		++$data['count'];
		set_transient( $key, $data, $data['expires_at'] - $now );

		return false;
	}

	/**
	 * Get best-effort client IP for rate limiting.
	 *
	 * @since 5.4.0
	 * @return string Client IP or empty string on failure.
	 */
	private function get_client_ip() {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// If multiple IPs, take the first one.
			$parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			$ip    = trim( $parts[0] );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		/**
		 * Filter the IP used for analytics rate limiting.
		 *
		 * @since 5.4.0
		 *
		 * @param string $ip Client IP address.
		 */
		$ip = apply_filters( 'ctc/analytics/client_ip', $ip );

		return $ip;
	}

	/**
	 * Validate analytics event payload.
	 *
	 * Ensures types and maximum lengths to protect the database schema
	 * and avoid obviously invalid data.
	 *
	 * @since 5.4.0
	 * @param array $event_data Normalized event data.
	 * @return \WP_Error|null
	 */
	private function validate_event_payload( array $event_data ) {
		// rule_id must be null or positive integer.
		if ( null !== $event_data['rule_id'] && ( ! is_int( $event_data['rule_id'] ) || $event_data['rule_id'] < 0 ) ) {
			return new \WP_Error(
				'ctc_analytics_invalid_rule_id',
				__( 'Invalid rule ID for analytics event.', 'ctc' ),
				[ 'status' => 400 ]
			);
		}

		// Source bounded length.
		if ( isset( $event_data['source'] ) && strlen( (string) $event_data['source'] ) > 64 ) {
			return new \WP_Error(
				'ctc_analytics_invalid_source',
				__( 'Invalid analytics event source.', 'ctc' ),
				[ 'status' => 400 ]
			);
		}

		// Failure reason bounded length.
		if ( isset( $event_data['failure_reason'] ) && strlen( (string) $event_data['failure_reason'] ) > 255 ) {
			return new \WP_Error(
				'ctc_analytics_invalid_failure_reason',
				__( 'Failure reason is too long for analytics event.', 'ctc' ),
				[ 'status' => 400 ]
			);
		}

		// Device / browser bounded length.
		foreach ( [ 'device', 'browser' ] as $field ) {
			if ( isset( $event_data[ $field ] ) && strlen( (string) $event_data[ $field ] ) > 100 ) {
				return new \WP_Error(
					'ctc_analytics_invalid_' . $field,
					__( 'Analytics event contains invalid user agent metadata.', 'ctc' ),
					[ 'status' => 400 ]
				);
			}
		}

		// Page URL bounded length.
		if ( isset( $event_data['page_url'] ) && strlen( (string) $event_data['page_url'] ) > 2048 ) {
			return new \WP_Error(
				'ctc_analytics_invalid_page_url',
				__( 'Analytics event URL is too long.', 'ctc' ),
				[ 'status' => 400 ]
			);
		}

		return null;
	}

	/**
	 * Get analytics summary (total_30d, total_all) from ctc_analytics.
	 *
	 * @since 5.3.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_analytics_summary( $request ) {
		$db         = Database::get();
		$now        = Helper::mysql_now();
		$date_30d   = Helper::mysql_date_ago( '-30 days' );
		$total_30d  = $db->get_total_copies( $date_30d, $now, null );
		$date_epoch = Helper::mysql_epoch();
		$total_all  = $db->get_total_copies( $date_epoch, $now, null );
		$response   = [
			'total_30d' => (int) $total_30d,
			'total_all' => (int) $total_all,
		];
		$response   = apply_filters( 'ctc/analytics/summary_response', $response, $request );
		return rest_ensure_response( $response );
	}

	/**
	 * Get analytics stats.
	 *
	 * @since 5.3.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_analytics_stats( $request ) {
		$date_from = $request->get_param( 'date_from' );
		$date_to   = $request->get_param( 'date_to' );
		$rule_id   = $request->get_param( 'rule_id' );

		if ( ! $date_from ) {
			$date_from = Helper::mysql_date_ago( '-30 days' );
		}
		if ( ! $date_to ) {
			$date_to = Helper::mysql_now();
		}

		$db = Database::get();

		$total_copies = $db->get_total_copies( $date_from, $date_to, $rule_id ? absint( $rule_id ) : null );
		$active_rules = $db->get_active_rules_count( $date_from, $date_to );
		$top_rules    = $db->get_top_rules( 1, $date_from, $date_to );
		$top_rule     = null;
		if ( ! empty( $top_rules ) ) {
			$top_rule_data = $top_rules[0];
			$top_rule_post = get_post( $top_rule_data['rule_id'] );
			if ( $top_rule_post ) {
				$top_rule = [
					'id'    => $top_rule_data['rule_id'],
					'name'  => $top_rule_post->post_title,
					'count' => (int) $top_rule_data['count'],
				];
			}
		}

		$now            = Helper::mysql_now();
		$ts             = time();
		$date_24h_ago   = Helper::mysql_date_ago( '-24 hours', $ts );
		$date_48h_ago   = Helper::mysql_date_ago( '-48 hours', $ts );
		$current_24h    = $db->get_total_copies( $date_24h_ago, $now, $rule_id ? absint( $rule_id ) : null );
		$previous_24h   = $db->get_total_copies( $date_48h_ago, $date_24h_ago, $rule_id ? absint( $rule_id ) : null );
		$change_percent = $db->calculate_change_percent( $current_24h, $previous_24h );

		return rest_ensure_response(
			[
				'success'        => true,
				'total_copies'   => $total_copies,
				'active_rules'   => $active_rules,
				'top_rule'       => $top_rule,
				'change_percent' => $change_percent,
			]
		);
	}

	/**
	 * Get rule-specific stats.
	 *
	 * @since 5.3.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_rule_stats( $request ) {
		$rule_id = absint( $request->get_param( 'id' ) );

		$post = get_post( $rule_id );
		if ( ! $post || 'copy-to-clipboard' !== $post->post_type ) {
			return new \WP_Error(
				'rule_not_found',
				__( 'Rule not found.', 'ctc' ),
				[ 'status' => 404 ]
			);
		}

		$date_from = $request->get_param( 'date_from' );
		$date_to   = $request->get_param( 'date_to' );

		if ( ! $date_from ) {
			$date_from = Helper::mysql_date_ago( '-30 days' );
		}
		if ( ! $date_to ) {
			$date_to = Helper::mysql_now();
		}

		$db    = Database::get();
		$stats = $db->get_rule_stats( $rule_id, $date_from, $date_to );

		return rest_ensure_response(
			[
				'success' => true,
				'stats'   => $stats,
			]
		);
	}

	/**
	 * Get analytics trends (timeline data).
	 *
	 * @since 5.3.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_analytics_trends( $request ) {
		$date_from = $request->get_param( 'date_from' );
		$date_to   = $request->get_param( 'date_to' );
		$group_by  = $request->get_param( 'group_by' ) ?: 'hour';

		if ( ! in_array( $group_by, [ 'hour', 'day' ], true ) ) {
			$group_by = 'hour';
		}

		if ( ! $date_from ) {
			$date_from = Helper::mysql_date_ago( '-24 hours' );
		}
		if ( ! $date_to ) {
			$date_to = Helper::mysql_now();
		}

		$db     = Database::get();
		$trends = $db->get_trends( $date_from, $date_to, $group_by );

		return rest_ensure_response(
			[
				'success' => true,
				'trends'  => $trends,
			]
		);
	}
}
