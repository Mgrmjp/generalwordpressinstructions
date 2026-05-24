<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', 'manual_setup');
add_action('wp_enqueue_scripts', 'manual_enqueue_assets');
add_action('init', 'manual_register_static_routes');
add_action('init', 'manual_flush_static_routes_if_needed', 20);
add_action('rest_api_init', 'manual_register_instruction_rest_routes');
add_filter('query_vars', 'manual_instruction_query_vars');
add_filter('template_include', 'manual_static_template_include');
add_filter('body_class', 'manual_static_body_classes');
add_action('pre_get_posts', 'manual_filter_instruction_archive_by_language');
add_filter('posts_search', 'manual_expand_instruction_search', 10, 2);
add_filter('the_content', 'manual_cleanup_instruction_content_language', 18);
add_filter('document_title_parts', 'manual_document_title_parts');

function manual_static_routes_version(): string
{
    return '20260524-glossary-v1';
}

function manual_register_static_routes(): void
{
    add_rewrite_rule('^sanasto/?$', 'index.php?manual_view=glossary', 'top');
}

function manual_flush_static_routes_if_needed(): void
{
    if (get_option('manual_static_routes_version') === manual_static_routes_version()) {
        return;
    }

    manual_register_static_routes();
    flush_rewrite_rules(false);
    update_option('manual_static_routes_version', manual_static_routes_version());
}

function manual_is_glossary_view(): bool
{
    return get_query_var('manual_view') === 'glossary';
}

function manual_glossary_url(): string
{
    return home_url('/sanasto/');
}

function manual_static_template_include(string $template): string
{
    if (!manual_is_glossary_view()) {
        return $template;
    }

    global $wp_query;

    if ($wp_query instanceof WP_Query) {
        $wp_query->is_404 = false;
    }

    status_header(200);

    $glossary_template = locate_template('glossary.php');

    return $glossary_template !== '' ? $glossary_template : $template;
}

function manual_static_body_classes(array $classes): array
{
    if (manual_is_glossary_view()) {
        $classes[] = 'manual-glossary-view';
    }

    return $classes;
}

function manual_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_editor_style('style.css');
    register_nav_menus([
        'primary' => __('Primary Menu', 'instruction-manual'),
    ]);
}

function manual_enqueue_assets(): void
{
    wp_enqueue_style('general-sans', 'https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap', [], null);
    wp_enqueue_style('instruction-manual', get_stylesheet_uri(), ['general-sans'], wp_get_theme()->get('Version'));

    $script_path = get_template_directory() . '/assets/js/manual-app.js';
    $style_dependencies = ['instruction-manual'];

    if (is_singular('wp_instruction')) {
        $reader_css = get_template_directory() . '/assets/css/instruction-reader.css';

        if (file_exists($reader_css)) {
            wp_enqueue_style(
                'instruction-manual-reader',
                get_template_directory_uri() . '/assets/css/instruction-reader.css',
                ['instruction-manual'],
                (string) filemtime($reader_css)
            );
            $style_dependencies[] = 'instruction-manual-reader';
        }
    }

    $edge_css = get_template_directory() . '/assets/css/theme-edge.css';

    if (file_exists($edge_css)) {
        wp_enqueue_style(
            'instruction-manual-edge',
            get_template_directory_uri() . '/assets/css/theme-edge.css',
            $style_dependencies,
            (string) filemtime($edge_css)
        );
    }

    if (file_exists($script_path)) {
        wp_enqueue_script(
            'instruction-manual-app',
            get_template_directory_uri() . '/assets/js/manual-app.js',
            [],
            (string) filemtime($script_path),
            true
        );
        wp_localize_script('instruction-manual-app', 'manualApp', manual_app_config());
    }
}

function manual_app_config(): array
{
    $groups = [];

    foreach (manual_instruction_library_groups() as $key => $group) {
        $groups[$key] = [
            'title' => $group['title'],
            'description' => $group['description'],
        ];
    }

    $current_language = '';

    if (is_singular('wp_instruction')) {
        $current_language = manual_instruction_language_code((int) get_queried_object_id());
    } else {
        $current_language = manual_instruction_current_language();
    }

    $is_english = $current_language === 'en';
    $labels = $is_english
        ? [
            'results' => __('guides', 'instruction-manual'),
            'result' => __('guide', 'instruction-manual'),
            'empty' => __('No guides match these filters.', 'instruction-manual'),
            'start' => __('Open guide', 'instruction-manual'),
            'searching' => __('Searching...', 'instruction-manual'),
            'suggestions' => __('Suggested guides', 'instruction-manual'),
            'clear' => __('Clear', 'instruction-manual'),
            'done' => __('Done', 'instruction-manual'),
            'undo' => __('Undo', 'instruction-manual'),
            'youAreHere' => __('You are here', 'instruction-manual'),
            'copyCode' => __('Copy', 'instruction-manual'),
            'codeCopied' => __('Copied', 'instruction-manual'),
            'darkModeOn' => __('Dark mode', 'instruction-manual'),
            'darkModeOff' => __('Light mode', 'instruction-manual'),
        ]
        : [
            'results' => __('ohjetta', 'instruction-manual'),
            'result' => __('ohje', 'instruction-manual'),
            'empty' => __('Mikään ohje ei vastaa näitä suodattimia.', 'instruction-manual'),
            'start' => __('Avaa ohje', 'instruction-manual'),
            'searching' => __('Haetaan...', 'instruction-manual'),
            'suggestions' => __('Ehdotetut ohjeet', 'instruction-manual'),
            'clear' => __('Tyhjennä', 'instruction-manual'),
            'done' => __('Valmis', 'instruction-manual'),
            'undo' => __('Peru', 'instruction-manual'),
            'youAreHere' => __('Olet tässä', 'instruction-manual'),
            'copyCode' => __('Kopioi', 'instruction-manual'),
            'codeCopied' => __('Kopioitu', 'instruction-manual'),
            'darkModeOn' => __('Tumma tila', 'instruction-manual'),
            'darkModeOff' => __('Vaalea tila', 'instruction-manual'),
        ];

    return [
        'restUrl' => esc_url_raw(rest_url('instruction-manual/v1/instructions')),
        'archiveUrl' => esc_url_raw(manual_instruction_archive_url()),
        'currentLanguage' => $current_language,
        'groups' => $groups,
        'labels' => $labels,
    ];
}

function manual_register_instruction_rest_routes(): void
{
    register_rest_route('instruction-manual/v1', '/instructions', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'manual_rest_get_instructions',
        'permission_callback' => '__return_true',
        'args' => [
            'search' => [
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'language' => [
                'sanitize_callback' => 'manual_instruction_sanitize_language',
            ],
            'category' => [
                'sanitize_callback' => 'sanitize_key',
            ],
            'limit' => [
                'sanitize_callback' => 'absint',
            ],
        ],
    ]);
}

function manual_rest_get_instructions(WP_REST_Request $request): WP_REST_Response
{
    $language = manual_instruction_sanitize_language($request->get_param('language'));
    $category = sanitize_key((string) $request->get_param('category'));
    $search = trim((string) $request->get_param('search'));
    $limit = absint($request->get_param('limit'));
    $limit = $limit > 0 ? min(100, $limit) : 60;

    $args = [
        'post_type' => 'wp_instruction',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    if ($language !== '') {
        $args['meta_query'] = [
            [
                'key' => '_gwi_language',
                'value' => $language,
                'compare' => '=',
            ],
        ];
    }

    if ($category !== '') {
        $args['tax_query'] = [
            [
                'taxonomy' => 'instruction_category',
                'field' => 'slug',
                'terms' => [$category],
            ],
        ];
    }

    $items = [];

    foreach (get_posts($args) as $post) {
        if (!$post instanceof WP_Post || !manual_instruction_matches_search($post->ID, $search)) {
            continue;
        }

        $items[] = manual_instruction_response_item($post);

        if (count($items) >= $limit) {
            break;
        }
    }

    return rest_ensure_response([
        'items' => $items,
        'count' => count($items),
    ]);
}

function manual_instruction_response_item(WP_Post $post): array
{
    $terms = get_the_terms($post->ID, 'instruction_category');
    $categories = [];
    $category_slugs = [];

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $categories[] = [
                'id' => $term->term_id,
                'slug' => $term->slug,
                'name' => manual_instruction_category_label($term),
            ];
            $category_slugs[] = $term->slug;
        }
    }

    return [
        'id' => $post->ID,
        'title' => manual_instruction_task_title($post->ID),
        'url' => get_permalink($post),
        'purpose' => manual_instruction_purpose($post->ID),
        'difficulty' => manual_instruction_difficulty_display_label($post->ID),
        'minutes' => manual_instruction_estimated_minutes($post->ID),
        'language' => manual_instruction_language_code($post->ID),
        'languageLabel' => manual_instruction_language_display_label($post->ID),
        'groupKey' => manual_instruction_group_key($post->ID),
        'categories' => $categories,
        'categorySlugs' => $category_slugs,
    ];
}

function manual_instruction_matches_search(int $post_id, string $search): bool
{
    $search = trim($search);

    if ($search === '') {
        return true;
    }

    $terms = array_merge(
        [$search],
        preg_split('/\s+/u', $search) ?: [],
        manual_expand_finnish_search($search),
        manual_expanded_search_terms($search)
    );
    $terms = array_values(array_unique(array_filter(array_map('trim', $terms))));
    $haystack = manual_lowercase(manual_instruction_search_haystack($post_id));

    foreach ($terms as $term) {
        if ($term !== '' && str_contains($haystack, manual_lowercase($term))) {
            return true;
        }
    }

    return false;
}

function manual_instruction_search_haystack(int $post_id): string
{
    $terms = get_the_terms($post_id, 'instruction_category');
    $category_text = '';

    if (!is_wp_error($terms) && !empty($terms)) {
        $category_text = implode(' ', array_map(static function (WP_Term $term): string {
            return $term->name . ' ' . $term->slug;
        }, $terms));
    }

    return implode(' ', [
        get_the_title($post_id),
        get_post_field('post_name', $post_id),
        manual_instruction_task_title($post_id),
        manual_instruction_purpose($post_id),
        manual_instruction_card_excerpt($post_id, 80),
        $category_text,
    ]);
}

function manual_lowercase(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    return strtolower($value);
}

function manual_design_icon(string $icon): string
{
    $attrs = 'xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
    $svg = static function (string $paths) use ($attrs): string {
        return '<svg ' . $attrs . '>' . $paths . '</svg>';
    };

    // Icon paths are from Lucide's open SVG set; keys keep the theme API stable.
    $icons = [
        'content' => $svg('<path d="M14.364 13.634a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506l4.013-4.009a1 1 0 0 0-3.004-3.004z"/><path d="M14.487 7.858A1 1 0 0 1 14 7V2"/><path d="M20 19.645V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l2.516 2.516"/><path d="M8 18h1"/>'),
        'page' => $svg('<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>'),
        'fix' => $svg('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"/>'),
        'settings' => $svg('<path d="M10 5H3"/><path d="M12 19H3"/><path d="M14 3v4"/><path d="M16 17v4"/><path d="M21 12h-9"/><path d="M21 19h-5"/><path d="M21 5h-7"/><path d="M8 10v4"/><path d="M8 12H3"/>'),
        'flag' => $svg('<path d="M4 22V4a1 1 0 0 1 .4-.8A6 6 0 0 1 8 2c3 0 5 2 7.333 2q2 0 3.067-.8A1 1 0 0 1 20 4v10a1 1 0 0 1-.4.8A6 6 0 0 1 16 16c-3 0-5-2-8-2a6 6 0 0 0-4 1.528"/>'),
        'code' => $svg('<path d="m18 16 4-4-4-4"/><path d="m6 8-4 4 4 4"/><path d="m14.5 4-5 16"/>'),
        'pen' => $svg('<path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/>'),
        'layout' => $svg('<rect width="18" height="7" x="3" y="3" rx="1"/><rect width="9" height="7" x="3" y="14" rx="1"/><rect width="5" height="7" x="16" y="14" rx="1"/>'),
        'block' => $svg('<path d="M10 22V7a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5a1 1 0 0 0-1-1H2"/><rect x="14" y="2" width="8" height="8" rx="1"/>'),
        'cache' => $svg('<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 15 21.84"/><path d="M21 5V8"/><path d="M21 12L18 17H22L19 22"/><path d="M3 12A9 3 0 0 0 14.59 14.87"/>'),
        'theme' => $svg('<path d="M12 22a1 1 0 0 1 0-20 10 9 0 0 1 10 9 5 5 0 0 1-5 5h-2.25a1.75 1.75 0 0 0-1.4 2.8l.3.4a1.75 1.75 0 0 1-1.4 2.8z"/><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>'),
        'plugin' => $svg('<path d="M15.39 4.39a1 1 0 0 0 1.68-.474 2.5 2.5 0 1 1 3.014 3.015 1 1 0 0 0-.474 1.68l1.683 1.682a2.414 2.414 0 0 1 0 3.414L19.61 15.39a1 1 0 0 1-1.68-.474 2.5 2.5 0 1 0-3.014 3.015 1 1 0 0 1 .474 1.68l-1.683 1.682a2.414 2.414 0 0 1-3.414 0L8.61 19.61a1 1 0 0 0-1.68.474 2.5 2.5 0 1 1-3.014-3.015 1 1 0 0 0 .474-1.68l-1.683-1.682a2.414 2.414 0 0 1 0-3.414L4.39 8.61a1 1 0 0 1 1.68.474 2.5 2.5 0 1 0 3.014-3.015 1 1 0 0 1-.474-1.68l1.683-1.682a2.414 2.414 0 0 1 3.414 0z"/>'),
        'field' => $svg('<path d="M13 5h8"/><path d="M13 12h8"/><path d="M13 19h8"/><path d="m3 17 2 2 4-4"/><path d="m3 7 2 2 4-4"/>'),
    ];
    $icons['edit'] = $icons['pen'];
    $icons['build'] = $icons['layout'];

    return $icons[$icon] ?? $icons['content'];
}

