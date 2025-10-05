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
  },

  showSchoolSelection: function () {
    const $ = this.$ || window.SchoolManagement.$ || jQuery;

    // Show school selection
    $("#select-school").show();

    // Hide other containers
    $("#select-class").hide();
    $("#entity-container").hide();

    // Reset dropdowns to default state
    $("#school-dropdown").val("");
    $("#class-dropdown").html('<option value="">-- Chọn lớp --</option>');

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

    $("#school-dropdown").on("change", function () {
      const schoolId = $(this).val();
      // Before handling school change, ensure a student is logged in
      if (window.SchoolManagement.StudentLogin) {
        // Provide a callback to run after session check / login
        window.SchoolManagement.StudentLogin.checkSessionWithCallback(
          function () {
            self.handleSchoolChange(schoolId);
          }
        );
      } else {
        // Fallback: just handle change
        self.handleSchoolChange(schoolId);
      }
    });

    $("#class-dropdown").on("change", function () {
      const classId = $(this).val();
      self.handleClassChange(classId);
    });
  },

  handleSchoolChange: function (schoolId) {
    const $ = this.$;
    if (schoolId) {
      this.loadClasses(schoolId);
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
        self.populateSchoolDropdown(data);
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
  populateSchoolDropdown: function (data) {
    const $ = this.$;
    const $dropdown = $("#school-dropdown");
    $dropdown.html(
      '<option value="">-- Chọn khối học sinh tham gia --</option>'
    );

    if (data && data.length > 0) {
      // Sort schools naturally by title
      const sortedData = window.SchoolManagement.Utils.sortByTitle(data);

      $.each(sortedData, function (index, school) {
        $dropdown.append(
          '<option value="' + school.ID + '">' + school.post_title + "</option>"
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
};
