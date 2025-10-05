<?php
/*
Plugin Name: School Management
Description: School management plugin.
Version: 1.0
Author: Binh Vo
*/

if (!defined('ABSPATH')) {
    exit; 
}

if (!class_exists('SchoolManagement')) {
    class SchoolManagement
    {
        public function __construct()
        {
            define('SCHOOLPLUGIN_PATH', plugin_dir_path(__FILE__));
            define('SCHOOLPLUGIN_URL', plugin_dir_url(__FILE__));
        }

        public function initialize_plugin()
        {
            // Plugin initialization code here
            require_once SCHOOLPLUGIN_PATH . 'includes/school-management.php';
        }
    }

    $school_management = new SchoolManagement();
    $school_management->initialize_plugin();
}