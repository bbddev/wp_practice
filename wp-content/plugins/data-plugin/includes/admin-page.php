<?php
/**
 * Admin page for BB Data Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add admin menu
 */
function bb_data_plugin_posts_menu()
{
    add_menu_page(
        'Import Data',
        'Import Data',
        'manage_options',
        'my-data-plugin-posts',
        'bb_data_plugin_posts_admin_page',
        'dashicons-database-view',
        27
    );
}
add_action('admin_menu', 'bb_data_plugin_posts_menu');

/**
 * Admin page content
 */
function bb_data_plugin_posts_admin_page()
{
    // Handle session response messages
    $status = '';
    $statusMsg = '';
    if (!empty($_SESSION['response'])) {
        $status = $_SESSION['response']['status'];
        $statusMsg = $_SESSION['response']['msg'];
        unset($_SESSION['response']);
    }

    // Include inline CSS
    echo '<link rel="stylesheet" href="' . BB_DATA_PLUGIN_URL . 'assets/admin-styles.css?v=1.0">';

    // Include inline JavaScript
    echo '<script src="' . BB_DATA_PLUGIN_URL . 'assets/admin-scripts.js?v=1.0"></script>';
    echo '<script>var bb_data_ajax = {
        export_nonce: "' . wp_create_nonce('bb_data_export') . '",
        export_json_nonce: "' . wp_create_nonce('bb_data_export_json') . '"
    };</script>';
    ?>
    <div class="wrap">
        <h1>Import Data</h1>

        <?php if (!empty($statusMsg)) { ?>
            <div class="alert alert-<?php echo esc_attr($status); ?>">
                <?php echo esc_html($statusMsg); ?>
            </div>
        <?php } ?>

        <div class="row">
            <!-- CSV file upload form -->
            <div class="col-md-12" id="importFrm"
                style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                <div class="col-md-6 head">
                    <div class="float-end">
                        <a href="javascript:void(0);" class="btn btn-success" onclick="downloadSample()">Download CSV
                            Sample</a>
                        <a href="javascript:void(0);" class="btn btn-success" onclick="downloadJsonSample()">Download JSON
                            Sample</a>
                        <a href="javascript:void(0);" class="btn btn-primary" onclick="exportData()">Export Data</a>
                        <a href="javascript:void(0);" class="btn btn-primary" onclick="exportDataJson()">Export JSON</a>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="action" value="import_csv_data_posts">
                    <?php wp_nonce_field('bb_data_import', 'bb_data_nonce'); ?>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="file" style="display: block; margin-bottom: 5px; font-weight: bold;">Choose CSV
                            File:</label>
                        <input type="file" name="file" id="file" accept=".csv" class="form-control" required
                            style="width: 100%; padding: 8px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <input type="submit" name="importSubmit" class="btn btn-primary" value="Import CSV"
                            style="padding: 10px 20px;">
                    </div>
                </form>

                <!-- JSON file upload form -->
                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data">
                    <input type="hidden" name="action" value="import_json_data_posts">
                    <?php wp_nonce_field('bb_data_import_json', 'bb_data_nonce'); ?>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="json_file" style="display: block; margin-bottom: 5px; font-weight: bold;">Choose JSON
                            File:</label>
                        <input type="file" name="json_file" id="json_file" accept=".json" class="form-control" required
                            style="width: 100%; padding: 8px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <input type="submit" name="importJsonSubmit" class="btn btn-primary" value="Import JSON"
                            style="padding: 10px 20px;">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}