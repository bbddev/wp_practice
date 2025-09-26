<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add meta boxes for class post type
 */
function add_class_meta_boxes()
{
    add_meta_box(
        'class_meta',
        'Class Details',
        'render_class_meta_box',
        'class',
        'normal',
        'high'
    );
}

/**
 * Render class meta box content
 */
function render_class_meta_box($post)
{
    // Thêm nonce để bảo mật
    wp_nonce_field('save_class_meta', 'class_meta_nonce');

    $class_school = get_post_meta($post->ID, 'Thuộc Trường', true);

    // Lấy danh sách tất cả các lớp học
    $school_list = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    ?>
    <p>
        <label for="class_school">Thuộc trường:</label>
        <select id="class_school" name="class_school" style="width: 100%;">
            <option value="">-- Chọn trường --</option>
            <?php foreach ($school_list as $school): ?>
                <option value="<?php echo esc_attr($school->post_title); ?>" 
                    <?php selected($class_school, $school->post_title); ?>>
                    <?php echo esc_html($school->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <?php
}