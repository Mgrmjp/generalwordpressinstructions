<?php

$title = isset($attributes['title']) ? (string) $attributes['title'] : '';
$steps = isset($attributes['steps']) && is_array($attributes['steps']) ? $attributes['steps'] : [];

if (empty($steps)) {
    return;
}

$post_id = gwi_get_instruction_post_id_from_block($block ?? null);
$language = ($post_id > 0 && function_exists('gwi_get_instruction_language'))
    ? gwi_get_instruction_language($post_id)
    : '';
$subtitle = $language === 'fi'
    ? __('Suorita vaiheet järjestyksessä. Merkitse valmiiksi kun olet tehnyt kohdan.', 'general-wp-instructions')
    : __('Work through these steps in order. Mark each one done when you finish it.', 'general-wp-instructions');
?>
<section <?php echo get_block_wrapper_attributes(['class' => 'gwi-step-list']); ?>>
    <?php if ($title !== '') : ?>
        <header class="gwi-step-list__header">
            <h2 class="gwi-step-list__title"><?php echo esc_html(wp_strip_all_tags($title)); ?></h2>
            <p class="gwi-step-list__subtitle"><?php echo esc_html($subtitle); ?></p>
        </header>
    <?php endif; ?>
    <ol class="gwi-step-list__items">
        <?php foreach ($steps as $index => $step) : ?>
            <?php
            $text = is_array($step) && isset($step['text']) ? (string) $step['text'] : '';

            if ($text === '') {
                continue;
            }

            $step_number = $index + 1;
            ?>
            <li class="gwi-step-list__item" data-step="<?php echo esc_attr((string) $step_number); ?>">
                <span class="gwi-step-list__marker" aria-hidden="true"><?php echo esc_html((string) $step_number); ?></span>
                <span class="gwi-step-list__text"><?php echo wp_kses_post($text); ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
