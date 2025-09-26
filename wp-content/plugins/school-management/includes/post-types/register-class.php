<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register Class post type
 */
function register_class_post_type()
{
    $args_class = [
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'edit.php?post_type=school',
        'menu_icon' => 'dashicons-groups',
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Lớp học',
            'singular_name' => 'Lớp học',
            'edit_item' => 'View Lớp học',
            'add_new_item' => 'Add Lớp',
        ],
    ];
    register_post_type('class', $args_class);
}