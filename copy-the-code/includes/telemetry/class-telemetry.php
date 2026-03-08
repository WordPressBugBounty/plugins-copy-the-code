<?php
/**
 * Telemetry
 *
 * Sends anonymized usage data to Signals API when user has opted in.
 * Privacy-first: no PII, hashed install IDs, opt-in only.
 *
 * @package CTC
 * @since 5.2.0
 */

namespace CTC;

use CTC\Global_Injector;
use CTC\Helper;
use CTC\Analytics\Database;

/**
 * Telemetry Class
 *
 * Manages opt-in, payload building, and sending telemetry to signals.bmapi.workers.dev.
 *
 * @since 5.2.0
 */
class Telemetry {

	/**
	 * Option key for telemetry opt-in.
	 *
	 * @var string
	 */
	const OPT_IN_OPTION = 'ctc_telemetry_opt_in';

	/**
	 * Option key for install ID.
	 *
	 * @var string
	 */
	const INSTALL_ID_OPTION = 'ctc_install_id';

	/**
	 * Option key for first seen timestamp.
	 *
	 * @var string
	 */
	const FIRST_SEEN_OPTION = 'ctc_telemetry_first_seen';

	/**
	 * Option key for last sent timestamp.
	 *
	 * @var string
	 */
	const LAST_SENT_OPTION = 'ctc_telemetry_last_sent';

	/**
	 * Cron hook for weekly telemetry send.
	 *
	 * @var string
	 */
	const CRON_HOOK = 'ctc_telemetry_weekly_send';

	/**
	 * Minimum seconds between sends (throttle).
	 */
	const SEND_INTERVAL = 7 * DAY_IN_SECONDS;

	/**
	 * Signals API endpoint.
	 *
	 * @var string
	 */
	const ENDPOINT = 'https://signals.bmapi.workers.dev/v1/telemetry';

	/**
	 * Length of truncated install_id hash (sha256 produces 64 chars).
	 *
	 * @var int
	 */
	const INSTALL_ID_HASH_LENGTH = 32;

	/**
	 * Instance
	 *
	 * @var Telemetry|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Telemetry
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
		self::maybe_set_first_seen();

		add_action( 'wp_ajax_ctc_update_telemetry_opt_in', [ $this, 'ajax_update_opt_in' ] );
		add_action( self::CRON_HOOK, [ __CLASS__, 'maybe_send' ] );
		add_action( 'init', [ __CLASS__, 'maybe_schedule_cron' ], 20 );
	}

	/**
	 * Set first seen timestamp if not set.
	 */
	private static function maybe_set_first_seen() {
		if ( get_option( self::FIRST_SEEN_OPTION ) ) {
			return;
		}
		update_option( self::FIRST_SEEN_OPTION, time() );
	}

	/**
	 * Generate a stable install ID hash from the current domain.
	 *
	 * Same domain (e.g. example.com, app.example.com) always yields the same id,
	 * so uninstall + reactivate on the same site is not counted as a new install.
	 *
	 * @return string
	 */
	private static function get_install_id_hash() {
		$domain = self::get_domain();
		$salt   = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'ctc-telemetry-salt';
		$full   = hash( 'sha256', $domain . $salt );
		return substr( $full, 0, self::INSTALL_ID_HASH_LENGTH );
	}

	/**
	 * Generate UUID v4 (kept for any future use; install identity is now domain-based).
	 *
	 * @return string
	 */
	private static function generate_uuid() {
		if ( function_exists( 'wp_generate_uuid' ) ) {
			return wp_generate_uuid();
		}

		$data    = random_bytes( 16 );
		$data[6] = chr( ord( $data[6] ) & 0x0f | 0x40 );
		$data[8] = chr( ord( $data[8] ) & 0x3f | 0x80 );

		return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
	}

	/**
	 * Check if user has opted in.
	 *
	 * Hybrid: Freemius opt-in (connect + tracking) OR our own option (welcome/dashboard).
	 *
	 * @return bool
	 */
	public static function get_opt_in() {
		$from_freemius   = function_exists( 'ctc_fs' )
			&& ctc_fs()->is_registered()
			&& ctc_fs()->is_tracking_allowed();
		$from_our_option = (bool) get_option( self::OPT_IN_OPTION, false );

		return $from_freemius || $from_our_option;
	}

