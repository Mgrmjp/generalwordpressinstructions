<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <section class="manual-grid">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <article <?php post_class('manual-card'); ?>>
                <div class="manual-card__meta">
                    <span class="manual-eyebrow"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? __('Post', 'instruction-manual')); ?></span>
                </div>
                <h2 class="manual-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php if (get_post_type() === 'wp_instruction') : ?>
                    <?php $excerpt = manual_instruction_card_excerpt(get_the_ID()); ?>
                    <?php if ($excerpt !== '') : ?>
                        <p class="manual-card__summary"><?php echo esc_html($excerpt); ?></p>
                    <?php endif; ?>
                <?php else : ?>
                    <?php the_excerpt(); ?>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </section>
    <?php the_posts_pagination(); ?>
<?php else : ?>
    <section class="manual-hero">
        <p class="manual-eyebrow"><?php esc_html_e('No content', 'instruction-manual'); ?></p>
        <h1 class="manual-title"><?php esc_html_e('No instructions found', 'instruction-manual'); ?></h1>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
