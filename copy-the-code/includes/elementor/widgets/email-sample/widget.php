<?php

/**
 * Elementor Email Sample Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block\Email;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Email Sample Block
 *
 * @since 5.0.0
 */
class Sample extends Widget_Base {
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
            'ctc-el-email-sample',
            CTC_URI . 'includes/elementor/widgets/email-sample/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-email-sample'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['ctc-blocks-core'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_copy_email_sample';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Email Sample', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-facebook-comments';
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
            'email',
            'copy',
            'content',
            'template'
        ] );
    }

    /**
     * Render
     */
    public function render() {
        $sample_email = $this->get_settings_for_display( 'sample_email' );
        if ( empty( $sample_email ) ) {
            return;
        }
        $display_content = preg_replace( '/\\[([^\\]]*)\\]/', '<span class="ctc-email-highlight">[$1]</span>', $sample_email );
        $display_content = wpautop( $display_content );
        ?>
		<div class="ctc-block ctc-email-sample">
			<div class="ctc-block-content">
				<?php 
        echo wp_kses_post( $display_content );
        ?>
			</div>
			<div class="ctc-block-actions">
				<?php 
        Helpers::render_copy_button( $this );
        ?>
			</div>
			<?php 
        Helpers::render_copy_content( $this, [
            'content' => wp_kses_post( $sample_email ),
        ] );
        ?>
		</div>
		<?php 
    }

    /**
     * Register controls
     */
    protected function _register_controls() {
        /**
         * Group: Email Section
         */
        $this->start_controls_section( 'email_section', [
            'label' => esc_html__( 'Email Sample', 'ctc' ),
        ] );
        $this->add_control( 'sample_email', [
            'label'       => esc_html__( 'Email Sample', 'ctc' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => "Subject: Application for [Job Title] - [Your Name]\n\nDear [Hiring Manager's Name],\n\nI hope this email finds you well. I am writing to express my strong interest in the [Job Title] position at [Company Name], as advertised on your website. With my background in [Relevant Skill/Experience] and a passion for [Company's Mission or Industry], I believe I am a strong fit for your team.\n\nSincerely,\n[Your Name]\n[Your Contact Information]",
            'rows'        => 10,
            'description' => esc_html__( 'Use [ ] to highlight the text.', 'ctc' ),
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this );
        Helpers::register_pro_sections( $this, ['Email Sample', 'Highlight Text'] );
        Helpers::register_copy_button_style_section( $this );
    }

}
