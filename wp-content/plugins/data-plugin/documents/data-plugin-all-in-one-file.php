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

function bb_data_plugin_register_post_type()
{
    // Register School Post Type
    register_post_type('school', array(
        'labels' => array(
            'name' => 'Schools',
            'singular_name' => 'School',
            'add_new' => 'Add New School',
            'add_new_item' => 'Add New School',
            'edit_item' => 'Edit School',
            'new_item' => 'New School',
            'view_item' => 'View School',
            'search_items' => 'Search Schools',
            'not_found' => 'No Schools found',
            'not_found_in_trash' => 'No Schools found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-building'
    ));

    // Register Class Post Type
    register_post_type('class', array(
        'labels' => array(
            'name' => 'Classes',
            'singular_name' => 'Class',
            'add_new' => 'Add New Class',
            'add_new_item' => 'Add New Class',
            'edit_item' => 'Edit Class',
            'new_item' => 'New Class',
            'view_item' => 'View Class',
            'search_items' => 'Search Classes',
            'not_found' => 'No Classes found',
            'not_found_in_trash' => 'No Classes found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-groups'
    ));

    // Register Entity Post Type
    register_post_type('entity', array(
        'labels' => array(
            'name' => 'Entities',
            'singular_name' => 'Entity',
            'add_new' => 'Add New Entity',
            'add_new_item' => 'Add New Entity',
            'edit_item' => 'Edit Entity',
            'new_item' => 'New Entity',
            'view_item' => 'View Entity',
            'search_items' => 'Search Entities',
            'not_found' => 'No Entities found',
            'not_found_in_trash' => 'No Entities found in Trash'
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'has_archive' => false,
        'rewrite' => false,
        'menu_icon' => 'dashicons-media-document'
    ));
}

// Start session if not already started
add_action('init', 'bb_data_plugin_start_session');
function bb_data_plugin_start_session()
{
    if (!session_id()) {
        session_start();
    }
}

// Handle JSON export using wp_posts
add_action('wp_ajax_export_json_data_posts', 'bb_data_plugin_export_json_posts');
function bb_data_plugin_export_json_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export_json')) {
        wp_die('Security check failed');
    }

    // Get all data from the three post types
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

// Handle JSON import using wp_posts
add_action('wp_ajax_import_json_data_posts', 'bb_data_plugin_import_json_posts');
function bb_data_plugin_import_json_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import_json')) {
        wp_die('Security check failed');
    }

    $res_status = $res_msg = '';

    if (isset($_POST['importJsonSubmit'])) {
        // Allowed mime types for JSON
        $jsonMimes = array('application/json', 'text/json', 'text/plain', 'application/octet-stream');

        // Validate whether selected file is a JSON file
        if (!empty($_FILES['json_file']['name']) && in_array($_FILES['json_file']['type'], $jsonMimes)) {

            // If the file is uploaded
            if (is_uploaded_file($_FILES['json_file']['tmp_name'])) {

                // Read JSON file content
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
                    // Initialize counters
                    $total_imported = 0;
                    $total_updated = 0;
                    $total_created = 0;
                    $total_skipped = 0;

                    // Import schools
                    if (isset($jsonData['schools']) && is_array($jsonData['schools'])) {
                        foreach ($jsonData['schools'] as $school) {
                            if (!isset($school['title']) || empty($school['title'])) {
                                $total_skipped++;
                                continue;
                            }

                            $title = sanitize_text_field($school['title']);

                            // Check if school already exists
                            $existing_posts = get_posts(array(
                                'post_type' => 'school',
                                'title' => $title,
                                'post_status' => array('publish', 'draft'),
                                'numberposts' => 1
                            ));

                            if ($existing_posts) {
                                // Update existing
                                $post_id = $existing_posts[0]->ID;
                                wp_update_post(array(
                                    'ID' => $post_id,
                                    'post_title' => $title,
                                    'post_status' => 'publish'
                                ));
                                $total_updated++;
                            } else {
                                // Create new
                                $post_id = wp_insert_post(array(
                                    'post_title' => $title,
                                    'post_type' => 'school',
                                    'post_status' => 'publish'
                                ));
                                if ($post_id && !is_wp_error($post_id)) {
                                    $total_created++;
                                }
                            }
                            $total_imported++;
                        }
                    }

                    // Import classes
                    if (isset($jsonData['classes']) && is_array($jsonData['classes'])) {
                        foreach ($jsonData['classes'] as $class) {
                            if (!isset($class['title']) || empty($class['title'])) {
                                $total_skipped++;
                                continue;
                            }

                            $title = sanitize_text_field($class['title']);
                            $password = isset($class['password']) ? sanitize_text_field($class['password']) : '';
                            $parent = isset($class['parent_school']) ? sanitize_text_field($class['parent_school']) : '';

                            // Check if class already exists
                            $existing_posts = get_posts(array(
                                'post_type' => 'class',
                                'title' => $title,
                                'post_status' => array('publish', 'draft'),
                                'numberposts' => 1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'Thuộc Trường',
                                        'value' => $parent,
                                        'compare' => '='
                                    )
                                )
                            ));

                            $meta_input = array();
                            if ($password)
                                $meta_input['class_password'] = $password;
                            if ($parent)
                                $meta_input['Thuộc Trường'] = $parent;

                            if ($existing_posts) {
                                // Update existing
                                $post_id = $existing_posts[0]->ID;
                                wp_update_post(array(
                                    'ID' => $post_id,
                                    'post_title' => $title,
                                    'post_status' => 'publish'
                                ));
                                foreach ($meta_input as $key => $value) {
                                    update_post_meta($post_id, $key, $value);
                                }
                                $total_updated++;
                            } else {
                                // Create new
                                $post_id = wp_insert_post(array(
                                    'post_title' => $title,
                                    'post_type' => 'class',
                                    'post_status' => 'publish',
                                    'meta_input' => $meta_input
                                ));
                                if ($post_id && !is_wp_error($post_id)) {
                                    $total_created++;
                                }
                            }
                            $total_imported++;
                        }
                    }

                    // Import entities
                    if (isset($jsonData['entities']) && is_array($jsonData['entities'])) {
                        foreach ($jsonData['entities'] as $entity) {
                            if (!isset($entity['title']) || empty($entity['title'])) {
                                $total_skipped++;
                                continue;
                            }

                            $title = sanitize_text_field($entity['title']);
                            $password = isset($entity['password']) ? sanitize_text_field($entity['password']) : '';
                            $parent = isset($entity['parent_class']) ? sanitize_text_field($entity['parent_class']) : '';
                            $link = isset($entity['link']) ? esc_url_raw($entity['link']) : '';
                            $image_url = isset($entity['image_url']) ? esc_url_raw($entity['image_url']) : '';

                            // Check if entity already exists
                            $existing_posts = get_posts(array(
                                'post_type' => 'entity',
                                'title' => $title,
                                'post_status' => array('publish', 'draft'),
                                'numberposts' => 1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'Thuộc lớp',
                                        'value' => $parent,
                                        'compare' => '='
                                    )
                                )
                            ));

                            $meta_input = array();
                            if ($password)
                                $meta_input['lesson_password'] = $password;
                            if ($parent)
                                $meta_input['Thuộc lớp'] = $parent;
                            if ($link)
                                $meta_input['Link khi click'] = $link;
                            if ($image_url)
                                $meta_input['Hình'] = $image_url;

                            if ($existing_posts) {
                                // Update existing
                                $post_id = $existing_posts[0]->ID;
                                wp_update_post(array(
                                    'ID' => $post_id,
                                    'post_title' => $title,
                                    'post_status' => 'publish'
                                ));
                                foreach ($meta_input as $key => $value) {
                                    update_post_meta($post_id, $key, $value);
                                }
                                $total_updated++;
                            } else {
                                // Create new
                                $post_id = wp_insert_post(array(
                                    'post_title' => $title,
                                    'post_type' => 'entity',
                                    'post_status' => 'publish',
                                    'meta_input' => $meta_input
                                ));
                                if ($post_id && !is_wp_error($post_id)) {
                                    $total_created++;
                                }
                            }
                            $total_imported++;
                        }
                    }

                    $res_status = 'success';
                    $res_msg = "JSON Import hoàn tất! Tổng cộng: {$total_imported} dòng đã import thành công (Tạo mới: {$total_created}, Cập nhật: {$total_updated}, Bỏ qua: {$total_skipped}).";
                }
            } else {
                $res_status = 'danger';
                $res_msg = 'Something went wrong, please try again.';
            }
        } else {
            $res_status = 'danger';
            $res_msg = 'Please select a valid JSON file.';
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

// Handle CSV export using wp_posts
add_action('wp_ajax_export_csv_data_posts', 'bb_data_plugin_export_csv_posts');
function bb_data_plugin_export_csv_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export')) {
        wp_die('Security check failed');
    }

    // Get all data from the three post types
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

    // Set headers for CSV download
    $filename = 'exported-data-' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, array('type', 'title', 'password', 'parent', 'link', 'image_url'));

    // Export schools
    foreach ($schools as $school) {
        fputcsv($output, array(
            'school',
            $school->post_title,
            '', // Schools don't have passwords
            '', // Schools don't have parents
            '', // Schools don't have links
            ''  // Schools don't have images
        ));
    }

    // Export classes
    foreach ($classes as $class) {
        $password = get_post_meta($class->ID, 'class_password', true);
        $parent = get_post_meta($class->ID, 'Thuộc Trường', true);

        fputcsv($output, array(
            'class',
            $class->post_title,
            $password,
            $parent,
            '', // Classes don't have links
            ''  // Classes don't have images
        ));
    }

    // Export entities
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
    exit();
}

