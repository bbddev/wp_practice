<?php

if (!defined('ABSPATH')) {
    exit;
}

class StudentValidator
{
    const STUDENT_POST_TYPE = 'student';

    const USERNAME_META_KEY = 'student_username';

    const PASSWORD_META_KEY = 'student_password';

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

    public static function verifyPassword($student, $password)
    {
        $stored_password = get_post_meta($student->ID, self::PASSWORD_META_KEY, true);

        return $password === $stored_password;
    }

    public static function getStudentData($student)
    {
        return array(
            'student_id' => $student->ID,
            'student_name' => $student->post_title,
            'student_of' => get_post_meta($student->ID, 'student_of', true)
        );
    }

    public static function isValidStudentPost($student_id)
    {
        $student_post = get_post($student_id);
        return $student_post && $student_post->post_type === self::STUDENT_POST_TYPE;
    }

    public static function getCurrentStudentData($student_id)
    {
        if (!self::isValidStudentPost($student_id)) {
            return null;
        }

        $student_post = get_post($student_id);

        $student_data = array(
            'id' => $student_post->ID,
            'name' => $student_post->post_title,
            'username' => get_post_meta($student_post->ID, self::USERNAME_META_KEY, true),
            'post_date' => $student_post->post_date,
            'post_status' => $student_post->post_status,
        );

        $custom_fields = array('student_email', 'student_phone', 'student_of');
        foreach ($custom_fields as $field) {
            $value = get_post_meta($student_post->ID, $field, true);
            if (!empty($value)) {
                $student_data[$field] = $value;
            }
        }

        return $student_data;
    }

    public static function hasAccessToResource($student_id, $resource_id, $resource_type = 'school')
    {
        if ($resource_type === 'school') {
            $student_of = get_post_meta($student_id, 'student_of', true);
            return !empty($student_of) && intval($student_of) === intval($resource_id);
        }

        return true;
    }
}