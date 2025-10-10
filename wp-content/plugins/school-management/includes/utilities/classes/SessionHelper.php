<?php

if (!defined('ABSPATH')) {
    exit;
}

class SessionHelper
{
    const SESSION_KEYS_TO_REMOVE = array(
        'school_management_student_id',
        'student_name',
        'student_of',
        'login_time',
        'last_activity',
        'user_ip',
        'user_agent',
        'device_browser',
        'device_os',
        'device_platform',
        'session_token'
    );

    public static function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function clearAllSessionKeys()
    {
        self::ensureSessionStarted();

        foreach (self::SESSION_KEYS_TO_REMOVE as $key) {
            unset($_SESSION[$key]);
        }
    }

    public static function setSessionData($student_data, $device_info, $session_token)
    {
        self::ensureSessionStarted();

        $_SESSION['school_management_student_id'] = $student_data['student_id'];
        $_SESSION['student_name'] = $student_data['student_name'];
        $_SESSION['student_of'] = $student_data['student_of'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['user_ip'] = $device_info['user_ip'];
        $_SESSION['user_agent'] = $device_info['user_agent'];
        $_SESSION['device_browser'] = $device_info['browser'];
        $_SESSION['device_os'] = $device_info['os'];
        $_SESSION['device_platform'] = $device_info['platform'];
        $_SESSION['session_token'] = $session_token;
    }

    public static function getSessionData()
    {
        self::ensureSessionStarted();

        return array(
            'student_id' => isset($_SESSION['school_management_student_id']) ? intval($_SESSION['school_management_student_id']) : 0,
            'session_token' => isset($_SESSION['session_token']) ? $_SESSION['session_token'] : '',
            'student_name' => isset($_SESSION['student_name']) ? $_SESSION['student_name'] : '',
            'student_of' => isset($_SESSION['student_of']) ? $_SESSION['student_of'] : '',
            'user_ip' => isset($_SESSION['user_ip']) ? $_SESSION['user_ip'] : '',
            'device_browser' => isset($_SESSION['device_browser']) ? $_SESSION['device_browser'] : '',
            'device_os' => isset($_SESSION['device_os']) ? $_SESSION['device_os'] : '',
            'device_platform' => isset($_SESSION['device_platform']) ? $_SESSION['device_platform'] : '',
            'login_time' => isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0,
        );
    }

    public static function hasSessionData()
    {
        self::ensureSessionStarted();
        return isset($_SESSION['school_management_student_id']) && $_SESSION['school_management_student_id'] > 0;
    }

    public static function getSessionStats()
    {
        self::ensureSessionStarted();

        return array(
            'session_id' => session_id(),
            'session_status' => session_status(),
            'logged_in_student' => isset($_SESSION['school_management_student_id']) ? $_SESSION['school_management_student_id'] : 0,
            'all_session_keys' => array_keys($_SESSION),
            'session_cookie_params' => session_get_cookie_params(),
        );
    }
}