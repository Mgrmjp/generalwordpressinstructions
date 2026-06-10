<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_block_editor(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'block-editor-basics-en',
            'Block Editor Basics',
            'en',
            gwi_seed_block_editor_basics_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'lohkoeditorin-perusteet-fi',
            'Lohkoeditorin perusteet',
            'fi',
            gwi_seed_block_editor_basics_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'working-with-blocks-en',
            'Working with Blocks',
            'en',
            gwi_seed_working_with_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'lohkojen-kaytto-fi',
            'Lohkojen käyttö',
            'fi',
            gwi_seed_working_with_blocks_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'common-content-blocks-en',
            'Common Content Blocks',
            'en',
            gwi_seed_common_content_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'yleiset-sisaltolohkot-fi',
            'Yleiset sisältölohkot',
            'fi',
            gwi_seed_common_content_blocks_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'media-blocks-en',
            'Media Blocks',
            'en',
            gwi_seed_media_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'medialohkot-fi',
            'Medialohkot',
            'fi',
            gwi_seed_media_blocks_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'layout-blocks-en',
            'Layout Blocks',
            'en',
            gwi_seed_layout_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'asettelulohkot-fi',
            'Asettelulohkot',
            'fi',
            gwi_seed_layout_blocks_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'reusable-blocks-en',
            'Synced Patterns and Patterns',
            'en',
            gwi_seed_reusable_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'uudelleenkaytettavat-lohkot-ja-mallit-fi',
            'Synkronoidut mallit ja mallit',
            'fi',
            gwi_seed_reusable_blocks_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_block_editor_basics_en(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>The Block Editor is the default editing experience in WordPress 7.0. Pages and posts are built from blocks, patterns, and media that can be selected, reordered, previewed, and published from the same editor screen.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Create content in the Block Editor","steps":[{"text":"Open Pages or Posts and choose Add New."},{"text":"Write a clear title before adding content blocks."},{"text":"Use the plus button or type / in an empty paragraph to insert blocks and patterns."},{"text":"Open List View to see the page structure and select nested blocks."},{"text":"Use the settings sidebar for publishing, templates, featured images, categories, and block-specific controls."},{"text":"Preview the page, then choose Publish or Update when the content is ready."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Block Editor with the block inserter highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Tip: In WordPress 7.0, the Command Palette is available from the admin bar with Ctrl+K or Cmd+K. Use it to jump to posts, pages, templates, settings, and editor actions without hunting through menus.</p>
<!-- /wp:paragraph -->
BLOCKS;
}

function gwi_seed_block_editor_basics_fi(): string
{
    return <<<'BLOCKS'
<!-- wp:paragraph -->
<p>Lohkoeditori on WordPress 7.0:n oletusmuokkausnäkymä. Sivut ja artikkelit rakennetaan lohkoista, malleista ja mediasta, joita voi valita, järjestää, esikatsella ja julkaista samassa editorissa.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Luo sisältöä lohkoeditorissa","steps":[{"text":"Avaa Sivut tai Artikkelit ja valitse Lisää uusi."},{"text":"Kirjoita selkeä otsikko ennen sisältölohkojen lisäämistä."},{"text":"Lisää lohkoja ja malleja plus-painikkeella tai kirjoittamalla / tyhjään kappaleeseen."},{"text":"Avaa Listanäkymä nähdäksesi sivun rakenteen ja valitaksesi sisäkkäisiä lohkoja."},{"text":"Käytä asetussivupalkkia julkaisuun, sivupohjiin, artikkelikuvaan, kategorioihin ja lohkokohtaisiin asetuksiin."},{"text":"Esikatsele sivu ja valitse Julkaise tai Päivitä, kun sisältö on valmis."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Lohkoeditori: lohkon lisääjä korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:paragraph -->
<p>Vinkki: WordPress 7.0:ssa komentopaletti löytyy hallintapalkista pikanäppäimillä Ctrl+K tai Cmd+K. Sen avulla voit siirtyä artikkeleihin, sivuihin, sivupohjiin, asetuksiin ja editorin toimintoihin ilman valikoiden etsimistä.</p>
<!-- /wp:paragraph -->
BLOCKS;
}

function gwi_seed_working_with_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Blocks are the building units of the WordPress editor. Every piece of content is a block: paragraphs, headings, images, buttons, icons, breadcrumbs, galleries, and layout containers.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Work with blocks","steps":[{"text":"Click the plus button in the editor toolbar to open the block inserter."},{"text":"Search or browse for the block type you need, then click to insert it."},{"text":"Click a block to select it and use the toolbar for quick formatting, alignment, moving, visibility, and more options."},{"text":"Use List View to inspect nested groups, patterns, columns, navigation, and reusable sections."},{"text":"Use the block settings sidebar for advanced controls such as typography, spacing, dimensions, custom CSS, and visibility when the block supports them."},{"text":"Use Hide or block visibility controls when a block should be omitted or hidden on mobile, tablet, or desktop."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-inserter","caption":"Block Editor with inserter toggle highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Blocks hidden only for a device size still remain in the page markup. Use them for presentation choices, not for hiding sensitive or heavy content that should not load.</p><!-- /wp:paragraph -->';
}

