<?php
/**
 * Admin Columns Handler for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add custom columns to post list in admin
 */
function bb_data_add_custom_columns()
{
    add_filter('manage_class_posts_columns', 'bb_data_custom_columns');
    add_filter('manage_entity_posts_columns', 'bb_data_custom_columns');
    add_filter('manage_student_posts_columns', 'bb_data_custom_columns');

    add_action('manage_class_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
    add_action('manage_entity_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
    add_action('manage_student_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
}
// add_action('init', 'bb_data_add_custom_columns');

/**
 * Define custom columns
 */
function bb_data_custom_columns($columns)
{
    global $typenow;
    unset($columns['date']);

    if ($typenow === 'class') {
        $columns['title'] = ' List';
        $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
    } elseif ($typenow === 'entity') {
        $columns['title'] = 'Lesson';
        // $columns['csv_username'] = 'Username';
        // $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
        $columns['csv_link'] = 'Link';
        $columns['csv_image'] = 'Image';
    } elseif ($typenow === 'student') {
        $columns['title'] = 'Student';
        $columns['csv_username'] = 'Username';
        // $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Khối';
        $columns['csv_link'] = 'Link';
        $columns['csv_image'] = 'Image';
    }

    return $columns;
}

/**
 * Display custom column content
 */
function bb_data_custom_column_content($column, $post_id)
{
    $post_type = get_post_type($post_id);

    switch ($column) {
        case 'csv_password':
            bb_data_display_password_column($post_type, $post_id);
            break;

        case 'csv_parent':
            bb_data_display_parent_column($post_type, $post_id);
            break;

        case 'csv_link':
            bb_data_display_link_column($post_type, $post_id);
            break;

        case 'csv_image':
            bb_data_display_image_column($post_type, $post_id);
            break;

        case 'csv_username':
            bb_data_display_username_column($post_type, $post_id);
            break;
    }
}

/**
 * Display password column
 */
function bb_data_display_password_column($post_type, $post_id)
{
    $password = '';

    if ($post_type === 'class') {
        $password = get_post_meta($post_id, 'class_password', true);
    } elseif ($post_type === 'entity') {
        $password = get_post_meta($post_id, 'lesson_password', true);
    } elseif ($post_type === 'student') {
        $password = get_post_meta($post_id, 'student_password', true);
    }

    echo $password ? '****' : '-';
}

/**
 * Display parent column
 */
function bb_data_display_parent_column($post_type, $post_id)
{
    $parent = '';

    if ($post_type === 'class') {
        $parent = get_post_meta($post_id, 'Thuộc Trường', true);
    } elseif ($post_type === 'entity') {
        $parent = get_post_meta($post_id, 'Thuộc lớp', true);
    } elseif ($post_type === 'student') {
        $parent = get_post_meta($post_id, 'student_of', true);
    }

    echo esc_html($parent ?: '-');
}

/**
 * Display link column
 */
function bb_data_display_link_column($post_type, $post_id)
{
    if ($post_type === 'entity') {
        $link = get_post_meta($post_id, 'Link khi click', true);
        if ($link) {
            echo '<a href="' . esc_url($link) . '" target="_blank" title="' . esc_attr($link) . '">View Link</a>';
        } else {
            echo '-';
        }
    } elseif ($post_type === 'student') {
        $link = get_post_meta($post_id, 'student_link', true);
        if ($link) {
            echo '<a href="' . esc_url($link) . '" target="_blank" title="' . esc_attr($link) . '">View Link</a>';
        } else {
            echo '-';
        }
    }

}

/**
 * Display image column
 */
function bb_data_display_image_column($post_type, $post_id)
{
    if ($post_type === 'entity') {
        $image_url = get_post_meta($post_id, 'Hình', true);
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="width: 50px; height: 50px; object-fit: cover;" alt="Preview" />';
        } else {
            echo '-';
        }
    } elseif ($post_type === 'student') {
        $image_url = get_post_meta($post_id, 'student_image', true);
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" style="width: 50px; height: 50px; object-fit: cover;" alt="Preview" />';
        } else {
            echo '-';
        }
    }
}
/**
 * Display username column
 */
function bb_data_display_username_column($post_type, $post_id)
{
    if ($post_type === 'entity') {
        $username = get_post_meta($post_id, 'Username', true);
        echo esc_html($username ?: '-');
    } elseif ($post_type === 'student') {
        $username = get_post_meta($post_id, 'student_username', true);
        echo esc_html($username ?: '-');
    }
}