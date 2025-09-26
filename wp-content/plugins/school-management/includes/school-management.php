<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_shortcode('school_management', 'render_school_management_shortcode');

add_action('rest_api_init', 'register_school_management_routes');

add_action('init', 'create_school_management_page');

add_action('add_meta_boxes', 'add_entity_meta_boxes');

add_action('save_post', 'save_entity_meta');

add_action('admin_enqueue_scripts', 'enqueue_media_scripts');

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

function render_entity_meta_box($post)
{
    // Thêm nonce để bảo mật
    wp_nonce_field('save_entity_meta', 'entity_meta_nonce');

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
    <p>
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
        <button type="button" id="upload_image_button" class="button">Chọn hình</button>
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

function enqueue_media_scripts($hook_suffix)
{
    // Chỉ enqueue trên trang edit entity
    global $post_type;
    if ('entity' === $post_type) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}

function create_school_management_page()
{
    // Đăng ký post type Trường
    $args_school = [
        'public' => true,
        'has_archive' => true,
        'menu_position' => 30,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Trường',
            'singular_name' => 'Trường',
            'edit_item' => 'View Trường',
            'add_new_item' => 'Add Trường',
        ],
    ];
    register_post_type('school', $args_school);

    // Đăng ký post type Lớp học
    $args_class = [
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'edit.php?post_type=school',
        'menu_icon' => 'dashicons-groups', // icon cho submenu Lớp học
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Lớp học',
            'singular_name' => 'Lớp học',
            'edit_item' => 'View Lớp học',
            'add_new_item' => 'Add Lớp',

        ],
    ];
    register_post_type('class', $args_class);

    // Đăng ký post type Thực thể
    $args_entity = [
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => 'edit.php?post_type=school',
        'menu_icon' => 'dashicons-id', // icon cho submenu Thực thể
        'publicly_queryable' => false,
        'labels' => [
            'name' => 'Thực thể',
            'singular_name' => 'Thực thể',
            'edit_item' => 'View Thực thể',
            'add_new_item' => 'Add Entity',

        ],
        'supports' => ['title'],

    ];
    register_post_type('entity', $args_entity);
}

function register_school_management_routes()
{
    register_rest_route('school-management/v1', '/schools', array(
        'methods' => 'GET',
        'callback' => 'get_schools',
    ));
}

function get_schools()
{
    // Sample data - replace with actual data retrieval logic
    $schools = array(
        array('id' => 1, 'name' => 'School A'),
        array('id' => 2, 'name' => 'School B'),
    );
    return $schools;
}

function render_school_management_shortcode()
{
    include_once SCHOOLPLUGIN_PATH . '/includes/templates/school-management.php';
}