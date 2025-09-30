# BB Data Plugin

> WordPress plugin quản lý dữ liệu School → Class → Entity với import/export CSV/JSON

## 🎯 Flow Chính

```
School (Trường) → Class (Lớp) → Entity (Bài học)
```

## 📁 Cấu Trúc Code

```
data-plugin.php          → Main file (69 dòng)
├── includes/
│   ├── post-types.php   → Đăng ký 3 custom post types
│   ├── admin-page.php   → Giao diện admin + forms
│   ├── csv-handler.php  → Logic CSV import/export
│   ├── json-handler.php → Logic JSON import/export
│   └── admin-columns.php → Custom columns admin
└── assets/
    ├── admin-styles.css → CSS styling
    └── admin-scripts.js → JavaScript functions
```

## ⚡ Tính Năng

### 📊 Data Types

- **School**: Chỉ có `title`
- **Class**: `title`, `password`, `parent_school`
- **Entity**: `title`, `password`, `parent_class`, `link`, `image_url`

### 🔄 Import/Export

- **CSV Format**: `type,title,password,parent,link,image_url`
- **JSON Format**: Structured data với metadata
- **Auto Download**: Sample files từ JavaScript

## 🚀 Quick Start

1. **Activate Plugin** → Xuất hiện menu "Import Data"
2. **View Data** → Schools/Classes/Entities trong admin
3. **Import** → Upload CSV/JSON files
4. **Export** → Download data

## 🔧 Core Functions

### Post Types (includes/post-types.php)

```php
bb_data_plugin_register_post_types()  // Đăng ký 3 post types
```

### Import/Export (includes/csv-handler.php, json-handler.php)

```php
bb_data_plugin_export_csv_posts()     // Export CSV
bb_data_plugin_import_csv_posts()     // Import CSV
bb_data_plugin_export_json_posts()    // Export JSON
bb_data_plugin_import_json_posts()    // Import JSON
```

### Admin Interface (includes/templates/admin-page.php)

```php
bb_data_plugin_posts_admin_page()     // Main admin page
```

## 📝 CSV Format

```csv
type,title,password,parent,link,image_url
school,"Trường THPT ABC","","","",""
class,"Lớp 10A1","password123","Trường THPT ABC","",""
entity,"Bài học 1","lesson123","Lớp 10A1","https://example.com","image.jpg"
```

## 🎯 Flow Xử Lý

### Import Flow

1. **Upload file** → Validate format
2. **Parse data** → Check existing records
3. **Create/Update** → Save to wp_posts + wp_postmeta
4. **Response** → Show success/error message

### Export Flow

1. **Get data** → Query wp_posts by post_type
2. **Format data** → Structure theo format
3. **Download** → Set headers + output file

## 💡 Key Points

- **Clean Architecture**: Mỗi file 1 chức năng
- **WordPress Standards**: Sử dụng wp_posts table
- **Security**: Nonce verification + sanitization
- **User Friendly**: Sample downloads + validation
- **Maintainable**: Code tách riêng, dễ debug

---

**Made with ❤️ by Binh Vo**
