<?php

/**
 * Elementor Shayari Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Shayari Block
 *
 * @since 5.0.0
 */
class Shayari extends Widget_Base {
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
            'ctc-lib-core',
            CTC_URI . 'assets/frontend/js/lib/ctc.js',
            ['jquery'],
            CTC_VER,
            true
        );
        wp_enqueue_script(
            'ctc-blocks-core',
            CTC_URI . 'includes/assets/js/core.js',
            ['ctc-lib-core'],
            CTC_VER,
            true
        );
        // Block.
        wp_enqueue_style(
            'ctc-el-shayari',
            CTC_URI . 'includes/elementor/widgets/shayari/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-shayari'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['ctc-lib-core', 'ctc-blocks-core'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_shayari';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Shayari', 'ctc' );
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
        return Helpers::get_keywords( ['shayari'] );
    }

    /**
     * Render
     */
    public function render() {
        $shayari = $this->get_settings_for_display( 'shayari' );
        if ( empty( $shayari ) ) {
            return;
        }
        $shayari = wpautop( $shayari );
        ?>
		<div class="ctc-block ctc-shayari">
			<div class="ctc-block-content">
				<div class="ctc-shayari-box">
					<div class="ctc-shayari-text"><?php 
        echo wp_kses_post( $shayari );
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
        $default = 'शायरी वाली बातें';
        Helpers::register_copy_content_section( $this, [
            'default' => $default,
        ] );
        /**
         * Group: shayari Section
         */
        $this->start_controls_section( 'shayari_section', [
            'label' => esc_html__( 'Shayari', 'ctc' ),
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
                '{{WRAPPER}} .ctc-shayari-text p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_control( 'shayari', [
            'label'   => esc_html__( 'Shayari', 'ctc' ),
            'type'    => Controls_Manager::WYSIWYG,
            'default' => $default,
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Shayari', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, ['Shayari Box', 'Shayari'] );
        Helpers::register_copy_button_style_section( $this );
    }

}
