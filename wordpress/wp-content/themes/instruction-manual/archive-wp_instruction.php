<?php get_header(); ?>
<?php
$current_language = manual_instruction_current_language();

if (is_search()) {
    $archive_title = sprintf(__('Hakutulokset haulle "%s"', 'instruction-manual'), get_search_query());
} elseif (is_tax('instruction_category')) {
    $archive_title = single_term_title('', false);
} else {
    $archive_title = __('Kaikki ohjeet', 'instruction-manual');
}

$instructions = [];

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $instructions[] = get_post();
    }

    wp_reset_postdata();
}

$groups = manual_instruction_library_groups();
$grouped_instructions = array_fill_keys(array_keys($groups), []);

foreach ($instructions as $instruction) {
    if ($instruction instanceof WP_Post) {
        $grouped_instructions[manual_instruction_group_key($instruction->ID)][] = $instruction;
    }
}
?>

<section class="manual-hero manual-hero--archive">
    <p class="manual-eyebrow"><?php esc_html_e('Ohjekirjasto', 'instruction-manual'); ?></p>
    <h1 class="manual-title"><?php echo esc_html($archive_title); ?></h1>
    <p class="manual-lead">
        <?php esc_html_e('Selaa kaikkia WordPress-ohjeita aiheen, tason ja kielen mukaan.', 'instruction-manual'); ?>
    </p>
</section>

<section class="manual-filterbar" aria-label="<?php esc_attr_e('Ohjesuodattimet', 'instruction-manual'); ?>">
    <nav class="manual-filters" aria-label="<?php esc_attr_e('Suodata kielen mukaan', 'instruction-manual'); ?>">
        <span class="manual-filterbar__label"><?php esc_html_e('Kieli', 'instruction-manual'); ?></span>
        <a href="<?php echo esc_url(manual_instruction_language_filter_url()); ?>" class="manual-filter <?php echo $current_language === '' ? 'is-active' : ''; ?>"><?php esc_html_e('Kaikki', 'instruction-manual'); ?></a>
        <?php foreach (manual_instruction_language_options() as $language_code => $language_label) : ?>
            <a href="<?php echo esc_url(manual_instruction_language_filter_url($language_code)); ?>" class="manual-filter <?php echo $current_language === $language_code ? 'is-active' : ''; ?>"><?php echo esc_html(manual_language_filter_label_fi($language_code)); ?></a>
        <?php endforeach; ?>
    </nav>

    <nav class="manual-filters" aria-label="<?php esc_attr_e('Suodata aiheen mukaan', 'instruction-manual'); ?>">
        <span class="manual-filterbar__label"><?php esc_html_e('Aihe', 'instruction-manual'); ?></span>
        <a href="<?php echo esc_url(manual_instruction_url_with_language(manual_instruction_archive_url())); ?>" class="manual-filter <?php echo !is_tax('instruction_category') ? 'is-active' : ''; ?>"><?php esc_html_e('Kaikki aiheet', 'instruction-manual'); ?></a>
        <?php
        $categories = get_terms(['taxonomy' => 'instruction_category', 'hide_empty' => true]);
        if (!is_wp_error($categories) && !empty($categories)) :
            foreach ($categories as $category) :
                $is_active = is_tax('instruction_category', $category->slug);
                $category_url = get_term_link($category);
                if (is_wp_error($category_url)) {
                    continue;
                }
        ?>
            <a href="<?php echo esc_url(manual_instruction_url_with_language($category_url)); ?>" class="manual-filter <?php echo $is_active ? 'is-active' : ''; ?>"><?php echo esc_html(manual_instruction_category_label($category)); ?> <span class="manual-filter__count"><?php echo esc_html($category->count); ?></span></a>
        <?php
            endforeach;
        endif;
        ?>
    </nav>
</section>

<?php if (!empty($instructions)) : ?>
    <section id="library" class="manual-cabinet" aria-label="<?php esc_attr_e('Ohjeasiakirjat', 'instruction-manual'); ?>">
        <?php foreach ($groups as $group_key => $group) : ?>
            <?php if (empty($grouped_instructions[$group_key])) : ?>
                <?php continue; ?>
            <?php endif; ?>
            <section class="manual-cabinet__group" aria-labelledby="manual-group-<?php echo esc_attr($group_key); ?>">
                <header class="manual-cabinet__header">
                    <div>
                        <h2 id="manual-group-<?php echo esc_attr($group_key); ?>"><?php echo esc_html($group['title']); ?></h2>
                        <p><?php echo esc_html($group['description']); ?></p>
                    </div>
                    <span><?php echo esc_html(sprintf(_n('%d ohje', '%d ohjetta', count($grouped_instructions[$group_key]), 'instruction-manual'), count($grouped_instructions[$group_key]))); ?></span>
                </header>
                <div class="manual-doc-list">
                    <?php foreach ($grouped_instructions[$group_key] as $instruction) : ?>
                        <?php
                        $post_id = $instruction->ID;
                        $purpose = manual_instruction_purpose($post_id);
                        $task_title = manual_instruction_task_title($post_id);
                        $difficulty = manual_tutorial_difficulty_label_fi($post_id);
                        $lang_label = manual_language_label_fi($post_id);
                        $minutes = manual_instruction_estimated_minutes($post_id);
                        ?>
                        <article <?php post_class('manual-doc-row', $post_id); ?>>
                            <div class="manual-doc-row__main">
                                <h3><a href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php echo esc_html($task_title); ?></a></h3>
                                <p><?php echo esc_html($purpose); ?></p>
                                <div class="manual-doc-row__meta" aria-label="<?php esc_attr_e('Ohjeen tiedot', 'instruction-manual'); ?>">
                                    <span><?php echo esc_html($difficulty); ?></span>
                                    <span><?php echo esc_html(sprintf(__('%d min', 'instruction-manual'), $minutes)); ?></span>
                                    <span><?php echo esc_html($lang_label); ?></span>
                                </div>
                            </div>
                            <a class="manual-doc-row__link" href="<?php echo esc_url(get_permalink($post_id)); ?>"><?php esc_html_e('Aloita', 'instruction-manual'); ?> &rarr;</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </section>
<?php else : ?>
    <section class="manual-empty">
        <p><?php esc_html_e('Tämä hakuehto ei vastaa vielä yhtään ohjetta.', 'instruction-manual'); ?></p>
    </section>
<?php endif; ?>

<section id="glossary" class="manual-support-section" aria-labelledby="manual-glossary-title">
    <div class="manual-section-heading">
        <p class="manual-eyebrow"><?php esc_html_e('Sanasto selkokielellä', 'instruction-manual'); ?></p>
        <h2 id="manual-glossary-title"><?php esc_html_e('WordPress-termit', 'instruction-manual'); ?></h2>
        <p class="manual-section-subtitle"><?php esc_html_e('Keskeiset WordPress-termit lyhyesti selitettynä.', 'instruction-manual'); ?></p>
    </div>
    <dl class="manual-glossary">
        <?php foreach (manual_glossary_terms() as $term => $definition) : ?>
            <div>
                <dt><?php echo esc_html($term); ?></dt>
                <dd><?php echo esc_html($definition); ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
</section>

<?php get_footer(); ?>
