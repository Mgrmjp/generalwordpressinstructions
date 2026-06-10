<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return list<array<string, mixed>>
 */
function gwi_flexible_examples_manifest(string $language): array
{
    if ($language === 'fi') {
        return [
            [
                'layout' => 'intro_text',
                'heading' => 'Johdantoteksti',
                'body' => '<p>Lyhyt otsikko ja teksti osion alkuun. Ei kuvia—pidä tämä tiiviinä.</p>',
            ],
            [
                'layout' => 'screenshot_step',
                'screenshot_id' => 'page-flexible-sections',
                'highlight_label' => 'Lisää osio',
                'action' => 'Valitse osiotyyppi listasta ja täytä rivin kentät.',
            ],
            [
                'layout' => 'checklist',
                'heading' => 'Tarkistuslista',
                'items' => [
                    ['text' => 'Avaa oikea sivu ja osiolista sivun muokkausnäkymässä.'],
                    ['text' => 'Lisää osiot järjestyksessä.'],
                    ['text' => 'Tallenna ja esikatsele julkaistu sivu.'],
                ],
            ],
            [
                'layout' => 'callout',
                'variant' => 'warning',
                'content' => '<p>Poistettu osio katoaa tallennuksen jälkeen. Esikatsele ennen poistamista.</p>',
            ],
        ];
    }

    return [
        [
            'layout' => 'intro_text',
            'heading' => 'Intro text',
            'body' => '<p>Short heading and text for the start of a section. No images—keep it brief.</p>',
        ],
        [
            'layout' => 'screenshot_step',
            'screenshot_id' => 'page-flexible-sections',
            'highlight_label' => 'Add section',
            'action' => 'Choose a section type from the list and fill in that row’s boxes.',
        ],
        [
            'layout' => 'checklist',
            'heading' => 'Checklist',
            'items' => [
                ['text' => 'Open the correct Page and find the section list on the edit screen.'],
                ['text' => 'Add sections in order.'],
                ['text' => 'Save and preview the published page.'],
            ],
        ],
        [
            'layout' => 'callout',
            'variant' => 'warning',
            'content' => '<p>A deleted section is gone after you save. Preview before you remove one.</p>',
        ],
    ];
}

/**
 * Resolve screenshot image for flexible seed rows: attachment, import, then URL.
 *
 * @return int|string
 */
function gwi_flexible_sections_resolve_screenshot_image(string $screenshot_id, string $language)
{
    $screenshot_id = sanitize_file_name($screenshot_id);

    if ($screenshot_id === '') {
        return 0;
    }

    $attachment_id = gwi_get_screenshot_attachment_id($screenshot_id, $language);

    if ($attachment_id > 0) {
        return $attachment_id;
    }

    $url = gwi_get_instruction_screenshot_url($screenshot_id, 0, '', $language);

    return $url !== '' ? $url : 0;
}

/**
 * @param array<string, mixed> $entry
 * @return array<string, mixed>
 */
function gwi_flexible_sections_build_row(array $entry, string $language): array
{
    $layout = (string) ($entry['layout'] ?? '');
    $row = ['acf_fc_layout' => $layout];

    if ($layout === 'intro_text') {
        $row['heading'] = (string) ($entry['heading'] ?? '');
        $row['body'] = (string) ($entry['body'] ?? '');

        return $row;
    }

    if ($layout === 'screenshot_step') {
        $row['screenshot_id'] = (string) ($entry['screenshot_id'] ?? '');
        $row['highlight_label'] = (string) ($entry['highlight_label'] ?? '');
        $row['action'] = (string) ($entry['action'] ?? '');
        $row['image'] = gwi_flexible_sections_resolve_screenshot_image(
            (string) ($entry['screenshot_id'] ?? ''),
            $language
        );

        return $row;
    }

    if ($layout === 'checklist') {
        $row['heading'] = (string) ($entry['heading'] ?? '');
        $row['items'] = is_array($entry['items'] ?? null) ? $entry['items'] : [];

        return $row;
    }

    if ($layout === 'callout') {
        $row['variant'] = (string) ($entry['variant'] ?? 'note');
        $row['content'] = (string) ($entry['content'] ?? '');
    }

    return $row;
}

