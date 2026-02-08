<?php
/**
 * Global Injector Frontend
 *
 * Handles frontend rendering and script loading for Global Injector rules.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector;

use CTC\Helper;
use CTC\Global_Injector\Display_Conditions;
use CTC\Global_Injector\Inline_CSS;
use CTC\Global_Injector\Styles\Button as ButtonStyle;
use CTC\Global_Injector\Styles\Icon as IconStyle;
use CTC\Global_Injector\Styles\Cover as CoverStyle;

/**
 * Frontend
 *
 * @since 5.0.0
 */
class Frontend {

	/**
	 * Instance
	 *
	 * @since 5.0.0
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 5.0.0
	 *
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
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_links' ], 100 );
	}

	/**
	 * Enqueue frontend assets
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function enqueue_assets() {
		$rules = $this->get_rules_for_page();
		if ( empty( $rules ) ) {
			return;
		}

		// Enqueue CTC lib core library (CopyEngine, CapabilityDetector).
		wp_enqueue_script(
			'ctc-lib-core',
			CTC_URI . 'assets/frontend/js/lib/ctc.js',
			[],
			CTC_VER,
			true
		);

		// Enqueue Global Injector frontend script.
		wp_enqueue_script(
			'ctc-global-injector-frontend',
			CTC_URI . 'assets/frontend/js/global-injector.js',
			[ 'ctc-lib-core' ],
			CTC_VER,
			true
		);

		// Inline CSS: only for needed styles/positions, minified, filterable. No separate CSS file.
		$needed     = $this->get_needed_styles_and_positions( $rules );
		$raw_css    = Inline_CSS::build( $needed['styles'], $needed['positions'] );
		$min_css    = Helper::minify_css( $raw_css );
		$inline_css = (string) apply_filters( 'ctc/global_injector/inline_css', $min_css );

		if ( $inline_css !== '' ) {
			wp_register_style( 'ctc-global-injector-inline', false, [], CTC_VER );
			wp_enqueue_style( 'ctc-global-injector-inline' );
			wp_add_inline_style( 'ctc-global-injector-inline', $inline_css );
		}

		// Localize script with rules and page context.
		wp_localize_script(
			'ctc-global-injector-frontend',
			'ctcGlobalInjector',
			$this->get_localize_data( $rules )
		);
	}

	/**
	 * Get localize data for frontend script
	 *
	 * @since 5.0.0
	 * @param array $rules Active rules.
	 * @return array
	 */
	private function get_localize_data( $rules ) {
		return [
			'rules'       => $rules,
			'apiUrl'      => rest_url( 'ctc/v1/rules' ),
			'eventsUrl'   => '', // rest_url( 'ctc/v1/events' ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
			'isPro'       => Helper::is_pro(),
			'postId'      => get_the_ID() ? get_the_ID() : null,
			'postType'    => get_post_type() ? get_post_type() : null,
			'pageUrl'     => $this->get_current_page_url(),
			'pageContext' => Display_Conditions::get_page_context(),
			'styles'      => [
				'button' => ButtonStyle::get_localize_data(),
				'icon'   => IconStyle::get_localize_data(),
				'cover'  => CoverStyle::get_localize_data(),
			],
		];
	}

	/**
	 * Get rules that should load on the current page (single source of truth).
	 *
	 * Used for enqueue_assets, admin bar, and localized data. No scripts or CSS
	 * are enqueued when this returns an empty array.
	 *
	 * @since 5.0.0
	 * @return array List of rule data arrays for rules that pass display conditions.
	 */
	public function get_rules_for_page() {
		$rules = $this->get_active_rules();
		return apply_filters( 'ctc/global_injector/rules_for_page', $rules );
	}

