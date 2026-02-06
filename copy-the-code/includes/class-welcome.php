<?php
/**
 * Welcome Notice
 *
 * Handles the welcome/onboarding experience for new installs and upgrades.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC;

/**
 * Welcome Class
 *
 * Manages welcome notices and onboarding for new users.
 *
 * @since 5.0.0
 */
class Welcome {

	/**
	 * Instance
	 *
	 * @var Welcome|null
	 */
	private static $instance = null;

	/**
	 * Option key for tracking welcome notice dismissal.
	 *
	 * @var string
	 */
	const NOTICE_DISMISSED_OPTION = 'ctc_welcome_notice_dismissed';

	/**
	 * Option key for tracking first install.
	 *
	 * @var string
	 */
	const FIRST_INSTALL_OPTION = 'ctc_first_install_version';

	/**
	 * Get instance.
	 *
	 * @return Welcome
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
		// Track first install.
		$this->maybe_set_first_install();

		// Admin hooks.
		add_action( 'admin_notices', [ $this, 'render_welcome_notice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_notice_scripts' ] );
		add_action( 'wp_ajax_ctc_dismiss_welcome_notice', [ $this, 'ajax_dismiss_notice' ] );

		// Hook into updater for upgrade notices.
		add_action( 'ctc/updater/after', [ $this, 'on_version_update' ], 10, 2 );
	}

	/**
	 * Set first install version if not set.
	 *
	 * @since 5.0.0
	 */
	private function maybe_set_first_install() {
		if ( ! get_option( self::FIRST_INSTALL_OPTION ) ) {
			update_option( self::FIRST_INSTALL_OPTION, CTC_VER );
		}
	}

	/**
	 * Check if this is a fresh install (no previous version).
	 *
	 * @return bool
	 */
	public function is_fresh_install() {
		$saved_version = get_option( Updater::VERSION_OPTION, '' );
		return empty( $saved_version ) || '0.0.0' === $saved_version;
	}

	/**
	 * Check if user upgraded from a version before 5.0.0.
	 *
	 * @return bool
	 */
	public function is_upgrade_to_5() {
		$first_install = get_option( self::FIRST_INSTALL_OPTION, '' );
		return ! empty( $first_install ) && version_compare( $first_install, '5.0.0', '<' );
	}

	/**
	 * Check if welcome notice should be shown.
	 *
	 * @return bool
	 */
	public function should_show_notice() {
		// Don't show if dismissed.
		if ( get_option( self::NOTICE_DISMISSED_OPTION ) ) {
			return false;
		}

		// Don't show on the Global Injector page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && 'ctc-global-injector' === $_GET['page'] ) {
			return false;
		}

		// Only show to users who can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the welcome message based on install type.
	 *
	 * @return array{title: string, message: string, type: string}
	 */
	private function get_welcome_content() {
		if ( $this->is_fresh_install() ) {
			return [
				'title'   => __( 'Thanks for installing Copy Anything to Clipboard!', 'ctc' ),
				'message' => __( 'Add copy-to-clipboard buttons to any element on your site in seconds. The new Global Injector makes it easier than ever — no coding required.', 'ctc' ),
				'type'    => 'fresh',
			];
		}

		// Upgrade message.
		return [
			'title'   => __( 'Welcome to Copy Anything to Clipboard v5.0.0!', 'ctc' ),
			'message' => __( 'This major update introduces the powerful Global Injector with visual style presets, display conditions, and live preview. Your existing settings have been preserved.', 'ctc' ),
			'type'    => 'upgrade',
		];
	}

