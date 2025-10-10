<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Session Storage Class
 * Xử lý lưu trữ và quản lý session trong database
 */
class SessionStorage
{
    /**
     * Tạo session token duy nhất
     * 
     * @return string Session token
     */
    public static function generateSessionToken()
    {
        return wp_generate_password(32, false) . '_' . time();
    }

    /**
     * Lưu thông tin session active vào database
     * 
     * @param int $student_id ID của student
     * @param string $session_token Session token
     * @param string $user_ip IP address
     * @param array $device_info Thông tin thiết bị
     * @param string $user_agent User agent string
     */
    public static function saveActiveSession($student_id, $session_token, $user_ip, $device_info, $user_agent)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;

        $session_data = array(
            'token' => $session_token,
            'ip' => $user_ip,
            'user_agent' => $user_agent,
            'browser' => $device_info['browser'],
            'os' => $device_info['os'],
            'platform' => $device_info['platform'],
            'login_time' => time(),
            'last_activity' => time()
        );

        // Chỉ lưu session hiện tại (single device)
        $active_sessions = array($session_token => $session_data);
        update_option($active_sessions_key, $active_sessions);
    }

    /**
     * Hủy tất cả session khác của student (chỉ giữ lại session hiện tại)
     * 
     * @param int $student_id ID của student
     * @param string $current_token Token của session hiện tại (không bị hủy)
     */
    public static function invalidateOtherSessions($student_id, $current_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        // Xóa tất cả session cũ (thiết bị khác sẽ bị logout)
        if (!empty($active_sessions)) {
            delete_option($active_sessions_key);
        }
    }

    /**
     * Kiểm tra tính hợp lệ của session hiện tại
     * 
     * @param int $student_id ID của student
     * @param string $session_token Session token cần kiểm tra
     * @return bool True nếu session hợp lệ
     */
    public static function isValidSession($student_id, $session_token)
    {
        if (empty($session_token)) {
            return false;
        }

        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        return isset($active_sessions[$session_token]);
    }

    /**
     * Cập nhật last activity cho session
     * 
     * @param int $student_id ID của student  
     * @param string $session_token Session token
     */
    public static function updateSessionActivity($student_id, $session_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        if (isset($active_sessions[$session_token])) {
            $active_sessions[$session_token]['last_activity'] = time();
            update_option($active_sessions_key, $active_sessions);
        }
    }

    /**
     * Xóa session khỏi database
     * 
     * @param int $student_id ID của student
     */
    public static function removeSessionFromDatabase($student_id)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        delete_option($active_sessions_key);
    }

    /**
     * Lấy thông tin session từ database
     * 
     * @param int $student_id ID của student
     * @param string $session_token Session token
     * @return array|null Thông tin session
     */
    public static function getSessionData($student_id, $session_token)
    {
        $active_sessions_key = 'student_active_sessions_' . $student_id;
        $active_sessions = get_option($active_sessions_key, array());

        return isset($active_sessions[$session_token]) ? $active_sessions[$session_token] : null;
    }
}