<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_polylang(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'polylang-languages-en',
            'Configure Languages with Polylang',
            'en',
            gwi_seed_polylang_languages_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'polylang-kielten-maaritys-fi',
            'Määritä kielet Polylangilla',
            'fi',
            gwi_seed_polylang_languages_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'polylang-translate-content-en',
            'Translate Content with Polylang',
            'en',
            gwi_seed_polylang_translate_content_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'sisallon-kaantaminen-polylangilla-fi',
            'Käännä sisältöä Polylangilla',
            'fi',
            gwi_seed_polylang_translate_content_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'polylang-media-translations-en',
            'Polylang Media Translations',
            'en',
            gwi_seed_polylang_media_translations_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'median-kaannokset-polylangilla-fi',
            'Median käännökset Polylangilla',
            'fi',
            gwi_seed_polylang_media_translations_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'polylang-language-switcher-en',
            'Add a Polylang Language Switcher',
            'en',
            gwi_seed_polylang_language_switcher_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'kielenvaihtajan-lisaaminen-polylangilla-fi',
            'Lisää Polylang-kielenvaihtaja',
            'fi',
            gwi_seed_polylang_language_switcher_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_polylang_languages_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when a WordPress site needs more than one language. Polylang manages languages from Languages > Languages. Each language has a full name, WordPress locale, language code, text direction, flag, and order. These settings affect admin labels, frontend URLs, language switching, and which WordPress translation files are loaded.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Add and review languages","steps":[{"text":"Go to Languages > Languages."},{"text":"Under Add new language, choose a language from the list when possible."},{"text":"Check the Full name, Locale, Language code, Text direction, Flag, and Order fields before saving."},{"text":"Click Add new language."},{"text":"Repeat for every public language the site will use."},{"text":"In the languages table, check the star icon. The first language you add becomes the default language, and the star lets an administrator change the default later."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-languages","caption":"Polylang Languages screen with the add-language form and language table"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Before publishing translated content</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Assign a language to existing pages, posts, categories, and tags. Content without a language can disappear from the public site language views. Changing a language code later can change URLs, so choose stable language codes before launching.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not use a country flag as the only source of truth. The locale and language code must match the content language.</li><li>Do not change the language code after publishing unless you also plan redirects.</li><li>Do not assume the default language change updates old content. Existing content keeps the language already assigned to it.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_languages_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun WordPress-sivusto tarvitsee useamman kuin yhden kielen. Polylang hallitsee kieliä kohdassa Languages > Languages. Jokaisella kielellä on koko nimi, WordPress-locale, kielikoodi, tekstin suunta, lippu ja järjestys. Nämä asetukset vaikuttavat hallinnan nimikkeisiin, julkisen sivuston URL-osoitteisiin, kielenvaihtajaan ja siihen, mitä WordPressin käännöstiedostoja ladataan.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Lisää ja tarkista kielet","steps":[{"text":"Mene kohtaan Languages > Languages."},{"text":"Valitse Add new language -kohdassa kieli listasta, jos se on saatavilla."},{"text":"Tarkista Full name, Locale, Language code, Text direction, Flag ja Order ennen tallennusta."},{"text":"Klikkaa Add new language."},{"text":"Toista sama jokaiselle julkiselle kielelle, jota sivusto käyttää."},{"text":"Tarkista kielitaulukosta tähtikuvake. Ensimmäisestä lisätystä kielestä tulee oletuskieli, ja tähdellä pääkäyttäjä voi myöhemmin vaihtaa oletuskielen."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-languages","caption":"Polylangin Languages-näkymä uuden kielen lomakkeella ja kielitaulukolla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Ennen käännetyn sisällön julkaisua</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Määritä kieli olemassa oleville sivuille, artikkeleille, kategorioille ja asiasanoille. Sisältö, jolla ei ole kieltä, voi jäädä näkymättä julkisen sivuston kielinäkymissä. Kielikoodin muuttaminen myöhemmin voi muuttaa URL-osoitteita, joten valitse pysyvät kielikoodit ennen julkaisua.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä käytä lippua ainoana totuutena. Localen ja kielikoodin pitää vastata sisällön kieltä.</li><li>Älä muuta kielikoodia julkaisun jälkeen, ellei myös uudelleenohjauksia ole suunniteltu.</li><li>Älä oleta oletuskielen vaihdon muuttavan vanhaa sisältöä. Olemassa oleva sisältö säilyttää sille määritetyn kielen.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_translate_content_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when you need to create or connect translated pages, posts, categories, or tags. Polylang adds language controls to the editor and language columns to the content list tables.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Translate a page or post","steps":[{"text":"Open Pages or Posts and edit the source item."},{"text":"In the editor language panel, choose the correct language for the source content."},{"text":"Use the plus icon for another language to create a new translation, or use the translation field to link an existing item."},{"text":"Translate the title, content, excerpt, SEO fields, images, categories, and any custom fields that appear on the edit screen."},{"text":"Publish or update the translated item."},{"text":"Return to the content list and use the language columns to confirm the translation link exists."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-page-language-panel","caption":"Polylang language panel on a page edit screen"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-content-language-columns","caption":"Pages list with Polylang language columns"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>List filters and partial translations</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The language filter in the admin list can show only one language or all languages. It is not mandatory to translate every item immediately, but untranslated content will not have a matching translation target in the language switcher.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not duplicate a page manually and leave it unlinked. Use the Polylang translation controls so the language switcher knows the relationship.</li><li>Do not forget categories and tags. They also need language assignments and translations when used on translated posts.</li><li>Do not leave existing content without language assignment after enabling Polylang.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_translate_content_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun sinun pitää luoda tai yhdistää sivujen, artikkelien, kategorioiden tai asiasanojen käännöksiä. Polylang lisää editoriin kielivalinnat ja sisältölistoihin kielisarakkeet.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Käännä sivu tai artikkeli","steps":[{"text":"Avaa Sivut tai Artikkelit ja muokkaa lähdesisältöä."},{"text":"Valitse editorin kielipaneelissa lähdesisällölle oikea kieli."},{"text":"Luo uusi käännös toisen kielen plus-kuvakkeesta tai yhdistä olemassa oleva sisältö käännöskentän avulla."},{"text":"Käännä otsikko, sisältö, ote, SEO-kentät, kuvat, kategoriat ja muut muokkausnäkymässä näkyvät mukautetut kentät."},{"text":"Julkaise tai päivitä käännetty sisältö."},{"text":"Palaa sisältölistaan ja varmista kielisarakkeista, että käännöslinkki on olemassa."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-page-language-panel","caption":"Polylangin kielipaneeli sivun muokkausnäkymässä"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-content-language-columns","caption":"Sivulista Polylangin kielisarakkeilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Listasuodattimet ja osittaiset käännökset</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hallinnan listan kielisuodatin voi näyttää vain yhden kielen tai kaikki kielet. Kaikkea sisältöä ei ole pakko kääntää heti, mutta kääntämättömällä sisällöllä ei ole vastaavaa käännöskohdetta kielenvaihtajassa.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä kopioi sivua käsin ja jätä sitä yhdistämättä. Käytä Polylangin käännösohjaimia, jotta kielenvaihtaja tunnistaa yhteyden.</li><li>Älä unohda kategorioita ja asiasanoja. Myös niille pitää määrittää kieli ja käännökset, kun niitä käytetään käännetyissä artikkeleissa.</li><li>Älä jätä vanhaa sisältöä ilman kielimääritystä Polylangin käyttöönoton jälkeen.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_media_translations_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this before deciding how media should behave on a multilingual site. Polylang media translation is for the text attached to a media item, such as title, caption, alt text, and description. It does not duplicate the file itself.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Choose the media translation approach","steps":[{"text":"Go to Languages > Settings."},{"text":"Find the Media module."},{"text":"Leave the Media module deactivated if the same files and attachment text can be used across languages."},{"text":"Activate the Media module only when titles, captions, alternative text, or descriptions need separate translations."},{"text":"If the module is active, go to Media > Library and assign a language to media items that need translated attachment text."},{"text":"When an image contains visible text in a specific language, upload a separate file for each language instead of relying on attachment-text translation."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-media-settings","caption":"Polylang Media module in Languages settings"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Edit a translated post or page and open the media picker. If media translations are enabled, the Media Library is filtered by the content language. Use Show all languages when you need to audit what is available.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not enable the Media module just because the site is multilingual. Enable it only when attachment text needs translation.</li><li>Do not expect Polylang to connect two different image files as translations of each other.</li><li>Do not change the media strategy casually after editors have already uploaded many files.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_media_translations_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta ennen kuin päätät, miten median pitää toimia monikielisellä sivustolla. Polylangin mediakäännös koskee mediatiedostoon liitettyä tekstiä, kuten otsikkoa, kuvatekstiä, vaihtoehtoista tekstiä ja kuvausta. Se ei monista itse tiedostoa.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Valitse median käännöstapa","steps":[{"text":"Mene kohtaan Languages > Settings."},{"text":"Etsi Media-moduuli."},{"text":"Pidä Media-moduuli pois käytöstä, jos samat tiedostot ja liitetekstit sopivat kaikkiin kieliin."},{"text":"Ota Media-moduuli käyttöön vain, jos otsikot, kuvatekstit, vaihtoehtoiset tekstit tai kuvaukset tarvitsevat omat käännökset."},{"text":"Jos moduuli on käytössä, mene kohtaan Media > Library ja määritä kieli mediatiedostoille, joiden liitetekstit pitää kääntää."},{"text":"Jos kuvassa on näkyvää tietyn kielen tekstiä, lataa erillinen tiedosto jokaiselle kielelle sen sijaan, että käyttäisit vain liitetekstien käännöksiä."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-media-settings","caption":"Polylangin Media-moduuli Languages-asetuksissa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Muokkaa käännettyä artikkelia tai sivua ja avaa mediavalitsin. Jos mediakäännökset ovat käytössä, mediakirjasto suodattuu sisällön kielen mukaan. Käytä Show all languages -valintaa, kun haluat tarkistaa kaiken saatavilla olevan median.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä ota Media-moduulia käyttöön vain siksi, että sivusto on monikielinen. Ota se käyttöön vain, kun liitetekstit pitää kääntää.</li><li>Älä odota Polylangin yhdistävän kahta eri kuvatiedostoa toistensa käännöksiksi.</li><li>Älä muuta mediastrategiaa kevyesti sen jälkeen, kun toimittajat ovat jo ladanneet paljon tiedostoja.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_language_switcher_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when visitors need a visible way to move between language versions. The language switcher creates links to translations of the current page. If the current page is not translated, Polylang can link to the language home page or hide that language depending on the switcher options.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Add a language switcher to a classic menu","steps":[{"text":"Go to Appearance > Menus."},{"text":"Create one menu per language, such as Main menu English and Main menu Finnish."},{"text":"Add the pages for the matching language to each menu."},{"text":"In Menu Settings, assign each menu to the theme location for the matching language."},{"text":"Open the Language switcher box, select Languages, and click Add to Menu."},{"text":"Configure whether the switcher shows language names, flags, dropdown behavior, the current language, or languages without a translation, then save the menu."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-language-switcher-menu","caption":"Polylang language switcher box in the classic menu editor"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Open a translated page on the public site and use the switcher. It should take you to the matching translation when one exists. Test an untranslated page too, because that is where hide or front-page fallback settings are easiest to spot.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not build one mixed-language menu unless that is a deliberate design choice. Separate menus per language are easier to maintain.</li><li>If the Language switcher box is missing in Appearance > Menus, check Screen Options.</li><li>Do not assume the switcher appears before the site has published content in the target languages.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_polylang_language_switcher_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun kävijät tarvitsevat näkyvän tavan siirtyä kieliversioiden välillä. Kielenvaihtaja luo linkkejä nykyisen sivun käännöksiin. Jos nykyistä sivua ei ole käännetty, Polylang voi ohjata kielen etusivulle tai piilottaa kyseisen kielen kielenvaihtajan asetuksista riippuen.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Lisää kielenvaihtaja perinteiseen valikkoon","steps":[{"text":"Mene kohtaan Ulkoasu > Valikot."},{"text":"Luo yksi valikko jokaista kieltä varten, esimerkiksi Päävalikko suomi ja Main menu English."},{"text":"Lisää kuhunkin valikkoon kyseisen kielen sivut."},{"text":"Määritä Menu Settings -kohdassa jokainen valikko sitä vastaavaan teeman kielisijaintiin."},{"text":"Avaa Language switcher -laatikko, valitse Languages ja klikkaa Add to Menu."},{"text":"Määritä näytetäänkö kielten nimet, liput, pudotusvalikko, nykyinen kieli tai kielet ilman käännöstä, ja tallenna valikko."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"polylang-language-switcher-menu","caption":"Polylangin kielenvaihtajalaatikko perinteisessä valikkoeditorissa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Avaa käännetty sivu julkisella sivustolla ja käytä kielenvaihtajaa. Sen pitäisi viedä vastaavaan käännökseen, kun sellainen on olemassa. Testaa myös kääntämätön sivu, koska piilotus- tai etusivulle ohjaavat asetukset näkyvät helpoimmin siinä tilanteessa.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä rakenna yhtä sekakielistä valikkoa, ellei se ole tarkoituksellinen suunnittelupäätös. Erilliset kielikohtaiset valikot ovat helpompia ylläpitää.</li><li>Jos Language switcher -laatikko puuttuu kohdasta Ulkoasu > Valikot, tarkista Screen Options.</li><li>Älä oleta kielenvaihtajan näkyvän ennen kuin sivustolla on julkaistua sisältöä kohdekielillä.</li></ul>
<!-- /wp:list -->
GUIDE;
}
