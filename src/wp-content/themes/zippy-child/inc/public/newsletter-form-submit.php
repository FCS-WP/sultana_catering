<?php

if (!defined('ABSPATH')) {
    exit;
}

function zippy_handle_newsletter_signup()
{
    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/');
    $redirect_to = $redirect_to ?: home_url('/');

    $nonce = isset($_POST['zippy_newsletter_nonce']) ? sanitize_text_field(wp_unslash($_POST['zippy_newsletter_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'zippy_newsletter_signup')) {
        wp_safe_redirect(add_query_arg('newsletter_signup', 'failed', $redirect_to));
        exit;
    }

    $honeypot = isset($_POST['newsletter_company']) ? trim((string) wp_unslash($_POST['newsletter_company'])) : '';
    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('newsletter_signup', 'success', $redirect_to));
        exit;
    }

    $email = isset($_POST['newsletter_email']) ? sanitize_email(wp_unslash($_POST['newsletter_email'])) : '';
    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('newsletter_signup', 'invalid_email', $redirect_to));
        exit;
    }

    $recipient_email = apply_filters('zippy_newsletter_form_recipient', get_option('admin_email'));
    $subject = apply_filters('zippy_newsletter_form_subject', sprintf('New newsletter signup on %s', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)));
    $message = implode(
        "\n\n",
        array(
            'A new newsletter signup was submitted.',
            'Email: ' . $email,
            'Site: ' . home_url('/'),
            'Submitted at: ' . current_time('mysql'),
        )
    );

    do_action('zippy_newsletter_form_submitting', $email);

    $sent = wp_mail($recipient_email, $subject, $message);
    if (!$sent) {
        wp_safe_redirect(add_query_arg('newsletter_signup', 'failed', $redirect_to));
        exit;
    }

    do_action('zippy_newsletter_form_submitted', $email);

    wp_safe_redirect(add_query_arg('newsletter_signup', 'success', $redirect_to));
    exit;
}

add_action('admin_post_nopriv_zippy_newsletter_signup', 'zippy_handle_newsletter_signup');
add_action('admin_post_zippy_newsletter_signup', 'zippy_handle_newsletter_signup');
