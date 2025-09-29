<?php
/**
 * Ví dụ kết hợp các phương pháp lưu trữ
 */

// 1. Cấu hình plugin trong wp_options
function bb_get_plugin_settings()
{
    return get_option('bb_plugin_settings', array(
        'import_batch_size' => 100,
        'enable_logging' => true,
        'default_status' => 1
    ));
}

// 2. Dữ liệu chính trong wp_posts (Custom Post Type)
function bb_save_main_data($name, $email, $phone)
{
    $post_id = wp_insert_post(array(
        'post_title' => $name,
        'post_type' => 'bb_data',
        'post_status' => 'publish',
        'meta_input' => array(
            'bb_email' => $email,
            'bb_phone' => $phone
        )
    ));
    return $post_id;
}

// 3. Log hoạt động trong bảng riêng (cho hiệu suất)
function bb_create_log_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bb_logs';

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) DEFAULT NULL,
        action varchar(50) NOT NULL,
        details text,
        user_id bigint(20) DEFAULT NULL,
        created datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY action (action)
    ) {$wpdb->get_charset_collate()};";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function bb_log_activity($post_id, $action, $details = '')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bb_logs';

    $wpdb->insert($table_name, array(
        'post_id' => $post_id,
        'action' => $action,
        'details' => $details,
        'user_id' => get_current_user_id(),
        'created' => current_time('mysql')
    ));
}

// 4. Cache thống kê trong wp_options (transient)
function bb_get_statistics()
{
    $stats = get_transient('bb_statistics');

    if (false === $stats) {
        // Tính toán thống kê
        $stats = array(
            'total_records' => wp_count_posts('bb_data')->publish,
            'active_records' => bb_count_active_records(),
            'last_import' => bb_get_last_import_date()
        );

        // Cache trong 1 giờ
        set_transient('bb_statistics', $stats, HOUR_IN_SECONDS);
    }

    return $stats;
}

// 5. Thông tin user mở rộng trong wp_usermeta
function bb_save_user_preferences($user_id, $preferences)
{
    update_user_meta($user_id, 'bb_import_preferences', $preferences);
    update_user_meta($user_id, 'bb_last_import_date', current_time('mysql'));
}

// Hàm tiện ích
function bb_count_active_records()
{
    $args = array(
        'post_type' => 'bb_data',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'bb_status',
                'value' => 1,
                'compare' => '='
            )
        ),
        'fields' => 'ids'
    );

    $query = new WP_Query($args);
    return $query->found_posts;
}

function bb_get_last_import_date()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'bb_logs';

    return $wpdb->get_var($wpdb->prepare(
        "SELECT created FROM $table_name WHERE action = %s ORDER BY created DESC LIMIT 1",
        'csv_import'
    ));
}