function gwi_seed_working_with_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Lohkot ovat WordPress-editorin rakennusyksiköitä. Jokainen sisällön osa on lohko: kappaleet, otsikot, kuvat, painikkeet, ikonit, murupolut, galleriat ja asettelusäiliöt.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Työskentele lohkojen kanssa","steps":[{"text":"Klikkaa plus-painiketta editorin työkalurivissä avataksesi lohkon lisääjän."},{"text":"Hae tai selaa tarvitsemaasi lohkotyyppiä ja klikkaa lisätäksesi sen."},{"text":"Valitse lohko ja käytä työkaluriviä nopeaan muotoiluun, tasaukseen, siirtämiseen, näkyvyyteen ja lisävalintoihin."},{"text":"Käytä Listanäkymää sisäkkäisten ryhmien, mallien, sarakkeiden, navigaation ja uudelleenkäytettävien osien tarkistamiseen."},{"text":"Käytä lohkon asetussivupalkkia tarkempiin säätöihin, kuten typografiaan, väleihin, mittoihin, omaan CSS:ään ja näkyvyyteen, jos lohko tukee niitä."},{"text":"Käytä Piilota- tai näkyvyysasetuksia, kun lohko pitää jättää pois tai piilottaa mobiilissa, tabletissa tai työpöydällä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-inserter","caption":"Lohkoeditori: lisääjän painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Vain tietyllä laiteleveydellä piilotettu lohko jää edelleen sivun koodiin. Käytä tätä ulkoasun säätöön, älä arkaluontoisen tai raskaan sisällön piilottamiseen.</p><!-- /wp:paragraph -->';
}

function gwi_seed_common_content_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>The most frequently used blocks are for text and simple page structure: paragraphs, headings, lists, quotes, tables, details, icons, and breadcrumbs.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use common content blocks","steps":[{"text":"Paragraph: The default block for regular text. Just start typing."},{"text":"Heading: Use H2 for main sections and H3 for subsections. In WordPress 7.0 the Heading block is easier to find and switch between levels."},{"text":"List: Choose ordered or unordered lists from the inserter, then use List View when nesting gets complex."},{"text":"Quote and Pullquote: Highlight important text with an optional citation."},{"text":"Table and Details: Use tables for structured data and Details for expandable support information."},{"text":"Icon and Breadcrumbs: Add a visual cue with the Icon block or show page hierarchy with Breadcrumbs when your theme supports it."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Block inserter showing content blocks"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use the Transform button in the block toolbar to convert one block type to another, such as paragraph to list or heading.</p><!-- /wp:paragraph -->';
}

