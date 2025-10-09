<?php
/**
 * Plugin Name: WN Source Protector
 * Description: Bảo vệ file nguồn bằng session có sẵn từ School Management. Tự tận dụng sm_is_user_logged_in() / sm_do_login() nếu có.
 * Version: 1.2
 * Author: WebNow 1% - ChatGPT 99%
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Path tới session-manager (nếu khác thì sửa cho phù hợp)
 */
if (! defined('WNSP_SM_PATH')) {
    define('WNSP_SM_PATH', ABSPATH . 'wp-content/plugins/school-management/includes/utilities/session-manager.php');
}

/**
 * Load session-manager nếu có, và đảm bảo session started.
 */
function wnsp_ensure_session_manager_loaded() {
    // load file session-manager nếu có
    if (file_exists(WNSP_SM_PATH)) {
        require_once WNSP_SM_PATH;
    }

    // đảm bảo session được start
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
}

/**
 * Xử lý POST login (init hook).
 * Nếu sm_do_login tồn tại sẽ gọi nó để login thay vì gán session thủ công.
 * Nếu không có, fallback dùng wp_signon.
 * Lưu lỗi vào session để hiển thị sau redirect (không redirect về admin/trang chủ).
 */
function wnsp_handle_login_post() {
    // Chỉ xử lý khi form của chúng ta gửi
    if (empty($_POST['wnsp_login_submit'])) return;

    // CSRF check
    if (empty($_POST['wnsp_login_nonce']) || ! wp_verify_nonce($_POST['wnsp_login_nonce'], 'wnsp_login_action')) {
        // bảo mật: dừng và show lỗi
        wnsp_ensure_session_manager_loaded();
        $_SESSION['wnsp_last_error'] = 'Yêu cầu không hợp lệ (nonce).';
        wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
        exit;
    }

    $user = isset($_POST['wnsp_login_user']) ? trim($_POST['wnsp_login_user']) : '';
    $pass = isset($_POST['wnsp_login_pass']) ? $_POST['wnsp_login_pass'] : '';

    wnsp_ensure_session_manager_loaded();

    $error = '';

    // 1) Nếu có sm_do_login() của hệ thống School Management -> gọi nó (an toàn vì hàm gốc xử lý session)
    if (function_exists('sm_do_login')) {
        // Gọi sm_do_login; chấp nhận bool hoặc mảng result
        $res = @call_user_func('sm_do_login', $user, $pass);
        if ($res === true || (is_array($res) && ! empty($res['success']))) {
            // thành công -> reload lại trang hiện tại
            wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
            exit;
        } else {
            if (is_array($res) && ! empty($res['message'])) {
                $error = $res['message'];
            } else {
                $error = 'Đăng nhập thất bại.';
            }
        }
    } else {
        // 2) Fallback: dùng WP authentication (wp_signon)
        if (! function_exists('wp_signon')) {
            // đảm bảo load các hàm auth, trong trường hợp chưa đầy đủ
            require_once ABSPATH . 'wp-includes/pluggable.php';
        }

        $creds = array(
            'user_login'    => sanitize_user($user),
            'user_password' => $pass,
            'remember'      => true,
        );

        $signed = wp_signon($creds, false);

        if (is_wp_error($signed)) {
            // Lấy message chi tiết nếu có, nhưng không leak thông tin quá cụ thể
            $error = $signed->get_error_message();
            if (empty($error)) $error = 'Đăng nhập thất bại.';
        } else {
            // thành công: set cookie/user & redirect lại trang hiện tại
            wp_set_current_user($signed->ID);
            wp_set_auth_cookie($signed->ID);
            // Optionally set a session flag for plugin usage
            if (session_status() === PHP_SESSION_NONE) @session_start();
            $_SESSION['wnsp_logged_in'] = true;

            wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
            exit;
        }
    }

    // Nếu tới đây là lỗi -> lưu lỗi vào session và redirect back (để hiển thị message trên trang)
    if (! empty($error)) {
        if (session_status() === PHP_SESSION_NONE) @session_start();
        $_SESSION['wnsp_last_error'] = $error;
        wp_safe_redirect( wp_unslash( $_SERVER['REQUEST_URI'] ) );
        exit;
    }
}
add_action('init', 'wnsp_handle_login_post', 5);

/**
 * Hiển thị form login (nội bộ)
 */
