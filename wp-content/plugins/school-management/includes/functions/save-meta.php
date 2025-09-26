<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Save entity meta data
 */
function save_entity_meta($post_id)
{
    // Kiểm tra nonce để bảo mật
    if (!isset($_POST['entity_meta_nonce']) || !wp_verify_nonce($_POST['entity_meta_nonce'], 'save_entity_meta')) {
        return;
    }

    // Kiểm tra quyền chỉnh sửa post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Kiểm tra autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Kiểm tra post type
    if (get_post_type($post_id) !== 'entity') {
        return;
    }

    // Lưu các trường meta
    if (isset($_POST['entity_class'])) {
        update_post_meta($post_id, 'Thuộc lớp', sanitize_text_field($_POST['entity_class']));
    }

    if (isset($_POST['entity_link'])) {
        update_post_meta($post_id, 'Link khi click', esc_url_raw($_POST['entity_link']));
    }

    if (isset($_POST['entity_image'])) {
        update_post_meta($post_id, 'Hình', sanitize_text_field($_POST['entity_image']));
    }
}

function save_class_meta($post_id)
{
    // Kiểm tra nonce để bảo mật
    if (!isset($_POST['class_meta_nonce']) || !wp_verify_nonce($_POST['class_meta_nonce'], 'save_class_meta')) {
        return;
    }

    // Kiểm tra quyền chỉnh sửa post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Kiểm tra autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Kiểm tra post type
    if (get_post_type($post_id) !== 'class') {
        return;
    }

    // Lưu các trường meta
    if (isset($_POST['class_school'])) {
        update_post_meta($post_id, 'Thuộc Trường', sanitize_text_field($_POST['class_school']));
    }
}