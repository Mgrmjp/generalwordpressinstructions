<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'gwi_register_acf_blocks');
add_filter('acf/settings/load_json', 'gwi_acf_json_load_paths');
add_filter('acf/settings/save_json', 'gwi_acf_json_save_path');
add_filter('the_content', 'gwi_append_flexible_content');
add_shortcode('gwi_flexible_content', 'gwi_render_flexible_content_shortcode');

function gwi_register_acf_blocks(): void
{
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'gwi-callout',
        'title' => __('Instruction Callout', 'general-wp-instructions'),
        'description' => __('A highlighted note, warning, or tip inside an instruction.', 'general-wp-instructions'),
        'category' => 'text',
        'icon' => 'info-outline',
        'keywords' => ['instruction', 'note', 'tip'],
        'render_template' => GWI_PLUGIN_DIR . 'templates/acf-block-callout.php',
        'supports' => [
            'align' => false,
            'mode' => false,
        ],
    ]);

    acf_register_block_type([
        'name' => 'gwi-screenshot-step',
        'title' => __('Screenshot Step', 'general-wp-instructions'),
        'description' => __('A screenshot with a written action for instruction pages.', 'general-wp-instructions'),
        'category' => 'media',
        'icon' => 'format-image',
        'keywords' => ['instruction', 'screenshot', 'step'],
        'render_template' => GWI_PLUGIN_DIR . 'templates/acf-block-screenshot-step.php',
        'supports' => [
            'align' => ['wide', 'full'],
            'mode' => false,
        ],
    ]);
}

function gwi_acf_json_load_paths(array $paths): array
{
    $paths[] = GWI_PLUGIN_DIR . 'acf-json';

    return $paths;
}

function gwi_acf_json_save_path(string $path): string
{
    return GWI_PLUGIN_DIR . 'acf-json';
}

function gwi_render_flexible_content_shortcode(): string
{
    if (!function_exists('have_rows')) {
        return '';
    }

    ob_start();
    include GWI_PLUGIN_DIR . 'templates/flexible-content.php';

    return (string) ob_get_clean();
}

function gwi_append_flexible_content(string $content): string
{
    if (!is_singular('wp_instruction') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (!function_exists('have_rows') || has_shortcode($content, 'gwi_flexible_content')) {
        return $content;
    }

    return $content . gwi_render_flexible_content_shortcode();
}

function gwi_acf_image_url($image): string
{
    if (is_array($image) && !empty($image['url'])) {
        return (string) $image['url'];
    }

    if (is_numeric($image)) {
        return (string) wp_get_attachment_image_url((int) $image, 'large');
    }

    return is_string($image) ? $image : '';
}

function gwi_acf_image_alt($image): string
{
    if (is_array($image) && isset($image['alt'])) {
        return (string) $image['alt'];
    }

    if (is_numeric($image)) {
        return (string) get_post_meta((int) $image, '_wp_attachment_image_alt', true);
    }

    return '';
}
