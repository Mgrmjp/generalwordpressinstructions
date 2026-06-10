<?php

$legacy_attributes = [];

if (isset($content)) {
    $decoded_attributes = json_decode(trim((string) $content), true);

    if (is_array($decoded_attributes)) {
        $legacy_attributes = $decoded_attributes;
    }
}

$post_id = gwi_get_instruction_post_id_from_block($block ?? null);
$language = ($post_id > 0 && function_exists('gwi_get_instruction_language'))
    ? gwi_get_instruction_language($post_id)
    : '';
$image_url = isset($attributes['imageUrl']) ? (string) $attributes['imageUrl'] : '';
$screenshot_id = isset($attributes['screenshotId']) ? sanitize_file_name((string) $attributes['screenshotId']) : '';
$alt = isset($attributes['alt']) ? (string) $attributes['alt'] : '';
$caption = isset($attributes['caption']) ? (string) $attributes['caption'] : '';
$label = isset($attributes['label']) ? (string) $attributes['label'] : '';

if ($image_url === '' && isset($legacy_attributes['imageUrl'])) {
    $image_url = (string) $legacy_attributes['imageUrl'];
}

if ($screenshot_id === '' && isset($legacy_attributes['screenshotId'])) {
    $screenshot_id = sanitize_file_name((string) $legacy_attributes['screenshotId']);
}

if ($alt === '' && isset($legacy_attributes['alt'])) {
    $alt = (string) $legacy_attributes['alt'];
}

if ($caption === '' && isset($legacy_attributes['caption'])) {
    $caption = (string) $legacy_attributes['caption'];
}

if ($label === '' && isset($legacy_attributes['label'])) {
    $label = (string) $legacy_attributes['label'];
}

$context_url = '';

if ($screenshot_id !== '') {
    $resolved_url = gwi_get_instruction_screenshot_url($screenshot_id, $post_id, $image_url);

    if ($resolved_url !== '') {
        $image_url = $resolved_url;
    }

    $context_url = gwi_get_instruction_screenshot_context_url($screenshot_id, $post_id, $language);
}

$environment_ready = $screenshot_id === ''
    || gwi_instruction_screenshot_environment_ready($screenshot_id);

if ($image_url === '' && !$environment_ready && $caption !== '') {
    $missing_message = $language === 'fi'
        ? __('Kuvakaappaus ei ole saatavilla: tarvittava lisäosa (esim. ACF tai SEO) ei ole käytössä tässä ympäristössä.', 'general-wp-instructions')
        : __('Screenshot unavailable: the required plugin (e.g. ACF or SEO) is not active in this environment.', 'general-wp-instructions');
    ?>
    <figure <?php echo get_block_wrapper_attributes(['class' => 'gwi-highlighted-screenshot gwi-highlighted-screenshot--unavailable']); ?>>
        <div class="gwi-highlighted-screenshot__note">
            <strong><?php echo esc_html($language === 'fi' ? __('Kuvassa', 'general-wp-instructions') : __('Screenshot', 'general-wp-instructions')); ?></strong>
            <span><?php echo esc_html(wp_strip_all_tags($caption)); ?></span>
        </div>
        <p class="gwi-highlighted-screenshot__unavailable"><?php echo esc_html($missing_message); ?></p>
    </figure>
    <?php
    return;
}

if ($image_url === '') {
    return;
}

if ($alt === '' && $caption !== '') {
    $alt = wp_strip_all_tags($caption);
}

$note_label = $language === 'fi' ? __('Kuvassa', 'general-wp-instructions') : __('Screenshot', 'general-wp-instructions');
$highlight_config = $screenshot_id !== '' ? gwi_screenshot_highlight_config($screenshot_id) : ['showOverlay' => true];
$show_overlay = ($highlight_config['showOverlay'] ?? true) === true;
$show_figcaption = $caption !== '' && $screenshot_id === '';

$note_hint = $language === 'fi'
    ? __('Kohde on merkitty kehyksellä kuvassa.', 'general-wp-instructions')
    : __('The target control is outlined in the image.', 'general-wp-instructions');

if ($label === '' && $screenshot_id !== '') {
    $label = gwi_screenshot_highlight_default_label($screenshot_id, $language);
}

$overlay_attributes = array_merge(
    $legacy_attributes,
    $attributes,
    ['language' => $language]
);
?>
<figure <?php echo get_block_wrapper_attributes(['class' => 'gwi-highlighted-screenshot']); ?>>
    <div class="gwi-highlighted-screenshot__note">
        <strong><?php echo esc_html($note_label); ?></strong>
        <span><?php echo esc_html($caption !== '' ? wp_strip_all_tags($caption) : $note_hint); ?></span>
    </div>
    <div class="gwi-highlighted-screenshot__frame">
        <?php
        gwi_render_expandable_screenshot([
            'detail_url' => $image_url,
            'context_url' => $context_url,
            'alt' => $alt,
            'caption' => $caption,
            'language' => $language,
            'frame_class' => 'gwi-highlighted-screenshot__expand',
            'loading' => 'eager',
        ]);
        ?>
        <?php if ($show_overlay) : ?>
            <?php gwi_render_screenshot_highlight_overlay($overlay_attributes, $screenshot_id, $label); ?>
        <?php endif; ?>
    </div>
    <?php if ($show_figcaption) : ?>
        <figcaption><?php echo esc_html($note_hint); ?></figcaption>
    <?php endif; ?>
</figure>
