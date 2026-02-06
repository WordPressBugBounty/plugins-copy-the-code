<?php
/**
 * Gutenberg Blocks
 *
 * @package Copy the Code
 * @since 3.1.0
 */

namespace CopyTheCode\Gutenberg;

use CopyTheCode\Gutenberg\Blocks\Term_Title;
use CopyTheCode\Gutenberg\Blocks\Copy_Button;
use CopyTheCode\Gutenberg\Blocks\Copy_Icon;
use CopyTheCode\Gutenberg\Blocks\Social_Share;

/**
 * Blocks
 *
 * @since 3.1.0
 */
class Blocks {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );

		require_once COPY_THE_CODE_DIR . 'classes/gutenberg/blocks/term-title/class-block.php';
		require_once COPY_THE_CODE_DIR . 'classes/gutenberg/blocks/copy-button/class-block.php';
		require_once COPY_THE_CODE_DIR . 'classes/gutenberg/blocks/copy-icon/class-block.php';
		require_once COPY_THE_CODE_DIR . 'classes/gutenberg/blocks/social-share/class-block.php';

		new Term_Title();
		new Copy_Button();
		new Copy_Icon();
		new Social_Share();
	}

	/**
	 * Register category
	 *
	 * @param array  $categories Block categories.
	 * @param object $post       Post object.
	 * @return array
	 */
	public function register_category( $categories, $post ) {
		$categories[] = [
			'slug'  => 'ctc-blocks',
			'title' => esc_html__( 'Copy Anything to Clipboard', 'copy-the-code' ),
			'icon'  => 'wordpress',
		];
		return $categories;
	}

}

new Blocks();
