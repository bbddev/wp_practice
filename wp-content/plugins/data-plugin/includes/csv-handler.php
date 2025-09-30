<?php
/**
 * CSV Export/Import Handler for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle CSV export
 */
function bb_data_plugin_export_csv_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export')) {
        wp_die('Security check failed');
    }

    // Get all data from the three post types
    $schools = bb_data_get_posts_by_type('school');
    $classes = bb_data_get_posts_by_type('class');
    $entities = bb_data_get_posts_by_type('entity');

    // Set headers for CSV download
    bb_data_set_csv_headers();

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, array('type', 'title', 'password', 'parent', 'link', 'image_url'));

    // Export data
    bb_data_export_schools_to_csv($output, $schools);
    bb_data_export_classes_to_csv($output, $classes);
    bb_data_export_entities_to_csv($output, $entities);

    fclose($output);
    exit();
}
add_action('wp_ajax_export_csv_data_posts', 'bb_data_plugin_export_csv_posts');

/**
 * Handle CSV import
 */
function bb_data_plugin_import_csv_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import')) {
        wp_die('Security check failed');
    }

    $res_status = $res_msg = '';

    if (isset($_POST['importSubmit'])) {
        $result = bb_data_process_csv_upload();
        $res_status = $result['status'];
        $res_msg = $result['message'];

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
add_action('wp_ajax_import_csv_data_posts', 'bb_data_plugin_import_csv_posts');

/**
 * Get posts by type
 */
function bb_data_get_posts_by_type($post_type)
{
    return get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'publish'
    ));
}

/**
 * Set CSV headers for download
 */
function bb_data_set_csv_headers()
{
    $filename = 'exported-data-' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
}

/**
 * Export schools to CSV
 */
function bb_data_export_schools_to_csv($output, $schools)
{
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
}

/**
 * Export classes to CSV
 */
function bb_data_export_classes_to_csv($output, $classes)
{
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
}

/**
 * Export entities to CSV
 */
function bb_data_export_entities_to_csv($output, $entities)
{
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
}

/**
 * Process CSV upload
 */
function bb_data_process_csv_upload()
{
    // Allowed mime types
    $csvMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel'
    );

    // Validate file
    if (empty($_FILES['file']['name']) || !in_array($_FILES['file']['type'], $csvMimes)) {
        return array('status' => 'danger', 'message' => 'Please select a valid CSV file.');
    }

    if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
        return array('status' => 'danger', 'message' => 'Something went wrong, please try again.');
    }

    // Process CSV file
    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
    fgetcsv($csvFile); // Skip header line

    $counters = array(
        'imported' => 0,
        'updated' => 0,
        'created' => 0,
        'skipped' => 0
    );

    while (($line = fgetcsv($csvFile)) !== FALSE) {
        bb_data_process_csv_line($line, $counters);
    }

    fclose($csvFile);

    $message = "Import hoàn tất! Tổng cộng: {$counters['imported']} dòng đã import thành công " .
        "(Tạo mới: {$counters['created']}, Cập nhật: {$counters['updated']}, Bỏ qua: {$counters['skipped']}).";

    return array('status' => 'success', 'message' => $message);
}

/**
 * Process single CSV line
 */
function bb_data_process_csv_line($line, &$counters)
{
    if (count($line) < 2) {
        $counters['skipped']++;
        return;
    }

    $type = trim($line[0]);
    $title = trim($line[1]);
    $password = isset($line[2]) ? trim($line[2]) : '';
    $parent = isset($line[3]) ? trim($line[3]) : '';
    $link = isset($line[4]) ? trim($line[4]) : '';
    $image_url = isset($line[5]) ? trim($line[5]) : '';

    if (empty($title) || !in_array($type, array('school', 'class', 'entity'))) {
        $counters['skipped']++;
        return;
    }

    // Check if post exists
    $existing_post = get_page_by_title($title, OBJECT, $type);

    if ($existing_post) {
        bb_data_update_existing_post($existing_post, $type, $password, $parent, $link, $image_url, $counters);
    } else {
        bb_data_create_new_post($type, $title, $password, $parent, $link, $image_url, $counters);
    }

    $counters['imported']++;
}

/**
 * Update existing post
 */
function bb_data_update_existing_post($post, $type, $password, $parent, $link, $image_url, &$counters)
{
    bb_data_update_post_meta($post->ID, $type, $password, $parent, $link, $image_url);
    $counters['updated']++;
}

/**
 * Create new post
 */
function bb_data_create_new_post($type, $title, $password, $parent, $link, $image_url, &$counters)
{
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_type' => $type,
        'post_status' => 'publish'
    ));

    if ($post_id) {
        bb_data_update_post_meta($post_id, $type, $password, $parent, $link, $image_url);
        $counters['created']++;
    } else {
        $counters['skipped']++;
    }
}

/**
 * Update post meta based on type
 */
function bb_data_update_post_meta($post_id, $type, $password, $parent, $link, $image_url)
{
    switch ($type) {
        case 'class':
            if (!empty($password)) {
                update_post_meta($post_id, 'class_password', $password);
            }
            if (!empty($parent)) {
                update_post_meta($post_id, 'Thuộc Trường', $parent);
            }
            break;

        case 'entity':
            if (!empty($password)) {
                update_post_meta($post_id, 'lesson_password', $password);
            }
            if (!empty($parent)) {
                update_post_meta($post_id, 'Thuộc lớp', $parent);
            }
            if (!empty($link)) {
                update_post_meta($post_id, 'Link khi click', $link);
            }
            if (!empty($image_url)) {
                update_post_meta($post_id, 'Hình', $image_url);
            }
            break;
    }
}