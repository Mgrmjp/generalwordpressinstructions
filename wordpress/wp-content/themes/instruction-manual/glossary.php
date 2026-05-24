<?php get_header(); ?>

<?php
$terms = manual_glossary_terms();
uksort($terms, static function (string $a, string $b): int {
    return strnatcasecmp($a, $b);
});

$grouped_terms = [];

foreach ($terms as $term => $definition) {
    $letter = function_exists('mb_substr') ? mb_substr($term, 0, 1, 'UTF-8') : substr($term, 0, 1);
    $letter = function_exists('mb_strtoupper') ? mb_strtoupper($letter, 'UTF-8') : strtoupper($letter);
    $grouped_terms[$letter][$term] = $definition;
}

$term_count = count($terms);
?>

<section class="manual-hero manual-hero--glossary">
    <div class="manual-hero__content">
        <p class="manual-eyebrow"><?php esc_html_e('Sanasto', 'instruction-manual'); ?></p>
        <h1 class="manual-title"><?php esc_html_e('WordPress-sanasto', 'instruction-manual'); ?></h1>
        <p class="manual-lead">
            <?php esc_html_e('Keskeiset WordPress-termit lyhyesti selitettynä, jotta ohjeita on helpompi seurata.', 'instruction-manual'); ?>
        </p>
    </div>
</section>

<div
    class="manual-glossary-page"
    data-manual-glossary
    data-count-singular="<?php esc_attr_e('termi', 'instruction-manual'); ?>"
    data-count-plural="<?php esc_attr_e('termiä', 'instruction-manual'); ?>"
>
    <aside class="manual-glossary-sidebar" aria-labelledby="manual-glossary-index-title">
        <h2 id="manual-glossary-index-title"><?php esc_html_e('Sanaston osiot', 'instruction-manual'); ?></h2>
        <nav class="manual-glossary-index" aria-label="<?php esc_attr_e('Sanaston kirjaimet', 'instruction-manual'); ?>">
            <?php foreach (array_keys($grouped_terms) as $letter) : ?>
                <a href="#sanasto-<?php echo esc_attr(sanitize_title($letter)); ?>"><?php echo esc_html($letter); ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="manual-glossary-guides-link" href="<?php echo esc_url(manual_instruction_archive_url()); ?>"><?php esc_html_e('Avaa kaikki ohjeet', 'instruction-manual'); ?> &rarr;</a>
    </aside>

    <section class="manual-glossary-content" aria-labelledby="manual-glossary-content-title">
        <header class="manual-glossary-toolbar">
            <div>
                <h2 id="manual-glossary-content-title"><?php esc_html_e('Termit', 'instruction-manual'); ?></h2>
                <p data-manual-glossary-count>
                    <?php echo esc_html(sprintf(_n('%d termi', '%d termiä', $term_count, 'instruction-manual'), $term_count)); ?>
                </p>
            </div>
            <div class="manual-glossary-search">
                <label for="manual-glossary-search-input"><?php esc_html_e('Hae sanastosta', 'instruction-manual'); ?></label>
                <div class="manual-glossary-search__row">
                    <input
                        type="search"
                        id="manual-glossary-search-input"
                        data-manual-glossary-search
                        placeholder="<?php esc_attr_e('Etsi termiä', 'instruction-manual'); ?>"
                        autocomplete="off"
                    >
                    <button type="button" data-manual-glossary-clear><?php esc_html_e('Tyhjennä', 'instruction-manual'); ?></button>
                </div>
            </div>
        </header>

        <p class="manual-glossary-empty" data-manual-glossary-empty hidden>
            <?php esc_html_e('Mikään termi ei vastaa hakua.', 'instruction-manual'); ?>
        </p>

        <div class="manual-glossary-list" data-manual-glossary-list>
            <?php foreach ($grouped_terms as $letter => $letter_terms) : ?>
                <section
                    id="sanasto-<?php echo esc_attr(sanitize_title($letter)); ?>"
                    class="manual-glossary-group"
                    data-manual-glossary-group
                    aria-labelledby="manual-glossary-letter-<?php echo esc_attr(sanitize_title($letter)); ?>"
                >
                    <h3 id="manual-glossary-letter-<?php echo esc_attr(sanitize_title($letter)); ?>"><?php echo esc_html($letter); ?></h3>
                    <dl class="manual-glossary-terms">
                        <?php foreach ($letter_terms as $term => $definition) : ?>
                            <?php
                            $related_url = add_query_arg([
                                's' => $term,
                                'post_type' => 'wp_instruction',
                            ], home_url('/'));
                            ?>
                            <div
                                class="manual-glossary-term"
                                data-manual-glossary-item
                                data-glossary-text="<?php echo esc_attr($term . ' ' . $definition); ?>"
                            >
                                <dt><?php echo esc_html($term); ?></dt>
                                <dd><?php echo esc_html($definition); ?></dd>
                                <a href="<?php echo esc_url($related_url); ?>"><?php esc_html_e('Ohjeet termistä', 'instruction-manual'); ?> &rarr;</a>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </section>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php get_footer(); ?>
