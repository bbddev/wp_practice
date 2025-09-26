<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register School post type
 */
function register_school_post_type()
{
    $args_school = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 30,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Trường',
            'singular_name' => 'Trường',
            'edit_item' => 'View Trường',
            'add_new_item' => 'Add Trường',
        ],
    ];
    register_post_type('school', $args_school);
}