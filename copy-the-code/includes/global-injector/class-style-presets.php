<?php
/**
 * Style Presets
 *
 * Registers the Style Presets Custom Post Type for storing
 * visual styling configurations per style (Button, Icon, Cover).
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector;

/**
 * Style Presets
 *
 * @since 5.0.0
 */
class Style_Presets {

	/**
	 * Instance
	 *
	 * @since 5.0.0
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Post type name
	 *
	 * @var string
	 */
	const POST_TYPE = 'ctc-style-preset';

	/**
	 * Maximum presets per style for free users
	 *
	 * @var int
	 */
	const MAX_FREE_PRESETS_PER_STYLE = 3;

	/**
	 * Initiator
	 *
	 * @since 5.0.0
	 * @return object initialized object of class.
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	/**
	 * Register custom post type
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function register_post_type() {
		$labels = [
			'name'               => _x( 'Style Presets', 'post type general name', 'ctc' ),
			'singular_name'      => _x( 'Style Preset', 'post type singular name', 'ctc' ),
			'menu_name'          => _x( 'Style Presets', 'admin menu', 'ctc' ),
			'add_new'            => _x( 'Add New', 'style preset', 'ctc' ),
			'add_new_item'       => __( 'Add New Style Preset', 'ctc' ),
			'edit_item'          => __( 'Edit Style Preset', 'ctc' ),
			'new_item'           => __( 'New Style Preset', 'ctc' ),
			'view_item'          => __( 'View Style Preset', 'ctc' ),
			'search_items'       => __( 'Search Style Presets', 'ctc' ),
			'not_found'          => __( 'No style presets found', 'ctc' ),
			'not_found_in_trash' => __( 'No style presets found in trash', 'ctc' ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => [ 'title' ],
			'show_in_rest'       => false, // We use custom REST endpoints.
		];

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Get default presets (hardcoded)
	 *
	 * @since 5.0.0
	 * @return array Default presets by style.
	 */
	public static function get_default_presets() {
		return [
			'button' => [
				'id'            => 'default',
				'title'         => __( 'System Default', 'ctc' ),
				'visual_style'  => 'button',
				'is_default'    => true,
				'text_color'    => '#ffffff',
				'bg_color'      => '#4f46e5',
				'border_radius' => 6,
				'font_size'     => 13,
				'padding_x'     => 16,
				'padding_y'     => 8,
			],
			'icon'   => [
				'id'               => 'default',
				'title'            => __( 'System Default', 'ctc' ),
				'visual_style'     => 'icon',
				'is_default'       => true,
				'icon_color'       => '#6b7280',
				'icon_hover_color' => '#4f46e5',
				'bg_color'         => '#ffffff',
				'border_color'     => '#e5e7eb',
				'icon_size'        => 16,
				'padding'          => 8,
				'border_radius'    => 8,
				'border_width'     => 1,
			],
			'cover'  => [
				'id'                  => 'default',
				'title'               => __( 'System Default', 'ctc' ),
				'visual_style'        => 'cover',
				'is_default'          => true,
				'overlay_color'       => '#0f172a',
				'text_color'          => '#0f172a',
				'badge_bg_color'      => '#ffffff',
				'hover_overlay_color' => '#4f46e5',
				'overlay_opacity'     => 10,
				'blur'                => 2,
				'font_size'           => 10,
				'badge_radius'        => 9999,
			],
		];
	}

