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
