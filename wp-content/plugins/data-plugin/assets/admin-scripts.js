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
});
