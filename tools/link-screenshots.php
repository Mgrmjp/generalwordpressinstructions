<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/lib/screenshot-log.php';

if (!function_exists('gwi_get_instruction_screenshot_url')) {
    require_once dirname(__DIR__) . '/wordpress/wp-content/plugins/general-wp-instructions/includes/blocks.php';
}

$screenshot_dir = ABSPATH . 'wp-content/uploads/instruction-screenshots';
$manifest_file = $screenshot_dir . '/.import-manifest';

gwi_screenshot_log_init('link', $screenshot_dir);

/**
 * Replace highlighted-screenshot block JSON with normalized attributes.
 */
function gwi_replace_highlighted_screenshot_blocks(string $content, string $screenshot_id, string $image_url, int $image_id = 0): string
{
    $pattern = '/<!-- wp:general-wp-instructions\/highlighted-screenshot -->\s*(\{.*?\})\s*<!-- \/wp:general-wp-instructions\/highlighted-screenshot -->/s';

    return (string) preg_replace_callback(
        $pattern,
        static function (array $matches) use ($screenshot_id, $image_url, $image_id): string {
            $attributes = json_decode($matches[1], true);

            if (!is_array($attributes) || (($attributes['screenshotId'] ?? '') !== $screenshot_id)) {
                return $matches[0];
            }

            $attributes['imageUrl'] = $image_url;

            if ($image_id > 0) {
                $attributes['imageId'] = $image_id;
            }

            $json = wp_json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return '<!-- wp:general-wp-instructions/highlighted-screenshot -->'
                . $json
                . '<!-- /wp:general-wp-instructions/highlighted-screenshot -->';
        },
        $content
    );
}

if (!file_exists($manifest_file)) {
    gwi_screenshot_log('error', 'Import manifest not found', [
        'manifest' => $manifest_file,
    ]);
    gwi_screenshot_log_finish('failed');
    exit(1);
}

$lines = file($manifest_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

gwi_screenshot_log('info', 'Link run started', [
    'manifest' => $manifest_file,
    'entryCount' => count($lines),
    'jsonlPath' => $GLOBALS['gwi_screenshot_log']['jsonl_path'],
]);

$updated_posts = 0;
$skipped_posts = 0;
$failed_entries = 0;

foreach ($lines as $line) {
    $parts = explode('|', $line, 4);

    if (count($parts) < 3) {
        $failed_entries++;
        gwi_screenshot_log('warn', 'Skipped invalid manifest entry', [
            'line' => $line,
        ]);
        gwi_screenshot_log_count('warnings');
        continue;
    }

    [$screenshot_id, $manifest_url, $language] = $parts;
    $image_id = isset($parts[3]) ? (int) $parts[3] : 0;

    $posts = get_posts([
        'post_type' => 'wp_instruction',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_gwi_language',
        'meta_value' => $language,
    ]);

    foreach ($posts as $post) {
        if (strpos($post->post_content, '"screenshotId":"' . $screenshot_id . '"') === false) {
            continue;
        }

        $resolved_url = function_exists('gwi_get_instruction_screenshot_url')
            ? gwi_get_instruction_screenshot_url($screenshot_id, $post->ID, $manifest_url, $language)
            : $manifest_url;

        $image_url = $resolved_url !== '' ? $resolved_url : $manifest_url;

        $new_content = gwi_replace_highlighted_screenshot_blocks(
            $post->post_content,
            $screenshot_id,
            $image_url,
            $image_id
        );

        if ($new_content === $post->post_content) {
            $skipped_posts++;
            gwi_screenshot_log_count('skipped');
            gwi_screenshot_log('info', 'Post already linked', [
                'postId' => $post->ID,
                'postTitle' => $post->post_title,
                'screenshotId' => $screenshot_id,
                'language' => $language,
            ]);
            continue;
        }

        global $wpdb;
        $result = $wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post->ID]);

        if ($result !== false) {
            clean_post_cache($post->ID);
            $updated_posts++;
            gwi_screenshot_log_count('linked');
            gwi_screenshot_log('info', 'Linked post to screenshot', [
                'postId' => $post->ID,
                'postTitle' => $post->post_title,
                'screenshotId' => $screenshot_id,
                'language' => $language,
                'attachmentId' => $image_id,
                'imageUrl' => $image_url,
            ]);
            continue;
        }

        $failed_entries++;
        gwi_screenshot_log('error', 'Failed to update post content', [
            'postId' => $post->ID,
            'postTitle' => $post->post_title,
            'screenshotId' => $screenshot_id,
            'language' => $language,
        ]);
        gwi_screenshot_log_count('errors');
    }
}

unlink($manifest_file);

$status = $failed_entries > 0 ? 'failed' : 'success';

gwi_screenshot_log('info', 'Link run finished', [
    'updatedPosts' => $updated_posts,
    'skippedPosts' => $skipped_posts,
    'failedEntries' => $failed_entries,
]);

gwi_screenshot_log_finish($status, [
    'updatedPosts' => $updated_posts,
    'skippedPosts' => $skipped_posts,
    'failedEntries' => $failed_entries,
]);

if ($status === 'failed') {
    exit(1);
}
