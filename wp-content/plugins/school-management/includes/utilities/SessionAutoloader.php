<?php

if (!defined('ABSPATH')) {
    exit;
}

class SessionAutoloader
{
    private static $base_path;

    public static function init()
    {
        self::$base_path = dirname(__FILE__) . '/';
        self::loadAllClasses();
    }

    private static function loadAllClasses()
    {
        $classes = array(
            'interfaces/SessionInterface.php',
            'classes/DeviceDetection.php',
            'classes/SessionStorage.php',
            'classes/SessionHelper.php',
            'classes/StudentValidator.php'
        );

        foreach ($classes as $class_file) {
            $file_path = self::$base_path . $class_file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }

    public static function checkLoadedClasses()
    {
        $required_classes = array(
            'SessionInterface',
            'DeviceDetection',
            'SessionStorage',
            'SessionHelper',
            'StudentValidator'
        );

        $status = array();
        foreach ($required_classes as $class) {
            $status[$class] = class_exists($class) || interface_exists($class);
        }

        return $status;
    }
}