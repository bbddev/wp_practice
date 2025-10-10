# Student Session Manager - Hệ thống quản lý phiên đăng nhập

## Tổng quan

Đây là hệ thống quản lý session cho học sinh với các tính năng:

- Đăng nhập/đăng xuất
- Chặn đăng nhập đồng thời (chỉ một thiết bị)
- Theo dõi thông tin thiết bị (IP, trình duyệt, hệ điều hành)
- Quản lý session token an toàn

## Cấu trúc file sau khi refactor

```
utilities/
├── session-manager.php              # Class chính (StudentSessionManager)
├── SessionAutoloader.php            # Autoloader để load các class
├── interfaces/
│   └── SessionInterface.php         # Interface định nghĩa các method cần thiết
└── classes/
    ├── DeviceDetection.php          # Nhận diện thiết bị, IP, trình duyệt
    ├── SessionStorage.php           # Quản lý lưu trữ session trong database
    ├── SessionHelper.php            # Các utility methods cho session
    └── StudentValidator.php         # Validation và authentication
```

## Giải thích các class

### 1. SessionInterface.php

**Chức năng:** Định nghĩa interface cho các method cần thiết

```php
interface SessionInterface {
    public static function checkSession();
    public static function login($username, $password);
    public static function logout();
    public static function getCurrentStudent();
}
```

### 2. DeviceDetection.php

**Chức năng:** Nhận diện thông tin thiết bị

```php
DeviceDetection::getUserIP()           // Lấy IP (xử lý proxy, CDN)
DeviceDetection::parseUserAgent($ua)   // Parse thông tin từ User Agent
```

### 3. SessionStorage.php

**Chức năng:** Quản lý lưu trữ session trong database

```php
SessionStorage::generateSessionToken()                    // Tạo token
SessionStorage::saveActiveSession()                       // Lưu session
SessionStorage::isValidSession($student_id, $token)       // Kiểm tra token
SessionStorage::invalidateOtherSessions()                 // Hủy session khác
SessionStorage::updateSessionActivity()                   // Cập nhật hoạt động
```

### 4. SessionHelper.php

**Chức năng:** Các utility methods hỗ trợ session

```php
SessionHelper::ensureSessionStarted()     // Khởi tạo PHP session
SessionHelper::setSessionData()           // Set data vào $_SESSION
SessionHelper::getSessionData()           // Lấy data từ $_SESSION
SessionHelper::clearAllSessionKeys()      // Xóa tất cả session keys
SessionHelper::hasSessionData()           // Kiểm tra có session không
```

### 5. StudentValidator.php

**Chức năng:** Validation và authentication

```php
StudentValidator::validateLoginInput()                    // Validate đầu vào
StudentValidator::findStudentByUsername()                 // Tìm student
StudentValidator::verifyPassword()                        // Kiểm tra password
StudentValidator::isValidStudentPost()                    // Validate student post
StudentValidator::getCurrentStudentData()                 // Lấy thông tin student
StudentValidator::hasAccessToResource()                   // Kiểm tra quyền truy cập
```

### 6. StudentSessionManager.php (Class chính)

**Chức năng:** Quản lý session chính, sử dụng các class helper

```php
StudentSessionManager::checkSession()        // Kiểm tra trạng thái login
StudentSessionManager::login()               // Đăng nhập
StudentSessionManager::logout()              // Đăng xuất
StudentSessionManager::getCurrentStudent()   // Lấy thông tin student hiện tại
StudentSessionManager::requireLogin()        // Bắt buộc đăng nhập
```

## Cách sử dụng

### 1. Khởi tạo (tự động load)

```php
// Không cần làm gì, autoloader sẽ tự động load các class
require_once 'session-manager.php';
```

### 2. Đăng nhập

```php
$result = StudentSessionManager::login('username', 'password');

if ($result['success']) {
    echo "Đăng nhập thành công!";
    echo "Student ID: " . $result['student_id'];
    echo "IP: " . $result['user_ip'];
    echo "Browser: " . $result['device_info']['browser'];
} else {
    echo "Lỗi: " . $result['message'];
}
```