function gwi_seed_common_content_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Useimmin käytetyt lohkot liittyvät tekstiin ja sivun perusrakenteeseen: kappaleet, otsikot, listat, lainaukset, taulukot, lisätiedot, ikonit ja murupolut.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä yleisiä sisältölohkoja","steps":[{"text":"Kappale: Oletuslohko tavalliselle tekstille. Aloita kirjoittaminen."},{"text":"Otsikko: Käytä H2-tasoa pääosioihin ja H3-tasoa alaosioihin. WordPress 7.0:ssa otsikkolohko on helpompi löytää ja vaihtaa eri tasoihin."},{"text":"Lista: Valitse numeroitu tai merkitty lista lisääjästä ja käytä Listanäkymää, kun sisäkkäisyys monimutkaistuu."},{"text":"Lainaus ja vetolainaus: Korosta tärkeä teksti valinnaisella lähteellä."},{"text":"Taulukko ja Lisätiedot: Käytä taulukkoa rakenteiseen dataan ja Lisätiedot-lohkoa avattavaan tukisisältöön."},{"text":"Ikoni ja Murupolut: Lisää visuaalinen vihje Ikoni-lohkolla tai näytä sivuhierarkia Murupolut-lohkolla, jos teema tukee sitä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Lohkon lisääjä näyttää sisältölohkot"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Muunna-painiketta lohkon työkalurivissä muuttaaksesi lohkotyypin toiseksi, esimerkiksi kappaleen listaksi tai otsikoksi.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Media blocks let you add images, galleries, videos, audio, files, and immersive covers to your content.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use media blocks","steps":[{"text":"Image: Insert a single image from your Media Library or upload a new one."},{"text":"Gallery: Display multiple images in a grid and enable enlarge-on-click when visitors should browse the images in a lightbox or slideshow."},{"text":"Video: Embed a video file or paste a URL from YouTube, Vimeo, or another supported platform."},{"text":"Audio: Add audio files with a built-in player for podcasts or music."},{"text":"File: Add a downloadable file with an optional download button."},{"text":"Cover: Add a background image or video with text overlay for hero sections."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Media Library for selecting images and files"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Always add useful alt text to images. WordPress 7.0 can import image alt text from IPTC metadata when the file contains it, but you should still review the result before publishing.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Medialohkot antavat lisätä kuvia, gallerioita, videoita, ääniä, tiedostoja ja näyttäviä kansiosioita sisältöön.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä medialohkoja","steps":[{"text":"Kuva: Lisää yksittäinen kuva mediakirjastosta tai lataa uusi."},{"text":"Galleria: Näytä useita kuvia ruudukossa ja ota suurennus klikkauksella käyttöön, kun kävijän pitää selata kuvia valolaatikossa tai diaesityksenä."},{"text":"Video: Upota videotiedosto tai liitä URL-osoite YouTubesta, Vimeosta tai muusta tuetusta palvelusta."},{"text":"Ääni: Lisää äänitiedostoja sisäänrakennetulla soittimella podcasteille tai musiikille."},{"text":"Tiedosto: Lisää ladattava tiedosto valinnaisella latauspainikkeella."},{"text":"Kansi: Lisää taustakuva tai taustavideo tekstikerroksella sankariosioihin."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Mediakirjasto kuvien ja tiedostojen valintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Lisää kuville aina hyödyllinen vaihtoehtoinen teksti. WordPress 7.0 voi tuoda kuvan alt-tekstin IPTC-metadatasta, jos tiedostossa on sellainen, mutta tarkista teksti silti ennen julkaisua.</p><!-- /wp:paragraph -->';
}

function gwi_seed_layout_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Layout blocks help you structure a page with columns, groups, rows, stacks, spacing, buttons, and responsive visibility controls.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use layout blocks","steps":[{"text":"Columns: Create multi-column layouts, then check tablet and mobile previews before publishing."},{"text":"Group: Combine multiple blocks into a single section for shared background, spacing, width, and custom CSS controls."},{"text":"Row and Stack: Arrange blocks horizontally or vertically with flexbox-style controls."},{"text":"Buttons: Add one or more call-to-action buttons and keep labels short."},{"text":"Spacer and Separator: Add intentional breathing room or divide sections visually."},{"text":"Visibility: Hide alternate layout blocks on mobile, tablet, or desktop only when the duplicated content is acceptable in the page markup."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Layout blocks in the block inserter"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use patterns for repeated layouts. In WordPress 7.0, inserted patterns can behave as a single unit until you choose to edit the pattern in more detail.</p><!-- /wp:paragraph -->';
}

function gwi_seed_layout_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Asettelulohkot auttavat rakentamaan sivun rakenteen sarakkeilla, ryhmillä, riveillä, pinoilla, väleillä, painikkeilla ja responsiivisilla näkyvyysasetuksilla.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä asettelulohkoja","steps":[{"text":"Sarakkeet: Luo monisarakkeisia asetteluja ja tarkista tabletti- sekä mobiiliesikatselu ennen julkaisua."},{"text":"Ryhmä: Yhdistä useita lohkoja yhdeksi osioksi, jolla on yhteinen tausta, välistys, leveys ja tarvittaessa oma CSS."},{"text":"Rivi ja Pino: Järjestä lohkot vaakasuoraan tai pystysuoraan flexbox-tyyppisillä ohjaimilla."},{"text":"Painikkeet: Lisää yksi tai useampi toimintopainike ja pidä tekstit lyhyinä."},{"text":"Välilyönti ja Erotin: Lisää tarkoituksellista tilaa tai jaa osiot visuaalisesti."},{"text":"Näkyvyys: Piilota vaihtoehtoinen asettelulohko mobiilissa, tabletissa tai työpöydällä vain, jos sisällön kahdentuminen sivun koodissa on hyväksyttävää."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Asettelulohkot lohkon lisääjässä"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä malleja toistuviin asetteluihin. WordPress 7.0:ssa lisätty malli voi toimia yhtenä kokonaisuutena, kunnes päätät muokata mallia tarkemmin.</p><!-- /wp:paragraph -->';
}

function gwi_seed_reusable_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>WordPress now uses Patterns for saved block arrangements. A synced pattern behaves like the old reusable block: editing the saved pattern updates every place where it is used.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use synced and standard patterns","steps":[{"text":"Select one or more blocks and open the three-dot menu."},{"text":"Choose Create pattern, give it a clear name, and decide whether Sync should be on."},{"text":"Use a synced pattern for content that must stay identical everywhere, such as a disclaimer or recurring callout."},{"text":"Use a standard pattern for a layout starting point that editors can customize per page."},{"text":"Insert saved patterns from the Patterns area of the block inserter or by searching for the pattern name."},{"text":"Detach a synced pattern when this one instance needs independent edits."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-reusable-list","caption":"Patterns management page for saved reusable content"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: In WordPress 7.0, patterns can be edited more like a single unit. Use List View before detaching or making deep structural changes.</p><!-- /wp:paragraph -->';
}

