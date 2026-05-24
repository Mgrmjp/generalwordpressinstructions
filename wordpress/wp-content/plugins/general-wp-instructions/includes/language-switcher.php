<?php

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'gwi_prepend_language_switcher');

function gwi_prepend_language_switcher(string $content): string
{
    if (!is_singular('wp_instruction') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (wp_get_theme()->get_stylesheet() === 'instruction-manual') {
        return $content;
    }

    return gwi_render_language_switcher(get_the_ID()) . $content;
}

function gwi_render_language_switcher(?int $post_id = null): string
{
    $post_id = $post_id ?: get_the_ID();

    if (!$post_id || get_post_type($post_id) !== 'wp_instruction') {
        return '';
    }

    $current_language = gwi_get_instruction_language($post_id);
    $translation_id = gwi_get_translation_id($post_id);
    $items = [
        $current_language => [
            'label' => gwi_languages()[$current_language] ?? __('English', 'general-wp-instructions'),
            'url' => get_permalink($post_id),
            'active' => true,
            'available' => true,
        ],
    ];

    if ($translation_id) {
        $translation_language = gwi_get_instruction_language($translation_id);
        $items[$translation_language] = [
            'label' => gwi_languages()[$translation_language] ?? $translation_language,
            'url' => get_permalink($translation_id),
            'active' => false,
            'available' => get_post_status($translation_id) === 'publish',
        ];
    }

    foreach (gwi_languages() as $code => $label) {
        if (!isset($items[$code])) {
            $items[$code] = [
                'label' => $label,
                'url' => '',
                'active' => false,
                'available' => false,
            ];
        }
    }

    ob_start();
    ?>
    <nav class="gwi-language-switcher" aria-label="<?php esc_attr_e('Instruction language', 'general-wp-instructions'); ?>">
        <span class="gwi-language-switcher__label"><?php esc_html_e('Language', 'general-wp-instructions'); ?></span>
        <?php foreach ($items as $code => $item) : ?>
            <?php if ($item['available']) : ?>
                <a class="gwi-language-switcher__item<?php echo $item['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url($item['url']); ?>" lang="<?php echo esc_attr($code); ?>" <?php echo $item['active'] ? 'aria-current="page"' : ''; ?>>
                    <?php echo esc_html($item['label']); ?>
                </a>
            <?php else : ?>
                <span class="gwi-language-switcher__item is-disabled" lang="<?php echo esc_attr($code); ?>" aria-disabled="true">
                    <?php echo esc_html($item['label']); ?>
                </span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php

    return (string) ob_get_clean();
}
