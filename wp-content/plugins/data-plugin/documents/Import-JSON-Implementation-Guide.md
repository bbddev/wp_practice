# WordPress Plugin - Import JSON Implementation Guide

## Tóm tắt

Hướng dẫn chi tiết cách thêm tính năng import JSON vào WordPress plugin, cho phép nhập dữ liệu từ file JSON có cấu trúc vào custom post types.

## 📋 Các bước thực hiện

### 1. Tạo AJAX Handler cho Import JSON

```php
// Handle JSON import using wp_posts
add_action('wp_ajax_import_json_data_posts', 'bb_data_plugin_import_json_posts');
function bb_data_plugin_import_json_posts()
{
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import_json')) {
        wp_die('Security check failed');
    }

    $res_status = $res_msg = '';

    if (isset($_POST['importJsonSubmit'])) {
        // Allowed mime types for JSON
        $jsonMimes = array('application/json', 'text/json', 'text/plain', 'application/octet-stream');

        // Validate file type
        if (!empty($_FILES['json_file']['name']) && in_array($_FILES['json_file']['type'], $jsonMimes)) {

            if (is_uploaded_file($_FILES['json_file']['tmp_name'])) {

                // Read and parse JSON file
                $jsonContent = file_get_contents($_FILES['json_file']['tmp_name']);
                $jsonData = json_decode($jsonContent, true);

                // Validate JSON structure
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $res_status = 'danger';
                    $res_msg = 'Invalid JSON file format. Error: ' . json_last_error_msg();
                } elseif (!isset($jsonData['schools']) || !isset($jsonData['classes']) || !isset($jsonData['entities'])) {
                    $res_status = 'danger';
                    $res_msg = 'Invalid JSON structure. Missing required sections: schools, classes, or entities.';
                } else {
                    // Process import logic here...
                    // Initialize counters
                    $total_imported = 0;
                    $total_updated = 0;
                    $total_created = 0;
                    $total_skipped = 0;

                    // Import each data type...
                    // (See full code in main file)

                    $res_status = 'success';
                    $res_msg = "JSON Import hoàn tất! Tổng cộng: {$total_imported} dòng đã import thành công.";
                }
            }
        } else {
            $res_status = 'danger';
            $res_msg = 'Please select a valid JSON file.';
        }

        // Store result in session
        $_SESSION['response'] = array(
            'status' => $res_status,
            'msg' => $res_msg
        );
    }

    // Redirect back to admin page
    wp_redirect(admin_url('admin.php?page=my-data-plugin-posts'));
    exit();
}
```

### 2. Thêm Form Upload JSON vào Admin Page

```html
<!-- JSON file upload form -->
<form
  action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
  method="post"
  enctype="multipart/form-data"
  class="col-md-6"
  style="margin-top: 20px;"
>
  <?php wp_nonce_field('bb_data_import_json', 'bb_data_nonce'); ?>
  <input type="hidden" name="action" value="import_json_data_posts" />

  <div style="margin-bottom: 15px;">
    <input
      type="file"
      name="json_file"
      id="json_file"
      required
      accept=".json"
      style="margin-top: 5px;"
    />

    <p style="margin: 10px 0 0 0; font-size: 12px;">
      <em>JSON format: structured data với schools, classes, entities</em><br />
    </p>
    <p style="margin: 10px 0 0 0; font-size: 12px;">
      <a href="#" onclick="downloadJsonSample();" style="color: #0073aa;"
        >Download JSON Sample Format</a
      >
    </p>
  </div>

  <div>
    <input
      type="submit"
      class="btn btn-info"
      name="importJsonSubmit"
      value="Import JSON"
    />
  </div>
</form>
```

### 3. JavaScript cho Download Sample JSON

```javascript
function downloadJsonSample() {
  var jsonContent = {
    export_info: {
      export_date: "2025-09-30 14:30:15",
      plugin_version: "1.0",
      total_records: 3,
    },
    schools: [
      {
        id: 1,
        title: "Sample School",
        type: "school",
        created_date: "2025-09-30 10:00:00",
      },
    ],
    classes: [
      {
        id: 2,
        title: "Sample Class",
        type: "class",
        password: "123456",
        parent_school: "Sample School",
        created_date: "2025-09-30 11:00:00",
      },
    ],
    entities: [
      {
        id: 3,
        title: "Sample Entity",
        type: "entity",
        password: "password123",
        parent_class: "Sample Class",
        link: "https://example.com",
        image_url: "https://example.com/image.jpg",
        created_date: "2025-09-30 12:00:00",
      },
    ],
  };

  var jsonString = JSON.stringify(jsonContent, null, 2);
  var blob = new Blob([jsonString], {
    type: "application/json;charset=utf-8;",
  });
  var link = document.createElement("a");
  var url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", "sample-data.json");
  link.style.visibility = "hidden";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
```

## 🔧 Các thành phần chính

### A. File Validation (Kiểm tra file)

- **MIME Types**: Cho phép multiple JSON mime types
- **File Upload Check**: `is_uploaded_file()` security
- **JSON Parsing**: `json_decode()` với error handling

### B. JSON Structure Validation

```php
// Check JSON parsing errors
if (json_last_error() !== JSON_ERROR_NONE) {
    $error = json_last_error_msg();
}

// Check required structure
if (!isset($jsonData['schools']) || !isset($jsonData['classes']) || !isset($jsonData['entities'])) {
    // Invalid structure
}
```

