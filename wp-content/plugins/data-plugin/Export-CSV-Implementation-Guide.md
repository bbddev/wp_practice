# WordPress Plugin - Export CSV Implementation Guide

## Tóm tắt

Hướng dẫn chi tiết cách thêm tính năng export CSV vào WordPress plugin, cho phép xuất dữ liệu từ custom post types ra file CSV.

## 📋 Các bước thực hiện

### 1. Tạo AJAX Handler cho Export

```php
// Handle CSV export using wp_posts
add_action('wp_ajax_export_csv_data_posts', 'bb_data_plugin_export_csv_posts');
function bb_data_plugin_export_csv_posts()
{
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export')) {
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

    // Set CSV download headers
    $filename = 'exported-data-' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, array('type', 'title', 'password', 'parent', 'link', 'image_url'));

    // Export data for each post type
    foreach ($schools as $school) {
        fputcsv($output, array(
            'school',
            $school->post_title,
            '', '', '', '' // Schools don't have these fields
        ));
    }

    foreach ($classes as $class) {
        $password = get_post_meta($class->ID, 'class_password', true);
        $parent = get_post_meta($class->ID, 'Thuộc Trường', true);

        fputcsv($output, array(
            'class',
            $class->post_title,
            $password,
            $parent,
            '', '' // Classes don't have link/image
        ));
    }

    foreach ($entities as $entity) {
        $password = get_post_meta($entity->ID, 'lesson_password', true);
        $parent = get_post_meta($entity->ID, 'Thuộc lớp', true);
        $link = get_post_meta($entity->ID, 'Link khi click', true);
        $image_url = get_post_meta($entity->ID, 'Hình', true);

        fputcsv($output, array(
            'entity',
            $entity->post_title,
            $password,
            $parent,
            $link,
            $image_url
        ));
    }

    fclose($output);
    exit(); // Important: Exit after file output
}
```

### 2. Thêm Button Export vào Admin Page

```html
<button
  type="button"
  class="btn btn-success"
  onclick="exportData();"
  style="margin-right: 5px;"
>
  Export CSV
</button>
```

### 3. JavaScript xử lý Export

```javascript
function exportData() {
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

    // Add security nonce
    var nonceField = document.createElement('input');
    nonceField.type = 'hidden';
    nonceField.name = 'bb_data_nonce';
    nonceField.value = '<?php echo wp_create_nonce('bb_data_export'); ?>';
    form.appendChild(nonceField);

    // Add AJAX action
    var actionField = document.createElement('input');
    actionField.type = 'hidden';
    actionField.name = 'action';
    actionField.value = 'export_csv_data_posts';
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

- **Nonce verification**: `wp_verify_nonce()` để xác thực request
- **Sanitization**: Dữ liệu đã được lưu an toàn trong database

### B. Data Retrieval (Lấy dữ liệu)

- **get_posts()**: Lấy tất cả posts từ custom post types
- **get_post_meta()**: Lấy metadata của từng post
- **numberposts => -1**: Lấy tất cả records (không giới hạn)

### C. CSV Generation (Tạo file CSV)

- **HTTP Headers**: Thiết lập để browser hiểu đây là file download
- **fopen('php://output', 'w')**: Tạo output stream trực tiếp
- **fputcsv()**: WordPress function để tạo CSV format đúng chuẩn

### D. File Download (Tải file)

- **Content-Type**: `text/csv; charset=utf-8`
- **Content-Disposition**: `attachment; filename=...` để force download
- **Dynamic filename**: Thêm timestamp để tránh trùng tên

## 📊 Định dạng CSV Output

```csv
type,title,password,parent,link,image_url
school,Trường ABC,,,,
class,Lớp 10A1,123456,Trường ABC,,
entity,Bài học 1,pass123,Lớp 10A1,https://example.com,image.jpg
```

## 💡 Key Points cần nhớ

### 1. WordPress AJAX Pattern

```php
// Hook cho logged-in users
add_action('wp_ajax_your_action', 'your_function');

// Hook cho non-logged-in users (nếu cần)
add_action('wp_ajax_nopriv_your_action', 'your_function');
```

### 2. CSV Headers quan trọng

```php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
header('Pragma: no-cache');
header('Expires: 0');
```

### 3. Security Best Practices

- Luôn verify nonce: `wp_verify_nonce()`
- Check user permissions nếu cần: `current_user_can()`
- Sanitize input data (tuy nhiên ở đây chỉ export nên không cần)

### 4. Memory Management

- Sử dụng `fopen('php://output', 'w')` thay vì build toàn bộ CSV trong memory
- Gọi `exit()` sau khi output xong để tránh WordPress thêm extra content

## 🚀 Mở rộng tính năng

### Export theo filter/điều kiện

```php
// Thêm tham số filter vào request
$date_from = $_POST['date_from'] ?? '';
$date_after = $_POST['date_after'] ?? '';

$args = array(
    'post_type' => 'your_type',
    'numberposts' => -1,
    'post_status' => 'publish'
);

if ($date_from) {
    $args['date_query'] = array(
        'after' => $date_from
    );
}
```

### Export với pagination (cho dataset lớn)

```php
$offset = $_POST['offset'] ?? 0;
$args['offset'] = $offset;
$args['numberposts'] = 1000; // Export từng batch 1000 records
```

## 📁 File Structure

```
plugin-folder/
├── plugin-main-file.php
├── Export-CSV-Implementation-Guide.md (file này)
└── sample-data.csv (có thể tạo)
```

---

**Lưu ý**: Luôn test kỹ tính năng export với dữ liệu thật trước khi deploy lên production!
