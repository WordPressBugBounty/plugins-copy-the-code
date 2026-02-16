<?php
/**
 * Global Injector
 *
 * @package CTC
 * @since 5.1.0
 */

namespace CTC;

use CTC\Helper;
use CTC\Global_Injector\Styles\Button as ButtonStyle;
use CTC\Global_Injector\Styles\Icon as IconStyle;
use CTC\Global_Injector\Styles\Cover as CoverStyle;
use CTC\Global_Injector\Display_Conditions;

/**
 * Global Injector
 *
 * @since 5.1.0
 */
class Global_Injector {

	/**
	 * Instance
	 *
	 * @since 5.1.0
	 *
	 * @access private
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 5.1.0
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
	 * @since 5.1.0
	 */
	public function __construct() {
		// Menus are registered under Settings in \CTC\Base::register_menus().
	}

	/**
	 * Render Global Injector page
	 *
	 * @since 5.1.0
	 * @return void
	 */
	public function render() {
		// Remove WordPress admin footer text.
		add_filter( 'admin_footer_text', '__return_empty_string' );
		add_filter( 'update_footer', '__return_empty_string', 11 );

		$this->enqueue_assets();
		$this->localize_data();
		?>
		<div class="wrap ctc-admin-root ctc-global-injector-page" id="ctc-global-injector-root"></div>
		<?php
	}

	/**
	 * Enqueue assets (scripts and styles)
	 *
	 * @since 5.1.0
	 * @return void
	 */
	private function enqueue_assets() {
		wp_enqueue_style(
			'ctc-global-injector',
			CTC_URI . 'assets/admin/css/global-injector.css',
			[],
			CTC_VER
		);

		wp_enqueue_script(
			'ctc-global-injector',
			CTC_URI . 'assets/admin/js/global-injector.js',
			[
				'wp-element',
				'wp-data',
				'wp-components',
				'wp-i18n',
				'lodash',
				'jquery',
			],
			CTC_VER,
			true
		);
	}

	/**
	 * Localize data for React app
	 *
	 * @since 5.1.0
	 * @return void
	 */
	private function localize_data() {
		$rules           = $this->load_rules();
		$presets         = Global_Injector\Style_Presets::load_all_presets();
		$default_presets = Global_Injector\Style_Presets::get_default_presets();

		$selected_rule_id = $this->get_selected_rule_id( $rules );

		wp_localize_script(
			'ctc-global-injector',
			'GlobalInjectorVars',
			apply_filters(
				'ctc/global_injector/localize_data',
				[
					'version'           => CTC_VER,
					'isPro'             => Helper::is_pro(),
					'rules'             => $rules,
					'selectedRuleId'    => $selected_rule_id,
					'apiUrl'            => rest_url( 'ctc/v1/' ),
					'nonce'             => wp_create_nonce( 'wp_rest' ),
					'presets'           => $presets,
					'defaultPresets'    => $default_presets,
					'presetLimits'      => [
						'button' => [
							'count' => Global_Injector\Style_Presets::get_preset_count_by_style( 'button' ),
							'max'   => Global_Injector\Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
						],
						'icon'   => [
							'count' => Global_Injector\Style_Presets::get_preset_count_by_style( 'icon' ),
							'max'   => Global_Injector\Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
						],
						'cover'  => [
							'count' => Global_Injector\Style_Presets::get_preset_count_by_style( 'cover' ),
							'max'   => Global_Injector\Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
						],
					],
					// Visual Style Configurations (single source of truth).
					'styles'            => [
						'button' => ButtonStyle::get_localize_data(),
						'icon'   => IconStyle::get_localize_data(),
						'cover'  => CoverStyle::get_localize_data(),
					],
					// Display Conditions.
					'displayConditions' => Display_Conditions::get_localize_data(),
					'urls'              => [
						'rules' => admin_url( 'options-general.php?page=ctc-rules' ),
					],
				]
			),
		);
	}

	/**
	 * Get rules for admin list views.
	 *
	 * @since 5.1.0
	 * @return array
	 */
	public function get_admin_rules() {
		return $this->load_rules();
	}

	/**
	 * Get the selected rule ID for the editor (default first rule, or from ?rule= if valid).
	 *
	 * @since 5.1.0
	 * @param array $rules Rules array (each item has 'id' key).
	 * @return int|null Selected rule ID, or null if no rules.
	 */
	private function get_selected_rule_id( $rules ) {
		$selected = ! empty( $rules ) ? $rules[0]['id'] : null;
		if ( ! empty( $_GET['rule'] ) && is_numeric( $_GET['rule'] ) ) {
			$requested_id = (int) $_GET['rule'];
			foreach ( $rules as $rule ) {
				if ( isset( $rule['id'] ) && (int) $rule['id'] === $requested_id ) {
					$selected = $requested_id;
					break;
				}
			}
		}
		return $selected;
	}

