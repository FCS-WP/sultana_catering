<?php
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="custom-woo-column">
    <h2 class="text-center" style="margin-bottom: 25px;">Login</h2>
    <form class="woocommerce-form woocommerce-form-login login" method="post">
        <?php do_action('woocommerce_login_form_start'); ?>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text"
                name="username" id="username" autocomplete="username"
                value="<?php echo (! empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
        </p>

        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
            <span class="password-input">
                <input class="woocommerce-Input woocommerce-Input--text input-text"
                    type="password" name="password" id="password" autocomplete="current-password" />
                <span class="show-password-input"></span>
            </span>
        </p>

        <?php do_action('woocommerce_login_form'); ?>

        <div class="login-function-container">
            <span class="form-row">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme"
                        type="checkbox" id="rememberme" value="forever" />
                    <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
                </label>
                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
            </span>
            <span class="woocommerce-LostPassword lost_password">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
            </span>
        </div>
        <div class="login-submit-container" style="text-align: center;">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login"
                value="<?php esc_attr_e('Log in', 'woocommerce'); ?>" style="margin-right:0px; margin-bottom:10px;"><?php esc_html_e('Log in', 'woocommerce'); ?>
            </button>

            <div class="register-link-container">
                <span>or</span><br>
                <a class="link-create-account" href="<?php echo esc_url(wc_get_account_endpoint_url('register')); ?>">Create new account</a>
            </div>
        </div>
        <?php do_action('woocommerce_login_form_end'); ?>
    </form>
</div>