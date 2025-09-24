<?php
/**
 * Plugin Name: My CRUD Plugin
 * Description: A simple CRUD plugin for WordPress.
 * Version: 1.0
 * Author: Binh Vo
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('admin_menu', 'my_crud_plugin_menu');
function my_crud_plugin_menu()
{
    add_menu_page(
        //page title
        'CRUD System',
        //menu title
        'CRUD Plugin',
        //capability
        'manage_options',
        //menu slug
        'my-crud-plugin',
        //callback function
        'my_crud_plugin_admin_page',
        //icon
        'dashicons-database',
        //position
        26
    );
}
function my_crud_plugin_admin_page()
{
    global $wpdb;
    //for delete code
    $table_name = $wpdb->prefix . 'my_crud_data';
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $deleted = $wpdb->delete($table_name, ['id' => $id]);
        if ($deleted) {
            echo '<div class="updated"><p>Record deleted successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Error deleting record.</p></div>';
        }
    }
    // Handle form submission
    if (isset($_POST['my_crud_submit'])) {
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $wpdb->insert(
            $table_name,
            [
                'name' => $name,
                'email' => $email,
            ]
        );
        echo '<div class="updated"><p>Data added successfully!</p></div>';
    }

    // Handle update submission
    if (isset($_POST['my_crud_update'])) {
        $id = intval($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);

        $updated = $wpdb->update(
            $table_name,
            ['name' => $name, 'email' => $email],
            ['id' => $id]
        );
        if ($updated !== false) {
            echo '<div class="updated"><p>Record updated successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Error updating record.</p></div>';
        }
    }

    //Check if editing
    $editing = false;
    $edit_id = 0;
    $edit_name = '';
    $edit_email = '';

    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $edit_id = intval($_GET['id']);
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $edit_id");
        if ($row) {
            $editing = true;
            $edit_name = esc_attr($row->name);
            $edit_email = esc_attr($row->email);
        }
    }

    ?>

    <div class="wrap">
        <h1><?php echo $editing ? 'Edit Record' : 'My CRUD Plugin'; ?></h1>
        <form action="" method="post">
            <table class="form-data">
                <tr>
                    <th>
                        <label for="name">Name</label>
                    </th>
                    <td>
                        <input type="text" id="name" name="name" required value="<?php echo $edit_name; ?>">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="email">Email</label>
                    </th>
                    <td>
                        <input type="email" id="email" name="email" required value="<?php echo $edit_email; ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if ($editing): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                            <input type="submit" name="my_crud_update" value="Update Record" class="button button-primary">
                            <a href="?page=my-crud-plugin" class="button">Cancel</a>
                        <?php else: ?>
                            <input type="submit" name="my_crud_submit" value="Add Record" class="button button-primary">
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
    // Fetch data in ASC Order
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}my_crud_data ORDER BY id ASC");
    if ($results) {
        echo '<h2> Saved Records</h2>';
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th><th>Actions</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->create_at) . '</td>';
            echo '<td>';
            echo '<a href="?page=my-crud-plugin&action=edit&id=' . $row->id . '" class="button">Edit</a>';
            echo '<a href="?page=my-crud-plugin&action=delete&id=' . $row->id . '" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete this record?\');">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    }
}

register_activation_hook(__FILE__, 'my_crud_plugin_create_table');

function my_crud_plugin_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'my_crud_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        create_at datetime DEFAULT CURRENT_TIMESTAMP,        
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
