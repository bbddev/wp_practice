<?php
/**
 * Custom Post Types Registration for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register all custom post types
 */
function bb_data_plugin_register_post_types()
{
    bb_data_plugin_register_school_post_type();
    bb_data_plugin_register_class_post_type();
    bb_data_plugin_register_entity_post_type();
}
// add_action('init', 'bb_data_plugin_register_post_types');

/**
 * Register School Post Type
 */
function bb_data_plugin_register_school_post_type()
{
    register_post_type('school', array(
        'labels' => array(
            'name' => 'Schools',
            'singular_name' => 'School',
            'add_new' => 'Add New School',
            'add_new_item' => 'Add New School',
            'edit_item' => 'Edit School',
            'new_item' => 'New School',
            'view_item' => 'View School',
            'search_items' => 'Search Schools',
            'not_found' => 'No Schools found',
            'not_found_in_trash' => 'No Schools found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-building'
    ));
}

/**
 * Register Class Post Type
 */
function bb_data_plugin_register_class_post_type()
{
    register_post_type('class', array(
        'labels' => array(
            'name' => 'Classes',
            'singular_name' => 'Class',
            'add_new' => 'Add New Class',
            'add_new_item' => 'Add New Class',
            'edit_item' => 'Edit Class',
            'new_item' => 'New Class',
            'view_item' => 'View Class',
            'search_items' => 'Search Classes',
            'not_found' => 'No Classes found',
            'not_found_in_trash' => 'No Classes found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-groups'
    ));
}

/**
 * Register Entity Post Type
 */
function bb_data_plugin_register_entity_post_type()
{
    register_post_type('entity', array(
        'labels' => array(
            'name' => 'Entities',
            'singular_name' => 'Entity',
            'add_new' => 'Add New Entity',
            'add_new_item' => 'Add New Entity',
            'edit_item' => 'Edit Entity',
            'new_item' => 'New Entity',
            'view_item' => 'View Entity',
            'search_items' => 'Search Entities',
            'not_found' => 'No Entities found',
            'not_found_in_trash' => 'No Entities found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-media-document'
    ));
}

/**
 * Plugin activation hook
 */
function bb_data_plugin_activation()
{
    bb_data_plugin_register_post_types();
    flush_rewrite_rules();
}
register_activation_hook(dirname(__DIR__) . '/data-plugin.php', 'bb_data_plugin_activation');