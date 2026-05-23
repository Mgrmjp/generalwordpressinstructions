<?php

if (!defined('ABSPATH')) {
    exit;
}

$variant = function_exists('get_field') ? (string) get_field('variant') : 'note';
$variant = in_array($variant, ['note', 'warning', 'success'], true) ? $variant : 'note';
$content = function_exists('get_field') ? (string) get_field('content') : '';
$classes = 'gwi-callout gwi-callout--' . $variant;

if ($content === '' && is_admin()) {
    $content = __('Add callout content in the block fields.', 'general-wp-instructions');
}
?>
<aside class="<?php echo esc_attr($classes); ?>">
    <?php echo wp_kses_post(wpautop($content)); ?>
</aside>
