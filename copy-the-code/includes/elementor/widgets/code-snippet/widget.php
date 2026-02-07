<?php

/**
 * Elementor Code Snippet Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Code Snippet Block
 *
 * @since 5.0.0
 */
class CodeSnippet extends Widget_Base {
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
        // Prism JS scripts.
        wp_register_script(
            'ctc-prism-default',
            CTC_URI . 'includes/assets/lib/prism/prism-default.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-coy',
            CTC_URI . 'includes/assets/lib/prism/prism-coy.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-dark',
            CTC_URI . 'includes/assets/lib/prism/prism-dark.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-funky',
            CTC_URI . 'includes/assets/lib/prism/prism-funky.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-okaidia',
            CTC_URI . 'includes/assets/lib/prism/prism-okaidia.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-solarizedlight',
            CTC_URI . 'includes/assets/lib/prism/prism-solarizedlight.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-tomorrow',
            CTC_URI . 'includes/assets/lib/prism/prism-tomorrow.js',
            [],
            CTC_VER,
            true
        );
        wp_register_script(
            'ctc-prism-twilight',
            CTC_URI . 'includes/assets/lib/prism/prism-twilight.js',
            [],
            CTC_VER,
            true
        );
        // Prism CSS styles.
        wp_register_style(
            'ctc-prism-default',
            CTC_URI . 'includes/assets/lib/prism/prism-default.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-coy',
            CTC_URI . 'includes/assets/lib/prism/prism-coy.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-dark',
            CTC_URI . 'includes/assets/lib/prism/prism-dark.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-funky',
            CTC_URI . 'includes/assets/lib/prism/prism-funky.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-okaidia',
            CTC_URI . 'includes/assets/lib/prism/prism-okaidia.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-solarizedlight',
            CTC_URI . 'includes/assets/lib/prism/prism-solarizedlight.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-tomorrow',
            CTC_URI . 'includes/assets/lib/prism/prism-tomorrow.css',
            [],
            CTC_VER,
            'all'
        );
        wp_register_style(
            'ctc-prism-twilight',
            CTC_URI . 'includes/assets/lib/prism/prism-twilight.css',
            [],
            CTC_VER,
            'all'
        );
        // Block.
        wp_enqueue_style(
            'ctc-el-code-snippet',
            CTC_URI . 'includes/elementor/widgets/code-snippet/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    public function get_theme() {
        return 'ctc-prism-' . apply_filters( 'ctc/elementor/code_snippet/theme', 'default' );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        $theme = $this->get_theme();
        if ( $theme ) {
            return [$theme, 'ctc-el-code-snippet'];
        }
        return ['ctc-el-code-snippet'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        $theme = $this->get_theme();
        if ( $theme ) {
            return [$theme, 'ctc-blocks-core'];
        }
        return ['ctc-blocks-core'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_code_snippet';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Code Snippet', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-code-bold';
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
        return Helpers::get_keywords( [
            'code',
            'snippet',
            'copy code',
            'copy snippet',
            'copy code snippet'
        ] );
    }

    /**
     * Render
     */
    public function render() {
        $code_snippet = $this->get_settings_for_display( 'code_snippet' );
        $language = $this->get_settings_for_display( 'language' );
        $file_name = $this->get_settings_for_display( 'file_name' );
        $theme = $this->get_theme();
        $languages = $this->get_languages();
        $language = ( isset( $languages[$language] ) ? $languages[$language] : $language );
        ?>
		<div class="ctc-block ctc-code-snippet <?php 
        echo esc_attr( $theme );
        ?>">
			<div class="ctc-code-snippet-header">
				<?php 
        if ( $file_name ) {
            ?>
					<div class='ctc-code-snippet-file-name'>
						<?php 
            echo do_shortcode( '[copy_inline text="' . esc_html( $file_name ) . '"]' );
            ?>
					</div>
				<?php 
        }
        ?>
				<div class='ctc-code-snippet-language'><?php 
        echo esc_html( $language );
        ?></div>
			</div>
			<div class="ctc-block-content">
				<pre><code class="language-<?php 
        echo esc_attr( $language );
        ?>"><?php 
        echo esc_html( $code_snippet );
        ?></code></pre>
			</div>
			<div class="ctc-block-actions">
				<?php 
        Helpers::render_copy_button( $this );
        ?>
			</div>
			<textarea class="ctc-copy-content" style="display: none;"><?php 
        echo esc_html( $code_snippet );
        ?></textarea>
		</div>
		<?php 
    }

    /**
     * Get languages
     */
    public function get_languages() {
        return [
            'markup'     => esc_html__( 'Markup', 'ctc' ),
            'html'       => esc_html__( 'HTML', 'ctc' ),
            'css'        => esc_html__( 'CSS', 'ctc' ),
            'javascript' => esc_html__( 'JavaScript', 'ctc' ),
            'php'        => esc_html__( 'PHP', 'ctc' ),
            'python'     => esc_html__( 'Python', 'ctc' ),
            'ruby'       => esc_html__( 'Ruby', 'ctc' ),
            'sass'       => esc_html__( 'Sass', 'ctc' ),
            'scss'       => esc_html__( 'SCSS', 'ctc' ),
            'sql'        => esc_html__( 'SQL', 'ctc' ),
            'bash'       => esc_html__( 'Bash', 'ctc' ),
            'c'          => esc_html__( 'C', 'ctc' ),
            'cpp'        => esc_html__( 'C++', 'ctc' ),
            'csharp'     => esc_html__( 'C#', 'ctc' ),
            'go'         => esc_html__( 'Go', 'ctc' ),
            'java'       => esc_html__( 'Java', 'ctc' ),
            'kotlin'     => esc_html__( 'Kotlin', 'ctc' ),
            'objectivec' => esc_html__( 'Objective-C', 'ctc' ),
            'swift'      => esc_html__( 'Swift', 'ctc' ),
            'typescript' => esc_html__( 'TypeScript', 'ctc' ),
            'vbnet'      => esc_html__( 'VB.Net', 'ctc' ),
        ];
    }

    /**
     * Register controls
     */
    protected function _register_controls() {
        /**
         * Group: Code Section
         */
        $this->start_controls_section( 'code_section', [
            'label' => esc_html__( 'Code Snippet', 'ctc' ),
        ] );
        $this->add_control( 'file_name', [
            'label'   => esc_html__( 'File Name', 'ctc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => 'index.js',
        ] );
        $this->add_control( 'language', [
            'label'   => esc_html__( 'Language', 'ctc' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'javascript',
            'options' => $this->get_languages(),
        ] );
        $this->add_control( 'code_snippet', [
            'label'   => esc_html__( 'Code Snippet', 'ctc' ),
            'type'    => Controls_Manager::TEXTAREA,
            'default' => 'console.log("Hello World");',
            'rows'    => 10,
        ] );
        $this->end_controls_section();
        // Copy Button Section.
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Code Snippet', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, [
            'Quote Box',
            'Message Box',
            'Quote',
            'Author'
        ] );
        Helpers::register_copy_button_style_section( $this );
    }

}
