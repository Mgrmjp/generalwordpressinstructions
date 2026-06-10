<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_fundamentals(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'dashboard-overview-en',
            'Dashboard Overview',
            'en',
            gwi_seed_dashboard_overview_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'hallintapaneelin-yleiskatsaus-fi',
            'Hallintapaneelin yleiskatsaus',
            'fi',
            gwi_seed_dashboard_overview_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'creating-posts-en',
            'Creating and Editing Posts',
            'en',
            gwi_seed_creating_posts_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'artikkelien-luominen-ja-muokkaus-fi',
            'Artikkelien luominen ja muokkaus',
            'fi',
            gwi_seed_creating_posts_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'categories-tags-en',
            'Categories and Tags',
            'en',
            gwi_seed_categories_tags_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'kategoriat-ja-asiasanat-fi',
            'Kategoriat ja asiasanat',
            'fi',
            gwi_seed_categories_tags_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'creating-pages-en',
            'Creating and Editing Pages',
            'en',
            gwi_seed_creating_pages_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'sivujen-luominen-ja-muokkaus-fi',
            'Sivujen luominen ja muokkaus',
            'fi',
            gwi_seed_creating_pages_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'media-library-en',
            'Media Library',
            'en',
            gwi_seed_media_library_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'mediakirjasto-fi',
            'Mediakirjasto',
            'fi',
            gwi_seed_media_library_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'managing-comments-en',
            'Managing Comments',
            'en',
            gwi_seed_managing_comments_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'kommenttien-hallinta-fi',
            'Kommenttien hallinta',
            'fi',
            gwi_seed_managing_comments_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_dashboard_overview_en(): string
{
    return '<!-- wp:paragraph --><p>The Dashboard is your WordPress home base. In WordPress 7.0 it has a refreshed admin style, smoother navigation, a Command Palette shortcut in the admin bar, and new entry points for fonts and supported service connections.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Navigate the Dashboard","steps":[{"text":"Log in to WordPress and you will land on the Dashboard automatically."},{"text":"Use the left sidebar menu to access Posts, Pages, Media, Appearance, Plugins, Users, Tools, and Settings."},{"text":"Use Ctrl+K or Cmd+K, or the Command Palette icon in the admin bar, to jump quickly to admin screens and editor actions."},{"text":"Check the At a Glance widget for a quick count of published content."},{"text":"Review the Activity widget for recent posts and comments."},{"text":"Use the Screen Options tab to show or hide dashboard widgets."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"dashboard-home","caption":"The WordPress Dashboard with key areas highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Appearance > Fonts opens the WordPress 7.0 Font Library. Settings > Connectors appears when the site supports managed external service or AI-provider connections.</p><!-- /wp:paragraph -->';
}

function gwi_seed_dashboard_overview_fi(): string
{
    return '<!-- wp:paragraph --><p>Hallintapaneeli on WordPressin kotinäkymä. WordPress 7.0:ssa hallinnan ulkoasu on uudistettu, siirtymät ovat sulavampia, hallintapalkissa on komentopaletin pikakuvake ja fonteille sekä tuetuille palveluyhteyksille on uudet sisäänkäynnit.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Liiku hallintapaneelissa","steps":[{"text":"Kirjaudu sisään WordPressiin ja päädyt hallintapaneeliin automaattisesti."},{"text":"Käytä vasenta sivuvalikkoa päästäksesi artikkeleihin, sivuihin, mediaan, ulkoasuun, lisäosiin, käyttäjiin, työkaluihin ja asetuksiin."},{"text":"Käytä Ctrl+K- tai Cmd+K-pikanäppäintä tai hallintapalkin komentopaletin kuvaketta siirtyäksesi nopeasti näkymiin ja editorin toimintoihin."},{"text":"Tarkista Yleiskatsaus-widget julkaistun sisällön nopeasta määrästä."},{"text":"Katso Toiminta-widgetistä viimeisimmät artikkelit ja kommentit."},{"text":"Käytä Näyttöasetukset-välilehteä hallintapaneelin widgettien näyttämiseen tai piilottamiseen."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"dashboard-home","caption":"WordPress-hallintapaneeli keskeisalueet korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Ulkoasu > Fontit avaa WordPress 7.0:n Fonttikirjaston. Asetukset > Connectors näkyy, kun sivusto tukee hallittuja ulkoisia palvelu- tai AI-palveluyhteyksiä.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_posts_en(): string
{
    return '<!-- wp:paragraph --><p>Posts are time-based content entries, typically used for blog articles, news, and updates. They appear in reverse chronological order on your site.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Create a new post","steps":[{"text":"Go to Posts and click Add New."},{"text":"Write a clear, descriptive title in the title field."},{"text":"Add content using blocks, media, and patterns in the editor below the title."},{"text":"Assign a category from the Categories panel on the right."},{"text":"Add relevant tags in the Tags panel."},{"text":"Set a featured image using the Featured Image panel and review its alt text in the Media Library."},{"text":"Preview the post, then click Publish when it is ready."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"posts-list","caption":"The Posts list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: WordPress 7.0 includes visual revisions, so you can compare saved versions more clearly before restoring an older draft or published version.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_posts_fi(): string
{
    return '<!-- wp:paragraph --><p>Artikkelit ovat aikaperusteisia sisältöjulkaisuja, joita käytetään tyypillisesti blogikirjoituksiin, uutisiin ja päivityksiin. Ne näkyvät sivustollasi uusimmasta vanhimpaan järjestettyinä.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Luo uusi artikkeli","steps":[{"text":"Mene Artikkelit-valikkoon ja klikkaa Lisää uusi."},{"text":"Kirjoita selkeä ja kuvaava otsikko otsikkokenttään."},{"text":"Lisää sisältöä lohkoilla, medialla ja malleilla otsikon alla olevassa editorissa."},{"text":"Valitse kategoria Kategoriat-paneelista oikealla."},{"text":"Lisää sopivat asiasanat Asiasanat-paneelissa."},{"text":"Aseta artikkelikuva Artikkelikuva-paneelista ja tarkista sen vaihtoehtoinen teksti Mediakirjastossa."},{"text":"Esikatsele artikkeli ja klikkaa Julkaise, kun se on valmis."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"posts-list","caption":"Artikkelit-lista Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: WordPress 7.0 sisältää visuaaliset versiot, joiden avulla tallennettuja versioita voi vertailla selkeämmin ennen vanhemman luonnoksen tai julkaistun version palauttamista.</p><!-- /wp:paragraph -->';
}

function gwi_seed_categories_tags_en(): string
{
    return '<!-- wp:paragraph --><p>Categories and tags help organize your content and make it easier for visitors to find related posts. Categories are broad groupings, while tags are specific keywords.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Manage categories and tags","steps":[{"text":"Go to Posts to see the Categories and Tags submenus."},{"text":"Click Categories to add, edit, or delete categories."},{"text":"Enter a name and optional slug for new categories."},{"text":"Use parent categories to create a hierarchy."},{"text":"Click Tags to add specific keywords to your content."},{"text":"When editing a post, assign categories and tags from the right sidebar panels."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"post-categories","caption":"Categories management page with Add New form"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use categories for broad topics and tags for specific details. A post can have multiple tags but usually one or two categories.</p><!-- /wp:paragraph -->';
}

function gwi_seed_categories_tags_fi(): string
{
    return '<!-- wp:paragraph --><p>Kategoriat ja asiasanat auttavat järjestämään sisältöäsi ja helpottavat kävijöitä löytämään liittyviä artikkeleita. Kategoriat ovat laajoja ryhmittelyjä, kun taas asiasanat ovat tarkkoja avainsanoja.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallinnoi kategorioita ja asiasanoja","steps":[{"text":"Mene Artikkelit-valikkoon nähdäksesi Kategoriat ja Asiasanat -alivalikot."},{"text":"Klikkaa Kategoriat lisätäksesi, muokataksesi tai poistaaksesi kategorioita."},{"text":"Syötä nimi ja valinnainen polkutunnus uusille kategorioille."},{"text":"Käytä yläkategorioita luodaksesi hierarkian."},{"text":"Klikkaa Asiasanat lisätäksesi tarkkoja avainsanoja sisältöösi."},{"text":"Muokatessasi artikkelia, aseta kategoriat ja asiasanat oikean sivupaneelin paneeleista."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"post-categories","caption":"Kategorioiden hallintasivu Lisää uusi -lomakkeella"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä kategorioita laajoille aiheille ja asiasanoja tarkoille yksityiskohdille. Artikkelilla voi olla useita asiasanoja mutta yleensä yksi tai kaksi kategoriaa.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_pages_en(): string
{
    return '<!-- wp:paragraph --><p>Pages are static content that is not time-dependent. Use them for About, Contact, Services, and other permanent sections of your site.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Create a new page","steps":[{"text":"Go to Pages and click Add New."},{"text":"Write a clear title for your page."},{"text":"Add content using blocks, media, and patterns in the editor."},{"text":"Use List View before publishing to check heading order, nested groups, and pattern structure."},{"text":"Set a parent page in the Page Attributes panel if you want a hierarchy."},{"text":"Choose a template if your theme offers page templates, then preview and publish."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"pages-list-add-new","caption":"Pages list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: In WordPress 7.0, patterns can act as a single editable unit. If you only need text or image changes, edit the available fields first before detaching or restructuring the pattern.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_pages_fi(): string
{
    return '<!-- wp:paragraph --><p>Sivut ovat staattista sisältöä, joka ei ole aikariippuvaista. Käytä niitä Tietoa, Yhteystiedot, Palvelut ja muihin sivustosi pysyviin osioihin.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Luo uusi sivu","steps":[{"text":"Mene Sivut-valikkoon ja klikkaa Lisää uusi."},{"text":"Kirjoita selkeä otsikko sivullesi."},{"text":"Lisää sisältöä lohkoilla, medialla ja malleilla editorissa."},{"text":"Käytä Listanäkymää ennen julkaisua tarkistaaksesi otsikkotasot, sisäkkäiset ryhmät ja mallirakenteen."},{"text":"Aseta yläsivu Sivun asetukset -paneelissa, jos haluat hierarkian."},{"text":"Valitse sivupohja, jos teemasi tarjoaa sivupohjia, esikatsele ja julkaise."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"pages-list-add-new","caption":"Sivulista Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: WordPress 7.0:ssa mallit voivat toimia yhtenä muokattavana kokonaisuutena. Jos tarvitset vain teksti- tai kuvamuutoksia, muokkaa ensin tarjolla olevia kenttiä ennen mallin irrottamista tai rakenteen muuttamista.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_library_en(): string
{
    return '<!-- wp:paragraph --><p>The Media Library stores all your uploaded images, documents, videos, and audio files. You can manage, edit, and reuse media from this central location.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use the Media Library","steps":[{"text":"Go to Media to see all uploaded files."},{"text":"Click Add New to upload new files by dragging and dropping or selecting files."},{"text":"Click any file to view its details, edit alt text, copy the file URL, or replace metadata."},{"text":"Use the list or grid view toggle to switch between display modes."},{"text":"Filter by media type using the dropdown above the media grid."},{"text":"Review alt text even when WordPress imports it from image metadata."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Media Library with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use galleries when several images belong together. WordPress 7.0 galleries can use an enlarged lightbox/slideshow experience when configured from the Gallery block.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_library_fi(): string
{
    return '<!-- wp:paragraph --><p>Mediakirjasto tallentaa kaikki ladatut kuvat, dokumentit, videot ja äänitiedostot. Voit hallinnoida, muokata ja käyttää mediaa uudelleen tästä keskitetystä sijainnista.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä mediakirjastoa","steps":[{"text":"Siirry Media-valikkoon nähdäksesi kaikki ladatut tiedostot."},{"text":"Klikkaa Lisää uusi ladataksesi uusia tiedostoja vetämällä ja pudottamalla tai valitsemalla tiedostoja."},{"text":"Klikkaa tiedostoa nähdäksesi sen tiedot, muokataksesi vaihtoehtoista tekstiä, kopioidaksesi URL-osoitteen tai korjataksesi metatietoja."},{"text":"Käytä lista- tai ruudukkonäkymän vaihtoa vaihtaaksesi näyttötiloja."},{"text":"Suodata mediatyypin mukaan käyttämällä pudotusvalikkoa mediaruudukon yläpuolella."},{"text":"Tarkista vaihtoehtoinen teksti myös silloin, kun WordPress tuo sen kuvan metadatasta."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Mediakirjasto Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä gallerioita, kun useat kuvat kuuluvat yhteen. WordPress 7.0:n gallerioissa voi käyttää suurennettua valolaatikko- tai diaesityskokemusta, kun se määritetään Galleria-lohkosta.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_comments_en(): string
{
    return '<!-- wp:paragraph --><p>Comments allow visitors to interact with your content. Managing comments includes approving, replying, editing, and spam filtering.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Manage comments","steps":[{"text":"Go to Comments to see all comments across your site."},{"text":"Use the filter links at the top to view Pending, Approved, Spam, or Trash comments."},{"text":"Hover over a comment to see action links: Approve, Reply, Edit, Spam, or Trash."},{"text":"Click Reply to respond directly to a comment."},{"text":"Empty the Trash regularly to keep your database clean."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"comments-list","caption":"Comments management page with filter options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Enable comment moderation in Settings to hold new comments for approval before they appear publicly.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_comments_fi(): string
{
    return '<!-- wp:paragraph --><p>Kommentit mahdollistavat kävijöiden vuorovaikutuksen sisältösi kanssa. Kommenttien hallintaan kuuluu hyväksyminen, vastaaminen, muokkaus ja roskapostisuodatus.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallinnoi kommentteja","steps":[{"text":"Siirry Kommentit-valikkoon nähdäksesi kaikki kommentit sivustollasi."},{"text":"Käytä suodatinlinkkejä ylhäällä nähdäksesi Odottavat, Hyväksytyt, Roskapostit tai Roskakori-kommentit."},{"text":"Vie hiiri kommentin päälle nähdäksesi toimintolinkit: Hyväksy, Vastaa, Muokkaa, Roskaposti tai Roskakori."},{"text":"Klikkaa Vastaa vastataksesi suoraan kommenttiin."},{"text":"Tyhjennä Roskakori säännöllisesti pitääksesi tietokantasi siistinä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"comments-list","caption":"Kommenttien hallintasivu suodatinvaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Ota kommenttien moderointi käyttöön Asetuksissa, jotta uudet kommentit pidätetään hyväksyntää varten ennen julkaisua.</p><!-- /wp:paragraph -->';
}