// Handle CSV import using wp_posts
add_action('wp_ajax_import_csv_data_posts', 'bb_data_plugin_import_csv_posts');
function bb_data_plugin_import_csv_posts()
{
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

                // Initialize counters
                $total_imported = 0;
                $total_updated = 0;
                $total_created = 0;
                $total_skipped = 0;

                // Parse data from CSV file line by line
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    $line_arr = !empty($line) ? array_filter($line) : '';
                    if (!empty($line_arr)) {
                        // Get row data
                        $type = sanitize_text_field(trim($line_arr[0]));
                        $title = sanitize_text_field(trim($line_arr[1]));
                        $password = !empty($line_arr[2]) ? sanitize_text_field(trim($line_arr[2])) : '';
                        $parent = !empty($line_arr[3]) ? sanitize_text_field(trim($line_arr[3])) : '';
                        $link = !empty($line_arr[4]) ? esc_url_raw(trim($line_arr[4])) : '';
                        $image_url = !empty($line_arr[5]) ? esc_url_raw(trim($line_arr[5])) : '';

                        // Validate type
                        if (!in_array($type, array('school', 'class', 'entity'))) {
                            $total_skipped++;
                            continue; // Skip invalid types
                        }

                        // Check if post with same title, type and parent already exists
                        $existing_posts = get_posts(array(
                            'post_type' => $type,
                            'title' => $title,
                            'post_status' => array('publish', 'draft'),
                            'numberposts' => 1,
                            'meta_query' => array(
                                array(
                                    'key' => $type === 'class' ? 'Thuộc Trường' : ($type === 'entity' ? 'Thuộc lớp' : ''),
                                    'value' => $parent,
                                    'compare' => '='
                                )
                            )
                        ));

                        // For school type or when no parent, use simple check
                        if ($type === 'school' || empty($parent)) {
                            $existing_posts = get_posts(array(
                                'post_type' => $type,
                                'title' => $title,
                                'post_status' => array('publish', 'draft'),
                                'numberposts' => 1
                            ));
                        }

                        $meta_input = array();

                        // Add fields based on type
                        if ($type === 'school') {
                            // School: only type and title
                        } elseif ($type === 'class') {
                            // Class: type, title, password, parent (Thuộc Trường)
                            if ($password)
                                $meta_input['class_password'] = $password;
                            if ($parent)
                                $meta_input['Thuộc Trường'] = $parent;
                        } elseif ($type === 'entity') {
                            // Entity: type, title, password, parent (Thuộc lớp), link, image_url
                            if ($password)
                                $meta_input['lesson_password'] = $password;
                            if ($parent)
                                $meta_input['Thuộc lớp'] = $parent;
                            if ($link)
                                $meta_input['Link khi click'] = $link;
                            if ($image_url)
                                $meta_input['Hình'] = $image_url;
                        }

                        if ($existing_posts) {
                            // Update existing post
                            $post_id = $existing_posts[0]->ID;
                            wp_update_post(array(
                                'ID' => $post_id,
                                'post_title' => $title,
                                'post_status' => 'publish'
                            ));

                            // Update meta fields
                            foreach ($meta_input as $key => $value) {
                                update_post_meta($post_id, $key, $value);
                            }

                            $total_updated++;
                            $total_imported++;
                        } else {
                            // Create new post
                            $post_data = array(
                                'post_title' => $title,
                                'post_type' => $type,
                                'post_status' => 'publish',
                                'meta_input' => $meta_input
                            );

                            $post_id = wp_insert_post($post_data);
                            if ($post_id && !is_wp_error($post_id)) {
                                $total_created++;
                                $total_imported++;
                            }
                        }
                    }
                }

                // Close opened CSV file
                fclose($csvFile);

                $res_status = 'success';
                $res_msg = "CSV Import hoàn tất! Tổng cộng: {$total_imported} dòng đã import thành công (Tạo mới: {$total_created}, Cập nhật: {$total_updated}, Bỏ qua: {$total_skipped}).";
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
function bb_data_plugin_posts_menu()
{
    add_menu_page(
        'Import Data',
        'Import Data',
        'manage_options',
        'my-data-plugin-posts',
        'bb_data_plugin_posts_admin_page',
        'dashicons-database-view',
        27
    );
}

