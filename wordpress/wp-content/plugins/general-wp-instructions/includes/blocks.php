<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'gwi_register_blocks');
add_filter('wp_insert_post_data', 'gwi_prepare_instruction_post_content_save', 1, 4);
add_filter('content_save_pre', 'gwi_normalize_highlighted_screenshot_block_content', 20);
add_filter('content_edit_pre', 'gwi_normalize_highlighted_screenshot_block_content', 20);
add_action('enqueue_block_assets', 'gwi_enqueue_instruction_assets');
add_action('enqueue_block_editor_assets', 'gwi_enqueue_highlighted_screenshot_editor_data', 100);
add_action('wp_enqueue_scripts', 'gwi_enqueue_instruction_frontend_assets');

/**
 * Pass screenshot base URL and post language to the highlighted-screenshot block editor script.
 */
function gwi_enqueue_highlighted_screenshot_editor_data(): void
{
    $handle = 'general-wp-instructions-highlighted-screenshot-editor-script';

    if (!wp_script_is($handle, 'enqueued')) {
        return;
    }

    $post_id = 0;

    if (isset($_GET['post'])) {
        $post_id = (int) $_GET['post'];
    }

    $language = 'en';

    if ($post_id > 0 && function_exists('gwi_get_instruction_language')) {
        $language = gwi_get_instruction_language($post_id);
    }

    $upload_dir = wp_get_upload_dir();

    wp_localize_script(
        $handle,
        'gwiHighlightedScreenshot',
        [
            'baseUrl' => trailingslashit($upload_dir['baseurl']) . 'instruction-screenshots/',
            'language' => $language,
        ]
    );
}

function gwi_register_blocks(): void
{
    if (!function_exists('register_block_type')) {
        return;
    }

    register_block_type(GWI_PLUGIN_DIR . 'blocks/step-list');
    register_block_type(GWI_PLUGIN_DIR . 'blocks/highlighted-screenshot');
}

/**
 * ACF's content_save_pre handler strips attrs from non-ACF dynamic blocks.
 *
 * content_save_pre runs before wp_insert_post_data, so restore instruction content
 * from the original post array after running the normal save filters without ACF's
 * block parser.
 *
 * @param array<string, mixed> $data
 * @param array<string, mixed> $postarr
 * @param array<string, mixed> $unsanitized_postarr
 * @return array<string, mixed>
 */
function gwi_prepare_instruction_post_content_save(
    array $data,
    array $postarr,
    array $unsanitized_postarr = []
): array
{
    if (!gwi_is_instruction_post_save($data, $postarr, $unsanitized_postarr)) {
        return $data;
    }

    if (!array_key_exists('post_content', $unsanitized_postarr)) {
        return $data;
    }

    $content = wp_unslash((string) $unsanitized_postarr['post_content']);
    $acf_parser_removed = false;

    if (function_exists('acf_parse_save_blocks') && has_filter('content_save_pre', 'acf_parse_save_blocks')) {
        remove_filter('content_save_pre', 'acf_parse_save_blocks', 5);
        $acf_parser_removed = true;
    }

    $data['post_content'] = wp_slash(apply_filters('content_save_pre', $content));

    if ($acf_parser_removed) {
        add_filter('content_save_pre', 'acf_parse_save_blocks', 5);
    }

    return $data;
}

/**
 * @param array<string, mixed> $data
 * @param array<string, mixed> $postarr
 * @param array<string, mixed> $unsanitized_postarr
 */
function gwi_is_instruction_post_save(array $data, array $postarr, array $unsanitized_postarr): bool
{
    foreach ([$data, $postarr, $unsanitized_postarr] as $candidate) {
        if (($candidate['post_type'] ?? '') === 'wp_instruction') {
            return true;
        }
    }

    $post_id = (int) ($postarr['ID'] ?? $unsanitized_postarr['ID'] ?? 0);

    return $post_id > 0 && get_post_type($post_id) === 'wp_instruction';
}

/**
 * Move legacy JSON innerHTML into block attrs so dynamic save() output matches stored markup.
 */
