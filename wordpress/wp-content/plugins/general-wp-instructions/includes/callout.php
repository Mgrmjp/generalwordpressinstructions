<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Localized callout heading for instruction tone blocks.
 */
function gwi_get_callout_label(string $variant, string $language = ''): string
{
    $variant = in_array($variant, ['note', 'warning', 'success'], true) ? $variant : 'note';
    $language = $language === 'fi' ? 'fi' : 'en';

    $labels = [
        'note' => [
            'fi' => __('Tärkeää tietää', 'general-wp-instructions'),
            'en' => __('Good to know', 'general-wp-instructions'),
        ],
        'warning' => [
            'fi' => __('Varo tätä', 'general-wp-instructions'),
            'en' => __('Warning', 'general-wp-instructions'),
        ],
        'success' => [
            'fi' => __('Vinkki', 'general-wp-instructions'),
            'en' => __('Tip', 'general-wp-instructions'),
        ],
    ];

    return $labels[$variant][$language];
}

/**
 * Render a styled instruction callout with icon and tinted background.
 */
function gwi_render_callout(string $variant, string $content, string $language = ''): void
{
    if ($content === '' && !is_admin()) {
        return;
    }

    if ($content === '' && is_admin()) {
        $content = __('Add callout content in the block fields.', 'general-wp-instructions');
    }

    $variant = in_array($variant, ['note', 'warning', 'success'], true) ? $variant : 'note';
    $language = $language === 'fi' ? 'fi' : 'en';
    ?>
    <aside class="gwi-callout gwi-callout--<?php echo esc_attr($variant); ?>" role="note">
        <div class="gwi-callout__header">
            <span class="gwi-callout__icon gwi-callout__icon--<?php echo esc_attr($variant); ?>" aria-hidden="true"></span>
            <p class="gwi-callout__label"><?php echo esc_html(gwi_get_callout_label($variant, $language)); ?></p>
        </div>
        <div class="gwi-callout__body">
            <?php echo wp_kses_post(wpautop($content)); ?>
        </div>
    </aside>
    <?php
}