	/**
	 * Set opt-in status.
	 *
	 * @param bool $opt_in Whether to opt in.
	 * @return bool
	 */
	public static function set_opt_in( $opt_in ) {
		return update_option( self::OPT_IN_OPTION, $opt_in ? '1' : '0' );
	}

	/**
	 * Build telemetry payload.
	 *
	 * @return array
	 */
	private static function build_payload() {
		$hashed_id = self::get_install_id_hash();

		$rules   = Global_Injector::get()->get_admin_rules();
		$copy_as = [];
		foreach ( $rules as $rule ) {
			if ( ! empty( $rule['copy_as'] ) && ! in_array( $rule['copy_as'], $copy_as, true ) ) {
				$copy_as[] = sanitize_key( $rule['copy_as'] );
			}
		}

		$domain = self::get_domain();
		$env    = [
			'wp_version'           => get_bloginfo( 'version' ),
			'php_version'          => PHP_VERSION,
			'theme_name'           => wp_get_theme()->get( 'Name' ),
			'theme_version'        => wp_get_theme()->get( 'Version' ),
			'active_plugins_count' => count( (array) get_option( 'active_plugins', [] ) ),
			'multisite'            => is_multisite(),
			'domain'               => $domain,
			'active_themes'        => self::get_active_themes(),
			'inactive_themes'      => self::get_inactive_themes(),
			'wp'                   => self::get_wp_info(),
			'server'               => self::get_server_info(),
			'active_plugins'       => self::get_active_plugins_data(),
			'inactive_plugins'     => self::get_inactive_plugins_data(),
			'users'                => count_users(),
		];

		$wc_data = self::get_woocommerce_data();
		if ( ! empty( $wc_data ) ) {
			$env['wc_products'] = $wc_data['products'] ?? [];
			$env['wc_orders']   = $wc_data['orders'] ?? [];
		}

		$payload = [
			'install_id'     => $hashed_id,
			'opt_in'         => self::get_opt_in(),
			'ts'             => gmdate( 'c' ),
			'plugin_version' => defined( 'CTC_VER' ) ? CTC_VER : '',
			'environment'    => $env,
			'features'       => [
				'rules_count'   => count( $rules ),
				'is_pro'        => Helper::is_pro(),
				'copy_as_types' => $copy_as,
			],
			'locale'         => get_locale(),
		];

		// Add copy-event aggregates from local analytics (shortcode + global-injector) when available.
		$copies = self::get_copies_summary();
		if ( ! empty( $copies ) ) {
			$payload['copies'] = $copies;
		}

		return apply_filters( 'ctc/telemetry/payload', $payload );
	}

	/**
	 * Get domain slug from home URL.
	 *
	 * @return string
	 */
	private static function get_domain() {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( empty( $host ) ) {
			return '';
		}
		$host = preg_replace( '/^www\./', '', $host );
		$slug = sanitize_key( str_replace( '.', '-', $host ) );
		return apply_filters( 'ctc/telemetry/domain', $slug );
	}

	/**
	 * Get active theme(s) data.
	 *
	 * Includes current theme and parent theme when using a child theme.
	 *
	 * @return array<string, array{name: string, version: string, child_theme: bool, wc_support: bool, block_theme: bool}>
	 */
	private static function get_active_themes() {
		$theme           = wp_get_theme();
		$themes          = [];
		$slug            = $theme->get_stylesheet();
		$themes[ $slug ] = [
			'name'        => $theme->get( 'Name' ),
			'version'     => $theme->get( 'Version' ),
			'child_theme' => (bool) $theme->parent(),
			'wc_support'  => current_theme_supports( 'woocommerce' ),
			'block_theme' => function_exists( 'wp_is_block_theme' ) ? wp_is_block_theme() : false,
		];
		$parent          = $theme->parent();
		if ( $parent ) {
			$parent_slug            = $parent->get_stylesheet();
			$themes[ $parent_slug ] = [
				'name'        => $parent->get( 'Name' ),
				'version'     => $parent->get( 'Version' ),
				'child_theme' => false,
				'wc_support'  => false,
				'block_theme' => false,
			];
		}
		return $themes;
	}

