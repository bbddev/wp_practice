<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Save entity meta data
 */
function save_entity_meta($post_id)
{
    if (!isset($_POST['entity_meta_nonce']) || !wp_verify_nonce($_POST['entity_meta_nonce'], 'save_entity_meta')) {
        return;
    }

    if (get_post_type($post_id) !== 'entity') {
        return;
    }
    if (isset($_POST['entity_username'])) {
        update_post_meta($post_id, 'Username', sanitize_text_field($_POST['entity_username']));
    }
    if (isset($_POST['lesson_password'])) {
        update_post_meta($post_id, 'lesson_password', sanitize_text_field($_POST['lesson_password']));
    }

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
    if (!isset($_POST['class_meta_nonce']) || !wp_verify_nonce($_POST['class_meta_nonce'], 'save_class_meta')) {
        return;
    }

    if (get_post_type($post_id) !== 'class') {
        return;
    }
    if (isset($_POST['class_password'])) {
        update_post_meta($post_id, 'class_password', sanitize_text_field($_POST['class_password']));
    }

    if (isset($_POST['class_school'])) {
        update_post_meta($post_id, 'Thuộc Trường', sanitize_text_field($_POST['class_school']));
    }
}
function save_student_meta($post_id)
{
    if (!isset($_POST['student_meta_nonce']) || !wp_verify_nonce($_POST['student_meta_nonce'], 'save_student_meta')) {
        return;
    }

    if (get_post_type($post_id) !== 'student') {
        return;
    }
    if (isset($_POST['student_username'])) {
        update_post_meta($post_id, 'student_username', sanitize_text_field($_POST['student_username']));
    }
    if (isset($_POST['student_password'])) {
        update_post_meta($post_id, 'student_password', sanitize_text_field($_POST['student_password']));
    }

    if (isset($_POST['student_of'])) {
        update_post_meta($post_id, 'student_of', sanitize_text_field($_POST['student_of']));
    }

    if (isset($_POST['student_link'])) {
        update_post_meta($post_id, 'student_link', esc_url_raw($_POST['student_link']));
    }

    if (isset($_POST['student_image'])) {
        update_post_meta($post_id, 'student_image', sanitize_text_field($_POST['student_image']));
    }
}