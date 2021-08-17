<?php

/**
 * Theme Name: Headless
 * Author: Kurage Digital
 */

/* Setup Theme
----------------------------------------------- */
if (!function_exists('headless_setup')) :
    function headless_setup()
    {
        // Delete Gutenburg core block patterns.
        remove_theme_support('core-block-patterns');

        // Enable support for Post Thumbnails on posts and pages
        add_theme_support('post-thumbnails');

        // Add Excerpt support to pages
        add_post_type_support('page', 'excerpt');
    }
endif;
add_action('after_setup_theme', 'headless_setup');

/* Remove Features
----------------------------------------------- */
// Removes automatic addition of <p> tags to content and excerpt
remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');

// Removes "Customize" menu and comment-edit button from admin menu
add_action('admin_menu', function () {
    remove_menu_page('themes.php');
    remove_menu_page('edit-comments.php');
});

// Removes comment feature from post and pages
add_action('init', function () {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
}, 100);

// Removes comment feature from admin bar
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});

// Remove Page Attributes from Page post-type
add_action('init', function () {
    remove_post_type_support('page', 'page-attributes');
});

/* Add ACF options page
----------------------------------------------- */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'     => 'Site Options',
        'menu_title'    => 'Site Options',
        'menu_slug'     => 'site-options',
        'capability'    => 'edit_posts',
        'redirect'        => false,
        'show_in_graphql' => true, // Show in GraphQL
    ));
}

/* ACF JSON Import
https://www.advancedcustomfields.com/resources/local-json/
----------------------------------------------- */
// function custom_acf_json_save_point( $path ) {
//     $path = get_stylesheet_directory() . '/acf-import';
//     return $path;
// }
// add_filter('acf/settings/save_json', 'custom_acf_json_save_point');

// function my_acf_json_load_point( $paths ) {
//     unset($paths[0]);
//     $paths[] = get_stylesheet_directory() . '/json-imports';
//     return $paths;
// }
// add_filter('acf/settings/load_json', 'my_acf_json_load_point');

/* Admin Area Custom CSS
----------------------------------------------- */
function admin_style()
{
    wp_enqueue_style('admin-styles', get_template_directory_uri() . '/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');

/* ACF Options Page Webhook Support
https://www.advancedcustomfields.com/resources/acf-save_post/
----------------------------------------------- */
function trigger_webhook_on_acf_save($post_id)
{
    $custom_data = array();
    $webhook_names = array();
    $http_args = array();

    $response = apply_filters('wp_webhooks_send_to_webhook_filter', array(), $custom_data, $webhook_names, $http_args);
}
add_action('acf/save_post', 'trigger_webhook_on_acf_save', 20);

/* Increase max queryable posts past 100 (if logged in)
----------------------------------------------- */
// add_filter( 'graphql_connection_max_query_amount', function( $amount, $source, $args, $context, $info  ) {
//     if ( current_user_can( 'manage_options' ) ) {
//          $amount = 1000;
//     }
//     return $amount;
// }, 10, 5 );