	/**
	 * Load rules from database
	 *
	 * @since 5.1.0
	 * @return array
	 */
	private function load_rules() {
		$posts = get_posts(
			[
				'post_type'      => 'copy-to-clipboard',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'orderby'        => 'date',
				'order'          => 'DESC',
			]
		);

		// Get default presets for style fallbacks.
		$default_presets = Global_Injector\Style_Presets::get_default_presets();

		$rules = [];
		foreach ( $posts as $post ) {
			$button_text        = get_post_meta( $post->ID, 'button-text', true );
			$success_text       = get_post_meta( $post->ID, 'button-copy-text', true );
			$tooltip_text       = get_post_meta( $post->ID, 'tooltip-text', true );
			$reveal_text        = get_post_meta( $post->ID, 'reveal-text', true );
			$button_title       = get_post_meta( $post->ID, 'button-title', true );
			$copy_format        = get_post_meta( $post->ID, 'copy-format', true );
			$copy_as            = get_post_meta( $post->ID, 'copy-as', true );
			$selector           = get_post_meta( $post->ID, 'selector', true );
			$exclude_selector   = get_post_meta( $post->ID, 'exclude_selector', true );
			$style_type         = get_post_meta( $post->ID, 'style', true );
			$visual_style_raw   = get_post_meta( $post->ID, 'visual_style', true );
			$visual_style       = ! empty( $visual_style_raw ) ? $visual_style_raw : 'button';
			$button_position    = get_post_meta( $post->ID, 'button-position', true );
			$display_conditions = get_post_meta( $post->ID, 'display_conditions', true );

			// Get style defaults for this visual style.
			$style_defaults = isset( $default_presets[ $visual_style ] ) ? $default_presets[ $visual_style ] : $default_presets['button'];

			// Helper function for getting style defaults.
			$style_default = function ( $key, $fallback ) use ( $style_defaults ) {
				return isset( $style_defaults[ $key ] ) ? $style_defaults[ $key ] : $fallback;
			};

			// Load Button style fields.
			$text_color    = get_post_meta( $post->ID, 'text_color', true );
			$bg_color      = get_post_meta( $post->ID, 'bg_color', true );
			$border_radius = get_post_meta( $post->ID, 'border_radius', true );
			$font_size     = get_post_meta( $post->ID, 'font_size', true );
			$padding_x     = get_post_meta( $post->ID, 'padding_x', true );
			$padding_y     = get_post_meta( $post->ID, 'padding_y', true );

			// Load Icon style fields.
			$icon_color       = get_post_meta( $post->ID, 'icon_color', true );
			$icon_hover_color = get_post_meta( $post->ID, 'icon_hover_color', true );
			$border_color     = get_post_meta( $post->ID, 'border_color', true );
			$icon_size        = get_post_meta( $post->ID, 'icon_size', true );
			$padding          = get_post_meta( $post->ID, 'padding', true );
			$border_width     = get_post_meta( $post->ID, 'border_width', true );

			// Load Cover style fields.
			$overlay_color        = get_post_meta( $post->ID, 'overlay_color', true );
			$hover_overlay_color  = get_post_meta( $post->ID, 'hover_overlay_color', true );
			$overlay_opacity      = get_post_meta( $post->ID, 'overlay_opacity', true );
			$blur                 = get_post_meta( $post->ID, 'blur', true );
			$cover_text_color     = get_post_meta( $post->ID, 'cover_text_color', true );
			$cover_bg_color       = get_post_meta( $post->ID, 'cover_bg_color', true );
			$cover_hover_bg_color = get_post_meta( $post->ID, 'cover_hover_bg_color', true );
			$cover_border_radius  = get_post_meta( $post->ID, 'cover_border_radius', true );
			$cover_font_size      = get_post_meta( $post->ID, 'cover_font_size', true );
			$cover_padding_x      = get_post_meta( $post->ID, 'cover_padding_x', true );
			$cover_padding_y      = get_post_meta( $post->ID, 'cover_padding_y', true );

			$is_active = get_post_meta( $post->ID, 'is_active', true );
			if ( '' === $is_active ) {
				// Fallback for rules without is_active meta.
				$is_active = 'publish' === $post->post_status;
			} else {
				// Handle both string '1'/'0' and boolean values.
				$is_active = '0' !== $is_active && false !== $is_active;
			}

			// Load icon settings.
			$icon_enabled_raw = get_post_meta( $post->ID, 'icon_enabled', true );
			$icon_enabled     = '' === $icon_enabled_raw ? true : '1' === $icon_enabled_raw;
			$icon_position    = get_post_meta( $post->ID, 'icon_position', true );
			$icon_key         = get_post_meta( $post->ID, 'icon_key', true );

			$rule = [
				'id'                   => $post->ID,
				'title'                => isset( $post->post_title ) ? $post->post_title : __( 'Untitled Rule', 'ctc' ),
				'selector'             => ! empty( $selector ) ? $selector : 'pre',
				'exclude_selector'     => ! empty( $exclude_selector ) ? $exclude_selector : '',
				'style_type'           => ! empty( $style_type ) ? $style_type : 'button',
				'visual_style'         => $visual_style,
				'button_text'          => ! empty( $button_text ) ? $button_text : __( 'Copy to Clipboard', 'ctc' ),
				'success_text'         => ! empty( $success_text ) ? $success_text : __( 'Copied!', 'ctc' ),
				'tooltip_text'         => ! empty( $tooltip_text ) ? $tooltip_text : __( 'Copy to clipboard', 'ctc' ),
				'reveal_text'          => ! empty( $reveal_text ) ? $reveal_text : __( 'Click to Reveal', 'ctc' ),
				'button_position'      => ! empty( $button_position ) ? $button_position : 'inside',
				'is_active'            => $is_active,
				'display_conditions'   => ! empty( $display_conditions ) ? $display_conditions : [],
				'button_title'         => ! empty( $button_title ) ? $button_title : '',
				'copy_format'          => ! empty( $copy_format ) ? $copy_format : '',
				'copy_as'              => ! empty( $copy_as ) ? $copy_as : '',
				// Icon settings.
				'icon_enabled'         => $icon_enabled,
				'icon_position'        => ! empty( $icon_position ) ? $icon_position : 'left',
				'icon_key'             => ! empty( $icon_key ) ? $icon_key : 'clipboard',
				// Style fields (with default preset fallback).
				// Button style fields.
				'text_color'           => ! empty( $text_color ) ? $text_color : $style_default( 'text_color', '#ffffff' ),
				'bg_color'             => ! empty( $bg_color ) ? $bg_color : $style_default( 'bg_color', '#4f46e5' ),
				'border_radius'        => (int) ( ! empty( $border_radius ) ? $border_radius : $style_default( 'border_radius', 6 ) ),
				'font_size'            => (int) ( ! empty( $font_size ) ? $font_size : $style_default( 'font_size', 13 ) ),
				'padding_x'            => (int) ( ! empty( $padding_x ) ? $padding_x : $style_default( 'padding_x', 16 ) ),
				'padding_y'            => (int) ( ! empty( $padding_y ) ? $padding_y : $style_default( 'padding_y', 8 ) ),
				// Icon style fields.
				'icon_color'           => ! empty( $icon_color ) ? $icon_color : $style_default( 'icon_color', '#6b7280' ),
				'icon_hover_color'     => ! empty( $icon_hover_color ) ? $icon_hover_color : $style_default( 'icon_hover_color', '#4f46e5' ),
				'border_color'         => ! empty( $border_color ) ? $border_color : $style_default( 'border_color', '#e5e7eb' ),
				'icon_size'            => (int) ( ! empty( $icon_size ) ? $icon_size : $style_default( 'icon_size', 16 ) ),
				'padding'              => (int) ( ! empty( $padding ) ? $padding : $style_default( 'padding', 8 ) ),
				'border_width'         => (int) ( ! empty( $border_width ) ? $border_width : $style_default( 'border_width', 1 ) ),
				// Cover style fields.
				'overlay_color'        => ! empty( $overlay_color ) ? $overlay_color : $style_default( 'overlay_color', '#0f172a' ),
				'hover_overlay_color'  => ! empty( $hover_overlay_color ) ? $hover_overlay_color : $style_default( 'hover_overlay_color', '#4f46e5' ),
				'overlay_opacity'      => (int) ( ! empty( $overlay_opacity ) ? $overlay_opacity : $style_default( 'overlay_opacity', 20 ) ),
				'blur'                 => (int) ( ! empty( $blur ) ? $blur : $style_default( 'blur', 2 ) ),
				'cover_text_color'     => ! empty( $cover_text_color ) ? $cover_text_color : $style_default( 'text_color', '#0f172a' ),
				'cover_bg_color'       => ! empty( $cover_bg_color ) ? $cover_bg_color : $style_default( 'badge_bg_color', '#ffffff' ),
				'cover_hover_bg_color' => ! empty( $cover_hover_bg_color ) ? $cover_hover_bg_color : '#f1f5f9',
				'cover_border_radius'  => (int) ( ! empty( $cover_border_radius ) ? $cover_border_radius : $style_default( 'badge_radius', 9999 ) ),
				'cover_font_size'      => (int) ( ! empty( $cover_font_size ) ? $cover_font_size : $style_default( 'font_size', 10 ) ),
				'cover_padding_x'      => (int) ( ! empty( $cover_padding_x ) ? $cover_padding_x : 12 ),
				'cover_padding_y'      => (int) ( ! empty( $cover_padding_y ) ? $cover_padding_y : 6 ),
			];

			/**
			 * Filter the admin rule data before adding to the rules array.
			 *
			 * Pro add-on can use this to add additional fields like custom_css_class.
			 *
			 * @since 5.1.0
			 * @param array    $rule Rule data array.
			 * @param \WP_Post $post Post object.
			 */
			$rules[] = apply_filters( 'ctc/global_injector/admin_rule_data', $rule, $post );
		}

		return $rules;
	}
}
