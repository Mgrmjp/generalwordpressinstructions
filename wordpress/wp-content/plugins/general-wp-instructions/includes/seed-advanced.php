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
            'Mukautetut kentät ACF:lla',
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
            'Joustavat sisältöasettelut',
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
            'Suorituskyky ja välimuisti',
            'fi',
            gwi_seed_performance_caching_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_custom_fields_acf_en(): string
{
    return '<!-- wp:paragraph --><p>Advanced Custom Fields (ACF) lets you add extra data fields to posts, pages, and custom content types. Use it when content needs structured fields outside the normal editor canvas.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use ACF custom fields","steps":[{"text":"When editing a post or page, scroll down or open the relevant panel to find the ACF field groups."},{"text":"Fill in the fields as instructed. Fields may include text, images, selects, dates, repeaters, and more."},{"text":"Some fields are required and marked with an asterisk. Complete them before publishing."},{"text":"Use the WordPress editor for page body content and ACF fields for structured data the theme expects."},{"text":"Field groups are organized by the developer. Contact them if you need new fields or different placement."},{"text":"Save, update, or publish the post to save the custom field values."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"ACF field groups in the WordPress admin"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: In WordPress 7.0, some editor screens are more isolated for stability. If ACF styling or placement looks different after an update, check plugin compatibility before changing field data.</p><!-- /wp:paragraph -->';
}

function gwi_seed_custom_fields_acf_fi(): string
{
    return '<!-- wp:paragraph --><p>Advanced Custom Fields (ACF) antaa lisätä ylimääräisiä tietokenttiä artikkeleihin, sivuihin ja mukautettuihin sisältötyyppeihin. Käytä sitä, kun sisältö tarvitsee rakenteisia kenttiä tavallisen editorin ulkopuolella.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä ACF-mukautettuja kenttiä","steps":[{"text":"Muokatessasi artikkelia tai sivua vieritä alas tai avaa oikea paneeli löytääksesi ACF-kenttäryhmät."},{"text":"Täytä kentät ohjeiden mukaan. Kentät voivat sisältää tekstiä, kuvia, valintoja, päivämääriä, toistimia ja muuta."},{"text":"Jotkin kentät ovat pakollisia ja merkitty tähdellä. Täytä ne ennen julkaisua."},{"text":"Käytä WordPress-editoria sivun leipätekstille ja ACF-kenttiä rakenteiselle datalle, jota teema odottaa."},{"text":"Kenttäryhmät ovat kehittäjän järjestämiä. Ota yhteyttä kehittäjään, jos tarvitset uusia kenttiä tai eri sijoittelua."},{"text":"Tallenna, päivitä tai julkaise artikkeli tallentaaksesi mukautettujen kenttien arvot."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"acf-field-groups","caption":"ACF-kenttäryhmät WordPress-hallinnassa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: WordPress 7.0:ssa osa editorinäkymistä on eristetty vakauden parantamiseksi. Jos ACF:n ulkoasu tai sijoittelu näyttää päivityksen jälkeen erilaiselta, tarkista lisäosan yhteensopivuus ennen kenttädatan muuttamista.</p><!-- /wp:paragraph -->';
}

