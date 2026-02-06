<?php

/**
 * Elementor Message Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Message Block
 *
 * @since 5.0.0
 */
class Message extends Widget_Base {
    /**
     * Constructor
     *
     * @param array $data
     * @param array $args
     *
     * @since 5.0.0
     */
    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        // Core.
        wp_enqueue_style(
            'ctc-blocks-core',
            CTC_URI . 'includes/assets/css/style.css',
            [],
            CTC_VER,
            'all'
        );
        wp_enqueue_script(
            'ctc-clipboard',
            CTC_URI . 'assets/frontend/js/vendor/ctc.js',
            ['jquery'],
            CTC_VER,
            true
        );
        wp_enqueue_script(
            'ctc-blocks-core',
            CTC_URI . 'includes/assets/js/core.js',
            ['ctc-clipboard'],
            CTC_VER,
            true
        );
        // Block.
        wp_enqueue_style(
            'ctc-el-message',
            CTC_URI . 'includes/elementor/widgets/message/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-message'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['ctc-clipboard', 'ctc-blocks-core'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_message';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Message', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-columns';
    }

    /**
     * Get categories
     */
    public function get_categories() {
        return Helpers::get_categories();
    }

    /**
     * Get keywords
     */
    public function get_keywords() {
        return Helpers::get_keywords( ['message'] );
    }

    /**
     * Render
     */
    public function render() {
        $message = $this->get_settings_for_display( 'message' );
        if ( empty( $message ) ) {
            return;
        }
        $message = wpautop( $message );
        ?>
		<div class="ctc-block ctc-message">
			<div class="ctc-block-content">
				<div class="ctc-message-box">
					<div class="ctc-message-text"><?php 
        echo wp_kses_post( $message );
        ?></div>
				</div>
			</div>
			<div class="ctc-block-actions">
				<?php 
        Helpers::render_copy_button( $this );
        ?>
			</div>
			<?php 
        Helpers::render_copy_content( $this );
        ?>
		</div>
		<?php 
    }

    /**
     * Register controls
     */
    protected function _register_controls() {
        $default = 'May your birthday be filled with many happy hours and your life with many happy birthdays.

HAPPY BIRTHDAY !!';
        Helpers::register_copy_content_section( $this, [
            'default' => $default,
        ] );
        /**
         * Group: message Section
         */
        $this->start_controls_section( 'message_section', [
            'label' => esc_html__( 'Message', 'ctc' ),
        ] );
        // Two paragraph gap in pixcel.
        $this->add_responsive_control( 'line_gap', [
            'label'      => esc_html__( 'Line Gap', 'ctc' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => ['px', 'em'],
            'range'      => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors'  => [
                '{{WRAPPER}} .ctc-message-text p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_control( 'message', [
            'label'   => esc_html__( 'Message', 'ctc' ),
            'type'    => Controls_Manager::WYSIWYG,
            'default' => $default,
            'dynamic' => [
                'active' => true,
            ],
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Message', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, ['Message Box', 'Message'] );
        Helpers::register_copy_button_style_section( $this );
    }

}
