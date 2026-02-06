<?php
/**
 * Base
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC;

/**
 * Base
 *
 * @since 5.0.0
 */
class Base {

	/**
	 * Instance
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 5.0.0
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
	 * @since 5.0.0
	 */
	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'add_menus' ] );
	}

	/**
	 * Add menus
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function add_menus() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'register_menus' ] );
	}

	/**
	 * Register menus
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function register_menus() {
		add_menu_page(
			__( 'Copy to Clipboard', 'ctc' ),
			__( 'Copy to Clipboard', 'ctc' ),
			'manage_options',
			'ctc',
			[ $this, 'render_dashboard' ],
			'dashicons-clipboard',
			80
		);

		add_submenu_page(
			'ctc',
			__( 'Global Injector', 'ctc' ),
			__( 'Global Injector', 'ctc' ),
			'manage_options',
			'ctc-global-injector',
			[ $this, 'render_global_injector' ]
		);
	}

	/**
	 * Render dashboard
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function render_dashboard() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Copy to Clipboard', 'ctc' ); ?></h1>
			<p><?php esc_html_e( 'Welcome to Copy to Clipboard plugin.', 'ctc' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render global injector
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function render_global_injector() {
		?>
		<div id="ctc-global-injector-root"></div>
		<?php
	}
}
