<?php

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($GLOBALS['gwi_flexible_content_post_id'])
    ? (int) $GLOBALS['gwi_flexible_content_post_id']
    : (int) get_the_ID();
$show_layout_badges = !empty($GLOBALS['gwi_flexible_show_layout_badges']);

if (!function_exists('have_rows') || !have_rows('instruction_sections', $post_id)) {
    return;
}

$flexible_language = $post_id > 0 && function_exists('gwi_get_instruction_language')
    ? gwi_get_instruction_language($post_id)
    : '';
?>
<div class="gwi-flexible-content">
    <?php while (have_rows('instruction_sections', $post_id)) : ?>
        <?php the_row(); ?>
        <?php
        $layout = (string) get_row_layout();
        $layout_label = gwi_flexible_layout_admin_label($layout);
        ?>
        <?php if ($layout === 'intro_text') : ?>
            <?php
            $heading = (string) get_sub_field('heading');
            $body = (string) get_sub_field('body');
            ?>
            <section class="gwi-flexible-section gwi-flexible-intro" data-gwi-layout="intro_text">
                <?php if ($show_layout_badges) : ?>
                    <span class="gwi-flexible-layout-badge"><?php echo esc_html($layout_label); ?></span>
                <?php endif; ?>
                <?php if ($heading !== '') : ?>
                    <h2><?php echo esc_html($heading); ?></h2>
                <?php endif; ?>
                <?php echo wp_kses_post(wpautop($body)); ?>
            </section>
        <?php elseif ($layout === 'screenshot_step') : ?>
            <?php
            $image = get_sub_field('image');
            $screenshot_id = (string) get_sub_field('screenshot_id');
            $image_url = gwi_acf_image_url($image);
            $image_alt = gwi_acf_image_alt($image);

            if ($screenshot_id !== '' && function_exists('gwi_get_instruction_screenshot_url')) {
                $canonical_url = gwi_get_instruction_screenshot_url(
                    $screenshot_id,
                    $post_id,
                    '',
                    $flexible_language
                );

                if ($canonical_url !== '') {
                    $image_url = $canonical_url;
                }
            }
            $action = (string) get_sub_field('action');
            $highlight_label = (string) get_sub_field('highlight_label');
            $unavailable_message = $flexible_language === 'fi'
                ? __('Esimerkkikuvaa ei voitu näyttää. Muokkausnäkymässä näet silti saman kohdan—vieritä editorin alle osiolistaan.', 'general-wp-instructions')
                : __('Example image could not be shown. On the edit screen you will still see the same area—scroll below the editor to the section list.', 'general-wp-instructions');
            ?>
            <section class="gwi-flexible-section" data-gwi-layout="screenshot_step">
                <?php if ($show_layout_badges) : ?>
                    <span class="gwi-flexible-layout-badge"><?php echo esc_html($layout_label); ?></span>
                <?php endif; ?>
                <?php if ($image_url !== '') : ?>
                    <figure class="gwi-flexible-screenshot gwi-highlighted-screenshot">
                        <div class="gwi-flexible-screenshot__frame gwi-highlighted-screenshot__frame">
                            <?php
                            gwi_render_expandable_screenshot([
                                'detail_url' => $image_url,
                                'alt' => $image_alt,
                                'caption' => trim($highlight_label . ' ' . $action),
                                'frame_class' => 'gwi-screenshot-expandable__frame',
                                'language' => $flexible_language,
                            ]);
                            ?>
                            <?php
                            if ($screenshot_id !== '' && function_exists('gwi_render_screenshot_highlight_overlay')) {
                                gwi_render_screenshot_highlight_overlay(
                                    ['language' => $flexible_language],
                                    $screenshot_id,
                                    $highlight_label !== ''
                                        ? $highlight_label
                                        : gwi_screenshot_highlight_default_label($screenshot_id, $flexible_language)
                                );
                            }
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
                <?php else : ?>
                    <div class="gwi-flexible-screenshot gwi-flexible-screenshot--unavailable" role="note">
                        <?php if ($highlight_label !== '' || $action !== '') : ?>
                            <p class="gwi-flexible-screenshot__instruction">
                                <?php if ($highlight_label !== '') : ?>
                                    <strong><?php echo esc_html($highlight_label); ?>:</strong>
                                <?php endif; ?>
                                <?php echo esc_html($action); ?>
                            </p>
                        <?php endif; ?>
                        <p class="gwi-flexible-screenshot__unavailable"><?php echo esc_html($unavailable_message); ?></p>
                    </div>
                <?php endif; ?>
            </section>
        <?php elseif ($layout === 'checklist') : ?>
            <?php
            $checklist_subtitle = $flexible_language === 'fi'
                ? __('Suorita vaiheet järjestyksessä.', 'general-wp-instructions')
                : __('Complete these steps in order.', 'general-wp-instructions');
            ?>
            <section class="gwi-flexible-section gwi-step-list" data-gwi-layout="checklist">
                <?php if ($show_layout_badges) : ?>
                    <span class="gwi-flexible-layout-badge"><?php echo esc_html($layout_label); ?></span>
                <?php endif; ?>
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
        <?php elseif ($layout === 'callout') : ?>
            <?php
            $variant = (string) get_sub_field('variant');
            $content = (string) get_sub_field('content');
            ?>
            <div class="gwi-flexible-section gwi-flexible-callout" data-gwi-layout="callout">
                <?php if ($show_layout_badges) : ?>
                    <span class="gwi-flexible-layout-badge"><?php echo esc_html($layout_label); ?></span>
                <?php endif; ?>
                <?php gwi_render_callout($variant, $content, $flexible_language); ?>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
<?php do_action('gwi_after_flexible_content', $post_id); ?>