	/**
	 * Get which visual styles and positions are used by the given rules.
	 *
	 * Used to inject only the CSS needed for the current page.
	 *
	 * @since 5.0.2
	 * @param array $rules Rule data arrays (from get_rules_for_page).
	 * @return array{styles: string[], positions: string[]}
	 */
	private function get_needed_styles_and_positions( array $rules ) {
		$styles          = [];
		$positions       = [];
		$valid_positions = [
			'inside_top_left',
			'inside_top_right',
			'inside_bottom_left',
			'inside_bottom_right',
			'outside_before_left',
			'outside_before_right',
			'outside_after_left',
			'outside_after_right',
		];

		foreach ( $rules as $rule ) {
			$style = isset( $rule['visual_style'] ) ? $rule['visual_style'] : 'button';
			if ( ! in_array( $style, $styles, true ) ) {
				$styles[] = $style;
			}
			$pos = isset( $rule['button_position'] ) ? $rule['button_position'] : 'inside_top_right';
			if ( in_array( $pos, $valid_positions, true ) && ! in_array( $pos, $positions, true ) ) {
				$positions[] = $pos;
			}
		}

		if ( empty( $positions ) ) {
			$positions = [ 'inside_top_right' ];
		}

		return [
			'styles'    => array_values( $styles ),
			'positions' => array_values( $positions ),
		];
	}

	/**
	 * Get current page URL
	 *
	 * @since 5.0.0
	 * @return string|null
	 */
	private function get_current_page_url() {
		if ( is_singular() ) {
			return get_permalink();
		}

		if ( is_front_page() ) {
			return home_url( '/' );
		}

		// Fallback to current URL.
		global $wp;
		return home_url( add_query_arg( [], $wp->request ) );
	}

