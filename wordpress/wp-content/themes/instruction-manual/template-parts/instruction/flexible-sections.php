<?php

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_the_ID();
$content = isset($args['content']) ? (string) $args['content'] : '';
$language = manual_instruction_language_code($post_id);
$is_wrapped_shortcode = $content !== '';
$skip_region_header = $is_wrapped_shortcode && manual_instruction_is_flexible_demo($post_id);

if ($content === '' && function_exists('gwi_render_instruction_flexible_sections')) {
    $content = gwi_render_instruction_flexible_sections($post_id, false);
}

if ($content === '') {
    return;
}

$region_title = $language === 'en'
    ? __('Page sections (examples)', 'instruction-manual')
    : __('Sivuosiot (esimerkit)', 'instruction-manual');
?>
<section class="manual-flexible-region" id="flexible-sections" aria-labelledby="flexible-sections-heading">
    <?php if (!$skip_region_header) : ?>
        <header class="manual-flexible-region__header">
            <h2 id="flexible-sections-heading"><?php echo esc_html($region_title); ?></h2>
        </header>
    <?php endif; ?>
    <?php echo $content; ?>
</section>
