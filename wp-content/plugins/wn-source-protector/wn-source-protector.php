<?php
/**
 * Plugin Name: WN Source Protector
 * Description: Bảo vệ file nguồn bằng session từ StudentSessionManager
 * Version: 2.0
 * Author: WebNow
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Path tới session-manager
 */
if (!defined('WNSP_SM_PATH')) {
    define('WNSP_SM_PATH', ABSPATH . 'wp-content/plugins/school-management/includes/utilities/session-manager.php');
}

/**
 * Load StudentSessionManager class
 */
function wnsp_ensure_session_manager_loaded()
{
    if (file_exists(WNSP_SM_PATH)) {
        require_once WNSP_SM_PATH;
    }
}
/**
 * Kiểm tra login status bằng StudentSessionManager
 */
function wnsp_check_login_status()
{
    wnsp_ensure_session_manager_loaded();

    if (!class_exists('StudentSessionManager')) {
        return false;
    }

    $session_data = StudentSessionManager::checkSession();
    return $session_data['logged_in'];
}

/**
 * Hàm chính: require_protect
 * Gọi ở đầu file tĩnh hoặc template để bảo vệ nội dung.
 */
function wnsp_require_protect()
{
    if (wnsp_check_login_status()) {
        return true; // Đã login, cho phép truy cập
    }

    // Chưa login, chuyển hướng về trang chủ
    if (function_exists('home_url')) {
        wp_redirect(home_url());
    } else {
        // Fallback nếu không có WordPress functions
        header('Location: /');
    }
    exit;
}

/**
 * Shortcode [wnsp_protect]...[/wnsp_protect]
 * Bọc nội dung cần bảo vệ trong bài/post.
 */
function wnsp_shortcode_protect($atts, $content = null)
{
    if (wnsp_check_login_status()) {
        return do_shortcode($content);
    }

    // Chưa login, hiển thị thông báo
    return '<div class="wnsp-protected-content">
        <p>Bạn cần đăng nhập để xem nội dung này.</p>
        <p><a href="' . home_url() . '">Về trang chủ để đăng nhập</a></p>
    </div>';
}
add_shortcode('wnsp_protect', 'wnsp_shortcode_protect');

/**
 * Helper: kiểm tra và trả true/false (có thể dùng trong template)
 */
function wnsp_is_logged_in()
{
    return wnsp_check_login_status();
}

/**
 * Admin notice nếu không tìm thấy session-manager (giúp debug)
 */
function wnsp_admin_check_sm()
{
    if (!current_user_can('manage_options'))
        return;

    if (!file_exists(WNSP_SM_PATH)) {
        echo '<div class="notice notice-warning"><p><strong>WN Source Protector:</strong> Không tìm thấy session-manager.php ở <code>' . esc_html(WNSP_SM_PATH) . '</code></p></div>';
        return;
    }

    // Kiểm tra class StudentSessionManager
    wnsp_ensure_session_manager_loaded();
    if (!class_exists('StudentSessionManager')) {
        echo '<div class="notice notice-error"><p><strong>WN Source Protector:</strong> Không tìm thấy class StudentSessionManager trong session-manager.php</p></div>';
    }
}
add_action('admin_notices', 'wnsp_admin_check_sm');
