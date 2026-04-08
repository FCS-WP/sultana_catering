<?php

/**
 * Format by WC Currency
 * @param float $price
 */

function format_price($price)
{
    return wc_price($price);
}

/**
 * Check if order mode exists in session.
 */
function is_existing_shipping()
{
    if (is_admin()) return false;
    if (!function_exists('WC') || !WC()->session) return false;
    return !empty(WC()->session->get('order_mode'));
}

/**
 * Check if current order mode is takeaway/pickup.
 */
function is_takeaway()
{
    if (!is_existing_shipping()) return false;
    return WC()->session->get('order_mode') === 'takeaway' || WC()->session->get('order_mode') === 'pickup';
}

/**
 * Check if current order mode is delivery.
 */
function is_delivery()
{
    if (!is_existing_shipping()) return false;
    return WC()->session->get('order_mode') === 'delivery';
}

/**
 * Get minimum rule by order mode.
 */
function get_minimum_rule_by_order_mode()
{
    $response = [
        'minimum_total_to_order'    => 0,
        'minimum_total_to_freeship' => 0,
    ];

    if (is_delivery()) {
        $response['minimum_total_to_order']    = floatval(get_option('minimum_order', 0));
        $response['minimum_total_to_freeship'] = floatval(get_option('free_shipping_minimum', 0)); // Fallback
    }

    return $response;
}

/**
 * Get tax percentage
 */
function get_tax_percent()
{
    if (!class_exists('WC_Tax')) return null;
    $all_tax_rates = [];
    $tax_classes = WC_Tax::get_tax_classes();
    if (!in_array('', $tax_classes)) {
        array_unshift($tax_classes, '');
    }

    foreach ($tax_classes as $tax_class) {
        $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
        $all_tax_rates = array_merge($all_tax_rates, $taxes);
    }

    if (empty($all_tax_rates)) return null;
    return $all_tax_rates[0];
}

/**
 * Get total cart value including tax
 */
function get_total_cart()
{
    if (!function_exists('WC') || !WC()->cart) return 0;
    
    $subtotal = WC()->cart->get_subtotal();
    $tax = get_tax_percent();
    
    if ($tax && isset($tax->tax_rate)) {
        return wc_format_decimal($subtotal * (1 + $tax->tax_rate / 100));
    }
    return wc_format_decimal($subtotal);
}

/**
 * Outlet session keys
 */
function get_keys_outlet_session()
{
    return array(
        'date',
        'time',
        'order_mode',
        'extra_fee',
        'outlet_address',
        'outlet_name',
        'delivery_address',
        'status_popup',
    );
}

/**
 * Safely get WooCommerce session data
 */
function zippy_get_wc_session($key = null)
{
    if (!function_exists('WC') || !WC()->session) return null;

    $keys = get_keys_outlet_session();

    if ($key !== null) {
        return in_array($key, $keys) ? WC()->session->get($key) : null;
    }

    $session_data = array();
    foreach ($keys as $k) {
        $session_data[$k] = WC()->session->get($k);
    }

    return $session_data;
}

/**
 * Get delivery time string from session
 */
function zippy_get_delivery_time()
{
    $time_conf = zippy_get_wc_session('time');
    if (empty($time_conf) || !isset($time_conf['from']) || !isset($time_conf['to'])) return '';
    return 'From ' . date("H:i", strtotime($time_conf['from'])) . ' To ' . date("H:i", strtotime($time_conf['to']));
}

/**
 * Format date wrapper
 */
function format_date_DdMY($date_string)
{
    if (empty($date_string)) return '';
    $timestamp = strtotime($date_string);
    return date('D, d M Y', $timestamp);
}

/**
 * Get delivery address from session
 */
function get_delivery_address()
{
    return zippy_get_wc_session('delivery_address') ?: '';
}

/**
 * Calculate tax inclusive amount
 */
function get_tax_inclusive_amount($total, $tax_rate)
{
    if ($tax_rate <= 0) return 0;
    return $total - ($total / (1 + $tax_rate / 100));
}
