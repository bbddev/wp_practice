# WordPress Plugin - Export JSON Implementation Guide

## Tóm tắt

Hướng dẫn chi tiết cách thêm tính năng export JSON vào WordPress plugin, cho phép xuất dữ liệu từ custom post types ra file JSON với cấu trúc có tổ chức.

## 📋 Các bước thực hiện

### 1. Tạo AJAX Handler cho Export JSON

```php
// Handle JSON export using wp_posts
add_action('wp_ajax_export_json_data_posts', 'bb_data_plugin_export_json_posts');
function bb_data_plugin_export_json_posts()
{
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export_json')) {
        wp_die('Security check failed');
    }

    // Get all data from custom post types
    $schools = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    $classes = get_posts(array(
        'post_type' => 'class',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    $entities = get_posts(array(
        'post_type' => 'entity',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    // Build structured data array
    $export_data = array(
        'export_info' => array(
            'export_date' => current_time('Y-m-d H:i:s'),
            'plugin_version' => '1.0',
            'total_records' => count($schools) + count($classes) + count($entities)
        ),
        'schools' => array(),
        'classes' => array(),
        'entities' => array()
    );

    // Process schools
    foreach ($schools as $school) {
        $export_data['schools'][] = array(
            'id' => $school->ID,
            'title' => $school->post_title,
            'type' => 'school',
            'created_date' => $school->post_date
        );
    }

    // Process classes
    foreach ($classes as $class) {
        $password = get_post_meta($class->ID, 'class_password', true);
        $parent = get_post_meta($class->ID, 'Thuộc Trường', true);

        $export_data['classes'][] = array(
            'id' => $class->ID,
            'title' => $class->post_title,
            'type' => 'class',
            'password' => $password,
            'parent_school' => $parent,
            'created_date' => $class->post_date
        );
    }

    // Process entities
    foreach ($entities as $entity) {
        $password = get_post_meta($entity->ID, 'lesson_password', true);
        $parent = get_post_meta($entity->ID, 'Thuộc lớp', true);
        $link = get_post_meta($entity->ID, 'Link khi click', true);
        $image_url = get_post_meta($entity->ID, 'Hình', true);

        $export_data['entities'][] = array(
            'id' => $entity->ID,
            'title' => $entity->post_title,
            'type' => 'entity',
            'password' => $password,
            'parent_class' => $parent,
            'link' => $link,
            'image_url' => $image_url,
            'created_date' => $entity->post_date
        );
    }

    // Set headers for JSON download
    $filename = 'exported-data-' . date('Y-m-d-H-i-s') . '.json';
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output JSON data
    echo wp_json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}
```

### 2. Thêm Button Export JSON vào Admin Page

```html
<button
  type="button"
  class="btn btn-info"
  onclick="exportJsonData();"
  style="margin-right: 5px;"
>
  Export JSON
</button>
```

### 3. JavaScript xử lý Export JSON

```javascript
function exportJsonData() {
    // Show loading state
    var exportBtn = event.target;
    var originalText = exportBtn.innerHTML;
    exportBtn.innerHTML = 'Exporting...';
    exportBtn.disabled = true;

    // Create form for POST request
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    form.style.display = 'none';

    // Add security nonce (khác với CSV export)
    var nonceField = document.createElement('input');
    nonceField.type = 'hidden';
    nonceField.name = 'bb_data_nonce';
    nonceField.value = '<?php echo wp_create_nonce('bb_data_export_json'); ?>';
    form.appendChild(nonceField);

    // Add AJAX action
    var actionField = document.createElement('input');
    actionField.type = 'hidden';
    actionField.name = 'action';
    actionField.value = 'export_json_data_posts';
    form.appendChild(actionField);

    // Submit form
    document.body.appendChild(form);
    form.submit();

    // Reset button state
    setTimeout(function() {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        document.body.removeChild(form);
    }, 2000);
}
```

## 🔧 Các thành phần chính

### A. Security (Bảo mật)

- **Nonce khác biệt**: `bb_data_export_json` khác với CSV export
- **Cùng pattern**: `wp_verify_nonce()` và `wp_create_nonce()`

### B. Structured Data (Cấu trúc dữ liệu)

- **Export Info**: Metadata về quá trình export
- **Grouped by Type**: Dữ liệu được nhóm theo post type
- **Complete Fields**: Bao gồm ID, dates, và tất cả metadata

### C. JSON Generation (Tạo file JSON)

- **wp_json_encode()**: WordPress function để encode JSON
- **JSON_PRETTY_PRINT**: Format JSON đẹp, dễ đọc
- **JSON_UNESCAPED_UNICODE**: Giữ nguyên ký tự Unicode (tiếng Việt)

