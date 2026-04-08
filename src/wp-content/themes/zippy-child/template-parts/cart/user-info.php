<?php
/**
 * User Info template part for Cart/Header.
 * Ported from Ji Xiang Everton.
 */

if (!function_exists('WC') || !WC()->session || !WC()->session->get('order_mode')) {
    return;
}

$order_mode = WC()->session->get('order_mode');
$outlet_name = WC()->session->get('outlet_name') ?: 'Main Outlet';
$delivery_address = WC()->session->get('delivery_address');
?>

<div class="box_infor_method_shipping">
    <div class="items_infor_method_shipping">
        <div class="text_items">
            <h4>Order Mode:</h4>
            <p><?php echo esc_html(ucfirst($order_mode)); ?></p>
        </div>
        <div class="icon_items">
            <button id="removeMethodShipping" title="Change order mode">
                <img src="<?php echo esc_url(get_theme_file_uri('assets/icons/edit-light.png')); ?>" alt="Edit">
            </button>
        </div>
    </div>

    <div class="items_infor_method_shipping">
        <div class="text_items">
            <h4>Select Outlet:</h4>
            <p><?php echo esc_html($outlet_name); ?></p>
        </div>
    </div>

    <?php if (is_delivery() && !empty($delivery_address)) : ?>
        <div class="items_infor_method_shipping">
            <div class="text_items">
                <h4>Delivery Address:</h4>
                <p><?php echo esc_html($delivery_address); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="items_infor_method_shipping">
        <div class="text_items">
            <h4><?php echo (is_takeaway()) ? 'Takeaway' : 'Delivery'; ?> Time:</h4>
            <p>
                <?php
                $date = WC()->session->get('date');
                $time = WC()->session->get('time');
                if ($date) {
                    echo esc_html(date('d M Y', strtotime($date))) . '<br>';
                }
                if (is_array($time)) {
                    echo 'From ' . esc_html($time['from']) . ' To ' . esc_html($time['to']);
                } elseif (is_string($time)) {
                    echo esc_html($time);
                }
                ?>
            </p>
        </div>
    </div>
</div>
