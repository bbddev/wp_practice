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
 * Detect required student group based on the current request path.
 * Returns a string like 'Khối 6', 'Khối 7', 'Khối 8' or empty when no restriction.
 */
function wnsp_get_required_group_from_request()
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
 * Check whether the current logged-in student belongs to the required group for this request.
 * Returns true when allowed, false when access should be denied.
 */
function wnsp_check_group_access()
{
    $required = wnsp_get_required_group_from_request();
    if (empty($required)) {
        return true; // no restriction for this path
    }

    // Ensure StudentSessionManager is available
    wnsp_ensure_session_manager_loaded();
    if (!class_exists('StudentSessionManager')) {
        // If we can't check, be conservative and deny access
        return false;
    }

    $session = StudentSessionManager::checkSession();
    $student_of = isset($session['student_of']) ? trim($session['student_of']) : '';

    // Direct string comparison (exact match)
    return $student_of === $required;
}

/**
 * Sanitize text field nếu function WordPress không có
 */
function wnsp_sanitize_text_field($str)
{
    if (function_exists('sanitize_text_field')) {
        return sanitize_text_field($str);
    }
    return trim(strip_tags($str));
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
 * Log debug information for view counting (optional)
 */
function wnsp_log_view_count($entity_id, $action, $count = null)
{
    // Uncomment for debugging
    // error_log("WNSP View Count - Entity ID: $entity_id, Action: $action" . ($count !== null ? ", Count: $count" : ""));
}

/**
 * Increment view count for entity based on URL parameter
 * Supports URL patterns like: source/khoi6/k6cd1t1/?33403
 * Only counts once per session to prevent duplicate counting
 */
function wnsp_increment_entity_view_count()
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
    
    // If we have a valid entity ID, increment the view count
    if ($entity_id > 0) {
        $should_count = true;
        
        // Only use session tracking for web requests (not CLI)
        if (php_sapi_name() !== 'cli' && isset($_SERVER['HTTP_HOST'])) {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            
            // Check if we've already counted this entity in this session
            $session_key = 'wnsp_viewed_entities';
            if (!isset($_SESSION[$session_key])) {
                $_SESSION[$session_key] = array();
            }
            
            // Only count if not already viewed in this session
            if (in_array($entity_id, $_SESSION[$session_key])) {
                $should_count = false;
            }
        }
        
        if ($should_count) {
            // Check if WordPress functions are available
            if (function_exists('get_post') && function_exists('get_post_meta') && function_exists('update_post_meta')) {
                // Verify this is actually an entity post
                $post = get_post($entity_id);
                if ($post && $post->post_type === 'entity') {
                    // Get current count
                    $current_count = get_post_meta($entity_id, 'countuser', true);
                    $current_count = is_numeric($current_count) ? intval($current_count) : 0;
                    
                    // Increment and update
                    $new_count = $current_count + 1;
                    update_post_meta($entity_id, 'countuser', $new_count);
                    
                    // Log the increment
                    wnsp_log_view_count($entity_id, 'incremented', $new_count);
                    
                    // Mark as viewed in this session (only for web requests)
                    if (php_sapi_name() !== 'cli' && isset($_SERVER['HTTP_HOST']) && isset($_SESSION[$session_key])) {
                        $_SESSION[$session_key][] = $entity_id;
                    }
                } else {
                    wnsp_log_view_count($entity_id, 'invalid_post_type');
                }
            } else {
                wnsp_log_view_count($entity_id, 'wordpress_functions_not_available');
            }
        } else {
            wnsp_log_view_count($entity_id, 'already_viewed_in_session');
        }
    }
}

/**
 * Hàm chính: require_protect
 * Gọi ở đầu file tĩnh hoặc template để bảo vệ nội dung.
 */
