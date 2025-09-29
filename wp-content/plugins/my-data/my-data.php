<?php
/**
 * Plugin Name: My Data Plugin
 * Description: A simple data plugin for WordPress.
 * Version: 1.0
 * Author: Binh Vo
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Hook for plugin activation
register_activation_hook(__FILE__, 'my_data_plugin_create_table');

// Create database table on plugin activation
function my_data_plugin_create_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'members';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        status tinyint(1) DEFAULT 1,
        created datetime DEFAULT CURRENT_TIMESTAMP,
        modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Start session if not already started
add_action('init', 'my_data_plugin_start_session');
function my_data_plugin_start_session()
{
    if (!session_id()) {
        session_start();
    }
}

// Handle CSV import
add_action('wp_ajax_import_csv_data', 'my_data_plugin_import_csv');
function my_data_plugin_import_csv()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['my_data_nonce'], 'my_data_import')) {
        wp_die('Security check failed');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'members';

    $res_status = $res_msg = '';

    if (isset($_POST['importSubmit'])) {
        // Allowed mime types
        $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel');

        // Validate whether selected file is a CSV file
        if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {

            // If the file is uploaded
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {

                // Open uploaded CSV file with read-only mode
                $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

                // Skip the first line
                fgetcsv($csvFile);

                // Parse data from CSV file line by line
                while (($line = fgetcsv($csvFile)) !== FALSE) {
                    $line_arr = !empty($line) ? array_filter($line) : '';
                    if (!empty($line_arr)) {
                        // Get row data
                        $name = sanitize_text_field(trim($line_arr[0]));
                        $email = sanitize_email(trim($line_arr[1]));
                        $phone = sanitize_text_field(trim($line_arr[2]));
                        $status = intval(trim($line_arr[3]));

                        // Check whether member already exists in the database with the same email
                        $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE email = %s", $email));

                        if ($existing) {
                            // Update member data in the database
                            $wpdb->update(
                                $table_name,
                                array(
                                    'name' => $name,
                                    'phone' => $phone,
                                    'status' => $status,
                                    'modified' => current_time('mysql')
                                ),
                                array('email' => $email),
                                array('%s', '%s', '%d', '%s'),
                                array('%s')
                            );
                        } else {
                            // Insert member data in the database
                            $wpdb->insert(
                                $table_name,
                                array(
                                    'name' => $name,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'status' => $status,
                                    'created' => current_time('mysql'),
                                    'modified' => current_time('mysql')
                                ),
                                array('%s', '%s', '%s', '%d', '%s', '%s')
                            );
                        }
                    }
                }

                // Close opened CSV file
                fclose($csvFile);

                $res_status = 'success';
                $res_msg = 'Members data has been imported successfully.';
            } else {
                $res_status = 'danger';
                $res_msg = 'Something went wrong, please try again.';
            }
        } else {
            $res_status = 'danger';
            $res_msg = 'Please select a valid CSV file.';
        }

        // Store status in SESSION
        $_SESSION['response'] = array(
            'status' => $res_status,
            'msg' => $res_msg
        );
    }

    // Redirect back to the admin page
    wp_redirect(admin_url('admin.php?page=my-data-plugin'));
    exit();
}

add_action('admin_menu', 'my_data_plugin_menu');
function my_data_plugin_menu()
{
    add_menu_page(
        //page title
        'New data',
        //menu title
        'New data',
        //capability
        'manage_options',
        //menu slug
        'my-data-plugin',
        //callback function
        'my_data_plugin_admin_page',
        //icon
        'dashicons-database',
        //position
        26
    );
}

function my_data_plugin_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'members';

    if (!empty($_SESSION['response'])) {
        $status = $_SESSION['response']['status'];
        $statusMsg = $_SESSION['response']['msg'];
        unset($_SESSION['response']);
    }

    // Add Bootstrap CSS for styling
    echo '<style>
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .btn { display: inline-block; padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: 400; line-height: 1.42857143; text-align: center; white-space: nowrap; vertical-align: middle; cursor: pointer; border: 1px solid transparent; border-radius: 4px; text-decoration: none; }
        .btn-primary { color: #fff; background-color: #337ab7; border-color: #2e6da4; }
        .btn-success { color: #fff; background-color: #5cb85c; border-color: #4cae4c; }
        .float-end { float: right; }
        .table { width: 100%; max-width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
        .table-bordered { border: 1px solid #ddd; }
        .table-bordered th, .table-bordered td { border: 1px solid #ddd; }
        .table th, .table td { padding: 8px; line-height: 1.42857143; vertical-align: top; }
        .table-dark { background-color: #333; color: #fff; }
        #importFrm { margin-bottom: 20px; }
    </style>';
    ?>
    <div class="wrap">
        <h1>Member Data Management</h1>

        <?php if (!empty($statusMsg)) { ?>
            <div class="alert alert-<?php echo esc_attr($status); ?>">
                <?php echo esc_html($statusMsg); ?>
            </div>
        <?php } ?>

        <div class="row">
            <!-- Import link -->
            <div class="col-md-12 head" style="margin-bottom: 20px;">
                <div class="float-end">
                    <a href="javascript:void(0);" class="btn btn-primary" onclick="formToggle('importFrm');">
                        <i class="plus"></i> Import CSV
                    </a>
                </div>
                <div style="clear: both;"></div>
            </div>

            <!-- CSV file upload form -->
            <div class="col-md-12" id="importFrm" style="display: none;">
                <form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post"
                    enctype="multipart/form-data"
                    style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                    <?php wp_nonce_field('my_data_import', 'my_data_nonce'); ?>
                    <input type="hidden" name="action" value="import_csv_data">

                    <div style="margin-bottom: 15px;">
                        <label for="csv_file"><strong>Select CSV File:</strong></label><br>
                        <input type="file" name="file" id="csv_file" required accept=".csv" style="margin-top: 5px;">

                        <!-- Link to download sample format -->
                        <p style="margin: 10px 0 0 0; font-size: 12px;">
                            <em>CSV format: Name, Email, Phone, Status (1 for Active, 0 for Inactive)</em><br>
                            <a href="#" onclick="downloadSample();" style="color: #0073aa;">Download Sample Format</a>
                        </p>
                    </div>

                    <div>
                        <input type="submit" class="btn btn-success" name="importSubmit" value="Import CSV">
                        <button type="button" class="btn" onclick="formToggle('importFrm');"
                            style="margin-left: 10px;">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Data list table -->
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch member records from database 
                    $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
                    if ($result) {
                        foreach ($result as $row) {
                            ?>
                            <tr>
                                <td><?php echo esc_html('#' . $row->id); ?></td>
                                <td><?php echo esc_html($row->name); ?></td>
                                <td><?php echo esc_html($row->email); ?></td>
                                <td><?php echo esc_html($row->phone); ?></td>
                                <td>
                                    <span style="color: <?php echo $row->status == 1 ? 'green' : 'red'; ?>">
                                        <?php echo $row->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html(date('Y-m-d H:i', strtotime($row->created))); ?></td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #666;">No member(s) found...</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript functions -->
    <script>
        function formToggle(ID) {
            var element = document.getElementById(ID);
            if (element.style.display === "none") {
                element.style.display = "block";
            } else {
                element.style.display = "none";
            }
        }

        function downloadSample() {
            var csvContent = "Name,Email,Phone,Status\nJohn Doe,john@example.com,123-456-7890,1\nJane Smith,jane@example.com,098-765-4321,0";
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement("a");
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "sample-members.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <?php
}