	/**
	 * Render the welcome admin notice.
	 *
	 * @since 5.0.0
	 */
	public function render_welcome_notice() {
		if ( ! $this->should_show_notice() ) {
			return;
		}

		$content             = $this->get_welcome_content();
		$global_injector_url = admin_url( 'admin.php?page=ctc-global-injector' );
		$docs_url            = 'https://docs.clipboard.agency/getting-started';

		?>
		<div class="notice notice-info is-dismissible ctc-welcome-notice" id="ctc-welcome-notice">
			<div class="ctc-welcome-notice-content">
				<div class="ctc-welcome-notice-icon">
					<span class="dashicons dashicons-clipboard"></span>
				</div>
				<div class="ctc-welcome-notice-text">
					<p class="ctc-welcome-notice-title">
						<strong><?php echo esc_html( $content['title'] ); ?></strong>
						<?php if ( 'fresh' === $content['type'] ) : ?>
							<span class="ctc-welcome-emoji">🎉</span>
						<?php else : ?>
							<span class="ctc-welcome-emoji">🚀</span>
						<?php endif; ?>
					</p>
					<p class="ctc-welcome-notice-message"><?php echo esc_html( $content['message'] ); ?></p>
					<p class="ctc-welcome-notice-actions">
						<a href="<?php echo esc_url( $global_injector_url ); ?>" class="button button-primary">
							<?php esc_html_e( 'Get Started', 'ctc' ); ?>
						</a>
						<a href="<?php echo esc_url( $docs_url ); ?>" class="button button-secondary" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'View Documentation', 'ctc' ); ?>
							<span class="dashicons dashicons-external" style="font-size: 14px; line-height: 1.8;"></span>
						</a>
						<?php if ( 'upgrade' === $content['type'] ) : ?>
							<a href="https://clipboard.agency/changelog/" class="ctc-welcome-link" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( "See what's new", 'ctc' ); ?> →
							</a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue notice scripts and styles.
	 *
	 * @since 5.0.0
	 */
	public function enqueue_notice_scripts() {
		if ( ! $this->should_show_notice() ) {
			return;
		}

		// Inline styles for the notice.
		$styles = '
			#ctc-welcome-notice {
				padding: 12px 12px 12px 0;
				border-left-color: #2271b1;
			}
			#ctc-welcome-notice .ctc-welcome-notice-content {
				display: flex;
				align-items: flex-start;
				gap: 12px;
			}
			#ctc-welcome-notice .ctc-welcome-notice-icon {
				flex-shrink: 0;
				width: 40px;
				height: 40px;
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				border-radius: 8px;
				display: flex;
				align-items: center;
				justify-content: center;
				margin-left: 12px;
			}
			#ctc-welcome-notice .ctc-welcome-notice-icon .dashicons {
				color: #fff;
				font-size: 20px;
				width: 20px;
				height: 20px;
			}
			#ctc-welcome-notice .ctc-welcome-notice-text {
				flex: 1;
			}
			#ctc-welcome-notice .ctc-welcome-notice-title {
				margin: 0 0 4px 0;
				font-size: 14px;
			}
			#ctc-welcome-notice .ctc-welcome-emoji {
				margin-left: 4px;
			}
			#ctc-welcome-notice .ctc-welcome-notice-message {
				margin: 0 0 12px 0;
				color: #50575e;
			}
			#ctc-welcome-notice .ctc-welcome-notice-actions {
				display: flex;
				align-items: center;
				gap: 8px;
				flex-wrap: wrap;
				margin: 0;
			}
			#ctc-welcome-notice .ctc-welcome-notice-actions .button-primary {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				border-color: #667eea;
			}
			#ctc-welcome-notice .ctc-welcome-notice-actions .button-primary:hover {
				background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
				border-color: #5a6fd6;
			}
			#ctc-welcome-notice .ctc-welcome-link {
				color: #2271b1;
				text-decoration: none;
				font-weight: 500;
				margin-left: 8px;
			}
			#ctc-welcome-notice .ctc-welcome-link:hover {
				color: #135e96;
			}
		';

		wp_add_inline_style( 'wp-admin', $styles );

		// Inline script for handling dismissal.
		$script = "
			jQuery(document).ready(function($) {
				$('#ctc-welcome-notice').on('click', '.notice-dismiss', function() {
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'ctc_dismiss_welcome_notice',
							nonce: '" . wp_create_nonce( 'ctc_dismiss_welcome_notice' ) . "'
						}
					});
				});
			});
		";

		wp_add_inline_script( 'jquery', $script );
	}

	/**
	 * AJAX handler for dismissing the welcome notice.
	 *
	 * @since 5.0.0
	 */
	public function ajax_dismiss_notice() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ctc_dismiss_welcome_notice' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		// Check permissions.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}

		// Mark as dismissed.
		update_option( self::NOTICE_DISMISSED_OPTION, true );

		wp_send_json_success();
	}

	/**
	 * Handle version updates - reset notice for major upgrades.
	 *
	 * @since 5.0.0
	 *
	 * @param string $from_version Previous version.
	 * @param string $to_version   New version.
	 */
	public function on_version_update( $from_version, $to_version ) {
		// Show welcome notice again for major version upgrades.
		$from_major = explode( '.', $from_version )[0];
		$to_major   = explode( '.', $to_version )[0];

		if ( $from_major !== $to_major ) {
			delete_option( self::NOTICE_DISMISSED_OPTION );
		}
	}

	/**
	 * Reset welcome notice (useful for testing).
	 *
	 * @since 5.0.0
	 */
	public static function reset_notice() {
		delete_option( self::NOTICE_DISMISSED_OPTION );
	}
}
