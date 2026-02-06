<?php
/**
 * Display Conditions for Global Injector
 *
 * Defines available display conditions, operators, and evaluation logic.
 * Conditions determine when/where the copy button should be displayed.
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Global_Injector;

/**
 * Display Conditions Class
 */
class Display_Conditions {

	/**
	 * Get all available display conditions.
	 *
	 * Each condition has:
	 * - key: Unique identifier
	 * - label: Human-readable name
	 * - icon: SVG icon key
	 * - operators: Available comparison operators
	 * - value_type: 'select', 'text', 'number'
	 * - values: For select type, array of options
	 * - is_pro: Whether this is a Pro-only condition
	 *
	 * @return array Array of conditions.
	 */
	public static function get_conditions() {
		$conditions = [
			// Free conditions.
			'post_type'      => [
				'key'        => 'post_type',
				'label'      => __( 'Post Type', 'ctc' ),
				'icon'       => 'document',
				'operators'  => [ 'eq', 'neq' ],
				'value_type' => 'select',
				'values'     => self::get_post_types(),
				'is_pro'     => false,
			],
			'user_logged_in' => [
				'key'        => 'user_logged_in',
				'label'      => __( 'User Logged In', 'ctc' ),
				'icon'       => 'user',
				'operators'  => [ 'is' ],
				'value_type' => 'select',
				'values'     => [
					[
						'value' => 'yes',
						'label' => __( 'Yes', 'ctc' ),
					],
					[
						'value' => 'no',
						'label' => __( 'No', 'ctc' ),
					],
				],
				'is_pro'     => false,
			],
			'page_id'        => [
				'key'        => 'page_id',
				'label'      => __( 'Page ID', 'ctc' ),
				'icon'       => 'page',
				'operators'  => [ 'eq', 'neq' ],
				'value_type' => 'text',
				'values'     => [],
				'is_pro'     => false,
			],
			'post_id'        => [
				'key'        => 'post_id',
				'label'      => __( 'Post ID', 'ctc' ),
				'icon'       => 'post',
				'operators'  => [ 'eq', 'neq' ],
				'value_type' => 'text',
				'values'     => [],
				'is_pro'     => false,
			],

			// Pro conditions.
			'user_role'      => [
				'key'        => 'user_role',
				'label'      => __( 'User Role', 'ctc' ),
				'icon'       => 'users',
				'operators'  => [ 'eq', 'neq' ],
				'value_type' => 'select',
				'values'     => self::get_user_roles(),
				'is_pro'     => true,
			],
			'device'         => [
				'key'        => 'device',
				'label'      => __( 'Device', 'ctc' ),
				'icon'       => 'device',
				'operators'  => [ 'is' ],
				'value_type' => 'select',
				'values'     => [
					[
						'value' => 'mobile',
						'label' => __( 'Mobile', 'ctc' ),
					],
					[
						'value' => 'tablet',
						'label' => __( 'Tablet', 'ctc' ),
					],
					[
						'value' => 'desktop',
						'label' => __( 'Desktop', 'ctc' ),
					],
				],
				'is_pro'     => true,
			],
			'url_contains'   => [
				'key'        => 'url_contains',
				'label'      => __( 'URL Contains', 'ctc' ),
				'icon'       => 'link',
				'operators'  => [ 'contains', 'not_contains' ],
				'value_type' => 'text',
				'values'     => [],
				'is_pro'     => true,
			],
		];

		/**
		 * Filter the available display conditions.
		 *
		 * Allows developers to add, modify, or remove display conditions.
		 *
		 * @since 5.0.0
		 *
		 * @param array $conditions Array of condition definitions.
		 */
		return apply_filters( 'ctc/display_conditions/list', $conditions );
	}

	/**
	 * Get available operators.
	 *
	 * @return array Array of operators with key => label.
	 */
	public static function get_operators() {
		$operators = [
			'eq'           => __( 'Is Equal To', 'ctc' ),
			'neq'          => __( 'Is Not Equal To', 'ctc' ),
			'is'           => __( 'Is', 'ctc' ),
			'contains'     => __( 'Contains', 'ctc' ),
			'not_contains' => __( 'Does Not Contain', 'ctc' ),
			'gt'           => __( 'Greater Than', 'ctc' ),
			'lt'           => __( 'Less Than', 'ctc' ),
			'gte'          => __( 'Greater Than or Equal', 'ctc' ),
			'lte'          => __( 'Less Than or Equal', 'ctc' ),
		];

		/**
		 * Filter the available operators.
		 *
		 * @since 5.0.0
		 *
		 * @param array $operators Array of operator definitions.
		 */
		return apply_filters( 'ctc/display_conditions/operators', $operators );
	}

	/**
	 * Get available post types for condition values.
	 *
	 * @return array Array of post types with value/label.
	 */
	public static function get_post_types() {
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$options    = [];

		foreach ( $post_types as $post_type ) {
			$options[] = [
				'value' => $post_type->name,
				'label' => $post_type->label,
			];
		}

		return $options;
	}

	/**
	 * Get available user roles for condition values.
	 *
	 * @return array Array of user roles with value/label.
	 */
	public static function get_user_roles() {
		$roles   = wp_roles()->roles;
		$options = [];

		foreach ( $roles as $key => $role ) {
			$options[] = [
				'value' => $key,
				'label' => $role['name'],
			];
		}

		return $options;
	}

