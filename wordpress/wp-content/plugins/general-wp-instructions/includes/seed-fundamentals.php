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
    return '<!-- wp:paragraph --><p>The Dashboard is your WordPress home base. Use it to see site activity, quick links, and important updates at a glance.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Navigate the Dashboard","steps":[{"text":"Log in to WordPress and you will land on the Dashboard automatically."},{"text":"Use the left sidebar menu to access Posts, Pages, Media, and other sections."},{"text":"Check the At a Glance widget for a quick count of your published content."},{"text":"Review the Activity widget for recent posts and comments."},{"text":"Use the Screen Options tab to show or hide dashboard widgets."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"dashboard-home","caption":"The WordPress Dashboard with key areas highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: You can drag and drop dashboard widgets to rearrange them to suit your workflow.</p><!-- /wp:paragraph -->';
}

function gwi_seed_dashboard_overview_fi(): string
{
    return '<!-- wp:paragraph --><p>Hallintapaneeli on WordPressin kotisivu. Käytä sitä sivuston toiminnan, pikalinkkien ja tärkeiden päivitysten tarkasteluun.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Liiku hallintapaneelissa","steps":[{"text":"Kirjaudu sisään WordPressiin ja päädyt hallintapaneeliin automaattisesti."},{"text":"Käytä vasenta sivuvalikkoa päästäksesi artikkeleihin, sivuihin, mediaan ja muihin osioihin."},{"text":"Tarkista Yleiskatsaus-widget julkaistun sisällön nopeasta määrästä."},{"text":"Katso Toiminta-widgetistä viimeisimmät artikkelit ja kommentit."},{"text":"Käytä Näyttöasetukset-välilehteä hallintapaneelin widgettien näyttämiseen tai piilottamiseen."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"dashboard-home","caption":"WordPress-hallintapaneeli keskeisalueet korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Voit vetää ja pudottaa hallintapaneelin widgettejä järjestääksesi ne työskentelytapasi mukaan.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_posts_en(): string
{
    return '<!-- wp:paragraph --><p>Posts are time-based content entries, typically used for blog articles, news, and updates. They appear in reverse chronological order on your site.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Create a new post","steps":[{"text":"Go to Posts and click Add New."},{"text":"Write a clear, descriptive title in the title field."},{"text":"Add your content using blocks in the editor below the title."},{"text":"Assign a category from the Categories panel on the right."},{"text":"Add relevant tags in the Tags panel."},{"text":"Set a featured image using the Featured Image panel."},{"text":"Click Publish when your post is ready."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"posts-list","caption":"The Posts list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use the Preview button to check how your post looks before publishing.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_posts_fi(): string
{
    return '<!-- wp:paragraph --><p>Artikkelit ovat aikaperusteisia sisältöjulkaisuja, joita käytetään tyypillisesti blogikirjoituksiin, uutisiin ja päivityksiin. Ne näkyvät sivustollasi uusimmasta vanhimpaan järjestettyinä.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Luo uusi artikkeli","steps":[{"text":"Mene Artikkelit-valikkoon ja klikkaa Lisää uusi."},{"text":"Kirjoita selkeä ja kuvaava otsikko otsikkokenttään."},{"text":"Lisää sisältöäsi käyttämällä lohkoja editorissa otsikon alla."},{"text":"Valitse kategoria Kategoriat-paneelista oikealla."},{"text":"Lisää relevantit asiasanat Asiasanat-paneelissa."},{"text":"Aseta artikkelikuva Artikkelikuva-paneelista."},{"text":"Klikkaa Julkaise kun artikkelisi on valmis."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"posts-list","caption":"Artikkelit-lista Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Esikatsele-painiketta tarkistaaksesi artikkelin ulkoasu ennen julkaisua.</p><!-- /wp:paragraph -->';
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
        . '<!-- wp:general-wp-instructions/step-list {"title":"Create a new page","steps":[{"text":"Go to Pages and click Add New."},{"text":"Write a clear title for your page."},{"text":"Add content using blocks in the editor."},{"text":"Set a parent page in the Page Attributes panel if you want a hierarchy."},{"text":"Choose a template if your theme offers page templates."},{"text":"Click Publish when your page is ready."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"pages-list-add-new","caption":"Pages list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Pages can use different templates provided by your theme, such as full-width or sidebar layouts.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_pages_fi(): string
{
    return '<!-- wp:paragraph --><p>Sivut ovat staattista sisältöä, joka ei ole aikariippuvaista. Käytä niitä Tietoa, Yhteystiedot, Palvelut ja muihin sivustosi pysyviin osioihin.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Luo uusi sivu","steps":[{"text":"Mene Sivut-valikkoon ja klikkaa Lisää uusi."},{"text":"Kirjoita selkeä otsikko sivullesi."},{"text":"Lisää sisältöä käyttämällä lohkoja editorissa."},{"text":"Aseta yläsivu Sivun asetukset -paneelissa jos haluat hierarkian."},{"text":"Valitse malli tarjoaako teemasi sivumalleja."},{"text":"Klikkaa Julkaise kun sivusi on valmis."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"pages-list-add-new","caption":"Sivulista Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Sivut voivat käyttää eri malleja, joita teemasi tarjoaa, kuten täyslevyisiä tai sivupalkillisia asetteluja.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_library_en(): string
{
    return '<!-- wp:paragraph --><p>The Media Library stores all your uploaded images, documents, videos, and audio files. You can manage, edit, and reuse media from this central location.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use the Media Library","steps":[{"text":"Go to Media to see all uploaded files."},{"text":"Click Add New to upload new files by dragging and dropping or selecting files."},{"text":"Click any file to view its details, edit alt text, and get the file URL."},{"text":"Use the list or grid view toggle to switch between display modes."},{"text":"Filter by media type using the dropdown above the media grid."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Media Library with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Always add descriptive alt text to images for accessibility and SEO purposes.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_library_fi(): string
{
    return '<!-- wp:paragraph --><p>Mediakirjasto tallentaa kaikki ladattujen kuviesi, dokumenttiesi, videosi ja äänitiedostosi. Voit hallinnoida, muokata ja käyttää uudelleen mediaa tästä keskitetystä sijainnista.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä mediakirjastoa","steps":[{"text":"Siirry Media-valikkoon nähdäksesi kaikki ladatut tiedostot."},{"text":"Klikkaa Lisää uusi ladataksesi uusia tiedostoja vetämällä ja pudottamalla tai valitsemalla tiedostoja."},{"text":"Klikkaa mitä tahansa tiedostoa nähdäksesi sen tiedot, muokataksesi vaihtoehtoista tekstiä ja saadaksesi tiedoston URL-osoitteen."},{"text":"Käytä lista- tai ruudukkonäkymän vaihtoa vaihtaaksesi näyttötiloja."},{"text":"Suodata mediatyypin mukaan käyttämällä pudotusvalikkoa mediaruudukon yläpuolella."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Mediakirjasto Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Lisää aina kuvaileva vaihtoehtoinen teksti kuviin saavutettavuuden ja SEO:n parantamiseksi.</p><!-- /wp:paragraph -->';
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
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallinnoi kommentteja","steps":[{"text":"Mene Kommentit nähdäksesi kaikki kommentit sivustollasi."},{"text":"Käytä suodatinlinkkejä ylhäällä nähdäksesi Odottavat, Hyväksytyt, Roskapostit tai Roskakori-kommentit."},{"text":"Vie hiiri kommentin päälle nähdäksesi toimintolinkit: Hyväksy, Vastaa, Muokkaa, Roskaposti tai Roskakori."},{"text":"Klikkaa Vastaa vastataksesi suoraan kommenttiin."},{"text":"Tyhjennä Roskakori säännöllisesti pitääksesi tietokantasi siistinä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"comments-list","caption":"Kommenttien hallintasivu suodatinvaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Ota kommenttien moderointi käyttöön Asetuksissa, jotta uudet kommentit pidätetään hyväksyntää varten ennen julkaisua.</p><!-- /wp:paragraph -->';
}
