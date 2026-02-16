<?php
/**
 * Unified Shortcode Handler
 *
 * Provides `[copy]` shortcode with design presets (button, icon, inline, cover).
 * Also provides backward-compatible `[copy_inline]` shortcode.
 *
 * Reuses the same styles and JS from Global Injector for consistency.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC;

use CTC\Global_Injector\Styles\Button;
use CTC\Global_Injector\Styles\Icon;
use CTC\Global_Injector\Styles\Cover;

/**
 * Shortcode Class
 *
 * Single source of truth for all copy shortcodes.
 */
class Shortcode {

	/**
	 * Instance
	 *
	 * @var Shortcode|null
	 */
	private static $instance = null;

	/**
	 * Track if styles have been enqueued.
	 *
	 * @var bool
	 */
	private $styles_enqueued = false;

	/**
	 * Track which presets were used on the page (for conditional CSS).
	 *
	 * @var array<string, true>
	 */
	private $used_presets = [];

	/**
	 * Get instance.
	 *
	 * @return Shortcode
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
		// Register shortcodes.
		add_shortcode( 'copy', [ $this, 'render_copy_shortcode' ] );
		add_shortcode( 'copy_inline', [ $this, 'render_copy_inline_shortcode' ] );
		add_shortcode( 'ctc', [ $this, 'render_copy_shortcode' ] );

		// Enqueue scripts/styles when shortcode is used.
		add_action( 'wp_footer', [ $this, 'maybe_enqueue_assets' ], 5 );
	}

	/**
	 * Get default attributes for [copy] shortcode.
	 *
	 * @return array Default attributes.
	 */
	public static function get_default_atts() {
		return [
			// Content.
			'text'          => '',          // Content to copy (or use content between tags).
			'display'       => '',          // What to display (default: same as text).
			'target'        => '',          // CSS selector to copy from (advanced).
			'copy-as'       => 'text',      // 'text' or 'html' (legacy).
			'copy_as'       => '',          // Copy as: text, html, text_and_html, image, json, svg.

			// Preset/Style.
			'preset'        => 'inline',    // 'button', 'icon', 'inline', 'cover'.

			// Text.
			'button-text'   => '',          // Button text (overrides preset default).
			'success-text'  => '',          // Success message.
			'tooltip'       => '',          // Tooltip text.

			// Icon.
			'icon'          => '',          // Icon key: clipboard, copy, link, etc.
			'icon-position' => '',         // 'left' or 'right'.
			'show-icon'     => '',          // 'yes' or 'no'.

			// Colors (overrides preset).
			'color'         => '',          // Text color.
			'bg'            => '',          // Background color.
			'hover-bg'      => '',          // Hover background color.
			'icon-color'    => '',          // Icon color (for icon preset).

			// Layout.
			'class'         => '',          // Additional CSS class.
			'id'            => '',          // Custom ID.

			// Redirect: URL to open after copy (e.g. store page).
			'redirect'      => '',          // Preferred: URL to open after copying.
			'link'          => '',          // Backward compatibility: same as redirect.

			// Legacy attributes (backward compatibility).
			'copied-text'   => '',          // Maps to success-text.
			'style'         => '',          // Maps to preset.
			'tag'           => '',          // Legacy: HTML tag.
			'title'         => '',          // Legacy: Tooltip.
			'content'       => '',          // Legacy: Content to copy.
			'hidden'        => '',          // Legacy: Hide display text.
		];
	}

