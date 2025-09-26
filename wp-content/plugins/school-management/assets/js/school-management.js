jQuery(document).ready(function ($) {
  // Load schools on page load
  loadSchools();

  // Handle school selection
  $("#school-dropdown").on("change", function () {
    const schoolId = $(this).val();
    if (schoolId) {
      loadClasses(schoolId);
      $("#select-class").show();
    } else {
      $("#select-class").hide();
      $("#entity-container").hide();
    }
    // Reset class dropdown and entities
    $("#class-dropdown").html('<option value="">-- Chọn lớp --</option>');
    $("#entity-grid").empty();
  });

  // Handle class selection
  $("#class-dropdown").on("change", function () {
    const classId = $(this).val();
    if (classId) {
      loadEntities(classId);
      $("#entity-container").show();
    } else {
      $("#entity-container").hide();
      $("#entity-grid").empty();
    }
  });

  function loadSchools() {
    $.ajax({
      url: schoolManagementAjax.apiUrl + "school-management/v1/schools",
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      success: function (data) {
        const $dropdown = $("#school-dropdown");
        $dropdown.html('<option value="">-- Chọn trường --</option>');

        if (data && data.length > 0) {
          $.each(data, function (index, school) {
            $dropdown.append(
              '<option value="' +
                school.ID +
                '">' +
                school.post_title +
                "</option>"
            );
          });
        }
      },
      error: function () {
        console.error("Error loading schools");
      },
    });
  }

  function loadClasses(schoolId) {
    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/classes/" +
        schoolId,
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      success: function (data) {
        const $dropdown = $("#class-dropdown");
        $dropdown.html('<option value="">-- Chọn lớp --</option>');

        if (data && data.length > 0) {
          $.each(data, function (index, classItem) {
            $dropdown.append(
              '<option value="' +
                classItem.ID +
                '">' +
                classItem.post_title +
                "</option>"
            );
          });
        }
      },
      error: function () {
        console.error("Error loading classes");
      },
    });
  }

  function loadEntities(classId) {
    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/entities/" +
        classId,
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      success: function (data) {
        const $grid = $("#entity-grid");
        $grid.empty();

        if (data && data.length > 0) {
          $.each(data, function (index, entity) {
            let entityHtml = '<div class="entity-item">';

            if (entity.link) {
              entityHtml =
                '<a href="' +
                entity.link +
                '" class="entity-item" target="_blank">';
            }

            if (entity.image) {
              entityHtml +=
                '<img src="' + entity.image + '" alt="' + entity.title + '">';
            } else {
              entityHtml += '<div class="no-image">Không có hình</div>';
            }

            entityHtml += "<h4>" + entity.title + "</h4>";

            if (entity.link) {
              entityHtml += "</a>";
            } else {
              entityHtml += "</div>";
            }

            $grid.append(entityHtml);
          });
        } else {
          $grid.html("<p>Không có entity nào trong lớp này.</p>");
        }
      },
      error: function () {
        console.error("Error loading entities");
      },
    });
  }
});
