<?php
/**
 * Plugin Name: BB - Data Plugin (Posts Version)
 * Description: A simple data plugin using WordPress posts table.
 * Version: 1.0
 * Author: Binh Vo
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register Custom Post Type on plugin activation
register_activation_hook(__FILE__, 'bb_data_plugin_register_post_type');
add_action('init', 'bb_data_plugin_register_post_type');

function bb_data_plugin_register_post_type() {
    register_post_type('bb_data', array(
        'labels' => array(
            'name' => 'BB Data',
            'singular_name' => 'BB Data Item',
            'add_new' => 'Add New Data',
            'add_new_item' => 'Add New BB Data',
            'edit_item' => 'Edit BB Data',
            'new_item' => 'New BB Data',
            'view_item' => 'View BB Data',
            'search_items' => 'Search BB Data',
            'not_found' => 'No BB Data found',
            'not_found_in_trash' => 'No BB Data found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, // Chúng ta sẽ tạo menu riêng
        'capability_type' => 'post',
        'supports' => array('title', 'custom-fields'),
        'has_archive' => false,
        'rewrite' => false
    ));
}

// Start session if not already started
add_action('init', 'bb_data_plugin_start_session');
function bb_data_plugin_start_session() {
    if (!session_id()) {
        session_start();
    }
}

// Handle CSV import using wp_posts
add_action('wp_ajax_import_csv_data_posts', 'bb_data_plugin_import_csv_posts');
function bb_data_plugin_import_csv_posts() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import')) {
        wp_die('Security check failed');
    }

    $res_status = $res_msg = '';

    if (isset($_POST['importSubmit'])) {
        // Allowed mime types
        $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel');

        // Validate whether selected file is a CSV file
        if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {

            // If the file is uploaded
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {

                // Open uploaded CSV file with read-only mode
                $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

                // Skip the first line
                fgetcsv($csvFile);

                // Parse data from CSV file line by line
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    $line_arr = !empty($line) ? array_filter($line) : '';
                    if (!empty($line_arr)) {
                        // Get row data
                        $name = sanitize_text_field(trim($line_arr[0]));
                        $email = sanitize_email(trim($line_arr[1]));
                        $phone = sanitize_text_field(trim($line_arr[2]));
                        $status = intval(trim($line_arr[3]));

                        // Check if post with same email already exists
                        $existing_posts = get_posts(array(
                            'post_type' => 'bb_data',
                            'meta_key' => 'bb_email',
                            'meta_value' => $email,
                            'post_status' => array('publish', 'draft'),
                            'numberposts' => 1
                        ));

                        if ($existing_posts) {
                            // Update existing post
                            $post_id = $existing_posts[0]->ID;
                            wp_update_post(array(
                                'ID' => $post_id,
                                'post_title' => $name,
                                'post_status' => $status ? 'publish' : 'draft'
                            ));
                            
                            // Update meta fields
                            update_post_meta($post_id, 'bb_email', $email);
                            update_post_meta($post_id, 'bb_phone', $phone);
                            update_post_meta($post_id, 'bb_status', $status);
                        } else {
                            // Create new post
                            $post_data = array(
                                'post_title' => $name,
                                'post_type' => 'bb_data',
                                'post_status' => $status ? 'publish' : 'draft',
                                'meta_input' => array(
                                    'bb_email' => $email,
                                    'bb_phone' => $phone,
                                    'bb_status' => $status
                                )
                            );
                            
                            wp_insert_post($post_data);
                        }
                    }
                }

                // Close opened CSV file
                fclose($csvFile);

                $res_status = 'success';
                $res_msg = 'Data has been imported successfully using wp_posts table.';
            } else {
                $res_status = 'danger';
                $res_msg = 'Something went wrong, please try again.';
            }
        } else {
            $res_status = 'danger';
            $res_msg = 'Please select a valid CSV file.';
        }

        // Store status in SESSION
        $_SESSION['response'] = array(
            'status' => $res_status,
            'msg' => $res_msg
        );
    }

    // Redirect back to the admin page
    wp_redirect(admin_url('admin.php?page=my-data-plugin-posts'));
    exit();
}

// Add admin menu
add_action('admin_menu', 'bb_data_plugin_posts_menu');
function bb_data_plugin_posts_menu() {
    add_menu_page(
        'BB Data (Posts)',
        'BB Data (Posts)',
        'manage_options',
        'my-data-plugin-posts',
        'bb_data_plugin_posts_admin_page',
        'dashicons-database-view',
        27
    );
}

function bb_data_plugin_posts_admin_page() {
    if (!empty($_SESSION['response'])) {
        $status = $_SESSION['response']['status'];
        $statusMsg = $_SESSION['response']['msg'];
        unset($_SESSION['response']);
    }

    // Add CSS for styling
    echo '<style>
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .btn { display: inline-block; padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: 400; line-height: 1.42857143; text-align: center; white-space: nowrap; vertical-align: middle; cursor: pointer; border: 1px solid transparent; border-radius: 4px; text-decoration: none; }
        .btn-primary { color: #fff; background-color: #337ab7; border-color: #2e6da4; }
        .btn-success { color: #fff; background-color: #5cb85c; border-color: #4cae4c; }
        .btn-danger { color: #fff; background-color: #d9534f; border-color: #d43f3a; }
        .float-end { float: right; }
        .table { width: 100%; max-width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
        .table-bordered { border: 1px solid #ddd; }
        .table-bordered th, .table-bordered td { border: 1px solid #ddd; }
        .table th, .table td { padding: 8px; line-height: 1.42857143; vertical-align: top; }
        .table-dark { background-color: #333; color: #fff; }
        #importFrm { margin-bottom: 20px; }
        .badge { display: inline-block; min-width: 10px; padding: 3px 7px; font-size: 12px; font-weight: bold; color: #fff; line-height: 1; vertical-align: baseline; white-space: nowrap; text-align: center; border-radius: 10px; }
        .badge-success { background-color: #5cb85c; }
        .badge-warning { background-color: #f0ad4e; }
    </style>';
    ?>
    <div class="wrap">
        <h1>Import Data using WordPress Posts Table</h1>
        
        <div class="notice notice-info">
            <p><strong>Lưu ý:</strong> Plugin này sử dụng bảng wp_posts thay vì tạo bảng mới. Dữ liệu được lưu dưới dạng Custom Post Type 'bb_data'.</p>
        </div>

        <?php if (!empty($statusMsg)) { ?>
            <div class="alert alert-<?php echo esc_attr($status); ?>">
                <?php echo esc_html($statusMsg); ?>
            </div>
        <?php } ?>

        <div class="row">
            <!-- Import link -->
            <div class="col-md-12 head" style="margin-bottom: 20px;">
                <div class="float-end">
                    <a href="javascript:void(0);" class="btn btn-primary" onclick="formToggle('importFrm');">
                        <i class="plus"></i> Import CSV to Posts Table
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=bb_data'); ?>" class="btn btn-success">
                        View in Posts Admin
                    </a>
                </div>
                <div style="clear: both;"></div>
            </div>

            <!-- CSV file upload form -->
            <div class="col-md-12" id="importFrm" style="display: none;">
                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data"
                    style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <?php wp_nonce_field('bb_data_import', 'bb_data_nonce'); ?>
                    <input type="hidden" name="action" value="import_csv_data_posts">

                    <div style="margin-bottom: 15px;">
                        <label for="csv_file"><strong>Select CSV File:</strong></label><br>
                        <input type="file" name="file" id="csv_file" required accept=".csv" style="margin-top: 5px;">

                        <p style="margin: 10px 0 0 0; font-size: 12px;">
                            <em>CSV format: Name, Email, Phone, Status (1 for Active, 0 for Inactive)</em><br>
                            <a href="#" onclick="downloadSample();" style="color: #0073aa;">Download Sample Format</a>
                        </p>
                    </div>

                    <div>
                        <input type="submit" class="btn btn-success" name="importSubmit" value="Import to Posts Table">
                        <button type="button" class="btn" onclick="formToggle('importFrm');"
                            style="margin-left: 10px;">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Data list table -->
            <h3>Data from wp_posts table (Custom Post Type: bb_data)</h3>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Post ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Post Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from wp_posts with post_type = 'bb_data'
                    $posts = get_posts(array(
                        'post_type' => 'bb_data',
                        'post_status' => array('publish', 'draft'),
                        'numberposts' => -1,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($posts) {
                        foreach ($posts as $post) {
                            $email = get_post_meta($post->ID, 'bb_email', true);
                            $phone = get_post_meta($post->ID, 'bb_phone', true);
                            $bb_status = get_post_meta($post->ID, 'bb_status', true);
                            ?>
                            <tr>
                                <td><?php echo esc_html('#' . $post->ID); ?></td>
                                <td><?php echo esc_html($post->post_title); ?></td>
                                <td><?php echo esc_html($email); ?></td>
                                <td><?php echo esc_html($phone); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $bb_status == 1 ? 'success' : 'warning'; ?>">
                                        <?php echo $bb_status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html(date('Y-m-d H:i', strtotime($post->post_date))); ?></td>
                                <td>
                                    <span style="color: <?php echo $post->post_status == 'publish' ? 'green' : 'orange'; ?>">
                                        <?php echo ucfirst($post->post_status); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo get_edit_post_link($post->ID); ?>" class="btn btn-primary" style="font-size: 12px; padding: 3px 8px;">Edit</a>
                                    <a href="<?php echo get_delete_post_link($post->ID, '', true); ?>" class="btn btn-danger" style="font-size: 12px; padding: 3px 8px;" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #666;">No data found in wp_posts table...</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding: 15px; background: #f0f0f1; border-radius: 5px;">
                <h4>Thông tin kỹ thuật:</h4>
                <ul>
                    <li><strong>Bảng sử dụng:</strong> wp_posts (thay vì tạo bảng mới)</li>
                    <li><strong>Post Type:</strong> bb_data</li>
                    <li><strong>Meta fields:</strong> bb_email, bb_phone, bb_status</li>
                    <li><strong>Ưu điểm:</strong> Tận dụng hệ thống có sẵn của WordPress, dễ quản lý, có sẵn các chức năng CRUD</li>
                    <li><strong>Nhược điểm:</strong> Có thể chậm hơn với lượng dữ liệu lớn, phụ thuộc vào cấu trúc WordPress</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- JavaScript functions -->
    <script>
        function formToggle(ID) {
            var element = document.getElementById(ID);
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        }

        function downloadSample() {
            var csvContent = "Name,Email,Phone,Status\nJohn Doe,john@example.com,123-456-7890,1\nJane Smith,jane@example.com,098-765-4321,0";
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "sample-bbdatas-posts.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <?php
}

// Add custom columns to post list in admin
add_filter('manage_bb_data_posts_columns', 'bb_data_custom_columns');
function bb_data_custom_columns($columns) {
    $columns['bb_email'] = 'Email';
    $columns['bb_phone'] = 'Phone';
    $columns['bb_status'] = 'Status';
    return $columns;
}

add_action('manage_bb_data_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
function bb_data_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'bb_email':
            echo esc_html(get_post_meta($post_id, 'bb_email', true));
            break;
        case 'bb_phone':
            echo esc_html(get_post_meta($post_id, 'bb_phone', true));
            break;
        case 'bb_status':
            $status = get_post_meta($post_id, 'bb_status', true);
            echo '<span style="color: ' . ($status == 1 ? 'green' : 'red') . '">';
            echo $status == 1 ? 'Active' : 'Inactive';
            echo '</span>';
            break;
    }
}