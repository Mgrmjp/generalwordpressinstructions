<?php get_header(); ?>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>
    <?php
    $post_id = get_the_ID();
    $purpose = manual_instruction_purpose($post_id);
    $category = manual_instruction_primary_category($post_id);
    $related_instructions = manual_related_instructions($post_id);
    $language_label = manual_instruction_language_display_label($post_id);
    $reviewed_month = manual_reviewed_month_label_fi($post_id);
    $instruction_language = manual_instruction_language_code($post_id);
    $is_english = $instruction_language === 'en';
    $text = static function (string $key) use ($post_id): string {
        return manual_instruction_single_text($post_id, $key);
    };
    $english_version = manual_get_english_version($post_id);
    $finnish_version = null;
    if ($is_english) {
        $pair = manual_get_translation_pair($post_id);
        if ($pair) {
            $finnish_version = get_post($pair);
        }
    }
    if (!$is_english && $english_version instanceof WP_Post && $english_version->ID === $post_id) {
        $english_version = null;
    }
    $manual_contents_terms = get_terms([
        'taxonomy' => 'instruction_category',
        'hide_empty' => true,
    ]);
    $current_category_slug = $category instanceof WP_Term ? $category->slug : '';
    ?>
    <div class="manual-doc-workspace">
        <aside class="manual-doc-contents" aria-label="<?php echo esc_attr($text('manual_contents')); ?>">
            <p class="manual-doc-contents__title"><?php echo esc_html($text('manual_contents')); ?></p>
            <nav>
                <a href="<?php echo esc_url(manual_instruction_url_with_language(manual_instruction_archive_url())); ?>"><?php echo esc_html($text('all_guides')); ?></a>
                <?php if (!is_wp_error($manual_contents_terms) && !empty($manual_contents_terms)) : ?>
                    <?php foreach ($manual_contents_terms as $manual_term) : ?>
                        <?php
                        $manual_term_url = get_term_link($manual_term);

                        if (is_wp_error($manual_term_url)) {
                            continue;
                        }
                        ?>
                        <a href="<?php echo esc_url(manual_instruction_url_with_language($manual_term_url)); ?>" class="<?php echo $current_category_slug === $manual_term->slug ? 'is-active' : ''; ?>"><?php echo esc_html(manual_instruction_category_label($manual_term)); ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </aside>

        <article <?php post_class('manual-document'); ?>>
            <header class="manual-document__header">
                <p class="manual-eyebrow"><?php echo esc_html($text('eyebrow')); ?></p>
                <h1 class="manual-article__title"><?php the_title(); ?></h1>
                <p class="manual-document__purpose"><?php echo esc_html($purpose); ?></p>

                <div class="manual-document__meta-badges">
                    <span class="manual-badge manual-badge--language"><?php echo esc_html($language_label); ?></span>
                    <?php if ($reviewed_month) : ?>
                        <span class="manual-badge manual-badge--review <?php echo manual_instruction_review_status_is_current($post_id) ? '' : 'is-stale'; ?>"><?php echo esc_html($reviewed_month); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($is_english && $finnish_version) : ?>
                    <div class="manual-translation-notice">
                        <?php printf(__('Also available in <a href="%s">Finnish</a>.', 'instruction-manual'), esc_url(get_permalink($finnish_version))); ?>
                    </div>
                <?php elseif ($is_english) : ?>
                    <div class="manual-translation-notice">
                        <?php esc_html_e('This guide is currently available only in English.', 'instruction-manual'); ?>
                    </div>
                <?php elseif ($english_version) : ?>
                    <div class="manual-translation-notice">
                        <?php printf(__('Saatavilla myös <a href="%s">englanniksi</a>.', 'instruction-manual'), esc_url(get_permalink($english_version))); ?>
                    </div>
                <?php endif; ?>
            </header>

            <nav class="manual-progress" aria-label="<?php echo esc_attr($text('progress_label')); ?>">
                <a href="#overview"><?php echo esc_html($text('overview')); ?></a>
                <a href="#steps"><?php echo esc_html($text('steps')); ?></a>
                <a href="#check"><?php echo esc_html($text('check')); ?></a>
                <a href="#related"><?php echo esc_html($text('related_short')); ?></a>
            </nav>

            <section id="overview" class="manual-document-section">
                <h2><?php echo esc_html($text('one_sentence')); ?></h2>
                <p class="manual-section-intro"><?php echo esc_html($text('overview_intro')); ?></p>
                <p><?php echo esc_html($purpose); ?></p>

                <?php $before_items = manual_instruction_before_start_items($post_id); ?>
                <?php if (!empty($before_items)) : ?>
                    <details class="manual-quick-checklist" open>
                        <summary><?php echo esc_html($text('quick_checklist')); ?></summary>
                        <div class="manual-quick-checklist__body">
                            <ul class="manual-check-list">
                                <?php foreach ($before_items as $item) : ?>
                                    <li><?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </details>
                <?php endif; ?>
            </section>

            <section id="steps" class="manual-document-section">
                <h2><?php echo esc_html($text('steps')); ?></h2>
                <p class="manual-section-intro"><?php echo esc_html($text('steps_intro')); ?></p>
                <div class="manual-content">
                    <?php the_content(); ?>
                </div>
            </section>

            <section id="check" class="manual-document-section">
                <div class="manual-mistakes-box">
                    <h2><?php echo esc_html($text('watch')); ?></h2>
                    <ul class="manual-check-list manual-check-list--warning">
                        <?php foreach (manual_instruction_common_mistakes($post_id) as $item) : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="manual-check-box">
                    <h2><?php echo esc_html($text('final_check')); ?></h2>
                    <ul class="manual-check-list">
                        <?php foreach (manual_instruction_success_checks($post_id) as $item) : ?>
                            <li><?php echo esc_html($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>

            <section id="related" class="manual-document-section">
                <h2><?php echo esc_html($text('related')); ?></h2>
                <?php if (!empty($related_instructions)) : ?>
                    <ul class="manual-related-list">
                        <?php foreach ($related_instructions as $related) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($related)); ?>"><?php echo esc_html(get_the_title($related)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p><?php echo esc_html($text('no_related')); ?></p>
                <?php endif; ?>
            </section>
        </article>

        <aside class="manual-doc-aside" aria-label="<?php echo esc_attr($text('tools_label')); ?>">
            <div class="manual-doc-aside__panel manual-on-page">
                <h2><?php echo esc_html($text('on_this_page')); ?></h2>
                <p class="manual-on-page__here" data-manual-you-are-here hidden>
                    <span><?php echo esc_html($text('you_are_here')); ?>:</span>
                    <strong data-manual-you-are-here-label></strong>
                </p>
                <nav>
                    <a href="#overview"><?php echo esc_html($text('overview')); ?></a>
                    <a href="#steps"><?php echo esc_html($text('steps')); ?></a>
                    <a href="#check"><?php echo esc_html($text('final_check')); ?></a>
                    <a href="#related"><?php echo esc_html($text('related')); ?></a>
                </nav>
            </div>

            <div class="manual-doc-aside__panel manual-doc-aside__actions">
                <h2><?php echo esc_html($text('guide_actions')); ?></h2>
                <button type="button" class="manual-doc-aside__action" onclick="window.print()"><?php echo esc_html($text('print')); ?></button>
                <button
                    type="button"
                    class="manual-doc-aside__action"
                    data-manual-theme-toggle
                    data-label-dark="<?php echo esc_attr($text('dark_mode_off')); ?>"
                    data-label-light="<?php echo esc_attr($text('dark_mode_on')); ?>"
                    aria-pressed="false"
                    aria-label="<?php echo esc_attr($text('dark_mode_on')); ?>"
                ><?php echo esc_html($text('dark_mode_on')); ?></button>
                <button
                    type="button"
                    class="manual-doc-aside__action"
                    data-copy-link
                    data-copy-label="<?php echo esc_attr($text('copy_link')); ?>"
                    data-copied-label="<?php echo esc_attr($text('copied')); ?>"
                ><?php echo esc_html($text('copy_link')); ?></button>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="manual-doc-aside__action"><?php echo esc_html($text('report_issue')); ?></a>
            </div>
        </aside>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>
