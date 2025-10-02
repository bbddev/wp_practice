/**
 * Password Management Module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.Password = {
  /**
   * Initialize password functionality
   * @param {Object} $ - jQuery object
   */
  init: function ($) {
    this.$ = $;
    this.bindEvents();
  },

  /**
   * Bind password-related events
   */
  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Class password submission
    $("#submitClassPassword").on("click", function () {
      self.submitClassPassword();
    });

    $("#classPasswordInput").on("keypress", function (e) {
      if (e.which === 13) {
        // Enter key
        e.preventDefault();
        self.submitClassPassword();
      }
    });

    // Lesson password submission
    $("#submitLessonPassword").on("click", function () {
      self.submitLessonPassword();
    });

    $("#lessonPasswordInput").on("keypress", function (e) {
      if (e.which === 13) {
        // Enter key
        e.preventDefault();
        self.submitLessonPassword();
      }
    });

    // Password visibility toggles
    $("#toggleClassPassword").on("click", function () {
      self.togglePasswordVisibility(
        "#classPasswordInput",
        "#classPasswordIcon"
      );
    });

    $("#toggleLessonPassword").on("click", function () {
      self.togglePasswordVisibility(
        "#lessonPasswordInput",
        "#lessonPasswordIcon"
      );
    });

    // Clear error messages when typing
    $("#classPasswordInput").on("input", function () {
      $("#classPasswordError").hide();
    });

    $("#lessonPasswordInput").on("input", function () {
      $("#lessonPasswordError").hide();
    });

    // Clear modal data when hidden
    $("#classPasswordModal").on("hidden.bs.modal", function () {
      $("#classPasswordInput").val("");
      $("#classPasswordError").hide();
    });

    $("#lessonPasswordModal").on("hidden.bs.modal", function () {
      $("#lessonPasswordInput").val("");
      $("#lessonPasswordError").hide();
    });
  },

  /**
   * Check if a class has password protection
   * @param {string} classId - Class ID
   */
  checkClassPassword: function (classId) {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/check-class-password/" +
        classId,
      method: "GET",
      success: function (data) {
        if (data.has_password) {
          // Show modal to enter password
          const $ = self.$ || window.SchoolManagement.$ || jQuery;
          $("#classPasswordModal").modal("show");
          $("#classPasswordModal").data("class-id", classId);
        } else {
          // Load entities directly
          window.SchoolManagement.Entity.loadEntities(classId);
          const $ = self.$ || window.SchoolManagement.$ || jQuery;
          $("#entity-container").show();
          $("#select-school").hide();
          $("#select-class").hide();
        }
      },
      error: function () {
        console.error("Error checking class password");
      },
    });
  },

  /**
   * Check if a lesson has password protection
   * @param {string} entityId - Entity ID
   * @param {string} entityLink - Entity link
   */
  checkLessonPassword: function (entityId, entityLink) {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/check-lesson-password/" +
        entityId,
      method: "GET",
      success: function (data) {
        if (data.has_password) {
          // Show modal to enter password
          const $ = self.$ || window.SchoolManagement.$ || jQuery;
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
  },

  /**
   * Submit class password for validation
   */
  submitClassPassword: function () {
    const $ = this.$;
    const classId = $("#classPasswordModal").data("class-id");
    const password = $("#classPasswordInput").val();

    if (!password) {
      $("#classPasswordError").text("Vui lòng nhập mật khẩu").show();
      return;
    }

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/validate-class-password",
      method: "POST",
      data: {
        class_id: classId,
        password: password,
      },
      success: function (data) {
        if (data.valid) {
          $("#classPasswordModal").modal("hide");
          $("#classPasswordInput").val("");
          $("#classPasswordError").hide();
          window.SchoolManagement.Entity.loadEntities(classId);
          $("#entity-container").show();
          $("#select-school").hide();
          $("#select-class").hide();
        } else {
          $("#classPasswordError").text("Mật khẩu không đúng").show();
          $("#classPasswordInput").val("").focus();
        }
      },
      error: function () {
        $("#classPasswordError").text("Có lỗi xảy ra. Vui lòng thử lại").show();
      },
    });
  },

  /**
   * Submit lesson password for validation
   */
  submitLessonPassword: function () {
    const $ = this.$;
    const entityId = $("#lessonPasswordModal").data("entity-id");
    const entityLink = $("#lessonPasswordModal").data("entity-link");
    const password = $("#lessonPasswordInput").val();

    if (!password) {
      $("#lessonPasswordError").text("Vui lòng nhập mật khẩu").show();
      return;
    }

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/validate-lesson-password",
      method: "POST",
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
          $("#lessonPasswordInput").val("").focus();
        }
      },
      error: function () {
        $("#lessonPasswordError")
          .text("Có lỗi xảy ra. Vui lòng thử lại")
          .show();
      },
    });
  },

  /**
   * Toggle password visibility
   * @param {string} inputSelector - Password input selector
   * @param {string} iconSelector - Eye icon selector
   */
  togglePasswordVisibility: function (inputSelector, iconSelector) {
    const $ = this.$;
    const passwordInput = $(inputSelector);
    const passwordIcon = $(iconSelector);

    if (passwordInput.attr("type") === "password") {
      passwordInput.attr("type", "text");
      passwordIcon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      passwordInput.attr("type", "password");
      passwordIcon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  },
};
