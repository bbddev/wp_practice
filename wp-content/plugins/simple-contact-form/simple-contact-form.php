<?php
/**
 * Plugin Name: Simple Contact Form
 * Description: A simple contact form plugin for WordPress.
 * Version: 1.0
 * Author: Bb tech
 * Author URI: https://example.com
 * Text Domain: simple-contact-form
 * 
 */
if (!defined('ABSPATH')) {
    echo 'You are not allowed to access this file directly.';
    exit; // Exit if accessed directly.
}

class SimpleContactForm
{
    public function __construct()
    {
        //create custom post type
        add_action('init', array($this, 'create_custom_post_type'));

        //add assets(js, css, etc)
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));

        //Add shortcode
        add_shortcode('contact-form', array($this, 'load_shortcode'));

        //Load javascript in footer
        add_action('wp_footer', array($this, 'load_scripts'));

        //Register REST API
        add_action('rest_api_init', array($this, 'register_rest_api'));
    }
    public function create_custom_post_type()
    {
        $args = array(
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
            'exclude_from_search' => true,
            'capability' => 'manage_options',
            'labels' => array(
                'name' => 'Contact Forms',
                'singular_name' => 'Contact Form Entry',
            ),
            'menu_icon' => 'dashicons-media-document',

        );
        register_post_type('contact_form', $args);
    }
    public function load_assets()
    {
        // Enqueue Bootstrap CSS from CDN
        wp_enqueue_style(
            'bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
            array(),
            '5.3.0',
            'all'
        );

        // Enqueue Bootstrap JS from CDN
        wp_enqueue_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
            array(),
            '5.3.0',
            true
        );

        // Enqueue custom CSS (loads after Bootstrap)
        wp_enqueue_style(
            'simple-contact-form-style',
            plugin_dir_url(__FILE__) . 'css/simple-contact-form.css',
            array('bootstrap-css'), // Depends on Bootstrap CSS
            1,
            'all'
        );

        // Enqueue custom JS
        wp_enqueue_script(
            'simple-contact-form-script',
            plugin_dir_url(__FILE__) . 'js/simple-contact-form.js',
            array('jquery', 'bootstrap-js'), // Depends on jQuery and Bootstrap JS
            1,
            true
        );
    }
    public function load_shortcode()
    {

        return '
        <div class="simple-contact-form">
            <h1>Send us an email</h1>
            <p>Please fill out the form below to get in touch with us.</p>
            <form id="simple-contact-form__form">
                <div class="form-group mb-2">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="form-group mb-2">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
                <div class="form-group mb-2">
                    <input type="tel" name="phone" class="form-control" placeholder="Phone">
                </div>
                <div class="form-group mb-2">
                    <textarea name="message" class="form-control" placeholder="Type your Message" required></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-lock w-100">Send</button>
            </form>
        </div>
        ';
    }

    public function load_scripts()
    { ?>
        <script>
            var nonce = '<?php echo wp_create_nonce('wp_rest'); ?>';
            (function ($) {
                $('#simple-contact-form__form').submit(function (event) {
                    event.preventDefault(); // Prevent the default form submission
                    var form = $(this).serialize(); // Serialize form data
                    console.log("🚀 ~ SimpleContactForm ~ load_scripts ~ form:", form)
                    $.ajax({
                        method: 'post',
                        url: '<?php echo get_rest_url(null, 'simple-contact-form/v1/send-email'); ?>',
                        headers: { 'X-WP-Nonce': nonce },
                        data: form,
                        success: function (response) {
                            alert('Thank you! Your message has been sent.');
                            $('#simple-contact-form__form')[0].reset();
                        },
                        error: function (xhr, status, error) {
                            alert('Sorry, there was an error sending your message. Please try again.');
                            console.error('Error:', error);
                        }
                    })

                })

            })(jQuery);
        </script>
    <?php }

    public function register_rest_api()
    {
        register_rest_route('simple-contact-form/v1', 'send-email', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_contact_form')
        ));
    }

    public function handle_contact_form($data)
    {
        $headers = $data->get_headers();
        $params = $data->get_params();
        $nonce = $headers['x_wp_nonce'][0];

        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_REST_Response('Message not sent', 422);
        }

        // Extract form data
        $name = sanitize_text_field($params['name'] ?? '');
        $email = sanitize_email($params['email'] ?? '');
        $phone = sanitize_text_field($params['phone'] ?? '');
        $message = sanitize_textarea_field($params['message'] ?? '');

        // Validate required fields
        if (empty($name) || empty($email) || empty($message)) {
            return new WP_REST_Response('Please fill in all required fields', 400);
        }

        if (!is_email($email)) {
            return new WP_REST_Response('Please enter a valid email address', 400);
        }

        $post_id = wp_insert_post([
            'post_type' => 'contact_form',
            'post_title' => 'Contact from ' . $name . ' - ' . date('Y-m-d H:i:s'),
            'post_status' => 'publish'
        ]);

        if ($post_id) {
            // Store form data as post meta
            update_post_meta($post_id, '_contact_name', $name);
            update_post_meta($post_id, '_contact_email', $email);
            update_post_meta($post_id, '_contact_phone', $phone);
            update_post_meta($post_id, '_contact_message', $message);

            return new WP_REST_Response('Thank you for your email', 200);
        } else {
            return new WP_REST_Response('Failed to save your message', 500);
        }
    }


}

// Initialize the plugin after WordPress is loaded
new SimpleContactForm;