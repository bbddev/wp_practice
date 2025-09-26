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
            'name' => 'Thực thể',
            'singular_name' => 'Thực thể',
            'edit_item' => 'View Thực thể',
            'add_new_item' => 'Add Entity',
        ],
        'supports' => ['title'],
    ];
    register_post_type('entity', $args_entity);
}