<?php

/**
 * Elementor Contact Information Block
 *
 * @package CTC
 * @since 5.0.0
 */
namespace CTC\Elementor\Block;

use CTC\Helpers;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Contact Information Block
 *
 * @since 5.0.0
 */
class ContactInformation extends Widget_Base {
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
            'ctc-el-contact-information',
            CTC_URI . 'includes/elementor/widgets/contact-information/style.css',
            ['ctc-blocks-core'],
            CTC_VER,
            'all'
        );
    }

    /**
     * Get script dependencies
     */
    public function get_script_depends() {
        return ['ctc-el-contact-information'];
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
        return 'ctc_contact_information';
    }

    /**
     * Get title
     */
    public function get_title() {
        return esc_html__( 'Contact Information', 'ctc' );
    }

    /**
     * Get icon
     */
    public function get_icon() {
        return 'eicon-menu-toggle';
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
            'contact',
            'information',
            'copy',
            'content',
            'template'
        ] );
    }

    /**
     * Get SVG icon
     */
    public function get_svg_icon( $link = '' ) {
        if ( strpos( $link, 'facebook' ) !== false ) {
            return Helpers::get_svg_facebook_icon();
        }
        if ( strpos( $link, 'twitter' ) !== false ) {
            return Helpers::get_svg_twitter_icon();
        }
        if ( strpos( $link, 'linkedin' ) !== false ) {
            return Helpers::get_svg_linkedin_icon();
        }
        if ( strpos( $link, 'pinterest' ) !== false ) {
            return Helpers::get_svg_pinterest_icon();
        }
        if ( strpos( $link, 'whatsapp' ) !== false ) {
            return Helpers::get_svg_whatsapp_icon();
        }
        return Helpers::get_svg_link_icon();
    }

    /**
     * Render
     */
    public function render() {
        $contact_heading = $this->get_settings_for_display( 'contact_heading' );
        $contact_fields = $this->get_settings_for_display( 'contact_fields' );
        $social_heading = $this->get_settings_for_display( 'social_heading' );
        $social_profiles = $this->get_settings_for_display( 'social_profiles' );
        if ( empty( $contact_fields ) && empty( $social_profiles ) && empty( $contact_heading ) && empty( $social_heading ) ) {
            return;
        }
        ?>
		<div class="ctc-block ctc-contact-information">
			<div class="ctc-block-content">

				<?php 
        if ( !empty( $contact_heading ) ) {
            ?>
					<div class="ctc-block-heading ctc-contact-heading">
						<?php 
            echo esc_html( $contact_heading );
            ?>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( !empty( $contact_fields ) ) {
            ?>
					<div class="ctc-block-fields">
						<?php 
            foreach ( $contact_fields as $contact_field ) {
                ?>
							<div class="ctc-block-field">
								<div class="ctc-block-field-label">
									<?php 
                echo esc_html( $contact_field['label'] );
                ?>
								</div>
								<div class="ctc-block-field-value">
									<?php 
                echo do_shortcode( '[copy_inline text="' . esc_html( $contact_field['value'] ) . '"]' );
                ?>
								</div>
							</div>
						<?php 
            }
            ?>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( !empty( $social_heading ) ) {
            ?>
					<div class="ctc-block-heading ctc-social-heading">
						<?php 
            echo esc_html( $social_heading );
            ?>
					</div>
				<?php 
        }
        ?>

				<?php 
        if ( !empty( $social_profiles ) ) {
            ?>
					<div class="ctc-block-social">
						<?php 
            foreach ( $social_profiles as $social_profile ) {
                ?>
							<div class="ctc-block-social-profile-link">
								<a href="<?php 
                echo esc_url( $social_profile['link'] );
                ?>" target="_blank" rel="nofollow noopener noreferrer">
									<?php 
                echo $this->get_svg_icon( $social_profile['link'] );
                ?>
								</a>
							</div>
						<?php 
            }
            ?>
					</div>
				<?php 
        }
        ?>

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
        $default = 'Contact Information

Name: John Doe
Email: contact@clipboard.agency
Phone: +1 123 456 7890
Address: 123 Street, City, Country

Social Profiles: 

https://facebook.com
https://twitter.com
https://linkedin.com';
        Helpers::register_copy_content_section( $this, [
            'default' => $default,
        ] );
        /**
         * Group: Contact Information Section
         */
        $this->start_controls_section( 'fields_section', [
            'label' => esc_html__( 'Contact Information', 'ctc' ),
        ] );
        // Contact fields.
        $this->add_control( 'contact_heading', [
            'label'       => esc_html__( 'Heading', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__( 'Contact Information', 'ctc' ),
            'placeholder' => esc_html__( 'Enter your contact fields heading', 'ctc' ),
        ] );
        // Contact fields.
        $this->add_control( 'contact_fields', [
            'label'       => esc_html__( 'Contact Fields', 'ctc' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => [[
                'name'        => 'label',
                'label'       => esc_html__( 'Label', 'ctc' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ], [
                'name'        => 'value',
                'label'       => esc_html__( 'Value', 'ctc' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ]],
            'default'     => [
                [
                    'label' => esc_html__( 'Name: ', 'ctc' ),
                    'value' => 'John Doe',
                ],
                [
                    'label' => esc_html__( 'Email: ', 'ctc' ),
                    'value' => 'contact@clipboard.agency',
                ],
                [
                    'label' => esc_html__( 'Phone: ', 'ctc' ),
                    'value' => '+1 123 456 7890',
                ],
                [
                    'label' => esc_html__( 'Address: ', 'ctc' ),
                    'value' => '123 Street, City, Country',
                ]
            ],
            'title_field' => '{{{ name }}}',
        ] );
        $this->end_controls_section();
        /**
         * Group: Social Profiles Section
         */
        $this->start_controls_section( 'social_profiles_section', [
            'label' => esc_html__( 'Social Profiles', 'ctc' ),
        ] );
        // Social profiles.
        $this->add_control( 'social_heading', [
            'label'       => esc_html__( 'Heading', 'ctc' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__( 'Social Profiles', 'ctc' ),
            'placeholder' => esc_html__( 'Enter your social profiles heading', 'ctc' ),
        ] );
        // Social profiles.
        $this->add_control( 'social_profiles', [
            'label'       => esc_html__( 'Social Profiles', 'ctc' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => [[
                'name'        => 'link',
                'label'       => esc_html__( 'Link', 'ctc' ),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ]],
            'default'     => [[
                'link' => 'https://facebook.com',
            ], [
                'link' => 'https://twitter.com',
            ], [
                'link' => 'https://linkedin.com',
            ]],
            'title_field' => '{{{ name }}}',
        ] );
        $this->end_controls_section();
        Helpers::register_copy_button_section( $this, [
            'button_text' => esc_html__( 'Copy Contact Information', 'ctc' ),
        ] );
        Helpers::register_pro_sections( $this, [
            'Box',
            'Contact Heading',
            'Contact Fields',
            'Contact Field',
            'Field Label',
            'Field Value',
            'Social Heading',
            'Social Icon'
        ] );
        Helpers::register_copy_button_style_section( $this );
    }

}
