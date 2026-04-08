<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Function   : getlist_posts()
 * Description  : Get list
 *
 * @return    : array post
 */
function getlist_posts($atts)
{
    /* default params */
    $atts = shortcode_atts(
        array(
            'max_posts'      => -1,
            'pagination'     => 'true',
            'limit_text'     => 0,
            'taxonomy'       => '',
            'term_id'        => '',
            'template'       => '',
            'filter'         => 'true',
            'paged'          => 1,
            'posts_per_page' => -1,
            'category'       => '',
            'category_name'  => '',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'include'        => '',
            'exclude'        => '',
            'post_type'      => 'post',
            'post_parent'    => '',
            'author'         => '',
            'author_name'    => '',
            'post_status'    => 'publish',
            'hidden_content' => 'false',
        ),
        $atts
    );

    /* filter post by URL */
    // by category
    $cateID   = isset($_GET['cate_id']) ? $_GET['cate_id'] : $atts['category']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : $atts['taxonomy']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

    //by term_id
    if (isset($_GET['term_id']) && $_GET['term_id'] != 0 && $atts['filter'] != 'false') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $termID           = $_GET['term_id']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $param_detail_url = '?term_id=' . $termID;
        $tax_query_custom = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $termID,
            ),
        );
    } elseif ($atts['term_id'] != '') {
        $tax_query_custom = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $atts['term_id'],
            ),
        );
    } else {
        $param_detail_url = '';
        $tax_query_custom = '';
    }

    /* count total posts */
    $count_post = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type'      => $atts['post_type'],
        'cat'            => $cateID,
        'tax_query'      => $tax_query_custom,
    ));
    $total_post = $count_post->post_count;

    /* query posts */
    $paged          = (get_query_var('paged')) ? get_query_var('paged') : $atts['paged'];
    $posts_per_page = $atts['max_posts'] != -1 ? $atts['max_posts'] : $atts['posts_per_page'];

    $getlist_posts = new WP_Query(array(
        'paged'          => $paged,
        'posts_per_page' => $posts_per_page,
        'cat'            => $cateID,
        'category_name'  => $atts['category_name'],
        'orderby'        => $atts['orderby'],
        'order'          => $atts['order'],
        'include'        => $atts['include'],
        'exclude'        => $atts['exclude'],
        'post_type'      => $atts['post_type'],
        'post_parent'    => $atts['post_parent'],
        'author'         => $atts['author'],
        'author_name'    => $atts['author_name'],
        'post_status'    => $atts['post_status'],
        'tax_query'      => $tax_query_custom,
        'pagination'     => $atts['pagination'],
    ));

    ob_start();
    if ($getlist_posts->have_posts()) {
        /* get template post list */
        if ($atts['template'] != '') {
            $template_file = $atts['template'];
        } else {
            $template_file = 'template-list/' . $atts['post_type'] . '.php';
        }
        include(locate_template($template_file));
        wp_reset_postdata();

        /* pagination */
        if ($atts['pagination'] == 'true' && function_exists('custom_pagination')) {
            echo '<div class="nav-links pager-list panigation-' . get_the_ID() . '">';
            custom_pagination($getlist_posts->max_num_pages, "2", $paged, true);
            echo '</div>';
        }
    } else {
        /* no post */
        //echo '<p>No post.</p>';
    }

    return ob_get_clean();
}

add_shortcode('GET_LIST', 'getlist_posts');
