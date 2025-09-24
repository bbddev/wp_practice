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
    $table_details = $wpdb->prefix . 'product_details';

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

    dbDelta("CREATE TABLE $table_details (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_id mediumint(9) NOT NULL,
        size varchar(50) NOT NULL,
        quantity int NOT NULL,
        image_url varchar(255) DEFAULT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (product_id) REFERENCES $table_products(id) ON DELETE CASCADE
    ) $charset_collate;");
}

// Trang quản trị
function pc_crud_admin_page()
{
    global $wpdb;
    $table_products = $wpdb->prefix . 'products';
    $table_categories = $wpdb->prefix . 'categories';

    // Xóa category
    if (isset($_GET['delete_category'])) {
        $cat_id = intval($_GET['delete_category']);
        $wpdb->delete($table_categories, ['id' => $cat_id]);
        echo '<div class="updated"><p>Xóa category thành công!</p></div>';
    }

    // Xóa product
    if (isset($_GET['delete_product'])) {
        $prod_id = intval($_GET['delete_product']);
        $wpdb->delete($table_products, ['id' => $prod_id]);
        echo '<div class="updated"><p>Xóa product thành công!</p></div>';
    }

    // Update category
    if (isset($_POST['update_category'])) {
        $cat_id = intval($_POST['cat_id']);
        $cat_name = sanitize_text_field($_POST['cat_name']);
        $wpdb->update($table_categories, ['name' => $cat_name], ['id' => $cat_id]);
        echo '<div class="updated"><p>Cập nhật category thành công!</p></div>';
    }

    // Update product
    if (isset($_POST['update_product'])) {
        $prod_id = intval($_POST['prod_id']);
        $name = sanitize_text_field($_POST['prod_name']);
        $price = floatval($_POST['prod_price']);
        $cat_id = intval($_POST['prod_category']);
        $wpdb->update($table_products, [
            'name' => $name,
            'price' => $price,
            'category_id' => $cat_id
        ], ['id' => $prod_id]);
        echo '<div class="updated"><p>Cập nhật product thành công!</p></div>';
    }

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
    $categories = $wpdb->get_results("SELECT * FROM $table_categories ORDER BY id ASC");

    // Hiển thị form edit category nếu có
    $edit_cat = false;
    if (isset($_GET['edit_category'])) {
        $edit_cat_id = intval($_GET['edit_category']);
        $edit_cat = $wpdb->get_row("SELECT * FROM $table_categories WHERE id = $edit_cat_id");
    }

    // Form thêm hoặc sửa category
    echo '<h2>' . ($edit_cat ? 'Sửa Category' : 'Thêm Category') . '</h2>';
    echo '<form method="post">';
    if ($edit_cat) {
        echo '<input type="hidden" name="cat_id" value="' . esc_attr($edit_cat->id) . '">';
        echo '<input type="text" name="cat_name" required value="' . esc_attr($edit_cat->name) . '" placeholder="Category name">';
        echo '<input type="submit" name="update_category" value="Cập nhật Category" class="button button-primary">';
        echo '<a href="?page=pc-crud" class="button">Hủy</a>';
    } else {
        echo '<input type="text" name="cat_name" required placeholder="Category name">';
        echo '<input type="submit" name="add_category" value="Thêm Category" class="button button-primary">';
    }
    echo '</form>';

    // Hiển thị danh sách categories
    echo '<h2>Danh sách Categories</h2>';
    echo '<table class="widefat striped">
        <thead><tr><th>ID</th><th>Name</th><th>Created At</th><th>Actions</th></tr></thead><tbody>';
    foreach ($categories as $cat) {
        echo '<tr>
            <td>' . esc_html($cat->id) . '</td>
            <td>' . esc_html($cat->name) . '</td>
            <td>' . esc_html($cat->created_at) . '</td>
            <td>
                <a href="?page=pc-crud&edit_category=' . $cat->id . '" class="button">Sửa</a> '
            . '<a href="?page=pc-crud&delete_category=' . $cat->id . '" class="button button-danger" onclick="return confirm(\'Xóa category này?\');">Xóa</a>' . '
            </td>
        </tr>';
    }
    echo '</tbody></table>';

    // Hiển thị form edit product nếu có
    $edit_prod = false;
    if (isset($_GET['edit_product'])) {
        $edit_prod_id = intval($_GET['edit_product']);
        $edit_prod = $wpdb->get_row("SELECT * FROM $table_products WHERE id = $edit_prod_id");
    }

    // Form thêm hoặc sửa product
    echo '<h2>' . ($edit_prod ? 'Sửa Product' : 'Thêm Product') . '</h2>';
    echo '<form method="post">';
    if ($edit_prod) {
        echo '<input type="hidden" name="prod_id" value="' . esc_attr($edit_prod->id) . '">';
        echo '<input type="text" name="prod_name" required value="' . esc_attr($edit_prod->name) . '" placeholder="Product name">';
        echo '<input type="number" step="0.01" name="prod_price" required value="' . esc_attr($edit_prod->price) . '" placeholder="Price">';
        echo '<select name="prod_category" required>';
        foreach ($categories as $cat) {
            $selected = ($edit_prod->category_id == $cat->id) ? 'selected' : '';
            echo '<option value="' . esc_attr($cat->id) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="update_product" value="Cập nhật Product" class="button button-primary">';
        echo '<a href="?page=pc-crud" class="button">Hủy</a>';
    } else {
        echo '<input type="text" name="prod_name" required placeholder="Product name">';
        echo '<input type="number" step="0.01" name="prod_price" required placeholder="Price">';
        echo '<select name="prod_category" required>';
        echo '<option value="">Chọn Category</option>';
        foreach ($categories as $cat) {
            echo '<option value="' . esc_attr($cat->id) . '">' . esc_html($cat->name) . '</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="add_product" value="Thêm Product" class="button button-primary">';
    }
    echo '</form>';

    // Hiển thị danh sách products
    $products = $wpdb->get_results("SELECT p.*, c.name as category_name FROM $table_products p LEFT JOIN $table_categories c ON p.category_id = c.id ORDER BY p.id DESC");
    echo '<h2>Danh sách Products</h2>';
    echo '<table class="widefat striped">
        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Created At</th><th>Actions</th></tr></thead><tbody>';
    foreach ($products as $prod) {
        echo '<tr>
            <td>' . esc_html($prod->id) . '</td>
            <td>' . esc_html($prod->name) . '</td>
            <td>' . esc_html($prod->price) . '</td>
            <td>' . esc_html($prod->category_name) . '</td>
            <td>' . esc_html($prod->created_at) . '</td>
            <td>
                <a href="?page=pc-crud&edit_product=' . $prod->id . '" class="button">Sửa</a> '
            . '<a href="?page=pc-crud&delete_product=' . $prod->id . '" class="button button-danger" onclick="return confirm(\'Xóa product này?\');">Xóa</a>' . '
            </td>
        </tr>';
    }
    echo '</tbody></table>';
}