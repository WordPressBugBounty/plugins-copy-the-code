<?php // phpcs:ignore
/**
 * Term Title Block
 *
 * @package Copy the Code
 * @since 4.0.0
 */

namespace CopyTheCode\Gutenberg\Blocks;

use CopyTheCode\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Term Title class.
 */
class Term_Title {

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
			[]
		);
	}

}

