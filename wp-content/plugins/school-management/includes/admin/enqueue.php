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

    wp_enqueue_style(
        'school-management-css',
        SCHOOLPLUGIN_URL . '/assets/style/school-management.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'school-management-js',
        SCHOOLPLUGIN_URL . '/assets/js/school-management.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script('school-management-js', 'schoolManagementAjax', array(
        'apiUrl' => rest_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

add_action('wp_enqueue_scripts', 'enqueue_school_management_frontend_scripts');