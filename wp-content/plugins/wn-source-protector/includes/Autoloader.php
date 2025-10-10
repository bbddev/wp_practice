<?php

if (!defined('ABSPATH')) {
    exit;
}

class WNSP_Autoloader
{
    private static $classes = array(
        'WNSP_SessionManager' => 'classes/SessionManager.php',
        'WNSP_AccessController' => 'classes/AccessController.php',
        'WNSP_StudyCountManager' => 'classes/StudyCountManager.php',
        'WNSP_LoginRenderer' => 'classes/LoginRenderer.php',
    );

    public static function init()
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($class)
    {
        if (isset(self::$classes[$class])) {
            $file = dirname(__FILE__) . '/' . self::$classes[$class];
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    public static function load_all_classes()
    {
        foreach (self::$classes as $class => $file) {
            $full_path = dirname(__FILE__) . '/' . $file;
            if (file_exists($full_path)) {
                require_once $full_path;
            }
        }
    }
}