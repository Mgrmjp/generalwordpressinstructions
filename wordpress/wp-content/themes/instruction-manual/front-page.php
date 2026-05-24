<?php get_header(); ?>

<?php
$fi_tutorials = get_posts([
    'post_type' => 'wp_instruction',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_key' => '_gwi_language',
    'meta_value' => 'fi',
    'orderby' => 'title',
    'order' => 'ASC',
]);

$intent_categories = manual_intent_categories_with_popular($fi_tutorials);
$featured = manual_featured_finnish_tutorials($fi_tutorials, 3);
$learning_paths = manual_learning_paths_with_posts($fi_tutorials);
$suggested_searches = manual_finnish_suggested_searches();
$glossary_preview = manual_glossary_preview(5);
$featured_icons = ['code', 'pen', 'layout'];
$featured_tones = ['green', 'blue', 'orange'];
$glossary_icons = ['block', 'cache', 'theme', 'plugin', 'field'];
?>

<section class="manual-hero manual-hero--home">
    <div class="manual-hero__content">
        <p class="manual-eyebrow"><?php esc_html_e('WordPress-ohjeet', 'instruction-manual'); ?></p>
        <h1 class="manual-title"><?php esc_html_e('WordPress-ohjeet selkeästi', 'instruction-manual'); ?></h1>
        <p class="manual-lead">
            <?php esc_html_e('Löydä oikea ohje nopeasti. Muokkaa sisältöä, luo sivuja, hallinnoi asetuksia ja ratkaise yleisimmät ongelmat.', 'instruction-manual'); ?>
        </p>
        <div class="manual-hero-search" aria-label="<?php esc_attr_e('Hae ohjeita', 'instruction-manual'); ?>">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="post_type" value="wp_instruction">
                <label for="manual-search-main" class="screen-reader-text"><?php esc_html_e('Etsi ohjetta', 'instruction-manual'); ?></label>
                <span class="manual-search-field manual-search-field--hero">
                    <input type="search" id="manual-search-main" name="s" placeholder="<?php esc_attr_e('Etsi ohjetta...', 'instruction-manual'); ?>">
                </span>
                <button type="submit"><?php esc_html_e('Hae', 'instruction-manual'); ?></button>
            </form>
            <div class="manual-suggested-searches">
                <?php esc_html_e('Esimerkkejä:', 'instruction-manual'); ?>
                <?php foreach ($suggested_searches as $index => $suggestion) : ?>
                    <a href="<?php echo esc_url(add_query_arg(['s' => $suggestion, 'post_type' => 'wp_instruction'], home_url('/'))); ?>"><?php echo esc_html($suggestion); ?></a><?php echo $index < count($suggested_searches) - 1 ? ', ' : ''; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="manual-hero-visual" aria-hidden="true">
        <span class="manual-hero-visual__panel manual-hero-visual__panel--one"></span>
        <span class="manual-hero-visual__panel manual-hero-visual__panel--two"></span>
        <span class="manual-hero-visual__panel manual-hero-visual__panel--three"></span>
        <span class="manual-hero-visual__node manual-hero-visual__node--one"></span>
        <span class="manual-hero-visual__node manual-hero-visual__node--two"></span>
        <span class="manual-hero-visual__node manual-hero-visual__node--three"></span>
        <span class="manual-hero-visual__node manual-hero-visual__node--four"></span>
        <span class="manual-hero-visual__node manual-hero-visual__node--five"></span>
    </div>
</section>

<?php if (!empty($intent_categories)) : ?>
    <section class="manual-intent-section" aria-labelledby="manual-intent-title">
        <h2 id="manual-intent-title" class="manual-section-title"><?php esc_html_e('Aloita tehtävästä', 'instruction-manual'); ?></h2>
        <p class="manual-section-subtitle"><?php esc_html_e('Valitse mitä haluat tehdä.', 'instruction-manual'); ?></p>
        <div class="manual-intent-grid">
            <?php foreach ($intent_categories as $intent) : ?>
                <?php
                $intent_icon = $intent['icon'] ?? 'content';
                $intent_tone = $intent['tone'] ?? 'green';
                ?>
                <a href="<?php echo esc_url($intent['url']); ?>" class="manual-intent-card manual-intent-card--<?php echo esc_attr($intent_tone); ?>">
                    <span class="manual-icon manual-intent-card__icon" aria-hidden="true"><?php echo manual_design_icon($intent_icon); ?></span>
                    <h3><?php echo esc_html($intent['title']); ?></h3>
                    <p><?php echo esc_html($intent['description']); ?></p>
                    <span class="manual-intent-card__action"><?php esc_html_e('Aloita', 'instruction-manual'); ?> &rarr;</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($learning_paths)) :
    $first_path = $learning_paths[0];
