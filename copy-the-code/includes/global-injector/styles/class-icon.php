<?php
/**
 * Icon Style for Global Injector
 *
 * Defines the JSON schema, icons, and rendering logic for the Icon visual style.
 * This class serves as the single source of truth for both PHP (frontend) and JS (admin preview).
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector\Styles;

/**
 * Icon Style Class
 */
class Icon {

	/**
	 * Get the default configuration for icon style.
	 *
	 * This is the single source of truth for the icon JSON schema.
	 * Used by both PHP renderer and localized to JS for admin preview.
	 *
	 * @return array Default icon configuration.
	 */
	public static function get_default_config() {
		return [
			'text'  => [
				'tooltip_text' => 'Copy to clipboard',
				'success_text' => 'Copied!',
			],
			'icon'  => [
				'icon_key'   => 'clipboard',
				'custom_url' => null, // Pro feature: custom icon URL.
			],
			'style' => [
				'icon_color'       => '#6b7280',
				'icon_hover_color' => '#4f46e5',
				'bg_color'         => '#ffffff',
				'border_color'     => '#e5e7eb',
				'icon_size'        => '16px',
				'padding'          => '8px',
				'border_radius'    => '8px',
				'border_width'     => '1px',
			],
		];
	}

	/**
	 * Get available icons for icon style.
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
	 * Generate a unique icon ID.
	 *
	 * @return string Unique icon ID.
	 */
	public static function generate_icon_id() {
		return 'ctc-copy-icon-' . wp_generate_password( 8, false, false );
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

		// Fallback to clipboard icon.
		return $icons['clipboard']['svg'];
	}

