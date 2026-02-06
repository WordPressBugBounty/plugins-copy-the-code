<?php
/**
 * Plugin Updater
 *
 * Handles version updates and migrations.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC;

/**
 * Updater Class
 *
 * Manages plugin version updates and data migrations.
 */
class Updater {

	/**
	 * Instance
	 *
	 * @var Updater|null
	 */
	private static $instance = null;

	/**
	 * Option key for stored version.
	 *
	 * @var string
	 */
	const VERSION_OPTION = 'ctc_version';

	/**
	 * Get instance.
	 *
	 * @return Updater
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
		add_action( 'admin_init', [ $this, 'check_version' ] );
	}

	/**
	 * Check version and run updates if needed.
	 */
	public function check_version() {
		$saved_version   = get_option( self::VERSION_OPTION, '0.0.0' );
		$current_version = CTC_VER;

		// If versions match, no update needed.
		if ( version_compare( $saved_version, $current_version, '=' ) ) {
			return;
		}

		/**
		 * Fires before plugin update runs.
		 *
		 * @since 5.0.0
		 *
		 * @param string $saved_version   Previously saved version.
		 * @param string $current_version Current plugin version.
		 */
		do_action( 'ctc/updater/before', $saved_version, $current_version );

		// Run version-specific updates.
		$this->run_updates( $saved_version, $current_version );

		// Update stored version.
		update_option( self::VERSION_OPTION, $current_version );

		/**
		 * Fires after plugin update completes.
		 *
		 * @since 5.0.0
		 *
		 * @param string $saved_version   Previously saved version.
		 * @param string $current_version Current plugin version.
		 */
		do_action( 'ctc/updater/after', $saved_version, $current_version );
	}

	/**
	 * Run version-specific updates.
	 *
	 * @param string $from_version Version updating from.
	 * @param string $to_version   Version updating to.
	 */
	private function run_updates( $from_version, $to_version ) {
		// Update to 5.0.0.
		if ( version_compare( $from_version, '5.0.0', '<' ) ) {
			$this->update_to_5_0_0();
		}

		// Add future version updates here.
		// Example:
		// if ( version_compare( $from_version, '5.1.0', '<' ) ) {
		//     $this->update_to_5_1_0();
		// }
	}

	/**
	 * Update to version 5.0.0.
	 *
	 * Migrates from legacy settings to new Global Injector.
	 */
	private function update_to_5_0_0() {
		/**
		 * Fires during 5.0.0 update.
		 *
		 * Use this hook to run custom migration logic.
		 *
		 * @since 5.0.0
		 */
		do_action( 'ctc/updater/5.0.0' );

		// Migration logic for 5.0.0 can be added here.
		// For example: migrate legacy CPT settings to new format.
	}
}
