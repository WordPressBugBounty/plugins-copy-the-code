<?php
/**
 * Button Style for Global Injector
 *
 * Defines the JSON schema, icons, and rendering logic for the Button visual style.
 * This class serves as the single source of truth for both PHP (frontend) and JS (admin preview).
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector\Styles;

/**
 * Button Style Class
 */
class Button {

	/**
	 * Get the default configuration for button style.
	 *
	 * This is the single source of truth for the button JSON schema.
	 * Used by both PHP renderer and localized to JS for admin preview.
	 *
	 * @return array Default button configuration.
	 */
	public static function get_default_config() {
		return [
			'text'  => [
				'button_text'  => 'Copy Code',
				'success_text' => 'Copied!',
			],
			'icon'  => [
				'enabled'    => true,
				'position'   => 'left', // 'left' or 'right'
				'icon_key'   => 'clipboard',
				'custom_url' => null, // Pro feature: custom icon URL
			],
			'style' => [
				'text_color'             => '#ffffff',
				'background_color'       => '#4f46e5',
				'hover_background_color' => '#4338ca',
				'font_size'              => '12px',
				'font_weight'            => '700',
				'padding_x'              => '12px',
				'padding_y'              => '6px',
				'border_radius'          => '6px',
			],
		];
	}

	/**
	 * Get available icons for button style.
	 *
	 * Each icon has a key and SVG markup.
	 * The key is stored in config, SVG is used for rendering.
	 *
	 * @return array Array of icons with key => SVG markup.
	 */
	public static function get_icons() {
		return [
			'clipboard' => [
				'label' => 'Clipboard',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
			],
			'copy'      => [
				'label' => 'Copy',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>',
			],
			'paperclip' => [
				'label' => 'Attachment',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>',
			],
			'link'      => [
				'label' => 'Link',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
			],
			'document'  => [
				'label' => 'Document',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
			],
			'check'     => [
				'label' => 'Checkmark',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
			],
		];
	}

