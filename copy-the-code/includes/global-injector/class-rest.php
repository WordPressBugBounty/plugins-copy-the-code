<?php
/**
 * Rest API
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector;

use CTC\Helper;

/**
 * Rest API
 *
 * @since 5.0.0
 */
class Rest {

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
	 * REST namespace
	 *
	 * @var string
	 */
	private $namespace = 'ctc/v1';

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
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register REST routes
	 *
	 * @since 5.0.0
	 * @return void
	 */
	public function register_routes() {
		// Rules endpoints.
		register_rest_route(
			$this->namespace,
			'/rules/(?P<id>\d+)',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'save_rule' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/rules',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'create_rule' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/rules/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ $this, 'delete_rule' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);

		// Style Presets endpoints (matching v5.0.0 convention).
		register_rest_route(
			$this->namespace,
			'/style-presets',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_presets' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/style-presets',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'create_preset' ],
				'permission_callback' => [ $this, 'check_permissions' ],
			]
		);

		register_rest_route(
			$this->namespace,
			'/style-presets/(?P<id>\d+)',
			[
				'methods'             => [ 'PUT', 'POST' ], // Support both PUT and POST for updates.
				'callback'            => [ $this, 'update_preset' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);

		register_rest_route(
			$this->namespace,
			'/style-presets/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ $this, 'delete_preset' ],
				'permission_callback' => [ $this, 'check_permissions' ],
				'args'                => [
					'id' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param );
						},
					],
				],
			]
		);
	}

	/**
	 * Check permissions
	 *
	 * @since 5.0.0
	 * @return bool
	 */
	public function check_permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Save rule
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function save_rule( $request ) {
		$id   = $request->get_param( 'id' );
		$data = $request->get_json_params();

		$post = get_post( $id );
		if ( ! $post || 'copy-to-clipboard' !== $post->post_type ) {
			return new \WP_Error(
				'rule_not_found',
				__( 'Rule not found.', 'ctc' ),
				[ 'status' => 404 ]
			);
		}

		// Update post title.
		if ( isset( $data['title'] ) ) {
			wp_update_post(
				[
					'ID'         => $id,
					'post_title' => sanitize_text_field( $data['title'] ),
				]
			);
		}

		// Update meta fields.
		$meta_mapping = [
			'selector'             => 'selector',
			'exclude_selector'     => 'exclude_selector',
			'style_type'           => 'style',
			'visual_style'         => 'visual_style',
			'button_text'          => 'button-text',
			'success_text'         => 'button-copy-text',
			'tooltip_text'         => 'tooltip-text',
			'reveal_text'          => 'reveal-text',
			'button_position'      => 'button-position',
			'button_title'         => 'button-title',
			'copy_format'          => 'copy-format',
			'copy_as'              => 'copy-as',
			'is_active'            => 'is_active',
			'display_conditions'   => 'display_conditions',
			// Icon settings.
			'icon_enabled'         => 'icon_enabled',
			'icon_position'        => 'icon_position',
			'icon_key'             => 'icon_key',
			// Style fields (temporary storage on rule until proper preset management).
			// Button style fields.
			'text_color'           => 'text_color',
			'bg_color'             => 'bg_color',
			'border_radius'        => 'border_radius',
			'font_size'            => 'font_size',
			'padding_x'            => 'padding_x',
			'padding_y'            => 'padding_y',
			// Icon style fields.
			'icon_color'           => 'icon_color',
			'icon_hover_color'     => 'icon_hover_color',
			'border_color'         => 'border_color',
			'icon_size'            => 'icon_size',
			'padding'              => 'padding',
			'border_width'         => 'border_width',
			// Cover style fields.
			'overlay_color'        => 'overlay_color',
			'hover_overlay_color'  => 'hover_overlay_color',
			'overlay_opacity'      => 'overlay_opacity',
			'blur'                 => 'blur',
			'cover_text_color'     => 'cover_text_color',
			'cover_bg_color'       => 'cover_bg_color',
			'cover_hover_bg_color' => 'cover_hover_bg_color',
			'cover_border_radius'  => 'cover_border_radius',
			'cover_font_size'      => 'cover_font_size',
			'cover_padding_x'      => 'cover_padding_x',
			'cover_padding_y'      => 'cover_padding_y',
		];

		/**
		 * Filter the meta mapping for saving rule fields.
		 *
		 * Pro add-on can use this to add additional fields like custom_css_class.
		 *
		 * @since 5.0.0
		 * @param array $meta_mapping Array of data_key => meta_key mappings.
		 */
		$meta_mapping = apply_filters( 'ctc/global_injector/rule_meta_mapping', $meta_mapping );

		// Fields that need numeric sanitization.
		$numeric_fields = [ 'border_radius', 'font_size', 'padding_x', 'padding_y', 'icon_size', 'padding', 'border_width', 'overlay_opacity', 'blur', 'cover_border_radius', 'cover_font_size', 'cover_padding_x', 'cover_padding_y' ];
		// Fields that need boolean sanitization.
		$boolean_fields = [ 'icon_enabled' ];
		// Fields that need hex color sanitization.
		$color_fields = [ 'text_color', 'bg_color', 'icon_color', 'icon_hover_color', 'border_color', 'overlay_color', 'hover_overlay_color', 'cover_text_color', 'cover_bg_color', 'cover_hover_bg_color' ];

		/**
		 * Filter enum fields for sanitization (data_key => allowed values).
		 * Pro can add fields like image_format => ['png','jpeg','webp'].
		 *
		 * @since 5.1.0
		 * @param array $enum_fields Array of data_key => allowed values.
		 */
		$enum_fields = apply_filters( 'ctc/global_injector/rule_enum_fields', [] );

		/**
		 * Filter the text fields for sanitization.
		 *
		 * Pro can add custom text fields here if needed.
		 *
		 * @since 5.0.0
		 * @param array $text_fields Array of field keys that need text sanitization.
		 */
		$text_fields = apply_filters( 'ctc/global_injector/rule_text_fields', [] );

		foreach ( $meta_mapping as $data_key => $meta_key ) {
			if ( isset( $data[ $data_key ] ) ) {
				$value = $data[ $data_key ];
				if ( 'display_conditions' === $data_key ) {
					$value = Display_Conditions::sanitize( $value );
				} elseif ( 'is_active' === $data_key ) {
					// Save as string '1' or '0' to avoid WordPress empty string issue.
					$value = $value ? '1' : '0';
				} elseif ( isset( $enum_fields[ $data_key ] ) && is_array( $enum_fields[ $data_key ] ) ) {
					$allowed = $enum_fields[ $data_key ];
					$value   = in_array( $value, $allowed, true ) ? $value : ( $allowed[0] ?? '' );
				} elseif ( in_array( $data_key, $boolean_fields, true ) ) {
					// Save as string '1' or '0' for boolean fields.
					$value = $value ? '1' : '0';
				} elseif ( in_array( $data_key, $numeric_fields, true ) ) {
					$value = absint( $value );
				} elseif ( in_array( $data_key, $color_fields, true ) ) {
					$value = sanitize_hex_color( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
				update_post_meta( $id, $meta_key, $value );
			}
		}

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Rule saved successfully.', 'ctc' ),
				'rule'    => $this->get_rule_data( $id ),
			]
		);
	}

	/**
	 * Create rule
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_rule( $request ) {
		$data = $request->get_json_params();

		$post_id = wp_insert_post(
			[
				'post_type'   => 'copy-to-clipboard',
				'post_title'  => isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : __( 'New Rule', 'ctc' ),
				'post_status' => 'publish',
			]
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error(
				'rule_creation_failed',
				$post_id->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		// Set meta values (use provided data or defaults).
		$selector  = isset( $data['selector'] ) ? sanitize_text_field( $data['selector'] ) : 'pre';
		$is_active = isset( $data['is_active'] ) ? ( $data['is_active'] ? '1' : '0' ) : '1';

		update_post_meta( $post_id, 'selector', $selector );
		update_post_meta( $post_id, 'style', 'button' );
		update_post_meta( $post_id, 'button-text', __( 'Copy', 'ctc' ) );
		update_post_meta( $post_id, 'button-copy-text', __( 'Copied!', 'ctc' ) );
		update_post_meta( $post_id, 'button-position', 'inside' );
		update_post_meta( $post_id, 'is_active', $is_active );

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Rule created successfully.', 'ctc' ),
				'rule'    => $this->get_rule_data( $post_id ),
			]
		);
	}

	/**
	 * Delete rule
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_rule( $request ) {
		$id = $request->get_param( 'id' );

		$post = get_post( $id );
		if ( ! $post || 'copy-to-clipboard' !== $post->post_type ) {
			return new \WP_Error(
				'rule_not_found',
				__( 'Rule not found.', 'ctc' ),
				[ 'status' => 404 ]
			);
		}

		wp_delete_post( $id, true );

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Rule deleted successfully.', 'ctc' ),
			]
		);
	}

	/**
	 * Get rule data
	 *
	 * @since 5.0.0
	 * @param int $id Rule ID.
	 * @return array
	 */
	private function get_rule_data( $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return [];
		}

		$is_active = get_post_meta( $id, 'is_active', true );
		if ( '' === $is_active ) {
			// Fallback for rules without is_active meta.
			$is_active = 'publish' === $post->post_status;
		} else {
			// Handle both string '1'/'0' and boolean values.
			$is_active = '0' !== $is_active && false !== $is_active;
		}

		// Get style fields with defaults from default preset.
		$default_presets = Style_Presets::get_default_presets();
		$visual_style    = get_post_meta( $id, 'visual_style', true );
		$visual_style    = ! empty( $visual_style ) ? $visual_style : 'button';
		$style_defaults  = isset( $default_presets[ $visual_style ] ) ? $default_presets[ $visual_style ] : $default_presets['button'];

		// Helper function for getting meta with default value.
		$get_meta = function ( $meta_key, $default_value ) use ( $id ) {
			$value = get_post_meta( $id, $meta_key, true );
			return ! empty( $value ) ? $value : $default_value;
		};

		// Helper function for getting style defaults.
		$style_default = function ( $key, $fallback ) use ( $style_defaults ) {
			return isset( $style_defaults[ $key ] ) ? $style_defaults[ $key ] : $fallback;
		};

		// Get icon enabled as boolean (stored as '1' or '0').
		$icon_enabled_raw = get_post_meta( $id, 'icon_enabled', true );
		$icon_enabled     = '' === $icon_enabled_raw ? true : '1' === $icon_enabled_raw;

		$rule = [
			'id'                   => $post->ID,
			'title'                => isset( $post->post_title ) ? $post->post_title : __( 'Untitled Rule', 'ctc' ),
			'selector'             => $get_meta( 'selector', 'pre' ),
			'exclude_selector'     => $get_meta( 'exclude_selector', '' ),
			'style_type'           => $get_meta( 'style', 'button' ),
			'visual_style'         => $visual_style,
			'button_text'          => $get_meta( 'button-text', __( 'Copy', 'ctc' ) ),
			'success_text'         => $get_meta( 'button-copy-text', __( 'Copied!', 'ctc' ) ),
			'tooltip_text'         => $get_meta( 'tooltip-text', __( 'Copy to clipboard', 'ctc' ) ),
			'reveal_text'          => $get_meta( 'reveal-text', __( 'Click to Reveal', 'ctc' ) ),
			'button_position'      => $this->migrate_position( $get_meta( 'button-position', 'inside_top_right' ) ),
			'is_active'            => $is_active,
			'display_conditions'   => $get_meta( 'display_conditions', [] ),
			'button_title'         => $get_meta( 'button-title', '' ),
			'copy_format'          => $get_meta( 'copy-format', '' ),
			'copy_as'              => $get_meta( 'copy-as', '' ),
			// Icon settings.
			'icon_enabled'         => $icon_enabled,
			'icon_position'        => $get_meta( 'icon_position', 'left' ),
			'icon_key'             => $get_meta( 'icon_key', 'clipboard' ),
			// Style fields (stored on rule for now, uses default preset values as fallback).
			// Button style fields.
			'text_color'           => $get_meta( 'text_color', $style_default( 'text_color', '#ffffff' ) ),
			'bg_color'             => $get_meta( 'bg_color', $style_default( 'bg_color', '#4f46e5' ) ),
			'border_radius'        => (int) $get_meta( 'border_radius', $style_default( 'border_radius', 6 ) ),
			'font_size'            => (int) $get_meta( 'font_size', $style_default( 'font_size', 13 ) ),
			'padding_x'            => (int) $get_meta( 'padding_x', $style_default( 'padding_x', 16 ) ),
			'padding_y'            => (int) $get_meta( 'padding_y', $style_default( 'padding_y', 8 ) ),
			// Icon style fields.
			'icon_color'           => $get_meta( 'icon_color', $style_default( 'icon_color', '#6b7280' ) ),
			'icon_hover_color'     => $get_meta( 'icon_hover_color', $style_default( 'icon_hover_color', '#4f46e5' ) ),
			'border_color'         => $get_meta( 'border_color', $style_default( 'border_color', '#e5e7eb' ) ),
			'icon_size'            => (int) $get_meta( 'icon_size', $style_default( 'icon_size', 16 ) ),
			'padding'              => (int) $get_meta( 'padding', $style_default( 'padding', 8 ) ),
			'border_width'         => (int) $get_meta( 'border_width', $style_default( 'border_width', 1 ) ),
			// Cover style fields.
			'overlay_color'        => $get_meta( 'overlay_color', $style_default( 'overlay_color', '#0f172a' ) ),
			'hover_overlay_color'  => $get_meta( 'hover_overlay_color', $style_default( 'hover_overlay_color', '#4f46e5' ) ),
			'overlay_opacity'      => (int) $get_meta( 'overlay_opacity', $style_default( 'overlay_opacity', 20 ) ),
			'blur'                 => (int) $get_meta( 'blur', $style_default( 'blur', 2 ) ),
			'cover_text_color'     => $get_meta( 'cover_text_color', $style_default( 'text_color', '#0f172a' ) ),
			'cover_bg_color'       => $get_meta( 'cover_bg_color', $style_default( 'badge_bg_color', '#ffffff' ) ),
			'cover_hover_bg_color' => $get_meta( 'cover_hover_bg_color', '#f1f5f9' ),
			'cover_border_radius'  => (int) $get_meta( 'cover_border_radius', $style_default( 'badge_radius', 9999 ) ),
			'cover_font_size'      => (int) $get_meta( 'cover_font_size', $style_default( 'font_size', 10 ) ),
			'cover_padding_x'      => (int) $get_meta( 'cover_padding_x', 12 ),
			'cover_padding_y'      => (int) $get_meta( 'cover_padding_y', 6 ),
		];

		/**
		 * Filter REST rule data before returning.
		 * Pro can add fields like image_format.
		 *
		 * @since 5.1.0
		 * @param array $rule Rule data array.
		 * @param int   $id   Rule ID.
		 */
		return apply_filters( 'ctc/global_injector/rest_rule_data', $rule, $id );
	}

	// =========================================================================
	// PRESET ENDPOINTS
	// =========================================================================

	/**
	 * Get all presets
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function get_presets( $request ) {
		$presets  = Style_Presets::load_all_presets();
		$defaults = Style_Presets::get_default_presets();

		return rest_ensure_response(
			[
				'success'  => true,
				'presets'  => $presets,
				'defaults' => $defaults,
				'limits'   => [
					'button' => [
						'count' => Style_Presets::get_preset_count_by_style( 'button' ),
						'max'   => Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
					],
					'icon'   => [
						'count' => Style_Presets::get_preset_count_by_style( 'icon' ),
						'max'   => Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
					],
					'cover'  => [
						'count' => Style_Presets::get_preset_count_by_style( 'cover' ),
						'max'   => Style_Presets::MAX_FREE_PRESETS_PER_STYLE,
					],
				],
			]
		);
	}

	/**
	 * Create preset
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function create_preset( $request ) {
		$data         = $request->get_json_params();
		$visual_style = isset( $data['visual_style'] ) ? sanitize_text_field( $data['visual_style'] ) : 'button';

		// Check preset limit for free users.
		$is_pro = apply_filters( 'ctc/is_pro', false );
		if ( ! Style_Presets::can_create_preset( $visual_style, $is_pro ) ) {
			return new \WP_Error(
				'preset_limit_reached',
				sprintf(
					/* translators: %d: Maximum presets allowed */
					__( 'You have reached the maximum limit of %d presets for this style. Upgrade to Pro for unlimited presets.', 'ctc' ),
					Style_Presets::MAX_FREE_PRESETS_PER_STYLE
				),
				[ 'status' => 403 ]
			);
		}

		$post_id = wp_insert_post(
			[
				'post_type'   => Style_Presets::POST_TYPE,
				'post_title'  => isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : __( 'New Preset', 'ctc' ),
				'post_status' => 'publish',
			]
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_Error(
				'preset_creation_failed',
				$post_id->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		// Save visual style.
		update_post_meta( $post_id, 'visual_style', $visual_style );

		// Save style-specific meta fields.
		$this->save_preset_meta( $post_id, $visual_style, $data );

		$preset = Style_Presets::format_preset( get_post( $post_id ) );

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Preset created successfully.', 'ctc' ),
				'preset'  => $preset,
			]
		);
	}

	/**
	 * Update preset
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_preset( $request ) {
		$id   = $request->get_param( 'id' );
		$data = $request->get_json_params();

		$post = get_post( $id );
		if ( ! $post || Style_Presets::POST_TYPE !== $post->post_type ) {
			return new \WP_Error(
				'preset_not_found',
				__( 'Preset not found.', 'ctc' ),
				[ 'status' => 404 ]
			);
		}

		// Update title if provided.
		if ( isset( $data['title'] ) ) {
			wp_update_post(
				[
					'ID'         => $id,
					'post_title' => sanitize_text_field( $data['title'] ),
				]
			);
		}

		// Get visual style.
		$visual_style_value = get_post_meta( $id, 'visual_style', true );
		$visual_style       = ! empty( $visual_style_value ) ? $visual_style_value : 'button';

		// Save style-specific meta fields.
		$this->save_preset_meta( $id, $visual_style, $data );

		$preset = Style_Presets::format_preset( get_post( $id ) );

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Preset updated successfully.', 'ctc' ),
				'preset'  => $preset,
			]
		);
	}

	/**
	 * Delete preset
	 *
	 * @since 5.0.0
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function delete_preset( $request ) {
		$id = $request->get_param( 'id' );

		$post = get_post( $id );
		if ( ! $post || Style_Presets::POST_TYPE !== $post->post_type ) {
			return new \WP_Error(
				'preset_not_found',
				__( 'Preset not found.', 'ctc' ),
				[ 'status' => 404 ]
			);
		}

		wp_delete_post( $id, true );

		return rest_ensure_response(
			[
				'success' => true,
				'message' => __( 'Preset deleted successfully.', 'ctc' ),
			]
		);
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
	 * Save preset meta fields based on visual style
	 *
	 * @since 5.0.0
	 * @param int    $post_id Post ID.
	 * @param string $visual_style Visual style type.
	 * @param array  $data Data to save.
	 * @return void
	 */
	private function save_preset_meta( $post_id, $visual_style, $data ) {
		// Define meta fields per style.
		$meta_fields = [
			'button' => [
				'text_color'    => 'sanitize_hex_color',
				'bg_color'      => 'sanitize_hex_color',
				'border_radius' => 'absint',
				'font_size'     => 'absint',
				'padding_x'     => 'absint',
				'padding_y'     => 'absint',
			],
			'icon'   => [
				'icon_color'       => 'sanitize_hex_color',
				'icon_hover_color' => 'sanitize_hex_color',
				'bg_color'         => 'sanitize_hex_color',
				'border_color'     => 'sanitize_hex_color',
				'icon_size'        => 'absint',
				'padding'          => 'absint',
				'border_radius'    => 'absint',
				'border_width'     => 'absint',
			],
			'cover'  => [
				'overlay_color'       => 'sanitize_hex_color',
				'text_color'          => 'sanitize_hex_color',
				'badge_bg_color'      => 'sanitize_hex_color',
				'hover_overlay_color' => 'sanitize_hex_color',
				'overlay_opacity'     => 'absint',
				'blur'                => 'absint',
				'font_size'           => 'absint',
				'badge_radius'        => 'absint',
			],
		];

		$fields = $meta_fields[ $visual_style ] ?? $meta_fields['button'];

		foreach ( $fields as $key => $sanitize_callback ) {
			if ( isset( $data[ $key ] ) ) {
				$value = call_user_func( $sanitize_callback, $data[ $key ] );
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
}
