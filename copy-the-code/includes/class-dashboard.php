<?php
/**
 * Dashboard
 *
 * @package CTC
 * @since 5.1.0
 */

namespace CTC;

use CTC\Global_Injector;
use CTC\Helper;

/**
 * Dashboard class.
 *
 * Renders the Copy to Clipboard admin dashboard (page=ctc).
 *
 * @since 5.1.0
 */
class Dashboard {

	/**
	 * Instance
	 *
	 * @since 5.1.0
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 5.1.0
	 *
	 * @return object
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Bootstrap the Dashboard UI.
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function bootstrap() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->enqueue_assets();
	}

	/**
	 * Enqueue assets and localize data.
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function enqueue_assets() {
		wp_enqueue_style(
			'ctc-dashboard',
			CTC_URI . 'assets/admin/css/dashboard.css',
			[],
			CTC_VER
		);

		wp_enqueue_script(
			'ctc-dashboard',
			CTC_URI . 'assets/admin/js/dashboard.js',
			[
				'wp-element',
				'wp-i18n',
				'jquery',
			],
			CTC_VER,
			true
		);

		$rules = Global_Injector::get()->get_admin_rules();
		$active_count = array_reduce(
			$rules,
			function ( $n, $r ) {
				return $n + ( ! empty( $r['is_active'] ) ? 1 : 0 );
			},
			0
		);
		$paused_count = count( $rules ) - $active_count;
		$recent_rules = array_slice( array_values( $rules ), 0, 5 );

		wp_localize_script(
			'ctc-dashboard',
			'CTCDashboard',
			[
				'isPro'       => Helper::is_pro(),
				'rulesCount'  => count( $rules ),
				'activeCount' => $active_count,
				'pausedCount' => $paused_count,
				'version'     => CTC_VER,
				'recentRules' => $recent_rules,
				'urls'        => [
					'rules'            => admin_url( 'options-general.php?page=ctc-rules' ),
					'addNew'           => admin_url( 'options-general.php?page=ctc-global-injector' ),
					'docs'             => 'https://docs.clipboard.agency/',
					'changelog'        => 'https://docs.clipboard.agency/changelog',
					'gettingStarted'   => 'https://docs.clipboard.agency/getting-started',
					'globalInjector'   => 'https://docs.clipboard.agency/guides/global-injector',
					'visualStyles'     => 'https://docs.clipboard.agency/guides/visual-styles',
					'editRule'         => admin_url( 'options-general.php?page=ctc-global-injector&rule=' ),
				],
			]
		);
	}
}
