<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register student post type
 */
function register_student_post_type()
{
    $args_student = [
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'edit.php?post_type=school',
        'menu_icon' => 'dashicons-id',
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Học sinh',
            'singular_name' => 'Học sinh',
            'edit_item' => 'View Học sinh',
            'add_new_item' => 'Add Học sinh',
        ],
        'supports' => ['title'],
    ];
    register_post_type('student', $args_student);
}