function manual_instruction_language_code(int $post_id): string
{
    $language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';

    return in_array($language, ['fi', 'en'], true) ? $language : 'fi';
}

function manual_instruction_single_text(int $post_id, string $key): string
{
    $language = manual_instruction_language_code($post_id);
    $labels = [
        'fi' => [
            'eyebrow' => __('WordPress-ohje', 'instruction-manual'),
            'progress_label' => __('Ohjeen edistyminen', 'instruction-manual'),
            'overview' => __('Yleiskatsaus', 'instruction-manual'),
            'steps' => __('Vaiheet', 'instruction-manual'),
            'check' => __('Tarkista', 'instruction-manual'),
            'related_short' => __('Liittyvät', 'instruction-manual'),
            'one_sentence' => __('Yhdellä lauseella', 'instruction-manual'),
            'before' => __('Ennen kuin aloitat', 'instruction-manual'),
            'watch' => __('Varo näitä', 'instruction-manual'),
            'final_check' => __('Tarkista lopuksi', 'instruction-manual'),
            'related' => __('Liittyvät ohjeet', 'instruction-manual'),
            'no_related' => __('Liittyviä ohjeita ei ole vielä saatavilla.', 'instruction-manual'),
            'tools_label' => __('Ohjeen työkalut', 'instruction-manual'),
            'contents' => __('Sisältö', 'instruction-manual'),
            'manual_contents' => __('Ohjealueet', 'instruction-manual'),
            'all_guides' => __('Kaikki ohjeet', 'instruction-manual'),
            'on_this_page' => __('Tällä sivulla', 'instruction-manual'),
            'you_are_here' => __('Olet tässä', 'instruction-manual'),
            'quick_checklist' => __('Pikachecklist ennen aloitusta', 'instruction-manual'),
            'steps_intro' => __('Seuraa vaiheita järjestyksessä. Merkitse valmiiksi kun olet tehnyt kohdan.', 'instruction-manual'),
            'overview_intro' => __('Lyhyt kuvaus siitä, mitä tämä ohje auttaa sinua tekemään.', 'instruction-manual'),
            'dark_mode_on' => __('Tumma tila', 'instruction-manual'),
            'dark_mode_off' => __('Vaalea tila', 'instruction-manual'),
            'copy_code' => __('Kopioi', 'instruction-manual'),
            'code_copied' => __('Kopioitu', 'instruction-manual'),
            'guide_actions' => __('Toiminnot', 'instruction-manual'),
            'print' => __('Tulosta', 'instruction-manual'),
            'copy_link' => __('Kopioi linkki', 'instruction-manual'),
            'copied' => __('Kopioitu!', 'instruction-manual'),
            'report_issue' => __('Ilmoita ongelmasta', 'instruction-manual'),
        ],
        'en' => [
            'eyebrow' => __('WordPress guide', 'instruction-manual'),
            'progress_label' => __('Guide progress', 'instruction-manual'),
            'overview' => __('Overview', 'instruction-manual'),
            'steps' => __('Steps', 'instruction-manual'),
            'check' => __('Check', 'instruction-manual'),
            'related_short' => __('Related', 'instruction-manual'),
            'one_sentence' => __('In one sentence', 'instruction-manual'),
            'before' => __('Before you start', 'instruction-manual'),
            'watch' => __('Watch out for this', 'instruction-manual'),
            'final_check' => __('Final check', 'instruction-manual'),
            'related' => __('Related guides', 'instruction-manual'),
            'no_related' => __('No related guides are available yet.', 'instruction-manual'),
            'tools_label' => __('Guide tools', 'instruction-manual'),
            'contents' => __('Contents', 'instruction-manual'),
            'manual_contents' => __('Contents', 'instruction-manual'),
            'all_guides' => __('All guides', 'instruction-manual'),
            'on_this_page' => __('On this page', 'instruction-manual'),
            'you_are_here' => __('You are here', 'instruction-manual'),
            'quick_checklist' => __('Quick checklist before you start', 'instruction-manual'),
            'steps_intro' => __('Follow the steps in order. Mark each one done when you finish it.', 'instruction-manual'),
            'overview_intro' => __('A short summary of what this guide helps you accomplish.', 'instruction-manual'),
            'dark_mode_on' => __('Dark mode', 'instruction-manual'),
            'dark_mode_off' => __('Light mode', 'instruction-manual'),
            'copy_code' => __('Copy', 'instruction-manual'),
            'code_copied' => __('Copied', 'instruction-manual'),
            'guide_actions' => __('Actions', 'instruction-manual'),
            'print' => __('Print', 'instruction-manual'),
            'copy_link' => __('Copy link', 'instruction-manual'),
            'copied' => __('Copied!', 'instruction-manual'),
            'report_issue' => __('Report an issue', 'instruction-manual'),
        ],
    ];

    return $labels[$language][$key] ?? $labels['en'][$key] ?? $key;
}

function manual_instruction_difficulty_display_label(int $post_id): string
{
    return manual_instruction_language_code($post_id) === 'fi'
        ? manual_tutorial_difficulty_label_fi($post_id)
        : manual_instruction_difficulty_label($post_id);
}

function manual_instruction_review_status_display_label(int $post_id): string
{
    return manual_instruction_language_code($post_id) === 'fi'
        ? manual_review_status_label_fi($post_id)
        : manual_instruction_review_status($post_id);
}

function manual_instruction_language_display_label(int $post_id): string
{
    return manual_instruction_language_code($post_id) === 'fi'
        ? manual_language_label_fi($post_id)
        : manual_instruction_language_label($post_id);
}

function manual_document_title_parts(array $title): array
{
    $title['site'] = __('WordPress-ohjeet', 'instruction-manual');

    if (manual_is_glossary_view()) {
        $title['title'] = __('WordPress-sanasto', 'instruction-manual');
    } elseif (is_front_page()) {
        $title['title'] = __('WordPress-ohjeet selkeästi', 'instruction-manual');
    } elseif (is_post_type_archive('wp_instruction')) {
        $title['title'] = __('Kaikki ohjeet', 'instruction-manual');
    } elseif (is_tax('instruction_category')) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $title['title'] = manual_instruction_category_label($term);
        }
    }

    return $title;
}

function manual_instruction_language_label(int $post_id): string
{
    if (function_exists('gwi_get_instruction_language_label')) {
        return gwi_get_instruction_language_label($post_id);
    }

    return __('Instruction', 'instruction-manual');
}

function manual_instruction_archive_url(): string
{
    $archive_url = get_post_type_archive_link('wp_instruction');

    return $archive_url ?: home_url('/');
}

function manual_instruction_query_vars(array $vars): array
{
    $vars[] = 'instruction_language';
    $vars[] = 'manual_view';

    return $vars;
}

function manual_instruction_language_options(): array
{
    if (function_exists('gwi_languages')) {
        return gwi_languages();
    }

    return [
        'en' => __('English', 'instruction-manual'),
        'fi' => __('Finnish', 'instruction-manual'),
    ];
}

function manual_instruction_sanitize_language($language): string
{
    $language = is_string($language) ? sanitize_key(strtolower($language)) : '';
    $languages = manual_instruction_language_options();

    return array_key_exists($language, $languages) ? $language : '';
}

function manual_instruction_current_language(): string
{
    $raw_language = get_query_var('instruction_language');

    if (is_string($raw_language) && strtolower($raw_language) === 'all') {
        return '';
    }

    $language = manual_instruction_sanitize_language($raw_language);

    return $language !== '' ? $language : 'fi';
}

function manual_filter_instruction_archive_by_language(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    $is_instruction_context = $query->is_post_type_archive('wp_instruction')
        || $query->is_tax('instruction_category')
        || ($query->is_search() && $query->get('post_type') === 'wp_instruction');

    if (!$is_instruction_context) {
        return;
    }

    if ($query->is_post_type_archive('wp_instruction') || $query->is_tax('instruction_category') || $query->is_search()) {
        $query->set('posts_per_page', -1);
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
    }

    $raw_language = $query->get('instruction_language');

    if (is_string($raw_language) && strtolower($raw_language) === 'all') {
        return;
    }

    $language = manual_instruction_sanitize_language($raw_language);

    if ($language === '') {
        $language = 'fi';
        $query->set('instruction_language', 'fi');
    }

    $meta_query = (array) $query->get('meta_query');
    $meta_query[] = [
        'key' => '_gwi_language',
        'value' => $language,
        'compare' => '=',
    ];

    $query->set('meta_query', $meta_query);
}

function manual_instruction_language_filter_url(string $language = ''): string
{
    $base_url = manual_instruction_archive_url();

    if (is_tax('instruction_category')) {
        $term = get_queried_object();

        if ($term instanceof WP_Term) {
            $term_url = get_term_link($term);
            $base_url = !is_wp_error($term_url) ? $term_url : $base_url;
        }
    }

    if ($language === '') {
        return add_query_arg('instruction_language', 'all', $base_url);
    }

    return add_query_arg('instruction_language', manual_instruction_sanitize_language($language), $base_url);
}

function manual_instruction_url_with_language(string $url): string
{
    $language = manual_instruction_current_language();

    if ($language === '') {
        return remove_query_arg('instruction_language', $url);
    }

    return add_query_arg('instruction_language', $language, $url);
}

function manual_instruction_category_label(WP_Term $term): string
{
    $labels = [
        'fundamentals' => __('Perusteet', 'instruction-manual'),
        'site-config' => __('Sivuston asetukset', 'instruction-manual'),
        'block-editor' => __('Lohkoeditori', 'instruction-manual'),
        'classic-editor' => __('Perinteinen editori', 'instruction-manual'),
        'advanced' => __('Edistyneet ominaisuudet', 'instruction-manual'),
    ];

    if (isset($labels[$term->slug])) {
        return $labels[$term->slug];
    }

    if (trim($term->description) !== '') {
        return trim($term->description);
    }

    return $term->name;
}