function wnsp_require_protect()
{
    // Xử lý login request nếu có
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'wnsp_login') {
        wnsp_handle_ajax_login();
        exit;
    }

    // First ensure logged in
    if (!wnsp_check_login_status()) {
        // Not logged in, render login popup
        wnsp_render_login_page();
        exit;
    }

    // If logged in, also check group/folder access (Khối 6/7/8 -> source/khoi6|7|8)
    if (!wnsp_check_group_access()) {
        // Deny access with 403
        status_header(403);
        // Simple 403 message - keep minimal HTML to avoid dependencies
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>403 Forbidden</title></head><body style="font-family:Arial,sans-serif;margin:40px;">';
        echo '<h1>403 Forbidden</h1>';
        echo '<p>Bạn không có quyền truy cập vào bài học này.</p>';
        echo '<p><a href="' . esc_url(function_exists('home_url') ? home_url() : '/') . '">Về trang chủ</a></p>';
        echo '</body></html>';
        exit;
    }

    // Logged in and group check passed
    // Increment view count for entity if ID is provided in URL
    wnsp_increment_entity_view_count();
    
    return true;
}

/**
 * Xử lý AJAX login từ popup
 */
function wnsp_handle_ajax_login()
{
    header('Content-Type: application/json');

    $username = isset($_POST['username']) ? wnsp_sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng nhập đầy đủ thông tin'
        ]);
        return;
    }

    wnsp_ensure_session_manager_loaded();

    if (!class_exists('StudentSessionManager')) {
        echo json_encode([
            'success' => false,
            'message' => 'Hệ thống đăng nhập không khả dụng'
        ]);
        return;
    }

    // Thực hiện đăng nhập
    $login_result = StudentSessionManager::login($username, $password);

    if ($login_result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Đăng nhập thành công'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $login_result['message'] ?? 'Tên đăng nhập hoặc mật khẩu không đúng'
        ]);
    }
}

/**
 * Render trang login popup
 */
function wnsp_render_login_page()
{
    // Lấy thông tin lỗi từ session nếu có
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    $error_message = '';
    if (!empty($_SESSION['wnsp_login_error'])) {
        $error_message = $_SESSION['wnsp_login_error'];
        unset($_SESSION['wnsp_login_error']);
    }

    $home_url = function_exists('home_url') ? home_url() : '/';

    ?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            <?php echo wnsp_get_login_styles(); ?>
        </style>
    </head>

    <body>
        <div class="wnsp-login-overlay">
            <div class="wnsp-login-container">
                <div class="wnsp-login-header">
                    <h2>Login</h2>
                </div>

                <form id="wnspLoginForm" class="wnsp-login-form" method="post">
                    <?php if ($error_message): ?>
                        <div class="wnsp-error-message"><?php echo esc_html($error_message); ?></div>
                    <?php endif; ?>

                    <div class="wnsp-form-group">
                        <label for="wnsp_username">Username:</label>
                        <input type="text" id="wnsp_username" name="username" placeholder="Nhập username..." required>
                        <div id="wnsp_username_error" class="wnsp-field-error"></div>
                    </div>

                    <div class="wnsp-form-group">
                        <label for="wnsp_password">Password:</label>
                        <div class="wnsp-password-group">
                            <input type="password" id="wnsp_password" name="password" placeholder="Nhập mật khẩu..."
                                required>
                            <button type="button" id="wnsp_toggle_password" class="wnsp-password-toggle">
                                <i class="fas fa-eye" id="wnsp_password_icon"></i>
                            </button>
                        </div>
                        <div id="wnsp_password_error" class="wnsp-field-error"></div>
                    </div>

                    <div class="wnsp-form-actions">
                        <button type="submit" id="wnsp_login_btn" class="wnsp-btn wnsp-btn-primary">Login</button>
                        <a href="<?php echo esc_url($home_url); ?>" class="wnsp-btn wnsp-btn-secondary">Home</a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            <?php echo wnsp_get_login_script(); ?>
        </script>
    </body>

    </html>
    <?php
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
 * CSS styles cho login form
 */
function wnsp_get_login_styles()
{
    return '
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f0f0; }
        
        .wnsp-login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .wnsp-login-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .wnsp-login-header h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 18px;
        }
        
        .wnsp-form-group {
            margin-bottom: 20px;
        }
        
        .wnsp-form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        .wnsp-form-group input[type="text"],
        .wnsp-form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .wnsp-form-group input:focus {
            outline: none;
            border-color: #007cba;
            box-shadow: 0 0 5px rgba(0, 124, 186, 0.2);
        }
        
        .wnsp-password-group {
            position: relative;
        }
        
        .wnsp-password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 5px;
        }
        
        .wnsp-password-toggle:hover {
            color: #007cba;
        }
        
        .wnsp-error-message {
            background: #ffebe8;
            color: #d63638;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #d63638;
        }
        
        .wnsp-field-error {
            color: #d63638;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        .wnsp-form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        
        .wnsp-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s;
            display: inline-block;
        }
        
        .wnsp-btn-primary {
            background: #007cba;
            color: white;
        }
        
        .wnsp-btn-primary:hover {
            background: #005a87;
        }
        
        .wnsp-btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .wnsp-btn-secondary {
            background: #f7f7f7;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .wnsp-btn-secondary:hover {
            background: #e7e7e7;
            text-decoration: none;
        }
        
        @media (max-width: 480px) {
            .wnsp-login-container {
                padding: 20px;
                margin: 20px;
            }
            
            .wnsp-form-actions {
                flex-direction: column;
            }
        }
    ';
}

