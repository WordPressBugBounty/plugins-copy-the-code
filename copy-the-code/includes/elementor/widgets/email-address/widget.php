<?php

/**
 * Elementor Email Address Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block\Email;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Email Address Block
 *
 * @since 5.0.0
 */
class Address extends Widget_Base {
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
            'ctc-el-email-address',
            CTC_URI . 'includes/elementor/widgets/email-address/style.css',
            ['ctc-blocks'],
            CTC_VER,
            'all'
        );
    }

    public function get_script_depends() {
        return ['ctc-el-email-address'];
    }

    public function get_style_depends() {
        return ['ctc-el-email-address'];
    }

    public function get_name() {
        return 'ctc_copy_email_address';
    }

    public function get_title() {
        return esc_html__( 'Email Address', 'ctc' );
    }

    public function get_icon() {
        return 'eicon-email-field';
    }

    public function get_categories() {
        return Helpers::get_categories();
    }

    public function get_keywords() {
        return Helpers::get_keywords( [
            'email',
            'copy',
            'content',
            'address'
        ] );
    }

    public function render() {
        $email = $this->get_settings( 'email' );
        if ( empty( $email ) ) {
            return;
        }
        ?>
		<span class="ctc-block ctc-email-address">
			<a href="mailto:<?php 
        echo esc_attr( $email );
        ?>" class="ctc-block-content">
				<?php 
        echo esc_html( $email );
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
            'content' => $email,
        ] );
        ?>
		</span>
		<?php 
    }

    protected function _register_controls() {
        $this->start_controls_section( 'email_address_section', [
            'label' => esc_html__( 'Email', 'ctc' ),
        ] );
        $this->add_control( 'email', [
            'label'   => esc_html__( 'Email Address', 'ctc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'contact@clipboard.agency',
            'dynamic' => [
                'active' => true,
            ],
        ] );
        $this->end_controls_section();
        Helpers::register_pro_sections( $this, ['Email Address', 'Icon'] );
    }

}
