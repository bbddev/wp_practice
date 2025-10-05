<?php
/**
 * Plugin Name: WN Source Protector
 * Description: Bảo vệ file PHP/HTML tĩnh bằng username & password, lưu trong custom field của bài viết/trang.
 * Version: 1.1
 * Author: WN Digital Solutions
 */

if (!defined('ABSPATH')) exit;

if (!function_exists('wnsp_check_login')) {
    function wnsp_normalize_url($url) {
        // Bỏ http:// hoặc https://
        $url = preg_replace('#^https?://#i', '', $url);

        // Bỏ index.php ở cuối (nếu có)
        $url = preg_replace('#/index\.php$#i', '', $url);

        // Bỏ slash cuối (trừ trường hợp chỉ "/")
        $url = rtrim($url, '/');

        return $url;
    }

    function wnsp_check_login() {
        if (!session_id()) {
            session_start();
        }

        // URL hiện tại
        $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
        $current_url .= "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        // Chuẩn hóa URL hiện tại
        $normalized_current = wnsp_normalize_url($current_url);

        global $wpdb;

        // Lấy tất cả post có meta "Link khi click"
        $results = $wpdb->get_results(
            "SELECT post_id, meta_value 
             FROM $wpdb->postmeta 
             WHERE meta_key = 'Link khi click'"
        );

        $post_id = null;

        if ($results) {
            foreach ($results as $row) {
                $meta_link = wnsp_normalize_url($row->meta_value);
                if ($meta_link === $normalized_current) {
                    $post_id = $row->post_id;
                    break;
                }
            }
        }

        if ($post_id) {
            $username = get_post_meta($post_id, 'Username', true);
            $password = get_post_meta($post_id, 'lesson_password', true);

            // Nếu chưa login thì yêu cầu login
            if (empty($_SESSION['wnsp_access'][$post_id])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $input_user = sanitize_text_field($_POST['wnsp_user'] ?? '');
                    $input_pass = sanitize_text_field($_POST['wnsp_pass'] ?? '');

                    if ($input_user === $username && $input_pass === $password) {
                        $_SESSION['wnsp_access'][$post_id] = true;
                        return; // ✅ login thành công
                    } else {
                        echo "<p style='color:red'>Sai username hoặc mật khẩu</p>";
                    }
                }

                // Form login
                echo '<form method="POST" style="max-width:300px;margin:50px auto;text-align:center">
                        <input type="text" name="wnsp_user" placeholder="Username" required style="display:block;width:100%;margin:5px 0;padding:8px;">
                        <input type="password" name="wnsp_pass" placeholder="Password" required style="display:block;width:100%;margin:5px 0;padding:8px;">
                        <button type="submit" style="padding:8px 16px">Đăng nhập</button>
                      </form>';
                exit;
            }
        }
    }
}
