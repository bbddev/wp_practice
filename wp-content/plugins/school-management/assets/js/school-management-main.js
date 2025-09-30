/**
 * Main School Management Controller
 * Coordinates all modules and handles initialization
 */
window.SchoolManagement = window.SchoolManagement || {};

window.SchoolManagement.App = {
  /**
   * Initialize the entire application
   */
  init: function () {
    // Wait for DOM to be ready
    jQuery(document).ready(
      function ($) {
        // Store jQuery reference globally for modules
        window.SchoolManagement.$ = $;
        // Initialize all modules
        this.initializeModules($);
      }.bind(this)
    );
  },

  /**
   * Initialize all modules in proper order
   * @param {Object} $ - jQuery object
   */
  initializeModules: function ($) {
    // Initialize utilities (no initialization needed, just functions)

    // Initialize pagination
    if (window.SchoolManagement.Pagination) {
      window.SchoolManagement.Pagination.init($);
    }

    // Initialize entity management
    if (window.SchoolManagement.Entity) {
      window.SchoolManagement.Entity.init($);
    }

    // Initialize password management
    if (window.SchoolManagement.Password) {
      window.SchoolManagement.Password.init($);
    }

    // Initialize modal management
    if (window.SchoolManagement.Modal) {
      window.SchoolManagement.Modal.init($);
    }

    // Initialize school/class management (should be last as it loads initial data)
    if (window.SchoolManagement.SchoolClass) {
      window.SchoolManagement.SchoolClass.init($);
    }
  },
};

// Auto-initialize when script loads
window.SchoolManagement.App.init();
