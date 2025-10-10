<?php

if (!defined('ABSPATH')) {
    exit;
}

class WNSP_SessionManager
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ensure StudentSessionManager is loaded
     */
    public function ensure_session_manager_loaded()
    {
        if (file_exists(WNSP_SM_PATH)) {
            require_once WNSP_SM_PATH;
        }
    }

    /**
     * Check if user is logged in
     */
    public function is_logged_in()
    {
        $this->ensure_session_manager_loaded();

        if (!class_exists('StudentSessionManager')) {
            return false;
        }

        $session_data = StudentSessionManager::checkSession();
        return $session_data['logged_in'];
    }

    /**
     * Get current session data
     */
    public function get_session_data()
    {
        $this->ensure_session_manager_loaded();

        if (!class_exists('StudentSessionManager')) {
            return ['logged_in' => false];
        }

        return StudentSessionManager::checkSession();
    }

    /**
     * Handle AJAX login
     */
    public function handle_login($username, $password)
    {
        $username = $this->sanitize_text_field($username);

        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin'
            ];
        }

        $this->ensure_session_manager_loaded();

        if (!class_exists('StudentSessionManager')) {
            return [
                'success' => false,
                'message' => 'Hệ thống đăng nhập không khả dụng'
            ];
        }

        $login_result = StudentSessionManager::login($username, $password);

        if ($login_result['success']) {
            return [
                'success' => true,
                'message' => 'Đăng nhập thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => $login_result['message'] ?? 'Tên đăng nhập hoặc mật khẩu không đúng'
            ];
        }
    }

    /**
     * Sanitize text field
     */
    private function sanitize_text_field($str)
    {
        if (function_exists('sanitize_text_field')) {
            return sanitize_text_field($str);
        }
        return trim(strip_tags($str));
    }
}