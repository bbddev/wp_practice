<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register REST API routes
 */
function register_school_management_routes()
{
    register_rest_route('school-management/v1', '/schools', array(
        'methods' => 'GET',
        'callback' => 'get_schools',
    ));

    register_rest_route('school-management/v1', '/classes/(?P<school_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_classes_by_school',
        'args' => array(
            'school_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/entities/(?P<class_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_entities_by_class',
        'args' => array(
            'class_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/check-class-password/(?P<class_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'check_class_password',
        'args' => array(
            'class_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/check-lesson-password/(?P<entity_id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'check_lesson_password',
        'args' => array(
            'entity_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/validate-class-password', array(
        'methods' => 'POST',
        'callback' => 'validate_class_password',
        'args' => array(
            'class_id' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
            'password' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));

    register_rest_route('school-management/v1', '/validate-lesson-password', array(
        'methods' => 'POST',
        'callback' => 'validate_lesson_password',
        'args' => array(
            'entity_id' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
            'password' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));

    // Check student session
    register_rest_route('school-management/v1', '/check-student-session', array(
        'methods' => 'GET',
        'callback' => 'check_student_session',
    ));

    // Student login
    register_rest_route('school-management/v1', '/student-login', array(
        'methods' => 'POST',
        'callback' => 'student_login',
        'args' => array(
            'username' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'password' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
}

/**
 * Get schools data for REST API
 */
function get_schools()
{
    $school_list = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));
    return $school_list;
}

/**
 * Get classes by school for REST API
 */
function get_classes_by_school($request)
{
    $school_id = $request['school_id'];
    $school_post = get_post($school_id);

    if (!$school_post || $school_post->post_type !== 'school') {
        return new WP_Error('invalid_school', 'Invalid school ID', array('status' => 404));
    }

    $school_name = $school_post->post_title;

    $classes = get_posts(array(
        'post_type' => 'class',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'Thuộc trường',
                'value' => $school_name,
                'compare' => '='
            )
        )
    ));

    return $classes;
}

/**
 * Get entities by class for REST API
 */
function get_entities_by_class($request)
{
    $class_id = $request['class_id'];
    $class_post = get_post($class_id);

    if (!$class_post || $class_post->post_type !== 'class') {
        return new WP_Error('invalid_class', 'Invalid class ID', array('status' => 404));
    }

    $class_name = $class_post->post_title;

    $entities = get_posts(array(
        'post_type' => 'entity',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'Thuộc lớp',
                'value' => $class_name,
                'compare' => '='
            )
        )
    ));

    // Enhance entities with meta data
    $enhanced_entities = array();
    foreach ($entities as $entity) {
        $enhanced_entity = array(
            'id' => $entity->ID,
            'title' => $entity->post_title,
            'content' => $entity->post_content,
            'link' => get_post_meta($entity->ID, 'Link khi click', true),
            'image' => get_post_meta($entity->ID, 'Hình', true)
        );
        $enhanced_entities[] = $enhanced_entity;
    }

    return $enhanced_entities;
}

/**
 * Check if class has password
 */
function check_class_password($request)
{
    $class_id = $request['class_id'];
    $class_post = get_post($class_id);

    if (!$class_post || $class_post->post_type !== 'class') {
        return new WP_Error('invalid_class', 'Invalid class ID', array('status' => 404));
    }

    $class_password = get_post_meta($class_id, 'class_password', true);

    return array(
        'has_password' => !empty($class_password)
    );
}

/**
 * Check if lesson has password
 */
function check_lesson_password($request)
{
    $entity_id = $request['entity_id'];
    $entity_post = get_post($entity_id);

    if (!$entity_post || $entity_post->post_type !== 'entity') {
        return new WP_Error('invalid_entity', 'Invalid entity ID', array('status' => 404));
    }

    $lesson_password = get_post_meta($entity_id, 'lesson_password', true);

    return array(
        'has_password' => !empty($lesson_password)
    );
}

/**
 * Validate class password
 */
function validate_class_password($request)
{
    $class_id = $request['class_id'];
    $password = $request['password'];

    $class_post = get_post($class_id);

    if (!$class_post || $class_post->post_type !== 'class') {
        return new WP_Error('invalid_class', 'Invalid class ID', array('status' => 404));
    }

    $stored_password = get_post_meta($class_id, 'class_password', true);

    return array(
        'valid' => ($password === $stored_password)
    );
}

/**
 * Validate lesson password
 */
function validate_lesson_password($request)
{
    $entity_id = $request['entity_id'];
    $password = $request['password'];
    $username = $request['username'];

    $entity_post = get_post($entity_id);

    if (!$entity_post || $entity_post->post_type !== 'entity') {
        return new WP_Error('invalid_entity', 'Invalid entity ID', array('status' => 404));
    }

    $stored_password = get_post_meta($entity_id, 'lesson_password', true);
    $stored_username = get_post_meta($entity_id, 'Username', true);

    return array(
        'valid' => ($password === $stored_password && $username === $stored_username)
    );
}


/**
 * Check if a student is logged in via PHP session
 */
function check_student_session()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $student_id = isset($_SESSION['school_management_student_id']) ? intval($_SESSION['school_management_student_id']) : 0;

    return array(
        'logged_in' => $student_id > 0,
        'student_id' => $student_id,
    );
}


/**
 * Handle student login. Look up student post by meta username and password.
 */
function student_login($request)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $params = $request->get_params();
    $username = isset($params['username']) ? sanitize_text_field($params['username']) : '';
    $password = isset($params['password']) ? sanitize_text_field($params['password']) : '';

    if (empty($username) || empty($password)) {
        return new WP_Error('invalid_input', 'Username and password required', array('status' => 400));
    }

    // Query students with matching username
    $args = array(
        'post_type' => 'student',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'student_username',
                'value' => $username,
                'compare' => '='
            )
        )
    );

    $students = get_posts($args);

    if (!$students || count($students) === 0) {
        return array('success' => false, 'message' => 'Invalid credentials');
    }

    // Find the first student with matching password
    foreach ($students as $student) {
        $stored_password = get_post_meta($student->ID, 'student_password', true);
        if ($password === $stored_password) {
            // success - set session
            $_SESSION['school_management_student_id'] = $student->ID;
            return array('success' => true, 'student_id' => $student->ID);
        }
    }

    return array('success' => false, 'message' => 'Invalid credentials');
}