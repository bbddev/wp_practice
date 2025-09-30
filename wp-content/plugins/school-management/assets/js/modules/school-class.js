/**
 * School and Class Management Module
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.SchoolClass = {
  /**
   * Initialize school and class dropdowns
   * @param {Object} $ - jQuery object
   */
  init: function ($) {
    this.$ = $;
    this.bindEvents();
    this.loadSchools();
  },

  /**
   * Bind events for school and class dropdowns
   */
  bindEvents: function () {
    const self = this;
    const $ = this.$;

    // Handle school selection
    $("#school-dropdown").on("change", function () {
      const schoolId = $(this).val();
      self.handleSchoolChange(schoolId);
    });

    // Handle class selection
    $("#class-dropdown").on("change", function () {
      const classId = $(this).val();
      self.handleClassChange(classId);
    });
  },

  /**
   * Handle school dropdown change
   * @param {string} schoolId - Selected school ID
   */
  handleSchoolChange: function (schoolId) {
    const $ = this.$;
    if (schoolId) {
      this.loadClasses(schoolId);
      $("#select-class").show();
    } else {
      $("#select-class").hide();
      $("#entity-container").hide();
    }

    // Reset class dropdown and entities
    $("#class-dropdown").html('<option value="">-- Chọn lớp --</option>');
    $("#entity-grid").empty();
    $("#pagination-container").hide();

    // Reset entity module state
    if (window.SchoolManagement.Entity) {
      window.SchoolManagement.Entity.reset();
    }
  },

  /**
   * Handle class dropdown change
   * @param {string} classId - Selected class ID
   */
  handleClassChange: function (classId) {
    const $ = this.$;
    if (classId) {
      // Check if class has password first
      window.SchoolManagement.Password.checkClassPassword(classId);
    } else {
      $("#entity-container").hide();
      $("#entity-grid").empty();
      $("#pagination-container").hide();

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
    $dropdown.html('<option value="">-- Chọn trường --</option>');

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