	/**
	 * Add admin bar links to edit Global Injector rules that apply on this page.
	 *
	 * Shows clipboard icon + "CTC" parent with "Edit: [Rule name]" children for each active rule.
	 *
	 * @since 5.0.0
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar object.
	 * @return void
	 */
	public function add_admin_bar_links( $wp_admin_bar ) {
		if ( is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$rules = $this->get_rules_for_page();
		if ( empty( $rules ) ) {
			return;
		}

		$base_url  = admin_url( 'options-general.php?page=ctc-global-injector' );
		$parent_id = 'ctc';
		$title     = __( 'CTC', 'ctc' );

		$wp_admin_bar->add_node(
			[
				'id'    => $parent_id,
				'title' => '<span class="ab-icon dashicons dashicons-clipboard" style="top: 4px;font-size: 18px;"></span><span class="ab-label">' . $title . '</span>',
				'href'  => $base_url,
				'meta'  => [
					'title' => __( 'Copy the Code – edit rules on this page', 'ctc' ),
				],
			]
		);

		foreach ( $rules as $rule ) {
			$rule_id    = isset( $rule['id'] ) ? (int) $rule['id'] : 0;
			$rule_title = $rule_id ? get_the_title( $rule_id ) : __( 'Untitled Rule', 'ctc' );
			$edit_url   = add_query_arg( 'rule', $rule_id, $base_url );

			$wp_admin_bar->add_node(
				[
					'parent' => $parent_id,
					'id'     => $parent_id . '-rule-' . $rule_id,
					'title'  => sprintf( /* translators: %s: rule name */ __( 'Edit: %s', 'ctc' ), $rule_title ),
					'href'   => $edit_url,
					'meta'   => [
						'title' => sprintf( /* translators: %s: rule name */ __( 'Edit rule "%s" in Global Injector', 'ctc' ), $rule_title ),
					],
				]
			);
		}
	}

	/**
	 * Get active rules for frontend
	 *
	 * @since 5.0.0
	 * @return array
	 */
	private function get_active_rules() {
		$posts = get_posts(
			[
				'post_type'      => 'copy-to-clipboard',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'orderby'        => 'date',
				'order'          => 'DESC',
			]
		);

		$rules = [];
		foreach ( $posts as $post ) {
			// Check if rule is active.
			$is_active = get_post_meta( $post->ID, 'is_active', true );
			if ( '' === $is_active ) {
				$is_active = 'publish' === $post->post_status;
			} else {
				$is_active = '0' !== $is_active && false !== $is_active;
			}

			// Skip inactive rules.
			if ( ! $is_active ) {
				continue;
			}

			// Get display conditions.
			$display_conditions = get_post_meta( $post->ID, 'display_conditions', true );

			// Evaluate display conditions.
			if ( ! empty( $display_conditions ) && is_array( $display_conditions ) ) {
				$should_display = Display_Conditions::evaluate( $display_conditions );
				if ( ! $should_display ) {
					continue;
				}
			}

			// Build rule data.
			$rule = $this->build_rule_data( $post );

			if ( $rule ) {
				/**
				 * Filter the frontend rule data before adding to the rules array.
				 *
				 * Pro add-on can use this to add additional fields like custom_css_class.
				 *
				 * @since 5.0.0
				 * @param array    $rule Rule data array.
				 * @param \WP_Post $post Post object.
				 */
				$rules[] = apply_filters( 'ctc/global_injector/frontend_rule_data', $rule, $post );
			}
		}

		return $rules;
	}

	/**
	 * Build rule data for frontend
	 *
	 * @since 5.0.0
	 * @param \WP_Post $post Post object.
	 * @return array|null
	 */
	private function build_rule_data( $post ) {
		$selector = get_post_meta( $post->ID, 'selector', true );

		// Skip rules without selectors.
		if ( empty( $selector ) ) {
			return null;
		}

		$visual_style_raw = get_post_meta( $post->ID, 'visual_style', true );
		$visual_style     = ! empty( $visual_style_raw ) ? $visual_style_raw : 'button';

		// Get style configuration based on visual style.
		$style_config = $this->get_style_config( $post->ID, $visual_style );

		return [
			'id'               => $post->ID,
			'selector'         => $selector,
			'exclude_selector' => get_post_meta( $post->ID, 'exclude_selector', true ),
			'visual_style'     => $visual_style,
			'button_text'      => $this->get_meta_with_default( $post->ID, 'button-text', __( 'Copy', 'ctc' ) ),
			'success_text'     => $this->get_meta_with_default( $post->ID, 'button-copy-text', __( 'Copied!', 'ctc' ) ),
			'tooltip_text'     => $this->get_meta_with_default( $post->ID, 'tooltip-text', __( 'Copy to clipboard', 'ctc' ) ),
			'button_position'  => $this->migrate_position( $this->get_meta_with_default( $post->ID, 'button-position', 'inside_top_right' ) ),
			'copy_format'      => $this->get_meta_with_default( $post->ID, 'copy-format', 'text' ),
			'icon_enabled'     => $this->get_bool_meta( $post->ID, 'icon_enabled', true ),
			'icon_position'    => $this->get_meta_with_default( $post->ID, 'icon_position', 'left' ),
			'icon_key'         => $this->get_meta_with_default( $post->ID, 'icon_key', 'clipboard' ),
			'style'            => $style_config,
		];
	}

	/**
	 * Get style configuration for a rule
	 *
	 * @since 5.0.0
	 * @param int    $post_id Post ID.
	 * @param string $visual_style Visual style type.
	 * @return array
	 */
	private function get_style_config( $post_id, $visual_style ) {
		switch ( $visual_style ) {
			case 'button':
				return [
					'text_color'    => $this->get_meta_with_default( $post_id, 'text_color', '#ffffff' ),
					'bg_color'      => $this->get_meta_with_default( $post_id, 'bg_color', '#4f46e5' ),
					'border_radius' => $this->get_int_meta_with_default( $post_id, 'border_radius', 6 ),
					'font_size'     => $this->get_int_meta_with_default( $post_id, 'font_size', 13 ),
					'padding_x'     => $this->get_int_meta_with_default( $post_id, 'padding_x', 16 ),
					'padding_y'     => $this->get_int_meta_with_default( $post_id, 'padding_y', 8 ),
				];

			case 'icon':
				return [
					'icon_color'       => $this->get_meta_with_default( $post_id, 'icon_color', '#6b7280' ),
					'icon_hover_color' => $this->get_meta_with_default( $post_id, 'icon_hover_color', '#4f46e5' ),
					'bg_color'         => $this->get_meta_with_default( $post_id, 'bg_color', 'transparent' ),
					'border_color'     => $this->get_meta_with_default( $post_id, 'border_color', '#e5e7eb' ),
					'icon_size'        => $this->get_int_meta_with_default( $post_id, 'icon_size', 16 ),
					'padding'          => $this->get_int_meta_with_default( $post_id, 'padding', 8 ),
					'border_radius'    => $this->get_int_meta_with_default( $post_id, 'border_radius', 6 ),
					'border_width'     => $this->get_int_meta_with_default( $post_id, 'border_width', 1 ),
				];

			case 'cover':
				return [
					'overlay_color'        => $this->get_meta_with_default( $post_id, 'overlay_color', '#0f172a' ),
					'hover_overlay_color'  => $this->get_meta_with_default( $post_id, 'hover_overlay_color', '#4f46e5' ),
					'overlay_opacity'      => $this->get_int_meta_with_default( $post_id, 'overlay_opacity', 20 ),
					'blur'                 => $this->get_int_meta_with_default( $post_id, 'blur', 2 ),
					'cover_text_color'     => $this->get_meta_with_default( $post_id, 'cover_text_color', '#0f172a' ),
					'cover_bg_color'       => $this->get_meta_with_default( $post_id, 'cover_bg_color', '#ffffff' ),
					'cover_hover_bg_color' => $this->get_meta_with_default( $post_id, 'cover_hover_bg_color', '#f1f5f9' ),
					'cover_border_radius'  => $this->get_int_meta_with_default( $post_id, 'cover_border_radius', 9999 ),
					'cover_font_size'      => $this->get_int_meta_with_default( $post_id, 'cover_font_size', 10 ),
					'cover_padding_x'      => $this->get_int_meta_with_default( $post_id, 'cover_padding_x', 12 ),
					'cover_padding_y'      => $this->get_int_meta_with_default( $post_id, 'cover_padding_y', 6 ),
				];

			default:
				return [];
		}
	}

	/**
	 * Migrate legacy position values to new format
	 *
	 * Old wp.org plugin used 'inside' and 'outside' values.
	 * New format uses specific positions like 'inside_top_right'.
	 *
	 * @since 5.0.0
	 * @param string $position Position value (may be legacy or new).
	 * @return string Migrated position value.
	 */
	private function migrate_position( $position ) {
		// Legacy to new position mapping.
		$legacy_map = [
			'inside'  => 'inside_top_right',
			'outside' => 'outside_after_right',
		];

		// All valid new position values.
		$valid_positions = [
			'inside_top_left',
			'inside_top_right',
			'inside_bottom_left',
			'inside_bottom_right',
			'outside_before_left',
			'outside_before_right',
			'outside_after_left',
			'outside_after_right',
		];

		// If already a valid new position, return as-is.
		if ( in_array( $position, $valid_positions, true ) ) {
			return $position;
		}

		// If legacy value, migrate it.
		if ( isset( $legacy_map[ $position ] ) ) {
			return $legacy_map[ $position ];
		}

		// Default fallback.
		return 'inside_top_right';
	}

	/**
	 * Get post meta with default value
	 *
	 * @since 5.0.0
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @param mixed  $fallback Default value.
	 * @return mixed
	 */
	private function get_meta_with_default( $post_id, $key, $fallback ) {
		$value = get_post_meta( $post_id, $key, true );
		return ! empty( $value ) ? $value : $fallback;
	}

	/**
	 * Get integer post meta with default value
	 *
	 * @since 5.0.0
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @param int    $fallback Default value.
	 * @return int
	 */
	private function get_int_meta_with_default( $post_id, $key, $fallback ) {
		$value = get_post_meta( $post_id, $key, true );
		return ! empty( $value ) ? (int) $value : $fallback;
	}

	/**
	 * Get boolean meta value
	 *
	 * @since 5.0.0
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @param bool   $fallback Default value.
	 * @return bool
	 */
	private function get_bool_meta( $post_id, $key, $fallback = false ) {
		$value = get_post_meta( $post_id, $key, true );

		if ( '' === $value ) {
			return $fallback;
		}

		return '1' === $value || true === $value;
	}
}
