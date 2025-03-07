<?php
/**
 * Update
 *
 * @package Copy the Code
 * @since 2.0.0
 */

if ( ! class_exists( 'Copy_The_Code_Update' ) ) :

	/**
	 * Copy The Code Update
	 *
	 * @since 2.0.0
	 */
	class Copy_The_Code_Update {

		/**
		 * Instance
		 *
		 * @var object Class object.
		 * @access private
		 * @since 2.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.0.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			add_action( 'admin_init', __CLASS__ . '::init' );
		}

		/**
		 * Init
		 *
		 * @since 2.0.0
		 * @return void
		 */
		public static function init() {

			do_action( 'copy_the_code_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'copy-the-code-auto-version', false );

			// Update auto saved version number.
			if ( ! $saved_version ) {
				update_option( 'copy-the-code-auto-version', COPY_THE_CODE_VER );
			}

			// If equals then return.
			if ( version_compare( $saved_version, COPY_THE_CODE_VER, '=' ) ) {
				return;
			}

			// Update auto saved version number.
			update_option( 'copy-the-code-auto-version', COPY_THE_CODE_VER );

			do_action( 'copy_the_code_update_after' );
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Copy_The_Code_Update::get_instance();

endif;
