<?php
/*
Plugin Name: Product & Category CRUD
Description: CRUD với 2 bảng có relationship: products và categories.
Version: 1.0
Author: GitHub Copilot
*/

if (!defined('ABSPATH'))
    exit;

// Tạo menu
add_action('admin_menu', 'pc_crud_menu');
function pc_crud_menu()
{
    add_menu_page('Product & Category CRUD', 'Product CRUD', 'manage_options', 'pc-crud', 'pc_crud_admin_page', 'dashicons-products', 27);
}

// Tạo bảng khi kích hoạt
register_activation_hook(__FILE__, 'pc_crud_create_tables');
function pc_crud_create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_products = $wpdb->prefix . 'products';
    $table_categories = $wpdb->prefix . 'categories';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta("CREATE TABLE $table_categories (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;");

    dbDelta("CREATE TABLE $table_products (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        price float NOT NULL,
        category_id mediumint(9) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (category_id) REFERENCES $table_categories(id) ON DELETE CASCADE
    ) $charset_collate;");
}

// Trang quản trị
function pc_crud_admin_page()
{
    global $wpdb;
    $table_products = $wpdb->prefix . 'products';
    $table_categories = $wpdb->prefix . 'categories';

    // Thêm category
    if (isset($_POST['add_category'])) {
        $cat_name = sanitize_text_field($_POST['cat_name']);
        $wpdb->insert($table_categories, ['name' => $cat_name]);
        echo '<div class="updated"><p>Thêm category thành công!</p></div>';
    }

    // Thêm product
    if (isset($_POST['add_product'])) {
        $name = sanitize_text_field($_POST['prod_name']);
        $price = floatval($_POST['prod_price']);
        $cat_id = intval($_POST['prod_category']);
        $wpdb->insert($table_products, [
            'name' => $name,
            'price' => $price,
            'category_id' => $cat_id
        ]);
        echo '<div class="updated"><p>Thêm product thành công!</p></div>';
    }

    // Lấy danh sách categories
    $categories = $wpdb->get_results("SELECT * FROM $table_categories ORDER BY name ASC");

    // Form thêm category
    echo '<h2>Thêm Category</h2>
    <form method="post">
        <input type="text" name="cat_name" required placeholder="Category name">
        <input type="submit" name="add_category" value="Thêm Category" class="button button-primary">
    </form>';

    // Form thêm product
    echo '<h2>Thêm Product</h2>
    <form method="post">
        <input type="text" name="prod_name" required placeholder="Product name">
        <input type="number" step="0.01" name="prod_price" required placeholder="Price">
        <select name="prod_category" required>
            <option value="">Chọn Category</option>';
    foreach ($categories as $cat) {
        echo '<option value="' . esc_attr($cat->id) . '">' . esc_html($cat->name) . '</option>';
    }
    echo '</select>
        <input type="submit" name="add_product" value="Thêm Product" class="button button-primary">
    </form>';

    // Hiển thị danh sách products
    $products = $wpdb->get_results("SELECT p.*, c.name as category_name FROM $table_products p LEFT JOIN $table_categories c ON p.category_id = c.id ORDER BY p.id DESC");
    echo '<h2>Danh sách Products</h2>
    <table class="widefat striped">
        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Created At</th></tr></thead><tbody>';
    foreach ($products as $prod) {
        echo '<tr>
            <td>' . esc_html($prod->id) . '</td>
            <td>' . esc_html($prod->name) . '</td>
            <td>' . esc_html($prod->price) . '</td>
            <td>' . esc_html($prod->category_name) . '</td>
            <td>' . esc_html($prod->created_at) . '</td>
        </tr>';
    }
    echo '</tbody></table>';
}