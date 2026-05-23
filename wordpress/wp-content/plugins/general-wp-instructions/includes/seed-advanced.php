<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_advanced(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'custom-fields-acf-en',
            'Custom Fields with ACF',
            'en',
            gwi_seed_custom_fields_acf_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'mukautetut-kentat-acf-fi',
            'Mukautetut kentat ACF:lla',
            'fi',
            gwi_seed_custom_fields_acf_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'flexible-content-en',
            'Flexible Content Layouts',
            'en',
            gwi_seed_flexible_content_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'joustavat-sisaltoasettelut-fi',
            'Joustavat sisaltoasettelut',
            'fi',
            gwi_seed_flexible_content_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'acf-blocks-en',
            'ACF Blocks',
            'en',
            gwi_seed_acf_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'acf-lohkot-fi',
            'ACF-lohkot',
            'fi',
            gwi_seed_acf_blocks_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'seo-basics-en',
            'SEO Basics',
            'en',
            gwi_seed_seo_basics_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'seo-perusteet-fi',
            'SEO-perusteet',
            'fi',
            gwi_seed_seo_basics_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'performance-caching-en',
            'Performance and Caching',
            'en',
            gwi_seed_performance_caching_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'suorituskyky-ja-valimuisti-fi',
            'Suorituskyky ja valimuisti',
            'fi',
            gwi_seed_performance_caching_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_custom_fields_acf_en(): string
{
    return '<!-- wp:paragraph --><p>Advanced Custom Fields (ACF) lets you add extra data fields to posts, pages, and custom content types. Use it when the standard editor is not enough.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use ACF custom fields","steps":[{"text":"When editing a post or page, scroll down to find the ACF field groups below the editor."},{"text":"Fill in the fields as instructed. Fields may include text, images, selects, dates, and more."},{"text":"Some fields are required and marked with an asterisk. Complete them before publishing."},{"text":"Field groups are organized by the developer. Contact them if you need new fields."},{"text":"Save or publish the post to save the custom field values."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"ACF field groups in the WordPress admin"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: ACF fields appear on the editing screen automatically based on the field group rules set by your developer.</p><!-- /wp:paragraph -->';
}

function gwi_seed_custom_fields_acf_fi(): string
{
    return '<!-- wp:paragraph --><p>Advanced Custom Fields (ACF) antaa lisata ylimääräisiä tietokenttia artikkeleihin, sivuihin ja mukautettuihin sisaltotyyppeihin. Kayta sitä kun normaali editori ei riitä.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä ACF-mukautettuja kenttia","steps":[{"text":"Muokatessasi artikkelia tai sivua, vieritä alas loytääksesi ACF-kenttäryhmät editorin alla."},{"text":"Täytä kentät ohjeiden mukaan. Kentät voivat sisaltaa tekstia, kuvia, valintoja, päivämääria ja muuta."},{"text":"Jotkin kentät ovat pakollisia ja merkitty tähdellä. Täytä ne ennen julkaisua."},{"text":"Kenttäryhmät ovat kehittajan jarjestämia. Ota yhteyttä heihin jos tarvitset uusia kenttia."},{"text":"Tallenna tai julkaise artikkeli tallentaaksesi mukautettujen kenttien arvot."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"ACF-kenttäryhmät WordPress-hallinnassa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: ACF-kentat näkyvät muokkausruudulla automaattisesti kehittajan asettamien kenttäryhmäsaantojen perusteella.</p><!-- /wp:paragraph -->';
}

function gwi_seed_flexible_content_en(): string
{
    return '<!-- wp:paragraph --><p>Flexible Content is an ACF feature that lets editors build pages by adding, removing, and reordering content sections. Each section is a predefined layout with its own fields.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Edit Flexible Content layouts","steps":[{"text":"Scroll to the Flexible Content field group when editing a page."},{"text":"Click Add Row to see the available layout options."},{"text":"Choose a layout type from the dropdown. Each layout has different fields."},{"text":"Fill in the fields for the section you added."},{"text":"Use the drag handle to reorder sections. Click Remove to delete a section."},{"text":"Save the page to apply your changes."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"Flexible Content field group with layout options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Add the [gwi_flexible_content] shortcode to the post content if the sections do not appear automatically on the front end.</p><!-- /wp:paragraph -->';
}

function gwi_seed_flexible_content_fi(): string
{
    return '<!-- wp:paragraph --><p>Joustava sisalto on ACF-ominaisuus, joka antaa muokkaajien rakentaa sivuja lisäämällä, poistamalla ja järjestämällä sisalto-osioita uudelleen. Jokainen osio on esimääritelty asettelu omilla kentillään.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Muokkaa joustavia sisaltoasetteluja","steps":[{"text":"Vieritä Joustava sisalto -kenttäryhmään muokatessasi sivua."},{"text":"Klikkaa Lisää rivi nähdäksesi saatavilla olevat asetteluvaihtoehdot."},{"text":"Valitse asettelutyyppi pudotusvalikosta. Jokaisella asettelulla on eri kentat."},{"text":"Täytä kentat lisäämällesi osiolle."},{"text":"Käytä vetokahvaa järjestääksesi osiot uudelleen. Klikkaa Poista poistaaksesi osion."},{"text":"Tallenna sivu soveltaaksesi muutokset."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"Joustava sisalto -kenttäryhmä asetteluvaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Lisaa [gwi_flexible_content] lyhytkoodi artikkelin sisaltoon jos osiot eivat näy automaattisesti etupäässä.</p><!-- /wp:paragraph -->';
}

function gwi_seed_acf_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>ACF Blocks are custom Gutenberg blocks created with Advanced Custom Fields. They appear in the block inserter alongside native WordPress blocks.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use ACF Blocks","steps":[{"text":"Open the block inserter by clicking the plus button in the editor toolbar."},{"text":"Search for the ACF block name, such as Instruction Callout or Screenshot Step."},{"text":"Click the block to insert it into your content."},{"text":"Fill in the block fields in the editor or the block settings sidebar."},{"text":"Use the block toolbar to align, move, or transform the block."},{"text":"Preview the page to see how the block renders on the front end."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"ACF Blocks available in the block inserter"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: ACF Blocks behave like native blocks. You can save them as part of reusable blocks or patterns.</p><!-- /wp:paragraph -->';
}