	/**
	 * Render [copy] shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string HTML output.
	 */
	public function render_copy_shortcode( $atts = [], $content = '' ) {
		$atts = shortcode_atts( self::get_default_atts(), $atts, 'copy' );

		// Normalize legacy attributes.
		$atts = $this->normalize_legacy_atts( $atts, $content );

		/**
		 * Filter shortcode attributes before rendering.
		 *
		 * @since 5.0.0
		 *
		 * @param array  $atts    Normalized shortcode attributes.
		 * @param string $content Shortcode content.
		 */
		$atts = apply_filters( 'ctc/shortcode/atts', $atts, $content );

		// Mark that we need to enqueue assets.
		$this->styles_enqueued = true;

		// Track preset for conditional CSS (only load styles for presets used).
		$preset                        = $atts['preset'] ?? 'inline';
		$this->used_presets[ $preset ] = true;

		// Render based on preset.
		switch ( $preset ) {
			case 'button':
				return $this->render_button_preset( $atts );

			case 'icon':
				return $this->render_icon_preset( $atts );

			case 'cover':
				return $this->render_cover_preset( $atts );

			case 'inline':
			default:
				return $this->render_inline_preset( $atts );
		}
	}

	/**
	 * Render [copy_inline] shortcode (backward compatible).
	 *
	 * Maps to [copy preset="inline"].
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return string HTML output.
	 */
	public function render_copy_inline_shortcode( $atts = [], $content = '' ) {
		// Map copy_inline attributes to copy attributes.
		$mapped_atts = [
			'preset'       => 'inline',
			'text'         => isset( $atts['text'] ) ? $atts['text'] : $content,
			'display'      => isset( $atts['display'] ) ? $atts['display'] : '',
			'success-text' => isset( $atts['tooltip'] ) ? $atts['tooltip'] : __( 'Copied', 'ctc' ),
			'class'        => isset( $atts['style'] ) ? 'ctc-inline-style-' . $atts['style'] : '',
			'hidden'       => isset( $atts['hidden'] ) ? $atts['hidden'] : '',
		];

		return $this->render_copy_shortcode( $mapped_atts, $content );
	}

