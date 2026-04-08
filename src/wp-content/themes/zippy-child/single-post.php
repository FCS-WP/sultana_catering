<?php
/**
 * Blog detail template.
 *
 * @package zippy-child
 */

get_header();
?>

<div class="zippy-blog-detail zippy-site-container">
    <div class="zippy-blog-detail__shell container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : ?>
                <?php the_post(); ?>
                <?php
                $current_post_id = get_the_ID();
                $related_category_ids = wp_get_post_categories($current_post_id);
                $related_posts_args = array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'posts_per_page'      => 3,
                    'post__not_in'        => array($current_post_id),
                    'ignore_sticky_posts' => true,
                    'no_found_rows'       => true,
                );

                if (!empty($related_category_ids)) {
                    $related_posts_args['category__in'] = $related_category_ids;
                }

                $related_posts = new WP_Query($related_posts_args);
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('zippy-blog-detail__article'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="zippy-blog-detail__media">
                            <?php
                            the_post_thumbnail(
                                'large',
                                array(
                                    'class' => 'zippy-blog-detail__image',
                                    'loading' => 'eager',
                                )
                            );
                            ?>
                        </div>
                    <?php endif; ?>

                    <header class="zippy-blog-detail__header">
                        <h1 class="zippy-blog-detail__title"><?php the_title(); ?></h1>

                        <div class="zippy-blog-detail__meta">
                            <time class="zippy-blog-detail__date" datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                <?php echo esc_html(get_the_date('M d, Y')); ?>
                            </time>
                            <span class="zippy-blog-detail__separator" aria-hidden="true">&middot;</span>
                            <span class="zippy-blog-detail__author"><?php echo esc_html(get_the_author()); ?></span>
                        </div>
                    </header>

                    <div class="zippy-blog-detail__description">
                        <?php
                        the_content();

                        wp_link_pages(
                            array(
                                'before' => '<div class="zippy-blog-detail__pagination">',
                                'after'  => '</div>',
                            )
                        );
                        ?>
                    </div>
                </article>

                <?php if ($related_posts->have_posts()) : ?>
                    <section class="zippy-blog-related" aria-labelledby="zippy-blog-related-title">
                        <h2 class="zippy-blog-related__title" id="zippy-blog-related-title">
                            <?php esc_html_e('Related Blogs', 'zippy-child'); ?>
                        </h2>

                        <div class="zippy-blog-related__grid">
                            <?php while ($related_posts->have_posts()) : ?>
                                <?php $related_posts->the_post(); ?>

                                <article class="zippy-blog-related__item">
                                    <a class="zippy-blog-related__link" href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <span class="zippy-blog-related__media">
                                                <?php
                                                the_post_thumbnail(
                                                    'medium_large',
                                                    array(
                                                        'class' => 'zippy-blog-related__image',
                                                        'loading' => 'lazy',
                                                    )
                                                );
                                                ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="zippy-blog-related__body">
                                            <span class="zippy-blog-related__meta">
                                                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                                    <?php echo esc_html(get_the_date('M d, Y')); ?>
                                                </time>
                                                <span aria-hidden="true">&middot;</span>
                                                <span><?php echo esc_html(get_the_author()); ?></span>
                                            </span>
                                            <span class="zippy-blog-related__post-title"><?php the_title(); ?></span>
                                        </span>
                                    </a>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
