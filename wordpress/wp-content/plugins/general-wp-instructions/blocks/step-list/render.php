<?php

$title = isset($attributes['title']) ? (string) $attributes['title'] : '';
$steps = isset($attributes['steps']) && is_array($attributes['steps']) ? $attributes['steps'] : [];

if (empty($steps)) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'gwi-step-list']); ?>>
    <?php if ($title !== '') : ?>
        <h2 class="gwi-step-list__title"><?php echo esc_html(wp_strip_all_tags($title)); ?></h2>
    <?php endif; ?>
    <ol class="gwi-step-list__items">
        <?php foreach ($steps as $step) : ?>
            <?php
            $text = is_array($step) && isset($step['text']) ? (string) $step['text'] : '';

            if ($text === '') {
                continue;
            }
            ?>
            <li class="gwi-step-list__item">
                <span><?php echo wp_kses_post($text); ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
