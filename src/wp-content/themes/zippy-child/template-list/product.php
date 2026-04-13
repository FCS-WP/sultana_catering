<?php

if (!isset($getlist_posts) || !($getlist_posts instanceof WP_Query)) {
    return;
}

$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/');
$card_index = 0;
$render_uid = function_exists('wp_unique_id') ? wp_unique_id('zippy-get-list-qty-') : uniqid('zippy-get-list-qty-', false);
?>
<div class="zippy-home-product-grid row g-4">
    <?php while ($getlist_posts->have_posts()) : $getlist_posts->the_post(); ?>
        <?php
        $product_id = (int) get_the_ID();
        $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
        $description = get_the_excerpt();
        if (empty($description)) {
            $description = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 18, '...');
        }

        $image_url = get_the_post_thumbnail_url($product_id, 'medium');
        if (empty($image_url) && function_exists('wc_placeholder_img_src')) {
            $image_url = wc_placeholder_img_src('medium');
        }

        $price_html = $product ? $product->get_price_html() : '';
        $qty_input_id = $render_uid . '-' . $card_index . '-' . $product_id;
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
        $is_add_to_cart_disabled = !$product || !$product->is_purchasable() || !$product->is_in_stock();
        $woo_button_classes = $product ? 'product_type_' . $product->get_type() . ' add_to_cart_button' : 'add_to_cart_button';
        if ($product && $product->supports('ajax_add_to_cart')) {
            $woo_button_classes .= ' ajax_add_to_cart';
        }
        $has_existing_shipping = function_exists('is_existing_shipping') && is_existing_shipping();
        $add_to_cart_action_class = $has_existing_shipping ? $woo_button_classes : 'lightbox-zippy-btn';
        $button_href = $has_existing_shipping ? $add_to_cart_href : '#lightbox-zippy-form';
        $button_state_classes = $is_add_to_cart_disabled ? ' is-disabled disabled pe-none opacity-50' : '';
        $card_index++;
        ?>
        <div class="col-12 col-lg-6 d-flex p-1">
            <article class="zippy-home-product-card d-grid w-100 h-100" data-product-card>
                <div class="zippy-home-product-media">
                    <?php if (!empty($image_url)) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" />
                    <?php else : ?>
                        <div class="zippy-home-media-placeholder"></div>
                    <?php endif; ?>
                </div>

                <div class="zippy-home-product-body d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="zippy-home-product-title"><?php the_title(); ?></h4>
                        <?php if (!empty($description)) : ?>
                            <p class="zippy-home-product-description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($price_html)) : ?>
                      <!--  <div class="zippy-home-price">
                            <?php echo wp_kses_post($price_html); ?>
                        </div> -->
                    <?php endif; ?>

                    <div class="zippy-home-product-actions d-flex align-items-center">
                        <div class="zippy-home-qty" data-qty-control>
                            <?php
                            if (function_exists('woocommerce_quantity_input')) {
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
                            class="zippy-home-add-cart zippy-button <?php echo esc_attr($add_to_cart_action_class . $button_state_classes); ?>"
                            href="<?php echo esc_url($button_href); ?>"
                            <?php if ($has_existing_shipping) : ?>
                            data-add-cart
                            <?php endif; ?>
                            data-product-id="<?php echo esc_attr($product_id); ?>"
                            data-product_id="<?php echo esc_attr($product_id); ?>"
                            data-product_sku="<?php echo esc_attr($product_sku); ?>"
                            data-product-url="<?php echo esc_url($add_to_cart_href); ?>"
                            data-woo-button-classes="<?php echo esc_attr($woo_button_classes); ?>"
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
    <?php endwhile; ?>
</div>
