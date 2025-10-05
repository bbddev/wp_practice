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
// add_action('admin_menu', 'bb_data_plugin_posts_menu');

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

        <div class="row">
            <!-- Import Options Tabs -->
            <div class="nav-tab-wrapper" style="margin-bottom: 20px;">
                <a href="javascript:void(0);" class="nav-tab nav-tab-active" onclick="switchImportType('general')"
                    id="generalTab">
                    General Data Import
                </a>
                <a href="javascript:void(0);" class="nav-tab" onclick="switchImportType('student')" id="studentTab">
                    Student Import
                </a>
            </div>

            <!-- General Data Import Section -->
            <div id="generalImportSection" class="import-section">
                <div class="col-md-12" id="importFrm"
                    style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">

                    <h3 style="margin-top: 0;">Import Schools, Classes & Entities</h3>

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
                    </div>

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
                                <input type="file" name="json_file" id="json_file" accept=".json" class="form-control"
                                    required style="width: 100%; padding: 8px;">
                            </div>

                            <div class="form-group" style="margin-bottom: 15px; margin-top: 30px;">
                                <input type="submit" name="importJsonSubmit" class="btn btn-primary" value="Import JSON"
                                    style="padding: 10px 20px;">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Student Import Section -->
            <div id="studentImportSection" class="import-section" style="display: none;">
                <div class="col-md-12" id="studentImportFrm"
                    style="background: #f0f8ff; padding: 20px; border-radius: 5px; margin-bottom: 20px;">

                    <h3 style="margin-top: 0;">Import Students</h3>

                    <!-- Student Action buttons -->
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <a href="javascript:void(0);" class="btn" onclick="downloadStudentSample()">Download Student
                                CSV Sample</a>
                            <a href="javascript:void(0);" class="button" onclick="exportStudentData()">Export Student
                                CSV</a>
                        </div>
                    </div>

                    <!-- Student CSV Import Form -->
                    <form id="studentCsvImportForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
                        method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="init_student_batch_csv_import">
                        <?php wp_nonce_field('bb_data_student_batch_import', 'bb_data_nonce'); ?>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="student_file" style="display: block; margin-bottom: 5px; font-weight: bold;">Choose
                                Student CSV
                                File:</label>
                            <input type="file" name="student_file" id="student_file" accept=".csv" class="form-control"
                                required style="width: 100%; padding: 8px;">
                            <small style="color: #666; font-style: italic;">CSV Format: student_username,
                                student_password, student_link, student_image</small>
                        </div>
                        <div id="select-student-of">
                            <label for="student-of-dropdown" class="center-label" style="font-weight: bold;">Khối:   </label>
                            <select id="student-of-dropdown" class="center-select">
                                <option value="">-- Chọn --</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 15px; margin-top: 30px;">
                            <input type="button" id="importStudentCsvBtn" class="btn btn-primary" value="Import Student CSV"
                                style="padding: 10px 20px;">
                        </div>
                    </form>

                    <!-- Student Progress bar container (hidden by default) -->
                    <div id="studentProgressContainer" style="display: none; margin-bottom: 20px;">
                        <div
                            style="width: 100%; background-color: #e0e0e0; border-radius: 5px; height: 25px; position: relative;">
                            <div id="studentProgressBar"
                                style="width: 0%; background-color: #2196F3; height: 100%; border-radius: 5px; transition: width 0.3s ease;">
                            </div>
                            <div id="studentProgressPercent"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #333;">
                                0%</div>
                        </div>
                        <div style="margin-top: 10px;">
                            <strong id="studentProgressText">Đang chuẩn bị import students...</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
}