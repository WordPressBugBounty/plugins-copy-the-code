<?php

/**
 * Elementor AI Prompt Generator Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block\AI\Prompt;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * AI Prompt Generator Block
 *
 * @since 5.0.0
 */
class Generator extends Widget_Base {
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
        wp_enqueue_script(
            'ctc-el-ai-prompt-generator',
            CTC_URI . 'includes/elementor/widgets/ai-prompt-generator/script.js',
            ['jquery', 'ctc-lib-core', 'ctc-blocks-core'],
            CTC_VER,
            'all'
        );
        wp_enqueue_style(
            'ctc-el-ai-prompt-generator',
            CTC_URI . 'includes/elementor/widgets/ai-prompt-generator/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-ai-prompt-generator'];
    }

    /**
     * Get style dependencies
     */
    public function get_style_depends() {
        return ['ctc-el-ai-prompt-generator'];
    }

    /**
     * Get name
     */
    public function get_name() {
        return 'ctc_ai_prompt_generator';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'AI Prompt Generator', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-ai';
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
            'chat',
            'gpt',
            'chatgpt',
            'ai',
            'prompt',
            'prompts',
            'generator'
        ] );
    }

    /**
     * Render
     */
    public function render() {
        $task = $this->get_settings_for_display( 'task' );
        $topic = $this->get_settings_for_display( 'topic' );
        $style = $this->get_settings_for_display( 'style' );
        $tone = $this->get_settings_for_display( 'tone' );
        $audience = $this->get_settings_for_display( 'audience' );
        $length = $this->get_settings_for_display( 'length' );
        $format = $this->get_settings_for_display( 'format' );
        $output_heading = $this->get_settings_for_display( 'output_heading' );
        $button_text = $this->get_settings_for_display( 'copy_button_text' );
        $button_text_copied = $this->get_settings_for_display( 'copy_button_text_copied' );
        $show_icon = $this->get_settings_for_display( 'copy_show_icon' );
        $icon_direction = $this->get_settings_for_display( 'copy_icon_direction' );
        $display_task = $this->get_settings_for_display( 'display_task' );
        $display_topic = $this->get_settings_for_display( 'display_topic' );
        $display_style = $this->get_settings_for_display( 'display_style' );
        $display_tone = $this->get_settings_for_display( 'display_tone' );
        $display_audience = $this->get_settings_for_display( 'display_audience' );
        $display_length = $this->get_settings_for_display( 'display_length' );
        $display_format = $this->get_settings_for_display( 'display_format' );
        $with_icon = ( 'yes' === $this->get_settings_for_display( 'show_icon' ) ? 'with-icon' : '' );
        ?>
		<div class="ctc-block ctc-ai-prompt-generator">
			<div class="ctc-block-fields">

				<?php 
        if ( 'yes' === $display_task ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Task', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-task" type="text" value="<?php 
            echo esc_html( $task );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_topic ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Topic', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-topic" type="text" value="<?php 
            echo esc_html( $topic );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_style ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Style', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-style" type="text" value="<?php 
            echo esc_html( $style );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_tone ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Tone', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-tone" type="text" value="<?php 
            echo esc_html( $tone );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_audience ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Audience', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-audience" type="text" value="<?php 
            echo esc_html( $audience );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_length ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Length', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-length" type="text" value="<?php 
            echo esc_html( $length );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( 'yes' === $display_format ) {
            ?>
					<div class="ctc-block-field">
						<div class="ctc-block-field-label"><?php 
            echo esc_html__( 'Format', 'ctc' );
            ?></div>
						<div class="ctc-block-field-value"><input class="ctc-ai-prompt-generator-format" type="text" value="<?php 
            echo esc_html( $format );
            ?>" /></div>
					</div>
				<?php 
        }
        ?>

			</div>
			<div class="ctc-block-content">
				<h3 class="ctc-ai-prompt-heading"><?php 
        echo esc_html( $output_heading );
        ?></h3>
				<textarea class="ctc-ai-prompt-generator-textarea" placeholder="Enter your prompt here..."></textarea>
			</div>
			<div class="ctc-block-actions">
				<?php 
        echo Helpers::get_copy_button( [
            'show_icon'          => $show_icon,
            'as_raw'             => 'yes',
            'button_text'        => $button_text,
            'button_class'       => 'ctc-ai-generator-button',
            'button_text_copied' => $button_text_copied,
            'icon_direction'     => $icon_direction,
        ] );
        ?>
			</div>
		</div>
		<?php 
    }

    /**
     * Register controls
     */
    protected function _register_controls() {
        /**
         * Group: Task
         */
        $this->start_controls_section( 'prompt_fields', [
            'label' => esc_html__( 'Prompt Fields', 'ctc' ),
        ] );
        $this->add_control( 'display_task', [
            'label'        => esc_html__( 'Display Task Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'task', [
            'label'       => esc_html__( 'Task', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Write a cold email',
            'placeholder' => esc_html__( 'E.g. Write a cold email, Write a sales email, etc.', 'ctc' ),
            'condition'   => [
                'display_task' => 'yes',
            ],
        ] );
        /**
         * Group: Topic
         */
        $this->add_control( 'display_topic', [
            'label'        => esc_html__( 'Display Topic Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'topic', [
            'label'       => esc_html__( 'Topic', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Job Application',
            'placeholder' => esc_html__( 'E.g. Job Application, Sales, etc.', 'ctc' ),
            'condition'   => [
                'display_topic' => 'yes',
            ],
        ] );
        /**
         * Group: Style
         */
        $this->add_control( 'display_style', [
            'label'        => esc_html__( 'Display Style Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'style', [
            'label'       => esc_html__( 'Style', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Business',
            'placeholder' => esc_html__( 'E.g. Academic, Business, Informative, etc.', 'ctc' ),
            'condition'   => [
                'display_style' => 'yes',
            ],
        ] );
        /**
         * Group: Tone
         */
        $this->add_control( 'display_tone', [
            'label'        => esc_html__( 'Display Tone Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'tone', [
            'label'       => esc_html__( 'Tone', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Formal',
            'placeholder' => esc_html__( 'E.g. Formal, Informal, Empathetic, Confident, etc.', 'ctc' ),
            'condition'   => [
                'display_tone' => 'yes',
            ],
        ] );
        /**
         * Group: Audience
         */
        $this->add_control( 'display_audience', [
            'label'        => esc_html__( 'Display Audience Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'audience', [
            'label'       => esc_html__( 'Audience', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Hiring Manager',
            'placeholder' => esc_html__( 'E.g. Hiring Manager, CEO, College Student, etc.', 'ctc' ),
            'condition'   => [
                'display_audience' => 'yes',
            ],
        ] );
        /**
         * Group: Length
         */
        $this->add_control( 'display_length', [
            'label'        => esc_html__( 'Display Length Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'length', [
            'label'       => esc_html__( 'Length', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => '1 paragraph',
            'placeholder' => esc_html__( 'E.g. 1 paragraph, 20 words, 500 words, etc.', 'ctc' ),
            'condition'   => [
                'display_length' => 'yes',
            ],
        ] );
        /**
         * Group: Format
         */
        $this->add_control( 'display_format', [
            'label'        => esc_html__( 'Display Format Field', 'ctc' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'ctc' ),
            'label_off'    => esc_html__( 'Hide', 'ctc' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        $this->add_control( 'format', [
            'label'       => esc_html__( 'Format', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => 'Text',
            'placeholder' => esc_html__( 'E.g. Text, HTML, JSON, Markup, etc.', 'ctc' ),
            'condition'   => [
                'display_format' => 'yes',
            ],
        ] );
        $this->end_controls_section();
        /**
         * Group: Prompt Output
         */
        $this->start_controls_section( 'prompt_output_section', [
            'label' => esc_html__( 'Prompt Output', 'ctc' ),
        ] );
        $this->add_control( 'output_heading', [
            'label'   => esc_html__( 'Heading', 'ctc' ),
            'type'    => Controls_Manager::TEXT,
            'default' => esc_html__( 'Generated prompt:', 'ctc' ),
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Prompt', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, [
            'Prompt Form',
            'Heading',
            'Input Fields',
            'Textarea'
        ] );
        Helpers::register_copy_button_style_section( $this );
    }

}
