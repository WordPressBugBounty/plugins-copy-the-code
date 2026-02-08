<?php
/**
 * Base
 *
 * @package CTC
 * @since 5.1.0
 */

namespace CTC;

use CTC\Dashboard;
use CTC\Global_Injector;
use CTC\Global_Injector\Main_Rule_List;

/**
 * Base
 *
 * @since 5.1.0
 */
class Base {

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
	 * Constructor
	 *
	 * @since 5.1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menus' ] );
		add_action( 'load-settings_page_ctc', [ $this, 'load_dashboard' ] );
	}

	/**
	 * Load dashboard assets and body class before admin header (required for page=ctc).
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function load_dashboard() {
		Dashboard::get()->bootstrap();
	}

	/**
	 * Register menus (all under Settings).
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function register_menus() {
		add_submenu_page(
			'options-general.php',
			__( 'Copy to Clipboard', 'ctc' ),
			__( 'Copy to Clipboard', 'ctc' ),
			'manage_options',
			'ctc',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'options-general.php',
			__( 'Global Injector', 'ctc' ),
			'↳ ' . __( 'Global Injector', 'ctc' ),
			'manage_options',
			'ctc-rules',
			[ $this, 'render_rules_list' ]
		);

		add_submenu_page(
			'options-general.php',
			__( 'Add New Rule', 'ctc' ),
			'↳ ' . __( 'Add new', 'ctc' ),
			'manage_options',
			'ctc-global-injector',
			[ Global_Injector::get(), 'render' ]
		);

		/*
		add_submenu_page(
			'options-general.php',
			__( 'Copy to Clipboard Settings', 'ctc' ),
			'↳ ' . __( 'Settings', 'ctc' ),
			'manage_options',
			'ctc-settings',
			[ $this, 'render_settings_placeholder' ]
		);
		*/

		/*
		add_submenu_page(
			'options-general.php',
			__( 'Help & Docs', 'ctc' ),
			'↳ ' . __( 'Help & Docs', 'ctc' ),
			'manage_options',
			'ctc-help',
			[ $this, 'render_help_placeholder' ]
		);
		*/
	}

	/**
	 * Render dashboard (React app mounts here).
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function render_dashboard() {
		?>
		<div class="wrap ctc-admin-root ctc-dashboard-page" id="ctc-dashboard-root"></div>
		<?php
	}

	/**
	 * Render rules list page (Main Rule List UI mounts here).
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function render_rules_list() {
		Main_Rule_List::get()->bootstrap();
		?>
		<div class="wrap ctc-admin-root ctc-main-rule-list-page" id="ctc-main-rule-list-root"></div>
		<?php
	}

	/**
	 * Placeholder for future Settings page.
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function render_settings_placeholder() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Copy to Clipboard Settings', 'ctc' ); ?></h1>
			<p><?php esc_html_e( 'Settings will be available in a future release.', 'ctc' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Placeholder for future Help & Docs page.
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function render_help_placeholder() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Help & Docs', 'ctc' ); ?></h1>
			<p><?php esc_html_e( 'Documentation and help will be available here in a future release.', 'ctc' ); ?></p>
		</div>
		<?php
	}
}
