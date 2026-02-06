<?php // phpcs:ignore
/**
 * Copy Button Block
 *
 * @package Copy the Code
 * @since 4.0.0
 */

namespace CopyTheCode\Gutenberg\Blocks;

use CopyTheCode\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Copy Button class.
 */
class Copy_Button {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
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
	 * Render the block.
	 *
	 * @param array $attributes Block attributes.
	 */
	public function render( $attributes = [] ) {
		$post_id      = $block->context['postId'] ?? null;
		$post_content = $post_id ? get_post_field( 'post_content', $post_id ) : null;
		if ( $post_content ) {
			$post_content = wp_strip_all_tags( $post_content );
			$post_content = preg_replace( '/<br\s*\/?>/', "\n", $post_content );
		}

		// Block attributes.
		$button_text        = $attributes['buttonText'] ?? esc_html__( 'Copy to Clipboard', 'copy-the-code' );
		$content            = ! empty( $attributes['content'] ) ? $attributes['content'] : $post_content;
		$button_text_copied = $attributes['buttonTextCopied'] ?? esc_html__( 'Copied!', 'copy-the-code' );
		$wrapper_attributes = get_block_wrapper_attributes(
			[
				'data-copied-text' => $button_text_copied,
			]
		);

		ob_start();
		?>
		<div class="ctc-copy-button-wrap">
			<button <?php echo $wrapper_attributes; // phpcs:ignore ?>>
				<?php echo esc_html( $button_text ); ?>
			</button>
			<textarea class="ctc-copy-button-textarea" readonly><?php echo wp_kses_post( $content ); ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}

}
