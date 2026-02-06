<?php
/**
 * Term Title Block
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Gutenberg\Blocks;

defined( 'ABSPATH' ) || exit;

/**
 * Term Title class.
 */
class Term_Title {

	/**
	 * Instance
	 *
	 * @var Term_Title|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Term_Title
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
	}

	/**
	 * Initialize the block.
	 */
	public function init() {
		register_block_type_from_metadata( __DIR__ );
	}
}
