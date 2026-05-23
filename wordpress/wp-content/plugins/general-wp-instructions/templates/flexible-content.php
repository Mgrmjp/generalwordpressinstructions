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
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy">
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
            <section class="gwi-step-list">
                <h2 class="gwi-step-list__title"><?php echo esc_html((string) get_sub_field('heading')); ?></h2>
                <?php if (have_rows('items')) : ?>
                    <ol class="gwi-step-list__items">
                        <?php while (have_rows('items')) : ?>
                            <?php the_row(); ?>
                            <li class="gwi-step-list__item">
                                <span><?php echo esc_html((string) get_sub_field('text')); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ol>
                <?php endif; ?>
            </section>
        <?php elseif (get_row_layout() === 'callout') : ?>
            <?php
            $variant = (string) get_sub_field('variant');
            $variant = in_array($variant, ['note', 'warning', 'success'], true) ? $variant : 'note';
            $content = (string) get_sub_field('content');
            ?>
            <aside class="<?php echo esc_attr('gwi-flexible-callout gwi-flexible-callout--' . $variant); ?>">
                <?php echo wp_kses_post(wpautop($content)); ?>
            </aside>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
