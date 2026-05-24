<?php

if (!defined('ABSPATH')) {
    exit;
}

$image = function_exists('get_field') ? get_field('image') : null;
$image_url = gwi_acf_image_url($image);
$image_alt = gwi_acf_image_alt($image);
$action = function_exists('get_field') ? (string) get_field('action') : '';
$highlight_label = function_exists('get_field') ? (string) get_field('highlight_label') : '';

if ($image_url === '' && is_admin()) {
    echo '<div class="gwi-editor-screenshot-placeholder"><p>' . esc_html__('Choose a screenshot in the block fields.', 'general-wp-instructions') . '</p></div>';
    return;
}

if ($image_url === '') {
    return;
}
?>
<figure class="gwi-acf-screenshot-step">
    <div class="gwi-acf-screenshot-step__frame">
        <?php
        gwi_render_expandable_screenshot([
            'detail_url' => $image_url,
            'alt' => $image_alt,
            'caption' => trim($highlight_label . ' ' . $action),
            'frame_class' => 'gwi-screenshot-expandable__frame',
        ]);
        ?>
    </div>
    <?php if ($action !== '' || $highlight_label !== '') : ?>
        <figcaption>
            <?php if ($highlight_label !== '') : ?>
                <strong><?php echo esc_html($highlight_label); ?>:</strong>
            <?php endif; ?>
            <?php echo esc_html($action); ?>
        </figcaption>
    <?php endif; ?>
</figure>
