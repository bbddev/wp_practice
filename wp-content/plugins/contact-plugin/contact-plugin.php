<?php
/**
 * Plugin Name: Contact Plugin
 * Description: This is my test plugin.
 * Version: 1.0
 * Text Domain: contact-plugin
 */

if (!defined('ABSPATH')) {
    die('You cannot be bypassed'); // Exit if accessed directly.
}

if (!class_exists('ContactPlugin')) {
    class ContactPlugin
    {
        public function __construct()
        {
            define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
            require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');
        }

    }
    new ContactPlugin;
}