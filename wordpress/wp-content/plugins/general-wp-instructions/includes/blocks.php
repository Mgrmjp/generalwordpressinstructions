<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'gwi_register_blocks');
add_action('enqueue_block_assets', 'gwi_enqueue_instruction_assets');
add_action('wp_enqueue_scripts', 'gwi_enqueue_instruction_frontend_assets');

function gwi_register_blocks(): void
{
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type(GWI_PLUGIN_DIR . 'blocks/step-list');
    register_block_type(GWI_PLUGIN_DIR . 'blocks/highlighted-screenshot');
}

function gwi_enqueue_instruction_assets(): void
{
    $dependencies = [];

    if (wp_style_is('instruction-manual-tokens', 'registered')) {
        wp_enqueue_style('instruction-manual-tokens');
        $dependencies[] = 'instruction-manual-tokens';
    }

    wp_enqueue_style(
        'gwi-instructions',
        GWI_PLUGIN_URL . 'assets/css/instructions.css',
        $dependencies,
        GWI_VERSION
    );
}

/**
 * Front-end scripts for instruction pages (lightbox, expand).
 */
function gwi_enqueue_instruction_frontend_assets(): void
{
    if (is_admin() || !is_singular('wp_instruction')) {
        return;
    }

    gwi_enqueue_instruction_assets();

    $script_path = GWI_PLUGIN_DIR . 'assets/js/screenshot-lightbox.js';

    if (!is_readable($script_path)) {
        return;
    }

    wp_enqueue_script(
        'gwi-screenshot-lightbox',
        GWI_PLUGIN_URL . 'assets/js/screenshot-lightbox.js',
        [],
        (string) filemtime($script_path),
        true
    );

    $language = function_exists('gwi_get_instruction_language')
        ? gwi_get_instruction_language((int) get_queried_object_id())
        : 'en';
    $is_finnish = $language === 'fi';

    wp_localize_script(
        'gwi-screenshot-lightbox',
        'gwiScreenshotLightbox',
        [
            'labels' => [
                'close' => $is_finnish ? 'Sulje' : 'Close',
                'detail' => $is_finnish ? 'Lähennä kohdetta' : 'Zoom target',
                'context' => $is_finnish ? 'Koko hallintapaneeli' : 'Full admin view',
                'expand' => $is_finnish ? 'Avaa suurempi kuva' : 'Open larger image',
            ],
        ]
    );
}

function gwi_percent_attribute($value, float $fallback): float
{
    if (!is_numeric($value)) {
        return $fallback;
    }

    return max(0, min(100, (float) $value));
}

function gwi_get_instruction_post_id_from_block($block): int
{
    if (isset($block) && $block instanceof WP_Block && !empty($block->context['postId'])) {
        return (int) $block->context['postId'];
    }

    $post_id = get_the_ID();

    return $post_id ? (int) $post_id : 0;
}

function gwi_get_instruction_screenshot_url(
    string $screenshot_id,
    int $post_id = 0,
    string $fallback_url = '',
    string $language = '',
    string $variant = ''
): string {
    $screenshot_id = sanitize_file_name($screenshot_id);

    if ($screenshot_id === '') {
        return $fallback_url;
    }

    if ($language === '' && $post_id > 0 && function_exists('gwi_get_instruction_language')) {
        $language = gwi_get_instruction_language($post_id);
    }

    if ($language !== '' && function_exists('gwi_sanitize_language')) {
        $language = gwi_sanitize_language($language);
    }

    $upload_dir = wp_get_upload_dir();
    $suffix = $variant === 'context' ? '-context' : '';
    $candidates = [];

    if ($language !== '') {
        $candidates[] = $screenshot_id . '-' . $language . $suffix . '.png';
    }

    if ($suffix === '') {
        $candidates[] = $screenshot_id . '-fi.png';
        $candidates[] = $screenshot_id . '-en.png';
        $candidates[] = $screenshot_id . '.png';
    } else {
        $candidates[] = $screenshot_id . '-fi' . $suffix . '.png';
        $candidates[] = $screenshot_id . '-en' . $suffix . '.png';
    }

    $candidates = array_unique(array_filter($candidates));

    foreach ($candidates as $filename) {
        $path = trailingslashit($upload_dir['basedir']) . 'instruction-screenshots/' . $filename;

        if (!is_readable($path)) {
            continue;
        }

        $url = trailingslashit($upload_dir['baseurl']) . 'instruction-screenshots/' . $filename;
        $version = filemtime($path);

        if ($version !== false) {
            $url = add_query_arg('v', (string) $version, $url);
        }

        return $url;
    }

    return $variant === 'context' ? '' : $fallback_url;
}

