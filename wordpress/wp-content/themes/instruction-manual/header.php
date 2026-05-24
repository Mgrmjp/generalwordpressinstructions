<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$manual_is_instruction_library = is_post_type_archive('wp_instruction')
    || is_singular('wp_instruction')
    || is_tax('instruction_category')
    || (is_search() && get_query_var('post_type') === 'wp_instruction');
$manual_is_glossary = manual_is_glossary_view();
$manual_header_language = manual_instruction_sanitize_language(get_query_var('instruction_language'));

if (is_singular('wp_instruction')) {
    $manual_header_language = manual_instruction_language_code((int) get_queried_object_id());
}

$manual_header_is_english = $manual_header_language === 'en';
$manual_header_text = $manual_header_is_english
    ? [
        'brand' => __('WordPress guides', 'instruction-manual'),
        'tagline' => __('Clear guides, reliable results.', 'instruction-manual'),
        'start' => __('Start', 'instruction-manual'),
        'guides' => __('Guides', 'instruction-manual'),
        'paths' => __('Paths', 'instruction-manual'),
        'glossary' => __('Glossary', 'instruction-manual'),
        'admin' => __('Admin', 'instruction-manual'),
        'nav' => __('Main navigation', 'instruction-manual'),
        'search_label' => __('Search guides', 'instruction-manual'),
        'search_placeholder' => __('Search guides...', 'instruction-manual'),
        'search_button' => __('Search', 'instruction-manual'),
        'switch_label' => __('Switch to Finnish', 'instruction-manual'),
        'language' => __('EN', 'instruction-manual'),
        'switch_to' => 'fi',
        'skip' => __('Skip to content', 'instruction-manual'),
        'theme_dark' => __('Dark mode', 'instruction-manual'),
        'theme_light' => __('Light mode', 'instruction-manual'),
    ]
    : [
        'brand' => __('WordPress-ohjeet', 'instruction-manual'),
        'tagline' => __('Selkeät ohjeet, varmat tulokset.', 'instruction-manual'),
        'start' => __('Aloita', 'instruction-manual'),
        'guides' => __('Ohjeet', 'instruction-manual'),
        'paths' => __('Polut', 'instruction-manual'),
        'glossary' => __('Sanasto', 'instruction-manual'),
        'admin' => __('Ylläpito', 'instruction-manual'),
        'nav' => __('Päänavigaatio', 'instruction-manual'),
        'search_label' => __('Hae ohjeita', 'instruction-manual'),
        'search_placeholder' => __('Etsi ohjetta...', 'instruction-manual'),
        'search_button' => __('Hae', 'instruction-manual'),
        'switch_label' => __('Vaihda englanniksi', 'instruction-manual'),
        'language' => __('FI', 'instruction-manual'),
        'switch_to' => 'en',
        'skip' => __('Siirry sisältöön', 'instruction-manual'),
        'theme_dark' => __('Tumma tila', 'instruction-manual'),
        'theme_light' => __('Vaalea tila', 'instruction-manual'),
    ];
$manual_language_switch_url = manual_instruction_language_filter_url($manual_header_text['switch_to']);

if (is_singular('wp_instruction')) {
    $manual_translation_id = (int) get_post_meta((int) get_queried_object_id(), '_gwi_translation_id', true);

    if ($manual_translation_id > 0 && get_post_status($manual_translation_id) === 'publish') {
        $manual_language_switch_url = get_permalink($manual_translation_id);
    }
}
?>
<a class="manual-skip-link" href="#main"><?php echo esc_html($manual_header_text['skip']); ?></a>
<header class="manual-site-header">
    <div class="manual-site-header__inner">
        <a class="manual-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <span class="manual-brand__title"><?php echo esc_html($manual_header_text['brand']); ?></span>
            <span class="manual-brand__tagline"><?php echo esc_html($manual_header_text['tagline']); ?></span>
        </a>
        <div class="manual-site-header__toolbar">
            <nav class="manual-nav" aria-label="<?php echo esc_attr($manual_header_text['nav']); ?>">
                <div class="manual-nav__links">
                    <a href="<?php echo esc_url(home_url('/')); ?>"<?php echo is_front_page() ? ' class="is-active"' : ''; ?>><?php echo esc_html($manual_header_text['start']); ?></a>
                    <a href="<?php echo esc_url(manual_instruction_archive_url()); ?>"<?php echo $manual_is_instruction_library ? ' class="is-active"' : ''; ?>><?php echo esc_html($manual_header_text['guides']); ?></a>
                    <a href="<?php echo esc_url(home_url('/#polut')); ?>"><?php echo esc_html($manual_header_text['paths']); ?></a>
                    <a href="<?php echo esc_url(manual_glossary_url()); ?>"<?php echo $manual_is_glossary ? ' class="is-active"' : ''; ?>><?php echo esc_html($manual_header_text['glossary']); ?></a>
                    <?php if (current_user_can('edit_posts')) : ?>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=wp_instruction')); ?>"><?php echo esc_html($manual_header_text['admin']); ?></a>
                    <?php endif; ?>
                </div>
                <div class="manual-nav__tools">
                    <a href="<?php echo esc_url($manual_language_switch_url); ?>" class="manual-lang-switch" aria-label="<?php echo esc_attr($manual_header_text['switch_label']); ?>"><?php echo esc_html($manual_header_text['language']); ?></a>
                    <button
                        type="button"
                        class="manual-theme-toggle"
                        data-manual-theme-toggle
                        data-label-dark="<?php echo esc_attr($manual_header_text['theme_light']); ?>"
                        data-label-light="<?php echo esc_attr($manual_header_text['theme_dark']); ?>"
                        aria-pressed="false"
                    ><?php echo esc_html($manual_header_text['theme_dark']); ?></button>
                </div>
            </nav>
            <form class="manual-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="manual-search-input"><?php echo esc_html($manual_header_text['search_label']); ?></label>
                <span class="manual-search-field">
                    <input type="search" id="manual-search-input" name="s" placeholder="<?php echo esc_attr($manual_header_text['search_placeholder']); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
                </span>
                <input type="hidden" name="post_type" value="wp_instruction">
                <button type="submit"><?php echo esc_html($manual_header_text['search_button']); ?></button>
            </form>
        </div>
    </div>
</header>
<main id="main" class="manual-main">
