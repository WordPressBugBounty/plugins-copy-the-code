<?php
/**
 * Global Injector Inline CSS
 *
 * Single source for Global Injector frontend CSS. Output is built from chunks
 * (base, button, icon, cover, shared, positions, print) and minified at output time.
 *
 * @package CTC
 * @since 5.0.2
 */

namespace CTC\Global_Injector;

use CTC\Global_Injector\Styles\Button;
use CTC\Global_Injector\Styles\Cover;
use CTC\Global_Injector\Styles\Icon;

/**
 * Inline_CSS
 *
 * Assembles Global Injector frontend CSS from style classes (Button, Icon, Cover)
 * and layout chunks (base, shared, positions, print). Source is unminified; minification at output.
 *
 * @since 5.0.2
 */
class Inline_CSS {

	/**
	 * Build full CSS string for the given needed styles and positions (unminified).
	 *
	 * @since 5.0.2
	 * @param array $needed_styles   Optional. List of 'button', 'icon', 'cover'. Default all.
	 * @param array $needed_positions Optional. List of position keys. Default all inside + outside if any outside.
	 * @return string Unminified CSS.
	 */
	public static function build( array $needed_styles = [], array $needed_positions = [] ) {
		$chunks        = self::get_chunks();
		$use_styles    = ! empty( $needed_styles ) ? $needed_styles : [ 'button', 'icon', 'cover' ];
		$use_positions = ! empty( $needed_positions ) ? $needed_positions : array_keys( $chunks['positions'] );

		$css = $chunks['base'];

		if ( in_array( 'button', $use_styles, true ) ) {
			$css .= $chunks['button'];
		}
		if ( in_array( 'icon', $use_styles, true ) ) {
			$css .= $chunks['icon'];
		}
		if ( in_array( 'cover', $use_styles, true ) ) {
			$css .= $chunks['cover'];
		}
		if ( in_array( 'button', $use_styles, true ) || in_array( 'icon', $use_styles, true ) ) {
			$css .= $chunks['shared'];
		}

		$has_outside = false;
		foreach ( $use_positions as $p ) {
			if ( strpos( (string) $p, 'outside' ) === 0 ) {
				$has_outside = true;
				break;
			}
		}
		if ( $has_outside && isset( $chunks['positions']['outside'] ) ) {
			$css .= $chunks['positions']['outside'];
		}
		foreach ( $use_positions as $pos ) {
			if ( isset( $chunks['positions'][ $pos ] ) ) {
				$css .= $chunks['positions'][ $pos ];
			}
		}

		$css .= $chunks['print'];
		return $css;
	}

	/**
	 * Get raw CSS chunks (unminified). Keys: base, button, icon, cover, shared, positions, print.
	 * Button, icon, cover come from style classes; base, shared, positions, print are layout/structure.
	 *
	 * @since 5.0.2
	 * @return array<string, string|array<string, string>>
	 */
	public static function get_chunks() {
		$positions = self::get_positions_chunks();
		return [
			'base'      => self::get_base(),
			'button'    => Button::get_global_injector_css(),
			'icon'      => Icon::get_global_injector_css(),
			'cover'     => Cover::get_global_injector_css(),
			'shared'    => self::get_shared(),
			'positions' => $positions,
			'print'     => self::get_print(),
		];
	}

	/**
	 * Base layout and copy button reset (unminified).
	 *
	 * @return string
	 */
	private static function get_base() {
		return '/* CTC Global Injector */
			.ctc-wrapper {
				position: relative;
			}
			.ctc-copy-btn {
				display: inline-flex;
				align-items: center;
				gap: 6px;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
				font-weight: var(--ctc-font-weight, 500);
				line-height: 1.4;
				cursor: pointer;
				border: none;
				outline: none;
				transition: all 0.15s ease;
			}
			.ctc-copy-btn:focus {
				outline: 2px solid var(--ctc-bg, #4f46e5);
				outline-offset: 2px;
			}
			.ctc-copy-btn:active {
				transform: scale(0.97);
			}
		';
	}

	/**
	 * Shared (button + icon) states and icon/copied (unminified).
	 *
	 * @return string
	 */
	private static function get_shared() {
		return '
			.ctc-copy-btn__icon {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				flex-shrink: 0;
			}
			.ctc-copy-btn__icon svg {
				display: block;
				fill: transparent;
				width: 14px;
				height: 14px;
			}
			.ctc-copy-btn--copied svg {
				fill: currentColor;
			}
			.ctc-copy-btn__text {
				white-space: nowrap;
			}
			.ctc-copy-btn--copied {
				animation: ctc-pulse 0.3s ease;
			}
			.ctc-copy-btn--error {
				opacity: 0.7;
			}
			@keyframes ctc-pulse {
				0%, 100% { transform: scale(1); }
				50% { transform: scale(1.05); }
			}
		';
	}

	/**
	 * Position chunks: inside_* and outside (unminified).
	 *
	 * @return array<string, string>
	 */
	private static function get_positions_chunks() {
		$all_inside = [ 'inside_top_left', 'inside_top_right', 'inside_bottom_left', 'inside_bottom_right' ];
		$positions  = [];
		foreach ( $all_inside as $p ) {
			$sel             = '.ctc-wrapper--' . $p . ' .ctc-copy-btn--button,.ctc-wrapper--' . $p . ' .ctc-copy-btn--icon';
			$parts           = explode( '_', str_replace( 'inside_', '', $p ), 2 );
			$vert            = $parts[0] ?? 'top';
			$horz            = $parts[1] ?? 'right';
			$top             = ( $vert === 'top' ) ? '8px' : 'auto';
			$bottom          = ( $vert === 'bottom' ) ? '8px' : 'auto';
			$left            = ( $horz === 'left' ) ? '8px' : 'auto';
			$right           = ( $horz === 'right' ) ? '8px' : 'auto';
			$positions[ $p ] = $sel . ' {
				position: absolute;
				top: ' . $top . ';
				right: ' . $right . ';
				bottom: ' . $bottom . ';
				left: ' . $left . ';
			}
			';
		}
		$positions['outside'] = '
			.ctc-outside {
				display: flex;
				margin: 4px 0;
			}
			.ctc-outside--outside_before_left,
			.ctc-outside--outside_after_left {
				justify-content: flex-start;
			}
			.ctc-outside--outside_before_right,
			.ctc-outside--outside_after_right {
				justify-content: flex-end;
			}
			.ctc-wrapper--outside_before_left .ctc-copy-btn,
			.ctc-wrapper--outside_before_right .ctc-copy-btn,
			.ctc-wrapper--outside_after_left .ctc-copy-btn,
			.ctc-wrapper--outside_after_right .ctc-copy-btn {
				position: relative;
				top: auto;
				right: auto;
				bottom: auto;
				left: auto;
			}
		';
		return $positions;
	}

	/**
	 * Print hide (unminified).
	 *
	 * @return string
	 */
	private static function get_print() {
		return '
			@media print {
				.ctc-copy-btn,
				.ctc-cover-overlay {
					display: none !important;
				}
			}
		';
	}
}
