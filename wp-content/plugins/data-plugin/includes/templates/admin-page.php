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

    // Assets are now properly enqueued via wp_enqueue_scripts in main plugin file
    ?>
    <div class="wrap">
        <h1>Import Data</h1>

        <?php if (!empty($statusMsg)) { ?>
            <div class="alert alert-<?php echo esc_attr($status); ?>"
                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: nowrap;">
                <div class="status-message">
                    <?php echo esc_html($statusMsg); ?>
                </div>
                <div class="view-links" style="white-space: nowrap;">
                    <a href="edit.php?post_type=entity" style="font-style: italic;">View Lesson List</a>,
                    <a href="edit.php?post_type=class" style="font-style: italic;">View Class List</a>,
                    <a href="edit.php?post_type=school" style="font-style: italic;">View School List</a>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <!-- CSV file upload form -->
            <div class="col-md-12" id="importFrm"
                style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">

                <!-- Button row with responsive layout -->
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                    <!-- Left side - Import buttons -->
                    <div style="display: flex; gap: 10px;">
                        <a href="javascript:void(0);" class="button-primary" onclick="formToggle('csvForm')"
                            id="toggleCsvBtn">Import CSV</a>
                        <a href="javascript:void(0);" class="button-secondary" onclick="formToggle('jsonForm')"
                            id="importJsonBtn">Import JSON</a>
                    </div>

                    <!-- Right side - Action buttons -->
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="javascript:void(0);" class="btn" onclick="downloadSample()">Download CSV
                            Sample</a>
                        <a href="javascript:void(0);" class="btn" onclick="downloadJsonSample()">Download JSON
                            Sample</a>
                        <a href="javascript:void(0);" class="button" onclick="exportData()">Export CSV</a>
                        <a href="javascript:void(0);" class="button" onclick="exportDataJson()">Export JSON</a>
                    </div>
                </div>
                <div id="csvForm" style="display:block;">                  
                    <form id="csvImportForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                        enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import_csv_data_posts">
                        <?php wp_nonce_field('bb_data_import', 'bb_data_nonce'); ?>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="file" style="display: block; margin-bottom: 5px; font-weight: bold;">Choose CSV
                                File:</label>
                            <input type="file" name="file" id="file" accept=".csv" class="form-control" required
                                style="width: 100%; padding: 8px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 15px; margin-top: 30px;">
                            <input type="button" id="importCsvBtn" class="btn btn-primary" value="Import CSV"
                                style="padding: 10px 20px;">
                            <!-- <input type="submit" name="importSubmit" class="btn btn-secondary" value="Import CSV (Legacy)"
                                style="padding: 10px 20px; margin-left: 10px;"> -->
                        </div>
                    </form>
                    <!-- Progress bar container (hidden by default) -->
                       <div id="progressContainer" style="display: none; margin-bottom: 20px;">                        
                        <div
                            style="width: 100%; background-color: #e0e0e0; border-radius: 5px; height: 25px; position: relative;">
                            <div id="progressBar"
                                style="width: 0%; background-color: #4CAF50; height: 100%; border-radius: 5px; transition: width 0.3s ease;">
                            </div>
                            <div id="progressPercent"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #333;">
                                0%</div>
                        </div> 
                        <div style="margin-top: 10px;">
                            <strong id="progressText">Đang chuẩn bị import...</strong>
                        </div>                       
                    </div>
                </div>
                <div id="jsonForm" style="display:none;">
                    <!-- JSON file upload form -->
                    <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                        enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import_json_data_posts">
                        <?php wp_nonce_field('bb_data_import_json', 'bb_data_nonce'); ?>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="json_file" style="display: block; margin-bottom: 5px; font-weight: bold;">Choose
                                JSON
                                File:</label>
                            <input type="file" name="json_file" id="json_file" accept=".json" class="form-control" required
                                style="width: 100%; padding: 8px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 15px; margin-top: 30px;">
                            <input type="submit" name="importJsonSubmit" class="btn btn-primary" value="Import JSON"
                                style="padding: 10px 20px;">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}