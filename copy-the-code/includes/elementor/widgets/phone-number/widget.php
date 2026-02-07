<?php

/**
 * Elementor Phone Number Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Phone Number Block
 *
 * @since 5.0.0
 */
class PhoneNumber extends Widget_Base {
    /**
     * Constructor
     */
    public function __construct( $data = [], $args = null ) {
        parent::__construct( $data, $args );
        // Core.
        wp_enqueue_style(
            'ctc-blocks',
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
        // Block.
        wp_enqueue_style(
            'ctc-el-phone-number',
            CTC_URI . 'includes/elementor/widgets/phone-number/style.css',
            ['ctc-blocks'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-phone-number'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['ctc-el-phone-number'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_phone_number';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Phone Number', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-number-field';
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
        return Helpers::get_keywords( [
            'phone',
            'copy',
            'content',
            'number'
        ] );
    }

    /**
     * Render
     */
    public function render() {
        $phone_number = $this->get_settings( 'phone_number' );
        if ( empty( $phone_number ) ) {
            return;
        }
        ?>
		<span class="ctc-block ctc-phone-number">
			<a href="tel:<?php 
        echo esc_attr( $phone_number );
        ?>" class="ctc-block-content">
				<?php 
        echo esc_html( $phone_number );
        ?>
			</a>
			<span class="ctc-block-copy ctc-block-copy-icon" role="button" aria-label="Copied">
				<?php 
        echo Helpers::get_svg_copy_icon();
        ?>
				<?php 
        echo Helpers::get_svg_checked_icon();
        ?>
			</span>
			<?php 
        Helpers::render_copy_content( $this, [
            'content' => $phone_number,
        ] );
        ?>
		</span>
		<?php 
    }

    /**
     * Register controls
     */
    protected function _register_controls() {
        $this->start_controls_section( 'phone_number_section', [
            'label' => esc_html__( 'Phone Number', 'ctc' ),
        ] );
        $this->add_control( 'phone_number', [
            'label'   => esc_html__( 'Phone Number', 'ctc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => '+1234567890',
            'dynamic' => [
                'active' => true,
            ],
        ] );
        $this->end_controls_section();
        Helpers::register_pro_sections( $this, ['Phone Number', 'Icon'] );
    }

}
