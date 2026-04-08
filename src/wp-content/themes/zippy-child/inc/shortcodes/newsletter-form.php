<?php

if (!defined('ABSPATH')) {
    exit;
}

function zippy_newsletter_form_shortcode($atts = array())
{
    $atts = shortcode_atts(
        array(
            'placeholder' => 'Email',
            'button_text' => 'Sign Up',
        ),
        $atts,
        'zippy_newsletter_form'
    );

    $status = isset($_GET['newsletter_signup']) ? sanitize_key(wp_unslash($_GET['newsletter_signup'])) : '';
    $message = '';
    $message_class = '';

    if ($status === 'success') {
        $message = 'Thank you. Your signup has been received.';
        $message_class = 'is-success';
    } elseif ($status === 'invalid_email') {
        $message = 'Please enter a valid email address.';
        $message_class = 'is-error';
    } elseif ($status === 'failed') {
        $message = 'Something went wrong. Please try again.';
        $message_class = 'is-error';
    }

    $redirect_url = remove_query_arg(array('newsletter_signup'));

    ob_start();
    ?>
    <div class="zippy-newsletter">
        <form class="zippy-newsletter__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="zippy_newsletter_signup" />
            <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_url); ?>" />
            <?php wp_nonce_field('zippy_newsletter_signup', 'zippy_newsletter_nonce'); ?>

            <label class="screen-reader-text" for="zippy-newsletter-email"><?php esc_html_e('Email address', 'zippy-child'); ?></label>
            <input
                id="zippy-newsletter-email"
                class="zippy-newsletter__input"
                type="email"
                name="newsletter_email"
                placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
                autocomplete="email"
                required
            />

            <input class="zippy-newsletter__trap" type="text" name="newsletter_company" tabindex="-1" autocomplete="off" aria-hidden="true" />

            <button class="zippy-newsletter__button zippy-button" type="submit">
                <?php echo esc_html($atts['button_text']); ?>
            </button>
        </form>

        <?php if ($message !== '') : ?>
            <p class="zippy-newsletter__message <?php echo esc_attr($message_class); ?>">
                <?php echo esc_html($message); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean();
}

add_shortcode('zippy_newsletter_form', 'zippy_newsletter_form_shortcode');
