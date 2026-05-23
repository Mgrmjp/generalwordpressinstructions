<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_block_editor(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'working-with-blocks-en',
            'Working with Blocks',
            'en',
            gwi_seed_working_with_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'lohkojen-kaytto-fi',
            'Lohkojen kaytto',
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
            'Yleiset sisaltolohkot',
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
            'Reusable Blocks and Patterns',
            'en',
            gwi_seed_reusable_blocks_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'uudelleenkaytettavat-lohkot-ja-mallit-fi',
            'Uudelleenkaytettavat lohkot ja mallit',
            'fi',
            gwi_seed_reusable_blocks_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_working_with_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Blocks are the building units of the WordPress editor. Every piece of content is a block: paragraphs, headings, images, buttons, and more.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Work with blocks","steps":[{"text":"Click the plus button in the editor toolbar to open the block inserter."},{"text":"Search or browse for the block type you need and click to insert it."},{"text":"Click a block to select it and see its toolbar with formatting options."},{"text":"Use the up and down arrows in the block toolbar to move blocks."},{"text":"Click the three-dot menu for more options like duplicate, group, or delete."},{"text":"Use the List View button in the toolbar to see all blocks as a nested list."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-inserter","caption":"Block Editor with inserter toggle highlighted"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Press Enter to create a new paragraph block. Type / to open the block inserter with a slash command.</p><!-- /wp:paragraph -->';
}

function gwi_seed_working_with_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Lohkot ovat WordPress-editorin rakennusyksikokset. Jokainen sisallön palanen on lohko: kappaleet, otsikot, kuvat, painikkeet ja muut.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Tyo lohkojen kanssa","steps":[{"text":"Klikkaa plus-painiketta editorin tyokalurivissa avataksesi lohkon lisaajan."},{"text":"Hae tai selaa tarvitsemaasi lohkotyyppia ja klikkaa lisätäksesi sen."},{"text":"Klikkaa lohkoa valitaksesi sen ja nähdaksesi sen tyokalurivin muotoiluvaihtoehdoilla."},{"text":"Käytä nuolia lohkon tyokalurivissa siirtääksesi lohkoja."},{"text":"Klikkaa kolmen pisteen valikkoa lisavaihtoehtoihin kuten kopioi, ryhmitä tai poista."},{"text":"Käytä Listanäkymä-painiketta tyokalurivissa nhdäksesi kaikki lohkot sisäkkäisena listana."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-inserter","caption":"Lohkoeditori lisaajan painike korostettuna"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Paina Enter luodaksesi uuden kappalelohkon. Kirjoita / avataksesi lohkon lisaajan kauttakomentona.</p><!-- /wp:paragraph -->';
}

function gwi_seed_common_content_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>The most frequently used blocks are for text content: paragraphs, headings, lists, quotes, and tables.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use common content blocks","steps":[{"text":"Paragraph: The default block for regular text. Just start typing."},{"text":"Heading: Use H2 for main sections, H3 for subsections. Add from the inserter or type ## followed by a space."},{"text":"List: Choose ordered (numbered) or unordered (bulleted) lists from the inserter."},{"text":"Quote: Add pull quotes or block quotes with optional citation."},{"text":"Table: Insert a table with rows and columns for structured data."},{"text":"Separator: Add a horizontal line to visually divide content sections."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Block inserter showing content blocks"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use the Transform button in the block toolbar to convert one block type to another, such as paragraph to list.</p><!-- /wp:paragraph -->';
}

function gwi_seed_common_content_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Useimmin kaytetyt lohkot ovat tekstisisaltoa: kappaleet, otsikot, listat, lainaukset ja taulukot.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä yleisia sisaltolohkoja","steps":[{"text":"Kappale: Oletuslohko tavalliselle tekstille. Aloita kirjoittaminen."},{"text":"Otsikko: Käytä H2 paasioihin, H3 alaosioihin. Lisaa lisaajasta tai kirjoita ## seettuna valilyönnista."},{"text":"Listaa: Valitse jarjestetty (numeroitu) tai jarjestamaton (merkitty) listaa lisaajasta."},{"text":"Lainaus: Lisaa vetolainauksia tai lohkolainauksia valinnaisella viittauksella."},{"text":"Taulukko: Lisaa taulukko riveilla ja sarakkeilla rakennetulle datalle."},{"text":"Erotin: Lisaa vaakaviiva visuaalisesti erottamaan sisaltloosiot."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Lohkon lisaaja näyttää sisaltolohkot"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Muunna-painiketta lohkon tyokalurivissa muuttaaksesi yhden lohkotyypin toiseksi, kuten kappale listaksi.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Media blocks let you add images, galleries, videos, audio, and files to your content.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use media blocks","steps":[{"text":"Image: Insert a single image from your Media Library or upload a new one."},{"text":"Gallery: Display multiple images in a grid layout with optional captions."},{"text":"Video: Embed a video file or paste a URL from YouTube, Vimeo, or other platforms."},{"text":"Audio: Add audio files with a built-in player for podcasts or music."},{"text":"File: Add a downloadable file with an optional download button."},{"text":"Cover: Add a background image with text overlay for hero sections."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Media Library for selecting images and files"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Always add alt text to images for accessibility. Use the Cover block for eye-catching hero sections.</p><!-- /wp:paragraph -->';
}

