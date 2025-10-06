window.SchoolManagement = window.SchoolManagement || {};

// Global function for HTML onclick compatibility
function showSchoolSelection() {
  if (window.SchoolManagement.SchoolClass) {
    window.SchoolManagement.SchoolClass.showSchoolSelection();
  }
}

window.SchoolManagement.SchoolClass = {
  init: function ($) {
    this.$ = $;
    this.bindEvents();
    this.loadSchools();
    this.checkInitialLoginStatus();
  },

  showSchoolSelection: function () {
    const $ = this.$ || window.SchoolManagement.$ || jQuery;

    // Show school selection (menu navigation)
    // $("#select-school").show();

    // Hide other containers
    $("#select-class").hide();
    $("#entity-container").hide();

    // Clear class title
    $("#class-title").text("");

    // Clear entity grid and hide pagination
    $("#entity-grid").empty();
    $("#pagination-container").hide();

    // Reset entity module state if it exists
    if (window.SchoolManagement.Entity) {
      window.SchoolManagement.Entity.reset();
    }
  },

  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Bind click event for navigation menu
    $(document).on("click", ".school-nav-item", function (e) {
      e.preventDefault();
      const schoolId = $(this).data("school-id");

      // Active class toggle
      $(".school-nav-item").removeClass("active");
      $(this).addClass("active");

      if (schoolId === "home") {
        // Reset to initial state (showSchoolSelection)
        self.showSchoolSelection();
      } else {
        // Ensure student is logged in before handling school change
        if (window.SchoolManagement.StudentLogin) {
          window.SchoolManagement.StudentLogin.checkSessionWithCallback(
            function () {
              self.handleSchoolChange(schoolId);
            }
          );
        }
      }
    });

    $("#class-dropdown").on("change", function () {
      const classId = $(this).val();
      self.handleClassChange(classId);
    });
  },

  checkStudentOf: function (schoolId, studentOf, callback) {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/checkstudentof/" +
        schoolId +
        "/" +
        encodeURIComponent(studentOf),
      method: "GET",
      success: function (data) {
        const hasAccess = data && data.has_access;
        if (callback && typeof callback === "function") {
          callback(hasAccess);
        }
      },
      error: function () {
        console.error("Error checking student access to school");
        if (callback && typeof callback === "function") {
          callback(false); // Deny access on error
        }
      },
    });
  },

  handleSchoolChange: function (schoolId) {
    const self = this;
    const $ = this.$;
    const studentof = window.SchoolManagement.StudentLogin.studentOf;
    console.log("🚀 ~ studentof:", studentof);

    // Check if student has access to this school
    this.checkStudentOf(schoolId, studentof, function (hasAccess) {
      if (!hasAccess) {
        alert("Chọn sai Khối");
        return;
      }

      // Student has access, proceed with loading classes
      if (schoolId) {
        self.loadClasses(schoolId);
        $("#select-class").show();
      } else {
        $("#select-class").hide();
        $("#entity-container").hide();
      }

      $("#class-dropdown").html('<option value="">-- Chọn lớp --</option>');
      $("#entity-grid").empty();
      $("#pagination-container").hide();

      if (window.SchoolManagement.Entity) {
        window.SchoolManagement.Entity.reset();
      }
    });
  },

  handleClassChange: function (classId) {
    const $ = this.$;
    if (classId) {
      // Get the selected class name and update the title
      const selectedClassName = $("#class-dropdown option:selected").text();
      $("#class-title").text(selectedClassName);

      // Check if class has password first
      window.SchoolManagement.Password.checkClassPassword(classId);
    } else {
      $("#entity-container").hide();
      $("#entity-grid").empty();
      $("#pagination-container").hide();

      // Clear class title
      $("#class-title").text("");

      // Reset entity module state
      if (window.SchoolManagement.Entity) {
        window.SchoolManagement.Entity.reset();
      }
    }
  },

  /**
   * Load all schools from API
   */
  loadSchools: function () {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url: schoolManagementAjax.apiUrl + "school-management/v1/schools",
      method: "GET",
      success: function (data) {
        self.populateSchoolNav(data);
      },
      error: function () {
        console.error("Error loading schools");
      },
    });
  },

  /**
   * Populate school dropdown with data
   * @param {Array} data - Schools data
   */
  populateSchoolNav: function (data) {
    const $ = this.$;
    const $navlist = $("#school-nav-list");

    if (data && data.length > 0) {
      // Sort schools naturally by title
      const sortedData = window.SchoolManagement.Utils.sortByTitle(data);

      $.each(sortedData, function (index, school) {
        $navlist.append(
          '<li class="nav-item"><a href="#" class="nav-link school-nav-item" data-school-id="' +
            school.ID +
            '">' +
            school.post_title +
            "</a></li>"
        );
      });
    }
  },

  /**
   * Load classes for a specific school
   * @param {string} schoolId - School ID
   */
  loadClasses: function (schoolId) {
    const self = this;

    window.SchoolManagement.Utils.createAjaxRequest({
      url:
        schoolManagementAjax.apiUrl +
        "school-management/v1/classes/" +
        schoolId,
      method: "GET",
      success: function (data) {
        self.populateClassDropdown(data);
      },
      error: function () {
        console.error("Error loading classes");
      },
    });
  },

  /**
   * Populate class dropdown with data
   * @param {Array} data - Classes data
   */
  populateClassDropdown: function (data) {
    const $ = this.$;
    const $dropdown = $("#class-dropdown");
    $dropdown.html('<option value="">-- Chọn lớp --</option>');

    if (data && data.length > 0) {
      // Sort classes naturally by title
      const sortedData = window.SchoolManagement.Utils.sortByTitle(data);

      $.each(sortedData, function (index, classItem) {
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

  /**
   * Check login status on page load to show/hide logout button
   */
  checkInitialLoginStatus: function () {
    if (window.SchoolManagement.StudentLogin) {
      window.SchoolManagement.Utils.createAjaxRequest({
        url:
          schoolManagementAjax.apiUrl +
          "school-management/v1/check-student-session",
        method: "GET",
        success: function (data) {
          if (data && data.logged_in) {
            // Set studentOf first before updating login status
            window.SchoolManagement.StudentLogin.studentOf =
              data.student_of || null;
            window.SchoolManagement.StudentLogin.updateLoginStatus(
              true,
              data.student_id,
              data.student_name,
              data.student_of
            );
          } else {
            window.SchoolManagement.StudentLogin.studentOf = null;
            window.SchoolManagement.StudentLogin.updateLoginStatus(false);
          }
        },
        error: function () {
          console.error("Error checking initial login status");
        },
      });
    }
  },
};