?>
    <section id="polut" class="manual-paths-section" aria-labelledby="manual-paths-title">
        <div class="manual-section-header">
            <h2 id="manual-paths-title" class="manual-section-title"><?php esc_html_e('Suositeltu aloituspolku', 'instruction-manual'); ?></h2>
            <a href="<?php echo esc_url($first_path['url']); ?>" class="manual-view-all"><?php esc_html_e('Näytä kaikki polut', 'instruction-manual'); ?> &rarr;</a>
        </div>
        <p class="manual-section-subtitle"><?php esc_html_e('Aloita tästä, jos olet uusi WordPress-käyttäjä.', 'instruction-manual'); ?></p>
        <div class="manual-path-single">
            <div class="manual-path-single__marker">
                <span class="manual-icon manual-path-single__icon" aria-hidden="true"><?php echo manual_design_icon('flag'); ?></span>
            </div>
            <div class="manual-path-single__body">
                <div class="manual-path-single__header">
                    <h3><?php echo esc_html($first_path['title']); ?></h3>
                    <span class="manual-path-count"><?php echo esc_html(sprintf(_n('%d vaihe', '%d vaihetta', $first_path['count'], 'instruction-manual'), $first_path['count'])); ?></span>
                </div>
                <ol class="manual-path-single__steps">
                    <?php foreach ($first_path['steps'] as $step) : ?>
                        <li>
                            <?php if (!empty($step['url'])) : ?>
                                <a href="<?php echo esc_url($step['url']); ?>"><?php echo esc_html($step['title']); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($step['title']); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <a href="<?php echo esc_url($first_path['url']); ?>" class="manual-path-single__cta"><?php esc_html_e('Aloita polku', 'instruction-manual'); ?> &rarr;</a>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($featured)) : ?>
    <section class="manual-featured-section" aria-labelledby="manual-featured-title">
        <div class="manual-section-header">
            <h2 id="manual-featured-title" class="manual-section-title"><?php esc_html_e('Suosituimmat ohjeet', 'instruction-manual'); ?></h2>
            <a href="<?php echo esc_url(manual_instruction_archive_url()); ?>" class="manual-view-all"><?php esc_html_e('Näytä kaikki ohjeet', 'instruction-manual'); ?> &rarr;</a>
        </div>
        <div class="manual-tutorial-grid">
            <?php foreach ($featured as $featured_index => $tutorial) : ?>
                <?php
                $t_id = $tutorial->ID;
                $t_purpose = manual_instruction_purpose($t_id);
                $t_difficulty = manual_tutorial_difficulty_label_fi($t_id);
                $t_minutes = manual_instruction_estimated_minutes($t_id);
                $t_reviewed = manual_reviewed_month_label_fi($t_id);
                $featured_icon = $featured_icons[$featured_index] ?? 'content';
                $featured_tone = $featured_tones[$featured_index] ?? 'green';
                ?>
                <a href="<?php echo esc_url(get_permalink($t_id)); ?>" class="manual-tutorial-card manual-tutorial-card--<?php echo esc_attr($featured_tone); ?>">
                    <span class="manual-icon manual-tutorial-card__icon" aria-hidden="true"><?php echo manual_design_icon($featured_icon); ?></span>
                    <div class="manual-tutorial-card__content">
                        <h3><?php echo esc_html(manual_instruction_task_title($t_id)); ?></h3>
                        <p><?php echo esc_html($t_purpose); ?></p>
                        <div class="manual-tutorial-card__meta">
                            <span class="manual-badge manual-badge--difficulty"><?php echo esc_html($t_difficulty); ?></span>
                            <span class="manual-badge"><?php echo esc_html(sprintf(__('%d min', 'instruction-manual'), $t_minutes)); ?></span>
                            <?php if ($t_reviewed) : ?>
                                <span class="manual-badge manual-badge--review"><?php echo esc_html($t_reviewed); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="manual-tutorial-card__action"><?php esc_html_e('Aloita', 'instruction-manual'); ?> &rarr;</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="manual-glossary-strip-section" aria-labelledby="manual-glossary-strip-title">
    <div class="manual-section-header">
        <div>
            <h2 id="manual-glossary-strip-title" class="manual-section-title"><?php esc_html_e('Sanasto', 'instruction-manual'); ?></h2>
            <p class="manual-section-subtitle"><?php esc_html_e('Ymmärrä tärkeimmät käsitteet.', 'instruction-manual'); ?></p>
        </div>
        <a href="<?php echo esc_url(manual_glossary_url()); ?>" class="manual-view-all"><?php esc_html_e('Avaa sanasto', 'instruction-manual'); ?> &rarr;</a>
    </div>
    <dl class="manual-glossary-strip">
        <?php $glossary_item_index = 0; ?>
        <?php foreach ($glossary_preview as $term => $definition) : ?>
            <?php
            $glossary_icon = $glossary_icons[$glossary_item_index] ?? 'block';
            ?>
            <div class="manual-glossary-strip__item">
                <dt>
                    <span class="manual-icon manual-glossary-strip__icon" aria-hidden="true"><?php echo manual_design_icon($glossary_icon); ?></span>
                    <span class="manual-glossary-strip__term"><?php echo esc_html($term); ?></span>
                </dt>
                <dd><?php echo esc_html($definition); ?></dd>
            </div>
            <?php $glossary_item_index++; ?>
        <?php endforeach; ?>
    </dl>
</section>

<?php get_footer(); ?>
