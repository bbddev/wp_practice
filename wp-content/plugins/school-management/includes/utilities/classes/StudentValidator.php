<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Student Validator Class
 * Xử lý validation và authentication cho student
 */
class StudentValidator
{
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
     * Validate đầu vào cho đăng nhập
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array Kết quả validation
     */
    public static function validateLoginInput($username, $password)
    {
        if (empty($username) || empty($password)) {
            return array(
                'valid' => false,
                'message' => 'Username and password are required',
                'error_code' => 'INVALID_INPUT'
            );
        }

        return array('valid' => true);
    }

    /**
     * Tìm student bằng username
     * 
     * @param string $username Username của student
     * @return array|false Danh sách student hoặc false nếu không tìm thấy
     */
    public static function findStudentByUsername($username)
    {
        $username = sanitize_text_field($username);

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

        return !empty($students) ? $students : false;
    }

    /**
     * Verify password cho student
     * 
     * @param object $student Student post object
     * @param string $password Password cần kiểm tra
     * @return bool True nếu password đúng
     */
    public static function verifyPassword($student, $password)
    {
        $stored_password = get_post_meta($student->ID, self::PASSWORD_META_KEY, true);

        // So sánh password (có thể thay bằng password_verify() nếu dùng hash)
        return $password === $stored_password;
    }

    /**
     * Lấy thông tin student từ post object
     * 
     * @param object $student Student post object
     * @return array Thông tin student
     */
    public static function getStudentData($student)
    {
        return array(
            'student_id' => $student->ID,
            'student_name' => $student->post_title,
            'student_of' => get_post_meta($student->ID, 'student_of', true)
        );
    }

    /**
     * Kiểm tra student post có hợp lệ hay không
     * 
     * @param int $student_id ID của student
     * @return bool True nếu hợp lệ
     */
    public static function isValidStudentPost($student_id)
    {
        $student_post = get_post($student_id);
        return $student_post && $student_post->post_type === self::STUDENT_POST_TYPE;
    }

    /**
     * Lấy thông tin đầy đủ của student hiện tại
     * 
     * @param int $student_id ID của student
     * @return array|null Thông tin student
     */
    public static function getCurrentStudentData($student_id)
    {
        if (!self::isValidStudentPost($student_id)) {
            return null;
        }

        $student_post = get_post($student_id);

        // Lấy thông tin meta của student
        $student_data = array(
            'id' => $student_post->ID,
            'name' => $student_post->post_title,
            'username' => get_post_meta($student_post->ID, self::USERNAME_META_KEY, true),
            'post_date' => $student_post->post_date,
            'post_status' => $student_post->post_status,
        );

        // Có thể thêm các meta fields khác nếu cần
        $custom_fields = array('student_email', 'student_phone', 'student_of');
        foreach ($custom_fields as $field) {
            $value = get_post_meta($student_post->ID, $field, true);
            if (!empty($value)) {
                $student_data[$field] = $value;
            }
        }

        return $student_data;
    }

    /**
     * Kiểm tra quyền truy cập resource
     * 
     * @param int $student_id ID của student
     * @param int $resource_id ID của resource cần kiểm tra
     * @param string $resource_type Loại resource
     * @return bool True nếu có quyền truy cập
     */
    public static function hasAccessToResource($student_id, $resource_id, $resource_type = 'school')
    {
        if ($resource_type === 'school') {
            $student_of = get_post_meta($student_id, 'student_of', true);
            return !empty($student_of) && intval($student_of) === intval($resource_id);
        }

        return true; // Default cho phép truy cập
    }
}