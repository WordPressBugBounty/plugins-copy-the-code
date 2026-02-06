<?php
/**
 * Block script dependencies.
 *
 * @since 5.0.0
 * @package    Copy the Code
 */

$block_json = file_get_contents( __DIR__ . '/../block.json' ); // phpcs:ignore
$block_json = json_decode( $block_json, true );

return [
	'dependencies' => [
		'wp-blocks',
		'wp-block-editor',
		'wp-core-data',
		'wp-element',
		'wp-components',
		'wp-data',
		'wp-i18n',
		'lodash',
	],
	'version'      => $block_json['version'],
];
