<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('have_rows') || !have_rows('instruction_sections')) {
    return;
}
?>
<div class="gwi-flexible-content">
    <?php while (have_rows('instruction_sections')) : ?>
        <?php the_row(); ?>
        <?php if (get_row_layout() === 'intro_text') : ?>
            <?php
            $heading = (string) get_sub_field('heading');
            $body = (string) get_sub_field('body');
            ?>
            <section class="gwi-flexible-intro">
                <?php if ($heading !== '') : ?>
                    <h2><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>
                <?php echo wp_kses_post(wpautop($body)); ?>
            </section>
        <?php elseif (get_row_layout() === 'screenshot_step') : ?>
            <?php
            $image = get_sub_field('image');
            $image_url = gwi_acf_image_url($image);
            $image_alt = gwi_acf_image_alt($image);
            $action = (string) get_sub_field('action');
            $highlight_label = (string) get_sub_field('highlight_label');
            ?>
            <?php if ($image_url !== '') : ?>
                <figure class="gwi-flexible-screenshot">
                    <div class="gwi-flexible-screenshot__frame">
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
            <?php endif; ?>
        <?php elseif (get_row_layout() === 'checklist') : ?>
            <?php
            $checklist_language = get_the_ID() > 0 && function_exists('gwi_get_instruction_language')
                ? gwi_get_instruction_language(get_the_ID())
                : '';
            $checklist_subtitle = $checklist_language === 'fi'
                ? __('Suorita vaiheet järjestyksessä.', 'general-wp-instructions')
                : __('Complete these steps in order.', 'general-wp-instructions');
            ?>
            <section class="gwi-step-list">
                <header class="gwi-step-list__header">
                    <h2 class="gwi-step-list__title"><?php echo esc_html((string) get_sub_field('heading')); ?></h2>
                    <p class="gwi-step-list__subtitle"><?php echo esc_html($checklist_subtitle); ?></p>
                </header>
                <?php if (have_rows('items')) : ?>
                    <ol class="gwi-step-list__items">
                        <?php
                        $step_index = 0;
                        while (have_rows('items')) :
                            the_row();
                            $step_index++;
                            ?>
                            <li class="gwi-step-list__item" data-step="<?php echo esc_attr((string) $step_index); ?>">
                                <span class="gwi-step-list__marker" aria-hidden="true"><?php echo esc_html((string) $step_index); ?></span>
                                <span class="gwi-step-list__text"><?php echo esc_html((string) get_sub_field('text')); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ol>
                <?php endif; ?>
            </section>
        <?php elseif (get_row_layout() === 'callout') : ?>
            <?php
            $variant = (string) get_sub_field('variant');
            $content = (string) get_sub_field('content');
            $language = get_the_ID() > 0 && function_exists('gwi_get_instruction_language')
                ? gwi_get_instruction_language(get_the_ID())
                : '';
            gwi_render_callout($variant, $content, $language);
            ?>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
