<?php
/**
 * Plugin Name: Data Plugin
 * Description: A WordPress plugin to manage custom data entries with CSV and JSON import/export functionality.
 * Version: 2.0
 * Author: Binh Vo
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('BB_DATA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BB_DATA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once BB_DATA_PLUGIN_PATH . 'includes/post-types.php';
require_once BB_DATA_PLUGIN_PATH . 'includes/templates/admin-page.php';
require_once BB_DATA_PLUGIN_PATH . 'includes/csv-handler.php';
require_once BB_DATA_PLUGIN_PATH . 'includes/json-handler.php';
require_once BB_DATA_PLUGIN_PATH . 'includes/admin-columns.php';

/**
 * Initialize plugin
 */
function bb_data_plugin_init()
{
    // Start session if not already started
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'bb_data_plugin_init');

/**
 * Enqueue admin styles and scripts
 */
function bb_data_plugin_admin_assets($hook)
{
    // Only load on our plugin pages
    if ($hook !== 'toplevel_page_my-data-plugin-posts') {
        return;
    }

    // Enqueue CSS
    wp_enqueue_style(
        'bb-data-admin-css',
        BB_DATA_PLUGIN_URL . 'assets/admin-styles.css',
        array(),
        '1.0'
    );

    // Enqueue JavaScript
    wp_enqueue_script(
        'bb-data-admin-js',
        BB_DATA_PLUGIN_URL . 'assets/admin-scripts.js',
        array(),
        '1.0',
        true
    );

    // Localize script for AJAX
    wp_localize_script('bb-data-admin-js', 'bb_data_ajax', array(
        'export_nonce' => wp_create_nonce('bb_data_export'),
        'export_json_nonce' => wp_create_nonce('bb_data_export_json')
    ));
}
add_action('admin_enqueue_scripts', 'bb_data_plugin_admin_assets');