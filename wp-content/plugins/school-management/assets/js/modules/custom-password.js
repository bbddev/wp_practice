/**
 * Custom Password Management Module - No Bootstrap Dependencies
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.CustomPassword = {
  /**
   * Initialize password functionality
   * @param {Object} $ - jQuery object (optional for backward compatibility)
   */
  init: function ($) {
    this.$ = $ || null;
    this.bindEvents();
  },

  /**
   * Bind password-related events
   */
  bindEvents: function () {
    const self = this;

    // Class password submission
    const submitClassPasswordBtn = document.getElementById(
      "submitClassPassword"
    );
    if (submitClassPasswordBtn) {
      submitClassPasswordBtn.addEventListener("click", function () {
        self.submitClassPassword();
      });
    }

    const classPasswordInput = document.getElementById("classPasswordInput");
    if (classPasswordInput) {
      classPasswordInput.addEventListener("keypress", function (e) {
        if (e.which === 13 || e.keyCode === 13) {
          e.preventDefault();
          self.submitClassPassword();
        }
      });

      // Clear error messages when typing
      classPasswordInput.addEventListener("input", function () {
        window.SchoolManagement.CustomModal.hideError("classPasswordInput");
      });
    }

    // Lesson password submission
    const submitLessonPasswordBtn = document.getElementById(
      "submitLessonPassword"
    );
    if (submitLessonPasswordBtn) {
      submitLessonPasswordBtn.addEventListener("click", function () {
        self.submitLessonPassword();
      });
    }

    const lessonPasswordInput = document.getElementById("lessonPasswordInput");
    if (lessonPasswordInput) {
      lessonPasswordInput.addEventListener("keypress", function (e) {
        if (e.which === 13 || e.keyCode === 13) {
          e.preventDefault();
          self.submitLessonPassword();
        }
      });

      // Clear error messages when typing
      lessonPasswordInput.addEventListener("input", function () {
        window.SchoolManagement.CustomModal.hideError("lessonPasswordInput");
      });
    }

    const usernameInput = document.getElementById("usernameInput");
    if (usernameInput) {
      usernameInput.addEventListener("input", function () {
        window.SchoolManagement.CustomModal.hideError("usernameInput");
      });
    }

    // Password visibility toggles
    const toggleClassPassword = document.getElementById("toggleClassPassword");
    if (toggleClassPassword) {
      toggleClassPassword.addEventListener("click", function () {
        window.SchoolManagement.CustomModal.togglePasswordVisibility(
          "classPasswordInput",
          "classPasswordIcon"
        );
      });
    }

    const toggleLessonPassword = document.getElementById(
      "toggleLessonPassword"
    );
    if (toggleLessonPassword) {
      toggleLessonPassword.addEventListener("click", function () {
        window.SchoolManagement.CustomModal.togglePasswordVisibility(
          "lessonPasswordInput",
          "lessonPasswordIcon"
        );
      });
    }
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
          // Show custom modal to enter password
          window.SchoolManagement.CustomModal.showModal("classPasswordModal", {
            data: { "class-id": classId },
            onShow: function () {
              // Focus on password input when modal opens
              setTimeout(() => {
                const input = document.getElementById("classPasswordInput");
                if (input) input.focus();
              }, 100);
            },
          });
        } else {
          // Load entities directly
          window.SchoolManagement.Entity.loadEntities(classId);
          document.getElementById("entity-container").style.display = "block";
          // document.getElementById("select-school").style.display = "none";
          document.getElementById("select-class").style.display = "none";
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
          // Show custom modal to enter password
          window.SchoolManagement.CustomModal.showModal("lessonPasswordModal", {
            data: {
              "entity-id": entityId,
              "entity-link": entityLink,
            },
            onShow: function () {
              // Focus on username input when modal opens
              setTimeout(() => {
                const input = document.getElementById("usernameInput");
                if (input) input.focus();
              }, 100);
            },
          });
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
    const classId = window.SchoolManagement.CustomModal.getModalData(
      "classPasswordModal",
      "class-id"
    );
    const passwordInput = document.getElementById("classPasswordInput");
    const password = passwordInput ? passwordInput.value : "";

    if (!password) {
      window.SchoolManagement.CustomModal.showError(
        "classPasswordInput",
        "Vui lòng nhập mật khẩu"
      );
      return;
    }

    // Set loading state
    window.SchoolManagement.CustomModal.setButtonLoading(
      "submitClassPassword",
      true
    );

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
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitClassPassword",
          false
        );

        if (data.valid) {
          window.SchoolManagement.CustomModal.hideModal("classPasswordModal");
          window.SchoolManagement.Entity.loadEntities(classId);
          document.getElementById("entity-container").style.display = "block";
          document.getElementById("select-school").style.display = "none";
          document.getElementById("select-class").style.display = "none";
        } else {
          window.SchoolManagement.CustomModal.showError(
            "classPasswordInput",
            "Mật khẩu không đúng"
          );
          if (passwordInput) {
            passwordInput.value = "";
            passwordInput.focus();
          }
        }
      },
      error: function () {
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitClassPassword",
          false
        );
        window.SchoolManagement.CustomModal.showError(
          "classPasswordInput",
          "Có lỗi xảy ra. Vui lòng thử lại"
        );
      },
    });
  },

  /**
   * Submit lesson password for validation
   */
  submitLessonPassword: function () {
    const entityId = window.SchoolManagement.CustomModal.getModalData(
      "lessonPasswordModal",
      "entity-id"
    );
    const entityLink = window.SchoolManagement.CustomModal.getModalData(
      "lessonPasswordModal",
      "entity-link"
    );

    const usernameInput = document.getElementById("usernameInput");
    const passwordInput = document.getElementById("lessonPasswordInput");

    const username = usernameInput ? usernameInput.value : "";
    const password = passwordInput ? passwordInput.value : "";

    if (!username) {
      window.SchoolManagement.CustomModal.showError(
        "usernameInput",
        "Vui lòng nhập tên đăng nhập"
      );
      return;
    }
    if (!password) {
      window.SchoolManagement.CustomModal.showError(
        "lessonPasswordInput",
        "Vui lòng nhập mật khẩu"
      );
      return;
    }

    // Set loading state
    window.SchoolManagement.CustomModal.setButtonLoading(
      "submitLessonPassword",
      true
    );

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/validate-lesson-password",
      method: "POST",
      data: {
        entity_id: entityId,
        username: username,
        password: password,
      },
      success: function (data) {
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitLessonPassword",
          false
        );

        if (data.valid) {
          window.SchoolManagement.CustomModal.hideModal("lessonPasswordModal");
          window.open(entityLink, "_blank");
        } else {
          window.SchoolManagement.CustomModal.showError(
            "lessonPasswordInput",
            "Username hoặc Mật khẩu không đúng"
          );
          if (passwordInput) {
            passwordInput.value = "";
            passwordInput.focus();
          }
        }
      },
      error: function () {
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitLessonPassword",
          false
        );
        window.SchoolManagement.CustomModal.showError(
          "lessonPasswordInput",
          "Có lỗi xảy ra. Vui lòng thử lại"
        );
      },
    });
  },
};

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  // For backward compatibility, try to get jQuery if available
  const $ = window.jQuery || window.$ || null;
  window.SchoolManagement.CustomPassword.init($);
});

// Also maintain the original Password object for backward compatibility
window.SchoolManagement.Password = window.SchoolManagement.CustomPassword;
