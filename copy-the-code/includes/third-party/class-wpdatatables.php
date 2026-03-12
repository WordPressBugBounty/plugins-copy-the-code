<?php
/**
 * WPDataTables integration.
 *
 * Registers the wpDataTables shortcode as a container so that [copy] / [copy_inline]
 * inside table cells trigger script enqueue and work with cached/dynamic table output.
 *
 * @package CTC
 * @since 5.5.1
 */

namespace CTC\ThirdParty;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPDataTables
 */
class WPDataTables {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'ctc/shortcode/container_shortcodes', [ $this, 'add_container_shortcodes' ], 10, 1 );
	}

	/**
	 * Add wpDataTables shortcode to the list of container shortcodes.
	 *
	 * @param string[] $container_shortcodes List of shortcode names that may output [copy] in their content.
	 * @return string[] Modified list.
	 */
	public function add_container_shortcodes( $container_shortcodes ) {
		$container_shortcodes[] = 'wpdatatable';
		return $container_shortcodes;
	}
}
