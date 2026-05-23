<?php

$legacy_attributes = [];

if (isset($content)) {
    $decoded_attributes = json_decode(trim((string) $content), true);

    if (is_array($decoded_attributes)) {
        $legacy_attributes = $decoded_attributes;
    }
}

$image_url = isset($attributes['imageUrl']) ? (string) $attributes['imageUrl'] : '';
$screenshot_id = isset($attributes['screenshotId']) ? sanitize_file_name((string) $attributes['screenshotId']) : '';

if ($image_url === '' && isset($legacy_attributes['imageUrl'])) {
    $image_url = (string) $legacy_attributes['imageUrl'];
}

if ($screenshot_id === '' && isset($legacy_attributes['screenshotId'])) {
    $screenshot_id = sanitize_file_name((string) $legacy_attributes['screenshotId']);
}

if ($image_url === '' && $screenshot_id !== '') {
    $upload_dir = wp_get_upload_dir();
    $language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language(get_the_ID()) : '';
    $candidates = array_filter([
        $language !== '' ? $screenshot_id . '-' . $language . '.png' : '',
        $screenshot_id . '-fi.png',
        $screenshot_id . '-en.png',
        $screenshot_id . '.png',
    ]);

    foreach (array_unique($candidates) as $filename) {
        $path = trailingslashit($upload_dir['basedir']) . 'instruction-screenshots/' . $filename;

        if (file_exists($path)) {
            $image_url = trailingslashit($upload_dir['baseurl']) . 'instruction-screenshots/' . $filename;
            break;
        }
    }
}

if ($image_url === '') {
    return;
}

$language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language(get_the_ID()) : '';
$alt = isset($attributes['alt']) ? (string) $attributes['alt'] : '';
$caption = isset($attributes['caption']) ? (string) $attributes['caption'] : '';
$label = isset($attributes['label']) ? (string) $attributes['label'] : '';

if ($alt === '' && isset($legacy_attributes['alt'])) {
    $alt = (string) $legacy_attributes['alt'];
}

if ($caption === '' && isset($legacy_attributes['caption'])) {
    $caption = (string) $legacy_attributes['caption'];
}

if ($label === '' && isset($legacy_attributes['label'])) {
    $label = (string) $legacy_attributes['label'];
}

if ($alt === '' && $caption !== '') {
    $alt = wp_strip_all_tags($caption);
}

$note_label = $language === 'fi' ? __('Kuvassa', 'general-wp-instructions') : __('Screenshot', 'general-wp-instructions');
$note_hint = $language === 'fi'
    ? __('Katso keltaisella korostettua kohtaa.', 'general-wp-instructions')
    : __('Look for the yellow highlight.', 'general-wp-instructions');

$highlight_x = gwi_percent_attribute($attributes['highlightX'] ?? $legacy_attributes['highlightX'] ?? null, 62);
$highlight_y = gwi_percent_attribute($attributes['highlightY'] ?? $legacy_attributes['highlightY'] ?? null, 18);
$highlight_width = gwi_percent_attribute($attributes['highlightWidth'] ?? $legacy_attributes['highlightWidth'] ?? null, 20);
$highlight_height = gwi_percent_attribute($attributes['highlightHeight'] ?? $legacy_attributes['highlightHeight'] ?? null, 10);
$show_overlay = !isset($legacy_attributes['screenshotId'])
    || isset($legacy_attributes['label'])
    || isset($legacy_attributes['highlightX'])
    || isset($legacy_attributes['highlightY'])
    || isset($legacy_attributes['highlightWidth'])
    || isset($legacy_attributes['highlightHeight']);
$style = sprintf(
    'left:%.2f%%;top:%.2f%%;width:%.2f%%;height:%.2f%%;',
    $highlight_x,
    $highlight_y,
    $highlight_width,
    $highlight_height
);
?>
<figure <?php echo get_block_wrapper_attributes(['class' => 'gwi-highlighted-screenshot']); ?>>
    <div class="gwi-highlighted-screenshot__note">
        <strong><?php echo esc_html($note_label); ?></strong>
        <span><?php echo esc_html($caption !== '' ? wp_strip_all_tags($caption) : $note_hint); ?></span>
    </div>
    <div class="gwi-highlighted-screenshot__frame">
        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="eager" decoding="async">
        <?php if ($show_overlay) : ?>
            <span class="gwi-highlighted-screenshot__box" style="<?php echo esc_attr($style); ?>">
                <?php if ($label !== '') : ?>
                    <span class="gwi-highlighted-screenshot__label"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </div>
    <?php if ($caption !== '') : ?>
        <figcaption><?php echo esc_html($note_hint); ?></figcaption>
    <?php endif; ?>
</figure>
