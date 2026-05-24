<?php

if (!defined('ABSPATH')) {
    exit;
}

function gwi_seed_classic_editor(): array
{
    $pairs = [];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'classic-formatting-en',
            'Classic Editor Formatting',
            'en',
            gwi_seed_classic_formatting_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'perinteisen-editorin-muotoilu-fi',
            'Perinteisen editorin muotoilu',
            'fi',
            gwi_seed_classic_formatting_fi()
        ),
    ];

    $pairs[] = [
        'en' => gwi_create_seed_instruction(
            'classic-media-en',
            'Classic Editor Media',
            'en',
            gwi_seed_classic_media_en()
        ),
        'fi' => gwi_create_seed_instruction(
            'perinteisen-editorin-media-fi',
            'Perinteisen editorin media',
            'fi',
            gwi_seed_classic_media_fi()
        ),
    ];

    return $pairs;
}

function gwi_seed_classic_formatting_en(): string
{
    return '<!-- wp:paragraph --><p>The Classic Editor uses a toolbar similar to a word processor. You can format text, create links, and structure content without writing HTML.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Format text in the Classic Editor","steps":[{"text":"Use the toolbar buttons for Bold, Italic, and Strikethrough formatting."},{"text":"Create bullet lists or numbered lists using the list buttons."},{"text":"Use the blockquote button to add a quote block."},{"text":"Select text and click the link button to add a hyperlink. Use the unlink button to remove it."},{"text":"Use the Paragraph dropdown to change text to headings (H2 through H6)."},{"text":"Toggle between Visual and Text tabs to switch between visual editing and HTML."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"classic-editor-formatting","caption":"Classic Editor toolbar with formatting options"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: Use the Text tab only when you need to add custom HTML. The Visual tab is safer for most editing tasks.</p><!-- /wp:paragraph -->';
}

function gwi_seed_classic_formatting_fi(): string
{
    return '<!-- wp:paragraph --><p>Perinteinen editori käyttää tekstinkäsittelyohjelman kaltaista työkaluriviä. Voit muotoilla tekstiä, luoda linkkejä ja rakentaa sisältöä ilman HTML-kirjoittamista.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Muotoile tekstiä perinteisessä editorissa","steps":[{"text":"Käytä työkalurivin painikkeita Lihavoitu, Kursivoitu ja Yliviivattu -muotoiluun."},{"text":"Luo listoja tai numeroituja listoja käyttämällä listapainikkeita."},{"text":"Käytä lainauspainiketta lisätäksesi lainauslohkon."},{"text":"Valitse teksti ja klikkaa linkkipainiketta lisätäksesi hyperlinkin. Käytä linkinpoistopainiketta poistaaksesi sen."},{"text":"Käytä Kappale-pudotusvalikkoa muuttaaksesi tekstiksi otsikoiksi (H2–H6)."},{"text":"Vaihda Visuaalinen ja Teksti -välilehtien välillä siirtyäksesi visuaalisen muokkauksen ja HTML:n välillä."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"classic-editor-formatting","caption":"Perinteisen editorin työkalurivi muotoiluvaihtoehdoilla"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Käytä Teksti-välilehteä vain kun tarvitsee lisätä mukautettua HTML:ää. Visuaalinen välilehti on turvallisempi useimmille muokkaustehtäville.</p><!-- /wp:paragraph -->';
}

function gwi_seed_classic_media_en(): string
{
    return '<!-- wp:paragraph --><p>In the Classic Editor, you add images and files using the Add Media button above the toolbar. Media can be inserted inline or as galleries.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Add media in the Classic Editor","steps":[{"text":"Place your cursor where you want to insert media in the content."},{"text":"Click the Add Media button above the editor toolbar."},{"text":"Choose an existing file from the Media Library or upload a new one."},{"text":"Set the alignment, link, and size options in the attachment display settings."},{"text":"Click Insert into post to add the media to your content."},{"text":"To create a gallery, select multiple images and choose Create Gallery."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"classic-editor-new","caption":"Classic Editor with Add Media button area"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Tip: After inserting an image, click it and use the pencil icon to edit its details without re-uploading.</p><!-- /wp:paragraph -->';
}

function gwi_seed_classic_media_fi(): string
{
    return '<!-- wp:paragraph --><p>Perinteisessä editorissa lisäät kuvia ja tiedostoja käyttämällä Lisää media -painiketta työkalurivin yläpuolella. Media voidaan sisällyttää riville tai gallerioiksi.</p><!-- /wp:paragraph -->'
        . '<!-- wp:general-wp-instructions/step-list {"title":"Lisää mediaa perinteisessä editorissa","steps":[{"text":"Aseta kursori paikkaan, johon haluat lisätä median sisällössä."},{"text":"Klikkaa Lisää media -painiketta editorin työkalurivin yläpuolella."},{"text":"Valitse olemassa oleva tiedosto mediakirjastosta tai lataa uusi."},{"text":"Aseta tasaus, linkki ja kokovaihtoehdot liitetiedoston näyttöasetuksissa."},{"text":"Klikkaa Lisää artikkeliin lisätäksesi median sisältöösi."},{"text":"Luodaksesi gallerian valitse useita kuvia ja valitse Luo galleria."}]} /-->'
        . '<!-- wp:general-wp-instructions/highlighted-screenshot -->{"screenshotId":"classic-editor-new","caption":"Perinteinen editori Lisää media -painikkeen alueella"}<!-- /wp:general-wp-instructions/highlighted-screenshot -->'
        . '<!-- wp:paragraph --><p>Vinkki: Kuvan lisäämisen jälkeen klikkaa sitä ja käytä lyijykynäkuvaa muokataksesi sen tietoja lataamatta uudelleen.</p><!-- /wp:paragraph -->';
}