	/**
	 * Normalize legacy attributes to new format.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return array Normalized attributes.
	 */
	private function normalize_legacy_atts( $atts, $content ) {
		// Decode HTML entities in content so [ ] work (e.g. &#91; &#93;).
		if ( ! empty( $atts['content'] ) ) {
			$atts['content'] = html_entity_decode( $atts['content'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		// Legacy support: 'content' attribute contains what to copy.
		// 'text' attribute is the display text in legacy usage.
		// Example: [copy text="Location" content="/wp [...]"] - displays "Location", copies "/wp [...]"
		if ( ! empty( $atts['content'] ) ) {
			// Legacy mode: content = what to copy, text = what to display.
			$copy_text    = $atts['content'];
			$display_text = ! empty( $atts['text'] ) ? $atts['text'] : $atts['content'];

			$atts['text']    = $copy_text;
			$atts['display'] = $display_text;
		} elseif ( empty( $atts['text'] ) && ! empty( $content ) ) {
			// Modern mode: text = both copy and display (unless display is set).
			$atts['text'] = $content;
		}

		// Decode HTML entities in text (supports &#91; for [ and &#93; for ] etc.).
		// This allows users to use HTML entities for special characters in shortcode attributes.
		if ( ! empty( $atts['text'] ) ) {
			$atts['text'] = html_entity_decode( $atts['text'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			// Fix WordPress wptexturize() converting -- to en-dash inside CSS var().
			$atts['text'] = $this->fix_var_texturize( $atts['text'] );
		}

		// Display text: prefer inner content when present, else default to copy text.
		$content_trimmed = isset( $content ) ? trim( (string) $content ) : '';
		if ( ! empty( $content_trimmed ) && empty( $atts['display'] ) ) {
			$atts['display'] = html_entity_decode( $content_trimmed, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		} elseif ( empty( $atts['display'] ) ) {
			$atts['display'] = $atts['text'];
		} else {
			$atts['display'] = html_entity_decode( $atts['display'], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		// Success text.
		if ( empty( $atts['success-text'] ) && ! empty( $atts['copied-text'] ) ) {
			$atts['success-text'] = $atts['copied-text'];
		}
		if ( empty( $atts['success-text'] ) ) {
			$atts['success-text'] = __( 'Copied!', 'ctc' );
		}

		// Tooltip.
		if ( empty( $atts['tooltip'] ) && ! empty( $atts['title'] ) ) {
			$atts['tooltip'] = $atts['title'];
		}
		if ( empty( $atts['tooltip'] ) ) {
			$atts['tooltip'] = __( 'Copy to clipboard', 'ctc' );
		}

		// Preset from legacy style.
		if ( empty( $atts['preset'] ) && ! empty( $atts['style'] ) ) {
			$style_map = [
				'icon'   => 'icon',
				'button' => 'button',
				'cover'  => 'cover',
			];
			if ( isset( $style_map[ $atts['style'] ] ) ) {
				$atts['preset'] = $style_map[ $atts['style'] ];
			}
		}

		// Button text.
		if ( empty( $atts['button-text'] ) ) {
			$atts['button-text'] = __( 'Copy', 'ctc' );
		}

		// Redirect URL: prefer redirect attribute; fall back to link for backward compatibility.
		if ( ! empty( $atts['redirect'] ) ) {
			$atts['link'] = $atts['redirect'];
		}

		return $atts;
	}

	/**
	 * Fix WordPress wptexturize() inside CSS var() functions.
	 *
	 * WordPress converts -- to en-dash (–) and --- to em-dash (—).
	 * This breaks CSS custom properties like var(--wp--preset--color--bg).
	 *
	 * This method only fixes content inside var() to avoid affecting
	 * legitimate typography like "2020–2025" or "He said—yes".
	 *
	 * @since 5.0.0
	 *
	 * @param string $text Text to process.
	 * @return string Processed text with var() contents fixed.
	 */
	private function fix_var_texturize( $text ) {
		if ( empty( $text ) || strpos( $text, 'var(' ) === false ) {
			return $text;
		}

		// Replace en-dash/em-dash with hyphens only inside var().
		return preg_replace_callback(
			'/var\s*\(([^)]+)\)/',
			function ( $matches ) {
				$inside = str_replace(
					[ '–', '—' ],    // En-dash, Em-dash (Unicode).
					[ '--', '---' ], // Double, Triple hyphen.
					$matches[1]
				);
				return 'var(' . $inside . ')';
			},
			$text
		);
	}

	/**
	 * Render inline preset.
	 *
	 * Displays text with a copy icon. Best for coupon codes, promo codes, etc.
	 *
	 * Supports:
	 * - tag="a" : Uses native anchor tag with theme styling (no custom CSS overrides).
	 * - show-icon="no" : Hides the copy icon.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	private function render_inline_preset( $atts ) {
		$id           = ! empty( $atts['id'] ) ? $atts['id'] : 'ctc-inline-' . wp_generate_password( 8, false, false );
		$hidden_class = 'yes' === $atts['hidden'] ? 'ctc-inline-hidden' : '';
		$custom_class = ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

		// Determine if using native anchor tag (inherits theme styling).
		$use_native_tag = 'a' === strtolower( $atts['tag'] );

		// Determine if icon should be shown (default: yes, unless show-icon="no").
		$show_icon = ! in_array( strtolower( $atts['show-icon'] ), [ 'no', 'false', '0' ], true );

		// Build CSS class based on tag type.
		if ( $use_native_tag ) {
			// Native mode: Use theme's anchor styling, minimal CTC styling.
			$this->used_presets['native'] = true;
			$css_class                    = 'ctc-shortcode ctc-shortcode--native' . $custom_class;
		} else {
			// Modern mode: Full CTC styling.
			$css_class = 'ctc-shortcode ctc-shortcode--inline' . $custom_class;
		}

		// Build inline styles (only for non-native mode).
		$inline_style = '';
		if ( ! $use_native_tag && ! empty( $atts['color'] ) ) {
			$inline_style .= '--ctc-inline-color: ' . esc_attr( $atts['color'] ) . ';';
		}

		// Determine HTML tag.
		$tag = $use_native_tag ? 'a' : 'span';

		// Copy as: prefer copy_as.
		$copy_as_value = ! empty( $atts['copy_as'] ) ? $atts['copy_as'] : '';

		// Build icon HTML.
		$icon_html = '';
		if ( $show_icon ) {
			$icon_key  = ! empty( $atts['icon'] ) ? $atts['icon'] : 'clipboard';
			$icon_html = '<span class="ctc-shortcode__icon" aria-hidden="true">' .
				wp_kses( $this->get_icon_svg( $icon_key ), $this->get_allowed_svg_tags() ) .
				'</span>';
		}

		ob_start();
		?>
		<<?php echo esc_attr( $tag ); ?> 
			id="<?php echo esc_attr( $id ); ?>"
			class="<?php echo esc_attr( $css_class ); ?>"
			data-ctc-copy="<?php echo esc_attr( $atts['text'] ); ?>"
			data-ctc-success="<?php echo esc_attr( $atts['success-text'] ); ?>"
			data-ctc-format="<?php echo esc_attr( $atts['copy-as'] ); ?>"
			<?php if ( ! empty( $copy_as_value ) ) : ?>
				data-ctc-copy-as="<?php echo esc_attr( $copy_as_value ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $atts['target'] ) ) : ?>
				data-ctc-target="<?php echo esc_attr( $atts['target'] ); ?>"
			<?php endif; ?>
			<?php if ( $inline_style ) : ?>
				style="<?php echo esc_attr( $inline_style ); ?>"
			<?php endif; ?>
			<?php if ( $use_native_tag ) : ?>
				href="javascript:void(0);"
				title="<?php echo esc_attr( $atts['tooltip'] ); ?>"
			<?php else : ?>
				role="button"
				tabindex="0"
				aria-label="<?php echo esc_attr( $atts['tooltip'] ); ?>"
			<?php endif; ?>
		>
			<span class="ctc-shortcode__text <?php echo esc_attr( $hidden_class ); ?>"><?php echo esc_html( $atts['display'] ); ?></span>
			<?php echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="ctc-shortcode__success" aria-live="polite"></span>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render button preset.
	 *
	 * Uses Button style class for consistent styling with Global Injector.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	private function render_button_preset( $atts ) {
		$id           = ! empty( $atts['id'] ) ? $atts['id'] : Button::generate_button_id();
		$custom_class = ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

		// Build config for Button style.
		$config = [
			'text'  => [
				'button_text'  => $atts['button-text'],
				'success_text' => $atts['success-text'],
			],
			'icon'  => [
				'enabled'  => 'no' !== $atts['show-icon'],
				'position' => ! empty( $atts['icon-position'] ) ? $atts['icon-position'] : 'left',
				'icon_key' => ! empty( $atts['icon'] ) ? $atts['icon'] : 'clipboard',
			],
			'style' => [],
		];

		// Add custom colors if provided.
		if ( ! empty( $atts['color'] ) ) {
			$config['style']['text_color'] = $atts['color'];
		}
		if ( ! empty( $atts['bg'] ) ) {
			$config['style']['background_color'] = $atts['bg'];
		}
		if ( ! empty( $atts['hover-bg'] ) ) {
			$config['style']['hover_background_color'] = $atts['hover-bg'];
		}

		// Merge with defaults.
		$config = Button::merge_config( $config );

		// Build inline styles.
		$inline_styles = Button::build_inline_styles( $config['style'] );

		// Get icon SVG.
		$icon_svg = '';
		if ( $config['icon']['enabled'] ) {
			$icon_svg = '<span class="ctc-shortcode__icon">' . Button::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';
		}

		// Build content based on icon position.
		$button_content = '';
		if ( 'left' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}
		$button_content .= '<span class="ctc-shortcode__text">' . esc_html( $config['text']['button_text'] ) . '</span>';
		if ( 'right' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}

		// Copy as: prefer copy_as.
		$copy_as_value = ! empty( $atts['copy_as'] ) ? $atts['copy_as'] : '';

		ob_start();
		?>
		<button
			type="button"
			id="<?php echo esc_attr( $id ); ?>"
			class="ctc-shortcode ctc-shortcode--button<?php echo esc_attr( $custom_class ); ?>"
			style="<?php echo esc_attr( $inline_styles ); ?>"
			data-ctc-copy="<?php echo esc_attr( $atts['text'] ); ?>"
			data-ctc-success="<?php echo esc_attr( $config['text']['success_text'] ); ?>"
			data-ctc-original="<?php echo esc_attr( $config['text']['button_text'] ); ?>"
			data-ctc-format="<?php echo esc_attr( $atts['copy-as'] ); ?>"
			<?php if ( ! empty( $copy_as_value ) ) : ?>
				data-ctc-copy-as="<?php echo esc_attr( $copy_as_value ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $atts['target'] ) ) : ?>
				data-ctc-target="<?php echo esc_attr( $atts['target'] ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $atts['link'] ) ) : ?>
				data-ctc-link="<?php echo esc_attr( $atts['link'] ); ?>"
			<?php endif; ?>
			aria-label="<?php echo esc_attr( $atts['tooltip'] ); ?>"
		>
			<?php echo $button_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render icon preset.
	 *
	 * Uses Icon style class for consistent styling with Global Injector.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	private function render_icon_preset( $atts ) {
		$id           = ! empty( $atts['id'] ) ? $atts['id'] : Icon::generate_icon_id();
		$custom_class = ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

		// Build config for Icon style.
		$config = [
			'text'  => [
				'tooltip_text' => $atts['tooltip'],
				'success_text' => $atts['success-text'],
			],
			'icon'  => [
				'icon_key' => ! empty( $atts['icon'] ) ? $atts['icon'] : 'clipboard',
			],
			'style' => [],
		];

		// Add custom colors if provided.
		if ( ! empty( $atts['icon-color'] ) ) {
			$config['style']['icon_color'] = $atts['icon-color'];
		}
		if ( ! empty( $atts['bg'] ) ) {
			$config['style']['bg_color'] = $atts['bg'];
		}

		// Merge with defaults.
		$config = Icon::merge_config( $config );

		// Build inline styles.
		$inline_styles = Icon::build_inline_styles( $config['style'] );

		// Get icon SVG.
		$icon_svg = '<span class="ctc-shortcode__icon">' . Icon::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';

		// Copy as: prefer copy_as.
		$copy_as_value = ! empty( $atts['copy_as'] ) ? $atts['copy_as'] : '';

		ob_start();
		?>
		<button
			type="button"
			id="<?php echo esc_attr( $id ); ?>"
			class="ctc-shortcode ctc-shortcode--icon<?php echo esc_attr( $custom_class ); ?>"
			style="<?php echo esc_attr( $inline_styles ); ?>"
			data-ctc-copy="<?php echo esc_attr( $atts['text'] ); ?>"
			data-ctc-success="<?php echo esc_attr( $config['text']['success_text'] ); ?>"
			data-ctc-tooltip="<?php echo esc_attr( $config['text']['tooltip_text'] ); ?>"
			data-ctc-format="<?php echo esc_attr( $atts['copy-as'] ); ?>"
			<?php if ( ! empty( $copy_as_value ) ) : ?>
				data-ctc-copy-as="<?php echo esc_attr( $copy_as_value ); ?>"
			<?php endif; ?>
			<?php if ( ! empty( $atts['target'] ) ) : ?>
				data-ctc-target="<?php echo esc_attr( $atts['target'] ); ?>"
			<?php endif; ?>
			aria-label="<?php echo esc_attr( $config['text']['tooltip_text'] ); ?>"
		>
			<?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render cover preset.
	 *
	 * Wraps content with hover overlay and copy button.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	private function render_cover_preset( $atts ) {
		$id           = ! empty( $atts['id'] ) ? $atts['id'] : Cover::generate_cover_id();
		$custom_class = ! empty( $atts['class'] ) ? ' ' . esc_attr( $atts['class'] ) : '';

		// Build config for Cover style.
		$config = [
			'text'   => [
				'button_text'  => $atts['button-text'],
				'success_text' => $atts['success-text'],
			],
			'icon'   => [
				'enabled'  => 'no' !== $atts['show-icon'],
				'position' => ! empty( $atts['icon-position'] ) ? $atts['icon-position'] : 'left',
				'icon_key' => ! empty( $atts['icon'] ) ? $atts['icon'] : 'clipboard',
			],
			'button' => [],
		];

		// Add custom colors if provided.
		if ( ! empty( $atts['color'] ) ) {
			$config['button']['text_color'] = $atts['color'];
		}
		if ( ! empty( $atts['bg'] ) ) {
			$config['button']['bg_color'] = $atts['bg'];
		}
		if ( ! empty( $atts['hover-bg'] ) ) {
			$config['button']['hover_bg_color'] = $atts['hover-bg'];
		}

		// Merge with defaults.
		$config = Cover::merge_config( $config );

		// Get icon SVG.
		$icon_svg = '';
		if ( $config['icon']['enabled'] ) {
			$icon_svg = '<span class="ctc-cover-icon">' . Cover::get_icon_svg( $config['icon']['icon_key'] ) . '</span>';
		}

		// Build button content.
		$button_content = '';
		if ( 'left' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}
		$button_content .= '<span class="ctc-cover-text">' . esc_html( $config['text']['button_text'] ) . '</span>';
		if ( 'right' === $config['icon']['position'] && $icon_svg ) {
			$button_content .= $icon_svg;
		}

		ob_start();
		?>
		<div
			id="<?php echo esc_attr( $id ); ?>"
			class="ctc-shortcode ctc-shortcode--cover<?php echo esc_attr( $custom_class ); ?>"
			data-ctc-format="<?php echo esc_attr( $atts['copy-as'] ); ?>"
		>
			<div class="ctc-shortcode__content">
				<?php echo wp_kses_post( $atts['display'] ); ?>
			</div>
			<div 
				class="ctc-cover-overlay"
				data-ctc-copy="<?php echo esc_attr( $atts['text'] ); ?>"
				data-ctc-success="<?php echo esc_attr( $config['text']['success_text'] ); ?>"
				<?php if ( ! empty( $atts['target'] ) ) : ?>
					data-ctc-target="<?php echo esc_attr( $atts['target'] ); ?>"
				<?php endif; ?>
				role="button"
				tabindex="0"
				aria-label="<?php echo esc_attr( $atts['tooltip'] ); ?>"
			>
				<button type="button" class="ctc-cover-button" aria-label="<?php echo esc_attr( $atts['tooltip'] ); ?>">
					<?php echo $button_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Get icon SVG by key.
	 *
	 * @param string $icon_key Icon key.
	 * @return string SVG markup.
	 */
	private function get_icon_svg( $icon_key ) {
		$icons = [
			'clipboard' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
			'copy'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
			'link'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>',
			'check'     => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
		];

		return isset( $icons[ $icon_key ] ) ? $icons[ $icon_key ] : $icons['clipboard'];
	}

	/**
	 * Get success icon SVG.
	 *
	 * @return string SVG markup.
	 */
	private function get_success_icon_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
	}

	/**
	 * Get allowed SVG tags for wp_kses.
	 *
	 * @return array Allowed HTML tags and attributes.
	 */
	private function get_allowed_svg_tags() {
		return [
			'svg'  => [
				'xmlns'       => true,
				'fill'        => true,
				'viewbox'     => true,
				'stroke'      => true,
				'width'       => true,
				'height'      => true,
				'class'       => true,
				'aria-hidden' => true,
			],
			'path' => [
				'd'               => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'stroke-width'    => true,
				'fill'            => true,
			],
		];
	}

	/**
	 * Maybe enqueue assets if shortcode was used.
	 *
	 * @return void
	 */
	public function maybe_enqueue_assets() {
		if ( ! $this->styles_enqueued ) {
			return;
		}

		wp_enqueue_script(
			'ctc-lib-core',
			CTC_URI . 'assets/frontend/js/lib/ctc.js',
			[],
			CTC_VER,
			true
		);

		wp_enqueue_script(
			'ctc-shortcode',
			CTC_URI . 'assets/frontend/js/shortcode.js',
			[ 'ctc-lib-core' ],
			CTC_VER,
			true
		);

		// Enqueue inline CSS (only for presets used on the page, minified).
		wp_register_style( 'ctc-shortcode', false, [], CTC_VER );
		wp_enqueue_style( 'ctc-shortcode' );
		wp_add_inline_style( 'ctc-shortcode', $this->get_inline_css() );
	}

	/**
	 * Minify CSS: strip comments and collapse whitespace.
	 *
	 * @param string $css Raw CSS.
	 * @return string Minified CSS.
	 */
	private function minify_css( $css ) {
		$css = (string) preg_replace( '/\/\*[\s\S]*?\*\//', '', $css );
		$css = (string) preg_replace( '/\s+/', ' ', $css );
		return trim( $css );
	}

	/**
	 * Get inline CSS for shortcode styles.
	 * Builds from base + only the preset chunks that were used, then minifies.
	 *
	 * @return string CSS styles.
	 */
	private function get_inline_css() {
		$parts     = [ $this->get_css_base() ];
		$presets   = array_keys( $this->used_presets );
		$chunk_map = [
			'inline' => 'get_css_inline',
			'native' => 'get_css_native',
			'button' => 'get_css_button',
			'icon'   => 'get_css_icon',
			'cover'  => 'get_css_cover',
		];
		foreach ( $presets as $preset ) {
			if ( isset( $chunk_map[ $preset ] ) ) {
				$parts[] = $this->{$chunk_map[ $preset ]}();
			}
		}
		return $this->minify_css( implode( "\n", $parts ) );
	}

	/**
	 * Base + shared CSS (vars, icon, success, table, keyframes). Always included when shortcode is used.
	 *
	 * @return string
	 */
	private function get_css_base() {
		return '
.ctc-shortcode {
	--ctc-inline-color: currentColor;
	--ctc-inline-hover-color: currentColor;
}
.ctc-shortcode__icon {
	display: inline-flex;
	align-items: center;
	justify-content: center;
}
.ctc-shortcode__icon svg {
	width: 14px;
	height: 14px;
}
table .ctc-shortcode__icon,
table .ctc-shortcode__icon svg,
table .ctc-cover-icon,
table .ctc-cover-icon svg {
	width: 14px !important;
	height: 14px !important;
	min-width: 14px;
	min-height: 14px;
	max-width: 14px !important;
	max-height: 14px !important;
	flex-shrink: 0;
}
table .ctc-cover-icon svg {
	width: 12px !important;
	height: 12px !important;
	max-width: 12px !important;
	max-height: 12px !important;
}
.ctc-shortcode__success {
	position: absolute;
	pointer-events: none;
}
.ctc-shortcode--copied {
	animation: ctc-shortcode-pulse 0.3s ease;
}
@keyframes ctc-shortcode-pulse {
	0%, 100% { transform: scale(1); }
	50% { transform: scale(1.05); }
}
';
	}

	/**
	 * Inline preset CSS.
	 *
	 * @return string
	 */
	private function get_css_inline() {
		return '
.ctc-shortcode--inline {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	cursor: pointer;
	color: var(--ctc-inline-color);
	transition: opacity 0.15s ease;
}
.ctc-shortcode--inline:hover {
	opacity: 0.8;
}
.ctc-shortcode--inline:focus {
	outline: 2px solid currentColor;
	outline-offset: 2px;
}
.ctc-shortcode--inline .ctc-inline-hidden {
	display: none;
}
';
	}

	/**
	 * Native preset CSS.
	 *
	 * @return string
	 */
	private function get_css_native() {
		return '
.ctc-shortcode--native {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	cursor: pointer;
}
.ctc-shortcode--native .ctc-shortcode__icon {
	opacity: 0.7;
	transition: opacity 0.15s ease;
}
.ctc-shortcode--native:hover .ctc-shortcode__icon {
	opacity: 1;
}
';
	}

	/**
	 * Button preset CSS.
	 *
	 * @return string
	 */
	private function get_css_button() {
		return '
.ctc-shortcode--button {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	border: none;
	cursor: pointer;
	font-family: inherit;
	line-height: 1.4;
	transition: all 0.15s ease;
	color: var(--ctc-text-color, #ffffff);
	background: var(--ctc-bg, #4f46e5);
	font-size: var(--ctc-font-size, 13px);
	font-weight: var(--ctc-font-weight, 600);
	padding: var(--ctc-padding-y, 8px) var(--ctc-padding-x, 16px);
	border-radius: var(--ctc-border-radius, 6px);
}
.ctc-shortcode--button:hover {
	background: var(--ctc-hover-bg, #4338ca);
}
.ctc-shortcode--button:focus {
	outline: 2px solid var(--ctc-bg, #4f46e5);
	outline-offset: 2px;
}
.ctc-shortcode--button:active {
	transform: scale(0.97);
}
.ctc-shortcode--button .ctc-shortcode__icon svg {
	width: 14px;
	height: 14px;
}
';
	}

	/**
	 * Icon preset CSS.
	 *
	 * @return string
	 */
	private function get_css_icon() {
		return '
.ctc-shortcode--icon {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-style: solid;
	cursor: pointer;
	transition: all 0.2s ease;
	color: var(--ctc-icon-color, #6b7280);
	background: var(--ctc-icon-bg, transparent);
	border-color: var(--ctc-icon-border, #d1d5db);
	border-width: var(--ctc-icon-border-width, 1px);
	padding: var(--ctc-icon-padding, 6px);
	border-radius: var(--ctc-icon-radius, 6px);
}
.ctc-shortcode--icon:hover {
	color: var(--ctc-icon-hover-color, #374151);
	border-color: var(--ctc-icon-hover-border, #9ca3af);
	background: var(--ctc-icon-hover-bg, #f3f4f6);
}
.ctc-shortcode--icon:focus {
	outline: 2px solid var(--ctc-icon-border, #d1d5db);
	outline-offset: 2px;
}
.ctc-shortcode--icon .ctc-shortcode__icon svg {
	width: var(--ctc-icon-size, 16px);
	height: var(--ctc-icon-size, 16px);
}
';
	}

	/**
	 * Cover preset CSS.
	 *
	 * @return string
	 */
	private function get_css_cover() {
		return '
.ctc-shortcode--cover {
	position: relative;
	display: block;
}
.ctc-shortcode--cover .ctc-shortcode__content {
	display: block;
}
.ctc-shortcode--cover .ctc-cover-overlay {
	position: absolute;
	inset: 0;
	z-index: 10;
	border-radius: inherit;
	transition: all 0.3s ease;
	cursor: pointer;
	opacity: 0;
	background: rgba(15, 23, 42, 0.2);
	display: flex;
	align-items: center;
	justify-content: center;
}
.ctc-shortcode--cover:hover .ctc-cover-overlay {
	opacity: 1;
	background: rgba(79, 70, 229, 0.3);
	backdrop-filter: blur(2px);
	-webkit-backdrop-filter: blur(2px);
}
.ctc-shortcode--cover .ctc-cover-button {
	transform: scale(0.9);
	opacity: 0;
	transition: all 0.2s ease;
	color: #0f172a;
	background: rgba(255, 255, 255, 0.95);
	font-size: 10px;
	font-weight: 700;
	padding: 6px 12px;
	border-radius: 9999px;
	display: inline-flex;
	align-items: center;
	gap: 6px;
	border: 1px solid rgba(255, 255, 255, 0.5);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
	white-space: nowrap;
	cursor: pointer;
}
.ctc-shortcode--cover:hover .ctc-cover-button {
	opacity: 1;
	transform: scale(1);
}
.ctc-shortcode--cover .ctc-cover-button:hover {
	background: #f1f5f9;
	transform: scale(1.05);
}
.ctc-shortcode--cover .ctc-cover-icon svg {
	width: 12px;
	height: 12px;
}
';
	}
}