	/**
	 * Build inline CSS variables from style config.
	 *
	 * @param array $style Style configuration.
	 * @return string Inline style attribute value.
	 */
	public static function build_inline_styles( $style ) {
		$css_vars = [
			'--ctc-icon-color'        => $style['icon_color'],
			'--ctc-icon-hover-color'  => $style['icon_hover_color'],
			'--ctc-icon-bg'           => $style['bg_color'],
			'--ctc-icon-border'       => $style['border_color'],
			'--ctc-icon-size'         => $style['icon_size'],
			'--ctc-icon-padding'      => $style['padding'],
			'--ctc-icon-radius'       => $style['border_radius'],
			'--ctc-icon-border-width' => $style['border_width'],
		];

		// Map CSS var names to property keys for unit detection.
		$var_to_prop = [
			'--ctc-icon-size'         => 'icon_size',
			'--ctc-icon-padding'      => 'padding',
			'--ctc-icon-radius'       => 'border_radius',
			'--ctc-icon-border-width' => 'border_width',
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
	 * Render the icon button HTML.
	 *
	 * This is the main render function used on the frontend.
	 * The same logic should be replicated in JS for admin preview.
	 *
	 * @param array  $config  Icon configuration (merged with defaults).
	 * @param string $icon_id Optional. Unique icon ID. Auto-generated if not provided.
	 * @return string Icon button HTML markup.
	 */
	public static function render( $config = [], $icon_id = null ) {
		// Merge with defaults.
		$config = self::merge_config( $config );

		// Generate icon ID if not provided.
		if ( empty( $icon_id ) ) {
			$icon_id = self::generate_icon_id();
		}

		// Get icon SVG.
		$icon_svg = '';
		if ( ! empty( $config['icon']['custom_url'] ) ) {
			// Pro feature: custom icon.
			$icon_svg = '<img src="' . esc_url( $config['icon']['custom_url'] ) . '" alt="" class="ctc-icon-img" />';
		} else {
			$icon_svg = '<span class="ctc-icon-svg">' . self::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';
		}

		// Build inline styles.
		$inline_styles = self::build_inline_styles( $config['style'] );

		// Build the icon button HTML.
		$html = sprintf(
			'<button id="%s" class="ctc-copy-icon" style="%s" data-tooltip="%s" data-success-text="%s" aria-label="%s">%s</button>',
			esc_attr( $icon_id ),
			esc_attr( $inline_styles ),
			esc_attr( $config['text']['tooltip_text'] ),
			esc_attr( $config['text']['success_text'] ),
			esc_attr( $config['text']['tooltip_text'] ),
			$icon_svg
		);

		return $html;
	}

	/**
	 * Get base CSS for icon style.
	 *
	 * This CSS uses CSS variables that are set inline on each icon button.
	 * These are the "hidden" styles that users don't customize via UI.
	 *
	 * @return string CSS styles.
	 */
	public static function get_base_css() {
		return '
			.ctc-copy-icon {
				/* User-customizable via CSS variables */
				color: var(--ctc-icon-color, #6b7280);
				background-color: var(--ctc-icon-bg, #ffffff);
				border-color: var(--ctc-icon-border, #e5e7eb);
				border-width: var(--ctc-icon-border-width, 1px);
				padding: var(--ctc-icon-padding, 8px);
				border-radius: var(--ctc-icon-radius, 8px);

				/* Hidden CSS - always applied, not customizable via UI */
				display: inline-flex;
				align-items: center;
				justify-content: center;
				border-style: solid;
				cursor: pointer;
				transition: all 0.2s ease;
				position: absolute;
				top: 8px;
				right: 8px;
				z-index: 10;
				box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
			}

			.ctc-copy-icon:hover {
				color: var(--ctc-icon-hover-color, #4f46e5);
				background-color: var(--ctc-icon-bg, #ffffff);
			}

			.ctc-copy-icon:focus {
				outline: 2px solid var(--ctc-icon-hover-color, #4f46e5);
				outline-offset: 2px;
			}

			.ctc-copy-icon .ctc-icon-svg {
				display: inline-flex;
				align-items: center;
				justify-content: center;
			}

			.ctc-copy-icon .ctc-icon-svg svg {
				width: var(--ctc-icon-size, 16px);
				height: var(--ctc-icon-size, 16px);
			}

			.ctc-copy-icon .ctc-icon-img {
				width: var(--ctc-icon-size, 16px);
				height: var(--ctc-icon-size, 16px);
				object-fit: contain;
			}

			/* Success state */
			.ctc-copy-icon.ctc-copied {
				color: #059669;
				border-color: #a7f3d0;
				background-color: #ecfdf5;
			}

			/* Tooltip */
			.ctc-copy-icon[data-tooltip]::before {
				content: attr(data-tooltip);
				position: absolute;
				top: 50%;
				right: calc(100% + 8px);
				transform: translateY(-50%);
				padding: 4px 8px;
				background: #1e293b;
				color: white;
				font-size: 11px;
				font-weight: 500;
				border-radius: 4px;
				white-space: nowrap;
				opacity: 0;
				visibility: hidden;
				transition: opacity 0.2s ease, visibility 0.2s ease;
				pointer-events: none;
			}

			.ctc-copy-icon:hover[data-tooltip]::before,
			.ctc-copy-icon:focus[data-tooltip]::before {
				opacity: 1;
				visibility: visible;
			}
		';
	}

	/**
	 * Get stylesheet CSS for Global Injector (frontend).
	 *
	 * Used when outputting inline CSS for Global Injector rules. Uses selector
	 * .ctc-copy-btn--icon and CSS variables (same as build_inline_styles mapping for Global Injector).
	 * This is the Global Injector counterpart to get_base_css() (which targets .ctc-copy-icon for shortcode/block).
	 *
	 * @return string Unminified CSS.
	 */
	public static function get_global_injector_css() {
		return '
			.ctc-copy-btn--icon {
				position: absolute;
				z-index: 10;
				padding: var(--ctc-padding, 8px);
				background: var(--ctc-bg, transparent);
				color: var(--ctc-icon-color, #6b7280);
				border: var(--ctc-icon-stroke, 1px) solid var(--ctc-border-color, #e5e7eb);
				border-radius: var(--ctc-border-radius, 6px);
			}
			.ctc-copy-btn--icon:hover {
				color: var(--ctc-icon-hover-color, #4f46e5);
				border-color: var(--ctc-icon-hover-color, #4f46e5);
			}
		';
	}

	/**
	 * Get data to localize for JavaScript.
	 *
	 * This includes everything JS needs to render the icon preview.
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
