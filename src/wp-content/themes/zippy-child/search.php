<?php
/**
 * Search results template.
 *
 * @package zippy-child
 */

get_header();

$search_query = get_search_query();
?>

<div class="zippy-search-page zippy-site-container">
    <section class="zippy-search-hero container">
        <p class="zippy-search-hero__eyebrow"><?php esc_html_e('Search', 'zippy-child'); ?></p>

        <h1 class="zippy-search-hero__title">
            <?php
            if ($search_query !== '') {
                printf(
                    /* translators: %s: Search query. */
                    esc_html__('Search results for "%s"', 'zippy-child'),
                    esc_html($search_query)
                );
            } else {
                esc_html_e('Search results', 'zippy-child');
            }
            ?>
        </h1>

        <form class="zippy-search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <label class="screen-reader-text" for="zippy-search-input"><?php esc_html_e('Search for:', 'zippy-child'); ?></label>
            <input
                id="zippy-search-input"
                class="zippy-search-form__input"
                type="search"
                name="s"
                value="<?php echo esc_attr($search_query); ?>"
                placeholder="<?php esc_attr_e('Search...', 'zippy-child'); ?>"
            />
            <button class="zippy-search-form__button zippy-button" type="submit">
                <?php esc_html_e('Search', 'zippy-child'); ?>
            </button>
        </form>
    </section>

    <section class="zippy-search-results container">
        <?php if (have_posts()) : ?>
            <div class="zippy-search-results__grid">
                <?php while (have_posts()) : ?>
                    <?php the_post(); ?>
                    <?php
                    $post_type_object = get_post_type_object(get_post_type());
                    $post_type_label = $post_type_object && isset($post_type_object->labels->singular_name)
                        ? $post_type_object->labels->singular_name
                        : get_post_type();
                    $excerpt = get_the_excerpt();
                    if ($excerpt === '') {
                        $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 28, '...');
                    }
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('zippy-search-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a class="zippy-search-card__media" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                <?php
                                the_post_thumbnail(
                                    'medium_large',
                                    array(
                                        'class' => 'zippy-search-card__image',
                                        'loading' => 'lazy',
                                    )
                                );
                                ?>
                            </a>
                        <?php endif; ?>

                        <div class="zippy-search-card__body">
                            <p class="zippy-search-card__meta">
                                <span><?php echo esc_html($post_type_label); ?></span>
                                <span aria-hidden="true">&middot;</span>
                                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>">
                                    <?php echo esc_html(get_the_date('M d, Y')); ?>
                                </time>
                            </p>

                            <h2 class="zippy-search-card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <?php if ($excerpt !== '') : ?>
                                <p class="zippy-search-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>

                            <a class="zippy-search-card__link" href="<?php the_permalink(); ?>">
                                <span><?php esc_html_e('Read More', 'zippy-child'); ?></span>
                                <span aria-hidden="true">&#8594;</span>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            the_posts_pagination(
                array(
                    'mid_size'  => 1,
                    'prev_text' => esc_html__('Previous', 'zippy-child'),
                    'next_text' => esc_html__('Next', 'zippy-child'),
                )
            );
            ?>
        <?php else : ?>
            <div class="zippy-search-empty">
                <h2 class="zippy-search-empty__title"><?php esc_html_e('No results found', 'zippy-child'); ?></h2>
                <p class="zippy-search-empty__text"><?php esc_html_e('Try searching again with another keyword.', 'zippy-child'); ?></p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php
get_footer();