	/**
	 * Get inactive themes data.
	 *
	 * @return array<string, array{name: string, version: string, child_theme: bool, wc_support: bool, block_theme: bool}>
	 */
	private static function get_inactive_themes() {
		if ( ! function_exists( 'wp_get_themes' ) ) {
			return [];
		}
		$active_slug   = wp_get_theme()->get_stylesheet();
		$parent        = wp_get_theme()->parent();
		$active_parent = $parent ? $parent->get_stylesheet() : '';
		$themes        = [];
		foreach ( wp_get_themes() as $slug => $theme ) {
			if ( $slug === $active_slug || $slug === $active_parent ) {
				continue;
			}
			$themes[ $slug ] = [
				'name'        => $theme->get( 'Name' ),
				'version'     => $theme->get( 'Version' ),
				'child_theme' => (bool) $theme->parent(),
				'wc_support'  => false,
				'block_theme' => false,
			];
		}
		return $themes;
	}

	/**
	 * Get WordPress environment info.
	 *
	 * @return array{memory_limit?: int, debug_mode?: bool, locale?: string, version?: string, multisite?: bool, env_type?: string}
	 */
	private static function get_wp_info() {
		$memory = ini_get( 'memory_limit' );
		$bytes  = $memory ? wp_convert_hr_to_bytes( $memory ) : 0;
		return [
			'memory_limit' => $bytes,
			'debug_mode'   => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'locale'       => get_locale(),
			'version'      => get_bloginfo( 'version' ),
			'multisite'    => is_multisite(),
			'env_type'     => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		];
	}

