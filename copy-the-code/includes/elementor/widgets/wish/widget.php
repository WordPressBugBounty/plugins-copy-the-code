<?php

/**
 * Elementor Wish Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Wish Block
 *
 * @since 5.0.0
 */
class Wish extends Widget_Base {
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
            'ctc-el-wish',
            CTC_URI . 'includes/elementor/widgets/wish/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-wish'];
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
        return 'ctc_wish';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Wish', 'ctc' );
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
        return Helpers::get_keywords( ['wish'] );
    }

    /**
     * Render
     */
    public function render() {
        $wish = $this->get_settings_for_display( 'wish' );
        if ( empty( $wish ) ) {
            return;
        }
        $wish = wpautop( $wish );
        ?>
		<div class="ctc-block ctc-wish">
			<div class="ctc-block-content">
				<div class="ctc-wish-box">
					<div class="ctc-wish-text"><?php 
        echo wp_kses_post( $wish );
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
        $default = 'Wishing you a very happy and prosperous Diwali.

May the light of Diwali fill your home with light of joy and happiness.

On this great day, I wish you a happy Diwali.';
        Helpers::register_copy_content_section( $this, [
            'default' => $default,
        ] );
        /**
         * Group: Wish Section
         */
        $this->start_controls_section( 'wish_section', [
            'label' => esc_html__( 'Wish', 'ctc' ),
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
                '{{WRAPPER}} .ctc-wish-text p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ] );
        $this->add_control( 'wish', [
            'label'   => esc_html__( 'Wish', 'ctc' ),
            'type'    => Controls_Manager::WYSIWYG,
            'default' => $default,
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Wish', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, ['Wish Box', 'Wish'] );
        Helpers::register_copy_button_style_section( $this );
    }

}
