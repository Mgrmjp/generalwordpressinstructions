<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_editor_workflows(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'preview-publish-changes-en',
            'Preview and Publish Changes',
            'en',
            gwi_seed_preview_publish_changes_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'esikatsele-ja-julkaise-muutokset-fi',
            'Esikatsele ja julkaise muutokset',
            'fi',
            gwi_seed_preview_publish_changes_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'use-list-view-en',
            'Use List View',
            'en',
            gwi_seed_use_list_view_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'listanakyman-kaytto-fi',
            'Listanäkymän käyttö',
            'fi',
            gwi_seed_use_list_view_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'add-images-with-alt-text-en',
            'Add Images with Alt Text',
            'en',
            gwi_seed_add_images_with_alt_text_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'lisaa-kuvia-alt-tekstilla-fi',
            'Lisää kuvia alt-tekstillä',
            'fi',
            gwi_seed_add_images_with_alt_text_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_preview_publish_changes_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when you need to update a page or post without publishing changes blindly. Preview the result first, then publish only when the page looks correct.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Preview and publish safely","steps":[{"text":"Open the page or post you need to edit."},{"text":"Make the content change and keep each edit focused."},{"text":"Click Preview to open the page in a new tab or preview mode."},{"text":"Check desktop and mobile preview when the layout or image placement changed."},{"text":"Return to the editor and fix anything that looks wrong."},{"text":"Click Publish or Update only after the preview matches what visitors should see."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-publish-controls","caption":"Block Editor preview and publish controls"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Open the public page in a fresh browser tab. Confirm the changed text, images, links, and layout are visible without editor controls.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not publish before checking the preview.</li><li>Avoid leaving old preview tabs open, because they can show an outdated version.</li><li>Do not rely only on desktop preview when the change affects columns, images, or buttons.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_preview_publish_changes_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä, kun sinun pitää päivittää sivua tai artikkelia julkaisematta muutoksia sokkona. Esikatsele lopputulos ensin ja julkaise vasta, kun sivu näyttää oikealta.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Esikatsele ja julkaise turvallisesti","steps":[{"text":"Avaa sivu tai artikkeli, jota sinun pitää muokata."},{"text":"Tee sisältömuutos ja pidä muokkaus rajattuna."},{"text":"Klikkaa Esikatsele avataksesi sivun uuteen välilehteen tai esikatselutilaan."},{"text":"Tarkista työpöytä- ja mobiiliesikatselu, jos asettelu tai kuvan sijainti muuttui."},{"text":"Palaa editoriin ja korjaa kaikki, mikä näyttää väärältä."},{"text":"Klikkaa Julkaise tai Päivitä vasta, kun esikatselu vastaa sitä, mitä kävijöiden pitää nähdä."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-publish-controls","caption":"Lohkoeditorin esikatselu- ja julkaisupainikkeet"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Avaa julkinen sivu uuteen selaimen välilehteen. Varmista, että muutettu teksti, kuvat, linkit ja asettelu näkyvät ilman editorin hallintapainikkeita.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä julkaise ennen kuin olet tarkistanut esikatselun.</li><li>Vältä vanhojen esikatseluvälilehtien käyttöä, koska ne voivat näyttää vanhan version.</li><li>Älä tarkista vain työpöytänäkymää, jos muutos vaikuttaa sarakkeisiin, kuviin tai painikkeisiin.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_use_list_view_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when a page has many blocks, groups, columns, or patterns. List View shows the page structure in one panel so you can select the right block without guessing.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Find and organize blocks with List View","steps":[{"text":"Open the page or post in the Block Editor."},{"text":"Click List View or Document Overview in the top toolbar."},{"text":"Expand nested groups, columns, and patterns until you find the block you need."},{"text":"Click a block name in List View to select that exact block in the editor."},{"text":"Rename, move, duplicate, or remove blocks from the block options when needed."},{"text":"Close List View after the structure is clear, or keep it open while editing complex pages."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-list-view","caption":"Block Editor List View control"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Select a block from List View and confirm the same block is highlighted in the editor canvas. If the page has nested blocks, check that the parent and child blocks are easy to tell apart.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not delete a parent Group or Columns block unless you mean to remove everything inside it.</li><li>Avoid dragging nested blocks when you are unsure where the drop line appears.</li><li>Do not edit a synced pattern deeply before checking whether the change affects other pages.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_use_list_view_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä, kun sivulla on paljon lohkoja, ryhmiä, sarakkeita tai malleja. Listanäkymä näyttää sivun rakenteen yhdessä paneelissa, jotta voit valita oikean lohkon arvaamatta.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Etsi ja järjestä lohkoja listanäkymässä","steps":[{"text":"Avaa sivu tai artikkeli lohkoeditorissa."},{"text":"Klikkaa ylätyökaluriviltä Listanäkymä tai Asiakirjan yleiskatsaus."},{"text":"Avaa sisäkkäiset ryhmät, sarakkeet ja mallit, kunnes löydät tarvitsemasi lohkon."},{"text":"Klikkaa lohkon nimeä listanäkymässä valitaksesi juuri sen lohkon editorissa."},{"text":"Nimeä, siirrä, kopioi tai poista lohkoja lohkon valinnoista tarvittaessa."},{"text":"Sulje listanäkymä, kun rakenne on selvä, tai pidä se auki monimutkaista sivua muokatessa."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"block-editor-list-view","caption":"Lohkoeditorin listanäkymän painike"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Valitse lohko listanäkymästä ja varmista, että sama lohko korostuu editorin sisällössä. Jos sivulla on sisäkkäisiä lohkoja, tarkista että ylä- ja alalohkot erottaa helposti toisistaan.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä poista Ryhmä- tai Sarakkeet-ylälohkoa, ellet halua poistaa kaikkea sen sisällä.</li><li>Vältä sisäkkäisten lohkojen raahaamista, jos et näe varmasti mihin pudotusviiva tulee.</li><li>Älä tee syvää muutosta synkronoituun malliin ennen kuin tarkistat vaikuttaako muutos muihin sivuihin.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_add_images_with_alt_text_en(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Use this when you add an image to a page or post. Alt text is a short image description for screen readers and for cases where the image cannot load.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Add an image with useful alt text","steps":[{"text":"Open the page or post and place the cursor where the image should appear."},{"text":"Insert an Image block or choose an existing Image block."},{"text":"Choose an image from the Media Library or upload a new file."},{"text":"Fill the Alternative Text field with a short description of the image content or purpose."},{"text":"Leave alt text empty only when the image is purely decorative and adds no information."},{"text":"Update the page and preview it to confirm the image size, crop, and placement are correct."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-grid","caption":"Media Library for choosing images"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>How to check it worked</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Select the image in the editor and check that the Alternative Text field contains the saved description. Preview the page and confirm the image supports the surrounding text.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Common mistakes</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Do not use the file name as alt text unless it clearly describes the image.</li><li>Avoid phrases like image of or picture of when the description can start with the useful detail.</li><li>Do not stuff keywords into alt text. Write for people first.</li></ul>
<!-- /wp:list -->
GUIDE;
}

function gwi_seed_add_images_with_alt_text_fi(): string
{
    return <<<'GUIDE'
<!-- wp:paragraph -->
<p>Käytä tätä, kun lisäät kuvan sivulle tai artikkeliin. Alt-teksti on lyhyt kuvan kuvaus ruudunlukijoille ja tilanteisiin, joissa kuva ei lataudu.</p>
<!-- /wp:paragraph -->

<!-- wp:general-wp-instructions/step-list {"title":"Lisää kuva hyödyllisellä alt-tekstillä","steps":[{"text":"Avaa sivu tai artikkeli ja aseta kursori kohtaan, johon kuvan pitää tulla."},{"text":"Lisää Kuva-lohko tai valitse olemassa oleva Kuva-lohko."},{"text":"Valitse kuva mediakirjastosta tai lataa uusi tiedosto."},{"text":"Täytä Vaihtoehtoinen teksti -kenttään lyhyt kuvaus kuvan sisällöstä tai tarkoituksesta."},{"text":"Jätä alt-teksti tyhjäksi vain, jos kuva on puhtaasti koristeellinen eikä lisää tietoa."},{"text":"Päivitä sivu ja esikatsele, että kuvan koko, rajaus ja sijainti ovat oikein."}]} /-->

<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"media-library-grid","caption":"Mediakirjasto kuvien valintaan"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->

<!-- wp:heading -->
<h2>Miten tarkistaa että se toimii</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Valitse kuva editorissa ja tarkista, että Vaihtoehtoinen teksti -kentässä on tallennettu kuvaus. Esikatsele sivu ja varmista, että kuva tukee ympäröivää tekstiä.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Yleiset virheet</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul><li>Älä käytä tiedostonimeä alt-tekstinä, ellei se kuvaa kuvaa selvästi.</li><li>Vältä ilmaisuja kuva aiheesta, jos kuvauksen voi aloittaa suoraan hyödyllisellä tiedolla.</li><li>Älä täytä alt-tekstiä avainsanoilla. Kirjoita ensin ihmisille.</li></ul>
<!-- /wp:list -->
GUIDE;
}
