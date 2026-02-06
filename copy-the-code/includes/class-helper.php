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
}
