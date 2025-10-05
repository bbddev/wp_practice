<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add meta boxes for entity post type
 */
function add_entity_meta_boxes()
{
    add_meta_box(
        'entity_meta',
        'Entity Details',
        'render_entity_meta_box',
        'entity',
        'normal',
        'high'
    );
}

/**
 * Render entity meta box content
 */
function render_entity_meta_box($post)
{
    // Thêm nonce để bảo mật
    wp_nonce_field('save_entity_meta', 'entity_meta_nonce');

    $entity_username = get_post_meta($post->ID, 'Username', true);
    $lesson_password = get_post_meta($post->ID, 'lesson_password', true);
    $entity_class = get_post_meta($post->ID, 'Thuộc lớp', true);
    $entity_link = get_post_meta($post->ID, 'Link khi click', true);
    $entity_image = get_post_meta($post->ID, 'Hình', true);


    // Lấy danh sách tất cả các lớp học
    $classes = get_posts(array(
        'post_type' => 'class',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    ?>
        <!-- <p>
            <label for="entity_username">Username:</label>
            <input type="text" id="entity_username" name="entity_username" value="<?php echo esc_attr($entity_username); ?>"
                style="width: 100%;" />
        </p>
        <p>
            <label for="entity_password">Password:</label>
            <input type="text" id="lesson_password" name="lesson_password" value="<?php echo esc_attr($lesson_password); ?>"
                style="width: 100%;" />
        <p> -->
        <label for="entity_class">Thuộc lớp:</label>
        <select id="entity_class" name="entity_class" style="width: 100%;">
            <option value="">-- Chọn lớp --</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo esc_attr($class->post_title); ?>" 
                    <?php selected($entity_class, $class->post_title); ?>>
                    <?php echo esc_html($class->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="entity_link">Link khi click:</label>
        <input type="text" id="entity_link" name="entity_link" value="<?php echo esc_attr($entity_link); ?>"
            style="width: 100%;" />
    </p>
    <p>
        <label for="entity_image">Hình:</label>
        <input type="text" id="entity_image" name="entity_image" value="<?php echo esc_attr($entity_image); ?>"
            style="width: 70%;" hidden />
        <button type="button" id="upload_image_button" class="button">Chọn hình bài học</button>
        <button type="button" id="remove_image_button" class="button" style="color: red;">Xóa ảnh</button>
    </p>

    <div id="image_preview_container" style="margin-top: 10px;">
        <?php if ($entity_image): ?>
            <img id="image_preview" src="<?php echo esc_url($entity_image); ?>"
                style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;" />
        <?php else: ?>
            <img id="image_preview" src=""
                style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px; display: none;" />
        <?php endif; ?>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Function to show/hide remove button and preview based on image value
            function toggleImageElements() {
                var imageUrl = $('#entity_image').val();
                if (imageUrl) {
                    $('#remove_image_button').show();
                    $('#image_preview').attr('src', imageUrl).show();
                } else {
                    $('#remove_image_button').hide();
                    $('#image_preview').hide();
                }
            }

            // Initialize on page load
            toggleImageElements();

            // Upload image button
            $('#upload_image_button').click(function () {
                var frame = wp.media({
                    title: 'Chọn hình ảnh',
                    button: {
                        text: 'Sử dụng hình này'
                    },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#entity_image').val(attachment.url);
                    toggleImageElements();
                });

                frame.open();
            });

            // Remove image button
            $('#remove_image_button').click(function () {
                $('#entity_image').val('');
                toggleImageElements();
            });
        });
    </script>

    <?php
}