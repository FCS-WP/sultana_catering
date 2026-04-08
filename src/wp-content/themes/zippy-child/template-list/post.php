<?php

if (!isset($getlist_posts) || !($getlist_posts instanceof WP_Query)) {
    return;
}
?>
<div class="zippy-post-list row g-4">
    <?php while ($getlist_posts->have_posts()) : $getlist_posts->the_post(); ?>
        <div class="col-12">
            <article class="zippy-post-list__item">
                <h3 class="zippy-post-list__title">
                    <a href="<?php echo esc_url(get_permalink()); ?>">
                        <?php the_title(); ?>
                    </a>
                </h3>
                <p class="zippy-post-list__excerpt">
                    <?php echo esc_html(wp_trim_words(wp_strip_all_tags((string) get_the_excerpt()), 30, '...')); ?>
                </p>
            </article>
        </div>
    <?php endwhile; ?>
</div>
