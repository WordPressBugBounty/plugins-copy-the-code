<?php // @codingStandardsIgnoreLine
/**
 * Copy Anything to Clipboard Plugin.
 *
 * @package      Copy Anything to Clipboard
 * @copyright    Copyright (C) 2026, Clipboard Agency - dev@clipboard.agency
 * @link         https://clipboard.agency
 * @since 5.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Copy Anything to Clipboard
 * Plugin URI:        https://clipboard.agency/
 * Description:       Add copy-to-clipboard buttons to any element with the powerful <strong>Global Injector</strong> — no coding required. Features visual style presets (Button, Icon, Cover), display conditions, and live preview. Perfect for <strong>Code Snippets</strong>, <strong>Coupons</strong>, <strong>WooCommerce SKUs</strong>, and more.
 * Version:           5.1.0
 * Author:            Clipboard Agency
 * Author URI:        https://clipboard.agency/
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       ctc
 * Domain Path:       /languages
 *
  */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Freemius conflict resolution.
 *
 * If another instance of this plugin (free or pro) has already initialized Freemius,
 * just register this file with Freemius and exit. This prevents class redeclaration
 * errors when both free and pro versions are active.
 */
if ( function_exists( 'ctc_fs' ) ) {
	$fs = ctc_fs();
	if ( $fs ) {
		// Register this file with Freemius.
		// Use true for premium, false for free. Freemius handles this via @fs_premium_only.
		$fs->set_basename( false, __FILE__ );
	}
	return;
}

// Define plugin constants.
define( 'CTC_VER', '5.0.0' );
define( 'CTC_FILE', __FILE__ );
define( 'CTC_BASE', plugin_basename( CTC_FILE ) );
define( 'CTC_DIR', plugin_dir_path( CTC_FILE ) );
define( 'CTC_URI', plugins_url( '/', CTC_FILE ) );
define( 'CTC_GUTENBERG_DIR', CTC_DIR . 'includes/gutenberg/' );
define( 'CTC_GUTENBERG_URI', CTC_URI . 'includes/gutenberg/' );
define( 'CTC_ELEMENTOR_DIR', CTC_DIR . 'includes/elementor/' );
define( 'CTC_ELEMENTOR_URI', CTC_URI . 'includes/elementor/' );

// Initialize Freemius.
require_once CTC_DIR . 'includes/freemius.php';

// Include the main plugin class.
require_once CTC_DIR . 'includes/class-core.php';

// Start the plugin.
ctc();
