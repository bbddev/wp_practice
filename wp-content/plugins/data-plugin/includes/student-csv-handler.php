<?php
/**
 * Student CSV Export/Import Handler for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle Student CSV export
 */
function bb_data_plugin_export_student_csv()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_student_export')) {
        wp_die('Security check failed');
    }

    // Get all students
    $students = get_posts(array(
        'post_type' => 'student',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    // Set headers for CSV download
    $filename = 'exported-students-' . date('Y-m-d-H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, array('student_username', 'student_password', 'student_link', 'student_image'));

    // Export student data
    foreach ($students as $student) {
        $username = get_post_meta($student->ID, 'student_username', true);
        $password = get_post_meta($student->ID, 'student_password', true);
        $link = get_post_meta($student->ID, 'student_link', true);
        $image = get_post_meta($student->ID, 'student_image', true);

        fputcsv($output, array($username, $password, $link, $image));
    }

    fclose($output);
    exit();
}

/**
 * Handle Student batch CSV import - Initialize
 */
function bb_data_plugin_init_student_batch_import()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_student_batch_import')) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Security check failed']));
    }

    // Validate file upload
    if (empty($_FILES['student_file']['name'])) {
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

    if (!in_array($_FILES['student_file']['type'], $csvMimes)) {
        wp_die(json_encode(['status' => 'error', 'message' => 'Please select a valid CSV file.']));
    }

    // Read and parse CSV file
    $csvFile = fopen($_FILES['student_file']['tmp_name'], 'r');
    $headers = fgetcsv($csvFile); // Skip header line

    $csvData = array();
    $lineNumber = 1;

    while (($line = fgetcsv($csvFile)) !== FALSE) {
        $csvData[] = array(
            'line_number' => $lineNumber++,
            'data' => $line
        );
    }
    fclose($csvFile);

    $totalRecords = count($csvData);
    $batchSize = $totalRecords > 1000 ? 25 : ($totalRecords > 500 ? 50 : 100);
    $totalBatches = ceil($totalRecords / $batchSize);

    // Store data in session for batch processing
    $sessionKey = 'bb_student_batch_import_' . uniqid();
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

/**
 * Handle Student batch CSV import - Process single batch
 */
function bb_data_plugin_process_student_batch()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_student_batch_import')) {
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
            bb_data_process_student_csv_line($csvData[$i]['data'], $counters);
        }
    }

    // Update session data
    $_SESSION[$sessionKey]['current_batch'] = $batchNumber + 1;
    $_SESSION[$sessionKey]['processed_records'] = $endIndex;
    $_SESSION[$sessionKey]['counters'] = $counters;

    $isComplete = $endIndex >= count($csvData);

    if ($isComplete) {
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

/**
 * Process single student CSV line
 */
function bb_data_process_student_csv_line($line, &$counters)
{
    if (count($line) < 1) {
        $counters['skipped']++;
        return;
    }

    $username = isset($line[0]) ? trim($line[0]) : '';
    $password = isset($line[1]) ? trim($line[1]) : '';
    $link = isset($line[2]) ? trim($line[2]) : '';
    $image = isset($line[3]) ? trim($line[3]) : '';

    if (empty($username)) {
        $counters['skipped']++;
        return;
    }

    // Check if student with same username already exists
    $existing_post = get_posts(array(
        'post_type' => 'student',
        'meta_query' => array(
            array(
                'key' => 'student_username',
                'value' => $username,
                'compare' => '='
            )
        ),
        'numberposts' => 1,
        'post_status' => array('publish', 'draft')
    ));

    if ($existing_post) {
        // Update existing student
        $post_id = $existing_post[0]->ID;
        bb_data_update_student_meta($post_id, $username, $password, $link, $image);
        $counters['updated']++;
    } else {
        // Create new student
        $post_id = wp_insert_post(array(
            'post_title' => $username, // Use username as title
            'post_type' => 'student',
            'post_status' => 'publish'
        ));

        if ($post_id) {
            bb_data_update_student_meta($post_id, $username, $password, $link, $image);
            $counters['created']++;
        } else {
            $counters['skipped']++;
        }
    }

    $counters['imported']++;
}

/**
 * Update student meta fields
 */
function bb_data_update_student_meta($post_id, $username, $password, $link, $image)
{
    update_post_meta($post_id, 'student_username', $username);
    if (!empty($password)) {
        update_post_meta($post_id, 'student_password', $password);
    }
    if (!empty($link)) {
        update_post_meta($post_id, 'student_link', $link);
    }
    if (!empty($image)) {
        update_post_meta($post_id, 'student_image', $image);
    }
}