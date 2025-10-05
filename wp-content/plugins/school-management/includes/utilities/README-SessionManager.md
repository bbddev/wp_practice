# Student Session Manager

## Mô tả

`StudentSessionManager` là một class PHP để quản lý session cho học sinh trong hệ thống WordPress. Class này có thể được tái sử dụng cho các project khác bằng cách chỉnh sửa các constants và meta keys.

## Tính năng

- ✅ Quản lý session PHP cho student login
- ✅ Validate username/password với WordPress posts
- ✅ Kiểm tra trạng thái đăng nhập
- ✅ Lấy thông tin student hiện tại
- ✅ Kiểm tra quyền truy cập resource
- ✅ Session statistics và debugging
- ✅ Tự động xóa session khi student post không tồn tại

## Cài đặt

1. Copy file `session-manager.php` vào project của bạn
2. Include file trong code:

```php
require_once 'path/to/session-manager.php';
```

## Cấu hình

Chỉnh sửa các constants trong class để phù hợp với project:

```php
class StudentSessionManager
{
    // Key để lưu student ID trong $_SESSION
    const SESSION_KEY = 'school_management_student_id';

    // Post type của student trong WordPress
    const STUDENT_POST_TYPE = 'student';

    // Meta key cho username
    const USERNAME_META_KEY = 'student_username';

    // Meta key cho password
    const PASSWORD_META_KEY = 'student_password';
}
```

## Sử dụng

### 1. Kiểm tra session hiện tại

```php
$session = StudentSessionManager::checkSession();

if ($session['logged_in']) {
    echo "Student đã đăng nhập: " . $session['student_name'];
    echo "Student ID: " . $session['student_id'];
} else {
    echo "Chưa đăng nhập";
}
```

### 2. Xử lý đăng nhập

```php
$result = StudentSessionManager::login($username, $password);

if ($result['success']) {
    echo "Đăng nhập thành công!";
    echo "Student ID: " . $result['student_id'];
    echo "Student Name: " . $result['student_name'];
} else {
    echo "Lỗi: " . $result['message'];
    echo "Error Code: " . $result['error_code'];
}
```

### 3. Đăng xuất

```php
$result = StudentSessionManager::logout();

if ($result['success']) {
    echo "Đã đăng xuất thành công";
}
```

### 4. Lấy thông tin student hiện tại

```php
$student = StudentSessionManager::getCurrentStudent();

if ($student) {
    echo "ID: " . $student['id'];
    echo "Tên: " . $student['name'];
    echo "Username: " . $student['username'];

    // Các meta fields khác (nếu có)
    if (isset($student['student_email'])) {
        echo "Email: " . $student['student_email'];
    }
} else {
    echo "Không có student nào đăng nhập";
}
```

### 5. Kiểm tra quyền truy cập

```php
$hasAccess = StudentSessionManager::hasAccessToResource($class_id, 'class');

if ($hasAccess) {
    echo "Student có quyền truy cập class này";
} else {
    echo "Student không có quyền truy cập";
}
```

### 6. Yêu cầu đăng nhập (với redirect)

```php
if (!StudentSessionManager::requireLogin('/login-page')) {
    // Code này sẽ không chạy nếu chưa login
    // User sẽ được redirect về /login-page
}
```

### 7. Session debugging

```php
$stats = StudentSessionManager::getSessionStats();
print_r($stats);
```

## Sử dụng với WordPress REST API

### 1. Trong routes.php

```php
// Include SessionManager
require_once plugin_dir_path(__FILE__) . '../utilities/session-manager.php';

// Check session endpoint
function check_student_session() {
    return StudentSessionManager::checkSession();
}

// Login endpoint
function student_login($request) {
    $params = $request->get_params();
    $username = isset($params['username']) ? $params['username'] : '';
    $password = isset($params['password']) ? $params['password'] : '';

    return StudentSessionManager::login($username, $password);
}

// Logout endpoint
function student_logout() {
    return StudentSessionManager::logout();
}
```

### 2. Đăng ký REST routes

```php
register_rest_route('your-plugin/v1', '/check-student-session', array(
    'methods' => 'GET',
    'callback' => 'check_student_session',
));

register_rest_route('your-plugin/v1', '/student-login', array(
    'methods' => 'POST',
    'callback' => 'student_login',
    'args' => array(
        'username' => array('required' => true),
        'password' => array('required' => true),
    ),
));

register_rest_route('your-plugin/v1', '/student-logout', array(
    'methods' => 'POST',
    'callback' => 'student_logout',
));
```

## Tùy chỉnh cho project khác

### 1. Thay đổi cấu trúc database

Nếu project không dùng WordPress posts, bạn có thể override các methods:

```php
class CustomSessionManager extends StudentSessionManager
{
    public static function login($username, $password)
    {
        // Custom login logic với database khác
        // Ví dụ: MySQL, PostgreSQL, MongoDB, etc.

        $user = YourDatabase::findUserByUsername($username);

        if ($user && password_verify($password, $user->password)) {
            self::ensureSessionStarted();
            $_SESSION[self::SESSION_KEY] = $user->id;

            return array(
                'success' => true,
                'student_id' => $user->id,
                'student_name' => $user->name,
            );
        }

        return array('success' => false, 'message' => 'Invalid credentials');
    }

    protected static function getStudentById($id)
    {
        // Custom logic để lấy student info
        return YourDatabase::findUserById($id);
    }
}
```

### 2. Thay đổi session storage

```php
class RedisSessionManager extends StudentSessionManager
{
    protected static $redis;

    public static function init($redis_connection)
    {
        self::$redis = $redis_connection;
    }

    protected static function setSession($key, $value)
    {
        $session_id = session_id();
        self::$redis->hset("session:$session_id", $key, $value);
    }

    protected static function getSession($key)
    {
        $session_id = session_id();
        return self::$redis->hget("session:$session_id", $key);
    }
}
```

## Error Codes

- `INVALID_INPUT`: Username hoặc password trống
- `INVALID_CREDENTIALS`: Username hoặc password không đúng

## Response Format

### Success Response

```json
{
  "success": true,
  "student_id": 123,
  "student_name": "Nguyễn Văn A",
  "message": "Login successful"
}
```

### Error Response

```json
{
  "success": false,
  "message": "Invalid credentials",
  "error_code": "INVALID_CREDENTIALS"
}
```

## Security Notes

1. **Password Storage**: Hiện tại class lưu password dạng plain text. Trong production nên dùng `password_hash()` và `password_verify()`

2. **Session Security**: Nên config session với các options bảo mật:

```php
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => 'yourdomain.com',
    'secure' => true,    // HTTPS only
    'httponly' => true,  // No JavaScript access
    'samesite' => 'Strict'
]);
```

3. **Input Validation**: Class đã có basic sanitization, nhưng nên thêm validation tùy theo yêu cầu

## Changelog

### v1.0.0

- Initial release
- Basic session management
- WordPress integration
- REST API support

## License

MIT License - Có thể sử dụng tự do trong các project thương mại và cá nhân.

## Hỗ trợ

Nếu có thắc mắc hoặc bug, vui lòng tạo issue hoặc liên hệ developer.
