<?php
/**
 * JSON Export/Import Handler for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Handle JSON export
 */
function bb_data_plugin_export_json_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_export_json')) {
        wp_die('Security check failed');
    }

    // Get all data from the three post types
    $schools = bb_data_get_posts_by_type('school');
    $classes = bb_data_get_posts_by_type('class');
    $entities = bb_data_get_posts_by_type('entity');

    // Build structured data array
    $export_data = bb_data_build_json_export_data($schools, $classes, $entities);

    // Set headers for JSON download
    bb_data_set_json_headers();

    // Output JSON data
    echo wp_json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}
// add_action('wp_ajax_export_json_data_posts', 'bb_data_plugin_export_json_posts');

/**
 * Handle JSON import
 */
function bb_data_plugin_import_json_posts()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['bb_data_nonce'], 'bb_data_import_json')) {
        wp_die('Security check failed');
    }

    $res_status = $res_msg = '';

    if (isset($_POST['importJsonSubmit'])) {
        $result = bb_data_process_json_upload();
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
// add_action('wp_ajax_import_json_data_posts', 'bb_data_plugin_import_json_posts');

/**
 * Build JSON export data structure
 */
function bb_data_build_json_export_data($schools, $classes, $entities)
{
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
        $export_data['schools'][] = bb_data_format_school_for_export($school);
    }

    // Process classes
    foreach ($classes as $class) {
        $export_data['classes'][] = bb_data_format_class_for_export($class);
    }

    // Process entities
    foreach ($entities as $entity) {
        $export_data['entities'][] = bb_data_format_entity_for_export($entity);
    }

    return $export_data;
}

/**
 * Format school data for export
 */
function bb_data_format_school_for_export($school)
{
    return array(
        'id' => $school->ID,
        'title' => $school->post_title,
        'type' => 'school',
        'created_date' => $school->post_date
    );
}

/**
 * Format class data for export
 */
function bb_data_format_class_for_export($class)
{
    $password = get_post_meta($class->ID, 'class_password', true);
    $parent = get_post_meta($class->ID, 'Thuộc Trường', true);

    return array(
        'id' => $class->ID,
        'title' => $class->post_title,
        'type' => 'class',
        'password' => $password,
        'parent_school' => $parent,
        'created_date' => $class->post_date
    );
}

/**
 * Format entity data for export
 */
function bb_data_format_entity_for_export($entity)
{
    $password = get_post_meta($entity->ID, 'lesson_password', true);
    $parent = get_post_meta($entity->ID, 'Thuộc lớp', true);
    $link = get_post_meta($entity->ID, 'Link khi click', true);
    $image_url = get_post_meta($entity->ID, 'Hình', true);
    $username = get_post_meta($entity->ID, 'Username', true);

    return array(
        'id' => $entity->ID,
        'title' => $entity->post_title,
        'type' => 'entity',
        'password' => $password,
        'parent_class' => $parent,
        'link' => $link,
        'image_url' => $image_url,
        'username' => $username,
        'created_date' => $entity->post_date
    );
}

/**
 * Set JSON headers for download
 */
function bb_data_set_json_headers()
{
    $filename = 'exported-data-' . date('Y-m-d-H-i-s') . '.json';
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');
}

/**
 * Process JSON upload
 */
function bb_data_process_json_upload()
{
    // Allowed mime types for JSON
    $jsonMimes = array('application/json', 'text/json', 'text/plain', 'application/octet-stream');

    // Validate file
    if (empty($_FILES['json_file']['name']) || !in_array($_FILES['json_file']['type'], $jsonMimes)) {
        return array('status' => 'danger', 'message' => 'Please select a valid JSON file.');
    }

    if (!is_uploaded_file($_FILES['json_file']['tmp_name'])) {
        return array('status' => 'danger', 'message' => 'Something went wrong, please try again.');
    }

    // Read and validate JSON
    $jsonContent = file_get_contents($_FILES['json_file']['tmp_name']);
    $jsonData = json_decode($jsonContent, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return array('status' => 'danger', 'message' => 'Invalid JSON file format. Error: ' . json_last_error_msg());
    }

    if (!bb_data_validate_json_structure($jsonData)) {
        return array('status' => 'danger', 'message' => 'Invalid JSON structure. Missing required sections: schools, classes, or entities.');
    }

    // Import data
    $counters = bb_data_import_json_data($jsonData);

    $message = "JSON Import hoàn tất! " .
        "Schools: {$counters['schools']['created']} tạo mới, {$counters['schools']['updated']} cập nhật. " .
        "Classes: {$counters['classes']['created']} tạo mới, {$counters['classes']['updated']} cập nhật. " .
        "Entities: {$counters['entities']['created']} tạo mới, {$counters['entities']['updated']} cập nhật.";

    return array('status' => 'success', 'message' => $message);
}

