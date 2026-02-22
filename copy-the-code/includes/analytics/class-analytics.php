<?php
/**
 * Analytics Admin Page
 *
 * Handles Analytics dashboard admin page.
 *
 * @package CTC
 * @since 5.3.0
 */

namespace CTC\Analytics;

use CTC\Helper;

/**
 * Analytics Class
 *
 * @since 5.3.0
 */
class Analytics {

	/**
	 * Instance
	 *
	 * @var Analytics|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Analytics
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
		// Page rendering is handled by Base::register_menus().
	}

	/**
	 * Render Analytics page.
	 *
	 * @since 5.3.0
	 * @return void
	 */
	public function render() {
		add_filter( 'admin_footer_text', '__return_empty_string' );
		add_filter( 'update_footer', '__return_empty_string', 11 );

		$this->enqueue_assets();
		$this->localize_data();
		?>
		<div class="wrap ctc-admin-root ctc-analytics-page" id="ctc-analytics-root"></div>
		<?php
	}

	/**
	 * Enqueue assets (scripts and styles).
	 *
	 * @since 5.3.0
	 * @return void
	 */
	private function enqueue_assets() {
		wp_enqueue_style(
			'ctc-analytics',
			CTC_URI . 'assets/admin/css/analytics.css',
			[],
			CTC_VER
		);

		wp_enqueue_script(
			'ctc-analytics',
			CTC_URI . 'assets/admin/js/analytics.js',
			[
				'wp-element',
				'wp-data',
				'wp-components',
				'wp-i18n',
				'wp-api-fetch',
				'lodash',
				'jquery',
			],
			CTC_VER,
			true
		);
	}

	/**
	 * Localize script data.
	 *
	 * @since 5.3.0
	 * @return void
	 */
	private function localize_data() {
		$localize_data = [
			'apiUrl'  => rest_url( 'ctc/v1/analytics/' ),
			'nonce'   => wp_create_nonce( 'ctc_analytics' ),
			'isPro'   => Helper::is_pro(),
			'version' => CTC_VER,
			'urls'    => [
				'dashboard'    => admin_url( 'options-general.php?page=ctc' ),
				'editRuleBase' => admin_url( 'options-general.php?page=ctc-global-injector&rule=' ),
			],
		];

		/**
		 * Filter analytics localize data.
		 *
		 * @since 5.3.0
		 * @param array $localize_data Localized data array.
		 */
		$localize_data = apply_filters( 'ctc/analytics/localize_data', $localize_data );

		wp_localize_script(
			'ctc-analytics',
			'ctcAnalytics',
			$localize_data
		);
	}
}
