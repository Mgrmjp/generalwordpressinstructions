<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_wpml(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wpml-languages-en',
            'Configure Languages with WPML',
            'en',
            gwi_seed_wpml_languages_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wpml-kielten-maaritys-fi',
            'Määritä kielet WPML:llä',
            'fi',
            gwi_seed_wpml_languages_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wpml-translate-content-en',
            'Translate Content with WPML',
            'en',
            gwi_seed_wpml_translate_content_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wpml-sisallon-kaantaminen-fi',
            'Käännä sisältöä WPML:llä',
            'fi',
            gwi_seed_wpml_translate_content_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wpml-media-translation-en',
            'WPML Media Translation',
            'en',
            gwi_seed_wpml_media_translation_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wpml-median-kaantaminen-fi',
            'Median kääntäminen WPML:llä',
            'fi',
            gwi_seed_wpml_media_translation_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wpml-language-switcher-en',
            'Add a WPML Language Switcher',
            'en',
            gwi_seed_wpml_language_switcher_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wpml-kielenvaihtajan-lisaaminen-fi',
            'Lisää WPML-kielenvaihtaja',
            'fi',
            gwi_seed_wpml_language_switcher_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wpml-string-translation-en',
            'WPML String Translation',
            'en',
            gwi_seed_wpml_string_translation_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wpml-merkkijonojen-kaantaminen-fi',
            'Merkkijonojen kääntäminen WPML:llä',
            'fi',
            gwi_seed_wpml_string_translation_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_wpml_languages_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when WPML has been installed and the site needs a controlled language setup. WPML manages public site languages, the default language, URL format, and language switchers from WPML > Languages.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Review language setup","steps":[{"text":"Go to WPML > Languages."},{"text":"In Site Languages, confirm the default language and the enabled public languages."},{"text":"Use Add / Remove languages when the site needs another configured language."},{"text":"Use Edit Languages only when a custom language, custom flag, locale, or language code is needed."},{"text":"Review Language URL format and choose whether languages use directories, domains, or a URL parameter."},{"text":"Review language switcher sections on the same screen before testing the public site."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-languages","caption":"WPML Languages screen with site language and URL format settings"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Before translating content</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Confirm the language of existing content during setup. The default language affects source content, translation relationships, and URL behavior, so it should be settled before editors start translating many pages.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not change the default language casually after translation work has started.</li><li>Do not choose a URL format without checking how it affects existing links and redirects.</li><li>Do not create custom languages unless a built-in WPML language is not suitable.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_languages_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun WPML on asennettu ja sivuston kieliasetukset pitää tarkistaa hallitusti. WPML hallitsee julkisen sivuston kieliä, oletuskieltä, URL-muotoa ja kielenvaihtajia kohdassa WPML > Languages.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Tarkista kieliasetukset","steps":[{"text":"Mene kohtaan WPML > Languages."},{"text":"Varmista Site Languages -kohdassa oletuskieli ja käytössä olevat julkiset kielet."},{"text":"Käytä Add / Remove languages -toimintoa, kun sivusto tarvitsee toisen valmiiksi määritetyn kielen."},{"text":"Käytä Edit Languages -toimintoa vain, kun tarvitset mukautetun kielen, lipun, localen tai kielikoodin."},{"text":"Tarkista Language URL format ja valitse käytetäänkö kielille hakemistoja, verkkotunnuksia vai URL-parametria."},{"text":"Tarkista saman näkymän kielenvaihtajaosiot ennen julkisen sivuston testaamista."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-languages","caption":"WPML Languages -näkymä sivuston kielille ja URL-muodolle"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Ennen sisällön kääntämistä</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Varmista olemassa olevan sisällön kieli käyttöönoton aikana. Oletuskieli vaikuttaa lähdesisältöön, käännösten yhteyksiin ja URL-osoitteisiin, joten se kannattaa päättää ennen kuin toimittajat kääntävät paljon sivuja.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä muuta oletuskieltä kevyesti sen jälkeen, kun käännöstyö on alkanut.</li><li>Älä valitse URL-muotoa tarkistamatta vaikutusta vanhoihin linkkeihin ja uudelleenohjauksiin.</li><li>Älä luo mukautettuja kieliä, jos WPML:n valmis kieli sopii tarkoitukseen.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_translate_content_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when editors need to translate pages, posts, categories, tags, or custom taxonomies with WPML. The Translation Dashboard is the central place to select content, send it for translation, and check translation progress.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Translate pages or posts","steps":[{"text":"Go to WPML > Translation Dashboard."},{"text":"Filter or search for the pages or posts you want to translate."},{"text":"Select the source items and choose the target language."},{"text":"Choose the translation method available for the site and send the items for translation."},{"text":"Open WPML > Translations when a manual translator needs to complete assigned work."},{"text":"Return to the dashboard or the content list to confirm the translation status."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-translation-dashboard","caption":"WPML Translation Dashboard for selecting content and target languages"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Editor and taxonomy checks</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>On individual edit screens, use the WPML language box to check the source language, translation links, and translation status. For categories, tags, and custom taxonomies, use WPML > Taxonomy translation so translated posts can use translated terms.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not manually duplicate translated content without linking it through WPML.</li><li>Do not forget taxonomy terms when translated posts use categories or tags.</li><li>Do not assume a page is finished because a translation job exists. Check the translation status before publishing links.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_translate_content_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun toimittajien pitää kääntää sivuja, artikkeleita, kategorioita, asiasanoja tai mukautettuja taksonomioita WPML:llä. Translation Dashboard on keskitetty paikka sisällön valintaan, käännökseen lähettämiseen ja käännösten etenemisen tarkistamiseen.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Käännä sivuja tai artikkeleita","steps":[{"text":"Mene kohtaan WPML > Translation Dashboard."},{"text":"Suodata tai etsi sivut ja artikkelit, jotka haluat kääntää."},{"text":"Valitse lähdesisällöt ja kohdekieli."},{"text":"Valitse sivustolla käytössä oleva käännöstapa ja lähetä sisällöt käännettäväksi."},{"text":"Avaa WPML > Translations, kun manuaalisen kääntäjän pitää viimeistellä hänelle osoitetut työt."},{"text":"Palaa dashboardiin tai sisältölistaan ja varmista käännöksen tila."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-translation-dashboard","caption":"WPML Translation Dashboard sisällön ja kohdekielten valintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Editorin ja taksonomioiden tarkistus</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Yksittäisissä muokkausnäkymissä WPML:n kielilaatikosta voi tarkistaa lähdekielen, käännöslinkit ja käännöksen tilan. Kategorioille, asiasanoille ja mukautetuille taksonomioille käytetään kohtaa WPML > Taxonomy translation, jotta käännetyt artikkelit voivat käyttää käännettyjä termejä.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä kopioi käännettyä sisältöä käsin ilman, että se linkitetään WPML:ssä.</li><li>Älä unohda taksonomiatermejä, kun käännetyt artikkelit käyttävät kategorioita tai asiasanoja.</li><li>Älä oleta sivun olevan valmis vain siksi, että käännöstyö on olemassa. Tarkista käännöksen tila ennen linkkien julkaisua.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_media_translation_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when pages contain images or other media that need translated titles, captions, alt text, or descriptions. WPML can send media texts for translation along with the page content, and media settings control how existing and new media texts are handled.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Review media translation settings","steps":[{"text":"Go to WPML > Settings."},{"text":"Find the Media Translation section."},{"text":"Keep automatic detection enabled when WPML should translate image titles, captions, and alt text with page content."},{"text":"For existing media, use the duplication options only when media text should be copied to every language before translation."},{"text":"Translate the page or post from WPML > Translation Dashboard so image texts appear in the translation editor."},{"text":"Use separate language-specific image files when the image itself contains visible text or culturally specific content."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-media-settings","caption":"WPML media settings for translating image text"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>When different images are needed</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Translated alt text is enough when the same image works in every language. If the visible image must change by language, use WPML media translation support so the translated content can reference the correct language-specific file.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not translate only the page body and forget image alt text.</li><li>Do not duplicate media texts across all languages unless that matches the editorial plan.</li><li>Do not rely on alt text translation to fix text that is baked into an image.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_media_translation_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun sivuilla on kuvia tai muuta mediaa, joiden otsikot, kuvatekstit, alt-tekstit tai kuvaukset pitää kääntää. WPML voi lähettää mediatekstit käännettäväksi sivusisällön mukana, ja media-asetukset ohjaavat vanhojen ja uusien mediatekstien käsittelyä.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Tarkista median käännösasetukset","steps":[{"text":"Mene kohtaan WPML > Settings."},{"text":"Etsi Media Translation -osio."},{"text":"Pidä automaattinen tunnistus käytössä, kun WPML:n pitää kääntää kuvien otsikot, kuvatekstit ja alt-tekstit sivusisällön mukana."},{"text":"Käytä olemassa olevan median kopiointiasetuksia vain, kun mediatekstit pitää kopioida jokaiseen kieleen ennen kääntämistä."},{"text":"Käännä sivu tai artikkeli kohdasta WPML > Translation Dashboard, jotta kuvien tekstit näkyvät käännöseditorissa."},{"text":"Käytä erillisiä kielikohtaisia kuvatiedostoja, kun kuvassa itsessään on näkyvää tekstiä tai kulttuurikohtaista sisältöä."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-media-settings","caption":"WPML:n media-asetukset kuvatekstien kääntämiseen"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Milloin tarvitaan eri kuvat</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Käännetty alt-teksti riittää, kun sama kuva sopii jokaiseen kieleen. Jos näkyvän kuvan pitää vaihtua kielen mukaan, käytä WPML:n mediakäännöstukea, jotta käännetty sisältö voi viitata oikeaan kielikohtaiseen tiedostoon.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä käännä vain sivun leipätekstiä ja unohda kuvien alt-tekstejä.</li><li>Älä kopioi mediatekstejä kaikkiin kieliin, ellei se vastaa toimituksellista suunnitelmaa.</li><li>Älä yritä korjata kuvaan upotettua tekstiä pelkällä alt-tekstin käännöksellä.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_language_switcher_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when visitors need a visible way to change languages. WPML language switchers are configured from WPML > Languages and can appear in menus, widget areas, the footer, templates, or page and post content.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Add and configure a switcher","steps":[{"text":"Go to WPML > Languages."},{"text":"Scroll to the language switcher sections."},{"text":"For a menu switcher, click Add a new language switcher to a menu."},{"text":"Choose the menu, position, and whether the switcher is a dropdown or a list."},{"text":"Choose whether to show flags, native names, current-language names, and languages without translations."},{"text":"Save and test a translated page plus an untranslated page on the public site."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-language-switcher-settings","caption":"WPML language switcher settings on the Languages screen"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Other switcher locations</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Use widget, footer, template, or post-content switchers only where the site design needs them. If the site uses block templates, the WPML Language Switcher block can be added to a template or template part and then translated like other template content.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not add multiple switchers to the same area unless the design calls for it.</li><li>Do not forget to test untranslated pages, because missing-translation behavior is controlled by switcher settings.</li><li>Do not customize colors before confirming the switcher appears in the correct menu or template.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_language_switcher_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun kävijät tarvitsevat näkyvän tavan vaihtaa kieltä. WPML:n kielenvaihtajat määritetään kohdassa WPML > Languages, ja ne voivat näkyä valikoissa, widget-alueilla, alatunnisteessa, malleissa tai sivujen ja artikkelien sisällössä.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Lisää ja määritä kielenvaihtaja","steps":[{"text":"Mene kohtaan WPML > Languages."},{"text":"Vieritä kielenvaihtajaosioihin."},{"text":"Valikkokielenvaihtajaa varten klikkaa Add a new language switcher to a menu."},{"text":"Valitse valikko, sijainti ja näytetäänkö kielenvaihtaja pudotusvalikkona vai listana."},{"text":"Valitse näytetäänkö liput, alkuperäiskieliset nimet, nykyisen kielen nimet ja kielet, joilta puuttuu käännös."},{"text":"Tallenna ja testaa julkisella sivustolla sekä käännetty sivu että kääntämätön sivu."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-language-switcher-settings","caption":"WPML:n kielenvaihtaja-asetukset Languages-näkymässä"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Muut kielenvaihtajan sijainnit</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Käytä widget-, alatunniste-, malli- tai sisältökielenvaihtajia vain siellä, missä sivuston ulkoasu niitä tarvitsee. Jos sivusto käyttää lohkomalleja, WPML Language Switcher -lohkon voi lisätä malliin tai mallinosaan ja kääntää kuten muun mallisisällön.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä lisää useita kielenvaihtajia samaan alueeseen, ellei ulkoasu sitä vaadi.</li><li>Älä unohda testata kääntämättömiä sivuja, koska puuttuvan käännöksen toiminta määritetään kielenvaihtajan asetuksissa.</li><li>Älä muokkaa värejä ennen kuin olet varmistanut, että kielenvaihtaja näkyy oikeassa valikossa tai mallissa.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_string_translation_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when text is not part of a normal page, post, category, tag, or custom taxonomy. WPML String Translation is for site structure text such as the site tagline, widget titles, theme strings, plugin strings, and texts saved in admin settings.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Find and translate strings","steps":[{"text":"Confirm WPML String Translation is active."},{"text":"Go to WPML > String Translation."},{"text":"Use the search field, status filter, and domain filter to find the string."},{"text":"Open or select the string and enter the translation for the target language."},{"text":"Save the translation and refresh the public page where the string appears."},{"text":"If a settings text is missing, use Translate texts in admin screens to register admin and settings strings."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-string-translation","caption":"WPML String Translation filters and string table"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>When not to use this screen</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Do not use String Translation for normal page or post body content. Use the Translation Dashboard or edit-screen translation controls for regular content, and reserve String Translation for text that belongs to the site structure or plugin/theme settings.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not translate a string in the wrong domain when the same text appears in multiple places.</li><li>Do not scan broad admin settings unless you know which setting text is missing.</li><li>Do not forget to clear caches if the translated string does not appear immediately.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wpml_string_translation_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun teksti ei kuulu tavalliseen sivuun, artikkeliin, kategoriaan, asiasanaan tai mukautettuun taksonomiaan. WPML String Translation on tarkoitettu sivuston rakenteen teksteille, kuten sivuston kuvaukselle, widget-otsikoille, teeman teksteille, lisäosien teksteille ja hallinta-asetuksiin tallennetuille teksteille.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Etsi ja käännä merkkijonoja","steps":[{"text":"Varmista, että WPML String Translation on aktiivinen."},{"text":"Mene kohtaan WPML > String Translation."},{"text":"Etsi merkkijono hakukentän, tilasuodattimen ja domain-suodattimen avulla."},{"text":"Avaa tai valitse merkkijono ja kirjoita käännös kohdekielelle."},{"text":"Tallenna käännös ja päivitä julkinen sivu, jossa merkkijono näkyy."},{"text":"Jos asetusteksti puuttuu, rekisteröi hallinnan ja asetusten tekstejä toiminnolla Translate texts in admin screens."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"wpml-string-translation","caption":"WPML String Translation -suodattimet ja merkkijonotaulukko"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Milloin tätä näkymää ei käytetä</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Älä käytä String Translation -näkymää tavallisen sivu- tai artikkelisisällön kääntämiseen. Käytä normaaliin sisältöön Translation Dashboardia tai muokkausnäkymän käännösohjaimia, ja pidä String Translation sivuston rakenteen sekä lisäosa- ja teema-asetusten tekstejä varten.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä käännä merkkijonoa väärässä domainissa, jos sama teksti esiintyy useassa paikassa.</li><li>Älä skannaa laajoja hallinta-asetuksia, ellet tiedä mikä asetusteksti puuttuu.</li><li>Älä unohda tyhjentää välimuisteja, jos käännetty merkkijono ei näy heti.</li></ul>
<!-- /wp:list -->
GUIDE;
}
