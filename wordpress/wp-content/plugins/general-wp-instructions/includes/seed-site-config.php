<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_site_config(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'creating-menus-en',
            'Navigation Menus',
            'en',
            gwi_seed_creating_menus_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'valikkojen-luominen-fi',
            'Valikkojen luominen',
            'fi',
            gwi_seed_creating_menus_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'managing-users-en',
            'Managing Users',
            'en',
            gwi_seed_managing_users_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'kayttajien-hallinta-fi',
            'Käyttäjien hallinta',
            'fi',
            gwi_seed_managing_users_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'profile-admin-color-scheme-en',
            'Change Your Admin Color Scheme',
            'en',
            gwi_seed_profile_admin_color_scheme_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'hallintapaneelin-variteeman-vaihto-fi',
            'Vaihda hallintapaneelin väriteema',
            'fi',
            gwi_seed_profile_admin_color_scheme_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'wordpress-settings-en',
            'WordPress Settings',
            'en',
            gwi_seed_wordpress_settings_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'wordpress-asetukset-fi',
            'WordPress-asetukset',
            'fi',
            gwi_seed_wordpress_settings_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'theme-customizer-en',
            'Theme Appearance and Fonts',
            'en',
            gwi_seed_theme_customizer_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'teeman-mukauttaja-fi',
            'Teeman ulkoasu ja fontit',
            'fi',
            gwi_seed_theme_customizer_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_creating_menus_en(): string
{
    return '<!-- wp:paragraph --><p>Menus control the links visitors use to move around your site. In WordPress 7.0, block themes usually manage navigation in the Site Editor with the Navigation block, while classic themes still use Appearance > Menus.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Edit site navigation","steps":[{"text":"Check your theme type first. Block themes use Appearance > Editor; classic themes usually use Appearance > Menus."},{"text":"For a block theme, open the Site Editor and select the header, template part, or Navigation block."},{"text":"Use the Navigation block List View to add page links, custom links, submenus, buttons, logos, or other supported blocks."},{"text":"If the mobile menu opens as an overlay, choose or design the navigation overlay and include a clear close control."},{"text":"For a classic theme, go to Appearance > Menus, add pages or custom links, reorder items, choose a display location, and save."},{"text":"Preview the site on desktop and mobile before considering the menu finished."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"nav-menus","caption":"Classic menu editor with structure and location settings"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Do not edit navigation in only one viewport. WordPress 7.0 gives more control over mobile overlays, so always check the collapsed menu as well as the desktop header.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_menus_fi(): string
{
    return '<!-- wp:paragraph --><p>Valikot ohjaavat linkkejä, joilla kävijät liikkuvat sivustolla. WordPress 7.0:ssa lohkoteemat hallitsevat navigaatiota yleensä sivustoeditorissa Navigaatio-lohkolla, kun taas perinteiset teemat käyttävät edelleen kohtaa Ulkoasu > Valikot.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Muokkaa sivuston navigaatiota","steps":[{"text":"Tarkista ensin teeman tyyppi. Lohkoteemat käyttävät kohtaa Ulkoasu > Editor; perinteiset teemat käyttävät yleensä kohtaa Ulkoasu > Valikot."},{"text":"Lohkoteemassa avaa sivustoeditori ja valitse ylätunniste, sivupohjan osa tai Navigaatio-lohko."},{"text":"Käytä Navigaatio-lohkon Listanäkymää sivulinkkien, mukautettujen linkkien, alivalikoiden, painikkeiden, logojen tai muiden tuettujen lohkojen lisäämiseen."},{"text":"Jos mobiilivalikko avautuu peittokerroksena, valitse tai rakenna navigaation peittokerros ja lisää selkeä sulkupainike."},{"text":"Perinteisessä teemassa mene kohtaan Ulkoasu > Valikot, lisää sivuja tai mukautettuja linkkejä, järjestä kohteet, valitse sijainti ja tallenna."},{"text":"Esikatsele sivusto työpöydällä ja mobiilissa ennen kuin pidät valikkoa valmiina."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"nav-menus","caption":"Perinteinen valikkoeditori rakenteella ja sijaintiasetuksilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Älä tarkista navigaatiota vain yhdessä näkymässä. WordPress 7.0 antaa enemmän hallintaa mobiilin peittovalikkoon, joten tarkista sekä pieneksi taittuva valikko että työpöydän ylätunniste.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_users_en(): string
{
    return '<!-- wp:paragraph --><p>Users are people who can log in to your WordPress site. Each user has a role that determines what they can do: Administrator, Editor, Author, Contributor, or Subscriber.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Manage users","steps":[{"text":"Go to Users to see all registered users."},{"text":"Click Add New to create a new user account."},{"text":"Enter a username, email, and password for the new user."},{"text":"Select the lowest role that gives the person enough access."},{"text":"Click Add New User to save the account."},{"text":"Hover over a user to Edit or Delete their account."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"users-list","caption":"Users list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Give users the minimum role they need. WordPress 7.0 also protects the site default-role setting by keeping Administrator and Editor out of that default-role selector.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_users_fi(): string
{
    return '<!-- wp:paragraph --><p>Käyttäjät ovat henkilöitä, jotka voivat kirjautua sivustollesi. Jokaisella käyttäjällä on rooli, joka määrittää mitä he voivat tehdä: Pääkäyttäjä, Toimittaja, Kirjoittaja, Osallistuja tai Tilaaja.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallinnoi käyttäjiä","steps":[{"text":"Mene Käyttäjät-näkymään nähdäksesi kaikki rekisteröityneet käyttäjät."},{"text":"Klikkaa Lisää uusi luodaksesi uuden käyttäjätilin."},{"text":"Syötä käyttäjänimi, sähköposti ja salasana uudelle käyttäjälle."},{"text":"Valitse pienin rooli, joka antaa henkilölle riittävät oikeudet."},{"text":"Klikkaa Lisää uusi käyttäjä tallentaaksesi tilin."},{"text":"Vie hiiri käyttäjän päälle muokataksesi tai poistaaksesi tilin."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"users-list","caption":"Käyttäjälista: Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Anna käyttäjille pienin tarvittava rooli. WordPress 7.0 suojaa myös sivuston oletusrooliasetusta pitämällä Pääkäyttäjä- ja Toimittaja-roolit poissa oletusroolin valitsimesta.</p><!-- /wp:paragraph -->';
}

function gwi_seed_profile_admin_color_scheme_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when the WordPress admin interface uses the Default WordPress 7 look and you prefer an older or calmer dashboard color scheme. This changes only your own admin screens; it does not change the public website theme or affect other users.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Choose your dashboard color scheme","steps":[{"text":"Open Profile from the top-right account menu, or go to Users > Profile."},{"text":"Find Personal Options near the top of the page."},{"text":"Under Administration Color Scheme, choose Fresh for the older WordPress look or choose another scheme you prefer."},{"text":"Use the color swatches to compare the menu and toolbar colors before saving."},{"text":"Scroll to the bottom and click Update Profile."},{"text":"Return to any admin screen and confirm the left menu and top toolbar use the selected colors."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"user-profile-admin-color-scheme","caption":"Administration Color Scheme options on the Profile screen"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>After saving, the admin menu and toolbar should use the selected color scheme. Visit the public site to confirm that the visible website design did not change.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not use Appearance > Themes for this task. That changes the public site theme, not your admin colors.</li><li>Do not expect this to change other users. Each user has their own profile preference.</li><li>Do not forget to click Update Profile after selecting the color scheme.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_profile_admin_color_scheme_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä ohjetta, kun WordPressin hallintapaneeli käyttää WordPress 7:n oletusulkoasua ja haluat vanhemman tai rauhallisemman väriteeman. Muutos koskee vain omia hallintanäkymiäsi; se ei muuta julkisen sivuston teemaa eikä vaikuta muihin käyttäjiin.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Valitse hallintapaneelin väriteema","steps":[{"text":"Avaa Profiili oikean yläkulman käyttäjävalikosta tai mene kohtaan Käyttäjät > Profiili."},{"text":"Etsi sivun yläosasta Omat asetukset tai Personal Options."},{"text":"Valitse kohdassa Administration Color Scheme Fresh, jos haluat vanhemman WordPress-ilmeen, tai valitse jokin muu sinulle sopiva teema."},{"text":"Vertaa valikon ja työkalupalkin värejä värimallien avulla ennen tallennusta."},{"text":"Vieritä sivun alaosaan ja klikkaa Päivitä profiili."},{"text":"Palaa mihin tahansa hallintanäkymään ja varmista, että vasen valikko ja ylätyökalupalkki käyttävät valittuja värejä."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"user-profile-admin-color-scheme","caption":"Administration Color Scheme -valinnat profiilinäkymässä"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tallennuksen jälkeen hallintavalikon ja työkalupalkin pitäisi käyttää valittua väriteemaa. Avaa julkinen sivusto ja varmista, että sivuston näkyvä ulkoasu ei muuttunut.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä käytä tähän kohtaa Ulkoasu > Teemat. Se vaihtaa julkisen sivuston teeman, ei hallintapaneelin värejä.</li><li>Älä odota muutoksen vaikuttavan muihin käyttäjiin. Jokaisella käyttäjällä on oma profiiliasetuksensa.</li><li>Älä unohda klikata Päivitä profiili väriteeman valinnan jälkeen.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_wordpress_settings_en(): string
{
    return '<!-- wp:paragraph --><p>WordPress settings control how your site works. The main settings include General, Writing, Reading, Discussion, Media, Permalinks, Privacy, and in WordPress 7.0 a Connectors screen for supported external service connections.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Configure WordPress settings","steps":[{"text":"Go to Settings to see all available setting pages."},{"text":"General Settings: Set your site title, tagline, URL, date format, timezone, and safe default role for new users."},{"text":"Reading Settings: Choose whether your homepage shows latest posts or a static page."},{"text":"Discussion Settings: Configure comment moderation, avatars, and notification options."},{"text":"Permalink Settings: Choose your URL structure. Post name is recommended for most sites."},{"text":"Connectors: Manage supported external service and AI-provider connections only when the site intentionally uses those features."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"General Settings page with key options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Set your permalink structure early and avoid changing it later, as it affects all your existing URLs. Treat connector API keys as credentials and only add them on sites that need them.</p><!-- /wp:paragraph -->';
}

function gwi_seed_wordpress_settings_fi(): string
{
    return '<!-- wp:paragraph --><p>WordPress-asetukset hallitsevat sivustosi toimintaa. Pääasetuksia ovat Yleiset, Kirjoittaminen, Lukeminen, Keskustelu, Media, Osoiterakenteet, Tietosuoja ja WordPress 7.0:ssa Connectors-näkymä tuetuille ulkoisille palveluyhteyksille.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Muokkaa WordPress-asetuksia","steps":[{"text":"Siirry Asetukset-valikkoon nähdäksesi kaikki saatavilla olevat asetussivut."},{"text":"Yleiset asetukset: Aseta sivuston otsikko, kuvaus, URL-osoite, päivämäärämuoto, aikavyöhyke ja turvallinen oletusrooli uusille käyttäjille."},{"text":"Lukemisen asetukset: Valitse näyttääkö etusivu viimeisimmät artikkelit vai staattisen sivun."},{"text":"Keskustelun asetukset: Muokkaa kommenttien moderointia, avatareja ja ilmoitusvaihtoehtoja."},{"text":"Osoiterakenteen asetukset: Valitse URL-rakenne. Artikkelin nimi on suositeltu useimmille sivustoille."},{"text":"Connectors: Hallitse tuettuja ulkoisia palvelu- ja AI-palveluyhteyksiä vain, jos sivusto käyttää niitä tarkoituksella."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"Yleiset asetukset -sivu keskeisillä vaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Aseta osoiterakenne aikaisin ja vältä sen muuttamista myöhemmin, koska se vaikuttaa kaikkiin olemassa oleviin URL-osoitteisiin. Käsittele connectorien API-avaimia tunnuksina ja lisää niitä vain sivustoille, jotka tarvitsevat niitä.</p><!-- /wp:paragraph -->';
}

function gwi_seed_theme_customizer_en(): string
{
    return '<!-- wp:paragraph --><p>Appearance tools depend on your active theme. In WordPress 7.0, the Font Library is available from Appearance > Fonts for block, hybrid, and classic themes. Block themes use the Site Editor, while classic themes may still use the Customizer.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Manage appearance and fonts","steps":[{"text":"Go to Appearance > Fonts to install, upload, update, or remove site fonts."},{"text":"For a block theme, use Appearance > Editor to edit templates, template parts, styles, patterns, and navigation."},{"text":"For a classic theme, use Appearance > Customize when the theme exposes live-preview options."},{"text":"Change one visual system at a time: fonts, colors, templates, widgets, or menus."},{"text":"Preview the result on desktop and mobile before publishing changes."},{"text":"Publish only when you are confident the change belongs to the whole site."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"customizer","caption":"Classic theme Customizer with live preview and controls"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Fonts can affect performance and brand consistency. Remove fonts you no longer use and avoid installing many similar families.</p><!-- /wp:paragraph -->';
}

function gwi_seed_theme_customizer_fi(): string
{
    return '<!-- wp:paragraph --><p>Ulkoasutyökalut riippuvat aktiivisesta teemasta. WordPress 7.0:ssa Fonttikirjasto löytyy kohdasta Ulkoasu > Fontit sekä lohko-, hybridi- että perinteisille teemoille. Lohkoteemat käyttävät sivustoeditoria, kun taas perinteiset teemat voivat käyttää edelleen Mukauttajaa.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallitse ulkoasua ja fontteja","steps":[{"text":"Mene kohtaan Ulkoasu > Fontit asentaaksesi, ladataksesi, päivittääksesi tai poistaaksesi sivuston fontteja."},{"text":"Lohkoteemassa käytä kohtaa Ulkoasu > Editor sivupohjien, sivupohjan osien, tyylien, mallien ja navigaation muokkaamiseen."},{"text":"Perinteisessä teemassa käytä kohtaa Ulkoasu > Mukauta, jos teema tarjoaa reaaliaikaisia esikatseluasetuksia."},{"text":"Muuta yhtä ulkoasun osa-aluetta kerrallaan: fontit, värit, sivupohjat, vimpaimet tai valikot."},{"text":"Esikatsele tulos työpöydällä ja mobiilissa ennen muutosten julkaisua."},{"text":"Julkaise vasta, kun olet varma että muutos kuuluu koko sivustolle."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"customizer","caption":"Perinteisen teeman Mukauttaja reaaliaikaisella esikatselulla ja ohjaimilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Fontit vaikuttavat suorituskykyyn ja brändin yhtenäisyyteen. Poista fontit, joita et enää käytä, äläkä asenna montaa lähes samanlaista fonttiperhettä.</p><!-- /wp:paragraph -->';
}
