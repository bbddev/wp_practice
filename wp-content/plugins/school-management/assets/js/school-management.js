jQuery(document).ready(function ($) {
  // Pagination variables
  let allEntities = [];
  let currentPage = 1;
  const entitiesPerPage = 12;
  
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
    $("#pagination-container").hide();
    allEntities = [];
    currentPage = 1;
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
      $("#pagination-container").hide();
      allEntities = [];
      currentPage = 1;
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
          // Sort schools naturally by title
          data.sort(function (a, b) {
            return a.post_title.localeCompare(b.post_title, undefined, {
              numeric: true,
              sensitivity: "base",
            });
          });

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
          // Sort classes naturally by title
          data.sort(function (a, b) {
            return a.post_title.localeCompare(b.post_title, undefined, {
              numeric: true,
              sensitivity: "base",
            });
          });

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

  // Function to sort text naturally (handles numbers in text)
  function naturalSort(a, b) {
    const aTitle = a.title.toLowerCase();
    const bTitle = b.title.toLowerCase();

    return aTitle.localeCompare(bTitle, undefined, {
      numeric: true,
      sensitivity: "base",
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
        if (data && data.length > 0) {
          // Sort entities naturally by title before storing
          data.sort(naturalSort);
          allEntities = data;
          currentPage = 1;
          displayEntities();
          createPagination();
        } else {
          const $grid = $("#entity-grid");
          $grid.html("<p>Không có bài học nào trong lớp này.</p>");
          $("#pagination-container").hide();
        }
      },
      error: function () {
        console.error("Error loading entities");
      },
    });
  }

  function displayEntities() {
    const $grid = $("#entity-grid");
    $grid.empty();

    const startIndex = (currentPage - 1) * entitiesPerPage;
    const endIndex = startIndex + entitiesPerPage;
    const entitiesToShow = allEntities.slice(startIndex, endIndex);

    $.each(entitiesToShow, function (index, entity) {
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
  }

  function createPagination() {
    const totalPages = Math.ceil(allEntities.length / entitiesPerPage);
    
    if (totalPages <= 1) {
      $("#pagination-container").hide();
      return;
    }

    const $pagination = $("#pagination-container");
    $pagination.empty();

    let paginationHtml = '<nav aria-label="Phân trang bài học"><ul class="pagination justify-content-center">';

    // Previous button
    if (currentPage > 1) {
      paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">« Trước</a></li>';
    } else {
      paginationHtml += '<li class="page-item disabled"><span class="page-link">« Trước</span></li>';
    }

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      if (i === currentPage) {
        paginationHtml += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
      } else {
        paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
      }
    }

    // Next button
    if (currentPage < totalPages) {
      paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Sau »</a></li>';
    } else {
      paginationHtml += '<li class="page-item disabled"><span class="page-link">Sau »</span></li>';
    }

    paginationHtml += '</ul></nav>';
    
    $pagination.html(paginationHtml).show();
  }

  // Handle pagination click
  $(document).on("click", ".pagination .page-link[data-page]", function (e) {
    e.preventDefault();
    const page = parseInt($(this).data("page"));
    if (page !== currentPage) {
      currentPage = page;
      displayEntities();
      createPagination();
      
      // Scroll to top of entity container
      $("#entity-container")[0].scrollIntoView({ behavior: 'smooth' });
    }
  });

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
          // Clear input when password is wrong
          $("#classPasswordInput").val("").focus();
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
          // Clear input when password is wrong
          $("#lessonPasswordInput").val("").focus();
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

  // Clear error messages when typing in password inputs
  $("#classPasswordInput").on("input", function () {
    $("#classPasswordError").hide();
  });

  $("#lessonPasswordInput").on("input", function () {
    $("#lessonPasswordError").hide();
  });

  // Toggle password visibility for class password
  $("#toggleClassPassword").on("click", function () {
    const passwordInput = $("#classPasswordInput");
    const passwordIcon = $("#classPasswordIcon");

    if (passwordInput.attr("type") === "password") {
      passwordInput.attr("type", "text");
      passwordIcon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      passwordInput.attr("type", "password");
      passwordIcon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });

  // Toggle password visibility for lesson password
  $("#toggleLessonPassword").on("click", function () {
    const passwordInput = $("#lessonPasswordInput");
    const passwordIcon = $("#lessonPasswordIcon");

    if (passwordInput.attr("type") === "password") {
      passwordInput.attr("type", "text");
      passwordIcon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      passwordInput.attr("type", "password");
      passwordIcon.removeClass("fa-eye-slash").addClass("fa-eye");
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
