<?php
/**
 * Post Types Registration
 *
 * Handles registration of custom post types for the plugin.
 *
 * @package CTC
 * @since 5.1.0
 */

namespace CTC;

/**
 * Post_Types Class
 *
 * Registers custom post types.
 */
class Post_Types {

	/**
	 * Instance
	 *
	 * @var Post_Types|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Post_Types
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
		add_action( 'init', [ $this, 'register' ] );

		/*
		 * Remove these redirects once the legacy post type editor is fully deprecated.
		 * These redirect the "Add New" button and edit links to the Global Injector page.
		 */
		add_filter( 'admin_url', [ $this, 'redirect_add_new_to_global_injector' ], 10, 2 );
		add_action( 'current_screen', [ $this, 'maybe_add_edit_link_filter' ] );
	}

	/**
	 * Redirect "Add New" button to Global Injector page.
	 *
	 * Remove this method once the legacy post type editor is fully deprecated.
	 *
	 * @since 5.1.0
	 *
	 * @param string $url  The complete admin area URL including scheme and path.
	 * @param string $path Path relative to the admin area URL.
	 * @return string Modified URL.
	 */
	public function redirect_add_new_to_global_injector( $url, $path ) {
		if ( 'post-new.php?post_type=copy-to-clipboard' === $path ) {
			return admin_url( 'options-general.php?page=ctc-global-injector' );
		}

		return $url;
	}

	/**
	 * Conditionally add edit link filter only on copy-to-clipboard list table.
	 *
	 * Remove this method once the legacy post type editor is fully deprecated.
	 *
	 * @since 5.1.0
	 *
	 * @param \WP_Screen $screen Current admin screen object.
	 */
	public function maybe_add_edit_link_filter( $screen ) {
		if ( 'copy-to-clipboard' === $screen->post_type && 'edit' === $screen->base ) {
			add_filter( 'get_edit_post_link', [ $this, 'redirect_edit_link_to_global_injector' ], 10, 2 );
		}
	}

	/**
	 * Redirect edit post links to Global Injector page.
	 *
	 * This filter is only added on the copy-to-clipboard list table screen,
	 * so we don't need to check post type here.
	 *
	 * Remove this method once the legacy post type editor is fully deprecated.
	 *
	 * @since 5.1.0
	 *
	 * @param string $link    The edit link.
	 * @param int    $post_id Post ID.
	 * @return string Modified URL.
	 */
	public function redirect_edit_link_to_global_injector( $link, $post_id ) {
		return admin_url( 'options-general.php?page=ctc-global-injector&rule=' . $post_id );
	}

	/**
	 * Register custom post types.
	 *
	 * @since 5.1.0
	 */
	public function register() {
		$labels = [
			'name'               => __( 'Copy to Clipboard', 'ctc' ),
			'singular_name'      => __( 'Copy to Clipboard', 'ctc' ),
			'add_new'            => _x( 'Add New', 'ctc', 'ctc' ),
			'add_new_item'       => __( 'Add New', 'ctc' ),
			'edit_item'          => __( 'Edit Copy to Clipboard', 'ctc' ),
			'new_item'           => __( 'New Copy to Clipboard', 'ctc' ),
			'view_item'          => __( 'View Copy to Clipboard', 'ctc' ),
			'search_items'       => __( 'Search Copy to Clipboard', 'ctc' ),
			'not_found'          => __( 'No Copy to Clipboard found', 'ctc' ),
			'not_found_in_trash' => __( 'No Copy to Clipboard found in Trash', 'ctc' ),
			'parent_item_colon'  => __( 'Parent Copy to Clipboard:', 'ctc' ),
			'menu_name'          => __( 'Copy to Clipboard', 'ctc' ),
		];

		$args = [
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => [],
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-clipboard',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => [
				'title',
			],
		];

		register_post_type( 'copy-to-clipboard', $args );
	}
}