function gwi_seed_media_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Medialohkot antavat lisata kuvia, gallerioita, videoita, aania ja tiedostoja sisaltoosi.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä medialohkoja","steps":[{"text":"Kuva: Lisaa yksittäinen kuva mediakirjastostasi tai lataa uusi."},{"text":"Galleria: Näytä useita kuvia ruudukkoasettelulla valinnaisilla kuvateksteilla."},{"text":"Video: Upota videotiedostoa tai liitä URL-osoite YouTubesta, Vimeosta tai muista alustoista."},{"text":"Aani: Lisaa aänitiedostoja sisäänrakennetulla soittimella podcasteille tai musiikille."},{"text":"Tiedosto: Lisaa ladattava tiedosto valinnaisella latauspainikkeella."},{"text":"Kansi: Lisaa taustakuva tekstipäällä sankariosioihin."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-add-new","caption":"Mediakirjasto kuvien ja tiedostojen valintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Lisää aina vaihtoehtoinen teksti kuviin saavutettavuutta varten. Käytä Kansi-lohkoa silmiinpaneviin sankariosioihin.</p><!-- /wp:paragraph -->';
}

function gwi_seed_layout_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Layout blocks help you structure your page with columns, groups, and spacing elements.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use layout blocks","steps":[{"text":"Columns: Create multi-column layouts with 2, 3, 4, or more columns."},{"text":"Group: Combine multiple blocks into a single group for easy management."},{"text":"Buttons: Add one or more call-to-action buttons with customizable styles."},{"text":"Spacer: Add vertical space between content sections."},{"text":"Separator: Add a horizontal line with optional wide or full-width styling."},{"text":"Row and Stack: Arrange blocks horizontally (row) or vertically (stack) with flexbox controls."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Layout blocks in the block inserter"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use the Group block to apply background colors or padding to multiple blocks at once.</p><!-- /wp:paragraph -->';
}

function gwi_seed_layout_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Asettelulohkot auttavat rakentamaan sivusi rakenteen sarakkeilla, ryhmillä ja valilyontielementeilla.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä asetteluohkoja","steps":[{"text":"Sarakkeet: Luo monisarakkeisia asetteluja 2, 3, 4 tai useammalla sarakkeella."},{"text":"Ryhmä: Yhdistä useat lohkot yhdeksi ryhmäksi helppoa hallintaa varten."},{"text":"Painikkeet: Lisaa yksi tai useampi toimintopainike mukautettavilla tyyleilla."},{"text":"Valilyönti: Lisaa pystysuora tila sisaltoosioiden valille."},{"text":"Erotin: Lisaa vaakaviiva valinnaisella levealla tai täyslevealla tyylillä."},{"text":"Rivi ja Pino: Järjestä lohkot vaakasuoraan (rivi) tai pystysuoraan (pino) flexbox-ohjaimilla."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-add-block","caption":"Asettelulohkot lohkon lisaajassa"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Ryhmä-lohkoa soveltaaksesi taustavärejä tai täyttöä useisiin lohkoihin kerralla.</p><!-- /wp:paragraph -->';
}

function gwi_seed_reusable_blocks_en(): string
{
    return '<!-- wp:paragraph --><p>Reusable blocks and patterns let you save and reuse content across your site. Use them for consistent elements like call-to-action sections, disclaimers, or custom layouts.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Use reusable blocks and patterns","steps":[{"text":"Select one or more blocks and click the three-dot menu, then choose Create Reusable block."},{"text":"Name your reusable block and save it. It will appear in the inserter under Reusable."},{"text":"Edit a reusable block once and all instances update automatically across your site."},{"text":"Convert a reusable block to regular blocks if you need to edit one instance independently."},{"text":"Browse Patterns in the inserter for pre-designed block arrangements from your theme."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-reusable-list","caption":"Reusable blocks management page"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use Patterns for design layouts that you want to customize individually. Use Reusable blocks for content that should stay identical everywhere.</p><!-- /wp:paragraph -->';
}

function gwi_seed_reusable_blocks_fi(): string
{
    return '<!-- wp:paragraph --><p>Uudelleenkaytettavat lohkot ja mallit antavat tallentaa ja kayttaa uudelleen sisaltoa sivustollasi. Kayta niita johdonmukaisille elementeille kuten toimintosioille, vastuuvapauslausekkeille tai mukautetuille asetteluille.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Käytä uudelleenkaytettavia lohkoja ja malleja","steps":[{"text":"Valitse yksi tai useampi lohko ja klikkaa kolmen pisteen valikkoa, sitten valitse Luo uudelleenkaytettava lohko."},{"text":"Nimeä uudelleenkaytettava lohkosi ja tallenna se. Se naytyy lisaajassa Uudelleenkaytettavat-kohdassa."},{"text":"Muokkaa uudelleenkaytettavaa lohkoa kerran ja kaikki esiintymat paivittyvat automaattisesti sivustollasi."},{"text":"Muunna uudelleenkaytettava lohko tavallisiksi lohkoiksi jos tarvitsee muokata yhta esiintymaa itsenaisesti."},{"text":"Selaa Malleja lisaajassa esikaytettyjen lohkojen asetteluja teemastasi."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-reusable-list","caption":"Uudelleenkaytettavien lohkojen hallintasivu"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Malleja asetteluille, jotka haluat mukauttaa yksittaisesti. Käytä Uudelleenkaytettavia lohkoja sisallolle, jonka tulisi pysya identtisena kaikkialla.</p><!-- /wp:paragraph -->';
}
