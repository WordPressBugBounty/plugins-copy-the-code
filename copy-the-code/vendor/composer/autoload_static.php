<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb5c6444e3bb6dd35f30bbc983a860a40
{
    public static $classMap = array (
        'CTC\\Analytics' => __DIR__ . '/../..' . '/includes/class-analytics.php',
        'CTC\\CLI\\Commands' => __DIR__ . '/../..' . '/includes/cli/class-commands.php',
        'CTC\\Dashboard' => __DIR__ . '/../..' . '/includes/class-dashboard.php',
        'CTC\\Elementor' => __DIR__ . '/../..' . '/includes/elementor/class-elementor.php',
        'CTC\\Elementor\\Block\\AI\\Prompt\\Generator' => __DIR__ . '/../..' . '/includes/elementor/widgets/ai-prompt-generator/widget.php',
        'CTC\\Elementor\\Block\\Blockquote' => __DIR__ . '/../..' . '/includes/elementor/widgets/blockquote/widget.php',
        'CTC\\Elementor\\Block\\CodeSnippet' => __DIR__ . '/../..' . '/includes/elementor/widgets/code-snippet/widget.php',
        'CTC\\Elementor\\Block\\Contact\\Information' => __DIR__ . '/../..' . '/includes/elementor/widgets/contact-information/widget.php',
        'CTC\\Elementor\\Block\\CopyIcon' => __DIR__ . '/../..' . '/includes/elementor/widgets/copy-icon/widget.php',
        'CTC\\Elementor\\Block\\Copy_Button' => __DIR__ . '/../..' . '/includes/elementor/widgets/copy-button/widget.php',
        'CTC\\Elementor\\Block\\Coupon' => __DIR__ . '/../..' . '/includes/elementor/widgets/coupon/widget.php',
        'CTC\\Elementor\\Block\\Deal' => __DIR__ . '/../..' . '/includes/elementor/widgets/deal/widget.php',
        'CTC\\Elementor\\Block\\Email\\Address' => __DIR__ . '/../..' . '/includes/elementor/widgets/email-address/widget.php',
        'CTC\\Elementor\\Block\\Email\\Sample' => __DIR__ . '/../..' . '/includes/elementor/widgets/email-sample/widget.php',
        'CTC\\Elementor\\Block\\Message' => __DIR__ . '/../..' . '/includes/elementor/widgets/message/widget.php',
        'CTC\\Elementor\\Block\\Phone_Number' => __DIR__ . '/../..' . '/includes/elementor/widgets/phone-number/widget.php',
        'CTC\\Elementor\\Block\\SMS' => __DIR__ . '/../..' . '/includes/elementor/widgets/sms/widget.php',
        'CTC\\Elementor\\Block\\Shayari' => __DIR__ . '/../..' . '/includes/elementor/widgets/shayari/widget.php',
        'CTC\\Elementor\\Block\\Table' => __DIR__ . '/../..' . '/includes/elementor/widgets/table/widget.php',
        'CTC\\Elementor\\Block\\Wish' => __DIR__ . '/../..' . '/includes/elementor/widgets/wish/widget.php',
        'CTC\\Elementor\\Widgets' => __DIR__ . '/../..' . '/includes/elementor/class-widgets.php',
        'CTC\\Gutenberg' => __DIR__ . '/../..' . '/includes/gutenberg/class-gutenberg.php',
        'CTC\\Gutenberg\\Blocks' => __DIR__ . '/../..' . '/includes/gutenberg/class-blocks.php',
        'CTC\\Gutenberg\\Blocks\\Copy_Button' => __DIR__ . '/../..' . '/includes/gutenberg/blocks/copy-button/class-block.php',
        'CTC\\Gutenberg\\Blocks\\Copy_Icon' => __DIR__ . '/../..' . '/includes/gutenberg/blocks/copy-icon/class-block.php',
        'CTC\\Gutenberg\\Blocks\\Social_Share' => __DIR__ . '/../..' . '/includes/gutenberg/blocks/social-share/class-block.php',
        'CTC\\Gutenberg\\Blocks\\Term_Title' => __DIR__ . '/../..' . '/includes/gutenberg/blocks/term-title/class-block.php',
        'CTC\\Helper' => __DIR__ . '/../..' . '/includes/class-helper.php',
        'CTC\\KB' => __DIR__ . '/../..' . '/includes/class-kb.php',
        'CTC\\Modules' => __DIR__ . '/../..' . '/includes/class-modules.php',
        'CTC\\Page' => __DIR__ . '/../..' . '/includes/class-page.php',
        'CTC\\Pro' => __DIR__ . '/../..' . '/premium/class-ctc-pro.php',
        'CTC\\Pro\\Gutenberg\\Blocks\\Shayari_Card' => __DIR__ . '/../..' . '/premium/modules/shayari/gutenberg/shayari-card/class-block.php',
        'CTC\\Pro\\Gutenberg\\Blocks\\Shayari_List' => __DIR__ . '/../..' . '/premium/modules/shayari/gutenberg/shayari-list/class-block.php',
        'CTC\\Pro\\Shayari' => __DIR__ . '/../..' . '/premium/modules/shayari/class-ctc-pro-shayari.php',
        'CTC\\Shortcode' => __DIR__ . '/../..' . '/includes/class-shortcode.php',
        'CTC\\Update' => __DIR__ . '/../..' . '/includes/class-update.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitb5c6444e3bb6dd35f30bbc983a860a40::$classMap;

        }, null, ClassLoader::class);
    }
}
