<?php
$manual_footer = manual_footer_context();
$manual_footer_year = date_i18n('Y');
?>
</main>
<footer class="manual-site-footer">
    <div class="manual-site-footer__inner">
        <div class="manual-site-footer__brand">
            <a class="manual-site-footer__title" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo esc_html($manual_footer['brand']); ?>
            </a>
            <p class="manual-site-footer__tagline"><?php echo esc_html($manual_footer['tagline']); ?></p>
        </div>

        <nav class="manual-site-footer__nav" aria-label="<?php echo esc_attr($manual_footer['nav_label']); ?>">
            <ul class="manual-site-footer__links">
                <li>
                    <a href="<?php echo esc_url(home_url('/')); ?>"<?php echo is_front_page() && !manual_is_glossary_view() ? ' aria-current="page"' : ''; ?>>
                        <?php echo esc_html($manual_footer['start']); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(manual_instruction_archive_url()); ?>"<?php echo is_post_type_archive('wp_instruction') || is_tax('instruction_category') ? ' aria-current="page"' : ''; ?>>
                        <?php echo esc_html($manual_footer['guides']); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(home_url('/#polut')); ?>">
                        <?php echo esc_html($manual_footer['paths']); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(manual_glossary_url()); ?>"<?php echo manual_is_glossary_view() ? ' aria-current="page"' : ''; ?>>
                        <?php echo esc_html($manual_footer['glossary']); ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="manual-site-footer__bar">
        <div class="manual-site-footer__inner manual-site-footer__inner--bar">
            <p class="manual-site-footer__meta">
                <span>&copy; <?php echo esc_html($manual_footer_year); ?> <?php echo esc_html($manual_footer['brand']); ?></span>
            </p>
            <p class="manual-site-footer__meta">
                <?php echo esc_html($manual_footer['credit']); ?>
                <a
                    class="manual-site-footer__credit"
                    href="https://www.linkedin.com/in/miikkamgr/"
                    rel="noopener noreferrer"
                    target="_blank"
                >Miikka</a>
            </p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
