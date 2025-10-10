<?php

if (!defined('ABSPATH')) {
    exit;
}

class WNSP_StudyCountManager
{
    private static $instance = null;
    private $session_manager;

    public function __construct()
    {
        $this->session_manager = WNSP_SessionManager::get_instance();
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Update student study counts
     */
    public function update_student_study_counts($entity_id, $is_new_lesson = false)
    {
        $session_data = $this->session_manager->get_session_data();

        if (!$session_data['logged_in'] || empty($session_data['student_id'])) {
            return false;
        }

        $student_id = intval($session_data['student_id']);

        // Check if WordPress functions are available
        if (!function_exists('get_post') || !function_exists('get_post_meta') || !function_exists('update_post_meta')) {
            return false;
        }

        // Verify student exists and is of correct post type
        $student_post = get_post($student_id);
        if (!$student_post || $student_post->post_type !== 'student') {
            return false;
        }

        // Always increment study_count (every access)
        $current_study_count = get_post_meta($student_id, 'study_count', true);
        $current_study_count = is_numeric($current_study_count) ? intval($current_study_count) : 0;
        $new_study_count = $current_study_count + 1;
        update_post_meta($student_id, 'study_count', $new_study_count);

        // Only increment study_lesson_count for new lessons
        if ($is_new_lesson) {
            $current_lesson_count = get_post_meta($student_id, 'study_lesson_count', true);
            $current_lesson_count = is_numeric($current_lesson_count) ? intval($current_lesson_count) : 0;
            $new_lesson_count = $current_lesson_count + 1;
            update_post_meta($student_id, 'study_lesson_count', $new_lesson_count);
        }

        return true;
    }

    /**
     * Increment entity view count
     */
    public function increment_entity_view_count()
    {
        $entity_id = $this->get_entity_id_from_request();

        if ($entity_id > 0) {
            $should_count = true;
            $is_new_lesson = false;

            // Only use session tracking for web requests (not CLI)
            if (php_sapi_name() !== 'cli' && isset($_SERVER['HTTP_HOST'])) {
                $this->start_session_if_needed();

                $session_key = 'wnsp_viewed_entities';
                if (!isset($_SESSION[$session_key])) {
                    $_SESSION[$session_key] = array();
                }

                // Check if this is a new lesson
                if (!in_array($entity_id, $_SESSION[$session_key])) {
                    $is_new_lesson = true;
                }

                // Only count if not already viewed in this session
                if (in_array($entity_id, $_SESSION[$session_key])) {
                    $should_count = false;
                }
            }

            if ($should_count) {
                $this->increment_entity_count($entity_id);

                // Mark as viewed in this session
                if (php_sapi_name() !== 'cli' && isset($_SERVER['HTTP_HOST']) && isset($_SESSION[$session_key])) {
                    $_SESSION[$session_key][] = $entity_id;
                }
            }

            // Update student study counts
            $this->update_student_study_counts($entity_id, $is_new_lesson);
        }
    }

    /**
     * Get entity ID from request
     */
    private function get_entity_id_from_request()
    {
        $entity_id = 0;

        // Check for entity ID in query string (pattern: /?33403)
        $query_string = isset($_SERVER['QUERY_STRING']) ? trim($_SERVER['QUERY_STRING']) : '';
        if (!empty($query_string) && is_numeric($query_string)) {
            $entity_id = intval($query_string);
        }

        // Also check for 'id' parameter
        if ($entity_id === 0 && isset($_GET['id'])) {
            $entity_id = intval($_GET['id']);
        }

        // Also check for 'entity_id' parameter
        if ($entity_id === 0 && isset($_GET['entity_id'])) {
            $entity_id = intval($_GET['entity_id']);
        }

        return $entity_id;
    }

    /**
     * Increment entity count
     */
    private function increment_entity_count($entity_id)
    {
        // Check if WordPress functions are available
        if (!function_exists('get_post') || !function_exists('get_post_meta') || !function_exists('update_post_meta')) {
            return false;
        }

        // Verify this is actually an entity post
        $post = get_post($entity_id);
        if ($post && $post->post_type === 'entity') {
            // Get current count
            $current_count = get_post_meta($entity_id, 'countuser', true);
            $current_count = is_numeric($current_count) ? intval($current_count) : 0;

            // Increment and update
            $new_count = $current_count + 1;
            update_post_meta($entity_id, 'countuser', $new_count);

            return true;
        }

        return false;
    }

    /**
     * Start session if needed
     */
    private function start_session_if_needed()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }

    /**
     * Get student study stats - for debugging/testing
     */
    public function get_student_study_stats($student_id)
    {
        if (!function_exists('get_post_meta') || !function_exists('get_post')) {
            return array('error' => 'WordPress functions not available');
        }

        $student_post = get_post($student_id);
        if (!$student_post) {
            return array('error' => 'Student post not found', 'student_id' => $student_id);
        }

        if ($student_post->post_type !== 'student') {
            return array('error' => 'Post is not student type', 'student_id' => $student_id, 'post_type' => $student_post->post_type);
        }

        $study_count = get_post_meta($student_id, 'study_count', true);
        $study_lesson_count = get_post_meta($student_id, 'study_lesson_count', true);

        return array(
            'student_id' => $student_id,
            'post_title' => $student_post->post_title,
            'post_status' => $student_post->post_status,
            'study_count' => is_numeric($study_count) ? intval($study_count) : 0,
            'study_lesson_count' => is_numeric($study_lesson_count) ? intval($study_lesson_count) : 0
        );
    }
}