function gwi_seed_acf_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>ACF-lohkot ovat mukautettuja Gutenberg-lohkoja, jotka on luotu Advanced Custom Fieldsilla. Ne näkyvät lohkon lisaajassa WordPressin omien lohkojen rinnalla.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä ACF-lohkoja","steps":[{"text":"Avaa lohkon lisaaja klikkaamalla plus-painiketta editorin tyokalurivissa."},{"text":"Hae ACF-lohkon nimea, kuten Ohjeilmoitus tai Kuvakaappausvaihe."},{"text":"Klikkaa lohkoa lisätäksesi sen sisaltoosi."},{"text":"Täytä lohkon kentat editorissa tai lohkon asetussivupalkissa."},{"text":"Käytä lohkon tyokalurivia tasataksesi, siirtääksesi tai muuntaaksesi lohkon."},{"text":"Esikatsele sivua nähdäksesi miten lohko renderöityy etupäässä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"ACF-lohkot saatavilla lohkon lisaajassa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: ACF-lohkot kayttaytyvat kuin natiivilohkot. Voit tallentaa ne osaksi uudelleenkaytettavia lohkoja tai malleja.</p><!-- /wp:paragraph -->';
}

function gwi_seed_seo_basics_en(): string
{
    return '<!-- wp:paragraph --><p>Search Engine Optimization (SEO) helps your content rank higher in search results. WordPress has built-in features, and plugins like Yoast SEO or RankMath add more tools.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Apply basic SEO","steps":[{"text":"Write a clear, descriptive title for every post and page. Include your main keyword."},{"text":"Use headings (H2, H3) to structure your content logically."},{"text":"Add alt text to all images describing what they show."},{"text":"Write a meta description if your SEO plugin provides a field for it."},{"text":"Use internal links to connect related content on your site."},{"text":"Keep URLs short and descriptive. Use the Permalink settings to configure the structure."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"seo-dashboard","caption":"SEO plugin dashboard for managing optimization"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Focus on writing helpful, well-structured content first. SEO plugins can guide you, but quality content is the most important ranking factor.</p><!-- /wp:paragraph -->';
}

