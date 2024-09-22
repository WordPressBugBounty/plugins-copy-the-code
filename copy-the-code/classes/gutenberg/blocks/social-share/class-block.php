<?php // phpcs:ignore
/**
 * Copy Button Block
 *
 * @package Copy the Code
 * @since 4.0.0
 */

namespace CopyTheCode\Gutenberg\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Social Share class.
 */
class Social_Share {

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
		$label      = $attributes['label'] ?? esc_html__( 'Share on:', 'copy-the-code' );
		$show_label = $attributes['showLabel'] ?? '1';
		$facebook   = $attributes['facebook'] ?? '1';
		$twitter    = $attributes['twitter'] ?? '1';
		$whatsapp   = $attributes['whatsapp'] ?? '1';

		// Post content.
		$post_id      = $block->context['postId'] ?? null;
		$link         = $post_id ? get_permalink( $post_id ) : null;
		$post_content = $post_id ? get_post_field( 'post_content', $post_id ) : null;
		if ( ! $post_content ) {
			$post_content = $post_id ? get_the_title( $post_id ) : null;
		}
		if ( $post_content ) {
			$post_content = wp_strip_all_tags( $post_content );
		}
		$wrapper_attributes = get_block_wrapper_attributes();

		ob_start();
		?>
		<div <?php echo $wrapper_attributes; // phpcs:ignore ?>>
			<?php if ( $show_label ) : ?>
				<span class='ctc-social-share__label'><?php echo esc_html( $label ); ?></span>
			<?php endif; ?>
			<?php if ( $whatsapp ) : ?>
				<a class="ctc-social-share__whatsapp" href='https://api.whatsapp.com/send?text=<?php echo esc_attr( $post_content ); ?>%0A<?php echo esc_url( $link ); ?>' target='_blank'>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 241.19">
						<path fill="#25d366" d="M205,35.05A118.61,118.61,0,0,0,120.46,0C54.6,0,1,53.61,1,119.51a119.5,119.5,0,0,0,16,59.74L0,241.19l63.36-16.63a119.43,119.43,0,0,0,57.08,14.57h0A119.54,119.54,0,0,0,205,35.07v0ZM120.5,219A99.18,99.18,0,0,1,69.91,205.1l-3.64-2.17-37.6,9.85,10-36.65-2.35-3.76A99.37,99.37,0,0,1,190.79,49.27,99.43,99.43,0,0,1,120.49,219ZM175,144.54c-3-1.51-17.67-8.71-20.39-9.71s-4.72-1.51-6.75,1.51-7.72,9.71-9.46,11.72-3.49,2.27-6.45.76-12.63-4.66-24-14.84A91.1,91.1,0,0,1,91.25,113.3c-1.75-3-.19-4.61,1.33-6.07s3-3.48,4.47-5.23a19.65,19.65,0,0,0,3-5,5.51,5.51,0,0,0-.24-5.23C99,90.27,93,75.57,90.6,69.58s-4.89-5-6.73-5.14-3.73-.09-5.7-.09a11,11,0,0,0-8,3.73C67.48,71.05,59.75,78.3,59.75,93s10.69,28.88,12.19,30.9S93,156.07,123,169c7.12,3.06,12.68,4.9,17,6.32a41.18,41.18,0,0,0,18.8,1.17c5.74-.84,17.66-7.21,20.17-14.18s2.5-13,1.75-14.19-2.69-2.06-5.7-3.59l0,0Z" />
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( $facebook ) : ?>
				<a class="ctc-social-share__facebook" href='https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( $link ); ?>&quote=<?php echo esc_attr( $post_content ); ?>' target='_blank'>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
						<path fill="#425993" fill-rule="evenodd" d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h7v-7h-2v-3h2V8.5A3.5 3.5 0 0115.5 5H18v3h-2a1 1 0 00-1 1v2h3v3h-3v7h4a2 2 0 002-2V5a2 2 0 00-2-2z" clip-rule="evenodd" />
					</svg>
				</a>
			<?php endif; ?>
			<?php if ( $twitter ) : ?>
				<a class="ctc-social-share__twitter" href='https://twitter.com/intent/tweet?text=<?php echo esc_attr( $post_content ); ?>&url=<?php echo esc_url( $link ); ?>' target='_blank'>
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
						<path fill="#4BA8F5" d="M22.92 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.83 4.5 17.72 4 16.46 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98-3.56-.18-6.73-1.89-8.84-4.48-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 01-1.93.07 4.28 4.28 0 004 2.98 8.521 8.521 0 01-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.9 20.29 6.16 21 8.58 21c7.88 0 12.21-6.54 12.21-12.21 0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
					</svg>
				</a>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

}

