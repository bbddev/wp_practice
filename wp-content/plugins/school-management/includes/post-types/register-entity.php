<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register Entity post type
 */
function register_entity_post_type()
{
    $args_entity = [
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'edit.php?post_type=school',
        'menu_icon' => 'dashicons-id',
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Bài học',
            'singular_name' => 'Bài học',
            'edit_item' => 'View Bài học',
            'add_new_item' => 'Add Bài học',
        ],
        'supports' => ['title'],
    ];
    register_post_type('entity', $args_entity);
}