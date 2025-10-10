<?php

if (!defined('ABSPATH')) {
    exit;
}

class WNSP_LoginRenderer
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render login page
     */
    public function render_login_page()
    {
        // Start session for error messages
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $error_message = '';
        if (!empty($_SESSION['wnsp_login_error'])) {
            $error_message = $_SESSION['wnsp_login_error'];
            unset($_SESSION['wnsp_login_error']);
        }

        $home_url = function_exists('home_url') ? home_url() : '/';

        $template_path = dirname(dirname(__FILE__)) . '/templates/login-page.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            $this->render_inline_login_page($error_message, $home_url);
        }
    }

    /**
     * Render login page inline (fallback)
     */
    private function render_inline_login_page($error_message, $home_url)
    {
        ?>
        <!DOCTYPE html>
        <html lang="vi">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <style>
                <?php echo $this->get_login_styles(); ?>
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
                            <div class="wnsp-error-message">
                                <?php echo function_exists('esc_html') ? esc_html($error_message) : htmlspecialchars($error_message); ?>
                            </div>
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
                            <a href="<?php echo function_exists('esc_url') ? esc_url($home_url) : htmlspecialchars($home_url); ?>"
                                class="wnsp-btn wnsp-btn-secondary">Home</a>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                <?php echo $this->get_login_script(); ?>
            </script>
        </body>

        </html>
        <?php
    }

    /**
     * Get login styles
     */
    public function get_login_styles()
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
     * Get login JavaScript
     */
    public function get_login_script()
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
}