<?php

/**
 * Elementor Blockquote Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Blockquote Block
 *
 * @since 5.0.0
 */
class Blockquote extends Widget_Base {
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
            'ctc-el-blockquote',
            CTC_URI . 'includes/elementor/widgets/blockquote/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-blockquote'];
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
        return 'ctc_blockquote';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Blockquote', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-blockquote';
    }

    /**
     * Get categories
     */
    public function get_categories() {
        return ['ctc'];
    }

    /**
     * Get keywords
     */
    public function get_keywords() {
        return Helpers::get_keywords( ['blockquote', 'quote'] );
    }

    /**
     * Render
     */
    public function render() {
        $blockquote = $this->get_settings_for_display( 'blockquote' );
        $author = $this->get_settings_for_display( 'author' );
        $with_icon = ( 'yes' === $this->get_settings_for_display( 'show_icon' ) ? 'with-icon' : '' );
        ?>
		<div class="ctc-block ctc-blockquote">
			<div class="ctc-block-content">
				<div class="ctc-blockquote-box">
					<div class="ctc-blockquote-message"><?php 
        echo wp_kses_post( $blockquote );
        ?></div>
					<div class="ctc-blockquote-author"><?php 
        echo esc_html( $author );
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
        // Copy Content Section.
        Helpers::register_copy_content_section( $this, [
            'default' => '"Top improve is to change; to be perfect is to change often."

— WINSTON CHURCHILL',
        ] );
        /**
         * Group: Blockquote Section
         */
        $this->start_controls_section( 'blockquote_section', [
            'label' => esc_html__( 'Blockquote', 'ctc' ),
        ] );
        $this->add_control( 'blockquote', [
            'label'   => esc_html__( 'Blockquote', 'ctc' ),
            'type'    => Controls_Manager::TEXTAREA,
            'default' => '"Top improve is to change; to be perfect is to change often."',
            'rows'    => 10,
        ] );
        $this->add_control( 'author', [
            'label'   => esc_html__( 'Author', 'ctc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => '— WINSTON CHURCHILL',
        ] );
        $this->end_controls_section();
        // Copy Button Section.
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Blockquote', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, [
            'Quote Box',
            'Message Box',
            'Quote',
            'Author'
        ] );
        // Copy Button Style Section.
        Helpers::register_copy_button_style_section( $this );
    }

}
