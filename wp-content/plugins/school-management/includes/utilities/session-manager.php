<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/SessionAutoloader.php';
SessionAutoloader::init();

/**
 * Student Session Manager Class (Refactored)
 * @version 2.0.0 
 * @author WN-DEVBINH
 * 
 * class chính quản lý session, sử dụng các class helper
 */
class StudentSessionManager implements SessionInterface
{
    const SESSION_KEY = 'student_id';

    public static function checkSession()
    {
        if (!SessionHelper::hasSessionData()) {
            return array(
                'logged_in' => false,
                'student_id' => 0,
                'student_name' => '',
                'student_of' => '',
                'user_ip' => '',
                'device_browser' => '',
                'device_os' => '',
                'device_platform' => '',
                'login_time' => 0,
            );
        }

        $session_data = SessionHelper::getSessionData();
        $student_id = $session_data['student_id'];
        $session_token = $session_data['session_token'];

        if (!SessionStorage::isValidSession($student_id, $session_token)) {
            self::clearSession();
            return self::checkSession();
        }

        if (!StudentValidator::isValidStudentPost($student_id)) {
            self::clearSession();
            return self::checkSession();
        }

        SessionStorage::updateSessionActivity($student_id, $session_token);

        return array_merge($session_data, array('logged_in' => true));
    }

    public static function login($username, $password)
    {
        $validation = StudentValidator::validateLoginInput($username, $password);
        if (!$validation['valid']) {
            return array(
                'success' => false,
                'message' => $validation['message'],
                'error_code' => $validation['error_code']
            );
        }

        $students = StudentValidator::findStudentByUsername($username);
        if (!$students) {
            return array(
                'success' => false,
                'message' => 'Invalid credentials',
                'error_code' => 'INVALID_CREDENTIALS'
            );
        }

        foreach ($students as $student) {
            if (StudentValidator::verifyPassword($student, $password)) {
                return self::performLogin($student);
            }
        }

        return array(
            'success' => false,
            'message' => 'Invalid credentials',
            'error_code' => 'INVALID_CREDENTIALS'
        );
    }

    private static function performLogin($student)
    {
        $student_data = StudentValidator::getStudentData($student);
        $user_ip = DeviceDetection::getUserIP();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $device_info = DeviceDetection::parseUserAgent($user_agent);

        $session_token = SessionStorage::generateSessionToken();
        SessionStorage::invalidateOtherSessions($student_data['student_id'], $session_token);

        $device_data = array_merge($device_info, array(
            'user_ip' => $user_ip,
            'user_agent' => $user_agent
        ));
        SessionHelper::setSessionData($student_data, $device_data, $session_token);

        SessionStorage::saveActiveSession(
            $student_data['student_id'],
            $session_token,
            $user_ip,
            $device_info,
            $user_agent
        );

        return array(
            'success' => true,
            'student_id' => $student_data['student_id'],
            'student_name' => $student_data['student_name'],
            'student_of' => $student_data['student_of'],
            'user_ip' => $user_ip,
            'device_info' => $device_info,
            'message' => 'Login successful'
        );
    }

    public static function logout()
    {
        if (SessionHelper::hasSessionData()) {
            $session_data = SessionHelper::getSessionData();

            if ($session_data['student_id'] > 0) {
                SessionStorage::removeSessionFromDatabase($session_data['student_id']);
            }
        }

        SessionHelper::clearAllSessionKeys();

        return array(
            'success' => true,
            'message' => 'Logged out successfully'
        );
    }

    private static function clearSession()
    {
        SessionHelper::clearAllSessionKeys();
    }

    public static function getCurrentStudent()
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            return null;
        }

        return StudentValidator::getCurrentStudentData($session['student_id']);
    }

    public static function hasAccessToResource($resource_id, $resource_type = 'school')
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            return false;
        }

        return StudentValidator::hasAccessToResource($session['student_id'], $resource_id, $resource_type);
    }

    public static function requireLogin($redirect_url = '')
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            if (!empty($redirect_url)) {
                wp_redirect($redirect_url);
                exit;
            }
            return false;
        }

        return true;
    }

    public static function getSessionStats()
    {
        return SessionHelper::getSessionStats();
    }

}