function gwi_normalize_highlighted_screenshot_block_content(string $content): string
{
    if ($content === '' || !function_exists('parse_blocks') || !function_exists('serialize_blocks')) {
        return $content;
    }

    $blocks = parse_blocks($content);

    if ($blocks === []) {
        return $content;
    }

    return serialize_blocks(gwi_normalize_highlighted_screenshot_blocks_list($blocks));
}

/**
 * @param array<int, array<string, mixed>> $blocks
 * @return array<int, array<string, mixed>>
 */
function gwi_normalize_highlighted_screenshot_blocks_list(array $blocks): array
{
    foreach ($blocks as $index => $block) {
        if (!empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            $blocks[$index]['innerBlocks'] = gwi_normalize_highlighted_screenshot_blocks_list($block['innerBlocks']);
        }

        if (($block['blockName'] ?? '') !== 'general-wp-instructions/highlighted-screenshot') {
            continue;
        }

        $inner_html = trim((string) ($block['innerHTML'] ?? ''));
        $attrs = is_array($block['attrs'] ?? null) ? $block['attrs'] : [];

        if ($inner_html !== '') {
            $decoded = json_decode($inner_html, true);

            if (is_array($decoded)) {
                $attrs = array_merge($attrs, $decoded);
            }
        }

        $blocks[$index]['attrs'] = $attrs;
        $blocks[$index]['innerHTML'] = '';
        $blocks[$index]['innerContent'] = [];
    }

    return $blocks;
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

/**
 * Highlight overlay defaults per screenshot id (full-viewport vs element crop).
 *
 * @return array{showOverlay: bool, highlightX?: float, highlightY?: float, highlightWidth?: float, highlightHeight?: float}
 */
function gwi_screenshot_highlight_config(string $screenshot_id): array
{
    $screenshot_id = sanitize_file_name($screenshot_id);

    if ($screenshot_id === 'page-flexible-sections' || $screenshot_id === 'instruction-flexible-sections') {
        return [
            'showOverlay' => true,
            'highlightX' => 89.67,
            'highlightY' => 2.58,
            'highlightWidth' => 9.63,
            'highlightHeight' => 2.1,
        ];
    }

    return [
        'showOverlay' => false,
    ];
}

/**
 * Default highlight label for a screenshot id when the block/row has no custom label.
 */
function gwi_screenshot_highlight_default_label(string $screenshot_id, string $language): string
{
    $screenshot_id = sanitize_file_name($screenshot_id);

    if ($screenshot_id === 'page-flexible-sections' || $screenshot_id === 'instruction-flexible-sections') {
        return $language === 'fi' ? __('Lisää osio', 'general-wp-instructions') : __('Add section', 'general-wp-instructions');
    }

    return '';
}

/**
 * Render the yellow highlight box over a screenshot frame (percent positioning).
 *
 * @param array<string, mixed> $attributes Block attrs, legacy attrs, or manual overrides.
 */
function gwi_render_screenshot_highlight_overlay(array $attributes, string $screenshot_id = '', string $label = ''): void
{
    $screenshot_id = sanitize_file_name($screenshot_id);
    $highlight_config = $screenshot_id !== '' ? gwi_screenshot_highlight_config($screenshot_id) : ['showOverlay' => true];

    if (($highlight_config['showOverlay'] ?? true) !== true) {
        return;
    }

    $has_config_coords = isset(
        $highlight_config['highlightX'],
        $highlight_config['highlightY'],
        $highlight_config['highlightWidth'],
        $highlight_config['highlightHeight']
    );

    $highlight_x = gwi_percent_attribute(
        $has_config_coords ? ($highlight_config['highlightX'] ?? null) : ($attributes['highlightX'] ?? null),
        62
    );
    $highlight_y = gwi_percent_attribute(
        $has_config_coords ? ($highlight_config['highlightY'] ?? null) : ($attributes['highlightY'] ?? null),
        18
    );
    $highlight_width = gwi_percent_attribute(
        $has_config_coords ? ($highlight_config['highlightWidth'] ?? null) : ($attributes['highlightWidth'] ?? null),
        20
    );
    $highlight_height = gwi_percent_attribute(
        $has_config_coords ? ($highlight_config['highlightHeight'] ?? null) : ($attributes['highlightHeight'] ?? null),
        10
    );

    if ($label === '' && $screenshot_id !== '') {
        $language = isset($attributes['language']) && is_string($attributes['language'])
            ? $attributes['language']
            : '';

        if ($language !== '' && function_exists('gwi_sanitize_language')) {
            $language = gwi_sanitize_language($language);
        }

        $label = gwi_screenshot_highlight_default_label($screenshot_id, $language);
    }

    $style = sprintf(
        'left:%.2f%%;top:%.2f%%;width:%.2f%%;height:%.2f%%;',
        $highlight_x,
        $highlight_y,
        $highlight_width,
        $highlight_height
    );
    ?>
    <span class="gwi-highlighted-screenshot__box" style="<?php echo esc_attr($style); ?>">
        <?php if ($label !== '') : ?>
            <span class="gwi-highlighted-screenshot__label"><?php echo esc_html($label); ?></span>
        <?php endif; ?>
    </span>
    <?php
}

function gwi_get_instruction_post_id_from_block($block): int
{
    if (isset($block) && $block instanceof WP_Block && !empty($block->context['postId'])) {
        return (int) $block->context['postId'];
    }

    $post_id = get_the_ID();

    return $post_id ? (int) $post_id : 0;
}

/**
 * Whether the current WordPress install can produce a meaningful admin screenshot for this id.
 */
function gwi_instruction_screenshot_environment_ready(string $screenshot_id): bool
{
    $screenshot_id = sanitize_file_name($screenshot_id);

    if ($screenshot_id === 'acf-field-groups') {
        return post_type_exists('acf-field-group');
    }

    if ($screenshot_id === 'page-flexible-sections' || $screenshot_id === 'instruction-flexible-sections') {
        return function_exists('have_rows') && post_type_exists('page');
    }

    if ($screenshot_id === 'seo-dashboard') {
        return defined('WPSEO_VERSION') || class_exists('WPSEO_Admin');
    }

    return true;
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

    if (!gwi_instruction_screenshot_environment_ready($screenshot_id)) {
        return $variant === 'context' ? '' : $fallback_url;
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

/**
 * Attachment ID for an imported instruction screenshot, or 0 when not available.
 */
function gwi_get_screenshot_attachment_id(string $screenshot_id, string $language): int
{
    $screenshot_id = sanitize_file_name($screenshot_id);
    $language = $language === 'fi' ? 'fi' : 'en';

    if ($screenshot_id === '') {
        return 0;
    }

    $screenshot_key = $screenshot_id . '-' . $language;
    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_gwi_screenshot_key',
        'meta_value' => $screenshot_key,
    ]);

    if (!empty($attachments[0])) {
        return (int) $attachments[0];
    }

    return gwi_import_screenshot_attachment_from_disk($screenshot_id, $language);
}

/**
 * Import a PNG from uploads/instruction-screenshots when screenshots were not linked yet.
 */
function gwi_import_screenshot_attachment_from_disk(string $screenshot_id, string $language): int
{
    if (!function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $upload_dir = wp_get_upload_dir();
    $source_path = trailingslashit($upload_dir['basedir']) . 'instruction-screenshots/' . $screenshot_id . '-' . $language . '.png';

    if (!is_readable($source_path)) {
        return 0;
    }

    $tmp_path = wp_tempnam($screenshot_id . '-' . $language . '.png');

    if ($tmp_path === '' || !copy($source_path, $tmp_path)) {
        return 0;
    }

    $file_array = [
        'name' => $screenshot_id . '-' . $language . '.png',
        'type' => 'image/png',
        'tmp_name' => $tmp_path,
        'error' => 0,
        'size' => (int) filesize($source_path),
    ];

    $attachment_id = media_handle_sideload($file_array, 0);

    if (is_wp_error($attachment_id)) {
        if (is_readable($tmp_path)) {
            wp_delete_file($tmp_path);
        }

        return 0;
    }

    update_post_meta((int) $attachment_id, '_gwi_screenshot_key', $screenshot_id . '-' . $language);
    update_post_meta((int) $attachment_id, '_wp_attachment_image_alt', sprintf(
        'WordPress-hallinnan kuvakaappaus: %s',
        $screenshot_id
    ));

    return (int) $attachment_id;
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
