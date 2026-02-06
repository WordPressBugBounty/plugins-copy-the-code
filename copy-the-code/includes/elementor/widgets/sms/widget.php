<?php

/**
 * Elementor SMS Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * SMS Block
 *
 * @since 5.0.0
 */
class SMS extends Widget_Base {
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
            'ctc-el-sms',
            CTC_URI . 'includes/elementor/widgets/sms/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-sms'];
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
        return 'ctc_sms';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'SMS', 'ctc' );
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
        return Helpers::get_keywords( ['sms'] );
    }

    /**
     * Render
     */
    public function render() {
        $sms = $this->get_settings_for_display( 'sms' );
        if ( empty( $sms ) ) {
            return;
        }
        $sms = wpautop( $sms );
        ?>
		<div class="ctc-block ctc-sms">
			<div class="ctc-block-content">
				<div class="ctc-sms-box">
					<div class="ctc-sms-text"><?php 
        echo wp_kses_post( $sms );
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
        $default = 'You are the most special person in my life. I love you from the deepest core of my heart.';
        Helpers::register_copy_content_section( $this, [
            'default' => $default,
        ] );
        /**
         * Group: SMS Section
         */
        $this->start_controls_section( 'sms_section', [
            'label' => esc_html__( 'SMS', 'ctc' ),
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
                '{{WRAPPER}} .ctc-sms-text p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_control( 'sms', [
            'label'   => esc_html__( 'SMS', 'ctc' ),
            'type'    => Controls_Manager::WYSIWYG,
            'default' => $default,
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy SMS', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, ['SMS Box', 'SMS'] );
        Helpers::register_copy_button_style_section( $this );
    }

}
