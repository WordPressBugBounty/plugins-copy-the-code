<?php

/**
 * Core plugin class.
 *
 * @package CTC
 * @since 5.0.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * CTC class.
 *
 * @class Main class of the plugin.
 */
final class CTC {
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '5.0.0';

    /**
     * The single instance of the class.
     *
     * @var CTC
     */
    protected static $instance = null;

    /**
     * Retrieve main CTC instance.
     *
     * Ensure only one instance is loaded or can be loaded.
     *
     * @see ctc()
     * @return CTC
     */
    public static function get() {
        if ( null === self::$instance ) {
            self::$instance = new CTC();
            self::$instance->setup();
        }
        return self::$instance;
    }

    /**
     * Instantiate the plugin.
     */
    private function setup() {
        // Include required files.
        $this->includes();
        // Register activation hook to deactivate free when pro is activated.
        register_activation_hook( CTC_FILE, [__CLASS__, 'activation'] );
        // Hook initialization to 'init' to avoid early translation loading issues (WP 6.7+).
        add_action( 'init', [$this, 'init'], 0 );
        // Load textdomain early on plugins_loaded.
        add_action( 'plugins_loaded', [$this, 'load_textdomain'], 9 );
        // Plugin action links.
        add_filter( 'plugin_action_links_' . plugin_basename( CTC_FILE ), [$this, 'action_links'] );
    }

    /**
     * Initialize plugin components.
     *
     * Called at 'init' action to ensure translations are available.
     *
     * @since 5.0.0
     */
    public function init() {
        // Initialize components.
        $this->initialize();
        // Loaded action.
        do_action( 'ctc/loaded' );
    }

    /**
     * Include the required files.
     */
    private function includes() {
        include CTC_DIR . 'vendor/autoload.php';
    }

    /**
     * Initialize the plugin.
     */
    private function initialize() {
        \CTC\Base::get();
        // Register post types.
        \CTC\Post_Types::get();
        // Plugin updater.
        \CTC\Updater::get();
        // Welcome notice for onboarding.
        \CTC\Welcome::get();
        // Initialize Style Presets CPT.
        \CTC\Global_Injector\Style_Presets::get();
        // Initialize REST API.
        \CTC\Global_Injector\Rest::get();
        \CTC\Shortcode::get();
        // Elementor blocks.
        \CTC\Elementor\Blocks::get();
        // Gutenberg blocks.
        \CTC\Gutenberg\Blocks::get();
        if ( is_admin() ) {
            // Admin: Global Injector settings page.
            \CTC\Global_Injector::get();
        } else {
            // Frontend: Global Injector rules rendering.
            \CTC\Global_Injector\Frontend::get();
        }
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'ctc', false, dirname( CTC_BASE ) . '/languages/' );
    }

    /**
     * Add plugin action links.
     *
     * @param array $links Plugin action links.
     * @return array Modified plugin action links.
     */
    public function action_links( $links ) {
        $action_links = [
            'settings' => '<a href="' . admin_url( 'admin.php?page=ctc-global-injector' ) . '">' . esc_html__( 'Settings', 'ctc' ) . '</a>',
        ];
        return array_merge( $action_links, $links );
    }

    /**
     * Plugin activation callback.
     *
     * Deactivates the free version if pro version is being activated.
     * This prevents conflicts when both versions are installed.
     *
     * @since 5.0.0
     */
    public static function activation() {
        // Check if this is the premium version (has /premium/ folder).
        $is_premium = file_exists( CTC_DIR . 'premium/' );
        if ( !$is_premium ) {
            return;
        }
        // Include plugin.php if not available (needed for is_plugin_active and deactivate_plugins).
        if ( !function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        // Possible free version plugin paths.
        // Freemius uses 'copy-the-code' for free and 'copy-the-code-premium' for pro.
        $free_plugins = ['copy-the-code/copy-the-code.php'];
        foreach ( $free_plugins as $free_plugin ) {
            // Skip if this is the same plugin.
            if ( CTC_BASE === $free_plugin ) {
                continue;
            }
            // Deactivate free version if active.
            if ( is_plugin_active( $free_plugin ) ) {
                deactivate_plugins( $free_plugin );
            }
        }
    }

}

/**
 * Returns the main instance of CTC to prevent the need to use globals.
 *
 * @return CTC
 */
function ctc() {
    // @codingStandardsIgnoreLine
    return CTC::get();
}