function gwi_get_instruction_screenshot_context_url(
    string $screenshot_id,
    int $post_id = 0,
    string $language = ''
): string {
    $detail_url = gwi_get_instruction_screenshot_url($screenshot_id, $post_id, '', $language);
    $context_url = gwi_get_instruction_screenshot_url($screenshot_id, $post_id, '', $language, 'context');

    if ($context_url === '' || $context_url === $detail_url) {
        return '';
    }

    return $context_url;
}

/**
 * @param array{
 *   detail_url: string,
 *   context_url?: string,
 *   alt: string,
 *   caption?: string,
 *   language?: string,
 *   frame_class?: string,
 *   loading?: string
 * } $args
 */
function gwi_render_expandable_screenshot(array $args): void
{
    $detail_url = isset($args['detail_url']) ? (string) $args['detail_url'] : '';
    $context_url = isset($args['context_url']) ? (string) $args['context_url'] : '';
    $alt = isset($args['alt']) ? (string) $args['alt'] : '';
    $caption = isset($args['caption']) ? (string) $args['caption'] : '';
    $language = isset($args['language']) ? (string) $args['language'] : '';
    $frame_class = isset($args['frame_class']) ? (string) $args['frame_class'] : 'gwi-screenshot-expandable__frame';
    $loading = isset($args['loading']) ? (string) $args['loading'] : 'lazy';

    if ($detail_url === '') {
        return;
    }

    if (!is_singular('wp_instruction')) {
        ?>
        <img
            src="<?php echo esc_url($detail_url); ?>"
            alt="<?php echo esc_attr($alt); ?>"
            loading="<?php echo esc_attr($loading); ?>"
            decoding="async"
        >
        <?php
        return;
    }

    $is_finnish = $language === 'fi';
    $expand_label = $is_finnish ? 'Avaa suurempi kuva' : 'Open larger image';
    $has_context = $context_url !== '' && $context_url !== $detail_url;
    $default_view = $has_context ? 'context' : 'detail';
    ?>
    <button
        type="button"
        class="gwi-screenshot-expandable"
        data-gwi-screenshot-expand
        data-detail-url="<?php echo esc_url($detail_url); ?>"
        <?php if ($has_context) : ?>
            data-context-url="<?php echo esc_url($context_url); ?>"
        <?php endif; ?>
        data-default-view="<?php echo esc_attr($default_view); ?>"
        <?php if ($caption !== '') : ?>
            data-caption="<?php echo esc_attr(wp_strip_all_tags($caption)); ?>"
        <?php endif; ?>
        aria-label="<?php echo esc_attr($expand_label); ?>"
    >
        <span class="<?php echo esc_attr($frame_class); ?>">
            <img
                src="<?php echo esc_url($detail_url); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                loading="<?php echo esc_attr($loading); ?>"
                decoding="async"
            >
            <span class="gwi-screenshot-expandable__badge" aria-hidden="true">
                <span class="gwi-screenshot-expandable__badge-icon"></span>
                <span class="gwi-screenshot-expandable__badge-text"><?php echo esc_html($expand_label); ?></span>
            </span>
        </span>
    </button>
    <?php
}
