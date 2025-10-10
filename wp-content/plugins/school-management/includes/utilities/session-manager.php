<?php

if (!defined('ABSPATH')) {
    exit;
}

// Load tất cả các class dependencies
require_once dirname(__FILE__) . '/SessionAutoloader.php';
SessionAutoloader::init();

/**
 * Student Session Manager Class (Refactored)
 * @version 2.0.0 
 * @author Your Name
 * 
 * Đây là class chính quản lý session, sử dụng các class helper
 */
class StudentSessionManager implements SessionInterface
{
    /**
     * Session key để lưu student ID
     */
    const SESSION_KEY = 'school_management_student_id';

    /**
     * Kiểm tra xem student có đang login hay không
     * 
     * @return array Thông tin session hiện tại
     */
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

        // Kiểm tra session token có hợp lệ không (chưa bị logout từ thiết bị khác)
        if (!SessionStorage::isValidSession($student_id, $session_token)) {
            // Session không hợp lệ, xóa session local
            self::clearSession();
            return self::checkSession(); // Recursive call để return empty session
        }

        // Kiểm tra student post có hợp lệ không
        if (!StudentValidator::isValidStudentPost($student_id)) {
            self::clearSession();
            return self::checkSession(); // Recursive call để return empty session
        }

        // Cập nhật last activity
        SessionStorage::updateSessionActivity($student_id, $session_token);

        // Return session data với logged_in = true
        return array_merge($session_data, array('logged_in' => true));
    }

    /**
     * Xử lý đăng nhập student
     * 
     * @param string $username Username của student
     * @param string $password Password của student
     * @return array Kết quả đăng nhập
     */
    public static function login($username, $password)
    {
        // Validate input
        $validation = StudentValidator::validateLoginInput($username, $password);
        if (!$validation['valid']) {
            return array(
                'success' => false,
                'message' => $validation['message'],
                'error_code' => $validation['error_code']
            );
        }

        // Tìm student bằng username
        $students = StudentValidator::findStudentByUsername($username);
        if (!$students) {
            return array(
                'success' => false,
                'message' => 'Invalid credentials',
                'error_code' => 'INVALID_CREDENTIALS'
            );
        }

        // Tìm student có password matching
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

    /**
     * Thực hiện đăng nhập sau khi validate thành công
     * 
     * @param object $student Student post object
     * @return array Kết quả đăng nhập
     */
    private static function performLogin($student)
    {
        // Lấy thông tin student và thiết bị
        $student_data = StudentValidator::getStudentData($student);
        $user_ip = DeviceDetection::getUserIP();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $device_info = DeviceDetection::parseUserAgent($user_agent);

        // Tạo session token mới và hủy session khác
        $session_token = SessionStorage::generateSessionToken();
        SessionStorage::invalidateOtherSessions($student_data['student_id'], $session_token);

        // Set session data
        $device_data = array_merge($device_info, array(
            'user_ip' => $user_ip,
            'user_agent' => $user_agent
        ));
        SessionHelper::setSessionData($student_data, $device_data, $session_token);

        // Lưu session vào database
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

    /**
     * Đăng xuất student - xóa session
     * 
     * @return array Kết quả đăng xuất
     */
    public static function logout()
    {
        if (SessionHelper::hasSessionData()) {
            $session_data = SessionHelper::getSessionData();

            // Xóa session khỏi database
            if ($session_data['student_id'] > 0) {
                SessionStorage::removeSessionFromDatabase($session_data['student_id']);
            }
        }

        // Xóa tất cả session data local
        SessionHelper::clearAllSessionKeys();

        return array(
            'success' => true,
            'message' => 'Logged out successfully'
        );
    }

    /**
     * Xóa session (private method để sử dụng internal)
     */
    private static function clearSession()
    {
        SessionHelper::clearAllSessionKeys();
    }

    /**
     * Lấy thông tin student hiện đang login
     * 
     * @return array|null Thông tin student hoặc null nếu chưa login
     */
    public static function getCurrentStudent()
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            return null;
        }

        return StudentValidator::getCurrentStudentData($session['student_id']);
    }

    /**
     * Kiểm tra xem student có quyền truy cập resource nào đó không
     * 
     * @param int $resource_id ID của resource cần kiểm tra
     * @param string $resource_type Loại resource (school, class, etc.)
     * @return bool True nếu có quyền truy cập
     */
    public static function hasAccessToResource($resource_id, $resource_type = 'school')
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            return false;
        }

        return StudentValidator::hasAccessToResource($session['student_id'], $resource_id, $resource_type);
    }

    /**
     * Validate session và redirect nếu chưa login (dành cho WordPress)
     * 
     * @param string $redirect_url URL để redirect nếu chưa login
     * @return bool True nếu đã login, false nếu chưa login
     */
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

    /**
     * Get session statistics (for debugging/monitoring)
     * 
     * @return array Session statistics
     */
    public static function getSessionStats()
    {
        return SessionHelper::getSessionStats();
    }

}