function gwi_seed_flexible_content_en(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>On many sites, <strong>Pages</strong> are built from a <strong>section list</strong> (ACF flexible content)—not from the block editor. You add rows on the Page edit screen, pick a ready-made type (intro, image with instructions, checklist, or highlighted note), fill the fields, and drag to reorder. The theme prints those sections on the public page. If your site expects blocks in the main editor, use the block editor guides instead.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">When this applies</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>You edit <strong>Pages</strong> (or similar) and the site was built by an agency or theme that uses ready-made section types.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>The main story of the page is <strong>not</strong> a stack of blocks—it is a list of sections below the editor.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>You never create new section types yourself; you only pick from the list the site already provides.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Where you edit</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Open <strong>Pages → All Pages</strong>, then the page you need. Scroll past the title and the small text editor at the top (if your site uses one). The <strong>Page sections</strong> box is where you add, reorder, and fill content. Names vary: Content sections, Flexible content, Page builder, and so on.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Quick start</h2>
<!-- /wp:heading -->

<!-- wp:general-wp-instructions/step-list {"title":"Add a section","steps":[{"text":"Go to Pages and open the page you need (or add a new one)."},{"text":"Use the classic or visual editor at the top only if your site keeps a short intro there—the main page body is usually the section list further down."},{"text":"Find a box named Page sections, Content sections, or similar (wording varies)."},{"text":"Click Add section, choose a type, fill required fields, click Update or Publish, then preview the page."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot {"screenshotId":"page-flexible-sections","caption":"Page edit screen: section list with Add section and layout choices (classic editor)","highlightX":89.67,"highlightY":2.58,"highlightWidth":9.63,"highlightHeight":2.1,"label":"Add section"} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Section types</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Not sure which type to pick? Open the short guide below. The examples after it show how each type looks when published—on a real site that output lives on the Page itself, not inside this instruction article.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[manual_flexible_legend]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Examples (for learning)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>One sample per section type, embedded here so you can compare layouts. On client sites you edit the same types in the section list on the Page—not in this guide.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[gwi_flexible_content]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Reorder and save</h2>
<!-- /wp:heading -->

<!-- wp:general-wp-instructions/step-list {"title":"After editing","steps":[{"text":"Drag a section by the handle on the left to change order, then click Update."},{"text":"Duplicate a section when two blocks should start from the same layout—then change only the text or image."},{"text":"Remove sections you do not need so the published page has no empty gaps."}]} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Trying to build the whole page in the block editor when the site is set up for a section list.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Editing only the small text area at the top and missing the section list where the page content actually lives.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Picking the wrong section type (for example image instructions in a text-only intro).</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Reordering sections but forgetting Update—the live page keeps the old order until you save.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
BLOCKS;
}

function gwi_seed_flexible_content_fi(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Monilla sivustoilla <strong>sivut</strong> rakennetaan <strong>osiolistasta</strong> (ACF flexible content)—ei lohkoeditorilla. Lisäät rivejä sivun muokkausnäkymässä, valitset valmiin tyypin (johdanto, kuva ja ohje, tarkistuslista tai korostettu huomio), täytät kentät ja järjestät vetämällä. Teema tulostaa osiot julkiselle sivulle. Jos sivustosi odottaa lohkoja pääeditorissa, käytä lohkoeditorin ohjeita.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Milloin tämä koskee sinua</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Muokkaat <strong>sivuja</strong> ja sivusto on tehty valmiilla osiotyypeillä (toimisto tai teema).</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Sivun varsinainen sisältö ei ole lohkopino vaan <strong>osiolista</strong> editorin alapuolella.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Et luo uusia osiotyyppejä—itse valitset vain listasta, mitä sivustolla on tarjolla.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Missä muokkaat</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Avaa <strong>Sivut → Kaikki sivut</strong> ja valitse oikea sivu. Vieritä otsikon ja mahdollisen lyhyen yläeditorin ohi. Laatikko <strong>Sivuosiot</strong> (tai vastaava nimi) on paikka, jossa lisäät, järjestät ja täytät sisällön. Nimi voi olla esimerkiksi Sisältöosiot tai Joustava sisältö.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Pikaohje</h2>
<!-- /wp:heading -->

<!-- wp:general-wp-instructions/step-list {"title":"Lisää osio","steps":[{"text":"Avaa Sivut ja valitse muokattava sivu (tai lisää uusi)."},{"text":"Käytä yläreunan perinteistä tai visuaalista editoria vain, jos sivustolla on lyhyt johdanto—varsinaiset sivuosiot ovat yleensä alempana osiolistassa."},{"text":"Etsi laatikko nimellä Sivuosiot, Sisältöosiot tms. (nimi vaihtelee)."},{"text":"Klikkaa Lisää osio, valitse tyyppi, täytä pakolliset kentät, paina Päivitä tai Julkaise ja esikatsele."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot {"screenshotId":"page-flexible-sections","caption":"Sivun muokkaus: osiolista, Lisää osio ja tyypit (perinteinen editori)","highlightX":89.67,"highlightY":2.58,"highlightWidth":9.63,"highlightHeight":2.1,"label":"Lisää osio"} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Osiotyypit</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Epäselvä valinta? Avaa lyhyt opas alla. Esimerkit näyttävät, miltä kukin tyyppi näyttää julkaistulla sivulla—oikealla sivustolla sama sisältö on itse sivulla, ei tässä ohjeartikkelissa.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[manual_flexible_legend]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Esimerkit (oppimista varten)</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Yksi malli per osiotyyppi tässä ohjeessa. Asiakassivustoilla muokkaat samoja tyyppejä sivun osiolistassa—et tässä oppaassa.</p>
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[gwi_flexible_content]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Järjestä ja tallenna</h2>
<!-- /wp:heading -->

<!-- wp:general-wp-instructions/step-list {"title":"Muokkauksen jälkeen","steps":[{"text":"Järjestä osioita vasemmasta kahvasta ja paina Päivitä."},{"text":"Kopioi osio, kun kaksi kohtaa käyttää samaa mallia—vaihda sitten vain teksti tai kuva."},{"text":"Poista turhat osiot, ettei julkaistulle sivulle jää tyhjiä välejä."}]} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>Koko sivu yritetään tehdä lohkoeditorilla, vaikka sivusto käyttää osiolistaa.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Muokataan vain yläreunan lyhyttä tekstialuetta ja ohitetaan osiolista, jossa sivun sisältö oikeasti on.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Valitaan väärä osiotyyppi (esim. kuvaohje pelkkään tekstijohdantoon).</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Järjestystä muutetaan ilman Päivitä—julkinen sivu päivittyy vasta tallennuksen jälkeen.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
BLOCKS;
}

function gwi_seed_acf_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>ACF Blocks are custom blocks created with Advanced Custom Fields. They appear in the block inserter alongside native WordPress blocks and may have fields tailored to your site.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use ACF Blocks","steps":[{"text":"Open the block inserter by clicking the plus button or typing / in the editor."},{"text":"Search for the ACF block name, such as Instruction Callout or Screenshot Step."},{"text":"Click the block to insert it into your content."},{"text":"Fill in the block fields in the editor canvas or the block settings sidebar."},{"text":"Use List View to select nested custom blocks reliably."},{"text":"Preview the page to confirm the block renders correctly on the front end."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"ACF Blocks available in the block inserter"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: ACF Blocks can be saved inside patterns, but synced patterns may update every instance. Detach or use a standard pattern when a page needs one-off content.</p><!-- /wp:paragraph -->';
}

function gwi_seed_acf_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>ACF-lohkot ovat mukautettuja lohkoja, jotka on luotu Advanced Custom Fieldsilla. Ne näkyvät lohkon lisääjässä WordPressin omien lohkojen rinnalla ja voivat sisältää sivustollesi räätälöityjä kenttiä.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä ACF-lohkoja","steps":[{"text":"Avaa lohkon lisääjä klikkaamalla plus-painiketta tai kirjoittamalla / editorissa."},{"text":"Hae ACF-lohkon nimeä, kuten Ohjeilmoitus tai Kuvakaappausvaihe."},{"text":"Klikkaa lohkoa lisätäksesi sen sisältöön."},{"text":"Täytä lohkon kentät editorinäkymässä tai lohkon asetussivupalkissa."},{"text":"Käytä Listanäkymää sisäkkäisten mukautettujen lohkojen luotettavaan valintaan."},{"text":"Esikatsele sivua varmistaaksesi, että lohko näkyy oikein julkisella sivulla."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"ACF-lohkot saatavilla lohkon lisääjässä"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: ACF-lohkoja voi tallentaa mallien sisään, mutta synkronoidut mallit voivat päivittää kaikki esiintymät. Irrota malli tai käytä tavallista mallia, kun sivu tarvitsee yksittäisen muokkauksen.</p><!-- /wp:paragraph -->';
}

function gwi_seed_seo_basics_en(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Search Engine Optimization (SEO) helps people and search engines understand your content. WordPress has built-in features, and plugins like Yoast SEO or Rank Math add more tools.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Apply basic SEO","steps":[{"text":"Write a clear, descriptive title for every post and page. Include the main topic naturally."},{"text":"Use headings in order, usually H2 for main sections and H3 for subsections."},{"text":"Add useful alt text to all images and review imported image metadata before publishing."},{"text":"Write a meta description if your SEO plugin provides a field for it."},{"text":"Use internal links and, when appropriate, the Breadcrumbs block to clarify site hierarchy."},{"text":"Keep URLs short and descriptive. Use Permalink settings to configure the structure early."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"seo-dashboard","caption":"SEO plugin dashboard for managing optimization"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Tip: Focus on helpful, well-structured content first. AI-assisted titles, excerpts, or alt text can be useful only when the site has intentionally configured a provider and an editor reviews the result.</p>
<!-- /wp:paragraph -->
BLOCKS;
}

function gwi_seed_seo_basics_fi(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Hakukoneoptimointi (SEO) auttaa ihmisiä ja hakukoneita ymmärtämään sisältösi. WordPressissa on sisäänrakennettuja ominaisuuksia, ja lisäosat kuten Yoast SEO tai Rank Math lisäävät työkaluja.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Sovella perus-SEO:ta","steps":[{"text":"Kirjoita selkeä, kuvaava otsikko jokaiselle artikkelille ja sivulle. Sisällytä pääaihe luontevasti."},{"text":"Käytä otsikoita järjestyksessä, yleensä H2 pääosioille ja H3 alaosioille."},{"text":"Lisää hyödyllinen vaihtoehtoinen teksti kaikkiin kuviin ja tarkista tuotu kuvametadata ennen julkaisua."},{"text":"Kirjoita metakuvaus, jos SEO-lisäosa tarjoaa siihen kentän."},{"text":"Käytä sisäisiä linkkejä ja tarvittaessa Murupolut-lohkoa sivuston hierarkian selkeyttämiseen."},{"text":"Pidä URL-osoitteet lyhyinä ja kuvaavina. Määritä rakenne Osoiterakenteen asetuksissa ajoissa."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"seo-dashboard","caption":"SEO-lisäosan hallintapaneeli optimoinnin hallintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Vinkki: Keskity ensin hyödylliseen ja hyvin rakennettuun sisältöön. AI-avusteiset otsikot, tiivistelmät tai alt-tekstit voivat olla hyödyllisiä vain, kun sivustolle on tarkoituksella määritetty palveluntarjoaja ja toimittaja tarkistaa lopputuloksen.</p>
<!-- /wp:paragraph -->
BLOCKS;
}

function gwi_seed_performance_caching_en(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Site speed affects user experience and search rankings. WordPress 7.0 improves image loading prioritization and classic-theme block stylesheet loading, but editors still need to manage media, layout, caching, and plugins carefully.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Improve site performance","steps":[{"text":"Use caching through your host or a trusted caching plugin when the site needs it."},{"text":"Optimize images before uploading. Use the correct dimensions, compression, and modern formats where possible."},{"text":"Avoid duplicating large hero images only to hide one version on mobile or desktop; device-hidden blocks can still exist in the page markup."},{"text":"Use a CDN to serve static assets from servers closer to visitors."},{"text":"Keep WordPress, themes, and plugins updated for security and performance fixes."},{"text":"Remove unused plugins, themes, fonts, and heavy embeds to reduce page and admin load."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"WordPress settings for performance-related options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Tip: After editing navigation overlays, galleries, fonts, or large media sections, test key pages again with PageSpeed Insights or your host performance tooling.</p>
<!-- /wp:paragraph -->
BLOCKS;
}

function gwi_seed_performance_caching_fi(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Sivuston nopeus vaikuttaa käyttökokemukseen ja hakutulossijoituksiin. WordPress 7.0 parantaa kuvien latauspriorisointia ja perinteisten teemojen lohkotyylien latausta, mutta toimittajan pitää silti hallita mediaa, asettelua, välimuistia ja lisäosia huolellisesti.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Paranna sivuston suorituskykyä","steps":[{"text":"Käytä välimuistia hostingin tai luotettavan välimuistilisäosan kautta, kun sivusto tarvitsee sitä."},{"text":"Optimoi kuvat ennen lataamista. Käytä oikeita mittoja, pakkausta ja mahdollisuuksien mukaan moderneja tiedostomuotoja."},{"text":"Vältä suurten sankarikuvien kahdentamista vain siksi, että toinen versio piilotetaan mobiilissa tai työpöydällä; laitekohtaisesti piilotetut lohkot voivat silti olla sivun koodissa."},{"text":"Käytä CDN:ää tarjoamaan staattisia resursseja lähempänä kävijöitä."},{"text":"Pidä WordPress, teemat ja lisäosat päivitettyinä turvallisuus- ja suorituskykykorjauksia varten."},{"text":"Poista käyttämättömät lisäosat, teemat, fontit ja raskaat upotukset keventääksesi sivua ja hallintaa."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"WordPress-asetukset suorituskykyyn liittyvillä vaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Vinkki: Kun muokkaat navigaation peittovalikkoja, gallerioita, fontteja tai suuria mediaosioita, testaa tärkeät sivut uudelleen PageSpeed Insightsilla tai hostingin suorituskykytyökaluilla.</p>
<!-- /wp:paragraph -->
BLOCKS;
}
