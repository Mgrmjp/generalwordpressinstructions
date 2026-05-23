<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once GWI_PLUGIN_DIR . 'includes/seed-fundamentals.php';
require_once GWI_PLUGIN_DIR . 'includes/seed-site-config.php';
require_once GWI_PLUGIN_DIR . 'includes/seed-block-editor.php';
require_once GWI_PLUGIN_DIR . 'includes/seed-classic-editor.php';
require_once GWI_PLUGIN_DIR . 'includes/seed-advanced.php';

function gwi_seed_instruction_content(): void
{
    $all_pairs = array_merge(
        gwi_seed_fundamentals(),
        gwi_seed_site_config(),
        gwi_seed_block_editor(),
        gwi_seed_classic_editor(),
        gwi_seed_advanced()
    );

    foreach ($all_pairs as $pair) {
        if (!empty($pair['en']) && !empty($pair['fi'])) {
            gwi_pair_seed_translations($pair['en'], $pair['fi']);
        }
    }

    gwi_assign_seed_categories();
}

function gwi_create_seed_instruction(string $slug, string $title, string $language, string $content): int
{
    $existing = get_page_by_path($slug, OBJECT, 'wp_instruction');

    if ($existing instanceof WP_Post) {
        return (int) $existing->ID;
    }

    $post_id = wp_insert_post([
        'post_type' => 'wp_instruction',
        'post_status' => 'publish',
        'post_name' => $slug,
        'post_title' => $title,
        'post_content' => $content,
        'post_excerpt' => gwi_seed_instruction_excerpt($content),
    ]);

    if (is_wp_error($post_id)) {
        return 0;
    }

    update_post_meta((int) $post_id, '_gwi_language', gwi_sanitize_language($language));
    update_post_meta((int) $post_id, '_gwi_seeded', 1);

    return (int) $post_id;
}

function gwi_seed_instruction_excerpt(string $content): string
{
    $content = preg_replace('~<!--\s+wp:general-wp-instructions/highlighted-screenshot\b.*?<!--\s+/wp:general-wp-instructions/highlighted-screenshot\s+-->~s', ' ', $content) ?? $content;
    $content = preg_replace('~<!--\s+/?wp:[^>]*?-->~s', ' ', $content) ?? $content;
    $content = wp_strip_all_tags(strip_shortcodes($content));

    return wp_trim_words($content, 28);
}

function gwi_pair_seed_translations(int $english_id, int $finnish_id): void
{
    if (!$english_id || !$finnish_id) {
        return;
    }

    update_post_meta($english_id, '_gwi_translation_id', $finnish_id);
    update_post_meta($finnish_id, '_gwi_translation_id', $english_id);
}

function gwi_assign_seed_categories(): void
{
    $category_map = [
        'fundamentals' => [
            'dashboard-overview-en', 'hallintapaneelin-yleiskatsaus-fi',
            'creating-posts-en', 'artikkelien-luominen-ja-muokkaus-fi',
            'categories-tags-en', 'kategoriat-ja-asiasanat-fi',
            'creating-pages-en', 'sivujen-luominen-ja-muokkaus-fi',
            'media-library-en', 'mediakirjasto-fi',
            'managing-comments-en', 'kommenttien-hallinta-fi',
        ],
        'site-config' => [
            'creating-menus-en', 'valikkojen-luominen-fi',
            'managing-users-en', 'kayttajien-hallinta-fi',
            'wordpress-settings-en', 'wordpress-asetukset-fi',
            'theme-customizer-en', 'teeman-mukauttaja-fi',
        ],
        'block-editor' => [
            'block-editor-basics-en', 'lohkoeditorin-perusteet-fi',
            'working-with-blocks-en', 'lohkojen-kaytto-fi',
            'common-content-blocks-en', 'yleiset-sisaltolohkot-fi',
            'media-blocks-en', 'medialohkot-fi',
            'layout-blocks-en', 'asettelulohkot-fi',
            'reusable-blocks-en', 'uudelleenkaytettavat-lohkot-ja-mallit-fi',
        ],
        'classic-editor' => [
            'classic-editor-basics-en', 'perinteisen-editorin-perusteet-fi',
            'classic-formatting-en', 'perinteisen-editorin-muotoilu-fi',
            'classic-media-en', 'perinteisen-editorin-media-fi',
        ],
        'advanced' => [
            'custom-fields-acf-en', 'mukautetut-kentat-acf-fi',
            'flexible-content-en', 'joustavat-sisaltoasettelut-fi',
            'acf-blocks-en', 'acf-lohkot-fi',
            'seo-basics-en', 'seo-perusteet-fi',
            'performance-caching-en', 'suorituskyky-ja-valimuisti-fi',
        ],
    ];

    foreach ($category_map as $category => $slugs) {
        foreach ($slugs as $slug) {
            $post = get_page_by_path($slug, OBJECT, 'wp_instruction');

            if ($post instanceof WP_Post) {
                wp_set_object_terms($post->ID, $category, 'instruction_category', true);
            }
        }
    }
}
