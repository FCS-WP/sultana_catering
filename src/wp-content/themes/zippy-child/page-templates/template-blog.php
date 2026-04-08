<?php
/*
Template Name: Template Blog
Template Post Type: page
*/

get_header();

$blog_placeholder_text = get_field('blog_placeholder_text') ?: 'I PUT PLACEHOLDER PHOTO HERE FIRST';
$blog_page_heading = get_field('blog_page_heading') ?: 'Blog';
$blog_hero_image_url = zippy_child_get_acf_image_url(get_field('blog_hero_image'));
$blog_posts_count = (int) (get_field('blog_posts_count') ?: 6);
if ($blog_posts_count <= 0) {
    $blog_posts_count = 6;
}
$blog_empty_text = get_field('blog_empty_text') ?: 'No blog posts are available yet. Add a few posts and they will appear here automatically.';

$blog_posts = new WP_Query(array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $blog_posts_count,
    'ignore_sticky_posts' => true,
));

$render_media = static function ($url, $alt, $wrapper_classes = '', $image_classes = 'zippy-blog-media__image') use ($blog_placeholder_text) {
    $wrapper_class = trim('zippy-blog-media ' . $wrapper_classes);
    ?>
    <div class="<?php echo esc_attr($wrapper_class); ?>">
        <?php if ($url !== '') : ?>
            <img class="<?php echo esc_attr($image_classes); ?>" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr((string) $alt); ?>" loading="lazy" />
        <?php else : ?>
            <div class="zippy-blog-media__placeholder">
                <?php echo esc_html($blog_placeholder_text); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
};
?>

<div class="zippy-blog-page zippy-site-container">
    <section class="zippy-blog-hero container">
        <div class="text-center">
            <h1 class="zippy-blog-hero__title"><?php echo esc_html($blog_page_heading); ?></h1>
        </div>

        <div class="zippy-blog-bleed">
            <?php $render_media($blog_hero_image_url, $blog_page_heading, 'zippy-blog-hero__media'); ?>
        </div>
    </section>

    <section class="zippy-blog-archive container">
        <div class="zippy-blog-list">
            <?php if ($blog_posts->have_posts()) : ?>
                <div class="row g-4 zippy-blog-grid">
                    <?php while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
                        <?php
                        $post_id = get_the_ID();
                        $image_url = get_the_post_thumbnail_url($post_id, 'large');
                        $excerpt = get_the_excerpt();
                        if ($excerpt === '') {
                            $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 24, '...');
                        }
                        ?>
                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="zippy-blog-card">
                                <?php $render_media($image_url ?: '', get_the_title(), 'zippy-blog-card__media'); ?>

                                <div class="zippy-blog-card__body">
                                    <p class="zippy-blog-card__meta">
                                        <span><?php echo esc_html(get_the_date('M d, Y')); ?></span>
                                        <span aria-hidden="true">&bull;</span>
                                        <span><?php echo esc_html(get_the_author()); ?></span>
                                    </p>

                                    <h3 class="zippy-blog-card__title">
                                        <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <?php if ($excerpt !== '') : ?>
                                        <p class="zippy-blog-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                                    <?php endif; ?>

                                    <a class="zippy-blog-link zippy-blog-link--small" href="<?php echo esc_url(get_permalink()); ?>">
                                        <span><?php esc_html_e('Read More', 'zippy-child'); ?></span>
                                        <span aria-hidden="true">&#8594;</span>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="zippy-blog-empty">
                    <p class="zippy-blog-empty__text"><?php echo esc_html($blog_empty_text); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
wp_reset_postdata();
get_footer();