function bb_data_plugin_posts_admin_page()
{
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
        #importFrm { margin-bottom: 20px; }
        .badge { display: inline-block; min-width: 10px; padding: 3px 7px; font-size: 12px; font-weight: bold; color: #fff; line-height: 1; vertical-align: baseline; white-space: nowrap; text-align: center; border-radius: 10px; }
        .badge-success { background-color: #5cb85c; }
        .badge-warning { background-color: #f0ad4e; }
        .badge-info { background-color: #0073aa; }
    </style>';
    ?>
    <div class="wrap">
        <h1>Import Data</h1>

        <!-- <div class="notice notice-info">
            <p><strong>Info:</strong> Dữ liệu được lưu ở table wp_posts và wp_postmeta.</p>
        </div> -->

        <?php if (!empty($statusMsg)) { ?>
            <div class="alert alert-<?php echo esc_attr($status); ?>">
                <?php echo esc_html($statusMsg); ?>
            </div>
        <?php } ?>

        <div class="row">
            <!-- Import link -->
            <!-- <div class="col-md-12 head" style="margin-bottom: 20px;">
                <div class="float-end">
                    <a href="<?php echo admin_url('edit.php?post_type=school'); ?>" class="btn btn-primary"
                        style="margin-right: 5px;">
                        View Schools
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=class'); ?>" class="btn btn-primary"
                        style="margin-right: 5px;">
                        View Classes
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=entity'); ?>" class="btn btn-primary">
                        View Entities
                    </a>
                </div>
                <div style="clear: both;"></div>
            </div> -->

            <!-- CSV file upload form -->
            <div class="col-md-12" id="importFrm"
                style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                <div class="col-md-6 head">
                    <div class="float-end">
                        <a href="<?php echo admin_url('edit.php?post_type=school'); ?>" class="btn btn-primary"
                            style="margin-right: 5px;">
                            View Schools
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=class'); ?>" class="btn btn-primary"
                            style="margin-right: 5px;">
                            View Classes
                        </a>
                        <a href="<?php echo admin_url('edit.php?post_type=entity'); ?>" class="btn btn-primary"
                            style="margin-right: 5px;">
                            View Entities
                        </a>
                        <button type="button" class="btn btn-success" onclick="exportData();" style="margin-right: 5px;">
                            Export CSV
                        </button>
                        <button type="button" class="btn btn-info" onclick="exportJsonData();" style="margin-right: 5px;">
                            Export JSON
                        </button>
                    </div>
                </div>
                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data" class="col-md-6">
                    <?php wp_nonce_field('bb_data_import', 'bb_data_nonce'); ?>
                    <input type="hidden" name="action" value="import_csv_data_posts">

                    <div style="margin-bottom: 15px;">
                        <input type="file" name="file" id="csv_file" required accept=".csv" style="margin-top: 5px;">

                        <p style="margin: 10px 0 0 0; font-size: 12px;">
                            <em>CSV format: type, title, password, parent, link, image_url</em> |
                            <em>type: school, class, entity</em><br>
                        </p>
                        <p style="margin: 10px 0 0 0; font-size: 12px;">

                            <a href="#" onclick="downloadSample();" style="color: #0073aa;">Download Sample Format</a>
                        </p>
                    </div>

                    <div>
                        <input type="submit" class="btn btn-primary" name="importSubmit" value="Import CSV">
                    </div>
                </form>

                <!-- JSON file upload form -->
                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data" class="col-md-6" style="margin-top: 20px;">
                    <?php wp_nonce_field('bb_data_import_json', 'bb_data_nonce'); ?>
                    <input type="hidden" name="action" value="import_json_data_posts">

                    <div style="margin-bottom: 15px;">
                        <input type="file" name="json_file" id="json_file" required accept=".json" style="margin-top: 5px;">

                        <p style="margin: 10px 0 0 0; font-size: 12px;">
                            <em>JSON format: structured data với schools, classes, entities</em><br>
                        </p>
                        <p style="margin: 10px 0 0 0; font-size: 12px;">
                            <a href="#" onclick="downloadJsonSample();" style="color: #0073aa;">Download JSON Sample
                                Format</a>
                        </p>
                    </div>

                    <div>
                        <input type="submit" class="btn btn-info" name="importJsonSubmit" value="Import JSON">
                    </div>
                </form>
            </div>
            <!-- JavaScript functions -->
            <script>
                function downloadSample() {
                    var csvContent = "type,title,password,parent,link,image_url\nschool,Field 1,,,,\nclass,Class 1 - Field 1,123456,Field 1,,\nentity,Lesson 1,password123,Class 1,https://example.com,http://localhost/wp_practice/wp-content/uploads/2025/09/lesson1.png";
                    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    var link = document.createElement("a");
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", "sample-data.csv");
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                function downloadJsonSample() {
                    var jsonContent = {
                        "export_info": {
                            "export_date": "2025-09-30 14:30:15",
                            "plugin_version": "1.0",
                            "total_records": 3
                        },
                        "schools": [
                            {
                                "id": 1,
                                "title": "Sample School",
                                "type": "school",
                                "created_date": "2025-09-30 10:00:00"
                            }
                        ],
                        "classes": [
                            {
                                "id": 2,
                                "title": "Sample Class",
                                "type": "class",
                                "password": "123456",
                                "parent_school": "Sample School",
                                "created_date": "2025-09-30 11:00:00"
                            }
                        ],
                        "entities": [
                            {
                                "id": 3,
                                "title": "Sample Entity",
                                "type": "entity",
                                "password": "password123",
                                "parent_class": "Sample Class",
                                "link": "https://example.com",
                                "image_url": "https://example.com/image.jpg",
                                "created_date": "2025-09-30 12:00:00"
                            }
                        ]
                    };

                    var jsonString = JSON.stringify(jsonContent, null, 2);
                    var blob = new Blob([jsonString], { type: 'application/json;charset=utf-8;' });
                    var link = document.createElement("a");
                    var url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", "sample-data.json");
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                function exportData() {
                    // Show loading message
                    var exportBtn = event.target;
                    var originalText = exportBtn.innerHTML;
                    exportBtn.innerHTML = 'Exporting...';
                    exportBtn.disabled = true;

                    // Create a form to submit export request
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
                    form.style.display = 'none';

                    // Add nonce field
                    var nonceField = document.createElement('input');
                    nonceField.type = 'hidden';
                    nonceField.name = 'bb_data_nonce';
                    nonceField.value = '<?php echo wp_create_nonce('bb_data_export'); ?>';
                    form.appendChild(nonceField);

                    // Add action field
                    var actionField = document.createElement('input');
                    actionField.type = 'hidden';
                    actionField.name = 'action';
                    actionField.value = 'export_csv_data_posts';
                    form.appendChild(actionField);

                    // Add form to body and submit
                    document.body.appendChild(form);
                    form.submit();

                    // Reset button after a short delay
                    setTimeout(function () {
                        exportBtn.innerHTML = originalText;
                        exportBtn.disabled = false;
                        document.body.removeChild(form);
                    }, 2000);
                }

                function exportJsonData() {
                    // Show loading message
                    var exportBtn = event.target;
                    var originalText = exportBtn.innerHTML;
                    exportBtn.innerHTML = 'Exporting...';
                    exportBtn.disabled = true;

                    // Create a form to submit export request
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
                    form.style.display = 'none';

                    // Add nonce field
                    var nonceField = document.createElement('input');
                    nonceField.type = 'hidden';
                    nonceField.name = 'bb_data_nonce';
                    nonceField.value = '<?php echo wp_create_nonce('bb_data_export_json'); ?>';
                    form.appendChild(nonceField);

                    // Add action field
                    var actionField = document.createElement('input');
                    actionField.type = 'hidden';
                    actionField.name = 'action';
                    actionField.value = 'export_json_data_posts';
                    form.appendChild(actionField);

                    // Add form to body and submit
                    document.body.appendChild(form);
                    form.submit();

                    // Reset button after a short delay
                    setTimeout(function () {
                        exportBtn.innerHTML = originalText;
                        exportBtn.disabled = false;
                        document.body.removeChild(form);
                    }, 2000);
                }
            </script>
            <?php
}

// Add custom columns to post list in admin for all 3 post types
// add_filter('manage_school_posts_columns', 'bb_data_custom_columns');
add_filter('manage_class_posts_columns', 'bb_data_custom_columns');
add_filter('manage_entity_posts_columns', 'bb_data_custom_columns');

function bb_data_custom_columns($columns)
{
    // Lấy post type hiện tại từ URL hoặc global
    global $typenow;

    if ($typenow === 'class') {
        $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
    } elseif ($typenow === 'entity') {
        $columns['csv_password'] = 'Password';
        $columns['csv_parent'] = 'Parent';
        $columns['csv_link'] = 'Link';
        $columns['csv_image'] = 'Image';
    }
    return $columns;
}

// add_action('manage_school_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
add_action('manage_class_posts_custom_column', 'bb_data_custom_column_content', 10, 2);
add_action('manage_entity_posts_custom_column', 'bb_data_custom_column_content', 10, 2);

function bb_data_custom_column_content($column, $post_id)
{
    $post_type = get_post_type($post_id);

    switch ($column) {
        case 'csv_password':
            if ($post_type === 'class') {
                $password = get_post_meta($post_id, 'class_password', true);
            } elseif ($post_type === 'entity') {
                $password = get_post_meta($post_id, 'lesson_password', true);
            } else {
                $password = '';
            }
            echo $password ? '****' : '-';
            break;
        case 'csv_parent':
            if ($post_type === 'class') {
                $parent = get_post_meta($post_id, 'Thuộc Trường', true);
            } elseif ($post_type === 'entity') {
                $parent = get_post_meta($post_id, 'Thuộc lớp', true);
            } else {
                $parent = '';
            }
            echo esc_html($parent ?: '-');
            break;
        case 'csv_link':
            if ($post_type === 'entity') {
                $link = get_post_meta($post_id, 'Link khi click', true);
                if ($link) {
                    echo '<a href="' . esc_url($link) . '" target="_blank" style="color: #0073aa;">View Link</a>';
                } else {
                    echo '-';
                }
            } else {
                echo '-';
            }
            break;
        case 'csv_image':
            if ($post_type === 'entity') {
                $image_url = get_post_meta($post_id, 'Hình', true);
                if ($image_url) {
                    echo '<a href="' . esc_url($image_url) . '" target="_blank" style="color: #0073aa;">View Image</a>';
                } else {
                    echo '-';
                }
            } else {
                echo '-';
            }
            break;
    }
}