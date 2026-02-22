<?php
/**
 * Main Rule List (Global Injector)
 *
 * @package CTC
 * @since 5.1.0
 */

namespace CTC\Global_Injector;

use CTC\Global_Injector;
use CTC\Helper;
use CTC\Analytics\Database as Analytics_Database;

/**
 * Main Rule List class.
 *
 * Replaces the default post list table with a custom UI for Pro users.
 *
 * @since 5.1.0
 */
class Main_Rule_List {

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
	 * @return object initialized object of class.
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Bootstrap the Main Rule List UI.
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
			'ctc-global-injector',
			CTC_URI . 'assets/admin/css/global-injector.css',
			[],
			CTC_VER
		);

		wp_enqueue_style(
			'ctc-main-rule-list',
			CTC_URI . 'assets/admin/css/main-rule-list.css',
			[],
			CTC_VER
		);

		wp_enqueue_script(
			'ctc-main-rule-list',
			CTC_URI . 'assets/admin/js/main-rule-list.js',
			[
				'wp-element',
				'wp-i18n',
				'wp-api-fetch',
				'lodash',
				'jquery',
			],
			CTC_VER,
			true
		);

		$rules = Global_Injector::get()->get_admin_rules();

		$rule_stats = [];
		if ( ! empty( $rules ) && class_exists( 'CTC\Analytics\Database' ) ) {
			$db        = Analytics_Database::get();
			$now       = Helper::mysql_now();
			$date_from = Helper::mysql_date_ago( '-30 days' );
			foreach ( $rules as $rule ) {
				$id = isset( $rule['id'] ) ? (int) $rule['id'] : 0;
				if ( $id > 0 ) {
					$rule_stats[ $id ] = $db->get_rule_stats( $id, $date_from, $now );
				}
			}
		}

		$localize_data = [
			'isPro'      => Helper::is_pro(),
			'version'    => CTC_VER,
			'rules'      => $rules,
			'rule_stats' => $rule_stats,
			'apiUrl'     => rest_url( 'ctc/v1/' ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'urls'       => [
				'addNew'       => admin_url( 'options-general.php?page=ctc-global-injector' ),
				'dashboard'    => admin_url( 'options-general.php?page=ctc' ),
				'editRuleBase' => admin_url( 'options-general.php?page=ctc-global-injector&rule=' ),
				'analytics'    => admin_url( 'options-general.php?page=ctc-analytics' ),
			],
		];

		/**
		 * Filter Main Rule List localize data.
		 *
		 * Pro can use this to add urls.analytics and ruleActivity data.
		 *
		 * @since 5.3.0
		 * @param array $data Localized data array.
		 */
		$localize_data = apply_filters( 'ctc/main_rule_list/localize_data', $localize_data );

		wp_localize_script(
			'ctc-main-rule-list',
			'CTCMainRuleList',
			$localize_data
		);
	}
}
