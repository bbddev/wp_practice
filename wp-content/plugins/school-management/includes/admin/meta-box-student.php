<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add meta boxes for student post type
 */
function add_student_meta_boxes()
{
    add_meta_box(
        'student_meta',
        'Student Details',
        'render_student_meta_box',
        'student',
        'normal',
        'high'
    );
}

/**
 * Render student meta box content
 */
function render_student_meta_box($post)
{
    wp_nonce_field('save_student_meta', 'student_meta_nonce');

    $student_username = get_post_meta($post->ID, 'student_username', true);
    $student_password = get_post_meta($post->ID, 'student_password', true);
    $student_of = get_post_meta($post->ID, 'student_of', true); // Lớp mà học sinh thuộc về, môn mà học sinh đăng ký
    $student_link = get_post_meta($post->ID, 'student_link', true); // quản lý thông tin học tập, điểm số
    $student_image = get_post_meta($post->ID, 'student_image', true); // Hình ảnh học sinh

    $classes = get_posts(array(
        'post_type' => 'school',
        'numberposts' => -1,
        'post_status' => 'publish'
    ));

    ?>
    <p>
        <label for="student_username">Username:</label>
        <input type="text" id="student_username" name="student_username" value="<?php echo esc_attr($student_username); ?>"
            style="width: 100%;" />
    </p>
    <p>
        <label for="student_password">Password:</label>
        <input type="text" id="student_password" name="student_password" value="<?php echo esc_attr($student_password); ?>"
            style="width: 100%;" />
    <p>
        <label for="student_of">Thuộc khối:</label>
        <select id="student_of" name="student_of" style="width: 100%;">
            <option value="">-- Chọn khối --</option>
            <?php foreach ($classes as $class): ?>
                <option value="<?php echo esc_attr($class->post_title); ?>" <?php selected($student_of, $class->post_title); ?>>
                    <?php echo esc_html($class->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="student_link">Student home link:</label>
        <input type="text" id="student_link" name="student_link" value="<?php echo esc_attr($student_link); ?>"
            style="width: 100%;" />
    </p>
    <p>
        <label for="student_image">Hình:</label>
        <input type="text" id="student_image" name="student_image" value="<?php echo esc_attr($student_image); ?>"
            style="width: 70%;" hidden />
        <button type="button" id="upload_student_image_button" class="button">Chọn hình học sinh</button>
        <button type="button" id="remove_student_image_button" class="button" style="color: red;">Xóa ảnh</button>
    </p>

    <div id="image_preview_container" style="margin-top: 10px;">
        <?php if ($student_image): ?>
            <img id="image_preview" src="<?php echo esc_url($student_image); ?>"
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
                var imageUrl = $('#student_image').val();
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
            $('#upload_student_image_button').click(function () {
                var frame = wp.media({
                    title: 'Chọn hình ảnh học sinh',
                    button: {
                        text: 'Sử dụng hình này'
                    },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#student_image').val(attachment.url);
                    toggleImageElements();
                });

                frame.open();
            });

            // Remove image button
            $('#remove_student_image_button').click(function () {
                $('#student_image').val('');
                toggleImageElements();
            });
        });
    </script>

    <?php
}