	/**
	 * Get presets count by style
	 *
	 * @since 5.0.0
	 * @param string $visual_style Style type (button, icon, cover).
	 * @return int Number of presets for this style.
	 */
	public static function get_preset_count_by_style( $visual_style ) {
		$query = new \WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'visual_style',
						'value' => $visual_style,
					],
				],
				'fields'         => 'ids',
			]
		);

		return $query->found_posts;
	}

	/**
	 * Check if user can create preset for style
	 *
	 * @since 5.0.0
	 * @param string $visual_style Style type.
	 * @param bool   $is_pro Whether user has Pro.
	 * @return bool
	 */
	public static function can_create_preset( $visual_style, $is_pro = false ) {
		if ( $is_pro ) {
			return true;
		}

		$count = self::get_preset_count_by_style( $visual_style );
		return $count < self::MAX_FREE_PRESETS_PER_STYLE;
	}

	/**
	 * Load all presets
	 *
	 * @since 5.0.0
	 * @return array All presets grouped by style.
	 */
	public static function load_all_presets() {
		$presets = [
			'button' => [],
			'icon'   => [],
			'cover'  => [],
		];

		$query = new \WP_Query(
			[
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'ASC',
			]
		);

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$preset = self::format_preset( $post );
				$style  = $preset['visual_style'];
				if ( isset( $presets[ $style ] ) ) {
					$presets[ $style ][] = $preset;
				}
			}
		}

		return $presets;
	}

	/**
	 * Format preset data from post
	 *
	 * @since 5.0.0
	 * @param \WP_Post $post Post object.
	 * @return array Formatted preset data.
	 */
	public static function format_preset( $post ) {
		$id           = $post->ID;
		$visual_style = get_post_meta( $id, 'visual_style', true ) ?: 'button';

		$base = [
			'id'           => $id,
			'title'        => $post->post_title,
			'visual_style' => $visual_style,
			'is_default'   => false,
		];

		// Load style-specific meta fields.
		switch ( $visual_style ) {
			case 'icon':
				return array_merge(
					$base,
					[
						'icon_color'       => get_post_meta( $id, 'icon_color', true ) ?: '#6b7280',
						'icon_hover_color' => get_post_meta( $id, 'icon_hover_color', true ) ?: '#4f46e5',
						'bg_color'         => get_post_meta( $id, 'bg_color', true ) ?: '#ffffff',
						'border_color'     => get_post_meta( $id, 'border_color', true ) ?: '#e5e7eb',
						'icon_size'        => (int) get_post_meta( $id, 'icon_size', true ) ?: 16,
						'padding'          => (int) get_post_meta( $id, 'padding', true ) ?: 8,
						'border_radius'    => (int) get_post_meta( $id, 'border_radius', true ) ?: 8,
						'border_width'     => (int) get_post_meta( $id, 'border_width', true ) ?: 1,
					]
				);

			case 'cover':
				return array_merge(
					$base,
					[
						'overlay_color'       => get_post_meta( $id, 'overlay_color', true ) ?: '#0f172a',
						'text_color'          => get_post_meta( $id, 'text_color', true ) ?: '#0f172a',
						'badge_bg_color'      => get_post_meta( $id, 'badge_bg_color', true ) ?: '#ffffff',
						'hover_overlay_color' => get_post_meta( $id, 'hover_overlay_color', true ) ?: '#4f46e5',
						'overlay_opacity'     => (int) get_post_meta( $id, 'overlay_opacity', true ) ?: 10,
						'blur'                => (int) get_post_meta( $id, 'blur', true ) ?: 2,
						'font_size'           => (int) get_post_meta( $id, 'font_size', true ) ?: 10,
						'badge_radius'        => (int) get_post_meta( $id, 'badge_radius', true ) ?: 9999,
					]
				);

			case 'button':
			default:
				return array_merge(
					$base,
					[
						'text_color'    => get_post_meta( $id, 'text_color', true ) ?: '#ffffff',
						'bg_color'      => get_post_meta( $id, 'bg_color', true ) ?: '#4f46e5',
						'border_color'  => get_post_meta( $id, 'border_color', true ) ?: '#4338ca',
						'border_radius' => (int) get_post_meta( $id, 'border_radius', true ) ?: 6,
						'font_size'     => (int) get_post_meta( $id, 'font_size', true ) ?: 13,
						'padding_x'     => (int) get_post_meta( $id, 'padding_x', true ) ?: 16,
						'padding_y'     => (int) get_post_meta( $id, 'padding_y', true ) ?: 8,
					]
				);
		}
	}
}
