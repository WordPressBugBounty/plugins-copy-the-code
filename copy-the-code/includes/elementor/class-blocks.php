<?php
/**
 * Elementor Blocks
 *
 * @package CTC
 * @since 5.0.0
 */

namespace CTC\Elementor;

/**
 * Blocks
 *
 * @since 5.0.0
 */
class Blocks {

	/**
	 * Instance
	 *
	 * @var Blocks|null
	 */
	private static $instance = null;

	/**
	 * Registered widgets
	 *
	 * @var array
	 */
	private $widgets = [];

	/**
	 * Get instance.
	 *
	 * @return Blocks
	 */
	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
	}

	/**
	 * Get widget definitions.
	 *
	 * @return array
	 */
	private function get_widget_definitions() {
		return [
			'email-sample'        => [
				'name'        => __( 'Email Sample', 'ctc' ),
				'description' => __( 'Create the email sample and allow users to copy it.', 'ctc' ),
				'class'       => Block\Email\Sample::class,
			],
			'email-address'       => [
				'name'        => __( 'Email Address', 'ctc' ),
				'description' => __( 'Add the email address and allow users to copy it.', 'ctc' ),
				'class'       => Block\Email\Address::class,
			],
			'phone-number'        => [
				'name'        => __( 'Phone Number', 'ctc' ),
				'description' => __( 'Add the phone number and allow users to copy it.', 'ctc' ),
				'class'       => Block\PhoneNumber::class,
			],
			'copy-button'         => [
				'name'        => __( 'Copy Button', 'ctc' ),
				'description' => __( 'Add the copy button and add the hidden content which you want to copy.', 'ctc' ),
				'class'       => Block\CopyButton::class,
			],
			'copy-icon'           => [
				'name'        => __( 'Copy Icon', 'ctc' ),
				'description' => __( 'Add the copy icon and add the hidden content which you want to copy.', 'ctc' ),
				'class'       => Block\CopyIcon::class,
			],
			'blockquote'          => [
				'name'        => __( 'Blockquote', 'ctc' ),
				'description' => __( 'Create the blockquote and allow users to copy the text.', 'ctc' ),
				'class'       => Block\Blockquote::class,
			],
			'code-snippet'        => [
				'name'        => __( 'Code Snippet', 'ctc' ),
				'description' => __( 'Create the code snippet and allow users to copy the text.', 'ctc' ),
				'class'       => Block\CodeSnippet::class,
			],
			'message'             => [
				'name'        => __( 'Message', 'ctc' ),
				'description' => __( 'Create a message and allow users to copy it.', 'ctc' ),
				'class'       => Block\Message::class,
			],
			'wish'                => [
				'name'        => __( 'Wish', 'ctc' ),
				'description' => __( 'Create a wish and allow users to copy it.', 'ctc' ),
				'class'       => Block\Wish::class,
			],
			'shayari'             => [
				'name'        => __( 'Shayari', 'ctc' ),
				'description' => __( 'Create shayari and allow users to copy it.', 'ctc' ),
				'class'       => Block\Shayari::class,
			],
			'sms'                 => [
				'name'        => __( 'SMS', 'ctc' ),
				'description' => __( 'Create an SMS template and allow users to copy it.', 'ctc' ),
				'class'       => Block\SMS::class,
			],
			'deal'                => [
				'name'        => __( 'Deal', 'ctc' ),
				'description' => __( 'Create a deal card with copy functionality.', 'ctc' ),
				'class'       => Block\Deal::class,
			],
			'coupon'              => [
				'name'        => __( 'Coupon', 'ctc' ),
				'description' => __( 'Create a coupon code and allow users to copy it.', 'ctc' ),
				'class'       => Block\Coupon::class,
			],
			'ai-prompt-generator' => [
				'name'        => __( 'AI Prompt Generator', 'ctc' ),
				'description' => __( 'Generate AI prompts and allow users to copy them.', 'ctc' ),
				'class'       => Block\AI\Prompt\Generator::class,
			],
			'table'               => [
				'name'        => __( 'Table', 'ctc' ),
				'description' => __( 'Create a table with copy functionality.', 'ctc' ),
				'class'       => Block\Table::class,
			],
			'contact-information' => [
				'name'        => __( 'Contact Information', 'ctc' ),
				'description' => __( 'Display contact information with copy functionality.', 'ctc' ),
				'class'       => Block\ContactInformation::class,
			],
		];
	}

	/**
	 * Register category
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 *
	 * @since 5.0.0
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'ctc',
			[
				'title' => esc_html__( 'Copy Anything to Clipboard', 'ctc' ),
				'icon'  => 'fa fa-code',
			]
		);
	}

	/**
	 * Register widgets
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 *
	 * @since 5.0.0
	 */
	public function register_widgets( $widgets_manager ) {
		$definitions = $this->get_widget_definitions();

		foreach ( $definitions as $id => $widget ) {
			$class = $widget['class'];

			// Check if class exists (via autoloader).
			if ( ! class_exists( $class ) ) {
				// Fallback: require the widget file.
				$file = CTC_ELEMENTOR_DIR . 'widgets/' . $id . '/widget.php';
				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}

			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}

	/**
	 * Get blocks for external use (e.g., admin UI).
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function get_blocks() {
		$instance    = self::get();
		$definitions = $instance->get_widget_definitions();
		$blocks      = [];

		foreach ( $definitions as $id => $widget ) {
			$blocks[] = [
				'id'          => $id,
				'name'        => $widget['name'],
				'description' => $widget['description'],
			];
		}

		return $blocks;
	}
}
