<?php
/**
 * Product helper functions ported from Ji Xiang Everton.
 */

/**
 * Get product pricing rules (safe version).
 */
function get_product_pricing_rules($product, $quantity, $user_id = null)
{
    // Check for Price_Books_Helper if it exists
    if (class_exists('Zippy_Booking\Src\Services\Price_Books\Price_Books_Helper')) {
        $helper = new \Zippy_Booking\Src\Services\Price_Books\Price_Books_Helper();
        $rules  = $helper->get_active_rules_for_current_user(null, $user_id);
        $product_id =  $product->get_id();
        $regular_price =  $product->get_price();
        if (isset($rules[$product_id])) {
            return $helper->apply_rule_to_price($regular_price, $rules[$product_id]);
        }
    }
    return $product->get_price();
}

/**
 * Get minimum price for a combo product.
 */
function get_minimum_price_for_combo($product)
{
    $product_combo = get_field('product_combo', $product->get_id());

    if (!is_array($product_combo)) return $product->get_price_html();

    $price_range = [];
    foreach ($product_combo as $sub_product_obj) {
        if (empty($sub_product_obj)) continue;

        $sub_product_id = $sub_product_obj["product"]->ID ?? null;
        $sub_product = wc_get_product($sub_product_id);

        if ($sub_product) {
            $price_range[] = get_product_pricing_rules($sub_product, 1);
        }
    }

    if (empty($price_range)) return $product->get_price_html();

    $price = min($price_range);
    return wc_price($price);
}

function zippy_child_loop_add_to_cart_button($product = null)
{
    if (!$product) {
        global $product;
    }

    if (!$product instanceof WC_Product) {
        return;
    }

    $product_id = $product->get_id();

    if ($product->is_virtual()) {
        $contact_url = function_exists('build_whatsapp_link')
            ? build_whatsapp_link($product)
            : get_permalink($product_id);

        printf(
            '<a class="whatsapp_product_btn" target="_blank" href="%s">%s</a>',
            esc_url($contact_url),
            esc_html__('Contact for Sale', 'zippy-child')
        );
        return;
    }

    $classes = 'zippy-home-add-cart zippy-shop-add-cart product_type_' . $product->get_type() . ' add_to_cart_button';

    if ($product->supports('ajax_add_to_cart')) {
        $classes .= ' ajax_add_to_cart';
    }

    if (!$product->is_purchasable() || !$product->is_in_stock()) {
        $classes .= ' disabled';
    }

    if (!is_existing_shipping()) {
        printf(
            '<a href="#lightbox-zippy-form" class="zippy-shop-add-cart lightbox-zippy-btn" data-product_id="%1$d" data-product-id="%1$d" data-product-url="%2$s" data-woo-button-classes="%3$s" aria-label="%4$s" rel="nofollow">%5$s</a>',
            esc_attr($product_id),
            esc_url($product->add_to_cart_url()),
            esc_attr($classes),
            esc_attr($product->add_to_cart_description()),
            esc_html($product->add_to_cart_text())
        );
        return;
    }

    printf(
        '<a href="%1$s" data-add-cart data-quantity="1" class="%2$s" data-product_id="%3$d" data-product-id="%3$d" data-product_sku="%4$s" data-product-url="%1$s" data-woo-button-classes="%5$s" aria-label="%6$s" rel="nofollow">%7$s</a>',
        esc_url($product->add_to_cart_url()),
        esc_attr($classes),
        esc_attr($product_id),
        esc_attr($product->get_sku()),
        esc_attr($classes),
        esc_attr($product->add_to_cart_description()),
        esc_html($product->add_to_cart_text())
    );
}

function zippy_child_product_box_after_add_to_cart()
{
    global $product;

    echo '<div class="zippy-shop-actions">';
    zippy_child_loop_add_to_cart_button($product);
    echo '</div>';
}

add_action('wp', function () {
    remove_action('flatsome_product_box_actions', 'flatsome_lightbox_button', 50);
    remove_action('flatsome_product_box_after', 'flatsome_woocommerce_shop_loop_button', 100);
    add_action('flatsome_product_box_after', 'zippy_child_product_box_after_add_to_cart', 90);
});
