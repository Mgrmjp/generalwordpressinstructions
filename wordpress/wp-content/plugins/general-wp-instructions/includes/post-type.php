<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'gwi_register_instruction_post_type');
add_action('init', 'gwi_register_instruction_taxonomy');
add_action('add_meta_boxes', 'gwi_add_instruction_meta_boxes');
add_action('save_post_wp_instruction', 'gwi_save_instruction_meta');
add_action('admin_menu', 'gwi_register_instruction_template_page');
add_filter('manage_wp_instruction_posts_columns', 'gwi_instruction_columns');
add_action('manage_wp_instruction_posts_custom_column', 'gwi_instruction_column_content', 10, 2);
add_filter('default_content', 'gwi_default_instruction_content', 10, 2);
add_filter('wp_insert_post_data', 'gwi_guard_instruction_publish', 10, 2);
add_action('admin_notices', 'gwi_render_clarity_guard_notice');

function gwi_languages(): array
{
    return [
        'en' => __('English', 'general-wp-instructions'),
        'fi' => __('Finnish', 'general-wp-instructions'),
    ];
}

function gwi_admin_labels(string $language): array
{
    $labels = [
        'en' => [
            'template' => __('Template', 'general-wp-instructions'),
            'difficulty' => __('Difficulty', 'general-wp-instructions'),
            'estimated_minutes' => __('Estimated time in minutes', 'general-wp-instructions'),
            'review_status' => __('Review status', 'general-wp-instructions'),
            'last_reviewed' => __('Last reviewed', 'general-wp-instructions'),
            'owner' => __('Owner', 'general-wp-instructions'),
            'purpose' => __('Plain-language purpose', 'general-wp-instructions'),
            'clarity_score' => __('Clarity score', 'general-wp-instructions'),
            'publishing_requires' => __('Publishing requires 75 or higher.', 'general-wp-instructions'),
            'clarity_mode' => __('Clarity mode: if a sentence needs a comma, check whether it should be two sentences.', 'general-wp-instructions'),
            'checklist' => __('Required writing checklist', 'general-wp-instructions'),
            'templates_info' => __('New documents can be started from preloaded templates through Instructions > Templates.', 'general-wp-instructions'),
            'owner_placeholder' => __('Who maintains this document?', 'general-wp-instructions'),
            'purpose_placeholder' => __('Use this when you want to...', 'general-wp-instructions'),
            'language' => __('Language', 'general-wp-instructions'),
            'translation' => __('Linked translation', 'general-wp-instructions'),
            'no_translation' => __('No linked translation', 'general-wp-instructions'),
            'translation_description' => __('Use this lightweight pairing for the Finnish/English switcher. It can be replaced by Polylang/WPML later if needed.', 'general-wp-instructions'),
        ],
        'fi' => [
            'template' => __('Malli', 'general-wp-instructions'),
            'difficulty' => __('Vaikeustaso', 'general-wp-instructions'),
            'estimated_minutes' => __('Arvioitu aika minuuteissa', 'general-wp-instructions'),
            'review_status' => __('Tarkistuksen tila', 'general-wp-instructions'),
            'last_reviewed' => __('Viimeksi tarkistettu', 'general-wp-instructions'),
            'owner' => __('Omistaja', 'general-wp-instructions'),
            'purpose' => __('Selkeä tarkoitus', 'general-wp-instructions'),
            'clarity_score' => __('Selvyyspisteet', 'general-wp-instructions'),
            'publishing_requires' => __('Julkaiseminen vaatii 75 pistettä tai enemmän.', 'general-wp-instructions'),
            'clarity_mode' => __('Selvyystila: jos lauseessa tarvitsee pilkkoa, tarkista pitäisikö siitä olla kaksi lausetta.', 'general-wp-instructions'),
            'checklist' => __('Pakollinen kirjoitustarkistuslista', 'general-wp-instructions'),
            'templates_info' => __('Uusia dokumentteja voi aloittaa esiladatuista malleista kohdasta Ohjeet > Mallit.', 'general-wp-instructions'),
            'owner_placeholder' => __('Kuka ylläpitää tätä dokumenttia?', 'general-wp-instructions'),
            'purpose_placeholder' => __('Käytä tätä kun haluat...', 'general-wp-instructions'),
            'language' => __('Kieli', 'general-wp-instructions'),
            'translation' => __('Linkitetty käännös', 'general-wp-instructions'),
            'no_translation' => __('Ei linkitettyä käännöstä', 'general-wp-instructions'),
            'translation_description' => __('Käytä tätä kevyttä paritusta suomi/englanti-vaihtimeen. Sen voi korvata Polylang/WMPL:llä myöhemmin tarvittaessa.', 'general-wp-instructions'),
        ],
    ];

    return $labels[$language] ?? $labels['en'];
}

function gwi_sanitize_language($language): string
{
    $language = is_string($language) ? strtolower($language) : 'en';

    return array_key_exists($language, gwi_languages()) ? $language : 'en';
}

