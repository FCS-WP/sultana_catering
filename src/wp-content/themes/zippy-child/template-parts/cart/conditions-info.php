<?php
/**
 * Conditions Info template part for Cart (Progress bars).
 *
 * @var array $args {
 *     @var float $total_cart
 *     @var array $rules
 * }
 */

$total_cart = $args['total_cart'] ?? 0;
$rules = $args['rules'] ?? [];
$min_order = $rules['minimum_total_to_order'] ?? 0;
$min_free_ship = $rules['minimum_total_to_freeship'] ?? 0;

$remaining_min_order = max(0, $min_order - $total_cart);
$remaining_free_ship = max(0, $min_free_ship - $total_cart);

$percent_min_order = $min_order > 0 ? min(100, ($total_cart / $min_order) * 100) : 100;
$percent_free_ship = $min_free_ship > 0 ? min(100, ($total_cart / $min_free_ship) * 100) : 100;
?>

<div class="zippy-cart-conditions">
    <?php if ($min_free_ship > 0) : ?>
        <div class="condition-item">
            <p>
                <?php if ($remaining_free_ship > 0) : ?>
                    <strong><?php echo wc_price($remaining_free_ship); ?></strong> more for Free delivery
                <?php else : ?>
                    <strong>Free delivery</strong> unlocked!
                <?php endif; ?>
            </p>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?php echo $percent_free_ship; ?>%;"></div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($min_order > 0) : ?>
        <div class="condition-item">
            <p>
                <?php if ($remaining_min_order > 0) : ?>
                    <strong><?php echo wc_price($remaining_min_order); ?></strong> more for minimum order
                <?php else : ?>
                    <strong>Minimum order</strong> met!
                <?php endif; ?>
            </p>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?php echo $percent_min_order; ?>%;"></div>
            </div>
        </div>
    <?php endif; ?>
</div>
