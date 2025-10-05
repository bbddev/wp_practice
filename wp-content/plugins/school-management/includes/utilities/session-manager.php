<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Student Session Manager Class
 * @version 1.0.0
 * @author Your Name
 */
class StudentSessionManager
{
    /**
     * Session key để lưu student ID
     */
    const SESSION_KEY = 'school_management_student_id';

    /**
     * Post type của student
     */
    const STUDENT_POST_TYPE = 'student';

    /**
     * Meta key cho username của student
     */
    const USERNAME_META_KEY = 'student_username';

    /**
     * Meta key cho password của student
     */
    const PASSWORD_META_KEY = 'student_password';

    /**
     * Khởi tạo PHP session nếu chưa có
     */
    private static function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Kiểm tra xem student có đang login hay không
     * 
     * @return array Thông tin session hiện tại
     */
    public static function checkSession()
    {
        self::ensureSessionStarted();

        $student_id = isset($_SESSION[self::SESSION_KEY]) ? intval($_SESSION[self::SESSION_KEY]) : 0;
        $student_name = '';

        if ($student_id > 0) {
            $student_post = get_post($student_id);
            if ($student_post && $student_post->post_type === self::STUDENT_POST_TYPE) {
                $student_name = $student_post->post_title;
            } else {
                // Post không tồn tại hoặc không phải student, xóa session
                self::clearSession();
                $student_id = 0;
            }
        }

        return array(
            'logged_in' => $student_id > 0,
            'student_id' => $student_id,
            'student_name' => $student_name,
        );
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
        self::ensureSessionStarted();

        // Validate input
        if (empty($username) || empty($password)) {
            return array(
                'success' => false,
                'message' => 'Username and password are required',
                'error_code' => 'INVALID_INPUT'
            );
        }

        // Sanitize input
        $username = sanitize_text_field($username);
        $password = sanitize_text_field($password);

        // Query students với username matching
        $args = array(
            'post_type' => self::STUDENT_POST_TYPE,
            'numberposts' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => self::USERNAME_META_KEY,
                    'value' => $username,
                    'compare' => '='
                )
            )
        );

        $students = get_posts($args);

        if (!$students || count($students) === 0) {
            return array(
                'success' => false,
                'message' => 'Invalid credentials',
                'error_code' => 'INVALID_CREDENTIALS'
            );
        }

        // Tìm student có password matching
        foreach ($students as $student) {
            $stored_password = get_post_meta($student->ID, self::PASSWORD_META_KEY, true);

            // So sánh password (có thể thay bằng password_verify() nếu dùng hash)
            if ($password === $stored_password) {
                // Đăng nhập thành công - set session
                $_SESSION[self::SESSION_KEY] = $student->ID;

                return array(
                    'success' => true,
                    'student_id' => $student->ID,
                    'student_name' => $student->post_title,
                    'message' => 'Login successful'
                );
            }
        }

        return array(
            'success' => false,
            'message' => 'Invalid credentials',
            'error_code' => 'INVALID_CREDENTIALS'
        );
    }

    /**
     * Đăng xuất student - xóa session
     * 
     * @return array Kết quả đăng xuất
     */
    public static function logout()
    {
        self::ensureSessionStarted();

        // Xóa student session
        unset($_SESSION[self::SESSION_KEY]);

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
        self::ensureSessionStarted();
        unset($_SESSION[self::SESSION_KEY]);
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

        $student_post = get_post($session['student_id']);

        if (!$student_post || $student_post->post_type !== self::STUDENT_POST_TYPE) {
            return null;
        }

        // Lấy thông tin meta của student
        $student_data = array(
            'id' => $student_post->ID,
            'name' => $student_post->post_title,
            'username' => get_post_meta($student_post->ID, self::USERNAME_META_KEY, true),
            'post_date' => $student_post->post_date,
            'post_status' => $student_post->post_status,
        );

        // Có thể thêm các meta fields khác nếu cần
        $custom_fields = array('student_email', 'student_phone', 'student_class');
        foreach ($custom_fields as $field) {
            $value = get_post_meta($student_post->ID, $field, true);
            if (!empty($value)) {
                $student_data[$field] = $value;
            }
        }

        return $student_data;
    }

    /**
     * Kiểm tra xem student có quyền truy cập resource nào đó không
     * 
     * @param int $resource_id ID của resource cần kiểm tra
     * @param string $resource_type Loại resource (class, entity, etc.)
     * @return bool True nếu có quyền truy cập
     */
    public static function hasAccessToResource($resource_id, $resource_type = 'class')
    {
        $session = self::checkSession();

        if (!$session['logged_in']) {
            return false;
        }

        // Logic kiểm tra quyền truy cập có thể customize tùy theo yêu cầu
        // Ví dụ: kiểm tra student có thuộc class này không
        if ($resource_type === 'class') {
            $student_class = get_post_meta($session['student_id'], 'student_class', true);
            return !empty($student_class) && intval($student_class) === intval($resource_id);
        }

        return true; // Default cho phép truy cập
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
        self::ensureSessionStarted();

        return array(
            'session_id' => session_id(),
            'session_status' => session_status(),
            'logged_in_student' => isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : 0,
            'all_session_keys' => array_keys($_SESSION),
            'session_cookie_params' => session_get_cookie_params(),
        );
    }
}