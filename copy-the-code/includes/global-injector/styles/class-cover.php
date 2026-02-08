<?php
/**
 * Cover Style for Global Injector
 *
 * Defines the JSON schema, icons, and rendering logic for the Cover visual style.
 * Cover style shows content as-is by default, and on hover displays a copy button
 * with an overlay blur effect.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector\Styles;

/**
 * Cover Style Class
 */
class Cover {

	/**
	 * Get the default configuration for cover style.
	 *
	 * This is the single source of truth for the cover JSON schema.
	 * Used by both PHP renderer and localized to JS for admin preview.
	 *
	 * @return array Default cover configuration.
	 */
	public static function get_default_config() {
		return [
			'text'    => [
				'button_text'  => 'Copy',
				'success_text' => 'Copied!',
			],
			'icon'    => [
				'enabled'    => true,
				'position'   => 'left',
				'icon_key'   => 'clipboard',
				'custom_url' => null,
			],
			'overlay' => [
				'overlay_color'       => '#0f172a',
				'hover_overlay_color' => '#4f46e5',
				'overlay_opacity'     => 20,
				'blur'                => 2,
			],
			'button'  => [
				'text_color'     => '#0f172a',
				'bg_color'       => '#ffffff',
				'hover_bg_color' => '#f1f5f9',
				'border_radius'  => 9999,
				'font_size'      => 10,
				'padding_x'      => 12,
				'padding_y'      => 6,
			],
		];
	}

	/**
	 * Get available icons for cover style.
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
			'eye'       => [
				'label' => 'Eye',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
			],
			'click'     => [
				'label' => 'Click',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>',
			],
			'lock'      => [
				'label' => 'Lock',
				'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
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
	 * Generate a unique cover ID.
	 *
	 * @return string Unique cover ID.
	 */
	public static function generate_cover_id() {
		return 'ctc-cover-' . wp_generate_password( 8, false, false );
	}

