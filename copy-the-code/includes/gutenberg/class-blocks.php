<?php
/**
 * Gutenberg Blocks
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Gutenberg;

/**
 * Blocks
 *
 * @since 5.0.0
 */
class Blocks {

	/**
	 * Instance
	 *
	 * @var Blocks|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Blocks
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );

		// Initialize blocks.
		$this->init_blocks();
	}

	/**
	 * Get block definitions.
	 *
	 * @return array
	 */
	private function get_block_definitions() {
		return [
			'term-title'   => [
				'name'        => __( 'Term Title', 'ctc' ),
				'description' => __( 'Display the current term title.', 'ctc' ),
				'class'       => Blocks\Term_Title::class,
			],
			'copy-button'  => [
				'name'        => __( 'Copy Button', 'ctc' ),
				'description' => __( 'Add a copy button with custom content.', 'ctc' ),
				'class'       => Blocks\Copy_Button::class,
			],
			'copy-icon'    => [
				'name'        => __( 'Copy Icon', 'ctc' ),
				'description' => __( 'Add a copy icon with custom content.', 'ctc' ),
				'class'       => Blocks\Copy_Icon::class,
			],
			'icon'         => [
				'name'        => __( 'Icon', 'ctc' ),
				'description' => __( 'Add a standalone copy icon.', 'ctc' ),
				'class'       => Blocks\Icon::class,
			],
			'social-share' => [
				'name'        => __( 'Social Share', 'ctc' ),
				'description' => __( 'Add social share buttons.', 'ctc' ),
				'class'       => Blocks\Social_Share::class,
			],
		];
	}

	/**
	 * Initialize blocks.
	 */
	private function init_blocks() {
		$definitions = $this->get_block_definitions();

		foreach ( $definitions as $id => $block ) {
			$class = $block['class'];

			if ( class_exists( $class ) && method_exists( $class, 'get' ) ) {
				$class::get();
			}
		}
	}

	/**
	 * Register category
	 *
	 * @param array                   $categories Block categories.
	 * @param \WP_Block_Editor_Context $context    Block editor context.
	 * @return array
	 */
	public function register_category( $categories, $context ) {
		$categories[] = [
			'slug'  => 'ctc-blocks',
			'title' => esc_html__( 'Copy Anything to Clipboard', 'ctc' ),
			'icon'  => 'wordpress',
		];
		return $categories;
	}

	/**
	 * Get blocks for external use (e.g., admin UI).
	 *
	 * @return array
	 */
	public static function get_blocks() {
		$instance    = self::get();
		$definitions = $instance->get_block_definitions();
		$blocks      = [];

		foreach ( $definitions as $id => $block ) {
			$blocks[] = [
				'id'          => $id,
				'name'        => $block['name'],
				'description' => $block['description'],
			];
		}

		return $blocks;
	}
}
