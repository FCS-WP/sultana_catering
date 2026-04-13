<?php

/**
 * WooCommerce My Account Favourites Customizations
 */

/**
 * 1. Register new endpoint to use for My Account page
 * Note: After adding this, you may need to flush rewrite rules by visiting Settings > Permalinks.
 */
function zippy_add_favourites_endpoint()
{
    add_rewrite_endpoint('my-favourites', EP_PAGES);
}
add_action('init', 'zippy_add_favourites_endpoint');

/**
 * 2. Add new query var
 */
function zippy_favourites_query_vars($vars)
{
    $vars[] = 'my-favourites';
    return $vars;
}
add_filter('query_vars', 'zippy_favourites_query_vars', 0);

/**
 * 3. Insert the new endpoint into the My Account menu
 */
function zippy_add_favourites_link_my_account($items)
{
    // Insert after "orders" or at the end
    $new_items = array();

    foreach ($items as $key => $value) {
        $new_items[$key] = $value;
        if ($key === 'orders') {
            $new_items['my-favourites'] = __('My Favourites', 'woocommerce');
        }
    }

    // fallback if 'orders' not found
    if (!isset($new_items['my-favourites'])) {
        $new_items['my-favourites'] = __('My Favourites', 'woocommerce');
    }

    return $new_items;
}
add_filter('woocommerce_account_menu_items', 'zippy_add_favourites_link_my_account');

/**
 * 4. Add content for the new endpoint
 */
function zippy_favourites_content()
{
    echo '<h3>' . __('My Favourites', 'woocommerce') . '</h3>';
    echo '<p>' . __('Your favourite items will appear here.', 'woocommerce') . '</p>';
    // You can add a shortcode or custom loop here for the wishlist
}
add_action('woocommerce_account_my-favourites_endpoint', 'zippy_favourites_content');