function manual_instruction_card_excerpt(int $post_id, int $word_limit = 24): string
{
    $post = get_post($post_id);

    if (!$post instanceof WP_Post) {
        return '';
    }

    $text = manual_instruction_text_from_content($post->post_content);

    if ($text === '') {
        $text = wp_strip_all_tags(strip_shortcodes($post->post_excerpt));
    }

    $text = html_entity_decode($text, ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim((string) $text);

    return $text !== '' ? wp_trim_words($text, $word_limit, '...') : '';
}

function manual_instruction_text_from_content(string $content): string
{
    if ($content === '') {
        return '';
    }

    if (function_exists('parse_blocks')) {
        $text = manual_instruction_text_from_blocks(parse_blocks($content));

        if ($text !== '') {
            return $text;
        }
    }

    $content = preg_replace('~<!--\s+wp:general-wp-instructions/highlighted-screenshot\b.*?<!--\s+/wp:general-wp-instructions/highlighted-screenshot\s+-->~s', ' ', $content) ?? $content;
    $content = preg_replace('~<!--\s+/?wp:[^>]*?-->~s', ' ', $content) ?? $content;

    return wp_strip_all_tags(strip_shortcodes($content));
}

function manual_instruction_text_from_blocks(array $blocks): string
{
    $parts = [];

    foreach ($blocks as $block) {
        $block_name = $block['blockName'] ?? null;

        if (is_string($block_name) && strpos($block_name, 'general-wp-instructions/') === 0) {
            continue;
        }

        if (!empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            $inner_text = manual_instruction_text_from_blocks($block['innerBlocks']);

            if ($inner_text !== '') {
                $parts[] = $inner_text;
            }
        }

        $html = $block['innerHTML'] ?? '';

        if (is_string($html) && trim($html) !== '') {
            $parts[] = wp_strip_all_tags($html);
        }
    }

    return trim(preg_replace('/\s+/', ' ', implode(' ', $parts)) ?? '');
}

function manual_instruction_library_groups(): array
{
    return [
        'start' => [
            'title' => __('Aloita tästä', 'instruction-manual'),
            'description' => __('Perusohjeet yleisimpiin WordPressin muokkaustilanteisiin.', 'instruction-manual'),
            'terms' => ['fundamentals', 'classic-editor'],
        ],
        'blocks' => [
            'title' => __('Sisältölohkot', 'instruction-manual'),
            'description' => __('Ohjeet sivun osioiden, lohkojen ja valmiiden sisältörakenteiden käyttöön.', 'instruction-manual'),
            'terms' => ['block-editor'],
        ],
        'settings' => [
            'title' => __('Sivuston asetukset', 'instruction-manual'),
            'description' => __('Ohjeet valikoihin, käyttäjiin, sivuston asetuksiin ja julkaisun hallintaan.', 'instruction-manual'),
            'terms' => ['site-config'],
        ],
        'advanced' => [
            'title' => __('Edistyneet', 'instruction-manual'),
            'description' => __('Teknisemmät ohjeet suorituskykyyn, SEO-asetuksiin ja mukautettuihin kenttiin.', 'instruction-manual'),
            'terms' => ['advanced'],
        ],
    ];
}

function manual_instruction_group_key(int $post_id): string
{
    $terms = get_the_terms($post_id, 'instruction_category');

    if (is_wp_error($terms) || empty($terms)) {
        return 'advanced';
    }

    $groups = manual_instruction_library_groups();

    foreach ($terms as $term) {
        foreach ($groups as $group_key => $group) {
            if (in_array($term->slug, $group['terms'], true)) {
                return $group_key;
            }
        }
    }

    return 'advanced';
}

function manual_instruction_primary_category(int $post_id): ?WP_Term
{
    $terms = get_the_terms($post_id, 'instruction_category');

    if (is_wp_error($terms) || empty($terms)) {
        return null;
    }

    return $terms[0];
}

function manual_instruction_purpose(int $post_id): string
{
    $purpose = trim((string) get_post_meta($post_id, '_gwi_purpose', true));

    if ($purpose !== '') {
        return $purpose;
    }

    $slug = (string) get_post_field('post_name', $post_id);
    $language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';

    if ($language === 'en') {
        foreach (manual_instruction_english_task_purposes() as $needle => $text) {
            if (str_contains($slug, $needle)) {
                return $text;
            }
        }

        return manual_instruction_card_excerpt($post_id, 18);
    }

    foreach (manual_instruction_task_copy() as $needle => $copy) {
        if (str_contains($slug, $needle) && isset($copy['purpose'])) {
            return $copy['purpose'];
        }
    }

    return manual_instruction_card_excerpt($post_id, 18);
}

function manual_instruction_task_title(int $post_id): string
{
    $slug = (string) get_post_field('post_name', $post_id);
    $language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';

    if ($language === 'en') {
        return get_the_title($post_id);
    }

    foreach (manual_instruction_task_copy() as $needle => $copy) {
        if (str_contains($slug, $needle) && isset($copy['title'])) {
            return $copy['title'];
        }
    }

    return get_the_title($post_id);
}

function manual_instruction_english_task_purposes(): array
{
    return [
        'acf-blocks' => __('Use this when you want to add or edit special content sections on a page.', 'instruction-manual'),
        'performance-caching' => __('Use this when old content still appears after editing or cache needs checking.', 'instruction-manual'),
        'custom-fields' => __('Use this when content is stored in fields outside the normal editor.', 'instruction-manual'),
        'media-library' => __('Use this when you want to add, replace, or organize pictures and files.', 'instruction-manual'),
        'creating-menus' => __('Use this when you want to change the navigation links visitors use.', 'instruction-manual'),
        'seo-basics' => __('Use this when you want people and search engines to understand a page.', 'instruction-manual'),
        'block' => __('Use this when you want to add or edit content sections in the WordPress editor.', 'instruction-manual'),
    ];
}

function manual_instruction_task_copy(): array
{
    return [
        'dashboard-overview' => [
            'title' => __('Tutustu WordPressin hallintapaneeliin', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat löytää tärkeimmät WordPressin hallintanäkymät nopeasti.', 'instruction-manual'),
        ],
        'hallintapaneelin-yleiskatsaus' => [
            'title' => __('Tutustu WordPressin hallintapaneeliin', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat löytää tärkeimmät WordPressin hallintanäkymät nopeasti.', 'instruction-manual'),
        ],
        'creating-posts' => [
            'title' => __('Luo tai muokkaa artikkelia', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä uutisen, blogikirjoituksen tai muun ajankohtaisen sisällön.', 'instruction-manual'),
        ],
        'artikkelien-luominen-ja-muokkaus' => [
            'title' => __('Luo tai muokkaa artikkelia', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä uutisen, blogikirjoituksen tai muun ajankohtaisen sisällön.', 'instruction-manual'),
        ],
        'categories-tags' => [
            'title' => __('Järjestä artikkelit kategorioilla ja asiasanoilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat auttaa kävijöitä löytämään samaan aiheeseen liittyvät artikkelit.', 'instruction-manual'),
        ],
        'kategoriat-ja-asiasanat' => [
            'title' => __('Järjestä artikkelit kategorioilla ja asiasanoilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat auttaa kävijöitä löytämään samaan aiheeseen liittyvät artikkelit.', 'instruction-manual'),
        ],
        'creating-pages' => [
            'title' => __('Luo tai muokkaa sivua', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä pysyvän sivun tai päivittää olemassa olevan sivun sisältöä.', 'instruction-manual'),
        ],
        'sivujen-luominen-ja-muokkaus' => [
            'title' => __('Luo tai muokkaa sivua', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä pysyvän sivun tai päivittää olemassa olevan sivun sisältöä.', 'instruction-manual'),
        ],
        'media-library' => [
            'title' => __('Lisää tai vaihda kuva', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat ladata kuvan, vaihtaa tiedoston tai käyttää mediakirjastoa uudelleen.', 'instruction-manual'),
        ],
        'mediakirjasto' => [
            'title' => __('Lisää tai vaihda kuva', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat ladata kuvan, vaihtaa tiedoston tai käyttää mediakirjastoa uudelleen.', 'instruction-manual'),
        ],
        'managing-comments' => [
            'title' => __('Hallinnoi kommentteja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat hyväksyä, poistaa tai vastata sivuston kommentteihin.', 'instruction-manual'),
        ],
        'kommenttien-hallinta' => [
            'title' => __('Hallinnoi kommentteja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat hyväksyä, poistaa tai vastata sivuston kommentteihin.', 'instruction-manual'),
        ],
        'creating-menus' => [
            'title' => __('Muokkaa valikkoa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä, poistaa tai järjestää sivuston navigaatiolinkkejä.', 'instruction-manual'),
        ],
        'valikkojen-luominen' => [
            'title' => __('Muokkaa valikkoa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä, poistaa tai järjestää sivuston navigaatiolinkkejä.', 'instruction-manual'),
        ],
        'managing-users' => [
            'title' => __('Lisää tai hallitse käyttäjiä', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat antaa käyttäjälle oikeudet tai tarkistaa olemassa olevan käyttäjän roolin.', 'instruction-manual'),
        ],
        'kayttajien-hallinta' => [
            'title' => __('Lisää tai hallitse käyttäjiä', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat antaa käyttäjälle oikeudet tai tarkistaa olemassa olevan käyttäjän roolin.', 'instruction-manual'),
        ],
        'wordpress-settings' => [
            'title' => __('Tarkista WordPress-asetukset', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat muuttaa koko sivustoon vaikuttavia perusasetuksia.', 'instruction-manual'),
        ],
        'wordpress-asetukset' => [
            'title' => __('Tarkista WordPress-asetukset', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat muuttaa koko sivustoon vaikuttavia perusasetuksia.', 'instruction-manual'),
        ],
        'theme-customizer' => [
            'title' => __('Muokkaa teeman asetuksia', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat muuttaa teeman tarjoamia ulkoasu- ja sivustoasetuksia.', 'instruction-manual'),
        ],
        'teeman-mukauttaja' => [
            'title' => __('Muokkaa teeman asetuksia', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat muuttaa teeman tarjoamia ulkoasu- ja sivustoasetuksia.', 'instruction-manual'),
        ],
        'block-editor-basics' => [
            'title' => __('Aloita lohkoeditorin käyttö', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat ymmärtää, miten WordPressin lohkoeditori rakentuu.', 'instruction-manual'),
        ],
        'lohkoeditorin-perusteet' => [
            'title' => __('Aloita lohkoeditorin käyttö', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat ymmärtää, miten WordPressin lohkoeditori rakentuu.', 'instruction-manual'),
        ],
        'working-with-blocks' => [
            'title' => __('Lisää ja järjestä lohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä sisältölohkon tai vaihtaa lohkojen järjestystä sivulla.', 'instruction-manual'),
        ],
        'lohkojen-kaytto' => [
            'title' => __('Lisää ja järjestä lohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä sisältölohkon tai vaihtaa lohkojen järjestystä sivulla.', 'instruction-manual'),
        ],
        'common-content-blocks' => [
            'title' => __('Käytä yleisiä sisältölohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä otsikoita, tekstiä, listoja, painikkeita tai muita tavallisia osia.', 'instruction-manual'),
        ],
        'yleiset-sisaltolohkot' => [
            'title' => __('Käytä yleisiä sisältölohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä otsikoita, tekstiä, listoja, painikkeita tai muita tavallisia osia.', 'instruction-manual'),
        ],
        'media-blocks' => [
            'title' => __('Lisää kuvia ja mediaa lohkoilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä kuvan, gallerian, videon tai tiedoston sisältöön.', 'instruction-manual'),
        ],
        'medialohkot' => [
            'title' => __('Lisää kuvia ja mediaa lohkoilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä kuvan, gallerian, videon tai tiedoston sisältöön.', 'instruction-manual'),
        ],
        'layout-blocks' => [
            'title' => __('Rakenna sivun asettelua', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat jakaa sisällön sarakkeisiin, ryhmiin tai selkeisiin osioihin.', 'instruction-manual'),
        ],
        'asettelulohkot' => [
            'title' => __('Rakenna sivun asettelua', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat jakaa sisällön sarakkeisiin, ryhmiin tai selkeisiin osioihin.', 'instruction-manual'),
        ],
        'reusable-blocks' => [
            'title' => __('Käytä uudelleenkäytettäviä lohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sama sisältörakenne pitää lisätä usealle sivulle hallitusti.', 'instruction-manual'),
        ],
        'uudelleenkaytettavat-lohkot' => [
            'title' => __('Käytä uudelleenkäytettäviä lohkoja', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sama sisältörakenne pitää lisätä usealle sivulle hallitusti.', 'instruction-manual'),
        ],
        'classic-editor-basics' => [
            'title' => __('Muokkaa sisältöä perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivusto käyttää vanhempaa WordPress-editoria lohkoeditorin sijaan.', 'instruction-manual'),
        ],
        'perinteisen-editorin-perusteet' => [
            'title' => __('Muokkaa sisältöä perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivusto käyttää vanhempaa WordPress-editoria lohkoeditorin sijaan.', 'instruction-manual'),
        ],
        'classic-formatting' => [
            'title' => __('Muotoile tekstiä perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä otsikoita, listoja, linkkejä ja perusmuotoiluja vanhassa editorissa.', 'instruction-manual'),
        ],
        'perinteisen-editorin-muotoilu' => [
            'title' => __('Muotoile tekstiä perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä otsikoita, listoja, linkkejä ja perusmuotoiluja vanhassa editorissa.', 'instruction-manual'),
        ],
        'classic-media' => [
            'title' => __('Lisää mediaa perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä kuvia tai tiedostoja vanhemman editorin sisältöalueelle.', 'instruction-manual'),
        ],
        'perinteisen-editorin-media' => [
            'title' => __('Lisää mediaa perinteisessä editorissa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä kuvia tai tiedostoja vanhemman editorin sisältöalueelle.', 'instruction-manual'),
        ],
        'custom-fields' => [
            'title' => __('Muokkaa mukautettuja kenttiä', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sisältö löytyy erillisistä kentistä normaalin tekstieditorin sijaan.', 'instruction-manual'),
        ],
        'mukautetut-kentat' => [
            'title' => __('Muokkaa mukautettuja kenttiä', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sisältö löytyy erillisistä kentistä normaalin tekstieditorin sijaan.', 'instruction-manual'),
        ],
        'flexible-content' => [
            'title' => __('Rakenna sivua joustavilla sisältöosioilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivu koostuu valmiista osioista, joita voi lisätä ja järjestää.', 'instruction-manual'),
        ],
        'joustavat-sisaltoasettelut' => [
            'title' => __('Rakenna sivua joustavilla sisältöosioilla', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivu koostuu valmiista osioista, joita voi lisätä ja järjestää.', 'instruction-manual'),
        ],
        'acf-blocks' => [
            'title' => __('Lisää tai muokkaa ACF-lohkoa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä tai päivittää sivun erikoissisältöosion.', 'instruction-manual'),
        ],
        'acf-lohkot' => [
            'title' => __('Lisää tai muokkaa ACF-lohkoa', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat lisätä tai päivittää sivun erikoissisältöosion.', 'instruction-manual'),
        ],
        'seo-basics' => [
            'title' => __('Tarkista SEO-perusteet', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat auttaa ihmisiä ja hakukoneita ymmärtämään sivun sisällön.', 'instruction-manual'),
        ],
        'seo-perusteet' => [
            'title' => __('Tarkista SEO-perusteet', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun haluat auttaa ihmisiä ja hakukoneita ymmärtämään sivun sisällön.', 'instruction-manual'),
        ],
        'performance-caching' => [
            'title' => __('Korjaa päivittymätön sivu', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivu näyttää vanhaa sisältöä tai haluat tarkistaa välimuistin vaikutuksen.', 'instruction-manual'),
        ],
        'suorituskyky-ja-valimuisti' => [
            'title' => __('Korjaa päivittymätön sivu', 'instruction-manual'),
            'purpose' => __('Käytä tätä ohjetta, kun sivu näyttää vanhaa sisältöä tai haluat tarkistaa välimuistin vaikutuksen.', 'instruction-manual'),
        ],
    ];
}

function manual_instruction_difficulty(int $post_id): string
{
    $difficulty = sanitize_key((string) get_post_meta($post_id, '_gwi_difficulty', true));
    $allowed = ['basic', 'intermediate', 'advanced'];

    if (in_array($difficulty, $allowed, true)) {
        return $difficulty;
    }

    $category = manual_instruction_primary_category($post_id);

    if ($category instanceof WP_Term && $category->slug === 'advanced') {
        return 'intermediate';
    }

    return 'basic';
}

function manual_instruction_difficulty_label(int $post_id): string
{
    $labels = [
        'basic' => __('Basic', 'instruction-manual'),
        'intermediate' => __('Intermediate', 'instruction-manual'),
        'advanced' => __('Advanced', 'instruction-manual'),
    ];

    return $labels[manual_instruction_difficulty($post_id)] ?? $labels['basic'];
}

function manual_instruction_estimated_minutes(int $post_id): int
{
    $minutes = absint(get_post_meta($post_id, '_gwi_estimated_minutes', true));

    if ($minutes > 0) {
        return min(60, $minutes);
    }

    $content = manual_instruction_text_from_content((string) get_post_field('post_content', $post_id));
    $word_count = str_word_count($content);

    return max(2, min(20, (int) ceil($word_count / 130)));
}

function manual_instruction_review_status(int $post_id): string
{
    $status = sanitize_key((string) get_post_meta($post_id, '_gwi_review_status', true));
    $labels = [
        'draft' => __('Draft', 'instruction-manual'),
        'needs-review' => __('Needs review', 'instruction-manual'),
        'tested' => __('Tested', 'instruction-manual'),
        'outdated' => __('Outdated', 'instruction-manual'),
        'deprecated' => __('Deprecated', 'instruction-manual'),
    ];

    return $labels[$status] ?? $labels['needs-review'];
}

function manual_instruction_review_label(int $post_id): string
{
    $raw_date = get_post_meta($post_id, '_gwi_last_reviewed', true);
    $last_reviewed = function_exists('gwi_sanitize_review_date')
        ? gwi_sanitize_review_date($raw_date)
        : (is_string($raw_date) ? preg_replace('/[^0-9-]/', '', $raw_date) : '');

    if ($last_reviewed !== '') {
        return sprintf(__('Reviewed %s', 'instruction-manual'), $last_reviewed);
    }

    return __('Review needed', 'instruction-manual');
}

function manual_instruction_owner(int $post_id): string
{
    $owner = trim((string) get_post_meta($post_id, '_gwi_owner', true));

    return $owner !== '' ? $owner : __('Documentation', 'instruction-manual');
}

function manual_instruction_clarity_score(int $post_id): int
{
    $score = absint(get_post_meta($post_id, '_gwi_clarity_score', true));

    if ($score > 0) {
        return min(100, $score);
    }

    if (function_exists('gwi_calculate_instruction_clarity_score')) {
        return gwi_calculate_instruction_clarity_score(
            (string) get_the_title($post_id),
            (string) get_post_field('post_content', $post_id),
            (string) get_post_meta($post_id, '_gwi_purpose', true),
            function_exists('gwi_get_instruction_clarity_checks') ? gwi_get_instruction_clarity_checks($post_id) : []
        );
    }

    return 0;
}

function manual_problem_searches(): array
{
    return [
        __('I changed text but it does not show', 'instruction-manual') => 'cache old version page not updating',
        __('I need to hide a page', 'instruction-manual') => 'hide page visibility draft',
        __('I want to add a button', 'instruction-manual') => 'button block link',
        __('The image looks wrong on mobile', 'instruction-manual') => 'image mobile media picture',
    ];
}

function manual_suggested_searches(): array
{
    return [
        __('change image', 'instruction-manual'),
        __('page not updating', 'instruction-manual'),
        __('add button', 'instruction-manual'),
        __('hide page', 'instruction-manual'),
        __('edit menu', 'instruction-manual'),
    ];
}

function manual_quick_start_categories(): array
{
    $terms = get_terms([
        'taxonomy' => 'instruction_category',
        'slug' => ['fundamentals', 'block-editor', 'site-config', 'advanced'],
        'hide_empty' => false,
    ]);

    $term_map = [];
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_map[$term->slug] = get_term_link($term);
        }
    }

    $fallback_url = manual_instruction_archive_url();

    return [
        [
            'title' => __('Start here', 'instruction-manual'),
            'description' => __('Basic editing, pages, images, links, menus, and everyday WordPress work.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['fundamentals'] ?? $fallback_url),
        ],
        [
            'title' => __('Content blocks', 'instruction-manual'),
            'description' => __('Blocks, reusable sections, ACF blocks, flexible content, and custom fields.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['block-editor'] ?? $fallback_url),
        ],
        [
            'title' => __('Site settings', 'instruction-manual'),
            'description' => __('Navigation, users, SEO basics, site settings, and publishing controls.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['site-config'] ?? $fallback_url),
        ],
        [
            'title' => __('Troubleshooting', 'instruction-manual'),
            'description' => __('Common problems and fixes: pages not updating, cache, mobile issues.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['advanced'] ?? $fallback_url),
        ],
    ];
}

function manual_instruction_review_status_is_current(int $post_id, int $days_threshold = 90): bool
{
    $raw_date = get_post_meta($post_id, '_gwi_last_reviewed', true);
    if ($raw_date === '') {
        return false;
    }
    $reviewed_time = strtotime($raw_date);
    if ($reviewed_time === false) {
        return false;
    }
    $threshold = time() - ($days_threshold * DAY_IN_SECONDS);
    return $reviewed_time >= $threshold;
}

function manual_glossary_terms(): array
{
    return [
        __('Lohko', 'instruction-manual') => __('Sisältöpalsta WordPress-editorissa. Lohko voi olla teksti, kuva, painike tai asetteluosio.', 'instruction-manual'),
        __('Välimuisti', 'instruction-manual') => __('Tallennettu versio sivusta. Tyhjennä se, kun vanha sisältö näkyy vielä muokkauksen jälkeen.', 'instruction-manual'),
        __('Teema', 'instruction-manual') => __('Määrittää sivuston ulkoasun ja toiminnallisuudet.', 'instruction-manual'),
        __('Plugin', 'instruction-manual') => __('Laajennus, joka lisää sivustoon uusia ominaisuuksia.', 'instruction-manual'),
        __('Kenttä', 'instruction-manual') => __('Lisätietokenttä, johon syötetään dataa. Näkyy sisällössä.', 'instruction-manual'),
        __('Osoite', 'instruction-manual') => __('Sivun URL-osoitteen viimeinen osa.', 'instruction-manual'),
        __('Sivupohja', 'instruction-manual') => __('Uudelleenkäytettävä rakenne, joka määrittää mitä sisältökenttiä tai osioita on saatavilla.', 'instruction-manual'),
        __('Uudelleenohjaus', 'instruction-manual') => __('Sääntö, joka ohjaa kävijät yhdestä URL-osoitteesta toiseen.', 'instruction-manual'),
        __('Julkinen sivu', 'instruction-manual') => __('Sivu, jonka kävijät näkevät – ei WordPressin muokkausnäkymä.', 'instruction-manual'),
        __('ACF', 'instruction-manual') => __('Advanced Custom Fields -lisäosa, jolla sivuille ja artikkeleille voidaan lisätä omia kenttiä.', 'instruction-manual'),
        __('ACF-lohko', 'instruction-manual') => __('Mukautettu lohko, jonka kentät on rakennettu Advanced Custom Fieldsilla.', 'instruction-manual'),
        __('Ajastettu julkaisu', 'instruction-manual') => __('Sisältö, joka on asetettu julkaistavaksi automaattisesti tiettynä päivänä ja kellonaikana.', 'instruction-manual'),
        __('Alatunniste', 'instruction-manual') => __('Sivuston alaosa, jossa on usein yhteystietoja, linkkejä tai lisänavigaatio.', 'instruction-manual'),
        __('Alt-teksti', 'instruction-manual') => __('Kuvan vaihtoehtoinen teksti. Se auttaa hakukoneita ja ruudunlukijoita ymmärtämään kuvan sisällön.', 'instruction-manual'),
        __('Ankkurilinkki', 'instruction-manual') => __('Linkki, joka vie saman sivun tiettyyn kohtaan.', 'instruction-manual'),
        __('Arkisto', 'instruction-manual') => __('Sivu, joka listaa useita sisältöjä, kuten artikkeleita, kategorian sisältöjä tai hakutuloksia.', 'instruction-manual'),
        __('Artikkeli', 'instruction-manual') => __('Ajankohtainen julkaisu tai blogikirjoitus, joka voi kuulua kategorioihin ja avainsanoihin.', 'instruction-manual'),
        __('Asetukset', 'instruction-manual') => __('WordPressin hallinta-alue, jossa säädetään sivuston yleisiä toimintoja.', 'instruction-manual'),
        __('Asettelu', 'instruction-manual') => __('Sisällön rakenne sivulla: esimerkiksi sarakkeet, nostot, kuvat ja tekstialueet.', 'instruction-manual'),
        __('Avainsana', 'instruction-manual') => __('Artikkeleihin liitettävä tunniste, jolla samankaltaista sisältöä voidaan ryhmitellä.', 'instruction-manual'),
        __('Automaattitallennus', 'instruction-manual') => __('WordPressin väliaikainen tallennus, joka voi palauttaa muutoksia jos selain sulkeutuu vahingossa.', 'instruction-manual'),
        __('CDN', 'instruction-manual') => __('Sisällönjakeluverkko, joka voi nopeuttaa kuvien, tyylien ja tiedostojen lataamista eri sijainneista.', 'instruction-manual'),
        __('Editorin sivupalkki', 'instruction-manual') => __('Muokkausnäkymän oikea paneeli, jossa säädetään sivun, artikkelin tai valitun lohkon asetuksia.', 'instruction-manual'),
        __('Esikatselu', 'instruction-manual') => __('Näkymä, jossa muutosta voi tarkistaa ennen julkaisua tai päivittämistä.', 'instruction-manual'),
        __('Etupää', 'instruction-manual') => __('Sivuston julkinen puoli, jonka kävijät näkevät selaimessa.', 'instruction-manual'),
        __('Galleria', 'instruction-manual') => __('Usean kuvan kokonaisuus, joka näytetään sivulla yhdessä.', 'instruction-manual'),
        __('Hallintapalkki', 'instruction-manual') => __('Kirjautuneelle käyttäjälle näkyvä yläpalkki, josta pääsee nopeasti muokkaukseen ja hallintaan.', 'instruction-manual'),
        __('Hallintapaneeli', 'instruction-manual') => __('WordPressin sisäinen hallinta-alue, jossa sisältöä, käyttäjiä, teemoja ja asetuksia muokataan.', 'instruction-manual'),
        __('Hakukoneoptimointi', 'instruction-manual') => __('Sisällön ja sivuston rakenteen parantamista, jotta hakukoneet ymmärtävät sivut paremmin.', 'instruction-manual'),
        __('Hakutulos', 'instruction-manual') => __('Sivuston sisäisen haun tai hakukoneen näyttämä tulos.', 'instruction-manual'),
        __('Hosting', 'instruction-manual') => __('Palvelu ja palvelinympäristö, jossa WordPress-sivusto sijaitsee.', 'instruction-manual'),
        __('HTML', 'instruction-manual') => __('Merkintäkieli, jolla verkkosivun rakenne kuvataan. WordPress tuottaa sitä sivun sisällöstä ja lohkoista.', 'instruction-manual'),
        __('HTTPS', 'instruction-manual') => __('Suojattu verkkoyhteys, joka näkyy selaimen osoitteessa lukon tai https-alkuisen osoitteen muodossa.', 'instruction-manual'),
        __('Indeksointi', 'instruction-manual') => __('Prosessi, jossa hakukone lisää sivun omaan hakemistoonsa hakutuloksia varten.', 'instruction-manual'),
        __('Joustava sisältö', 'instruction-manual') => __('ACF:n kenttätyyppi, jolla sivulle voidaan lisätä ja järjestää valmiita sisältöosioita.', 'instruction-manual'),
        __('Julkaise', 'instruction-manual') => __('Toiminto, joka tekee luonnoksesta julkisen sivun tai artikkelin.', 'instruction-manual'),
        __('Julkaisupäivä', 'instruction-manual') => __('Päivä ja aika, jolloin sisältö julkaistaan tai on julkaistu.', 'instruction-manual'),
        __('Julkaisutila', 'instruction-manual') => __('Kertoo onko sisältö luonnos, julkaistu, ajastettu, yksityinen tai roskakorissa.', 'instruction-manual'),
        __('Kappale', 'instruction-manual') => __('Tavallinen tekstilohko WordPress-editorissa.', 'instruction-manual'),
        __('Kategoria', 'instruction-manual') => __('Artikkelien aihealue, jolla sisältöä voidaan ryhmitellä laajempiin kokonaisuuksiin.', 'instruction-manual'),
        __('Keskustelun asetukset', 'instruction-manual') => __('Asetukset, joissa hallitaan kommentteja, ilmoituksia ja keskustelun oletuksia.', 'instruction-manual'),
        __('Kenttäryhmä', 'instruction-manual') => __('ACF:n kokoelma kenttiä, joka näkyy tietyissä muokkausnäkymissä.', 'instruction-manual'),
        __('Kirjoittamisen asetukset', 'instruction-manual') => __('Asetukset, jotka vaikuttavat sisällön kirjoittamisen oletuksiin ja julkaisutapoihin.', 'instruction-manual'),
        __('Kirjautuminen', 'instruction-manual') => __('Toiminto, jolla käyttäjä pääsee WordPressin hallintaan omilla tunnuksillaan.', 'instruction-manual'),
        __('Kirjoittaja', 'instruction-manual') => __('Käyttäjärooli, joka voi yleensä kirjoittaa ja julkaista omia artikkeleitaan.', 'instruction-manual'),
        __('Kommentti', 'instruction-manual') => __('Kävijän tai käyttäjän jättämä viesti artikkelin tai sivun yhteyteen, jos kommentointi on käytössä.', 'instruction-manual'),
        __('Kommenttien moderointi', 'instruction-manual') => __('Kommenttien tarkistamista, hyväksymistä, poistamista tai merkitsemistä roskapostiksi.', 'instruction-manual'),
        __('Kommenttijono', 'instruction-manual') => __('Näkymä, jossa odottavat, hyväksytyt, roskapostiksi merkityt ja poistetut kommentit näkyvät.', 'instruction-manual'),
        __('Kotisivu', 'instruction-manual') => __('Sivuston etusivu. Se voi olla staattinen sivu tai lista uusimmista artikkeleista.', 'instruction-manual'),
        __('Kuva', 'instruction-manual') => __('Mediakirjastoon ladattu kuvatiedosto, jota voidaan käyttää sivuilla ja artikkeleissa.', 'instruction-manual'),
        __('Kuvateksti', 'instruction-manual') => __('Kuvan yhteydessä näkyvä lyhyt seliteteksti.', 'instruction-manual'),
        __('Käyttäjä', 'instruction-manual') => __('Henkilö, jolla on tunnus WordPress-sivustolle ja jokin käyttäjärooli.', 'instruction-manual'),
        __('Käyttäjärooli', 'instruction-manual') => __('Määrittää mitä käyttäjä saa tehdä WordPressissä, kuten muokata sivuja tai hallita asetuksia.', 'instruction-manual'),
        __('Käyttöoikeus', 'instruction-manual') => __('Tietty oikeus tehdä jokin toiminto WordPressissä, kuten julkaista, muokata tai hallita asetuksia.', 'instruction-manual'),
        __('Liitetiedosto', 'instruction-manual') => __('Mediakirjastoon ladattu tiedosto, kuten kuva, PDF tai dokumentti.', 'instruction-manual'),
        __('Linkki', 'instruction-manual') => __('Klikattava osoite, joka vie toiselle sivulle, tiedostoon tai sivustolle.', 'instruction-manual'),
        __('Lisäosa', 'instruction-manual') => __('WordPressiin asennettava toiminnallisuuspaketti. Sama asia kuin plugin.', 'instruction-manual'),
        __('Lista', 'instruction-manual') => __('Lohko, jolla tehdään luettelomerkkejä tai numeroituja listoja.', 'instruction-manual'),
        __('Lohkoeditori', 'instruction-manual') => __('WordPressin nykyinen editori, jossa sisältö rakennetaan erillisistä lohkoista.', 'instruction-manual'),
        __('Lohkomalli', 'instruction-manual') => __('Valmis lohkojen yhdistelmä, jonka voi lisätä sivulle uudelleenkäytettäväksi rakenteeksi.', 'instruction-manual'),
        __('Logo', 'instruction-manual') => __('Sivuston tunnuskuva tai tekstimuotoinen merkki, joka näkyy usein ylätunnisteessa.', 'instruction-manual'),
        __('Luonnos', 'instruction-manual') => __('Tallennettu sisältö, jota ei ole vielä julkaistu kävijöille.', 'instruction-manual'),
        __('Lyhytkoodi', 'instruction-manual') => __('Hakasulkeissa kirjoitettava koodi, joka lisää sivulle valmiin toiminnon tai sisällön.', 'instruction-manual'),
        __('Lukemisen asetukset', 'instruction-manual') => __('Asetukset, joissa valitaan esimerkiksi etusivu ja artikkeleiden näyttötapa.', 'instruction-manual'),
        __('Media-asetukset', 'instruction-manual') => __('Asetukset, jotka vaikuttavat kuvien oletuskokoihin ja mediatiedostojen käsittelyyn.', 'instruction-manual'),
        __('Mediakirjasto', 'instruction-manual') => __('WordPressin paikka kuville, PDF-tiedostoille, videoille ja muille ladatuille tiedostoille.', 'instruction-manual'),
        __('Metakuvaus', 'instruction-manual') => __('Hakukoneille tarkoitettu lyhyt kuvaus sivun sisällöstä. Usein SEO-lisäosan kenttä.', 'instruction-manual'),
        __('Mobiilinäkymä', 'instruction-manual') => __('Sivun ulkoasu puhelimella tai pienellä näytöllä.', 'instruction-manual'),
        __('Mukautettu kenttä', 'instruction-manual') => __('Lisäkenttä, johon tallennetaan sivulle tai artikkelille erillistä dataa.', 'instruction-manual'),
        __('Mukautettu linkki', 'instruction-manual') => __('Valikkoon lisättävä linkki, joka voi osoittaa mihin tahansa URL-osoitteeseen.', 'instruction-manual'),
        __('Mukautettu sisältötyyppi', 'instruction-manual') => __('WordPressiin lisätty oma sisältölaji, kuten ohje, tapahtuma, tuote tai henkilö.', 'instruction-manual'),
        __('Muokkaaja', 'instruction-manual') => __('Henkilö, joka päivittää sivuston sisältöä WordPressissä.', 'instruction-manual'),
        __('Murupolku', 'instruction-manual') => __('Navigaatiopolku, joka näyttää käyttäjän sijainnin sivuston rakenteessa.', 'instruction-manual'),
        __('Navigaatio', 'instruction-manual') => __('Sivuston linkkirakenne, jonka avulla kävijä liikkuu sivulta toiselle.', 'instruction-manual'),
        __('Noindex', 'instruction-manual') => __('SEO-asetus, jolla hakukoneita pyydetään olemaan lisäämättä sivua hakutuloksiin.', 'instruction-manual'),
        __('Osoiterakenne', 'instruction-manual') => __('Sääntö, jonka mukaan WordPress muodostaa sivujen ja artikkeleiden URL-osoitteet.', 'instruction-manual'),
        __('Osallistuja', 'instruction-manual') => __('Käyttäjärooli, joka voi yleensä kirjoittaa luonnoksia mutta ei julkaista niitä itse.', 'instruction-manual'),
        __('Otsikko', 'instruction-manual') => __('Teksti, joka nimeää sivun, artikkelin tai sisältöosion.', 'instruction-manual'),
        __('Painike', 'instruction-manual') => __('Korostettu linkki, jota käytetään selkeänä toimintakehotuksena.', 'instruction-manual'),
        __('Palautusversio', 'instruction-manual') => __('Aiempi tallennettu versio sisällöstä, johon voidaan tarvittaessa palata.', 'instruction-manual'),
        __('Palvelin', 'instruction-manual') => __('Tekninen ympäristö, jossa WordPress, tietokanta ja tiedostot toimivat.', 'instruction-manual'),
        __('PDF', 'instruction-manual') => __('Tiedostomuoto, jota käytetään usein ladattaville dokumenteille ja lomakkeille.', 'instruction-manual'),
        __('Perinteinen editori', 'instruction-manual') => __('WordPressin vanhempi tekstieditori, jossa sisältöä muokataan yhtenä pääsisältöalueena.', 'instruction-manual'),
        __('Päävalikko', 'instruction-manual') => __('Sivuston tärkein navigaatiovalikko, joka näkyy yleensä ylätunnisteessa.', 'instruction-manual'),
        __('Pysyvä linkki', 'instruction-manual') => __('Sivun tai artikkelin pysyvä URL-osoite.', 'instruction-manual'),
        __('Päivitä', 'instruction-manual') => __('Painike, jolla julkaistun sivun tai artikkelin muutokset tallennetaan.', 'instruction-manual'),
        __('Päivitys', 'instruction-manual') => __('WordPressin, teeman tai lisäosan uusi versio, joka voi sisältää korjauksia ja parannuksia.', 'instruction-manual'),
        __('Pääkäyttäjä', 'instruction-manual') => __('Käyttäjärooli, jolla on laajat oikeudet sivuston sisältöön, asetuksiin, teemoihin ja lisäosiin.', 'instruction-manual'),
        __('Responsiivinen', 'instruction-manual') => __('Sivusto tai asettelu, joka mukautuu eri kokoisille näytöille.', 'instruction-manual'),
        __('Robots.txt', 'instruction-manual') => __('Tiedosto tai sääntö, jolla annetaan hakukoneille ohjeita sivuston indeksointiin.', 'instruction-manual'),
        __('Rooli', 'instruction-manual') => __('Käyttäjälle annettu oikeustaso, kuten pääkäyttäjä, toimittaja tai tilaaja.', 'instruction-manual'),
        __('Roskakori', 'instruction-manual') => __('Paikka poistetulle sisällölle ennen pysyvää poistamista.', 'instruction-manual'),
        __('Roskaposti', 'instruction-manual') => __('Ei-toivottu kommentti tai viesti, joka kannattaa poistaa tai suodattaa.', 'instruction-manual'),
        __('Salasanasuojattu', 'instruction-manual') => __('Sisältö, jonka näkeminen vaatii erillisen salasanan.', 'instruction-manual'),
        __('SEO', 'instruction-manual') => __('Hakukoneoptimoinnin lyhenne. Tavoitteena on tehdä sivusta ymmärrettävämpi hakukoneille ja käyttäjille.', 'instruction-manual'),
        __('Sisäinen linkki', 'instruction-manual') => __('Linkki saman sivuston toiseen sivuun tai artikkeliin.', 'instruction-manual'),
        __('Sisältöosio', 'instruction-manual') => __('Sivun erillinen alue, kuten hero, teksti-kuva-osio, nosto tai yhteydenottolohko.', 'instruction-manual'),
        __('Sisältötyyppi', 'instruction-manual') => __('WordPressin tapa erottaa erilaiset sisällöt, kuten sivut, artikkelit ja omat sisältötyypit.', 'instruction-manual'),
        __('Sitemap', 'instruction-manual') => __('Sivustokartta, joka auttaa hakukoneita löytämään sivuston tärkeät URL-osoitteet.', 'instruction-manual'),
        __('Sivu', 'instruction-manual') => __('Pysyvämpi sisältö, kuten etusivu, yhteystiedot tai palvelusivu.', 'instruction-manual'),
        __('Sivupalkki', 'instruction-manual') => __('Sivun reuna-alue, jossa voi olla navigaatiota, widgettejä tai muuta lisäsisältöä.', 'instruction-manual'),
        __('Sivuston kuvaus', 'instruction-manual') => __('Lyhyt kuvausteksti, joka kertoo sivuston tarkoituksen. Löytyy yleisistä asetuksista.', 'instruction-manual'),
        __('Sivuston otsikko', 'instruction-manual') => __('Sivuston nimi WordPressin asetuksissa.', 'instruction-manual'),
        __('Sivustokuvake', 'instruction-manual') => __('Pieni kuvake, joka näkyy selaimen välilehdessä ja kirjanmerkeissä.', 'instruction-manual'),
        __('Slug', 'instruction-manual') => __('URL-osoitteen tekninen tunniste, esimerkiksi sivun nimestä muodostettu polku.', 'instruction-manual'),
        __('SSL', 'instruction-manual') => __('Suojaustekniikka, joka mahdollistaa HTTPS-yhteyden sivustolle.', 'instruction-manual'),
        __('Synkronoitu malli', 'instruction-manual') => __('Uudelleenkäytettävä lohkokokonaisuus, jonka muutokset päivittyvät kaikkiin sen käyttökohteisiin.', 'instruction-manual'),
        __('Taksonomia', 'instruction-manual') => __('Sisällön ryhmittelytapa, kuten kategoriat ja avainsanat.', 'instruction-manual'),
        __('Taulukko', 'instruction-manual') => __('Lohko, jolla esitetään tietoa riveinä ja sarakkeina.', 'instruction-manual'),
        __('Teeman mukauttaja', 'instruction-manual') => __('Ulkoasun asetusten näkymä, jossa joitakin teeman valintoja voidaan muuttaa esikatselun kanssa.', 'instruction-manual'),
        __('Tekstieditori', 'instruction-manual') => __('Muokkaustila, jossa sisältöä voidaan käsitellä teknisempänä tekstinä tai HTML:nä.', 'instruction-manual'),
        __('Tietokanta', 'instruction-manual') => __('Paikka, johon WordPress tallentaa sisällöt, asetukset, käyttäjät ja monet lisäosien tiedot.', 'instruction-manual'),
        __('Tilaaja', 'instruction-manual') => __('Käyttäjärooli, jolla on yleensä vain omaan profiiliin liittyviä oikeuksia.', 'instruction-manual'),
        __('Toimittaja', 'instruction-manual') => __('Käyttäjärooli, joka voi yleensä muokata ja julkaista useiden käyttäjien sisältöjä.', 'instruction-manual'),
        __('Turvallisuus', 'instruction-manual') => __('Käytännöt ja asetukset, joilla sivustoa suojataan väärinkäytöltä ja teknisiltä riskeiltä.', 'instruction-manual'),
        __('Tyylit', 'instruction-manual') => __('Ulkoasun asetukset, kuten värit, typografia, välit ja lohkojen esitystapa.', 'instruction-manual'),
        __('Ulkoinen linkki', 'instruction-manual') => __('Linkki toiselle sivustolle.', 'instruction-manual'),
        __('Upotus', 'instruction-manual') => __('Sisältö, joka näytetään toisesta palvelusta, kuten video tai kartta.', 'instruction-manual'),
        __('URL', 'instruction-manual') => __('Verkko-osoite, joka kertoo selaimelle mistä sivu tai tiedosto löytyy.', 'instruction-manual'),
        __('Valikko', 'instruction-manual') => __('Kokoelma linkkejä, joita käytetään sivuston navigaatiossa.', 'instruction-manual'),
        __('Varmuuskopio', 'instruction-manual') => __('Kopio sivuston tiedostoista ja tietokannasta palautusta varten.', 'instruction-manual'),
        __('Verkkotunnus', 'instruction-manual') => __('Sivuston domain-nimi, kuten esimerkki.fi.', 'instruction-manual'),
        __('Virhe 404', 'instruction-manual') => __('Tilanne, jossa pyydettyä sivua tai tiedostoa ei löydy annetusta osoitteesta.', 'instruction-manual'),
        __('Visuaalinen editori', 'instruction-manual') => __('Muokkaustila, jossa sisältö näyttää mahdollisimman lähellä lopullista ulkoasua.', 'instruction-manual'),
        __('Widget', 'instruction-manual') => __('Pieni sisältö- tai toimintoalue, jota käytetään esimerkiksi sivupalkissa tai alatunnisteessa.', 'instruction-manual'),
        __('WordPress-hallinta', 'instruction-manual') => __('Kirjautumisen takana oleva alue, jossa sivustoa ylläpidetään ja sisältöä muokataan.', 'instruction-manual'),
        __('Yleiset asetukset', 'instruction-manual') => __('Asetussivu, jossa määritetään muun muassa sivuston nimi, kuvaus, osoite ja aikavyöhyke.', 'instruction-manual'),
        __('Yksityinen', 'instruction-manual') => __('Julkaisutila, jossa sisältö ei näy kaikille kävijöille.', 'instruction-manual'),
        __('Ylätunniste', 'instruction-manual') => __('Sivuston yläosa, jossa on usein logo, päävalikko ja haku.', 'instruction-manual'),
    ];
}

function manual_search_synonyms(): array
{
    return [
        'image' => ['picture', 'photo', 'media', 'kuva'],
        'menu' => ['navigation', 'nav', 'valikko'],
        'block' => ['section', 'content element', 'lohko'],
        'cache' => ['page not updating', 'old version', 'välimuisti', 'valimuisti'],
        'page' => ['sivu', 'post', 'article'],
        'button' => ['link button', 'call to action', 'cta'],
    ];
}

function manual_expanded_search_terms(string $search): array
{
    $search = strtolower(trim($search));
    $terms = $search !== '' ? [$search] : [];

    foreach (manual_search_synonyms() as $term => $synonyms) {
        $haystack = array_merge([$term], $synonyms);

        foreach ($haystack as $candidate) {
            if ($candidate !== '' && str_contains($search, strtolower($candidate))) {
                $terms = array_merge($terms, [$term], $synonyms);
                break;
            }
        }
    }

    return array_values(array_unique(array_filter($terms)));
}

function manual_expand_instruction_search(string $search, WP_Query $query): string
{
    if (is_admin() || !$query->is_search() || $query->get('post_type') !== 'wp_instruction') {
        return $search;
    }

    $search_query = (string) $query->get('s');
    $terms_fi = manual_expand_finnish_search($search_query);
    $terms_en = manual_expanded_search_terms($search_query);
    $terms = array_values(array_unique(array_merge($terms_fi, $terms_en)));

    if (count($terms) < 2) {
        return $search;
    }

    global $wpdb;

    $clauses = [];

    foreach ($terms as $term) {
        $like = '%' . $wpdb->esc_like($term) . '%';
        $clauses[] = $wpdb->prepare(
            "({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_excerpt LIKE %s OR {$wpdb->posts}.post_content LIKE %s)",
            $like,
            $like,
            $like
        );
    }

    return ' AND (' . implode(' OR ', $clauses) . ')';
}

function manual_cleanup_instruction_content_language(string $content): string
{
    if (!is_singular('wp_instruction') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (manual_instruction_language_code(get_the_ID()) !== 'fi') {
        return $content;
    }

    $replacements = [
        'Language' => 'Kieli',
        'Finnish' => 'Suomi',
        'English' => 'Englanti',
        'Tip:' => 'Vinkki:',
        'kayttaytyvat' => 'käyttäytyvät',
        'kayttaytyy' => 'käyttäytyy',
        'kayttajatilin' => 'käyttäjätilin',
        'kayttajan' => 'käyttäjän',
        'kayttajat' => 'käyttäjät',
        'kayttajia' => 'käyttäjiä',
        'kayttaja' => 'käyttäjä',
        'kayttaa' => 'käyttää',
        'kayttoa' => 'käyttöä',
        'kaytto' => 'käyttö',
        'kayta' => 'käytä',
        'Kayta' => 'Käytä',
        'Kayttajat' => 'Käyttäjät',
        'Paakayttaja' => 'Pääkäyttäjä',
        'Paakayttajilla' => 'Pääkäyttäjillä',
        'sisaltloosiot' => 'sisältöosiot',
        'sisaltoosioiden' => 'sisältöosioiden',
        'sisaltoon' => 'sisältöön',
        'sisaltoosi' => 'sisältöösi',
        'sisaltoa' => 'sisältöä',
        'sisaltosi' => 'sisältösi',
        'sisalto' => 'sisältö',
        'Sisalto' => 'Sisältö',
        'tyokalurivissa' => 'työkalurivissä',
        'tyokalurivin' => 'työkalurivin',
        'tyokalurivia' => 'työkaluriviä',
        'tyokalurivi' => 'työkalurivi',
        'tyokaluja' => 'työkaluja',
        'työkaluja' => 'työkaluja',
        'lisaajan' => 'lisääjän',
        'lisaaja' => 'lisääjä',
        'lisata' => 'lisätä',
        'Lisaa' => 'Lisää',
        'lisaa' => 'lisää',
        'nähdaksesi' => 'nähdäksesi',
        'nhdäksesi' => 'nähdäksesi',
        'tehda' => 'tehdä',
        'maarittaa' => 'määrittää',
        'sahkoposti' => 'sähköposti',
        'aanitiedostosi' => 'äänitiedostosi',
        'aania' => 'ääntä',
        'Aani' => 'Ääni',
        'valilehtien valilla' => 'välilehtien välillä',
        'valilyönnista' => 'välilyönnistä',
        'valilyontielementeilla' => 'välielementeillä',
        'Valilyönti' => 'Välilyönti',
        'valilla' => 'välillä',
        'jarjestetty' => 'järjestetty',
        'jarjestamaton' => 'järjestämätön',
        'riveilla' => 'riveillä',
        'levealla' => 'leveällä',
        'yhta esiintymaa itsenaisesti' => 'yhtä esiintymää itsenäisesti',
        'esiintymat paivittyvat' => 'esiintymät päivittyvät',
        'naytyy' => 'näkyy',
        'niita' => 'niitä',
        'kaytettavia' => 'käytettäviä',
        'uudelleenkaytettava' => 'uudelleenkäytettävä',
        'Uudelleenkaytettavat' => 'Uudelleenkäytettävät',
        'Uudelleenkaytettavien' => 'Uudelleenkäytettävien',
        'paasioihin' => 'pääosioihin',
        'kirjoita ## seettuna valilyönnista' => 'kirjoita ## ja välilyönti',
        'Mene Media-nähdäksesi kaikki ladatut tiedostot.' => 'Siirry Media-valikkoon nähdäksesi kaikki ladatut tiedostot.',
        'Käytä asetteluohkoja' => 'Käytä asettelulohkoja',
    ];

    return strtr($content, $replacements);
}

function manual_instruction_before_start_items(int $post_id): array
{
    $category = manual_instruction_primary_category($post_id);
    $is_finnish = manual_instruction_language_code($post_id) === 'fi';
    $base = $is_finnish
        ? [
            __('Tarvitset muokkausoikeuden WordPressiin.', 'instruction-manual'),
            __('Varmista, että muokkaat oikeaa sivua, artikkelia tai kieliversiota.', 'instruction-manual'),
            __('Pidä lopullinen teksti, kuvat ja linkit valmiina.', 'instruction-manual'),
        ]
        : [
            __('You need edit access to WordPress.', 'instruction-manual'),
            __('You need to know which page, post, or language version you are editing.', 'instruction-manual'),
            __('You should have the final text, images, or links ready.', 'instruction-manual'),
        ];

    if (!$category instanceof WP_Term) {
        return $base;
    }

    $extra = $is_finnish
        ? [
            'advanced' => [
                __('Varmista, ettet muuta jaettua sisältöä, joka näkyy useassa paikassa.', 'instruction-manual'),
                __('Kysy ylläpitäjältä ennen välimuistin, suorituskyvyn, SEO:n tai ACF-asetusten muuttamista.', 'instruction-manual'),
            ],
            'site-config' => [
                __('Tarkista, vaikuttaako muutos koko sivustoon eikä vain yhteen sivuun.', 'instruction-manual'),
            ],
            'block-editor' => [
                __('Valitse oikea lohko ennen kuin muutat sen asetuksia.', 'instruction-manual'),
            ],
        ]
        : [
            'advanced' => [
                __('Confirm you are not changing shared content that appears in several places.', 'instruction-manual'),
                __('Ask a maintainer before changing performance, cache, SEO, or ACF settings.', 'instruction-manual'),
            ],
            'site-config' => [
                __('Check whether the change affects the whole site, not only one page.', 'instruction-manual'),
            ],
            'block-editor' => [
                __('Open the correct block toolbar before changing block settings.', 'instruction-manual'),
            ],
        ];

    return array_merge($base, $extra[$category->slug] ?? []);
}

function manual_instruction_common_mistakes(int $post_id): array
{
    $category = manual_instruction_primary_category($post_id);
    $is_finnish = manual_instruction_language_code($post_id) === 'fi';
    $base = $is_finnish
        ? [
            __('Älä muokkaa väärää kieliversiota.', 'instruction-manual'),
            __('Älä liitä muotoiltua tekstiä suoraan Wordista ilman siistimistä.', 'instruction-manual'),
            __('Älä unohda painaa Päivitä ennen julkisen sivun tarkistamista.', 'instruction-manual'),
        ]
        : [
            __('Do not edit the wrong language version.', 'instruction-manual'),
            __('Do not paste formatted text directly from Word unless formatting has been cleaned.', 'instruction-manual'),
            __('Do not forget to click Update before checking the public page.', 'instruction-manual'),
        ];

    if (!$category instanceof WP_Term) {
        return $base;
    }

    $extra = $is_finnish
        ? [
            'advanced' => [
                __('Älä tyhjennä tai muuta välimuistiasetuksia tarkistamatta julkista sivua sen jälkeen.', 'instruction-manual'),
                __('Älä muokkaa uudelleenkäytettäviä tai jaettuja kenttiä, ellei muutoksen kuulu näkyä kaikkialla.', 'instruction-manual'),
            ],
            'block-editor' => [
                __('Älä muokkaa uudelleenkäytettäviä lohkoja, ellei muutoksen kuulu näkyä useassa paikassa.', 'instruction-manual'),
            ],
            'site-config' => [
                __('Älä muuta valikoita, käyttäjiä tai asetuksia tarkistamatta ketä muutos koskee.', 'instruction-manual'),
            ],
        ]
        : [
            'advanced' => [
                __('Do not clear or change cache settings without checking the public page afterward.', 'instruction-manual'),
                __('Do not edit reusable or shared fields unless the change should appear everywhere.', 'instruction-manual'),
            ],
            'block-editor' => [
                __('Do not edit reusable blocks unless you want the change to appear in several places.', 'instruction-manual'),
            ],
            'site-config' => [
                __('Do not change menus, users, or settings without checking who else is affected.', 'instruction-manual'),
            ],
        ];

    return array_slice(array_merge($base, $extra[$category->slug] ?? []), 0, 5);
}

function manual_instruction_success_checks(int $post_id): array
{
    if (manual_instruction_language_code($post_id) === 'fi') {
        return [
            __('Avaa sivu uuteen välilehteen.', 'instruction-manual'),
            __('Tarkista työpöytänäkymä.', 'instruction-manual'),
            __('Tarkista mobiilinäkymä.', 'instruction-manual'),
            __('Varmista, että sisältö näkyy oikealla kieliversiolla.', 'instruction-manual'),
            __('Pyydä toista henkilöä tarkistamaan lopputulos, jos muutos vaikuttaa julkaistuun sivuun.', 'instruction-manual'),
        ];
    }

    return [
        __('Open the page in a new tab.', 'instruction-manual'),
        __('Check the desktop view.', 'instruction-manual'),
        __('Check the mobile view.', 'instruction-manual'),
        __('Confirm the content appears in the correct language.', 'instruction-manual'),
        __('Ask someone else to confirm the result if this affects a live page.', 'instruction-manual'),
    ];
}

function manual_related_instructions(int $post_id, int $limit = 3): array
{
    $category = manual_instruction_primary_category($post_id);
    $language = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';

    $args = [
        'post_type' => 'wp_instruction',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'post__not_in' => [$post_id],
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    if ($category instanceof WP_Term) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'instruction_category',
                'field' => 'term_id',
                'terms' => [$category->term_id],
            ],
        ];
    }

    if ($language !== '') {
        $args['meta_query'] = [
            [
                'key' => '_gwi_language',
                'value' => $language,
                'compare' => '=',
            ],
        ];
    }

    return get_posts($args);
}

function manual_finnish_search_synonyms(): array
{
    return [
        'kuva' => ['valokuva', 'kuva', 'image', 'picture', 'photo', 'media', 'logo'],
        'valikko' => ['valikko', 'menu', 'navigaatio', 'navigation', 'nav'],
        'lohko' => ['lohko', 'block', 'osio', 'elementti', 'sisältöelementti'],
        'välimuisti' => ['välimuisti', 'cache', 'sivu ei päivity', 'vanha sisältö näkyy'],
        'sivu' => ['sivu', 'page', 'post', 'artikkeli'],
        'painike' => ['painike', 'button', 'link', 'cta', 'painonappi'],
        'muokkaa' => ['muokkaa', 'edit', 'muuta', 'change'],
        'lisää' => ['lisää', 'add', 'uusi'],
        'poista' => ['poista', 'remove', 'delete', 'poistaa'],
        'asetukset' => ['asetukset', 'settings', 'config'],
        'redirect' => ['uudelleenohjaus', 'redirect', 'osoite'],
    ];
}

function manual_expand_finnish_search(string $search): array
{
    $search = strtolower(trim($search));
    $terms = $search !== '' ? [$search] : [];

    foreach (manual_finnish_search_synonyms() as $term => $synonyms) {
        $haystack = array_merge([$term], $synonyms);

        foreach ($haystack as $candidate) {
            if ($candidate !== '' && str_contains($search, strtolower($candidate))) {
                $terms = array_merge($terms, [$term], $synonyms);
                break;
            }
        }
    }

    return array_values(array_unique(array_filter($terms)));
}

function manual_featured_tutorials(int $limit = 8): array
{
    $slugs = [
        'media-library', 'mediakirjasto',
        'menus', 'valikko',
        'block-editor-basics', 'lohkoeditorin-perusteet',
        'cache',
        'seo',
        'acf-blocks', 'acf-lohkot',
    ];

    $posts = get_posts([
        'post_type' => 'wp_instruction',
        'post_status' => 'publish',
        'posts_per_page' => $limit,
        'meta_key' => '_gwi_language',
        'meta_value' => 'fi',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    if (count($posts) >= $limit) {
        return $posts;
    }

    $featured = [];
    foreach ($slugs as $slug) {
        $found = get_posts([
            'post_type' => 'wp_instruction',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'name' => $slug,
        ]);
        if (!empty($found)) {
            $featured[] = $found[0];
        }
        if (count($featured) >= $limit) {
            break;
        }
    }

    if (count($featured) < $limit) {
        $rest = get_posts([
            'post_type' => 'wp_instruction',
            'post_status' => 'publish',
            'posts_per_page' => $limit - count($featured),
            'meta_key' => '_gwi_language',
            'meta_value' => 'fi',
            'post__not_in' => wp_list_pluck($featured, 'ID'),
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $featured = array_merge($featured, $rest);
    }

    return $featured;
}

function manual_learning_paths(): array
{
    $terms = get_terms([
        'taxonomy' => 'instruction_category',
        'slug' => ['fundamentals', 'block-editor', 'advanced'],
        'hide_empty' => false,
    ]);

    $term_map = [];
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_map[$term->slug] = get_term_link($term);
        }
    }

    $archive_url = manual_instruction_archive_url();
    $fallback = home_url('/ohjeet/');

    $fundamentals_url = $term_map['fundamentals'] ?? $fallback;
    $block_editor_url = $term_map['block-editor'] ?? $fallback;
    $advanced_url = $term_map['advanced'] ?? $fallback;

    return [
        [
            'title' => __('Uuden editorin polku', 'instruction-manual'),
            'steps' => [
                ['title' => __('Kirjaudu WordPressiin', 'instruction-manual'), 'url' => ''],
                ['title' => __('Muokkaa sivua', 'instruction-manual'), 'url' => ''],
                ['title' => __('Vaihda kuva', 'instruction-manual'), 'url' => ''],
                ['title' => __('Lisää painike', 'instruction-manual'), 'url' => ''],
                ['title' => __('Esikatsele ja julkaise', 'instruction-manual'), 'url' => ''],
            ],
            'url' => manual_instruction_url_with_language($fundamentals_url),
            'count' => 5,
        ],
        [
            'title' => __('Sisältölohkojen polku', 'instruction-manual'),
            'steps' => [
                ['title' => __('Mikä on lohko?', 'instruction-manual'), 'url' => ''],
                ['title' => __('Lisää ACF-lohko', 'instruction-manual'), 'url' => ''],
                ['title' => __('Muokkaa lohkon kenttiä', 'instruction-manual'), 'url' => ''],
                ['title' => __('Järjestä osiot', 'instruction-manual'), 'url' => ''],
                ['title' => __('Tarkista mobiilinäkymä', 'instruction-manual'), 'url' => ''],
            ],
            'url' => manual_instruction_url_with_language($block_editor_url),
            'count' => 5,
        ],
        [
            'title' => __('Ongelmanratkaisun polku', 'instruction-manual'),
            'steps' => [
                ['title' => __('Tyhjennä välimuisti', 'instruction-manual'), 'url' => ''],
                ['title' => __('Tarkista esikatselu', 'instruction-manual'), 'url' => ''],
                ['title' => __('Korjaa päivittymätön sivu', 'instruction-manual'), 'url' => ''],
                ['title' => __('Palauta vanha sisältö', 'instruction-manual'), 'url' => ''],
            ],
            'url' => manual_instruction_url_with_language($advanced_url),
            'count' => 4,
        ],
    ];
}

function manual_intent_categories(): array
{
    $terms = get_terms([
        'taxonomy' => 'instruction_category',
        'slug' => ['fundamentals', 'block-editor', 'advanced', 'site-config'],
        'hide_empty' => false,
    ]);

    $term_map = [];
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_map[$term->slug] = get_term_link($term);
        }
    }

    $fallback = manual_instruction_archive_url();

    return [
        [
            'title' => __('Haluan muokata sisältöä', 'instruction-manual'),
            'description' => __('Tekstit, kuvat, linkit ja painikkeet.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['fundamentals'] ?? $fallback),
            'icon' => 'edit',
        ],
        [
            'title' => __('Haluan rakentaa sivua', 'instruction-manual'),
            'description' => __('Lohkot, ACF-osuudet ja sivupohjat.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['block-editor'] ?? $fallback),
            'icon' => 'build',
        ],
        [
            'title' => __('Haluan korjata ongelman', 'instruction-manual'),
            'description' => __('Välimuisti, puuttuvat muutokset ja näkymävirheet.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['advanced'] ?? $fallback),
            'icon' => 'fix',
        ],
        [
            'title' => __('Haluan muuttaa asetuksia', 'instruction-manual'),
            'description' => __('Valikot, SEO, uudelleenohjaukset ja sivuston asetukset.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['site-config'] ?? $fallback),
            'icon' => 'settings',
        ],
    ];
}

function manual_tutorial_difficulty_label_fi(int $post_id): string
{
    $labels = [
        'basic' => __('Aloittelija', 'instruction-manual'),
        'intermediate' => __('Keskitaso', 'instruction-manual'),
        'advanced' => __('Edistynyt', 'instruction-manual'),
    ];

    return $labels[manual_instruction_difficulty($post_id)] ?? $labels['basic'];
}

function manual_review_status_label_fi(int $post_id): string
{
    $status = sanitize_key((string) get_post_meta($post_id, '_gwi_review_status', true));
    $labels = [
        'draft' => __('Luonnos', 'instruction-manual'),
        'needs-review' => __('Odottaa tarkistusta', 'instruction-manual'),
        'tested' => __('Testattu', 'instruction-manual'),
        'outdated' => __('Vanhentunut', 'instruction-manual'),
        'deprecated' => __('Poistuva ohje', 'instruction-manual'),
    ];

    return $labels[$status] ?? $labels['needs-review'];
}

function manual_review_label_fi(int $post_id): string
{
    $raw_date = get_post_meta($post_id, '_gwi_last_reviewed', true);
    $last_reviewed = function_exists('gwi_sanitize_review_date')
        ? gwi_sanitize_review_date($raw_date)
        : (is_string($raw_date) ? preg_replace('/[^0-9-]/', '', $raw_date) : '');

    if ($last_reviewed !== '') {
        return sprintf(__('Tarkistettu %s', 'instruction-manual'), $last_reviewed);
    }

    return __('Tarkistus puuttuu', 'instruction-manual');
}

function manual_finnish_suggested_searches(): array
{
    return [
        __('vaihda kuva', 'instruction-manual'),
        __('lisää painike', 'instruction-manual'),
        __('luo sivu', 'instruction-manual'),
        __('vaihda teema', 'instruction-manual'),
    ];
}

function manual_language_label_fi(int $post_id): string
{
    $lang = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';

    if ($lang === 'fi') {
        return __('Suomi', 'instruction-manual');
    }
    if ($lang === 'en') {
        return __('Englanti', 'instruction-manual');
    }

    return __('Suomi', 'instruction-manual');
}

function manual_language_filter_label_fi(string $language_code): string
{
    $labels = [
        'fi' => __('Suomi', 'instruction-manual'),
        'en' => __('Englanti', 'instruction-manual'),
    ];

    return $labels[$language_code] ?? $language_code;
}

function manual_reviewed_month_label_fi(int $post_id): string
{
    $raw_date = get_post_meta($post_id, '_gwi_last_reviewed', true);
    if ($raw_date === '') {
        return '';
    }
    $timestamp = strtotime($raw_date);
    if ($timestamp === false) {
        return '';
    }
    return gmdate('m/Y', $timestamp);
}

function manual_get_translation_pair(int $post_id): int
{
    $translation_id = absint(get_post_meta($post_id, '_gwi_translation_id', true));
    if ($translation_id && get_post_type($translation_id) === 'wp_instruction') {
        return $translation_id;
    }

    $reverse = get_posts([
        'post_type' => 'wp_instruction',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_gwi_translation_id',
        'meta_value' => $post_id,
        'exclude' => [$post_id],
    ]);

    return !empty($reverse) ? absint($reverse[0]) : 0;
}

function manual_has_english_version(int $post_id): bool
{
    $lang = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';
    if ($lang === 'en') {
        return true;
    }

    $pair_id = manual_get_translation_pair($post_id);
    if ($pair_id) {
        $pair_lang = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($pair_id) : '';
        return $pair_lang === 'en';
    }

    return false;
}

function manual_get_english_version(int $post_id): ?WP_Post
{
    $lang = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($post_id) : '';
    if ($lang === 'en') {
        return get_post($post_id);
    }

    $pair_id = manual_get_translation_pair($post_id);
    if ($pair_id) {
        $pair_lang = function_exists('gwi_get_instruction_language') ? gwi_get_instruction_language($pair_id) : '';
        if ($pair_lang === 'en') {
            return get_post($pair_id);
        }
    }

    return null;
}

function manual_intent_categories_with_popular(array $fi_tutorials): array
{
    $terms = get_terms([
        'taxonomy' => 'instruction_category',
        'slug' => ['fundamentals', 'block-editor', 'advanced', 'site-config'],
        'hide_empty' => false,
    ]);

    $term_map = [];
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_map[$term->slug] = get_term_link($term);
        }
    }

    $fallback = manual_instruction_archive_url();

    $fi_by_category = [];
    foreach ($fi_tutorials as $t) {
        $cats = get_the_terms($t->ID, 'instruction_category');
        if (!is_wp_error($cats) && !empty($cats)) {
            foreach ($cats as $cat) {
                if (!isset($fi_by_category[$cat->slug])) {
                    $fi_by_category[$cat->slug] = [];
                }
                $fi_by_category[$cat->slug][] = $t;
            }
        }
    }

    return [
        [
            'title' => __('Muokkaa sisältöä', 'instruction-manual'),
            'description' => __('Tekstit, kuvat, linkit ja painikkeet.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['fundamentals'] ?? $fallback),
            'icon' => 'content',
            'tone' => 'green',
            'popular' => array_slice($fi_by_category['fundamentals'] ?? [], 0, 3),
        ],
        [
            'title' => __('Rakenna sivua', 'instruction-manual'),
            'description' => __('Lohkot, osiot ja sivupohjat.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['block-editor'] ?? $fallback),
            'icon' => 'page',
            'tone' => 'blue',
            'popular' => array_slice($fi_by_category['block-editor'] ?? [], 0, 3),
        ],
        [
            'title' => __('Korjaa ongelma', 'instruction-manual'),
            'description' => __('Välimuisti, päivitykset ja näkymävirheet.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['advanced'] ?? $fallback),
            'icon' => 'fix',
            'tone' => 'orange',
            'popular' => array_slice($fi_by_category['advanced'] ?? [], 0, 3),
        ],
        [
            'title' => __('Hallitse asetuksia', 'instruction-manual'),
            'description' => __('Valikot, käyttäjät, SEO ja sivuston asetukset.', 'instruction-manual'),
            'url' => manual_instruction_url_with_language($term_map['site-config'] ?? $fallback),
            'icon' => 'settings',
            'tone' => 'olive',
            'popular' => array_slice($fi_by_category['site-config'] ?? [], 0, 3),
        ],
    ];
}

function manual_featured_finnish_tutorials(array $fi_tutorials, int $limit = 6): array
{
    if (count($fi_tutorials) <= $limit) {
        return $fi_tutorials;
    }

    $priority_slugs = [
        'mediakirjasto', 'media-library',
        'valikko', 'menus',
        'lohkoeditorin-perusteet', 'block-editor-basics',
        'cache',
        'seo',
    ];

    $featured = [];
    $used_ids = [];
    foreach ($priority_slugs as $slug) {
        foreach ($fi_tutorials as $t) {
            if (get_post_field('post_name', $t->ID) === $slug && !in_array($t->ID, $used_ids, true)) {
                $featured[] = $t;
                $used_ids[] = $t->ID;
                break;
            }
        }
        if (count($featured) >= $limit) {
            break;
        }
    }

    foreach ($fi_tutorials as $t) {
        if (count($featured) >= $limit) {
            break;
        }
        if (!in_array($t->ID, $used_ids, true)) {
            $featured[] = $t;
            $used_ids[] = $t->ID;
        }
    }

    return $featured;
}

function manual_learning_paths_with_posts(array $fi_tutorials): array
{
    $terms = get_terms([
        'taxonomy' => 'instruction_category',
        'slug' => ['fundamentals', 'block-editor', 'advanced'],
        'hide_empty' => false,
    ]);

    $term_map = [];
    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $term_map[$term->slug] = get_term_link($term);
        }
    }

    $fallback = manual_instruction_archive_url();

    $fi_by_slug = [];
    foreach ($fi_tutorials as $t) {
        $fi_by_slug[(string) get_post_field('post_name', $t->ID)] = $t;
    }

    $build_path_steps = function (array $definitions) use ($fi_by_slug): array {
        $steps = [];

        foreach ($definitions as $definition) {
            $url = '';
            foreach ($definition['slugs'] as $slug) {
                if (isset($fi_by_slug[$slug])) {
                    $url = get_permalink($fi_by_slug[$slug]->ID);
                    break;
                }
            }

            $steps[] = [
                'title' => $definition['title'],
                'url' => $url,
            ];
        }

        return $steps;
    };

    $new_user_steps = $build_path_steps([
        ['title' => __('Kirjaudu WordPressiin', 'instruction-manual'), 'slugs' => ['hallintapaneelin-yleiskatsaus-fi']],
        ['title' => __('Muokkaa sivua', 'instruction-manual'), 'slugs' => ['sivujen-luominen-ja-muokkaus-fi']],
        ['title' => __('Vaihda kuva', 'instruction-manual'), 'slugs' => ['mediakirjasto-fi']],
        ['title' => __('Lisää linkki', 'instruction-manual'), 'slugs' => ['yleiset-sisaltolohkot-fi']],
        ['title' => __('Julkaise muutokset', 'instruction-manual'), 'slugs' => ['sivujen-luominen-ja-muokkaus-fi']],
        ['title' => __('Seuraavat askeleet', 'instruction-manual'), 'slugs' => ['lohkoeditorin-perusteet-fi']],
    ]);

    $content_editor_steps = $build_path_steps([
        ['title' => __('Muokkaa sivun tekstiä', 'instruction-manual'), 'slugs' => ['sivujen-luominen-ja-muokkaus-fi']],
        ['title' => __('Vaihda kuva', 'instruction-manual'), 'slugs' => ['mediakirjasto-fi']],
        ['title' => __('Lisää painike', 'instruction-manual'), 'slugs' => ['yleiset-sisaltolohkot-fi', 'asettelulohkot-fi']],
        ['title' => __('Käytä sisältölohkoa', 'instruction-manual'), 'slugs' => ['yleiset-sisaltolohkot-fi']],
        ['title' => __('Tarkista mobiilinäkymä', 'instruction-manual'), 'slugs' => ['asettelulohkot-fi']],
        ['title' => __('Päivitä sivu turvallisesti', 'instruction-manual'), 'slugs' => ['sivujen-luominen-ja-muokkaus-fi']],
    ]);

    $troubleshooting_steps = $build_path_steps([
        ['title' => __('Tyhjennä välimuisti', 'instruction-manual'), 'slugs' => ['suorituskyky-ja-valimuisti-fi']],
        ['title' => __('Tarkista esikatselu', 'instruction-manual'), 'slugs' => ['sivujen-luominen-ja-muokkaus-fi']],
        ['title' => __('Korjaa päivittymätön sivu', 'instruction-manual'), 'slugs' => ['suorituskyky-ja-valimuisti-fi']],
        ['title' => __('Tarkista mobiilinäkymä', 'instruction-manual'), 'slugs' => ['asettelulohkot-fi']],
    ]);

    return [
        [
            'title' => __('Uusi käyttäjä', 'instruction-manual'),
            'steps' => $new_user_steps,
            'url' => manual_instruction_url_with_language($term_map['fundamentals'] ?? $fallback),
            'count' => count($new_user_steps),
        ],
        [
            'title' => __('Sisällön päivittäjä', 'instruction-manual'),
            'steps' => $content_editor_steps,
            'url' => manual_instruction_url_with_language($term_map['block-editor'] ?? $fallback),
            'count' => count($content_editor_steps),
        ],
        [
            'title' => __('Ongelmanratkaisu', 'instruction-manual'),
            'steps' => $troubleshooting_steps,
            'url' => manual_instruction_url_with_language($term_map['advanced'] ?? $fallback),
            'count' => count($troubleshooting_steps),
        ],
    ];
}

function manual_glossary_preview(int $limit = 4): array
{
    $all = manual_glossary_terms();
    $preview = array_slice($all, 0, $limit, true);
    return $preview;
}