	/**
	 * Merge user config with defaults.
	 *
	 * @param array $user_config User-provided configuration.
	 * @return array Merged configuration.
	 */
	public static function merge_config( $user_config = [] ) {
		$defaults = self::get_default_config();

		// Deep merge.
		$merged = $defaults;

		if ( isset( $user_config['text'] ) && is_array( $user_config['text'] ) ) {
			$merged['text'] = array_merge( $defaults['text'], $user_config['text'] );
		}

		if ( isset( $user_config['icon'] ) && is_array( $user_config['icon'] ) ) {
			$merged['icon'] = array_merge( $defaults['icon'], $user_config['icon'] );
		}

		if ( isset( $user_config['overlay'] ) && is_array( $user_config['overlay'] ) ) {
			$merged['overlay'] = array_merge( $defaults['overlay'], $user_config['overlay'] );
		}

		if ( isset( $user_config['button'] ) && is_array( $user_config['button'] ) ) {
			$merged['button'] = array_merge( $defaults['button'], $user_config['button'] );
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

		// Fallback to clipboard icon.
		return $icons['clipboard']['svg'];
	}

	/**
	 * Convert hex color to rgba.
	 *
	 * @param string $hex     Hex color (e.g., '#0f172a').
	 * @param float  $opacity Opacity (0-1).
	 * @return string RGBA color string.
	 */
	public static function hex_to_rgba( $hex, $opacity = 1 ) {
		// Remove # if present.
		$hex = ltrim( $hex, '#' );

		// Parse hex to RGB.
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		return "rgba({$r}, {$g}, {$b}, {$opacity})";
	}

	/**
	 * Build inline CSS variables from config.
	 *
	 * @param array $config Cover configuration.
	 * @return string Inline style attribute value.
	 */
	public static function build_inline_styles( $config ) {
		$overlay = $config['overlay'];
		$button  = $config['button'];

		// Calculate overlay opacity as decimal.
		$overlay_opacity = isset( $overlay['overlay_opacity'] ) ? $overlay['overlay_opacity'] / 100 : 0.2;
		$hover_opacity   = min( $overlay_opacity + 0.1, 1 );

		$css_vars = [
			// Overlay styles.
			'--ctc-cover-overlay'       => self::hex_to_rgba( $overlay['overlay_color'], $overlay_opacity ),
			'--ctc-cover-hover-overlay' => self::hex_to_rgba( $overlay['hover_overlay_color'], $hover_opacity ),
			'--ctc-cover-blur'          => ( isset( $overlay['blur'] ) ? $overlay['blur'] : 2 ) . 'px',
			// Button styles.
			'--ctc-cover-btn-text'      => $button['text_color'],
			'--ctc-cover-btn-bg'        => $button['bg_color'],
			'--ctc-cover-btn-hover-bg'  => isset( $button['hover_bg_color'] ) ? $button['hover_bg_color'] : '#f1f5f9',
			'--ctc-cover-btn-radius'    => $button['border_radius'],
			'--ctc-cover-btn-font-size' => $button['font_size'],
			'--ctc-cover-btn-padding-x' => $button['padding_x'],
			'--ctc-cover-btn-padding-y' => $button['padding_y'],
		];

		// Properties that need 'px' unit.
		$px_props = [
			'--ctc-cover-btn-radius',
			'--ctc-cover-btn-font-size',
			'--ctc-cover-btn-padding-x',
			'--ctc-cover-btn-padding-y',
		];

		$inline = [];
		foreach ( $css_vars as $var => $value ) {
			// Add 'px' unit for numeric properties.
			if ( in_array( $var, $px_props, true ) && is_numeric( $value ) ) {
				$value = $value . 'px';
			}
			$inline[] = esc_attr( $var ) . ': ' . esc_attr( $value );
		}

		return implode( '; ', $inline );
	}

	/**
	 * Render the cover HTML.
	 *
	 * This renders the overlay element that wraps the target content.
	 * The actual copy functionality is handled by JS.
	 *
	 * @param array  $config   Cover configuration (merged with defaults).
	 * @param string $cover_id Optional. Unique cover ID. Auto-generated if not provided.
	 * @return string Cover HTML markup.
	 */
	public static function render( $config = [], $cover_id = null ) {
		// Merge with defaults.
		$config = self::merge_config( $config );

		// Generate cover ID if not provided.
		if ( empty( $cover_id ) ) {
			$cover_id = self::generate_cover_id();
		}

		// Get icon SVG.
		$icon_svg = '';
		if ( $config['icon']['enabled'] ) {
			if ( ! empty( $config['icon']['custom_url'] ) ) {
				// Pro feature: custom icon.
				$icon_svg = '<img src="' . esc_url( $config['icon']['custom_url'] ) . '" alt="" class="ctc-cover-icon" />';
			} else {
				$icon_svg = '<span class="ctc-cover-icon">' . self::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';
			}
		}

		// Build inline styles.
		$inline_styles = self::build_inline_styles( $config );

		// Build button content based on icon position.
		$button_content = '';
		if ( 'left' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}
		$button_content .= '<span class="ctc-cover-text">' . esc_html( $config['text']['button_text'] ) . '</span>';
		if ( 'right' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}

		// Build the cover overlay HTML.
		$html = sprintf(
			'<div id="%s" class="ctc-cover-overlay" style="%s" data-success-text="%s" data-original-text="%s">
				<button type="button" class="ctc-cover-button" aria-label="%s">%s</button>
			</div>',
			esc_attr( $cover_id ),
			esc_attr( $inline_styles ),
			esc_attr( $config['text']['success_text'] ),
			esc_attr( $config['text']['button_text'] ),
			esc_attr( $config['text']['button_text'] ),
			$button_content
		);

		return $html;
	}

	/**
	 * Get base CSS for cover style.
	 *
	 * This CSS uses CSS variables that are set inline on each cover element.
	 *
	 * @return string CSS styles.
	 */
	public static function get_base_css() {
		return '
			/* Cover overlay - hidden by default, visible on hover */
			.ctc-cover-overlay {
				position: absolute;
				inset: 0;
				z-index: 20;
				border-radius: inherit;
				transition: all 0.3s ease;
				cursor: pointer;

				/* Hidden by default */
				opacity: 0;
				background: var(--ctc-cover-overlay, rgba(15, 23, 42, 0.2));
				backdrop-filter: blur(0px);
				-webkit-backdrop-filter: blur(0px);
			}

			/* Show overlay on hover */
			.ctc-cover-overlay:hover {
				opacity: 1;
				background: var(--ctc-cover-hover-overlay, rgba(79, 70, 229, 0.3));
				backdrop-filter: blur(var(--ctc-cover-blur, 2px));
				-webkit-backdrop-filter: blur(var(--ctc-cover-blur, 2px));
			}

			/* Copy button - centered, appears with overlay on hover */
			.ctc-cover-button {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%) scale(0.9);
				opacity: 0;
				transition: all 0.2s ease;

				/* Button styles from CSS variables */
				color: var(--ctc-cover-btn-text, #0f172a);
				background: var(--ctc-cover-btn-bg, rgba(255, 255, 255, 0.95));
				font-size: var(--ctc-cover-btn-font-size, 10px);
				font-weight: 700;
				padding: var(--ctc-cover-btn-padding-y, 6px) var(--ctc-cover-btn-padding-x, 12px);
				border-radius: var(--ctc-cover-btn-radius, 9999px);

				/* Fixed styles */
				display: inline-flex;
				align-items: center;
				gap: 6px;
				border: 1px solid rgba(255, 255, 255, 0.5);
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
				white-space: nowrap;
				cursor: pointer;
			}

			/* Show button when overlay is hovered */
			.ctc-cover-overlay:hover .ctc-cover-button {
				opacity: 1;
				transform: translate(-50%, -50%) scale(1);
			}

			.ctc-cover-button:hover {
				background: var(--ctc-cover-btn-hover-bg, #f1f5f9);
				transform: translate(-50%, -50%) scale(1.05);
			}

			.ctc-cover-button:active {
				transform: translate(-50%, -50%) scale(0.95);
			}

			/* Icon styles */
			.ctc-cover-icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
			}

			.ctc-cover-icon svg {
				width: 12px;
				height: 12px;
			}

			.ctc-cover-icon img {
				width: 12px;
				height: 12px;
				object-fit: contain;
			}

			.ctc-cover-text {
				white-space: nowrap;
			}

			/* Success state */
			.ctc-cover-overlay.ctc-copied .ctc-cover-button {
				background: #059669;
				color: #ffffff;
				border-color: #059669;
			}

			/* Ensure parent has relative positioning */
			.ctc-cover-wrapper {
				position: relative;
			}

			/* Disable text selection on covered content */
			.ctc-cover-wrapper.ctc-covered {
				user-select: none;
			}
		';
	}

	/**
	 * Get CSS for Global Injector inline output.
	 *
	 * Used by Inline_CSS::get_chunks(). Cover style uses the same selectors and
	 * CSS variables as get_base_css() for both shortcode/block and Global Injector.
	 *
	 * @since 5.0.2
	 * @return string Unminified CSS.
	 */
	public static function get_global_injector_css() {
		return self::get_base_css();
	}

	/**
	 * Get data to localize for JavaScript.
	 *
	 * This includes everything JS needs to render the cover preview.
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
