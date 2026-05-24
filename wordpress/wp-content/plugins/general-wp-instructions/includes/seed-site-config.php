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
            'Creating Menus',
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
            'Theme Customizer',
            'en',
            gwi_seed_theme_customizer_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'teeman-mukauttaja-fi',
            'Teeman mukauttaja',
            'fi',
            gwi_seed_theme_customizer_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_creating_menus_en(): string
{
    return '<!-- wp:paragraph --><p>Menus control the navigation on your site. You can create custom menus with pages, posts, categories, and custom links.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Create a navigation menu","steps":[{"text":"Go to Appearance and click Menus."},{"text":"Enter a menu name and click Create Menu."},{"text":"Add items from the left panels: Pages, Posts, Custom Links, or Categories."},{"text":"Drag items to reorder them or indent them to create sub-menus."},{"text":"Choose a display location from the Menu Settings at the bottom."},{"text":"Click Save Menu to apply your changes."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"nav-menus","caption":"Menu editor with structure and location settings"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: You can create multiple menus for different locations like header, footer, and sidebar.</p><!-- /wp:paragraph -->';
}

function gwi_seed_creating_menus_fi(): string
{
    return '<!-- wp:paragraph --><p>Valikot ohjaavat sivustosi navigointia. Voit luoda mukautettuja valikoita sivuista, artikkeleista, kategorioista ja mukautetuista linkeista.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Luo navigointivalikko","steps":[{"text":"Mene Ulkoasu-valikkoon ja klikkaa Valikot."},{"text":"Syötä valikon nimi ja klikkaa Luo valikko."},{"text":"Lisää kohteita vasemman paneelin paneeleista: Sivut, Artikkelit, Mukautetut linkit tai Kategoriat."},{"text":"Vedä kohteita järjestääksesi ne tai sisennä luodaksesi alivalikkoja."},{"text":"Valitse näyttösijainti Valikon asetukset -kohdasta alhaalla."},{"text":"Klikkaa Tallenna valikko soveltaaksesi muutokset."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"nav-menus","caption":"Valikkoeditori rakenteella ja sijaintiasetuksilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Voit luoda useita valikoita eri sijainteihin kuten ylätunnisteeseen, alatunnisteeseen ja sivupalkkiin.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_users_en(): string
{
    return '<!-- wp:paragraph --><p>Users are people who can log in to your WordPress site. Each user has a role that determines what they can do: Administrator, Editor, Author, Contributor, or Subscriber.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Manage users","steps":[{"text":"Go to Users to see all registered users."},{"text":"Click Add New to create a new user account."},{"text":"Enter a username, email, and password for the new user."},{"text":"Select a role from the Role dropdown."},{"text":"Click Add New User to save the account."},{"text":"Hover over a user to Edit or Delete their account."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"users-list","caption":"Users list with Add New button highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Give users the minimum role they need. Administrators have full access, while Subscribers can only manage their own profile.</p><!-- /wp:paragraph -->';
}

function gwi_seed_managing_users_fi(): string
{
    return '<!-- wp:paragraph --><p>Käyttäjät ovat henkilöitä, jotka voivat kirjautua sivustollesi. Jokaisella käyttäjällä on rooli, joka määrittää mitä he voivat tehdä: Pääkäyttäjä, Toimittaja, Kirjoittaja, Osallistuja tai Tilaaja.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Hallinnoi käyttäjiä","steps":[{"text":"Mene Käyttäjät-näkymään nähdäksesi kaikki rekisteröityneet käyttäjät."},{"text":"Klikkaa Lisää uusi luodaksesi uuden käyttäjätilin."},{"text":"Syötä käyttäjänimi, sähköposti ja salasana uudelle käyttäjälle."},{"text":"Valitse rooli Roli-pudotusvalikosta."},{"text":"Klikkaa Lisää uusi käyttäjä tallentaaksesi tilin."},{"text":"Vie hiiren käyttäjän päälle muokataksesi tai poistaaksesi hänen tilinsä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"users-list","caption":"Käyttäjälista: Lisää uusi -painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Anna käyttäjille pienin tarvittava rooli. Pääkäyttäjillä on täysi pääsy, kun taas Tilaajat voivat hallita vain omaa profiiliaan.</p><!-- /wp:paragraph -->';
}

function gwi_seed_wordpress_settings_en(): string
{
    return '<!-- wp:paragraph --><p>WordPress settings control how your site works. The main settings include General, Writing, Reading, Discussion, Media, and Permalinks.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Configure WordPress settings","steps":[{"text":"Go to Settings to see all available setting pages."},{"text":"General Settings: Set your site title, tagline, URL, date format, and timezone."},{"text":"Reading Settings: Choose whether your homepage shows latest posts or a static page."},{"text":"Discussion Settings: Configure comment moderation, avatars, and notification options."},{"text":"Permalink Settings: Choose your URL structure. Post name is recommended for most sites."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"General Settings page with key options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Set your permalink structure early and avoid changing it later, as it affects all your existing URLs.</p><!-- /wp:paragraph -->';
}

function gwi_seed_wordpress_settings_fi(): string
{
    return '<!-- wp:paragraph --><p>WordPress-asetukset hallitsevat sivustosi toimintaa. Pääasetukset sisältävät Yleiset, Kirjoittaminen, Lukeminen, Keskustelu, Media ja Osoiterakenteet.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Muokkaa WordPress-asetuksia","steps":[{"text":"Siirry Asetukset-valikkoon nähdäksesi kaikki saatavilla olevat asetussivut."},{"text":"Yleiset asetukset: Aseta sivuston otsikko, kuvaus, URL-osoite, päivämäärämuoto ja aikavyöhyke."},{"text":"Lukemisen asetukset: Valitse näyttääkö etusivu viimeisimmät artikkelit vai staattisen sivun."},{"text":"Keskustelun asetukset: Muokkaa kommenttien moderointia, avatarja ja ilmoitusvaihtoehtoja."},{"text":"Osoiterakenteen asetukset: Valitse URL-rakenteesi. Artikkelin nimi on suositeltu useimmille sivustoille."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"settings-general","caption":"Yleiset asetukset -sivu keskeisilla vaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Aseta osoiterakenteesi aikaisin ja vältä sen muuttamista myöhemmin, koska se vaikuttaa kaikkiin olemassa oleviin URL-osoitteisiisi.</p><!-- /wp:paragraph -->';
}

function gwi_seed_theme_customizer_en(): string
{
    return '<!-- wp:paragraph --><p>The Theme Customizer lets you change your site appearance with a live preview. You can modify colors, fonts, menus, widgets, and other theme-specific options.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use the Theme Customizer","steps":[{"text":"Go to Appearance and click Customize."},{"text":"Use the left panel to navigate between customization sections."},{"text":"Change options and see the live preview update on the right."},{"text":"Use the Hide Controls button to see a full-width preview."},{"text":"Click Publish to save your changes, or Save Draft to save without publishing."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"customizer","caption":"Theme Customizer with live preview and controls"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: The Customizer options depend on your active theme. Switching themes will give you different customization options.</p><!-- /wp:paragraph -->';
}

function gwi_seed_theme_customizer_fi(): string
{
    return '<!-- wp:paragraph --><p>Teeman mukauttaja antaa muuttaa sivustosi ulkoasua reaaliaikaisella esikatselulla. Voit muokata värejä, fontteja, valikoita, widgettejä ja muita teemakohtaisia vaihtoehtoja.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä teeman mukauttajaa","steps":[{"text":"Mene Ulkoasu-valikkoon ja klikkaa Mukauta."},{"text":"Käytä vasenta paneelia navigoidaksesi mukautusosioiden välillä."},{"text":"Muuta vaihtoehtoja ja katso reaaliaikaisen esikatselun päivittyvän oikealla."},{"text":"Käytä Piilota ohjauspainiketta nähdäksesi täysleveän esikatselun."},{"text":"Klikkaa Julkaise tallentaaksesi muutokset tai Tallenna luonnos tallentaaksesi julkaisematta."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"customizer","caption":"Teeman mukauttaja reaaliaikaisella esikatselulla ja ohjaimilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Mukauttajan vaihtoehdot riippuvat aktiivisestä teemastasi. Teeman vaihtaminen antaa eri mukautusvaihtoehtoja.</p><!-- /wp:paragraph -->';
}
