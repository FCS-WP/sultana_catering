<?php

/**
 * Delivery and Shipping logic ported from Ji Xiang Everton.
 */

// Filter shipping rates based on order mode
add_filter('woocommerce_package_rates', 'customize_shipping_rates_based_on_order_mode', 999);

function customize_shipping_rates_based_on_order_mode($rates)
{
    if (empty($rates)) return $rates;

    $rules = get_minimum_rule_by_order_mode();
    $cart_subtotal = floatval(WC()->cart->get_subtotal());

    // Check for free shipping eligibility
    $minimum_for_free_shipping = floatval($rules['minimum_total_to_freeship']);

    if ($minimum_for_free_shipping > 0 && $cart_subtotal >= $minimum_for_free_shipping) {
        foreach ($rates as $rate_key => $rate) {
            if ($rate->method_id !== 'free_shipping') {
                unset($rates[$rate_key]);
            }
        }
    } else {
        // If not eligible for free shipping, manage methods based on mode
        if (is_delivery()) {
            foreach ($rates as $rate_key => $rate) {
                if ($rate->method_id === 'free_shipping') {
                    unset($rates[$rate_key]);
                }
            }
        } elseif (is_takeaway()) {
            // For takeaway, maybe only allow local pickup or specific methods
            foreach ($rates as $rate_key => $rate) {
                if ($rate->method_id !== 'local_pickup' && $rate->method_id !== 'free_shipping') {
                    // unset($rates[$rate_key]); // Keep it flexible for now
                }
            }
        }
    }

    return $rates;
}

// Minimum order amount validation
add_action('woocommerce_checkout_process', 'set_minimum_order_amount_validation', 10);
add_action('woocommerce_before_cart', 'set_minimum_order_notice_display', 10);

function set_minimum_order_amount_validation()
{
    if (!is_delivery()) return;

    $minimum = get_minimum_rule_by_order_mode();
    $minimum_order = $minimum['minimum_total_to_order'];

    if (WC()->cart && WC()->cart->total < $minimum_order) {
        wc_add_notice(
            sprintf(
                'You must have an order with a minimum of %s to proceed to checkout for delivery.',
                wc_price($minimum_order)
            ),
            'error'
        );
    }
}

function set_minimum_order_notice_display()
{
    if (!is_delivery()) return;

    $minimum = get_minimum_rule_by_order_mode();
    $minimum_order = $minimum['minimum_total_to_order'];

    if (WC()->cart && WC()->cart->total < $minimum_order) {
        wc_print_notice(
            sprintf(
                'Your current order total is %s — you must have a minimum of %s for delivery.',
                wc_price(WC()->cart->total),
                wc_price($minimum_order)
            ),
            'error'
        );
    }
}
add_filter('woocommerce_checkout_fields', function ($fields) {

    $fields['billing']['billing_country']['required'] = false;
    $fields['billing']['billing_postcode']['required'] = false;

    return $fields;
});
