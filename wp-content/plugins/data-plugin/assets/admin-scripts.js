/**
 * Admin JavaScript for BB Data Plugin
 */
function formToggle(ID) {
  var jsonForm = document.getElementById("jsonForm");
  var csvForm = document.getElementById("csvForm");

  // Ẩn tất cả forms trước
  csvForm.style.display = "none";
  jsonForm.style.display = "none";

  // Hiển thị form được chọn
  var targetForm = document.getElementById(ID);
  if (targetForm) {
    targetForm.style.display = "block";
  }
}

/**
 * Download CSV sample file
 */
function downloadSample() {
  var csvContent = "type,title,password,parent,link,image_url\n";
  csvContent += 'school,"Khối 6","","","",""\n';
  csvContent += 'class,"Lớp 6.1","password123","Khối 6","",""\n';
  csvContent +=
    'entity,"Hình 1","lesson123","Lớp 6.1","https://example.com","https://example.com/image.jpg"\n';

  var blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  var link = document.createElement("a");
  var url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", "sample-data.csv");
  link.style.visibility = "hidden";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

/**
 * Download JSON sample file
 */
function downloadJsonSample() {
  var jsonContent = {
    export_info: {
      export_date: "2024-01-01 10:00:00",
      plugin_version: "1.0",
      total_records: 3,
    },
    schools: [
      {
        id: 1,
        title: "Khối 6",
        type: "school",
        created_date: "2024-01-01 10:00:00",
      },
    ],
    classes: [
      {
        id: 2,
        title: "Lớp 6.1",
        type: "class",
        password: "password123",
        parent_school: "Khối 6",
        created_date: "2024-01-01 10:05:00",
      },
    ],
    entities: [
      {
        id: 3,
        title: "Hình 1",
        type: "entity",
        password: "lesson123",
        parent_class: "Lớp 6.1",
        link: "https://example.com",
        image_url: "https://example.com/image.jpg",
        created_date: "2024-01-01 10:10:00",
      },
    ],
  };

  var blob = new Blob([JSON.stringify(jsonContent, null, 2)], {
    type: "application/json;charset=utf-8;",
  });
  var link = document.createElement("a");
  var url = URL.createObjectURL(blob);
  link.setAttribute("href", url);
  link.setAttribute("download", "sample-data.json");
  link.style.visibility = "hidden";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

/**
 * Export data as CSV
 */
function exportData() {
  var form = document.createElement("form");
  form.method = "post";
  form.action = ajaxurl;

  // Add action input
  var actionInput = document.createElement("input");
  actionInput.type = "hidden";
  actionInput.name = "action";
  actionInput.value = "export_csv_data_posts";
  form.appendChild(actionInput);

  // Add nonce input
  var nonceInput = document.createElement("input");
  nonceInput.type = "hidden";
  nonceInput.name = "bb_data_nonce";
  nonceInput.value = bb_data_ajax.export_nonce;
  form.appendChild(nonceInput);

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}

/**
 * Export data as JSON
 */
function exportDataJson() {
  var form = document.createElement("form");
  form.method = "post";
  form.action = ajaxurl;

  // Add action input
  var actionInput = document.createElement("input");
  actionInput.type = "hidden";
  actionInput.name = "action";
  actionInput.value = "export_json_data_posts";
  form.appendChild(actionInput);

  // Add nonce input
  var nonceInput = document.createElement("input");
  nonceInput.type = "hidden";
  nonceInput.name = "bb_data_nonce";
  nonceInput.value = bb_data_ajax.export_json_nonce;
  form.appendChild(nonceInput);

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}

/**
 * Validate file before upload
 */
function validateCsvFile(input) {
  var file = input.files[0];
  if (file) {
    var fileName = file.name;
    var fileExtension = fileName.split(".").pop().toLowerCase();

    if (fileExtension !== "csv") {
      alert("Please select a valid CSV file.");
      input.value = "";
      return false;
    }
  }
  return true;
}

/**
 * Validate JSON file before upload
 */
function validateJsonFile(input) {
  var file = input.files[0];
  if (file) {
    var fileName = file.name;
    var fileExtension = fileName.split(".").pop().toLowerCase();

    if (fileExtension !== "json") {
      alert("Please select a valid JSON file.");
      input.value = "";
      return false;
    }
  }
  return true;
}

/**
 * Start batch CSV import
 */
function startBatchImport() {
  // Debug: Check if bb_data_ajax is available
  if (typeof bb_data_ajax === "undefined") {
    alert("Error: bb_data_ajax is not defined. Please refresh the page.");
    return;
  }

  var fileInput = document.getElementById("file");
  var file = fileInput.files[0];

  if (!file) {
    alert("Please select a CSV file first.");
    return;
  }

  if (!validateCsvFile(fileInput)) {
    return;
  }

  // Show progress container
  var progressContainer = document.getElementById("progressContainer");
  var importBtn = document.getElementById("importCsvBtn");

  progressContainer.style.display = "block";
  importBtn.disabled = true;
  importBtn.value = "Importing...";

  // Reset progress
  updateProgress(0, "Đang khởi tạo batch import...", 0, 0, 0, 0, 0);

  // Create FormData for file upload
  var formData = new FormData();
  formData.append("action", "init_batch_csv_import");
  formData.append("bb_data_nonce", bb_data_ajax.batch_import_nonce);
  formData.append("file", file);

  // Initialize batch import
  var xhr = new XMLHttpRequest();
  xhr.open("POST", bb_data_ajax.ajax_url, true);

  xhr.onload = function () {
    if (xhr.status === 200) {
      try {
        var response = JSON.parse(xhr.responseText);
        if (response.status === "success") {
          // Start processing batches
          processBatches(
            response.session_key,
            response.total_records,
            response.total_batches,
            response.batch_size
          );
        } else {
          handleImportError("Initialization failed: " + response.message);
        }
      } catch (e) {
        handleImportError("Invalid server response during initialization");
      }
    } else {
      handleImportError("Server error during initialization: " + xhr.status);
    }
  };

  xhr.onerror = function () {
    handleImportError("Network error during initialization");
  };

  xhr.send(formData);
}

/**
 * Process batches sequentially
 */
function processBatches(sessionKey, totalRecords, totalBatches, batchSize) {
  var currentBatch = 0;
  var totalProcessed = 0;
  var totalSuccess = 0;
  var totalErrors = 0;

  function processSingleBatch() {
    updateProgress(
      Math.round((currentBatch / totalBatches) * 100),
      `Đang xử lý batch ${currentBatch + 1}/${totalBatches}...`,

      currentBatch + 1,
      totalBatches,
      totalProcessed,
      totalRecords
    );

    var formData = new FormData();
    formData.append("action", "process_batch_csv_import");
    formData.append("bb_data_nonce", bb_data_ajax.batch_import_nonce);
    formData.append("session_key", sessionKey);
    formData.append("batch_number", currentBatch);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", bb_data_ajax.ajax_url, true);

    xhr.onload = function () {
      if (xhr.status === 200) {
        try {
          var response = JSON.parse(xhr.responseText);
          if (response.status === "success") {
            currentBatch++;
            totalProcessed = response.processed_records;

            // Update counters from response
            var counters = response.counters;
            totalSuccess = counters.created + counters.updated;
            totalErrors = counters.skipped;

            updateProgress(
              response.progress_percent,
              `Đã xử lý ${totalProcessed}/${totalRecords} records...`,
              currentBatch,
              totalBatches,
              totalProcessed,
              totalRecords,
              totalSuccess,
              totalErrors
            );

            if (response.is_complete) {
              // Import completed
              completeImport(counters);
            } else {
              // Process next batch with a small delay
              setTimeout(processSingleBatch, 100);
            }
          } else {
            handleImportError("Batch processing failed: " + response.message);
          }
        } catch (e) {
          handleImportError("Invalid server response during batch processing");
        }
      } else {
        handleImportError(
          "Server error during batch processing: " + xhr.status
        );
      }
    };

    xhr.onerror = function () {
      handleImportError("Network error during batch processing");
    };

    xhr.send(formData);
  }

  // Start processing
  processSingleBatch();
}

/**
 * Update progress display
 */
function updateProgress(
  percent,
  text,
  currentBatch,
  totalBatches,
  processed,
  total,
  success,
  errors
) {
  var progressBar = document.getElementById("progressBar");
  var progressPercent = document.getElementById("progressPercent");
  var progressText = document.getElementById("progressText");
  var batchInfo = document.getElementById("currentBatch");
  var recordsInfo = document.getElementById("recordsProcessed");
  var successInfo = document.getElementById("successCount");
  var errorInfo = document.getElementById("errorCount");

  if (progressBar) progressBar.style.width = percent + "%";
  if (progressPercent) progressPercent.textContent = Math.round(percent) + "%";
  if (progressText) progressText.textContent = text;
  if (batchInfo)
    batchInfo.textContent = "Batch: " + currentBatch + "/" + totalBatches;
  if (recordsInfo)
    recordsInfo.textContent = "Records: " + processed + "/" + total;
  if (successInfo) successInfo.textContent = "Success: " + (success || 0);
  if (errorInfo) errorInfo.textContent = "Errors: " + (errors || 0);
}

/**
 * Complete import process
 */
function completeImport(counters) {
  updateProgress(100, "Import hoàn tất!", 0, 0, 0, 0, 0, 0);

  var importBtn = document.getElementById("importCsvBtn");
  importBtn.disabled = false;
  importBtn.value = "Import CSV (Batch Mode)";

  // Show success message
  var message =
    `Import hoàn tất! Tổng cộng: ${counters.imported} dòng đã import thành công ` +
    `(Tạo mới: ${counters.created}, Cập nhật: ${counters.updated}, Bỏ qua: ${counters.skipped}).`;

  alert(message);

  // Hide progress after 3 seconds
  setTimeout(function () {
    document.getElementById("progressContainer").style.display = "none";
  }, 3000);

  // Refresh the page to show updated data
  setTimeout(function () {
    window.location.reload();
  }, 3500);
}

/**
 * Handle import errors
 */
function handleImportError(message) {
  var importBtn = document.getElementById("importCsvBtn");
  importBtn.disabled = false;
  importBtn.value = "Import CSV (Batch Mode)";

  document.getElementById("progressContainer").style.display = "none";
  alert("Import error: " + message);
}

/**
 * Initialize admin scripts when document is ready
 */
document.addEventListener("DOMContentLoaded", function () {
  // Add file validation to file inputs
  var csvFileInput = document.getElementById("file");
  if (csvFileInput) {
    csvFileInput.addEventListener("change", function () {
      validateCsvFile(this);
    });
  }

  var jsonFileInput = document.getElementById("json_file");
  if (jsonFileInput) {
    jsonFileInput.addEventListener("change", function () {
      validateJsonFile(this);
    });
  }

  // Add batch import button click handler
  var batchImportBtn = document.getElementById("importCsvBtn");
  if (batchImportBtn) {
    batchImportBtn.addEventListener("click", function () {
      startBatchImport();
    });
  }
});
