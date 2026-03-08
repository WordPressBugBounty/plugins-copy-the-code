<?php

/**
 * Freemius helper.
 *
 * @package CTC
 */
use CTC\Telemetry;
/**
 * Create a helper function for easy SDK access.
 *
 * @return object|null Freemius SDK instance, or null if not yet initialized.
 */
function ctc_fs() {
    global $ctc_fs;
    // Return existing instance if already initialized.
    if ( isset( $ctc_fs ) ) {
        return $ctc_fs;
    }
    // Don't initialize before plugins_loaded to avoid early translation loading (WP 6.7+).
    if ( !did_action( 'plugins_loaded' ) ) {
        return null;
    }
    // Include Freemius SDK from Composer vendor directory.
    $freemius_sdk_path = dirname( __DIR__ ) . '/vendor/freemius/wordpress-sdk/start.php';
    if ( !file_exists( $freemius_sdk_path ) ) {
        wp_die( 'Freemius SDK not found. Please run: composer install' );
    }
    require_once $freemius_sdk_path;
    $ctc_fs = fs_dynamic_init( [
        'id'               => '2780',
        'slug'             => 'ctc',
        'type'             => 'plugin',
        'public_key'       => 'pk_15a174f8c30f506a9a35ccbf0fa76',
        'is_premium'       => false,
        'premium_suffix'   => 'Premium',
        'has_addons'       => false,
        'has_paid_plans'   => true,
        'has_affiliation'  => 'selected',
        'menu'             => [
            'slug'           => 'ctc-global-injector',
            'override_exact' => true,
            'first-path'     => 'options-general.php?page=ctc-global-injector',
            'contact'        => true,
            'support'        => true,
            'affiliation'    => false,
            'parent'         => [
                'slug' => 'options-general.php',
            ],
        ],
        'is_live'          => true,
        'is_org_compliant' => true,
    ] );
    // Signal that SDK was initiated.
    do_action( 'ctc/freemius/loaded' );
    return $ctc_fs;
}

/**
 * Initialize Freemius SDK and add filters.
 *
 * Called on plugins_loaded to avoid early translation loading issues.
 */
function ctc_fs_init() {
    $fs = ctc_fs();
    if ( !$fs ) {
        return;
    }
    // @phpstan-ignore-next-line
    $fs->add_filter( 'connect_url', 'ctc_fs_settings_url' );
    // @phpstan-ignore-next-line
    $fs->add_filter( 'after_skip_url', 'ctc_fs_settings_url' );
    // @phpstan-ignore-next-line
    $fs->add_filter( 'after_connect_url', 'ctc_fs_settings_url' );
    // @phpstan-ignore-next-line
    $fs->add_filter( 'after_pending_connect_url', 'ctc_fs_settings_url' );
    // @phpstan-ignore-next-line
    $fs->add_filter( 'after_pending_connect_url', 'ctc_enable_telemetry' );
    // Clean up telemetry options when user uninstalls (Freemius runs this via after_uninstall).
    // Do not add uninstall.php in plugin root — Freemius requires it absent to track uninstall feedback.
    $fs->add_action( 'after_uninstall', 'ctc_cleanup_on_uninstall' );
}

/**
 * Enable telemetry.
 */
function ctc_enable_telemetry() {
    Telemetry::set_opt_in( true );
    Telemetry::maybe_send();
    Telemetry::schedule_cron();
    return ctc_fs_settings_url();
}

/**
 * Clean up plugin options on uninstall (called by Freemius after_uninstall hook).
 *
 * Install identity is domain-based (no stored install id), so reinstall on same domain keeps the same id.
 */
function ctc_cleanup_on_uninstall() {
    delete_option( 'ctc_telemetry_opt_in' );
    delete_option( 'ctc_telemetry_first_seen' );
    delete_option( 'ctc_telemetry_last_sent' );
    delete_option( 'ctc_install_id' );
    wp_clear_scheduled_hook( 'ctc_telemetry_weekly_send' );
}

/**
 * Get the settings URL.
 *
 * @return string
 */
function ctc_fs_settings_url() {
    return admin_url( 'options-general.php?page=ctc-global-injector' );
}

// Initialize Freemius on plugins_loaded (after textdomain is loaded).
add_action( 'plugins_loaded', 'ctc_fs_init', 10 );