function gwi_register_instruction_post_type(): void
{
    $labels = [
        'name' => __('Instructions', 'general-wp-instructions'),
        'singular_name' => __('Instruction', 'general-wp-instructions'),
        'add_new_item' => __('Add New Instruction', 'general-wp-instructions'),
        'edit_item' => __('Edit Instruction', 'general-wp-instructions'),
        'new_item' => __('New Instruction', 'general-wp-instructions'),
        'view_item' => __('View Instruction', 'general-wp-instructions'),
        'search_items' => __('Search Instructions', 'general-wp-instructions'),
    ];

    register_post_type('wp_instruction', [
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'has_archive' => 'ohjeet',
        'rewrite' => ['slug' => 'ohjeet'],
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author'],
    ]);

    register_post_meta('wp_instruction', '_gwi_language', [
        'type' => 'string',
        'single' => true,
        'default' => 'en',
        'show_in_rest' => true,
        'sanitize_callback' => 'gwi_sanitize_language',
        'auth_callback' => 'gwi_can_edit_post_meta',
    ]);

    register_post_meta('wp_instruction', '_gwi_translation_id', [
        'type' => 'integer',
        'single' => true,
        'default' => 0,
        'show_in_rest' => true,
        'sanitize_callback' => 'absint',
        'auth_callback' => 'gwi_can_edit_post_meta',
    ]);

    foreach (gwi_instruction_meta_schema() as $meta_key => $schema) {
        register_post_meta('wp_instruction', $meta_key, [
            'type' => $schema['type'],
            'single' => true,
            'default' => $schema['default'],
            'show_in_rest' => true,
            'sanitize_callback' => $schema['sanitize_callback'],
            'auth_callback' => 'gwi_can_edit_post_meta',
        ]);
    }
}

