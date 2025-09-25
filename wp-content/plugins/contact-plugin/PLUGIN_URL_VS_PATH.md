# WordPress Plugin URL vs PATH

## Khác biệt cơ bản

**PLUGIN_PATH** (`plugin_dir_path(__FILE__)`)

- Đường dẫn file system: `C:\xampp\htdocs\wp_practice\wp-content\plugins\contact-plugin\`
- Dùng cho: Include PHP files, đọc/ghi files

**PLUGIN_URL** (`plugin_dir_url(__FILE__)`)

- URL web: `http://localhost/wp_practice/wp-content/plugins/contact-plugin/`
- Dùng cho: CSS, JS, images

## Quy tắc đơn giản

### ✅ PATH cho PHP:

```php
include_once MY_PLUGIN_PATH . 'includes/utilities.php';
```

### ✅ URL cho Browser:

```php
wp_enqueue_style('my-style', MY_PLUGIN_URL . '/assets/css/style.css');
```

## Lỗi thường gặp

❌ **PATH cho CSS** → 404 error  
❌ **URL cho include** → failed to open stream

## Nhớ nhanh

## Khi nào dùng cái gì?

| Tình huống                | Dùng gì     | Lý do                           |
| ------------------------- | ----------- | ------------------------------- |
| Include/require PHP files | PLUGIN_PATH | PHP cần đường dẫn file hệ thống |
| Đọc/ghi file nội bộ       | PLUGIN_PATH | Chỉ server truy cập được        |
| Load template PHP         | PLUGIN_PATH | PHP include cần path thật       |
| Enqueue CSS/JS            | PLUGIN_URL  | Browser cần URL để tải file     |
| Ảnh, icon trong HTML      | PLUGIN_URL  | Browser cần URL để hiển thị     |
| AJAX endpoint, asset      | PLUGIN_URL  | JS/browser cần URL              |

- **PATH** = Server (PHP)
- **URL** = Browser (CSS/JS)
