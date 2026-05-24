<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Character and phrase fixes for Finnish instruction content (ASCII fallbacks, leftovers).
 *
 * @return array<string, string>
 */
function gwi_finnish_text_replacements(): array
{
    return [
        'Language' => 'Kieli',
        'Finnish' => 'Suomi',
        'English' => 'Englanti',
        'Tip:' => 'Vinkki:',
        'Add New' => 'Lisää uusi',
        'Add Media' => 'Lisää media',
        'Pikachecklist' => 'Pikatarkistuslista',
        'Työ lohkojen kanssa' => 'Työskentele lohkojen kanssa',
        'relevantit asiasanat' => 'sopivat asiasanat',
        'Valitse malli tarjoaako teemasi sivumalleja.' => 'Valitse sivupohja, jos teemasi tarjoaa sivupohjia.',
        'Mene Kommentit nähdäksesi' => 'Siirry Kommentit-valikkoon nähdäksesi',
        'Mene Asetukset nähdäksesi' => 'Siirry Asetukset-valikkoon nähdäksesi',
        'Mene Artikkelit-valikkoon nähdäksesi' => 'Siirry Artikkelit-valikkoon nähdäksesi',
        'Mene Artikkelit-valikkoon ja' => 'Siirry Artikkelit-valikkoon ja',
        'Mene Sivut-valikkoon ja' => 'Siirry Sivut-valikkoon ja',
        'Mene Ulkoasu-valikkoon ja' => 'Siirry Ulkoasu-valikkoon ja',
        'Mene Käyttäjät-näkymään' => 'Siirry Käyttäjät-valikkoon',
        'Visuaalinen-välilehti' => 'Visuaalinen välilehti',
        'identtisena kaikkialla' => 'identtisenä kaikkialla',
        'täyty ymmärtää' => 'täytyy ymmärtää',
        'täyty suorittaa' => 'täytyy suorittaa',
        'kayttaytyvat' => 'käyttäytyvät',
        'kayttaytyy' => 'käyttäytyy',
        'kayttajatilin' => 'käyttäjätilin',
        'kayttajan' => 'käyttäjän',
        'kayttajat' => 'käyttäjät',
        'kayttajia' => 'käyttäjiä',
        'kayttaja' => 'käyttäjä',
        'kayttaa' => 'käyttää',
        'kayttoa' => 'käyttöä',
        'kaytto' => 'käyttö',
        'kayta' => 'käytä',
        'Kayta' => 'Käytä',
        'Kayttajat' => 'Käyttäjät',
        'Paakayttaja' => 'Pääkäyttäjä',
        'Paakayttajilla' => 'Pääkäyttäjillä',
        'sisaltloosiot' => 'sisältöosiot',
        'sisaltoosioiden' => 'sisältöosioiden',
        'sisaltoon' => 'sisältöön',
        'sisaltoosi' => 'sisältöösi',
        'sisaltoa' => 'sisältöä',
        'sisaltosi' => 'sisältösi',
        'sisalto' => 'sisältö',
        'Sisalto' => 'Sisältö',
        'tyokalurivissa' => 'työkalurivissä',
        'tyokalurivin' => 'työkalurivin',
        'tyokalurivia' => 'työkaluriviä',
        'tyokalurivi' => 'työkalurivi',
        'tyokaluja' => 'työkaluja',
        'lisaajan' => 'lisääjän',
        'lisaaja' => 'lisääjä',
        'lisata' => 'lisätä',
        'Lisaa' => 'Lisää',
        'lisaa' => 'lisää',
        'nähdaksesi' => 'nähdäksesi',
        'nhdäksesi' => 'nähdäksesi',
        'tehda' => 'tehdä',
        'maarittaa' => 'määrittää',
        'sahkoposti' => 'sähköposti',
        'aanitiedostosi' => 'äänitiedostosi',
        'aania' => 'ääntä',
        'Aani' => 'Ääni',
        'valilehtien valilla' => 'välilehtien välillä',
        'valilyönnista' => 'välilyönnistä',
        'valilyontielementeilla' => 'välielementeillä',
        'Valilyönti' => 'Välilyönti',
        'valilla' => 'välillä',
        'jarjestetty' => 'järjestetty',
        'jarjestamaton' => 'järjestämätön',
        'riveilla' => 'riveillä',
        'levealla' => 'leveällä',
        'yhta esiintymaa itsenaisesti' => 'yhtä esiintymää itsenäisesti',
        'esiintymat paivittyvat' => 'esiintymät päivittyvät',
        'naytyy' => 'näkyy',
        'niita' => 'niitä',
        'kaytettavia' => 'käytettäviä',
        'uudelleenkaytettava' => 'uudelleenkäytettävä',
        'Uudelleenkaytettavat' => 'Uudelleenkäytettävät',
        'Uudelleenkaytettavien' => 'Uudelleenkäytettävien',
        'paasioihin' => 'pääosioihin',
        'valimuisti' => 'välimuisti',
        'Valimuisti' => 'Välimuisti',
        'asetteluohkoja' => 'asettelulohkoja',
        'kirjoita ## seettuna valilyönnista' => 'kirjoita ## ja välilyönti',
        'Mene Media-nähdäksesi kaikki ladatut tiedostot.' => 'Siirry Media-valikkoon nähdäksesi kaikki ladatut tiedostot.',
        'Käytä asetteluohkoja' => 'Käytä asettelulohkoja',
        '<ul><i>' => '<ul><li>',
    ];
}

function gwi_normalize_finnish_text(string $text): string
{
    if ($text === '') {
        return $text;
    }

    $replacements = gwi_finnish_text_replacements();
    uksort(
        $replacements,
        static function (string $left, string $right): int {
            return strlen($right) <=> strlen($left);
        }
    );

    return strtr($text, $replacements);
}

function gwi_filter_finnish_instruction_content(string $content): string
{
    if (!is_singular('wp_instruction') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();

    if (!$post_id || gwi_get_instruction_language($post_id) !== 'fi') {
        return $content;
    }

    return gwi_normalize_finnish_text($content);
}

add_filter('the_content', 'gwi_filter_finnish_instruction_content', 18);
