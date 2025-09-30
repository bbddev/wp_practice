<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue media scripts for admin
 */
function enqueue_media_scripts($hook_suffix)
{
    // Chỉ enqueue trên trang edit entity
    global $post_type;
    if ('entity' === $post_type) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}

/**
 * Enqueue frontend scripts for school management
 */
function enqueue_school_management_frontend_scripts()
{
    wp_enqueue_script('jquery');

    // Enqueue Bootstrap CSS và JS cho modal
    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css',
        array(),
        '4.6.0'
    );

    // Enqueue FontAwesome for icons
    wp_enqueue_style(
        'fontawesome-css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        array(),
        '5.15.4'
    );

    wp_enqueue_script(
        'bootstrap-js',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        '4.6.0',
        true
    );

    // Use file modification time as version to prevent caching issues
    $css_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/style/school-management.css');
    $js_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/js/school-management.js');

    wp_enqueue_style(
        'school-management-css',
        SCHOOLPLUGIN_URL . '/assets/style/school-management.css',
        array('bootstrap-css'),
        $css_version
    );

    wp_enqueue_script(
        'school-management-js',
        SCHOOLPLUGIN_URL . '/assets/js/school-management.js',
        array('jquery', 'bootstrap-js'),
        $js_version,
        true
    );

    wp_localize_script('school-management-js', 'schoolManagementAjax', array(
        'apiUrl' => rest_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

add_action('wp_enqueue_scripts', 'enqueue_school_management_frontend_scripts');