function wnsp_render_login_form($message = '') {
    $action = esc_url( $_SERVER['REQUEST_URI'] );
    // đọc lỗi từ session nếu có
    if (session_status() === PHP_SESSION_NONE) @session_start();
    if (empty($message) && ! empty($_SESSION['wnsp_last_error'])) {
        $message = $_SESSION['wnsp_last_error'];
        unset($_SESSION['wnsp_last_error']);
    }
    ob_start();
    ?>
    <!doctype html>
    <html lang="vi">
    <head>
        <meta charset="utf-8">
        <title>Đăng nhập để xem nội dung</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <style>
            body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;margin:0;padding:0;display:flex;align-items:center;justify-content:center;height:100vh}
            .wnsp-box{width:360px;background:#fff;padding:20px;border-radius:8px;box-shadow:0 6px 24px rgba(0,0,0,0.08)}
            .wnsp-box h2{margin:0 0 12px;font-size:18px}
            .wnsp-box p.msg{color:#c00;background:#fff0f0;padding:8px;border-radius:4px;margin-bottom:12px}
            .wnsp-box input{width:100%;padding:10px;margin-bottom:10px;box-sizing:border-box;border:1px solid #ddd;border-radius:4px}
            .wnsp-box button{width:100%;padding:10px;background:#0073aa;color:#fff;border:0;border-radius:4px;cursor:pointer}
            .wnsp-box a.small{display:inline-block;margin-top:10px;font-size:13px;color:#555}
        </style>
    </head>
    <body>
        <div class="wnsp-box" role="main">
            <h2>Đăng nhập</h2>
            <?php if (!empty($message)): ?>
                <p class="msg"><?php echo esc_html($message); ?></p>
            <?php endif; ?>
            <form method="post" action="<?php echo $action; ?>">
                <?php wp_nonce_field('wnsp_login_action','wnsp_login_nonce'); ?>
                <input type="text" name="wnsp_login_user" placeholder="Tên đăng nhập" required autofocus>
                <input type="password" name="wnsp_login_pass" placeholder="Mật khẩu" required>
                <input type="hidden" name="wnsp_login_submit" value="1">
                <button type="submit">Đăng nhập</button>
            </form>
            <a class="small" href="<?php echo esc_url( apply_filters('wnsp_sm_login_url', site_url('/') ) ); ?>">Đăng nhập ở trang chính</a>
        </div>
    </body>
    </html>
    <?php
    echo ob_get_clean();
}

/**
 * Hàm chính: require_protect
 * Gọi ở đầu file tĩnh hoặc template để bảo vệ nội dung.
 */
function wnsp_require_protect() {
    // load session-manager + start session
    wnsp_ensure_session_manager_loaded();

    // Ưu tiên sử dụng helper sm_is_user_logged_in() nếu có
    $logged = false;
    if (function_exists('sm_is_user_logged_in')) {
        try {
            $logged = (bool) call_user_func('sm_is_user_logged_in');
        } catch (Throwable $e) {
            $logged = false;
        }
    } else {
        // fallback: kiểm tra session key chuẩn của plugin (sm_current_user)
        if (session_status() === PHP_SESSION_NONE) @session_start();
        if (! empty($_SESSION['sm_current_user'])) {
            $logged = true;
        }
    }

    if ($logged) {
        // đã login -> tiếp tục cho hiển thị nội dung (không echo gì)
        return true;
    }

    // nếu chưa login -> hiển thị form login
    // lấy lỗi nếu có từ session
    $msg = '';
    if (session_status() === PHP_SESSION_NONE) @session_start();
    if (! empty($_SESSION['wnsp_last_error'])) {
        $msg = $_SESSION['wnsp_last_error'];
        unset($_SESSION['wnsp_last_error']);
    }

    wnsp_render_login_form($msg);
    // dừng tiếp tục render file để không leak nội dung
    exit;
}

/**
 * Shortcode [wnsp_protect]...[/wnsp_protect]
 * Bọc nội dung cần bảo vệ trong bài/post.
 */
function wnsp_shortcode_protect($atts, $content = null) {
    // load session-manager
    wnsp_ensure_session_manager_loaded();

    // kiểm tra login như ở trên
    $logged = false;
    if (function_exists('sm_is_user_logged_in')) {
        try {
            $logged = (bool) call_user_func('sm_is_user_logged_in');
        } catch (Throwable $e) {
            $logged = false;
        }
    } else {
        if (session_status() === PHP_SESSION_NONE) @session_start();
        if (! empty($_SESSION['sm_current_user'])) $logged = true;
    }

    if ($logged) {
        return do_shortcode($content);
    }

    // nếu chưa login, hiển thị form login (nhưng không exit vì shortcode trong post)
    ob_start();
    // hiển thị lỗi nếu có
    $msg = '';
    if (session_status() === PHP_SESSION_NONE) @session_start();
    if (! empty($_SESSION['wnsp_last_error'])) {
        $msg = $_SESSION['wnsp_last_error'];
        unset($_SESSION['wnsp_last_error']);
    }
    wnsp_render_login_form($msg);
    return ob_get_clean();
}
add_shortcode('wnsp_protect', 'wnsp_shortcode_protect');

/**
 * Helper: kiểm tra và trả true/false (có thể dùng trong template)
 */
function wnsp_is_logged_in() {
    wnsp_ensure_session_manager_loaded();
    if (function_exists('sm_is_user_logged_in')) {
        return (bool) call_user_func('sm_is_user_logged_in');
    }
    if (session_status() === PHP_SESSION_NONE) @session_start();
    return ! empty($_SESSION['sm_current_user']);
}

/**
 * Admin notice nếu không tìm thấy session-manager (giúp debug)
 */
function wnsp_admin_check_sm() {
    if (! current_user_can('manage_options')) return;
    if (! file_exists(WNSP_SM_PATH)) {
        echo '<div class="notice notice-warning"><p><strong>WN Source Protector:</strong> Không tìm thấy session-manager ở <code>' . esc_html(WNSP_SM_PATH) . '</code>. Nếu đường dẫn khác, chỉnh hằng WNSP_SM_PATH trong plugin.</p></div>';
    }
}
add_action('admin_notices', 'wnsp_admin_check_sm');
