<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include admin files
require_once __DIR__ . '/admin/meta-box-class.php';
require_once __DIR__ . '/admin/meta-box-entity.php';
require_once __DIR__ . '/admin/enqueue.php';

// Include post types
require_once __DIR__ . '/post-types/register-school.php';
require_once __DIR__ . '/post-types/register-class.php';
require_once __DIR__ . '/post-types/register-entity.php';

// Include REST API
require_once __DIR__ . '/rest/routes.php';

// Include functions
require_once __DIR__ . '/functions/save-meta.php';
require_once __DIR__ . '/functions/shortcode.php';

// Register hooks
add_shortcode('school_management', 'render_school_management_shortcode');

add_action('rest_api_init', 'register_school_management_routes');

add_action('init', 'create_school_management_page');

add_action('add_meta_boxes', 'add_entity_meta_boxes');

add_action('save_post', 'save_entity_meta');

add_action('add_meta_boxes', 'add_class_meta_boxes');

add_action('save_post', 'save_class_meta');

add_action('admin_enqueue_scripts', 'enqueue_media_scripts');

/**
 * Create all post types
 */
function create_school_management_page()
{
    register_school_post_type();
    register_class_post_type();
    register_entity_post_type();
}