# BB - Data Plugin (Posts Version) - Complete Guide 📚

## Mục Lục

1. [Tổng Quan Plugin](#1-tổng-quan-plugin-)
2. [Kiến Trúc & Cấu Trúc](#2-kiến-trúc--cấu-trúc-)
3. [Custom Post Types System](#3-custom-post-types-system-)
4. [CSV Import System](#4-csv-import-system-)
5. [Admin Interface](#5-admin-interface-)
6. [Security & Validation](#6-security--validation-)
7. [Database Schema](#7-database-schema-)
8. [Code Analysis Chi Tiết](#8-code-analysis-chi-tiết-)
9. [Workflow & User Journey](#9-workflow--user-journey-)
10. [Customization Guide](#10-customization-guide-)
11. [Troubleshooting](#11-troubleshooting-)
12. [Extension Ideas](#12-extension-ideas-)

---

## 1. TỔNG QUAN PLUGIN 🎯

### A. Mục đích Plugin:

Plugin **BB - Data Plugin (Posts Version)** được thiết kế để:

- ✅ Quản lý dữ liệu giáo dục theo cấu trúc **School → Class → Entity**
- ✅ Import dữ liệu hàng loạt từ file CSV
- ✅ Sử dụng WordPress Posts table thay vì tạo custom tables
- ✅ Cung cấp interface admin thân thiện

### B. Đối tượng sử dụng:

- 🏫 **Trường học** - Quản lý cấu trúc tổ chức
- 👨‍🏫 **Giáo viên** - Quản lý lớp học và bài giảng
- 💼 **Admin** - Import/export dữ liệu hàng loạt

### C. Tính năng chính:

1. **3 Custom Post Types**: School, Class, Entity
2. **CSV Import System**: Upload và xử lý file CSV
3. **Hierarchical Data**: Quan hệ cha-con giữa các entities
4. **Custom Admin Columns**: Hiển thị meta data
5. **Security Layer**: Nonce verification, data sanitization

---

## 2. KIẾN TRÚC & CẤU TRÚC 🏗️

### A. Plugin Architecture:

```
BB Data Plugin
├── Custom Post Types Layer
│   ├── School (Trường)
│   ├── Class (Lớp)
│   └── Entity (Bài học/Tài liệu)
├── Data Management Layer
│   ├── CSV Import System
│   ├── Meta Data Handler
│   └── Relationship Manager
├── Admin Interface Layer
│   ├── Import Page
│   ├── Custom Columns
│   └── View Links
└── Security Layer
    ├── Nonce Verification
    ├── Data Sanitization
    └── Capability Checks
```

### B. Data Flow:

```
CSV File → Upload → Validation → Parse → Sanitize → Database → Display
     ↓
[type,title,password,parent,link,image_url]
     ↓
WordPress Posts Table + Meta Table
     ↓
Admin Interface với Custom Columns
```

### C. File Structure:

```
data-plugin/
├── data-plugin-posts-version.php    (Main plugin file - 467 lines)
├── schooldata.csv                   (Sample data)
├── hybrid-approach-example.php      (Alternative approach)
└── BB-Data-Plugin-Complete-Guide.md (This guide)
```

---

## 3. CUSTOM POST TYPES SYSTEM 📝

### A. School Post Type:

```php
register_post_type('school', array(
    'labels' => array(
        'name' => 'Schools',
        'singular_name' => 'School',
        // ... more labels
    ),
    'public' => false,           // Không hiện ở frontend
    'show_ui' => true,           // Hiện ở admin
    'show_in_menu' => true,      // Có menu riêng
    'supports' => array('title'), // Chỉ hỗ trợ title
    'menu_icon' => 'dashicons-building'
));
```

**Đặc điểm School:**

- 🏢 **Không có parent** - Là cấp cao nhất
- 📋 **Chỉ có title** - Không cần thêm fields
- 🎯 **Root level** - Các Class sẽ thuộc về School

### B. Class Post Type:

```php
register_post_type('class', array(
    'labels' => array(
        'name' => 'Classes',
        'singular_name' => 'Class',
        // ... more labels
    ),
    'menu_icon' => 'dashicons-groups'
));
```

**Đặc điểm Class:**

- 🏫 **Thuộc School** - Meta: `Thuộc Trường`
- 🔐 **Có password** - Meta: `class_password`
- 👥 **Chứa Entities** - Là parent của các Entity

### C. Entity Post Type:

```php
register_post_type('entity', array(
    'labels' => array(
        'name' => 'Entities',
        'singular_name' => 'Entity',
        // ... more labels
    ),
    'menu_icon' => 'dashicons-media-document'
));
```

**Đặc điểm Entity:**

- 📚 **Thuộc Class** - Meta: `Thuộc lớp`
- 🔐 **Có password** - Meta: `lesson_password`
- 🔗 **Có link** - Meta: `Link khi click`
- 🖼️ **Có hình ảnh** - Meta: `Hình`

### D. Meta Fields Schema:

| Post Type | Meta Key          | Purpose          | Example                     |
| --------- | ----------------- | ---------------- | --------------------------- |
| school    | -                 | Không có meta    | -                           |
| class     | `class_password`  | Mật khẩu lớp     | "123456"                    |
| class     | `Thuộc Trường`    | Tên trường       | "Trường THPT ABC"           |
| entity    | `lesson_password` | Mật khẩu bài học | "password123"               |
| entity    | `Thuộc lớp`       | Tên lớp          | "Lớp 10A1"                  |
| entity    | `Link khi click`  | URL liên kết     | "https://example.com"       |
| entity    | `Hình`            | URL hình ảnh     | "http://site.com/image.png" |

---

## 4. CSV IMPORT SYSTEM 📊

### A. CSV Format:

```csv
type,title,password,parent,link,image_url
school,Trường THPT ABC,,,,
class,Lớp 10A1,123456,Trường THPT ABC,,
entity,Bài 1: Giới thiệu,pass123,Lớp 10A1,https://example.com,http://site.com/img.png
```

### B. Import Process Flow:

#### Step 1: File Validation

```php
// Allowed MIME types
$csvMimes = array(
    'text/x-comma-separated-values',
    'text/comma-separated-values',
    'application/octet-stream',
    'application/vnd.ms-excel',
    'application/x-csv',
    'text/x-csv',
    'text/csv',
    'application/csv',
    'application/excel',
    'application/vnd.msexcel'
);

// Validate file type
if (!in_array($_FILES['file']['type'], $csvMimes)) {
    // Error: Invalid file type
}
```

#### Step 2: Data Processing

```php
// Open CSV file
$csvFile = fopen($_FILES['file']['tmp_name'], 'r');

// Skip header row
fgetcsv($csvFile);

// Process each row
while (($line = fgetcsv($csvFile)) !== FALSE) {
    // Parse and sanitize data
    $type = sanitize_text_field(trim($line_arr[0]));
    $title = sanitize_text_field(trim($line_arr[1]));
    $password = sanitize_text_field(trim($line_arr[2]));
    $parent = sanitize_text_field(trim($line_arr[3]));
    $link = esc_url_raw(trim($line_arr[4]));
    $image_url = esc_url_raw(trim($line_arr[5]));
}
```

#### Step 3: Duplicate Detection

```php
// Check for existing posts
$existing_posts = get_posts(array(
    'post_type' => $type,
    'title' => $title,
    'post_status' => array('publish', 'draft'),
    'numberposts' => 1,
    'meta_query' => array(
        array(
            'key' => $parent_meta_key,
            'value' => $parent,
            'compare' => '='
        )
    )
));
```

#### Step 4: Data Creation/Update

```php
if ($existing_posts) {
    // Update existing post
    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => $title,
        'post_status' => 'publish'
    ));
    // Update meta fields
    foreach ($meta_input as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
} else {
    // Create new post
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_type' => $type,
        'post_status' => 'publish',
        'meta_input' => $meta_input
    ));
}
```

### C. Import Statistics:

Plugin track và hiển thị:

- ✅ **Total Imported**: Tổng số records đã import
- 🆕 **Created**: Records mới tạo
- 🔄 **Updated**: Records đã cập nhật
- ⚠️ **Skipped**: Records bị bỏ qua (lỗi validation)

---

## 5. ADMIN INTERFACE 🖥️

### A. Main Import Page:

#### Location: `wp-admin/admin.php?page=my-data-plugin-posts`

#### Features:

1. **File Upload Form**:

   ```html
   <form action="admin-ajax.php" method="post" enctype="multipart/form-data">
     <input type="file" name="file" accept=".csv" required />
     <input type="submit" name="importSubmit" value="Import" />
   </form>
   ```

2. **Quick Links**:

   - View Schools → `edit.php?post_type=school`
   - View Classes → `edit.php?post_type=class`
   - View Entities → `edit.php?post_type=entity`

3. **Sample Download**:

   ```javascript
   function downloadSample() {
     var csvContent =
       "type,title,password,parent,link,image_url\n" +
       "school,Field 1,,,,\n" +
       "class,Class 1 - Field 1,123456,Field 1,,\n" +
       "entity,Lesson 1,password123,Class 1,https://example.com,image.png";
     // Create download blob
   }
   ```

4. **Status Messages**:
   - Success: Hiển thị số liệu import
   - Error: Thông báo lỗi chi tiết

### B. Custom Admin Columns:

#### For Class Post Type:

| Column   | Content       | Source              |
| -------- | ------------- | ------------------- |
| Title    | Class name    | post_title          |
| Password | \*\*\*\* or - | class_password meta |
| Parent   | School name   | Thuộc Trường meta   |

#### For Entity Post Type:

| Column   | Content       | Source               |
| -------- | ------------- | -------------------- |
| Title    | Entity name   | post_title           |
| Password | \*\*\*\* or - | lesson_password meta |
| Parent   | Class name    | Thuộc lớp meta       |
| Link     | View Link     | Link khi click meta  |
| Image    | View Image    | Hình meta            |

#### Implementation:

```php
function bb_data_custom_columns($columns) {
    global $typenow;

    if ($typenow === 'class') {
        $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
    } elseif ($typenow === 'entity') {
        $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
        $columns['csv_link'] = 'Link';
        $columns['csv_image'] = 'Image';
    }
    return $columns;
}
```

---

## 6. SECURITY & VALIDATION 🛡️

### A. Security Measures:

#### 1. Nonce Verification:

```php
// Generate nonce in form
wp_nonce_field('bb_data_import', 'bb_data_nonce');

// Verify nonce in handler
if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import')) {
    wp_die('Security check failed');
}
```

#### 2. Capability Checks:

```php
// Admin menu only for administrators
add_menu_page(
    'Import Data',
    'Import Data',
    'manage_options',  // Only admins can access
    'my-data-plugin-posts',
    'bb_data_plugin_posts_admin_page'
);
```

#### 3. File Upload Security:

```php
// Validate file type
$csvMimes = array('text/csv', 'application/csv', ...);
if (!in_array($_FILES['file']['type'], $csvMimes)) {
    // Reject file
}

// Check if file is uploaded
if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
    // Security violation
}
```

### B. Data Sanitization:

#### Input Sanitization:

```php
$type = sanitize_text_field(trim($line_arr[0]));       // Text field
$title = sanitize_text_field(trim($line_arr[1]));      // Text field
$password = sanitize_text_field(trim($line_arr[2]));   // Text field
$parent = sanitize_text_field(trim($line_arr[3]));     // Text field
$link = esc_url_raw(trim($line_arr[4]));               // URL
$image_url = esc_url_raw(trim($line_arr[5]));          // URL
```

#### Output Escaping:

```php
// HTML content
echo esc_html($parent ?: '-');

// URL attributes
echo '<a href="' . esc_url($link) . '" target="_blank">View Link</a>';

// HTML attributes
echo '<div class="alert alert-' . esc_attr($status) . '">';
```

### C. Validation Rules:

#### Type Validation:

```php
if (!in_array($type, array('school', 'class', 'entity'))) {
    $total_skipped++;
    continue; // Skip invalid types
}
```

#### Data Completeness:

- **School**: Chỉ cần `type` và `title`
- **Class**: Cần `type`, `title`, và có thể có `password`, `parent`
- **Entity**: Cần `type`, `title`, có thể có tất cả fields khác

---

## 7. DATABASE SCHEMA 💾

### A. WordPress Tables Used:

#### wp_posts:

| Column      | Usage            | Example                     |
| ----------- | ---------------- | --------------------------- |
| ID          | Primary key      | 123                         |
| post_title  | Entity name      | "Trường THPT ABC"           |
| post_type   | Entity type      | "school", "class", "entity" |
| post_status | Always "publish" | "publish"                   |
| post_date   | Creation time    | "2025-09-30 10:00:00"       |

#### wp_postmeta:

| Column     | Usage                    | Example          |
| ---------- | ------------------------ | ---------------- |
| post_id    | Reference to wp_posts.ID | 123              |
| meta_key   | Field identifier         | "class_password" |
| meta_value | Field value              | "123456"         |

### B. Relationships:

#### Hierarchical Structure:

```
School (post_type: school)
├── Class 1 (post_type: class, meta: Thuộc Trường = "School Name")
│   ├── Entity 1 (post_type: entity, meta: Thuộc lớp = "Class 1")
│   └── Entity 2 (post_type: entity, meta: Thuộc lớp = "Class 1")
└── Class 2 (post_type: class, meta: Thuộc Trường = "School Name")
    ├── Entity 3 (post_type: entity, meta: Thuộc lớp = "Class 2")
    └── Entity 4 (post_type: entity, meta: Thuộc lớp = "Class 2")
```

#### Query Examples:

```php
// Get all classes of a school
$classes = get_posts(array(
    'post_type' => 'class',
    'meta_query' => array(
        array(
            'key' => 'Thuộc Trường',
            'value' => 'School Name',
            'compare' => '='
        )
    )
));

// Get all entities of a class
$entities = get_posts(array(
    'post_type' => 'entity',
    'meta_query' => array(
        array(
            'key' => 'Thuộc lớp',
            'value' => 'Class Name',
            'compare' => '='
        )
    )
));
```

---

## 8. CODE ANALYSIS CHI TIẾT 🔍

### A. Plugin Header Analysis:

```php
/**
 * Plugin Name: BB - Data Plugin (Posts Version)  ← Tên hiển thị
 * Description: A simple data plugin using WordPress posts table. ← Mô tả
 * Version: 1.0 ← Phiên bản
 * Author: Binh Vo ← Tác giả
 */
```

**Ý nghĩa**: Header này giúp WordPress nhận diện plugin và hiển thị trong danh sách plugins.

### B. Security Check Analysis:

```php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
```

**Ý nghĩa**: Ngăn chặn truy cập trực tiếp file PHP, tránh lộ source code.

### C. Hook Registration Analysis:

```php
// Activation hook - chạy 1 lần khi activate plugin
register_activation_hook(__FILE__, 'bb_data_plugin_register_post_type');

// Init hook - chạy mỗi lần WordPress load
add_action('init', 'bb_data_plugin_register_post_type');
```

**Ý nghĩa**: Đảm bảo Custom Post Types được đăng ký cả khi activate và khi site chạy.

### D. Session Management Analysis:

```php
add_action('init', 'bb_data_plugin_start_session');
function bb_data_plugin_start_session() {
    if (!session_id()) {
        session_start();
    }
}
```

**Ý nghĩa**: Khởi tạo PHP session để lưu temporary data (như thông báo import).

### E. AJAX Handler Analysis:

```php
add_action('wp_ajax_import_csv_data_posts', 'bb_data_plugin_import_csv_posts');
```

**Vấn đề**: Thiếu `wp_ajax_nopriv_` nghĩa là chỉ logged-in users mới dùng được.

### F. Meta Input Strategy Analysis:

```php
$meta_input = array();

if ($type === 'school') {
    // School: only type and title
} elseif ($type === 'class') {
    if ($password) $meta_input['class_password'] = $password;
    if ($parent) $meta_input['Thuộc Trường'] = $parent;
} elseif ($type === 'entity') {
    if ($password) $meta_input['lesson_password'] = $password;
    if ($parent) $meta_input['Thuộc lớp'] = $parent;
    if ($link) $meta_input['Link khi click'] = $link;
    if ($image_url) $meta_input['Hình'] = $image_url;
}
```

**Ý nghĩa**: Conditional meta creation - chỉ tạo meta fields khi có dữ liệu.

---

## 9. WORKFLOW & USER JOURNEY 🚀

### A. Admin User Journey:

#### Step 1: Plugin Activation

```
Admin Dashboard → Plugins → Activate "BB - Data Plugin"
    ↓
Custom Post Types được đăng ký
    ↓
Menu "Import Data" xuất hiện
```

#### Step 2: Data Preparation

```
Chuẩn bị file CSV với format:
type,title,password,parent,link,image_url
school,Trường A,,,,
class,Lớp 10A,123456,Trường A,,
entity,Bài 1,pass123,Lớp 10A,https://link.com,image.png
```

#### Step 3: Data Import

```
Dashboard → Import Data → Choose File → Upload CSV
    ↓
File validation → Data parsing → Database insertion
    ↓
Success message với statistics
```

#### Step 4: Data Management

```
View Schools → Quản lý các trường
View Classes → Quản lý các lớp (với Password và Parent columns)
View Entities → Quản lý các entities (với đầy đủ columns)
```

### B. System Workflow:

#### Import Process Detailed:

```
1. File Upload
   ├── MIME type validation
   ├── File existence check
   └── Security validation

2. CSV Processing
   ├── Open file handle
   ├── Skip header row
   └── Process each data row

3. Data Validation
   ├── Type validation (school/class/entity)
   ├── Required field check
   └── Data sanitization

4. Duplicate Detection
   ├── Query existing posts by title + type
   ├── Meta query for parent relationship
   └── Decide create vs update

5. Database Operations
   ├── wp_insert_post() or wp_update_post()
   ├── Meta fields update
   └── Statistics tracking

6. User Feedback
   ├── Success/error messages
   ├── Import statistics display
   └── Redirect to import page
```

---

## 10. CUSTOMIZATION GUIDE 🛠️

### A. Thêm Custom Post Type mới:

#### Step 1: Đăng ký Post Type

```php
// Thêm vào function bb_data_plugin_register_post_type()
register_post_type('teacher', array(
    'labels' => array(
        'name' => 'Teachers',
        'singular_name' => 'Teacher',
        // ... more labels
    ),
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => array('title'),
    'menu_icon' => 'dashicons-admin-users'
));
```

#### Step 2: Update CSV Format

```csv
type,title,password,parent,link,image_url,email,phone
teacher,Nguyễn Văn A,,,,,teacher@email.com,0123456789
```

#### Step 3: Update Import Logic

```php
// Trong CSV processing loop
elseif ($type === 'teacher') {
    if ($email) $meta_input['teacher_email'] = sanitize_email($email);
    if ($phone) $meta_input['teacher_phone'] = sanitize_text_field($phone);
}
```

### B. Thêm Custom Fields:

#### Step 1: Update Meta Input

```php
// Thêm fields cho entity
elseif ($type === 'entity') {
    // Existing fields...
    if ($duration) $meta_input['lesson_duration'] = absint($duration);
    if ($difficulty) $meta_input['lesson_difficulty'] = sanitize_text_field($difficulty);
}
```

#### Step 2: Update Custom Columns

```php
function bb_data_custom_columns($columns) {
    global $typenow;

    if ($typenow === 'entity') {
        // Existing columns...
        $columns['duration'] = 'Duration';
        $columns['difficulty'] = 'Difficulty';
    }
    return $columns;
}
```

### C. Thêm Validation Rules:

```php
// Custom validation function
function validate_phone_number($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return (strlen($phone) === 10) ? $phone : false;
}

// Sử dụng trong import process
if (!empty($phone)) {
    $phone = validate_phone_number($phone);
    if (!$phone) {
        $total_skipped++;
        continue;
    }
}
```

### D. Thêm Export Functionality:

```php
// AJAX handler cho export
add_action('wp_ajax_export_csv_data', 'bb_data_plugin_export_csv');

function bb_data_plugin_export_csv() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['export_nonce'], 'bb_data_export')) {
        wp_die('Security check failed');
    }

    // Get all posts
    $posts = get_posts(array(
        'post_type' => array('school', 'class', 'entity'),
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    // Generate CSV content
    $csv_content = "type,title,password,parent,link,image_url\n";

    foreach ($posts as $post) {
        $line = array(
            $post->post_type,
            $post->post_title,
            get_post_meta($post->ID, $post->post_type . '_password', true),
            // ... more fields
        );
        $csv_content .= implode(',', $line) . "\n";
    }

    // Output CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="export.csv"');
    echo $csv_content;
    die();
}
```

---

## 11. TROUBLESHOOTING 🔧

### A. Common Issues:

#### 1. Import không hoạt động:

**Triệu chứng**: Click Import không có gì xảy ra
**Nguyên nhân**:

- File không đúng MIME type
- Nonce verification failed
- PHP errors

**Giải pháp**:

```php
// Debug trong bb_data_plugin_import_csv_posts()
error_log('File type: ' . $_FILES['file']['type']);
error_log('Nonce: ' . $_POST['bb_data_nonce']);
```

#### 2. Custom Columns không hiện:

**Triệu chứng**: Admin columns không xuất hiện
**Nguyên nhân**: Hook không được register đúng

**Giải pháp**:

```php
// Kiểm tra global $typenow
function bb_data_custom_columns($columns) {
    global $typenow;
    error_log('Current post type: ' . $typenow);
    // ... rest of code
}
```

#### 3. Meta data không được lưu:

**Triệu chứng**: Import thành công nhưng meta fields trống
**Nguyên nhân**: Meta keys không match

**Giải pháp**:

```php
// Debug meta input
error_log('Meta input: ' . print_r($meta_input, true));
```

#### 4. Session data không persist:

**Triệu chứng**: Success messages không hiện
**Nguyên nhân**: Session không được start

**Giải pháp**:

```php
// Check session status
function bb_data_plugin_start_session() {
    error_log('Session status: ' . session_status());
    if (!session_id()) {
        session_start();
        error_log('Session started: ' . session_id());
    }
}
```

### B. Performance Issues:

#### 1. Large CSV Import timeout:

**Giải pháp**: Batch processing

```php
$batch_size = 100;
$processed = 0;

while (($line = fgetcsv($csvFile)) !== FALSE && $processed < $batch_size) {
    // Process line
    $processed++;
}

if ($processed >= $batch_size) {
    // Store progress in option, continue later
}
```

#### 2. Memory limit với large files:

**Giải pháp**: Stream processing

```php
ini_set('memory_limit', '256M');
set_time_limit(300); // 5 minutes
```

---

## 12. EXTENSION IDEAS 🚀

### A. Advanced Features:

#### 1. User Role Integration:

```php
// Teachers chỉ thấy classes của họ
function filter_posts_by_user_role($query) {
    if (!is_admin() || !$query->is_main_query()) return;

    if (current_user_can('teacher')) {
        $teacher_id = get_current_user_id();
        $query->set('meta_query', array(
            array(
                'key' => 'assigned_teacher',
                'value' => $teacher_id,
                'compare' => '='
            )
        ));
    }
}
add_action('pre_get_posts', 'filter_posts_by_user_role');
```

#### 2. Frontend Display:

```php
// Shortcode để hiển thị data
function bb_data_display_shortcode($atts) {
    $atts = shortcode_atts(array(
        'type' => 'school',
        'parent' => '',
        'limit' => 10
    ), $atts);

    $posts = get_posts(array(
        'post_type' => $atts['type'],
        'numberposts' => $atts['limit'],
        'meta_query' => !empty($atts['parent']) ? array(
            array(
                'key' => 'Thuộc Trường', // or appropriate parent key
                'value' => $atts['parent'],
                'compare' => '='
            )
        ) : array()
    ));

    $output = '<ul class="bb-data-list">';
    foreach ($posts as $post) {
        $output .= '<li>' . esc_html($post->post_title) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}
add_shortcode('bb_data', 'bb_data_display_shortcode');
```

#### 3. REST API Endpoints:

```php
// Register REST routes
add_action('rest_api_init', function() {
    register_rest_route('bb-data/v1', '/schools', array(
        'methods' => 'GET',
        'callback' => 'bb_data_get_schools',
        'permission_callback' => '__return_true'
    ));
});

function bb_data_get_schools($request) {
    $schools = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1
    ));

    $data = array();
    foreach ($schools as $school) {
        $data[] = array(
            'id' => $school->ID,
            'title' => $school->post_title,
            'classes' => get_school_classes($school->post_title)
        );
    }

    return new WP_REST_Response($data, 200);
}
```

#### 4. Advanced Import Features:

```php
// Image upload during import
function process_image_url($image_url, $post_id) {
    if (empty($image_url)) return '';

    // Download image
    $image_data = file_get_contents($image_url);
    if (!$image_data) return $image_url;

    // Upload to WordPress
    $upload = wp_upload_bits(basename($image_url), null, $image_data);

    if (!$upload['error']) {
        // Create attachment
        $attachment = array(
            'post_mime_type' => $upload['type'],
            'post_title' => basename($image_url),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
        return wp_get_attachment_url($attach_id);
    }

    return $image_url;
}
```

### B. UI/UX Improvements:

#### 1. Progress Bar:

```javascript
// AJAX progress tracking
function uploadWithProgress() {
  var formData = new FormData();
  formData.append("file", $("#csv_file")[0].files[0]);
  formData.append("action", "import_csv_data_posts");
  formData.append("bb_data_nonce", $("#bb_data_nonce").val());

  $.ajax({
    url: ajaxurl,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener(
        "progress",
        function (evt) {
          if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            $(".progress-bar").width(percentComplete * 100 + "%");
          }
        },
        false
      );
      return xhr;
    },
    success: function (response) {
      // Handle success
    },
  });
}
```

#### 2. Data Validation Preview:

```php
// Preview import before actual import
add_action('wp_ajax_preview_csv_import', 'bb_data_preview_import');

function bb_data_preview_import() {
    // Similar to import but only validate and return preview
    $preview_data = array();
    $errors = array();

    // Process first 10 rows for preview
    $row_count = 0;
    while (($line = fgetcsv($csvFile)) !== FALSE && $row_count < 10) {
        // Validate and collect data
        $preview_data[] = array(
            'row' => $row_count + 1,
            'type' => $line[0],
            'title' => $line[1],
            'status' => 'valid', // or 'error'
            'message' => ''
        );
        $row_count++;
    }

    wp_send_json_success(array(
        'preview' => $preview_data,
        'errors' => $errors
    ));
}
```

---

## 📋 SUMMARY & NEXT STEPS

### Key Takeaways:

1. ✅ Plugin sử dụng **WordPress Posts table** thay vì custom tables
2. ✅ **3-tier hierarchy**: School → Class → Entity
3. ✅ **CSV Import system** với validation và duplicate detection
4. ✅ **Security measures**: Nonces, sanitization, capability checks
5. ✅ **Custom admin columns** cho better UX

### Điểm mạnh:

- 🎯 **WordPress native** - Tận dụng built-in functionality
- 🔒 **Security conscious** - Proper nonce và sanitization
- 📊 **User friendly** - Clear admin interface
- 🔄 **Flexible** - Dễ extend với custom fields

### Điểm cần cải thiện:

- ⚡ **Performance** - Large CSV files có thể timeout
- 🔍 **Error handling** - Cần detailed error messages
- 📱 **Mobile responsive** - Admin interface chưa optimize cho mobile
- 🌐 **Internationalization** - Chưa support multiple languages

### Next Steps:

1. 🔧 **Performance optimization** - Implement batch processing
2. 📊 **Export functionality** - Add CSV export feature
3. 🎨 **UI improvements** - Better admin interface
4. 🔌 **REST API** - Enable frontend integration
5. 🧪 **Unit tests** - Add comprehensive testing

---

_Happy coding with BB Data Plugin! 🚀_
