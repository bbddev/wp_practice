<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue media scripts for admin
 */
function enqueue_media_scripts($hook_suffix)
{
    // Chỉ enqueue trên trang edit entity
    global $post_type;
    if ('entity' === $post_type) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}