### 3. Kiểm tra trạng thái đăng nhập

```php
$session = StudentSessionManager::checkSession();

if ($session['logged_in']) {
    echo "Xin chào " . $session['student_name'];
    echo "Thiết bị: " . $session['device_browser'] . " trên " . $session['device_os'];
} else {
    echo "Chưa đăng nhập";
}
```

### 4. Lấy thông tin student hiện tại

```php
$student = StudentSessionManager::getCurrentStudent();

if ($student) {
    echo "Tên: " . $student['name'];
    echo "Email: " . $student['student_email'];
    echo "Lớp: " . $student['student_of'];
}
```

### 5. Đăng xuất

```php
$result = StudentSessionManager::logout();
echo $result['message']; // "Logged out successfully"
```

### 6. Bảo vệ trang (yêu cầu đăng nhập)

```php
if (!StudentSessionManager::requireLogin()) {
    // Chưa đăng nhập, hiện form login
    exit;
}
// Đã đăng nhập, tiếp tục xử lý...
```

## Tính năng Single Device Login

Hệ thống chỉ cho phép đăng nhập trên một thiết bị:

1. **Khi đăng nhập thiết bị mới:**

   - Tạo session token mới
   - Xóa tất cả session cũ khỏi database
   - Thiết bị cũ sẽ bị logout tự động

2. **Khi kiểm tra session:**
   - Mỗi request kiểm tra token có hợp lệ không
   - Nếu token không tồn tại trong database → logout
   - Cập nhật `last_activity` cho session hợp lệ

## Database Storage

Session được lưu trong WordPress options table:

```php
// Key: student_active_sessions_{student_id}
// Value: Array chứa thông tin session
array(
    'token_abc123' => array(
        'token' => 'abc123_1234567890',
        'ip' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0...',
        'browser' => 'Google Chrome',
        'os' => 'Windows 10',
        'platform' => 'Desktop',
        'login_time' => 1234567890,
        'last_activity' => 1234567890
    )
)
```

## Lợi ích của việc refactor

### 1. **Tách biệt trách nhiệm (Separation of Concerns)**

- Mỗi class chỉ làm một việc cụ thể
- Dễ dàng test và debug từng phần

### 2. **Dễ bảo trì (Maintainability)**

- Code ngắn gọn, rõ ràng
- Dễ tìm và sửa lỗi
- Dễ thêm tính năng mới

### 3. **Tái sử dụng (Reusability)**

- Các class helper có thể dùng cho dự án khác
- Interface giúp dễ dàng thay đổi implementation

### 4. **Extensibility**

- Dễ dàng thêm loại thiết bị mới (DeviceDetection)
- Dễ dàng thêm cách lưu trữ mới (SessionStorage)
- Dễ dàng thêm validation mới (StudentValidator)

## Testing

Để test các class riêng biệt:

```php
// Test Device Detection
$device_info = DeviceDetection::parseUserAgent($_SERVER['HTTP_USER_AGENT']);
var_dump($device_info);

// Test Session Storage
$token = SessionStorage::generateSessionToken();
echo "Generated token: " . $token;

// Test Student Validator
$validation = StudentValidator::validateLoginInput('', '');
var_dump($validation);
```

## Mở rộng trong tương lai

1. **Thêm loại authentication:**

   - OAuth (Google, Facebook)
   - Two-factor authentication
   - Biometric authentication

2. **Thêm loại storage:**

   - Redis
   - Memcached
   - Custom database table

3. **Thêm tính năng:**
   - Session timeout
   - Concurrent login limit
   - Login history
   - Security alerts

## Lưu ý quan trọng

1. **Security:**

   - Session token được generate random an toàn
   - Validate tất cả input
   - Xử lý SQL injection và XSS

2. **Performance:**

   - Sử dụng WordPress options cache
   - Minimize database queries
   - Efficient session cleanup

3. **Compatibility:**
   - Tương thích với WordPress multisite
   - Hoạt động với các plugin cache
   - Không conflict với WordPress authentication
