<?php

if (!defined('ABSPATH')) {
    exit;
}

class WNSP_AccessController
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
     * Get required group from current request
     */
    public function get_required_group_from_request()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $script = isset($_SERVER['SCRIPT_FILENAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']) : '';

        $hay = strtolower($uri . ' ' . $script);

        if (strpos($hay, '/source/khoi6') !== false) {
            return 'Khối 6';
        }
        if (strpos($hay, '/source/khoi7') !== false) {
            return 'Khối 7';
        }
        if (strpos($hay, '/source/khoi8') !== false) {
            return 'Khối 8';
        }

        return '';
    }

    /**
     * Check if current user has access to required group
     */
    public function check_group_access()
    {
        $required = $this->get_required_group_from_request();
        if (empty($required)) {
            return true; // no restriction for this path
        }

        $session_data = $this->session_manager->get_session_data();
        if (!$session_data['logged_in']) {
            return false;
        }

        $student_of = isset($session_data['student_of']) ? trim($session_data['student_of']) : '';

        // Direct string comparison (exact match)
        return $student_of === $required;
    }

    /**
     * Main protection function
     */
    public function require_protect()
    {
        // Handle login request if any
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'wnsp_login') {
            $this->handle_ajax_login();
            exit;
        }

        // First ensure logged in
        if (!$this->session_manager->is_logged_in()) {
            // Not logged in, render login popup
            $login_renderer = WNSP_LoginRenderer::get_instance();
            $login_renderer->render_login_page();
            exit;
        }

        // If logged in, also check group/folder access
        if (!$this->check_group_access()) {
            $this->render_403_page();
            exit;
        }

        // Logged in and group check passed
        // Increment view count for entity if ID is provided in URL
        $study_count_manager = WNSP_StudyCountManager::get_instance();
        $study_count_manager->increment_entity_view_count();

        return true;
    }

    /**
     * Handle AJAX login request
     */
    private function handle_ajax_login()
    {
        header('Content-Type: application/json');

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $result = $this->session_manager->handle_login($username, $password);
        echo json_encode($result);
    }

    /**
     * Render 403 forbidden page
     */
    private function render_403_page()
    {
        if (function_exists('status_header')) {
            status_header(403);
        } else {
            http_response_code(403);
        }

        $home_url = function_exists('home_url') ? home_url() : '/';
        $safe_home_url = function_exists('esc_url') ? esc_url($home_url) : htmlspecialchars($home_url);

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>403 Forbidden</title></head><body style="font-family:Arial,sans-serif;margin:40px;">';
        echo '<h1>403 Forbidden</h1>';
        echo '<p>Bạn không có quyền truy cập vào bài học này.</p>';
        echo '<p><a href="' . $safe_home_url . '">Về trang chủ</a></p>';
        echo '</body></html>';
    }
}