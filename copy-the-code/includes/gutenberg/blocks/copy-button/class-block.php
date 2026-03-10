<?php
/**
 * Copy Button Block
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Gutenberg\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Copy Button class.
 */
class Copy_Button {

	/**
	 * Instance
	 *
	 * @var Copy_Button|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Copy_Button
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
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_frontend_assets' ] );
	}

	/**
	 * Initialize the block.
	 */
	public function init() {
		register_block_type_from_metadata(
			__DIR__,
			[
				'render_callback' => [ $this, 'render' ],
			]
		);
	}

	/**
	 * Enqueue frontend assets (copy engine + analytics) when Copy Button block is present.
	 */
	public function enqueue_frontend_assets() {
		if ( is_admin() ) {
			return;
		}

		$post = get_post();
		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		if ( ! has_block( 'ctc/copy-button', $post ) ) {
			return;
		}

		wp_enqueue_script( 'ctc-lib-core', CTC_URI . 'assets/frontend/js/lib/ctc.js', [ 'jquery' ], CTC_VER, true );
		wp_enqueue_script( 'ctc-core', CTC_URI . 'includes/assets/js/core.js', [ 'ctc-lib-core' ], CTC_VER, true );

		wp_localize_script(
			'ctc-core',
			'ctcBlockAnalytics',
			[
				'eventsUrl' => rest_url( 'ctc/v1/analytics/events' ),
				'postId'    => $post->ID,
				'postType'  => $post->post_type,
			]
		);
	}

	/**
	 * Render the block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block content.
	 * @param WP_Block $block      Block instance.
	 * @return string
	 */
	public function render( $attributes = [], $content = '', $block = null ) {
		$post_id      = $block->context['postId'] ?? null;
		$post_content = $post_id ? get_post_field( 'post_content', $post_id ) : null;
		if ( $post_content ) {
			$post_content = wp_strip_all_tags( $post_content );
			$post_content = preg_replace( '/<br\s*\/?>/', "\n", $post_content );
		}

		// Block attributes.
		$button_text        = $attributes['buttonText'] ?? esc_html__( 'Copy to Clipboard', 'ctc' );
		$content            = ! empty( $attributes['content'] ) ? $attributes['content'] : $post_content;
		$button_text_copied = $attributes['buttonTextCopied'] ?? esc_html__( 'Copied!', 'ctc' );
		$wrapper_attributes = get_block_wrapper_attributes(
			[
				'class'            => 'ctc-block-copy ctc-copy-button',
				'data-copied-text' => $button_text_copied,
			]
		);

		ob_start();
		?>
		<div class="ctc-block ctc-copy-button-wrap" data-ctc-analytics="1" data-ctc-source="gutenberg-block" data-ctc-block-type="ctc/copy-button">
			<button type="button" <?php echo $wrapper_attributes; // phpcs:ignore ?> data-copied="<?php echo esc_attr( $button_text_copied ); ?>">
				<span class="ctc-button-text"><?php echo esc_html( $button_text ); ?></span>
			</button>
			<textarea class="ctc-copy-content ctc-copy-button-textarea" readonly><?php echo wp_kses_post( $content ); ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}
}