/**
 * Example flexible rows for the Flexible Content instruction (all four layouts).
 *
 * @return list<array<string, mixed>>
 */
function gwi_flexible_sections_example_rows(string $language): array
{
    $language = $language === 'fi' ? 'fi' : 'en';
    $rows = [];

    foreach (gwi_flexible_examples_manifest($language) as $entry) {
        $rows[] = gwi_flexible_sections_build_row($entry, $language);
    }

    return $rows;
}

/**
 * Draft Pages used for flexible-content admin screenshots (classic editor).
 */
function gwi_seed_flexible_demo_pages(): void
{
    $pages = [
        'flexible-sections-demo-en' => [
            'language' => 'en',
            'title' => 'Flexible sections demo',
        ],
        'flexible-sections-demo-fi' => [
            'language' => 'fi',
            'title' => 'Joustavat osiot (esimerkkisivu)',
        ],
    ];

    foreach ($pages as $slug => $config) {
        $existing = get_page_by_path($slug, OBJECT, 'page');
        $postarr = [
            'post_type' => 'page',
            'post_status' => 'draft',
            'post_name' => $slug,
            'post_title' => $config['title'],
            'post_content' => $config['language'] === 'fi'
                ? '<p>Esimerkkisivu kuvakaappauksia varten. Julkaistulla sivustolla tämä tekstialue voi olla tyhjä tai lyhyt—varsinaiset sivuosiot ovat alla.</p>'
                : '<p>Demo page for screenshots. On a live site this area may be empty or short—the page sections are below.</p>',
        ];

        if ($existing instanceof WP_Post) {
            $postarr['ID'] = $existing->ID;
            wp_update_post($postarr);
            $page_id = $existing->ID;
        } else {
            $page_id = wp_insert_post($postarr);
        }

        if (!is_int($page_id) || $page_id <= 0) {
            continue;
        }

        update_post_meta($page_id, '_gwi_flexible_demo', 1);
        update_post_meta($page_id, '_gwi_language', gwi_sanitize_language($config['language']));
    }
}

/**
 * @return array<string, string>
 */
function gwi_flexible_demo_page_slugs(): array
{
    return [
        'flexible-content-en' => 'flexible-sections-demo-en',
        'joustavat-sisaltoasettelut-fi' => 'flexible-sections-demo-fi',
    ];
}

/**
 * Apply example flexible content to the Flexible Content instruction posts.
 */
function gwi_seed_flexible_content_examples(bool $force = false): void
{
    if (!function_exists('update_field')) {
        return;
    }

    $map = [
        'joustavat-sisaltoasettelut-fi' => 'fi',
        'flexible-content-en' => 'en',
    ];

    foreach ($map as $slug => $language) {
        $post = get_page_by_path($slug, OBJECT, 'wp_instruction');

        if (!$post instanceof WP_Post) {
            continue;
        }

        $seeded_version = (string) get_post_meta($post->ID, '_gwi_flexible_examples_seeded', true);

        if (!$force && $seeded_version === GWI_FLEXIBLE_EXAMPLES_VERSION) {
            continue;
        }

        $rows = gwi_flexible_sections_example_rows($language);
        update_field('instruction_sections', $rows, $post->ID);
        update_post_meta($post->ID, '_gwi_flexible_examples_seeded', GWI_FLEXIBLE_EXAMPLES_VERSION);

        $demo_slug = gwi_flexible_demo_page_slugs()[$slug] ?? '';

        if ($demo_slug === '') {
            continue;
        }

        $demo_page = get_page_by_path($demo_slug, OBJECT, 'page');

        if ($demo_page instanceof WP_Post) {
            update_field('instruction_sections', $rows, $demo_page->ID);
        }
    }
}
