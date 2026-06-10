<?php

if (!defined('ABSPATH')) {
    exit;
}

$language = isset($args['language']) ? manual_instruction_sanitize_language((string) $args['language']) : 'fi';
$cards = manual_flexible_layout_legend($language);
$is_fi = $language === 'fi';
$summary = $is_fi
    ? __('Näytä osiotyypit (mitä täytät ja milloin)', 'instruction-manual')
    : __('Show section types (what to fill in and when)', 'instruction-manual');
?>
<details class="manual-flexible-legend">
    <summary class="manual-flexible-legend__summary"><?php echo esc_html($summary); ?></summary>
    <div class="manual-flexible-legend__body">
        <p class="manual-flexible-legend__intro">
            <?php echo esc_html($is_fi
                ? __('Nimet voivat poiketa hieman sivustoltasi—valitse lähin vastine listasta. Esimerkit ovat alla.', 'instruction-manual')
                : __('Names may differ slightly on your site—pick the closest match in the list. Examples are below.', 'instruction-manual')); ?>
        </p>
        <div class="manual-flexible-legend__grid">
            <?php foreach ($cards as $card) : ?>
                <article class="manual-flexible-legend__card" data-gwi-layout="<?php echo esc_attr((string) $card['layout']); ?>">
                    <h3 class="manual-flexible-legend__card-title"><?php echo esc_html((string) $card['label']); ?></h3>
                    <dl class="manual-flexible-legend__meta">
                        <div>
                            <dt><?php echo esc_html($is_fi ? __('Mitä täytät', 'instruction-manual') : __('What you fill in', 'instruction-manual')); ?></dt>
                            <dd><?php echo esc_html((string) $card['fields']); ?></dd>
                        </div>
                        <div>
                            <dt><?php echo esc_html($is_fi ? __('Käytä kun', 'instruction-manual') : __('Use for', 'instruction-manual')); ?></dt>
                            <dd><?php echo esc_html((string) $card['use_for']); ?></dd>
                        </div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</details>
