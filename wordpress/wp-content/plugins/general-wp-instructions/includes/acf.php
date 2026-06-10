<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'gwi_register_acf_blocks');
add_filter('use_block_editor_for_post', 'gwi_flexible_demo_pages_use_classic_editor', 10, 2);
add_filter('acf/settings/load_json', 'gwi_acf_json_load_paths');
add_filter('acf/settings/save_json', 'gwi_acf_json_save_path');
add_filter('the_content', 'gwi_append_flexible_content');
add_shortcode('gwi_flexible_content', 'gwi_render_flexible_content_shortcode');

/**
 * Screenshot demo pages use the classic editor so the section list is visible below the content area.
 */
function gwi_flexible_demo_pages_use_classic_editor(bool $use_block_editor, WP_Post $post): bool
{
    if ($post->post_type !== 'page') {
        return $use_block_editor;
    }

    if ((int) get_post_meta($post->ID, '_gwi_flexible_demo', true) !== 1) {
        return $use_block_editor;
    }

    return false;
}

function gwi_register_acf_blocks(): void
{
    if (!function_exists('acf_register_block_type')) {
        return;
    }

    acf_register_block_type([
        'name' => 'gwi-callout',
        'api_version' => 3,
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
        'api_version' => 3,
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

/**
 * Admin label for a flexible layout slug (matches ACF JSON).
 */
function gwi_flexible_layout_admin_label(string $layout): string
{
    $labels = [
        'intro_text' => __('Intro text', 'general-wp-instructions'),
        'screenshot_step' => __('Screenshot step', 'general-wp-instructions'),
        'checklist' => __('Checklist', 'general-wp-instructions'),
        'callout' => __('Callout', 'general-wp-instructions'),
    ];

    return $labels[$layout] ?? $layout;
}

/**
 * Render instruction_sections flexible rows for a post.
 */
function gwi_render_instruction_flexible_sections(int $post_id = 0, bool $apply_wrapper = true): string
{
    if (!function_exists('have_rows')) {
        return '';
    }

    if ($post_id <= 0) {
        $post_id = (int) get_the_ID();
    }

    if ($post_id <= 0 || !have_rows('instruction_sections', $post_id)) {
        return '';
    }

    $GLOBALS['gwi_flexible_content_post_id'] = $post_id;
    $GLOBALS['gwi_flexible_show_layout_badges'] = (bool) apply_filters(
        'gwi_show_flexible_layout_badges',
        false,
        $post_id
    );

    ob_start();
    include GWI_PLUGIN_DIR . 'templates/flexible-content.php';
    unset($GLOBALS['gwi_flexible_content_post_id'], $GLOBALS['gwi_flexible_show_layout_badges']);

    $html = (string) ob_get_clean();

    if ($html === '') {
        return '';
    }

    if (!$apply_wrapper) {
        return $html;
    }

    $wrapped = apply_filters('gwi_flexible_content_wrapper', $html, $post_id);

    return $wrapped !== '' ? $wrapped : $html;
}

function gwi_render_flexible_content_shortcode(): string
{
    return gwi_render_instruction_flexible_sections((int) get_the_ID(), true);
}

function gwi_should_append_flexible_content_to_post(int $post_id): bool
{
    return (bool) apply_filters('gwi_should_append_flexible_content', true, $post_id);
}

function gwi_append_flexible_content(string $content): string
{
    if (!is_singular('wp_instruction') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = (int) get_the_ID();

    if (!gwi_should_append_flexible_content_to_post($post_id)) {
        return $content;
    }

    if (!function_exists('have_rows') || has_shortcode($content, 'gwi_flexible_content')) {
        return $content;
    }

    return $content . gwi_render_instruction_flexible_sections($post_id, true);
}

function gwi_acf_image_url($image): string
{
    if (is_array($image) && !empty($image['url'])) {
        return (string) $image['url'];
    }

    if (is_numeric($image)) {
        $full = (string) wp_get_attachment_image_url((int) $image, 'full');

        return $full !== '' ? $full : (string) wp_get_attachment_image_url((int) $image, 'large');
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