	/**
	 * Get the current page context for frontend evaluation.
	 *
	 * This data is localized to JS so conditions can be evaluated client-side.
	 *
	 * @return array Current page context.
	 */
	public static function get_page_context() {
		global $post;

		$context = [
			'post_type'      => get_post_type(),
			'post_id'        => get_the_ID(),
			'page_id'        => is_page() ? get_the_ID() : 0,
			'user_logged_in' => is_user_logged_in() ? 'yes' : 'no',
			'user_role'      => self::get_current_user_role(),
			'url'            => self::get_current_url(),
			'device'         => 'desktop', // Will be detected by JS.
		];

		/**
		 * Filter the page context for condition evaluation.
		 *
		 * @since 5.0.0
		 *
		 * @param array $context Current page context.
		 */
		return apply_filters( 'ctc/display_conditions/page_context', $context );
	}

	/**
	 * Get the current user's primary role.
	 *
	 * @return string User role or empty string if not logged in.
	 */
	private static function get_current_user_role() {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user  = wp_get_current_user();
		$roles = $user->roles;

		return ! empty( $roles ) ? reset( $roles ) : '';
	}

	/**
	 * Get the current page URL.
	 *
	 * @return string Current URL.
	 */
	private static function get_current_url() {
		global $wp;
		return home_url( add_query_arg( [], $wp->request ) );
	}

	/**
	 * Evaluate conditions for a rule.
	 *
	 * Groups are OR (any match = true).
	 * Rules within a group are AND (all must match = true).
	 *
	 * @param array $display_conditions The display conditions array.
	 * @param array $context            The page context to evaluate against.
	 * @return bool Whether the conditions are met.
	 */
	public static function evaluate( $display_conditions, $context = [] ) {
		// No conditions = show everywhere.
		if ( empty( $display_conditions ) || ! is_array( $display_conditions ) ) {
			return true;
		}

		// Use current page context if not provided.
		if ( empty( $context ) ) {
			$context = self::get_page_context();
		}

		// OR logic: Any group matching = true.
		foreach ( $display_conditions as $group ) {
			if ( self::evaluate_group( $group, $context ) ) {
				return true;
			}
		}

		// No groups matched.
		return false;
	}

	/**
	 * Evaluate a single condition group.
	 *
	 * All rules in the group must match (AND logic).
	 *
	 * @param array $group   The condition group.
	 * @param array $context The page context.
	 * @return bool Whether all rules in the group match.
	 */
	private static function evaluate_group( $group, $context ) {
		if ( empty( $group['rules'] ) || ! is_array( $group['rules'] ) ) {
			return true; // Empty group = always true.
		}

		// AND logic: All rules must match.
		foreach ( $group['rules'] as $rule ) {
			if ( ! self::evaluate_rule( $rule, $context ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Evaluate a single rule.
	 *
	 * @param array $rule    The rule to evaluate.
	 * @param array $context The page context.
	 * @return bool Whether the rule matches.
	 */
	private static function evaluate_rule( $rule, $context ) {
		$param    = isset( $rule['param'] ) ? $rule['param'] : '';
		$operator = isset( $rule['op'] ) ? $rule['op'] : 'eq';
		$value    = isset( $rule['value'] ) ? $rule['value'] : '';

		// Get the actual value from context.
		$actual = isset( $context[ $param ] ) ? $context[ $param ] : '';

		return self::compare( $actual, $operator, $value );
	}

	/**
	 * Compare two values using an operator.
	 *
	 * @param mixed  $actual   The actual value from context.
	 * @param string $operator The comparison operator.
	 * @param mixed  $expected The expected value from rule.
	 * @return bool Comparison result.
	 */
	private static function compare( $actual, $operator, $expected ) {
		switch ( $operator ) {
			case 'eq':
			case 'is':
				return (string) $actual === (string) $expected;

			case 'neq':
				return (string) $actual !== (string) $expected;

			case 'contains':
				return false !== strpos( (string) $actual, (string) $expected );

			case 'not_contains':
				return false === strpos( (string) $actual, (string) $expected );

			case 'gt':
				return (float) $actual > (float) $expected;

			case 'lt':
				return (float) $actual < (float) $expected;

			case 'gte':
				return (float) $actual >= (float) $expected;

			case 'lte':
				return (float) $actual <= (float) $expected;

			default:
				return false;
		}
	}

	/**
	 * Get data to localize for JavaScript.
	 *
	 * @return array Data for wp_localize_script.
	 */
	public static function get_localize_data() {
		return [
			'conditions'  => self::get_conditions(),
			'operators'   => self::get_operators(),
			'pageContext' => self::get_page_context(),
		];
	}

	/**
	 * Sanitize display conditions before saving.
	 *
	 * @param mixed $conditions Raw conditions data.
	 * @return array Sanitized conditions.
	 */
	public static function sanitize( $conditions ) {
		if ( ! is_array( $conditions ) ) {
			return [];
		}

		$sanitized = [];

		foreach ( $conditions as $group ) {
			if ( ! is_array( $group ) ) {
				continue;
			}

			$sanitized_group = [
				'id'    => isset( $group['id'] ) ? sanitize_key( $group['id'] ) : wp_generate_uuid4(),
				'rules' => [],
			];

			if ( isset( $group['rules'] ) && is_array( $group['rules'] ) ) {
				foreach ( $group['rules'] as $rule ) {
					if ( ! is_array( $rule ) ) {
						continue;
					}

					$sanitized_group['rules'][] = [
						'param' => isset( $rule['param'] ) ? sanitize_key( $rule['param'] ) : '',
						'op'    => isset( $rule['op'] ) ? sanitize_key( $rule['op'] ) : 'eq',
						'value' => isset( $rule['value'] ) ? sanitize_text_field( $rule['value'] ) : '',
					];
				}
			}

			// Only add groups that have rules.
			if ( ! empty( $sanitized_group['rules'] ) ) {
				$sanitized[] = $sanitized_group;
			}
		}

		return $sanitized;
	}
}
