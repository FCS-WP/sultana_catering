<?php

/**
 * Remove payment methods from the main checkout column review section.
 * They are already displayed in the sidebar custom template.
 */
remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);

add_action('init', function () {
    add_rewrite_rule(
        '^my-account/register/?$',
        'index.php?pagename=my-account',
        'top'
    );
});
