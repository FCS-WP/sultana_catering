<?php
/*
Template Name: Template Home
Template Post Type: page
*/

get_header();

$hero_title = get_field('hero_title') ?: 'YOUR TRUSTED CORPORATE CATERING SERVICE';
$hero_button_text = get_field('hero_button_text') ?: 'Order Now';
$hero_button_url = get_field('hero_button_url') ?: '#';

$about_title = get_field('about_title') ?: 'SULTANA CATERING';
$about_description = get_field('about_description') ?: 'Suhra Catering began in 2012 with a simple goal: to provide fresh, delicious, and reliable meals for offices and industrial teams in UAE. What started as small lunch service to nearby factories has grown into a trusted corporate catering service, offering Western, Malay, and Indian meals prepared with care and delivered on time to keep teams satisfied and productive.';

$promotions_title = get_field('promotions_title') ?: 'PROMOTIONS TODAY';
$promotions_items = get_field('promotions_items') ?: array();
$promotions_items = is_array($promotions_items) ? $promotions_items : array();

$most_ordered_title = get_field('most_ordered_title') ?: 'MOST ORDERED';
$most_ordered_subtitle = get_field('most_ordered_subtitle') ?: 'The most commonly ordered items and dishes from this menu';

$secondary_title = get_field('secondary_title') ?: "Also I'd Salty";
$secondary_subtitle = get_field('secondary_subtitle') ?: '';
$secondary_products = get_field('secondary_products') ?: array();
$secondary_products = is_array($secondary_products) ? $secondary_products : array();

$bottom_title = get_field('bottom_title') ?: 'Aloo Ki Sabji';
$bottom_subtitle = get_field('bottom_subtitle') ?: '';
$bottom_products = get_field('bottom_products') ?: array();
$bottom_products = is_array($bottom_products) ? $bottom_products : array();

$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/');

$build_product_cards = function ($selected_products) {
    $cards = [];

    if (is_array($selected_products) && !empty($selected_products)) {
        foreach ($selected_products as $product_item) {
            $product_id = is_object($product_item) ? (int) $product_item->ID : (int) $product_item;
            if ($product_id <= 0) {
                continue;
            }

            $title = get_the_title($product_id);
            if (empty($title)) {
                continue;
            }

            $description = get_post_field('post_excerpt', $product_id);
            if (empty($description)) {
                $description = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $product_id)), 16, '...');
            }

            $image_url = get_the_post_thumbnail_url($product_id, 'medium');
            if (empty($image_url)) {
                if (function_exists('wc_placeholder_img_src')) {
                    $image_url = wc_placeholder_img_src('medium');
                } else {
                    $image_url = '';
                }
            }

            $price_html = '';
            if (function_exists('wc_get_product')) {
                $product = wc_get_product($product_id);
                if ($product) {
                    $price_html = $product->get_price_html();
                }
            }

            $cards[] = [
                'product_id' => $product_id,
                'title' => $title,
                'description' => $description,
                'image_url' => $image_url,
                'price_html' => $price_html,
            ];
        }
    }

    return $cards;
};

$build_home_products_markup = function ($term_id = 0) {
    if (!shortcode_exists('GET_LIST')) {
        return '';
    }

    $shortcode = '[GET_LIST posts_per_page="4" post_type="product" filter="false" pagination="false" template="/template-list/product.php"';
    if ((int) $term_id > 0) {
        $shortcode .= ' taxonomy="product_cat" term_id="' . (int) $term_id . '"';
    }
    $shortcode .= ']';

    return do_shortcode($shortcode);
};

