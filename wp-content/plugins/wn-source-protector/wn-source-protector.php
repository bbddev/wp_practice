<?php
/**
 * Plugin Name: WN Source Protector
 * Description: Bảo vệ file nguồn bằng session từ StudentSessionManager
 * Version: 2.1
 * Author: WebNow
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
if (!defined('WNSP_SM_PATH')) {
    define('WNSP_SM_PATH', ABSPATH . 'wp-content/plugins/school-management/includes/utilities/session-manager.php');
}

// Load autoloader
require_once plugin_dir_path(__FILE__) . 'includes/Autoloader.php';
WNSP_Autoloader::load_all_classes();

/**
 * Main protection function - backward compatibility
 */
function wnsp_require_protect()
{
    $access_controller = WNSP_AccessController::get_instance();
    return $access_controller->require_protect();
}

/**
 * Check if user is logged in - backward compatibility
 */
function wnsp_is_logged_in()
{
    $session_manager = WNSP_SessionManager::get_instance();
    return $session_manager->is_logged_in();
}

/**
 * Backward compatibility functions
 */
function wnsp_check_login_status()
{
    return wnsp_is_logged_in();
}

function wnsp_check_group_access()
{
    $access_controller = WNSP_AccessController::get_instance();
    return $access_controller->check_group_access();
}

function wnsp_get_student_study_stats($student_id)
{
    $study_manager = WNSP_StudyCountManager::get_instance();
    return $study_manager->get_student_study_stats($student_id);
}

/**
 * Shortcode protect - backward compatibility
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
 * Admin check for session manager
 */
function wnsp_admin_check_sm()
{
    if (!current_user_can('manage_options'))
        return;

    if (!file_exists(WNSP_SM_PATH)) {
        echo '<div class="notice notice-warning"><p><strong>WN Source Protector:</strong> Không tìm thấy session-manager.php ở <code>' . esc_html(WNSP_SM_PATH) . '</code></p></div>';
        return;
    }

    // Check class StudentSessionManager
    $session_manager = WNSP_SessionManager::get_instance();
    $session_manager->ensure_session_manager_loaded();
    if (!class_exists('StudentSessionManager')) {
        echo '<div class="notice notice-error"><p><strong>WN Source Protector:</strong> Không tìm thấy class StudentSessionManager trong session-manager.php</p></div>';
    }
}
add_action('admin_notices', 'wnsp_admin_check_sm');