function gwi_seed_seo_basics_fi(): string
{
    return '<!-- wp:paragraph --><p>Hakukoneoptimointi (SEO) auttaa sivustosi sisaltoa sijoittumaan paremmin hakutuloksissa. WordPressissa on sisäänrakennettuja ominaisuuksia, ja lisäosat kuten Yoast SEO tai RankMath lisäävat työkaluja.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Sovella perus-SEO:ta","steps":[{"text":"Kirjoita selkeä, kuvaava otsikko jokaiselle artikkelille ja sivulle. Sisällytä paasiasanakirjoituksesi."},{"text":"Käytä otsikoita (H2, H3) rakentaaksesi sisaltosi loogisesti."},{"text":"Lisää vaihtoehtoinen teksti kaikkiin kuviin kuvaillen mita ne näyttävät."},{"text":"Kirjoita metakuvaus jos SEO-lisäosa tarjoaa siihen kentän."},{"text":"Käytä sisäisiä linkkejä yhdistääksesi liittyvän sisaltosi sivustollasi."},{"text":"Pidä URL-osoitteet lyhyinä ja kuvaavina. Käytä Osoiterakenteen asetuksia rakenteen määrittämiseen."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"seo-dashboard","caption":"SEO-lisäosan hallintapaneeli optimoinnin hallintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Keskity ensin kirjoittamaan hyödyllistä, hyvin rakennettua sisaltoa. SEO-lisäosat voivat ohjata, mutta laadukas sisalto on tärkein sijoittumistekijä.</p><!-- /wp:paragraph -->';
}

function gwi_seed_performance_caching_en(): string
{
    return '<!-- wp:paragraph --><p>Site speed affects user experience and search rankings. WordPress performance can be improved with caching, image optimization, and proper hosting.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Improve site performance","steps":[{"text":"Install a caching plugin like WP Super Cache or W3 Total Cache to serve static pages."},{"text":"Optimize images before uploading. Use the correct size and compress files."},{"text":"Use a CDN (Content Delivery Network) to serve static assets from servers closer to visitors."},{"text":"Keep WordPress, themes, and plugins updated for security and performance fixes."},{"text":"Remove unused plugins and themes to reduce server load."},{"text":"Choose quality hosting with good server response times."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"WordPress settings for performance-related options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Test your site speed regularly using tools like Google PageSpeed Insights or GTmetrix.</p><!-- /wp:paragraph -->';
}

function gwi_seed_performance_caching_fi(): string
{
    return '<!-- wp:paragraph --><p>Sivuston nopeus vaikuttaa kayttökokemukseen ja hakutulossijoituksiin. WordPressin suorituskykyä voidaan parantaa välimuistilla, kuvien optimoinnilla ja oikealla hostingilla.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Paranna sivuston suorituskykyä","steps":[{"text":"Asenna välimuistilisäosa kuten WP Super Cache tai W3 Total Cache tarjoamaan staattisia sivuja."},{"text":"Optimoi kuvat ennen lataamista. Käytä oikeaa kokoa ja pakenna tiedostot."},{"text":"Käytä CDN:aa (Content Delivery Network) tarjoamaan staattisia resursseja lähempana kävijöitä."},{"text":"Pidä WordPress, teemat ja lisäosat päivitettyinä turvallisuus- ja suorituskyvyn korjauksia varten."},{"text":"Poista käyttämättömät lisäosat ja teemat vähentääksesi palvelimen kuormitusta."},{"text":"Valitse laadukas hosting hyvillä palvelinvastausajoilla."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"WordPress-asetukset suorituskykyyn liittyvillä vaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Testaa sivustosi nopeutta säännöllisesti käyttämällä työkaluja kuten Google PageSpeed Insights tai GTmetrix.</p><!-- /wp:paragraph -->';
}
