# WordPress Plugin Development - Complete Guide 🚀

## Mục Lục

1. [Tổng Quan](#1-tổng-quan-wordpress-plugin-development-)
2. [Cấu Trúc Plugin](#2-cấu-trúc-plugin-file-chính-)
3. [WordPress Hook System](#3-wordpress-hook-system-)
4. [Custom Post Types](#4-custom-post-types---chi-tiết-)
5. [Admin Menu & Pages](#5-admin-menu--pages-️)
6. [AJAX Handling](#6-ajax-handling-)
7. [Database Operations](#7-database-operations-)
8. [Data Sanitization & Validation](#8-data-sanitization--validation-️)
9. [Custom Admin Columns](#9-custom-admin-columns-)
10. [Best Practices & Patterns](#10-best-practices--patterns-)
11. [Development Flow](#11-wordpress-plugin-development-flow-)
12. [Quick Reference](#12-quick-reference-)

---

## 1. TỔNG QUAN WORDPRESS PLUGIN DEVELOPMENT 🎯

### Khái niệm cơ bản:

- **Plugin** = Phần mở rộng chức năng cho WordPress
- **Hook System** = Hệ thống móc nối cho phép code chạy tại các thời điểm cụ thể
- **Custom Post Types** = Loại bài viết tùy chỉnh
- **Meta Fields** = Dữ liệu bổ sung cho posts

### Cấu trúc Plugin cơ bản:

```
plugin-folder/
├── main-plugin-file.php    (file chính - bắt buộc)
├── includes/               (các file PHP phụ)
├── assets/                 (CSS, JS, images)
├── templates/              (file template)
├── languages/              (file ngôn ngữ)
└── README.md              (documentation)
```

### WordPress Plugin Lifecycle:

1. **Activation** → Plugin được kích hoạt
2. **Initialization** → WordPress load plugin
3. **Execution** → Plugin chạy các hooks đã đăng ký
4. **Deactivation** → Plugin bị tắt
5. **Uninstall** → Plugin bị gỡ bỏ hoàn toàn

---

## 2. CẤU TRÚC PLUGIN FILE CHÍNH 📋

### A. Plugin Header (BẮT BUỘC):

```php
<?php
/**
 * Plugin Name: Your Plugin Name
 * Plugin URI: https://yourwebsite.com/plugin
 * Description: Plugin description here.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: your-plugin-textdomain
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 */
```

**🔑 Các fields quan trọng:**

- `Plugin Name`: Tên hiển thị (bắt buộc)
- `Description`: Mô tả chức năng
- `Version`: Phiên bản plugin
- `Text Domain`: Dùng cho internationalization

### B. Security Check (BẮT BUỘC):

```php
// Ngăn truy cập trực tiếp file PHP
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Hoặc dùng cách này
defined('ABSPATH') || exit;
```

### C. Plugin Structure Template:

```php
<?php
/**
 * Plugin Header ở đây
 */

// Security check
if (!defined('ABSPATH')) exit;

// Constants
define('YOUR_PLUGIN_VERSION', '1.0.0');
define('YOUR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('YOUR_PLUGIN_URL', plugin_dir_url(__FILE__));

// Main Plugin Class
class Your_Plugin {

    public function __construct() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Initialize plugin
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
    }

    public function activate() {
        // Code chạy khi plugin được activate
    }

    public function deactivate() {
        // Code chạy khi plugin được deactivate
    }

    public function init() {
        // Plugin initialization
    }

    public function admin_init() {
        // Admin initialization
    }
}

// Initialize plugin
new Your_Plugin();
```

---

## 3. WORDPRESS HOOK SYSTEM 🎣

### A. Action Hooks:

```php
// Syntax
add_action('hook_name', 'callback_function', priority, accepted_args);

// Examples
add_action('init', 'my_init_function');
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');
add_action('admin_menu', 'my_admin_menu');
```

### B. Filter Hooks:

```php
// Syntax
add_filter('hook_name', 'callback_function', priority, accepted_args);

// Examples
add_filter('the_content', 'modify_post_content');
add_filter('wp_title', 'custom_title');
```

### C. Các Hooks quan trọng:

#### Lifecycle Hooks:

```php
register_activation_hook(__FILE__, 'function_name');    // Khi activate
register_deactivation_hook(__FILE__, 'function_name');  // Khi deactivate
register_uninstall_hook(__FILE__, 'function_name');     // Khi uninstall
```

#### Initialization Hooks:

```php
add_action('init', 'function_name');                     // WordPress init
add_action('admin_init', 'function_name');               // Admin init
add_action('wp_loaded', 'function_name');                // Sau khi WP load xong
```

#### Admin Hooks:

```php
add_action('admin_menu', 'function_name');               // Tạo admin menu
add_action('admin_enqueue_scripts', 'function_name');    // Load admin scripts
add_action('add_meta_boxes', 'function_name');           // Thêm meta boxes
```

#### AJAX Hooks:

```php
add_action('wp_ajax_action_name', 'function_name');         // Logged-in users
add_action('wp_ajax_nopriv_action_name', 'function_name'); // Non-logged users
```

#### Frontend Hooks:

```php
add_action('wp_enqueue_scripts', 'function_name');       // Load frontend scripts
add_action('wp_head', 'function_name');                  // Add to <head>
add_action('wp_footer', 'function_name');                // Add to footer
```

### D. Hook Priority:

```php
// Priority càng thấp chạy càng sớm (default = 10)
add_action('init', 'function_early', 5);    // Chạy sớm
add_action('init', 'function_normal');       // Priority 10 (default)
add_action('init', 'function_late', 20);     // Chạy muộn
```

---

## 4. CUSTOM POST TYPES - CHI TIẾT 📝

### A. Cấu trúc cơ bản:

```php
function register_my_post_type() {
    register_post_type('post_type_name', array(
        'labels' => array(
            'name' => 'Post Types',
            'singular_name' => 'Post Type',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Post Type',
            'edit_item' => 'Edit Post Type',
            'new_item' => 'New Post Type',
            'view_item' => 'View Post Type',
            'search_items' => 'Search Post Types',
            'not_found' => 'No post types found',
            'not_found_in_trash' => 'No post types found in Trash'
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'post-type'),
        'menu_icon' => 'dashicons-admin-post'
    ));
}
add_action('init', 'register_my_post_type');
```

### B. Các tham số quan trọng:

#### Visibility Settings:

```php
'public' => true,              // Hiện ở frontend & admin
'show_ui' => true,             // Hiện UI trong admin
'show_in_menu' => true,        // Hiện trong admin menu
'show_in_nav_menus' => true,   // Hiện trong nav menu settings
'show_in_admin_bar' => true,   // Hiện trong admin bar
```

#### Functionality Settings:

```php
'supports' => array(
    'title',          // Tiêu đề
    'editor',         // Content editor
    'thumbnail',      // Featured image
    'excerpt',        // Excerpt
    'comments',       // Comments
    'custom-fields',  // Custom fields
    'revisions',      // Revisions
    'author',         // Author
    'page-attributes' // Page order, parent
),
```

#### URL Settings:

```php
'has_archive' => true,                    // Có trang archive
'rewrite' => array(
    'slug' => 'custom-slug',              // URL slug
    'with_front' => false                 // Không dùng permalink front
),
```

#### Permissions:

```php
'capability_type' => 'post',             // Dùng permission như post
'capabilities' => array(
    'edit_post' => 'edit_custom_post',
    'edit_posts' => 'edit_custom_posts',
    'edit_others_posts' => 'edit_others_custom_posts'
),
```

### C. Menu Icons (Dashicons):

```php
'menu_icon' => 'dashicons-admin-post',      // Post icon
'menu_icon' => 'dashicons-admin-media',     // Media icon
'menu_icon' => 'dashicons-admin-users',     // Users icon
'menu_icon' => 'dashicons-building',        // Building icon
'menu_icon' => 'dashicons-groups',          // Groups icon
// Hoặc dùng custom icon URL
'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode($svg)
```

### D. Complete Example:

```php
function register_product_post_type() {
    $labels = array(
        'name' => _x('Products', 'Post type general name', 'textdomain'),
        'singular_name' => _x('Product', 'Post type singular name', 'textdomain'),
        'menu_name' => _x('Products', 'Admin Menu text', 'textdomain'),
        'add_new' => __('Add New', 'textdomain'),
        'add_new_item' => __('Add New Product', 'textdomain'),
        'edit_item' => __('Edit Product', 'textdomain'),
        'new_item' => __('New Product', 'textdomain'),
        'view_item' => __('View Product', 'textdomain'),
        'view_items' => __('View Products', 'textdomain'),
        'search_items' => __('Search Products', 'textdomain'),
        'not_found' => __('No products found.', 'textdomain'),
        'not_found_in_trash' => __('No products found in Trash.', 'textdomain')
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 20,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon' => 'dashicons-products'
    );

    register_post_type('product', $args);
}
add_action('init', 'register_product_post_type');
```

---

## 5. ADMIN MENU & PAGES 🖥️

### A. Tạo Top-level Menu:

```php
function add_my_admin_menu() {
    add_menu_page(
        'Page Title',              // Tiêu đề trang (hiện trong <title>)
        'Menu Title',              // Tên menu (hiện trong sidebar)
        'manage_options',          // Capability required
        'my-menu-slug',            // Menu slug (unique)
        'my_admin_page_callback',  // Callback function
        'dashicons-admin-generic', // Icon
        6                          // Position
    );
}
add_action('admin_menu', 'add_my_admin_menu');
```

### B. Tạo Sub-menu:

```php
function add_my_admin_submenu() {
    add_submenu_page(
        'my-menu-slug',            // Parent slug
        'Sub Page Title',          // Page title
        'Sub Menu',                // Menu title
        'manage_options',          // Capability
        'my-submenu-slug',         // Menu slug
        'my_submenu_callback'      // Callback function
    );
}
add_action('admin_menu', 'add_my_admin_submenu');
```

### C. Menu vào existing pages:

```php
// Thêm vào Settings menu
add_options_page('Title', 'Menu Title', 'manage_options', 'slug', 'callback');

// Thêm vào Tools menu
add_management_page('Title', 'Menu Title', 'manage_options', 'slug', 'callback');

// Thêm vào Users menu
add_users_page('Title', 'Menu Title', 'manage_options', 'slug', 'callback');

// Thêm vào Media menu
add_media_page('Title', 'Menu Title', 'manage_options', 'slug', 'callback');
```

### D. Callback Function Example:

```php
function my_admin_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Handle form submission
    if (isset($_POST['submit'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['my_nonce'], 'my_admin_action')) {
            wp_die('Security check failed');
        }

        // Process form data
        $option_value = sanitize_text_field($_POST['option_field']);
        update_option('my_option', $option_value);

        // Show success message
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }

    // Get current option value
    $current_value = get_option('my_option', '');

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="">
            <?php wp_nonce_field('my_admin_action', 'my_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="option_field">Option Field</label>
                    </th>
                    <td>
                        <input type="text"
                               id="option_field"
                               name="option_field"
                               value="<?php echo esc_attr($current_value); ?>" />
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
```

### E. Capabilities (Quyền truy cập):

```php
'read'                    // Tất cả users đã đăng nhập
'edit_posts'              // Author, Editor, Admin
'edit_published_posts'    // Editor, Admin
'edit_others_posts'       // Editor, Admin
'manage_options'          // Admin only
'manage_categories'       // Editor, Admin
'moderate_comments'       // Editor, Admin
'manage_users'            // Admin only (multisite: super admin)
```

---

## 6. AJAX HANDLING 🔄

### A. Đăng ký AJAX Actions:

```php
// For logged-in users
add_action('wp_ajax_my_action', 'handle_my_ajax_request');

// For non-logged-in users
add_action('wp_ajax_nopriv_my_action', 'handle_my_ajax_request');

function handle_my_ajax_request() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'my_ajax_nonce')) {
        wp_die('Security check failed');
    }

    // Process request
    $data = sanitize_text_field($_POST['data']);

    // Do something with data
    $result = process_data($data);

    // Return response
    if ($result) {
        wp_send_json_success(array('message' => 'Success!', 'data' => $result));
    } else {
        wp_send_json_error(array('message' => 'Error occurred'));
    }

    // Always die in AJAX
    wp_die();
}
```

### B. Frontend AJAX Form:

```html
<form id="my-ajax-form">
  <?php wp_nonce_field('my_ajax_nonce', 'ajax_nonce'); ?>
  <input type="text" name="data_field" id="data_field" />
  <button type="submit">Submit</button>
</form>

<script>
  jQuery(document).ready(function ($) {
    $("#my-ajax-form").on("submit", function (e) {
      e.preventDefault();

      var data = {
        action: "my_action",
        nonce: $("#ajax_nonce").val(),
        data: $("#data_field").val(),
      };

      $.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            alert("Success: " + response.data.message);
          } else {
            alert("Error: " + response.data.message);
          }
        },
      });
    });
  });
</script>
```

### C. Enqueue AJAX Script:

```php
function enqueue_my_ajax_script() {
    wp_enqueue_script('my-ajax-script', 'path/to/script.js', array('jquery'));

    // Localize script với AJAX URL
    wp_localize_script('my-ajax-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('my_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_my_ajax_script');
```

### D. Admin AJAX Form:

```php
function admin_ajax_form() {
    ?>
    <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php wp_nonce_field('my_admin_ajax', 'admin_nonce'); ?>
        <input type="hidden" name="action" value="my_admin_action" />
        <input type="text" name="admin_data" />
        <input type="submit" value="Submit" />
    </form>
    <?php
}
```

---

## 7. DATABASE OPERATIONS 💾

### A. Post Operations:

#### Tạo Post:

```php
$post_data = array(
    'post_title' => 'Post Title',
    'post_content' => 'Post content here',
    'post_status' => 'publish',
    'post_type' => 'post',
    'post_author' => get_current_user_id(),
    'meta_input' => array(
        'meta_key' => 'meta_value',
        'another_key' => 'another_value'
    )
);

$post_id = wp_insert_post($post_data);

if (is_wp_error($post_id)) {
    // Handle error
    error_log('Post creation failed: ' . $post_id->get_error_message());
} else {
    echo 'Post created with ID: ' . $post_id;
}
```

#### Cập nhật Post:

```php
$post_data = array(
    'ID' => $post_id,
    'post_title' => 'New Title',
    'post_content' => 'New content'
);

$result = wp_update_post($post_data);

if (is_wp_error($result)) {
    // Handle error
} else {
    echo 'Post updated successfully';
}
```

#### Query Posts:

```php
$posts = get_posts(array(
    'post_type' => 'product',
    'numberposts' => 10,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => 'featured',
            'value' => 'yes',
            'compare' => '='
        )
    ),
    'tax_query' => array(
        array(
            'taxonomy' => 'product_category',
            'field' => 'slug',
            'terms' => 'electronics'
        )
    ),
    'orderby' => 'date',
    'order' => 'DESC'
));

foreach ($posts as $post) {
    echo $post->post_title;
}
```

#### Xóa Post:

```php
wp_delete_post($post_id, true); // true = force delete (bypass trash)
```

### B. Meta Operations:

#### Thêm/Cập nhật Meta:

```php
// Thêm meta mới
add_post_meta($post_id, 'meta_key', 'meta_value');

// Cập nhật meta (hoặc tạo mới nếu chưa có)
update_post_meta($post_id, 'meta_key', 'new_value');

// Cập nhật với previous value check
update_post_meta($post_id, 'meta_key', 'new_value', 'old_value');
```

#### Lấy Meta:

```php
// Lấy single value
$value = get_post_meta($post_id, 'meta_key', true);

// Lấy all values (array)
$values = get_post_meta($post_id, 'meta_key');

// Lấy tất cả meta của post
$all_meta = get_post_meta($post_id);
```

#### Xóa Meta:

```php
// Xóa tất cả values của key
delete_post_meta($post_id, 'meta_key');

// Xóa specific value
delete_post_meta($post_id, 'meta_key', 'specific_value');
```

### C. Option Operations:

```php
// Thêm option
add_option('option_name', 'default_value');

// Lấy option
$value = get_option('option_name', 'default_value');

// Cập nhật option
update_option('option_name', 'new_value');

// Xóa option
delete_option('option_name');
```

### D. User Meta Operations:

```php
// Thêm/Cập nhật user meta
update_user_meta($user_id, 'meta_key', 'meta_value');

// Lấy user meta
$value = get_user_meta($user_id, 'meta_key', true);

// Xóa user meta
delete_user_meta($user_id, 'meta_key');
```

---

## 8. DATA SANITIZATION & VALIDATION 🛡️

### A. Input Sanitization Functions:

#### Text Fields:

```php
// Text field sanitization
$clean_text = sanitize_text_field($input);         // Loại bỏ HTML, scripts

// Textarea sanitization
$clean_textarea = sanitize_textarea_field($input);  // Giữ line breaks

// HTML sanitization
$clean_html = wp_kses_post($input);                 // Cho phép HTML an toàn

// Email sanitization
$clean_email = sanitize_email($input);

// URL sanitization
$clean_url = esc_url_raw($input);                   // Cho database
$display_url = esc_url($input);                     // Cho hiển thị

// File name sanitization
$clean_filename = sanitize_file_name($input);
```

#### Specific Sanitization:

```php
// Key sanitization (for option names, meta keys)
$clean_key = sanitize_key($input);

// Title sanitization
$clean_title = sanitize_title($input);

// User login sanitization
$clean_user = sanitize_user($input);
```

### B. Output Escaping Functions:

#### HTML Escaping:

```php
// HTML content escaping
echo esc_html($text);                               // Escape HTML entities

// HTML attribute escaping
echo '<input value="' . esc_attr($value) . '" />';

// Textarea content
echo '<textarea>' . esc_textarea($content) . '</textarea>';
```

#### URL Escaping:

```php
// URL escaping
echo '<a href="' . esc_url($url) . '">Link</a>';

// JavaScript escaping
echo '<script>var data = "' . esc_js($data) . '";</script>';
```

### C. Validation Functions:

#### Built-in Validation:

```php
// Email validation
if (is_email($email)) {
    // Valid email
}

// URL validation
if (wp_http_validate_url($url)) {
    // Valid URL
}

// Check if user exists
if (username_exists($username)) {
    // Username exists
}

// Check if email exists
if (email_exists($email)) {
    // Email exists
}
```

#### Custom Validation:

```php
function validate_phone_number($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Check if it's 10 digits
    if (strlen($phone) !== 10) {
        return false;
    }

    return $phone;
}

function validate_required_fields($data, $required_fields) {
    $errors = array();

    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            $errors[] = $field . ' is required';
        }
    }

    return $errors;
}
```

### D. Complete Form Processing Example:

```php
function process_contact_form() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['contact_nonce'], 'contact_form')) {
        wp_die('Security check failed');
    }

    // Sanitize inputs
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $message = sanitize_textarea_field($_POST['message']);

    // Validate inputs
    $errors = array();

    if (empty($name)) {
        $errors[] = 'Name is required';
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = 'Valid email is required';
    }

    if (!empty($phone)) {
        $phone = validate_phone_number($phone);
        if (!$phone) {
            $errors[] = 'Invalid phone number';
        }
    }

    if (empty($message)) {
        $errors[] = 'Message is required';
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Save to database or send email
        $result = save_contact_form($name, $email, $phone, $message);

        if ($result) {
            wp_redirect(add_query_arg('success', '1', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('error', '1', wp_get_referer()));
        }
    } else {
        // Handle errors
        wp_redirect(add_query_arg('errors', urlencode(implode('|', $errors)), wp_get_referer()));
    }

    exit;
}
```

---

## 9. CUSTOM ADMIN COLUMNS 📊

### A. Thêm Custom Columns:

```php
// Hook để thêm columns
add_filter('manage_{post_type}_posts_columns', 'add_custom_columns');

function add_custom_columns($columns) {
    // Thêm column mới
    $columns['custom_field'] = __('Custom Field', 'textdomain');
    $columns['featured'] = __('Featured', 'textdomain');
    $columns['price'] = __('Price', 'textdomain');

    // Sắp xếp lại columns
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Thêm custom column sau title
        if ($key === 'title') {
            $new_columns['custom_field'] = __('Custom Field', 'textdomain');
        }
    }

    return $new_columns;
}
```

### B. Hiển thị nội dung Columns:

```php
// Hook để hiển thị nội dung
add_action('manage_{post_type}_posts_custom_column', 'show_custom_columns', 10, 2);

function show_custom_columns($column, $post_id) {
    switch ($column) {
        case 'custom_field':
            $value = get_post_meta($post_id, 'custom_field', true);
            echo $value ? esc_html($value) : '—';
            break;

        case 'featured':
            $featured = get_post_meta($post_id, 'featured', true);
            if ($featured === 'yes') {
                echo '<span style="color: green;">✓ Yes</span>';
            } else {
                echo '<span style="color: red;">✗ No</span>';
            }
            break;

        case 'price':
            $price = get_post_meta($post_id, 'price', true);
            if ($price) {
                echo '$' . number_format($price, 2);
            } else {
                echo '—';
            }
            break;
    }
}
```

### C. Sortable Columns:

```php
// Làm column có thể sort
add_filter('manage_edit-{post_type}_sortable_columns', 'make_columns_sortable');

function make_columns_sortable($columns) {
    $columns['price'] = 'price';
    $columns['featured'] = 'featured';
    return $columns;
}

// Xử lý query khi sort
add_action('pre_get_posts', 'handle_custom_column_sorting');

function handle_custom_column_sorting($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'price') {
        $query->set('meta_key', 'price');
        $query->set('orderby', 'meta_value_num');
    }

    if ($orderby === 'featured') {
        $query->set('meta_key', 'featured');
        $query->set('orderby', 'meta_value');
    }
}
```

### D. Column Width Styling:

```php
function add_admin_column_styles() {
    echo '<style>
        .column-price { width: 100px; }
        .column-featured { width: 80px; text-align: center; }
        .column-custom_field { width: 150px; }
    </style>';
}
add_action('admin_head', 'add_admin_column_styles');
```

---

## 10. BEST PRACTICES & PATTERNS 🏆

### A. Naming Conventions:

#### Functions:

```php
// Prefix với plugin name
yourplugin_function_name()
yourplugin_ajax_handler()
yourplugin_init()

// Hoặc dùng class
class YourPlugin {
    public function init() {}
    public function ajax_handler() {}
}
```

#### Hooks & Options:

```php
// Hook names
add_action('yourplugin_init', 'callback');
add_filter('yourplugin_content', 'callback');

// Option names
update_option('yourplugin_settings', $data);
get_option('yourplugin_version');

// Meta keys
update_post_meta($id, '_yourplugin_data', $value); // Private meta (starts with _)
update_post_meta($id, 'yourplugin_public_data', $value); // Public meta
```

### B. Code Organization:

#### Plugin Structure:

```php
class YourPlugin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Hooks
        add_action('init', array($this, 'plugin_init'));
        add_action('admin_init', array($this, 'admin_init'));

        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function plugin_init() {
        // Plugin initialization
    }

    public function admin_init() {
        // Admin initialization
    }

    public function activate() {
        // Activation code
        $this->create_tables();
        $this->set_default_options();
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Deactivation code
        flush_rewrite_rules();
    }

    private function create_tables() {
        // Database table creation
    }

    private function set_default_options() {
        // Set default options
    }
}

// Initialize plugin
YourPlugin::get_instance();
```

### C. Error Handling:

#### WordPress Way:

```php
function safe_insert_post($post_data) {
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        error_log('Post creation failed: ' . $post_id->get_error_message());
        return false;
    }

    return $post_id;
}

// Usage
$result = safe_insert_post($post_data);
if (!$result) {
    // Handle error
    wp_die('Failed to create post');
}
```

#### Try-Catch for External APIs:

```php
function call_external_api($url, $data) {
    try {
        $response = wp_remote_post($url, array(
            'body' => $data,
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);

    } catch (Exception $e) {
        error_log('API call failed: ' . $e->getMessage());
        return false;
    }
}
```

### D. Security Best Practices:

#### Always Use Nonces:

```php
// Generate nonce
wp_nonce_field('my_action', 'my_nonce');

// Verify nonce
if (!wp_verify_nonce($_POST['my_nonce'], 'my_action')) {
    wp_die('Security check failed');
}
```

#### Capability Checks:

```php
// Check user permissions
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

// Check for specific post
if (!current_user_can('edit_post', $post_id)) {
    wp_die('Access denied');
}
```

#### Sanitize & Escape:

```php
// Input sanitization
$input = sanitize_text_field($_POST['field']);

// Output escaping
echo esc_html($output);
echo '<a href="' . esc_url($url) . '">' . esc_html($text) . '</a>';
```

### E. Performance Tips:

#### Caching:

```php
function get_expensive_data($param) {
    $cache_key = 'expensive_data_' . md5($param);
    $data = get_transient($cache_key);

    if (false === $data) {
        // Expensive operation
        $data = perform_expensive_operation($param);

        // Cache for 1 hour
        set_transient($cache_key, $data, HOUR_IN_SECONDS);
    }

    return $data;
}
```

#### Conditional Loading:

```php
// Only load admin scripts on admin pages
function load_admin_scripts($hook) {
    if ('toplevel_page_my-plugin' !== $hook) {
        return;
    }

    wp_enqueue_script('my-admin-script', 'script.js');
}
add_action('admin_enqueue_scripts', 'load_admin_scripts');
```

---

## 11. WORDPRESS PLUGIN DEVELOPMENT FLOW 🔄

### A. Development Workflow:

#### 1. Planning Phase:

```
□ Xác định chức năng cần thiết
□ Research existing plugins
□ Thiết kế database schema (nếu cần)
□ Lên wireframe cho admin pages
□ Chọn naming convention
```

#### 2. Setup Phase:

```
□ Tạo plugin folder
□ Tạo main plugin file với header
□ Setup security checks
□ Tạo plugin class structure
□ Setup activation/deactivation hooks
```

#### 3. Development Phase:

```
□ Implement core functionality
□ Tạo custom post types (nếu cần)
□ Tạo admin pages & menus
□ Implement AJAX handlers
□ Add database operations
□ Setup form processing
```

#### 4. Enhancement Phase:

```
□ Add custom admin columns
□ Implement caching (nếu cần)
□ Add user permissions
□ Create shortcodes (nếu cần)
□ Add frontend functionality
```

#### 5. Security & Validation:

```
□ Add nonce verification
□ Implement data sanitization
□ Add output escaping
□ Test user permissions
□ Validate all inputs
```

#### 6. Testing Phase:

```
□ Test trên môi trường dev
□ Test với different user roles
□ Test edge cases
□ Test plugin conflicts
□ Performance testing
```

#### 7. Documentation:

```
□ Viết inline comments
□ Tạo README file
□ Document hooks & filters
□ Create user guide
□ Write changelog
```

### B. File Organization:

```
my-plugin/
├── my-plugin.php              (Main file)
├── uninstall.php             (Uninstall cleanup)
├── readme.txt                (WordPress.org readme)
├── includes/
│   ├── class-main.php        (Main plugin class)
│   ├── class-admin.php       (Admin functionality)
│   ├── class-frontend.php    (Frontend functionality)
│   ├── class-ajax.php        (AJAX handlers)
│   └── functions.php         (Helper functions)
├── admin/
│   ├── partials/             (Admin page templates)
│   └── js/                   (Admin JavaScript)
├── public/
│   ├── css/                  (Frontend CSS)
│   └── js/                   (Frontend JavaScript)
├── languages/                (Translation files)
└── assets/                   (Images, icons)
```

### C. Version Control Best Practices:

```bash
# .gitignore for WordPress plugin
node_modules/
*.log
.DS_Store
Thumbs.db
wp-config.php
/.env
```

### D. Release Checklist:

```
□ Update version number in main file
□ Update changelog
□ Test on fresh WordPress install
□ Check compatibility with latest WP version
□ Validate code with WordPress standards
□ Create release notes
□ Tag version in git
□ Submit to WordPress.org (if applicable)
```

---

## 12. QUICK REFERENCE 📚

### A. Essential Functions:

#### Plugin Basics:

```php
plugin_dir_path(__FILE__)     // Plugin directory path
plugin_dir_url(__FILE__)      // Plugin directory URL
plugin_basename(__FILE__)     // Plugin basename
```

#### Hooks:

```php
add_action($hook, $function, $priority, $args)
add_filter($hook, $function, $priority, $args)
remove_action($hook, $function, $priority)
remove_filter($hook, $function, $priority)
do_action($hook, $arg1, $arg2, ...)
apply_filters($hook, $value, $arg1, $arg2, ...)
```

#### Database:

```php
// Posts
wp_insert_post($postarr)
wp_update_post($postarr)
wp_delete_post($postid, $force_delete)
get_posts($args)

// Meta
update_post_meta($post_id, $meta_key, $meta_value)
get_post_meta($post_id, $meta_key, $single)
delete_post_meta($post_id, $meta_key, $meta_value)

// Options
update_option($option, $value)
get_option($option, $default)
delete_option($option)
```

### B. Common Hooks:

#### Initialization:

- `init` - WordPress initialized
- `admin_init` - Admin initialized
- `wp_loaded` - WordPress fully loaded

#### Admin:

- `admin_menu` - Add admin menus
- `admin_enqueue_scripts` - Enqueue admin scripts
- `add_meta_boxes` - Add meta boxes

#### Frontend:

- `wp_enqueue_scripts` - Enqueue frontend scripts
- `wp_head` - Add to <head>
- `wp_footer` - Add to footer

#### Content:

- `the_content` - Filter post content
- `the_title` - Filter post title
- `wp_title` - Filter page title

### C. Security Functions:

```php
// Nonces
wp_nonce_field($action, $name)
wp_verify_nonce($nonce, $action)
wp_create_nonce($action)

// Sanitization
sanitize_text_field($str)
sanitize_textarea_field($str)
sanitize_email($email)
esc_url_raw($url)

// Escaping
esc_html($text)
esc_attr($text)
esc_url($url)
esc_js($text)

// Capabilities
current_user_can($capability)
user_can($user, $capability)
```

### D. Debugging:

```php
// Enable debugging in wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Debug functions
error_log('Debug message');
var_dump($variable);
wp_die('Stop execution');

// Query debugging
define('SAVEQUERIES', true);
global $wpdb;
print_r($wpdb->queries);
```

---

## 📝 NOTES & REMINDERS

### Remember These Rules:

1. **Always prefix** functions, classes, và variables
2. **Security first** - nonce, sanitize, escape
3. **Use WordPress functions** thay vì raw PHP/SQL
4. **Follow WordPress coding standards**
5. **Test thoroughly** với different scenarios
6. **Document your code** với comments
7. **Handle errors gracefully**
8. **Check user capabilities**

### Common Mistakes to Avoid:

- ❌ Forgetting security checks
- ❌ Not sanitizing inputs
- ❌ Not escaping outputs
- ❌ Direct database queries
- ❌ Not checking user permissions
- ❌ Hardcoding paths/URLs
- ❌ Not handling errors
- ❌ Poor naming conventions

### Performance Tips:

- ✅ Use transients for caching
- ✅ Load scripts only when needed
- ✅ Optimize database queries
- ✅ Use WordPress object cache
- ✅ Minimize HTTP requests
- ✅ Compress images and assets

---

_Happy WordPress Plugin Development! 🚀_
