<?php

if (!defined('ABSPATH')) {
    exit;
}

function enqueue_media_scripts($hook_suffix)
{
    // Only enqueue on the edit entity page
    global $post_type;
    if ('entity' === $post_type) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    } elseif ('student' === $post_type) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}

function enqueue_school_management_frontend_scripts()
{
    wp_enqueue_script('jquery');

    wp_enqueue_style(
        'bootstrap-css',
        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css',
        array(),
        '4.6.0'
    );

    wp_enqueue_style(
        'fontawesome-css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        array(),
        '5.15.4'
    );

    $css_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/style/school-management.css');

    wp_enqueue_style(
        'school-management-css',
        SCHOOLPLUGIN_URL . '/assets/style/school-management.css',
        array('bootstrap-css'),
        $css_version
    );

    $custom_modal_css_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/style/custom-modal.css');

    wp_enqueue_style(
        'custom-modal-css',
        SCHOOLPLUGIN_URL . '/assets/style/custom-modal.css',
        array('bootstrap-css'),
        $custom_modal_css_version
    );

    $custom_header_css_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/style/custom-header.css');

    wp_enqueue_style(
        'custom-header-css',
        SCHOOLPLUGIN_URL . '/assets/style/custom-header.css',
        array('bootstrap-css'),
        $custom_header_css_version
    );

    $custom_header_css_version = filemtime(SCHOOLPLUGIN_PATH . '/assets/style/custom-header.css');

    wp_enqueue_style(
        'custom-header-css',
        SCHOOLPLUGIN_URL . '/assets/style/custom-header.css',
        array('bootstrap-css'),
        $custom_header_css_version
    );

    $js_modules = array(
        'utils' => '/assets/js/modules/utils.js',
        'pagination' => '/assets/js/modules/pagination.js',
        'entity' => '/assets/js/modules/entity.js',
        'custom-modal' => '/assets/js/modules/custom-modal.js',
        'custom-password' => '/assets/js/modules/custom-password.js',
        'student-login' => '/assets/js/modules/student-login.js',
        'school-class' => '/assets/js/modules/school-class.js',
        'main' => '/assets/js/school-management-main.js'
    );

    $previous_handle = array('jquery');

    foreach ($js_modules as $handle => $file_path) {
        $full_path = SCHOOLPLUGIN_PATH . $file_path;
        $js_version = file_exists($full_path) ? filemtime($full_path) : '1.0';

        wp_enqueue_script(
            'school-management-' . $handle,
            SCHOOLPLUGIN_URL . $file_path,
            $previous_handle,
            $js_version,
            true
        );

        $previous_handle = array('school-management-' . $handle);
    }

    // Localize script data on the main controller
    wp_localize_script('school-management-main', 'schoolManagementAjax', array(
        'apiUrl' => rest_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

// add_action('wp_enqueue_scripts', 'enqueue_school_management_frontend_scripts');