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
				'lodash',
				'jquery',
			],
			CTC_VER,
			true
		);

		$rules = Global_Injector::get()->get_admin_rules();

		wp_localize_script(
			'ctc-main-rule-list',
			'CTCMainRuleList',
			[
				'isPro'   => Helper::is_pro(),
				'version' => CTC_VER,
				'rules'   => $rules,
				'apiUrl'  => rest_url( 'ctc/v1/' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'urls'    => [
					'addNew'   => admin_url( 'options-general.php?page=ctc-global-injector' ),
					'dashboard' => admin_url( 'options-general.php?page=ctc' ),
					'editRuleBase' => admin_url( 'options-general.php?page=ctc-global-injector&rule=' ),
				],
			]
		);
	}
}
