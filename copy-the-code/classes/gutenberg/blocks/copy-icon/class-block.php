<?php // phpcs:ignore
/**
 * Copy Icon Block
 *
 * @package Copy the Code
 * @since 4.0.0
 */

namespace CopyTheCode\Gutenberg\Blocks;

use CopyTheCode\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Copy Icon class.
 */
class Copy_Icon {

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
		$content            = ! empty( $attributes['content'] ) ? $attributes['content'] : $post_content;
		$wrapper_attributes = get_block_wrapper_attributes();

		ob_start();
		?>
		<div <?php echo $wrapper_attributes; // phpcs:ignore ?>>
			<span copy-as-raw="yes" class="ctc-block-copy ctc-block-copy-icon" role="button" aria-label="Copied">
				<?php echo Helpers::get_svg_copy_icon(); // phpcs:ignore ?>
				<?php echo Helpers::get_svg_checked_icon(); // phpcs:ignore ?>
			</span>
			<textarea class="ctc-copy-icon-textarea" readonly><?php echo wp_kses_post( $content ); // phpcs:ignore ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}

}

