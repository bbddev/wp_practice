<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php
        $login_renderer = WNSP_LoginRenderer::get_instance();
        echo $login_renderer->get_login_styles();
        ?>
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
        <?php
        echo $login_renderer->get_login_script();
        ?>
    </script>
</body>

</html>