### C. Data Processing Logic

- **Duplicate Check**: Tìm existing posts theo title + parent
- **Sanitization**: `sanitize_text_field()`, `esc_url_raw()`
- **Flexible Import**: Handle missing fields gracefully
- **Counters**: Track created, updated, skipped records

### D. Security (Bảo mật)

- **Nonce khác biệt**: `bb_data_import_json` khác với CSV
- **File validation**: Check MIME types và upload status
- **Data sanitization**: Clean all input data

## 📊 JSON Input Structure Expected

```json
{
  "export_info": {
    "export_date": "2025-09-30 14:30:15",
    "plugin_version": "1.0",
    "total_records": 25
  },
  "schools": [
    {
      "title": "Trường ABC",
      "type": "school"
    }
  ],
  "classes": [
    {
      "title": "Lớp 10A1",
      "type": "class",
      "password": "123456",
      "parent_school": "Trường ABC"
    }
  ],
  "entities": [
    {
      "title": "Bài học 1",
      "type": "entity",
      "password": "pass123",
      "parent_class": "Lớp 10A1",
      "link": "https://example.com",
      "image_url": "http://localhost/image.jpg"
    }
  ]
}
```

## 💡 Import Logic Flow

### 1. **File Processing**

```
Upload → Validate MIME → Read Content → Parse JSON → Validate Structure
```

### 2. **Data Import Process**

```
For each data type (schools, classes, entities):
├── Validate required fields (title)
├── Sanitize input data
├── Check for existing records
├── Update existing OR Create new
└── Update counters
```

### 3. **Duplicate Handling**

- **Schools**: Check by `title` only
- **Classes**: Check by `title` + `parent_school`
- **Entities**: Check by `title` + `parent_class`

## 🔥 Ưu điểm của JSON Import

### 1. **Structured Data Import**

- Nhập được cả hierarchy relationships
- Bao gồm metadata đầy đủ
- Flexible với missing fields

### 2. **Validation Mạnh**

- JSON parsing validation
- Structure validation
- Field-level validation

### 3. **User-Friendly**

- Sample file download
- Clear error messages
- Progress feedback

### 4. **Data Integrity**

- Transaction-like processing
- Rollback on major errors
- Detailed import statistics

## 🚀 Mở rộng tính năng

### A. Batch Processing (cho file lớn)

```php
// Process in chunks
$chunk_size = 100;
$chunks = array_chunk($jsonData['schools'], $chunk_size);
foreach ($chunks as $chunk) {
    // Process chunk
    // Avoid memory limits
}
```

### B. Backup trước khi Import

```php
// Create backup before import
$backup_data = array(
    'schools' => get_posts(['post_type' => 'school']),
    'classes' => get_posts(['post_type' => 'class']),
    'entities' => get_posts(['post_type' => 'entity'])
);
set_transient('import_backup_' . time(), $backup_data, HOUR_IN_SECONDS);
```

### C. Preview Mode

```php
$preview_mode = isset($_POST['preview_mode']) ? true : false;
if ($preview_mode) {
    // Show what would be imported without actually importing
    return $preview_results;
}
```

### D. Field Mapping

```php
// Allow custom field mapping
$field_mapping = array(
    'school_name' => 'title',
    'school_type' => 'type',
    // etc...
);
```

## 🛠️ Error Handling Best Practices

### 1. **JSON Parsing Errors**

```php
$error_messages = array(
    JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
    JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
    JSON_ERROR_CTRL_CHAR => 'Control character error',
    JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
    JSON_ERROR_UTF8 => 'Malformed UTF-8 characters'
);
```

### 2. **Graceful Degradation**

```php
// Continue processing even if some records fail
try {
    // Import logic
} catch (Exception $e) {
    $total_skipped++;
    error_log("Import error: " . $e->getMessage());
    continue; // Continue with next record
}
```

### 3. **User Feedback**

```php
$res_msg = "Import completed with {$total_created} new, {$total_updated} updated, {$total_skipped} skipped records.";
if ($total_skipped > 0) {
    $res_msg .= " Check logs for skipped record details.";
}
```

## 📋 So sánh Import Methods

| Feature             | CSV Import             | JSON Import                  |
| ------------------- | ---------------------- | ---------------------------- |
| **Structure**       | Flat table format      | Nested, hierarchical         |
| **Validation**      | Basic field validation | Structure + field validation |
| **Metadata**        | Limited                | Full metadata support        |
| **Relationships**   | String references      | Structured relationships     |
| **Error Handling**  | Line-by-line errors    | Section-based errors         |
| **File Size**       | Compact                | Larger (metadata overhead)   |
| **User Experience** | Simple upload          | Advanced with preview        |

## 🎯 Best Practices

1. **Security**: Always validate and sanitize input
2. **Performance**: Process in batches for large files
3. **User Experience**: Provide clear feedback and progress
4. **Data Integrity**: Check relationships and constraints
5. **Error Recovery**: Graceful handling of partial failures
6. **Documentation**: Clear sample files and field descriptions

## 📁 Complete Feature Set

```
Plugin Features:
├── Import CSV ✅
├── Import JSON ✅
├── Export CSV ✅
├── Export JSON ✅
└── Data Management UI ✅
```

---

**Kết luận**: JSON Import bổ sung hoàn hảo cho ecosystem import/export, đặc biệt phù hợp cho advanced users và system integration!