/**
 * JavaScript cho login form
 */
function wnsp_get_login_script()
{
    return '
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("wnspLoginForm");
            const toggleBtn = document.getElementById("wnsp_toggle_password");
            const passwordInput = document.getElementById("wnsp_password");
            const passwordIcon = document.getElementById("wnsp_password_icon");
            const loginBtn = document.getElementById("wnsp_login_btn");
            
            // Toggle password visibility
            if (toggleBtn) {
                toggleBtn.addEventListener("click", function() {
                    if (passwordInput.type === "password") {
                        passwordInput.type = "text";
                        passwordIcon.className = "fas fa-eye-slash";
                    } else {
                        passwordInput.type = "password";
                        passwordIcon.className = "fas fa-eye";
                    }
                });
            }
            
            // Form submission
            if (form) {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();
                    
                    const username = document.getElementById("wnsp_username").value.trim();
                    const password = document.getElementById("wnsp_password").value;
                    
                    // Clear previous errors
                    hideError("wnsp_username");
                    hideError("wnsp_password");
                    
                    // Validation
                    if (!username) {
                        showError("wnsp_username", "Vui lòng nhập username");
                        return;
                    }
                    if (!password) {
                        showError("wnsp_password", "Vui lòng nhập mật khẩu");
                        return;
                    }
                    
                    // Disable button
                    loginBtn.disabled = true;
                    loginBtn.textContent = "Đang đăng nhập...";
                    
                    // Create form data
                    const formData = new FormData();
                    formData.append("action", "wnsp_login");
                    formData.append("username", username);
                    formData.append("password", password);
                    
                    // Submit via AJAX
                    fetch(window.location.href, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page on success
                            window.location.reload();
                        } else {
                            showError("wnsp_password", data.message || "Đăng nhập thất bại");
                        }
                    })
                    .catch(error => {
                        console.error("Login error:", error);
                        showError("wnsp_password", "Có lỗi xảy ra. Vui lòng thử lại");
                    })
                    .finally(() => {
                        loginBtn.disabled = false;
                        loginBtn.textContent = "Đăng nhập";
                    });
                });
            }
            
            function showError(fieldId, message) {
                const errorEl = document.getElementById(fieldId + "_error");
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.style.display = "block";
                }
            }
            
            function hideError(fieldId) {
                const errorEl = document.getElementById(fieldId + "_error");
                if (errorEl) {
                    errorEl.style.display = "none";
                }
            }
        });
    ';
}

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
