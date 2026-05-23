<?php

if (!defined('ABSPATH')) {
    exit;
}

$screenshot_dir = ABSPATH . 'wp-content/uploads/instruction-screenshots';
$manifest_file = $screenshot_dir . '/.import-manifest';

if (!file_exists($manifest_file)) {
    echo "No import manifest found. Run import-screenshots.sh first.\n";
    exit(1);
}

$lines = file($manifest_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$updated = 0;

foreach ($lines as $line) {
    list($screenshot_id, $image_url, $language) = explode('|', $line);

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

        $new_content = str_replace(
            '"screenshotId":"' . $screenshot_id . '"',
            '"screenshotId":"' . $screenshot_id . '","imageUrl":"' . $image_url . '"',
            $post->post_content
        );

        global $wpdb;
        $result = $wpdb->update($wpdb->posts, ['post_content' => $new_content], ['ID' => $post->ID]);

        if ($result) {
            echo "Updated: {$post->post_title} (ID: {$post->ID})\n";
            $updated++;
        }
    }
}

echo "\nLinked {$updated} tutorials to screenshots.\n";

unlink($manifest_file);
