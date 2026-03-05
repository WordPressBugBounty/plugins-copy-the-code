<?php
/**
 * Helper Class
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC;

/**
 * Helper Class
 *
 * @since 5.0.0
 */
class Helper {

	/**
	 * Check if the plugin is Pro version
	 *
	 * Uses Freemius or can be filtered by Pro add-on plugin.
	 *
	 * @since 5.0.0
	 * @return bool
	 */
	public static function is_pro() {
		/**
		 * Filter to enable Pro features.
		 *
		 * Pro add-on plugin can hook into this to enable Pro features.
		 *
		 * @since 5.0.0
		 * @param bool $is_pro Whether Pro is active. Default false.
		 */
		return apply_filters( 'ctc/is_pro', function_exists( 'ctc_fs' ) && ctc_fs()->is__premium_only() );
	}

	/**
	 * Minify CSS: strip comments and collapse whitespace.
	 *
	 * @since 5.0.2
	 * @param string $css Raw CSS.
	 * @return string Minified CSS.
	 */
	public static function minify_css( $css ) {
		$css = (string) preg_replace( '/\/\*[\s\S]*?\*\//', '', $css );
		$css = (string) preg_replace( '/\s+/', ' ', $css );
		return trim( $css );
	}

	/**
	 * Current date/time in MySQL format (UTC).
	 *
	 * @since 5.3.0
	 * @return string
	 */
	public static function mysql_now() {
		return current_time( 'mysql', true );
	}

	/**
	 * Date relative to now (or a given timestamp) in MySQL format (UTC).
	 *
	 * @since 5.3.0
	 * @param string   $relative       Relative time e.g. '-30 days', '-24 hours'.
	 * @param int|null $from_timestamp Optional base timestamp (UTC); default current timestamp.
	 * @return string MySQL datetime (Y-m-d H:i:s).
	 */
	public static function mysql_date_ago( $relative, $from_timestamp = null ) {
		if ( null === $from_timestamp ) {
			$from_timestamp = time();
		}
		return gmdate( 'Y-m-d H:i:s', strtotime( $relative, $from_timestamp ) );
	}

	/**
	 * Epoch date for "all time" analytics (very old start date).
	 *
	 * @since 5.3.0
	 * @return string MySQL datetime.
	 */
	public static function mysql_epoch() {
		return '1970-01-01 00:00:00';
	}

	/**
	 * Map analytics period key to number of days.
	 *
	 * @since 5.3.0
	 * @param string $period One of '24h', '7d', '30d', '60d', '90d', '180d'.
	 * @return int Number of days; default 7.
	 */
	public static function analytics_period_to_days( $period ) {
		$map = [
			'24h'  => 1,
			'7d'   => 7,
			'30d'  => 30,
			'60d'  => 60,
			'90d'  => 90,
			'180d' => 180,
		];
		return isset( $map[ $period ] ) ? $map[ $period ] : 7;
	}

	/**
	 * Get analytics date range (from/to) in MySQL datetime format.
	 *
	 * Use when custom from/to are provided, or derive from period.
	 *
	 * @since 5.3.0
	 * @param string $period Period key ('24h', '7d', '30d', '90d').
	 * @param string|null $from Optional custom start date (Y-m-d or MySQL datetime).
	 * @param string|null $to   Optional custom end date (Y-m-d or MySQL datetime).
	 * @return array{from: string, to: string} Keys 'from' and 'to' as MySQL datetime strings.
	 */
	public static function analytics_date_range( $period, $from = null, $to = null ) {
		if ( $from && $to ) {
			return [
				'from' => sanitize_text_field( $from ),
				'to'   => sanitize_text_field( $to ),
			];
		}
		$days       = self::analytics_period_to_days( $period );
		$start_date = self::mysql_date_ago( "-{$days} days" );
		$end_date   = self::mysql_now();
		return [
			'from' => $start_date,
			'to'   => $end_date,
		];
	}
}
