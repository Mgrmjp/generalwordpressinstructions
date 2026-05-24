<?php get_header(); ?>
<?php
$current_language = manual_instruction_current_language();
$raw_language_filter = get_query_var('instruction_language');
$is_all_languages = is_string($raw_language_filter) && strtolower($raw_language_filter) === 'all';

if (is_search()) {
    $archive_title = sprintf(__('Hakutulokset haulle "%s"', 'instruction-manual'), get_search_query());
} elseif (is_tax('instruction_category')) {
    $queried_term = get_queried_object();
    $archive_title = $queried_term instanceof WP_Term
        ? manual_instruction_category_label($queried_term)
        : single_term_title('', false);
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

$active_category_slug = '';
$is_category_archive = is_tax('instruction_category');

if ($is_category_archive) {
    $active_term = get_queried_object();

    if ($active_term instanceof WP_Term) {
        $active_category_slug = $active_term->slug;
    }
}

$instruction_count = count($instructions);
$count_language = manual_instruction_count_language();
$hide_language_meta = $count_language !== '';
$library_app_classes = 'manual-library-app manual-library-app--stacked';

if ($is_category_archive) {
    $library_app_classes .= ' manual-library-app--category';
}

if ($hide_language_meta) {
    $library_app_classes .= ' manual-library-app--language-filtered';
}
?>

<section class="manual-hero manual-hero--archive<?php echo $is_category_archive ? ' manual-hero--archive-category' : ''; ?>">
    <p class="manual-eyebrow"><?php esc_html_e('Ohjekirjasto', 'instruction-manual'); ?></p>
    <h1 class="manual-title"><?php echo esc_html($archive_title); ?></h1>
    <p class="manual-lead">
        <?php if ($is_category_archive && $instruction_count > 0) : ?>
            <?php
            echo esc_html(
                sprintf(
                    _n('%d ohje tässä aiheessa.', '%d ohjetta tässä aiheessa.', $instruction_count, 'instruction-manual'),
                    $instruction_count
                )
            );
            ?>
        <?php else : ?>
            <?php esc_html_e('Selaa WordPress-ohjeita aiheen ja kielen mukaan.', 'instruction-manual'); ?>
        <?php endif; ?>
    </p>
    <nav class="manual-archive-lang" aria-label="<?php esc_attr_e('Suodata kielen mukaan', 'instruction-manual'); ?>">
        <a href="<?php echo esc_url(manual_instruction_language_filter_url()); ?>" class="manual-archive-lang__link <?php echo $is_all_languages ? 'is-active' : ''; ?>" data-manual-language=""><?php esc_html_e('Kaikki', 'instruction-manual'); ?></a>
        <?php foreach (manual_instruction_language_options() as $language_code => $language_label) : ?>
            <a href="<?php echo esc_url(manual_instruction_language_filter_url($language_code)); ?>" class="manual-archive-lang__link <?php echo !$is_all_languages && $current_language === $language_code ? 'is-active' : ''; ?>" data-manual-language="<?php echo esc_attr($language_code); ?>"><?php echo esc_html(manual_language_filter_label_fi($language_code)); ?></a>
        <?php endforeach; ?>
    </nav>
</section>

<div
    class="<?php echo esc_attr($library_app_classes); ?>"
    data-manual-library
    data-language="<?php echo esc_attr($current_language); ?>"
    data-category="<?php echo esc_attr($active_category_slug); ?>"
    data-hide-group-headers="<?php echo $is_category_archive ? '1' : '0'; ?>"
    data-hide-language-meta="<?php echo $hide_language_meta ? '1' : '0'; ?>"
>
    <div class="manual-library-search-slim" role="search" aria-label="<?php esc_attr_e('Suodata ohjekirjastoa', 'instruction-manual'); ?>">
        <label class="screen-reader-text" for="manual-library-search-input"><?php esc_html_e('Suodata ohjeita', 'instruction-manual'); ?></label>
        <input
            type="search"
            id="manual-library-search-input"
            class="manual-library-search-slim__input"
            data-manual-library-search
            value="<?php echo esc_attr(get_search_query()); ?>"
            placeholder="<?php esc_attr_e('Suodata listaa…', 'instruction-manual'); ?>"
            autocomplete="off"
        >
        <button type="button" class="manual-library-search-slim__clear" data-manual-library-clear hidden><?php esc_html_e('Tyhjennä', 'instruction-manual'); ?></button>
        <p class="manual-result-count" data-manual-results-count hidden aria-live="polite">
            <?php echo esc_html(sprintf(_n('%d ohje', '%d ohjetta', $instruction_count, 'instruction-manual'), $instruction_count)); ?>
        </p>
    </div>

    <div class="manual-library-body">
        <aside class="manual-library-nav" aria-label="<?php esc_attr_e('Aihealueet', 'instruction-manual'); ?>">
            <p class="manual-filterbar__label"><?php esc_html_e('Aihe', 'instruction-manual'); ?></p>
            <nav class="manual-filters manual-filters--topics">
                <a href="<?php echo esc_url(manual_instruction_url_with_language(manual_instruction_archive_url())); ?>" class="manual-filter <?php echo !is_tax('instruction_category') ? 'is-active' : ''; ?>" data-manual-category="" data-manual-url="<?php echo esc_url(manual_instruction_archive_url()); ?>"><?php esc_html_e('Kaikki aiheet', 'instruction-manual'); ?></a>
                <?php
                $categories = get_terms(['taxonomy' => 'instruction_category', 'hide_empty' => false]);
                if (!is_wp_error($categories) && !empty($categories)) :
                    foreach ($categories as $category) :
                        $category_count = manual_instruction_category_count($category, $count_language);

                        if ($category_count < 1) {
                            continue;
                        }

                        $is_active = is_tax('instruction_category', $category->slug);
                        $category_url = get_term_link($category);

                        if (is_wp_error($category_url)) {
                            continue;
                        }
                        ?>
                    <a href="<?php echo esc_url(manual_instruction_url_with_language($category_url)); ?>" class="manual-filter <?php echo $is_active ? 'is-active' : ''; ?>" data-manual-category="<?php echo esc_attr($category->slug); ?>" data-manual-url="<?php echo esc_url($category_url); ?>">
                        <span class="manual-filter__text"><?php echo esc_html(manual_instruction_category_label($category)); ?></span>
                        <span class="manual-filter__count"><?php echo esc_html((string) $category_count); ?></span>
                    </a>
                        <?php
                    endforeach;
                endif;
                ?>
            </nav>
        </aside>

        <div class="manual-library-results-wrap">
<?php if (!empty($instructions)) : ?>
    <section id="library" class="manual-cabinet" data-manual-library-results aria-live="polite" aria-label="<?php esc_attr_e('Ohjeasiakirjat', 'instruction-manual'); ?>">
        <?php foreach ($groups as $group_key => $group) : ?>
            <?php if (empty($grouped_instructions[$group_key])) : ?>
                <?php continue; ?>
            <?php endif; ?>
            <section class="manual-cabinet__group" aria-labelledby="manual-group-<?php echo esc_attr($group_key); ?>">
                <?php if (!$is_category_archive) : ?>
                    <header class="manual-cabinet__header">
                        <div>
                            <h2 id="manual-group-<?php echo esc_attr($group_key); ?>"><?php echo esc_html($group['title']); ?></h2>
                            <p><?php echo esc_html($group['description']); ?></p>
                        </div>
                        <span><?php echo esc_html(sprintf(_n('%d ohje', '%d ohjetta', count($grouped_instructions[$group_key]), 'instruction-manual'), count($grouped_instructions[$group_key]))); ?></span>
                    </header>
                <?php endif; ?>
                <div class="manual-doc-list">
                    <?php foreach ($grouped_instructions[$group_key] as $instruction) : ?>
                        <?php
                        $post_id = $instruction->ID;
                        $purpose = manual_instruction_purpose($post_id);
                        $task_title = manual_instruction_task_title($post_id);
                        $lang_label = manual_language_label_fi($post_id);
                        ?>
                        <article <?php post_class('manual-doc-row', $post_id); ?>>
                            <a class="manual-doc-row__card" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                                <span class="manual-doc-row__content">
                                    <span class="manual-doc-row__title"><?php echo esc_html($task_title); ?></span>
                                    <span class="manual-doc-row__purpose"><?php echo esc_html($purpose); ?></span>
                                    <?php if (!$hide_language_meta) : ?>
                                        <span class="manual-doc-row__meta"><?php echo esc_html($lang_label); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="manual-doc-row__cta"><?php esc_html_e('Aloita', 'instruction-manual'); ?><?php echo manual_link_arrow(); ?></span>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </section>
<?php else : ?>
    <section class="manual-empty" data-manual-library-results aria-live="polite">
        <p><?php esc_html_e('Tämä hakuehto ei vastaa vielä yhtään ohjetta.', 'instruction-manual'); ?></p>
    </section>
<?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