### D. HTTP Headers cho JSON

- **Content-Type**: `application/json; charset=utf-8`
- **Content-Disposition**: Force browser download file
- **Dynamic filename**: Thêm timestamp

## 📊 Cấu trúc JSON Output

```json
{
  "export_info": {
    "export_date": "2025-09-30 14:30:15",
    "plugin_version": "1.0",
    "total_records": 25
  },
  "schools": [
    {
      "id": 123,
      "title": "Trường ABC",
      "type": "school",
      "created_date": "2025-09-15 10:00:00"
    }
  ],
  "classes": [
    {
      "id": 124,
      "title": "Lớp 10A1",
      "type": "class",
      "password": "123456",
      "parent_school": "Trường ABC",
      "created_date": "2025-09-16 11:00:00"
    }
  ],
  "entities": [
    {
      "id": 125,
      "title": "Bài học 1",
      "type": "entity",
      "password": "pass123",
      "parent_class": "Lớp 10A1",
      "link": "https://example.com",
      "image_url": "http://localhost/image.jpg",
      "created_date": "2025-09-17 12:00:00"
    }
  ]
}
```

## 💡 So sánh CSV vs JSON Export

| Tính năng     | CSV Export             | JSON Export              |
| ------------- | ---------------------- | ------------------------ |
| **Cấu trúc**  | Flat, dạng bảng        | Nested, có hierarchy     |
| **Metadata**  | Hạn chế                | Đầy đủ (ID, dates, etc.) |
| **Readable**  | Excel/Sheets           | Code editors, APIs       |
| **File Size** | Nhỏ hơn                | Lớn hơn (do metadata)    |
| **Use Case**  | Import/Export đơn giản | Backup, API integration  |

## 🔥 Ưu điểm của JSON Export

### 1. **Structured Data**

- Dữ liệu được tổ chức theo hierarchy logic
- Dễ parse và xử lý bằng code
- Bao gồm metadata đầy đủ

### 2. **Export Information**

- Thời gian export
- Version plugin
- Tổng số records
- Có thể mở rộng thêm thông tin khác

### 3. **Preserve Data Types**

- Numbers remain numbers
- Booleans remain booleans
- Null values properly handled

### 4. **Unicode Support**

- `JSON_UNESCAPED_UNICODE` giữ nguyên tiếng Việt
- Không bị encode thành `\u0xyz`

## 🚀 Mở rộng tính năng

### A. Thêm Filters cho JSON Export

```php
// Add to export function
$export_data = apply_filters('bb_data_json_export_data', $export_data);

// Custom filter example
add_filter('bb_data_json_export_data', function($data) {
    $data['custom_info'] = array(
        'site_url' => get_site_url(),
        'admin_email' => get_option('admin_email')
    );
    return $data;
});
```

### B. Export theo Date Range

```php
$date_from = $_POST['date_from'] ?? '';
$args = array(
    'post_type' => 'school',
    'numberposts' => -1,
    'post_status' => 'publish'
);

if ($date_from) {
    $args['date_query'] = array(
        'after' => $date_from
    );
}
```

### C. Selective Export (chọn loại data)

```php
$export_types = $_POST['export_types'] ?? array('schools', 'classes', 'entities');

if (in_array('schools', $export_types)) {
    // Export schools
}
```

## 🛠️ WordPress Patterns được sử dụng

### 1. **AJAX Actions**

```php
add_action('wp_ajax_your_action', 'your_function');
```

### 2. **Nonce Security**

```php
wp_verify_nonce($_POST['nonce'], 'action_name');
wp_create_nonce('action_name');
```

### 3. **JSON Encoding**

```php
wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
```

### 4. **WordPress Time**

```php
current_time('Y-m-d H:i:s'); // WordPress timezone aware
```

## 📁 File Structure

```
plugin-folder/
├── plugin-main-file.php
├── Export-CSV-Implementation-Guide.md
├── Export-JSON-Implementation-Guide.md (file này)
└── sample-data.json (có thể tạo)
```

## 🎯 Best Practices

1. **Security First**: Luôn verify nonce
2. **Memory Efficient**: Sử dụng output buffering cho large datasets
3. **Error Handling**: Check wp_json_encode() return value
4. **User Experience**: Loading states và feedback
5. **Consistent Naming**: Follow WordPress naming conventions

---

**Lưu ý**: JSON export phù hợp cho backup data và integration với các hệ thống khác, trong khi CSV export tốt cho end-users và spreadsheet tools!
