<?php
/**
 * Takeaway / Cart clearing logic.
 */

function remove_cart_session()
{
    if (function_exists('WC') && WC()->cart) {
        WC()->cart->empty_cart();
    }
    
    if (function_exists('WC') && WC()->session) {
        WC()->session->destroy_session();
    }

    wp_send_json_success(['message' => 'Cart session removed']);
}

add_action('wp_ajax_remove_cart_session', 'remove_cart_session');
add_action('wp_ajax_nopriv_remove_cart_session', 'remove_cart_session');

/**
 * Set order mode in session via AJAX.
 */
function set_order_mode_session()
{
    $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : '';

    if (function_exists('WC') && WC()->session) {
        WC()->session->set('order_mode', $mode);
        
        // Clear address if switching to takeaway
        if ($mode === 'takeaway') {
            WC()->session->set('delivery_address', '');
        }
        
        wp_send_json_success(['message' => 'Order mode set to ' . $mode]);
    }

    wp_send_json_error(['message' => 'Failed to set order mode']);
}

add_action('wp_ajax_set_order_mode_session', 'set_order_mode_session');
add_action('wp_ajax_nopriv_set_order_mode_session', 'set_order_mode_session');
