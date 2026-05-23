</main>
<?php
$manual_footer_language = manual_instruction_sanitize_language(get_query_var('instruction_language'));

if (is_singular('wp_instruction')) {
    $manual_footer_language = manual_instruction_language_code((int) get_queried_object_id());
}

$manual_footer_text = $manual_footer_language === 'en'
    ? __('Guides are written clearly and kept current. Did not find what you needed? Contact us and we will help.', 'instruction-manual')
    : __('Ohjeet on kirjoitettu selkeästi ja pidetään ajan tasalla. Etkö löytänyt etsimääsi? Ota yhteyttä – autamme mielellämme.', 'instruction-manual');
?>
<footer class="manual-site-footer">
    <div class="manual-site-footer__inner">
        <span class="manual-footer-icon" aria-hidden="true">i</span>
        <p><?php echo esc_html($manual_footer_text); ?></p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
