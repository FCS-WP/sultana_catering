<?php
/*
Template Name: Template About Us
Template Post Type: page
*/

get_header();

$about_placeholder_text = get_field('about_placeholder_text') ?: 'I PUT PLACEHOLDER PHOTO HERE FIRST';
$about_page_heading = get_field('about_page_heading') ?: 'About us';
$about_hero_text = get_field('about_hero_text') ?: 'Voted one of the Top 10 best steakhouses in New York City, Boucherie is a masterful blend of ultra-stylish contemporary design and time-honored steakhouse artistry. All of our prime steaks and chops are dry-aged on-site and served by a dedicated and knowledgeable staff each with a minimum of 10 years steakhouse experience. The setting is primed to impress: whether for a romantic dinner, a power lunch, a client dinner or a family celebration.';
$about_hero_image_url = zippy_child_get_acf_image_url(get_field('about_hero_image'));

$about_connect_title = get_field('about_connect_title') ?: 'We connect farmers, butchers and cooks';
$about_connect_text = get_field('about_connect_text') ?: 'The critical moment for us was the decision to have our own meat. We started to work with butchers and farmers, and together we found a way to age beef. We learned to make our own sausages and have brought the meat of heritage Prestice pigs back on to plates.';
$about_connect_image_left = zippy_child_get_acf_image_url(get_field('about_connect_image_left'));

$about_kitchen_title = get_field('about_kitchen_title') ?: 'See all the way into our kitchen';
$about_kitchen_text = get_field('about_kitchen_text') ?: 'Our menu mirrors the restaurant design in marrying inspiration from old school butcher shops with modern steakhouse cuts and presentations, plus the unexpected flare that Quality Branded has come to be known for. Dishes like our renowned Slab Bacon, Peanut Butter and Jalapeno, the Tomahawk Ribsteak, The Three Filets and Corn Creme Brulee are menu staples, and tableside presentations add even more excitement to any meal.';
$about_kitchen_image_left = zippy_child_get_acf_image_url(get_field('about_kitchen_image_left'));
$about_kitchen_button_text = get_field('about_kitchen_button_text') ?: 'View Our Menu';
$about_kitchen_button_url = get_field('about_kitchen_button_url') ?: home_url('/menu/');

$review_title = get_field('review_title') ?: 'See what the critics say';
$review_items = get_field('review_items') ?: array();
$review_items = is_array($review_items) ? $review_items : array();



$render_media = static function ($url, $alt, $wrapper_classes = '', $image_classes = 'zippy-about-media__image') use ($about_placeholder_text) {
    $wrapper_class = trim('zippy-about-media ' . $wrapper_classes);
?>
    <div class="<?php echo esc_attr($wrapper_class); ?>">
        <?php if ($url !== '') : ?>
            <img class="<?php echo esc_attr($image_classes); ?>" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr((string) $alt); ?>" loading="lazy" />
        <?php else : ?>
            <div class="zippy-about-media__placeholder">
                <?php echo esc_html($about_placeholder_text); ?>
            </div>
        <?php endif; ?>
    </div>
<?php
};
?>

<div class="zippy-about zippy-site-container">
    <section class="zippy-about-hero container">
        <div class="zippy-about-shell text-center">
            <h1 class="zippy-about-hero__title"><?php echo esc_html($about_page_heading); ?></h1>
        </div>

        <div class="zippy-about-bleed">
            <?php $render_media($about_hero_image_url, $about_page_heading, 'zippy-about-hero__media'); ?>
        </div>
    </section>

    <section class="zippy-about-intro container">
        <div class="zippy-about-shell py-lg-3 py-2">
            <div class="zippy-about-intro__inner">
                <div class="zippy-about-intro__content">
                    <?php echo wpautop(wp_kses_post($about_hero_text)); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="zippy-about-band bg-black py-2 mt-lg-5 ">
        <div class="zippy-about-shell container">
            <div class="row g-4 g-lg-5 zippy-about-band__row">
                <div class="col-12 col-md-6 p-2">
                    <article class="zippy-about-band-card zippy-about-band-card--offset-down">
                        <div class="mb-1 mb-lg-2">
                            <?php $render_media($about_connect_image_left, $about_connect_title, 'zippy-about-band-card__media'); ?>
                        </div>
                        <div class="zippy-about-band-card__body">
                            <h2 class="zippy-about-band-card__title text-white"><?php echo nl2br(esc_html($about_connect_title)); ?></h2>
                            <?php if ($about_connect_text !== '') : ?>
                                <div class="zippy-about-band-card__text text-white"><?php echo wpautop(wp_kses_post($about_connect_text)); ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-md-6 p-2">
                    <article class="zippy-about-band-card zippy-about-band-card--offset-up">
                        <div class="mb-1 mb-lg-2">
                            <?php $render_media($about_kitchen_image_left, $about_kitchen_title, 'zippy-about-band-card__media zippy-about-band-card__media--tall'); ?>
                        </div>
                        <div class="zippy-about-band-card__body">
                            <h2 class="zippy-about-band-card__title text-white"><?php echo nl2br(esc_html($about_kitchen_title)); ?></h2>
                            <?php if ($about_kitchen_text !== '') : ?>
                                <div class="zippy-about-band-card__text text-white"><?php echo wpautop(wp_kses_post($about_kitchen_text)); ?></div>
                            <?php endif; ?>
                            <?php if ($about_kitchen_button_text !== '') : ?>
                                <a class="zippy-about-link zippy-about-link--light zippy-button zippy-button--ghost-light text-white" href="<?php echo esc_url($about_kitchen_button_url); ?>">
                                    <span><?php echo esc_html($about_kitchen_button_text); ?></span>
                                    <span aria-hidden="true">&#8594;</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
    <?php if (!empty($review_items)) : ?>
        <section class="reviews zippy-about-reviews container">
            <div class="zippy-about-shell py-3">
                <h2 class="zippy-about-reviews__title text-center"><?php echo esc_html($review_title); ?></h2>
                <div class="zippy-about-reviews__inner row g-3 g-lg-4">
                    <?php foreach ($review_items as $review) : ?>
                        <?php
                        $review_rating = isset($review['start']) ? (int) $review['start'] : 5;
                        $review_rating = max(0, min(5, $review_rating));
                        $review_author = isset($review['name']) ? trim((string) $review['name']) : '';
                        $review_text = isset($review['description']) ? trim((string) $review['description']) : '';
                        $review_initial = $review_author !== '' ? strtoupper(substr($review_author, 0, 1)) : 'R';
                        ?>
                        <div class="col-12 col-lg-4 d-flex p-1">
                            <article class="zippy-about-review">
                                <div class="zippy-about-review__head">
                                    <div class="zippy-about-review__stars" aria-label="<?php echo esc_attr(sprintf(__('Rated %d out of 5', 'zippy-child'), $review_rating)); ?>">
                                        <?php for ($star_index = 1; $star_index <= 5; $star_index++) : ?>
                                            <span class="zippy-about-review__star <?php echo $star_index <= $review_rating ? 'is-active' : ''; ?>" aria-hidden="true">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="zippy-about-review__text">
                                    <?php echo nl2br(esc_html($review_text)); ?>
                                </div>

                                <div class="zippy-about-review__footer">
                                    <span class="zippy-about-review__avatar" aria-hidden="true"><?php echo esc_html($review_initial); ?></span>
                                    <div class="zippy-about-review__author-group">
                                        <p class="zippy-about-review__author"><?php echo esc_html($review_author); ?></p>
                                        <p class="zippy-about-review__role"><?php esc_html_e('Customer Review', 'zippy-child'); ?></p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php
get_footer();
