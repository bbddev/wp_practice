<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
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
    // Enqueue jQuery
    wp_enqueue_script('jquery');
    
    // Enqueue our custom script
    wp_enqueue_script(
        'school-management-js',
        plugin_dir_url(__FILE__) . '../../assets/js/school-management.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // Localize script to pass AJAX URL and nonce
    wp_localize_script('school-management-js', 'schoolManagementAjax', array(
        'apiUrl' => rest_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

// Hook to enqueue scripts on frontend when shortcode is used
add_action('wp_enqueue_scripts', 'enqueue_school_management_frontend_scripts');