	/**
	 * Get server info.
	 *
	 * @return array<string, mixed>
	 */
	private static function get_server_info() {
		global $wpdb;
		return [
			'software'             => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : null,
			'php_version'          => PHP_VERSION,
			'php_post_max_size'    => ini_get( 'post_max_size' ) ?: null,
			'php_time_limit'       => (int) ini_get( 'max_execution_time' ),
			'php_max_input_vars'   => (int) ini_get( 'max_input_vars' ),
			'php_max_upload_size'  => function_exists( 'wp_max_upload_size' ) ? wp_max_upload_size() : wp_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) ?: '0' ),
			'php_default_timezone' => date_default_timezone_get(),
			'mysql_version'        => $wpdb && isset( $wpdb->db_version ) ? $wpdb->db_version() : null,
		];
	}

	/**
	 * Get active plugins with full data.
	 *
	 * @return list<array{slug: string, name: string, version: string, active: bool}>
	 */
	private static function get_active_plugins_data() {
		return self::get_plugins_data( true );
	}

	/**
	 * Get inactive plugins with full data.
	 *
	 * @return list<array{slug: string, name: string, version: string, active: bool}>
	 */
	private static function get_inactive_plugins_data() {
		return self::get_plugins_data( false );
	}

	/**
	 * Get plugins data (active or inactive).
	 *
	 * @param bool $active True for active, false for inactive.
	 * @return list<array{slug: string, name: string, version: string, active: bool, author?: string, author_uri?: string, plugin_uri?: string}>
	 */
	private static function get_plugins_data( $active ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', [] );
		$active_plugins = is_array( $active_plugins ) ? $active_plugins : [];
		$result         = [];
		foreach ( $all_plugins as $file => $data ) {
			$sitewide  = is_multisite() ? array_keys( (array) get_site_option( 'active_sitewide_plugins', [] ) ) : [];
			$is_active = in_array( $file, $active_plugins, true ) || in_array( $file, $sitewide, true );
			if ( $is_active !== $active ) {
				continue;
			}
			$dir  = dirname( $file );
			$slug = '.' === $dir ? sanitize_key( pathinfo( $file, PATHINFO_FILENAME ) ) : sanitize_key( $dir );
			$item = [
				'slug'    => $slug,
				'name'    => isset( $data['Name'] ) ? $data['Name'] : '',
				'version' => isset( $data['Version'] ) ? $data['Version'] : '',
				'active'  => $active,
			];
			if ( ! empty( $data['Author'] ) ) {
				$item['author'] = $data['Author'];
			}
			if ( ! empty( $data['AuthorURI'] ) ) {
				$item['author_uri'] = $data['AuthorURI'];
			}
			if ( ! empty( $data['PluginURI'] ) ) {
				$item['plugin_uri'] = $data['PluginURI'];
			}
			$result[] = $item;
		}
		return $result;
	}

	/**
	 * Get copy-event summary from local analytics (ctc_analytics) for telemetry.
	 * Includes total_all, total_7d, total_30d from shortcode + global-injector events.
	 * Returns empty array if analytics table is missing or unavailable.
	 *
	 * @return array{total_all?: int, total_7d?: int, total_30d?: int}
	 */
	private static function get_copies_summary() {
		try {
			$db  = Database::get();
			$now = Helper::mysql_now();
			$ep  = Helper::mysql_epoch();
			$d7  = Helper::mysql_date_ago( '-7 days' );
			$d30 = Helper::mysql_date_ago( '-30 days' );

			$total_all = $db->get_total_copies( $ep, $now, null );
			$total_7d  = $db->get_total_copies( $d7, $now, null );
			$total_30d = $db->get_total_copies( $d30, $now, null );
			$by_source = $db->get_total_copies_by_source( $ep, $now );

			$out = [
				'total_all' => (int) $total_all,
				'total_7d'  => (int) $total_7d,
				'total_30d' => (int) $total_30d,
			];
			if ( ! empty( $by_source ) ) {
				$out['by_source'] = $by_source;
			}
			return $out;
		} catch ( \Throwable $e ) {
			return [];
		}
	}

	/**
	 * Get WooCommerce tracking data if available.
	 *
	 * @return array{products?: array, orders?: array}
	 */
	private static function get_woocommerce_data() {
		if ( ! apply_filters( 'ctc/telemetry/include_woocommerce', true ) ) {
			return [];
		}
		if ( ! class_exists( 'WC_Tracker' ) ) {
			return [];
		}
		$data = \WC_Tracker::get_tracking_data();
		return [
			'products' => $data['products'] ?? [],
			'orders'   => $data['orders'] ?? [],
		];
	}

	/**
	 * Send telemetry (only when opted in).
	 */
	public static function maybe_send() {
		if ( ! self::get_opt_in() ) {
			return;
		}

		$last_sent = (int) get_option( self::LAST_SENT_OPTION, 0 );
		if ( $last_sent && ( time() - $last_sent ) < self::SEND_INTERVAL ) {
			return;
		}

		$payload = wp_json_encode( self::build_payload() );
		if ( ! $payload ) {
			return;
		}

		$response = wp_safe_remote_post(
			self::ENDPOINT,
			[
				'body'    => $payload,
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'timeout' => 30,
			]
		);

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) >= 200 && wp_remote_retrieve_response_code( $response ) < 300 ) {
			update_option( self::LAST_SENT_OPTION, time() );
		}
	}

	/**
	 * AJAX handler for updating opt-in (dashboard and welcome notice).
	 */
	public function ajax_update_opt_in() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ctc_telemetry_opt_in' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}

		$opt_in = isset( $_POST['opt_in'] ) && '1' === $_POST['opt_in'];

		self::set_opt_in( $opt_in );

		if ( $opt_in ) {
			self::maybe_send();
			self::schedule_cron();
		} else {
			self::unschedule_cron();
		}

		wp_send_json_success( [ 'opt_in' => $opt_in ] );
	}

	/**
	 * Schedule weekly telemetry cron (opt-in only).
	 */
	public static function schedule_cron() {
		if ( wp_next_scheduled( self::CRON_HOOK ) ) {
			return;
		}

		// First run in 50 seconds (WooCommerce-style), then weekly.
		wp_schedule_event( time() + 50, 'weekly', self::CRON_HOOK );
	}

	/**
	 * Unschedule telemetry cron.
	 */
	public static function unschedule_cron() {
		wp_clear_scheduled_hook( self::CRON_HOOK );
	}

	/**
	 * Ensure cron is scheduled when opted in (e.g. after plugin update).
	 */
	public static function maybe_schedule_cron() {
		if ( ! self::get_opt_in() ) {
			return;
		}
		if ( wp_next_scheduled( self::CRON_HOOK ) ) {
			return;
		}
		self::schedule_cron();
	}

	/**
	 * Delete options (for uninstall).
	 */
	public static function cleanup() {
		delete_option( self::OPT_IN_OPTION );
		delete_option( self::FIRST_SEEN_OPTION );
		delete_option( self::LAST_SENT_OPTION );
	}
}