	/**
	 * Get success icon (shown after copy).
	 *
	 * @return string SVG markup for success icon.
	 */
	public static function get_success_icon() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
	}

	/**
	 * Generate a unique button ID.
	 *
	 * @return string Unique button ID.
	 */
	public static function generate_button_id() {
		return 'ctc-copy-btn-' . wp_generate_password( 8, false, false );
	}

	/**
	 * Merge user config with defaults.
	 *
	 * @param array $user_config User-provided configuration.
	 * @return array Merged configuration.
	 */
	public static function merge_config( $user_config = [] ) {
		$defaults = self::get_default_config();

		// Deep merge
		$merged = $defaults;

		if ( isset( $user_config['text'] ) && is_array( $user_config['text'] ) ) {
			$merged['text'] = array_merge( $defaults['text'], $user_config['text'] );
		}

		if ( isset( $user_config['icon'] ) && is_array( $user_config['icon'] ) ) {
			$merged['icon'] = array_merge( $defaults['icon'], $user_config['icon'] );
		}

		if ( isset( $user_config['style'] ) && is_array( $user_config['style'] ) ) {
			$merged['style'] = array_merge( $defaults['style'], $user_config['style'] );
		}

		return $merged;
	}

	/**
	 * Get the icon SVG by key.
	 *
	 * @param string $icon_key Icon key.
	 * @return string SVG markup or empty string if not found.
	 */
	public static function get_icon_svg( $icon_key ) {
		$icons = self::get_icons();

		if ( isset( $icons[ $icon_key ]['svg'] ) ) {
			return $icons[ $icon_key ]['svg'];
		}

		// Fallback to clipboard icon
		return $icons['clipboard']['svg'];
	}

	/**
	 * Darken a hex color by a percentage.
	 *
	 * @param string $hex     Hex color (e.g., '#4f46e5').
	 * @param int    $percent Percentage to darken (0-100).
	 * @return string Darkened hex color.
	 */
	public static function darken_color( $hex, $percent = 10 ) {
		// Remove # if present.
		$hex = ltrim( $hex, '#' );

		// Parse hex to RGB.
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		// Darken each channel.
		$factor = 1 - ( $percent / 100 );
		$r      = (int) round( $r * $factor );
		$g      = (int) round( $g * $factor );
		$b      = (int) round( $b * $factor );

		// Ensure values are in valid range.
		$r = max( 0, min( 255, $r ) );
		$g = max( 0, min( 255, $g ) );
		$b = max( 0, min( 255, $b ) );

		// Convert back to hex.
		return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
	}

	/**
	 * Build inline CSS variables from style config.
	 * Auto-calculates hover background if not provided (darkens by 12%).
	 *
	 * @param array $style Style configuration.
	 * @return string Inline style attribute value.
	 */
	public static function build_inline_styles( $style ) {
		// Auto-calculate hover background if not provided.
		$bg_color = isset( $style['background_color'] ) ? $style['background_color'] : '#4f46e5';
		$hover_bg = isset( $style['hover_background_color'] ) && ! empty( $style['hover_background_color'] )
			? $style['hover_background_color']
			: self::darken_color( $bg_color, 12 );

		$css_vars = [
			'--ctc-text-color'    => $style['text_color'],
			'--ctc-bg'            => $bg_color,
			'--ctc-hover-bg'      => $hover_bg,
			'--ctc-font-size'     => $style['font_size'],
			'--ctc-font-weight'   => $style['font_weight'],
			'--ctc-padding-x'     => $style['padding_x'],
			'--ctc-padding-y'     => $style['padding_y'],
			'--ctc-border-radius' => $style['border_radius'],
		];

		// Map CSS var names to property keys for unit detection.
		$var_to_prop = [
			'--ctc-font-size'     => 'font_size',
			'--ctc-padding-x'     => 'padding_x',
			'--ctc-padding-y'     => 'padding_y',
			'--ctc-border-radius' => 'border_radius',
		];

		$inline = [];
		foreach ( $css_vars as $var => $value ) {
			// Add 'px' unit for numeric properties.
			if ( isset( $var_to_prop[ $var ] ) && is_numeric( $value ) ) {
				$value = $value . 'px';
			}
			$inline[] = esc_attr( $var ) . ': ' . esc_attr( $value );
		}

		return implode( '; ', $inline );
	}

	/**
	 * Render the button HTML.
	 *
	 * This is the main render function used on the frontend.
	 * The same logic should be replicated in JS for admin preview.
	 *
	 * @param array  $config    Button configuration (merged with defaults).
	 * @param string $button_id Optional. Unique button ID. Auto-generated if not provided.
	 * @return string Button HTML markup.
	 */
	public static function render( $config = [], $button_id = null ) {
		// Merge with defaults
		$config = self::merge_config( $config );

		// Generate button ID if not provided
		if ( empty( $button_id ) ) {
			$button_id = self::generate_button_id();
		}

		// Get icon SVG
		$icon_svg = '';
		if ( $config['icon']['enabled'] ) {
			if ( ! empty( $config['icon']['custom_url'] ) ) {
				// Pro feature: custom icon
				$icon_svg = '<img src="' . esc_url( $config['icon']['custom_url'] ) . '" alt="" class="ctc-btn-icon" />';
			} else {
				$icon_svg = '<span class="ctc-btn-icon">' . self::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';
			}
		}

		// Build inline styles
		$inline_styles = self::build_inline_styles( $config['style'] );

		// Build button content based on icon position
		$button_content = '';
		if ( $config['icon']['position'] === 'left' && $icon_svg ) {
			$button_content .= $icon_svg;
		}
		$button_content .= '<span class="ctc-btn-text">' . esc_html( $config['text']['button_text'] ) . '</span>';
		if ( $config['icon']['position'] === 'right' && $icon_svg ) {
			$button_content .= $icon_svg;
		}

		// Build the button HTML
		$html = sprintf(
			'<button id="%s" class="ctc-copy-button" style="%s" data-success-text="%s" data-original-text="%s" aria-label="%s">%s</button>',
			esc_attr( $button_id ),
			esc_attr( $inline_styles ),
			esc_attr( $config['text']['success_text'] ),
			esc_attr( $config['text']['button_text'] ),
			esc_attr( $config['text']['button_text'] ),
			$button_content
		);

		return $html;
	}

	/**
	 * Get base CSS for button style.
	 *
	 * This CSS uses CSS variables that are set inline on each button.
	 * These are the "hidden" styles that users don't customize via UI.
	 *
	 * @return string CSS styles.
	 */
	public static function get_base_css() {
		return '
			.ctc-copy-button {
				/* User-customizable via CSS variables */
				color: var(--ctc-text-color, #ffffff);
				background-color: var(--ctc-bg, #4f46e5);
				font-size: var(--ctc-font-size, 12px);
				font-weight: var(--ctc-font-weight, 700);
				padding: var(--ctc-padding-y, 6px) var(--ctc-padding-x, 12px);
				border-radius: var(--ctc-border-radius, 6px);

				/* Hidden CSS - always applied, not customizable via UI */
				display: inline-flex;
				align-items: center;
				gap: 4px;
				border: none;
				cursor: pointer;
				transition: background-color 0.2s ease;
				position: absolute;
				top: 8px;
				right: 8px;
				z-index: 10;
				font-family: inherit;
				line-height: 1;
			}

			.ctc-copy-button:hover {
				background-color: var(--ctc-hover-bg, #4338ca);
			}

			.ctc-copy-button:focus {
				outline: 2px solid var(--ctc-bg, #4f46e5);
				outline-offset: 2px;
			}

			.ctc-copy-button .ctc-btn-icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
			}

			.ctc-copy-button .ctc-btn-icon svg {
				width: 14px;
				height: 14px;
			}

			.ctc-copy-button .ctc-btn-icon img {
				width: 14px;
				height: 14px;
				object-fit: contain;
			}

			.ctc-copy-button .ctc-btn-text {
				white-space: nowrap;
			}

			/* Success state */
			.ctc-copy-button.ctc-copied {
				background-color: #059669;
			}

			.ctc-copy-button.ctc-copied:hover {
				background-color: #047857;
			}
		';
	}

	/**
	 * Get stylesheet CSS for Global Injector (frontend).
	 *
	 * Used when outputting inline CSS for Global Injector rules. Uses selectors
	 * .ctc-copy-btn--button and the same CSS variables as build_inline_styles().
	 * This is the Global Injector counterpart to get_base_css() (which targets .ctc-copy-button for shortcode/block).
	 *
	 * @return string Unminified CSS.
	 */
	public static function get_global_injector_css() {
		return '
			.ctc-copy-btn--button {
				padding: var(--ctc-padding-y, 8px) var(--ctc-padding-x, 16px);
				background: var(--ctc-bg, #4f46e5);
				color: var(--ctc-text-color, #fff);
				border-radius: var(--ctc-border-radius, 6px);
				font-size: var(--ctc-font-size, 13px);
			}
			.ctc-copy-btn--button:hover {
				background: var(--ctc-hover-bg, #4338ca);
				filter: brightness(1.05);
			}
			.ctc-copy-btn__icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				flex-shrink: 0;
			}
			.ctc-copy-btn__icon svg {
				width: 14px;
				height: 14px;
			}
			.ctc-copy-btn__text {
				white-space: nowrap;
			}
		';
	}

	/**
	 * Get data to localize for JavaScript.
	 *
	 * This includes everything JS needs to render the button preview.
	 *
	 * @return array Data for wp_localize_script.
	 */
	public static function get_localize_data() {
		return [
			'defaultConfig' => self::get_default_config(),
			'icons'         => self::get_icons(),
			'successIcon'   => self::get_success_icon(),
			'baseCSS'       => self::get_base_css(),
		];
	}
}