function gwi_instruction_meta_schema(): array
{
    return [
        '_gwi_template' => [
            'type' => 'string',
            'default' => 'basic-task',
            'sanitize_callback' => 'gwi_sanitize_instruction_template',
        ],
        '_gwi_difficulty' => [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'gwi_sanitize_instruction_difficulty',
        ],
        '_gwi_estimated_minutes' => [
            'type' => 'integer',
            'default' => 0,
            'sanitize_callback' => 'gwi_sanitize_estimated_minutes',
        ],
        '_gwi_review_status' => [
            'type' => 'string',
            'default' => 'needs-review',
            'sanitize_callback' => 'gwi_sanitize_review_status',
        ],
        '_gwi_last_reviewed' => [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'gwi_sanitize_review_date',
        ],
        '_gwi_owner' => [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        '_gwi_purpose' => [
            'type' => 'string',
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ],
        '_gwi_clarity_score' => [
            'type' => 'integer',
            'default' => 0,
            'sanitize_callback' => 'absint',
        ],
    ];
}

function gwi_instruction_templates(string $language = 'en'): array
{
    $templates = [
        'en' => [
            'basic-task' => [
                'label' => __('Basic task guide', 'general-wp-instructions'),
                'description' => __('For one common editing task with clear steps.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('basic-task', 'en'),
            ],
            'troubleshooting' => [
                'label' => __('Troubleshooting guide', 'general-wp-instructions'),
                'description' => __('For a symptom, likely causes, and checks.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('troubleshooting', 'en'),
            ],
            'concept' => [
                'label' => __('Concept explanation', 'general-wp-instructions'),
                'description' => __('For explaining a WordPress idea before tasks.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('concept', 'en'),
            ],
            'checklist' => [
                'label' => __('Checklist', 'general-wp-instructions'),
                'description' => __('For repeatable review or launch checks.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('checklist', 'en'),
            ],
            'client-facing' => [
                'label' => __('Client-facing guide', 'general-wp-instructions'),
                'description' => __('For non-technical editors and site owners.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('client-facing', 'en'),
            ],
            'internal-technical' => [
                'label' => __('Internal technical guide', 'general-wp-instructions'),
                'description' => __('For maintainers who need implementation detail.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('internal-technical', 'en'),
            ],
        ],
        'fi' => [
            'basic-task' => [
                'label' => __('Perustehtäväopas', 'general-wp-instructions'),
                'description' => __('Yhtä yleistä muokkaustehtävää varten selkeillä vaiheilla.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('basic-task', 'fi'),
            ],
            'troubleshooting' => [
                'label' => __('Vianmääritysopas', 'general-wp-instructions'),
                'description' => __('Oireen, todennäköisten syiden ja tarkistusten varten.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('troubleshooting', 'fi'),
            ],
            'concept' => [
                'label' => __('Käsitteen selitys', 'general-wp-instructions'),
                'description' => __('WordPress-idean selittäminen ennen tehtäviä.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('concept', 'fi'),
            ],
            'checklist' => [
                'label' => __('Tarkistuslista', 'general-wp-instructions'),
                'description' => __('Toistettavien tarkistusten tai julkaisutarkistusten varten.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('checklist', 'fi'),
            ],
            'client-facing' => [
                'label' => __('Asiakasopas', 'general-wp-instructions'),
                'description' => __('Ei-teknisten muokkaajien ja sivustonomistajien varten.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('client-facing', 'fi'),
            ],
            'internal-technical' => [
                'label' => __('Sisäinen tekninen opas', 'general-wp-instructions'),
                'description' => __('Ylläpitäjille, jotka tarvitsevat teknisen kontekstin.', 'general-wp-instructions'),
                'content' => gwi_instruction_template_content('internal-technical', 'fi'),
            ],
        ],
    ];

    return $templates[$language] ?? $templates['en'];
}

function gwi_instruction_template_content(string $template, string $language = 'en'): string
{
    $templates = [
        'en' => [
            'basic-task' => '<!-- wp:heading --><h2>What this is for</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when you need to complete one clear WordPress editing task.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Before you start</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>You need edit access to WordPress.</li><li>You need to know which page or post you are editing.</li><li>You should have the text or images ready.</li></ul><!-- /wp:list -->'
                . '<!-- wp:general-wp-instructions/step-list {"title":"Steps","steps":[{"text":"Open the page or post."},{"text":"Make one clear change."},{"text":"Click Update."},{"text":"Open the page in a new tab."}]} /-->'
                . '<!-- wp:heading --><h2>Example</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Example: add the prepared text to the correct content field, then click Update.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Do not paste formatted text directly from Word without cleaning it.</li><li>Do not edit shared content unless you want it to change everywhere.</li><li>Do not forget to check the mobile view.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Open the page in a new tab.</li><li>Check the desktop view.</li><li>Check the mobile view.</li><li>Confirm the content appears in the correct language.</li></ul><!-- /wp:list -->',
            'troubleshooting' => '<!-- wp:heading --><h2>What problem this solves</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when something in WordPress does not look or behave as expected.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Before you start</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Write down what you changed.</li><li>Open the page in a new tab.</li><li>Check whether the problem happens on desktop, mobile, or both.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Checks</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Refresh the page.</li><li>Clear the cache if the old version still appears.</li><li>Check that you edited the correct language version.</li><li>Ask a reviewer to confirm the result.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Do not assume Update was clicked.</li><li>Do not check only the logged-in view.</li><li>Do not fix the same content in two places without checking which one is live.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>The page shows the expected content for a logged-out visitor.</p><!-- /wp:paragraph -->',
            'concept' => '<!-- wp:heading --><h2>What this means</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Explain the term in plain language before giving steps.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Use this when</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when a user needs to understand the idea before editing safely.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Simple example</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Give one concrete example from this WordPress site.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Do not use the technical term before defining it.</li><li>Do not explain edge cases before the basic idea.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>A new editor can explain the idea back in one sentence.</p><!-- /wp:paragraph -->',
            'checklist' => '<!-- wp:heading --><h2>What this checklist is for</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when you need to confirm a repeatable WordPress task is complete.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Before you start</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Open the page you are checking.</li><li>Know which language version you are reviewing.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Checklist</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>The content is correct.</li><li>The links work.</li><li>The images have alt text.</li><li>The mobile view works.</li><li>The page has been reviewed by another person.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Do not mark the checklist complete until the public page has been checked.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Every checklist item has been confirmed on the public page.</p><!-- /wp:paragraph -->',
            'client-facing' => '<!-- wp:heading --><h2>What this is for</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when a non-technical editor needs to complete the task without developer help.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Before you start</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>You need WordPress edit access.</li><li>You should have the final content ready.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Steps</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Open the correct page.</li><li>Make one change at a time.</li><li>Click Update.</li><li>Check the public page.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Do not edit another language by accident.</li><li>Do not leave placeholder text visible.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>The public page shows the change in the correct place and language.</p><!-- /wp:paragraph -->',
            'internal-technical' => '<!-- wp:heading --><h2>What this is for</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Use this when a maintainer needs technical context to support the WordPress site.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Before you start</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>You need the correct WordPress role.</li><li>You need access to the relevant plugin, theme, or integration.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Steps</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Confirm the current behavior.</li><li>Make the smallest safe change.</li><li>Clear caches if needed.</li><li>Test the public page.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Common mistakes</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Do not change production settings without recording the reason.</li><li>Do not skip mobile checks.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>How to check it worked</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>The behavior is confirmed in WordPress admin and on the public page.</p><!-- /wp:paragraph -->',
        ],
        'fi' => [
            'basic-task' => '<!-- wp:heading --><h2>Mihin tämä on tarkoitettu</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä, kun sinun täytyy suorittaa yksi selkeä WordPress-muokkaustehtävä.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Ennen kuin aloitat</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Tarvitset WordPress-muokkausoikeudet.</li><li>Sinun täytyy tietää mitä sivua tai artikkelia muokkaat.</li><li>Sinulla pitäisi olla valmis teksti tai kuvat valmiina.</li></ul><!-- /wp:list -->'
                . '<!-- wp:general-wp-instructions/step-list {"title":"Vaiheet","steps":[{"text":"Avaa sivu tai artikkeli."},{"text":"Tee yksi selkeä muutos."},{"text":"Klikkaa Päivitä."},{"text":"Avaa sivu uudessa välilehdessä."}]} /-->'
                . '<!-- wp:heading --><h2>Esimerkki</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Esimerkki: lisää valmisteltu teksti oikeaan sisältökenttään ja klikkaa sitten Päivitä.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Älä liitä muotoiltua tekstiä suoraan Wordista sitä siivottamatta.</li><li>Älä muokkaa jaettua sisältöä ellet halua sen muuttuvan kaikkialla.</li><li>Muista tarkistaa mobiilinäkymä.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Avaa sivu uudessa välilehdessä.</li><li>Tarkista työpöytänäkymä.</li><li>Tarkista mobiilinäkymä.</li><li>Vahvista että sisältö näkyy oikealla kielellä.</li></ul><!-- /wp:list -->',
            'troubleshooting' => '<!-- wp:heading --><h2>Minkä ongelman tämä ratkaisee</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä kun jokin WordPressissä ei näytä tai toimi odotetusti.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Ennen kuin aloitat</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Kirjoita ylös mitä muutit.</li><li>Avaa sivu uudessa välilehdessä.</li><li>Tarkista tapahtuuko ongelma työpöydällä, mobiilissa vai molemmissa.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Tarkistukset</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Päivitä sivu.</li><li>Tyhjennä välimuisti jos vanha versio näkyy edelleen.</li><li>Tarkista että muokkasit oikeaa kieliversiota.</li><li>Pyydä tarkistajaa vahvistamaan tulos.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Älä oleta että Päivitä-painiketta klikattiin.</li><li>Älä tarkista vain kirjautunutta näkymää.</li><li>Älä korjaa samaa sisältöä kahdesta paikasta tarkistamatta kumpi on reaaliaikainen.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Sivu näyttää odotetun sisällön kirjautumattomalle kävijälle.</p><!-- /wp:paragraph -->',
            'concept' => '<!-- wp:heading --><h2>Mitä tämä tarkoittaa</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Selitä termi selkokielellä ennen vaiheita.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Käytä tätä kun</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä, kun käyttäjän täytyy ymmärtää idea ennen turvallista muokkausta.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Yksinkertainen esimerkki</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Anna yksi konkreettinen esimerkki tästä WordPress-sivustosta.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Älä käytä teknistä termiä ennen kuin määrittelet sen.</li><li>Älä selitä reunatapauksia ennen perusajatusta.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Uusi muokkaaja voi selittää idean takaisin yhdellä lauseella.</p><!-- /wp:paragraph -->',
            'checklist' => '<!-- wp:heading --><h2>Mihin tämä tarkistuslista on tarkoitettu</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä kun haluat vahvistaa toistettavan WordPress-tehtävän olevan valmis.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Ennen kuin aloitat</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Avaa sivu jota tarkistat.</li><li>Tiedä mitä kieliversiota tarkistat.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Tarkistuslista</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Sisältö on oikein.</li><li>Linkit toimivat.</li><li>Kuvilla on alt-teksti.</li><li>Mobiilinäkymä toimii.</li><li>Sivu on tarkistettu toisen henkilön toimesta.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Älä merkitse tarkistuslistaa valmiiksi ennen kuin julkinen sivu on tarkistettu.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Jokainen tarkistuslistan kohta on vahvistettu julkisella sivulla.</p><!-- /wp:paragraph -->',
            'client-facing' => '<!-- wp:heading --><h2>Mihin tämä on tarkoitettu</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä kun ei-teknisen muokkaajan täytyy suorittaa tehtävä ilman kehittäjän apua.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Ennen kuin aloitat</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Tarvitset WordPress-muokkausoikeudet.</li><li>Sinulla pitäisi olla lopullinen sisältö valmiina.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Vaiheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Avaa oikea sivu.</li><li>Tee yksi muutos kerrallaan.</li><li>Klikkaa Päivitä.</li><li>Tarkista julkinen sivu.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Älä muokkaa toista kieltä vahingossa.</li><li>Älä jää paikkamerkkitekstiä näkyviin.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Julkinen sivu näyttää muutoksen oikeassa paikassa ja kielellä.</p><!-- /wp:paragraph -->',
            'internal-technical' => '<!-- wp:heading --><h2>Mihin tämä on tarkoitettu</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käytä tätä kun ylläpitäjä tarvitsee teknisen kontekstin WordPress-sivuston tukemiseen.</p><!-- /wp:paragraph -->'
                . '<!-- wp:heading --><h2>Ennen kuin aloitat</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Tarvitset oikean WordPress-roolin.</li><li>Tarvitset pääsyn relevanttiin lisäosaan, teemaan tai integraatioon.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Vaiheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list {"ordered":true} --><ol><li>Vahvista nykyinen käyttäytyminen.</li><li>Tee pienin turvallinen muutos.</li><li>Tyhjennä välimuistit tarvittaessa.</li><li>Testaa julkinen sivu.</li></ol><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Yleiset virheet</h2><!-- /wp:heading -->'
                . '<!-- wp:list --><ul><li>Älä muuta tuotantoasetuksia ilman syyn kirjaamista.</li><li>Älä ohita mobiilitarkistuksia.</li></ul><!-- /wp:list -->'
                . '<!-- wp:heading --><h2>Miten tarkistaa että se toimii</h2><!-- /wp:heading -->'
                . '<!-- wp:paragraph --><p>Käyttäytyminen on vahvistettu WordPress-hallinnassa ja julkisella sivulla.</p><!-- /wp:paragraph -->',
        ],
    ];

    $language_templates = $templates[$language] ?? $templates['en'];

    return $language_templates[$template] ?? $language_templates['basic-task'];
}

function gwi_instruction_difficulties(string $language = 'en'): array
{
    $difficulties = [
        'en' => [
            'basic' => __('Basic', 'general-wp-instructions'),
            'intermediate' => __('Intermediate', 'general-wp-instructions'),
            'advanced' => __('Advanced', 'general-wp-instructions'),
        ],
        'fi' => [
            'basic' => __('Perus', 'general-wp-instructions'),
            'intermediate' => __('Keskitaso', 'general-wp-instructions'),
            'advanced' => __('Edistynyt', 'general-wp-instructions'),
        ],
    ];

    return $difficulties[$language] ?? $difficulties['en'];
}

function gwi_instruction_review_statuses(string $language = 'en'): array
{
    $statuses = [
        'en' => [
            'draft' => __('Draft', 'general-wp-instructions'),
            'needs-review' => __('Needs review', 'general-wp-instructions'),
            'tested' => __('Tested', 'general-wp-instructions'),
            'outdated' => __('Outdated', 'general-wp-instructions'),
            'deprecated' => __('Deprecated', 'general-wp-instructions'),
        ],
        'fi' => [
            'draft' => __('Luonnos', 'general-wp-instructions'),
            'needs-review' => __('Tarvitsee tarkistuksen', 'general-wp-instructions'),
            'tested' => __('Testattu', 'general-wp-instructions'),
            'outdated' => __('Vanhentunut', 'general-wp-instructions'),
            'deprecated' => __('Käytöstä poistettu', 'general-wp-instructions'),
        ],
    ];

    return $statuses[$language] ?? $statuses['en'];
}

function gwi_instruction_clarity_items(string $language = 'en'): array
{
    $items = [
        'en' => [
            'task_title' => __('The title starts with a verb or clear task.', 'general-wp-instructions'),
            'purpose' => __('The intro explains who this is for.', 'general-wp-instructions'),
            'action_steps' => __('Every step starts with an action.', 'general-wp-instructions'),
            'one_action' => __('No step contains more than one action.', 'general-wp-instructions'),
            'screenshots_near_steps' => __('Screenshots are placed immediately after the related step.', 'general-wp-instructions'),
            'terms_explained' => __('Technical terms are explained the first time they appear.', 'general-wp-instructions'),
            'success_check' => __('The document has a How to check it worked section.', 'general-wp-instructions'),
            'common_mistakes' => __('The document has a Common mistakes section.', 'general-wp-instructions'),
            'outside_tested' => __('The document has been tested by someone who did not write it.', 'general-wp-instructions'),
        ],
        'fi' => [
            'task_title' => __('Otsikko alkaa verbistä tai selkeästä tehtävästä.', 'general-wp-instructions'),
            'purpose' => __('Johdanto selittää kenelle tämä on tarkoitettu.', 'general-wp-instructions'),
            'action_steps' => __('Jokainen vaihe alkaa toiminnosta.', 'general-wp-instructions'),
            'one_action' => __('Mikään vaihe ei sisällä useampaa toimintoa.', 'general-wp-instructions'),
            'screenshots_near_steps' => __('Kuvakaappaukset on sijoitettu heti liittyvän vaiheen jälkeen.', 'general-wp-instructions'),
            'terms_explained' => __('Tekniset termit selitetään ensimmäisellä kerralla.', 'general-wp-instructions'),
            'success_check' => __('Dokumentissa on osio "Miten tarkistaa että se toimii".', 'general-wp-instructions'),
            'common_mistakes' => __('Dokumentissa on osio "Yleiset virheet".', 'general-wp-instructions'),
            'outside_tested' => __('Dokumentin on testannut joku muu kuin sen kirjoittaja.', 'general-wp-instructions'),
        ],
    ];

    return $items[$language] ?? $items['en'];
}

function gwi_sanitize_instruction_template($template): string
{
    $template = is_string($template) ? sanitize_key($template) : 'basic-task';

    return array_key_exists($template, gwi_instruction_templates()) ? $template : 'basic-task';
}

function gwi_sanitize_instruction_difficulty($difficulty): string
{
    $difficulty = is_string($difficulty) ? sanitize_key($difficulty) : 'basic';

    return array_key_exists($difficulty, gwi_instruction_difficulties()) ? $difficulty : 'basic';
}

function gwi_sanitize_review_status($status): string
{
    $status = is_string($status) ? sanitize_key($status) : 'needs-review';

    return array_key_exists($status, gwi_instruction_review_statuses()) ? $status : 'needs-review';
}

function gwi_sanitize_estimated_minutes($minutes): int
{
    return max(1, min(60, absint($minutes)));
}

function gwi_sanitize_review_date($date): string
{
    $date = is_string($date) ? trim($date) : '';

    if ($date === '') {
        return '';
    }

    $timestamp = strtotime($date);

    return $timestamp ? gmdate('Y-m-d', $timestamp) : '';
}

function gwi_register_instruction_taxonomy(): void
{
    $labels = [
        'name' => __('Instruction Categories', 'general-wp-instructions'),
        'singular_name' => __('Instruction Category', 'general-wp-instructions'),
        'search_items' => __('Search Categories', 'general-wp-instructions'),
        'all_items' => __('All Categories', 'general-wp-instructions'),
        'parent_item' => __('Parent Category', 'general-wp-instructions'),
        'parent_item_colon' => __('Parent Category:', 'general-wp-instructions'),
        'edit_item' => __('Edit Category', 'general-wp-instructions'),
        'update_item' => __('Update Category', 'general-wp-instructions'),
        'add_new_item' => __('Add New Category', 'general-wp-instructions'),
        'new_item_name' => __('New Category Name', 'general-wp-instructions'),
        'menu_name' => __('Categories', 'general-wp-instructions'),
    ];

    register_taxonomy('instruction_category', 'wp_instruction', [
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => [
            'slug' => 'ohje-aihe',
            'with_front' => false,
        ],
    ]);

    $categories = [
        'fundamentals' => [
            'en' => 'WordPress Fundamentals',
            'fi' => 'WordPress-perusteet',
        ],
        'site-config' => [
            'en' => 'Site Configuration',
            'fi' => 'Sivuston asetukset',
        ],
        'block-editor' => [
            'en' => 'Block Editor',
            'fi' => 'Lohkoeditori',
        ],
        'classic-editor' => [
            'en' => 'Classic Editor',
            'fi' => 'Perinteinen editori',
        ],
        'multilingual' => [
            'en' => 'Multilingual',
            'fi' => 'Monikielisyys',
        ],
        'advanced' => [
            'en' => 'Advanced Features',
            'fi' => 'Edistyneet ominaisuudet',
        ],
    ];

    foreach ($categories as $slug => $names) {
        if (!term_exists($slug, 'instruction_category')) {
            wp_insert_term($names['en'], 'instruction_category', [
                'slug' => $slug,
                'description' => $names['en'],
            ]);
        }

        $term = get_term_by('slug', $slug, 'instruction_category');

        if ($term instanceof WP_Term && ($term->name !== $names['en'] || $term->description !== $names['en'])) {
            wp_update_term($term->term_id, 'instruction_category', [
                'name' => $names['en'],
                'description' => $names['en'],
            ]);
        }
    }
}

function gwi_can_edit_post_meta(bool $allowed, string $meta_key, int $post_id): bool
{
    return current_user_can('edit_post', $post_id);
}

function gwi_get_instruction_language(int $post_id): string
{
    return gwi_sanitize_language(get_post_meta($post_id, '_gwi_language', true));
}

function gwi_get_instruction_language_label(int $post_id): string
{
    $language = gwi_get_instruction_language($post_id);
    $languages = gwi_languages();

    return $languages[$language] ?? $languages['en'];
}

function gwi_get_translation_id(int $post_id): int
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

function gwi_add_instruction_meta_boxes(): void
{
    add_meta_box(
        'gwi_instruction_language',
        __('Instruction Language', 'general-wp-instructions'),
        'gwi_render_instruction_language_meta_box',
        'wp_instruction',
        'side',
        'high'
    );

    add_meta_box(
        'gwi_instruction_standards',
        __('Document Standards', 'general-wp-instructions'),
        'gwi_render_instruction_standards_meta_box',
        'wp_instruction',
        'normal',
        'high'
    );
}

function gwi_render_instruction_language_meta_box(WP_Post $post): void
{
    wp_nonce_field('gwi_save_instruction_meta', 'gwi_instruction_meta_nonce');

    $current_language = gwi_get_instruction_language($post->ID);
    $current_translation = gwi_get_translation_id($post->ID);
    $labels = gwi_admin_labels($current_language);
    $instructions = get_posts([
        'post_type' => 'wp_instruction',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 100,
        'orderby' => 'title',
        'order' => 'ASC',
        'exclude' => [$post->ID],
    ]);
    ?>
    <p>
        <label for="gwi_language"><strong><?php echo esc_html($labels['language']); ?></strong></label>
        <select id="gwi_language" name="gwi_language" class="widefat">
            <?php foreach (gwi_languages() as $code => $label) : ?>
                <option value="<?php echo esc_attr($code); ?>" <?php selected($current_language, $code); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="gwi_translation_id"><strong><?php echo esc_html($labels['translation']); ?></strong></label>
        <select id="gwi_translation_id" name="gwi_translation_id" class="widefat">
            <option value="0"><?php echo esc_html($labels['no_translation']); ?></option>
            <?php foreach ($instructions as $instruction) : ?>
                <option value="<?php echo esc_attr($instruction->ID); ?>" <?php selected($current_translation, $instruction->ID); ?>>
                    <?php echo esc_html(get_the_title($instruction)); ?> (<?php echo esc_html(gwi_get_instruction_language_label($instruction->ID)); ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class="description">
        <?php echo esc_html($labels['translation_description']); ?>
    </p>
    <?php
}

function gwi_render_instruction_standards_meta_box(WP_Post $post): void
{
    $template = gwi_sanitize_instruction_template(get_post_meta($post->ID, '_gwi_template', true));
    $difficulty = gwi_sanitize_instruction_difficulty(get_post_meta($post->ID, '_gwi_difficulty', true));
    $estimated_minutes = gwi_sanitize_estimated_minutes(get_post_meta($post->ID, '_gwi_estimated_minutes', true));
    $review_status = gwi_sanitize_review_status(get_post_meta($post->ID, '_gwi_review_status', true));
    $last_reviewed = gwi_sanitize_review_date(get_post_meta($post->ID, '_gwi_last_reviewed', true));
    $owner = (string) get_post_meta($post->ID, '_gwi_owner', true);
    $purpose = (string) get_post_meta($post->ID, '_gwi_purpose', true);
    $checked_items = gwi_get_instruction_clarity_checks($post->ID);
    $current_language = gwi_get_instruction_language($post->ID);
    $labels = gwi_admin_labels($current_language);
    $score = gwi_calculate_instruction_clarity_score(
        $post->post_title,
        $post->post_content,
        $purpose,
        $checked_items,
        $current_language
    );

    update_post_meta($post->ID, '_gwi_clarity_score', $score);
    ?>
    <div class="gwi-standards-grid">
        <p>
            <label for="gwi_template"><strong><?php echo esc_html($labels['template']); ?></strong></label>
            <select id="gwi_template" name="gwi_template" class="widefat">
                <?php foreach (gwi_instruction_templates($current_language) as $key => $template_config) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($template, $key); ?>>
                        <?php echo esc_html($template_config['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="gwi_difficulty"><strong><?php echo esc_html($labels['difficulty']); ?></strong></label>
            <select id="gwi_difficulty" name="gwi_difficulty" class="widefat">
                <?php foreach (gwi_instruction_difficulties($current_language) as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($difficulty, $key); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="gwi_estimated_minutes"><strong><?php echo esc_html($labels['estimated_minutes']); ?></strong></label>
            <input id="gwi_estimated_minutes" name="gwi_estimated_minutes" type="number" min="1" max="60" value="<?php echo esc_attr((string) $estimated_minutes); ?>" class="small-text">
        </p>
        <p>
            <label for="gwi_review_status"><strong><?php echo esc_html($labels['review_status']); ?></strong></label>
            <select id="gwi_review_status" name="gwi_review_status" class="widefat">
                <?php foreach (gwi_instruction_review_statuses($current_language) as $key => $label) : ?>
                    <option value="<?php echo esc_attr($key); ?>" <?php selected($review_status, $key); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="gwi_last_reviewed"><strong><?php echo esc_html($labels['last_reviewed']); ?></strong></label>
            <input id="gwi_last_reviewed" name="gwi_last_reviewed" type="date" value="<?php echo esc_attr($last_reviewed); ?>" class="regular-text">
        </p>
        <p>
            <label for="gwi_owner"><strong><?php echo esc_html($labels['owner']); ?></strong></label>
            <input id="gwi_owner" name="gwi_owner" type="text" value="<?php echo esc_attr($owner); ?>" class="regular-text" placeholder="<?php echo esc_attr($labels['owner_placeholder']); ?>">
        </p>
    </div>
    <p>
        <label for="gwi_purpose"><strong><?php echo esc_html($labels['purpose']); ?></strong></label>
        <input id="gwi_purpose" name="gwi_purpose" type="text" value="<?php echo esc_attr($purpose); ?>" class="large-text" placeholder="<?php echo esc_attr($labels['purpose_placeholder']); ?>">
    </p>
    <div class="gwi-clarity-score">
        <strong><?php echo esc_html($labels['clarity_score']); ?>:</strong>
        <?php echo esc_html((string) $score); ?>/100
        <span class="description"><?php echo esc_html($labels['publishing_requires']); ?></span>
    </div>
    <p class="description">
        <?php echo esc_html($labels['clarity_mode']); ?>
    </p>
    <fieldset>
        <legend><strong><?php echo esc_html($labels['checklist']); ?></strong></legend>
        <?php foreach (gwi_instruction_clarity_items($current_language) as $key => $label) : ?>
            <label style="display:block;margin:0.45rem 0;">
                <input type="checkbox" name="gwi_clarity_checks[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $checked_items, true)); ?>>
                <?php echo esc_html($label); ?>
            </label>
        <?php endforeach; ?>
    </fieldset>
    <p class="description">
        <?php echo esc_html($labels['templates_info']); ?>
    </p>
    <?php
}

function gwi_save_instruction_meta(int $post_id): void
{
    if (!isset($_POST['gwi_instruction_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['gwi_instruction_meta_nonce'])), 'gwi_save_instruction_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $language = isset($_POST['gwi_language']) ? gwi_sanitize_language(wp_unslash($_POST['gwi_language'])) : 'en';
    $translation_id = isset($_POST['gwi_translation_id']) ? absint($_POST['gwi_translation_id']) : 0;
    $template = isset($_POST['gwi_template']) ? gwi_sanitize_instruction_template(wp_unslash($_POST['gwi_template'])) : 'basic-task';
    $difficulty = isset($_POST['gwi_difficulty']) ? gwi_sanitize_instruction_difficulty(wp_unslash($_POST['gwi_difficulty'])) : 'basic';
    $estimated_minutes = isset($_POST['gwi_estimated_minutes']) ? gwi_sanitize_estimated_minutes(wp_unslash($_POST['gwi_estimated_minutes'])) : 4;
    $review_status = isset($_POST['gwi_review_status']) ? gwi_sanitize_review_status(wp_unslash($_POST['gwi_review_status'])) : 'needs-review';
    $last_reviewed = isset($_POST['gwi_last_reviewed']) ? gwi_sanitize_review_date(wp_unslash($_POST['gwi_last_reviewed'])) : '';
    $owner = isset($_POST['gwi_owner']) ? sanitize_text_field(wp_unslash($_POST['gwi_owner'])) : '';
    $purpose = isset($_POST['gwi_purpose']) ? sanitize_text_field(wp_unslash($_POST['gwi_purpose'])) : '';
    $clarity_checks = isset($_POST['gwi_clarity_checks']) && is_array($_POST['gwi_clarity_checks'])
        ? array_values(array_intersect(array_map('sanitize_key', wp_unslash($_POST['gwi_clarity_checks'])), array_keys(gwi_instruction_clarity_items($language))))
        : [];
    $post = get_post($post_id);
    $score = $post instanceof WP_Post
        ? gwi_calculate_instruction_clarity_score($post->post_title, $post->post_content, $purpose, $clarity_checks, $language)
        : 0;

    update_post_meta($post_id, '_gwi_language', $language);
    update_post_meta($post_id, '_gwi_translation_id', $translation_id);
    update_post_meta($post_id, '_gwi_template', $template);
    update_post_meta($post_id, '_gwi_difficulty', $difficulty);
    update_post_meta($post_id, '_gwi_estimated_minutes', $estimated_minutes);
    update_post_meta($post_id, '_gwi_review_status', $review_status);
    update_post_meta($post_id, '_gwi_last_reviewed', $last_reviewed);
    update_post_meta($post_id, '_gwi_owner', $owner);
    update_post_meta($post_id, '_gwi_purpose', $purpose);
    update_post_meta($post_id, '_gwi_clarity_checks', $clarity_checks);
    update_post_meta($post_id, '_gwi_clarity_score', $score);
}

function gwi_instruction_columns(array $columns): array
{
    $next_columns = [];

    foreach ($columns as $key => $label) {
        $next_columns[$key] = $label;

        if ($key === 'title') {
            $next_columns['gwi_language'] = __('Language', 'general-wp-instructions');
            $next_columns['gwi_review_status'] = __('Status', 'general-wp-instructions');
            $next_columns['gwi_clarity_score'] = __('Clarity', 'general-wp-instructions');
            $next_columns['gwi_translation'] = __('Translation', 'general-wp-instructions');
        }
    }

    return $next_columns;
}

function gwi_instruction_column_content(string $column, int $post_id): void
{
    if ($column === 'gwi_language') {
        echo esc_html(gwi_get_instruction_language_label($post_id));
        return;
    }

    if ($column === 'gwi_review_status') {
        $status = gwi_sanitize_review_status(get_post_meta($post_id, '_gwi_review_status', true));
        $language = gwi_get_instruction_language($post_id);
        $statuses = gwi_instruction_review_statuses($language);
        echo esc_html($statuses[$status] ?? $statuses['needs-review']);
        return;
    }

    if ($column === 'gwi_clarity_score') {
        echo esc_html((string) absint(get_post_meta($post_id, '_gwi_clarity_score', true))) . '/100';
        return;
    }

    if ($column === 'gwi_translation') {
        $translation_id = gwi_get_translation_id($post_id);
        echo $translation_id ? '<a href="' . esc_url(get_edit_post_link($translation_id)) . '">' . esc_html(get_the_title($translation_id)) . '</a>' : '&mdash;';
    }
}

function gwi_get_instruction_clarity_checks(int $post_id): array
{
    $checks = get_post_meta($post_id, '_gwi_clarity_checks', true);
    $language = gwi_get_instruction_language($post_id);

    if (!is_array($checks)) {
        return [];
    }

    return array_values(array_intersect(array_map('sanitize_key', $checks), array_keys(gwi_instruction_clarity_items($language))));
}

function gwi_calculate_instruction_clarity_score(string $title, string $content, string $purpose = '', array $checked_items = [], string $language = 'en'): int
{
    $plain_text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags(strip_shortcodes($content))) ?? '');
    $sections_text = strtolower($plain_text . ' ' . wp_strip_all_tags($content));
    $words = preg_split('/\s+/', $plain_text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $sentences = preg_split('/[.!?]+/', $plain_text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $average_sentence_words = count($sentences) > 0 ? count($words) / count($sentences) : 0;
    $paragraphs = preg_split('/\n\s*\n/', wp_strip_all_tags($content), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $long_paragraphs = array_filter($paragraphs, static function ($paragraph): bool {
        return str_word_count((string) $paragraph) > 80;
    });
    $title_has_task = preg_match('/^(add|edit|change|create|remove|update|upload|publish|check|fix|make|manage|how to)\b/i', trim($title)) === 1;
    $has_purpose = trim($purpose) !== '' || preg_match('/use this when|what this is for|what problem this solves|use this if/i', $content) === 1;
    $has_success_check = preg_match('/how to check it worked|result|what you should see/i', $content) === 1;
    $has_common_mistakes = preg_match('/common mistakes|do not|avoid/i', $content) === 1;
    $has_unexplained_terms = preg_match('/\b(acf|cache|slug|template|field|redirect|frontend)\b/i', $plain_text) === 1
        && preg_match('/\b(means|is a|is an|called|in simple terms)\b/i', $plain_text) !== 1;
    $has_passive_clicking = preg_match('/\b(button|link|field|page|post)\s+should\s+be\s+(clicked|opened|updated|edited|saved)\b/i', $plain_text) === 1;
    $has_screenshot_without_caption = preg_match('/<img\b/i', $content) === 1 && preg_match('/<figcaption\b|caption/i', $content) !== 1;
    $checked_items = array_values(array_intersect(array_map('sanitize_key', $checked_items), array_keys(gwi_instruction_clarity_items($language))));

    $score = 0;
    $score += $title_has_task ? 10 : 0;
    $score += $has_purpose ? 12 : 0;
    $score += $has_success_check ? 12 : 0;
    $score += $has_common_mistakes ? 10 : 0;
    $score += $average_sentence_words > 0 && $average_sentence_words <= 18 ? 12 : 0;
    $score += empty($long_paragraphs) ? 8 : 0;
    $score += !$has_unexplained_terms ? 8 : 0;
    $score += !$has_passive_clicking ? 6 : 0;
    $score += !$has_screenshot_without_caption ? 6 : 0;
    $score += (int) floor((count($checked_items) / max(1, count(gwi_instruction_clarity_items($language)))) * 16);

    if (str_contains($sections_text, 'steps') || str_contains($sections_text, 'checklist')) {
        $score += 4;
    }

    return min(100, max(0, $score));
}

function gwi_guard_instruction_publish(array $data, array $postarr): array
{
    if (($data['post_type'] ?? '') !== 'wp_instruction') {
        return $data;
    }

    if (!in_array($data['post_status'] ?? '', ['publish', 'future'], true)) {
        return $data;
    }

    if (!is_admin() || !isset($_POST['gwi_instruction_meta_nonce'])) {
        return $data;
    }

    $purpose = isset($_POST['gwi_purpose']) ? sanitize_text_field(wp_unslash($_POST['gwi_purpose'])) : '';
    $language = isset($_POST['gwi_language']) ? gwi_sanitize_language(wp_unslash($_POST['gwi_language'])) : 'en';
    $checks = isset($_POST['gwi_clarity_checks']) && is_array($_POST['gwi_clarity_checks'])
        ? array_values(array_intersect(array_map('sanitize_key', wp_unslash($_POST['gwi_clarity_checks'])), array_keys(gwi_instruction_clarity_items($language))))
        : [];
    $score = gwi_calculate_instruction_clarity_score(
        (string) ($data['post_title'] ?? ''),
        (string) ($data['post_content'] ?? ''),
        $purpose,
        $checks,
        $language
    );

    if ($score >= 75) {
        return $data;
    }

    $data['post_status'] = 'draft';
    set_transient('gwi_clarity_guard_' . get_current_user_id(), $score, 60);

    return $data;
}

function gwi_render_clarity_guard_notice(): void
{
    $user_id = get_current_user_id();
    $score = get_transient('gwi_clarity_guard_' . $user_id);

    if ($score === false) {
        return;
    }

    delete_transient('gwi_clarity_guard_' . $user_id);
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <strong><?php esc_html_e('Instruction kept as draft.', 'general-wp-instructions'); ?></strong>
            <?php echo esc_html(sprintf(__('Clarity score is %d/100. Publishing requires 75/100 and the required checklist.', 'general-wp-instructions'), absint($score))); ?>
        </p>
    </div>
    <?php
}

function gwi_default_instruction_content(string $content, WP_Post $post): string
{
    if ($post->post_type !== 'wp_instruction') {
        return $content;
    }

    $template = isset($_GET['gwi_template']) ? gwi_sanitize_instruction_template(wp_unslash($_GET['gwi_template'])) : '';

    if ($template === '') {
        return $content;
    }

    $templates = gwi_instruction_templates();

    return $templates[$template]['content'] ?? $content;
}

function gwi_register_instruction_template_page(): void
{
    add_submenu_page(
        'edit.php?post_type=wp_instruction',
        __('Templates', 'general-wp-instructions'),
        __('Templates', 'general-wp-instructions'),
        'edit_posts',
        'gwi-instruction-templates',
        'gwi_render_instruction_template_page'
    );
}

function gwi_render_instruction_template_page(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Instruction Templates', 'general-wp-instructions'); ?></h1>
        <p><?php esc_html_e('Start with a forced structure so guides answer: what is this for, what do I do, and how do I know it worked?', 'general-wp-instructions'); ?></p>
        <div class="card" style="max-width: 900px;">
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Template', 'general-wp-instructions'); ?></th>
                        <th><?php esc_html_e('Use when', 'general-wp-instructions'); ?></th>
                        <th><?php esc_html_e('Action', 'general-wp-instructions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (gwi_instruction_templates() as $key => $template) : ?>
                        <tr>
                            <td><strong><?php echo esc_html($template['label']); ?></strong></td>
                            <td><?php echo esc_html($template['description']); ?></td>
                            <td><a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=wp_instruction&gwi_template=' . $key)); ?>"><?php esc_html_e('Start document', 'general-wp-instructions'); ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
