<?php

add_action('wp_enqueue_scripts', 'zippy_child_enqueue_assets');

function zippy_child_enqueue_assets()
{
    zippy_child_enqueue_bundle('app');

    foreach (zippy_child_get_asset_contexts() as $context) {
        zippy_child_enqueue_bundle($context);
    }

    zippy_child_enqueue_dynamic_styles();
}

function zippy_child_enqueue_bundle($slug)
{
    $css_abs_path = zippy_child_get_bundle_path($slug, 'css');
    $js_abs_path = zippy_child_get_bundle_path($slug, 'js');

    $theme_uri = get_stylesheet_directory_uri();

    if (file_exists($css_abs_path)) {
        $css_rel_path = "/assets/dist/css/{$slug}.min.css";
        wp_enqueue_style(
            "zippy-child-{$slug}-style",
            $theme_uri . $css_rel_path,
            array(),
            filemtime($css_abs_path),
            'all'
        );
    }

    if (file_exists($js_abs_path)) {
        $js_rel_path = "/assets/dist/js/{$slug}.min.js";
        $dependencies = array('jquery');

        if ($slug === 'app' && class_exists('WooCommerce')) {
            $dependencies[] = 'flatsome-theme-woocommerce-js';
        }

        wp_enqueue_script(
            "zippy-child-{$slug}-script",
            $theme_uri . $js_rel_path,
            $dependencies,
            filemtime($js_abs_path),
            true
        );
    }
}

function zippy_child_get_asset_contexts()
{
    $contexts = array();

    // Page assets now follow page slug only (template-independent).
    if (is_page()) {
        $page_id = get_queried_object_id();
        $page_slug = $page_id ? sanitize_title((string) get_post_field('post_name', $page_id)) : '';

        if (!empty($page_slug) && zippy_child_bundle_exists($page_slug)) {
            $contexts[] = $page_slug;
        } elseif (zippy_child_bundle_exists('page')) {
            $contexts[] = 'page';
        }

        return array_values(array_unique($contexts));
    }

    if (is_front_page() && zippy_child_bundle_exists('home')) {
        $contexts[] = 'home';
    }

    if (is_home()) {
        $contexts[] = 'blog';
    }

    if (is_404()) {
        $contexts[] = '404';
    }

    if (is_search()) {
        $contexts[] = 'search';
    }

    if (function_exists('is_cart') && is_cart()) {
        $contexts[] = 'cart';
    }

    if (function_exists('is_checkout') && is_checkout()) {
        $contexts[] = 'checkout';
    }

    if (function_exists('is_account_page') && is_account_page()) {
        $contexts[] = 'my-account';
    }

    if (function_exists('is_product_category') && is_product_category()) {
        $contexts[] = 'product-category';
    }

    if (function_exists('is_product_tag') && is_product_tag()) {
        $contexts[] = 'product-tag';
    }

    if (is_singular('product')) {
        $contexts[] = 'product';
    }

    if (function_exists('is_shop') && is_shop()) {
        $contexts[] = 'shop';
    }

    if (is_single()) {
        $contexts[] = 'single';
    }

    return array_values(array_unique($contexts));
}

function zippy_child_bundle_exists($slug)
{
    $css_abs_path = zippy_child_get_bundle_path($slug, 'css');
    $js_abs_path = zippy_child_get_bundle_path($slug, 'js');

    return file_exists($css_abs_path) || file_exists($js_abs_path);
}

function zippy_child_get_bundle_path($slug, $type)
{
    $theme_dir = get_stylesheet_directory();

    if ($type === 'css') {
        return $theme_dir . "/assets/dist/css/{$slug}.min.css";
    }

    return $theme_dir . "/assets/dist/js/{$slug}.min.js";
}

function zippy_child_enqueue_dynamic_styles()
{
    if (!is_page_template('page-templates/template-homepage.php')) {
        return;
    }

    $page_id = get_queried_object_id();
    if (!$page_id) {
        return;
    }

    $dynamic_css = '';

    $hero_background = function_exists('get_field') ? get_field('hero_background', $page_id) : null;
    $hero_background_url = zippy_child_get_acf_image_url($hero_background);
    if (!empty($hero_background_url)) {
        $dynamic_css .= sprintf(
            '.zippy-home-hero{background-image:url(%s);}',
            wp_json_encode(esc_url_raw($hero_background_url))
        );
    }

    $hero_badge_bg = function_exists('get_field') ? get_field('hero_badge_bg', $page_id) : '';
    $hero_badge_bg = is_string($hero_badge_bg) ? sanitize_hex_color($hero_badge_bg) : '';
    if (!empty($hero_badge_bg)) {
        $dynamic_css .= sprintf(
            '.zippy-home-hero__badge{background-color:%s;}',
            $hero_badge_bg
        );
    }

    if ($dynamic_css !== '') {
        wp_add_inline_style('zippy-child-app-style', $dynamic_css);
    }
}

function zippy_child_get_acf_image_url($image_field)
{
    if (is_array($image_field) && !empty($image_field['url'])) {
        return $image_field['url'];
    }

    if (is_numeric($image_field)) {
        $image_url = wp_get_attachment_image_url((int) $image_field, 'full');
        return $image_url ? $image_url : '';
    }

    if (is_string($image_field)) {
        return $image_field;
    }

    return '';
}
