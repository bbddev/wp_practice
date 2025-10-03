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
// add_action('wp_ajax_export_csv_data_posts', 'bb_data_plugin_export_csv_posts');

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
// add_action('wp_ajax_import_csv_data_posts', 'bb_data_plugin_import_csv_posts');

/**
 * Handle batch CSV import - Initialize
 */
function bb_data_plugin_init_batch_import()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_batch_import')) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Security check failed']));
    }

    // Validate file upload
    if (empty($_FILES['file']['name'])) {
        wp_die(json_encode(['status' => 'error', 'message' => 'No file uploaded']));
    }

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

    if (!in_array($_FILES['file']['type'], $csvMimes)) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Please select a valid CSV file.']));
    }

    // Read and parse CSV file
    $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
    $headers = fgetcsv($csvFile); // Skip header line

    $csvData = array();
    $lineNumber = 1; // Start from 1 since we skip header

    while (($line = fgetcsv($csvFile)) !== FALSE) {
        $csvData[] = array(
            'line_number' => $lineNumber++,
            'data' => $line
        );
    }
    fclose($csvFile);

    $totalRecords = count($csvData);
    // Dynamic batch size based on total records
    $batchSize = $totalRecords > 1000 ? 25 : ($totalRecords > 500 ? 50 : 100);
    $totalBatches = ceil($totalRecords / $batchSize);

    // Store data in session for batch processing
    $sessionKey = 'bb_batch_import_' . uniqid();
    $_SESSION[$sessionKey] = array(
        'csv_data' => $csvData,
        'total_records' => $totalRecords,
        'batch_size' => $batchSize,
        'total_batches' => $totalBatches,
        'current_batch' => 0,
        'processed_records' => 0,
        'counters' => array(
            'imported' => 0,
            'updated' => 0,
            'created' => 0,
            'skipped' => 0
        )
    );

    wp_die(json_encode([
        'status' => 'success',
        'session_key' => $sessionKey,
        'total_records' => $totalRecords,
        'total_batches' => $totalBatches,
        'batch_size' => $batchSize
    ]));
}
// add_action('wp_ajax_init_batch_csv_import', 'bb_data_plugin_init_batch_import');

/**
 * Handle batch CSV import - Process single batch
 */
function bb_data_plugin_process_batch()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_batch_import')) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Security check failed']));
    }

    $sessionKey = sanitize_text_field($_POST['session_key']);
    $batchNumber = intval($_POST['batch_number']);

    if (!isset($_SESSION[$sessionKey])) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Session expired. Please restart import.']));
    }

    $sessionData = $_SESSION[$sessionKey];
    $csvData = $sessionData['csv_data'];
    $batchSize = $sessionData['batch_size'];
    $counters = $sessionData['counters'];

    // Calculate batch range
    $startIndex = $batchNumber * $batchSize;
    $endIndex = min($startIndex + $batchSize, count($csvData));

    // Process batch
    for ($i = $startIndex; $i < $endIndex; $i++) {
        if (isset($csvData[$i])) {
            bb_data_process_csv_line($csvData[$i]['data'], $counters);
        }
    }

    // Update session data
    $_SESSION[$sessionKey]['current_batch'] = $batchNumber + 1;
    $_SESSION[$sessionKey]['processed_records'] = $endIndex;
    $_SESSION[$sessionKey]['counters'] = $counters;

    $isComplete = $endIndex >= count($csvData);

    if ($isComplete) {
        // Clean up session data
        unset($_SESSION[$sessionKey]);
    }

    wp_die(json_encode([
        'status' => 'success',
        'batch_number' => $batchNumber + 1,
        'processed_records' => $endIndex,
        'total_records' => count($csvData),
        'counters' => $counters,
        'is_complete' => $isComplete,
        'progress_percent' => round(($endIndex / count($csvData)) * 100, 2)
    ]));
}
// add_action('wp_ajax_process_batch_csv_import', 'bb_data_plugin_process_batch');

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
    $username = isset($line[6]) ? trim($line[6]) : '';

    if (empty($title) || !in_array($type, array('school', 'class', 'entity'))) {
        $counters['skipped']++;
        return;
    }

    // // Check if post exists
    // $existing_post = get_page_by_title($title, OBJECT, $type);

    // Check if post with same title, type and parent already exists
    $existing_post = get_posts(array(
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
        $existing_post = get_posts(array(
            'post_type' => $type,
            'title' => $title,
            'post_status' => array('publish', 'draft'),
            'numberposts' => 1
        ));
    }


    if ($existing_post) {
        bb_data_update_existing_post($existing_post, $type, $password, $parent, $link, $image_url, $username, $counters);
    } else {
        bb_data_create_new_post($type, $title, $password, $parent, $link, $image_url, $username, $counters);
    }

    $counters['imported']++;
}

/**
 * Update existing post
 */
function bb_data_update_existing_post($post, $type, $password, $parent, $link, $image_url, $username, &$counters)
{
    bb_data_update_post_meta($post->ID, $type, $password, $parent, $link, $image_url, $username);
    $counters['updated']++;
}

/**
 * Create new post
 */
function bb_data_create_new_post($type, $title, $password, $parent, $link, $image_url, $username, &$counters)
{
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_type' => $type,
        'post_status' => 'publish'
    ));

    if ($post_id) {
        bb_data_update_post_meta($post_id, $type, $password, $parent, $link, $image_url, $username);
        $counters['created']++;
    } else {
        $counters['skipped']++;
    }
}

/**
 * Update post meta based on type
 */
function bb_data_update_post_meta($post_id, $type, $password, $parent, $link, $image_url, $username)
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
            if (!empty($username)) {
                update_post_meta($post_id, 'Username', $username);
            }
            break;
    }
}