<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Autoloader for Session Management Classes
 * Tự động load các class cần thiết
 */
class SessionAutoloader
{
    /**
     * Đường dẫn base của utilities
     */
    private static $base_path;

    /**
     * Khởi tạo autoloader
     */
    public static function init()
    {
        self::$base_path = dirname(__FILE__) . '/';
        self::loadAllClasses();
    }

    /**
     * Load tất cả các class cần thiết
     */
    private static function loadAllClasses()
    {
        $classes = array(
            // Interfaces
            'interfaces/SessionInterface.php',

            // Classes
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

    /**
     * Kiểm tra tất cả class đã được load chưa
     * 
     * @return array Danh sách class và trạng thái
     */
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