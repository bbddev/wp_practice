/**
 * Student login module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.StudentLogin = {
  init: function ($) {
    this.$ = $ || window.SchoolManagement.$ || jQuery;
    this.pendingCallback = null;
    this.bindEvents();
  },

  bindEvents: function () {
    const self = this;

    // Submit student login
    const submitBtn = document.getElementById("submitStudentLogin");
    if (submitBtn) {
      submitBtn.addEventListener("click", function () {
        self.submitLogin();
      });
    }

    const usernameInput = document.getElementById("studentUsernameInput");
    if (usernameInput) {
      usernameInput.addEventListener("input", function () {
        window.SchoolManagement.CustomModal.hideError("studentUsernameInput");
      });
    }

    const passwordInput = document.getElementById("studentPasswordInput");
    if (passwordInput) {
      passwordInput.addEventListener("input", function () {
        window.SchoolManagement.CustomModal.hideError("studentPasswordInput");
      });

      passwordInput.addEventListener("keypress", function (e) {
        if (e.which === 13 || e.keyCode === 13) {
          e.preventDefault();
          self.submitLogin();
        }
      });
    }

    const toggleBtn = document.getElementById("toggleStudentPassword");
    if (toggleBtn) {
      toggleBtn.addEventListener("click", function () {
        window.SchoolManagement.CustomModal.togglePasswordVisibility(
          "studentPasswordInput",
          "studentPasswordIcon"
        );
      });
    }

    // Logout button
    const logoutBtn = document.getElementById("student-logout-btn");
    if (logoutBtn) {
      logoutBtn.addEventListener("click", function () {
        self.logout();
      });
    }
  },

  /**
   * Check if a student session exists; if yes run callback immediately. If not, show login modal and store callback.
   * @param {Function} callback - Function to call on successful session or after login
   */
  checkSessionWithCallback: function (callback) {
    const self = this;
    // Save callback
    this.pendingCallback = callback || null;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/check-student-session",
      method: "GET",
      success: function (data) {
        if (data && data.logged_in) {
          // already logged in
          self.updateLoginStatus(true, data.student_id, data.student_name);
          if (typeof self.pendingCallback === "function") {
            self.pendingCallback();
            self.pendingCallback = null;
          }
        } else {
          self.updateLoginStatus(false);
          // show login modal
          window.SchoolManagement.CustomModal.showModal("studentLoginModal", {
            onShow: function () {
              setTimeout(function () {
                const input = document.getElementById("studentUsernameInput");
                if (input) input.focus();
              }, 100);
            },
          });
        }
      },
      error: function () {
        console.error("Error checking student session");
        // As fallback, show login modal
        window.SchoolManagement.CustomModal.showModal("studentLoginModal");
      },
    });
  },

  submitLogin: function () {
    const username = document.getElementById("studentUsernameInput")
      ? document.getElementById("studentUsernameInput").value.trim()
      : "";
    const password = document.getElementById("studentPasswordInput")
      ? document.getElementById("studentPasswordInput").value
      : "";

    if (!username) {
      window.SchoolManagement.CustomModal.showError(
        "studentUsernameInput",
        "Vui lòng nhập username"
      );
      return;
    }
    if (!password) {
      window.SchoolManagement.CustomModal.showError(
        "studentPasswordInput",
        "Vui lòng nhập mật khẩu"
      );
      return;
    }

    window.SchoolManagement.CustomModal.setButtonLoading(
      "submitStudentLogin",
      true
    );

    window.SchoolManagement.Utils.createAjaxRequest({
      url: schoolManagementAjax.apiUrl + "school-management/v1/student-login",
      method: "POST",
      data: {
        username: username,
        password: password,
      },
      success: function (data) {
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitStudentLogin",
          false
        );
        if (data && data.success) {
          window.SchoolManagement.CustomModal.hideModal("studentLoginModal");
          // self.updateLoginStatus(true, data.student_id, data.student_name);
            window.SchoolManagement.StudentLogin.updateLoginStatus(
              true,
              data.student_id,
              data.student_name
            );
          
          if (
            typeof window.SchoolManagement.StudentLogin.pendingCallback ===
            "function"
          ) {
            window.SchoolManagement.StudentLogin.pendingCallback();
            window.SchoolManagement.StudentLogin.pendingCallback = null;
          }
        } else {
          window.SchoolManagement.CustomModal.showError(
            "studentPasswordInput",
            "Username hoặc mật khẩu không đúng"
          );
        }
      },
      error: function () {
        window.SchoolManagement.CustomModal.setButtonLoading(
          "submitStudentLogin",
          false
        );
        window.SchoolManagement.CustomModal.showError(
          "studentPasswordInput",
          "Có lỗi xảy ra. Vui lòng thử lại"
        );
      },
    });
  },

  /**
   * Logout current student
   */
  logout: function () {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url: schoolManagementAjax.apiUrl + "school-management/v1/student-logout",
      method: "POST",
      success: function (data) {
        if (data && data.success) {
          self.updateLoginStatus(false);
          // Reset school selection
          if (window.SchoolManagement.SchoolClass) {
            window.SchoolManagement.SchoolClass.showSchoolSelection();
          }
        }
      },
      error: function () {
        console.error("Error during logout");
      },
    });
  },

  /**
   * Update login status UI
   * @param {boolean} isLoggedIn - Whether user is logged in
   * @param {number} studentId - Student ID (optional)
   * @param {string} studentName - Student name (optional)
   */
  updateLoginStatus: function (isLoggedIn, studentId, studentName) {
    const statusDiv = document.getElementById("student-status");
    const studentNameEl = document.getElementById("student-name");

    if (isLoggedIn) {
      if (statusDiv) statusDiv.style.display = "flex";
      if (studentNameEl) {
        studentNameEl.textContent =
          studentName || "Học sinh #" + (studentId || "???");
      }
    } else {
      if (statusDiv) statusDiv.style.display = "none";
      if (studentNameEl) studentNameEl.textContent = "";
    }
  },
};

// Initialize when DOM ready
document.addEventListener("DOMContentLoaded", function () {
  const $ = window.jQuery || window.$ || null;
  window.SchoolManagement.StudentLogin.init($);
});