/**
 * Validate JSON structure
 */
function bb_data_validate_json_structure($jsonData)
{
    return isset($jsonData['schools']) && isset($jsonData['classes']) && isset($jsonData['entities']);
}

/**
 * Import JSON data
 */
function bb_data_import_json_data($jsonData)
{
    $counters = array(
        'schools' => array('created' => 0, 'updated' => 0),
        'classes' => array('created' => 0, 'updated' => 0),
        'entities' => array('created' => 0, 'updated' => 0)
    );

    // Import schools
    foreach ($jsonData['schools'] as $school) {
        bb_data_import_school($school, $counters['schools']);
    }

    // Import classes
    foreach ($jsonData['classes'] as $class) {
        bb_data_import_class($class, $counters['classes']);
    }

    // Import entities
    foreach ($jsonData['entities'] as $entity) {
        bb_data_import_entity($entity, $counters['entities']);
    }

    return $counters;
}

/**
 * Import single school
 */
function bb_data_import_school($data, &$counters)
{
    $title = sanitize_text_field($data['title']);
    $existing_post = get_page_by_title($title, OBJECT, 'school');

    if ($existing_post) {
        $counters['updated']++;
    } else {
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'school',
            'post_status' => 'publish'
        ));
        if ($post_id) {
            $counters['created']++;
        }
    }
}

/**
 * Import single class
 */
function bb_data_import_class($data, &$counters)
{
    $title = sanitize_text_field($data['title']);
    $parent = !empty($data['parent_class']) ? sanitize_text_field($data['parent_class']) : '';
    $type = $data['type'];
    $existing_post = get_posts(array(
        'post_type' => $type,
        'title' => $title,
        'post_status' => array('publish', 'draft'),
        'numberposts' => 1,
        'meta_query' => array(
            array(
                'key' =>  'Thuộc Trường',
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
        bb_data_update_class_meta($existing_post->ID, $data);
        $counters['updated']++;
    } else {
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'class',
            'post_status' => 'publish'
        ));
        if ($post_id) {
            bb_data_update_class_meta($post_id, $data);
            $counters['created']++;
        }
    }
}

/**
 * Import single entity
 */
function bb_data_import_entity($data, &$counters)
{
    $title = sanitize_text_field($data['title']);
    $parent = !empty($data['parent_class']) ? sanitize_text_field($data['parent_class']) : '';
    $type = $data['type'];
    $existing_post = get_posts(array(
        'post_type' => $type,
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
        bb_data_update_entity_meta($existing_post->ID, $data);
        $counters['updated']++;
    } else {
        $post_id = wp_insert_post(array(
            'post_title' => $title,
            'post_type' => 'entity',
            'post_status' => 'publish'
        ));
        if ($post_id) {
            bb_data_update_entity_meta($post_id, $data);
            $counters['created']++;
        }
    }
}

/**
 * Update class meta data
 */
function bb_data_update_class_meta($post_id, $data)
{
    if (!empty($data['password'])) {
        update_post_meta($post_id, 'class_password', sanitize_text_field($data['password']));
    }
    if (!empty($data['parent_school'])) {
        update_post_meta($post_id, 'Thuộc Trường', sanitize_text_field($data['parent_school']));
    }
}

/**
 * Update entity meta data
 */
function bb_data_update_entity_meta($post_id, $data)
{
    if (!empty($data['password'])) {
        update_post_meta($post_id, 'lesson_password', sanitize_text_field($data['password']));
    }
    if (!empty($data['parent_class'])) {
        update_post_meta($post_id, 'Thuộc lớp', sanitize_text_field($data['parent_class']));
    }
    if (!empty($data['link'])) {
        update_post_meta($post_id, 'Link khi click', esc_url($data['link']));
    }
    if (!empty($data['image_url'])) {
        update_post_meta($post_id, 'Hình', esc_url($data['image_url']));
    }
    if (!empty($data['username'])) {
        update_post_meta($post_id, 'Username', sanitize_text_field($data['username']));
    }
}