function gwi_seed_reusable_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>WordPress käyttää tallennetuille lohkorakenteille nykyisin nimeä Mallit. Synkronoitu malli toimii kuten vanha uudelleenkäytettävä lohko: tallennetun mallin muokkaus päivittyy kaikkiin paikkoihin, joissa sitä käytetään.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä synkronoituja ja tavallisia malleja","steps":[{"text":"Valitse yksi tai useampi lohko ja avaa kolmen pisteen valikko."},{"text":"Valitse Luo malli, anna selkeä nimi ja päätä, onko Synkronointi päällä."},{"text":"Käytä synkronoitua mallia sisällölle, jonka pitää pysyä samana kaikkialla, kuten vastuulauseke tai toistuva huomio."},{"text":"Käytä tavallista mallia asettelun lähtöpisteenä, jota voi muokata sivukohtaisesti."},{"text":"Lisää tallennettuja malleja lohkon lisääjän Mallit-alueelta tai hakemalla mallin nimellä."},{"text":"Irrota synkronoitu malli, jos juuri tämä esiintymä tarvitsee omat muutokset."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-reusable-list","caption":"Mallien hallintasivu tallennetulle uudelleenkäytettävälle sisällölle"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: WordPress 7.0:ssa malleja voi muokata enemmän yhtenä kokonaisuutena. Avaa Listanäkymä ennen irrottamista tai suuria rakennemuutoksia.</p><!-- /wp:paragraph -->';
}