$render_product_card_grid = function ($cards, $section_key) use ($cart_url) {
    if (empty($cards)) {
        return '';
    }

    ob_start();
    ?>
    <div class="zippy-home-product-grid row g-4">
        <?php foreach ($cards as $card_index => $card) : ?>
            <?php
            $product_id = isset($card['product_id']) ? (int) $card['product_id'] : 0;
            $product = function_exists('wc_get_product') && $product_id > 0 ? wc_get_product($product_id) : null;
            $qty_input_id = 'zippy-home-qty-' . sanitize_key((string) $section_key) . '-' . $card_index;
            $qty_min = $product ? (float) $product->get_min_purchase_quantity() : 1;
            $qty_max = $product ? $product->get_max_purchase_quantity() : '';
            $add_to_cart_href = $product ? $product->add_to_cart_url() : add_query_arg(
                array(
                    'add-to-cart' => $product_id,
                    'quantity' => 1,
                ),
                $cart_url
            );
            $add_to_cart_text = $product ? $product->add_to_cart_text() : 'Add to Cart';
            $add_to_cart_description = $product ? $product->add_to_cart_description() : '';
            $product_sku = $product ? $product->get_sku() : '';
            $is_add_to_cart_disabled = $product_id <= 0 || !$product || !$product->is_purchasable() || !$product->is_in_stock();
            $woo_button_classes = $product ? 'product_type_' . $product->get_type() . ' add_to_cart_button' : 'add_to_cart_button';
            if ($product && $product->supports('ajax_add_to_cart')) {
                $woo_button_classes .= ' ajax_add_to_cart';
            }
            $add_disabled_classes = $is_add_to_cart_disabled ? ' is-disabled disabled pe-none opacity-50' : '';
            ?>
            <div class="col-12 col-lg-6 d-flex p-1">
                <article class="zippy-home-product-card d-grid w-100 h-100" data-product-card>
                    <div class="zippy-home-product-media">
                        <?php if (!empty($card['image_url'])) : ?>
                            <img src="<?php echo esc_url($card['image_url']); ?>" alt="<?php echo esc_attr($card['title']); ?>" loading="lazy" />
                        <?php else : ?>
                            <div class="zippy-home-media-placeholder"></div>
                        <?php endif; ?>
                    </div>

                    <div class="zippy-home-product-body d-flex flex-column justify-content-between">
                        <div>
                            <h4 class="zippy-home-product-title"><?php echo esc_html($card['title']); ?></h4>
                            <?php if (!empty($card['description'])) : ?>
                                <p class="zippy-home-product-description"><?php echo esc_html($card['description']); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($card['price_html'])) : ?>
                            <!--<div class="zippy-home-price"><?php echo wp_kses_post($card['price_html']); ?></div>-->
                        <?php endif; ?>

                        <div class="zippy-home-product-actions d-flex align-items-center">
                            <div class="zippy-home-qty" data-qty-control>
                                <?php
                                if (function_exists('woocommerce_quantity_input')) {
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo woocommerce_quantity_input(
                                        array(
                                            'input_id'    => $qty_input_id,
                                            'input_value' => max(1, $qty_min),
                                            'min_value'   => max(1, $qty_min),
                                            'max_value'   => $qty_max,
                                            'classes'     => array(
                                                'input-text',
                                                'qty',
                                                'text',
                                                'zippy-home-qty__input',
                                            ),
                                        ),
                                        $product,
                                        false
                                    );
                                } else {
                                ?>
                                    <input class="input-text qty text zippy-home-qty__input" id="<?php echo esc_attr($qty_input_id); ?>" type="number" min="1" value="1" />
                                <?php
                                }
                                ?>
                            </div>

                            <a
                                class="zippy-home-add-cart zippy-button lightbox-zippy-btn <?php echo esc_attr($add_disabled_classes); ?>"
                                href="#lightbox-zippy-form"
                                data-product-id="<?php echo esc_attr($product_id); ?>"
                                data-product_id="<?php echo esc_attr($product_id); ?>"
                                data-product_sku="<?php echo esc_attr($product_sku); ?>"
                                data-product-url="<?php echo esc_url($add_to_cart_href); ?>"
                                data-quantity="<?php echo esc_attr(max(1, $qty_min)); ?>"
                                data-qty-input-id="<?php echo esc_attr($qty_input_id); ?>"
                                rel="nofollow"
                                <?php if (!empty($add_to_cart_description)) : ?>
                                aria-label="<?php echo esc_attr($add_to_cart_description); ?>"
                                <?php endif; ?>>
                                <?php echo esc_html($add_to_cart_text); ?>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
    <?php

    return ob_get_clean();
};

