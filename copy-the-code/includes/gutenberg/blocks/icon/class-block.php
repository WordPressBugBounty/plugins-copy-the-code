<?php
/**
 * Icon Block
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Gutenberg\Blocks;

use CTC\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Icon class.
 */
class Icon {

	/**
	 * Instance
	 *
	 * @var Icon|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Icon
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
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_frontend_assets' ] );
	}

	/**
	 * Enqueue editor-only assets (block registration JS).
	 *
	 * Styles for this block are registered via block.json (style/editorStyle).
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'ctc-gb-icon',
			CTC_GUTENBERG_URI . 'blocks/icon/js/icon.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-server-side-render' ],
			CTC_VER,
			true
		);
	}

	/**
	 * Enqueue frontend assets (styles and copy functionality).
	 * Only enqueues when the Icon block is present in the current post/content.
	 */
	public function enqueue_frontend_assets() {
		if ( is_admin() ) {
			return;
		}
		if ( ! $this->current_content_has_icon_block() ) {
			return;
		}

		wp_enqueue_script( 'ctc-lib-core', CTC_URI . 'assets/frontend/js/lib/ctc.js', [ 'jquery' ], CTC_VER, true );
		wp_enqueue_script( 'ctc-core', CTC_URI . 'includes/assets/js/core.js', [ 'ctc-lib-core' ], CTC_VER, true );

		$post = get_post();
		wp_localize_script(
			'ctc-core',
			'ctcBlockAnalytics',
			[
				'eventsUrl' => rest_url( 'ctc/v1/analytics/events' ),
				'postId'    => $post instanceof \WP_Post ? $post->ID : null,
				'postType'  => $post instanceof \WP_Post ? $post->post_type : null,
			]
		);
	}

	/**
	 * Whether the current queried post (or global post) contains the Icon block.
	 *
	 * @return bool
	 */
	private function current_content_has_icon_block() {
		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return false;
		}
		return has_block( 'copy-the-code/icon', $post );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		register_block_type(
			CTC_GUTENBERG_DIR . 'blocks/icon/block.json',
			[
				'render_callback' => [ $this, 'render' ],
			]
		);
	}

	/**
	 * Render.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 * @return string
	 */
	public function render( $attributes, $content ) {
		$alignment    = isset( $attributes['alignment'] ) ? $attributes['alignment'] : 'left';
		$copy_content = isset( $attributes['content'] ) ? $attributes['content'] : '';
		ob_start();
		?>
		<div class="ctc-block ctc-copy-icon" data-ctc-analytics="1" data-ctc-source="gutenberg-block" data-ctc-block-type="copy-the-code/icon" style="text-align: <?php echo esc_attr( $alignment ); ?>">
			<span copy-as-raw="yes" class="ctc-block-copy ctc-block-copy-icon" role="button" aria-label="Copied">
				<?php echo Helpers::get_svg_copy_icon(); // phpcs:ignore ?>
				<?php echo Helpers::get_svg_checked_icon(); // phpcs:ignore ?>
			</span>
			<textarea class="ctc-copy-content" style="display: none;"><?php echo wp_kses_post( apply_shortcodes( $copy_content ) ); ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}
}
