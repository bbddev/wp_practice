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
      // Check if class has password first
      checkClassPassword(classId);
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

  function checkClassPassword(classId) {
    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/check-class-password/" +
        classId,
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      success: function (data) {
        if (data.has_password) {
          // Show modal to enter password
          $("#classPasswordModal").modal("show");
          $("#classPasswordModal").data("class-id", classId);
        } else {
          // Load entities directly
          loadEntities(classId);
          $("#entity-container").show();
        }
      },
      error: function () {
        console.error("Error checking class password");
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
            let entityHtml =
              '<div class="entity-item" data-entity-id="' + entity.id + '"';

            if (entity.link) {
              entityHtml += ' data-entity-link="' + entity.link + '"';
            }

            entityHtml += ">";

            if (entity.image) {
              entityHtml +=
                '<img src="' + entity.image + '" alt="' + entity.title + '">';
            } else {
              entityHtml += '<div class="no-image">Không có hình</div>';
            }

            entityHtml += "<h4>" + entity.title + "</h4>";
            entityHtml += "</div>";

            $grid.append(entityHtml);
          });
        } else {
          $grid.html("<p>Không có bài học nào trong lớp này.</p>");
        }
      },
      error: function () {
        console.error("Error loading entities");
      },
    });
  }

  // Handle entity click
  $(document).on("click", ".entity-item", function () {
    const entityId = $(this).data("entity-id");
    const entityLink = $(this).data("entity-link");

    if (!entityLink) {
      return; // No link to navigate to
    }

    // Check if lesson has password
    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/check-lesson-password/" +
        entityId,
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      success: function (data) {
        if (data.has_password) {
          // Show modal to enter password
          $("#lessonPasswordModal").modal("show");
          $("#lessonPasswordModal").data("entity-id", entityId);
          $("#lessonPasswordModal").data("entity-link", entityLink);
        } else {
          // Navigate directly
          window.open(entityLink, "_blank");
        }
      },
      error: function () {
        console.error("Error checking lesson password");
      },
    });
  });

  // Function to submit class password
  function submitClassPassword() {
    const classId = $("#classPasswordModal").data("class-id");
    const password = $("#classPasswordInput").val();

    if (!password) {
      $("#classPasswordError").text("Vui lòng nhập mật khẩu").show();
      return;
    }

    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/validate-class-password",
      method: "POST",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      data: {
        class_id: classId,
        password: password,
      },
      success: function (data) {
        if (data.valid) {
          $("#classPasswordModal").modal("hide");
          $("#classPasswordInput").val("");
          $("#classPasswordError").hide();
          loadEntities(classId);
          $("#entity-container").show();
        } else {
          $("#classPasswordError").text("Mật khẩu không đúng").show();
        }
      },
      error: function () {
        $("#classPasswordError").text("Có lỗi xảy ra. Vui lòng thử lại").show();
      },
    });
  }

  // Handle class password submission via button click
  $("#submitClassPassword").on("click", function () {
    submitClassPassword();
  });

  // Handle class password submission via Enter key
  $("#classPasswordInput").on("keypress", function (e) {
    if (e.which === 13) {
      // Enter key
      e.preventDefault();
      submitClassPassword();
    }
  });

  // Function to submit lesson password
  function submitLessonPassword() {
    const entityId = $("#lessonPasswordModal").data("entity-id");
    const entityLink = $("#lessonPasswordModal").data("entity-link");
    const password = $("#lessonPasswordInput").val();

    if (!password) {
      $("#lessonPasswordError").text("Vui lòng nhập mật khẩu").show();
      return;
    }

    $.ajax({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/validate-lesson-password",
      method: "POST",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", schoolManagementAjax.nonce);
      },
      data: {
        entity_id: entityId,
        password: password,
      },
      success: function (data) {
        if (data.valid) {
          $("#lessonPasswordModal").modal("hide");
          $("#lessonPasswordInput").val("");
          $("#lessonPasswordError").hide();
          window.open(entityLink, "_blank");
        } else {
          $("#lessonPasswordError").text("Mật khẩu không đúng").show();
        }
      },
      error: function () {
        $("#lessonPasswordError")
          .text("Có lỗi xảy ra. Vui lòng thử lại")
          .show();
      },
    });
  }

  // Handle lesson password submission via button click
  $("#submitLessonPassword").on("click", function () {
    submitLessonPassword();
  });

  // Handle lesson password submission via Enter key
  $("#lessonPasswordInput").on("keypress", function (e) {
    if (e.which === 13) {
      // Enter key
      e.preventDefault();
      submitLessonPassword();
    }
  });
  function forceModalOnTop() {
    $(".modal-backdrop").remove();
  }

  // Clear error messages when modal is hidden
  $("#classPasswordModal").on("hidden.bs.modal", function () {
    $("#classPasswordInput").val("");
    $("#classPasswordError").hide();
  });

  $("#lessonPasswordModal").on("hidden.bs.modal", function () {
    $("#lessonPasswordInput").val("");
    $("#lessonPasswordError").hide();
  });

  // Force modals on top when shown
  $("#classPasswordModal").on("show.bs.modal", function () {
    setTimeout(() => {
      forceModalOnTop();
    }, 50);
  });

  $("#lessonPasswordModal").on("show.bs.modal", function () {
    setTimeout(() => {
      forceModalOnTop("#lessonPasswordModal");
    }, 50);
  });

  // Additional fix for when modal is fully shown
  $("#classPasswordModal").on("shown.bs.modal", function () {
    forceModalOnTop();
    // Focus on input to ensure it's interactive
    $("#classPasswordInput").focus();
  });

  $("#lessonPasswordModal").on("shown.bs.modal", function () {
    forceModalOnTop();
    // Focus on input to ensure it's interactive
    $("#lessonPasswordInput").focus();
  });
});