$secondary_cards = $build_product_cards($secondary_products);
$bottom_cards = $build_product_cards($bottom_products);

$home_tabs = [];
if (taxonomy_exists('product_cat')) {
    $product_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ));

    if (!is_wp_error($product_categories) && is_array($product_categories)) {
        foreach ($product_categories as $product_category) {
            if (!$product_category instanceof WP_Term) {
                continue;
            }

            if ((string) $product_category->slug === 'uncategorized') {
                continue;
            }

            $tab_key = sanitize_title((string) $product_category->slug);
            if ($tab_key === '') {
                $tab_key = 'product-cat-' . (int) $product_category->term_id;
            }

            $home_tabs[] = array(
                'label' => (string) $product_category->name,
                'key' => $tab_key,
                'term_id' => (int) $product_category->term_id,
            );
        }
    }
}

if (empty($home_tabs)) {
    $home_tabs[] = array(
        'label' => $most_ordered_title !== '' ? $most_ordered_title : 'Menu',
        'key' => 'all-products',
        'term_id' => 0,
    );
}

?>

<div class="zippy-home zippy-site-container" data-zippy-home data-cart-url="<?php echo esc_url($cart_url); ?>">
    <section class="zippy-home-hero position-relative overflow-hidden container">
        <div class="zippy-home-hero__badge position-absolute top-50 start-50 translate-middle text-center">
            <h1 class="zippy-home-hero__title"><?php echo esc_html($hero_title); ?></h1>
            <a class="zippy-home-hero__button zippy-button zippy-button--light" href="<?php echo esc_url($hero_button_url); ?>"><?php echo esc_html($hero_button_text); ?></a>
        </div>
    </section>

    <section class="zippy-home-about container row align-items-center gx-md-3 gx-lg-gutter gy-1">
        <h2 class="zippy-home-about__title col-lg-4"><?php echo esc_html($about_title); ?></h2>
        <div class="zippy-home-about__description col-lg-8">
            <?php echo wpautop(wp_kses_post($about_description)); ?>
        </div>
    </section>

    <?php if (!empty($promotions_items)) : ?>
        <section class="zippy-home-promotions container">
            <h3 class="zippy-home-promotions__title"><?php echo esc_html($promotions_title); ?></h3>

            <div class="container row px-2">
                <?php foreach ($promotions_items as $index => $item) : ?>
                    <?php
                    $item_title = isset($item['title']) ? $item['title'] : '';
                    $item_subtitle = isset($item['subtitle']) ? $item['subtitle'] : '';
                    $item_note = isset($item['note']) ? $item['note'] : '';
                    $item_icon = isset($item['icon']) && is_array($item['icon']) && !empty($item['icon']['url']) ? $item['icon']['url'] : '';
                    $legacy_body_mode = empty($item_note) && strlen(trim((string) $item_subtitle)) > 24;
                    ?>
                    <div class="col-12 col-md-4 d-flex p-1 mt-2 mt-lg-0">
                        <article class="zippy-home-promo-card position-relative d-flex flex-column align-items-center text-center w-100 h-100">
                            <div class="zippy-home-promo-icon position-absolute start-50 translate-middle-x d-flex align-items-center justify-content-center">
                                <?php if (!empty($item_icon)) : ?>
                                    <img src="<?php echo esc_url($item_icon); ?>" alt="" loading="lazy" />
                                <?php else : ?>
                                    <span><?php echo esc_html($index + 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <h4 class="zippy-home-promo-card__title"><?php echo esc_html($item_title); ?></h4>
                            <?php if (!empty($item_subtitle)) : ?>
                                <?php if ($legacy_body_mode) : ?>
                                    <p class="zippy-home-promo-card__body"><?php echo esc_html($item_subtitle); ?></p>
                                <?php else : ?>
                                    <h5 class="zippy-home-promo-card__subtitle"><?php echo esc_html($item_subtitle); ?></h5>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($item_note)) : ?>
                                <p class="zippy-home-promo-card__body"><?php echo esc_html($item_note); ?></p>
                            <?php endif; ?>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="zippy-home-tabs d-flex align-items-center container" aria-label="Menu categories">
        <span class="zippy-home-tabs__icon d-inline-flex flex-shrink-0 align-items-center justify-content-center" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 7H20M4 12H20M4 17H20" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
            </svg>
        </span>
        <?php foreach ($home_tabs as $tab_index => $tab) : ?>
            <button
                type="button"
                class="zippy-home-tab d-inline-flex align-items-center <?php echo $tab_index === 0 ? 'is-active' : ''; ?>"
                data-home-tab="<?php echo esc_attr($tab['key']); ?>">
                <?php echo esc_html($tab['label']); ?>
            </button>
        <?php endforeach; ?>
    </section>

    <section class="zippy-home-products container">
        <header class="zippy-home-section-head">
            <h3 class="zippy-home-section-head__title"><?php echo esc_html($most_ordered_title); ?></h3>
            <?php if (!empty($most_ordered_subtitle)) : ?>
                <p class="zippy-home-section-head__subtitle"><?php echo esc_html($most_ordered_subtitle); ?></p>
            <?php endif; ?>
        </header>

        <div class="zippy-home-products-panels" data-home-products-panels>
            <?php foreach ($home_tabs as $tab_index => $tab) : ?>
                <?php
                $tab_term_id = isset($tab['term_id']) ? (int) $tab['term_id'] : 0;
                $tab_products_markup = $build_home_products_markup($tab_term_id);
                ?>
                <div
                    class="zippy-home-products-panel <?php echo $tab_index === 0 ? '' : 'd-none'; ?>"
                    data-home-products-panel="<?php echo esc_attr($tab['key']); ?>"
                    <?php echo $tab_index === 0 ? '' : 'hidden'; ?>>
                    <?php
                    if (trim((string) $tab_products_markup) !== '') {
                        echo $tab_products_markup;
                    } else {
                    ?>
                        <div class="zippy-home-empty text-center">
                            <p class="zippy-home-empty__text">
                                <?php esc_html_e('No products found in this category.', 'zippy-child'); ?>
                            </p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if (!empty($secondary_cards)) : ?>
        <section class="zippy-home-products zippy-home-products--secondary container">
            <header class="zippy-home-section-head">
                <h3 class="zippy-home-section-head__title zippy-home-section-head__title--secondary"><?php echo esc_html($secondary_title); ?></h3>
                <?php if (!empty($secondary_subtitle)) : ?>
                    <p class="zippy-home-section-head__subtitle"><?php echo esc_html($secondary_subtitle); ?></p>
                <?php endif; ?>
            </header>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $render_product_card_grid($secondary_cards, 'secondary');
            ?>
        </section>
    <?php endif; ?>

    <?php if (!empty($bottom_cards)) : ?>
        <section class="zippy-home-products zippy-home-products--bottom">
            <header class="zippy-home-section-head">
                <h3 class="zippy-home-section-head__title"><?php echo esc_html($bottom_title); ?></h3>
                <?php if (!empty($bottom_subtitle)) : ?>
                    <p class="zippy-home-section-head__subtitle"><?php echo esc_html($bottom_subtitle); ?></p>
                <?php endif; ?>
            </header>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $render_product_card_grid($bottom_cards, 'bottom');
            ?>
        </section>
    <?php endif; ?>
</div>

<?php
get_footer();
